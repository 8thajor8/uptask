<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token){

         $this->email = $email;
         $this->nombre = $nombre;
         $this->token = $token;
        
    }

    public function enviarConfirmacion(){

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '82dd4947d438f4';
        $mail->Password = 'a723bb984f406b';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com','uptask.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= '<p><strong>Hola ' . $this->nombre . '</strong>. Has Creado tu cuenta en UpTask, solo debes confirmarla en el siguiente enlace</p>';
        $contenido .= '<p>Presiona aqui: <a href="http://localhost:3000/confirmar?token='. $this->token.'">Confirmar Cuenta</a></p>';
        $contenido .= '<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje</p>';
        $contenido .= '<html>';

        $mail->Body = $contenido;

        //Enviar mail
        $mail->send();



    }
    
    public function enviarInstrucciones(){

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '82dd4947d438f4';
        $mail->Password = 'a723bb984f406b';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com','uptask.com');
        $mail->Subject = 'Reestablecer Password';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= '<p><strong>Hola ' . $this->nombre . '</strong>. Parece que has olvidado tu password. Sigue el siguiente enlace para recuperarlo. <a href="http://localhost:3000/reestablecer?token='. $this->token.'">Reestablecer Password</a></p>';
        $contenido .= '<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje</p>';
        $contenido .= '<html>';

        $mail->Body = $contenido;

        //Enviar mail
        $mail->send();



    }
}