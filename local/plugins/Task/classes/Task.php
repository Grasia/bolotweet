<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

class Task extends Managed_DataObject {

    public $__table = 'task';
    public $id = null; // id
    public $userid = null; // graderID
    public $status = null;

    function staticGet($k, $v = null) {
        return Memcached_DataObject::staticGet('Task', $k, $v);
    }

    function pkeyGet($kv) {
        return Memcached_DataObject::pkeyGet('Task', $kv);
    }

    /**
     * Data definition for email reminders
     */
    public static function schemaDef() {
        return array(
            'description' => 'Task Graders',
            'fields' => array(
                'id' => array(
                    'type' => 'serial',
                    'not null' => true,
                    'description' => 'Task ID'
                ),
                'userid' => array(
                    'type' => 'int',
                    'not null' => true,
                    'description' => 'ID del grader'
                ),
                'status' => array(
                    'type' => 'tinyint',
                    'not null' => true,
                    'description' => 'Status of Task'
                ),
            ),
            'primary key' => array('id', 'userid'),
        );
    }

    static function getPendingTasks($userid) {

        $task = new Task();

        $qry = 'select t.id as taskid '
                . 'from task t '
                . 'where t.userid = ' . $userid
                . ' and t.status = 0';

        $task->query($qry);

        $taskIds = array();

        while ($task->fetch()) {
            $taskIds[] = $task->taskid;
        }

        return $taskIds;
    }

    static function getNumberPendingTasks($userid) {

        $task = new Task();

        $qry = 'select count(t.id) as number '
                . 'from task t '
                . 'where t.userid = ' . $userid
                . ' and t.status = 0';

        $task->query($qry);
        if ($task->fetch()) {
            $number = $task->number;
        }

        return $number;
    }

    static function getNumberCompletedTasks($userid) {

        $task = new Task();

        $qry = 'select count(t.id) as number '
                . 'from task t '
                . 'where t.userid = ' . $userid
                . ' and t.status = 1';

        $task->query($qry);
        if ($task->fetch()) {
            $number = $task->number;
        }

        return $number;
    }

    static function getNumberRejectedTasks($userid) {

        $task = new Task();

        $qry = 'select count(t.id) as number '
                . 'from task t '
                . 'where t.userid = ' . $userid
                . ' and t.status = -1';

        $task->query($qry);
        if ($task->fetch()) {
            $number = $task->number;
        }

        return $number;
    }

    static function register($fields) {

        extract($fields);

        $task = new Task();

        $qry = 'INSERT INTO task (id,userid,status) values ';

        for ($i = 0; $i < count($idsUsers); $i++) {

            $qry .= '(' . $id . ',' . $idsUsers[$i] . ', 0)';

            if ($i < (count($idsUsers) - 1))
                $qry .= ', ';
        }

        $task->query($qry);
    }

    static function rejectTask($userid, $taskid) {

        $task = new Task();

        $qry = 'UPDATE task ' .
                'SET status=-1 ' .
                'WHERE userid=' . $userid .
                ' AND id=' . $taskid;

        $result = $task->query($qry);

        if (!$result) {
            common_log_db_error($user, 'UPDATE TASK', __FILE__);
        }

        $task->free();
        
    }
    
        static function completeTask($userid, $taskid) {

        $task = new Task();

        $qry = 'UPDATE task ' .
                'SET status=1 ' .
                'WHERE userid=' . $userid .
                ' AND id=' . $taskid;

        $result = $task->query($qry);

        if (!$result) {
            common_log_db_error($user, 'UPDATE TASK', __FILE__);
        }

        $task->free();
        
    }

}
