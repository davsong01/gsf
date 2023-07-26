<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Logo extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $width;
    public $height;
    public $class;
    
    public function __construct($width, $height, $class = '')
    {
        $this->width = $width ?? NULL;
        $this->height = $height ?? NULL;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.logo');
    }
}
