/**
 * PT. ZEIN INTERNASIONAL — Enhanced OCR Document Scanner & Quality Engine
 * ========================================================================
 * High-accuracy OCR scanner for Indonesian e-KTP, Kartu Keluarga (KK), and Passport.
 * Includes:
 * 1. Image Quality Assessment (resolution, blur, lighting/contrast variance)
 * 2. Canvas-based Preprocessing (contrast enhancement, binarization aid)
 * 3. Proximity-based Label Search (NIK, No. KK, Nama, Tempat/Tgl Lahir)
 * 4. Strict 16-Digit Normalization & Validation
 * 5. Cross-Check between KTP and KK
 */

import { createWorker } from 'tesseract.js';

/**
 * Valid Indonesian Province Codes (11 to 94)
 */
const VALID_PROVINCES = [
    '11', '12', '13', '14', '15', '16', '17', '18', '19',
    '21', '31', '32', '33', '34', '35', '36', '51', '52',
    '53', '61', '62', '63', '64', '65', '71', '72', '73',
    '74', '75', '76', '81', '82', '91', '92', '93', '94', '95', '96'
];

/**
 * Assess Image Quality before OCR processing
 * @param {File|Blob|string} imageSource
 * @returns {Promise<{ isAcceptable: boolean, warning: string|null, width: number, height: number }>}
 */
export function assessImageQuality(imageSource) {
    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = () => {
            const width = img.naturalWidth || img.width;
            const height = img.naturalHeight || img.height;

            // 1. Resolution Check
            if (width < 350 || height < 250) {
                return resolve({
                    isAcceptable: false,
                    warning: 'Resolusi foto terlalu kecil. Pastikan foto dokumen diambil dari jarak dekat dan teks terbaca jelas.',
                    width,
                    height,
                });
            }

            // 2. Canvas Brightness & Contrast Variance Check
            try {
                const canvas = document.createElement('canvas');
                const sampleW = 200;
                const sampleH = Math.round((height * sampleW) / width);
                canvas.width = sampleW;
                canvas.height = sampleH;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, sampleW, sampleH);

                const imgData = ctx.getImageData(0, 0, sampleW, sampleH);
                const data = imgData.data;
                let sum = 0;
                let sumSq = 0;
                const totalPixels = data.length / 4;

                for (let i = 0; i < data.length; i += 4) {
                    const gray = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
                    sum += gray;
                    sumSq += gray * gray;
                }

                const mean = sum / totalPixels;
                const variance = (sumSq / totalPixels) - (mean * mean);
                const stdDev = Math.sqrt(Math.max(0, variance));

                // If image is almost completely dark, washed out white, or has no contrast
                if (mean < 35) {
                    return resolve({
                        isAcceptable: false,
                        warning: 'Foto terlalu gelap / kurang pencahayaan. Silakan ambil foto ulang di tempat yang cukup terang.',
                        width,
                        height,
                    });
                }

                if (mean > 240 && stdDev < 15) {
                    return resolve({
                        isAcceptable: false,
                        warning: 'Foto terlalu silau / putih. Pastikan tidak ada pantulan cahaya lampu yang menutupi teks dokumen.',
                        width,
                        height,
                    });
                }

                if (stdDev < 18) {
                    return resolve({
                        isAcceptable: false,
                        warning: 'Foto buram atau kontras terlalu rendah. Pastikan kamera fokus pada teks dokumen.',
                        width,
                        height,
                    });
                }

                return resolve({
                    isAcceptable: true,
                    warning: null,
                    width,
                    height,
                });
            } catch (e) {
                // If canvas reading fails, allow OCR to proceed
                return resolve({ isAcceptable: true, warning: null, width, height });
            }
        };

        img.onerror = () => resolve({ isAcceptable: true, warning: null, width: 0, height: 0 });

        if (imageSource instanceof File || imageSource instanceof Blob) {
            img.src = URL.createObjectURL(imageSource);
        } else {
            img.src = imageSource;
        }
    });
}

/**
 * Preprocess image on canvas (grayscale + contrast stretch + unsharp sharpening) for optimal OCR accuracy
 */
