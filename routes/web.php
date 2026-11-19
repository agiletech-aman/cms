<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\CorporateOfficeController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Location\StateController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Location\RegionController;
use App\Http\Controllers\OldComplaintController;
use App\Http\Controllers\QuarterlyVisitController;
use App\Http\Controllers\RegionalManagerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WareHouseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth', 'prevent-sensitive-cache'])->group(function () {
    //Admin All Route
    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin/logout', 'destroy')->name('admin.logout');
        Route::get('/admin/profile', 'Profile')->name('admin.profile');
        Route::get('/edit/profile', 'EditProfile')->name('edit.profile');
        Route::post('/store/profile', 'StoreProfile')->name('store.profile');

        Route::get('/change/password', 'ChangePassword')->name('change.password');
        Route::post('/update/password', 'UpdatePassword')->name('update.password');
    });
    
    //User All Route
    Route::controller(AdminController::class)->group(function () {
        Route::get('/all/user', 'AllUser')->name('all.user')->middleware('permission:user.list');
        Route::get('/add/user', 'AddUser')->name('add.user')->middleware('permission:user.add');
        Route::post('/store/user', 'StoreUser')->name('store.user')->middleware('permission:user.add');
        Route::get('/edit/user/{id}', 'EditUser')->name('edit.user')->middleware('permission:user.edit');
        Route::post('/update/user', 'UpdateUser')->name('update.user')->middleware('permission:user.edit');
        Route::get('/delete/user/{id}', 'DeleteUser')->name('delete.user')->middleware('permission:user.delete');
        Route::get('/inactive/user/{id}', 'InactiveUser')->name('inactive.user')->middleware('permission:user.status');
        Route::get('/active/user/{id}', 'ActiveUser')->name('active.user')->middleware('permission:user.status');
        Route::get('/get/user', 'GetUser')->name('get.user');
        Route::get('/get/warehouse', 'GetWarehouse')->name('get.warehouse');
        Route::get('/user/passord/{id}', 'Viewchangepass')->name('user.passwordget');
        Route::post('/update/user/password',  'ChangeUserPassword')->name('updateuser.password');
    });

    //State All Route
    Route::controller(StateController::class)->group(function () {
        Route::get('add/state', 'AddState')->name('add.state')->middleware('permission:state.add');
        Route::post('store/state', 'StoreState')->name('store.state')->middleware('permission:state.add');
        Route::get('all/state', 'AllState')->name('all.state')->middleware('permission:state.list');
        Route::get('/edit/state/{id}', 'EditState')->name('edit.state')->middleware('permission:state.edit');
        Route::post('/update/state', 'UpdateState')->name('update.state')->middleware('permission:state.edit');
        Route::get('/delete/state/{id}', 'DeleteState')->name('delete.state')->middleware('permission:state.delete');

        Route::get('/region/ajax/{state_id}', 'GetRegion');
    });

    //Sub Location All Route
    Route::controller(RegionController::class)->group(function () {
        Route::get('all/region', 'AllRegion')->name('all.region')->middleware('permission:region.list');
        Route::get('add/region', 'AddRegion')->name('add.region')->middleware('permission:region.add');
        Route::post('store/region', 'StoreRegion')->name('store.region')->middleware('permission:region.add');
        Route::get('/edit/region/{id}', 'EditRegion')->name('edit.region')->middleware('permission:region.edit');
        Route::post('/update/region', 'UpdateRegion')->name('update.region')->middleware('permission:region.edit');
        Route::get('/delete/region/{id}', 'DeleteRegion')->name('delete.region')->middleware('permission:region.delete');
    });


    //Permission All Route
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/permission', 'AllPermission')->name('all.permission')->middleware('role:SuperAdmin');
        Route::get('/add/permission', 'AddPermission')->name('add.permission')->middleware('role:SuperAdmin');
        Route::post('/store/permission', 'StorePermission')->name('store.permission')->middleware('role:SuperAdmin');
        Route::get('/edit/permission/{id}', 'EditPermission')->name('edit.permission')->middleware('role:SuperAdmin');
        Route::post('/update/permission', 'UpdatePermission')->name('update.permission')->middleware('role:SuperAdmin');
        Route::get('/delete/permission/{id}', 'DeletePermission')->name('delete.permission')->middleware('role:SuperAdmin');
        Route::get('/all/roles', 'AllRoles')->name('all.roles')->middleware('role:SuperAdmin');
    });

    //Roles All Route
    Route::controller(RoleController::class)->group(function () {
        Route::get('/all/roles', 'AllRoles')->name('all.roles')->middleware('role:SuperAdmin');
        Route::get('/add/roles', 'AddRoles')->name('add.roles')->middleware('role:SuperAdmin');
        Route::post('/store/roles', 'StoreRoles')->name('store.roles')->middleware('role:SuperAdmin');
        Route::get('/edit/roles/{id}', 'EditRoles')->name('edit.roles')->middleware('role:SuperAdmin');
        Route::post('/update/roles', 'UpdateRoles')->name('update.roles')->middleware('role:SuperAdmin');
        Route::get('/delete/roles/{id}', 'DeleteRoles')->name('delete.roles')->middleware('role:SuperAdmin');

        Route::get('/add/roles/permission', 'AddRolesPermission')->name('add.roles.permission')->middleware('role:SuperAdmin');
        Route::get('/all/roles/permission', 'AllRolesPermission')->name('all.roles.permission')->middleware('role:SuperAdmin');
        Route::post('/role/permission/store', 'RolePermissionStore')->name('role.permission.store')->middleware('role:SuperAdmin');
        Route::get('/admin/edit/roles/{id}', 'AdminEditRoles')->name('admin.edit.roles')->middleware('role:SuperAdmin');
        Route::get('/admin/delete/roles/{id}', 'AdminDeleteRoles')->name('admin.delete.roles')->middleware('role:SuperAdmin');
        Route::post('/role/permission/update/{id}', 'RolePermissionUpdate')->name('role.permission.update')->middleware('role:SuperAdmin');
    });

    //Quarterly Visit All Route
    Route::controller(QuarterlyVisitController::class)->group(function () {
        Route::get('/all/quarterlyvisit', 'AllQuarterlyVisit')->name('all.quarterlyvisit')->middleware('permission:quarterlyVisit.list');
        Route::get('/add/quarterlyvisit', 'AddQuarterlyVisit')->name('add.quarterlyvisit')->middleware('permission:quarterlyVisit.add');
        Route::post('/store/quarterlyvisit', 'StoreQuarterlyVisit')->name('store.quarterlyvisit')->middleware('permission:quarterlyVisit.add');
        Route::get('/edit/quarterlyvisit/{id}', 'EditQuarterlyVisit')->name('edit.quarterlyvisit')->middleware('permission:quarterlyVisit.edit');
        Route::post('/update/quarterlyvisit/{id}', 'UpdateQuarterlyVisit')->name('update.quarterlyvisit')->middleware('permission:quarterlyVisit.edit');
        Route::get('/delete/quarterlyvisit/{id}', 'DeleteQuarterlyVisit')->name('delete.quarterlyvisit')->middleware('permission:quarterlyVisit.delete');
        Route::post('/update/quarterlyvisit/status/{id}', 'UpdateQuarterlyVisitStatus')->name('update.quarterlyvisit.status')->middleware('permission:quarterlyVisit.detail');

        Route::get('/detail/quarterlyvisit/{id}', 'DetailQuarterlyVisit')->name('detail.quarterlyvisit')->middleware('permission:quarterlyVisit.detail');
    });

    //WareHouse All Route
    Route::controller(WareHouseController::class)->group(function () {
        Route::get('/all/warehouse', 'AllWarehouse')->name('all.warehouse')->middleware('permission:warehouse.list');
        Route::get('/add/warehouse', 'AddWarehouse')->name('add.warehouse')->middleware('permission:warehouse.add');
        Route::post('/store/warehouse', 'StoreWarehouse')->name('store.warehouse')->middleware('permission:warehouse.add');
        Route::get('/edit/warehouse/{id}', 'EditWarehouse')->name('edit.warehouse')->middleware('permission:warehouse.edit');
        Route::post('/update/warehouse/{id}', 'UpdateWarehouse')->name('update.warehouse')->middleware('permission:warehouse.edit');
        Route::get('/delete/warehouse/{id}', 'DeleteWarehouse')->name('delete.warehouse')->middleware('permission:warehouse.delete');
        Route::get('/inactive/warehouse/{id}', 'InactiveWarehouse')->name('inactive.warehouse')->middleware('permission:warehouse.status');
        Route::get('/active/warehouse/{id}', 'ActiveWarehouse')->name('active.warehouse')->middleware('permission:warehouse.status');
    });


    //Item All Route
    Route::controller(ItemController::class)->group(function () {
        Route::get('/all/items', 'AllItems')->name('all.items')->middleware('permission:item.list');
        Route::get('/add/items', 'AddItems')->name('add.items')->middleware('permission:item.add');
        Route::post('/store/items', 'StoreItems')->name('store.items')->middleware('permission:item.add');
        Route::get('/edit/items/{id}', 'EditItems')->name('edit.items')->middleware('permission:item.edit');
        Route::post('/update/items/{id}', 'UpdateItems')->name('update.items')->middleware('permission:item.edit');
        Route::get('/delete/items/{id}', 'DeleteItems')->name('delete.items')->middleware('permission:item.delete');
        Route::post('/import/excel', 'ImportExcel')->name('import.excel');
    });

    //Category All Route
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/all/category', 'AllCategory')->name('all.category')->middleware('permission:category.list');
        Route::get('/add/category', 'AddCategory')->name('add.category')->middleware('permission:category.add');
        Route::post('/store/category', 'StoreCategory')->name('store.category')->middleware('permission:category.add');
        Route::get('/edit/category/{id}', 'EditCategory')->name('edit.category')->middleware('permission:category.edit');
        Route::post('/update/category/{id}', 'UpdateCategory')->name('update.category')->middleware('permission:category.edit');
        Route::get('/delete/category/{id}', 'DeleteCategory')->name('delete.category')->middleware('permission:category.delete');

        Route::get('/item/ajax/{cat_id}', 'GetItem');
    });

    //RegionalManager All Route
    Route::controller(RegionalManagerController::class)->group(function () {
        Route::get('/all/regionalmanager', 'AllRegionalManager')->name('all.regionalmanager')->middleware('permission:regionalManager.list');
        Route::get('/add/regionalmanager', 'AddRegionalManager')->name('add.regionalmanager')->middleware('permission:regionalManager.add');
        Route::post('/store/regionalmanager', 'StoreRegionalManager')->name('store.regionalmanager')->middleware('permission:regionalManager.add');
        Route::get('/edit/regionalmanager/{id}', 'EditRegionalManager')->name('edit.regionalmanager')->middleware('permission:regionalManager.edit');
        Route::post('/update/regionalmanager', 'UpdateRegionalManager')->name('update.regionalmanager')->middleware('permission:regionalManager.edit');
        Route::get('/delete/regionalmanager/{id}', 'DeleteRegionalManager')->name('delete.regionalmanager')->middleware('permission:regionalManager.delete');
        Route::get('/inactive/regionalmanager/{id}', 'InactiveRegionalManager')->name('inactive.regionalmanager')->middleware('permission:regionalManager.status');
        Route::get('/active/regionalmanager/{id}', 'ActiveRegionalManager')->name('active.regionalmanager')->middleware('permission:regionalManager.status');
    });

    //Engineer All Route (SuperAdmin + Railtel)
    Route::controller(\App\Http\Controllers\EngineerController::class)->middleware('role:SuperAdmin|Railtel')->group(function () {
        Route::get('/all/engineer', 'AllEngineer')->name('all.engineer');
        Route::get('/add/engineer', 'AddEngineer')->name('add.engineer');
        Route::post('/store/engineer', 'StoreEngineer')->name('store.engineer');
        Route::get('/edit/engineer/{id}', 'EditEngineer')->name('edit.engineer');
        Route::post('/update/engineer/{id}', 'UpdateEngineer')->name('update.engineer');
        Route::get('/delete/engineer/{id}', 'DeleteEngineer')->name('delete.engineer');
    });

    //CorporateOffice All Route
    Route::controller(CorporateOfficeController::class)->group(function () {
        Route::get('/all/corporateoffice', 'AllCorporateOffice')->name('all.corporateoffice')->middleware('permission:corporateOffice.list');
        Route::get('/add/corporateoffice', 'AddCorporateOffice')->name('add.corporateoffice')->middleware('permission:corporateOffice.add');
        Route::post('/store/corporateoffice', 'StoreCorporateOffice')->name('store.corporateoffice')->middleware('permission:corporateOffice.add');
        Route::get('/edit/corporateoffice/{id}', 'EditCorporateOffice')->name('edit.corporateoffice')->middleware('permission:corporateOffice.edit');
        Route::post('/update/corporateoffice', 'UpdateCorporateOffice')->name('update.corporateoffice')->middleware('permission:corporateOffice.edit');
        Route::get('/delete/corporateoffice/{id}', 'DeleteCorporateOffice')->name('delete.corporateoffice')->middleware('permission:corporateOffice.delete');
        Route::get('/inactive/corporateoffice/{id}', 'InactiveCorporateOffice')->name('inactive.corporateoffice')->middleware('permission:corporateOffice.status');
        Route::get('/active/corporateoffice/{id}', 'ActiveCorporateOffice')->name('active.corporateoffice')->middleware('permission:corporateOffice.status');
    });

    //New Infra Complaint All Route
    Route::controller(ComplaintController::class)->group(function () {
        Route::get('/all/new/complaint', 'AllNewComplaint')->name('all.new.complaint')->middleware('permission:complaint.list');
        Route::get('/add/complaint', 'AddComplaint')->name('add.complaint')->middleware('permission:complaint.add');
        Route::post('/store/complaint', 'StoreComplaint')->name('store.complaint')->middleware('permission:complaint.add');
        Route::get('/edit/complaint/{id}', 'EditComplaint')->name('edit.complaint')->middleware('permission:complaint.edit');
        Route::post('/update/complaint/{id}', 'UpdateComplaint')->name('update.complaint')->middleware('permission:complaint.edit');
        Route::get('/delete/complaint/{id}', 'DeleteComplaint')->name('delete.complaint')->middleware('permission:complaint.delete');


        Route::get('/complaint/{id}', 'Complaint')->name('complaint')->middleware('permission:complaint.list');
        Route::get('/closed/complaint/{id}', 'ClosedComplaint')->name('closed.complaint')->middleware('permission:complaint.closed');

        Route::post('/update/complaint/status/{id}', 'UpdateComplaintStatus')->name('update.complaint.status')->middleware('permission:complaint.detail');
        Route::get('/detail/complaint/{id}', 'DetailComplaint')->name('detail.complaint')->middleware('permission:complaint.detail');


        Route::get('/piechart/complaint', 'ComplaintPiechart')->name('complaint.piechart');

        Route::post('/uptime/complaint', 'UptimeComplaint')->name('complaint.uptime');

        Route::get('/new/complaint/visit', 'NewComplaintVisit')->name('new.complaint.visit')->middleware('permission:complaint.visit');



    });

    //Alert log Route
    Route::get('/alert/log', [App\Http\Controllers\AlertController::class, 'AlertLog'])->name('alert.log')->middleware('permission:alertLog.list');
    Route::get('/read/alert/log/{id}', [App\Http\Controllers\AlertController::class, 'ReadLog'])->name('read.alert.log');
    Route::get('/unread/alert/log/{id}', [App\Http\Controllers\AlertController::class, 'UnReadLog'])->name('unread.alert.log');



    //Old Infra Complaint All Route
    Route::controller(OldComplaintController::class)->group(function () {
        Route::get('/all/old/complaint', 'AllOldComplaint')->name('all.old.complaint')->middleware('permission:oldcomplaint.list');
        Route::post('/store/old/complaint', 'StoreOldComplaint')->name('store.old.complaint')->middleware('permission:oldcomplaint.add');

        Route::get('/detail/old/complaint/{id}', 'DetailOldComplaint')->name('detail.old.complaint')->middleware('permission:oldcomplaint.detail');

        Route::get('/edit/old/complaint/{id}', 'EditOldComplaint')->name('edit.old.complaint')->middleware('permission:oldcomplaint.edit');
        Route::post('/update/old/complaint/{id}', 'UpdateOldComplaint')->name('update.old.complaint')->middleware('permission:oldcomplaint.edit');
        Route::get('/delete/old/complaint/{id}', 'DeleteOldComplaint')->name('delete.old.complaint')->middleware('permission:oldcomplaint.delete');
        Route::post('/update/old/complaint/status/{id}', 'UpdateOldComplaintStatus')->name('update.old.complaint.status')->middleware('permission:oldcomplaint.detail');

        Route::get('/old/complaint/visit', 'OldComplaintVisit')->name('old.complaint.visit')->middleware('permission:oldcomplaint.visit');

        Route::get('/old-complaints/{id}', [OldComplaintController::class, 'OldComplaint'])->name('old.complaint');
        //Route::get('/index/old-complaints', [OldComplaintController::class, 'oldcomplaintindex'])->name('old.complaint.index');
        Route::get('/piechart/oldcomplaint', 'OldComplaintPiechart')->name('oldcomplaint.piechart');
        Route::get('add/old/complaint',  'addOldComplaint')->name('add.old.complaint'); ///ch// Add Old Complaint

    });


    //Report All Route
    Route::controller(ReportController::class)->group(function () {
        Route::get('/report/new/complaint', 'ReportNewComplaint')->name('report.new.complaint')->middleware('permission:report.newlist');
        Route::get('/report/old/complaint', 'ReportOldComplaint')->name('report.old.complaint')->middleware('permission:report.oldlist');
        Route::get('/report/incident/complaint', 'ReportIncident')->name('report.incident.complaint')->middleware('permission:report.incidentlist');

        Route::post('/search/new/complaint', 'SearchNewComplaint')->name('search.complaint')->middleware('permission:report.newlist');
        Route::post('/search/old/complaint', 'SearchOldComplaint')->name('search.old.complaint')->middleware('permission:report.oldlist');

        Route::post('/search/incident', 'SearchIncident')->name('search.incident')->middleware('permission:report.incidentlist');
    });
    //Incidents All Route
    Route::controller(IncidentController::class)->group(function () {
        Route::get('/all/incident', 'AllIncident')->name('all.incident')->middleware('permission:incident.list');
        Route::post('/store/incident', 'StoreIncident')->name('store.incident')->middleware('permission:incident.add');
        Route::get('/detail/incident/{id}', 'DetailIncident')->name('detail.incident')->middleware('permission:incident.detail');

        Route::get('/edit/incident/{id}', 'EditIncident')->name('edit.incident')->middleware('permission:incident.edit');
        Route::post('/update/incident/{id}', 'UpdateIncident')->name('update.incident')->middleware('permission:incident.edit');
        Route::get('/delete/incident/{id}', 'DeleteIncident')->name('delete.incident')->middleware('permission:incident.delete');
        Route::post('/update/incident/status/{id}', 'UpdateIncidentStatus')->name('update.incident.status')->middleware('permission:incident.detail');

        Route::get('/incident/visit', 'IncidentVisit')->name('incident.visit')->middleware('permission:incident.visit');

        Route::get('/incident/{id}',  'Incident')->name('view.incident');
        Route::get('add/incident',  'addIncident')->name('add.incident');//ch// Add Incident


    });

    //Complaint All Route
    Route::controller(CommentController::class)->group(function () {
        Route::post('/store/comment/{id}', 'StoreComment')->name('store.comment');
        Route::post('/store/old/comment/{id}', 'StoreOldComment')->name('store.old.comment');
        Route::post('/store/incident/comment/{id}', 'StoreIncidentComment')->name('store.incident.comment');
    });
});
Route::get('/dashboard', function () {
    return view('admin.index');
})->middleware(['auth', 'prevent-sensitive-cache'])->name('dashboard');

