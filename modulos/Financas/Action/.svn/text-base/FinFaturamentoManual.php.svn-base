<?php
header ( 'Content-Type: text/html; charset=ISO-8859-1' );
/**
 * Classe de persistência de dados
 */
require (_MODULEDIR_ . "Financas/DAO/FinFaturamentoManualDAO.php");
require (_MODULEDIR_ . "Financas/VO/FaturamentoManual.php");
//require (_MODULEDIR_ . "Financas/Action/FinFaturamentoUnificado.php");
require 'lib/Components/ComponenteBuscaCliente.php';

/**
 * FinImportacaoStatusContrato.php
 *
 * - Cadastro manual de faturamento e notas, baseado em notas anteriores
 * - Classe para importar e atualizar contratos em lote através
 * de um arquivo CSV.
 *
 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 * @package Finanças
 * @since 25/03/2013
 *       
 */
class FinFaturamentoManual {
	
	// arquivos de log de importacao de arquivo, inclusao e nova nota.
	protected $_logFileI = "/var/www/docs_temporario/log_fat_inserir_item.csv";
	protected $_logFileN = "/var/www/docs_temporario/log_fat_nova_nota.csv";
	private $dao;
	private $vo;
	private $comp_cliente;
	private $msgSucesso = "";
	private $msgAlerta = "";
	private $msgErro = "";
	private $comp_cliente_params = array (
			'id' => 'cliente_id',
			'name' => 'cliente_nome',
			'cpf' => 'cliente_cpf',
			'cnpj' => 'cliente_cnpj',
			'tipo_pessoa' => 'tipo_pessoa',
			'btnFind' => true,
			'btnFindText' => 'Pesquisar',
			'data' => array (
					'table' => 'clientes',
					'where' => 'clidt_exclusao is null',
					'fieldFindByText' => 'clinome',
					'fieldFindById' => 'clioid',
					'fieldFindByCPF' => 'clino_cpf',
					'fieldFindByTipoPessoa' => 'clitipo',
					'fieldFindByCNPJ' => 'clino_cgc',
					'fieldLabel' => 'clinome',
					'fieldReturn' => 'clioid',
					'fieldReturnCPF' => 'clino_cpf',
					'fieldReturnCNPJ' => 'clino_cgc' 
			) 
	);
	const TEMP_DIR = '/var/www/docs_temporario/';
	const ARQUIVO_REL = 'rel_fat_manual.xls';
	private $arquivoImportado = null;

	// [START][ORGMKTOTVS-1929] - Leandro Corso
	function __construct($int_totvs_msg = null) {
	// [END][ORGMKTOTVS-1929] - Leandro Corso

		global $conn;
	
		// [START][ORGMKTOTVS-1929] - Leandro Corso
		$this->integracao_totvs_msg = $int_totvs_msg;
		// [END][ORGMKTOTVS-1929] - Leandro Corso

		$this->dao = new FinFaturamentoManualDAO ( $conn );
		$this->comp_cliente = new ComponenteCliente ( $this->comp_cliente_params );
		$this->comp_cliente2 = new ComponenteCliente ( $this->comp_cliente_params );
		if ($_REQUEST ['ids_notas']) {
			$this->vo = new FaturamentoManual ( $_REQUEST ['ids_notas'] );
		}
	}
	
	/**
	 * Caso o Faturamento Unificado esteja em execução, bloquear a tela.
	 */
	private function _testaFaturamentoUnificado() {
		// bloquear tela caso faturamento unificado esteja em execução
		$FinFaturamentoUnificado = new FinFaturamentoUnificado ( false );
		$retorno = $FinFaturamentoUnificado->verificarProcesso ( false );
		
		if ($retorno ['codigo'] == 2) {
			$this->msgErro = "Não é possível acessar o Faturamento Manual neste momento. O processo de Faturamento Unificado está sendo executado. Tente mais tarde.";
			include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/mensagem.php');
			exit ();
		}
	}
	
