<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/form.php';


class ReopenForm extends Form {


    var $taskid = null;

    
    function __construct($out = null, $taskid = null) {
        parent::__construct($out);

        $this->taskid = $taskid;

   }


    /**
     * Action of the form
     *
     * @return string URL of the action
     */
    function action() {
        return common_local_url('taskcreate');
    }


    /**
     * Data elements
     *
     * @return void
     */
    function formData() {
        
            $this->out->hidden('reopen-task-h' . $this->taskid, $this->taskid, 'reopen-task');
            $this->out->element('input', array('type' => 'submit',
                                      'class' => 'reopen-task-button',
                                      'value' => 'Iniciar',
                                      'title' => 'Reabre esta tarea.',
                                      'onclick' => 'updateTaskStatus('.$this->taskid.');'));
    }


    /**
     * Class of the form.
     *
     * @return string the form's class
     */
    function formClass() {
        return 'ajax';
    }

}
