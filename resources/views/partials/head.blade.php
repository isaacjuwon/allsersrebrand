<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $metaTitle ?? $title ?? config('app.name') }}</title>
<meta name="description" content="{{ $metaDescription ?? 'Connect with verified artisans and service providers. Chat directly, view their work, and hire with confidence.' }}" />
<meta name="author" content="Allsers" />

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $metaUrl ?? url()->current() }}">
<meta property="og:title" content="{{ $metaTitle ?? $title ?? config('app.name') }}">
<meta property="og:description" content="{{ $metaDescription ?? 'Connect with verified artisans and service providers.' }}">
<meta property="og:image" content="{{ $metaImage ?? asset('assets/allsers.png') }}">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $metaUrl ?? url()->current() }}">
<meta property="twitter:title" content="{{ $metaTitle ?? $title ?? config('app.name') }}">
<meta property="twitter:description" content="{{ $metaDescription ?? 'Connect with verified artisans and service providers.' }}">
<meta property="twitter:image" content="{{ $metaImage ?? asset('assets/allsers.png') }}">

<link rel="canonical" href="{{ $metaUrl ?? url()->current() }}" />

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
@snowfall