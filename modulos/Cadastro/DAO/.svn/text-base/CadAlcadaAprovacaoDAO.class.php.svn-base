<?php

/**
 * Classe padrÃ£o para DAO
 * tabela alcada_compra 
 */
class CadAlcadaAprovacaoDAO {

	private $conn;  
    private $cd_usuario;
    
    public function __construct() {        
        global $conn;
        $this->conn = $conn;   
        $this->cd_usuario = $_SESSION['usuario']['oid'];    

    }

	public function pesquisar($param){

		$sql = "SELECT 
					alcoid, alcodupla_check,
					(select nm_usuario from usuarios where cd_usuario = alcousuoid ) as alcousuoid,
					alcovlr_inicio, alcovlr_fim,
					(select nm_usuario from usuarios where cd_usuario = alcousuoid_dupla_check ) as alcousuoid_dupla_check,
					alcovlr_inicio_dupla_check,	alcovlr_fim_dupla_check,alcodt_cadastro,
					(select nm_usuario from usuarios where cd_usuario = alcousuoid_cadastro ) as alcousuoid_cadastro,
					alcodt_exclusao, alcousuoid_exclusao
				FROM 
					alcada_compra
				JOIN 
					usuarios on cd_usuario = alcousuoid
				WHERE
					1=1
				";

		if($param['alcousuoid'] != ""){
			$sql .= " and alcousuoid = ".$param['alcousuoid'] ;
		}
		if($param['alcovlr_inicio_pesq'] != ""){
			$sql .= " and alcovlr_inicio >= ".str_replace(",",".",str_replace(".","",$param['alcovlr_inicio_pesq'])) ;
		}
		if($param['alcovlr_inicio_pesq'] != "" && $param['alcovlr_fim_pesq'] != ""){
			$sql .= " and alcovlr_fim <= ".str_replace(",",".",str_replace(".","",$param['alcovlr_fim_pesq'])) ;
		}
		if($param['data_inicial'] != "" && $param['data_final'] != ""){
			$sql .= "	and alcodt_cadastro between '".date('Y-m-d',strtotime(str_replace('/','-',$param['data_inicial'])))."' 
						and '".date('Y-m-d',strtotime(str_replace('/','-',$param['data_final'])))."'";
		}
		if(isset($param['alcodupla_check'])){
			$sql .= " and alcodupla_check = 'S'";
		}
		if(isset($param['alcodt_exclusao'])){
			$sql .= " and alcodt_exclusao is not null";
		}else{
			$sql .= " and alcodt_exclusao is null";
		}

		$rs = pg_query($sql);
		if(pg_num_rows($rs) > 0 ){
			return pg_fetch_all($rs);
		}
		return array();		
	}

	public function cadastrarAlcada($param){
		
		try{

			pg_query($this->conn, "BEGIN");

			if(self::verificaAprovadores($param) > 0){
				throw new Exception ("Alçada de aprovação já cadastrada para os usários informados");
			}

			$valores = "";
			$campos = "alcousuoid,alcovlr_inicio,alcovlr_fim, alcodupla_check, alcousuoid_dupla_check,alcovlr_inicio_dupla_check,alcovlr_fim_dupla_check,alcodt_cadastro,alcousuoid_cadastro";
			
			$valores = 	$param['alcousuoid'].",'".
						str_replace(",",".",str_replace(".","",$param['alcovlr_inicio']))."','".
						str_replace(",",".",str_replace(".","",$param['alcovlr_fim']))."',";
					if($param['alcodupla_check'] != ""){
						$valores .= "'S',".$param['alcousuoid_dupla_check'].",'".
						str_replace(",",".",str_replace(".","",$param['alcovlr_inicio_dupla_check']))."','".
						str_replace(",",".",str_replace(".","",$param['alcovlr_fim_dupla_check']))."',";
					}else{
						$valores .= "'N',null,null,null,";
					}

					$valores .= "now(),".$this->cd_usuario;
			
			$sql = "INSERT INTO alcada_compra ($campos) values($valores)";
			if (!pg_query($this->conn, $sql)) {
                throw new Exception ("Erro ao cadastrar alçada de aprovação.");
            }

			pg_query($this->conn, "COMMIT");
            $mensagem = 'Cadastro efetuado com sucesso';
            $status = 'sucesso';

		} catch (Exception $e) {
    	
    		$mensagem = $e->getMessage();
    		$status = 'erro';
    	
    	}
    	$resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;
		return $resultado;
	}

