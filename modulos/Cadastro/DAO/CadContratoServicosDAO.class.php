<?php

/**
 * @file CadContratoServicosDAO.class.php
 * @author Rafael Mitsuo Moriya 
 * @version 06/09/2013
 * @since 06/09/2013
 * @package SASCAR CadContratoServicosDAO.class.php 
 */

class CadContratoServicosDAO {
    
    private $conn; 
    private $connlog; 
    private $cd_usuario;
    
    public function __construct() {
        global $conn;
        
        $this->conn = $conn; 
        $this->connlog = $conn;     
        $this->cd_usuario = $_SESSION['usuario']['oid'];  
    }
        
    /**
     * Verfica o status da ativação do equipamento
     * @param int $serial
     * @param int $consoid
     * @throws Exception
     * @return boolean
     */
    public function getStatusAtivacao($serial){

        try{
            
            
    		$sql = "SELECT
    					equoid
    				FROM
    					equipamento
    				WHERE
    					equno_serie = '$serial'
    					AND equdt_exclusao IS NULL";
        			
    		if(!$rs = pg_query($this->conn, $sql)){
    			throw new Exception ("Equipamento não encontrado com este serial.");
    		}
        		 
    		if(pg_num_rows($rs) > 0 ){
    			$equoid = pg_fetch_result($rs,0,"equoid");

        	    $sql= "SELECT 
        	    			srectstatus 
        	    		FROM 
        	    			status_retorno_equipamento_ct
    					WHERE
    						srectequoid = '$equoid'
    					AND srectdt_exclusao IS NULL";

    			if(!$rs = pg_query($this->conn, $sql)){
    				throw new Exception ("Houve um erro ao verificar o tempo do registro.");
        		}
        		
        		if(pg_num_rows($rs) > 0 ){        		    
        		    return  pg_fetch_result($rs,0,"srectstatus"); 
        		}else{
        		    return 'N';
        		}
        		
    		}
    		
        } catch (Exception $e) {
    			 
	    	return false;    			 
		}
    }
    
