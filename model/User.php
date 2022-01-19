<?php
class User extends Auth {
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

  // cria um novo usuário
  // Api Public: YES
  private function post() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $name = @$Sanitizer->alphanumeric( DATA[ 'name' ], true, true, 55 );
      $email = @$Sanitizer->email( DATA[ 'email' ] );
      $login = @strtolower( $Sanitizer->alphanumeric( DATA[ 'login' ], false, false, 35 ) );
      $password = DATA[ 'password' ];

      if ( empty( $name ) ) {
        http_response_code( 422 );
        return array( "message" => "O nome não foi informado." );
      } else {
        if ( empty( $email ) ) {
          http_response_code( 422 );
          return array( "message" => "O e-mail não foi informado." );
        } else {
          if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            http_response_code( 422 );
            return array( "message" => "O e-mail parece ser inválido." );
          } else {
            if ( empty( $login ) ) {
              http_response_code( 422 );
              return array( "message" => "O login não foi informado." );
            } else {
              if ( strlen( $login ) > '30' ) {
                http_response_code( 422 );
                return array( "message" => "O login não pode ter mais de trinta caracteres." );
              } else {
                if ( strlen( $login ) < '3' ) {
                  http_response_code( 422 );
                  return array( "message" => "O login não pode ter menos de três caracteres." );
                } else {
                  if ( strlen( $login ) != strlen( DATA[ 'login' ] ) ) {
                    http_response_code( 422 );
                    return array( "message" => "O login pode ter apenas letras e números. Não é permitido espaços ou caracteres especiais." );
                  } else {
                    $totalLogin = $this->database_count( "tb_users", "`login`='$login' AND `deleted`='0'" );
                    if ( $totalLogin > '0' ) {
                      http_response_code( 422 );
                      return array( "message" => "O login $login já está em uso." );
                    } else {
                      if ( empty( $password ) ) {
                        http_response_code( 422 );
                        return array( "message" => "A senha não foi informada." );
                      } else {
                        if ( strlen( $password ) < '6' ) {
                          http_response_code( 422 );
                          return array( "message" => "A senha não pode ter menos de seis caracteres." );
                        } else {
                          if ( strlen( $password ) > '30' ) {
                            http_response_code( 422 );
                            return array( "message" => "A senha não pode ter mais de trinta caracteres." );
                          } else {
                            $dateNow = date( 'Y-m-d H:i:s' );
                            $passwordHash = hash( 'sha256', $password );
                            $userId = $Sanitizer->number( microtime( true ) . rand( 100, 9999 ), 55 );
                            $data_insert = array( 'userId' => "$userId", 'login' => "$login", 'status' => "pending", 'name' => "$name", 'email' => "$email", 'password' => "$passwordHash", 'created' => "$dateNow" );
                            $result = $this->database_insert( "tb_users", $data_insert );
                            if ( !$result ) {
                              http_response_code( 500 );
                              return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
                            } else {
                              http_response_code( 200 );
                              $user = $this->userData( $userId, $userId, 'user' );
                              return array( "user" => $user );
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

  // exibe os dados de um usuário ou uma lista de usuários
  // permite listar usuários referente a um projeto
  // Api Public: NO
  private function get() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $search = @$Sanitizer->alphanumeric( DATA[ 'search' ], true, true, 55 );
      $page = @$Sanitizer->number( $_GET[ 'page' ], 15 );
      $userId = @RESOURCES[ 'users' ];
      $projectId = @RESOURCES[ 'projects' ];

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
          http_response_code( 405 );
          return array( "message" => "Projeto não encontrado." );
        } else {
          if ( empty( $userId ) && empty( $projectId ) && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
            http_response_code( 403 );
            return array( "message" => "Usuário sem permissão para acessar este recurso." );
          } else {
            $projectTotalIsPart = $this->database_count( "tb_projects_users", "`projectId`='$projectId' AND `userId`='" . AUTH[ 'userId' ] . "' AND `deleted`='0'" );
            if ( empty( $userId ) && strcasecmp( $projectTotalIsPart, '1' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
              http_response_code( 403 );
              return array( "message" => "Usuário sem permissão para acessar este recurso." );
            } else {
              if ( !empty( $userId ) && strcasecmp( $userId, AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                http_response_code( 403 );
                return array( "message" => "Usuário sem permissão para acessar este recurso." );
              } else {
                if ( !empty( $userId ) && !empty( $projectId ) ) {
                  http_response_code( 405 );
                  return array( "message" => "A requisição é inválida." );
                } else {
                  if ( !empty( $userId ) ) {
                    $userTotal = $this->database_count( "tb_users", "`userId`='$userId' AND `deleted`='0'" );
                    if ( $userTotal != '1' ) {
                      http_response_code( 405 );
                      return array( "message" => "Usuário não encontrado." );
                    } else {
                      $user = $this->userData( $userId, AUTH[ 'userId' ], AUTH[ 'role' ] );
                      return $user;
                    }
                  } else {
                    $sqlwhere = "`deleted`='0'";
                    if ( !empty( $projectId ) ) {
                      $sqlwhere .= " AND EXISTS (SELECT 1 FROM tb_projects_users t2 WHERE t2.userId = t1.userId AND projectId='$projectId' AND `deleted`='0')";
                    }
                    if ( strlen( $search ) >= '3' ) {
                      $sqlwhere .= " AND (`userId` LIKE '%$search%' OR `login` LIKE '%$search%' OR `email` LIKE '%$search%')";
                    }

                    if ( empty( $page ) ) {
                      $page = '1';
                    } else {
                      $page = intval( $page );
                    }
                    $perpage = '10';

                    $result_rows = $this->mysqlquery( "SELECT COUNT(*) AS total FROM tb_users t1 WHERE $sqlwhere" );
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

                    $result = $this->mysqlquery( "SELECT userId FROM tb_users t1 WHERE $sqlwhere ORDER BY created DESC $limit" );

                    $data = array();
                    $i = 0;
                    while ( $rowsLine = $result->fetch_object() ) {
                      $i++;
                      $data[ $i ] = $this->userData( $rowsLine->userId, AUTH[ 'userId' ], AUTH[ 'role' ] );
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
                      'users' => $data
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

  // edita um usuário
  // Api Public: NO
  private function put() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $Sanitizer = new Sanitizer();
      $name = @$Sanitizer->alphanumeric( DATA[ 'name' ], true, true, 55 );
      $email = @$Sanitizer->email( DATA[ 'email' ] );
      $login = @strtolower( $Sanitizer->alphanumeric( DATA[ 'login' ], false, false, 35 ) );
      $password = DATA[ 'password' ];
      $userId = @RESOURCES[ 'users' ];
      $role = @strtolower( $Sanitizer->alphanumeric( DATA[ 'role' ], false, false, 35 ) );
      $status = @strtolower( $Sanitizer->alphanumeric( DATA[ 'status' ], false, false, 35 ) );

      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $userId ) ) {
          http_response_code( 405 );
          return array( "message" => "O ID do usuário não informado." );
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
              if ( empty( $name ) ) {
                http_response_code( 422 );
                return array( "message" => "O nome não foi informado." );
              } else {
                if ( empty( $email ) ) {
                  http_response_code( 422 );
                  return array( "message" => "O e-mail não foi informado." );
                } else {
                  if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                    http_response_code( 422 );
                    return array( "message" => "O e-mail parece ser inválido." );
                  } else {
                    if ( empty( $login ) ) {
                      http_response_code( 422 );
                      return array( "message" => "O login não foi informado." );
                    } else {
                      if ( strlen( $login ) > '30' ) {
                        http_response_code( 422 );
                        return array( "message" => "O login não pode ter mais de trinta caracteres." );
                      } else {
                        if ( strlen( $login ) < '3' ) {
                          http_response_code( 422 );
                          return array( "message" => "O login não pode ter menos de três caracteres." );
                        } else {
                          if ( strlen( $login ) != strlen( DATA[ 'login' ] ) ) {
                            http_response_code( 422 );
                            return array( "message" => "O login pode ter apenas letras e números. Não é permitido espaços ou caracteres especiais." );
                          } else {
                            $totalLogin = $this->database_count( "tb_users", "`login`='$login' AND `deleted`='0' AND `userId`!='$userId'" );
                            if ( $totalLogin > '0' ) {
                              http_response_code( 422 );
                              return array( "message" => "O login $login já está em uso." );
                            } else {

                              if ( !empty( $password ) && strlen( $password ) < '6' ) {
                                http_response_code( 422 );
                                return array( "message" => "A senha não pode ter menos de seis caracteres." );
                              } else {
                                if ( !empty( $password ) && strlen( $password ) > '30' ) {
                                  http_response_code( 422 );
                                  return array( "message" => "A senha não pode ter mais de trinta caracteres." );
                                } else {
                                  if ( !empty( $role ) && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                                    http_response_code( 401 );
                                    return array( "message" => "Usuário sem permissão para alterar a função." );
                                  } else {
                                    if ( !empty( $status ) && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
                                      http_response_code( 401 );
                                      return array( "message" => "Usuário sem permissão para alterar o status." );
                                    } else {
                                      if ( !empty( $role ) && $role != "user" && $role != "admin" ) {
                                        http_response_code( 422 );
                                        return array( "message" => "A função do usuário é inválida. Valores permitidos: user, admin" );
                                      } else {
                                        if ( !empty( $status ) && $status != "pending" && $status != "active" && $status != "suspended" ) {
                                          http_response_code( 422 );
                                          return array( "message" => "O status é inválido. Valores permitidos: pending, active, suspended" );
                                        } else {
                                          $userCurrent = $this->userData( $userId, $userId, AUTH[ 'role' ] );

                                          if ( !empty( $password ) ) {
                                            $passwordHash = hash( 'sha256', $password );
                                          } else {
                                            $passwordHash = $userCurrent[ 'password' ];
                                          }

                                          if ( empty( $role ) ) {
                                            $role = $userCurrent[ 'role' ];
                                          }

                                          if ( empty( $status ) ) {
                                            $status = $userCurrent[ 'status' ];
                                          }

                                          $dateNow = date( 'Y-m-d H:i:s' );
                                          $query = array();
                                          $query[] = "UPDATE `tb_users` SET `deleted`='$dateNow' WHERE `userId`='$userId' AND `deleted`='0';";

                                          $query[] = "INSERT INTO `tb_users` (`userId`, `role`, `login`, `status`, `name`, `email`, `password`, `created`, `deleted`) VALUES ('" . $userCurrent[ 'userId' ] . "', '$role', '$login', '$status', '$name', '$email', '$passwordHash', '$dateNow');";
                                          $result = $this->mysqlquery( $query );
                                          if ( !$result ) {
                                            http_response_code( 500 );
                                            return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
                                          } else {
                                            http_response_code( 200 );
                                            return array( "message" => "Cadastro realizado com sucesso." );
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
            }
          }
        }
      }
    }
  }

  // apaga um usuário
  // Api Public: NO
  private function delete() {
    $checkPermission = $this->checkPermission();
    if ( $checkPermission[ 'responseCode' ] != '200' ) {
      http_response_code( $checkPermission[ 'responseCode' ] );
      return array( "message" => $checkPermission[ 'message' ] );
    } else {
      $userId = @RESOURCES[ 'users' ];
      if ( strcasecmp( AUTH[ 'role' ], 'user' ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
        http_response_code( 401 );
        return array( "message" => "A solicitação não foi autorizada." );
      } else {
        if ( empty( $userId ) ) {
          http_response_code( 405 );
          return array( "message" => "O ID do usuário não informado." );
        } else {
          $userTotal = $this->database_count( "tb_users", "`userId`='$userId' AND `deleted`='0'" );
          if ( $userTotal != '1' ) {
            http_response_code( 405 );
            return array( "message" => "Usuário não foi encontrado." );
          } else {
            if ( strcasecmp( $userId, AUTH[ 'userId' ] ) != 0 && strcasecmp( AUTH[ 'role' ], 'admin' ) != 0 ) {
              http_response_code( 403 );
              return array( "message" => "Usuário sem permissão para acessar este recurso." );
            } else {
              $dateNow = date( 'Y-m-d H:i:s' );
              $query = array();
              $query[] = "UPDATE `tb_users` SET `deleted`='$dateNow' WHERE `userId`='$userId' AND `deleted`='0';";
              $result = $this->mysqlquery( $query );
              if ( !$result ) {
                http_response_code( 500 );
                return array( "message" => "Erro interno. Por favor, tente novamente mais tarde." );
              } else {
                http_response_code( 200 );
                return array( "message" => "O usuário foi excluído." );
              }
            }
          }
        }
      }
    }
  }

  private function userData( $userId, $userIdRequest, $role ) {
    if ( $userId == $userIdRequest ) {
      $cols = array( 'userId', 'role', 'login', 'status', 'name', 'email', 'created' );
    } else {
      if ( $role == "admin" ) {
        $cols = array( 'userId', 'role', 'login', 'status', 'name', 'email', 'created' );
      } else {
        $cols = array( 'userId', 'name' );
      }
    }
    $result = $this->database_select( "tb_users", $cols, "`userId`='$userId' AND `deleted`='0'" );
    $row = ( array )$result->fetch_object();
    return $row;
  }
}
?>
