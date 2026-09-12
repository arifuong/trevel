<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Media Hero Profil Perusahaan (1 Foto Hero + 1 Video Profil)
        Gallery::updateOrCreate(
            ['title' => 'Gedung Kantor Pusat PT. Zein Internasional'],
            [
                'type'            => 'photo',
                'is_profile_hero' => true,
                'image_path'      => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format,webp&fit=crop&w=1200&q=80',
                'video_url'       => null,
                'caption'         => 'Kantor Pusat PT. Zein Tour & Travel Internasional di Parongpong, Bandung Barat.',
                'sort_order'      => 1,
                'is_active'       => true,
            ]
        );

        Gallery::updateOrCreate(
            ['title' => 'Profil Resmi PT. Zein Internasional'],
            [
                'type'            => 'video',
                'is_profile_hero' => true,
                'image_path'      => null,
                'video_url'       => 'https://youtu.be/QrYcpXEC0RU',
                'caption'         => 'Video pengenalan komitmen pelayanan bimbingan ibadah umrah dan haji PT. Zein Internasional.',
                'sort_order'      => 2,
                'is_active'       => true,
            ]
        );

        // 2. Dokumentasi Galeri Umum Lainnya
        $items = [
            [
                'type'            => 'photo',
                'is_profile_hero' => false,
                'title'           => 'Kekhusyukan Tawaf Jamaah di Depan Ka\'bah',
                'image_path'      => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'caption'         => 'Pelaksanaan thawaf umrah jamaah di Masjidil Haram dengan pembimbingan intensif.',
                'sort_order'      => 3,
            ],
            [
                'type'            => 'photo',
                'is_profile_hero' => false,
                'title'           => 'Keluarga Jamaah PT. Zein di Pelataran Masjid Nabawi',
                'image_path'      => 'https://images.unsplash.com/photo-1565552645632-d725f8bfc19a?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'caption'         => 'Momen kebersamaan dan kekeluargaan jamaah di pelataran payung Masjid Nabawi.',
                'sort_order'      => 4,
            ],
            [
                'type'            => 'photo',
                'is_profile_hero' => false,
                'title'           => 'Ziarah Sejarah di Jabal Uhud Madinah',
                'image_path'      => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'caption'         => 'Mengunjungi makam para syuhada Uhud dan bukit rumaat bersama muthawwif.',
                'sort_order'      => 5,
            ],
            [
                'type'            => 'photo',
                'is_profile_hero' => false,
                'title'           => 'Bimbingan Manasik Akbar Pra-Keberangkatan',
                'image_path'      => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'caption'         => 'Simulasi rukun dan wajib umrah secara terperinci sebelum bertolak ke tanah suci.',
                'sort_order'      => 6,
            ],
            [
                'type'            => 'photo',
                'is_profile_hero' => false,
                'title'           => 'Kenyamanan Fasilitas Hotel Bintang 5 View Ka\'bah',
                'image_path'      => 'https://images.unsplash.com/photo-1580835845971-a393b73bf370?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'caption'         => 'Akomodasi hotel premium jarak dekat untuk memudahkan ibadah shalat berjamaah.',
                'sort_order'      => 7,
            ],
            [
                'type'            => 'video',
                'is_profile_hero' => false,
                'title'           => 'Suasana Doa Bersama Jamaah di Raudhah',
                'image_path'      => null,
                'video_url'       => 'https://www.youtube.com/watch?v=QrYcpXEC0RU',
                'caption'         => 'Dokumentasi video saat jamaah berkesempatan ziarah dan shalat di Raudhah Syarifah.',
                'sort_order'      => 8,
            ],
        ];

        foreach ($items as $item) {
            Gallery::updateOrCreate(
                ['title' => $item['title']],
                [
                    'type'            => $item['type'],
                    'is_profile_hero' => $item['is_profile_hero'],
                    'image_path'      => $item['image_path'],
                    'video_url'       => $item['video_url'] ?? null,
                    'caption'         => $item['caption'],
                    'sort_order'      => $item['sort_order'],
                    'is_active'       => true,
                ]
            );
        }
    }
}
