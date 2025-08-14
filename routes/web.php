<?php

use App\Http\Controllers\MessageLogsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.index');
});
Route::match(['get', 'post'], '/webhook/update-status', [MessageLogsController::class, 'handleUpdateStatusWebhook'])
    ->name('webhook.update-status');
    
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    
    Route::get('/message-templates', [MessageTemplateController::class, 'index'])->name('templates.index');
    Route::get('/message-templates/create', [MessageTemplateController::class, 'create'])->name('templates.create');
    Route::post('/message-templates', [MessageTemplateController::class, 'store'])->name('templates.store');
    Route::get('/message-templates/{messageTemplate}/edit', [MessageTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/message-templates/{messageTemplate}', [MessageTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/message-templates/{messageTemplate}', [MessageTemplateController::class, 'destroy'])->name('templates.destroy');
    
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index'); 
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create'); 
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'edit'])->name('campaigns.edit'); 
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
    Route::get('/download-csv-template',[CampaignController::class, 'downloadCsvTemplate'])->name('download.csv.template');
    
    Route::get('/logs',[MessageLogsController::class,'index'])->name('logs.index');
    Route::get('/logs/{id}',[MessageLogsController::class,'show'])->name('logs.show');
    
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/admin/settings/edit',[AdminController::class,'updateSystem'])->name('system.update');
});
require __DIR__.'/auth.php';
