<?php

/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
 */
define('STATUSNET', true);
define('LACONICA', true); // compatibility
define('INSTALLDIR', realpath(dirname(__FILE__) . '/../../../..'));

require_once INSTALLDIR . '/lib/common.php';
require_once INSTALLDIR . '/local/plugins/Task/classes/Task_Grader.php';


$graderid = $_POST['graderid'];
$groupid = $_POST['groupid'];


$historical = Task_Grader::getHistorical($graderid, $groupid);


foreach ($historical as $taskHistory) {

    echo '<div id="task-' . $taskHistory['id'] . '" class="div-historical-task">';

    if ($taskHistory['status'] == 1) {
        $status = 'Iniciada';
    } else {
        $status = 'Cancelada';
    }

    if ($taskHistory['tag'] == "") {

        $taskHistory['tag'] = '&lt;Ninguno&gt;';
    }

    echo '<p><span class="historical-bold">' . $status . '</span> '
    . '| <span class="historical-bold">Fecha:</span> ' . $taskHistory['cdate'] . ' '
    . '| <span class="historical-bold">Tag:</span>  ' . $taskHistory['tag'] . ' '
    . '| <span class="historical-bold">Completada:</span>  '
    . $taskHistory['completed'] . '/' . $taskHistory['total'] . '</p>';

    if ($taskHistory['completed'] == 0) {

        if ($taskHistory['status'] == 1) {
            echo '<form action="' . common_local_url('taskcreate') . '" method="POST" class="ajax">';
            echo '<input type="hidden" value="' . $taskHistory[id] . '" name="cancel-task"></input>';
            echo '<input type="submit" value="Cancelar" class="cancel-task-button title="Cancela esta tarea" onclick="updateTaskStatus(' . $taskHistory[id] . ');"></input>';
            echo '</form>';
        } else {

            echo '<form action="' . common_local_url('taskcreate') . '" method="POST" class="ajax">';
            echo '<input type="hidden" value="' . $taskHistory[id] . '" name="reopen-task"></input>';
            echo '<input type="submit" value="Iniciar" class="reopen-task-button" title="Reabre esta tarea" onclick="updateTaskStatus(' . $taskHistory[id] . ');"></input>';
            echo '</form>';
        }
    }
    echo '</div>';
}