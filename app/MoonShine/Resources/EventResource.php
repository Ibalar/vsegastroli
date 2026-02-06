<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\Venue;
use App\Models\City;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\ListOf;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends ModelResource<Event>
 */
class EventResource extends ModelResource
{
    protected string $model = Event::class;

    protected string $title = 'Мероприятия';

    protected string $column = 'title';

    protected array $with = ['category', 'city', 'venue'];

    protected bool $saveQueryState = true;

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Image::make('Превью', 'poster_url'),
            Text::make('Название', 'title')
                ->sortable()
                ->unescape(),
            BelongsTo::make('Категория', 'category', formatted: 'name'),
            BelongsTo::make('Город', 'city', formatted: 'name'),
            Switcher::make('Новинка', 'is_new')
                ->sortable()
                ->updateOnPreview(),
            Switcher::make('Популярное', 'is_popular')
                ->sortable()
                ->updateOnPreview(),
            Text::make('Статус', 'status_name')
                ->badge(fn($value, $record) => match($value) {
                    'Черновик' => 'gray',
                    'Опубликовано' => 'green',
                    'Отменено' => 'red',
                    default => 'gray'
                }),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Tabs::make([
                Tab::make('Основная информация', [
                    ID::make()->sortable(),
                    Flex::make([
                        Text::make('Название мероприятия', 'title')
                            ->required()
                            ->unescape(),
                        Slug::make('Slug', 'slug')
                            ->unique()
                            ->from('title')
                            ->locked(),
                    ])
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Flex::make([
                        Grid::make([
                            Column::make([
                                Switcher::make('Новинка', 'is_new')->default(true),
                            ])->columnSpan(2),
                            Column::make([
                                Switcher::make('Популярное', 'is_popular')->default(false),
                            ])->columnSpan(2),
                        ])
                    ])
                        ->justifyAlign('left')
                        ->itemsAlign('start'),
                    Flex::make([
                        BelongsTo::make('Категория', 'category', resource: CategoryResource::class)->nullable(),
                        BelongsTo::make('Город', 'city', resource: CityResource::class)
                            ->nullable()
                            ->searchable()
                            ->reactive(),
                        BelongsTo::make('Место проведения', 'venue', resource: VenueResource::class)
                            ->nullable()
                            ->searchable()
                            ->reactive()
                            ->associatedWith('city_id')
                            ->placeholder('Начните ввод...'),
                        Date::make('Дата и время начала', 'start_date')
                            ->withTime()
                            ->required(),
                    ])
                        ->justifyAlign('between')
                        ->itemsAlign('start'),




                    Select::make('Статус', 'status')->options([
                        'draft' => 'Черновик',
                        'published' => 'Опубликовано',
                        'cancelled' => 'Отменено'
                    ])->default('draft'),
                ]),

                Tab::make('Описание', [
                    TinyMce::make('Описание', 'description')->nullable(),
                ]),

                Tab::make('Изображения и медиа', [
                    Image::make('Постер', 'poster_url')
                        ->disk('public')
                        ->dir('events')
                        ->removable()
                        ->nullable(),
                    Json::make('Галерея изображений', 'images')
                        ->fields([
                            Image::make('Фото')
                                ->disk('public')
                                ->dir('events/gallery')
                                ->removable()
                        ])
                        ->nullable(),
                ]),

                Tab::make('Стоимость и бронирование билетов', [
                    Flex::make([
                        Select::make('Организатор', 'organizer_code')
                            ->options([
                                'pre5420' => 'Медведева',
                                'pre10068' => 'Лагутеев',
                            ])
                            ->nullable()
                            ->searchable()
                            ->hint('Выберите организатора для формирования ссылки на Inticket'),
                        Text::make('Код бронирования', 'booking_code')
                            ->nullable()
                            ->hint('Введите номер кода XXXXXXXX из ссылки Inticket iframeab-pre1111.intickets.ru/seance/XXXXXXXX'),
                    ])
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                    Flex::make([
                        Number::make('Минимальная стоимость билетов', 'price_min')
                            ->step(0.01)
                            ->suffix('₽')
                            ->nullable(),
                        Number::make('Максимальная стоимость билетов', 'price_max')
                            ->step(0.01)
                            ->suffix('₽')
                            ->nullable(),
                    ])
                        ->justifyAlign('between')
                        ->itemsAlign('start'),
                ]),

                Tab::make('SEO', [
                    Text::make('SEO заголовок', 'meta_title')->hint('Максимум 255 символов'),
                    Textarea::make('SEO описание', 'meta_description')->hint('Максимум 500 символов'),
                ])
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
     * @param Event $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'venue_id' => 'required|exists:venues,id',
            'start_date' => 'required|date',
            'images' => 'nullable|array',
            'organizer_code' => 'nullable|string|max:255',
            'booking_code' => 'nullable|string|max:255',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'is_popular' => 'boolean',
            'is_new' => 'boolean',
            'status' => 'required|in:draft,published,cancelled',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    protected function indexButtons(): ListOf
    {
        return parent::indexButtons()
            ->except(fn(ActionButton $btn) => $btn->getName() === 'resource-detail-button');

    }

    protected function formButtons(): ListOf
    {
        return parent::formButtons()
            ->except(fn(ActionButton $btn) => $btn->getName() === 'resource-detail-button')
            ->except(fn(ActionButton $btn) => $btn->getName() === 'resource-delete-button')
            ->prepend(ActionButton::make('Назад', fn() => $this->getIndexPageUrl())
                ->icon('arrow-left'));
    }
}
