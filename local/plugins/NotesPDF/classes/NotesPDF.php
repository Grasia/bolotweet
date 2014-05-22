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

class NotesPDF extends Memcached_DataObject {

    static function getGroupByID($idGroup) {

        return User_group::staticGet($idGroup);
    }

    /*
     * Devuelve los hashtag puntuados de un grupo dado.
     */

    static function getTagsOfUserWithGradeInGroup($idGroup, $userid, $grade) {

        $notes = new NotesPDF();

        $qry = 'select distinct nt.tag as tag' .
                ' from grades g, group_inbox gr, notice_tag nt, notice n, profile p' .
                ' where g.noticeid = gr.notice_id' .
                ' and g.grade LIKE \'' . $grade . '\'' .
                ' and gr.group_id = ' . $idGroup .
                ' and gr.notice_id = nt.notice_id' .
                ' and n.id = nt.notice_id' .
                ' and n.profile_id = p.id' .
                ' and p.nickname LIKE \'' . $userid . '\'';


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

    static function getUsersinGroupWithHashtagAndGrade($idGroup, $hashtag, $grade) {

        $notes = new NotesPDF();

        if ($tag == '%') {
            $qry = 'select p.nickname as nick' .
                    ' from grades g, group_inbox gr, notice_tag nt, notice n, profile p' .
                    ' where g.noticeid = gr.notice_id' .
                    ' and g.grade LIKE \'' . $grade . '\'' .
                    ' and gr.group_id = ' . $idGroup .
                    ' and gr.notice_id = n.id' .
                    ' and n.profile_id = p.id' .
                    ' group by p.nickname';
        } else {
            $qry = 'select p.nickname as nick' .
                    ' from grades g, group_inbox gr, notice_tag nt, notice n, profile p' .
                    ' where g.noticeid = gr.notice_id' .
                    ' and g.grade LIKE \'' . $grade . '\'' .
                    ' and gr.group_id = ' . $idGroup .
                    ' and gr.notice_id = nt.notice_id' .
                    ' and nt.tag LIKE \'' . $hashtag . '\'' .
                    ' and n.id = nt.notice_id' .
                    ' and n.profile_id = p.id' .
                    ' group by p.nickname';
        }
        $notes->query($qry); // all select fields will
// be written to fields of the Grade object. It is required that
// select fields are named after the Grade fields.

        $foundUsers = array();

        while ($notes->fetch()) {
            $foundUsers[] = $notes->nick;
        }

        $notes->free();

        return $foundUsers;
    }

    /*
     * Devuelve las puntuaciones de un usuario dado, en un grupo dado,
     * y con un tag determinado..
     */

    static function getGradesinGroupWithTagAndUser($idGroup, $userid, $tag) {

        $notes = new NotesPDF();

        if ($tag == '%') {

            $qry = 'select distinct g.grade as grade' .
                    ' from grades g, group_inbox gr, notice_tag nt, notice n, profile p' .
                    ' where g.noticeid = gr.notice_id' .
                    ' and gr.group_id = ' . $idGroup .
                    ' and gr.notice_id = n.id' .
                    ' and n.profile_id = p.id' .
                    ' and p.nickname LIKE \'' . $userid . '\'' .
                    ' order by grade asc';
        } else {

            $qry = 'select distinct g.grade as grade' .
                    ' from grades g, group_inbox gr, notice_tag nt, notice n, profile p' .
                    ' where g.noticeid = gr.notice_id' .
                    ' and gr.group_id = ' . $idGroup .
                    ' and gr.notice_id = nt.notice_id' .
                    ' and nt.tag LIKE \'' . $tag . '\'' .
                    ' and n.id = nt.notice_id' .
                    ' and n.profile_id = p.id' .
                    ' and p.nickname LIKE \'' . $userid . '\'' .
                    ' order by grade asc';
        }
        $notes->query($qry); // all select fields will
// be written to fields of the Grade object. It is required that
// select fields are named after the Grade fields.

        $foundGrades = array();

        while ($notes->fetch()) {
            $foundGrades[] = $notes->grade;
        }

        $notes->free();

        return $foundGrades;
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

        $qry = 'select distinct g.noticeid as noticeID' .
                ' from grades g, group_inbox gr' .
                ' where g.noticeid = gr.notice_id' .
                ' and gr.group_id = ' . $idGroup .
                ' and (g.grade = 2 or g.grade = 3)' .
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

    static function getNoticesInModeCustom($fields) {

        extract($fields);

        $notes = new NotesPDF();

        if ($tag == '%') {

            $qry = 'select distinct g.noticeid as noticeid' .
                    ' from grades g, group_inbox gr, notice_tag nt, notice n, profile p' .
                    ' where g.noticeid = gr.notice_id' .
                    ' and g.grade LIKE \'' . $grade . '\'' .
                    ' and gr.group_id = ' . $idGroup .
                    ' and gr.notice_id = n.id' .
                    ' and n.profile_id = p.id' .
                    ' and p.nickname LIKE \'' . $nick . '\'';
        } else {

            $qry = 'select distinct g.noticeid as noticeid' .
                    ' from grades g, group_inbox gr, notice_tag nt, notice n, profile p' .
                    ' where g.noticeid = gr.notice_id' .
                    ' and g.grade LIKE \'' . $grade . '\'' .
                    ' and gr.group_id = ' . $idGroup .
                    ' and gr.notice_id = nt.notice_id' .
                    ' and nt.tag LIKE \'' . $tag . '\'' .
                    ' and n.id = nt.notice_id' .
                    ' and n.profile_id = p.id' .
                    ' and p.nickname LIKE \'' . $nick . '\'';
        }

        $notes->query($qry); // all select fields will
// be written to fields of the Grade object. It is required that
// select fields are named after the Grade fields.

        $noticesids = array();

        while ($notes->fetch()) {
            $noticesids[] = $notes->noticeid;
        }

        $notes->free();

        return $noticesids;
    }

}
