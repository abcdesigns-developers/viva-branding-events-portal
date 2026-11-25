<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeetEventStoreRequest;
use App\Http\Requests\MeetEventUpdateRequest;
use App\Models\BrandingMaterial;
use App\Models\Event;
use App\Models\EventType;
use App\Models\GuestCategory;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PortalRequest;
use App\Models\RequestDocument;
use App\Models\RequestHistory;

/**
 * Module 1 – Meet Events: the requester (Sales / HOD / Zonal Manager) raises a
 * request and submits it to get a Request ID.
 */
class MeetEventRequestController extends Controller
{
    const MODULE = 'meet_event';

    public function index()
    {
        // All Meet statuses, with Returned after Request Raised.
        $statuses = collect(array_keys(PortalRequest::MEET_STATUSES))->splice(0, 1)->push('Returned')
            ->merge(array_slice(array_keys(PortalRequest::MEET_STATUSES), 1));
        return view('Admin.MeetEventRequests.index', compact('statuses'));
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
                return $portalRequest->preferred_date_1?->format('d-m-Y');
            })
            ->addColumn('raised_by', function ($portalRequest) {
                return $portalRequest->user?->name;
            })
            ->editColumn('status', function ($portalRequest) {
                return $this->statusBadge($portalRequest->status);
            })
            ->addColumn('action', function ($portalRequest) {
                $links = '<a href="' . route('admin.meet-event-requests.show', ['portal_request' => $portalRequest->route_key]) . '" class="badge bg-info fs-1" title="View"><i class="fa fa-eye"></i></a>';
                if ($portalRequest->isEditable()) {
                    $links .= ' <a href="' . route('admin.meet-event-requests.edit', ['portal_request' => $portalRequest->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                return $links;
            })
            ->addIndexColumn()
            ->rawColumns(['request_no', 'status', 'action'])->setRowId('id')->make(true);
    }

    public function create()
    {
        return view('Admin.MeetEventRequests.form', $this->formData());
    }

    public function store(MeetEventStoreRequest $request)
    {
        $data = $request->validated();

        $portalRequest = DB::transaction(function () use ($request, $data) {
            $portalRequest = new PortalRequest([
                'module' => self::MODULE,
                'user_id' => auth()->id(),
                'status' => 'Request Raised',
            ]);
            $this->saveRequest($portalRequest, $request, $data);

            return $portalRequest;
        });

        return $this->savedResponse($portalRequest);
    }

    public function show(PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        $portalRequest->load(['event', 'eventType', 'location', 'user', 'guestCategories.guestCategory', 'items', 'documents.uploader', 'histories.user', 'approvals.user', 'proposal.options', 'teamTasks.purchaseOrders', 'teamTasks.payments', 'teamTasks.dispatchDetail.createdBy', 'teamTasks.completer']);
        $brandingMaterials = BrandingMaterial::whereIn('id', $portalRequest->branding_needs ?? [])->orderBy('name')->get();
        // Everything each person needs to do their part on this one page.
        $hotels = Hotel::where('status', 'ACTIVE')->orderBy('name')->pluck('name');
        $paymentTypes = Payment::TYPES;
        $paymentModes = Payment::MODES;

        return view('Admin.MeetEventRequests.show', compact('portalRequest', 'brandingMaterials', 'hotels', 'paymentTypes', 'paymentModes'));
    }

    public function edit(PortalRequest $portalRequest)
    {
        $this->authorizeOwner($portalRequest);
        abort_unless($portalRequest->isEditable(), 403, 'Only returned requests can be edited.');
        $portalRequest->load(['guestCategories', 'items', 'documents', 'approvals.user']);

        return view('Admin.MeetEventRequests.form', $this->formData($portalRequest) + compact('portalRequest'));
    }

    public function update(MeetEventUpdateRequest $request, PortalRequest $portalRequest)
    {
        $this->authorizeOwner($portalRequest);
        abort_unless($portalRequest->isEditable(), 403, 'Only returned requests can be edited.');

        $data = $request->validated();

        DB::transaction(function () use ($portalRequest, $request, $data) {
            $this->saveRequest($portalRequest, $request, $data);
        });

        return $this->savedResponse($portalRequest);
    }

    public function document(PortalRequest $portalRequest, RequestDocument $requestDocument)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($requestDocument->request_id == $portalRequest->id, 404);

        return Storage::disk('local')->response($requestDocument->file_path, $requestDocument->original_name, ['X-Content-Type-Options' => 'nosniff']);
    }

    /** The static "Meet Events" event (created by EventSeeder); every request here belongs to it. */
    private function meetEvent(): Event
    {
        $event = Event::byCode(Event::MEET_EVENT);
        abort_if(! $event, 500, 'The Meet Events section is missing. Run: php artisan db:seed --class=EventSeeder');

        return $event;
    }

    /**
     * Everyone who works on Meet requests (Director, Branding, Purchase, Accounts,
     * Logistics) and SuperAdmin see all of them; a requester sees only their own.
     */
    private function seesAll(): bool
    {
        return auth()->user()->hasRole('SuperAdmin') || PortalRequest::meetActorsOf(auth()->user()) !== [];
    }

    private function visibleRequests()
    {
        $query = PortalRequest::where('module', self::MODULE);
        if (! $this->seesAll()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    private function authorizeRequest(PortalRequest $portalRequest)
    {
        abort_unless($portalRequest->module == self::MODULE, 404);
        abort_unless($this->seesAll() || $portalRequest->user_id == auth()->id(), 403);
    }

    /** Only the requester (or SuperAdmin) edits the request form itself. */
    private function authorizeOwner(PortalRequest $portalRequest)
    {
        abort_unless($portalRequest->module == self::MODULE, 404);
        abort_unless(auth()->user()->hasRole('SuperAdmin') || $portalRequest->user_id == auth()->id(), 403);
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

        $event = $this->meetEvent();
        $eventTypes = EventType::with('gifts:gift_items.id')->where('event_id', $event->id)
            ->where(fn ($query) => $query->where('status', 'ACTIVE')->orWhere('id', $portalRequest?->event_type_id))
            ->orderBy('name')->get();

        return [
            'event' => $event,
            'eventTypes' => $eventTypes,
            'locations' => $active(Location::class, array_filter([$portalRequest?->location_id])),
            'guestCategories' => $active(GuestCategory::class, $portalRequest ? $portalRequest->guestCategories->pluck('guest_category_id')->all() : []),
            'brandingMaterials' => $active(BrandingMaterial::class, $portalRequest->branding_needs ?? []),
            'itemTypes' => $items,
            // Gift ids per event type, so the form can auto-add them.
            'eventTypeGifts' => $eventTypes->mapWithKeys(fn ($eventType) => [$eventType->id => $eventType->gifts->pluck('id')->values()]),
        ];
    }

    private function saveRequest(PortalRequest $portalRequest, Request $request, array $data): void
    {
        $fromStatus = $portalRequest->exists ? $portalRequest->status : null;

        // Keep only categories with guests; their total is the request's guest count.
        $data['guests'] = collect($data['guests'] ?? [])->filter(fn ($count) => (int) $count > 0)->map(fn ($count) => (int) $count)->all();

        // Gifts set on the Event Type are always kept (one row each). Their quantity is what
        // was entered, or total guests when left blank.
        $typeGiftIds = EventType::find($data['event_type_id'] ?? null)?->gifts()->where('gift_items.status', 'ACTIVE')->pluck('gift_items.id')->all() ?? [];
        $items = collect($data['items'] ?? []);
        $isTypeGift = fn ($item) => $item['type'] === 'gift' && in_array((int) $item['item_id'], $typeGiftIds, true);
        $giftQty = $items->filter($isTypeGift)->mapWithKeys(fn ($item) => [(int) $item['item_id'] => (int) ($item['quantity'] ?? 0)]);
        $data['items'] = $items->reject($isTypeGift)->values()
            ->merge(array_map(fn ($id) => ['type' => 'gift', 'item_id' => $id, 'quantity' => ($giftQty[$id] ?? 0) ?: array_sum($data['guests'])], $typeGiftIds))
            ->all();

        $portalRequest->fill([
            'event_id' => $this->meetEvent()->id,
            'event_type_id' => $data['event_type_id'] ?? null,
            'location_id' => $data['location_id'] ?? null,
            'objective' => $data['objective'] ?? null,
            'preferred_date_1' => $data['preferred_date_1'] ?? null,
            'team_strength' => $data['team_strength'] ?? null,
            'total_guests' => array_sum($data['guests']),
            'branding_needs' => array_values(array_map('intval', $data['branding_needs'] ?? [])),
            'remarks' => $data['remarks'] ?? null,
        ]);
        $portalRequest->save();

        $portalRequest->guestCategories()->delete();
        foreach ($data['guests'] as $categoryId => $count) {
            $portalRequest->guestCategories()->create(['guest_category_id' => $categoryId, 'guest_count' => $count]);
        }

        $portalRequest->items()->delete();
        foreach ($data['items'] ?? [] as $item) {
            $portalRequest->items()->create([
                'item_type' => $item['type'],
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        if ($request->hasFile('guest_list')) {
            $this->storeDocument($portalRequest, $request->file('guest_list'), 'guest_list');
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

    /** Next Request ID for this module and year, e.g. MEET-2026-0001. */
    private function nextRequestNo(): string
    {
        return \App\Support\RequestNumber::next(self::MODULE);
    }

    /** Files are kept per request; a new upload of the same type becomes the next version. */
    private function storeDocument(PortalRequest $portalRequest, $file, string $type): void
    {
        $version = (int) $portalRequest->documents()->where('type', $type)->max('version') + 1;
        $name = $type . '_v' . $version . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('requests/' . $portalRequest->id, $name, 'local');

        $portalRequest->documents()->create([
            'type' => $type,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'version' => $version,
            'uploaded_by' => auth()->id(),
        ]);
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
            'redirect' => route('admin.meet-event-requests.show', ['portal_request' => $portalRequest->route_key]),
        ], 201);
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
