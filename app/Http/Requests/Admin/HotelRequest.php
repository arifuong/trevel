<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // WAJIB
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'in:makkah,madinah,Makkah,Madinah'],
            'star_rating' => ['required', 'string', 'max:50'],

            // OPSIONAL (NULLABLE) - Sesuai Revisi
            'distance_to_haram' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],

            // Media & Relasi
            'main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gallery_photos' => ['nullable', 'array'],
            'gallery_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:hotel_facilities,id'],
            'deleted_photo_ids' => ['nullable', 'array'],
            'deleted_photo_ids.*' => ['integer'],
        ];
    }

    /**
     * Custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nama Hotel',
            'city' => 'Kota',
            'star_rating' => 'Rating Bintang',
            'distance_to_haram' => 'Jarak ke Haram / Masjid Nabawi',
            'address' => 'Alamat Lengkap',
            'description' => 'Deskripsi Hotel',
            'main_photo' => 'Foto Utama',
            'gallery_photos' => 'Galeri Foto',
            'facilities' => 'Fasilitas Hotel',
        ];
    }
}