	/**
	 * Método principal, pesquisa e seleção de notas
	 * 
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 */
	public function pesquisa() {
		
		// $this->_testaFaturamentoUnificado();
		if (isset ( $_POST ['ajax'] )) {
			
			$parametros = $_POST;
			
			$parametros ['clioid'] = $_POST ['cpx_valor_cliente_nome'];
			try {
				$notas = $this->dao->pesquisarNotas ( $parametros );
			} catch ( Exception $e ) {
				echo json_encode ( array (
						"status" => "error",
						"message" => $e->getMessage (),
						"redirect" => "" 
				) );
				return;
			}
		}
		include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/pesquisa' . (isset ( $_POST ['ajax'] ) ? '.ajax' : '') . '.php');

	}
	public function pesquisaClientes() {
		if (isset ( $_POST ['cliente'] )) {
			
			$cliente = $_POST ['cliente'];
			
			$response = null;
			$clientes = $this->dao->clientesPesquisas ( $cliente );
			
			while ( $rsRow = pg_fetch_array ( $clientes ) ) {
				
				if (isset ( $rsRow ['retornocpf'] ) || ! empty ( $rsRow ['retornocpf'] ) || $rsRow ['retornocpf'] != '' || $rsRow ['retornocpf'] != null) {
					$resultadoCPFCNPJ = "CPF:" . $this->mask ( $rsRow ['retornocpf'], '###.###.###-##' );
				} else {
					
					$resultadoCPFCNPJ = "CNPJ:" . $this->mask ( $rsRow ['retornocnpj'], '##.###.###/####-##' );
				}
				echo "<div class='cpx_div_link cpx_div_link2' title=" . $rsRow ['label'] . " id=" . $rsRow ['id'] . " >
			<label style='float: left; cursor: pointer;'>" . $rsRow ['label'] . "  " . $resultadoCPFCNPJ . "</label>
			<div style='clear:both;'></div>
			</div>";
			}
		}
	}
	public function mask($val, $mask) {
		$maskared = '';
		$k = 0;
		for($i = 0; $i <= strlen ( $mask ) - 1; $i ++) {
			if ($mask [$i] == '#') {
				
				if (isset ( $val [$k] ))
					$maskared .= $val [$k ++];
			} else {
				if (isset ( $mask [$i] ))
					$maskared .= $mask [$i];
			}
		}
		return $maskared;
	}
	
