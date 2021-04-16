<?php
/**
 * @file CadCicloEquipamentoCTDAO.class.php
 * @author Rafael B. Silva - rafaelbarbetasilva@brq.com
 * @version 05/06/2013
 * @since 05/06/2013
 */
 
/**
 * Acesso a dados para o módulo de Emergência
 */
class CadCicloEquipamentoCTDAO {
	
	/**
	 * Conexão com o banco de dados
	 * @var resource
	 */
	private $conn;	
	
	/**
	 * Construtor, recebe a conexão com o banco
	 * @param resource $connection
	 * @throws Exception
	 */
	public function __construct() {        
        global $conn;
        $this->conn = $conn;
    }

	public function pesquisar($desc=false, $ID=false){
    	
		$sql = "SELECT 
					ciceqpoid, ciceqpdescricao 
		        FROM ciclo_equipamento 
				WHERE 
		            ciceqpdt_exclusao IS NULL ";

		if($desc){
			$sql .= " AND ciceqpdescricao ILIKE '%$desc%'";
		}

		if($ID){
			$sql .= " AND ciceqpoid=$ID";
		}
		
		$sql .= " ORDER BY ciceqpdescricao";

        $query  = pg_query($this->conn, $sql);
		$result = pg_fetch_all($query);

        return $result;
    }
	
    public function editar($dados, $oid){
    	
		$sql = "UPDATE ciclo_equipamento SET 
					ciceqpdescricao = '".$dados['ciceqpdescricao']."',
					ciceqpusuoid_alteracao = $oid,
					ciceqpdt_alteracao = now()
				WHERE ciceqpoid = ".$dados['ciceqpoid'];

        $query = pg_query($this->conn, $sql);

        return $result;    	
    }

	public function novo($desc, $oid){
		
		$valDescricao = $this->validaDescricao($desc, $oid);
		
		try {
			// valida descrição antes de iniciar a transaction
			if(!$valDescricao) {
				throw new Exception ("Registro já cadastrado");
			}
			pg_query($this->conn, "BEGIN");
			
			$sql = "INSERT INTO ciclo_equipamento
			(ciceqpdescricao, ciceqpusuoid_cadastro, ciceqpdt_cadastro, ciceqpusuoid_alteracao, ciceqpdt_alteracao)
			VALUES ('$desc', $oid, now(), $oid, now())";
				
			$query = pg_query($this->conn, $sql);
			
			$mensagem = 'Registro cadastrado com sucesso.';
			$status   = 'sucesso';
			$acao     = 'index';
			pg_query($this->conn, "COMMIT");
			
		}catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
            $status   = 'alerta';
	    }

	    $resultado['mensagem'] = $mensagem;
        $resultado['status']   = $status;
	    $resultado['acao']     = $acao;
	    return $resultado;  	
    }
	
	public function excluir($ID, $oid){

		$sql = "UPDATE ciclo_equipamento 
				SET ciceqpusuoid_exclusao = $oid,
		            ciceqpdt_exclusao = now()
		        WHERE ciceqpoid = ".$ID;

        $query = pg_query($this->conn, $sql);

        return $result;    	
    }
    
    public function validaDescricao($desc, $idCadastro = null) {
    
    	$retorno = false;
    
    	$sql = "SELECT ciceqpoid 
	    	FROM ciclo_equipamento 
	    	WHERE ciceqpdescricao ILIKE '{$desc}' 
	    	AND ciceqpdt_exclusao IS NULL";
    
    	$rs = pg_query($this->conn, $sql);
    
    	$count = pg_num_rows($rs);
    
    
    	// se não encontrar pelo nome retorna true e libera o cadastro
    	if($count == 0)
    	$retorno = true;
    
    	// se tem id é 'update', não pode barrar atualização do mesmo registro
    	if($idCadastro != null && $retorno == false):
    	$idBanco = pg_fetch_result($rs, 0, 'ciceqpoid');
    
    	if($idCadastro == $idBanco)
    		$retorno = true;
    
    		endif;
    
    		return $retorno;
    }
}