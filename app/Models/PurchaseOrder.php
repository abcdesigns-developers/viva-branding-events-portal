<?php

namespace App\Models;

/** A purchase order raised by the Purchase team. */
class PurchaseOrder extends BaseModel
{
    protected $fillable = ['team_task_id', 'vendor_name', 'po_number', 'po_date', 'amount', 'remarks'];

    protected $casts = [
        'po_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function task()
    {
        return $this->belongsTo(TeamTask::class, 'team_task_id');
    }
}
