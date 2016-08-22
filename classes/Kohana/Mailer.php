<?php

/**
 * Helper which is responsible for sending emails.
 *
 * @author  Marcin Klawitter <marcin.klawitter@gmail.com>
 */
class Kohana_Mailer
{
    /**
     * @param string $to Email which message is being send to
     * @param string $name Username which message is being send to
     * @param string $subject Email subject
     * @param string $body Email body
     * @param array $attachments attached files ['filename', 'name']
     * @param array $unsubscribe list of custom header List-Unsubscribe
     * @param null|string $list_id custom header List-id
     * @param null|Kohana_Config_Group $config
     * @return bool
     * @throws Kohana_Exception
     * @throws phpmailerException
     */
    public static function send($to, $name, $subject, $body, $unsubscribe = [], $list_id = null, Kohana_Config_Group $config = null, $attachments = [])
    {
        $mail = new PHPMailer(true);

        if (!$config) {
            $config = Kohana::$config->load('mailer');
        }

        if ($config->get('mode', 'mail') == 'smtp' and $smtp = $config->get('smtp')) {
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host = Arr::get($smtp, 'host');
            $mail->Port = Arr::get($smtp, 'port');
            $mail->Username = Arr::get($smtp, 'username');
            $mail->Password = Arr::get($smtp, 'password');
        } else {
            $mail->IsMail();
        }

        $mail->CharSet = "UTF-8";
        $mail->From = $config->from['mail'];
        $mail->FromName = $config->from['name'];
        $mail->Subject = $subject;

        if ($unsubscribe) {
            foreach ($unsubscribe as $k => $val) {
                $unsubscribe[$k] = '<' . $val . '>';
            }
            $mail->AddCustomHeader("List-Unsubscribe", implode(', ', $unsubscribe));
        }

        if ($list_id) {
            $mail->AddCustomHeader("List-id", '<' . $list_id . '.' . Kohana::$config->load('url')->domain . '>');
        }

        // Prepare HTML and Alt message
        $mail->MsgHTML($body);
        $mail->AddAddress($to, $name);
        if(count($attachments)>0){
            foreach($attachments as $name=>$attachment) {
                $mail->addAttachment($attachment, $name);
            }
        }

        return $mail->Send();
    }
}
