<?php

require _MODULEDIR_ . 'Financas/DAO/FinCreditoFuturoParametrizacaoTipoCampanhaDAO.php';

require _MODULEDIR_ . 'Financas/DAO/FinCreditoFuturoParametrizacaoMotivoCreditoDAO.php';

require _MODULEDIR_ . 'Financas/DAO/FinCreditoFuturoParametrizacaoEmailAprovacaoDAO.php';

/**
 * FinImportacaoStatusContrato.php
 *
 * - Cadastro motivo crédito
 * @author Vanessa Rabelo <vanessa.rabelo@meta.com.br>
 * @package Finanças
 * @since 27/06/2013
 *
 */
class FinCreditoFuturoParametrizacao {

    private $dao;
    private $view;
    private $resultados;
    private $mensagem_alerta;
    private $mensagem_sucesso;
    private $mensagem_erro;
    private $mensagem_info;
    private $parametros;

    public function __construct() {
        
    }

    /**
     * Instancia a classe que será usada no DAO
     * @global connection $conn
     * @param string $classe
     */
    private function instanciarDAO($classe) {
        global $conn;
        $this->dao = new $classe($conn);
    }

    public function index($filtros = null, $parametros = null) {


        $this->view = (empty($this->view)) ? 'motivo_credito.php' : $this->view;

        require _MODULEDIR_ . 'Financas/View/fin_credito_futuro_parametrizacao/index.php';
    }

    /**
     * Pesquisa os cadastros de motivos do credito
     * @param boolean $operacao
     */
    public function pesquisarMotivoCredito($operacao = false) {
        $this->instanciarDAO('FinCreditoFuturoParametrizacaoMotivoCreditoDAO');

        $descricao = isset($_POST['cfmcdescricao']) ? $_POST['cfmcdescricao'] : null;
        $cfmctipo = (isset($_POST['cfmctipo']) && strlen($_POST['cfmcdescricao'])) ? $_POST['cfmctipo'] : "";

        try {
            $this->parametros = new stdClass();
            $this->parametros->descricao = $this->tratarDescricaoPesquisa($descricao);
            $this->parametros->cfmctipo = $cfmctipo;
            $this->resultados = $this->dao->visualisar($this->parametros);

            if (count($this->resultados) == 0 && !is_null($descricao) && !$operacao) {

                $this->mensagem_alerta = 'Nenhum registro encontrado.';
            }
            $this->view = 'motivo_credito.php';
            //Retorna o filtro
            $this->parametros->descricao = $descricao;
            $this->index($this->parametros);
        } catch (Exception $e) {
            $this->mensagem_erro = $e->getMessage();
            $this->index();
        }
    }

    /**
     * Pesquisa os tipos de campanhas promocionais
     * @param boolean $operacao
     */
    public function pesquisarTipoCampanhaPromocional($operacao = false) {
        $this->instanciarDAO('FinCreditoFuturoParametrizacaoTipoCampanhaDAO');

        $descricao = isset($_POST['cftpdescricao']) ? strip_tags($_POST['cftpdescricao']) : NULL;

        try {
            $filtros = new stdClass();
            $filtros->descricao = $this->tratarDescricaoPesquisa($descricao);
            $this->resultados = $this->dao->pesquisar($filtros);

            if (count($this->resultados) == 0 && !is_null($descricao) && !$operacao) {

                $this->mensagem_alerta = 'Nenhum registro encontrado.';
            }
            $this->view = 'tipo_campanha_promocional.php';
            //Retorna o filtro
            $filtros->descricao = $descricao;
            $this->index($filtros);
        } catch (Exception $e) {
            $this->mensagem_erro = $e->getMessage();
            $this->index();
        }
    }

    /**
     * Exibe o cadastro da campanha promocional
     */
    public function cadastrarTipoCampanhaPromocional() {
        $this->mensagem_info = 'Campos com * são obrigatórios.';
        $this->view = 'tipo_campanha_promocional_novo.php';
        $this->index();
    }

    /**
     * Exibe o cadastro da motivo credito
     */
    public function cadastrarMotivoCredito() {
        $this->mensagem_info = 'Campos com * são obrigatórios.';
        $this->view = 'novo.php';
        $this->index();
    }

