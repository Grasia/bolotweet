#!/usr/bin/env php
<?php
/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
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
