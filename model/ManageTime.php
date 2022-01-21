<?php
class ManageTime extends Auth {
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

  // cria um novo tempo / inicia um novo tempo
  // Api Public: NO
  private function post() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $started = @$Sanitizer->datetime( DATA[ 'started' ] );
      $ended = @$Sanitizer->datetime( DATA[ 'ended' ] );
      $description = @$Sanitizer->text( DATA[ 'description' ], 1025 );
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $userId ) ) {
          http_response_code( 422 );
          return array( "message" => "Usuário não informado." );
        } else {
          $userTotal = $this->database_count( "tb_users", "`userId`='$userId' AND `deleted`='0'" );
          if ( $userTotal != '1' ) {
            http_response_code( 422 );
            return array( "message" => "Usuário não encontrado." );
          } else {
            if ( strcasecmp( $userId, AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
              http_response_code( 403 );
              return array( "message" => "Usuário sem permissão para acessar este recurso." );
            } else {
              if ( empty( $projectId ) ) {
                http_response_code( 422 );
                return array( "message" => "Projeto não informado." );
              } else {
                $projectTotal = $this->database_count( "tb_projects", "`projectId`='$projectId' AND `deleted`='0'" );
                if ( $projectTotal != '1' ) {
                  http_response_code( 422 );
                  return array( "message" => "Projeto não encontrado." );
                } else {
                  $projectIsPart = $this->database_count( "tb_projects_users", "`projectId`='$projectId' AND `userId`='$userId' AND `deleted`='0'" );
                  if ( $projectTotal != '1' ) {
                    http_response_code( 422 );
                    return array( "message" => "Usuário não faz parte do projeto." );
                  } else {
                    if ( empty( $started ) ) {
                      http_response_code( 422 );
                      return array( "message" => "Data e hora inicial não foram informadas." );
                    } else {
                      if ( !$this->validateDate( $started, 'Y-m-d H:i:s' ) ) {
                        http_response_code( 422 );
                        return array( "message" => "Data ou hora inicial é inválida." );
                      } else {
                        if ( !empty( $ended ) && !$this->validateDate( $ended, 'Y-m-d H:i:s' ) ) {
                          http_response_code( 422 );
                          return array( "message" => "Data ou hora final é inválida." );
                        } else {
                          if ( !empty( $ended ) && $started >= $ended ) {
                            http_response_code( 422 );
                            return array( "message" => "Data e hora final não pode ser menor que a data e hora inicial." );
                          } else {
                            if ( strlen( $description ) > '1024' ) {
                              http_response_code( 422 );
                              return array( "message" => "A descrição não pode ter mais de 1024 caracteres." );
                            } else {
                              $datetime_started = strtotime( "$started" );
                              if ( empty( $ended ) ) {
                                $ended = NULL;
                                $datetime_ended = NULL;
                                $secs = '0';
                              } else {
                                $datetime_ended = strtotime( "$ended" );
                                $secs = round( $datetime_ended - $datetime_started );
                              }

                              $timeId = $Sanitizer->number( '3' . microtime( true ) . rand( 100, 9999 ), 55 );
                              $dateNow = date( 'Y-m-d H:i:s' );
                              $query = array();

                              $query[] = "INSERT INTO `tb_times` (`timeId`, `projectId`, `userId`, `started`, `ended`, `seconds`, `description`, `created`) VALUES ('$timeId', '$projectId', '$userId', '$started', '$ended', '$secs', '$description', '$dateNow');";
                              $result = $this->database_transaction( $query );
                              if ( !$result ) {
                                http_response_code( 500 );
                                return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
                              } else {
                                http_response_code( 200 );
                                $time = $this->timeData( $timeId );
                                return array( "time" => $time );
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }

  // exibe os dados de um tempo ou uma lista de tempos
  // permite listar tempos referente a um usuário ou um projeto
  // Api Public: NO
  private function get() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
      $timeId = @RESOURCES[ 'times' ];
      $started = @$Sanitizer->datetime( $_GET[ 'started' ] );
      $ended = @$Sanitizer->datetime( $_GET[ 'ended' ] );

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $projectId ) ) {
          $projectTotal = '0';
        } else {
          $projectTotal = $this->database_count( "tb_projects", "`projectId`='$projectId' AND `deleted`='0'" );
        }
        if ( empty( $userId ) ) {
          $userTotal = '0';
        } else {
          $userTotal = $this->database_count( "tb_users", "`userId`='$userId' AND `deleted`='0'" );
        }
        if ( empty( $timeId ) ) {
          $timeTotal = '0';
        } else {
          $timeTotal = $this->database_count( "tb_times", "`timeId`='$timeId' AND `deleted`='0'" );
        }

        if ( !empty( $projectId ) && $projectTotal != '1' ) {
          http_response_code( 422 );
          return array( "message" => "Projeto não encontrado." );
        } else {
          if ( !empty( $userId ) && $userTotal != '1' ) {
            http_response_code( 422 );
            return array( "message" => "Usuário não encontrado." );
          } else {
            if ( !empty( $timeId ) && $timeTotal != '1' ) {
              http_response_code( 422 );
              return array( "message" => "Registro de tempo não encontrado." );
            } else {

              if ( !empty( $userId ) && strcasecmp( $userId, AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                http_response_code( 403 );
                return array( "message" => "Usuário sem permissão para acessar este recurso." );
              } else {
                if ( !empty( $userId ) && !empty( $projectId ) ) {
                  $projectIsPart = $this->database_count( "tb_projects_users", "`projectId`='$projectId' AND `userId`='$userId' AND `deleted`='0'" );
                } else {
                  $projectIsPart = '0';
                }
                if ( !empty( $userId ) && !empty( $projectId ) && $projectIsPart != '1' && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                  http_response_code( 403 );
                  return array( "message" => "Usuário sem permissão para acessar este recurso." );
                } else {

                  if ( empty( $userId ) && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                    http_response_code( 403 );
                    return array( "message" => "Usuário sem permissão para acessar este recurso." );
                  } else {

                    if ( !empty( $timeId ) ) {
                      http_response_code( 200 );
                      $time = $this->timeData( $timeId );
                      return array( "time" => $time );
                    } else {

                      $sqlwhere = "`deleted`='0'";
                      if ( !empty( $userId ) ) {
                        $sqlwhere .= " AND `userId`='$userId'";
                      }
                      if ( !empty( $projectId ) ) {
                        $sqlwhere .= " AND `projectId`='$projectId'";
                      }
                      if ( $this->validateDate( $started, 'Y-m-d H:i:s' ) ) {
                        $sqlwhere .= " AND  `started`>='$started'";
                      }
                      if ( $this->validateDate( $ended, 'Y-m-d H:i:s' ) ) {
                        $sqlwhere .= " AND `ended`<='$ended'";
                      }

                      if ( empty( $page ) ) {
                        $page = '1';
                      } else {
                        $page = intval( $page );
                      }
                      $perpage = '10';

                      $result_rows = $this->mysqlquery( "SELECT COUNT(*) AS total FROM tb_times t1 WHERE $sqlwhere" );
                      $obj_rows = $result_rows->fetch_object();
                      $rows = $obj_rows->total;

                      $page_rows = $perpage;
                      $last = ceil( $rows / $page_rows );
                      if ( $last < 1 ) {
                        $last = 1;
                      }
                      if ( $page <= '0' ) {
                        $page = '1';
                      }
                      if ( $page > $last ) {
                        $page = $last;
                      }
                      $limit = 'LIMIT ' . ( $page - 1 ) * $page_rows . ',' . $page_rows;

                      $result = $this->mysqlquery( "SELECT timeId FROM tb_times t1 WHERE $sqlwhere ORDER BY created DESC $limit" );

                      $data = array();
                      $i = 0;
                      while ( $rowsLine = $result->fetch_object() ) {
                        $i++;
                        $data[ $i ] = $this->timeData( $rowsLine->timeId );
                      }
                      $result->free();
                      $nextpage = ( $page + 1 );
                      if ( $nextpage > $last ) {
                        $nextpage = '0';
                      }
                      if ( $nextpage <= '1' ) {
                        $nextpage = '0';
                      }
                      http_response_code( '200' );
                      return array(
                        'page' => number_format( $page, 0, ',', '.' ),
                        'pageRows' => number_format( $i, 0, ',', '.' ),
                        'totalPages' => number_format( $last, 0, ',', '.' ),
                        'nextPage' => $nextpage,
                        'times' => $data
                      );

                    }

                  }
                }
              }
            }
          }
        }

      }
    }
  }

  // edita um tempo / para informar uma data final
  // Api Public: NO
  private function put() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $ended = @$Sanitizer->datetime( DATA[ 'ended' ] );
      $description = @$Sanitizer->text( DATA[ 'description' ], 1024 );
      $timeId = @RESOURCES[ 'times' ];

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $timeId ) ) {
          http_response_code( 422 );
          return array( "message" => "ID do registro de tempo não foi informado." );
        } else {
          $timeTotal = $this->database_count( "tb_times", "`timeId`='$timeId' AND `deleted`='0'" );
          if ( $timeTotal != '1' ) {
            http_response_code( 422 );
            return array( "message" => "Registro de tempo não encontrado." );
          } else {
            $time = $this->timeData( $timeId );
            $projectId = $time[ 'projectId' ];
            $userId = $time[ 'userId' ];

            if ( strcasecmp( $userId, AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
              http_response_code( 403 );
              return array( "message" => "Usuário sem permissão para acessar este recurso." );
            } else {
              if ( !empty( $ended ) && !empty( $time[ 'ended' ] ) ) {
                http_response_code( 422 );
                return array( "message" => "Data e hora final não pode ser alterada." );
              } else {
                if ( !empty( $ended ) && !$this->validateDate( $ended, 'Y-m-d H:i:s' ) ) {
                  http_response_code( 422 );
                  return array( "message" => "Data ou hora final é inválida." );
                } else {

                  if ( !empty( $ended ) && $time[ 'started' ] >= $ended ) {
                    http_response_code( 422 );
                    return array( "message" => "Data e hora final não pode ser menor que a data e hora inicial." );
                  } else {
                    if ( strlen( $description ) > '1024' ) {
                      http_response_code( 422 );
                      return array( "message" => "A descrição não pode ter mais de 1024 caracteres." );
                    } else {
                      $started = $time[ 'started' ];
                      $datetime_started = strtotime( "$started" );
                      if ( empty( $ended ) ) {
                        if ( empty( $time[ 'ended' ] ) ) {
                          $ended = NULL;
                          $datetime_ended = NULL;
                          $secs = '0';
                        } else {
                          $ended = $time[ 'ended' ];
                          $datetime_ended = $time[ 'ended' ];
                          $secs = $time[ 'seconds' ];
                        }
                      } else {
                        $datetime_ended = strtotime( "$ended" );
                        $secs = round( $datetime_ended - $datetime_started );
                      }
                      $dateNow = date( 'Y-m-d H:i:s' );
                      $query = array();
                      $query[] = "UPDATE `tb_times` SET `deleted`='$dateNow' WHERE `timeId`='$timeId' AND `deleted`='0';";
                      $query[] = "INSERT INTO `tb_times` (`timeId`, `projectId`, `userId`, `started`, `ended`, `seconds`, `description`, `created`) VALUES ('$timeId', '$projectId', '$userId', '$started', '$ended', '$secs', '$description', '$dateNow');";
                      $result = $this->database_transaction( $query );
                      if ( !$result ) {
                        http_response_code( 500 );
                        return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
                      } else {
                        http_response_code( 200 );
                        $time = $this->timeData( $timeId );
                        return array( "time" => $time );
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }

  // apaga um tempo
  // Api Public: NO
  private function delete() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $timeId = @RESOURCES[ 'times' ];

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $timeId ) ) {
          http_response_code( 422 );
          return array( "message" => "ID do tempo não informado." );
        } else {
          $timeTotal = $this->database_count( "tb_times", "`timeId`='$timeId' AND `deleted`='0'" );
          if ( $timeTotal != '1' ) {
            http_response_code( 422 );
            return array( "message" => "Registro do tempo não encontrado." );
          } else {
            $time = $this->timeData( $timeId );
            if ( strcasecmp( $time[ 'userId' ], AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
              http_response_code( 403 );
              return array( "message" => "Usuário sem permissão para acessar este recurso." );
            } else {
              $dateNow = date( 'Y-m-d H:i:s' );
              $query = array();
              $query[] = "UPDATE `tb_times` SET `deleted`='$dateNow' WHERE `timeId`='$timeId' AND `deleted`='0';";
              $result = $this->database_transaction( $query );
              if ( !$result ) {
                http_response_code( 500 );
                return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
              } else {
                http_response_code( 200 );
                $project = $this->projectData( $projectId );
                return array( "message" => "Registro de tempo apagado." );
              }
            }
          }
        }

      }
    }
  }

  private function validateDate( $date, $format = 'Y-m-d H:i:s' ) {
    $datecreate = DateTime::createFromFormat( $format, $date );
    if ( $datecreate && $datecreate->format( $format ) == $date ) {
      return true;
    } else {
      return false;
    }
  }

  private function timeData( $timeId ) {
    $cols = array( 'timeId', 'projectId', 'userId', 'started', 'ended', 'seconds', 'description', 'created' );
    $result = $this->database_select( "tb_times", $cols, "`timeId`='$timeId' AND `deleted`='0'" );
    $row = ( array )$result->fetch_object();
    return $row;
  }
}
?>
