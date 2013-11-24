<?php
/**
 * StatusNet, the distributed open-source microblogging tool
 *
 * Form for favoring a notice
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Form
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @author    Sarven Capadisli <csarven@status.net>
 * @copyright 2009 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}


require_once INSTALLDIR.'/classes/Memcached_DataObject.php';


/**
 * Form for favoring a notice
 *
 * @category Form
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @author   Sarven Capadisli <csarven@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link     http://status.net/
 *
 * @see      DisfavorForm
 */

class Grades extends Memcached_DataObject
{
    
    /**
     * Notice to favor
     */
     
    public $__table = 'grades'; 
    public $id;
    public $noticeid = null; // graded notice

    public $userid=null; // user who created the grade
    
    public $cdate=null; // date where the grade was created
    
    public $grade=0; // default grade
    
    
     /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Grades',$k,$v); }
    
          function pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Grades', $kv);
    }
    
    /**
     * Data definition for email reminders
     */
    public static function schemaDef() {
        return array(
        'description' => 'Grade notices',
         'fields' => array(
            'noticeid' => array(
                'type' => 'int',
                'not null' => true,
                 'description' => 'ID of the notice'
            ),
            'userid' => array(
            'type' => 'varchar',
            'not null' => true,
            'length' => 255,
            'description' => 'ID del usuario'
            ),
            'grade' => array(
            'type' => 'int',
            'not null' => true,
            'description' => 'Puntuation given'
            ),
            'id' => array(
            'type' => 'int',
            'not null' => true,
            'description' => 'Puntuation ID'
            ),
            'cdate' => array(
            'type' => 'timestamp',
            'not null' => true,
            'description' => 'Date and time the puntuation was sent'
            ),
        ),
        'primary key' => array('id'),
        );
    }
    
static function getGroupsWithGrades(){
  $grade= new Grades();
          if(common_config('db','quote_identifiers'))
          $user_table = '"grades"';
        else $user_table = 'grades';

        $qry =
        'SELECT notice_tag.tag as userid' .
        ' FROM grades, notice_tag, local_group WHERE ' .
        ' notice_tag.notice_id = grades.noticeid and ' .
        ' notice_tag.tag = local_group.nickname ' .
        ' group by notice_tag.tag'; 
      
        
        $grade->query($qry); // all select fields will
        // be written to fields of the Grade object. It is required that
        // select fields are named after the Grade fields.

        $foundgroups = array();

        while ($grade->fetch()) {
          $foundgroups[] = $grade->userid;
        }
        
        $grade->free();
        return $foundgroups;
  
}

static function getGradedNoticesAndUsersWithinGroup($groupid){
    $grade= new Grades();
          if(common_config('db','quote_identifiers'))
          $user_table = '"grades"';
        else $user_table = 'grades';

        $qry =
        'SELECT profile.nickname as userid, sum(grades.grade) as grade' .
        ' FROM (select * from ' .
        '(SELECT grades.noticeid, grades.grade,cdate FROM grades   order by cdate DESC) as grades group by grades.noticeid) as grades, notice, profile, notice_tag WHERE ' .
        ' notice_tag.tag = "%s" and ' .
        ' notice_tag.notice_id = grades.noticeid and ' .
        ' notice_tag.notice_id = notice.id and ' .
        ' profile.id = notice.profile_id  ' .
        ' group by notice.profile_id'; 
        
        
        $grade->query(sprintf($qry, $groupid)); // all select fields will
        // be written to fields of the Grade object. It is required that
        // select fields are named after the Grade fields.

        $obtainedgrade = array();

        while ($grade->fetch()) {
          $obtainedgrade[$grade->userid] = $grade->grade;
        }
        
        $grade->free();
        return $obtainedgrade;
}



 static   function getNoticeGrade($noticeid){
          
          $grade= new Grades();
          if(common_config('db','quote_identifiers'))
          $user_table = '"grades"';
        else $user_table = 'grades';

        $qry =
          'SELECT grade ' .
          'FROM '. $user_table . ' ' .
          'WHERE grades.noticeid = %d order by grades.cdate DESC';

       // print sprintf($qry, $noticeid);
        
        $grade->query(sprintf($qry, $noticeid));

        $obtainedgrade = null;

        if ($grade->fetch()) {
          $obtainedgrade = $grade->grade;
        } 
        else
          $obtainedgrade =  '?';

//print $obtainedgrades->length();
        $grade->free();
        return $obtainedgrade;
 }
 
  static   function getNoticeGradeUserId($noticeid){
          
          $grade= new Grades();
          if(common_config('db','quote_identifiers'))
          $user_table = '"grades"';
        else $user_table = 'grades';

        $qry =
          'SELECT grade, userid ' .
          'FROM '. $user_table . ' ' .
          'WHERE grades.noticeid = %d order by grades.cdate DESC';

       // print sprintf($qry, $noticeid);
        
        $grade->query(sprintf($qry, $noticeid));

        $obtainedgrade = null;

        if ($grade->fetch()) {
          $obtainedgrade = $grade->userid;
        } 
        else
          $obtainedgrade =  '?';

//print $obtainedgrades->length();
        $grade->free();
        return $obtainedgrade;
 }

    static function register($fields) {

        // MAGICALLY put fields into current scope

        extract($fields);

     
        $ngrade = new Grades();

        $ngrade->userid = $userid;

        $ngrade->cdate = common_sql_now();
        $ngrade->grade = $grade;
        $ngrade->noticeid = $noticeid;


        $ngrade->query('BEGIN');
        
        $result = $ngrade->insert();
        
        
            if (!$result) {
                common_log_db_error($user, 'INSERT', __FILE__);
                return false;
            }
        

            $ngrade->query('COMMIT');
        

        return $ngrade;
    }

    
    
}
