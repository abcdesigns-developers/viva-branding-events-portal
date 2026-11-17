<?php

namespace App\Models;

/** Work released to one team for a request, e.g. Purchase raises the PO. */
class TeamTask extends BaseModel
{
    const PENDING = 'Pending';
    const IN_PROGRESS = 'In Progress';
    const DONE = 'Done';

    /** Teams that get a task after the Meet final approval, with the name shown on screen. */
    const TEAMS = [
        'purchase' => 'Purchase',
        'accounts' => 'Accounts',
        'logistics' => 'Logistics',
    ];

    protected $fillable = ['request_id', 'team', 'status', 'prepared_items', 'material_ready_at', 'completed_at', 'completed_by', 'remarks'];

    protected $casts = [
        'prepared_items' => 'array',
        'material_ready_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(PortalRequest::class, 'request_id');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class)->orderBy('id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('id');
    }

    /** Named dispatchDetail so it never clashes with Laravel's dispatch() helpers. */
    public function dispatchDetail()
    {
        return $this->hasOne(Dispatch::class);
    }

    public function teamName(): string
    {
        return self::TEAMS[$this->team] ?? ucfirst($this->team);
    }

    public function isDone(): bool
    {
        return $this->status === self::DONE;
    }
}
