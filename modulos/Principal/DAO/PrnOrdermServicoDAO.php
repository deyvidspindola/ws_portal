<?php

class PrnOrdermServicoDAO {
	
	const ERRO_CLIENTE_INDICADOR = 'Não foi possível gerar crédito ao cliente indicador';
	
    private $conn;
    
    /**
     * Inicia uma transação com o o bancod e dados
     */
    public function iniciarTransacao(){
    
    	pg_query($this->conn, "BEGIN;");
    }
    
    /**
     * aborta uma transação com o o bancod e dados
     */
    public function abortarTransacao(){
    
    	pg_query($this->conn, "ROLLBACK;");
    }
    
    /**
     * comita uma transação com o o bancod e dados
     */
    public function comitarTransacao(){
    
    	pg_query($this->conn, "COMMIT;");
    }
    

    public function getEmailCliente($ordoid) {

        $sql = "
            SELECT 
                c.cliemail AS email
            FROM 
                clientes AS c
            INNER JOIN
                ordem_servico AS os ON c.clioid = os.ordclioid
            WHERE 
                os.ordoid = $ordoid;
            ";

        $rs = pg_query($this->conn, $sql);

        $email = "";

        if (pg_num_rows($rs) > 0) {
            $email = pg_fetch_result($rs, 0, 'email');
        }

        return $email;
    }

    public function getEmailClienteNf2($ordoid) {

        $sql = "
            SELECT 
                c.cliemail_nfe2 AS email
            FROM 
                clientes AS c
            INNER JOIN
                ordem_servico AS os ON c.clioid = os.ordclioid
            WHERE 
                os.ordoid = $ordoid;
            ";

        $rs = pg_query($this->conn, $sql);

        $email = "";

        if (pg_num_rows($rs) > 0) {
            $email = pg_fetch_result($rs, 0, 'email');
        }

        return $email;
    }

    public function getEmailGerenciadora($ordoid) {
        $sql = "
            SELECT 
                g.geremail AS email
            FROM 
                ordem_servico os
            INNER JOIN
                contrato AS c ON os.ordconnumero = c.connumero
            INNER JOIN
                contrato_gerenciadora cg ON c.connumero = cg.congconnumero
            INNER JOIN
                gerenciadora AS g 
                ON 
                    cg.conggeroid1 = g.geroid 
                OR 
                    cg.conggeroid2 = g.geroid 
                OR 
                    cg.conggeroid3 = g.geroid
            WHERE 
                os.ordoid = $ordoid;
            ";        

        $rs = pg_query($this->conn, $sql);

        $emails = array();

        if (pg_num_rows($rs) > 0) {
            for ($i = 0; $i < pg_num_rows($rs); $i++) {
                
                $emails_gerenciadora = str_replace(';', ',', pg_fetch_result($rs, $i, 'email'));
                $emails_gerenciadora = explode(',', $emails_gerenciadora);
                
                foreach($emails_gerenciadora as $email_gerenciadora) {
                    $emails[] = trim($email_gerenciadora);
                }                               
            }
        }

        return $emails;
    }

    public function getEmailsOs($ordoid) {
        $sql = "
            SELECT 
                eos.eosemail AS email
            FROM 
                ordem_servico os
            INNER JOIN
                email_ordem_servico AS eos ON os.ordoid = eos.eosordoid            
            WHERE 
                os.ordoid = $ordoid;
            ";

        $rs = pg_query($this->conn, $sql);

        $emails = array();

        if (pg_num_rows($rs) > 0) {
            for ($i = 0; $i < pg_num_rows($rs); $i++) {
                $emails[] = pg_fetch_result($rs, $i, 'email');
            }
        }

        return $emails;
    }

    public function getPlacaVeiculoByOs($ordoid) {

        $sql = "
            SELECT 
                v.veiplaca AS placa
            FROM 
                ordem_servico os
            INNER JOIN
                veiculo AS v ON v.veioid = os.ordveioid            
            WHERE 
                os.ordoid = $ordoid;
            ";

        $rs = pg_query($this->conn, $sql);

        $placa = "";

        if (pg_num_rows($rs) > 0) {
            $placa = pg_fetch_result($rs, 0, 'placa');
        }

        return $placa;
    }

