<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DistrictController;
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
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthController::class, 'login'])->name('admin.login');
Route::post('check-login', [AuthController::class, 'checkLogin'])->name('admin.checkLogin');
Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('admin.forgot.password');
Route::post('otp', [AuthController::class, 'otp'])->name('admin.otp');
Route::get('reset-password', [AuthController::class, 'resetPassword'])->name('admin.reset.password');
Route::post('save-password', [AuthController::class, 'savePassword'])->name('admin.save.password');

Route::group(['middleware' => 'auth.admin'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::prefix('account')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('admin.account.setting');
        Route::post('/update-account', [AccountController::class, 'addAction'])->name('admin.account.update');
        Route::post('/update-password', [AccountController::class, 'updatePassword'])->name('admin.account.update.password');
        Route::get('/logout', [AccountController::class, 'logout'])->name('admin.logout');
    });

    Route::prefix('module')->group(function () {
        Route::get('/', [ModuleController::class, 'list'])->name('admin.module');
        Route::get('/add.edit', [ModuleController::class, 'addEdit'])->name('admin.module.addEdit');
        Route::post('/store', [ModuleController::class, 'store'])->name('admin.module.store');
        Route::post('/delete', [ModuleController::class, 'delete'])->name('admin.module.delete');
    });

    Route::prefix('role')->group(function () {
        Route::get('/', [RoleController::class, 'list'])->name('admin.role');
        Route::get('/add.edit', [RoleController::class, 'addEdit'])->name('admin.role.addEdit');
        Route::post('/store', [RoleController::class, 'store'])->name('admin.role.store');
        Route::post('/delete', [RoleController::class, 'delete'])->name('admin.role.delete');
    });

    Route::prefix('permission')->group(function () {
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

    Route::prefix('user')->group(function () {
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
    });

    Route::prefix('applicant')->group(function () {
        Route::get('/', [ApplicantController::class, 'list'])->name('admin.applicant');
        Route::get('/new', [ApplicantController::class, 'list'])->name('admin.applicant.new');
        Route::get('/pending', [ApplicantController::class, 'pending'])->name('admin.applicant.pending');
        Route::get('/approved', [ApplicantController::class, 'approved'])->name('admin.applicant.approved');
        Route::get('/rejected', [ApplicantController::class, 'rejected'])->name('admin.applicant.rejected');
        Route::get('/add.edit', [ApplicantController::class, 'addEdit'])->name('admin.applicant.addEdit');
        Route::post('/store', [ApplicantController::class, 'store'])->name('admin.applicant.store');
        Route::post('/delete', [ApplicantController::class, 'delete'])->name('admin.applicant.delete');
        Route::post('/status', [ApplicantController::class, 'saveStatus'])->name('admin.applicant.save.status');
        Route::post('/state', [ApplicantController::class, 'saveState'])->name('admin.applicant.save.state');
        Route::get('/export', [ApplicantController::class, 'export'])->name('admin.applicant.export');
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

});