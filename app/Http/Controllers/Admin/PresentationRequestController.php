<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\PresentationStoreRequest;
use App\Http\Requests\PresentationUpdateRequest;
use App\Models\BrandingMaterial;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Location;
use App\Models\PortalRequest;
use App\Models\RequestDocument;
use App\Models\RequestHistory;
use Illuminate\Support\Facades\Storage;

/**
 * Module 3 – Presentations & Small Events: Sales / HOD / Zonal Manager ask for
 * branding and material support for a meeting at a Government, Architect or
 * Client office. Kept simple: submit to get a Request ID.
 */
class PresentationRequestController extends Controller
{
    const MODULE = 'presentation';

    public function index()
    {
        // Statuses for the filter: the usual ones plus any already used on this user's requests.
        $statuses = collect(['Request Raised', 'Returned'])
            ->merge($this->visibleRequests()->distinct()->pluck('status'))
            ->unique()->values();

        return view('Admin.PresentationRequests.index', compact('statuses'));
    }

    public function data(Request $request)
    {
        $query = $this->visibleRequests()->with(['eventType', 'location', 'user']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->editColumn('request_no', function ($portalRequest) {
                return $portalRequest->request_no ?? '-';
            })
            ->addColumn('event_type', function ($portalRequest) {
                return $portalRequest->eventType?->name;
            })
            ->addColumn('location', function ($portalRequest) {
                return $portalRequest->location?->name;
            })
            ->editColumn('preferred_date_1', function ($portalRequest) {
                return $this->dateTime($portalRequest);
            })
            ->addColumn('raised_by', function ($portalRequest) {
                return $portalRequest->user?->name;
            })
            ->editColumn('status', function ($portalRequest) {
                return $this->statusBadge($portalRequest->status);
            })
            ->addColumn('action', function ($portalRequest) {
                $links = '<a href="' . route('admin.presentation-requests.show', ['portal_request' => $portalRequest->route_key]) . '" class="badge bg-info fs-1" title="View"><i class="fa fa-eye"></i></a>';
                if ($portalRequest->isEditable()) {
                    $links .= ' <a href="' . route('admin.presentation-requests.edit', ['portal_request' => $portalRequest->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                return $links;
            })
            ->addIndexColumn()
            ->rawColumns(['request_no', 'status', 'action'])->setRowId('id')->make(true);
    }

    public function create()
    {
        return view('Admin.PresentationRequests.form', $this->formData());
    }

    public function store(PresentationStoreRequest $request)
    {
        $data = $request->validated();

        $portalRequest = DB::transaction(function () use ($data) {
            $portalRequest = new PortalRequest([
                'module' => self::MODULE,
                'user_id' => auth()->id(),
                'status' => 'Request Raised',
            ]);
            $this->saveRequest($portalRequest, $data);

            return $portalRequest;
        });

        return $this->savedResponse($portalRequest);
    }

    public function show(PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        $portalRequest->load(['event', 'eventType', 'location', 'user', 'items', 'documents.uploader', 'histories.user']);
        $brandingMaterials = BrandingMaterial::whereIn('id', $portalRequest->branding_needs ?? [])->orderBy('name')->get();
        $meetingOn = $this->dateTime($portalRequest);

        return view('Admin.PresentationRequests.show', compact('portalRequest', 'brandingMaterials', 'meetingOn'));
    }

    public function edit(PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->isEditable(), 403, 'Only returned requests can be edited.');
        $portalRequest->load(['items']);

        return view('Admin.PresentationRequests.form', $this->formData($portalRequest) + compact('portalRequest'));
    }

    public function update(PresentationUpdateRequest $request, PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->isEditable(), 403, 'Only returned requests can be edited.');

        $data = $request->validated();

        DB::transaction(function () use ($portalRequest, $data) {
            $this->saveRequest($portalRequest, $data);
        });

        return $this->savedResponse($portalRequest);
    }

    /** Photos, meeting report and follow-up files will be listed here once that step is built. */
    public function document(PortalRequest $portalRequest, RequestDocument $requestDocument)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($requestDocument->request_id == $portalRequest->id, 404);

        return Storage::disk('local')->response($requestDocument->file_path, $requestDocument->original_name, ['X-Content-Type-Options' => 'nosniff']);
    }

    /** The static "Presentations & Small Events" event (created by EventSeeder). */
    private function presentationEvent(): Event
    {
        $event = Event::byCode(Event::PRESENTATION);
        abort_if(! $event, 500, 'The Presentations & Small Events section is missing. Run: php artisan db:seed --class=EventSeeder');

        return $event;
    }

    /** Requests this user may see: all for SuperAdmin, otherwise only their own. */
    private function visibleRequests()
    {
        $query = PortalRequest::where('module', self::MODULE);
        if (! PortalRequest::seesAll(auth()->user())) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    private function authorizeRequest(PortalRequest $portalRequest)
    {
        abort_unless($portalRequest->module == self::MODULE, 404);
        abort_unless(PortalRequest::seesAll(auth()->user()) || $portalRequest->user_id == auth()->id(), 403);
    }

    /** Dropdown and checkbox options for the form, from the Master Management lists. */
    private function formData(?PortalRequest $portalRequest = null): array
    {
        $active = fn ($model, $keep = []) => $model::where('status', 'ACTIVE')
            ->when($keep, fn ($query) => $query->orWhereIn('id', $keep))
            ->orderBy('name')->get();

        $items = [];
        foreach (PortalRequest::ITEM_TYPES as $type => $config) {
            $keep = $portalRequest ? $portalRequest->items->where('item_type', $type)->pluck('item_id')->all() : [];
            $items[$type] = [
                'label' => $config['label'],
                'options' => $active($config['model'], $keep)->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])->values(),
            ];
        }

        $event = $this->presentationEvent();
        $eventTypes = EventType::with('gifts:gift_items.id')->where('event_id', $event->id)
            ->where(fn ($query) => $query->where('status', 'ACTIVE')->orWhere('id', $portalRequest?->event_type_id))
            ->orderBy('name')->get();

        return [
            'event' => $event,
            'eventTypes' => $eventTypes,
            'locations' => $active(Location::class, array_filter([$portalRequest?->location_id])),
            'brandingMaterials' => $active(BrandingMaterial::class, $portalRequest->branding_needs ?? []),
            'itemTypes' => $items,
            // Gift ids per meeting type, so the form can auto-add them.
            'eventTypeGifts' => $eventTypes->mapWithKeys(fn ($eventType) => [$eventType->id => $eventType->gifts->pluck('id')->values()]),
        ];
    }

