<?php
ini_set('memory_limit', '640M');
ini_set('max_execution_time', 0);
set_time_limit(0);
/**
 * @file CadImportacaoFipe.php
 * @author marcioferreira
 * @version 01/10/2013 11:31:30
 * @since 01/10/2013 11:31:30
 * @package SASCAR CadImportacaoFipe.php 
 */

//manipula os dados no BD
require(_MODULEDIR_ . "Cadastro/DAO/CadImportacaoFipeDAO.php");

//classe reponsável em enviar os e-mails
require_once _SITEDIR_ .'modulos/Principal/Action/ServicoEnvioEmail.php';


/**
 * 
 */
class CadImportacaoFipe {
	
	/**
	 * Fornece acesso aos dados necessarios para o módulo
	 * @property CadImportacaoFipeDAO
	 */
	private $dao;
	
	/**
	 * Path da pasta onde a aplicação gerencia os aquivos de importação
	 * @var string
	 */
	private $caminhoArquivo;
    
	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
    public function __construct() 
    {
		global $conn;
        
		$this->dao  = new CadImportacaoFipeDAO($conn);
		
		$this->caminhoArquivo = _SITEDIR_."importacao_fipe";

    }

    /**
     * Retorna lista dos tipo de veículos cadastrados no banco
     * 
     */
    /*public function listarTipoVeiculo(){
    	return $this->dao->listarTipoVeiculo();
    }*/
    
    /**
     * Cria um arquivo(caso não exista) para saída de dados,
     * chama o arquivo que contém a classe e o método responsável em efetuar a importação
     * dos dados do arquivo em background
     * 
     * @throws Exception
     * @return multitype:number string |multitype:number NULL
     */ 
    public function prepararImportacaoDados($tipo){

    	try {
    			
    		if($tipo == 'dadosFipe'){
    			$arquivo_log = 'importacao_fipe';
       				
    		}elseif($tipo == 'tarifa'){
    			$arquivo_log = 'importacao_fipe_tarifa';
    		}
    		
    		if (!$handle = fopen(_SITEDIR_."importacao_fipe/$arquivo_log", "w")) {
    			throw new Exception('Falha ao criar arquivo de log.');
    		}
    		
    		fputs($handle, "Importacao Iniciada\r\n");
    		
    		fclose($handle);
    		
    		chmod(_SITEDIR_."importacao_fipe/$arquivo_log", 0777);

    		//processa o arquivo em background
    		passthru("/usr/bin/php "._SITEDIR_."cad_importacao_fipe_upload.php >> "._SITEDIR_."importacao_fipe/$arquivo_log 2>&1 &");
    		
    		return true;
    		 
    	} catch (Exception $e) {
    		
    		$this->dao->finalizarProcesso(false, $e->getMessage());
    			
    		return false;
    	}
    	
    }
    

