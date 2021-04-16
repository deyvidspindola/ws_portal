<?php
/**
 * @file CadEmergenciaCTDAO.class.php
 * @author Rafael B. Silva - rafaelbarbetasilva@brq.com
 * @version 05/06/2013
 * @since 05/06/2013
 */
 
/**
 * Acesso a dados para o módulo de Emergência
 */
class CadEmergenciaCTDAO {
	
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
					emeeqpoid, emeeqpdescricao 
		        FROM emergencia_equipamento 
				WHERE 
		            emeeqpdt_exclusao IS NULL ";

		if($desc){
			$sql .= " and emeeqpdescricao ilike '%$desc%'";
		}

		if($ID){
			$sql .= " and emeeqpoid=$ID";
		}

        $query  = pg_query($this->conn, $sql);
		$result = pg_fetch_all($query);

        return $result;
    }
	
    public function editar($dados, $oid){
    	
		$sql = "update emergencia_equipamento set 
					emeeqpdescricao = '".$dados['emeeqpdescricao']."',
					emeeqpusuoid_alteracao = $oid,
					emeeqpdt_alteracao = now()
				where emeeqpoid = ".$dados['emeeqpoid'];

        $query = pg_query($this->conn, $sql);

        return $result;    	
    }

 	public function validaDescricao($desc, $idCadastro = null) {

    	$retorno = false;

    	$sql = "SELECT emeeqpoid FROM emergencia_equipamento WHERE emeeqpdescricao ILIKE '{$desc}' AND emeeqpdt_exclusao IS NULL";  

    	$rs = pg_query($this->conn, $sql);

    	$count = pg_num_rows($rs);


    	// se não encontrar pelo nome retorna true e libera o cadastro
    	if($count == 0)
    		$retorno = true;

    	// se tem id é 'update', não pode barrar atualização do mesmo registro
		if($idCadastro != null && $retorno == false):
			$idBanco = pg_fetch_result($rs, 0, 'emeeqpoid');

			if($idCadastro == $idBanco)
				$retorno = true;

		endif;    

    	return $retorno;
    }
    
	public function novo($desc, $oid){
		
		$valDescricao = $this->validaDescricao($desc, null);
		
		try{
			
			if(!$valDescricao) {
				throw new Exception ("Registro já cadastrado");
			}
			
			// valida nome antes de iniciar a transaction
			if($desc == '') {
				throw new Exception ("Preencha os campos obrigatórios");
			}
			
			$sql = "insert into emergencia_equipamento 
						(emeeqpdescricao, emeeqpusuoid_cadastro, emeeqpdt_cadastro, emeeqpusuoid_alteracao, emeeqpdt_alteracao) 
					values ('$desc', $oid, now(), $oid, now())";
						
	        $query = pg_query($this->conn, $sql);
	        
	        pg_query($this->conn, "END");
	        
	        $mensagem = 'Registro cadastrado com sucesso.';
	        $status   = 'sucesso';
	        $acao     = 'index';
        }
        catch (Exception $e) {
        	pg_query($this->conn, "ROLLBACK");
        	
        	$mensagem = $e->getMessage();
        	$status   = 'alerta';
	        $acao     = 'novo';
        }
        
        $resultado['mensagem'] = $mensagem;
        $resultado['status']   = $status;
        $resultado['acao']     = $acao;
        
        return $resultado;
    }
	
	public function excluir($ID,$oid){
    	
		$sql = "UPDATE emergencia_equipamento 
		        SET emeeqpusuoid_exclusao = $oid,
					emeeqpdt_exclusao = now()
		        WHERE emeeqpoid = ".$ID;

        $query = pg_query($this->conn, $sql);

        return $result;    	
    }

}