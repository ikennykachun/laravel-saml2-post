<?php


Route::group([
    'prefix' => config('saml2_settings.routesPrefix'),
    'middleware' => config('saml2_settings.routesMiddleware'),
], function () {

    Route::get('/logout', array(
        'as' => 'saml_logout',
        'https' => 'true',
        'uses' => 'SamlPost\Saml2\Http\Controllers\Saml2Controller@logout',
    ));

    Route::get('/login', array(
        'as' => 'login',
        'https' => 'true',
        'uses' => 'SamlPost\Saml2\Http\Controllers\Saml2Controller@login',
    ));

    Route::get('/sso', array(
        'as' => 'saml_sso_form',
        'https' => 'true',
        'uses' => 'SamlPost\Saml2\Http\Controllers\Saml2Controller@samlCapture',
    ));

    Route::get('/metadata', array(
        'as' => 'saml_metadata',
        'https' => 'true',
        'uses' => 'SamlPost\Saml2\Http\Controllers\Saml2Controller@metadata',
    ));

    Route::post('/acs', array(
        'as' => 'saml_acs',
        'https' => 'true',
        'uses' => 'SamlPost\Saml2\Http\Controllers\Saml2Controller@acs',
    ));

    Route::get('/acs', array(
        'as' => 'saml_acs',
        'https' => 'true',
        'uses' => 'SamlPost\Saml2\Http\Controllers\Saml2Controller@acs',
    ));

    Route::get('/sls', array(
        'as' => 'saml_sls',
        'https' => 'true',
        'uses' => 'SamlPost\Saml2\Http\Controllers\Saml2Controller@sls',
    ));
});
