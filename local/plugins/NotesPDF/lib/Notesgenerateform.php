<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

class Notesgenerateform extends Form {

    protected $idGroup = null;
    protected $disabled = null;
    
     function __construct($out = null, $id, $disabled = 'false') {
        parent::__construct($out);

        $this->idGroup = $id;
        $this->disabled = $disabled;
    }
 
     function id() {
            
        return 'notes-generate' . $this->idGroup;
    }
    
    function action() {
        
        return common_local_url('notesoptions');
        
    }
    
        function sessionToken() {
        $this->out->hidden('token-' . $this->idGroup, common_session_token());
    }
    
    
        function formClass() {
        return 'form_notes_generate';
    }
    
        function formData(){
           
            $this->out->hidden('group-h-' . $this->idGroup, $this->idGroup, 'idGroup');
            //$this->out->hidden('value-notice-n' . $this->notice->id, $this->value, 'value');
            
             if($this->disabled=='true'){
             $this->out->element('input', array('type' => 'submit',
                                      'id' => 'notes-submit-' . $this->idGroup,
                                      'class' => 'submit',
                                      'value' => _('Generar Apuntes'),
                                      'title' => _('Genera apuntes de este grupo en PDF'),
                                      'disabled' => $this->disabled));
             
            }
            
            else{
                
                  $this->out->element('input', array('type' => 'submit',
                                      'id' => 'notes-submit-' . $this->idGroup,
                                      'class' => 'submit',
                                      'value' => _('Generar Apuntes'),
                                      'title' => _('Genera apuntes de este grupo en PDF')));
            }
    
    }
    
    
    
    
}