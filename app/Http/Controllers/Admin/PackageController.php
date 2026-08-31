<?php

namespace App\Http\Controllers\Admin;

use App\Models\Package;
use App\Models\Registration;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        // Search by name, filter by status
        $query = Package::withCount('variants')
            ->with(['variants' => function($q) {
                $q->where('status', 'aktif')->with(['prices' => function($q2) {
                    $q2->where('is_active', true);
                }]);
            }])
            ->latest();

        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $packages = $query->paginate(10)->withQueryString();

        $totalPackages = Package::count();
        $activePackages = Package::where('status', 'aktif')->count();
        $nonactivePackages = Package::where('status', '!=', 'aktif')->count();

        return view('admin.packages.index', compact(
            'packages', 'totalPackages', 'activePackages', 'nonactivePackages'
        ));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'departure_date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'main_photo.image' => 'Foto utama harus berupa file gambar.',
            'main_photo.mimes' => 'Format file tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'main_photo.max' => 'Ukuran foto utama maksimal 5MB.',
        ]);

        // Generate unique slug
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;

        // Set legacy fields to satisfy NOT NULL constraints
        $validated['price'] = 0;
        $validated['facilities'] = '-';
        $validated['quota'] = 0;

        // Handle main photo upload
        if ($request->hasFile('main_photo')) {
            $validated['main_photo'] = $request->file('main_photo')->store('packages/photos', 'public');
        }

        $package = Package::create($validated);

        return redirect()->route('admin.packages.show', $package)
            ->with('success', 'Paket berhasil dibuat. Tambahkan sub-paket (VIP/Bisnis/Ekonomi) untuk melengkapi.');
    }

    public function show(Package $package)
    {
        $package->load([
            'variants' => function($q) {
                $q->orderBy('sort_order')->with([
                    'prices' => fn($q2) => $q2->orderBy('sort_order'),
                    'hotelPhotos' => fn($q4) => $q4->orderBy('sort_order'),
                ]);
            }
        ]);

        return view('admin.packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'departure_date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'main_photo.image' => 'Foto utama harus berupa file gambar.',
            'main_photo.mimes' => 'Format file tidak didukung. Silakan upload JPG, JPEG, PNG, atau WEBP.',
            'main_photo.max' => 'Ukuran foto utama maksimal 5MB.',
        ]);

        // Update slug if name changed
        if ($package->name !== $validated['name']) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Package::where('slug', $slug)->where('id', '!=', $package->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Handle file uploads
        if ($request->hasFile('main_photo')) {
            // Delete old photo
            if ($package->main_photo && Storage::disk('public')->exists($package->main_photo)) {
                Storage::disk('public')->delete($package->main_photo);
            }
            $validated['main_photo'] = $request->file('main_photo')->store('packages/photos', 'public');
        }

        $package->update($validated);

        return redirect()->route('admin.packages.show', $package)
            ->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Upload photo utama paket secara independen tanpa me-reset form atau mengubah field lainnya.
     */
    public function uploadPhoto(Request $request, Package $package)
    {
        $request->validate([
            'main_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'main_photo.required' => 'File foto wajib diunggah.',
            'main_photo.image' => 'File harus berupa gambar.',
            'main_photo.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'main_photo.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        if ($package->main_photo && Storage::disk('public')->exists($package->main_photo)) {
            Storage::disk('public')->delete($package->main_photo);
        }

        $path = $request->file('main_photo')->store('packages/photos', 'public');
        $package->update(['main_photo' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Foto utama berhasil diperbarui.',
            'photo_url' => Storage::url($path),
        ]);
    }

    /**
     * Hapus paket umroh beserta seluruh varian dan fotonya secara aman menggunakan PackageDeletionService.
     */
    public function destroy(Package $package, \App\Services\PackageDeletionService $deletionService)
    {
        $result = $deletionService->deletePackage($package, auth()->id());

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', $result['message']);
    }
}
