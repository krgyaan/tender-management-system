<?php

use App\Models\EmdBg;
use App\Models\Project;
use App\Exports\BgExport;
use Illuminate\Http\Request;
use App\Exports\ProjectExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Exports\EmployeeImprestExport;
use App\Http\Controllers\TQController;
use App\Http\Controllers\RFQController;
use App\Http\Controllers\EmdsController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Gst3BController;
use App\Http\Controllers\GstR1Controller;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RaMgmtController;
use App\Http\Controllers\ReqExtController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\PhyDocsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TdsFormController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WebsitesController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowUpsController;
use App\Http\Controllers\TenderFeeController;
use App\Http\Controllers\WorkorderController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\GoogletoolController;
use App\Http\Controllers\TeamLeaderController;
use App\Http\Controllers\TenderInfoController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\SubmitQueryController;
use App\Http\Controllers\EmdDashboardController;
use App\Http\Controllers\FixedExpenseController;
use App\Http\Controllers\LoanAdvancesController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PricingSheetController;
use App\Http\Controllers\BidSubmissionController;
use App\Http\Controllers\CacheOptimizeController;
use App\Http\Controllers\TlPerformanceController;
use App\Http\Controllers\KickoffmeetingController;
use App\Http\Controllers\OemPerformanceController;
use App\Http\Controllers\ClientDirectoryController;
use App\Http\Controllers\CostingApprovalController;
use App\Http\Controllers\EmployeeImprestController;
use App\Http\Controllers\CourierDashboardController;
use App\Http\Controllers\AccountsChecklistController;
use App\Http\Controllers\BatteryPriceSheetController;
use App\Http\Controllers\ContractAgreementController;
use App\Http\Controllers\DocumentSubmittedController;
use App\Http\Controllers\AccountsPerformanceController;
use App\Http\Controllers\BusinessPerformanceController;
use App\Http\Controllers\CustomerPerformanceController;
use App\Http\Controllers\EmployeePerformanceController;
use App\Http\Controllers\LocationPerformanceController;
use App\Http\Controllers\OperationPerformanceController;


Route::view('/', 'auth.login')->name('/');

