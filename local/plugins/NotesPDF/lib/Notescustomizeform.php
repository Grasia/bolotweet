<?php

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

class Notescustomizeform extends Form {

    protected $idGroup = null;


    function __construct($out = null, $id) {
        parent::__construct($out);

        $this->idGroup = $id;
    }

    function id() {

        return 'notes-customize' . $this->idGroup;
    }

    function action() {

        return common_local_url('notesgenerate');
    }

    function sessionToken() {
        $this->out->hidden('token-' . $this->idGroup, common_session_token());
    }

    function formClass() {
        return 'form_notes_customize';
    }

    function formData() {
        
        $this->out->hidden('group-h-' . $this->idGroup, $this->idGroup, 'idGroup');
         
   
        // Box para apuntes automáticos
        $this->out->elementStart('div', array('class' => 'notes-div-auto'));
        $this->out->element('p', 'notes-text-auto', 'Generar Apuntes Automáticos');
        $this->out->element('p', null, 'Se seleccionarán los tweets con la máxima puntuación hasta la fecha.');
        $this->out->elementStart('div');
        $this->out->submit('notes-submit-auto', _m('BUTTON', 'Aceptar'), 'submit', 'submit-auto');
         $this->out->elementEnd('div');
        $this->out->elementEnd('div');


        // Box para apuntes personalizados
        /*$this->out->elementStart('div', array('class' => 'notes-div-manual'));
        $this->out->element('p', 'notes-text-manual', 'Generar Apuntes Personalizados');

        $this->out->elementStart('div');
        $this->out->element('p', 'notes-manual-option', 'Hashtag: ');
        $this->out->elementStart('select', array('id' => 'notes-combo-hashtag', 'class' => 'notes-combo-manual'));

        $tags = NotesPDF::getTagsGradedinGroup($this->idGroup);

        $this->out->element('option', null, 'Todos');
        for ($i = 0; $i < sizeof($tags); $i++) {
            $this->out->elementStart('option');
            $this->out->raw($tags[$i]);
            $this->out->elementEnd('option');
        }

        $this->out->elementEnd('select');
        $this->out->elementEnd('div');
        $this->out->elementStart('div');
        $this->out->element('p', 'notes-manual-option', 'Usuario: ');
        $this->out->elementStart('select', array('id' => 'notes-combo-user', 'class' => 'notes-combo-manual'));

        $tags = NotesPDF::getTagsGradedinGroup($this->idGroup);

        $this->out->element('option', null, 'Todos');

        for ($i = 0; $i < sizeof($tags); $i++) {
            $this->out->element('option', null, $tags[$i]);
        }

        $this->out->elementEnd('select');
        $this->out->elementEnd('div');
        $this->out->elementStart('div');
        $this->out->element('p', 'notes-manual-option', 'Puntuación: ');
        $this->out->elementStart('select', array('id' => 'notes-combo-grade', 'class' => 'notes-combo-manual'));

        $this->out->element('option', null, 'Todos');
        for ($i = 0; $i < 4; $i++) {
            $this->out->elementStart('option');
            $this->out->raw($i);
            $this->out->elementEnd('option');
        }

        $this->out->elementEnd('select');
        $this->out->elementEnd('div');
        
        $this->out->submit('notes-submit-manual', _m('BUTTON', 'Aceptar'), 'submit', 'submit-custom');
        
        $this->out->elementEnd('div');*/

        
    }

}
