<?php

require_once _SITEDIR_ . 'boleto_funcoes.php';
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';
require_once _MODULEDIR_ . 'Financas/Action/FinFaturamentoCartaoCredito.class.php';
require_once _SITEDIR_ . 'lib/GeraBoletoHSBC.php';

/*
 * @author	Ricardo Marangoni da Mota
 * @email	ricardo.mota@meta.com.br
 * @since	04/07/2012
 * */

#############################################################################################################
#   Histórico
#       16/08/2012 - Diego C. Ribeiro(BRQ)
#           Alterada a visibilidade do método generateXML para público
#############################################################################################################


class FinFatVendaEquipamentoDAO {
	
	private $conn;	
	
	public function FinFatVendaEquipamentoDAO($conn){
		$this->conn = $conn;
	}

	/*
	 * @author	Ricardo Marangoni da Mota
	 * @email	ricardo.mota@meta.com.br
	 * @return	Array com os itens filtrados pela pesquisa
	 * */
	public function getContratosFaturamento() {
		
		$cliente_id 		= (isset($_POST['cpx_valor_cliente_nome'])) ? $_POST['cpx_valor_cliente_nome'] : null;
		$data_ini 			= trim($_POST['dt_ini']);
		$data_fim 			= trim($_POST['dt_fim']);
		$cpf 				= (isset($_POST['cpf'])) ? trim($_POST['cpf']) : null;
		$cnpj 				= (isset($_POST['cnpj'])) ? trim($_POST['cnpj']) : null;
		$numero_contrato 	= (isset($_POST['numero_contrato'])) ? trim($_POST['numero_contrato']) : null;
		$numero_resultados 	= trim($_POST['numero_resultados']);
		$is_faturado  		= ($_POST['situacao_contratos'] == 'nao_faturados') ? 'false' : 'true';
		
		$sql_add = "WHERE data_cadastro BETWEEN '$data_ini 00:00:00' AND '$data_fim 23:59:59'";
		$sql_add .= " AND faturado = $is_faturado";
				
		if(!empty($cliente_id)) {
			$sql_add .= " AND clioid = $cliente_id";
		}
		
		if(!empty($cpf)) {
			$sql_add .= " AND cpf = $cpf";
		}
		
		if(!empty($cnpj)) {
			$sql_add .= " AND cnpj = $cnpj";
		}
		
		if(!empty($numero_contrato)) {
			$sql_add .= " AND contrato = $numero_contrato";
		}
		
		/*
		 * O motivo disso é que como limitamos a consulta, o último
		 * contrato por vir faltando itens e isso não pode acontecer
		 * então temos que verificar
		 * */
		$numero_resultados += $this->verificaSeContratoPossuiMaisItens($numero_resultados, $sql_add);
		
		$sql = "
			SELECT DISTINCT
				*			 
			FROM
				faturamento_venda_view
			$sql_add
			ORDER BY 
				cliente
			LIMIT $numero_resultados
			";

		$rs = pg_query($this->conn, $sql);
		
		$resultado = array();
		$resultado['contratos'] = array();
		$valor_total = 0;
		
		/*
		 * Populando o array que será usado em formato json
		 * pelo ajax para popular a tabela
		 * */		
		for($i = 0; $i < pg_num_rows($rs); $i++){
			
			$valor_total += pg_fetch_result($rs, $i, 'valor');
			
			$data_cadastro = pg_fetch_result($rs, $i, 'data_cadastro');
			$data_faturamento = pg_fetch_result($rs, $i, 'data_faturamento');
			$obrigacao_financeira = pg_fetch_result($rs, $i, 'obrigacao_financeira');
			$valor = pg_fetch_result($rs, $i, 'valor');
			$obroid = pg_fetch_result($rs, $i, 'obroid');
			$veiculo = pg_fetch_result($rs, $i, 'veiculo');
			$produto = pg_fetch_result($rs, $i, 'produto');
			$numero_parcelas = pg_fetch_result($rs, $i, 'numero_parcelas');
			$contrato_servico_id = pg_fetch_result($rs, $i, 'contrato_servico_id');			
			
			$resultado['contratos'][$i]['clioid'] 				= utf8_encode(pg_fetch_result($rs, $i, 'clioid'));
			$resultado['contratos'][$i]['contrato'] 			= utf8_encode(pg_fetch_result($rs, $i, 'contrato'));
			$resultado['contratos'][$i]['cliente'] 				= utf8_encode(pg_fetch_result($rs, $i, 'cliente'));
			$resultado['contratos'][$i]['data_cadastro'] 		= is_null($data_cadastro) ? '' : utf8_encode(date('d/m/Y', strtotime($data_cadastro)));
			$resultado['contratos'][$i]['data_faturamento'] 	= is_null($data_faturamento) ? '' : utf8_encode($data_faturamento);
			$resultado['contratos'][$i]['valor'] 				= utf8_encode($valor);
			$resultado['contratos'][$i]['valor_formatado'] 		= utf8_encode(number_format($valor, 2, ",", "."));// apenas para apresentar na tela, não usar para cálculos
			$resultado['contratos'][$i]['obrigacao_financeira'] = is_null($obrigacao_financeira) ? '' : utf8_encode($obrigacao_financeira);
			$resultado['contratos'][$i]['dia_vencimento'] 		= utf8_encode(pg_fetch_result($rs, $i, 'dia_vencimento'));
			$resultado['contratos'][$i]['obroid'] 				= is_null($obroid) ? 0 : utf8_encode($obroid);
			$resultado['contratos'][$i]['veiculo'] 				= is_null($veiculo) ? 0 : utf8_encode($veiculo);
			$resultado['contratos'][$i]['produto'] 				= is_null($produto) ? 'NULL' : utf8_encode($produto);
			$resultado['contratos'][$i]['numero_parcelas'] 		= utf8_encode($numero_parcelas);
			$resultado['contratos'][$i]['id_nota'] 				= utf8_encode(pg_fetch_result($rs, $i, 'id_nota'));
			$resultado['contratos'][$i]['tipo'] 				= utf8_encode(pg_fetch_result($rs, $i, 'tipo'));
			$resultado['contratos'][$i]['contrato_servico_id'] 	= is_null($contrato_servico_id) ? 'NULL' : utf8_encode($contrato_servico_id);
			$resultado['contratos'][$i]['termo_aditivo_id'] 	= utf8_encode(pg_fetch_result($rs, $i, 'termo_aditivo_id'));
		}
		
		$resultado['valor_total'] = utf8_encode('R$'.number_format($valor_total, 2, ',', '.'));
		$resultado['total_registros'] = utf8_encode(count($resultado['contratos']) . ' Registro(s) encontrado(s)');
		$resultado['faturado'] = utf8_encode($is_faturado);
		
		return $resultado;
		
	}
	
