<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hotel;
use App\Models\HotelPhoto;
use App\Models\HotelFacility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HotelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::withCount(['variantsAsMakkah', 'variantsAsMadinah'])
            ->with('facilities')
            ->latest();

        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($city = $request->city) {
            $query->where('city', $city);
        }

        $hotels = $query->paginate(10)->withQueryString();

        return view('admin.hotels.index', compact('hotels'));
    }

    public function create()
    {
        $facilities = HotelFacility::orderBy('sort_order')->get();
        return view('admin.hotels.create', compact('facilities'));
    }

    public function store(HotelRequest $request)
    {
        $validated = $request->validated();
        $validated['city'] = strtolower($validated['city']);

        $hotel = DB::transaction(function () use ($request, $validated) {
            // Upload main photo
            if ($request->hasFile('main_photo')) {
                $validated['main_photo'] = $request->file('main_photo')->store('hotels/photos', 'public');
            }

            $hotel = Hotel::create(collect($validated)->except(['gallery_photos', 'facilities'])->toArray());

            // Upload gallery photos
            if ($request->hasFile('gallery_photos')) {
                $order = 1;
                foreach ($request->file('gallery_photos') as $photoFile) {
                    $path = $photoFile->store('hotels/photos', 'public');
                    $hotel->photos()->create([
                        'photo_path' => $path,
                        'category' => 'gallery',
                        'sort_order' => $order++,
                    ]);
                }
            }

            // Sync facilities
            if (!empty($validated['facilities'])) {
                $hotel->facilities()->sync($validated['facilities']);
            }

            return $hotel;
        });

        return redirect()->route('admin.hotels.index')
            ->with('success', "Hotel '{$hotel->name}' berhasil ditambahkan.");
    }

    public function edit(Hotel $hotel)
    {
        $hotel->load(['photos', 'facilities']);
        $facilities = HotelFacility::orderBy('sort_order')->get();
        $selectedFacilities = $hotel->facilities->pluck('id')->toArray();

        return view('admin.hotels.edit', compact('hotel', 'facilities', 'selectedFacilities'));
    }

    public function update(HotelRequest $request, Hotel $hotel)
    {
        $validated = $request->validated();
        $validated['city'] = strtolower($validated['city']);

        DB::transaction(function () use ($request, $hotel, $validated) {
            // Handle main photo
            if ($request->hasFile('main_photo')) {
                if ($hotel->main_photo && Storage::disk('public')->exists($hotel->main_photo)) {
                    Storage::disk('public')->delete($hotel->main_photo);
                }
                $validated['main_photo'] = $request->file('main_photo')->store('hotels/photos', 'public');
            }

            $hotel->update(collect($validated)->except(['gallery_photos', 'facilities', 'deleted_photo_ids'])->toArray());

            // Delete marked photos
            if (!empty($validated['deleted_photo_ids'])) {
                $photosToDelete = $hotel->photos()->whereIn('id', $validated['deleted_photo_ids'])->get();
                foreach ($photosToDelete as $photo) {
                    if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
                        Storage::disk('public')->delete($photo->photo_path);
                    }
                    $photo->delete();
                }
            }

            // Upload new gallery photos
            if ($request->hasFile('gallery_photos')) {
                $order = ($hotel->photos()->max('sort_order') ?? 0) + 1;
                foreach ($request->file('gallery_photos') as $photoFile) {
                    $path = $photoFile->store('hotels/photos', 'public');
                    $hotel->photos()->create([
                        'photo_path' => $path,
                        'category' => 'gallery',
                        'sort_order' => $order++,
                    ]);
                }
            }

            // Sync facilities
            $hotel->facilities()->sync($validated['facilities'] ?? []);
        });

        return redirect()->route('admin.hotels.index')
            ->with('success', "Hotel '{$hotel->name}' berhasil diperbarui.");
    }

    public function destroy(Hotel $hotel)
    {
        // Protect hotels in use by active variants
        if ($hotel->is_in_use) {
            return back()->with('error', 'Hotel tidak dapat dihapus karena sedang digunakan oleh sub-paket aktif.');
        }

        // Delete physical files
        if ($hotel->main_photo && Storage::disk('public')->exists($hotel->main_photo)) {
            Storage::disk('public')->delete($hotel->main_photo);
        }
        foreach ($hotel->photos as $photo) {
            if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }
        }

        $hotel->delete();

        return redirect()->route('admin.hotels.index')
            ->with('success', "Hotel '{$hotel->name}' berhasil dihapus.");
    }

    /**
     * API endpoint for autocomplete search in variant forms.
     */
    public function search(Request $request)
    {
        $query = Hotel::active();

        if ($city = $request->city) {
            $query->where('city', $city);
        }
        if ($search = $request->q) {
            $query->where('name', 'like', "%{$search}%");
        }

        $hotels = $query->with(['photos', 'facilities'])
            ->limit(10)
            ->get()
            ->map(function ($hotel) {
                return [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'city' => $hotel->city,
                    'city_label' => $hotel->city_label,
                    'star_rating' => $hotel->star_rating,
                    'description' => $hotel->description,
                    'main_photo' => $hotel->main_photo ? Storage::url($hotel->main_photo) : null,
                    'distance_to_haram' => $hotel->distance_to_haram,
                    'photos' => $hotel->photos->map(fn($p) => [
                        'id' => $p->id,
                        'url' => Storage::url($p->photo_path),
                        'category' => $p->category,
                    ]),
                    'facilities' => $hotel->facilities->map(fn($f) => [
                        'id' => $f->id,
                        'name' => $f->name,
                    ]),
                ];
            });

        return response()->json($hotels);
    }
}