    /**
     * Grava o status do equipamento
     * @param bigint $serial
     * @throws Exception
     * @return string
     */
    public function gravaStatusAtivacao($serial,$consoid=null,$consrf,$status,$api_key,$connumero=null,$protocolo=null,$usuario=null){
       // $params['serial'],$params['consoid'],$params['rf'],"R",$api_key,$params['connumero'], $retorno->protocolo, $params['usuario']
    	try{
	    	pg_query($this->conn, "BEGIN");
	    	
	    	if($usuario == null){
	    	    $usuario = $this->cd_usuario;
	    	}
	    	
    		// Grava RF do equipamento
    		$retorno = $this->gravaRF($serial,$consrf);
    		
    		if($retorno!=true){
    			throw new Exception ($retorno);
    		}
    		
    		$sql = "SELECT 
    					equoid 
    				FROM 
    					equipamento 
    				WHERE 
    					equno_serie = '$serial'
						AND equdt_exclusao IS NULL";

    		if(!$rs = pg_query($this->conn, $sql)){
    			throw new Exception ("Equipamento não encontrado com este serial.");
    		}
    	
    		if(pg_num_rows($rs) > 0 ){
    			$equoid = pg_fetch_result($rs,0,"equoid");
    			
	    		$sql = "SELECT 
	    					srectoid,
	    					srectstatus
	    				FROM 
	    					status_retorno_equipamento_ct 
	    				WHERE 
	    					srectequoid = '$equoid'
	    					AND srectdt_exclusao IS NULL";

	    		if(!$rs = pg_query($this->conn, $sql)){
	    			throw new Exception ("Houve um erro ao selecionar o registro.");
	    		}

	    		if(pg_num_rows($rs) > 0 ){
	    		    
	    			$srectoid = pg_fetch_result($rs,0,"srectoid");
	    			
		    		$sql= "UPDATE
		    					status_retorno_equipamento_ct
		    				SET 
		    					srectstatus = '$status',
		    					srectprotocolo = '$protocolo',
		    					srectdt_ultima_solicitacao = NOW()
		    				WHERE 
		    					srectoid = $srectoid";
	
		    		if(!$rs = pg_query($this->conn, $sql)){
		    			throw new Exception ("Houve um erro ao atualizar o status de ativação.");
		    		}
		    		if(pg_affected_rows($rs) == 0){
		    		    throw new Exception ("O status de ativação não foi atualizado.");
		    		} 	
	    				
	    		}else{
	    			
	    		    if($consoid){
	    		        $insConsoid = $consoid;
	    		    }else{
	    		        $insConsoid = 'null';
	    		    }
	    		    
		    		$sql= "INSERT INTO 
		    					status_retorno_equipamento_ct 
		    					(srectequoid,srectstatus,srectprotocolo,srectconsoid,srectapi_key) 
		    				VALUES 
		    					($equoid,'$status','$protocolo',$insConsoid,'$api_key')";
		    			
		    		if(!$rs = pg_query($this->conn, $sql)){
		    			throw new Exception ("Houve um erro ao inserir o registro.");
		    		}
		    		
	    		}		    		
		    		
	    		if($consoid){
    				$sql = "SELECT 
    							consconoid
    						FROM
    							contrato_servico 
    						WHERE 
    							consoid = $consoid
    							AND consiexclusao IS NULL";	
    					    		
    				if(!$rs = pg_query($this->conn, $sql)){
    					throw new Exception ("Houve um erro ao achar o contrato.");
    				}
    				
    				$connumero = pg_fetch_result($rs,0,"consconoid");
    				
    				$retorno = $this->gravaHistorico($connumero,'Requisicao de ativacao de equipamento N. Serial ' . $serial . 'para o servico N.' . $consoid,$usuario);
	    		}else{
	    		    $retorno = $this->gravaHistorico($connumero,'Requisicao de ativacao de equipamento N. Serial ' . $serial,$usuario);	    		    
	    		}
    				
				if($retorno != 1){ 
					throw new Exception ($retorno);
				}
    				
    		}else{
    			throw new Exception ("Equipamento não encontrado.");
    		}

    		pg_query($this->conn, "COMMIT");
    		pg_query($this->conn, "END");
    		
    		return true;
    	
    	} catch (Exception $e) {
    		pg_query($this->conn, "ROLLBACK");
    		return $e->getMessage();
    	}
    }
    
    /**
     * Atualiza o status do equipamento 
     * @param bigint $serial
     * @throws Exception
     * @return string
     */
    public function atualizaStatusAtivacao($ccid,$status,$api_key,$motivo = null){
    	
    	try{
	    	    		
    		$sql = "SELECT 
                        equoid
                    FROM 
    		            linha
                    INNER JOIN 
    		            equipamento ON equaraoid = linaraoid 
    		                        AND equno_fone = linnumero
                    WHERE 
    		            lincid = '".$ccid."'
                    AND
    		            equdt_exclusao IS NULL";

    		if(!$rs = pg_query($this->conn, $sql)){
    			throw new Exception ("Equipamento não encontrado com este serial.");
    		}
    		
    		$setMotivo = ' srectmotivo = null, ';
    		if($motivo){
    		    $setMotivo = " srectmotivo = '$motivo', ";
    		}
    	

            // ALTERA STATUS OS
            $this->alteraStatusOS($ccid, $motivo);

    		if(pg_num_rows($rs) > 0 ){
    			$equoid = pg_fetch_result($rs,0,"equoid");

                

	    		$sql= "UPDATE
	    					status_retorno_equipamento_ct
	    				SET 
	    				    $setMotivo 
	    					srectstatus = '$status',
	    					srectdt_ultima_solicitacao = NOW()
	    				WHERE 
	    				    srectdt_exclusao IS NULL	    				    
	    					AND srectequoid = '$equoid'
	    					AND srectapi_key = '$api_key'";

	    		if(!$rs = pg_query($this->conn, $sql)){
	    			throw new Exception ("Houve um erro ao atualizar o status de ativação.");
	    		}
	    		if(pg_affected_rows($rs) == 0){
	    		    throw new Exception ("O status de ativação não foi atualizado.");
	    		}  
    		}else{
    			throw new Exception ("Equipamento não encontrado.");
    		}
    		
    		return "O status de ativação atualizado com sucesso.";
    	
    	} catch (Exception $e) {
    		return $e->getMessage();
    	}
    }
    
