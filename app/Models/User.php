<?php

namespace App\Models;

use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * PRD Section 9 - Data Akun & Profil Jamaah:
     * Nama sesuai KTP, Email, No. HP, Password, Foto Profil,
     * Jenis Kelamin, Tempat Lahir, Tanggal Lahir, Alamat
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'phone_formatted',
        'avatar_url',
        'initials',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date:Y-m-d',
            'password' => 'hashed',
        ];
    }

    /**
     * Model boot: Otomatis hapus file avatar fisik dari storage saat user dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            if ($user->avatar) {
                app(ImageUploadService::class)->deleteFile($user->avatar);
            }
        });
    }

    /**
     * Format nomor WhatsApp/HP agar rapi dan mudah dibaca di UI (+62 812-3456-7890).
     */
    protected function phoneFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                $phone = $this->phone;
                if (!$phone) {
                    return '-';
                }

                // Bersihkan karakter non-digit
                $digits = preg_replace('/[^0-9]/', '', $phone);

                // Format 628xxxxxxxx atau 08xxxxxxx
                if (str_starts_with($digits, '62')) {
                    $prefix = '+62 ';
                    $rest = substr($digits, 2);
                } elseif (str_starts_with($digits, '0')) {
                    $prefix = '+62 ';
                    $rest = substr($digits, 1);
                } else {
                    $prefix = '+';
                    $rest = $digits;
                }

                // Pecah menjadi grup 3 atau 4 digit: misal 812-3456-7890
                if (strlen($rest) >= 9) {
                    $part1 = substr($rest, 0, 3);
                    $part2 = substr($rest, 3, 4);
                    $part3 = substr($rest, 7);
                    return $prefix . $part1 . '-' . $part2 . '-' . $part3;
                }

                return $prefix . $rest;
            }
        );
    }

    /**
     * URL publik untuk foto profil / avatar jamaah.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->avatar ? Storage::url($this->avatar) : null
        );
    }

    /**
     * Inisial nama (1-2 huruf kapital).
     */
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: function () {
                $words = explode(' ', trim($this->name ?? 'J'));
                if (count($words) >= 2) {
                    return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                }
                return strtoupper(substr($this->name ?? 'J', 0, 1));
            }
        );
    }

    /**
     * Label Jenis Kelamin ramah pengguna.
     */
    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'laki-laki' => 'Laki-Laki',
            'perempuan' => 'Perempuan',
            default => '-',
        };
    }

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah jamaah.
     */
    public function isJamaah(): bool
    {
        return $this->role === 'jamaah';
    }

    /**
     * Satu user bisa punya banyak pendaftaran.
     * Business Rule: hanya 1 aktif pada satu waktu (divalidasi di logic).
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Dapatkan pendaftaran yang sedang aktif (belum berangkat, belum selesai, dan belum dibatalkan).
     */
    public function activeRegistration(): ?Registration
    {
        return $this->registrations()
            ->whereNotIn('status', ['berangkat', 'selesai', 'dibatalkan'])
            ->latest('id')
            ->first();
    }

    /**
     * Cek apakah user memiliki pendaftaran yang sedang aktif.
     */
    public function hasActiveRegistration(): bool
    {
        return $this->activeRegistration() !== null;
    }
}
