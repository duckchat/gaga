<?php
/**
 * Created by PhpStorm.
 * User: zhangjun
 * Date: 24/08/2018
 * Time: 10:55 AM
 */


class ZalyMail
{
    private $mail;

    public function __construct()
    {
        $this->mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $this->wpf_Logger = new Wpf_Logger();

        $mailConfig = ZalyConfig::getConfig("mail");
        try {
            //Server settings
            $this->SMTPDebug = 2;                                 // Enable verbose debug output
            $this->mail->isSMTP(true);                                      // Set mailer to use SMTP
            $this->mail->Host = $mailConfig['host']; // Specify main and backup SMTP servers
            $this->mail->SMTPAuth = $mailConfig['SMTPAuth'];                      // Enable SMTP authentication
            $this->mail->Username = $mailConfig['emailAddress'];               // SMTP username
            $this->mail->Password = $mailConfig['password'];                             // SMTP password
            $this->mail->SMTPSecure =  $mailConfig['SMTPSecure'];                            // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port = $mailConfig['port'];                        // TCP port to connect to
        } catch (Exception $ex) {
            $tag = __CLASS__.'-'.__FUNCTION__;
            $this->wpf_Logger->error($tag, $ex->getMessage());
        }
    }

    public function sendEmail($toEmail, $code, $sendName = "", $subject="重置密码")
    {
        try{
            $this->mail->setFrom($this->mail->Username, $sendName);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $this->getMailHtml($code);
            $this->mail->addAddress($toEmail);
            $sendFlag = $this->mail->send();
            if($sendFlag) {
                return true;
            }
            throw new Exception("send email failed");
        }catch (Exception $ex) {
            $tag = __CLASS__.'-'.__FUNCTION__;
            $this->wpf_Logger->error($tag, $ex->getMessage());
        }
    }

    private function getMailHtml($code)
    {
        $html = <<<HTML
            你正在重置站点密码，验证码是 {$code} , 请勿将密码透露给他人，防止自己的账户信息泄露。
HTML;
        return $html;
    }
}