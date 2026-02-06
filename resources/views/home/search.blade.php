<form action="{{ $currentCity? route('home', ['city'=>$currentCity->slug]) : route('main') }}" method="GET" class="position-relative z-3 bg-body border rounded shadow p-2 mb-4">
    <div class="d-flex flex-column flex-md-row gap-2 p-1">
        <div class="d-flex flex-column flex-md-row w-100 gap-2 gap-sm-3">
            <div class="position-relative w-100">
                <i class="fi-search position-absolute top-50 start-0 translate-middle-y fs-xl text-secondary-emphasis ms-2"></i>
                <input type="search"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control form-control-lg form-icon-start border-0 rounded-0 pe-0"
                       placeholder="Поиск по событиям и площадкам">
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="fi-calendar fs-base"></i></span>
                <input type="text" name="date_start" class="form-control"
                       data-datepicker='{"dateFormat": "d.m.Y"}'
                       placeholder="с даты"
                       value="{{ request('date_start') }}">
                <input type="text" name="date_end" class="form-control"
                       data-datepicker='{"dateFormat": "d.m.Y"}'
                       placeholder="по дату"
                       value="{{ request('date_end') }}">
            </div>
            @if($currentCity)
                <input type="hidden" name="city_slug" value="{{ $currentCity->slug }}">
            @else
                <div class="position-relative w-100" style="max-width: 200px">
                    <i class="fi-map-pin position-absolute top-50 start-0 translate-middle-y z-1 fs-xl text-secondary-emphasis ms-2"></i>
                    <select
                        data-select='{
                                    "classNames": {
                                      "containerInner": ["form-select", "form-select-lg", "form-icon-start", "border-0"]
                                    },
                                    "searchEnabled": true,
                                    "searchPlaceholderValue": "Поиск..."
                                  }'
                        name="city_slug"
                        id="searchCitySelect"
                        class="form-select form-select-lg form-icon-start border-0"
                        aria-label="Выбор города"
                        data-placeholder="Выбор города"
                        required
                    >
                        <option value="">Город</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->slug }}"
                                {{ (request('city_slug') == $city->slug || (isset($currentCity) && $currentCity->slug == $city->slug)) ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach

                    </select>
                </div>
            @endif
        </div>
        <button type="submit" class="btn btn-lg btn-primary">Найти</button>
    </div>
</form>