	/**
	 * Edição de notas selecionadas.
	 * 
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 */
	public function editar() {
		// $this->_testaFaturamentoUnificado();
		if ($_POST ['chk_oid']) {
			
			$ids_notas = implode ( ",", $_POST ['chk_oid'] );
			$this->vo = new FaturamentoManual ( $ids_notas );
			$this->vo->cliente = $this->dao->pesquisarClienteNota ( $ids_notas );
			$this->vo->setItens ( $this->dao->pesquisarItensNota ( $ids_notas ) );
		}
		
		$formasCobranca = $this->dao->getFormasCobranca ();
		$fontesPagadoras = $this->dao->getFontesPagadoras ( $this->vo->cliente ['clioid'], null, 1 );
		$transportes = $this->dao->getTransportes ();
		
		// faço a busca dos creditos referente ao cliente e salvo na VO
		$this->dao->buscarCreditosConceder($this->vo,$this->vo->cliente['clioid']);
		
		$this->creditosFuturo = $this->vo->getCreditos();
		
		include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/editar.php');
	}
	public function editarItens() {
		include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/editar_itens.ajax.php');
	}
	public function pesquisarContrato() {
		if ($_POST ['ajax']) {
			$params = $_POST;
			if (isset ( $_POST ['cpx_valor_cliente_nome'] ) && ! empty ( $_POST ['cpx_valor_cliente_nome'] )) {
				$params ['clioid'] = $_POST ['cpx_valor_cliente_nome'];
			} else {
				$params ['clioid'] = $this->vo->cliente ['clioid'];
			}
			try {
				$listContratos = $this->dao->pesquisarContratos ( $params );
			} catch ( Exception $e ) {
				echo json_encode ( array (
						"status" => "error",
						"message" => $e->getMessage (),
						"redirect" => "" 
				) );
				return;
			}
		}
		include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/pesquisa_contrato.ajax.php');
	}
	public function pesquisarObrigacoesFinancerias() {
		if ($_POST ['ajax']) {
			try {
				
				$listObrigacoes = $this->dao->getObrigacoesFinanceiras ( $_POST );
			} catch ( Exception $e ) {
				echo json_encode ( array (
						"status" => "error",
						"message" => $e->getMessage (),
						"redirect" => "" 
				) );
				return;
			}
		}
		include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/pesquisa_obrfin.ajax.php');
	}
	public function incluirItem() {
		if (isset ( $_POST ['notaFiscalItem'] )) {
			
			$editar = true;
			$itensNf = $this->vo->getItens ();
			$itemEdicao = $itensNf [$_POST ['notaFiscalItem']];
			$_POST ['connumero'] = $itemEdicao ['connumero'];
		} else if (! $_POST ['connumero']) {
			
			echo json_encode ( array (
					"status" => "error",
					"message" => "N&uacute;mero do contrato n&atilde;o informado.",
					"redirect" => "" 
			) );
			return;
		}
		
		include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/incluir_item.ajax.php');
	}
	public function outrasInformacoesNF() {
		include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/outras_inf_nota.ajax.php');
	}
	public function gerarNotaFiscal() {
		try {
			$this->dao->begin ();
			
			$params = $_POST;

			$notasGeradas = $this->dao->gerarNotas ( $this->vo, $params );
			
			$this->itensNota = $notasGeradas [0] ['itens_nota'];
			
			$idsNota = array ();
			foreach ( $notasGeradas as $nota ) {
				$idsNota [] = $nota ['nfloid'];
			}
			$listNotas = $this->dao->pesquisarNotas ( array (
					"clioid" => $this->vo->cliente ['clioid'],
					"nfloid" => implode ( ", ", $idsNota ) 
			) );
			
			include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/nota_gerada.php');
			// $this->dao->rollback();
			$this->dao->commit ();
		} catch ( Exception $e ) {
			$this->dao->rollback ();
			$this->msgErro = $e->getMessage ();
			$this->editar ();
			return;
		}
	}
	public function gerarPreviaNotaFiscal() {
		try {
			$this->dao->begin ();
			$this->params = $_POST;
			
			$listNotas = $this->dao->gerarPreviaNotas ( $this->vo, $this->params );
			
			include (_MODULEDIR_ . 'Financas/View/fin_fat_manual/previa_nota_gerada.php');
			
			// $this->dao->rollback();
			$this->dao->commit ();
		} catch ( Exception $e ) {
			$this->dao->rollback ();
			$this->msgErro = $e->getMessage ();
			$this->editar ();
			return;
		}
	}
	public function atualizaValorCreditoPercentual() {
		$params = $_POST;
		
		$itensNota = $this->vo->getItens ();
		
		$valorTotalNota = 0;
		
		$valorTotalNota = array ();
		foreach ( $itensNota as $key => $nota ) {
			$valorTotalNota [$nota ['nfitipo']] += floatval ( $nota ['nfivl_item'] ) - floatval ( $nota ['nfidesconto'] );
		}
		
		$retorno = array ();
		
		foreach ( $params ['creditos'] as $key => $credito ) {
			
			// se for porcentagem e monitoração
			if ($credito ['cfoaplicar_desconto'] == '1') {
				$credito ['valor'] = ($credito ['percentual'] * $valorTotalNota ['M']);
			}
			
			// se for porcentagem e locação
			if ($credito ['cfoaplicar_desconto'] == '2') {
				$credito ['valor'] = ($credito ['percentual'] * $valorTotalNota ['L']);
			}
			
			if ($credito ['percentual'] != 1) {
				$credito ['valor'] = floor ( $credito ['valor'] * 100 ) / 100;
			}
			
			$credito ['valor_formatado'] = number_format ( $credito ['valor'], 2, ',', '.' );
			
			$this->vo->editCredito ( $credito );
			
			$retorno [] = $credito;
		}
		
		echo json_encode ( $retorno );
		
		exit ();
	}
	public function excluirItem() {
		if (isset ( $_POST ['ajax'] )) {
			
			$parametros = $_POST ['chk_oid'];
			foreach ( $parametros as $key ) {
				$this->vo->removeItem ( $key );
			}
		}
		// if(isset($_POST['stroid'])){
		
		// $this->vo->removeItem($_POST['stroid']);
		// }
	}
	public function salvarItem() {
		if ($_POST ['item']) {
			
			try {
				$contrato = $this->dao->getContrato ( $_POST ['item'] ['connumero'] );
				
				$obrigacao = $this->dao->getObrigacaoFinanceira ( $_POST ['item'] ['obroid'] );
				
				if (! $obrigacao)
					throw new Exception ( 'Obriga&ccedil;&atilde;o financeira n&atilde;o encontrada.' );
				
				$_POST ['item'] ['obrobrigacao'] = utf8_decode ( $_POST ['item'] ['obrobrigacao'] );
				// $_POST['item']['nfivl_item']=str_replace(",", ".", str_replace(".", "", $_POST['item']['nfivl_item']));
				// $_POST['item']['nfidesconto']=str_replace(",", ".", str_replace(".", "", $_POST['item']['nfidesconto']));
				$_POST ['item'] ['nfivl_item'] = $_POST ['item'] ['nfivl_item'];
				$_POST ['item'] ['nfidesconto'] = $_POST ['item'] ['nfidesconto'];
				
				$_POST ['item'] ['obrobrigacao'] = $obrigacao ['obrobrigacao'];
				$_POST ['item'] ['obroid'] = $obrigacao ['obroid'];
				if ($contrato) {
					$_POST ['item'] ['tpcoid'] = $contrato ['tpcoid'];
					$_POST ['item'] ['tpcdescricao'] = $contrato ['tpcdescricao'];
				}
				if (isset ( $_POST ['chaveItem'] )) {
					$this->vo->editItem ( $_POST ['item'], $_POST ['chaveItem'] );
				} else {
					
					if (isset ( $_POST ['qtd_replicacoes'] ) && intval ( $_POST ['qtd_replicacoes'] ) > 1) {
						
						if (count ( $this->vo->getItens () ) + intval ( $_POST ['qtd_replicacoes'] ) > 5000) {
							throw new Exception ( 'O limite m&aacuteximo &eacute de 5000 itens.' );
						}
						
						$itens = $this->vo->getItens ();
						
						$itensComContratos = array ();
						
						if (count ( $itens )) {
							foreach ( $itens as $key => $value ) {
								$itensComContratos [] = $value ['connumero'];
							}
						}
						
						$verifica = false;
						
						if (isset ( $_POST ['item'] ['connumero'] ) && in_array ( $_POST ['item'] ['connumero'], $itensComContratos )) {
							$verifica = true;
						}
						
						for($i = 0; $i < intval ( $_POST ['qtd_replicacoes'] ); $i ++) {
							$this->vo->addItem ( $_POST ['item'], $verifica );
						}
					} else {
						
						$this->vo->addItem ( $_POST ['item'], true );
					}
				}
			} catch ( Exception $e ) {
				echo json_encode ( array (
						"status" => "error",
						"message" => $e->getMessage (),
						"redirect" => "" 
				) );
				return;
			}
		}
	}
	
