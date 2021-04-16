	<?php

/**
 * Classe responsável pela cmada de persistência de Banco de Dados
 * @author andre.zilz <andre.zilz@meta.com.br>
 * @package Principal
 * @since 17/06/2013
 */

class PrnGestaoFrotaContratoVivoDAO {
	
	const TIPO_CONTRATO	= 'vivo';
	
	protected $conn;
	
	/**
	 * Construtor da Classe
	 * @param $conn
	 */
	public function __construct($conn) {
		$this->conn = $conn;
	}
	
	/**
	 * Inicia uma transação com o banco de dados
	 */
	public function abrirTransacao() {
		pg_query($this->conn, "BEGIN;");
	}
	
	/**
	 * Comita uma transação com o banco de dados
	 */
	public function fecharTransacao() {
		pg_query($this->conn, "COMMIT");
	}
	
	/**
	 * Aborta uma transação com o banco de dados
	 */
	public function abortarTransacao() {
		pg_query($this->conn, "ROLLBACK;");
	}
	
	/**
	 * Executa uma consulta (query)
	 *
	 * @param string $sql
	 * @return recordset
	 */
	public function executarQuery($sql) {
	
		$rs = pg_query($this->conn, $sql);
			
		return $rs;
	}
	
	/**
	 * Método que realiza a busca das informações completas do cliente
	 * @param string $filtro
	 * @param string $parametro
	 * @return array:
	 */
	public function buscarDadosCliente($clioid){
		
		$retorno = array();			
		$clioid = (int)$clioid;

		if(empty($clioid)){
						
			return $retorno;
		}
		
		$sql = "
				SELECT DISTINCT 
		            vppasubscription AS idvivo,
                    clinome AS nome,
                    (
                        CASE WHEN clitipo = 'F' THEN
                            clino_cpf	
                        ELSE
                            clino_cgc
                        END
                    ) AS cpf_cgc,				

                    (
                        clirua_res || ',' || 
                        clino_res || ', ' || 
                        clibairro_res || ', ' || 
                        clicidade_res || ', ' || 
                        cliuf_res || ', ' || 
                        clino_cep_res
                    ) AS endereco,
                    clitipo
                    
				FROM
                    clientes
				INNER JOIN
                    contrato ON conclioid = clioid
                    AND 
                        conno_tipo IN
                        (
                            SELECT 
                                tpcoid 
                            FROM	
                                tipo_contrato 
                            WHERE	
                                tpcdescricao ILIKE '%".$this::TIPO_CONTRATO."'
                        )
                    AND 
                        condt_exclusao IS NULL
                LEFT JOIN
		            veiculo_pedido_parceiro ON vppaconoid = connumero
                WHERE
                    clioid = ".$clioid."
				";

		$rs = $this->executarQuery($sql);
		$qtdeLinhasRetorno = pg_num_rows($rs); 
        if(pg_num_rows($rs) > 0) {        
            $row = pg_fetch_object($rs);

            $retorno['nome']	 = utf8_encode($row->nome);
            $retorno['cpf_cnpj']  = $row->clitipo == 'J' ? str_pad($row->cpf_cgc, 14, '0', STR_PAD_LEFT) : str_pad($row->cpf_cgc, 11, '0', STR_PAD_LEFT);
            $retorno['endereco'] = utf8_encode($row->endereco);
            if ($qtdeLinhasRetorno > 1) {
                $retorno['idvivo'] = "";
            } else {
                $retorno['idvivo'] 	 = utf8_encode($row->idvivo);
            }
        }
        
		return $retorno;
		
	}
	
