<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;

class PeriodFilterService
{
    public const PERIOD_WEEK = 'week';
    public const PERIOD_MONTH = 'month';
    public const PERIOD_YEAR = 'year';
    public const PERIOD_CUSTOM = 'custom';

    /**
     * Parse request into structured period filter data.
     *
     * @param Request $request
     * @return array{
     *     period: string,
     *     startDate: Carbon,
     *     endDate: Carbon,
     *     label: string,
     *     selectedMonth: int,
     *     selectedYear: int,
     *     weekDate: string,
     *     prevWeekDate: string,
     *     nextWeekDate: string,
     *     customStartDate: string|null,
     *     customEndDate: string|null,
     *     queryParams: array
     * }
     */
    public function parse(Request $request): array
    {
        $period = $request->input('period', self::PERIOD_MONTH);
        if (!in_array($period, [self::PERIOD_WEEK, self::PERIOD_MONTH, self::PERIOD_YEAR, self::PERIOD_CUSTOM])) {
            $period = self::PERIOD_MONTH;
        }

        $now = Carbon::now();
        $selectedMonth = (int) $request->input('month', $now->month);
        $selectedYear = (int) $request->input('year', $now->year);

        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = $now->month;
        }
        if ($selectedYear < 2020 || $selectedYear > 2035) {
            $selectedYear = $now->year;
        }

        $weekDateInput = $request->input('week_date', $now->format('Y-m-d'));
        try {
            $parsedWeek = Carbon::parse($weekDateInput);
        } catch (\Exception $e) {
            $parsedWeek = $now->copy();
        }

        $customStartInput = $request->input('start_date');
        $customEndInput = $request->input('end_date');

        switch ($period) {
            case self::PERIOD_WEEK:
                // Senin 00:00:00 s/d Minggu 23:59:59
                $startDate = $parsedWeek->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $endDate = $parsedWeek->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                $label = 'Minggu ' . $startDate->translatedFormat('d M') . ' — ' . $endDate->translatedFormat('d M Y');
                break;

            case self::PERIOD_YEAR:
                $startDate = Carbon::createFromDate($selectedYear, 1, 1)->startOfDay();
                $endDate = Carbon::createFromDate($selectedYear, 12, 31)->endOfDay();
                $label = 'Tahun ' . $selectedYear;
                break;

            case self::PERIOD_CUSTOM:
                try {
                    $startDate = $customStartInput ? Carbon::parse($customStartInput)->startOfDay() : $now->copy()->startOfMonth()->startOfDay();
                    $endDate = $customEndInput ? Carbon::parse($customEndInput)->endOfDay() : $now->copy()->endOfMonth()->endOfDay();
                    if ($startDate->gt($endDate)) {
                        $temp = $startDate;
                        $startDate = $endDate->copy()->startOfDay();
                        $endDate = $temp->copy()->endOfDay();
                    }
                } catch (\Exception $e) {
                    $startDate = $now->copy()->startOfMonth()->startOfDay();
                    $endDate = $now->copy()->endOfMonth()->endOfDay();
                }
                $label = $startDate->translatedFormat('d M Y') . ' — ' . $endDate->translatedFormat('d M Y');
                break;

            case self::PERIOD_MONTH:
            default:
                $period = self::PERIOD_MONTH;
                $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
                $endDate = $startDate->copy()->endOfMonth()->endOfDay();
                $label = $startDate->translatedFormat('F Y');
                break;
        }

        $prevWeekDate = $parsedWeek->copy()->subWeek()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $nextWeekDate = $parsedWeek->copy()->addWeek()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

        $queryParams = [
            'period' => $period,
            'month' => $selectedMonth,
            'year' => $selectedYear,
            'week_date' => $startDate->format('Y-m-d'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];

        $granularityLabel = match ($period) {
            self::PERIOD_WEEK => 'Harian',
            self::PERIOD_YEAR => 'Bulanan',
            self::PERIOD_CUSTOM => (isset($diffDays) && $diffDays > 31) ? 'Bulanan' : 'Harian',
            default => 'Mingguan',
        };

        return [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'label' => $label,
            'granularity_label' => $granularityLabel,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'weekDate' => $startDate->format('Y-m-d'),
            'prevWeekDate' => $prevWeekDate,
            'nextWeekDate' => $nextWeekDate,
            'customStartDate' => $startDate->format('Y-m-d'),
            'customEndDate' => $endDate->format('Y-m-d'),
            'queryParams' => $queryParams,
        ];
    }

