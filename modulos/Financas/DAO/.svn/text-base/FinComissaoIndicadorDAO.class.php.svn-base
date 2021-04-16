<?php
/**
 * @author	Gabriel Luiz Pereira
 * @email	gabriel.pereira@meta.com.br
 * @since	05/09/2012 
 */

/**
 * Fornece os dados necessarios para o módulo de calculo de comissão
 * @author Gabriel Luiz Pereira
 */
class FinComissaoIndicadorDAO {
	
	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	private $conn;
	
	
	/**
	 * Construtor
	 * @param resource $conn	Link de conexão com o banco
	 */
	public function __construct($conn){
		$this->conn = $conn;
	}
	
	
	/**
	 * Retorna a lista de series de nota fiscal encontradas no banco de dados
	 * @return array
	 */
	public function getSeriesNf() {
		
		// Array de retorno
		$seriesNf = array();
		
		$sqlBuscaSeriesNf = "
		SELECT 
			nfsserie 
		FROM 
			nota_fiscal_serie 
		WHERE 
			nfsdt_exclusao IS NULL 
		ORDER BY 
			nfsserie
		";
		$rsBuscaSeriesNf = pg_query($this->conn,$sqlBuscaSeriesNf);
		if (!$rsBuscaSeriesNf) {
			throw new Exception("Erro ao buscar lista de séries");
		}
		
		if (pg_num_rows($rsBuscaSeriesNf) > 0) {
			while($serie = pg_fetch_object($rsBuscaSeriesNf)) {
				$seriesNf[$serie->nfsserie] = $serie->nfsserie; 
			}
		}
		return $seriesNf;
	}
	
	/**
	 * Retorna a lista de indicadores e corretores de negócio
	 * @return array
	 */
	public function getIndicadorNegocio() {
		
		// Array de retorno
		$listaIndicadorNegocio = array();
				
		$sqlBuscaIndicadoresNegocio = "
		SELECT
			corroid,
			trim(corrnome, ' ') As corrnome
		FROM
			corretor
		WHERE
			correxclusao IS NULL
		ORDER BY
			corrnome
		";
		$rsBuscaIndicadoresNegocio = pg_query($this->conn, $sqlBuscaIndicadoresNegocio);
		if (!$rsBuscaIndicadoresNegocio) {
			throw new Exception("Erro ao buscar lista de indicadores");	
		}
		
		if (pg_num_rows($rsBuscaIndicadoresNegocio) > 0) {
			while($corretor = pg_fetch_object($rsBuscaIndicadoresNegocio)) {
				$listaIndicadorNegocio[$corretor->corroid] = $corretor->corrnome;
			}
		}
		return $listaIndicadorNegocio;
	}
	
	public function getNomeIndicador($corroid) {
		
		$sql = "SELECT corrnome FROM corretor WHERE corroid=$corroid";
		
		$rs = pg_query($this->conn, $sql);
		if (!$rs) {
			throw new Exception("Erro ao buscar indicador");
		}
	
		if (pg_num_rows($rs) > 0) {
			while($corretor = pg_fetch_object($rs)) {
				return $corretor->corrnome;
			}
		}
	}
	
