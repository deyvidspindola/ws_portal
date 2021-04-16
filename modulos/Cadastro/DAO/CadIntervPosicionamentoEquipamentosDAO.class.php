<?php
 
/**
 * @file CadIntervPosicionamentoEquipamentosDAO.class.php
 * @author Diego de Campos Nogu�s
 * @version 06/08/2013
 * @since 06/08/2013
 * @package SASCAR CadIntervPosicionamentoEquipamentosDAO.class.php
 */
/**
 * Acesso a dados para o m�dulo de Cadastro de Validade de Equipamentos
 */
class CadIntervPosicionamentoEquipamentosDAO {
	
	/**
	 * Conex�o com o banco de dados
	 * @var resource
	 */
	private $conn;	
	
	/**
	 * Construtor, recebe a conex�o com o banco
	 * @param resource $connection
	 * @throws Exception
	 */
	public function __construct() {        
        global $conn;
        $this->conn = $conn;   
        $this->usuoid = $_SESSION['usuario']['oid'];    

    }

    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }

    public function getInterPosicionamentoEquipamento ($id) {
        $sql = "
            SELECT 
                iposeqpoid,
                iposeqpdescricao
            FROM 
                intervalo_posicionamento_equipamento 
            WHERE 
                iposeqpoid = {$id}";

        $rs = pg_query($this->conn, $sql);
        
        $result = pg_fetch_all($rs);
        $result = $result[0];       

        return $result;     
    } 

    public function pesquisa($params = array()) {

    	$sql = "
    		SELECT 
			    iposeqpoid,
                iposeqpdescricao
            FROM 
                intervalo_posicionamento_equipamento 		
			WHERE 
			   	iposeqpdt_exclusao IS NULL";

		// filtro por nome
		if($params['iposeqpdescricao'] != '')
			$sql .= " AND to_ascii(iposeqpdescricao) ILIKE to_ascii('%".$params['iposeqpdescricao']."%')";		

		$sql .= " ORDER BY iposeqpdescricao ";

		$rs = pg_query($this->conn, $sql);

		$result = pg_fetch_all($rs);
	
		if(!$result)
			$result = array();

        return $result;  

    }

    public function excluirDados($id)
	{
		$resultado = array();
		try{
	        pg_query($this->conn, "BEGIN");
	        
	        if(!$id){
	        	throw new Exception ("Erro ao Excluir.");
	        }	        

			$query = "  UPDATE
							intervalo_posicionamento_equipamento
						SET
							iposeqpdt_exclusao = NOW(),
							iposeqpusuoid_exclusao = '$this->usuoid'
						WHERE
							iposeqpoid = '$id'
					";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao excluir o registro.");
	        }

			$mensagem = 'Registro exclu�do com sucesso.';
            $status   = 'sucesso';
            $acao     = 'index';
			pg_query($this->conn, "END");
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
            $status   = 'alerta';
	    }

	    $resultado['mensagem'] = $mensagem;
        $resultado['status']   = $status;
        $resultado['acao']     = $acao;
	    return $resultado;

	}

	public function atualizaDados($params) {    	

    	// remove campos n�o utilizados no update
    	$params = $this->removeCamposPost($params);

    	// atualiza usuoid e dt_criacao
    	$params['iposeqpusuoid_alteracao'] = $this->usuoid;
    	$params['iposeqpdt_alteracao']     = 'NOW()';

    	$fields = implode(',', array_keys($params));
    	$values = strtoupper(str_replace("''", 'null', "'".implode("','", array_map('trim', array_values($params)))."'"));

    	$valDescricao = $this->validaDescricao($params['iposeqpdescricao'], $params['iposeqpoid']);

    	try{    		
    		// valida descri��o antes de iniciar a transaction    		
    		if(!$valDescricao) {
    			throw new Exception ("Registro j� cadastrado");
    		}

    		// valida descri��o antes de iniciar a transaction    		
    		if($params['iposeqpdescricao'] == '') {
    			throw new Exception ("Preencha os campos obrigat�rios");
    		}

    		pg_query($this->conn, "BEGIN");
	        
    		$query = "UPDATE intervalo_posicionamento_equipamento SET ";
    			foreach($params as $key => $value):
    				if($value == '')
    					$value = 'null';
    				else
    					$value = "'".$value."'";

    					$query .= " {$key} = {$value},";
    			endforeach;

    		$query = trim($query, ',');
    		$query .= " WHERE iposeqpoid = {$params['iposeqpoid']}";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao atualizar o registro.");
	        }	

			$mensagem = 'Registro atualizado com sucesso.';
            $status   = 'sucesso';
            $acao     = 'index';
			pg_query($this->conn, "END");    
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
            $status   = 'alerta';
        }

        $resultado['mensagem'] = $mensagem;
        $resultado['status']   = $status;
        $resultado['acao']     = $acao;
        return $resultado;
        
    }

    public function inserirDados($params) {    

    	// remove campos n�o utilizados no primeiro insert
    	$params = $this->removeCamposPost($params);

    	// adiciona usuoid e dt_criacao
    	$params['iposeqpusuoid_cadastro']      = $this->usuoid;
    	$params['iposeqpdt_cadastro'] = 'NOW()';

    	$params['iposeqpusuoid_alteracao'] = $this->usuoid;
    	$params['iposeqpdt_alteracao']     = 'NOW()';
        
    	// remove campo de id
    	unset($params['iposeqpoid']);

    	$fields = implode(',', array_keys($params));
    	$values = strtoupper(str_replace("''", 'null', "'".implode("','", array_map('trim', array_values($params)))."'"));

    	$valDescricao = $this->validaDescricao($params['iposeqpdescricao'], $params['iposeqpoid']);

    	try{
    		
    		// valida descri��o antes de iniciar a transaction    		
    		if(!$valDescricao) {
    			throw new Exception ("Registro j� cadastrado");
    		}

    		// valida nome antes de iniciar a transaction    		
    		if($params['iposeqpdescricao'] == '') {
    			throw new Exception ("Preencha os campos obrigat�rios");
    		}

	        pg_query($this->conn, "BEGIN");
    		$query = "INSERT INTO intervalo_posicionamento_equipamento
									($fields) 
								VALUES 
									($values)
								RETURNING 
									iposeqpoid";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao cadastrar o registro.");
	        }	

			$mensagem = 'Registro cadastrado com sucesso.';
            $status   = 'sucesso';
			$acao     = 'index';
			pg_query($this->conn, "END");    
		}
		catch (Exception $e) {
	        pg_query($this->conn, "ROLLBACK");
	        $mensagem = $e->getMessage();
            $status   = 'alerta';
	    }

	    $resultado['mensagem'] = $mensagem;
        $resultado['status']   = $status;
	    $resultado['acao']     = $acao;
	    return $resultado;
        
    }

    public function validaDescricao($desc, $idCadastro = null) {

    	$retorno = false;

    	$sql = "SELECT iposeqpoid FROM intervalo_posicionamento_equipamento WHERE iposeqpdescricao ILIKE '{$desc}' AND iposeqpdt_exclusao IS NULL";  

    	$rs = pg_query($this->conn, $sql);

    	$count = pg_num_rows($rs);


    	// se n�o encontrar pelo nome retorna true e libera o cadastro
    	if($count == 0)
    		$retorno = true;

    	// se tem id � 'update', n�o pode barrar atualiza��o do mesmo registro
		if($idCadastro != null && $retorno == false):
			$idBanco = pg_fetch_result($rs, 0, 'iposeqpoid');

			if($idCadastro == $idBanco)
				$retorno = true;

		endif;    

    	return $retorno;
    }

    public function	removeCamposPost($params) {
    	// campos que devem permanecer
    	$arrCampos = array(
    			'iposeqpoid',
    			'iposeqpdescricao'
    		);

    	return array_intersect_key($params, array_flip($arrCampos));    	
    }
	
}