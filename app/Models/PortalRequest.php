<?php

namespace App\Models;

/**
 * A raised request (table "requests"). Not called "Request" so it never clashes
 * with Illuminate\Http\Request inside controllers.
 */
class PortalRequest extends BaseModel
{
    protected $table = 'requests';

    protected $fillable = [
        'module', 'user_id', 'event_id', 'event_type_id', 'zone_id', 'location_id', 'city', 'objective',
        'preferred_date_1', 'preferred_date_2', 'required_by', 'team_strength', 'total_guests',
        'branding_needs', 'remarks', 'stage', 'status',
        'event_name', 'organiser', 'expected_footfall', 'target_audience', 'space_details', 'proposed_budget', 'deliverables',
        'organisation', 'contact_person', 'meeting_time', 'attendee_count', 'presentation_material', 'needs_design',
        'recipient',
        'dealer_project', 'dimensions', 'quantity', 'creative_requirement', 'estimated_budget',
        'interior_requirement', 'av_requirement', 'furniture_requirement', 'launch_needed', 'launch_requirement',
    ];

    protected $casts = [
        'preferred_date_1' => 'date',
        'preferred_date_2' => 'date',
        'required_by' => 'date',
        'branding_needs' => 'array',
        'deliverables' => 'array',
        'proposed_budget' => 'decimal:2',
        'needs_design' => 'boolean',
        'estimated_budget' => 'decimal:2',
        'launch_needed' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /** Short code per module, used in the Request ID (MEET-2026-0001, EVNT-2026-0001). */
    const MODULE_CODES = [
        'meet_event' => 'MEET',
        'exhibition' => 'EVNT',
        'presentation' => 'PRES',
        'gift_pop' => 'GIFT',
        'branding' => 'BRND',
        'experience_centre' => 'EXPC',
    ];

    /** The 7 master stages shown on every request, in order. */
    const STAGES = [
        'Request Raised',
        'Approval',
        'Planning / Design',
        'Commercial / Production',
        'Material / Service Ready',
        'Execution / Activity',
        'Completed / Closed',
    ];

    /**
     * Meet Events' own 12 statuses, in order, with the master stage each one rolls up to.
     * Draft and Returned sit outside this line.
     */
    const MEET_STATUSES = [
        'Request Raised' => 'Request Raised',
        'Initial Approval' => 'Approval',
        'Branding Planning' => 'Planning / Design',
        'Proposal Submitted' => 'Planning / Design',
        'Final Approval' => 'Approval',
        'PO Ready' => 'Commercial / Production',
        'Payment Done' => 'Commercial / Production',
        'Material Ready' => 'Material / Service Ready',
        'Dispatched' => 'Material / Service Ready',
        'Event Ready' => 'Execution / Activity',
        'Completed' => 'Completed / Closed',
        'Closed' => 'Completed / Closed',
    ];

    /** Master items a request item can point to, by item_type. */
    const ITEM_TYPES = [
        'gift' => ['label' => 'Gift', 'model' => GiftItem::class],
        'pop' => ['label' => 'POP Item', 'model' => PopItem::class],
        'catalogue' => ['label' => 'Catalogue', 'model' => CatalogueItem::class],
        'sample' => ['label' => 'Product Sample', 'model' => ProductSample::class],
    ];

    /**
     * Who works on a Meet request, with the permission that lets a user act as them.
     * The requester is the request's own user (meeteventrequest-update).
     */
    const MEET_ACTORS = [
        'requester' => ['label' => 'Requester', 'permission' => 'meeteventrequest-update'],
        'director' => ['label' => 'Director', 'permission' => 'approval-action'],
        'branding' => ['label' => 'Branding', 'permission' => 'meetplanning-update'],
        'purchase' => ['label' => 'Purchase', 'permission' => 'purchasetask-update'],
        'accounts' => ['label' => 'Accounts', 'permission' => 'accountstask-update'],
        'logistics' => ['label' => 'Logistics', 'permission' => 'logisticstask-update'],
    ];

    /** The Meet actors a user can work as, from their permissions (requester not included). */
    public static function meetActorsOf(User $user): array
    {
        return collect(self::MEET_ACTORS)->except('requester')
            ->filter(fn ($actor) => $user->can($actor['permission']))->keys()->all();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function guestCategories()
    {
        return $this->hasMany(RequestGuestCategory::class, 'request_id');
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class, 'request_id');
    }

    public function documents()
    {
        return $this->hasMany(RequestDocument::class, 'request_id');
    }

    public function histories()
    {
        return $this->hasMany(RequestHistory::class, 'request_id')->latest('id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'request_id')->latest('id');
    }

    /** Branding's proposal (Meet Events, after initial approval). */
    public function proposal()
    {
        return $this->hasOne(MeetProposal::class, 'request_id');
    }

    /** Work released to Purchase, Accounts and Logistics after the final approval. */
    public function teamTasks()
    {
        return $this->hasMany(TeamTask::class, 'request_id')->orderBy('id');
    }

    /**
     * Moves a Meet request along PO Ready → Payment Done → Material Ready → Dispatched as the
     * teams finish. Teams work at the same time, but the status follows this order: it stops
     * at the first step that is not done yet. Returns the new status when it changed.
     */
    public function syncMeetTeamStatus(): ?string
    {
        $teamSteps = ['Final Approval', 'PO Ready', 'Payment Done', 'Material Ready', 'Dispatched'];
        if ($this->module !== 'meet_event' || ! in_array($this->status, $teamSteps, true)) {
            return null;
        }

        $tasks = $this->teamTasks()->get()->keyBy('team');
        $reached = [
            'PO Ready' => $tasks->get('purchase')?->isDone(),
            'Payment Done' => $tasks->get('accounts')?->isDone(),
            'Material Ready' => (bool) $tasks->get('logistics')?->material_ready_at,
            'Dispatched' => $tasks->get('logistics')?->isDone(),
        ];
        $status = 'Final Approval';
        foreach ($reached as $step => $done) {
            if (! $done) {
                break;
            }
            $status = $step;
        }

        // Only forward: a later step never moves the request back.
        if (array_search($status, $teamSteps) <= array_search($this->status, $teamSteps)) {
            return null;
        }

        $from = $this->status;
        $this->status = $status;
        $this->stage = self::MEET_STATUSES[$status];
        $this->save();
        RequestHistory::create([
            'request_id' => $this->id,
            'user_id' => auth()->id(),
            'action' => 'Status moved to ' . $status,
            'from_status' => $from,
            'to_status' => $status,
        ]);

        return $status;
    }

    /** Meet statuses in which Branding can still change the proposal. */
    public function isPlannable(): bool
    {
        return $this->module === 'meet_event' && in_array($this->status, ['Initial Approval', 'Branding Planning'], true);
    }

    /** The most recent return, to show its remarks to the requester. */
    public function latestReturn(): ?Approval
    {
        return $this->approvals->firstWhere('action', Approval::RETURNED);
    }

    /** The newest version of an uploaded document type, e.g. the current guest list. */
    public function latestDocument(string $type)
    {
        return $this->documents->where('type', $type)->sortByDesc('version')->first();
    }

    public function isEditable(): bool
    {
        // Requests are edited only after an approver returns them.
        return $this->status === 'Returned';
    }
}
