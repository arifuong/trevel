<?php

namespace App\Http\Controllers\Admin;

use App\Models\Package;
use App\Models\PackageVariant;
use App\Models\PackageVariantPrice;
use App\Models\Airline;
use App\Models\Hotel;
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
        $package->load(['includes', 'excludes']);
        $airlines = Airline::active()->orderBy('sort_order')->get();
        $hotelsMakkah = Hotel::active()->makkah()->orderBy('name')->get();
        $hotelsMadinah = Hotel::active()->madinah()->orderBy('name')->get();

        return view('admin.packages.variants.create', compact(
            'package', 'airlines', 'hotelsMakkah', 'hotelsMadinah'
        ));
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

            // Hotel (FK to master)
            'hotel_makkah_id' => ['nullable', 'exists:hotels,id'],
            'hotel_madinah_id' => ['nullable', 'exists:hotels,id'],

            // Airlines (many-to-many)
            'airline_ids' => ['nullable', 'array'],
            'airline_ids.*' => ['exists:airlines,id'],

            // Pricing matrix
            'prices' => ['nullable', 'array'],
            'prices.*.room_type' => ['required_with:prices', 'string', 'in:quad,triple,double'],
            'prices.*.normal_price' => ['nullable'],
            'prices.*.promo_price' => ['nullable', 'numeric', 'min:0'],
            'prices.*.is_active' => ['nullable'],
            'prices.*.has_promo' => ['nullable'],

            // Override Include/Exclude (opsional, tier-specific)
            'has_include_override' => ['nullable', 'boolean'],
            'override_includes' => ['nullable', 'array'],
            'override_includes.*' => ['nullable', 'string', 'max:255'],
            'has_exclude_override' => ['nullable', 'boolean'],
            'override_excludes' => ['nullable', 'array'],
            'override_excludes.*' => ['nullable', 'string', 'max:255'],
        ], [
            'main_photo.image' => 'Foto utama harus berupa file gambar.',
            'main_photo.mimes' => 'Format tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'main_photo.max' => 'Ukuran foto utama maksimal 5MB.',
        ]);

        // Custom pricing validation
        $validator->after(function ($validator) use ($request) {
            $prices = $request->input('prices', []);
            if (is_array($prices)) {
                foreach ($prices as $idx => $priceRow) {
                    $isActive = !empty($priceRow['is_active']) && $priceRow['is_active'] != '0';
                    $normalPrice = $priceRow['normal_price'] ?? null;
                    $roomType = $priceRow['room_type'] ?? 'terpilih';
                    $roomLabel = PackageVariantPrice::ROOM_LABELS[$roomType] ?? ucfirst($roomType);

                    if ($isActive) {
                        if ($normalPrice === null || $normalPrice === '' || (float) $normalPrice <= 0) {
                            $validator->errors()->add("prices.{$idx}.normal_price", "Harga normal untuk {$roomLabel} wajib diisi dan > 0 jika aktif.");
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
            $data = [
                'package_id' => $package->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'quota' => $validated['quota'],
                'status' => $validated['status'],
                'sort_order' => $validated['sort_order'] ?? ($package->variants()->max('sort_order') ?? 0) + 1,
                'hotel_makkah_id' => $validated['hotel_makkah_id'] ?? null,
                'hotel_madinah_id' => $validated['hotel_madinah_id'] ?? null,
                'has_include_override' => !empty($validated['has_include_override']),
                'has_exclude_override' => !empty($validated['has_exclude_override']),
            ];

            // Handle main photo upload
            if ($request->hasFile('main_photo')) {
                $data['main_photo'] = $request->file('main_photo')->store('packages/variants/photos', 'public');
            }

            $variant = PackageVariant::create($data);

            // Sync airlines (many-to-many)
            if (!empty($validated['airline_ids'])) {
                $syncData = [];
                foreach ($validated['airline_ids'] as $order => $airlineId) {
                    $syncData[$airlineId] = ['sort_order' => $order];
                }
                $variant->airlines()->sync($syncData);
            }

            // Create pricing matrix
            if (!empty($validated['prices'])) {
                $sortOrder = 0;
                foreach ($validated['prices'] as $priceData) {
                    $isActive = isset($priceData['is_active']) && $priceData['is_active'] != '0';
                    $normalPrice = ($priceData['normal_price'] !== null && $priceData['normal_price'] !== '') ? (float) $priceData['normal_price'] : 0;
                    $hasPromo = !empty($priceData['has_promo']) && $priceData['has_promo'] != '0';
                    $promoPrice = ($hasPromo && !empty($priceData['promo_price']) && (float) $priceData['promo_price'] > 0) ? (float) $priceData['promo_price'] : null;

                    $variant->prices()->create([
                        'room_type' => $priceData['room_type'],
                        'normal_price' => $normalPrice,
                        'promo_price' => $promoPrice,
                        'is_active' => $isActive,
                        'sort_order' => $sortOrder++,
                    ]);
                }
            } else {
                // Default: create all room types as inactive
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

            // Override includes (tier-specific additions)
            if (!empty($validated['has_include_override']) && !empty($validated['override_includes'])) {
                $filteredIncludes = array_values(array_filter($validated['override_includes'], fn($item) => !is_null($item) && trim($item) !== ''));
                $sortOrder = 0;
                foreach ($filteredIncludes as $item) {
                    $variant->overrideIncludes()->create(['item' => trim($item), 'sort_order' => $sortOrder++]);
                }
            }

            // Override excludes (tier-specific additions)
            if (!empty($validated['has_exclude_override']) && !empty($validated['override_excludes'])) {
                $filteredExcludes = array_values(array_filter($validated['override_excludes'], fn($item) => !is_null($item) && trim($item) !== ''));
                $sortOrder = 0;
                foreach ($filteredExcludes as $item) {
                    $variant->overrideExcludes()->create(['item' => trim($item), 'sort_order' => $sortOrder++]);
                }
            }

            return $variant;
        });

        return redirect()->route('admin.packages.show', $package)
            ->with('success', "Sub-paket '{$validated['name']}' berhasil ditambahkan.");
    }

    public function edit(Package $package, PackageVariant $variant)
    {
        $variant->load([
            'prices' => fn($q) => $q->orderBy('sort_order'),
            'overrideIncludes' => fn($q) => $q->orderBy('sort_order'),
            'overrideExcludes' => fn($q) => $q->orderBy('sort_order'),
            'airlines',
            'hotelMakkah.photos',
            'hotelMakkah.facilities',
            'hotelMadinah.photos',
            'hotelMadinah.facilities',
        ]);

        $package->load(['includes', 'excludes']);
        $airlines = Airline::active()->orderBy('sort_order')->get();
        $hotelsMakkah = Hotel::active()->makkah()->orderBy('name')->get();
        $hotelsMadinah = Hotel::active()->madinah()->orderBy('name')->get();
        $selectedAirlineIds = $variant->airlines->pluck('id')->toArray();

        return view('admin.packages.variants.edit', compact(
            'package', 'variant', 'airlines', 'hotelsMakkah', 'hotelsMadinah',
            'selectedAirlineIds'
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

            // Hotel (FK to master)
            'hotel_makkah_id' => ['nullable', 'exists:hotels,id'],
            'hotel_madinah_id' => ['nullable', 'exists:hotels,id'],

            // Airlines (many-to-many)
            'airline_ids' => ['nullable', 'array'],
            'airline_ids.*' => ['exists:airlines,id'],

            // Pricing matrix
            'prices' => ['nullable', 'array'],
            'prices.*.room_type' => ['required_with:prices', 'string', 'in:quad,triple,double'],
            'prices.*.normal_price' => ['nullable'],
            'prices.*.promo_price' => ['nullable', 'numeric', 'min:0'],
            'prices.*.is_active' => ['nullable'],
            'prices.*.has_promo' => ['nullable'],

            // Override Include/Exclude
            'has_include_override' => ['nullable', 'boolean'],
            'override_includes' => ['nullable', 'array'],
            'override_includes.*' => ['nullable', 'string', 'max:255'],
            'has_exclude_override' => ['nullable', 'boolean'],
            'override_excludes' => ['nullable', 'array'],
            'override_excludes.*' => ['nullable', 'string', 'max:255'],
        ], [
            'main_photo.image' => 'Foto utama harus berupa file gambar.',
            'main_photo.mimes' => 'Format tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'main_photo.max' => 'Ukuran foto utama maksimal 5MB.',
        ]);

        // Custom pricing validation
        $validator->after(function ($validator) use ($request) {
            $prices = $request->input('prices', []);
            if (is_array($prices)) {
                foreach ($prices as $idx => $priceRow) {
                    $isActive = !empty($priceRow['is_active']) && $priceRow['is_active'] != '0';
                    $normalPrice = $priceRow['normal_price'] ?? null;
                    $roomType = $priceRow['room_type'] ?? 'terpilih';
                    $roomLabel = PackageVariantPrice::ROOM_LABELS[$roomType] ?? ucfirst($roomType);

                    if ($isActive) {
                        if ($normalPrice === null || $normalPrice === '' || (float) $normalPrice <= 0) {
                            $validator->errors()->add("prices.{$idx}.normal_price", "Harga normal untuk {$roomLabel} wajib diisi dan > 0 jika aktif.");
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
            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'quota' => $validated['quota'],
                'status' => $validated['status'],
                'sort_order' => $validated['sort_order'] ?? $variant->sort_order,
                'hotel_makkah_id' => $validated['hotel_makkah_id'] ?? null,
                'hotel_madinah_id' => $validated['hotel_madinah_id'] ?? null,
                'has_include_override' => !empty($validated['has_include_override']),
                'has_exclude_override' => !empty($validated['has_exclude_override']),
            ];

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

            // Handle main photo
            if ($request->hasFile('main_photo')) {
                if ($variant->main_photo && Storage::disk('public')->exists($variant->main_photo)) {
                    Storage::disk('public')->delete($variant->main_photo);
                }
                $data['main_photo'] = $request->file('main_photo')->store('packages/variants/photos', 'public');
            }

            $variant->update($data);

            // Sync airlines
            $syncData = [];
            if (!empty($validated['airline_ids'])) {
                foreach ($validated['airline_ids'] as $order => $airlineId) {
                    $syncData[$airlineId] = ['sort_order' => $order];
                }
            }
            $variant->airlines()->sync($syncData);

            // Sync pricing matrix
            $pricesInput = $validated['prices'] ?? [];
            if (!empty($pricesInput)) {
                $existingPrices = $variant->prices()->get()->keyBy('room_type');
                $sortOrder = 0;

                foreach ($pricesInput as $priceData) {
                    $roomType = $priceData['room_type'];
                    $isActive = !empty($priceData['is_active']) && $priceData['is_active'] != '0';
                    $normalPrice = $priceData['normal_price'] ?? null;
                    $hasPromo = !empty($priceData['has_promo']) && $priceData['has_promo'] != '0';
                    $promoPrice = ($hasPromo && !empty($priceData['promo_price']) && (float) $priceData['promo_price'] > 0) ? (float) $priceData['promo_price'] : null;

                    if ($existingPrices->has($roomType)) {
                        $existingPrices->get($roomType)->update([
                            'normal_price' => ($normalPrice !== null && $normalPrice !== '') ? (float) $normalPrice : 0,
                            'promo_price' => $promoPrice,
                            'is_active' => $isActive,
                            'sort_order' => $sortOrder++,
                        ]);
                    } else {
                        $variant->prices()->create([
                            'room_type' => $roomType,
                            'normal_price' => (float) ($normalPrice ?? 0),
                            'promo_price' => $promoPrice,
                            'is_active' => $isActive,
                            'sort_order' => $sortOrder++,
                        ]);
                    }
                }
            }

            // Sync override includes
            $variant->overrideIncludes()->delete();
            if (!empty($validated['has_include_override']) && !empty($validated['override_includes'])) {
                $filteredIncludes = array_values(array_filter($validated['override_includes'], fn($item) => !is_null($item) && trim($item) !== ''));
                $sortOrder = 0;
                foreach ($filteredIncludes as $item) {
                    $variant->overrideIncludes()->create(['item' => trim($item), 'sort_order' => $sortOrder++]);
                }
            }

            // Sync override excludes
            $variant->overrideExcludes()->delete();
            if (!empty($validated['has_exclude_override']) && !empty($validated['override_excludes'])) {
                $filteredExcludes = array_values(array_filter($validated['override_excludes'], fn($item) => !is_null($item) && trim($item) !== ''));
                $sortOrder = 0;
                foreach ($filteredExcludes as $item) {
                    $variant->overrideExcludes()->create(['item' => trim($item), 'sort_order' => $sortOrder++]);
                }
            }
        });

        return redirect()->route('admin.packages.show', $package)
            ->with('success', "Sub-paket '{$validated['name']}' berhasil diperbarui.");
    }

    public function destroy(Package $package, PackageVariant $variant)
    {
        if ($variant->registrations()->count() > 0) {
            return back()->with('error', 'Sub-paket tidak dapat dihapus karena sudah memiliki data pendaftaran jamaah.');
        }

        // Delete physical files
        if ($variant->main_photo && Storage::disk('public')->exists($variant->main_photo)) {
            Storage::disk('public')->delete($variant->main_photo);
        }

        $variant->delete();

        return redirect()->route('admin.packages.show', $package)
            ->with('success', "Sub-paket '{$variant->name}' berhasil dihapus.");
    }
}
