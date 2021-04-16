<?php
/**
 * Acesso a dados para o módulo Relatório de envio de emails automaticos
 */
class RelEnvioEmailsAutomaticosDAO {
	
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
	public function __construct($connection) {
		
		if (!$connection) {
			throw new Exception("Link de conexão com o banco não informada");
		}
		
		$this->conn = $connection;
	}

	/**
	 * Retorna a lista de tipos de OS
	 * @throws Exception
	 * @return array|NULL
	 */
	public function getTipoOSList() {
		
		$sql = "
		SELECT
			ostoid			AS id,
			ostdescricao	AS descricao
		FROM
			os_tipo
		WHERE
			ostdt_exclusao IS NULL
			AND ostoid IN (2,3,4,9)
		ORDER BY
			ostdescricao
		";
		$result = pg_query($this->conn, $sql);
		if(!$result) {
			throw new Exception("Erro ao buscar a lista de tipos de OS");
		}
		
		if (pg_num_rows($result) > 0) {
				
			while($row = pg_fetch_object($result)) {
		
				$return[$row->id] = $row->descricao;
			}
				
			return $return;
		}
		else {
			return null;
		}
	}
	

   public function getPropostas() {
                
        $sql = "
            SELECT 
            	tppoid, tppdescricao 
			FROM 
				tipo_proposta
			WHERE 
				tppoid_supertipo IS NULL          
            ORDER BY 
                tppdescricao";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['tppoid']       = pg_fetch_result($rs, $i, 'tppoid');
            $result[$i]['tppdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'tppdescricao'));
        }
        
        return $result;
        
    }
    
    public function getSubPropostas() {
    
        $proposta =  $_POST['tipoProposta'];

        $sql = "
            SELECT 
            	tppoid, tppdescricao 
			FROM 
				tipo_proposta
			WHERE 
				tppoid_supertipo IS NOT NULL   
			AND tppoid_supertipo = $proposta   
            ORDER BY 
                tppdescricao";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['tppoid']       = pg_fetch_result($rs, $i, 'tppoid');
            $result[$i]['tppdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'tppdescricao'));
        }
        
        return $result;
        
    }
	
    public function getContratos() {
                
        $sql = "
            SELECT 
            	tpcoid, tpcdescricao 
            FROM 
            	tipo_contrato
            WHERE 
            	tpcativo IS TRUE        
            ORDER BY 
            	tpcdescricao";
        
        $rs = pg_query($this->conn, $sql);
        
        $result = array();
        
        for($i = 0; $i < pg_num_rows($rs); $i++) {
            $result[$i]['tpcoid']       = pg_fetch_result($rs, $i, 'tpcoid');
            $result[$i]['tpcdescricao'] = utf8_encode(pg_fetch_result($rs, $i, 'tpcdescricao'));
        }
        
        return $result;
        
    }
	
