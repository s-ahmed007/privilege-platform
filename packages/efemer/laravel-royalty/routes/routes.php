<?php

Route::get('/ping', '\Efemer\Royalty\Controllers\WebController@ping');

// cms/api
Route::group(
    [
        'middleware' => [ 'api' ],
        'prefix' => '/api2',
        'namespace' => '\Efemer\Royalty\Controllers'
    ],
    function(){

        Route::match( ['GET','POST'],    '/handle/{method}/{operation?}', 'ApiController@handleMethod');
        Route::match( ['GET','POST'],    '/{endpoint}/{action?}/{condition?}', 'ApiController@apiHandle' );

    }
);
