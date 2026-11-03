<?php

namespace App\Console\Commands;

use App\Services\InventoryService;
use Illuminate\Console\Command;

class ReconcileStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'stock:reconcile {--warehouse= : Specific warehouse ID to reconcile} {--fix : Automatically sync cached balances to ledger}';

    /**
     * The console command description.
     */
    protected $description = 'Reconcile cached inventory balances against the immutable stock_movements ledger';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $warehouseId = $this->option('warehouse') ? (int) $this->option('warehouse') : null;
        $fix = (bool) $this->option('fix');

        $this->info('Starting inventory balance reconciliation against immutable stock_movements ledger...');

        $discrepancies = InventoryService::reconcileWarehouse($warehouseId, $fix);

        if (empty($discrepancies)) {
            $this->info('✓ Perfect reconciliation: All cached balances strictly match the stock_movements ledger.');
            return Command::SUCCESS;
        }

        $this->warn(sprintf('Found %d discrepancy(ies) between balances and ledger!', count($discrepancies)));

        $rows = array_map(function ($d) {
            return [
                $d['warehouse_id'],
                $d['location_id'],
                $d['product_id'],
                $d['batch_id'],
                $d['ledger_quantity'],
                $d['cached_on_hand'],
                $d['variance'],
                $d['fixed'] ? 'Fixed' : 'Unfixed',
            ];
        }, $discrepancies);

        $this->table(
            ['Warehouse ID', 'Location ID', 'Product ID', 'Batch ID', 'Ledger Total', 'Cached On-Hand', 'Variance', 'Status'],
            $rows
        );

        if ($fix) {
            $this->info('✓ All cached balances have been re-synchronized to match the immutable ledger.');
        } else {
            $this->comment('Tip: Run with --fix to automatically correct these discrepancies to match the immutable ledger.');
        }

        return Command::SUCCESS;
    }
}