    private function saveRequest(PortalRequest $portalRequest, array $data): void
    {
        $fromStatus = $portalRequest->exists ? $portalRequest->status : null;

        $portalRequest->fill([
            'event_id' => $this->presentationEvent()->id,
            'event_type_id' => $data['event_type_id'] ?? null,
            'organisation' => $data['organisation'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'location_id' => $data['location_id'] ?? null,
            'preferred_date_1' => $data['preferred_date_1'] ?? null,
            'meeting_time' => $data['meeting_time'] ?? null,
            'attendee_count' => $data['attendee_count'] ?? null,
            'objective' => $data['objective'] ?? null,
            'presentation_material' => $data['presentation_material'] ?? null,
            'needs_design' => (bool) ($data['needs_design'] ?? false),
            'branding_needs' => array_values(array_map('intval', $data['branding_needs'] ?? [])),
            'remarks' => $data['remarks'] ?? null,
        ]);
        $portalRequest->save();

        // Gifts set on the Meeting Type are always kept (one row each). Their quantity is what
        // was entered, or the attendee count when left blank.
        $typeGiftIds = EventType::find($data['event_type_id'] ?? null)?->gifts()->where('gift_items.status', 'ACTIVE')->pluck('gift_items.id')->all() ?? [];
        $items = collect($data['items'] ?? []);
        $isTypeGift = fn ($item) => $item['type'] === 'gift' && in_array((int) $item['item_id'], $typeGiftIds, true);
        $giftQty = $items->filter($isTypeGift)->mapWithKeys(fn ($item) => [(int) $item['item_id'] => (int) ($item['quantity'] ?? 0)]);
        $data['items'] = $items->reject($isTypeGift)->values()
            ->merge(array_map(fn ($id) => ['type' => 'gift', 'item_id' => $id, 'quantity' => ($giftQty[$id] ?? 0) ?: (int) $data['attendee_count']], $typeGiftIds))
            ->all();

        $portalRequest->items()->delete();
        foreach ($data['items'] ?? [] as $item) {
            $portalRequest->items()->create([
                'item_type' => $item['type'],
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        if (! $portalRequest->request_no) {
            $portalRequest->request_no = $this->nextRequestNo();
        }
        $portalRequest->status = 'Request Raised';
        $portalRequest->stage = 'Request Raised';
        $portalRequest->submitted_at = now();
        $portalRequest->save();
        $this->addHistory($portalRequest, $fromStatus === 'Returned' ? 'Resubmitted' : 'Submitted', $fromStatus, 'Request Raised');
    }

    /** Next Request ID for this module and year, e.g. PRES-2026-0001. */
    private function nextRequestNo(): string
    {
        return \App\Support\RequestNumber::next(self::MODULE);
    }

    private function addHistory(PortalRequest $portalRequest, string $action, ?string $from, ?string $to): void
    {
        RequestHistory::create([
            'request_id' => $portalRequest->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'from_status' => $from,
            'to_status' => $to,
        ]);
    }

    private function savedResponse(PortalRequest $portalRequest)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Request ' . $portalRequest->request_no . ' submitted successfully',
            'redirect' => route('admin.presentation-requests.show', ['portal_request' => $portalRequest->route_key]),
        ], 201);
    }

    /** e.g. 12-12-2026, 03:30 PM */
    private function dateTime(PortalRequest $portalRequest): string
    {
        $date = $portalRequest->preferred_date_1?->format('d-m-Y') ?? '';
        $time = $portalRequest->meeting_time ? date('h:i A', strtotime($portalRequest->meeting_time)) : '';

        return trim($date . ($date && $time ? ', ' : '') . $time);
    }

    private function statusBadge(?string $status): string
    {
        $colors = [
            'Returned' => 'bg-amber-50 text-amber-700',
            'Closed' => 'bg-emerald-50 text-emerald-700',
        ];
        $color = $colors[$status] ?? 'bg-sky-50 text-sky-700';

        return '<span class="rounded-full px-2.5 py-1 text-xs font-medium ' . $color . '">' . e($status) . '</span>';
    }

}
