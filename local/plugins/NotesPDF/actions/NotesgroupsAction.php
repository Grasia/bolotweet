<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('STATUSNET')) {
    exit(1);
}

class NotesgroupsAction extends Action {

    var $user = null;

    function prepare($args) {
        parent::prepare($args);

        $this->user = common_current_user();

        return true;
    }

    function handle($args) {
        parent::handle($args);

        $this->showPage();
    }

    function title() {
        return _m('Apuntes');
    }

    function showContent() {
        if (empty($this->user)) {
            $this->element('p', array('class' => 'notespdf-generate-error'), _m('Login first!'));
        } else {


            $this->element('h2', null, 'Grupos Disponibles');

            // Obtenemos los grupos a los que pertenece el usuario.
            $groupsUser = $this->user->getGroups()->fetchAll();

            // Si no pertenece a ninguno, le animamos a que se una a alguno de los existentes.
            if (empty($groupsUser)) {

                $this->elementStart('p', array('class' => 'notes-empty-groups'));

                $this->raw("Todavía no perteneces a ningún grupo.");
                $this->element('br');
                $this->raw("Prueba uniéndote a alguno de los ");
                $this->element('a', array('href' => common_root_url() . 'groups/'), "Grupos Disponibles");

                $this->elementEnd('p');
            }

            // Si pertenece a alguno, los obtenemos.
            else {

                // Obtenemos los grupos que contienen tweets puntuados.
                $groupsGraded = Grades::getIDsGroupsWithGrades();

                $this->elementStart('ul');
                foreach ($groupsUser as $group) {
                    $this->elementStart('li', array('class' => 'notespdf-group-item'));

                    $avatar = $group->getAvatar();
                    $this->element('img', array('src' => $avatar, 'width' => '48', 'height' => '48'));
                    $this->elementStart('p');

                    $name = $group->getBestName();
                    $this->raw($name);

                    $this->elementEnd('p');

                    // Si el grupo no tiene puntuaciones no se pueden generar apuntes
                    if (!in_array($group->id, $groupsGraded)) {

                        $butGenerate = new Notesgroupsform($this, $group->id, 'true');
                        $butGenerate->show();

                        $this->elementStart('div', array('class' => 'prueba'));
                        $this->elementStart('p', array('class' => 'notes-error-group-text'));
                        $this->raw("No es posible generar apuntes. Grupo sin puntuaciones.");
                        $this->elementEnd('p');
                        $path = common_path('local/plugins/NotesPDF/css/x.gif');
                        $this->element('img', array('class' => 'notes-error-group', 'src' => $path));
                        $this->elementEnd('div');
                    } else {

                        $butGenerate = new Notesgroupsform($this, $group->id);
                        $butGenerate->show();
                    }


                    $this->elementEnd('li');
                }
                $this->elementEnd('ul');
            }
        }
    }

    function isReadOnly($args) {

        return true;
    }

}
