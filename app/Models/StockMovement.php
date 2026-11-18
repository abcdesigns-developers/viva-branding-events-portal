<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One line of the stock ledger. Balance of an item = sum of its quantities. Never edited, only added to. */
class StockMovement extends Model
{
    protected $fillable = ['item_type', 'item_id', 'type', 'quantity', 'request_id', 'note', 'user_id', 'unit_cost'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function request()
    {
        return $this->belongsTo(PortalRequest::class, 'request_id');
    }
}