	/**
	 * Efetua a pesquisa por notas fiscais do processo de calculo de comissão
	 * 
	 * @param date('d/m/Y') $dataInicial 			Período de  (nfldt_nota)
	 * @param date('d/m/Y') $dataFinal				Período até (nfldt_nota)
	 * @param integer 		$indicadorNegocio		Corretor (corroid)
	 * @param integer 		$numeroNf				Número da nota fiscal (nflno_numero)
	 * @param string 		$serieNf				Série da nota fiscal (nflserie)
	 * @param integer 		$contrato				Número do contrato (connumero)
	 * @param string 		$statusComissao			Variavel de acordo com as regras do DUM 79889
	 * @param boolean		$somenteComissionaveis	Mostra somente notas fiscaveis comissionáveis
	 * @throws Exception
	 * @return resource|null
	 */
	public function getResultadoPesquisa
	($dataInicial, $dataFinal, $indicadorNegocio, $numeroNf, $serieNf, $contrato, $statusComissao, $somenteComissionaveis) {
	
		
		/*
		 * Filtro da pesquisa
		 */
		$filtro = '';
		
		if (!empty($dataInicial)) {
			$filtro .= " AND nfldt_emissao >= '$dataInicial' ";
		}
		
		if (!empty($dataFinal)) {
			$filtro .= " AND nfldt_emissao <= '$dataFinal' ";
		}
		
		if (!empty($indicadorNegocio)) {
			$filtro .= " AND CR.id_corretor = $indicadorNegocio ";
		}
		
		if (!empty($numeroNf) && is_numeric($numeroNf)) {
			$filtro .= " AND nflno_numero = $numeroNf ";
		}
		
		if (!empty($serieNf)) {
			$filtro .= " AND nflserie = '$serieNf' ";
		}
		
		if (!empty($contrato) && is_numeric($contrato)) {
			$filtro .= " AND connumero = $contrato ";
		}
		
		if (!empty($statusComissao)) {
			
				switch ($statusComissao) {
					case 'PENDENTE'://{
							$filtro .= " 
								AND coinoid IS NULL 
								AND pciitem_comissao = 't'
							";
						break;//}
						
					case 'GERADA'://{
							$filtro .= "
								AND coinoid IS NOT NULL
								AND coindt_pagamento IS NULL
								AND pciitem_comissao = 't'
							";
						break;//}
						
					case 'PAGA'://{
							$filtro .= "
								AND coinoid IS NOT NULL
								AND coindt_pagamento IS NOT NULL
								AND pciitem_comissao = 't'
							";
						break;//}
					
					default:
						break;
				}
		}
		
		if (!empty($somenteComissionaveis) && $somenteComissionaveis == 'on') {
			$filtro .= " AND pciitem_comissao = 't' ";
		}
		
		/**
		 * Pesquisa em banco
		 */
                
		$sqlPesquisaNotasComissao = "
		SELECT
			eqcoid,
			nfloid,
			nflno_numero,
			nflserie,
			connumero,
			nfivl_item,
			CASE 
				WHEN(
					SELECT
						COUNT(*)
					FROM
						titulo
					WHERE
						titnfloid = nota_fiscal.nfloid
						AND titdt_vencimento < NOW() 
						AND titdt_pagamento IS NULL
		
					) > 0 
				THEN
					'SIM'
				ELSE
					'NÃO'
			END AS titulo_vencido,
			obroid,
			obrobrigacao,
			(SELECT corrnome FROM corretor WHERE corroid=CR.id_corretor) AS corrnome,
			CR.id_corretor AS corroid,
			TO_CHAR(nfldt_inclusao, 'dd/mm/YYYY') AS nfldt_faturamento,
			clinome,
			cliuf_com AS uf,
			clicidade_com AS cidade,
			veiplaca,
			coinvl_comissao,
			TO_CHAR(coindt_cadastro, 'dd/mm/YYYY') AS coindt_cadastro ,
			TO_CHAR(coindt_pagamento, 'dd/mm/YYYY') AS coindt_pagamento,
			CASE 
				WHEN pciitem_comissao = 't' AND coinoid IS NULL THEN
					'PENDENTE'
				WHEN coinoid IS NOT NULL AND coindt_pagamento IS NULL AND pciitem_comissao = 't' THEN
					'GERADA'
				WHEN coinoid IS NOT NULL AND coindt_pagamento IS NOT NULL AND pciitem_comissao = 't' THEN
					'PAGA'
				ELSE
					''
			END status_comissao,
			pciitem_comissao
		FROM
			nota_fiscal
			INNER JOIN nota_fiscal_item 			ON (nfino_numero    = nflno_numero AND nfiserie = nflserie)
			INNER JOIN contrato 				ON connumero        = nficonoid
			INNER JOIN equipamento_classe 			ON coneqcoid        = eqcoid
			INNER JOIN obrigacao_financeira			ON (eqcobroid       = obroid AND nfiobroid = obroid)
            INNER JOIN veiculo				ON veioid           = conveioid			
			INNER JOIN clientes				ON clioid           = conclioid
			LEFT  JOIN comissao_indicador_negocio		ON (coinconoid      = connumero  AND coinnflno_numero = nflno_numero AND coinnflserie = nflserie AND coindt_exclusao IS NULL)
			LEFT  JOIN parametros_comissao_indicador	ON pcieqcoid        = eqcoid
			INNER JOIN (
				SELECT 
					CASE WHEN Y.conindicadoroid IS NULL THEN
						Y.concorroid
					ELSE
						Y.conindicadoroid
					END AS id_corretor, Y.connumero AS num_contrato
				FROM contrato Y
				) CR ON CR.num_contrato = connumero
		WHERE 
			1 = 1
			$filtro
		ORDER BY 
			nfloid, 
			connumero		
		";
		$rsPesquisaNotasComissao = pg_query($this->conn, $sqlPesquisaNotasComissao);
		if (!$rsPesquisaNotasComissao) {
			throw new Exception("Erro ao efetuar a pesquisa de notas fiscais");
		}
		/* echo '<pre>';
		echo $sqlPesquisaNotasComissao;
		echo '</pre>'; */
		$numRows = pg_num_rows($rsPesquisaNotasComissao);
		
		if ($numRows > 0) {
			return $rsPesquisaNotasComissao;
		}
		else {
			return null;
		}
	}

	public function getParametrosComissao($classe) {
		
	}
	