	/**
	 * Realiza pesquisa dinamica por nome / razao socail
	 * @param string $filtro
	 * @param string $parametro
	 * @return string
	 */
	public function retornarPesquisaDinamicaNome($filtro, $parametro){
		
		$retorno = array(); 
		$tipo = '';
		$chave = 0;
		$parametro = pg_escape_string($parametro);
		
		if(empty($filtro) || empty($parametro)){
		
			return $retorno;
		}
		
		
		if($filtro == 'razao_social'){
			$tipo = 'J';
		}
		else{
			$tipo = 'F';
		}
		
		$sql = "
				SELECT 
                    DISTINCT clinome AS nome,
                    clioid
				FROM
                    clientes
				INNER JOIN 
                    contrato ON conclioid = clioid 
                AND 
                    conno_tipo IN
                    (
                        SELECT 
                            tpcoid 
                        FROM	
                            tipo_contrato 
                        WHERE	
                            tpcdescricao ILIKE '%".$this::TIPO_CONTRATO."'
                    )
                AND 
                    condt_exclusao IS NULL
				WHERE	
                    clitipo = '".$tipo."'
				AND
                    clinome ILIKE '".$parametro."%'
				";		
        
        //echo '<pre>', $sql, '</pre>';
        
		$rs = $this->executarQuery($sql);
		
		while($row = pg_fetch_object($rs)){
				
			$retorno[$chave]['label'] = utf8_encode($row->nome);
            $retorno[$chave]['value'] = utf8_encode($row->nome);
			$retorno[$chave]['id'] = $row->clioid;
				
			$chave++;

		}
		
		return $retorno;
		
	}
	
	/**
	 * Realiza pesquisa dinamica por cnpj ou CPF
	 * @param string $filtro
	 * @param bigint $parametro
	 * @return array
	 */
	public function retornarPesquisaDinamicaCpfCnpj($filtro, $parametro){
	
		$retorno = array();
		$chave = 0;
        
        $parametro = preg_replace('/[^0-9]/', '', $parametro); // somente números
        
        if(empty($filtro) || empty($parametro)){
	
			return $retorno;
		}
	
		$sql = "
				SELECT
                    DISTINCT (
                        CASE WHEN clitipo = 'F' THEN
                            clino_cpf	
                        ELSE
                            clino_cgc
                        END					
                    ) as cpf_cgc,
                    clioid	
				FROM
                    clientes
				INNER JOIN
                    contrato ON conclioid = clioid
                AND 
                    conno_tipo IN
                    (
                        SELECT 
                            tpcoid 
                        FROM	
                            tipo_contrato 
                        WHERE	
                            tpcdescricao ILIKE '%".$this::TIPO_CONTRATO."'
                    )
                AND 
                    condt_exclusao IS NULL
				WHERE					
				";
		
		if($filtro == 'cpf'){
			$sql .= " lpad(clino_cpf::TEXT, 11,'0') LIKE '" . pg_escape_string($parametro) . "%'
					AND clitipo = 'F'
					";
		}
		else if($filtro == 'cnpj'){
			$sql .= " lpad(clino_cgc::TEXT, 14,'0') LIKE '" . pg_escape_string($parametro) ."%'
					 AND clitipo = 'J'
					";
		}

		$rs = $this->executarQuery($sql);
	
		while($row = pg_fetch_object($rs)){
	
		    if($filtro == 'cnpj'){
			    $retorno[$chave]['label'] = $this->formatarDados('cnpj', $row->cpf_cgc);
			    $retorno[$chave]['value'] = $this->formatarDados('cnpj', $row->cpf_cgc);
		    } elseif($filtro == 'cpf'){
		        $retorno[$chave]['label'] = $this->formatarDados('cpf', $row->cpf_cgc);
		        $retorno[$chave]['value'] = $this->formatarDados('cpf', $row->cpf_cgc);
		    }
			$retorno[$chave]['id'] = $row->clioid;
				
			$chave++;
	
		}

		return $retorno;
	
	
	}
	
	
	/**
	 * Realiza pesquisa dinâmica por IdVivo (subscription ID)
	 * @param string $filtro
	 * @param bigint $parametro
	 * @return array
	 */
	public function retornarPesquisaDinamicaIdVivo($filtro, $parametro){
	
		$retorno = array();
		$chave = 0;

		$parametro = pg_escape_string($parametro);
	
		if(empty($filtro) || empty($parametro)){
	
			return $retorno;
		}
	
		$sql = "
				SELECT
                    DISTINCT vppasubscription AS idvivo,
                    clioid
				FROM
		            veiculo_pedido_parceiro 
		        INNER JOIN 
		            contrato ON vppaconoid = connumero
                INNER JOIN
                    clientes ON conclioid = clioid
                AND 
                    conno_tipo IN 
                    (
                        SELECT 
                            tpcoid 
                        FROM	
                            tipo_contrato 
                        WHERE	
                            tpcdescricao ILIKE '%".$this::TIPO_CONTRATO."'
                    )
                AND 
                    condt_exclusao IS NULL
                AND
                    clitipo = '".$filtro."'
				WHERE
                    vppasubscription ILIKE '".$parametro."%'
                LIMIT 1
				";
        
		$rs = $this->executarQuery($sql);
	
		while($row = pg_fetch_object($rs)){
	
			$retorno[$chave]['label'] = utf8_encode($row->idvivo);
			$retorno[$chave]['value'] = utf8_encode($row->idvivo);
			$retorno[$chave]['id'] = $row->clioid;
			
			$chave++;
	
		}
	
		return $retorno;	
	
	}

