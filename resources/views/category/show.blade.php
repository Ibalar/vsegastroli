@extends('layouts.app')

@section('content')

    <x-categories-nav :currentCity="$currentCity ?? null" :currentCategory="$currentCategory ?? null" :categories="$categories"/>

    <x-breadcrumbs :items="\App\Helpers\Breadcrumbs::generate()" />

    <section class="container mb-2">
        <h1 class="mb-4">{{ $pageTitle }}</h1>
        <!-- Filters -->
        <form id="filterForm" method="GET" class="mb-4">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-2 g-md-3 g-lg-4">
                <!-- Текстовый поиск -->
                <div class="col">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Поиск по названию или площадке">
                </div>

                <!-- Дата с -->
                <div class="col">
                    <div class="position-relative">
                        <i class="fi-calendar position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" name="date_start" value="{{ request('date_start') }}" data-datepicker='{"dateFormat": "d.m.Y"}' class="form-control form-icon-start bg-transparent flatpickr-input" placeholder="С даты">
                    </div>
                </div>

                <!-- Дата по -->
                <div class="col">
                    <div class="position-relative">
                        <i class="fi-calendar position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input type="text" name="date_end" value="{{ request('date_end') }}" data-datepicker='{"dateFormat": "d.m.Y"}' class="form-control form-icon-start bg-transparent flatpickr-input" placeholder="по дату">
                    </div>
                </div>

                <div class="col d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Найти</button>
                    <a href="{{ route('category.show', [ 'city' => $currentCity->slug, 'category' => $currentCategory->slug ]) }}" class="btn btn-outline-secondary">Очистить</a>
                </div>
            </div>
        </form>

    </section>

    <!-- Event listsings grid -->
    <section class="container pb-2 pb-sm-3 pb-md-4 pb-lg-5 mb-xxl-3">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4" id="eventsContainer">

            {{-- Результаты --}}
            @forelse($events as $event)

                <!-- Event listing card -->
                <div class="col col-lg-3">
                    <article class="card h-100 hover-effect-scale hover-effect-opacity bg-body-tertiary border-0">
                        <div class="bg-body-secondary rounded overflow-hidden">
                            <div class="ratio hover-effect-target" style="--fn-aspect-ratio: calc(550 / 361 * 100%)">
                                @if($event->poster_url)
                                    <img src="{{ asset('storage/' . $event->poster_url) }}" alt="{!! $event->title !!}">
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled flex-row flex-wrap align-items-center gap-2 fs-sm mb-2">
                                <li class="d-flex align-items-center">
                                    <i class="fi-calendar me-1"></i>
                                    {{ $event->start_date ? $event->start_date->format('d.m.Y') : '' }}
                                </li>
                                @if($event->start_date)
                                    <li>{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}</li>
                                @endif
                                <li class="d-flex mx-n1">
                                    <i class="fi-bullet fs-base"></i>
                                </li>
                                @if($event->category)
                                    <li>{{ $event->category->name }}</li>
                                @endif
                            </ul>
                            <h3 class="h5 pt-1 mb-2">
                                <a class="hover-effect-underline stretched-link" href="{{ route('event.show', ['city' => $event->city->slug, 'slug' => $event->slug]) }}">{!! $event->title !!}</a>
                            </h3>
                            <div class="d-flex align-items-center fs-sm">
                                <i class="fi-map-pin me-1"></i>
                                {{ $event->city?->name }} - {{ $event->venue?->name }}
                            </div>
                        </div>
                        <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-3 bg-transparent border-0 pt-0 pb-4">
                            <div class="h5 text-info mb-0">
                                @if($event->price_min)
                                    от {{ number_format($event->price_min, 0, '.', ' ') }} ₽
                                @else
                                    Бесплатно
                                @endif
                            </div>
                            <a href="{{ route('event.show', ['city' => $event->city->slug, 'slug' => $event->slug]) }}" class="btn btn-outline-dark position-relative z-2">Купить билет</a>
                        </div>
                    </article>
                </div>



            @empty
                <p class="text-center py-5">Мероприятий не найдено</p>
            @endforelse


            {{ $events->links() }}
        </div>
    </section>
@endsection

