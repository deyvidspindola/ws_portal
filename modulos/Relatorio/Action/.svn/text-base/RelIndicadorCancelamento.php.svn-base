<?php

/**
 * Classe RelIndicadorCancelamento.
 * Camada de regra de negócio.
 *
 * @package  Relatorio
 * @author   GABRIEL PEREIRA <gabriel.pereira@meta.com.br>
 * 
 */
class RelIndicadorCancelamento {

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

    public $grafico_contratos;

    public $grafico_sugestoes;

    /**
     * Método construtor.
     * 
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

        $this->grafico_contratos = "images/grafico/grafico_contratos_" . md5(date('H:i:s')) . ".jpg";
        $this->grafico_sugestoes = "images/grafico/grafico_sugestoes_" . md5(date('H:i:s')) . ".jpg";


        $this->dao = $dao;

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

            if (isset($_GET['cliente_id']) && trim($_GET['cliente_id']) != '') {
                $this->view->parametros->nome_cliente = $this->dao->buscarClienteNomeId($_GET['cliente_id']);
            }

            //Inicializa os dados
            $this->inicializarParametros();

            //Verificar se a ação pesquisar e executa pesquisa
            if ( isset($this->view->parametros->sub_acao) && $this->view->parametros->sub_acao == 'pesquisar' ) {

                if ($this->validarCamposPesquisa($this->view->parametros)) {

                    $dadosContratos = $this->pesquisarContratos($this->view->parametros);
                    $dadosClasse= $this->pesquisarClasseTermo($this->view->parametros);
                    $dadosSugestoesReclamacoes = $this->pesquisarSugestoesReclamacoes($this->view->parametros);    

                    $dadosViews['dadosContratos'] = $dadosContratos;
                    $dadosViews['dadosClasse'] = $dadosClasse;
                    $dadosViews['dadosSugestoesReclamacoes'] = $dadosSugestoesReclamacoes;


                    if (count($dadosViews['dadosContratos']) > 0) {
                        $dadosViews['graficoContratos'] = $this->gerarGraficoContratos($dadosViews['dadosContratos']);
                    }

                    if (count($dadosViews['dadosSugestoesReclamacoes']) > 0) {
                        $dadosViews['graficoSugestoesReclamacoes'] = $this->gerarGraficoSugestoesReclamacoes($dadosViews['dadosSugestoesReclamacoes']);
                    }


                    $this->view->nadaEncontrado = false;

                    if (count($dadosViews['dadosContratos']) == 0 && count($dadosViews['dadosSugestoesReclamacoes']) == 0) {

                        $this->view->nadaEncontrado = true;

                        throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
                    }
                    
                    $this->view->dados = $dadosViews;
                }
                
            }

        } catch (ErrorException $e) {
		
            $this->view->mensagemErro = $e->getMessage();
			
        } catch (Exception $e) {
		
            $this->view->mensagemAlerta = $e->getMessage();
			
        }
        
        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Relatorio/View/rel_indicador_cancelamento/index.php";
    }


    public function gerarGraficoContratos(array $dados) {

        $sitedir = strpos($_SERVER['HTTP_HOST'], '10.20.12.') === false ? '' : _SITEDIR_.'/lib/php5-jpgraph/';
        
        require_once $sitedir.'jpgraph.php';
        require_once $sitedir.'jpgraph_pie.php';

        /*
         * PERMISSÕES
         */
        chmod(_SITEDIR_.'images/grafico', 0777);
               

        // Some data

        $labels = array();
        $data = array();
        $percentual = array();
        foreach ($dados as $key => $value) {
            $labels[] = truncate($value->status, 40 , '');
            $data[] = $value->qtd;
            //$percent = str_replace(',', ',', $value->porcentagem);
            //$percentual[] = $percent;
            $percentual[] = $value->porcentagem;
        }

        $qtdDados = count($labels);

        if ($qtdDados > 5) {

            $heightGrafico     = 270 + (10 * $qtdDados);
            $topCenterGrafico  = 0.4;

        } else {


            $heightGrafico     = 270;
            $topCenterGrafico  = 0.54;


        }


        // Create the Pie Graph. 
        $graph = new PieGraph(588,$heightGrafico);


        $theme_class="DefaultTheme";
        //$graph->SetTheme(new $theme_class());

        // Set A title for the plot
        $graph->title->Set("Status de Contratos");
        //$graph->SetBox(true);

        $graph->legend->SetPos(0.019,0.15,'right','top');

        // Create
        $p1 = new PiePlot($data);
        $p1->SetSize(100);
        $p1->SetCenter(0.27,$topCenterGrafico);
        $graph->Add($p1);

        $p1->ShowBorder();
        $p1->SetColor('black');
        $p1->SetLabels($percentual,1);
        $p1->SetLegends($labels);

                
        $p1->value->SetFormatCallback('formataValor');

        $graph->Stroke(_SITEDIR_ . $this->grafico_contratos);

