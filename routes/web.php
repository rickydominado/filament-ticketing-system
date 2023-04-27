<?php

use App\Http\Livewire\Inquiry\Form;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('filament.pages.dashboard');
});

Route::prefix('inquiry')->name('inquiry.')->group(function () {
    Route::get('/form', Form::class)->name('form');

    Route::middleware(['signed'])->group(function () {
        Route::get('/{inquiry}/create-success', function (Inquiry $inquiry) {
            return view('components.pages.create-success', compact('inquiry'));
        })->name('create-success');
    });
});
