<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/form.php';


class InitForm extends Form {

    /**
     * Notice to favor
     */
    var $groupid = null;
    var $enabled = null;
    /**
     * Constructor
     *
     * @param HTMLOutputter $out    output channel
     * @param Notice        $notice notice to favor
     */
    function __construct($out = null, $groupid = null, $enabled = true) {
        parent::__construct($out);

        $this->groupid = $groupid;
        $this->enabled = $enabled;
   }

    /**
     * ID of the form
     *
     * @return int ID of the form
     */
    function id() {
        return 'init-task-group-' . $this->groupid;
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
        $this->out->hidden('group-task-h'.$this->groupid, $this->groupid, 'groupid');
      
        if($this->enabled){
        $this->out->element('input', array('type' => 'submit',
                                      'id' => 'task-submit-' . $this->groupid,
                                      'class' => 'submit task-button-enabled',
                                      'value' => 'Iniciar',
                                      'title' => 'Crea una tarea para este grupo'));
    }
    else{
            $this->out->element('input', array('type' => 'button',
                                      'class' => 'task-disabled',
                                      'value' => 'Iniciada',
                                      'title' => 'La tarea ya está iniciada.',
                                      'disabled' => 'disabled'));
    }
    }


    /**
     * Class of the form.
     *
     * @return string the form's class
     */
    function formClass() {
        return 'form_task ajax';
    }

}
