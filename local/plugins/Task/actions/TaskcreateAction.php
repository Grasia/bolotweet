<?php

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR . '/local/plugins/Grades/classes/Gradesgroup.php';

class TaskcreateAction extends Action {

    var $user = null;

    /**
     * Take arguments for running
     *
     * This method is called first, and it lets the action class get
     * all its arguments and validate them. It's also the time
     * to fetch any relevant data from the database.
     *
     * Action classes should run parent::prepare($args) as the first
     * line of this method to make sure the default argument-processing
     * happens.
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     */
    function prepare($args) {
        parent::prepare($args);

        $this->user = common_current_user();

        return true;
    }

    /**
     * Class handler.
     *
     * @param array $args query arguments
     *
     * @return void
     */
    function handle($args) {

        parent::handle($args);
        if (!common_logged_in()) {
            $this->clientError(_('Not logged in.'));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if ($this->trimmed('groupid')) {
                $groupid = $this->trimmed('groupid');
                $tag = $this->trimmed('task-tag-' . $groupid);
                $status = $this->trimmed('status');
                $taskid = $this->trimmed('taskid');
                if (substr_compare($tag, "#", 0, 1) == 0) {
                    $tag = substr($tag, 1);
                }

                $this->initTask($groupid, $tag, $status, $taskid);
            } else if ($this->trimmed('delete')) {
                $taskid = $this->trimmed('delete');
                $this->deleteTask($taskid);
            } else if ($this->trimmed('cancel-task')) {
                $taskid = $this->trimmed('cancel-task');
                $this->cancelTaskGrader($taskid);
            } else if ($this->trimmed('reopen-task')) {
                $taskid = $this->trimmed('reopen-task');
                $this->reopenTask($taskid);
            }
        } else {

            $this->showPage();
        }
    }

    /**
     * Cancela una tarea iniciada por un profesor, siempre y cuando
     * la tarea no tenga aún ningún alumno que la haya completado.
     */
    function cancelTaskGrader($taskid) {

        // Cancelamos la tarea del grader.
        Task_Grader::cancel($taskid);

        // Cancelamos cada tarea asociada a los alumnos de esa tarea.
        Task::cancelTask($taskid);

        if ($this->boolean('ajax')) {

            $this->startHTML('application/xml,text/xml;charset=utf-8');
            $this->elementStart('head');
            $this->element('title', null, _('Disfavor favorite'));
            $this->elementEnd('head');
            $this->elementStart('body');
            $form = new ReopenForm($this, $taskid);
            $form->show();
            $this->elementEnd('body');
            $this->elementEnd('html');
        }
    }

    function reopenTask($taskid) {

        // Reabrimos la tarea del grader.
        Task_Grader::reopenTask($taskid);

        // Reabrimos cada tarea asociada a los alumnos de esa tarea.
        Task::reopenTask($taskid);

        if ($this->boolean('ajax')) {

            $this->startHTML('application/xml,text/xml;charset=utf-8');
            $this->elementStart('head');
            $this->element('title', null, _('Disfavor favorite'));
            $this->elementEnd('head');
            $this->elementStart('body');
            $form = new CancelForm($this, $taskid);
            $form->show();
            $this->elementEnd('body');
            $this->elementEnd('html');
        }
    }

    function deleteTask($taskid) {

        Task::rejectTask($this->user->id, $taskid);


        if ($this->boolean('ajax')) {

            $this->startHTML('application/xml,text/xml;charset=utf-8');
            $this->elementStart('head');
            $this->element('title', null, _('Disfavor favorite'));
            $this->elementEnd('head');
            $this->elementStart('body');
            $this->elementStart('form', array('action' => common_local_url('taskcreate'), 'method' => 'POST', 'class' => 'ajax'));
            $this->hidden('hidden-' . $task->id, $task->id, 'delete');
            $this->element('input', array('type' => 'submit', 'value' => 'Sí', 'class' => 'button-option-dialog', 'onclick' => 'deleteTask(' . $task->id . ');'));
            $this->element('input', array('type' => 'button', 'value' => 'No', 'onclick' => 'hideConfirmDialog(' . $task->id . ');', 'class' => 'button-option-dialog'));
            $this->elementEnd('form');
            $this->elementEnd('body');
            $this->elementEnd('html');
        }
    }

    function showContent() {

        if ($this->user->hasRole('grader')) {
            $this->createTask();
        } else {
            $this->showTasks();
        }
    }

