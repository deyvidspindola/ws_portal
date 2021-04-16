<?php

/**
 * Classe RelFotosOrdemServico.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 * @author   Vinicius Senna <teste_desenv@sascar.com.br>
 * 
 */

require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

class RelFotosOrdemServico {

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
     * Mensagem de alerta para quando nenhum campo é preenchido
     * @const String
     */
    const MENSAGEM_ALERTA_CAMPOS_NAO_PREENCHIDOS = "Selecione ao menos um filtro de busca.";

    /**
     * Mensagem de alerta para o caso de nenhum filtro de busca preenchido
     * @const String
     */
    const MENSAGEM_FILTRO_BUSCA = "Selecione ao menos um filtro de busca.";

    /**
     * Mensagem para nenhum registro encontrado
     * @const String
     */
    const MENSAGEM_NENHUM_REGISTRO = "Nenhum registro encontrado.";

    /**
     *  Mensagem para o caso de não existir a ordem de servico
     *  @const String
     */
    const MENSAGEM_OS_INEXISTENTE = "A Ordem de Serviço informada não existe.";
    
    /**
     * Mensagem de erro para o processamentos dos dados
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

    /**
     * Mensagem de erro para o processamento do arquivo
     * @const String
     */
    const MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO = "Houve um erro no processamento do arquivo.";

    /**
     * Mensagem de alerta para quando a data inicial é maior que a final
     * @const String
     */
    const MENSAGEM_PERIODO_INVALIDO = "O período selecionado é inválido.";

    /**
     * Contém dados a serem utilizados na View.
     * 
     * @var stdClass 
     */
    private $view;

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

        //Campos do formulario
        $this->view->campos = null;

