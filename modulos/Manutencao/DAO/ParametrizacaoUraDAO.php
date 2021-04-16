<?php

class ParametrizacaoUraDAO
{
	
	public function __construct()
	{
		global $conn;		
		$this->_adapter = $conn;
	}
	
	/**
	 * Executa uma query
	 * @param	string		$sql		SQL a ser executado
	 * @return	resource
	 */
	protected function _query($sql)
	{
		return pg_query($this->_adapter, $sql);
	}
	
	/**
	 * Conta os resultados de uma consulta
	 * @param	resource	$results
	 * @return	int
	 */
	protected function _count($results)
	{
		return pg_num_rows($results);
	}
	
	/**
	 * Retorna os resultados de uma consulta num array associativo (hash-like)
	 * @param	resource	$results
	 * @return	array
	 */
	protected function _fetchAll($results)
	{
		return pg_fetch_all($results);
	}
	
	/**
	 * Retorna o resultado de uma coluna num array associativo (hash-like)
	 * @param	resource	$results
	 * @return	array
	 */
	protected function _fetchAssoc($result)
	{
		return pg_fetch_assoc($result);
	}
	
	/**
	 * Insere valores numa tabela
	 * @param	string	$table
	 * @param	array	$values
	 * @return	boolean
	 */
	protected function _insert($table, $arr)
	{
		return pg_insert($this->_adapter, $table, $arr);
	}
	
	/**
	 * Escapa os elementos de um vetor
	 * @param	array	$arr
	 * @return	array
	 */
	protected function _escapeArray($arr)
	{
		array_walk($arr, function(&$item, $key) {
			$item = pg_escape_string($item);
		});
		
		return $arr;
	}
	
	
	/*
	 * Código real abaixo, código boilerplate acima.
     * "Thou shall worship the holy flying spaghetti monster."
	 */
	
	
	
	
	/**
     * Constrói um vetor do Postgres para inserção
     */
    protected function _buildPgArray($arr, $index)
    {
        if (isset($arr[$index]))
        {
            $elm = $arr[$index];
            
            if (isset($elm) && is_array($elm))
            {
                return "'{" . implode(',', $elm) . "}'";
            }
            elseif (strlen($elm))
            {
                return "'{" . $elm . "}'";            
            }
        }
        
        return 'NULL';
    }
    
    /**
     * Inicia uma transação com o banco de dados
     */
    public function abrirTransacao() {
    	pg_query($this->_adapter, "BEGIN;");
    }
    
    /**
     * Comita uma transação com o banco de dados
     */
    public function fecharTransacao() {
    	pg_query($this->_adapter, "COMMIT");
    }
    
    /**
     * Aborta uma transação com o banco de dados
     */
    public function abortarTransacao() {
    	pg_query($this->_adapter, "ROLLBACK;");
    }        
    
