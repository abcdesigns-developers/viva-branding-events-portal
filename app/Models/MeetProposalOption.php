<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One venue / hotel option or one vendor quotation in a Meet proposal. */
class MeetProposalOption extends Model
{
    protected $fillable = ['meet_proposal_id', 'type', 'hotel_id', 'name', 'details', 'capacity', 'amount', 'is_recommended'];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_recommended' => 'boolean',
    ];

    public function proposal()
    {
        return $this->belongsTo(MeetProposal::class, 'meet_proposal_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
