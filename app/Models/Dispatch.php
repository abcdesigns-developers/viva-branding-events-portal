<?php

namespace App\Models;

/** Courier / transport details entered by Logistics when material is sent. */
class Dispatch extends BaseModel
{
    protected $fillable = ['team_task_id', 'courier', 'tracking_no', 'dispatched_on', 'expected_on', 'remarks'];

    protected $casts = [
        'dispatched_on' => 'date',
        'expected_on' => 'date',
    ];

    public function task()
    {
        return $this->belongsTo(TeamTask::class, 'team_task_id');
    }
}
