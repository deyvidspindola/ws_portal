<?php

require _MODULEDIR_ . 'Financas/DAO/FinCreditoFuturoParametrizacaoCampanhaDAO.class.php';

/**
 * - Cadastro de Campanha Promocional
 * @author Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 * @package Finanças
 * @since 03/07/2013
 *
 */
class  FinCreditoFuturoParametrizacaoCampanha {

    /**
     * Define se requisicoes post padrao serao feitas por "submit(false)" ou "ajax(true)"
     * @var boolean
     */
    private $ajaxPost = false;

    /**
     * Regorno de mensagens
     * @var object
     */
    private $retorno;

    /**
     * Parametros especificos para a tela de pesquisa
     * @var object:Parametros
     */
    private $parametroPesquisa;

    /**
     * Parametros especificos para a tela de Cadastro
     * @var object:Parametros
     */
    private $parametroCadastro;

    /**
     * Tela de pesquisa
     */
    public function index() {
        $this->existeParametroEmail = $this->dao->existeParametroEmail();

        if (!$this->existeParametroEmail) {
            $this->adicionarMensagemAlerta("É necessário o cadastramento do e-mail para aprovação do crédito futuro.");
        }

        $this->listarTipoCampanha = $this->dao->pesquisarTiposCampanha();
        $this->listarMotivo = $this->dao->pesquisarMotivosCredito();
        $this->listaObrigacaoFinanceira = $this->dao->pesquisarListaObrigacaoFinanceira();

        //$arrayTipoCampanha
        foreach ($this->listarTipoCampanha as $key => $item) {
            $arrayTipoCampanha[$item->cftpoid] = $item->cftpdescricao;
        }
        $this->arrayTiposCampanha = $arrayTipoCampanha;

        //$arrayMotivosCreditos
        foreach ($this->listarMotivo as $key => $item) {
            $arrayMotivosCreditos[$item->cfmcoid] = $item->cfmcdescricao;
        }
        $this->arrayMotivosCreditos = $arrayMotivosCreditos;

        //$arrayObrigacaoFinanceira
        foreach ($this->listaObrigacaoFinanceira as $key => $item) {
            $arrayObrigacaoFinanceira[$item->obroid] = $item->obrobrigacao;
        }
        $this->arrayObrigacaoFinanceira = $arrayObrigacaoFinanceira;




        require _MODULEDIR_ . 'Financas/View/fin_credito_futuro_parametrizacao_campanha/index.php';
    }

    /**
     * Tela de cadastro
     */
    public function cadastro() {

        //se existir parametro id, é populado o formulário para edição de dados
        if (isset($_GET['id']) && trim($_GET['id']) != '') {
            $this->parametroCadastro->registro = $this->dao->pesquisarCampanha($_GET['id']);
        }

        $this->parametroEmailParametrizacao = $this->existeParametroEmail = $this->dao->existeParametroEmail();

        if (empty($this->existeParametroEmail->cfeavalor_percentual_desconto)) {
            $this->existeParametroEmail = false;
        } else {
            $this->existeParametroEmail = true;
        }

        if (!$this->existeParametroEmail) {
            $this->adicionarMensagemAlerta("É necessário o cadastramento do e-mail para aprovação do crédito futuro.");
        }

        $this->listarTipoCampanha = $this->dao->pesquisarTiposCampanha();
        $this->listarMotivo = $this->dao->pesquisarMotivosCredito();
        $this->listaObrigacaoFinanceira = $this->dao->pesquisarListaObrigacaoFinanceira();
        if ($this->parametroCadastro->registro->cfcpoid) {
            $this->parametroCadastro->historico = $this->dao->pesquisarHistorico($this->parametroCadastro->registro->cfcpoid);
        }

        require _MODULEDIR_ . 'Financas/View/fin_credito_futuro_parametrizacao_campanha/cadastro.php';
    }

    /*
     * Método que realiza cadastro/edição
     */

