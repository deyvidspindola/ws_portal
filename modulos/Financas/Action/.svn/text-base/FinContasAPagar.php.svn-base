<?php

/**
 * Classe FinContasAPagar.
 * Camada de regra de negócio.
 *
 * @package  Financas
 * @author 
 *
 */

require_once "lib/funcoes.php";
require_once _SITEDIR_.'gerador_arquivo.php';
require_once _SITEDIR_.'lib/Components/Paginacao/PaginacaoComponente.php';
require_once _SITEDIR_.'lib/Components/CsvWriter.php';
require_once _SITEDIR_.'lib/phpMailer/class.phpmailer.php';

// [START][ORGMKTOTVS-1185] - Leandro Corso
require_once _MODULEDIR_.'core/infra/autoload.php';
use module\Parametro\ParametroIntegracaoTotvs;
define(INTEGRACAO, ParametroIntegracaoTotvs::getIntegracao('INTEGRACAO_ATIVA'));
// [END][ORGMKTOTVS-1185] - Leandro Corso

class FinContasAPagar {

    /** Objeto DAO da classe */
    private $dao;
    private $daoCRN;

    /** propriedade para dados a serem utilizados na View. */
    private $view;

    /** Usuario logado */
    private $usuarioLogado;

    private $arquivoRemessa;

    private $ambiente;

    private $caminhoLocal;

    private $arquivo;

    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_SUCESSO_ENVIO              = "Remessa enviada com sucesso.";
    const MENSAGEM_SUCESSO_AUTORIZAR          = "Títulos autorizados com sucesso.";
    const MENSAGEM_SUCESSO_LIBERAR_REENVIO    = "Títulos liberados para reenvio com sucesso.";
    const MENSAGEM_SUCESSO_ARQUIVO            = "Arquivo gerado com sucesso.";
    const MENSAGEM_ALERTA_NENHUM_REGISTRO     = "Nenhum registro encontrado.";
    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_ALERTA_CAMPOS_TAMANHO      = "A Descrição deve ter no mínimo dois dígitos.";
    const MENSAGEM_ALERTA_DUPLICIDADE         = "Já existe um registro com a mesma descrição.";
    const MENSAGEM_ALERTA_TITULO              = "Nenhum título selecionado.";
    const MENSAGEM_ALERTA_DATA_INICIAL_FINAL  = "A data inicial não pode ser maior que a data final";



    /**
     * Método construtor.
     * @param $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

        $this->dao                   = (is_object($dao)) ? $this->dao = $dao : NULL;
        $this->daoCRN 				 = new BaixaContasAPagarItauDAO();
        $this->arquivoRemessa        = NULL;
        $this->ambiente              = ( _AMBIENTE_ == "PRODUCAO" ? "PRODUCAO" : "TESTE" );
        $this->caminhoLocal          = '/var/www/docs_temporario/';
        $this->view                  = new stdClass();
        $this->view->mensagemErro    = '';
        $this->view->mensagemAlerta  = '';
        $this->view->mensagemSucesso = '';
        $this->view->dados           = null;
        $this->view->parametros      = null;
        $this->view->paginacao       = null;
        $this->view->status          = false;
        $this->view->naoGerado       = '';
        $this->view->paginacao       = null;
        $this->view->ordenacao       = null;
        $this->usuarioLogado         = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        $this->arquivo               = new stdClass();

        //Se nao tiver nada na sessao assume usuario AUTOMATICO (para CRON e WebService)
        $this->usuarioLogado         = (empty($this->usuarioLogado)) ? 2750 : intval($this->usuarioLogado);
        
    }

    /**
     * Reponsável também por realizar a pesquisa invocando o método privado
     * @return void
     */
    public function index() {

        try {

            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();

            $this->view->empresas = $this->retornaEmpresas();
            $this->view->bancos   = $this->retornaBanco();
            $this->view->statuss  = $this->retornaStatus();
            
            $this->view->filtros  = $this->view->parametros;
            
            if ($this->view->parametros->limpaTecoid == 'true'){
            	unset($_SESSION['tecoid']);
            	unset($_POST['tecoid']);
            	unset($this->view->parametros->tecoid);
            	unset($this->view->filtros->tecoid);
            }

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisarGeraArquivo' ) {
                
                //valida campos do formulário
                $this->validarCampos($this->view->parametros);

                $this->view->dados = $this->pesquisarGeraArquivo($this->view->parametros);

            }
            elseif ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisarEnvioArquivos' ) {
                
                //valida campos do formulário
                $this->validarCampos($this->view->parametros);

                $this->view->dados['envioArquivos'] = $this->pesquisarEnvioArquivos($this->view->parametros);
                
            }
            elseif ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisarTitulosProcessados' ) {
                
                //valida campos do formulário
                $this->validarCampos($this->view->parametros);

