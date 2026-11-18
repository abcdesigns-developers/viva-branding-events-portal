<?php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\BrandingMaterial;
use App\Models\CatalogueItem;
use App\Models\Dispatch;
use App\Models\Event;
use App\Models\EventType;
use App\Models\GiftItem;
use App\Models\GuestCategory;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\MeetProposal;
use App\Models\MeetProposalOption;
use App\Models\Payment;
use App\Models\PopItem;
use App\Models\PortalRequest;
use App\Models\ProductSample;
use App\Models\PurchaseOrder;
use App\Models\RequestGuestCategory;
use App\Models\RequestHistory;
use App\Models\StockMovement;
use App\Support\Stock;
use App\Models\TeamTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Realistic sample data (amounts in rupees) tied to the @vivaacp.com logins, dated around today so the
 * dashboard reminders have something to show. Run TeamUsersSeeder, TestUsersSeeder and EventMasterSeeder first.
 * Safe to run again: it removes its own earlier demo requests (marked event_name = 'demo-seed') and rebuilds them.
 * Not for production. Run: php artisan db:seed --class=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    private const MARK = 'demo-seed';

    private const MEET_LADDER = ['Request Raised', 'Initial Approval', 'Branding Planning', 'Proposal Submitted', 'Final Approval', 'PO Ready', 'Payment Done', 'Material Ready', 'Dispatched', 'Event Ready', 'Completed', 'Closed'];

    private array $users = [];

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Demo data is not created in production.');
            return;
        }

        DB::transaction(function () {
            $this->loadUsers();
            $this->masters();
            $this->clearOld();
            $this->meets();
            $this->presentations();
            $this->gifts();
            $this->fixTestAmounts();
        });

        $this->command?->info('Demo data ready. Log in as hod1@, hod2@, zm1@, zm2@, sales1@, branding1@, director1@, approver1@ (all @vivaacp.com) to see it.');
    }

    // ---------------------------------------------------------------- masters

    private function loadUsers(): void
    {
        foreach (['hod1', 'hod2', 'zm1', 'zm2', 'sales1', 'branding1', 'purchase1', 'accounts1', 'logistics1', 'director1', 'approver1'] as $key) {
            $this->users[$key] = User::where('email', $key . '@vivaacp.com')->first();
        }
        $this->users['mayank'] = User::where('email', 'mayank@vivaacp.com')->first();
        $this->users['nitin'] = User::where('email', 'nitin@vivaacp.com')->first();
        $this->users['vipin'] = User::where('email', 'vipin@vivaacp.com')->first();
        $this->users['prathamesh'] = User::where('email', 'prathamesh@vivaacp.com')->first();

        // Real team members stand in for the test logins where those are missing.
        $this->users['branding1'] ??= $this->users['prathamesh'];
        $this->users['director1'] ??= $this->users['mayank'];
        $this->users['approver1'] ??= $this->users['vipin'];
        $missing = collect(['hod1', 'zm1', 'sales1', 'branding1', 'director1'])->filter(fn ($k) => ! $this->users[$k]);
        if ($missing->isNotEmpty()) {
            throw new \RuntimeException('Run TeamUsersSeeder and TestUsersSeeder first. Missing: ' . $missing->implode(', '));
        }
        foreach (['hod2' => 'hod1', 'zm2' => 'zm1', 'purchase1' => 'branding1', 'accounts1' => 'branding1', 'logistics1' => 'branding1'] as $k => $fallback) {
            $this->users[$k] ??= $this->users[$fallback];
        }
    }

    /** More realistic master rows. Only adds what is missing. */
    private function masters(): void
    {
        $add = function (string $model, array $names, array $extra = []) {
            foreach ($names as $name) {
                $model::firstOrCreate(['name' => $name], ['status' => 'ACTIVE'] + $extra);
            }
        };
        $add(GiftItem::class, ['Premium Desk Calendar 2027', 'Executive Diary & Pen Set', 'Steel Water Bottle (Branded)', 'Leather Laptop Bag', 'Wall Clock', 'Crystal Trophy', 'Dry Fruit Gift Box', 'Brass Ganesh Idol', 'Corporate Gift Hamper']);
        $add(PopItem::class, ['Standee', 'Danglers', 'Shelf Strip', 'Product Poster', 'Counter Display', 'Window Sticker', 'Showroom Wall Branding', 'Glow Sign Board', 'Dealer Name Board']);
        $add(ProductSample::class, ['Sliding Window Sample', 'Casement Window Sample', 'Door Profile Sample', 'Glass Sample Kit', 'Hardware Sample Board', 'Colour Finish Swatch Set', 'uPVC Profile Cross-section Set']);
        $add(CatalogueItem::class, ['Window Systems Catalogue', 'Door Systems Catalogue', 'Architect Reference Book', 'Dealer Price List', 'Project Reference Portfolio', 'Colour & Finish Guide'], ['description' => 'Latest printed edition.']);
        $add(Hotel::class, ['Hyatt Regency, Ahmedabad', 'Taj Skyline, Ahmedabad', 'Novotel, Pune', 'Radisson Blu, Nagpur', 'The Grand Bhagwati, Surat', 'Lords Eco Inn, Surat', 'Hotel Sun-N-Sand, Mumbai', 'Courtyard by Marriott, Mumbai', 'The Leela Ambience, Gurugram', 'ITC Grand Chola, Chennai', 'Novotel Convention Centre, Hyderabad', 'Fairmont, Jaipur', 'Clarks Awadh, Lucknow', 'Sayaji Hotel, Indore', 'ITC Gardenia, Bengaluru']);
        $add(GuestCategory::class, ['Distributor', 'Dealer', 'Architect', 'Fabricator', 'Interior Designer', 'Builder / Developer', 'Contractor', 'Government Official', 'Others']);
        $add(BrandingMaterial::class, ['Backdrop', 'Banners', 'Stage Branding', 'Standees', 'Digital Creatives', 'Welcome Arch', 'Roll-up Standees', 'Table Branding']);
        $this->setRates();
        $this->openingStock();
    }

    /** Sample rates in rupees so request totals and stock value show up. Only fills items that have no rate yet. */
    private function setRates(): void
    {
        $rates = [
            GiftItem::class => ['Premium Desk Calendar 2027' => 180, 'Executive Diary & Pen Set' => 650, 'Steel Water Bottle (Branded)' => 420, 'Leather Laptop Bag' => 1850, 'Wall Clock' => 575, 'Crystal Trophy' => 1400, 'Dry Fruit Gift Box' => 950, 'Brass Ganesh Idol' => 1200, 'Corporate Gift Hamper' => 2400],
            PopItem::class => ['Standee' => 850, 'Danglers' => 22, 'Shelf Strip' => 35, 'Product Poster' => 60, 'Counter Display' => 1650, 'Window Sticker' => 18, 'Showroom Wall Branding' => 9500, 'Glow Sign Board' => 6200, 'Dealer Name Board' => 3800],
            ProductSample::class => ['Sliding Window Sample' => 2800, 'Casement Window Sample' => 3100, 'Door Profile Sample' => 1900, 'Glass Sample Kit' => 1450, 'Hardware Sample Board' => 2200, 'Colour Finish Swatch Set' => 380, 'uPVC Profile Cross-section Set' => 1750],
            CatalogueItem::class => ['Window Systems Catalogue' => 145, 'Door Systems Catalogue' => 130, 'Architect Reference Book' => 520, 'Dealer Price List' => 40, 'Project Reference Portfolio' => 380, 'Colour & Finish Guide' => 85],
        ];
        foreach ($rates as $model => $byName) {
            foreach ($byName as $name => $rate) {
                $model::where('name', $name)->whereNull('unit_cost')->update(['unit_cost' => $rate]);
            }
        }
    }

    /** Opening stock for every master item that has none, with a few low or empty so the Stock screen shows alerts. */
    private function openingStock(): void
    {
        $special = ['Leather Laptop Bag' => 18, 'Crystal Trophy' => 0, 'Danglers' => 800, 'Hardware Sample Board' => 12, 'Standee' => 60, 'Window Systems Catalogue' => 650, 'Door Systems Catalogue' => 500];
        $default = ['gift' => 300, 'pop' => 120, 'catalogue' => 400, 'sample' => 45];

        foreach (Stock::TYPES as $type) {
            $model = PortalRequest::ITEM_TYPES[$type]['model'];
            foreach ($model::all() as $item) {
                if (StockMovement::where('item_type', $type)->where('item_id', $item->id)->exists()) {
                    continue;
                }
                $qty = $special[$item->name] ?? $default[$type];
                if ($qty > 0) {
                    StockMovement::create(['item_type' => $type, 'item_id' => $item->id, 'type' => 'in', 'quantity' => $qty, 'unit_cost' => $item->unit_cost, 'note' => 'Opening stock', 'user_id' => $this->users['logistics1']->id]);
                }
            }
        }
    }

    private function clearOld(): void
    {
        $ids = PortalRequest::where('event_name', self::MARK)->pluck('id');
        if ($ids->isEmpty()) {
            return;
        }
        StockMovement::whereIn('request_id', $ids)->delete();
        $taskIds = TeamTask::whereIn('request_id', $ids)->pluck('id');
        PurchaseOrder::whereIn('team_task_id', $taskIds)->delete();
        Payment::whereIn('team_task_id', $taskIds)->delete();
        Dispatch::whereIn('team_task_id', $taskIds)->delete();
        TeamTask::whereIn('request_id', $ids)->delete();
        MeetProposal::whereIn('request_id', $ids)->get()->each(fn ($p) => $p->delete());
        PortalRequest::whereIn('id', $ids)->delete();
    }

    // ------------------------------------------------------------------- Meet

    private function meets(): void
    {
        $t = Carbon::today();

        // Waiting for the Director's final approval: realistic quotes, a recommended venue.
        $this->meet('Architect Meet', 'Surat', 'sales1', $t->copy()->addDays(9), 'Proposal Submitted', 110, [
            'Architect' => 70, 'Interior Designer' => 25, 'Builder / Developer' => 15,
        ], 'Evening meet for Surat architects and interior designers to showcase the new slim sliding series, with dinner.', [
            'venues' => [
                ['The Grand Bhagwati, Surat', 'Banquet hall, Dumas Road', 150, 185000, true],
                ['Lords Eco Inn, Surat', 'Conference hall, Ring Road', 90, 128000, false],
            ],
            'vendors' => [
                ['Shree Events & Decor', 'Stage, backdrop and floral decoration', 68000],
                ['Sai Audio Visuals', 'Sound, LED wall and lighting', 42000],
                ['Pixel Prints', 'Standees, banners and welcome arch printing', 26500],
            ],
            'plan' => 'The Grand Bhagwati is recommended: central location for Surat architects, in-house parking and a 150-seat hall with LED wall. Total with decoration, AV and printing comes to about Rs 3.21 lakh against Rs 3.5 lakh planned.',
        ]);

        // Waiting for the initial approval.
        $this->meet('Fabricator Meet', 'Pune', 'zm2', $t->copy()->addDays(21), 'Request Raised', 90, [
            'Fabricator' => 75, 'Dealer' => 15,
        ], 'Fabricator meet to launch the new casement range and the revised fabrication margin scheme for Western Maharashtra.');

        // Material is ready and the date is close: shows up in reminders.
        $this->meet('Dealer Meet', 'Ahmedabad', 'hod2', $t->copy()->addDays(4), 'Material Ready', 140, [
            'Dealer' => 110, 'Distributor' => 30,
        ], 'Annual dealer meet for Gujarat with product display, scheme announcement and dinner.', [
            'venues' => [['Hyatt Regency, Ahmedabad', 'Grand ballroom, Ashram Road', 200, 295000, true], ['Taj Skyline, Ahmedabad', 'Ballroom, SG Highway', 180, 330000, false]],
            'vendors' => [['Utsav Event Management', 'Stage, backdrop and lighting', 145000], ['Digital Dot Printers', 'Branding collaterals', 38000]],
            'plan' => 'Hyatt Regency recommended for capacity and a lower rate than Taj Skyline. Total estimate Rs 4.78 lakh.',
            'po' => [['Utsav Event Management', 'PO/2026/1187', 145000], ['Digital Dot Printers', 'PO/2026/1188', 38000]],
            'advance' => [['Utsav Event Management', 72500, 'NEFT'], ['Digital Dot Printers', 19000, 'UPI']],
        ]);

        // Finished meet, a few days ago.
        $this->meet('Architect Meet', 'Nagpur', 'hod1', $t->copy()->subDays(12), 'Completed', 60, [
            'Architect' => 45, 'Interior Designer' => 15,
        ], 'Architect meet in Nagpur to present project references and the Vidarbha dealer network.', [
            'venues' => [['Radisson Blu, Nagpur', 'Banquet hall, Wardha Road', 100, 98000, true]],
            'vendors' => [['Rangoli Decorators', 'Decoration and backdrop', 34000], ['Vidarbha Sound & Light', 'Sound and projector', 18000]],
            'plan' => 'Radisson Blu is the only venue with parking for 60 cars near the airport side. Estimate Rs 1.5 lakh.',
            'po' => [['Rangoli Decorators', 'PO/2026/1102', 34000], ['Vidarbha Sound & Light', 'PO/2026/1103', 18000]],
            'advance' => [['Rangoli Decorators', 17000, 'NEFT']],
            'final' => [['Rangoli Decorators', 17000, 'NEFT'], ['Vidarbha Sound & Light', 18000, 'UPI']],
            'dispatch' => ['Safexpress', 'SFX4829104'],
        ]);

        // Returned for correction.
        $this->meet('In-Shop Meet', 'Delhi', 'zm1', $t->copy()->addDays(15), 'Returned', 30, ['Fabricator' => 20, 'Dealer' => 10],
            'In-shop meet at the Karol Bagh dealer showroom for NCR fabricators.', [], 'Please add the dealer name and the expected spend per head, then resubmit.');

        // More requests across every stage and login, with event dates from overdue to a month out.
        $this->meet('Fabricator Meet', 'Hyderabad', 'sales1', $t->copy()->addDays(26), 'Initial Approval', 85, ['Fabricator' => 70, 'Dealer' => 15],
            'Fabricator meet for Telangana to introduce the new thermal-break casement series and the fabricator partner scheme.');

        $this->meet('Dealer Meet', 'Indore', 'zm2', $t->copy()->addDays(20), 'Branding Planning', 70, ['Dealer' => 55, 'Distributor' => 15],
            'Central India dealer meet with a product display and the new year target announcement.');

        $this->meet('Fabricator Meet', 'Jaipur', 'zm1', $t->copy()->addDays(16), 'Final Approval', 95, ['Fabricator' => 80, 'Dealer' => 15],
            'Rajasthan fabricator meet with a live demo of the slim sliding series.', [
            'venues' => [['Fairmont, Jaipur', 'Ballroom, Riico Industrial Area', 180, 240000, true], ['Clarks Amer, Jaipur', 'Banquet hall, Jawahar Circle', 120, 165000, false]],
            'vendors' => [['Pink City Events', 'Stage, backdrop and live demo setup', 82000], ['Raj Audio Visuals', 'Sound, projector and LED', 36000]],
            'plan' => 'Fairmont recommended for the larger hall that fits the demo setup. Total about Rs 3.58 lakh.',
        ]);

        $this->meet('Dealer Meet', 'Lucknow', 'hod1', $t->copy()->addDays(8), 'Payment Done', 120, ['Dealer' => 95, 'Distributor' => 25],
            'UP dealer meet to announce the festive season scheme and the new price list.', [
            'venues' => [['Clarks Awadh, Lucknow', 'Banquet hall, MG Marg', 150, 135000, true]],
            'vendors' => [['Nawab Events', 'Stage, backdrop and floral decoration', 56000], ['Lucknow Digital Prints', 'Standees and banners', 22000]],
            'plan' => 'Clarks Awadh is central and has parking for 60 cars. Total estimate Rs 2.13 lakh.',
            'po' => [['Nawab Events', 'PO/2026/1231', 56000], ['Lucknow Digital Prints', 'PO/2026/1232', 22000]],
            'advance' => [['Nawab Events', 28000, 'NEFT'], ['Lucknow Digital Prints', 11000, 'UPI']],
        ]);

        // Event tomorrow: material has already left.
        $this->meet('Architect Meet', 'Chennai', 'hod2', $t->copy()->addDay(), 'Dispatched', 75, ['Architect' => 55, 'Interior Designer' => 20],
            'Architect and interior designer evening in Chennai on the new coastal-grade window series.', [
            'venues' => [['ITC Grand Chola, Chennai', 'Pavilion hall, Guindy', 120, 210000, true]],
            'vendors' => [['Marina Events', 'Stage and decor', 74000], ['Southern Sound and Light', 'AV and LED wall', 39000]],
            'plan' => 'ITC Grand Chola recommended for the premium architect audience. Total about Rs 3.23 lakh.',
            'po' => [['Marina Events', 'PO/2026/1244', 74000], ['Southern Sound and Light', 'PO/2026/1245', 39000]],
            'advance' => [['Marina Events', 37000, 'NEFT'], ['Southern Sound and Light', 19500, 'UPI']],
            'final' => [['Marina Events', 37000, 'NEFT']],
            'dispatch' => ['VRL Logistics', 'VRL90283345'],
        ]);

        // Event today: everything is in place.
        $this->meet('Fabricator Meet', 'Mumbai', 'hod1', $t->copy(), 'Event Ready', 65, ['Fabricator' => 50, 'Dealer' => 15],
            'Mumbai fabricator meet with the new fabrication margin scheme.', [
            'venues' => [['Courtyard by Marriott, Mumbai', 'Banquet hall, Andheri East', 90, 120000, true]],
            'vendors' => [['Mumbai Stage Works', 'Stage, backdrop and registration desk', 64000], ['Dot Print Solutions', 'Standees and banners', 24000]],
            'plan' => 'Courtyard by Marriott chosen for access from Andheri and Navi Mumbai. Total about Rs 2.08 lakh.',
            'po' => [['Mumbai Stage Works', 'PO/2026/1252', 64000], ['Dot Print Solutions', 'PO/2026/1253', 24000]],
            'advance' => [['Mumbai Stage Works', 32000, 'NEFT'], ['Dot Print Solutions', 12000, 'UPI']],
            'final' => [['Mumbai Stage Works', 32000, 'NEFT'], ['Dot Print Solutions', 12000, 'UPI']],
            'dispatch' => ['Blue Dart', 'BD7712004'],
        ]);

        // Done and closed a while ago.
        $this->meet('Dealer Meet', 'Bengaluru', 'sales1', $t->copy()->subDays(25), 'Closed', 100, ['Dealer' => 80, 'Distributor' => 20],
            'Karnataka dealer meet with the annual scheme and awards.', [
            'venues' => [['ITC Gardenia, Bengaluru', 'Ballroom, Residency Road', 160, 255000, true]],
            'vendors' => [['Namma Events', 'Stage, decor and awards setup', 91000], ['Bangalore Print House', 'Collaterals and standees', 31000]],
            'plan' => 'ITC Gardenia recommended for capacity and city-centre access. Total about Rs 3.77 lakh.',
            'po' => [['Namma Events', 'PO/2026/1041', 91000], ['Bangalore Print House', 'PO/2026/1042', 31000]],
            'advance' => [['Namma Events', 45500, 'NEFT'], ['Bangalore Print House', 15500, 'UPI']],
            'final' => [['Namma Events', 45500, 'NEFT'], ['Bangalore Print House', 15500, 'UPI']],
            'dispatch' => ['Safexpress', 'SFX4811902'],
        ]);
    }

    private function meet(string $type, string $city, string $requester, Carbon $date, string $status, int $guests, array $guestMix, string $objective, array $opts = [], ?string $returnRemark = null): void
    {
        $user = $this->users[$requester];
        $event = Event::byCode(Event::MEET_EVENT);
        $request = $this->newRequest('meet_event', $user, [
            'event_id' => $event?->id,
            'event_type_id' => EventType::where('event_id', $event?->id)->where('name', $type)->value('id'),
            'location_id' => Location::where('name', $city)->value('id'),
            'city' => $city,
            'objective' => $objective,
            'preferred_date_1' => $date,
            'preferred_date_2' => $date->copy()->addDay(),
            'team_strength' => 6,
            'total_guests' => $guests,
            'branding_needs' => BrandingMaterial::whereIn('name', ['Backdrop', 'Banners', 'Standees', 'Welcome Arch'])->pluck('id')->all(),
        ], $status === 'Returned' ? 'Request Raised' : $status);

        foreach ($guestMix as $name => $count) {
            if ($id = GuestCategory::where('name', $name)->value('id')) {
                RequestGuestCategory::create(['request_id' => $request->id, 'guest_category_id' => $id, 'guest_count' => $count]);
            }
        }

        $back = fn (int $days) => now()->subDays($days);
        $this->history($request, $user, 'Submitted', null, 'Request Raised', null, $back(20));

        if ($status === 'Returned') {
            $this->approval($request, 'initial', Approval::RETURNED, $this->users['director1'], $returnRemark, $back(3));
            $this->history($request, $this->users['director1'], 'Initial approval - returned to the requester', 'Request Raised', 'Returned', $returnRemark, $back(3));
            $request->update(['status' => 'Returned', 'stage' => 'Request Raised']);

            return;
        }

        $level = array_search($status, self::MEET_LADDER, true);
        if ($level >= 1) {
            $by = $this->users['approver1'];
            $this->approval($request, 'initial', Approval::APPROVED, $by, 'Approved. Keep the spend within the planned budget.', $back(17));
            $this->history($request, $by, 'Initial approval given', 'Request Raised', 'Initial Approval', 'Approved. Keep the spend within the planned budget.', $back(17));
        }
        if ($level >= 3 && ! empty($opts['venues'])) {
            $this->proposal($request, $opts, $back(14));
            $this->history($request, $this->users['branding1'], 'Proposal submitted', 'Branding Planning', 'Proposal Submitted', null, $back(14));
        }
        if ($level >= 4) {
            $by = $this->users['director1'];
            $this->approval($request, 'final', Approval::APPROVED, $by, 'Go ahead with the recommended venue.', $back(12));
            $this->history($request, $by, 'Final proposal approval given', 'Proposal Submitted', 'Final Approval', 'Go ahead with the recommended venue.', $back(12));
            foreach (array_keys(TeamTask::TEAMS) as $team) {
                TeamTask::firstOrCreate(['request_id' => $request->id, 'team' => $team], ['status' => TeamTask::PENDING]);
            }
            $this->history($request, $by, 'Tasks released to Purchase, Accounts and Logistics', 'Final Approval', 'Final Approval', null, $back(12));
        }
        if ($level >= 5 && ! empty($opts['po'])) {
            $task = $request->teamTasks()->where('team', 'purchase')->first();
            foreach ($opts['po'] as $i => [$vendor, $number, $amount]) {
                PurchaseOrder::create(['team_task_id' => $task->id, 'vendor_name' => $vendor, 'po_number' => $number, 'po_date' => $back(10 - $i)->toDateString(), 'amount' => $amount]);
            }
            $task->update(['status' => TeamTask::DONE, 'completed_at' => $back(10), 'completed_by' => $this->users['purchase1']->id]);
            $this->history($request, $this->users['purchase1'], 'Purchase orders raised', 'Final Approval', 'PO Ready', null, $back(10));
        }
        if ($level >= 6 && ! empty($opts['advance'])) {
            $task = $request->teamTasks()->where('team', 'accounts')->first();
            foreach ($opts['advance'] as [$to, $amount, $mode]) {
                Payment::create(['team_task_id' => $task->id, 'type' => 'advance', 'paid_to' => $to, 'amount' => $amount, 'paid_on' => $back(8)->toDateString(), 'mode' => $mode, 'reference' => strtoupper(substr(md5($to . $amount), 0, 12))]);
            }
            foreach ($opts['final'] ?? [] as [$to, $amount, $mode]) {
                Payment::create(['team_task_id' => $task->id, 'type' => 'final', 'paid_to' => $to, 'amount' => $amount, 'paid_on' => $back(4)->toDateString(), 'mode' => $mode, 'reference' => strtoupper(substr(md5($amount . $to), 0, 12))]);
            }
            $task->update(['status' => TeamTask::DONE, 'completed_at' => $back(8), 'completed_by' => $this->users['accounts1']->id]);
            $this->history($request, $this->users['accounts1'], 'Payments recorded', 'PO Ready', 'Payment Done', null, $back(8));
        }
        if ($level >= 7) {
            $task = $request->teamTasks()->where('team', 'logistics')->first();
            $task?->update(['status' => TeamTask::IN_PROGRESS, 'material_ready_at' => $back(3)]);
            $this->history($request, $this->users['logistics1'], 'All material ready', 'Payment Done', 'Material Ready', null, $back(3));
        }
        if ($level >= 8 && ! empty($opts['dispatch'])) {
            $task = $request->teamTasks()->where('team', 'logistics')->first();
            Dispatch::create(['team_task_id' => $task->id, 'courier' => $opts['dispatch'][0], 'tracking_no' => $opts['dispatch'][1], 'dispatched_on' => $back(14)->toDateString(), 'expected_on' => $back(12)->toDateString(), 'remarks' => '14 cartons: standees, backdrop and welcome arch']);
            $task->update(['status' => TeamTask::DONE, 'completed_at' => $back(14), 'completed_by' => $this->users['logistics1']->id]);
            $this->history($request, $this->users['logistics1'], 'Material dispatched', 'Material Ready', 'Dispatched', null, $back(14));
        }
        if ($level >= 11 || $status === 'Completed') {
            $this->history($request, $this->users['branding1'], 'Event completed', 'Event Ready', 'Completed', 'Good turnout, 52 of 60 invited guests attended.', $back(11));
        }
    }

    private function proposal(PortalRequest $request, array $opts, Carbon $at): void
    {
        $venue = collect($opts['venues'])->firstWhere(4, true) ?? $opts['venues'][0];
        $estimate = $venue[3] + collect($opts['vendors'])->sum(2);
        $proposal = MeetProposal::create([
            'request_id' => $request->id,
            'decoration_plan' => 'Stage with printed backdrop, floral entrance arch, branded registration desk.',
            'branding_plan' => 'Welcome arch, 6 standees at the entrance and stage, product display table with sample kits.',
            'setup_plan' => 'Setup the evening before from 4 PM. Rehearsal of the AV and presentation at 9 AM on the event day.',
            'recommended_plan' => $opts['plan'],
            'estimated_budget' => $estimate,
            'submitted_at' => $at,
        ]);
        foreach ($opts['venues'] as [$name, $details, $capacity, $amount, $recommended]) {
            MeetProposalOption::create([
                'meet_proposal_id' => $proposal->id, 'type' => 'venue', 'hotel_id' => Hotel::where('name', $name)->value('id'),
                'name' => $name, 'details' => $details, 'capacity' => $capacity, 'amount' => $amount, 'is_recommended' => $recommended,
            ]);
        }
        foreach ($opts['vendors'] as [$name, $details, $amount]) {
            MeetProposalOption::create(['meet_proposal_id' => $proposal->id, 'type' => 'vendor', 'name' => $name, 'details' => $details, 'amount' => $amount]);
        }
    }

    // --------------------------------------------------------- Presentations

    private function presentations(): void
    {
        $t = Carbon::today();
        $rows = [
            ['Government Office Presentation', 'Gandhinagar', 'sales1', 'Roads & Buildings Dept, Gandhinagar', 'Er. R. K. Patel', $t->copy()->addDays(3), '11:00', 12, 'Request Raised', 'Presentation of fire-rated and acoustic window systems for the new Secretariat annex.'],
            ['Architect Office Presentation', 'Mumbai', 'hod1', 'Studio Lotus Architects, Bandra', 'Ar. Meera Shah', $t->copy()->addDays(6), '15:30', 8, 'Material Preparation', 'Product presentation for a 40-storey residential tower in Worli with samples and finish swatches.'],
            ['Client Office Presentation', 'Pune', 'zm2', 'Kolte-Patil Developers, Baner', 'Mr. Amit Deshmukh', $t->copy()->subDays(2), '14:00', 10, 'Meeting Conducted', 'Window and door system presentation for the Life Republic phase 3 project.'],
            ['Product Presentation', 'Delhi', 'zm1', 'DLF Limited, Gurugram', 'Ms. Kavita Sethi', $t->copy()->subDays(20), '10:30', 6, 'Closed', 'Product presentation to the DLF procurement team for the premium residential range.'],
            ['Architect Office Presentation', 'Ahmedabad', 'hod2', 'CEPT Design Studio, Navrangpura', 'Ar. Jignesh Trivedi', $t->copy()->addDays(8), '16:00', 15, 'Returned', 'Presentation for architects on the new slim sliding series.'],
            ['Government Office Presentation', 'Nagpur', 'hod1', 'PWD Division Office, Civil Lines', 'Er. R. K. Deshmukh', $t->copy()->addDays(4), '12:00', 8, 'Support Review', 'Compliance and product presentation for the district hospital facade tender.'],
            ['Architect Office Presentation', 'Chennai', 'hod2', 'Chennai Design Collective, Adyar', 'Ar. Lakshmi Narayanan', $t->copy()->addDay(), '11:30', 10, 'Dispatched', 'Presentation of coastal-grade window systems with finish samples.'],
            ['Client Office Presentation', 'Surat', 'sales1', 'Diamond Heights Developers, Vesu', 'Mr. Hardik Gajera', $t->copy()->subDays(5), '15:00', 9, 'Follow-up Uploaded', 'Presentation for the Rs 2.4 crore tower project and the finalised window schedule.'],
            ['Product Presentation', 'Hyderabad', 'zm1', 'Prestige Estates, Gachibowli', 'Ms. Anjali Reddy', $t->copy(), '16:30', 14, 'Request Raised', 'Product presentation for the new premium residential launch.'],
            ['Small Site / On-site Meeting', 'Jaipur', 'zm2', 'Mehta Constructions, Vaishali Nagar site office', 'Mr. Rakesh Mehta', $t->copy()->addDays(11), '10:00', 6, 'Material Preparation', 'On-site mock-up review of the sliding window system with the site engineers.'],
        ];

        foreach ($rows as [$type, $city, $who, $org, $contact, $date, $time, $count, $status, $purpose]) {
            $user = $this->users[$who];
            $event = Event::byCode(Event::PRESENTATION);
            $request = $this->newRequest('presentation', $user, [
                'event_id' => $event?->id,
                'event_type_id' => EventType::where('event_id', $event?->id)->where('name', $type)->value('id'),
                'location_id' => Location::where('name', $city)->value('id'),
                'city' => $city, 'organisation' => $org, 'contact_person' => $contact,
                'preferred_date_1' => $date, 'meeting_time' => $time, 'attendee_count' => $count, 'objective' => $purpose,
                'presentation_material' => 'Corporate presentation, project references and product brochures.',
                'needs_design' => $status !== 'Closed',
                'branding_needs' => BrandingMaterial::whereIn('name', ['Standees', 'Banners', 'Digital Creatives'])->pluck('id')->all(),
            ], $status);

            foreach ([['gift', 'Executive Diary & Pen Set', $count], ['catalogue', 'Window Systems Catalogue', $count], ['sample', 'Sliding Window Sample', 1]] as [$kind, $name, $qty]) {
                $this->item($request, $kind, $name, $qty);
            }
            $this->flowHistory('presentation', $request, $status, $user);
            if (in_array($status, ['Dispatched', 'Meeting Conducted', 'Follow-up Uploaded', 'Closed'], true)) {
                Stock::deduct($request->load('items'), $this->users['logistics1']->id);
            }
        }
    }

    // -------------------------------------------------------------- Gift & POP

    private function gifts(): void
    {
        $t = Carbon::today();
        $rows = [
            ['Seasonal Gifts', 'Ahmedabad', 'hod2', 'Top 40 Gujarat architects (New Year gifting)', $t->copy()->addDays(18), 'Request Raised', [['gift', 'Premium Desk Calendar 2027', 500], ['gift', 'Executive Diary & Pen Set', 200]], 'New Year gifting for key architects and channel partners in Gujarat.'],
            ['Client Gifts', 'Surat', 'sales1', 'Diamond Heights Developers, Surat', $t->copy()->addDays(5), 'Approved', [['gift', 'Leather Laptop Bag', 25]], 'Gift for the project head team after the Rs 2.4 crore order confirmation.'],
            ['Dealer / Channel Partner Gifts', 'Pune', 'zm2', 'Pune dealer showrooms (7 outlets)', $t->copy()->addDays(12), 'Item Confirmed', [['pop', 'Standee', 14], ['pop', 'Danglers', 350], ['pop', 'Counter Display', 7]], 'Fresh POP for dealer showrooms before the Pune fabricator meet.'],
            ['Architect Gifts', 'Mumbai', 'hod1', 'Mumbai architect fraternity meet', $t->copy()->addDays(2), 'Dispatched', [['catalogue', 'Architect Reference Book', 150], ['catalogue', 'Window Systems Catalogue', 150], ['sample', 'Colour Finish Swatch Set', 40]], 'Reference books and swatch sets for the architect meet at Worli.'],
            ['Government Department Gifts', 'Delhi', 'zm1', 'PWD Delhi project cell', $t->copy()->subDays(15), 'Closed', [['gift', 'Wall Clock', 10]], 'Courtesy gifts after the PWD tender presentation.'],
            ['Dealer / Channel Partner Gifts', 'Nagpur', 'hod1', 'Vidarbha dealer showrooms (5 outlets)', $t->copy()->addDays(9), 'Stock Checked', [['pop', 'Standee', 10], ['pop', 'Danglers', 200], ['pop', 'Dealer Name Board', 5]], 'POP refresh for Vidarbha dealer outlets after the Nagpur architect meet.'],
            ['Seasonal Gifts', 'Jaipur', 'sales1', 'Top 30 Rajasthan channel partners', $t->copy()->addDays(3), 'Prepared', [['gift', 'Steel Water Bottle (Branded)', 120], ['gift', 'Dry Fruit Gift Box', 60]], 'Festive season gifting for key channel partners in Rajasthan.'],
            ['Client Gifts', 'Indore', 'zm2', 'Sayaji Developers, Indore', $t->copy()->subDays(3), 'Delivered', [['gift', 'Brass Ganesh Idol', 15], ['gift', 'Corporate Gift Hamper', 8]], 'Thank-you gifts to the project team after the order confirmation.'],
            ['Architect Gifts', 'Hyderabad', 'zm1', 'Hyderabad architect community (top 40)', $t->copy()->addDay(), 'Request Raised', [['gift', 'Executive Diary & Pen Set', 40], ['gift', 'Premium Desk Calendar 2027', 40]], 'Year-end gifts for key Hyderabad architects.'],
            ['Event / Exhibition Gifts', 'Surat', 'hod2', 'Guests at the Surat fabricator meet', $t->copy()->subDays(2), 'Request Raised', [['gift', 'Crystal Trophy', 6]], 'Recognition trophies for top fabricators at the Surat meet.'],
        ];

        foreach ($rows as [$type, $city, $who, $recipient, $date, $status, $items, $purpose]) {
            $user = $this->users[$who];
            $event = Event::byCode(Event::GIFT_POP);
            $request = $this->newRequest('gift_pop', $user, [
                'event_id' => $event?->id,
                'event_type_id' => EventType::where('event_id', $event?->id)->where('name', $type)->value('id'),
                'location_id' => Location::where('name', $city)->value('id'),
                'city' => $city, 'recipient' => $recipient, 'required_by' => $date, 'objective' => $purpose,
                'remarks' => 'Approved budget as per the annual gifting plan.',
            ], $status);
            foreach ($items as [$kind, $name, $qty]) {
                $this->item($request, $kind, $name, $qty);
            }
            $this->flowHistory('gift_pop', $request, $status, $user);
            if (in_array($status, ['Prepared', 'Dispatched', 'Delivered', 'Closed'], true)) {
                Stock::deduct($request->load('items'), $this->users['logistics1']->id);
            }
        }
    }

    /** One history row per step the request has already passed, by the person who does that step. */
    private function flowHistory(string $module, PortalRequest $request, string $status, User $requester): void
    {
        $steps = \App\Support\RequestFlow::steps($module);
        $request->load('items');
        if ($module === 'gift_pop' && ! \App\Support\RequestFlow::needsApproval($request)) {
            $steps = array_values(array_diff($steps, ['Approved']));
        }
        $actors = ['Approved' => 'director1', 'Item Confirmed' => 'branding1', 'Stock Checked' => 'logistics1', 'Prepared' => 'logistics1', 'Dispatched' => 'logistics1', 'Delivered' => $requester,
            'Support Review' => 'branding1', 'Material Preparation' => 'branding1', 'Meeting Conducted' => $requester, 'Follow-up Uploaded' => $requester, 'Closed' => 'branding1'];

        $this->history($request, $requester, 'Submitted', null, 'Request Raised', null, now()->subDays(14));
        if ($status === 'Returned') {
            $by = $this->users['branding1'];
            $this->history($request, $by, 'Return to requester', 'Request Raised', 'Returned', 'Please attach the final presentation file and confirm the attendee count.', now()->subDays(2));

            return;
        }
        $reached = array_search($status, $steps, true);
        $from = 'Request Raised';
        foreach ($steps as $i => $step) {
            if ($i === 0 || $i > $reached) {
                continue;
            }
            $actor = $actors[$step] ?? 'branding1';
            $user = $actor instanceof User ? $actor : $this->users[$actor];
            $this->history($request, $user, 'Moved to ' . $step, $from, $step, null, now()->subDays(max(1, 13 - $i * 2)));
            $from = $step;
        }
    }

    // ---------------------------------------------------------------- helpers

    private function newRequest(string $module, User $user, array $fields, string $status): PortalRequest
    {
        $stage = $module === 'meet_event'
            ? (PortalRequest::MEET_STATUSES[$status] ?? 'Request Raised')
            : (\App\Support\RequestFlow::FLOWS[$module]['steps'][$status] ?? 'Request Raised');

        $prefix = PortalRequest::MODULE_CODES[$module] . '-' . now()->year . '-';
        $last = PortalRequest::where('request_no', 'like', $prefix . '%')->orderByDesc('request_no')->value('request_no');
        $number = $prefix . str_pad(($last ? (int) substr($last, strlen($prefix)) : 0) + 1, 4, '0', STR_PAD_LEFT);

        $request = new PortalRequest($fields + ['module' => $module, 'user_id' => $user->id, 'status' => $status, 'stage' => $stage, 'event_name' => self::MARK]);
        $request->request_no = $number;
        $request->submitted_at = now()->subDays(14);
        $request->save();

        return $request;
    }

    private function item(PortalRequest $request, string $kind, string $name, int $qty): void
    {
        $model = PortalRequest::ITEM_TYPES[$kind]['model'];
        if ($id = $model::where('name', $name)->value('id')) {
            $request->items()->create(['item_type' => $kind, 'item_id' => $id, 'quantity' => $qty]);
        }
    }

    private function history(PortalRequest $request, ?User $user, string $action, ?string $from, ?string $to, ?string $remarks, Carbon $at): void
    {
        (new RequestHistory)->forceFill([
            'request_id' => $request->id, 'user_id' => $user?->id, 'action' => $action, 'from_status' => $from,
            'to_status' => $to, 'remarks' => $remarks, 'created_at' => $at, 'updated_at' => $at,
        ])->save();
    }

    private function approval(PortalRequest $request, string $checkpoint, string $action, User $user, ?string $remarks, Carbon $at): void
    {
        (new Approval)->forceFill([
            'request_id' => $request->id, 'checkpoint' => $checkpoint, 'action' => $action, 'remarks' => $remarks,
            'user_id' => $user->id, 'created_at' => $at, 'updated_at' => $at,
        ])->save();
    }

    /**
     * Hand-typed test plans (venue Rs 44, vendor Rs 45, vendor "TT") become believable figures.
     * Only touches options under Rs 1,000, so real quotes are never changed.
     */
    private function fixTestAmounts(): void
    {
        $venues = [185000, 128000, 96000];
        $vendors = [['Shree Events & Decor', 'Stage, backdrop and floral decoration', 68000], ['Sai Audio Visuals', 'Sound, LED wall and lighting', 42000], ['Pixel Prints', 'Standees, banners and printing', 26500]];
        $proposalIds = MeetProposalOption::where('amount', '<', 1000)->whereNotNull('amount')->pluck('meet_proposal_id')->unique();

        foreach ($proposalIds as $proposalId) {
            $v = $w = 0;
            foreach (MeetProposalOption::where('meet_proposal_id', $proposalId)->orderBy('id')->get() as $option) {
                if ($option->amount >= 1000) {
                    continue;
                }
                if ($option->type === 'venue') {
                    $option->amount = $venues[$v++ % 3];
                    $option->capacity = $option->capacity && $option->capacity > 9 ? $option->capacity : 120;
                } else {
                    [$name, $details, $amount] = $vendors[$w++ % 3];
                    $option->name = strlen($option->name) <= 5 ? $name : $option->name;
                    $option->details = $option->details && strlen($option->details) > 5 ? $option->details : $details;
                    $option->amount = $amount;
                }
                $option->save();
            }
            $proposal = MeetProposal::find($proposalId);
            if ($proposal) {
                $options = $proposal->options()->get();
                $venue = $options->firstWhere('is_recommended', true) ?? $options->firstWhere('type', 'venue');
                $proposal->estimated_budget = ($venue?->amount ?? 0) + $options->where('type', 'vendor')->sum('amount');
                if (strlen((string) $proposal->recommended_plan) <= 5) {
                    $proposal->recommended_plan = 'Recommended venue fits the guest count and is central for most invitees. Total with decoration, AV and printing is within the planned budget.';
                }
                $proposal->save();
            }
        }
    }
}