    /**
     * Faz as validações e o upload do arquivo csv
     *
     * @throws Exception
     * @return number|multitype:number string
     */
    public function upload($tipo){
    
    	try{
    		
    		$msgErro = array();
    		$file = "";

    		$this->dao->begin();
    		
    		//verifica se existe dados de inicio de processo na tabela
    		$processo = $this->dao->verificarProcessoAndamento();
    		 
    		if(is_object($processo)){
    			throw new Exception('Processo de importação já foi iniciado em :  '.$processo->eifdt_inicio.', aguarde o recebimento do e-mail com a mensagem de finalização.',3);
    		}
    		
    		//validação para importação de dados FIPE, marca, modelo, etc
    		if($tipo == 'dadosFipe'){
    			
    			//tipo do veículo para importação escolhido pelo usuário
    			/*$tipo_veiculo =  (isset($_POST['tipo_veiculo'])) ? $_POST['tipo_veiculo'] : 'null' ;
    			
    			if(empty($tipo_veiculo)){
    				throw new Exception('O tipo de veículo deve ser informado.',0);
    			}*/
    			
    			//arquivo que será enviado
    			$file = $_FILES['arq_importacao'];

    		//validação para importação de dados da tabela tarifária	
    		}elseif($tipo == 'tarifa'){
    			
    			//arquivo que será enviado
    			$file = $_FILES['arq_importacao_tarifa'];
    		}
    		
    		if(empty($file)){
    			throw new Exception('Falha: O arquivo para importação não foi encontrado.',0);
    		}
    	    		
    		list($nome, $ext) = explode(".",$file['name']);

    		$tiposPermitidos = array('text/csv', 'application/force-download');
        		
    		if($ext != 'csv'){
    			if(!in_array($file['type'], $tiposPermitidos)){
    				return 2;
    			}
    		}
    		
    		//limpa a pasta de aquivos menos o arquivo de log
    		$limpaPasta = $this->limparPasta('importacao_fipe');
       		
            if(file_exists($file['tmp_name'])){
            
            	//limpa o cache de arquivo
            	clearstatcache();
            	
            	//cria a pasta se não existir
            	if (!is_dir($this->caminhoArquivo)) {
            		if (!mkdir($this->caminhoArquivo, 0777)) {
            			throw new Exception('Falha ao criar pasta.',0);
            		}
            	}
            	//seta as permissões (escrita, leitura e gravação) na pasta
            	chmod($this->caminhoArquivo, 0777);
            	
            	//retira os espaços em branco do nome do aquivo
            	$file_name = str_replace(" ","_",$file['name']);
            	$temp_name = str_replace(" ","_",$file['tmp_name']);
            	
            	$file_name = str_replace("\\","",$file_name);
            	$file_name = str_replace("'","",$file_name);
				
            	//caminho do arquivo	
            	$file_path = $this->caminhoArquivo.'/'.$file_name;
            	
            	//se o arquivo já existir, apaga
            	if(file_exists($file_path)){
            		unlink($file_path);
            	}
            	
            	//faz o download do aquivo csv
            	$uploadArquivo = move_uploaded_file($temp_name, $file_path);

            	if($uploadArquivo){
            		
            		$resUpload = $this->dao->iniciarProcesso(/*$tipo_veiculo,*/ $file_name, $tipo);
                    		
            		if($resUpload != 1){
            			throw new Exception($resUpload,0);
            		}
            		
            		//confirma os dados no banco
            		$this->dao->commit();
            		
            		//inicia o processo de importação em background
            		$this->prepararImportacaoDados($tipo);
            	}
            	            	
    			return 1;

    		} else {
    			//Erro
    			throw new Exception('Erro ao importar o arquivo.',0);
    		}

    	}catch(Exception $e){
    	
    		$this->dao->rollback();
    	
    		$msgErro['msg'] =  json_encode(utf8_encode($e->getMessage()));
    		$msgErro['cod'] =  $e->getCode();
    		
    		return $msgErro;
    	}
    }
    
	
    /**
     * Este método é consumido em backgound chamado pelo arquivo cad_importacao_fipe_upload.php
     * Efetua a validação dos dados que serão importados do arquivo em uma pasta
     * Verifica os parâmetros de início de importação do BD, ou seja, os dados que precisa estão
     * no BD e no arquivo .csv baixado para a pasta importacao_fipe
     */
    public function importarDados(){
    	
 	    try{
 	    	
 	    	$nomeProcesso = 'cad_importacao_fipe_upload.php';
 	    	
 	    	if(burnCronProcess($nomeProcesso) === true){
 	    		throw new Exception (" O processo [$nomeProcesso] está em processamento.");
 	    	}
 	    	 	   
	    	//verifica se foi iniciado processo no bd
	      	$processo = $this->dao->verificarProcessoAndamento();
	    	 
	    	if(!is_object($processo)){
	    		throw new Exception('Não foi possível processar a importação de dados, processo no banco não iniciado.');
	    	}
	    	
	    	//pesquisa os parâmetros para importação no bd
	    	$dadosImportacao = $this->dao->consultarDadosImportacao();
	    	
	    	if(!is_object($dadosImportacao)){
	    		throw new Exception('Dados para importação não encontrados');
	    	}
	    	
	    	if($dadosImportacao->eiftipo_importacao == 'Tabela FIPE'){
	    		
	    		$tipo = 'dadosFipe';
	    		$nomeColunaArquivo = 'COD_FIPE';
	    		
	    		//recupera o tipo de veículo no bd
	    		/*$cod_desc_tipo = explode("|", $dadosImportacao->eiftipo_veiculo);
	    		
	    		$objTipoVeiculo = new stdClass();
	    		$objTipoVeiculo->cod_tipo_veiculo  = $cod_desc_tipo[0];
	    		$objTipoVeiculo->desc_tipo_veiculo = $cod_desc_tipo[1];*/
	    		
	    	}elseif($dadosImportacao->eiftipo_importacao == 'Categoria Tarifaria'){
	    		
	    		$tipo = 'tarifa';
	    		$nomeColunaArquivo = 'FIPE';
	    		//$objTipoVeiculo = new stdClass();
	    		//$objTipoVeiculo->desc_tipo_veiculo = "";
	    	}
	    	
	    	
	    	//arquivo que contém os dados para importação
	    	$arquivo = $dadosImportacao->eifarquivo;
	    	
	    	//verifica o usuário que iniciou o processo
	    	if(empty($this->dao->usuarioID)){
	    		$this->dao->usuarioID = $dadosImportacao->eifusuoid;
	    	}
	    	
	    	//pasta de arquivos
	    	$caminhoArquivo = $this->caminhoArquivo.'/'.$arquivo;
	    	
	    	//verifica se o aquivo existe na pasta
	    	if(file_exists($caminhoArquivo)){
	    		 
	    		$fh = fopen($caminhoArquivo, 'r');
	    		 
	    		$dados = array();
	
	    		//extrai os dados do aquivo e insere em uma matriz
	    		while (($data = fgetcsv($fh, 1000, ";")) !== FALSE)	{
	    			$dados[] = $data;
	    		}
	
	    		fclose($fh);
	    		 
	    	}else{
	    		throw new Exception('Falha-> arquivo para importação não encontrado.');
	    	}
	    	
	    	//valida se o arquivo possui a coluna com o código FIPE
	    	if($tipo == 'dadosFipe'){
	    		if(strtoupper($dados[0][2]) != $nomeColunaArquivo){
	    			throw new Exception('Layout do arquivo para importação de dados FIPE inválido.');
	    		}
	    		
	    	}elseif($tipo == 'tarifa'){
	    		if(strtoupper($dados[0][2]) != $nomeColunaArquivo){
	    			throw new Exception('Layout do arquivo para importação de categoria tarifária inválido.');
	    		}
	    	}
	    	
	    	### incia o processo de importação dos dados FIPE para o banco
	    	
	    	//percorre as linhas
	    	for($linha = 0; $linha < count($dados); $linha++){
	    		
	    		//percorre as colunas
	    		for($coluna = 0; $coluna < count($dados[0]); $coluna++){

	    			$nomeColuna = "";
	    			$str_nomeColuna = "";

	    			//colunas relacionadas com ano e combustível ex: med14a
	    			$nomeColuna = trim($dados[0][$coluna]);
	    			 
	    			//pega os 3 primeiros caracteres para verificar o ano e o combustível
	    			$str_nomeColuna = trim(substr($nomeColuna, 0, 3));
	    			 
	    			//verifica se existe a coluna do código fipe
	    			if(strtoupper($dados[0][2]) == $nomeColunaArquivo){

	    				if($linha != 0){

	    					$indiceCodFipe = 2;

	    					//recupera o cod fipe da linha para verificar se é válido
	    					$cod_fipe = $dados[$linha][$indiceCodFipe];
	    					
	    					if(empty($cod_fipe)){
	    						$linha = $linha+1;
	    						throw new Exception("O código Fipe não pode ser nulo.  Linha: $linha ");
	    					}

	    					//efetua a validação
	    					$validaCodFipe = $this->validarCodFipe($cod_fipe /*, $objTipoVeiculo->desc_tipo_veiculo*/, $linha, $tipo);
	    					
	    					if($validaCodFipe != 1){

	    						throw new Exception($validaCodFipe);

	    					}else{

	    						//verificar se o código fipe já existe na base
	    						$cosulta_id_fipe = $this->dao->getCodFipe($cod_fipe);

	    						//se o código fipe não existir, faz o insert dos dados, somente para importação dos dados da tabela FIPE
	    						if(!is_object($cosulta_id_fipe) && $tipo == 'dadosFipe'){

	    							//verifica o título da coluna para manipular os dados
	    							if(strtoupper($dados[0][0]) == 'MARCA'){

	    								$indiceMarca = 0;
	    								$novaMarca = "";

	    								//array recebe a marca do arquivo
	    								$novaMarca = $this->removerAcentos($dados[$linha][$indiceMarca]);

	    								//verificar se a marca já existe na base
	    								$insert_id_marca = $this->dao->getMarca($novaMarca);

	    								//se a marca não existir, então, faz o insert retornado o id para inserir na tabela modelo
	    								if(!is_object($insert_id_marca)){
	    									$insert_id_marca = $this->dao->setMarca($novaMarca);
	    								}
	    							}

	    							//verifica se a coluna é de modelo e se existe o id da marca 
	    							if(strtoupper($dados[0][1]) == 'MODELO' && is_object($insert_id_marca)){

	    								$indiceModelo = 1;
	    								$novoModelo = "";

	    								//array recebe o modelo do arquivo
	    								$novoModelo = $this->removerAcentos($dados[$linha][$indiceModelo]);

	    								$modelo = new stdClass();
	    								$modelo->descricao        = $novoModelo;
	    								$modelo->cod_marca        = $insert_id_marca->mcaoid;
	    								$modelo->cod_fipe         = $cod_fipe;
	    								
	    								$cod_id_modelo = $this->dao->getModelo($modelo);

	    								//se o modelo não existir, então, faz o insert na tabela
	    								if(!is_object($cod_id_modelo)){
	    									$cod_id_modelo = $this->dao->setModelo($modelo);
	    								}
	    							}
	    							

	    						//atualiza as informações
	    						}else{

	    							if(is_object($cosulta_id_fipe)){
	    									
	    								$cod_id_modelo = new stdClass();
	    								$cod_id_modelo->mlooid = $cosulta_id_fipe->mlooid;
	    							}

	    						}//fim verificação se cod_Fipe existe
	    							
	    						
	    						//verifica se a coluna é de tipo do veículo e se o tipo informado existe
	    						if(strtoupper($dados[0][4]) == 'CD_TP_VEICULO' && is_object($cod_id_modelo)){
	    							
	    							$ndiceTipo = 4;
	    							
	    							//recebe o tipo que vem do arquivo
	    							$tipoVeiculo = $dados[$linha][$ndiceTipo];
	    							
	    							if(!empty($tipoVeiculo) && is_numeric($tipoVeiculo)){
	    								
	    								//valida se o código existe na tabela tipo_veiculo
	    								$validaCodTipo  = $this->dao->getTipoVeiculo($tipoVeiculo);
	    								
	    								if(is_object($validaCodTipo)){
	    								    									
	    									$novoTipo = new stdClass();
	    									$novoTipo->cod_tipo_veiculo     = $validaCodTipo->cod_tipo_veiculo;
	    									$novoTipo->cod_modelo           = $cod_id_modelo->mlooid;
	    							    									
	    									$ret_novo_tipo = $this->dao->setTipoVeiculo($novoTipo);
	    								
	    								}else{
	    									
	    									$linha_num = $linha+1;
	    									
	    									throw new Exception('Código do tipo de veículo -> '.$tipoVeiculo.', não encontrado.  Linha: ' .$linha_num);
	    								}
	    							}
	    						}
	    						
	    						
	    						//veículos zero Km Álcool
	    						if(strtoupper($dados[0][5]) == 'NOVO_A' && is_object($cod_id_modelo)){

	    							$indiceNovo_a = 5;
	    							$ano_a = 'Zero KM';
	    							$combustivel = 'Álcool';

	    							$valorZeroAlcool = $dados[$linha][$indiceNovo_a];

	    							if(!empty($valorZeroAlcool)){

	    								//busca o código do ano
	    								$cod_ano_a = $this->dao->getCodAno($ano_a);

	    								if(is_object($cod_ano_a)){

	    									//busca o código do combustível
	    									$cod_combustivel = $this->dao->getCodCombustivel($combustivel);

	    									if(is_object($cod_combustivel)){

	    										$dadosModelo = new stdClass();
	    										$dadosModelo->cod_modelo             = $cod_id_modelo->mlooid;
	    										$dadosModelo->cod_modelo_ano         = $cod_ano_a->mdaoid;
	    										$dadosModelo->cod_modelo_combustivel = $cod_combustivel->mdcoid;
	    										$dadosModelo->valor_modelo           = $valorZeroAlcool;

	    										//busca o código do modelo ano combustível
	    										$cod_modelo_ano_combustivel = $this->dao->getCodModeloAnoCombustivel($dadosModelo);

	    										//se não existir, então, faz o insert
	    										if(!is_object($cod_modelo_ano_combustivel)){
	    											$cod_modelo_ano_combustivel = $this->dao->setCodModeloAnoCombustivel($dadosModelo);
	    										}
	    									}

	    								}//fim is_object($cod_ano)
	    							}//fim !empty($valorZeroAlcool)
	    						}//fim NOVO_A


	    						//veículos zero Km Gasolina
	    						if(strtoupper($dados[0][6]) == 'NOVO_G' && is_object($cod_id_modelo)){

	    							$indiceNovo_g = 6;
	    							$ano_g = 'Zero KM';
	    							$combustivel = 'Gasolina';

	    							//pega o valor corresponde
	    							$valorZeroGasolina = $dados[$linha][$indiceNovo_g];

	    							if(!empty($valorZeroGasolina)){
	    									
	    								//busca o código do ano
	    								$cod_ano_g = $this->dao->getCodAno($ano_g);

	    								if(is_object($cod_ano_g)){

	    									//busca o código do combustível
	    									$cod_combustivel = $this->dao->getCodCombustivel($combustivel);

	    									if(is_object($cod_combustivel)){
	    											
	    										$dadosModelo = new stdClass();
	    										$dadosModelo->cod_modelo             = $cod_id_modelo->mlooid;
	    										$dadosModelo->cod_modelo_ano         = $cod_ano_g->mdaoid;
	    										$dadosModelo->cod_modelo_combustivel = $cod_combustivel->mdcoid;
	    										$dadosModelo->valor_modelo           = $valorZeroGasolina;
	    											
	    										//busca o código do modelo ano combustível
	    										$cod_modelo_ano_combustivel = $this->dao->getCodModeloAnoCombustivel($dadosModelo);

	    										//se não exitir, faz o insert
	    										if(!is_object($cod_modelo_ano_combustivel)){
	    											$cod_modelo_ano_combustivel = $this->dao->setCodModeloAnoCombustivel($dadosModelo);
	    										}
	    									}

	    								}//fim is_object($cod_ano)
	    							}//fim !empty($valorZeroAlcool)

	    						}//fim  NOVO_G*/

	    						
	    						//verifica as linhas e colunas que contenham os valores
	    						if(strtoupper($str_nomeColuna) == 'MED' && is_object($cod_id_modelo)){

	    							//pega o valor médio
	    							$valorMedio = $dados[$linha][$coluna];
	    								
	    							if(!empty($valorMedio)){

	    								//pega o caractere referente ao ano exe: 14
	    								$ano_coluna = substr($nomeColuna, 3, -1);

	    								//verifica e retorna o ano com 4 dígitos
	    								$ano_retorno = $this->verificarAno(trim($ano_coluna));

	    								//consulta se o ano está na tabela
	    								$cod_ano_med = $this->dao->getCodAno($ano_retorno);

	    								if(!is_object($cod_ano_med)){
	    									//insere o ano caso não exista na tabela
	    									$cod_ano_med = $this->dao->setCodAno($ano_retorno);
	    								}

	    								if(is_object($cod_ano_med)){

	    									//pega o caractere referente ao combustivel exe: a
	    									$tipo_combustivel_coluna = substr($nomeColuna, 5, 1);
	    										
	    									if($tipo_combustivel_coluna == 'a'){
	    										$combustivel = 'Álcool';

	    									}else if($tipo_combustivel_coluna == 'g'){
	    										$combustivel = 'Gasolina';
	    									}
	    										
	    									//busca o código do combustível
	    									$cod_combustivel = $this->dao->getCodCombustivel($combustivel);

	    									if(is_object($cod_combustivel)){

	    										$dadosModelo = new stdClass();
	    										$dadosModelo->cod_modelo             = $cod_id_modelo->mlooid;
	    										$dadosModelo->cod_modelo_ano         = $cod_ano_med->mdaoid;
	    										$dadosModelo->cod_modelo_combustivel = $cod_combustivel->mdcoid;
	    										$dadosModelo->valor_modelo           = $valorMedio;

	    										//verifca se já existe registro de modelo ano combustível
	    										$cod_modelo_ano_combustivel = $this->dao->getCodModeloAnoCombustivel($dadosModelo);

	    										if(!is_object($cod_modelo_ano_combustivel)){
	    											$cod_modelo_ano_combustivel = $this->dao->setCodModeloAnoCombustivel($dadosModelo);
	    										}
	    									}

	    								}//fim is_object($cod_ano_med)

	    							}//fim !empty($valorMedio)

	    						}//fim verificação coluna == med
	    						

	    						//verifica a coluna de números de passageiros
	    						if(strtoupper($nomeColuna) == 'NUM_PASSAG' && is_object($cod_id_modelo)){
	    							
	    							//pega o valor
	    							$passageiros = $dados[$linha][$coluna];
	    						
	    							if(!empty($passageiros) && is_numeric($passageiros)){
	    								
	    								$dadosPassag = new stdClass();
	    								$dadosPassag->cod_modelo             = $cod_id_modelo->mlooid;
	    								$dadosPassag->quant_passageiros      = $passageiros;
	    								
	    								//atualiza a quantidade de passageiros na tabela modelo
	    								$ret_num_passag = $this->dao->setNumPassageiros($dadosPassag);
	    							}
	    						}

	    						
	    						### CATEGORIA TARIFÁRIA ###
	    						
	    						//verifica a coluna Procedência = Nacional ou Importado
	    						if(strtoupper($this->removerAcentos($nomeColuna)) == 'PROCEDENCIA' && isset($cod_id_modelo->mlooid)){
	    						
	    							//pega o valor
	    							$procedencia = $dados[$linha][$coluna];
	    								
	    							if(!empty($procedencia)){
	    									
	    								$dadosProcedencia = new stdClass();
	    								$dadosProcedencia->cod_modelo             = $cod_id_modelo->mlooid;
	    								$dadosProcedencia->nome_procedencia       = $procedencia;
	    									
	    								//atualiza os dados
	    								$ret_procedencia = $this->dao->setDadosCategoriaTarifaria($dadosProcedencia);
	    							}
	    						}
	    						
	    						
	    						//verifica a coluna de  Categoria Base
	    						if(strtoupper($this->removerAcentos($nomeColuna)) == 'CATEGORIA BASE' && isset($cod_id_modelo->mlooid)){
	    								
	    							//pega o valor
	    							$categoria_base = $dados[$linha][$coluna];
	    								
	    							if(!empty($categoria_base)){
	    						
	    								$dadosCategoriaBase = new stdClass();
	    								$dadosCategoriaBase->cod_modelo             = $cod_id_modelo->mlooid;
	    								$dadosCategoriaBase->nome_categoria_base    = $categoria_base;
	    						
	    								//atualiza os dados
	    								$ret_categoria_base = $this->dao->setDadosCategoriaTarifaria($dadosCategoriaBase);
	    							}
	    						}
	    							    						
	    						//verifica a coluna de Código Categoria base:
	    						if(strtoupper($this->removerAcentos($nomeColuna)) == 'CODIGO CATEGORIA BASE:' && isset($cod_id_modelo->mlooid)){
	    								
	    							//pega o valor
	    							$cod_categoria_base = $dados[$linha][$coluna];
	    								
	    							if(!empty($cod_categoria_base)){
	    									
	    								$dadosCodCategoriaBase = new stdClass();
	    								$dadosCodCategoriaBase->cod_modelo             = $cod_id_modelo->mlooid;
	    								$dadosCodCategoriaBase->cod_categoria_base     = $cod_categoria_base;
	    									
	    								//atualiza os dados
	    								$ret_cod_categoria_base = $this->dao->setDadosCategoriaTarifaria($dadosCodCategoriaBase);
	    							}
	    						}
	    						
	    						
	    					}//fim else se código fipe válido
	    					
	    				}//fim se $linha != 0
	    			
	    			//fim da verificação da coluna fipe
	    			}
	    				
	    		}//fim for $coluna

	    		 
	    	}//fim  for $linha

	    	//limpa a pasta de aquivos menos o arquivo de log
    		$limpaPasta = $this->limparPasta('importacao_fipe');
	    	
	    	$msg = 'Processo de importação '.$dadosImportacao->eiftipo_importacao.' finalizado com sucesso.';
	    	
	    	//finaliza processo com sucesso
	    	$finalizarProcesso = $this->dao->finalizarProcesso(true, $msg);
	    	
	    	//recupera os dados do processo finalizado com sucesso para enviar por e-mail
	    	$dadosProcesso = $this->dao->verificarProcessoFinalizado();
	    	
	    	//envia email de sucesso
	    	$enviarEnmail = $this->enviarEmail($msg, true, $dadosProcesso, $tipo);
		    		    	
	    }catch (Exception $e){
	    	
	    	//deleta o arquivo na pasta
	    	unlink($caminhoArquivo);
	    	
	    	//finaliza processo com erro 
	    	$finalizarProcesso = $this->dao->finalizarProcesso(false, $e->getMessage());
	    	
	    	//recupera os dados do processo finalizado com erro para enviar por e-mail
	    	$dadosProcesso = $this->dao->verificarProcessoFinalizado();
	    	
	    	$enviarEnmail = $this->enviarEmail($e->getMessage(), false, $dadosProcesso, $tipo);
	    	
	    	
	    	return $e->getMessage();
	    }
    }
    
    
    /**
     * Efetua a validação do código fipe, seguindo os padrões e tamanho
     * 
     * @param string $codigo_fipe
     * @param string $tipo_veiculo
     * @param int $linha
     * @param string $tipo
     * @return string|boolean
     */
	
