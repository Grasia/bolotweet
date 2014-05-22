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
class GradeForm extends Form {

    /**
     * Notice to favor
     */
    var $notice = null;
    var $value = 0;

    /**
     * Constructor
     *
     * @param HTMLOutputter $out    output channel
     * @param Notice        $notice notice to favor
     */
    function __construct($out = null, $notice = null, $value = 0) {
        parent::__construct($out);

        $this->notice = $notice;
        $this->value = $value;
    }

    /**
     * ID of the form
     *
     * @return int ID of the form
     */
    function id() {
        return 'grade-' . $this->value;
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */
    function action() {
        return common_local_url('grade');
    }

    /**
     * Include a session token for CSRF protection
     *
     * @return void
     */
    function sessionToken() {
        $this->out->hidden('token-' . $this->notice->id, common_session_token());
    }

    /**
     * Legend of the Form
     *
     * @return void
     */
    function formLegend() {
        $this->out->element('legend', null, _('Grade this student'));
    }

    /**
     * Data elements
     *
     * @return void
     */
    function formData() {
        $this->out->hidden('notice-n' . $this->notice->id, $this->notice->id, 'notice');
        $this->out->hidden('value-notice-n' . $this->notice->id, $this->value, 'value');

        /* VERSION 2 */
        $this->out->element('input', array('type' => 'submit',
            'id' => 'grade-submit-' . $this->notice->id,
            'class' => 'submit',
            'value' => '' . $this->value,
            'title' => 'Grade this notice',
            'onClick' => 'puntuarNota(' . $this->notice->id . ',' . $this->value . ');'));
    }

    /**
     * Class of the form.
     *
     * @return string the form's class
     */
    function formClass() {
        return 'form_grade ajax';
    }

}
