<?php

namespace App\Services;

use App\Models\DocumentSequence;
use App\Models\FiscalYear;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DocumentSequenceService
{
    /**
     * Get the next document number for a given series with row-level lock.
     *
     * @param string $series e.g. 'PO', 'GRN', 'INV', 'PINV', 'SRTN', 'PRTN', 'ADJ', 'TRF', 'CRCPT', 'SPMT'
     * @param FiscalYear|null $fiscalYear
     * @param string|null $defaultPrefix
     * @param int $defaultPadLength
     * @return string
     * @throws RuntimeException
     */
    public static function getNextNumber(
        string $series,
        ?FiscalYear $fiscalYear = null,
        ?string $defaultPrefix = null,
        int $defaultPadLength = 5
    ): string {
        $fiscalYear = $fiscalYear ?? FiscalYear::current();
        if (! $fiscalYear) {
            // Fallback: get the latest fiscal year
            $fiscalYear = FiscalYear::orderByDesc('id')->first();
        }

        if (! $fiscalYear) {
            throw new RuntimeException('Cannot generate document number: No active fiscal year configured.');
        }

        return DB::transaction(function () use ($series, $fiscalYear, $defaultPrefix, $defaultPadLength) {
            $prefix = $defaultPrefix ?? ($series . '-');

            $sequence = DocumentSequence::where('series', $series)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = DocumentSequence::create([
                    'fiscal_year_id' => $fiscalYear->id,
                    'series' => $series,
                    'prefix' => $prefix,
                    'suffix' => null,
                    'last_number' => 0,
                    'pad_length' => $defaultPadLength,
                ]);
            }

            $sequence->last_number += 1;
            $sequence->save();

            $formattedNumber = ($sequence->prefix ?? '')
                . str_pad((string) $sequence->last_number, $sequence->pad_length, '0', STR_PAD_LEFT)
                . ($sequence->suffix ?? '');

            return $formattedNumber;
        });
    }

    /**
     * Preview the next document number without incrementing.
     */
    public static function previewNextNumber(string $series, ?FiscalYear $fiscalYear = null): string
    {
        $fiscalYear = $fiscalYear ?? FiscalYear::current() ?? FiscalYear::orderByDesc('id')->first();
        if (! $fiscalYear) {
            return $series . '-00001';
        }

        $sequence = DocumentSequence::where('series', $series)
            ->where('fiscal_year_id', $fiscalYear->id)
            ->first();

        $prefix = $sequence?->prefix ?? ($series . '-');
        $nextNum = ($sequence?->last_number ?? 0) + 1;
        $pad = $sequence?->pad_length ?? 5;
        $suffix = $sequence?->suffix ?? '';

        return $prefix . str_pad((string) $nextNum, $pad, '0', STR_PAD_LEFT) . $suffix;
    }
}
