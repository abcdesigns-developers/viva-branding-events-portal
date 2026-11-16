<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\GiftPopStoreRequest;
use App\Http\Requests\GiftPopUpdateRequest;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Location;
use App\Models\PortalRequest;
use App\Models\RequestHistory;

/**
 * Module 4 – Gift & POP Requirement: a quick request for gifts, POP material,
 * samples and catalogues without a full event request. Submit to get a Request ID.
 */
class GiftPopRequestController extends Controller
{
    const MODULE = 'gift_pop';

    public function index()
    {
        // Statuses for the filter: the usual ones plus any already used on this user's requests.
        $statuses = collect(['Request Raised', 'Returned'])
            ->merge($this->visibleRequests()->distinct()->pluck('status'))
            ->unique()->values();

        return view('Admin.GiftPopRequests.index', compact('statuses'));
    }

    public function data(Request $request)
    {
        $query = $this->visibleRequests()->with(['eventType', 'location', 'user', 'items']);
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
            ->editColumn('required_by', function ($portalRequest) {
                return $portalRequest->required_by?->format('d-m-Y');
            })
            ->addColumn('item_summary', function ($portalRequest) {
                return $portalRequest->items->map(fn ($item) => ($item->item()?->name ?? '-') . ' × ' . $item->quantity)->implode(', ');
            })
            ->addColumn('raised_by', function ($portalRequest) {
                return $portalRequest->user?->name;
            })
            ->editColumn('status', function ($portalRequest) {
                return $this->statusBadge($portalRequest->status);
            })
            ->addColumn('action', function ($portalRequest) {
                $links = '<a href="' . route('admin.gift-pop-requests.show', ['portal_request' => $portalRequest->route_key]) . '" class="badge bg-info fs-1" title="View"><i class="fa fa-eye"></i></a>';
                if ($portalRequest->isEditable()) {
                    $links .= ' <a href="' . route('admin.gift-pop-requests.edit', ['portal_request' => $portalRequest->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                return $links;
            })
            ->addIndexColumn()
            ->rawColumns(['request_no', 'status', 'action'])->setRowId('id')->make(true);
    }

    public function create()
    {
        return view('Admin.GiftPopRequests.form', $this->formData());
    }

    public function store(GiftPopStoreRequest $request)
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
        $portalRequest->load(['event', 'eventType', 'location', 'user', 'items', 'histories.user']);

        return view('Admin.GiftPopRequests.show', compact('portalRequest'));
    }

    public function edit(PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->isEditable(), 403, 'Only returned requests can be edited.');
        $portalRequest->load(['items']);

        return view('Admin.GiftPopRequests.form', $this->formData($portalRequest) + compact('portalRequest'));
    }

    public function update(GiftPopUpdateRequest $request, PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->isEditable(), 403, 'Only returned requests can be edited.');

        $data = $request->validated();

        DB::transaction(function () use ($portalRequest, $data) {
            $this->saveRequest($portalRequest, $data);
        });

        return $this->savedResponse($portalRequest);
    }

    /** The static "Gift & POP Requirement" event (created by EventSeeder). */
    private function giftPopEvent(): Event
    {
        $event = Event::byCode(Event::GIFT_POP);
        abort_if(! $event, 500, 'The Gift & POP Requirement section is missing. Run: php artisan db:seed --class=EventSeeder');

        return $event;
    }

    /** Requests this user may see: all for SuperAdmin, otherwise only their own. */
    private function visibleRequests()
    {
        $query = PortalRequest::where('module', self::MODULE);
        if (! auth()->user()->hasRole('SuperAdmin')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    private function authorizeRequest(PortalRequest $portalRequest)
    {
        abort_unless($portalRequest->module == self::MODULE, 404);
        abort_unless(auth()->user()->hasRole('SuperAdmin') || $portalRequest->user_id == auth()->id(), 403);
    }

    /**
     * Item type the first row starts on for a request type: POP for POP material,
     * Sample for samples & catalogues, nothing for other promotional material,
     * Gift for every gift type.
     */
    private function defaultItemType(EventType $eventType): ?string
    {
        $name = strtolower($eventType->name);

        return match (true) {
            str_contains($name, 'pop') => 'pop',
            str_contains($name, 'sample') => 'sample',
            str_contains($name, 'gift') => 'gift',
            default => null,
        };
    }

    /** Dropdown options for the form, from the Master Management lists. */
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

        $event = $this->giftPopEvent();
        $eventTypes = EventType::where('event_id', $event->id)
            ->where(fn ($query) => $query->where('status', 'ACTIVE')->orWhere('id', $portalRequest?->event_type_id))
            ->orderBy('name')->get();

        return [
            'event' => $event,
            'eventTypes' => $eventTypes,
            'typeItemDefaults' => $eventTypes->mapWithKeys(fn ($eventType) => [$eventType->id => $this->defaultItemType($eventType)]),
            'locations' => $active(Location::class, array_filter([$portalRequest?->location_id])),
            'itemTypes' => $items,
        ];
    }

    private function saveRequest(PortalRequest $portalRequest, array $data): void
    {
        $fromStatus = $portalRequest->exists ? $portalRequest->status : null;

        $portalRequest->fill([
            'event_id' => $this->giftPopEvent()->id,
            'event_type_id' => $data['event_type_id'] ?? null,
            'objective' => $data['objective'] ?? null,
            'recipient' => $data['recipient'] ?? null,
            'location_id' => $data['location_id'] ?? null,
            'required_by' => $data['required_by'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);
        $portalRequest->save();

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

    /** Next Request ID for this module and year, e.g. GIFT-2026-0001. */
    private function nextRequestNo(): string
    {
        $prefix = PortalRequest::MODULE_CODES[self::MODULE] . '-' . now()->year . '-';
        $last = PortalRequest::where('request_no', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('request_no')
            ->value('request_no');
        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
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
            'redirect' => route('admin.gift-pop-requests.show', ['portal_request' => $portalRequest->route_key]),
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
