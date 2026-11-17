<?php

namespace App\Models;

/** Branding's plan for a Meet Event: venues, vendor quotes, plans and budget. */
class MeetProposal extends BaseModel
{
    protected $fillable = ['request_id', 'decoration_plan', 'branding_plan', 'setup_plan', 'recommended_plan', 'estimated_budget', 'submitted_at'];

    protected $casts = [
        'estimated_budget' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(PortalRequest::class, 'request_id');
    }

    public function options()
    {
        return $this->hasMany(MeetProposalOption::class)->orderBy('id');
    }

    public function venues()
    {
        return $this->options()->where('type', 'venue');
    }

    public function vendors()
    {
        return $this->options()->where('type', 'vendor');
    }

    /** Indian number format for rupee amounts, e.g. 1,25,000 or 1,25,000.50. */
    public static function money($amount): string
    {
        $amount = (float) $amount;
        $paise = round($amount - floor($amount), 2);
        $rupees = (string) (int) floor($amount);
        $last3 = substr($rupees, -3);
        $rest = substr($rupees, 0, -3);
        $formatted = $rest !== '' ? preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest) . ',' . $last3 : $last3;

        return $paise > 0 ? $formatted . '.' . str_pad((string) round($paise * 100), 2, '0', STR_PAD_LEFT) : $formatted;
    }
}
