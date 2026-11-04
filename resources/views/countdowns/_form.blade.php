<form class="countdown-form" method="POST" action="{{ isset($countdown) ? route('countdowns.update', $countdown->manage_token) : route('countdowns.store') }}" data-preview-form>
    @csrf
    @isset($countdown) @method('PUT') @endisset
    @if($errors->any())
        <div class="form-errors" role="alert" tabindex="-1">
            <strong>Periksa kembali data acara.</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="field">
        <label for="name">Nama acara</label>
        <input id="name" name="name" type="text" placeholder="Misalnya, Peluncuran Karya" maxlength="100" required
            value="{{ old('name', $countdown->name ?? '') }}" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name')<p class="field-error" id="name-error">{{ $message }}</p>@enderror
    </div>
    <div class="field">
        <label for="description">Deskripsi singkat <span class="optional">Opsional</span></label>
        <textarea id="description" name="description" rows="2" maxlength="500" placeholder="Apa yang membuat momen ini istimewa?" @error('description') aria-invalid="true" aria-describedby="description-error" @enderror>{{ old('description', $countdown->description ?? '') }}</textarea>
        @error('description')<p class="field-error" id="description-error">{{ $message }}</p>@enderror
    </div>
    <div class="date-time-fields">
        <div class="field">
            <label for="date">Tanggal acara</label>
            <input id="date" name="date" type="date" min="{{ now('Asia/Jakarta')->toDateString() }}" max="9999-12-31" required
                value="{{ old('date', isset($countdown) ? $countdown->target_at->timezone('Asia/Jakarta')->format('Y-m-d') : '') }}"
                @error('date') aria-invalid="true" aria-describedby="date-error" @enderror>
            @error('date')<p class="field-error" id="date-error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="time">Waktu <span class="optional">WIB</span></label>
            <input id="time" name="time" type="time" required value="{{ old('time', isset($countdown) ? $countdown->target_at->timezone('Asia/Jakarta')->format('H:i') : '09:00') }}"
                @error('time') aria-invalid="true" aria-describedby="time-error" @enderror>
            @error('time')<p class="field-error" id="time-error">{{ $message }}</p>@enderror
        </div>
    </div>
    <p class="form-hint">Semua waktu menggunakan WIB (Asia/Jakarta).</p>
    <button class="button submit-button" type="submit">{{ isset($countdown) ? 'Simpan Perubahan' : 'Buat Countdown' }} <span aria-hidden="true">↗</span></button>
    @isset($countdown)<a class="cancel-link" href="{{ route('countdowns.index') }}">Batal mengedit</a>@endisset
</form>