function preprocessImage(imageSource) {
    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const maxDim = 1800;
            let width = img.width;
            let height = img.height;

            if (width > maxDim || height > maxDim) {
                if (width > height) {
                    height = Math.round((height * maxDim) / width);
                    width = maxDim;
                } else {
                    width = Math.round((width * maxDim) / height);
                    height = maxDim;
                }
            }

            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            const imgData = ctx.getImageData(0, 0, width, height);
            const data = imgData.data;

            // 1. Grayscale + High Contrast Enhancement
            const grayArray = new Float32Array(width * height);
            for (let i = 0, p = 0; i < data.length; i += 4, p++) {
                const avg = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
                // Contrast stretch
                const contrast = 1.35;
                const factor = (259 * (contrast + 255)) / (255 * (259 - contrast));
                const enhanced = factor * (avg - 128) + 128;
                const finalVal = Math.min(255, Math.max(0, enhanced));
                grayArray[p] = finalVal;
            }

            // 2. Light unsharp mask sharpening for crisp text digits
            for (let y = 1; y < height - 1; y++) {
                for (let x = 1; x < width - 1; x++) {
                    const idx = y * width + x;
                    const center = grayArray[idx];
                    const up = grayArray[idx - width];
                    const down = grayArray[idx + width];
                    const left = grayArray[idx - 1];
                    const right = grayArray[idx + 1];

                    // Sharpening kernel
                    const sharpVal = center * 2.0 - (up + down + left + right) * 0.25;
                    const clamped = Math.min(255, Math.max(0, sharpVal));

                    const pIdx = idx * 4;
                    data[pIdx] = clamped;
                    data[pIdx + 1] = clamped;
                    data[pIdx + 2] = clamped;
                }
            }

            ctx.putImageData(imgData, 0, 0);
            resolve(canvas.toDataURL('image/jpeg', 0.92));
        };
        img.onerror = () => resolve(imageSource);

        if (imageSource instanceof File || imageSource instanceof Blob) {
            img.src = URL.createObjectURL(imageSource);
        } else {
            img.src = imageSource;
        }
    });
}

/**
 * Worker getter with fallback
 */
async function getWorker(onProgress) {
    try {
        const worker = await createWorker('ind+eng', 1, {
            logger: (m) => {
                if (onProgress && m.status === 'recognizing text') {
                    onProgress(Math.round((m.progress || 0) * 100));
                }
            }
        });
        return worker;
    } catch (e) {
        console.warn('Fallback to eng worker:', e);
        const fallbackWorker = await createWorker('eng', 1, {
            logger: (m) => {
                if (onProgress && m.status === 'recognizing text') {
                    onProgress(Math.round((m.progress || 0) * 100));
                }
            }
        });
        return fallbackWorker;
    }
}

/**
 * Normalize and clean 16-digit numbers from common OCR character confusions
 */
export function normalizeDigits(text) {
    if (!text) return '';
    return text
        .replace(/[OoQqDd]/g, '0')
        .replace(/[IilL|!]/g, '1')
        .replace(/[Zz]/g, '2')
        .replace(/[Ee]/g, '3')
        .replace(/[Aa]/g, '4')
        .replace(/[Ss]/g, '5')
        .replace(/[b]/g, '6')
        .replace(/[Tt]/g, '7')
        .replace(/[B]/g, '8')
        .replace(/[gGq]/g, '9')
        .replace(/[^0-9]/g, '');
}

/**
 * Validate whether a string is a strict 16-digit Indonesian NIK/KK
 */
export function isValid16Digits(numStr) {
    if (!numStr || typeof numStr !== 'string') return false;
    return /^[0-9]{16}$/.test(numStr);
}

/**
 * High-accuracy KTP text parser with label proximity & province verification
 */