	public function getUsuarioAprovador(){

		$sql = "
                SELECT
                    cd_usuario, nm_usuario
                FROM
                	usuarios
                WHERE
                	dt_exclusao is null
                ORDER BY 
                	nm_usuario
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        if(pg_num_rows($rs) > 0 ){
            for($i = 0; $i < pg_num_rows($rs); $i++) {
                $result[$i]['cd_usuario']       = pg_fetch_result($rs, $i, 'cd_usuario');
                $result[$i]['nm_usuario'] = utf8_encode(pg_fetch_result($rs, $i, 'nm_usuario'));
            }
        }
        
        return $result;
	}

	public function excluirAlcada($id){

		try{
            pg_query($this->conn, "BEGIN");
			
			
			$sql = "UPDATE 
						alcada_compra 
					SET 
						alcodt_exclusao = now(), 
						alcousuoid_exclusao = ".$this->cd_usuario."
					WHERE
						alcoid = $id";
			if (!pg_query($this->conn, $sql)) {
                throw new Exception ("Erro ao excluir alçada de aprovação.");
            }

			pg_query($this->conn, "COMMIT");
            $mensagem = 'Exclusão efetuada com sucesso';
            $status = 'sucesso';
        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
        }
        $resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;

        return $resultado;
	}

	public function pesquisaById($id){

		$sql = "SELECT 
					alcoid, alcousuoid,alcovlr_inicio,alcovlr_fim,alcousuoid_dupla_check, alcodupla_check,
					alcovlr_inicio_dupla_check,alcovlr_fim_dupla_check,alcodt_cadastro,alcousuoid_cadastro
				FROM 
					alcada_compra
				WHERE 
					alcoid = $id
				";
		$rs = pg_query($sql);
		return pg_fetch_assoc($rs);
	}

	public function atualizarAlcada($param){

		try{
            pg_query($this->conn, "BEGIN");
			

			if(self::verificaAprovadores($param) > 0){
				throw new Exception ("Alçada de aprovação já cadastrada para os usários informados");
			}


			$sql = "UPDATE 
						alcada_compra 
					SET 
						alcousuoid = ".$param['alcousuoid'].", 
						alcovlr_inicio = '".str_replace(",",".",str_replace(".","",$param['alcovlr_inicio']))."', 
						alcovlr_fim = '".str_replace(",",".",str_replace(".","",$param['alcovlr_fim']))."',	"; 
						
					if($param['alcodupla_check'] != ""){
						$sql .= " alcousuoid_dupla_check =".$param['alcousuoid_dupla_check'].",
								alcodupla_check ='".$param['alcodupla_check']."', 
								alcovlr_inicio_dupla_check = '".str_replace(",",".",str_replace(".","",$param['alcovlr_inicio_dupla_check']))."', 
								alcovlr_fim_dupla_check ='".str_replace(",",".",str_replace(".","",$param['alcovlr_fim_dupla_check']))."' ";
					}else{
						$sql .= " alcousuoid_dupla_check = null,
								alcodupla_check = 'N',
								alcovlr_inicio_dupla_check = null, 
								alcovlr_fim_dupla_check = null ";
					}
						
					$sql .= "WHERE alcoid =".$param['alcoid'];

			if (!pg_query($this->conn, $sql)) {
                throw new Exception ("Erro ao atualizar alçada de aprovação.");
            }

			pg_query($this->conn, "COMMIT");
            $mensagem = 'Atualizacao efetuada com sucesso';
            $status = 'sucesso';
        } catch (Exception $e) {
            pg_query($this->conn, "ROLLBACK");
            $mensagem = $e->getMessage();
            $status = 'erro';
        }
        $resultado['mensagem'] = $mensagem;
        $resultado['status'] = $status;

        return $resultado;
	}

	public function verificaAprovadores($param){
		$alcousuoid 			= ($param['alcousuoid'] != '') 				? $param['alcousuoid'] 				: 'null';
		$alcousuoid_dupla_check = ($param['alcousuoid_dupla_check'] != '') 	? $param['alcousuoid_dupla_check'] 	: 'null';
		$alcodt_exclusao 		= ($param['alcodt_exclusao'] != '') 		? $param['alcodt_exclusao'] 		: 'null';

		$sql = "SELECT 1 
				  FROM alcada_compra
				 WHERE alcousuoid = ".$alcousuoid." 
				   AND alcodt_exclusao IS NULL ";

			if($alcousuoid_dupla_check != 'null'){
				$sql .= " AND alcousuoid_dupla_check = ".$alcousuoid_dupla_check." ";
			}
		
			if(isset($param['alcoid'])){
				$sql .= " AND alcoid != ".$param['alcoid'];
			}
			
		$rs = pg_query($sql);
		return pg_num_rows($rs);
	}
}