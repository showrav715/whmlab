<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Frontend;

class CookiePolicy extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $cookie;

    public function __construct()
    {
        $this->cookie = Frontend::where('data_keys','cookie.data')->first();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.cookie-policy');
    }
}
