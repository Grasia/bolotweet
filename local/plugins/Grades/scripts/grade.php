#!/usr/bin/env php
<?php
/*
 * StatusNet - a distributed open-source microblogging tool
 * Copyright (C) 2008, 2009, StatusNet, Inc.
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

define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

$shortoptions = 'u:n:g:';
$longoptions = array('user=', 'noticeid=', 'grade=');

$helptext = <<<END_OF_REGISTERUSER_HELP
grade.php [options]
Create a grade in the name of a user for a notice

  -u --user nickname of the user who creates the notice
  -n --noticeid id of the notice to grade
  -g --grade grade assigned to the notice

END_OF_REGISTERUSER_HELP;

require_once INSTALLDIR . '/scripts/commandline.inc';

require_once INSTALLDIR . '/local/plugins/Grades/classes/Grades.php';

$nickname = get_option_value('u', 'user');
$noticeid = get_option_value('n', 'noticeid');
$gradeval = get_option_value('g', 'grade');

if (empty($nickname) || empty($noticeid) || empty($gradeval)) {
    print "Must provide a username, a notice id, and a grade.\n";
    exit(1);
}


try {

    $user = User::staticGet('nickname', $nickname);

    if (empty($user)) {
        throw new Exception("A user named '$nickname' must exists.");
    }


    $notice = Notice::staticGet('notice', $noticeid);

    if (empty($notice)) {
        throw new Exception("A notice with id '$noticeid' must exists.");
    }


    $grade = Grades::register(array('userid' => $nickname,
                'noticeid' => $noticeid,
                'grade' => $gradeval));
} catch (Exception $e) {
    print $e->getMessage() . "\n";
    print $e->getTraceAsString();
    exit(1);
}
