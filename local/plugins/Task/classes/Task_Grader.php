<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

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
    
        function multiGet($keyCol, $keyVals, $skipNulls=true)
    {
        return parent::multiGet('Task_Grader', $keyCol, $keyVals, $skipNulls);
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

        $qry = 'INSERT INTO task_grader (graderid,groupid,cdate) '
                . 'VALUES (' . $graderid . ',' . $groupid . ',CURDATE())';

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
    
    static function checkTask($graderid, $groupid){
        
        
        $task = new Task_Grader();

        $new = true;
        
        $qry = 'select tg.id as taskid '
                . 'from task_grader tg '
                . 'where tg.graderid =  "' . $graderid .'"'
                . ' and tg.groupid = ' . $groupid
                . ' and tg.cdate = CURDATE()';


        $result = $task->query($qry);

          if ($task->fetch()) {
            $new = false;
        }

      return $new;
        
    }

}
