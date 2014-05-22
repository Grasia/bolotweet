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
class GradeexportcsvAction extends Action {

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

        if (!common_logged_in()) {
            $this->clientError(_('Not logged in.'));
            return;
        }

        if (!$this->user->hasRole('grader')) {
            $this->clientError(_('Usted no tiene privilegios para visitar esta página.'));
            return;
        }

        $groupid = $this->trimmed('groupid');
        $delimiter = $this->trimmed('grade-export-delimiter');
        $separator = $this->trimmed('grade-export-separator');

        $arrayReport = Grades::getGradedNoticesAndUsersWithinGroup($groupid);
        $nicksMembers = Grades::getMembersNicksExcludeGradersAndAdmin($groupid);

        foreach ($nicksMembers as $nick) {

            if (!array_key_exists($nick, $arrayReport)) {
                $arrayReport[$nick] = 0;
            }
        }

        $arrayFinal = array();

        foreach ($arrayReport as $alumno => $puntuacion) {
            $arrayFinal[] = array($alumno, number_format($puntuacion, 2));
        }

        $this->generarInformeCSV($arrayFinal, 'report_group_' . $groupid . '.csv', $separator, $delimiter);
    }

    function generarInformeCSV($array, $filename = "report.csv", $separator = ',', $delimiter = '"') {

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachement; filename="' . $filename . '";');

        if ($separator == "") {
            $separator = ',';
        }

        if ($delimiter == "") {
            $delimiter = '"';
        }


        $f = fopen('php://output', 'w');

        foreach ($array as $line) {
            fputcsv($f, $line, $separator, $delimiter);
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
