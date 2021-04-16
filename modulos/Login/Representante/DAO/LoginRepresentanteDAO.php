<?php

/**
* @see Classe Responsável por realizar a persistencia  do 
* gerenciamento dos usuários tipo 'RT' representações.
*
* @package 'modulos/Login/Action'
* @author Alexandre
* @since 16/05/2016
*
*/
class LoginRepresentanteDAO  {
	
	/** @var Conexao Banco Intranet	*/
	private $conn;
		
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		global $conn;
		$this->conn = $conn;
	}
	
	/**
	 * 
	 * 
	 * @param string $login
	 * @param string $senha
	 * @return boolean|NULL
	 */
	public function autenticar($login, $senha){
		try {
	
			$strSQL = "SELECT
							*
						FROM
							usuarios
						WHERE
							ds_login = '$login'
						AND ds_senha = '$senha'
						AND dt_exclusao IS NULL";
				
			$result = pg_query($this->conn, $strSQL);
				
			if(pg_numrows($result)>0){
				return true;
			}
			return false;
				
	} catch (Exception $e) {
	pg_close($this->conn);
		$e->getMessage();
		return null;
	}
	}
	
	/**
	 * @see Buscar login representante
	 * 
	 * @param integer $cd_usuario
	 * @return NULL ou Objeto Usuario tabela usuarios
	 */
	public function buscarLogin($cd_usuario){
		try {
						
			$strSQL = "SELECT
							*
						FROM
							usuarios
						WHERE cd_usuario = '$cd_usuario'
						AND dt_exclusao IS NULL
						AND usurefoid IS NOT NULL
					";
			
			$result = pg_query($this->conn, $strSQL);
			
			if(pg_numrows($result)>0){
				return pg_fetch_object($result, 0);
			}
			return null;
			
		} catch (Exception $e) {
			pg_close($this->conn);
			$e->getMessage();
			return null;
		}
	}
		
	/**
	 * 
	 * @param integer $cd_usuario
	 * @param integer $novaSenhaCodificada
	 * @return boolean
	 */
	public function alterarSenha($cd_usuario, $novaSenhaCodificada){
		try {
			
			$str = "UPDATE usuarios
				SET ds_senha = (E'$novaSenhaCodificada')
			WHERE cd_usuario = $cd_usuario;";
			
			$rs = pg_query($this->conn, $str);
			
			if(pg_affected_rows($rs)==0){
				return false;
			}
			
			return true;
				
		} catch (Exception $e) {
			pg_close($this->conn);
			$e->getMessage();
			return false;
		}
	}
		
}