                $this->view->dados['titulosProcessados'] = $this->pesquisarTitulosProcessados($this->view->parametros);
                
            }
            elseif ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisarLogs' ) {

                $this->view->dados['logs'] = $this->pesquisarLogs($this->view->parametros);
                
            }
            elseif ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'gerarCSV' ) {

                //valida campos do formulário
                $this->validarCampos($this->view->parametros);

                $this->view->dados['titulosProcessados'] = $this->pesquisarTitulosProcessados($this->view->parametros);
                
                $this->gerarCSV();
                
            }
            elseif ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'chamarCron' ) {

                $this->atualizarArquivos();
            }

        } catch (ErrorException $e) {

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        //valida aba
        if ( !isset($this->view->parametros->aba) || $this->view->parametros->aba == '' ) {
            $this->view->parametros->aba = "gerar_arquivo";
        }
        
        $_SESSION['tecoid'] = $this->view->parametros->tecoid;

        //Incluir a view padrão
        require_once _MODULEDIR_ . "Financas/View/fin_contas_apagar/index.php";
    }

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *
     * @return stdClass Parametros tradados
     * @return stdClass
     */
    private function tratarParametros() {

       $retorno = new stdClass();

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {

                if(is_array($value)) {

                    //Tratamento de POST com Arrays
                    foreach ($value as $chave => $valor) {
                        $value[$chave] = trim($valor);
                    }
                    $retorno->$key = isset($_POST[$key]) ? $_POST[$key] : array();

                } else {
                    $retorno->$key = isset($_POST[$key]) ? trim($value) : '';
                }

            }
        }

        if (count($_GET) > 0) {  
            foreach ($_GET as $key => $value) {

                //Verifica se atributo ja existe e nao sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? trim($value) : '';
                }
            }
        }

        if (count($_FILES) > 0) {
           foreach ($_FILES as $key => $value) {

               //Verifica se atributo já existe e não sobrescreve.
               if (!isset($retorno->$key)) {
                    $retorno->$key = isset($_FILES[$key]) ? $value : '';
               }
           }
        }

        return $retorno;
    }

    /**
     * Popula e trata os parametros bidirecionais entre view e action
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senão iniciliza todos
        $this->view->parametros->tecoid     = isset($this->view->parametros->tecoid) && !empty($this->view->parametros->tecoid) ? trim($this->view->parametros->tecoid) : $_SESSION['tecoid']; 
        $this->view->parametros->tecrazao   = isset($this->view->parametros->tecrazao) && !empty($this->view->parametros->tecrazao) ? trim($this->view->parametros->tecrazao) : "";
        $this->view->parametros->aba        = isset($this->view->parametros->aba) && !empty($this->view->parametros->aba) ? trim($this->view->parametros->aba) : $_GET['aba'];

    	//Tratamentos prenchimento campos data período
		$dt_atual = date("d/m/Y");
		//Dois campos dt vazios
		if( 
		(!isset($this->view->parametros->periodo_inicial_busca) || empty($this->view->parametros->periodo_inicial_busca))
		&&
		(!isset($this->view->parametros->periodo_final_busca) || empty($this->view->parametros->periodo_final_busca))
		) {
			//Dt inicial e dt final recebem a data atual
			$this->view->parametros->periodo_inicial_busca = $dt_atual;
			$this->view->parametros->periodo_final_busca = $dt_atual;
        }
		//Campo dt inicial vazio e dt final preenchido
		if( 
		(!isset($this->view->parametros->periodo_inicial_busca) || empty($this->view->parametros->periodo_inicial_busca))
		&&
		(isset($this->view->parametros->periodo_final_busca) && !empty($this->view->parametros->periodo_final_busca))
		) {
			//Dt inicial recebe dt final 
			$this->view->parametros->periodo_inicial_busca = $this->view->parametros->periodo_final_busca;
		}
		//Campo dt inicial preenchido e dt final vazio 
		if( 
		(isset($this->view->parametros->periodo_inicial_busca) && !empty($this->view->parametros->periodo_inicial_busca))
		&&
		(!isset($this->view->parametros->periodo_final_busca) || empty($this->view->parametros->periodo_final_busca))
		) {
			//Dt final recebe dt inicial 
			$this->view->parametros->periodo_final_busca = $this->view->parametros->periodo_inicial_busca;
		}
        $_POST['periodo_inicial_busca'] = $this->view->parametros->periodo_inicial_busca;
        $_POST['periodo_final_busca']   = $this->view->parametros->periodo_final_busca;

    }

    /**
     * Responsavel por tratar e retornar o resultado da pesquisa
     * @param  stdClass $filtros [description]
     * @return [type]            [description]
     */
    private function pesquisarEnvioArquivos(stdClass $filtros){

        $resultadoPesquisa = $this->dao->pesquisarEnvioArquivos($filtros);
        
        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_NENHUM_REGISTRO ;
        }

        $this->view->filtros = $filtros;
        $this->view->status = TRUE;

        return $resultadoPesquisa;

    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa.
     * @param stdClass $filtros Filtros da pesquisa
     * @return array
     */
    private function pesquisarGeraArquivo(stdClass $filtros) {
     
        $filtros = $this->tratarParametros();

        $periodo_inicial_busca          = $this->date_to($filtros->periodo_inicial_busca, true);
        $periodo_final_busca            = $this->date_to($filtros->periodo_final_busca, true);
        $filtros->periodo_inicial_busca = $periodo_inicial_busca;
        $filtros->periodo_final_busca   = $periodo_final_busca;
		
		//Atribui parametros de busca
        $parametros = new stdClass;
		$parametros->codigoParametro     = 'CONTAS_A_PAGAR';
		$parametros->codigoItemParametro = 'STATUS_PERMITIDO_ENVIO_TITULO';
		
		//Busca códigos de status parametrizados para permitir o envio de títulos
		if ($codigosStatus = $this->daoCRN->buscaParametros($parametros)) {
			//Atribui códigos de status parametrizados 
			foreach ($codigosStatus as $value) {
				$statusParam = trim($value->pcsidescricao);
			}
  		
    		//Atribui parametros busca status
			$dadosStatus = new stdClass;
			$dadosStatus->codigoBanco =$filtros->banco;
    		$dadosStatus->tipo = 'Remessa';
    		$dadosStatus->codStatus = $statusParam; //Códigos de status parametrizados para permitir o envio de títulos
    		
    		//Busca os códigos de status parametrizados
    		$retornoStatus = $this->daoCRN->buscaCodigoStatus($dadosStatus);
            foreach ($retornoStatus as $chave => $valor) {
                $auxRetornoStatus[] = $valor->apgsoid;
            }
    		//Atribui os códigos dos status parametrizados
    		$filtros->arrayStatus = implode(',', $auxRetornoStatus);
			
		}
        
        $resultadoPesquisa['geraArquivo']  = $this->dao->retornaPesquisaGeraArquivo($filtros);
        $resultadoPesquisa['titulosPagos'] = $this->dao->resultadoTitulosPagos($filtros);
        $resultadoPesquisa['adiantamento'] = $this->dao->resultadoTitulosAdiantamentoFornecedor($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa['geraArquivo']) == 0 && count($resultadoPesquisa['titulosPagos']) == 0 && count($resultadoPesquisa['adiantamento']) == 0) {
            $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_NENHUM_REGISTRO ;
            return $resultadoPesquisa;            
        }
        
        //trata dados do resultado
        if (count($resultadoPesquisa['geraArquivo']) > 0) {
            $resultadoPesquisa['geraArquivo']  = $this->trataGerarArquivo($resultadoPesquisa['geraArquivo']);
        }
        if (count($resultadoPesquisa['titulosPagos']) > 0) {
            $resultadoPesquisa['titulosPagos'] = $this->tratatitulosPagos($resultadoPesquisa['titulosPagos']);
        }
        if (count($resultadoPesquisa['adiantamento']) > 0) {
            $resultadoPesquisa['adiantamento'] = $this->trataAdiantamentos($resultadoPesquisa['adiantamento']);
        }

        return $resultadoPesquisa;
    }

    /**
     * Responsavel por tratar e retornar o resultado da pesquisa
     * @param  stdClass $filtros [description]
     * @return [type]            [description]
     */
    private function pesquisarTitulosProcessados(stdClass $filtros){

        $resultadoPesquisa = $this->dao->pesquisarTitulosProcessados($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_NENHUM_REGISTRO ;
        }
        
        //
        $this->view->filtros->apresentar_valor = 1;
        
        //trata dados do resultado
        if (count($resultadoPesquisa) > 0) {
            $resultadoPesquisa = $this->trataGerarArquivo($resultadoPesquisa);
        }

        $this->view->filtros = $filtros;
        $this->view->status = TRUE;

        return $resultadoPesquisa;
    }

     /**
     * Pesquisa logs por banco selecionado
     * @return [type] [description]
     */
    private function pesquisarLogs($filtros){

        //direciona para o layout do banco
        switch ($filtros->banco) {
            case '341': //ID Itaú
                    $retornoLogs['comunicacao'] = $this->consultaLogsItau();
                    $retornoLogs['remessas']    = $this->getLogsRemessa();
                break;
            default:
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO); 
                break;
        }

         //Valida se houve resultado na pesquisa
        if (count($retornoLogs) == 0) {
            $this->view->mensagemAlerta = self::MENSAGEM_ALERTA_NENHUM_REGISTRO ;
        }

        $this->view->filtros = $filtros;

        return $retornoLogs;
    }

    /**
     * Responsavel por gerar CSV de titulos processados
     * @param  stdClass $filtros [description]
     * @return [type]            [description]
     */	
    private function gerarCSV() {
        //Diretório do Arquivo
        $caminho = $this->caminhoLocal;
        //Nome do arquivo
        $nomeArquivo = 'titulosProcessados'. date("Ymd").'.csv';
        //Flag para identificar se o arquivo foi gerado
        $arquivo = false;

        if (file_exists($caminho)) {

            // Instanciar CSV
            $csvWriter = new CsvWriter($caminho . $nomeArquivo, ';', '', true);

            // Adicionar título
            //$csvWriter->addLine('Títulos Processados');

            // Gerar cabeçalho
            $cabecalho = array(
                "Fornecedor",
                "Banco",
                "Nº Remessa",
                "Status",
                "Empresa",
                "Documento",
                "Vencimento",
                "Data Entrada",
                "Cod. Título",
                "Valor Bruto",
                "Valor Total",                
                "Forma de Pagamento",
                "Status do Retorno",
                "Tipo Contas a Pagar"
            );
            
            // Adicionar cabeçalho
            $csvWriter->addLine($cabecalho);

            //Total de registros
            $this->countRelatorio = count($this->view->dados['titulosProcessados']);
            
            if ($this->countRelatorio > 0) {
                foreach ($this->view->dados['titulosProcessados'] as $relatorio) {
                	
                	//Adicionar linha
                	$csvWriter->addLine(
                			array(
								$relatorio->fornecedor,
								$relatorio->bannome,
								$relatorio->apgno_remessa,
								$relatorio->apgsdescricao,
								$relatorio->tecrazao,
								$relatorio->doc,
								$relatorio->apgdt_vencimento,
								$relatorio->apgdt_entrada,
								$relatorio->apgoid,
								$relatorio->apgvl_apagar,                                
								$relatorio->valor,                                
								$relatorio->ocorrencia,
								$relatorio->codigo_erros,
								$relatorio->tipo_contas
                            )
                    );
                } //Foreach
            } //IF Count do Relatório
        } //IF File_exists        

        //Verifica se o arquivo foi gerado
        $arquivo = file_exists($caminho . $nomeArquivo);

        if ($arquivo === false) {
        	
            throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
            
        } elseif ($this->countRelatorio > 0) {

            //Mensagem do arquivo gerado
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ARQUIVO;
        }
		
        $this->view->csv = TRUE;
        $this->view->nomeArquivo = $nomeArquivo;

        return TRUE;
    }

    /**
     * Validar os campos obrigatórios do cadastro.
     *
     * @param stdClass $dados Dados a serem validados
     * @throws Exception
     * @return void
     */
    private function validarCampos(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();
        $camposDestaquesTamanho = array();

        
        //Verifica os campos obrigatórios
        if (!isset($dados->tecoid) || trim($dados->tecoid) == '') {
            $camposDestaques[] = array(
                'campo' => 'tecoid'
            );
        }

        if (!empty($camposDestaques)) {
            $this->view->camposDestaque = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        //Valida se uma data é maior que a outra
        $validacaoData = $this->validarDataMaior($dados->periodo_inicial_busca, $dados->periodo_final_busca);
        
        if ($validacaoData == -1) {
        	$camposDestaques[] = array(
        			'campo' => 'periodo_inicial_busca'
        	);
        	$camposDestaques[] = array(
        			'campo' => 'periodo_final_busca'
        	);
        	$this->view->camposDestaque = $camposDestaques;
        	throw new Exception(self::MENSAGEM_ALERTA_DATA_INICIAL_FINAL);
        }

    }


    //Retorna todas empresas para o select de pesquisa
    public function retornaEmpresas(){

        try {

            $retorno = $this->dao->retornaTodasEmpresas();

            return $retorno;
            
        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }
  	
    }
    
    //retorna todos bancos cadastrados
    public function retornaBanco(){

        try {

            $retorno = $this->dao->retornaBanco();
        
            return $retorno;
            
        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }
    }
    
    //retorna todos status cadastrados
    public function retornaStatus(){

        try {

            $retorno = $this->dao->retornaStatus();
        
            return $retorno;
            
        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();
        }
    }

    /**
     * Buscar fornecedores (autocomplete)
     * @return json
     */
    public function buscarFornecedor() {
        $resultado = array();
        $parametros = $this->tratarParametros();

        if (strlen($parametros->term) > 2) {
            $resultado = $this->dao->getNomeFornecedor($parametros->term);
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }


    /**
     * [enviarRemessa description]
     * @return [type] [description]
     */
    public function enviarRemessa(){

        try {
			$this->dao->begin();

            $this->view->parametros = $this->tratarParametros();

            if(count($this->view->parametros->ck) == 0 ){
                throw new Exception(self::MENSAGEM_ALERTA_TITULO);
            }

            //direciona para o layout do banco
            switch ($this->view->parametros->banco) {
                case '341': //ID Itaú
                        $this->gerarArquivoItau();
                    break;
                default:
                        throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO); 
                    break;
            }

            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ENVIO;
			$this->dao->commit();
            
        } catch (Exception $e) {
			$this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();   
        }

        $this->index();
    }

    /**
     * Captura informações do Codigo de Barras Padrão Titulos de Cobranca
     * @param  [type] $codigoBarras [description]
     * @return [type]               [description]
     */
    private function codigoBarrasTituloCobranca($codigoBarras){

        //341|9|6|1667|0000012345|1101234567880057123457000

        $dados = new stdClass;
        $dados->codBanco        = substr($codigoBarras, 0, 3);
        $dados->codMoeda        = substr($codigoBarras, 3, 1); 
        $dados->dvGeral         = substr($codigoBarras, 4, 1); 
        $dados->fatorVencimento = substr($codigoBarras, 5, 4); 
        $dados->valorTitulo     = substr($codigoBarras, 9, 10); 
        $dados->campoLivre      = substr($codigoBarras, 19); 

        return $dados;
    }

    /**
     * Captura informações da Linha Digitavel Padrão Titulos de Cobranca
     * @param  [type] $linhaDigitavel [description]
     * @return [type]                 [description]
     */
    private function linhaDigitavelTituloCobranca($linhaDigitavel){

        //341|9|11012|1|3456788005|8|7123457000|1|6|1667|0000012345
        
        $dados = new stdClass;
        $dados->codBanco        = substr($linhaDigitavel, 0, 3);
        $dados->codMoeda        = substr($linhaDigitavel, 3, 1); 
        $dados->campoLivre1     = substr($linhaDigitavel, 4, 5); 
        $dados->dvCampo1        = substr($linhaDigitavel, 9, 1); 
        $dados->campoLivre2     = substr($linhaDigitavel, 10, 10); 
        $dados->dvCampo2        = substr($linhaDigitavel, 20, 1); 
        $dados->campoLivre3     = substr($linhaDigitavel, 21, 10); 
        $dados->dvCampo3        = substr($linhaDigitavel, 31, 1); 
        $dados->dvGeral         = substr($linhaDigitavel, 32, 1); 
        $dados->fatorVencimento = substr($linhaDigitavel, 33, 4); 
        $dados->valorTitulo     = substr($linhaDigitavel, 37, 10); 

        return $dados;
    }

    /**
     * Captura informações do Codigo de Barras Padrão Concessionárias
     * @param  [type] $codigoBarras [description]
     * @return [type]               [description]
     */
    private function codigoBarrasConcessionarias($codigoBarras){

        //8|4|6|1|00000003627|0006|0002000102000000457986595

        $dados = new stdClass;
        $dados->identProduto    = substr($codigoBarras, 0, 1);
        $dados->identSegmento   = substr($codigoBarras, 1, 1); //1 = Prefeituras (IPTU); 2 = Saneamento; 3 = Energia Elétrica e Gás; 4 = Telecomunicações; 
        $dados->identValor      = substr($codigoBarras, 2, 1); //6 = Reais; 7 = Moeda Variável;
        $dados->dvGeral         = substr($codigoBarras, 3, 1); 
        $dados->valorDocumento  = substr($codigoBarras, 4, 11); 
        $dados->empresaOrgao    = substr($codigoBarras, 15, 4); 
        $dados->campoLivre      = substr($codigoBarras, 19); 

        return $dados;

    }

    /**
     * Captura informações da Linha Digitável Padrão Concessionárias
     * @param  [type] $linhaDigitavel [description]
     * @return [type]                 [description]
     */
    private function linhaDigitavelConcessionarias($linhaDigitavel){

        //8|4|6|1|0000000|5|3627|0006|000|1|20001020000|0|00457986595|9
        
        $dados = new stdClass;
        $dados->identProduto    = substr($linhaDigitavel, 0, 1);
        $dados->identSegmento   = substr($linhaDigitavel, 1, 1); //1 = Prefeituras (IPTU); 2 = Saneamento; 3 = Energia Elétrica e Gás; 4 = Telecomunicações; 
        $dados->identValor      = substr($linhaDigitavel, 2, 1); //6 = Reais; 7 = Moeda Variável;
        $dados->dvGeral         = substr($linhaDigitavel, 3, 1); 
        $dados->valorDocumento1 = substr($linhaDigitavel, 4, 7); 
        $dados->dvCampo1        = substr($linhaDigitavel, 11, 1); 
        $dados->valorDocumento2 = substr($linhaDigitavel, 12, 4); 
        $dados->empresaOrgao    = substr($linhaDigitavel, 16, 4); 
        $dados->campoLivre1     = substr($linhaDigitavel, 20, 3); 
        $dados->dvCampo2        = substr($linhaDigitavel, 23, 1); 
        $dados->campoLivre2     = substr($linhaDigitavel, 24, 11); 
        $dados->dvCampo3        = substr($linhaDigitavel, 35, 1);  
        $dados->campoLivre3     = substr($linhaDigitavel, 36, 11);
        $dados->dvCampo4        = substr($linhaDigitavel, 47, 1);

        return $dados;

    }


    /**
     * Gerar arquivo de títulos a pagar - Modelo Itaú
     * @param [object] $[dados]
     * @return [type] [description]
     */
    public function gerarArquivoItau(){

        $parametros = $this->view->parametros;

        if (count($parametros->ck) == 0) {
            throw new Exception(MENSAGEM_ALERTA_NENHUM_REGISTRO);
        }else{
            $titulos = implode(',', $parametros->ck);
            $titulos = $this->dao->dadosTitulo($titulos);
        }

        if (!$parametros->dadosEmpresa = $this->dao->buscaInformacoesEmpresa($parametros->tecoid, $parametros->banco)){
            throw new Exception ( "Erro ao buscar dados da empresa." );
        }
        $parametros->dadosEmpresa->teccnpj          = $this->somenteNumeros($parametros->dadosEmpresa->teccnpj);
        $parametros->dadosEmpresa->tecendereco      = explode(",", $parametros->dadosEmpresa->tecendereco     );
        $parametros->dadosEmpresa->abconta_corrente = explode("-", $parametros->dadosEmpresa->abconta_corrente);

        
        $this->arquivo->headerArquivo = $this->headerArquivo($parametros);

        $parametros->totalLotes            = 0;
        $parametros->totalRegistrosArquivo = 0;
        $parametros->totalOutrasEntidades  = array();
        $parametros->totalValorAcrescimos  = array();
        $parametros->totalValorArrecadado  = array();
        $parametros->titulosGerados        = array();
        $parametros->totalValorPagtosLote  = array();
        $parametros->totalRegistrosLote    = array();
        $parametros->numTitulo             = array();


        //Pesquisa Segmentos e Forma de Pagamentos do Título
        foreach ($titulos as $id => $dadosTitulo){

            $titulos[$id]->segmento = $this->segmentoTitulo($parametros, $dadosTitulo);
            //caso título não seja suportado
            if(!$dadosTitulo->segmento){
                unset($titulos[$id]);
                continue;
            }
            $titulos[$id]->formaPagamento = $this->defineFormaPagamentoItau($dadosTitulo, $parametros->dadosEmpresa);
            //caso título não tenha forma de pagamento
            if(!$dadosTitulo->formaPagamento){
                unset($titulos[$id]);
            }

            $arraySegmentos[$id] = $titulos[$id]->segmento;
        }

        //ordena titulos por Segmento
        if(count($arraySegmentos) > 1){
            array_multisort($arraySegmentos, SORT_ASC, SORT_STRING, $titulos);
        }

		foreach ($titulos as $dadosTitulo){

            $apgoid = $dadosTitulo->apgoid;

            $parametros->numTitulo[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] = $parametros->numTitulo[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] + 1;
            $dadosTitulo->numRegistro = $parametros->numTitulo[$dadosTitulo->segmento][$dadosTitulo->formaPagamento];
            if ($dadosTitulo->apgforma_recebimento == "31" && $dadosTitulo->apgtipo_docto == "05") {
                $parametros->totalValorPagtosLote[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] += substr($dadosTitulo->valor_documento, 0 , -2);
            
            }elseif ($dadosTitulo->apgforma_recebimento == "31" && 
                       ($dadosTitulo->apgtipo_docto == "06" || 
                        $dadosTitulo->apgtipo_docto == "12" || 
                        $dadosTitulo->apgtipo_docto == "13" || 
                        $dadosTitulo->apgtipo_docto == "09" || 
                        $dadosTitulo->apgtipo_docto == "07" ) ) { 
                $parametros->totalValorPagtosLote[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] += substr($dadosTitulo->valor_titulo_equal_boleto, 0 , -1);
            
            }else{
                $parametros->totalValorPagtosLote[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] += substr($dadosTitulo->valor_documento, 0 , -1);
            }

            if( !isset($this->arquivo->headerLoteArquivo[$dadosTitulo->segmento][$dadosTitulo->formaPagamento]) ){
                
                $parametros->totalLotes++;
                $parametros->codLote[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] = $parametros->totalLotes;

                $this->arquivo->headerLoteArquivo[$dadosTitulo->segmento][$dadosTitulo->formaPagamento]  = array();
                $this->arquivo->trailerLoteArquivo[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] = array();
            
            }
            //gera linha dos titulos por segmento
            $this->geraLinha($parametros, $dadosTitulo);

            //soma para trailer do lote Segmento N
            if($dadosTitulo->segmento == 'N'){
                $parametros->totalOutrasEntidades[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] = $parametros->totalOutrasEntidades[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] + $dadosTitulo->apgvalor_entidades; 
                $parametros->totalValorAcrescimos[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] = $parametros->totalValorAcrescimos[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] + $dadosTitulo->apgvl_juros + $dadosTitulo->apgvl_multa; // + $dadosTitulo->apgvalor_receita_bruta;// + $dadosTitulo->apgpercentual_receita_bruta;
                $parametros->totalValorArrecadado[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] = $parametros->totalValorArrecadado[$dadosTitulo->segmento][$dadosTitulo->formaPagamento] + substr($dadosTitulo->valor_documento, 0 , -1);
            }

            //adiona no array da titulos gerados
            array_push($parametros->titulosGerados, $apgoid);
        }

        foreach ($parametros->codLote as $segmento => $formaPagamento) {
            foreach ($formaPagamento as $idFormaPagamento => $idLote) {

                //soma a quantidade de registros de um lote contando com header e trailer lote
                $parametros->totalRegistrosLote[$segmento][$idFormaPagamento] = count($this->arquivo->titulos[$segmento][$idFormaPagamento]) + 2;
                //soma a quantidade de registros do arquivo contando com header e trailer do arquivo
                $parametros->totalRegistrosArquivo += $parametros->totalRegistrosLote[$segmento][$idFormaPagamento];

                $this->arquivo->headerLoteArquivo[$segmento][$idFormaPagamento]  = $this->headerLoteArquivo($parametros, $segmento, $idFormaPagamento);
                $this->arquivo->trailerLoteArquivo[$segmento][$idFormaPagamento] = $this->trailerLoteArquivo($parametros, $segmento, $idFormaPagamento);
            }
        }

        //soma com header e trailer do arquivo
        $parametros->totalRegistrosArquivo += 2;

        $this->arquivo->trailerArquivo = $this->trailerArquivo($parametros);

        /*echo '<pre>';
        print_r($this->arquivo);
        echo '</pre>';
        //$this->criaArquivoItau();
        exit;*/

		if (! $sis_seq_sispag = $this->dao->retornoTabelaSistema()){
    		throw new Exception ( "Erro ao gerar número sequencial SISPAG." );
		}

		if (! $retornoUpdateSistema = $this->dao->UpdateTabelaSistema()){
    		throw new Exception ( "Erro ao atualizar número sequencial SISPAG." );
		}

		if (count($parametros->titulosGerados) == 0 ){
    		throw new Exception ( "Nenhum titulo gerado na remessa." );
		}

		$listaTitulos = implode(',', $parametros->titulosGerados);	
		if (! $retornoTabApagar = $this->dao->UpdateTabelaApagar($listaTitulos,$sis_seq_sispag)){
    		throw new Exception ( "Erro ao executar atualização de registros." );
		}

        $dadosStatus = new stdClass;
        //Atribui parametros busca status
        $dadosStatus->codigoBanco = $parametros->banco;
        $dadosStatus->tipo        = 'Retorno';
        $dadosStatus->codStatus   = 21; //Aguardando retorno Itaú
        
        //Busca o código do status a pagar
        if (! $retornoStatus = $this->daoCRN->buscaCodigoStatus($dadosStatus)){
            throw new Exception ( "Erro ao buscar código do status a pagar." );
        }
        //Atribui o código do status a pagar
        $codigoStatus = $retornoStatus[0]->apgsoid;
				
		//Atribui dados do título para atualização do status
		$dadosStatus->apgoid          = $listaTitulos;
        $dadosStatus->apgsoid         = $codigoStatus;
		$dadosStatus->baixaAutomatica = 'f';
		$dadosStatus->apgnosso_numero = 'null';
		
		//Atualiza o status do título a pagar
		if (! $this->daoCRN->atualizaStatusTitulo($dadosStatus)){
			throw new Exception ( "Erro ao atualizar o status do título a pagar." );
		}

        //chama funcao para criar arquivo e enviar ao servidor
        $this->criaArquivoItau();
		
		//Atribui dados controle arquivo
		$dadosControleArquivo                      = new stdClass;
		$dadosControleArquivo->apcetecoid          = $parametros->tecoid;
		$dadosControleArquivo->apcebancoid         = $parametros->banco;
		$dadosControleArquivo->apceapgno_remessa   = $sis_seq_sispag;
		$dadosControleArquivo->apcenome_arquivo    = $this->arquivoRemessa;
		$dadosControleArquivo->apceusuoid_cadastro = $this->usuarioLogado;		
		// Insere registro controle arquivo
		if (! $this->dao->insereRegistroControleArquivo($dadosControleArquivo)){
			throw new Exception ( "Erro ao inserir registro de controle de arquivo" );
		}

	}

    /**
     * Cria o arquivo e envia ao servidor
     * @return [type] [description]
     */
    private function criaArquivoItau() {
        
        $arquivo = implode('', $this->arquivo->headerArquivo);
        foreach ($this->arquivo->headerLoteArquivo as $segmento => $headerLoteArquivo) {
            foreach ($headerLoteArquivo as $formaPagamento => $headerLote) {
                
                $arquivo .= "\r\n";
                $arquivo .= implode('', $headerLote);
                if(isset($this->arquivo->titulos[$segmento][$formaPagamento])){
                    foreach ($this->arquivo->titulos[$segmento][$formaPagamento] as $idTitulo => $titulo) {
                        $arquivo .= "\r\n";
                        $arquivo .= implode('', $titulo);
                    }
                }
                $arquivo .= "\r\n";
                $arquivo .= implode('', $this->arquivo->trailerLoteArquivo[$segmento][$formaPagamento]);                
                
            }
        }
        $arquivo .= "\r\n";
        $arquivo .= implode('', $this->arquivo->trailerArquivo);

        //Verifica o ambiente
        if ($this->ambiente == "PRODUCAO"){
            $amb = 'I';
        }else{
            $amb = 'T';
        }
        $ppp = '008';
        $hoje = date('mdHis');
        $extensao = '.REM';
            
        //Atribui nome do arquivo (TpppMMDDhhmmss.REM)
        $nome_arquivo = $amb.$ppp.$hoje.$extensao;
        $this->arquivoRemessa = $nome_arquivo;

        /* gerando o arquivo */
        if(!is_dir($this->caminhoLocal)){
            mkdir($this->caminhoLocal);
        }

        $fp = fopen ($this->caminhoLocal."/".$this->arquivoRemessa, "w+");
        fwrite($fp, $arquivo);
        fclose($fp);

        //Atribui parametros FTP
        $parametrosFTP = new stdClass;

        $parametrosFTP->nomeArquivo  = $this->arquivoRemessa;
        $parametrosFTP->caminhoLocal = $this->caminhoLocal;

        //Atribui parametros de busca
        $parametros = new stdClass;
        $parametros->codigoParametro = 'CONTAS_A_PAGAR';
        
        //Busca caminho da pasta retorno/ remessa Itaú
        $parametros->codigoItemParametro = 'PASTA_ENVIO_REMESSA_ITAU';
        if ($caminho = $this->daoCRN->buscaParametros($parametros)) { 
            $parametrosFTP->destino = trim($caminho[0]->pcsidescricao);
        }

        //Busca servidor da pasta retorno/ remessa Itaú
        $parametros->codigoItemParametro = 'SERVIDOR_STCP_'.$this->ambiente;
        if ($servidor = $this->daoCRN->buscaParametros($parametros)) { 
            $parametrosFTP->servidor = trim($servidor[0]->pcsidescricao);
        }

        //Busca usuario FTP
        $parametros->codigoItemParametro = 'USUARIO_STCP_'.$this->ambiente;
        if ($usuario = $this->daoCRN->buscaParametros($parametros)) { 
            $parametrosFTP->usuario = trim($usuario[0]->pcsidescricao);
        }

        //Busca senha FTP
        $parametros->codigoItemParametro = 'PASSWORD_STCP_'.$this->ambiente;
        if ($senha = $this->daoCRN->buscaParametros($parametros)) { 
            $parametrosFTP->senha = trim($senha[0]->pcsidescricao);
        }

        if($this->enviarArquivoFTP($parametrosFTP)){
            //remove arquivo local pois foi enviado ao servidor do STCP
            //unlink($this->caminhoLocal.$this->arquivoRemessa);
        }
    
        return true;
    }

   
    public function autorizarTitulos(){
    	
       try {

            $this->dao->begin();

            $this->view->parametros = $this->tratarParametros();

            if(count($this->view->parametros->ck) == 0 ){
                throw new Exception(self::MENSAGEM_ALERTA_TITULO);
            }

            $retorno = $this->dao->autorizarTitulos($this->view->parametros->ck, $this->usuarioLogado, $this->view->parametros->banco);
            //
            $this->dao->rollback();
            //
            if($retorno){
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_AUTORIZAR;
                $this->dao->commit();
            }else{
                throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);   
            }
            
        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();   
        }

        $_POST['acao'] = 'pesquisarGeraArquivo';
    	$this->view->parametros->acao = 'pesquisarGeraArquivo';
        $this->index();
    }

 
	public function liberarReenvio(){

	   try {
	
	        $this->dao->begin();
	
	        $this->view->parametros = $this->tratarParametros();
	
	        if(count($this->view->parametros->ck) == 0 ){
	            throw new Exception(self::MENSAGEM_ALERTA_TITULO);
	        }
	
	        $retorno = $this->dao->liberarReenvio($this->view->parametros->ck, $this->usuarioLogado);
	
	        if($retorno){
	            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_LIBERAR_REENVIO;
	            $this->dao->commit();
	        }else{
	            throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);   
	        }
	        
	    } catch (Exception $e) {
	        $this->dao->rollback();
	        $this->view->mensagemErro = $e->getMessage();   
	    }
	
	    $this->index();
	}

 
	private function gerarFatorVencimento($novoFator)	{
	
	    $fator = 1000; //corresponde o dia 03/07/2000 (soma 1 ao fator por dia decorrido até a data de pagamento)
	    $dataFator = "";
	    $date = "2000-07-03";
	    $novoFator = $novoFator - $fator;
	
	    /** Para somar +x dias faça: */
	    $dataFator = date("d/m/Y", strtotime("+$novoFator day", strtotime($date)));
	    $dataFator = str_replace("/", "", $dataFator);
	
	    return $dataFator;
	}


	private function modulo_10($num) {

	    $numtotal10 = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo (falor 10)
            $temp = $numeros[$i] * $fator; 
            $temp0=0;
	        foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $k => $v) {
	            $temp0 += $v;
	        }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }
        
        // várias linhas removidas, vide função original
        // Calculo do modulo 10
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
	
        if ($resto == 0) {
            $digito = 0;
        }
        
        return $digito;
	
	}

	private function modulo_11($num, $base = 9, $r = 0)	{

	    $soma = 0;
	    $fator = 2;
	
	    /* Separacao dos numeros */
	    for ($i = strlen($num); $i > 0; $i--) {
	        // pega cada numero isoladamente
	        $numeros[$i] = substr($num, $i - 1, 1);
	        // Efetua multiplicacao do numero pelo falor
	        $parcial[$i] = $numeros[$i] * $fator;
	        // Soma dos digitos
	        $soma += $parcial[$i];
	        if ($fator == $base) {
	            // restaura fator de multiplicacao para 2
	            $fator = 1;
	        }
	        $fator++;
	    }
	
	    /* Calculo do modulo 11 */
	    if ($r == 0) {
	        $soma *= 10;
	        $digito = $soma % 11;
	        if ($digito == 10) {
	            $digito = 0;
	        }
	        return $digito;
	    } elseif ($r == 1) {
	        $resto = $soma % 11;
	        return $resto;
	    }
	}


	//função para formatar a data
	private function date_to($data, $to_american = false) {

	    if ($to_american) {
	        $data = str_replace("/", "-", $data);
	        $dia = substr($data, 0, strpos($data, "-"));
	        $data = substr($data, strpos($data, "-") + 1);
	        $mes = substr($data, 0, strpos($data, "-"));
	        $data = substr($data, strpos($data, "-") + 1);
	        if (strpos($data, " ")) $ano = substr($data, 0, strpos($data, " "));
	        else $ano = $data;
	
	        if (!(@checkdate($mes, $dia, $ano))) $data = false;
	        else $data = $ano . "-" . $mes . "-" . $dia;
	    }
	    else {
	        $data = str_replace("/", "-", $data);
	        $ano = substr($data, 0, strpos($data, "-"));
	        $data = substr($data, strpos($data, "-") + 1);
	        $mes = substr($data, 0, strpos($data, "-"));
	        $data = substr($data, strpos($data, "-") + 1);
	        if (strpos($data, " ")) $dia = substr($data, 0, strpos($data, " "));
	        else $dia = $data;
	
	        if (!(@checkdate($mes, $dia, $ano))) $data = false;
	        else $data = $dia . "/" . $mes . "/" . $ano;
	    }
	    return $data;
	}

	/**
	 * Complementa os dados a serem exibidos no resultado da pesquisa
	 * @param  [type] $resultado [description]
	 * @return [type]            [description]
	 */
	private function trataGerarArquivo($resultado){
	
	    $total = new stdClass();
	    $total->valor = 0;
	    $total->tapgvl_desconto = 0;
	    $total->tapgvl_juros = 0;
	    $total->tapgvl_multa = 0;
	    $total->tapgvl_ir = 0;
	    $total->tapgvl_pis = 0;
	    $total->tapgvl_iss = 0;
	    $total->tapgvl_inss = 0;
	    $total->tapgvl_cofins = 0;
	    $total->tapgvl_csll = 0;
	    $total->tvalor_pagamento = 0;
	    $total->tapgvl_tarifa_bancaria = 0;
        $total->tapapgcsrf = 0;

	    $titulo_atual = $resultado[0]->apgoid;
	    $conta_atual = 0;

        $qtdContas = array();

	    foreach ($resultado as $linha => $dados) {

            $qtdParcelas = $dados->entno_parcela;

            //verifica a quantidade de contas
            $qtdContas[$dados->apgoid] = $qtdContas[$dados->apgoid] + 1;

            //calcula contas
            if($linha > 0 && $qtdContas[$dados->apgoid] > 1){
                $conta_atual++;
            }else{
                $conta_atual = 1;
            }
	        
	        //col1 - checkbox
			/* Apresentar checkbox quando:	
	        	- Ocorrência for 'Crédito em C/C'('1') OU;
				- Ocorrência for 'Boleto'('31','30') E Tipo Doc for 'Concessionária'('11') OU 'Outros'('05').
	         */
			if ($dados->apgforma_recebimento == '1' || 
                ( 
                    ($dados->apgforma_recebimento == '31' || $dados->apgforma_recebimento == '30') && 
                    (
                        $dados->apgtipo_docto == '05' ||
                        $dados->apgtipo_docto == '06' || 
                        $dados->apgtipo_docto == '07' || 
                        $dados->apgtipo_docto == '09' || 
                        $dados->apgtipo_docto == '10' || 
                        $dados->apgtipo_docto == '11' || 
                        $dados->apgtipo_docto == '12' || 
                        $dados->apgtipo_docto == '13'
                    )
                ) 
            ){
	        	$resultado[$linha]->checkbox = true;
	        	if($dados->apgprevisao != 0){
	        		$resultado[$linha]->checkbox = false;
	        	}
			}else{
	            $resultado[$linha]->checkbox = false;
	        }
	        //col1 - checkbox - Se o titulo estiver aguardando retorno banco
	        $dadosStatus = new stdClass;
	        //Atribui parametros busca status
	        $dadosStatus->codigoBanco = $this->view->parametros->banco;
	        $dadosStatus->tipo        = 'Retorno';
	        $dadosStatus->codStatus   = 21; //Aguardando retorno $banco
	
	        //Busca o código do status a pagar
	        if (! $retornoStatus = $this->daoCRN->buscaCodigoStatus($dadosStatus)){
	            throw new Exception ( "Erro ao buscar código do status a pagar." );
	        }
	        //Atribui o código do status a pagar
	        $codigoStatus = $retornoStatus[0]->apgsoid;
	        if($dados->apgapgsoid == $codigoStatus ){
	            $resultado[$linha]->checkbox = false;
	        }
	
	        //col ocorrencia
	        $resultado[$linha]->ocorrencia = false;
	        if($dados->apgforma_recebimento == ""){
	            $resultado[$linha]->ocorrencia = "*Não há Forma de Pagamento";
	        }else{
	            if( in_array($dados->fortipo, array("J","A","M","R","T","P","E","H","C","L")) && !verifica_cnpj($this->ajustaString($dados->fordocto,14,"0")) ){
	                $resultado[$linha]->ocorrencia = "*Pessoa Jurídica e CNPJ Inválido";
	            }elseif( in_array($dados->fortipo, array("F","N","U")) && !verifica_cpf($this->ajustaString($dados->fordocto,11,"0")) ) {
	                $resultado[$linha]->ocorrencia = "*Pessoa Fisica e CPF Inválido";
	            }elseif($dados->apgprevisao == 1) {
	                $resultado[$linha]->ocorrencia = "*Título em previsão";
	            }else{
	                if(!$formaRecebimento = $this->formaRecebimento($dados->apgforma_recebimento)){
	                    $resultado[$linha]->ocorrencia = "Forma de recebimento inválida";
	                    $resultado[$linha]->checkbox = false;
	                }else{
	                    $resultado[$linha]->ocorrencia = $formaRecebimento;
	                }
	            }
	        }
	
	        //(BOLETOS) col checkbox e ocorrencias
	        if( 
                (
                    ($dados->apgforma_recebimento == '31' || $dados->apgforma_recebimento == '30') &&
                    (
                        $dados->apgtipo_docto == '05' || 
                        $dados->apgtipo_docto == '09' || 
                        $dados->apgtipo_docto == '10' || 
                        $dados->apgtipo_docto == '11'
                    )

                ) && empty($dados->apgcodigo_barras) 
            ){
	            $resultado[$linha]->checkbox = false;
	            $resultado[$linha]->ocorrencia = "Boleto sem codigo de barras.";
	        }
	
	        //col Fornecedor
	        if( $resultado[$linha]->ocorrencia || $dados->apgforma_recebimento == 0 ){
	            $resultado[$linha]->cor_fornecedor = "#FF0000";
	        }else{
	            $resultado[$linha]->cor_fornecedor = false;
	        }
	
            $imposto = $dados->apgvl_ir + 
                              $dados->apgvl_iss + 
                              $dados->apgvl_inss + 
                              $dados->apgvl_csll + 
                              $dados->apgvl_pis + 
                              $dados->apgvl_cofins +
                              $dados->apgcsrf;

	        //caso a apresentacao do valor seja "Documento"
	        if ($this->view->filtros->apresentar_valor == 1) {
	            $resultado[$linha]->apgvl_apagar = number_format($dados->apgvl_apagar   , 2, ",", ".");
	            $resultado[$linha]->desconto     = number_format($dados->apgvl_desconto , 2, ",", ".");
	            $resultado[$linha]->juros        = number_format($dados->apgvl_juros    , 2, ",", ".");
	            $resultado[$linha]->multa        = number_format($dados->apgvl_multa    , 2, ",", ".");
	            $resultado[$linha]->imposto      = number_format($imposto               , 2, ",", ".");
	            $resultado[$linha]->tarifa       = number_format($dados->apgvl_tarifa_bancaria , 2, ",", ".");
	            $resultado[$linha]->valor        = number_format($dados->valor_pagamento       , 2, ",", ".");                
	
	            //não exibe parcelas neste caso
	            if($conta_atual > 1){
	                unset($resultado[$linha]);
	                continue;
	            }
	
	        //caso seja por "Conta Contábil"
	        }elseif ($this->view->filtros->apresentar_valor == 2) {

                if ($conta_atual == 1) {
                   $valor_parcela = 0;
                   $desconto_total = 0;
                   $juros_total = 0;
                   $multa_total = 0;
                   $imposto_total = 0; 
                   $tarifa_total = 0;
                }
	
	            //Valor Título
	            if ($qtdParcelas <= 1) {
	                $proporcao = $dados->valor_item / $dados->apgvl_apagar;
	                $valor_parcela = $dados->valor_item;
	                $resultado[$linha]->apgvl_apagar = number_format($dados->valor_item, 2, ",", ".");
	            } else {
	                $valor_parcela = $this->calcula_dividindo($dados->qtd_contas, $conta_atual, $dados->apgvl_apagar);
	                $resultado[$linha]->apgvl_apagar = number_format($valor_parcela, 2, ",", ".");
	            }
	
	            //Desconto
	            if ($qtdParcelas <= 1) {
	                $desconto_parcela = $this->calcula_proporcional($dados->qtd_contas, $conta_atual, $dados->apgvl_desconto, $proporcao, $desconto_total);
	                $desconto_total += $desconto_parcela;
	
	                $resultado[$linha]->desconto = number_format($desconto_parcela, 2, ",", ".");
	            }else{
	                $desconto_parcela = $this->calcula_dividindo($dados->qtd_contas, $conta_atual, $dados->apgvl_desconto);
	                $resultado[$linha]->desconto = number_format($desconto_parcela, 2, ",", ".");
	            }
	
	            //Juros
	            if ($qtdParcelas <= 1) {
	                $juros_parcela = $this->calcula_proporcional($dados->qtd_contas, $conta_atual, $dados->apgvl_juros, $proporcao, $juros_total);
	                $juros_total += $juros_parcela;
	
	                $resultado[$linha]->juros = number_format($juros_parcela, 2, ",", ".");
	            }else{
	                $juros_parcela = $this->calcula_dividindo($dados->qtd_contas, $conta_atual, $dados->apgvl_juros);
	                $resultado[$linha]->juros = number_format($juros_parcela, 2, ",", ".");
	            }
	
	            //Multa
                if ($qtdParcelas <= 1) {
	                $multa_parcela = $this->calcula_proporcional($dados->qtd_contas, $conta_atual, $dados->apgvl_multa, $proporcao, $multa_total);
	                $multa_total += $multa_parcela;
	
	                $resultado[$linha]->multa = number_format($multa_parcela, 2, ",", ".");
	            }else{
	                $multa_parcela = $this->calcula_dividindo($dados->qtd_contas, $conta_atual, $dados->apgvl_multa);
	                $resultado[$linha]->multa = number_format($multa_parcela, 2, ",", ".");
	            }
	
	            //Imposto
                if ($qtdParcelas <= 1) {
                    $imposto_parcela = $this->calcula_proporcional($dados->qtd_contas, $conta_atual, $imposto, $proporcao, $imposto_total);
                    $imposto_total += $imposto_parcela;
    
                    $resultado[$linha]->imposto = number_format($imposto_parcela, 2, ",", ".");
                }else{
                    $imposto_parcela = $this->calcula_dividindo($dados->qtd_contas, $conta_atual, $imposto);
                    $resultado[$linha]->imposto = number_format($imposto_parcela, 2, ",", ".");
                }                
	
	            // Tarifa Bancária
	            if ($qtdParcelas <= 1) {
	                $tarifa_parcela = $this->calcula_proporcional($dados->qtd_contas, $conta_atual, $dados->apgvl_tarifa_bancaria, $proporcao, $tarifa_total);
	                $tarifa_total += $tarifa_parcela;
	                $resultado[$linha]->tarifa = number_format($tarifa_parcela, 2, ",", ".");
	            }else{
	                $tarifa_parcela = $this->calcula_dividindo($dados->qtd_contas, $conta_atual, $dados->apgvl_tarifa_bancaria);
	                $resultado[$linha]->tarifa = number_format($tarifa_parcela, 2, ",", ".");
	            }
	
	            // Valor
	            $valor = $valor_parcela - $desconto_parcela + $juros_parcela + $multa_parcela - $imposto_parcela + $tarifa_parcela - $csrf_parcela;
	            $resultado[$linha]->valor = number_format($valor, 2, ",", ".");


                if($resultado[$linha]->qtd_contas > 1){
                    $resultado[$linha]->checkbox = false;
                    $resultado[$linha]->valor_titulo_equal_boleto = '-';
                }
	
	        }
	    
	        //Tipo Documento
	        $resultado[$linha]->tipo_documento = $this->tipoDocumento($dados->apgtipo_docto);
	
	        //Conta Contábil
	        $resultado[$linha]->conta_contabil = "";
	        if ($this->view->filtros->apresentar_valor == 2 || ($this->view->filtros->apresentar_valor == 1 && $resultado[$linha]->qtd_contas == 1)) {
	            if ($dados->plcconta != ""){
	                $resultado[$linha]->conta_contabil = substr($dados->plcconta,0,13) . " - " . $dados->descricao;
	            }
	        }
	
	        //verifica status (coluna img)
	        if ($dados->excluido == 0) {
	            if ($dados->apgautorizado == "t") {
	                $img = "ap01.jpg"; //autorizado
	            }else{
	                $img = "ap03.jpg"; //pendente
	            }
	        }else{
	            $img = "ap04.jpg"; //cancelado
	        }
	        $resultado[$linha]->status_imagem = $img;
	        
	        //Cód. Erros
	        $resultado[$linha]->codigo_erros = "";
	        $codigo_erros = "";
	        $objetoCodigoErros = $this->dao->buscaOcorrencias($dados->apgoid);
	        if (count($objetoCodigoErros) > 0){ 
	        	$check = '';
				foreach ($objetoCodigoErros as $value) {
					$codigo_erros.= ", ".$value->codigo_erro;
					if ($check != 'f'){
						$check = (trim($value->apgocodigo) == '' || trim($value->apgocodigo) == '00' || trim($value->apgocodigo) == 'BD') ? 'f' : 't';
					}
                    if(trim($value->apgocodigo) == 'RJ'){
                        $resultado[$linha]->apgsdescricao = 'Rejeitado';
                    }

				}
				$resultado[$linha]->codigo_erros = substr($codigo_erros, 2);
				$resultado[$linha]->check 		 = $check;
	        }

            //caso não tenha retorno do banco após 24h é possivel liberar para reenvio
            if($resultado[$linha]->apgno_remessa != '' && $resultado[$linha]->apgscodigo == 21){
                $dataAtual = new DateTime();
                $dataEnvio = new DateTime($resultado[$linha]->apcedt_envio);

                $diff = $dataAtual->diff($dataEnvio); 
                if($diff->d >= 1){
                    $resultado[$linha]->check = 't';
                    $resultado[$linha]->apgsdescricao = '<font color=bf3a2e>Aguardando Retorno Itaú</font>';
                }
            }	        
	
	        //soma tabela total
	        if ($dados->excluido == 0) {
	            $total->valor                      += str_replace(array(".",","), array("","."), $resultado[$linha]->apgvl_apagar);
                if($conta_atual == 1){
                    $total->tapgvl_desconto        += $resultado[$linha]->apgvl_desconto;
                    $total->tapgvl_juros           += $resultado[$linha]->apgvl_juros;
                    $total->tapgvl_multa           += $resultado[$linha]->apgvl_multa;
                    $total->tapgvl_ir              += $resultado[$linha]->apgvl_ir;
                    $total->tapgvl_pis             += $resultado[$linha]->apgvl_pis;
                    $total->tapgvl_iss             += $resultado[$linha]->apgvl_iss;
                    $total->tapgvl_inss            += $resultado[$linha]->apgvl_inss;
                    $total->tapgvl_cofins          += $resultado[$linha]->apgvl_cofins;
                    $total->tapgvl_csll            += $resultado[$linha]->apgvl_csll;
                    $total->tapgvl_tarifa_bancaria += $resultado[$linha]->apgvl_tarifa_bancaria;
                    $total->tapapgcsrf             += $resultado[$linha]->apgcsrf;
                    $total->tvalor_pagamento       += $resultado[$linha]->valor_pagamento;
                }
	        }
	    }
        	
	    $this->view->total['geraArquivo'] = $total;
	
	    return $resultado;
	}

    /**
     * [trataTitulosPagos description]
     * @param  [type] $resultado [description]
     * @return [type]            [description]
     */
    private function trataTitulosPagos($resultado){

        $total = new stdClass();
        $total->valor = 0;
        $total->tapgvl_desconto = 0;
        $total->tapgvl_juros = 0;
        $total->tapgvl_multa = 0;
        $total->tapgvl_ir = 0;
        $total->tapgvl_pis = 0;
        $total->tapgvl_iss = 0;
        $total->tapgvl_inss = 0;
        $total->tapgvl_cofins = 0;
        $total->tapgvl_csll = 0;
        $total->tvalor_pagamento = 0;
        $total->tapgvl_tarifa_bancaria = 0;
        $total->tapapgcsrf = 0;

        foreach ($resultado as $linha => $dados) {

            //soma tabela total
            if ($dados->excluido == 0) {
                $total->valor                  += $resultado[$linha]->apgvl_apagar;
                $total->tapgvl_desconto        += $resultado[$linha]->apgvl_desconto;
                $total->tapgvl_juros           += $resultado[$linha]->apgvl_juros;
                $total->tapgvl_multa           += $resultado[$linha]->apgvl_multa;
                $total->tapgvl_ir              += $resultado[$linha]->apgvl_ir;
                $total->tapgvl_pis             += $resultado[$linha]->apgvl_pis;
                $total->tapgvl_iss             += $resultado[$linha]->apgvl_iss;
                $total->tapgvl_inss            += $resultado[$linha]->apgvl_inss;
                $total->tapgvl_cofins          += $resultado[$linha]->apgvl_cofins;
                $total->tapgvl_csll            += $resultado[$linha]->apgvl_csll;
                $total->tvalor_pagamento       += $resultado[$linha]->valor_pagamento;
                $total->tapgvl_tarifa_bancaria += $resultado[$linha]->apgvl_tarifa_bancaria;
                $total->tapapgcsrf             += $resultado[$linha]->apgcsrf;
            }

            //verifica status (coluna img)
            if ($dados->excluido == 0) {
                if ($dados->apgautorizado == "t") {
                    $img = "ap01.jpg"; //autorizado
                }else{
                    $img = "ap03.jpg"; //pendente
                }
            }else{
                $img = "ap04.jpg"; //cancelado
            }
            $resultado[$linha]->status_imagem = $img;

            //col ocorrencia
            $resultado[$linha]->ocorrencia = false;
            if($dados->apgforma_recebimento == ""){
                $resultado[$linha]->ocorrencia = "*Não há Forma de Pagamento";
            }else{
                if( in_array($dados->fortipo, array("J","A","M","R","T","P","E","H","C","L")) && !verifica_cnpj($this->ajustaString($dados->fordocto,14,"0")) ){
                    $resultado[$linha]->ocorrencia = "*Pessoa Jurídica e CNPJ Inválido";
                }elseif( in_array($dados->fortipo, array("F","N","U")) && !verifica_cpf($this->ajustaString($dados->fordocto,11,"0")) ) {
                    $resultado[$linha]->ocorrencia = "*Pessoa Fisica e CPF Inválido";
                }elseif($dados->apgprevisao == 1) {
                    $resultado[$linha]->ocorrencia = "*Título em previsão";
                }else{
                    if(!$formaRecebimento = $this->formaRecebimento($dados->apgforma_recebimento)){
                        $resultado[$linha]->ocorrencia = "Forma de recebimento inválida";
                        $resultado[$linha]->checkbox = false;
                    }else{
                        $resultado[$linha]->ocorrencia = $formaRecebimento;
                    }
                }
            }

            //Tipo Documento
            $resultado[$linha]->tipo_documento = $this->tipoDocumento($dados->apgtipo_docto);
        }

        $this->view->total['titulosPagos'] = $total;


        return $resultado;
    }


    /**
     * [trataAdiantamentos description]
     * @param  [type] $resultado [description]
     * @return [type]            [description]
     */
    private function trataAdiantamentos($resultado){

        $total = new stdClass();
        $total->valor = 0;
        $total->tapgvl_desconto = 0;
        $total->tapgvl_juros = 0;
        $total->tapgvl_multa = 0;
        $total->tapgvl_ir = 0;
        $total->tapgvl_pis = 0;
        $total->tapgvl_iss = 0;
        $total->tapgvl_inss = 0;
        $total->tapgvl_cofins = 0;
        $total->tapgvl_csll = 0;
        $total->tvalor_pagamento = 0;
        $total->tapgvl_tarifa_bancaria = 0;
        $total->tapapgcsrf = 0;


        foreach ($resultado as $linha => $dados) {

            //soma tabela total
            if ($dados->excluido == 0) {
                $total->valor                  += str_replace(array(".",","), array("","."), $resultado[$linha]->apgvl_apagar);
                $total->tapgvl_desconto        += $resultado[$linha]->apgvl_desconto;
                $total->tapgvl_juros           += $resultado[$linha]->apgvl_juros;
                $total->tapgvl_multa           += $resultado[$linha]->apgvl_multa;
                $total->tapgvl_ir              += $resultado[$linha]->apgvl_ir;
                $total->tapgvl_pis             += $resultado[$linha]->apgvl_pis;
                $total->tapgvl_iss             += $resultado[$linha]->apgvl_iss;
                $total->tapgvl_inss            += $resultado[$linha]->apgvl_inss;
                $total->tapgvl_cofins          += $resultado[$linha]->apgvl_cofins;
                $total->tapgvl_csll            += $resultado[$linha]->apgvl_csll;
                $total->tvalor_pagamento       += $resultado[$linha]->valor_pagamento;
                $total->tapgvl_tarifa_bancaria += $resultado[$linha]->apgvl_tarifa_bancaria;
                $total->tapapgcsrf             += $resultado[$linha]->apgcsrf;
            }

            //verifica status (coluna img)
            if ($dados->excluido == 0) {
                if ($dados->apgautorizado == "t") {
                    $img = "ap01.jpg"; //autorizado
                }else{
                    $img = "ap03.jpg"; //pendente
                }
            }else{
                $img = "ap04.jpg"; //cancelado
            }
            $resultado[$linha]->status_imagem = $img;

            //col ocorrencia
            $resultado[$linha]->ocorrencia = false;
            if($dados->apgforma_recebimento == ""){
                $resultado[$linha]->ocorrencia = "*Não há Forma de Pagamento";
            }else{
                if( in_array($dados->fortipo, array("J","A","M","R","T","P","E","H","C","L")) && !verifica_cnpj($this->ajustaString($dados->fordocto,14,"0")) ){
                    $resultado[$linha]->ocorrencia = "*Pessoa Jurídica e CNPJ Inválido";
                }elseif( in_array($dados->fortipo, array("F","N","U")) && !verifica_cpf($this->ajustaString($dados->fordocto,11,"0")) ) {
                    $resultado[$linha]->ocorrencia = "*Pessoa Fisica e CPF Inválido";
                }elseif($dados->apgprevisao == 1) {
                    $resultado[$linha]->ocorrencia = "*Título em previsão";
                }else{
                    if(!$formaRecebimento = $this->formaRecebimento($dados->apgforma_recebimento)){
                        $resultado[$linha]->ocorrencia = "Forma de recebimento inválida";
                        $resultado[$linha]->checkbox = false;
                    }else{
                        $resultado[$linha]->ocorrencia = $formaRecebimento;
                    }
                }
            }
        }

        //Tipo Documento
        $resultado[$linha]->tipo_documento = $this->tipoDocumento($dados->apgtipo_docto);

        $this->view->total['adiantamento'] = $total;

        return $resultado;
    }

    private function calcula_dividindo($numero_parcelas, $parcela, $valor){

        if ($parcela == $numero_parcelas){
            return ($valor  * 100 - (ceil($valor  * 100 / $numero_parcelas) * ($numero_parcelas - 1 ))) / 100;
        }else{
            return ceil($valor * 100 / $numero_parcelas) / 100;
        }
    }

    private function calcula_proporcional($numero_parcelas, $parcela, $valor, $proporcao, $valor_calculado){

        if ($parcela == $numero_parcelas){
            return $valor - $valor_calculado;
        }else{
            return ceil($proporcao * $valor * 100) / 100;
        }
    }

    /**
     * Ajusta tamanho da string, incluindo ou retirando elementos
     * @param  string $texto   string a ser tratada
     * @param  int    $tamanho tamanho que a string deve conter
     * @param  string $char    char que deve ser completada a string
     * @param  string $posicao constante 'D' ou 'E' (Direita ou Esquerda)
     * @return string          [description]
     */
	private function ajustaString( $texto,  $tamanho,  $char, $posicao = 'D'){

        $texto = (string)$texto;
        $char = (string)$char;

        $tamanhoChar = strlen($char);
        if($tamanhoChar == 0){
            return $texto;
        }

        $tamanhoTexto = strlen($texto);

        if($tamanhoTexto > $tamanho){
            //remove chars do final da string
            $retornoString = substr($texto, 0, $tamanho);
        }elseif($tamanhoTexto < $tamanho){
            if(strtoupper($posicao) == 'E'){
                $retornoString = str_pad($texto, $tamanho, $char, STR_PAD_LEFT);
            }else{
                $retornoString = str_pad($texto, $tamanho, $char);
            }
        }else{
            $retornoString = $texto;
        }

        return $retornoString;
    }


	public function atualizarArquivos()	{
        try {

            //Banco Itaú
            $BaixaContasAPagarItau = new BaixaContasAPagarItau();
            $retorno = $BaixaContasAPagarItau->baixarContasAPagarItau();

            if ($retorno === null) {

                throw new Exception('Erro ao baixar contas a pagar Itaú.');
            
            } else {
                
                $this->view->mensagemSucesso = "Realizada a baixa de ".count($retorno)." conta(s) a pagar Itaú.";
            }

        } catch (Exception $e) {

            $this->view->mensagemErro = $e->getMessage();
            
        }

        $this->view->parametros->aba = "envio_arquivos";

        return true;

    }

    private function somenteNumeros($valor){
        return preg_replace("/[^0-9]/", "", $valor);
    }


    /**
     *
     * @param type $dataInicial
     * @param type $dataFinal
     * @param type $meses
     * @return int -1 Se data inicial for maior que final
     */
    private function validarDataMaior($dataInicial, $dataFinal) {
    	$retorno = 0;
    	$dataInicioArr = explode('/', $dataInicial);
        $dataFimArr = explode('/', $dataFinal);
		
        $dataInicioTS = strtotime($dataInicioArr[2] . '-' . $dataInicioArr[1] . '-' . $dataInicioArr[0]);
        $dataFimTS = strtotime($dataFimArr[2] . '-' . $dataFimArr[1] . '-' . $dataFimArr[0]);
		
        if ($dataInicioTS > $dataFimTS) {
            $retorno = -1;
        }

        return $retorno;
    }


    /**
     *  Metodo para enviar arquivo via ftp
     *  @param  class $parametros
     *  @param  class $parametros->servidor
     *  @param  class $parametros->usuario
     *  @param  class $parametros->senha
     *  @param  class $parametros->nomeArquivo
     *  @param  class $parametros->caminhoLocal
     *  @param  class $parametros->destino
     *  @return true
     */
    private function enviarArquivoFTP($parametros){

            $conecta = ftp_connect($parametros->servidor,21);
            if(!$conecta){
                throw new Exception("Erro ao conectar com o servidor FTP");
            }
      
            /* Autenticar no servidor */
            $login = ftp_login($conecta, $parametros->usuario, $parametros->senha);
            if(!$login){ 
                throw new Exception("Erro ao autenticar com o servidor FTP");
            }
            
            ftp_pasv($conecta, TRUE);

            $envio = ftp_put($conecta, $parametros->destino.$parametros->nomeArquivo, $parametros->caminhoLocal.$parametros->nomeArquivo, FTP_BINARY); 
            if(!$envio){ 
                throw new Exception("Erro ao enviar o arquivo ao servidor FTP");
            }

            ftp_close($conecta);

            return true;
    }

    /**
     * Busca arquivos de LOGs no servidor STCP
     */
    private function consultaLogsItau(){

        //Atribui parametros FTP
        $parametrosFTP = new stdClass;

        $parametrosFTP->caminhoLocal = $this->caminhoLocal;

        //Atribui parametros de busca
        $parametros = new stdClass;
        $parametros->codigoParametro = 'CONTAS_A_PAGAR';
        
        //Busca caminho da pasta retorno/ remessa Itaú
        $parametros->codigoItemParametro = 'PASTA_LOG_RETORNO_ITAU';
        if ($caminhoLogs = $this->daoCRN->buscaParametros($parametros)) { 
            $parametrosFTP->caminhoLogs = trim($caminhoLogs[0]->pcsidescricao);
        }

        //Busca servidor da retorno remessa Itaú
        $parametros->codigoItemParametro = 'SERVIDOR_STCP_'.$this->ambiente;
        if ($servidor = $this->daoCRN->buscaParametros($parametros)) { 
            $parametrosFTP->servidor = trim($servidor[0]->pcsidescricao);
        }

        //Busca usuario FTP
        $parametros->codigoItemParametro = 'USUARIO_STCP_'.$this->ambiente;
        if ($usuario = $this->daoCRN->buscaParametros($parametros)) { 
            $parametrosFTP->usuario = trim($usuario[0]->pcsidescricao);
        }

        //Busca senha FTP
        $parametros->codigoItemParametro = 'PASSWORD_STCP_'.$this->ambiente;
        if ($senha = $this->daoCRN->buscaParametros($parametros)) { 
            $parametrosFTP->senha = trim($senha[0]->pcsidescricao);
        }


        $conecta = ftp_connect($parametrosFTP->servidor,21);
        if(!$conecta){
            throw new Exception("Erro ao conectar com o servidor FTP");
        }
  
        /* Autenticar no servidor */
        $login = ftp_login($conecta, $parametrosFTP->usuario, $parametrosFTP->senha);
        if(!$login){ 
            throw new Exception("Erro ao autenticar com o servidor FTP");
        }
        
        ftp_pasv($conecta, TRUE);

        $arquivosLogs = array();

        //Consulta arquivos que constam na pasta
        $arquivosLogs = ftp_nlist($conecta, $parametrosFTP->caminhoLogs);

        $arquivosLogs = array_reverse($arquivosLogs);

        $arquivosLogs = array_chunk($arquivosLogs, 20);

        ftp_close($conecta);

        return $arquivosLogs[0];
    }

    /**
     * [getLogsRemessa description]
     * @return [type] [description]
     */
    private function getLogsRemessa() {

        $dirLog = "/var/www/log/";

        $arquivosLogs = scandir($dirLog);

        $arquivosLogs = array_reverse($arquivosLogs);

        foreach ($arquivosLogs as $chave => $valor) {
            if(substr($valor, -8, -4) != '.RET'){
                unset($arquivosLogs[$chave]);
            }else{
                $arquivosLogs[$chave] = $dirLog.$valor;
            }       
        }

        $arquivosLogs = array_chunk($arquivosLogs, 20);
        
        return $arquivosLogs[0];
    }

    /**
     * Baixa arquivo de log do servidor STCP Itaú
     * @return [type] [description]
     */
    public function baixarLogItau(){

        $parametros = $this->tratarParametros();

        try {
            //Atribui parametros FTP
            $parametrosFTP = new stdClass;

            $parametrosFTP->caminhoLocal = $this->caminhoLocal;

            //Atribui parametros de busca
            $parametros->codigoParametro = 'CONTAS_A_PAGAR';
            
            //Busca caminho da pasta retorno/ remessa Itaú
            $parametros->codigoItemParametro = 'PASTA_LOG_RETORNO_ITAU';
            if ($caminhoLogs = $this->daoCRN->buscaParametros($parametros)) { 
                $parametrosFTP->caminhoLogs = trim($caminhoLogs[0]->pcsidescricao);
            }

            //Busca servidor da retorno remessa Itaú
            $parametros->codigoItemParametro = 'SERVIDOR_STCP_'.$this->ambiente;
            if ($servidor = $this->daoCRN->buscaParametros($parametros)) { 
                $parametrosFTP->servidor = trim($servidor[0]->pcsidescricao);
            }

            //Busca usuario FTP
            $parametros->codigoItemParametro = 'USUARIO_STCP_'.$this->ambiente;
            if ($usuario = $this->daoCRN->buscaParametros($parametros)) { 
                $parametrosFTP->usuario = trim($usuario[0]->pcsidescricao);
            }

            //Busca senha FTP
            $parametros->codigoItemParametro = 'PASSWORD_STCP_'.$this->ambiente;
            if ($senha = $this->daoCRN->buscaParametros($parametros)) { 
                $parametrosFTP->senha = trim($senha[0]->pcsidescricao);
            }


            $conecta = ftp_connect($parametrosFTP->servidor,21);
            if(!$conecta){
                throw new Exception("Erro ao conectar com o servidor FTP");
            }
      
            /* Autenticar no servidor */
            $login = ftp_login($conecta, $parametrosFTP->usuario, $parametrosFTP->senha);
            if(!$login){ 
                throw new Exception("Erro ao autenticar com o servidor FTP");
            }
            
            ftp_pasv($conecta, TRUE);

            $retorno = ftp_get($conecta, $parametrosFTP->caminhoLocal.$parametros->arquivo, $parametrosFTP->caminhoLogs.$parametros->arquivo, FTP_BINARY); 
            if(!$retorno){ 
                throw new Exception("Erro ao buscar o arquivo no servidor FTP");
            }

            ftp_close($conecta);

            //Incluir a view padrão
            header('Location:download.php?arquivo=' . $parametrosFTP->caminhoLocal.$parametros->arquivo);
            
        } catch (Exception $e) {
            $this->view->mensagemErro = $e->getMessage();     
        }

        $this->index();
    }


    /**
     * Remove acentos da string
     * @param  string $str
     * @return string
     */
    private function removerAcentos($str){
         
        $busca     = array("à","á","ã","â","ä","è","é","ê","ë","ì","í","î","ï","ò","ó","õ","ô","ö","ù","ú","û","ü","ç", "'", '"', "%");
        $substitui = array("a","a","a","a","a","e","e","e","e","i","i","i","i","o","o","o","o","o","u","u","u","u","c", "\'" , '\"', "\\\%");
         
        $str       = str_replace($busca,$substitui,$str);
         
        $busca     = array("À","Á","Ã","Â","Ä","È","É","Ê","Ë","Ì","Í","Î","Ï","Ò","Ó","Õ","Ô","Ö","Ù","Ú","Û","Ü","Ç","‡","“", "<", ">" );
        $substitui = array("A","A","A","A","A","E","E","E","E","I","I","I","I","O","O","O","O","O","U","U","U","U","C", ""  ,"" , "" , "");
         
        $str       = str_replace($busca,$substitui,$str);
        return $str;
    }





    /**
     * --------------------------
     * ---- Layout CNAB Itaú ----
     * --------------------------
     */
    
    private function segmentoTitulo($parametros, $dadosTitulo){

        //define o segmento do titulo
        switch ($dadosTitulo->apgforma_recebimento) {
            case '1': //Crédito em C/C
            case '2': //Crédito Conta/Salário
                $segmento = 'A';
                break;
            case '30': //Boleto
            case '31': //Boleto
                //concessionária ou GNRE
                if($dadosTitulo->apgtipo_docto == '11' || $dadosTitulo->apgtipo_docto == '10'){
                    $segmento = 'O';
                //Outros
                }elseif ($dadosTitulo->apgtipo_docto == '05') {
                    $segmento = 'J';
                //GPS, DARF, DARF SIMPLES, GARE – SP ICMS e FGTS
                }elseif ($dadosTitulo->apgtipo_docto == '07' || $dadosTitulo->apgtipo_docto == '06' || $dadosTitulo->apgtipo_docto == '09' || $dadosTitulo->apgtipo_docto == '12' || $dadosTitulo->apgtipo_docto == '13') {
                    $segmento = 'N';
                }else{
                    $segmento = false;
                }
                break;
            default:
                $segmento = false;
                break;
        }

        return $segmento;

    }


    /**
     * Direciona os dados do titulo ao metodo do segmento
     * @param  [type] $dadosTitulo [description]
     * @param  [type] $parametros  [description]
     * @return [type]              [description]
     */
    private function geraLinha($parametros, $dadosTitulo){

        /* Criado para alterar todos os valores */

        // Total de Impostos
        $impostos = $dadosTitulo->apgvl_ir        +
                    $dadosTitulo->apgvl_inss      +
                    $dadosTitulo->apgvl_csll      +
                    $dadosTitulo->apgvl_pis       +
                    $dadosTitulo->apgvl_cofins    +
                    $dadosTitulo->apgcsrf         +
                    $dadosTitulo->apgvl_iss       ;        

        $dadosTitulo->apgvl_desconto            = (($dadosTitulo->apgforma_recebimento == "31" && $dadosTitulo->apgtipo_docto == "05") ? $dadosTitulo->apgvl_desconto : 0 );
        $dadosTitulo->apgvl_tarifa_bancaria     = (($dadosTitulo->apgforma_recebimento == "31" && $dadosTitulo->apgtipo_docto == "05") ? $dadosTitulo->apgvl_tarifa_bancaria : 0);        

        //define o segmento do titulo
        switch ($dadosTitulo->segmento) {
            case 'A': 
                $this->arquivo->titulos[$dadosTitulo->segmento][$dadosTitulo->formaPagamento][] = $this->segmentoA($parametros, $dadosTitulo);   
                break;
            case 'J':
                $this->arquivo->titulos[$dadosTitulo->segmento][$dadosTitulo->formaPagamento][] = $this->segmentoJ($parametros, $dadosTitulo);   
                $this->arquivo->titulos[$dadosTitulo->segmento][$dadosTitulo->formaPagamento][] = $this->segmentoJ52($parametros, $dadosTitulo);
                break;
            case 'O':
                $this->arquivo->titulos[$dadosTitulo->segmento][$dadosTitulo->formaPagamento][] = $this->segmentoO($parametros, $dadosTitulo);   
                break;
            case 'N':
                $this->arquivo->titulos[$dadosTitulo->segmento][$dadosTitulo->formaPagamento][] = $this->segmentoN($parametros, $dadosTitulo);   
                break;
            default:
                break;
        }
    }

    
    /**
     * Monta HEADER DE ARQUIVO
     * @param  [type] $parametros [description]
     * @return [type]             [description]
     */
    private function headerArquivo($parametros){

        $retorno = array();

        $retorno['codBanco']      = $this->ajustaString($parametros->banco, 3, ' ');
        $retorno['codLote']       = '0000';
        $retorno['tipoRegistro']  = '0';
        $retorno['brancos1']      = $this->ajustaString('', 6, ' ');
        $retorno['tipoLayout']    = '080';
        $retorno['tipoInscricao'] = '2'; //cnpj
        $retorno['cnpjEmpresa']   = $this->ajustaString($parametros->dadosEmpresa->teccnpj, 14, ' ');
        $retorno['brancos2']      = $this->ajustaString('', 20, ' ');
        $retorno['agencia']       = $this->ajustaString($parametros->dadosEmpresa->abagencia, 5, '0', 'E');
        $retorno['brancos3']      = $this->ajustaString('', 1, ' ');
        $retorno['conta']         = $this->ajustaString($parametros->dadosEmpresa->abconta_corrente[0], 12, '0', 'E');
        $retorno['brancos4']      = $this->ajustaString('', 1, ' ');
        $retorno['dac']           = $this->ajustaString($parametros->dadosEmpresa->abconta_corrente[1], 1, '0', 'E');
        $retorno['empresa']       = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecrazao), 30, ' ');
        $retorno['nomeBanco']     = $this->ajustaString('ITAU S/A', 30, ' ');
        $retorno['brancos5']      = $this->ajustaString('', 10, ' ');
        $retorno['arquivoCodigo'] = '1'; //remessa
        $retorno['data']          = date('dmY');
        $retorno['hora']          = date('His');
        $retorno['zeros']         = '000000000';
        $retorno['densidade']     = '00000';
        $retorno['brancos6']      = $this->ajustaString('', 69, ' ');

        return $retorno;
    }

    /**
     * Monta TRAILER DE ARQUIVO
     * @param  [type] $parametros [description]
     * @return [type]             [description]
     */
    private function trailerArquivo($parametros){

        $retorno = array();

        $retorno['codBanco']       = $this->ajustaString($parametros->banco, 3, ' ');
        $retorno['codLote']        = '9999';
        $retorno['tipoRegistro']   = '9';
        $retorno['brancos1']       = $this->ajustaString('', 9, ' ');
        $retorno['totalLotes']     = $this->ajustaString($parametros->totalLotes, 6, '0', 'E');
        $retorno['totalRegistros'] = $this->ajustaString($parametros->totalRegistrosArquivo, 6, '0', 'E');
        $retorno['brancos2']       = $this->ajustaString('', 211, ' ');

        return $retorno;
    }

    /**
     * Monta HEADER DO LOTE DO ARQUIVO
     * @param  [type] $parametros [description]
     * @return [type]             [description]
     */
    private function headerLoteArquivo($parametros, $segmento, $formaPagamento){

        $retorno = array();

        $retorno['codBanco']        = $this->ajustaString($parametros->banco, 3, ' ');
        $retorno['codLote']         = $this->ajustaString($parametros->codLote[$segmento][$formaPagamento], 4, '0', 'E');
        $retorno['tipoRegistro']    = '1';
        $retorno['tipoOperacao']    = 'C';
        $retorno['tipoPagamento']   = ($segmento == 'N' || ($segmento == 'O' && $formaPagamento == '91') ? '22' : '20'); //22-Tributos | 20-Fornecedores
        $retorno['formaPagamento']  = $this->ajustaString($formaPagamento, 2, '0', 'E');
        $retorno['tipoLayout']      = ($segmento == 'A' ? '040' : '030');
        $retorno['brancos1']        = $this->ajustaString('', 1, ' ');
        $retorno['tipoInscricao']   = '2'; //cnpj
        $retorno['cnpjEmpresa']     = $this->ajustaString($parametros->dadosEmpresa->teccnpj, 14, ' ');
        $retorno['brancos2']        = $this->ajustaString('', 20, ' ');
        $retorno['agencia']         = $this->ajustaString($parametros->dadosEmpresa->abagencia, 5, '0', 'E');
        $retorno['brancos3']        = $this->ajustaString('', 1, ' ');
        $retorno['conta']           = $this->ajustaString($parametros->dadosEmpresa->abconta_corrente[0], 12, '0', 'E');
        $retorno['brancos4']        = $this->ajustaString('', 1, ' ');
        $retorno['dac']             = $this->ajustaString($parametros->dadosEmpresa->abconta_corrente[1], 1, '0', 'E');
        $retorno['empresa']         = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecrazao), 30, ' ');
        $retorno['finalidadeLote']  = $this->ajustaString('', 30, ' ');
        $retorno['historicoCC']     = $this->ajustaString('', 10, ' ');
        $retorno['enderecoEmpresa'] = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecendereco[0]), 30, ' ');
        $retorno['numeroEmpresa']   = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecendereco[1]), 5, ' ');
        $retorno['compEmpresa']     = $this->ajustaString('', 15, ' ');
        $retorno['cidadeEmpresa']   = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->teccidade), 20, ' ');
        $retorno['cepEmpresa']      = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->cep), 8, ' ');
        $retorno['estadoEmpresa']   = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecuf), 2, ' ');
        $retorno['brancos5']        = $this->ajustaString('', 8, ' ');
        $retorno['ocorrencia']      = $this->ajustaString('', 10, ' ');

        return $retorno;
    }

    /**
     * Monta TRAILER DO LOTE DO ARQUIVO
     * @param  stdClass $parametros  [description]
     * @param  stdClass $dadosTitulo [description]
     * @return [type]                [description]
     */
    private function trailerLoteArquivo($parametros, $segmento, $formaPagamento){

        $retorno = array();

        $retorno['codBanco']                 = $this->ajustaString($parametros->banco, 3, ' ');
        $retorno['codLote']                  = $this->ajustaString($parametros->codLote[$segmento][$formaPagamento], 4, '0', 'E');
        $retorno['tipoRegistro']             = '5';
        $retorno['brancos1']                 = $this->ajustaString('', 9, ' ');
        $retorno['totalRegistros']           = $this->ajustaString($parametros->totalRegistrosLote[$segmento][$formaPagamento], 6, '0', 'E');
        if($segmento == 'O'){
            $retorno['totalValorPagtos']     = $this->ajustaString($parametros->totalValorPagtosLote[$segmento][$formaPagamento], 18, '0', 'E');
            $retorno['somaQtdMoedas']        = $this->ajustaString('', 15, '0');
            $retorno['brancos2']             = $this->ajustaString('', 174, ' ');
        }elseif ($segmento == 'N') {
            $retorno['totalValorPagtos']     = $this->ajustaString($parametros->totalValorPagtosLote[$segmento][$formaPagamento] - $parametros->totalOutrasEntidades[$segmento][$formaPagamento], 14, '0', 'E');
            $retorno['totalOutrasEntidades'] = $this->ajustaString($parametros->totalOutrasEntidades[$segmento][$formaPagamento], 14, '0', 'E');
            $retorno['totalValorAcrescimos'] = $this->ajustaString(substr($parametros->totalValorAcrescimos[$segmento][$formaPagamento], 0 , -1), 14, '0', 'E');
            $retorno['totalValorArrecadado'] = $this->ajustaString($parametros->totalValorArrecadado[$segmento][$formaPagamento], 14, '0', 'E');
            $retorno['brancos2']             = $this->ajustaString('', 151, ' ');
        }else{
            $retorno['totalValorPagtos']     = $this->ajustaString($parametros->totalValorPagtosLote[$segmento][$formaPagamento], 18, '0', 'E');
            $retorno['zeros1']               = $this->ajustaString('', 18, '0');    
            $retorno['brancos2']             = $this->ajustaString('', 171, ' ');
        }
        $retorno['ocorrencia']               = $this->ajustaString('', 10, ' ');

        return $retorno;
    }


     /**
     * Monta SEGMENTO A (Pagamentos através de cheque, OP, DOC, TED e crédito em conta corrente)
     * @param  [type] $parametros [description]
     * @return [type]             [description]
     */
    private function segmentoA($parametros, $dadosTitulo){

        $retorno = array();

        $retorno['codBanco']                = $this->ajustaString($parametros->banco, 3, ' ');
        $retorno['codLote']                 = $this->ajustaString($parametros->codLote[$dadosTitulo->segmento][$dadosTitulo->formaPagamento], 4, '0', 'E');
        $retorno['tipoRegistro']            = '3';
        $retorno['numRegistro']             = $this->ajustaString($dadosTitulo->numRegistro, 5, '0', 'E');
        $retorno['segmento']                = "A";
        $retorno['tipoMovimento']           = '000';
        $retorno['camara']                  = '000';
        $retorno['bancoFavorecido']         = $this->ajustaString($dadosTitulo->forbanco, 3, '0', 'E');
        $retorno['agenciaContaFavorecido']  = $this->agenciaConta($dadosTitulo->forbanco, $dadosTitulo->foragencia, $dadosTitulo->forconta, $dadosTitulo->fordigito_conta);
        $retorno['nomeFavorecido']          = $this->ajustaString($this->removerAcentos($dadosTitulo->forfornecedor), 30, ' ');
        $retorno['seuNumero']               = $this->ajustaString($dadosTitulo->apgoid, 20, '0', 'E');
        $retorno['dataPagamento']           = $this->ajustaString($dadosTitulo->apgdt_pagamento, 8, ' ');
        $retorno['moeda']                   = "REA";
        $retorno['codigoISPB']              = $this->ajustaString('', 8, '0');
        $retorno['zeros1']                  = $this->ajustaString('', 7, '0');
        $retorno['valorPagamento']          = $this->ajustaString(substr($dadosTitulo->valor_documento, 0 , -1), 15, '0', 'E');
        $retorno['nossoNumero']             = $this->ajustaString('', 15, ' ');
        $retorno['brancos1']                = $this->ajustaString('', 5, ' ');
        $retorno['dataEfetiva']             = $this->ajustaString('', 8, ' ');
        $retorno['valorEfetivo']            = $this->ajustaString('', 15, ' ');
        $retorno['finalidadeDetalhe']       = $this->ajustaString('', 20, ' ');
        $retorno['numDocumento']            = $this->ajustaString('', 6, ' ');
        $retorno['numInscricao']            = $this->ajustaString($dadosTitulo->fordocto, 14, ' ');
        $retorno['finalidadeDocStatusFunc'] = $this->ajustaString('', 2, '0');
        $retorno['finalidadeTed']           = '00010';
        $retorno['brancos2']                = $this->ajustaString('', 5, ' ');
        $retorno['aviso']                   = '0';
        $retorno['ocorrencias']             = $this->ajustaString('', 10, ' ');

        return $retorno;
    }
    
    /**
     * Monta SEGMENTO J e J-52 (Liquidação de títulos (boletos) em cobrança no Itaú e em outros Bancos)
     * @param  [type] $parametros [description]
     * @return [type]             [description]
     */
    private function segmentoJ($parametros, $dadosTitulo){        
        
        $retorno = array();

        $dadosCodigoBarras = $this->codigoBarrasTituloCobranca($dadosTitulo->apgcodigo_barras);

        $retorno['codBanco']             = $this->ajustaString($parametros->banco, 3, ' ');
        $retorno['codLote']              = $this->ajustaString($parametros->codLote[$dadosTitulo->segmento][$dadosTitulo->formaPagamento], 4, '0', 'E');
        $retorno['tipoRegistro']         = '3';
        $retorno['numRegistro']          = $this->ajustaString($dadosTitulo->numRegistro, 5, '0', 'E');
        $retorno['segmento']             = "J";
        $retorno['tipoMovimento']        = '000';
        $retorno['CBbancoFavorecido']    = $dadosCodigoBarras->codBanco;
        $retorno['CBMoeda']              = $dadosCodigoBarras->codMoeda;
        $retorno['CBDigitoVerificador']  = $dadosCodigoBarras->dvGeral;
        $retorno['CBFatorVencimento']    = $dadosCodigoBarras->fatorVencimento;
        $retorno['CBValor']              = $dadosCodigoBarras->valorTitulo;
        $retorno['CBCampoLivre']         = $dadosCodigoBarras->campoLivre;
        $retorno['nomeFavorecido']       = $this->ajustaString($this->removerAcentos($dadosTitulo->forfornecedor), 30, ' ');
        $retorno['dataVencimento']       = ( $dadosCodigoBarras->fatorVencimento == '0000' ?  $this->ajustaString($dadosTitulo->apgdt_pagamento, 8, ' ') : $this->gerarFatorVencimento($dadosCodigoBarras->fatorVencimento) );
        $retorno['valorTitulo']          = $this->ajustaString(substr($dadosTitulo->valor_titulo_equal_boleto, 0 , -2), 15, '0', 'E');
        $retorno['descontos']            = $this->ajustaString(substr($dadosTitulo->apgvl_desconto, 0 , -1), 15, '0', 'E');
        $retorno['acrescimos']           = $this->ajustaString(                                                  
                                                      substr($dadosTitulo->apgvl_juros, 0 , -1)
                                                    + substr($dadosTitulo->apgvl_multa, 0 , -1)                                                    
                                                  , 15, '0', 'E');        

        $retorno['dataPagamento']        = $this->ajustaString($dadosTitulo->apgdt_pagamento, 8, ' ');
        $retorno['valorPagamento']       = $this->ajustaString(substr($dadosTitulo->valor_documento, 0 , -2), 15, '0', 'E');
        $retorno['zeros1']               = $this->ajustaString('', 15, '0');
        $retorno['zeros1']               = $this->ajustaString('', 15, '0');
        $retorno['seuNumero']            = $this->ajustaString($dadosTitulo->apgoid, 20, '0', 'E');
        $retorno['brancos1']             = $this->ajustaString('', 13, ' ');
        $retorno['nossoNumero']          = $this->ajustaString('', 15, ' ');
        $retorno['ocorrencias']          = $this->ajustaString('', 10, ' ');

        return $retorno;
    }

    private function segmentoJ52($parametros, $dadosTitulo){

        $retorno = array();

        $retorno['codBanco']             = $this->ajustaString($parametros->banco, 3, ' ');
        $retorno['codLote']              = $this->ajustaString($parametros->codLote[$dadosTitulo->segmento][$dadosTitulo->formaPagamento], 4, '0', 'E');
        $retorno['tipoRegistro']         = '3';
        $retorno['numRegistro']          = $this->ajustaString($dadosTitulo->numRegistro, 5, '0', 'E');
        $retorno['segmento']             = "J";
        $retorno['tipoMovimento']        = '000';
        $retorno['codRegistro']          = '52';
        $retorno['tipoInscricaoSacado']  = '2'; //cnpj
        $retorno['cnpjEmpresaSacado']    = $this->ajustaString($parametros->dadosEmpresa->teccnpj, 15, '0', 'E');
        $retorno['nomeSacado']           = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecrazao), 40, ' ');
        $retorno['tipoInscricaoCedente'] = '2'; //cnpj
        $retorno['inscricaoCedente']     = $this->ajustaString($dadosTitulo->fordocto, 15, '0', 'E');
        $retorno['nomeCedente']          = $this->ajustaString($this->removerAcentos($dadosTitulo->forfornecedor), 40, ' ');
        $retorno['tipoInscricaoSacador'] = '2'; //cnpj
        $retorno['inscricaoSacador']     = $this->ajustaString($dadosTitulo->fordocto, 15, '0', 'E');
        $retorno['nomeSacador']          = $this->ajustaString($this->removerAcentos($dadosTitulo->forfornecedor), 40, ' ');
        $retorno['brancos1']             = $this->ajustaString('', 53, ' ');
        
        return $retorno;

    }   

    /**
     * Monta SEGMENTO O (Pagamento de Contas de Concessionárias e Tributos com código de barras)
     * @param  [type] $parametros [description]
     * @return [type]             [description]
     */
    private function segmentoO($parametros, $dadosTitulo){

        $retorno = array();

        $retorno['codBanco']             = $this->ajustaString($parametros->banco, 3, ' ');
        $retorno['codLote']              = $this->ajustaString($parametros->codLote[$dadosTitulo->segmento][$dadosTitulo->formaPagamento], 4, '0', 'E');
        $retorno['tipoRegistro']         = '3';
        $retorno['numRegistro']          = $this->ajustaString($dadosTitulo->numRegistro, 5, '0', 'E');
        $retorno['segmento']             = "O";
        $retorno['tipoMovimento']        = '000';
        $retorno['codigoBarras']         = $this->ajustaString($dadosTitulo->apgcodigo_barras, 48, ' ');
        $retorno['nomeFavorecido']       = $this->ajustaString($this->removerAcentos($dadosTitulo->forfornecedor), 30, ' ');
        $retorno['dataVencimento']       = $this->ajustaString($dadosTitulo->apgdt_vencimento, 8, ' ');
        $retorno['moeda']                = "REA";
        $retorno['qtdMoeda']             = $this->ajustaString('', 15, ' ');
        $retorno['valorPagar']           = $this->ajustaString(substr($dadosTitulo->valor_documento, 0 , -1), 15, '0', 'E');
        $retorno['dataPagamento']        = $this->ajustaString($dadosTitulo->apgdt_pagamento, 8, ' ');
        $retorno['valorPago']            = $this->ajustaString('', 15, ' ');
        $retorno['brancos1']             = $this->ajustaString('', 3, ' ');
        $retorno['notaFiscal']           = $this->ajustaString($dadosTitulo->apgno_notafiscal, 9, ' ');
        $retorno['brancos2']             = $this->ajustaString('', 3, ' ');
        $retorno['seuNumero']            = $this->ajustaString($dadosTitulo->apgoid, 20, '0', 'E');
        $retorno['brancos3']             = $this->ajustaString('', 21, ' ');
        $retorno['nossoNumero']          = $this->ajustaString('', 15, ' ');
        $retorno['ocorrencias']          = $this->ajustaString('', 10, ' ');

        return $retorno;
    }

    /**
     * Monta SEGMENTO N (Pagamento de Tributos sem código de barras e FGTS-GRF/GRRF/GRDE com código de barras)
     * @param  [type] $parametros  [description]
     * @param  [type] $dadosTitulo [description]
     * @return [type]              [description]
     */
    private function segmentoN($parametros, $dadosTitulo){

        $retorno = array();

        $retorno['codBanco']                      = $this->ajustaString($parametros->banco, 3, ' ');
        $retorno['codLote']                       = $this->ajustaString($parametros->codLote[$dadosTitulo->segmento][$dadosTitulo->formaPagamento], 4, '0', 'E');
        $retorno['tipoRegistro']                  = '3';
        $retorno['numRegistro']                   = $this->ajustaString($dadosTitulo->numRegistro, 5, '0', 'E');
        $retorno['segmento']                      = "N";
        $retorno['tipoMovimento']                 = '000';
        
        switch ($dadosTitulo->apgtipo_docto) {
            case '07': //GPS
                    $retorno['tributo']           = '01';
                    $retorno['codPagamento']      = $this->ajustaString($dadosTitulo->apgcodigo_receita, 4, '0', 'E');
                    $retorno['competencia']       = $this->ajustaString($dadosTitulo->mesano_referencia, 6, ' ');
                    $retorno['cnpjContribuinte']  = $this->ajustaString($dadosTitulo->apgidentificador_gps, 14, ' ', 'E');
                    $retorno['valorTributo']      = $this->ajustaString(substr($dadosTitulo->valor_titulo_equal_boleto, 0 , -1) - $dadosTitulo->apgvalor_entidades, 14, '0', 'E');
                    $retorno['valorEntidades']    = $this->ajustaString($dadosTitulo->apgvalor_entidades, 14, '0', 'E');
                    $retorno['atualMonetaria']    = $this->ajustaString(substr($dadosTitulo->apgvl_multa, 0 , -1) + substr($dadosTitulo->apgvl_juros, 0 , -1), 14, '0', 'E');
                    $retorno['valorArrecadado']   = $this->ajustaString(substr($dadosTitulo->valor_documento, 0 , -1), 14, '0', 'E');
                    $retorno['dataArrecadacao']   = $this->ajustaString($dadosTitulo->apgdt_pagamento, 8, ' ');
                    $retorno['brancos1']          = $this->ajustaString('', 8, ' ');
                    $retorno['usoEmpresa']        = $this->ajustaString('', 50, ' ');
                    $retorno['contribuinte']      = $this->ajustaString($this->removerAcentos($dadosTitulo->contribuinte_gps), 30, ' ');
                break;
            case '06': //DARF NORMAL
                    $retorno['tributo']           = '02';
                    $retorno['codReceita']        = $this->ajustaString($dadosTitulo->apgcodigo_receita, 4, '0', 'E');
                    $retorno['tipoInscricao']     = '2'; //cnpj
                    $retorno['cnpjEmpresa']       = $this->ajustaString($parametros->dadosEmpresa->teccnpj, 14, ' ');
                    $retorno['periodoApuracao']   = $this->ajustaString($dadosTitulo->apgperiodo_referencia, 8, ' ');
                    $retorno['numeroReferencia']  = $this->ajustaString($dadosTitulo->apgnumero_referencia, 17, '0', 'E');
                    $retorno['valorPrincipal']    = $this->ajustaString(substr($dadosTitulo->valor_titulo_equal_boleto, 0 , -1), 14, '0', 'E');
                    $retorno['multa']             = $this->ajustaString(substr($dadosTitulo->apgvl_multa, 0 , -1), 14, '0', 'E');
                    $retorno['juros']             = $this->ajustaString(substr($dadosTitulo->apgvl_juros, 0 , -1), 14, '0', 'E');
                    $retorno['valorTotal']        = $this->ajustaString(substr($dadosTitulo->valor_documento, 0 , -1), 14, '0', 'E');
                    $retorno['dataVencimento']    = $this->ajustaString($dadosTitulo->apgdt_vencimento, 8, ' ');
                    $retorno['dataPagamento']     = $this->ajustaString($dadosTitulo->apgdt_pagamento, 8, ' ');
                    $retorno['brancos1']          = $this->ajustaString('', 30, ' ');
                    $retorno['empresa']           = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecrazao), 30, ' ');
                break;
            case '12': //DARF SIMPLES
                    $retorno['tributo']           = '03';
                    $retorno['codReceita']        = $this->ajustaString($dadosTitulo->apgcodigo_receita, 4, '0', 'E');
                    $retorno['tipoInscricao']     = '2'; //cnpj
                    $retorno['cnpjEmpresa']       = $this->ajustaString($parametros->dadosEmpresa->teccnpj, 14, ' ');
                    $retorno['periodoApuracao']   = $this->ajustaString($dadosTitulo->apgperiodo_referencia, 8, ' ');
                    $retorno['receitaBruta']      = $this->ajustaString($dadosTitulo->apgvalor_receita_bruta, 9, '0', 'E');
                    $retorno['percentual']        = $this->ajustaString($dadosTitulo->apgpercentual_receita_bruta, 4, '0', 'E');
                    $retorno['brancos1']          = $this->ajustaString('', 4, ' ');
                    $retorno['valorPrincipal']    = $this->ajustaString(substr($dadosTitulo->valor_titulo_equal_boleto, 0 , -1), 14, '0', 'E');
                    $retorno['multa']             = $this->ajustaString(substr($dadosTitulo->apgvl_multa, 0 , -1), 14, '0', 'E');
                    $retorno['juros']             = $this->ajustaString(substr($dadosTitulo->apgvl_juros, 0 , -1), 14, '0', 'E');
                    $retorno['valorTotal']        = $this->ajustaString(substr($dadosTitulo->valor_documento, 0 , -1), 14, '0', 'E');
                    $retorno['dataVencimento']    = $this->ajustaString($dadosTitulo->apgdt_vencimento, 8, ' ');
                    $retorno['dataPagamento']     = $this->ajustaString($dadosTitulo->apgdt_pagamento, 8, ' ');
                    $retorno['brancos2']          = $this->ajustaString('', 30, ' ');
                    $retorno['empresa']           = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecrazao), 30, ' ');
                break;
            case '13': //GARE – SP ICMS
                    $retorno['tributo']           = '05';
                    $retorno['codReceita']        = $this->ajustaString($dadosTitulo->apgcodigo_receita, 4, '0', 'E');
                    $retorno['tipoInscricao']     = '2'; //cnpj
                    $retorno['cnpjContribuinte']  = $this->ajustaString($dadosTitulo->apgcnpj_contribuinte, 14, '0', 'E');
                    $retorno['inscricaoEstadual'] = $this->ajustaString($dadosTitulo->apginscricao_estadual, 12, '0', 'E');
                    $retorno['dividaAtiva']       = $this->ajustaString($dadosTitulo->apgdivida_ativa, 13, ' ');
                    $retorno['referencia']        = $this->ajustaString($dadosTitulo->mesano_referencia, 6, ' ');
                    $retorno['parcela']           = $this->ajustaString($dadosTitulo->apgnum_parcela, 13, ' ');
                    $retorno['receita']           = $this->ajustaString(substr($dadosTitulo->valor_titulo_equal_boleto, 0 , -1), 14, '0', 'E');
                    $retorno['juros']             = $this->ajustaString(substr($dadosTitulo->apgvl_juros, 0 , -1), 14, '0', 'E');
                    $retorno['multa']             = $this->ajustaString(substr($dadosTitulo->apgvl_multa, 0 , -1), 14, '0', 'E');
                    $retorno['valorPagamento']    = $this->ajustaString(substr($dadosTitulo->valor_documento, 0 , -1), 14, '0', 'E');
                    $retorno['dataVencimento']    = $this->ajustaString($dadosTitulo->apgdt_vencimento, 8, ' ');
                    $retorno['dataPagamento']     = $this->ajustaString($dadosTitulo->apgdt_pagamento, 8, ' ');
                    $retorno['brancos1']          = $this->ajustaString('', 11, ' ');
                    $retorno['empresa']           = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecrazao), 30, ' ');
                break;
                case '09': //FGTS
                    $retorno['tributo']           = '11';
                    $retorno['codReceita']        = $this->ajustaString($dadosTitulo->apgcodigo_receita, 4, '0', 'E');
                    $retorno['tipoInscricao']     = '1'; //cnpj
                    $retorno['cnpjEmpresa']       = $this->ajustaString($parametros->dadosEmpresa->teccnpj, 14, ' ');
                    $retorno['codigoBarras']      = $this->ajustaString($dadosTitulo->apgcodigo_barras, 48, ' ');
                    $retorno['identificadorFGTS'] = $this->ajustaString($dadosTitulo->apgidentificador_fgts, 16, ' ');
                    $retorno['lacre']             = $this->ajustaString('', 9, ' ');
                    $retorno['digitoLacre']       = $this->ajustaString('', 2, ' ');
                    $retorno['empresa']           = $this->ajustaString($this->removerAcentos($parametros->dadosEmpresa->tecrazao), 30, ' ');
                    $retorno['dataPagamento']     = $this->ajustaString($dadosTitulo->apgdt_pagamento, 8, ' ');
                    $retorno['valorTotal']        = $this->ajustaString(substr($dadosTitulo->valor_documento, 0 , -1), 14, '0', 'E');
                    $retorno['brancos1']          = $this->ajustaString('', 30, ' ');
                break;
            default:
                $retorno['dadosTributo']          = $this->ajustaString('', 178, ' ');
                break;
        }
        $retorno['seuNumero']                     = $this->ajustaString($dadosTitulo->apgoid, 20, '0', 'E');
        $retorno['nossoNumero']                   = $this->ajustaString('', 15, ' ');
        $retorno['ocorrencias']                   = $this->ajustaString('', 10, ' ');

        return $retorno;
    }


    /**
     * Aplica regras do layout (Nota 11)
     * @param  string $banco   [description]
     * @param  string $agencia [description]
     * @param  string $conta   [description]
     * @param  string $digito  [description]
     * @return [type]          [description]
     */
    private function agenciaConta($banco, $agencia, $conta, $digito){

        $banco   = intval(trim(strval($banco)));
        $agencia = intval(trim(strval($agencia)));
        $conta   = intval(trim(strval($conta)));
        $digito  = intval(trim(strval($digito)));

        $retorno = "";

        if($banco == '341' || $banco == '409'){
            $retorno  = "0";
            $retorno .= $this->ajustaString($agencia, 4, '0', 'E');
            $retorno .= " ";
            $retorno .= "000000";
            $retorno .= $this->ajustaString($conta, 6, '0', 'E');
            if(strlen($digito) == 1){
                $retorno .= " ";
                $retorno .= $this->ajustaString($digito, 1, '0');
            }else{
                $retorno .= $this->ajustaString($digito, 2, '0', 'E');
            } 
        }else{
            $retorno .= $this->ajustaString($agencia, 5, '0', 'E');
            $retorno .= " ";
            $retorno .= $this->ajustaString($conta, 12, '0', 'E');
            if(strlen($digito) == 1){
                $retorno .= " ";
                $retorno .= $this->ajustaString($digito, 1, '0');
            }else{
                $retorno .= $this->ajustaString($digito, 2, '0', 'E');
            }
        }
        $retorno = $this->ajustaString($retorno, 20, '0');

        return $retorno;
    }


    /**
     * Retorna descricao da forma de pagamento
     * @param  int    $valor
     * @return string
     */
    private function formaRecebimento($cod){

        $retorno = array(
                        '0' => 'Cheque',
                        '1' => 'Crédito em C/C',
                        '2' => 'Crédito Conta/Salário',
                        '4' => 'Dinheiro',
                        '30'=> 'Boleto',
                        '31'=> 'Boleto'
                    );

        if(!array_key_exists($cod, $retorno)){
            return false;
        }

        return $retorno[$cod];

    } 

    /**
     * [tipoDocumento description]
     * @param  [type] $cod [description]
     * @return [type]      [description]
     */
    private function tipoDocumento($cod) {

        $retorno = array(
                '11' => 'Concessionária',
                '06' => 'DARF NORMAL',
                '12' => 'DARF SIMPLES',
                '04' => 'Duplicata',
                '02' => 'Fatura',
                '09' => 'FGTS',
                '03' => 'Nota Fiscal',
                '01' => 'Nota Fiscal/Fatura',
                '13' => 'GARE – SP ICMS',
                '10' => 'GNRE',                
                '07' => 'GPS',
                '08' => 'Guia de Recolhimento',
                '05' => 'Outros'
        );

        if(!array_key_exists($cod, $retorno)){
            return false;
        }

        return $retorno[$cod];
    }

    /**
     * [formaPagamento description]
     * @param  [type] $cod [description]
     * @return [type]      [description]
     */
    private function formaPagamento($cod) {

        $retorno = array(
                '01' => 'CRÉDITO EM CONTA CORRENTE NO ITAÚ',
                '02' => 'CHEQUE PAGAMENTO/ADMINISTRATIVO',
                '03' => 'DOC "C"',
                '05' => 'CRÉDITO EM CONTA POUPANÇA NO ITAÚ',
                '06' => 'CRÉDITO EM CONTA CORRENTE DE MESMA TITULARIDADE',
                '07' => 'DOC "D"',
                '10' => 'ORDEM DE PAGAMENTO À DISPOSIÇÃO',
                '13' => 'PAGAMENTO DE CONCESSIONÁRIAS',
                '16' => 'DARF NORMAL',
                '17' => 'GPS - GUIA DA PREVIDÊNCIA SOCIAL',
                '18' => 'DARF SIMPLES',
                '19' => 'IPTU/ISS/OUTROS TRIBUTOS MUNICIPAIS',
                '22' => 'GARE – SP ICMS',
                '25' => 'IPVA',
                '27' => 'DPVAT',
                '30' => 'PAGAMENTO DE TÍTULOS EM COBRANÇA NO ITAÚ',
                '31' => 'PAGAMENTO DE TÍTULOS EM COBRANÇA EM OUTROS BANCOS',
                '32' => 'NOTA FISCAL - LIQUIDAÇÃO ELETRÔNICA',
                '35' => 'FGTS',
                '41' => 'TED – OUTRO TITULAR',
                '43' => 'TED – MESMO TITULAR',
                '60' => 'CARTÃO SALÁRIO',
                '91' => 'GNRE E TRIBUTOS COM CÓDIGO DE BARRAS'
        );

        if(!array_key_exists($cod, $retorno)){
            return false;
        }

        return $retorno[$cod];
    }

    /**
     * Ajusta IDs conforme layout do Itaú (Nota 5)
     * @param  [type] $dadosTitulo  [description]
     * @param  [type] $dadosEmpresa [description]
     * @return [type]               [description]
     */
    private function defineFormaPagamentoItau($dadosTitulo, $dadosEmpresa){

        $idRetorno = '00';

        //Boletos
        if($dadosTitulo->apgforma_recebimento == '30' || $dadosTitulo->apgforma_recebimento == '31'){
            //Concessionárias
            if ($dadosTitulo->apgtipo_docto == '11') {
                $idRetorno = '13'; //PAGAMENTO DE CONCESSIONÁRIAS
            //DARF Normal
            }elseif ($dadosTitulo->apgtipo_docto == '06') {
                $idRetorno = '16'; //DARF NORMAL 
            //DARF Simples
            }elseif ($dadosTitulo->apgtipo_docto == '12') {
                $idRetorno = '18'; //DARF SIMPLES
            //FGTS
            }elseif ($dadosTitulo->apgtipo_docto == '09') {
                $idRetorno = '35'; //FGTS
            //GARE
            }elseif ($dadosTitulo->apgtipo_docto == '13') {
                $idRetorno = '22'; //GARE – SP ICMS
            //GNRE
            }elseif ($dadosTitulo->apgtipo_docto == '10') {
                $idRetorno = '91'; //GNRE E TRIBUTOS COM CÓDIGO DE BARRAS
            //GPS
            }elseif ($dadosTitulo->apgtipo_docto == '07') {
                $idRetorno = '17'; //GPS - GUIA DA PREVIDÊNCIA SOCIAL
            //Outros
            }elseif ($dadosTitulo->apgtipo_docto == '05') {

                $dadosCodigoBarras = $this->codigoBarrasTituloCobranca($dadosTitulo->apgcodigo_barras);
                if($dadosCodigoBarras->codBanco == '341'){ //Itau
                    $idRetorno = '30'; //PAGAMENTO DE TÍTULOS EM COBRANÇA NO ITAÚ
                }else{
                    $idRetorno = '31'; //PAGAMENTO DE TÍTULOS EM COBRANÇA EM OUTROS BANCOS
                }
            }   
        // Crédito em C/C
        }elseif ($dadosTitulo->apgforma_recebimento == '1') {

            // se for banco itaú
            if($dadosTitulo->forbanco == '341'){
                //se for poupança
                if($dadosTitulo->fortipo_conta == 'PO'){
                    $idRetorno = '05'; //CRÉDITO EM CONTA POUPANÇA NO ITAÚ
                }else{
                    //se é mesmo titular
                    if($dadosEmpresa->teccnpj == $dadosTitulo->fordocto){
                        $idRetorno = '06'; //CRÉDITO EM CONTA CORRENTE DE MESMA TITULARIDADE
                    }else{
                        $idRetorno = '01'; //CRÉDITO EM CONTA CORRENTE NO ITAÚ
                    }
                }
            }else{
                //se é mesmo titular
                if($dadosEmpresa->teccnpj == $dadosTitulo->fordocto){
                    $idRetorno = '43'; //TED – MESMO TITULAR
                }else{
                    $idRetorno = '41'; //TED – OUTRO TITULAR
                }
            }
        }
        return $idRetorno;
    }

    /**
     * ------------------------------
     * ---- FIM Layout CNAB Itaú ----
     * ------------------------------
     */

}
