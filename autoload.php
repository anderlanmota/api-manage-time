<?php 
date_default_timezone_set('UTC');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: *');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// permite a verificação se está acessível sem processar todo o sistema
if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {    
        return 0;    
    }    
}
 
// antes de incluir um arquivo de uma classe instanciada, verifica se ele existe
spl_autoload_register(function ($classname) {  
  $classincluded = false;
  if (!$classincluded) {
    if (file_exists(dirname(__FILE__) . "/core/" . $classname . '.php')) {
      require_once dirname(__FILE__) . "/core/" . $classname . '.php';
      $classincluded = true;
    }
  }
  if (!$classincluded) {
    if (file_exists(dirname(__FILE__) . "/model/" . $classname . '.php')) {
      require_once dirname(__FILE__) . "/model/" . $classname . '.php';
      $classincluded = true;
    }
  }
});

// inclui o arquivo da versão solicitada
if (array_key_exists('PATH_INFO', $_SERVER)) {
	$elements = explode('/', $_SERVER['PATH_INFO']);
} else {
	
	
}
        if (!array_key_exists('1', $this->elements)) {
            $this->errormessage = "Acesso negado. Link inválido.";
        } else { 
			
		}



// inclui o arquivo principal dentro da pasta resource
require_once dirname(__FILE__) . "/resource/main.php";
?>