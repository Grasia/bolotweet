<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('STATUSNET')) {
    exit(1);
}

class LogoPlugin extends Plugin {

    function onInitializePlugin() {
        // A chance to initialize a plugin in a complete environment
    }

    function onCleanupPlugin() {
        // A chance to cleanup a plugin at the end of a program
    }

    function onStartLoginGroupNav($action) {

        // Creamos la URL del logo.

        $path = $this->path('css/logo.png');


        // Agregamos el logo al Nav.
        $action->elementStart('li', array('id' => 'liLogoPlugin'));
        $action->element('img', array('id' => 'logoNav', 'class' => 'logo photo', 'alt' => 'Bolotweet 2.0', 'src' => $path));
        $action->elementEnd('li');

        return true;
    }

    function onStartDefaultLocalNav($action) {

        // Creamos las URL's de destino, y del logo.
        if (common_logged_in()) {
            $cur = common_current_user();
            $url = common_local_url('all', array('nickname' => $cur->nickname));
        } else {
            $url = common_local_url('public');
        }

        $path = $this->path('css/logo.png');


        // Agregamos el logo al Nav.

        $action->out->elementStart('li', array('id' => 'liLogoPlugin'));
        $action->out->elementStart('address', array('class' => 'vcard'));
        $action->out->elementStart('a', array('class' => 'url home bookmark', 'href' => $url));
        $action->out->element('img', array('id' => 'logoNav', 'class' => 'logo photo', 'alt' => 'Bolotweet 2.0', 'src' => $path));
        $action->out->elementEnd('a');
        $action->out->elementEnd('address');
        $action->out->elementEnd('li');

        return true;
    }

    function onEndShowStyles($action) {
        $action->cssLink($this->path('css/logo.css'));
        return true;
    }

    function onPluginVersion(&$versions) {
        $versions[] = array('name' => 'LogoPlugin',
            'version' => STATUSNET_VERSION,
            'author' => 'Alvaro Ortego Marcos',
            'rawdescription' =>
            _m('A plugin to put a Logo in Default Nav'));
        return true;
    }

}
