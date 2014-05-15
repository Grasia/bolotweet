#!/usr/bin/env php
<?php
/*
 * StatusNet - a distributed open-source microblogging tool
 * Copyright (C) 2008, 2009, StatusNet, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

$shortoptions = 'n:w:f:e:';
$longoptions = array('nickname=', 'password=', 'fullname=', 'email=');

$helptext = <<<END_OF_REGISTERUSER_HELP
registeruser.php [options]
registers a user in the database

  -n --nickname nickname of the new user
  -w --password password of the new user
  -f --fullname full name of the new user (optional)
  -e --email    email address of the new user (optional)

END_OF_REGISTERUSER_HELP;

require_once INSTALLDIR . '/scripts/commandline.inc';

$nickname = get_option_value('n', 'nickname');
$password = get_option_value('w', 'password');
$fullname = get_option_value('f', 'fullname');

$email = get_option_value('e', 'email');

if (empty($nickname) || empty($password)) {
    print "Must provide a username and password.\n";
    exit(1);
}

try {

    $user = User::staticGet('nickname', $nickname);

    if (!empty($user)) {
        throw new Exception("El usuario '$nickname' ya existe.");
    }

    $user = User::register(array('nickname' => $nickname,
                'password' => $password,
                'fullname' => $fullname));

    if (empty($user)) {
        throw new Exception("Can't register user '$nickname' with password '$password' and fullname '$fullname'.");
    }

    print "Registrado usuario '$user->nickname'\n";
    if (!empty($email)) {

        $orig = clone($user);

        $user->email = $email;

        if (!$user->updateKeys($orig)) {
            print "Failed!\n";
            throw new Exception("Can't update email address.");
        } else {

            $confirm = new Confirm_address();
            $confirm->code = common_confirmation_code(128);
            $confirm->address_type = 'recover';
            $confirm->user_id = $user->id;
            $confirm->address = (!empty($user->email)) ? $user->email : $confirm_email->address;

            if (!$confirm->insert()) {
                common_log_db_error($confirm, 'INSERT', __FILE__);
                throw new ServerException(_('Error saving address confirmation.'));
                return;
            }

            //Creamos el correo
            $subject = "¡Bienvenido a " . common_config('site', 'name') . "!";
            $body = crearEmail($user, $confirm);

            print "Enviando correo a $user->nickname...";
            if (mail_to_user($user, $subject, $body)) {
                print " Enviado.\n";
                sleep(2);
            } else {
                print " Fallo.\n";
            }
        }
    }
    
} catch (Exception $e) {
    print $e->getMessage() . "\n";
    exit(1);
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
