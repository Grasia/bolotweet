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

$shortoptions = 'n';
$longoptions = array('noticeid=');

$helptext = <<<END_OF_REGISTERUSER_HELP
grade.php [options]
read grades associated to a notice

  -n --noticeid id of the notice to grade

END_OF_REGISTERUSER_HELP;

require_once INSTALLDIR . '/scripts/commandline.inc';

require_once INSTALLDIR . '/local/plugins/Grades/classes/Grades.php';

$noticeid = get_option_value('n', 'noticeid');

if (empty($noticeid)) {
    print "Must provide a  notice id.\n";
    exit(1);
}


try {

    $notice = Notice::staticGet('notice', $noticeid);

    if (empty($notice)) {
        throw new Exception("A notice with id '$noticeid' must exists.");
    }



    print 'Current grade is :' . Grades::getNoticeGrade($noticeid) . ' ';
} catch (Exception $e) {
    print $e->getMessage() . "\n";
    print $e->getTraceAsString();
    exit(1);
}