	/*
	 * @author	Ricardo Marangoni da Mota
	 * @email	ricardo.mota@meta.com.br
	 * @param	$itens_a_faturar - Array com os itens a serem faturados
	 * @return	Mensagem de sucesso ou erro em formato JSON
	 * */
	public function faturar($itens_a_faturar) {
		
		try{
		
			pg_query($this->conn, 'BEGIN');
			
			/*
			 * Para realizar o faturamento temos que seguir os passos:
			 * 1 - Inserir a nota fiscal
			 * 2 - Inserir os itens da nota na nota_fiscal_item_venda
			 * 3 - Inserir o título
			 * */
			
			/*
			 * Inserindo as Notas Fiscais
			 * Os itens 2 e 3 listados acima acontecem
			 * dentro do método insertNFs
			 *  
			 * */
			$return = $this->insertNFs($itens_a_faturar);
			
			if ($return['error']){
				
				throw new Exception($return['message']);
				
			}
			
			pg_query($this->conn, 'COMMIT');
			
			echo json_encode($return);
			
		}catch(Exception $e){
			
			echo json_encode($return);
			
			pg_query($this->conn, 'ROLLBACK');
			
		}
	}
	
	/*
	 * @author	Ricardo Marangoni da Mota
	 * @email	ricardo.mota@meta.com.br
	 * @param	$itens_a_reenviar_xml - Array com os itens a serem reenviados
	 * @return	Mensagem de sucesso ou erro em formato JSON
	 * */
	private function reSendNFs($itens_a_reenviar_xml) {
		
		$notas = array();
		
		foreach ($itens_a_reenviar_xml as $cliente) {
		
			/*
			 * Cada lista de itens pertence a um
			 * cliente, o qual está referenciado no array $cliente
			 * */
			$cliente_id = $cliente['cliente_id'];
			
			foreach($cliente['itens'] as $grupo_parcela) {
				
				$nota_id = $grupo_parcela[0]->id_nota;
				
				$this->updateDataFaturamentoNF($nota_id);
				
				array_push($notas, array(
						'nota_id' 	=> $nota_id,
						'cliente_id' => $cliente_id
				));
				
			}
		}
		
		return $this->generateXML($notas);
		
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$nota_id - ID da NF
	 * @description	Atualiza a data de faturamento da NF
	 * */
	private function updateDataFaturamentoNF($nota_id) {
		
		$sql = "
			UPDATE
				nota_fiscal_venda
			SET
				nfldt_faturamento = NOW()
			WHERE 
				nfloid = $nota_id
		";
		
		pg_query($this->conn, $sql);
		
	}
	
	/*
	 * @author	Ricardo Marangoni da Mota
	 * @email	ricardo.mota@meta.com.br
	 * @param	$itens_a_faturar - Array com os itens a serem faturados
	 * @return	Mensagem de sucesso ou erro em formato JSON
	 * */
	private function insertNFs($itens_a_faturar) {
		
		$notas = array();
		
		/*
		 * Cada item pertence a um cliente, e para cada
		 * cliente geramos uma nota fiscal
		 * */
		foreach ($itens_a_faturar as $cliente) {
			
			/*
			 * Cada lista de itens pertence a um
			 * cliente, o qual está referenciado no array $cliente
			 * */
			$cliente_id = $cliente['cliente_id'];
						
			$dia_vencimento  = $cliente['dia_vencimento'];			
			
			foreach($cliente['itens'] as $grupo_parcela) {
															
				$sql = "
					INSERT INTO
						nota_fiscal_venda
					(
						nfldt_nota, 
						nflnatureza, 
						nfltransporte, 
						nflclioid, 
						nflno_numero, 
						nflserie, 
						nflvl_total, 
						nfldt_referencia, 
						nflobs,
						nflusuoid,
						nflnota_ant,
						nflempenho,
						nfldt_faturamento
					)
					VALUES
					(
						now(),
						'VENDA DE EQUIPAMENTOS',
						'RODOVIARIO',
						$cliente_id,
						NULL,
						NULL,
						0,
						'".date( "Y" ) . '-' . date( "m" ) .'-01' . "',
						'',
						".$_SESSION["usuario"]["oid"].",
						NULL,
						NULL,
						now()
					)
					
					RETURNING nfloid";
				
				$sql_nfloid = pg_query($this->conn, $sql);
				
				$nota_id = pg_fetch_result($sql_nfloid, 0, 'nfloid');
				
				$numero_parcelas = $grupo_parcela[0]->numero_parcelas;
																
				/*
				 * Precisamos do valor total da nota, então
				 * somamos o valor de cada item
				 * */
				$valor_total = 0;
				
				/*
				 * Cada item tem uma sequencia de inserção
				 * para a mesma nota, então enumeramos cada um
				 * em ordem crescente
				 * */
				$item_sequence = 1;
				
				$this->valor_itens = 0;
				
				foreach($grupo_parcela as $item) {
					
					/*
					 * Se só houver 1 item e este for um aditivo
					 * então pegamos o parcelamento da tabela de aditivo
					 * e não usamos o parcelamento do contrato
					 * */
					$numero_parcelas = (count($grupo_parcela) == 1 && $item->tipo == 'ADITIVO') ? $this->getParcelasAditivo($item->termo_aditivo_id) : $numero_parcelas;
					
					$valor_total += $item->valor;					
					
					$this->valor_item_principal = 0;
					
					if ($retorno = $this->saveNFItem($item, $nota_id, $item_sequence)){
						return $retorno;
					}
					
					if ($this->valor_item_principal != $item->valor){
						
						/*
						 * Se houver diferença de centavos após a distribuição 
						 * dos valores entre os itens da classe principal, corrige 
						 * o valor do último item somando a diferença
						 */
						$diferenca = round($item->valor - $this->valor_item_principal, 2);
						$this->fixValueItem($nota_id, $item_sequence, $diferenca);
						$this->valor_itens += $diferenca;
					}
					
					// Aqui o incremento do item
					$item_sequence++;
				
				}
				
				$this->valor_itens = round($this->valor_itens, 2);
				$valor_total = round($valor_total, 2);
				
				if ($this->valor_itens != $valor_total){
					
					/*
					 * Valida se o valor da nota coincide com o valor dos itens após o ajuste de centavaos. 
					 */
					return array("error" => true, "message" => utf8_encode("O valor da soma dos itens é diferente do valor da nota."));
				}
				
				/*
				 * Inserindo os títulos referentes a NF				 *
				 * */
				$this->insertTitulos($numero_parcelas, $dia_vencimento, $cliente_id, $valor_total);
				
				/*
				 * Atualiza a nota com o valor total do itens
				 * */
				$this->updateValorNF($nota_id, $valor_total);
				
			}
		}
		
		return array("error" => false);
		
	}
	
	public function getParcelasAditivo($termo_aditivo_id) {
		
		$sql = "SELECT 
					cpvparcela 
				FROM 
					cond_pgto_venda 
				INNER JOIN 
					termo_aditivo ON tadcpvoid = cpvoid
				WHERE 
					tadoid = $termo_aditivo_id
				";
		
		$rs = pg_query($this->conn, $sql);
		$numero_parcelas = 1;
		
		if(pg_num_rows($rs) > 0) {
			
			$numero_parcelas = pg_fetch_result($rs, 0, 'cpvparcela');
			
		}
		
		return $numero_parcelas;
		
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$nota_id - ID da NF
	 * @param		$valor_total - Valor total da nota
	 * @description	Atualiza o valor total da nota após inserir os itens
	 * */
	private function updateValorNF($nota_id, $valor_total) {
		
		$sql = "
			UPDATE 
				nota_fiscal_venda
			SET
				nflvl_total = $valor_total
			WHERE
				nfloid = $nota_id
		";
		
		pg_query($this->conn, $sql);
		
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$item - Objeto json com os valor do item
	 * @param		$nota_id - ID da NF
	 * @param		$item_sequence - Uma nota tem vários itens e cada um tem uma sequencia de inserção	 
	 * */
	private function saveNFItem($item, $nota_id, &$item_sequence) {
		/*
		 * Busca valor da obrigação financeira do produto principal.
		 */ 		
		$obrigacao_financeira = $this->getObrigacaoFinanceiraById($item->obroid);
		$total = $obrigacao_financeira['obrvl_obrigacao'];
		
		/*
		 * Busca valor da obrigação financeira do kit básico do produto principal.
		 */
		if(!empty($obrigacao_financeira) && $obrigacao_financeira['obroftoid'] == 12) {
			
			$basic_itens = $this->getBasicItens($item->obroid);
			
			if (!empty($basic_itens)){
				
				foreach($basic_itens as $basic_item){
					
					if ((int)$basic_item['obrvl_obrigacao'] == 0){
						/*
						 * Exceção para quando o valor da obrigação financeira estiver zerado.
						 */
						return array("error" => true, "message" => utf8_encode("A obrigação financeira ".$basic_item['obrobrigacao']." possui o valor R$ 0,00, por favor atualize o seu valor no módulo Finanças >> Obrigação Financeira."));	
					}
										
					if (empty($basic_item['obrprdoid'])){
						/*
						 * Exceção para quando uma obrigação financeira não possuir um produto.
						 */
						return array("error" => true, "message" => utf8_encode("A obrigação financeira ".$basic_item['obrobrigacao']." não possui um produto atrelado a ela, por favor atualize no módulo Finanças >> Obrigação Financeira."));	
					
					}
					
					$total += $basic_item['obrvl_obrigacao'];
				
				}
				
			}	
					
		}
		
		/*
		 * Calcula o percentual de desconto a ser aplicado em cada item		
		 * Fórmula:
		 * Perc_Desc = (100-(ValorNegociado*100)/SomaDosItensDaClasse)/100
		 */
		$percentual_desconto = (100 - ($item->valor * 100) / $total ) / 100; 
		
		/*
		 * Aplica o percentual de desconto em cada item que compõem a classe
		 */ 
		$obrigacao_financeira['obrvl_obrigacao'] = round(($obrigacao_financeira['obrvl_obrigacao'] - ( $obrigacao_financeira['obrvl_obrigacao'] * $percentual_desconto )), 2);               
		
		/*
		 * Váriaveis de controle
		 * $this->valor_itens: Armazena a soma dos valores dos itens da nota
		 * $this->valor_item_principal: Armazena a soma dos valores dos itens de uma classe
		 */
		$this->valor_itens += $obrigacao_financeira['obrvl_obrigacao'];
		$this->valor_item_principal += $obrigacao_financeira['obrvl_obrigacao'];
		
		$sql = "
			INSERT INTO
				nota_fiscal_item_venda
			(
				nfinfloid,
				nfino_numero,
				nfiserie,
				nficonoid,
				nfiobroid,
				nfids_item,
				nfivl_item,
				nfidt_referencia,
				nfino_item,
				nfidesconto,
				nfinota_ant,
				nfiordoid,
				nfidt_inclusao,
				nfiequoid,
				nfiveioid,
				nfiprdoid,
				nficonsoid				
			)
			VALUES
			(
				$nota_id,
				NULL,
				NULL,
				$item->contrato,
				$item->obroid,
				'" . utf8_decode($item->obrigacao_financeira) . "',
				" . $obrigacao_financeira['obrvl_obrigacao'] . ",				
				'" . date( "Y" ) . '-' . date( "m" ) .'-01' . "',
				'" . $item_sequence . "',
				0,
				NULL,
				0,
				now(),				
				NULL, -- SEM EQUIPAMENTO, POIS, NA REVENDA O EQUIPAMENTO SERÁ INSTALADO SOMENTE APÓS O PGTO 
				$item->veiculo,
				$item->produto,
				$item->contrato_servico_id
			)";

		pg_query($this->conn, $sql);
		
		/*
		 * Após inserir o item devemos verificar se o item que
		 * está sendo inserido na tabela nota_fiscal_item_venda 
		 * no momento é uma obrigação financeira do tipo 
		 * "Venda produto básico" (obroftoid = 12). 
		 * Caso seja deverá buscar todas as obrigações financeiras 
		 * do tipo "Locação/Revenda c/ baixa de estoque por 
		 * serial" (obroftoid = 3) que a compõe e inserir estas 
		 * obrigações na tabela nota_fiscal_item_vendas.
		 * */
		$this->insertBasicProducts($item, $nota_id, $item_sequence, $percentual_desconto);
		
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$item - Objeto json com os valor do item
	 * @param		$nota_id - ID da NF
	 * @param		$item_sequence - Uma nota tem vários itens e cada um tem uma sequencia de inserção
	 * */
	private function insertBasicProducts($item, $nota_id, &$item_sequence, $percentual_desconto) {
		$obrigacao_financeira = $this->getObrigacaoFinanceiraById($item->obroid);
		
		if(!empty($obrigacao_financeira) && $obrigacao_financeira['obroftoid'] == 12) {

			$basic_itens = $this->getBasicItens($item->obroid);
			
			foreach($basic_itens as $basic_item) {
				
				$item_sequence++;
				/*
				* Aplica o percentual de desconto em cada item que compõem a classe
				*/
				$basic_item['obrvl_obrigacao'] =  round(($basic_item['obrvl_obrigacao'] - ( $basic_item['obrvl_obrigacao'] * $percentual_desconto)), 2) ;
				
				/*
				* Váriaveis de controle
				* $this->valor_itens: Armazena a soma dos valores dos itens da nota
				* $this->valor_item_principal: Armazena a soma dos valores dos itens de uma classe
				*/
				$this->valor_itens += $basic_item['obrvl_obrigacao'];
				$this->valor_item_principal += $basic_item['obrvl_obrigacao'];
				
				$sql = "
					INSERT INTO
						nota_fiscal_item_venda
					(
						nfinfloid,
						nfino_numero,
						nfiserie,
						nficonoid,
						nfiobroid,
						nfids_item,
						nfivl_item,
						nfidt_referencia,
						nfino_item,
						nfidesconto,
						nfinota_ant,
						nfiordoid,
						nfidt_inclusao,
						nfiequoid,
						nfiveioid,
						nfiprdoid
					)
					VALUES
					(
						$nota_id,
						NULL,
						NULL,
						$item->contrato,
						" . $basic_item['obroid'] . ",
						'" . utf8_decode($basic_item['obrobrigacao']) . "',
						" . $basic_item['obrvl_obrigacao'] . ",
						'" . date( "Y" ) . '-' . date( "m" ) .'-01' . "',
						'" . $item_sequence . "',
						0,
						NULL,
						0,
						now(),
						NULL, -- SEM EQUIPAMENTO, POIS, NA REVENDA O EQUIPAMENTO SERÁ INSTALADO SOMENTE APÓS O PGTO
						$item->veiculo,
						" . $basic_item['obrprdoid'] . "
					)";
				
				pg_query($this->conn, $sql);
			}
			
		}
		
	}
	
	/*
	* @author		Willian Ouchi
	* @email		willian.ouchi@meta.com.br
	* @param		$nota_id - ID da nota fiscal
	* @param		$item_sequence - Número sequencial do item da nota
	* @param		$diferenca - Valor da diferença  
	* */
	public function fixValueItem($nota_id, $item_sequence, $diferenca) {
		$sql = "
			UPDATE 
				nota_fiscal_item_venda
			SET
				nfivl_item = nfivl_item + ".$diferenca."
			WHERE
				nfinfloid = ".$nota_id."
				AND nfino_item = ".$item_sequence."
		";	
		$rs = pg_query($this->conn, $sql);
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br	 
	 * @param		$obrigacao_financeira_id - ID da obrigacao financeira	
	 * @return		Array - Dados da obrigacao financeira	
	 * */
	public function getObrigacaoFinanceiraById($obrigacao_financeira_id) {
		$sql = "
			SELECT 
				*
			FROM
				obrigacao_financeira
			WHERE
				obroid = $obrigacao_financeira_id;
		";
		
		$rs = pg_query($this->conn, $sql);
		
		return pg_fetch_assoc($rs);
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$obrigacao_financeira_id - ID da obrigacao financeira
	 * @return		Array - Itens basicos atrelados à obrigacao financeira
	 * */
	public function getBasicItens($obrigacao_financeira_id) {
		$sql = "
			SELECT 
				of.* 
			FROM 
				obrigacao_financeira_item AS ofi
			INNER JOIN 
				obrigacao_financeira AS of ON ofi.ofiservico = of.obroid 
			WHERE
				ofi.ofiobroid = $obrigacao_financeira_id 
			AND 
				ofi.ofiexclusao IS NULL
			AND 
				of.obroftoid = 3";
		
		$rs = pg_query($this->conn, $sql);
		$itens = array();
		
		if(pg_num_rows($rs) > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$itens[$i]['obroid']			= pg_fetch_result($rs, $i, 'obroid');
				$itens[$i]['obrobrigacao']		= pg_fetch_result($rs, $i, 'obrobrigacao');
				$itens[$i]['obrprdoid']			= pg_fetch_result($rs, $i, 'obrprdoid');
				$itens[$i]['obrvl_obrigacao']	= pg_fetch_result($rs, $i, 'obrvl_obrigacao');
			}
		}		
		
		return $itens;
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$numero_parcelas - Número do parcelamento da nota
	 * @param		$dia_vencimento - DIA do vencimento das parcelas
	 * @param		$cliente_id - ID do cliente
	 * @param		$valor_total - Valor total da nota
	 * @description	Cada titulo é uma parcela da nota, então inserimos um títulos para cada percela
	 * */
	private function insertTitulos($numero_parcelas, $dia_vencimento, $cliente_id, $valor_total) {
		
		$finFaturamentoCartaoCredito = new FinFaturamentoCartaoCredito();
		
		$need_round = explode(".", ($valor_total / $numero_parcelas));
		
		/*
		 * Pegamos os decimais e verificamos se a divisão das parcelas foi exata
		 * exemplo 99.99, caso não seja (99.996 por exemplo) arredondamos para 100
		 * e tiramos os centavos na última parcela
		 * */
		$need_round = $need_round[1];
		
		/*
		 * Nota: ceil arredonda para cima 39.986 -> 40
		 * Na última parcela é removido o acúmulo.
		 * Isso é necessário para regulazirar as parcelas
		 * */
		$valor_parcela = (strlen($need_round) > 2) ? ceil($valor_total / $numero_parcelas) : $valor_total / $numero_parcelas;
		
		//se a forma de pagamento do cliente for cartão, então retorna os dados do cartão.
		$dadosCartao = $finFaturamentoCartaoCredito->getDadosCartao($cliente_id);
		
		$valor_acumulado = 0;
		
		for($i = 1; $i <= $numero_parcelas; $i++) {		
		
			/*
			 * Se for a última parcela remove o acúmulo
			 * */			
			$parcela = ($i == $numero_parcelas) ? $valor_total - $valor_acumulado : $valor_parcela;
			
			//seta data de pagamento e forma de cobrança nos títulos se for cartão de crédito
			if(!empty($dadosCartao)){
					
				//se não for a primeira parcela, decrementa 1 do $i para pegar o número do próximo mês correto
				$proximo_mes = ($i != 1) ? $i - 1 : $i ;
								
				# A primeira parcela do cartão é sempre para a data atual, as demais são para daqui 1 mês 
				$data_vencimento = ($i == 1) ? date('Y-m-d') : date("Y-m-d", strtotime("+ $proximo_mes month"));
				
				$formaPagamento = $dadosCartao['forma_cobranca']; //Cartões de crédito
		
			}else{
				
			
			/*
				 * Verifica se o Tipo de contrato está com parâmetro que gera O.S. Intalação :  'Sim' ou  'Indefinido'
				 * e se no campo do pré-cadastro  está com parâmetro  que gera O.S. Intalação: 'Sim'.
				 * 
				 */
													
				
				$sql = "
					SELECT
						tcptpcoid,
						tcpgera_os_instalacao
					FROM
						tipo_contrato
						INNER JOIN tipo_contrato_parametrizacao on tcptpcoid = tpcoid
						INNER JOIN proposta on tpcoid =prptpcoid
						INNER JOIN contrato on connumero = prptermo
					WHERE
						connumero = (
						SELECT
							connumero
						FROM
							nota_fiscal_venda
						INNER JOIN
							nota_fiscal_item_venda ON nfinfloid = nfloid
						INNER JOIN
							contrato ON nficonoid = connumero
                        WHERE 
                            nflclioid = " . $cliente_id . "
						ORDER BY
							nfloid DESC limit 1
						)
					AND
					tcpgera_os_instalacao = 'S'
					OR
					(tcpgera_os_instalacao = 'I' AND prpgera_os_instalacao = 't')" ;
					
					
					$rs = pg_query($this->conn, $sql);
				
				if(pg_num_rows($rs) > 0 ) {					
					
					if($dia_vencimento - (int)date('d') <= 10) {
                        $proximo_mes = $i;
                        
                        $date = new DateTime( date("Y-m-$dia_vencimento") );
                        $date->modify( "+ $i month" );
                        $data_vencimento = $date->format( 'Y-m-d' );
                        
					} else {
                        $proximo_mes = $i - 1;
                        
                        if($i == 1){
                        	 
                        	$data_vencimento = date("Y-m-$dia_vencimento");
                        	 
                        } else {
                        	 
                        	$date = new DateTime( date("Y-m-$dia_vencimento") );
                        	$date->modify( "+ $proximo_mes month" );
                        	$data_vencimento = $date->format( 'Y-m-d' );
                        	 
					}
					}
                    
				} else {
                    
					/*
                     * Se 10 dias após a inserção cair em outra mês, exemplo:
                     * A inserção é dia 20/06 e o mês vai até 30, a primeira parcela será
                     * dia 05/07 e as outras sempre 1 mês depois								
                     *
					* */
                    $proximo_mes = ((int)date('m') < (int)date('m', strtotime("+ 10 day"))) ? $i : $i - 1;
                    
                    if($i == 1){
                    	
                    	$date = new DateTime( date("Y-m-$dia_vencimento") );
                    	$date->modify( "+ 10 day" );
                    	$data_vencimento = $date->format( 'Y-m-d' );
                    	
                    } else {
                    	
                    	$date = new DateTime( date("Y-m-$dia_vencimento") );
                    	$date->modify( "+ $proximo_mes month" );
                    	$data_vencimento = $date->format( 'Y-m-d' );
                    	
				}
				}
				
				$formaPagamento = 1; //Boleto
			}
			
			$sql = "  INSERT INTO 
					titulo_venda
				(
					titnfloid,
					titdt_referencia,
					titvl_titulo_venda,
					titdt_vencimento,
					titno_parcela,
					titformacobranca,
					titclioid
				)
				VALUES 
				(
					( SELECT MAX(nfloid) FROM nota_fiscal_venda ),
					now(),
					'" . $parcela . "',
					'" . $data_vencimento . "',
					".$i.",
							$formaPagamento,
					" . $cliente_id . "
				)";
			
			pg_query($this->conn, $sql);
			
			$valor_acumulado += $valor_parcela;
			
		}
		
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$notas - Array com o id da nota e do cliente	
	 * @description	Gera o XML (STRING NÃO ARQUIVO) das notas fiscais para impressão
	 * */
	public function generateXML($notas) {
		
		$total_titulos = 0;
		$contador_notas = 1;
		$tpcoidContratoVivo = 844;// Por convenção deve ser ID FIXO para identificar se o tipo é VIVO
		
		$codigo_cedente = 3471241;
		
		$body = "";
		
		foreach($notas as $nota) {
			
			$cliente = $this->getClientById($nota['cliente_id']);
									
			$titulos = $this->getTitulosByNF($nota['nota_id']);
			
			$conno_tipo_ID = $this->getTipoContatoByNF($nota['nota_id']);
			
			$imagem = '';
			$conno_tipo_ID = (int) $conno_tipo_ID;
			if($conno_tipo_ID == $tpcoidContratoVivo){
				$imagem = 'welcome-vivo-boleto';
			}else{
				$imagem = 'welcome-venda';
			}
			
			$body .= '
				<row id="'.$contador_notas.'">
					<int name="NumeroNF"></int>
					<int name="QtdeBoleto">'.count($titulos).'</int>
					<str name="email">'.$cliente['email'].'</str>
					<str name="imagem">'.$imagem.'</str>
					<arr name="titulos">';
			
			$contador_notas++;
			
			$total_titulos += count($titulos);
			
			foreach($titulos as $titulo) {				
				
				$nosso_numero 	= montaNossoNumeroHSBC($titulo['titulo_id'], date('d/m/Y', strtotime($titulo['data_vencimento'])), $codigo_cedente);
				$linhadig 		= montaLinhaDigitavelCodBarrasHSBC($titulo['titulo_id'], date('d/m/Y', strtotime($titulo['data_vencimento'])), $codigo_cedente, $titulo['valor_parcela'], 'linha_digitavel');
				$codbarras 		= montaLinhaDigitavelCodBarrasHSBC($titulo['titulo_id'], date('d/m/Y', strtotime($titulo['data_vencimento'])), $codigo_cedente, $titulo['valor_parcela'], 'codigo_barras');
			
				$body .= '<row>
							<str name="NomeSacado">'.$cliente['nome'].'</str>
							<str name="Endereco">'.$cliente['endereco'].'</str>
							<str name="Bairro">'.$cliente['bairro'].'</str>
							<str name="Cidade">'.$cliente['cidade'].'</str>
							<str name="CEP">'.$cliente['cep'].'</str>
							<str name="CNPJ">'.$cliente['cpf_cnpj'].'</str>
							<date name="DataEmissao">'.$titulo['data_emissao'].'</date>
							<date name="Vencimento">'.$titulo['data_vencimento'].'</date>
							<str name="NossoNumero">'.$nosso_numero.'</str>
							<str name="NumeroDocumento">'.$titulo['titulo_id'].'</str>
							<dec name="ValorPagar">'.$titulo['valor_parcela_formatado'].'</dec>
							<dec name="ValorFatura">'.$titulo['valor_parcela_formatado'].'</dec>
							<dec name="ValorTitulo">'.$titulo['valor_parcela_formatado'].'</dec>
							<dec name="ValorDesconto">'.$titulo['desconto'].'</dec>
							<str name="LinhaDigitavel">'.$linhadig.'</str>
							<str name="CodigoBarras">'.$codbarras.'</str>
							<int nome="parcela">'.$titulo['numero_parcela'].'</int>
							<str name="Boleto">SIM</str>
							<str name="FormaPagamento"> </str>
							<str name="Mensagem1">NAO RECEBER APOS 30 DIAS DO VENCIMENTO</str>
							<str name="Mensagem2">Apos o vencimento cobrar:</str>
							<str name="Mensagem3">Multa de 2%</str>
							<str name="Mensagem4">Juros de 0,033% ao dia</str>
							<str name="Mensagem5"> </str>
						</row>';
				
				
				//ATUALIZA O titulo com o nosso_numero gerado
				$this->updateNossoNumero($nosso_numero, $titulo['titulo_id']);
				
			}
			
			$body .='
					</arr>
				</row>';
				
            //Envia para o cliente com os boletos por email
            $this->sendEmailBoletoCliente($nota, $titulos);
				
		}	
		
		$header = '
			<?xml version="1.0" encoding="ISO-8859-1" ?>
			<job cliente="dd" template="dd" dataGeracao="'.date("Y-m-d").'" numRows="'.$total_titulos.'">';
		
		$footer = '</job>';
		
		$xml = $header.$body.$footer;

		return $this->generateXMLArchive(trim($xml));
		
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$nosso_numero - Número gerado pela função montaNossoNumeroHSBC
	 * @param		$titulo_id - ID do Título
	 * @description	Quando o XML é gerado tbm geramos o "nosso_numero" e temos que atualizar o titulo
	 * */
	private function updateNossoNumero($nosso_numero, $titulo_id) {
		$sql = "
			UPDATE 
				titulo
			SET 
				titnumero_registro_banco = $nosso_numero 
			WHERE 
				titoid = $titulo_id";
		
		pg_query($this->conn, $sql);

		$sql2 = "
			UPDATE 
				titulo_venda
			SET 
				titnumero_registro_banco = $nosso_numero 
			WHERE 
				titoid = $titulo_id";
		
		pg_query($this->conn, $sql2);
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$cliente_id - ID do cliente
	 * @description	Retorna dados do cliente de acordo com o seu ID
	 * */
	public function getClientById($cliente_id) {
		$sql = "
			SELECT
			    clitipo,
				clinome,
				CASE WHEN 
					clitipo = 'F'
				THEN 
					(COALESCE(clirua_res,'')||' '||COALESCE(clino_res::text,'')||' '||COALESCE(clicompl_res,'')||' '||COALESCE(clibairro_res,''))
				ELSE 
					COALESCE(clirua_com,'')||' '||COALESCE(clino_com::text,'')||' '||COALESCE(clicompl_com,'')||' '||COALESCE(clibairro_com,'')
				END AS endereco,
				CASE WHEN 
					clitipo = 'F' 
				THEN 
					clibairro_res 
				ELSE 
					clibairro_com 
				END as bairro,
				CASE WHEN 
					clitipo = 'F' 
				THEN 
					clicidade_res 
				ELSE 
					clicidade_com 
				END AS cidade,
				CASE WHEN 
					clitipo = 'F' 
				THEN 
					clino_cep_res 
				ELSE 
					clino_cep_com 
				END AS cep,
				CASE WHEN 
					clitipo = 'F' 
				THEN 
					clino_cpf 
				ELSE 
					clino_cgc 
				END AS cpf_cnpj,
				CASE 
					WHEN cliemail_nfe similar to '%@%.%' THEN cliemail_nfe
					WHEN cliemail similar to '%@%.%' THEN cliemail
					ELSE null
				END AS email				
			FROM 
				clientes
			WHERE 
				clioid = $cliente_id";
		
		$rs = pg_query($this->conn, $sql);
		
		$cliente = array();		
		
		if(pg_num_rows($rs) > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$cliente['clitipo']  	= pg_fetch_result($rs, $i, 'clitipo');
				$cliente['nome'] 	 	= pg_fetch_result($rs, $i, 'clinome');
				$cliente['endereco'] 	= pg_fetch_result($rs, $i, 'endereco');
				$cliente['bairro'] 		= pg_fetch_result($rs, $i, 'bairro');
				$cliente['cidade'] 		= pg_fetch_result($rs, $i, 'cidade');
				$cliente['cep'] 		= pg_fetch_result($rs, $i, 'cep');
				$cliente['cpf_cnpj'] 	= pg_fetch_result($rs, $i, 'cpf_cnpj');
				$cliente['email'] 		= strtolower(pg_fetch_result($rs, $i, 'email'));
			}
		}
		
		return $cliente;
		
	}
	
	/**
	 * Retorna email do usuário que efetuou a venda  
	 * 
	 * @param $nota_id
	 * @return object
	 */
	public function getDadosUsuarioNota($nota_id) {
	
		try{
			$sql = "SELECT nm_usuario as nomeUsuario, 
						   usuemail as emailUsuario
					  FROM usuarios
				INNER JOIN nota_fiscal_venda ON nflusuoid = cd_usuario
				  	 WHERE nfloid = $nota_id
					 LIMIT 1 ";
			
			if (!$rs = pg_query($this->conn, $sql)) {
				throw new Exception('ERRO: <b>Falha ao buscar dados do usuario.</b>');
			}
		
			return pg_fetch_object($rs);
		
		}catch (Exception $e){
			return $e->getMessage();
		}
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$nota_id - ID da NF
	 * @description	Retorna dados do título de acordo com o ID da NF
	 * */
	public function getTitulosByNF($nota_id) {
		
		$sql = "
			SELECT 
				titoid,
				titdt_referencia,
				titdt_vencimento,
				titno_parcela,
				titvl_titulo_venda,
				titvl_desconto
			FROM 
				titulo_venda
			WHERE 
				titnfloid = $nota_id
			ORDER BY titno_parcela
		";
		
		$rs = pg_query($this->conn, $sql);
		
		$titulos = array();
		
		if(pg_num_rows($rs) > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$titulos[$i]['titulo_id'] 					= pg_fetch_result($rs, $i, 'titoid');
				$titulos[$i]['data_emissao'] 				= pg_fetch_result($rs, $i, 'titdt_referencia');
				$titulos[$i]['data_vencimento'] 			= pg_fetch_result($rs, $i, 'titdt_vencimento');
				$titulos[$i]['numero_parcela'] 				= pg_fetch_result($rs, $i, 'titno_parcela');
				$titulos[$i]['valor_parcela'] 				= pg_fetch_result($rs, $i, 'titvl_titulo_venda');
				$titulos[$i]['valor_parcela_formatado'] 	= number_format(pg_fetch_result($rs, $i, 'titvl_titulo_venda'), 2, ",", ".");
				$titulos[$i]['desconto'] 					= pg_fetch_result($rs, $i, 'titvl_desconto');
			}
		}
		
		return $titulos;
		
	}
	
	/*
	* @author		Jorge A. D. Kautzmann
	* @email		jorge.kautzmann@sascar.com.br
	* @param		$nota_id - ID da NF
	* @description	Retorna conno_tipo da tabela contrato com base em 
	* */
	private function getTipoContatoByNF($nota_id) {
		$nflno_numero = 0;
		$nflserie = '';
		$nficonoid = 0;
		$conno_tipo = 0;
		// Pegar número e séria da NF
		$sql = " SELECT nflno_numero, nflserie FROM nota_fiscal WHERE nfloid = $nota_id";
		$rs = pg_query($this->conn, $sql);
		if(pg_num_rows($rs) > 0) {
			$nflno_numero = pg_fetch_result($rs, 0, 'nflno_numero');
			$nflserie = pg_fetch_result($rs, 0, 'nflserie');
		}
		// Pegar ID contrato nota_fiscal_item->nficonoid	
		$sql = " SELECT nficonoid FROM nota_fiscal_item WHERE nfino_numero = $nflno_numero AND nfiserie = '$nflserie'";
		$rs = pg_query($this->conn, $sql);
		if(pg_num_rows($rs) > 0) {
			$nficonoid = pg_fetch_result($rs, 0, 'nficonoid');
		}
		// Pegar ID contrato nota_fiscal_item->nficonoid
		$sql = " SELECT conno_tipo FROM contrato WHERE connumero = $nficonoid";
		$rs = pg_query($this->conn, $sql);
		if(pg_num_rows($rs) > 0) {
			$conno_tipo = pg_fetch_result($rs, 0, 'conno_tipo');
		}
		return $conno_tipo;
	}
	
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$xml - String do XML
	 * @description	Gera o XML (ARQUIVO FÍSICO .zip) das notas fiscais para impressão
	 * */
	private function generateXMLArchive($xml) {
		
		try {
		
			/*
			 * GERA O ARQUIVO .XML E COMPACTA ELE PARA .ZIP
			 */
			if(!file_exists('/var/www/boletos_venda_equipamento/')){
				mkdir('/var/www/boletos_venda_equipamento',0777);
			}
			
			$file_name = 'boletos_venda_equipamento_'.date("d_m_Y").'.xml';
			$file_path = '/var/www/boletos_venda_equipamento/'.$file_name;
					
			//verifica se o arquivo existe, caso exista, apaga o atual e cria o novo (sobreescreve o arquivo)
			if(file_exists($file_path)){
				unlink($file_path);		//apaga o arquivo atual
			
				//apaga o temporario gerado
				if(file_exists($file_path.'~')){
					unlink($file_path.'~');
				}
			
			}
			
			$fp = fopen($file_path, "w");
			fwrite($fp, $xml);
			fclose($fp);
			
			//GERA O ZIP DO ARQUIVO
			if(class_exists('ZipArchive')) {
				$zip = new ZipArchive();
				$zip->open($file_path.'.zip', ZIPARCHIVE::CREATE);
				$zip->addFile($file_path, $file_name);
				$zip->close();
			} else {
				$exec_zip = shell_exec("cd /var/www/boletos_venda_equipamento/ && zip {$file_name}.zip {$file_name}");
			}
					
			//APAGA O ARQUIVO .xml E DEIXA APENAS O ZIP NA PASTA
			if(file_exists($file_path)){
				unlink($file_path);
			}		
			
			//Envia o arquivo para a gráfica por email
			$is_sended_grafica = $this->sendEmailGrafica($file_path);
			
                        
			/*if(!$is_sended) {
				throw new Exception('Erro ao enviar o email para a gráfica.');
			}*/
			
			$retorno = array(
						'error' => false
					);
			
		}catch(Exception $e) {
			$retorno = array(
						'error'   => true,
						'message' => $e->getMessage()
					);
		}
		
		return $retorno;
		
	}
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$file_path - Caminho para o .zip do XML
	 * @description	Envia o XML para impressao
	 * */
	private function sendEmailGrafica($file_path) {
		
		//array com os destinatarios do email
		if($_SESSION['servidor_teste'] == 1){
			$lista_email = array( _EMAIL_TESTE_ );
		}
		else{
			$lista_email = array( "producao@atualcard.com.br", "grafica_financeiro@sascar.com.br","patrik.m@sascar.com.br","daniele.leite@sascar.com.br");
		}
				
		$mail = new PHPMailer();
		$mail->ClearAllRecipients();
		
		$mail->IsSMTP();
		$mail->From = "sistema@sascar.com.br";
		$mail->FromName = "Intranet SASCAR - E-mail automático";
		$mail->Subject = "[SASCAR] Faturamento de Vendas";
		
		$mail->MsgHTML("Segue anexo.");
		
		//ANEXA O ARQUIVO ZIP NO EMAIL
		$mail->AddAttachment($file_path.'.zip');
		
		//adiciona os destinatarios
		foreach( $lista_email as $destinatarios ){
			$mail->AddAddress($destinatarios);
		}
				
		 $is_sended = $mail->Send();
		 
		 return $is_sended;		
		
	}
	
 public function sendEmailBoletoCliente($nota, $titulos) {
        
        $cliente = $this->getClientById($nota['cliente_id']);
        
        $tipos_de_contrato = $this->getTiposContratoNF($nota['nota_id']);
        $subject = "Carnê pagamento aquisição de equipamentos";
        $from = "sistema@sascar.com.br";
        $fromName = "Intranet SASCAR - E-mail automático";
        
        ob_start();
        include _MODULEDIR_ . 'Financas/View/fin_fat_venda_equipamento/layout_email_venda.php';
        $html = ob_get_contents();
        ob_end_clean();
        
        $pdf_path = "/var/www/docs_temporario/boletos_nf{$nota['nota_id']}.pdf";

        /**
         * Se ao menos um tipo de contrato da nota for VIVO (844), o assunto 
         * do e-mail será "Vivo Gestão de Frotas - Parceria Sascar Boleto do 
         * equipamento".
         */        
        if(in_array(844, $tipos_de_contrato)) {
            $subject = 'Vivo Gestão de Frotas - Parceria Sascar  Boleto de aquisição de equipamento';
            $from = "vivogestaodefrotas@sascar.com.br";
            $fromName = "Vivo Gestão de Frotas";
            $pdf_path = "/var/www/docs_temporario/Vivo_Gestao_de_Frotas_Boleto_NF{$nota['nota_id']}.pdf";  
            
            ob_start();
            include _MODULEDIR_ . 'Financas/View/fin_fat_venda_equipamento/layout_email_venda_vivo.php';
            $html = ob_get_contents();
            ob_end_clean();            
        }
        
        $boleto = new GeraBoletoHSBC($nota['cliente_id'], $pdf_path);
        $boleto->titulos = $titulos;
        $boleto->geraArquivo();
        
        if(file_exists($pdf_path) && !empty($cliente['email'])) {
            //array com os destinatarios do email
            if($_SESSION['servidor_teste'] == 1){
                $lista_email = array('teste_desenv@sascar.com.br');
            }
            else{
                $lista_email = array($cliente['email']);
            }
            
            $mail = new PHPMailer();
            $mail->ClearAllRecipients();

            $mail->IsSMTP();
            $mail->From = $from;
            $mail->FromName = $fromName;
            $mail->Subject = $subject;            
            
            $mail->MsgHTML($html);

            //ANEXA O ARQUIVO ZIP NO EMAIL
            $mail->AddAttachment($pdf_path);

            //adiciona os destinatarios
            foreach( $lista_email as $destinatario ){
                $mail->AddAddress($destinatario);                
            }
            
            $mail->AddBCC('controle_vivo@sascar.com.br');

            $mail->Send();
        }
        
        //APAGA O ARQUIVO
        if(file_exists($pdf_path)){
            unlink($pdf_path);
        }	
        
    }
    
    public function getTiposContratoNF($nota_id) {
        $sql = "
            SELECT
                conno_tipo
            FROM
                nota_fiscal_venda                
            INNER JOIN
                nota_fiscal_item_venda ON nfloid = nfinfloid
            INNER JOIN
                contrato ON connumero = nficonoid
            WHERE
                nfloid = $nota_id
            ";
        
        $rs = pg_query($this->conn, $sql);
        
        $tipos_contrato = array();
        
        while($contrato = pg_fetch_assoc($rs)) {
            $tipos_contrato[] = (int)$contrato['conno_tipo'];
        }
        
        return $tipos_contrato;
        
        
    }
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @param		$numero_resultados - Limit setado no formulário pelo usuário
	 * @param		$sql_add - Filtros setados no formulário
	 * @description	Como limitamos a consulta, o último contrato por vir faltando 
	 *				itens e isso não pode acontecer
	 * @return		Número de itens que faltaram no último contrato listado
	 * */
	private function verificaSeContratoPossuiMaisItens($numero_resultados, $sql_add) {
	
		$qtd_contrato_com_limit = 0;
		$qtd_contrato_sem_limit = 0;
	
		$sql = "
			SELECT
				contrato
			FROM
				faturamento_venda_view
			$sql_add
			ORDER BY
				contrato				
			LIMIT $numero_resultados
			";
	
		$rs = pg_query($this->conn, $sql);
	
		if(pg_num_rows($rs) > 0) {
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$contratos[] = pg_fetch_result($rs, $i, 'contrato');
			}
		} else {
			return 0;
		}
					
		//pega o último contrato 
		$contrato = $contratos[count($contratos) - 1];
		
		$sql = "
			SELECT
				contrato AS contratos_com_limit
			FROM
				faturamento_venda_view
			$sql_add
			AND 
				contrato = $contrato
			LIMIT $numero_resultados
			";
	
		$rs = pg_query($this->conn, $sql);
	
		if(pg_num_rows($rs) > 0) {			 
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$key = pg_fetch_result($rs, $i, 'contratos_com_limit');
				$contratos_com_limit[$key][] = $i;
			}
		}
	
		$sql = "
			SELECT
				contrato AS contratos_sem_limit
			FROM
				faturamento_venda_view			
			$sql_add
			AND 
				contrato = $contrato
		";
	
		$rs = pg_query($this->conn, $sql);
	
		if(pg_num_rows($rs) > 0) {			 
			for($i = 0; $i < pg_num_rows($rs); $i++) {
				$key = pg_fetch_result($rs, $i, 'contratos_sem_limit');
				$contratos_sem_limit[$key][] = $i;
			}
		}
		
		$qtd_contrato_com_limit = count($contratos_com_limit[$contrato]);
		$qtd_contrato_sem_limit = count($contratos_sem_limit[$contrato]);
		
		return $qtd_contrato_sem_limit - $qtd_contrato_com_limit;
	
	}
	
}