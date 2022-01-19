<?php
// class responsible for sending emails
class Email {
  // $obj = new Email();
  // $return = $obj->send("to@email.com", "Subject here", "Message here", "Html message <b>here</b>");
  public function send( $toemail, $subject, $textmessage, $htmlmessage ) {
    if ( empty( $toemail ) ) {
      return false;
    } else {
      if ( empty( $subject ) ) {
        return false;
      } else {
        if ( empty( $textmessage ) && empty( $htmlmessage ) ) {
          return false;
        } else {
          if ( !filter_var( $toemail, FILTER_VALIDATE_EMAIL ) ) {
            return false;
          } else {
            // choose which option to use to send email
            // mailgun
            return $this->mailgun( $toemail, $subject, $textmessage, $htmlmessage );
          }
        }
      }
    }
  }

  // send email using Mailgun
  private function mailgun( $toemail, $subject, $textmessage, $htmlmessage ) {
	require(dirname( __FILE__ ) . "/../config/mailgun.php");
    //$fromEmail, $fromName, $fromDomain, $mailgunKey
    $url = "https://api.eu.mailgun.net/v3/" . $fromDomain . "/messages";
    $data = array( 'from' => $fromName . " <" . $fromEmail . ">", 'to' => "$toemail", 'subject' => "$subject", 'text' => "$textmessage", 'html' => "$htmlmessage" );
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
    curl_setopt( $ch, CURLOPT_USERPWD, 'api:' . $mailgunKey );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    $response = curl_exec( $ch );
    curl_close( $ch );
    $responsejson = json_decode( $response, true );
    if ( empty( $responsejson[ 'id' ] ) ) {
      $message_error = $responsejson[ 'message' ];
      error_log( date( "Y-m-d H:i:s" ) . " - " . $_SERVER[ 'REMOTE_ADDR' ] . "\r\nMailgun: $response.\r\n\r\n", 3, dirname( __FILE__ ) . "/_error_email.log" );
      return false;
    } else {
      return true;
    }
  }

}
?>