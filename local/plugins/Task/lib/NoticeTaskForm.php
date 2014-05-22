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
 * Form for posting a notice
 *
 * Frequently-used form for posting a notice
 *
 * @category Form
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @author   Sarven Capadisli <csarven@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link     http://status.net/
 *
 * @see      HTMLOutputter
 */
class NoticeTaskForm extends Form {

    /**
     * Current action, used for returning to this page.
     */
    var $actionName = null;

    /**
     * Pre-filled content of the form
     */
    var $content = null;

    /**
     * The current user
     */
    var $user = null;

    /**
     * The notice being replied to
     */
    var $inreplyto = null;

    /**
     * Pre-filled location content of the form
     */
    var $lat;
    var $lon;
    var $location_id;
    var $location_ns;

    /** select this group from the drop-down by default. */
    var $to_group;

    /** select this user from the drop-down by default. */
    var $to_profile;

    /** Pre-click the private checkbox. */
    var $private;
    var $msg;
    var $taskid;

    /**
     * Constructor
     *
     * @param Action $action  Action we're being embedded into
     * @param array  $options Array of optional parameters
     *                        'user' a user instead of current
     *                        'content' notice content
     *                        'inreplyto' ID of notice to reply to
     *                        'lat' Latitude
     *                        'lon' Longitude
     *                        'location_id' ID of location
     *                        'location_ns' Namespace of location
     */
    function __construct($action, $taskid, $options = null, $msg = null) {
        // XXX: ??? Is this to keep notice forms distinct?
        // Do we have to worry about sub-second race conditions?
        // XXX: Needs to be above the parent::__construct() call...?

        $this->id_suffix = time();

        parent::__construct($action);

        if (is_null($options)) {
            $options = array();
        }

        if ($msg != null) {
            $this->msg = $msg;
        }

        $this->taskid = $taskid;

        $this->actionName = $action->trimmed('action');

        $prefill = array('content', 'inreplyto', 'lat',
            'lon', 'location_id', 'location_ns',
            'to_group', 'to_profile', 'private');

        foreach ($prefill as $fieldName) {
            if (array_key_exists($fieldName, $options)) {
                $this->$fieldName = $options[$fieldName];
            }
        }

        // Prefill the profile if we're replying

        if (empty($this->to_profile) &&
                !empty($this->inreplyto)) {
            $notice = Notice::staticGet('id', $this->inreplyto);
            if (!empty($notice)) {
                $this->to_profile = $notice->getProfile();
            }
        }

        if (array_key_exists('user', $options)) {
            $this->user = $options['user'];
        } else {
            $this->user = common_current_user();
        }

        $this->profile = $this->user->getProfile();

        if (common_config('attachments', 'uploads')) {
            $this->enctype = 'multipart/form-data';
        }
    }

    /**
     * ID of the form
     *
     * @return string ID of the form
     */
    function id() {
        return 'form_notice_task_' . $this->taskid;
    }

    /**
     * Class of the form
     *
     * @return string class of the form
     */
    function formClass() {
        return 'form_notice ajax';
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */
    function action() {
        return common_local_url('newnoticetask');
    }

    /**
     * Legend of the Form
     *
     * @return void
     */
    function formLegend() {
        // TRANS: Form legend for notice form.
        $this->out->element('legend', null, _('Enviar una noticia'));
    }

    /**
     * Data elements
     *
     * @return void
     */
    function formData() {

        $this->out->element('label', array('for' => 'notice_data-text',
            'id' => 'notice_data-text-label'),
                // TRANS: Title for notice label. %s is the user's nickname.
                sprintf(_('What\'s up, %s?'), $this->user->nickname));
        // XXX: vary by defined max size

        if (!empty($this->msg)) {
            $this->out->element('p', array('class' => 'error error-notice-task'), $this->msg);
        }

        $this->out->element('textarea', array('class' => 'notice_data-text',
            'cols' => 35,
            'rows' => 4,
            'name' => 'status_textarea',
            'onfocus' => 'counter(' . $this->taskid . ');'), ($this->content) ? $this->content : '');

        $contentLimit = Notice::maxContent();

        if ($contentLimit > 0) {

            $count = $contentLimit;
            if ($this->content) {

                $count = $contentLimit - mb_strlen($this->content);
            }

            if ($count < 0) {
                $this->out->element('span', array('class' => 'count count-negative'), $count);
            } else
                $this->out->element('span', array('class' => 'count'), $count);
        }

        if (common_config('attachments', 'uploads')) {
            $this->out->hidden('MAX_FILE_SIZE', common_config('attachments', 'file_quota'));
            $this->out->elementStart('label', array('class' => 'notice_data-attach'));
            // TRANS: Input label in notice form for adding an attachment.
            $this->out->text(_('Attach'));
            $this->out->element('input', array('class' => 'notice_data-attach',
                'type' => 'file',
                'name' => 'attach',
                // TRANS: Title for input field to attach a file to a notice.
                'title' => _('Attach a file.')));
            $this->out->elementEnd('label');
        }
        if (!empty($this->actionName)) {
            $this->out->hidden('notice_return-to', $this->actionName, 'returnto');
        }
        $this->out->hidden('notice_in-reply-to', $this->inreplyto, 'inreplyto');

        $this->out->hidden('hidden-group-id', $this->to_group->id, 'groupid');
        $this->out->hidden('hidden-task-id', $this->taskid, 'taskid');


        $this->out->elementStart('div', 'to-selector');

        $dropdown[$this->to_group->id] = $this->to_group->getBestName();

        $this->out->dropdown('notice_to',
                // TRANS: Label for drop-down of potential addressees.
                'Para:', $dropdown, null, false, $this->to_group->getBestName());

        $this->out->elementEnd('div');
    }

    /**
     * Action elements
     *
     * @return void
     */
    function formActions() {
        $this->out->element('input', array('id' => 'submit-task-' . $this->taskid,
            'class' => 'submit task-submit-form',
            'name' => 'status_submit',
            'type' => 'submit',
            'onclick' => 'reducir(' . $this->taskid . ');',
            // TRANS: Button text for sending notice.
            'value' => _m('BUTTON', 'Enviar')));
    }

}
