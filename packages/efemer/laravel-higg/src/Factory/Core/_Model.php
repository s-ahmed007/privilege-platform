<?php namespace Efemer\Higg;

class Model extends \Eloquent {

    public $composite = array( 'params' => JSON_FORMAT );                           // explode/implode json, bin, csv
    public $guarded = array( TS_CREATED_AT, TS_UPDATED_AT, TS_DELETED_AT );         // dont auto assign
    public $protected = array();                                                    // hide sensitive columns from direct exposure
    public $acl = array();                                                          // allowed access to by role/callback
    public $textSearch = array();                                                   // columns treated with LIKE operator
    public $soundexSearch = false;                                                  // columns treated with SOUNDEX operator
    public $associateWith = array();                                                // dynamic model fillings
    public $validation = array();                                                   // validation rules
    public $columns = array();                                                      // table column names
    public $callback = array();                                                     // extend operations with colsure, arg passed as array
    public $timestamps = true;
    public $query = null;
    public $snapshot = null;
    public static $cacheThings = null;


    function isCached($key){
        return (isset(self::$cacheThings[$key])) ? true : false;
    }
    function getCached($key, $else = null){
        return $this->onCache($key, function() use ($else){
            return $else;
        });
    }
    function setCached($key, $value){
        return $this->onCache($key, function() use ($value){
            return $value;
        });
    }
    function onCache($key, $setter){
        if (!isset(self::$cacheThings[$key])) {
            if (isset($setter)) {
                $gem = $setter();
                self::$cacheThings[$key] = $gem;
            }
        }
        return isset(self::$cacheThings[$key]) ? self::$cacheThings[$key] : null;
    }

    // model has composite columns
    function hasCompositeColumns(){
        return (!empty($this->composite)) ? true : false;
    }
    function explodeCompositeColumnName($column){
        if (strpos($column, '.') !== FALSE) {
            $parts = explode('.', $column);
            $column = $parts[0]; unset($parts[0]);
            $map = implode('.', $parts);
            if ($this->isCompositeColumn($column)) {
                return array($column, $map);
            }
        }
        return false;
    }
    // column has assigned value
    function isFilled($column){
        $value = array_get($this->toArray(), $column);
        return (!is_null($value)) ? true : false;
    }
    function isNewObject(){
        if (isset($this->id) && !empty($this->id)) {
            return false;
        }
        return true;
    }
    function isFieldEdited($field){
        if (strpos($field, '.')) {
            $parts = explode('.', $field);
            if ($this->isFieldEdited($parts[0])) {
                $old = array_get($this->snapshot, $field);
                $new = $this->getComposite($field);
                return ($old != $new) ? true : false;
            }
        } else {
            if (isset($this->snapshot[$field]) && isset($this->{$field})) {
                if ($this->snapshot[$field] != $this->{$field}) {
                    return true;
                }
            }
        }
        return false;
    }
    function getEditedFields(){
        $edited = array();
        foreach($this->columns as $column) {
            $column = $column['name'];
            if (!$this->isCompositeColumn($column)) {
                if ($this->isFieldEdited($column)) {
                    $edited[$column] = $this->snapshot[$column];
                }
            } else {
                $compositeEdited = array();
                if (isset($this->{$column}) && !empty($this->{$column})) {
                    $composite = $this->{$column};
                    foreach($composite as $key => $val) {
                        $fieldmap = "{$column}.{$key}";
                        $old = array_get($this->snapshot, $fieldmap);
                        $new = $this->getComposite($fieldmap);
                        if ($old != $new) {
                            $compositeEdited[$key] = $old;
                        }
                    }
                    if (!empty($compositeEdited)) $edited[$column] = $compositeEdited;
                }
            }
        }
        return $edited;
    }

    function isLocked(){
        if (isset($this->locked) && !empty($this->locked)) {
            return true;
        }
        return false;
    }

    function replenish(){
        if (isset($this->id) && !empty($this->id)) {
            $model = $this->getObject($this->id);
            return $model;
        }
        return $this;
    }
    // remove sensitive columns
    function protect($fields = null){
        $fields = is_null($fields)? $this->protected : $fields;
        if (!empty($fields)) {
            foreach($fields as $column) {
                if ($this->isFilled($column)) {
                    if (strpos($column, '.')) {
                        $compositeName = $this->explodeCompositeColumnName($column);
                        if ($compositeName) {
                            $value = $this->{$compositeName[0]};
                            $value = array_forget($value, $compositeName[1]);
                            $this->{$compositeName[0]} = $value;
                        }
                    } else {
                        unset($this->$column);
                    }
                }
            }
        }
        return $this;
    }