    /**
     * Grava o status do equipamento
     * @param bigint $serial
     * @throws Exception
     * @return string
     */
    public function gravaStatusDesativacao($serial,$consoid = null,$connumero = null, $usuario = null){
    	 
    	try{
    		 
    		pg_query($this->conn, "BEGIN");
    		
    		if($usuario == null){
    		    $usuario = $this->cd_usuario;
    		}
    		
    		// Seleciona o equipamento
    		$sql = "SELECT
		    			equoid
		    		FROM
		    			equipamento
		    		WHERE
		    			equno_serie = '$serial'";
    			
    		if(!$rs = pg_query($this->conn, $sql)){
    			throw new Exception ("Equipamento não encontrado com este serial.");
    		}
   
    		if(pg_num_rows($rs) > 0 ){
	    		$equoid = pg_fetch_result($rs,0,"equoid");
	    		 
	    		// Atualiza o status de retorno do equipamento
	    		$sql= "UPDATE
			    			status_retorno_equipamento_ct
			    		SET
			    			srectstatus = 'N',
			    			srectdt_exclusao = NOW()
			    		WHERE
				    		srectequoid = '$equoid'
				    		AND srectdt_exclusao IS NULL";
	    		 
	    		if(!$rs = pg_query($this->conn, $sql)){
	    			throw new Exception ("Houve um erro ao atualizar o registro.");
	    		}
	    
	    		// Grava histórico
	    		if($consoid != ''){
    	    		$sql = "SELECT
    			    			consconoid as connumero
    			    		FROM
    			    			contrato_servico
    			    		WHERE
    			    			consoid = $consoid";

    	    		if(!$rs = pg_query($this->conn, $sql)){
    	    		    throw new Exception ("Houve um erro ao achar o contrato.");
    	    		}
    	    		 
    	    		$connumero = pg_fetch_result($rs,0,"connumero");
    	    		
    	    		$texto = 'Requisição de desativação de equipamento Nº Serial ' . $serial . ' para o servico Nº '. $consoid;
	    		}else{
	    		    	
    	    		$texto = 'Requisição de desativação de equipamento Nº Serial ' . $serial ;    
	    		}
	    	    		
				$retorno = $this->gravaHistorico($connumero,$texto,$usuario);

				if($retorno!=true){
					throw new Exception ($retorno);
				}
    		}
    
    		$status   = 'sucesso';
    		pg_query($this->conn, "COMMIT");
    		pg_query($this->conn, "END");
    		
    		return true;
		} catch (Exception $e) {
    		 
    		pg_query($this->conn, "ROLLBACK");
    		return false;
    		 
    	}
    
    }
    