    /**
     * Insere novos parâmetros Estatistica
     * @param	array	$arr
     * @return 	boolean
     * 
     * 
     * 
     * 
     */
    		
    		
    public function insertEstatistica($usuario, $parametros)
    {
    	try {
    		$pegarUltimo = "SELECT MAX(pueoid) FROM parametros_ura_estatistica";
    		$res = $this->_query($pegarUltimo);
    		if ($res) {
    			if (pg_num_rows($res) > 0) {
    				$excluirUltimo = "UPDATE parametros_ura_estatistica SET  puedt_exclusao = NOW(), pueusuoid_exclusao = '$usuario' where pueoid = (select max(pueoid) from parametros_ura_estatistica)";
    				$res2 = $this->_query($excluirUltimo);
    			}
    		}
    			
    		$pueostoid 		= (is_array($parametros['pueostoid'])
    				? "array [".implode(", " , $parametros['pueostoid'])."]"
    				: 'null');
    		$pueitem 		= (is_array($parametros['pueitem'])
    				? "array ['".implode("', '" , $parametros['pueitem'])."']"
    				: 'null');
    		$pueossoid 	    = (is_array($parametros['pueossoid'])
    				? "array [".implode(", " , $parametros['pueossoid'])."]"
    				: 'null');
    		$puetpcoid   = (is_array($parametros['puetpcoid'])
    				? "array [".implode(", " , $parametros['puetpcoid'])."]"
    				: 'null');
    		$puecsioid = (is_array($parametros['puecsioid'])
    				? "array [".implode(", " , $parametros['puecsioid'])."]"
    				: 'null');
    		$pueocostatus  = (is_array($parametros['pueocostatus'])
    				? "array ['".implode("', '", $parametros['pueocostatus'])."']"
    				: 'null');
    		$puestatus 		= (is_array($parametros['puestatus'])
    				? " array ['".implode("', '", $parametros['puestatus'])."']"
    				: 'null');
    		 $pueegaoid	 = (is_array($parametros['pueegaoid'])
    				? "array [".implode(", " , $parametros['pueegaoid'])."]"
    				: 'null');
    		 
    		 $puecliente_frota	 = (is_array($parametros['puecliente_frota'])
    		 		? "array [".implode(", " , $parametros['puecliente_frota'])."]"
    		 		: 'null');
    		$sql = "INSERT INTO parametros_ura_estatistica(
						  pueostoid
						, pueitem
						, pueossoid
						, puetpcoid
						, puecsioid
	    				, pueocostatus
	    				, pueegaoid
	    				, puestatus
    					, pueled_bloqueio
    					, pueperiodo_lavacar
    				    , pueperiodo_manutencao
    					, puecliente_frota
    					, pueperiodo_atualizacao
    					, puependencia_financeira
    				)
					VALUES (
	    				 ".$pueostoid."
						,".$pueitem."
						,".$pueossoid."
						,".$puetpcoid."
						,".$puecsioid."
						,".$pueocostatus."
						,".$pueegaoid."
						,".$puestatus."		
						,".( $parametros['pueled_bloqueio'] ? "true" : "false")."
						,".( $parametros['pueperiodo_lavacar'] ? "true" : "false")."
						,".( $parametros['pueperiodo_manutencao'] ? "true" : "false")."
						,".$puecliente_frota."		
						,".( $parametros['pueperiodo_atualizacao'] ?   $parametros['pueperiodo_atualizacao'] : "null")."
						,".( $parametros['puependencia_financeira'] ?  $parametros['puependencia_financeira'] : "null")."						
						 			
								)"	;
    				
    		//print_r($sql);
    
    		$resinsert = $this->_query($sql);
    		//var_dump(pg_last_error($this->_adapter));
    		//die();
    	} catch (Exception $ex) {
    		throw new $ex;
    	}
    	 
    }    
    
    
    
    
    
    
  /*  public function insertEstatistica($arr)
    {
    	$this->_exclusaoTodosEst();
    	
    	
    //	$form['pueostoid'] 					 = $this->_buildPgArray($form['pueostoid']);
    //	$form['pueitem'] 				     = $this->_buildPgArray($form['pueitem']);
    	//$form['pueossoid']   				 = $this->_buildPgArray($form['pueossoid']);
   //	$form['puetpcoid']   				 = $this->_buildPgArray($form['puetpcoid']);
    //	$form['puecsioid'] 					 = $this->_buildPgArray($form['puecsioid']);
    //	$form['pueocostatus'] 				 = $this->_buildPgArray($form['pueocostatus']);   

    	
    
    	// LED de Bloqueio Ativo:
    	if (isset($arr['pueled_bloqueio']))
    	{
    		$arr['pueled_bloqueio'] = 'true';
    	}
    	else
    	{
    		$arr['pueled_bloqueio'] = 'false';
    	}
    
    	// Veículo dentro do período de Manutenção/Lava-Car (Front-End) :
    	if (isset($arr['pueperiodo_lavacar']))
    	{
    		$arr['pueperiodo_lavacar'] = 'true';
    	}
    	else
    	{
    		$arr['pueperiodo_lavacar'] = 'false';
    	}
    
    	// Veículo dentro do período de Manutenção (Cadastro URA) :
    	if (isset($arr['pueperiodo_manutencao']))
    	{
    		$arr['pueperiodo_manutencao'] = 'true';
    	}
    	else
    	{
    		$arr['pueperiodo_manutencao'] = 'false';
    	}
    
    
    	
    
    	
    	$sql = "INSERT INTO parametros_ura_estatistica (
                     pueperiodo_atualizacao
                  , puependencia_financeira
                  , pueled_bloqueio    			
                  , pueperiodo_lavacar    			
                  , pueperiodo_manutencao
                  , puecliente_frota 
                  , pueostoid
                  , pueitem
                  , pueossoid
                  , puetpcoid
                  , puecsioid
                  , pueocostatus
                  , pueegaoid
                  , puestatus
                 
    	) VALUES (
   				 {$arr['pueperiodo_atualizacao']}
    			, {$arr['puependencia_financeira']}
    		    
    		    , {$arr['pueled_bloqueio']}
    			, {$arr['pueperiodo_lavacar']}
    			, {$arr['pueperiodo_manutencao']}
    			
    			,array [".implode(", ", $arr['puecliente_frota'])."]
    		    ,array [".implode(", ", $arr['pueostoid'])."] 
    		    ,array ['".implode("', '", $arr['pueitem'])."'] 
    		    ,array [".implode(", ", $arr['pueossoid'])."]
    		    ,array [".implode(", ", $arr['puetpcoid'])."]
      		    ,array [".implode(", ", $arr['puecsioid'])."]
      		    ,array ['".implode("', '", $arr['pueocostatus'])."'] 
      		    ,array [".implode(", ", $arr['pueegaoid'])."]	
      		    ,array ['".implode("', '", $arr['puestatus'])."'] 							
    							
    	)";
    
    			 echo $sql;
    	return $this->_query($sql);
	}
    
    
    
    
	*/
    
    
    
    /**
     * Insere os valores padrão do form Estatistica
     */
    public function insertDefaultEst()
    {
    	$this->_exclusaoTodosEst();
    
    	$sql = "INSERT INTO parametros_ura_estatistica (
                    pueperiodo_atualizacao
                  , puependencia_financeira
                  , pueled_bloqueio    			
                  , pueperiodo_lavacar    			
                  , pueperiodo_manutencao 
                  , pueostoid    			
                  , pueitem   			
    			  , pueossoid    			
                  , puetpcoid    			    			
                  , puecsioid    			
                  , pueocostatus
    			  , pueegaoid 
    			  , puestatus
    			  ,puecliente_frota
    			    			
                ) VALUES (
                    '96'
                  , '30'
                  , 'true'   	
                  , 'true'
                  , 'true'
    			  , '{ 4,1,2,9,3 }'
    			  , '{ A, E }'
    			  , '{ 4,1,10 }'   			
                  , ARRAY(
                      SELECT tpcoid
                      FROM tipo_contrato
                      WHERE tpcdescricao ILIKE 'Ex-%')
    			  , '{ 12,21,29,6,23,25,35,26,38,9,34,33,4,22}'
    			 , '{ A, S, N }' 
    			 ,'{ 11,6,4}'  
    			, '{C}' 
    			,array [239473,48619,163485]		
                
                )";
       
    

    	return $this->_query($sql);
    }
    
    protected function _exclusaoTodosEst()
    {
    	$userId = $_SESSION['usuario']['oid'];
    
    	$sql = "UPDATE
    				parametros_ura_estatistica
    			SET
    				  puedt_exclusao     = NOW()
    				, pueusuoid_exclusao = ${userId}
    			WHERE
    				puedt_exclusao IS NULL";
  
    	return $this->_query($sql);
    }
    
    /**
    * Busca o último registro salvo
    */
    public function findLastEst()
    {
    $sql = "SELECT
  				  *
    		FROM
   				parametros_ura_estatistica
   			WHERE
   				 puedt_exclusao IS NULL";
 
    	$res = $this->_fetchAssoc($this->_query($sql));
    
        if ($res == false)
        {
            $this->insertDefaultEst();
            return $this->findLastEst();
    }
    
    return $res;
    }
    
    

    
    
	/**
	 * Insere novos parâmetros Panico
	 * @param	array	$arr
	 * @return 	boolean
	 */
    
   public function insert($arr)
    {
    	try {
    		$pegarUltimo = "SELECT MAX(pupoid) FROM parametros_ura_panico";
    		$res = $this->_query($pegarUltimo);
    		
    		$userId = $_SESSION['usuario']['oid'];
    		if ($res) {
    			if (pg_num_rows($res) > 0) {
    				$excluirUltimo = "UPDATE
                  parametros_ura_panico
                SET
                    pupdt_exclusao     = NOW()
                  , pupusuoid_exclusao = ${userId}
                WHERE
                  pupdt_exclusao IS NULL";
    				$res2 = $this->_query($excluirUltimo);
    			}
    		}
    			
    		$puppantoid 	= (is_array($arr['puppantoid'])
    				? "array [".implode(", " , $arr['puppantoid'])."]"
    				: 'null');
    		$puptpcoid 		= (is_array($arr['puptpcoid'])
    				? "array [".implode(", " , $arr['puptpcoid'])."]"
    				: 'null');
    	
    		$pupcsioid 	    = (is_array($arr['pupcsioid'])
    				? "array [".implode(", " , $arr['pupcsioid'])."]"
    				: 'null');
    		$pupostoid   = (is_array($arr['pupostoid'])
    				? "array [".implode(", " , $arr['pupostoid'])."]"
    				: 'null');
    		$pupitem 		= (is_array($arr['pupitem'])
    				? "array ['".implode("', '" , $arr['pupitem'])."']"
    				: 'null');    		
    		$pupossoid  = (is_array($arr['pupossoid'])
    				? "array [".implode(", " , $arr['pupossoid'])."]"
    				: 'null');
    		$pupotdoid 		= (is_array($arr['pupotdoid'])
    				? "array [".implode(", " , $arr['pupotdoid'])."]"
    				: 'null');
    		$pupocostatus 		= (is_array($arr['pupocostatus'])
    				? "array ['".implode("', '" , $arr['pupocostatus'])."']"
    				: 'null');    		
    		$pupporta_panico 	= (is_array($arr['pupporta_panico'])
    				? "array [".implode(", " , $arr['pupporta_panico'])."]"
    				: 'null');
    		 
    		$sql = "INSERT INTO parametros_ura_panico(
						  puppantoid
						, puptpcoid
						, pupcsioid
						, pupostoid
						, pupitem
	    				, pupossoid
	    				, pupotdoid
    					, pupocostatus
    					, pupporta_panico
    					, pupinstalado
    					, puppossui_gerenciadora
    					, puppossui_suspensao
    					, pupacionamento
    					, puppendencia_financeira)
					VALUES (
	    				 ".$puppantoid."
						,".$puptpcoid."
						,".$pupcsioid."
						,".$pupostoid."
						,".$pupitem."
						,".$pupossoid."						
						,".$pupotdoid."	
						,".$pupocostatus."	
						,".$pupporta_panico."
						,".( $arr['pupinstalado'] ? "true" : "false")."
						,".( $arr['puppossui_gerenciadora'] ? "true" : "false")."
						,".( $arr['puppossui_suspensao'] ? "true" : "false")."    		
    		            ,".( $arr['pupacionamento'] ?   $arr['pupacionamento'] : "null")."
					    ,".( $arr['puppendencia_financeira'] ?  $arr['puppendencia_financeira'] : "null")."					
						)"	;		
    
    		
    
    		$resinsert = $this->_query($sql);
    	} catch (Exception $ex) {
    		throw new $ex;
    	}
    	 
    }
    
    
    /*
	public function insert($arr)
	{
		$this->_exclusaoTodos();
        
        $arr['puppantoid']      = $this->_buildPgArray($arr, 'puppantoid');
        $arr['pupporta_panico'] = $this->_buildPgArray($arr, 'pupporta_panico');
        $arr['puptpcoid']       = $this->_buildPgArray($arr, 'puptpcoid');
        $arr['pupcsioid']       = $this->_buildPgArray($arr, 'pupcsioid');
        $arr['pupostoid']       = $this->_buildPgArray($arr, 'pupostoid');
        $arr['pupitem']         = $this->_buildPgArray($arr, 'pupitem');
        $arr['pupossoid']       = $this->_buildPgArray($arr, 'pupossoid');
        $arr['pupotdoid']       = $this->_buildPgArray($arr, 'pupotdoid');
        $arr['pupocostatus']    = $this->_buildPgArray($arr, 'pupocostatus');
        
        // Pânicos de Instalação:
        if (isset($arr['pupinstalado']))
        {
            $arr['pupinstalado'] = 'true';
        }
        else
        {
            $arr['pupinstalado'] = 'false';
        }
        
        // Possui Gerenciadora
        if (isset($arr['puppossui_gerenciadora']))
        {
            $arr['puppossui_gerenciadora'] = 'true';
        }
        else
        {
            $arr['puppossui_gerenciadora'] = 'false';
        }
        
        // Possui Cadastro de Suspensão de Pânico GSM
        if (isset($arr['puppossui_suspensao']))
        {
            $arr['puppossui_suspensao'] = 'true';
        }
        else
        {
            $arr['puppossui_suspensao'] = 'false';
        }
        
        $sql = "INSERT INTO parametros_ura_panico (
                    puppantoid
                  , pupacionamento
                  , pupinstalado
                  , pupporta_panico
                  , puppossui_gerenciadora
                  , puppossui_suspensao
                  , puppendencia_financeira
                  , puptpcoid
                  , pupcsioid
                  , pupostoid
                  , pupitem
                  , pupossoid
                  , pupotdoid
                  , pupocostatus
                ) VALUES (
                    {$arr['puppantoid']}
                  , {$arr['pupacionamento']}
                  , {$arr['pupinstalado']}
                  , {$arr['pupporta_panico']}
                  , {$arr['puppossui_gerenciadora']}
                  , {$arr['puppossui_suspensao']}
                  , {$arr['puppendencia_financeira']}
                  , {$arr['puptpcoid']}
                  , {$arr['pupcsioid']}
                  , {$arr['pupostoid']}
                  , {$arr['pupitem']}
                  , {$arr['pupossoid']}
                  , {$arr['pupotdoid']}
                  , {$arr['pupocostatus']}
                )";
                
        return $this->_query($sql);
	}*/
    
    /**
     * Insere os valores padrão do form Panico
     */
    public function insertDefault()
    {
        $this->_exclusaoTodos();
        
        $sql = "INSERT INTO parametros_ura_panico (
                    puppantoid
                  , pupacionamento
                  , pupinstalado
                  , pupporta_panico
                  , puppossui_gerenciadora
                  , puppossui_suspensao
                  , puppendencia_financeira
                  , puptpcoid
                  , pupcsioid
                  , pupostoid
                  , pupitem
                  , pupossoid
                  , pupotdoid
                  , pupocostatus
                ) VALUES (
                    '{ 131 }'
                  , 15
                  , 'true'
                  , '{ 0 }'
                  , 'true'
                  , 'true'
                  , 30
                  , ARRAY(
                      SELECT tpcoid
                      FROM tipo_contrato
                      WHERE tpcdescricao ILIKE 'Ex-%')
                  , '{ 12, 21, 29, 6, 23, 25, 35, 26, 38, 9, 34, 33, 4, 22 }'
                  , '{ 4 }'
                  , '{ A, E }'
                  , '{ 4 }'
                  , '{ 4, 97 }'
                  , '{ A, S, N }'
                )";
                
       
        return $this->_query($sql);
    }
    
    protected function _exclusaoTodos()
    {
        $userId = $_SESSION['usuario']['oid'];
        
       $sql = "UPDATE
                 parametros_ura_panico
                SET
                    pupdt_exclusao     = NOW()
                  , pupusuoid_exclusao = ${userId}
                WHERE
                 pupdt_exclusao IS NULL";
        
        return $this->_query($sql);
    }
    
    /**
     * Busca o último registro salvo
     */
    public function findLast()
    {
        $sql = "SELECT
                  *
                FROM
                  parametros_ura_panico
                WHERE
                  pupdt_exclusao IS NULL";
                  
        $res = $this->_fetchAssoc($this->_query($sql));
        
        if ($res == false)
        {
            $this->insertDefault();
            return $this->findLast();
        }
        
        return $res;
    }
    
    
     
    
    
    /**
     * Busca tipos de pânico
     */
    public function findTiposPanico()
    {
        $sql = "SELECT *
                FROM panico_tipo
                WHERE
                  pantexclusao IS NULL
                ORDER BY
                  pantdescricao ASC";
                  
        return $this->_fetchAll($this->_query($sql));
    }
    
    /**
     * Busca tipos de contrato
     */
    public function findTiposContrato()
    {
        $sql = "SELECT
                    tpcoid
                  , tpcdescricao
                FROM tipo_contrato
                ORDER BY
                  tpcdescricao ASC";
                  
        return $this->_fetchAll($this->_query($sql));
    }
    
    public function findStatusContratos()
    {
        $sql = "SELECT
                    csioid
                  , csidescricao
                FROM
                  contrato_situacao
                WHERE
                  csiexclusao IS NULL
                ORDER BY
                  csidescricao ASC";
                  
        return $this->_fetchAll($this->_query($sql));
    }
    
    public function findTiposOrdemServico()
    {
        $sql = "SELECT
                    ostoid
                  , ostdescricao 
                FROM
                  os_tipo
                WHERE
                  ostdt_exclusao IS NULL
                ORDER BY
                  ostdescricao ASC";
                  
        return $this->_fetchAll($this->_query($sql));
    }
    
    public function findStatusOrdemServico()
    {
        $sql = "SELECT
                    ossoid
                  , ossdescricao 
                FROM
                  ordem_servico_status
                WHERE
                  ossexclusao IS NULL
                ORDER BY
                  ossdescricao ASC";
                  
        return $this->_fetchAll($this->_query($sql));
    }
    
    public function findDefeitoAlegado()
    {
        $sql = "SELECT
                    otdoid
                  , otddescricao 
                FROM
                  os_tipo_defeito
                WHERE
                  otdalegado = 'true'
                  AND otddt_exclusao IS NULL
                ORDER BY
                  otddescricao ASC";
                  
        return $this->_fetchAll($this->_query($sql));
    }
    
   	public function findAcaoOS()
   	{
   		$sql = "SELECT 
					mhcoid,
					mhcdescricao
				FROM 
					motivo_hist_corretora
				WHERE 
					mhcexclusao IS NULL
				ORDER BY 
					mhcdescricao";
   		
   		return $this->_query($sql);
   	}
    
    
    public function findOcorrenciasStatus()
    {
        return array(
        		'L' => 'Cancelados.'
        	  , 'C' => 'Concluídos'        		
        	  , 'A' => 'Em Andamento'
        	  , 'O' => 'Não Concluídos'	
        	  , 'N' => 'Não Recuperado'	
        	  , 'P' => 'Pendentes'
              , 'R' => 'Recuperado'
        	  , 'S' => 'Sem Contato'	
            
        );

    }
    
    public function findOsTipo()
    {
    	$sql = "SELECT * FROM os_tipo WHERE ostdt_exclusao IS NULL ORDER BY ostdescricao";
    	return $this->_query($sql);
    }
    
    public function findOsStatus()
    {
    	$sql = "SELECT * FROM ordem_servico_status WHERE ossexclusao IS NULL ORDER BY ossdescricao";
    	return $this->_query($sql);
    }
    
    public function findTipoContrato()
    {
    	$sql = "SELECT	*
				FROM tipo_contrato
				WHERE tpcativo = true
    			ORDER BY tpcdescricao";
    	return $this->_query($sql);
    }
    
    public function findContratoStatus()
    {
    	$sql = "SELECT	*
				FROM contrato_situacao
				WHERE csiexclusao IS NULL
    			ORDER BY csidescricao";
    	return $this->_query($sql);
    }

    /**
     * Busca o último registro salvo
     */
    public function findLastAssistencia($usuario=null)
    {
    	$sql = "SELECT
  				  *
	    		FROM
	   				parametros_ura_assistencia
	   			WHERE
	   				puaoid = (select max(puaoid) from parametros_ura_assistencia where puausuoid_exclusao IS NULL)";
    
    	$res = $this->_fetchAssoc($this->_query($sql));    
    	if ($res == false and $usuario!= null)
    	{
    		$this->predefinidosSalvar($usuario);
    		return $this->findLastAssistencia($usuario);
    	}
    
    	return $res;
    }
    
    public function assistenciaSalvar($usuario, $parametros)
    {
    	try {
	    	$pegarUltimo = "SELECT MAX(puaoid) FROM parametros_ura_assistencia";
			$res = $this->_query($pegarUltimo);
			if ($res) {
				if (pg_num_rows($res) > 0) {
					$excluirUltimo = "UPDATE parametros_ura_assistencia SET puadt_exclusao = NOW(), puausuoid_exclusao = '$usuario' where puaoid = (select max(puaoid) from parametros_ura_assistencia)";
					$res2 = $this->_query($excluirUltimo);
				}
			}
			
			$tipoOS 		= (is_array($parametros['tipoOS']) 
									? "array [".implode(", " , $parametros['tipoOS'])."]" 
									: 'null');
			$itemOS 		= (is_array($parametros['itemOS']) 
									? "array ['".implode("', '" , $parametros['itemOS'])."']" 
									: 'null');
			$statusOS 	    = (is_array($parametros['statusOS']) 
									? "array [".implode(", " , $parametros['statusOS'])."]" 
									: 'null');
			$tipoContrato   = (is_array($parametros['tipoContrato']) 
									? "array [".implode(", " , $parametros['tipoContrato'])."]" 
									: 'null');
			$statusContrato = (is_array($parametros['statusContrato']) 
									? "array [".implode(", " , $parametros['statusContrato'])."]" 
									: 'null');
			$tipoDefeitoOS  = (is_array($parametros['tipoDefeitoOS']) 
									? "array [".implode(", " , $parametros['tipoDefeitoOS'])."]" 
									: 'null');
			$acaoOS 		= (is_array($parametros['acaoOS']) 
									? "array [".implode(", " , $parametros['acaoOS'])."]" 
									: 'null');
				    	
	    	$sql = "INSERT INTO parametros_ura_assistencia(
						  puaostoid
						, puaitem
						, puaossoid
						, puatpcoid
						, puacsioid
	    				, puaotdoid
	    				, puaacao
	    				, puaagenda_posterior)
					VALUES (
	    				 ".$tipoOS."
						,".$itemOS."
						,".$statusOS."
						,".$tipoContrato."
						,".$statusContrato."
						,".$tipoDefeitoOS."
						,".$acaoOS."
						,".( $parametros['agendaOS'] ? "true" : "false").")";

	    	//print_r($sql);exit;
	    	
	    	$resinsert = $this->_query($sql);
    	} catch (Exception $ex) {
    		throw new $ex;
    	}
    	
    }   
    
    public function predefinidosSalvar($usuario = null)
    {
    	try {
    		$pegarUltimo = "SELECT MAX(puaoid) FROM parametros_ura_assistencia";
    		$res = $this->_query($pegarUltimo);
    		if ($res) {
    			if (pg_num_rows($res) > 0) {    				
    				$excluirUltimo = "UPDATE parametros_ura_assistencia SET puadt_exclusao = NOW(), puausuoid_exclusao = '$usuario' where puaoid = (select max(puaoid) from parametros_ura_assistencia)";
    				$res2 = $this->_query($excluirUltimo);
    			}
    		}
    		 
    		$sql = "INSERT INTO parametros_ura_assistencia(
            puaostoid
            , puaitem
            , puaossoid
            , puatpcoid
            , puacsioid
            , puaotdoid
            , puaagenda_posterior
    		,puaacao
    				
    		)
    VALUES (array [1,2,3,9]
	        ,NULL
	        ,array [1,10,14,9,20,3,12,16,17,8,5,7,13,18,11,19]
	    	,ARRAY(SELECT tpcoid
                      FROM tipo_contrato
                      WHERE tpcdescricao ILIKE 'Ex-%')
    		,array [20,5,7,27,32,24,16,6,12,21,29,23,25,35,26,38,9,34,33,4,22]
            ,array [4,97]
            , true
    		,array [109]		
    		)";
    		
	    	$resinsert = $this->_query($sql);
    	} catch (Exception $ex) {
    		throw new $ex;
    	}
    	 
    }
    
    
    public function findTiposAcao()
    {
    	$sql = "SELECT 
 					egaoid,
  					egadescricao
				FROM 
 					estatistica_gsm_acao
				WHERE 
  					egadt_exclusao IS NULL
				ORDER BY 
  					egadescricao ASC";
    	
    
    	
    	return $this->_fetchAll($this->_query($sql));
    
    }
    
    public function findBuscaClientes()
    {
    	$sql = "SELECT
 					*
				FROM
 					clientes
				WHERE
  					clioid = ANY((select puecliente_frota from parametros_ura_estatistica where puedt_exclusao IS NULL limit 1)::integer[])";
    	 
    
    	 
    	return $this->_fetchAll($this->_query($sql));
    
    }
    
    
    public function findTipoStatusEstatistica()
    {
        return array(
        	  'C' => 'Concluídos'
             , 'A' => 'Em Andamento' 
             , 'P' => 'Pendentes'
            
        );

    }
    
    /**
     * Busca informação de acionamento do Cron das campanhas da URA
     * @return object
     */
    public function buscarInformacoesCron(){
    	
    	$sql = "
    			SELECT
    					cuaoid,
    					cuacronenvio,
    					cuacroninsucesso,
    					cuacronadicional,
    					cuacronreenvio
    			
    			FROM
    					campanhas_ura_ativa    			
    			";   	
    	
    	$retorno =  $this->_fetchAll($this->_query($sql));
    	
    	return $retorno;

    }
    
    /**
     * Atualiza o status das campanhas URA
     * @param array $status
     * @param string $campanha
     * @return boolean
     */
    public function atualizarInformacoesCron($statusCron, $campanha){
    	    	
    	$retorno          = false;
    	$cuaoid           = 0;
    	$cuacronenvio     = isset($statusCron['envio']) 		? $statusCron['envio'] 		: 'A';
    	$cuacroninsucesso = isset($statusCron['insucesso']) 	? $statusCron['insucesso'] 	: 'A';
    	$cuacronadicional = isset($statusCron['adicional']) 	? $statusCron['adicional'] 	: 'A';
    	$cuacronreenvio   = isset($statusCron['reenvio']) 		? $statusCron['reenvio'] 	: 'A';
    	
    	if(empty($campanha)){
    		return $retorno;
    	}
    	
    	if($campanha == 'panico'){
    		
    		$cuaoid = 1;    		
    	}
    	else if($campanha == 'assistencia'){
    	
    		$cuaoid = 2;
    	}
    	else{
    	
    		$cuaoid = 3;
    	}
    	
    	$sql = "
    			UPDATE
    					campanhas_ura_ativa
				SET 	
    					cuacronenvio = '". $cuacronenvio ."',
    					cuacroninsucesso = '". $cuacroninsucesso ."',
    					cuacronadicional = '". $cuacronadicional ."',
    					cuacronreenvio = '". $cuacronreenvio ."'
    			WHERE
    					cuaoid = ". $cuaoid .";
    			";
 
    	$retorno = $this->_query($sql);
    	$retorno = (boolean)$retorno;
    	
    	return $retorno;
    	
    }
    
}