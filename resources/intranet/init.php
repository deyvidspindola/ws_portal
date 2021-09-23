<?php

//if ($_GET['specialcase'] == 1) { /* nesse caso, ? necess?rio "for?ar" a sess?o */
//  $my_session = $_GET['mysid'];
//  session_id($my_session);
//}
//error_reporting(ALL ^NOTICE);

//if((_PROTOCOLO_ == 'http://') && (!isset($_SERVER['HTTPS']))){
//	$urlRedirect = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
//	header("Location: $urlRedirect");
//}
session_start();

confere (autenticacao (), $loginpage, "" );
function cabecalho () {
   global $include_cabecalho;
   global $onLoad;

    // fim teste
   include ($PW."lib/cabecalho.php");
}

function protheus_integration_msg($title) {
  echo '<div class="modulo_titulo">' . $title . '</div>	
  <div class="modulo_conteudo">
      <div class="mensagem alerta" id="composicao">Devido à integração com o Totvs Protheus esta funcionalidade está desativada na Intranet.</div>
  </div>';
}

function debug_php($tipo,$mensagem,$variavel){
  /*
  DEBUG = 0 DESLIGADO
  DEBUG = 1 LIGADO
  DEBUG = 2 SOMENTE GRAVA NO ARQUIVO
  */

  /*Tipo DEBUG
    1 Querys
    2 Variaveis Importantes
    3 Variaveis Secundarias
    4 Informações Importantes
  */

  /* TIPO SAIDA
  1 - TELA
  2 - ARQUIVO
  */

  global $DEBUG;
  global $DEBUG_ARRAY;
  global $DEBUG_TIPO_SAIDA;
  global $DEBUG_ARQ_SAIDA;

  settype($DEBUG_ARRAY,"array");

  //VALIDAÇÕES
  $DEBUG_TIPO_SAIDA = ($DEBUG_TIPO_SAIDA!="" && $DEBUG_TIPO_SAIDA!=1 && $DEBUG_ARQ_SAIDA=="") ? $DEBUG_TIPO_SAIDA : 1 ;

  if($DEBUG==1 && $_SESSION['servidor_teste'] == 1 && in_array($tipo,$DEBUG_ARRAY) && $DEBUG_TIPO_SAIDA==1){
      echo "<div style=\"width:300px;\"><pre><font color=\"#FF0000\">DEBUG_PHP -> $mensagem </font> ";
      print_r($variavel);
      echo "</pre></div>";
  }
}

include($siteDir."lib/debug.php");
$debug= new DEBUG();
//Paginas que estão sem
$array_menu_ultimo_acessos=array();
$array_menu_ultimo_acessos=array("Relatorios");

//Gravando acessos por usuário

   $pacusuoid=$_SESSION[usuario][oid];
   $pacurl=$_SERVER['PHP_SELF'];
   $pacurl=substr($pacurl,1,100);
   $pacurl=trim($pacurl);
   $indice_ins=($pacusuoid%10);

   if($pacusuoid>0 && $pacurl!="" && $pacurl!="principal.php" ) {
   $sqlINS="insert into pagina_acesso$indice_ins (pacurl,pacusuoid) values ('$pacurl',$pacusuoid);";
   //pg_query($conn,$sqlINS);
   }

 /* if($pacusuoid==1467){
echo  $sqlINS;
  }*/

//Verifica se página tem acesso;
//$debug->mensagem_pagina_bloqueada();

//grava a data atual na tabela pagina
//$debug->ultimo_acesso_paginas();


// setado variavel para contar tempo de inicio da pagina
$debug->set_time_in();
?>