    /**
     * Grava RF
     * @param int $imoboid
     */
    public function gravaRF($serial,$consrf){
    	
    	try{
			
	    	$sql = "SELECT 
	    				imoboid 
	    			FROM 
	    				imobilizado 
	    			WHERE 
	    				imobserial ilike '" . $serial . "'
						AND imobexclusao IS NULL";
			
	    	if (!$rs = pg_query($this->conn, $sql)) {
	    		throw new Exception ("Erro ao selecionar imobilizado.");
	    	}
	    	
	    	if(pg_num_rows($rs)>0){
	    		$imoboid = pg_fetch_result($rs,0,"imoboid");
		    	
		    	$sql = "SELECT 
		    				* 
		    			FROM 
		    				atributo_equipamento_ct 
		    			WHERE 
		    				aectimoboid = " . $imoboid . " 
							AND aectdt_exclusao IS NULL";
		    	
		    	if (!$rs = pg_query($this->conn, $sql)) {
		    		throw new Exception ("Erro ao selecionar imobilizado.");
		    	}
		    	
		    	if(pg_num_rows($rs)==0){
		    		
		    		// Adiciona RF
					$sql = "INSERT INTO atributo_equipamento_ct
								(aectimoboid,aectrf)
							VALUES
	    						($imoboid,$consrf)";
	    					
					if (!pg_query($this->conn, $sql)) {
						throw new Exception ("Erro ao adicionar RF.");
					}
	
		    						
				}
	    	}
	    	
			return true;
			
		} catch (Exception $e){
			return $e->getMessage();
			break;
		}
    }

	/**
	 * Grava Histórico na tabela historico_termo
	 * @param int $connumero
	 * @param text $text
	 */
	public function gravaHistorico($connumero,$text,$usuario){
		
		try{
			
			// Inserindo histórico no termo/contrato
			$sql = "SELECT historico_termo_i($connumero, $usuario, '$text')";

			if(!pg_query($this->conn,$sql)){
				throw new exception('Erro: Nao foi possivel gravar historico do termo');
			}
			
			return true;
		
		} catch (Exception $e){
			return $e->getMessage();
		}
		
	}

	/**
	 * Retorna atributos para ativação através do serial
	 * @param int $serial
	 */
	public function getAtributosAtivacao($serial, $connumero){
		
		$sql = "
		        -- equipamento principal
                SELECT 
                	conalidoid_equipamento as cod_local, veiplaca
                FROM 
                	equipamento
                INNER JOIN contrato ON conequoid = equoid
                INNER JOIN veiculo ON conveioid = veioid
                WHERE 
                	equno_serie = '$serial'
                AND connumero = $connumero

                UNION
                
                -- acessorio
                SELECT
                	consalioid as cod_local, veiplaca
                FROM
                	equipamento
                INNER JOIN contrato_servico ON consrefioid = equoid
                INNER JOIN contrato ON consconoid = connumero
                INNER JOIN veiculo ON conveioid = veioid
                WHERE 
                	equno_serie = '$serial'
                	AND consconoid = $connumero
                ";
		
		if(!$rs = pg_query($this->conn,$sql)){
			throw new exception('Erro: Não foi possível encontrar atributos através do serial');
		}
		
		$array = array();
		if(pg_num_rows($rs) > 0 ){
			$array['placa'] 	= pg_fetch_result($rs,0,"veiplaca"); 
			$array['cod_local'] = pg_fetch_result($rs,0,"cod_local"); 
		}
			
		return $array;
	}

	/**
	 * Retorna Radio Frequencia (RF) através do serial
	 * @param int $serial
	 */
	public function getRF($serial){
		
		$sql = "SELECT 
		            aectrf AS rf 
                FROM 
                    atributo_equipamento_ct 
                LEFT JOIN imobilizado ON aectimoboid = imoboid
                LEFT JOIN consumo_serial ON aectcseroid = cseroid
                WHERE 
                    aectdt_exclusao IS NULL
                    AND aectrf IS NOT NULL
                    AND (cserserial = '$serial' OR imobserial = '$serial')
                LIMIT 1";
		
		$rs = pg_query($this->conn,$sql);
		
		$rf = '';
		if(pg_num_rows($rs) > 0 ){
			$rf = pg_fetch_result($rs,0,"rf"); 
		}
			
		return $rf;
	}

