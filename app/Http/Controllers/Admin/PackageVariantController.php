<?php

namespace App\Http\Controllers\Admin;

use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\PackageVariantPrice;
use App\Models\PackageVariantHotelPhoto;
use App\Models\HotelFacility;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PackageVariantController extends Controller
{
    public function create(Package $package)
    {
        $hotelFacilities = HotelFacility::orderBy('sort_order')->get();
        return view('admin.packages.variants.create', compact('package', 'hotelFacilities'));
    }

    public function store(Request $request, Package $package)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quota' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:aktif,nonaktif,sold_out'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            
            // Airline
            'airline_departure' => ['nullable', 'string', 'max:255'],
            'airline_return' => ['nullable', 'string', 'max:255'],
            'airline_departure_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'airline_return_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            
            // Hotel Makkah
            'hotel_makkah_name' => ['nullable', 'string', 'max:255'],
            'hotel_makkah_star' => ['nullable', 'string', 'max:50'],
            'hotel_makkah_description' => ['nullable', 'string'],
            'hotel_makkah_main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_makkah_building_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_makkah_room_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_makkah_dining_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_makkah_facility_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'makkah_photos' => ['nullable', 'array'],
            'makkah_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            // Hotel Madinah
            'hotel_madinah_name' => ['nullable', 'string', 'max:255'],
            'hotel_madinah_star' => ['nullable', 'string', 'max:50'],
            'hotel_madinah_description' => ['nullable', 'string'],
            'hotel_madinah_main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_madinah_building_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_madinah_room_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_madinah_dining_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_madinah_facility_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'madinah_photos' => ['nullable', 'array'],
            'madinah_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            // Pricing matrix
            'prices' => ['nullable', 'array'],
            'prices.*.room_type' => ['required_with:prices', 'string', 'in:quad,triple,double'],
            'prices.*.normal_price' => ['nullable'],
            'prices.*.promo_price' => ['nullable', 'numeric', 'min:0'],
            'prices.*.is_active' => ['nullable'],
            
            // Includes & Excludes
            'includes' => ['nullable', 'array'],
            'includes.*' => ['required', 'string', 'max:255'],
            'excludes' => ['nullable', 'array'],
            'excludes.*' => ['required', 'string', 'max:255'],
            
            // Hotel Facilities
            'makkah_facilities' => ['nullable', 'array'],
            'makkah_facilities.*' => ['exists:hotel_facilities,id'],
            'madinah_facilities' => ['nullable', 'array'],
            'madinah_facilities.*' => ['exists:hotel_facilities,id'],
        ], [
            'main_photo.image' => 'Foto utama harus berupa file gambar.',
            'main_photo.mimes' => 'Format file tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'main_photo.max' => 'Ukuran foto utama maksimal 5MB.',
            'airline_departure_logo.image' => 'Logo maskapai berangkat harus berupa gambar.',
            'airline_departure_logo.mimes' => 'Format file tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'airline_departure_logo.max' => 'Ukuran logo maskapai berangkat maksimal 5MB.',
            'airline_return_logo.image' => 'Logo maskapai pulang harus berupa gambar.',
            'airline_return_logo.mimes' => 'Format file tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'airline_return_logo.max' => 'Ukuran logo maskapai pulang maksimal 5MB.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $prices = $request->input('prices', []);
            if (is_array($prices)) {
                foreach ($prices as $idx => $priceRow) {
                    $isActive = !empty($priceRow['is_active']) && $priceRow['is_active'] != '0' && $priceRow['is_active'] !== false;
                    $normalPrice = $priceRow['normal_price'] ?? null;
                    $roomType = $priceRow['room_type'] ?? 'terpilih';
                    $roomLabel = PackageVariantPrice::ROOM_LABELS[$roomType] ?? ucfirst($roomType);

                    if ($isActive) {
                        if ($normalPrice === null || $normalPrice === '' || (float) $normalPrice <= 0) {
                            $validator->errors()->add("prices.{$idx}.normal_price", "Harga normal untuk {$roomLabel} wajib diisi dan harus lebih dari 0 jika statusnya aktif.");
                        }
                    }

                    $promoPrice = $priceRow['promo_price'] ?? null;
                    if ($promoPrice !== null && $promoPrice !== '') {
                        if ($normalPrice && (float) $promoPrice >= (float) $normalPrice) {
                            $validator->errors()->add("prices.{$idx}.promo_price", "Harga promo untuk {$roomLabel} harus lebih kecil dari harga normal.");
                        }
                    }
                }
            }
        });

        $validated = $validator->validate();

        $variant = DB::transaction(function () use ($request, $package, $validated) {
            $data = collect($validated)->except([
                'prices', 'includes', 'excludes', 'makkah_facilities', 'madinah_facilities',
                'main_photo', 'airline_departure_logo', 'airline_return_logo',
                'makkah_photos', 'madinah_photos',
                'hotel_makkah_main_photo', 'hotel_makkah_building_photo', 'hotel_makkah_room_photo', 'hotel_makkah_dining_photo', 'hotel_makkah_facility_photo',
                'hotel_madinah_main_photo', 'hotel_madinah_building_photo', 'hotel_madinah_room_photo', 'hotel_madinah_dining_photo', 'hotel_madinah_facility_photo',
            ])->toArray();

            $data['package_id'] = $package->id;
            $data['sort_order'] = $validated['sort_order'] ?? ($package->variants()->max('sort_order') ?? 0) + 1;

            // Handle file uploads
            if ($request->hasFile('main_photo')) {
                $data['main_photo'] = $request->file('main_photo')->store('packages/variants/photos', 'public');
            }
            if ($request->hasFile('airline_departure_logo')) {
                $data['airline_departure_logo'] = $request->file('airline_departure_logo')->store('packages/variants/airlines', 'public');
            }
            if ($request->hasFile('airline_return_logo')) {
                $data['airline_return_logo'] = $request->file('airline_return_logo')->store('packages/variants/airlines', 'public');
            }

            $variant = PackageVariant::create($data);

            // Handle Hotel Makkah categorized photos
            $makkahCategories = [
                'hotel_makkah_main_photo' => 'main',
                'hotel_makkah_building_photo' => 'building',
                'hotel_makkah_room_photo' => 'room',
                'hotel_makkah_dining_photo' => 'dining',
                'hotel_makkah_facility_photo' => 'facility',
            ];
            foreach ($makkahCategories as $inputName => $cat) {
                if ($request->hasFile($inputName)) {
                    $path = $request->file($inputName)->store('packages/variants/hotels/makkah', 'public');
                    $variant->hotelPhotos()->create([
                        'hotel_type' => 'makkah',
                        'photo_path' => $path,
                        'category' => $cat,
                        'sort_order' => 0,
                    ]);
                }
            }
            if ($request->hasFile('makkah_photos')) {
                $order = 1;
                foreach ($request->file('makkah_photos') as $photoFile) {
                    $path = $photoFile->store('packages/variants/hotels/makkah', 'public');
                    $variant->hotelPhotos()->create([
                        'hotel_type' => 'makkah',
                        'photo_path' => $path,
                        'category' => 'gallery',
                        'sort_order' => $order++,
                    ]);
                }
            }

            // Handle Hotel Madinah categorized photos
            $madinahCategories = [
                'hotel_madinah_main_photo' => 'main',
                'hotel_madinah_building_photo' => 'building',
                'hotel_madinah_room_photo' => 'room',
                'hotel_madinah_dining_photo' => 'dining',
                'hotel_madinah_facility_photo' => 'facility',
            ];
            foreach ($madinahCategories as $inputName => $cat) {
                if ($request->hasFile($inputName)) {
                    $path = $request->file($inputName)->store('packages/variants/hotels/madinah', 'public');
                    $variant->hotelPhotos()->create([
                        'hotel_type' => 'madinah',
                        'photo_path' => $path,
                        'category' => $cat,
                        'sort_order' => 0,
                    ]);
                }
            }
            if ($request->hasFile('madinah_photos')) {
                $order = 1;
                foreach ($request->file('madinah_photos') as $photoFile) {
                    $path = $photoFile->store('packages/variants/hotels/madinah', 'public');
                    $variant->hotelPhotos()->create([
                        'hotel_type' => 'madinah',
                        'photo_path' => $path,
                        'category' => 'gallery',
                        'sort_order' => $order++,
                    ]);
                }
            }

            // Create pricing matrix
            if (!empty($validated['prices'])) {
                $sortOrder = 0;
                foreach ($validated['prices'] as $priceData) {
                    $isActive = isset($priceData['is_active']) && $priceData['is_active'] != '0';
                    $normalPrice = ($priceData['normal_price'] !== null && $priceData['normal_price'] !== '') ? (float) $priceData['normal_price'] : 0;
                    $promoPrice = (!empty($priceData['promo_price']) && (float) $priceData['promo_price'] > 0) ? (float) $priceData['promo_price'] : null;

                    $variant->prices()->create([
                        'room_type' => $priceData['room_type'],
                        'normal_price' => $normalPrice,
                        'promo_price' => $promoPrice,
                        'is_active' => $isActive,
                        'sort_order' => $sortOrder++,
                    ]);
                }
            } else {
                foreach (['quad' => 0, 'triple' => 1, 'double' => 2] as $type => $order) {
                    $variant->prices()->create([
                        'room_type' => $type,
                        'normal_price' => 0,
                        'promo_price' => null,
                        'is_active' => false,
                        'sort_order' => $order,
                    ]);
                }
            }

            // Create includes
            if (!empty($validated['includes'])) {
                $sortOrder = 0;
                foreach ($validated['includes'] as $item) {
                    if (trim($item)) {
                        $variant->includes()->create(['item' => trim($item), 'sort_order' => $sortOrder++]);
                    }
                }
            }

            // Create excludes
            if (!empty($validated['excludes'])) {
                $sortOrder = 0;
                foreach ($validated['excludes'] as $item) {
                    if (trim($item)) {
                        $variant->excludes()->create(['item' => trim($item), 'sort_order' => $sortOrder++]);
                    }
                }
            }

            // Sync hotel facilities
            $facilitySync = [];
            if (!empty($validated['makkah_facilities'])) {
                foreach ($validated['makkah_facilities'] as $facilityId) {
                    $facilitySync[$facilityId] = ['hotel_type' => 'makkah'];
                }
            }
            if (!empty($validated['madinah_facilities'])) {
                foreach ($validated['madinah_facilities'] as $facilityId) {
                    if (isset($facilitySync[$facilityId])) {
                        DB::table('package_variant_hotel_facilities')->insert([
                            'package_variant_id' => $variant->id,
                            'hotel_facility_id' => $facilityId,
                            'hotel_type' => 'madinah',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $facilitySync[$facilityId] = ['hotel_type' => 'madinah'];
                    }
                }
            }
            if (!empty($facilitySync)) {
                $variant->hotelFacilities()->attach($facilitySync);
            }
        });

        return redirect()->route('admin.packages.show', $package)
            ->with('success', "Sub-paket '{$validated['name']}' berhasil ditambahkan.");
    }

    public function edit(Package $package, PackageVariant $variant)
    {
        $variant->load([
            'prices' => fn($q) => $q->orderBy('sort_order'),
            'includes' => fn($q) => $q->orderBy('sort_order'),
            'excludes' => fn($q) => $q->orderBy('sort_order'),
            'hotelPhotos' => fn($q) => $q->orderBy('sort_order'),
        ]);

        $hotelFacilities = HotelFacility::orderBy('sort_order')->get();
        
        $selectedMakkahFacilities = DB::table('package_variant_hotel_facilities')
            ->where('package_variant_id', $variant->id)
            ->where('hotel_type', 'makkah')
            ->pluck('hotel_facility_id')
            ->toArray();
        
        $selectedMadinahFacilities = DB::table('package_variant_hotel_facilities')
            ->where('package_variant_id', $variant->id)
            ->where('hotel_type', 'madinah')
            ->pluck('hotel_facility_id')
            ->toArray();

        return view('admin.packages.variants.edit', compact(
            'package', 'variant', 'hotelFacilities',
            'selectedMakkahFacilities', 'selectedMadinahFacilities'
        ));
    }

    public function update(Request $request, Package $package, PackageVariant $variant)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quota' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:aktif,nonaktif,sold_out'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            
            // Airline
            'airline_departure' => ['nullable', 'string', 'max:255'],
            'airline_return' => ['nullable', 'string', 'max:255'],
            'airline_departure_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'airline_return_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            
            // Hotel Makkah
            'hotel_makkah_name' => ['nullable', 'string', 'max:255'],
            'hotel_makkah_star' => ['nullable', 'string', 'max:50'],
            'hotel_makkah_description' => ['nullable', 'string'],
            'hotel_makkah_main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_makkah_building_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_makkah_room_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_makkah_dining_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_makkah_facility_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'makkah_photos' => ['nullable', 'array'],
            'makkah_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            // Hotel Madinah
            'hotel_madinah_name' => ['nullable', 'string', 'max:255'],
            'hotel_madinah_star' => ['nullable', 'string', 'max:50'],
            'hotel_madinah_description' => ['nullable', 'string'],
            'hotel_madinah_main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_madinah_building_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_madinah_room_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_madinah_dining_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'hotel_madinah_facility_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'madinah_photos' => ['nullable', 'array'],
            'madinah_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            // Pricing matrix
            'prices' => ['nullable', 'array'],
            'prices.*.id' => ['nullable', 'integer'],
            'prices.*.room_type' => ['required_with:prices', 'string', 'in:quad,triple,double'],
            'prices.*.normal_price' => ['nullable'],
            'prices.*.promo_price' => ['nullable', 'numeric', 'min:0'],
            'prices.*.is_active' => ['nullable'],
            
            // Includes & Excludes
            'includes' => ['nullable', 'array'],
            'includes.*' => ['required', 'string', 'max:255'],
            'excludes' => ['nullable', 'array'],
            'excludes.*' => ['required', 'string', 'max:255'],
            
            // Hotel Facilities
            'makkah_facilities' => ['nullable', 'array'],
            'makkah_facilities.*' => ['exists:hotel_facilities,id'],
            'madinah_facilities' => ['nullable', 'array'],
            'madinah_facilities.*' => ['exists:hotel_facilities,id'],
        ], [
            'main_photo.image' => 'Foto utama harus berupa file gambar.',
            'main_photo.mimes' => 'Format file tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'main_photo.max' => 'Ukuran foto utama maksimal 5MB.',
            'airline_departure_logo.image' => 'Logo maskapai berangkat harus berupa gambar.',
            'airline_departure_logo.mimes' => 'Format file tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'airline_departure_logo.max' => 'Ukuran logo maskapai berangkat maksimal 5MB.',
            'airline_return_logo.image' => 'Logo maskapai pulang harus berupa gambar.',
            'airline_return_logo.mimes' => 'Format file tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'airline_return_logo.max' => 'Ukuran logo maskapai pulang maksimal 5MB.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $prices = $request->input('prices', []);
            if (is_array($prices)) {
                foreach ($prices as $idx => $priceRow) {
                    $isActive = !empty($priceRow['is_active']) && $priceRow['is_active'] != '0' && $priceRow['is_active'] !== false;
                    $normalPrice = $priceRow['normal_price'] ?? null;
                    $roomType = $priceRow['room_type'] ?? 'terpilih';
                    $roomLabel = PackageVariantPrice::ROOM_LABELS[$roomType] ?? ucfirst($roomType);

                    if ($isActive) {
                        if ($normalPrice === null || $normalPrice === '' || (float) $normalPrice <= 0) {
                            $validator->errors()->add("prices.{$idx}.normal_price", "Harga normal untuk {$roomLabel} wajib diisi dan harus lebih dari 0 jika statusnya aktif.");
                        }
                    }

                    $promoPrice = $priceRow['promo_price'] ?? null;
                    if ($promoPrice !== null && $promoPrice !== '') {
                        if ($normalPrice && (float) $promoPrice >= (float) $normalPrice) {
                            $validator->errors()->add("prices.{$idx}.promo_price", "Harga promo untuk {$roomLabel} harus lebih kecil dari harga normal.");
                        }
                    }
                }
            }
        });

        $validated = $validator->validate();

        DB::transaction(function () use ($request, $package, $variant, $validated) {
            $data = collect($validated)->except([
                'prices', 'includes', 'excludes', 'makkah_facilities', 'madinah_facilities',
                'main_photo', 'airline_departure_logo', 'airline_return_logo',
                'makkah_photos', 'madinah_photos',
                'hotel_makkah_main_photo', 'hotel_makkah_building_photo', 'hotel_makkah_room_photo', 'hotel_makkah_dining_photo', 'hotel_makkah_facility_photo',
                'hotel_madinah_main_photo', 'hotel_madinah_building_photo', 'hotel_madinah_room_photo', 'hotel_madinah_dining_photo', 'hotel_madinah_facility_photo',
            ])->toArray();

            // Update slug if name changed
            if ($variant->name !== $validated['name']) {
                $baseSlug = Str::slug($package->name . ' ' . $validated['name']);
                $slug = $baseSlug;
                $counter = 1;
                while (PackageVariant::where('slug', $slug)->where('id', '!=', $variant->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $data['slug'] = $slug;
            }

            // Handle file uploads
            if ($request->hasFile('main_photo')) {
                if ($variant->main_photo && Storage::disk('public')->exists($variant->main_photo)) {
                    Storage::disk('public')->delete($variant->main_photo);
                }
                $data['main_photo'] = $request->file('main_photo')->store('packages/variants/photos', 'public');
            }
            if ($request->hasFile('airline_departure_logo')) {
                if ($variant->airline_departure_logo && Storage::disk('public')->exists($variant->airline_departure_logo)) {
                    Storage::disk('public')->delete($variant->airline_departure_logo);
                }
                $data['airline_departure_logo'] = $request->file('airline_departure_logo')->store('packages/variants/airlines', 'public');
            }
            if ($request->hasFile('airline_return_logo')) {
                if ($variant->airline_return_logo && Storage::disk('public')->exists($variant->airline_return_logo)) {
                    Storage::disk('public')->delete($variant->airline_return_logo);
                }
                $data['airline_return_logo'] = $request->file('airline_return_logo')->store('packages/variants/airlines', 'public');
            }

            $variant->update($data);

            // Support explicit deletion of hotel photos by ID if provided in request
            if ($request->filled('deleted_hotel_photo_ids') && is_array($request->deleted_hotel_photo_ids)) {
                $photosToDelete = $variant->hotelPhotos()->whereIn('id', $request->deleted_hotel_photo_ids)->get();
                foreach ($photosToDelete as $delPhoto) {
                    if ($delPhoto->photo_path && Storage::disk('public')->exists($delPhoto->photo_path)) {
                        Storage::disk('public')->delete($delPhoto->photo_path);
                    }
                    $delPhoto->delete();
                }
            }

            // Handle Hotel Makkah categorized photos (APPEND new photos, KEEP all existing photos)
            $makkahCategories = [
                'hotel_makkah_main_photo' => 'main',
                'hotel_makkah_building_photo' => 'building',
                'hotel_makkah_room_photo' => 'room',
                'hotel_makkah_dining_photo' => 'dining',
                'hotel_makkah_facility_photo' => 'facility',
            ];
            foreach ($makkahCategories as $inputName => $cat) {
                if ($request->hasFile($inputName)) {
                    $order = ($variant->hotelPhotos()->where('hotel_type', 'makkah')->max('sort_order') ?? 0) + 1;
                    $path = $request->file($inputName)->store('packages/variants/hotels/makkah', 'public');
                    $variant->hotelPhotos()->create([
                        'hotel_type' => 'makkah',
                        'photo_path' => $path,
                        'category' => $cat,
                        'sort_order' => $order,
                    ]);
                }
            }
            if ($request->hasFile('makkah_photos')) {
                $order = ($variant->hotelPhotos()->where('hotel_type', 'makkah')->max('sort_order') ?? 0) + 1;
                foreach ($request->file('makkah_photos') as $photoFile) {
                    $path = $photoFile->store('packages/variants/hotels/makkah', 'public');
                    $variant->hotelPhotos()->create([
                        'hotel_type' => 'makkah',
                        'photo_path' => $path,
                        'category' => 'gallery',
                        'sort_order' => $order++,
                    ]);
                }
            }

            // Handle Hotel Madinah categorized photos (APPEND new photos, KEEP all existing photos)
            $madinahCategories = [
                'hotel_madinah_main_photo' => 'main',
                'hotel_madinah_building_photo' => 'building',
                'hotel_madinah_room_photo' => 'room',
                'hotel_madinah_dining_photo' => 'dining',
                'hotel_madinah_facility_photo' => 'facility',
            ];
            foreach ($madinahCategories as $inputName => $cat) {
                if ($request->hasFile($inputName)) {
                    $order = ($variant->hotelPhotos()->where('hotel_type', 'madinah')->max('sort_order') ?? 0) + 1;
                    $path = $request->file($inputName)->store('packages/variants/hotels/madinah', 'public');
                    $variant->hotelPhotos()->create([
                        'hotel_type' => 'madinah',
                        'photo_path' => $path,
                        'category' => $cat,
                        'sort_order' => $order,
                    ]);
                }
            }
            if ($request->hasFile('madinah_photos')) {
                $order = ($variant->hotelPhotos()->where('hotel_type', 'madinah')->max('sort_order') ?? 0) + 1;
                foreach ($request->file('madinah_photos') as $photoFile) {
                    $path = $photoFile->store('packages/variants/hotels/madinah', 'public');
                    $variant->hotelPhotos()->create([
                        'hotel_type' => 'madinah',
                        'photo_path' => $path,
                        'category' => 'gallery',
                        'sort_order' => $order++,
                    ]);
                }
            }

            // Sync pricing matrix
            $pricesInput = $validated['prices'] ?? [];
            if (!empty($pricesInput)) {
                $existingPrices = $variant->prices()->get()->keyBy('room_type');
                $sortOrder = 0;

                foreach ($pricesInput as $priceData) {
                    $roomType = $priceData['room_type'];
                    $isActive = !empty($priceData['is_active']) && $priceData['is_active'] != '0' && $priceData['is_active'] !== false;
                    $normalPrice = $priceData['normal_price'] ?? null;
                    $promoPrice = $priceData['promo_price'] ?? null;

                    if ($existingPrices->has($roomType)) {
                        $existingRecord = $existingPrices->get($roomType);
                        $existingRecord->update([
                            'normal_price' => ($normalPrice !== null && $normalPrice !== '') ? (float) $normalPrice : (float) ($existingRecord->normal_price ?? 0),
                            'promo_price' => ($promoPrice !== null && $promoPrice !== '') ? (float) $promoPrice : null,
                            'is_active' => $isActive,
                            'sort_order' => $sortOrder++,
                        ]);
                    } else {
                        if ($isActive && ($normalPrice === null || $normalPrice === '')) {
                            continue;
                        }
                        $variant->prices()->create([
                            'room_type' => $roomType,
                            'normal_price' => (float) ($normalPrice ?? 0),
                            'promo_price' => ($promoPrice !== null && $promoPrice !== '') ? (float) $promoPrice : null,
                            'is_active' => $isActive,
                            'sort_order' => $sortOrder++,
                        ]);
                    }
                }
            }

            // Sync includes
            $variant->includes()->delete();
            if (!empty($validated['includes'])) {
                $sortOrder = 0;
                foreach ($validated['includes'] as $item) {
                    if (trim($item)) {
                        $variant->includes()->create(['item' => trim($item), 'sort_order' => $sortOrder++]);
                    }
                }
            }

            // Sync excludes
            $variant->excludes()->delete();
            if (!empty($validated['excludes'])) {
                $sortOrder = 0;
                foreach ($validated['excludes'] as $item) {
                    if (trim($item)) {
                        $variant->excludes()->create(['item' => trim($item), 'sort_order' => $sortOrder++]);
                    }
                }
            }

            // Sync hotel facilities
            DB::table('package_variant_hotel_facilities')
                ->where('package_variant_id', $variant->id)
                ->delete();

            if (!empty($validated['makkah_facilities'])) {
                foreach ($validated['makkah_facilities'] as $facilityId) {
                    DB::table('package_variant_hotel_facilities')->insert([
                        'package_variant_id' => $variant->id,
                        'hotel_facility_id' => $facilityId,
                        'hotel_type' => 'makkah',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            if (!empty($validated['madinah_facilities'])) {
                foreach ($validated['madinah_facilities'] as $facilityId) {
                    DB::table('package_variant_hotel_facilities')->insert([
                        'package_variant_id' => $variant->id,
                        'hotel_facility_id' => $facilityId,
                        'hotel_type' => 'madinah',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return redirect()->route('admin.packages.show', $package)
            ->with('success', "Sub-paket '{$validated['name']}' berhasil diperbarui.");
    }

    /**
     * Upload photo hotel via AJAX tanpa me-reset atau mengubah field lain pada paket / varian.
     */
    public function uploadPhotos(Request $request, Package $package, PackageVariant $variant)
    {
        $request->validate([
            'hotel_type' => ['required', 'in:makkah,madinah'],
            'category' => ['nullable', 'string', 'in:main,building,room,dining,facility,gallery'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $hotelType = $request->hotel_type;
        $category = $request->category ?? 'gallery';
        $uploaded = [];

        if ($request->hasFile('photo')) {
            $order = ($variant->hotelPhotos()->where('hotel_type', $hotelType)->max('sort_order') ?? 0) + 1;
            $path = $request->file('photo')->store("packages/variants/hotels/{$hotelType}", 'public');
            $hPhoto = $variant->hotelPhotos()->create([
                'hotel_type' => $hotelType,
                'photo_path' => $path,
                'category' => $category,
                'sort_order' => $order,
            ]);
            $uploaded[] = [
                'id' => $hPhoto->id,
                'url' => Storage::url($hPhoto->photo_path),
                'category' => $hPhoto->category,
                'hotel_type' => $hPhoto->hotel_type,
            ];
        }

        if ($request->hasFile('photos')) {
            $order = ($variant->hotelPhotos()->where('hotel_type', $hotelType)->max('sort_order') ?? 0) + 1;
            foreach ($request->file('photos') as $photoFile) {
                $path = $photoFile->store("packages/variants/hotels/{$hotelType}", 'public');
                $hPhoto = $variant->hotelPhotos()->create([
                    'hotel_type' => $hotelType,
                    'photo_path' => $path,
                    'category' => $category,
                    'sort_order' => $order++,
                ]);
                $uploaded[] = [
                    'id' => $hPhoto->id,
                    'url' => Storage::url($hPhoto->photo_path),
                    'category' => $hPhoto->category,
                    'hotel_type' => $hPhoto->hotel_type,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto hotel berhasil diunggah.',
            'photos' => $uploaded,
        ]);
    }

    public function deleteHotelPhoto(Request $request, Package $package, PackageVariant $variant, PackageVariantHotelPhoto $photo)
    {
        if ($photo->package_variant_id !== $variant->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $photo->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Foto hotel berhasil dihapus.']);
        }

        return back()->with('success', 'Foto hotel berhasil dihapus.');
    }

    public function destroy(Package $package, PackageVariant $variant)
    {
        if ($variant->registrations()->count() > 0) {
            return back()->with('error', 'Sub-paket tidak dapat dihapus karena sudah memiliki data pendaftaran jamaah.');
        }

        // Delete physical files
        foreach (['main_photo', 'airline_departure_logo', 'airline_return_logo'] as $field) {
            if ($variant->$field && Storage::disk('public')->exists($variant->$field)) {
                Storage::disk('public')->delete($variant->$field);
            }
        }

        foreach ($variant->hotelPhotos as $photo) {
            if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }
        }

        $variant->delete();

        return redirect()->route('admin.packages.show', $package)
            ->with('success', "Sub-paket '{$variant->name}' berhasil dihapus.");
    }
}
