@extends('layouts.app', ['shared' => true, 'pageTitle' => $countdown->name.' — Countdown'])
@section('content')
    <div class="shared-page">
        <p class="eyebrow shared-intro">Tandai waktunya. Nikmati penantiannya.</p>
        @include('components.countdown', [
            'name' => $countdown->name, 'description' => $countdown->description,
            'target' => $countdown->target_at, 'shareUrl' => route('countdowns.show', $countdown),
        ])
        <p class="shared-caption">Hal-hal baik layak dinantikan.</p>
    </div>
@endsection
