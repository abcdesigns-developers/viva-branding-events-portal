<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalRequest;
use App\Models\RequestHistory;
use App\Support\PortalMenu;
use App\Support\RequestFlow;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /** Chart colours per module, same order as PortalRequest::MODULE_CODES. */
    private const MODULE_COLORS = [
        'meet_event' => '#1c9cd8',
        'presentation' => '#9333ea',
        'gift_pop' => '#ec4899',
        'exhibition' => '#f97316',
        'branding' => '#16a34a',
        'experience_centre' => '#64748b',
    ];

    private const MODULE_LABELS = [
        'meet_event' => 'Meet Events',
        'presentation' => 'Presentations',
        'gift_pop' => 'Gift & POP',
        'exhibition' => 'Exhibition',
        'branding' => 'Branding',
        'experience_centre' => 'Experience Centre',
    ];

    public function index()
    {
        $user = auth()->user();
        $seesAll = PortalRequest::seesAll($user);

        // Only the modules switched on (Phase 1 unless PORTAL_PHASE2) and visible to this user.
        $modules = collect(PortalMenu::REQUESTS)
            ->filter(fn ($m) => PortalMenu::moduleEnabled($m[4]) && $user->can($m[2] . '-view'))
            ->map(fn ($m) => $this->moduleKey($m[2]))
            ->filter()->values();

        $base = fn () => PortalRequest::query()
            ->whereIn('module', $modules)
            ->when(! $seesAll, fn ($q) => $q->where('user_id', $user->id));

        $total = $base()->whereNotNull('request_no')->count();
        $open = $base()->whereNotNull('request_no')->whereNotIn('status', ['Completed', 'Closed'])->count();
        $done = $base()->whereIn('status', ['Completed', 'Closed'])->count();
        $returned = $base()->where('status', 'Returned')->count();

        // Waiting for this user: Director approvals, or own returned requests.
        $waiting = collect();
        if ($user->can('approval-action')) {
            $waiting = $base()->where('module', 'meet_event')->whereIn('status', ['Request Raised', 'Proposal Submitted'])
                ->whereNotNull('request_no')->with('user:id,name')->latest('submitted_at')->limit(6)->get();
        }
        // Section 3 and 4 steps this user can press now (Branding, Logistics, Director, or the requester).
        $waiting = $waiting->merge($this->flowWaiting($user, $base))->unique('id')->take(8)->values();
        $mine = $base()->where('user_id', $user->id)->where('status', 'Returned')->with('user:id,name')->latest('updated_at')->limit(6)->get();

        // The 7 master stages, with a count each.
        $byStage = $base()->whereNotNull('request_no')->selectRaw('stage, count(*) as n')->groupBy('stage')->pluck('n', 'stage');
        $pipeline = collect(PortalRequest::STAGES)->map(fn ($s) => ['label' => $s, 'count' => (int) ($byStage[$s] ?? 0)]);

        $byModule = $base()->whereNotNull('request_no')->selectRaw('module, count(*) as n')->groupBy('module')->pluck('n', 'module');
        $moduleKeys = $modules->all();

        // Requests raised per day for the last 14 days.
        $start = Carbon::today()->subDays(13);
        $perDay = $base()->whereNotNull('submitted_at')->where('submitted_at', '>=', $start)
            ->selectRaw('DATE(submitted_at) as d, count(*) as n')->groupBy('d')->pluck('n', 'd');
        $weekly = collect(range(0, 13))->map(fn ($i) => $start->copy()->addDays($i))
            ->map(fn ($d) => ['label' => $d->format('d M'), 'n' => (int) ($perDay[$d->toDateString()] ?? 0)]);

        $reminders = $this->reminders($base);

        $activity = RequestHistory::with(['user:id,name', 'portalRequest:id,request_no,module'])
            ->when(! $seesAll, fn ($q) => $q->whereIn('request_id', $base()->select('id')))
            ->latest('id')->limit(8)->get();

        return view('Admin.Dashboard.index', [
            'user' => $user,
            'seesAll' => $seesAll,
            'stats' => [
                ['label' => $seesAll ? 'Total requests' : 'My requests', 'value' => $total, 'icon' => 'ti-files', 'tone' => 'brand'],
                ['label' => 'In progress', 'value' => $open, 'icon' => 'ti-loader-3', 'tone' => 'amber'],
                ['label' => 'Completed', 'value' => $done, 'icon' => 'ti-circle-check', 'tone' => 'emerald'],
                ['label' => 'Returned', 'value' => $returned, 'icon' => 'ti-arrow-back-up', 'tone' => 'rose'],
            ],
            'waiting' => $waiting,
            'mine' => $mine,
            'pipeline' => $pipeline,
            'reminders' => $reminders,
            'activity' => $activity,
            'chart' => [
                'modules' => [
                    'labels' => collect($moduleKeys)->map(fn ($k) => self::MODULE_LABELS[$k])->all(),
                    'data' => collect($moduleKeys)->map(fn ($k) => (int) ($byModule[$k] ?? 0))->all(),
                    'colors' => collect($moduleKeys)->map(fn ($k) => self::MODULE_COLORS[$k])->all(),
                ],
                'weekly' => ['labels' => $weekly->pluck('label')->all(), 'data' => $weekly->pluck('n')->all()],
            ],
            'phase2' => [
                ['Exhibition / Sponsorship', 'ti-building-store'],
                ['Branding Support', 'ti-brush'],
                ['New Experience Centre', 'ti-building'],
            ],
            'showPhase2' => ! config('portal.phase2'),
            'moduleLabels' => self::MODULE_LABELS,
        ]);
    }

    /** Request date that matters per module: event date, else the required-by date. */
    private const DUE_SQL = 'COALESCE(preferred_date_1, required_by)';

    /**
     * Reminders for events, meets, exhibitions, meetings and gift deadlines: overdue (not closed yet),
     * today, this week and the next 30 days, each with a countdown.
     */
    private function reminders(callable $base): array
    {
        $today = Carbon::today();
        $rows = $base()->whereNotNull('request_no')
            ->whereNotIn('status', ['Completed', 'Closed', 'Draft', 'Returned'])
            ->whereRaw(self::DUE_SQL . ' >= ?', [$today->copy()->subDays(14)->toDateString()])
            ->whereRaw(self::DUE_SQL . ' <= ?', [$today->copy()->addDays(30)->toDateString()])
            ->selectRaw('requests.*, ' . self::DUE_SQL . ' as due_on')
            ->with(['event:id,name', 'eventType:id,name', 'location:id,name', 'user:id,name'])
            ->orderBy('due_on')->limit(40)->get()
            ->map(function ($r) use ($today) {
                $due = Carbon::parse($r->due_on)->startOfDay();
                $days = (int) $today->diffInDays($due, false);
                $r->due_date = $due;
                $r->days_left = $days;
                $r->due_kind = $r->preferred_date_1 ? 'Event' : 'Required by';

                return $r;
            });

        $group = fn ($min, $max) => $rows->filter(fn ($r) => $r->days_left >= $min && $r->days_left <= $max)->values();

        return [
            'groups' => array_filter([
                ['label' => 'Overdue', 'tone' => 'rose', 'rows' => $group(-14, -1)],
                ['label' => 'Today and tomorrow', 'tone' => 'amber', 'rows' => $group(0, 1)],
                ['label' => 'This week', 'tone' => 'brand', 'rows' => $group(2, 7)],
                ['label' => 'Next 30 days', 'tone' => 'slate', 'rows' => $group(8, 30)],
            ], fn ($g) => $g['rows']->isNotEmpty()),
            'soon' => $group(0, 2)->count(),
            'overdue' => $group(-14, -1)->count(),
        ];
    }

    /** Requests waiting for a step this user is allowed to do (see RequestFlow). */
    private function flowWaiting($user, callable $base)
    {
        $found = collect();
        foreach (array_keys(RequestFlow::FLOWS) as $module) {
            $rows = $base()->where('module', $module)->whereNotNull('request_no')
                ->whereIn('status', array_keys(RequestFlow::FLOWS[$module]['actions']))
                ->with(['user:id,name', 'items'])->latest('submitted_at')->limit(30)->get();
            $found = $found->merge($rows->filter(fn ($r) => RequestFlow::actionsFor($user, $r) !== []));
        }

        return $found->sortByDesc('submitted_at')->values();
    }

    /** Permission prefix to module key. */
    private function moduleKey(string $permission): ?string
    {
        return [
            'meeteventrequest' => 'meet_event',
            'presentationrequest' => 'presentation',
            'giftpoprequest' => 'gift_pop',
            'exhibitionrequest' => 'exhibition',
            'brandingrequest' => 'branding',
            'experiencecentrerequest' => 'experience_centre',
        ][$permission] ?? null;
    }
}
