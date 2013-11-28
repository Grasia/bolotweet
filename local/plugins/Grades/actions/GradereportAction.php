<?php

/**
 * Give a warm greeting to our friendly user
 *
 * PHP version 5
 *
 * @category Sample
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 *
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2009, StatusNet, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
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
class GradereportAction extends Action {

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
     * Handle request
     *
     * This is the main method for handling a request. Note that
     * most preparation should be done in the prepare() method;
     * by the time handle() is called the action should be
     * more or less ready to go.
     *
     * @param array $args $_REQUEST args; handled in prepare()
     *
     * @return void
     */
    function handle($args) {
        parent::handle($args);

        $this->showPage();
    }

    /**
     * Title of this page
     *
     * Override this method to show a custom title.
     *
     * @return string Title of the page
     */
    function title() {
        return _m('Grade Reports');
    }

    /**
     * Show content in the content area
     *
     * The default StatusNet page has a lot of decorations: menus,
     * logos, tabs, all that jazz. This method is used to show
     * content in the content area of the page; it's the main
     * thing you want to overload.
     *
     * This method also demonstrates use of a plural localized string.
     *
     * @return void
     */
    function showContent() {
        if (empty($this->user)) {
            $this->element('p', array('class' => 'grade-report-error'), _m('Login first!'));
        } else {
            $groupswithgrades = Grades::getGroupsWithGrades();
            $this->elementStart('ul', array('class' => 'grade-report-group'));
            foreach ($groupswithgrades as $groupid) {
                $this->elementStart('li', array('class' => 'grade-report-group-item'));
                $this->element('a', array('class' => 'grade-report-group-link', 'href' =>
                    '#' . $groupid), sprintf(_m('%s'), $groupid));
                $this->elementEnd('li');
            }
            $this->elementEnd('ul');
            $this->element('h2', null, sprintf(_m('Group notices'), $groupid));
            foreach ($groupswithgrades as $groupid) {
                $gradespergroup = Grades::getGradedNoticesAndUsersWithinGroup($groupid);
                $this->elementStart('p', array('class' => 'grade-report-group'));
                $this->element('a', array('class' => 'grade-report-group-link', 'href' =>
                    common_root_url() . 'group/' . $groupid), sprintf(_m('Group  %s'), $groupid));
                $this->elementEnd('p');
                $this->elementStart('ul', array('class' => 'grade-report-groupmembers'));
                foreach ($gradespergroup as $login => $sumnoticegrades) {

                    $this->elementStart('li', array('class' => 'grade-report-groupmembers-item'));
                    $this->element('a', array('class' => 'grade-report-group-link', 'name' =>
                        $groupid));
                    $this->element('a', array('class' => 'grade-report-group-link', 'href' =>
                        common_root_url() . $login), sprintf(_m('%s, %s'), $login, $sumnoticegrades));
                    $this->elementEnd('li');
                }
                $this->elementEnd('ul');
            }
        }
    }

    /**
     * Return true if read only.
     *
     * Some actions only read from the database; others read and write.
     * The simple database load-balancer built into StatusNet will
     * direct read-only actions to database mirrors (if they are configured),
     * and read-write actions to the master database.
     *
     * This defaults to false to avoid data integrity issues, but you
     * should make sure to overload it for performance gains.
     *
     * @param array $args other arguments, if RO/RW status depends on them.
     *
     * @return boolean is read only action?
     */
    function isReadOnly($args) {
        return false;
    }

}
