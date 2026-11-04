@extends('layouts.app')
@section('content')
    <section class="hero">
        <p class="eyebrow"><span class="accent-mark" aria-hidden="true"></span> Untuk semua yang kamu nantikan</p>
        <h1>Hitung Mundur Momen Penting.</h1>
        <div class="hero-bottom">
            <p>Buat hitung mundur dan bagikan momen yang kamu nantikan.</p>
            <span class="edition-note">Rencanakan. Nantikan. Rayakan.</span>
        </div>
    </section>
    <div class="workspace" id="buat">
        <section class="form-panel" aria-labelledby="form-title">
            <div class="section-heading"><span class="section-number">01 /</span><h2 id="form-title">Mulai dari satu momen.</h2></div>
            @include('countdowns._form')
        </section>
        <div class="preview-panel">
            @include('components.countdown', [
                'preview' => true, 'name' => 'Momen berikutnya.', 'description' => '',
                'target' => now('Asia/Jakarta')->addDays(30)->startOfDay()->setHour(9),
            ])
            <p class="preview-caption"><span aria-hidden="true">↳</span> Isi detail acaramu. Lihat hitung mundurnya di sini.</p>
        </div>
    </div>
    <section class="collection" id="koleksi" aria-labelledby="collection-title">
        <div class="collection-heading">
            <div class="section-heading"><span class="section-number">02 /</span><h2 id="collection-title">Momen yang dinantikan.</h2></div>
            <span class="eyebrow">{{ $countdowns->total() }} countdown</span>
        </div>
        @forelse($countdowns as $event)
            <article class="event-row">
                <span class="row-number">{{ str_pad((string) ($countdowns->firstItem() + $loop->index), 2, '0', STR_PAD_LEFT) }}</span>
                <div class="row-event">
                    <h3><a href="{{ route('countdowns.show', $event) }}">{{ $event->name }}</a></h3>
                    <p>{{ $event->target_at->timezone('Asia/Jakarta')->locale('id')->isoFormat('D MMMM YYYY · HH.mm') }} WIB</p>
                </div>
                <span class="row-status" data-remaining="{{ $event->target_at->getTimestampMs() }}">{{ $event->target_at->isPast() ? 'Acara telah dimulai.' : 'Menghitung mundur' }}</span>
                <div class="row-actions">
                    <button class="text-button" type="button" data-copy="{{ route('countdowns.show', $event) }}" aria-label="Salin tautan {{ $event->name }}">Salin tautan ↗</button>
                    <a class="text-button" href="{{ route('countdowns.edit', $event->manage_token) }}" aria-label="Edit {{ $event->name }}">Edit</a>
                    <form method="POST" action="{{ route('countdowns.destroy', $event->manage_token) }}" data-delete>
                        @csrf @method('DELETE')
                        <button class="text-button" type="submit" aria-label="Hapus {{ $event->name }}">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <span class="empty-symbol" aria-hidden="true">＋</span>
                <div><h3>Belum ada momen yang ditandai.</h3><p>Acara besar atau hal kecil yang berarti. Mulai dengan countdown pertamamu.</p></div>
                <a class="text-button" href="#buat">Buat yang pertama <span aria-hidden="true">↗</span></a>
            </div>
        @endforelse
        {{ $countdowns->links() }}
    </section>
@endsection