    // mysql table column names
    function getColumnNames(){
        if (!empty($this->columns)) return $this->columns;
        $connection = $this->connection;
        $db = \DB::connection($connection);
        $sql = "SHOW COLUMNS FROM " . $this->getTable();
        $raw = $db->select($sql);
        $columns = array();
        foreach($raw as $c) {
            $columns[$c->Field] = array( 'name' => $c->Field, 'type' => $c->Type );
        }
        $this->columns = $columns;
        return $this->columns;
    }

    function columnValues(){
        $data = $this->toArray();
        $columns = array();
        foreach($data as $c => $v) {
            if ($this->isRealColumn($c)) {
                $columns[$c] = $v;
            }
        }
        return $columns;
    }

    function dataRows($where = null){
        $count = $this->countObjects(array('where' => $where));
        $tableRows = array();

        if (!empty($count)) {
            $pages = ceil($count / 500);
            for($i=1;$i<=$pages;$i++) {
                $rows = $this->getObjectCollection(array('page' => $i));
                //$first = $rows->first();
                //$tableRows[] = array_keys($first->columnValues());
                foreach($rows as $model) {
                    $tableRows[] = $model->implode()->columnValues();
                }
            }
        }

        return $tableRows;

    }

    function dataExport(){
        $rows = $this->dataRows();
        $excel = \App::make('excel');
        $tableName = $this->table;
        $filename = array($this->table);
        if (isset($this->classType)) $filename[] = $this->classType;
        $filename[] = date('Y-m-d');
        $filename = implode('_', $filename);
        $excel->create($filename, function($csv) use ($rows, $tableName){

            $csv->setTitle('Higg Exports');
            $csv->setCreator('John Efemer')->setCompany('John Efemer');
            $csv->setDescription('Higg Exports');

            $csv->sheet($tableName, function($sheet) use ($rows){
                $sheet->with($rows);
            });

        })->download('xlsx');
        exit;
    }

    // is a real table column
    function isRealColumn($column){
        if (empty($this->columns)) $this->getColumnNames();
        return isset($this->columns[$column]) ? true : false;
    }

    function isCompositeColumn($column){
        if (!empty($this->composite)) {
            if (isset($this->composite[$column])) return true;
        }
        return false;
    }

    // assign column values from array or post data
    public function assign($data = null){
        $this->snapshot = (isset($this->id)) ? $this->toArray() : null;
        if (is_null($data)) $data = Higg::post();
        if (!empty($data)) {
            foreach($data as $column => $value) {
                if ($this->isRealColumn($column) && !$this->isGuarded($column)) {
                    if ($this->isCompositeColumn($column)) {
                        $this->setComposite($column, $value);
                    } else {
                        $this->$column = $value;
                    }
                }
            }
        }
        return $this;
    }

    // json to array
    function explode_json($string, $as_array = true){
        if (Higg::isJSON($string)) {
            return json_decode($string, $as_array);
        } /*
        else if( $string === ""){
            return array();
        } */
        return $string;
    }
    // array to json string
    function implode_json($object){
        if (is_array($object) || is_object($object)) {
            return json_encode($object);
        }
        return $object;
    }

    // csv to array
    function explode_csv($data){
        if (is_string($data)) {
            return str_getcsv($data);
        }
        return $data;
    }
    // array to csv string
    function implode_csv($data) {
        if (is_array($data)) {
            $handle = fopen('php://temp', 'r+');
            fputcsv($handle, $data, ',', '"');
            rewind($handle); $csv = '';
            while (!feof($handle)) {
                $csv .= fread($handle, 8192);
            }
            fclose($handle);
            return $csv;
        }
        return $data;
    }

    // binary to hex string
    function explode_bin($data){
        return bin2hex($data);
    }
    // hex to binary
    function implode_bin($data){
        if (is_string($data)) {
            return hex2bin($data);
        }
        return $data;
    }

    // decode composite columns
    public function explode(){
        if ($this->hasCompositeColumns()) {
            foreach($this->composite as $column => $encoding) {
                if (is_array($encoding)) $encoding = $encoding['format'];
                if ($this->isFilled($column)) {
                    switch($encoding){
                        case JSON_FORMAT:
                            $this->$column = $this->explode_json($this->$column);
                            break;
                        case BIN_FORMAT:
                            $this->$column = $this->explode_bin($this->$column);
                            break;
                        case CSV_FORMAT:
                            $this->$column = $this->explode_csv($this->$column);
                            break;
                    }
                }
            }
        }
        return $this;
    }

