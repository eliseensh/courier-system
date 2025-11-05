<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\OutgoingLetterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LetterAnnexController;
use App\Http\Controllers\NotificationController;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🏠 Home Page
Route::get('/', function () {
    $incomingLetters = IncomingLetter::all();
    $totalLetters = $incomingLetters->count();
    $pending = $incomingLetters->where('status', 'pending')->count();
    $responded = $incomingLetters->where('status', 'responded')->count();
    $done = $incomingLetters->where('status', 'done')->count();

    $outgoingLetters = OutgoingLetter::all();
    $totalOutgoing = $outgoingLetters->count();
    $draftOutgoing = $outgoingLetters->where('status', 'draft')->count();
    $sentOutgoing = $outgoingLetters->where('status', 'sent')->count();
    $archivedOutgoing = $outgoingLetters->where('status', 'archived')->count();

    return view('welcome', compact(
        'incomingLetters',
        'outgoingLetters',
        'totalLetters',
        'pending',
        'responded',
        'done',
        'totalOutgoing',
        'draftOutgoing',
        'sentOutgoing',
        'archivedOutgoing'
    ));
})->name('welcome');

// 📥 Incoming Letters
Route::prefix('incoming-letters')->name('incoming-letters.')->group(function () {
    Route::get('/', [IncomingLetterController::class, 'index'])->name('index');
    Route::get('/create', [IncomingLetterController::class, 'create'])->name('create');
    Route::post('/', [IncomingLetterController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [IncomingLetterController::class, 'edit'])->name('edit');
    Route::put('/{id}', [IncomingLetterController::class, 'update'])->name('update');
    Route::delete('/{id}', [IncomingLetterController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/print', [IncomingLetterController::class, 'print'])->name('print');
    Route::get('/{id}/email', [IncomingLetterController::class, 'emailForm'])->name('email.form');
    Route::post('/{id}/email', [IncomingLetterController::class, 'sendEmail'])->name('email.send');
    Route::get('/history/{year?}/{month?}/{day?}', [IncomingLetterController::class, 'history'])->name('history');
    Route::get('/{id}', [IncomingLetterController::class, 'show'])->name('show');

    // Delete a single annex
    Route::delete('/annex/{id}', [LetterAnnexController::class, 'destroy'])->name('annexes.destroy');
});

// 📤 Outgoing Letters
Route::prefix('outgoing-letters')->name('outgoing-letters.')->group(function () {
    Route::get('/', [OutgoingLetterController::class, 'index'])->name('index');
    Route::get('/create', [OutgoingLetterController::class, 'create'])->name('create');
    Route::post('/', [OutgoingLetterController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [OutgoingLetterController::class, 'edit'])->name('edit');
    Route::put('/{id}', [OutgoingLetterController::class, 'update'])->name('update');
    Route::delete('/{id}', [OutgoingLetterController::class, 'destroy'])->name('destroy');
    Route::get('/history/{year?}/{month?}/{day?}', [OutgoingLetterController::class, 'history'])->name('history');
});

// Auth & Dashboard (Breeze)
require __DIR__.'/auth.php';

// 📊 Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// 👨‍💼 Admin Section (protected)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// 🔔 Notifications (API for private channels)
Route::prefix('notifications')->middleware(['auth'])->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/read/{id}', [NotificationController::class, 'markOneRead'])->name('notifications.markOneRead');
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.getUnreadCount');
});

// 🌐 Language Switcher
// Updated route to match /language/{lang}
Route::get('/language/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'fr'])) {
        session(['locale' => $lang]);
    }
    return redirect()->back();
})->name('lang.switch');
