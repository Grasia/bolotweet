<?php

/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
 */
if (!defined('STATUSNET')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

/**
 * Secure Registration
 *
 * @category  Secure registration
 * @package   StatusNet
 * @author    Alvaro Ortego
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */
class SecureRegistrationPlugin extends Plugin {

    function onAutoload($cls) {
        $dir = dirname(__FILE__);

        switch ($cls) {
            case 'SecureregisterAction':
                include_once $dir . '/actions/' . $cls . '.php';
                return false;

            default:
                return true;
        }
    }

    function onArgsInitialize(&$args) {
        if (array_key_exists('action', $args) && $args['action'] == 'register') {
            $args['action'] = 'secureregister';
        }
        return true;
    }

    function onLoginAction($action, &$login) {
        if ($action == 'secureregister') {
            $login = true;
            return false;
        }
        return true;
    }

    function onEndShowStyles($action) {
        $action->cssLink($this->path('css/secureregistration.css'));
        return true;
    }

    function onPluginVersion(&$versions) {
        $versions[] = array('name' => 'SecureRegistration',
            'version' => STATUSNET_VERSION,
            'author' => 'Alvaro Ortego',
            'rawdescription' =>
            _m('Custom Registration with Captcha.'));
        return true;
    }

}