    // implode columns to db data type representation (mostly everything to string)
    public function implode(){
        if ($this->hasCompositeColumns()) {
            foreach($this->composite as $column => $encoding) {
                if (is_array($encoding)) $encoding = $encoding['format'];
                if ($this->isFilled($column)) {
                    switch($encoding){
                        case JSON_FORMAT:
                            $this->$column = $this->implode_json($this->$column);
                            break;
                        case BIN_FORMAT:
                            $this->$column = $this->implode_bin($this->$column);
                            break;
                        case CSV_FORMAT:
                            $this->$column = $this->implode_csv($this->$column);
                            break;
                    }
                }

            }
        }
        return $this;
    }

    function validateBeforeSave(){
        $rules = $this->validation;
        if (empty($rules)) return true;
        if (!$this->isNewObject()){
            $editModeRules = array();
            foreach($rules as $field => $condition) {
                if (isset($data[$field]) && $this->isFieldEdited($field)) $editModeRules[$field] = $condition;
            }
            if (empty($editModeRules)) return true;
            $rules = $editModeRules;
        }
        return $this->isValid($rules);
    }

    function validateField($field, $rule = null){
        $rule = ($rule)?:$this->validation[$field];
        return $this->isValid(array($field => $rule));
    }

    function isValid($rules = null, $data = null){
        $rules = ($rules) ?: $this->validation;
        $data = ($data) ?: $this->toArray();
        $messages = null;

        if (!empty($rules)) {
            $messages = array();
            $validatingRules = array();
            foreach($rules as $rule => $condition) {
                if (is_array($condition)) {
                    $validatingRules[$rule] = $condition[0];
                    $messages = array_merge($messages, $condition[1]);
                } else {
                    $validatingRules[$rule] = $condition;
                }
            }
            $rules = $validatingRules;
            $validator = \Validator::make( $data, $rules, $messages );
            if ($validator->fails()) {
                $messages = $validator->messages();
                foreach($messages->all() as $message) {
                    \Process::withError(true, $message);
                }
                \Process::completed();
                return false;
            }
        }
        return true;
    }


    function associateWith($associate = null){
        $model = $this;
        if (is_null($associate)) $associate = $model->associateWith;
        if (!empty($associate)) {
            if (!is_array($associate)) $associate = array($associate);
            foreach($associate as $assoc) {
                if (is_callable(array($model, (string)$assoc))) {
                    $model->{$assoc} = call_user_func(array($model, (string)$assoc));
                }
            }
        }
        return $model;
    }

    function objectId($token = null){
        $model = $this;
        if (is_null($token)) {
            $token = uniqid();
        }
        if ($model->isRealColumn('object_id')) {
            $model->object_id = \Str::slug($token);
        }
        return $model;
    }
    function userId($user_id = null){
        $model = $this;
        if ($model->isRealColumn('user_id')) {
            if (is_null($user_id)) {
                $user_id = (\Auth::check()) ? \Auth::user()->id : 0;
            }
            $model->user_id = $user_id;
        }
        return $model;
    }

