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
class LoginClienteDAO  {
	
	/** @var Conexao Banco Intranet	*/
	private $conn;
	
	/** @var Conexao Banco AVL */
	private $connAvl;
	
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		global $conn;
		$this->conn = $conn;
	}
			
	public function buscarLoginClienteIntranet($login){
		try {
						
			$strSQL = " SELECT * FROM usuario_localizacao 
						WHERE usulexclusao is null
						AND usullogin = '$login';
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
	
	public function buscarLoginClienteAVL($clioid, $login){
		try {
			$this->connAvl = self::getConnectAVL();
			
			$str = "SELECT
						*
					FROM login
						INNER JOIN cliente ON (logclioid = clioid)
					WHERE logexclusao IS NULL
					AND loglogin = 'ADM'";
			
			if($clioid != '' || $clioid != null){
				$str .= " AND logclioid = '$clioid'";
			}
			
			if($login != '' || $login != null){
				$str .= " AND clilogin = '$login'";
			}

			$result = pg_query($this->connAvl, $str);
			
			if(pg_numrows($result)>0){
				return pg_fetch_object($result, 0);
			}
			
			return null;
									
		} catch (Exception $e) {
			pg_close($this->connAvl);
			$e->getMessage();
			return null;
		}
	}

	public function alterarSenhaClienteAvl($logoid, $nova_senha_encriptada){
		try {
			$this->connAvl = self::getConnectAVL();
				
			$str = "UPDATE login
			SET logsenha = '$nova_senha_encriptada',
			logtrocarsenha = 'f'
			WHERE logoid = '$logoid'";
			
			$rs = pg_query($this->connAvl, $str);
			
			if(pg_affected_rows($rs)==0){
				return false;
			}
			
			return true;
				
		} catch (Exception $e) {
			pg_close($this->connAvl);
			$e->getMessage();
			return null;
		}
	}
	
	private static function getConnectAVL(){
		global $dbstring_avl;
		return pg_connect($dbstring_avl);
	}
	
}