<?php
namespace SamlPost\Saml2;

use Illuminate\Support\ServiceProvider;
// use OneLogin_Saml2_Auth;
use URL;

class Saml2ServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if(config('saml2_settings.useRoutes', false) == true ){
            include __DIR__ . '/../../routes.php';
        }

        $this->publishes([
            __DIR__.'/../../config/saml2_settings.php' => config_path('saml2_settings.php'),
        ]);

        if (config('saml2_settings.proxyVars', false)) {
            \OneLogin_Saml2_Utils::setProxyVars(true);
        }

        // Set SAML POST URI config
        config([
            'saml2_settings.idp.singleSignOnService.url' => route('saml_sso_form'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerOneLoginInContainer();

        $this->app->singleton('SamlPost\Saml2\Saml2Auth', function ($app) {

            return new \SamlPost\Saml2\Saml2Auth($app['\OneLogin\Saml2\Auth']);
        });

    }

    protected function registerOneLoginInContainer()
    {
        $this->app->singleton('\OneLogin\Saml2\Auth', function ($app) {
            $config = config('saml2_settings');
            if (empty($config['sp']['entityId'])) {
                $config['sp']['entityId'] = URL::route('saml_metadata');
            }
            if (empty($config['sp']['assertionConsumerService']['url'])) {
                $config['sp']['assertionConsumerService']['url'] = URL::route('saml_acs');
            }
            if (!empty($config['sp']['singleLogoutService']) &&
                empty($config['sp']['singleLogoutService']['url'])) {
                $config['sp']['singleLogoutService']['url'] = URL::route('saml_sls');
            }

            return new \OneLogin\Saml2\Auth($config);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