    public function salvar() {
        if ($this->validarCadastro()) {
            if ($this->parametroCadastro->registro = $this->dao->salvar($this->parametroCadastro->registro)) {
                if ($this->editar) {
                    $this->adicionarMensagemSucesso('Campanha Promocional alterada com sucesso.');
                } else {
                    $this->adicionarMensagemSucesso('Campanha Promocional incluída com sucesso.');
                }
                unset($_POST);
                $this->pesquisar();
                return true;
            }
        }

        $this->cadastro();
        return true;
    }

    public function pesquisar() {
        if ($this->validarPesquisa()) {
            $this->parametroPesquisa->resultado = $this->dao->pesquisar($this->parametroPesquisa);
        }

        $this->index();
    }

    public function excluir() {
        
        $this->parametrosExclusao->usuario = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';
        $this->parametrosExclusao->cfcpoid = isset($_GET['id']) ? $_GET['id'] : '';
        $this->parametrosExclusao->cfcpcftpoid = isset($_GET['campanha_id']) ? $_GET['campanha_id'] : '';
            
        if ($this->dao->verificarUsoCampanhaPromocional($this->parametrosExclusao)) {
            $this->adicionarMensagemAlerta('Não foi possível excluir a campanha pois a mesma já esta em uso em um crédito futuro.');
        } else if ($this->dao->excluirCampanha($this->parametrosExclusao)) {
            $this->adicionarMensagemSucesso('Campanha Promocional excluída com sucesso.');
        }

        unset($_POST);
        $this->pesquisar();
    }

