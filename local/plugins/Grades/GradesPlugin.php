<?php

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR . '/local/plugins/Grades/lib/gradeform.php';
require_once INSTALLDIR . '/local/plugins/Grades/classes/Grades.php';
require_once INSTALLDIR . '/local/plugins/Grades/classes/Gradesgroup.php';
require_once INSTALLDIR . '/lib/util.php';

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
        $schema->ensureTable('grades_group', Gradesgroup::schemaDef());

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

        $grade = new GradeForm($args->out, $args->notice, $value);
        $grade->show();
    }

    function onStartPrimaryNav($action) {
        // '''common_local_url()''' gets the correct URL for the action name we provide
        $user = common_current_user();
        if (!empty($user)) {
            $action->menuItem(common_local_url('gradereport'), _m('Grade Reports'), _m('Reports on user behaviors '), false, 'nav_gradereport');
        }

        return true;
    }

    /* function onEndProfileListItemActionElements($item){

      $item->action->elementStart('li', array('class' => 'entity_block'));
      $item->action->raw('explota');
      $item->action->elementEnd('li');
      } */

    function onStartShowNoticeOptionItems($item) {
        $user = common_current_user();
        if (!empty($user)) {

            if ($user->hasRole('grader')) {

                // Si la noticia NO es de un profesor, entonces se puede puntuar.
                if (!$item->notice->getProfile()->getUser()->hasRole('grader')) {

                    $noticeid = $item->notice->id;
                    $nickname = $user->nickname;
                    $userid = $user->id;

                    // Si puede puntuar (porque es grader en el grupo del tweet)
                    if (Grades::getValidGrader($noticeid, $userid)) {

                        $gradevalue = Grades::getNoticeGrade($noticeid, $nickname);

                        if ($gradevalue != '?') {

                            $item->out->elementStart('a', array('href' => 'javascript:editarNota(' . $noticeid . ');', 'class' => 'notice-modify-grade', 'id' => 'button-modify-grade-' . $noticeid));
                            $item->out->raw('Modificar Nota');
                            $item->out->elementEnd('a');
                        }
                    }
                }
            }
        }

        return true;
    }

    function onStartShowNoticeItem($args) {

        // Si la noticia es de un profesor, mostramos el birrete.
        if ($args->notice->getProfile()->getUser()->hasRole('grader')) {

            $path = $this->path('css/birrete-small.png');
            $args->out->element('img', array('id' => 'birrete-grades', 'alt' => 'Profesor', 'src' => $path));
        } else {
            $noticeid = $args->notice->id;
            $gradeResult = Grades::devolverGrade($noticeid);

            if (is_array($gradeResult)) {

                $gradeValue = reset($gradeResult);
                $grader = key($gradeResult);

                $args->out->elementStart('div', array('class' => 'notice-current-grade'));
                $args->out->elementStart('p', array('class' => 'notice-current-grade-value'));
                $args->out->raw($grader . '<br/>' . $gradeValue);
                $args->out->elementEnd('p');
                $args->out->elementEnd('div');
            }
        }

        return true;
    }

    function onEndShowNoticeItem($args) {

        $user = common_current_user();
        if (!empty($user)) {
            if ($user->hasRole('grader')) {

                // Si la noticia NO es de un profesor, entonces se puede puntuar.
                if (!$args->notice->getProfile()->getUser()->hasRole('grader')) {

                    $noticeid = $args->notice->id;
                    $nickname = $user->nickname;
                    $userid = $user->id;

                    // Si puede puntuar (porque es grader en el grupo del tweet)
                    if (Grades::getValidGrader($noticeid, $userid)) {

                        $gradevalue = Grades::getNoticeGrade($noticeid, $nickname);

                        if ($gradevalue == '?')
                            $args->out->elementStart('div', array('class' => 'notice-grades'));


                        else if ($gradevalue != '?')
                            $args->out->elementStart('div', array('id' => 'div-grades-hidden-' . $noticeid, 'class' => 'notice-grades-hidden'));

                        $this->showNumbers($args, 0);
                        $this->showNumbers($args, 1);
                        $this->showNumbers($args, 2);
                        $this->showNumbers($args, 3);
                        $args->out->elementEnd('div');
                    }
                }
            }
        }

        return true;
    }

    function onStartShowAccountProfileBlock($out, $profile) {

        if ($profile->getUser()->hasRole('grader')) {

            // Ponemos la imagen del birrete
            $path = $this->path('css/birrete-small.png');
            $out->element('img', array('id' => 'birrete-profile', 'alt' => 'Profesor', 'src' => $path));

            // La etiqueta de profesor
            $out->elementStart('p', array('id' => 'label-profesor'));
            $out->raw(_m('PROFESOR'));
            $out->elementEnd('p');
        }

        return true;
    }

    function onEndShowStyles($action) {
        $action->cssLink($this->path('css/grades.css'));
        return true;
    }

     function onEndShowScripts($action) {
      $action->script($this->path('js/grades.js'));
      return true;
      } 
}
