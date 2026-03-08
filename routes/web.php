<?php

use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TeamInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/o-nas', function () {
    return view('pages.about');
})->name('about');

Route::get('/zakladatel-ceo-dominik-klimek', function () {
    return view('pages.dominik-klimek');
})->name('dominik-klimek');

Route::get('/treningy', function () {
    return view('pages.treningy');
})->name('treningy');

Route::get('/trening/parkour-teens', function () {
    return view('pages.trening');
})->name('trening.parkour-teens');

Route::get('/trener/michal-cecko', function () {
    return view('pages.trener');
})->name('trener.michal-cecko');

Route::get('/archiv-treningov', function () {
    return view('pages.archiv-treningov');
})->name('archiv-treningov');

Route::get('/kategoria/parkour-freerunning', function () {
    return view('pages.parkour-freerunning');
})->name('kategoria.parkour-freerunning');

Route::get('/kategoria/street-workout', function () {
    return view('pages.street-workout');
})->name('kategoria.street-workout');

Route::get('/vystupenia-workshopy', function () {
    return view('pages.vystupenia-workshopy');
})->name('vystupenia-workshopy');

Route::get('/vystupenia', function () {
    return view('pages.vystupenia');
})->name('vystupenia');

Route::get('/prednasky', function () {
    return view('pages.prednasky');
})->name('prednasky');

Route::get('/workshopy', function () {
    return view('pages.workshopy');
})->name('workshopy');

Route::get('/archiv-podujati', function () {
    return view('pages.archiv-podujati');
})->name('archiv-podujati');

Route::get('/vystupenie/grape-festival-2024', function () {
    return view('pages.vystupenie-detail');
})->name('vystupenie.grape-festival-2024');

Route::get('/prednaska/sos-cadca', function () {
    return view('pages.prednaska-detail');
})->name('prednaska.sos-cadca');

Route::get('/workshop/kurz-stojky', function () {
    return view('pages.workshop-detail');
})->name('workshop.kurz-stojky');

Route::get('/kontakt', function () {
    return view('pages.kontakt');
})->name('kontakt');

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/archiv-trenerov', function () {
    return view('pages.archiv-trenerov');
})->name('archiv-trenerov');

Route::get('/podporte-nas', function () {
    return view('pages.podporte-nas');
})->name('podporte-nas');

Route::get('/dva-percenta-z-dane', function () {
    return view('pages.dva-percenta');
})->name('dva-percenta');

Route::get('/sutaze', function () {
    return view('pages.sutaze');
})->name('sutaze');

Route::get('/sutaz/world-freerunning-championship-2026/popis', function () {
    return view('pages.sutaz-popis');
})->name('sutaz.popis');

Route::get('/sutaz/world-freerunning-championship-2026/harmonogram', function () {
    return view('pages.sutaz-harmonogram');
})->name('sutaz.harmonogram');

Route::get('/sutaz/world-freerunning-championship-2026/vysledky', function () {
    return view('pages.sutaz-vysledky');
})->name('sutaz.vysledky');

Route::get('/sutaz/world-freerunning-championship-2026/registracia', function () {
    return view('pages.sutaz-registracia');
})->name('sutaz.registracia');

Route::get('/sutaz/world-freerunning-championship-2026/vysledky-ukoncena', function () {
    return view('pages.sutaz-vysledky-ukoncena');
})->name('sutaz.vysledky-ukoncena');

Route::get('/sutaz/world-freerunning-championship-2026/registracia-coskoro', function () {
    return view('pages.sutaz-registracia-coskoro');
})->name('sutaz.registracia-coskoro');

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
