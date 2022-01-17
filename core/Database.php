<?php
abstract class Database extends Sanitizer {
  /*
  Example:
  $query = "SELECT `id`, `login` FROM `tb_test` WHERE `id`='123';";
  $result = $this->mysqlquery($query);
  
  Return:
  false or query result
  */
  protected function mysqlquery( $query ) {
    $fileContents = file_get_contents( dirname( __FILE__ ) . "/../config/database.php" );
	eval($fileContents);
    //$server
    //$user
    //$password
    //$database
    //$port
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
  Example:
  $campos = array('id','login');
  $result = $this->database_select("tb_usuarios", $campos, "`id`='123'"); // busca com index
  OU 
  $result = $this->database_select("tb_usuarios", $campos); // busca sem index
  RETORNO:
  false ou resultado do query mysqli
  - Listar varios registros
  while ($rows = $result->fetch_object()) {
  echo $rows->login."<br>";
  }
  $result->free(); // libera a memoria
  - Listar um registro
  $row = $result->fetch_object();
  echo $row->login."<br>";
  $result->free(); // libera a memoria
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
  EXEMPLO [Insert]  
  $dados_insert = array('nome' => "$nome", 'status' => "pendente");
  $result = $this->database_insert("tb_usuarios", $dados_insert); 
  RETORNO:
  false ou resultado do query mysqli
  */
  protected function database_insert( $table_name, $data ) {
    $fields = implode( ',', array_keys( $data ) );
    $values = "'" . implode( "','", array_values( $data ) ) . "'";
    $query = "INSERT INTO $table_name ($fields) VALUES ($values)";
    $result = $this->mysqlquery( $query );
    return $result;
  }

  /*
  EXEMPLO [Count] 
  $result = $this->database_count("tb_usuarios", "`id`='123'"); // contar com index
  OU 
  $total = $this->database_count("tb_usuarios"); // contar sem index
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
  EXEMPLO [Sum] 
  $result = $this->database_sum("tb_usuarios","valor", "`id`='123'"); // somar com index
  OU 
  $total = $this->database_sum("tb_usuarios","valor"); // somar sem index
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
	EXEMPLO [Update] 
	$campos_update = array('nome' => "$nome", 'status' => "$status");
	$result = $this->database_update("tb_usuarios", $campos_update, "`id`='123'"); // alterar com index
	OU 
	$result = $this->database_update("tb_usuarios", $campos_update); // alterar sem index
	RETORNO:
	false ou resultado do query mysqli
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
  EXEMPLO [Delete] 
  $result = $this->database_delete("tb_usuarios", "`id`='123'"); // deletar com index
  OU 
  $result = $this->database_delete("tb_usuarios"); // deletar sem index (todos)
  RETORNO:
  false ou resultado do query mysqli
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