<section class="countdown-display {{ $preview ?? false ? 'is-preview' : '' }}" data-countdown
    data-target="{{ $target->getTimestampMs() }}" aria-label="Hitung mundur acara">
    <div class="display-topline">
        <span class="eyebrow">{{ $preview ?? false ? 'Pratinjau langsung' : 'Menuju momen penting' }}</span>
        <span class="live-label"><span aria-hidden="true">●</span> <span data-state>{{ $preview ?? false ? 'Pratinjau' : 'Menghitung mundur' }}</span></span>
    </div>
    <div class="event-heading">
        <h2 data-event-name>{{ $name }}</h2>
        <p class="event-description" data-event-description @if(empty($description)) hidden @endif>{{ $description }}</p>
        <p class="event-date" data-event-date>{{ $target->timezone('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY · HH.mm') }} WIB</p>
    </div>
    @include('components.digits')
    <p class="completion" data-completion role="status" hidden>Acara telah dimulai.</p>
    <div class="display-bottomline">
        <span data-display-note>{{ $preview ?? false ? 'Momen besar dimulai dari satu tanggal.' : 'Satu momen. Jutaan kenangan.' }}</span>
        <div class="display-actions">
            @isset($shareUrl)
                <button type="button" class="text-button" data-copy="{{ $shareUrl }}">Salin tautan <span aria-hidden="true">↗</span></button>
            @endisset
            <button type="button" class="text-button" data-fullscreen aria-pressed="false">Layar penuh <span aria-hidden="true">⛶</span></button>
        </div>
    </div>
</section>