    function createTask() {

        $groupsIds = Gradesgroup::getGroups($this->user->id);

        $groups = User_group::multiGet('id', $groupsIds)->fetchAll();

        if (empty($groups)) {
            $this->element('p', 'error', 'No tiene ningún grupo asociado.');
        } else {

            foreach ($groups as $group) {

                $this->elementStart('div', array('id' => 'div-group-task-' . $group->id, 'class' => 'group-task-grader'));
                $this->element('h2', null, strtoupper($group->getBestName()));

                // Comprobamos si la tarea de ese día para ese grupo ya está creada.
                $result = Task_Grader::checkTask($this->user->id, $group->id);

                // Creamos el form para ese grupo.
                $form = new InitForm($this, $group->id, $result);
                $form->show();

                $this->element('p', null, 'Fecha: ' . strftime('%d-%m-%Y'));

                // Cogemos el histórico de tareas de ese grupo y ese profesor.
                $historical = Task_Grader::getHistorical($this->user->id, $group->id);

                $this->elementStart('div', array('class' => 'wrapper-group-historical'));
                // Creamos el título del histórico y un enlace para expandirlo.
                $this->elementStart('a', array('class' => 'task-show-historical',
                    'onclick' => 'showHistorical(' . $group->id . ');'));
                $this->raw('Histórico de tareas <span class="show-historical">▸</span>');
                $this->elementEnd('a');

                $this->elementStart('div', array('id' => 'historical-' . $group->id, 'class' => 'div-group-historical'));


                if (count($historical) > 0) {

                    foreach ($historical as $taskHistory) {

                        $this->elementStart('div', array('id' => 'task-' . $taskHistory['id'], 'class' => 'div-historical-task'));

                        if ($taskHistory['status'] == 1) {
                            $status = 'Iniciada';
                        } else {
                            $status = 'Cancelada';
                        }

                        if ($taskHistory['tag'] == "") {

                            $taskHistory['tag'] = '&lt;Ninguno&gt;';
                        }

                        $this->elementStart('p');
                        $this->raw('<span class="historical-bold">' . $status . '</span> '
                                . '| <span class="historical-bold">Fecha:</span> ' . $taskHistory['cdate'] . ' '
                                . '| <span class="historical-bold">Tag:</span>  ' . $taskHistory['tag'] . ' '
                                . '| <span class="historical-bold">Completada:</span>  ' . $taskHistory['completed'] . '/' . $taskHistory['total']);

                        $this->elementEnd('p');
                        if ($taskHistory['completed'] == 0) {

                            if ($taskHistory['status'] == 1) {
                                $form = new CancelForm($this, $taskHistory['id']);
                                $form->show();
                            } else {

                                $form = new ReopenForm($this, $taskHistory['id']);
                                $form->show();
                            }
                        }
                        $this->elementEnd('div');
                    }
                } else {

                    $this->element('p', null, 'Todavía no ha iniciado ninguna tarea.');
                }

                $this->elementEnd('div');

                $this->elementEnd('div');

                $this->element('p', 'task-underline');

                $this->elementEnd('div');
            }
        }
    }

    function showTasks() {

        $taskIds = Task::getPendingTasks($this->user->id);

        if (count($taskIds) == 0) {
            $this->element('p', 'no-pending-task-msg', 'Enhorabuena, no tienes ninguna tarea pendiente.');
        } else {

            $tasks = Task_Grader::multiGet('id', $taskIds)->fetchAll();

            foreach ($tasks as $task) {

                $group = User_group::staticGet('id', $task->groupid);

                $this->elementStart('div', array('class' => 'div-group-task', 'id' => 'div-task-' . $task->id));
                $this->element('h2', null, strtoupper('Tarea de ' . $group->nickname));

                $this->elementStart('div', array('id' => 'options-task-' . $task->id, 'class' => 'options-task'));
                $this->element('input', array('type' => 'button', 'class' => 'button-option-reject', 'value' => 'Rechazar', 'onclick' => 'showConfirmDialog(' . $task->id . ');'));
                $this->element('input', array('type' => 'button', 'class' => 'button-option-complete', 'value' => 'Completar', 'onclick' => 'mostrarBox(' . $task->id . ');'));
                $this->elementEnd('div');

                $this->element('p', null, 'Fecha: ' . $task->cdate);

                $this->elementStart('div', array('id' => 'confirm-reject-task-' . $task->id, 'class' => 'confirm-reject-task'));
                $this->element('a', array('class' => 'close-confirm-dialog', 'href' => 'javascript:hideConfirmDialog(' . $task->id . ');'), 'x');
                $this->element('p', null, '¿Está seguro que desea eliminar la tarea?');
                $this->elementStart('form', array('action' => common_local_url('taskcreate'), 'method' => 'POST', 'class' => 'ajax'));
                $this->hidden('hidden-' . $task->id, $task->id, 'delete');
                $this->element('input', array('type' => 'submit', 'value' => 'Sí', 'class' => 'button-option-dialog', 'onclick' => 'deleteTask(' . $task->id . ');'));
                $this->element('input', array('type' => 'button', 'value' => 'No', 'onclick' => 'hideConfirmDialog(' . $task->id . ');', 'class' => 'button-option-dialog'));
                $this->elementEnd('form');
                $this->elementEnd('div');

                $this->elementStart('div', array('class' => 'input_form'));

                if ($task->tag == "") {
                    $notice_form = new NoticeTaskForm($this, $task->id, array('content' => '#' . $task->cdate, 'to_group' => $group));
                } else {
                    $notice_form = new NoticeTaskForm($this, $task->id, array('content' => '#' . $task->cdate . ' #' . $task->tag, 'to_group' => $group));
                }
                $notice_form->show();

                $this->elementEnd('div');

                $this->element('p', 'task-underline');

                $this->elementEnd('div');
            }
        }
    }

    function initTask($groupid, $tag, $status, $taskid) {

        $result = array();

        if ($status == -1) {
            // Registramos la tarea en la tabla task_grader
            $id = Task_Grader::register(array('graderid' => $this->user->id, 'groupid' => $groupid, 'tag' => $tag));

            $idsUsers = Grades::getMembersExcludeGradersAndAdmin($groupid);

            // Creamos una tarea asociada para cada alumno del grupo.
            if (count($idsUsers) > 0) {
                Task::register(array('id' => $id, 'idsUsers' => $idsUsers));
            }

            $result[0] = 1;
            $result[1] = $id;
        } else {

            Task_Grader::updateTask($taskid, $tag);

            Task::reopenTask($taskid);

            $result[0] = 1;
            $result[1] = $taskid;
        }

        if ($this->boolean('ajax')) {
            $this->startHTML('text/xml;charset=utf-8');
            $this->elementStart('head');
            // TRANS: Title.
            $this->element('title', null, _m('Add to favorites'));
            $this->elementEnd('head');
            $this->elementStart('body');
            $form = new InitForm($this, $groupid, $result);
            $form->show();
            $this->elementEnd('body');
            $this->elementEnd('html');
        }
    }

    function title() {
        return _m('Tareas');
    }

}
