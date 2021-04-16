<?php

/**
 * description: Persistir alteraÁ„o de senha e buscar o usu·rio.
 * @author alexandre.reczcki
 *
 */

/** ImportaÁıes das Classes */
require 'modulos/TI/Model/TIUsuarioModel.class.php';

class TIResetSenhaDAO {
	
	/** @var string conx„o com o banco  */
	private $conn;
	
	/** @var TIUsuarioModel */
	private $usuario;
	
	/**
	 * Injeta a string de conex„o da intranet
	 * do config.php
	 */
	public function __construct() {
		global $conn;
		$this->conn = $conn;
		$this->usuario = new TIUsuarioModel();
	}
	
	/**
	 * 
	 * @param string $rs_nm_usuario - nm_usuario Nome do usu·rio 
	 * @param string $rs_ds_login 	- ds_login login do usu·rio.
	 * @throws Exception
	 * @return resource
	 */
	public function pesquisar($rs_nm_usuario, $rs_ds_login) {
		try {
			$sql = "SELECT 
						cd_usuario AS codigo,
						nm_usuario AS nome, 
						ds_login AS login, 
						dt_exclusao AS excluido, 
						usuemail AS email,
						(select count(1) from funcao_permissao_cargo where fpccargooid = usucargooid) as funcoes,
						(select count(1) from pagina_permissao_cargo where ppccargooid = usucargooid) as paginas,
						
						CASE WHEN
							usuacesso_externo = 'F'
							THEN 'N&Atilde;O'
							ELSE 'SIM'
						END AS externo,
					
						CASE WHEN
							dt_exclusao is null
							THEN 'Ativo'
                            ELSE '<font color=red>Inativo</font>'
						END AS ativo,

						CASE WHEN
							usubloqueado = 'F'
							THEN 'Desbloqueado'
							ELSE '<font color=red>Bloqueado</font>'
						END AS bloqueado,
						
						CASE WHEN
								usuloginseqad ilike '%.1.S1%' THEN 'SIM (SASCAR)'
							WHEN
								usuloginseqad ilike '%.1.S2%' THEN 'SIM (SMARTAGENDA)'
							ELSE 'N&Atilde;O'
						END AS ad
					
					FROM usuarios
					WHERE 1 = 1 --dt_exclusao IS NULL
					";
			
			if ($rs_nm_usuario != '' && $rs_ds_login != '') {
				$sql .= "AND nm_usuario iLIKE('%" . $rs_nm_usuario . "%') AND ds_login iLIKE('%" . $rs_ds_login . "%')";
								
			} else if ($rs_ds_login != '') {
				$sql .= "AND ds_login iLIKE ('%" . $rs_ds_login . "%')";
				
			} else {
				$sql .= "AND nm_usuario iLIKE ('%" . $rs_nm_usuario . "%')";
			}
			
			$result = pg_query($this->conn, $sql);
			
			if (! $result) {
				throw new Exception ( 'ERRO: <b>Falha ao listar dados do Usu√°rio.</b>' );
			} else {
				return $result;
			}
		} catch ( Exception $e ) {
			return $e->getMessage ();
		}
	}
	
	/**
	 * 
	 * 
	 * @param int $id_usuario
	 * @throws Exception
	 * @return multitype:
	 */
	public function pesquisarUsuario($id_usuario) {
		try {
			$sql = "SELECT 
						cd_usuario AS codigo, 
						nm_usuario AS nome, 
						ds_login AS login, 
						dt_exclusao AS excluido, 
						usuemail AS email,
						(select count(1) from funcao_permissao_cargo where fpccargooid = usucargooid) as funcoes,
						(select count(1) from pagina_permissao_cargo where ppccargooid = usucargooid) as paginas,
						
						CASE WHEN
							usuacesso_externo = 'F'
							THEN 'N&Atilde;O'
							ELSE 'SIM'
						END AS externo,
						
						CASE WHEN
							dt_exclusao is null
							THEN 'Ativo'
							ELSE '<font color=red>Inativo</font>'
						END AS ativo,
						
						CASE WHEN
							usubloqueado = 'F'
							THEN 'Desbloqueado'
							ELSE '<font color=red>Bloqueado</font>'
						END AS bloqueado,
						
						CASE WHEN
								usuloginseqad ilike '%.1.S1%' THEN 'SIM (SASCAR)'
							WHEN
								usuloginseqad ilike '%.1.S2%' THEN 'SIM (SMARTAGENDA)'
							ELSE 'N&Atilde;O'
						END AS ad
						
						FROM 
						usuarios
						WHERE
						-- dt_exclusao IS NULL
						
						-- AND 
						cd_usuario = $id_usuario";
						
			
			$result = pg_query($this->conn, $sql);
			$usuario = pg_fetch_array($result);
			
			if (! $result) {
				throw new Exception('ERRO: <b>Falha ao listar dados do Usu√°rio.</b>');
			} else {
				return $usuario;
			}
		} catch ( Exception $e ) {
			return $e->getMessage ();
		}
	}
	
	/**
	 * Description: Atualiza a senha e o email do usu·rio.
	 * @param int $id_usuario
	 * @param string $email
	 * @throws Exception
	 * 
	 * @return TIUsuarioModel
	 */
	public function resetarSenha($id_usuario, $email) {
		try {
			
			/** Validar existencia do usu·rio, e pegar o login do mesmo, para retorno. */
			$sqlLogin = "SELECT ds_login AS login, nm_usuario AS nome FROM usuarios WHERE dt_exclusao IS NULL AND cd_usuario = $id_usuario";
			
			$resultLogin = pg_query($this->conn, $sqlLogin);
			$ds_login = pg_fetch_array($resultLogin);
			
			if (! $resultLogin) {
				$this->usuario->mensagem = 'N„o foi possÌvel encontrar o usu·rio';
				throw new Exception('ERRO: <b>Falha ao listar dados do Usu√°rio.</b>');
			}
			$novaSenha = $this->gerarSenha($id_usuario);
			
			//TODO: VERIFICAR SE … NECESS¡RIO FAZER ESSE UPDATE
			
			$sql = "SELECT resetsenha('" . $ds_login['login'] . "','" . $email . "');";
			$result = @pg_query($this->conn, $sql);
			
			if (!$result) {
				$this->usuario->mensagem = pg_last_error($this->conn);
				throw new Exception ( '<b>N„o foi possivel resetar a senha do Usu·rio.<br/>'. pg_last_error($this->conn) . '</b>');
			}
		} catch ( Exception $e ) {
			return $e->getMessage ();
		}
		
		/** Setar TIUsuarioModel */
		$this->usuario->mensagem = '';
		$this->usuario->login = $ds_login['login'];
		$this->usuario->senha = $novaSenha;
		$this->usuario->email = $email;
		$this->usuario->nome = $ds_login['nome'];
		
		return $this->usuario;
	}
	
	/**
	 * Gerador de senha randomico chama a funÁ„o:
	 * SELECT resetsenha('".$codUsuario."'); 
	 * 
	 * @param unknown $codUsuario
	 * @return boolean|string
	 */
	private function gerarSenha($codUsuario) {
		try {
		
			/*
			$sql = "SELECT resetsenha('".$codUsuario."');";
			
			if(!pg_query($this->conn, $sql)){
				return false;
			}
			*/
			
			//TODO: PEGAR RETORNO DA PROCEDURE.
			return "12345";
		
		} catch (Excpetion $e) {
			return false;
		}
		
	}
	
}
