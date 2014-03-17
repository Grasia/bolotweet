<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class GuiadescargarAction extends Action {

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

        $file = basename($this->trimmed('filename'));

        $url = common_path() . 'local/plugins/Guia/files/';
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/bolotweet/local/plugins/Guia/files/';

        $pathURL = $url . $file;
        $pathDIR = $dir . $file;


        if (is_file($pathDIR)) {

            // Definir headers
            header("Content-type: application/pdf");
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . $file . "\"");
            readfile($pathURL);
        } else {
            die("El archivo no existe.");
        }
    }

    /**
     * Title of this page
     *
     * Override this method to show a custom title.
     *
     * @return string Title of the page
     */
    function title() {
        return _m('Descargar Manual de BoloTweet');
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
        return true;
    }

}
