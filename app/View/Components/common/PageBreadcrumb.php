<?php

namespace App\View\Components\common;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageBreadcrumb extends Component
{
    public string $pageTitle;
    public array $breadcrumbs;

    /**
     * Create a new component instance.
     */
    public function __construct(string $pageTitle = 'Page', array $breadcrumbs = [])
    {
        $this->pageTitle = $pageTitle;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.common.page-breadcrumb');
    }
}
