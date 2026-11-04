<?php

namespace App\Http\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CountdownRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->hasAny(['date', 'time'])) {
                return;
            }

            if ($this->targetAt()->lessThanOrEqualTo(CarbonImmutable::now('UTC'))) {
                $validator->errors()->add('date', 'Tanggal dan waktu acara harus berada di masa depan.');
            }
        });
    }

    public function countdownData(): array
    {
        return [
            'name' => $this->validated('name'),
            'description' => $this->validated('description'),
            'target_at' => $this->targetAt(),
        ];
    }

    private function targetAt(): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat(
            '!Y-m-d H:i', $this->input('date').' '.$this->input('time'), 'Asia/Jakarta',
        )->utc();
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama acara wajib diisi.',
            'name.string' => 'Nama acara harus berupa teks.',
            'name.max' => 'Nama acara maksimal 100 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'date.required' => 'Tanggal acara wajib diisi.',
            'date.date_format' => 'Tanggal acara tidak valid.',
            'time.required' => 'Waktu acara wajib diisi.',
            'time.date_format' => 'Waktu acara tidak valid.',
        ];
    }
}
