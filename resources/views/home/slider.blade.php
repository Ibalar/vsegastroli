<div class="row position-relative z-2">
    <div class="col-md-4 d-flex flex-column">
        <div class="text-center text-md-start pt-3 pt-md-4 pt-lg-5 mt-xl-5 mb-4">
            <h1 class="display-5 mt-xxl-3 mb-xxl-4">Билеты на мероприятия</h1>
            <p class="mb-0">События, которые могут быть Вам интересны</p>
        </div>
        <div class="d-flex justify-content-center justify-content-md-start gap-3 mt-auto pb-lg-2 mb-4 mb-md-0 mb-lg-4">
            <button type="button" class="btn btn-icon btn-outline-secondary animate-slide-start bg-body rounded-circle" id="hero-prev" aria-label="Prev">
                <i class="fi-chevron-left fs-lg animate-target"></i>
            </button>
            <button type="button" class="btn btn-icon btn-outline-secondary animate-slide-end bg-body rounded-circle" id="hero-next" aria-label="Next">
                <i class="fi-chevron-right fs-lg animate-target"></i>
            </button>
        </div>
    </div>
    <div class="col-md-8">
        <div class="swiper" data-swiper='{
                "effect": "creative",
                "loop": true,
                "speed": 450,
                "autoplay": {
                  "delay": 7000,
                  "disableOnInteraction": false
                },
                "creativeEffect": {
                  "prev": {
                    "translate": [0, 0, -800],
                    "rotate": [180, 0, 0]
                  },
                  "next": {
                    "translate": [0, 0, -800],
                    "rotate": [-180, 0, 0]
                  }
                },
                "navigation": {
                  "prevEl": "#hero-prev",
                  "nextEl": "#hero-next"
                }
              }'>


            <!-- Event -->
            @if($slides->count())
                <div class="swiper-wrapper">
                    @foreach($slides as $slide)
                        <div class="swiper-slide">
                            <a class="ratio d-block bg-body-secondary rounded-4 overflow-hidden"
                               href="{{ $slide->event ? route('event.show', ['city' => $slide->event->city->slug, 'slug' => $slide->event->slug]) : '#' }}"
                               style="--fn-aspect-ratio: calc(429 / 1024 * 100%)">
                                <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}">
                            </a>
                            <div class="position-absolute shadow"
                                 style="bottom: 9%; right: 7%; width: 160px; height: 86px"
                                 data-bs-theme="light">
                                <div class="position-absolute vstack text-white z-2" style="top: 19px; left: 60px">
                                    @if($slide->price)
                                        <div style="font-size: 15px; line-height: 10px">от</div>
                                        <div class="fs-5 fw-semibold">{{ number_format($slide->price, 0, '.', ' ') }} ₽</div>
                                    @endif
                                </div>
                                <svg class="position-relative z-1" style="margin: 4px 0 0 8px" xmlns="http://www.w3.org/2000/svg" width="142" height="69">
                                    <path class="text-primary" d="M8 0h31.189c.666 2.588 3.015 4.5 5.811 4.5s5.145-1.912 5.811-4.5H134a8 8 0 0 1 8 8v53a8 8 0 0 1-8 8H50.659c-.824-2.33-3.046-4-5.659-4s-4.835 1.67-5.659 4H8a8 8 0 0 1-8-8V8a8 8 0 0 1 8-8z" fill="currentColor"/>
                                    <path d="M45 65V4.5c2.796 0 5.145-1.912 5.811-4.5H134a8 8 0 0 1 8 8v53a8 8 0 0 1-8 8H50.659c-.824-2.33-3.046-4-5.659-4z" fill="url(#A)"/>
                                    <path d="M45 4v61" stroke="#1d2735" stroke-width="1.5" stroke-dasharray="4 2"/>
                                    <path d="M23.5 25l.008 8.919a1 1 0 0 0 .499.865l7.72 4.466-7.728-4.453a1 1 0 0 0-.998 0l-7.728 4.453 7.72-4.466a1 1 0 0 0 .499-.865L23.5 25z" stroke="currentColor" stroke-width="2" style="color: var(--fn-primary-text-emphasis)"/>
                                    <defs><linearGradient id="A" x1="46" y1="35.5" x2="66.5" y2="35.5" gradientUnits="userSpaceOnUse"><stop stop-color="rgba(0,0,0,.15)"/><stop class="text-primary" offset="1" stop-color="currentColor" stop-opacity="0"/></linearGradient></defs>
                                </svg>
                                <img src="/assets/img/hero-slider/ticket.png"
                                     class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" alt="Ticket">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
