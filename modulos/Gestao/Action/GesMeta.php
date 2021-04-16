<?php

/**
 * Classe GesMeta.
 * Camada de regra de negócio.
 *
 * @package  Gestao
 * @author   José Fernando Carlos <jose.carlos@meta.com.br>
 * 
 */
class GesMeta {

    /**
     * Objeto DAO da classe.
     * 
     * @var CadExemploDAO
     */
    private $dao;

    private $layout;

    /**
     * Mensagem de alerta para campos obrigatórios não preenchidos
     * @const String
     */

    const CODIGO_MENSAGEM_ALERTA = 1;

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
    
    
    private $recarregarArvore = false;

    /**
     * Método construtor.
     * 
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function __construct($dao = null, $layout) {

        //Verifica o se a variável é um objeto e a instancia na atributo local
        if (is_object($dao)) {
            $this->dao = $dao;
        }

        $this->layout = $layout;

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

            $this->mensagens('pesquisar');

            // $this->view->parametros = $this->tratarParametros();

            // //Inicializa os dados
            // $this->inicializarParametros();

            // //Verificar se a ação pesquisar e executa pesquisa
            // if ( isset($this->view->parametros->acao) && $this->view->parametros->acao == 'pesquisar' ) {
            //     $this->view->dados = $this->pesquisar($this->view->parametros);
            // }


            if (isset($_POST['acao']) && trim($_POST['acao']) == 'pesquisar') {

                $this->limparSessaoPesquisa();

                $this->view->parametros = $this->tratarParametrosPesquisa();

                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);

            } else if (isset($_GET['acao']) && trim($_GET['acao']) == 'exportarPlanilha') {
                $this->view->status = $this->exportarPlanilha();
            } else if ($_SESSION['pesquisa']['usarSessao'] && $_GET['acao'] == 'pesquisar') {
                $this->view->parametros = (object) $_SESSION['pesquisa'];
                //aqui atribui a $this->resultadoPesquisa o resultado da pesquisa
                $this->view->dados = $this->pesquisar($this->view->parametros);

            } else {
                $this->limparSessaoPesquisa();
            }

        } catch (ErrorException $e) {
		
            $this->view->mensagemErro = $e->getMessage();
			
        } catch (Exception $e) {
		
            $this->view->mensagemAlerta = $e->getMessage();
			
        }
        $this->inicializarParametros();
        
        //Inclir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Gestao/View/ges_meta/index.php";
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
		$this->view->parametros->gmeoid                = isset($this->view->parametros->gmeoid) ? $this->view->parametros->gmeoid : "" ; 
        $this->view->parametros->gmeano                = isset($this->view->parametros->gmeano) ? trim($this->view->parametros->gmeano) : (intval(date('Y')) < 2014 ? '2014' : date(Y));
        $this->view->parametros->filtro_gmeano         = isset($this->view->parametros->filtro_gmeano) ? trim($this->view->parametros->filtro_gmeano) : (intval(date('Y')) < 2014 ? '2014' : date(Y));
        $this->view->parametros->gmenome               = isset($this->view->parametros->gmenome) ? trim($this->view->parametros->gmenome) : '';
        $this->view->parametros->filtro_cargo          = isset($this->view->parametros->filtro_cargo) ? trim($this->view->parametros->filtro_cargo) : '';
        $this->view->parametros->gmefunoid_responsavel = isset($this->view->parametros->gmefunoid_responsavel) ? trim($this->view->parametros->gmefunoid_responsavel) : '';
        $this->view->parametros->gmetipo               = isset($this->view->parametros->gmetipo) ? trim($this->view->parametros->gmetipo) : 'M';
        $this->view->parametros->gmemetrica            = isset($this->view->parametros->gmemetrica) ? trim($this->view->parametros->gmemetrica) : 'P';
        $this->view->parametros->gmepeso               = isset($this->view->parametros->gmepeso) ? trim($this->view->parametros->gmepeso) : 0;
        $this->view->parametros->gmeprecisao           = isset($this->view->parametros->gmeprecisao) ? trim($this->view->parametros->gmeprecisao) : 0;
        $this->view->parametros->gmedirecao            = isset($this->view->parametros->gmedirecao) ? trim($this->view->parametros->gmedirecao) : 'D';
        $this->view->parametros->gmeformula            = isset($this->view->parametros->gmeformula) ? trim($this->view->parametros->gmeformula) : '';
        $this->view->parametros->filtro_gmeoid         = isset($this->view->parametros->filtro_gmeoid) ? trim($this->view->parametros->filtro_gmeoid) : '';

        
        $this->view->parametros->listarMetas = array();
        //if (isset($_POST['gmeano']) && !empty($_POST['gmeano'])) {
            $this->view->parametros->listarMetas = $this->dao->buscarNomeMetas($this->view->parametros->gmeano);
        //}

        $this->view->parametros->listarCargos       = $this->dao->buscarCargos();

        $this->view->parametros->listarFuncionarios = $this->dao->buscarFuncionarios($this->view->parametros->gmeoid, '','','');
        $this->view->parametros->listarFuncionariosCadastro = $this->dao->buscarFuncionarios('','', '',$this->view->parametros->gmeano);
        $this->view->parametros->listarFuncionariosCadastroCompartilhamento = $this->dao->buscarFuncionarios('','', $this->view->parametros->gmefunoid_responsavel,$this->view->parametros->gmeano);
        
        $this->view->parametros->listarTipos        = array(
                                                            'D' => 'Diário',
                                                            'M' => 'Mensal',
                                                            'B' => 'Bimestral',
                                                            'T' => 'Trimestral',
                                                            'Q' => 'Quadrimestral',
                                                            'S' => 'Semestral',
                                                            'A' => 'Anual'
                                                     );

        $this->view->parametros->listarMetricas     = array(
                                                            'V' => 'Vlr',
                                                            'P' => '%',
                                                            'M' => '$'
                                                     );

        $this->view->parametros->listarDirecoes     = array(
                                                            'D' => 'Diretamente',
                                                            'I' => 'Inversamente'
                                                     );

        $this->view->parametros->listarIndicadores  = $this->dao->buscarIndicadores($this->view->parametros->gmetipo);
        
        $this->view->recarregarArvore = !isset($_GET['recarregarArvore']) ? false : (intval($_GET['recarregarArvore']) == 1);
        

    }
    

    /**
     * Responsável por tratar e retornar o resultado da pesquisa. 
     * 
     * @param stdClass $filtros Filtros da pesquisa
     * 
     * @return array
     */
    private function pesquisar(stdClass $filtros) {

        if ($this->validarCamposPesquisa($filtros)) {
            $resultadoPesquisa = $this->dao->pesquisar($filtros);
        }        

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
        
            $this->mensagens('cadastrar');

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

            if (isset($_GET['gmeoid']) && trim($_GET['gmeoid']) != '') {

                //Realiza o CAST do parametro
                $parametros->gmeoid = (int) $_GET['gmeoid'];
                
                //Pesquisa o registro para edição
                $this->view->parametros = $this->dao->pesquisarPorID($parametros->gmeoid);

                foreach($this->view->dados as $campo) {
                    $this->view->parametros->$campo['campo'] = $_POST[$campo['campo']];
                }

                $this->view->parametros->compartilhamento = $this->dao->buscarCompartilhamento($parametros->gmeoid);
                $this->view->parametros->gridIndicadores = $this->dao->buscarIndicadoresMetas($parametros->gmeoid);

                $this->view->parametros->gmelimite          = trim($this->view->parametros->gmelimite) != '' ? number_format($this->view->parametros->gmelimite,2,',','.') : '';
                $this->view->parametros->gmelimite_superior = trim($this->view->parametros->gmelimite_superior) != '' ? number_format($this->view->parametros->gmelimite_superior,2,',','.') : '';
                $this->view->parametros->gmelimite_inferior = trim($this->view->parametros->gmelimite_inferior) != '' ? number_format($this->view->parametros->gmelimite_inferior,2,',','.') : '';
                $this->view->parametros->somenteLeitura = true;

                if (trim($this->view->parametros->gmeformula) != '') {
                    $this->view->parametros->formulaSomenteLeitura = $this->verificarFormulaIndicadores($parametros->gmeoid,$this->view->parametros->gmeformula);    
                }
                $this->inicializarParametros();

            }

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            if (isset($_GET['gmeoid']) && trim($_GET['gmeoid']) != '') {

                //Realiza o CAST do parametro
                $parametros->gmeoid = (int) $_GET['gmeoid'];
                
                //Pesquisa o registro para edição
                $this->view->parametros = $this->dao->pesquisarPorID($parametros->gmeoid);

                foreach($this->view->dados as $campo) {
                    $this->view->parametros->$campo['campo'] = $_POST[$campo['campo']];
                }

                $this->view->parametros->compartilhamento = $this->dao->buscarCompartilhamento($parametros->gmeoid);
                $this->view->parametros->gridIndicadores = $this->dao->buscarIndicadoresMetas($parametros->gmeoid);

                $this->view->parametros->gmelimite          = trim($this->view->parametros->gmelimite) != '' ? number_format($this->view->parametros->gmelimite,2,',','.') : '';
                $this->view->parametros->gmelimite_superior = trim($this->view->parametros->gmelimite_superior) != '' ? number_format($this->view->parametros->gmelimite_superior,2,',','.') : '';
                $this->view->parametros->gmelimite_inferior = trim($this->view->parametros->gmelimite_inferior) != '' ? number_format($this->view->parametros->gmelimite_inferior,2,',','.') : '';
                $this->view->parametros->somenteLeitura = true;

                if (trim($this->view->parametros->gmeformula) != '') {
                    $this->view->parametros->formulaSomenteLeitura = $this->verificarFormulaIndicadores($parametros->gmeoid,$this->view->parametros->gmeformula);    
                }
                $this->inicializarParametros();

            }

            //Rollback em caso de erro
            $this->dao->rollback();

            $this->view->mensagemAlerta = $e->getMessage();
        }
        
