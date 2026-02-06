<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Venue;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends ModelResource<Venue>
 */
class VenueResource extends ModelResource
{
    protected string $model = Venue::class;

    protected string $title = 'Площадки';

    protected string $column = 'name';

    protected array $with = ['city'];

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name')->sortable(),
            BelongsTo::make('Город', 'city', resource: CityResource::class)->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                Text::make('Название площадки', 'name')->required(),
                Text::make('Адрес', 'address'),
                BelongsTo::make('Город', 'city', resource: CityResource::class)
                    ->required()
                    ->searchable(),
                Textarea::make('Описание', 'description'),
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Название', 'name'),
            Text::make('Адрес', 'address'),
            BelongsTo::make('Город', 'city', resource: CityResource::class),
            Textarea::make('Описание', 'description'),
        ];
    }

    /**
     * @param Venue $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'description' => 'nullable|string',
        ];
    }
}
