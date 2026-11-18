<?php

namespace App\Support;

use App\Models\PortalRequest;
use App\Models\User;

/**
 * The step-by-step life of Section 3 (Presentations & Small Events) and Section 4 (Gift & POP).
 * One table drives the screen, the checks and the history, so Meet Events stays untouched.
 *
 * steps:   status => master stage it rolls up to (in order)
 * actions: status => list of buttons. 'actor' decides who may press it:
 *   requester = the person who raised the request, director = approval-action,
 *   branding = meetplanning-update, logistics = logisticstask-update (SuperAdmin can do all).
 */
class RequestFlow
{
    const FLOWS = [
        'presentation' => [
            'steps' => [
                'Request Raised' => 'Request Raised',
                'Support Review' => 'Planning / Design',
                'Material Preparation' => 'Commercial / Production',
                'Dispatched' => 'Material / Service Ready',
                'Meeting Conducted' => 'Execution / Activity',
                'Follow-up Uploaded' => 'Execution / Activity',
                'Closed' => 'Completed / Closed',
            ],
            'actions' => [
                'Request Raised' => [
                    ['key' => 'review', 'label' => 'Accept for support', 'actor' => 'branding', 'to' => 'Support Review', 'tone' => 'primary'],
                    ['key' => 'return', 'label' => 'Return to requester', 'actor' => 'branding', 'to' => 'Returned', 'remarks' => 'required', 'tone' => 'danger'],
                ],
                'Support Review' => [
                    ['key' => 'prepare', 'label' => 'Start material preparation', 'actor' => 'branding', 'to' => 'Material Preparation', 'tone' => 'primary'],
                ],
                'Material Preparation' => [
                    ['key' => 'dispatch', 'label' => 'Mark dispatched', 'actor' => 'logistics', 'to' => 'Dispatched', 'tone' => 'primary'],
                ],
                'Dispatched' => [
                    ['key' => 'conducted', 'label' => 'Meeting conducted', 'actor' => 'requester', 'to' => 'Meeting Conducted', 'tone' => 'primary'],
                ],
                'Meeting Conducted' => [
                    ['key' => 'followup', 'label' => 'Upload follow-up', 'actor' => 'requester', 'to' => 'Follow-up Uploaded', 'remarks' => 'required', 'files' => true, 'tone' => 'primary'],
                ],
                'Follow-up Uploaded' => [
                    ['key' => 'close', 'label' => 'Close request', 'actor' => 'branding', 'to' => 'Closed', 'tone' => 'success'],
                ],
            ],
        ],
        'gift_pop' => [
            'steps' => [
                'Request Raised' => 'Request Raised',
                'Approved' => 'Approval',
                'Item Confirmed' => 'Planning / Design',
                'Stock Checked' => 'Commercial / Production',
                'Prepared' => 'Material / Service Ready',
                'Dispatched' => 'Material / Service Ready',
                'Delivered' => 'Execution / Activity',
                'Closed' => 'Completed / Closed',
            ],
            'actions' => [
                // Gifts need a Director's approval; POP-only requests go straight to Branding.
                'Request Raised' => [
                    ['key' => 'approve', 'label' => 'Approve', 'actor' => 'director', 'to' => 'Approved', 'when' => 'needs_approval', 'tone' => 'success'],
                    ['key' => 'return', 'label' => 'Return to requester', 'actor' => 'director', 'to' => 'Returned', 'remarks' => 'required', 'when' => 'needs_approval', 'tone' => 'danger'],
                    ['key' => 'confirm', 'label' => 'Confirm items', 'actor' => 'branding', 'to' => 'Item Confirmed', 'when' => 'no_approval', 'tone' => 'primary'],
                    ['key' => 'return_branding', 'label' => 'Return to requester', 'actor' => 'branding', 'to' => 'Returned', 'remarks' => 'required', 'when' => 'no_approval', 'tone' => 'danger'],
                ],
                'Approved' => [
                    ['key' => 'confirm', 'label' => 'Confirm items', 'actor' => 'branding', 'to' => 'Item Confirmed', 'tone' => 'primary'],
                ],
                'Item Confirmed' => [
                    ['key' => 'stock', 'label' => 'Stock available', 'actor' => 'logistics', 'to' => 'Stock Checked', 'remarks' => 'optional', 'tone' => 'primary'],
                ],
                'Stock Checked' => [
                    ['key' => 'prepare', 'label' => 'Mark prepared', 'actor' => 'logistics', 'to' => 'Prepared', 'tone' => 'primary'],
                ],
                'Prepared' => [
                    ['key' => 'dispatch', 'label' => 'Mark dispatched', 'actor' => 'logistics', 'to' => 'Dispatched', 'remarks' => 'optional', 'tone' => 'primary'],
                ],
                'Dispatched' => [
                    ['key' => 'delivered', 'label' => 'Received / delivered', 'actor' => 'requester', 'to' => 'Delivered', 'tone' => 'primary'],
                ],
                'Delivered' => [
                    ['key' => 'close', 'label' => 'Close request', 'actor' => 'branding', 'to' => 'Closed', 'tone' => 'success'],
                ],
            ],
        ],
    ];

    /** Permission that makes a user act as each team. */
    const ACTOR_PERMISSIONS = [
        'director' => 'approval-action',
        'branding' => 'meetplanning-update',
        'logistics' => 'logisticstask-update',
    ];

    public static function handles(string $module): bool
    {
        return isset(self::FLOWS[$module]);
    }

    public static function steps(string $module): array
    {
        return array_keys(self::FLOWS[$module]['steps'] ?? []);
    }

    /** Gift requests need a Director's approval. POP, catalogue and sample requests do not. */
    public static function needsApproval(PortalRequest $request): bool
    {
        return $request->module === 'gift_pop'
            && $request->items->contains(fn ($item) => $item->item_type === 'gift');
    }

    public static function mayAct(User $user, PortalRequest $request, string $actor): bool
    {
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }
        if ($actor === 'requester') {
            return $request->user_id === $user->id;
        }

        return $user->can(self::ACTOR_PERMISSIONS[$actor] ?? '__none__');
    }

    /** Buttons this user can press now. */
    public static function actionsFor(User $user, PortalRequest $request): array
    {
        $request->loadMissing('items');
        $rows = self::FLOWS[$request->module]['actions'][$request->status] ?? [];

        return collect($rows)
            ->filter(fn ($row) => match ($row['when'] ?? null) {
                'needs_approval' => self::needsApproval($request),
                'no_approval' => ! self::needsApproval($request),
                default => true,
            })
            ->filter(fn ($row) => self::mayAct($user, $request, $row['actor']))
            ->values()->all();
    }

    /** The action that is pending now, with the team that must press it (for "waiting on" text). */
    public static function waitingOn(PortalRequest $request): ?string
    {
        $request->loadMissing('items');
        $row = collect(self::FLOWS[$request->module]['actions'][$request->status] ?? [])
            ->first(fn ($row) => match ($row['when'] ?? null) {
                'needs_approval' => self::needsApproval($request),
                'no_approval' => ! self::needsApproval($request),
                default => true,
            });

        return $row ? ['director' => 'Director approval', 'branding' => 'Branding', 'logistics' => 'Logistics', 'requester' => 'the requester'][$row['actor']] : null;
    }
}