    /**
     * Grava o tipo da campanha promocional
     * @throws Exception
     */
    public function gravarTipoCampanhaPromocional() {
        $this->instanciarDAO('FinCreditoFuturoParametrizacaoTipoCampanhaDAO');




        $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : '';
        $cfmtipo = isset($_POST['cfmtipo']) ? $_POST['cfmtipo'] : '';

        try {
            $parametros = new stdClass();
            $parametros->descricao = trim($descricao);

            if (empty($parametros->descricao)) {
                throw new Exception('Existem campos obrigatórios não preenchidos.');
            }

            $parametroVerificacao = new stdClass();
            $parametroVerificacao->descricao = $this->tratarDescricaoPesquisa($descricao);

            if ($this->dao->verificarExistenciaDescricao($parametroVerificacao) > 0) {
                $this->mensagem_alerta = 'Já existe um Tipo de Campanha Promocional com a descrição informada.';
            } else {
                if ($this->dao->cadastrar($parametros)) {
                    $this->mensagem_sucesso = "Tipo de campanha promocional incluído com sucesso.";
                }
            }
            $this->pesquisarTipoCampanhaPromocional(true);
        } catch (Exception $e) {
            $this->mensagem_erro = $e->getMessage();
            $this->pesquisarTipoCampanhaPromocional();
        }
    }

    /**
     * Grava o motivo de credito
     * @throws Exception
     */
    public function gravarMotivoCredito() {
        $this->instanciarDAO('FinCreditoFuturoParametrizacaoMotivoCreditoDAO');


        $descricao = isset($_POST['descricao']) ? strip_tags($_POST['descricao']) : '';
        $cfmcobservacao = isset($_POST['cfmcobservacao']) ? strip_tags($_POST['cfmcobservacao']) : '';
        $cfmctipo = isset($_POST['cfmctipo']) ? $_POST['cfmctipo'] : '';

        unset($_POST['descricao']);
        unset($_POST['cfmctipo']);
        unset($_POST['cfmcobservacao']);

        try {
            $parametros = new stdClass();
            $parametros->descricao = trim($descricao);
            $parametros->cfmcobservacao = $cfmcobservacao;
            $parametros->cfmctipo = $cfmctipo;

            if (empty($parametros->descricao)) {
                throw new Exception('Existem campos obrigatórios não preenchidos.');
            }

            $parametroVerificacao = new stdClass();
            $parametroVerificacao->descricao = $this->tratarDescricaoPesquisa($descricao);

            if ($this->dao->verificarExistenciaDescricao($parametroVerificacao) > 0) {
                $this->mensagem_alerta = 'Já existe um Motivo do Crédito com a descrição informada.';
            } else {
                if ($this->dao->cadastrar($parametros)) {
                    $this->mensagem_sucesso = "Motivo do Crédito incluído com sucesso.";
                }
            }
            $this->pesquisarMotivoCredito(true);
        } catch (Exception $e) {
            $this->mensagem_erro = $e->getMessage();
            $this->pesquisarMotivoCredito();
        }
    }

    /**
     * Excluir uma campanha promocional
     * @throws Exception
     */
    public function excluirTipoCampanhaPromocional() {
        $this->instanciarDAO('FinCreditoFuturoParametrizacaoTipoCampanhaDAO');

        $cftpoid = isset($_POST['cftpoid']) ? $_POST['cftpoid'] : '';
        $usuario = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        try {
            if (empty($cftpoid) || empty($usuario)) {
                throw new Exception('Não foi informado um tipo para exclusão');
            }

            $parametros = new stdClass();
            $parametros->id = $cftpoid;
            $parametros->usuario = $usuario;

            if ($this->dao->verificarCampanha($parametros) > 0) {
                $this->mensagem_alerta = 'Exclusão não permitida. Tipo de campanha promocional utilizado em cadastro de campanha promocional.';
            } else {
                if ($this->dao->excluir($parametros)) {
                    $this->mensagem_sucesso = "Tipo de campanha promocional excluído com sucesso.";
                }
            }
            $this->pesquisarTipoCampanhaPromocional(true);
        } catch (Exception $e) {
            $this->mensagem_erro = $e->getMessage();
            $this->pesquisarTipoCampanhaPromocional();
        }
    }

