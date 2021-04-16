<?php
/**
 * PrnMigracaoLoteDAO.php
 * 
 * Classe de persistência dos dados
 * 
 * @author Alex Sandro Médice
 * @email alex.medice@meta.com.br
 * @since 14/10/2012
 * @package Principal
 *
 */
class PrnMigracaoLoteDAO {

    private $conn;
    private $conusuoid;

    /*
     * Construtor
     */
    public function __construct($conn, $conusuoid) {

        $this->conn = $conn;
        $this->conusuoid = $conusuoid;
    }
    
    /**
     * Cancela o agendamento da OS
     * @param $os, $usuario
     * @return boolean
     */
    public function ativarContrato($contrato) {
    	
    	$sql = "UPDATE 
    				contrato
    			SET 
    				conno_tipo = ".intval($contrato->tpcoidcorrespondente_ex)." ,
    				condt_exclusao = null ,
    				conusuoid_exclusao = null,
    				condt_alteracao = now() ,
    				concsioid = 1
    			WHERE connumero = ".intval($contrato->contrato);    	
    	
    	ob_start();
    	$query = pg_query($sql);
    	ob_end_clean();
    
    	if ($query) {
    		return true;
    	} else {
    		return false;
    	}
    }
    /**
     * Busca o contrato pelo $connumero a ser migrado
     * 
     * @param int $connumero
     * @return array
     */
    public function contrato($connumero, $isVerificarBloqueiado=false) {
    	
    	$sql = '
			SELECT 
		    	tpc.tpcseguradora,
		    	c.connumero AS contrato,
		    	vei.veichassi AS chassi,
		    	vei.veiplaca AS placa,
		    	tpc.tpcdescricao AS tipo_contrato,
		    	(SELECT x.tpcdescricao FROM tipo_contrato x WHERE x.tpcoid = tpc.tpcoidcorrespondente_ex) AS tipo_contrato_correspondente,
		    	c.condt_exclusao AS data_exclusao,
		    	c.condt_quarentena_seg AS quarentena, 
    			tpc.tpcoidcorrespondente_ex,
    			tpc.tpcoid,
    			c.coneqcoid,
    			c.concsioid
    		FROM 
    			contrato AS c
    		INNER JOIN
    			veiculo AS vei ON c.conveioid = vei.veioid 
    		INNER JOIN
    			tipo_contrato AS tpc ON c.conno_tipo = tpc.tpcoid 
    		WHERE 
    			c.connumero = '.intval($connumero).' 
    	';
    	
    	$rs = pg_query($this->conn, $sql);    	

    	$contrato = null;
    	if (!$rs) {
    		throw new Exception('Falha na pesquisa do contrato: '.$connumero);
    	}
    	if (pg_num_rows($rs) > 1) {
    		throw new Exception('Foi encontrato mais de um contrato para: '.$connumero);
    	}
    		
    	$contrato = pg_fetch_object($rs);
    	if (!$contrato) {
    		throw new Exception('Contrato não encontrado: '.$connumero);
    	}
    	
    	$contrato->is_contrato_bloqueado = false;
    	if ($isVerificarBloqueiado) {
	    	$contrato->is_contrato_bloqueado = $this->isContratoBloqueado($contrato->data_exclusao, $contrato->tpcseguradora, $contrato->tpcoid, $contrato->tpcoidcorrespondente_ex);
	    	//if ($contrato->is_contrato_bloqueado == 1) {
	    	//	throw new Exception('Contratos excluídos, contratos que não são de Seguradora e Contratos cujo tipo não possue um tipo Ex parametrizado não podem ser migrados: '.$connumero);
	    	//}
    	}
    	
    	return $contrato;
    }
    