	/**
	 * Pesquisa os contatos do cliente
	 * @param int $clioid
	 * @return array
	 */
	public function pesquisarContatosCliente($clioid){
		
		$clioid = (int)$clioid;
		$retorno = array();
		$chave = 0;
		
		if(empty($clioid)){
			return $retorno;
			
		}
		
		$sql = "				
				(
					SELECT
                        tctcontato AS nome,
                        (tctno_ddd_res || tctno_fone_res) AS residencial,
                        (tctno_ddd_com || tctno_fone_com) AS comercial,
                        (tctno_ddd_cel || tctno_fone_cel) AS celular,						
                        tctorigem AS tipo_contato
					FROM
                        telefone_contato		
					INNER JOIN
                        contrato ON connumero = tctconnumero
                        AND conno_tipo IN
                        (
                            SELECT 
                                    tpcoid 
                            FROM	
                                    tipo_contrato 
                            WHERE	
                                    tpcdescricao ILIKE '%".$this::TIPO_CONTRATO."'
                        )
					INNER JOIN
                        clientes ON clioid = conclioid AND clioid = ".$clioid."
					WHERE 		
                        tctorigem = 'A'
					ORDER BY 	
                        tctdt_cadastro DESC
					LIMIT 1
				)		
				UNION ALL				
				(
					SELECT
                        tctcontato AS nome,
                        (tctno_ddd_res || tctno_fone_res) AS residencial,
                        (tctno_ddd_com || tctno_fone_com) AS comercial,
                        (tctno_ddd_cel || tctno_fone_cel) AS celular,			
                        tctorigem AS tipo_contato
					FROM
                        telefone_contato		
					INNER JOIN
                        contrato ON connumero = tctconnumero
                    AND conno_tipo IN
                    (
                        SELECT 
                            tpcoid 
                        FROM	
                            tipo_contrato 
                        WHERE	
                            tpcdescricao ILIKE '%".$this::TIPO_CONTRATO."'
                    )
					INNER JOIN
                        clientes ON clioid = conclioid AND clioid = ".$clioid."
					WHERE 		
                        tctorigem = 'E'
					ORDER BY	
                        tctdt_cadastro DESC
					LIMIT 2
				)
		";		
		
		$rs = $this->executarQuery($sql);
		
		while($row = pg_fetch_object($rs)){
			
			
			$residencial	= formatar_fone_nono_digito($row->residencial);
			$comercial		= formatar_fone_nono_digito($row->comercial);
			$celular 		= formatar_fone_nono_digito($row->celular);
			
			
			if(!empty($residencial)){		
										
				$retorno[$chave]['telefone'] 	 = $residencial;
				
			}
			else if (!empty($comercial)){
				
				$retorno[$chave]['telefone'] 	 = $comercial;
				
			}
			else{
				
				$retorno[$chave]['telefone'] 	 = $celular;
				
			}
		
			$retorno[$chave]['nome'] 		 = utf8_encode($row->nome);
			$retorno[$chave]['tipo_contato'] = $row->tipo_contato;
			
			$chave++;
		}
		
		return $retorno;
		
	}
	
