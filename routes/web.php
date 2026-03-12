<?php

use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamInvitationController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes (shared definition)
|--------------------------------------------------------------------------
*/
$frontendRoutes = function () {
    Route::get('/', [PageController::class, 'show'])
        ->defaults('slug', '/');

    Route::get('/treningy', [TrainingController::class, 'index']);
    Route::get('/trening/{training:slug}', [TrainingController::class, 'show']);

    Route::get('/sutaze', [CompetitionController::class, 'index']);
    Route::get('/sutaz/{competition:slug}', [CompetitionController::class, 'show']);

    Route::get('/vystupenia', [EventController::class, 'index']);
    Route::get('/vystupenie/{event:slug}', [EventController::class, 'show']);

    Route::prefix('tim/{team:slug}')->group(function () {
        Route::get('/', [TeamController::class, 'show']);
        Route::get('/treningy', [TeamController::class, 'trainings']);
        Route::get('/sutaze', [TeamController::class, 'competitions']);
        Route::get('/clenovia', [TeamController::class, 'members']);
    });

    Route::get('/o-nas', [PageController::class, 'show'])->defaults('slug', 'o-nas');
    Route::get('/kontakt', [PageController::class, 'show'])->defaults('slug', 'kontakt');
    Route::get('/faq', [PageController::class, 'show'])->defaults('slug', 'faq');
    Route::get('/podporte-nas', [PageController::class, 'show'])->defaults('slug', 'podporte-nas');
    Route::get('/zakladatel-ceo-dominik-klimek', [PageController::class, 'show'])->defaults('slug', 'zakladatel-ceo-dominik-klimek');
    Route::get('/dva-percenta-z-dane', [PageController::class, 'show'])->defaults('slug', 'dva-percenta-z-dane');
    Route::get('/vystupenia-workshopy', [PageController::class, 'show'])->defaults('slug', 'vystupenia-workshopy');
    Route::get('/prednasky', [PageController::class, 'show'])->defaults('slug', 'prednasky');
    Route::get('/workshopy', [PageController::class, 'show'])->defaults('slug', 'workshopy');
    Route::get('/kategoria/parkour-freerunning', [PageController::class, 'show'])->defaults('slug', 'kategoria/parkour-freerunning');
    Route::get('/kategoria/street-workout', [PageController::class, 'show'])->defaults('slug', 'kategoria/street-workout');
    Route::get('/cennik', [PricingController::class, 'index']);

    Route::redirect('/archiv-treningov', '/treningy', 301);
    Route::redirect('/archiv-podujati', '/vystupenia', 301);
    Route::redirect('/archiv-trenerov', '/treningy', 301);

    Route::get('/{slug}', [PageController::class, 'show'])
        ->where('slug', '^(?!admin|stripe|team-invitations|en|cs).*$');
};

// Localized: /en/..., /cs/...
Route::prefix('{locale}')
    ->where(['locale' => 'en|cs'])
    ->group($frontendRoutes);

// Default Slovak (no prefix) — named routes live here
Route::get('/', [PageController::class, 'show'])->defaults('slug', '/')->name('home');
Route::get('/treningy', [TrainingController::class, 'index'])->name('treningy');
Route::get('/trening/{training:slug}', [TrainingController::class, 'show'])->name('training.show');
Route::get('/sutaze', [CompetitionController::class, 'index'])->name('sutaze');
Route::get('/sutaz/{competition:slug}', [CompetitionController::class, 'show'])->name('competition.show');
Route::get('/vystupenia', [EventController::class, 'index'])->name('vystupenia');
Route::get('/vystupenie/{event:slug}', [EventController::class, 'show'])->name('event.show');
Route::prefix('tim/{team:slug}')->name('team.')->group(function () {
    Route::get('/', [TeamController::class, 'show'])->name('show');
    Route::get('/treningy', [TeamController::class, 'trainings'])->name('trainings');
    Route::get('/sutaze', [TeamController::class, 'competitions'])->name('competitions');
    Route::get('/clenovia', [TeamController::class, 'members'])->name('members');
});
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
Route::get('/cennik', [PricingController::class, 'index'])->name('cennik');
Route::redirect('/archiv-treningov', '/treningy', 301);
Route::redirect('/archiv-podujati', '/vystupenia', 301)->name('archiv-podujati');
Route::redirect('/archiv-trenerov', '/treningy', 301);
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|stripe|team-invitations|en|cs).*$')
    ->name('page.show');

/*
|--------------------------------------------------------------------------
| Non-localized Routes
|--------------------------------------------------------------------------
*/
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
