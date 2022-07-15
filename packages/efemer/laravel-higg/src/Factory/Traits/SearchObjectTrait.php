<?php

namespace Efemer\Higg\Factory\Traits;

Trait SearchObjectTrait {

    protected $searchObjectField = 'search_object';
    protected $searchIndexName = null;
    protected $hasIndex = false;

    public function __construct(){
        $this->searchIndexName = property_exists($this, 'searchIndexName') ? $this->searchIndexName : $this->table ;
        $this->hasIndex = property_exists($this, $this->searchObjectField);
        $this->checkFieldDefinition();
    }

    public function checkFieldDefinition(){
        $field = $this->getFieldConfig($this->searchObjectField);
        if (emopty($field) && $this->hasIndex) {
            $fieldConfig = [ 'cast' => CAST_ARRAY ];
            $this->addField($this->searchObjectField, $fieldConfig);
        }
    }

    public function syncSearchObject(){
        // @todo sync elastic search object
    }

    public function findSearchObject(){
        // @todo find elastic search object
    }

    public function browseSearchObjects(){
        // @todo drop elastic search objects within index
    }

    public function dropSearchObjects(){
        // @todo drop elastic search object
    }

    public function querySearchObjects(){
        // @todo run query on elastic search objects
    }

} // end class