<?php

namespace Tests\Feature;

use App\Models\PortalRequest;
use App\Models\User;
use App\Notifications\RequestUpdated;
use App\Support\RequestNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_requester_is_told_when_a_request_comes_back_to_them(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $request = new PortalRequest(['module' => 'meet_event', 'user_id' => $user->id, 'status' => 'Request Raised', 'stage' => 'Request Raised']);
        $request->request_no = 'MEET-2026-0001';
        $request->submitted_at = now();
        $request->save();

        $request->status = 'Returned';
        $request->save();

        Notification::assertSentTo($user, RequestUpdated::class, fn ($n) => $n->status === 'Returned' && $n->actionNeeded);
    }

    public function test_notifications_page_and_open_mark_as_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new RequestUpdated('PRES-2026-0001', 'Support Review', 'Title', 'Line', url('/profile'), true));
        $id = $user->notifications()->first()->id;

        $this->actingAs($user)->get('/notifications')->assertOk();
        $this->actingAs($user)->get('/notifications/' . $id)->assertRedirect(url('/profile'));
        $this->assertNotNull($user->notifications()->first()->read_at);
    }

    public function test_request_numbers_count_up_per_module_and_year(): void
    {
        $a = RequestNumber::next('presentation');
        $b = RequestNumber::next('presentation');
        $c = RequestNumber::next('gift_pop');

        $this->assertSame('PRES-' . now()->year . '-0001', $a);
        $this->assertSame('PRES-' . now()->year . '-0002', $b);
        $this->assertSame('GIFT-' . now()->year . '-0001', $c);
    }
}
