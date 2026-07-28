
<?php
use App\Http\Controllers\SearchController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RepairHistoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacilityItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FacilityMaintenanceController;
use App\Http\Controllers\FacilityReportController;
use App\Http\Controllers\DamageReportController;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('search', [SearchController::class, 'index'])->name('search');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
});

Route::middleware(['auth'])->group(function () {

    // Assets
    Route::resource('assets', AssetController::class);
    Route::post('assets/{asset}/reassign', [AssetController::class, 'reassign'])->name('assets.reassign');
    Route::resource('facility-items', FacilityItemController::class);
    // Employees
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/assign-asset', [EmployeeController::class, 'assignAsset'])->name('employees.assign-asset');

    Route::post('employees/quick-store', [EmployeeController::class, 'quickStore'])->name('employees.quick-store');

    // Preventive Maintenance
        Route::get('facility-maintenance', [FacilityMaintenanceController::class, 'index'])->name('facility-maintenance.index');
        Route::get('facility-maintenance/create', [FacilityMaintenanceController::class, 'create'])->name('facility-maintenance.create');
        Route::post('facility-maintenance', [FacilityMaintenanceController::class, 'store'])->name('facility-maintenance.store');
        Route::get('maintenance', [MaintenanceScheduleController::class, 'index'])->name('maintenance.index');
        Route::post('maintenance/{schedule}/complete', [MaintenanceScheduleController::class, 'complete'])->name('maintenance.complete');
        Route::get('facility-maintenance/{facilityMaintenance}', [FacilityMaintenanceController::class, 'show'])->name('facility-maintenance.show');
        Route::post('facility-maintenance/{facilityMaintenance}/complete', [FacilityMaintenanceController::class, 'complete'])->name('facility-maintenance.complete');
    // Repairs

        Route::get('assets/{asset}/repairs/create', [RepairHistoryController::class, 'create'])->name('repairs.create');
        Route::post('assets/{asset}/repairs', [RepairHistoryController::class, 'store'])->name('repairs.store');
        Route::post('repairs/{repair}/complete', [RepairHistoryController::class, 'complete'])->name('repairs.complete');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('preventive-maintenance', [ReportController::class, 'preventiveMaintenance'])->name('preventive-maintenance');
        Route::get('warranty-expiration', [ReportController::class, 'warrantyExpiration'])->name('warranty-expiration');
        Route::get('repair-history', [ReportController::class, 'repairHistory'])->name('repair-history');
        Route::get('asset-assignment', [ReportController::class, 'assetAssignment'])->name('asset-assignment');
        Route::get('annual-summary', [ReportController::class, 'annualSummary'])->name('annual-summary');
        Route::get('damage-reports', [DamageReportController::class, 'index'])->name('damage.index');
        Route::get('damage-reports/create', [DamageReportController::class, 'create'])->name('damage.create');
        Route::post('damage-reports', [DamageReportController::class, 'store'])->name('damage.store');
        Route::get('damage-reports/{damageReport}', [DamageReportController::class, 'show'])->name('damage.show');
    });

        Route::prefix('facility-reports')->name('facility-reports.')->group(function () {
        Route::get('inventory', [FacilityReportController::class, 'inventory'])->name('inventory');
        Route::get('condition', [FacilityReportController::class, 'condition'])->name('condition');
        Route::get('department-distribution', [FacilityReportController::class, 'departmentDistribution'])->name('department-distribution');
        Route::get('maintenance-due', [FacilityReportController::class, 'maintenanceDue'])->name('maintenance-due');
    });
});

require __DIR__.'/auth.php';
