<?php
header ( 'Content-Type: text/html; charset=ISO-8859-1' );
/**
 * Classe de persistência de dados
 */

require_once (_MODULEDIR_ . 'Financas/DAO/FinImportarCustoMedioProdutoDAO.php');
require_once _SITEDIR_ . "lib/Components/CsvWriter.php";
require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

class FinImportarCustoMedioProduto {
	
	private $dao;
	private $cd_usuario;
	private $view;
	
	/**
	 * Mensagem de alerta para campos obrigatórios não preenchidos
	 * @const String
	 */
	const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";
	const MENSAGEM_ALERTA_SUCESSO = "Arquivo importado com sucesso.";
	const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
	const MENSAGEM_ALERTA_DADOS_DATAS_EXISTENTES = "Mês/ Ano de importação já possuem informações na base. Favor informar o Mês/ Ano corretos, para realizar a importação.";
	const MENSAGEM_ALERTA_CAMPOS_ARQUIVO_INCORRETO = "O arquivo selecionado não possui o formato esperado.";
	const MENSAGEM_ALERTA_ERRO_CADASTRADO = "O arquivo possui alguns erros de códigos não encontrados, ou de preenchimento, como podem ser verificados no arquivo de erro abaixo “erro_importacao_custo_medio.csv”.";
	const MENSAGEM_ALERTA_CODIGO_NENHUM_PRODUTO = "Registros não possuem código de produto cadastrado. Verifique arquivo de erro gerado.";
        const MENSAGEM_ALERTA_ERRO_EXCLUSAO = "Este arquivo não pode ser excluído. É permitido excluir arquivos somente do mês / ano de referência vigente.";
        const MENSAGEM_ALERTA_DUPLICIDADE = "O Arquivo importado possui produto duplicado, nenhum registro foi importado, verifique o arquivo de Log, corrija o arquivo a ser importado e tente novamente.";
        const MENSAGEM_ALERTA_DATA_VIGENCIA = "O Mês e Ano de referência é maior do que a data atual do sistema, corrija a data e tente novamente. ";
	
	function __construct() {
		
		global $conn;
		$this->dao = new FinImportarCustoMedioProdutoDAO ( $conn );
		$this->cd_usuario = $_SESSION ['usuario'] ['oid'];
		
		$this->view = new stdClass ();
		
		// Mensagem
		$this->view->mensagemErro = '';
		$this->view->mensagemAlerta = '';
		$this->view->mensagemSucesso = '';
		
		// Dados para view
		$this->view->download = null;
		$this->view->arquivos = null;
		
		$this->view->paginacao = null;
	}
	
	//metodo da pagina inicial de importacao
	public function index() {
		
		//chama o metodo que pesquisa se já tem algum importe e realiza a paginação
		$this->view->arquivos = $this->buscaArquivosImportadosCustoMedio ();
		
		include (_MODULEDIR_ . 'Financas/View/fin_importar_custo_medio_produto/index.php');
	}
	
	//verifica se já existe arquivos importados e realiza a paginacao
	public function buscaArquivosImportadosCustoMedio() {
		
		//$paginacao = new PaginacaoComponente ();
		
	//	$resultado = $this->dao->listaArquivosImportadosCustoMedio ();
		
	//	$this->view->paginacao = $paginacao->gerarPaginacao ( count ( $resultado ) );
		
		//$resultado = $this->dao->listaArquivosImportadosCustoMedio ( $paginacao->buscarPaginacao () );
		
		$resultado = $this->dao->listaArquivosImportadosCustoMedio ();
		
		return $resultado;
	}
	
