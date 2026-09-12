<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\YouTubeHelper;
use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

    /**
     * Tampilkan daftar seluruh item galeri media
     */
    public function index(Request $request)
    {
        $typeFilter = $request->query('type');
        $heroFilter = $request->query('hero');
        $search = $request->query('q');

        $query = Gallery::latest();

        if ($typeFilter && in_array($typeFilter, ['photo', 'video'])) {
            $query->where('type', $typeFilter);
        }

        if ($heroFilter === '1') {
            $query->where('is_profile_hero', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        $galleries = $query->paginate(12)->withQueryString();

        return view('admin.galleries.index', compact('galleries', 'typeFilter', 'heroFilter', 'search'));
    }

    /**
     * Tampilkan formulir tambah item galeri baru
     */
    public function create()
    {
        return view('admin.galleries.create');
    }

    /**
     * Simpan item galeri baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'type'            => ['required', 'in:photo,video'],
            'is_profile_hero' => ['nullable', 'boolean'],
            'image'           => ['required_if:type,photo', 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'video_url'       => ['required_if:type,video', 'nullable', 'string', 'max:500'],
            'video_thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'caption'         => ['nullable', 'string', 'max:1000'],
            'sort_order'      => ['nullable', 'integer'],
            'is_active'       => ['nullable', 'boolean'],
        ], [
            'title.required'        => 'Judul dokumentasi wajib diisi.',
            'image.required_if'     => 'File foto wajib diunggah untuk item bertipe Foto.',
            'image.max'             => 'Ukuran foto maksimal 5MB.',
            'video_url.required_if' => 'Link video YouTube wajib diisi untuk item bertipe Video.',
        ]);

        $imagePath = null;

        if ($request->input('type') === 'video') {
            $extractedId = YouTubeHelper::extractVideoId($validated['video_url'] ?? '');
            if (!$extractedId) {
                return back()
                    ->withInput()
                    ->withErrors(['video_url' => 'Format URL YouTube tidak dikenali. Gunakan format https://youtu.be/ID atau https://www.youtube.com/watch?v=ID.']);
            }

            // Jika ada upload thumbnail kustom untuk video
            if ($request->hasFile('video_thumbnail')) {
                $imagePath = $this->imageUploadService->uploadPublicPhoto($request->file('video_thumbnail'), 'galleries');
            }
        } else {
            // Tipe Foto
            if ($request->hasFile('image')) {
                $imagePath = $this->imageUploadService->uploadPublicPhoto($request->file('image'), 'galleries');
            }
        }

        DB::transaction(function () use ($request, $validated, $imagePath) {
            $isProfileHero = $request->boolean('is_profile_hero');
            $type = $validated['type'];

            // Aturan ketat: Maksimal 1 item aktif per tipe untuk Hero Profil
            // Jika toggle diaktifkan, otomatis nonaktifkan item lama bertipe sama
            if ($isProfileHero) {
                Gallery::where('type', $type)
                    ->where('is_profile_hero', true)
                    ->update(['is_profile_hero' => false]);
            }

            Gallery::create([
                'title'           => $validated['title'],
                'type'            => $type,
                'is_profile_hero' => $isProfileHero,
                'image_path'      => $imagePath,
                'video_url'       => $type === 'video' ? $validated['video_url'] : null,
                'caption'         => $validated['caption'] ?? null,
                'sort_order'      => (int) ($validated['sort_order'] ?? 0),
                'is_active'       => $request->boolean('is_active', true),
            ]);
        });

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Item galeri media berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit item galeri
     */
    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    /**
     * Perbarui item galeri yang ada
     */
    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'type'            => ['required', 'in:photo,video'],
            'is_profile_hero' => ['nullable', 'boolean'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'video_url'       => ['required_if:type,video', 'nullable', 'string', 'max:500'],
            'video_thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'caption'         => ['nullable', 'string', 'max:1000'],
            'sort_order'      => ['nullable', 'integer'],
            'is_active'       => ['nullable', 'boolean'],
        ]);

        $imagePath = $gallery->image_path;

        if ($request->input('type') === 'video') {
            $extractedId = YouTubeHelper::extractVideoId($validated['video_url'] ?? '');
            if (!$extractedId) {
                return back()
                    ->withInput()
                    ->withErrors(['video_url' => 'Format URL YouTube tidak dikenali. Gunakan format https://youtu.be/ID atau https://www.youtube.com/watch?v=ID.']);
            }

            if ($request->boolean('remove_thumbnail')) {
                if ($gallery->image_path) {
                    $this->imageUploadService->deleteFile($gallery->image_path);
                }
                $imagePath = null;
            } elseif ($request->hasFile('video_thumbnail')) {
                $imagePath = $this->imageUploadService->replaceFile($gallery->image_path, $request->file('video_thumbnail'), 'galleries', 'public_photo');
            }
        } else {
            // Tipe foto
            if ($request->hasFile('image')) {
                $imagePath = $this->imageUploadService->replaceFile($gallery->image_path, $request->file('image'), 'galleries', 'public_photo');
            }
        }

        DB::transaction(function () use ($request, $validated, $gallery, $imagePath) {
            $isProfileHero = $request->boolean('is_profile_hero');
            $type = $validated['type'];

            // Aturan ketat: Maksimal 1 item aktif per tipe untuk Hero Profil
            // Jika toggle diaktifkan, otomatis nonaktifkan item lama bertipe sama (kecuali item ini sendiri)
            if ($isProfileHero) {
                Gallery::where('type', $type)
                    ->where('id', '!=', $gallery->id)
                    ->where('is_profile_hero', true)
                    ->update(['is_profile_hero' => false]);
            }

            $gallery->update([
                'title'           => $validated['title'],
                'type'            => $type,
                'is_profile_hero' => $isProfileHero,
                'image_path'      => $imagePath,
                'video_url'       => $type === 'video' ? $validated['video_url'] : null,
                'caption'         => $validated['caption'] ?? null,
                'sort_order'      => (int) ($validated['sort_order'] ?? 0),
                'is_active'       => $request->boolean('is_active', true),
            ]);
        });

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Item galeri media berhasil diperbarui.');
    }

    /**
     * Hapus item galeri
     */
    public function destroy(Gallery $gallery)
    {
        if ($gallery->image_path) {
            $this->imageUploadService->deleteFile($gallery->image_path);
        }

        $gallery->delete();

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Item galeri berhasil dihapus.');
    }
}
