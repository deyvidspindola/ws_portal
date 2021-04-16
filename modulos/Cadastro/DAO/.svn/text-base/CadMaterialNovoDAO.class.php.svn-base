<?php

class CadMaterialNovoDAO {
	
	private $conn;
	private $cd_usuario;
	
	public function __construct($conexao = '') {        
		global $conn;
		$this->conn = $conn;
		$this->cd_usuario = $_SESSION['usuario']['oid'];    
	}

	public function consultaGrupoSimilar(){

		$sql = "SELECT grsoid, grsgrupo FROM grupo_similaridade WHERE grsdt_exclusao is null ORDER BY grsgrupo";
		
		$rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        if(pg_num_rows($rs) > 0 ){
            $result = pg_fetch_all($rs);
        }
        
        return $result;

	}

	public function salvarGrupoSimilar($params){

		try {

			$sql = "SELECT grsdt_exclusao FROM grupo_similaridade WHERE grsgrupo = '".$params['grsgrupo']."'";
			$rs = pg_query($this->conn, $sql);
			
			if(trim($params['grsgrupo']) == ''){
				throw new Exception ("Informe um nome para o grupo similar");
			}

			if(pg_num_rows($rs) > 0){
				$dados = pg_fetch_assoc($rs);
				if($dados['grsdt_exclusao'] != ''){
					throw new Exception ("Já existe um Grupo Similar cadastrado com esta descrição, porém o mesmo encontra-se excluído");
				}else{
					throw new Exception ("Já existe um Grupo Similar cadastrado com esta descrição");	
				}				
			}

			if($params['grsoid'] != ""){
				$sql = "UPDATE grupo_similaridade set grsgrupo = '".$params['grsgrupo']."', grsusuoid_alteracao = ".$this->cd_usuario.", grsdt_cadastro = now() WHERE grsoid = ".$params['grsoid'];
			}else{
				$sql = "INSERT INTO grupo_similaridade (grsgrupo, grsusuoid_cadastro, grsdt_cadastro) VALUES ('".$params['grsgrupo']."', ".$this->cd_usuario.", now())";
			}
			if(!$rs = pg_query($this->conn, $sql)){
				throw new Exception ("Erro ao incluir grupo similar");
			}
			
			$retorno['status'] = 'sucesso';
		} catch (Exception $e) {
			$retorno['status'] = 'erro';
			$retorno['mensagem'] = $e->getMessage();
		}

		return $retorno;
	}

	public function excluirGrupoSimilar($grsoid){

		try {
			$sql = "UPDATE grupo_similaridade SET grsusuoid_exclusao = ".$this->cd_usuario.", grsdt_exclusao = now() WHERE grsoid = ".$grsoid;
			if(!$rs = pg_query($this->conn, $sql)){
				throw new Exception ("Erro ao excluir grupo similar");
			}
			$retorno['status'] = 'sucesso';
		} catch (Exception $e) {
			$retorno['status'] = 'erro';
			$retorno['mensagem'] = $e->getMessage();
		}
		return $retorno;
	}
}