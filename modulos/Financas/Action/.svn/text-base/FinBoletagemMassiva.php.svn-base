<?php
/**
 * @file FinBoletagemMassiva.php
 * @author marcio.ferreira
 * @version 29/07/2015 14:16:04
 * @since 29/07/2015 14:16:04
 * @package SASCAR FinBoletagemMassiva.php 
 */


//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/boletagem_massiva_'.date('d-m-Y').'.txt');

//manipula os dados no BD
require(_MODULEDIR_ . "Financas/DAO/FinBoletagemMassivaDAO.php");

//nodulo de nanutenção da politica de desconto
require (_MODULEDIR_ . "Manutencao/DAO/ManPoliticaDescontoDAO.php"); 


//faz os cálculos da dívida dos títulos sem e com a politica de desconto
require(_MODULEDIR_ . "Financas/Action/FinCalculoDivida.php");

//arquivo com o html de boleto
require_once _SITEDIR_ . 'boleto_boletagem_massiva.php';

//geração de PDF
require_once _SITEDIR_ . 'lib/MPDF/mpdf.php';
require_once _SITEDIR_ . 'lib/tcpdf_php4/tcpdf.php';

require_once 'lib/phpMailer/class.phpmailer.php';

// classe para gerar arquivo csv
include_once 'lib/Components/CsvWriter.php';

//Paginação
require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';



class FinBoletagemMassiva{
	
	//atributos privados
	private $dao;
	
	private $finCalculoDivida;
	
	private $daoManutencaoPolitica;
	
	private $daoFinCalculoDivida;
	
	private $usuarioID ;
	
	private $id_campanha;
	
	private $nome_politica;
	
	private $desconto_politica;
	
	private $pasta_arquivo;
	
	private $nome_arquivo_csv;
	
	private $nome_arquivo_xml;
	
	
	//Objetos para exibicao e dados em telas
	private $view;
	
	/**
	 * Id da tabela execucao arquivo
	 * @var int
	 */
	public $earoid;
	
	
	