	//tela que recebe o arquivo csv 
	public function telaImportacao() {
		
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
			
			$data = $_POST ['mes_ano'];
			$file = $_FILES ['arquivo_csv'] ['tmp_name'];
			
			$dataRef = explode ( "/", $data );
			$dataAtual = $dataRef [1] . "-" . $dataRef [0] . "-01";
                        $data_sistema = date("m/Y");
			
			//verifica se a data e o arquivo está vazio
			if ($data == '' || empty ( $data ) || $data == null) {
                            $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS;
                        } else if ($data > $data_sistema) {
                            $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_DATA_VIGENCIA;
			} else if ($file == '' || empty ( $file ) || $file == null) {
                            $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS;
			} else {
				//verifica se já existe arquivo importado na data de referência
				$custoMedioResult = $this->dao->listaCustoMedioData ( $dataAtual );
				
				//caso exista da a mensagem de data já existente
				if ($custoMedioResult > 0) {
					$this->view->mensagemAlerta = self::MENSAGEM_ALERTA_DADOS_DATAS_EXISTENTES;
				} else {
					
					//chama o metodo para realizar as validações e cadastro no banco de dados do arquivo e passa a data atual
					$records = $this->_importarArquivo ( $dataAtual );
					//se o retorno do metodo retornar falso da a mensagem de arquivo de formato errado
					if (!$records && !is_array($records)) {
						$this->view->mensagemAlerta = self::MENSAGEM_ALERTA_CAMPOS_ARQUIVO_INCORRETO;
					} else {

						// count($records) > 0 || !empty($records) || $records != null ||
						//verifica se o retorno é diferente de vazio
						if (count($records) > 0 || !empty($records) || $records != null) {
							//se for diferente de vazio chama o metodo para gerar o csv com os erros
							$this->view->download = $this->gerarCSV ( $records );
							//retorna a mensagem de erro
                                                        if(array_key_exists('duplicidade',$records[0]))
                                                        {
                                                            $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_DUPLICIDADE;
                                                        }
                                                        else
                                                        {
                                                           $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_ERRO_CADASTRADO; 
                                                        }
						} else {
							//caso o arquivo for 
							$this->view->mensagemSucesso = self::MENSAGEM_ALERTA_SUCESSO;
						}
					}
				}
			}
		}
		