    private function validarCodFipe($codigo_fipe/*, $tipo_veiculo = NULL*/, $linha, $tipo){
    
    	$num_linha = $linha+1;
    	
    	//valida o padrão do código
    	if(strlen(trim($codigo_fipe)) != 8){
    		return 'O código Fipe deve conter 8 caracteres. Linha: '. $num_linha;
    	}
    	
    	if(!strstr(trim($codigo_fipe), '-')){
    		return 'O código Fipe: '.$codigo_fipe.' deve estar no seguinte formato XXXXXX-X . Linha: '. $num_linha;
    	
    	}else{
    		//verifica a quantidade de hifens encontrados
    		$quantHifen = strstr(trim($codigo_fipe), '-');
    		$quantHifen = explode("-", $quantHifen);
    		
    		if(count($quantHifen) != 2){
    			return 'O código Fipe: '.$codigo_fipe.' deve estar no seguinte formato XXXXXX-X . Linha: '. $num_linha;
    		}
    	}
    	
		//pega o posição após o hífen
    	$cod_fipe_ = explode("-", $codigo_fipe);
      	
    	//se o hifen não tiver na posição correta, gera erro
    	if(strlen(trim($cod_fipe_[1])) != 1){
    		return 'O código Fipe: '.$codigo_fipe.'  deve estar no seguinte formato XXXXXX-X . Linha: '. $num_linha;
    	}

    	//se a importação for de dados da fipe, faz a validações do tipo de importação pelo tipo do veículo informado
    	/*if($tipo == 'dadosFipe'){
    		
    		//pega o primeiro dígito do código fipe
    		$inicial_cod_fipe = $codigo_fipe[0];
    		 
    		//Carros e Utilitários Pequenos
    		if($inicial_cod_fipe == 0){

    			if($tipo_veiculo !== 'Carros e Utilitários Pequenos'){
    				return 'Tipo de veículo escolhido "'.$tipo_veiculo.'"  não corresponde com o tipo de veículo "Carros e Utilitários Pequenos" dos dados a serem importados.  Linha: '.$num_linha.'. ';
    			}
    			 
    			return true;
    			 
    		//Caminhões e Micro-Ônibus
    		}elseif($inicial_cod_fipe == 5){

    			if($tipo_veiculo !== 'Caminhões e Micro-Ônibus'){
    				return 'Tipo de veículo escolhido "'.$tipo_veiculo.'"  não corresponde com o tipos de veículo "Caminhões e Micro-Ônibus" dos dados a serem importados.  Linha: '.$num_linha.'.';
    			}

    			return true;
    			 
    		//Motos
    		}elseif($inicial_cod_fipe == 8){

    			if($tipo_veiculo !== 'Motos'){
    				return 'Tipo de veículo escolhido "'.$tipo_veiculo.'"  não corresponde com o tipo de veículo "Motos" dos dados a serem importados.  Linha: '.$num_linha.'.';
    			}

    			return true;
    			 
    		//não implementado
    		}else{
    			return 'Tratamento para o código Fipe " '.$codigo_fipe.' "  não implementado.  Linha: '.$num_linha.'. ';
    		}
    		
    	}else{
    		return true;
    	}
    	*/
    	
    	return true;
    }
    
	
    public function envioConcluido(){
        return 2;
    }

    
    /**
     * Helper para ano com 2 dígitos
     * 
     * @param unknown $ano
     * @return number|boolean
     */
    private function verificarAno($ano){
    	
    	if(!empty($ano)){
    		
    		if($ano >= 80 && $ano <= 99){
    			return '19'.trim($ano);
    		}elseif($ano >= 00){
    			return '20'.trim($ano);
    		}
    	}
    	return false;
    }
    
    
    /**
     * Remove acentuação de string.
     * @param String $str
     * @return String
     */
    public function removerAcentos($str){
    	
    	$busca     = array("à","á","ã","â","ä","è","é","ê","ë","ì","í","î","ï","ò","ó","õ","ô","ö","ù","ú","û","ü","ç", "'", '"','º','ª','°', '&');
    	$substitui = array("a","a","a","a","a","e","e","e","e","i","i","i","i","o","o","o","o","o","u","u","u","u","c", "" , "" ,'' ,'' ,'', '');
    	
    	$str       = str_replace($busca,$substitui,$str);
    	
    	$busca     = array("À","Á","Ã","Â","Ä","È","É","Ê","Ë","Ì","Í","Î","Ï","Ò","Ó","Õ","Ô","Ö","Ù","Ú","Û","Ü","Ç","‡","“", "<", ">" );
    	$substitui = array("A","A","A","A","A","E","E","E","E","I","I","I","I","O","O","O","O","O","U","U","U","U","C", ""  ,"" , "" , "");
    	
    	$str       = str_replace($busca,$substitui,$str);
    	return $str;
    }
    
