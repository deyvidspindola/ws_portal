
<?php

/**
 * Classe RelObrigacaoFinanceiraReajusteHistorico.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 * @author   Vanessa Rabelo <vanessa.rabelo@meta.com.br>
 * 
 */

include "lib/Components/CsvWriter.php";
class RelObrigacaoFinanceiraReajusteHistorico {

    /**
     * Objeto DAO da classe.
     * 
     * @var CadExemploDAO
     */
    private $dao;

    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";

    /**
     * Mensagem de sucesso para inserção do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_INCLUIR = "Registro incluído com sucesso.";

    /**
     * Mensagem de sucesso para alteração do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_ATUALIZAR = "Registro alterado com sucesso.";

    /**
     * Mensagem de sucesso para exclusão do registro
     * @const String
     */
    const MENSAGEM_SUCESSO_EXCLUIR = "Registro excluído com sucesso.";

    /**
     * Mensagem para nenhum registro encontrado
     * @const String
     */
    const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";
    
    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
    /**
     * Contém dados a serem utilizados na View.
     * 
     * @var stdClass 
     */
    private $view;

    private $countRelatorio = 0;

    /**
     * Método construtor.
     * 
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

        //Verifica o se a variável é um objeto e a instancia na atributo local
        if (is_object($dao)) {
            $this->dao = $dao;
        }

        //Cria objeto da view
        $this->view = new stdClass();
        //Mensagem
        $this->view->mensagemErro = '';
        $this->view->mensagemAlerta = '';
        $this->view->mensagemSucesso = '';

        //Dados para view
        $this->view->dados = null;

        //Filtros/parametros utlizados na view
        $this->view->parametros = null;

        //Status de uma transação 
        $this->view->status = false;
    }

    /**
     * Método padrão da classe. 
     * 
     * Reponsável também por realizar a pesquisa invocando o método privado
     * 
     * @return void
     */
    public function index() {
        try {
            $this->view->parametros = $this->tratarParametros();

            //Inicializa os dados
            $this->inicializarParametros();


            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {               
                $this->view->dados = $this->pesquisar($this->view->parametros);
                $this->gerarCsv($this->view->dados);
            }


        } catch (ErrorException $e) {
        
            $this->view->mensagemErro = $e->getMessage();
            
        } catch (Exception $e) {
        
            $this->view->mensagemAlerta = $e->getMessage();
            
        }
        

        $tipoContrato = $this->dao->buscarTipoContrato();
        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index

        require_once _MODULEDIR_ . "Relatorio/View/rel_obrigacao_financeira_reajuste_historico/index.php";
    }


    public function buscarClienteNome() {
 
        $parametros = $this->tratarParametros();

        $parametros->tipo = trim($parametros->filtro) != '' ? trim($parametros->filtro) : '';
        $parametros->nome = trim($parametros->term) != '' ? trim($parametros->term) : '';

        $retorno = $this->dao->buscarClienteNome($parametros);

        echo json_encode($retorno);
        exit;
    }

