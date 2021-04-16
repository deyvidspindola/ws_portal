<?php
 
/**
 * @file CadValidadesEquipamentosDAO.class.php
 * @author Diego de Campos Noguês
 * @version 06/08/2013
 * @since 06/08/2013
 * @package SASCAR CadValidadesEquipamentosDAO.class.php
 */
/**
 * Acesso a dados para o módulo de Cadastro de Validade de Equipamentos
 */
class CadValidadesEquipamentosDAO {
	
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
        $this->usuoid = $_SESSION['usuario']['oid'];    

    }

    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }

    public function getValidadeEquipamento ($id) {
        $sql = "
            SELECT 
                veqpoid,
                veqpdescricao,
                veqpqtd_dias
            FROM 
                validade_equipamento 
            WHERE 
                veqpoid = {$id}";

        $rs = pg_query($this->conn, $sql);
        
        $result = pg_fetch_all($rs);
        $result = $result[0];       

        return $result;     
    } 

    public function pesquisa($params = array()) {

    	$sql = "
    		SELECT 
			    veqpoid, 
			    veqpdescricao,
                veqpqtd_dias
			FROM 
				validade_equipamento			
			WHERE 
			   	veqpdt_exclusao IS NULL";

		// filtro por nome
		if($params['veqpdescricao'] != '')
			$sql .= " AND to_ascii(veqpdescricao) ILIKE to_ascii('%".$params['veqpdescricao']."%')";		

		$sql .= " ORDER BY veqpdescricao ";

		$rs = pg_query($this->conn, $sql);

		$result = pg_fetch_all($rs);
	
		if(!$result)
			$result = array();

        return $result;  

    }

    public function excluirDados($veqpoid)
	{
		$resultado = array();
		try{
	        pg_query($this->conn, "BEGIN");
	        
	        if(!$veqpoid){
	        	throw new Exception ("Erro ao Excluir.");
	        }	        

			$query = "  UPDATE
							validade_equipamento
						SET
							veqpdt_exclusao = NOW(),
							veqpusuoid_exclusao = '$this->usuoid'
						WHERE
							veqpoid = '$veqpoid'
					";

			if(!$sql = pg_query($this->conn, $query)){
	        	throw new Exception ("Houve um erro ao excluir o registro.");
	        }

			$mensagem = 'Registro excluído com sucesso.';
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

    	// remove campos não utilizados no update
    	$params = $this->removeCamposPost($params);

    	// atualiza usuoid e dt_criacao
    	$params['veqpusuoid_alteracao'] = $this->usuoid;
    	$params['veqpdt_alteracao']     = 'NOW()';

    	$params['veqpdescricao'] = $params['veqpqtd_dias'].'D';
        $fields = implode(',', array_keys($params));
    	$values = strtoupper(str_replace("''", 'null', "'".implode("','", array_map('trim', array_values($params)))."'"));
        
    	$valDescricao = $this->validaDescricao($params['veqpdescricao'], $params['veqpoid']);

    	try{    		
    		// valida descrição antes de iniciar a transaction    		
    		if(!$valDescricao) {
    			throw new Exception ("Registro já cadastrado");
    		}

    		// valida descrição antes de iniciar a transaction    		
    		if($params['veqpqtd_dias'] == '') {
    			throw new Exception ("Preencha os campos obrigatórios");
    		}

    		pg_query($this->conn, "BEGIN");
	        
    		$query = "UPDATE validade_equipamento SET ";
    			foreach($params as $key => $value):
    				if($value == '')
    					$value = 'null';
    				else
    					$value = "'".$value."'";

    					$query .= " {$key} = {$value},";
    			endforeach;

    		$query = trim($query, ',');
    		$query .= " WHERE veqpoid = {$params['veqpoid']}";

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

    	// remove campos não utilizados no primeiro insert
    	$params = $this->removeCamposPost($params);

    	// adiciona usuoid e dt_criacao
    	$params['veqpusuoid_cadastro']      = $this->usuoid;
    	$params['veqpdt_cadastro'] = 'NOW()';

    	$params['veqpusuoid_alteracao'] = $this->usuoid;
    	$params['veqpdt_alteracao']     = 'NOW()';
        
    	// remove campo de id
    	unset($params['veqpoid']);


        $params['veqpdescricao'] = $params['veqpqtd_dias'].'D';
    	$fields = implode(',', array_keys($params));
    	$values = strtoupper(str_replace("''", 'null', "'".implode("','", array_map('trim', array_values($params)))."'"));

    	$valDescricao = $this->validaDescricao($params['veqpdescricao'], $params['veqpoid']);

    	try{
    		
    		// valida descrição antes de iniciar a transaction    		
    		if(!$valDescricao) {
    			throw new Exception ("Registro já cadastrado");
    		}

    		// valida nome antes de iniciar a transaction    		
    		if($params['veqpdescricao'] == '') {
    			throw new Exception ("Preencha os campos obrigatórios");
    		}

	        pg_query($this->conn, "BEGIN");
    		$query = "INSERT INTO validade_equipamento
									($fields) 
								VALUES 
									($values)
								RETURNING 
									veqpoid";

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

    	$sql = "SELECT veqpoid FROM validade_equipamento WHERE veqpdescricao ILIKE '{$desc}' AND veqpusuoid_exclusao IS NULL";  

    	$rs = pg_query($this->conn, $sql);

    	$count = pg_num_rows($rs);


    	// se não encontrar pelo nome retorna true e libera o cadastro
    	if($count == 0)
    		$retorno = true;

    	// se tem id é 'update', não pode barrar atualização do mesmo registro
		if($idCadastro != null && $retorno == false):
			$idBanco = pg_fetch_result($rs, 0, 'veqpoid');

			if($idCadastro == $idBanco)
				$retorno = true;

		endif;    

    	return $retorno;
    }

    public function	removeCamposPost($params) {
    	// campos que devem permanecer
    	$arrCampos = array(
    			'veqpoid',
    			'veqpdescricao',
                'veqpqtd_dias'
    		);

    	return array_intersect_key($params, array_flip($arrCampos));    	
    }
	
}