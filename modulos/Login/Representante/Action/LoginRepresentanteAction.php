<?php

require_once _MODULEDIR_ . 'Login/Representante/DAO/LoginRepresentanteDAO.php';
require_once _MODULEDIR_ . 'Login/Representante/VO/LoginRepresentanteVO.php';

/**
* @see Classe Responsável por gerenciar usuários tipo 'RT' representações.
* 
* - senha_portal_servicos.php
* 
*
* @package WS_Portal
* @author Alexandre
* @since 16/05/2016
*
*/
class LoginRepresentanteAction {

	/** @var LoginRepresentanteDAO */
	private $loginRepresentanteDAO;
	
	public static $MSG1 = array('COD' => 001, 'MSG' => "É NECESSÁRIO INFORMAR O PARAMETRO LOGIN");
	public static $MSG2 = array('COD' => 002, 'MSG' => "O PARAMETRO SENHA ATUAL NÃO PODE SER VAZIO OU NULO");
	public static $MSG3 = array('COD' => 003, 'MSG' => "O PARAMETRO SENHA ANTIGA NÃO PODE SER VAZIO OU NULO");
	public static $MSG4 = array('COD' => 004, 'MSG' => "FAVOR INSERIR O LOGIN DO CLIENTE");
	public static $MSG5 = array('COD' => 005, 'MSG' => "CÓDIGO DO CLIENTE DEVE SER NÚMERICO");
	public static $MSG6 = array('COD' => 006, 'MSG' => "LOGIN DO CLIENTE NÃO ENCONTRADO");
	public static $MSG7 = array('COD' => 007, 'MSG' => "ERRO AO ATUALIZAR LOGIN DO CLIENTE NA BASE SASWEB");
	public static $MSG8 = array('COD' => 008, 'MSG' => "PARA ATUALIZAR A SENHA E NECESSARIO INFORMAR LOGIN E SENHA VÁLIDOS");
	public static $MSG9 = array('COD' => 009, 'MSG' => "ERRO AO ALTERAR SENHA CLIENTE LOCALIZAÇÃO");
		
	public static $MSG10 = array('COD' => 010, 'MSG' => "SENHA ALTERADA COM SUCESSO");
	public static $MSG11 = array('COD' => 011, 'MSG' => "ERRO AO ALTERAR SENHA CONTATE O SUPORTE SASCAR");
	public static $MSG12 = array('COD' => 012, 'MSG' => "ERRO NA AUTENTICAÇÃO");
		
	/**	Construtor da classe */	
	public function __construct() {
		$this->loginRepresentanteDAO = new LoginRepresentanteDAO();
	}
	
	/**
	 * 
	 * @param unknown $login
	 * @param unknown $senha
	 * @throws Exception
	 * @return LoginRepresentanteVO
	 */
	public function autenticar($login, $senha){
		try {
			if($login == null || $login== ""){
				throw new Exception(self::$MSG8['MSG'], self::$MSG8['COD']);
			}
			if($senha == null || $senha== ""){
				throw new Exception(self::$MSG8['MSG'], self::$MSG8['COD']);
			}
			if($objRetorno = $this->loginRepresentanteDAO->autenticar($login, self::padronizarSenha($senha))){
				return new LoginRepresentanteVO(true, null, $objRetorno);
			}
				
			return new LoginRepresentanteVO(false, self::$MSG12['MSG'], null);
	
		} catch (Exception $e) {
			return new LoginRepresentanteVO(false, $e->getMessage(), null);
		}
	}
	
	/**
	 * 
	 * @param integer $cd_usuario
	 * @throws Exception
	 * @return LoginRepresentanteVO
	 */
	public function buscarLogin($cd_usuario){
		try {			
			if($cd_usuario != null || $cd_usuario!= ""){
				if(! is_numeric($cd_usuario)){
					throw new Exception(self::$MSG5['MSG'], self::$MSG5['COD']);
				}
			}
			if($objRetorno = $this->loginRepresentanteDAO->buscarLogin($cd_usuario)){
				return new LoginRepresentanteVO(true, null, $objRetorno);
			}
			
			return new LoginRepresentanteVO(false, self::$MSG6['MSG'], null);
				
		} catch (Exception $e) {
			return new LoginRepresentanteVO(false, $e->getMessage(), null);
		}
	}
	
	/**
	 * 
	 * @param integer $cd_usuario
	 * @param string $novaSenha
	 * @throws Exception
	 * @return LoginRepresentanteVO
	 */
	public function atualizarLogin($cd_usuario, $novaSenha) {
		try{
			
			if($cd_usuario == null || $cd_usuario == '')
				throw new Exception(self::$MSG1['MSG'], self::$MSG1['COD']);
			
			$objLoginVO = $this->buscarLogin($cd_usuario);
 			if(! $objLoginVO->retorno)
 				throw new Exception($objLoginVO->mensagem['MSG'], $objLoginVO->mensagem['COD']);
 										
			if($objRetorno = $this->loginRepresentanteDAO->alterarSenha($objLoginVO->objeto->cd_usuario, strtoupper(self::padronizarSenha($novaSenha)))){
				return new LoginRepresentanteVO(true, self::$MSG10['MSG'], NULL);
			}
			
			return new LoginRepresentanteVO(false, self::$MSG11['MSG'], NULL);
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
		
	/**
	 * @see Esse metodo criptografa a senha e faz os tratamentos necessários
	 * para não estourar a query.
	 * No caso das barras é feito para atender a insersão das informações no
	 * banco, pois a criptografia do portal para os RT utiliza alguns
	 * caracteres especiais que podem quebrar o script.
	 * 
	 * @param string $senha
	 * @return string
	 */
	private function padronizarSenha($senha){
		/** As senha devem ser todas maisculas */
		$senha = addslashes(strtoupper($senha));
		
		/** Realizar criptografia da senha */
		$senhaCriptografada = self::criptografaSenha($senha);
	
		/** tratar barras */
		$trocaEsse = trim('\ ');
		$porEsse =  trim('\ \ \ ');
		$trocaEsse = str_replace(' ', '', $trocaEsse);
		$porEsse = str_replace(' ', '', $porEsse);
		
		$buf = str_replace($trocaEsse, $porEsse, $senhaCriptografada);
		return $buf;
	}
	
	/**
	 *  Esse metodo criptografa a senha
	 * 
	 * @param string $senha
	 * @return string
	 */
	private function criptografaSenha($senha){
		$buf = "";
		for($x=0;$x<strlen($senha);$x++){
			$buf.=chr(ord($senha[$x]) + (strlen($senha) - $x));
		}
		return $buf;
	}
	
		
}