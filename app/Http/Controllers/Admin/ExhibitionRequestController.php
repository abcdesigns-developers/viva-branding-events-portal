<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExhibitionStoreRequest;
use App\Http\Requests\ExhibitionUpdateRequest;
use App\Models\BrandingMaterial;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Location;
use App\Models\PortalRequest;
use App\Models\RequestDocument;
use App\Models\RequestHistory;
use App\Models\SponsorshipDeliverable;

/**
 * Module 2 – Events / Exhibitions / Sponsorship: HOD / Zonal Manager / Sales raise a
 * Major Exhibition, Small Exhibition or Sponsorship request with the proposal and
 * proposed budget, and submit it to get a Request ID.
 */
class ExhibitionRequestController extends Controller
{
    const MODULE = 'exhibition';

    public function index()
    {
        // Statuses for the filter: the usual ones plus any already used on this user's requests.
        $statuses = collect(['Request Raised', 'Returned'])
            ->merge($this->visibleRequests()->distinct()->pluck('status'))
            ->unique()->values();

        return view('Admin.ExhibitionRequests.index', compact('statuses'));
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
            ->editColumn('proposed_budget', function ($portalRequest) {
                return $portalRequest->proposed_budget !== null ? '₹ ' . $this->money($portalRequest->proposed_budget) : '';
            })
            ->addColumn('raised_by', function ($portalRequest) {
                return $portalRequest->user?->name;
            })
            ->editColumn('status', function ($portalRequest) {
                return $this->statusBadge($portalRequest->status);
            })
            ->addColumn('action', function ($portalRequest) {
                $links = '<a href="' . route('admin.exhibition-requests.show', ['portal_request' => $portalRequest->route_key]) . '" class="badge bg-info fs-1" title="View"><i class="fa fa-eye"></i></a>';
                if ($portalRequest->isEditable()) {
                    $links .= ' <a href="' . route('admin.exhibition-requests.edit', ['portal_request' => $portalRequest->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                return $links;
            })
            ->addIndexColumn()
            ->rawColumns(['request_no', 'status', 'action'])->setRowId('id')->make(true);
    }

    public function create()
    {
        return view('Admin.ExhibitionRequests.form', $this->formData());
    }

    public function store(ExhibitionStoreRequest $request)
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
        $portalRequest->load(['event', 'eventType', 'location', 'user', 'items', 'documents.uploader', 'histories.user']);
        $brandingMaterials = BrandingMaterial::whereIn('id', $portalRequest->branding_needs ?? [])->orderBy('name')->get();
        $deliverables = SponsorshipDeliverable::whereIn('id', $portalRequest->deliverables ?? [])->orderBy('name')->get();
        $isSponsorship = $this->isSponsorship($portalRequest->eventType);
        $eventDate = $portalRequest->preferred_date_1?->format('d-m-Y');
        $budget = $portalRequest->proposed_budget !== null ? '₹ ' . $this->money($portalRequest->proposed_budget) : null;

        return view('Admin.ExhibitionRequests.show', compact('portalRequest', 'brandingMaterials', 'deliverables', 'isSponsorship', 'eventDate', 'budget'));
    }

    public function edit(PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->isEditable(), 403, 'Only returned requests can be edited.');
        $portalRequest->load(['items', 'documents']);

        return view('Admin.ExhibitionRequests.form', $this->formData($portalRequest) + compact('portalRequest'));
    }

    public function update(ExhibitionUpdateRequest $request, PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
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

    /** The static "Events / Exhibitions / Sponsorship" event (created by EventSeeder). */
    private function exhibitionEvent(): Event
    {
        $event = Event::byCode(Event::EXHIBITION);
        abort_if(! $event, 500, 'The Events / Exhibitions / Sponsorship section is missing. Run: php artisan db:seed --class=EventSeeder');

        return $event;
    }

    /** Sponsorship types need Committed Deliverables; exhibitions need stall details. */
    private function isSponsorship(?EventType $eventType): bool
    {
        return $eventType && str_contains(strtolower($eventType->name), 'sponsor');
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

        $event = $this->exhibitionEvent();
        $eventTypes = EventType::where('event_id', $event->id)
            ->where(fn ($query) => $query->where('status', 'ACTIVE')->orWhere('id', $portalRequest?->event_type_id))
            ->orderBy('name')->get();

        return [
            'event' => $event,
            'eventTypes' => $eventTypes,
            // Type ids that show the Committed Deliverables section.
            'sponsorshipTypeIds' => $eventTypes->filter(fn ($eventType) => $this->isSponsorship($eventType))->pluck('id')->values(),
            'locations' => $active(Location::class, array_filter([$portalRequest?->location_id])),
            'deliverableOptions' => $active(SponsorshipDeliverable::class, $portalRequest->deliverables ?? []),
            'brandingMaterials' => $active(BrandingMaterial::class, $portalRequest->branding_needs ?? []),
            'itemTypes' => $items,
        ];
    }

    private function saveRequest(PortalRequest $portalRequest, Request $request, array $data): void
    {
        $fromStatus = $portalRequest->exists ? $portalRequest->status : null;

        // Deliverables belong to sponsorships only.
        if (! $this->isSponsorship(EventType::find($data['event_type_id'] ?? null))) {
            $data['deliverables'] = [];
        }

        $portalRequest->fill([
            'event_id' => $this->exhibitionEvent()->id,
            'event_type_id' => $data['event_type_id'] ?? null,
            'event_name' => $data['event_name'] ?? null,
            'organiser' => $data['organiser'] ?? null,
            'location_id' => $data['location_id'] ?? null,
            'preferred_date_1' => $data['preferred_date_1'] ?? null,
            'expected_footfall' => $data['expected_footfall'] ?? null,
            'target_audience' => $data['target_audience'] ?? null,
            'space_details' => $data['space_details'] ?? null,
            'proposed_budget' => $data['proposed_budget'] ?? null,
            'deliverables' => array_values(array_map('intval', $data['deliverables'] ?? [])),
            'branding_needs' => array_values(array_map('intval', $data['branding_needs'] ?? [])),
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

        foreach ($request->file('documents', []) as $file) {
            $this->storeDocument($portalRequest, $file, 'proposal');
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

    /** Next Request ID for this module and year, e.g. EVNT-2026-0001. */
    private function nextRequestNo(): string
    {
        return \App\Support\RequestNumber::next(self::MODULE);
    }

    /** Each uploaded file is kept with the request; files are numbered in upload order. */
    private function storeDocument(PortalRequest $portalRequest, $file, string $type): void
    {
        $version = (int) $portalRequest->documents()->where('type', $type)->max('version') + 1;
        $name = $type . '_' . $version . '.' . $file->getClientOriginalExtension();
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
            'redirect' => route('admin.exhibition-requests.show', ['portal_request' => $portalRequest->route_key]),
        ], 201);
    }

    /** Indian grouping, e.g. 8,00,000. */
    private function money($amount): string
    {
        $amount = (float) $amount;
        $paise = round($amount - floor($amount), 2);
        $rupees = (string) (int) floor($amount);
        $last3 = substr($rupees, -3);
        $rest = substr($rupees, 0, -3);
        $formatted = $rest !== '' ? preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest) . ',' . $last3 : $last3;

        return $paise > 0 ? $formatted . '.' . str_pad((string) round($paise * 100), 2, '0', STR_PAD_LEFT) : $formatted;
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
