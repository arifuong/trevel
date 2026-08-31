<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Rules per PRD Section 9 & 6.9:
     * Nama paket, harga, tanggal keberangkatan, durasi, fasilitas, kuota kursi, status
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'departure_date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'min:1'],
            'facilities' => ['required', 'string'],
            'quota' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:aktif,sold_out'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama paket wajib diisi.',
            'price.required' => 'Harga paket wajib diisi.',
            'price.numeric' => 'Harga paket harus berupa angka.',
            'price.min' => 'Harga paket tidak boleh bernilai negatif.',
            'departure_date.required' => 'Tanggal keberangkatan wajib diisi.',
            'departure_date.date' => 'Format tanggal keberangkatan tidak valid.',
            'duration.required' => 'Durasi perjalanan wajib diisi.',
            'duration.integer' => 'Durasi perjalanan harus berupa angka (hari).',
            'facilities.required' => 'Fasilitas paket wajib diisi.',
            'quota.required' => 'Jumlah kuota kursi wajib diisi.',
            'quota.integer' => 'Jumlah kuota kursi harus berupa angka bulat.',
            'quota.min' => 'Jumlah kuota kursi tidak boleh negatif.',
            'status.required' => 'Status paket wajib dipilih.',
            'status.in' => 'Status paket harus bernilai Aktif atau Sold Out.',
        ];
    }
}