    /**
     * Excluir motivo de credito
     * @throws Exception
     */
    public function excluirMotivoCredito() {
        $this->instanciarDAO('FinCreditoFuturoParametrizacaoMotivoCreditoDAO');

        $cfmcoid = isset($_POST['cfmcoid']) ? $_POST['cfmcoid'] : '';

        try {
            if (empty($cfmcoid)) {
                throw new Exception('Não foi informado um tipo para exclusão');
            }

            $parametros = new stdClass();
            $parametros->id = $cfmcoid;

            if ($this->dao->verificarUsoMotivo($parametros)) {
                $this->mensagem_alerta = "Não foi possível excluir o motivo de crédito, pois o mesmo já está em uso.";
            } else if ($this->dao->excluirMotivo($parametros)) {
                $this->mensagem_sucesso = "Motivo do Crédito excluído com sucesso.";
            }

            $this->pesquisarMotivoCredito(true);
        } catch (Exception $e) {
            $this->mensagem_erro = $e->getMessage();
            $this->pesquisarMotivoCredito();
        }
    }

    /**
     * Cadastro de e-mail aprovação
     * @throws Exception
     */
    public function emailAprovacao() {

        if (!$_SESSION['funcao']['autoriza_credito_futuro_email_aprovacao']) {
            $this->pesquisarMotivoCredito();
            return;
        }

        $this->instanciarDAO('FinCreditoFuturoParametrizacaoEmailAprovacaoDAO');

        try {

            $this->atualParametros = $this->dao->pesquisarParametro();
            $this->atualParametros->cfeavalor_credito_futuro = isset($this->atualParametros->cfeavalor_credito_futuro) && !empty($this->atualParametros->cfeavalor_credito_futuro) ? number_format($this->atualParametros->cfeavalor_credito_futuro, 2, ',', '.') : '';
            $this->atualParametros->cfeavalor_percentual_desconto = isset($this->atualParametros->cfeavalor_percentual_desconto) && !empty($this->atualParametros->cfeavalor_percentual_desconto) ? number_format($this->atualParametros->cfeavalor_percentual_desconto, 2, ',', '.') : '';

            $this->opcoesObrigacaoFinanceira = $this->dao->pesquisarListaObrigacaoFinanceira();

            if (isset($_POST) && !empty($_POST)) {
                $camposParametrizacao = new stdClass();
                foreach ($_POST as $campo => $item) {
                    $camposParametrizacao->$campo = isset($_POST[$campo]) && !empty($_POST[$campo]) ? htmlspecialchars(trim($_POST[$campo])) : '';
                }

                //verifico se há alteração
                $registroAlterado = false;
                foreach ($camposParametrizacao as $key => $value) {
                    if (isset($this->atualParametros->$key) && $camposParametrizacao->$key != $this->atualParametros->$key) {
                        $registroAlterado = true;
                    }
                }

                //se não houver alteração envia mensagem de alerta, se houver é dado sequencia no processo
                if (!$registroAlterado) {
                    $this->mensagemAlerta = "Nenhuma informação foi alterada.";
                } else {
                    //Realizado tratamento de valores para salvar em tabela
                    $camposParametrizacao->cfeavalor_credito_futuro = str_replace('.', '', $camposParametrizacao->cfeavalor_credito_futuro);
                    $camposParametrizacao->cfeavalor_credito_futuro = str_replace(',', '.', $camposParametrizacao->cfeavalor_credito_futuro);

                    $camposParametrizacao->cfeavalor_percentual_desconto = str_replace('.', '', $camposParametrizacao->cfeavalor_percentual_desconto);
                    $camposParametrizacao->cfeavalor_percentual_desconto = str_replace(',', '.', $camposParametrizacao->cfeavalor_percentual_desconto);

                    $camposParametrizacao->usuario = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

                    if ($this->dao->salvar($camposParametrizacao)) {
                        $this->mensagemSucesso = "Cadastro atualizado com sucesso.";
                    }
                }
            }
        } catch (Exception $e) {
            $this->mensagemErro = $e->getMessage();
        }

        $this->atualParametros = $this->dao->pesquisarParametro();
        $this->atualParametros->cfeavalor_credito_futuro = isset($this->atualParametros->cfeavalor_credito_futuro) && !empty($this->atualParametros->cfeavalor_credito_futuro) ? number_format($this->atualParametros->cfeavalor_credito_futuro, 2, ',', '.') : '';
        $this->atualParametros->cfeavalor_percentual_desconto = isset($this->atualParametros->cfeavalor_percentual_desconto) && !empty($this->atualParametros->cfeavalor_percentual_desconto) ? number_format($this->atualParametros->cfeavalor_percentual_desconto, 2, ',', '.') : '';

        $this->opcoesObrigacaoFinanceira = $this->dao->pesquisarListaObrigacaoFinanceira();

        $this->view = 'email_aprovacao/email_aprovacao.php';

        $parametros = new stdClass();

        $parametros->responsaveis = $this->dao->pesquisarListaEmailResponsavel();

        $this->index(null, $parametros);
    }

