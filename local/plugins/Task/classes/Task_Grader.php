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

}
