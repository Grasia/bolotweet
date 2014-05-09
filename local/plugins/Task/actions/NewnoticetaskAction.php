<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/noticelist.php';
require_once INSTALLDIR . '/lib/mediafile.php';

class NewnoticetaskAction extends Action {

    /**
     * Error message, if any
     */
    var $msg = null;
    var $content = null;

    /**
     * Title of the page
     *
     * Note that this usually doesn't get called unless something went wrong
     *
     * @return string page title
     */
    function title() {
        // TRANS: Page title for sending a new notice.
        return _m('TITLE', 'New notice');
    }

    /**
     * Handle input, produce output
     *
     * Switches based on GET or POST method. On GET, shows a form
     * for posting a notice. On POST, saves the results of that form.
     *
     * Results may be a full page, or just a single notice list item,
     * depending on whether AJAX was requested.
     *
     * @param array $args $_REQUEST contents
     *
     * @return void
     */
    function handle($args) {
        if (!common_logged_in()) {
            // TRANS: Error message displayed when trying to perform an action that requires a logged in user.
            $this->clientError(_('Not logged in.'));
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // check for this before token since all POST and FILES data
            // is losts when size is exceeded
            if (empty($_POST) && $_SERVER['CONTENT_LENGTH']) {
                // TRANS: Client error displayed when the number of bytes in a POST request exceeds a limit.
                // TRANS: %s is the number of bytes of the CONTENT_LENGTH.
                $msg = _m('The server was unable to handle that much POST data (%s byte) due to its current configuration.', 'The server was unable to handle that much POST data (%s bytes) due to its current configuration.', intval($_SERVER['CONTENT_LENGTH']));
                $this->clientError(sprintf($msg, $_SERVER['CONTENT_LENGTH']));
            }
            parent::handle($args);

            $user = common_current_user();
                    
            $group = User_group::staticGet('id', $this->trimmed('groupid'));
            $taskid = $this->trimmed('taskid');

            try {
                $this->saveNewNotice();
                Task::completeTask($user->id, $taskid);
            } catch (Exception $e) {
                $this->ajaxErrorMsg($e->getMessage(), $taskid, $group);
                return;
            }
        }
    }

    /**
     * Save a new notice, based on arguments
     *
     * If successful, will show the notice, or return an Ajax-y result.
     * If not, it will show an error message -- possibly Ajax-y.
     *
     * Also, if the notice input looks like a command, it will run the
     * command and show the results -- again, possibly ajaxy.
     *
     * @return void
     */
    function saveNewNotice() {
        $user = common_current_user();
        assert($user); // XXX: maybe an error instead...
        $this->content = $this->trimmed('status_textarea');
        $groupid = $this->trimmed('inreplyto');

        $options = array();

        if (!$this->content) {
            // TRANS: Client error displayed trying to send a notice without content.
            $this->clientError(_('Mensaje vacío!!'));
            return;
        }

        $content_shortened = $user->shortenLinks($this->content);
        if (Notice::contentTooLong($content_shortened)) {
            // TRANS: Client error displayed when the parameter "status" is missing.
            // TRANS: %d is the maximum number of character for a notice.
            $this->clientError('Contenido demasiado largo! El tamaño máximo de caracteres es ' . Notice::maxContent() . '.');
        }

        $replyto = intval($this->trimmed('inreplyto'));
        if ($replyto) {
            $options['reply_to'] = $replyto;
        }

        $upload = null;
        $upload = MediaFile::fromUpload('attach');

        if (isset($upload)) {

            $content_shortened .= ' ' . $upload->shortUrl();

            if (Notice::contentTooLong($content_shortened)) {
                $upload->delete();
                // TRANS: Client error displayed exceeding the maximum notice length.
                // TRANS: %d is the maximum length for a notice.
                $this->clientError('Contenido demasiado largo! El tamaño máximo de caracteres '
                        . 'es ' . Notice::maxContent() . ', incluyendo la URL del adjunto.');
            }
        }

        if ($user->shareLocation()) {
            // use browser data if checked; otherwise profile data
            if ($this->arg('notice_data-geo')) {
                $locOptions = Notice::locationOptions($this->trimmed('lat'), $this->trimmed('lon'), $this->trimmed('location_id'), $this->trimmed('location_ns'), $user->getProfile());
            } else {
                $locOptions = Notice::locationOptions(null, null, null, null, $user->getProfile());
            }

            $options = array_merge($options, $locOptions);
        }

        $author_id = $user->id;
        $text = $content_shortened;

        // Does the heavy-lifting for getting "To:" information

        $notice_to = $this->trimmed('notice_to');
        $options['groups'] = array($notice_to);
 

        $notice = Notice::saveNew($user->id, $content_shortened, 'web', $options);
                     
        if (isset($upload)) {
            $upload->attachToNotice($notice);
        }

        if ($this->boolean('ajax')) {
            header('Content-Type: text/xml;charset=utf-8');
            $this->xw->startDocument('1.0', 'UTF-8');
            $this->elementStart('html');
            $this->elementStart('head');
            // TRANS: Page title after sending a notice.
            $this->element('title', null, _('Notice posted'));
            $this->elementEnd('head');
            $this->elementStart('body');
            $this->element('p', array('class' => 'notice-task-posted'), 'Tarea completada correctamente, mensaje publicado.');
            $this->elementEnd('body');
            $this->elementEnd('html');
        }
    }

    /**
     * Show an Ajax-y error message
     *
     * Goes back to the browser, where it's shown in a popup.
     *
     * @param string $msg Message to show
     *
     * @return void
     */
    function ajaxErrorMsg($msg, $taskid, $group) {
        
        $this->startHTML('text/xml;charset=utf-8', true);
        $this->elementStart('head');
        // TRANS: Page title after an AJAX error occurs on the send notice page.
        $this->element('title', null, _('Ajax Error'));
        $this->elementEnd('head');
        $this->elementStart('body');
        
        $this->elementStart('div', array('class' => 'input_form'));
        $notice_form = new NoticeTaskForm($this, $taskid, array('content' => $this->content, 'to_group' => $group), $msg);
        $notice_form->show();
        $this->elementEnd('div');
        $this->elementEnd('body');
        $this->elementEnd('html');
    }

}