        return $this->grafico_contratos;

    }



    public function gerarGraficoSugestoesReclamacoes(array $dados) {

        $sitedir = strpos($_SERVER['HTTP_HOST'], '10.20.12.') === false ? '' : _SITEDIR_.'/lib/php5-jpgraph/';
        
        require_once $sitedir.'jpgraph.php';
        require_once $sitedir.'jpgraph_pie.php';

        /*
         * PERMISSÕES
         */
        chmod(_SITEDIR_.'images/grafico', 0777);


        // Some data

        $labels = array();
        $data = array();
        $percentual = array();
        foreach ($dados['grafico'] as $key => $value) {
            $labels[] = truncate($value->trsdescricao, 40, '');
            $data[] = $value->total;
            $percentual[] = $value->porcentagem;
        }


        $qtdDados = count($labels);

        if ($qtdDados > 5) {

            $heightGrafico     = 270 + (10 * $qtdDados);
            $topCenterGrafico  = 0.4;

        } else {


            $heightGrafico     = 270;
            $topCenterGrafico  = 0.54;


        }

        // Create the Pie Graph. 
        $graph = new PieGraph(588,$heightGrafico);


        $theme_class="DefaultTheme";
        //$graph->SetTheme(new $theme_class());

        // Set A title for the plot
        $graph->title->Set("Sugestão/Reclamação de Cliente");
        //$graph->SetBox(true);

        $graph->legend->SetPos(0.019,0.15,'right','top');

        // Create
        $p1 = new PiePlot($data);
        $p1->SetSize(100);
        $p1->SetCenter(0.27,$topCenterGrafico);
        $graph->Add($p1);

        $p1->ShowBorder();
        $p1->SetColor('black');
        $p1->SetLabels($percentual,1);
        $p1->SetLegends($labels);        

        $p1->value->SetFormatCallback('formataValor');

        $graph->Stroke(_SITEDIR_ . $this->grafico_sugestoes);

        return $this->grafico_sugestoes;

    }


    public function buscarMotivos() {

        $this->view->parametros = $this->tratarParametros();
        
        //Inicializa os dados
        $this->inicializarParametros();

        $retorno = $this->dao->buscarMotivos($this->view->parametros);

        echo json_encode($retorno);

        exit;
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
		$this->view->parametros->cliente_id = isset($this->view->parametros->cliente_id) ? $this->view->parametros->cliente_id : "" ;
        $this->view->parametros->data_de = isset($this->view->parametros->data_de) ? $this->view->parametros->data_de : date('d/m/Y', strtotime(" -12 months ")) ; 
        $this->view->parametros->data_ate = isset($this->view->parametros->data_ate) ? $this->view->parametros->data_ate : date('d/m/Y') ;
        $this->view->parametros->nome_cliente = isset($this->view->parametros->nome_cliente) ? $this->view->parametros->nome_cliente : '' ;  

    }
    

    /**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisarContratos(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisarContratos($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            //throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;
        
        return $resultadoPesquisa;
    }

    private function pesquisarClasseTermo(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisarClasseTermo($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
            //throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        $this->view->status = TRUE;
        
        return $resultadoPesquisa;

    }

    private function pesquisarSugestoesReclamacoes(stdClass $filtros) {

        $resultadoPesquisa = $this->dao->pesquisarSugestoesReclamacoes($filtros);

        //Valida se houve resultado na pesquisa
        if (count($resultadoPesquisa) == 0) {
           // throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
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
            require_once _MODULEDIR_ . "Relatorio/View/rel_indicador_cancelamento/cadastrar.php";
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
            if (isset($parametros->clioid) && intval($parametros->clioid) > 0) {
                //Realiza o CAST do parametro
                $parametros->clioid = (int) $parametros->clioid;
                
                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->clioid);
				
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

        if ($dados->clioid > 0) {
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

        /**
         * Verifica os campos obrigatórios
         */
        /** Ex.:
        if (!isset($dados->excnome) || trim($dados->excnome) == '') {
            $camposDestaques[] = array(
                'campo' => 'excnome'
            );
            $error = true;
        }
		*/

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Description
     * @param type stdClass $parametros 
     * @return type
     */
    private function validarCamposPesquisa(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $obrigatoriosInformados = true;
        $clienteOk = true;

        if (!isset($dados->data_de) || trim($dados->data_de) == '') {
            $camposDestaques[] = array(
                'campo' => 'data_de'
            );
            $obrigatoriosInformados = false;
        }

        if (!isset($dados->data_ate) || trim($dados->data_ate) == '') {
            $camposDestaques[] = array(
                'campo' => 'data_ate'
            );
            $obrigatoriosInformados = false;
        }

        if (!isset($dados->nome_cliente) || trim($dados->nome_cliente) == '') {
             $camposDestaques[] = array(
                'campo' => 'nome_cliente'
            );
            $obrigatoriosInformados = false;
        }


        if (!isset($dados->cliente_id) || trim($dados->cliente_id) == '') {            
            $clienteOk = false;
        }

        $this->view->dados = $camposDestaques;

        if (!$obrigatoriosInformados) {            
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        if (!$clienteOk) {
            throw new Exception($dados->nome_cliente . ' não consta no cadastro.');
        }

        return true;

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
            if (!isset($parametros->clioid) || trim($parametros->clioid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
            
            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->clioid = (int) $parametros->clioid;
            
            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->clioid);

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

    /**
     * Buscar cliente por nome sendo ele PJ || PF
     * 
     * @return array $retorno
     */
    public function buscarClienteNome() {

        $parametros = $this->tratarParametros();

        $parametros->tipo = trim($parametros->filtro) != '' ? trim($parametros->filtro) : '';
        $parametros->nome = trim($parametros->term) != '' ? trim($parametros->term) : '';

        $retorno = $this->dao->buscarClienteNome($parametros);

        echo json_encode($retorno);
        exit;
    }


}

function formataValor($valor){
    //$valor = number_format($valor, 1, ',', '.'); (ARRENDONDA A PORRA DO VALOR)
    $valor = str_replace('.', ',', $valor); //NAO ARREDONDA 
    return $valor . '%';
}

function truncate($text, $chars = 40, $ext="...") {

    if (strlen($text) > $chars) {
        $text = $text." ";
        $text = substr($text,0,$chars);
        //$text = substr($text,0,strrpos($text,' '));
        $text = $text.$ext;
    } 
    return $text;
}