<?php

/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
 */
class MakeGraderForm extends Form {

    /**
     * Profile of user to make Grader
     */
    var $profile = null;

    /**
     * Group
     */
    var $group = null;

    /**
     * Return-to args
     */
    var $args = null;

    /**
     * Constructor
     *
     * @param HTMLOutputter $out     output channel
     * @param Profile       $profile profile of user to block
     * @param User_group    $group   group to block user from
     * @param array         $args    return-to args
     */
    function __construct($out = null, $profile = null, $group = null, $args = null) {
        parent::__construct($out);

        $this->profile = $profile;
        $this->group = $group;
        $this->args = $args;
    }

    /**
     * ID of the form
     *
     * @return int ID of the form
     */
    function id() {
        // This should be unique for the page.
        return 'makegrader-' . $this->profile->id;
    }

    /**
     * class of the form
     *
     * @return string class of the form
     */
    function formClass() {
        return 'form_make_grader';
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */
    function action() {
        return common_local_url('makegrader', array('nickname' => $this->group->nickname));
    }

    /**
     * Legend of the Form
     *
     * @return void
     */
    function formLegend() {
        // TRANS: Form legend for form to make a user a group grader.
        $this->out->element('legend', null, _('Hacer Grader a ese usuario.'));
    }

    /**
     * Data elements of the form
     *
     * @return void
     */
    function formData() {
        $this->out->hidden('profileid-' . $this->profile->id, $this->profile->id, 'profileid');
        $this->out->hidden('groupid-' . $this->group->id, $this->group->id, 'groupid');
        if ($this->args) {
            foreach ($this->args as $k => $v) {
                $this->out->hidden('returnto-' . $k, $v);
            }
        }
    }

    /**
     * Action elements
     *
     * @return void
     */
    function formActions() {
        $this->out->submit(
                'submit',
                // TRANS: Button text for the form that will make a user administrator.
                _m('BUTTON', 'Hacer Grader'), 'submit', null,
                // TRANS: Submit button title.
                _m('TOOLTIP', 'Hace Grader a este usuario.'));
    }

}
