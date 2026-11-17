<?php

namespace App\Models;

/** An advance or final payment made by the Accounts team. */
class Payment extends BaseModel
{
    const TYPES = ['advance' => 'Advance', 'final' => 'Final payment'];
    const MODES = ['NEFT', 'RTGS', 'IMPS', 'UPI', 'Cheque', 'Cash'];

    protected $fillable = ['team_task_id', 'type', 'paid_to', 'amount', 'paid_on', 'mode', 'reference'];

    protected $casts = [
        'paid_on' => 'date',
        'amount' => 'decimal:2',
    ];

    public function task()
    {
        return $this->belongsTo(TeamTask::class, 'team_task_id');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }
}
