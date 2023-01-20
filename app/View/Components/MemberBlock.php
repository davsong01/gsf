<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MemberBlock extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $size;
    public $user;

    public function __construct($size, $user)
    {
        $this->size = $size;
        $this->user = $user;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        dd($this->user);
        return view('components.member-block');
    }
}
