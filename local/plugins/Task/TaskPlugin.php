<?php

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/util.php';

class TaskPlugin extends Plugin {

    function onCheckSchema() {
        $schema = Schema::get();

        $schema->ensureTable('task', Task::schemaDef());
        $schema->ensureTable('task_grader', Task_Grader::schemaDef());

        return true;
    }

    function onInitializePlugin() {
        // A chance to initialize a plugin in a complete environment
    }

    function onCleanupPlugin() {
        // A chance to cleanup a plugin at the end of a program.
    }

    function onRouterInitialized($m) {
        $m->connect('main/tareas', array('action' => 'taskcreate'));
        $m->connect('main/tareas/', array('action' => 'newnoticetask'));
        return true;
    }

    function onEndToolsLocalNav($action) {

        $actionName = $action->trimmed('action');

        $user = common_current_user();
        if (!empty($user)) {

            if ($actionName === 'taskcreate')
                $action->elementStart('li', array('id' => 'nav_task', 'class' => 'current'));
            else
                $action->elementStart('li', array('id' => 'nav_task'));

            $action->elementStart('a', array('href' => common_local_url('taskcreate'), 'title' => _m('Tareas de Clase')));
            $action->text('Tareas');

            // Si es alumno, mostramos las tareas pendientes.
            if (!$user->hasRole('grader')) {

                // Llamamos a la función que obtenga el numero de tareas
                $number = Task::getNumberPendingTasks($user->id);
                
                if($number != 0)
                    $action->element('span', 'pending-tasks-number', $number);
            }

            $action->elementEnd('a');
            $action->elementEnd('li');
        }

        return true;
    }

    function onPluginVersion(&$versions) {
        $versions[] = array('name' => 'Task',
            'version' => STATUSNET_VERSION,
            'author' => 'Alvaro Ortego Marcos',
            'rawdescription' =>
            _m('A plugin to generate homeworks.'));
        return true;
    }

    function onAutoload($cls) {

        $dir = dirname(__FILE__);

        switch ($cls) {

            case 'TaskcreateAction':
            case 'NewnoticetaskAction':
                include_once $dir . '/actions/' . $cls . '.php';
                return false;
            case 'InitForm':
            case 'NoticeTaskForm':
                include_once $dir . '/lib/' . $cls . '.php';
                return false;
            case 'Task':
            case 'Task_Grader':
                include_once $dir . '/classes/' . $cls . '.php';
                return false;
                break;
            default:
                return true;
        }
    }

    function onEndShowStyles($action) {
        $action->cssLink($this->path('css/task.css'));
        return true;
    }

     function onEndShowScripts($action) {
      $action->script($this->path('js/task.js'));
      return true;
      } 
}
