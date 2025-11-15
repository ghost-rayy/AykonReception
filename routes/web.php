<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AppointmentController;

Auth::routes();

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->role) {
            case 'receptionist':
                return redirect()->route('dashboard');
            case 'admin':
                return redirect()->route('admin_dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/admin-dashboard', function () {
        return view('admin_dashboard');
    })->name('admin_dashboard');

    Route::get('/staff-dashboard', function () {
        return view('staff_dashboard');
    })->name('staff_dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff/store', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/edit/{id}', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/update/{id}', [StaffController::class, 'update'])->name('staff.update');
    Route::get('/staff/delete/{id}', [StaffController::class, 'destroy'])->name('staff.delete');

    Route::get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');
    Route::get('/visitors/create', [VisitorController::class, 'create'])->name('visitors.create');
    Route::post('/visitors/store', [VisitorController::class, 'store'])->name('visitors.store');
    Route::get('/visitors/checkout/{id}', [VisitorController::class, 'checkout'])->name('visitors.checkout');
    Route::get('/visitors/delete/{id}', [VisitorController::class, 'destroy'])->name('visitors.delete');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments/store', [AppointmentController::class, 'store'])->name('appointments.store');

Route::post('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
Route::get('/appointments/unavailable-times', [AppointmentController::class, 'getUnavailableTimes'])->name('appointments.unavailable');

Route::get('/messages/conversation/{userId}', [App\Http\Controllers\MessageController::class, 'getConversation'])->name('messages.conversation');
Route::get('/visitors/{id}/details', [App\Http\Controllers\MessageController::class, 'getVisitorDetails'])->name('visitors.details');
Route::get('/appointments/{id}/details', [App\Http\Controllers\MessageController::class, 'getAppointmentDetails'])->name('appointments.details');

Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
Route::post('/messages/{id}/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.markAsRead');
});
