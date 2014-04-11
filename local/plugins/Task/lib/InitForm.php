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
    var $user = null;
    var $status = null;
    var $taskid = null;
    
    /**
     * Constructor
     *
     * @param HTMLOutputter $out    output channel
     * @param Notice        $notice notice to favor
     */
    function __construct($out = null, $groupid = null, $result = null) {
        parent::__construct($out);

        $this->user = common_current_user();
        $this->groupid = $groupid;
        
        if($result == -1){
        $this->status = $result;
        $this->taskid = $result;
        }
        
        else{
            
              $this->status = $result[0];
              $this->taskid = $result[1];
        }
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
        $this->out->hidden('new-task-h'.$this->groupid, $this->status, 'status');
        $this->out->hidden('taskid-h'.$this->groupid, $this->taskid, 'taskid');

        if($this->status != 1){
        $this->out->element('input', array('type' => 'submit',
                                      'id' => 'task-submit-' . $this->groupid,
                                      'class' => 'submit task-button-enabled',
                                      'value' => 'Iniciar',
                                      'title' => 'Crea una tarea para este grupo',
                                      'onclick' => 'updateHistorical('.$this->user->id . ',' . $this->groupid . ')'));
        

        
        $this->out->element('input', array('type' => 'text',
            'name' => 'task-tag-' . $this->groupid,
            'class' => 'task-tag',
            'maxlength' => "13",
            'title' => 'Añade un tag relacionado con la tarea'));
        
                $this->out->element('p', 'label-for-tag', 'Tag: (Opcional)');
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
