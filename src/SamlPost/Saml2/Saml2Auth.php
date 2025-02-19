<?php

namespace SamlPost\Saml2;

use SamlPost\Saml2\Events\Saml2LogoutEvent;

use Log;
use Psr\Log\InvalidArgumentException;

class Saml2Auth
{

    /**
     * @var \\OneLogin\Saml2\Auth
     */
    protected $auth;

    protected $samlAssertion;

    function __construct(\OneLogin\Saml2\Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @return bool if a valid user was fetched from the saml assertion this request.
     */
    function isAuthenticated()
    {
        $auth = $this->auth;

        return $auth->isAuthenticated();
    }

    /**
     * The user info from the assertion
     * @return Saml2User
     */
    function getSaml2User()
    {
        return new Saml2User($this->auth);
    }

    /**
     * The ID of the last message processed
     * @return String
     */
    function getLastMessageId()
    {
        return $this->auth->getLastMessageId();
    }

    /**
     * Initiate a saml2 login flow. It will redirect! Before calling this, check if user is
     * authenticated (here in saml2). That would be true when the assertion was received this request.
     */
    function login($returnTo = null, $parameters = array(), $forceAuthn = false, $isPassive = false, $stay = false, $setNameIdPolicy = true)
    {
        $auth = $this->auth;

        $auth->login($returnTo, $parameters, $forceAuthn, $isPassive, $stay, $setNameIdPolicy);
    }

    /**
     * Initiate a saml2 logout flow. It will close session on all other SSO services. You should close
     * local session if applicable.
     */
    function logout($returnTo = null, $nameId = null, $sessionIndex = null, $nameIdFormat = null)
    {
        $auth = $this->auth;

        $auth->logout($returnTo, [], $nameId, $sessionIndex, false, $nameIdFormat);
    }

    /**
     * Process a Saml response (assertion consumer service)
     * When errors are encountered, it returns an array with proper description
     */
    function acs()
    {

        /** @var $auth OneLogin_Saml2_Auth */
        $auth = $this->auth;

        $auth->processResponse();

        $errors = $auth->getErrors();

        if (!empty($errors)) {
            return $errors;
        }

        if (!$auth->isAuthenticated()) {
            return array('error' => 'Could not authenticate');
        }

        return null;

    }

    /**
     * Process a Saml response (assertion consumer service)
     * returns an array with errors if it can not logout
     */
    function sls($retrieveParametersFromServer = false)
    {
        $auth = $this->auth;

        // destroy the local session by firing the Logout event
        $keep_local_session = false;
        $session_callback = function () {
            event(new Saml2LogoutEvent());
        };

        $auth->processSLO($keep_local_session, null, $retrieveParametersFromServer, $session_callback);

        $errors = $auth->getErrors();

        return $errors;
    }

    /**
     * Show metadata about the local sp. Use this to configure your saml2 IDP
     * @return mixed xml string representing metadata
     * @throws \InvalidArgumentException if metadata is not correctly set
     */
    function getMetadata()
    {
        $auth = $this->auth;
        $settings = $auth->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);

        if (empty($errors)) {

            return $metadata;
        } else {

            throw new InvalidArgumentException(
                'Invalid SP metadata: ' . implode(', ', $errors),
                \OneLogin\Saml2\Error::METADATA_SP_INVALID
            );
        }
    }

    /**
     * Get the last error reason from \OneLogin_Saml2_Auth, useful for error debugging.
     * @see \OneLogin_Saml2_Auth::getLastErrorReason()
     * @return string
     */
    function getLastErrorReason() {
        return $this->auth->getLastErrorReason();
    }
}