	/**
	 * Pesquisa todos veículos com contrato vivo do cliente
	 * @param int $clioid
	 * @param string $placa
	 *  @param string $idvivo
	 * @return array
	 */
	public function pesquisarVeiculos($clioid, $placa = '', $idvivo = ''){
		
		$retorno                    = array();
        $retorno['resultados']      = array();
        $placa                      = pg_escape_string($placa);
        $idvivo                     = pg_escape_string($idvivo);
		
		if(empty($clioid)){
			return $retorno;			
		}
        
		$sql = "
				SELECT 
					DISTINCT
                    ((count(veioid)  OVER ()) - (count(veidt_exclusao)  OVER ())) as total_ativos,
		            vppasubscription,
                    veiplaca,
                    eqcdescricao, 
                    cpagmonitoramento,
				---PARCELAS PAGAS 
                    (
						SELECT
                        		MAX(titno_parcela)
                       	FROM
                        		nota_fiscal_item
								INNER JOIN nota_fiscal ON nflno_numero = nfino_numero AND nflserie = nfiserie
								INNER JOIN titulo ON titnfloid = nfloid
						WHERE
                        		nficonoid = connumero
                        AND
                        		titdt_pagamento IS NOT NULL
                    ) AS parcelas_pagas,
				---TOTAL PARCELAS
				  (
                  		SELECT
                        		MAX(titno_parcela)
                        FROM
                        		nota_fiscal_item
								INNER JOIN nota_fiscal ON nflno_numero = nfino_numero AND nflserie = nfiserie
								INNER JOIN titulo ON titnfloid = nfloid
						WHERE
                        		nficonoid = connumero                                              
                    ) AS total_parcelas,		
				---TOTAL PAGO
				    (
                    	SELECT SUM(AUX1.valor) FROM
                    	(SELECT
					DISTINCT titnfloid, (titvl_titulo) AS valor,titno_parcela
                        FROM
                        		nota_fiscal_item
								INNER JOIN nota_fiscal ON nflno_numero = nfino_numero AND nflserie = nfiserie
								INNER JOIN titulo ON titnfloid = nfloid
						WHERE
                        		nficonoid = connumero
                        AND
                        		titdt_pagamento IS NOT NULL) AS AUX1
                    ) AS total_pago,
    			---DATA FIM
                    (
                        CASE WHEN
                            condt_fim_vigencia IS NOT NULL THEN
                                condt_fim_vigencia
                        ELSE
                                condt_exclusao
                        END						
                    )AS data_fim,
				---STATUS
                    (
                        CASE WHEN  
                            veidt_exclusao IS NULL THEN
                                'Ativo'
                        ELSE
                                'Cancelado'
                        END
                    ) AS status,
				---TEMPO CONTRATO
                    (
                        CASE WHEN
                            condt_fim_vigencia IS NOT NULL THEN
                                (EXTRACT(MONTH FROM (AGE(condt_fim_vigencia, condt_ini_vigencia)))) 
                                + (EXTRACT(YEAR FROM (AGE(condt_fim_vigencia, condt_ini_vigencia))) * 12)
                        ELSE
                                (EXTRACT(MONTH FROM (AGE(veidt_exclusao, condt_ini_vigencia)))) 
                                + (EXTRACT(YEAR FROM (AGE(veidt_exclusao, condt_ini_vigencia))) * 12)
                        END						
                    )AS tempo_contrato,
				    condt_ini_vigencia as data_inicio,
                    connumero	
				FROM veiculo
				INNER JOIN contrato ON conveioid = veioid
		        INNER JOIN equipamento_classe ON eqcoid = coneqcoid		
				LEFT JOIN contrato_pagamento ON cpagconoid = connumero
				LEFT JOIN veiculo_pedido_parceiro ON vppaconoid = connumero
		        WHERE
                    conclioid = ".$clioid."
                AND 
                    conno_tipo IN (
                        SELECT 
                            tpcoid 
                        FROM   
                            tipo_contrato 
                        WHERE 
                            tpcdescricao ILIKE '%".$this::TIPO_CONTRATO."'
                )                            		
               ";
                	
		if(!empty($placa)){
			$sql .= " AND veiplaca ILIKE '%".$placa."%'";
		}
		
		if(!empty($idvivo)){
			$sql .= " AND vppasubscription ILIKE '".$idvivo."%'";
		}
		
		$sql .= "  ORDER BY veiplaca, parcelas_pagas, total_parcelas";
        
		$rs = $this->executarQuery($sql);		
		
		$qtdeRegistros = pg_num_rows($rs);
		
		while($row = pg_fetch_object($rs)){
		
			$veiculo = new stdClass();
            
            $tempoContrato = (int) $row->tempo_contrato;
            
            if(!empty($tempoContrato)) {
                $descricaoTempoContrato = $tempoContrato > 1 ? $tempoContrato . ' meses' : '1 mês';
            } else {
                $descricaoTempoContrato = '';
            }
			
            $veiculo->connumero 	= $row->connumero;
            $veiculo->idvivo 		= $row->vppasubscription;
            $veiculo->placa 		= $row->veiplaca;
			$veiculo->descricao  	= utf8_encode($row->eqcdescricao);
			$veiculo->valorServico  = 'R$'.number_format($row->cpagmonitoramento, 2, ',', '.');
			$veiculo->parcela 		= !empty($row->parcelas_pagas) ? $row->parcelas_pagas  : '';
			$veiculo->totalParcelas = !empty($row->total_parcelas) ? $row->total_parcelas : '';
            $veiculo->valorParcela  = 'R$'.number_format($row->total_pago, 2, ',', '.');
			$veiculo->dataInicio	= !empty($row->data_inicio) ?  date('d/m/Y', strtotime($row->data_inicio)) : '';
			$veiculo->dataFim 		= !empty($row->data_fim) ?  date('d/m/Y', strtotime($row->data_fim)) : '';
			$veiculo->status 		= $row->status;
			$veiculo->tempoContrato = utf8_encode($descricaoTempoContrato);
			
			$retorno['resultados'][] = $veiculo;
            $retorno['total_ativos'] = $qtdeRegistros;
			
			unset($veiculo);

		}
		
		return $retorno;
		
	}	

