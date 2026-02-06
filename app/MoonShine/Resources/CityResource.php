<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\City;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\SortDirection;

use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<City>
 */
class CityResource extends ModelResource
{
    protected string $model = City::class;

    protected string $title = 'Города';

    protected string $column = 'name';

    protected array $with = [];

    protected string $sortColumn = 'name';
    protected SortDirection $sortDirection = SortDirection::ASC;

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name')->sortable(),
            Switcher::make('Активное', 'is_active')
                ->sortable()
                ->updateOnPreview(),
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
                Text::make('Название', 'name')->required(),
                Switcher::make('Активное', 'is_active')->default(true),
                Slug::make('Slug', 'slug')
                    ->from('name')
                    ->unique()
                    ->readonly()
                    ->locked(),
                Text::make('Название в предложном падеже', 'name_in')
                    ->hint('Например: в Москве, в Санкт-Петербурге'),
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
            Text::make('В предложном падеже', 'name_in'),
            Text::make('Slug', 'slug'),
            Switcher::make('Активно', 'is_active'),
        ];
    }

    /**
     * @param City $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_in' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}
