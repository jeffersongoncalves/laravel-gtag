@php
    $settings = app(\JeffersonGoncalves\Gtag\Settings\GtagSettings::class);
@endphp

@if($settings->enabled && !empty($settings->gtag_id))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings->gtag_id }}"></script>
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
            gtag('config', '{{ $settings->gtag_id }}', {!! json_encode($configParams, JSON_UNESCAPED_SLASHES) !!});
        @else
            gtag('config', '{{ $settings->gtag_id }}');
        @endif
    </script>
@endif
