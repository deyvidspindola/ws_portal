<?php
/**
 * Carrega a Action responsável pelas requisições do módulo.
 * 
 */

//Carrega bibliotecas básicas
require_once "../../../../lib/config.php";
require_once "../../../../lib/init.php";

/**
 * Carrega a Action responsável pelas requisições do módulo.
 * 
 */ 
 
require_once _MODULEDIR_ ."Cadastro/Action/CadGerenciadora.php";

//Instancia a Action
$CadGerenciadora    = new CadGerenciadora();

/**
 * Chama a ação (método) da classe Acion dinâmicamente
 */

//Verifica ação geral 
$gerenciadora_acao = isset($_POST['gerenciadora_acao']) ? $_POST['gerenciadora_acao'] : (isset($_GET['gerenciadora_acao']) ? $_GET['gerenciadora_acao'] : '');
 
//Verifica se existe o parametro endpoint_acao no POST e GET
$endpoint_acao = isset($_POST['endpoint_acao']) ? $_POST['endpoint_acao'] : (isset($_GET['endpoint_acao']) ? $_GET['endpoint_acao'] : 'index');

// se for exclusão da gerenciadora, deve excluir aqui também, ignorando a acao endpoint
$endpoint_acao = ($gerenciadora_acao == "excluir"? $gerenciadora_acao : $endpoint_acao);

//Verifica se a ação existe no método. Caso contrário chama o metodo padrão
$endpoint_acao = method_exists($CadGerenciadora, $endpoint_acao) ? $endpoint_acao : 'index';

//Verifica se o metodo é public e pode ser chamado
$endpoint_acao = is_callable(array($CadGerenciadora, $endpoint_acao), false) ? $endpoint_acao : 'index';

//Chamada do metodo
$CadGerenciadora->$endpoint_acao();