<?php

use JeffersonGoncalves\Gtag\Facades\Gtag;
use JeffersonGoncalves\Gtag\Settings\GtagSettings;

it('resolves GtagSettings from the container', function () {
    $settings = app(GtagSettings::class);

    expect($settings)->toBeInstanceOf(GtagSettings::class);
});

it('has correct default values', function () {
    $settings = app(GtagSettings::class);

    expect($settings->gtag_id)->toBeNull()
        ->and($settings->enabled)->toBeTrue()
        ->and($settings->anonymize_ip)->toBeFalse()
        ->and($settings->additional_config)->toBe([]);
});

it('persists updated settings', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->save();

    $fresh = app(GtagSettings::class);

    expect($fresh->gtag_id)->toBe('G-TESTID123');
});

it('is accessible via helper function', function () {
    $settings = gtag_settings();

    expect($settings)->toBeInstanceOf(GtagSettings::class);
});

it('is accessible via Facade', function () {
    expect(Gtag::getFacadeRoot())->toBeInstanceOf(GtagSettings::class);
});

it('does not render script when gtag_id is null', function () {
    $view = view('gtag::script')->render();

    expect($view)->not->toContain('googletagmanager.com');
});

it('does not render script when disabled', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->enabled = false;
    $settings->save();

    $view = view('gtag::script')->render();

    expect($view)->not->toContain('googletagmanager.com');
});

it('renders script when enabled with valid gtag_id', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->enabled = true;
    $settings->save();

    $view = view('gtag::script')->render();

    expect($view)
        ->toContain('googletagmanager.com/gtag/js?id=G-TESTID123')
        ->toContain("gtag('config', 'G-TESTID123')");
});

it('renders anonymize_ip when enabled', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->enabled = true;
    $settings->anonymize_ip = true;
    $settings->save();

    $view = view('gtag::script')->render();

    expect($view)
        ->toContain('googletagmanager.com/gtag/js?id=G-TESTID123')
        ->toContain('"anonymize_ip":true');
});

it('renders additional_config parameters', function () {
    $settings = app(GtagSettings::class);
    $settings->gtag_id = 'G-TESTID123';
    $settings->enabled = true;
    $settings->additional_config = ['send_page_view' => false, 'cookie_domain' => 'example.com'];
    $settings->save();

    $view = view('gtag::script')->render();

    expect($view)
        ->toContain('googletagmanager.com/gtag/js?id=G-TESTID123')
        ->toContain('"send_page_view":false')
        ->toContain('"cookie_domain":"example.com"');
});
