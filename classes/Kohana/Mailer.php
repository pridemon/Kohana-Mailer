<?php
/**
 * Helper which is responsible for sending emails.
 *
 * @author  Marcin Klawitter <marcin.klawitter@gmail.com>
 */
class Kohana_Mailer
{
    /**
     * Find and include phpMailer library
     */
    protected static function include_library()
    {
        //require_once Kohana::find_file( 'vendor/phpmailer', 'class.phpmailer' );
    }

    /**
     * Send message.
     *
     * @param       string      Email which message is being send to
     * @param       string      Username which message is being send to
     * @param       string      Email subject
     * @param       string      Email body
     * @return      boolean
     */
    public static function send( $to, $name, $subject, $body, $unsubscribe = array(), $list_id = null )
    {
        self::include_library();

        $mail   = new PHPMailer();
        $config = Kohana::$config->load('mailer');

        $from   = $config->get( 'from' );

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
