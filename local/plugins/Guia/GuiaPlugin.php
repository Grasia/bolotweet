<?php

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/util.php';

class GuiaPlugin extends Plugin {

    
    function onInitializePlugin() {
        // A chance to initialize a plugin in a complete environment
    }

    function onCleanupPlugin() {
        // A chance to cleanup a plugin at the end of a program
    }

    function onRouterInitialized($m) {
        $m->connect('main/guia', array('action' => 'guiamostrar'));
        $m->connect('main/guia-descargar', array('action' => 'guiadescargar'));
        return true;
    }

    function onPluginVersion(&$versions) {
        $versions[] = array('name' => 'Grade',
            'version' => STATUSNET_VERSION,
            'author' => 'Alvaro Ortego Marcos',
            'rawdescription' =>
            _m('A plugin to show a manual.'));
        return true;
    }

    function onAutoload($cls) {

        $dir = dirname(__FILE__);

        switch ($cls) {

            case 'GuiamostrarAction':
                include_once $dir . '/actions/' . $cls . '.php';
                return false;
            case 'GuiadescargarAction':
                include_once $dir . '/actions/' . $cls . '.php';
                return false;
            case 'GuiaForm':
                include_once $dir . '/lib/' . $cls . '.php';
                return false;
            default:
                return true;
        }
    }

    function onStartPrimaryNav($action) {
        // '''common_local_url()''' gets the correct URL for the action name we provide
        $user = common_current_user();
        if (!empty($user)) {
            $action->menuItem(common_local_url('guiamostrar'), _m('Manual de Uso'), _m('Guía de uso BoloTweet'), false, 'nav_guia');
        }

        return true;
    }
 
    function onEndShowStyles($action) {
        $action->cssLink($this->path('css/guia.css'));
        return true;
    }

}
