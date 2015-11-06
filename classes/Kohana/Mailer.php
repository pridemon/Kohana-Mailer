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
     * @param array $unsubscribe list of custom header List-Unsubscribe
     * @param null|string $list_id custom header List-id
     * @param null|string $from Sender
     *
     * @return bool
     * @throws Exception
     * @throws Kohana_Exception
     * @throws phpmailerException
     */
    public static function send( $to, $name, $subject, $body, $unsubscribe = [], $list_id = null, $from = null)
    {
        $mail   = new PHPMailer();
        $config = Kohana::$config->load('mailer');

        if (!$from) {
            $from = $config->get( 'from' );
        }

        if( $config->get( 'mode', 'mail' ) == 'smtp' ) {
            $smtp = $config->get( 'smtp' );

            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host = Arr::get( $smtp, 'host' );
            $mail->Port = Arr::get( $smtp, 'port' );
            $mail->Username = Arr::get( $smtp, 'username' );
            $mail->Password = Arr::get( $smtp, 'password' );
        } else {
            $mail->IsMail();
        }

        $mail->CharSet      = "UTF-8";

        $mail->From         = Arr::get( $from, 'mail' );
        $mail->FromName     = Arr::get( $from, 'name' );

        $mail->Subject      = $subject;

        if ($unsubscribe) {

            foreach($unsubscribe as $k => $val) {
                $unsubscribe[$k] = '<'.$val.'>';
            }

            $unsubscribe = implode(', ',$unsubscribe);

            $mail->AddCustomHeader(
                "List-Unsubscribe", $unsubscribe
            );

        }
        if ($list_id) {
            $mail->AddCustomHeader("List-id", $list_id);
        }

        // Prepare HTML and Alt message
        $mail->MsgHTML( $body );

        $mail->AddAddress( $to, $name );

        return $mail->Send();
    }
}
