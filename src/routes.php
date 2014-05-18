<?php
/**
 * Sdfcloud/Langpo routes
 */
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

Route::get('set-locale/{locale}', function($locale) {
    // set locale
    Sdfcloud\Langpo\Facades\Langpo::setLocale($locale);

    // redirect to referrer or home
    if (Request::server('HTTP_REFERER'))
        return Redirect::to($_SERVER['HTTP_REFERER']);
    else
        return Redirect::to('/');
});

Route::get('set-encoding/{encoding}', function($encoding) {
    // set encoding
    Sdfcloud\Langpo\Facades\Langpo::setEncoding($encoding);

    // redirect to referrer or home
    if (Request::server('HTTP_REFERER'))
        return Redirect::to($_SERVER['HTTP_REFERER']);
    else
        return Redirect::to('/');
});

