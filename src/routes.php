<?php


Route::group([
    'prefix' => config('saml2_settings.routesPrefix'),
    'middleware' => config('saml2_settings.routesMiddleware'),
], function () {

    Route::get('/logout', array(
        'as' => 'saml_logout',
        'uses' => 'Nwea\Saml2\Http\Controllers\Saml2Controller@logout',
    ));

    Route::get('/login', array(
        'as' => 'saml_login',
        'uses' => 'Nwea\Saml2\Http\Controllers\Saml2Controller@samlCapture',
    ));

    Route::get('/metadata', array(
        'as' => 'saml_metadata',
        'uses' => 'Nwea\Saml2\Http\Controllers\Saml2Controller@metadata',
    ));

    Route::post('/acs', array(
        'as' => 'saml_acs',
        'uses' => 'Nwea\Saml2\Http\Controllers\Saml2Controller@acs',
    ));

    Route::get('/sls', array(
        'as' => 'saml_sls',
        'uses' => 'Nwea\Saml2\Http\Controllers\Saml2Controller@sls',
    ));
});
