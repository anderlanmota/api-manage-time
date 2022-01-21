<?php
class Auth extends Database {
  // check if the requesting user is logged in
  protected function checkPermission() {
    $Sanitizer = new Sanitizer();
    $method = strtolower( $Sanitizer->alphabetic( $_SERVER[ 'REQUEST_METHOD' ], false, false, 20 ) );
    if ( $method != "post" && $method != "get" && $method != "put" && $method != "patch" && $method != "delete" && $method != "unlink" ) {
      return array( "responseCode" => "200", "message" => "OK" );
    } else {
		
		
		
	

    $auth = array( "login" => "ander", "userId" => "164260223919587552", "role" => "user", "status" => "active" );
    define( 'AUTH', $auth );
    // salva esses dados em define AUTH
    return array( "responseCode" => "200", "message" => "OK" );	
 
    }

  }

  // starts execution, identifying which method will call
  public function run() {
    $Sanitizer = new Sanitizer();
    $method = strtolower( $Sanitizer->alphabetic( $_SERVER[ 'REQUEST_METHOD' ], false, false, 20 ) );
    if ( method_exists( $this, $method ) ) {
      return $this->$method();
    } else {
      http_response_code( 405 );
      return array( "message" => "Método $method indisponível." );
    }
  }

  // cria uma nova sessão
  // Api Public: YES
  private function post() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $login = @strtolower( $Sanitizer->alphanumeric( DATA[ 'login' ], false, false, 35 ) );
      $password = DATA[ 'password' ];

      if ( empty( $login ) ) {
        http_response_code( 422 );
        return array( "message" => "Login não informado." );
      } else {
        if ( empty( $password ) ) {
          http_response_code( 422 );
          return array( "message" => "Senha não informada." );
        } else {
          $totalLogin = $this->database_count( "tb_users", "`login`='$login' AND `deleted`='0'" );
          if ( $totalLogin != '1' ) {
            http_response_code( 401 );
            return array( "message" => "Os dados de acesso não conferem." );
          } else {
            $dateNow = date( 'Y-m-d H:i:s' );
            $dateBack = date( 'Y-m-d H:i:s', strtotime( $dateNow . " -3600 seconds" ) );
            $totalLoginError = $this->database_count( "tb_auth_error_log", "`login`='$login' AND `created`>='$dateBack'" );
            if ( $totalLoginError >= '6' ) {
              http_response_code( 422 );
              return array( "message" => "Acesso temporariamente bloqueado." );
            } else {
              $user = $this->userDataAuth( $login );
              $passwordHash = hash( 'sha256', $password );
              if ( strcasecmp( $passwordHash, $user[ 'password' ] ) != 0 ) {
                $data_insert = array( 'login' => "$login", 'created' => "$dateNow" );
                $result = $this->database_insert( "tb_auth_error_log", $data_insert );
                $attemptsTotal = '6';
                $totalLoginError = $this->database_count( "tb_auth_error_log", "`login`='$login' AND `created`>='$dateBack'" );
                $attempts = ( $attemptsTotal - $totalLoginError );
                http_response_code( 401 );
                if ( $attempts >= '2' ) {
                  return array( "message" => "Os dados de acesso não conferem. Você tem $attempts tentativas restantes." );
                } else if ( $attempts == '1' ) {
                  return array( "message" => "Os dados de acesso não conferem. Você tem uma tentativa restante." );
                } else {
                  return array( "message" => "Os dados de acesso não conferem. Acesso temporariamente bloqueado." );
                }
              } else {
                if ( $user[ 'status' ] != "pending" && $user[ 'status' ] != "active" ) {
                  http_response_code( 422 );
                  return array( "message" => "Conta indisponível." );
                } else {
                  $userId = $user[ 'userId' ];
                  $role = $user[ 'role' ];
                  unset( $user[ 'password' ] );
                  $key = $this->generateKey();
                  $header = [ 'typ' => 'JWT', 'alg' => 'HS256' ];
                  $exp = time() + 3600;
                  $payload = [ 'exp' => $exp, 'uid' => $userId, 'role' => $role, ];
                  $header = json_encode( $header );
                  $payload = json_encode( $payload );
                  $header = $this->base64url_encode( $header );
                  $payload = $this->base64url_encode( $payload );
                  $sign = hash_hmac( 'sha256', $header . "." . $payload, $key, true );
                  $sign = $this->base64url_encode( $sign );
                  $jwt = $header . '.' . $payload . '.' . $sign;
                  http_response_code( 200 );
                  return array( "token" => "$jwt", "user" => $user );
                }
              }
            }
          }
        }
      }
    }
  }

  private function generateKey() {
    $characters = rand( 25, 45 );
    $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pass = array();
    $alphaLength = strlen( $alphabet ) - 1;
    for ( $i = 0; $i < $characters; $i++ ) {
      $n = rand( 0, $alphaLength );
      $pass[] = $alphabet[ $n ];
    }
    $token = implode( $pass );
    return $token;
  }

  private function userDataAuth( $login ) {
    $cols = array( 'userId', 'role', 'login', 'status', 'name', 'email', 'password', 'created' );
    $result = $this->database_select( "tb_users", $cols, "`login`='$login' AND `deleted`='0'" );
    $row = ( array )$result->fetch_object();
    return $row;
  }

  private function base64url_encode( $data ) {
    $b64 = base64_encode( $data );
    $url = strtr( $b64, '+/', '-_' );
    return rtrim( $url, '=' );
  }

  private function check_jwt( $jwt, $secret ) {
    $tokenParts = explode( '.', $jwt );
    $header = base64_decode( $tokenParts[ 0 ] );
    $payload = base64_decode( $tokenParts[ 1 ] );
    $signature_provided = $tokenParts[ 2 ];
    $expiration = json_decode( $payload )->exp;
    $is_token_expired = ( $expiration - time() ) < 0;
    $base64_url_header = base64url_encode( $header );
    $base64_url_payload = base64url_encode( $payload );
    $signature = hash_hmac( 'SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true );
    $base64_url_signature = base64url_encode( $signature );
    $is_signature_valid = ( $base64_url_signature === $signature_provided );
    if ( $is_token_expired ) {
      return false;
    } else {
      if ( !$is_signature_valid ) {
        return false;
      } else {
        return true;
      }
    }
  }
}
?>
