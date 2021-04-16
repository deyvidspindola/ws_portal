<?php
/**
 * DAO
 * @author Bruno Bonfim Affonso [bruno.bonfim@sascar.com.br]
 * @package Principal
 * @version 1.0
 * @since 22/11/2013
 */
class PrnImpEquipamentoSbtecDAO{
	private $conn = null;

    public function __construct(){	
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction(){
    	pg_query($this->conn, "BEGIN;");
    }
    
    /**
     * Confirma uma transação
     */
    public function commitTransaction(){
    	pg_query($this->conn, "COMMIT;");
    }
    
    /**
     * Reverte uma transação
     */
    public function rollbackTransaction(){
    	pg_query($this->conn, "ROLLBACK;");
    }
    
    /**
     * Retorna dados do equipamento.
     * 
     * @param string $whereKey (Cláusula WHERE)
     * @return array
     */
    public function getEquipamento($whereKey){
    	$sql = "SELECT
    				equoid,
    				equno_serie,
    				eqsdescricao,
    				equno_ddd,
    				equno_fone,
    				equesn,
    				eveversao,
    				eprnome,
    				eqcdescricao,
    				contrato,
    				eqsoid,
    				equpatrimonio,
    				equrelroid,
    				equtipo,
    				equaraoid,
    				equeveoid,
    				equeqmoid,
    				equeqfoid,
    				equprdoid,
    				equversao_hardware,
    				equversao_firmware,
    				equid_sascarga
    			FROM
    				sbtec.equipamento
    			LEFT OUTER JOIN
    				equipamento_status ON eqsoid = equeqsoid
    			LEFT OUTER JOIN
    				equipamento_versao ON eveoid = equeveoid
    			LEFT OUTER JOIN
    				equipamento_projeto ON eproid = eveprojeto
    			LEFT OUTER JOIN
    				equipamento_classe ON eqcoid = eveeqcoid
    			LEFT OUTER JOIN
    				sincroniza_placa_sbtec ON id_sascarga::text = equid_sascarga
    			WHERE 
    				$whereKey;";
    	    	
    	$sql = pg_query($this->conn, $sql);
    	$resulSet = array();
    	
    	if(pg_num_rows($sql) > 0){
    		$resulSet = array('equoid' => pg_fetch_result($sql, 0, 'equoid'), 'equno_serie' => pg_fetch_result($sql, 0, 'equno_serie'),
    				'eqsdescricao' => pg_fetch_result($sql, 0, 'eqsdescricao'), 'equno_ddd' => pg_fetch_result($sql, 0, 'equno_ddd'),
    				'equno_fone' => pg_fetch_result($sql, 0, 'equno_fone'), 'equesn' => pg_fetch_result($sql, 0, 'equesn'),
    				'eveversao' => pg_fetch_result($sql, 0, 'eveversao'), 'eprnome' => pg_fetch_result($sql, 0, 'eprnome'),
    				'eqcdescricao' => pg_fetch_result($sql, 0, 'eqcdescricao'), 'contrato' => pg_fetch_result($sql, 0, 'contrato'),
    				'eqsoid' => pg_fetch_result($sql, 0, 'eqsoid'), 'equpatrimonio' => pg_fetch_result($sql, 0, 'equpatrimonio'),
    				'equrelroid' => pg_fetch_result($sql, 0, 'equrelroid'), 'equtipo' => pg_fetch_result($sql, 0, 'equtipo'),
    				'equaraoid' => pg_fetch_result($sql, 0, 'equaraoid'), 'equeveoid' => pg_fetch_result($sql, 0, 'equeveoid'),
    				'equeqmoid' => pg_fetch_result($sql, 0, 'equeqmoid'), 'equeqfoid' => pg_fetch_result($sql, 0, 'equeqfoid'),
    				'equprdoid' => pg_fetch_result($sql, 0, 'equprdoid'), 'equversao_hardware' => pg_fetch_result($sql, 0, 'equversao_hardware'),
    				'equversao_firmware' => pg_fetch_result($sql, 0, 'equversao_firmware'), 'equid_sascarga' => pg_fetch_result($sql, 0, 'equid_sascarga'));
    	}
    	
    	return $resulSet;
    }
    
    /**
     * Verifica se existe a entrada.
     *
     * @param int $patrimonio
     * @return boolean
     */
    public function existeEntrada($patrimonio){
    	$sql = "SELECT
    				entoid
    			FROM
    				entrada
    			WHERE
    				entoid = (SELECT imobentoid FROM imobilizado WHERE imobpatrimonio = $patrimonio);";
    	
    	$sql = pg_query($this->conn, $sql);
    	
    	if(pg_num_rows($sql) > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna dados de ENTRADA.
     * @param int $patrimonio
     * @return array
     */
    public function getEntrada($patrimonio){
    	$sql = "SELECT
    				entoid
    			FROM
    				entrada
    			WHERE
    				entoid = (SELECT imobentoid FROM imobilizado WHERE imobpatrimonio = $patrimonio);";
    	 
    	$sql = pg_query($this->conn, $sql);
    	 
    	$resulSet = array();
    	
    	if(pg_num_rows($sql) > 0){
    		$resulSet = array('entoid' => pg_fetch_result($sql, 0, 'entoid'));
    	}
    	
    	return $resulSet;
    }
    
    /**
     * Insere uma entrada.
     * 
     * @param int $patrimonio
     * @return int entoid/false
     */
    public function insertEntrada($patrimonio){
    	$sql = "INSERT INTO entrada
    				(entcadastro,entdt_entrada,entdt_emissao,
    				entnota,entserie,entforoid,enttotal_real,
    				entusuoid,entstatus,entexclusao,entexcl_usuoid,
    				entconpoid,enttnfoid,entftcoid,enttecoid,enttotal,
    				ententrega_futura)
    				
    				(SELECT
    					entcadastro,entdt_entrada,entdt_emissao,
    					entnota,entserie,entforoid,enttotal_real,
    					entusuoid,entstatus,entexclusao,entexcl_usuoid,
    					entconpoid,enttnfoid,entftcoid,enttecoid,enttotal,
    					ententrega_futura
    				FROM
    					sbtec.entrada
    				WHERE
    					entoid = (SELECT imobentoid FROM sbtec.imobilizado WHERE imobpatrimonio = $patrimonio)) RETURNING entoid;";
    	
    	$sql = pg_query($this->conn, $sql);
    	
    	if(pg_affected_rows($sql) > 0){
    		return pg_fetch_result($sql, 0, 'entoid');
    	} else{
    		return false;
    	}
    }
    
    /**
     * Verifica se existe o Item de Entrada.
     *
     * @param int $patrimonio
     * @return boolean
     */
    public function existeItemEntrada($patrimonio){
    	$sql = "SELECT
    				entioid
    			FROM
    				entrada_item
    			WHERE
    				entioid = (SELECT imobentioid FROM imobilizado WHERE imobpatrimonio = $patrimonio);";
    	 
    	$sql = pg_query($this->conn, $sql);
    	 
    	if(pg_num_rows($sql) > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna dados de ITEM ENTRADA.
     * @param int $patrimonio
     * @return array
     */
    public function getItemEntrada($patrimonio){
    	$sql = "SELECT
    				entioid
    			FROM
    				entrada_item
    			WHERE
    				entioid = (SELECT imobentioid FROM imobilizado WHERE imobpatrimonio = $patrimonio);";
    	
    	$sql = pg_query($this->conn, $sql);
    	
    	$resulSet = array();
    	 
    	if(pg_num_rows($sql) > 0){
    		$resulSet = array('entioid' => pg_fetch_result($sql, 0, 'entioid'));
    	}
    	 
    	return $resulSet;
    }
	
    /**
     * Insere ITEM ENTRADA.
     * 
     * @param int $patrimonio
     * @param int $entoid
     * @return int entioid/false
     */
    public function insertItemEntrada($patrimonio, $entoid){
    	$sql = "INSERT INTO entrada_item
    				(entientoid,entiprdoid,entiplcoid,entiperc_ipi,entivlr_ipi_float,entiperc_icms,
    				entivlr_icms,entinopoid,entipis,enticofins,enticsll,entiiss,entiinss,entidepoid,
    				entivlr_unit,entiqtde,entivlr_ipi)
    				
    				(SELECT
    					$entoid,entiprdoid,entiplcoid,entiperc_ipi,entivlr_ipi_float,entiperc_icms,
    					entivlr_icms,entinopoid,entipis,enticofins,enticsll,entiiss,entiinss,entidepoid,
    					entivlr_unit,entiqtde,entivlr_ipi
    				FROM
    					sbtec.entrada_item
    				WHERE
    					entioid = (SELECT imobentioid FROM sbtec.imobilizado WHERE imobpatrimonio = $patrimonio))
    				RETURNING
    					entioid;";
    	
    	$sql = pg_query($this->conn, $sql);
    	
    	if(pg_affected_rows($sql) > 0){
    		return pg_fetch_result($sql, 0, 'entioid');
    	} else{
    		return false;
    	}
    }
    
    /**
     * Verifica se existe Imobilizado.
     * 
     * @param int $patrimonio
     * @return boolean
     */
    public function existeImobilizado($patrimonio){
    	$sql = "SELECT
    				imoboid
    			FROM
    				imobilizado
				WHERE
    				imobpatrimonio = $patrimonio;";
    	
		$sql = pg_query($this->conn, $sql);
    	 
    	if(pg_num_rows($sql) > 0){
    		return true;
    	} else{
    		return false;
    	}
    }
    
    /**
     * Retorna dados do IMOBILIZADO.
     * @param int $patrimonio
     * @return array()
     */
    public function getImobilizado($patrimonio){
    	$sql = "SELECT
    				imoboid
    			FROM
    				imobilizado
    			WHERE
    				imobpatrimonio = $patrimonio;";
    	
    	$sql = pg_query($this->conn, $sql);
    	
    	$resulSet = array();
    	 
    	if(pg_num_rows($sql) > 0){
    		$resulSet = array('imoboid' => pg_fetch_result($sql, 0, 'imoboid'));
    	}
    	 
    	return $resulSet;
    }
    
    /**
     * Insere IMOBILIZADO.
     * @param int $patrimonio
     * @param int $entoid
     * @param int $entioid
     * @return imoboid/false
     */
    public function insertImobilizado($patrimonio, $entoid, $entioid){
    	$sql = "INSERT INTO imobilizado
    				(imobentoid,imobentioid,imobpatrimonio,imobtecoid,imobimotoid,imobimsoid,
    				imobrelroid,imobitloid,imobpedoid,imobusuoid,imobcadastro,imobexclusao,
    				imobusuoid_exclusao,imobforoid,imobprdoid,imobentrada,imobemissao,imobprimeira_instalacao,
    				imobvalor_custo,imobbaixa,imobibmoid,imobdepreciacao_acumulada,imobdepreciacao_mensal,imobinicio_depreciacao,
    				imobprovisao,imobusuoid_alteracao)
    	
    				(SELECT
    					$entoid,$entioid,imobpatrimonio,imobtecoid,imobimotoid,imobimsoid,
    					imobrelroid,imobitloid,imobpedoid,imobusuoid,imobcadastro,imobexclusao,
    					imobusuoid_exclusao,imobforoid,imobprdoid,imobentrada,imobemissao,imobprimeira_instalacao,
    					imobvalor_custo,imobbaixa,imobibmoid,imobdepreciacao_acumulada,imobdepreciacao_mensal,imobinicio_depreciacao,
    					imobprovisao,imobusuoid_alteracao
    				FROM
    					sbtec.imobilizado where imobpatrimonio = $patrimonio)
    				RETURNING
    					imoboid;";
    	
    	$sql = pg_query($this->conn, $sql);
    	 
    	if(pg_affected_rows($sql) > 0){
    		return pg_fetch_result($sql, 0, 'imoboid');
    	} else{
    		return false;
    	}
    }
    
    /**
     * Atualiza dados do equipamento.
     * 
     * @param int $patrimonio
     * @param array $arrayDados (array com dados do equipamento)
     * @return equoid/false
     */
    public function updateEquipamento($patrimonio, $arrayDados){
		$sql = "
			UPDATE
				equipamento
    		SET
    			equno_serie = ".$arrayDados['equno_serie'].",
		    	equaraoid = ".$arrayDados['equaraoid'].",
		    	equesn = '".$arrayDados['equesn']."',
		    	equrelroid = ".$arrayDados['equrelroid'].",
		    	equtipo = '".$arrayDados['equtipo']."',
		    	equno_fone = ".$arrayDados['equno_fone'].",
		    	equeqsoid = ".$arrayDados['eqsoid'].",
		    	equeveoid = ".$arrayDados['equeveoid'].",
		    	equeqmoid = ".$arrayDados['equeqmoid'].",
		    	equeqfoid = ".$arrayDados['equeqfoid'].",
		    	equprdoid = ".$arrayDados['equprdoid'].",
		    	equversao_hardware = ".$arrayDados['equversao_hardware'].",
		    	equversao_firmware = ".$arrayDados['equversao_firmware'].",
		    	equid_sascarga = ".$arrayDados['equid_sascarga']."
    		WHERE
    			equpatrimonio = $patrimonio
			AND
				equno_serie IS NULL
			RETURNING
				equoid;";
		
		$sql = pg_query($this->conn, $sql);
		
		if(pg_affected_rows($sql) > 0){
			return pg_fetch_result($sql, 0, 'equoid');
		} else{
			return false;
		}
    }
    
    /**
     * Atualiza imobilizado.
     * 
     * @param int $patrimonio
     * @param int $imoboid
     * @return imoboid/false
     */
    public function updateImobilizado($patrimonio, $imoboid){
    	$sql = "UPDATE
    				imobilizado
    			SET
    				imobserial = (SELECT equno_serie FROM equipamento WHERE equpatrimonio = $patrimonio) WHERE imoboid = $imoboid
    			RETURNING
    				imoboid;";
    	
    	$sql = pg_query($this->conn, $sql);
    	
    	if(pg_affected_rows($sql) > 0){
    		return pg_fetch_result($sql, 0, 'imoboid');
    	} else{
    		return false;
    	}
    }
    
    /**
     * Insere a linha.
     * 
     * @param int $equno_fone
     * @param int $equaraoid
     * @return linoid/false
     */
    public function insertLinha($equno_fone, $equaraoid){
    	$sql = "INSERT INTO linha
    				(linnumero,lincadastro,linusuoid,linoploid,linaraoid,linobs,linexclusao,
    				linhabilitacao,lincsloid,linclasse,lincid,linlscoid,linroaming_int,linban,
    				linlintoid,linsuspensao,linpotoid,linusuoid_susp,lindt_alteracaocid,lindt_alteracaontc)
    	
    				(SELECT
    					linnumero,lincadastro,linusuoid,linoploid,linaraoid,linobs,linexclusao,
    					linhabilitacao,lincsloid,linclasse,lincid,linlscoid,linroaming_int,linban,
    					linlintoid,linsuspensao,linpotoid,linusuoid_susp,lindt_alteracaocid,lindt_alteracaontc
    				FROM
    					sbtec.linha
    				WHERE
    					linnumero = $equno_fone
    				AND
    					linaraoid = $equaraoid)
    			RETURNING
    				linoid";
    	
    	$sql = pg_query($this->conn, $sql);
    	
    	if(pg_affected_rows($sql) > 0){
    		return pg_fetch_result($sql, 0, 'linoid');
    	} else{
    		return false;
    	}
    }
    
    public function getLinhaSBTEC($equno_fone, $equaraoid){
        $sql = "SELECT
                    linoid
    			FROM
    				sbtec.linha
    			WHERE
    				linnumero = $equno_fone
    			AND
                    linaraoid = $equaraoid;";
                   
        $sql = pg_query($this->conn, $sql);
                    
        $linoid = 0;
    	 
    	if(pg_num_rows($sql) > 0){
    		$linoid = pg_fetch_result($sql, 0, 'linoid');
    	}
    	 
    	return $linoid;
    }
    
    public function insertCelular($equno_fone, $equaraoid, $linoid, $linoid_sbtec){
    	$sql = "INSERT INTO celular
    				(cellinoid,celesn,cellinha,celdt_cadastro,celusuoid,celstatus,celmcloid,celoploid,
    				celaraoid,celobs,celforoid,celdt_remessa,cellote,celchicote,celhabilitacao,celcsloid,
    				celtemp,celversao,celcvooid,celdt_fabricacao,celgps,celgarantia)
    	
    				(SELECT
    					$linoid,celesn,cellinha,celdt_cadastro,celusuoid,celstatus,celmcloid,celoploid,
    					celaraoid,celobs,celforoid,celdt_remessa,cellote,celchicote,celhabilitacao,celcsloid,
    					celtemp,celversao,celcvooid,celdt_fabricacao,celgps,celgarantia
    				FROM
    					sbtec.celular
    				WHERE
    					celdt_exclusao IS NULL
    				AND
    					((cellinha = $equno_fone AND celaraoid = $equaraoid) OR cellinoid = $linoid_sbtec)
                    )
    			RETURNING
    				celoid;";
    	
    	$sql = pg_query($this->conn, $sql);
    	 
    	if(pg_affected_rows($sql) > 0){
    		return pg_fetch_result($sql, 0, 'celoid');
    	} else{
    		return false;
    	}
    }
    
    /**
     * Insere histórico imobilizado.
     * 
     * @param string $observacao
     * @param int $imoboid
     * @param int $usuoid
     */
    public function insertHistoricoImobilizado($observacao, $imoboid, $usuoid){
    	$sql = "INSERT INTO imobilizado_historico
    				(imobhmotivo,imobhobs,imobhbaixa,imobhibmoid,imobhusuoid,imobhimsoid,imobhimoboid,imobhrelroid)
    				(SELECT
    					'Transferência Estoque (E)', '$observacao',now(),3,$usuoid,imobimsoid,imoboid,imobrelroid
    				FROM
    					imobilizado
    				WHERE
    					imoboid = $imoboid);";
    	    	
    	$sql = pg_query($this->conn, $sql);
    }
    
    /**
     * Insere histórico equipamento.
     * 
     * @param string $observacao
     * @param int $equno_serie
     * @param int $usuoid
     */
    public function insertHistoricoEquipamento($observacao, $equno_serie, $usuoid){
    	$sql = "INSERT INTO historico_equipamento 
         			(hiemotivo,hieobservacao,hieusuoid,hieeqsoid,hierelroid,hieesn, hientc,hiearaoid,hieeveoid)
         			(SELECT
    					'Trasnferência de Esquema','$observacao',$usuoid,equeqsoid,equrelroid,equesn,equno_fone,equaraoid,equeveoid
    				FROM
    					sbtec.equipamento
    				WHERE
    					equno_serie = $equno_serie);";
    	
    	$sql = pg_query($this->conn, $sql);
    }
    
    /**
     * @param int $patrimonio
     */
    public function updateImobilizadoSBTEC($patrimonio){
    	$sql = "UPDATE sbtec.imobilizado SET imobimsoid = 99 WHERE imobpatrimonio = $patrimonio;";
    	$sql = pg_query($this->conn, $sql);
    }
    
    /**
     * @param string $observacao
     * @param int $patrimonio
     */
    public function updateImobilizadoHistoricoSBTEC($observacao, $patrimonio, $usuoid){
    	$sql = "INSERT INTO sbtec.imobilizado_historico
    				(imobhimsoid,imobhmotivo,imobhobs,imobhbaixa,imobhibmoid,imobhusuoid,imobhimoboid,imobhrelroid)
    				(SELECT 50,'Estoque (B)','$observacao',now(),3,$usuoid,imoboid,imobrelroid FROM imobilizado WHERE imobpatrimonio = $patrimonio);";
    	$sql = pg_query($this->conn, $sql);
    }
    
    /**
     * @param int $patrimonio
     */
    public function updateEquipamentoSBTEC($patrimonio){
    	$sql = "UPDATE sbtec.equipamento SET equeqsoid = 99 WHERE equpatrimonio = $patrimonio;";
    	$sql = pg_query($this->conn, $sql);
    }
    
    /**
     * @param string $observacao
     * @param int $equno_serie
     */
    public function updateHistoricoEquipamentoSBTEC($observacao, $equno_serie, $usuoid){
    	$sql = "INSERT INTO sbtec.historico_equipamento 
         			(hiemotivo,hieobservacao,hieusuoid,hieeqsoid,hierelroid,hieesn, hientc,hiearaoid,hieeveoid)
         			(SELECT
    					'Trasnferência de Esquema','$observacao',$usuoid,50,equrelroid,equesn,equno_fone,equaraoid,equeveoid
    				FROM
    					sbtec.equipamento
    				WHERE
    					equno_serie = $equno_serie);";
    	$sql = pg_query($this->conn, $sql);
    }

    /**
     * Verifica se existe o número de série.
     *
     * @param int $serie
     * @return int
     */
    public function serieExists($serie){
        $sql = "SELECT
                    equno_serie
                FROM
                    public.equipamento
                WHERE
                    equno_serie = $serie;";

        $sql = pg_query($this->conn, $sql);
        return pg_num_rows($sql);
    }
}
?>