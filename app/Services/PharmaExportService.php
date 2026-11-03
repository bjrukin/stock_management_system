<?php

namespace App\Services;

use App\Models\Product;
use App\Models\TaxCategory;
use App\Models\Unit;
use RuntimeException;

class PharmaExportService
{
    /**
     * Export Stock Valuation Report to CSV format.
     */
    public static function exportStockValuationCsv(?int $warehouseId = null): string
    {
        $report = ReportingService::getStockValuationReport($warehouseId);

        $output = fopen('php://temp', 'r+');

        // CSV Header
        fputcsv($output, [
            'Product Code',
            'Product Name',
            'Generic Name',
            'Batch Number',
            'Expiry Date (AD)',
            'Expiry Date (BS)',
            'Warehouse',
            'Location',
            'Qty On Hand',
            'Unit Cost (NPR)',
            'Cost Valuation (NPR)',
            'MRP (NPR)',
            'MRP Valuation (NPR)',
        ]);

        foreach ($report['rows'] as $row) {
            fputcsv($output, [
                $row['product_code'],
                $row['product_name'],
                $row['generic_name'],
                $row['batch_number'],
                $row['expiry_date_ad'],
                $row['expiry_date_bs'],
                $row['warehouse'],
                $row['location'],
                $row['quantity_on_hand'],
                $row['unit_cost'],
                $row['cost_value'],
                $row['mrp'],
                $row['mrp_value'],
            ]);
        }

        // Summary row
        fputcsv($output, [
            'TOTAL',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $report['total_units'],
            '',
            $report['total_cost_value'],
            '',
            $report['total_mrp_value'],
        ]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Export Customer Statement of Account to CSV format.
     */
    public static function exportCustomerStatementCsv(int $customerId, ?string $fromDateAd = null, ?string $toDateAd = null): string
    {
        $stmt = ReportingService::getCustomerStatement($customerId, $fromDateAd, $toDateAd);

        $output = fopen('php://temp', 'r+');

        // Customer Info header
        fputcsv($output, ['Customer Name:', $stmt['customer']['name']]);
        fputcsv($output, ['Customer Code:', $stmt['customer']['code']]);
        fputcsv($output, ['PAN/VAT:', $stmt['customer']['pan_vat']]);
        fputcsv($output, ['Closing Balance (NPR):', $stmt['closing_balance']]);
        fputcsv($output, []); // blank line

        // Transaction Headers
        fputcsv($output, [
            'Date (AD)',
            'Date (BS)',
            'Transaction Type',
            'Reference #',
            'Debit (NPR)',
            'Credit (NPR)',
            'Running Balance (NPR)',
            'Notes',
        ]);

        foreach ($stmt['entries'] as $entry) {
            fputcsv($output, [
                $entry['date_ad'],
                $entry['date_bs'],
                $entry['transaction_type'],
                $entry['reference_number'],
                $entry['debit_amount'],
                $entry['credit_amount'],
                $entry['running_balance'],
                $entry['notes'],
            ]);
        }

        fputcsv($output, [
            'TOTAL',
            '',
            '',
            '',
            $stmt['total_debit'],
            $stmt['total_credit'],
            $stmt['closing_balance'],
            '',
        ]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Export Sales Register to CSV format.
     */
    public static function exportSalesRegisterCsv(?string $fromDateAd = null, ?string $toDateAd = null, ?int $fiscalYearId = null): string
    {
        $reg = ReportingService::getSalesRegister($fromDateAd, $toDateAd, $fiscalYearId);

        $output = fopen('php://temp', 'r+');

        fputcsv($output, [
            'Invoice #',
            'Date (AD)',
            'Date (BS)',
            'Customer Name',
            'Customer PAN',
            'Non-Taxable Amount (NPR)',
            'Taxable Amount (NPR)',
            'VAT Amount (NPR)',
            'Total Amount (NPR)',
            'Status',
        ]);

        foreach ($reg['invoices'] as $row) {
            fputcsv($output, [
                $row['invoice_number'],
                $row['invoice_date_ad'],
                $row['invoice_date_bs'],
                $row['customer_name'],
                $row['customer_pan'],
                $row['non_taxable_amount'],
                $row['taxable_amount'],
                $row['tax_amount'],
                $row['total_amount'],
                $row['status'],
            ]);
        }

        fputcsv($output, [
            'TOTAL',
            '',
            '',
            '',
            '',
            $reg['total_non_taxable'],
            $reg['total_taxable'],
            $reg['total_tax'],
            $reg['total_grand'],
            '',
        ]);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Bulk Import Products from CSV content.
     * Expected CSV headers: code,name,generic_name,brand,dosage_form,strength,base_unit,purchase_price,selling_price,mrp,min_stock,reorder_level
     */
    public static function importProductsFromCsv(string $csvContent): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($csvContent));
        if (empty($lines)) {
            throw new RuntimeException("CSV content is empty.");
        }

        $header = str_getcsv(array_shift($lines));
        $headerMap = array_flip(array_map('trim', array_map('strtolower', $header)));

        $requiredHeaders = ['code', 'name', 'generic_name', 'base_unit'];
        foreach ($requiredHeaders as $req) {
            if (! isset($headerMap[$req])) {
                throw new RuntimeException("Missing mandatory CSV header: '{$req}'.");
            }
        }

        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($lines as $lineIdx => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $row = str_getcsv($line);
            $code = trim($row[$headerMap['code']] ?? '');
            $name = trim($row[$headerMap['name']] ?? '');
            $generic = trim($row[$headerMap['generic_name']] ?? '');
            $unitName = trim($row[$headerMap['base_unit']] ?? 'Piece');

            if (empty($code) || empty($name)) {
                $errors[] = "Row #" . ($lineIdx + 2) . ": Product code and name are required.";
                continue;
            }

            // Find or create base unit
            $unit = Unit::firstOrCreate(
                ['name' => $unitName],
                ['abbreviation' => substr($unitName, 0, 3)]
            );

            $purchasePrice = isset($headerMap['purchase_price']) ? (float) ($row[$headerMap['purchase_price']] ?? 0) : 0.00;
            $sellingPrice = isset($headerMap['selling_price']) ? (float) ($row[$headerMap['selling_price']] ?? 0) : 0.00;
            $mrp = isset($headerMap['mrp']) ? (float) ($row[$headerMap['mrp']] ?? 0) : 0.00;
            $minStock = isset($headerMap['min_stock']) ? (int) ($row[$headerMap['min_stock']] ?? 0) : 0;
            $reorderLevel = isset($headerMap['reorder_level']) ? (int) ($row[$headerMap['reorder_level']] ?? 0) : 0;

            $brand = isset($headerMap['brand']) ? trim($row[$headerMap['brand']] ?? '') : null;
            $dosageForm = isset($headerMap['dosage_form']) ? trim($row[$headerMap['dosage_form']] ?? '') : null;
            $strength = isset($headerMap['strength']) ? trim($row[$headerMap['strength']] ?? '') : null;

            $existing = Product::where('code', $code)->first();

            Product::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'generic_name' => $generic,
                    'brand' => $brand,
                    'dosage_form' => $dosageForm,
                    'strength' => $strength,
                    'base_unit_id' => $unit->id,
                    'purchase_price' => $purchasePrice,
                    'selling_price' => $sellingPrice,
                    'mrp' => $mrp,
                    'min_stock' => $minStock,
                    'reorder_level' => $reorderLevel,
                    'is_active' => true,
                ]
            );

            if ($existing) {
                $updated++;
            } else {
                $imported++;
            }
        }

        return [
            'imported_count' => $imported,
            'updated_count' => $updated,
            'errors' => $errors,
        ];
    }
}