	/**
	 * Busca todas as entradas de histórico de um contrato
	 * @param integer $connumero
	 */
	public function buscarHistoricoContrato($connumero) {
		
		$connumero = isset($connumero) ? $connumero : 0;
		$resultado = array();
		
		if ($connumero > 0) {
			$sql = "SELECT
                        hitobs,
                        hitprotprotocolo,
                        hitdt_acionamento,
                        nm_usuario,
                        (
                            SELECT
                                clinome
                            FROM
                                clientes
                            INNER JOIN
                                contrato ON conclioid = clioid
                            WHERE
                                connumero = ".$connumero."
                            LIMIT 1
                        ) AS nome_cliente
					FROM
                        historico_termo
					INNER JOIN
                        usuarios ON cd_usuario = hitusuoid
					WHERE
                        hitconnumero = ".$connumero."
					ORDER BY 
                        hitdt_acionamento DESC";
			
			if ($query = pg_query($sql)) {
				while($res = pg_fetch_object($query)) {
					$resultado[] = $res;
				}
			}
		}
		return $resultado;
	}
	
	/**
	 * Busca todos os contatos de um contrato do tipo especificado.
	 * @param integer $connumero
	 * @param string $tipo
	 * @return array:stdClass
	 */
	public function buscarContatosContrato($connumero, $tipo='A') {
		
		$connumero 	= isset($connumero) ? $connumero : 0;
		$tipo 		= empty($tipo)		? $tipo		 : 'A';
		$resultado = array();
		
		if ($connumero > 0) {
			$sql = "
                SELECT
                    tctcontato, 
                    tctcpf, 
                    tctrg, 
                    CASE 
                        WHEN tctno_ddd_res IS NOT NULL AND tctno_fone_res IS NOT NULL THEN
                            tctno_ddd_res||tctno_fone_res
                        ELSE
                            ''
                    END AS fone_res,
                    CASE 
                        WHEN tctno_ddd_com IS NOT NULL AND tctno_fone_com IS NOT NULL THEN
                            tctno_ddd_com||tctno_fone_com
                        ELSE
                            ''
                    END AS fone_com,
                    CASE 
                        WHEN tctno_ddd_cel IS NOT NULL AND tctno_fone_cel IS NOT NULL THEN
                            tctno_ddd_cel||tctno_fone_cel
                        ELSE
                            ''
                    END AS fone_cel,
                    tctid_nextel,
                    tctobs
                FROM
                    telefone_contato                
                WHERE
                    tctconnumero = ".$connumero."
                AND
                    tctorigem	 = '".$tipo."'
                ORDER BY
                    tctcontato ASC
                    ";
			if ($query = pg_query($sql)) {
				while($res = pg_fetch_object($query)) {
					$resultado[] = $res;
				}
			}
		}
		return $resultado;
	}
	
