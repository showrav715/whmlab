<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Language as LanguageModel;

class Language extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $language;

    public function __construct()
    {
        $this->language = LanguageModel::all();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.language');
    }
}
