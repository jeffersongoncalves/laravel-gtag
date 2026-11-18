<?php

use JeffersonGoncalves\Gtag\Settings\GtagSettings;

// Stand-in for the helper of jeffersongoncalves/laravel-cookie-consent, which this package
// calls only when it is installed.
if (! function_exists('cookie_consent_script_attributes')) {
    function cookie_consent_script_attributes(string $id): string
    {
        return $GLOBALS['cookie_consent_attributes'][$id] ?? '';
    }
}

afterEach(fn () => $GLOBALS['cookie_consent_attributes'] = []);

it('holds every script while laravel-cookie-consent blocks its category', function () {
    $GLOBALS['cookie_consent_attributes']['gtag'] = ' type="text/plain" data-category="analytics"';
    $settings = app(GtagSettings::class);
    $settings->enabled = true;
    $settings->gtag_id = 'G-ABC123';
    $settings->save();
    app()->forgetInstance(GtagSettings::class);
    $html = view('gtag::script')->render();

    preg_match_all('/<script\b[^>]*>/', $html, $tags);

    expect($tags[0])->not->toBeEmpty()
        ->each(fn ($tag) => $tag->toContain('type="text/plain" data-category="analytics"')->not->toContain('text/javascript'))
        ->and($html)->not->toContain('<noscript');
});

it('renders its scripts as before when nothing is held', function () {
    $settings = app(GtagSettings::class);
    $settings->enabled = true;
    $settings->gtag_id = 'G-ABC123';
    $settings->save();
    app()->forgetInstance(GtagSettings::class);
    $html = view('gtag::script')->render();

    expect($html)->toContain('<script')->not->toContain('text/plain');
});