	/**
	 * Busca as ordens de serviços do cliente relacionadas com contratos do tipo vivo
	 * @param int $clioid
	 * @param int $ordoid
	 * @return array
	 */
	public function pesquisarOrdemServico($clioid, $ordoid = 0, $idvivo = ''){
		
		$retorno                 = array();
		$retorno['resultados']   = array();
	
		if(empty($clioid)){
			return $retorno;
		}
		
		$idvivo = pg_escape_string($idvivo);
	
		$sql = "
				SELECT
						DISTINCT
		                count(ordoid) OVER() AS total_registros,
                        vppasubscription,
                        veiplaca,
						ordoid AS ordem_servico,
						TO_CHAR(orddt_ordem::date, 'dd/mm/YYYY') AS data_abertura,
						ossdescricao AS status,
						ds_login AS atendente,
					---MOTIVO
						(
							SELECT
									otidescricao
							FROM
									ordem_servico_item
							INNER JOIN
									os_tipo_item ON otioid = ositotioid
							WHERE
									ositordoid = ordoid
							AND
									ositstatus  = 'A'
							OR
									ositstatus = 'P'
							LIMIT 1
						) AS motivo,
	
					---DATA ENCERRAMENTO
						 (
							CASE WHEN ossdescricao ilike 'conclu_do' THEN
									(
										SELECT
										    	TO_CHAR(orsdt_situacao,'dd/mm/yyyy') AS orsdt_situacao
										FROM
										    	ordem_situacao
										WHERE
										    	orsordoid = ordoid
										ORDER BY
										    	orsdt_situacao DESC
										LIMIT 1
									)
							ELSE
									NULL
							END
						) as data_encerramento,
	
					---TEMPO CONCLUSAO
						(
							CASE WHEN ossdescricao ilike 'conclu_do' THEN
									(
										SELECT
										   		(SELECT((EXTRACT (EPOCH FROM AGE(orsdt_situacao, orddt_ordem)))* INTERVAL '1 second'))
										FROM
										    	ordem_situacao
										WHERE
										    	orsordoid = ordoid
										ORDER BY
										    	orsdt_situacao DESC
										LIMIT 1
									)
	
							ELSE
									NULL
							END) as tempo_conclusao,
	
					---PROTOCOLO SASCAR
						(
							SELECT
									hitprotprotocolo
							FROM
									historico_termo
							WHERE
									hitconnumero = connumero
							ORDER BY
									hitdt_acionamento DESC
							LIMIT 1
						) AS protocolo_sascar,
	
					---ULTIMA ACAO
						(
							SELECT
									orssituacao
							FROM
							 		ordem_situacao
							WHERE
									orsordoid = ordoid
							ORDER BY
									orsdt_situacao DESC
							LIMIT 	1
						) AS ultima_acao,
	
					--- PROTOCOLO VIVO
						(
							SELECT
									slpprotocolo_vivo
							FROM
									solicitacao_parceria
							WHERE
									slpclioid = conclioid
							LIMIT 1
						) AS protocolo_vivo,
	
				--- DEFEITO ALEGADO
						(
							SELECT
									otddescricao
							FROM
									ordem_servico_defeito
							INNER JOIN
									os_tipo_defeito ON otdoid = osdfotdoid
							WHERE
									osdfoid = ositosdfoid_alegado
						) AS defeito_alegado,
                connumero
	
			FROM
						clientes
			INNER JOIN
                        contrato ON conclioid = clioid
            INNER JOIN
                        veiculo ON veioid = conveioid		        
			INNER JOIN
						ordem_servico ON ordconnumero = connumero
			INNER JOIN
						ordem_servico_status ON ossoid = ordstatus
			INNER JOIN
						ordem_servico_item ON ositordoid = ordoid
			INNER JOIN
						os_tipo_item ON otioid = ositotioid
			LEFT JOIN
						usuarios ON cd_usuario = ordacomp_usuoid
            LEFT JOIN
                        veiculo_pedido_parceiro ON vppaconoid = connumero
			WHERE
						conclioid = ".$clioid."
			AND
						condt_exclusao IS NULL
			AND
						conno_tipo IN(select tpcoid from tipo_contrato where tpcdescricao ilike '%vivo%')
			AND
						ossoid IN
							(
								SELECT
										ossoid FROM ordem_servico_status
								WHERE
										TRIM(ossdescricao) ILIKE 'aguardando%autoriza__o'
								OR
										TRIM(ossdescricao) ILIKE 'aguard%autori%cobran_a'
								OR
										TRIM(ossdescricao) ILIKE 'autorizado'
								OR
										TRIM(ossdescricao) ILIKE 'pendente%teste%conclus_o'
								OR
										TRIM(ossdescricao) ILIKE 'conclu_do'
								OR
										TRIM(ossdescricao) ILIKE 'cancelada'
							)
	
				";
	
		if(!empty($ordoid)){
	
			$sql .= " AND ordoid = ".$ordoid."";
		}
	
		if(!empty($idvivo)){
			
		    $sql .= " AND vppasubscription ILIKE '".$idvivo."%'";
		    
		}
		
		$rs = $this->executarQuery($sql);
		
		$qtdeRegistros = pg_num_rows($rs);
	
		while($row = pg_fetch_object($rs)){
				
			$ordemServico = new stdClass();
				
	
			$ordemServico->total_registros 		= $row->total_registros;
			$ordemServico->connumero            = $row->connumero;
			$ordemServico->idvivo 		        = !empty($row->vppasubscription) ? utf8_encode($row->vppasubscription) : '';
			$ordemServico->placa 		        = !empty($row->veiplaca) ? utf8_encode($row->veiplaca) : '';
			$ordemServico->ordem_servico 		= !empty($row->ordem_servico) ? utf8_encode($row->ordem_servico) : '';
			$ordemServico->data_abertura 		= !empty($row->data_abertura) ? utf8_encode($row->data_abertura) : '';
			$ordemServico->status 				= !empty($row->status) ? utf8_encode($row->status) : '';
			$ordemServico->atendente 			= !empty($row->atendente) ? utf8_encode($row->atendente) : '';
			$ordemServico->motivo 				= !empty($row->motivo) ? utf8_encode($row->motivo) : '';
			$ordemServico->data_encerramento 	= !empty($row->data_encerramento) ? utf8_encode($row->data_encerramento) : '';
			$ordemServico->tempo_conclusao 		= !empty($row->tempo_conclusao) ? utf8_encode($this->tratarTempoConculsao($row->tempo_conclusao)) : '';
			$ordemServico->protocolo_sascar 	= !empty($row->protocolo_sascar) ? utf8_encode($row->protocolo_sascar) : '';
			$ordemServico->ultima_acao 			= !empty($row->ultima_acao) ? utf8_encode($row->ultima_acao) : '';
			$ordemServico->protocolo_vivo 		= !empty($row->protocolo_vivo) ? utf8_encode($row->protocolo_vivo) : '';
			$ordemServico->defeito_alegado 		= !empty($row->defeito_alegado) ? utf8_encode($row->defeito_alegado) : '';

			$retorno['resultados'][] = $ordemServico;
			$retorno['total_registros'] = $qtdeRegistros;
				
			unset($ordemServico);
				
		}
	
		return $retorno;
	
	}