    public function getNomeInstaladorByOs($ordoid) {

        $sql = "
            SELECT 
                i.itlnome AS nome_instalador
            FROM 
                ordem_servico os
            INNER JOIN
                instalador AS i ON i.itloid = os.orditloid
            WHERE 
                os.ordoid = $ordoid;
            ";

        $rs = pg_query($this->conn, $sql);

        $nome_instalador = "";

        if (pg_num_rows($rs) > 0) {
            $nome_instalador = pg_fetch_result($rs, 0, 'nome_instalador');
        }

        return $nome_instalador;
    }
    
    public function getStatus($ordoid) {
        $sql = "
            SELECT 
                os.ordstatus AS status_os
            FROM 
                ordem_servico os            
            WHERE 
                os.ordoid = $ordoid;
            ";

        $rs = pg_query($this->conn, $sql);

        $status = 0;

        if (pg_num_rows($rs) > 0) {
            $status = pg_fetch_result($rs, 0, 'status_os');
        }

        return $status;
    }

    public function getMotivoOs($ordoid) {
        $sql = "
            SELECT (CASE WHEN ordstatus = 9
                        THEN (SELECT concatena(ostdescricao||'-'||otidescricao) FROM ordem_servico_item,os_tipo_item,os_tipo WHERE otioid = ositotioid AND ostoid = otiostoid AND ositordoid = ordoid AND ositstatus NOT IN ('N') AND ositexclusao IS NULL) 
                        ELSE (SELECT concatena(ostdescricao||'-'||otidescricao) FROM ordem_servico_item,os_tipo_item,os_tipo WHERE otioid = ositotioid AND ostoid = otiostoid AND ositordoid = ordoid AND ositstatus NOT IN ('X','N') AND ositexclusao IS NULL)
                        END) AS motivo_os
            FROM ordem_servico
            WHERE ordoid = $ordoid;
            ";

        $rs = pg_query($this->conn, $sql);

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_result($rs, 0, 'motivo_os');
        }

