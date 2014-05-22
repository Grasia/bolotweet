<?php

/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
 */
if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

class Gradesgroup extends Managed_DataObject {

    /**
     * Notice to favor
     */
    public $__table = 'grades_group';
    public $groupid = null; // group id
    public $userid = null; // user id

    function staticGet($k, $v = null) {
        return Memcached_DataObject::staticGet('Gradesgroup', $k, $v);
    }

    function pkeyGet($kv) {
        return Memcached_DataObject::pkeyGet('Gradesgroup', $kv);
    }

    /**
     * Data definition for email reminders
     */
    public static function schemaDef() {
        return array(
            'description' => 'Context Graders',
            'fields' => array(
                'userid' => array(
                    'type' => 'int',
                    'not null' => true,
                    'description' => 'ID of user'
                ),
                'groupid' => array(
                    'type' => 'int',
                    'not null' => true,
                    'description' => 'Group ID'
                ),
            ),
            'primary key' => array('userid', 'groupid'),
        );
    }

    static function vincularGrupo($userid, $groupid) {

        // MAGICALLY put fields into current scope

        $grGroup = new Gradesgroup();

        $grGroup->userid = $userid;
        $grGroup->groupid = $groupid;

        $result = $grGroup->insert();

        if (!$result) {
            common_log_db_error($userid, 'INSERT', __FILE__);
            return false;
        }

        return $grGroup;
    }

    static function desvincularGrupo($userid, $groupid) {


        $grGroup = new Gradesgroup();

        if (common_config('db', 'quote_identifiers'))
            $user_table = '"grades_group"';
        else
            $user_table = 'grades_group';

        $qry = 'DELETE FROM ' . $user_table .
                ' WHERE userid=' . $userid .
                ' AND groupid=' . $groupid;

        $grGroup->query($qry);

        $grGroup->free();
    }

    static function getGraders($groupid) {

        $qry = 'SELECT profile.* ' .
                'FROM profile JOIN grades_group ' .
                'ON profile.id = grades_group.userid ' .
                'WHERE grades_group.groupid = ' . $groupid;

        $graders = new Profile();

        $graders->query($qry);

        return $graders;
    }

    static function getGroups($graderid) {

        $qry = 'SELECT gg.groupid'
                . ' FROM grades_group gg'
                . ' where gg.userid=' . $graderid;

        $grGroup = new Gradesgroup();

        $grGroup->query($qry);

        $groups = array();

        while ($grGroup->fetch()) {
            $groups[] = $grGroup->groupid;
        }

        $grGroup->free();

        return $groups;
    }

    static function isGrader($userid, $groupid) {

        $qry = 'SELECT gg.userid'
                . ' FROM grades_group gg'
                . ' where gg.userid=' . $userid
                . ' and gg.groupid =' . $groupid;

        $grGroup = new Gradesgroup();

        $grGroup->query($qry);


        if ($grGroup->fetch()) {
            $result = true;
        } else {
            $result = false;
        }

        $grGroup->free();
        return $result;
    }

}