        //Verifica se o registro foi gravado e chama a index, caso contrário chama a view de cadastro.
        if ($registroGravado !== false){
            $this->index();
        } else {          

            //@TODO: Montar dinamicamente o caminho apenas da view Index
            require_once _MODULEDIR_ . "Gestao/View/ges_meta/cadastrar.php";
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
            if (isset($parametros->gmeoid) && intval($parametros->gmeoid) > 0) {
                //Realiza o CAST do parametro
                $parametros->gmeoid = (int) $parametros->gmeoid;
                
                //Pesquisa o registro para edição
                $dados = $this->dao->pesquisarPorID($parametros->gmeoid);
                $dados->compartilhamento = $this->dao->buscarCompartilhamento($parametros->gmeoid);
                $dados->gridIndicadores = $this->dao->buscarIndicadoresMetas($parametros->gmeoid);

                $dados->gmelimite          = number_format($dados->gmelimite,2,',','.');
                $dados->gmelimite_superior = number_format($dados->gmelimite_superior,2,',','.');
                $dados->gmelimite_inferior = number_format($dados->gmelimite_inferior,2,',','.');
                $dados->somenteLeitura = true;

                if (trim($dados->gmeformula) != '') {
                    $dados->formulaSomenteLeitura = $this->verificarFormulaIndicadores($parametros->gmeoid,$dados->gmeformula);    
                }
				
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

        if ($dados->gmeoid > 0) {

            if ($this->dao->registroJaExcluido($dados->gmeoid)) {
                $this->redirect('alerta', 'Alteração não permitida. Essa meta está excluída.','editar&gmeoid='.$dados->gmeoid);
                exit();
            }

            //Efetua a gravação do registro
            $gravacao = $this->dao->atualizar($dados);    
            
            //Atualiza a arvore
            $this->recarregarArvore = TRUE;
            
            //Seta a mensagem de atualização
            $this->redirect('sucesso', self::MENSAGEM_SUCESSO_ATUALIZAR ,'cadastrar');
            //$this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
        } else {
            //Efetua a inserção do registro
            $gravacao = $this->dao->inserir($dados);
            $_SESSION['gestao']['meta']['id'] = $gravacao;
            
            //Atualiza a arvore
            $this->recarregarArvore = TRUE;

            $this->redirect('sucesso', self::MENSAGEM_SUCESSO_INCLUIR ,'cadastrar');
            //$this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
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
        $codigoAno = false;

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

        if (!isset($dados->gmenome) || trim($dados->gmenome) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmenome'
            );
            $error = true;
        }

        if (!isset($dados->gmefunoid_responsavel) || trim($dados->gmefunoid_responsavel) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmefunoid_responsavel'
            );
            $error = true;
        }

        if (trim($dados->gmeoid) == '')  {

            if (!isset($dados->gmecodigo) || trim($dados->gmecodigo) == '') {
                $camposDestaques[] = array(
                    'campo' => 'gmecodigo'
                );
                $error = true;
            } else {

                if (!$this->dao->verificarCodigo(trim($dados->gmecodigo), trim($dados->gmeano))) {

                    $camposDestaques[] = array(
                        'campo' => 'gmecodigo'
                    );
                    $camposDestaques[] = array(
                        'campo' => 'gmeano'
                    );
                    $codigoAno = true;

                }

            }

        }
        

        if (!isset($dados->gmelimite) || trim($dados->gmelimite) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmelimite'
            );
            $error = true;
        }

