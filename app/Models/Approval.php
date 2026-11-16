<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One approve or return on a request at a checkpoint (e.g. Meet "initial"). */
class Approval extends Model
{
    const APPROVED = 'Approved';
    const RETURNED = 'Returned';

    /** Checkpoint keys and the names shown on screen. */
    const CHECKPOINTS = [
        'initial' => 'Initial approval',
        'final' => 'Final proposal approval',
    ];

    protected $fillable = ['request_id', 'checkpoint', 'action', 'remarks', 'user_id'];

    public function request()
    {
        return $this->belongsTo(PortalRequest::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkpointName(): string
    {
        return self::CHECKPOINTS[$this->checkpoint] ?? ucfirst($this->checkpoint);
    }
}