	/**
	 * Retorna $company_id referente ao tipo de contrato
	 * @param int $connumero
	 * @param int $consoid
	 */
	public function getCompanyId($connumero = null,$consoid = null){
		
	    if($connumero) {
    		$sql = "SELECT
    		            tpccompany_id, conno_tipo, tpcdescricao
                    FROM 
    		            contrato
                    INNER JOIN 
    		            tipo_contrato ON conno_tipo = tpcoid
                    WHERE 
    		            connumero = $connumero
                    LIMIT 1";
	    }elseif($consoid){
	        $sql = "SELECT 
	                    tpccompany_id, conno_tipo, tpcdescricao
                    FROM 
	                    contrato_servico
                    INNER JOIN 
                        contrato ON consconoid = connumero
                    INNER JOIN 
                        tipo_contrato ON conno_tipo = tpcoid
                    WHERE 
                        consoid = $consoid
	                LIMIT 1";
	    }
		
		$rs = pg_query($this->conn,$sql);
		
		$company_id = Array();
		if(pg_num_rows($rs) > 0 ){
			$company_id['company_id'] = pg_fetch_result($rs,0,"tpccompany_id"); 
			$company_id['conno_tipo'] = pg_fetch_result($rs,0,"conno_tipo"); 
			$company_id['tpcdescricao'] = pg_fetch_result($rs,0,"tpcdescricao"); 
		}
			
		return $company_id;
	}

	/**
	 * Verifica se o equipamento já está em um processo de ativação em outro servico
	 * Caso esteja retorna true
	 * @param int $serial
	 * @throws Exception
	 * @return boolean
	 */
	public function verificaExisteProcessoAtivacao($serial,$consoid = null){   
	    	    
		$sql = "SELECT
					equoid
				FROM
					equipamento
				WHERE
					equno_serie = '$serial'
					AND equdt_exclusao IS NULL";
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception ("Equipamento não encontrado com este serial.");
    	}
   
