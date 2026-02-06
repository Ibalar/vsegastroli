<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;


use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Decorations\Block;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Components\MoonShineComponent;

/**
 * @extends ModelResource<Category>
 */
class CategoryResource extends ModelResource
{
    protected string $model = Category::class;

    protected string $title = 'Категории';

    protected string $column = 'name';

    protected array $with = [];

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name')->sortable(),
            Text::make('Сортировка', 'sort_order')->sortable(),
            Switcher::make('Активна', 'is_active')
                ->sortable()
                ->updateOnPreview(),
            Switcher::make('Показывать на главной', 'show_on_home')
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
                ID::make()->sortable(),
                Text::make('Название', 'name')->required(),
                Slug::make('Slug', 'slug')
                    ->from('name')
                    ->unique()
                    ->readonly()
                    ->locked(),
                Textarea::make('Описание', 'description'),
                Number::make('Порядок сортировки', 'sort_order')->default(0),
                Switcher::make('Активно', 'is_active')->default(true),
                Switcher::make('Показывать на главной', 'show_on_home')->default(false),
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name'),
            Text::make('Slug', 'slug'),
            Textarea::make('Описание', 'description'),
            Number::make('Порядок сортировки', 'sort_order'),
            Switcher::make('Активно', 'is_active'),
            Switcher::make('Показывать на главной', 'show_on_home'),
        ];
    }

    /**
     * @param Category $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'show_on_home' => 'boolean',
        ];
    }

}
