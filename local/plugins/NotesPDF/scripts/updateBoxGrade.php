<?php

define('STATUSNET', true);
define('LACONICA', true); // compatibility
define('INSTALLDIR', realpath(dirname(__FILE__) . '/../../../..'));

require_once INSTALLDIR . '/lib/common.php';
require_once INSTALLDIR . '/local/plugins/NotesPDF/classes/NotesPDF.php';

$tag = ($_POST['tag'] == 'Todos') ? '%' : $_POST['tag'];
$userid = ($_POST['userid'] == 'Todos') ? '%' : $_POST['userid'];
$groupid = $_POST['groupid'];


$grades = NotesPDF::getGradesinGroupWithTagAndUser($groupid, $userid, $tag);

echo '<option value="Todos">Todos</option>';

for ($i = 0; $i < count($grades); $i++) {
    echo '<option value="' . $grades[$i] . '">' . $grades[$i] . '</option>';
}
    
    
    