		if(pg_num_rows($rs) > 0 ){
			$equoid = pg_fetch_result($rs,0,"equoid");
				 
			$sql = "SELECT
						srectoid,
						srectstatus,
						srectconsoid,
						srectequoid
					FROM
						status_retorno_equipamento_ct
					WHERE
						srectequoid = '$equoid'
						AND srectdt_exclusao IS NULL";
	
			if(!$rs = pg_query($this->conn, $sql)){
				throw new Exception ("Houve um erro ao selecionar o registro.");
			}
		
			if(pg_num_rows($rs) > 0 ){
				
				$srectconsoid = pg_fetch_result($rs,0,"srectconsoid");
				$srectequoid = pg_fetch_result($rs,0,"srectequoid");
		
				if($consoid && $consoid == $srectconsoid){
	    			return false;
	    		}elseif($equoid == $srectequoid){
	    			return false;
	    		}else{
	    			return true;
				}
				
			}else{
				
    			return false;
    			
			}
		}
	}

	/**
	 * Verifica se o equipamento é CargoTracck
	 * Caso esteja retorna true
	 * @param int $serial
	 * @throws Exception
	 * @return boolean
	 */
	public function validaEquipamentoCT($serial){
	    
		$sql = "SELECT
					prdcargotracck
				FROM
					equipamento
                INNER JOIN 
                	produto ON equprdoid = prdoid 
				WHERE
					equno_serie = '$serial'
					AND equdt_exclusao IS NULL";
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception ("Equipamento não encontrado com este serial.");
    	}
   
		if(pg_num_rows($rs) > 0 ){
		    
			$prdcargotracck = pg_fetch_result($rs,0,"prdcargotracck");
			
		}
		
		return $prdcargotracck;
		
	}

	/**
	 * Verifica se o contrato servico é CargoTracck
	 * Caso esteja retorna true
	 * @param int $serial
	 * @throws Exception
	 * @return boolean
	 */
	public function validaContratoServicoCT($consoid){
	    
		$sql = "SELECT
                    prdcargotracck
                FROM 
                    obrigacao_financeira
                    INNER JOIN contrato_servico ON consobroid = obroid
                    INNER JOIN contrato ON consconoid = connumero
                    LEFT JOIN obrigacao_financeira_tecnica ON oftcobroid = obroid
                    LEFT JOIN produto ON obrprdoid = prdoid
                WHERE 
                    consoid = $consoid";
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception ("Equipamento não encontrado com este serviço.");
    	}
   
		if(pg_num_rows($rs) > 0 ){
		    
			$prdcargotracck = pg_fetch_result($rs,0,"prdcargotracck");
			
		}
		
		return $prdcargotracck;
		
	}

	/**
	 * Pega o cpf do instalado
	 * @param int $itl
	 * @throws Exception
	 * @return bigint
	 */
	public function getCpfInstalador($itl){
	    
		$sql = "SELECT 
		            itlno_cpf 
		        FROM 
		            instalador 
		        WHERE 
		            itloid = $itl";
		
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception ("Instalador não encontrado.");
    	}
   
		if(pg_num_rows($rs) > 0 ){
		    
			$cpf = pg_fetch_result($rs,0,"itlno_cpf");
			
		}
		
		return $cpf;
		
	}

	/**
	 * Pega o rf do equipamento
	 * @param bigint $serial
	 * @throws Exception
	 * @return integer
	 */
	public function getRfEquipamento($serial){
	    
		$sql = "SELECT 
		            aectrf 
		        FROM 
		            atributo_equipamento_ct
                LEFT JOIN imobilizado ON aectimoboid = imoboid
                LEFT JOIN consumo_serial ON aectcseroid = cseroid
                WHERE
                    (ltrim(cserserial,'0') = ltrim('$serial','0') OR ltrim(imobserial,'0') = ltrim('$serial','0'))
                    AND aectdt_exclusao IS NULL ";
		
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception ("Atributo não encontrado.");
    	}
   
		if(pg_num_rows($rs) > 0 ){
		    
			$rf = pg_fetch_result($rs,0,"aectrf");
			
		}
		
		return $rf;
		
	}

	/**
	 * Pega o local de instalacao do equipamento
	 * @param integer $connumero
	 * @throws Exception
	 * @return integer
	 */
	public function getLocalInstalacao($connumero){
	    
		$sql = "SELECT 
		            conalidoid_equipamento 
		        FROM
		            contrato 
		        WHERE
		            connumero = $connumero";
		
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception ("Contrato não encontrado.");
    	}
   
		if(pg_num_rows($rs) > 0 ){
		    
			$local = pg_fetch_result($rs,0,"conalidoid_equipamento");
			
		}
		
		return $local;
		
	}

	/**
	 * Pega o placa do veiculo do contrato
	 * @param integer $connumero
	 * @throws Exception
	 * @return string
	 */
	public function getPlaca($connumero){
	    
		$sql = "SELECT
		            veiplaca
		        FROM
		            contrato
		        INNER JOIN 
		            veiculo ON conveioid = veioid
		        WHERE 
		            connumero =  $connumero";
		
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception ("Veiculo não encontrado.");
    	}
   
		if(pg_num_rows($rs) > 0 ){
		    
			$placa = pg_fetch_result($rs,0,"veiplaca");
			
		}
		
		return $placa;
		
	}


	/**
	 * Grava as chamadas aos WS da CargoTracck
	 * @param string $method
	 * @param string $url
	 * @param string $dados
	 * @param string $resposta
	 * @throws Exception
	 */
	public function gravaLOG($method = null,$url = null,$dados = null, $resposta = null){
	     	
		if($method != null){
			$method = addslashes($method);
		}
		if($url != null){
			$url = addslashes($url);
		}
		if($dados != null){
			$dados = addslashes($dados);
		}
		if($resposta != null){
			$resposta = addslashes($resposta);
		}

        // Adiciona LOG
	    pg_query($this->connlog, "BEGIN");
        $sql = "INSERT INTO log_ws_cargotracck
                    (lwctmethod,lwcturl,lwctdados,lwctresposta)
                VALUES
                    ('$method','$url','$dados','$resposta')";

        if (!pg_query($this->connlog, $sql)) {
            throw new Exception ("Erro ao adicionar LOG.");
        }
        
        pg_query($this->connlog, "COMMIT");
        pg_query($this->connlog, "END");

	
	}

    /**
     * Atualiza status O.S.
     * @param int $equoid
     */
    public function alteraStatusOS($ccid, $motivo){

        /*SELECIONA EQUIPAMENTO*/
        $sql = "SELECT 
        			equoid
                FROM 
        			equipamento        		
        		INNER JOIN 
        			linha ON equaraoid = linaraoid AND equno_fone = linnumero
                WHERE 
        			lincid = '".$ccid."'
                	AND equdt_exclusao IS NULL";
        
        $rs = pg_query($this->conn,$sql);
        if(pg_num_rows($rs) > 0){
            $equoid = pg_fetch_result($rs,0,"equoid");
        }

        // VERIFICA NR CONTRATO
        $sql = "SELECT 
                    connumero, 'E' as tipo
                FROM 
                    contrato
                WHERE 
                    conequoid = ".$equoid."
                UNION
                SELECT 
                    consconoid as connumero, 'A' as tipo
                FROM 
                    contrato_servico
                WHERE 
                    consrefioid = ".$equoid;
        $rs = pg_query($this->conn,$sql);
        if(pg_num_rows($rs) > 0){
            $connumero = pg_fetch_result($rs,0,"connumero");
            $tipo = pg_fetch_result($rs,0,"tipo");
        }

        // NR ORDEM SERVICO
        /*SELECT PRA EQUIPAMENTO*/
        $sqlEquipamento = 
            "SELECT ordoid, ordstatus, ositoid, otitipo, ossoid
            FROM ordem_servico 
            INNER JOIN contrato ON ordconnumero = connumero 
            INNER JOIN ordem_servico_item ON ositordoid = ordoid
            INNER JOIN os_tipo_item ON ositotioid = otioid 
            INNER JOIN ordem_servico_status ON ordstatus = ossoid
            WHERE connumero = ".$connumero."
            AND otitipo = 'E'
            AND ordstatus <> 9";
        
        /*SELECT PARA ACESSORIO*/
        $sqlAcessorio = 
            "SELECT ordoid, ordstatus, ositoid, otitipo, ossoid
            FROM ordem_servico 
            INNER JOIN contrato ON ordconnumero = connumero 
            INNER JOIN contrato_servico ON consconoid = connumero
            INNER JOIN ordem_servico_item ON ositordoid = ordoid
            INNER JOIN os_tipo_item ON ositotioid = otioid 
            INNER JOIN ordem_servico_status ON ordstatus = ossoid
            WHERE connumero = ".$connumero."
            AND consobroid = otiobroid
            AND otitipo = 'A'";

        if($tipo == "E"){
            $rs = pg_query($this->conn,$sqlEquipamento);
        }else if($tipo == "A"){
            $rs = pg_query($this->conn,$sqlAcessorio);
        }

        if(pg_num_rows($rs) > 0){
            $ordoid = pg_fetch_result($rs,0,"ordoid");
            $status = pg_fetch_result($rs,0,"ossoid");
            $ositoid = pg_fetch_result($rs,0,"ositoid");

            // 21 => "Pendente Teste para Conclusão"
            if($status == 21){
                // concluir servico
                $sql = "UPDATE ordem_servico_item
                        SET ositstatus = 'C'
                        WHERE ositoid  = $ositoid";
                pg_query ($this->conn, $sql);

                // VERIFICA ITENS PENDENTES
                echo $sql = "SELECT 1 FROM ordem_servico_item WHERE ositordoid = ".$ordoid." and ositstatus <> 'C'";
                $rs = pg_query($this->conn,$sql);
            
                $total = pg_num_rows($rs);

                //se nao possui acessorios pendentes conclui O.S.
                if($total == 0){                        
                    $sql = "UPDATE 
                                ordem_servico
                            SET 
                                ordeqcoid = (SELECT coneqcoid FROM contrato WHERE connumero=ordconnumero),
                                ordstatus = 3,
                                orddescr_motivo = '$motivo'
                            WHERE 
                                ordoid=".$ordoid;
                    $rs = pg_query($this->conn, $sql);
                }
            }
        }
    }

    /**
     * Numero de posições sem descarregar a bateria.
     * @param int $prdoid
     */
    public function getNumeroPosicoesSemDescarregarBateria($ccid){

        $sql = "SELECT prdposicoes_enviadas FROM linha
                INNER JOIN equipamento ON equaraoid = linaraoid AND equno_fone = linnumero
                LEFT JOIN produto ON prdoid = equprdoid
                WHERE lincid = '".$ccid."' AND equdt_exclusao IS NULL";

        $rs = pg_query($this->conn,$sql);

        $nrPosicoes = 0;
        if(pg_num_rows($rs) > 0){
            $nrPosicoes = (pg_fetch_result($rs,0,"prdposicoes_enviadas")) == '' ? 0 : pg_fetch_result($rs,0,"prdposicoes_enviadas") ;
        }

        return $nrPosicoes;
    }

    /**
     * Pega o numero serial a partir do CCID.
     * @param int $ccid
     */
    public function getCcid($serial){

        $sql = "SELECT lincid
                FROM linha
                INNER JOIN equipamento ON equaraoid = linaraoid AND equno_fone = linnumero
                WHERE equno_serie = '$serial' 
                AND linexclusao is null
                AND equdt_exclusao IS NULL";

        $rs = pg_query($this->conn,$sql);

        $ccid = 0;
        if(pg_num_rows($rs) > 0){
            $ccid = pg_fetch_result($rs,0,"lincid");
        }

        return $ccid;
    }


    /**
    * Valida status da linha
    **/
    public function validaStatusLinha($equno_serie)
    {

        $sql = "SELECT 
                        linoid, 
                        lincsloid, 
                        cslstatus, 
                        linaraoid, 
                        linnumero, 
                        equno_fone, 
                        equno_serie, 
                        equoid, 
                        clsoid 
                FROM contrato 
                INNER JOIN equipamento ON equoid = conequoid 
                INNER JOIN linha ON equno_fone = linnumero AND equaraoid = linaraoid 
                LEFT JOIN celular_linha_canc ON clslinha = linnumero AND clsaraoid = linaraoid 
                LEFT JOIN celular_status_linha ON csloid = lincsloid 
                WHERE linexclusao IS NULL 
                AND equno_serie = {$equno_serie}";

        $rs = pg_query($this->conn, $sql);

        if(pg_num_rows($rs) > 0){
            
            return (object) pg_fetch_object($rs);
        }

    }

    /**
     * COnsulta posicao dos acessórios para tipos carreta
     * @return array
     */
    public function getTipoCarretaPosicao(){

        $retorno = array();

        $sql = "SELECT tcpaoid, tcpadescricao FROM tipo_carreta_posicao_acessorio ORDER BY tcpadescricao ASC";

        $rs = pg_query($this->conn, $sql);

        if(pg_num_rows($rs) > 0){

            while ($result = pg_fetch_object($rs)) {
                $retorno[] = $result;
            }
        }

        return $retorno;
    }

    /**
     * Valida posição selecionada
     * @return bool
     */
    public function validaRegistroPosicaoAcessorioCarreta($consconoid, $consobroid, $constcpaoid, $consoid){

        $sql = "SELECT 
                    1 
                FROM 
                    contrato_servico 
                WHERE 
                    consconoid = $consconoid 
                AND 
                    consinstalacao IS NOT NULL 
                AND 
                    consiexclusao IS NULL 
                AND 
                    consobroid = $consobroid 
                AND 
                    constcpaoid = $constcpaoid 
                AND 
                    consoid <> $consoid";

        $rs = pg_query($this->conn, $sql);

        if(pg_num_rows($rs) > 0){
            return false;
        }else{
            return true;
        }
    }
}