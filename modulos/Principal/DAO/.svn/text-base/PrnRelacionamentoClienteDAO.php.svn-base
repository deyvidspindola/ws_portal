<?php

require_once _MODULEDIR_.'Principal/Action/PrnParametrosSiggo.class.php';

class PrnRelacionamentoClienteDAO
{
	private $conn;
	
	// ID do Tipo de Parcelamento
	private $idtipo_parcela = 13;
	private $idtaxa_instalação = 23;
	
	// Texto padrão 
	private $texto_titulo = "TAXA DE INSTALACAO";
	
	// Codigo tabela tipo_boleto -> indica "TAXA DE INSTALACAO SIGGO"
	private $codigo_tipo_boleto = 6;
	
	public function PrnRelacionamentoClienteDAO($conn){
		$this->conn = $conn;
	}
	
	/* Titulo_retencao da taxa de instalacao pagos na data atual e data de ontem */
	public function titulosEfetuados(){
		/* Id da taxa de instalação de acordo com a tabela */
		$parametrosSiggo = new PrnParametrosSiggo();
		
		$paramsPesquisa = array(
				'id_tipo_proposta'		=>	0,
				'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
		);
		
		$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
		
		$idTaxaInstalacao = $retornoValor['valor'];
		
		$data_hoje  = date("Y-m-d"); 
		$data_ontem = date("Y-m-d", strtotime("$data_hoje -1 days"));
		
		$sqlTitulos = "
				SELECT 
					DISTINCT(titoid), titriconoid, prpoid
				FROM 
					titulo_retencao 
				INNER JOIN 
					titulo_retencao_item ON titrititoid = titoid
				INNER JOIN
					proposta ON prptermo = titriconoid
				WHERE 
					titriobroid = {$idTaxaInstalacao}
				AND titdt_cancelamento IS NULL
				AND titvl_pagamento >= titvl_titulo_retencao
				AND (titdt_pagamento = '{$data_ontem}' OR titdt_pagamento = '{$data_hoje}')
				AND titformacobranca = 1
				;
		";
		
		$res = pg_query($this->conn, $sqlTitulos);
		
		if (pg_num_rows($res)>0) {
				
			return pg_fetch_all($res);
		}
		return false;
	}

	public function tituloPagamentoEfetuado($proposta_cod = null, $contrato_num = null) {
		
		$dadosProposta = $this->dadosProposta($proposta_cod);

		/* Id da taxa de instalação de acordo com a tabela */
		$parametrosSiggo = new PrnParametrosSiggo();
		
		$paramsPesquisa = array(
				'id_tipo_proposta'		=>	$dadosProposta->tppoid_supertipo,
				'id_subtipo_proposta'	=>	$dadosProposta->prptppoid,
				'id_tipo_contrato'		=>	$dadosProposta->conno_tipo,
				'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
		);
		
		$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
		
		$idTaxaInstalacao = $retornoValor['valor'];
		
		// Verifica se esta pago -> Quando for realizado o pagamento é gerado nota fiscal para o mesmo
		$sqlTituloEfetuado = "
			SELECT
				t.titoid AS titulo_id_pago
			FROM
				nota_fiscal_item
			INNER JOIN
				nota_fiscal nf ON nf.nflno_numero = nfino_numero
			INNER JOIN
				titulo t ON t.titnfloid = nf.nfloid
			WHERE
				nfiobroid = $idTaxaInstalacao
			AND nficonoid = $contrato_num
			AND titdt_cancelamento IS NULL
			AND titvl_pagamento >= titvl_titulo;
			";
		
		$res = pg_query($this->conn, $sqlTituloEfetuado);
		
		if (pg_num_rows($res)>0) {
			
			$titulo = pg_fetch_all($res);
			
			$tituloEfetuado = $titulo[0]['titulo_id_pago'];

			$retorno = array(
					"erro"				=> 0,
					"tituloEfetuado"	=> $tituloEfetuado
					);
			
		} else {
			
			$retorno = array(
					"erro"				=> 0,
					"tituloEfetuado"	=> ""
			);
		}
			
		return $retorno;	
	}
	
	/* Titulo_retencao que irão vencer daqui a 2 dias */
	public function titulosAVencer(){
		/* Id da taxa de instalação de acordo com a tabela */
		$parametrosSiggo = new PrnParametrosSiggo();
	
		$paramsPesquisa = array(
				'id_tipo_proposta'		=>	0,
				'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
		);
	
		$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
	
		$idTaxaInstalacao = $retornoValor['valor'];
	
		$data_hoje  = date("Y-m-d");
		$data_pesq = date("Y-m-d", strtotime("$data_hoje +2 days"));
	
		$sqlTitulos = "
			SELECT 
				DISTINCT(titoid), titriconoid, prpoid  
			FROM 
				titulo_retencao
			INNER JOIN 
				titulo_retencao_item ON titrititoid = titoid 
			INNER JOIN 
				proposta ON prptermo = titriconoid 
			
			WHERE 
				titriobroid = {$idTaxaInstalacao} 
			AND titdt_cancelamento IS NULL
			AND titdt_pagamento IS NULL
			AND titformacobranca = 1
			AND titdt_vencimento = '{$data_pesq}'
		";
		
		$res = pg_query($this->conn, $sqlTitulos);
	
		if (pg_num_rows($res)>0) {
	
			return pg_fetch_all($res);
		}
		return false;
	}
	