	/**
	 * Busca todas as entradas de histórico de uma Ordem de Serviço
	 * @param integer $ordoid
	 */
	public function buscarHistoricoOrdemServico($ordoid) {
	
	    $ordoid = isset($ordoid) ? $ordoid : 0;
	    
	    $resultado = array();
	
	    if ($ordoid > 0) {
	        
	        $sql = "SELECT 
	                    orsordoid,
                        nm_usuario,
                        (SELECT 
	                        mhcdescricao 
	                    FROM 
	                        motivo_hist_corretora 
	                    WHERE 
	                        mhcoid=orsstatus) AS orsstatusi,
                        orsstatus,
	                    orssituacao,
	                    to_char(orsdt_situacao,'dd/mm/yy hh24:mi') AS dt_situacao,
                        to_char(orsdt_agenda,'dd/mm/yyyy') AS dt_agenda,
	                    to_char(orshr_agenda,'hh24:mi') AS hr_agenda
                    FROM 
	                    ordem_situacao, usuarios 
	                WHERE 
	                    orsordoid = ".$ordoid."
                        AND orsusuoid=cd_usuario 
                    ORDER BY 
                        orsdt_situacao DESC 
                    ";
	        	
	        if ($query = pg_query($sql)) {
	            
	            while($res = pg_fetch_object($query)) {
	                
	                $resultado[] = $res;
	                
	            }
	        }
	        
	    }
	    return $resultado;
	}
	