	/**
	 * 
	 * @param integer $coinconoid Código do contrato. Foreign Key com a tabela CONTRATO.
	 * @param integer $coinobroid Código da obrigação financeira. Foreign Key com a tabela OBRIGACAO_FINANCEIRA.
	 * @param numeric(12,2) $coinvl_item Valor do item original que a comissão foi calculada.
	 * @param numeric(12,2) $coinvl_comissao Valor da comissão calculada.
	 * @param integer $coincorroid Código do corretor/indicador de negócio da comissão. Foreign Key da tabela Corretor.
	 * @param integer $coinnflno_numero
	 * @param character(3) $coinnflserie
	 */
	public function setComissao($coinconoid,
						$coinobroid,
						$coinvl_item,
						$coinvl_comissao,
						$coincorroid,
						$coinnflno_numero,
						$coinnflserie) {
		
		try {
		
			$cd_usuario = $_SESSION['usuario']['oid'];
			$cd_usuario = ($cd_usuario > 0) ? $cd_usuario : 0;
		
			$sql = "
					SELECT 
						* 
					FROM
						comissao_indicador_negocio
					WHERE
						coinconoid = $coinconoid
						AND coinnflno_numero = $coinnflno_numero
						AND coinnflserie = '$coinnflserie'
					";
			if (!$res = pg_query($this->conn,$sql)) {
				throw new Exception("Erro ao buscar registros existentes. ",1);
			}
			
			if (pg_num_rows($res)>0) {
				$sqlUpdate = "
						UPDATE 
							comissao_indicador_negocio
						SET
							coinconoid=$coinconoid,
							coinobroid=$coinobroid,
							coinvl_item=$coinvl_item,
							coinvl_comissao=$coinvl_comissao,
							coincorroid=$coincorroid,
							coinusuoid_atualizacao=$cd_usuario,
							coindt_atualizacao=now(),
							coinusuoid_exclusao=null,
							coindt_exclusao=null,
							coinnflno_numero=$coinnflno_numero,
							coinnflserie='$coinnflserie'
						WHERE
							coinconoid = $coinconoid
							AND coinnflno_numero = $coinnflno_numero
							AND coinnflserie = '$coinnflserie'
				";
				
				if (!$resUpdate = pg_query($this->conn,$sqlUpdate)) {
					throw new Exception("Erro ao atualizar registro.",1);
				}
				$codigo = 2;
				
			} else {
				$sqlInsert = "
						INSERT INTO comissao_indicador_negocio
						(
							coinconoid,
							coinobroid,
							coinvl_item,
							coinvl_comissao,
							coincorroid,
							coinusuoid_cadastro,
							coinusuoid_atualizacao,
							coinnflno_numero,
							coinnflserie
						) VALUES (
							$coinconoid,
							$coinobroid,
							$coinvl_item,
							$coinvl_comissao,
							$coincorroid,
							$cd_usuario,
							$cd_usuario,
							$coinnflno_numero,
							'$coinnflserie'
						);
						";
				
				if (!$resInsert = pg_query($this->conn,$sqlInsert)) {
					throw new Exception("Erro ao inserir registro. ",1);
				}
				$codigo = 0;
			}
			
			$msg = "Comissões geradas com sucesso. ";
			return array(
						"msg" 	=> $msg,
						"code" 	=> $codigo);
			
		} catch (Exception $e) {
			return array(
						"msg"	=> $e->getMessage(),
						"code" 	=> $e->getCode());
		}
	}

	public function setExcluidos($coinconoid,$coinnflno_numero,$coinnflserie) {
		try {
			$cd_usuario = $_SESSION['usuario']['oid'];
			$cd_usuario = ($cd_usuario > 0) ? $cd_usuario : 0;
			$coinnflserie = trim($coinnflserie);
			
			$sqlUpdate = "
						UPDATE
							comissao_indicador_negocio
						SET
							coinusuoid_exclusao=$cd_usuario,
							coindt_exclusao=now()
						WHERE
							coinconoid = $coinconoid
							AND coinnflno_numero = $coinnflno_numero
							AND coinnflserie = '$coinnflserie'
			";
			
			if (!$resUpdate = pg_query($this->conn,$sqlUpdate)) {
				throw new Exception("Erro ao excluir registro. ",1);
			}
			
			$msg = "Comissões excluídas com sucesso. ";
			$codigo = 0;
			
			return array(
					"msg" 	=> $msg,
					"code" 	=> $codigo);
			
		} catch (Exception $e) {
			return array(
					"msg"	=> $e->getMessage(),
					"code" 	=> $e->getCode());
		}
	}
	
	public function setPagos($coinconoid,$coinnflno_numero,$coinnflserie,$coindt_pagamento,$coinobs) {
		try {
			$coinnflserie = trim($coinnflserie);
			
			$sqlUpdate = "
						UPDATE
							comissao_indicador_negocio
						SET
							coinobs='$coinobs',
							coindt_pagamento='$coindt_pagamento'
						WHERE
							coinconoid = $coinconoid
							AND coinnflno_numero = $coinnflno_numero
							AND coinnflserie = '$coinnflserie'
			";
				
			if (!$resUpdate = pg_query($this->conn,$sqlUpdate)) {
				throw new Exception("Erro ao pagar comissão. ",1);
			}
				
			$msg = "Comissões pagas com sucesso. ";
					$codigo = 0;
						
					return array(
							"msg" 	=> $msg,
							"code" 	=> $codigo);
						
		} catch (Exception $e) {
			return array(
					"msg"	=> $e->getMessage(),
					"code" 	=> $e->getCode());
		}
	}

}