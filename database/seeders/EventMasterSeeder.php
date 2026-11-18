<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Location;
use App\Models\Zone;
use Illuminate\Database\Seeder;

/**
 * Master data taken from the past events on vivaacp.com/eventgallery
 * (Fab Meets, Architect Meets, Lunch & Learn, domestic and international events).
 * Safe to run again: it only adds what is missing and fills state, country and zone on locations.
 * Run: php artisan db:seed --class=EventMasterSeeder
 */
class EventMasterSeeder extends Seeder
{
    /** Event types that were missing: [section name, type name]. */
    private const EVENT_TYPES = [
        ['Presentations & Small Events', 'Lunch & Learn Session'],
        ['Events / Exhibitions / Sponsorship', 'International Exhibition'],
        ['Events / Exhibitions / Sponsorship', 'Conference / Summit'],
    ];

    /** City => [state, country, zone]. Spellings corrected from the website. Zone is only for India. */
    private const LOCATIONS = [
        // West
        'Surat' => ['Gujarat', 'India', 'West'], 'Ahmedabad' => ['Gujarat', 'India', 'West'],
        'Vadodara' => ['Gujarat', 'India', 'West'], 'Rajkot' => ['Gujarat', 'India', 'West'],
        'Kutch' => ['Gujarat', 'India', 'West'], 'Gandhinagar' => ['Gujarat', 'India', 'West'],
        'Mumbai' => ['Maharashtra', 'India', 'West'], 'Thane' => ['Maharashtra', 'India', 'West'],
        'Pune' => ['Maharashtra', 'India', 'West'], 'Nashik' => ['Maharashtra', 'India', 'West'],
        // Central
        'Nagpur' => ['Maharashtra', 'India', 'Central'], 'Chandrapur' => ['Maharashtra', 'India', 'Central'],
        'Indore' => ['Madhya Pradesh', 'India', 'Central'], 'Ratlam' => ['Madhya Pradesh', 'India', 'Central'],
        'Jabalpur' => ['Madhya Pradesh', 'India', 'Central'], 'Ambikapur' => ['Chhattisgarh', 'India', 'Central'],
        // South
        'Bengaluru' => ['Karnataka', 'India', 'South'], 'Bijapur' => ['Karnataka', 'India', 'South'],
        'Davanagere' => ['Karnataka', 'India', 'South'], 'Hyderabad' => ['Telangana', 'India', 'South'],
        'Chennai' => ['Tamil Nadu', 'India', 'South'], 'Coimbatore' => ['Tamil Nadu', 'India', 'South'],
        'Tiruchirappalli' => ['Tamil Nadu', 'India', 'South'], 'Kasaragod' => ['Kerala', 'India', 'South'],
        'Kannur' => ['Kerala', 'India', 'South'], 'Kozhikode' => ['Kerala', 'India', 'South'],
        'Thiruvananthapuram' => ['Kerala', 'India', 'South'],
        // East
        'Kolkata' => ['West Bengal', 'India', 'East'],
        // North
        'Delhi' => ['Delhi', 'India', 'North'], 'Gurugram' => ['Haryana', 'India', 'North'],
        'Hisar' => ['Haryana', 'India', 'North'], 'Kaithal' => ['Haryana', 'India', 'North'],
        'Ludhiana' => ['Punjab', 'India', 'North'], 'Chandigarh' => ['Chandigarh', 'India', 'North'],
        'Lucknow' => ['Uttar Pradesh', 'India', 'North'], 'Prayagraj' => ['Uttar Pradesh', 'India', 'North'],
        'Gorakhpur' => ['Uttar Pradesh', 'India', 'North'], 'Udaipur' => ['Rajasthan', 'India', 'North'],
        'Jaipur' => ['Rajasthan', 'India', 'North'],
        // International (no zone)
        'Dubai' => ['Dubai', 'United Arab Emirates', null], 'Doha' => ['Doha', 'Qatar', null],
        'Muscat' => ['Muscat', 'Oman', null], 'Johannesburg' => ['Gauteng', 'South Africa', null],
    ];

    public function run(): void
    {
        foreach (self::EVENT_TYPES as [$section, $type]) {
            $event = Event::where('name', $section)->first();
            if (! $event) {
                $this->command?->warn("Section '{$section}' not found, skipped '{$type}'");
                continue;
            }
            EventType::firstOrCreate(['event_id' => $event->id, 'name' => $type], ['status' => 'ACTIVE']);
        }

        $zones = Zone::pluck('id', 'name');
        foreach (self::LOCATIONS as $city => [$state, $country, $zone]) {
            Location::updateOrCreate(
                ['name' => $city],
                ['state' => $state, 'country' => $country, 'zone_id' => $zone ? ($zones[$zone] ?? null) : null, 'status' => 'ACTIVE']
            );
        }

        $this->command?->info(count(self::LOCATIONS) . ' locations and ' . count(self::EVENT_TYPES) . ' event types checked.');
    }
}
