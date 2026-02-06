document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';

    const cityModalEl = document.getElementById('cityModal');
    const saveBtn = document.getElementById('saveCityBtn');
    const citySelectModal = document.getElementById('citySelectModal'); // select в модалке
    const searchCitySelect = document.getElementById('searchCitySelect'); // select в форме поиска на главной
    const currentCityBtn = document.getElementById('currentCityName'); // элемент в header, показывающий выбранный город (опционально)

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    function updateSearchCity(citySlug, cityName) {
        if (searchCitySelect) {
            searchCitySelect.value = citySlug || '';
        }
        if (currentCityBtn) {
            currentCityBtn.textContent = cityName || 'Город';
        }
    }

    async function saveCity(citySlug, cityName) {
        const csrf = getCsrfToken();
        const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';

        if (!csrf) {
            console.error('CSRF token not found');
            return;
        }

        try {
            const resp = await fetch(`${baseUrl}/set-city`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ city_slug: citySlug })
            });

            if (!resp.ok) throw new Error('Network response was not ok');

            const data = await resp.json();
            if (data.success) {
                localStorage.setItem('selectedCitySlug', citySlug);
                localStorage.setItem('selectedCityName', cityName);

                updateSearchCity(citySlug, cityName);

                if (cityModalEl) {
                    const bsModal = bootstrap.Modal.getInstance(cityModalEl) || new bootstrap.Modal(cityModalEl);
                    bsModal.hide();
                }
            } else {
                console.error('Server error saving city', data);
                alert(data.message || 'Ошибка при сохранении города');
            }
        } catch (err) {
            console.error(err);
            alert('Ошибка при сохранении города.');
        }
    }


    // Обработчик кнопки Save в модальном окне
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            const sel = citySelectModal;
            if (!sel) return alert('Не выбран город');
            const selectedSlug = sel.value;
            const selectedName = sel.options[sel.selectedIndex]?.text || selectedSlug;
            if (!selectedSlug) return alert('Выберите город');

            saveCity(selectedSlug, selectedName);
        });
    }

    // При загрузке — если в localStorage есть город — применяем его в форме поиска
    const initialCitySlug = localStorage.getItem('selectedCitySlug');
    const initialCityName = localStorage.getItem('selectedCityName');
    if (initialCitySlug && initialCityName) {
        updateSearchCity(initialCitySlug, initialCityName);
        if (citySelectModal) citySelectModal.value = initialCitySlug;
    }
});
