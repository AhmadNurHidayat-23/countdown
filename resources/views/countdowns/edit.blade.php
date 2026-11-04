@extends('layouts.app', ['pageTitle' => 'Edit Countdown — Countdown'])
@section('content')
    <section class="hero hero-compact">
        <p class="eyebrow">Atur kembali momenmu</p>
        <h1>Rencana boleh berubah.</h1>
        <p>Perbarui detail acara. Tautan berbagimu tetap sama.</p>
    </section>
    <div class="workspace" id="buat">
        <section class="form-panel" aria-labelledby="form-title">
            <div class="section-heading"><span class="section-number">01 /</span><h2 id="form-title">Edit countdown.</h2></div>
            @include('countdowns._form')
        </section>
        <div class="preview-panel">
            @include('components.countdown', [
                'preview' => true, 'name' => $countdown->name, 'description' => $countdown->description,
                'target' => $countdown->target_at,
            ])
            <p class="preview-caption"><span aria-hidden="true">↳</span> Perubahan tampil di sini sebelum kamu menyimpannya.</p>
        </div>
    </div>
@endsection