        return null;
    }
	
	public function getClasseContrato($ordoid) {
        $sql = "SELECT ordoid, ordconnumero, conno_tipo FROM ordem_servico JOIN contrato ON ordconnumero = connumero WHERE ordoid = $ordoid";

        $rs = pg_query($this->conn, $sql);

        if (pg_num_rows($rs) > 0) {
            return pg_fetch_result($rs, 0, 'conno_tipo');
        }

        return null;
    }

    public function __construct() {
        global $conn;

        $this->conn = $conn;
    }

    public function dadosCliente($ordoid){
    
    	$sql = "
				SELECT 
	               c.*
	            FROM 
	                clientes AS c
	            INNER JOIN
	                ordem_servico AS os ON c.clioid = os.ordclioid
	            WHERE 
	                os.ordoid = $ordoid";
    
    	$query   = pg_query($this->conn, $sql);
    	$retorno = pg_fetch_all($query);
    
    	return $retorno[0];
    }
    
    public function dadosOS($clioid, $ordoid){
    
    	$sql = "
		    	SELECT
		    		V.veiplaca, V.veichassi , OS.ordconnumero
		    	FROM
		    		veiculo AS V
		    	JOIN
		    		ordem_servico OS ON  OS.ordveioid = V.veioid
		    	WHERE
		    		OS.ordclioid = $clioid
		    	AND Os.ordoid = $ordoid
    	";
    
    	$query   = pg_query($this->conn, $sql);
    	$retorno = pg_fetch_all($query);
    
    	return $retorno[0];
    }
    
    public function dadosRemetente($servidor) {
    	$sql = "
		    	SELECT
		    		*
		    	FROM
		    		servidor_email
		    	WHERE srvoid = $servidor
    	";
    
    	$res   = pg_query($this->conn, $sql);
    	$retorno = pg_fetch_all($res);
    	return $retorno[0];
    }
    
    public function registraHistoricoOS($ordoid, $msg){
    
    	$sql = "
    			INSERT INTO 
					ordem_situacao (orsordoid, orsusuoid, orssituacao, orsdt_situacao) 
				VALUES ($ordoid, 4873, '$msg', now())";
    
    	$query   = pg_query($this->conn, $sql);
    	$retorno = (!$query) ? false : true;
    
    	return $retorno;
    }
    
    public function verificaHistorico($ordoid, $texto) {
    	$sql = "
    			SELECT 	* 
    			FROM 	ordem_situacao 
    			WHERE 	orsordoid = $ordoid 
    		";
    	if (!empty($texto)){
    		$sql .= " AND orssituacao = '{$texto}'";
    	}
    	
    	$query   = pg_query($this->conn, $sql);

    	$count = pg_num_rows($query);
    	return $count;
    }
    
    


    /**
     * Verifica se o contrato da OS tem um amigo indicador que deverá receber crédito
     *
     * @param int $ordoid
     * @return boolean
     *
     */
    public function verificarElegivelAmigoIndicador($ordoid) {
    
    	$existe = false;
    
    	$sql = "
    		SELECT
    			COUNT(cfciordoid) AS resultado,
    			COUNT(DISTINCT cfciclioid) AS total_indicadores
    		FROM
    			credito_futuro_cliente_indicador
    		INNER JOIN
    			contrato ON connumero = cfcitermo
    		INNER JOIN
    			ordem_servico ON connumero = ordconnumero
    		WHERE
    			ordoid = ".$ordoid;
    	
    	$recordset = pg_query($this->conn, $sql);
    	$linha = pg_fetch_object($recordset);
    
    	$resultado = isset($linha->resultado) ? $linha->resultado : 0;
    	$totalIndicadores = isset($linha->total_indicadores) ? $linha->total_indicadores : 0;
    
    	/*
    	 * Somente se o cliente indicador não teve crédito ref. a ao cliente da OS.
    	*/
    	if($resultado == 0){
    
    		$sql = "
	            SELECT EXISTS(
	                            SELECT
	                                1
	                            FROM
	                                ordem_servico
	                --somente contratos nao migrados e nao excluidos
	                            INNER JOIN
	                                contrato ON
	                                        (
	                                            connumero = ordconnumero
	                                            AND connumero_migrado IS NULL
	                                            AND condt_exclusao IS NULL
	                                        )
	                            INNER JOIN
	                                credito_futuro_cliente_indicador ON (cfcitermo = ordconnumero)
	                            INNER JOIN
	                                ordem_servico_item ON (ositordoid = ordoid)
	                --somente OS do tipo instalacao
	                            INNER JOIN
	                                os_tipo_item ON (otioid = ositotioid and otiostoid = 1)
	                            WHERE
	                                ordoid = ". $ordoid ."
	                --verifica se o cliente indicador ja recebeu credito de alguma OS concluida do cliente indicado
	                            AND cfcieqpto_instalado = FALSE
	                            AND cfciordoid IS NULL
                    --verifica se o cliente indicador ja recebeu credito de contrato indicado manualmente
                                  AND conclioid NOT IN (
                                                        SELECT
                                                            conclioid
                                                        FROM
                                                            credito_futuro
                                                        INNER JOIN
                                                            contrato ON connumero = cfoconnum_indicado
                                                        WHERE
                                                            cfoclioid = cfciclioid AND
                                                            cfoforma_inclusao = 2
                                                        )";
    		/*
    		 * Se houver mais de um cliente indicador, considera o primeiro contrato.
    		*/
    		if($totalIndicadores > 1){
    			$sql .= "
	        			 AND
                            ordconnumero = (
                                            SELECT
                                                con.connumero
                                            FROM
                                                contrato con
                                            WHERE
                                                con.conclioid = ordclioid
                                            ORDER BY
                                                condt_cadastro
                                            LIMIT 1
                                        )
	        			";
    
    		}
    
    		$sql .=") AS existe";
    
    		$recordset = pg_query($this->conn, $sql);
    		
    
    		$linha = pg_fetch_object($recordset);
    
    		$existe = ($linha->existe == 't') ? true : false;
    
    	}
    
    	return $existe;
    
    }
    
    /**
     * Insere Crédito Futuro para o cliente indicador
     *
     * @param stdClass $historico
     * @return int
     * @throws exception
     *
     */
    public function gerarCreditoFuturoClienteIndicador($historico) {
    
    
    	$saldo = 'NULL';
    
    	if($historico->cfcptipo_desconto == 2){
    
    		$saldo = ($historico->cfcpdesconto * $historico->cfcpqtde_parcelas);
    
    		$saldo = floatval($saldo);
    
    	}
    
    	$sql = "
            INSERT INTO
                credito_futuro
                    (
                    cfoclioid,
                    cfousuoid_inclusao,
                    cfoconnum_indicado,
                    cfocfcpoid,
                    cfocfmcoid,
                    cfoobroid_desconto,
                    cfostatus,
                    cfotipo_desconto,
                    cfovalor,
                    cfoforma_aplicacao,
                    cfoforma_inclusao,
                    cfosaldo,
                    cfoobservacao,
                    cfoaplicar_desconto
                    )
            VALUES
                    (
                    ".$historico->cliente.",
                    ".intval($historico->usuario).",
                    ".$historico->connumero.",
                    ".$historico->campanha.",
                    ".$historico->cfmcoid.",
                    ".$historico->cfcpobroid.",
                    ".$historico->cfostatus.",
                    ".$historico->cfcptipo_desconto.",
                    ".$historico->cfcpdesconto.",
                    ".$historico->cfcpaplicacao.",
                    ".$historico->cfoforma_inclusao.",
                    ".$saldo.",
                    '".$historico->cfcpobservacao."',
                    ".$historico->cfcpaplicar_sobre."
                    )
            RETURNING cfooid
        ";
    
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new exception($this::ERRO_CLIENTE_INDICADOR);
    	}
    
    	$cfooid = pg_fetch_result($rs,0,'cfooid');
    
    	for($cont = 0; $cont < $historico->cfcpqtde_parcelas; $cont++){
    
    		$parcela = $cont + 1;
    
    		$sql = "
             INSERT INTO
                credito_futuro_parcela
                    (
                    cfpcfooid,
                    cfpnumero,
                    cfpvalor,
             		cfpativo
                    )
            VALUES
                    (
                    ".$cfooid.",
                    ".$parcela.",
                    ".$historico->cfcpdesconto.",
                    't'
                    )
            ";
    
    		if (!$rs = pg_query($this->conn, $sql)) {
    			throw new exception($this::ERRO_CLIENTE_INDICADOR);
    		}
    
    	}
    
    	return $cfooid;
    
    }
    
    /**
     * Atualiza o cliente indicador com a OS
     *
     * @param int $ordoid
     * @param int $connumero
     * $return object
     * @throws exception
     *
     */
    public function atualizarClienteIndicador($ordoid = null, $connumero) {
    
    	$retorno = array();
    
    	if (empty($connumero)) {
    		throw new exception($this::ERRO_CLIENTE_INDICADOR);
    	}
    
    	$sql = "
            UPDATE
                credito_futuro_cliente_indicador
            SET
                cfcieqpto_instalado = TRUE
        		, cfciusuoid_inclusao = 2750
        		";
    
    	if(!is_null($ordoid)){
    		$sql.= ", cfciordoid = ".$ordoid;
    	}
    	$sql.= "
            WHERE
                cfcitermo = ".intval($connumero)."
            RETURNING
                cfcicfcpoid,
                cfciclioid
        ";
    
    	if (!$rs = pg_query($this->conn, $sql)) {
    		throw new exception($this::ERRO_CLIENTE_INDICADOR);
    	}
    
    	$retorno = pg_fetch_object($rs);
    
    	return $retorno;
    }
    
    /**
     * Busca informações da Campanha relacionada ao Crédito Futuro
     *
     * @param int $cfcpoid
     * @return stdClass
     *
     */
    public function buscarInformacaoesCampanha($cfcpoid) {
    
    	$dados = new stdClass();
    
    	$sql = "
                SELECT
                    cfmcoid,
                    cfcpobroid,
                     (CASE WHEN cfcptipo_desconto = 'P' THEN 1 ELSE 2 END) AS cfcptipo_desconto,
                    cfcpdesconto,
                    cfcpqtde_parcelas,
                    (CASE WHEN cfcpaplicacao = 'I' THEN 1 ELSE 2 END) AS cfcpaplicacao,
                    cfcpobservacao,
                    (CASE WHEN cfcpaplicar_sobre = 'M' THEN 1 ELSE 2 END) AS cfcpaplicar_sobre,
                    cfmcdescricao,
                    obrobrigacao
                FROM
                    credito_futuro_campanha_promocional
                INNER JOIN
                   credito_futuro_motivo_credito on cfmcoid = cfcpcfmccoid
                INNER JOIN
                    obrigacao_financeira ON obroid = cfcpobroid
                WHERE
                    cfcpoid = ".$cfcpoid."
        ";
    
    	$rs = pg_query($this->conn, $sql);
    
    	while ($linha = pg_fetch_object($rs)) {
    
    		$dados->cfmcoid             = isset($linha->cfmcoid)            ? $linha->cfmcoid : 0;
    		$dados->cfcpobroid          = isset($linha->cfcpobroid)         ? $linha->cfcpobroid : 0;
    		$dados->cfcptipo_desconto   = isset($linha->cfcptipo_desconto)  ? $linha->cfcptipo_desconto : 1;
    		$dados->cfcpdesconto        = isset($linha->cfcpdesconto)       ? $linha->cfcpdesconto : 0;
    		$dados->cfcpqtde_parcelas   = isset($linha->cfcpqtde_parcelas)  ? $linha->cfcpqtde_parcelas : 0;
    		$dados->cfcpaplicacao       = isset($linha->cfcpaplicacao)      ? $linha->cfcpaplicacao : 0;
    		$dados->cfcpobservacao      = isset($linha->cfcpobservacao)     ? $linha->cfcpobservacao : '';
    		$dados->cfcpaplicar_sobre   = isset($linha->cfcpaplicar_sobre)  ? $linha->cfcpaplicar_sobre : 0;
    		$dados->cfmcdescricao       = isset($linha->cfmcdescricao)      ? $linha->cfmcdescricao : '';
    		$dados->obrobrigacao        = isset($linha->obrobrigacao)       ? $linha->obrobrigacao : '';
    
    	}
    
    	return $dados;
    }
    
    /**
     * Insere Histórico do Crédito Futuro
     *
     * @param stdClass $dadosHistorico
     * @throws exception
     *
     */
    public function inserirHistoricoCreditoFuturo($dadosHistorico) {
    
    
    	$observacao = "Ordem de serviço encerrada em ".date("d/m/Y")." às ".date("H:i:s")." pelo instalador ".$dadosHistorico->nome_instalador.".";
    
    	$sql = "
            INSERT INTO
                credito_futuro_historico
            (
                cfhusuoid,
                cfhoperacao,
                cfhorigem,
                cfhcfooid,
                cfhstatus,
                cfhobrigacao_desconto,
                cfhtipo_desconto,
                cfhforma_aplicacao,
                cfhaplicar_desconto,
                cfhqtde_parcelas,
                cfhvalor,
                cfhobservacao,
                cfhsaldo_parcelas
            )
            VALUES
            (
                ".$dadosHistorico->usuario.",
                1,
                5,
                ".$dadosHistorico->cfooid.",
                ".$dadosHistorico->cfostatus.",
                ".$dadosHistorico->cfcpobroid.",
                ".$dadosHistorico->cfcptipo_desconto.",
                ".$dadosHistorico->cfcpaplicacao.",
                ".$dadosHistorico->cfcpaplicar_sobre.",
                ".$dadosHistorico->cfcpqtde_parcelas.",
                ".$dadosHistorico->cfcpdesconto.",
                '".$observacao."',
                '".$dadosHistorico->cfhsaldo_parcelas."'
            )
            ";
    
    	if (!pg_query($this->conn, $sql)) {
    		throw new exception($this::ERRO_CLIENTE_INDICADOR);
    	}
    
    }
    
    /**
     * Insere Histórico no Contrato
     *
     * @param stdClass $dadosHistorico
     * @throws exception
     * @return void
     *
     */
    public function inserirHistoricoContrato($dadosHistorico) {
    
    	$obs = "A ordem de serviço desse contrato foi concluída e, por isso, ";
    	$obs .= "foi criado um registro de crédito futuro com Código de Identificação " . $dadosHistorico->cfooid;
    	$obs .= " para o cliente indicador CPF/CNPJ " . $dadosHistorico->doc_indicador;
    
    	$sql = "SELECT historico_termo_i(
                                        ".$dadosHistorico->connumero.",
                                        ".$dadosHistorico->usuario.",
                                        '".$obs."')";
    
    	if (!pg_query($this->conn, $sql)) {
    		throw new exception($this::ERRO_CLIENTE_INDICADOR);
    	}
    
    }
    
    /**
     * Insere historico no cliente
     *
     * @param stdClass $dadosHistorico
     * @param string $obs
     * @throws exception
     * @return void
     */
    public function inserirHistoricoCliente($dadosHistorico, $obs) {
    
    	$sql = "SELECT
                    cliente_historico_i(
                            ".$dadosHistorico->cliente.",
                            ".$dadosHistorico->usuario.",
                            '".$obs."',
                            'A',
                            '',
                            ''
                    )";
    
    	if (!pg_query($this->conn, $sql)) {
    		throw new exception($this::ERRO_CLIENTE_INDICADOR);
    	}
    
    }
    
    /**
     * Busca pelo nome do instalador para histórico
     *
     * @param int $itloid
     * @return object
     *
     */
    public function buscarDadosInstalador($itloid) {
    
    	$sql = "
             SELECT
                itlnome,
                itlno_cpf
             FROM
                instalador
            WHERE
                itloid = ".intval($itloid)."
            ";
    
    	$rs = pg_query($this->conn, $sql);
    
    	if (pg_num_rows($rs)) {
    		return pg_fetch_object($rs);
    	}
    
    	return array();
    
    }
    
    /**
     * Buscar informacoes adicionais da OS
     *
     * @param int $ordoid
     * @return object
     *
     */
    public function buscarDadosOrdemServico($ordoid){
    
    	$sql = "
                SELECT
                    ordconnumero,
                    orditloid,
                    ordusuoid_concl,
        			ordclioid
                FROM
                    ordem_servico
                WHERE
                    ordoid = ".$ordoid."
            ";
    
    	$rs = pg_query($this->conn, $sql);
    
    	if (pg_num_rows($rs)) {
    		return pg_fetch_object($rs);
    	}
    
    	return array();
    }
    
    /**
     * Buscar dados extras da OS e Contrato
     *
     * @param int $ordoid
     * @return object
     *
     */
    public function buscarDadosOsContrato($ordoid){
    
    	$sql = "
                SELECT 
					eqcecgoid as grupo, 	  -- classe grupo 2 = carga
					ordstatus as status, 	  -- status da od 4 = autorizada
					concatena(ostoid) as servico,  	  -- tipo servico 1 = instalacao, 2 = reinstalacao, 4 = assistencia
					conequoid_antigo, -- antigo equipamento no contrato
					conequoid, 	  -- atual equipamento no contrato
					ordequoid, 	  -- equipamento na OS
    				(conequoid_antigo = ordequoid AND ordequoid <> conequoid AND conequoid_antigo <> conequoid ) as troca,
    				equesn, 
    				consobroid
				FROM ordem_servico
				INNER JOIN ordem_servico_item ON ositordoid = ordoid
				INNER JOIN os_tipo_item ON ositotioid = otioid
				INNER JOIN os_tipo ON otiostoid = ostoid
				INNER JOIN contrato ON ordconnumero = connumero
				LEFT JOIN contrato_servico ON consconoid = connumero and consobroid in (133, 1697, 288)
				INNER JOIN equipamento_classe ON coneqcoid = eqcoid 
				INNER JOIN equipamento ON conequoid = equoid
				WHERE 
					conequoid is not null 
					AND ordoid = ".$ordoid."
				GROUP BY eqcecgoid, ordstatus, conequoid_antigo, conequoid, ordequoid, equesn, consobroid
            ";
    
    	$rs = pg_query($this->conn, $sql);
    
    	if (pg_num_rows($rs)) {
    		return pg_fetch_object($rs);
    	}
    
    	return array();
    }


    /**
     * Buscar dados dos servicos da OS
     *
     * @param int $ordoid
     * @return object
     *
     */
    public function buscarDadosServicosOS($ordoid){
    
    	$sql = "SELECT 1 as valido				    			
				FROM ordem_servico
				INNER JOIN ordem_servico_item ON ositordoid = ordoid
				INNER JOIN os_tipo_item ON ositotioid = otioid
				INNER JOIN os_tipo ON otiostoid = ostoid
				WHERE ordoid = ".$ordoid."
				  AND ostoid = 4 -- serviço assistencia
                  AND (
                      otiobroid in (133, 1697) AND otioid in (259, 1327)
                    OR
                      otioid in (104)
                  )
            ";
    
    	$rs = pg_query($this->conn, $sql);
    
    	if (pg_num_rows($rs)) {
    		return pg_fetch_object($rs);
    	}
    
    	return array();
    }
    
    
    /**
     * Busca dados do cliente indicador para histórico
     *
     * @param int $clioid
     * @return array
     *
     */
    public function buscarDadosIndicador($clioid) {
    
    	$sql = "
            SELECT
                clinome,
                (CASE WHEN clitipo = 'F' THEN
                    clino_cpf
                ELSE
                    clino_cgc
                END) AS cpf_cnpj,
                clitipo
            FROM
                clientes
            WHERE
                clioid = ".intval($clioid)."
            ";
    
    	$rs = pg_query($this->conn, $sql);
    
    	if (pg_num_rows($rs)) {
    		return pg_fetch_object($rs);
    	}
    
    	return array();
    
    }
}