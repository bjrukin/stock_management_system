<?php

use App\Http\Controllers\Professional\AccountController;
use App\Http\Controllers\Professional\AdmitCardController;
use App\Http\Controllers\Professional\CertificateController;
use App\Http\Controllers\Professional\DashboardController;
use App\Http\Controllers\Professional\ExamController;
use App\Http\Controllers\Professional\ICardController;
use App\Http\Controllers\Professional\KhaltiController;
use App\Http\Controllers\Professional\KycController;
use App\Http\Controllers\Professional\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth.professional'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('professional.dashboard');
    Route::prefix('account')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('professional.account');
        Route::post('/store', [AccountController::class, 'store'])->name('professional.account.store');
        Route::get('/change-password', [AccountController::class, 'changePassword'])->name('professional.account.change.password');
        Route::post('/update-password', [AccountController::class, 'updatePassword'])->name('professional.account.update.password');
        Route::get('/logout', [AccountController::class, 'logout'])->name('professional.logout');
    });

    Route::prefix('profile')->group(function () {
        Route::get('/personal', [ProfileController::class, 'personal'])->name('professional.profile.personal');
        Route::post('/savePersonal', [ProfileController::class, 'savePersonal'])->name('professional.profile.save.personal');
        Route::post('/imageDelete', [ProfileController::class, 'imageDelete'])->name('professional.profile.imageDelete');
        Route::get('/guardian', [ProfileController::class, 'guardian'])->name('professional.profile.guardian');
        Route::post('/saveGuardian', [ProfileController::class, 'saveGuardian'])->name('professional.profile.save.guardian');
        Route::post('/qualification.imageDelete', [ProfileController::class, 'imageQualificationDelete'])->name('professional.profile.qualification.imageDelete');
        Route::get('/slc', [ProfileController::class, 'slc'])->name('professional.profile.slc');
        Route::post('/save.slc', [ProfileController::class, 'saveSlc'])->name('professional.profile.save.slc');
        Route::get('/tslc', [ProfileController::class, 'tslc'])->name('professional.profile.tslc');
        Route::post('/save.tslc', [ProfileController::class, 'saveTslc'])->name('professional.profile.save.tslc');
        Route::get('/pcl', [ProfileController::class, 'pcl'])->name('professional.profile.pcl');
        Route::post('/save.pcl', [ProfileController::class, 'savePcl'])->name('professional.profile.save.pcl');
        Route::get('/bachelor', [ProfileController::class, 'bachelor'])->name('professional.profile.bachelor');
        Route::post('/save.bachelor', [ProfileController::class, 'saveBachelor'])->name('professional.profile.save.bachelor');
        Route::get('/master', [ProfileController::class, 'master'])->name('professional.profile.master');
        Route::post('/save.master', [ProfileController::class, 'saveMaster'])->name('professional.profile.save.master');
        Route::get('/voucher', [ProfileController::class, 'voucher'])->name('professional.profile.voucher');
        Route::post('/save.voucher', [ProfileController::class, 'saveVoucher'])->name('professional.profile.save.voucher');
        Route::post('/voucher.imageDelete', [ProfileController::class, 'voucherImageDelete'])->name('professional.profile.voucher.imageDelete');
    });

    Route::prefix('exam')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('professional.exam.dashboard');
        Route::get('/tslc', [ExamController::class, 'tslc'])->name('professional.exam.tslc');
        Route::get('/history', action: [ExamController::class, 'history'])->name('professional.exam.history');
        Route::get('/application', [ExamController::class, 'application'])->name('professional.exam.application');
        Route::post('/save.application', [ExamController::class, 'saveApplication'])->name('professional.exam.save.application');
        Route::get('/khalti.verify', [ExamController::class, 'khaltiVerify'])->name('professional.exam.khalti.verify');
        Route::get('/status', [ExamController::class, 'status'])->name('professional.exam.status');
        // Route::post('/voucher.imageDelete', [ExamController::class, 'voucherImageDelete'])->name('professional.exam.voucher.imageDelete');

    });

    Route::prefix('kyc')->group(function () {
        Route::get('/', [KycController::class, 'index'])->name('professional.kyc');
        Route::post('/save.kyc', [KycController::class, 'saveKyc'])->name('professional.kyc.save');
    });

    Route::prefix('admitcard')->group(function () {
        Route::get('/', [AdmitCardController::class, 'admitcard'])->name('professional.admitcard');
    });

    Route::prefix('certificate')->group(function () {
        Route::get('/', [CertificateController::class, 'list'])->name('professional.certificate');
        // Route::get('/', [CertificateController::class, 'certificate'])->name('professional.certificate');
        // Route::post('/apply', [CertificateController::class, 'saveCertificateApply'])->name('professional.certificate.apply');

        // Route::prefix('verification')->group(function () {
        //     Route::get('/', [CertVerificationRequestController::class, 'list'])->name('professional.certificate.verification.list');
        //     Route::get('/form', [CertVerificationRequestController::class, 'form'])->name('professional.certificate.verification.form');
        //     Route::post('/save', [CertVerificationRequestController::class, 'save'])->name('professional.certificate.verification.save');
        //     Route::get('/addedit', [CertVerificationRequestController::class, 'addEdit'])->name('professional.certificate.verification.addedit');
        //     Route::post('/store', [CertVerificationRequestController::class, 'store'])->name('professional.certificate.verification.store');

        // });

        // Route::get('/search', [CertificateController::class, 'renewSearchPage'])->name('professional.certificate.search');
        // Route::get('/{certificate}', [CertificateController::class, 'detail'])->name('professional.certificate.detail');

        // Route::get('/renew/list', [CertificateRenewRequestController::class, 'list'])->name('professional.certificate_renew.list');

        // Route::get('/renew/search', [CertificateController::class, 'renewSearchPage'])->name('professional.certificate.renew.search');
        // Route::post('/renew/search', [CertificateController::class, 'renewSearch'])->name('professional.certificate.renew.search.post');
        // Route::get('/renew/{certificate}', [CertificateController::class, 'renewDetail'])->name('professional.certificate.renew.detail');
        // Route::post('/renew/{certificate}/submit', [CertificateController::class, 'submitRenew'])->name('professional.certificate.renew.submit');
        //

    });

    // Route::prefix('certificaterenew')->group(function () {
    //     Route::get('/form', [CertificateRenewRequestController::class, 'form'])->name('professional.certificate.renew.request');
    //     Route::post('/form', [CertificateRenewRequestController::class, 'form'])->name('professional.certificate.renew.request');
    //     Route::post('/save', [CertificateRenewRequestController::class, 'store'])->name('professional.certificate.renew.save');
    //     Route::get('/print', [CertificateRenewRequestController::class, 'print'])->name('professional.certificate.renew.print');
    // });

   

    Route::prefix('idcard')->group(function () {
        Route::get('/', [ICardController::class, 'index'])->name('professional.idcard');
    });

    Route::prefix('payment')->group(function () {
        Route::get('/initiate', [KhaltiController::class, 'initiate'])->name('professional.khalti.initiate');
        Route::get('/callback', [KhaltiController::class, 'callback'])->name('professional.khalti.callback');
    });

});