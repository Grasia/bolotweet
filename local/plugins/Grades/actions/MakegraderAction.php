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

/**
 * Make another user an grader of a group
 *
 */
class MakegraderAction extends RedirectingAction {

    var $profile = null;
    var $group = null;

    function prepare($args) {
        parent::prepare($args);
        if (!common_logged_in()) {
            $this->clientError(_('Not logged in.'));
            return false;
        }
        $id = $this->trimmed('profileid');
        if (empty($id)) {
            $this->clientError(_('No profile specified.'));
            return false;
        }
        $this->profile = Profile::staticGet('id', $id);
        if (empty($this->profile)) {
            $this->clientError(_('No profile with that ID.'));
            return false;
        }
        $group_id = $this->trimmed('groupid');
        if (empty($group_id)) {
            $this->clientError(_('No group specified.'));
            return false;
        }
        $this->group = User_group::staticGet('id', $group_id);
        if (empty($this->group)) {
            $this->clientError(_('No such group.'));
            return false;
        }
        $user = common_current_user();
        if (!$user->isAdmin($this->group) &&
                !$user->hasRole('grader')) {
            $this->clientError(_('Only an admin and grader can make another user a grader.'), 401);
            return false;
        }
        if ($this->profile->hasRole('grader')) {
            $this->clientError(sprintf(_('%1$s is already a grader for group "%2$s".'), $this->profile->getBestName(), $this->group->getBestName()), 401);
            return false;
        }
        return true;
    }

    /**
     * Handle request
     *
     * @param array $args $_REQUEST args; handled in prepare()
     *
     * @return void
     */
    function handle($args) {
        parent::handle($args);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->makeGrader();
        }
    }

    /**
     * Make user an admin
     *
     * @return void
     */
    function makeGrader() {

        if (!$this->profile->hasRole('grader')) {
            $this->profile->grantRole('grader');
        }

        if (!$this->profile->hasRole('deleter')) {
            $this->profile->grantRole('deleter');
        }

        $result = Gradesgroup::vincularGrupo($this->profile->id, $this->group->id);

        if (!$result) {

            $this->clientError(sprintf(_('Ha habido un error al vincular a %1$s con el grupo "%2$s".'), $this->profile->getBestName(), $this->group->getBestName()), 401);
        }

        $this->returnToPrevious();
    }

    /**
     * If we reached this form without returnto arguments, default to
     * the top of the group's member list.
     * 
     * @return string URL
     */
    function defaultReturnTo() {
        return common_local_url('groupmembers', array('nickname' => $this->group->nickname));
    }

}
