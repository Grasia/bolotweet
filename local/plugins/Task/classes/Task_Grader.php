<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

require_once INSTALLDIR . '/classes/Managed_DataObject.php';

class Task_Grader extends Managed_DataObject {

    public $__table = 'task_grader';
    public $id = null; // id
    public $graderid = null; // graderID
    public $groupid = null; // groupID
    public $cdate = null;

    function staticGet($k, $v = null) {
        return Memcached_DataObject::staticGet('Task_Grader', $k, $v);
    }

    function pkeyGet($kv) {
        return Memcached_DataObject::pkeyGet('Task_Grader', $kv);
    }

    function multiGet($keyCol, $keyVals, $skipNulls = true) {
        return parent::multiGet('Task_Grader', $keyCol, $keyVals, $skipNulls);
    }

    public static function prueba() {

        return true;
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
                'graderid' => array(
                    'type' => 'int',
                    'not null' => true,
                    'description' => 'ID del grader'
                ),
                'groupid' => array(
                    'type' => 'int',
                    'not null' => true,
                    'description' => 'Group ID'
                ),
                'tag' => array(
                    'type' => 'varchar',
                    'length' => 50,
                    'not null' => true,
                    'description' => 'Task tag'
                ),
                'status' => array(
                    'type' => 'tinyint',
                    'not null' => true,
                    'description' => 'Status of Task'
                ),
                'cdate' => array(
                    'type' => 'date',
                    'not null' => true,
                    'description' => 'Date the task was created'
                ),
            ),
            'primary key' => array('id'),
        );
    }

    static function register($fields) {

        extract($fields);

        $taskG = new Task_Grader();

        if ($tag == "") {
            $qry = 'INSERT INTO task_grader (graderid,groupid,cdate,status) '
                    . 'VALUES (' . $graderid . ',' . $groupid . ',CURDATE(), 1)';
        } else {

            $qry = 'INSERT INTO task_grader (graderid,groupid,tag,cdate,status) '
                    . 'VALUES (' . $graderid . ',' . $groupid . ',"' . $tag . '",CURDATE(), 1)';
        }

        $qry2 = 'SELECT LAST_INSERT_ID() as id FROM task_grader';

        $taskG->query('BEGIN');
        $taskG->query($qry);
        $taskG->query($qry2);
        $taskG->query('COMMIT');

        if ($taskG->fetch()) {
            $id = $taskG->id;
        }

        return $id;
    }

    static function cancel($taskid) {


        $task = new Task_Grader();

        $qry = 'UPDATE task_grader ' .
                'SET status=0 ' .
                'WHERE id=' . $taskid;

        $task->query($qry);

        $task->free();
    }

    static function reopenTask($taskid) {


        $task = new Task_Grader();

        $qry = 'UPDATE task_grader ' .
                'SET status=1 ' .
                'WHERE id=' . $taskid;

        $task->query($qry);

        $task->free();
    }

    static function updateTask($taskid, $tag) {

        $task = new Task_Grader();

        if ($tag == "") {
            $qry = 'UPDATE task_grader ' .
                    'SET status=1 ' .
                    'WHERE id=' . $taskid;
        } else {
            $qry = 'UPDATE task_grader ' .
                    'SET status=1, tag="' . $tag . '" '.
                    'WHERE id=' . $taskid;
        }


        $task->query($qry);

        $task->free();
    }

    static function checkTask($graderid, $groupid) {


        $task = new Task_Grader();

        $qry = 'select tg.status as status, tg.id as id '
                . 'from task_grader tg '
                . 'where tg.graderid =  "' . $graderid . '"'
                . ' and tg.groupid = ' . $groupid
                . ' and tg.cdate = CURDATE()';


        $task->query($qry);

        if($task->fetch()){
            
            $result = array($task->status, $task->id);
        }
        
        else 
            $result = -1;
        
        return $result;
    }

    static function getHistorical($graderid, $groupid) {

        $task = new Task_Grader();

        $qry = 'select ' .
                '(select count(id) from task where id = tg.id and status = 1) as completed, ' .
                '(select count(id) as total from task where id = tg.id) as total, ' .
                'tg.cdate as cdate, ' .
                'tg.status as status, ' .
                'tg.id as id, ' .
                'tg.tag as tag ' .
                'from task_grader tg ' .
                'where tg.graderid = ' . $graderid .
                ' and tg.groupid = ' . $groupid .
                ' order by tg.cdate desc';

        $task->query($qry);

        $historical = array();

        while ($task->fetch()) {
            $historical[] = array('completed' => $task->completed, 'total' => $task->total,
                'cdate' => $task->cdate, 'status' => $task->status, 'id' => $task->id, 'tag' => $task->tag);
        }

        return $historical;
    }

}