export function parseKtp(rawText) {
    const result = {
        nik: '',
        nik_confidence: 'none', // 'high' | 'medium' | 'low' | 'none'
        name: '',
        birth_place: '',
        birth_date: '',
        gender: '',
        address: '',
        raw_text: rawText || '',
    };

    if (!rawText || !rawText.trim()) return result;

    const lines = rawText.split('\n').map((l) => l.trim()).filter(Boolean);

    // 1. NIK Extraction (Proximity based: search line with NIK label and adjacent lines)
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        if (/NIK|N\.?I\.?K|N!K|N\s*I\s*K|N1K/i.test(line)) {
            // Check current line after label
            const valuePart = line.replace(/^.*?(?:NIK|N\.?I\.?K|N!K|N\s*I\s*K|N1K)\s*[:=\s\-]*/i, '');
            const cleaned = normalizeDigits(valuePart);
            
            // Check if exactly 16 digits or contains 16-digit substring
            const match16 = cleaned.match(/[0-9]{16}/);
            if (match16) {
                result.nik = match16[0];
                result.nik_confidence = 'high';
                break;
            }

            // If not found on the same line, check the immediate next line
            if (i + 1 < lines.length) {
                const nextLine = lines[i + 1];
                // Exclude if next line looks like "Nama"
                if (!/nama|neme|tempat|tgl|lahir/i.test(nextLine)) {
                    const cleanedNext = normalizeDigits(nextLine);
                    const matchNext = cleanedNext.match(/[0-9]{16}/);
                    if (matchNext) {
                        result.nik = matchNext[0];
                        result.nik_confidence = 'high';
                        break;
                    }
                }
            }
        }
    }

    // Fallback NIK: Search full text for 16-digit candidate starting with valid province code
    if (!result.nik) {
        // Strip label words first so letters in NIK / PROVINSI don't turn into leading digits
        const strippedText = rawText.replace(/(?:NIK|N\.?I\.?K|N!K|N\s*I\s*K|N1K|NO|NOMOR|KARTU|KELUARGA|PROVINSI|KABUPATEN|KOTA|REPUBLIK|INDONESIA)/gi, ' ');
        const allDigits = normalizeDigits(strippedText);
        
        // Find all 16-digit occurrences
        const candidates = allDigits.match(/[0-9]{16}/g) || [];
        for (const candidate of candidates) {
            const provCode = candidate.substring(0, 2);
            if (VALID_PROVINCES.includes(provCode)) {
                result.nik = candidate;
                result.nik_confidence = 'medium';
                break;
            }
        }

        // Second fallback: any 16-digit sequence
        if (!result.nik && candidates.length > 0) {
            result.nik = candidates[0];
            result.nik_confidence = 'low';
        }
    }

    // 2. Nama Extraction
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        const namaMatch = line.match(/(?:Nama|Neme|Name)\s*[:\s\-]\s*([A-Za-z\s\.,\']+)/i);
        if (namaMatch && namaMatch[1]) {
            let nameVal = namaMatch[1].trim();
            nameVal = nameVal.replace(/^[:\-\s]+/, '').replace(/[^A-Za-z\s\.,\']/g, '').trim();
            if (nameVal.length >= 3 && !/tempat|tgl|lahir|nik|gol|darah|alamat/i.test(nameVal)) {
                result.name = nameVal.toUpperCase();
                break;
            }
        }
    }

    // 3. Tempat & Tanggal Lahir
    for (const line of lines) {
        const ttlMatch = line.match(/(?:Tempat|Tmp|Tgl|Lahir)[^\n\r:]*[:\s\-]+([A-Za-z\s]+)[,\s]+(\d{1,2}[-\/\s\.]\d{1,2}[-\/\s\.]\d{2,4})/i);
        if (ttlMatch) {
            result.birth_place = ttlMatch[1].trim().toUpperCase();
            const rawDate = ttlMatch[2].replace(/[\/\s\.]/g, '-');
            const dateParts = rawDate.split('-');
            if (dateParts.length === 3) {
                let day = dateParts[0].padStart(2, '0');
                let month = dateParts[1].padStart(2, '0');
                let year = dateParts[2];
                if (year.length === 2) year = '19' + year;
                result.birth_date = `${year}-${month}-${day}`;
            }
            break;
        }
    }

    // 4. Jenis Kelamin
    if (/LAKI|LAK!-LAK!|LAKI-LAKI|PRIA/i.test(rawText)) {
        result.gender = 'laki-laki';
    } else if (/PEREMPUAN|WANITA/i.test(rawText)) {
        result.gender = 'perempuan';
    }

    // 5. Alamat
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        const addrMatch = line.match(/(?:Alamat|Alamat\s*[:\s])([^\n\r]+)/i);
        if (addrMatch) {
            let addr = addrMatch[1].replace(/^[:\-\s]+/, '').trim();
            if (addr.length >= 3) {
                result.address = addr.toUpperCase();
            }
            break;
        }
    }

    return result;
}

/**
 * Crop the top portion of an image and apply advanced preprocessing for KK header OCR
 * @param {File|Blob|string} imageSource
 * @param {number} cropPercent - Top percentage to crop (0.35 to 0.40)
 * @param {number} scale - Scaling factor (2.0 to 3.0)
 * @param {'contrast'|'binary'} mode - 'contrast' (contrast + unsharp) or 'binary' (Otsu adaptive threshold)
 * @returns {Promise<string>} Data URL
 */
function cropAndPreprocessHeader(imageSource, cropPercent = 0.38, scale = 2.5, mode = 'contrast') {
    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = () => {
            const origW = img.naturalWidth || img.width;
            const origH = img.naturalHeight || img.height;
            const cropH = Math.max(120, Math.round(origH * cropPercent));

            const canvas = document.createElement('canvas');
            const targetW = Math.round(origW * scale);
            const targetH = Math.round(cropH * scale);

            canvas.width = targetW;
            canvas.height = targetH;
            const ctx = canvas.getContext('2d');

            // Draw cropped top region
            ctx.drawImage(img, 0, 0, origW, cropH, 0, 0, targetW, targetH);

            const imgData = ctx.getImageData(0, 0, targetW, targetH);
            const data = imgData.data;
            const totalPixels = targetW * targetH;

            // 1. Grayscale
            const grays = new Float32Array(totalPixels);
            let sumGray = 0;
            for (let i = 0, p = 0; i < data.length; i += 4, p++) {
                const g = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
                grays[p] = g;
                sumGray += g;
            }

            if (mode === 'binary') {
                // Adaptive thresholding for high text clarity
                const mean = sumGray / totalPixels;
                const thresh = Math.max(65, Math.min(185, mean - 15));

                for (let p = 0, i = 0; p < totalPixels; p++, i += 4) {
                    const val = grays[p] < thresh ? 0 : 255;
                    data[i] = val;
                    data[i + 1] = val;
                    data[i + 2] = val;
                }
            } else {
                // High contrast stretch + unsharp mask
                for (let i = 0, p = 0; i < data.length; i += 4, p++) {
                    const avg = grays[p];
                    const contrast = 1.45;
                    const factor = (259 * (contrast + 255)) / (255 * (259 - contrast));
                    const enhanced = factor * (avg - 128) + 128;
                    const finalVal = Math.min(255, Math.max(0, enhanced));
                    grays[p] = finalVal;
                }

                for (let y = 1; y < targetH - 1; y++) {
                    for (let x = 1; x < targetW - 1; x++) {
                        const idx = y * targetW + x;
                        const center = grays[idx];
                        const up = grays[idx - targetW];
                        const down = grays[idx + targetW];
                        const left = grays[idx - 1];
                        const right = grays[idx + 1];

                        const sharpVal = center * 2.2 - (up + down + left + right) * 0.3;
                        const clamped = Math.min(255, Math.max(0, sharpVal));

                        const pIdx = idx * 4;
                        data[pIdx] = clamped;
                        data[pIdx + 1] = clamped;
                        data[pIdx + 2] = clamped;
                    }
                }
            }

            ctx.putImageData(imgData, 0, 0);
            resolve(canvas.toDataURL('image/png'));
        };
        img.onerror = () => resolve(imageSource);

        if (imageSource instanceof File || imageSource instanceof Blob) {
            img.src = URL.createObjectURL(imageSource);
        } else {
            img.src = imageSource;
        }
    });
}

