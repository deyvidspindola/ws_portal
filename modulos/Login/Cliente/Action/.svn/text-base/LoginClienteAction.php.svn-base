<?php

include "lib/config.php";

require_once _MODULEDIR_ . 'Login/Cliente/DAO/LoginClienteDAO.php';
require_once _MODULEDIR_ . 'Login/Cliente/VO/LoginClienteVO.php';
require_once _MODULEDIR_ . 'Login/Cliente/WS/LoginClienteWS.php';


/**
* @see Classe Responsável por fazer integrações com WS
* dos Sistemas SASCAR, para efetuar a syncronia
* na alteração de senha, uma forma a mais
* de garantir a syncronia de forma mais ágil.
* 
* - login_localizacao.php
* - WS_Portal/recuperarSenha.php
* - WS_Portal/alterarSenha.php
* 
*
* @package WS_Portal
* @author Alexandre
* @since 16/05/2016
*
*/
class LoginClienteAction {

	/** @var LoginClienteDAO */
	private $loginClienteDAO;
	
	public static $MSG1 = array('COD' => 001, 'MSG' => "É NECESSÁRIO INFORMAR O PARAMETRO LOGIN");
	public static $MSG2 = array('COD' => 002, 'MSG' => "O PARAMETRO SENHA ATUAL NÃO PODE SER VAZIO OU NULO");
	public static $MSG3 = array('COD' => 003, 'MSG' => "O PARAMETRO SENHA ANTIGA NÃO PODE SER VAZIO OU NULO");
	public static $MSG4 = array('COD' => 004, 'MSG' => "FAVOR INSERIR O LOGIN DO CLIENTE");
	public static $MSG5 = array('COD' => 005, 'MSG' => "CÓDIGO DO CLIENTE DEVE SER NúMERICO");
	public static $MSG6 = array('COD' => 006, 'MSG' => "LOGIN DO CLIENTE NÃO ENCONTRADO");
	public static $MSG7 = array('COD' => 007, 'MSG' => "ERRO AO ATUALIZAR LOGIN DO CLIENTE NA BASE SASWEB");
	public static $MSG8 = array('COD' => 008, 'MSG' => "PARA ATUALIZAR A SENHA É NECESSÁRIO INFORMAR LOGIN E SENHA VÁLIDOS");
	public static $MSG9 = array('COD' => 009, 'MSG' => "ERRO AO ALTERAR SENHA CLIENTE LOCALIZAÇÃO");
		
	/**	Construtor da classe */	
	public function __construct() {
		$this->loginClienteDAO 		= new LoginClienteDAO();
		$this->loginClienteWS 		= new LoginClienteWS();

	}
	
	public function buscarLoginClienteIntranetPorLogin($login){
		try {
			if($login == null || $login == ''){
				throw new Exception(self::$MSG4['MSG'], self::$MSG4['COD']);
			}
			
			if($objRetorno = $this->loginClienteDAO->buscarLoginClienteIntranet($login)){
				return new LoginClienteVO(true, null, $objRetorno);
			}
			
			return new LoginClienteVO(false, self::$MSG6['MSG'], null);
			
		} catch (Exception $e) {
			$e->getMessage();
			return null;
		}
	}
	
	public function buscarLoginClienteAVL($clioid, $login){
		try {
			
			if(($clioid == null || $clioid== "") && ($login == null || $login== "")){
				throw new Exception(self::$MSG5['MSG'], self::$MSG5['COD']);
			}
			
			if(($login != null || $login != "")){
				$login = strtoupper($login);
			}
			
			if($clioid != null || $clioid!= ""){
				if(! is_numeric($clioid)){
					throw new Exception(self::$MSG5['MSG'], self::$MSG5['COD']);
				}
			}
						
			if($objRetorno = $this->loginClienteDAO->buscarLoginClienteAVL($clioid, $login)){
				return new LoginClienteVO(true, null, $objRetorno);
			}
			
			return new LoginClienteVO(false, self::$MSG6['MSG'], null);
				
		} catch (Exception $e) {
			//return $e;
			return new LoginClienteVO(false, $e->getMessage(), null);
		}
	}
	
	
	public function atualizarLoginClienteAVL($usullogin, $novaSenha) {
		try{
			
			if($usullogin == null || $usullogin == '')
				throw new Exception(self::$MSG1['MSG'], self::$MSG1['COD']);
				
			if($novaSenha == null || $novaSenha == '')
				throw new Exception(self::$MSG2['MSG'], self::$MSG2['COD']);
			
			$objLoginVO = $this->buscarLoginClienteAVL(null, $usullogin);
 			if(! $objLoginVO->retorno)
 				throw new Exception($objLoginVO->mensagem['MSG'], $objLoginVO->mensagem['COD']);
 										
			if($objRetorno = $this->loginClienteDAO->alterarSenhaClienteAvl($objLoginVO->objeto->logoid, strtoupper(self::padronizarSenhaAvl($novaSenha)))){
				return new LoginClienteVO(true, null, null);
			}
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * 
	 * @example
	 * $loginClienteWS = new LoginLocaliza();
	 * $loginClienteWS->atualizarUsuarioOracleREST($novo_usullogin, $senha_atual, $nova_senha);
	 * 
	 * @see Chamado no arquivo: 
	 * 
	 * @param string $login
	 * @param string $senhaAtual
	 * @param string $senhaAntiga
	 * @return boolean
	 */
	public function atualizarUsuarioOracleREST($login, $senhaAntiga, $senhaAtual) {
		try {
			
			if($login == null || $login == '')
				throw new Exception(self::$MSG1['MSG'], self::$MSG1['COD']);
			
			if($senhaAtual == null || $senhaAtual == '')
				throw new Exception(self::$MSG2['MSG'], self::$MSG2['COD']);
			
			if($senhaAntiga == null || $senhaAntiga == '')
				throw new Exception(self::$MSG3['MSG'], self::$MSG3['COD']);
			
			
			if($objRetorno = $this->loginClienteWS->atualizarUsuarioOracleREST($login, $senhaAntiga, $senhaAtual)){
				return new LoginClienteVO(true, null, $objRetorno);
			}
				
			return new LoginClienteVO(false, self::$MSG7['MSG'], null);
			
		} catch (Exception $e) {
			$e->getMessage();
			return null;
		}
	}
	
	private static function padronizarSenhaAvl($senha){
		return strtoupper(md5($senha));
	}
	
}