<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\BillingController;

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('submit-login', [AuthController::class, 'submitLogin'])->name('submitLogin');
Route::get('/billing', [BillingController::class, 'index'])->name('billing');
Route::get('/ajax-billing', [BillingController::class, 'ajaxBilling'])->name('ajaxBilling');
Route::post('submit-faktur', [BillingController::class, 'submitFaktur'])->name('submitFaktur');
Route::post('submit-bukti-potong-pph', [BillingController::class, 'submitBuktiPotongPph'])->name('submitBuktiPotongPph');
Route::get('/invoice/{filename}', function ($filename) {
    $path = public_path('invoice/' . $filename);

    if (!File::exists($path)) {
        abort(404, 'Invoice not found.');
    }

    $mimeType = File::mimeType($path); // ex: application/pdf
    return Response::file($path, [
        'Content-Type' => $mimeType
    ]);
});
Route::get('/bukti-potong-pph/{filename}', function ($filename) {
    $path = public_path('bukti_potong/' . $filename);

    if (!File::exists($path)) {
        abort(404, 'Invoice not found.');
    }

    $mimeType = File::mimeType($path); // ex: application/pdf
    return Response::file($path, [
        'Content-Type' => $mimeType
    ]);
});
Route::get('/faktur/{filename}', function ($filename) {
    $path = public_path('faktur/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $mimeType = File::mimeType($path); // ex: application/pdf
    return Response::file($path, [
        'Content-Type' => $mimeType
    ]);
});
Route::get('export-billing', [BillingController::class, 'exportBilling'])->name('exportBilling');
Route::get('/admin/dashboard/chart-data', [BillingController::class, 'getChartData'])->name('admin.dashboard.chart-data');