    /**
     * Alias for parse($request)
     */
    public function parseRequest(Request $request): array
    {
        return $this->parse($request);
    }

    /**
     * Menghasilkan granularitas waktu (buckets) untuk visualisasi grafik.
     * - Week: 7 harian (Senin - Minggu)
     * - Month: 4-5 mingguan (Minggu 1, Minggu 2, ...)
     * - Year: 12 bulanan (Januari - Desember)
     * - Custom: Harian (jika <= 31 hari) atau Bulanan (jika > 31 hari)
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $period
     * @return array<int, array{label: string, start: Carbon, end: Carbon}>
     */
    public function getBuckets(Carbon $startDate, Carbon $endDate, string $period): array
    {
        $buckets = [];

        if ($period === self::PERIOD_WEEK) {
            $cursor = $startDate->copy();
            while ($cursor->lte($endDate)) {
                $dayStart = $cursor->copy()->startOfDay();
                $dayEnd = $cursor->copy()->endOfDay();
                $buckets[] = [
                    'label' => $cursor->translatedFormat('D, d M'),
                    'start' => $dayStart,
                    'end' => $dayEnd,
                ];
                $cursor->addDay();
            }
        } elseif ($period === self::PERIOD_MONTH) {
            $cursor = $startDate->copy();
            $weekNum = 1;
            while ($cursor->lte($endDate)) {
                $weekStart = $cursor->copy()->startOfDay();
                // Akhir pekan (Minggu) atau akhir bulan
                $weekEnd = $cursor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate->copy()->endOfDay();
                }

                $buckets[] = [
                    'label' => 'Mg ' . $weekNum . ' (' . $weekStart->format('d/m') . '-' . $weekEnd->format('d/m') . ')',
                    'start' => $weekStart,
                    'end' => $weekEnd,
                ];

                $cursor = $weekEnd->copy()->addSecond()->startOfDay();
                $weekNum++;
            }
        } elseif ($period === self::PERIOD_YEAR) {
            $year = $startDate->year;
            for ($m = 1; $m <= 12; $m++) {
                $mStart = Carbon::createFromDate($year, $m, 1)->startOfDay();
                $mEnd = $mStart->copy()->endOfMonth()->endOfDay();
                $buckets[] = [
                    'label' => $mStart->translatedFormat('M'),
                    'start' => $mStart,
                    'end' => $mEnd,
                ];
            }
        } else {
            // Custom period
            $diffDays = $startDate->diffInDays($endDate);
            if ($diffDays <= 31) {
                $cursor = $startDate->copy();
                while ($cursor->lte($endDate)) {
                    $dayStart = $cursor->copy()->startOfDay();
                    $dayEnd = $cursor->copy()->endOfDay();
                    $buckets[] = [
                        'label' => $cursor->translatedFormat('d M'),
                        'start' => $dayStart,
                        'end' => $dayEnd,
                    ];
                    $cursor->addDay();
                }
            } else {
                $cursor = $startDate->copy()->startOfMonth();
                while ($cursor->lte($endDate)) {
                    $mStart = $cursor->copy()->startOfDay();
                    $mEnd = $cursor->copy()->endOfMonth()->endOfDay();
                    if ($mStart->lt($startDate)) {
                        $mStart = $startDate->copy();
                    }
                    if ($mEnd->gt($endDate)) {
                        $mEnd = $endDate->copy();
                    }
                    $buckets[] = [
                        'label' => $cursor->translatedFormat('M Y'),
                        'start' => $mStart,
                        'end' => $mEnd,
                    ];
                    $cursor->addMonth()->startOfMonth();
                }
            }
        }

        return $buckets;
    }
}
