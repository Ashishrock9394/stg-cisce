<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GetStudentList extends Component
{


    public $classes;
    public $type;
    public $cwsnType;
    public $title;
    /**
     * Create a new component instance.
     *
     * @param array $classes
     */
    public function __construct($classes = [], $type, $cwsnType = null, $title = null)
    {
        $this->classes  = $classes;
        $this->type = $type;
        $this->cwsnType = $cwsnType;
        $this->title = $title;
    }
     /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.get-student-list');
    }
}
