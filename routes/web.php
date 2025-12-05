<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ReportsController;

use Carbon\Carbon;

Auth::routes();

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->role) {
            case 'receptionist':
                return redirect()->route('dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
    return redirect()->route('login');
})->name('home');

Route::get('/check-in', [VisitorController::class, 'publicCheckIn'])->name('check-in');
Route::post('/check-in', [VisitorController::class, 'publicStore'])->name('check-in.store');

Route::middleware(['auth'])->group(function () {
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/staff/stats', [DashboardController::class, 'getStaffStats'])->name('staff.stats');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users/store', [App\Http\Controllers\AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}/edit', [App\Http\Controllers\AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}/update', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.delete');
        Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('settings');
        Route::post('/settings/update', [App\Http\Controllers\AdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('/logs', [App\Http\Controllers\AdminController::class, 'logs'])->name('logs');
        Route::get('/health', [App\Http\Controllers\AdminController::class, 'health'])->name('health');
        Route::get('/export', [App\Http\Controllers\AdminController::class, 'exportData'])->name('export');
        Route::get('/backup', [App\Http\Controllers\AdminController::class, 'backup'])->name('backup');
    });

    // Legacy admin dashboard route (redirect to new one)
    Route::get('/admin-dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin_dashboard');

    Route::get('/staff-dashboard', function () {
        $currentStatus = auth()->user()->status ?? 'free';
        return view('staff_dashboard', compact('currentStatus'));
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
    Route::get('/visitors/{id}', [VisitorController::class, 'show'])->name('visitors.show');
    Route::get('/visitors/history/{phone}', [VisitorController::class, 'history'])->name('visitors.history');
    Route::post('/visitors/{id}/notes', [VisitorController::class, 'updateNotes'])->name('visitors.updateNotes');
    Route::get('/visitors/checkout/{id}', [VisitorController::class, 'checkout'])->name('visitors.checkout');
    Route::post('/visitors/{id}/wait/start', [VisitorController::class, 'startWaitTime'])->name('visitors.startWait');
    Route::post('/visitors/{id}/wait/end', [VisitorController::class, 'endWaitTime'])->name('visitors.endWait');
    Route::get('/visitors/delete/{id}', [VisitorController::class, 'destroy'])->name('visitors.delete');
    
    // Pre-registration routes
    Route::get('/visitors/preregister', [VisitorController::class, 'preregister'])->name('visitors.preregister');
    Route::post('/visitors/preregister/store', [VisitorController::class, 'storePreregistration'])->name('visitors.preregister.store');
    Route::get('/visitors/preregistrations', [VisitorController::class, 'preregistrations'])->name('visitors.preregistrations');
    Route::post('/visitors/preregistrations/{id}/checkin', [VisitorController::class, 'checkInFromPreregistration'])->name('visitors.preregistrations.checkin');
    
    // Feedback routes
    Route::get('/visitors/{id}/feedback', [VisitorController::class, 'feedback'])->name('visitors.feedback');
    Route::post('/visitors/{id}/feedback', [VisitorController::class, 'storeFeedback'])->name('visitors.feedback.store');
    
    // Notification routes
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/check-alerts', [App\Http\Controllers\NotificationController::class, 'checkAllAlerts'])->name('notifications.check-alerts');
    
    // Visitor Management (Admin/Receptionist only)
    Route::middleware(['auth'])->group(function () {
        // Categories
        Route::get('/visitors/categories', [App\Http\Controllers\VisitorManagementController::class, 'categories'])->name('visitors.categories');
        Route::get('/visitors/categories/create', [App\Http\Controllers\VisitorManagementController::class, 'createCategory'])->name('visitors.categories.create');
        Route::post('/visitors/categories/store', [App\Http\Controllers\VisitorManagementController::class, 'storeCategory'])->name('visitors.categories.store');
        Route::get('/visitors/categories/{id}/edit', [App\Http\Controllers\VisitorManagementController::class, 'editCategory'])->name('visitors.categories.edit');
        Route::put('/visitors/categories/{id}/update', [App\Http\Controllers\VisitorManagementController::class, 'updateCategory'])->name('visitors.categories.update');
        Route::delete('/visitors/categories/{id}', [App\Http\Controllers\VisitorManagementController::class, 'deleteCategory'])->name('visitors.categories.delete');
        
        // Blacklist
        Route::get('/visitors/blacklist', [App\Http\Controllers\VisitorManagementController::class, 'blacklist'])->name('visitors.blacklist');
        Route::get('/visitors/blacklist/create', [App\Http\Controllers\VisitorManagementController::class, 'createBlacklist'])->name('visitors.blacklist.create');
        Route::post('/visitors/blacklist/store', [App\Http\Controllers\VisitorManagementController::class, 'storeBlacklist'])->name('visitors.blacklist.store');
        Route::get('/visitors/blacklist/{id}/edit', [App\Http\Controllers\VisitorManagementController::class, 'editBlacklist'])->name('visitors.blacklist.edit');
        Route::put('/visitors/blacklist/{id}/update', [App\Http\Controllers\VisitorManagementController::class, 'updateBlacklist'])->name('visitors.blacklist.update');
        Route::delete('/visitors/blacklist/{id}', [App\Http\Controllers\VisitorManagementController::class, 'deleteBlacklist'])->name('visitors.blacklist.delete');
    });

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments/store', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::get('/appointments/check-conflict', [AppointmentController::class, 'checkConflict'])->name('appointments.checkConflict');
    Route::post('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::post('/appointments/{appointment}/reminders', [AppointmentController::class, 'createReminder'])->name('appointments.reminders.create');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/appointments/unavailable-times', [AppointmentController::class, 'getUnavailableTimes'])->name('appointments.unavailable');
    
    // Recurring Appointments
    Route::get('/appointments/recurring', [AppointmentController::class, 'recurringIndex'])->name('appointments.recurring.index');
    Route::get('/appointments/recurring/create', [AppointmentController::class, 'createRecurring'])->name('appointments.recurring.create');
    Route::post('/appointments/recurring/store', [AppointmentController::class, 'storeRecurring'])->name('appointments.recurring.store');
    Route::post('/appointments/recurring/{recurring}/generate', [AppointmentController::class, 'generateRecurringAppointments'])->name('appointments.recurring.generate');

Route::get('/messages/conversation/{userId}', [App\Http\Controllers\MessageController::class, 'getConversation'])->name('messages.conversation');
Route::get('/messages/notifications', [App\Http\Controllers\MessageController::class, 'getNotifications'])->name('messages.notifications');
Route::get('/visitors/{id}/details', [App\Http\Controllers\MessageController::class, 'getVisitorDetails'])->name('visitors.details');
Route::get('/appointments/{id}/details', [App\Http\Controllers\MessageController::class, 'getAppointmentDetails'])->name('appointments.details');

Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
Route::post('/messages/{id}/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.markAsRead');
Route::get('/messages/recent', [App\Http\Controllers\MessageController::class, 'getRecentMessages'])->name('messages.recent');
Route::get('/messages/unread-count', [App\Http\Controllers\MessageController::class, 'getUnreadCount'])->name('messages.unreadCount');
Route::get('/messages/{id}/attachment', [App\Http\Controllers\MessageController::class, 'downloadAttachment'])->name('messages.attachment');
Route::get('/messages/notifications', [App\Http\Controllers\MessageController::class, 'getNotifications'])->name('messages.notifications');
Route::get('/messages/status', [App\Http\Controllers\MessageController::class, 'getStatus'])->name('messages.getStatus');
Route::post('/messages/status', [App\Http\Controllers\MessageController::class, 'updateStatus'])->name('messages.updateStatus');
Route::get('/messages/all-staff', [App\Http\Controllers\MessageController::class, 'getAllStaff'])->name('messages.allStaff');
Route::get('/messages/staff-notifications', [App\Http\Controllers\MessageController::class, 'getStaffNotifications'])->name('messages.staffNotifications');
Route::get('/api/receptionist', function() {
    $receptionist = \App\Models\User::where('role', 'receptionist')->first();
    if ($receptionist) {
        return response()->json(['id' => $receptionist->id]);
    } else {
        return response()->json(['error' => 'No receptionist found'], 404);
    }
})->middleware('auth');

Route::get('/api/staff/status-counts', function() {
    $staffInMeeting = \App\Models\User::where('role', 'staff')->where('status', 'busy')->count();
    $staffFree = \App\Models\User::where('role', 'staff')->where('status', 'free')->count();
    return response()->json(['staffInMeeting' => $staffInMeeting, 'staffFree' => $staffFree]);
})->middleware('auth');

Route::get('/api/staff/status-updates', function() {
    $staff = \App\Models\User::where('role', 'staff')
        ->select('id', 'name', 'status', 'status_updated_at')
        ->get()
        ->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'status' => $user->status ?? 'free',
            ];
        });
    return response()->json(['staff' => $staff]);
})->middleware('auth');

    // Reports Routes
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/visitors', [ReportsController::class, 'getVisitorReports'])->name('reports.visitors');
    Route::get('/reports/staff', [ReportsController::class, 'getStaffPerformance'])->name('reports.staff');
    Route::get('/reports/appointments', [ReportsController::class, 'getAppointmentStatistics'])->name('reports.appointments');
    Route::get('/reports/trends', [ReportsController::class, 'getVisitorTrends'])->name('reports.trends');
    Route::get('/reports/export', [ReportsController::class, 'exportReport'])->name('reports.export');
});

Route::get('/refresh-staff-status', function () {

    // All staff
    $allStaff = \App\Models\User::where('role', 'staff')->get();

    // Staff whose status was updated more than 30 minutes ago
    $thirtyMinutesAgo = \App\Models\User::where('role', 'staff')
                ->where('status_updated_at', '>=', Carbon::now()->subMinutes(30))
                ->get();

    return view('partials.staff-status', compact('allStaff', 'thirtyMinutesAgo'))->render();
});


