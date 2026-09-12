<?php

namespace App\Http\Requests;

use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isJamaah();
    }

    /**
     * PRD Section 6.2, 6.3, 6.4, 9 Data Requirements:
     * - NIK + KTP
     * - No. KK + KK
     * - No. Paspor + Paspor
     * - Hubungan keluarga
     * - Buku Nikah (wajib jika suami/istri)
     * - Akta Lahir (wajib jika anak)
     */
    public function rules(): array
    {
        return [
            'package_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('packages', 'id')->where(function ($query) {
                    $query->where('status', 'aktif');
                }),
            ],
            'package_variant_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('package_variants', 'id')->where(function ($query) {
                    $query->where('status', 'aktif');
                }),
            ],
            'room_type' => ['nullable', 'string', 'in:quad,triple,double'],
            'members' => ['required', 'array', 'min:1'],
            'members.*.name' => ['required', 'string', 'max:255'],
            'members.*.birth_place' => ['required', 'string', 'max:100'],
            'members.*.birth_date' => ['required', 'date'],
            'members.*.gender' => ['required', 'in:laki-laki,perempuan'],
            'members.*.nik' => ['required', 'string', 'digits:16', 'regex:/^[0-9]{16}$/'],
            'members.*.address' => ['required', 'string', 'max:1000'],
            'members.*.ktp_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'members.*.no_kk' => ['required', 'string', 'digits:16', 'regex:/^[0-9]{16}$/'],
            'members.*.kk_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'members.*.no_passport' => ['nullable', 'string', 'max:50'],
            'members.*.passport_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'members.*.relationship' => ['required', 'in:diri_sendiri,suami,istri,anak,orang_tua,saudara'],
            'members.*.marriage_book_file' => [
                'nullable',
                'required_if:members.*.relationship,suami,istri',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
            'members.*.birth_certificate_file' => [
                'nullable',
                'required_if:members.*.relationship,anak',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'package_id.required' => 'Silakan pilih paket umrah/haji yang ingin didaftarkan.',
            'package_id.exists' => 'Paket yang Anda pilih tidak tersedia atau sedang tidak aktif.',
            'package_variant_id.exists' => 'Sub-paket yang dipilih tidak tersedia atau sedang tidak aktif.',
            'members.required' => 'Minimal 1 calon jamaah harus didaftarkan.',
            'members.min' => 'Minimal 1 calon jamaah harus didaftarkan.',
            'members.*.name.required' => 'Nama lengkap jamaah wajib diisi sesuai KTP.',
            'members.*.birth_place.required' => 'Tempat lahir wajib diisi sesuai KTP.',
            'members.*.birth_date.required' => 'Tanggal lahir wajib diisi sesuai KTP.',
            'members.*.birth_date.date' => 'Format tanggal lahir tidak valid.',
            'members.*.gender.required' => 'Jenis kelamin wajib dipilih sesuai KTP.',
            'members.*.gender.in' => 'Pilihan jenis kelamin harus Laki-laki atau Perempuan sesuai KTP.',
            'members.*.nik.required' => 'NIK wajib diisi sesuai KTP.',
            'members.*.nik.digits' => 'NIK harus berupa tepat 16 angka (0–9).',
            'members.*.nik.regex' => 'NIK hanya boleh berisi angka (0–9) tanpa huruf, spasi, atau simbol.',
            'members.*.address.required' => 'Alamat tinggal wajib diisi persis sesuai KTP.',
            'members.*.ktp_file.required' => 'Foto / Scan KTP wajib diunggah.',
            'members.*.ktp_file.mimes' => 'Format file KTP harus berupa JPG, JPEG, PNG, atau WEBP.',
            'members.*.ktp_file.max' => 'Ukuran file KTP maksimal 10 MB.',
            'members.*.no_kk.required' => 'Nomor Kartu Keluarga (KK) wajib diisi.',
            'members.*.no_kk.digits' => 'Nomor Kartu Keluarga (KK) harus berupa tepat 16 angka (0–9).',
            'members.*.no_kk.regex' => 'Nomor Kartu Keluarga (KK) hanya boleh berisi angka (0–9) tanpa huruf, spasi, atau simbol.',
            'members.*.kk_file.required' => 'Foto / Scan KK wajib diunggah.',
            'members.*.kk_file.mimes' => 'Format file KK harus berupa JPG, JPEG, PNG, atau WEBP.',
            'members.*.kk_file.max' => 'Ukuran file KK maksimal 10 MB.',
            'members.*.passport_file.mimes' => 'Format file Paspor harus berupa JPG, JPEG, PNG, atau WEBP.',
            'members.*.passport_file.max' => 'Ukuran file Paspor maksimal 10 MB.',
            'members.*.relationship.required' => 'Hubungan keluarga wajib dipilih.',
            'members.*.relationship.in' => 'Hubungan keluarga tidak valid.',
            'members.*.marriage_book_file.required_if' => 'Scan Buku Nikah wajib diunggah untuk pendaftaran suami/istri.',
            'members.*.marriage_book_file.mimes' => 'Format file Buku Nikah harus berupa JPG, JPEG, PNG, atau WEBP.',
            'members.*.marriage_book_file.max' => 'Ukuran file Buku Nikah maksimal 10 MB.',
            'members.*.birth_certificate_file.required_if' => 'Scan Akta Kelahiran wajib diunggah untuk pendaftaran anak.',
            'members.*.birth_certificate_file.mimes' => 'Format file Akta Kelahiran harus berupa JPG, JPEG, PNG, atau WEBP.',
            'members.*.birth_certificate_file.max' => 'Ukuran file Akta Kelahiran maksimal 10 MB.',
        ];
    }

    /**
     * Custom validation rules:
     * Cek apakah paket masih aktif & kuota cukup, serta pastikan user belum memiliki registrasi aktif,
     * dan validasi ketat apakah varian dan tipe kamar yang dipilih benar-benar aktif di database.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            if ($user && $user->hasActiveRegistration()) {
                $validator->errors()->add('registration', 'Anda sudah memiliki 1 pendaftaran yang sedang aktif. Satu akun hanya boleh memiliki 1 pendaftaran aktif pada satu waktu.');
            }

            if ($packageId = $this->input('package_id')) {
                $package = Package::with(['variants.prices'])->find($packageId);
                if ($package) {
                    if ($package->status !== 'aktif') {
                        $validator->errors()->add('package_id', 'Paket yang Anda pilih sedang tidak aktif.');
                        return;
                    }

                    $memberCount = count($this->input('members', []));
                    $packageRemaining = $package->getRemainingQuota();

                    if ($packageRemaining <= 0) {
                        $validator->errors()->add('package_id', 'Paket yang Anda pilih sudah Sold Out / kuota kursi telah habis.');
                    } elseif ($packageRemaining < $memberCount) {
                        $validator->errors()->add('package_id', "Sisa kuota paket ({$packageRemaining} kursi) tidak mencukupi untuk mendaftarkan {$memberCount} jamaah.");
                    }

                    $variantId = $this->input('package_variant_id');
                    $roomType = $this->input('room_type');

                    if ($variantId) {
                        $variant = $package->variants->firstWhere('id', $variantId);
                        if (!$variant || $variant->status !== 'aktif') {
                            $validator->errors()->add('package_variant_id', 'Sub-paket yang dipilih sedang tidak aktif atau tidak valid.');
                        } else {
                            $variantRemaining = $variant->getRemainingQuota();

                            if ($variantRemaining <= 0) {
                                $validator->errors()->add('package_variant_id', 'Sub-paket yang dipilih sedang tidak aktif atau kuota kursi telah habis.');
                            } elseif ($variantRemaining < $memberCount) {
                                $validator->errors()->add('package_variant_id', "Sisa kuota sub-paket ({$variantRemaining} kursi) tidak mencukupi untuk mendaftarkan {$memberCount} jamaah.");
                            }

                            if ($roomType) {
                                $priceObj = $variant->prices
                                    ->where('room_type', $roomType)
                                    ->where('is_active', true)
                                    ->first(fn($p) => (float) $p->normal_price > 0);

                                if (!$priceObj) {
                                    $validator->errors()->add('room_type', 'Tipe kamar yang dipilih tidak tersedia atau sedang dinonaktifkan untuk sub-paket ini.');
                                }
                            }
                        }
                    } elseif ($package->variants()->where('status', 'aktif')->exists()) {
                        $validator->errors()->add('package_variant_id', 'Silakan pilih salah satu sub-paket yang tersedia.');
                    }
                }
            }
        });
    }
}
