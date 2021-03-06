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

/**
 * Give a warm greeting to our friendly user
 *
 * This sample action shows some basic ways of doing output in an action
 * class.
 *
 * Action classes have several output methods that they override from
 * the parent class.
 *
 * @category Sample
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 */
require_once INSTALLDIR . '/local/plugins/Grades/classes/Grades.php';
require_once INSTALLDIR . '/classes/User.php';
require_once INSTALLDIR . '/classes/Notice.php';

class NotesgenerateAction extends Action {

    var $user = null;
    var $msg;

    /**
     * Take arguments for running
     *
     * This method is called first, and it lets the action class get
     * all its arguments and validate them. It's also the time
     * to fetch any relevant data from the database.
     *
     * Action classes should run parent::prepare($args) as the first
     * line of this method to make sure the default argument-processing
     * happens.
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     */
    function prepare($args) {
        parent::prepare($args);

        $this->user = common_current_user();


        return true;
    }

    /**
     * Class handler.
     *
     * @param array $args query arguments
     *
     * @return void
     */
    function handle($args) {

        parent::handle($args);
        if (!common_logged_in()) {
            $this->clientError(_('Not logged in.'));
            return;
        }
        $user = common_current_user();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            common_redirect(common_local_url('all', array('nickname' => $user->nickname)));
            return;
        }

        $idGroup = $this->trimmed('idGroup');

        $token = $this->trimmed('token-' . $idGroup);
        if (!$token || $token != common_session_token()) {
            $this->clientError(_('There was a problem with your session token. Try again, please.'));
            return;
        }


        if ($this->trimmed('submit-auto') != null) {


            $groupids = NotesPDF::getNoticeIDsInAGroupModeAuto($idGroup);

            $notices = Notice::multiGet('id', $groupids)->fetchAll();

            GenerarPDF::content($idGroup, $notices, 'Automáticos');
        } else if (($this->trimmed('submit-custom') != null)) {


            $tag = $this->trimmed('combo-tag') == 'Todos' ? '%' : $this->trimmed('combo-tag');
            $nick = $this->trimmed('combo-user') == 'Todos' ? '%' : $this->trimmed('combo-user');
            $grade = $this->trimmed('combo-grade') == 'Todos' ? '%' : $this->trimmed('combo-grade');

            $noticeIds = NotesPDF::getNoticesInModeCustom(array('idGroup' => $idGroup, 'tag' => $tag, 'nick' => $nick, 'grade' => $grade));

            $notices = Notice::multiGet('id', $noticeIds)->fetchAll();

            GenerarPDF::content($idGroup, $notices, 'Personalizados');
        } else {

            $this->showForm('Error al generar los apuntes. Inténtelo de nuevo en unos minutos.');
        }
    }

    function showForm($msg = null) {
        $this->msg = $msg;
        $this->showPage();
    }

    function showContent() {

        $idGroup = $this->trimmed('idGroup');

        $group = NotesPDF::getGroupByID($idGroup);

        $this->element('h2', null, 'Apuntes para el grupo ' . $group->getBestName());

        $this->elementStart('p');
        $this->raw('A continuación personalice los apuntes.');
        $this->elementEnd('p');

        $optionsForm = new Notescustomizeform($this, $group->id);
        $optionsForm->show();
    }

    function showPageNotice() {
        if ($this->msg) {
            $this->element('p', 'error', $this->msg);
        }
    }

    function title() {
        return _m('Personalización de Apuntes');
    }

}
