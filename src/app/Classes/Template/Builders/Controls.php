<?php

namespace LaravelEnso\VueDatatable\app\Classes\Template\Builders;

use LaravelEnso\VueDatatable\app\Classes\Attributes\Controls as Attributes;

class Controls
{
    private $template;
    private $defaultControls;

    public function __construct($template)
    {
        $this->template = $template;
        $this->defaultControls = config('enso.datatable.controls');
    }

    public function build()
    {
        $this->template->set('controls', $this->preset(Attributes::List));
    }

    private function compute($controls)
    {
        return collect($this->defaultControls)->intersect($controls)
            ->values()->unique()->implode(' ');
    }

    private function preset($controls)
    {
        return collect($controls);
    }
}
