<?php

include_once _SITEDIR_.'boleto_funcoes.php';

require_once _MODULEDIR_.'Principal/Action/PrnParametrosSiggo.class.php';
require_once _MODULEDIR_.'Principal/Action/PrnBoletoSeco.class.php';


class PrnPreCadastroDAO
{
	private $conn;
	
	// ID do Tipo de Parcelamento
	private $idtipo_parcela = 13;
	private $idtaxa_instalação = 23;
	
	// Texto padrão 
	private $texto_titulo = "TAXA DE INSTALACAO";
	
	// Codigo tabela tipo_boleto -> indica "TAXA DE INSTALACAO SIGGO"
	private $codigo_tipo_boleto = 6;
	
	public function PrnPreCadastroDAO($conn){
		$this->conn = $conn;
	}

	public function tipoContratoParametrizacao($tpcoid) {
	
		$sql = "
				SELECT
					*
				FROM
					tipo_contrato_parametrizacao
				WHERE
					tcptpcoid = $tpcoid
		";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao pesquisar parametros do Tipo de Contrato.");
		}
	
		if (pg_num_rows($res)>0) {
			$retorno = pg_fetch_all($res);
		} else {
			$retorno = array();
		}
	
		return $retorno;
	
	}


	public function tipoProposta($filtro) {
	
		$sql = "
				SELECT
					tppoid, tppdescricao, tppcodigo
				FROM
					tipo_proposta
				WHERE
					tppoid_supertipo IS NULL
				".$filtro."
				ORDER BY
					tppdescricao
				";
		
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao pesquisar parametros do Tipo de Proposta.");
		}
	
		if (pg_num_rows($res)>0) {
			$retorno = pg_fetch_all($res);
		} else {
			$retorno = array();
		}
	
		return $retorno;
	
	}


	public function subProposta($proposta) {
	
		$sql = "
	            SELECT 
	            	tppoid, tppdescricao, tppcodigo  
				FROM 
					tipo_proposta
				WHERE 
					tppoid_supertipo IS NOT NULL   
				AND tppoid_supertipo = $proposta   
	            ORDER BY 
	                tppdescricao";
			
		if (!$res = pg_query($this->conn, $sql)) {
			throw new Exception("Erro ao pesquisar parametros do Tipo de Sub Proposta.");
		}
	
		if (pg_num_rows($res)>0) {
			$retorno = pg_fetch_all($res);
		} else {
			$retorno = array();
		}
	
		return $retorno;
	
	}
	
	/**
	 * STI 81493
	 */
	public function verificaTaxaIntalacao(){
		try{
			$id_subproposta = $_POST['id_subproposta'];
			
			$sql = "
				SELECT 
					*
				FROM 
					tipo_proposta_acao tpa
				INNER JOIN 
					acao_contrato ac ON tpa.tpaapcoid = ac.apcoid
				WHERE 
					tpatppoid = $id_subproposta
					AND apcativo = 't'
				";
			
			if (!$res = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao buscar configuração de sub proposta.");
			}
			
			if (pg_num_rows($res)>0) {
				$retorno = pg_fetch_all($res);
			} else {
				$retorno = array();
			}
			
			return $retorno;
				
		}catch (Exception $e){
			$retorno = array(
								'erro' => true,
								'msg'  => $e->getMessage()
					);
			return $retorno;
		}
	}
	
	public function getFormaPagamentoTaxaInstalacao(){
		try{	
			
			/* Id da taxa de instalação de acordo com a tabela */
			$parametrosSiggo = new PrnParametrosSiggo();
				
			$paramsPesquisa = array(
					'id_tipo_proposta'		=>	$_POST['id_proposta'],
					'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
					'id_tipo_contrato'		=>	$_POST['prptpcoid'],
					'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
					'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
			);
				
			$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
				
			$idTaxaInstalacao = $retornoValor['valor'];
				
			$forma_pagamento = $_POST['forma_pagamento'];
			
			$sql = "
				SELECT 
					offc.offcforcoid, fc.forcnome
				FROM 
					obrigacao_financeira_forma_cobranca offc
				INNER JOIN 
					obrigacao_financeira o ON o.obroid = offc.offcobroid
				INNER JOIN 
					forma_cobranca fc ON fc.forcoid = offc.offcforcoid 				
				WHERE obroid = $idTaxaInstalacao";
			
			// Tem forma selecionada no monitoramento
			if (!empty($forma_pagamento)){
				// Verificar se é do tipo cartão de crédito
				$credito  = $this->formaPagamentoIsCredito();
												
				if ($credito['erro'] == 1){
					throw new Exception($credito['msg']);
				}
				// Se é do tipo cartão de crédito, as opções da taxa de instalação é boleto e o cartão de crédito escolhido no monitoramento
				if ($credito['credito'] == 't'){
					$sql .= " AND offcforcoid IN (1, $forma_pagamento)";
				}else{
					// Não foi escolhido cartão de crédito no monitoramento, então exibe somente por boleto.
					$sql .= " AND offcforcoid IN (1)";
				}
			}	
						
			if (!$res = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao carregar formas de pagamento da taxa de instalação.");
			}
			
			if (pg_num_rows($res)>0) {
				$retorno = pg_fetch_all($res);
			} else {
				throw new Exception("Não encontrado nenhuma forma de pagamento para a taxa de instalação.");
			}
				
			return $retorno;
			
		}catch (Exception $e){
			$retorno = array(
					'erro' => true,
					'msg'  => $e->getMessage()
			);
			return $retorno;
		}
	}
	
	public function getValorTaxaInstalacao(){
		try{				
			
			/* Id da taxa de instalação de acordo com a tabela */
			$parametrosSiggo = new PrnParametrosSiggo();
			
			$paramsPesquisa = array(
					'id_tipo_proposta'		=>	$_POST['id_proposta'],
					'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
					'id_tipo_contrato'		=>	$_POST['prptpcoid'],
					'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
					'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
			);
			
			$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
			
			$idTaxaInstalacao = $retornoValor['valor'];
			
			$sql = "
				SELECT 
					tpivalor, tpivalor_minimo
				FROM 
					tabela_preco_item
				INNER JOIN 
					tabela_preco ON tproid = tpitproid 
				WHERE 
					tpicpvoid = $this->idtipo_parcela
					AND tpiobroid = $idTaxaInstalacao
					AND tpiexclusao IS NULL
					AND tprstatus = 'A'
			";
			
			if (!$res = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao buscar informações.");
			}
		
			if (pg_num_rows($res)>0) {
				$valor = pg_fetch_all($res);

				// Transforma em formato moeda.
				$retorno['tpivalor'] 		= ($valor[0]['tpivalor'] != "") ? number_format($valor[0]['tpivalor'], 2, ",", ".") : "";
				$retorno['tpivalor_minimo'] = ($valor[0]['tpivalor_minimo'] != "") ? number_format($valor[0]['tpivalor_minimo'], 2, ",", ".") : "";
				
			} else {
				throw new Exception("Erro ao buscar informações.");
			}
		
			return $retorno;
		}catch (Exception $e){
			$retorno = array(
						'erro' => true,
						'msg'  => $e->getMessage()
			);
			return $retorno;
		}
	}
	
	/**
	 * Função: Buscar o valor da taxa de instalação do contrato
	 */
	public function getValorTaxaInstalacaoContrato(){
		try{
			
			/* Id da taxa de instalação de acordo com a tabela */
			$parametrosSiggo = new PrnParametrosSiggo();
				
			$paramsPesquisa = array(
					'id_tipo_proposta'		=>	$_POST['id_proposta'],
					'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
					'id_tipo_contrato'		=>	$_POST['prptpcoid'],
					'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
					'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
			);
				
			$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
				
			$idTaxaInstalacao = $retornoValor['valor'];
			
			$contrato	= $_POST['contrato_numero'];
			$prpoid		= $_POST['prpoid'];
						
			$sqlContrato = "
					SELECT
						prptermo
					FROM
						proposta
					WHERE
						prpoid=$prpoid
			";
			
			if (!$resContrato = pg_query($this->conn, $sqlContrato)) {
				throw new Exception ("Erro ao buscar informações.");
			}
		
			$contrato = pg_fetch_result($resContrato,0,'prptermo');	
						
			// Busca se a taxa de instalação é por boleto
			$sqlValor = "
				SELECT 
		      		titrivl_item AS valor_item, titformacobranca AS forma_pagamento, titoid AS titulo_id, titno_parcela AS parcelas
		 		FROM 
		 			titulo_retencao_item
		 		INNER JOIN 
		 			titulo_retencao tr ON tr.titoid = titrititoid
		  		WHERE 
			  		titriobroid = $idTaxaInstalacao 
			  		AND titriconoid = $contrato
			  		AND titdt_cancelamento IS NULL
			";
							
			if (!$resValor = pg_query($this->conn, $sqlValor)) {
				throw new Exception("Erro ao buscar informações.");
			}
			
			// Quantidade de resultado da pesquisa
			$itemTaxaInstalacao = pg_num_rows($resValor);	

			if ($itemTaxaInstalacao > 0){
				// Encontrou titulo_retencao_item ( Forma de pagamento da taxa por boleto)
				
				// Verifica se esta pago -> Quando for realizado o pagamento é gerado nota fiscal para o mesmo
				$sqlTituloBoletoPago = "
					SELECT
						t.titnfloid AS titulo_id_pago
					FROM
						nota_fiscal_item
					INNER JOIN
						nota_fiscal nf ON nf.nflno_numero = nfino_numero
					INNER JOIN
						titulo t ON t.titnfloid = nf.nfloid
					WHERE
						nfiobroid = $idTaxaInstalacao
						AND nficonoid = $contrato   
						AND titdt_cancelamento IS NULL
						AND titvl_pagamento >= titvl_titulo;
					";
				
				if (!$resTituloBoletoPago = pg_query($this->conn, $sqlTituloBoletoPago)) {
					throw new Exception("Erro ao buscar informações.");
				}
					
				// Se encontrou resultado, o titulo por boleto esta pago
				$boletoPago = pg_num_rows($resTituloBoletoPago);
				
				if ($boletoPago > 0){
					$retorno['tituloPago'] = 1;
				}
			}			
			
			// Não encontrou taxa de instalação por pagamento por Boleto.
			if ($itemTaxaInstalacao == 0){
				
				// Busca se a taxa de instalação é por Cartão de Crédito
				$sqlValor = "
											
					SELECT
						nfivl_item AS valor_item, 
						t.titformacobranca AS forma_pagamento, 
						t.titnfloid AS titulo_id,
						pp.ppagadesao_parcela AS parcelas
					FROM
					nota_fiscal_item
					INNER JOIN nota_fiscal nf ON nf.nflno_numero = nfino_numero
					INNER JOIN titulo t ON t.titnfloid = nf.nfloid
					INNER JOIN proposta p ON p.prptermo = nficonoid
					INNER JOIN proposta_pagamento pp ON pp.ppagprpoid = p.prpoid
					WHERE nfiobroid = $idTaxaInstalacao
					AND nficonoid = $contrato
					AND titvl_pagamento >= titvl_titulo
						
				";
				/*INNER JOIN 
				nota_fiscal nf ON nf.nfloid = nfinfloid*/
				
				
				if (!$resValor = pg_query($this->conn, $sqlValor)) {
					throw new Exception("Erro ao buscar informações.");
				}
					
				// Se encontrou resultado
				$itemTaxaInstalacao = pg_num_rows($resValor);
				
				// Titulo esta pago
				if ($itemTaxaInstalacao > 0){
					$retorno['tituloPago'] = 1;
				}
			}
			
			$sqlPropostaPagamento = "
					
					SELECT 
						ppagadesao_parcela as parcelas, ppagforcoid_adesao as forma_pagamento, ppagadesao as valor_item
					FROM proposta
					JOIN 
						proposta_pagamento ON ppagprpoid = prpoid
					WHERE 
						prpoid = $prpoid;
					";
			

			if (!$resProposta = pg_query($this->conn, $sqlPropostaPagamento)) {
				throw new Exception("Erro ao buscar informações.");
			}
				
			// Se encontrou resultado
			$itemTaxaInstalacaoProposta = pg_num_rows($resProposta);
			
			if ($itemTaxaInstalacaoProposta > 0){
				$itemTaxaInstalacao = $itemTaxaInstalacaoProposta;
				$resValor = pg_query($this->conn, $sqlPropostaPagamento);
			}
			
			
			// Encontrou a taxa de instalação do contrato
			if ($itemTaxaInstalacao > 0) {
				$valor = pg_fetch_all($resValor);
				
				// Procura na tabela de titulo, se está pago
				$titulo_id = $valor[0]['titulo_id'];				
				
				// Transforma em formato moeda.
				$retorno['valorTaxaContrato'] 	= ($valor[0]['valor_item'] != "") ? number_format($valor[0]['valor_item'], 2, ",", ".") : "";
				
				// Forma de pagamento que foi cadastrada
				$retorno['formaPagamento'] 		= $valor[0]['forma_pagamento'];
				
				// Parcela 
				$retorno['parcela']				= $valor[0]['parcelas'];
				
				// Numero titulo
				$retorno['titulo'] = $valor[0]['titulo_id'];
	
			} else {
				// Nenhum título encontrado
				$retorno['titulo'] = 0;				
			}
			
	
			return $retorno;
		}catch (Exception $e){
			$retorno = array(
							'erro' => true,
							'msg'  => $e->getMessage()
					);
			return $retorno;
		}
	}
	
	public function getParcelasFormaPagamento(){
		/* Id da taxa de instalação de acordo com a tabela */
		$parametrosSiggo = new PrnParametrosSiggo();
		
		$paramsPesquisa = array(
				'id_tipo_proposta'		=>	$_POST['id_proposta'],
				'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
				'id_tipo_contrato'		=>	$_POST['prptpcoid'],
				'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
				'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
		);
		
		$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
		
		$idTaxaInstalacao = $retornoValor['valor'];
		
		try{
			$forma_pagamento = $_POST['forma_pagamento'];	
			
			$sql = "
				SELECT 
					offcqtdmaxparcsemjuros, offcqtdmaxparccomjuros,  offctaxajurosam				
				FROM 
					obrigacao_financeira_forma_cobranca offc
				INNER JOIN 
					obrigacao_financeira o ON o.obroid = offc.offcobroid		
				WHERE 
					obroid = $idTaxaInstalacao
					AND offcforcoid = $forma_pagamento
			";			
				
			if (!$res = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao buscar informações.");
			}
		
			if (pg_num_rows($res)>0) {
				$parcelas = pg_fetch_all($res);
		
				$retorno['maxparcsemjuros'] 	= $parcelas[0]['offcqtdmaxparcsemjuros'];
				$retorno['maxparccomjuros'] 	= $parcelas[0]['offcqtdmaxparccomjuros'];
				$retorno['taxajurosam'] 		= $parcelas[0]['offctaxajurosam'];

			} else {
				throw new Exception("Erro ao buscar informações.",1);
			}
		
			return $retorno;
		}catch (Exception $e){
			$retorno = array(
					'erro' 			=> true,
					'codigoErro' 	=> $e->getCode(),
					'msg'  			=> $e->getMessage()
			);
			return $retorno;
		}
	}
	
	public function formaPagamentoIsCredito(){
		try{
			
			$forma_pagamento = $_POST['forma_pagamento'];

			if ($forma_pagamento == ""){
				$forma_pagamento = $_POST['taxa_instalacao_pagamento_copia'];
			}
			
			if ($forma_pagamento == ""){
				$forma_pagamento = $_POST['taxa_instalacao_pagamento'];
			}
			
			$sql = "
					SELECT 
						forccobranca_cartao_credito
					FROM 
						forma_cobranca
					WHERE 
						forcoid = $forma_pagamento";
							
			if (!$res = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao verificar forma de cobrança.");
			}
		
			if (pg_num_rows($res)>0) {
				$config = pg_fetch_all($res);
		
				$retorno['credito'] 		= $config[0]['forccobranca_cartao_credito'];
				$retorno['forma_pagamento']	= $forma_pagamento;
			} else {
				throw new Exception("Erro ao verificar forma de cobrança.");
			}
		
			return $retorno;
		}catch (Exception $e){
			$retorno = array(
					'erro' 			=> true,
					'msg'  			=> $e->getMessage()
			);
			return $retorno;
		}
	}
	
	public function tituloPago(){
		try{					
			/* Id da taxa de instalação de acordo com a tabela */
			$parametrosSiggo = new PrnParametrosSiggo();
			
			$paramsPesquisa = array(
					'id_tipo_proposta'		=>	$_POST['id_proposta'],
					'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
					'id_tipo_contrato'		=>	$_POST['prptpcoid'],
					'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
					'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
			);
			
			$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
			
			$idTaxaInstalacao = $retornoValor['valor'];
			
			$prpoid = $_POST['prpoid'];
			
			$prpoid_temp = explode(",", $prpoid);
			$prpoid = $prpoid_temp[0];
			
			$sqlContrato = "
					SELECT
						prptermo
					FROM
						proposta
					WHERE
						prpoid = $prpoid
			";
			
			if (!$resContrato = pg_query($this->conn, $sqlContrato)) {
				throw new Exception ("Erro ao inserir titulo do responsável. Tente novamente!");
			}
			
			$contrato_numero = pg_fetch_result($resContrato,0,'prptermo');
			
			$sqlTituloPago = "
					SELECT
						t.titnfloid AS titulo_id_pago
					FROM
						nota_fiscal_item
					INNER JOIN
						nota_fiscal nf ON nf.nflno_numero = nfino_numero
					INNER JOIN
						titulo t ON t.titnfloid = nf.nfloid
					WHERE
						nfiobroid = $idTaxaInstalacao
						AND nficonoid = $contrato_numero
						AND titdt_cancelamento IS NULL
						AND titvl_pagamento >= titvl_titulo;
					";
			
			if (!$resTituloPago = pg_query($this->conn, $sqlTituloPago)) {
				throw new Exception("Erro ao verificar contrato.");
			}
			
			$tituloPago = pg_num_rows($resTituloPago);
			
			
			$retorno = array(
					'erro' 			=> false,
					'tituloPago'  	=> $tituloPago
			);
			return $retorno;
			
		}catch (Exception $e){
			$retorno = array(
					'erro' 			=> true,
					'msg'  			=> $e->getMessage()
			);
			return $retorno;
		}
	}
	
	public function tituloAlterado(){
		try{
			
			/* Id da taxa de instalação de acordo com a tabela */
			$parametrosSiggo = new PrnParametrosSiggo();
				
			$paramsPesquisa = array(
					'id_tipo_proposta'		=>	$_POST['id_proposta'],
					'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
					'id_tipo_contrato'		=>	$_POST['prptpcoid'],
					'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
					'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
			);
				
			$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
				
			$idTaxaInstalacao = $retornoValor['valor'];
			
			// Forma de pagamento selecionada na taxa de instalação
			$forma_pagamento_nova = $_POST['forma_pagamento'];
			
			if ($forma_pagamento_nova == ""){
				$forma_pagamento_nova = $_POST['taxa_instalacao_pagamento_copia'];
			}
				
			if ($forma_pagamento_nova == ""){
				$forma_pagamento_nova = $_POST['taxa_instalacao_pagamento'];
			}
			
			// Novo valor taxa de instalacao
			$valor_taxa_novo 	= str_replace(".","", $_POST['taxa_instalacao_valor']);
			$valor_taxa_novo 	= str_replace(",",".", $valor_taxa_novo);
			
			// titulo alterado valor padrão 0 (sem alteração)
			$tituloAlterado = 0;
			
			$prpoid = $_POST['prpoid'];
				
			$prpoid_temp = explode(",", $prpoid);
			$prpoid = $prpoid_temp[0];
				
			$sqlContrato = "
					SELECT
						prptermo
					FROM
						proposta
					WHERE
						prpoid = $prpoid
			";
				
			if (!$resContrato = pg_query($this->conn, $sqlContrato)) {
				throw new Exception ("Erro ao inserir titulo do responsável. Tente novamente!");
			}
				
			$contrato_numero = pg_fetch_result($resContrato,0,'prptermo');
						
			$sqlTituloAlterado = "
				SELECT 
		      		titrivl_item AS valor_item, titformacobranca AS forma_pagamento
		 		FROM 
		 			titulo_retencao_item
		 		INNER JOIN 
		 			titulo_retencao tr ON tr.titoid = titrititoid
		  		WHERE 
			  		titriobroid = $idTaxaInstalacao
			  		AND titriconoid = $contrato_numero
			  		AND titdt_cancelamento IS NULL
			";
			
			if (!$resTituloAlterado = pg_query($this->conn, $sqlTituloAlterado)) {
				throw new Exception("Erro ao verificar contrato.");
			}
					
			// Encontrou o item do titulo
			if (pg_num_rows($resTituloAlterado)>0) {
				
				$valor_item_atual = pg_fetch_result($resTituloAlterado,0,'valor_item');
				$forma_pagamento_atual = pg_fetch_result($resTituloAlterado,0,'forma_pagamento');

				// Verifica se o valor atual é diferente do novo
				if ($valor_taxa_novo != $valor_item_atual){
					$tituloAlterado = 1;
				}
				
				// Verifica se a forma atual é diferente da nova
				if ($forma_pagamento_nova != $forma_pagamento_atual){
					$tituloAlterado = 1;
				}	
			}else{
				// Se não encontrou nenhum titulo, define como alteração de titulo, para ser inserido novo titulo
				$tituloAlterado = 1;
			}

			$retorno = array(
						'erro' 				=> false,
						'tituloAlterado'  	=> $tituloAlterado
			);
			return $retorno;
		
		}catch (Exception $e){
			$retorno = array(
					'erro' 			=> true,
					'msg'  			=> $e->getMessage()
			);
			return $retorno;
		}
	}
	
	public function salvarTituloBoleto(){
		try{			

			/* Data de vencimento de acordo com a data informada na tabela parametros_gerais */
			$parametrosSiggo = new PrnParametrosSiggo();
				
			$paramsPesquisa = array(
					'id_tipo_proposta'		=>	$_POST['id_proposta'],
					'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
					'id_tipo_contrato'		=>	$_POST['prptpcoid'],
					'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
					'nome_parametro'		=> 	'BOLETO_SECO_DIAS_DE_VENCIMENTO'
			);
				
			$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
				
			$diasVencimento = $retornoValor['valor'];
			
			$paramsPesquisa = array(
					'id_tipo_proposta'		=>	$_POST['id_proposta'],
					'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
					'id_tipo_contrato'		=>	$_POST['prptpcoid'],
					'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
					'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
			);
				
			$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
				
			$idTaxaInstalacao = $retornoValor['valor'];
			
			$sqlObrigacao = "
					SELECT 
						* 
					FROM 
						obrigacao_financeira 
					WHERE 
						obroid = {$idTaxaInstalacao}
				";
			
			if (!$resTitulo = pg_query($this->conn, $sqlObrigacao)) {
				throw new Exception("Erro ao verificar contrato.");
			}
			
			$texto_titulo = pg_fetch_result($resTitulo,0,'obrobrigacao');
			
			
			$forma_pagamento = $_POST['forma_pagamento'];
				
			if ($forma_pagamento == ""){
				$forma_pagamento = $_POST['taxa_instalacao_pagamento_copia'];
			}
			
			if ($forma_pagamento == ""){
				$forma_pagamento = $_POST['taxa_instalacao_pagamento'];
			}
			
			$contrato_numero = $_POST['_termo_veiculo'];
			
			// Informações
			$cdCliente 		= $_POST['clioid'];
			$cdUsuario 		= $_POST['cod_usu'];
			
			$valorBoleto 	= str_replace(".","", $_POST['taxa_instalacao_valor']);
			$valorBoleto 	= str_replace(",",".", $valorBoleto);
			
			$qntdVeiculos	= $_POST['taxa_instalacao_qntd_veiculos'];
			
			// Valor Por Item 
			$valorBoletoItem = $valorBoleto / $qntdVeiculos;
			
			// Formatar valor para salvar na base
			$valorBoleto = ($valorBoleto != "") ? number_format($valorBoleto, 2, ".", "") : "";
			$valorBoletoItem = ($valorBoletoItem != "") ? number_format($valorBoletoItem, 2, ".", "") : "";
			
			// Contém a proposta ou as propostas dependendo da quantidade de veículos (várias propostas pode ocorrer no cadastro)
			$listaProposta 		= $_POST['prpoid'];
							
			// Array de itens a ser cadastrado
			$itensContratos = array();
			
			// Array com as propostas referentes ao titulo.
			$tituloPropostas = array();
			
			$numTitulos = 0;
			// Se possuir número de contrato
			if (!empty($contrato_numero)){
				
			// verifica se possui algum título_retencao de taxa de instalação, sem estar excluido
				$sqlTituloItem = "
						SELECT
							titrititoid
						FROM
							titulo_retencao_item
						INNER JOIN 
							titulo_retencao t ON t.titoid = titrititoid
						WHERE
							titriobroid = $idTaxaInstalacao
							AND titriconoid = $contrato_numero
							AND titdt_cancelamento IS NULL
						";
				
				if (!$resTitulo = pg_query($this->conn, $sqlTituloItem)) {
					throw new Exception("Erro ao verificar contrato.");
				}
				
				// Titulos de taxa de instalação para o contrato
				$numTitulos = pg_num_rows($resTitulo);
				
				if ($numTitulos > 0){
					// Encontrou título de taxa de instalação para o contrato, então cancela os títulos
					
					$titulo_id = pg_fetch_result($resTitulo,0,'titrititoid');
					$sqlTituloExcluir = "
							UPDATE
								titulo_retencao
							SET
								titdt_cancelamento = NOW(),
								titobs_cancelamento = 'Gerado novo titulo'
							WHERE
								titoid = $titulo_id
							";
					
					if (!$resTituloExcluir = pg_query($this->conn, $sqlTituloExcluir)) {
						throw new Exception("Erro atualizar título da taxa de instalação.");
					}
					
					// Gerar titulo para o item unico
					$valorBoleto = $valorBoletoItem;
					$valorBoleto = ($valorBoleto != "") ? number_format($valorBoleto, 2, ".", "") : "";
					$itensContratos[$contrato_numero] = $valorBoletoItem;
					
					/*
					// Buscar todos os itens do titulo que foi excluido, com o valor de cada item, para ser inserido novamente
					$sqlTituloItens = "
							SELECT 
								titriconoid, titrivl_item, prpoid
							FROM 
								titulo_retencao_item
							INNER JOIN 
								proposta p ON p.prptermo = titriconoid
							WHERE
								titrititoid = $titulo_id
							";
						
					if (!$resTituloItens = pg_query($this->conn, $sqlTituloItens)) {
						throw new Exception("Erro atualizar título da taxa de instalação.");
					}
					
					// Monta um array com itens do contrato com o valor que estava cadastrado, array que sera cadastrado na tabela titulo_retencao_item
					while ($arrRs = pg_fetch_array($resTituloItens)){
						$itensContratos[$arrRs['titriconoid']] = $arrRs['titrivl_item'];
						$tituloPropostas[] = $arrRs['prpoid'];
						
					}
					
					// Atualiza para o novo valor
					$itensContratos[$contrato_numero] = $valorBoletoItem;
					
					// Soma o valor do título
					$valorBoleto = 0;
					foreach ($itensContratos as $item => $valor){
						$valorBoleto += $valor;
					}
					$valorBoleto = ($valorBoleto != "") ? number_format($valorBoleto, 2, ".", "") : "";
					*/
					
					
					
					
				}
			}
			
			// Não tem itens cadastrado ( Proposta nova)
			if ($numTitulos == 0){	
				$propostas = explode(",", $listaProposta);
				
				foreach ($propostas as $prop) {
					$sqlContrato = "
							SELECT
								prptermo
							FROM
								proposta
							WHERE
								prpoid=$prop";
						
					if (!$resContrato = pg_query($this->conn, $sqlContrato)) {
						throw new Exception ("Erro ao inserir titulo do responsável. Tente novamente!");
					}
						
					$contrato_numero = pg_fetch_result($resContrato,0,'prptermo');
					
					$itensContratos[$contrato_numero] = $valorBoletoItem;
				}
			}			
			
			$titulo_id = "";
			
			
			$data_hoje = date('d-m-Y');
			$data_venc = date("d-m-Y", strtotime("$data_hoje +$diasVencimento days"));
			
			//echo $data_venc;exit;
			
			$data_venc = str_replace("-","/", $data_venc);
			
			$boletoSeco = new PrnBoletoSeco();
			$data_venc = $boletoSeco->isUtil($data_venc);
			
			$data_venc_boleto = implode("-",array_reverse(explode("/",$data_venc)));
			
			// Gera o título a ser pago
			$sqlTitulo = "
					INSERT INTO titulo_retencao
						(
							titclioid,
							titformacobranca,
							titdt_inclusao,
							titvl_titulo_retencao,
							titnatureza,
							titserie,
							titusuoid_alteracao,
							tittboid,
							titno_parcela,
							titdt_vencimento
						)
						VALUES
						(
							$cdCliente,
							$forma_pagamento,
							NOW(),
							'$valorBoleto',
							'".$texto_titulo."',
							'A',
							$cdUsuario,
							'".$this->codigo_tipo_boleto."',
							1,
							'$data_venc_boleto'
						)
						RETURNING titoid";
				
			if (!$resTitulo = pg_query($this->conn, $sqlTitulo)) {
				throw new Exception ("ERRO ao inserir titulo do responsável. Tente novamente!");
			}
			
			$titulo_id = pg_fetch_result($resTitulo,0,'titoid');
			
			$codigo_cedente= 3471241;
			$nossonum_com_DV = montaNossoNumeroHSBC($titulo_id, $data_venc, $codigo_cedente);
			
			// Atualiza o titulo com o nosso numetro gerado
			$sqlTituloNossoNumero = "
					UPDATE 
						titulo_retencao 
					SET 
						titnumero_registro_banco = $nossonum_com_DV 
					WHERE titoid = $titulo_id";
			
			if (!$resTituloNossoNumero = pg_query($this->conn, $sqlTituloNossoNumero)) {
				throw new Exception ("ERRO ao inserir titulo do responsável. Tente novamente!");
			}
			
			// Inserir os itens do titulo
			foreach ($itensContratos as $contrato => $valor){
				$sqlTituloItem = "
						INSERT INTO titulo_retencao_item
							(
							titrititoid,
							titriconoid,
							titriobroid,
							titridt_cadastro,
							titrivl_item
							)
						VALUES
							(
							$titulo_id,
							$contrato,
							$idTaxaInstalacao,
							NOW(),
							'$valor'
						)";
				
				if (!$resTituloItem = pg_query($this->conn, $sqlTituloItem)) {
					throw new Exception ("Erro ao inserir item do titulo.");
				}				
			}					
			
			
			// Insere na tabela titulo_controle_envio
			// Indicando que o boleto ainda não foi enviado ao cliente
			
			$sqlControleEnvio = "
					INSERT INTO titulo_controle_envio
						(
						tcetitoid,
						tceconoid,
						tcetipo,
						tcestatus_envio,
						tcedata_criacao
						)
					VALUES 
						(
						$titulo_id,
						'$contrato_numero',
						'boleto_seco',
						'false',
						NOW()
						)
					";
			
			if (!$resControle = pg_query($this->conn, $sqlControleEnvio)) {
				throw new Exception ("Erro ao inserir item do titulo.");
			}
			
			$retorno = array(
					'erro' 				=> false,
					'titoid'  			=> $titulo_id,
					'tituloPropostas'	=> implode(",", $tituloPropostas),
					'contrato'			=> $contrato_numero,
					'cartaoCredito'		=> 'f'
			);
			return $retorno;
		}catch (Exception $e){
			$retorno = array(
					'erro' 			=> true,
					'msg'  			=> $e->getMessage()
			);
			return $retorno;
		}
	}
	
	public function salvarTituloCartaoCredito(){
		try{
			
			/* Id da taxa de instalação de acordo com a tabela */
			$parametrosSiggo = new PrnParametrosSiggo();
			
			$paramsPesquisa = array(
					'id_tipo_proposta'		=>	$_POST['id_proposta'],
					'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
					'id_tipo_contrato'		=>	$_POST['prptpcoid'],
					'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
					'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
			);
			
			$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
			
			$idTaxaInstalacao = $retornoValor['valor'];
			
			$sqlObrigacao = "
					SELECT
						*
					FROM
						obrigacao_financeira
					WHERE
						obroid = {$idTaxaInstalacao}
				";
				
			if (!$resTitulo = pg_query($this->conn, $sqlObrigacao)) {
				throw new Exception("Erro ao verificar contrato.");
			}
				
			$texto_titulo = pg_fetch_result($resTitulo,0,'obrobrigacao');
			
			$forma_pagamento = $_POST['forma_pagamento'];
			
			if ($forma_pagamento == ""){
				$forma_pagamento = $_POST['taxa_instalacao_pagamento_copia'];
			}
				
			if ($forma_pagamento == ""){
				$forma_pagamento = $_POST['taxa_instalacao_pagamento'];
			}
			
			$contrato_numero = $_POST['_termo_veiculo'];
				
			// Se nao tem contrato, procura o contrato pelo código da proposta
			if (empty($contrato_numero)){
				
				$prop_temp = explode("," ,$_POST['prpoid']);
				
				$prop_temp = $prop_temp[0];
				
				$sqlContrato = "
						SELECT
							prptermo
						FROM
							proposta
						WHERE
							prpoid=$prop_temp
						";
								
				if (!$resContrato = pg_query($this->conn, $sqlContrato)) {
					throw new Exception ("Erro ao inserir titulo do responsável. Tente novamente!");
				}
			
				$contrato_numero = pg_fetch_result($resContrato,0,'prptermo');
			}
			
			
			// Informações
			$cdCliente 			= $_POST['clioid'];
			$cdUsuario 			= $_POST['cod_usu'];
			
			$valorBoleto 		= str_replace(".","", $_POST['taxa_instalacao_valor']);
			$valorBoleto 		= str_replace(",",".", $valorBoleto);
			
			$qntdVeiculos		= $_POST['taxa_instalacao_qntd_veiculos'];
							
			// Valor Por Item
			$valorBoletoItem = $valorBoleto / $qntdVeiculos;
				
			// Contém a proposta ou as propostas dependendo da quantidade de veículos
			$listaProposta 		= $_POST['prpoid'];
				
			// Formatar valor para salvar na base
			$valorBoleto = ($valorBoleto != "") ? number_format($valorBoleto, 2, ".", "") : "";
			$valorBoletoItem = ($valorBoletoItem != "") ? number_format($valorBoletoItem, 2, ".", "") : "";
	
			// Array de itens a ser cadastrado
			$itensContratos = array();
			
			// Array a ser preenchido com as propostas referente ao titulo excluido
			$tituloPropostas = array();
			
			$numTitulos = 0;
			// Tem número de contrato
			if (!empty($contrato_numero)){
				
				// Procura se antes a forma era pagamento por boleto
				$sqlTituloItem = "
						SELECT
							titrititoid
						FROM
							titulo_retencao_item
						INNER JOIN
							titulo_retencao t ON t.titoid = titrititoid
						WHERE
							titriobroid = $idTaxaInstalacao
							AND titriconoid = $contrato_numero
							AND titdt_cancelamento IS NULL
				";
				
				if (!$resTitulo = pg_query($this->conn, $sqlTituloItem)) {
					throw new Exception("Erro ao verificar contrato.");
				}
				
				// Titulos de taxa de instalação para o contrato
				$numTitulos = pg_num_rows($resTitulo);
				
				if ($numTitulos > 0){
				// Encontrou título de taxa de instalação para o contrato, então cancela os títulos
						
					$titulo_id = pg_fetch_result($resTitulo,0,'titrititoid');
					$sqlTituloExcluir = "
							UPDATE
								titulo_retencao
							SET
								titdt_cancelamento = NOW(),
								titobs_cancelamento = 'Alterado a forma de pagamento para cartao de credito'
							WHERE
								titoid = $titulo_id
								";
													
					if (!$resTituloExcluir = pg_query($this->conn, $sqlTituloExcluir)) {
						throw new Exception("Erro atualizar título da taxa de instalação.");
					}
					
					// Buscar todos os itens do titulo que foi excluido, com o valor de cada item, para ser inserido novamente
					$sqlTituloItens = "
							SELECT
								titriconoid, titrivl_item, prpoid
							FROM
								titulo_retencao_item
							INNER JOIN 
								proposta p ON p.prptermo = titriconoid
							WHERE
								titrititoid = $titulo_id
					";
					
					if (!$resTituloItens = pg_query($this->conn, $sqlTituloItens)) {
						throw new Exception("Erro atualizar título da taxa de instalação.");
					}
			
					// Monta um array com itens do contrato com o valor que estava cadastrado, array que sera cadastrado na tabela titulo_retencao_item
					while ($arrRs = pg_fetch_array($resTituloItens)){
						$itensContratos[$arrRs['titriconoid']] = $arrRs['titrivl_item'];
						$tituloPropostas[] = $arrRs['prpoid'];
					}
					// Atualiza para o novo valor
					$itensContratos[$contrato_numero] = $valorBoletoItem;
						
					// Soma o valor do título
					$valorBoleto = 0;
					foreach ($itensContratos as $item => $valor){
						$valorBoleto += $valor;
					}
					$valorBoleto = ($valorBoleto != "") ? number_format($valorBoleto, 2, ".", "") : "";
				}
				
				// Verifica se possui algum título de taxa de instalação (pagamento por cartão de credito)
				$sqlTituloItem = "
					SELECT
						nfioid
					FROM
						nota_fiscal_item
					WHERE
						nfiobroid = $idTaxaInstalacao
						AND nficonoid = $contrato_numero";
	
				if (!$res = pg_query($this->conn, $sqlTituloItem)) {
					throw new Exception("Erro ao verificar contrato.");
				}
	
				// Itens de taxa de instalação para o contrato
				$numTitulos = pg_num_rows($res);
			}
				
			// Não tem itens cadastrado ( Proposta nova)
			if ($numTitulos == 0){
				$propostas = explode(",", $listaProposta);
			
				foreach ($propostas as $prop) {
					$sqlContrato = "
					SELECT
						prptermo
					FROM
						proposta
					WHERE
						prpoid=$prop";
			
					if (!$resContrato = pg_query($this->conn, $sqlContrato)) {
						throw new Exception ("Erro ao inserir titulo do responsável. Tente novamente!");
					}
			
					$contrato_numero = pg_fetch_result($resContrato,0,'prptermo');
						
					$itensContratos[$contrato_numero] = $valorBoletoItem;
				}
			}
				
			$titulo_id = "";
						
			// INSERIR TITULO (PAGAMENTO CARTAO DE CREDITO)
			
			// Gerar no_numero (para inserir na nota_fiscal)
			$sqlNoNumero = "
					SELECT 
						MAX(nflno_numero) + 1 AS no_numero
					FROM 
						nota_fiscal n
					WHERE 
						n.nflserie = 'A';
			";
				
			if (!$resNoNumero = pg_query($this->conn, $sqlNoNumero)) {
				throw new Exception ("ERRO ao inserir titulo do responsável. Tente novamente!");
			}
			
			$no_numero = pg_fetch_result($resNoNumero,0,'no_numero');			
			
			$sqlNotaFiscal = "
				INSERT INTO nota_fiscal
					(
						nflno_numero,
						nfldt_inclusao,
						nfldt_nota,
						nfldt_emissao,
						nflnatureza,
						nflclioid,
						nflserie,
						nflvl_total,
						nflusuoid
					)
					VALUES
					(
						$no_numero,
						NOW(),
						NOW(),
						NOW(),
						'".$texto_titulo."',
						$cdCliente,
						'A',
						'$valorBoleto',
						$cdUsuario 
				)	
				RETURNING nfloid";
			
			if (!$resNotaFiscal = pg_query($this->conn, $sqlNotaFiscal)) {
				throw new Exception ("ERRO ao inserir titulo do responsável. Tente novamente!");
			}

			$nota_fiscal_id = pg_fetch_result($resNotaFiscal,0,'nfloid');

			$propostas = explode(",", $listaProposta);
					
			if (is_array($itensContratos)) {
				foreach ($itensContratos as $contrato => $valor){

					// Fazer de cada item de acordo com a quantidade de veiculos/contratos
					$sqlNotaFiscalItem = "
						INSERT INTO nota_fiscal_item
							(
								nfino_numero,
								nfinfloid,
								nfiserie,
								nficonoid,
								nfiobroid,
								nfids_item,
								nfivl_item,
								nfidt_inclusao
							)
						VALUES
							(
								$no_numero,
								$nota_fiscal_id,
								'A',
								$contrato,
								$idTaxaInstalacao,
								'TAXA DE INSTALACAO',
								'$valor',
								NOW()
							)
					";
										
					if (!$resNotaFiscalItem = pg_query($this->conn, $sqlNotaFiscalItem)) {
						throw new Exception ("Erro ao inserir item da nota fiscal.");
					}
					
				}
				
				// Inserir o titulo
				$sqlTitulo = "
						INSERT INTO titulo
						(
							titdt_inclusao,
							titemissao,
							titdt_vencimento,
							titnfloid,
							titno_parcela,
							titvl_titulo,
							titclioid,
							titformacobranca								
						)
						VALUES
						(
							NOW(),
							NOW(),
							NOW(),
							$nota_fiscal_id,
							1,
							'$valorBoleto',
							$cdCliente,
							$forma_pagamento
						)
						RETURNING titoid";
									
				if (!$resTitulo = pg_query($this->conn, $sqlTitulo)) {
					throw new Exception ("ERRO ao inserir titulo do responsável. Tente novamente!");
				}
				
				$titulo_id = pg_fetch_result($resTitulo,0,'titoid');
			}
					
			$retorno = array(
					'erro' 				=> false,
					'titoid'  			=> $titulo_id,
					'valorTotal'  		=> $valorBoleto,
					'tituloPropostas'	=> implode(",", $tituloPropostas),
					'tituloAlterado'	=> true,
					'cartaoCredito'		=> 't'
			);
			
			return $retorno;
			
		}catch (Exception $e){
				$retorno = array(
						'erro' 			=> true,
						'msg'  			=> $e->getMessage()
			);
			return $retorno;
		}
	}

	public function confereGerarConOSAuto($contrato, $proposta){

		$sql = "select 
					p.prpoid, ca.apcoid, ca.apcativo, 
					c.connumero, p.prpforcoid, ca.apccodigo
				from contrato c, 
					proposta p,
					tipo_proposta tp,
					tipo_proposta_acao tpa,
					acao_contrato ca
				where 1=1
					and c.connumero = $contrato
					and p.prpoid = $proposta
					and p.prptipo_proposta = tp.tppcodigo
					and p.prptppoid = tp.tppoid
					and tp.tppoid = tpa.tpatppoid
					and ca.apcoid = tpa.tpaapcoid
				group by
					p.prpoid, c.connumero, ca.apccodigo, ca.apcativo,
					p.prpforcoid, ca.apccodigo, ca.apcoid";

		if(!$res = pg_query($this->conn, $sql)){
			throw new Exception("Erro ao pesquisar contrato e OS automaticos.");
		}

		$retorno = (pg_num_rows($res) > 0) ? pg_fetch_all($res) : false;

		return $retorno;
	}

	public function getDadosOSPagamento($contrato){
		
		
		$parametrosSiggo = new PrnParametrosSiggo();
			
		$paramsPesquisa = array(
				'id_tipo_proposta'		=>	$_POST['id_proposta'],
				'id_subtipo_proposta'	=>	$_POST['id_subproposta'],
				'id_tipo_contrato'		=>	$_POST['prptpcoid'],
				'id_equipamento_classe'	=> 	$_POST['prpeqcoid'],
				'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
		);
			
		$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);
			
		$idTaxaInstalacao = $retornoValor['valor'];

		$sql = "select 
					nfi.nfino_numero, t.titvl_pagamento, titvl_titulo, nfi.nfiobroid
				from nota_fiscal_item nfi, titulo t
				where nfi.nficonoid = $contrato
					and t.titvl_pagamento >= titvl_titulo 
					and t.titnfloid = nfi.nfinfloid 
					and nfi.nfiobroid = $idTaxaInstalacao
					and t.titformacobranca in (7, 8, 9, 10, 24, 25, 26, 27, 82)";

		//nfino_numero
		if(!$res = pg_query($this->conn, $sql)){
			throw new Exception("Erro ao pesquisar OS automatica.");
		}
		
		$retorno = (pg_num_rows($res) > 0) ? pg_fetch_all($res) : false;

		return $retorno;
	}
	
	
	public function verificaStatusGestorCredito($prpoid) {
		$sql = "
			SELECT
				prppsfoid, psfdescricao
			FROM
				proposta prp
			INNER JOIN
				proposta_status_financeiro psf ON prp.prppsfoid = psf.psfoid
			WHERE prpoid in ($prpoid);
		";
		
		$query = pg_query($this->conn, $sql);
		 
		$result = pg_fetch_object($query);
		
		return $result;
	}
	
	public function getValorMultaRescisoria($vigencia_contrato){
		try {
			$sql = "
				SELECT 
					cvgmulta_rescisoria AS multa_rescisoria
				FROM 
					contrato_vigencia
				WHERE 
					cvgvigencia = {$vigencia_contrato}
				AND cvgdt_exclusao IS NULL
				";
			
			if(!$res = pg_query($this->conn, $sql)){
				throw new Exception("Erro ao buscar o valor da multa rescisória.");
			}
			
			if (pg_num_rows($res) > 0){
				$resultado = pg_fetch_object($res);
				
				$retorno = array(
							'erro'		 		=> 0,
							'multa_rescisoria'	=> $resultado->multa_rescisoria
						);
			} else {
				throw new Exception("Não encontrado valor da multa rescisória de acordo com o prazo de vigência informado.");
			}
			
			return $retorno;
			
		}catch (Exception $e) {
			
			$retorno = array(
						'erro'	=>	1,
						'msg'	=>	$e->getMessage()
					);
			return $retorno;
		}
	}
	
	public function getAcaoContrato($acaoNome){
		try {
			$sql = "
				SELECT
					*
				FROM
					acao_contrato
				WHERE
					apccodigo like '{$acaoNome}'
			";
				
			if(!$res = pg_query($this->conn, $sql)){
				throw new Exception("Erro ao buscar os tipos de ação.");
			}
		
			if (pg_num_rows($res) > 0){
				
				$acao = pg_fetch_object($res);
				
				$retorno = array(
							'erro'			=> 0,
							'resultado'		=> $acao
						);
			} 
		
			return $retorno;
		
		}catch (Exception $e) {
				
			$retorno = array(
					'erro'	=>	1,
					'msg'	=>	$e->getMessage()
					);
			return $retorno;
		}
	}
	
	public function getTaxaInstalacaoProposta($proposta){
		try {
			$sql = "
				SELECT
					ppagadesao AS taxa_valor, ppagadesao_parcela AS taxa_parcelas, ppagforcoid_adesao AS taxa_forma_pagm
				FROM
					proposta_pagamento
				WHERE
					ppagprpoid = $proposta
			";

			if(!$res = pg_query($this->conn, $sql)){
				throw new Exception("Erro ao busca os dados da proposta.");
			}

			if (pg_num_rows($res) > 0){

				$dados = pg_fetch_object($res);
				
				$retorno = array(
						'erro'				=> 0,
						'taxa_valor'		=> $dados->taxa_valor,
						'taxa_parcelas'		=> $dados->taxa_parcelas,
						'taxa_forma_pagm'	=> $dados->taxa_forma_pagm
				);
			}

			return $retorno;

		}catch (Exception $e) {

			$retorno = array(
					'erro'	=>	1,
					'msg'	=>	$e->getMessage()
			);
			return $retorno;

		}
	}
	
	public function getValorTaxaParcela($busca){
		try{

			/* Id da taxa de instalação de acordo com a tabela */
			$parametrosSiggo = new PrnParametrosSiggo();

			$paramsPesquisa = array(
					'id_tipo_proposta'		=>	$busca['id_proposta'],
					'id_subtipo_proposta'	=>	$busca['id_subproposta'],
					'id_tipo_contrato'		=>	$busca['prptpcoid'],
					'id_equipamento_classe'	=> 	$busca['prpeqcoid'],
					'nome_parametro'		=> 	'ID_OBRIGACAO_FINANCEIRA_DA_TAXA_DE_INSTALACAO'
			);

			$retornoValor = $parametrosSiggo->getValorParametros($paramsPesquisa);

			$idTaxaInstalacao = $retornoValor['valor'];

			$sql = "
				SELECT
					tpivalor, tpivalor_minimo
				FROM
					tabela_preco_item
				INNER JOIN
					tabela_preco ON tproid = tpitproid
				INNER JOIN
					cond_pgto_venda ON cpvoid = tpicpvoid
				WHERE
					tpiobroid = $idTaxaInstalacao
				AND tpiexclusao IS NULL
				AND tprstatus = 'A'
				AND cpvparcela = {$busca['num_parcelas']}
			";

			if ($busca['juros'] == "true"){
				$sql .= " AND cpvdescricao ilike '%juros%' ";
			}
				
			if (!$res = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao buscar informações.");
			}

			if (pg_num_rows($res)>0) {
				$valor = pg_fetch_all($res);

				$valor_taxa = $valor[0]['tpivalor'];
				$valor_taxa_minimo =$valor[0]['tpivalor_minimo'];
				
				$valor_taxa = $valor_taxa * $busca['num_parcelas'];
				
				// Transforma em formato moeda.
				$retorno['tpivalor'] 		= ($valor_taxa != "") ? number_format($valor_taxa, 2, ",", ".") : "";
				$retorno['tpivalor_minimo'] = ($valor_taxa_minimo != "") ? number_format($valor_taxa_minimo, 2, ",", ".") : "";

			} else {
				throw new Exception("Erro ao buscar informações.");
			}

			return $retorno;
		}catch (Exception $e){
			$retorno = array(
					'erro' => true,
					'msg'  => $e->getMessage()
			);
			return $retorno;
		}
	}
	
	public function infoTipoVeiculo($tipvoid) {

		$retorno = array();

		try {

			$sql = "SELECT 
						tipvoid,
						tipvdescricao,
						tipvusuoid_excl,
						tipvexclusao,
						tipvcategoria,
						tipvstatus,
						tipvdt_cadastro,
						tipvusuoid_inclusao,
						tipvdt_alteracao,
						tipvusuoid_alteracao,
						tipvcarreta 
					FROM 
						tipo_veiculo 
					WHERE 
						tipvexclusao IS NULL
					AND
						tipvoid = " . (int) $tipvoid;

			$res = pg_query($this->conn, $sql);

			if (!$res) {
				throw new Exception("Erro ao recuperar tipo do veiculo.");
			}

			if(pg_num_rows($res) > 0) {
				$retorno['result'] = pg_fetch_assoc($res);
			} else {
				throw new Exception("Não foi possível recuperar tipo do veículo.");
			}

		} catch (Exception $e) {
			$retorno['erro'] = $e->getMessage();
		}

		//testes
		//$retorno['result']['tipvcarreta'] = 't';
		
		return $retorno;
	}

	public function tipoCarreta($tipcoid = NULL) {

		$retorno = array();

		try{

			$sql = "SELECT
						tipcoid,
						tipcdescricao,
						tipcrecebe_categoria,
						tipceixos
					FROM 
						tipo_carreta
					WHERE tipcexclusao IS NULL 
					";

			if(!is_null($tipcoid)) {
				$sql .= ' AND tipcoid = ' . (int) $tipcoid;
			}

			$sql .= ' ORDER BY tipcdescricao ASC ';

			$res = pg_query($this->conn, $sql);

			if (!$res) {
				throw new Exception("Erro ao recuperar os tipos de carreta.");
			}

			if(pg_num_rows($res) > 0) {
				return $res;
			} else {
				throw new Exception("Não foi possível recuperar tipos de carreta.");
			}

		} catch(Exception $e) {
			$retorno['erro'] = $e->getMessage();
		}

		return $retorno;
	}

	public function marcaModeloEBS($modeoid = NULL) {
		$retorno = array();

		try{

			$sql = "SELECT 
						mme.mmeoid,
						mme.mmedescricao,
						mode.modeoid,
						mode.modedescricao,
						mode.modeobroid
					FROM 
						modelo_ebs AS mode
					INNER JOIN
						marca_modelo_ebs AS mme
					ON mode.modemmeoid = mme.mmeoid
					WHERE
						mode.modedt_exclusao IS NULL
					AND
						mme.mmedt_exclusao IS NULL
					";

			if(!is_null($modeoid)) {
				$sql .= " AND modeoid = " . (int) $modeoid;
			}

			$sql .=" ORDER BY 
						mmedescricao, modedescricao; ";

			$res = pg_query($this->conn, $sql);

			if (!$res) {
				throw new Exception("Erro ao recuperar marca e modelos EBS.");
			}

			if(pg_num_rows($res) > 0) {
				$retorno = $res;
			} else {
				throw new Exception("Não foi possível recuperar marca e modelos EBS.");
			}

		} catch(Exception $e) {
			$retorno['erro'] = $e->getMessage();
		}

		return $retorno;
	}

	public function eixosCarreta($eixosConfigurados = NULL) {
		$retorno = array();

		try{

			$sql = "SELECT 
						eixcoid,
						eixcnumero,
						eixcdescricao,
						eixcusuoid_inclusao,
						eixcdt_inclusao,
						eixcusuoid_excl,
						eixcdt_exclusao
					FROM 
						eixos_carreta
					WHERE
						eixcdt_exclusao IS NULL ";

			if(!is_null($eixosConfigurados)) {
				$sql .= " AND eixcoid IN ( ".$eixosConfigurados." ) ";
			}

			$sql .= ' ORDER BY eixcnumero ASC ';			

			$res = pg_query($this->conn, $sql);

			if (!$res) {
				throw new Exception("Erro ao recuperar os tipos de carreta.");
			}

			if(pg_num_rows($res) > 0) {
				$retorno = $res;
			} else {
				throw new Exception("Não foi possível recuperar tipos de carreta.");
			}

		} catch(Exception $e) {
			$retorno['erro'] = $e->getMessage();
		}

		return $retorno;
	}

	public function dimensaoPneus() {
		$retorno = array();

		try{

			$sql = "SELECT 
						dimpoid,
						dimpdescricao,
						dimpusuoid_inclusao,
						dimpdt_inclusao,
						dimpusuoid_excl,
						dimpdt_exclusao
					FROM 
						dimensao_pneu
					WHERE
						dimpdt_exclusao IS NULL
					ORDER BY dimpdescricao ASC;";

			$res = pg_query($this->conn, $sql);

			if (!$res) {
				throw new Exception("Erro ao recuperar as dimensões de pneus.");
			}

			if(pg_num_rows($res) > 0) {
				$retorno = $res;
			} else {
				throw new Exception("Não foi possível recuperar as dimensões de pneus.");
			}

		} catch(Exception $e) {
			$retorno['erro'] = $e->getMessage();
		}

		return $retorno;
	}

	public function buscarDadosModelo($mlooid) {
		$retorno = array();

		try{

			$sql = "SELECT 
						*
					FROM 
						modelo
					WHERE
						mlooid= " . (int) $mlooid;

			$res = pg_query($this->conn, $sql);

			if (!$res) {
				throw new Exception("Erro ao recuperar dados do modelo.");
			}

			if(pg_num_rows($res) > 0) {
				$retorno['result'] = pg_fetch_assoc($res);
			} else {
				throw new Exception("Não foi possível recuperar dados do modelo.");
			}

		} catch(Exception $e) {
			$retorno['erro'] = $e->getMessage();
		}

		return $retorno;
	}

	public function buscarTipoCarretaCategoria() {
		$retorno = array();

		try{

			$sql = "SELECT 
						tccoid,
						tccdescricao
					FROM 
						tipo_carreta_categoria
					WHERE
						tccdt_exclusao IS NULL 
					ORDER BY tccdescricao ASC";

			$res = pg_query($this->conn, $sql);

			if (!$res) {
				throw new Exception("Erro ao recuperar categorias de carreta.");
			}

			if(pg_num_rows($res) > 0) {
				$retorno = $res;
			} else {
				throw new Exception("Não foi possível recuperar categorias de carreta.");
			}

		} catch(Exception $e) {
			$retorno['erro'] = $e->getMessage();
		}

		return $retorno;
	}

	public function insereServicoContrato($dados) {

		$sql = '';
    	$retorno = array();

    	try {

    		if(!$dadosInsert = $this->insert($dados)) {
    			throw new Exception('Erro ao inserir serviço do contrato');
    		}

	    	$sql = "INSERT INTO
	    				contrato_servico
	    				(:colunas)
						VALUES
						(:valores)";

			$sql = str_replace(':colunas', $dadosInsert['columns'], $sql);
			$sql = str_replace(':valores', $dadosInsert['values'], $sql);
			$retorno['sql'] = $sql;
			
			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception('Erro ao inserir serviço do contrato');
			}

			$retorno['resultado'] = $rs;

		} catch(Exception $e) {
			$retorno['erro'] = $e->getMessage() . " - " . $sql;
		}

		return $retorno;
	}

	public function inserePropostaServico($prpoid) {
		$sql = '';
    	$retorno = array();

    	try {

	    	$sql = "INSERT INTO proposta_servico 
			            (prossituacao, 
			             prosusuoid, 
			             prosqtde, 
			             prosprpoid, 
			             prosobroid, 
			             prosvalor_tabela, 
			             prosvalor)
						(SELECT 'L', 
								2750, 
								1, 
								prpoid, 
								modeobroid, 
								CASE 
								  WHEN (SELECT tpivalor 
										FROM   tabela_preco, 
											   tabela_preco_item, 
											   proposta_pagamento 
										WHERE  ppagprpoid = prpoid 
											   AND tprstatus = 'A' 
											   AND tpitproid = tproid 
											   AND tpicpvoid = ppagcpvoid 
											   AND tpiobroid = modeobroid 
											   AND tpiexclusao IS NULL) > 0 THEN 
								  (SELECT tpivalor 
								   FROM   tabela_preco, 
										  tabela_preco_item, 
										  proposta_pagamento 
								   WHERE  ppagprpoid = prpoid 
										  AND tprstatus = 'A' 
										  AND tpitproid = tproid 
										  AND tpicpvoid = ppagcpvoid 
										  AND tpiobroid = modeobroid 
										  AND tpiexclusao IS NULL) 
								  ELSE 0 
								END, 
								CASE 
								  WHEN (SELECT tpivalor 
										FROM   tabela_preco, 
											   tabela_preco_item, 
											   proposta_pagamento 
										WHERE  ppagprpoid = prpoid 
											   AND tprstatus = 'A' 
											   AND tpitproid = tproid 
											   AND tpicpvoid = ppagcpvoid 
											   AND tpiobroid = modeobroid 
											   AND tpiexclusao IS NULL) > 0 THEN 
								  (SELECT tpivalor 
								   FROM   tabela_preco, 
										  tabela_preco_item, 
										  proposta_pagamento 
								   WHERE  ppagprpoid = prpoid 
										  AND tprstatus = 'A' 
										  AND tpitproid = tproid 
										  AND tpicpvoid = ppagcpvoid 
										  AND tpiobroid = modeobroid 
										  AND tpiexclusao IS NULL) 
								  ELSE 0 
								END 
						 FROM   proposta, 
								modelo_ebs 
						 WHERE  prpmodeoid = modeoid 
								AND modeobroid > 0 
								AND modedt_exclusao IS NULL 
								AND prpoid = :prpoid); ";

			$sql = str_replace(':prpoid', (int) $prpoid, $sql);
			$retorno['sql'] = $sql;
			
			$rs = pg_query($this->conn, $sql);

			if(!$rs) {
				throw new Exception('Erro ao inserir serviço da proposta');
			}

			$retorno['resultado'] = $rs;

		} catch(Exception $e) {
			$retorno['erro'] = $e->getMessage() . " - " . $sql;
		}

		return $retorno;
	}


	/**
     * Retorna array com os dados para realizar insert
     * @param  [array] $dados [Contém chave/valor de cada coluna no insert]
     * @return [array]        [description]
     */
    private function insert($dados) {

    	if(is_null($dados) || !is_array($dados) || count($dados) == 0) {
    		return false;
    	}

    	$valores = '';
    	$colunas = implode(", ", array_keys($dados));

    	foreach ($dados as $key => $value) {

    		$valor = pg_escape_string($value);

    		if(strlen($valores) > 0) {
				$valores .= ' , ';
			}

			if($valor != 'NULL') {
				$valor = "'" . $valor . "'";
			}

    		$valores .= " " . $valor . " ";
    	}

    	return array('columns' => $colunas, 'values' => $valores);
    }
}