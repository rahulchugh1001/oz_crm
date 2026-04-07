<?php

use App\Http\Controllers\ProfileController;
use App\Mail\UserCredentialsMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin/dashboard');
    }
    return redirect('login');
});

// Test Mail Route
Route::get('/test-mail', function () {
    // Create a dummy user object for testing
    $testUser = new User([
        'name' => 'Test User',
        'email' => 'rahulchugh1001@gmail.com',
        'role' => 'User',
    ]);
    
    $testPassword = 'TestPassword123';
    
    try {
        Mail::to('rahulchugh1001@gmail.com')
        ->queue(new UserCredentialsMail($testUser, $testPassword));
        return '<h1>✅ Email sent successfully!</h1><p>Check your mail logs at: <code>storage/logs/laravel.log</code></p>';
    } catch (\Exception $e) {
        return '<h1>❌ Email failed to send</h1><p>Error: ' . $e->getMessage() . '</p>';
    }
})->name('test.mail');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