/**
 * Extract pure digit sequences from text (no letter→digit conversion)
 * Collapses spaces between digits first (e.g. "3204 1234 5678 9012" → "3204123456789012")
 */
function extractCleanDigitSequences(text) {
    if (!text) return [];
    const normalized = text.replace(/(\d)\s+(?=\d)/g, '$1');
    const matches = normalized.match(/\d{16,}/g) || [];
    const results = [];
    for (const m of matches) {
        for (let i = 0; i <= m.length - 16; i++) {
            results.push(m.substring(i, i + 16));
        }
    }
    return results;
}

/**
 * Extract a KK number candidate from a text fragment with limited OCR correction
 */
function extractKkCandidate(text) {
    if (!text) return null;
    // Remove spaces, dots, dashes, commas, colons
    const cleaned = text.replace(/[\s.\-,:]/g, '');

    // Check for pure 16-digit number first
    const pureMatch = cleaned.match(/(\d{16})/);
    if (pureMatch) return pureMatch[1];

    // Limited OCR correction: only common digit-lookalike characters
    const safeReplacements = {
        'O': '0', 'o': '0',
        'I': '1', 'l': '1', '|': '1', '!': '1',
        'S': '5', 's': '5',
        'B': '8',
        'G': '6',
        'Z': '2', 'z': '2',
    };

    // Find substrings that are mostly digits (14-20 chars of digits + confusable chars)
    const chunks = cleaned.match(/[\dOoIl|!SsBGZz]{14,20}/g) || [];
    for (const chunk of chunks) {
        const pureDigitCount = (chunk.match(/\d/g) || []).length;
        if (pureDigitCount >= 10) {
            let corrected = '';
            for (const ch of chunk) {
                corrected += safeReplacements[ch] || ch;
            }
            corrected = corrected.replace(/[^0-9]/g, '');
            if (corrected.length >= 16) {
                return corrected.substring(0, 16);
            }
        }
    }

    return null;
}

/**
 * Advanced Multi-Factor Candidate Scoring Engine for KK Number
 * Evaluates candidate numbers based on proximity to KK labels, header position,
 * spatial coordinates, valid province codes, and penalties for table/NIK/dates.
 */
