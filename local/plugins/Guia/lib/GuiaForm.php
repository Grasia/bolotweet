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

require_once INSTALLDIR . '/lib/form.php';

/**
 * Form for favoring a notice
 *
 * @category Form
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @author   Sarven Capadisli <csarven@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link     http://status.net/
 *
 * @see      DisfavorForm
 */
class GuiaForm extends Form {

    var $fileName = null;

    function __construct($out = null, $fileName = null) {
        parent::__construct($out);
        $this->fileName = $fileName;
    }

    /**
     * ID of the form
     *
     * @return int ID of the form
     */
    function id() {
        return 'download-guide-form';
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */
    function action() {
        return common_local_url('guiadescargar');
    }

    function formData() {
        $this->out->hidden('filename-guide', $this->fileName, 'filename');
    }

    /**
     * Legend of the Form
     *
     * @return void
     */

    /**
     * Action elements
     *
     * @return void
     */
    function formActions() {
        $this->out->submit('guide-download-submit', 'Descargar', 'submit', $this->fileName, 'Descarga la Guía');
    }

    /**
     * Class of the form.
     *
     * @return string the form's class
     */
    function formClass() {
        return 'form_download_guide';
    }

}