	/**
	 * Trata o formato de hora, removendo os milesegundos
	 * @param string $hora
	 * @return string
	 */
	private function tratarTempoConculsao($hora){
		
		$hora = explode(':', $hora);
		
		$hora[2] = substr($hora[2], 0, 2);
		
		$hora = $hora[0] . ':' . $hora[1] . ':' .$hora[2];
		
		return $hora;
		
	}
	
	/**
	 * Formatar dados (CPF||CNPJ)
	 *
	 * @param string $tipo  tipo doc
	 * @param string $valor valor do doc
	 *
	 * @return string $valor
	 */
	public function formatarDados($tipo, $valor) {
	
	    if ($tipo == "cpf" && $valor != "") {
	        $valor = str_pad($valor, 11, "0", STR_PAD_LEFT);
	        return $valor = substr($valor, 0, 3) . "." . substr($valor, 3, 3) . "." . substr($valor, 6, 3) . "-" . substr($valor, 9, 2);
	    }
	
	    if ($tipo == "cnpj" && $valor != "") {
	        $valor = str_pad($valor, 14, "0", STR_PAD_LEFT);
	        return $valor = substr($valor, 0, 2) . "." . substr($valor, 2, 3) . "." . substr($valor, 5, 3) . "/" . substr($valor, 8, 4) . "-" . substr($valor, 12, 2);
	    }
	}
	
		
}