// Public routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes requiring authentication
Route::middleware('auth')->group(function () {

    Route::any('/employee/performance', [EmployeePerformanceController::class, 'performance'])->name('employee/performance');
    Route::any('/team-leader/performance', [TlPerformanceController::class, 'performance'])->name('team-leader/performance');
    Route::any('/operation/performance', [OperationPerformanceController::class, 'performance'])->name('operation/performance');
    Route::any('/accounts/performance', [AccountsPerformanceController::class, 'performance'])->name('accounts/performance');
    Route::any('/oem/performance', [OemPerformanceController::class, 'performance'])->name('oem/performance');
    Route::any('/business/performance', [BusinessPerformanceController::class, 'performance'])->name('business/performance');
    Route::any('/customer/performance', [CustomerPerformanceController::class, 'performance'])->name('customer/performance');
    Route::any('/location/performance', [LocationPerformanceController::class, 'performance'])->name('location/performance');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,coordinator')->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/dashboard/admin', 'index')->name('dashboard.admin');
            Route::any('/admin/user/all', 'allUsers')->name('admin/user/all');
            Route::any('/admin/user/create', 'createUser')->name('admin/user/create');
            Route::any('/admin/user/edit/{id}', 'editUser')->name('admin/user/edit');
            Route::any('/admin/user/delete/{id}', 'deleteUser')->name('admin/user/delete');
        });

        Route::prefix('admin')->group(function () {
            Route::resource('statuses', StatusController::class);
            Route::resource('industries', IndustryController::class);

            Route::controller(OrganizationController::class)->group(function () {
                Route::match(['get', 'post'], 'org-industries/add', 'addIndustry')->name('org-industries.add');
                Route::get('org-industries/edit/{id}', 'editIndustry')->name('org-industries.edit');
                Route::put('org-industries/update/{id}', 'updateIndustry')->name('org-industries.update');
                Route::post('org-industries/delete/{id}', 'deleteIndustry')->name('org-industries.delete');
                Route::resource('organizations', OrganizationController::class);
            });

            Route::controller(ItemController::class)->group(function () {
                Route::post('items/approve/{id}', 'approve')->name('items.approve');
                Route::post('items/delete/{id}', 'delete')->name('items.delete');
                Route::match(['get', 'post'], 'items/add-heading', 'addHeading')->name('items.add-heading');
                Route::get('headings/edit/{id}', 'editHeading')->name('headings.edit');
                Route::put('headings/update/{id}', 'updateHeading')->name('headings.update');
                Route::post('headings/delete/{id}', 'deleteHeading')->name('headings.delete');
                Route::get('items/get-headings', [ItemController::class, 'getHeadings'])->name('items.get-headings');
                Route::resource('items', ItemController::class);
            });
            Route::controller(VendorController::class)->group(function () {
                Route::resource('vendors', VendorController::class)->except(['edit', 'update']);
                Route::any('vendors/edit/{id}', 'edit')->name('vendors.edit');
                Route::any('vendors/update/{id}', 'update')->name('vendors.update');
                Route::delete('/vendors/delete-account/{id}', 'deleteAccount')->name('vendors.delete-account');
                Route::delete('/vendors/delete-gst/{id}', 'deleteGst')->name('vendors.delete-gst');
                Route::delete('/vendors/delete-contact/{id}', 'deleteContact')->name('vendors.delete-contact');
            });

            Route::resource('locations', LocationController::class);

            Route::resource('websites', WebsitesController::class);
            Route::resource('submitteddocs', DocumentSubmittedController::class);
            Route::controller(EmployeeImprestController::class)->group(function () {
                Route::get('categories', 'categories')->name('categories');
                Route::post('categories_add', 'categories_add')->name('categories_add');
                Route::get('category_del/{id}', 'category_del')->name('category_del');
                Route::post('category_edit', 'category_edit')->name('category_edit');
            });
            Route::controller(FinanceController::class)->group(function () {
                Route::get('documenttype', 'documenttype')->name('documenttype');
                Route::post('documenttype_add', 'documenttype_add')->name('documenttype_add');
                Route::get('documenttype_del/{id}', 'documenttype_del')->name('documenttype_del');
                Route::post('documenttype_edit', 'documenttype_edit')->name('documenttype_edit');
                Route::get('financialyear', 'financialyear')->name('financialyear');
                Route::post('financialyear_add', 'financialyear_add')->name('financialyear_add');
                Route::get('financialyear_del/{id}', 'financialyear_del')->name('financialyear_del');
                Route::post('financialyear_edit', 'financialyear_edit')->name('financialyear_edit');
            });
            Route::controller(FollowUpsController::class)->group(function () {
                Route::get('followups/followup-categories', 'FollowupFor')->name('followup-categories');
                Route::post('followups/followup-categories/add', 'FollowupForAdd')->name('followup-categories-add');
                Route::post('followups/followup-categories/update', 'FollowupForUpdate')->name('followup-categories-update');
                Route::delete('followups/followup-categories/delete/{id}', 'FollowupForDelete')->name('followup-categories-destroy');
            });
        });
    });

    Route::prefix('admin')->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::get('download-projects', function (Request $request) {
            $projects = Project::orderBy('id', 'desc')->get(['po_no', 'location_id', 'project_code', 'project_name', 'po_date']);
            return Excel::download(new ProjectExport($projects), 'projects.xlsx');
        })->name('download-projects');

        Route::get('/admin/cache-optimize', [CacheOptimizeController::class, 'optimize']);
    });

    Route::middleware('role:coordinator')->group(function () {
        Route::get('/dashboard/coordinator', [CoordinatorController::class, 'index'])->name('dashboard.coordinator');
    });

    Route::middleware('role:account')->group(function () {
        Route::get('/dashboard/account', [AccountantController::class, 'index'])->name('dashboard.account');
    });

    Route::middleware('role:employee')->group(function () {
        Route::get('/dashboard/employee', [EmployeeController::class, 'index'])->name('dashboard.employee');
    });

    Route::middleware('role:team-leader')->group(function () {
        Route::get('/dashboard/team-leader', [TeamLeaderController::class, 'index'])->name('dashboard.team-leader');
    });

    Route::get('/tender/data/{type}', [TenderInfoController::class, 'getTenderData'])->name('tender.data');

    Route::prefix('tender')->group(function () {
        Route::resource('courier', CourierDashboardController::class);
        Route::any('courier/despatch/{id}', [CourierDashboardController::class, 'despatch'])->name('courier.despatch');
        Route::any('courier/updateStatus', [CourierDashboardController::class, 'updateStatus'])->name('courier.updateStatus');
        Route::resource('phydocs', PhyDocsController::class);

        Route::resource('pricingsheets', PricingSheetController::class);
        Route::post('pricingsheets/step1', [PricingSheetController::class, 'step1'])->name('pricingsheets.post.step1');
        Route::get('pricingsheets/step2/{id}', [PricingSheetController::class, 'step2'])->name('pricingsheets.get.step2');
        Route::post('pricingsheets/step2', [PricingSheetController::class, 'step2'])->name('pricingsheets.post.step2');
        Route::get('pricingsheets/step3/{id}', [PricingSheetController::class, 'step3'])->name('pricingsheets.get.step3');
        Route::post('pricingsheets/step3', [PricingSheetController::class, 'step3'])->name('pricingsheets.post.step3');

        Route::resource('emds', EmdsController::class)->except('create');
        Route::get('emds/create/{id?}', [EmdsController::class, 'create'])->name('emds.create');
        Route::get('emds/create-two', [EmdsController::class, 'getStep1'])->name('emds.get.step2');
        Route::post('emds/create-two', [EmdsController::class, 'postStep1'])->name('emds.post.step1');
        Route::any('emds/bank-transfer-status/{id}', [EmdsController::class, 'BankTransferStatus'])->name('bank-transfer-status');
        Route::any('emds/cheque-status/{id}', [EmdsController::class, 'ChequeStatus'])->name('cheque-status');
        Route::any('emds/dd-status/{id}', [EmdsController::class, 'DDStatus'])->name('dd-status');
        Route::any('emds/pop-status/{id}', [EmdsController::class, 'PopStatus'])->name('pop-status');

        Route::controller(TenderFeeController::class)->group(function () {
            Route::get('tender-fees/create/{id?}',  'create')->name('tender-fees.create');
            Route::match(['get', 'put', 'patch'], 'tender-fees/update/{id}',  'update')->name('tender-fees.edit');
            Route::post('tender-fees/bt/store',  'BTstore')->name('tender-fees.bt.store');
            Route::post('tender-fees/pop/store',  'Popstore')->name('tender-fees.pop.store');
            Route::post('tender-fees/status',  'DDstore')->name('tender-fees.dd.store');
            Route::post('tender-fees/dd/store',  'tender_fee_status')->name('tender-fees.status');
            Route::resource('tender-fees', TenderFeeController::class)->except('create');
        });

        Route::get('emds-dashboard', [EmdDashboardController::class, 'dashboard'])->name('emds-dashboard.index');
        Route::get('emds-dashboard/bank-gurantee', [EmdDashboardController::class, 'BG'])->name('emds-dashboard.bg');
        Route::get('emds-dashboard/demand-draft', [EmdDashboardController::class, 'DD'])->name('emds-dashboard.dd');
        Route::get('emds-dashboard/bank-transfer', [EmdDashboardController::class, 'BT'])->name('emds-dashboard.bt');
        Route::get('emds-dashboard/pay-on-portal', [EmdDashboardController::class, 'POP'])->name('emds-dashboard.pop');
        Route::get('emds-dashboard/cheque', [EmdDashboardController::class, 'CHQ'])->name('emds-dashboard.chq');
        Route::get('emds-dashboard/fdr', [EmdDashboardController::class, 'FDR'])->name('emds-dashboard.fdr');

        Route::get('emds-dashboard/show/{id}', [EmdDashboardController::class, 'show'])->name('emds-dashboard.show');
        Route::get('emds-dashboard/{id}/edit', [EmdDashboardController::class, 'edit'])->name('emds-dashboard.edit');
        Route::any('emds-dashboard/{id}/update', [EmdDashboardController::class, 'update'])->name('emds-dashboard.update');
        Route::delete('emds-dashboard/delete/{id}', [EmdDashboardController::class, 'destroy'])->name('emds-dashboard.destroy');
        Route::get('download-bgs', function () {
            Log::info('Downloading BGs Started');
            $bgs = EmdBg::all();
            Log::info('Collected All BGs ' . count($bgs));
            return Excel::download(new BgExport($bgs), 'allbgs.xlsx');
        })->name('download-bgs');

        Route::get('emds/export/bt', [EmdDashboardController::class, 'export_bt'])->name('emds.export.bt');
        Route::get('emds/export/pop', [EmdDashboardController::class, 'export_pop'])->name('emds.export.pop');

        Route::any('dd-status/update/{id}', [EmdDashboardController::class, 'DemandDraftDashboard'])->name('dd-status.update');
        Route::any('cheque-status/update/{id}', [EmdDashboardController::class, 'ChequeDashboard'])->name('cheque-status.update');
        Route::any('bt-action/{id}', [EmdDashboardController::class, 'BankTransferDashboard'])->name('bt-action');
        Route::any('pop-action/{id}', [EmdDashboardController::class, 'PayOnPortalDashboard'])->name('pop-action');
        Route::any('bg-action/{id}', [EmdDashboardController::class, 'BankGuaranteeDashboard'])->name('bg-action');
        Route::any('dd-action/{id}', [EmdDashboardController::class, 'DemandDraftDashboard'])->name('dd-action');
        Route::any('cheque-action/{id}', [EmdDashboardController::class, 'ChequeDashboard'])->name('cheque-action');

        Route::any('emds/bg/old-entry', [EmdsController::class, 'BgOldEntry'])->name('bg-old-entry');
        Route::any('emds/dd/old-entry', [EmdsController::class, 'DdOldEntry'])->name('dd-old-entry');

        Route::any('emds/cheque/ott-entry', [EmdsController::class, 'ChequeOTTEntry'])->name('cheque-ott-entry');
        Route::any('emds/bg/ott-entry', [EmdsController::class, 'BgOTTEntry'])->name('bg-ott-entry');
        Route::any('emds/dd/ott-entry', [EmdsController::class, 'DdOTTEntry'])->name('dd-ott-entry');

        Route::resource('results', ResultController::class);
        Route::post('/results/technical', [ResultController::class, 'storeTechnicalResult'])->name('results.storeTechnical');
        Route::post('/results/final', [ResultController::class, 'storeFinalResult'])->name('results.storeFinal');
        Route::resource('checklist', ChecklistController::class);

        Route::resource('followups', FollowUpsController::class);
        Route::delete('followups/person-delete/{id}', [FollowUpsController::class, 'deletePerson'])->name('followups.person-delete');
        Route::post('followups/status-update/{id}', [FollowUpsController::class, 'updateFollowup'])->name('updateFollowup');
    });

    Route::prefix('accounts')->group(function () {
        Route::resource('lead', LeadController::class);
    });


    Route::prefix('accounts')->group(function () {
        // Checklists
        Route::prefix('checklists')->name('checklists.')
            ->controller(AccountsChecklistController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{checklist}', 'show')->name('show');
                Route::get('/{checklist}/edit', 'edit')->name('edit');
                Route::put('/{checklist}', 'update')->name('update');
                Route::delete('/{checklist}', 'destroy')->name('destroy');

                // Custom checklist actions
                Route::post('/{checklist}/resp-remark', 'storeResponsibilityRemark')->name('resp.remark');
                Route::post('/{checklist}/acct-remark', 'storeAccountabilityRemark')->name('acct.remark');
                Route::post('/{checklist}/upload-result', 'uploadResultFile')->name('upload.result');
                Route::post('/{checklist}/complete-resp', 'completeResponsibility')->name('complete.resp');
                Route::post('/{checklist}/complete-acct', 'completeAccountability')->name('complete.acct');
                Route::get('/{checklist}/download', 'download')->name('download');
            });

        // Fixed Expenses
        Route::prefix('fixed-expenses')->name('fixed-expenses.')
            ->controller(FixedExpenseController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{fixedExpense}', 'show')->name('show');
                Route::get('/{fixedExpense}/edit', 'edit')->name('edit');
                Route::put('/{fixedExpense}', 'update')->name('update');
                Route::delete('/{fixedExpense}', 'destroy')->name('destroy');
                Route::get('/status/{fixedExpense}', 'status')->name('status.index');
                Route::put('/status/update/{fixedExpense}', 'updateStatus')->name('status.update');
            });

        // GST 3B
        Route::prefix('gst3b')->name('gst3b.')
            ->controller(Gst3BController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::post('/{id}/upload2a', 'upload2a')->name('upload2a');
                Route::post('/{id}/uploadPaymentChallan', 'uploadPaymentChallan')->name('uploadPaymentChallan');
                Route::post('/{id}/approve', 'approve')->name('approve');
                Route::post('/{id}/reject', 'reject')->name('reject');
            });

        //Gst r1 routes
        Route::prefix('gstr1')->name('gstr1.')
            ->controller(GstR1Controller::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{gstR1}/edit', 'edit')->name('edit');
                Route::get('/show/{gstR1}', 'show')->name('show');
                Route::post('/update/{gstR1}', 'update')->name('update');
                Route::delete('/{gstR1}', 'destroy')->name('destroy');
            });
    });

    Route::resource('tender/rfq', RFQController::class)->except('create');
    Route::get('tender/rfq/create/{id?}', [RFQController::class, 'create'])->name('rfq.create');
    Route::delete('deleteDoc/{id}', [PhyDocsController::class, 'deleteDoc'])->name('deleteDoc');
    Route::delete('deleteSlip/{id}', [PhyDocsController::class, 'deleteSlip'])->name('deleteSlip');
    Route::any('deleteVendor/{id}', [RFQController::class, 'deleteVendor'])->name('deleteVendor');
    Route::any('getVendorDetails', [VendorController::class, 'getVendorDetails'])->name('getVendorDetails');
    Route::get('/get-vendors-by-org', [VendorController::class, 'getVendorsByOrg'])->name('getVendorsByOrg');
    Route::any('/rfq/recipient/{id}', [RFQController::class, 'rfqRecipients'])->name('rfq.recipient');
    Route::any('/rfq/receipt', [RFQController::class, 'RFQReceipts'])->name('rfq.receipt');

    Route::delete('delTechical/{id}', [RFQController::class, 'delTechical'])->name('delTechical');
    Route::delete('delBoq/{id}', [RFQController::class, 'delBoq'])->name('delBoq');
    Route::delete('delScope/{id}', [RFQController::class, 'delScope'])->name('delScope');
    Route::delete('delMaf/{id}', [RFQController::class, 'delMaf'])->name('delMaf');
    Route::delete('delMii/{id}', [RFQController::class, 'delMii'])->name('delMii');

    Route::any('getTenderDetails', [RFQController::class, 'getTenderDetails'])->name('getTenderDetails');
    Route::resource('tender', TenderInfoController::class);
    Route::post('updateStatus', [TenderInfoController::class, 'updateStatus'])->name('tender.updateStatus');
    Route::get('/approve/tender', [TenderInfoController::class, 'tlapproval'])->name('tlapproval');
    Route::get('/tender-approval-form/{id}', [TenderInfoController::class, 'tlApprovalForm'])->name('tlApprovalForm');
    Route::post('/tlapproved', [TenderInfoController::class, 'tlapproved'])->name('tlapproved');
    Route::delete('tender/item/{id}', [TenderInfoController::class, 'deleteItem'])->name('tender.item.delete');
    Route::delete('tender/doc/{id}', [TenderInfoController::class, 'deleteDoc'])->name('tender.doc.delete');
    Route::post('/generate-tender-name', [TenderInfoController::class, 'checkAndGenerateTenderName']);

    Route::get('tender/extension/create/{id?}', [ReqExtController::class, 'create'])->name('extension.create');
    Route::post('tender/extension/store', [ReqExtController::class, 'store'])->name('extension.store');
    Route::get('tender/submit_query/create/{id?}', [SubmitQueryController::class, 'create'])->name('submit_query.create');
    Route::post('tender/submit_query/store', [SubmitQueryController::class, 'store'])->name('submit_query.store');

    Route::get('tender/info/create/{id}', [TenderInfoController::class, 'infoCreate'])->name('tender.info.create');
    Route::put('tender/info/update/{id}', [TenderInfoController::class, 'infoUpdate'])->name('tender.info.update');

    Route::resource('profile', ProfileController::class);
    Route::post('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change_password');

    // other developer routes
    Route::get('admin/employeeimprest', [EmployeeImprestController::class, 'employeeimprest'])->name('employeeimprest');
    Route::get('admin/employeeimprest_add', [EmployeeImprestController::class, 'employeeimprest_add'])->name('employeeimprest_add');
    Route::post('employeeimprest_post', [EmployeeImprestController::class, 'employeeimprest_post'])->name('employeeimprest_post');
    Route::get('admin/employeeimprest_delete/{id}', [EmployeeImprestController::class, 'employeeimprest_delete'])->name('employeeimprest_delete');
    Route::get('admin/employeeimprest_edit/{id}', [EmployeeImprestController::class, 'employeeimprest_edit'])->name('employeeimprest_edit');
    Route::post('admin/employeeimprest_update', [EmployeeImprestController::class, 'employeeimprest_update'])->name('employeeimprest_update');

    Route::post('admin/add_proof', [EmployeeImprestController::class, 'add_proof'])->name('add_proof');
    Route::get('admin/get_proof/{id}', [EmployeeImprestController::class, 'get_proof']);

    Route::get('admin/employeeimprest_account', [EmployeeImprestController::class, 'employeeimprest_account'])->name('employeeimprest_account');
    Route::get('admin/employeeimprest_account_add/{id}', [EmployeeImprestController::class, 'employeeimprest_account_add'])->name('employeeimprest_account_add');
    Route::post('admin/employeeimprest_amount_post', [EmployeeImprestController::class, 'employeeimprest_amount_post'])->name('employeeimprest_amount_post');
    Route::get('admin/employeeimprest_account_delete/{id}', [EmployeeImprestController::class, 'employeeimprest_account_delete'])->name('employeeimprest_account_delete');
    Route::get('admin/employeeimprest_account_edit/{id}', [EmployeeImprestController::class, 'employeeimprest_account_edit'])->name('employeeimprest_account_edit');
    Route::post('admin/employeeimprest_account_update', [EmployeeImprestController::class, 'employeeimprest_account_update'])->name('employeeimprest_account_update');
    Route::get('admin/employeeimprest_dashboard/{id}', [EmployeeImprestController::class, 'employeeimprest_dashboard'])->name('employeeimprest_dashboard');
    Route::post('admin/employee_status', [EmployeeImprestController::class, 'employee_status'])->name('employee_status');
    Route::post('admin/tally_status', [EmployeeImprestController::class, 'tally_status'])->name('tally_status');
    Route::post('admin/proof_status', [EmployeeImprestController::class, 'proof_status'])->name('proof_status');
    Route::post('admin/employeeimprest_remark', [EmployeeImprestController::class, 'employeeimprest_remark'])->name('employeeimprest_remark');
    Route::get('admin/employeeimprest_amount_project', [EmployeeImprestController::class, 'employeeimprest_amount_project']);
    Route::post('admin/imprest/dateFilter', [EmployeeImprestController::class, 'dateFilter'])->name('dateFilter');
    Route::post('admin/imprest/dateFilterAcc', [EmployeeImprestController::class, 'dateFilterAcc'])->name('dateFilterAcc');
    Route::get('/download-employee-imprest', function (Request $request) {
        $user = Auth::user();
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $nameId = $request->query('name_id');
        Log::info('Exporting employee imprest data', ['user' => $user->id, 'start_date' => $startDate, 'end_date' => $endDate, 'name_id' => $nameId]);
        return Excel::download(new EmployeeImprestExport($user, $startDate, $endDate, $nameId), 'employee_imprest.xlsx');
    });
    Route::get('employee/payment-history/{id?}', [EmployeeImprestController::class, 'paymentHistory'])->name('payment-history');
    Route::get('employee/imprest-voucher/{id?}', [EmployeeImprestController::class, 'imprestVoucher'])->name('imprest-voucher');
    Route::post('employee/voucher-view', [EmployeeImprestController::class, 'voucherView'])->name('voucher-view');
    Route::any('imprest/account-sign/{id}', [EmployeeImprestController::class, 'accountSignedVoucher'])->name('account-sign');
    Route::post('imprest/admin-sign/{id}', [EmployeeImprestController::class, 'adminSignedVoucher'])->name('admin-sign');
    Route::get('view-proof/{proof?}', function ($proof) {
        return view('employeeimprest.view-proof', compact('proof'));
    })->name('view-proof');
    Route::delete('imprest-delete/{id}', [EmployeeImprestController::class, 'imprestDelete'])->name('imprest.delete');
    Route::delete('payment-history/delete/{id}', [EmployeeImprestController::class, 'historyDelete'])->name('employeeimprest.delete-history');

    Route::get('admin/pqr_dashboard', [EmployeeImprestController::class, 'pqr_dashboard'])->name('pqr_dashboard');
    Route::get('admin/pqr_dashboard_add', [EmployeeImprestController::class, 'pqr_dashboard_add'])->name('pqr_dashboard_add');
    Route::post('admin/pqr_dashboard_post', [EmployeeImprestController::class, 'pqr_dashboard_post'])->name('pqr_dashboard_post');
    Route::get('admin/pqr_delete/{id}', [EmployeeImprestController::class, 'pqr_delete'])->name('pqr_delete');
    Route::get('admin/pqr_edit/{id}', [EmployeeImprestController::class, 'pqr_edit'])->name('pqr_edit');
    Route::post('admin/pqr_dashboard_edit', [EmployeeImprestController::class, 'pqr_dashboard_edit'])->name('pqr_dashboard_edit');

    Route::post('admin/upload_po', [EmployeeImprestController::class, 'upload_po'])->name('upload_po');
    Route::post('admin/ac_upload_po', [EmployeeImprestController::class, 'ac_upload_po'])->name('ac_upload_po');

    Route::get('admin/finance', [FinanceController::class, 'finance'])->name('finance');
    Route::get('admin/finance_add', [FinanceController::class, 'finance_add'])->name('finance_add');
    Route::post('admin/finance_post', [FinanceController::class, 'finance_post'])->name('finance_post');
    Route::get('admin/finance_delete/{id}', [FinanceController::class, 'finance_delete'])->name('finance_delete');
    Route::get('admin/finance_edit/{id}', [FinanceController::class, 'finance_edit'])->name('finance_edit');
    Route::post('admin/finance_update', [FinanceController::class, 'finance_update'])->name('finance_update');
    Route::post('admin/image_uplode', [FinanceController::class, 'image_uplode'])->name('image_uplode');

    Route::get('admin/rent', [FinanceController::class, 'rent'])->name('rent');
    Route::get('admin/rent_add', [FinanceController::class, 'rent_add'])->name('rent_add');
    Route::post('admin/rent_post', [FinanceController::class, 'rent_post'])->name('rent_post');
    Route::get('admin/rent_delete/{id}', [FinanceController::class, 'rent_delete'])->name('rent_delete');
    Route::get('admin/rent_edit/{id}', [FinanceController::class, 'rent_edit'])->name('rent_edit');
    Route::post('admin/rent_update', [FinanceController::class, 'rent_update'])->name('rent_update');
    Route::get('/update-rent-status', [FinanceController::class, 'updateRentStatus'])->name('update.rent.status');
    Route::any('admin/rentexpirymail', [FinanceController::class, 'RentExpiryMail'])->name('rentexpirymail');

    Route::get('admin/loanadvances', [LoanAdvancesController::class, 'loanadvances'])->name('loanadvances');
    Route::get('admin/loanadvancesadd', [LoanAdvancesController::class, 'loanadvancesadd'])->name('loanadvancesadd');
    Route::post('admin/loanadvancescreate', [LoanAdvancesController::class, 'loanadvancescreate'])->name('loanadvancescreate');
    Route::get('admin/loanadvancesdelete/{id}', [LoanAdvancesController::class, 'loanadvancesdelete'])->name('loanadvancesdelete');
    Route::get('admin/loanadvancesupdate/{id}', [LoanAdvancesController::class, 'loanadvancesupdate'])->name('loanadvancesupdate');
    Route::post('admin/loanadvancesedit', [LoanAdvancesController::class, 'loanadvancesedit'])->name('loanadvancesedit');
    Route::get('admin/dueview/{id}', [LoanAdvancesController::class, 'dueview'])->name('dueview');
    Route::post('admin/dueemiadd', [LoanAdvancesController::class, 'dueemiadd'])->name('dueemiadd');
    Route::get('admin/dueemiupdate/{id}', [LoanAdvancesController::class, 'dueemiupdate'])->name('dueemiupdate');
    Route::put('admin/dueemiupdatepost/{id}', [LoanAdvancesController::class, 'dueemiupdatepost'])->name('dueemiupdatepost');
    Route::get('admin/dueemidelete/{id}', [LoanAdvancesController::class, 'dueemidelete'])->name('dueemidelete');
    Route::get('admin/loancloseupdate/{id}', [LoanAdvancesController::class, 'loancloseupdate'])->name('loancloseupdate');
    Route::post('admin/loancloseupdate_post', [LoanAdvancesController::class, 'loancloseupdate_post'])->name('loancloseupdate_post');
    Route::get('admin/tdsrecoveryview/{id}', [LoanAdvancesController::class, 'tdsrecoveryview'])->name('tdsrecoveryview');
    Route::post('admin/tdsrecoveryadd', [LoanAdvancesController::class, 'tdsrecoveryadd'])->name('tdsrecoveryadd');
    Route::get('admin/tdsrecoveryupdate/{id}', [LoanAdvancesController::class, 'tdsrecoveryupdate'])->name('tdsrecoveryupdate');
    Route::put('admin/tdsrecoveryupdatepost/{id}', [LoanAdvancesController::class, 'tdsrecoveryupdatepost'])->name('tdsrecoveryupdatepost');
    Route::get('admin/tdsrecoverydelete/{id}', [LoanAdvancesController::class, 'tdsrecoverydelete'])->name('tdsrecoverydelete');

    Route::get('admin/clientdirectory', [ClientDirectoryController::class, 'clientdirectory'])->name('clientdirectory');
    Route::get('admin/clientdirectoryadd', [ClientDirectoryController::class, 'clientdirectoryadd'])->name('clientdirectoryadd');
    Route::post('admin/clientdirectorycreate', [ClientDirectoryController::class, 'clientdirectorycreate'])->name('clientdirectorycreate');
    Route::get('admin/clientdirectorydelete/{id}', [ClientDirectoryController::class, 'clientdirectorydelete'])->name('clientdirectorydelete');
    Route::get('admin/clientdirectoryupdate/{id}', [ClientDirectoryController::class, 'clientdirectoryupdate'])->name('clientdirectoryupdate');
    Route::post('admin/clientdirectoryedit', [ClientDirectoryController::class, 'clientdirectoryedit'])->name('clientdirectoryedit');

    Route::get('admin/basicdetailview', [WorkorderController::class, 'basicdetailview'])->name('basicdetailview');
    Route::get('admin/basicdetailadd/{id}', [WorkorderController::class, 'basicdetailadd'])->name('basicdetailadd');
    Route::post('admin/basicdetailaddpost', [WorkorderController::class, 'basicdetailaddpost'])->name('basicdetailaddpost');
    Route::get('admin/basicdetailupdate/{id}', [WorkorderController::class, 'basicdetailupdate'])->name('basicdetailupdate');
    Route::post('admin/basicdetailupdatepost', [WorkorderController::class, 'basicdetailupdatepost'])->name('basicdetailupdatepost');
    Route::get('admin/basicdetaildelete/{id}', [WorkorderController::class, 'basicdetaildelete'])->name('basicdetaildelete');
    Route::get('admin/wodetailadd/{id}', action: [WorkorderController::class, 'wodetailadd'])->name('wodetailadd');
    Route::post('admin/wodetailaddpost', [WorkorderController::class, 'wodetailaddpost'])->name('wodetailaddpost');
    Route::get('admin/wodetailupdate/{id}', [WorkorderController::class, 'wodetailupdate'])->name('wodetailupdate');
    Route::post('admin/wodetailupdatepost', [WorkorderController::class, 'wodetailupdatepost'])->name('wodetailupdatepost');
    Route::get('admin/wodetaildelete/{id}', [WorkorderController::class, 'wodetaildelete'])->name('wodetaildelete');
    Route::get('admin/woacceptanceform/{id}', [WorkorderController::class, 'woacceptanceform'])->name('woacceptanceform');
    Route::post('admin/woacceptanceformpost', [WorkorderController::class, 'woacceptanceformpost'])->name('woacceptanceformpost');
    Route::get('admin/woacceptanceview', [WorkorderController::class, 'woacceptanceview'])->name('woacceptanceview');
    Route::get('admin/woacceptanceform_mail', [WorkorderController::class, 'woacceptanceform_mail'])->name('woacceptanceform_mail');
    Route::get('admin/woupdate/{id}', [WorkorderController::class, 'woupdate'])->name('woupdate');
    Route::post('admin/woupdate_post', [WorkorderController::class, 'woupdate_post'])->name('woupdate_post');
    Route::get('admin/wodashboardview', [WorkorderController::class, 'wodashboardview'])->name('wodashboardview');
    Route::get('admin/woviewbuttenfoa/{id}', [WorkorderController::class, 'woviewbuttenfoa'])->name('woviewbuttenfoa');
    Route::get('admin/kickmeeting_dashbord', [KickoffmeetingController::class, 'kickmeeting_dashbord'])->name('kickmeeting_dashbord');
    Route::get('admin/viewbutten_dashboard/{id}', [KickoffmeetingController::class, 'viewbutten_dashboard'])->name('viewbutten_dashboard');
    Route::get('admin/initiate_meeting/{id}', [KickoffmeetingController::class, 'initiate_meeting'])->name('initiate_meeting');
    Route::post('admin/initiate_meeting_post', [KickoffmeetingController::class, 'initiate_meeting_post'])->name('initiate_meeting_post');
    Route::post('admin/uplode_mom', [KickoffmeetingController::class, 'uplode_mom'])->name('uplode_mom');

    Route::get('admin/contract_dashboardview', [ContractAgreementController::class, 'contract_dashboardview'])->name('contract_dashboardview');
    Route::post('admin/uplade_contract_agereement', [ContractAgreementController::class, 'uplade_contract_agereement'])->name('uplade_contract_agereement');
    Route::get('admin/viewbuttencontract/{id}', [ContractAgreementController::class, 'viewbuttencontract'])->name('viewbuttencontract');

    Route::any('auto-followup', [FollowupsController::class, 'DailyFollowupMail'])->name('auto_followup');

    Route::get('admin/batteryprice', [BatteryPriceSheetController::class, 'batteryprice'])->name('batteryprice');
    Route::get('admin/batterypriceadd', [BatteryPriceSheetController::class, 'batterypriceadd'])->name('batterypriceadd');
    Route::get('admin/batterypricedelete/{id}', [BatteryPriceSheetController::class, 'batterypricedelete'])->name('batterypricedelete');
    Route::post('admin/batterypricecreate', [BatteryPriceSheetController::class, 'batterypricecreate'])->name('batterypricecreate');
    Route::post('admin/houseajexbatteryprice', [BatteryPriceSheetController::class, 'houseajexbatteryprice'])->name('houseajexbatteryprice');

    Route::get('admin/batteryinstallation', [BatteryPriceSheetController::class, 'batteryinstallation'])->name('batteryinstallation');
    Route::get('admin/batteryinstallationdelete/{id}', [BatteryPriceSheetController::class, 'batteryinstallationdelete'])->name('batteryinstallationdelete');
    Route::post('admin/batteryinstallationadd', [BatteryPriceSheetController::class, 'batteryinstallationadd'])->name('batteryinstallationadd');
    Route::post('admin/batteryinstallationupdate', [BatteryPriceSheetController::class, 'batteryinstallationupdate'])->name('batteryinstallationupdate');
    Route::post('admin/houseajexbatteryinstallation', [BatteryPriceSheetController::class, 'houseajexbatteryinstallation'])->name('houseajexbatteryinstallation');
    Route::get('admin/batteryinstallationview/{id}', [BatteryPriceSheetController::class, 'batteryinstallationview'])->name('batteryinstallationview');
    Route::post('admin/batteryinstallationviewadd', [BatteryPriceSheetController::class, 'batteryinstallationviewadd'])->name('batteryinstallationviewadd');
    Route::get('admin/batteryaccessories', [BatteryPriceSheetController::class, 'batteryaccessories'])->name('batteryaccessories');
    Route::get('admin/batteryaccessoriesdelete/{id}', [BatteryPriceSheetController::class, 'batteryaccessoriesdelete'])->name('batteryaccessoriesdelete');
    Route::post('admin/batteryaccessoriesadd', [BatteryPriceSheetController::class, 'batteryaccessoriesadd'])->name('batteryaccessoriesadd');
    Route::post('admin/batteryaccessoriesupdate', [BatteryPriceSheetController::class, 'batteryaccessoriesupdate'])->name('batteryaccessoriesupdate');
    Route::post('admin/houseajexbatteryaccessoriestion', [BatteryPriceSheetController::class, 'houseajexbatteryaccessoriestion'])->name('houseajexbatteryaccessoriestion');
    Route::get('admin/batteryaccessoriesview/{id}', [BatteryPriceSheetController::class, 'batteryaccessoriesview'])->name('batteryaccessoriesview');
    Route::post('admin/batteryaccessoriesviewadd', [BatteryPriceSheetController::class, 'batteryaccessoriesviewadd'])->name('batteryaccessoriesviewadd');
    Route::get('admin/batterypriceview', [BatteryPriceSheetController::class, 'batterypriceview'])->name('batterypriceview');

    Route::get('admin/tq_type', [TQController::class, 'tq_type'])->name('tq_type');
    Route::post('admin/tq_type_add', [TQController::class, 'tq_type_add'])->name('tq_type_add');
    Route::post('admin/tq_type_update', [TQController::class, 'tq_type_update'])->name('tq_type_update');
    Route::get('admin/tq_type_delete/{id}', [TQController::class, 'tq_type_delete'])->name('tq_type_delete');

    Route::get('admin/view_butten/{id}', [TQController::class, 'view_butten'])->name('view_butten');
    Route::get('admin/tq_dashboard', [TQController::class, 'tq_dashboard'])->name('tq_dashboard');
    Route::get('admin/tq_received_form/{id}', [TQController::class, 'tq_received_form'])->name('tq_received_form');
    Route::post('admin/tq_received_form_post', [TQController::class, 'tq_received_form_post'])->name('tq_received_form_post');
    Route::get('admin/tq_replied_form/{id}', [TQController::class, 'tq_replied_form'])->name('tq_replied_form');
    Route::post('admin/tq_replied_form_post', [TQController::class, 'tq_replied_form_post'])->name('tq_replied_form_post');
    Route::get('admin/tq_missed_form/{id}', [TQController::class, 'tq_missed_form'])->name('tq_missed_form');
    Route::post('admin/tq_missed_form_post', [TQController::class, 'tq_missed_form_post'])->name('tq_missed_form_post');

    Route::group(['prefix' => 'ra-management'], function () {
        Route::get('/', [RaMgmtController::class, 'index'])->name('ra.index');
        Route::get('/{id}', [RaMgmtController::class, 'show'])->name('ra.show');
        Route::post('/schedule/{id}', [RaMgmtController::class, 'schedule'])->name('ra-management.schedule');
        Route::post('/upload-result/{id}', [RaMgmtController::class, 'uploadResult'])->name('ra-management.upload-result');
    });
    Route::group(['prefix' => 'bid-submission'], function () {
        Route::get('/', [BidSubmissionController::class, 'index'])->name('bs.index');
        Route::get('/{id}', [BidSubmissionController::class, 'show'])->name('bs.show');
        Route::post('/submit-bid/{id}', [BidSubmissionController::class, 'submitBid'])->name('bs.submit-bid');
        Route::post('/mark-missed/{id}', [BidSubmissionController::class, 'markAsMissed'])->name('bs.mark-missed');
    });
    Route::prefix('costing-approval')->group(function () {
        Route::get('/', [CostingApprovalController::class, 'index'])->name('costing-approval.index');
        Route::post('/approve-sheet/{id}', [CostingApprovalController::class, 'approveSheet'])->name('costing-approval.approve');
        Route::get('/{id}', [CostingApprovalController::class, 'show'])->name('costing-approval.show');
    });

    Route::get('admin/googlesheet', [GoogletoolController::class, 'googlesheet'])->name('googlesheet');
    Route::get('admin/google_drivesSheetsAjex', [GoogletoolController::class, 'google_drivesSheetsAjex'])->name('google_drivesSheetsAjex');
    Route::get('admin/googletool/integrate', [GoogletoolController::class, 'integrate'])->name('integrate');
    Route::get('admin/googletoolsredirects', [GoogletoolController::class, 'googletoolsredirects'])->name('googletoolsredirects');
    Route::get('admin/googletoolview/{id}', [GoogletoolController::class, 'googletoolview'])->name('googletoolview');
    Route::post('admin/googletoolssave', [GoogletoolController::class, 'googletoolssave'])->name('googletoolssave');
    Route::put('admin/googletoolssubmitsheet', [GoogletoolController::class, 'submitSheet'])->name('googletoolssubmitsheet');

    Route::get('/upload-csv', [CsvImportController::class, 'showUploadForm']);
    Route::post('/upload-csv', [CsvImportController::class, 'upload'])->name('csv.upload');
});

// Error pages
Route::fallback(fn() => view('errors.404'));
