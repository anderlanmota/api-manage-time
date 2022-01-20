<?php
class Project extends Auth {
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

  // cria um novo projeto
  // Api Public: NO
  private function post() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $title = @$Sanitizer->alphanumeric( DATA[ 'title' ], true, true, 55 );
      $description = @$Sanitizer->text( DATA[ 'description' ], 1025 );
      $status = @strtolower( $Sanitizer->alphabetic( DATA[ 'status' ], false, false, 30 ) );
      $userId = @RESOURCES[ 'users' ];

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
              if ( empty( $title ) ) {
                http_response_code( 422 );
                return array( "message" => "O título do projeto não foi informado." );
              } else {
                if ( strlen( $title ) < '3' ) {
                  http_response_code( 422 );
                  return array( "message" => "O título do projeto precisa ter três ou mais caracteres." );
                } else {
                  if ( strlen( $description ) > '1024' ) {
                    http_response_code( 422 );
                    return array( "message" => "A descrição do projeto não pode ter mais de 1024 caracteres." );
                  } else {
                    if ( $status != "active" && $status != "inactive" ) {
                      http_response_code( 422 );
                      return array( "message" => "Status não permitido." );
                    } else {
                      $projectId = $Sanitizer->number( '2' . microtime( true ) . rand( 100, 9999 ), 55 );
                      $dateNow = date( 'Y-m-d H:i:s' );
                      $query = array();

                      $query[] = "INSERT INTO `tb_projects` (`projectId`, `userId`, `status`, `title`, `description`, `created`) VALUES ('$projectId', '$userId', '$status', '$title', '$description', '$dateNow');";
                      $query[] = "INSERT INTO `tb_projects_users` (`projectId`, `userId`, `created`) VALUES ('$projectId', '$userId', '$dateNow');";
                      $result = $this->database_transaction( $query );
                      if ( !$result ) {
                        http_response_code( 500 );
                        return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
                      } else {
                        http_response_code( 200 );
                        $project = $this->projectData( $projectId );
                        return array( "project" => $project );
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

  // exibe os dados de um projeto ou uma lista de projetos
  // permite listar projetos referente a um usuário
  // Api Public: NO
  private function get() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];
      $search = $Sanitizer->alphanumeric( @DATA[ 'search' ], true, true, 55 );
      $page = @$Sanitizer->number( $_GET[ 'page' ], 15 );

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $projectId ) ) {
          $projectTotal = '0';
        } else {
          $projectTotal = $this->database_count( "tb_projects", "`projectId`='$projectId' AND `deleted`='0'" );
        }
        if ( !empty( $projectId ) && $projectTotal != '1' ) {
          http_response_code( 422 );
          return array( "message" => "Projeto não encontrado." );
        } else {
          if ( !empty( $userId ) && !empty( $projectId ) ) {
            $projectIsPart = $this->database_count( "tb_projects_users", "`projectId`='$projectId' AND `userId`='$userId' AND `deleted`='0'" );
          } else {
            $projectIsPart = '0';
          }
          if ( !empty( $userId ) && !empty( $projectId ) && strcasecmp( $projectIsPart, '0' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
            http_response_code( 403 );
            return array( "message" => "Usuário sem permissão para acessar este recurso." );
          } else {
            if ( empty( $userId ) ) {
              $userTotal = '0';
            } else {
              $userTotal = $this->database_count( "tb_users", "`userId`='$userId' AND `deleted`='0'" );
            }
            if ( !empty( $userId ) && $userTotal != '1' ) {
              http_response_code( 422 );
              return array( "message" => "Usuário não encontrado." );
            } else {
              if ( !empty( $userId ) && strcasecmp( $userId, AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                http_response_code( 403 );
                return array( "message" => "Usuário sem permissão para acessar este recurso." );
              } else {
                if ( !empty( $projectId ) && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                  http_response_code( 403 );
                  return array( "message" => "Usuário sem permissão para acessar este recurso." );
                } else {

                  if ( !empty( $projectId ) ) {
                    http_response_code( 200 );
                    $project = $this->projectData( $projectId );
                    return array( "project" => $project );
                  } else {

                    $sqlwhere = "`deleted`='0'";
                    if ( !empty( $userId ) ) {
                      $sqlwhere .= " AND EXISTS (SELECT 1 FROM tb_projects_users t2 WHERE t2.projectId = t1.projectId AND userId='$userId' AND `deleted`='0')";
                    }
                    if ( strlen( $search ) >= '3' ) {
                      $sqlwhere .= " AND (`projectId` LIKE '%$search%' OR `status` LIKE '$search' OR `title` LIKE '%$search%')";
                    }

                    if ( empty( $page ) ) {
                      $page = '1';
                    } else {
                      $page = intval( $page );
                    }
                    $perpage = '10';

                    $result_rows = $this->mysqlquery( "SELECT COUNT(*) AS total FROM tb_projects t1 WHERE $sqlwhere" );
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

                    $result = $this->mysqlquery( "SELECT projectId FROM tb_projects t1 WHERE $sqlwhere ORDER BY created DESC $limit" );

                    $data = array();
                    $i = 0;
                    while ( $rowsLine = $result->fetch_object() ) {
                      $i++;
                      $data[ $i ] = $this->projectData( $rowsLine->projectId );
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
                      'projects' => $data
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

  // edita um projeto
  // Api Public: NO
  private function put() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $title = @$Sanitizer->alphanumeric( DATA[ 'title' ], true, true, 55 );
      $description = @$Sanitizer->text( DATA[ 'description' ], 1025 );
      $status = @strtolower( $Sanitizer->alphabetic( DATA[ 'status' ], false, false, 30 ) );
      $projectId = @RESOURCES[ 'projects' ];

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $projectId ) ) {
          http_response_code( 422 );
          return array( "message" => "ID do projeto não informado." );
        } else {
          $projectTotal = $this->database_count( "tb_projects", "`projectId`='$projectId' AND `deleted`='0'" );
          if ( $projectTotal != '1' ) {
            http_response_code( 422 );
            return array( "message" => "Projeto não encontrado." );
          } else {
            $project = $this->projectData( $projectId );
            if ( strcasecmp( $project[ 'userId' ], AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
              http_response_code( 403 );
              return array( "message" => "Usuário sem permissão para acessar este recurso." );
            } else {

              if ( empty( $title ) ) {
                http_response_code( 422 );
                return array( "message" => "O título do projeto não foi informado." );
              } else {
                if ( strlen( $title ) < '3' ) {
                  http_response_code( 422 );
                  return array( "message" => "O título do projeto precisa ter três ou mais caracteres." );
                } else {
                  if ( strlen( $description ) > '1024' ) {
                    http_response_code( 422 );
                    return array( "message" => "A descrição do projeto não pode ter mais de 1024 caracteres." );
                  } else {
                    if ( $status != "active" && $status != "inactive" && $status != "canceled" ) {
                      http_response_code( 422 );
                      return array( "message" => "Status não permitido." );
                    } else {
                      $userId = $project[ 'userId' ];
                      $dateNow = date( 'Y-m-d H:i:s' );
                      $query = array();
                      $query[] = "UPDATE `tb_projects` SET `deleted`='$dateNow' WHERE `projectId`='$projectId' AND `deleted`='0';";
                      $query[] = "INSERT INTO `tb_projects` (`projectId`, `userId`, `status`, `title`, `description`, `created`) VALUES ('$projectId', '$userId', '$status', '$title', '$description', '$dateNow');";

                      $result = $this->database_transaction( $query );
                      if ( !$result ) {
                        http_response_code( 500 );
                        return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
                      } else {
                        http_response_code( 200 );
                        $project = $this->projectData( $projectId );
                        return array( "project" => $project );
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

  // apaga um projeto
  // Api Public: NO
  private function delete() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $projectId ) ) {
          http_response_code( 422 );
          return array( "message" => "ID do projeto não informado." );
        } else {
          $projectTotal = $this->database_count( "tb_projects", "`projectId`='$projectId' AND `deleted`='0'" );
          if ( $projectTotal != '1' ) {
            http_response_code( 422 );
            return array( "message" => "Projeto não encontrado." );
          } else {
            $project = $this->projectData( $projectId );
            if ( strcasecmp( $project[ 'userId' ], AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
              http_response_code( 403 );
              return array( "message" => "Usuário sem permissão para acessar este recurso." );
            } else {
              $dateNow = date( 'Y-m-d H:i:s' );
              $query = array();
              $query[] = "UPDATE `tb_projects` SET `deleted`='$dateNow' WHERE `projectId`='$projectId' AND `deleted`='0';";
              $result = $this->database_transaction( $query );
              if ( !$result ) {
                http_response_code( 500 );
                return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
              } else {
                http_response_code( 200 );
                $project = $this->projectData( $projectId );
                return array( "message" => "Projeto apagado." );
              }
            }
          }
        }
      }
    }
  }

  // apaga um projeto
  // Api Public: NO
  private function patch() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $projectId ) ) {
          http_response_code( 422 );
          return array( "message" => "ID do projeto não informado." );
        } else {
          $projectTotal = $this->database_count( "tb_projects", "`projectId`='$projectId' AND `deleted`='0'" );
          if ( $projectTotal != '1' ) {
            http_response_code( 422 );
            return array( "message" => "Projeto não encontrado." );
          } else {
            if ( empty( $userId ) ) {
              http_response_code( 422 );
              return array( "message" => "ID do usuário não informado." );
            } else {
              $userTotal = $this->database_count( "tb_users", "`userId`='$userId' AND `deleted`='0'" );
              if ( $userTotal != '1' ) {
                http_response_code( 422 );
                return array( "message" => "Usuário não encontrado." );
              } else {
                $project = $this->projectData( $projectId );
                if ( strcasecmp( $project[ 'userId' ], AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                  http_response_code( 403 );
                  return array( "message" => "Usuário sem permissão para acessar este recurso." );
                } else {
                  $projectUserTotal = $this->database_count( "tb_projects_users", "`projectId`='$projectId' AND `userId`='$userId' AND `deleted`='0'" );
                  if ( $projectUserTotal > '0' ) {
                    http_response_code( 422 );
                    return array( "message" => "Usuário já faz parte do projeto." );
                  } else {
                    $dateNow = date( 'Y-m-d H:i:s' );
                    $query = array();
                    $query[] = "INSERT INTO `tb_projects_users` (`projectId`, `userId`, `created`) VALUES ('$projectId', '$userId', '$dateNow');";
                    $result = $this->database_transaction( $query );
                    if ( !$result ) {
                      http_response_code( 500 );
                      return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
                    } else {
                      http_response_code( 200 );
                      return array( "message" => "Usuário adicionado ao projeto." );
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

  // apaga um projeto
  // Api Public: NO
  private function unlink() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $projectId ) ) {
          http_response_code( 422 );
          return array( "message" => "ID do projeto não informado." );
        } else {
          $projectTotal = $this->database_count( "tb_projects", "`projectId`='$projectId' AND `deleted`='0'" );
          if ( $projectTotal != '1' ) {
            http_response_code( 422 );
            return array( "message" => "Projeto não encontrado." );
          } else {
            if ( empty( $userId ) ) {
              http_response_code( 422 );
              return array( "message" => "ID do usuário não informado." );
            } else {
              $userTotal = $this->database_count( "tb_users", "`userId`='$userId' AND `deleted`='0'" );
              if ( $userTotal != '1' ) {
                http_response_code( 422 );
                return array( "message" => "Usuário não encontrado." );
              } else {
                $project = $this->projectData( $projectId );
                if ( strcasecmp( $project[ 'userId' ], AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                  http_response_code( 403 );
                  return array( "message" => "Usuário sem permissão para acessar este recurso." );
                } else {
                  $projectUserTotal = $this->database_count( "tb_projects_users", "`projectId`='$projectId' AND `userId`='$userId' AND `deleted`='0'" );
                  if ( $projectUserTotal != '1' ) {
                    http_response_code( 422 );
                    return array( "message" => "Usuário não faz parte do projeto." );
                  } else {
                    $dateNow = date( 'Y-m-d H:i:s' );
                    $query = array();
                    $query[] = "UPDATE `tb_projects_users` SET `deleted`='$dateNow' WHERE `projectId`='$projectId' AND `userId`='$userId' AND `deleted`='0';";
                    $result = $this->database_transaction( $query );
                    if ( !$result ) {
                      http_response_code( 500 );
                      return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
                    } else {
                      http_response_code( 200 );
                      return array( "message" => "Usuário removido projeto." );
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

  private function projectData( $projectId ) {
    $cols = array( 'projectId', 'userId', 'status', 'title', 'description', 'created' );
    $result = $this->database_select( "tb_projects", $cols, "`projectId`='$projectId' AND `deleted`='0'" );
    $row = ( array )$result->fetch_object();
    return $row;
  }
}
?>