Route::middleware(['auth', 'prevent-sensitive-cache'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/complaints/check-unresolved', [ComplaintController::class, 'checkUnresolvedComplaints'])
    ->name('complaints.check-unresolved');


// 
        Route::get('/histogram/complaint', [ComplaintController::class,'ComplaintMonthlyHistogram'])->name('complaint.histogram');
        Route::get('/histogram/old/complaint', [ComplaintController::class,'OldComplaintMonthlyHistogram'])->name('old.complaint.histogram');//
        Route::get('/regionwise/complaint', [ComplaintController::class,'ComplaintRegionwiseData'])->name('complaint.regionwise');
        Route::get('/regionwise/old/complaint',  [ComplaintController::class,'OldComplaintRegionwiseData'])->name('old.complaint.regionwise');
        Route::get('/warehousewise/complaint', [ComplaintController::class,'ComplaintWarehousewiseData'])->name('complaint.warehousewise');
        Route::get('/warehousewise/old/complaint', [ComplaintController::class,'OldComplaintWarehousewiseData'])->name('old.complaint.warehousewise');//
        



require __DIR__ . '/auth.php';




        // Route::get('/histogram/complaint', 'ComplaintMonthlyHistogram')->name('complaint.histogram');
        // Route::get('/histogram/old/complaint', 'OldComplaintMonthlyHistogram')->name('old.complaint.histogram');//
        // Route::get('/regionwise/complaint', 'ComplaintRegionwiseData')->name('complaint.regionwise');
        // Route::get('/regionwise/old/complaint',  'OldComplaintRegionwiseData')->name('old.complaint.regionwise');
        // Route::get('/warehousewise/complaint', 'ComplaintWarehousewiseData')->name('complaint.warehousewise');
        // // Route::get('/warehousewise/old/complaint', 'OldComplaintWarehousewiseData')->name('old.complaint.warehousewise');//