    function prepQuery( $conditions = array() ){
        $model = $this;
        $builder = $model->newQuery();

        $join = isset($conditions['join']) ? $conditions['join'] : false;
        $joins = isset($conditions['joins']) ? $conditions['joins'] : [];
        $mergejoin = isset($conditions['mergejoin']) ? $conditions['mergejoin'] : false;
        $where = isset($conditions['where']) ? $conditions['where'] : false;
        $select = isset($conditions['select']) ? $conditions['select'] : false;
        $sort = isset($conditions['sort']) ? $conditions['sort'] : false;
        $search = isset($conditions['find']) ? $conditions['find'] : false;

        if (!empty($join)) $joins[] = $join;

        // JOIN
        if (!empty($joins) && is_array($joins) && count($joins) > 0) {
            foreach($joins as $join) {

                if (is_object($join[0])) {
                    $joiningModel = $join[0];
                    $closure = $join[1];
                    $joiningTable = $joiningModel->getTable();
                    $builder->join($joiningTable, $closure);

                    // distinguish column names for joining tables
                    if (empty($select)) {
                        $select = array();
                        foreach($model->getColumnNames() as $column => $desc) {
                            $select[] = $model->getTable() . ".{$column} as {$column}";
                        }
                        if ($mergejoin) {
                            foreach($joiningModel->getColumnNames() as $column => $desc) {
                                $select[] = $joiningModel->getTable() . ".{$column} as " . $joiningModel->getTable() . ".{$column}";
                            }
                        }
                    }
                } else {

                    // [ 'label_maps', 'products.id = label_maps.map_to' ]
                    if (count($join) == 2) {
                        $compare = explode(' ', $join[1]);
                        if (count($compare) == 3) {
                            $builder->join($joins[0], $compare[0], $compare[1], $compare[2] );
                        }
                    }
                }

            } // end foreach

        }

        // SELECT
        if (!empty($select)) {
            if (!is_array($select)) $select = explode(',', $select);
            $select = array_map('trim', $select);
            $builder->select($select);
        }

        // WHERE
        $ignoreValues = isset($conditions['ignoreValues']) ? $conditions['ignoreValues'] : array( '' );
        if (!empty($where)) {
            foreach($where as $column => $match) {

                if (!empty($ignoreValues)) {
                    $skip = false;
                    foreach($ignoreValues as $value){
                        if ($match === $value) $skip = true;
                    }
                    if ($skip) continue;
                }

                if (is_string($column)) {
                    if (strpos($column, ' ') === false) {
                        if (!in_array($column, $this->textSearch)) {
                            if ($match === 'null') {
                                $builder->whereNull($column);
                            } else {
                                $builder->where($column, $match);
                            }
                        } else {
                            $builder->where($column, 'LIKE', "%{$match}%");
                        }
                    } else {
                        $parts = explode(' ', $column, 2);
                        if ($parts[1] == 'in') {
                            $builder->whereIn( $parts[0], $match );
                        } elseif ($parts[1] == 'notIn') {
                            $builder->whereNotIn( $parts[0], $match );
                        } else {
                            $builder->where( $parts[0], $parts[1], $match );
                        }
                    }
                } else {
                    $builder->whereRaw($match);
                }

            }
        }

        // SORT
        if (!empty($sort)) {
            if ( !is_array($sort) && strtoupper($sort) == 'RAND') {
                $builder->orderByRaw('RAND()');
            } else {
                foreach($sort as $column => $order) {
                    $builder->orderBy($column, $order);
                }
            }
        }

        // SEARCH -- MUST HAVE FULLTEXT INDEX or text_search soundex cache
        if (!empty($search)) {
            if (!empty($this->soundexSearch)) {
                $words = explode(' ', $search);
                $matchSoundex = soundex(array_shift($words));
                $builder->whereRaw( "MATCH({$this->soundexSearch}) AGAINST('{$matchSoundex}' IN NATURAL LANGUAGE MODE)" );
            } else {
                if (!empty($this->textSearch)) {
                    $textSearch = implode(', ', $this->textSearch);
                    $builder->whereRaw( "MATCH( {$textSearch} ) AGAINST(? IN BOOLEAN MODE)", array($search));
                }
            }
        }

        return $builder;

    } // end prepQuery

    function prepCollection( $conditions = null, $return = RETURN_COLLECTION){
        $model = $this;
        $builder = $model->prepQuery($conditions);

        $page = isset($conditions['page']) ? (int)$conditions['page'] : 1;
        $limit = isset($conditions['limit']) ? (int)$conditions['limit'] : null;

        if ($limit !== null) {
            $skip = ($page - 1) * $limit;
            $builder->skip($skip)->take($limit);
        }

        $collection = $builder->get();
        if (!empty($collection)) {
            switch($return) {
                case RETURN_COLLECTION: return $collection;
                case RETURN_ARRAY: return $collection->toArray();
                case RETURN_FIRST: return $collection->shift();
                case RETURN_LAST: return $collection->pop();
                case RETURN_COUNT: return $collection->count();
                case RETURN_LIST: return $collection->lists( 'name' );
            }
        }
        return $collection;
    }

    function prepAssociation($with = null){
        if (!empty($this->associateWith) && !empty($with)) {
            if (!is_array($with)) $with = array($with);
            $with = array_merge($this->associateWith, $with);
        }
        return $this->associateWith($with);
    }