export function evaluateKkCandidates(ocrDataOrText, passName = 'Pass 1') {
    let lines = [];
    let words = [];
    let fullText = '';

    if (typeof ocrDataOrText === 'string') {
        fullText = ocrDataOrText;
        lines = ocrDataOrText.split('\n').map((l) => ({ text: l.trim(), bbox: null })).filter(l => Boolean(l.text));
    } else if (ocrDataOrText && typeof ocrDataOrText === 'object') {
        fullText = ocrDataOrText.text || '';
        if (Array.isArray(ocrDataOrText.lines)) {
            lines = ocrDataOrText.lines.map(l => ({
                text: (l.text || '').trim(),
                bbox: l.bbox || null,
                confidence: l.confidence || 0,
                words: l.words || []
            })).filter(l => Boolean(l.text));
        } else {
            lines = fullText.split('\n').map((l) => ({ text: l.trim(), bbox: null })).filter(l => Boolean(l.text));
        }
        if (Array.isArray(ocrDataOrText.words)) {
            words = ocrDataOrText.words;
        }
    }

    // 1. NIK Exclusions & Table Boundary Detection
    const nikExclusions = new Set();
    let tableStartLineIndex = lines.length;

    for (let i = 0; i < lines.length; i++) {
        const lText = lines[i].text;

        // Table header detection
        if (/(?:Nama\s+Lengkap|Jenis\s+Kelamin|Tempat\s+Lahir|Tanggal\s+Lahir|Status\s+Perkawinan)/i.test(lText)) {
            if (tableStartLineIndex === lines.length) {
                tableStartLineIndex = i;
            }
        }

        // Line containing NIK label (exclude KK label lines)
        if (/\bNIK\b|N\.?I\.?K\b/i.test(lText) && !/KARTU\s*KELUARGA|KARTU\s*KLUARGA|NO\.?\s*KK|NOMOR\s*KK/i.test(lText)) {
            for (const d of extractCleanDigitSequences(lText)) {
                nikExclusions.add(d);
            }
            if (i + 1 < lines.length) {
                for (const d of extractCleanDigitSequences(lines[i + 1].text)) {
                    nikExclusions.add(d);
                }
            }
        }

        // Table rows pattern (e.g. "1 ROSIDAH 3203066811840009...")
        const nikInline = lText.match(/(?:NIK\s*[:=]\s*)?(\d[\d\s]{14,18}\d)/i);
        if (nikInline && i >= tableStartLineIndex) {
            const cleaned = nikInline[1].replace(/\s/g, '');
            if (cleaned.length === 16) {
                nikExclusions.add(cleaned);
            }
        }
    }

    // 2. Candidate Collection
    const candidates = [];
    const kkLabelPattern = /(?:NO\.?\s*(?:KK|KARTU\s*KELUARGA|KLUARGA)|NOMOR\s*(?:KK|KARTU\s*KELUARGA|KLUARGA))/i;
    const kkTitlePattern = /(?:K[A4]RTU\s*K[E3]?[L1]U?A[R1]?[G6][A4]?|KARTU\s*KLUARGA|KARTU\s*KELUARGA)/i;
    const stopPattern = /\b(?:Nama\s+Kepala|Alamat|NIK|Kepala\s+Keluarga|RT\/RW|Kelurahan|Kecamatan|Kabupaten|Kota|Provinsi)\b/i;

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        const lText = line.text;
        const linePosRatio = lines.length > 0 ? (i / lines.length) : 0;
        const isHeaderRegion = (i < 8) || (linePosRatio < 0.35) || (i < tableStartLineIndex);

        // Pattern A: Line contains specific "NO. KK" / "NOMOR KK"
        if (kkLabelPattern.test(lText)) {
            const valuePart = lText.replace(/^.*?(?:NO\.?\s*(?:KK|KARTU\s*KELUARGA|KLUARGA)|NOMOR\s*(?:KK|KARTU\s*KELUARGA|KLUARGA))\s*[:=\s\-]*/i, '');
            const cand = extractKkCandidate(valuePart);
            if (cand) {
                candidates.push({
                    value: cand,
                    rawPart: valuePart,
                    source: 'same_line_after_kk_label',
                    lineIndex: i,
                    isDirectlyRightOfLabel: true,
                    isHeaderRegion: true,
                    isNearKkLabel: true,
                    confidence: line.confidence || 90
                });
            }

            // Check 1-3 lines below label
            for (let j = 1; j <= 3 && i + j < lines.length; j++) {
                const nextLine = lines[i + j];
                if (stopPattern.test(nextLine.text)) break;
                const nextCand = extractKkCandidate(nextLine.text);
                if (nextCand) {
                    candidates.push({
                        value: nextCand,
                        rawPart: nextLine.text,
                        source: 'line_below_kk_label',
                        lineIndex: i + j,
                        isDirectlyBelowKkLabel: true,
                        isHeaderRegion: true,
                        isNearKkLabel: true,
                        confidence: nextLine.confidence || 85
                    });
                    break;
                }
            }
        }

        // Pattern B: Line contains "KARTU KELUARGA" / "KARTU KLUARGA" title
        if (kkTitlePattern.test(lText)) {
            // Check same line after title
            let valuePart = lText.replace(kkTitlePattern, '').replace(/^(?:No\.?|Nomor|N0\.?|N\.)\s*[:=\s\-]*/i, '');
            let cand = extractKkCandidate(valuePart);
            if (cand) {
                candidates.push({
                    value: cand,
                    rawPart: valuePart,
                    source: 'same_line_after_kk_title',
                    lineIndex: i,
                    isDirectlyRightOfLabel: true,
                    isHeaderRegion: true,
                    isNearKkLabel: true,
                    confidence: line.confidence || 90
                });
            }

            // Check 1-4 lines below title (such as line "No.  3277032911240001")
            for (let j = 1; j <= 4 && i + j < lines.length; j++) {
                const nextLine = lines[i + j];
                if (stopPattern.test(nextLine.text)) break;
                const stripped = nextLine.text.replace(/^(?:No\.?|Nomor|N0\.?|N\.)\s*[:=\s\-]*/i, '');
                const nextCand = extractKkCandidate(stripped);
                if (nextCand) {
                    candidates.push({
                        value: nextCand,
                        rawPart: nextLine.text,
                        source: 'line_below_kk_title',
                        lineIndex: i + j,
                        isDirectlyBelowKkTitle: true,
                        isHeaderRegion: true,
                        isNearKkLabel: true,
                        confidence: nextLine.confidence || 88
                    });
                    break;
                }
            }
        }

        // Pattern C: Line starting with "No." / "Nomor" / "N0." in header
        if (isHeaderRegion && /^(?:No\.?|Nomor|N0\.?|N\.)\s*[:=\s\-]*/i.test(lText)) {
            const stripped = lText.replace(/^(?:No\.?|Nomor|N0\.?|N\.)\s*[:=\s\-]*/i, '');
            const cand = extractKkCandidate(stripped);
            if (cand) {
                candidates.push({
                    value: cand,
                    rawPart: lText,
                    source: 'no_prefix_header_line',
                    lineIndex: i,
                    isNearKkLabel: true,
                    isHeaderRegion: true,
                    confidence: line.confidence || 85
                });
            }
        }

        // Pattern D: Multi-word horizontal digit joining on same line in header (e.g. "3204 1234 5678 9012")
        if (isHeaderRegion) {
            const seqs = extractCleanDigitSequences(lText);
            for (const seq of seqs) {
                candidates.push({
                    value: seq,
                    rawPart: lText,
                    source: 'clean_digits_header_line',
                    lineIndex: i,
                    isHeaderRegion: true,
                    confidence: line.confidence || 80
                });
            }
        }
    }

    // Pattern E: Stacked multi-line digits in header (e.g. 4 lines of 4 digits)
    if (lines.length >= 4) {
        for (let i = 0; i <= Math.min(lines.length - 4, 6); i++) {
            const chunk0 = lines[i].text.replace(/\D/g, '');
            const chunk1 = lines[i + 1].text.replace(/\D/g, '');
            const chunk2 = lines[i + 2].text.replace(/\D/g, '');
            const chunk3 = lines[i + 3].text.replace(/\D/g, '');
            if (chunk0.length === 4 && chunk1.length === 4 && chunk2.length === 4 && chunk3.length === 4) {
                const combined = chunk0 + chunk1 + chunk2 + chunk3;
                candidates.push({
                    value: combined,
                    rawPart: `${chunk0}-${chunk1}-${chunk2}-${chunk3}`,
                    source: 'stacked_multiline_digits',
                    lineIndex: i,
                    isHeaderRegion: true,
                    confidence: 75
                });
            }
        }
    }

    // 3. Candidate Scoring
    const scoredCandidates = [];
    const seenValues = new Set();

    for (const cand of candidates) {
        if (!cand.value || !/^[0-9]{16}$/.test(cand.value)) continue;
        if (seenValues.has(cand.value + cand.source)) continue;
        seenValues.add(cand.value + cand.source);

        let score = 10;
        const reasons = [];

        // Label Proximity
        if (cand.isDirectlyRightOfLabel) {
            score += 60;
            reasons.push('+60 (Right of KK label)');
        }
        if (cand.isDirectlyBelowKkTitle) {
            score += 50;
            reasons.push('+50 (Directly below KK title)');
        }
        if (cand.isDirectlyBelowKkLabel) {
            score += 45;
            reasons.push('+45 (Directly below KK label)');
        }
        if (cand.isNearKkLabel && !cand.isDirectlyRightOfLabel && !cand.isDirectlyBelowKkTitle) {
            score += 35;
            reasons.push('+35 (Near KK label/No. prefix)');
        }

        // Spatial Location
        if (cand.isHeaderRegion) {
            score += 30;
            reasons.push('+30 (Header region)');
        }

        // Province Code Validity
        const provCode = cand.value.substring(0, 2);
        if (VALID_PROVINCES.includes(provCode)) {
            score += 25;
            reasons.push(`+25 (Valid province code: ${provCode})`);
        } else {
            score -= 15;
            reasons.push(`-15 (Uncommon province code: ${provCode})`);
        }

        // OCR Confidence bonus
        if (cand.confidence) {
            const confBonus = Math.round(cand.confidence * 0.15);
            score += confBonus;
            reasons.push(`+${confBonus} (Confidence ${cand.confidence}%)`);
        }

        // NIK Penalty (Critical exclusion)
        if (nikExclusions.has(cand.value)) {
            score -= 120;
            reasons.push('-120 (PENALTY: Matched family member NIK)');
        }

        // Table Region Penalty
        if (cand.lineIndex >= tableStartLineIndex) {
            score -= 80;
            reasons.push('-80 (PENALTY: Inside family member table)');
        }

        // Date / NIP Penalty
        if (/^(?:19|20)\d{2}/.test(cand.value) && (cand.value.includes('2024') || cand.value.includes('2025') || cand.value.includes('2026') || cand.value.includes('1990'))) {
            score -= 20;
            reasons.push('-20 (Possible date/NIP prefix pattern)');
        }

        scoredCandidates.push({
            value: cand.value,
            score,
            source: cand.source,
            lineIndex: cand.lineIndex,
            reasons,
            rawPart: cand.rawPart
        });
    }

    // Sort by score descending
    scoredCandidates.sort((a, b) => b.score - a.score);

    const bestCandidate = scoredCandidates.length > 0 ? scoredCandidates[0] : null;
    const bestValue = (bestCandidate && bestCandidate.score >= 45) ? bestCandidate.value : '';
    const bestScore = bestCandidate ? bestCandidate.score : 0;
    const confidence = bestScore >= 70 ? 'high' : (bestScore >= 45 ? 'medium' : 'none');

    // Collect all 16-digit numbers in the document for family NIK cross-checking
    const all16Digits = new Set();
    for (const l of lines) {
        for (const seq of extractCleanDigitSequences(l.text)) {
            all16Digits.add(seq);
        }
    }
    const familyNiks = Array.from(all16Digits).filter(d => d !== bestValue);

    return {
        bestCandidate: bestValue,
        bestScore,
        confidence,
        scoredCandidates,
        familyNiks,
        nikExclusions: Array.from(nikExclusions),
        debugLog: {
            passName,
            totalLines: lines.length,
            tableStartLineIndex,
            evaluatedCount: scoredCandidates.length,
            bestCandidate: bestValue,
            bestScore,
            confidence,
            candidates: scoredCandidates
        }
    };
}