	public function getTituloFuncionalidade($titulo){
		 
		$sql = "
		    	SELECT
		    		seetoid AS titulo_id, seetseefoid AS funcionalidade_id
		    	FROM
		    		servico_envio_email_titulo
		    	WHERE
		    		seetdescricao = '".$titulo."';
    			";
	
		$rs = pg_query($this->conn, $sql);
	
		return pg_fetch_object($rs);;
		 
	}
	
	public function dadosCliente($titulo_cod) {
		
		$str = "SELECT
					cliemail, clinome
				FROM titulo
				INNER JOIN clientes
				ON titclioid=clioid
				WHERE titoid = ".$titulo_cod;
		
		$sql = pg_query($this->conn, $str);
		
		$dadosCliente = array();
		
		while( $resul = pg_fetch_array($sql) ){
		
			$dadosCliente['clinome'] = $resul['clinome'];
			$dadosCliente['cliemail'] = $resul['cliemail'];		
		}
		
		return $dadosCliente;
	}
	
	
	public function dadosProposta($proposta_cod) {
		// identificar layout $prpoid
		$sql = "
			SELECT
				prptppoid, tppoid_supertipo, conno_tipo, clinome, cliemail
			FROM
				proposta
			INNER JOIN contrato ON prptermo = connumero
			LEFT JOIN tipo_proposta ON tppoid = prptppoid
			INNER JOIN clientes ON clioid = conclioid
			WHERE
				prpoid = $proposta_cod";
	
		
		$ret = pg_query($this->conn, $sql);
	
		if(pg_num_rows($ret) > 0) {
			$dadosContratoLayout = pg_fetch_object($ret);
		}
		 
		return $dadosContratoLayout;
	}
	
	public function getLayoutEmail($tipo) {
		$tipo = pg_escape_string($tipo);
		 
		$sql = "SELECT * FROM servico_envio_email WHERE seeoid = " . $tipo;
		 
		$ret = pg_query($this->conn, $sql);		 
		return pg_fetch_object($ret);
	}
	
	
	public function verificarHistorico($proposta_cod, $msgHistorico) {
		
		$sqlSelect = "
				SELECT 
					* 
				FROM 
					proposta_historico  
				WHERE 
					prphprpoid = {$proposta_cod} 
				AND prphobs = '{$msgHistorico}'
			";
			
		$res = pg_query($this->conn, $sqlSelect);
		
		if (pg_num_rows($res)>0) {
			return true;
		}
		
		return false;	
	}
	
	public function salvarHistorico($listaContrato, $msgHistorico) {
		
		$usuario = 2750; // AUTOMATICO -> PROCESSO CRON
		if ($_SESSION['usuario']['oid']) {
			$usuario = $_SESSION['usuario']['oid'];
		}
		
		foreach ($listaContrato AS $key => $contrato_cod){
			$sqlInsert = "
				INSERT INTO 
					proposta_historico (prphprpoid, prphdata, prphusuoid, prphobs)
				VALUES (
					(SELECT prpoid FROM proposta WHERE prptermo = {$contrato_cod}),
					now(),
					".$usuario.",
					'".$msgHistorico."'
				)";
			
					
			if (!pg_query($this->conn, $sqlInsert)){
				return false;
			}
		}
		
		return true;	
	}
	
	public function contratoTitulo($titulo_cod){
		
		$sqlSelect = "
				SELECT
					nficonoid 
				FROM 
					nota_fiscal_item
				INNER JOIN 
					nota_fiscal ON nfloid = nfinfloid
				INNER JOIN
					titulo ON titnfloid = nfloid
				WHERE 
					titoid = {$titulo_cod}
		";

		$res = pg_query($this->conn, $sqlSelect);
		
		if (pg_num_rows($res)>0) {
			
			return pg_fetch_all_columns($res);
		}
		return false;
	}
	
	public function contratoTituloBoleto($titulo_cod){
	
		$sqlSelect = "
				SELECT 
					titriconoid 
				FROM 
					titulo_retencao_item 
				WHERE 
					titrititoid = {$titulo_cod} 
		";
	
		$res = pg_query($this->conn, $sqlSelect);
	
		if (pg_num_rows($res)>0) {
			return pg_fetch_all_columns($res);
		}
		return false;
	}
	
}