		//chama o metodo buscando os resultados e faz a paginacao do arquivos já importados
		$this->view->arquivos = $this->buscaArquivosImportadosCustoMedio ();
		include (_MODULEDIR_ . 'Financas/View/fin_importar_custo_medio_produto/index.php');
	}
	
	/**
	 * Importa arquivo CSV e retorna uma array associativo com chaves
	 * específicas para cada layout
	 *
	 * @param int $operation        	
	 * @return array
	 */
	public function _importarArquivo($datareferencia) {
		
		unset ( $records );
		
		$retorno = true;
		
		
		// Valida a extensão do arquivo
		if (! preg_match ( '/\.csv$/', $_FILES ['arquivo_csv'] ['name'] )) {
			$retorno = false;
		}
		
		$file = $_FILES ['arquivo_csv'] ['tmp_name'];
		
		$lines = explode ( "\n", file_get_contents ( $file ) );
		
	
		$cabecalho = explode ( ";", $lines [0] );
		
		
		//verifica se o cabelho codigo do produto está vazio
		if ($cabecalho [0] == '' || $cabecalho [0] == null || empty ( $cabecalho [0] )) {
			$retorno = false;
		}
		
		//verifica se o cabelho valor do produto está vazio
		if ($cabecalho [1] == '' || $cabecalho [1] == null || empty ( $cabecalho [1] )) {
			$retorno = false;
		}
		
		// Verifica se o campo título está preenchido corretamente
		if (! preg_match ( '/[A-Za-z]/', $cabecalho [0] )) {
			$retorno = false;
		}
		
		// Verifica se o campo título está preenchido corretamente
		if (! preg_match ( '/[A-Za-z]/', $cabecalho [1] )) {
			$retorno = false;
		}
		
		// $NotaAVencer != '' || $NotaAVencer != null || $NotaAVencer > 0
	
		// varifica se o arquivo tem conteudo
		if (($lines [1] == '') || ($lines [1] == null) || empty ( $lines [1] )) {
			$retorno = false;
		}
		
		
		unset ( $lines [0] );
		
		//se o arquivo tiver conteudo e cabeçaçho estiver ok começa a leitura de cada linha do arquivo
		if ($retorno == true) {
                    
                    $records = array();
                    $duplicity = array();
                    $count = 0;
			
                    foreach ( $lines as $line ) {


                            //variavel somente para verificar se entrou em algum erro ou não para fazer a validação final
                            $resposta = true;

                            // Pula linhas vazias
                            if (strlen ( trim ( $line ) ) == 0) {
                                    continue;
                            }

                            $cols = explode ( ";", $line );                            

                            $codigoProduto = trim ( $cols [0] );

                            $custoMedio = trim ( $cols [1] );
                            
                            $duplicity[$count] = $codigoProduto;
                            $teste = array_count_values($duplicity);
                            
                            //verifica duplicidade de registros
                            if($teste[$codigoProduto] > 1)
                            {
                                $records [] = array (
                                    'codigoproduto' => $codigoProduto,
                                    'customedio'  => $custoMedio,
                                    'duplicidade' => 'Registro Duplicado'
                                );
                                $resposta = false;
                            }

                            
                            //faz a validação do formato do campo medio , verifica casas decimais
                            if (! preg_match ( '/^[0-9]+([.,][0-9]{2,20})+([,][0-9]{1,2})?$/', $custoMedio ) && $custoMedio != '') {
                                    $records [] = array (
                                                    'codigoproduto' => $codigoProduto,
                                                    'customedio' => $custoMedio
                                    );

                                    $resposta = false;

                            }



                            if (($custoMedio == '' || $custoMedio == null || empty ( $custoMedio)) && $resposta != false) {

                                    $records [] = array (
                                                    'codigoproduto' => $codigoProduto,
                                                    'customedio' => $custoMedio
                                    );

                                    $resposta = false;
                            }else {

                                    if($custoMedio != '' &&  $resposta != false) {

                                            $valor = explode(",", $custoMedio);
                                            $valorPonto = explode(".",$custoMedio);

                                            if(count($valorPonto) > 2){
                                                    $records [] = array (
                                                                    'codigoproduto' => $codigoProduto,
                                                                    'customedio' => $custoMedio
                                                    );

                                                    $resposta = false;
                                            }

                                            if((count($valor) < 3) && $resposta != false){
                                                    if($valor[1] != '') {
                                                            if(strlen($valor[1]) > 2 ) {
                                                                    $records [] = array (
                                                                                    'codigoproduto' => $codigoProduto,
                                                                                    'customedio' => $custoMedio
                                                                    );
                                                                    $resposta = false;
                                                            }
                                                    }else{
                                                            $records [] = array (
                                                                            'codigoproduto' => $codigoProduto,
                                                                            'customedio' => $custoMedio
                                                            );

                                                            $resposta = false;
                                                    }
                                            }else{
                                                    $records [] = array (
                                                                    'codigoproduto' => $codigoProduto,
                                                                    'customedio' => $custoMedio
                                                    );

                                                    $resposta = false;
                                            }

                                    }
                            }



                            //verifica se o campo custo medio esta fazio, caso não esteja vazio ele armazena



                            //verifica se o codigo do produto é numerico caso não for armazena o erro
                            if (! is_numeric ( $codigoProduto ) && $codigoProduto != '' ) {
                                    $records [] = array (
                                                    'codigoproduto' => $codigoProduto,
                                                    'customedio' => $custoMedio 
                                    // 'titvl_desconto' => (floatval ( $cols [5] ) > 0) ? floatval ( $cols [5] ) : '0.00', )
                                    );

                                    $resposta = false; 
                            }else if(preg_match('/\s/', $codigoProduto)){

                                    $records [] = array (
                                                    'codigoproduto' => $codigoProduto,
                                                    'customedio' => $custoMedio
                                    );

                                    $resposta = false;

                            }else	if ($codigoProduto != '' || $codigoProduto != null || ! empty ( $codigoProduto )) {
                                    //verifica se o codigo do produto é numerico
                                    if (is_numeric ( $codigoProduto )) {
                                            $listaProduto = $this->dao->listaProdutoCodigo ( $codigoProduto );
                                    }else{
                                            $records [] = array (
                                                            'codigoproduto' => $codigoProduto,
                                                            'customedio' => $custoMedio
                                            );
                                            $resposta =false;
                                    }

                            } else{

                                    $records [] = array (
                                                    'codigoproduto' => $codigoProduto,
                                                    'customedio' => $custoMedio 
                                    // 'titvl_desconto' => (floatval ( $cols [5] ) > 0) ? floatval ( $cols [5] ) : '0.00', )
                                                                            );
                                    $resposta =false;
                            }


                            //se a pesquisa por codigo do produto for maior que zero ele armazena os dados para cadastro
                            if ($listaProduto > 0 && $resposta != false) {

                                    $valor = str_replace ( ".", "", $custoMedio );
                                    $valor = str_replace ( ",", ".", $valor );
                                    $data [] = array (
                                                    'data' => $datareferencia,
                                                    'codigoproduto' => $codigoProduto,
                                                    'customedio' => $valor,
                                                    'usuario' => $this->cd_usuario 
                                    );
                            } else if($resposta != false) {

                                            $records [] = array (
                                                            'codigoproduto' => $codigoProduto,
                                                            'customedio' => $custoMedio 
                                                                                    );


                            }

                            $count ++;
			}

			//caso não tenha nenhum erro ele salva no banco de dados
			if (count ( $records ) == 0 && empty ( $records ) || $records == null) {
				$this->dao->salvaCustoMedio ( $data );
			}

			//retorna os erros ou vazio
			return $records;
		}
		
		// $records[] = array('totalRegistro' => $totalRegistro);
		//caso tenha algum problema de cabeçalho retorna falso
		return $retorno;
	}
	
	
	//cria os arquivos csv de erro
	public function gerarCSV($records) {
		
		try {
			
			$content .= "Código do produto;Custo médio\n";
			
			foreach ( $records as $key ) {
				
				$content .= str_replace ( ';', '', $key [codigoproduto] ) . ";";
				$content .= str_replace ( ';', '', $key [customedio] ) . "\n";
			}
		} catch ( Exception $e ) {
		}
		
		$arquivo ['file_path'] = "/var/www/docs_temporario/";
		$arquivo ['file_name'] = "erro_importacao_custo_medio.csv";
		file_put_contents ( $arquivo ['file_path'] . $arquivo ['file_name'], $content );
		
		$arquivo = $arquivo ['file_path'] . $arquivo ['file_name'];
		
		return $arquivo;
	}
        
        //cria o csv retornando o dados do arquivo selecionado
        public function gerarCSVdoRegistro()
        {
            $usuario = $_GET['usu'];
            $data    = $_GET['dt'];
            
            $records = $this->dao->getRegistrosArquivosImportados($usuario, $data);
            
            try 
            {     
                $content .= "Código do produto;Custo médio\n";
                
                foreach($records as $key)
                {
                    $content .= str_replace(';', '', $key->pcmprdoid) . ";";
                    $content .= str_replace('.', ',', $key->pcmcusto_medio) . "\n";
                }
                
            }
            catch (Exception $e) {                
            }
            
            $arquivo ['file_name'] = "importacao_custo_medio.csv";
            file_put_contents ($arquivo ['file_name'],$content);

            $arquivo =$arquivo ['file_name'];
                      
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);
            header("content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"".$arquivo."\";");
            header("Content-Transfer-Encoding: binary");
            set_time_limit(0);
            
            readfile("$arquivo") or die("Arquivo não encontrado!");
        }
        
        //Exclui os registros selecionados
        public function excluiRegistro() 
        {           
            $usuario = $_GET['usu'];
            $data_registro = $_GET['dt'];
            $data_referencia = $_GET['ref'];
            $data_sistema = date("m/Y");
            $valido = $_GET['val'];
            
            if($data_referencia == $data_sistema && $valido == "true")
            {
                $this->dao->excluiArquivosImportadosCustoMedio($usuario, $data_registro);
            }
            else
            {
                $this->view->mensagemErro = self::MENSAGEM_ALERTA_ERRO_EXCLUSAO;
            }
            
            
            //chama o metodo buscando os resultados e faz a paginacao do arquivos já importados
            $this->view->arquivos = $this->buscaArquivosImportadosCustoMedio ();
            include (_MODULEDIR_ . 'Financas/View/fin_importar_custo_medio_produto/index.php');
        }
}