<?php

require_once _MODULEDIR_.'Produto_Com_Seguro/Action/ProdutoComSeguro.php';

/**
 * Classe CadChamadasSeguradora.
 * Camada de regra de negócio.
 *
 * @package  Cadastro
 * @author   Vinicius Senna de Siqueira Nascimento <teste_desenv@sascar.com.br>
 * 
 */
class CadChamadasSeguradora {

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

    /**
     * Id do usuario logado
     * @var stdClass
     */
    private $usuoid;

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
        $this->view->mensagemInfo = 'Os campos com * são obrigatórios.';

        // Dados para view
        $this->view->dados = null;

        // Filtros/parametros utlizados na view
        $this->view->parametros = null;

        // Status de uma transação 
        $this->view->status = false;

        $this->usuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
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

            // Inicializa os dados
            $this->inicializarParametros($this->view->parametros->acao);

            // Popula combos do formulario
            $this->populaCombosFormulario();

            // Realiza chamada da cotacao
            if(isset($this->view->parametros->acao) && $this->view->parametros->acao == 'cotacao') {
                $this->view->dados = $this->cotacaoSeguradora($this->view->parametros);
            }

            // Realiza chamada da proposta
            if(isset($this->view->parametros->acao) && $this->view->parametros->acao == 'proposta' 
                && $this->view->parametros->origem == 'proposta') {
                $this->view->dados = $this->propostaSeguradora($this->view->parametros);
            }

            // Realiza chamada da apolice
            if(isset($this->view->parametros->acao) && $this->view->parametros->acao == 'apolice'
                && $this->view->parametros->origem == 'apolice') {
                $this->view->dados = $this->apoliceSeguradora($this->view->parametros);
            }

