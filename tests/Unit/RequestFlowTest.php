<?php

namespace Tests\Unit;

use App\Models\PortalRequest;
use App\Models\RequestItem;
use App\Support\RequestFlow;
use Tests\TestCase;

class RequestFlowTest extends TestCase
{
    public function test_every_step_can_move_forward_and_ends_at_closed(): void
    {
        foreach (RequestFlow::FLOWS as $module => $flow) {
            $steps = array_keys($flow['steps']);
            $this->assertSame('Closed', end($steps), $module);

            foreach ($flow['actions'] as $from => $actions) {
                $this->assertContains($from, $steps, "$module has actions for an unknown status $from");
                foreach ($actions as $action) {
                    $this->assertTrue(
                        $action['to'] === 'Returned' || in_array($action['to'], $steps, true),
                        "$module: $from -> {$action['to']} is not a step"
                    );
                    $this->assertArrayHasKey($action['actor'], ['requester' => 1, 'director' => 1, 'branding' => 1, 'logistics' => 1]);
                }
            }

            // Every status except Closed has a way forward.
            foreach (array_diff($steps, ['Closed']) as $status) {
                $this->assertArrayHasKey($status, $flow['actions'], "$module: nothing moves $status forward");
            }
        }
    }

    public function test_only_gift_requests_with_a_gift_item_need_director_approval(): void
    {
        $gift = new PortalRequest(['module' => 'gift_pop']);
        $gift->setRelation('items', collect([new RequestItem(['item_type' => 'gift'])]));
        $pop = new PortalRequest(['module' => 'gift_pop']);
        $pop->setRelation('items', collect([new RequestItem(['item_type' => 'pop'])]));
        $presentation = new PortalRequest(['module' => 'presentation']);
        $presentation->setRelation('items', collect([new RequestItem(['item_type' => 'gift'])]));

        $this->assertTrue(RequestFlow::needsApproval($gift));
        $this->assertFalse(RequestFlow::needsApproval($pop));
        $this->assertFalse(RequestFlow::needsApproval($presentation));
    }
}
