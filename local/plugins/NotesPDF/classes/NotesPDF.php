<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

class NotesPDF extends Memcached_DataObject {

    static function getGroupByID($idGroup) {

        return User_group::staticGet($idGroup);
    }

    /*
     * Devuelve los hashtag puntuados de un grupo dado.
     */
    static function getTagsGradedinGroup($idGroup) {

        $notes = new NotesPDF();

        $qry = 'select distinct nt.tag as tag' .
                ' from grades g, group_inbox gi, notice_tag nt' .
                ' where g.noticeid = gi.notice_id' .
                ' and gi.notice_id = nt.notice_id' .
                ' and group_id = ' . $idGroup;


        $notes->query($qry); // all select fields will
// be written to fields of the Grade object. It is required that
// select fields are named after the Grade fields.

        $foundTags = array();

        while ($notes->fetch()) {
            $foundTags[] = $notes->tag;
        }

        $notes->free();

        return $foundTags;
    }
    
    

    /*
     * Devuelve los nickname de los usuarios de un grupo dado, y que tengan
     * tweets puntuados en ese grupo con un cierto hashtag.
     */
    static function getUsersinGroupGradedWithHashtag($idGroup, $hashtag) {

        $notes = new NotesPDF();

        $qry = 'select p.nickname as nick' .
                ' from grades g, group_inbox gr, notice_tag nt, notice n, profile p' .
                ' where g.noticeid = gr.notice_id' .
                ' and gr.group_id = ' . $idGroup .
                ' and gr.notice_id = nt.notice_id' .
                ' and nt.tag = \'' . $hashtag . '\'' .
                ' and n.id = nt.notice_id' .
                ' and n.profile_id = p.id' .
                ' group by p.nickname';


        $notes->query($qry); // all select fields will
// be written to fields of the Grade object. It is required that
// select fields are named after the Grade fields.

        $foundUsers = array();

        while ($notes->fetch()) {
            $foundUsers[] = $notes->tag;
        }

        $notes->free();

        return $foundUsers;
    }

     /*
     * Devuelve los hashtag de tweets puntuados que un usuario concreto
      *  haya hecho en un grupo dado.
     */
    static function getTagsinGroupGradedOfUser($idGroup, $nickName) {

        $notes = new NotesPDF();

        $qry = 'select nt.tag as tag' .
                ' from grades g, group_inbox gr, notice_tag nt, notice n, profile p' .
                ' where g.noticeid = gr.notice_id' .
                ' and gr.group_id = ' . $idGroup .
                ' and gr.notice_id = nt.notice_id' .
                ' and n.id = nt.notice_id' .
                ' and n.profile_id = p.id' .
                ' and p.nickname = \'' . $nickName . '\'' .
                ' group by tag';


        $notes->query($qry); // all select fields will
// be written to fields of the Grade object. It is required that
// select fields are named after the Grade fields.

        $foundTags = array();

        while ($notes->fetch()) {
            $foundTags[] = $notes->tag;
        }

        $notes->free();

        return $foundTags;
    }
    
    /*
     * Devuelve los ID's de los tweets puntuados con 2 y 3 de un grupo dado.
     * Es la función utilizada para obtener los apuntes en modo automático.
     */
     static function getNoticeIDsInAGroupModeAuto($idGroup) {

        $notes = new NotesPDF();

        $qry = 'select g.noticeid as noticeID' .
                ' from grades g, group_inbox gr' .
                ' where g.noticeid = gr.notice_id' .
                ' and gr.group_id = ' . $idGroup .
                ' and g.grade = 2 or g.grade = 3' .
                ' order by g.noticeid';


        $notes->query($qry); // all select fields will
// be written to fields of the Grade object. It is required that
// select fields are named after the Grade fields.

        $noticesids = array();

        while ($notes->fetch()) {
            $noticesids[] = $notes->noticeID;
        }

        $notes->free();

        return $noticesids;
    }
    

}
