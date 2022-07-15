<?php

//
// MODEL
//

define('NOTICE_EMERGENCY', 'emergency');
define('NOTICE_ALERT', 'alert');
define('NOTICE_CRITICAL', 'critical');
define('NOTICE_ERROR', 'error');
define('NOTICE_WARNING', 'warning');
define('NOTICE_NOTICE', 'notice');
define('NOTICE_INFO', 'info');
define('NOTICE_DEBUG', 'debug');
define('NOTICE_SUCCESS', 'success');
define('NOTICE_EXCEPTION', 'exception');

define('HANDLE_BEFORE_READ', 'beforeRead');
define('HANDLE_AFTER_READ', 'afterRead');
define('HANDLE_BEFORE_SAVE', 'beforeSave');
define('HANDLE_AFTER_SAVE', 'afterSave');
define('HANDLE_BEFORE_DELETE', 'beforeDelete');
define('HANDLE_AFTER_DELETE', 'afterDelete');

define('HANDLE_BEFORE_STORE', 'beforeStore');
define('HANDLE_AFTER_STORE', 'afterStore');
define('HANDLE_BEFORE_FORM', 'beforeForm');
define('HANDLE_AFTER_FORM', 'afterForm');

// callbacks handled on key scope
define('HANDLE_KEY_READ', 'read');
define('HANDLE_KEY_EDIT', 'edit');
define('HANDLE_KEY_DELETE', 'delete');
define('HANDLE_KEY_GUARD', 'guard');
define('HANDLE_KEY_VALIDATE', 'validate');
define('HANDLE_KEY_CHANGED', 'changed');
define('HANDLE_KEY_DEFAULT_VALUE', 'default');
define('HANDLE_KEY_CAST', 'cast');
define('HANDLE_KEY_DEFAULT_HUMANIZE', 'humanize');
define('HANDLE_KEY_FORMAT', 'format');
define('HANDLE_KEY_SERIALIZE', 'serialize');
define('HANDLE_KEY_DESERIALIZE', 'deserialize');
define('HANDLE_KEY_MAP', 'map');
define('HANDLE_KEY_HELP', 'help');
define('HANDLE_KEY_PLACEHOLDER', 'placeholder');
define('HANDLE_KEY_OPTIONS', 'options');
define('HANDLE_KEY_TO_STRING', 'toString');


// field cast types
define('CAST_INTEGER', 'integer');
define('CAST_REAL', 'real');
define('CAST_FLOAT', 'float');
define('CAST_DOUBLE', 'double');
define('CAST_CHAR', 'char');
define('CAST_STRING', 'string');
define('CAST_BOOLEAN', 'boolean');
define('CAST_OBJECT', 'object');
define('CAST_ARRAY', 'array');
define('CAST_JSON', 'json');
define('CAST_LIST', 'list');
define('CAST_COLLECTION', 'collection');
define('CAST_DATE', 'date');
define('CAST_TIME', 'time');
define('CAST_DATETIME', 'datetime');
define('CAST_TIMESTAMP', 'timestamp');
define('CAST_POINT', 'point');
define('CAST_PASSWORD', 'password');
define('CAST_ENCRYPT', 'encrypt');
define('CAST_EMAIL', 'email');

define('ACCESS_PRIVILEGE_MANAGE', 'manage');
define('ACCESS_PRIVILEGE_BROWSE', 'browse');
define('ACCESS_PRIVILEGE_READ', 'read');
define('ACCESS_PRIVILEGE_EDIT', 'edit');
define('ACCESS_PRIVILEGE_DELETE', 'delete');
define('ACCESS_PRIVILEGE_TOUCH', 'trash');

define('EDITOR_TEXT', 'text');
define('EDITOR_TEXTAREA', 'textarea');
define('EDITOR_RICH', 'rich');
define('EDITOR_DROPDOWN', 'dropdown');
define('EDITOR_DATEPICKER', 'datepicker');
define('EDITOR_UPLOAD', 'upload');

define('HTTP_OK', '200');
define('HTTP_ACCEPTED', '202');
define('HTTP_NO_CONTENT', '204');
define('HTTP_MOVED', '301');
define('HTTP_NOT_MODIFIED', '304');
define('HTTP_UNAUTHORIZED', '401');
define('HTTP_NOT_FOUND', '404');
define('HTTP_FORBIDDEN', '403');
define('HTTP_NOT_ACCEPTABLE', '406');
define('HTTP_INTERNAL_ERROR', '500');
define('HTTP_SERVICE_UNAVAILABLE', '503');

define('SECOND_ONE_MIN', '60');
define('SECOND_ONE_HOUR', '3500');
define('SECOND_ONE_DAY', '86400');
define('SECOND_ONE_WEEK', '604800');
define('SECOND_ONE_MONTH', '2592000');