    /**
     * Substitui caracteres especiais e espaços de forma a adequar para a busca em banco
     * @param string $descricao
     * @return string
     */
    private function tratarDescricaoPesquisa($descricao) {

        $descricao = trim($descricao);

        $texto = preg_replace("[^a-z A-Z 0-9.,/()]", "", strtr($descricao, 'áàãâéêíìóôõúüçÁÀÃÂÉÊÍÌÓÔÕÚÜÇ', 'aaaaeeiiooouucAAAAEEIIOOUUC'));

        $texto = str_replace(' ', '%', $texto);

        return $texto;
    }

    /**
     * Exibe mensagens do sistema
     * @return string
     */
    private function exibirMensagem() {

        $mensagem = "";

        if (!empty($this->mensagem_info)) {
            $mensagem .= "<div class=\"mensagem info\">" . $this->mensagem_info . "</div>";
        }

        if (!empty($this->mensagem_alerta)) {
            $mensagem .= "<div class=\"mensagem alerta\" id=\"mensagem_alerta\">" . $this->mensagem_alerta . "</div>";
        } else {
            $mensagem .= "<div class=\"mensagem alerta invisivel\" id=\"mensagem_alerta\"></div>";
        }

        if (!empty($this->mensagem_sucesso)) {
            $mensagem .= "<div class=\"mensagem sucesso\">" . $this->mensagem_sucesso . "</div>";
        }

        if (!empty($this->mensagem_erro)) {
            $mensagem .= "<div class=\"mensagem erro\">" . $this->mensagem_erro . "</div>";
        }

        return $mensagem;
    }

    /**
     * Buscar os responsáveis por receber os emails da parametrização
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @return String (json)
     */
    public function buscarResponsavel() {

        $this->instanciarDAO('FinCreditoFuturoParametrizacaoEmailAprovacaoDAO');

        try {

            $filtros = new stdClass();

            $filtros->nome = isset($_GET['term']) ? trim(addslashes($_GET['term'])) : 0;

            $retorno = $this->dao->buscarResponsavel($filtros);

            echo json_encode($retorno);
        } catch (Exception $e) {
            
        }
    }

    /**
     * Adiciona o responsável por receber os emails da parametrização
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @return String (json)
     */
    public function adicionarResponsavel() {

        $this->instanciarDAO('FinCreditoFuturoParametrizacaoEmailAprovacaoDAO');

        try {

            $parametro = new stdClass();
            $retorno = array('status' => true);

            $parametro->cd_usuario = isset($_POST['id_usuario']) ? trim($_POST['id_usuario']) : 0;

            $parametro->motivoCredito = isset($_POST['motivos']) && count($_POST['motivos']) ? $_POST['motivos'] : array();


            if (empty($parametro->cd_usuario)) {

                $retorno['tipoErro'] = 'alerta';

                $retorno['dados'][] = array(
                    'campo' => 'nome',
                    'mensagem' => utf8_encode('Campo obrigatório.')
                );

                throw new Exception('Existem campos obrigatórios não preenchidos.');
            }

            $usuario = $this->dao->buscarResponsavel($parametro);
            if (count($usuario) > 0) {
                if (empty($usuario[0]['email'])) {
                    $retorno['tipoErro'] = 'alerta';

                    $retorno['dados'][] = array(
                        'campo' => 'nome',
                        'mensagem' => utf8_encode('Usuário sem e-mail cadastrado.')
                    );
                    throw new Exception('O e-mail do responsável é uma informação obrigatória.');
                }
            }

            $inseriu = $this->dao->incluirEmailResponsavel($parametro);

            if ($inseriu === false) {

                $retorno['tipoErro'] = 'erro';
                throw new Exception('Erro ao adicionar o responsável.');
            }

            if (count($parametro->motivoCredito)) {
                //print_r($inseriu);
                $statusTipoMotivo = false;

                foreach ($parametro->motivoCredito as $motivo) {
                    $statusTipoMotivo = $this->dao->incluirMotivoCreditoResponsavel($parametro->cd_usuario, $motivo);
                }

                if ($statusTipoMotivo) {
                    /**
                     * segundo parametro booleano para trazer o último inserido
                     */
                    $retorno['responsavel'] = $this->dao->pesquisarListaEmailResponsavel(null, true);
                    echo json_encode($retorno);
                    exit;
                } else {
                    $retorno['tipoErro'] = 'erro';
                    throw new Exception('Erro ao adicionar o responsável.');
                }
            } else {
                $retorno['tipoErro'] = 'erro';
                throw new Exception('Erro ao adicionar o responsável.');
            }

            /**
             * segundo parametro booleano para trazer o último inserido
             */
            //$retorno['responsavel'] = $this->dao->pesquisarListaEmailResponsavel(null, true);

            exit;
        } catch (Exception $e) {

            $retorno['status'] = false;
            $retorno['mensagem'] = utf8_encode($e->getMessage());

            echo json_encode($retorno);
        }
    }