    /**
     * Busca contratos pelos números de contratos migrados
     * 
     * @param array $contratosmigrados
     * @return array
     */
    public function contratosMigrados($contratosmigrados) {
    
    	$inContratos = array();
    	foreach ($contratosmigrados as $connumero_migrado) {
    		$connumero_migrado = trim($connumero_migrado);
    		$inContratos[] = "'".pg_escape_string($connumero_migrado)."'";
    	}
    	$inContratos = implode(',', $inContratos);
    	
    	$sql = '
    		SELECT 
	    		c.connumero_migrado AS contrato_original, 
	    		(SELECT x.condt_exclusao FROM contrato x WHERE x.connumero = c.connumero_migrado) AS data_cancelamento, 
	    		c.connumero AS novo_contrato, 
	    		vei.veiplaca AS placa, 
	    		tpc.tpcdescricao AS tipo_contrato, 
	    		(SELECT ordoid FROM ordem_servico WHERE ordstatus = 4 AND ordconnumero = c.connumero) AS os ,
    			c.concsioid,
    			tpc.tpcoid
    		FROM 
    			contrato AS c 
    		INNER JOIN 
    			veiculo AS vei ON c.conveioid = vei.veioid 
    		INNER JOIN 
    			tipo_contrato AS tpc ON c.conno_tipo = tpc.tpcoid 
    		WHERE 
    			c.connumero IN ('.$inContratos.'); 
    	';
    	
    	ob_start();
    	$rs = pg_query($this->conn, $sql);
    	ob_end_clean();
    	
    	if (!$rs) {
    		throw new Exception('Falha na pesquisa dos contratos migrados: '.$inContratos);
    	}
    	
    	$contratos = array();
        while($contrato = pg_fetch_object($rs)) {
        	$contratos[] = $contrato;
        }
    	
    	return $contratos;
    }
    
    /**
     * Busca contratos pelos chassis dos veículos a serem migrados
     * 
     * @param array $chassis
     * @param array $order
     * @param array $order_d
     * @return array
     */
    public function contratosPorChassi($chassis, $order='', $sort='', $ativando=false) {

    	$order = pg_escape_string($order);
    	$sort = pg_escape_string($sort);
    	
    	$inChassi = array();
    	foreach ($chassis as $chassi) {
    		$inChassi[] = "'".pg_escape_string($this->conn, trim($chassi))."'";
    	}
    	$inChassi = implode(',', $inChassi);
    	    	
    	$sql = '
			SELECT 
		    	tpc.tpcseguradora,
		    	c.connumero AS contrato,
		    	vei.veichassi AS chassi,
		    	vei.veiplaca AS placa,
		    	tpc.tpcdescricao AS tipo_contrato,
		    	(SELECT x.tpcdescricao FROM tipo_contrato x WHERE x.tpcoid = tpc.tpcoidcorrespondente_ex) AS tipo_contrato_correspondente,
		    	c.condt_exclusao AS data_exclusao,
		    	c.condt_quarentena_seg AS quarentena, 
    			tpc.tpcoidcorrespondente_ex,  
    			(
    				SELECT 
				    	COUNT(*)
		    		FROM 
		    			contrato AS c2
		    		INNER JOIN
		    			veiculo AS vei2 ON c2.conveioid = vei2.veioid 
		    		INNER JOIN
		    			tipo_contrato AS tpc2 ON c2.conno_tipo = tpc2.tpcoid 
    				WHERE vei2.veichassi = vei.veichassi 
		    		GROUP BY 
		    			vei2.veichassi 
    			) AS quantidade_por_chassi ,
    			c.concsioid,
    			tpc.tpcoid
    		FROM 
    			contrato AS c
    		INNER JOIN
    			veiculo AS vei ON c.conveioid = vei.veioid 
    		INNER JOIN
    			tipo_contrato AS tpc ON c.conno_tipo = tpc.tpcoid 
    		WHERE 
    			vei.veichassi IN ('.$inChassi.') 
	    	ORDER BY 
    	';
    	
    	switch ($order) {
    		case 'contrato':
    			$sql .= 'c.connumero '.$sort;
    			break;
    		case 'tipo_contrato':
    			$sql .= 'tpc.tpcdescricao '.$sort;
    			break;
    		default:
    			$sql .= 'vei.veichassi, vei.veiplaca, c.connumero ';
    			break;
    	}
    	
    	$rs = pg_query($this->conn, $sql);
    	
    	if (!$rs) {
    		throw new Exception('Falha na pesquisa dos contratos pelos chassi: '.$inChassi);
    	}
    	
    	$contratos = array();
        while($contrato = pg_fetch_object($rs)) {
        	$contrato->is_contrato_bloqueado = $this->isContratoBloqueado($contrato->data_exclusao, $contrato->tpcseguradora, $contrato->tpcoid, $contrato->tpcoidcorrespondente_ex, $ativando, $contrato->concsioid==1);
  	
        	$contratos[] = $contrato;
        }
    	
    	return $contratos;
    }
    
