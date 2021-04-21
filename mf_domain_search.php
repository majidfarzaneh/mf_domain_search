<?php
/**
 * WHMCS SDK Sample Addon Module
 *
 * An addon module allows you to add additional functionality to WHMCS. It
 * can provide both client and admin facing user interfaces, as well as
 * utilise hook functionality within WHMCS.
 *
 * This sample file demonstrates how an addon module for WHMCS should be
 * structured and exercises all supported functionality.
 *
 * Addon Modules are stored in the /modules/addons/ directory. The module
 * name you choose must be unique, and should be all lowercase, containing
 * only letters & numbers, always starting with a letter.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "addonmodule" and therefore all functions
 * begin "addonmodule_".
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/addon-modules/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

/**
 * Require any libraries needed for the module to function.
 * require_once __DIR__ . '/path/to/library/loader.php';
 *
 * Also, perform any initialization required by the service's library.
 */

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\AddonModule\Admin\AdminDispatcher;
use WHMCS\Module\Addon\AddonModule\Client\ClientDispatcher;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define addon module configuration parameters.
 *
 * Includes a number of required system fields including name, description,
 * author, language and version.
 *
 * Also allows you to define any configuration parameters that should be
 * presented to the user when activating and configuring the module. These
 * values are then made available in all module function calls.
 *
 * Examples of each and their possible configuration parameters are provided in
 * the fields parameter below.
 *
 * @return array
 */
function mf_domain_search_config()
{
    return [
        // Display name for your module
        'name' => 'Addon Module For Domain Search Api',
        // Description displayed within the admin interface
        'description' => 'This module provides Api service for another application' .
        '<br> Contact me for support and develop: +989307346262 | <a href="http://mrfarzaneh.ir" target="_blank">http://mrfarzaneh.ir</a>',
        // Module author name
        'author' => 'Majid Farzaneh <a href="mailto:mfarzaneh2013@gmail.com">mfarzaneh2013@gmail.com</a>',
        // Default language
        'language' => 'english',
        // Version number
        'version' => '1.0',
    ];
}

/**
 * Client Area Output.
 *
 * Called when the addon module is accessed via the client area.
 * Should return an array of output parameters.
 *
 * This function is optional.
 *
 * @see AddonModule\Client\Controller::index()
 *
 * @return array
 */
function mf_domain_search_clientarea($vars)
{
    if (isset($_POST['action']) && $_POST['action'] === 'search-domain'){

        $domain = $_POST['domain'];

        $postData = array(
            'domain' => "$domain",
        );

        $domainResult = localAPI('DomainWhois', $postData);
        if($domainResult['result'] === 'success') {

            $tld = explode('.', $domain);
            array_shift($tld);
            $tld = implode('.', $tld);
            $results = localAPI('GetTLDPricing', []);
            if ($results['result'] === 'success'){
                $registerPricing = $results['pricing']["$tld"]['register'];
                $transferPricing = $results['pricing']["$tld"]['transfer'];
            }

            $return = [
                'action' => true,
                'status' => ($domainResult['status'] === 'available') ? true : false,
                'pricing' => [
                    'register' => $registerPricing['1'],
                    'transfer' => $transferPricing['1'],
                ]
            ];

            print json_encode($return);
            die;
        }
    }
}
