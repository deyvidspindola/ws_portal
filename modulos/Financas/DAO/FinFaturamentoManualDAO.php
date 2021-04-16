<?php

require (_MODULEDIR_ . "Financas/DAO/FinCreditoFuturoDAO.php");
require (_MODULEDIR_ . "Financas/Action/CreditoFuturo.php");

/* [start][ORGMKTOTVS-1929] - Leandro Corso */
//andre
// require _MODULEDIR_.'core/infra/autoload.php';
// use module\Parametro\ParametroIntegracaoTotvs;
// define('INTEGRACAO_TOTVS_ATIVA', ParametroIntegracaoTotvs::getIntegracaoTotvsAtiva());
//fim andre
/* [end][ORGMKTOTVS-1929] - Leandro Corso */

/**
 * Classe de persistência de dados
 *
 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 *        
 */
class FinFaturamentoManualDAO {
	private $conn;
	private $vo; 
	private $params;
	private $contrato;
	private $obrigacao;
	function __construct($conn) {
		$this->conn = $conn;
		$this->finCreditoFuturoDao = new FinCreditoFuturoDAO($conn);
		$this->bo = new CreditoFuturo($this->finCreditoFuturoDao);
	}
	
	/**
	 * Executa uma query
	 * 
	 * @param string $sql
	 *        	SQL a ser executado
	 * @return resource
	 */
	protected function _query($sql) {
		// Suprime erros para lançar exceção ao invés de E_WARNING
		$result = pg_query ( $this->conn, $sql );
		
		if ($result === false) {
			throw new Exception ( pg_last_error ( $this->conn ) );
		}
		
		return $result;
	}
	
	/**
	 * Conta os resultados de uma consulta
	 * 
	 * @param resource $results        	
	 * @return int
	 */
	protected function _count($results) {
		return pg_num_rows ( $results );
	}
	
	/**
	 * Retorna os resultados de uma consulta num array associativo (hash-like)
	 * 
	 * @param resource $results        	
	 * @return array
	 */
	protected function _fetchAll($results) {
		return pg_fetch_all ( $results );
	}
	
	/**
	 * Retorna o resultado de uma coluna num array associativo (hash-like)
	 * 
	 * @param resource $results        	
	 * @return array
	 */
	protected function _fetchAssoc($result) {
		return pg_fetch_assoc ( $result );
	}
	
	/**
	 * Retorna o resultado como um vetor de objetos
	 * 
	 * @param resource $results        	
	 * @return array
	 */
	public function _fetchObj($results) {
		$rows = array_map ( function ($item) {
			return ( object ) $item;
		}, $this->_fetchAll ( $results ) );
		
		return $rows;
	}
	public function begin() {
		pg_query ( $this->conn, "BEGIN;" );
	}
	public function commit() {
		pg_query ( $this->conn, "COMMIT;" );
	}
	public function rollback() {
		pg_query ( $this->conn, "ROLLBACK;" );
	}
	
	/**
	 * Pesquisa de Notas fiscais
	 * 
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 */
	public function pesquisarNotas($parametros = array()) {
		if ($parametros ['dt_ini'] && ! $parametros ['dt_fim'])
			$parametros ['dt_fim'] = $parametros ['dt_ini'];
		if ($parametros ['dt_fim'] && ! $parametros ['dt_ini'])
			$parametros ['dt_ini'] = $parametros ['dt_fim'];
		
		$condicao = ($parametros ['dt_ini'] && $parametros ['dt_fim'] ? " and nfldt_emissao between '{$parametros[dt_ini]}' and '{$parametros[dt_fim]}' " : "") . ($parametros ['nflno_numero'] ? " and nflno_numero=" . $parametros ['nflno_numero'] . " " : "") . ($parametros ['nflserie'] ? " and upper(nflserie)='" . trim ( strtoupper ( $parametros ['nflserie'] ) ) . "' " : "") . ($parametros ['nfloid'] ? " and nfloid in (" . $parametros ['nfloid'] . ") " : "");
		
		$condicaoCliente = intval ( $parametros ['clioid'] ) > 0 ? "AND (nflclioid = " . $parametros ['clioid'] . ")" : "";
		
		$sql = "select 
     				nfloid,
			     	nflno_numero,
					nflserie,
					TO_CHAR(nfldt_referencia,'DD/MM/YYYY') AS nfldt_referencia,
					TO_CHAR(nfldt_inclusao,'DD/MM/YYYY') AS nfldt_inclusao,
					TO_CHAR(nfldt_emissao,'DD/MM/YYYY') AS nfldt_emissao,
					nfldt_emissao AS dt_emissao,
					TO_CHAR(nfldt_envio_grafica,'DD/MM/YYYY') AS nfldt_envio_grafica,
					TO_CHAR(nfldt_vencimento,'DD/MM/YYYY') AS nfldt_vencimento,
					nfltransporte,
					nflnatureza,
					nflvl_total,
					nflvl_desconto,
					nflvlr_pis,
					nflvlr_cofins,
					nflvlr_csll,
					nflvlr_ir,
					nflvlr_iss,
					nflinfcomp
     	 		from nota_fiscal
				where 1=1 
                    $condicaoCliente
					$condicao 
     			order by nflno_numero, lpad(nflserie, 10,'0') limit 2000 ";
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar notas fiscais.' );
		
		return pg_fetch_all ( $rs );
	}
	private function _ultimaDataEmissao() {
		$sql = "select max(nfldt_emissao) as dt_emissao from nota_fiscal where nfldt_emissao < now()";
		$rs = pg_query ( $this->conn, $sql );
		
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar data da ultima nota emitida.' );
		
		$arr = pg_fetch_array ( $rs );
		return $arr ['dt_emissao'];
	}
	public function clientesPesquisas($cliente) {
		$sql = "SELECT 
     			clioid as id,
				clitipo AS tipo,
				clinome AS label,
     			clioid AS retorno,
				clino_cpf AS retornoCPF,
				clino_cgc AS retornoCNPJ
				FROM clientes
				WHERE clidt_exclusao IS NULL AND clinome iLIKE '%$cliente%' ORDER BY clinome ASC LIMIT 100";
		
		return $rsResponse = pg_query ( $this->conn, $sql );
		
		// return $response;
	}
	
	/**
	 * Encontra o Cliente da Nota.
	 * 
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 * @param $ids_notas -
	 *        	ids das notas separados por vírgula. ex.: '5526,6985'
	 */
	public function pesquisarClienteNota($ids_notas) {
		$sql = "select distinct c.* from clientes c
     				join nota_fiscal n on nflclioid=clioid
     			where nfloid in ($ids_notas)
     			order by clinome";
	
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar cliente.' );
		
		return pg_fetch_array ( $rs, 0 );
	}
	
	/**
	 * Busca dados do cliente
	 *
	 * @param $clioid ID
	 *        	do cliente
	 *        	
	 * @return array
	 */
	public function pesquisarCliente($clioid) {
		$sql = "select * from clientes where clioid = " . intval ( $clioid );
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao buscar dados do cliente.' );
		
		return pg_fetch_array ( $rs, 0 );
	}
	
	/**
	 *
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 * @param $ids_notas -
	 *        	ids das notas separados por vírgula. ex.: '5526,6985'
	 */
	public function pesquisarItensNota($ids_notas) {
		$sql = "SELECT distinct connumero
					,nflno_numero
					,nflserie
					,TO_CHAR(nfldt_referencia,'DD/MM/YYYY') AS nfldt_referencia
					,nfldt_emissao AS dt_emissao
     				,tpcoid
					,tpcdescricao
					,nfiobroid
					,nfivl_item
					,nfidesconto
					,obroid
					,obrobrigacao
					,nfids_item
					,CASE
					WHEN obrhabilitacao = true THEN 'L'
					WHEN obrmonitoramento = true THEN 'M'
					ELSE nfitipo 
				    END AS nfitipo
				FROM nota_fiscal				
					JOIN nota_fiscal_item ON nfino_numero = nflno_numero AND nfiserie = nflserie
					left JOIN contrato ON connumero = nficonoid
					left JOIN tipo_contrato ON tpcoid = conno_tipo
					JOIN obrigacao_financeira ON obroid = nfiobroid				
				WHERE nfloid IN ($ids_notas)				
				ORDER BY obrobrigacao,obroid,nfiobroid,nflno_numero,nflserie,connumero ASC";
	
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar itens das notas.' );
		
		$arr = pg_fetch_all ( $rs );
		return $arr;
	}
	
