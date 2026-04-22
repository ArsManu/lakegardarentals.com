<?php

use App\Http\Controllers\Admin\AccountSettingsController;
use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\ApartmentController as AdminApartmentController;
use App\Http\Controllers\Admin\ApartmentImageController;
use App\Http\Controllers\Admin\ApartmentSeasonController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TriggerTranslationController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

$registerPublicSite = function (string $namePrefix, bool $localeInPath = false): void {
    Route::get('/', [PageController::class, 'home'])->name($namePrefix.'home');
    Route::get('/lake-garda', [PageController::class, 'lakeGarda'])->name($namePrefix.'lake-garda');
    Route::get('/contact', [PageController::class, 'contact'])->name($namePrefix.'contact');

    Route::get('/apartments', [ApartmentController::class, 'index'])->name($namePrefix.'apartments.index');
    Route::get(
        '/apartments/{apartment:slug}',
        [ApartmentController::class, $localeInPath ? 'showInLocale' : 'show']
    )->name($namePrefix.'apartments.show');

    Route::post('/inquiry', [InquiryController::class, 'storeBooking'])
        ->middleware('throttle:10,1')
        ->name($namePrefix.'inquiry.store');

    Route::post('/contact', [InquiryController::class, 'storeContact'])
        ->middleware('throttle:10,1')
        ->name($namePrefix.'contact.store');

    Route::get('/thank-you', [InquiryController::class, 'thankYou'])
        ->middleware('noindex')
        ->name($namePrefix.'thank-you');
};

$registerPublicSite('');

Route::prefix('{locale}')
    ->where(['locale' => 'de|it'])
    ->middleware('locale')
    ->group(function () use ($registerPublicSite): void {
        $registerPublicSite('locale.', true);
    });

Route::middleware(['auth', 'noindex'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user instanceof User && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'admin', 'noindex'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', [AccountSettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [AccountSettingsController::class, 'update'])->name('settings.update');

    Route::resource('apartments', AdminApartmentController::class)->except(['show']);
    // Avoid 405/blank page if someone opens the POST URL in a browser after a failed upload.
    Route::get('apartments/{apartment}/images', function (Apartment $apartment) {
        return redirect()->route('admin.apartments.edit', $apartment);
    })->name('apartments.images.index-redirect');

    Route::post('apartments/{apartment}/images', [ApartmentImageController::class, 'store'])->name('apartments.images.store');
    Route::patch('apartments/{apartment}/images/order', [ApartmentImageController::class, 'updateOrder'])->name('apartments.images.order');
    Route::patch('apartments/{apartment}/images/{image}', [ApartmentImageController::class, 'update'])->name('apartments.images.update');
    Route::post('apartments/{apartment}/images/{image}/move-up', [ApartmentImageController::class, 'moveUp'])->name('apartments.images.move-up');
    Route::post('apartments/{apartment}/images/{image}/move-down', [ApartmentImageController::class, 'moveDown'])->name('apartments.images.move-down');
    Route::delete('apartments/{apartment}/images/{image}', [ApartmentImageController::class, 'destroy'])->name('apartments.images.destroy');

    Route::post('apartments/{apartment}/seasons', [ApartmentSeasonController::class, 'store'])->name('apartments.seasons.store');
    Route::put('apartments/{apartment}/seasons/{season}', [ApartmentSeasonController::class, 'update'])->name('apartments.seasons.update');
    Route::delete('apartments/{apartment}/seasons/{season}', [ApartmentSeasonController::class, 'destroy'])->name('apartments.seasons.destroy');

    Route::resource('amenities', AmenityController::class)->except(['show']);
    Route::resource('inquiries', AdminInquiryController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::get('pages/{page:slug}/hero-slideshow/edit', [AdminPageController::class, 'editHeroSlideshow'])->name('pages.hero-slideshow.edit');
    Route::put('pages/{page:slug}/hero-slideshow', [AdminPageController::class, 'updateHeroSlideshow'])->name('pages.hero-slideshow.update');
    Route::get('pages/{page:slug}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{page:slug}', [AdminPageController::class, 'update'])->name('pages.update');
    Route::post('translate', TriggerTranslationController::class)->name('translate');
    Route::resource('faqs', FaqController::class)->except(['show']);
    Route::resource('testimonials', TestimonialController::class)->except(['show']);
});
