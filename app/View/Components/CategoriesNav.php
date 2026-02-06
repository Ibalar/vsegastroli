<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Category;

class CategoriesNav extends Component
{

    public $categories;
    public $currentCity;
    public $currentCategory;

    /**
     * @param \App\Models\City|null $currentCity
     * @param \App\Models\Category|null $currentCategory
     */
    public function __construct($currentCity = null, $currentCategory = null)
    {
        $this->categories = Category::active()->orderBy('sort_order')->get();
        $this->currentCity = $currentCity;
        $this->currentCategory = $currentCategory;
    }

    public function render(): View
    {
        return view('components.categories-nav');
    }

}
