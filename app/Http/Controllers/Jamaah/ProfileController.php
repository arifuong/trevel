<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct(
        protected ImageUploadService $imageUploadService
    ) {}

    /**
     * Tampilkan halaman profil jamaah.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return view('jamaah.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Perbarui data informasi pribadi jamaah.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(08|628)[0-9]{8,12}$/', 'unique:users,phone,' . $user->id],
            'gender' => ['nullable', 'in:laki-laki,perempuan'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid (contoh: 08123456789).',
            'phone.unique' => 'Nomor WhatsApp sudah digunakan oleh akun lain.',
            'gender.in' => 'Pilihan jenis kelamin tidak valid.',
            'birth_place.max' => 'Tempat lahir maksimal 100 karakter.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
            'address.max' => 'Alamat maksimal 500 karakter.',
        ]);

        // Normalisasi nomor telepon: 08xxx -> 628xxx
        $phone = $validated['phone'];
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        $validated['phone'] = $phone;

        $user->update($validated);

        return back()->with('success', '✓ Profil berhasil diperbarui.');
    }

    /**
     * Upload / Ganti foto profil jamaah.
     */
    public function updatePhoto(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'avatar.required' => 'Silakan pilih file foto profil yang ingin diunggah.',
            'avatar.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran file foto maksimal 10 MB.',
        ]);

        // Kompresi terpusat & hapus file lama otomatis
        $newPath = $this->imageUploadService->replaceFile(
            oldPath: $user->avatar,
            newFile: $request->file('avatar'),
            directory: 'avatars',
            type: 'photo'
        );

        $user->update([
            'avatar' => $newPath,
        ]);

        return back()->with('success', '✓ Foto profil berhasil diperbarui.');
    }

    /**
     * Hapus foto profil jamaah.
     */
    public function deletePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->avatar) {
            $this->imageUploadService->deleteFile($user->avatar);
            $user->update([
                'avatar' => null,
            ]);
        }

        return back()->with('success', '✓ Foto profil berhasil dihapus.');
    }

    /**
     * Perbarui kata sandi akun jamaah.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.different' => 'Kata sandi baru harus berbeda dengan kata sandi saat ini.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', '✓ Kata sandi berhasil diperbarui.');
    }
}
