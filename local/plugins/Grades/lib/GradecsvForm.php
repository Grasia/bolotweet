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
class GradecsvForm extends Form {

    var $groupid = null;

    /**
     * Constructor
     *
     * @param HTMLOutputter $out    output channel
     * @param Notice        $notice notice to favor
     */
    function __construct($out = null, $groupid = null) {
        parent::__construct($out);

        $this->groupid = $groupid;
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */
    function action() {
        return common_local_url('gradeexportcsv');
    }

    /**
     * Data elements
     *
     * @return void
     */
    function formData() {
        $this->out->hidden(null, $this->groupid, 'groupid');

        $this->out->element('p', 'options-text-csv', 'Introduce el separador entre palabras que desee:');
        $this->out->element('input', array('type' => 'text',
            'name' => 'grade-export-separator',
            'id' => 'grade-export-separator',
            'maxlength' => '1',
            'value' => ','));

        $this->out->element('p', 'options-text-csv', 'Introduce el delimitador de palabras que desee:');
        $this->out->element('input', array('type' => 'text',
            'name' => 'grade-export-delimiter',
            'id' => 'grade-export-delimiter',
            'maxlength' => '1',
            'value' => '"'));

        $this->out->element('p', 'hint-csv-text', 'Sólo se añadirá en los campos que lo precisen.');


        $this->out->element('br');
        $this->out->element('br');
        $this->out->element('input', array('type' => 'submit',
            'id' => 'grade-export-button',
            'value' => 'Generar CSV',
            'title' => 'Pulsa para generar el listado CSV'));
    }

    /**
     * Class of the form.
     *
     * @return string the form's class
     */
    function formClass() {
        return 'form_export_csv';
    }

}