        $this->view->paginacao = null;
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
            if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' && $this->view->parametros->gera_csv == '') {
                $this->view->dados = $this->pesquisar($this->view->parametros);
            } else if($this->view->parametros->gera_csv != '') {
                // Realiza busca p/ CSV
                $this->view->arquivoCSV = $this->gerarCSV($this->view->parametros);
            }

        } catch (ErrorException $e) {
		
            $this->view->mensagemErro = $e->getMessage();
			
        } catch (Exception $e) {
		
            $this->view->mensagemAlerta = $e->getMessage();
			
        }
        
        //Inclui a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Relatorio/View/rel_fotos_ordem_servico/index.php";
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
            
            $this->view->paginacao = null;

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
		$this->view->parametros->ordoid = isset($this->view->parametros->ordoid) ? $this->view->parametros->ordoid : "" ; 	
        $this->view->parametros->data_inicial = isset($this->view->parametros->data_inicial) ? $this->view->parametros->data_inicial : "" ; 		
        $this->view->parametros->data_final = isset($this->view->parametros->data_final) ? $this->view->parametros->data_final : "" ; 		
        $this->view->parametros->gera_csv = isset($this->view->parametros->gera_csv) ? $this->view->parametros->gera_csv : "" ;
        $this->view->parametros->combo_visualizar = isset($this->view->parametros->combo_visualizar) ? $this->view->parametros->combo_visualizar : "" ;
        $this->view->parametros->os_sem_foto = isset($this->view->parametros->os_sem_foto) ? $this->view->parametros->os_sem_foto : "" ;

    }
    
    /**
     * Valida periodo entre datas
     * @param  [type] $dataInicial [description]
     * @param  [type] $dataFinal   [description]
     * @return boolean
     */
    private function validarPeriodo($dataInicial, $dataFinal){

        $dataIni = explode('-',$dataInicial);
        $dataFim = explode('-',$dataFinal);

        $dataIni = mktime(0,0,0,$dataIni[1],$dataIni[2],$dataIni[0]);
        $dataFim = mktime(0,0,0,$dataFim[1],$dataFim[2],$dataFim[0]);

        if($dataIni <= $dataFim) {
            return true;
        }

        return false;
    }


    /**
     * Transforma data
     * @param  [type] $data [description]
     * @return string
     */
    private function transformaData($data){
        return implode('-', array_reverse(explode('/', substr($data, 0, 10)))).substr($data, 10);
    }


    /**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        $parametros = clone $filtros;
        $paginacao = new PaginacaoComponente();

        if(isset($parametros->data_inicial) && $parametros->data_inicial != '' ) {
            $parametros->data_inicial = $this->transformaData($parametros->data_inicial);
        }

        if(isset($parametros->data_final) && $parametros->data_final != '' ){
            $parametros->data_final = $this->transformaData($parametros->data_final);
        }

        // Validacao
        $this->validarCamposPesquisa($parametros);

        $totalRegistros = $this->dao->pesquisar($parametros);

        $this->view->totalResultados = $totalRegistros[0]->total_registros;
        
        // Valida se houve resultado na pesquisa
        if (intval($totalRegistros[0]->total_registros) == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);

        $resultadoPesquisa = $this->dao->pesquisar($filtros, $paginacao->buscarPaginacao());

        $this->view->status = TRUE;
        
        return $resultadoPesquisa;
    }

    /**
     * Método usado para gerar o arquivo csv
     * @param  stdClass $filtros [description]
     * @return array            
     */
    private function gerarCSV(stdClass $filtros) {

        require_once "lib/Components/CsvWriter.php";
        $arquivo = 'relatorio_inst_'.date('Ymdhis').'.csv';
        $diretorio = '/var/www/docs_temporario/';

        $parametros = clone $filtros;

        if(isset($parametros->data_inicial) && $parametros->data_inicial != '' ) {
            $parametros->data_inicial = $this->transformaData($parametros->data_inicial);
        }

        if(isset($parametros->data_final) && $parametros->data_final != '' ){
            $parametros->data_final = $this->transformaData($parametros->data_final);
        }

        // Validacao
        $this->validarCamposPesquisa($parametros);

        try {
            if (is_dir($diretorio) && is_writable($diretorio)) {

                $dados = $this->dao->pesquisaCSV($parametros, false);
                
                if(count($dados) > 0) {

                    $csvWriter = new CsvWriter( $diretorio.$arquivo, ';', '', true);

                    $csvWriter->addLine(array(
                        'Ordem de serviço',
                        'Data',
                        'Contrato',
                        'Cliente',
                        'Modelo do equipamento',
                        'Marca',
                        'Modelo',
                        'Tipo de veículo',
                        'Ano',
                        'Chassi',
                        'Placa',
                        //'Tipo de contrato',
                        //'Cliente seguradora',
                        'Código representante',
                        'Representante',
                        'Instalador',
                        'UF',
                        'Data de conclusão',
                        'Classe',
                        'Envio de fotos'
                    ));

                    foreach($dados as $dado) {

                        $linha[0] = $dado->ordoid;
                        $linha[1] = date('d/m/Y',strtotime($dado->dt_criacao_os));
                        $linha[2] = $dado->ordconnumero;
                        $linha[3] = $dado->clinome;
                        $linha[4] = $dado->modelo_equipamento;
                        $linha[5] = $dado->marca;
                        $linha[6] = $dado->mlomodelo;
                        $linha[7] = $dado->tipo_veiculo;
                        $linha[8] = $dado->ano_veiculo;
                        $linha[9] = $dado->chassi;
                        $linha[10] = $dado->placa;
                        $linha[11] = $dado->repoid;
                        $linha[12] = $dado->repnome;
                        $linha[13] = $dado->nome_instalador;
                        $linha[14] = $dado->uf_representante;
                        $linha[15] = $dado->data_conclusao;
                        $linha[16] = $dado->classe;
                        $linha[17] = $dado->possui_fotografia;

                        $csvWriter->addLine($linha);
                    }

                    //Verifica se o arquivo foi gerado
                    $arquivoGerado = file_exists( $diretorio.$arquivo);
                    if ($arquivoGerado === false) {
                        throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                    }

                    return array('caminho' => $diretorio . $arquivo , 'arquivo' => $arquivo);
                }else {
                    throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
                }

            } 

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Valida campos da pesquisa
     * @param  stdClass $dados [description]
     * @return [type]          [description]
     */
    private function validarCamposPesquisa(stdClass $dados) {

        $camposDestaques = array();

        // Nenhum campo preenchido
        if(trim($dados->data_inicial) == '' && trim($dados->data_final) == ''
            && trim($dados->ordoid) == '' && trim($dados->combo_visualizar) == '' 
            && trim($dados->os_sem_foto) == '') {

            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_NAO_PREENCHIDOS);
            
        }

        // Trata data inicial/final não preenchida
        if((trim($dados->data_inicial) == '' || trim($dados->data_final) == '') 
            && trim($dados->ordoid) == '') {

            if(trim($dados->data_inicial) == '' && trim($dados->data_final) != '') {

                $camposDestaques[] = array('campo' => 'data_inicial');

            } else if(trim($dados->data_final) == '' && trim($dados->data_inicial) != '') {

                $camposDestaques[] = array('campo' => 'data_final');

            } else {
                $camposDestaques[] = array('campo' => 'data_inicial');
                $camposDestaques[] = array('campo' => 'data_final');

            }
            
            $this->view->campos = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        // Trata data final menor do que a data inicial
        if ( isset($dados->data_inicial) && trim($dados->data_inicial) != ''
            && isset($dados->data_final) && trim($dados->data_final) != '') {
            
            $flagPeriodo = $this->validarPeriodo($dados->data_inicial,$dados->data_final);
            
            if ($flagPeriodo == false) {

                $camposDestaques[] = array('campo' => 'data_inicial');
                $camposDestaques[] = array('campo' => 'data_final');

                $this->view->campos = $camposDestaques;
                throw new Exception(self::MENSAGEM_PERIODO_INVALIDO);
            }

        }


    }

}

