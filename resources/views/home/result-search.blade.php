@if(request()->filled('q') || request()->filled('city_slug') || request()->filled('date_start') || request()->filled('date_end'))
    <section class="container">
        <div class="col-lg-12">
            <h2 class="h2 pb-2 pb-lg-3">Результаты поиска</h2>

        @if($events->count())
                <div class="vstack gap-4">
                @foreach($events as $event)
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <div class="col-sm-4 position-relative {{ ($loop->iteration % 2 === 0) ? 'order-sm-2' : '' }}" style="min-height: 220px">
                                    <img src="{{ asset('storage/' . $event->poster_url) }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" alt="{!! $event->title !!}">
                                </div>
                                <div class="col-sm-8 {{ ($loop->iteration % 2 === 0) ? 'order-sm-1' : '' }}">
                                    <div class="card-body">
                                        <a class="hover-effect-underline" href="{{ route('event.show', ['city' => $event->city->slug, 'slug' => $event->slug]) }}">
                                            <h5 class="card-title">{!! $event->title !!}</h5>
                                        </a>
                                        @if($event->category)
                                            <a href="{{ route('category.show', ['city' => $event->city->slug, 'category' => $event->category->slug]) }}" >
                                                <span class="badge text-body-emphasis bg-body-secondary mb-2">{{ $event->category->name }}</span>
                                            </a>
                                        @endif
                                        @if($event->price_min)
                                            <p class="h5 mb-2">
                                            от {{ number_format($event->price_min, 0, '.', ' ') }} ₽ до {{ number_format($event->price_max, 0, '.', ' ') }} ₽</p>
                                        @else
                                            <p class="h5 mb-2">Бесплатно</p>
                                        @endif
                                        <div class="fs-xs text-body-secondary mb-3">
                                            <i class="fi-calendar me-1"></i>
                                            {{ $event->start_date ? $event->start_date->format('d.m.Y') : '' }}
                                            @if($event->start_date)
                                                <span class="ms-1">{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="fs-xs text-body-secondary mb-3">
                                            <i class="fi-map-pin me-1"></i>
                                            {{ $event->city?->name }}
                                            <span class="ms-1">{{ $event->venue?->name }}</span>
                                        </div>
                                        <a class="btn btn-primary" href="{{ route('event.show', ['city' => $event->city->slug, 'slug' => $event->slug]) }}">Подробнее</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endforeach
                </div>

            {{-- Пагинация --}}
            {{ $events->links() }}

        @else
            <div class="alert alert-warning mt-3">
                По вашему запросу мероприятий не найдено.
            </div>
        @endif
        </div>
    </section>
@endif