    /**
     * Método listarResponsaveis()
     * Usado na requisição ajax para listar os responsáveis na Parametrização de E-mail
     */
    public function listarResponsaveis() {
        $this->instanciarDAO('FinCreditoFuturoParametrizacaoEmailAprovacaoDAO');
        $parametros = new stdClass();
        $parametros->responsaveis = $this->dao->pesquisarListaEmailResponsavel();
        require _MODULEDIR_ . 'Financas/View/fin_credito_futuro_parametrizacao/email_aprovacao/lista-responsaveis.php';
    }

    /**
     * Exclui e-mail do responsável na seção de e-mail para aprovação
     * @param string $descricao
     * @return json
     */
    public function excluirEmailResponsavel() {

        $this->instanciarDAO('FinCreditoFuturoParametrizacaoEmailAprovacaoDAO');

        try {

            $parametros_email_aprovacao = $this->dao->pesquisarParametro();
            $responsaveis = $this->dao->pesquisarListaEmailResponsavel();

            if (!empty($parametros_email_aprovacao) && (!is_array($responsaveis) || count($responsaveis) == 1)) {
                $retorno['tipoErro'] = 'alerta';
                throw new Exception('Pelo menos um responsável deve ser informado.');
            }

            $parametros = new stdClass();
            $retorno = array('status' => true);

            $parametros->cferoid = isset($_POST['cferoid']) ? (int) $_POST['cferoid'] : 0;
            $parametros->usuarioid = isset($_POST['usuarioid']) ? (int) $_POST['usuarioid'] : 0;

            if (empty($parametros->cferoid)) {
                $retorno['tipoErro'] = 'alerta';
                throw new Exception('O e-mail do responsável é uma informação obrigatória.');
            }

            $deletou = $this->dao->excluirEmailResponsavel($parametros);

            if (!$deletou) {
                $retorno['tipoErro'] = 'erro';
                throw new Exception('Houve um erro no processamento dos dados.');
            }

            echo json_encode($retorno);
        } catch (Exception $e) {
            $retorno['status'] = false;
            $retorno['mensagem'] = utf8_encode($e->getMessage());

            echo json_encode($retorno);
        }
    }

