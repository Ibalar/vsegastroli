<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Components\Layout\{Locales, Notifications, Profile, Search};
use MoonShine\UI\Components\{Breadcrumbs,
    Components,
    Layout\Flash,
    Layout\Div,
    Layout\Body,
    Layout\Burger,
    Layout\Content,
    Layout\Footer,
    Layout\Head,
    Layout\Favicon,
    Layout\Assets,
    Layout\Meta,
    Layout\Header,
    Layout\Html,
    Layout\Layout,
    Layout\Logo,
    Layout\Menu,
    Layout\Sidebar,
    Layout\ThemeSwitcher,
    Layout\TopBar,
    Layout\Wrapper,
    When};
use App\MoonShine\Resources\CategoryResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\CityResource;
use App\MoonShine\Resources\VenueResource;
use App\MoonShine\Resources\EventResource;
use App\MoonShine\Resources\SlideResource;

final class MoonShineLayout extends AppLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    protected function menu(): array
    {
        return [
            MenuItem::make('Мероприятия', EventResource::class)
                ->icon('book-open'),
            MenuGroup::make('Слайдеры', [
                MenuItem::make('Слайдер Главная', SlideResource::class)
                    ->icon('square-3-stack-3d'),
            ])
                ->icon('film'),
            MenuItem::make('Категории', CategoryResource::class)
                ->icon('folder-open'),
            MenuGroup::make('Справочники', [
                MenuItem::make('Города', CityResource::class)
                    ->icon('map'),
                MenuItem::make('Места проведения мероприятий', VenueResource::class)
                    ->icon('home-modern'),
            ])
                ->icon('globe-alt'),
            MenuGroup::make('Система', [
                MenuItem::make('Администраторы', MoonShineUserResource::class),
                MenuItem::make('Роли', MoonShineUserRoleResource::class),
            ])
                ->icon('cog-6-tooth'),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);

        // $colorManager->primary('#00000');
    }

    public function build(): Layout
    {
        return parent::build();
    }
}