    private function gerarCsv($dados) {
        //Diretório do Arquivo
        $caminho = '/var/www/docs_temporario/';
        
        //Nome do arquivo
        $nome_arquivo = 'RelatorioReajustesEfetuadosIGPM-INPC.csv';
        //Flag para identifica se o arquivo foi gerado
      
        $arquivo = false;
        
        if ( count($dados) > 0 ){
            
            //Verifica se o caminho existe
            if ( file_exists($caminho) ){
                // Gera CSV
                $csvWriter = new CsvWriter( $caminho.$nome_arquivo, ';', '', true);

                //Gera o cabeçalho 
                $cabecalho = array(
                    "Cliente ",
                    "Código Cliente ",
                	"Tipo Reajuste",
                   // "Número NF ",                    
                   // "Série NF",
                    "Contrato ",
                    "Data Início Vigência",
                    "Mês/Ano que ocorreu o Reajuste",
                    "Valor Com Reajuste",
                    "Valor Antigo",
                    "Obrigação Financeira Reajustada",
                    "Situação do Contrato",
                    "Tipo de Contrato",
                    "Equipamento"
                );
                
              
                //Adiciona o Cabeçalho
                $csvWriter->addLine( $cabecalho ); 
                
                
                //Adiciona os dados ao corpo do CSV
                if (count($dados)  > 0){

                    foreach ($dados as $key => $relatorio) {
                   
                        //Trata os dados                                  
                        $linha["clinome"]               = ( !empty($relatorio->clinome) )                ? $relatorio->clinome : ' ';
                        $linha["clioid"]                = ( !empty($relatorio->clioid) )                 ? $relatorio->clioid : ' ';
                      //  $linha["nflno_numero"]          = ( !empty($relatorio->nflno_numero) )           ? $relatorio->nflno_numero : ' ';
                      //  $linha["nflserie"]              = ( !empty($relatorio->nflserie) )               ? $relatorio->nflserie : ' ';
                        $linha["tp_reajuste"]           = ( !empty($relatorio->tp_reajuste) )            ? $relatorio->tp_reajuste : ' ';
                        $linha["ofrhconnumero"]         = ( !empty($relatorio->ofrhconnumero) )          ? $relatorio->ofrhconnumero :'';                        
                        $linha["condt_ini_vigencia"]    = ( !empty($relatorio->condt_ini_vigencia) )     ? $relatorio->condt_ini_vigencia : '';  
                        $linha["ofrhdt_referencia"]     = ( !empty($relatorio->ofrhdt_referencia) )      ? $relatorio->ofrhdt_referencia : ' ';
                        $linha["ofrhvalor_reajustado"]  = ( !empty($relatorio->ofrhvalor_reajustado) )   ? $relatorio->ofrhvalor_reajustado : ' ';
                        $linha["ofrhvalor_anterior"]    = ( !empty($relatorio->ofrhvalor_anterior) )     ? $relatorio->ofrhvalor_anterior : ' ';
                        $linha["obrobrigacao"]          = ( !empty($relatorio->obrobrigacao) )           ? $relatorio->obrobrigacao : ' ';
                        $linha["csidescricao"]          = ( !empty($relatorio->csidescricao) )           ? $relatorio->csidescricao : ' ';
                        $linha["tpcdescricao"]          = ( !empty($relatorio->tpcdescricao) )           ? $relatorio->tpcdescricao : ' ';
                        $linha["coneqcoid"]             = ( !empty($relatorio->coneqcoid) )              ? 'Instalado' : ' ';
                        
                        // Corpo do CSV
                        $csvWriter->addLine(
                                        
                            array(
                                $linha["clinome"],
                                $linha["clioid"],             
                              //  $linha["nflno_numero"],
                             //   $linha["nflserie"],    
                                $linha['tp_reajuste'],    
                                $linha["ofrhconnumero"],
                                $linha["condt_ini_vigencia"],
                                $linha["ofrhdt_referencia"],
                                $linha["ofrhvalor_reajustado"],
                                $linha["ofrhvalor_anterior"],                                
                                $linha["obrobrigacao"],
                                $linha["csidescricao"],
                                $linha["tpcdescricao"],
                                $linha["coneqcoid"] 
                            )
                        );
                    }

                } //IF Count do Relatório
                
            } //IF File_exists
            
            //Verifica se o arquivo foi gerado
            $arquivo = file_exists( $caminho.$nome_arquivo);
            //Lança uma exceção em caso de erro na geração do arquivo
            if ($arquivo === false){
                throw new Exception();
            } 
           
            
        } // ELSE Consulta
        //Se o arquivo foi gerado carrega a view para download do CSV
        if ( $arquivo === true ){
            $this->view->nome_arquivo = $nome_arquivo;
            return true;
        }

        return false;

    }    

 

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     * 
     * @return stdClass Parametros tradados
     * 
     * @retrun stdClass
     */
    private function tratarParametros() {
        $retorno = new stdClass();

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {
                $retorno->$key = isset($_POST[$key]) ? $value : '';
            }
        }
        
