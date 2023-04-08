<?php

namespace Niwee\Trident\Core;

use Niwee\Trident\Core\Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class Email
{
    public function __construct(string $to, string $subject, string $reply_to, string $template, $attachement = null)
    {
        $this->mail = new PHPMailer(true);

        $this->mail = new PHPMailer();
        $this->mailConfig = Config::get('mail');
        $this->appConfig = Config::get('app');
        $this->mail->setFrom($this->mailConfig->from_email, $this->mailConfig->from);
        $this->mail->CharSet = 'UTF-8';

        if ($this->appConfig->env == 'development')
        {
            $this->mail->isSendmail();
        }
        else if ($this->appConfig->env == 'production')
        {
            $this->mail->isSMTP();
            $this->mail->Host = $this->mailConfig->host;
            $this->mail->SMTPAuth = $this->mailConfig->auth;
            if ($this->mailConfig->auth == true)
            {
                $this->mail->Username = $this->mailConfig->username;
                $this->mail->Password = $this->mailConfig->password;
            }
            $this->mail->Port = $this->mailConfig->port;
        }

        $this->mail->addAddress($to);
        $this->mail->Subject = $subject;

        if ($reply_to)
        {
            $this->mail->addReplyTo($reply_to);
        }

        $this->mail->msgHTML($template);

        if ($attachement)
        {
            $this->mail->addAttachment($attachement);
        }

        if (!$this->mail->send())
        {
            throw new \Exception('Mailer Error: ' . $this->mail->ErrorInfo);
        }
        else
        {
            die('Le mail a bien été envoyé.');
        }
    }
}
