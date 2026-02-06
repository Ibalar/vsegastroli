<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Slide;
use App\Models\City;
use App\Models\Event;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Slide>
 */
class SlideResource extends ModelResource
{
    protected string $model = Slide::class;

    protected string $title = 'Слайдер на главной';
    protected string $column = 'name';

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Image::make('Изображение', 'image'),
            Text::make('Заголовок', 'title')->sortable(),
            BelongsTo::make('Город', 'city', formatted: 'name'),
            Switcher::make('Активен', 'is_active'),
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
                Text::make('Заголовок', 'title')->required(),

                Text::make('Стоимость', 'price')
                    ->suffix('₽'),

                Image::make('Изображение', 'image')
                    ->dir('slides')
                    ->removable()
                    ->required(),

                BelongsTo::make('Город', 'city', resource: CityResource::class)
                    ->nullable()
                    ->searchable()
                    ->reactive(),

                Select::make('Мероприятие', 'event_slug')
                    ->options(fn() => Event::pluck('title', 'slug')->toArray())
                    ->nullable()
                    ->searchable()
                    ->reactive(),

                Number::make('Порядок сортировки', 'sort_order')->default(0),

                Switcher::make('Активен', 'is_active')->default(true),
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
        ];
    }

    /**
     * @param Slide $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [];
    }
}
