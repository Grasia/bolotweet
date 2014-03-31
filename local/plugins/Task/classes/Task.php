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

}
