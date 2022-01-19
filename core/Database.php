<?php
// class responsible for executing transactions within the database
abstract class Database {

  protected function database_transaction( $querys ) {
    $fileContents = file_get_contents( dirname( __FILE__ ) . "/../config/database.json" );
    $contentArr = json_decode( $fileContents, true );
    $server = $contentArr[ 'server' ];
    $user = $contentArr[ 'user' ];
    $password = $contentArr[ 'password' ];
    $database = $contentArr[ 'database' ];
    $port = $contentArr[ 'port' ];
    if ( !is_array( $querys ) ) {
      return false;
    } else {
      $querystotal = count( $querys );
      if ( $querystotal <= "0" ) {
        return false;
      } else {
        @$mysqli = new mysqli( $server, $user, $password, $database, $port );
        if ( $mysqli->connect_error ) {
          $error_string = $mysqli->connect_errno . "\r\n" . $mysqli->connect_error;
          error_log( date( "Y-m-d H:i:s" ) . " - " . $_SERVER[ 'REMOTE_ADDR' ] . "\r\n$error_string\r\n\r\n", 3, dirname( __FILE__ ) . "/error_database_transaction.log" );
          return false;
        } else {
          if ( !$mysqli->set_charset( "utf8" ) ) {
            $error_string = $mysqli->errno . "\r\n" . $mysqli->error;
            error_log( date( "Y-m-d H:i:s" ) . " - " . $_SERVER[ 'REMOTE_ADDR' ] . "\r\n$error_string\r\n\r\n", 3, dirname( __FILE__ ) . "/error_database_transaction.log" );
            return false;
          } else {
            $executed = 0;
            $mysqli->begin_transaction();
            foreach ( $querys as $query_exec ) {
              if ( @$mysqli->query( "$query_exec" ) === TRUE ) {
                $executed++;
              } else {
                $error_string = "$query_exec\r\n" . $mysqli->errno . "\r\n" . $mysqli->error;
                error_log( date( "Y-m-d H:i:s" ) . " - " . $_SERVER[ 'REMOTE_ADDR' ] . "\r\n$error_string\r\n\r\n", 3, dirname( __FILE__ ) . "/error_database_transaction.log" );
              }
            }
            if ( $executed == $querystotal ) {
              $mysqli->commit();
              $mysqli->close();
              return true;
            } else {
              $mysqli->rollback();
              $mysqli->close();
              return false;
            }
          }
        }
      }
    }
  }

  /*
  Example mysqlquery:
  $query = "SELECT `id`, `login` FROM `tb_test` WHERE `id`='123';";
  $result = $this->mysqlquery($query);
  
  Return:
  false or query result
  */
  protected function mysqlquery( $query ) {
    $fileContents = file_get_contents( dirname( __FILE__ ) . "/../config/database.json" );
    $contentArr = json_decode( $fileContents, true );
    $server = $contentArr[ 'server' ];
    $user = $contentArr[ 'user' ];
    $password = $contentArr[ 'password' ];
    $database = $contentArr[ 'database' ];
    $port = $contentArr[ 'port' ];
    @$mysqli = new mysqli( $server, $user, $password, $database, $port );
    if ( $mysqli->connect_error ) {
      $error_string = $mysqli->connect_errno . "\r\n" . $mysqli->connect_error;
      error_log( date( "Y-m-d H:i:s" ) . " - " . $_SERVER[ 'REMOTE_ADDR' ] . "\r\n$error_string\r\n\r\n", 3, dirname( __FILE__ ) . "/error_database.log" );
      return false;
    } else {
      if ( !$mysqli->set_charset( "utf8" ) ) {
        $error_string = $mysqli->errno . "\r\n" . $mysqli->error;
        error_log( date( "Y-m-d H:i:s" ) . " - " . $_SERVER[ 'REMOTE_ADDR' ] . "\r\n$error_string\r\n\r\n", 3, dirname( __FILE__ ) . "/error_database.log" );
        return false;
      } else {
        $result = @$mysqli->query( "$query" );
        if ( $result ) {
          $mysqli->close();
          return $result;
        } else {
          $error_string = $mysqli->errno . "\r\n" . $mysqli->error;
          error_log( date( "Y-m-d H:i:s" ) . " - " . $_SERVER[ 'REMOTE_ADDR' ] . "\r\n$error_string\r\n\r\n", 3, dirname( __FILE__ ) . "/error_database.log" );
          $mysqli->close();
          return false;
        }
      }
    }
  }

