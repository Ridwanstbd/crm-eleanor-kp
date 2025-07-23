<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.index');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/user', [UserController::class,'index'])->name('users.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    // Customers Routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    //Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    //MessageTemplate Routes
    Route::get('/message-templates', [MessageTemplateController::class, 'index'])->name('templates.index');
    Route::get('/message-templates/create', [MessageTemplateController::class, 'create'])->name('templates.create');
    Route::post('/message-templates', [MessageTemplateController::class, 'store'])->name('templates.store');
    Route::get('/message-templates/{messageTemplate}/edit', [MessageTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/message-templates/{messageTemplate}', [MessageTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/message-templates/{messageTemplate}', [MessageTemplateController::class, 'destroy'])->name('templates.destroy');
    // Campaign Routes
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index'); // Menampilkan daftar kampanye
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create'); // Menampilkan form tambah kampanye
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store'); // Menyimpan kampanye baru
    Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit'); // Menampilkan form edit kampanye
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update'); // Memperbarui kampanye
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy'); // Menghapus kampanye




    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/admin/settings/edit',[AdminController::class,'updateSystem'])->name('system.update');
});
require __DIR__.'/auth.php';
