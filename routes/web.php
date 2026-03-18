<?php

use App\Filament\Pages\Auth\SetPassword;
use App\Http\Controllers\Admin\EmailPreviewController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MagicLoginController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamInvitationController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes (shared definition)
|--------------------------------------------------------------------------
*/
$frontendRoutes = function () {
    Route::get('/', [PageController::class, 'show'])
        ->defaults('slug', '/');

    // CMS landing page for trainings
    Route::get('/trenuj-s-nami', [PageController::class, 'show'])->defaults('slug', 'trenuj-s-nami');

    // Archives (flat, global)
    Route::get('/treningy', [PageController::class, 'show'])->defaults('slug', 'zoznam-treningov');
    Route::get('/sutaze', [PageController::class, 'show'])->defaults('slug', 'sutaze');
    Route::get('/eventy', [PageController::class, 'show'])->defaults('slug', 'vystupenia');

    // Flat people archives (CMS pages with Mason bricks)
    Route::get('/treneri', [PageController::class, 'show'])->defaults('slug', 'treneri');
    Route::get('/treneri/{user:slug}', [CoachController::class, 'show']);
    Route::get('/atleti', [PageController::class, 'show'])->defaults('slug', 'atleti');
    Route::get('/atleti/{user:slug}', [CoachController::class, 'showAthlete']);
    Route::get('/rozhodcovia', [PageController::class, 'show'])->defaults('slug', 'rozhodcovia');
    Route::get('/rozhodcovia/{user:slug}', [CoachController::class, 'showJudge']);

    // Teams archive (CMS page with Mason brick)
    Route::get('/timy', [PageController::class, 'show'])->defaults('slug', 'timy');

    // Flat event detail
    Route::get('/eventy/{event:slug}', [EventController::class, 'show']);

    // Team-nested routes
    Route::prefix('timy/{team:slug}')->group(function () {
        Route::get('/', [TeamController::class, 'show']);
        Route::get('/treningy', [TeamController::class, 'trainings']);
        Route::get('/treningy/{training:slug}', [TrainingController::class, 'show']);
        Route::get('/sutaze', [TeamController::class, 'competitions']);
        Route::get('/sutaze/{event:slug}', [CompetitionController::class, 'show']);
        Route::get('/clenovia', [TeamController::class, 'members']);
    });

    // CMS pages
    Route::get('/o-nas', [PageController::class, 'show'])->defaults('slug', 'o-nas');
    Route::get('/kontakt', [PageController::class, 'show'])->defaults('slug', 'kontakt');
    Route::get('/faq', [PageController::class, 'show'])->defaults('slug', 'faq');
    Route::get('/podporte-nas', [PageController::class, 'show'])->defaults('slug', 'podporte-nas');
    Route::get('/zakladatel-ceo-dominik-klimek', [PageController::class, 'show'])->defaults('slug', 'zakladatel-ceo-dominik-klimek');
    Route::get('/dva-percenta-z-dane', [PageController::class, 'show'])->defaults('slug', 'dva-percenta-z-dane');
    Route::get('/vystupenia-prednasky-workshopy', [PageController::class, 'show'])->defaults('slug', 'vystupenia-prednasky-workshopy');
    Route::get('/akrobaticke-vystupenia', [PageController::class, 'show'])->defaults('slug', 'akrobaticke-vystupenia');
    Route::get('/prednasky', [PageController::class, 'show'])->defaults('slug', 'prednasky');
    Route::get('/workshopy', [PageController::class, 'show'])->defaults('slug', 'workshopy');
    Route::get('/kategoria/parkour-freerunning', [PageController::class, 'show'])->defaults('slug', 'kategoria/parkour-freerunning');
    Route::get('/kategoria/street-workout', [PageController::class, 'show'])->defaults('slug', 'kategoria/street-workout');
    Route::get('/cennik', [PricingController::class, 'index']);

    Route::get('/pridaj-sa', fn () => view('pages.join-team'));
    Route::get('/registracia', fn () => view('pages.register-team'));

    // Legacy redirects
    Route::redirect('/archiv-treningov', '/treningy', 301);
    Route::redirect('/archiv-podujati', '/eventy', 301);
    Route::redirect('/archiv-trenerov', '/treningy', 301);
    Route::redirect('/zoznam-treningov', '/treningy', 301);
    Route::redirect('/vystupenia', '/eventy', 301);
    Route::redirect('/trening/{any}', '/treningy', 301)->where('any', '.+');
    Route::redirect('/sutaz/{any}', '/sutaze', 301)->where('any', '.+');
    Route::redirect('/vystupenie/{any}', '/eventy', 301)->where('any', '.+');
    Route::redirect('/tim/{any}', '/timy', 301)->where('any', '.+');

    Route::get('/{slug}', [PageController::class, 'show'])
        ->where('slug', '^(?!admin|stripe|team-invitations|magic-login|en|cs|timy).*$');
};

// Localized: /en/..., /cs/...
Route::prefix('{locale}')
    ->where(['locale' => 'en|cs'])
    ->group($frontendRoutes);

// Temporary route for comparing static page
Route::get('/temp-dominik-klimek-static', fn () => view('pages.dominik-klimek'))->name('temp-dominik-static');
Route::get('/temp-dva-percenta-static', fn () => view('pages.dva-percenta'))->name('temp-dva-percenta-static');

// Default Slovak (no prefix) — named routes live here
Route::get('/', [PageController::class, 'show'])->defaults('slug', '/')->name('home');

// CMS landing page for trainings
Route::get('/trenuj-s-nami', [PageController::class, 'show'])->defaults('slug', 'trenuj-s-nami')->name('trenuj-s-nami');

