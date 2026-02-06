
<section class="fn-gradient-secondary py-3">

    <div class="d-flex align-items-center justify-content-center gap-3">
        <div class="overflow-x-auto">
            <ul class="nav nav-pills flex-nowrap gap-2 text-nowrap px-2">
                @php
                    $currentCitySlug = $currentCity?->slug ?? null;
                    $currentCategorySlug = $currentCategory?->slug ?? null;
                @endphp

                @foreach($categories as $category)
                    @php
                        if (!empty($currentCitySlug)) {
                            $url = route('category.show', [
                                'city' => $currentCitySlug,
                                'category' => $category->slug
                            ]);
                        } else {
                            $url = route('category.show_no_city', [
                                'category' => $category->slug
                            ]);
                        }

                        $isActive = (!empty($currentCategorySlug) && $currentCategorySlug === $category->slug);
                    @endphp

                    <li class="nav-item me-1">
                        <a
                            class="nav-link text-white {{ $isActive ? 'active' : '' }}"
                            href="{{ $url }}"
                        >
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
