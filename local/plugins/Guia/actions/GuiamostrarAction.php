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

class GuiamostrarAction extends Action {

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
        return _m('Guía de Uso');
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


        $this->elementStart('p');
        $this->raw("En este manual podrás conocer las principales funcionalidades y mejoras de BoloTweet. "
                . "Aprenderás cómo manejar algunas de sus opciones, así como otros aspectos importantes de"
                . "su uso.<br/><br/>Esta guía estará constantemente actualizada a medida que se vayan añadiendo novedades "
                . "al sistema.");
        $this->elementEnd('p');
        $this->element('br');
        $this->element('br');
        $this->elementStart('p', array('class' => 'text-download-guide'));
        $this->raw('Descarga la Guía en Formato PDF &nbsp;&nbsp; [Versión v1.0]');
        $this->elementEnd('p');
        $downloadBut = new GuiaForm($this, "Manualv1.pdf");
        $downloadBut->show();
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
