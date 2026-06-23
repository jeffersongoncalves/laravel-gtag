@php
    use Illuminate\Support\Js;

    $settings = app(\JeffersonGoncalves\Gtag\Settings\GtagSettings::class);

    $hasValidId = ! empty($settings->gtag_id) && preg_match('/^[A-Z]+-[A-Z0-9]+$/', $settings->gtag_id) === 1;
@endphp

@if($settings->enabled && $hasValidId)
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($settings->gtag_id) }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        @php
            $configParams = [];
            if ($settings->anonymize_ip) {
                $configParams['anonymize_ip'] = true;
            }
            if (!empty($settings->additional_config)) {
                $configParams = array_merge($configParams, $settings->additional_config);
            }
        @endphp

        @if(!empty($configParams))
            gtag('config', {{ Js::from($settings->gtag_id) }}, @json($configParams, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES));
        @else
            gtag('config', {{ Js::from($settings->gtag_id) }});
        @endif
    </script>
@endif