            // Acao para preencher os dados do cliente (ajax)
            if(isset($this->view->parametros->acao) && $this->view->parametros->acao == 'dados_cli') {
               $this->populaDadosCliente($this->view->parametros);
            }

        } catch (ErrorException $e) {
		
            $this->view->mensagemErro = $e->getMessage();
			
        } catch (Exception $e) {
		
            $this->view->mensagemAlerta = $e->getMessage();
			
        }
        
        //Incluir a view padrão
        //@TODO: Montar dinamicamente o caminho apenas da view Index
        require_once _MODULEDIR_ . "Cadastro/View/cad_chamadas_seguradora/index.php";
    }

    /**
     * Popula combos do formulario
     * @return [type] [description]
     */
    private function populaCombosFormulario() {

        $this->view->comboCombustivel = $this->dao->buscaCombustivel();
        $this->view->comboUsoVeiculo = $this->dao->buscaUsoVeiculo();
        $this->view->comboProfissoes = $this->dao->buscaProfissoesSeguradora();
        $this->view->comboFormaPagamento = $this->dao->buscaFormaPagamento();
        $this->view->comboIdRevenda = $this->dao->buscaIdRevenda();
        $this->view->comboCorretor = $this->dao->buscaCorretoresSeguro();
    }

    /**
     * Popula dados do cliente no momento que o numero do contrato é preenchido
     * @param  [type] $parametros [description]
     * @return [type]            [description]
     */
    private function populaDadosCliente(stdClass $parametros) {

        $result = $this->dao->buscaDadosClienteContrato($parametros->connumero);

        echo json_encode($result);
        exit;
    }

    /**
     * Realiza chamada para gerar apolice na seguradora
     * @param  stdClass $parametros [description]
     * @return [type]               [description]
     */
    private function apoliceSeguradora(stdClass $parametros) {

        $this->validarCamposApolice($parametros);

        // Flag de vigencia do contrato (caso for renovacao não atualiza inicio de vigencia)
        $flagVigencia = $parametros->renovacao_siggo == '2' ? true : false;

        // instancia da classe produto com seguro
        $produtoComSeguro = new ProdutoComSeguro();

        $produtoComSeguro->setContratoNumero($parametros->num_contrato_apo);
        $produtoComSeguro->setDataInstalacaoEquipamento($parametros->dt_insta_equipa);
        $produtoComSeguro->setDataAtivacaoEquipamento($parametros->dt_ativa_equipa);
        $produtoComSeguro->setClasseProduto($parametros->classe_produto_apo);
        //$produtoComSeguro->setCodigoRepresentante($parametros->cod_rep);
        $produtoComSeguro->setCodUsuarioLogado($this->usuoid);
        $produtoComSeguro->setNumOrdemServico($parametros->num_ord);
        // checkbox renovacao
        $produtoComSeguro->setFlagVigencia($flagVigencia);
        $produtoComSeguro->setOrigemChamada('rel_produto_com_seguro');
        $produtoComSeguro->setOrigemSistema('Intranet');
        
        $apolice = $produtoComSeguro->processarApolice();

        if($apolice['status'] == 'Erro') {

            foreach ($apolice['cod_msg'] as $key=> $cod){

                //recupera mensagem de erro na tabela produto_seguro_mensagens
                $msg_log = $produtoComSeguro->getMensagem($cod);

                if(is_object($msg_log)){
                    $this->view->mensagemAlerta .=  $msg_log->msg_sascar."\r\n";
                }

            }

        } else if($apolice != false) {
            
            $this->view->mensagemSucesso = 'Apólice processada com sucesso.';

            $apoliceGerada = $this->dao->recuperaApoliceGerada($parametros->num_contrato_apo);

            if($apoliceGerada) {
                $this->view->mensagemSucesso .= ' Apólice gerada:' .$apoliceGerada;
            }


        } else if($apolice == false) {
             $this->view->mensagemAlerta = "Não foi possível processar apólice.";
        }

    }

    /**
     * Realiza chamada para gerar porposta na seguradora
     * @return [type] [description]
     */
    private function propostaSeguradora(stdClass $parametros) {

        $this->validarCamposProposta($parametros);

        // instancia da classe produto com seguro
        $produtoComSeguro = new ProdutoComSeguro();
        
        $produtoComSeguro->setCotacaoNumero($parametros->num_cotacao);
        $produtoComSeguro->setContratoNumero($parametros->num_contrato);
        $produtoComSeguro->setClienteNome(addslashes($parametros->nome_cliente));
        $produtoComSeguro->setClienteSexo($parametros->sexo);
        $produtoComSeguro->setClienteEstadoCivil($parametros->estado_civil);
        $produtoComSeguro->setClienteProfissao($parametros->profissa);
        $produtoComSeguro->setClienteDataNascimento($parametros->dt_nasc);
        $produtoComSeguro->setClientePep1(addslashes($parametros->pep1));
        $produtoComSeguro->setClientePep2(addslashes($parametros->pep2));
        $produtoComSeguro->setClienteResidencialDdd($parametros->ddd_res);
        $produtoComSeguro->setClienteResidencialFone($parametros->fone_res);
        $produtoComSeguro->setClienteCelularDdd($parametros->ddd_cel);
        $produtoComSeguro->setClienteCelularFone($parametros->num_cel);
        $produtoComSeguro->setClienteEmail(addslashes($parametros->email));
        $produtoComSeguro->setClienteEndereco(addslashes($parametros->endereco));
        $produtoComSeguro->setClienteEnderecoNumero($parametros->endereco_num);
        $produtoComSeguro->setClienteComplemento(addslashes($parametros->complemento));
        $produtoComSeguro->setClienteCidade(addslashes($parametros->cidade));
        $produtoComSeguro->setClienteUf($parametros->uf);
        $produtoComSeguro->setVeiculoPlaca(addslashes($parametros->placa));
        $produtoComSeguro->setVeiculoChassi(addslashes($parametros->chassi));
        $produtoComSeguro->setVeiculoUtilizacao($parametros->uti_vei);
        $produtoComSeguro->setClienteSeguroTipo($parametros->tipo_seguro);
        $produtoComSeguro->setFormaPagamento($parametros->forma_pag);
        $produtoComSeguro->setClasseProduto($parametros->classe_produto_prop);

        $produtoComSeguro->setCorretor($parametros->id_corretor_intranet);

        //chamada do método para processar a proposta do seguro
        $proposta = $produtoComSeguro->processarProposta();

         if($proposta['status'] == 'Erro') {

            if(is_array($proposta['mensagem'])) {
                foreach ($proposta['mensagem'] as $dadosProposta) {
                    $this->view->mensagemAlerta .= $dadosProposta . '<br />';
                }
            } else {
                $this->view->mensagemAlerta = (string) $proposta['mensagem'];
            }

        } else if ($proposta['status'] == 'Sucesso') {
            $this->view->mensagemSucesso = (string) $proposta['mensagem'];

            if(isset($proposta['proposta_numero']) && trim($proposta['proposta_numero']) != '') {
                // Concatena o numero da proposta na mensagem de sucesso.
                $this->view->mensagemSucesso .= '. Número da proposta: '.$proposta['proposta_numero'];

                // Preenche campos do numero do contrato e classe do produto
                $this->view->parametros->num_contrato_apo = $parametros->num_contrato;
                $this->view->parametros->classe_produto_apo = $parametros->classe_produto_prop;

                // Seta acao p/ cair na aba de apolice
                $this->view->parametros->acao = 'apolice';
            } 
        }

    }

    /**
     * Realiza a chamada para gerar cotacao na seguradora
     * @param  [type] $parametros [description]
     * @return [type]             [description]
     */
    private function cotacaoSeguradora(stdClass $parametros) {

        $this->validarCamposCotacao($parametros);

        // instancia da classe produto com seguro
        $produtoComSeguro = new ProdutoComSeguro();

        $erro = false;

        $produtoComSeguro->setTipoPessoa($parametros->tipo_pessoa_cotacao);  //1= fisico   2= juridico
        $produtoComSeguro->setCpf_cgc($parametros->cpf_cnpj);
        $produtoComSeguro->setCep($parametros->cep);
        $produtoComSeguro->setCodigo_fipe($parametros->cod_fipe);
        $produtoComSeguro->setAno_modelo($parametros->ano_modelo);
        $produtoComSeguro->setCarro_zero($parametros->novo_usado); // (1 = novo , 2 = usado)
        $produtoComSeguro->setTipo_combustivel($parametros->combustivel);
        $produtoComSeguro->setUso_veiculo($parametros->uso_veiculo);
        $produtoComSeguro->setFinalidade_uso_veiculo($parametros->finalidade_uso);
        $produtoComSeguro->setClasseProduto($parametros->classe_produto);
        $produtoComSeguro->setCorretor($parametros->id_revenda);

        $cotacao = $produtoComSeguro->processarCotacao();

        if($cotacao['status'] == 'Erro') {

            if(is_array($cotacao['mensagem'])) {
                foreach ($cotacao['mensagem'] as $dadosCotacao) {
                    $this->view->mensagemAlerta .= $dadosCotacao . '<br />';
                }
            } else {
                $this->view->mensagemAlerta = (string) $cotacao['mensagem'];
            }

        } else if ($cotacao['status'] == 'Sucesso') {
            $this->view->mensagemSucesso = (string) $cotacao['mensagem'];

            if(isset($cotacao['orcamento_numero']) && trim($cotacao['orcamento_numero']) != '') {
                //$this->view->mensagemSucesso .= '. Orçamento número: '.$cotacao['orcamento_numero'];

                $this->view->parametros->acao = 'proposta';

                $this->view->parametros->num_cotacao = $cotacao['orcamento_numero'];

                $this->view->parametros->id_corretor_intranet = $this->dao->buscaIdCorretor($parametros->id_revenda);
            } 
        }

    }

    /**
     * Valida campos cotacao
     * @param  stdClass $dados [description]
     * @return [type]          [description]
     */
    private function validarCamposCotacao(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        foreach($dados as $key=>$value) {
            
            if(trim($value) == '') {

                // Se o uso do veiculo for "Uso profissional", é obrigatório que
                // o campo finalidade_uso seja preenchidos
                if(isset($dados->uso_veiculo) && $dados->uso_veiculo != '' 
                    && $key == 'finalidade_uso' && $dados->uso_veiculo == '9') {

                    $camposDestaques[] = array(
                        'campo' => $key
                    );

                    $error = true;

                } else if($key != 'finalidade_uso') {

                    $camposDestaques[] = array(
                        'campo' => $key
                    );

                    $error = true;
                }
                
            }

            if($key == 'cod_fipe' && $value == '0') {
                
                $camposDestaques[] = array(
                    'campo' => $key
                );

                $error = true;
            }
            
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }

    }

    /**
     * Valida os campos do formulario de proposta
     * @param  stdClass $dados [description]
     * @return [type]          [description]
     */
    private function validarCamposProposta(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        foreach($dados as $key=>$value) {

            if(trim($value) == '' && $key != 'complemento') {

                $camposDestaques[] = array(
                    'campo' => $key
                );

                $error = true;
            }
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Valida campos formulario de apolice
     * @param  stdClass $dados [description]
     * @return [type]          [description]
     */
    private function validarCamposApolice(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        //Verifica se houve erro
        $error = false;

        foreach($dados as $key=>$value) {

            if(trim($value) == '') {

                $camposDestaques[] = array(
                    'campo' => $key
                );

                $error = true;
            }
        }

        if ($error) {
            $this->view->dados = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     * 
     * @return stdClass Parametros tratados
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
     * Inicializa parametros
     * 
     * @return void
     */
    private function inicializarParametros() {
        
        //Verifica se os parametro existem, senão iniciliza todos
        if(isset($this->view->parametros->acao) && $this->view->parametros->acao == 'cotacao') {

            $pattern = '/[^0-9]/';
            $replacement = '';

            $this->view->parametros->combustivel = isset($this->view->parametros->combustivel) ? $this->view->parametros->combustivel : "" ;
            $this->view->parametros->uso_veiculo = isset($this->view->parametros->uso_veiculo) ? $this->view->parametros->uso_veiculo : "" ;

            $this->view->parametros->cpf_cnpj = isset($this->view->parametros->cpf_cnpj) ? 
                                                    preg_replace($pattern, $replacement, $this->view->parametros->cpf_cnpj) : "" ;

            $this->view->parametros->cep = isset($this->view->parametros->cep) ? 
                                                    preg_replace($pattern, $replacement, $this->view->parametros->cep) : "" ;
        }

        foreach ($this->view->parametros as $key => $value) {

            $this->view->parametros->$key = trim($value);

        }

    }

}

