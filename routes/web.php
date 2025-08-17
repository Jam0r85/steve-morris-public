<?php

declare(strict_types=1);

use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'))->name('home');

Route::get('/about', fn() => view('about'))->name('about');

Route::get('/contact', fn() => view('contact'))->name('contact');

Route::get('/jobs', fn() => view('jobs'))->name('jobs');

Route::get('/complaint-procedure', fn() => view('complaint-procedure'))->name('complaint-procedure');

Route::get('/lettings', fn() => view('welcome'))->name('lettings');

Route::get('/for-sale', [PropertyController::class, 'sales'])->name('properties.sales');
Route::get('/to-rent', [PropertyController::class, 'lettings'])->name('properties.lettings');

// Canonical detail URL: /properties/{channel}/{slug}/{slug_id}
Route::get('/properties/{channel}/{slug}/{slug_id}', [PropertyController::class, 'show'])
    ->whereIn('channel', ['sales', 'lettings'])
    ->name('properties.show');

Route::get('/valuation', fn() => redirect('https://steve-morris.co.uk/valuation?_gl=1*2rlrf2*_up*MQ..*_ga*MTE5NTE3NzA3Ni4xNzUwNDA2MzI1*_ga_58L7V7SYC7*czE3NTA0MDYzMjQkbzEkZzAkdDE3NTA0MDYzMjQkajYwJGwwJGgw'))->name('valuation');

Route::get('/cookies', fn() => redirect('https://www.iubenda.com/privacy-policy/98748033/cookie-policy'))->name('cookie-policy');

Route::get('/privacy', fn() => redirect('https://www.iubenda.com/privacy-policy/98748033'))->name('privacy-policy');

Route::get('/client-money-protection', function () {
    $url = asset('certs/ukala_client_money_protection.pdf');

    return redirect($url);
})->name('cmp');

// Social

Route::get('twitter', fn() => redirect('https://x.com/SteveMorrisProp'))->name('twitter');

Route::get('facebook', fn() => redirect('https://www.facebook.com/stevemorrisestateagents'))->name('facebook');