/**
 * High-accuracy Kartu Keluarga (KK) text parser (compatible with raw text or OCR data)
 */
export function parseKk(rawTextOrOcrData) {
    const evalResult = evaluateKkCandidates(rawTextOrOcrData, 'Direct parseKk');
    const rawText = typeof rawTextOrOcrData === 'string' ? rawTextOrOcrData : (rawTextOrOcrData?.text || '');

    return {
        no_kk: evalResult.bestCandidate,
        no_kk_confidence: evalResult.confidence,
        family_niks: evalResult.familyNiks,
        raw_text: rawText,
    };
}

/**
 * Cross-check NIK between KTP and Kartu Keluarga
 * @param {string} ktpNik
 * @param {string} kkRawText
 * @returns {'matched'|'different'|'unknown'}
 */
export function crossCheckNik(ktpNik, kkRawText) {
    if (!ktpNik || !isValid16Digits(ktpNik) || !kkRawText) return 'unknown';
    const kkParsed = parseKk(kkRawText);
    if (kkParsed.family_niks.includes(ktpNik) || kkRawText.includes(ktpNik)) {
        return 'matched';
    }
    // If KK has valid 16-digit entries but doesn't have ktpNik
    if (kkParsed.family_niks.length > 0) {
        return 'different';
    }
    return 'unknown';
}

