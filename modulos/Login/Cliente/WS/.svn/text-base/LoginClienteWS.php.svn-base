<?php

/**
* @see Classe Responsável por fazer integrações com WS
* dos Sistemas SASCAR, para efetuar a syncronia
* na alteração de senha, uma forma a mais
* de garantir a syncronia de forma mais ágil.
*
* @package 'modulos/Login/Action'
* @author Alexandre
* @since 16/05/2016
*
*/
class LoginClienteWS  {
				
	/** @var string método do reset de senha no SASWEB	 */
	private static $WS_RESETE_SENHA_SASWEB = "cadastroUsuario/resetSenhaIntranet";
	
	/** Construtor da classe */
	public function __construct(){}
	
	/**
	 * 
	 * @example
	 * $loginLocaliza = new LoginLocaliza();
	 * $loginLocaliza->atualizarUsuarioOracleREST($novo_usullogin, $senha_atual, $nova_senha);
	 * 
	 * @see Chamado no arquivo login_localizacao.php
	 * 
	 * @param string $login
	 * @param string $senhaAtual
	 * @param string $senhaAntiga
	 * 
	 * @return boolean
	 */
	public function atualizarUsuarioOracleREST($login, $senhaAntiga, $senhaAtual) {
		try {
			$params = "login=$login&senhaAntiga=$senhaAntiga&senhaAtual=$senhaAtual";
			$url = _WS_SASWEB_ . self::$WS_RESETE_SENHA_SASWEB;
					
			return $this->wsRest("POST", $url, $params);
			
		} catch (Exception $e) {
			$e->getMessage();
			return null;
		}
	}
	
	/**
	 * 
	 * @param string $method GET, POST, PUT, DELETE
	 * @param string $url caminho do app rest
	 * @param string $data parametros submetidos no REST
	 * 
	 * @return boolean
	 */
	private function wsRest($method, $url, $data = false) {
		try {
			switch($method)
			{
				case 'GET':
					if ($data)
						$url = sprintf("%s?%s", $url, http_build_query($data));
						$curl = curl_init($url);
						break;
				case 'POST':
					$curl = curl_init($url);
					curl_setopt($curl, CURLOPT_POST, true);
					if ($data)
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					break;
				case 'PUT':
					$curl = curl_init($url);
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
					if ($data)
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					break;
			}
			
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 2000);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
			
			$curl_response = curl_exec($curl);
			
			if ($curl_response === false) {
				$info = curl_getinfo($curl);
				curl_close($curl);
				return false;
			}
			
			curl_close($curl);
			$decoded = json_decode($curl_response);
			
			return true;
		} catch (Exception $e) {
			$e->getMessage();
			return null;
		} 
	}
	
}