	/**
	 * Métodos referentes à importação de arquivos.
	 * 
	 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
	 */
	public function telaImportacao() {
		// $this->_testaFaturamentoUnificado();
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
			$operation = intval ( $_POST ['operacao'] );
			
			// Importa o arquivo CSV para ambos os layouts
			$records = $this->_importarArquivo ( $operation );
			
			if ($records) {
				require_once _MODULEDIR_ . "Financas/DAO/FinFaturamentoManualImportacaoDAO.php";
				$dao = new FinFaturamentoManualImportacaoDAO ();
				
				try {
					$this->dao->begin ();
					
					$count = $dao->importarRegistros ( $operation, $records );
					
					if ($count && $count > 0) {
						if ($count > 1) {
							$this->msgSucesso = "{$count} itens importados com sucesso!";
						} else {
							$this->msgSucesso = "{$count} item importado com sucesso!";
						}
					} else {
						$this->msgErro = 'Ocorreu um erro ao importar o arquivo.';
					}
					$this->dao->commit ();
				} catch ( Exception $e ) {
					$this->dao->rollback ();
					$this->msgErro = $e->getMessage ();
				}
			} else {
				$this->msgErro = 'O arquivo enviado deve estar no formato .csv.';
			}
		}
		
		require_once _MODULEDIR_ . 'Financas/View/fin_fat_manual/importar.php';
	}
	
	/**
	 * Importa arquivo CSV e retorna uma array associativo com chaves
	 * específicas para cada layout
	 *
	 * @param int $operation        	
	 * @return array
	 */
	protected function _importarArquivo($operation) {
		// Valida a extensão do arquivo
		if (! preg_match ( '/\.csv$/', $_FILES ['arquivo_csv'] ['name'] )) {
			return false;
		}
		
		$file = $_FILES ['arquivo_csv'] ['tmp_name'];
		$lines = explode ( "\n", file_get_contents ( $file ) );
		unset ( $lines [0] );
		
		foreach ( $lines as $line ) {
			
			// Pula linhas vazias
			if (strlen ( trim ( $line ) ) == 0) {
				
				continue;
			}
			
			$cols = explode ( ';', $line );
			
			// Layout 1: importação de itens para nota
			if ($operation == 1) {
				$records [] = array (
						'nflno_numero' => $cols [0],
						'nflserie' => $cols [1],
						'connumero' => $cols [2],
						'nfiobroid' => $cols [3],
						'titvl_titulo' => $cols [4],
						//'titvl_desconto' => (floatval ( $cols [5] ) > 0) ? floatval ( $cols [5] ) : '0.00',
						'titvl_desconto' => '0.00',
						'nfitipo' => trim ( $cols [6] ) 
				);
			}			// Layout 2: criação de nova nota
			elseif ($operation == 2) {
				
				$records [] = array (
						'cpf_cnpj' => $cols [0],
						'connumero' => $cols [1],
						'nfiobroid' => $cols [2],
						'titvl_titulo' => $cols [3],
						//'titvl_desconto' => (floatval ( $cols [4] ) > 0) ? floatval ( $cols [4] ) : '0.00',
						'titvl_desconto' => '0.00',
						'nfitipo' => trim ( $cols [5] ),
						'dt_vencimento' => trim ( $cols [6] ),
						'titno_parcela' => trim ( $cols [7] ),
						'titformacobranca' => trim ( $cols [8] ),
						'nflserie' => trim ( $cols [9] ) 
				);
			}
		}
		
		return $records;
	}
	public function downloadLogFileI() {
		header ( 'Content-Type: text/csv' );
		header ( 'Content-Disposition: attachment;filename=inconsistencia_1_' . date ( 'dmY' ) . '.csv' );
		header ( 'Cache-Control: max-age=0' );
		echo file_get_contents ( $this->_logFileI );
		return;
	}
	public function downloadLogFileN() {
		header ( 'Content-Type: text/csv' );
		header ( 'Content-Disposition: attachment;filename=inconsistencia_2_' . date ( 'dmY' ) . '.csv' );
		header ( 'Cache-Control: max-age=0' );
		echo file_get_contents ( $this->_logFileN );
		return;
	}
	public function validarNotaFiscal() {
		$retorno = array ();
		try {
			if (isset ( $_POST ['cpx_valor_cliente_nome'] )) {
				$this->vo->cliente = $this->dao->pesquisarCliente ( $_POST ['cpx_valor_cliente_nome'] );
			}
			$params = $_POST;
			
			$this->dao->validarNotas ( $this->vo, $params );
			
			$retorno ['status'] = true;
			$retorno ['tipoErro'] = 'success';
			$retorno ['mensagem'] = 'Dados validados com sucesso.';
		} catch ( Exception $e ) {
			$retorno ['status'] = false;
			$retorno ['tipoErro'] = 'alerta2';
			$retorno ['mensagem'] = utf8_encode ( $e->getMessage () );
		}
		
		echo json_encode ( $retorno );
		return;
	}
	public function gerarNfNova() {
		// Limpa lixo de sessao
		if (isset ( $_SESSION ['fat_manual'] )) {
			unset ( $_SESSION ['fat_manual'] );
		}
		$formasCobranca = $this->dao->getFormasCobranca ();
		
		require_once _MODULEDIR_ . 'Financas/View/fin_fat_manual/gerarNfNova.php';
	}
	public function mround($number, $precision = 0) {
		$precision = ($precision == 0 ? 1 : $precision);
		$pow = pow ( 10, $precision );
		
		$ceil = ceil ( $number * $pow ) / $pow;
		$floor = floor ( $number * $pow ) / $pow;
		
		$pow = pow ( 10, $precision + 1 );
		
		$diffCeil = $pow * ($ceil - $number);
		$diffFloor = $pow * ($number - $floor) + ($number < 0 ? - 1 : 1);
		
		if ($diffCeil >= $diffFloor)
			return $floor;
		else
			return $ceil;
	}
	public function somaParcelas() {
		$parcelas = count ( $_POST ) - 1;
		
		$somaTotal = 0;
		for($i = 0; $i < $parcelas; $i ++) {
			
			$valorParcela = str_replace ( '.', '', $_POST ['parcela_' . $i] );
			$valorParcela = str_replace ( ',', '.', $valorParcela );
			
			$somaTotal += floatval ( $valorParcela );
		}
		
		$somaTotal = $this->mround ( $somaTotal, 2 );
		
		echo number_format ( $somaTotal, 2, ',', '.' );
	}
	public function calcularParcelas() {
		global $conn;
		
		$itens = $this->vo->getItens ();
		$parcelas = $_POST ['qtd_parcela'];
		$data_base = $_POST ['data_base_vencimento'];
		$conceder_creditos = $_POST ['conceder_creditos'];
		
		$valorTotal = 0;
		
		if ($conceder_creditos == "true") {
			
			// 5.resgata a soma dos valores e desconto dos itens
			$totais ['M'] ['valor_total'] = '0';
			$totais ['L'] ['valor_total'] = '0';
			
			foreach ( $itens as $key => $value ) {
				$totais [$value ['nfitipo']] ['valor_total'] += floatval ( $value ['nfivl_item'] ) - floatval ( $value ['nfidesconto'] );
				$totais ['valor_descontos'] += floatval ( $value ['nfidesconto'] );
				$totais ['valor_total'] += floatval ( $value ['nfivl_item'] );
			}
			
			$finCreditoFuturoDao = new FinCreditoFuturoDAO ( $conn );
			
			$bo = new CreditoFuturo($finCreditoFuturoDao);
			
			$creditosFuturos = $this->vo->getCreditos();
			
			$arrayCreditosFuturo = array ();
			
			foreach ( $creditosFuturos as $key => $credito ) {
				
				$creditoFuturoObj = new CreditoFuturoVO ();
				$creditoFuturoParcelaObj = new CreditoFuturoParcelaVO ();
				$creditoFuturoMotivoObj = new CreditoFuturoMotivoCreditoVO ();
				
				$creditoFuturoObj->id = $credito ['credito_id'];
				$creditoFuturoObj->contratoIndicado = $credito ['connumero'];
				$creditoFuturoObj->aplicarDescontoSobre = $credito ['cfoaplicar_desconto'];
				$creditoFuturoObj->valor = $credito ['valor'];
				$creditoFuturoObj->obrigacaoFinanceiraDesconto = $credito ['obrigacao_id'];
				$creditoFuturoObj->tipoDesconto = $credito ['cfotipo_desconto'];
				$creditoFuturoObj->origem = 3;
				
				$creditoFuturoMotivoObj->id = $credito ['motivo_credito_id'];
				$creditoFuturoMotivoObj->tipo = $credito ['tipo_motivo_credito'];
				$creditoFuturoMotivoObj->descricao = $credito ['cfmcdescricao'];
				
				$creditoFuturoParcelaObj->id = $credito ['parcela_id'];
				$creditoFuturoParcelaObj->numero = $credito ['parcela_numero'];
				
				$creditoFuturoObj->Parcelas = $creditoFuturoParcelaObj;
				$creditoFuturoObj->MotivoCredito = $creditoFuturoMotivoObj;
				
				$arrayCreditosFuturo [] = $creditoFuturoObj;
				// code...
			}
			
			$retorno = $bo->processarDesconto ( $arrayCreditosFuturo, $totais, array (
					'nfloid' => 0,
					'nflno_numero' => 0,
					'nflserie' => '' 
			), false );
			
			$valorTotal = $retorno ['total'];
			
			$valorTotal = number_format ( $valorTotal, 2, '.', '' );
		} else {
			
			$valorTotal = 0;
			
			foreach ( $itens as $key => $value ) {
				$valorTotal += floatval ( $value ['nfivl_item'] ) - floatval ( $value ['nfidesconto'] );
			}
		}
		
		$valorParcelaTeste = $valorTotal / $parcelas;
		
		$valorParcela = floatval ( substr ( $valorParcelaTeste, 0, strrpos ( $valorParcelaTeste, '.' ) + 3 ) );
		
		$tbody = "";
		
		$somaParcelas = 0;
		
		for($i = 1; $i <= $parcelas; $i ++) {
			
			$class = $class == 'par' ? 'impar' : 'par';
			
			if ($i == 1) {
				$data = $data_base;
			} else {
				$vencimento = $i - 1;
				$data_base_temp = explode ( '/', $data_base );
				$data_base_temp = $data_base_temp [2] . '-' . $data_base_temp [1] . '-' . $data_base_temp [0];
				
				$data = date ( 'd/m/Y', strtotime ( $data_base_temp . " +" . $vencimento . " month" ) );
			}
			
			if ($i != $parcelas) {
				$somaParcelas += $valorParcela;
			} else {
				$valorParcela = $valorTotal - $somaParcelas;
			}
			
			$tbody .= "<tr class='" . $class . "'>";
			$tbody .= "<td style='text-align: center;'>" . $i . "</td>";
			$tbody .= "<td style='text-align: center;'><div class='campo data data_parcela'><input class=' campo grid_field data_grid_field' name='parcela[" . $i . "][data]' value = '" . $data . "'></div></td>";
			$tbody .= "<td style='text-align: center;'><input style='border:1px solid #999999; width: 100px' maxlength='12' id='parcela_valor_" . $i . "' class='grid_field valor_grid_field' name='parcela[" . $i . "][valor]' value = '" . number_format ( $valorParcela, 2, ',', '.' ) . "'></td>";
			$tbody .= "</tr>";
		}
		
		$tfoot = "<tr>";
		$tfoot .= "<td style='text-align: center' colspan='2' >Total da Nota: R$ <span id='total_nota'>" . number_format ( $valorTotal, 2, ',', '.' ) . "</span></td>";
		$tfoot .= "<td style='text-align: center'>Total das Parcelas: R$ <span id='total_parcelas_nota'>" . number_format ( $valorTotal, 2, ',', '.' ) . "</span></td>";
		$tfoot .= "</tr>";
		
		$valorTotal_tabela_descontos = 0;
		
		foreach ( $itens as $key => $value ) {
			$valorTotal_tabela_descontos += floatval ( $value ['nfivl_item'] ) - floatval ( $value ['nfidesconto'] );
		}
		
		if ($conceder_creditos == "true") {
			$htmlDescontosConceditos = "<tr class='impar'>";
			$htmlDescontosConceditos .= "<td style='width: 250px;' class='agrupamento'>Total da nota</td>";
			$htmlDescontosConceditos .= "<td class='direita'> R$ " . number_format ( $valorTotal_tabela_descontos, 2, ',', '.' ) . "</td>";
			$htmlDescontosConceditos .= "</tr>";
			
			$htmlDescontosConceditos .= "<tr class='par'>";
			$htmlDescontosConceditos .= "<td style='width: 250px;' class='agrupamento'>Créditos concedidos</td>";
			$htmlDescontosConceditos .= "<td class='direita'> R$ -" . number_format ( $totais ['valor_total'] - $valorTotal, 2, ',', '.' ) . "</td>";
			$htmlDescontosConceditos .= "</tr>";
			
			$htmlDescontosConceditos .= "<tr class='impar'>";
			$htmlDescontosConceditos .= "<td style='width: 250px;' class='agrupamento'>Total da nota c/ descontos</td>";
			$htmlDescontosConceditos .= "<td class='direita'> R$ " . number_format ( $valorTotal, 2, ',', '.' ) . "</td>";
			$htmlDescontosConceditos .= "</tr>";
		}
		
		$retorno = array ();
		
		if ($conceder_creditos == "true") {
			$retorno ['desconto_aplicado_tabela'] = utf8_encode ( $htmlDescontosConceditos );
		}
		
		$retorno ['tbody'] = utf8_encode ( $tbody );
		$retorno ['tfoot'] = utf8_encode ( $tfoot );
		$retorno ['total'] = utf8_encode ( $valorTotal );
		$retorno ['desconto_aplicado'] = $conceder_creditos == "true" ? $totais ['valor_total'] - $valorTotal : false;
		
		echo json_encode ( $retorno );
		
		exit ();
	}
	public function carregarCreditosAconceder() {
		$param = $_POST;
		
		$this->dao->buscarCreditosConceder($this->vo, $param['clioid']);
		
		$this->creditosFuturo = $this->vo->getCreditos();
		
		require_once _MODULEDIR_ . 'Financas/View/fin_fat_manual/listar_creditos_futuros_a_conceder.php';
	}
}