    private function validarCadastro() {

        $status = true;

        $camposObrigatorios = true;
        $diferenteZeroPorcentagem = true;
        $diferenteZero = true;
        $dataFinalMaior = true;
        $menorCem = true;

        $this->parametroCadastro = new stdClass();

        foreach ($_POST as $campo => $item) {
            $this->parametroCadastro->registro->$campo = isset($_POST[$campo]) && $_POST[$campo] != "" ? htmlspecialchars(trim($_POST[$campo])) : '';
        }

        $this->editar = false;
        if (isset($this->parametroCadastro->registro->cfcpoid) && !empty($this->parametroCadastro->registro->cfcpoid)) {
            $this->editar = true;

            // Verificação de houve alteração ou não
            $verificaAlteracao = false;


            if (isset($this->parametroCadastro->registro->cfcpdesconto_valor) && !empty($this->parametroCadastro->registro->cfcpdesconto_valor)) {
                $this->parametroCadastro->registro->cfcpdesconto = str_replace('.', '', $this->parametroCadastro->registro->cfcpdesconto_valor);
                $this->parametroCadastro->registro->cfcpdesconto = str_replace(',', '.', $this->parametroCadastro->registro->cfcpdesconto);
            } else if (isset($this->parametroCadastro->registro->cfcpdesconto_percentual) && !empty($this->parametroCadastro->registro->cfcpdesconto_percentual)) {
                $this->parametroCadastro->registro->cfcpdesconto = str_replace('.', '', $this->parametroCadastro->registro->cfcpdesconto_percentual);
                $this->parametroCadastro->registro->cfcpdesconto = str_replace(',', '.', $this->parametroCadastro->registro->cfcpdesconto);
            }

            $this->parametroCadastrado = $this->dao->pesquisarCampanha($this->parametroCadastro->registro->cfcpoid);

            foreach ($this->parametroCadastro->registro as $key => $value) {
                if (isset($this->parametroCadastrado->$key) && $this->parametroCadastrado->$key != $this->parametroCadastro->registro->$key) {
                    $verificaAlteracao = true;
                }
            }

            if (!$verificaAlteracao) {
                $this->adicionarMensagemAlerta("Nenhuma informação foi alterada.");
                $_GET['id'] = $this->parametroCadastro->registro->cfcpoid;
                $this->cadastro();
                exit();
            }
        }

        $this->parametroCadastro->registro->usuario = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';



        if (!empty($this->parametroCadastro->registro->cfcpdt_inicio_vigencia) && !empty($this->parametroCadastro->registro->cfcpdt_fim_vigencia) && !empty($this->parametroCadastro->registro->cfcpcftpoid) && $this->dao->existeTipoCampanhaPeriodo($this->parametroCadastro->registro)) {

            $this->adicionarMensagemAlerta("Já existe campanha promocional com o mesmo tipo
                            de campanha promocional onde o período de vigência está no intervalo ou coincide
                            com o intervalo informado.");
            $status = false;
        }


        /* Inicio validação do periodo */
        if (empty($this->parametroCadastro->registro->cfcpdt_inicio_vigencia)) {
            //verifico se é vazio
            $this->destacarCampo('cfcpdt_inicio_vigencia', 'Campo obrigatório.');
            $camposObrigatorios = false;
        }

        if (empty($this->parametroCadastro->registro->cfcpdt_fim_vigencia)) {
            //verifico se é vazio
            $this->destacarCampo('cfcpdt_fim_vigencia', 'Campo obrigatório.');
            $camposObrigatorios = false;
        }


        //if (!$this->editar) {
        if (!empty($this->parametroCadastro->registro->cfcpdt_fim_vigencia) && $this->getTime($this->parametroCadastro->registro->cfcpdt_fim_vigencia) < $this->getTime(date('d/m/Y'))) {
            $this->destacarCampo('cfcpdt_fim_vigencia', '');
            $this->adicionarMensagemAlerta("Período de vigência final não pode ser menor que a data de hoje.");
            $status = false;
        }
        //}


        if (!empty($this->parametroCadastro->registro->cfcpdt_inicio_vigencia) && !empty($this->parametroCadastro->registro->cfcpdt_fim_vigencia) && $this->getTime($this->parametroCadastro->registro->cfcpdt_inicio_vigencia) > $this->getTime($this->parametroCadastro->registro->cfcpdt_fim_vigencia)) {
            //verifico se a data de inicio é maior que a data final
            $this->destacarCampo('cfcpdt_fim_vigencia', '');
            $this->adicionarMensagemAlerta("A data inicial não pode ser maior que a data final.");
            $dataFinalMaior = false;
        }


        /* Fim validação do periodo */


        /* Inicio da validação de tipo campanha promocional */
        if (empty($this->parametroCadastro->registro->cfcpcftpoid)) {
            $this->destacarCampo('cfcpcftpoid', 'Campo obrigatório.');
            $camposObrigatorios = false;
        }
        /* Fim da validação de tipo campanha promocional */

        /* Inicio da validação de motivo de credito */
        if (empty($this->parametroCadastro->registro->cfcpcfmccoid)) {
            $this->destacarCampo('cfcpcfmccoid', 'Campo obrigatório.');
            $camposObrigatorios = false;
        }
        /* Fim da validação de tipo campanha promocional */


        /* Inicio da validação de tipo de desconto */

        if (!empty($this->parametroCadastro->registro->cfcptipo_desconto) && $this->parametroCadastro->registro->cfcptipo_desconto == 'P') {

            $this->parametroCadastro->registro->cfcpdesconto = str_replace(',', '.', str_replace('.', '', $this->parametroCadastro->registro->cfcpdesconto_percentual));

            if ($this->parametroCadastro->registro->cfcpdesconto == '') {
                $this->destacarCampo('cfcpdesconto_percentual', 'Campo obrigatório.');
                $camposObrigatorios = false;
            } else if ($this->parametroCadastro->registro->cfcpdesconto == 0) {
                $this->destacarCampo('cfcpdesconto_percentual', '');
                $this->adicionarMensagemAlerta("O percentual de desconto deve ser maior que zero.");
                $diferenteZeroPorcentagem = false;
            } else if ($this->parametroCadastro->registro->cfcpdesconto >= 100) {
                $this->destacarCampo('cfcpdesconto_percentual', '');
                $this->adicionarMensagemAlerta("O percentual de desconto não pode ser igual ou maior que 100%.");
                $menorCem = false;
            }

            $this->verificaPercentualEmailAprovacao = $this->dao->existeParametroEmail();
            if (floatval($this->parametroCadastro->registro->cfcpdesconto) > floatval($this->verificaPercentualEmailAprovacao->cfeavalor_percentual_desconto)) {
                $this->destacarCampo('cfcpdesconto_percentual', '');
                $this->adicionarMensagemAlerta("O percentual de desconto não pode ser maior que o percentual de desconto parametrizado.");
                $status = false;
            }
        } else if (!empty($this->parametroCadastro->registro->cfcptipo_desconto) && $this->parametroCadastro->registro->cfcptipo_desconto == 'V') {

            $this->parametroCadastro->registro->cfcpdesconto = str_replace(',', '.', str_replace('.', '', $this->parametroCadastro->registro->cfcpdesconto_valor));

            if (empty($this->parametroCadastro->registro->cfcpdesconto)) {
                $this->destacarCampo('cfcpdesconto_valor', 'Campo obrigatório.');
                $camposObrigatorios = false;
            } else if ($this->parametroCadastro->registro->cfcpdesconto == '0') {
                $this->destacarCampo('cfcpdesconto_valor', '');
                $this->adicionarMensagemAlerta("O valor de desconto deve ser maior que zero.");
                $diferenteZero = false;
            }

            $this->verificaPercentualEmailAprovacao = $this->dao->existeParametroEmail();
            if (floatval($this->parametroCadastro->registro->cfcpdesconto) > floatval($this->verificaPercentualEmailAprovacao->cfeavalor_credito_futuro)) {
                $this->destacarCampo('cfcpdesconto_valor', '');
                $this->adicionarMensagemAlerta("O valor de desconto não pode ser maior que o valor de desconto parametrizado.");
                $status = false;
            }
        }
        /* fim da validação de tipo de desconto */


        /* Inicio da validação de forma de aplicação */
        if (!empty($this->parametroCadastro->registro->cfcpaplicacao) && $this->parametroCadastro->registro->cfcpaplicacao == 'P') {

            if ($this->parametroCadastro->registro->cfcpqtde_parcelas == '') {
                $this->destacarCampo('cfcpqtde_parcelas', 'Campo obrigatório.');
                $camposObrigatorios = false;
            } else if (intval($this->parametroCadastro->registro->cfcpqtde_parcelas) <= 1) {
                $this->destacarCampo('cfcpqtde_parcelas', '');
                $this->adicionarMensagemAlerta("A quantidade de parcelas deve ser maior que 1.");
                $diferenteZero = false;
            }

            $this->verificaPercentualEmailAprovacao = $this->dao->existeParametroEmail();
            if ($this->parametroCadastro->registro->cfcpqtde_parcelas > $this->verificaPercentualEmailAprovacao->cfeaparcelas) {
                $this->destacarCampo('cfcpqtde_parcelas', '');
                $this->adicionarMensagemAlerta("A quantidade de parcelas não pode ser maior que a quantidade de parcelas parametrizada.");
                $status = false;
            }
        } else {
            //se não for parcelado, o valor padrão para integral é 1.
            $this->parametroCadastro->registro->cfcpqtde_parcelas = '1';
        }
        /* fim da validação de tipo de desconto */


        /* Inicio da validação de desconto sobre o valor total */
        if (empty($this->parametroCadastro->registro->cfcpaplicar_sobre)) {
            $this->destacarCampo('cfcpaplicar_sobre', 'Campo obrigatório.');
            $camposObrigatorios = false;
        }
        /* fim da validação de desconto sobre o valor total */

        /* Inicio da validação de obrigação financeira */
        if (empty($this->parametroCadastro->registro->cfcpobroid)) {
            $this->destacarCampo('cfcpobroid', 'Campo obrigatório.');
            $camposObrigatorios = false;
        }
        /* fim da validação de desconto sobre o valor total */

        if (!$camposObrigatorios || !$diferenteZeroPorcentagem || !$diferenteZero || !$dataFinalMaior || !$menorCem) {
            if (!$camposObrigatorios) {
                $this->adicionarMensagemAlerta("Existem campos obrigatórios não preenchidos.");
            }
            $status = false;
        }


        return $this->retorno->status = $status;
    }

    private function getTime($data) {

        $data = explode('/', $data);

        return strtotime($data[2] . '-' . $data[1] . '-' . $data[0]);
    }

    private function validarPesquisa() {
        $status = true;

        if (!isset($_POST['cfcpdt_inicio_vigencia'])) {
            if (isset($_SESSION['FCFPCpesquisar'])) {
                $this->parametroPesquisa = unserialize($_SESSION['FCFPCpesquisar']);
            } else {
                $status = false;
            }
        } else {
            $this->parametroPesquisa->cfcpdt_inicio_vigencia = isset($_POST['cfcpdt_inicio_vigencia']) ? $_POST['cfcpdt_inicio_vigencia'] : null;
            $this->parametroPesquisa->cfcpdt_fim_vigencia = isset($_POST['cfcpdt_fim_vigencia']) ? $_POST['cfcpdt_fim_vigencia'] : null;
            $this->parametroPesquisa->cfcpcftpoid = isset($_POST['cfcpcftpoid']) ? intval($_POST['cfcpcftpoid']) : 0;
            $this->parametroPesquisa->cfcpcfmccoid = isset($_POST['cfcpcfmccoid']) ? intval($_POST['cfcpcfmccoid']) : 0;
            $this->parametroPesquisa->cfcptipo_desconto = isset($_POST['cfcptipo_desconto']) ? trim($_POST['cfcptipo_desconto']) : null;
            $this->parametroPesquisa->cfcpaplicacao = isset($_POST['cfcpaplicacao']) ? trim($_POST['cfcpaplicacao']) : null;
            $this->parametroPesquisa->cfcpdesconto_percentual_de = isset($_POST['cfcpdesconto_percentual_de']) ? $_POST['cfcpdesconto_percentual_de'] : 0;
            $this->parametroPesquisa->cfcpdesconto_percentual_ate = isset($_POST['cfcpdesconto_percentual_ate']) ? $_POST['cfcpdesconto_percentual_ate'] : 0;
            $this->parametroPesquisa->cfcpdesconto_valor_de = isset($_POST['cfcpdesconto_valor_de']) ? $_POST['cfcpdesconto_valor_de'] : 0;
            $this->parametroPesquisa->cfcpdesconto_valor_ate = isset($_POST['cfcpdesconto_valor_ate']) ? $_POST['cfcpdesconto_valor_ate'] : 0;

            $this->parametroPesquisa->cfcpdesconto_valor_de = str_replace('.', '', $this->parametroPesquisa->cfcpdesconto_valor_de);
            $this->parametroPesquisa->cfcpdesconto_valor_de = str_replace(',', '.', $this->parametroPesquisa->cfcpdesconto_valor_de);

            $this->parametroPesquisa->cfcpdesconto_valor_ate = str_replace('.', '', $this->parametroPesquisa->cfcpdesconto_valor_ate);
            $this->parametroPesquisa->cfcpdesconto_valor_ate = str_replace(',', '.', $this->parametroPesquisa->cfcpdesconto_valor_ate);

            $this->parametroPesquisa->cfcpdesconto_percentual_de = str_replace('.', '', $this->parametroPesquisa->cfcpdesconto_percentual_de);
            $this->parametroPesquisa->cfcpdesconto_percentual_de = str_replace(',', '.', $this->parametroPesquisa->cfcpdesconto_percentual_de);

            $this->parametroPesquisa->cfcpdesconto_percentual_ate = str_replace('.', '', $this->parametroPesquisa->cfcpdesconto_percentual_ate);
            $this->parametroPesquisa->cfcpdesconto_percentual_ate = str_replace(',', '.', $this->parametroPesquisa->cfcpdesconto_percentual_ate);
        }

        return $this->retorno->status = $status;
    }

    /**
     * Verifica se uma requisição foi efetuada via AJAX
     * @return	boolean
     */
    private function isAjax() {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * Cria mensagem de erro para a view ou retorno json
     * @param string $mensagem
     * @param string $divId
     */
    private function adicionarMensagemErro($mensagem, $divId = 'mensagens') {
        $msg = new stdClass();
        $msg->divMensagens = $divId;
        $msg->tipo = 'erro';
        $msg->mensagem = $mensagem;
        $this->retorno->mensagens[] = $msg;
    }

    /**
     * Cria mensagem de sucesso para a view ou retorno json
     * @param string $mensagem
     * @param string $divId
     */
    private function adicionarMensagemSucesso($mensagem, $divId = 'mensagens') {
        $msg = new stdClass();
        $msg->divMensagens = $divId;
        $msg->tipo = 'sucesso';
        $msg->mensagem = $mensagem;
        $this->retorno->mensagens[] = $msg;
    }

    /**
     * Cria mensagem de alerta para a view ou retorno json
     * @param string $mensagem
     * @param string $divId
     */
    private function adicionarMensagemAlerta($mensagem, $divId = 'mensagens') {
        $msg = new stdClass();
        $msg->divMensagens = $divId;
        $msg->tipo = 'alerta';
        $msg->mensagem = $mensagem;
        $this->retorno->mensagens[] = $msg;
    }

    /**
     * Informa qual campo deve ser destacado na tela.
     * @param string $idCampo
     * @param string $mensagem
     */
    private function destacarCampo($idCampo, $mensagem = "") {
        $campo = new stdClass();
        $campo->campo = $idCampo;
        if (!empty($mensagem)) {
            $campo->mensagem = utf8_encode($mensagem);
        }
        $this->retorno->camposDestaque[] = $campo;
    }

    /**
     * Exibe mensagens do sistema
     * @return string
     */
    public function exibirMensagens($divId = 'mensagens') {
        $mensagem = "<div id=\"$divId\" class=\"invisivel\">";
        if (count($this->retorno->camposDestaque) > 0) {
            $mensagem .= "<script type=\"text/javascript\" >jQuery(document).ready(function() { showFormErros(" . json_encode($this->retorno->camposDestaque) . "); });</script>";
        }
        if (count($this->retorno->mensagens) > 0) {
            foreach ($this->retorno->mensagens as $msg) {
                if ($msg->divMensagens == $divId) {
                    $mensagem .= "<div class=\"mensagem " . $msg->tipo . "\">" . $msg->mensagem . "</div>";
                }
            }
        }
        $mensagem .= "<script type=\"text/javascript\" >jQuery(document).ready(function() { jQuery('#$divId').showMessage(); });</script></div>";
        return $mensagem;
    }

    /**
     * Caso a solicitação seja ajax, o retorno deve ser um json de retorno.
     * @return string - json
     */
    private function parseJsonRetorno() {
        foreach ($this->retorno->mensagens as $key => $mensagem) {
            $this->retorno->mensagens[$key]->mensagem = utf8_encode($mensagem->mensagem);
        }

        foreach ($this->retorno->camposDestaque as $key => $campo) {
            $this->retorno->camposDestaque[$key]->mensagem = utf8_encode($campo->mensagem);
        }

        return json_encode($this->retorno);
    }

    public function __construct() {
        global $conn;
        $this->dao = new FinCreditoFuturoParametrizacaoCampanhaDao($conn);

        //prepara retorno.
        $this->retorno = new stdClass();
        $this->retorno->status = true;
        $this->retorno->mensagens = array();
        $this->retorno->camposDestaque = array();
    }

    public function __destruct() {
        if (!empty($this->parametroPesquisa)) {
            $_SESSION['FCFPCpesquisar'] = serialize($this->parametroPesquisa);
        }
    }

}