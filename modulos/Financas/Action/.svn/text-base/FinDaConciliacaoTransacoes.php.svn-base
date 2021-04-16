<?php
/**
 *  Valida e importa vários aquivos com dados de pagamento de Débito Automático retornados pelos bancos :
 *  Bradesco, Itaú, HSBC, Banco do Brasil e Santander, obedecendo a padronização de cada banco (layout),
 *  validando os aquivos com as extensões: .txt, .csv , .ret e .dat
 * 
 */
require _MODULEDIR_ . 'Financas/DAO/FinDaConciliacaoTransacoesDAO.php';
 

 class FinDaConciliacaoTransacoes {
     
     /**
	 * Atributo para acesso a persistência de dados
	 */
	private $dao;
	private $conn;	
	private $valorTotalTransacao; //acumula a soma de todos dos valores recebidos (junto com encargos) linha a linha
	private $permiteMovimentacao; //controle para gravar movimentação bancária só em caso de sucesso
	
	/**
	 * Construtor
	 */
	public function __construct() {
	
		global $conn;
	
		$this->conn = $conn;
		$this->dao = new FinDaConciliacaoTransacoesDAO($conn);
		
		$this->valorTotalTransacao = 0;
		$this->permiteMovimentacao = FALSE;
	}
	
	
	public function view($action, $resultadoPesquisa = array(),$layoutCompleto = true) {

		if (!empty($_FILES)) {
				
			$retorno = $this->importarDados($_FILES);
		}

		if($layoutCompleto){
			include _MODULEDIR_.'Financas/View/fin_da_conciliacao_transacoes/header.php';
		}
		 
		if($action == 'index'){
			include _MODULEDIR_.'Financas/View/fin_da_conciliacao_transacoes/index.php';
		}

		if($layoutCompleto){
			include _MODULEDIR_.'Financas/View/fin_da_conciliacao_transacoes/footer.php';
		}
		 
	}
	 
	public function index() {
    	$this->view('index', '', true, false);
    }
	
    //$_FILES
	public function importarDados($_FILES){
		
		$data_credito_cc = isset($_POST['data_credito']) ? $_POST['data_credito'] : 'NULL' ;
		
		$data = explode("/","$data_credito_cc");


		$dataValida = checkdate($data[1],$data[0],$data[2]);
		
		$periodoMaximo = 15; //dias antes da data atual 
		
		$tipoarquivo = '".txt",".csv",".ret",".dat"';
		$allowed_filetypes = array('.txt','.csv','.ret','.dat'); // Estes serão os tipos de arquivo que vai passar a validação.
		$max_filesize = 524288; // Tamanho máximo do arquivo em bytes (atualmente 0,5 MB).

		$upload_path =  $this->diretorio(33);	// O local dos arquivos serão enviados para o (atualmente um diretório 'arquivos').
		
		//lista de mensagen
		$dataCredito = "Informe a data de crédito na C/C ! ";
		$dataCreditoInvalida = "Data de crédito com formato inválido ! ";
		$dataCreditoMaiorAtual = "Data de crédito na C/C maior que a data atual. Favor redigitar ! ";
		$dataCreditoForaPeriodo = ' Data de crédito na C/C menor que '.$periodoMaximo .' dias da data atual. Favor redigitar ! ';
		
		$arquivos = "Favor selecionar apenas arquivos no formato ".$tipoarquivo;
		$grande = "O arquivos que você tentou fazer o upload é muito grande.";
		$CHMOD = "Você não pode fazer o upload para o diretório especificado, por favor CHMOD para 777.";
		$abrir = "Não foi possível abrir o arquivo.";
		$cancelada = "Erro no layout do arquivo. Operação cancelada.";
		$BancoAtivo = "O Banco [NomeBanco] não esta habilitado para gerar/importar o arquivo de remessa.";
		$BancoNao = "O Banco [NomeBanco] não esta cadastrado no sistema.";
		$erro = "Houve um erro ao fazer o upload do arquivo, por favor, tente novamente!";
		$selecionar = "Favor selecionar ao menos um arquivo antes de enviar.";
		$sucesso = "Arquivo(s) processado(s) com sucesso.";
		
		
		if($data_credito_cc == 'NULL'){
			$msg = array('msg' =>$dataCredito,'tipo' => 'alerta','upload'=>'erro');
			return $msg; //Não foi informada a data de crédito.
		}
		
		If(!$this->validaData(date('Ymd',strtotime(str_replace('/','-',$data_credito_cc))))){
			$msg = array('msg' =>$dataCreditoInvalida,'tipo' => 'alerta','upload'=>'erro');
			return $msg; //Data com formato inválido.
		}
		
		$retorno_dias = $this->validarPeriodo($data_credito_cc, $periodoMaximo);
				
		if($retorno_dias === 2){
			$msg = array('msg' =>$dataCreditoMaiorAtual,'tipo' => 'alerta','upload'=>'erro');
			return $msg; //Data com formato inválido.
		}
		
	    if($retorno_dias === 3){
	    	$msg = array('msg' =>$dataCreditoForaPeriodo,'tipo' => 'alerta','upload'=>'erro');
	    	return $msg; //Data fora do período informado.
	    	
		}
		
		pg_query($this->conn, "BEGIN");
		
		
		$i = 0;
		 
		foreach ($_FILES as $key) {

		 	$filename = $key['name']; // Pega o nome do arquivo (incluindo extensão de arquivo).

		 	if ($filename !="") {

				   $ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1)); // Obter a extensão do nome do arquivo.
				   
				   // Verifique se o tipo de arquivo é permitido e informar o usuário.
				   if(!in_array($ext,$allowed_filetypes)){
					   	$msg = array('msg' => $arquivos,'tipo' => 'alerta','upload'=>'');
					   	return $msg;
				   }
				   	
				   // Agora, verifique o tamanho do arquivo, se for muito grande, então, informa o usuário.
				   if(filesize($key['tmp_name']) > $max_filesize){
					   	$msg = array('msg' => $grande,'tipo' => 'alerta','upload'=>'');
					   	return $msg;
				   }
				   
				   // Verifica se é que podemos fazer o upload para o caminho especificado, se não morrer e informar o usuário.
				   if(!is_writable($upload_path)){
					   	$msg = array('msg' => $CHMOD,'tipo' => 'erro','upload'=>'');
					   	return $msg;
				   }
				   	
				   // Carregar o arquivo em seu caminho especificado.
				   if(move_uploaded_file($key['tmp_name'], $upload_path.$filename)){
		
					   	$arquivo = fopen($upload_path.''.$filename,'r');
			
					   	if ($arquivo == false){
					   		
					   		$msg = array('msg' => $abrir,'tipo' => 'erro','upload'=>'');
					   		return $msg;
					   		
					   	}

					   	//zera o total da transação a cada leitura de arquivo, ou seja, cada arquivo é um banco
					   	$this->valorTotalTransacao = 0;
					   	
					   	//controla leitura da linha Z
					   	$letra_z = FALSE;
					   	
					   	//contraola leitura da linha A
					   	$letra_a = FALSE; //verifica se a letra já foi lida
					   	
					   	//inicia linha do arquivo em 0
					   	$linha = 0;
					   	
					   	// importa linha por linha ate o final
					   	while(!feof($arquivo)) {
			
					   		//PEGA A LINHA
					   		$dadosLinha = utf8_decode(fgets($arquivo));
			
					   		$t = str_replace(array("\n", "\r"), '', $dadosLinha);
			
					   		// pega a primeira letra da linha atual
					   		$letra = substr($dadosLinha, 0, 1);
					   		
					   		// verifica o tamanho da linha se contem 150 caracteres
					   		$tamanhoLinha =  strlen($t);

					   		//se a letra da primeira linha não for 'A' aborta o processo
					   		if($linha == 0 && strtoupper($letra)  != 'A' ){
					   			return $this->retornarMsgErro($cancelada, $upload_path, $filename);
					   		}
					   		
					   		if($letra == "Z"){
					   			$letra_z = TRUE;
					   		}
						   		
					   		//verfica o tamanho da linha e se não está na letra Z 
					   		if($tamanhoLinha == 150 AND $letra_z == FALSE){
					   			
					   			if ($letra != "A" AND $letra != "B" AND $letra != "F" AND $letra != "Z") {
					   				return $this->retornarMsgErro($cancelada, $upload_path, $filename);
					   			}

					   			/**
					   			 * LETRA A
					   			 */
					   			if($letra == "A" && !$letra_a){
					   							   				
					   				$banco = substr($dadosLinha, 42,3);
					   				$NomeBanco = substr($dadosLinha, 45, 20);
					   						   				
					   				// verificar se o banco esta cadastrado na base
					   				$validarbanco = $this->validarBanco($banco);
					   					
					   				if ($validarbanco) {
			
					   					//verificar se o banco esta ativo na base
					   					$validarBancoAtivo = $this->validarBancoAtivo($banco);
					   						
					   					if ($validarBancoAtivo) {
			
					   						$data = substr($dadosLinha, 65, 8);
					   						$data = $this->validaData($data);
			
					   						$Remessa = substr($dadosLinha, 1, 1);
					   									   						
					   						$Banco = substr($dadosLinha, 42, 3);
					   						$nsa = substr($dadosLinha, 73, 6);
			
					   						## inicia a verificação da primeira linha do arquivo
					   						if($data AND is_numeric($Remessa) AND is_numeric($Banco) AND is_numeric($nsa)){
					   							
					   							// receber os dados da primeira linha
					   							$a2 = substr($dadosLinha, 1, 1);// Código de Remessa  ( 2 a 2)
					   							$a3 = substr($dadosLinha, 2, 20);//  Código do Convênio    (2 a 22)
					   							$a4 = substr($dadosLinha, 22, 20); //Nome da Empresa  (23 a 42 )
					   							$a5 = substr($dadosLinha, 42, 3); //Código do Banco(43 a 45)
					   							$a6 = substr($dadosLinha, 45, 20); //Nome do Banco  (46 a 65)
					   							$a7 = substr($dadosLinha, 65, 8); //Data de geração do arquivo (AAAAMMDD) (66 a 73)
					   							$a8 = substr($dadosLinha, 73, 6); //Número seqüencial do arquivo (NSA) (74 a 79)
					   							$a9 = substr($dadosLinha, 79, 2);// Versão do lay-out (80 a 81)
					   							$a10 = substr($dadosLinha, 81, 17);// DÉBITO AUTOMÁTICO  (82 a 98)
					   							$a11 = substr($dadosLinha, 98, 150);// Reservado para o futuro (filler)  (99 a 150)
					   							
					   							//retirar os caracteres especiais
					   							$nomeTransacao = $this->retirar_acentos(trim($a10));
					   							
					   							// verifica se está no formanto de importação 2 - RETORNO - Enviado pelo Banco para a Empresa.
					   							if($a2 != 2){
					   								return $this->retornarMsgErro($cancelada, $upload_path, $filename);
					   							}
					   							
					   							//valida se nome da transação é DEBITO AUTOMATICO
					   							if ($nomeTransacao != 'DEBITO AUTOMATICO') {
					   								return $this->retornarMsgErro($cancelada, $upload_path, $filename);
					   							}
					   								
					   						}else {
					   							return $this->retornarMsgErro($cancelada, $upload_path, $filename);
					   						}
			
					   					}else { 
					   						return $this->retornarMsgErro(str_replace('NomeBanco',$NomeBanco, $BancoAtivo), $upload_path, $filename);
					   					}
					   						
					   				} else {
					   					return $this->retornarMsgErro(str_replace('NomeBanco',$NomeBanco, $BancoNao), $upload_path, $filename);
					   		     	}
					   		     	
					   		     	$letra_a = TRUE; //não vai ler a letra a novamente
					   			}
					   			
					   	
					   			/**
					   			 * LETRA B
					   			 */
					   			if($letra == "B"){
					   							   				
					   				$data = substr($dadosLinha, 44, 8);
					   				$data = $this->validaData($data);
					   			
					   				if($data AND is_numeric(substr($dadosLinha, 149, 1)) ){
					   					 				   					
					   					$b2 = substr($dadosLinha,   1, 23); //Identificação do cliente na Empresa ( 2 a 26) 
					   					$b3 = substr($dadosLinha,  26,  4); //Agência para débito  (27 a 30 ) 
					   					$b4 = substr($dadosLinha,  30, 14); //Identificação do cliente no Banco  (31 a 44 ) 
					   					$b5 = substr($dadosLinha,  44,  8); //Data da Opção/Exclusão (AAAAMMDD)(45 a 52) 
					   					$b7 = substr($dadosLinha, 149,  1); //Código do movimento  (150 a 150)
					   					 				   					
					   					//verifica se o cliente existe na tabela clientes
					   					$clienteExiste = $this->getCliente($b2);
					   					
					   					//tratamento para exibir o texto do erro caso ocorra uma exceção na consulta
					   					if(is_object($clienteExiste)){
					   						
					   						return $this->retornarMsgErro($clienteExiste->getMessage(), $upload_path, $filename);
					   									   					
					   				    //cliente existe
					   					}elseif(is_array($clienteExiste)){
					   						
											$textoObsOK = true;
					   					
										//cliente não existe	
					   					}else{
					   						$textoObsOK = false;
					   					}
					   					
					   					$dadosObs = new stdClass();
					   					$dadosObs->cod_movimento = $b7;
					   					$dadosObs->nome_cliente = $clienteExiste[0]['clinome'];
					   					$dadosObs->cpf_cnpj_cliente = $b2;
					   					$dadosObs->agencia = $b3;
					   					$dadosObs->conta = $b4;
					   					$dadosObs->textoObsOK = $textoObsOK;
					   					 
					   					$retornoObs = $this->getTipoObsCliente($dadosObs);
					   						
					   					//caso erro do retorno, exibe mensagem na tela
					   					if(is_object($retornoObs)){
					   						return $this->retornarMsgErro($retornoObs->getMessage(), $upload_path, $filename);
					   					}
					   									   					
					   					//grava o log na base e continua com o processo
					   					$dadosLog = new stdClass();
					   					$dadosLog->data_arquivo  = trim($a7);
					   					$dadosLog->data_operacao = trim($b5);
					   					$dadosLog->num_banco     = trim($a5);
					   					$dadosLog->nsa           = trim($nsa);
					   					//atribui a mensagem de observação
					   					$dadosLog->observacao   = $retornoObs;
					   					   					
					   					//grava o log
					   					$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLog);
	
					   					//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
					   					if(is_object($insereLogRetorno)){
					   						return $this->retornarMsgErro($insereLogRetorno->getMessage(), $upload_path, $filename);
					   					}	
					   							   		
					   				}else{
					   					return $this->retornarMsgErro($cancelada, $upload_path, $filename);
					   				}
					   			}
					   			
			
					   			/**
					   			 * LETRA F
					   			 */
					   			if($letra == "F"){
					   					
					   				$data = substr($dadosLinha, 44, 8);
					   				$data = $this->validaData($data);
					   					
					   				if($data AND is_numeric(substr($dadosLinha, 52, 15)) AND is_numeric(substr($dadosLinha, 149, 1)) ){
					   				
					   					$dadosF = new stdClass();
					   					   					
					   					//verifica qual banco está importando os dados
					   					if($a5 == 341){ //Itaú
					   						
					   						$f2 = substr($dadosLinha, 1, 25);// Identificação do cliente na Empresa (2 a 26)
					   						$f3 = substr($dadosLinha, 26, 4);//  Agência para débito  (27 a 30 )
					   						//$f4 = substr($dadosLinha, 30, 8);//  Brancos  COMPLEMENTO DE REGISTRO (31 a 38 )
					   						$f5 = substr($dadosLinha, 38, 5); // NÚMERO DA CONTA DEBITADA  (39 a 43 )
					   						$f6 = substr($dadosLinha, 43, 1); // DAC DÍGITO VERIFICADOR DA AG/CONTA  (44 a 44 )
					   						$f7 = substr($dadosLinha, 44, 8); //DATA P/ LANÇAMENTO DO DÉBITO/COBRADA (45 a 52 )
					   						$f8 = substr($dadosLinha, 52, 15); //VALOR DO LANÇAM. PARA DÉBITO/COBRADO  (53 a 67)
					   						$f9  = substr($dadosLinha, 67, 2); //CÓDIGO DAS OCORRÊNCIAS PARA RETORNO  (68 a 69)
					   						
					   						$f10 = substr($dadosLinha, 69, 25); //PARA USO RESERVADO DA EMPRESA (70 a 94) X(25)
					   						$f10_titulo = substr($dadosLinha, 69, 12); //extração do número do título
					   						$f10_nota_fiscal = substr($dadosLinha, 82, 12); //extração da nota fiscal
					   						
					   						//recupera o valor da mora (somente para o itaú)
					   						$f11 = substr($dadosLinha, 94, 15);// Valor da mora (95 a 109)
					   						//$f12 = substr($dadosLinha, 109, 26);// COMPLEMENTO DE REGISTRO (110 a 135)
					   						//$f13 = substr($dadosLinha, 135, 14);//Nº DE INSCRIÇÃO DO DEBITADO (CPF/CNPJ) (136 a 149)
					   						$f14 = substr($dadosLinha, 149, 1);// Código do movimento  (150 a 150)
					   						
					   						$dadosF->caminho_arquivo    = $upload_path;
					   						$dadosF->nome_arquivo       = $filename;
					   						$dadosF->data_credito_cc    = $data_credito_cc;
					   						$dadosF->data_arquivo       = trim($a7);
					   						$dadosF->num_banco          = trim($a5);
					   						$dadosF->nome_banco         = trim($a6);
					   						$dadosF->cpf_cnpj_cliente   = trim($f2);
					   						$dadosF->agencia            = trim($f3);
					   						$dadosF->conta              = trim($f5);
					   						$dadosF->digito_verificador = trim($f6);
					   						$dadosF->data_operacao      = trim($f7);
					   						$dadosF->vl_recebido        = $this->converterInteiroFloat((int) trim($f8)); //Valor do débito   (53 a 67)
					   						$dadosF->cod_retorno        = trim($f9);
					   						$dadosF->nsa                = (int)trim($nsa);
					   						$dadosF->titoid             = trim($f10_titulo);
					   						$dadosF->nota_fiscal        = trim($f10_nota_fiscal);
					   						$dadosF->vl_encargo         = $this->converterInteiroFloat((int) trim($f11)); //atribui o valor inteiro
					   						$dadosF->cod_movimento      = trim($f14);
					   						##//calcula o valor total recebido##
					   						$dadosF->total_recebido     = $dadosF->vl_recebido + $dadosF->vl_encargo;
					   						
					   						$retImportarItau = $this->importarDadosItau($dadosF);
					   						
					   						if(!$retImportarItau){
					   							return $retImportarItau;
					   						}
					   						
					   					## FIM IMPORTAÇÃO ITAÚ	
					   						
					   						
					   					## INICIO IMPORTAÇÃO HSBC	
					   					}elseif($a5 == 399){ 
	
					   						$f2 = substr($dadosLinha, 1, 20);// Identificação do cliente consumidor junto ao cliente credor ( 2 a 21)
					   						//$f3 = substr($dadosLinha, 21, 5);//  Brancos  (22 a 26 )
					   						$f4 = substr($dadosLinha, 26, 4); //Agência do Banco para débito (27 a 30)
					   						$f5 = substr($dadosLinha, 30, 14);//Identificação do cliente consumidor no Banco (conta corrente) (31 a 44)
					   						$f6 = substr($dadosLinha, 44, 8); //Data do vencimento/débito (AAAAMMDD)(45 a 52)
					   						$f7 = substr($dadosLinha, 52, 15); //Valor do débito   (53 a 67)
					   						$f8 = substr($dadosLinha, 67, 2); //Código de retorno  (68 a 69)
					   						
					   						$f9 = substr($dadosLinha, 69, 31);// Uso do cliente credor (livre) (70 a 100)
					   						$f9_titulo = substr($dadosLinha, 69, 12); //extração do número do título
					   						$f9_nota_fiscal = substr($dadosLinha, 82, 12); //extração da nota fiscal
					   						
					   						$f10 = substr($dadosLinha, 100, 14);//CPF/CNPJ do devedor (101 a 114)
					   						//$f11 = substr($dadosLinha, 114, 15);//Valor do IOF calculado/recolhido (duas decimais) (115 a 129)
					   						$f12 = substr($dadosLinha, 129, 2);//Código da moeda (130 a 131)
					   						//$f13 = substr($dadosLinha, 131, 2); //Brancos (132 a 149)
					   						$f14 = substr($dadosLinha, 149, 1);//Código do movimento (150 a 150)
					   						
					   						$dadosF->caminho_arquivo    = $upload_path;
					   						$dadosF->nome_arquivo       = $filename;
					   						$dadosF->data_credito_cc    = $data_credito_cc;
					   						$dadosF->data_arquivo       = trim($a7);
					   						$dadosF->num_banco          = trim($a5);
					   						$dadosF->nome_banco         = trim($a6);
					   						$dadosF->cpf_cnpj_cliente   = trim($f2);
					   						$dadosF->agencia            = trim($f4);
					   						$dadosF->conta              = trim($f5);
					   						$dadosF->data_operacao      = trim($f6);
					   						$dadosF->vl_recebido        = $this->converterInteiroFloat((int) trim($f7)); //Valor do débito   (53 a 67)
					   						$dadosF->cod_retorno        = trim($f8);
					   						$dadosF->nsa                = (int)trim($nsa);
					   						$dadosF->titoid             = trim($f9_titulo);
					   						$dadosF->nota_fiscal        = trim($f9_nota_fiscal);
					   						$dadosF->cod_movimento      = trim($f14);
					   						$dadosF->total_recebido     = $dadosF->vl_recebido ;

					   						$retImportarHsbc = $this->importarDadosHsbc($dadosF);
					   						
					   						if(!$retImportarHsbc){
					   							return $retImportarHsbc;
					   						}
					   							
					   					## FIM IMPORTAÇÃO HSBC

					   						
					   					## INICIO IMPORTAÇÃO LAYOUT PADRÃO (Bradesco, Banco do Brasil, Santander)	
					   					}else{
					   						
					   						
					   						//layout padrão
					   						$f2 = substr($dadosLinha, 1, 25); // Identificação do cliente na Empresa ( 2 a 26)
					   						$f3 = substr($dadosLinha, 26, 4); //Agência para débito  (27 a 30 )
					   						$f4 = substr($dadosLinha, 30, 14); //Identificação do cliente no Banco  (31 a 44 )
					   						$f5 = substr($dadosLinha, 44, 8); //Data do vencimento/débito (AAAAMMDD)(45 a 52)
					   						$f6 = substr($dadosLinha, 52, 15); //Valor Original ou Debitado   (53 a 67)
					   						$f7 = substr($dadosLinha, 67, 2); //Código de retorno  (68 a 69)
					   						
					   						$f8 = substr($dadosLinha, 69, 60); //Uso da Empresa  (70 a 129)
					   						$f8_titulo = substr($dadosLinha, 69, 12); //extração do número do título
					   						$f8_nota_fiscal = substr($dadosLinha, 82, 12); //extração da nota fiscal
					   						
					   						$f9  = substr($dadosLinha, 129, 1); //Tipo de Identificação (130 a 130) 1 = CNPJ ,  2 = CPF
					   						$f10 = substr($dadosLinha, 130, 15); //Identificação (131 a 145) CNPJ ou CPF
					   						$f11 = substr($dadosLinha, 145, 4); //Reservado para o futuro (146 a 149)
					   						$f12 = substr($dadosLinha, 149, 1);// Código do movimento  (150 a 150)
					   						
					   						$dadosF->caminho_arquivo    = $upload_path;
					   						$dadosF->nome_arquivo       = $filename;
					   						$dadosF->data_credito_cc    = $data_credito_cc;
					   						$dadosF->data_arquivo       = trim($a7);
					   						$dadosF->num_banco          = trim($a5);
					   						$dadosF->nome_banco         = trim($a6);
					   						$dadosF->cpf_cnpj_cliente   = trim($f2);
					   						$dadosF->agencia            = trim($f3);
					   						$dadosF->conta              = trim($f4);
					   						$dadosF->data_operacao      = trim($f5);
					   						$dadosF->vl_recebido        = $this->converterInteiroFloat((int) trim($f6)); //Valor do débito   (53 a 67)
					   						$dadosF->cod_retorno        = trim($f7);
					   						$dadosF->nsa                = (int)trim($nsa);
					   						$dadosF->titoid             = trim($f8_titulo);
					   						$dadosF->nota_fiscal        = trim($f8_nota_fiscal);
					   						$dadosF->cod_movimento      = trim($f12);
					   						$dadosF->total_recebido     = $dadosF->vl_recebido ;
					   						
					   						$retImportarPadrao = $this->importaDadosPadrao($dadosF);
					   						
					   						if(!$retImportarPadrao){
					   							return $retImportarPadrao;
					   						}
					  
					   					## FIM IMPORTAÇÃO LAYOUT PADRÃO
					   					}
					   				
					   				}else{
					   					return $this->retornarMsgErro($cancelada, $upload_path, $filename);
					   				}
					   			}
			
					   		/**
					   		 * LETRA Z
					   		 */	
	####    		   		//verifica se é a letra Z (fim da leitura)
					   		}elseif($letra_z == TRUE){
					   			
					   			//verifica se houve movimentação bancária no dia, passando o número do banco
					   			$movimentacaoBancaria = $this->getMovimentacaoBancaria(trim($a5), $data_credito_cc);
									   			
					   			if(is_array($movimentacaoBancaria) && $this->permiteMovimentacao){
					   			
						   			//se não houver movimentação bancária, então, executa a função movim_banco_i
						   			if($movimentacaoBancaria[0]['mbcooid'] == NULL){
		
						   				$dadosMov = new stdClass();
						   				
						   				$dadosMov->cod_banco       = $a5;
						   				$dadosMov->data_credito_cc = $data_credito_cc;
						   				$dadosMov->tipo_movi       = 'E';
						   				$dadosMov->historico       = $movimentacaoBancaria[0]['tmbhistorico'].' - '.$a6 ;
						   				$dadosMov->valor_total     = $this->valorTotalTransacao;
						   				$dadosMov->plano_contabil  = $movimentacaoBancaria[0]['tmbplcoid'];
						   				$dadosMov->forma_cobranca  = $movimentacaoBancaria[0]['tmboid'];
						   				$dadosMov->cod_usuario     = $_SESSION['usuario']['oid'];
						   				$dadosMov->mbcotecoid      = 1;
						   				$dadosMov->mbcoftcoid      = 1;
						   				$dadosMov->mbcodepoid      = 4;
						   				
						   				//insere movimentação bancária
						   				$retMoviventacao = $this->setMovimentacaoBancaria($dadosMov);
						   				
						   				//se houver erro, retorna o erro e desfaz as alterações no banco
						   				if(!$retMoviventacao){
						   					return $this->retornarMsgErro($retMoviventacao->getMessage(), $upload_path, $filename);
						   				}
						   			}
					   			}
					   			
					   			//atualiza a tabela config banco com o nsa
					   			$atualizarConfig = $this->atualizarRetornoConfigBanco(trim($a5), (int)trim($nsa));
					   			
					   			//se houver erro, retorna o erro e desfaz as alterações no banco
					   			if(!$atualizarConfig){
					   				return $this->retornarMsgErro($atualizarConfig->getMessage(), $upload_path, $filename);
					   			}
					   			
					   			##//finaliza a leitura com sucesso
					   			//return $this->retornarMsgSucesso($sucesso, $upload_path, $filename);
	
					   		}else{//fim das verificações das letras 
				
					   			return $this->retornarMsgErro($cancelada, $upload_path, $filename);
					   		}	
					   		
					   		$linha++;
					   		
					   	}//fim do while
					  
					   	
				   	// fim da verificação do move_uploaded_file
				   }else{
				   		return $this->retornarMsgErro($erro, $upload_path, $filename);
				   }

		 	}else{
		 		 
		 		$msg = array('msg' =>$selecionar,'tipo' => 'alerta','upload'=>'erro');
		 		return $msg; // Ele não selecionou o arquivo : (.

		 	}
		   
		   $i++;
	    
		 }//fim foreach $_FILES
		 
		 //fecha o arquivo
		 fclose($arquivo);
		 
		 //finaliza a leitura com sucesso, se caso não retornar nenhum erro nos retornos acima
		 return $this->retornarMsgSucesso($sucesso, $upload_path, $filename);
		  
	}

	
	/**
	 * Recebe dados para importação via parâmetro do arquivo de retorno do banco Itaú 
	 * 
	 * @param object $dadosF
	 * @return Ambigous <unknown, multitype:string string >|boolean
	 */
	private function importarDadosItau($dadosF){
		
		//verifica se o cliente existe na tabela clientes
		$clienteExiste = $this->getCliente($dadosF->cpf_cnpj_cliente);
		
		//tratamento para exibir o texto do erro caso ocorra uma exceção na consulta
		if(is_object($clienteExiste)){
		
			return $this->retornarMsgErro($clienteExiste->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
		
			//cliente existe
		}elseif(is_array($clienteExiste)){
		
			//verificar código de retorno do arquivo
			if($dadosF->cod_retorno != '00' && $dadosF->cod_retorno != '31'){
		
				$atualizaTitulo = $this->setTituloCodRetorno($dadosF->cod_retorno, $dadosF->titoid);
		
				//grava o log na base e continua com o processo
				$dadosLogF = new stdClass();
				$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
				$dadosLogF->data_operacao = $dadosF->data_operacao;
				$dadosLogF->num_banco     = $dadosF->num_banco;
				$dadosLogF->titoid        = $dadosF->titoid;
				$dadosLogF->nsa           = $dadosF->nsa;
				$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
		
				//grava o log
				$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
				//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
				if(is_object($insereLogRetorno)){
					return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
				}
		
			}elseif($dadosF->cod_retorno == '00' || $dadosF->cod_retorno == '31' && ($dadosF->vl_recebido > 0)){
					
				//recupera dados do título
				$dadosTitulo = $this->getDadosTitulo($dadosF->titoid);
		
				//se não retornar dados, grava log
				if(!is_array($dadosTitulo)){
						
					//grava o log na base e continua com o processo
					$dadosLogF = new stdClass();
					$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
					$dadosLogF->data_operacao = $dadosF->data_operacao;
					$dadosLogF->num_banco     = $dadosF->num_banco;
					$dadosLogF->titoid        = $dadosF->titoid;
					$dadosLogF->nsa           = $dadosF->nsa;
					$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
					//atribui a mensagem de observação se houver erro
					$dadosLogF->observacao   = 'Título não encontrado na base de título da Sascar.';
		
					//grava o log
					$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
					//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
					if(is_object($insereLogRetorno)){
						return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
					}
						
				//se retornar dados, faz as verificações para baixar o título
				}else{
						
					//verifica se o título não está baixado
					if(empty($dadosTitulo[0]['titdt_credito'])){
		
						//baixa o título
						$dadosBaixaTitulo = new stdClass();
						$dadosBaixaTitulo->data_credito_cc = $dadosF->data_credito_cc;
						$dadosBaixaTitulo->data_operacao   = $dadosF->data_operacao;
						$dadosBaixaTitulo->total_recebido  = $dadosF->total_recebido;
						$dadosBaixaTitulo->obs_recebimento = $dadosF->nome_banco." - Retorno: $dadosF->nsa  Agência: $dadosF->agencia  C/C: $dadosF->conta ";
						$dadosBaixaTitulo->cod_banco       = $dadosF->num_banco;
						$dadosBaixaTitulo->vl_encargo      = $dadosF->vl_encargo;
						$dadosBaixaTitulo->cod_retorno     = $dadosF->cod_retorno;
						$dadosBaixaTitulo->titoid          = (int)$dadosF->titoid;
							
						$retBaixaTitulo = $this->setBaixarTitulo($dadosBaixaTitulo);
		
						//se caso algum erro na baixa, interrompe o processo e exibe mensagem de erro
						if(is_object($retBaixaTitulo)){
							return $this->retornarMsgErro($retBaixaTitulo->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
						}
		
						$this->valorTotalTransacao = $this->valorTotalTransacao + $dadosBaixaTitulo->total_recebido;
					   										
   						//permite gravar a movimentação bancária do dia
   						$this->permiteMovimentacao = TRUE;
		
					//então, o título já está baixado grava o log
					}else{
		
						//grava o log na base e continua com o processo
						$dadosLogF = new stdClass();
						$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
						$dadosLogF->data_operacao = $dadosF->data_operacao;
						$dadosLogF->num_banco     = $dadosF->num_banco;
						$dadosLogF->titoid        = $dadosF->titoid;
						$dadosLogF->nsa           = $dadosF->nsa;
						$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
						//atribui a mensagem de observação se houver erro
						$dadosLogF->observacao   = 'Título já baixado em '.$dadosTitulo[0]['titdt_credito'].' com valor de '.number_format($dadosTitulo[0]['titvl_titulo'],2,",",".");
							
						//grava o log
						$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
						//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
						if(is_object($insereLogRetorno)){
							return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
						}
							
					}//fim se titulo baixado
						
				}//fim se retornou dados
		
			}//fim elseif do codigo de retorno do arquivo
		
			 
		//cliente não existe, atribui mensagem de cliente não encontrado e continua com o processo
		}else{
			 
			$textoObsOK = false;
			 
			$dadosObsF = new stdClass();
			$dadosObsF->cod_movimento     = $dadosF->cod_movimento;
			$dadosObsF->cpf_cnpj_cliente  = $dadosF->cpf_cnpj_cliente;
			$dadosObsF->textoObsOK        = $textoObsOK;
		
			$retornoObsF = $this->getTipoObsCliente($dadosObsF);
			 
			//caso erro do retorno, interrompe o processo exibe mensagem na tela
			if(is_object($retornoObsF)){
				return $this->retornarMsgErro($retornoObsF->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
			}
			 
			//grava o log na base e continua com o processo
			$dadosLogF = new stdClass();
			$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
			$dadosLogF->data_operacao = $dadosF->data_operacao;
			$dadosLogF->num_banco     = $dadosF->num_banco;
			$dadosLogF->titoid        = $dadosF->titoid;
			$dadosLogF->nsa           = $dadosF->nsa;
			$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
			//atribui a mensagem de observação se houver erro
			$dadosLogF->observacao    = $retornoObsF;
		
			//grava o log
			$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
			 
			//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
			if(is_object($insereLogRetorno)){
				return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
			}
			 
			 
		}//fim se cliente não existe
		
		## FIM IMPORTAÇÃO ITAÚ
		
		return TRUE;
	}
	
	
	
	/**
	 * Recebe dados para importação via parâmetro do arquivo de retorno do banco HSBC
	 * 
	 * @param object $dadosF
	 * @return Ambigous <unknown, multitype:string string >|boolean
	 */
	private function importarDadosHsbc($dadosF){
		
		//verifica se o cliente existe na tabela clientes
		$clienteExiste = $this->getCliente($dadosF->cpf_cnpj_cliente);
		
		//tratamento para exibir o texto do erro caso ocorra uma exceção na consulta
		if(is_object($clienteExiste)){
		
			return $this->retornarMsgErro($clienteExiste->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
		
			//cliente existe
		}elseif(is_array($clienteExiste)){
			 
			//verificar código de retorno do arquivo
			if($dadosF->cod_retorno != '00' && $dadosF->cod_retorno != '31'){
		
				$atualizaTitulo = $this->setTituloCodRetorno($dadosF->cod_retorno, $dadosF->titoid);
		
				//grava o log na base e continua com o processo
				$dadosLogF = new stdClass();
				$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
				$dadosLogF->data_operacao = $dadosF->data_operacao;
				$dadosLogF->num_banco     = $dadosF->num_banco;
				$dadosLogF->titoid        = $dadosF->titoid;
				$dadosLogF->nsa           = $dadosF->nsa;
				$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
		
				//grava o log
				$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
				//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
				if(is_object($insereLogRetorno)){
					return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
				}
		
		
			}elseif($dadosF->cod_retorno == '00' || $dadosF->cod_retorno == '31' && ($dadosF->vl_recebido > 0)){
		
				//recupera dados do título
				$dadosTitulo = $this->getDadosTitulo($dadosF->titoid);
		
				//se não retornar dados, grava log
				if(!is_array($dadosTitulo)){
		
					//grava o log na base e continua com o processo
					$dadosLogF = new stdClass();
					$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
					$dadosLogF->data_operacao = $dadosF->data_operacao;
					$dadosLogF->num_banco     = $dadosF->num_banco;
					$dadosLogF->titoid        = $dadosF->titoid;
					$dadosLogF->nsa           = $dadosF->nsa;
					$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
					//atribui a mensagem de observação se houver erro
					$dadosLogF->observacao   = 'Título não encontrado na base de título da Sascar.';
		
					//grava o log
					$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
					//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
					if(is_object($insereLogRetorno)){
						return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
					}
		
					//se retornar dados, faz as verificações para baixar o título
				}else{
		
					//verifica se o título não está baixado
					if(empty($dadosTitulo[0]['titdt_credito'])){
		
						//baixa o título
						$dadosBaixaTitulo = new stdClass();
						$dadosBaixaTitulo->data_credito_cc = $dadosF->data_credito_cc;
						$dadosBaixaTitulo->data_operacao   = $dadosF->data_operacao;
						$dadosBaixaTitulo->total_recebido  = $dadosF->total_recebido;
						$dadosBaixaTitulo->obs_recebimento = $dadosF->nome_banco." - Retorno: $dadosF->nsa  Agência: $dadosF->agencia  C/C: $dadosF->conta ";
						$dadosBaixaTitulo->cod_banco       = $dadosF->num_banco;
						$dadosBaixaTitulo->vl_encargo      = "NULL";
						$dadosBaixaTitulo->cod_retorno     = $dadosF->cod_retorno;
						$dadosBaixaTitulo->titoid          = (int)$dadosF->titoid;
							
						$retBaixaTitulo = $this->setBaixarTitulo($dadosBaixaTitulo);
		
						//se caso algum erro na baixa, interrompe o processo e exibe mensagem de erro
						if(is_object($retBaixaTitulo)){
							return $this->retornarMsgErro($retBaixaTitulo->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
						}
		
						$this->valorTotalTransacao = $this->valorTotalTransacao  + $dadosBaixaTitulo->total_recebido;
		
						//permite gravar a movimentação bancária do dia
						$this->permiteMovimentacao = TRUE;
		
						//então, o título já está baixado grava o log
					}else{
		
						//grava o log na base e continua com o processo
						$dadosLogF = new stdClass();
						$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
						$dadosLogF->data_operacao = $dadosF->data_operacao;
						$dadosLogF->num_banco     = $dadosF->num_banco;
						$dadosLogF->titoid        = $dadosF->titoid;
						$dadosLogF->nsa           = $dadosF->nsa;
						$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
						//atribui a mensagem de observação se houver erro
						$dadosLogF->observacao   = 'Título já baixado em '.$dadosTitulo[0]['titdt_credito'].' com valor de '.number_format($dadosTitulo[0]['titvl_titulo'],2,",",".");
							
						//grava o log
						$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
						//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
						if(is_object($insereLogRetorno)){
							return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
						}
							
					}//fim se titulo baixado
						
				}//fim se retornou dados
		
			}//fim elseif do codigo de retorno do arquivo
		
		
		//cliente não existe, atribui mensagem de cliente não encontrado e continua com o processo
		}else{
		
			$textoObsOK = false;
		
			$dadosObsF = new stdClass();
			$dadosObsF->cod_movimento     = $dadosF->cod_movimento;
			$dadosObsF->cpf_cnpj_cliente  = $dadosF->cpf_cnpj_cliente;
			$dadosObsF->textoObsOK        = $textoObsOK;
		
			$retornoObsF = $this->getTipoObsCliente($dadosObsF);
		
			//caso erro do retorno, interrompe o processo exibe mensagem na tela
			if(is_object($retornoObsF)){
				return $this->retornarMsgErro($retornoObsF->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
			}
		
			//grava o log na base e continua com o processo
			$dadosLogF = new stdClass();
			$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
			$dadosLogF->data_operacao = $dadosF->data_operacao;
			$dadosLogF->num_banco     = $dadosF->num_banco;
			$dadosLogF->titoid        = $dadosF->titoid;
			$dadosLogF->nsa           = $dadosF->nsa;
			$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
			//atribui a mensagem de observação se houver erro
			$dadosLogF->observacao    = $retornoObsF;
		
			//grava o log
			$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
			//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
			if(is_object($insereLogRetorno)){
				return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
			}
		
		}//fim se cliente não existe
		
		
		## FIM IMPORTAÇÃO HSBC
		
		return TRUE;
		
	}
	
	
	/**
	 * Recebe dados para importação via parâmetro do arquivo de retorno dos bancos: Banco do Brasil, Bradesco e Santander
	 * 
	 * @param object $dadosF
	 * @return Ambigous <unknown, multitype:string string >|boolean
	 */
	private function importaDadosPadrao($dadosF){
		
		//verifica se o cliente existe na tabela clientes
		$clienteExiste = $this->getCliente($dadosF->cpf_cnpj_cliente);
		
		//tratamento para exibir o texto do erro caso ocorra uma exceção na consulta
		if(is_object($clienteExiste)){
		
			return $this->retornarMsgErro($clienteExiste->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
		
		//cliente existe
		}elseif(is_array($clienteExiste)){
		
			//verificar código de retorno do arquivo
			if($dadosF->cod_retorno != '00' && $dadosF->cod_retorno != '31'){
		
				$atualizaTitulo = $this->setTituloCodRetorno($dadosF->cod_retorno, $dadosF->titoid);
		
				//grava o log na base e continua com o processo
				$dadosLogF = new stdClass();
				$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
				$dadosLogF->data_operacao = $dadosF->data_operacao;
				$dadosLogF->num_banco     = $dadosF->num_banco;
				$dadosLogF->titoid        = $dadosF->titoid;
				$dadosLogF->nsa           = $dadosF->nsa;
				$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
		
				//grava o log
				$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
				//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
				if(is_object($insereLogRetorno)){
					return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
				}
		
		
			}elseif($dadosF->cod_retorno == '00' || $dadosF->cod_retorno == '31' && ($dadosF->vl_recebido > 0)){
		
				//recupera dados do título
				$dadosTitulo = $this->getDadosTitulo($dadosF->titoid);
		
				//se não retornar dados, grava log
				if(!is_array($dadosTitulo)){
		
					//grava o log na base e continua com o processo
					$dadosLogF = new stdClass();
					$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
					$dadosLogF->data_operacao = $dadosF->data_operacao;
					$dadosLogF->num_banco     = $dadosF->num_banco;
					$dadosLogF->titoid        = $dadosF->titoid;
					$dadosLogF->nsa           = $dadosF->nsa;
					$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
					//atribui a mensagem de observação se houver erro
					$dadosLogF->observacao   = 'Título não encontrado na base de título da Sascar.';
		
					//grava o log
					$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
					//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
					if(is_object($insereLogRetorno)){
						return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
					}
		
				//se retornar dados, faz as verificações para baixar o título
				}else{
		
					//verifica se o título não está baixado
					if(empty($dadosTitulo[0]['titdt_credito'])){
		
						//baixa o título
						$dadosBaixaTitulo = new stdClass();
						$dadosBaixaTitulo->data_credito_cc = $dadosF->data_credito_cc;
						$dadosBaixaTitulo->data_operacao   = $dadosF->data_operacao;
						$dadosBaixaTitulo->total_recebido  = $dadosF->total_recebido;
						$dadosBaixaTitulo->obs_recebimento = $dadosF->nome_banco." - Retorno: $dadosF->nsa  Agência: $dadosF->agencia  C/C: $dadosF->conta ";
						$dadosBaixaTitulo->cod_banco       = $dadosF->num_banco;
						$dadosBaixaTitulo->vl_encargo      = "NULL";
						$dadosBaixaTitulo->cod_retorno     = $dadosF->cod_retorno;
						$dadosBaixaTitulo->titoid          = (int)$dadosF->titoid;
							
						$retBaixaTitulo = $this->setBaixarTitulo($dadosBaixaTitulo);
		
						//se caso algum erro na baixa, interrompe o processo e exibe mensagem de erro
						if(is_object($retBaixaTitulo)){
							return $this->retornarMsgErro($retBaixaTitulo->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
						}
		
						$this->valorTotalTransacao = $this->valorTotalTransacao + $dadosBaixaTitulo->total_recebido;
		
						//permite gravar a movimentação bancária do dia
						$this->permiteMovimentacao = TRUE;
		
					//então, o título já está baixado grava o log
					}else{
		
						//grava o log na base e continua com o processo
						$dadosLogF = new stdClass();
						$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
						$dadosLogF->data_operacao = $dadosF->data_operacao;
						$dadosLogF->num_banco     = $dadosF->num_banco;
						$dadosLogF->titoid        = $dadosF->titoid;
						$dadosLogF->nsa           = $dadosF->nsa;
						$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
						//atribui a mensagem de observação se houver erro
						$dadosLogF->observacao   = 'Título já baixado em '.$dadosTitulo[0]['titdt_credito'].' com valor de '.number_format($dadosTitulo[0]['titvl_titulo'],2,",",".");
							
						//grava o log
						$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
						//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
						if(is_object($insereLogRetorno)){
							return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
						}
							
					}//fim se titulo baixado
		
				}//fim se retornou dados
		
			}//fim elseif do codigo de retorno do arquivo
		
		
		//cliente não existe, atribui mensagem de cliente não encontrado e continua com o processo
		}else{
		
			$textoObsOK = false;
		
			$dadosObsF = new stdClass();
			$dadosObsF->cod_movimento     = $dadosF->cod_movimento;
			$dadosObsF->cpf_cnpj_cliente  = $dadosF->cpf_cnpj_cliente;
			$dadosObsF->textoObsOK        = $textoObsOK;
		
			$retornoObsF = $this->getTipoObsCliente($dadosObsF);
		
			//caso erro do retorno, interrompe o processo exibe mensagem na tela
			if(is_object($retornoObsF)){
				return $this->retornarMsgErro($retornoObsF->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
			}
		
			//grava o log na base e continua com o processo
			$dadosLogF = new stdClass();
			$dadosLogF->data_arquivo  = $dadosF->data_arquivo;
			$dadosLogF->data_operacao = $dadosF->data_operacao;
			$dadosLogF->num_banco     = $dadosF->num_banco;
			$dadosLogF->titoid        = $dadosF->titoid;
			$dadosLogF->nsa           = $dadosF->nsa;
			$dadosLogF->cod_retorno   = $dadosF->cod_retorno;
			//atribui a mensagem de observação se houver erro
			$dadosLogF->observacao    = $retornoObsF;
		
			//grava o log
			$insereLogRetorno = $this->setLogDebitoAutomatico($dadosLogF);
		
			//se caso algum erro na inserção do log, interrompe o processo e exibe mensagem de erro
			if(is_object($insereLogRetorno)){
				return $this->retornarMsgErro($insereLogRetorno->getMessage(), $dadosF->caminho_arquivo, $dadosF->nome_arquivo);
			}
		
		}//fim se cliente não existe
		
		
		## FIM IMPORTAÇÃO LAYOUT PADRÃO
		
		return TRUE;
		
	}
	
	
	/**
	 * Deleta arquivo de importação da pasta
	 * Faz rollback
	 * Retorna mensagem 
	 *  
	 * @param string $msg
	 * @return unknown
	 */
	public function retornarMsgErro($msg, $caminho_arquivo, $nome_arquivo){
		
		unlink($caminho_arquivo.$nome_arquivo);
		pg_query($this->conn, "ROLLBACK");
		$mensagem = array('msg' =>$msg,'tipo' => 'alerta','upload'=>'erro');

		return $mensagem;
	}
	
	
	/**
	 * Deleta arquivo de importação da pasta
	 * Faz commit de sucesso
	 *
	 * @param string $msg
	 * @return unknown
	 */
	public function retornarMsgSucesso($msg, $caminho_arquivo, $nome_arquivo){
	
		 unlink($caminho_arquivo.$nome_arquivo);
		 pg_query($this->conn, "COMMIT");
		 $mensagem = array('msg' =>$msg,'tipo' => 'sucesso');
	
		return $mensagem;
	}
	
	/**
	 * Verifica se o cpf/cnpj informado no parâmetro está cadastrado na base de clientes
	 * retornado verdadeiro caso sim, e falso cas não.
	 *  
	 * @param unknown $cpf_cnpj
	 * @return boolean
	 */
	public function getCliente($cpf_cnpj){
		
		$existeCliente = $this->dao->getCliente($cpf_cnpj);
		
		return $existeCliente;
		
	}
	
	/**
	 * Retorna dados do título
	 * 
	 * @param int  $titoid
	 * @return Ambigous <boolean, Exception, multitype:>
	 */
	public function getDadosTitulo($titoid){
		
		$retornoDados = $this->dao->getDadosTitulo($titoid);
		
		return $retornoDados;
	}
	
	/**
	 * Caso o código de retorno seja diferente de 00 e 31
	 * atualiza o título com o código do arquivo
	 *
	 * @param int $codRetorno
	 *@return Ambigous <unknown, boolean, Exception>
	 */
	public function setTituloCodRetorno($codRetorno, $titoid){
		
		$retornoCod = $this->dao->setTituloCodRetorno($codRetorno, $titoid);
		
		return $retornoCod;
		
	}
	
	/**
	 * Baixa um título 
	 * 
	 * @param object $dadosBaixa
	 * @return Ambigous <boolean, Exception>
	 */
	public function setBaixarTitulo($dadosBaixa){
		
		$retornoBaixa = $this->dao->setBaixarTitulo($dadosBaixa);
		
		return $retornoBaixa;
	}
	
	/**
	 * Retorna dados de movimentação bancária do dia
	 *
	 * @param int $num_banco
	 * @return Ambigous <multitype:, boolean, Exception, multitype:>
	 */
	public function getMovimentacaoBancaria($num_banco, $data_credito_cc){

		$retornoMovimentacao = $this->dao->getMovimentacaoBancaria($num_banco, $data_credito_cc);

		return $retornoMovimentacao;

	}
	
	
	/**
	 * Gera movimentação bancária
	 * 
	 * @param object $dados
	 * @return Ambigous <boolean, Exception, multitype:>
	 */
	public function setMovimentacaoBancaria($dados){
		
		$movimentacao = $this->dao->setMovimentacaoBancaria($dados);
		
		return $movimentacao;
		
	}
	
	/**
	 * Atualiza com o último nsa de retorno nas configurações do bamco 
	 * 
	 * @param int $banco
	 * @param int $nsa
	 * @return Ambigous <boolean, Exception>
	 */
	public function atualizarRetornoConfigBanco($banco, $nsa){
		
		$retConfig = $this->dao->atualizarRetornoConfigBanco($banco, $nsa);
		
		return $retConfig;
	}
	
	/**
	 * Recebe objeto de dados para montar o texto da observação que será gravada
	 * 
	 * @param object $dadosObs
	 * @throws Exception
	 * @return string|Exception
	 */
	public function getTipoObsCliente($dadosObs){
		
		try {
				
			if(!is_object($dadosObs)){
				throw new Exception('Os dados para observação do log devem ser informados.');	
			}
			
			//verifica o tipo de solicitação do movimento
			if($dadosObs->cod_movimento == 1){
				$tipo_solicitacao = 'exclusão';
			}elseif($dadosObs->cod_movimento == 2){
				$tipo_solicitacao = 'inclusão';
			}else{
				$tipo_solicitacao = 'tipo desconhecido';
			}
			
			if($dadosObs->textoObsOK){
				$observacao = 'Cliente '.$dadosObs->nome_cliente.' solicitou '.$tipo_solicitacao.' de débito automático! CGC/CPF = '.trim($dadosObs->cpf_cnpj_cliente).' Agência.: '.$dadosObs->agencia.' Conta: '.$dadosObs->conta.'  ';
			}else{
				$observacao = 'Cliente não encontrado na base de clientes Sascar. CGC/CPF = '.$dadosObs->cpf_cnpj_cliente;
			}
			
			return $observacao;
			
		} catch (Exception $e) {
			return $e;
		}
		
	}
	
	
	/**
	 * Grava log em caso de erro
	 * 
	 * @param object $dados
	 * @return Ambigous <boolean, Exception>
	 */
	public function setLogDebitoAutomatico($dadosLog){
		
		$retornoLog = $this->dao->setLogDebitoAutomatico($dadosLog);
		
		return $retornoLog;
	}
	
	
	/**
	 * Helper que converte o valor de inteiro para Float
	 *
	 * @param integer $entrada - valor em formato integer
	 * @return float $valor - valor convertido em formato float
	 */
	private function converterInteiroFloat($entrada){
		return number_format($entrada/100,2,".","");
	}
	
	/**
	 * verifica se o banco existe na base.
	 * @var: @codigo do banco
	 * @return:true/false
	 */
	public function validarBanco($banco) {
		return $this->dao->verificaBanco($banco);
	}
	
    /**
     * verifica se o banco esta ativo.
     * @var: @codigo do banco
     * @return:true/false
     */
    public function validarBancoAtivo($banco){
    	return $this->dao->validarBancoAtivo($banco);
    }

    /**
     * Calcula se a data informada é atual, se é maior e se é menor que periodo máximo informado
     * 
     * @param unknown $data_credito
     * @return boolean
     */
    public function validarPeriodo($data_credito, $periodoMaximo){
    	    	
    	list($dia, $mes, $ano) = explode("/", $data_credito);
    	list($hoje_dia, $hoje_mes, $hoje_ano ) = explode("/", date('d/m/Y'));
    		
    	$data_credito = mktime(0,0,0,$mes, $dia, $ano);
    	$data_hoje = mktime(0,0,0,$hoje_mes, $hoje_dia, $hoje_ano);
    		
    	$total_dias = ($data_hoje - $data_credito) / 86400;
     	
    	//data de crédito não pode se maior que a data atual
    	if($total_dias < 0){
    	
    		return 2;
    	
    		// data de crédito deve ser menor que a data atual até 15 dias
    	}else if($total_dias > $periodoMaximo && $total_dias != 0){
    		 
    		return 3;
    	
    	}else{
    		
    		return;
    	}
    	
    }
    
    
    
    /**
     * Valida se a data é válida;
     */
    public function validaData($data) {
    	$a = substr($data, 0, 4);
    	$m = substr($data, 4, 2);
    	$d = substr($data, 6, 2);

    	$valor = checkdate($m,$d,$a);
    	 
    	if($valor){
    		return TRUE;
    	}else{
    		return FALSE;
    	}
    }
	
	public function retirar_acentos($string){
		$string =  strtr($string, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC");
		return $string;
	}
	
	protected function diretorio($cfbbanco){
		return $this->dao->diretorioDao($cfbbanco);
	}
	
 }
 
?>