    /**
     * Cria string de diferenças do Historico anterior para o atual.
     * @param object $registroAnterior
     * @param object $registro
     * @return string
     */
    private function diferencaHistorico($registroAnterior, $registro) {
        $strDiferenca = "";

        if ($registroAnterior != null) {
            if ($registroAnterior->cfeavalor_credito_futuro != $registro->cfeavalor_credito_futuro) {
                $strDiferenca .= "<b>Valor do crédito futuro de: </b>" . number_format($registroAnterior->cfeavalor_credito_futuro, 2, ",", ".") .
                        "<b> para: </b>" . number_format($registro->cfeavalor_credito_futuro, 2, ",", ".") . "<br />";
            }
            if ($registroAnterior->cfeavalor_percentual_desconto != $registro->cfeavalor_percentual_desconto) {
                $strDiferenca .= "<b>Percentual de desconto do crédito futuro de:</b> " . number_format($registroAnterior->cfeavalor_percentual_desconto, 2, ",", ".") .
                        " <b> para: </b>" . number_format($registro->cfeavalor_percentual_desconto, 2, ",", ".") . "<br />";
            }
            if ($registroAnterior->cfeaparcelas != $registro->cfeaparcelas) {
                $strDiferenca .= "<b>Quantidade de parcelas do crédito futuro de:</b> " . number_format($registroAnterior->cfeaparcelas, 0, ",", ".") .
                        " <b> para:</b> " . number_format($registro->cfeaparcelas, 0, ",", ".") . "<br />";
            }
            if ($registroAnterior->cfeaobroid_contestacao != $registro->cfeaobroid_contestacao) {
                $strDiferenca .= "<b>Obrigação Financeira de Desconto para Contestação de:</b> " . $registroAnterior->cfeaobroid_contestacao . "-" . $registroAnterior->obrcontestacao .
                        " <b> para: </b>" . $registro->cfeaobroid_contestacao . "-" . $registro->obrcontestacao . "<br />";
            }
            if ($registroAnterior->cfeaobroid_contas != $registro->cfeaobroid_contas) {
                $strDiferenca .= "<b>Obrigação Financeira de Desconto para Contas a Receber de: </b>" . $registroAnterior->cfeaobroid_contas . "-" . $registroAnterior->obrcontas .
                        "<b> para: </b>" . $registro->cfeaobroid_contas . "-" . $registro->obrcontas . "<br />";
            }
            if ($registroAnterior->cfeaobroid_campanha != $registro->cfeaobroid_campanha) {
                $strDiferenca .= "<b>Obrigação Financeira de Desconto para Campanha Promocional de:</b> " . $registroAnterior->cfeaobroid_campanha . "-" . $registroAnterior->obrcampanha .
                        "<b> para:</b> " . $registro->cfeaobroid_campanha . "-" . $registro->obrcampanha . "<br />";
            }
            if ($registroAnterior->cfeacabecalho != $registro->cfeacabecalho) {
                $strDiferenca .= "<b>Cabeçalho de: </b>" . wordwrap($registroAnterior->cfeacabecalho, 60, '<br/>', true) . "<b> para: </b>" . wordwrap($registro->cfeacabecalho, 60, '<br/>', true) . "<br />";
            }
            if ($registroAnterior->cfeacorpo != $registro->cfeacorpo) {
                $strDiferenca .= "<b>Corpo do E-mail de:</b> " . wordwrap($registroAnterior->cfeacorpo, 60, '<br/>', true) . " <b>para:</b> " . wordwrap($registro->cfeacorpo, 60, '<br/>', true) . "<br />";
            }
        } else {
            $strDiferenca .= "<b>Valor do crédito futuro: </b>" . number_format($registro->cfeavalor_credito_futuro, 2, ",", ".") . "<br />" .
                    "<b>Percentual de desconto do crédito futuro: </b>" . number_format($registro->cfeavalor_percentual_desconto, 2, ",", ".") . "<br />" .
                    "<b>Quantidade de parcelas do crédito futuro:</b> " . number_format($registro->cfeaparcelas, 0, ",", ".") . "<br />" .
                    "<b>Obrigação Financeira de Desconto para Contestação:</b> " . $registro->cfeaobroid_contestacao . "-" . $registro->obrcontestacao . "<br />" .
                    "<b>Obrigação Financeira de Desconto para Contas a Receber: </b>" . $registro->cfeaobroid_contas . "-" . $registro->obrcontas . "<br />" .
                    "<b>Obrigação Financeira de Desconto para Campanha Promocional:</b> " . $registro->cfeaobroid_campanha . "-" . $registro->obrcampanha . "<br />" .
                    "<b>Cabeçalho:</b> " . wordwrap($registro->cfeacabecalho, 60, '<br/>', true) . "<br />" .
                    "<b>Corpo do E-mail:</b> " . wordwrap($registro->cfeacorpo, 60, '<br/>', true) . "<br />";
        }
        return $strDiferenca;
    }

}