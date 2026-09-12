<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airline;
use App\Models\Hotel;
use App\Models\HotelFacility;

class MasterTravelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Airlines
        if (Airline::count() === 0) {
            $airlines = [
                ['name' => 'Saudia Airlines', 'code' => 'SV', 'status' => 'aktif', 'sort_order' => 1],
                ['name' => 'Garuda Indonesia', 'code' => 'GA', 'status' => 'aktif', 'sort_order' => 2],
                ['name' => 'Qatar Airways', 'code' => 'QR', 'status' => 'aktif', 'sort_order' => 3],
                ['name' => 'Emirates', 'code' => 'EK', 'status' => 'aktif', 'sort_order' => 4],
                ['name' => 'Oman Air', 'code' => 'WY', 'status' => 'aktif', 'sort_order' => 5],
                ['name' => 'Turkish Airlines', 'code' => 'TK', 'status' => 'aktif', 'sort_order' => 6],
                ['name' => 'Etihad Airways', 'code' => 'EY', 'status' => 'aktif', 'sort_order' => 7],
                ['name' => 'Lion Air (Charter Umrah)', 'code' => 'JT', 'status' => 'aktif', 'sort_order' => 8],
            ];

            foreach ($airlines as $a) {
                Airline::create($a);
            }
        }

        // 2. Seed Hotels
        if (Hotel::count() === 0) {
            $hotels = [
                [
                    'name' => 'Pullman Zamzam Makkah',
                    'city' => 'makkah',
                    'star_rating' => '5',
                    'distance_to_haram' => '50',
                    'address' => 'Abraj Al Bait Complex, King Abdul Aziz Endowment, Makkah',
                    'description' => 'Hotel bintang 5 premium yang terhubung langsung dengan pelataran Masjidil Haram dan pusat perbelanjaan Abraj Al Bait.',
                    'status' => 'aktif',
                ],
                [
                    'name' => 'Swissôtel Al Maqam Makkah',
                    'city' => 'makkah',
                    'star_rating' => '5',
                    'distance_to_haram' => '100',
                    'address' => 'King Abdul Aziz Endowment, Ibrahim Al Khalil St, Makkah',
                    'description' => 'Hotel mewah dengan pemandangan langsung ke Ka\'bah dan akses mudah ke Masjidil Haram.',
                    'status' => 'aktif',
                ],
                [
                    'name' => 'Hilton Convention Hotel Makkah',
                    'city' => 'makkah',
                    'star_rating' => '5',
                    'distance_to_haram' => '300',
                    'address' => 'Jabal Omar, Ibrahim Al Khalil, Makkah',
                    'description' => 'Hotel modern bintang 5 berlokasi di kawasan Jabal Omar dengan fasilitas lengkap dan ruang sholat.',
                    'status' => 'aktif',
                ],
                [
                    'name' => 'Anjum Hotel Makkah',
                    'city' => 'makkah',
                    'star_rating' => '5',
                    'distance_to_haram' => '250',
                    'address' => 'Umm Al Qura Street, Harat Al Bab, Makkah',
                    'description' => 'Hotel megah dengan arsitektur khas Hijazi modern dekat gerbang perluasan Raja Abdullah.',
                    'status' => 'aktif',
                ],
                [
                    'name' => 'Dar Al Taqwa Hotel Madinah',
                    'city' => 'madinah',
                    'star_rating' => '5',
                    'distance_to_haram' => '20',
                    'address' => 'Off Al Sitteen Street, Central Area, Madinah',
                    'description' => 'Hotel bintang 5 tepat di depan pelataran gerbang utama Masjid Nabawi (pintu wanita & Raudhah).',
                    'status' => 'aktif',
                ],
                [
                    'name' => 'Pullman Zamzam Madinah',
                    'city' => 'madinah',
                    'star_rating' => '5',
                    'distance_to_haram' => '150',
                    'address' => 'Amr Bin Al Ghamas Street, Central Area, Madinah',
                    'description' => 'Hotel bintang 5 berkelas internasional di kawasan pusat Madinah dekat pelataran barat Masjid Nabawi.',
                    'status' => 'aktif',
                ],
                [
                    'name' => 'Grand Plaza Madinah',
                    'city' => 'madinah',
                    'star_rating' => '4',
                    'distance_to_haram' => '200',
                    'address' => 'Bada\'ah, Central Area, Madinah',
                    'description' => 'Hotel bintang 4 favorit jamaah Indonesia dengan lokasi strategis dan akses cepat ke Masjid Nabawi.',
                    'status' => 'aktif',
                ],
            ];

            $facilityIds = HotelFacility::pluck('id')->toArray();

            foreach ($hotels as $h) {
                $hotel = Hotel::create($h);
                if (!empty($facilityIds)) {
                    // attach some random 4-6 facilities
                    $randomFacs = array_slice($facilityIds, 0, rand(4, 7));
                    $hotel->facilities()->sync($randomFacs);
                }
            }
        }
    }
}