/**
 * Parse Paspor text output
 */
export function parsePassport(rawText) {
    const result = {
        no_passport: '',
        name: '',
        raw_text: rawText || '',
    };

    if (!rawText || !rawText.trim()) return result;

    // Search for passport number pattern (e.g. A1234567, B1234567, X1234567)
    const passMatch = rawText.match(/\b([A-Z][0-9]{7,8})\b/i);
    if (passMatch) {
        result.no_passport = passMatch[1].toUpperCase();
    }

    // Search for MRZ line (P<IDN...)
    const mrzMatch = rawText.match(/P<IDN([A-Z<]+)/i);
    if (mrzMatch && mrzMatch[1]) {
        const nameClean = mrzMatch[1].replace(/<+/g, ' ').trim();
        if (nameClean) {
            result.name = nameClean.toUpperCase();
        }
    }

    return result;
}

/**
 * Main OCR Scan function with Multi-Pass KK Extraction & Image Quality Check
 * @param {File|Blob|string} fileSource - File or image URL
 * @param {'ktp'|'kk'|'passport'} docType - Document type
 * @param {Function} onProgress - Progress callback (percentage 0-100)
 */
export async function scanDocument(fileSource, docType = 'ktp', onProgress = null) {
    try {
        if (onProgress) onProgress(5);

        // 1. Image Quality Assessment
        const quality = await assessImageQuality(fileSource);
        if (!quality.isAcceptable && quality.warning) {
            console.warn('Image quality warning:', quality.warning);
        }

        if (docType === 'kk') {
            // ──────────────────────────────────────────────────────────
            // MULTI-PASS KK OCR PIPELINE
            // ──────────────────────────────────────────────────────────
            if (onProgress) onProgress(15);
            const preprocessed = await preprocessImage(fileSource);
            if (onProgress) onProgress(30);

            const worker = await getWorker(onProgress);

            // PASS 1: Full Image Normal OCR with spatial analysis
            if (onProgress) onProgress(45);
            const pass1Res = await worker.recognize(preprocessed);
            let eval1 = evaluateKkCandidates(pass1Res.data, 'Pass 1 (Full Image Normal)');

            let finalCandidate = eval1.bestCandidate;
            let finalScore = eval1.bestScore;
            let finalConfidence = eval1.confidence;
            let finalPass = 'Pass 1 (Full Image)';
            const allLogs = [eval1.debugLog];

            // PASS 2: Header Region Crop + High Contrast & Unsharp Sharpening (if score < 70)
            if (finalScore < 70) {
                if (onProgress) onProgress(65);
                try {
                    const headerContrastUrl = await cropAndPreprocessHeader(fileSource, 0.38, 2.5, 'contrast');
                    const pass2Res = await worker.recognize(headerContrastUrl);
                    const eval2 = evaluateKkCandidates(pass2Res.data, 'Pass 2 (Header Contrast + Sharpening)');
                    allLogs.push(eval2.debugLog);

                    if (eval2.bestScore > finalScore) {
                        finalCandidate = eval2.bestCandidate;
                        finalScore = eval2.bestScore;
                        finalConfidence = eval2.confidence;
                        finalPass = 'Pass 2 (Header Contrast)';
                    }
                } catch (err2) {
                    console.warn('[OCR KK] Pass 2 failed:', err2);
                }
            }

            // PASS 3: Header Region Crop + Otsu Binary + Whitelist (if still score < 60)
            if (finalScore < 60) {
                if (onProgress) onProgress(80);
                try {
                    const headerBinaryUrl = await cropAndPreprocessHeader(fileSource, 0.38, 3.0, 'binary');
                    const pass3Res = await worker.recognize(headerBinaryUrl, {
                        tessedit_char_whitelist: '0123456789NoKk.:- '
                    });
                    const eval3 = evaluateKkCandidates(pass3Res.data, 'Pass 3 (Header Binary Whitelist)');
                    allLogs.push(eval3.debugLog);

                    if (eval3.bestScore > finalScore) {
                        finalCandidate = eval3.bestCandidate;
                        finalScore = eval3.bestScore;
                        finalConfidence = eval3.confidence;
                        finalPass = 'Pass 3 (Header Binary Whitelist)';
                    }
                } catch (err3) {
                    console.warn('[OCR KK] Pass 3 failed:', err3);
                }
            }

            await worker.terminate();
            if (onProgress) onProgress(100);

            // Debug Logging for Developers/Admins (Requirement #14)
            console.groupCollapsed(`[OCR KK DEBUG] Multi-pass Extraction — Chosen: "${finalCandidate || 'None'}" (${finalPass}, Score: ${finalScore})`);
            console.log('Final Selected No. KK:', finalCandidate);
            console.log('Final Score:', finalScore);
            console.log('Confidence Level:', finalConfidence);
            console.log('Winning Pass:', finalPass);
            console.log('Detailed Pass Logs:', allLogs);
            console.groupEnd();

            const parsedData = {
                no_kk: finalScore >= 45 ? finalCandidate : '',
                no_kk_confidence: finalConfidence,
                family_niks: eval1.familyNiks || [],
                raw_text: pass1Res.data.text || '',
            };

            return {
                success: true,
                type: 'kk',
                quality,
                data: parsedData,
            };
        }

        // Standard flow for KTP and Passport
        if (onProgress) onProgress(15);
        const preprocessed = await preprocessImage(fileSource);
        if (onProgress) onProgress(30);

        const worker = await getWorker(onProgress);
        if (onProgress) onProgress(45);

        const res = await worker.recognize(preprocessed);
        await worker.terminate();

        if (onProgress) onProgress(100);

        const text = res.data.text || '';
        let parsedData = {};

        if (docType === 'ktp') {
            parsedData = parseKtp(text);
        } else if (docType === 'passport') {
            parsedData = parsePassport(text);
        } else {
            parsedData = { raw_text: text };
        }

        return {
            success: true,
            type: docType,
            quality: quality,
            data: parsedData,
        };
    } catch (err) {
        console.error('OCR Processing error:', err);
        return {
            success: false,
            error: err.message || 'Gagal memproses dokumen dengan OCR.',
            data: {},
        };
    }
}

window.OcrScanner = {
    assessImageQuality,
    scanDocument,
    parseKtp,
    parseKk,
    parsePassport,
    crossCheckNik,
    normalizeDigits,
    isValid16Digits,
    evaluateKkCandidates,
};
