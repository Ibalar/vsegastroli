document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filterForm');
    if (!filterForm) return;

    // Попытка взять сохранённый город из localStorage
    const savedCitySlug = localStorage.getItem('selectedCitySlug') || '';

    // Если форма находится на странице /events/{city}/{category}, URL pathParts поможет выставить selects
    const pathParts = window.location.pathname.replace(/^\/|\/$/g, '').split('/');
    // ожидаем: ['events', '<city?>', '<category?>', ...]
    let urlCitySlug = '';
    let urlCategorySlug = '';
    if (pathParts[0] === 'events') {
        urlCitySlug = pathParts[1] || '';
        urlCategorySlug = pathParts[2] || '';
    }

    const citySelect = filterForm.querySelector('select[name="city_slug"]');
    const categorySelect = filterForm.querySelector('select[name="category"]');

    // Установим значения select при загрузке: сначала URL, затем localStorage
    if (citySelect) {
        citySelect.value = urlCitySlug || savedCitySlug || citySelect.value || '';
    }
    if (categorySelect) {
        categorySelect.value = urlCategorySlug || categorySelect.value || '';
    }

    function buildAndSubmit() {
        const q = filterForm.querySelector('input[name="q"]')?.value || '';
        const dateStart = filterForm.querySelector('input[name="date_start"]')?.value || '';
        const dateEnd = filterForm.querySelector('input[name="date_end"]')?.value || '';
        const city = citySelect ? citySelect.value : '';
        const category = categorySelect ? categorySelect.value : '';

        // Строим базовый путь: /events or /events/{city} or /events/{city}/{category}
        let path = '/events';
        if (city) path += '/' + encodeURIComponent(city);
        if (category) path += '/' + encodeURIComponent(category);

        // Параметры запроса (поиск/даты)
        const params = new URLSearchParams();
        if (q) params.set('q', q);
        if (dateStart) params.set('date_start', dateStart);
        if (dateEnd) params.set('date_end', dateEnd);

        const url = path + (params.toString() ? '?' + params.toString() : '');
        window.location.href = url;
    }

    // Подвязываем события
    filterForm.addEventListener('submit', function (ev) {
        ev.preventDefault();
        buildAndSubmit();
    });

    if (citySelect) citySelect.addEventListener('change', buildAndSubmit);
    if (categorySelect) categorySelect.addEventListener('change', buildAndSubmit);
});