        if (!isset($dados->gmelimite_superior) || trim($dados->gmelimite_superior) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmelimite_superior'
            );
            $error = true;
        }

        if (!isset($dados->gmelimite_inferior) || trim($dados->gmelimite_inferior) == '') {
            $camposDestaques[] = array(
                'campo' => 'gmelimite_inferior'
            );
            $error = true;
        }

        $this->view->dados = $camposDestaques;
       

        if ($error) {            
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

        if ($codigoAno) {
            throw new Exception('Já existe uma meta cadastrada com o código ' . trim($dados->gmecodigo) . ' para o ano de ' . trim($dados->gmeano) . '.');
        }

        return true;
    }



    private function validarCamposPesquisa(stdClass $dados) {

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

        if (!isset($dados->filtro_gmeano) || trim($dados->filtro_gmeano) == '') {
            $camposDestaques[] = array(
                'campo' => 'filtro_gmeano'
            );
            $error = true;
        }

        $this->view->destaque = $camposDestaques;

        if ($error) {
            
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
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
            if (!isset($parametros->gmeoid) || trim($parametros->gmeoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
            
            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->gmeoid = (int) $parametros->gmeoid;
            
            //Remove o registro
            $confirmacao = $this->dao->excluir($parametros->gmeoid,$parametros->logica);

            //Comita a transação
            $this->dao->commit();

            if ($confirmacao) {

                $this->redirect('sucesso',self::MENSAGEM_SUCESSO_EXCLUIR,'pesquisar');

                //$this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;
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
        
        //$this->index();
    }


    public function copiar() {

        try {

            //Retorna os parametros
            $parametros = $this->tratarParametros();

            //Verifica se foi informado o id
            if (!isset($parametros->gmeoid) || trim($parametros->gmeoid) == '') {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }
            
            //Inicia a transação
            $this->dao->begin();

            //Realiza o CAST do parametro
            $parametros->gmeoid = (int) $parametros->gmeoid;
            

            $metaCopiada = $this->dao->pesquisarMetaCopiada($parametros->gmeoid);

            $codigo = $metaCopiada->gmecodigo;

            $verficar_codigo = explode('_cópia', $metaCopiada->gmecodigo);

            if (count($verficar_codigo) > 1) {

                $codigo = $verficar_codigo['0'];

            }

            $metaOriginal = $this->dao->pesquisarMetaOriginal($codigo);
            $metaCopias   = $this->dao->pesquisarMetaCopias($codigo);


            $copia_nome = "_cópia";

            if (intval($metaCopias) > 1) {
                $copia_nome = $copia_nome.($metaCopias - 1);
            }


            $metaCopia = $metaOriginal;
            $metaCopia->gmenome   = $metaCopia->gmenome . $copia_nome;
            $metaCopia->gmecodigo = $metaCopia->gmecodigo . $copia_nome;
            $metaCopia->gmeano    = date('Y');


            $metaCopia->gmelimite = number_format($metaCopia->gmelimite, 2, ',', '.');
            $metaCopia->gmelimite_superior = number_format($metaCopia->gmelimite_superior, 2, ',', '.');
            $metaCopia->gmelimite_inferior = number_format($metaCopia->gmelimite_inferior, 2, ',', '.');

            $metaCopia->meta_compartilhamento = '';


            $salvarCopia = $this->dao->inserir($metaCopia);

            if ($salvarCopia != false) {
                $this->dao->commit();
                $this->redirect('sucesso','Meta copiada com sucesso.','pesquisar');
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

    }

    public function verificarMeta() {

        try {

            $this->view->parametros = $this->tratarParametros();

            $dados['exclusao_logica'] = $this->dao->buscarRelacionamentosMeta($this->view->parametros->meta);

            echo json_encode($dados);
            exit;            

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            exit(0);
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            exit(0);
        }

    }


    public function buscarNomeMetas() {


        try {

            $this->view->parametros = $this->tratarParametros();

            $dados = $this->dao->buscarNomeMetas($this->view->parametros->ano);

            echo json_encode($dados);
            exit;            

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            exit(0);
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            exit(0);
        }
    }

    public function buscarFuncionarios() {


        try {

            $this->view->parametros = $this->tratarParametros();

            $this->view->parametros->meta = isset($this->view->parametros->meta) ? trim($this->view->parametros->meta) : '';
            $this->view->parametros->cargo = isset($this->view->parametros->cargo) ? trim($this->view->parametros->cargo) : '';
            $this->view->parametros->funcionario = isset($this->view->parametros->funcionario) ? trim($this->view->parametros->funcionario) : '';
            $this->view->parametros->ano = isset($this->view->parametros->ano) ? trim($this->view->parametros->ano) : '';

            $dados = $this->dao->buscarFuncionarios($this->view->parametros->meta, $this->view->parametros->cargo, $this->view->parametros->funcionario, $this->view->parametros->ano);

            echo json_encode($dados);
            exit;            

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            exit(0);
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            exit(0);
        }

    }


    public function buscarIndicadores() {

        try {

            $this->view->parametros = $this->tratarParametros();

            $dados = $this->dao->buscarIndicadores($this->view->parametros->tipo);

            echo json_encode($dados);
            exit;            

        } catch (ErrorException $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            exit(0);
        } catch (Exception $e) {

            //Rollback em caso de erro
            $this->dao->rollback();

            exit(0);
        }

    }



    public function verificarFormulaIndicadores($meta, $formula){


        $retorno = preg_match_all('/\[([A-Za-z0-9]+)\]/', $formula, $indicadores);
        $indicadores = array_unique($indicadores[1]);


        $indicadoresBloquiados = false;

        foreach ($indicadores as $indicador) {

            $indicadorVerificado = $this->dao->verificarIndicador($meta, $indicador);

            if ($indicadorVerificado === true) {
                $indicadoresBloquiados = true;
                break;
            }

        }   

        return $indicadoresBloquiados;
    }


    public function redirect($tipoMensagem = "", $mensagem = "", $acao = "") {
        if (trim($tipoMensagem) != '' && trim($mensagem) != '') {
            $_SESSION['flash_message'][$acao]['tipo'] = $tipoMensagem;
            $_SESSION['flash_message'][$acao]['mensagem'] = $mensagem;
        }

        if (trim($acao) != '') {
            
            $local = 'Location:ges_meta.php?acao=' . $acao;
            
            if ($this->recarregarArvore){
                $local .= '&recarregarArvore=1';
            }
            
            header($local);
        }

    }

    public function mensagens($acao) {

        if (isset($_SESSION['flash_message'][$acao]) && count($_SESSION['flash_message'][$acao])) {

            if ($_SESSION['flash_message'][$acao]['tipo'] == 'sucesso') {
                $this->view->mensagemSucesso = $_SESSION['flash_message'][$acao]['mensagem'];
            }

            if ($_SESSION['flash_message'][$acao]['tipo'] == 'erro') {
                $this->view->mensagemErro = $_SESSION['flash_message'][$acao]['mensagem'];
            }

            if ($_SESSION['flash_message'][$acao]['tipo'] == 'alerta') {
                $this->view->mensagemAlerta = $_SESSION['flash_message'][$acao]['mensagem'];
            }

            unset($_SESSION['flash_message'][$acao]);
            $this->view->parametros = '';
        }
    }

    public function limparSessao() {
        unset($_SESSION['gestao']['meta']['id']);
    }

    public function cadastrarPlanoAcao() {

        try {

            unset($_SESSION['gestao']['meta']['id']);

            $this->view->parametros = $this->tratarParametros();
            

            $meta = $this->dao->pesquisarPorID($this->view->parametros->metaId);
            $date = date('Y-m-d');

            $planoAcao = new stdClass();
            $planoAcao->gplgmeoid = $meta->gmeoid;
            $planoAcao->gplfunoid_responsavel = $meta->gmefunoid_responsavel;
            $planoAcao->gplnome = "PA_" . $meta->gmenome;
            $planoAcao->gpldt_inicio = date('d/m/Y',strtotime(date("Y-m-d", strtotime($date)) . " +1 month"));
            $planoAcao->gpldt_fim = date('d/m/Y',strtotime(date("Y-m-d", strtotime($date)) . " +1 month"));
            $planoAcao->gplstatus = 'A';
            $planoAcao->gplcompartilhar = 1;


            $acao = new stdClass();
            $acao->gmafunoid_responsavel = $meta->gmefunoid_responsavel;
            $acao->gmagploid = 0;
            $acao->gmanome = "Ação 1";
            $acao->gmatipo = "P";
            $acao->gmadt_inicio_previsto = date('d/m/Y',strtotime(date("Y-m-d", strtotime($date)) . " +1 month"));
            $acao->gmadt_fim_previsto = date('d/m/Y',strtotime(date("Y-m-d", strtotime($date)) . " +1 month"));
            $acao->gmastatus = "A";
            $acao->gmapercentual = 0;
            $acao->gmacompartilhar = 1;


            if ($this->dao->salvarPlanoEacao($planoAcao,$acao)) {
                $this->redirect('sucesso','Plano de ação e ação incluídos com sucesso.','cadastrar');
            }


            exit;       

        } catch (ErrorException $e) {
        
            $this->view->mensagemErro = $e->getMessage();
            
        } catch (Exception $e) {
        
            $this->view->mensagemAlerta = $e->getMessage();
            
        }


    }

    private function exportarPlanilha() {

        $this->inicializarParametros();

        $this->view->parametros->anoReferencia = intval($_GET['anoReferencia']);

        $configuracoes = new stdClass();
        $configuracoes->registro = 1000;
        $configuracoes->memoria = '12MB';
        $this->view->parametros->nomeArquivo = '/var/www/docs_temporario/export_metas_' . $this->view->parametros->anoReferencia . '.xlsx';

        try {

            $metas = $this->inicializarMetasExportacao($this->view->parametros->anoReferencia);
            
            $mesesMetas = $this->buscarMesesMetas($metas);

            $phpExcelCache = new stdClass();
            $phpExcelCache->method  = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
            $phpExcelCache->setting = array( 'dir'  => '/var/www/docs_temporario', 'memoryCacheSize' => $configuracoes->memoria );

            PHPExcel_Settings::setCacheStorageMethod($phpExcelCache->method, $phpExcelCache->setting);

            $PHPExcel = new PHPExcel();
            $PHPExcel->setActiveSheetIndex(0);

            $this->gravarCabecalhoPlanilha($PHPExcel, $mesesMetas);

            $linha = 2;
            if (count($metas) > 0) {

                ksort($metas);

                foreach ($metas as $meta) {

                    $somaPrevisto = 0;
                    $somaRealizado = 0;

                    // Mergeando celulas, aplicando estilos e setando o valor
                    $PHPExcel->getActiveSheet()->mergeCells('A' . $linha . ':A' . ($linha+1) );
                    $PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->setCellValue( "A" . $linha, utf8_encode( $meta['codigoMeta']));
                    
                    // Mergeando celulas, aplicando estilos e setando o valor
                    $PHPExcel->getActiveSheet()->mergeCells('B' . $linha . ':B' . ($linha+1) );
                    $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->setCellValue( "B" . $linha, utf8_encode( $meta['nomeMeta']));

                    // Mergeando celulas, aplicando estilos e setando o valor
                    $PHPExcel->getActiveSheet()->mergeCells('C' . $linha . ':C' . ($linha+1) );
                    $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $PHPExcel->getActiveSheet()->setCellValue( "C" . $linha, utf8_encode( $meta['reponsavel']));

                    $PHPExcel->getActiveSheet()->getStyle('D' . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $PHPExcel->getActiveSheet()->setCellValue( "D" . $linha, "Previsto");

                    $PHPExcel->getActiveSheet()->getStyle('D' . ($linha+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $PHPExcel->getActiveSheet()->setCellValue( "D" . ($linha+1), "Realizado");

                    $indice = ord('D'); // última coluna utilizada

                    foreach($meta['indicadores'] as $mes => $indicadoresMes) {

                        $previstoMes = 0;

                        // Preenche valores previstos
                        foreach ($indicadoresMes as $indicador) {
                            if ( $indicador->tipo_indicador == 'M' ) {
                                $previstoMes += $indicador->valor;
                            }
                        }

                        $valorRealizado = $this->calculaValorRealizadoMes($meta['formula'], $indicadoresMes);

                        if ( ( $valorRealizado == 0 && !is_string($valorRealizado) ) || $previstoMes == 0 ) {
                            continue;
                        }

                        $milhar = $meta['precisao'] == '0' && $previstoMes > 999 ? '' : ($meta['precisao'] == '0' && $previstoMes < 999 ? '.' : '');
                        $previstoMesPrint = number_format($previstoMes, $meta['precisao'],',',$milhar);
                        $PHPExcel->getActiveSheet()->getStyle(chr($indice+intval($mes)) . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $PHPExcel->getActiveSheet()->setCellValue( chr($indice+intval($mes)) . $linha, $previstoMesPrint);
                        $somaPrevisto += $previstoMes;

                        // Preenche valores realizados
                        $milhar = $meta['precisao'] == '0' && $valorRealizado > 999 ? '' : ($meta['precisao'] == '0' && $valorRealizado < 999 ? '.' : '');
                        $valorRealizadoPrint = (intval($valorRealizado) > 0) ? number_format($valorRealizado, $meta['precisao'],',',$milhar) : $valorRealizado;
                        $PHPExcel->getActiveSheet()->getStyle(chr($indice+intval($mes)) . ($linha+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        $PHPExcel->getActiveSheet()->setCellValue( chr($indice+intval($mes)) . ($linha+1), $valorRealizadoPrint);
                        $somaRealizado += intval($valorRealizado);
                    }


                    $indice += (count($mesesMetas)+1); // próxima coluna disponível
                    

                    //$acumulado = intval($meta['limite']) . "% (" . $somaPrevisto . ")";
                    $milhar = $meta['precisao'] == '0' && $somaPrevisto > 999 ? '' : ($meta['precisao'] == '0' && $somaPrevisto < 999 ? '.' : '');
                    $acumulado = number_format($somaPrevisto, $meta['precisao'],',',$milhar);
                    $PHPExcel->getActiveSheet()->getStyle(chr($indice) . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->setCellValue( chr($indice) . $linha, $acumulado);

                    $milhar = $meta['precisao'] == '0' && $somaRealizado > 999 ? '' : ($meta['precisao'] == '0' && $somaRealizado < 999 ? '.' : '');
                    $somaRealizado = number_format($somaRealizado, $meta['precisao'],',',$milhar);
                    $PHPExcel->getActiveSheet()->getStyle(chr($indice) . ($linha+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->setCellValue( chr($indice++) . ($linha+1), $somaRealizado);

                    //$limiteInferior = intval($meta['limite_inferior']) . "% (" . number_format(($meta['limite_inferior']/100)*$somaPrevisto, 2, ',', '.') . ")";
                    $limiteInferior = ($meta['limite_inferior']/100)*$somaPrevisto;
                    $milhar = $meta['precisao'] == '0' && $limiteInferior > 999 ? '' : ($meta['precisao'] == '0' && $limiteInferior < 999 ? '.' : '');
                    $limiteInferior = number_format($limiteInferior, $meta['precisao'], ',', $milhar);
                    $PHPExcel->getActiveSheet()->getStyle(chr($indice) . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->setCellValue( chr($indice++) . $linha, $limiteInferior);

                    //$limiteSuperior = intval($meta['limite_superior']) . "% (" . number_format(($meta['limite_superior']/100)*$somaPrevisto, 2, ',', '.') . ")";
                    $limiteSuperior = ($meta['limite_superior']/100)*$somaPrevisto;
                    $milhar = $meta['precisao'] == '0' && $limiteSuperior > 999 ? '' : ($meta['precisao'] == '0' && $limiteSuperior < 999 ? '.' : '');
                    $limiteSuperior = number_format($limiteSuperior, $meta['precisao'], ',', $milhar);
                    $PHPExcel->getActiveSheet()->getStyle(chr($indice) . $linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    $PHPExcel->getActiveSheet()->setCellValue( chr($indice) . $linha, $limiteSuperior);

                    $linha+=2; // incrementa em 2 linhas, pois há linhas mescladas

                }

            } else {
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO, self::CODIGO_MENSAGEM_ALERTA);
            }

            $objetoEscrita = new PHPExcel_Writer_Excel2007($PHPExcel);

            if(is_file($this->view->parametros->nomeArquivo)) {
                unlink($this->view->parametros->nomeArquivo);
            }

            $objetoEscrita->save($this->view->parametros->nomeArquivo);

        } catch (Exception $e) {

            if ($e->getCode() == self::CODIGO_MENSAGEM_ALERTA) {
                $this->view->mensagemAlerta = $e->getMessage();
            } else {
                $this->view->mensagemErro = $e->getMessage();
            }

            return false;
            
        }

        return true;
    }

    private function calculaValorRealizadoMes($formula, $indicadoresMes) {
        $valorRealizado = 0;

        if ( !preg_match('/.*;.*/', $formula) ) {
            foreach ($indicadoresMes as $indicador) {
                $formula = str_replace($indicador->codigo_indicador, $indicador->valor, $formula);
            }

            $formula = preg_replace("/\[.*?\]/", 0, $formula);

            $retorno = '';
            ob_start();
            $valorRealizado = eval("return " . $formula . ";");

            $retorno = ob_get_contents();
            ob_end_clean();

            if ( preg_match('/.*Warning.*/', $retorno) ) {
                return $formula;
            }
            

        }

        return floatval($valorRealizado);
    }

    private function gravarCabecalhoPlanilha($PHPExcel, $mesesMetas) {
        $formatacaoCabecalho = array(
            'font' => array( 
                'name' => 'Calibri',
                'size' => 11,
                'bold' => true,
                'color' => array( 'rgb' => 'FFFFFF')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => '17375d')
            )
        );

        $cabecalho = array();
        $cabecalho['A'] = "Código";
        $cabecalho['B'] = "Meta";
        $cabecalho['C'] = "Responsável";
        $cabecalho['D'] = "ano/" . $this->view->parametros->anoReferencia;
        $indice = ord('E'); // próxima coluna livre
        foreach ($mesesMetas as $mes) {
            $cabecalho[chr($indice++)] = $this->mesDecimalParaExtenso($mes);
        }
        $cabecalho[chr($indice++)] = "Acumulado";
        $cabecalho[chr($indice++)] = "Limite Inferior";
        $cabecalho[chr($indice++)] = "Limite Superior";

        $linha = 1;
        foreach($cabecalho as $letraColuna => $titulo){
            $PHPExcel->getActiveSheet()->getColumnDimension($letraColuna)->setAutoSize(true);
            $PHPExcel->getActiveSheet()->getStyle($letraColuna.$linha)->applyFromArray($formatacaoCabecalho);
            $PHPExcel->getActiveSheet()->setCellValue($letraColuna.$linha, utf8_encode($titulo));
        }

        return true;
    }

    private function inicializarMetasExportacao($anoReferencia) {

        $metas = array();

        $metasExportacao = $this->dao->buscarMetasExportacao($this->view->parametros->anoReferencia);

        // Formatando as metas
        foreach($metasExportacao as $meta) {

            $metas[$meta->nome_meta.'-'.$meta->nome_funcionario]['codigoMeta']                = $meta->codigo_meta;
            $metas[$meta->nome_meta.'-'.$meta->nome_funcionario]['nomeMeta']                  = $meta->nome_meta;
            $metas[$meta->nome_meta.'-'.$meta->nome_funcionario]['reponsavel']                = $meta->nome_funcionario;
            $metas[$meta->nome_meta.'-'.$meta->nome_funcionario]['formula']                   = $meta->formula;
            $metas[$meta->nome_meta.'-'.$meta->nome_funcionario]['precisao']                  = $meta->precisao;
            $metas[$meta->nome_meta.'-'.$meta->nome_funcionario]['limite']                    = $meta->limite;
            $metas[$meta->nome_meta.'-'.$meta->nome_funcionario]['limite_superior']           = $meta->limite_superior;
            $metas[$meta->nome_meta.'-'.$meta->nome_funcionario]['limite_inferior']           = $meta->limite_inferior;
            $metas[$meta->nome_meta.'-'.$meta->nome_funcionario]['indicadores'][$meta->mes][] = $meta;

        }

        // Removendo metas sem os indicadores da formula
        foreach ($metas as $meta) {
            $codigoIndicadoresFormula = array();
            $codigoIndicadoresMeta = array();

            // Busca codigo dos indicadores da formula
            preg_match_all("/(\[.+?\])/", $meta['formula'], $codigoIndicadoresFormula);
            $codigoIndicadoresFormula = array_unique($codigoIndicadoresFormula[1]);

            foreach ($meta['indicadores'] as $mes => $indicadoresMes) {
                // Busca codigo dos indicadores da meta para cada mês
                foreach ($indicadoresMes as $indicador) {
                    $codigoIndicadoresMeta[$mes][] = $indicador->codigo_indicador;
                }
                $codigoIndicadoresMeta[$mes] = array_unique($codigoIndicadoresMeta[$mes]);

                // Remove o mês caso não contenha os mesmos indicadores da formula
                if ( array_diff($codigoIndicadoresFormula, $codigoIndicadoresMeta[$mes]) ) {
                    unset($metas[$meta['nomeMeta'].'-'.$meta['reponsavel']]['indicadores'][$mes]);
                }
            }

            // Remove a meta caso não contenha indicadores
            if(!count($metas[$meta['nomeMeta'].'-'.$meta['reponsavel']]['indicadores'])) {
                unset($metas[$meta['nomeMeta'].'-'.$meta['reponsavel']]);
            }
        }

        return $metas;
    }

    // retorna os meses que serão utilizados nesta exportação
    private function buscarMesesMetas($metas) {

        $mesesMetas = array();

        foreach($metas as $meta) {
            foreach ($meta['indicadores'] as $mes => $indicadores) {
                array_push($mesesMetas, $mes);
            }
        }

        $mesesMetas = array_unique($mesesMetas);
        asort($mesesMetas);

        return $mesesMetas;
    }

    private function mesDecimalParaExtenso($mes) {

        switch ( intval($mes) ) {
            case 1:
                return 'Janeiro';
            case 2:
                return 'Fevereiro';
            case 3:
                return 'Março';
            case 4:
                return 'Abril';
            case 5:
                return 'Maio';
            case 6:
                return 'Junho';
            case 7:
                return 'Julho';
            case 8:
                return 'Agosto';
            case 9:
                return 'Setembro';
            case 10:
                return 'Outubro';
            case 11:
                return 'Novembro';
            case 12:
                return 'Dezembro';
            }
    }

    public function buscarMetasUsuario ($idUsuario) {
        return $this->dao->buscarMetasUsuario($idUsuario);
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     *
     * @return stdClass Parametros tradados
     *
     * @retrun stdClass
     */
    public function tratarParametrosPesquisa() {

        $temp = array();

        if (isset($_POST['acao']) && $_POST['acao'] = 'pesquisar') {
            foreach ($_POST as $key => $value) {
                if (isset($_POST[$key])) {
                    $temp[$key] = trim($_POST[$key]);
                } elseif (isset($_SESSION['pesquisa'][$key])) {
                    $temp[$key] = trim($_SESSION['pesquisa'][$key]);
                }
                $_SESSION['pesquisa'][$key] = $temp[$key];
            }
        }

        $_SESSION['pesquisa']['usarSessao'] = TRUE;

        return (object) $_SESSION['pesquisa'];
    }

    /**
    * Método responsável por limpar sessão de pesquisa
    *
    * @return void
    */
    public function limparSessaoPesquisa() {
        if (isset($_SESSION['pesquisa']) && is_array($_SESSION['pesquisa'])) {
            foreach ($_SESSION['pesquisa'] as $key => $value) {
                $_SESSION['pesquisa'][$key] = '';
            }
        }
    }

}

