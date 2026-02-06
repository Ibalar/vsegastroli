@extends('layouts.app')

@section('content')

    <x-categories-nav :currentCity="$currentCity ?? null" :currentCategory="$currentCategory ?? null" :categories="$categories"/>


    <x-breadcrumbs :items="\App\Helpers\Breadcrumbs::generate()" />


    <!-- Event details -->
    <section class="container pb-3 pb-md-4 pb-lg-5 mb-xxl-3">
        <div class="row pb-5">

            <!-- Poster + Price + Action buttons -->
            <div class="col-sm-9 col-md-5 col-lg-4 pb-3 pb-sm-0 mb-4 mb-sm-5 mb-md-0">
                <div class="ratio bg-body-tertiary rounded overflow-hidden" style="--fn-aspect-ratio: calc(500 / 359 * 100%)">
                    @if($event->poster_url)
                        <img src="{{ asset('storage/' . $event->poster_url) }}"
                             alt="{{ $event->title }}">
                    @endif
                </div>

            </div>


            <!-- Event info -->
            <div class="col-md-7 offset-lg-1">
                <div class="ps-md-4 ps-lg-0">

                    <h1 class="display-6 mb-4">{!! $event->title !!}</h1>
                    <ul class="list-unstyled gap-3 fs-sm pb-1 pb-sm-0 mb-2 mb-sm-3">
                        <li class="d-flex">
                            <i class="fi-calendar fs-base me-2" style="margin-top: 3px"></i>
                            {{ $event->start_date ? $event->start_date->format('d.m.Y') : '' }}
                            @if($event->start_date)
                            {{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}
                            @endif
                        </li>
                        <li class="d-flex flex-wrap gap-2">
                            <div class="d-flex me-2">
                                <i class="fi-map-pin fs-base me-2" style="margin-top: 3px"></i>
                                {{ $event->city?->name }} - {{ $event->venue?->name }}
                            </div>
                        </li>
                    </ul>

                    <div class="vstack gap-1 pt-3 mt-2 mt-sm-3">
                        <div class="h4 text-info mb-0">
                            @if($event->price_min && $event->price_max)
                                от {{ number_format($event->price_min, 0, '.', ' ') }} ₽
                                до {{ number_format($event->price_max, 0, '.', ' ') }} ₽
                            @elseif($event->price_min)
                                от {{ number_format($event->price_min, 0, '.', ' ') }} ₽
                            @else
                                Бесплатно
                            @endif
                        </div>
                        <div class="d-flex gap-3 pt-2 mt-1 col-3">
                            <a href="#byu-intickets" class="btn btn-lg btn-primary">Купить билет</a>
                        </div>
                    </div>


                    @if($event->description)
                        <h2 class="h5 pt-3 pt-sm-0 mt-4 mt-sm-5">Описание</h2>
                        {!! $event->description !!}
                    @endif

                </div>
            </div>
        </div>
    </section>

    <section class=" bg-body-tertiary pb-3 pb-md-4 pb-lg-5 mb-xxl-3 mt-5" id="byu-intickets">
        <div class="container">
            <h2 class="pt-3 pt-lg-4">Покупка билетов онлайн</h2>

            <link rel="stylesheet" href="//s3.intickets.ru/interposed-frame.min.css">
            <script src="//s3.intickets.ru/interposed-frame.min.js"></script>
            <div class="intickets-frame-container pt-5" data-url="https://iframeab-{{ $event->organizer_code }}.intickets.ru/seance/{{ $event->booking_code }}"></div>
        </div>
    </section>

@endsection

