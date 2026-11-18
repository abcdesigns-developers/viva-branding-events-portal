<?php

namespace Tests\Feature;

use App\Models\GiftItem;
use App\Models\PortalRequest;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockLedgerTest extends TestCase
{
    use RefreshDatabase;

    private function requestFor(User $user, GiftItem $item, int $qty): PortalRequest
    {
        $request = new PortalRequest(['module' => 'gift_pop', 'user_id' => $user->id, 'status' => 'Prepared', 'stage' => 'Material / Service Ready']);
        $request->save();
        $request->items()->create(['item_type' => 'gift', 'item_id' => $item->id, 'quantity' => $qty]);

        return $request->load('items');
    }

    public function test_stock_is_taken_out_once_and_shortages_are_reported(): void
    {
        $user = User::factory()->create();
        $item = GiftItem::create(['name' => 'Test Calendar', 'status' => 'ACTIVE']);
        StockMovement::create(['item_type' => 'gift', 'item_id' => $item->id, 'type' => 'in', 'quantity' => 50]);

        $small = $this->requestFor($user, $item, 20);
        $this->assertSame([], Stock::shortages($small));

        Stock::deduct($small, $user->id);
        Stock::deduct($small, $user->id);               // second call must do nothing
        $this->assertSame(30, Stock::balance('gift', $item->id));
        $this->assertSame([], Stock::shortages($small)); // stock already issued to it still counts

        $big = $this->requestFor($user, $item, 40);
        $short = Stock::shortages($big);
        $this->assertCount(1, $short);
        $this->assertSame(30, $short[0]['have']);
        $this->assertSame(40, $short[0]['need']);
    }
}
