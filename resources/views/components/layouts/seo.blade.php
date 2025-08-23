@props([
    'title' => config('app.name'),
    'description' => '',
    'image' => asset('images/default-og.jpg'),
    'url' => url()->current(),
    'siteName' => config('app.name'),
])

@php
    $title = $title ?? null ? $title . ' | ' . config('app.name') : config('app.name');
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}" />
<meta name="author" content="{{ config('app.name') }}" />

<meta name="robots" content="index, follow" />
<meta name="theme-color" content="#ffffff" />
<link rel="canonical" href="{{ url()->current() }}" />

<link rel="icon" href="/favicon.ico" sizes="any" />
<link rel="apple-touch-icon" href="/apple-touch-icon.png" />

{{-- Open Graph --}}
<meta property="og:type" content="website" />
<meta property="og:title" content="{{ $title }}" />
<meta property="og:description" content="{{ $description }}" />
<meta property="og:url" content="{{ $url }}" />
<meta property="og:image" content="{{ $image }}" />
<meta property="og:site_name" content="{{ $siteName }}" />

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $title }}" />
<meta name="twitter:description" content="{{ $description }}" />
<meta name="twitter:image" content="{{ $image }}" />
