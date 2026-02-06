
@if($popularEvents->count())
    <section class="container pt-2 pt-sm-3 pt-md-4 pt-lg-5 my-xxl-3">
        <div class="d-flex align-items-start justify-content-between gap-4 pt-5 pb-3 mb-2 mb-sm-3">
            <h2 class="mb-0">Популярное</h2>
            @if($currentCity)
            <div class="nav">
                <a class="nav-link position-relative fs-base text-nowrap py-1 px-0" href="{{ route('city.all-events', ['city' => $currentCity->slug]) }}">
                    <span class="hover-effect-underline stretched-link me-1">Смотреть все</span>
                    <i class="fi-chevron-right fs-lg"></i>
                </a>
            </div>
            @endif
        </div>
        <div class="position-relative mx-3 mx-sm-0 mb-5">
            <div class="swiper" data-swiper='{
            "slidesPerView": 1,
            "spaceBetween": 24,
            "loop": {{ $popularEvents->count() > 1 ? "true" : "false" }},
            "autoHeight": true,
            "navigation": {
              "prevEl": "#popular-prev",
              "nextEl": "#popular-next"
            },
            "breakpoints": {
              "500": {"slidesPerView": 2},
              "800": {"slidesPerView": 3},
              "1100": {"slidesPerView": 4}
            }
        }'>
                <div class="swiper-wrapper">
                    @foreach($popularEvents as $event)
                        <div class="swiper-slide">
                            {{-- карточка события --}}
                            <article class="card hover-effect-scale hover-effect-opacity bg-transparent border-0">
                                <div class="bg-body-tertiary rounded overflow-hidden">
                                    <div class="ratio hover-effect-target" style="--fn-aspect-ratio: calc(550 / 361 * 100%)">
                                        @if($event->is_popular)
                                            <div class="d-flex flex-column gap-2 align-items-start position-absolute top-0 start-0 z-3 pt-1 pt-sm-0 ps-1 ps-sm-0 mt-2 mt-sm-3 ms-2 ms-sm-3">
                                                <span class="badge text-bg-primary d-inline-flex align-items-center">Популярно</span>
                                            </div>
                                        @endif
                                        <img src="{{ asset('storage/' . $event->poster_url) }}" alt="{!! $event->title !!}">
                                    </div>
                                </div>
                                <div class="card-body pt-3 pt-sm-4 p-0">
                                    <ul class="list-unstyled flex-row flex-wrap align-items-center gap-2 fs-sm pt-1 pt-sm-0 mb-2">
                                        <li class="d-flex align-items-center me-2">
                                            <i class="fi-calendar me-1"></i>
                                            {{ $event->start_date ? $event->start_date->format('d.m.Y') : '' }}
                                            @if($event->start_date)
                                                <span class="ms-1">{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}</span>
                                            @endif
                                        </li>
                                        <li class="d-flex align-items-center me-2">
                                            <i class="fi-map-pin me-1"></i>
                                            {{ $event->city?->name }}
                                        </li>
                                    </ul>
                                    <h3 class="h5 mb-0">
                                        <a class="hover-effect-underline stretched-link" href="{{ route('event.show', ['city' => $event->city->slug, 'slug' => $event->slug]) }}">
                                            {{ $event->title }}
                                        </a>
                                    </h3>
                                    <div class="h5 text-info mb-0">
                                        @if($event->price_min)
                                            от {{ number_format($event->price_min, 0, '.', ' ') }} ₽
                                        @else
                                            Бесплатно
                                        @endif
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($popularEvents->count() > 1)
                <button type="button" class="btn btn-icon btn-outline-secondary animate-slide-start bg-body rounded-circle position-absolute top-50 start-0 translate-middle z-1 mt-n5" id="popular-prev" aria-label="Prev">
                    <i class="fi-chevron-left fs-lg animate-target"></i>
                </button>
                <button type="button" class="btn btn-icon btn-outline-secondary animate-slide-end bg-body rounded-circle position-absolute top-50 start-100 translate-middle z-1 mt-n5" id="popular-next" aria-label="Next">
                    <i class="fi-chevron-right fs-lg animate-target"></i>
                </button>
            @endif
        </div>
    </section>
@endif