    public function getObject( $scope, $options = array() ){
        $id = isset($options['id']) ? $options['id'] : 'id';
        $with = isset($options['with']) ? $options['with'] : $this->associateWith;
        $sort = isset($options['sort']) ? $options['sort'] : false;
        $protected = isset($options['protected']) ? $options['protected'] : $this->protected;
        if (!is_array($scope)) {
            $scope = array( $id => $scope );
        }
        $model = $this;
        $conditions = array('where' => $scope, 'with' => $with, 'sort' => $sort);
        $builder = $model->prepQuery($conditions);
        $object = $builder->first();
        if (!empty($object)) {
            return $object->explode()->prepAssociation($with)->protect($protected);
        }
        return null;
    }

    //public function getObjectCollection( $where = null, $skip = 0, $take = 1000, $sort = null, $select = null )
    public function getObjectCollection( $conditions = array(), $return = null ){
        if (isset($conditions['return'])) $return = $conditions['return'];
        $with = isset($conditions['with']) ? $conditions['with'] : $this->associateWith;

        $collection = $this->prepCollection($conditions);

        if (!empty($collection)) {
            $collection->each(function($object) use ($with) {
                $object->explode()->prepAssociation($with)->protect();
            });
        }

        switch($return) {
            case RETURN_ARRAY:
                $list = [];
                foreach($collection as $model) $list[] = $model;
                return $list;
            case RETURN_FIRST: return $collection->shift();
            case RETURN_LAST: return $collection->pop();
            case RETURN_LIST: return $collection->lists('name', 'id');
            case RETURN_COUNT: return $this->countObjects($conditions);
            default: return $collection;
        }

    }

    public function countObjects($conditions = array()){
        $model = $this;
        $builder = $model->prepQuery($conditions);
        return $builder->count();
    }

    public function paginated($conditions = null){
        $model = $this;
        $paginate = array(
            'totalCount' => $model->count()
        );

        if (is_null($conditions)) $conditions = array();
        if (!is_array($conditions)) $conditions = array( 'page' => $conditions );
        if (!isset($conditions['limit'])) $conditions['limit'] = 10;

        $pageBlock = isset($config['pageBlock']) ? (int)$config['pageBlock'] : 8;
        $page = isset($conditions['page']) ? (int)$conditions['page'] : 1;
        $limit = isset($conditions['limit']) ? (int)$conditions['limit'] : 10;
        $with = isset($conditions['with']) ? $conditions['with'] : $this->associateWith;

        $builder = $model->prepQuery($conditions);

        $paginate['count'] = $builder->count();
        $paginate['limit'] = $limit;
        $paginate['page'] = $page;
        $paginate['pageCount'] = ($paginate['count'] > 0) ? ceil($paginate['count']/$limit) : 0;
        $paginate['block'] = Higg::paginationBlock($paginate['pageCount'], $paginate['page'], $pageBlock);
        $paginate['collection'] = array();
        $paginate['itemCount'] = 0;

        if ($paginate['count'] > 0) {
            $collection = $model->prepCollection($conditions);
            if (!empty($collection)) {

                $collection->each(function($object) use ($with) {
                    if (isset($object->typeof)) {
                        $object->setConfig();
                    }
                    $object->explode()->prepAssociation($with)->protect();
                });

                $paginate['itemCount'] = $collection->count();
                $paginate['collection'] = $collection;
            }
        }

        return $paginate;

    } // bleeding nation

    function setComposite($column, $value){
        if (is_array($value) && $this->isCompositeColumn($column)) {
            $params = (isset($this->{$column})) ? $this->{$column} : array();
            if ($params == 'Array') $params = [];
            if (!is_array($params)) $params = $this->explode_json($params, true);

            $encoding = $this->composite[$column];
            $merge = isset($encoding['merge']) ? (bool)$encoding['merge'] : false;

            if ($merge) {
                foreach($value as $key => $subVal) {
                    //if (empty($subVal) && $subVal != '0' && $subVal != "") continue; // remove key-less values
                    if (is_null($subVal)) continue;
                    /**
                     * @todo allow validation check for composite fields
                     */
                    $params[$key] = $subVal;
                }
            } else {
                $params = $value;
            }

            $this->{$column} = $params;
        }
        return $this;
    }
    function getComposite($column){
        return array_get($this->toArray(), $column);
    }
    function setParams( $key, $value, $params = 'params' ){
        $this->setComposite($params, array($key => $value));
        return $this;
    }

    function isSavable(){
        return true;
    }


} // end of model class
