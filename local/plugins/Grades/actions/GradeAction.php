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
require_once INSTALLDIR . '/classes/User.php';
require_once INSTALLDIR . '/classes/Notice.php';

class GradeAction extends Action {

    var $user = null;

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
        $noticeid = $this->trimmed('notice');
        $notice = Notice::staticGet($noticeid);


        $token = $this->trimmed('token-' . $notice->id);
        if (!$token || $token != common_session_token()) {
            $this->clientError(_('There was a problem with your session token. Try again, please.'));
            return;
        }

        $gradevalue = $this->trimmed('value');
        $nickname = $user->nickname;

        $exist = Grades::getNoticeGrade($noticeid, $nickname);

        if ($exist != '?') {

            Grades::updateNotice(array('noticeid' => $noticeid,
                'grade' => $gradevalue, 'userid' => $nickname));
        } else {
            Grades::register(array('userid' => $nickname,
                'noticeid' => $noticeid,
                'grade' => $gradevalue));
        }


        if ($this->boolean('ajax')) {

            $this->startHTML('application/xml,text/xml;charset=utf-8');
            $this->elementStart('head');
            $this->element('title', null, _('Disfavor favorite'));
            $this->elementEnd('head');
            $this->elementStart('body');
            $this->element('p');
            $this->elementEnd('body');
            $this->elementEnd('html');
        }
    }

    /**
     * Notifies a user when their notice is favorited.
     *
     * @param class $notice favorited notice
     * @param class $user   user declaring a favorite
     *
     * @return void
     */
    /* function notify($notice, $user)
      {
      $other = User::staticGet('id', $notice->profile_id);
      if ($other && $other->id != $user->id) {
      if ($other->email && $other->emailnotifyfav) {
      mail_notify_fave($other, $user, $notice);
      }
      // XXX: notify by IM
      // XXX: notify by SMS
      }
      } */
}