        if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {
                
                //Verifica se atributo já existe e não sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? $value : '';
                }
            }
        }
        return $retorno;
    }

    /**
     * Popula os arrays para os combos de estados e cidades
     * 
     * @return void
     */
    private function inicializarParametros() {
        
        //Verifica se os parametro existem, senão iniciliza todos
        $this->view->parametros->ofrhoid        = isset($this->view->parametros->ofrhoid) ? $this->view->parametros->ofrhoid : "" ;       
        $this->view->parametros->ofrhdt_inclusao = isset($this->view->parametros->ofrhdt_inclusao) ? 
        $this->view->parametros->ofrhdt_inclusao : "" ;        
        $this->view->parametros->ofrhdt_referencia = isset($this->view->parametros->ofrhdt_referencia) ? 
        $this->view->parametros->ofrhdt_referencia : "" ;      
        $this->view->parametros->ofrhusuoid_cadastro = isset($this->view->parametros->ofrhusuoid_cadastro) ? 
        $this->view->parametros->ofrhusuoid_cadastro : "" ;        
        $this->view->parametros->ofrhclioid = isset($this->view->parametros->ofrhclioid) ? $this->view->parametros->ofrhclioid : "" ;       
        $this->view->parametros->ofrhconnumero = isset($this->view->parametros->ofrhconnumero) ? $this->view->parametros->ofrhconnumero : "" ;      
        $this->view->parametros->ofrhtipo_reajuste = isset($this->view->parametros->ofrhtipo_reajuste) ? $this->view->parametros->ofrhtipo_reajuste : "" ;      
        $this->view->parametros->ofrhvl_referencia = isset($this->view->parametros->ofrhvl_referencia) ? $this->view->parametros->ofrhvl_referencia : "" ;     
        $this->view->parametros->ofrhobroid = isset($this->view->parametros->ofrhobroid) ? $this->view->parametros->ofrhobroid : "" ;      
        $this->view->parametros->ofrhnfloid = isset($this->view->parametros->ofrhnfloid) ? $this->view->parametros->ofrhnfloid : "" ;       
        $this->view->parametros->ofrhvalor_anterior = isset($this->view->parametros->ofrhvalor_anterior) ?  $this->view->parametros->ofrhvalor_anterior : "" ;       
        $this->view->parametros->ofrhvalor_reajustado = isset($this->view->parametros->ofrhvalor_reajustado) ? $this->view->parametros->ofrhvalor_reajustado : "" ;         
        $this->view->parametros->ofrhdt_inicio_cobranca = isset($this->view->parametros->ofrhdt_inicio_cobranca) ? $this->view->parametros->ofrhdt_inicio_cobranca : "" ;       
        $this->view->parametros->ofrhdt_fim_cobranca = isset($this->view->parametros->ofrhdt_fim_cobranca) ? $this->view->parametros->ofrhdt_fim_cobranca : "" ; 
       


        $this->view->parametros->tipo_contrato      = isset($this->view->parametros->tipo_contrato) ? $this->view->parametros->tipo_contrato : "" ;
        $this->view->parametros->clinome            = isset($this->view->parametros->clinome) ?  wordwrap($this->view->parametros->clinome,30,"<br />", true) : "" ;
        $this->view->parametros->dt_ini             = isset($this->view->parametros->dt_ini) ? $this->view->parametros->dt_ini : "" ;
        $this->view->parametros->dt_fim             = isset($this->view->parametros->dt_fim) ? $this->view->parametros->dt_fim : "" ;
        $this->view->parametros->cliente_id         = isset($this->view->parametros->cliente_id) ? $this->view->parametros->cliente_id : "" ;      
       
    }
    

    /**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisar($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;
        
        return $resultadoPesquisa;
    }

    /**
     * Responsável por receber exibir o formulário de cadastro ou invocar
     * o metodo para salvar os dados
     * 
     * @param stdClass $parametros Dados do cadastro, para edição (opcional)
     * 
     * @return void
     */
    public function cadastrar($parametros = null) {
        
        //identifica se o registro foi gravado
        $registroGravado = FALSE;
        try{
        
            if (is_null($parametros)) {
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            //Incializa os parametros
            $this->inicializarParametros();


            //Verificar se foi submetido o formulário e grava o registro em banco de dados 
            if (isset($_POST) && !empty($_POST)) {
                $registroGravado = $this->salvar($this->view->parametros);
            } 
        
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }
        
        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado){
            $this->index();
        } else {
            
            //@TODO: Montar dinamicamente o caminho apenas da view Index
            require_once _MODULEDIR_ . "Relatorio/View/rel_obrigacao_financeira_reajuste_historico/cadastrar.php";
        }
    }

    /**
     * Responsável por receber exibir o formulário de edição ou invocar
     * o metodo para salvar os dados
     * 
     * @return void
     */
    public function editar() {
        
        try {
            //Parametros 
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id do cadastro
            if (isset($parametros->ofrhoid) && intval($parametros->ofrhoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->ofrhoid = (int) $parametros->ofrhoid;
                
                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->ofrhoid);
                
                //Chama o metodo para edição passando os dados do registro por parametro.
                $this->cadastrar($dados);
            } else {
                $this->index();
            }
            
        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }
    }

    /**
     * Grava os dados na base de dados.
     * 
     * @param stdClass $dados Dados a serem gravados
     * 
     * @return void
     */
    private function salvar(stdClass $dados) {

        //Validar os campos
        $this->validarCamposCadastro($dados);

        //Inicia a transação
        $this->dao->begin();

        //Gravação
        $gravacao = null;

        if ($dados->ofrhoid > 0) {
            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);
            
            //Seta a mensagem de atualização
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {
            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
        }

        //Comita a transação
        $this->dao->commit();

        return $gravacao;
    }

    /**
     * Validar os campos obrigatórios do cadastro.
     * 
     * @param stdClass $dados Dados a serem validados
     * 
     * @throws Exception
     * 
     * @return void
     */
    private function validarCamposCadastro(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;


        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Executa a exclusão de registro.
     * 
     * @return void 
     */
    public function excluir() {
        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->ofrhoid) || trim($parametros->ofrhoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
            
            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->ofrhoid = (int) $parametros->ofrhoid;
            
            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->ofrhoid);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
            }
            
        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }
        
        $this->index();
    }


}

