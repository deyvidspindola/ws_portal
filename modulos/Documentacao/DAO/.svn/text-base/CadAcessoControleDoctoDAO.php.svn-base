<?php
class CadAcessoControleDoctoDAO {
    
    private $conn;
    
    public function __construct() {
        global $conn;

        $this->conn = $conn;
    }

	// Busca e insere na versão pendente
	public function getVersaoPendente($itaitoid){
		
		$sqlVersao = "SELECT itoid, itversao 
					  FROM instr_trabalho 
					  WHERE itoid_original = $itaitoid and itstatus = 'P'";
		
		$queryVersao  = pg_query($this->conn, $sqlVersao);
		$resultVersao = pg_fetch_all($queryVersao);
		
		return $resultVersao[0];
	}
	
	// Confere permissões em versões pendentes
	public function getPermissaoVersao($itaoid){

		$sql_Versao  = "select 
							itoid, 
							itoid_original, 
							itaprhoid, 
							itversao
						from 
							instr_trabalho
							inner join instr_trabalho_acesso ON itaitoid = itoid_original
						where 
							itaoid = $itaoid
							and itstatus = 'P'";
		
		$queryVersao  = pg_query($this->conn, $sql_Versao);
		$acessoVersao = pg_fetch_all($queryVersao);
		
		return $acessoVersao[0];
	}

	public function getRegistros($itaitoid, $itaprhoid, $tipoAcesso = "V"){
	
		$sql_ita = "SELECT itaoid, itatipo_acesso
		FROM instr_trabalho_acesso
		WHERE itaitoid = $itaitoid
		AND itaprhoid = $itaprhoid
		AND itatipo_acesso = '" . $tipoAcesso . "'; ";
	
		$query = pg_query($this->conn, $sql_ita);
	
		return $query;
	}
	
	public function setInsertAcessoCargo($itaitoid, $itaprhoid, $tipoAcesso){
		
		$sql_ins = "INSERT INTO instr_trabalho_acesso (itaitoid,itaprhoid,itatipo_acesso)
					VALUES ($itaitoid, $itaprhoid, '$tipoAcesso')
					RETURNING itaoid; ";

		if (!$res_ins = pg_query($this->conn, $sql_ins)){
			return false;
		}

		return true;
	}

	public function setUpdateAcessoCargo($itaoid, $tipoAcesso){
	
		$sql_upd = "UPDATE instr_trabalho_acesso
					SET itatipo_acesso = '$tipoAcesso'
					WHERE itaoid = $itaoid; ";

		if (!$res_upd = pg_query($this->conn, $sql_upd)){
			return false;
		}

		return true;
	}

	public function updateVersaoPendente($itoid, $itaprhoid, $tipoAcesso){
	
		// Atualiza Versão Pendente
		$updateVersao = "UPDATE instr_trabalho_acesso SET itatipo_acesso = '$tipoAcesso'
						 where 
							itaitoid = ".$itoid." 
							and itaprhoid = ".$itaprhoid;

		if (!$res_upd = pg_query($this->conn, $updateVersao)){
			return false;
		}

		return true;
	}
	
	public function setInsertAcessoVersao($itaitoid, $itaprhoid, $tipoAcesso){
		
		// Busca e insere na versão pendente
		$resultVersao = $this->getVersaoPendente($itaitoid);

		if($resultVersao != null){

			$sql_ins = "INSERT INTO instr_trabalho_acesso (itaitoid,itaprhoid,itatipo_acesso)
						VALUES (".$resultVersao['itoid'].", $itaprhoid, '$tipoAcesso')
						RETURNING itaoid; ";

			if (!$res_ins = pg_query($this->conn, $sql_ins)){
				return $resultVersao['itversao'];
			}
		}

		return false;
	}
	
	public function setUpdateAcessoVersao($itaoid, $tipoAcesso){
		
		// Busca e insere na versão pendente
		$resultVersao = $this->getPermissaoVersao($itaoid);	
		if(sizeof($resultVersao) > 0){
			$updateVersao = $this->updateVersaoPendente($resultVersao['itoid'], $resultVersao['itaprhoid'], $tipoAcesso);
			
			if(!$updateVersao){
				return $resultVersao['itversao'];
			}
		}

		return false;
	}
	
	public function excluirPermissaoVersao($itoid, $itaprhoid){
	
		$sql = "DELETE FROM instr_trabalho_acesso 
				WHERE 
					itaitoid = ".$itoid." 
					and itaprhoid = ".$itaprhoid;
						
		if (!pg_query($this->conn, $sql)){
			return false;
		}
		
		return true;
	}

	public function excluirPermissao($itaoid){
	
		$sql = "DELETE FROM instr_trabalho_acesso
				WHERE itaoid = $itaoid";

		if (!pg_query($this->conn, $sql)){
			return false;
		}

		return true;
	}

}