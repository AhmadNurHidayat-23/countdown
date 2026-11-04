@extends('layouts.app', ['shared' => true, 'pageTitle' => 'Ada kendala — Countdown'])
@section('content')
    <section class="error-page">
        <p class="eyebrow">Countdown / @yield('code')</p>
        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>
    </section>
@endsection
