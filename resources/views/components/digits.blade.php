<div class="digits" role="timer" aria-label="Waktu tersisa" aria-live="off">
    @foreach(['days' => 'Hari', 'hours' => 'Jam', 'minutes' => 'Menit', 'seconds' => 'Detik'] as $part => $label)
        <div class="digit-group">
            <span class="digit" data-part="{{ $part }}">00</span>
            <span class="digit-label">{{ $label }}</span>
        </div>
    @endforeach
</div>
