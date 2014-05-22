#!/usr/bin/env php
<?php

/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
 */

define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

$shortoptions = 'n:g:';
$longoptions = array('nickname=', 'group=');

$helptext = <<<END_OF_USERROLE_HELP
createGroup.php [options]
Create a group and admin.

  -n --nickname nickname of the user to vincule (admin)
  -g --group    Nickname or alias of group

END_OF_USERROLE_HELP;

require_once INSTALLDIR . '/scripts/commandline.inc';

// Cogemos usuario de parámetro

if (have_option('n', 'nickname')) {
    $nickOpt = get_option_value('n', 'nickname');
    $user = User::staticGet('nickname', $nickOpt);
    if (empty($user)) {
        print "Can't find user with nickname '$nickOpt'\n";
        exit(1);
    }
    $profile = $user->getProfile();
    if (empty($profile)) {
        print "User has no profile\n";
        exit(1);
    }
} else {
    print "You must provide a nickname.\n";
    exit(1);
}

// Cogemos nick de grupo de parámetro

if (have_option('g', 'group')) {
    $gnick = get_option_value('g', 'group');
} else {
    print "You must provide the nickname of the group.\n";
    exit(1);
}



// Comprobamos que el nick del grupo sea válido
try {
    $nickname = Nickname::normalize($gnick);
    if (!User_group::allowedNickname($nickname)) {
        print "Nick de grupo no válido.\n";
        exit(1);
    }
} catch (NicknameException $e) {
    print "Nick de grupo no válido.\n";
    exit(1);
}

// Comprobamos si ese nick de grupo existe.
$local = Local_group::staticGet('nickname', $nickname);
$alias = Group_alias::staticGet('alias', $nickname);

if (!empty($alias) || !empty($local)) {
    print "Nick de grupo en uso, elige otro nombre de grupo.\n";
    exit(1);
}


// Si hemos llegado aquí es que el usuario y el nick del grupo son válidos.
$force_scope = 1;
$join_policy = User_group::JOIN_POLICY_MODERATE;

User_group::register(array('nickname' => $nickname,
    'userid'   => $profile->id,
    'join_policy' => $join_policy,
    'force_scope' => $force_scope,
    'local' => true));


print "Registrado grupo '$nickname'.\n";
print "Vinculado usuario '$profile->nickname' con grupo '$nickname'.\n";