	/**
	 *
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 * @param $connumero -
	 *        	id do contrato a ser encontrado
	 */
	public function getContrato($connumero) {
		if (! $connumero)
			return false;
		
		$sql = "SELECT contrato.*
			     	,tpcoid
			     	,tpcdescricao
		     	FROM contrato
		     		JOIN tipo_contrato ON tpcoid = conno_tipo
		     	WHERE connumero IN ($connumero)";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar contrato.' );
		
		if (pg_num_rows ( $rs ) == 0)
			return false;
		
		$arr = pg_fetch_array ( $rs, 0 );
		return $arr;
	}
	
	/**
	 *
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 * @param $obroid -
	 *        	id da obrigacao fincanceira
	 */
	public function getObrigacaoFinanceira($obroid) {
		$sql = "select obroid, obrobrigacao, obrvl_obrigacao from obrigacao_financeira where obroid=$obroid ";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar obrigacao financeira.' );
		
		if (pg_num_rows ( $rs ) == 0)
			return false;
		
		$arr = pg_fetch_array ( $rs, 0 );
		return $arr;
	}
	
	/**
	 *
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 */
	public function getTransportes() {
		$sql = "SELECT UPPER(mdtrdescricao) AS mdtrdescricao FROM modo_transporte ORDER BY mdtrdescricao ASC";
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar transportes.' );
		
		if (pg_num_rows ( $rs ) == 0)
			return false;
		
		$arr = pg_fetch_all ( $rs );
		return $arr;
	}
	
	/**
	 *
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 * @param $params -
	 *        	array()
	 */
	public function pesquisarContratos($params = array()) {
		
		// $condicoes = array(" clioid=".intval($params['clioid'])." ");
		if ($params ['contrato_item']) {
			$condicoes [] = "connumero={$params[contrato_item]}";
		}
		// SITUACAO DO CONTRATO
		if ($params ['contratos']) {
			$condicoes [] = "concsioid = " . $params ['contratos'];
		}
		
		if ($params ['placa']) {
			$condicoes [] = "upper(veiplaca) ilike '" . pg_escape_string ( strtoupper ( $params ['placa'] ) ) . "%'";
		}
		if ($params ['classe_equipamento']) {
			$condicoes [] = "coneqcoid=" . intval ( $params ['classe_equipamento'] );
		}
		if ($params ['equipamento']) {
			$condicoes [] = "equno_serie=" . $params ['equipamento'];
		}
		if ($params ['tipo_contrato']) {
			$condicoes [] = "conno_tipo=" . $params ['tipo_contrato'];
		}
		
		if ($params ['id_cliente'] != '') {
			$condicoes [] = "clioid = " . $params ['id_cliente'];
		}
		
		$sql = "select     
				     connumero ,
				     veioid, veiplaca,
				     case when (condt_exclusao IS NULL AND conno_tipo <> 14) then true else false end as ativo,
				     equoid, equno_serie,
				     eqcoid, eqcdescricao, --classe equipamento
				     tpcoid, tpcdescricao, --tipo contrato
				     csioid, csidescricao --situacao contrato
			     from contrato
				     join clientes on clioid = conclioid
				     left join veiculo on veioid = conveioid
				     left join equipamento on equoid = conequoid
				     left join equipamento_classe on eqcoid = coneqcoid
				     left join contrato_situacao on csioid=concsioid
				     left join tipo_contrato on tpcoid=conno_tipo
			     where " . implode ( " AND ", $condicoes ) . " order by connumero";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar contratos.' );
		
		$arr = pg_fetch_all ( $rs );
		return $arr;
	}
	
	/**
	 * Busca classes de equipamento
	 * 
	 * @return array
	 */
	public function getClassesEquipamento() {
		$sql = "SELECT eqcoid, eqcdescricao
                FROM equipamento_classe
                ORDER BY eqcdescricao";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar classes de equipamentos.' );
		
		$arr = pg_fetch_all ( $rs );
		return $arr;
	}
	
	/**
	 * Busca classes de equipamento
	 * 
	 * @return array
	 */
	public function getClassesSeries() {
		$sql = "SELECT 
     				nfsoid,
     				nfsserie
                FROM
     				 nota_fiscal_serie
     			WHERE
     				nfsdt_exclusao   is  null
                ORDER BY nfsserie";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar classes de equipamentos.' );
		
		$arr = pg_fetch_all ( $rs );
		return $arr;
	}
	
	/**
	 * Busca tipos de contratos
	 * 
	 * @return array
	 */
	public function getTiposContrato() {
		$sql = "select tpcoid,tpcdescricao from tipo_contrato order by tpcdescricao";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar tipos de contrato.' );
		
		$arr = pg_fetch_all ( $rs );
		return $arr;
	}
	
	/**
	 * Busca Situacoes do contrato
	 * 
	 * @return array
	 */
	public function getSituacaoContrato() {
		$sql = "SELECT csioid,csidescricao FROM contrato_situacao where csiexclusao is null order by csidescricao";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar tipos de contrato.' );
		
		$arr = pg_fetch_all ( $rs );
		return $arr;
	}
	
	/**
	 * Busca Situacoes do contrato
	 * 
	 * @return array
	 */
	public function getFormasCobranca() {
		$sql = "SELECT forcoid, forcnome 
     			FROM forma_cobranca
     			WHERE forccobranca IS TRUE AND LENGTH(forcnome) > 1
     			ORDER BY forcnome";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar as formas de pagamento.' );
		
		$arr = pg_fetch_all ( $rs );
		return $arr;
	}
	
	/**
	 * Busca Obrigações financeiras
	 * 
	 * @return array
	 */
	public function getObrigacoesFinanceiras($params = array()) {
		
		// $condicoes = array("1=1");
		if ($params ['obroid'])
			$condicoes [] = "obroid=" . intval ( $params ['obroid'] );
		if ($params ['obrobrigacao'])
			$condicoes [] = "obrobrigacao ilike '" . pg_escape_string ( trim ( $params ['obrobrigacao'] ) ) . "%'";
		$sql = "select obroid, obrobrigacao, obrmonitoramento AS monitoramento, obrhabilitacao AS locacao, obrvl_obrigacao   from obrigacao_financeira where obrdt_exclusao is null and " . implode ( " and ", $condicoes ) . " order by obrobrigacao";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar obrigação financeira.' );
			
			// $arr = pg_fetch_all($rs);
		
		$retorno = array ();
		
		while ( $row = pg_fetch_assoc ( $rs ) ) {
			
			$row ['tipo_item'] = '0';
			
			if ($row ['monitoramento'] == 't' && $row ['locacao'] == 'f') {
				$row ['tipo_item'] = 'M';
			} elseif ($row ['monitoramento'] == 'f' && $row ['locacao'] == 't') {
				$row ['tipo_item'] = 'L';
			}
			
			unset ( $row ['monitoramento'] );
			unset ( $row ['locacao'] );
			
			$retorno [] = $row;
		}
		
		return $retorno;
	}
	
	/**
	 * Busca Fontes Pagadoras do Cliente, contrato
	 * - limitado em 1 fonte pagadora por limitação da tela e especificacao.
	 * 
	 * @return array
	 */
	public function getFontesPagadoras($clioid, $contrato = null, $limit = null) {
		$condContrato = ($contrato ? " and connumero=$contrato " : "");
		$condLimit = ($limit ? "limit $limit" : "");
		$sql = "(SELECT DISTINCT 'M' AS tipopg, connumero, c.*
				   FROM clientes c
				   JOIN tipo_contrato t ON t.tpccliente_pagador_monitoramento = c.clioid
				   JOIN contrato ct ON ct.conno_tipo=t.tpcoid 
     			 WHERE ct.conclioid = $clioid $condContrato $condLimit)
     			 
				UNION ALL
				
				  (SELECT DISTINCT 'L' AS tipopg, connumero, c.*
				   FROM clientes c
				   JOIN tipo_contrato t ON t.tpccliente_pagador_locacao = c.clioid
				   JOIN contrato ct ON ct.conno_tipo=t.tpcoid 
     			WHERE ct.conclioid = $clioid $condContrato $condLimit)";
		
