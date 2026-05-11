<?php

use App\Http\Controllers\HabitController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('habits.index'));

Route::resource('habits', HabitController::class)->except(['show']);
Route::get('habits/{habit}', [HabitController::class, 'show'])->name('habits.show');
Route::post('habits/{habit}/toggle', [HabitController::class, 'toggle'])->name('habits.toggle');