// Global archives
Route::get('/treningy', [PageController::class, 'show'])->defaults('slug', 'zoznam-treningov')->name('treningy');
Route::get('/sutaze', [PageController::class, 'show'])->defaults('slug', 'sutaze')->name('sutaze');
Route::get('/eventy', [PageController::class, 'show'])->defaults('slug', 'vystupenia')->name('eventy');

// Flat people
Route::get('/treneri', [PageController::class, 'show'])->defaults('slug', 'treneri')->name('coaches.index');
Route::get('/treneri/{user:slug}', [CoachController::class, 'show'])->name('coach.show');
Route::get('/atleti', [PageController::class, 'show'])->defaults('slug', 'atleti')->name('athletes.index');
Route::get('/atleti/{user:slug}', [CoachController::class, 'showAthlete'])->name('athlete.show');
Route::get('/rozhodcovia', [PageController::class, 'show'])->defaults('slug', 'rozhodcovia')->name('judges.index');
Route::get('/rozhodcovia/{user:slug}', [CoachController::class, 'showJudge'])->name('judge.show');

// Teams
Route::get('/timy', [PageController::class, 'show'])->defaults('slug', 'timy')->name('teams.index');

// Flat event detail
Route::get('/eventy/{event:slug}', [EventController::class, 'show'])->name('event.show');

// Team-nested routes
Route::prefix('timy/{team:slug}')->name('team.')->group(function () {
    Route::get('/', [TeamController::class, 'show'])->name('show');
    Route::get('/treningy', [TeamController::class, 'trainings'])->name('trainings');
    Route::get('/treningy/{training:slug}', [TrainingController::class, 'show'])->name('training.show');
    Route::get('/sutaze', [TeamController::class, 'competitions'])->name('competitions');
    Route::get('/sutaze/{event:slug}', [CompetitionController::class, 'show'])->name('competition.show');
    Route::get('/clenovia', [TeamController::class, 'members'])->name('members');
});

// CMS pages
Route::get('/o-nas', [PageController::class, 'show'])->defaults('slug', 'o-nas')->name('about');
Route::get('/kontakt', [PageController::class, 'show'])->defaults('slug', 'kontakt')->name('kontakt');
Route::get('/faq', [PageController::class, 'show'])->defaults('slug', 'faq')->name('faq');
Route::get('/podporte-nas', [PageController::class, 'show'])->defaults('slug', 'podporte-nas')->name('podporte-nas');
Route::get('/zakladatel-ceo-dominik-klimek', [PageController::class, 'show'])->defaults('slug', 'zakladatel-ceo-dominik-klimek')->name('dominik-klimek');
Route::get('/dva-percenta-z-dane', [PageController::class, 'show'])->defaults('slug', 'dva-percenta-z-dane')->name('dva-percenta');
Route::get('/vystupenia-prednasky-workshopy', [PageController::class, 'show'])->defaults('slug', 'vystupenia-prednasky-workshopy')->name('vystupenia-prednasky-workshopy');
Route::get('/akrobaticke-vystupenia', [PageController::class, 'show'])->defaults('slug', 'akrobaticke-vystupenia')->name('akrobaticke-vystupenia');
Route::get('/prednasky', [PageController::class, 'show'])->defaults('slug', 'prednasky')->name('prednasky');
Route::get('/workshopy', [PageController::class, 'show'])->defaults('slug', 'workshopy')->name('workshopy');
Route::get('/kategoria/parkour-freerunning', [PageController::class, 'show'])->defaults('slug', 'kategoria/parkour-freerunning')->name('parkour-freerunning');
Route::get('/kategoria/street-workout', [PageController::class, 'show'])->defaults('slug', 'kategoria/street-workout')->name('street-workout');
Route::get('/cennik', [PricingController::class, 'index'])->name('cennik');
Route::get('/pridaj-sa', fn () => view('pages.join-team'))->name('pridaj-sa');
Route::get('/registracia', fn () => view('pages.register-team'))->name('register');

// Legacy redirects
Route::redirect('/archiv-treningov', '/treningy', 301);
Route::redirect('/archiv-podujati', '/eventy', 301)->name('archiv-podujati');
Route::redirect('/archiv-trenerov', '/treningy', 301)->name('archiv-trenerov');
Route::redirect('/zoznam-treningov', '/treningy', 301)->name('zoznam-treningov');
Route::redirect('/vystupenia', '/eventy', 301)->name('vystupenia');
Route::redirect('/vystupenia-workshopy', '/vystupenia-prednasky-workshopy', 301);
Route::redirect('/trening/{any}', '/treningy', 301)->where('any', '.+');
Route::redirect('/sutaz/{any}', '/sutaze', 301)->where('any', '.+');
Route::redirect('/vystupenie/{any}', '/eventy', 301)->where('any', '.+');

// Catch-all CMS page
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|stripe|team-invitations|magic-login|en|cs|timy).*$')
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
    Route::get('/magic-login/{user}', MagicLoginController::class)
        ->name('magic-login');
});

Route::get('/login', fn () => redirect('/admin/login'))->name('login');
Route::get('/admin/set-password', SetPassword::class)
    ->middleware(['web', 'auth'])
    ->name('filament.admin.auth.set-password');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->to(request()->input('redirect', '/'));
})->name('logout');

Route::get('/admin/email-preview/{key}', [EmailPreviewController::class, 'show'])
    ->name('admin.email-preview');

Route::middleware('auth')->group(function () {
    Route::post('/admin/email-preview', [EmailPreviewController::class, 'store'])
        ->name('admin.email-preview.store');

    Route::get('/stripe/connect/{team}/onboard', [StripeConnectController::class, 'onboard'])
        ->name('stripe.connect.onboard');
    Route::get('/stripe/connect/callback', [StripeConnectController::class, 'callback'])
        ->name('stripe.connect.callback');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');
