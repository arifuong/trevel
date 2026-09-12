<?php

namespace App\Services;

use App\Models\DocumentType;
use App\Models\JamaahDocument;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationMember;
use Illuminate\Support\Facades\Log;

class BookingStatusService
{
    /**
     * Evaluasi dan sinkronisasi status booking/pendaftaran.
     *
     * Rules:
     * 1. Jika pendaftaran dibatalkan, jangan ubah status.
     * 2. Jika seluruh pembayaran lunas (remaining_balance <= 0) dan seluruh payment terverifikasi:
     *    - Cek apakah seluruh dokumen wajib phase 'keberangkatan' sudah valid untuk setiap anggota:
     *      * Jika YA -> status berpindah ke 'berangkat' (Siap Berangkat)
     *      * Jika BELUM -> status berpindah ke 'menunggu_kelengkapan_keberangkatan'
     */
    public function evaluateStatus(Registration $registration): string
    {
        if (in_array($registration->status, [Registration::STATUS_DIBATALKAN, Registration::STATUS_SELESAI])) {
            return $registration->status;
        }

        $invoice = $registration->invoice;
        $isFullyPaid = ($invoice && (float) $invoice->remaining_balance <= 0)
            || in_array($registration->status, [
                Registration::STATUS_LUNAS,
                Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
                Registration::STATUS_BERANGKAT,
            ]);

        $hasPendingPayments = $registration->payments()
            ->where('status', Payment::STATUS_MENUNGGU_VERIFIKASI)
            ->exists();

        // Hanya jika sudah lunas dan tidak ada pembayaran pending
        if ($isFullyPaid && !$hasPendingPayments) {
            if ($this->hasCompletedRequiredDepartureDocuments($registration)) {
                if ($registration->status !== Registration::STATUS_BERANGKAT) {
                    $registration->update([
                        'status' => Registration::STATUS_BERANGKAT,
                    ]);
                    Log::info("Booking {$registration->registration_number} status updated to BERANGKAT (Siap Berangkat).");
                }
                return Registration::STATUS_BERANGKAT;
            }

            // Jika belum lengkap dokumen keberangkatannya
            if (in_array($registration->status, [
                Registration::STATUS_LUNAS,
                Registration::STATUS_CICILAN_PELUNASAN,
                Registration::STATUS_JAMAAH,
                Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
                Registration::STATUS_BERANGKAT,
            ])) {
                if ($registration->status !== Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN) {
                    $registration->update([
                        'status' => Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN,
                    ]);
                    Log::info("Booking {$registration->registration_number} status updated to MENUNGGU_KELENGKAPAN_KEBERANGKATAN.");
                }
                return Registration::STATUS_MENUNGGU_KELENGKAPAN_KEBERANGKATAN;
            }
        }

        return $registration->status;
    }

    /**
     * Cek apakah SELURUH dokumen dengan document_types.phase = 'keberangkatan'
     * YANG BERSIFAT WAJIB (is_required = true) sudah berstatus VALID / DISETUJUI
     * untuk SETIAP jamaah/anggota aktif dalam booking tersebut.
     */
    public function hasCompletedRequiredDepartureDocuments(Registration $registration): bool
    {
        $members = $registration->members;
        if ($members->isEmpty()) {
            return false;
        }

        $requiredDepartureDocTypes = DocumentType::where('phase', DocumentType::PHASE_KEBERANGKATAN)
            ->where('is_required', true)
            ->get();

        if ($requiredDepartureDocTypes->isEmpty()) {
            return true;
        }

        foreach ($members as $member) {
            foreach ($requiredDepartureDocTypes as $docType) {
                $doc = JamaahDocument::where('registration_member_id', $member->id)
                    ->where('document_type_id', $docType->id)
                    ->first();

                // Dokumen harus ada dan berstatus 'valid' atau 'disetujui'
                if (!$doc || !$doc->isValid()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Ambil rincian status dokumen keberangkatan untuk suatu pendaftaran.
     * Mengembalikan array statistik dan daftar checklist per member.
     */
    public function getDepartureDocumentsSummary(Registration $registration): array
    {
        $departureDocTypes = DocumentType::where('phase', DocumentType::PHASE_KEBERANGKATAN)->get();
        $members = $registration->members()->with(['documents.documentType'])->get();

        $totalRequired = 0;
        $totalValid = 0;
        $totalPending = 0;
        $totalMissing = 0;
        $totalRejected = 0;

        $membersData = [];

        foreach ($members as $member) {
            $memberDocs = [];
            foreach ($departureDocTypes as $docType) {
                $doc = $member->documents->first(fn($d) => $d->document_type_id === $docType->id);
                $isRequired = $docType->is_required;

                if ($isRequired) {
                    $totalRequired++;
                }

                $status = 'belum_unggah';
                if ($doc) {
                    if ($doc->isValid()) {
                        $status = 'valid';
                        if ($isRequired) $totalValid++;
                    } elseif ($doc->isRejected()) {
                        $status = 'ditolak';
                        if ($isRequired) $totalRejected++;
                    } else {
                        $status = 'menunggu_verifikasi';
                        if ($isRequired) $totalPending++;
                    }
                } else {
                    if ($isRequired) $totalMissing++;
                }

                $memberDocs[] = [
                    'doc_type' => $docType,
                    'document' => $doc,
                    'status' => $status,
                ];
            }

            $membersData[] = [
                'member' => $member,
                'docs' => $memberDocs,
            ];
        }

        return [
            'departure_doc_types' => $departureDocTypes,
            'members_data' => $membersData,
            'total_required' => $totalRequired,
            'total_valid' => $totalValid,
            'total_pending' => $totalPending,
            'total_missing' => $totalMissing,
            'total_rejected' => $totalRejected,
            'is_all_valid' => $totalRequired > 0 && ($totalValid === $totalRequired),
            'is_complete' => $totalRequired > 0 && ($totalValid === $totalRequired),
        ];
    }
}
