<?php

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR . '/local/plugins/Grades/lib/gradeform.php';
require_once INSTALLDIR . '/local/plugins/Grades/classes/Grades.php';
require_once INSTALLDIR . '/lib/util.php';
require_once INSTALLDIR . '/local/plugins/Grades/js/grades.js';

class GradesPlugin extends Plugin {

    public $showMarkers;

    /**
     * constructor
     */
    function __construct($show = TRUE) {
        $this->showMarkers = $show;
        parent::__construct();
    }

    function onCheckSchema() {
        $schema = Schema::get();

        $schema->ensureTable('grades', Grades::schemaDef());

        return true;
    }

    function onInitializePlugin() {
        // A chance to initialize a plugin in a complete environment
    }

    function onCleanupPlugin() {
        // A chance to cleanup a plugin at the end of a program
    }

    function onRouterInitialized($m) {
        $m->connect('main/grade', array('action' => 'grade'));
        $m->connect('main/gradereport', array('action' => 'gradereport'));
        return true;
    }

    function onPluginVersion(&$versions) {
        $versions[] = array('name' => 'Grade',
            'version' => STATUSNET_VERSION,
            'author' => 'Jorge J. Gomez-Sanz',
            'homepage' => 'http://grasia.fdi.ucm.es/jorge',
            'rawdescription' =>
            _m('A plugin to grade notices'));
        return true;
    }

    function onAutoload($cls) {

        $dir = dirname(__FILE__);

        switch ($cls) {

            case 'GradeAction':
                include_once $dir . '/actions/' . $cls . '.php';
                return false;
            case 'GradereportAction':
                include_once $dir . '/actions/' . $cls . '.php';
                return false;
            default:
                return true;
        }
    }

    function showNumbers($args, $value) {
        $user = common_current_user();
        if (!empty($user)) {
            if ($user->hasRole('grader')) {

                $favor = new GradeForm($args->out, $args->notice, $value);
                $favor->show();
            }
        }
    }

    function onStartPrimaryNav($action) {
        // '''common_local_url()''' gets the correct URL for the action name we provide
        $user = common_current_user();
        if (!empty($user)) {
            $action->menuItem(common_local_url('gradereport'), _m('Grade Reports'), _m('Reports on user behaviors '), false, 'nav_gradereport');
        }

        return true;
    }

    
    function onStartShowNoticeItem($args) {
        
        
        $noticeid = $args->notice->id;
        $gradevalue = Grades::getNoticeGrade($noticeid);
        $userid = Grades::getNoticeGradeUserId($noticeid);
        if ($gradevalue != '?') {
            $args->out->elementStart('div', array('class' => 'notice-current-grade'));
            $args->out->elementStart('p', array('class' => 'notice-current-grade-value'));
            $args->out->raw($userid . '<br>' . $gradevalue);
            $args->out->elementEnd('p');
            $args->out->elementEnd('div');
        } else {
            $args->out->elementStart('div', array('class' => 'notice-current-grade'));
            $args->out->elementStart('p', array('class' => 'notice-current-grade-empty'));
            $args->out->elementEnd('p');
            $args->out->elementEnd('div');
        }
        return true;
    }

    function onEndShowNoticeItem($args) {
        // $hook = substr(__FUNCTION__, 2);
        $args->out->elementStart('div', array('class' => 'notice-grades'));
        $this->showNumbers($args, 0);
        $this->showNumbers($args, 1);
        $this->showNumbers($args, 2);
        $this->showNumbers($args, 3);
        $args->out->elementEnd('div');


        return true;
    }

    function onEndShowStyles($action) {
        $action->cssLink($this->path('css/grades.css'));
        return true;
    
    }
    
      function onEndShowScripts($action)
      {
      $action->script($this->path('js/grades.js'));
      return true;
      }

     
}
