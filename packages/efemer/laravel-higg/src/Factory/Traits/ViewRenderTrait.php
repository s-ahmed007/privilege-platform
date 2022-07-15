<?php

namespace Efemer\Higg\Factory\Traits;

trait ViewRenderTrait {

    function render($viewLocation){
        $viewNamespace = property_exists($this, 'viewNamespace') ? $this->viewNamespace : '';
        $viewPrefix = property_exists($this, 'viewPrefix') ? $this->viewPrefix : '';
        if (!empty($viewNamespace)) $viewNamespace = $viewNamespace . '::';
        if (!empty($viewPrefix)) $viewPrefix = $viewNamespace . $viewPrefix . '.';
        $viewLocation = $viewPrefix . $viewLocation;
        if (view()->exists($viewLocation)) {
            return view($viewLocation);
        }
        abort(404, "View not found {$viewLocation}");
    }

}