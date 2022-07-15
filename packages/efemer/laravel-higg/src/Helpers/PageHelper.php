<?php

namespace Efemer\Higg\Helpers;

class PageHelper {


    function meta($key){
        return cfg('web.meta.'.$key);
    }
    function title($title = null){
        if (is_null($title)) $title = cfg('web.title');
        if (!empty($append = cfg('web.signature'))) {
            $title = "{$title} // {$append}";
        }
        return $title;
    }
    function description(){
        return $this->meta('description');
    }
    function keywords(){
        return $this->meta('keywords');
    }
    function author(){
        return "@JohnEfemer";
    }

    function logo($variation = null, $srcset = false){
        $logo = config('web.logo');
        if (empty($logo)) return null;
        if (!empty($variation)) $logo = cfg('web.logo.'.$variation);
        if (is_string($logo)) return $logo;
        if ($srcset) {
            $src = []; $scale = 1;
            foreach($logo as $res) $src[] = url($res) . $scale++ . 'x';
            return implode(" ", $src);
        } 
        return array_shift($logo);
    } // end 

    function actions($key){
        return app("higg.action")->filter($key);
    }


}