<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\EligibilityMarkController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\MunicipalityController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfessionalController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SubjectCommitteeController;
use App\Http\Controllers\Admin\SubjectCommitteeUserController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationStepController;
use App\Http\Controllers\Admin\AdmitCardController;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'login'])->name('admin.login');
Route::post('check-login', [AuthController::class, 'checkLogin'])->name('admin.checkLogin');
Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('admin.forgot.password');
Route::post('otp', [AuthController::class, 'otp'])->name('admin.otp');
Route::get('reset-password', [AuthController::class, 'resetPassword'])->name('admin.reset.password');
Route::post('save-password', [AuthController::class, 'savePassword'])->name('admin.save.password');

Route::group(['middleware' => 'auth.admin'], function () {
    Route::get('/', [DashboardController::class, 'index'])->middleware('pharma.access')->name('admin.dashboard');
    Route::prefix('account')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('admin.account.setting');
        Route::post('/update-account', [AccountController::class, 'addAction'])->name('admin.account.update');
        Route::post('/update-password', [AccountController::class, 'updatePassword'])->name('admin.account.update.password');
        Route::get('/logout', [AccountController::class, 'logout'])->name('admin.logout');
    });

    Route::prefix('module')->middleware('auth.super-admin')->group(function () {
        Route::get('/', [ModuleController::class, 'list'])->name('admin.module');
        Route::get('/add.edit', [ModuleController::class, 'addEdit'])->name('admin.module.addEdit');
        Route::post('/store', [ModuleController::class, 'store'])->name('admin.module.store');
        Route::post('/delete', [ModuleController::class, 'delete'])->name('admin.module.delete');
    });

    Route::prefix('role')->middleware('auth.super-admin')->group(function () {
        Route::get('/', [RoleController::class, 'list'])->name('admin.role');
        Route::get('/add.edit', [RoleController::class, 'addEdit'])->name('admin.role.addEdit');
        Route::post('/store', [RoleController::class, 'store'])->name('admin.role.store');
        Route::post('/delete', [RoleController::class, 'delete'])->name('admin.role.delete');
    });

    Route::prefix('permission')->middleware('auth.super-admin')->group(function () {
        Route::get('/', [PermissionController::class, 'list'])->name('admin.permission');
        Route::get('/add.edit', [PermissionController::class, 'addEdit'])->name('admin.permission.addEdit');
        Route::post('/store', [PermissionController::class, 'store'])->name('admin.permission.store');
        Route::post('/delete', [PermissionController::class, 'delete'])->name('admin.permission.delete');
    });

    Route::prefix('verificationstep')->group(function () {
        Route::get('/', [VerificationStepController::class, 'list'])->name('admin.verificationstep');
        Route::post('/step', [VerificationStepController::class, 'step'])->name('admin.verificationstep.step');
        Route::get('/add.edit', [VerificationStepController::class, 'addEdit'])->name('admin.verificationstep.addEdit');
        Route::post('/store', [VerificationStepController::class, 'store'])->name('admin.verificationstep.store');
        Route::post('/delete', [VerificationStepController::class, 'delete'])->name('admin.verificationstep.delete');
    });

    Route::prefix('user')->middleware('auth.super-admin')->group(function () {
        Route::get('/', [UserController::class, 'list'])->name('admin.user');
        Route::post('/status', [UserController::class, 'status'])->name('admin.user.status');
        Route::get('/add.edit', [UserController::class, 'addEdit'])->name('admin.user.addEdit');
        Route::post('/store', [UserController::class, 'store'])->name('admin.user.store');
        Route::post('/delete', [UserController::class, 'delete'])->name('admin.user.delete');
    });

    Route::prefix('university')->group(function () {
        Route::get('/', [UniversityController::class, 'list'])->name('admin.university');
        Route::post('/status', [UniversityController::class, 'status'])->name('admin.university.status');
        Route::get('/add.edit', [UniversityController::class, 'addEdit'])->name('admin.university.addEdit');
        Route::post('/store', [UniversityController::class, 'store'])->name('admin.university.store');
        Route::post('/delete', [UniversityController::class, 'delete'])->name('admin.university.delete');
    });

    Route::prefix('college')->group(function () {
        Route::get('/', [CollegeController::class, 'list'])->name('admin.college');
        Route::post('/status', [CollegeController::class, 'status'])->name('admin.college.status');
        Route::get('/add.edit', [CollegeController::class, 'addEdit'])->name('admin.college.addEdit');
        Route::post('/store', [CollegeController::class, 'store'])->name('admin.college.store');
        Route::post('/delete', [CollegeController::class, 'delete'])->name('admin.college.delete');
    });

    Route::prefix('province')->group(function () {
        Route::get('/', [ProvinceController::class, 'list'])->name('admin.province');
        Route::post('/status', [ProvinceController::class, 'status'])->name('admin.province.status');
        Route::get('/add.edit', [ProvinceController::class, 'addEdit'])->name('admin.province.addEdit');
        Route::post('/store', [ProvinceController::class, 'store'])->name('admin.province.store');
        Route::post('/delete', [ProvinceController::class, 'delete'])->name('admin.province.delete');
    });

    Route::prefix('district')->group(function () {
        Route::get('/', [DistrictController::class, 'list'])->name('admin.district');
        Route::post('/status', [DistrictController::class, 'status'])->name('admin.district.status');
        Route::get('/add.edit', [DistrictController::class, 'addEdit'])->name('admin.district.addEdit');
        Route::post('/store', [DistrictController::class, 'store'])->name('admin.district.store');
        Route::post('/delete', [DistrictController::class, 'delete'])->name('admin.district.delete');
    });

    Route::prefix('municipality')->group(function () {
        Route::get('/', [MunicipalityController::class, 'list'])->name('admin.municipality');
        Route::post('/status', [MunicipalityController::class, 'status'])->name('admin.municipality.status');
        Route::get('/add.edit', [MunicipalityController::class, 'addEdit'])->name('admin.municipality.addEdit');
        Route::post('/store', [MunicipalityController::class, 'store'])->name('admin.municipality.store');
        Route::post('/delete', [MunicipalityController::class, 'delete'])->name('admin.municipality.delete');
    });

    Route::prefix('level')->group(function () {
        Route::get('/', [LevelController::class, 'list'])->name('admin.level');
        Route::post('/order', [LevelController::class, 'order'])->name('admin.level.order');
        Route::post('/status', [LevelController::class, 'status'])->name('admin.level.status');
        Route::get('/add.edit', [LevelController::class, 'addEdit'])->name('admin.level.addEdit');
        Route::post('/store', [LevelController::class, 'store'])->name('admin.level.store');
        Route::post('/delete', [LevelController::class, 'delete'])->name('admin.level.delete');
    });

    Route::prefix('subjectcommittee')->group(function () {
        Route::get('/', [SubjectCommitteeController::class, 'list'])->name('admin.subjectcommittee');
        Route::post('/status', [SubjectCommitteeController::class, 'status'])->name('admin.subjectcommittee.status');
        Route::get('/add.edit', [SubjectCommitteeController::class, 'addEdit'])->name('admin.subjectcommittee.addEdit');
        Route::post('/store', [SubjectCommitteeController::class, 'store'])->name('admin.subjectcommittee.store');
        Route::post('/delete', [SubjectCommitteeController::class, 'delete'])->name('admin.subjectcommittee.delete');
    });

    Route::prefix('subjectcommitteeuser')->group(function () {
        Route::get('/', [SubjectCommitteeUserController::class, 'list'])->name('admin.subjectcommitteeuser');
        Route::post('/status', [SubjectCommitteeUserController::class, 'status'])->name('admin.subjectcommitteeuser.status');
        Route::get('/add.edit', [SubjectCommitteeUserController::class, 'addEdit'])->name('admin.subjectcommitteeuser.addEdit');
        Route::post('/store', [SubjectCommitteeUserController::class, 'store'])->name('admin.subjectcommitteeuser.store');
        Route::post('/delete', [SubjectCommitteeUserController::class, 'delete'])->name('admin.subjectcommitteeuser.delete');
    });

    Route::prefix('program')->group(function () {
        Route::get('/', [ProgramController::class, 'list'])->name('admin.program');
        Route::post('/status', [ProgramController::class, 'status'])->name('admin.program.status');
        Route::get('/add.edit', [ProgramController::class, 'addEdit'])->name('admin.program.addEdit');
        Route::post('/store', [ProgramController::class, 'store'])->name('admin.program.store');
        Route::post('/delete', [ProgramController::class, 'delete'])->name('admin.program.delete');
    });

    Route::prefix('eligibilitymark')->group(function () {
        Route::get('/', [EligibilityMarkController::class, 'list'])->name('admin.eligibilitymark');
        Route::get('/add.edit', [EligibilityMarkController::class, 'addEdit'])->name('admin.eligibilitymark.addEdit');
        Route::post('/store', [EligibilityMarkController::class, 'store'])->name('admin.eligibilitymark.store');
        Route::post('/delete', [EligibilityMarkController::class, 'delete'])->name('admin.eligibilitymark.delete');
    });

    Route::prefix('professional')->group(function () {
        Route::get('/', [ProfessionalController::class, 'list'])->name('admin.professional');
        Route::get('/detail', [ProfessionalController::class, 'detail'])->name('admin.professional.detail');
        Route::get('/profile', [ProfessionalController::class, 'profile'])->name('admin.professional.profile');
        Route::post('/save.profile', [ProfessionalController::class, 'saveProfile'])->name('admin.professional.save.profile');
        Route::post('/imageProfileDelete', [ProfessionalController::class, 'imageProfileDelete'])->name('admin.professional.imageProfileDelete');
        Route::post('/status', [ProfessionalController::class, 'status'])->name('admin.professional.status');
        Route::post('/delete', [ProfessionalController::class, 'delete'])->name('admin.professional.delete');
        Route::get('/qualification/{level}/{id}', [ProfessionalController::class, 'qualification'])->name('admin.professional.qualification');
        Route::post('/imageQualificationDelete', [ProfessionalController::class, 'imageQualificationDelete'])->name('admin.professional.imageQualificationDelete');
        Route::post('/qualification/slc', [ProfessionalController::class, 'saveSlc'])->name('admin.professional.save.slc');
        Route::post('/qualification/tslc', [ProfessionalController::class, 'saveTslc'])->name('admin.professional.save.tslc');
        Route::post('/qualification/pcl', [ProfessionalController::class, 'savePcl'])->name('admin.professional.save.pcl');
        Route::post('/qualification/bachelor', [ProfessionalController::class, 'saveBachelor'])->name('admin.professional.save.bachelor');
        Route::post('/qualification/master', [ProfessionalController::class, 'saveMaster'])->name('admin.professional.save.master');
    });

    Route::prefix('exam')->group(function () {
        Route::get('/', [ExamController::class, 'list'])->name('admin.exam');
        Route::post('/status', [ExamController::class, 'status'])->name('admin.exam.status');
        Route::get('/add.edit', [ExamController::class, 'addEdit'])->name('admin.exam.addEdit');
        Route::post('/store', [ExamController::class, 'store'])->name('admin.exam.store');
        Route::post('/delete', [ExamController::class, 'delete'])->name('admin.exam.delete');
        Route::get('/detail', [ExamController::class, 'examDetail'])->name('admin.exam.detail');
        Route::get('/routine', [ExamController::class, 'routineForm'])->name('admin.exam.routine');
        Route::post('/routine/generate', [ExamController::class, 'routineGenerate'])->name('admin.exam.routineGenerate');
    });

    Route::prefix('applicant')->group(function () {
        Route::get('/', [ApplicantController::class, 'list'])->name('admin.applicant');
        Route::get('/new', [ApplicantController::class, 'new'])->name('admin.applicant.new');
        Route::get('/pending', [ApplicantController::class, 'pending'])->name('admin.applicant.pending');
        Route::get('/approved', [ApplicantController::class, 'approved'])->name('admin.applicant.approved');
        Route::get('/rejected', [ApplicantController::class, 'rejected'])->name('admin.applicant.rejected');
        Route::get('/add.edit', [ApplicantController::class, 'addEdit'])->name('admin.applicant.addEdit');
        Route::post('/store', [ApplicantController::class, 'store'])->name('admin.applicant.store');
        Route::post('/delete', [ApplicantController::class, 'delete'])->name('admin.applicant.delete');
        Route::post('/status', [ApplicantController::class, 'saveStatus'])->name('admin.applicant.save.status');
        Route::post('/state', [ApplicantController::class, 'saveState'])->name('admin.applicant.save.state');
        Route::get('/export', [ApplicantController::class, 'export'])->name('admin.applicant.export');
        Route::get('/admitcard/{applicant_id}', [ApplicantController::class, 'admitCard'])->name('admin.applicant.admitcard');
        // Route::get('/forward_reexam', [ApplicantController::class, 'fowrardReExam'])->name('operator-fowrard-reexam');
        // Route::post('/selected_forward_reexam', [ApplicantController::class, 'selectedFowrardReExam'])->name('operator-selected-fowrard-reexam');

    });

    Route::prefix('kyc')->group(function () {
        Route::get('/', [KycController::class, 'list'])->name('admin.kyc');
        Route::post('/delete', [KycController::class, 'delete'])->name('admin.kyc.delete');
        Route::get('/certificate', [KycController::class, 'certificate'])->name('admin.kyc.certificate');
        Route::get('/allocate', [KycController::class, 'allocate'])->name('admin.kyc.allocate');
        Route::post('/allocate.save', [KycController::class, 'saveAllocate'])->name('admin.kyc.allocate.save');
        Route::get('/json', [KycController::class, 'json'])->name('admin.kyc.json');
    });

    // Route::prefix('kyc')->group(function () {
    //     Route::get('/', [KYCController::class, 'list'])->name('operator-kyc');
    //     Route::post('/delete', [KYCController::class, 'delete'])->name('operator-kyc-delete');
    //     Route::get('/certificate', [KYCController::class, 'certificate'])->name('operator-kyc-certificate');
    //     Route::get('/allocate/{id}', [KYCController::class, 'allocate_form'])->name('operator-kyc-allocate_form');
    //     Route::post('/allocate/{id}', [KYCController::class, 'save'])->name('operator-kyc-allocate_save');
    //     Route::get('/json_list', [KYCController::class, 'json_list'])->name('operator-kyc-json-list');
    // });

    Route::prefix('admitcard')->group(function () {
        Route::get('/add.edit', [AdmitCardController::class, 'addEdit'])->name('admin.admitcard.addEdit');
        Route::post('/store', [AdmitCardController::class, 'store'])->name('admin.admitcard.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Pharmaceutical Wholesale / Distribution Stock Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('pharma')->name('admin.pharma.')->middleware('pharma.access')->group(function () {
        // Products & Batches
        Route::get('/products', [\App\Http\Controllers\Admin\Pharma\PharmaProductController::class, 'index'])->name('products');
        Route::get('/product/create', [\App\Http\Controllers\Admin\Pharma\PharmaProductController::class, 'create'])->name('product.create');
        Route::post('/product/store', [\App\Http\Controllers\Admin\Pharma\PharmaProductController::class, 'store'])->name('product.store');
        Route::get('/product/{id}/edit', [\App\Http\Controllers\Admin\Pharma\PharmaProductController::class, 'edit'])->name('product.edit');
        Route::post('/product/{id}/update', [\App\Http\Controllers\Admin\Pharma\PharmaProductController::class, 'update'])->name('product.update');
        Route::post('/product/{id}/delete', [\App\Http\Controllers\Admin\Pharma\PharmaProductController::class, 'destroy'])->name('product.delete');
        Route::get('/batches', [\App\Http\Controllers\Admin\Pharma\PharmaProductController::class, 'batches'])->name('batches');

        // Purchasing - Purchase Orders
        Route::get('/purchase-orders', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'orders'])->name('purchase-orders');
        Route::get('/purchase-order/create', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'createOrder'])->name('purchase-order.create');
        Route::post('/purchase-order/store', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'storeOrder'])->name('purchase-order.store');
        Route::get('/purchase-order/{id}', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'showOrder'])->name('purchase-order.show');
        Route::get('/purchase-order/{id}/edit', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'editOrder'])->name('purchase-order.edit');
        Route::post('/purchase-order/{id}/update', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'updateOrder'])->name('purchase-order.update');
        Route::post('/purchase-order/{id}/delete', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'destroyOrder'])->name('purchase-order.delete');
        Route::post('/purchase-order/{id}/approve', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'approveOrder'])->name('purchase-order.approve');

        // Purchasing - Goods Receipts (GRN)
        Route::get('/goods-receipts', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'receipts'])->name('goods-receipts');
        Route::get('/goods-receipt/create', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'createReceipt'])->name('goods-receipt.create');
        Route::post('/goods-receipt/store', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'storeReceipt'])->name('goods-receipt.store');
        Route::get('/goods-receipt/{id}', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'showReceipt'])->name('goods-receipt.show');
        Route::post('/goods-receipt/{id}/post', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'postReceipt'])->name('goods-receipt.post');

        // Purchasing - Purchase Returns
        Route::get('/purchase-returns', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'returns'])->name('purchase-returns');
        Route::get('/purchase-return/create', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'createReturn'])->name('purchase-return.create');
        Route::post('/purchase-return/store', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'storeReturn'])->name('purchase-return.store');
        Route::get('/purchase-return/{id}', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'showReturn'])->name('purchase-return.show');
        Route::post('/purchase-return/{id}/post', [\App\Http\Controllers\Admin\Pharma\PharmaPurchasingController::class, 'postReturn'])->name('purchase-return.post');

        // Sales & Invoicing
        Route::get('/sales-invoices', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'invoices'])->name('sales-invoices');
        Route::get('/sale-invoice/create', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'createInvoice'])->name('sale-invoice.create');
        Route::post('/sale-invoice/store', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'storeInvoice'])->name('sale-invoice.store');
        Route::get('/sale-invoice/{id}', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'showInvoice'])->name('sale-invoice.show');
        Route::get('/sale-invoice/{id}/edit', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'editInvoice'])->name('sale-invoice.edit');
        Route::post('/sale-invoice/{id}/update', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'updateInvoice'])->name('sale-invoice.update');
        Route::post('/sale-invoice/{id}/delete', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'destroyInvoice'])->name('sale-invoice.delete');
        Route::post('/sale-invoice/{id}/post', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'postInvoice'])->name('sale-invoice.post');
        Route::get('/sale-invoice/{id}/print', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'printInvoice'])->name('sale-invoice.print');

        // Sales Returns
        Route::get('/sales-returns', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'returns'])->name('sales-returns');
        Route::get('/sales-return/create', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'createReturn'])->name('sales-return.create');
        Route::post('/sales-return/store', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'storeReturn'])->name('sales-return.store');
        Route::get('/sales-return/{id}', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'showReturn'])->name('sales-return.show');
        Route::get('/sales-return/{id}/edit', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'editReturn'])->name('sales-return.edit');
        Route::post('/sales-return/{id}/update', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'updateReturn'])->name('sales-return.update');
        Route::post('/sales-return/{id}/delete', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'destroyReturn'])->name('sales-return.delete');
        Route::post('/sales-return/{id}/post', [\App\Http\Controllers\Admin\Pharma\PharmaSalesController::class, 'postReturn'])->name('sales-return.post');

        // Warehouse & Inventory
        Route::get('/stock-movements', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'movements'])->name('stock-movements');
        Route::get('/stock-transfers', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'transfers'])->name('stock-transfers');
        Route::get('/stock-transfer/create', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'createTransfer'])->name('stock-transfer.create');
        Route::post('/stock-transfer/store', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'storeTransfer'])->name('stock-transfer.store');
        Route::get('/stock-transfer/{id}', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'showTransfer'])->name('stock-transfer.show');
        Route::post('/stock-transfer/{id}/dispatch', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'dispatchTransfer'])->name('stock-transfer.dispatch');
        Route::post('/stock-transfer/{id}/receive', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'receiveTransfer'])->name('stock-transfer.receive');

        Route::get('/stock-adjustments', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'adjustments'])->name('stock-adjustments');
        Route::get('/stock-adjustment/create', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'createAdjustment'])->name('stock-adjustment.create');
        Route::post('/stock-adjustment/store', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'storeAdjustment'])->name('stock-adjustment.store');
        Route::get('/stock-adjustment/{id}', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'showAdjustment'])->name('stock-adjustment.show');
        Route::post('/stock-adjustment/{id}/approve', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'approveAdjustment'])->name('stock-adjustment.approve');

        Route::get('/recalls', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'recalls'])->name('recalls');
        Route::post('/reconcile', [\App\Http\Controllers\Admin\Pharma\PharmaWarehouseController::class, 'reconcile'])->name('reconcile');

        // Payments & Treasury
        Route::get('/customer-receipts', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'customerReceipts'])->name('customer-receipts');
        Route::get('/customer-receipt/create', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'createReceipt'])->name('customer-receipt.create');
        Route::post('/customer-receipt/store', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'storeReceipt'])->name('customer-receipt.store');
        Route::get('/customer-receipt/{id}', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'showReceipt'])->name('customer-receipt.show');
        Route::post('/customer-receipt/{id}/post', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'postReceipt'])->name('customer-receipt.post');

        Route::get('/supplier-payments', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'supplierPayments'])->name('supplier-payments');
        Route::get('/supplier-payment/create', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'createPayment'])->name('supplier-payment.create');
        Route::post('/supplier-payment/store', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'storePayment'])->name('supplier-payment.store');
        Route::get('/supplier-payment/{id}', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'showPayment'])->name('supplier-payment.show');
        Route::post('/supplier-payment/{id}/post', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'postPayment'])->name('supplier-payment.post');

        Route::get('/cash-bank', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'cashBank'])->name('cash-bank');
        Route::post('/cash-bank/store', [\App\Http\Controllers\Admin\Pharma\PharmaPaymentController::class, 'storeAccount'])->name('cash-bank.store');

        // Masters - Warehouses & Locations
        Route::get('/warehouses', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'warehouses'])->name('warehouses');
        Route::post('/warehouse/store', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'storeWarehouse'])->name('warehouse.store');
        Route::post('/warehouse/{id}/update', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'updateWarehouse'])->name('warehouse.update');
        Route::post('/warehouse/{id}/delete', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'destroyWarehouse'])->name('warehouse.delete');
        Route::get('/warehouse/{id}/locations', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'warehouseLocations'])->name('warehouse.locations');
        Route::get('/warehouse-locations', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'allLocations'])->name('warehouse.locations.all');
        Route::post('/warehouse/location/store', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'storeLocation'])->name('warehouse.location.store');
        Route::post('/warehouse/location/{id}/update', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'updateLocation'])->name('warehouse.location.update');
        Route::post('/warehouse/location/{id}/delete', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'destroyLocation'])->name('warehouse.location.delete');

        // Masters - Suppliers
        Route::get('/suppliers', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'suppliers'])->name('suppliers');
        Route::get('/supplier/create', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'createSupplier'])->name('supplier.create');
        Route::post('/supplier/store', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'storeSupplier'])->name('supplier.store');
        Route::get('/supplier/{id}/edit', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'editSupplier'])->name('supplier.edit');
        Route::post('/supplier/{id}/update', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'updateSupplier'])->name('supplier.update');
        Route::post('/supplier/{id}/delete', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'destroySupplier'])->name('supplier.delete');

        // Masters - Customers
        Route::get('/customers', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'customers'])->name('customers');
        Route::get('/customer/create', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'createCustomer'])->name('customer.create');
        Route::post('/customer/store', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'storeCustomer'])->name('customer.store');
        Route::get('/customer/{id}/edit', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'editCustomer'])->name('customer.edit');
        Route::post('/customer/{id}/update', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'updateCustomer'])->name('customer.update');
        Route::post('/customer/{id}/delete', [\App\Http\Controllers\Admin\Pharma\PharmaMasterController::class, 'destroyCustomer'])->name('customer.delete');

        // Reports
        Route::get('/reports/stock-valuation', [\App\Http\Controllers\Admin\Pharma\PharmaReportController::class, 'stockValuation'])->name('reports.stock-valuation');
        Route::get('/reports/near-expiry', [\App\Http\Controllers\Admin\Pharma\PharmaReportController::class, 'nearExpiry'])->name('reports.near-expiry');
        Route::get('/reports/sales-register', [\App\Http\Controllers\Admin\Pharma\PharmaReportController::class, 'salesRegister'])->name('reports.sales-register');
        Route::get('/reports/purchase-register', [\App\Http\Controllers\Admin\Pharma\PharmaReportController::class, 'purchaseRegister'])->name('reports.purchase-register');
        Route::get('/reports/aging', [\App\Http\Controllers\Admin\Pharma\PharmaReportController::class, 'aging'])->name('reports.aging');
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\Pharma\PharmaReportController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/settings', [\App\Http\Controllers\Admin\Pharma\PharmaReportController::class, 'settings'])->name('settings');
    });

});