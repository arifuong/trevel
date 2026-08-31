<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVerificationHistory extends Model
{
    /**
     * Audit trail untuk setiap perubahan status dokumen anggota jamaah.
     * Mencatat status, alasan (jika ditolak), dan admin yang melakukan aksi.
     */

    public $timestamps = false;

    protected $fillable = [
        'registration_member_id',
        'status',
        'reason',
        'action_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * History ini milik satu anggota jamaah (registration member).
     */
    public function registrationMember(): BelongsTo
    {
        return $this->belongsTo(RegistrationMember::class);
    }

    /**
     * User (admin/jamaah) yang melakukan aksi perubahan status.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
