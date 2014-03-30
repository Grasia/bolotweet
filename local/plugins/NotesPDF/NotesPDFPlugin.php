<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('STATUSNET')) {
    exit(1);
}

class NotesPDFPlugin extends Plugin {

    function onInitializePlugin() {
        // A chance to initialize a plugin in a complete environment
    }

    function onCleanupPlugin() {
        // A chance to cleanup a plugin at the end of a program
    }

    function onAutoload($cls) {

        $dir = dirname(__FILE__);

        switch ($cls) {

            case 'NotesgroupsAction':
            case 'NotescustomizeAction':
            case 'NotesgenerateAction':
                include_once $dir . '/actions/' . $cls . '.php';
                return false;
            case 'Notesgroupsform':
            case 'Notescustomizeform':
                include_once $dir . '/lib/' . $cls . '.php';
                return false;
            case 'NotesPDF':
            case 'GenerarPDF':
                include_once $dir . '/classes/' . $cls . '.php';
                return false;
                break;

            default:
                return true;
        }
    }

    function onRouterInitialized($m) {
        $m->connect('main/notespdf', array('action' => 'notesgroups'));
        $m->connect('main/notespdf/customize', array('action' => 'notescustomize'));
        $m->connect('main/notespdf/generate', array('action' => 'notesgenerate'));
        return true;
    }

    function onStartToolsLocalNav($action) {
        // '''common_local_url()''' gets the correct URL for the action name we provide
        
        $actionName = $action->trimmed('action');
        
        $user = common_current_user();
        if (!empty($user)) {
            $action->menuItem(common_local_url('notesgroups'), _m('Apuntes'), _m('Apuntes en PDF'), ($actionName === 'notesgroups' || $actionName === 'notescustomize'), 'nav_notespdf');
        }

        return true;
    }

    function onEndShowStyles($action) {
        $action->cssLink($this->path('css/notespdf.css'));
        return true;
    }

    function onPluginVersion(&$versions) {
        $versions[] = array('name' => 'NotesPDF',
            'version' => STATUSNET_VERSION,
            'author' => 'Alvaro Ortego Marcos',
            'rawdescription' =>
            _m('A plugin to export notes in PDF'));
        return true;
    }

    /* function onEndShowScripts($action)
      {
      $action->script($this->path('js/notespdf.js'));
      return true;
      } */
}