  /*
  Example database_select:
  $cols = array('id','login');
  $result = $this->database_select("tb_users", $cols, "`id`='123'");
  Or 
  $result = $this->database_select("tb_users", $cols);
  
  Return:
  false or query result
  
  Return example list
  while ($rows = $result->fetch_object()) {
  echo $rows->login."<br>";
  }
  $result->free();
  
  Return example one
  $row = $result->fetch_object();
  echo $row->login."<br>";
  $result->free();
  
  */
  protected function database_select( $table_name, $data, $index = false ) {
    $values = implode( ",", array_values( $data ) );
    if ( !$index ) {
      $query = "SELECT $values FROM `$table_name`";
    } else {
      $query = "SELECT $values FROM `$table_name` WHERE $index";
    }
    $result = $this->mysqlquery( $query );
    return $result;

  }

  /*
  Example database_insert:
  $data_insert = array('name' => "$name", 'status' => "pending");
  $result = $this->database_insert("tb_users", $data_insert); 
  
  Return:
  false or query result
  */
  protected function database_insert( $table_name, $data ) {
    $fields = implode( ',', array_keys( $data ) );
    $values = "'" . implode( "','", array_values( $data ) ) . "'";
    $query = "INSERT INTO $table_name ($fields) VALUES ($values)";
    $result = $this->mysqlquery( $query );
    return $result;
  }

  /*
  Example database_count:
  $total = $this->database_count("tb_users", "`id`='123'");
  Or 
  $total = $this->database_count("tb_users");
  */
  protected function database_count( $table_name, $index = false ) {
    if ( !$index ) {
      $query = "SELECT COUNT(*) AS total FROM `$table_name`";
    } else {
      $query = "SELECT COUNT(*) AS total FROM `$table_name` WHERE $index";
    }
    $result = $this->mysqlquery( $query );
    if ( $result ) {
      $obj = $result->fetch_object();
      $total = $obj->total;
      $result->free();
      return $total;
    } else {
      return false;
    }
  }

  /*
  Example database_sum:
  $sum = $this->database_sum("tb_users","balance", "`id`='123'");
  Or
  $sum = $this->database_sum("tb_users","balance");
  */
  protected function database_sum( $table_name, $field, $index = false ) {
    if ( !$index ) {
      $query = "SELECT SUM($field) AS total FROM `$table_name`";
    } else {
      $query = "SELECT SUM($field) AS total FROM `$table_name` WHERE $index";
    }
    $result = $this->mysqlquery( $query );
    if ( $result ) {
      $obj = $result->fetch_object();
      $total = $obj->total;
      $result->free();
      return $total;
    } else {
      return false;
    }
  }

  /*
  Example database_avg:
  $average = $this->database_avg("tb_users","balance", "`id`='123'");
  Or
  $average = $this->database_avg("tb_users","balance");
  */
  protected function database_avg( $table_name, $field, $index = false ) {
    if ( !$index ) {
      $query = "SELECT AVG($field) AS total FROM `$table_name`";
    } else {
      $query = "SELECT AVG($field) AS total FROM `$table_name` WHERE $index";
    }
    $result = $this->mysqlquery( $query );
    if ( $result ) {
      $obj = $result->fetch_object();
      $total = $obj->total;
      $result->free();
      return $total;
    } else {
      return false;
    }
  }

  /*
  Example database_update: 
  $data_update = array('name' => "$name", 'status' => "$status");
  $result = $this->database_update("tb_users", $data_update, "`id`='123'");
  Or 
  $result = $this->database_update("tb_users", $data_update);
	
  Return:
  false or query result
  */
  protected function database_update( $table_name, $data, $index = false ) {
    foreach ( $data as $key => $value ) {
      $i++;
      if ( $i > 1 ) {
        $fields .= ", `$key`='$value'";
      } else {
        $fields .= "`$key`='$value'";
      }
    }
    if ( !$index ) {
      $query = "UPDATE `$table_name` SET $fields";
    } else {
      $query = "UPDATE `$table_name` SET $fields WHERE $index";
    }
    $result = $this->mysqlquery( $query );
    return $result;
  }

  /*
  Example database_delete: 
  $result = $this->database_delete("tb_users", "`id`='123'"); 
  Or  
  $result = $this->database_delete("tb_users"); ** Delete everything from the table, care! **
  
  Return:
  false  or query result
  */
  protected function database_delete( $table_name, $index = false ) {
    if ( !$index ) {
      $query = "DELETE FROM `$table_name`";
    } else {
      $query = "DELETE FROM `$table_name` WHERE $index;";
    }
    $result = $this->mysqlquery( $query );
    return $result;
  }
}
?>