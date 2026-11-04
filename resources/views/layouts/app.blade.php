<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Buat hitung mundur dan bagikan momen yang kamu nantikan.">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $pageTitle ?? 'Countdown — Menantikan momen penting' }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-server-now="{{ now()->getTimestampMs() }}" data-clock-url="{{ route('clock') }}">
    <a class="skip-link" href="#utama">Langsung ke konten</a>
    <div class="page-shell">
        <header class="site-header">
            <a class="brand" href="{{ ($shared ?? false) ? url()->current() : route('countdowns.index') }}" aria-label="Countdown, beranda">Countdown<span aria-hidden="true">®</span></a>
            @unless($shared ?? false)
                <nav aria-label="Navigasi utama">
                    <a class="nav-link" href="{{ route('countdowns.index') }}#koleksi">Countdown Saya <span aria-hidden="true">↗</span></a>
                    <a class="button" href="{{ route('countdowns.index') }}#buat">Buat Countdown <span aria-hidden="true">+</span></a>
                </nav>
            @else
                <span class="eyebrow header-note">Sebuah momen untuk dinantikan</span>
            @endunless
        </header>
        @if(session('status'))
            <p class="notice" role="status">{{ session('status') }}</p>
        @endif
        <main id="utama">@yield('content')</main>
        <footer class="site-footer">
            <span>Countdown<span aria-hidden="true"> / </span>Setiap detik berarti.</span>
            <span>Waktu Indonesia Barat · UTC+7</span>
        </footer>
    </div>
    <p id="toast" class="toast" role="status" hidden></p>
    <dialog id="delete-dialog" aria-labelledby="delete-title">
        <form method="dialog">
            <p class="eyebrow">Konfirmasi</p>
            <h2 id="delete-title">Hapus countdown?</h2>
            <p>Countdown dan tautan berbaginya akan dihapus permanen.</p>
            <div class="dialog-actions">
                <button class="button button-plain" value="cancel">Batal</button>
                <button class="button" value="delete">Ya, hapus</button>
            </div>
        </form>
    </dialog>
</body>
</html>
