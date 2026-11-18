<?php

namespace App\Support;

use App\Models\PortalRequest;
use App\Models\User;
use App\Notifications\RequestUpdated;
use Illuminate\Support\Facades\Log;

/**
 * Tells the right people when a request is raised or moves on. Called from PortalRequest's saved
 * event, so every screen (Meet, Presentation, Gift & POP) is covered without touching each controller.
 * A failure here only writes to the log: it must never block someone's work.
 */
class RequestNotifier
{
    /** Who acts next, per Meet status. */
    const MEET_NEXT = [
        'Request Raised' => ['director'],
        'Initial Approval' => ['branding'],
        'Branding Planning' => ['branding'],
        'Proposal Submitted' => ['director'],
        'Final Approval' => ['purchase', 'accounts', 'logistics'],
        'PO Ready' => ['accounts'],
        'Payment Done' => ['logistics'],
        'Material Ready' => ['logistics'],
        'Dispatched' => ['branding'],
        'Event Ready' => ['branding'],
        'Completed' => ['branding'],
        'Returned' => ['requester'],
    ];

    const ACTOR_LABELS = ['director' => 'Director', 'branding' => 'Branding', 'purchase' => 'Purchase', 'accounts' => 'Accounts', 'logistics' => 'Logistics', 'requester' => 'the requester'];

    public static function handle(PortalRequest $request): void
    {
        if (! $request->request_no || $request->status === 'Draft') {
            return;
        }

        // A request saved twice in one submit (create, then number and status) is announced once.
        static $announced = [];
        $key = $request->id . '|' . $request->status . '|' . $request->submitted_at?->timestamp;
        if (isset($announced[$key])) {
            return;
        }
        $announced[$key] = true;

        try {
            self::send($request);
        } catch (\Throwable $e) {
            Log::warning('Request notification failed for ' . $request->request_no . ': ' . $e->getMessage());
        }
    }

    private static function send(PortalRequest $request): void
    {
        $by = auth()->id();
        $url = $request->showUrl();
        $label = $request->module_label . ' ' . $request->request_no;
        $actors = self::nextActors($request);

        $notified = [];
        foreach ($actors as $actor) {
            $users = $actor === 'requester' ? collect([$request->user])->filter() : self::usersFor($actor);
            foreach ($users as $user) {
                if ($user->id === $by || isset($notified[$user->id])) {
                    continue;
                }
                $notified[$user->id] = true;
                $user->notify(new RequestUpdated(
                    $request->request_no, $request->status,
                    $label . ' is now at "' . $request->status . '".',
                    'Your action is needed. Raised by ' . ($request->user?->name ?? 'a user') . '.',
                    $url, true
                ));
            }
        }

        // The requester always hears about progress on their own request.
        $owner = $request->user;
        if ($owner && $owner->id !== $by && ! isset($notified[$owner->id])) {
            $next = $actors ? ' Waiting on ' . collect($actors)->map(fn ($a) => self::ACTOR_LABELS[$a])->join(', ', ' and ') . '.' : '';
            $owner->notify(new RequestUpdated(
                $request->request_no, $request->status,
                $label . ' moved to "' . $request->status . '".', trim($next ?: 'No further action is pending.'), $url, false
            ));
        }
    }

    /** Actor keys that must act at the request's current status. */
    private static function nextActors(PortalRequest $request): array
    {
        if ($request->module === 'meet_event') {
            return self::MEET_NEXT[$request->status] ?? [];
        }
        if (! RequestFlow::handles($request->module)) {
            return [];
        }
        $request->loadMissing('items');

        return collect(RequestFlow::FLOWS[$request->module]['actions'][$request->status] ?? [])
            ->filter(fn ($row) => match ($row['when'] ?? null) {
                'needs_approval' => RequestFlow::needsApproval($request),
                'no_approval' => ! RequestFlow::needsApproval($request),
                default => true,
            })
            ->pluck('actor')->unique()->values()->all();
    }

    /** Active users who hold the permission of a team, SuperAdmin left out so they are not flooded. */
    private static function usersFor(string $actor)
    {
        $permission = ['director' => 'approval-action', 'branding' => 'meetplanning-update', 'purchase' => 'purchasetask-update',
            'accounts' => 'accountstask-update', 'logistics' => 'logisticstask-update'][$actor] ?? null;

        return $permission
            ? User::permission($permission)->where('status', 'ACTIVE')->get()->reject(fn ($u) => $u->hasRole('SuperAdmin'))
            : collect();
    }
}
