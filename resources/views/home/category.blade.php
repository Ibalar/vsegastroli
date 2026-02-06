@foreach($homeCategories as $category)
    @if($category->events->count())
        <section class="{{ $loop->iteration % 2 === 0 ? '' : 'bg-body-tertiary' }} pt-5 pb-4 pb-md-5">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="mb-0">{{ $category->name }} @if(!empty($cityIn))в {{ $cityIn ?? 'вашем городе' }}@endif</h2>
                    @if($currentCity && $category)
                    <a href="{{ route('category.show', ['city' => $currentCity->slug, 'category' => $category->slug]) }}" class="nav-link fs-base">
                        Смотреть все <i class="fi-chevron-right fs-sm ms-1"></i>
                    </a>
                    @endif
                </div>

                @if($category->events->count())
                    <div class="swiper" data-swiper='{"slidesPerView":2,"spaceBetween":24,"breakpoints":{"768":{"slidesPerView":3},"992":{"slidesPerView":4}}}'>
                        <div class="swiper-wrapper">

                            @foreach($category->events as $event)
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
                @else
                    <p class="text-muted">Нет мероприятий для отображения.</p>
                @endif
            </div>
        </section>
    @endif
@endforeach
