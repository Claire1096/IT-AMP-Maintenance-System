
<?php
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RepairHistoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});

Route::middleware(['auth'])->group(function () {

    // Assets
    Route::resource('assets', AssetController::class);
    Route::post('assets/{asset}/reassign', [AssetController::class, 'reassign'])->name('assets.reassign');

    // Employees
    Route::resource('employees', EmployeeController::class);
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::post('employees/quick-store', [EmployeeController::class, 'quickStore'])->name('employees.quick-store');

    // Preventive Maintenance
    Route::get('assets/{asset}/maintenance/create', [MaintenanceScheduleController::class, 'create'])->name('maintenance.create');
    Route::post('assets/{asset}/maintenance', [MaintenanceScheduleController::class, 'store'])->name('maintenance.store');
    Route::get('maintenance', [MaintenanceScheduleController::class, 'index'])->name('maintenance.index');
    Route::post('maintenance/{schedule}/complete', [MaintenanceScheduleController::class, 'complete'])->name('maintenance.complete');

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
    });
});

require __DIR__.'/auth.php';
