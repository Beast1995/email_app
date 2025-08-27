<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\CampaignSendController;
use App\Http\Controllers\TestEmailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// Email Templates Routes
Route::resource('templates', EmailTemplateController::class);
Route::post('templates/{template}/preview', [EmailTemplateController::class, 'preview'])->name('templates.preview');

// Recipients & Groups
use App\Http\Controllers\RecipientController;
Route::get('recipients', [RecipientController::class, 'index'])->name('recipients.index');
Route::post('recipients', [RecipientController::class, 'store'])->name('recipients.store');
Route::post('recipients/{recipient}/toggle', [RecipientController::class, 'toggle'])->name('recipients.toggle');
Route::delete('recipients/{recipient}', [RecipientController::class, 'destroy'])->name('recipients.destroy');

// Email Campaigns Routes
Route::resource('campaigns', EmailCampaignController::class);
Route::post('campaigns/{campaign}/send', [EmailCampaignController::class, 'send'])->name('campaigns.send');
Route::post('campaigns/{campaign}/schedule', [EmailCampaignController::class, 'schedule'])->name('campaigns.schedule');
Route::post('campaigns/{campaign}/duplicate', [EmailCampaignController::class, 'duplicate'])->name('campaigns.duplicate');

// Campaign Sending Routes
Route::get('campaigns/send', function() {
    return view('campaigns.send');
})->name('campaigns.send-page');
Route::post('campaigns/{campaign}/send-campaign', [CampaignSendController::class, 'sendCampaign'])->name('campaigns.send-campaign');
Route::post('campaigns/send-all-draft', [CampaignSendController::class, 'sendAllDraftCampaigns'])->name('campaigns.send-all-draft');
Route::post('campaigns/send-scheduled', [CampaignSendController::class, 'sendScheduledCampaigns'])->name('campaigns.send-scheduled');

// Test Email Routes
Route::get('test-email', [TestEmailController::class, 'showTestForm'])->name('test-email');
Route::post('test-email/send', [TestEmailController::class, 'sendTestEmail'])->name('test-email.send');
Route::get('mail-logs', [TestEmailController::class, 'checkMailLogs'])->name('mail-logs');
Route::post('resend-failed', [TestEmailController::class, 'resendFailedEmails'])->name('resend-failed');

// Unsubscribe route
Route::get('unsubscribe/{email}', function ($email) {
    $email = base64_decode($email);
    // Here you would typically mark the email as unsubscribed in your database
    return view('unsubscribe', compact('email'));
})->name('unsubscribe');
