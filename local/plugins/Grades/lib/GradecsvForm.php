<?php

/**
 * StatusNet, the distributed open-source microblogging tool
 *
 * Form for favoring a notice
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
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
 *
 * @category  Form
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @author    Sarven Capadisli <csarven@status.net>
 * @copyright 2009 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://status.net/
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
