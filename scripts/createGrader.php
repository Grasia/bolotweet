#!/usr/bin/env php
<?php

define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

$shortoptions = 'i:n:g:G:d';
$longoptions = array('id=', 'nickname=','group=', 'group-id=', 'delete');

$helptext = <<<END_OF_USERROLE_HELP
createGrader.php [options]
Create a grader.

  -d --delete   delete the grader
  -i --id       ID of the user
  -n --nickname nickname of the user to add
  -g --group    Nickname or alias of group
  -G --group-id ID of group

END_OF_USERROLE_HELP;

require_once INSTALLDIR.'/scripts/commandline.inc';

// Comprobamos usuario

if (have_option('i', 'id')) {
    $id = get_option_value('i', 'id');
    $profile = Profile::staticGet('id', $id);
    if (empty($profile)) {
        print "Can't find user with ID $id\n";
        exit(1);
    }
} else if (have_option('n', 'nickname')) {
    $nickname = get_option_value('n', 'nickname');
    $user = User::staticGet('nickname', $nickname);
    if (empty($user)) {
        print "Can't find user with nickname '$nickname'\n";
        exit(1);
    }
    $profile = $user->getProfile();
    if (empty($profile)) {
        print "User with ID $id has no profile\n";
        exit(1);
    }
} else {
    print "You must provide either an ID or a nickname.\n";
    exit(1);
}

// Comprobamos Grupo

    if (have_option('G', 'group-id')) {
        $gid = get_option_value('G', 'group-id');
        $lgroup = Local_group::staticGet('group_id', $gid);
    } else if (have_option('g', 'group')) {
        $gnick = get_option_value('g', 'group');
        $lgroup = Local_group::staticGet('nickname', $gnick);
    }
    if (empty($lgroup)) {
        throw new Exception("No such local group: $gnick");
    }

$role = 'grader';

// Comprobamos si se desea borrar.    
if (have_option('d', 'delete')) {
    print "Revoking role '$role' from user '$profile->nickname' ($profile->id)...";
    try {
        $profile->revokeRole($role);
        print "OK\n";
        
        print "Desvinculando usuario '$profile->nickname' del Grupo '$lgroup->nickname' ($lgroup->group_id)...";
        Gradesgroup::desvincularGrupo($profile->id, $lgroup->group_id);
        print "OK\n";
    } catch (Exception $e) {
        print "FAIL\n";
        print $e->getMessage();
        print "\n";
    }
    
} else { // Si no es borrar
    print "Granting role '$role' to user '$profile->nickname' ($profile->id)...";
    try {
        $profile->grantRole($role);
        print "OK\n";
        
        print "Vinculando usuario '$profile->nickname' con Grupo '$lgroup->nickname' ($lgroup->group_id)...";
        Gradesgroup::vincularGrupo($profile->id, $lgroup->group_id);
        print "OK\n";
        
    } catch (Exception $e) {
        print "FAIL\n";
        print $e->getMessage();
        print "\n";
    }
}