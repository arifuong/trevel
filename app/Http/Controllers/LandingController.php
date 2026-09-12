<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Data sentral / mock data untuk PT. Zein Internasional
     * Sangat mudah diedit sebelum migrasi ke Database di tahap selanjutnya.
     */
    private function getCompanyData()
    {
        // Ambil video profil utama dari Galeri (is_profile_hero = true)
        $profileVideo = \App\Models\Gallery::where('is_profile_hero', true)
            ->where('type', 'video')
            ->where('is_active', true)
            ->latest()
            ->first();

        $videoUrl = $profileVideo?->video_url ?? 'https://youtu.be/QrYcpXEC0RU';
        $videoTitle = $profileVideo?->title ?? 'Profil PT. Zein Internasional';
        $youtubeVideoId = $profileVideo?->youtube_id ?? \App\Helpers\YouTubeHelper::extractVideoId($videoUrl);
        $youtubeEmbedUrl = $profileVideo?->embed_url ?? \App\Helpers\YouTubeHelper::getEmbedUrl($videoUrl);

        // Ambil foto hero profil utama dari Galeri (is_profile_hero = true)
        $profileHero = \App\Models\Gallery::where('is_profile_hero', true)
            ->where('type', 'photo')
            ->where('is_active', true)
            ->latest()
            ->first();

        $heroImageUrl = $profileHero?->thumbnail_url ?? 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format,webp&fit=crop&w=800&q=75';

        $aboutSummary = 'PT. ZEIN INTERNASIONAL (Zeintour) adalah salah satu perusahaan penyelenggara perjalanan Ibadah Umrah yang didirikan pada 19 Oktober 2012 di Bandung oleh H. Zenal Abidin, dengan motivasi membangun dua kebaikan (kebaikan dunia dan akhirat).';
        $aboutDescription = 'Berdasarkan motivasi tersebut, Zeintour bertekad untuk menjadi pelayan para tamu Allah SWT, memberikan kemudahan atas segala hal yang berkaitan dengan proses pelaksanaan ibadahnya, mulai dari persiapan keberangkatan, pelaksanaan ibadah di Tanah Suci, sampai kembali ke tanah air dengan ikhlas dan tawakal kepadaNya, agar semua rangkaian ibadahnya diterima Allah SWT.';
        $ppiu = 'IZIN KEMENAG RI NOMOR U.255 TAHUN 2020';
        $pihk = 'IZIN KEMENAG RI NOMOR 599 TAHUN 2021';
        $visi = "Menjadi penyelenggara Haji dan Umrah dengan pelayanan terbaik berbasis Al Qur'an di Indonesia.";
        $misi = [
            'Membantu para calon Jemaah Umrah/Haji dalam pelaksanaan ibadahnya agar benar dan sempurna untuk mencapai ibadah yang mabrur.',
            'Mengembangkan perusahaan penyelenggara perjalanan ibadah Umrah/Haji yang baik, serta menjadi pembimbing ibadah yang siqah dengan pelayanan prima.',
            "Mengembangkan Ukhuwah Islamiyah, Silaturrahim dan kerja sama untuk mencapai kehidupan yang rahmatan lil 'alamin.",
        ];
        $tujuan = [
            'Mengelola usaha penyelenggara perjalanan ibadah yang berdimensi dua kebaikan.',
            'Menjadi salah satu sumber pendapatan yang barokah.',
            'Menjadi pintu masuk untuk mengembangkan berbagai usaha lain yang berkaitan.',
        ];
        $keunggulan = [
            ['icon' => 'currency', 'title' => 'Harga Paket Terjangkau', 'desc' => 'Harga paket relatif lebih murah dengan pelayanan terbaik.'],
            ['icon' => 'sliders', 'title' => 'Pilihan Paket Fleksibel', 'desc' => 'Disediakan pilihan paket sesuai dengan kemampuan/kebutuhan jamaah.'],
            ['icon' => 'kaaba', 'title' => 'Fasilitas Umrah Sunnah', 'desc' => 'Memfasilitasi jamaah untuk melakukan Umrah sunnah.'],
            ['icon' => 'bolt', 'title' => 'Fast Track Imigrasi', 'desc' => 'Layanan cepat imigrasi di bandara Soekarno Hatta.'],
            ['icon' => 'lounge', 'title' => 'Lounge Bandara', 'desc' => 'Di bandara disediakan lounge umrah.'],
            ['icon' => 'guide', 'title' => 'Pembimbing Profesional', 'desc' => 'Pembimbing yang profesional di bidangnya.'],
        ];
        $stats = [
            ['number' => '12000', 'suffix' => '+', 'label' => 'Jamaah Diberangkatkan'],
            ['number' => '99', 'suffix' => '%', 'label' => 'Tingkat Kepuasan'],
            ['number' => '14', 'suffix' => '+', 'label' => 'Tahun Pengalaman'],
            ['number' => '100', 'suffix' => '%', 'label' => 'Izin Resmi Kemenag'],
        ];

        return [
            'name' => 'PT. ZEIN INTERNASIONAL',
            'about_summary' => $aboutSummary,
            'about_description' => $aboutDescription,
            'video_url' => $videoUrl,
            'video_title' => $videoTitle,
            'youtube_video_id' => $youtubeVideoId,
            'youtube_embed_url' => $youtubeEmbedUrl,
            'hero_image_url' => $heroImageUrl,
            'brand' => 'ZEIN TOUR',
            'alias' => 'ZEIN TOUR',
            'leader' => 'H. ZENAL ABIDIN, M.Si',
            'founder' => 'H. ZENAL ABIDIN, M.Si',
            'notary' => 'IIN ABDUL JALIL, S.H., Sp.N',
            'npwp' => '70.507.160.3-421.000',
            'establishment_basis' => 'AKTA NOTARIS NO. 12, TANGGAL 17 JUNI 2013',
            'menkumham' => 'AHU-49882.AH.01.01. TAHUN 2013',
            'nib' => '9120005242349',
            'siup' => '00116/10-17/PK/II/2014',
            'tdp' => '103114600910',
            'tdup' => '56.3/001/JPW/PAR/2014',
            'akreditasi' => 'IMS-SPPIU-007/03 AGUSTUS 2020/A',
            'ppiu' => $ppiu,
            'ppiu_number' => $ppiu,
            'pihk' => $pihk,
            'pihk_number' => $pihk,
            'founded_date' => '19 Oktober 2012 di Bandung',
            'founded_year' => '2012',
            'motivation' => 'Membangun dua kebaikan (kebaikan dunia dan akhirat)',
            'phone' => '+62821 2148 3337',
            'phone_call' => '+6282121483337',
            'whatsapp' => '6281222222562',
            'whatsapp_formatted' => '+62812 2222 2562',
            'address' => 'Jl. Cihanjuang Kp. Karangsari No.15, Parongpong Bandung Barat 40559',
            'address_line1' => 'Jl. Cihanjuang Kp. Karangsari No.15',
            'address_line2' => 'Parongpong Bandung Barat 40559',
            'maps_url' => 'https://maps.app.goo.gl/mbTWxdHMtDLWKu9k7',
            'office_hours' => 'Senin - Sabtu: 08.30 - 17.00 WIB',
            'social_media' => [
                'instagram' => 'https://www.instagram.com/zeintour_official/',
                'facebook' => 'https://id-id.facebook.com/PTZeinInternasional/',
                'youtube' => 'https://www.youtube.com/@ZEINTV7',
                'tiktok' => 'https://tiktok.com/@zeintour',
            ],
            'stats' => $stats,
            'visi' => $visi,
            'misi' => $misi,
            'tujuan' => $tujuan,
            'keunggulan' => $keunggulan,
        ];
    }

    private function getLegalityData()
    {
        return [
            [
                'title' => 'Izin Operasional PPIU',
                'number' => 'PPIU NOMOR U.255 TAHUN 2020',
                'description' => 'Izin resmi Penyelenggara Perjalanan Ibadah Umrah (PPIU) dari Kementerian Agama Republik Indonesia.',
                'badge' => 'Kemenag RI',
            ],
            [
                'title' => 'Izin Operasional PIHK',
                'number' => 'PIHK NOMOR 599 TAHUN 2021',
                'description' => 'Izin resmi Penyelenggara Ibadah Haji Khusus (PIHK) dari Kementerian Agama Republik Indonesia.',
                'badge' => 'Kemenag RI',
            ],
            [
                'title' => 'Sertifikat Akreditasi',
                'number' => 'IMS-SPPIU-007/03 AGUSTUS 2020/A',
                'description' => 'Sertifikat Akreditasi mutu pelayanan dan manajemen operasional biro perjalanan ibadah Umrah.',
                'badge' => 'Akreditasi A',
            ],
            [
                'title' => 'Nomor Induk Berusaha (NIB)',
                'number' => '9120005242349',
                'description' => 'Nomor Induk Berusaha resmi Badan Usaha PT. ZEIN INTERNASIONAL terdaftar di OSS Republik Indonesia.',
                'badge' => 'Legalitas NIB',
            ],
            [
                'title' => 'Pengesahan Menkumham',
                'number' => 'AHU-49882.AH.01.01. TAHUN 2013',
                'description' => 'Surat Keputusan Pengesahan Badan Hukum Perseroan Terbatas dari Kementerian Hukum dan HAM RI.',
                'badge' => 'Kemenkumham RI',
            ],
            [
                'title' => 'Akta Notaris Pendirian',
                'number' => 'NO. 12, TANGGAL 17 JUNI 2013',
                'description' => 'Akta Notaris pendirian perusahaan oleh Notaris IIN ABDUL JALIL, S.H., Sp.N.',
                'badge' => 'Notaris Resmi',
            ],
            [
                'title' => 'Nomor Pokok Wajib Pajak',
                'number' => '70.507.160.3-421.000',
                'description' => 'Wajib Pajak Badan resmi atas nama PT. ZEIN INTERNASIONAL (Merek Usaha: ZEIN TOUR).',
                'badge' => 'NPWP Badan',
            ],
            [
                'title' => 'Izin Usaha Pariwisata (TDUP)',
                'number' => '56.3/001/JPW/PAR/2014',
                'description' => 'SIUP: 00116/10-17/PK/II/2014 · TDP: 103114600910 · Pimpinan: H. ZENAL ABIDIN, M.Si.',
                'badge' => 'Izin Usaha',
            ],
        ];
    }

    private function getPackagesData()
    {
        $dbPackages = \App\Models\Package::where('status', 'aktif')->with([
            'includes' => fn($qi) => $qi->orderBy('sort_order'),
            'excludes' => fn($qe) => $qe->orderBy('sort_order'),
            'variants' => function ($q) {
                $q->where('status', 'aktif')->orderBy('sort_order')->with([
                    'prices' => fn($qp) => $qp->orderBy('sort_order'),
                    'airlines',
                    'hotelMakkah.photos',
                    'hotelMakkah.facilities',
                    'hotelMadinah.photos',
                    'hotelMadinah.facilities',
                    'overrideIncludes' => fn($qi) => $qi->orderBy('sort_order'),
                    'overrideExcludes' => fn($qe) => $qe->orderBy('sort_order'),
                ]);
            }
        ])->get();

        if ($dbPackages->isEmpty()) {
            return [];
        }

        return $dbPackages->map(function ($pkg) {
            $resolvedType = $pkg->package_type ?? (str_contains(strtolower($pkg->name), 'haji') ? 'haji' : 'umrah');
            $isHaji = $resolvedType === 'haji';

            // Map variants (only active variants)
            $variants = $pkg->variants->where('status', 'aktif')->map(function ($var) {
                $activePrices = $var->prices->where('is_active', true);
                $lowestPrice = $activePrices->min(fn($p) => $p->promo_price ?? $p->normal_price) ?? 0;
                
                $hotelMakkah = $var->hotelMakkah;
                $hotelMadinah = $var->hotelMadinah;
                $airlines = $var->airlines;
                $airlineNames = $var->airlines_display;

                $makkahPhotos = $hotelMakkah ? $hotelMakkah->photos->map(fn($hp) => [
                    'url' => \App\Helpers\ImageHelper::url($hp->photo_path),
                    'category' => $hp->category_label ?? $hp->category,
                    'caption' => $hp->caption,
                ])->values()->toArray() : [];

                $madinahPhotos = $hotelMadinah ? $hotelMadinah->photos->map(fn($hp) => [
                    'url' => \App\Helpers\ImageHelper::url($hp->photo_path),
                    'category' => $hp->category_label ?? $hp->category,
                    'caption' => $hp->caption,
                ])->values()->toArray() : [];

                $makkahFacilities = $hotelMakkah ? $hotelMakkah->facilities->pluck('name')->values()->toArray() : [];
                $madinahFacilities = $hotelMadinah ? $hotelMadinah->facilities->pluck('name')->values()->toArray() : [];

                $mergedIncludes = $var->merged_includes->pluck('item')->values()->toArray();
                $mergedExcludes = $var->merged_excludes->pluck('item')->values()->toArray();

                return [
                    'id' => $var->id,
                    'name' => $var->name,
                    'slug' => $var->slug,
                    'description' => $var->description,
                    'quota' => $var->remaining_quota,
                    'total_quota' => $var->quota,
                    'remaining_quota' => $var->remaining_quota,
                    'status' => $var->status,
                    'status_label' => $var->status_label,
                    'is_sold_out' => $var->is_sold_out,
                    'lowest_price' => (float) $lowestPrice,
                    'lowest_price_formatted' => $lowestPrice ? 'Rp ' . number_format((float) $lowestPrice, 0, ',', '.') : '-',
                    'main_photo' => \App\Helpers\ImageHelper::url($var->main_photo),
                    'airline' => $airlineNames,
                    'airline_departure' => $airlineNames,
                    'airline_departure_logo' => \App\Helpers\ImageHelper::url($airlines->first()?->logo),
                    'airline_return' => $airlineNames,
                    'airline_return_logo' => \App\Helpers\ImageHelper::url(($airlines->count() > 1 && $airlines->last()->logo) ? $airlines->last()->logo : $airlines->first()?->logo),
                    'airlines' => $airlines->map(fn($a) => [
                        'id' => $a->id,
                        'name' => $a->name,
                        'code' => $a->code,
                        'logo' => \App\Helpers\ImageHelper::url($a->logo),
                    ])->values()->toArray(),
                    'hotel_makkah_name' => $hotelMakkah?->name,
                    'hotel_makkah_star' => $hotelMakkah?->star_rating,
                    'hotel_makkah_distance' => $hotelMakkah?->distance_to_haram,
                    'hotel_makkah_description' => $hotelMakkah?->description,
                    'hotel_makkah_main_photo' => \App\Helpers\ImageHelper::url($hotelMakkah?->main_photo),
                    'hotel_makkah_photos' => $makkahPhotos,
                    'hotel_madinah_name' => $hotelMadinah?->name,
                    'hotel_madinah_star' => $hotelMadinah?->star_rating,
                    'hotel_madinah_distance' => $hotelMadinah?->distance_to_haram,
                    'hotel_madinah_description' => $hotelMadinah?->description,
                    'hotel_madinah_main_photo' => \App\Helpers\ImageHelper::url($hotelMadinah?->main_photo),
                    'hotel_madinah_photos' => $madinahPhotos,
                    'makkah_facilities' => $makkahFacilities,
                    'madinah_facilities' => $madinahFacilities,
                    'prices' => $var->prices->where('is_active', true)->filter(fn($p) => (float) $p->normal_price > 0)->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'room_type' => $p->room_type,
                            'room_label' => $p->room_label,
                            'normal_price' => (float) $p->normal_price,
                            'normal_price_formatted' => $p->normal_price_formatted,
                            'promo_price' => ($p->promo_price && (float) $p->promo_price > 0) ? (float) $p->promo_price : null,
                            'promo_price_formatted' => ($p->promo_price && (float) $p->promo_price > 0) ? $p->promo_price_formatted : null,
                            'effective_price' => (float) $p->effective_price,
                            'effective_price_formatted' => $p->effective_price_formatted,
                            'is_active' => (bool) $p->is_active,
                        ];
                    })->values()->toArray(),
                    'includes' => $mergedIncludes,
                    'excludes' => $mergedExcludes,
                ];
            })->values()->toArray();

            // Overall lowest price for package
            $lowestPrice = 0;
            if (!empty($variants)) {
                $variantPrices = array_filter(array_column($variants, 'lowest_price'), fn($p) => $p > 0);
                $lowestPrice = !empty($variantPrices) ? min($variantPrices) : (float) $pkg->price;
            } else {
                $lowestPrice = (float) $pkg->price;
            }

            $totalQuota = $pkg->total_quota;
            $remainingQuota = $pkg->remaining_quota;
            $allSoldOut = $pkg->is_sold_out;

            $mainPhotoUrl = $pkg->main_photo 
                ? \App\Helpers\ImageHelper::url($pkg->main_photo) 
                : ($isHaji 
                    ? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fm=webp&fit=crop&w=480&q=70'
                    : 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fm=webp&fit=crop&w=480&q=70');

            return [
                'id' => $pkg->id,
                'slug' => $pkg->slug,
                'type' => $resolvedType,
                'type_label' => $pkg->type_label,
                'name' => $pkg->name,
                'badge' => $allSoldOut ? 'Sold Out' : ($lowestPrice >= 35000000 ? 'VIP Exclusive' : 'Paling Diminati'),
                'badge_color' => $allSoldOut ? 'red' : 'gold',
                'price' => (float) $lowestPrice,
                'price_formatted' => $lowestPrice > 0 ? 'Rp ' . number_format((float) $lowestPrice, 0, ',', '.') : '-',
                'duration' => $pkg->duration . ' Hari',
                'departure_date' => $pkg->departure_date ? $pkg->departure_date->translatedFormat('d F Y') : '-',
                'hotel_makkah' => !empty($variants[0]['hotel_makkah_name']) ? $variants[0]['hotel_makkah_name'] : 'Pullman Zamzam / Setaraf (★5)',
                'hotel_madinah' => !empty($variants[0]['hotel_madinah_name']) ? $variants[0]['hotel_madinah_name'] : 'Grand Plaza / Setaraf (★4)',
                'airline' => !empty($variants[0]['airline']) ? $variants[0]['airline'] : 'Saudia Airlines / Garuda Indonesia (Direct)',
                'seats_total' => $totalQuota,
                'seats_available' => $remainingQuota,
                'featured' => $pkg->status === 'aktif' && !$allSoldOut,
                'status' => $pkg->status,
                'is_sold_out' => $allSoldOut,
                'thumbnail' => $mainPhotoUrl,
                'description' => $pkg->description ?: 'Perjalanan ibadah penuh kekhusyukan bersama PT. Zein Internasional dengan fasilitas terbaik, kepastian seat, dan pembimbing berpengalaman.',
                'variants' => $variants,
                'includes' => $pkg->includes->isNotEmpty() ? $pkg->includes->pluck('item')->values()->toArray() : (!empty($variants[0]['includes']) ? $variants[0]['includes'] : [
                    'Tiket Pesawat PP Direct',
                    'Visa & Asuransi Perjalanan',
                    'Hotel Makkah & Madinah Bintang 4/5',
                    'Makan 3x sehari menu Indonesia (Fullboard)',
                    'Pembimbing Ibadah (Muthawwif) berpengalaman',
                ]),
                'excludes' => $pkg->excludes->isNotEmpty() ? $pkg->excludes->pluck('item')->values()->toArray() : (!empty($variants[0]['excludes']) ? $variants[0]['excludes'] : [
                    'Pembuatan / Perpanjangan Paspor',
                    'Buku Kuning Vaksin Meningitis / Polio',
                    'Pengeluaran Pribadi (Laundry, Roaming, dll)',
                    'Kelebihan bagasi di luar ketentuan maskapai',
                ]),
            ];
        })->toArray();
    }

    private function getRegistrationSteps()
    {
        return [
            [
                'step' => '01',
                'title' => 'Pilih Paket & Konsultasi',
                'desc' => 'Tentukan paket umrah atau haji khusus sesuai kebutuhan via website atau WhatsApp konsultan resmi Zeintour.',
                'icon' => 'calendar',
            ],
            [
                'step' => '02',
                'title' => 'Penyerahan Dokumen (Maks. 1 Bulan Pra-Berangkat)',
                'desc' => 'Menyerahkan Paspor Asli (min. 2 kata, Suku Kota Cond: IDN ACHMD, berlaku min. 7 bulan), FC KTP, FC KK, Akte Lahir, Buku Nikah, Pas Foto 4x6 5 lembar (latar putih, fokus wajah 80%), dan Kartu Kuning / Sertifikat Suntik Meningitis paling lambat 1 bulan sebelum keberangkatan.',
                'icon' => 'document',
            ],
            [
                'step' => '03',
                'title' => 'Pembayaran Uang Muka (10% & 40%)',
                'desc' => 'Pembayaran Uang Muka: 10% (3 bulan sebelum keberangkatan) dan 40% (2 bulan sebelum keberangkatan). Mendapatkan kuitansi sebagai bukti pembayaran untuk jaminan keberangkatan.',
                'icon' => 'credit-card',
            ],
            [
                'step' => '04',
                'title' => 'Penerimaan Perlengkapan Ibadah',
                'desc' => 'Menerima perlengkapan umrah: Koper 22", Kerudung, Ransel/Tas kabin-Ihrom, Tas Paspor-Sabuk Ihrom, Tas Sandal-Syal, Seragam batik-Buku Do\'a, Mukena/Bergo-Buku Panduan Umrah.',
                'icon' => 'gift',
            ],
            [
                'step' => '05',
                'title' => 'Bimbingan Manasik',
                'desc' => 'Mendapatkan informasi detail tentang keberangkatan dan aturan pembatalan, minimal 1 kali bimbingan manasik.',
                'icon' => 'academic-cap',
            ],
            [
                'step' => '06',
                'title' => 'Pelunasan Biaya (40 Hari Pra-Berangkat)',
                'desc' => 'Pelunasan biaya paket diselesaikan paling lambat 40 hari sebelum keberangkatan.',
                'icon' => 'check-badge',
            ],
            [
                'step' => '07',
                'title' => 'Berangkat Menuju Baitullah',
                'desc' => 'Mendapatkan pelayanan di airport berupa lounge umrah sebagai tempat istirahat untuk menunggu take off, serta pendamping dalam perjalanan / Tour Leader.',
                'icon' => 'paper-airplane',
            ],
        ];
    }

    private function getTravelScheme()
    {
        return [
            'udara' => [
                'title' => 'TRANSPORTASI UDARA',
                'subtitle' => 'Di Bandara Keberangkatan dan Kedatangan',
                'badge' => 'Penerbangan & Handling',
                'points' => [
                    'Jamaah didampingi oleh tour leader dari Indonesia.',
                    'Jamaah dijemput oleh petugas handling di bandara Jeddah atau Madinah.',
                    'Bagasi diangkut oleh porter yang telah disiapkan oleh petugas handling.',
                    'Jamaah diberikan makanan berupa catering/snack.',
                ],
            ],
            'darat' => [
                'title' => 'TRANSPORTASI DARAT',
                'subtitle' => 'Armada Bus Syariah Modern',
                'badge' => 'Bus Syariah 49 Seat',
                'points' => [
                    'Disediakan Bus Syariah jenis Kinglong & Mercedes Benz dengan kapasitas 49 seat, full AC dan memiliki toilet di dalamnya untuk mengantar jamaah ke hotel, tempat ibadah dan tempat-tempat ziarah.',
                    'Disediakan snack pada setiap perjalanan.',
                    'Didampingi Muthawif / Guide yang berpengalaman.',
                ],
            ],
        ];
    }

    private function getSaudiServices()
    {
        return [
            'hotel' => [
                'title' => 'PELAYANAN DI HOTEL',
                'subtitle' => 'Kenyamanan & Fasilitas Hotel',
                'points' => [
                    'Petugas melakukan check in hotel untuk mengambil kunci dan pembagian kamar sesuai room list dari pihak travel dan tipe room sesuai paket, sebelum jamaah tiba di hotel.',
                    'Bagasi diantar ke ruangan oleh bilboy/petugas barang.',
                    'Jamaah langsung masuk ke kamar untuk beristirahat.',
                    'Disediakan konsumsi menu Indonesia 3 kali sehari secara prasmanan.',
                ],
            ],
            'ibadah_ziarah' => [
                'title' => 'IBADAH DAN ZIARAH',
                'subtitle' => 'Bimbingan & Pendampingan Ibadah',
                'points' => [
                    'Pada pelaksanaan ibadah umroh dan ziarah Makkah, Madinah, dan city tour Jeddah didampingi tour leader dan Muthawwif.',
                    'Disediakan Muthawwifah untuk mendampingi jamaah umroh perempuan ketika sholat dan berdoa di Raudhah.',
                ],
            ],
            'tempat_ziarah' => [
                'title' => 'TEMPAT-TEMPAT ZIARAH YANG DIKUNJUNGI',
                'subtitle' => 'Destinasi Bersejarah Napak Tilas',
                'locations' => [
                    'Madinah' => [
                        'Masjid Quba',
                        'Kebun Kurma',
                        'Jabal Uhud',
                        'Masjid Qiblatain',
                        'Percetakan Mushaf (Tantifat)',
                        'Khandaq',
                    ],
                    'Makkah' => [
                        'Jabal Tsur',
                        'Arafah',
                        'Jabal Rahmah',
                        'Muzdalifah',
                        'Mina',
                        'Jiranah',
                        'Jabal Nur',
                        'Hira',
                        'Museum Ka\'bah',
                        'Peternakan Unta',
                        'Masjid Tan\'im',
                    ],
                    'Jeddah' => [
                        'Masjid Qishash',
                        'Makam Ibunda Hawa',
                        'Cornesh',
                        'Albalad',
                    ],
                ],
            ],
        ];
    }

    private function getPilgrimRights()
    {
        return [
            [
                'id' => 'syarat-umrah',
                'title' => 'Formulir & Dokumen Pendaftaran Umrah',
                'content' => '
                    <p class="text-sm font-semibold text-gray-800 mb-2">Mengisi Formulir Pendaftaran dan melampirkan dokumen sebagai berikut:</p>
                    <ol class="space-y-2 text-sm text-gray-700 list-decimal list-inside">
                        <li><strong>Paspor Asli:</strong> Nama di paspor minimal 2 kata, Paspor dari <strong>Suku Kota Cond: IDN ACHMD</strong>, Masa berlaku paspor masih <strong>7 bulan</strong> dari masa berlaku.</li>
                        <li><strong>Foto Copy KTP</strong></li>
                        <li><strong>Foto Copy Kartu Keluarga</strong></li>
                        <li><strong>Akte Lahir:</strong> Bagi anak yang berangkat bersama orang tua.</li>
                        <li><strong>Surat / Buku Nikah Asli dan Foto Copy:</strong> Bagi suami istri.</li>
                        <li><strong>Pas Foto Berwarna Terbaru:</strong> Ukuran <strong>4 x 6</strong> (Jumlah: <strong>5 lembar</strong>) dengan ketentuan: latar belakang berwarna putih, pakaian berwarna, pembesaran muka pada foto 80% (<strong>Fokus Wajah</strong>), tidak berpakaian dinas, tidak berkopyah, tidak menggunakan kaca mata.</li>
                        <li><strong>Kartu Kuning / Sertifikat Suntik Meningitis</strong></li>
                    </ol>
                    <div class="mt-3 p-3 bg-[#EAF1E8] rounded-xl border border-[#CCD8C7] text-xs font-semibold text-[#1B3B2B]">
                        Batas Penyerahan Dokumen: Dokumen persyaratan umrah harus diserahkan paling lambat <strong>1 (satu) bulan sebelum keberangkatan</strong>.
                    </div>
                ',
            ],
            [
                'id' => 'pembayaran-umrah',
                'title' => 'Pembayaran Uang Muka & Pelunasan Biaya',
                'content' => '
                    <div class="space-y-3 text-sm text-gray-700">
                        <div>
                            <strong class="text-gray-800 block mb-1">Pembayaran Uang Muka:</strong>
                            <ul class="space-y-1 pl-4 list-disc">
                                <li><strong>3 Bulan Sebelum Keberangkatan:</strong> 10%</li>
                                <li><strong>2 Bulan Sebelum Keberangkatan:</strong> 40%</li>
                            </ul>
                        </div>
                        <div class="pt-2 border-t border-gray-200">
                            <strong class="text-gray-800 block mb-1">Pelunasan:</strong>
                            <p>Pelunasan biaya paling lambat <strong>40 hari sebelum keberangkatan</strong>.</p>
                        </div>
                    </div>
                ',
            ],
            [
                'id' => 'pembatalan',
                'title' => 'Ketentuan Pembatalan Umrah (Cancellation Fee)',
                'content' => '
                    <p class="text-sm font-semibold text-gray-800 mb-2">Pembatalan dikenakan biaya <strong>cancellation fee</strong> dihitung dari harga paket yang dipilih dengan perincian sebagai berikut:</p>
                    <ol class="space-y-2 text-sm text-gray-700 list-decimal list-inside">
                        <li><strong>Setelah menjadi pendaftar:</strong> 2% dari harga paket.</li>
                        <li><strong>25 hari sebelum keberangkatan:</strong> 25% dari harga paket.</li>
                        <li><strong>15 hari sebelum keberangkatan:</strong> 65% dari harga paket.</li>
                        <li><strong>6 hari sebelum keberangkatan:</strong> 85% dari harga paket.</li>
                    </ol>
                ',
            ],
            [
                'id' => 'hak-sebelum-berangkat',
                'title' => 'HAK JAMAAH SEBELUM BERANGKAT',
                'content' => '
                    <ul class="space-y-2.5 text-sm text-gray-700">
                        <li>• Mendapatkan kuitansi sebagai bukti pembayaran untuk jaminan keberangkatan.</li>
                        <li>• Mendapatkan informasi detail tentang keberangkatan dan aturan pembatalan, minimal 1 kali bimbingan manasik.</li>
                        <li>• Menerima perlengkapan umrah:
                            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 pl-4 mt-2 text-xs text-[#12271E] font-medium bg-[#F9FAF8] p-3 rounded-xl border border-[#E0E7DC]">
                                <li>- Koper 22"</li>
                                <li>- Kerudung</li>
                                <li>- Ransel/Tas kabin-Ihrom</li>
                                <li>- Tas Paspor-Sabuk Ihrom</li>
                                <li>- Tas Sandal-Syal</li>
                                <li>- Seragam batik-Buku Do\'a</li>
                                <li>- Mukena/Bergo-Buku Panduan Umrah</li>
                            </ul>
                        </li>
                        <li class="pt-1">• Mendapatkan pelayanan di airport berupa lounge umrah sebagai tempat istirahat untuk menunggu take off.</li>
                        <li>• Mendapatkan pendamping dalam perjalanan / Tour Leader.</li>
                    </ul>
                ',
            ],
            [
                'id' => 'hak-di-saudi',
                'title' => 'HAK JAMAAH DI SAUDI ARABIA',
                'content' => '
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li>• Mendapatkan penjemputan di Bandara Saudi Arabia.</li>
                        <li>• Mendapatkan akomodasi, konsumsi dan transportasi sesuai dengan paket.</li>
                        <li>• Mendapatkan Muthawwif / Guide sebagai pemandu ibadah dan ziarah.</li>
                    </ul>
                ',
            ],
            [
                'id' => 'hak-kepulangan',
                'title' => 'HAK JAMAAH KEPULANGAN DARI SAUDI ARABIA',
                'content' => '
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li>• Mendapatkan Air Zam zam 5 liter.</li>
                        <li>• Mendapatkan cenderamata berupa sertifikat Umrah.</li>
                        <li>• Mendapatkan buku kenang-kenangan.</li>
                        <li>• Mendapatkan foto keberangkatan.</li>
                    </ul>
                ',
            ],
        ];
    }

    private function getGalleryData()
    {
        return [
            [
                'type' => 'image',
                'title' => 'Kekhusyukan Tawaf Jamaah di Depan Ka\'bah',
                'category' => 'Ibadah',
                'url' => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'thumb' => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fm=webp&fit=crop&w=480&q=70',
            ],
            [
                'type' => 'image',
                'title' => 'Keluarga Jamaah PT. Zein di Pelataran Masjid Nabawi',
                'category' => 'Kebersamaan',
                'url' => 'https://images.unsplash.com/photo-1565552645632-d725f8bfc19a?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'thumb' => 'https://images.unsplash.com/photo-1565552645632-d725f8bfc19a?auto=format&fm=webp&fit=crop&w=480&q=70',
            ],
            [
                'type' => 'image',
                'title' => 'Ziarah Sejarah di Jabal Uhud Madinah',
                'category' => 'Ziarah',
                'url' => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'thumb' => 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fm=webp&fit=crop&w=480&q=70',
            ],
            [
                'type' => 'image',
                'title' => 'Bimbingan Manasik Akbar Pra-Keberangkatan',
                'category' => 'Manasik',
                'url' => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'thumb' => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?auto=format&fm=webp&fit=crop&w=480&q=70',
            ],
            [
                'type' => 'image',
                'title' => 'Kenyamanan Fasilitas Hotel Bintang 5 View Ka\'bah',
                'category' => 'Fasilitas',
                'url' => 'https://images.unsplash.com/photo-1580835845971-a393b73bf370?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'thumb' => 'https://images.unsplash.com/photo-1580835845971-a393b73bf370?auto=format&fm=webp&fit=crop&w=480&q=70',
            ],
            [
                'type' => 'image',
                'title' => 'Pelepasan Jamaah di Bandara Soekarno-Hatta',
                'category' => 'Keberangkatan',
                'url' => 'https://images.unsplash.com/photo-1519817650390-64a93db51149?auto=format&fm=webp&fit=crop&w=1200&q=80',
                'thumb' => 'https://images.unsplash.com/photo-1519817650390-64a93db51149?auto=format&fm=webp&fit=crop&w=480&q=70',
            ],
        ];
    }

    /**
     * Data Mitra Kerja Sama (Partnership)
     * 12 Mitra Resmi: Hotel / Akomodasi (4), Maskapai Penerbangan (5), Bank (3)
     */
    private function getPartners()
    {
        return [
            'hotel' => [
                'title' => 'Hotel / Akomodasi',
                'items' => [
                    [
                        'name' => 'Dar Al-Eiman Co.',
                        'logo' => 'images/Dar Al-Eiman.webp',
                    ],
                    [
                        'name' => 'Al Safwah Royale Orchid',
                        'logo' => 'images/Al Safwah Royale Orchid.webp',
                    ],
                    [
                        'name' => 'Province Al Sham',
                        'logo' => 'images/Province Al Sham.webp',
                    ],
                    [
                        'name' => 'Millennium Al Aqiq Madinah',
                        'logo' => 'images/Millennium Al Aqiq Madinah.webp',
                    ],
                ],
            ],
            'airlines' => [
                'title' => 'Maskapai Penerbangan',
                'items' => [
                    [
                        'name' => 'Etihad Airways',
                        'logo' => 'images/Etihad Airways.webp',
                    ],
                    [
                        'name' => 'Qatar Airways',
                        'logo' => 'images/Qatar Airways.webp',
                    ],
                    [
                        'name' => 'Saudia (Saudi Arabian Airlines)',
                        'logo' => 'images/saudia.webp',
                    ],
                    [
                        'name' => 'Emirates',
                        'logo' => 'images/Emirates.webp',
                    ],
                    [
                        'name' => 'Turkish Airlines',
                        'logo' => 'images/turkish-airlines.webp',
                    ],
                ],
            ],
            'bank' => [
                'title' => 'Bank',
                'items' => [
                    [
                        'name' => 'Bank BRI',
                        'logo' => 'images/bri.webp',
                    ],
                    [
                        'name' => 'Bank Mandiri',
                        'logo' => 'images/mandiri.webp',
                    ],
                    [
                        'name' => 'BCA',
                        'logo' => 'images/bca.webp',
                    ],
                ],
            ],
        ];
    }

    /**
     * Data Lembaga Sertifikasi & Otorisasi Resmi (Authorized)
     * 5 Lembaga Resmi
     */
    private function getAuthorizedInstitutions()
    {
        return [
            [
                'name' => 'Kementerian Agama Republik Indonesia',
                'logo' => 'images/Kementerian Agama RI.webp',
            ],
            [
                'name' => 'SISKOPATUH',
                'logo' => 'images/siskopatuh.webp',
            ],
            [
                'name' => 'Kesthuri',
                'logo' => 'images/Kesthuri.webp',
            ],
            [
                'name' => 'ASITA',
                'logo' => 'images/asita.webp',
            ],
            [
                'name' => 'KAN',
                'logo' => 'images/kan.webp',
            ],
        ];
    }

    /**
     * Halaman Utama: Single Landing Page
     */
    public function index()
    {
        $company = $this->getCompanyData();
        $legalities = $this->getLegalityData();
        $packages = collect($this->getPackagesData())->where('featured', true)->values();
        $registrationSteps = $this->getRegistrationSteps();
        $travelScheme = $this->getTravelScheme();
        $saudiServices = $this->getSaudiServices();
        $pilgrimRights = $this->getPilgrimRights();
        $dbGalleries = \App\Models\Gallery::where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->take(6)
            ->get();
        $galleries = $dbGalleries->isNotEmpty() ? $dbGalleries : array_slice($this->getGalleryData(), 0, 6);
        $partners = $this->getPartners();
        $authorized = $this->getAuthorizedInstitutions();

        $profileVideo = \App\Models\Gallery::where('is_profile_hero', true)
            ->where('type', 'video')
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('landing', compact(
            'company',
            'legalities',
            'packages',
            'registrationSteps',
            'travelScheme',
            'saudiServices',
            'pilgrimRights',
            'galleries',
            'partners',
            'authorized',
            'profileVideo'
        ));
    }

    /**
     * Halaman Detail: Profil Perusahaan
     */
    public function profil()
    {
        $company = $this->getCompanyData();
        $legalities = $this->getLegalityData();

        $profileHero = \App\Models\Gallery::where('is_profile_hero', true)
            ->where('type', 'photo')
            ->where('is_active', true)
            ->latest()
            ->first();

        $profileVideo = \App\Models\Gallery::where('is_profile_hero', true)
            ->where('type', 'video')
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('pages.profil', compact('company', 'legalities', 'profileHero', 'profileVideo'));
    }

    /**
     * Halaman Detail: Legalitas & Izin Resmi
     */
    public function legalitas()
    {
        $company = $this->getCompanyData();
        $legalities = $this->getLegalityData();

        return view('pages.legalitas', compact('company', 'legalities'));
    }

    /**
     * Halaman Katalog Lengkap Paket Umrah & Haji
     */
    public function paket(Request $request)
    {
        $company = $this->getCompanyData();
        $allPackages = collect($this->getPackagesData());

        // Simple Filter
        $type = $request->query('type');
        $maxPrice = $request->query('max_price');

        $packages = $allPackages->when($type, function ($collection, $type) {
            return $collection->where('type', $type);
        })->when($maxPrice, function ($collection, $maxPrice) {
            return $collection->where('price', '<=', (int) $maxPrice);
        })->values();

        return view('pages.paket.index', compact('company', 'packages', 'type', 'maxPrice'));
    }

    /**
     * Halaman Detail Satu Paket
     */
    public function paketDetail($slug)
    {
        $company = $this->getCompanyData();
        $package = collect($this->getPackagesData())->firstWhere('slug', $slug);

        if (!$package) {
            abort(404, 'Paket Umrah/Haji tidak ditemukan');
        }

        $otherPackages = collect($this->getPackagesData())
            ->where('id', '!=', $package['id'])
            ->take(3)
            ->values();

        return view('pages.paket.detail', compact('company', 'package', 'otherPackages'));
    }

    /**
     * Halaman Galeri Foto & Dokumentasi Video
     */
    public function galeri()
    {
        $company = $this->getCompanyData();
        $dbGalleries = \App\Models\Gallery::where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->get();

        $galleries = $dbGalleries->isNotEmpty() ? $dbGalleries : collect($this->getGalleryData());

        $featuredVideo = \App\Models\Gallery::where('is_profile_hero', true)
            ->where('type', 'video')
            ->where('is_active', true)
            ->latest()
            ->first() ?? \App\Models\Gallery::where('type', 'video')
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('pages.galeri', compact('company', 'galleries', 'featuredVideo'));
    }

    /**
     * Halaman Kontak & Lokasi Kantor
     */
    public function kontak()
    {
        $company = $this->getCompanyData();

        return view('pages.kontak', compact('company'));
    }
}
