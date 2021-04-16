<?php
/**
 * 
 * @author Gabriel Luiz Pereira
 * @since 13/12/2012
 * @package modulos/Relatorio/DAO
 */
class RelLinhasDAO {
	
	/**
	 * Link de conexão com o banco
	 * @var resource
	 */
	private $conn;
	
	/**
	 * Construtor
	 * @param resource $conexao
	 */
	public function __construct($conexao) {
		$this->conn  = $conexao;
	}
	
	/**
	 * Gera relatório com base nos filtros
	 * @param array $filtros
	 * @throws Exception
	 * @return boolean
	 */
	public function getRelatorioLinhas($filtros) {
		
		$filtro 	= "";
		$numFiltros = count($filtros);
		$numLinhas 	= 0;
		$sql 		= "";
		$result		= null;
		
		if ($numFiltros > 0) {
			
			if ($filtros['tipo'] == 'cid') {
				
				$filtro .= "
					AND	lincid IN ('".implode('\',\'', $filtros['lista'])."')		
				";
			} 
			elseif ($filtros['tipo'] == 'linha') {
				
				$filtro .= "
					AND	(arano_ddd::text||linnumero::text)::text IN ('".implode('\',\'', $filtros['lista'])."')
				";
			}
			elseif ($filtros['tipo'] == 'antena') {
				
				$filtro .= "
				and cont.serial_antena IN ('".implode('\',\'', $filtros['lista'])."')
				";
			}
			else {
				// Sem filtro por default
			}
			
			/*
			 * Verifica se a tabela do relatório já existe;
			 * 	Caso não, cria a tabela com os dados importados
			 */
			$relatorioExiste = false;
			$sqlVerificaTabelaRelatorio = "
			SELECT 
				tablename
		    FROM   
				pg_catalog.pg_tables 
		    WHERE 
				tablename  = 'tmp_rel_linhas'		
			";
			$rsVerificaTabelaRelatorio = pg_query($this->conn, $sqlVerificaTabelaRelatorio);
			if (!$rsVerificaTabelaRelatorio) { 
				
				throw new Exception("Erro ao verificar se a tabela do relatório já existe.");
			} else {
				
				if (pg_num_rows($rsVerificaTabelaRelatorio) > 0) {
					$relatorioExiste = true;
				}
			}
		
			$sql  = "
			DROP TABLE IF EXISTS tmp_rel_linhas;
			CREATE TEMP TABLE tmp_rel_linhas AS 
			SELECT
				(select equipamento_classe.eqcdescricao from  equipamento_classe where CN.coneqcoid=eqcoid) as classe_contrato,
				(select tpcdescricao from tipo_contrato where tpcoid = CN.conno_tipo) as conno_tipo,
				( SELECT clinome FROM clientes WHERE CN.conclioid=clioid) as clinome,
				(select veiplaca from veiculo where veioid = CN.conveioid) as veiplaca,
				( SELECT eprnome||' >> '||eveversao 
				FROM equipamento, equipamento_versao, equipamento_projeto 
				WHERE 
				eveprojeto=eproid 
				AND evedt_exclusao IS NULL 
				AND equeveoid=eveoid 
				AND equoid=CN.conequoid) as versao_eqpto,
				lincid AS cid,
				lscdescricao AS status_cid,
				(arano_ddd::text||linnumero::text)::bigint AS numero,
				cslstatus AS status,
				linban AS ban,
				to_char(linhabilitacao , 'dd/mm/YYYY') AS data_habilitacao,
				equno_serie AS serie_equipamento,
				eqsdescricao AS status_equipamento,
				CN.CONNUMERO AS CONTRATO,
				cs.csidescricao AS status_contrato,
				cont.serial_antena AS serial_antena,
				--cont.fornecedor_antena,
				CASE
					WHEN ANTENA = 'A' THEN
						'ATIVO'
					WHEN ANTENA  = 'I' THEN
						'INATIVA'
					ELSE
						''
				END fornecedor_antena,
				oploperadora, 
				to_char(linbloqueado , 'dd/mm/YYYY') AS  linbloqueado,
				to_char(lindt_alteracaontc , 'dd/mm/YYYY') AS  lindt_alteracaontc,
				cont.plano
			FROM
				linha
				INNER JOIN operadora_linha              ON oploid = linoploid
				INNER JOIN area                         ON araoid = linaraoid
				LEFT JOIN linha_status_cid              ON lscoid = linlscoid
				LEFT JOIN celular                       ON cellinoid = linoid
				LEFT JOIN celular_status_linha          ON csloid = lincsloid
				LEFT JOIN equipamento e                 ON equno_fone = linnumero AND araoid = equaraoid AND equdt_exclusao IS NULL
				LEFT JOIN equipamento_status           	ON eqsoid = equeqsoid
				LEFT JOIN (
					SELECT 
						conequoid,
						connumero AS contrato,
						csidescricao,
						asatno_serie AS serial_antena,
						asatstatus_fornecedor AS ANTENA,
						asapdescricao as plano
					FROM 
						contrato
						INNER JOIN contrato_situacao ON concsioid = csioid
						INNER JOIN contrato_servico   c  ON consconoid = connumero
						INNER JOIN obrigacao_financeira ON obroid = consobroid AND obrobrigacao ILIKE '%Antena Satelital%'
						INNER JOIN obrigacao_financeira_tecnica ot ON oftcobroid = obroid and oftcprefixo = 'asat'
						INNER JOIN antena_satelital a ON asatoid = consrefioid
						INNER JOIN antena_satelital_plano ON asatasapoid = asapoid
					WHERE 
						consiexclusao IS NULL
						AND oftcexclusao IS NULL) cont on conequoid = e.equoid
				LEFT JOIN CONTRATO CN ON CN.conequoid = e.equoid
				LEFT JOIN contrato_situacao cs on cs.csioid = concsioid
			WHERE
				linexclusao IS NULL
				AND equdt_exclusao IS NULL
				$filtro
			;
			SELECT
				*
			FROM
				tmp_rel_linhas
			";
			
			$result = pg_query($this->conn, $sql);
			
			if (!$result) {
				throw new Exception("Erro ao gerar relatório de linhas");
			}
			
			$numLinhas = pg_num_rows($result);
			
			if ($numLinhas > 0) {
				return $result;
			}
			else {
				return false;
			}
		}
		else {
			throw new Exception("Filtro não informado");
		}
	}
	
	/**
	 * Retorna o relatório para exportação
	 * @throws Exception
	 * @return resource|boolean
	 */
	public function getRelatorioGerado() {
		
		$sql = "
		SELECT
			*
		FROM
			tmp_rel_linhas	
		";
		
		$result = pg_query($this->conn, $sql);
		
		if (!$result) {
			throw new Exception("Erro ao buscar relatório");
		}
		
		if (pg_num_rows($result) > 0) {
			return $result;
		}
		else {
			return false;
		}
	}
}