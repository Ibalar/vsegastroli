<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class EventFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'date_start' => ['nullable', 'date_format:d.m.Y'],
            'date_end' => ['nullable', 'date_format:d.m.Y', 'after_or_equal:date_start'],
            'city_slug' => ['nullable', 'string', 'exists:cities,slug'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'search.max' => 'Поисковый запрос не должен превышать 255 символов.',
            'q.max' => 'Поисковый запрос не должен превышать 255 символов.',
            'date_from.date_format' => 'Некорректный формат даты начала. Используйте формат ГГГГ-ММ-ДД.',
            'date_to.date_format' => 'Некорректный формат даты окончания. Используйте формат ГГГГ-ММ-ДД.',
            'date_to.after_or_equal' => 'Дата окончания должна быть позже или равна дате начала.',
            'date_start.date_format' => 'Некорректный формат даты начала. Используйте формат ДД.ММ.ГГГГ.',
            'date_end.date_format' => 'Некорректный формат даты окончания. Используйте формат ДД.ММ.ГГГГ.',
            'date_end.after_or_equal' => 'Дата окончания должна быть позже или равна дате начала.',
            'city_slug.exists' => 'Выбранный город не существует.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateDateRange($validator);
        });
    }

    /**
     * Validate that date_to is not before date_from when both are present.
     */
    private function validateDateRange(Validator $validator): void
    {
        $dateFrom = $this->input('date_from');
        $dateTo = $this->input('date_to');

        if ($dateFrom && $dateTo) {
            $from = \DateTime::createFromFormat('Y-m-d', $dateFrom);
            $to = \DateTime::createFromFormat('Y-m-d', $dateTo);

            if ($from && $to && $to < $from) {
                $validator->errors()->add('date_to', 'Дата окончания должна быть позже или равна дате начала.');
            }
        }

        $dateStart = $this->input('date_start');
        $dateEnd = $this->input('date_end');

        if ($dateStart && $dateEnd) {
            $start = \DateTime::createFromFormat('d.m.Y', $dateStart);
            $end = \DateTime::createFromFormat('d.m.Y', $dateEnd);

            if ($start && $end && $end < $start) {
                $validator->errors()->add('date_end', 'Дата окончания должна быть позже или равна дате начала.');
            }
        }
    }

    /**
     * Get the search query value (supports both 'search' and 'q' parameters).
     */
    public function getSearchQuery(): ?string
    {
        return $this->input('search') ?: $this->input('q') ?: null;
    }

    /**
     * Get the start date in Y-m-d format (supports both 'date_from' and 'date_start').
     */
    public function getStartDate(): ?string
    {
        if ($this->filled('date_from')) {
            return $this->input('date_from');
        }

        if ($this->filled('date_start')) {
            $date = \DateTime::createFromFormat('d.m.Y', $this->input('date_start'));

            return $date ? $date->format('Y-m-d') : null;
        }

        return null;
    }

    /**
     * Get the end date in Y-m-d format (supports both 'date_to' and 'date_end').
     */
    public function getEndDate(): ?string
    {
        if ($this->filled('date_to')) {
            return $this->input('date_to');
        }

        if ($this->filled('date_end')) {
            $date = \DateTime::createFromFormat('d.m.Y', $this->input('date_end'));

            return $date ? $date->format('Y-m-d') : null;
        }

        return null;
    }
}