    /**
     * Verifica se o contrato pode ser migrado
     * 
     * 1 – data_exclusao = não nula
     * 2 – tpcseguradora = false
     * 3 – tpcoidcorrespondente_ex = nulo
     * 
     * Não podem ter o checkbox ativo pois não podem ser migrados, porén devem ser apresentados ao usuário. 
     * Contratos excluídos, contratos que não são de Seguradora e Contratos cujo tipo não possue um tipo Ex parametrizado não podem ser migrados. 
     * 
     * @param string $data_exclusao
     * @param string $tpcseguradora
     * @param string $tpcoidcorrespondente_ex
     * @return boolean
     */
    private function isContratoBloqueado($data_exclusao, $tpcseguradora, $tpcoid, $tpcoidcorrespondente_ex, $ativando=false, $ativo=false) {
    	
    	//para ativar, regras retornam false para contratos desativados
    	if($ativando){
    		// 3 – mesmo que tipo seja para ativacao tipo Ex-Bradesco, ativar para Bradesco, 
    		//é preciso ter Bradesco como $tpcoidcorrespondente_ex definido em update.
	    	if (empty($tpcoidcorrespondente_ex)) {
	    		return true;
	    	}
	    	// se contrato estiver ativo, nao habilitar para ativar.
// 	    	if ($ativo==true) {
// 	    		return true;
// 	    	}
			if($tpcoid!=41){
				return true;
			}
	    	return false;
    	}
    	else {    	
	    	// 1 – data_exclusao = não nula
	    	if (!empty($data_exclusao)) {
	    		return true;
	    	}
	
	    	// 2 – tpcseguradora = false
	    	if ($tpcseguradora == 'f') {
	    		return true;
	    	}
	    	
	    	// 3 – tpcoidcorrespondente_ex = nulo
	    	if (empty($tpcoidcorrespondente_ex)) {
	    		return true;
	    	}
    	}    	
    	
    	return false;
    }

    
	/**
	 * Cancela OS de retirada.
	 * @param int $contrato
	 * @param int $usuario
	 * @param string $motivo
	 * @return number
	 */
    public function cancelarOS($contrato, $usuario, $motivo) {

    	$sql = "SELECT 
    				ordoid 
    			FROM 
    				ordem_servico
    			INNER JOIN 
    				ordem_servico_item ON ositordoid = ordoid
    			INNER JOIN 
    				os_tipo_item ON otioid = ositotioid
    			WHERE 
    				ordconnumero = ".intval($contrato)."
    			AND 
    				ordstatus NOT IN (3, 9)
    			AND 
    				otioid = 8";
    	
    	ob_start();
    	$query = pg_query($sql);
    	ob_end_clean();

    	if ($query) {
    		if (pg_num_rows($query) > 0) {
    			while ($row = pg_fetch_object($query)) {
    				$sqlCancelarItens = "UPDATE ordem_servico_item
    										SET ositstatus = 'X'
    									WHERE ositordoid = ".$row->ordoid;
    				
    				ob_start();
    				$queryCancelarItens = pg_query($sqlCancelarItens);
    				ob_end_clean();

    				if ($queryCancelarItens) {
    					if (!(pg_affected_rows($queryCancelarItens) > 0))
    						return 2;
    				}
    				 
    				$sqlCancelar = "UPDATE ordem_servico
    								SET ordstatus = 9
    								WHERE ordoid = ".$row->ordoid;
    				
    				ob_start();
    				$queryCancelar = pg_query($sqlCancelar);
    				ob_end_clean();

    				if ($queryCancelar) {
    					if (!(pg_affected_rows($queryCancelar) > 0))
    						return 1;
    				}

    				if (!$this->cancelarAgendamentosOS($row->ordoid, $usuario, $motivo))
    					return 5;
    				
    				$queryHistorico = $this->inserirHistoricoOS($row->ordoid, $usuario, $motivo, '9');
    				if ($queryHistorico) {
    					if ($queryHistorico==0)
    						return 4;
    				}
    			}
    		}
    		return 3;
    	} else {
    		return 1;
    	}
    }
    
    /**
     * Inserir Histórico da OS 
     * @param $os, $usuario
     * @return boolean
     */
    public function inserirHistoricoOS($os, $usuario, $motivo, $status) {
    
    	//Inserir Histórico
    	$sqlHistorico = "INSERT INTO ordem_situacao
    	(orsordoid, orssituacao, orsusuoid, orsstatus) VALUES
    	(".intval($os).", '".pg_escape_string($motivo)."', ".intval($usuario).",  ".intval($status).")";
    	
    	ob_start();
    	return pg_affected_rows(pg_query($sqlHistorico));
    	ob_end_clean();	
    }
    
