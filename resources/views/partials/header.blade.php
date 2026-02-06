<header class="navbar navbar-expand-xl navbar-sticky sticky-top z-fixed px-0" data-sticky-element>
    <div class="container">

        <!-- Mobile offcanvas menu toggler (Hamburger) -->
        <button type="button" class="navbar-toggler me-3 me-lg-0" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar brand (Logo) -->
        <a class="navbar-brand py-1 py-md-2 py-xl-1 me-2 me-sm-n4 me-md-n5 me-lg-0" href="{{ route('main') }}">
          <span class=" d-md-flex flex-shrink-0 text-primary rtl-flip me-2">
            <!-- лого -->
            <svg width="35" height="34" viewBox="0 0 35 34" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_740_3)">
                <path d="M34.5 16.894V27.625C34.5 31.131 31.631 34 28.125 34H17.5H16.65C7.725 33.575 0.5 26.138 0.5 17C0.5 7.65 8.15 0 17.5 0C26.85 0 34.5 7.544 34.5 16.894Z" fill="#C92D2D" />
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5004 13.258C14.3994 13.258 11.8454 15.812 11.8454 18.913C11.8454 22.014 14.3994 24.568 17.5004 24.568C20.6014 24.568 23.1554 22.014 23.1554 18.913C23.1554 15.812 20.6014 13.258 17.5004 13.258ZM8.06738 18.913C8.06738 13.726 12.3134 9.48 17.5004 9.48C22.6874 9.48 26.9334 13.726 26.9334 18.913C26.9343 20.7606 26.3884 22.5671 25.3644 24.105L27.7614 26.502C28.1153 26.8564 28.314 27.3367 28.314 27.8375C28.314 28.3383 28.1153 28.8186 27.7614 29.173C27.407 29.5269 26.9267 29.7256 26.4259 29.7256C25.9251 29.7256 25.4447 29.5269 25.0904 29.173L22.6934 26.776C21.1555 27.8 19.349 28.3459 17.5014 28.345C12.3144 28.345 8.06838 24.099 8.06838 18.912L8.06738 18.913Z" fill="black" fill-opacity="0.05" />
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.3942 10.153C13.6712 10.153 10.6532 13.171 10.6532 16.894C10.6532 20.617 13.6712 23.635 17.3942 23.635C21.1172 23.635 24.1352 20.617 24.1352 16.894C24.1352 13.171 21.1172 10.153 17.3942 10.153ZM7.34717 16.894C7.34796 14.2296 8.40674 11.6746 10.2907 9.79058C12.1747 7.90657 14.7298 6.8478 17.3942 6.847C20.0584 6.84806 22.6132 7.90695 24.4969 9.79093C26.3807 11.6749 27.4394 14.2298 27.4402 16.894C27.4391 19.558 26.3804 22.1127 24.4966 23.9964C22.6128 25.8802 20.0582 26.9389 17.3942 26.94C14.73 26.9392 12.1751 25.8806 10.2911 23.9968C8.40712 22.113 7.34823 19.5582 7.34717 16.894Z" fill="white" />
                <path fill-rule="evenodd" clip-rule="evenodd" d="M23.0252 22.525C23.6702 21.88 24.7172 21.88 25.3622 22.525L28.5502 25.713C29.1952 26.358 29.1952 27.405 28.5502 28.05C27.9052 28.695 26.8582 28.695 26.2132 28.05L23.0262 24.863C22.3812 24.217 22.3812 23.171 23.0262 22.526L23.0252 22.525Z" fill="white" />
              </g>
              <defs>
                <clipPath id="clip0_740_3">
                  <rect width="35" height="34" fill="white" />
                </clipPath>
              </defs>
            </svg>
          </span>
            Vsegastroli.ru
        </a>

        <!-- Main navigation -->
        <nav class="offcanvas offcanvas-start" id="navbarNav" tabindex="-1" aria-labelledby="navbarNavLabel">
            <div class="offcanvas-header py-3">
                <h5 class="offcanvas-title" id="navbarNavLabel">Меню сайта</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body pt-2 pb-4 py-lg-0 mx-lg-auto">
                <ul class="navbar-nav position-relative">

                    <!-- Главная всегда ведет на / -->
                    <li class="nav-item py-lg-2 me-lg-n2 me-xl-0">
                        <a class="nav-link {{ request()->routeIs('main') ? 'active' : '' }}" href="{{ route('main') }}">
                            Главная
                        </a>
                    </li>

                    <!-- Все мероприятия -> если город выбран, ссылка с чпу, иначе неактивная ссылка -->
                    <li class="nav-item py-lg-2 me-lg-n2 me-xl-0">
                        @if(!empty($currentCity))
                            <a class="nav-link" href="{{ route('city.all-events', ['city' => $currentCity->slug]) }}">
                                Все мероприятия
                            </a>
                        @else
                            <span class="nav-link text-muted" title="Выберите город">Все мероприятия</span>
                        @endif
                    </li>


                    <!-- Категории / примеры. Можно заменить на реальный массив в цикле -->
                    @foreach($mainCategories ?? [] as $category)
                        <li class="nav-item py-lg-2 me-lg-n2 me-xl-0">
                            @if(!empty($defaultCity))
                                <!-- С выбранным городом -->
                                <a class="nav-link" href="{{ route('category.show', ['city' => $defaultCity->slug, 'category' => $category->slug]) }}">
                                    {{ $category->name }}
                                </a>
                            @else
                                <!-- Без города -->
                                <a class="nav-link" href="{{ route('category.show.no-city', ['category' => $category->slug]) }}">
                                    {{ $category->name }}
                                </a>
                            @endif
                        </li>
                    @endforeach

                    <!-- Площадки, Кассы - статичные, примеры -->
                    <li class="nav-item py-lg-2 me-lg-n2 me-xl-0">
                        <a class="nav-link" href="#">Площадки</a>
                    </li>
                    <li class="nav-item py-lg-2 me-lg-n2 me-xl-0">
                        <a class="nav-link" href="#">Кассы продаж</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Button group -->
        <div class="d-flex gap-sm-1">
            <!-- Текущий город -->
            <a class="btn btn-outline-secondary border-0 animate-shake ms-1 me-1 mb-0" id="currentCityBtn" data-bs-toggle="modal" data-bs-target="#cityModal">
                <i class="fi-map-pin animate-target me-2"></i>
                <span id="currentCityName">
                    {{ $currentCity?->name ?? 'Город не выбран' }}
                </span>
            </a>

            <!-- Add business button  -->
            <a class="btn btn-primary animate-scale rounded-pill text-capitalize" href="#">
                <span class="d-none d-md-inline me-1">Реклама</span> для организаторов
            </a>
        </div>
    </div>
</header>
