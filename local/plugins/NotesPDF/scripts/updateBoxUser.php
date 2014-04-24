<?php

define('STATUSNET', true);
define('LACONICA', true); // compatibility
define('INSTALLDIR', realpath(dirname(__FILE__) . '/../../../..'));

require_once INSTALLDIR . '/lib/common.php';
require_once INSTALLDIR . '/local/plugins/NotesPDF/classes/NotesPDF.php';

$tag = ($_POST['tag'] == 'Todos') ? '%' : $_POST['tag'];
$grade = ($_POST['grade'] == 'Todos') ? '%' : $_POST['grade'];
$groupid = $_POST['groupid'];

$users = NotesPDF::getUsersinGroupWithHashtagAndGrade($groupid, $tag, $grade);

echo '<option value="Todos">Todos</option>';

for ($i = 0; $i < count($users); $i++) {
    echo '<option value="' . $users[$i] . '">' . $users[$i] . '</option>';
}
    
    
    