	/**
	 * Gera a massa do relatório
	 * 
	 * @param array $filtros
	 * @throws Exception
	 * @return resource|NULL
	 */
	public function getRelatorio($filtros) {
		
		$filtro 			= "";
		$verInsucessos 		= null;
		$quantidadeEmails 	= null;
		
		// Valida período
		if (empty($filtros['data_inicial'])) {
			throw new Exception("Data inicial não informada");
		}
		
		if (empty($filtros['data_final'])) {
			throw new Exception("Data final não informada");
		}
		
		#####################################################################################################################################################
		# INICIO - JOIN, WHERE e CAMPOS default para o relatorio (OBS: serao alterados caso o filtro Ura Ativa seja preenchido)								#
		#####################################################################################################################################################
		
		$FIELDS = "
				leeordoid		AS numero_os,
			CASE 
				WHEN ostdescricao='ASSIST' THEN
					'ASSISTÊNCIA'
				ELSE
					ostdescricao
			END AS tipo_os,
			ordconnumero		AS contrato,
			to_char(leedt_envio, 'dd/mm/yyyy')		AS data_notificacao,
			(
			SELECT
				COUNT(s.leeordoid)
			FROM
				log_envio_email s
			WHERE
				s.leeordoid = ordoid
			) 	AS numero_notificacao,
		
			clinome			AS nome_cliente,
			(
			CASE
				WHEN clitipo = 'J' THEN
					clino_cgc
				ELSE
					clino_cpf
			END
			)			AS cnpj_cpf,
		
			(
			CASE 
				WHEN clitipo = 'J' THEN
					cliuf_com
				ELSE
					cliuf_res
			END
			)			AS uf,
			
			(
			CASE 
				WHEN clitipo = 'J' THEN
					clicidade_com
				ELSE
					clicidade_res
			END
			)			AS cidade,
		
			veiplaca		AS placa,
			veichassi		AS chassi,
			mlomodelo		AS modelo,
			CASE
				WHEN tipo.tppoid_supertipo is not null THEN
					paitipo.tppdescricao
			ELSE
				tipo.tppdescricao
			END AS proposta,
			(
				SELECT
					MAX(to_char(ors.orsdt_situacao, 'dd/mm/yyyy HH24:MI'))
				FROM
					ordem_situacao ors
				WHERE
					ors.orsordoid = ordem_servico.ordoid
			) AS data,
			(
			CASE 
					WHEN leetipo_log = 'I' THEN
						'SIM'
					ELSE
						'NÃO'
			END	
			)	AS insucesso_envio	";
		
		$JOIN = "
				INNER JOIN ordem_servico 		ON ordoid 	= leeordoid
				INNER JOIN ordem_servico_item 	ON ositordoid 	= ordoid
				INNER JOIN os_tipo_item			ON otioid 	= ositotioid
				INNER JOIN os_tipo				ON ostoid	= otiostoid
				INNER JOIN contrato				ON connumero	= ordconnumero
				INNER JOIN ordem_situacao		ON orsordoid	= ordoid";
		
		$WHERE = "
				orsstatus IN (16,105)	
				AND (
					SELECT
						COUNT(*)
					FROM
						ordem_servico_item osit
						INNER JOIN os_tipo_item oti ON oti.otioid 	= osit.ositotioid
					WHERE
						osit.ositordoid = ordem_servico.ordoid
						AND oti.otiostoid = 1
				) = 0";
		
		#####################################################################################################################################################
		# FIM - JOIN, WHERE e CAMPOS default para o relatorio (OBS: serao alterados caso o filtro Ura Ativa seja preenchido)								#
		#####################################################################################################################################################
		
		$filtro = " AND leedt_envio::date BETWEEN '".$filtros['data_inicial']."'::date AND '".$filtros['data_final']."'::date ";
		
		/**
		 * RN2 Apenas se a combo URA Ativa estiver vazia filtrar por tipo e numero da OS
		 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
		 */
		if(empty($filtros['ura_ativa'])){
			
			if (!empty($filtros['tipo_os'])) {
				$filtro = " AND otiostoid = ".$filtros['tipo_os']."  ";
			}
			
			if (!empty($filtros['numero_os']) && is_numeric($filtros['numero_os'])) {
				$filtro .= " AND ordoid = ".$filtros['numero_os']."  ";
			}
		}
	
		if (strlen($filtros['ver_insucessos']) != 0) {
			
			if ($filtros['ver_insucessos'] == '1') {
				$verInsucessos = 'I';
			}
			else {
				$verInsucessos = 'S';
			}
			
			$filtro .= " AND leetipo_log = '".$verInsucessos."'  ";
		}
		
		if($filtros['ura_ativa'] == 'P'){ # Filtro campo Ura Ativa - Panico
			
			$filtro .= " AND leetipo_email = 1";
			
		} else if($filtros['ura_ativa'] == 'E'){ # Filtro campo Ura Ativa - Estatistica
			
			$filtro .= " AND leetipo_email = 2";
			
		}
		
		/*
		 * Alteramos o JOIN o WHERE e as colunas que aparecem no relatorio caso o filtro URA AtiVa esteja preenchido.
		 * JOIN apenas com contrato e nao mais com ordem de servico
		 */
		if(!empty($filtros['ura_ativa'])) {
			
			$JOIN = "INNER JOIN contrato	ON connumero = leeconnumero";
			$WHERE = " TRUE ";
			
			$FIELDS = "
				leeordoid		AS numero_os,
				leeconnumero		AS contrato,
				to_char(leedt_envio, 'dd/mm/yyyy')		AS data_notificacao,				
				clinome			AS nome_cliente,
				(
				CASE
					WHEN clitipo = 'J' THEN
						clino_cgc
					ELSE
						clino_cpf
				END
				)			AS cnpj_cpf,
				
				(
				CASE
					WHEN clitipo = 'J' THEN
						cliuf_com
					ELSE
						cliuf_res
				END
				)			AS uf,
			
				(
				CASE
					WHEN clitipo = 'J' THEN
						clicidade_com
					ELSE
						clicidade_res
				END
				)			AS cidade,
				
				veiplaca		AS placa,
				veichassi		AS chassi,
				mlomodelo		AS modelo,
			case
				when tipo.tppoid_supertipo is not null
				then
			paitipo.tppdescricao
			else
			tipo.tppdescricao
			end AS proposta,
			
				(
				CASE
						WHEN leetipo_log = 'I' THEN
							'SIM'
						ELSE
							'NÃO'
				END
				)	AS insucesso_envio	";
		}
		
		if (!empty($filtros['quantidade_emails'])) {
			
			if ($filtros['quantidade_emails'] > 0) {
				
				/* $filtro .= "
				AND (
				SELECT
					COUNT(s.leeordoid)
				FROM
					log_envio_email s
				WHERE
					s.leeordoid = ordem_servico.ordoid
				) = ".$filtros['quantidade_emails']."	
				"; */
				
				$filtro .= "
					AND leesseoid = '".$filtros['quantidade_emails']."'
				";
			}
		}
		
		if (!empty($filtros['placa'])) {
			$filtro .= " AND veiplaca ILIKE '".$filtros['placa']."%'  ";
		}
		
		if (!empty($filtros['nome_cliente'])) {
			$filtro .= " AND clinome ILIKE '".$filtros['nome_cliente']."%'  ";
		}
		
		if (!empty($filtros['comboSubpropostas'])) {
			$filtro .= " AND tipo.tppoid = '".$filtros['comboSubpropostas']."'  ";
		} else if (!empty($filtros['comboPropostas'])) {
			// $filtro .= " AND paitipo.tppoid = '".$filtros['comboPropostas']."'  ";
			$filtro .= "AND 
			CASE
				WHEN tipo.tppoid_supertipo is not null THEN
					paitipo.tppoid = '".$filtros['comboPropostas']."'
			ELSE
						 tipo.tppoid = '".$filtros['comboPropostas']."'
			END";
		}
		if (!empty($filtros['comboContratos'])) {
			$filtro .= " AND conno_tipo = '".$filtros['comboContratos']."'  ";
		}
		

		$sql = "
		SELECT DISTINCT ON (leeoid) --DISTINCT ON (leeordoid)
			$FIELDS
		FROM
			log_envio_email
			$JOIN
			INNER JOIN clientes				ON clioid		= conclioid
			INNER JOIN veiculo				ON veioid		= conveioid
			INNER JOIN modelo				ON mlooid		= veimlooid
			LEFT JOIN proposta 				ON prptermo 	= connumero
			LEFT JOIN tipo_proposta tipo 	ON prptppoid = tipo.tppoid 
			LEFT JOIN tipo_proposta paitipo ON paitipo.tppoid = tipo.tppoid_supertipo
			LEFT JOIN tipo_contrato 		ON tpcoid 		= conno_tipo
		WHERE
			$WHERE
			$filtro	
			
		";
		$result = pg_query($this->conn, $sql);
		if(!$result) {
			throw new Exception("Erro ao gerar o relatório");
		}
		
		if (pg_num_rows($result) > 0) {
		
			return $result;
		}
		else {
			return null;
		}
	}
}