	public function __construct() {
	
		global $conn;
		
		if(empty($this->usuarioID)){
			$this->usuarioID = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid']: NULL;
		}
	
		$this->dao = new FinBoletagemMassivaDAO($conn);
		$this->daoManutencaoPolitica = new ManPoliticaDescontoDAO($conn);
		
		//instância da classe para efetuar os cálculos e gerar o boleto unificado
		$this->finCalculoDivida = new FinCalculoDivida();
		$this->daoFinCalculoDivida = new FinCalculoDividaDAO($conn);
		
		$this->pasta_arquivo = '/var/www/faturamento/arquivo_grafica_boletagem_massiva/';
		
		//Cria objeto da view
		$this->view = new stdClass();
		
		// Ordenção e paginação
		$this->view->ordenacao = null;
		$this->view->paginacao = null;
		$this->view->totalResultados = 0;
		
	}
	
	
	/**
	 * 
	 * 
	 * @param string $param
	 */
	public function index($param=NULL){
		
		if($param['tipo'] == 'alerta'){
			//$tipo = $param['tipo'];
			$msg = $param['msg'];
			$classe = "mensagem alerta";
		}

		if($param['tipo'] == 'erro'){
			//$tipo = $param['tipo'];
			$msg = $param['msg'];
			$classe = 'mensagem erro';
		}
		
		if($param['tipo'] == 'sucesso'){
			//$tipo = $param['tipo'];
			$msg = $param['msg'];
			$classe = 'mensagem sucesso';
		}
		
		$botao = '';
		
		//exibe o botão novo caso o usuário tenha permissão
		$funcao_botao_novo  =  $_SESSION['funcao']['cadastrar_campanha_boletagem'];
		
		//verifica processo em andamento
		$processo = $this->dao->verificarProcessoAndamento();
		
		//caso tenho um processo, lança Exception e exibe tela de busca
		if(is_object($processo)){
			
			//verifica o tipo de processamento para exibir a mensagen
			$tipo_processo =  explode('|', $processo->earparametros);
		
			$processo->tipo_processo = $tipo_processo[1];
			
			$msg    =   $this->getMsgProcesso($processo);
			$classe =  'mensagem alerta';
			$botao = 'disabled=disabled';
		}
		
		include (_MODULEDIR_ . 'Financas/View/fin_boletagem_massiva/index.php');
	}
	
	
	/**
	 * 
	 * 
	 * @throws Exception
	 * @return Ambigous <boolean, multitype:>
	 */
	public function pesquisar(){
		
		
		try {
			
			//tratamento para os gets da paginação, se o usuário alter algum get, não deixa realizar a pesquisa e retorna ao index
			if(empty($_POST)) {
				if(!isset($_GET['nome_campanha']) || !isset($_GET['data_vencimento']) ||  !isset($_GET['data_ini']) || !isset($_GET['data_fim']) ){
					
					if(isset($_SESSION['paginacao'])) {
						unset($_SESSION['paginacao']);
					}
					
					$this->index();
					exit();
				}
			}
			
			$paginacao = new PaginacaoComponente();
			//$paginacao->desabilitarComboClassificacao();
			
			$pesquisa = new stdClass();
			
			$nome_campanha   = trim(isset($_GET['nome_campanha']) && $_GET['nome_campanha'] != '' ? $_GET['nome_campanha'] : 'NULL');
			$data_ini        = isset($_GET['data_ini']) && $_GET['data_ini'] != '' ? $_GET['data_ini'] : 'NULL';
			$data_fim        = isset($_GET['data_fim']) && $_GET['data_fim'] != '' ? $_GET['data_fim'] : 'NULL';
			$data_vencimento = isset($_GET['data_vencimento']) && $_GET['data_vencimento'] != '' ? $_GET['data_vencimento'] : 'NULL';
						
			
			$pesquisa->nome_campanha   = trim(isset($_POST['nome_campanha']) && $_POST['nome_campanha'] != '' ? $_POST['nome_campanha'] : $nome_campanha);
			$pesquisa->data_ini        = isset($_POST['data_ini']) && $_POST['data_ini'] != '' ? $_POST['data_ini'] : $data_ini;
			$pesquisa->data_fim        = isset($_POST['data_fim']) && $_POST['data_fim'] != '' ? $_POST['data_fim'] : $data_fim;
			$pesquisa->data_vencimento = isset($_POST['data_vencimento']) && $_POST['data_vencimento'] != '' ? $_POST['data_vencimento'] : $data_vencimento;
			
			
			if ($pesquisa->data_ini == 'NULL' ) {
				throw new Exception ( 'A data inicial deve ser informada.' );
			}
			
			if ($pesquisa->data_fim == 'NULL' ) {
				throw new Exception ( 'A data final deve ser informada.' );
			}
			
			$quantPesquisa = $this->dao->getCampanhas($pesquisa);
			
			$this->view->totalResultados = $quantPesquisa[0]['total_registros'];
			
			$campos = array(
					''                    => 'Escolha',
					'abooid'              => 'Cód. Campanha',
					'nome_campanha'       => 'Nome da Campanha',
					'abodt_vencimento'    => 'Data de Vencimento'
			);
			
			if ($paginacao->setarCampos($campos)) {
				$this->view->ordenacao = $paginacao->gerarOrdenacao('abooid, nome_campanha, abodt_vencimento');
				$this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);
			}
			
			$dadosPesquisa = $this->dao->getCampanhas($pesquisa, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao());
			
			$caminho = $this->pasta_arquivo;
			
			
			//acesso a função para ações dos ícones da coluna Ação
			//exibe o botão novo caso o usuário tenha permissão
			$funcao_acoes  =  $_SESSION['funcao']['acoes_boletagem_massiva'];
			
			
			$this->index();
			
			$processamento = false;
			
			//verifica processo em andamento
			$processo = $this->dao->verificarProcessoAndamento();
			
			//caso tenho um processo, exibe mensagem de processamento
			if(is_object($processo)){
				$processamento = true;
				
				$dados_campanha =  explode('|', $processo->earparametros);
				$id_campanha = $dados_campanha[0];
				
			}
			
			include (_MODULEDIR_ . 'Financas/View/fin_boletagem_massiva/pesquisar.view.php');
			

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
	}
	
	
	/**
	 * Verifica se existe processo em andamento, caso não, exibe tela para cadastro de uma nova campanha
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function novo(){
		
		try {
			
			//seta sessão para controlar o botão novo (só cadastra uma nova campanha se pressionar o botão novo)
			$_SESSION['botao_nova_campanha'] = true;
			
	        //verifica processo em andamento
			$processo = $this->dao->verificarProcessoAndamento();
			
			//caso tenho um processo, lança Exception e exibe tela de busca
			if(is_object($processo)){
				
				//verifica o tipo de processamento para exibir a mensagen
				$tipo_processo =  explode('|', $processo->earparametros);
				
				$processo->tipo_processo = $tipo_processo[1];
				
				throw new Exception( $this->getMsgProcesso($processo));
			}
			
			include (_MODULEDIR_ . 'Financas/View/fin_boletagem_massiva/formulario_nova_campanha.php');
			
		} catch (Exception $e) {
			
			$erro['msg']  =  $e->getMessage();
			$erro['tipo'] =  'alerta';
			
			$this->index($erro);
			
			return false;
			
		}
	}
	

	/**
	 * 
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function gerarCampanha(){
		
		 $dadosCampanha = new stdClass();
		 
		 $dadosCampanha->cad_nome_campanha = isset($_POST['cad_nome_campanha']) && $_POST['cad_nome_campanha'] != '' ? $_POST['cad_nome_campanha'] : 'NULL';
		 $dadosCampanha->aging_divida      = isset($_POST['aging_divida']) && $_POST['aging_divida'] != '' ? $_POST['aging_divida'] :'NULL';
		 $dadosCampanha->cad_vencimento    = isset($_POST['cad_vencimento']) && $_POST['cad_vencimento'] != '' ? $_POST['cad_vencimento'] : 'NULL';
		 $dadosCampanha->formato_envio     = isset($_POST['formato_envio']) && $_POST['formato_envio'] != '' ? $_POST['formato_envio'] : 'NULL';
		 $dadosCampanha->valor_divida_ini  = isset($_POST['valor_divida_ini']) && $_POST['valor_divida_ini'] != '' ? $_POST['valor_divida_ini'] : 'NULL';
		 $dadosCampanha->valor_divida_fim  = isset($_POST['valor_divida_fim']) && $_POST['valor_divida_fim'] != ''  ? $_POST['valor_divida_fim'] :'NULL';
		 $dadosCampanha->tipo_pessoa       = isset($_POST['tipo_pessoa']) && $_POST['tipo_pessoa'] != '' ? $_POST['tipo_pessoa'] : '';
		 $dadosCampanha->tipo_cliente      = isset($_POST['tipo_cliente']) && $_POST['tipo_cliente'] != '' ? $_POST['tipo_cliente'] : '';
		 $dadosCampanha->uf_cliente        = isset($_POST['uf_cliente']) && $_POST['uf_cliente'] != '' ? $_POST['uf_cliente'] : 'NULL';
		 $dadosCampanha->cod_cliente       = isset($_POST['cod_cliente']) && $_POST['cod_cliente'] != '' ? $_POST['cod_cliente'] : 'NULL';
	
		 
		 try {
		 	
		 	//verifica se tem processo em andamento
		 	$processo = $this->dao->verificarProcessoAndamento();
		 	
		 	//se o usuário dar f5 na tela, e ainda tiver um processo em andamento, exibe a mensagem e redireciona para a tela de busca
		 	//se tiver um processo em andamento, redireciona para a tela de busca e exibe mensagem na tela
		 	if(is_object($processo)){
		 		
		 		//verifica o tipo de processamento para exibir a mensagen
		 		$tipo_processo =  explode('|', $processo->earparametros);
		 		
		 		$processo->tipo_processo = $tipo_processo[1];
		 		
		 		throw new Exception($this->getMsgProcesso($processo));
		 	}
		 	
			 	
		 	//se o usuário pressionar F5, exibe mensagem
		 	if(!$_SESSION['botao_nova_campanha']){
		 		throw new Exception('Ação não permitida. Clique no botão Novo.');
		 	}
			
			if ($dadosCampanha->cad_nome_campanha == 'NULL' ) {
				throw new Exception ( 'O nome da campanha deve ser informado.' );
			}
			
			if ($dadosCampanha->aging_divida == 'NULL') {
				throw new Exception ( 'O Anging da Dívida deve ser informado.' );
			}
			
			if ($dadosCampanha->cad_vencimento == 'NULL') {
				throw new Exception ( 'A Data de vencimento deve ser informado.' );
			}
			
			if ($dadosCampanha->formato_envio == 'NULL') {
				throw new Exception ( 'O formato de envio deve ser informado.' );
			}

			//trata os campos para gravar em banco
			$dadosCampanha->valor_divida_ini = $this->limpaValor($dadosCampanha->valor_divida_ini);
			$dadosCampanha->valor_divida_fim = $this->limpaValor($dadosCampanha->valor_divida_fim);

			if($dadosCampanha->uf_cliente != 'NULL'){
				//separa por '|' pipe as ufs informadas
				foreach ($dadosCampanha->uf_cliente as $uf){
					$dadosCampanha->uf_cli .= $uf.'|';
				}
			}
			
			if($dadosCampanha->cod_cliente != 'NULL'){
				//separa por '|' pipe os finais dos códigos de cliente
				foreach ($dadosCampanha->cod_cliente as $cod_cli){
					$dadosCampanha->cod_cli .= $cod_cli.'|';
				}
			}
			
			//grava os dados da campanha e retorna o id gerado		
			$this->id_campanha = $this->dao->gravarDadosCampanha($dadosCampanha);
			
			if(empty($this->id_campanha)){
				throw new Exception('Falha ao recuperar id da campanha gerada.');
			}
			
			//inicia processo no banco setando os dados na tabela execucao_arquivo
			$parametro['id_campanha'] = $this->id_campanha;
			$parametro['tipo_processo'] = 'campanha';
			$parametro['msg'] = 'Processo de Gerar Campanha Boletagem Massiva iniciado';
			
			$inicia = $this->dao->iniciarProcesso($parametro);
			
			$processo = $this->dao->verificarProcessoAndamento();
			
			//recebe o id do processo iniciado
			$this->earoid = $processo->earoid;
			
			$processo->tipo_processo = $parametro['tipo_processo'];
			
			$sucesso['msg']  =  $this->getMsgProcesso($processo);
			$sucesso['tipo'] =  'sucesso';
			
			//inicia processo em background
			$this->prepararProcessoCampanha();
			
			//seta para false, só permite cadastrar uma nova campanha pressionando o botão Novo
			$_SESSION['botao_nova_campanha'] = false;
			
			$this->index($sucesso);
			
			
		} catch ( Exception $e ) {
			
			$erro['msg']  =  $e->getMessage();
			$erro['tipo'] =  'alerta';
			
			$_SESSION['botao_nova_campanha'] = false;
			
			$this->index($erro);
				
			return false;
		}
		
	}
	
	
	/**
	 * Cria pastas e arquivo de log no servidor e chama o cron para rodar em background
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function prepararProcessoCampanha(){
		

		try {
		
		
			if (!is_dir('/var/www/faturamento/arquivo_grafica_boletagem_massiva/')) {
				 
				if (!mkdir("/var/www/faturamento/arquivo_grafica_boletagem_massiva/", 0777)) {
					throw new Exception('Falha ao criar pasta -> arquivo_grafica_boletagem_massiva.');
				}
			}
		
			chmod("/var/www/faturamento/arquivo_grafica_boletagem_massiva/", 0777);
		
			if (!$handle = fopen("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", "a")) {
				throw new Exception('Falha ao criar arquivo de log --> arquivo_grafica_boletagem_massiva_log.');
			}
		
			fputs($handle, "----------------- \r\n Processo Boletagem Massiva Iniciado (". date('Y-m-d H:i:s') .") \r\n");
			fclose($handle);
			chmod("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", 0777);
		
			//processa o arquivo em background
			passthru("/usr/bin/php "._SITEDIR_."fin_boletagem_massiva_processo.php >> /var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt 2>&1 &");
		
			return true;
			 
		} catch (Exception $e) {
		
			$this->dao->finalizarProcesso(false, $e->getMessage());
			 
			return false;
		}
		
	}
	
	
	/**
	 * 
	 * 
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function processar(){
		
		try {
			file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Iniciando Processamento' . "\n", FILE_APPEND);	
			$nomeProcesso = 'fin_boletagem_massiva_processo.php';
			 
			if(burnCronProcess($nomeProcesso) === true){
				echo " O processo [$nomeProcesso] está em processamento.";
				return;
			}
			
			//verifica o processo para pegar o id da campanha dos parâmetros
			$processo = $this->dao->verificarProcessoAndamento();
			
			if(!is_object($processo)){
				throw new Exception('Não é possível processar, não há um processo iniciado em andamento.');
			}
			
				
			$dados_campanha =  explode('|', $processo->earparametros); 
			
			$this->id_campanha = $dados_campanha[0];
			$this->usuarioID = $processo->earusuoid;
			$this->dao->usuarioID = $processo->earusuoid;
				
			// recupera dados da campanha gravados na tabela arquivo_boletagem
			$parametrosCampanha = $this->dao->getDadosGerarCampanha($this->id_campanha);
						
			
			if(!is_object($parametrosCampanha)){
				throw new Exception('Não foram encontrados parâmetros da campanha informada.');
			}

			
			// se a solicitação for para envio de e-mail, gera PDF e envia para o cliente
			if ($dados_campanha[1] === 'envio_email') {
				
				
				$this->enviarBoletoConsolidadoCliente();
				
				return;
				
			}
			
			
			$dadosFiltro = new stdClass ();
			
			$dadosFiltro->abovl_divida_inicial = $parametrosCampanha->abovl_divida_inicial;
			$dadosFiltro->abovl_divida_final   = $parametrosCampanha->abovl_divida_final;
			$dadosFiltro->abodt_vencimento     = $parametrosCampanha->abodt_vencimento;
			$dadosFiltro->abopodoid            = $parametrosCampanha->abopodoid;
			$dadosFiltro->abotipo_pessoa       = $parametrosCampanha->abotipo_pessoa;
			$dadosFiltro->abotipo_cliente      = $parametrosCampanha->abotipo_cliente;
			$dadosFiltro->abouf_cliente        = $parametrosCampanha->abouf_cliente;
			$dadosFiltro->abocod_cliente       = $parametrosCampanha->abocod_cliente;
			
			
			//recupera da tabela dados do aging da política de desconto informados
			$dados_politica = $this->getAgingPolitivaByID($dadosFiltro->abopodoid);
			
			$dadosFiltro->poddias_atraso_ini = $dados_politica['poddias_atraso_ini'];
			$dadosFiltro->poddias_atraso_fim = $dados_politica['poddias_atraso_fim'];
			
			//busca os clientes com base nos filtros
			file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Iniciando Busca de clientes (getClientesCampanha).' . "\n", FILE_APPEND);
			$clientes = $this->dao->getClientesCampanha($dadosFiltro);
			file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Finalizando Busca de clientes.' . "\n", FILE_APPEND);
			
			//Validacao para verificar se o retorno de clientes possui registro (nao e nulo)
			if (empty($clientes)){
				throw new Exception('Para os filtros indicados não foi encontrado nenhum cliente.');
			}
			
			//aplica filtragem de aging da dívida e se possui titulo consolidado
			//pesquisa todos os títulos vencidos do cliente
			$dados_titulos_cliente  = new stdClass();
			$nova_lista_clientes = array();
			
			
		   //percorre a lista de clientes para encontrar os títulos de cada cliente para tratar o restante dos filtros
			if(count($clientes) > 0){
				
				
				//nome do arquivo csv
				$this->nome_arquivo_csv = $this->pasta_arquivo.$parametrosCampanha->abonm_campanha."_".date("Ymd").".csv";
				
				if (is_dir ( $this->pasta_arquivo )) {
					
					// Gera CSV
					$csvWriter = new CsvWriter( $this->nome_arquivo_csv, ';', '', true);
					
					//Gera o cabeçalho
					$cabecalho = array(
							"ID do Cliente", 
							"Cliente", 
							"Valor", 
							"Vencimento", 
							"Status"
					);
					
					$csvWriter->addLine( $cabecalho );
					
					
				} else {
					throw new Exception ( 'Diretório -->  '.$this->pasta_arquivo.' não existe.');
				}
				
				
				$arquivo = file_exists($this->nome_arquivo_csv);
				
				if ($arquivo === false) {
					throw new Exception("O arquivo --> ".$this->nome_arquivo_csv." não pode ser gerado.");
				}
				
				file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Iniciando validacao na lista de cliente.' . "\n", FILE_APPEND);
				foreach ($clientes AS $dados_cli){
					
					$retira_cliente_lista = false;
					
					$dados_titulos_cliente->clioid = $dados_cli['titclioid'];
					$dados_titulos_cliente->titoid = '';
					$dados_titulos_cliente->limit = '';
					
					//verifica se já possui título consolidado por alguma campanha, se não está pago e não vencido
					$titulo_consolidado = $this->dao->getTituloConsolidado($dados_titulos_cliente->clioid);
					
					//retira o cliente caso já possui titulo consolidado
					if(is_object($titulo_consolidado)){
						
						unset($dados_cli['titclioid']);
					
					}else{					
					
						//recupera os títulos vencidos
						$titulos_cliente = $this->daoFinCalculoDivida->pesquisarTitulosVencidosCliente($dados_titulos_cliente);
						
						//verifica o título mais antigo e retorna a quantidade de dias vencido
						$quant_dias_vencido = $this->finCalculoDivida->getMenorDataVencimentoTitulos($titulos_cliente);
						
						
						//recupera os dados na politica informada
						$politica_econtrada = $this->daoFinCalculoDivida->verificarAplicacaoPoliticaDesconto($quant_dias_vencido, $dadosFiltro->abopodoid);
						
						//recupera o dados para atualizar a tabela arquivo_boletagem
						if($politica_econtrada[0]['podaplicacao_desc'] != ''){
							$this->nome_politica     = $politica_econtrada[0]['podaplicacao_desc'];
							$this->desconto_politica = $politica_econtrada[0]['podvlr_desconto'];
						}
						
						
						//retira o cliente que não se enquadra na politica informada
						if(!is_array($politica_econtrada)){
							unset($dados_cli['titclioid']);
						}

					}
					
					if($dados_cli){
						//cria uma nova lista
						$nova_lista_clientes[] = $dados_cli['titclioid'];
					}
				}
				file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Finalizando validacao na lista de cliente.' . "\n", FILE_APPEND);
			}
			
			
			if(count($nova_lista_clientes) > 0){
				
				//gera xml somente se for para a Gráfica
				if ($parametrosCampanha->aboformato_envio === 'G') {
						
					//cria objeto com os dados do XML
					$dados_xml = new stdClass();
						
					//cabeçalho do xml
					$dados_xml->abonm_campanha = $parametrosCampanha->abonm_campanha;
					$dados_xml->quant_registros = count($nova_lista_clientes);
					$handle = $this->cabecalhoXML($dados_xml);
				}
				
				//atualiza a tabela arquivo boletagem com a politica encontrada
				$novoDadosCampanha = new stdClass();
				
			    $novoDadosCampanha->abnm_politica    = $this->nome_politica;
			    $novoDadosCampanha->aboprc_desconto  = $this->desconto_politica;
			    $novoDadosCampanha->abooid           = $this->id_campanha;
				
				$this->dao->atualizarDadosCampanha($novoDadosCampanha);
				
				//recupera os clientes um a um da nova lista montada e recupera os títulos vencidos para efetuar o cálculo da dívida
				file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Iniciado calculo de divida.' . "\n", FILE_APPEND);
				for($i=0; $i < count($nova_lista_clientes); $i++){
					
					$titulos_array = Array();
					$titulos_cliente = Array();
					$dados_titulos_cliente->clioid = '';
					
					$dados_titulos_cliente->clioid = $nova_lista_clientes[$i];
					$dados_titulos_cliente->titoid = '';
					$dados_titulos_cliente->limit = '';
					
					//recupera os títulos vencidos da nova lista de clientes
					$titulos_cliente = $this->daoFinCalculoDivida->pesquisarTitulosVencidosCliente($dados_titulos_cliente);
					
					//pega os títulos
					if(count($titulos_cliente) > 0){
						foreach ($titulos_cliente AS $titulos_cli){
							$titulos_array[] = $titulos_cli['titoid'];
						}
					}
					
					//seta os atributo para cálculo da dívida
					$this->finCalculoDivida->setTipoCalculo(2);//politica de desconto
					$this->finCalculoDivida->setCodigoCliente($dados_titulos_cliente->clioid);
					$this->finCalculoDivida->setIdTitulo($titulos_array);
					$this->finCalculoDivida->setNovaDataVencimento($dadosFiltro->abodt_vencimento); 
					$this->finCalculoDivida->setPercentoJuros(0.033);
					$this->finCalculoDivida->setPercentoMulta(2);
					$this->finCalculoDivida->setPercentoDesconto($this->desconto_politica);
					$this->finCalculoDivida->setIdUsuario($this->usuarioID);
					$this->finCalculoDivida->setObsTituloConsolidado('Titulo gerado pelo módulo de Boletagem Massiva (Geração de Campanha)');
					
					///calcula os valores de acordo os parâmetros informados no atributos acima
					$retornoCalculoPolitica = $this->finCalculoDivida->calcular(true);//chama o método com parâmetro true para retornar um ARRAY de resultados

					//gera o título consolidado de acordo o retorno do método calcular(true);
					$dadosConsolidados = $this->finCalculoDivida->consolidarTitulos($retornoCalculoPolitica);
					
					
					if(is_array($dadosConsolidados)){
						
						//armazena histórico de acionamento do boleto gerado
						$dadosAcionamento = new stdClass();

						$desc = "Boleto Unificado enviado para o cliente – valor R$ ".$dadosConsolidados['valor_titulo']." com vcto em ".$dadosFiltro->abodt_vencimento." ";
						
						$dadosAcionamento->tiaacionamento = $desc;
						$dadosAcionamento->tiausuoid      = $this->usuarioID;
						$dadosAcionamento->tiamotivo      = 'Campanha Boletagem';
						$dadosAcionamento->tiaclioid      = $dados_titulos_cliente->clioid;
						
						$this->dao->setAcionamentoTituloConsolidado($dadosAcionamento);
						
					}
					
					
					$paramsBoleto = new stdClass();
					
					///recupera dados para enviar para impressão do boleto
		 			$paramsBoleto->titoid                   = $dadosConsolidados['titulo'];
					$paramsBoleto->valor_total_original_tit = str_replace(',','.',$dadosConsolidados['valor_titulo']);
		 			$paramsBoleto->valor_recalc             = str_replace(',','.',$dadosConsolidados['valor_recalc']);
					$paramsBoleto->valor_multa              = str_replace(',','.',$dadosConsolidados['valor_multa']);
					$paramsBoleto->valor_juros              = str_replace(',','.',$dadosConsolidados['valor_juros']);
					$paramsBoleto->valor_desconto           = str_replace(',','.',$dadosConsolidados['valor_desconto']);
					$paramsBoleto->vencimento               = $dadosFiltro->abodt_vencimento;
					
					//atualiza o campo titcabooid (Id da campanha criada) da tabela titulo_consolidado
					$this->dao->setDadosTituloConsolidado($this->id_campanha, $dadosConsolidados['titulo']);
					
					//recupera dados do cliente 
					$dadosCliente = $this->dao->getDadosCliente($dados_titulos_cliente->clioid);
							
					$email_cliente = $dadosCliente->cliemail_nfe;
					
					if($parametrosCampanha->aboformato_envio === 'E'){
						$status = 'OK';
						if($email_cliente == ''){
							$status = 'Cliente sem email cadastrado';
						}
					}else{
						$status = 'Boleto gerado';
					}
					
					//gerar .csv
					$linha_csv = array(
							$dados_titulos_cliente->clioid,
							$dadosCliente->clinome,
							$paramsBoleto->valor_recalc,
							$paramsBoleto->vencimento,
							$status
					);
					
					//adiciona a linha ao arquivo	
					$csvWriter->addLine($linha_csv);
					
					// gera o boleto em html
					$dados_boleto = gerarBoletoMassivo ( $paramsBoleto );
					
					
					//gera xml somente se for para a Gráfica
					if ($parametrosCampanha->aboformato_envio === 'G') {
					
						// gera xml
						$dadosXML->id_linha = $i + 1;
						$dadosXML->data_referencia = $dadosFiltro->abodt_vencimento;
						$dadosXML->clitipo         = $dadosCliente->clitipo;
						$dadosXML->clinome         = $dadosCliente->clinome;
						$dadosXML->clino_doc       = $dadosCliente->clino_doc;
						$dadosXML->log_fiscal      = $dadosCliente->log_fiscal;
						$dadosXML->bairro_fiscal   = $dadosCliente->bairro_fiscal;
						$dadosXML->cep_fiscal      = $dadosCliente->cep_fiscal;
						$dadosXML->cidade_fiscal   = $dadosCliente->cidade_fiscal;
						$dadosXML->end_cor         = $dadosCliente->end_cor;
						$dadosXML->log_cor         = $dadosCliente->log_cor;
						$dadosXML->bairro_cor      = $dadosCliente->bairro_cor;
						$dadosXML->cep_cor         = $dadosCliente->cep_cor;
						$dadosXML->cidade_cor      = $dadosCliente->cidade_cor;
						$dadosXML->titoid          = $dados_boleto ['titoid'];
						$dadosXML->nossoNumeroDv   = $dados_boleto ['nossoNumeroDv'];
						$dadosXML->valorPagar      = $dados_boleto ['valorPagar'];
						$dadosXML->linhaDigitavel  = $dados_boleto ['linhaDigitavel'];
						$dadosXML->codigoBarras    = $dados_boleto ['codigoBarras'];
						$dadosXML->forma_cobranca  = 74; // Cobrança Registrada HSBC
						$dadosXML->abotipo_cliente = $parametrosCampanha->abotipo_cliente;
						
						$this->gerarXML ( $handle, $dadosXML );
					
					}
					
					 
				}//fim do for $nova_lista_clientes
				file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Finalizando calculo de divida.' . "\n", FILE_APPEND);
			
			}//fim do if $nova_lista_clientes
			
			
			//fecha o arquivo .csv
			$csvWriter->closeFile();
			
			//fecha xml somente se for campanha gerada para a Gráfica
			if ($parametrosCampanha->aboformato_envio === 'G') {
				//fecha o arquivo xml
				$this->rodapeXML($handle);
			}
			
			//zip os arquivos somente se foram encontrados clientes
			file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Criando arquivo zip.' . "\n", FILE_APPEND);
			if(count($nova_lista_clientes) > 0){
			
				// zipa os arquivos gerados
				$arquivoZip = $parametrosCampanha->abonm_campanha.'_'. date('Ymd');
				$dir = $this->pasta_arquivo;
					
				// Apaga o arquivo .zip caso exista para criar novo
				if (file_exists($dir . $arquivoZip . '.zip')) {
					unlink($dir . $arquivoZip . '.zip');  //apaga o arquivo 
				}
				
				//GERA O ZIP DO ARQUIVO
				if (class_exists('ZipArchive')) {
				
					$zip = new ZipArchive();
					$zip->open($dir . $arquivoZip . '.zip', ZIPARCHIVE::CREATE);
						
					if ($this->nome_arquivo_csv) {
						$zip->addFile($this->nome_arquivo_csv, str_replace($dir, '', $this->nome_arquivo_csv));
					}
						
				
					if ($this->nome_arquivo_xml) {
						$zip->addFile($this->nome_arquivo_xml, str_replace($dir, '', $this->nome_arquivo_xml));
					}
					
					$zip->close();
					
				} elseif (!$exec_zip = shell_exec("cd " . $dir . " && zip {$arquivoZip}.zip {$this->nome_arquivo_csv}")
				&& !$exec_zip = shell_exec("cd " . $dir . " && zip {$arquivoZip}.zip {$this->nome_arquivo_xml}")) {
					throw new Exception('Erro ao gerar arquivo.');
				}
				
			}
			file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Encerrando arquivo zip.' . "\n", FILE_APPEND);
				
			// Apaga os arquivos .csv e deixa apenas o ZIP na pasta
			if (file_exists($this->nome_arquivo_csv)) {
				unlink($this->nome_arquivo_csv);
			}
			
			// Apaga os arquivos .xml e deixa apenas o ZIP na pasta
			if (file_exists($this->nome_arquivo_xml)) {
				unlink($this->nome_arquivo_xml);  //apaga o arquivo
			}
			
			
			//atualiza os dados da campanha somente se foram encontrados clientes
			if(count($nova_lista_clientes) > 0){
				//atualiza dados da campanha
				$novoDadosCampanha = new stdClass();
				
				$novoDadosCampanha->aboarquivo      = $arquivoZip.'.zip';
				$novoDadosCampanha->abooid          = $this->id_campanha;
				
				//insere o nome do arquivo zip na tabela
				$this->dao->setArquivoBoletagem($novoDadosCampanha);
			}
			
			
			$msg = 'Processo de Gerar Campanha Boletagem finalizado com sucesso.';
			
			//finaliza processo com sucesso
		    $finalizarProcesso = $this->dao->finalizarProcesso(true, $msg);
			
			//recupera os dados do processo finalizado com sucesso para enviar por e-mail
			$dadosProcesso = $this->dao->verificarProcessoFinalizado('t','');
			
			$dadosProcesso->earoid = $this->earoid;
			$dadosProcesso->msg = $msg;
			
			file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Enviando email.' . "\n", FILE_APPEND);
			if(empty($nova_lista_clientes)){
	
				//envia email de sucesso com mensagem de clientes não encontrados
				$enviarEnmail = $this->enviarEmail('', false, $dadosProcesso,'','campanha');
			
			}else{
				//envia email de sucesso
				$anexo = $dir.$arquivoZip.'.zip';
				$enviarEnmail = $this->enviarEmail('', true, $dadosProcesso,$anexo,'campanha');
			}
			
			return true;
			
		} catch (Exception $e) {
			echo $e->getMessage();
			file_put_contents("/var/www/faturamento/arquivo_grafica_boletagem_massiva/arquivo_grafica_boletagem_massiva_log.txt", date('Y-m-d H:i:s') . ' Erro no processamento.'. $e->getMessage() ."\n", FILE_APPEND);
			$this->dao->finalizarProcesso(false, $e->getMessage());
			$enviarEnmail = $this->enviarEmail($e->getMessage(), true, $dadosProcesso,'','erro_processo');
		}
		
	}
	
	
	
	/**
	 * Prepara o processo para envio de e-mails para os clientes
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function prepararEnvioBoletoEmail(){
	
		//quando o envio for por e-mail pega o id da campanha via post da página de busca
		$idCampanha = isset($_POST['id_campanha']) && $_POST['id_campanha'] != '' ? $_POST['id_campanha'] : 'NULL';
			
		
		try {
			
			if($this->id_campanha == 'NULL'){
				throw new Exception('O id da campanha deve ser informado.');
			}
		
			//verifica se tem processo em andamento
			$processo = $this->dao->verificarProcessoAndamento();
		
			//se o usuário dar f5 na tela, e ainda tiver um processo em andamento, exibe a mensagem e redireciona para a tela de busca
			//se tiver um processo em andamento, redireciona para a tela de busca e exibe mensagem na tela
			if(is_object($processo)){
				 
				//verifica o tipo de processamento para exibir a mensagen
				$tipo_processo =  explode('|', $processo->earparametros);
				 
				$processo->tipo_processo = $tipo_processo[1];
				 
				throw new Exception($this->getMsgProcesso($processo));
			}
		
			$parametro['id_campanha'] = $idCampanha;
			$parametro['tipo_processo'] = 'envio_email';
			$parametro['msg'] = 'Processo de envio de e-mails iniciado';
				
			$inicia = $this->dao->iniciarProcesso($parametro);
				
			$processo = $this->dao->verificarProcessoAndamento();
				
			//recebe o id do processo iniciado
			$this->earoid = $processo->earoid;
				
			$processo->tipo_processo = $parametro['tipo_processo'];
				
			$sucesso['msg']  =  $this->getMsgProcesso($processo);
			$sucesso['tipo'] =  'sucesso';
				
			//inicia processo em background
			$this->prepararProcessoCampanha();
				
			//trava o botão Novo
			$_SESSION['botao_nova_campanha'] = false;
				
			$this->index($sucesso);
				
				
		} catch ( Exception $e ) {
				
			$erro['msg']  =  $e->getMessage();
			$erro['tipo'] =  'erro';
				
			$_SESSION['botao_nova_campanha'] = false;
				
			$this->index($erro);
		
			return false;
		}
	
	}
	
	
	/**
	 * Gera boleto PDF com base nos dados encontrados pelo campanha gerada na tabela titulo_consolidado e envia para o e-mail do cliente
	 * 
	 * @throws exception
	 */
	private function enviarBoletoConsolidadoCliente(){ 
			
		try {
			
			//busca os títuldos consolidados vinculados a campanha informada
			$dados_titulo = $this->dao->getTituloConsolidadoCampanha($this->id_campanha);
			
			$paramsBoleto = new stdClass();
			
			foreach ($dados_titulo AS $dados){
				
				///recupera dados para enviar para impressão do boleto
				$paramsBoleto->titoid                   = $dados['titulo'];
				$paramsBoleto->valor_total_original_tit = $dados['valor_titulo'];
				$paramsBoleto->valor_recalc             = $dados['valor_pagar'];
				$paramsBoleto->valor_multa              = $dados['valor_multa'];
				$paramsBoleto->valor_juros              = $dados['valor_juros'];
				
				//$paramsBoleto->valor_desconto           = $dados['valor_desconto'];
				$paramsBoleto->vencimento               = $dados['data_vencimento'];
				$paramsBoleto->perc_desconto            = $dados['perc_desconto'];
				
				//recupera dados do cliente
				$dadosCliente = $this->dao->getDadosCliente($dados['titcclioid']);
					
				$email_cliente = $dadosCliente->cliemail_nfe;
				
				
				if($email_cliente != ''){
				
					// gera o boleto em html
					$dados_boleto = gerarBoletoMassivo ( $paramsBoleto );
		
					//atualiza os dados de envio se for por e-mail,  na tabela arquivo_boletagem
					$this->dao->setDadosEnvioArquivo($this->id_campanha);
	
					$attachments = array ();
					$filenames = array ();
	
					// INICIO PDF BOLETO
					$filenames ['boletoPDF'] = 'boleto.pdf';
					$pdf = new TCPDF ( 'P', PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false );
					$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN,'',PDF_FONT_SIZE_MAIN));
					$pdf->setFooterFont (Array(PDF_FONT_NAME_DATA,'',PDF_FONT_SIZE_DATA));
					$pdf->SetMargins ( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
					$pdf->SetHeaderMargin ( PDF_MARGIN_HEADER );
					$pdf->SetFooterMargin ( PDF_MARGIN_FOOTER );
					$pdf->SetAutoPageBreak ( TRUE, PDF_MARGIN_BOTTOM );
					$pdf->setPrintHeader ( false );
					$pdf->setPrintFooter ( false );
					$pdf->SetFont ( 'Arial', '', 7 );
					$pdf->AddPage ();
					$pdf->writeHTML ( $dados_boleto ['html'] );
					$pdf->lastPage ();
					$attachments ['boletoPDF'] = $pdf->Output ( '', 'S' );
	
					// INICIO EMAIL
					$mail = new PHPMailer ();
					$mail->isSMTP ();
	
					// recupera e-mail de testes
					if ($_SESSION ['servidor_teste'] == 1) {
						// recupera email de testes da tabela parametros_configuracoes_sistemas_itens
						$emailTeste = $this->dao->getEmailTeste ();
						if (! is_object ( $emailTeste )) {
							throw new exception ( 'E necessario informar um e-mail de teste em ambiente de testes.' );
						}
						// pega e-mail de testes
						$email_cliente = $emailTeste->pcsidescricao;
					}
	
					// Email do cliente
					$mail->AddAddress("$email_cliente");
					$mail->From = 'sascar@sascar.com.br';
					$mail->FromName = 'SASCAR';
					$mail->Subject = 'SASCAR – Oportunidade Imperdível para quitação dos seus débitos';
	
					$corpo_email = $this->dao->getParamentos(103); //texto carta
					
					$mailMessage = str_replace('[NOME_CLIENTE]',$dadosCliente->clinome, $corpo_email[0]['valvalor']);
					$mailMessage = str_replace('[PERCENTO_DESCONTO]',$paramsBoleto->perc_desconto, $mailMessage);
					$mailMessage = str_replace('[VALOR_BOLETO]',$this->moeda($paramsBoleto->valor_recalc), $mailMessage);
					$mailMessage = str_replace('[DATA_VENCIMENTO]',$paramsBoleto->vencimento, $mailMessage);
					
					
					$cabecalhoMail = '<div style="text-align:center;"><p><img src="http://'._SITEURL_.'images/boletagem_massiva/cabecalho_email_campanha.jpg" border="0" /></p></div>';
					
					$rodapeMail = '<div style="text-align:center;"><p><img src="http://'._SITEURL_.'images/boletagem_massiva/rodape_email_campanha.jpg" border="0" /></p></div>';
					
					$corpoEmailMessage  = $cabecalhoMail;
					$corpoEmailMessage .= $mailMessage.'<br/>';
					$corpoEmailMessage .= $rodapeMail;
					
					
					$mail->MsgHTML($corpoEmailMessage);
					$mail->AddStringAttachment($attachments['boletoPDF'], $filenames['boletoPDF'], 'base64', 'application/pdf' );
					$mail->Send();
	
				}else{
					$clientes_sem_email[] = $dados['titcclioid'].' - '.$dadosCliente->clinome;
				}
			}
			
 			$msg = 'Processo de envio de e-mail finalizado com sucesso.';
				
			//finaliza processo com sucesso
			$finalizarProcesso = $this->dao->finalizarProcesso(true, $msg);
				
			//recupera os dados do processo finalizado com sucesso para enviar por e-mail
			$dadosProcesso = $this->dao->verificarProcessoFinalizado('t','');
				
			$dadosProcesso->earoid = $this->earoid;
			$dadosProcesso->msg = $msg;
			$dadosProcesso->clientes_sem_email = $clientes_sem_email;

			//envia email de sucesso
			$enviarEnmail = $this->enviarEmail('', true, $dadosProcesso,'','envio_email');

			return true;
			
		} catch (Exception $e) {
			
			echo $e->getMessage();
			exit;
		}
		
	}
	
	
	
	/**
	 * 
	 * @param object $processo
	 * @return string
	 */
	public function getMsgProcesso($processo){
		
		if($processo->tipo_processo == 'campanha'){
			$msg = 'Boletagem Massiva';
			$fim_msg = 'para gerar uma nova campanha';
		}
		
		if($processo->tipo_processo == 'envio_email'){
			$msg = 'envio de E-mails';
			$fim_msg = 'para envio de novos e-mails';
		}
		
		$msg = 'Processo de '.$msg.' foi iniciado por:  '.$processo->nm_usuario.' em :  '.$processo->data_inicio.' às '.$processo->hora_inicio.', aguarde o final do processamento '.$fim_msg.'.';
		
		return $msg;
	}
	
	
	/**
	 * Retorna as siglas e o nomes dos estados brasileiros
	 * 
	 * @return array
	 */
	public function getUf(){
		$retorno = $this->dao->getUf();
		return $retorno;
	}
	
	
	/**
	 * Retorna todos o angins da politica de desconto cadastradas
	 * 
	 * @return Ambigous <multitype:, NULL, multitype:unknown multitype:unknown NULL  >
	 */
	public function getAgingPolitica(){
		$retorno = $this->daoManutencaoPolitica->getAll();
		return $retorno;
	}
	
	/**
	 * Recupera os dados da politica de desconto pelo id informado
	 * 
	 * @param unknown $podoid
	 * @return Ambigous <multitype:, unknown, multitype:unknown mixed >
	 */
	public function getAgingPolitivaByID($podoid){
		$retorno = $this->daoManutencaoPolitica->getById($podoid);
		return $retorno;
	}
	
	
	/**
	 * Envia e-mail previamente parâmetrizado no BD 
	 *
	 * @param string $msg
	 * @param boolean $status
	 * @param object $dadosProcesso
	 * @param string $tipo
	 * @throws exception
	 */
	
	private function enviarEmail($msg=null, $status, $dadosProcesso, $anexo = NULL, $tipo = NULL){
		
		$dadosEmail = Array();
		
		//recupera os dados do usuário que iniciou o processo
		$emailUsuarioProcesso = $this->dao->getDadosUsuarioProcesso($this->usuarioID);
	
		if(is_array($emailUsuarioProcesso)){
	
			$nomeUsuarioProcesso = $emailUsuarioProcesso[0]['nm_usuario'];
	
			//verifica se o usário possui email cadastrado
			if(empty($emailUsuarioProcesso[0]['usuemail'])){
				 
				$msg_erro_email = 'Falha ao enviar e-mail : Usuário [ '.$this->dao->usuarioID.' ] que iniciou o processo, não possui e-mail cadastrado.';
				 
				//finaliza processo com sucesso mas com mensagem de erro de envio de email
				$finalizarProcesso = $this->dao->finalizarProcesso(true, $msg_erro_email, $dadosProcesso->earoid);
				 
				return true;
	
			}else{
				
				
				if($tipo === 'campanha'){
					
					$assunto = 'Boletagem Massiva';
					
					$corpo_email = 'O processo de boletagem massiva foi iniciado às '.$dadosProcesso->inicio_hora.' do dia  '.$dadosProcesso->inicio_data.' foi finalizado com sucesso às '.$dadosProcesso->termino.' ';
					
					if($status){
						$corpo_email .= ', o arquivo está disponível no modulo de boletagem massiva. <br/>';
					}else{
						$corpo_email .= ', porém não foram encontrados registros, nenhum boleto foi gerado.';
					}
					
				}
				
				 
				if($tipo === 'envio_email'){

					$assunto = 'Boletagem Massiva - Envio de e-mail para o cliente.';
						
					$corpo_email = 'O processo de envio de e-mail que foi iniciado às '.$dadosProcesso->inicio_hora.' do dia  '.$dadosProcesso->inicio_data.' foi finalizado com sucesso às '.$dadosProcesso->termino.'</br></br> ';
					
					//exibe lista de clientes sem email cadastrado
					if(!empty($dadosProcesso->clientes_sem_email)){
						
						$corpo_email .= " O(s) cliente(s) abaixo não possui(em) e-mail cadastrado, o boleto não foi enviado. <br/></br>";
						
						foreach ($dadosProcesso->clientes_sem_email AS $sem_email){
							$corpo_email .= $sem_email.'</br>';
						}
					}
				}
				
				
				if($tipo === 'erro_processo'){
					
					$assunto = 'Boletagem Massiva';
					
					$corpo_email = 'O processo de boletagem massiva foi iniciado às '.$dadosProcesso->inicio_hora.' do dia  '.$dadosProcesso->inicio_data.' <br/> Ocorreu erro na execução. ('.$msg.') <br/> O processamento da rotina foi interrompido e a tela desbloqueada para geração de nova campanha.';
			
				}
				
				 
				//recupera e-mail de testes
				if($_SESSION['servidor_teste'] == 1){
					 
					//recupera email de testes da tabela parametros_configuracoes_sistemas_itens
					$emailTeste = $this->dao->getEmailTeste();
					 
					$emailUsuarioProcesso[0]['usuemail'] = $emailTeste->pcsidescricao;
	
					if(!is_object($emailTeste)){
						throw new exception('E necessario informar um e-mail de teste em ambiente de testes.');
					}
				}
				 
				 
				$mail = new PHPMailer();
				$mail->isSMTP();
				$mail->From = "sascar@sascar.com.br";
				$mail->FromName = "sistema@sascar.com.br";
				$mail->Subject = $assunto;
				$mail->MsgHTML($corpo_email);
				$mail->ClearAllRecipients();
				$mail->AddAddress($emailUsuarioProcesso[0]['usuemail']);
				$mail->AddAttachment($anexo);
				
				
				if(!$mail->Send()) {
					 
					$msg_erro_email = $dadosProcesso->msg.' - Falha ao enviar e-mail -';
	
					//atualiza o processo com mensagem de erro de envio de email
					$this->dao->finalizarProcesso(true, $msg_erro_email, $dadosProcesso->earoid);
						
				}
				 
				return true;
				 
			}
			 
		}
		 
		return false;
	}
	
	
	/**
	 * 
	 * @param object $dados_xml
	 * @return resource
	 */
	private function cabecalhoXML($dados_xml){
		
		//caminho do arquivo xml
		$this->nome_arquivo_xml = $this->pasta_arquivo.$dados_xml->abonm_campanha."_".date("Ymd").".xml";
		
		$handle = fopen($this->nome_arquivo_xml, 'a');
			
		// Escreve o cabeçalho
		fwrite($handle, '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n");
		fwrite($handle, '<job cliente="dd" template="dd" dataGeracao="' . date('Y-m-d') . '" numRows="' .$dados_xml->quant_registros. '">'."\n");
		
		return $handle;
		
	}
	
	
	/**
	 * 
	 * @param unknown $handle
	 */
	private function rodapeXML($handle){
		fwrite($handle, '</job>'."\n");
		fclose($handle);
		return ;
	}
	
	
	public function gerarXML($handle,$row){
		
		//inverte o fomato da data
		$data_ref = $this->inverterData($row->data_referencia);
		
		$desconto_politica = explode('.', $this->desconto_politica);
		
		$perc_desconto = $desconto_politica[0];
		
		$meses = array(1 => "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
		
		$time = strtotime($data_ref);
		$ano = date('y', $time);
		$mes = (int) date('m', $time);
		//$mes = utf8_encode(strtoupper($meses[$mes]));
		$mes = strtoupper($meses[$mes]);
		
		$referencia = $mes . '/' . $ano;
		
		fwrite($handle, '    <row id="' . $row->id_linha . '">'."\n");
		fwrite($handle, '        <int name="NumeroNF"></int>'."\n");
		fwrite($handle, '        <str name="NomeSacado">' . $row->clinome . '</str>'."\n");
		
		if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
	       fwrite($handle, '    <str name="Endereco">' . $row->log_fiscal . ', '.$row->num_fiscal.' </str>'."\n");
	    }else{
	       fwrite($handle, '    <str name="Endereco">' . $row->log_cor . '</str>'."\n");
	    }
	    if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
	 	   fwrite($handle, '    <str name="Bairro">' . $row->bairro_fiscal .'</str>'."\n");
	    }else{
	  	   fwrite($handle, '    <str name="Bairro">' . $row->bairro_cor . '</str>'."\n");
	    }
	    if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
	  	   fwrite($handle, '    <str name="Cidade">' . $row->cidade_fiscal .'</str>'."\n");
	    }else{
	 	   fwrite($handle, '    <str name="Cidade">' . $row->cidade_cor . '</str>'."\n");
	    }
	    if(empty($row->end_cor) || $row->end_cor == '' ||  $row->end_cor == null || !isset($row->end_cor)) {
	 	   fwrite($handle, '    <str name="CEP">' . trim($row->cep_fiscal).'</str>'."\n");
	    }else{
	 	   fwrite($handle, '    <str name="CEP">' .trim($row->cep_cor).'</str>'."\n");
	    }
		
		fwrite($handle, '        <str name="Endereco_NF">' . $row->log_fiscal . ', '.$row->num_fiscal.' </str>'."\n");
		fwrite($handle, '        <str name="Bairro_NF">' . $row->bairro_fiscal . '</str>'."\n");
		fwrite($handle, '        <str name="Cidade_NF">' . $row->cidade_fiscal . '</str>'."\n");
		fwrite($handle, '        <str name="CEP_NF">' . $row->cep_fiscal . '</str>'."\n");
		fwrite($handle, '        <str name="CNPJ">' . $row->clino_doc . '</str>'."\n");
		fwrite($handle, '        <int name="NumeroContrato"></int>'."\n");
		fwrite($handle, '        <str name="Referencia">' . $referencia . '</str>'."\n");
		fwrite($handle, '        <date name="DataEmissao">' . date('d/m/Y') . '</date>'."\n");
		fwrite($handle, '        <str name="Impressao">ANALITICO</str>'."\n");
		fwrite($handle, '        <str name="EnviaCliente">SIM</str>'."\n");
		fwrite($handle, '        <date name="Vencimento">' . $row->data_referencia . '</date>'."\n");
		fwrite($handle, '        <str name="NossoNumero">' . $row->nossoNumeroDv . '</str>'."\n");
		fwrite($handle, '        <str name="NumeroDocumento">' . $row->titoid . '</str>'."\n");
		fwrite($handle, '        <dec name="ValorPagar">' . number_format($row->valorPagar,2,'.','') . '</dec>'."\n");
		fwrite($handle, '        <int name="TipoMensagem">0</int>'."\n");
		fwrite($handle, '        <dec name="ValorFatura">' . number_format($row->valorPagar,2,'.','') . '</dec>'."\n");
		fwrite($handle, '        <dec name="ValorTitulo">' . number_format($row->valorPagar,2,'.','') . '</dec>'."\n");
		fwrite($handle, '        <dec name="ValorDesconto">0</dec>'."\n");
		fwrite($handle, '        <dec name="ValorDescontoNF">0</dec>'."\n");
		fwrite($handle, '        <dec name="ValorOutrasDeducoes">0</dec>'."\n");
		fwrite($handle, '        <str name="InformacaoImposto"></str>'."\n"); 
		fwrite($handle, '        <str name="Boleto">SIM</str>'."\n");
		fwrite($handle, '        <str name="LinhaDigitavel">' . $row->linhaDigitavel . '</str>'."\n");
		fwrite($handle, '        <str name="CodigoBarras">' . $row->codigoBarras . '</str>'."\n");
		fwrite($handle, '        <str name="TipoEmpenho">N</str>'."\n");
		fwrite($handle, '        <str name="Empenho"></str>'."\n");
		fwrite($handle, '        <str name="FormaPagamento">'.$row->forma_cobranca.'</str>'."\n");
		fwrite($handle, '        <str name="Mensagem1">NÃO RECEBER APÓS O VENCIMENTO</str>'."\n");
		fwrite($handle, '        <str name="Mensagem2"></str>'."\n");
		fwrite($handle, '        <str name="Mensagem3"></str>'."\n");
		fwrite($handle, '        <str name="Mensagem4"></str>'."\n");
		fwrite($handle, '        <str name="Mensagem5"></str>'."\n");
		fwrite($handle, '        <str name="Mensagem6"></str>'."\n");
		fwrite($handle, '        <str name="Mensagem7"></str>'."\n");
		fwrite($handle, '        <str name="Mensagem8"></str>'."\n");
		fwrite($handle, '        <str name="Mensagem9"></str>'."\n");
		fwrite($handle, '        <str name="Mensagem10"></str>'."\n");
		fwrite($handle, '        <str name="Mensagem11"></str>'."\n");
		
		$corpo_campanha = $this->dao->getParamentos(103); //texto carta
		
		$texto_campanha = str_replace('[NOME_CLIENTE]',$row->clinome, $corpo_campanha[0]['valvalor']);
		$texto_campanha = str_replace('[PERCENTO_DESCONTO]',$perc_desconto, $texto_campanha);
		$texto_campanha = str_replace('[VALOR_BOLETO]',$this->moeda($row->valorPagar), $texto_campanha);
		$texto_campanha = str_replace('[DATA_VENCIMENTO]',$row->data_referencia, $texto_campanha);
		
		fwrite($handle, '        <str name="texto_campanha">'.$texto_campanha.'</str>'."\n");
		
		fwrite($handle, '        <str name="tipo_arte">'.$row->abotipo_cliente.'</str>'."\n");
		
		fwrite($handle, '    </row>');
		
		return $handle;
		
	}
	
	
	/**
	 *  Metodo para enviar arquivo via ftp para gráfica
	 *  @return true ou false
	 * 
	 * @param int $id_campanha
	 * @return boolean
	 */
	public function enviarArquivoFTP(){
		
		$id_campanha   = isset($_POST['id_campanha']) && $_POST['id_campanha'] != '' ? $_POST['id_campanha'] : 'NULL';
		
		if($id_campanha == 'NULL'){
			echo 'Erro -> O id da campanha deve ser informado.';
			exit();
		}
		 
		$caminho = $this->pasta_arquivo;
		 
		$infFtp = $this->RetornaInformacoesFPT();

		ksort($infFtp);
	
		$servidor = $infFtp[0];
		$usuario = $infFtp[1];
		$senha = $infFtp[2];
		//$sascar = $infFtp[3];
	 
		$pasta_serv_ext = $this->dao->getParamentos(106);//recupera pasta onde grava arquivo no servidor externo 
		
		if(!array($pasta_serv_ext)){
			echo utf8_encode('Pasta externa do servidor FTP não encontrada.');
			exit();
		}
		
		$sascar = $pasta_serv_ext[0]['valvalor'];
		
		//$siggo = $infFtp[4];
	
		$resposta = true;
	
		$res = $this->dao->getNomeArquivo($id_campanha);
	
		$nome_arquivo = $res->aboarquivo;
		
		$conecta = ftp_connect($servidor,21);
	
		if(!$conecta){
			echo utf8_encode('Erro ao conectar com o servidor FTP. Arquivo não enviado.');
			exit();
		}
	
		/* Autenticar no servidor */
		$login = ftp_login($conecta,$usuario,$senha);
		 
		if(!$login){
			echo utf8_encode('Erro ao fazer login com o servidor FTP. Arquivo não enviado.');
			exit();
		}
		 
		ftp_pasv($conecta, TRUE);
		// get contents of the current directory
		$Diretorio=ftp_pwd($conecta); //Devolve rota atual p.e. "/home/willy"
		
		// Define variáveis para o envio de arquivo
		$local_arquivo = $caminho; // Localização (local)
		$ftp_pasta = $sascar."/"; // Pasta (externa)
		$ftp_arquivo = $nome_arquivo; // Nome do arquivo (externo)
		
		
		//se o arquivo não existir, exibe msg
		if(!file_exists($local_arquivo.$nome_arquivo)){
			
			ftp_close($conecta);
			
			echo utf8_encode('Erro => Arquivo não encontrado para o envio.');
			exit();
			
		}
		
		// Retorno: true / false
		if($local_arquivo) {
			
			$envio = ftp_put($conecta, $Diretorio.$ftp_pasta.$ftp_arquivo, $local_arquivo.$nome_arquivo, FTP_BINARY);
		
			if($envio){
				$resposta = true;
			}else{
				$resposta = false;
			}
		}
		
		
		ftp_close($conecta);
		
		if($resposta) {
				
			// atualiza os dados de envio na tabela arquivo_boletagem
			$this->dao->setDadosEnvioArquivo($id_campanha);
			
			$msg = "Arquivo enviado para gráfica.";
			
		}else{
			$msg = "Houve um erro ao enviar arquivo para o ftp. Arquivo não enviado.";
		}
			
		echo utf8_encode($msg);
		exit();
			
	}
	
	

	/**
	 *  Metodo para retornar informações de usuario, servidor, senha do ftp
	 *  @return array
	 */
	public function RetornaInformacoesFPT() {
	
		$res = $this->dao->getInformacoesFPT();
	
		return $res;
	}
	
	
	/**
	 * Helper para limpeza dso valores para gravar em banco
	 *
	 * @param money $get_valor
	 * @return mixed
	 */
	public function limpaValor($get_valor) {
	
		$source = array('.', ',');
		$replace = array('', '.');
		$valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto
		return $valor; //retorna o valor formatado para gravar no banco
	}
	
	
	/**
	 * Recebe data no formato 21/08/2015 e retorna 2015-08-21
	 *
	 * @param date $data
	 * @return string
	 */
	public function inverterData($data){
		
		$data = substr($data,0,2) . "-" . substr($data,3,2) . "-" . substr($data,6,4);
		
		$nova_data = date('Y-m-d', strtotime($data));
		 
		return $nova_data;
	}
	
	
	public function moeda($number) {
	
		if (count($test)==1) $number = $number.".00";
	
		if (($number*1)==0) {
			return "0,00";
		}
		if (count($test)>1 && ($test[1]*1)>99) $number = round($number,2);
		$number = str_replace(".",",",$number);
		while (true) {
			$replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1.$2', $number);
			if ($replaced != $number) {
				$number = $replaced;
			} else {
				break;
			}
		}
		$test2 = explode(",",$number);
		if (strlen($test2[1])==1) {
			$number .= "0";
		}
	
		return $number;
	}
	
	
	
}




?>