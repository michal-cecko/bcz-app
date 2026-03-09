<?php

use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamInvitationController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'show'])
    ->defaults('slug', '/')
    ->name('home');

// Trainings (platform-wide)
Route::get('/treningy', [TrainingController::class, 'index'])->name('treningy');
Route::get('/trening/{training:slug}', [TrainingController::class, 'show'])->name('training.show');

// Competitions (platform-wide)
Route::get('/sutaze', [CompetitionController::class, 'index'])->name('sutaze');
Route::get('/sutaz/{competition:slug}', [CompetitionController::class, 'show'])->name('competition.show');

// Events (BCZ-only)
Route::get('/vystupenia', [EventController::class, 'index'])->name('vystupenia');
Route::get('/vystupenie/{event:slug}', [EventController::class, 'show'])->name('event.show');

// Team-scoped routes
Route::prefix('tim/{team:slug}')->name('team.')->group(function () {
    Route::get('/', [TeamController::class, 'show'])->name('show');
    Route::get('/treningy', [TeamController::class, 'trainings'])->name('trainings');
    Route::get('/sutaze', [TeamController::class, 'competitions'])->name('competitions');
    Route::get('/clenovia', [TeamController::class, 'members'])->name('members');
});

// Named routes for system pages (used by static templates)
Route::get('/o-nas', [PageController::class, 'show'])->defaults('slug', 'o-nas')->name('about');
Route::get('/kontakt', [PageController::class, 'show'])->defaults('slug', 'kontakt')->name('kontakt');
Route::get('/faq', [PageController::class, 'show'])->defaults('slug', 'faq')->name('faq');
Route::get('/podporte-nas', [PageController::class, 'show'])->defaults('slug', 'podporte-nas')->name('podporte-nas');
Route::get('/zakladatel-ceo-dominik-klimek', [PageController::class, 'show'])->defaults('slug', 'zakladatel-ceo-dominik-klimek')->name('dominik-klimek');
Route::get('/dva-percenta-z-dane', [PageController::class, 'show'])->defaults('slug', 'dva-percenta-z-dane')->name('dva-percenta');
Route::get('/vystupenia-workshopy', [PageController::class, 'show'])->defaults('slug', 'vystupenia-workshopy')->name('vystupenia-workshopy');
Route::get('/prednasky', [PageController::class, 'show'])->defaults('slug', 'prednasky')->name('prednasky');
Route::get('/workshopy', [PageController::class, 'show'])->defaults('slug', 'workshopy')->name('workshopy');
Route::get('/kategoria/parkour-freerunning', [PageController::class, 'show'])->defaults('slug', 'kategoria/parkour-freerunning')->name('parkour-freerunning');
Route::get('/kategoria/street-workout', [PageController::class, 'show'])->defaults('slug', 'kategoria/street-workout')->name('street-workout');

// Redirects from old static routes
Route::redirect('/archiv-treningov', '/treningy', 301);
Route::redirect('/archiv-podujati', '/vystupenia', 301)->name('archiv-podujati');
Route::redirect('/archiv-trenerov', '/treningy', 301);

Route::middleware('signed')->group(function () {
    Route::get('/team-invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])
        ->name('team-invitations.accept');
    Route::get('/team-invitations/{invitation}/register', [TeamInvitationController::class, 'showRegisterForm'])
        ->name('team-invitations.register');
    Route::post('/team-invitations/{invitation}/register', [TeamInvitationController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/stripe/connect/{team}/onboard', [StripeConnectController::class, 'onboard'])
        ->name('stripe.connect.onboard');
    Route::get('/stripe/connect/callback', [StripeConnectController::class, 'callback'])
        ->name('stripe.connect.callback');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|stripe|team-invitations).*$')
    ->name('page.show');
