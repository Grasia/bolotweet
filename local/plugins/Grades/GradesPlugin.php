<?php

/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
 */
if (!defined('STATUSNET')) {
    exit(1);
}

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
        // A chance to cleanup a plugin at the end of a program.
    }

    function onRouterInitialized($m) {
        $m->connect('main/grade', array('action' => 'grade'));
        $m->connect('main/gradereport', array('action' => 'gradereport'));
        $m->connect('main/exportCSV/generate', array('action' => 'gradeexportcsv'));
        $m->connect('main/exportCSV/options', array('action' => 'gradeoptionscsv'));
        $m->connect('main/gradereport/:nickgroup/:nickname', array('action' => 'gradeshowuser'), array('nickgroup' => Nickname::DISPLAY_FMT, 'nickname' => Nickname::DISPLAY_FMT)
        );
        $m->connect('group/:nickname/makegrader', array('action' => 'makegrader'), array('nickname' => Nickname::DISPLAY_FMT));
        return true;
    }

    function onPluginVersion(&$versions) {
        $versions[] = array('name' => 'Grade',
            'version' => STATUSNET_VERSION,
            'author' => 'Alvaro Ortego', 'Jorge Gomez',
            'rawdescription' =>
            _m('A plugin to grade notices'));
        return true;
    }

    function onAutoload($cls) {

        $dir = dirname(__FILE__);

        switch ($cls) {

            case 'GradeAction':
            case 'GradereportAction':
            case 'GradeexportcsvAction':
            case 'GradeoptionscsvAction':
            case 'GradeshowuserAction':
            case 'MakegraderAction':
                include_once $dir . '/actions/' . $cls . '.php';
                return false;
            case 'GradeForm':
            case 'GradecsvForm':
            case 'MakeGraderForm':
                include_once $dir . '/lib/' . $cls . '.php';
                return false;
            case 'Grades':
            case 'Gradesgroup':
                include_once $dir . '/classes/' . $cls . '.php';
                return false;
                break;
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

    function onStartAsideGroupProfile($out, $group) {

        $user = common_current_user();
        if (!empty($user)) {

            $out->elementStart('div', array("id" => "entity_graders", "class" => "section"));
            $out->elementStart('h2');

            $out->text('Profesores');

            $out->text(' ');

            $graders = Gradesgroup::getGraders($group->id);

            $out->text($graders->N);

            $out->elementEnd('h2');

            $out->elementStart('div', array('id' => 'div-members-scroll'));

            $gmml = new GroupMembersMiniList($graders, $out);
            $cnt = $gmml->show();
            if ($cnt == 0) {
                // TRANS: Description for mini list of group members on a group page when the group has no members.
                $out->element('p', null, _('(Ninguno)'));
            }

            $out->elementEnd('div');

            $out->elementEnd('div');
        }

        return true;
    }

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

                            $item->out->elementStart('a', array('href' => 'javascript:editarNota(' . $noticeid . ');', 'class' => 'notice-modify-grade', 'id' => 'button-modify-grade-' . $noticeid, 'title' => 'Modificar Puntuación'));
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

            $gradesAndGraders = Grades::getNoticeGradesAndGraders($noticeid);

            $gradeResult = Grades::devolverGrade($gradesAndGraders);

            if (is_array($gradeResult)) {

                $gradeValue = reset($gradeResult);
                $grader = key($gradeResult);

                // Si hay más de una puntuación para el tweet, añadimos JavaScript.
                if ($grader == "Nota") {

                    $args->out->elementStart('div', array('class' => 'div-with-grades-hidden'));
                    foreach ($gradesAndGraders as $profesor => $nota) {

                        $args->out->elementStart('p');
                        $args->out->raw($profesor . ': ' . $nota);
                        $args->out->elementEnd('p');
                    }

                    $args->out->elementEnd('div');

                    $args->out->elementStart('div', array('class' => 'notice-current-grade', 'onclick' => 'mostrarPuntuacion(' . $noticeid . ');'));
                } else {
                    $args->out->elementStart('div', array('class' => 'notice-current-grade'));
                }

                $args->out->elementStart('p', array('class' => 'notice-current-grade-value', 'title' => $grader));

                if (get_class($args) === 'ThreadedNoticeListSubItem') {
                    $args->out->raw($gradeValue);
                } else {
                    $args->out->raw($grader . '<br/>' . $gradeValue);
                }
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

    function onEndGroupSave($group) {
        $user = common_current_user();
        if (!empty($user)) {
            if ($user->hasRole('grader')) {

                Gradesgroup::vincularGrupo($user->id, $group->id);
            }
        }
    }

    function onEndProfileListItemActionElements($item) {

        if ($item->action->args['action'] === 'groupmembers') {

            $user = common_current_user();

            if ($user->hasRole('grader') && $user->isAdmin($item->group)) {

                if ($user->id != $item->profile->id && !Gradesgroup::isGrader($item->profile->id, $item->group->id)) {

                    $args = array('action' => 'groupmembers',
                        'nickname' => $item->group->nickname);
                    $page = $item->out->arg('page');
                    if ($page) {
                        $args['param-page'] = $page;
                    }

                    $item->out->elementStart('li', 'entity_make_grader');
                    $mgf = new MakeGraderForm($item->out, $item->profile, $item->group, $args);
                    $mgf->show();
                    $item->out->elementEnd('li');
                }
            }
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
