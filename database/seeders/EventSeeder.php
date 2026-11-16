<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventType;

/**
 * The 6 static portal sections (from the VIVA workflow documents) and their types.
 * Safe to run again: it adds what is missing and never deletes or renames anything.
 */
class EventSeeder extends Seeder
{
    private $events = [
        Event::MEET_EVENT => [
            'name' => 'Meet Events',
            'description' => 'Detailed planning and execution of structured meets involving fabricators, architects and related stakeholders.',
            'types' => [
                'Fabricator Meet',
                'Architect Meet',
                'In-Shop Meet',
                // From VIVA_BRANDING_PORTAL.jpeg ("Later: Dealer Meet" in the development plan).
                'Dealer Meet',
                'Others',
            ],
        ],
        Event::EXHIBITION => [
            'name' => 'Events / Exhibitions / Sponsorship',
            'description' => 'To manage participation in major exhibitions, small exhibition spaces and sponsorship opportunities.',
            'types' => [
                'Major Exhibition',
                'Small Exhibition',
                'Event Sponsorship',
            ],
        ],
        Event::PRESENTATION => [
            'name' => 'Presentations & Small Events',
            'description' => 'To provide Branding and Logistics support for presentations or meetings at Government, Architect or Client offices.',
            'types' => [
                'Government Office Presentation',
                'Architect Office Presentation',
                'Client Office Presentation',
                'Product Presentation',
                'Small Site / On-site Meeting',
                // From VIVA_BRANDING_PORTAL.jpeg.
                'Architect Visit',
            ],
        ],
        Event::GIFT_POP => [
            'name' => 'Gift & POP Requirement',
            'description' => 'A dedicated module for gifting and POP/material requests without creating a full event request.',
            'types' => [
                'Architect Gifts',
                'Fabricator Gifts',
                'Government Department Gifts',
                'Client Gifts',
                'Dealer / Channel Partner Gifts',
                'Seasonal Gifts',
                'Event / Exhibition Gifts',
                'POP Material',
                'Samples & Catalogues',
                'Other Promotional Material',
            ],
        ],
        Event::BRANDING => [
            'name' => 'Branding Support & Trade Marketing',
            'description' => 'To manage ongoing retail, display, product sample, outdoor and advertising support requirements.',
            'types' => [
                'POP Requirement',
                'Retail Branding Support',
                'Small Displays',
                'Big Displays',
                'Product Samples',
                'Outdoor Branding (OOH)',
                'Newspaper Advertisement',
                'Magazine Advertisement',
                'Other Trade Marketing',
            ],
        ],
        Event::EXPERIENCE_CENTRE => [
            'name' => 'New Experience Centre',
            'description' => 'To manage the end-to-end request, planning, design, development, setup and launch of a new Viva Experience Centre.',
            'types' => [
                'New City / Location',
                'Dealer / Partner Location',
                // Off until Viva turns it on (documents: "if enabled later").
                ['Relocation / Major Revamp', 'INACTIVE'],
            ],
        ],
    ];

    public function run()
    {
        foreach ($this->events as $code => $data) {
            $event = Event::firstOrCreate(
                ['code' => $code],
                ['name' => $data['name'], 'description' => $data['description'], 'status' => 'ACTIVE']
            );
            // Fill the purpose text on events created before the description column existed.
            if (! $event->description) {
                $event->update(['description' => $data['description']]);
            }

            foreach ($data['types'] as $type) {
                [$name, $status] = is_array($type) ? $type : [$type, 'ACTIVE'];
                EventType::firstOrCreate(
                    ['event_id' => $event->id, 'name' => $name],
                    ['status' => $status]
                );
            }
        }
    }
}
