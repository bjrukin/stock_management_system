<?php

namespace App\Helpers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Carbon\Carbon;
use Throwable;

class BsDateHelper
{
    /**
     * Convert an English (AD) date to Nepali (BS) date.
     *
     * @param string|\DateTimeInterface|null $dateAd
     * @param string $format
     * @return string|null
     */
    public static function toBs($dateAd, string $format = 'Y-m-d'): ?string
    {
        if (empty($dateAd)) {
            return null;
        }

        try {
            if ($dateAd instanceof \DateTimeInterface) {
                $dateAd = $dateAd->format('Y-m-d');
            } else {
                $dateAd = Carbon::parse($dateAd)->format('Y-m-d');
            }

            return LaravelNepaliDate::from($dateAd)->toNepaliDate($format);
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Convert a Nepali (BS) date to English (AD) date.
     *
     * @param string|null $dateBs
     * @param string $inputFormat
     * @param string $outputFormat
     * @return string|null
     */
    public static function toAd(?string $dateBs, string $inputFormat = 'Y-m-d', string $outputFormat = 'Y-m-d'): ?string
    {
        if (empty($dateBs)) {
            return null;
        }

        try {
            $adDate = LaravelNepaliDate::from($dateBs, $inputFormat, 'np')->toEnglishDate();
            if ($outputFormat !== 'Y-m-d') {
                return Carbon::parse($adDate)->format($outputFormat);
            }
            return $adDate;
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Get current Nepali (BS) date.
     *
     * @param string $format
     * @return string
     */
    public static function todayBs(string $format = 'Y-m-d'): string
    {
        return self::toBs(Carbon::today(), $format) ?? '';
    }

    /**
     * Check if a given string is a valid Nepali (BS) date.
     *
     * @param string|null $dateBs
     * @param string $format
     * @return bool
     */
    public static function isValidBs(?string $dateBs, string $format = 'Y-m-d'): bool
    {
        if (empty($dateBs)) {
            return false;
        }

        try {
            return LaravelNepaliDate::validateNepali($dateBs, $format);
        } catch (Throwable $e) {
            return false;
        }
    }
}