    /**
     * Cancela o agendamento da OS
     * @param $os, $usuario
     * @return boolean
     */
    public function cancelarAgendamentosOS($os, $usuario, $motivo) {

    	$sql = "UPDATE ordem_servico_agenda
    	SET osaexclusao = NOW(),
    	osausuoid_excl = $usuario,
    	osamotivo_excl = '".pg_escape_string($motivo)."'
    	WHERE osaordoid = $os";
    	ob_start();
    	$query = pg_query($sql);
    	ob_end_clean();

    	if ($query) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * Gera nova OS para o novo contrato gerado
     *
     * @param int $connumero_novo Número do novo contrato*
     * @param int $eqcoid_serv Classe do contrato antigo
     * @return boolean
     */
    public function gerarOS($connumero_novo, $eqcoid_serv) {
    	$sql = "SELECT numero_ordem_servico(NEXTVAL('ordem_servico_ordoid_seq'::text)) AS ordoid";
    	$rs = pg_query($this->conn, $sql);
    	
    	if (!$rs) {
    		throw new Exception('Falha na pesquisa do novo número de OS.');
    	}
    	if (pg_num_rows($rs) <= 0) {
    		throw new Exception('Falha ao gerar novo número de OS.');
    	}

    	$ordoid = pg_fetch_result($rs, 0, "ordoid");
    	if (!$ordoid) {
    		throw new Exception('Novo número da ordem de servico não encontrado.');
    	}
    	
    	$sql = '
    		INSERT INTO ordem_servico (
    			ordoid, ordveioid, ordclioid,
		    	ordequoid, ordeveoid, ordstatus,
		    	ordmtioid, orddesc_problema, ordusuoid,
		    	ordconnumero, ordrelroid
		    )
	    	SELECT 
	    		'.$ordoid.', conveioid, conclioid, conequoid, 
		    	(SELECT equeveoid FROM equipamento WHERE equoid=conequoid), 
	    		4, 5, \'Migração termo \', '.$this->conusuoid.', connumero, 752
	    	FROM 
	    		contrato
	    	WHERE 
	    		connumero = '.$connumero_novo.';
		    			
	    	INSERT INTO ordem_situacao (
	    		orsordoid, orsusuoid, orssituacao, orsstatus
	    	)
	    	SELECT 
			    ordoid, '.$this->conusuoid.', \'RETIRADA EM VIRTUDE DE MIGRAÇÃO\', ordstatus
	    	FROM 
			    ordem_servico
	    	WHERE 
			    ordoid = '.$ordoid.';';
    	 
    	//Insere o serviço de RETIRADA
    	$sql .= " 
	    	SELECT ordem_servico_item_i(
		    	'{  \"8\"
		    	\"$ordoid\"
		    	\"\"
		    	\"$eqcoid_serv\"
		    	\"MIGRAÇÃO \"
		    	\"P\" }'
    		) AS ositoid;";
    	
    	$rs = pg_query($this->conn, $sql);
    	 
    	if (!$rs) {
    		throw new Exception('Falha ao gerar a nova OS.');
    	}
    	
    	//Serviço de RETIRADA ACESSÓRIOS
    	$sql= "SELECT
			    	otioid,
			    	otidescricao
		    	FROM
			    	contrato_servico,
			    	os_tipo_item
		    	WHERE
		    		--consrefioid>0 --Mantis nº7062 - Autorizado pelo Thadeu Rocha
		    	otidt_exclusao IS NULL
		    	AND  otiobroid=consobroid
		    	AND  otiostoid=3
		    	AND  otitipo = 'A'
		    	AND  consiexclusao IS NULL
		    	AND  consconoid='.$connumero_novo.';";
    	 
    	$rs = pg_query($sql);
    	if(pg_num_rows($rs) > 0){

    		for ($i=0;pg_num_rows($rs) > $i; $i++){
    			 
    			$servico = pg_fetch_result($rs, $i, 'otioid');
    			$descricao_servico = pg_fetch_result($rs, $i, 'otidescricao');

    			$sql = "INSERT INTO
			    			ordem_servico_item
			    			(
				    			ositotioid,
				    			ositordoid,
				    			ositeqcoid,
				    			ositobs,
				    			ositstatus
			    			)
				    		(SELECT
				    			$servico,
				    			$ordoid,
				    			(SELECT coneqcoid FROM contrato WHERE connumero = '.$connumero_novo.' LIMIT 1),
				    			'Retirada de Acessório ($descricao_servico) por motivo de rescisão por inadimplência',
				    			'P'
			    			);";
    			 
    			ob_start();
    			$rs = pg_query($sql);
    			ob_end_clean();
    			if (!$rs) {
    				throw new Exception('Falha ao gerar a nova OS.');
    			}
    		}
    	}   	
    	
    	return true;
    }

}