<?php

namespace Efemer\Higg\Helpers;

const ASSET_CSS = 'css';
const ASSET_JS = 'js';

//use StrHelper;

class AssetHelper {

    function styles($assets){
        if (!is_array($assets)) $assets = [ $assets ];
        $lines = '';
        foreach ($assets as $url) {
            $lines .= html()->style($url);
        }
        return $lines;
    }

    function scripts($assets){
        if (!is_array($assets)) $assets = [ $assets ];
        $lines = '';
        foreach ($assets as $url) {
            $lines .= html()->script($url);
        }
        return $lines;
    }

    function assetUrls($assetType){
        $urls = [];
        $assetKey = 'assets.'.$assetType;
        $globalKey = "assets.global" . ucfirst($assetType);
        $assets = config($assetKey);
        $global = config($globalKey);
        if (!empty($global)) $assets = array_merge($global, $assets);
        if (!empty($assets)) {
            foreach ($assets as $asset) {
                $urls[] = $this->assetUrl($asset);
            }
        }
        return $urls;
    }

    function assetUrl($path){
        // $prefix = config('assets.prefix');
        // $scheme = config('assets.forceScheme');
        if (!StrHelper::startsWith($path, ['//', 'http'])) {
            // TODO serve asset url into cdn url if applicable
            $path = asset($path);
        }
        return $path;
    }



}