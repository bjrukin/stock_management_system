<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PharmaAccess;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\Warehouse;
use App\Services\ReportingService;

class DashboardController extends Controller
{
    public function index()
    {
        $data['nav'] = 'dashboard';
        $data['sub_nav'] = '';
        $data['page_title'] = 'Pharmaceutical Wholesale Dashboard';

        $data['summary'] = ReportingService::getDashboardSummary(warehouseIds: PharmaAccess::warehouseIds());
        $data['nearExpiryBatches'] = ReportingService::getNearExpiryBatches(90, warehouseIds: PharmaAccess::warehouseIds());
        $data['lowStockAlerts'] = ReportingService::getLowStockAlerts(warehouseIds: PharmaAccess::warehouseIds());
        $data['recentInvoices'] = PharmaAccess::scope(SaleInvoice::query())->with(['customer'])->orderBy('id', 'desc')->limit(5)->get();
        $data['recentReceipts'] = PharmaAccess::scope(GoodsReceipt::query())->with(['supplier'])->orderBy('id', 'desc')->limit(5)->get();
        $data['totalProducts'] = Product::count();
        $data['totalWarehouses'] = PharmaAccess::scope(Warehouse::query(), ['id'])->count();
        $data['stockFlow'] = \App\Services\InventoryService::getStockFlowSummary(warehouseIds: PharmaAccess::warehouseIds());

        return view('admin.dashboard.index', $data);
    }
}
