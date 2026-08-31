<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Services\OcrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OcrController extends Controller
{
    public function __construct(
        protected OcrService $ocrService
    ) {}

    /**
     * Endpoint API untuk memparsing teks dokumen hasil scan OCR.
     */
    public function parse(Request $request): JsonResponse
    {
        $request->validate([
            'doc_type' => ['required', 'in:ktp,kk,passport'],
            'raw_text' => ['required', 'string'],
        ]);

        $type = $request->input('doc_type');
        $rawText = $request->input('raw_text');

        $data = match ($type) {
            'ktp' => $this->ocrService->parseKtp($rawText),
            'kk' => $this->ocrService->parseKk($rawText),
            'passport' => $this->ocrService->parsePassport($rawText),
            default => ['raw_text' => $rawText],
        };

        return response()->json([
            'success' => true,
            'type' => $type,
            'data' => $data,
        ]);
    }
}
