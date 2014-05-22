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

$shortoptions = 'g';
$longoptions = array('groupnick=');

$helptext = <<<END_OF_REGISTERUSER_HELP
grade.php [options]
read grades associated to a notice

  -n --noticeid id of the notice to grade

END_OF_REGISTERUSER_HELP;

require_once INSTALLDIR . '/scripts/commandline.inc';

require_once INSTALLDIR . '/local/plugins/Grades/classes/Grades.php';
require_once INSTALLDIR . '/classes/Local_group.php';

$groupnick = get_option_value('g', 'groupnick');
try {
    if (empty($groupnick)) {
        $groups = Grades::getGroupsWithGrades();
        foreach ($groups as $key => $storedgroupnick) {
            reportGroupGrades($storedgroupnick);
        }
    } else {
        reportGroupGrades($groupnick);
    }
} catch (Exception $e) {
    print $e->getMessage() . "\n";
    print $e->getTraceAsString();
    exit(1);
}

function reportGroupGrades($groupnick) {
    $groupgrades = Grades::getGradedNoticesAndUsersWithinGroup($groupnick);
    print PHP_EOL;
    print PHP_EOL;
    print sprintf(_m('Group %s report' . PHP_EOL), $groupnick);
    foreach ($groupgrades as $login => $sumnoticegrades) {
        print sprintf(_m('+ %s : %s' . PHP_EOL), $login, $sumnoticegrades);
    }
}