    /**
     * Apaga todos os arquivos da pasta, menos o arquivo informado no parâmetro.
     * 
     * @param string $arquivo
     * @return void|number
     */
    private function limparPasta($arquivo){

    	$dir = $this->caminhoArquivo."/";

    	//verifica se a pasta existe
    	if(is_dir($dir)){
    		 
    		//lê a pasta
    		if($handle = opendir($dir))	{

    			while(($file = readdir($handle)) !== false){
    					
    				if($file != '.' && $file != '..'){

    					if( $file != $arquivo)	{
    						//apaga os arquivo diferente do informado no parâmetro
    						unlink($dir.$file);
    					}
    				}
    			}
    		}
    	}
    	return 0;
    }

    
    /**
     * Envia e-mail previamente parâmetrizado no BD com o resultado da importação do arquivo
     * 
     * @param string $msg
     * @param boolean $status
     * @param object $dadosProcesso
     * @param string $tipo
     * @throws exception
     */
    
    private function enviarEmail($msg, $status, $dadosProcesso, $tipo){
    	
    	$dadosEmail = Array();
    	
    	//instância de classe de configurações de servidores para envio de email
    	$servicoEnvioEmail = new ServicoEnvioEmail();
    	
    	//recupera email dos usuários da tabela de parâmetros
    	$emailUsuarioParam = $this->dao->getEmailUsuarioParametro();
    	
    	if(is_array($emailUsuarioParam)){
    		$dadosEmail = $emailUsuarioParam;
    	}
    	
    	//recupera os dados do usuário que iniciou o processo
      	$emailUsuarioProcesso = $this->dao->getDadosUsuarioProcesso($dadosProcesso->id_usuario);

      	if(is_array($emailUsuarioProcesso)){
      		$dadosEmail = $emailUsuarioProcesso;
      		$nomeUsuarioProcesso = $emailUsuarioProcesso[0]['nm_usuario'];
      	
      	}else{
      		$nomeUsuarioProcesso = 'Usuário [ '.$this->dao->usuarioID.' ] que iniciou o processo, não possui e-mail cadastrado.';
      	}
      		      	
      	if(is_array($emailUsuarioParam) && is_array($emailUsuarioProcesso)){
      		
      		$dadosEmail = "";
      		//junta o array com os dados para enviar o e-mail
      		$dadosEmail = array_merge($emailUsuarioParam, $emailUsuarioProcesso );
      	}
    	
       	
    	if(count($dadosEmail) > 0 ){
    		
    		if($tipo == 'dadosFipe'){
    			$str_titulo = 'FIPE';
    		
    		}elseif($tipo == 'tarifa'){
    			$str_titulo = 'Categoria Tarifária';
    		}
    		
    		if($status){
    			$assunto = 'Processo de importação '.$str_titulo.' finalizado com sucesso';
    		}else{
    			$assunto = 'Falha no processo de importação '.$str_titulo;
    		}
    		
    		$msg_status = $status ? 'Sucesso' : ' <font color="red">Falha</font>';
    		
    		$corpo_email = 'Processo de importação '.$str_titulo.' finalizado.<br/><br/>
							Inicio do processo: '.$dadosProcesso->inicio.'  <br/>
							Fim do processo: '.$dadosProcesso->termino.' <br/>
							Usuário do processo:  '.$nomeUsuarioProcesso.'  <br/>
							Status: '.$msg_status.'   <br/> 
							Mensagem:  '.$msg.'';
    		
    		//recupera e-mail de testes
    		if($_SESSION['servidor_teste'] == 1){
    		
    			//recupera email de testes da tabela parametros_configuracoes_sistemas_itens
    			$emailTeste = $this->dao->getEmailTeste();
    			 
    			if(!is_object($emailTeste)){
    				throw new exception('E necessario informar um e-mail de teste em ambiente de testes.');
    			}
    		}
    		
	    	foreach ($dadosEmail as $email_usu){
	    		 
	    		//envia o email
	    		$envio_email = $servicoEnvioEmail->enviarEmail(
	    				$email_usu['usuemail'],
	    				$assunto,
	    				$corpo_email,
	    				$arquivo_anexo = null,
	    				$email_copia = null,
	    				$email_copia_oculta = null,
	    				1,//sascar
	    				$emailTeste->pcsidescricao//$email_desenvolvedor = null
	    		);
	
	    		if(!empty($envio_email['erro'])){
	    			throw new exception($envio_email['msg']);
	    		}
	    	}
	    	
	    	return true;
    	}
    	
    	return false;
    }
	
}