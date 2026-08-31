<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isJamaah();
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1000000'],
            'proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal pembayaran DP wajib diisi.',
            'amount.numeric' => 'Nominal pembayaran harus berupa angka.',
            'amount.min' => 'Nominal pembayaran DP minimal Rp 1.000.000.',
            'proof_file.required' => 'Foto atau scan bukti transfer wajib diunggah.',
            'proof_file.mimes' => 'Format bukti transfer harus berupa JPG, JPEG, PNG, atau WEBP.',
            'proof_file.max' => 'Ukuran file bukti transfer maksimal 10 MB.',
        ];
    }
}
