<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Apartment;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $amenityNames = [
            'Wi‑Fi', 'Air conditioning', 'Kitchen', 'Private parking', 'Lake view',
            'Washing machine', 'Dishwasher', 'Balcony or terrace', 'Coffee machine', 'Smart TV',
        ];

        $amenities = [];
        foreach ($amenityNames as $i => $name) {
            $amenities[] = Amenity::query()->create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name).'-'.$i,
                'sort_order' => $i,
            ]);
        }

        $orchidea = Apartment::query()->create([
            'name' => 'Appartamento Orchidea Garda',
            'slug' => 'appartamento-orchidea-garda',
            'short_description' => 'Elegant one-bedroom apartment near the centre of Garda—perfect for couples seeking calm, style, and easy access to the lake.',
            'full_description' => '<p>Appartamento Orchidea is a refined retreat for two, with a bright living space, fully equipped kitchen, and thoughtful details throughout. Located in Garda on Lake Garda, you are minutes from the waterfront promenade, restaurants, and ferry connections.</p><p>Ideal for a romantic escape or a slow travel week exploring Verona, Bardolino, and the surrounding hills.</p>',
            'ideal_for' => 'Couples',
            'max_guests' => 2,
            'bedrooms' => 1,
            'bathrooms' => 1,
            'size_m2' => 48,
            'price_from' => 120.00,
            'address' => 'Via San Martino della Battaglia 12, 37016 Garda VR, Italy',
            'location_text' => "Quiet residential area in Garda, a short walk from the historic centre and lakefront. Easy access to bike paths and public transport along the lake.",
            'check_in_out_note' => 'Check-in from 15:00 to 20:00. Check-out by 10:00. Early/late arrivals on request.',
            'availability_note' => 'Minimum stay may apply in high season. Ask for your dates.',
            'is_active' => true,
            'sort_order' => 1,
            'external_listing_url' => 'https://www.booking.com/hotel/it/appartamento-orchidea-garda.html',
            'license_cir' => '023036-LOC-00059',
            'license_cin' => 'IT023036C2RGPU44NO',
            'meta_title' => 'Appartamento Orchidea Garda — Lake Garda apartment rental',
            'meta_description' => 'Romantic apartment in Garda for two guests. Direct booking inquiry. Near Lake Garda lakefront and old town.',
        ]);

        $lavanda = Apartment::query()->create([
            'name' => 'Appartamento Lavanda Garda',
            'slug' => 'appartamento-lavanda-garda',
            'short_description' => 'Spacious family-friendly apartment with generous living space, two bedrooms, and a relaxed atmosphere close to the lake.',
            'full_description' => '<p>Appartamento Lavanda welcomes families and small groups who want room to breathe after a day on the lake. The layout separates sleeping areas from the living zone, and the kitchen is set up for real stays—not just weekends.</p><p>From here you can reach beaches, playgrounds, boat trips, and day trips to Verona or the mountains in under an hour.</p>',
            'ideal_for' => 'Families and small groups',
            'max_guests' => 5,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'size_m2' => 72,
            'price_from' => 145.00,
            'address' => 'Via Capitanato 8, 37016 Garda VR, Italy',
            'location_text' => "Family-oriented neighbourhood in Garda with shops nearby and straightforward parking. The lake and promenade are within easy reach.",
            'check_in_out_note' => 'Check-in from 15:00. Check-out by 10:00.',
            'availability_note' => 'Popular in July and August—send dates early.',
            'is_active' => true,
            'sort_order' => 2,
            'external_listing_url' => 'https://www.booking.com/hotel/it/appartamento-lavanda-garda.html',
            'license_cir' => '023036-LOC-00059',
            'license_cin' => 'IT023036C2RGPU44NO',
            'meta_title' => 'Appartamento Lavanda Garda — family apartment Lake Garda',
            'meta_description' => 'Spacious two-bedroom holiday apartment in Garda for families. Direct inquiries. Near Lake Garda beaches and attractions.',
        ]);

        $orchidea->amenities()->attach(collect($amenities)->pluck('id')->take(7));
        $lavanda->amenities()->attach(collect($amenities)->pluck('id')->take(9));

        foreach ([
            [$orchidea, 'High season', '2026-06-01', '2026-09-15', 165.00],
            [$orchidea, 'Mid season', '2026-04-01', '2026-05-31', 125.00],
            [$lavanda, 'High season', '2026-06-01', '2026-09-15', 195.00],
            [$lavanda, 'Mid season', '2026-04-01', '2026-05-31', 150.00],
        ] as $i => $row) {
            [$apt, $label, $start, $end, $price] = $row;
            $apt->seasons()->create([
                'label' => $label,
                'start_date' => $start,
                'end_date' => $end,
                'price_per_night' => $price,
                'sort_order' => $i,
            ]);
        }

        Page::query()->updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'meta_title' => 'Lake Garda apartments in Garda — holiday rentals',
                'meta_description' => 'Premium holiday apartments in Garda on Lake Garda. Direct booking inquiries, fast replies, and local hosting. Ideal for couples and families.',
                'blocks' => [
                    'why_points' => [
                        ['title' => 'Direct hosting', 'text' => 'You speak with us before you arrive—we help with arrival, tips, and local recommendations.'],
                        ['title' => 'Prime location', 'text' => 'Explore the old town, beaches, boat trips, and wine country within minutes.'],
                        ['title' => 'Thoughtful spaces', 'text' => 'Fully equipped kitchens, reliable Wi‑Fi, and spaces that feel like a real home.'],
                        ['title' => 'Transparent pricing', 'text' => 'We confirm availability and rates for your dates before you commit.'],
                    ],
                    'cta_title' => 'Tell us your travel dates',
                    'cta_text' => 'We usually reply the same day with availability and next steps.',
                    'flex_blocks' => [
                        [
                            'type' => 'rich_text',
                            'html' => '<p>Stay in <strong>Garda</strong>, one of the most charming towns on <strong>Lake Garda</strong>, where mornings begin with espresso and evenings end with a stroll along the water. Our apartments are designed for travellers who value clarity, calm, and direct contact with the host—no anonymous booking lines, no surprises.</p>',
                        ],
                        [
                            'type' => 'rich_text',
                            'heading' => 'Lake life, your way',
                            'html' => '<p>Whether you want slow mornings on the terrace, family beach days, or sunset aperitivi in the <em>centro storico</em>, Garda is the perfect base. Sail to other lake towns, hike in the hills, or take a day trip to <strong>Verona</strong>—all within easy reach.</p><p><a href="/lake-garda">Discover Lake Garda →</a></p>',
                        ],
                        [
                            'type' => 'split_media',
                            'layout' => 'image_left',
                            'image_path' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80',
                            'image_alt' => 'Lake Garda waterfront',
                            'heading' => __('A calm base on the lake'),
                            'body_html' => '<p>'.__('Add more flexible sections—image + text, full-width photos, or extra rich text—without editing JSON.').'</p>',
                        ],
                    ],
                ],
            ]
        );

        Page::query()->updateOrCreate(
            ['slug' => 'lake-garda'],
            [
                'title' => 'Lake Garda',
                'meta_title' => 'Lake Garda & Garda — beaches, old town, boat trips',
                'meta_description' => 'Why stay in Garda on Lake Garda: beaches, restaurants, hiking, family activities, and day trips. Plan your holiday apartment stay.',
                'blocks' => [
                    'hero_title' => 'Lake Garda from Garda',
                    'hero_subtitle' => 'Italy’s largest lake—olive groves, clear water, medieval towns, and a pace that feels like a real holiday.',
                    'flex_blocks' => [
                        [
                            'type' => 'rich_text',
                            'html' => '<p><strong>Garda</strong> sits on the eastern shore of <strong>Lake Garda</strong>, where the lake is wide and the light is soft. It is compact enough to walk everywhere, yet connected by ferry to iconic destinations like Bardolino, Lazise, and Malcesine.</p>',
                        ],
                        [
                            'type' => 'rich_text',
                            'html' => '<h2>Why stay in Garda</h2><p>Compared to busier hubs, Garda balances calm evenings with easy access to beaches, cycling, and gastronomy. You are close to <strong>Verona</strong> for opera and Roman history, and to the hills for wine and olive oil.</p>',
                        ],
                        [
                            'type' => 'rich_text',
                            'html' => '<h2>Beaches, old town & activities</h2><ul><li><strong>Beaches & lidos</strong> — grassy shores, pebble bays, and family-friendly swimming.</li><li><strong>Old town</strong> — narrow streets, boutiques, gelato, and lakefront dining.</li><li><strong>Boat trips</strong> — ferries and private tours to explore the lake.</li><li><strong>Hiking & cycling</strong> — panoramic paths above the lake.</li><li><strong>Family fun</strong> — playgrounds, mini-golf, Gardaland nearby.</li></ul>',
                        ],
                        [
                            'type' => 'rich_text',
                            'html' => '<h2>Ideal for couples</h2><p>Romantic walks at sunset, wine tastings, and quiet terraces overlooking the water.</p><h2>Ideal for families</h2><p>Shallow swimming areas, easy day trips, and apartments with space to unwind after busy days.</p>',
                        ],
                    ],
                ],
            ]
        );

        Page::query()->updateOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact',
                'meta_title' => 'Contact — booking requests Lake Garda',
                'meta_description' => 'Call, WhatsApp, or send a booking request for our apartments in Garda on Lake Garda. Fast replies.',
                'blocks' => [
                    'hero_title' => 'Contact & booking requests',
                    'hero_subtitle' => 'We confirm availability personally—tell us your preferred apartment and dates.',
                    'reassurance' => 'We typically reply within a few hours during the day (CET). For urgent matters, call us.',
                ],
            ]
        );

        Page::query()->updateOrCreate(
            ['slug' => 'apartments'],
            [
                'title' => 'Apartments listing',
                'meta_title' => 'Lake Garda apartments in Garda',
                'meta_description' => 'Compare our two holiday apartments in Garda on Lake Garda. Direct booking inquiries, clear pricing, and fast host responses.',
                'canonical_url' => url('/apartments'),
                'blocks' => [
                    'hero_title' => 'Our apartments',
                    'hero_subtitle' => 'Two thoughtfully prepared rentals in Garda—ideal for couples and families exploring Lake Garda.',
                    'flex_blocks' => [
                        [
                            'type' => 'rich_text',
                            'html' => '<p>Compare our two holiday rentals below—each has its own character, seasonal pricing, and direct booking through this site.</p>',
                        ],
                    ],
                ],
            ]
        );

        $faqs = [
            ['page_slug' => 'home', 'question' => 'How do I book?', 'answer' => 'Send a request with your dates. We confirm availability and price, then guide you through confirmation and arrival details.', 'sort_order' => 1],
            ['page_slug' => 'home', 'question' => 'Is instant booking available?', 'answer' => 'We handle inquiries personally to ensure the right fit and fair rates for each season.', 'sort_order' => 2],
            ['page_slug' => 'lake-garda', 'question' => 'How far is Verona?', 'answer' => 'Verona is about 30–40 minutes by car depending on traffic—ideal for a day trip.', 'sort_order' => 1],
            ['page_slug' => 'lake-garda', 'question' => 'Are beaches walkable from Garda?', 'answer' => 'Yes, several swimming areas and lidos are within walking distance or a short drive.', 'sort_order' => 2],
            ['page_slug' => 'contact', 'question' => 'Do you charge a service fee?', 'answer' => 'No hidden platform fees on direct bookings—rates are discussed clearly in our reply.', 'sort_order' => 1],
        ];

        foreach ($faqs as $f) {
            Faq::query()->create([...$f, 'is_active' => true]);
        }

        Testimonial::query()->insert([
            ['author_name' => 'Sophie M.', 'author_location' => 'United Kingdom', 'quote' => 'Immaculate apartment and quick responses. Garda was the perfect base for exploring the lake.', 'rating' => 5, 'sort_order' => 1, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['author_name' => 'Thomas & Julia', 'author_location' => 'Germany', 'quote' => 'We loved the calm neighbourhood and still being close to restaurants. Would book again.', 'rating' => 5, 'sort_order' => 2, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['author_name' => 'Elena R.', 'author_location' => 'Italy', 'quote' => 'Family-friendly and well equipped. Host communication was excellent.', 'rating' => 5, 'sort_order' => 3, 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
