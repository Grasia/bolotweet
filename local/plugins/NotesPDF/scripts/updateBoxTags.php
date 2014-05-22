<?php

/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
 */
define('STATUSNET', true);
define('LACONICA', true); // compatibility
define('INSTALLDIR', realpath(dirname(__FILE__) . '/../../../..'));

require_once INSTALLDIR . '/lib/common.php';
require_once INSTALLDIR . '/local/plugins/NotesPDF/classes/NotesPDF.php';

$userid = ($_POST['userid'] == 'Todos') ? '%' : $_POST['userid'];
$grade = ($_POST['grade'] == 'Todos') ? '%' : $_POST['grade'];
$groupid = $_POST['groupid'];


$tags = NotesPDF::getTagsOfUserWithGradeInGroup($groupid, $userid, $grade);

echo '<option value="Todos">Todos</option>';

for ($i = 0; $i < count($tags); $i++) {
    echo '<option value="' . $tags[$i] . '">' . $tags[$i] . '</option>';
}
    
    
    
