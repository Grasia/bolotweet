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

$shortoptions = 'i:n:g:G:a';
$longoptions = array('id=', 'nickname=', 'group=', 'group-id=');

$helptext = <<<END_OF_USERROLE_HELP
emailBienvenida.php [options]
Send a welcome email with instructions.

  -i --id       ID of the user to send email
  -n --nickname nickname of the user to send email
  -g --group    Nickname or alias of group to send email
  -G --group-id ID of group to send email
  -a --all      Send email to all members

END_OF_USERROLE_HELP;

require_once INSTALLDIR . '/scripts/commandline.inc';

// Si tiene la opción all
if (have_option('a', 'all')) {


    $qry = "SELECT * FROM profile order by id asc";
    $pflQry = new Profile();

    $pflQry->query($qry);

    $members = array();

    while ($pflQry->fetch()) {
        $members[] = clone($pflQry);
    }

    $pflQry->free();
}

// Si tiene opción de usuario único
else if (have_option('i', 'id') || have_option('n', 'nickname')) {

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

    }
    
        $members = array($profile);
}

// Si tiene la opción de grupo
else if (have_option('G', 'group-id') || have_option('g', 'group')) {

    if (have_option('G', 'group-id')) {
        $gid = get_option_value('G', 'group-id');
        $group = User_group::staticGet('id', $gid);
        
    } else if (have_option('g', 'group')) {
        $gnick = get_option_value('g', 'group');
        $group = User_group::staticGet('nickname', $gnick);
    }

    if (empty($group)) {
        print "No such local group: $gnick\n\n";
        exit(1);
    }

    $nMiembros = $group->getMemberCount();

    if ($nMiembros == 0) {
        print "El grupo $gnick no tiene usuarios.";
        exit(1);
    } else {

        $profile = $group->getMembers();

        while ($profile->fetch()) {
            $members[] = clone($profile);
        }
    }
}
// Si no tiene ninguna de las anteriores.
else {
    print "Faltan parámetros\n";
    exit(1);
}


// Si hemos llegado aquí es que hay usuario o grupo válido.
foreach ($members as $member) {

    $user = $member->getUser();

    if (empty($user->email)) {
        print "El usuario '$user->nickname' no tiene email registrado.\n";
    } else {

        $confirm = new Confirm_address();
        $confirm->code = common_confirmation_code(128);
        $confirm->address_type = 'recover';
        $confirm->user_id = $user->id;
        $confirm->address = (!empty($user->email)) ? $user->email : $confirm_email->address;

        if (!$confirm->insert()) {
            common_log_db_error($confirm, 'INSERT', __FILE__);
// TRANS: Server error displayed if e-mail address confirmation fails in the database on the password recovery form.
            throw new ServerException(_('Error saving address confirmation.'));
            return;
        }

//Creamos el correo
        $subject = "¡Bienvenido a " . common_config('site', 'name') . "!";
        $body = crearEmail($user, $confirm);

        print "Enviando correo a $user->nickname...";
        if (mail_to_user($user, $subject, $body)) {
            print " Enviado.\n";
            sleep(5);
        } else {
            print " Fallo.\n";
        }
    }
}

function crearEmail($user, $confirm) {


// Creamos el correo personalizado

    $body = "Hola, $user->nickname.";
    $body .= "\n\n";
    $body .= "Bienvenido a " . common_config('site', 'name') . ".";
    $body .= "\n\n";
    $body .= "Gracias por unirte a esta nueva plataforma. Ahora, podrás disfrutar " .
            'de todas sus ventajas.';
    $body .= "\n\n";
    $body .= "Cuando accedas a BoloTweet, dirígete a esta dirección para obtener el " .
            "manual de uso.";
    $body .= "\n\n";
    $body .= "\t" . common_local_url('guiamostrar');
    $body .= "\n\n";
    $body .= "Si es la primera vez que entras al sistema, deberás pulsar en el siguiente enlace " .
            "para crear tu contraseña.";
    $body .= "\n\n";
    $body .= "\t" . common_local_url('recoverpassword', array('code' => $confirm->code));
    $body .= "\n\n";
    $body .= "¡Advertencia! Este enlace es de un sólo uso. Si al acceder no cambias la contraseña, quedará inservible "
            . "y deberás utilizar el apartado \"¿Contraseña olvidada o perdida?\" para solicitar una nueva.";
    $body .= "\n\n";
    $body .= "Si ya has visitado BoloTweet, puedes entrar de nuevo con tus datos de siempre.";
    $body .= "\n\n";
    $body .= "Puedes empezar a usarlo utilizando tu nombre de usuario o correo electrónico, "
            . "desde:\n\n";
    $body .= "\t" . common_local_url('login');
    $body .= "\n\n";
    $body .= "---------------------------------------------------\n";
    $body .= "Nombre de Usuario: $user->nickname\n";
    $body .= "Correo electrónico: $user->email\n";
    $body .= "---------------------------------------------------\n";
    $body .= "\n\n\nPara cualquier duda, pongánse en contacto con su profesor.";
    $body .= "\n\nUn saludo,\n";
    $body .= "Administrador de " . common_config('site', 'name');
    $body .= "\n\n\n\n";
    $body .= "--------------------------------------\n";
    $body .= "El contenido del presente mensaje es privado, estrictamente confidencial y " .
            "exclusivo para sus destinatarios, pudiendo contener información protegida por normas legales " .
            "y de secreto profesional. Bajo ninguna circunstancia su contenido puede ser transmitido o revelado " .
            "a terceros ni divulgado en forma alguna. En consecuencia de haberlo recibido por error, solicitamos contactar" .
            " al remitente y eliminarlo de su sistema.";

    return $body;
}
