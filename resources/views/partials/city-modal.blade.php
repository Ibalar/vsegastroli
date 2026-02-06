<div class="modal fade" id="cityModal" tabindex="-1" role="dialog" aria-labelledby="cityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cityModalLabel">
                    Выберите город
                    <span class="ms-2 small text-muted">
                        @if($currentCity?->name)
                            (Ваш город: <b>{{ $currentCity->name }}</b>)
                        @else
                            (Город не выбран)
                        @endif
                    </span>
                </h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div id="cityListSection">
                    <ul class="nav nav-pills flex-wrap" id="cityList" style="gap: .5rem;">
                        <!-- Сюда js отрендерит города -->
                    </ul>
                </div>
                <div id="cityDetectMsg" class="mt-3"></div>
            </div>
            <div class="modal-footer flex-column flex-sm-row align-items-stretch">
                <button class="btn btn-primary w-100 w-sm-auto" type="button" id="chooseCityBtn" disabled>
                    Выбрать
                </button>
                <button class="btn btn-secondary mt-2 mt-sm-0 ms-sm-2 w-100 w-sm-auto" type="button" data-bs-dismiss="modal">
                    Закрыть
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedCityId = null;
    let allCities = [];
    const cityListEl = document.getElementById('cityList');
    const cityDetectMsg = document.getElementById('cityDetectMsg');
    const chooseCityBtn = document.getElementById('chooseCityBtn');

    function renderCityList(cities, detectedCity) {
        cityListEl.innerHTML = '';
        cities.forEach(city => {
            const li = document.createElement('li');
            li.className = 'nav-item';
            const link = document.createElement('a');
            link.href = '#';
            link.className = 'nav-link rounded';
            link.textContent = city.name;
            link.dataset.cityId = city.id;

            // Если есть определённый город или это текущий город
            if (detectedCity && detectedCity.id == city.id) {
                link.classList.add('active');
                selectedCityId = city.id;
            }

            link.addEventListener('click', function (e) {
                e.preventDefault();
                // Убираем active у всех ссылок
                document.querySelectorAll('#cityList .nav-link').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
                selectedCityId = city.id;
                chooseCityBtn.disabled = false;
            });

            li.appendChild(link);
            cityListEl.appendChild(li);
        });

        chooseCityBtn.disabled = !selectedCityId;
    }

    function loadCitiesAndRender() {
        // Сбрасываем состояние
        selectedCityId = null;
        chooseCityBtn.disabled = true;
        cityDetectMsg.textContent = '';
        cityListEl.innerHTML = '<li class="nav-item"><span class="nav-link">Загрузка...</span></li>';

        fetch('/api/city/detect')
            .then(response => response.json())
            .then(data => {
                allCities = data.cities;
                renderCityList(allCities, data.detected_city);

                if (data.detected_city) {
                    cityDetectMsg.textContent = 'Ваш город: ' + data.detected_city.name + '. Если это не ваш город, выберите нужный.';
                } else {
                    cityDetectMsg.textContent = 'Если Ваш Город не определён или определен не верно, выберите из списка.';
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки городов:', error);
                cityListEl.innerHTML = '<li class="nav-item"><span class="nav-link text-danger">Ошибка загрузки городов</span></li>';
                cityDetectMsg.textContent = 'Не удалось загрузить список городов. Попробуйте позже.';
            });
    }

    // Главное исправление: загружаем города каждый раз при открытии модального окна
    document.getElementById('cityModal').addEventListener('shown.bs.modal', function () {
        loadCitiesAndRender();
    });

    // Функция для программного открытия модального окна
    function openCityModal() {
        const modal = new bootstrap.Modal(document.getElementById('cityModal'));
        modal.show();
    }

    // Обработчик кнопки "Выбрать"
    chooseCityBtn?.addEventListener('click', function () {
        if (!selectedCityId) return;

        // Блокируем кнопку на время запроса
        chooseCityBtn.disabled = true;
        chooseCityBtn.textContent = 'Сохраняем...';

        fetch('/api/city/set', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({ city_id: selectedCityId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    // Восстанавливаем кнопку при ошибке
                    chooseCityBtn.disabled = false;
                    chooseCityBtn.textContent = 'Выбрать';
                    alert('Ошибка при сохранении города. Попробуйте ещё раз.');
                }
            })
            .catch(error => {
                console.error('Ошибка сохранения города:', error);
                chooseCityBtn.disabled = false;
                chooseCityBtn.textContent = 'Выбрать';
                alert('Ошибка при сохранении города. Попробуйте ещё раз.');
            });
    });

    // Автоматическое открытие при первом визите
    @if($showCityModal ?? false)
    document.addEventListener('DOMContentLoaded', function() {
        openCityModal();
    });
    @endif
</script>