		$rs = pg_query ( $this->conn, $sql );
		if (! is_resource ( $rs ))
			throw new Exception ( 'Falha ao consultar fontes pagadoras.' );
		
		if (pg_num_rows ( $rs ) == 0)
			return false;
		
		$arr = pg_fetch_all ( $rs );
		return $arr;
	}
	public function gerarNotas($vo, $params) {
		$this->vo = $vo;
		$this->params = $params;
		$this->_validarNota ();
		$notasGeradas = array ();
		
		/*
		 * //CRIA NOTAS REFERENTES A PAGADORES. LOCAÇÃO E MONITORAMENTO. //RETIRADO DO PROJETO INICIAL, SERÁ IMPLEMENTADO POSTERIORMENTE $pagadores = $this->getFontesPagadoras($this->vo->cliente['clioid']); if($pagadores){ foreach ($pagadores as $pagador){ $notasGeradas[] = $this->_emitirNovaNota($pagador); } }
		 */
		
		// GERAR NOTAS PARA ITENS RESTANTES PARA O CLIENTE ONDE NÃO FORAM CRIADAS NOTAS PARA OS PAGADORES.
		$notasGeradas [] = $this->_emitirNovaNota ();
		
		return $notasGeradas;
	}
	public function gerarPreviaNotas($vo, $params) {
		$this->vo = $vo;
		$this->params = $params;
		
		$this->_validarNota ();
		$notasGeradas = array ();
		
		// GERAR NOTAS PARA ITENS RESTANTES PARA O CLIENTE ONDE NÃO FORAM CRIADAS NOTAS PARA OS PAGADORES.
		$notasGeradas [] = $this->_simularEmissaoNovaNota ();
		
		return $notasGeradas;
	}
	public function validarNotas($vo, $params) {
		$this->vo = $vo;
		$this->params = $params;
		$this->_validarNota ();
		return true;
	}
	protected function _validarNota() {
		if (! $this->vo->cliente ['clioid'])
			throw new Exception ( 'Cliente não informado.' );
		
		if (! $this->vo->getItens () || count ( $this->vo->getItens () ) == 0)
			throw new Exception ( 'Não é possível gerar nota sem cadastar ítens.' );
		
		$arrdtv = explode ( "/", $this->params ['dt_venc'] );
		$dtv = $arrdtv [2] . "-" . $arrdtv [1] . "-" . $arrdtv [0];
		
		$arrdt = explode ( "/", $this->params ['dt_emi'] );
		$dt = $arrdt [2] . "-" . $arrdt [1] . "-" . $arrdt [0];
		
		if ($this->_ultimaDataEmissao () > $dt)
			throw new Exception ( "A data de emissão não pode ser menor que a data da última nota fiscal gerada." );
		
		if (strtotime ( $dtv ) < strtotime ( date ( "Y-m-d" ) ))
			throw new Exception ( "Data de vencimento não pode ser menor que a data de hoje." );
		
		$this->params ['dt_referencia'] = date ( "01/m/Y", strtotime ( $dt ) );
		
		foreach ( $this->vo->getItens () as $item ) {
			if (! isset ( $item ['nfivl_item'] ) || floatval ( $item ['nfivl_item'] ) == 0.00)
				throw new Exception ( "Os itens não podem ter valor unitário igual a 0,00" );
			
			if (! isset ( $item ['nfitipo'] ) || ! in_array ( strtoupper ( $item ['nfitipo'] ), array (
					'L',
					'M' 
			) ))
				throw new Exception ( "Todos itens devem ser de Locação ou Monitoramento (Serviços)." );
				
				// @todo - validacao de contrato nao é obrigatoria quando nao informado.
				// if(!isset($item['connumero']))
				// throw new Exception("Todos os itens devem ter um número de contrato válido.");
			
			if (trim ( $item ['connumero'] ) != "") {
				$sql = "SELECT * FROM contrato WHERE connumero = {$item['connumero']} AND condt_exclusao IS NULL AND concsioid = 1";
				$this->contrato = $this->getContrato ( $item ['connumero'] );
				if (! $this->contrato)
					throw new Exception ( "O contrato {$item['connumero']} não existe ou não está ativo." );
			}
			
			if (! isset ( $item ['obroid'] ))
				throw new Exception ( "Todos os itens devem ter obrigação financeira." );
			
			$this->obrigacao = $this->getObrigacaoFinanceira ( $item ['obroid'] );
			if (! $this->obrigacao)
				throw new Exception ( "A obrigação financeira \"{$item['obrobrigacao']}\" não existe." );
		}
		
		return true;
	}
	
	/**
	 * Emissão de uma nota fiscal
	 * 
	 * @param array $itens        	
	 * @return boolean
	 */

	protected function _emitirNovaNota($pagador = null) {
	//andre
	
		// Vetor de armazenamento de itens (por cliente e tipo)
		$itensFiltrados = array ();
		$itens = $this->vo->getItens ();
                
		if (count ( $itens ) == 0)
			return false;
		
		/**
		 * //QUANDO FOR GERAR NOTAS A CLIENTE PAGADOR, DEVERÁ FILTRAR PELO TIPO PAGADOR E CONTRATO DO PAGADOR.
		 * foreach($itens as $item){
		 * if($item['nfitipo']==$pagador['tipopg']){
		 * $itensFiltrados[]=$item;
		 * }
		 * }
		 * if(count($itensFiltrados)==0)
		 * return false;
		 */
			
		// Inserção de nova nota: busca série e próximo número
		$sql = "SELECT
                    MAX(nflno_numero) + 1 AS nflno_numero,
                    '" . $this->params ['nflserie'] . "' AS nflserie
     			FROM 
                    nota_fiscal
                WHERE
                    nflserie = '" . $this->params ['nflserie'] . "'";
		$dadosNota = $this->_fetchAssoc ( $this->_query ( $sql ) );
		
		// Código do usuário logado
		$cdUsuario = $_SESSION ['usuario'] ['oid'];
		
		// Insere nova nota
		// @todo - transporte esta sendo gravado 'RODOVIÁRIO' por nao haver campo no protótipo.
		$infCompNfe = str_replace('|', ' ', html_entity_decode($this->params['infCompNfe']));

		$sql = "INSERT INTO nota_fiscal (
	     			nfldt_inclusao
     				, nfldt_faturamento
	     			, nfldt_nota
	     			, nfldt_emissao
	     			, nfldt_referencia
	     			, nfldt_vencimento
	     			, nflno_numero
	     			, nflserie
	     			, nflusuoid
	     			, nflclioid
	     			, nflvl_total
	     			, nflvl_desconto
					, nflnatureza
	        		, nfltransporte
	        		, nflclioid_fatura
					, nflinfcomp
     			) VALUES (
	     			NOW()
	     			, NOW()
	     			, NOW()
	     			, '" . $this->params ['dt_emi'] . "'
	     			, '" . $this->params ['dt_referencia'] . "'
	     			, '" . $this->params ['dt_venc'] . "'
	     			, {$dadosNota['nflno_numero']}
			     	, '{$dadosNota['nflserie']}'
			     	, {$cdUsuario}
			     	, " . $this->vo->cliente ['clioid'] . "
			     	, 0.00
			     	, 0.00
					, '" . $this->vo->getNatureza () . "'
	        		, 'RODOVIARIO'
					, " . $this->params ['forcoid'] . "
					, $1
		     	)
		     	RETURNING nfloid, nflno_numero, nflserie";
		
		$result1 = pg_prepare($this->conn, "sqlquery", $sql);
		$nota = $this->_fetchAssoc(pg_execute($this->conn, "sqlquery", array($infCompNfe)));
		
		$sql = "UPDATE nota_fiscal
                        SET nflnota_ant = {$nota['nflno_numero']}
                     WHERE (nfloid = {$nota['nfloid']})";
		
		$this->_query ( $sql );
		
		// Itera sobre cada item do tipo, inserindo nota_fiscal_item e titulo
		foreach ( $itens as $item ) {
			// Seleciona a obrigação financeira
			$sql = "SELECT
                        *
                    FROM
                        obrigacao_financeira
                    WHERE obroid = {$item['obroid']}";
			$obrigacao = $this->_fetchAssoc ( $this->_query ( $sql ) );
			
			$sql = "SELECT
                             *
                         FROM
                             contrato_obrigacao_financeira
                         WHERE cofconoid = '" . intval ( $item ['connumero'] ) . "'
                           AND cofobroid = '" . intval ( $item ['obroid'] ) . "'
                           AND cofdt_termino is null";
			$obrigacao_valor = $this->_fetchAssoc ( $this->_query ( $sql ) );
			$obrigacao_valor ['cofvl_obrigacao'] = floatval ( $obrigacao_valor ['cofvl_obrigacao'] ) > 0 ? floatval ( $obrigacao_valor ['cofvl_obrigacao'] ) : floatval ( $obrigacao ['obrvl_obrigacao'] );
			
			// Insere novo item na nota fiscal
			$dataReferencia = $this->params ['dt_referencia'];
			$sql = "INSERT INTO nota_fiscal_item (
	     		nfino_numero
	     		, nfiserie
	     		, nficonoid
	     		, nfiobroid
	     		, nfivl_item
	            , nfidt_referencia
	     		, nfidesconto 
	     		, nfidt_inclusao
	     		, nfinfloid
	     		, nfivl_obrigacao
	     		, nfids_item
	     		, nfitipo
	     		, nfinota_ant
     		) VALUES (
	     		{$nota['nflno_numero']}
	     		, '{$nota['nflserie']}'
	     		, " . (trim ( $item ['connumero'] ) ? $item ['connumero'] : "null") . "
	     		, {$item['obroid']}
	     		, " . floatval ( $item ['nfivl_item'] ) . "
	            , '{$dataReferencia}'
	     		, " . floatval ( $item ['nfidesconto'] ) . "
	     		, NOW()
	     		, {$nota['nfloid']}
	     		, " . $obrigacao_valor ['cofvl_obrigacao'] . "
	     		, '" . $obrigacao ['obrobrigacao'] . "'
	     		, '{$item['nfitipo']}'
	     		, {$nota['nflno_numero']}
     		)";
			$this->_query ( $sql );
                        
		}
		
		/*
		 * $sql = "SELECT SUM(nfivl_item) AS valor_total , SUM(nfidesconto) AS valor_descontos FROM nota_fiscal_item WHERE nfino_numero = {$nota['nflno_numero']} AND nfiserie = '{$nota['nflserie']}' "; $totais = $this->_fetchAssoc($this->_query($sql));
		 */
		
		$totais ['M'] ['valor_total'] = '0';
		$totais ['L'] ['valor_total'] = '0';
		
		foreach ( $itens as $key => $value ) {
			$totais [$value ['nfitipo']] ['valor_total'] += floatval ( $value ['nfivl_item'] ) - floatval ( $value ['nfidesconto'] );
			$totais ['valor_descontos'] += floatval ( $value ['nfidesconto'] );
			$totais ['valor_total'] += floatval ( $value ['nfivl_item'] );
		}
		
		/**
		 * Cálculos de impostos para a nota fiscal.
		 */
		$vlr_iss = $nflvlr_pis = $nflvlr_cofins = $nflvlr_csll = $nflvlr_ir = 0;
		
		// O ISS não tem valor limite para retenção.
		// Cálculo: aplicar sobre o valor total da NF o percentual do ISS encontrado em cadastro do cliente.
		if ($this->vo->cliente ['cliret_iss_perc'] > 0 && $totais ['valor_total'] > 0)
			$vlr_iss = round ( $this->vo->cliente ['cliret_iss_perc'] / 100 * floatval ( $totais ['valor_total'] ), 2 );
			
			// O PIS é sobre o valor total da nota fiscal acima de R$ 5.000,00.
		if ($this->vo->cliente ['cliret_pis_perc'] > 0 && $totais ['valor_total'] > 5000)
			$nflvlr_pis = round ( $this->vo->cliente ['cliret_pis_perc'] / 100 * floatval ( $totais ['valor_total'] ), 2 );
			
			// O COFINS é sobre o valor total da nota fiscal acima de R$ 5.000,00.
		if ($this->vo->cliente ['cliret_cofins_perc'] > 0 && $totais ['valor_total'] > 5000)
			$nflvlr_cofins = round ( $this->vo->cliente ['cliret_cofins_perc'] / 100 * floatval ( $totais ['valor_total'] ), 2 );
			
			// O CSLL é sobre o valor total da nota fiscal acima de R$ 5.000,00.
		if ($this->vo->cliente ['cliret_csll_perc'] > 0 && $totais ['valor_total'] > 5000)
			$nflvlr_csll = round ( $this->vo->cliente ['cliret_csll_perc'] / 100 * floatval ( $totais ['valor_total'] ), 2 );
		
		// Regra removida ASM 276807 GMUD 7393
		// O IR (1%) é sobre o valor total da nota fiscal. Para esta retenção, a NF tem que possuir um valor acima de R$ 1.000,00.
		//if ($this->vo->cliente ['cliret_piscofins'] == 't' && $totais ['valor_total'] > 1000)
			//$nflvlr_ir = (0.01) * floatval ( $totais ['valor_total'] );
		
		/**
		 * Lançamento de credito futuro *
		 */
		// Crio objetos do crédito futuro		
		$creditosFuturos = $this->vo->getCreditos(); 
		$arrayCreditosFuturo = array(); 
		
		foreach ($creditosFuturos as $key => $credito) { 
			$creditoFuturoObj = new CreditoFuturoVO(); 
			$creditoFuturoParcelaObj = new CreditoFuturoParcelaVO(); 
			$creditoFuturoMotivoObj = new CreditoFuturoMotivoCreditoVO(); 
			$creditoFuturoObj->id = $credito['credito_id']; 
			$creditoFuturoObj->contratoIndicado = $credito['connumero']; 
			$creditoFuturoObj->aplicarDescontoSobre = $credito['cfoaplicar_desconto']; 
			$creditoFuturoObj->valor = $credito['valor']; 
			$creditoFuturoObj->obrigacaoFinanceiraDesconto = $credito['obrigacao_id']; 
			$creditoFuturoObj->tipoDesconto = $credito['cfotipo_desconto']; 
			$creditoFuturoObj->origem = 3; 
			$creditoFuturoMotivoObj->id = $credito['motivo_credito_id']; 
			$creditoFuturoMotivoObj->tipo = $credito['tipo_motivo_credito']; 
			$creditoFuturoMotivoObj->descricao = $credito['cfmcdescricao']; 
			$creditoFuturoParcelaObj->id = $credito['parcela_id']; 
			$creditoFuturoParcelaObj->numero = $credito['parcela_numero']; 
			$creditoFuturoObj->Parcelas = $creditoFuturoParcelaObj; 
			$creditoFuturoObj->MotivoCredito = $creditoFuturoMotivoObj; 
			$arrayCreditosFuturo[] = $creditoFuturoObj; 
			# code... }
		}		 
		//andre
		// se escolher conceder creditos
		if (isset ( $this->params ['conceder_creditos'] ) && $this->params ['conceder_creditos'] == '1') {
			$retorno = $this->bo->processarDesconto ( $arrayCreditosFuturo, $totais, $nota, true );
			foreach ( $retorno ['creditos'] as $key => $itemDesconto ) {
				// TODO
				if ($itemDesconto->aplicarDescontoSobre == '1') {
					$itemDesconto->nfitipo = 'M';
				} else {
					$itemDesconto->nfitipo = 'L';
				}
				
				$itemDesconto->connumero = '';
				
				$itemDesconto->tpcdescricao = '';
				
				// Seleciona a obrigação financeira
				$sql = "SELECT
                            *
                        FROM
                            obrigacao_financeira
                        WHERE obroid = {$itemDesconto->obrigacaoFinanceiraDesconto}";
				$obrigacao = $this->_fetchAssoc ( $this->_query ( $sql ) );
				
				$itemDesconto->obrobrigacao = $obrigacao ['obrobrigacao'];
				
				$sql = "SELECT
                                 *
                             FROM
                                 contrato_obrigacao_financeira
                             WHERE cofconoid = '" . intval ( $itemDesconto->connumero ) . "'
                               AND cofobroid = '" . intval ( $itemDesconto->obrigacaoFinanceiraDesconto ) . "'
                               AND cofdt_termino is null";
				
				$obrigacao_valor = $this->_fetchAssoc ( $this->_query ( $sql ) );
				$obrigacao_valor ['cofvl_obrigacao'] = floatval ( $obrigacao_valor ['cofvl_obrigacao'] ) > 0 ? floatval ( $obrigacao_valor ['cofvl_obrigacao'] ) : floatval ( $obrigacao ['obrvl_obrigacao'] );
				
				$dataReferencia = $this->params ['dt_referencia'];
				
				$sql = "INSERT INTO nota_fiscal_item (
                    nfino_numero
                    , nfiserie
                    , nficonoid
                    , nfiobroid
                    , nfivl_item
                    , nfidt_referencia
                    , nfidesconto
                    , nfidt_inclusao
                    , nfinfloid
                    , nfivl_obrigacao
                    , nfids_item
                    , nfitipo
                    , nfinota_ant
                ) VALUES (
                    {$nota['nflno_numero']}
                    , '{$nota['nflserie']}'
                    , " . (trim ( $itemDesconto->connumero ) ? $itemDesconto->connumero : "null") . "
                    , {$itemDesconto->obrigacaoFinanceiraDesconto}
                    , -" . floatval ( $itemDesconto->desconto_aplicado ) . "
                    , '{$dataReferencia}'
                    , 0.00
                    , NOW()
                    , {$nota['nfloid']}
                    , " . $obrigacao_valor ['cofvl_obrigacao'] . "
                    , '" . $obrigacao ['obrobrigacao'] . "'
                    , '{$itemDesconto->nfitipo}'
                    , {$nota['nflno_numero']}
                )";
				$this->_query ( $sql );
				
				$itemDesconto = ( array ) $itemDesconto;
				
				array_push ( $itens, $itemDesconto );
			}
			
			$totais ['valor_total'] = $retorno ['total'];
		} else {
			// caso o usuário queira não conceder créditos futuros, é gravado historico de cancelamento de desconto cod. 6
			
			$historicosCancelamento = array ();
			
			/*
			 * foreach ($arrayCreditosFuturo as $key => $creditoFuturo) { //utilizo o método prepararHistoricosAplicacao() da BO também para preparar os historicos de cancelamento de descontos. $historicosCancelamento[] = $this->bo->prepararHistoricosAplicacao($creditoFuturo, $nota, 6); }
			 */
			
			foreach ( $historicosCancelamento as $key => $historicoCancelamentoItem ) {
				// utilizo o método salvarHistoricosAplicacao() da BO também para salvar os historicos de cancelamento de descontos.
				$this->bo->salvarHistoricosAplicacao ( $historicoCancelamentoItem );
			}
		}
		//andre fim

		$nota ['itens_nota'] = $itens;
		
		$nParcelas = $this->params ['parc'];
		$total = floatval ( $totais ['valor_total'] ) - floatval ( $totais ['valor_descontos'] );
		$parcela = floatval ( $totais ['valor_total'] );
		$totalParcelas = floatval ( $totais ['valor_total'] );
		$desconto_parcela = floatval ( $totais ['valor_descontos'] );
		$totalDesconto = floatval ( $totais ['valor_descontos'] );
		$impostosPisCofinsCsll = ( float ) floatval ( $nflvlr_pis ) + floatval ( $nflvlr_cofins ) + floatval ( $nflvlr_csll );
		
		$vlr_issP = 0;
		$nflvlr_pisP = 0;
		$nflvlr_cofinsP = 0;
		if ($nParcelas > 1) {
			if ($parcela > 0)
				$parcela = round ( $parcela / $nParcelas, 2 );
			
			if ($desconto_parcela > 0)
				$desconto_parcela = round ( $desconto_parcela / $nParcelas, 2 );
				
				// impostos por parcelas de titulo.
			if ($vlr_iss > 0)
				$vlr_issP = round ( $vlr_iss / $nParcelas, 2 );
			
			if ($nflvlr_pis > 0)
				$nflvlr_pisP = round ( $nflvlr_pis / $nParcelas, 2 );
			
			if ($nflvlr_cofins > 0)
				$nflvlr_cofinsP = round ( $nflvlr_cofins / $nParcelas, 2 );
		}
		
		// Prepara data vencimento para o título
		$vencimentoString = $this->params ['dt_venc'];
		$vencimentoArray = explode ( "/", $vencimentoString );
		$vencimentoAno = $vencimentoArray [2];
		$vencimentoMes = str_pad ( $vencimentoArray [1], "0", STR_PAD_LEFT );
		$vencimentoDia = str_pad ( $vencimentoArray [0], "0", STR_PAD_LEFT );
		$vencimentoString = $vencimentoAno . "-" . $vencimentoMes . "-" . $vencimentoDia;
		$time = strtotime ( $vencimentoString );
		
		// Valor do Titulo por parcela
		$valorParcelaTitulo = 0;
		if ($totalParcelas > 0) {
			$valorParcelaTitulo = round ( (($totalParcelas - $totalDesconto) / $nParcelas), 2 );
		}
		$valorIssTitulo = 0;
		$valorIrTitulo = 0;
		$valorDescontoTitulo = 0;
		$valorPisCofins = ($impostosPisCofinsCsll > 0) ? round ( ($impostosPisCofinsCsll / $nParcelas), 2 ) : 0;
		
		// Novo modo de inserção de titulos, com datas e vencimentos parametrizado pelo usuário.
		$parcelasParametrizadas = $this->params ['parcela'];
		
		$i = 1;
		

		foreach ( $parcelasParametrizadas as $key => $titulo ) {
			// Valor do ISS por parcela
			$valorIssTitulo = $vlr_issP;
			
			// Valor do IR por parcela
			$valorIrTitulo = $nflvlr_ir;
			if ($nflvlr_ir > 0 && $nParcelas > 1) {
				$valorIrTitulo = round ( $nflvlr_ir / $nParcelas, 2 );
			}
			
			/**
			 * Verifica se há dízima periódica do valor da parcela, ISS, IR e desconto.
			 */
			if ($i == $nParcelas && $nParcelas > 1) {
				// Verifica o valor da parcela
				// $valorParcelaTitulo = $this->calcularValorParcela($totalParcelas, $nParcelas, $totalDesconto);
				
				// Verifica no valor do ISS
				$valorIssTitulo = $this->calcularValorParcela ( $vlr_iss, $nParcelas );
				
				// Verifica no valor do IR
				$valorIrTitulo = $this->calcularValorParcela ( $nflvlr_ir, $nParcelas );
				
				// Verifica no valor total dos impostos
				$valorPisCofins = $this->calcularValorParcela ( $impostosPisCofinsCsll, $nParcelas );
			}
			
			// Insere um novo título com os dados
			// @todo - verificar calculo do IR
			$dataVencimento = $titulo ['data'];
			$dataReferenciaTitulo = explode ( '/', $titulo ['data'] );
			$dataReferenciaTitulo = strtotime ( $dataReferenciaTitulo [2] . '-' . $dataReferenciaTitulo [1] . '-' . $dataReferenciaTitulo [0] );
			$dataReferenciaTitulo = date ( '01/m/Y' ); // Data de referencia estava utilizando a data de vencimento em vez da data atual.
			
			$numParcela = count ( $parcelasParametrizadas ) == 1 ? 0 : $i;
			
			$valorParcelaTitulo = str_replace ( '.', '', $titulo ['valor'] );
			$valorParcelaTitulo = str_replace ( ',', '.', $valorParcelaTitulo );
			
			$totalVerificacao = number_format ( $totais ['valor_total'], 2, '.', '' );
			
			

			//ORGMKTOTVS-1143 [Inativar a geração de títulos] - Inicio
			if (floatval ( $totalVerificacao ) == 0.01) {
				
				$sqlUpdateNf = "UPDATE
                                    nota_fiscal
                                SET
                                    nflvl_desconto = 0.01
                                WHERE
                                    nfloid = " . $nota ['nfloid'] . "
                ";

				$this->_query ( $sqlUpdateNf );
			}
			
			
			/* [start][ORGMKTOTVS-1929] - Leandro Corso */
			if(!INTEGRACAO_TOTVS){
			/* [end][ORGMKTOTVS-1929] - Leandro Corso */

				if (floatval ( $totalVerificacao ) == 0.01) {
					

					$sql = "INSERT INTO titulo (
	                        titnfloid
	                        , titno_parcela
	                        , titvl_titulo 
	                        , titvl_pagamento
	                        , titdt_pagamento
	                        , titdt_credito
	                        , titobs_cancelamento
	                        , titformacobranca
	                        , titdt_inclusao
	                        , tittaxa_administrativa
	                        , titmdescoid
	                        , titcfbbanco
	                        , tittaxa_cobrterc
	                        , titobs_historico
	                        , titvlr_comissao_ch_terc
	                        , titdt_referencia
	                        , titdt_vencimento
	                        , titemissao
	                        , titclioid
	                        , titvl_ir
	                        , titvl_iss
	                        , titvl_piscofins
	                        , titvl_desconto
	                    ) VALUES (
	                        {$nota['nfloid']}
	                        , $numParcela
	                        , 0.01
	                        , 0.01
	                        , 'NOW()'
	                        , 'NOW()'
	                        , 'baixa automática'
	                        , 6
	                        , '01/" . date ( 'm/Y' ) . "'
	                        , 0.00
	                        , 60
	                        , 991
	                        , 0.00
	                        , 'baixa automática'
	                        , 0.00
	                        , date_trunc('month', current_date)::date
	                        , '{$dataVencimento}'
	                        , '" . $this->params ['dt_emi'] . "'
	                        , " . $this->vo->cliente ['clioid'] . "
	                        , $valorIrTitulo
	                        , $valorIssTitulo
	                        , $valorPisCofins
	                        , 0.01
	                    )";
				
				} else {
				
					$sql = "INSERT INTO titulo (
	                        titnfloid
	                        , titno_parcela
	                        , titdt_referencia
	                        , titdt_vencimento
	                        , titemissao
	                        , titvl_titulo
	                        , titclioid
	                        , titvl_ir
	                        , titvl_iss
	                        , titformacobranca
	                        , titvl_piscofins
	                    ) VALUES (
	                        {$nota['nfloid']}
	                        , $numParcela
	                        , '{$dataReferenciaTitulo}'
	                        , '{$dataVencimento}'
	                        , '" . $this->params ['dt_emi'] . "'
	                        , " . $valorParcelaTitulo . "
	                        , " . $this->vo->cliente ['clioid'] . "
	                        , $valorIrTitulo
	                        , $valorIssTitulo
	                        , " . $this->params ['forcoid'] . "
	                        , $valorPisCofins
	                    )";
				}
    			$this->_query ( $sql );
				
			}

            //ORGMKTOTVS-1143 [Inativar a geração de títulos] - Fim



			if (floatval ( $totalVerificacao ) == 0.01) {
				break;
			}
			
			$i ++;
		}
		
		// @todo - aplicar calculo do ir, falta a definicao do calculo.
		$sql = "UPDATE nota_fiscal SET 
					     	nflvl_total      = " . floatval ( $totais ['valor_total'] ) . "
					     	,nflvl_desconto  = " . floatval ( $totais ['valor_descontos'] ) . "
							, nflvlr_pis 	   = $nflvlr_pis
							, nflvlr_cofins    = $nflvlr_cofins
							, nflvlr_csll 	   = $nflvlr_csll
							, nflvlr_ir        = $nflvlr_ir
							, nflvlr_iss       = $vlr_iss
                            , nflvlr_piscofins = $impostosPisCofinsCsll
        				WHERE
		     			nfloid = {$nota['nfloid']}";
		
		$obs = "- Faturamento Manual realizado em " . date ( "d/m/Y" ) . " ás " . date ( "H:i" ) . " pelo usuário " . $_SESSION ['usuario'] ['nome'] . " <br /> ";
		


		if ($this->vo->ids_notas != 'null') {
			
			$listNotas = $this->pesquisarNotas ( array (
					"clioid" => $this->vo->cliente ['clioid'],
					"nfloid" => $this->vo->ids_notas 
			) );
			
			$obs .= "- Nota(s) Fiscal(ais) de Origem: <br />" . "Número: ";
			foreach ( $listNotas as $notaOrigem ) {
				$obs .= "{$notaOrigem['nflno_numero']}/{$notaOrigem['nflserie']}  Data de Emissão:  {$notaOrigem['nfldt_emissao']} <br />";
			}
			$obs .= "<br />";
		}
		




		$obs .= "- Nota Fiscal gerada: <br />" . "Número: {$nota['nflno_numero']}/{$nota['nflserie']}  Data de Emissão: " . $this->params ['dt_emi'] . " ";
				
		$sql_cli_his = "SELECT cliente_historico_i(" . $this->vo->cliente ['clioid'] . ", $cdUsuario,'$obs','A','','');";
		
		
		$this->_query ( $sql_cli_his );
		
		
		$this->_query ( $sql );
		

		return $nota;
	}

	private function calcularValorParcela($valorTotal, $numeroParcelas, $valorDesconto = 0) {
		$valorBase = $valorTotal - $valorDesconto;
		
		if ($valorTotal == 0) {
			return 0;
		}
		
		if ($numeroParcelas == 0) {
			return 0;
		}
		
		$valorParcelaOriginal = ($valorBase / $numeroParcelas);
		$valorParcela = round ( ($valorBase / $numeroParcelas), 2 );
		
		if ($valorParcela > $valorParcelaOriginal) {
			$diferenca = (($numeroParcelas * $valorParcela) - $valorBase);
			
			return ($valorParcela - $diferenca);
		} else {
			$diferenca = ($valorBase - ($numeroParcelas * $valorParcela));
			return ($valorParcela + $diferenca);
		}
	}
	


     /**
      * Emissão de uma nota fiscal
      * @param   array   $itens
      * @return  boolean
      */
     protected function _simularEmissaoNovaNota($pagador=null) {
        // Vetor de armazenamento de itens (por cliente e tipo)
        $itensFiltrados = array();
        $itens = $this->vo->getItens();    
        if(count($itens)==0)
            return false;   


        //1.seleciona o numero da nota

         
        // Código do usuário logado
        $cdUsuario = $_SESSION['usuario']['oid'];
         
        //2.insere o novo registrode NF

        $nota['nfloid'] = '';
        $nota['nflno_numero'] = '';
        $nota['nflserie'] = '';
                
        //3.realiza update na nota fical criada
            
        //4.insere item a item na tabela nota fical item     
        

        //5.resgata a soma dos valores e desconto dos itens
        $totais['M']['valor_total'] = '0';
        $totais['L']['valor_total'] = '0';

        foreach ($itens as $key => $value) {
            $totais[$value['nfitipo']]['valor_total'] += floatval($value['nfivl_item']) - floatval($value['nfidesconto']);
            $totais['valor_descontos'] += floatval($value['nfidesconto']);
            $totais['valor_total'] += floatval($value['nfivl_item']);
        }

        //6.Realiza calculo de descontos baseados nos valores
        /**
         * Cálculos de impostos para a nota fiscal.
         */ 
        $vlr_iss = $nflvlr_pis = $nflvlr_cofins = $nflvlr_csll = $nflvlr_ir = 0;    
        
        //O ISS não tem valor limite para retenção.
        //Cálculo: aplicar sobre o valor total da NF o percentual do ISS encontrado em cadastro do cliente.
        if($this->vo->cliente['cliret_iss_perc']>0 && $totais['valor_total']>0)
            $vlr_iss = round($this->vo->cliente['cliret_iss_perc']/100 * floatval($totais['valor_total']),2);
        
        //O PIS é sobre o valor total da nota fiscal acima de  R$ 5.000,00.
        if($this->vo->cliente['cliret_pis_perc']>0 && $totais['valor_total']>5000)
            $nflvlr_pis = round($this->vo->cliente['cliret_pis_perc']/100 * floatval($totais['valor_total']),2);
        
        //O COFINS é sobre o valor total da nota fiscal acima de  R$ 5.000,00.
        if($this->vo->cliente['cliret_cofins_perc']>0 && $totais['valor_total']>5000)
            $nflvlr_cofins = round($this->vo->cliente['cliret_cofins_perc']/100 * floatval($totais['valor_total']),2);
        
        //O CSLL é sobre o valor total da nota fiscal acima de  R$ 5.000,00.
        if($this->vo->cliente['cliret_csll_perc']>0 && $totais['valor_total']>5000)
            $nflvlr_csll = round($this->vo->cliente['cliret_csll_perc']/100 * floatval($totais['valor_total']),2);
        
        // Regra removida ASM 276807 GMUD 7393
        //O IR (1%) é sobre o valor total da nota fiscal. Para esta retenção, a NF tem que possuir um valor acima de R$ 1.000,00.
        //if($this->vo->cliente ['cliret_piscofins'] == 't' && $totais['valor_total'] > 1000)
            //$nflvlr_ir = (0.01)*floatval($totais['valor_total']);
        

        /** Lançamento de credito futuro **/
        //se escolher conceder creditos
        if (isset($this->params['conceder_creditos']) &&  $this->params['conceder_creditos'] == '1') {

            $creditosFuturos = $this->vo->getCreditos();

            $arrayCreditosFuturo = array();

            foreach ($creditosFuturos as $key => $credito) {


                $creditoFuturoObj = new CreditoFuturoVO();
                $creditoFuturoParcelaObj = new CreditoFuturoParcelaVO();
                $creditoFuturoMotivoObj = new CreditoFuturoMotivoCreditoVO();

                $creditoFuturoObj->id = $credito['credito_id'];
                $creditoFuturoObj->contratoIndicado = $credito['connumero'];
                $creditoFuturoObj->aplicarDescontoSobre = $credito['cfoaplicar_desconto'];
                $creditoFuturoObj->valor = $credito['valor'];
                $creditoFuturoObj->obrigacaoFinanceiraDesconto = $credito['obrigacao_id'];
                $creditoFuturoObj->tipoDesconto = $credito['cfotipo_desconto'];
                $creditoFuturoObj->origem = 3;

                $creditoFuturoMotivoObj->id = $credito['motivo_credito_id'];
                $creditoFuturoMotivoObj->tipo = $credito['tipo_motivo_credito'];
                $creditoFuturoMotivoObj->descricao = $credito['cfmcdescricao'];

                $creditoFuturoParcelaObj->id = $credito['parcela_id'];
                $creditoFuturoParcelaObj->numero = $credito['parcela_numero'];

                $creditoFuturoObj->Parcelas = $creditoFuturoParcelaObj;
                $creditoFuturoObj->MotivoCredito = $creditoFuturoMotivoObj;

                $arrayCreditosFuturo[] = $creditoFuturoObj;
                # code...
            }

            $retorno = $this->bo->processarDesconto($arrayCreditosFuturo, $totais, array('nfloid'=>0, 'nflno_numero'=>0, 'nflserie'=>''), false);


            foreach ($retorno['creditos'] as $key => $itemDesconto) {


                if ($itemDesconto->aplicarDescontoSobre == '1') {
                    $itemDesconto->nfitipo = 'M';
                } else {
                    $itemDesconto->nfitipo = 'L';
                }   

                $itemDesconto->connumero = '';

                $obrigacaoFincanceiraBusca = $this->getObrigacaoFinanceira($itemDesconto->obrigacaoFinanceiraDesconto);

                $itemDesconto->tpcdescricao = '';
                $itemDesconto->obrobrigacao = $obrigacaoFincanceiraBusca['obrobrigacao'];

                $itemDesconto = (array) $itemDesconto;

                array_push($itens, $itemDesconto);
            }

            $totais['valor_total'] = $retorno['total'];

        } 

        /** FIM Lançamento de credito futuro **/

        $nParcelas = $this->params['parc'];
        $total = floatval($totais['valor_total'])-floatval($totais['valor_descontos']);
        $parcela          = floatval($totais['valor_total']);
        $totalParcelas    = floatval($totais['valor_total']);
        $desconto_parcela = floatval($totais['valor_descontos']);
        $totalDesconto    = floatval($totais['valor_descontos']);
        $impostosPisCofinsCsll = (float) floatval($nflvlr_pis) + floatval($nflvlr_cofins) + floatval($nflvlr_csll);

        $vlr_issP=0;
        $nflvlr_pisP=0;
        $nflvlr_cofinsP=0;
        if($nParcelas>1){ 
            if($parcela > 0)
                $parcela = round($parcela/$nParcelas ,2);

            if($desconto_parcela > 0)
                $desconto_parcela = round($desconto_parcela/$nParcelas ,2);

            //impostos por parcelas de titulo.
            if($vlr_iss > 0)
                $vlr_issP = round($vlr_iss/$nParcelas ,2);

            if($nflvlr_pis > 0)
                $nflvlr_pisP = round($nflvlr_pis/$nParcelas ,2);

            if($nflvlr_cofins > 0)
                $nflvlr_cofinsP = round($nflvlr_cofins/$nParcelas ,2);          
        }


                
        $time = time();
        //Valor do Titulo por parcela
        $valorParcelaTitulo = 0;
        if ($totalParcelas > 0){
            $valorParcelaTitulo = round(  ( ( $totalParcelas - $totalDesconto) / $nParcelas ), 2);
        }
        $valorIssTitulo      = 0;
        $valorIrTitulo       = 0;
        $valorDescontoTitulo = 0;
        $valorPisCofins      = ($impostosPisCofinsCsll > 0) ? round( ($impostosPisCofinsCsll / $nParcelas), 2) :  0;
        for ($i=1; $i<=$nParcelas; $i++){
            
            //Valor do ISS por parcela
            $valorIssTitulo     = $vlr_issP;
   
            
            //Valor do IR por parcela
            $valorIrTitulo = $nflvlr_ir;
            if ($nflvlr_ir > 0 && $nParcelas > 1){
                $valorIrTitulo = round($nflvlr_ir/$nParcelas ,2);           
            }
            
            /**
             * Verifica se há dízima periódica do valor da parcela, ISS, IR e desconto. 
             */
            if ( $i == $nParcelas && $nParcelas > 1){
                //Verifica o valor da parcela
                $valorParcelaTitulo = $this->calcularValorParcela($totalParcelas, $nParcelas, $totalDesconto);

                //Verifica no valor do ISS
                $valorIssTitulo = $this->calcularValorParcela($vlr_iss, $nParcelas);
                
                //Verifica no valor do IR
                $valorIrTitulo = $this->calcularValorParcela($nflvlr_ir, $nParcelas);
                
                //Verifica no valor total dos impostos
                $valorPisCofins = $this->calcularValorParcela($impostosPisCofinsCsll, $nParcelas);
            }
            
            // Insere um novo título com os dados       
            // @todo - verificar calculo do IR  
            $dataVencimento = date('d/m/Y', $time);    
            $dataReferenciaTitulo = date('01/m/Y', $time);
            
            //7.realiza inserção de novo titulo

            $time = strtotime(date('Y-m-d', $time)." +1 month");
        }

       
        //8.Update na tabela e nota fiscal.
        

        //9.cria testo para historico do cliente
                
        //10.insere registro no historico do cliente.

        $nota['nfltransporte'] = 'RODOVIARIO';
        $nota['nflnatureza'] = $this->vo->getNatureza();
        $nota['nfldt_emissao'] = $this->params['dt_emi'];
        $nota['nfldt_vencimento'] = $this->params['dt_venc'];
        $nota['nflvl_total'] = $totais['valor_total'];
        $nota['nflvl_desconto'] = $totais['valor_descontos'];
        $nota['nflvlr_pis'] = $nflvlr_pis;
        $nota['nflvlr_cofins'] = $nflvlr_cofins;
        $nota['nflvlr_csll'] = $nflvlr_csll;
        $nota['nflvlr_ir'] = $nflvlr_ir;
        $nota['vlr_iss'] = $vlr_iss;
		$nota['impostosPisCofinsCsll'] = $impostosPisCofinsCsll;
		$nota['infCompNfe'] = $this->params['infCompNfe'];
        $nota['itens_nota'] = $itens;

        return $nota;       
        
     }
     
	public function buscarCreditosConceder($vo, $clienteId) {
		$this->vo = $vo;
		
		// 4 – "Débito Automático" 5 – "Cartão de Crédito"
		
		$sql = "SELECT
                    cfooid AS credito_id,

                    CASE
                        WHEN cfmctipo = 4 THEN 'D'
                        WHEN cfmctipo = 5 THEN 'C'
                        ELSE 'N'
                    END AS tipo_forma_cobranca,

                    cfoconnum_indicado AS connumero,
                    tpcdescricao,
                    cfmctipo AS tipo_motivo_credito,
                    cfmcdescricao,
                    cfocfmcoid AS motivo_credito_id,
                    cfoobroid_desconto AS obrigacao_id,
                    obrobrigacao,
                    cfoaplicar_desconto AS cfoaplicar_desconto,
                    -- 1 - percentual / 2 - valor
                    cfotipo_desconto,
                    CASE WHEN cfoaplicar_desconto = 1 THEN 'Monitoramento'
                             WHEN cfoaplicar_desconto = 2 THEN 'Locação'
                        END AS aplicar_desconto_descricao,
                    (SELECT
                        A.cfpvalor - (SELECT COALESCE(SUM(cfmvalor),SUM(cfmvalor), 0) FROM credito_futuro_movimento cfm WHERE cfm.cfmcfpoid = A.cfpoid AND cfm.cfmdt_exclusao IS NULL) AS valor_deferido
	
                    FROM
                        credito_futuro_parcela AS A
                    WHERE
                        A.cfpcfooid = cfooid
                    AND
                        A.cfpativo = true
                    ORDER BY
                        A.cfpnumero ASC
                    LIMIT 1) AS valor,
	
                    (SELECT
                        cfpoid
                    FROM
                        credito_futuro_parcela AS A
                    WHERE
                        A.cfpcfooid = cfooid
                    AND
                        A.cfpativo = true
                    ORDER BY
                        A.cfpnumero ASC
                    LIMIT 1) AS parcela_id,
	
                    (SELECT
                        cfpnumero
                    FROM
                        credito_futuro_parcela AS A
                    WHERE
                        A.cfpcfooid = cfooid
                    AND
                        A.cfpativo = true
                    ORDER BY
                        A.cfpnumero ASC
                    LIMIT 1) AS parcela_numero
	
                FROM
                    credito_futuro
                INNER JOIN
                    credito_futuro_motivo_credito ON (cfmcoid = cfocfmcoid)
                INNER JOIN
                    credito_futuro_ordem_aplicacao ON (cfoatipo_motivo = cfmctipo)
                INNER JOIN
                    obrigacao_financeira ON (obroid = cfoobroid_desconto)
                LEFT JOIN
                    contrato ON (connumero = cfoconnum_indicado)
                LEFT JOIN
                    tipo_contrato ON (tpcoid = conno_tipo)
                WHERE
                    --status igual a aprovado
                    cfostatus = 1
                AND
                    --saldo maio que 0 ou com parcelas ativas
                    (
                        credito_futuro.cfosaldo > 0
                    OR
                        (SELECT COUNT(cfpcfooid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid AND cfpativo = true) > 0
                    )
                AND
                    --que não esteja excluido
                    cfodt_exclusao IS NULL
                AND
                    --que não esteja encerrado
                    cfodt_encerramento IS NULL
                AND
                    --do cliente em questão
                    cfoclioid = " . intval($clienteId) . "                
                AND
                    (
                        (cfmctipo = 1)
                        OR (
                            cfmctipo <> 1
                            AND (EXISTS (
                                        SELECT
                                            cfmctipo
                                        FROM
                                            credito_futuro cf,
                                            credito_futuro_motivo_credito cfm
                                        WHERE
                                            cf.cfocfmcoid = cfm.cfmcoid
                                        AND
                                            cf.cfoclioid = " . intval($clienteId) . "
                                        AND
                                            cfostatus = 1
                                        AND
                                            --saldo maio que 0 ou com parcelas ativas
                                            (
                                                cf.cfosaldo > 0
                                            OR
                                                (SELECT COUNT(cfpcfooid) FROM credito_futuro_parcela WHERE cfpcfooid = cfooid AND cfpativo = true) > 0
                                            )
                                        AND
                                            --que não esteja excluido
                                            cfodt_exclusao IS NULL
                                        AND
                                            --que não esteja encerrado
                                            cfodt_encerramento IS NULL
                                        AND 
					     					cfmctipo != 1	
                                    )
                              )
                        )
                    )
	
                ORDER BY
                    --ordenando pela ordem de aplicação de tipo de desconto
                    cfoaordem_aplicacao ASC;
                ";
		
		if (! $rs = pg_query ( $this->conn, $sql )) {
			throw new Exception ( 'Falha ao buscar créditos Futuros do cliente.' );
		}
		
		$retorno = array ();
		
		$this->vo->deleteCredito ();
		
		$tipoFormaCobrancaCliente = $this->buscarTipoFormaCobrancaTitulo ( $clienteId );
		
		$itensNota = $this->vo->getItens ();
		
		$valorTotalNota = array ();
		$valorTotalNota ['M'] = 0;
		$valorTotalNota ['L'] = 0;
		if (isset ( $itensNota ) && count ( $itensNota )) {
			foreach ( $itensNota as $key => $nota ) {
				$valorTotalNota [$nota ['nfitipo']] += floatval ( $nota ['nfivl_item'] ) - floatval ( $nota ['nfidesconto'] );
			}
		}
		
		while ( $row = pg_fetch_assoc ( $rs ) ) {
			
			$row ['porcentagem_desconto'] = '';
			
			// se for porcentagem e monitoração
			if ($row ['cfotipo_desconto'] == '1' && $row ['cfoaplicar_desconto'] == '1') {
				$row ['porcentagem_desconto'] = $row ['valor'] / 100;
				$row ['valor'] = ($row ['porcentagem_desconto'] * $valorTotalNota ['M']);
				
				if ($row ['porcentagem_desconto'] != 1) {
					$row ['valor'] = floor ( $row ['valor'] * 100 ) / 100;
				}
			}
			
			// se for porcentagem e locação
			if ($row ['cfotipo_desconto'] == '1' && $row ['cfoaplicar_desconto'] == '2') {
				$row ['porcentagem_desconto'] = $row ['valor'] / 100;
				$row ['valor'] = ($row ['porcentagem_desconto'] * $valorTotalNota ['L']);
				
				if ($row ['porcentagem_desconto'] != 1) {
					$row ['valor'] = floor ( $row ['valor'] * 100 ) / 100;
				}
			}
			
			if ($row ['cfotipo_desconto'] != '1') {
				$row ['valor'] = floor ( $row ['valor'] * 100 ) / 100;
			}
			
			$row ['valor_formatado'] = number_format ( $row ['valor'], 2, ',', '.' );
			
			if ($row ['tipo_forma_cobranca'] == 'N' || $row ['tipo_forma_cobranca'] == $tipoFormaCobrancaCliente) {
				$this->vo->addCredito ( $row );
			}
		}
	}
	
	/**
	 * Retorna o tipo da forma de pagamento do ultimo titulo pago pelo cliente.
	 *
	 * @param int $clienteId        	
	 *
	 * @return string "D" - Débito | "C" - Crédito | "N" - NULO
	 *        
	 */
	public function buscarTipoFormaCobrancaTitulo($clienteId) {
		$sql = "SELECT 
                    CASE
                        WHEN forcdebito_conta = 't' THEN 'D'
                        WHEN forccobranca_cartao_credito = 't' THEN 'C'
                        ELSE 'N'
                    END AS tipo_forma_cobranca
                FROM 
                    titulo 
                INNER JOIN
                    forma_cobranca ON (titformacobranca = forcoid)
                INNER JOIN
                    nota_fiscal ON (nfloid = titnfloid)
                WHERE
                    titvl_pagamento > 0
                AND
                    titdt_pagamento IS NOT NULL
                AND
                    nflclioid = " . intval ( $clienteId ) . "
                ORDER BY
                    titdt_pagamento DESC
                LIMIT 1";
		
		if (! $rs = pg_query ( $this->conn, $sql )) {
			throw new Exception ( 'Falha ao buscar tipo da forma de pagamento do ultimo titulo pago pelo cliente.' );
		}
		
		if (pg_num_rows ( $rs ) > 0) {
			return pg_fetch_result ( $rs, 0, 'tipo_forma_cobranca' );
		} else {
			return "N";
		}
	}
}
