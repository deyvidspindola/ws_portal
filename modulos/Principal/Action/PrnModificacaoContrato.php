<?php
use module\GestorCredito\GestorCreditoService as GestorCredito;
use module\Cliente\ClienteService as Cliente;
use module\Veiculo\VeiculoService as Veiculo;
use module\TituloCobranca\TituloCobrancaService as TituloCobranca;

require_once _MODULEDIR_.'Cadastro/Action/SendLayoutEmails.php';
require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';
require_once _SITEDIR_ . 'lib/Components/Paginacao/PaginacaoComponente.php';

/**
 * Classe PrnModificacaoContrato.
 * Camada de regra de negocio.
 *
 * @package  Principal
 * @author   André Zilz <andre.zilz@sascar.com.br>
 *
 */
class PrnModificacaoContrato {


    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS   = "Existem campos obrigatórios não preenchidos ou inválidos.";
    const MENSAGEM_ALERTA_PERIODO_DATA          = "Data inicial não pode ser maior que a data final.";
    const MENSAGEM_ALERTA_TROCA_CLIENTE         = "O Cliente não pode ser o mesmo do Contrato Original, para este tipo de modificação.";
    const MENSAGEM_ALERTA_CONTRATO_ROUBADO      = "O contrato deve ser um Roubado e Não Recuperado, sem um contrato ativo que tenha sido derivado dele.";
    const MENSAGEM_ALERTA_CONTRATO_INATIVO      = "Contrato inexistente ou inativo.";
    const MENSAGEM_ALERTA_CONTRATO_SEM_VEICULO  = "Contrato não posssui veículo.";
    const MENSAGEM_ALERTA_CONTRATO_SEM_EQUIP    = "Contrato não possui equipamento.";
    const MENSAGEM_ALERTA_CONTRATO_SEM_CLASSE   = "Contrato não possui classe.";
    const MENSAGEM_ALERTA_CONTRATO_SEM_TIPO     = "Contrato não possui tipo.";
    const MENSAGEM_ALERTA_PGTO_CARTAO           = "Erro ao efetuar o pagamento com o cartão de crédito. ";
    const MENSAGEM_ALERTA_CONTRATO_INDISPONIVEL = "Contrato já associado a uma modificação pendente ou em andamento.";
    const MENSAGEM_ALERTA_CONTRATO_NAO_SIGGO    = "O contrato informado não é um contrato SIGGO.";
    const MENSAGEM_ALERTA_NENHUM_CHASSI          = "Nenhum registro encontrato ou disponível para o(s) Chassi(s) informado(s).";
    const MENSAGEM_ALERTA_CLIENTE_PARTICULARIDADE = "Cliente SIGGO sem cadastro de particularidades.";
    const MENSAGEM_ALERTA_ARQUIVO_INVALIDO      = "Formato de arquivo inválido.";
    const MENSAGEM_ALERTA_ARQUIVO_VAZIO         = "Arquivo não pode ser vazio.";
    const MENSAGEM_ALERTA_CLIENTES_DIFERENTES   = "Clientes não podem ser diferentes para o grupo de modificação escolhido.";
    const MENSAGEM_ALERTA_CLIENTES_IGUAIS       = "Clientes não podem ser iguais para o grupo de modificação escolhido.";
    const MENSAGEM_ALERTA_CARGA_SASTM           = "A classe de destino do contrato não pode ser CARGA ou SASTM.";
    const MENSAGEM_ALERTA_VIGENCIA_EXPIRADA     = "Contrato SIGGO fora do último mês de vigência.";
    const MENSAGEM_ALERTA_ANEXOS                = "Necessário adicionar ao menos um anexo.";
    const MENSAGEM_NENHUM_REGISTRO                = "Nenhum registro encontrado.";

    const MENSAGEM_SUCESSO_INCLUIR              = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_GERAR_CONTRATOS      = "Contrato(s) gerado(s) com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR            = "Registro(s) alterado(s) com sucesso.";
    const MENSAGEM_SUCESSO_CANCELAR             = "Modificação cancelada com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR              = "Registro excluído com sucesso.";

    const MENSAGEM_ERRO_PROCESSAMENTO           = "Houve um erro no processamento dos dados.";
    const MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO   = "Houve um erro no processamento do arquivo.";
    const MENSAGEM_ERRO_DADOS_VEICULO           = "Não foi possível recuperar as informações do veículo.";
    const MENSAGEM_ERRO_SERASA                    = "Não foi possível realizar a análise de crédito.";


    const TIPO_RENOVACAO_SIGGO_SEGURO            = '7';
    const TIPO_EFETIVACAO_DEMO                   = '8';
    const TIPO_INSTALACAO_DERIVADA_RNR           = '9';
    const TIPO_UPGRADE_MOBILE_EQPTO_CONVENCIONAL = '10';
    const TIPO_MIGRACAO_EX_SEGURADO              = '14';
    const TIPO_MIGRACAO_REATIVACAO_EX_SEGURADO   = '16';
    const TIPO_MIGRACAO_EQUIPAMENTO              = '18';
    const TIPO_DUPLICACAO_CONTRATO_PLACA2        = '21';

    const GRUPO_TROCA_VEICULO                    = '7';

    //Objeto referente a  classe de persitencia de dados
    private $dao;
    //Objetos para exibicao e dados em telas
    private $view;
    //Usuario logado
    private $usuoid;
    private $idsMigracao;
    private $idsContatoObrigatorio;
    private $permisaoAnaliseCredito;
    private $permisaoAutorizarTecnico;

    /**
     * Metodo construtor.
     * @author Andre L. Zilz
     * @param CadExemploDAO $dao Objeto DAO da classe
     */
    public function __construct($dao = null) {

        //Verifica o se a variavel e um objeto e a instancia na atributo local
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

        //Status de uma transacao
        $this->view->statusPesquisa = false;

         // Ordenção e paginação
        $this->view->ordenacao = null;
        $this->view->paginacao = null;
        $this->view->totalResultados = 0;

        //Telas
         $this->view->tela = 'pesquisa';
         $this->view->sub_tela = '';

         //Array de status de modificacao. Manter ordenado por descricao!
         $this->view->legenda_status = array('A' => 'Aguardando Autorização Técnica',
                                            'X' => 'Cancelado',
                                            'C' => 'Concluído',
                                            'E' => 'Em Andamento',
                                            'P' => 'Pendente');

         //Manter ordenado por descricao!
         $this->view->legenda_status_financeiro = array('P' => 'Aguardando Aprovação',
                                            'A' => 'Crédito Aprovado',
                                            'N' => 'Crédito Não Aprovado');


        $this->view->tipoNegociacao = array('C' => 'Cliente',
                                            'S' => 'Cortesia',
                                            'D' => 'Demonstração',
                                            'L' => 'Locação',
                                            'W' => 'Virtual');

        $this->idsMigracao = array('14','16');

        $this->idsContatoObrigatorio =array('18','21');

        //Usuario logado
        $this->usuoid = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao, a execucao vem do CRON
        if(empty($this->usuoid)) {
            $this->usuoid = 2750;
        }

        $this->permisaoAnaliseCredito = isset($_SESSION['funcao']['analise_credito_contrato_modificacao']) ? $_SESSION['funcao']['analise_credito_contrato_modificacao'] : 0;
        $this->permisaoAnaliseCredito = ($this->permisaoAnaliseCredito == 1) ? TRUE : FALSE;

        $this->permisaoAutorizarTecnico = isset($_SESSION['funcao']['proposta_autorizar_tecnico']) ? $_SESSION['funcao']['proposta_autorizar_tecnico'] : 0;
        $this->permisaoAutorizarTecnico = ($this->permisaoAutorizarTecnico == 1) ? TRUE : FALSE;

    }

    /**
     * Metodo padrao da classe.
     * Reponsavel tambem por realizar a pesquisa invocando o metodo privado
     * @author Andre L. Zilz
     * @return void
     */
    public function index($parametros = null) {

         try {

            if(is_null($parametros)){
                $this->view->parametros = $this->tratarParametros();
            } else {
                $this->view->parametros = $parametros;
            }

            //Inicializa os dados
            $this->inicializarParametros();

            if($this->view->tela != 'cadastro'){
                $this->destruirSessao();
            }


        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {

            $this->view->mensagemAlerta = $e->getMessage();

        }

        require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/index.php";
    }

    /**
     * Trata os parametros do POST/GET. Preenche um objeto com os parametros
     * do POST e/ou GET.
     * @author Andre L. Zilz
     * @return stdClass Parametros tradados
     */
    private function tratarParametros() {

        $retorno = new stdClass();

       if (count($_GET) > 0) {
            foreach ($_GET as $key => $value) {

                //Verifica se atributo ja existe e nao sobrescreve.
                if (!isset($retorno->$key)) {
                     $retorno->$key = isset($_GET[$key]) ? trim($value) : '';
                }
            }
        }

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
     * Tratamento dos dados enviados e consumidos pelas Views
     * @author Andre L. Zilz
     * @return void
     */
    private function inicializarParametros() {

        //Verifica se os parametro existem, senao inicializa todos
		$this->view->parametros->acao               = isset($this->view->parametros->acao)               ? $this->view->parametros->acao : "index" ;
        $this->view->parametros->sub_tela           = isset($this->view->parametros->sub_tela)           ? $this->view->parametros->sub_tela : '' ;
        $this->view->parametros->cmtoid             = isset($this->view->parametros->cmtoid)             ? $this->view->parametros->cmtoid : '' ;
        $this->view->parametros->cmfconnumero       = isset($this->view->parametros->cmfconnumero)       ? $this->view->parametros->cmfconnumero : '' ;
        $this->view->parametros->mdfstatus          = isset($this->view->parametros->mdfstatus)          ? $this->view->parametros->mdfstatus : '' ;
        $this->view->parametros->exibir_cadastro    = isset($this->view->parametros->exibir_cadastro)    ? $this->view->parametros->exibir_cadastro : 'f';


         //Define qual e a tela/aba ativa e aciona metodos especificos
        switch ($this->view->parametros->acao) {
            case "cadastrar":
            case "cadastrarModificacaoLote":
            case "cadastrarAnexo":
                $this->view->tela = 'cadastro';
                if(!empty($this->view->parametros->sub_tela)){
                    $this->view->sub_tela = $this->view->parametros->sub_tela ;
                } else{
                    $this->view->sub_tela = 'aba_dados_principais';
                }

                //Bloco Modificacao
                $this->view->parametros->mdfoid             = isset($this->view->parametros->mdfoid)             ? $this->view->parametros->mdfoid : '' ;
                $this->view->parametros->tipo_pessoa        = isset($this->view->parametros->tipo_pessoa)        ? $this->view->parametros->tipo_pessoa : '' ;
                $this->view->parametros->cpf_cnpj           = isset($this->view->parametros->cpf_cnpj)           ? preg_replace('/\D/', '', $this->view->parametros->cpf_cnpj) : '' ;
                $this->view->parametros->clioid             = isset($this->view->parametros->clioid)             ? $this->view->parametros->clioid : '' ;
                $this->view->parametros->cmfeqcoid_destino   = isset($this->view->parametros->cmfeqcoid_destino)   ? $this->view->parametros->cmfeqcoid_destino : '' ;
                $this->view->parametros->msuboid            = isset($this->view->parametros->msuboid)            ? $this->view->parametros->msuboid : '' ;
                $this->view->parametros->analise_credito          = isset($this->view->parametros->analise_credito)          ? $this->view->parametros->analise_credito                     : 'f' ;
                $this->view->parametros->analise_credito_status   = isset($this->view->parametros->analise_credito_status)   ? $this->view->parametros->analise_credito_status              : '' ;
                $this->view->parametros->aprovacao_credito_status = isset($this->view->parametros->aprovacao_credito_status) ? $this->view->parametros->aprovacao_credito_status            : '' ;
                $this->view->parametros->observacao         = isset($this->view->parametros->observacao)         ? $this->tratarTextoInput($this->view->parametros->observacao) : '' ;
                $this->view->parametros->observacao_serasa  = isset($this->view->parametros->observacao_serasa)  ? $this->view->parametros->observacao_serasa : '' ;
                $this->view->parametros->cmffunoid_executivo = isset($this->view->parametros->cmffunoid_executivo) ? $this->view->parametros->cmffunoid_executivo : '' ;
                $this->view->parametros->cmfrczoid          = isset($this->view->parametros->cmfrczoid)          ? $this->view->parametros->cmfrczoid : '' ;
                $this->view->parametros->msubeqcoid          = isset($this->view->parametros->msubeqcoid)        ? $this->view->parametros->msubeqcoid : '' ;
                $this->view->parametros->cmfclioid_destino  = isset($this->view->parametros->cmfclioid_destino)  ? $this->view->parametros->cmfclioid_destino : '' ;
                $this->view->parametros->cliente_pagador    = isset($this->view->parametros->cliente_pagador)   ? $this->view->parametros->cliente_pagador : '' ;
                $this->view->parametros->cmpttaxa           = isset($this->view->parametros->cmpttaxa)          ? $this->view->parametros->cmpttaxa : 'f' ;
                $this->view->parametros->cmtcmgoid                = isset($this->view->parametros->cmtcmgoid)                ? $this->view->parametros->cmtcmgoid                           : '' ;


                if($this->view->parametros->cmtcmgoid == self::GRUPO_TROCA_VEICULO) {
                    $this->view->parametros->bloquear_parcela = TRUE;
                } else {
                    $this->view->parametros->bloquear_parcela = FALSE;
                }

                if(empty($this->view->parametros->cmfclioid_destino)) {
                    $this->view->parametros->cmfclioid_destino = !empty($this->view->parametros->cliente_pagador)   ? $this->view->parametros->cliente_pagador : '' ;
                }

                $this->view->parametros->cmptleitura_arquivo  = isset($this->view->parametros->cmptleitura_arquivo)  ? $this->view->parametros->cmptleitura_arquivo : 'f' ;
                $this->view->parametros->cmptrecebe_dados_financeiro  = isset($this->view->parametros->cmptrecebe_dados_financeiro)  ? $this->view->parametros->cmptrecebe_dados_financeiro : 't' ;
                $this->view->parametros->esconder_cpf_cnpj  = isset($this->view->parametros->esconder_cpf_cnpj) ? $this->view->parametros->esconder_cpf_cnpj : 'f';
                $this->view->parametros->cmftpcoid_destino   = isset($this->view->parametros->cmftpcoid_destino)   ? $this->view->parametros->cmftpcoid_destino : '' ;
                $this->view->parametros->migrar_para        = isset($this->view->parametros->migrar_para)       ? $this->view->parametros->migrar_para : '';

                if(in_array($this->view->parametros->cmtoid, $this->idsMigracao)) {
                    $this->view->parametros->id_migracao_ex  = $this->view->parametros->cmtoid;
                } else {
                    $this->view->parametros->id_migracao_ex = '';
                }


                if($this->view->parametros->cmftpcoid_destino == '') {
                    $this->view->parametros->cmftpcoid_destino = !empty($this->view->parametros->migrar_para)   ? $this->view->parametros->migrar_para : '' ;
                }

                //Bloco Dados Contratuais
                $this->view->parametros->acoes_lote_migracao = isset($this->view->parametros->acoes_lote_migracao) ? $this->view->parametros->acoes_lote_migracao : '';
                $this->view->parametros->acoes_lote          = isset($this->view->parametros->acoes_lote)          ? $this->view->parametros->acoes_lote : 'f';
                $this->view->parametros->anexar_arquivo      = isset($this->view->parametros->anexar_arquivo)      ? $this->view->parametros->anexar_arquivo : 'f';

                if( ($this->view->parametros->acoes_lote != 't') && (in_array($this->view->parametros->cmtoid, $this->idsMigracao)) ) {
                    $this->view->parametros->acoes_lote = !empty($this->view->parametros->acoes_lote_migracao) ? $this->view->parametros->acoes_lote_migracao : '';
                }

                $this->view->parametros->produto_siggo          = isset($this->view->parametros->produto_siggo)         ? $this->view->parametros->produto_siggo : 'f' ;

                //Produto SIGGO nao pode ter acoes em lote
                if(($this->view->parametros->acoes_lote == 't') && ($this->view->parametros->produto_siggo == 't')) {

                    $this->view->parametros->acoes_lote = 'f';
                }


                $this->view->parametros->cmftppoid              = isset($this->view->parametros->cmftppoid)             ? $this->view->parametros->cmftppoid : '' ;
                $this->view->parametros->cmftppoid_subtitpo     = isset($this->view->parametros->cmftppoid_subtitpo)    ? $this->view->parametros->cmftppoid_subtitpo : '' ;
                $this->view->parametros->cmpgmodificacao_lote   = isset($this->view->parametros->cmpgmodificacao_lote)  ? $this->view->parametros->cmpgmodificacao_lote : 'f' ;
                $this->view->parametros->clinome                = isset($this->view->parametros->clinome)               ? $this->view->parametros->clinome : '' ;
                $this->view->parametros->cmfveioid              = isset($this->view->parametros->cmfveioid)             ? $this->view->parametros->cmfveioid : '' ;
                $this->view->parametros->veiplaca               = isset($this->view->parametros->veiplaca)              ? $this->view->parametros->veiplaca : '' ;
                $this->view->parametros->cmfveioid_novo         = isset($this->view->parametros->cmfveioid_novo)        ? $this->view->parametros->cmfveioid_novo : '' ;
                $this->view->parametros->exibir_novo_veiculo    = isset($this->view->parametros->exibir_novo_veiculo)   ? $this->view->parametros->exibir_novo_veiculo : 'f' ;

                if(!isset($this->view->parametros->cmfcvgoid)) {
                   $this->view->parametros->cmfcvgoid = isset($this->view->parametros->cmfcvgoid_aux) ? $this->view->parametros->cmfcvgoid_aux : '';
                } else {
                    $this->view->parametros->cmfcvgoid = !empty($this->view->parametros->cmfcvgoid) ?  $this->view->parametros->cmfcvgoid : '';
                }

                //Bloco Faturamento
                $this->view->parametros->cmdfpnum_parcela                 = isset($this->view->parametros->cmdfpnum_parcela)                 ? $this->view->parametros->cmdfpnum_parcela                 : '' ;
                $this->view->parametros->cmdfpdebito_agencia              = isset($this->view->parametros->cmdfpdebito_agencia)              ? $this->view->parametros->cmdfpdebito_agencia              : '' ;
                $this->view->parametros->cmdfpdebito_cc                   = isset($this->view->parametros->cmdfpdebito_cc)                   ? $this->view->parametros->cmdfpdebito_cc                   : '' ;
                $this->view->parametros->cmdfpcartao                      = isset($this->view->parametros->cmdfpcartao)                      ? $this->view->parametros->cmdfpcartao                      : '' ;
                $this->view->parametros->cmdfpnome_portador               = isset($this->view->parametros->cmdfpnome_portador)               ? $this->view->parametros->cmdfpnome_portador               : '' ;
                $this->view->parametros->cmdfpcartao_vencimento           = isset($this->view->parametros->cmdfpcartao_vencimento)           ? $this->view->parametros->cmdfpcartao_vencimento           : '' ;                
                $this->view->parametros->cmdfpforcoid                     = isset($this->view->parametros->cmdfpforcoid)                     ? $this->view->parametros->cmdfpforcoid                     : '' ;
                $this->view->parametros->cmdfpdebito_banoid               = isset($this->view->parametros->cmdfpdebito_banoid)               ? $this->view->parametros->cmdfpdebito_banoid               : '' ;
                $this->view->parametros->forma_pgto                       = isset($this->view->parametros->forma_pgto)                       ? $this->view->parametros->forma_pgto                       : '' ;
                $this->view->parametros->cmdfpisencao_taxa                = isset($this->view->parametros->cmdfpisencao_taxa)                ? $this->view->parametros->cmdfpisencao_taxa                : 'f' ;
                $this->view->parametros->cmdfppagar_cartao                = isset($this->view->parametros->cmdfppagar_cartao)                ? $this->view->parametros->cmdfppagar_cartao                : 'f' ;
                $this->view->parametros->cmdfpisencao_locacao             = isset($this->view->parametros->cmdfpisencao_locacao)             ? $this->view->parametros->cmdfpisencao_locacao             : 'f' ;
                $this->view->parametros->cmdfpvencimento_fatura           = isset($this->view->parametros->cmdfpvencimento_fatura)           ? $this->view->parametros->cmdfpvencimento_fatura           : '' ;
                $this->view->parametros->cmdfpvlr_monitoramento_tabela    = isset($this->view->parametros->cmdfpvlr_monitoramento_tabela)    ? $this->view->parametros->cmdfpvlr_monitoramento_tabela    : '' ;
                $this->view->parametros->cmdfpvlr_monitoramento_negociado = isset($this->view->parametros->cmdfpvlr_monitoramento_negociado) ? $this->view->parametros->cmdfpvlr_monitoramento_negociado : '' ;
                $this->view->parametros->cmdfpobroid_taxa                 = isset($this->view->parametros->cmdfpobroid_taxa)                 ? $this->view->parametros->cmdfpobroid_taxa                 : '' ;
                $this->view->parametros->cmdfpvlr_taxa_tabela             = isset($this->view->parametros->cmdfpvlr_taxa_tabela)             ? $this->view->parametros->cmdfpvlr_taxa_tabela             : '' ;
                $this->view->parametros->cmdfpvlr_taxa_negociado          = isset($this->view->parametros->cmdfpvlr_taxa_negociado)          ? $this->view->parametros->cmdfpvlr_taxa_negociado          : '' ;
                $this->view->parametros->cmdfpvlr_locacao_negociado       = isset($this->view->parametros->cmdfpvlr_locacao_negociado)       ? $this->view->parametros->cmdfpvlr_locacao_negociado       : '' ;
                $this->view->parametros->cmdfpvlr_locacao_tabela          = isset($this->view->parametros->cmdfpvlr_locacao_tabela)          ? $this->view->parametros->cmdfpvlr_locacao_tabela          : '' ;
                $this->view->parametros->cmdfpcpvoid                      = isset($this->view->parametros->cmdfpcpvoid)                      ? $this->view->parametros->cmdfpcpvoid                      : '' ;

                if( empty($this->view->parametros->cmdfpcpvoid) ) {
                    $this->view->parametros->cmdfpcpvoid                  = isset($this->view->parametros->cmdfpcpvoid_aux)                  ? $this->view->parametros->cmdfpcpvoid_aux                  : '' ;
                }

                //DEfine se sera exibido o bloco com os campos para pgto com cartao de credito
                if($this->view->parametros->cmdfppagar_cartao == 't') {
                    $this->view->parametros->exibir_bloco_credito = 't';
                } else if( ($this->view->parametros->forma_pgto == 'credito') && ($this->view->parametros->produto_siggo != 't') ) {
                    $this->view->parametros->exibir_bloco_credito = 't';
                } else {
                    $this->view->parametros->exibir_bloco_credito = 'f';
                }

                //Define se sera exibido o checkbox [Pagar com Cartao de Credito]
                if ( ($this->view->parametros->produto_siggo == 't')
                    && ($this->view->parametros->forma_pgto == 'credito')
                    && ($this->view->parametros->cmdfpisencao_taxa == 'f') ) {
                   $this->view->parametros->exibir_pagar_cartao = 't';
                } else {
                    $this->view->parametros->exibir_pagar_cartao = 'f';
                }

                //Bloco Contatos
                if(!isset($this->view->parametros->contatos)) {
                    $this->view->parametros->contatos = new stdClass();
                    $this->view->parametros->contatos->cmctnome        = isset($this->view->parametros->contatos_nome)         ? $this->view->parametros->contatos_nome : array() ;
                    $this->view->parametros->contatos->cmctcpf         = isset($this->view->parametros->contatos_cpf)          ? $this->view->parametros->contatos_cpf : array() ;
                    $this->view->parametros->contatos->cmctrg          = isset($this->view->parametros->contatos_rg)           ? $this->view->parametros->contatos_rg : array() ;
                    $this->view->parametros->contatos->cmctfone_res    = isset($this->view->parametros->contatos_fone_res)     ? $this->view->parametros->contatos_fone_res : array() ;
                    $this->view->parametros->contatos->cmctfone_com    = isset($this->view->parametros->contatos_fone_com)     ? $this->view->parametros->contatos_fone_com : array() ;
                    $this->view->parametros->contatos->cmctfone_cel    = isset($this->view->parametros->contatos_fone_cel)     ? $this->view->parametros->contatos_fone_cel : array() ;
                    $this->view->parametros->contatos->cmctfone_nextel = isset($this->view->parametros->contatos_nextel)       ? $this->view->parametros->contatos_nextel : array() ;
                    $this->view->parametros->contatos->cmctobservacao  = isset($this->view->parametros->contatos_obs)          ? $this->view->parametros->contatos_obs : array() ;
                    $this->view->parametros->contatos->cmctautorizada  = isset($this->view->parametros->contatos_autorizada)   ? $this->view->parametros->contatos_autorizada : array() ;
                    $this->view->parametros->contatos->cmctemergencia  = isset($this->view->parametros->contatos_emergencia)   ? $this->view->parametros->contatos_emergencia : array() ;
                    $this->view->parametros->contatos->cmctinstalacao  = isset($this->view->parametros->contatos_instalacao)   ? $this->view->parametros->contatos_instalacao : array() ;
                    $this->view->parametros->contatos                  = $this->reordenarListaFormulario($this->view->parametros->contatos);
                }

                 //Bloco Acessorios
                if(!isset($this->view->parametros->acessorios)) {
                    $this->view->parametros->acessorios = new stdClass();
                    $this->view->parametros->acessorios->obrobrigacao       = isset($this->view->parametros->acessorio_nome)            ? $this->view->parametros->acessorio_nome : array() ;
                    $this->view->parametros->acessorios->cmsobroid          = isset($this->view->parametros->acessorio_obroid)          ? $this->view->parametros->acessorio_obroid : array() ;
                    $this->view->parametros->acessorios->cmssituacao        = isset($this->view->parametros->acessorio_situacao)        ? $this->view->parametros->acessorio_situacao : array() ;
                    $this->view->parametros->acessorios->cmsvalor_negociado = isset($this->view->parametros->acessorio_valor_negociado) ? $this->view->parametros->acessorio_valor_negociado : array() ;
                    $this->view->parametros->acessorios->cmsvalor_tabela    = isset($this->view->parametros->acessorio_valor_tabela)    ? $this->view->parametros->acessorio_valor_tabela : array() ;
                    $this->view->parametros->acessorios->cmsqtde            = isset($this->view->parametros->acessorio_qtde)            ? $this->view->parametros->acessorio_qtde : array() ;
                    $this->view->parametros->acessorios->cmscpvoid          = isset($this->view->parametros->acessorio_cpvoid)          ? $this->view->parametros->acessorio_cpvoid : array() ;
                    $this->view->parametros->acessorios                     = $this->reordenarListaFormulario($this->view->parametros->acessorios);
                }

                //Combos
                $this->view->comboTipoProposta      = $this->dao->recuperarTipoProposta();
                $this->view->comboMotivoSubstituicao = $this->dao->recuperarMotivoSubstituicaoClasse($this->view->parametros->cmtoid, false);

                if($this->view->parametros->cmtoid == self::TIPO_EFETIVACAO_DEMO) {

                    $this->view->comboTipoContrato  = $this->dao->recuperarTipoContrato(false, strval(self::TIPO_EFETIVACAO_DEMO));

                } else {
                $this->view->comboTipoContrato      = $this->dao->recuperarTipoContrato(false);
                }

                $this->view->comboClasseContrato    = $this->dao->recuperarClasseContrato();
                $this->view->comboExecutivo         = $this->dao->recuperarExecutivo();
                $this->view->comboVigencia          = $this->dao->recuperarContratoVigencia();
                $this->view->comboTipoModificacao   = $this->dao->recuperarTipoModificacao();
                $this->view->comboFormaPagamento    = $this->dao->recuperarFormaPagamento();
                $this->view->comboParcelamento      = $this->dao->recuperarParcelamento();
                $this->view->comboTaxas             = $this->dao->recuperarTaxas();
                $this->view->comboDataVencimento    = $this->dao->recuperarDiasVencimento();
                $this->view->comboSubTipo           = $this->dao->recuperarSubTipoProposta($this->view->parametros->cmftppoid, false);

                foreach ($this->view->comboTaxas as $taxas) {
                    $taxas->cmdfpvlr_taxa_negociado = $this->tratarMoeda($taxas->cmdfpvlr_taxa_negociado, 'A');
                }

                if( $this->view->sub_tela == 'aba_itens') {
                   $this->view->parametros->migracaoLote    = isset($this->view->parametros->migracaoLote)  ? $this->view->parametros->migracaoLote : array() ;
                   $this->view->parametros->dadosUpDown     = isset($this->view->parametros->dadosUpDown)   ? $this->view->parametros->dadosUpDown : array() ;
                   $this->view->parametros->contratos_marcados     = isset($this->view->parametros->contratos_marcados)   ? $this->view->parametros->contratos_marcados : array() ;

                }

                $this->view->parametros->anexos  = isset($this->view->parametros->anexos)   ? $this->view->parametros->anexos : array() ;


                if( ($this->view->parametros->cmpgmodificacao_lote == 't')
                     && ($this->view->parametros->produto_siggo != 't')
                     && (self::TIPO_UPGRADE_MOBILE_EQPTO_CONVENCIONAL != $this->view->parametros->cmtoid)
                    )
                {
                    $this->view->parametros->exibir_lote = TRUE;
                } else {
                    $this->view->parametros->exibir_lote = FALSE;
                }

                break;
            case "listarContratos":
                $this->view->tela = 'lista_contratos';

                $this->view->parametros->connumero  = isset($this->view->parametros->connumero) ? $this->view->parametros->connumero : '' ;
                $this->view->parametros->listaContratos = isset($this->view->parametros->listaContratos) ? $this->view->parametros->listaContratos : array();

                break;
            case "detalhar":
            case "pesquisarAcessorio":
            case "excluirAcessorio":
                $this->view->tela = 'detalhes';
                 if(!empty($this->view->parametros->sub_tela)){
                    $this->view->sub_tela = $this->view->parametros->sub_tela ;
                } else{
                    $this->view->sub_tela = 'aba_dados_principais';
                }

                $this->view->parametros->contratos_marcados     = isset($this->view->parametros->contratos_marcados)   ? $this->view->parametros->contratos_marcados : array() ;
                $this->view->parametros->mdfoid                 = isset($this->view->parametros->mdfoid)                ? $this->view->parametros->mdfoid : '' ;
                $this->view->parametros->mdfcmtoid              = isset($this->view->parametros->mdfcmtoid)             ? $this->view->parametros->mdfcmtoid : '' ;
                $this->view->parametros->cmtoid                 = isset($this->view->parametros->cmtoid)                ? $this->view->parametros->cmtoid : '' ;
                $this->view->parametros->cmptgera_contrato_automatico = isset($this->view->parametros->cmptgera_contrato_automatico)  ? $this->view->parametros->cmptgera_contrato_automatico : 't' ;

                if($this->view->sub_tela == 'aba_acessorios'){

                    $this->view->comboAcessorio = $this->dao->recuperarAcessorioPorModificacao($this->view->parametros->mdfoid);

                    $this->view->parametros->connumero  = isset($this->view->parametros->connumero) ? $this->view->parametros->connumero : '' ;
                    $this->view->parametros->veiplaca   = isset($this->view->parametros->veiplaca)  ? $this->tratarTextoInput($this->view->parametros->veiplaca) : '' ;
                    $this->view->parametros->veichassi  = isset($this->view->parametros->veichassi) ? $this->tratarTextoInput($this->view->parametros->veichassi) : '' ;
                    $this->view->parametros->obroid     = isset($this->view->parametros->obroid)    ? $this->view->parametros->obroid : '' ;

                } else if ($this->view->sub_tela == 'aba_itens'){
                    //Aba Itens
                    $this->view->parametros->contratos              = isset($this->view->parametros->contratos)             ? $this->view->parametros->contratos : array();
                    $this->view->parametros->observacao_desfazer    = isset($this->view->parametros->observacao_desfazer)   ? $this->view->parametros->observacao_desfazer : '';

                } else if ($this->view->sub_tela == 'aba_historico') {

                    $this->view->parametros->historico              = isset($this->view->parametros->historico)              ? $this->view->parametros->historico                              : array();

                } else {

                //Bloco dados principais
                $this->view->parametros->data_modificacao       = isset($this->view->parametros->data_modificacao)      ? $this->view->parametros->data_modificacao : '' ;
                $this->view->parametros->motivo_modificacao     = isset($this->view->parametros->motivo_modificacao)    ? $this->view->parametros->motivo_modificacao : '' ;
                $this->view->parametros->tipo_modificacao       = isset($this->view->parametros->tipo_modificacao)      ? $this->view->parametros->tipo_modificacao : '' ;
                $this->view->parametros->substituicao_descricao = isset($this->view->parametros->substituicao_descricao)? $this->view->parametros->substituicao_descricao : '' ;
                $this->view->parametros->usuario_modificacao    = isset($this->view->parametros->usuario_modificacao)   ? $this->view->parametros->usuario_modificacao : '' ;
                $this->view->parametros->cliente_modificacao    = isset($this->view->parametros->cliente_modificacao)   ? $this->view->parametros->cliente_modificacao : '' ;
                $this->view->parametros->mdfclioid              = isset($this->view->parametros->mdfclioid)             ? $this->view->parametros->mdfclioid : '' ;


                //Bloco Dados Contratuais
                $this->view->parametros->vigencia               = isset($this->view->parametros->vigencia)              ? $this->view->parametros->vigencia : '' ;
                $this->view->parametros->executivo              = isset($this->view->parametros->executivo)             ? $this->view->parametros->executivo : '' ;
                $this->view->parametros->dmv                    = isset($this->view->parametros->dmv)                   ? $this->view->parametros->dmv : '' ;
                $this->view->parametros->tipo_contrato_novo     = isset($this->view->parametros->tipo_contrato_novo)    ? $this->view->parametros->tipo_contrato_novo : '' ;
                $this->view->parametros->cmfoid                 = isset($this->view->parametros->cmfoid)                ? $this->view->parametros->cmfoid : '' ;
                //Bloco Faturamento
                $this->view->parametros->forma_pgto             = isset($this->view->parametros->forma_pgto)            ? $this->view->parametros->forma_pgto : '' ;
                $this->view->parametros->monitoramento          = isset($this->view->parametros->monitoramento)         ? $this->tratarMoeda($this->view->parametros->monitoramento, 'A') : '' ;
                $this->view->parametros->locacao                = isset($this->view->parametros->locacao)               ? $this->tratarMoeda($this->view->parametros->locacao, 'A') : '' ;
                $this->view->parametros->taxa                   = isset($this->view->parametros->taxa)                  ? $this->tratarMoeda($this->view->parametros->taxa, 'A') : '' ;
                $this->view->parametros->taxa_descricao         = isset($this->view->parametros->taxa_descricao)        ? $this->view->parametros->taxa_descricao : '' ;
                $this->view->parametros->taxa_isencao           = isset($this->view->parametros->taxa_isencao)          ? $this->view->parametros->taxa_isencao : 'f' ;
                $this->view->parametros->cmdfppagar_cartao = isset($this->view->parametros->cmdfppagar_cartao)? $this->view->parametros->cmdfppagar_cartao : 'f' ;

                    $this->view->parametros->exibie_msg             = isset($this->view->parametros->exibie_msg)            ? $this->view->parametros->exibie_msg : 'f' ;
                }
                break;

            case "recuperarAnaliseCredito":
                $this->view->tela = 'analise_credito';

                $this->view->parametros->dadosAnalise = isset($this->view->parametros->dadosAnalise) ? $this->view->parametros->dadosAnalise : array();
                $this->view->parametros->combo_status  = isset($this->view->parametros->combo_status) ? $this->view->parametros->combo_status : '';
                $this->view->parametros->motivo  = isset($this->view->parametros->motivo) ? $this->view->parametros->motivo : '';
                $this->view->parametros->campo_liberacao = isset($this->view->parametros->campo_liberacao) ? $this->view->parametros->campo_liberacao : '';
                $this->view->parametros->check_periodo = isset($this->view->parametros->check_periodo) ? $this->view->parametros->check_periodo : '';
                $this->view->parametros->opcao = isset($this->view->parametros->opcao) ? $this->view->parametros->opcao : array();
                break;
            case "pesquisar":
            case "index":

                 //SE data inicial nao for informada assume a data final
                if(isset($this->view->parametros->data_inicial)) {
                    if(empty($this->view->parametros->data_inicial)){
                        $this->view->parametros->data_inicial = isset($this->view->parametros->data_final) ? $this->view->parametros->data_final : '' ;
                    }
                } else {
                     $this->view->parametros->data_inicial = isset($this->view->parametros->data_final) ? $this->view->parametros->data_final : '' ;
                }

                //SE data final nao for informada assume a data inicial
                if(isset($this->view->parametros->data_final)) {
                    if(empty($this->view->parametros->data_final)){
                        $this->view->parametros->data_final = isset($this->view->parametros->data_inicial) ? $this->view->parametros->data_inicial : '' ;
                    }
                } else {
                     $this->view->parametros->data_final = isset($this->view->parametros->data_inicial) ? $this->view->parametros->data_inicial : '' ;
                }

                $this->view->parametros->tela_pesquisa      = isset($this->view->parametros->tela_pesquisa)      ? $this->view->parametros->tela_pesquisa : '';
                $this->view->parametros->clinome_pesq       = isset($this->view->parametros->clinome_pesq)       ? $this->tratarTextoInput($this->view->parametros->clinome_pesq) : '' ;
                $this->view->parametros->clioid_pesq        = isset($this->view->parametros->clioid_pesq)        ? $this->view->parametros->clioid_pesq : '' ;
                $this->view->parametros->connumero          = isset($this->view->parametros->connumero)          ? $this->view->parametros->connumero : '' ;
                $this->view->parametros->chassi             = isset($this->view->parametros->chassi)             ? $this->tratarTextoInput($this->view->parametros->chassi) : '' ;
                $this->view->parametros->placa              = isset($this->view->parametros->placa)              ? $this->tratarTextoInput($this->view->parametros->placa) : '' ;

                if($this->view->parametros->tela_pesquisa == 'contratos_vencer') {

                $this->view->tela = 'contratos_vencer';

                    $this->view->comboTipoModificacaoContratoVencer  = $this->dao->recuperarTipoModificacaoContratoVencer(array(7,8));

                } else {

                    $this->view->tela = 'pesquisa';
                    $this->view->comboDepartamento   = $this->dao->recuperarDadosDepartamento();
                    $this->view->comboGrupoModificacao = $this->dao->recuperarGrupoModificacao();

                    $this->view->parametros->mdfoid_pesq        = isset($this->view->parametros->mdfoid_pesq)        ? $this->view->parametros->mdfoid_pesq : '' ;
                    $this->view->parametros->mdfmsuboid         = isset($this->view->parametros->mdfmsuboid)         ? $this->view->parametros->mdfmsuboid : '' ;
                    $this->view->parametros->cmgoid             = isset($this->view->parametros->cmgoid)             ? $this->view->parametros->cmgoid : '' ;
                    $this->view->parametros->msubdescricao      = isset($this->view->parametros->msubdescricao)      ? $this->view->parametros->msubdescricao : '' ;
                    $this->view->parametros->depoid             = isset($this->view->parametros->depoid)             ? $this->view->parametros->depoid : '' ;
                    $this->view->parametros->mdfusuoid_cadastro = isset($this->view->parametros->mdfusuoid_cadastro) ? $this->view->parametros->mdfusuoid_cadastro : '' ;
                    $this->view->parametros->tipo_resultado     = isset($this->view->parametros->tipo_resultado)     ? $this->view->parametros->tipo_resultado : 'T' ;
                    $this->view->parametros->tipo_pessoa        = isset($this->view->parametros->tipo_pessoa)        ? $this->view->parametros->tipo_pessoa : '' ;
                    $this->view->parametros->status             = isset($this->view->parametros->status)             ? $this->view->parametros->status : '' ;
                    $this->view->parametros->status_financeiro  = isset($this->view->parametros->status_financeiro)  ? $this->view->parametros->status_financeiro  : '' ;
                    }

                break;

            case "gerarContratos":
                $this->view->parametros->cartao_codigo      = isset($this->view->parametros->cartao_codigo)     ? $this->view->parametros->cartao_codigo : '' ;
                $this->view->parametros->mdfoid             = isset($this->view->parametros->mdfoid)            ? $this->view->parametros->mdfoid : '' ;
                $this->view->parametros->mdfcmtoid          = isset($this->view->parametros->mdfcmtoid)         ? $this->view->parametros->mdfcmtoid : '' ;
                $this->view->parametros->cmtoid             = isset($this->view->parametros->cmtoid)            ? $this->view->parametros->cmtoid : '' ;
                $this->view->parametros->produto_siggo      = isset($this->view->parametros->produto_siggo)     ? $this->view->parametros->produto_siggo : 'f' ;
                $this->view->parametros->mdfstatus          = isset($this->view->parametros->mdfstatus)         ? $this->view->parametros->mdfstatus : '' ;
                $this->view->parametros->cmdfppagar_cartao  = isset($this->view->parametros->cmdfppagar_cartao) ? $this->view->parametros->cmdfppagar_cartao : 'f' ;
                $this->view->parametros->autorizar          = isset($this->view->parametros->autorizar)         ? $this->view->parametros->autorizar : 'f' ;
                
                break;
            case "desfazerModificacao":
                $this->view->parametros->observacao_desfazer = isset($this->view->parametros->observacao_desfazer) ? $this->view->parametros->observacao_desfazer : '';
                $this->view->parametros->mdfoid              = isset($this->view->parametros->mdfoid)              ? $this->view->parametros->mdfoid : '' ;
                $this->view->parametros->contratos_marcados  = isset($this->view->parametros->contratos_marcados)  ? $this->view->parametros->contratos_marcados : array() ;
                break;
        }

    }

    /**
    * Responsavel por tratar e retornar o resultado da lista de modificacoes de contrato
    * @author Andre L. Zilz
    */
    public function listarContratos() {

        $this->view->parametros = $this->tratarParametros();

        try{

            if(empty($this->view->parametros->connumero)) {
                throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
            } else {

                $this->view->parametros->listaContratos =  $this->dao->recuperarModificacaoContrato($this->view->parametros->connumero);

                if(empty($this->view->parametros->listaContratos)){
                    throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
                }
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->index($this->view->parametros);

    }

    /**
     * recupera os registros pendentes de analise de crédito
     * @param  stdClass $dados
     */
    public function recuperarAnaliseCredito($dados = null) {

        if(is_null($dados)){
            $this->view->parametros = $this->tratarParametros();
            $this->inicializarParametros();
        }

        $this->view->tela = 'analise_credito';

        $this->view->parametros->dadosAnalise = $this->dao->recuperaDadosAnaliseCredito();

        $this->index($this->view->parametros);
    }


    public function atualizarAnaliseCredito() {

        $this->view->parametros = $this->tratarParametros();
        $this->inicializarParametros();

        $this->view->tela = 'analise_credito';

        try {
             if(isset($_POST) && !empty($_POST)) {

                    $this->dao->begin();

                    $this->validarCamposCadastro($this->view->parametros);

                    $parametros  = $this->view->parametros;

                    $dadosAprovador = $this->dao->buscaEmailUsuario($this->usuoid);

                    // Realiza insercao no banco
                    foreach ($parametros->opcao as $cmacoid) {

                        $arrayDados = array (
                            'cmacoid' => $cmacoid,
                            'cmacmotivo_status' => $this->tratarTextoInput($parametros->motivo),
                            'cmacstatus' => $parametros->combo_status
                        );

                        if($parametros->combo_status != 'N') {

                            if(strlen($parametros->campo_liberacao) > 0 && !isset($parametros->check_periodo)) {
                                $dataTemp = explode('/', $parametros->campo_liberacao);
                                $dataLiberacao = $dataTemp[2].'-'.$dataTemp[1].'-'.$dataTemp[0];

                                $arrayDados = array_merge($arrayDados,array("cmacdt_liberacao_limite" => $dataLiberacao));
                            }

                            if(strlen($parametros->campo_liberacao) == 0 && isset($parametros->check_periodo)) {
                                $arrayDados = array_merge($arrayDados,array("cmacliberado_periodo_indeterminado" => true));
                            }

                        }

                        $clienteAprovacao = $this->dao->atualizaDadosAnalise($arrayDados);

                        if($clienteAprovacao != '') {

                            $mdfoidLista = $this->dao->atualizarStatusFinanceiroPorID($parametros->combo_status, $cmacoid);

                            foreach ($mdfoidLista as $dado) {

                                if($parametros->combo_status == 'A') {
                                    $this->dao->inserirHistoricoModificacao($dado->mdfoid, "Crédito aprovado pelo financeiro.");
                                } else {
                                    $this->dao->inserirHistoricoModificacao($dado->mdfoid, "Modificação Cancelada por crédito não aprovado.");
                        }
                            }

                    }

                        $this->enviaEmailAnaliseCredito($cmacoid,$arrayDados,$dadosAprovador);

                        $this->dao->commit();

                    }
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
            }
        } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();


        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->recuperarAnaliseCredito();
    }


    public function enviaEmailAnaliseCredito($cmacoid,$arrayDados,$dadosAprovador) {

        $mail = new PHPMailer();
        $mail->isSMTP();

        $mail->ClearAllRecipients();
        $mail->FROM = 'sascar@sascar.com.br';
        $mail->FromName = 'Intranet Sascar';
        $mail->Subject = 'Resultado análise crédito manual';

        $status = '';
        $info = '';
        $nomeCliente = '';

        if($arrayDados['cmacstatus'] == 'A') {
            $status = 'Crédito aprovado. Solicitação nº: ' . intval($cmacoid);
            $info = ' Crédito aprovado pelo usuário: ';
        }

        if($arrayDados['cmacstatus'] == 'N') {
            $status = 'Crédito não aprovado. Solicitação nº: ' . intval($cmacoid);
            $info = ' Crédito nao aprovado pelo usuário: ';
        }

        $dadosAprovacao = $this->dao->recuperaDadosAprovacao($cmacoid);
        $dadosCliente = $this->dao->recuperaDadosCliente($dadosAprovacao->cmacclioid);
        $dadosUsuario = $this->dao->buscaEmailUsuario($dadosAprovacao->cmacusuoid_solicitante);

        if(isset($dadosUsuario->usuemail)) {
            $corpoEmail .= '<h3>' . $status . '</h3>';
            $corpoEmail .= $info . $dadosAprovador->nm_usuario . " <br />";
            $corpoEmail .= 'Motivo: ' . $arrayDados['cmacmotivo_status'] . " <br />";
            $corpoEmail .= 'Cliente: ' . $dadosCliente->clinome;

            $mail->AddAddress($dadosUsuario->usuemail);

            $mail->MsgHTML($corpoEmail);
            $mail->Send();
        }


    }

    /**
     * Responsavel por tratar e retornar o resultado das pesquisas
     * @author Andre L. Zilz
     */
    public function pesquisar() {

        $this->view->statusPesquisa = FALSE;
        $this->view->dados = array();
        $this->view->arquivoCSV = array();

        //tratamento para contornar problema com o componente de paginacao
        if(!empty($_POST)) {
            unset($_GET);
        }

        try {

            $this->view->parametros = $this->tratarParametros();

            $this->inicializarParametros();

            if($this->view->parametros->tela_pesquisa == 'contratos_vencer') {

                //Condicao para diferenciar se a chamada veio do formulario ou da Aba
                if(!empty($this->view->parametros->form_pesquisa_contratos_vencer)) {
                    $this->pesquisarContratosVencer($this->view->parametros);
                    $this->view->statusPesquisa = TRUE;
                } else {
                    $this->destruirSessaoPaginacao();
                }

            } else {
                $this->pesquisarModificacao($this->view->parametros);
                $this->view->statusPesquisa = TRUE;
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();

        }

        require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/index.php";


    }

    /**
     * Pesquisa por modificoes efetivadas
     * @author Andre L. Zilz
     */
    private function pesquisarModificacao($filtros) {

            $paginacao = new PaginacaoComponente();

        $this->validarCamposCadastro($filtros);

        $resultadoPesquisa = $this->dao->pesquisarModificacao($filtros);

        $this->view->totalResultados = $resultadoPesquisa[0]->total_registros;


        //Valida se houve resultado na pesquisa
        if ($this->view->totalResultados == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
        }

        if($filtros->tipo_resultado != 'A') {

             $campos = array(
                    '' => 'Escolha',
                    'mdfdt_cadastro' => 'Data',
                    'mdfoid' => 'Nº da Modificacao',
                    'mdfstatus' => 'Status Modificação',
                    'mdfstatus_financeiro' => 'Status Financeiro',
                    'cmgdescricao' => 'Grupo',
                    'cmtdescricao' => 'Tipo',
                    'cliente' => 'Cliente'
                    );

             if ($paginacao->setarCampos($campos)) {
                $this->view->ordenacao = $paginacao->gerarOrdenacao('mdfdt_cadastro, mdfoid');
                $this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);
            }

            $this->view->dados = $this->dao->pesquisarModificacao($filtros, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao());

        } else {
            $resultadoPesquisa = $this->dao->pesquisarModificacaoCsv($filtros);

            $this->view->arquivoCSV = $this->gerarArquivoCSV($resultadoPesquisa);

        }

    }

    /**
     * Pesquisa por modificoes efetivadas
     * @author Andre L. Zilz
     */
    public function pesquisarAcessorio() {

        try {

            $this->view->parametros = $this->tratarParametros();

            $this->view->tela == 'detalhar';
            $this->view->sub_tela == 'aba_acessorios';

            $this->inicializarParametros();

            $this->view->parametros->acessorios = $this->dao->pesquisarContratoModificacaoAcessorio($this->view->parametros);

            if(empty($this->view->parametros->acessorios)) {
                $this->view->mensagemAlerta = self::MENSAGEM_NENHUM_REGISTRO;
            }


        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();

        }

       require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/index.php";

    }

    /**
     * Pesquisa por contratos que estao para vencer (Renovacao Siggo e Demo)
     * @author Andre L. Zilz
     */
    private function pesquisarContratosVencer($filtros) {

        $paginacao = new PaginacaoComponente();

        $this->validarCamposCadastro($filtros);

        $resultadoPesquisa = $this->dao->pesquisarContratosVencer($filtros);

        $this->view->totalResultados = ($resultadoPesquisa[0]->total_registros + $resultadoPesquisa[1]->total_registros);


        //Valida se houve resultado na pesquisa
        if ($this->view->totalResultados == 0) {
            throw new Exception(self::MENSAGEM_NENHUM_REGISTRO);
            }

        $campos = array(
                '' => 'Escolha',
                'inicio_vigencia::DATE' => 'Data',
                'fim_vigencia::DATE' => 'Vencimento',
                'connumero' => 'Contrato',
                'tipo_contrato' => 'Tipo Contrato',
                'cliente' => 'Cliente',
                'eqcdescricao' => 'Classe'
                );

         if ($paginacao->setarCampos($campos)) {
            $this->view->ordenacao = $paginacao->gerarOrdenacao('inicio_vigencia::DATE, connumero');
            $this->view->paginacao = $paginacao->gerarPaginacao($this->view->totalResultados);
        }

        $this->view->dados = $this->dao->pesquisarContratosVencer($filtros, $paginacao->buscarPaginacao(), $paginacao->buscarOrdenacao());

    }

    /**
    * Responsavel por receber exibir o formulario de cadastro em lote e invocar
    * o metodo para salvar os dados
    *
    * @author Andre L. Zilz
    *
    */
    public function cadastrarModificacaoLote(){

        try{

            $this->view->parametros = $this->tratarParametros();

            $this->inicializarParametros();

            $this->validarCamposCadastro($this->view->parametros);


            if($this->view->parametros->cmptleitura_arquivo != 't') {
                $this->view->parametros->dadosUpDown = $this->dao->recuperarContratoDowngradeUpgrade($this->view->parametros);
            }

            if ( ($this->view->parametros->cmptleitura_arquivo != 't')
                && (!empty($this->view->parametros->dadosUpDown)) ) {
                    $this->view->parametros->exibe_UpDown = true;
            } else{
                $this->view->parametros->exibe_UpDown = false;
            }

            //Armazena em sessao antes da efetivacao do cadastro
            $_SESSION['dados_modificacao'] = serialize($this->view->parametros);


       } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
           $this->view->sub_tela = 'aba_dados_principais';
        }

        require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/index.php";

    }

    /**
    * Responsavel por receber exibir o formulario de cadastro de anexos
    *
    * @author Andre L. Zilz
    *
    */
    public function cadastrarAnexo(){

        try{

            $this->view->parametros = unserialize($_SESSION['dados_modificacao']);
            $listaArquivosAnexos = $this->view->parametros->anexos;

            $this->view->parametros = $this->tratarParametros();
            $this->view->parametros->anexos = $listaArquivosAnexos;

            $this->inicializarParametros();

            //Armazena em sessao antes da efetivacao do cadastro
            $_SESSION['dados_modificacao'] = serialize($this->view->parametros);

            $this->validarCamposCadastro($this->view->parametros);

       } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
           $this->view->sub_tela = 'aba_dados_principais';
        }

        require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/index.php";

    }

    /**
     * Responsavel por receber exibir o formulario de cadastro ou invocar
     * o metodo para salvar os dados
     * @author Andre L. Zilz
     * @param stdClass $parametros Dados do cadastro, para edicao (opcional)
     * @return void
     */
    public function cadastrar($parametros = null) {

        try{

            if(isset($_SESSION['dados_modificacao'])) {

                $contratosMarcados = $this->tratarParametros();

                $this->view->parametros = unserialize($_SESSION['dados_modificacao']);
                $this->view->parametros->contratosMarcados = $contratosMarcados->contratos_marcados;

            } else {

                if (is_null($parametros)) {
                    $this->view->parametros = $this->tratarParametros();
                } else {
                    $this->view->parametros = $parametros;
                }

                //Incializa os parametros
                $this->inicializarParametros();
            }

            $this->view->tela = 'cadastro';
            $this->view->parametros->sub_tela = 'aba_dados_principais';
            $this->view->parametros->acao = 'cadastrar';

            //Verificar se foi submetido o formulario e grava o registro em banco de dados
            if (isset($_POST) && !empty($_POST)) {

               $this->salvarModificacao($this->view->parametros);

            } else {
                $this->index($this->view->parametros);
            }

        } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

            $this->view->parametros->tela = 'pesquisa';
            $this->view->parametros->acao = 'index';
            $this->index($this->view->parametros);

        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
            $this->index($this->view->parametros);
        }

    }

     /**
     * Grava os dados na base de dados.
     * @author Andre L. Zilz
     * @param stdClass $dados Dados a serem gravados
     */
    private function salvarModificacao(stdClass $dados) {

        $vigenciaDozeMeses = '12';
        $requerAutorizacaoTecnica = false;
        $dados->totalContratos = 0;
        $errosStatusLinha = array(); // Responsável por guardar os erros vindos da validação do status da linha

        //Varivavel auxiliar para identificar se e um processo de contratos em lote
        $isMigracaoLote = (!empty($dados->contratosMarcados));

        //Caso nao seja migracao em lote valida novamente os campos
        if(!$isMigracaoLote) {
            //Validar os campos obrigatorios
            $this->validarCamposCadastro($dados);
        }

        //Parametros de particularidades para o tipo da modificacao
        $dados->tipoModificacao = $this->dao->recuperarTipoModificacao($dados->cmtoid);

        //Recupera dados especificos do contrato original
        $campos = array("connumero",
                        "conequoid",
                        "conveioid",
                        "coneqcoid",
                        "conno_tipo",
                        "conclioid",
                        "concsioid",
                        "condt_primeira_instalacao",
                        "conrczoid",
                        "(SELECT eqcobroid FROM equipamento_classe WHERE eqcoid = coneqcoid) AS eqcobroid",
                        "(CASE WHEN condt_ini_vigencia IS NOT NULL THEN
                            (TO_CHAR(NOW(), 'mm/yyyy') >= TO_CHAR((condt_ini_vigencia + INTERVAL '12 month'), 'mm/yyyy'))
                        ELSE
                            FALSE
                        END) AS is_mes_vigencia"
                        );


        if($isMigracaoLote) {

            //modificacao em lote com n contratos
            if(!empty($dados->contratosMarcados)) {

                foreach ($dados->contratosMarcados as $contrato) {
                    $dados->contrato[] = $this->dao->recuperarDadosContrato($contrato, $campos);
                }

            } else {
                $dados->contrato = array();
            }

        } else {
            //modificacao com um unico contrato
            $dados->contrato[0]  = $this->dao->recuperarDadosContrato($dados->cmfconnumero, $campos);
        }

         //Validacoes adicionais para contrato(s)
        foreach ($dados->contrato as $contrato) {

            $dados->totalContratos++;

            if($dados->cmtoid == self::TIPO_DUPLICACAO_CONTRATO_PLACA2) {

                $isCargaSASTM_origem = $this->dao->isClasseCargaSASTM($contrato->coneqcoid);
                $isCargaSASTM_destino = $this->dao->isClasseCargaSASTM($dados->cmfeqcoid_destino);

                // Quando for DUPLICACAO DE CONTRATO PLACA 2, se a Classe do contrato for do tipo SASTM ou CARGA
                // nao podera ter equipamentos CARGA ou SASTM em um mesmo contrato.
                if(($isCargaSASTM_origem) && ($isCargaSASTM_destino)) {
                     throw new Exception(self::MENSAGEM_ALERTA_CARGA_SASTM);
                }

                if ( ($contrato->conclioid != $dados->cmfeqcoid_destino) && empty($dados->anexos) ) {
                     throw new Exception(self::MENSAGEM_ALERTA_ANEXOS);
                }
            }

            if($dados->tipoModificacao[0]->produto_siggo_seguro == 't') {

                if($dados->tipoModificacao[0]->cmtcmgoid == 3 || $dados->tipoModificacao[0]->cmtcmgoid == 5) {
                    $this->validarDadosObrigatoriosSeguro($dados,$contrato->connumero);
                } else {
                    $this->validarDadosObrigatoriosSeguro($dados,$contrato->connumero,$dados->cmfveioid_novo,$dados->cmfclioid_destino);
                }

                $clienteParticularidade = $this->dao->recuperarClienteParticularidade(array('clipclioid'), $dados->cmfclioid_destino);

                if(empty($clienteParticularidade)){
                    throw new Exception(self::MENSAGEM_ALERTA_CLIENTE_PARTICULARIDADE);
                }

            }


            if($this->dao->verificarContratoDisponivel($contrato->connumero)){
                throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_INDISPONIVEL);
            }

            //Restricao a situacao do contrato
            $restricao = $this->dao->verificarRestricaoContrato($contrato->concsioid, $dados->cmtoid);
            if(!empty($restricao)) {
                throw new Exception("Situação: ". strtoupper($restricao) .", não é permitida para este tipo de modificação.");
            }

            //Cliente diferentes perimitido apenas para grupo de modificacao: MIGRACAO DE EQUIPAMENTO (2)
             if( (!in_array($dados->cmtoid, $this->idsMigracao)) && ($dados->cmtoid != self::TIPO_DUPLICACAO_CONTRATO_PLACA2) ) {
                if($dados->tipoModificacao[0]->cmtcmgoid != 2){

                    if($dados->cmfclioid_destino != $contrato->conclioid){
                        throw new Exception(self::MENSAGEM_ALERTA_CLIENTES_DIFERENTES);
                    }
                } 
			}

            //Verificar se e o ultimo mes de vigencia do contrato
            if (($dados->cmtoid == self::TIPO_RENOVACAO_SIGGO_SEGURO) && ($contrato->is_mes_vigencia == 'f')) {
                throw new Exception(self::MENSAGEM_ALERTA_VIGENCIA_EXPIRADA);
            }

            //Sem tipo de contrato
            if(is_null($contrato->conno_tipo)) {
                 throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_SEM_TIPO);
            }

            //Sem classe de contrato
            if(is_null($contrato->coneqcoid)) {
                 throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_SEM_CLASSE);
            }

            //Sem veiculo
            if(is_null($contrato->conveioid)) {
                 throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_SEM_VEICULO);
            }

            //Sem equipamento
            if(is_null($contrato->conequoid)) {
                 throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_SEM_EQUIP);
            }

            //Contrato informado e SIGGO?
            if($dados->produto_siggo == 't') {
                if(!$this->dao->isContratoSiggo($contrato->connumero)) {
                   throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_NAO_SIGGO);
                }
            }

            /**
            * Verifica o status da linha vinculado ao equipamento do contrato
            * Passando o código do tipo da modificação e o número do contrato
            **/
            $validacaoLinha = $this->validaStatusLinha((Object)array ('ajax' => false, 'cmtoid' => $dados->cmtoid, 'connumero' => $contrato->connumero));
            if ($validacaoLinha !== true){
                $errosStatusLinha[ $contrato->connumero ] = $validacaoLinha;
            }

        }
        /*
        * Verificando se houve algum problema na validação do status da linha
        */
        if ( count($errosStatusLinha) > 0 ){
            $msgErroStatusLinha = "";
            foreach ($errosStatusLinha as $k => $v) {
                $msgErroStatusLinha .= "CONTRATO: ".$k ." = ". $v."<br />";
            }
            throw new Exception($msgErroStatusLinha);
        }

        /**
        * SE tratar-se de uma migracao do tipo de modificacao: 
        * MIGRACAO PARA EX-SEGURADO (14) ou 
        * MIGRACAO COM REATIVACAO EX PARA SEGURADO (16)
        **/
        if(in_array($dados->cmtoid, $this->idsMigracao)) {
            //Dados adicionais
            $vigencia = $this->dao->recuperarContratoVigencia($vigenciaDozeMeses);
            $dados->cmfcvgoid = $vigencia[0]->cvgoid;
            $dados->cmfrczoid = $dados->contrato[0]->conrczoid;
            $dados->cmfeqcoid_destino =  $dados->contrato[0]->coneqcoid;
        }

        //Tratamento especifico de dados antes de persistir no banco
        $dados = $this->tratarDadosPersistencia($dados);

        //Efetua o INSERT do registro
        if (empty($dados->mdfoid)) {

             //Inicia a transacao
             $this->dao->begin();

            //incluir na tabela [modificacao]
            $dados->mdfoid = $this->dao->inserirModificacao($dados);

            if(empty($dados->mdfoid)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //incluir na tabela [contrato_modificacao]
            $cmfoidArray = $this->dao->inserirContratoModificacao($dados);

            if(empty($cmfoidArray)) {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            //SE NAO for migracao do tipo de modificacao MIGRACAO PARA EX-SEGURADO (14) ou MIGRACAO COM REATIVACAO EX PARA SEGURADO (16)
            if(!in_array($dados->cmtoid, $this->idsMigracao)) {
                //incluir na tabela [contrato_modificacao_pagamento]
                if( !$this->dao->inserirModificacaoPagamento($dados) ) {
                    throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
                }
            }

            //incluir na tabela [contrato_modificacao_contato]
            if(!empty($dados->contatos)) {
                if( !$this->dao->inserirContatoModificacao($dados) ) {
                    throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
                }
            }

            if(!empty($dados->acessorios)) {

                $this->dao->inserirAcessorioModificacao($dados->acessorios,  $cmfoidArray);

                $requerAutorizacaoTecnica = TRUE;

                $this->dao->atualizarStatusModificacao('A', $dados->mdfoid);
            }

            if(!empty($dados->anexos) && ($dados->anexar_arquivo == 't')) {
                $this->dao->inserirDadosAnexoModificacao($dados);
            }

            $this->dao->inserirHistoricoModificacao($dados->mdfoid, "Modificação Cadastrada.");

            if( ($dados->analise_credito == 't') && !in_array($dados->cmtoid, $this->idsMigracao)) {
                $this->submeterAnaliseCredito($dados);
            }

            //Finaliza a transacao
            $this->dao->commit();

            //Define se vai gerar o(s) contrato(s) automaticamente
            if( ($dados->tipoModificacao[0]->cmptgera_contrato_automatico == 't') && (!$requerAutorizacaoTecnica) && ($dados->analise_credito_status == 'A')){
            
                $this->gerarContratos($dados);
            
            }else{

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_INCLUIR;
                $this->destruirSessao();
                $dados->sub_tela = 'aba_dados_principais';
                $this->detalhar($dados);

        	}
        }

    }


    /**
    * Gera contrato(s) conforme grupo de modificacao
    *
    * @author Andre L. Zilz
    * @param stdClass $dados
    */
    public function gerarContratos($dados = null) {

        $isPagarTaxaCartao = false;
        $geradoOrdemServico = 0;
        $qtdeContratos = 0;
        $observacaoAurotizacaoTecnica = '';
        $contratosGerados = '';

        if(is_null($dados)){
            $this->view->parametros = $this->tratarParametros();
            $this->inicializarParametros();
            $dados = $this->view->parametros;
        }

        //Contornar o F5
        if(isset($_POST) && !empty($_POST)) {
        	
            if( !empty($dados->cartao_codigo) && $dados->cmdfppagar_cartao == 't' ) {
                $isPagarTaxaCartao = true;
            }

            //Parametros de particularidades para o tipo da modificacao
            $tipoModificacao = $this->dao->recuperarTipoModificacao($dados->cmtoid);

            $grupo = $tipoModificacao[0]->cmtcmgoid;

            //Recuperar dados da contrato_modificacao
            $dados->contrato_modificacao = $this->dao->recuperarContratoModificacao($dados->mdfoid, false, array("cmfoid"));

            try{
                //Inicia a transacao
                $this->dao->begin();

                if($_SERVER['REQUEST_METHOD']=='POST'){
                	$request = md5( implode( $_POST ) );
                	if(isset( $_SESSION['last_post_upgrade'] ) && $_SESSION['last_post_upgrade']== $request){
                		$this->index();
                		exit;
                	}else{
                		$_SESSION['last_post_upgrade']  = $request;
                	}
                }
                
                //Percorre o(s) Contrato(s)
                foreach ($dados->contrato_modificacao as $contrato) {
                	
                    $connumero = 0;

                    $acessorios = $this->dao->recuperarAcessorioPorContratoModificacao($contrato->cmfoid);
                    $possuiAcessorioAdicional = (!empty($acessorios));

                     /*
                        LEGENDA GRUPOS:
                        1 = "MIGRACAO SEGURADORA/ASSOCIACAO"
                        2 = "MIGRACAO DE EQUIPAMENTO"
                        3 = "UPGRADE"
                        4 = "DOWNGRADE"
                        5 = "RENOVACAO SIGGO"
                        6 = "INSTALACAO DERIVADA DE RNC"
                        7 = "TROCA DE VEÍCULO"
                        8 = "EFETIVACAO DEMO"
                        9 = "DUPLICACAO PLACA 2"
                        11 = "UPGRADE COM TROCA DE VEÍCULO"
                        12 = "DOWNGRADE COM TROCA DE VEICULO"
                        13 = "MIGRACAO PARA CLIENTE"
                    */
                    switch ($grupo) {
                        case 1:
                        case 13:
                            //MIGRACAO SEGURADORA/ASSOCIACAO
                            //MIGRACAO PARA CLIENTE
                            $connumero = $this->modificarContratoMigracaoSeg($contrato->cmfoid, $possuiAcessorioAdicional);
                            break;
                        case 2:
                            //MIGRACAO DE EQUIPAMENTO
                            $connumero = $this->modificarContratoMigracaoEqpto($contrato->cmfoid, $possuiAcessorioAdicional);
                            break;
                        case 3:
                        case 11:
                            //UPGRADE
                            //UPGRADE COM TROCA DE VEÍCULO
                            $connumero = $this->modificarContratoUpgrade($contrato->cmfoid, $isPagarTaxaCartao, $possuiAcessorioAdicional);
                            break;

                        case 4:
                        case 12:
                            //DOWNGRADE
                            //DOWNGRADE COM TROCA DE VEICULO
                            $connumero = $this->modificarContratoDowngrade($contrato->cmfoid, $isPagarTaxaCartao, $possuiAcessorioAdicional);
                            break;
                        case 5:
                            //RENOVACAO SIGGO
                            $connumero = $this->modificarContratoRenovacao($contrato->cmfoid);
                            break;
                        case 6:
                            //INSTALACAO DERIVADA DE RNC
                            $connumero = $this->modificarContratoInstalacaoRnr($contrato->cmfoid);
                            break;
                        case 7:
                            //TROCA DE VEÍCULO
                            $connumero = $this->modificarContratoTrocaVeiculo($contrato->cmfoid, $isPagarTaxaCartao);
                            break;
                        case 8:
                            //EFETIVACAO DEMO
                            $connumero = $this->modificarContratoEfetivacaoDemo($contrato->cmfoid, $possuiAcessorioAdicional);
                            break;
                        case 9:
                            //DUPLICACAO PLACA 2
                            $connumero = $this->modificarContratoPlaca2($contrato->cmfoid);
                            break;
                    }

                        $qtdeContratos++;

                    //Verifica se foi gerado OS
                    $contratoModificacao = $this->dao->recuperarContratoModificacaoPorID($contrato->cmfoid, array('cmfordoid'));

                    if(isset($contratoModificacao->cmfordoid) && empty($contratoModificacao->cmfordoid)) {
                        $geradoOrdemServico++;
                    }

                        if($possuiAcessorioAdicional){
                        $this->dao->inserirAcessoriosContratoServico($acessorios, $connumero, $contrato->cmfoid);
                    }

                    $contratosGerados .= $connumero . ', ';

                }

                $obsHistoricoModificacao = "Contrato(s) Gerado(s) / Modificados(s): " .  substr($contratosGerados, 0, -2);
                $this->dao->inserirHistoricoModificacao($dados->mdfoid, $obsHistoricoModificacao);

                //Se todos contratos tiveram OS geradas, o status da modificacao fica como Em Andamento
                if($geradoOrdemServico == $qtdeContratos) {
                   $this->dao->atualizarStatusModificacao('E', $dados->mdfoid);
                } else {
                   $this->dao->atualizarStatusModificacao('C', $dados->mdfoid);
                }

                //Pagamento da taxa (obrigacao financeira) com cartao de credito
                if($isPagarTaxaCartao){
                    $this->pagarTaxaCartaoCredito($dados);
                }

                //Finaliza a transacao
                $this->dao->commit();
                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_GERAR_CONTRATOS;

                $this->destruirSessao();
                $dados->sub_tela = 'aba_dados_principais';
                $this->detalhar($dados);

            } catch (Exception $e) {
                $this->dao->rollback();
                $this->view->mensagemErro = $e->getMessage();

                $dados->acao = 'index';
                unset($_POST);
                $this->index($dados);
        }

        } else {
        $dados->acao = 'index';
        unset($_POST);
        $this->index($dados);
        }


    }

    /**
     * Confirma a autorizacao tecnica
     */
    public function efetivarAutorizacaoTecnica() {


        $this->view->parametros = $this->tratarParametros();
        $this->view->parametros->sub_tela = 'aba_dados_principais';

        $dados = $this->view->parametros;

        try {

            $this->dao->begin();

            $this->dao->atualizarStatusModificacao('P', $dados->mdfoid);

            $this->dao->inserirHistoricoModificacao($dados->mdfoid, "Autorização Técnica.");

            $this->dao->commit();

            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;

       } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->detalhar($dados);

    }


     /**
    * Migra os anexos do contrato antigo para o novo contrato
    *
    * @author Andre L. Zilz
    * @param int $connumero | numero do contrato origem
    * @param int $connumeroNovo | numero do contrato destino
    * @param int $mdfoid | ID da modificacao
    */
    private function migrarAnexos($connumero, $connumeroNovo, $mdfoid) {

        $connumero = intval($connumero);
        $connumeroNovo = intval($connumeroNovo);
        $observacao = 'Arquivo(s) anexado(s) pelo processo de modificação número ' . $mdfoid . ': ';
        $anexados = 0;

        $diretorioTemp = "/var/www/docs_info_termo/termo". ($connumero % 10) ."/anexos_modificacao_" . $connumero;
        $diretorioOrigem = "/var/www/docs_info_termo/termo".($connumero % 10)."/" . $connumero;
        $diretorioDestino = "/var/www/docs_info_termo/termo".($connumeroNovo % 10)."/" . $connumeroNovo;

        $anexosNovos = $this->dao->recuperarNovosAnexos($mdfoid);
        $anexosAntigos = $this->dao->recuperarAnexosContratoOrigem($connumero);


        if(empty($connumeroNovo)) {

            //Migrar novos anexos
            if(!empty($anexosNovos)) {

                $this->dao->excluirDadosAnexo($mdfoid);
                $this->dao->inserirDadosAnexoContrato($anexosNovos, $connumero);

                if (!is_dir($diretorioTemp)) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                    //throw new Exception(__LINE__);
                }

                if (!is_writable($diretorioTemp)) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                    //throw new Exception(__LINE__);
                }

                if (!is_dir($diretorioOrigem)) {

                    if(!mkdir($diretorioOrigem, 0777, TRUE)){
                      throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                      //throw new Exception(__LINE__);
                    }
                }

                foreach ($anexosNovos as $anexo) {

                    $pathDestino = $diretorioOrigem ."/". $anexo->nome_arquivo;
                    $pathOrigem = $diretorioTemp ."/". $anexo->nome_arquivo;

                    if (file_exists($pathOrigem) === TRUE) {
                        if(!rename($pathOrigem, $pathDestino)) {
                            throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                            //throw new Exception(__LINE__);
                        }

                        $observacao .= $anexo->nome_arquivo . ", ";
                        $anexados++;
                    }
                }
            }

            if($anexados > 0) {
                $observacao = substr($observacao, 0, -2);
                $this->dao->inserirHistoricoContrato($connumero, $observacao);
            }

        } else {

            //Migrar novos anexos
            if(!empty($anexosNovos)) {

                $this->dao->excluirDadosAnexo($mdfoid);
                $this->dao->inserirDadosAnexoContrato($anexosNovos, $connumeroNovo);

                if (!is_dir($diretorioTemp)) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                    //throw new Exception(__LINE__);
                }

                if (!is_writable($diretorioTemp)) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                    //throw new Exception(__LINE__);
                }

                if (!is_dir($diretorioDestino)) {

                    if(!mkdir($diretorioDestino, 0777, TRUE)) {
                        throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                        //throw new Exception(__LINE__);
                    }
                }

                if (!is_writable($diretorioDestino)) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                    //throw new Exception(__LINE__);
                }

                foreach ($anexosNovos as $anexo) {

                    $pathDestino = $diretorioDestino ."/". $anexo->nome_arquivo;
                    $pathOrigem = $diretorioTemp ."/". $anexo->nome_arquivo;

                    if (file_exists($pathOrigem) === TRUE) {
                        if(!rename($pathOrigem, $pathDestino)) {
                            throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                            //throw new Exception(__LINE__);
                        }

                        $observacao .= $anexo->nome_arquivo . ", ";
                        $anexados++;
                    }
                }
            }

            //Migrar anexos antigos
            if(!empty($anexosAntigos)) {

                $this->dao->excluirDadosAnexo($mdfoid);
                $this->dao->inserirDadosAnexoContrato($anexosAntigos, $connumeroNovo);

                if(!is_dir($diretorioOrigem)) {

                     if(!mkdir($diretorioOrigem, 0777, TRUE)) {
                        throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                        //throw new Exception(__LINE__);
                    }
                }

                if(!is_writable($diretorioOrigem)) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                    //throw new Exception(__LINE__);
                }

                if (!is_dir($diretorioDestino)) {

                    if(!mkdir($diretorioDestino, 0777, TRUE)){
                        throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                        //throw new Exception(__LINE__);
                    }
                }

                if (!is_writable($diretorioDestino)) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                    //throw new Exception(__LINE__);
                }

                foreach ($anexosAntigos as $anexo) {

                    $pathDestino = $diretorioDestino ."/". $anexo->nome_arquivo;
                    $pathOrigem = $diretorioOrigem ."/". $anexo->nome_arquivo;

                    if (file_exists($pathOrigem) === TRUE) {
                        if(!copy($pathOrigem, $pathDestino)) {
                            throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                            //throw new Exception(__LINE__);
                        }

                        $observacao .= $anexo->nome_arquivo . ", ";
                        $anexados++;
                    }
                }
            }

        if($anexados > 0) {
            $observacao = substr($observacao, 0, -2);
            $this->dao->inserirHistoricoContrato($connumeroNovo, $observacao);
        }
    }
    }

    /**
    * inativa os anexos do contrato e excluiu os arquivos
    *
    * @author Andre L. Zilz
    * @param int $connumero | numero do contrato
    * @param int $mdfoid | ID da modificacao
    */
    private function excluirAnexos($connumero, $mdfoid) {

        $observacao = 'Arquivo(s) excluído(s) pelo processo de modificação número ' . $mdfoid . ': ';
        $diretorio = "/var/www/docs_info_termo/termo".($connumero % 10)."/" . $connumero;

        $anexos = $this->dao->recuperarAnexosContratoOrigem($connumero);

        if(!empty($anexos)) {

            foreach ($anexos as $anexo) {
                $observacao .=  $anexo->nome_arquivo . ", ";
            }

            //Remover a ultima virgula
            $observacao = substr($observacao, 0, -2);

            $this->dao->inativarDadosAnexoContrato($connumero);

            $this->dao->inserirHistoricoContrato($connumero, $observacao);

            if (!is_dir($diretorio)) {
                //throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                throw new Exception(__LINE__);
            }

            if(!is_writable($diretorio)) {
                //throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                throw new Exception(__LINE__);
            }

            foreach ($anexos as $anexo) {

                $path = $diretorio ."/". $anexo->nome_arquivo;

                if (file_exists($path) === TRUE) {
                   unlink($path);
                }

            }

        }

    }

     /**
     * Responsavel por exibir as abas de detalhes da modificacao
     *
     * @author Andre L. Zilz
     * @return void
     */
    public function detalhar($parametros = null) {

          try {

            if(is_null($parametros)) {
                 $parametros = $this->tratarParametros();
            }

            $parametros->mdfoid = (!empty($parametros->mdfoid)) ? $parametros->mdfoid : 0;
            $exibeMensagem = $parametros->exibie_msg;

            //Verifica se foi informado o id do cadastro
            if (isset($parametros->mdfoid) && intval($parametros->mdfoid) > 0) {

                //verifica o status da Modificacao
                $modificacao = $this->dao->recuperarDadosModificacao($parametros->mdfoid, array('mdfstatus, mdfcmtoid'));
                $parametros->mdfstatus = $modificacao->mdfstatus;

                if(!empty($parametros->mdfstatus)) {

                    if($parametros->sub_tela == 'aba_itens') {

                        //Recupera dados basicos dos contratos modificados
                       $parametros->contratos = $this->dao->recuperarContratoModificacao($parametros->mdfoid, false);

                       $camposOS = array(
                                    "COALESCE((SELECT (osadata = NOW()::date) from ordem_servico_agenda where osaordoid = ordoid and osaexclusao IS NULL LIMIT 1),FALSE) AS bloqueio_dt_agenda",
                                    "(SELECT osadata from ordem_servico_agenda where osaordoid = ordoid and osaexclusao IS NULL LIMIT 1) AS data_agendamento",
                                    "(ordoid::VARCHAR || ' / ' || ostdescricao) AS ordem_servico_tipo",
                                    "ossdescricao",
                                    "ordstatus",
                                    );

                       //Pegar a ultima OS e primeiro item da OS
                       $where = " ORDER BY
                                        orddt_ordem DESC, ositdt_cadastro ASC
                                    LIMIT 1";


                       //acrescenta dados adicionais de Ordem de Servico
                       foreach ($parametros->contratos as $contrato) {

                            $contrato->status_modificacao =  $parametros->mdfstatus;

                           if(!empty($contrato->cmfordoid)){
                                $ordemServico = $this->dao->recuperarDadosOrdemServico($contrato->cmfordoid, $camposOS, $where);

                                $contrato->ordem_servico_tipo =  $ordemServico[0]->ordem_servico_tipo;
                                $contrato->status_os =  $ordemServico[0]->ossdescricao;

                               /*
                               * define se sera exibido o checkbox [desfazer]
                               * Somente se:
                               * Status da modificacao diferente de Em Andamento
                               * Ordem de servico nao estiver concluida
                               * O contrato nao ter sido desfeito anteriormente
                               * A data de agendamento da OS nao for no dia atual
                               */
                                if( ($parametros->mdfstatus == 'E')
                                    && ($ordemServico[0]->ordstatus != 3)
                                    && ($contrato->modificacao_desfeita == 'f')
                                    && ($ordemServico[0]->bloqueio_dt_agenda == 'f') ) {

                                    $contrato->is_desfazer = TRUE;
                                } else{
                                    $contrato->is_desfazer = FALSE;
                                }
                            } else {
                                 $contrato->is_desfazer = FALSE;
                            }
                       }

                    } else if($parametros->sub_tela == 'aba_dados_principais') {
                        //recupera os dados da modifcacao
                         $camposModificacao = array(
                                "mdfoid",
                                "mdfstatus",
                                "mdfstatus_financeiro",
                                "TO_CHAR(mdfdt_cadastro, 'dd/mm/yyyy') AS data_modificacao",
                                "mdfobservacao_modificacao AS motivo_modificacao",
                                "cmtdescricao AS tipo_modificacao",
                                "(SELECT msubdescricao FROM motivo_substituicao WHERE msuboid = mdfmsuboid) AS substituicao_descricao",
                                "(SELECT nm_usuario FROM usuarios WHERE cd_usuario = mdfusuoid_cadastro) AS usuario_modificacao",
                                "(SELECT clinome FROM clientes WHERE clioid = mdfclioid) AS cliente_modificacao",
                                "mdfclioid"
                                );

                        $parametros = $this->dao->recuperarDadosModificacao($parametros->mdfoid, $camposModificacao);

                        $camposContrato = array(
                                        "(SELECT cvgvigencia FROM contrato_vigencia WHERE cvgoid = cmfcvgoid) AS vigencia",
                                        "(SELECT funnome FROM funcionario WHERE funoid = cmffunoid_executivo) AS executivo",
                                        "(SELECT rczcd_zona FROM regiao_comercial_zona WHERE rczoid = cmfrczoid) AS dmv",
                                        "(SELECT tpcdescricao FROM tipo_contrato WHERE tpcoid = cmftpcoid_destino) AS tipo_contrato_novo",
                                        "cmfclioid_destino"
                                        );

                        //Recupera dados adicionais da modificacao contrato
                        $contratoModificacao = $this->dao->recuperarContratoModificacao($parametros->mdfoid, true, $camposContrato);
                        $parametros->vigencia           = $contratoModificacao[0]->vigencia;
                        $parametros->executivo          = $contratoModificacao[0]->executivo;
                        $parametros->dmv                = $contratoModificacao[0]->dmv;
                        $parametros->tipo_contrato_novo = $contratoModificacao[0]->tipo_contrato_novo;

                        //Recupera dados de faturamento
                        $faturamento = $this->dao->recuperarDadosPagamentoModificacao($parametros->mdfoid);
                        $parametros->forma_pgto             = $faturamento->forma_pgto;
                        $parametros->monitoramento          = $faturamento->monitoramento;
                        $parametros->locacao                = $faturamento->locacao;
                        $parametros->taxa_descricao         = $faturamento->taxa_descricao;
                        $parametros->taxa                   = $faturamento->taxa;
                        $parametros->taxa_isencao           = $faturamento->taxa_isencao;
                        $parametros->cmdfppagar_cartao = $faturamento->cmdfppagar_cartao;

                         //Parametros de particularidades para o tipo da modificacao
                        $tipoModificacao = $this->dao->recuperarTipoModificacao($modificacao->mdfcmtoid);
                        $parametros->mdfcmtoid = $modificacao->mdfcmtoid;
                        $parametros->cmptgera_contrato_automatico = $tipoModificacao[0]->cmptgera_contrato_automatico;
                        $parametros->produto_siggo  = $tipoModificacao[0]->produto_siggo;

                        $this->view->tela = 'detalhes';
                        $parametros->sub_tela = 'aba_dados_principais';
                        $parametros->acao = 'detalhar';

                        if($exibeMensagem == 't') {
                            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;
                    }
                    } else if($parametros->sub_tela == 'aba_acessorios') {

                        $dadosAbaAcessorios = $this->dao->recuperarDadosModificacao($parametros->mdfoid, array("mdfoid", "mdfstatus"));
                        $parametros->mdfoid = $dadosAbaAcessorios->mdfoid;
                        $parametros->mdfstatus = $dadosAbaAcessorios->mdfstatus;

                    } else if ($parametros->sub_tela == 'aba_historico') {

                        $parametros->historico = $this->dao->recuperarHistoricoModificacao($parametros->mdfoid);
                    }

                }

                $this->index($parametros);

            } else {
                $this->index();
            }

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();
            $this->index();
        }

    }

    private function validarDadosObrigatoriosSeguro($dados,$connumero,$veioid = null,$clioid = null) {

        if(is_null($veioid)) {
            $resultDados = $this->dao->recuperaDadosVeiculoSiggo($connumero);
        } else {
            $resultDados = $this->dao->recuperaDadosVeiculoSiggo2($veioid,$clioid);
        }


        $camposVeiculo = '';
        $camposCliente = '';

        if(is_null($resultDados['codigo_fipe_veiculo']) || strlen($resultDados['codigo_fipe_veiculo']) == 0) {
            $camposVeiculo .= 'Código fipe,';
        }

        if(is_null($resultDados['id_zero_km'])) {
            $camposVeiculo .= ' Carro zero km,';
        }


        if(is_null($resultDados['combustivel'])) {
            $camposVeiculo .= ' Combustível,';
        }

        if(is_null($resultDados['utilizacao_do_veiculo'])) {
            $camposVeiculo .= ' Utilização do veículo,';
        }

        if(is_null($resultDados['ano_modelo']) || strlen($resultDados['ano_modelo']) == 0) {
            $camposVeiculo .= ' Ano do veículo,';
        }

        if(strlen($resultDados['placa']) == 0 || is_null($resultDados['placa'])) {
            $camposVeiculo .= ' Placa,';
        }

        if(strlen($resultDados['cpf_cnpj']) == 0 || is_null($resultDados['cpf_cnpj'])) {
            $camposCliente .= 'CPF/CNPJ,';
        }

        if(strlen($resultDados['clidt_nascimento']) == 0 || is_null($resultDados['clidt_nascimento'])) {
            $camposCliente .= ' Data de nascimento do cliente.';
        }

        if(strlen($resultDados['cliemail']) == 0 || is_null($resultDados['cliemail'])) {
            $camposCliente .= ' Email,';
        }

        if(strlen($resultDados['cliestado_civil']) == 0 || is_null($resultDados['cliestado_civil'])) {
            $camposCliente .= ' Estado civil,';
        }

        if(strlen($resultDados['cep']) == 0 || is_null($resultDados['cep'])) {
            $camposCliente .= ' CEP,';
        }

        if(strlen($resultDados['clirua_res']) == 0 || is_null($resultDados['clirua_res'])) {
            $camposCliente .= ' Rua,';
        }

        if(strlen($resultDados['clino_res']) == 0 || is_null($resultDados['clino_res'])) {
            $camposCliente .= ' Número da residência,';
        }

        if(strlen($resultDados['clicompl_res']) == 0 || is_null($resultDados['clicompl_res'])) {
            $camposCliente .= ' Complemento,';
        }

        if(strlen($resultDados['clicidade_res']) == 0 || is_null($resultDados['clicidade_res'])) {
            $camposCliente .= ' Cidade,';
        }

        if(strlen($resultDados['cliuf_res']) == 0 || is_null($resultDados['cliuf_res'])) {
            $camposCliente .= ' Estado,';
        }

        if(strlen($resultDados['cliformacobranca']) == 0 || is_null($resultDados['cliformacobranca'])) {
            $camposCliente .= ' Forma de cobrança.';
        }


        if(strlen($camposVeiculo) > 0 || strlen($camposCliente) > 0 ) {

            $msg = '';

            if(strlen($camposVeiculo) > 0) {
                $camposVeiculo = trim($camposVeiculo,',');
                $msg .= 'Atualize os seguintes dados do veículo:' . $camposVeiculo.'.';
            }

            if(strlen($camposCliente) > 0) {
                $camposCliente = trim($camposCliente,',');
                $msg .= ' Atualize os seguintes dados do cliente:' . $camposCliente.'.';
            }

            throw new Exception($msg);
        }
    }

    /**
    * Tratatamento pre-interacao com banco de dados
    *
    * @author Andre L. Zilz
    * @param stdClass $parametros
    */
    private function tratarDadosPersistencia($parametros) {

        //Dados de contratos
        foreach ($parametros->contrato as $contrato) {

            $contrato->condt_primeira_instalacao = empty($contrato->condt_primeira_instalacao)  ? 'NULL' : "'".$contrato->condt_primeira_instalacao."'";
            $contrato->conequoid                 = empty($contrato->conequoid)                  ? 'NULL' : intval($contrato->conequoid);
            $contrato->eqcobroid                 = empty($contrato->eqcobroid)                  ? 'NULL' : intval($contrato->eqcobroid);
            $contrato->conveioid                 = empty($contrato->conveioid)                  ? 'NULL' : intval($contrato->conveioid);
        }

        //Executa as insercoes
        foreach ($parametros->acessorios as $acessorio) {

            //Tratamento especifico de dados
            $acessorio->cmsvalor_negociado  = $this->tratarMoeda($acessorio->cmsvalor_negociado, 'B');
            $acessorio->cmsvalor_tabela     = $this->tratarMoeda($acessorio->cmsvalor_tabela, 'B');

        }

        $parametros->cmdfpvlr_monitoramento_negociado   = empty($parametros->cmdfpvlr_monitoramento_negociado)  ? 'NULL' : floatval($this->tratarMoeda($parametros->cmdfpvlr_monitoramento_negociado, 'B'));
        $parametros->cmdfpvlr_monitoramento_tabela      = empty($parametros->cmdfpvlr_monitoramento_tabela)     ? 'NULL' : floatval($this->tratarMoeda($parametros->cmdfpvlr_monitoramento_tabela, 'B'));
        $parametros->cmdfpvlr_locacao_negociado         = empty($parametros->cmdfpvlr_locacao_negociado)        ? '0.00' : floatval($this->tratarMoeda($parametros->cmdfpvlr_locacao_negociado, 'B'));
        $parametros->cmdfpvlr_locacao_tabela            = empty($parametros->cmdfpvlr_locacao_tabela)           ? 'NULL' : floatval($this->tratarMoeda($parametros->cmdfpvlr_locacao_tabela, 'B'));
        $parametros->msuboid                = empty($parametros->msuboid)               ? 'NULL' : intval($parametros->msuboid);
        $parametros->cmfcvgoid              = empty($parametros->cmfcvgoid)             ? 'NULL' : intval($parametros->cmfcvgoid);
        $parametros->cmffunoid_executivo    = empty($parametros->cmffunoid_executivo)   ? 'NULL' : intval($parametros->cmffunoid_executivo);
        $parametros->cmfrczoid              = empty($parametros->cmfrczoid)             ? 'NULL' : intval($parametros->cmfrczoid);
        $parametros->cmftppoid              = empty($parametros->cmftppoid)             ? 'NULL' : intval($parametros->cmftppoid);
        $parametros->cmftppoid_subtitpo     = empty($parametros->cmftppoid_subtitpo)    ? 'NULL' : intval($parametros->cmftppoid_subtitpo);
        $parametros->cmfveioid_novo         = empty($parametros->cmfveioid_novo)        ? 'NULL' : intval($parametros->cmfveioid_novo);
        $parametros->cmdfpforcoid           = empty($parametros->cmdfpforcoid)          ? 'NULL' : intval($parametros->cmdfpforcoid);
        $parametros->cmdfpnum_parcela       = empty($parametros->cmdfpnum_parcela)      ? 'NULL' : intval($parametros->cmdfpnum_parcela);
        $parametros->cmdfpcartao            = empty($parametros->cmdfpcartao)           ? 'NULL' : $parametros->cmdfpcartao;
        $parametros->cmdfpnome_portador     = empty($parametros->cmdfpnome_portador)    ? 'NULL' : $parametros->cmdfpnome_portador;
        $parametros->cmdfpdebito_banoid     = empty($parametros->cmdfpdebito_banoid)    ? 'NULL' : intval($parametros->cmdfpdebito_banoid);
        $parametros->cmdfpdebito_agencia    = empty($parametros->cmdfpdebito_agencia)   ? 'NULL' : intval($parametros->cmdfpdebito_agencia);
        $parametros->cmdfpdebito_cc         = empty($parametros->cmdfpdebito_cc)        ? 'NULL' : intval($parametros->cmdfpdebito_cc);
        $parametros->cmdfpcpvoid            = empty($parametros->cmdfpcpvoid)           ? 'NULL' : intval($parametros->cmdfpcpvoid);
        $parametros->cmdfpobroid_taxa       = empty($parametros->cmdfpobroid_taxa)      ? 'NULL' : intval($parametros->cmdfpobroid_taxa);
        $parametros->cmdfpvlr_taxa_negociado= empty($parametros->cmdfpvlr_taxa_negociado) ? '0.00' : floatval($this->tratarMoeda($parametros->cmdfpvlr_taxa_negociado, 'B'));
        $parametros->cmdfpvlr_taxa_tabela   = empty($parametros->cmdfpvlr_taxa_tabela)  ? 'NULL' : floatval($this->tratarMoeda($parametros->cmdfpvlr_taxa_tabela, 'B'));
        $parametros->cmdfpisencao_taxa      = empty($parametros->cmdfpisencao_taxa)     ? 'f' : $parametros->cmdfpisencao_taxa;
        $parametros->cmdfpisencao_locacao   = empty($parametros->cmdfpisencao_locacao)  ? 'f' : $parametros->cmdfpisencao_locacao;
        $parametros->cmdfpvencimento_fatura = empty($parametros->cmdfpvencimento_fatura) ? 'NULL' : intval($parametros->cmdfpvencimento_fatura);
        $parametros->cmfclioid_destino      = empty($parametros->cmfclioid_destino)     ? 'NULL' : intval($parametros->cmfclioid_destino);

        return $parametros;

    }


    /**
     * Validar os campos obrigatórios do cadastro.
     * @author Andre L. Zilz
     *
     * @param stdClass $dados Dados a serem validados
     * @throws Exception
     * @return void
     */
    private function validarCamposCadastro(stdClass $dados) {

        //Campos para destacar na view em caso de erro
        $camposDestaques = array();

        /**
         * Verifica os campos obrigatórios
         */
        switch ($this->view->tela) {
            case 'pesquisa':

               if ( ($dados->clinome_pesq == '') && ($dados->connumero == '')
                    && ($dados->chassi == '') && ($dados->placa == '') && ($dados->mdfoid_pesq == '')
                    && (($dados->data_inicial == '') || ($dados->data_final == '')) ) {

                    $camposDestaques[] = array('campo' => 'data_inicial');
                    $camposDestaques[] = array('campo' => 'data_final');

                } else if(($dados->data_inicial != '') && ($dados->data_final != '')){

                    if($this->validarPeriodo($dados->data_inicial, $dados->data_final)){

                        $camposDestaques[] = array('campo' => 'data_inicial');
                        $camposDestaques[] = array('campo' => 'data_final');
                    }

                    if ($error) {
                        $this->view->campos = $camposDestaques;
                        throw new Exception(self::MENSAGEM_ALERTA_PERIODO_DATA);
                    }
                }
                break;
            case 'contratos_vencer':

                if(($dados->data_inicial != '') && ($dados->data_final != '')){

                    if($this->validarPeriodo($dados->data_inicial, $dados->data_final)){

                        $camposDestaques[] = array('campo' => 'data_inicial');
                        $camposDestaques[] = array('campo' => 'data_final');
                    }
                }


                if(($dados->data_inicial == '') || ($dados->data_final == '')){

                        $camposDestaques[] = array('campo' => 'data_inicial');
                        $camposDestaques[] = array('campo' => 'data_final');

                }

                //Tipo Modificacao
                if(empty($dados->cmtoid)){
                         $camposDestaques[] = array('campo' => 'cmtoid');
                }

                break;
            case 'cadastro':

                //Tipo Modificacao
                if(empty($dados->cmtoid)){
                         $camposDestaques[] = array('campo' => 'cmtoid');
                }

                //Forma de pgto
                if(empty($dados->cmdfpforcoid) && (!in_array($dados->cmtoid, $this->idsMigracao)) ) {
                         $camposDestaques[] = array('campo' => 'cmdfpforcoid');
                }

                //Se tipos NAO forem 14: "MIGRACAO PARA EX-SEGURADO" / 16: "MIGRACAO COM REATIVACAO EX PARA SEGURADO"
                if(!in_array($dados->cmtoid, $this->idsMigracao)) {

                    $obrigatorios = array(
                        'tipo_pessoa',
                        'cmftpcoid_destino',
                        'cmfeqcoid_destino',
                        'cmfcvgoid',
                        );

                    foreach ($dados as $chave => $valor) {

                        if(in_array($chave, $obrigatorios)) {

                            if($valor == '') {
                                 $camposDestaques[] = array('campo' => $chave);
                            }
                        }
                    }


                    //CLiente
                    if($dados->cmfclioid_destino == ''){
                         $camposDestaques[] = array('campo' => 'cpf_cnpj');
                    }

                    //Plava Novo Veiculo
                    if(($dados->troca_veiculo == 't' || $dados->exibir_novo_veiculo == 't') && ($dados->cmfveioid_novo == '')) {
                         $camposDestaques[] = array('campo' => 'veiplaca_novo');
                    }

                    //combo [Motivo Substituicao]
                    if($dados->msuboid == '' && $dados->is_combo_motivo_visivel == 'S'){
                         $camposDestaques[] = array('campo' => 'msuboid');
                    }

                    //Se for SIGGO, aplica obrigatoriedade ao tipo e subtipo proposta
                    if($dados->produto_siggo == 't'){

                         if($dados->cmftppoid == ''){
                             $camposDestaques[] = array('campo' => 'cmftppoid');
                        }

                        if($dados->cmftppoid_subtitpo == ''){
                             $camposDestaques[] = array('campo' => 'cmftppoid_subtitpo');
                        }

                    }

                    //Numero de Contrato
                    if($dados->acoes_lote != 't' && $dados->cmfconnumero == ''){
                         $camposDestaques[] = array('campo' => 'cmfconnumero');
                    }

                } else {
                    if(empty($dados->migrar_para)){
                         $camposDestaques[] = array('campo' => 'migrar_para');
                    }
                }

                //Faturamento
                //Quando true, o bloco de faturamento devera estar visivel!
                if($dados->cmptrecebe_dados_financeiro == 't') {

                     //Dados Bancarios (debito)
                    if($dados->forma_pgto == 'debito'){
                        if($dados->cmdfpdebito_banoid == ''){
                             $camposDestaques[] = array('campo' => 'cmdfpdebito_banoid');
                        }

                        if($dados->cmdfpdebito_agencia == ''){
                             $camposDestaques[] = array('campo' => 'cmdfpdebito_agencia');
                        }

                        if($dados->cmdfpdebito_cc == ''){
                             $camposDestaques[] = array('campo' => 'cmdfpdebito_cc');
                        }
                    }

                     //Dados Bancarios (credito)
                    if( ($dados->forma_pgto == 'credito') && ($dados->exibir_bloco_credito == 't') ) {
                        if($dados->cmdfpcartao == ''){
                             $camposDestaques[] = array('campo' => 'cmdfpcartao');
                        }

                        if($dados->cmdfpcartao_vencimento == ''){
                             $camposDestaques[] = array('campo' => 'cmdfpcartao_vencimento');
                        }

                        if($dados->cmdfpnome_portador == ''){
                             $camposDestaques[] = array('campo' => 'cmdfpnome_portador');
                        }
                    }

                    //Parcelamento
                    if(empty($dados->cmdfpcpvoid)){
                         $camposDestaques[] = array('campo' => 'cmdfpcpvoid');
                    }

                    //Data Vencimento
                    if(empty($dados->cmdfpvencimento_fatura)){
                         $camposDestaques[] = array('campo' => 'cmdfpvencimento_fatura');
                    }

                    //Monitoramento
                    if(empty($dados->cmdfpvlr_monitoramento_negociado)){
                         $camposDestaques[] = array('campo' => 'cmdfpvlr_monitoramento_negociado');
                    }

                    //Taxa
                    if(empty($dados->cmdfpobroid_taxa) && ($dados->cmpttaxa == 't')) {
                         $camposDestaques[] = array('campo' => 'cmdfpobroid_taxa');
                    }

                    //Locacao
                    if (($dados->cmdfpisencao_locacao == 'f') && empty($dados->cmdfpvlr_locacao_negociado)) {
                        $camposDestaques[] = array('campo' => 'cmdfpvlr_locacao_negociado');
                    }

                    //Valor Taxa
                    $valorTaxa = $dados->cmdfpvlr_taxa_negociado;
                    $valorTaxa = str_replace('.', '', $valorTaxa);
                    $valorTaxa = str_replace(',', '', $valorTaxa);
                    $valorTaxa = intval($valorTaxa);

                    if ( ($dados->cmdfpisencao_taxa == 'f')
                        && empty($valorTaxa)
                        && ($dados->cmpttaxa == 't') ) {
                        $camposDestaques[] = array('campo' => 'cmdfpvlr_taxa_negociado');
                    }

                    if(($dados->cmdfppagar_cartao == 't')
                        && ($dados->cmdfpisencao_taxa == 'f')
                        && (empty($valorTaxa)) ) {
                            $camposDestaques[] = array('campo' => 'cmdfpvlr_taxa_negociado');
                    }

                }


                if($dados->cmfclioid_destino != '') {

                    if($dados->cmtoid == self::TIPO_DUPLICACAO_CONTRATO_PLACA2) {
                        //recuperar o cliente de origem
                         $dados->contrato[0]  = $this->dao->recuperarDadosContrato($dados->cmfconnumero, array('conclioid'));
                         $clioid_origem = $dados->contrato[0]->conclioid;
                    } else {
                        $clioid_origem = '';
                    }

                     /*
                    * O QUADRO CONTATOS SO E OBRIGATORIO PARA OS TIPOS
                    * (21- DUPLICACAO PLACA 2, QUANDO O CLIENTE DO CONTRATO E DIFERENTE DO CLIENTE DA ANALISE DE CREDITO)
                    * OU (18 - MIGRACAO DE EQUIPAMENTO).
                    */
                    if( ($dados->cmtoid == self::TIPO_MIGRACAO_EQUIPAMENTO)
                        || ($dados->cmtoid == self::TIPO_DUPLICACAO_CONTRATO_PLACA2 && $dados->cmfclioid_destino != $clioid_origem) ) {

                            //Contatos
                            if(empty($dados->contatos)){
                                 $camposDestaques[] = array('campo' => 'cmctnome');
                                 $camposDestaques[] = array('campo' => 'cmctcpf');
                                 $camposDestaques[] = array('campo' => 'cmctrg');
                                 $camposDestaques[] = array('campo' => 'cmctfone_res');
                                 $camposDestaques[] = array('campo' => 'cmctfone_cel');
                                 $camposDestaques[] = array('campo' => 'cmctfone_com');
                                 $camposDestaques[] = array('campo' => 'cmctfone_nextel');
                                 $camposDestaques[] = array('campo' => 'cmctautorizada');
                                 $camposDestaques[] = array('campo' => 'cmctobservacao');
                            }
                    }
                }

                break;
            case 'analise_credito':
                if(

                    !empty($dados->opcao)
                    && strlen($dados->combo_status) > 0
                    && strlen($dados->motivo) > 0

                    ) {

                    if( $dados->combo_status == 'A') {

                        if(strlen($dados->campo_liberacao) == 0 && strlen($dados->check_periodo) == 0){

                            $camposDestaques[] = array('campo' => 'campo_liberacao');
                        }

                    }

                } else {

                    if ( (strlen($dados->campo_liberacao) == 0 || !isset($dados->check_periodo)) &&
                           strlen($dados->motivo) > 0  && strlen($dados->combo_status) > 0 && $dados->combo_status != 'N') {


                        $camposDestaques[] = array('campo' => 'campo_liberacao');

                    } else if($dados->combo_status == 'N' && strlen($dados->motivo) == 0) {

                        $camposDestaques[] = array('campo' => 'motivo');

                    } else if($dados->combo_status != 'N'){

                        if( $dados->combo_status == 'A' &&
                            (strlen($dados->campo_liberacao) == 0 || isset($dados->check_periodo) )) {

                             if(strlen($dados->campo_liberacao) == 0 && strlen($dados->check_periodo) == 0
                                && $dados->combo_status != 'N') {

                                $camposDestaques[] = array('campo' => 'campo_liberacao');
                            }

                            if(strlen($dados->motivo) == 0) {
                                $camposDestaques[] = array('campo' => 'motivo');
                            }

                        } else {

                            if ($dados->combo_status == '') {

                                $camposDestaques[] = array('campo' => 'combo_status');
                            }

                            if ($dados->motivo == '') {

                                $camposDestaques[] = array('campo' => 'motivo');


                            }

                            if(strlen($dados->campo_liberacao) == 0 && strlen($dados->check_periodo) == 0
                                && $dados->combo_status != 'N') {

                                $camposDestaques[] = array('campo' => 'campo_liberacao');
                            }
                        }
                    }

                }

                break;
        }
        //Fim switch

        //echo "<pre>";var_dump($camposDestaques);echo "</pre>";exit();

        if (!empty($camposDestaques)) {
            $this->view->campos = $camposDestaques;
            throw new Exception(self::MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS);
        }
    }

    /**
    * Recupera dados para popular a combo informada
    * @author Andre L. Zilz
    * @return JSON | array
    *
    */
    public function popularComboAjax() {

        $params = $this->tratarParametros();
        $dados = array();

        switch ($params->combo) {
            case 'usuario':
                $dados = $this->dao->recuperarDadosUsuario($params->oid);
                break;
            case 'tipo_modificacao':
                $dados = $this->dao->recuperarTipoModificacaoAjax($params->oid);
                break;
            case 'banco':
                $dados = $this->dao->recuperarBanco($params->oid);
                break;
            case 'motivo_substituicao_classe':
                $dados = $this->dao->recuperarMotivoSubstituicaoClasse($params->oid, true);
                break;
            case 'sub_tipo_proposta':
                $dados = $this->dao->recuperarSubTipoProposta($params->oid, true);
                break;
            case 'migracao_ex':
                $dados = $this->dao->recuperarTipoContrato(true, self::TIPO_MIGRACAO_EX_SEGURADO);
                break;
             case 'migracao_ex_reativacao':
                $dados = $this->dao->recuperarTipoContrato(true, self::TIPO_MIGRACAO_REATIVACAO_EX_SEGURADO);
                break;
            case 'demonstracao':
                $dados = $this->dao->recuperarTipoContrato(true, self::TIPO_EFETIVACAO_DEMO);
                break;
            case 'nao_demonstracao':
                $dados = $this->dao->recuperarTipoContrato(true);
                break;
             case 'acessorios':
                $dados = $this->dao->recuperarAcessorio($params->oid);
                break;
        }

        echo json_encode($dados);
        exit;
    }

    /**
    * Validar o periodo entre datas
    * @author Andre L. Zilz
    * @param string $dataInicial
    * @param string $dataFinal
    * @return boolean
    */
    private function validarPeriodo($dataInicial, $dataFinal){

        $dataInicial = implode('-', array_reverse(explode('/', substr($dataInicial, 0, 10)))).substr($dataInicial, 10);
        $dataFinal = implode('-', array_reverse(explode('/', substr($dataFinal, 0, 10)))).substr($dataFinal, 10);

        if($dataInicial > $dataFinal) {
            return true;
        }

        return false;
    }

    /**
    * Buscar dados motivo de susbstituicao - AJAX
    *
    * @author Andre L. Zilz
    * @return JSON $retorno
    */
    public function recuperarMotivoSubstituicaoAjax() {

        $parametros = $this->tratarParametros();

        $parametros->nome = $this->tratarTextoInput($parametros->term, true);

        $retorno = $this->dao->recuperarMotivoSubstituicaoAjax($parametros->nome);

        echo json_encode($retorno);
        exit;
    }

     /**
    * Buscar dados motivo de susbstituicao - AJAX
    *
    * @author Andre L. Zilz
    * @return JSON $retorno
    */
    public function recuperarDadosVeiculoAjax() {


        $parametros = $this->tratarParametros();

        $parametros->nome = $this->tratarTextoInput($parametros->term);

        $retorno = $this->dao->recuperarDadosVeiculoAjax($parametros->nome);
        echo json_encode($retorno);
        exit;
    }

    /**
    * Buscar dados clientes - AJAX
    *
    * @author Andre L. Zilz
    * @return JSON $retorno
    */
    public function recuperarCliente() {

        $parametros = $this->tratarParametros();

        if(!empty($parametros->tipo_pessoa)){
            $parametros->texto = preg_replace('/\D/', '', $parametros->term);
        } else {
            $parametros->texto = $this->tratarTextoInput($parametros->term, true);
        }

        $retorno = $this->dao->recuperarCliente($parametros);

        echo json_encode($retorno);
        exit;
    }

    /**
    * Tratamento de input de dados, contra injection code e acentos
    * @author Andre L. Zilz
    * @param string $dado
    * @return string
    */
    private function tratarTextoInput($dado, $autocomplete = false){

        //Elimina acentos para pesquisa
        if($autocomplete){
            $dado = utf8_decode($dado);
        }

        $dado  = trim($dado);
        $dado  = str_replace("'", '', $dado);
        $dado  = str_replace('\\', '', $dado);
        $dado  = strip_tags($dado);

        return $dado;
    }

    /**
    * Destroi o objeto de sessao com os dados de formulario
    * Requisicao AJAX ou PHP
    *
    * @author Andre L. Zilz
    */
    public function destruirSessao() {

        if(isset($_SESSION['dados_modificacao'])) {
            unset($_SESSION['dados_modificacao']);
        }
        
        $this->destruirSessaoPaginacao();
        unset($_POST);
    }

     /**
    * Destroi o objeto de sessao com os dados de paginacao
    *
    * @author Andre L. Zilz
    */
    public function destruirSessaoPaginacao() {

        if(isset($_SESSION['paginacao'])) {
            unset($_SESSION['paginacao']);
        }
    }

    /**
    * Destroi o objeto de sessao e parametros de formulario
    *
    * @author Andre L. Zilz
    */
    public function destruirParametros() {

        $this->view->parametros = null;
        $this->destruirSessao();
    }

    /**
    * Inativa um acessorio associado a uma modificacao
    * @author Andre L. Zilz
    *
    */
    public function excluirAcessorio() {

        try {

            $this->view->parametros = $this->tratarParametros();

            $this->inicializarParametros();

            $this->dao->inativarContratoModificacaoAcessorio($this->view->parametros->contratos_marcados);

            $this->view->parametros = $this->tratarParametros();

            $this->view->tela == 'detalhar';
            $this->view->sub_tela == 'aba_acessorios';
            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_EXCLUIR;

        } catch (ErrorException $e) {
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->view->mensagemAlerta = $e->getMessage();

        }

        require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/index.php";

    }


    /**
    * Recupera os dados em lote de contratos e popula objeto para exibir em tela
    *
    * @author Andre L. Zilz
    *
    */
    public function popularMigracaoLote() {

        $this->view->parametros = $this->tratarParametros();
        $arquivo = $this->view->parametros->arquivo_chassi;

        try{

            $this->view->parametros = isset($_SESSION['dados_modificacao']) ? unserialize($_SESSION['dados_modificacao']): new stdClass();
            $this->view->parametros->migracaoLote = array();

            //rEcupera os Chassis do arquivo
            $chassi =  $this->importarChassi($arquivo);

            //Dados dos contratos conforme chassis
            $this->view->parametros->migracaoLote = $this->dao->recuperarContratoPorChassi($chassi,  $this->view->parametros->cmtoid);

            if(!empty($this->view->parametros->migracaoLote)) {

                $_SESSION['dados_modificacao'] =  serialize($this->view->parametros);
            } else{
                throw new Exception(self::MENSAGEM_ALERTA_NENHUM_CHASSI);
            }

        } catch(Exception $e){
           $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->view->sub_tela = 'aba_itens';
        $this->view->tela = 'cadastro';

        require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/index.php";

    }

    /**
    * Realiza a importacao de arquivo CSV | TXT com chassis de veiculos
    *
    * author Andre L. Zilz
    * @param array $arquivo
    * @return array
    */
    public function importarChassi($arquivo) {

        $linhasVazias = 0;
        $dadosArquivo = array();
        $path = pathinfo($arquivo['name']);

        //Validar extensao
        if( ($path['extension'] != "csv") && ($path['extension'] != "txt") ) {
           throw new Exception(self::MENSAGEM_ALERTA_ARQUIVO_INVALIDO);
        }

        $linhas = explode("\n", file_get_contents($arquivo['tmp_name']));

        foreach ($linhas as $linha) {
            if ((strlen(trim($linha)) == 0)) {
                $linhasVazias++;
            } else {
                $dadosArquivo[] =  "'" . $this->tratarTextoInput($linha,false) . "'";
            }
        }

        if($linhasVazias > 0) {
            throw new Exception(self::MENSAGEM_ALERTA_ARQUIVO_VAZIO);
        }

        return $dadosArquivo;

    }


    /**
    * Gera um arquivo CSV
    *
    * @author Andre L. Zilz
    * @param array | Dados da pesquisa
    * @return array
    */
    private function gerarArquivoCSV($dados) {

        require_once "lib/Components/CsvWriter.php";
        $arquivo = "contrato_moficacao_".date('Ymmhis').".csv";
        $diretorio = '/var/www/docs_temporario/';

        try {
            if (is_dir($diretorio) && is_writable($diretorio)) {

                // Gerar o arquivo CSV
                $csvWriter = new CsvWriter( $diretorio.$arquivo, ';', '', true);

                //Cabecalho
                $csvWriter->addLine(array(
                    'Data',
                    'Nº',
                    'Status Modificação',
                    'Status Financeiro',
                    'Grupo Modificacao',
                    'Tipo Modificacao',
                    'Cliente Origem',
                    'Cliente Destino',
                    'Contrato',
                    'Contrato (novo)',
                    'O.S.',
                    'Status O.S.',
                    'Usuário',
                    'Vigência (meses)',
                    'Tipo Contrato Origem',
                    'Tipo Contrato Destino',
                    'Placa',
                    'Chassi',
                    'Monitoramento',
                    'Locação',
                    'Taxa',
                    'Obrigação Financeira',
                    'Forma Pgto'
                ));

                //Gravar as linhas
                foreach($dados as $dado) {

                    $linha[0] = $dado->data_cadastro;
                    $linha[1] = $dado->mdfoid;
                    $linha[2] = $this->view->legenda_status[$dado->mdfstatus];
                    $linha[3] = $this->view->legenda_status_financeiro[$dado->mdfstatus_financeiro];
                    $linha[4] = $dado->grupo;
                    $linha[5] = $dado->tipo;
                    $linha[6] = $dado->cliente;
                    $linha[7] = $dado->cliente_destino;
                    $linha[8] = $dado->connumero;
                    $linha[9] = $dado->cmfconnumero_novo;
                    $linha[10] = $dado->cmfordoid;
                    $linha[11] = $dado->ordstatus;
                    $linha[12] = $dado->usuario;
                    $linha[13] = $dado->vigencia;
                    $linha[14] = $dado->tipo_contrato_origem;
                    $linha[15] = $dado->tipo_contrato_destino;
                    $linha[16] = $dado->veiplaca;
                    $linha[17] = $dado->veichassi;
                    $linha[18] = $this->tratarMoeda($dado->monitoramento,'A');
                    $linha[19] = $this->tratarMoeda($dado->locacao,'A');
                    $linha[20] = $this->tratarMoeda($dado->taxa,'A');
                    $linha[21] = $dado->obrigacao;
                    $linha[22] = $dado->forma_pgto;

                    $csvWriter->addLine($linha);
                }

                //Verifica se o arquivo foi gerado
                $arquivoGerado = file_exists( $diretorio.$arquivo);
                if ($arquivoGerado === false) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                }

                return array('caminho' => $diretorio . $arquivo , 'arquivo' => $arquivo);
            }
            else {
                throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
            }

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }

    }


    /**
    * Grava o anexo temporariamente na pasta do contrato e recupera
    * os dados da inclusao para listar em tela
    *
    * @author Andre L. Zilz
    */
    public function gravarArquivoTemporario() {

        $dadosTela          = $this->tratarParametros();
        $arquivo            = $dadosTela->arquivo_anexo;
        $dadosCadastro      = unserialize($_SESSION['dados_modificacao']);
        $connumero          = intval($dadosCadastro->cmfconnumero);
        $diretorio          = "/var/www/docs_info_termo/termo". ($connumero % 10) ."/anexos_modificacao_" . $connumero;
        $arquivoNome        = str_replace(array(" ", "'"),"_",$arquivo['name']);
        $arquivoTempNome    = str_replace(array(" ", "'"),"_",$arquivo['tmp_name']);
        $arquivoNome        = preg_replace("[^a-z A-Z 0-9.]", "", strtr(strtolower($arquivoNome), "áàãâéêíóôõúüç", "aaaaeeiooouuc"));
        $path               = $diretorio.'/'.$arquivoNome;
        $listaArquivos      = array();

        try {

            if (!is_dir($diretorio)) {

                if(!mkdir($diretorio, 0777, TRUE)){
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
                }
                }
            else if(!is_writable($diretorio)) {
                throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);

            }

            if(!move_uploaded_file($arquivoTempNome, $path)) {
                 throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO_ARQUIVO);
            }

            $listaArquivos['nome'] = $arquivoNome;
            $listaArquivos['local'] = $dadosTela->local_instalacao;

            //Limpar valores repetidos
            foreach ($dadosCadastro->anexos as $chave => $valor) {
                if($valor['nome'] == $arquivoNome) {
                   unset($dadosCadastro->anexos[$chave]);
                }
            }

            array_push($dadosCadastro->anexos, $listaArquivos);

            $this->view->parametros = $dadosCadastro;

            $_SESSION['dados_modificacao'] = serialize($dadosCadastro);

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }

        $this->view->sub_tela = 'aba_anexos';
        $this->view->tela = 'cadastro';

        require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/index.php";

    }

    /**
    * Remover um arquivo anexo do diretorio do contrato
    *
    * @author Andre L. Zilz
    */
    public function removerArquivoTemporarioAjax() {

        $retorno = 'NOK';
        $dadosTela = $this->tratarParametros();
        $dadosCadastro = unserialize($_SESSION['dados_modificacao']);
        $connumero      = intval($dadosCadastro->cmfconnumero);
        $diretorio      = "/var/www/docs_info_termo/termo". ($connumero % 10) ."/anexos_modificacao_" . $connumero;
        $path = $diretorio.'/'.$dadosTela->arquivo;

        try {

             if ( !is_dir($diretorio) || (!is_writable($diretorio)) ) {
                $retorno = 'NOK';
             } else {

                foreach ($dadosCadastro->anexos as $chave => $valor) {
                    if($valor['nome'] == $dadosTela->arquivo) {
                       unset($dadosCadastro->anexos[$chave]);
                    }
                }

                if( file_exists($path)) {
                unlink($path);
                }

                $_SESSION['dados_modificacao'] = serialize($dadosCadastro);

                $retorno = 'OK';
             }

        } catch (Exception $e) {
            $retorno = 'NOK';
        }

        echo $retorno;
        exit();

    }

    /**
    * Inicia o processo de pagamento de taxa com cartao de credito
    *
    * @author Andre L. Zilz
    * @param stdClass $dados
    *
    */
    private function pagarTaxaCartaoCredito($dados) {

        $dadosTaxa = array();
        $arrContrato = array();
        $msgErroCartao = '';

        //Recupera dados do pagamento
        $dados->dados_pagamento = $this->dao->recuperarDadosPagamentoCartao($dados->mdfoid);

        $camposContrato = array(
                        "COUNT(cmfoid) OVER() AS total_contratos",
                        "cmfprpoid",
                        "cmfclioid_destino",
                        "cmfconnumero",
                        "cmfconnumero_novo");

        //Recupera dados do contrato
        $contratos = $this->dao->recuperarContratoModificacao($dados->mdfoid,false, $camposContrato);

        $dataCartao = explode('/', $dados->dados_pagamento->cmdfpcartao_vencimento);

        // Parametros de envio
        $dadosTaxa['taxa_valor_item'] 			= $dados->dados_pagamento->cmdfpvlr_taxa_negociado;
        $dadosTaxa['taxa_valor_total'] 			= (floatval($contratos[0]->total_contratos) * floatval($dados->dados_pagamento->cmdfpvlr_taxa_negociado));
        $dadosTaxa['taxa_qntd_parcelas'] 		= $dados->dados_pagamento->cmdfpnum_parcela;
        $dadosTaxa['taxa_id_obrigacao'] 		= $dados->dados_pagamento->cmdfpobroid_taxa;
        $dadosTaxa['taxa_descricao_obrigacao'] 	= $dados->dados_pagamento->obrobrigacao;
        $dadosTaxa['taxa_forma_pagamento'] 		= $dados->dados_pagamento->cmdfpforcoid;
        $dadosTaxa['taxa_data_vencimento'] 		= date('d-m-Y');
        $dadosTaxa['taxa_num_parcela'] 			= 1;
        $dadosTaxa['taxa_num_cartao'] 			= $dados->dados_pagamento->cmdfpcartao;
        $dadosTaxa['taxa_nome_portador'] 		= $dados->dados_pagamento->cmdfpnome_portador;
        $dadosTaxa['taxa_data_validade_cartao'] = $dataCartao[0] . substr($dataCartao[1], 2);
        $dadosTaxa['taxa_codigo_seguranca'] 	= $dados->cartao_codigo;
        $dadosTaxa['taxa_serie'] 				= 'A';

        $prpoid = $contratos[0]->cmfprpoid;

        foreach ($contratos as $contrato) {
            $arrContrato[] = empty($contrato->cmfconnumero_novo) ? $contrato->cmfconnumero : $contrato->cmfconnumero_novo;
        }

        $clioid = $contratos[0]->cmfclioid_destino;

        /*
        * `Passar dados como POST por uma particularidade do metodo confirmarFormaPagamento presente na classe PrnManutencaoFormaCobrancaCliente
        */
        $_POST['clioid']            			= $clioid;
        $_POST['forcoid']           			= $dados->dados_pagamento->cmdfpforcoid;
        $_POST['entrada']                     	= "I"; //Canal de Entrada do histórico: I = Intranet; P = Portal
        $_POST['origem_chamada']    			= "CORE";
        $_POST['forma_pagamento_clidia_vcto'] 	= $dados->dados_pagamento->cmdfpvencimento_fatura;
        $_POST['cod_usu']           			= $this->usuoid;
        $_POST['numero_cartao']     			= $dados->dados_pagamento->cmdfpcartao;
        $_POST['mes_ano']           			= $dataCartao[0] . "/" . substr($dataCartao[1], 2);

        $tituloCobranca = new TituloCobranca();
        $retornoTituloCobranca = $tituloCobranca->geraTaxaCartao($prpoid, $this->usuoid,  $clioid, $arrContrato, $dadosTaxa);

        if(!$retornoTituloCobranca->dados){
            throw new Exception(self::MENSAGEM_ALERTA_PGTO_CARTAO . $retornoTituloCobranca->mensagem);
        }
    }

    /**
    * Verifica situacao de credito do cliente
    * AJAX
    * @author Andre L. Zilz
    */
    public function submeterAnaliseCredito($parametros) {


        $dadosAnaliseEnvio = array();
        $valorAcessorios   = 0;
        $statusFinanceiro  = '';
        $docCliente = preg_replace('/\D/', '', $parametros->cpf_cnpj);
        $clioid            = $parametros->cmfclioid_destino;
        $totalCompra       = (floatval($parametros->cmdfpvlr_locacao_negociado) + floatval($parametros->cmdfpvlr_monitoramento_negociado));
        $totalCompra =  ($totalCompra * $parametros->totalContratos);

        if($parametros->produto_siggo == 't'){
            $tipoProposta = is_null($parametros->cmftppoid) ? 0 : $parametros->cmftppoid;
            $subtipoProposta = is_null($parametros->cmftppoid_subtitpo) ? 0 : $parametros->cmftppoid_subtitpo;

        } else {
            $tipoProposta  = $this->dao->recuperarTipoPropostaContrato($parametros->cmfconnumero);
            $subtipoProposta = 0;
            }

        //corrige o comprimento de digitos
        if($parametros->tipo_pessoa == 'F') {
           $docCliente = $this->aplicarMascara('###########',$docCliente, 'D', '0');
        } else {
           $docCliente = $this->aplicarMascara('##############',$docCliente, 'D', '0');
        }

        $retornoConsulta = $this->verificarAprovacaoCreditoFinanceiro($clioid);
        $cmacoid = $retornoConsulta->cmacoid;

        switch ($retornoConsulta->status) {
            case 'A':
                $statusFinanceiro =  $retornoConsulta->status;
                break;
            case 'N':
                $statusFinanceiro =  'P';
                $obs = "Crédito não aprovado pelo financeiro ou com aprovação expirada.";
                $cmacoid = $this->dao->persistirAnaliseCredito($clioid, $obs);

                break;
            case 'P':
                $statusFinanceiro =  $retornoConsulta->status;
                break;
            default:
                $dadosAnaliseEnvio['formaPagamento']   = $parametros->cmdfpforcoid;
                $dadosAnaliseEnvio['tipoPessoa']       = $parametros->tipo_pessoa;
                $dadosAnaliseEnvio['tipoProposta']     = $tipoProposta;
                $dadosAnaliseEnvio['subTipoProposta']  = $subtipoProposta;
                $dadosAnaliseEnvio['tipoContrato']     = $parametros->cmftpcoid_destino;
                $dadosAnaliseEnvio['qtdEquipamentos']  = $parametros->totalContratos;
                $dadosAnaliseEnvio['valorTotalCompra'] = $totalCompra;

                //Submete analise de credito ao SERASA
                $analiseCredito = GestorCredito::analisaCredito($docCliente, $dadosAnaliseEnvio, 'P');
                $mensagemGestor = $analiseCredito->dados['prpresultado_str'];
                $statusGestor = $analiseCredito->dados['prpstatus_aprovacao'];

                //Se retorno diferente de zero, deve ser solicitado a aprovacao manual pelo financeiro
                $statusFinanceiro = ($statusGestor == 0) ? 'A' : 'P';

                if($statusFinanceiro == 'P'){
                    $obs = $mensagemGestor;
                    $cmacoid = $this->dao->persistirAnaliseCredito($clioid, $obs);
            }
                break;
        }

        if(!empty($cmacoid)){
        $this->dao->atualizarStatusFinanceiroPorModificacao($statusFinanceiro, $parametros->mdfoid, $cmacoid);
        }

        }


    /**
     * Verifica a situacao da solicitacaod e aprovacaod e um determinado cliente
     * @author Andre L. Zilz <andre.zilz@sascar.com.br>
     * @param $clioid
     * @return  status da Aprovacao
    */
    private function verificarAprovacaoCreditoFinanceiro($clioid) {

        $retorno = new stdClass();
        $retorno->status = '';
        $retorno->cmacoid = '';

        $dadosAprovacao = $this->dao->verificarAprovacaoCredito($clioid);

        if(count($dadosAprovacao) > 0) {

            $retorno->status = $dadosAprovacao->status;
            $retorno->cmacoid = $dadosAprovacao->cmacoid;

            if($retorno->status == 'A') {
                //Se nao for periodo indeterminado e a data limite de aprovacao estiver vencida
                if (($dadosAprovacao->periodo_indeterminado != 't') && (!$dadosAprovacao->is_data_limite)) {
                    $retorno->status = 'N';
                }
            }
        }

        return $retorno;

    }


    /**
    * faz as validacoes necessarias para o contrato
    * Requisicao: AJAX
    * @author Andre L. Zilz
    */
    public function validarContrato() {

        $parametros = $this->tratarParametros();
        $retorno = array();
        $retorno['erro'] = '';
        $retorno['conno_tipo'] = '';
        $retorno['coneqcoid'] = '';
        $retorno['conprazo_contrato'] = '';
        $campos = array(
                'conclioid',
                'conno_tipo',
                'coneqcoid',
                'conveioid',
                'conequoid',
                'conprazo_contrato'
                );

        try{

            if(!$this->dao->isExisteContrato($parametros->cmfconnumero)){
                throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_INATIVO);
            }

            //Recupera dados do contrato
            $contrato = $this->dao->recuperarDadosContrato($parametros->cmfconnumero, $campos);

          if(is_null($contrato->conno_tipo)) {
                 throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_SEM_TIPO);
            }

            if(is_null($contrato->coneqcoid)) {
                 throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_SEM_CLASSE);
            }

            if(is_null($contrato->conveioid)) {
                 throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_SEM_VEICULO);
            }

            if(is_null($contrato->conequoid)) {
                 throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_SEM_EQUIP);
            }

             //Cliente informado e o mesmo do contrato?
            if($parametros->cmpttroca_cliente == 't') {

                if($parametros->cmfclioid_destino == $contrato->concliod) {
                    throw new Exception(self::MENSAGEM_ALERTA_TROCA_CLIENTE);
                }
            }

            //Contrato roubado nao recuperado?
            if($parametros->cmtcmgoid == '6') {

                if(!$this->dao->isContratoRoubado($parametros->cmfconnumero)){
                    throw new Exception(self::MENSAGEM_ALERTA_CONTRATO_ROUBADO);
                }
            }

            $retorno['conno_tipo'] = $contrato->conno_tipo;
            $retorno['coneqcoid'] = $contrato->coneqcoid;
            $retorno['conprazo_contrato'] = $contrato->conprazo_contrato;

        } catch (Exception $e){
            $retorno['erro'] = utf8_encode($e->getMessage());
        } catch (ErrorException $e){
            $retorno['erro'] = 'erro_banco';
        }

        echo json_encode($retorno);
        exit;
    }

     /**
    * Recupera os dados de um determinado veiculo
    * Requisicao: AJAX
    * @author Andre L. Zilz
    */
    public function recuperarDadosVeiculo(){

        $parametros = $this->tratarParametros();
        $dados = array();

        $contrato = $this->dao->recuperarDadosContrato($parametros->cmfconnumero,array('conveioid'));

        $veiculo = Veiculo::veiculoGetDados($contrato->conveioid, 'ID');

        $dados['veioid'] = $veiculo->dados['veioid'];
        $dados['veiplaca'] = $veiculo->dados['veiplaca'];

        echo json_encode($dados);
        exit;
    }

    /**
    * Recupera os dados de Monitoramento
    * Requisicao: AJAX
    * @author Andre L. Zilz
    */
    public function recuperarDadosMonitoramento() {

        $parametros = $this->tratarParametros();

        //Definir o ID da classe de contrato
        if(empty($parametros->eqcoid) && (!empty($parametros->cmfconnumero))) {
            $contrato = $this->dao->recuperarDadosContrato($parametros->cmfconnumero,array('coneqcoid'));
            $eqcoid = $contrato->coneqcoid;
        } else {
            $eqcoid = $parametros->eqcoid;
        }

        ///Recuperar Valor Monitor
        $dados = $this->dao->recuperarValorMonitoramento($eqcoid);

        //Formatar moeda
        $dados->cmdfpvlr_monitoramento_negociado = $this->tratarMoeda($dados->cmdfpvlr_monitoramento_negociado, 'A');
        $dados->eqcvlr_minimo_mens   = $this->tratarMoeda($dados->eqcvlr_minimo_mens, 'A');
        $dados->eqcvlr_maximo_mens   = $this->tratarMoeda($dados->eqcvlr_maximo_mens, 'A');

        echo json_encode($dados);
        exit;
    }

    /**
    * Cancela uma modificacao
    * Requisicao: AJAX
    *
    * @author Andre L. Zilz
    * @return void
    */
    public function recuperarMonitoramentoLocacaoContrato() {

        $retorno = new stdClass();

        $this->view->parametros = $this->tratarParametros();

        //Inicializa os dados
        $this->inicializarParametros();

        $dados = $this->view->parametros;

        try {

             $retorno = $this->dao->recuperarMonitoramentoLocacaoContrato($dados->cmfconnumero);

        } catch (Exception $e) {
            echo json_encode($retorno);
            exit();
        }

        echo json_encode($retorno);
        exit();
    }

    /**
    * Recupera os dados de Monitoramento
    * Requisicao: AJAX
    * @author Andre L. Zilz
    */
    public function recuperarDadosLocacao() {

        $dados = new stdClass();

        $parametros = $this->tratarParametros();

        //Definir o ID da classe de contrato
        if(empty($parametros->eqcoid)){
            $contrato = $this->dao->recuperarDadosContrato($parametros->cmfconnumero,array('coneqcoid'));
            $parametros->eqcoid = $contrato->coneqcoid;
        }

        //Recuperar Valor Monitor
        $dados = $this->dao->recuperarValorLocacao($parametros);

        echo json_encode($dados);
        exit;
    }

    /**
    * Valida Compatibilidade CAN
    * Requisicao: AJAX
    * @author Marcello Borrmann
    */
    public function validaCompatibilidadeCAN() {

    	$compativel = new stdClass();
    	
	    $parametros = $this->tratarParametros();
	    
	    $eqcoid 	= null;
	    $classeCAN 	= null;
	    $veiculo 	= null;
	    $dados 		= null;
	    
    	try{
	
	        //Definir o ID da classe de contrato
	        if (empty($parametros->eqcoid) && (!empty($parametros->cmfconnumero))) {
	            $contrato = $this->dao->recuperarDadosContrato($parametros->cmfconnumero,array('coneqcoid'));
	            $eqcoid = $contrato->coneqcoid;
	        } else {
	            $eqcoid = $parametros->eqcoid;
	        }	        
	        if ($eqcoid == null) {
	        	throw new Exception('Não foi possível determinar a Classe.');
	        }
	        
	        // Verifcar se a classe necessita ser validada
	        $classeCAN = $this->dao->verificarClasseCAN($eqcoid);        
	        if ($classeCAN == null) {
	        	throw new Exception('Não foi possível determinar as classes relacionadas à Telemetria CAN.');
	        }

	        $eqcdescricao 	= $classeCAN->eqcdescricao;
	        $arrayClasse 	= explode(",", $classeCAN->valvalor);
	        
	        // Valida a compatibilidade
	        if (in_array($eqcoid, $arrayClasse)) {
	        	
	        	// Busca dados do veículo através da placa do novo veículo
	        	if (!empty($parametros->veiplaca_novo)) {
	        		$veiculo = Veiculo::veiculoGetDados($parametros->veiplaca_novo, 'PL');
	        	} 
	        	// Busca dados do veículo através do código do veículo no contrato
	        	else {	        	
	        		$contrato = $this->dao->recuperarDadosContrato($parametros->cmfconnumero,array('conveioid'));
	        		$veiculo = Veiculo::veiculoGetDados($contrato->conveioid, 'ID');
	        	}
				// Caso não tenha encontrado dados do veículo
	        	if (empty($veiculo)) {
	        		throw new Exception('Não foi possível buscar os dados do veículo.');
	        	}
	        	// Caso o veiculo não possua Ano informado
	        	if ($veiculo->dados['veino_ano'] == null) {
	        		throw new Exception('Não foi possível realizar esta operação, pois a informação de "Ano" é fundamental para Classes "'.$eqcdescricao.'", favor verificar o cadastro do veículo.');
	        	}
	        	// Busca dados da compatibilidade
	        	$dados = $this->dao->validarCompatibilidade($eqcoid, $veiculo);
	        	
	        	// Caso não haja um registro referente ao Modelo/ Ano do veículo 
	        	if ($dados == null) {
	        		// Busca dados para exibição da msg de compatibilidade desconhecida
	        		$dados = $this->dao->buscarDadosMsgCAN($eqcoid, $veiculo);

                    foreach ($dados as $dado) {
                    # INSERE A COMPATIBILIDADE
                    $cavoid = $this->dao->registraCompatibilidadeAcessorio($veiculo);
                    # INSERE REGISTRO DE HISTÓRICO E DISPARA E-MAIL PARA HOMOLOGAÇÃO
                        $this->dao->registraHistoricoNotificacao($cavoid, $veiculo->dados['veimlooid'], $veiculo->dados['veino_ano'], $dado->mlomodelo, $parametros->executivo);
                 
                        # ENVIA E-MAIL SOLICITANDO HOMOLOGAÇÃO
                        $this->enviaEmailSolicitacaoHomologacao($dado->mlomodelo, $veiculo->dados['veino_ano'], $cavoid);

                    }
                    
	        	} else {

                    foreach ($dados as $dado) {
                        if ($dado->cavstatus == ''){
                        // Verificando se já existe um registro de notificação
                        $res = $this->dao->verificaHistoricoNotificacao($veiculo->dados['veimlooid'], $veiculo->dados['veino_ano'], $parametros->executivo);
                            if (!$res){
                                # INSERIR REGISTRO NO HISTÓRICO CASO AINDA NÃO TENHA
                            $modeloVei = $dado->mlomodelo .", ano ". $dado->cavano;
                            $this->dao->registraHistoricoNotificacao($dado->cavoid, $veiculo->dados['veimlooid'], $veiculo->dados['veino_ano'], $modeloVei, $parametros->executivo);
                        }
                    }
                    }
                    
	        }
		
	        }
		
        } catch (Exception $e) {
        	$dados['erro'] = utf8_encode($e->getMessage());
        } 
		
		echo json_encode($dados);
		exit;
		
    }

    /**
    * Enviar e-mail
    * @param String $modelo Modelo do veículo
    * @param int $ano Ano do veículo
    * @param int $cavoid OID do cadastro de compatibilidade
    **/
    public function enviaEmailSolicitacaoHomologacao($modelo, $ano, $cavoid)
    {
        try{

            // Buscando os destinatários
            $ssql = "SELECT valvalor FROM valor 
            INNER JOIN registro ON valregoid = regoid
            INNER JOIN dominio ON regdomoid = domoid
            WHERE domnome = 'DESTINATÁRIO DOS PEDIDOS DE HOMOLOGAÇÃO DO CADASTRO DE COMPATIBILIDADE CAN'
            AND valtpvoid <> 6";

            $res = pg_query($ssql);

            if ($res) {
                if (pg_num_rows($res) > 0) {
                    $valores = pg_fetch_all($res);
                }
            }
            $destinatarios = array();
            foreach ($valores as $valor) {
                $destinatarios[] = $valor['valvalor'];
            }
            
            // Buscando o layout do e-mail
            $SendLayoutEmails = new SendLayoutEmails();

            //retorna o codigo do titulo e da funcionalidade de acordo com o nome do titulo passado
            $dadosLayout = $SendLayoutEmails->getTituloFuncionalidade('Solicitação de Homologação de veículo');

            if($dadosLayout == null || empty($dadosLayout) || !isset($dadosLayout) || count($dadosLayout) == 0 ){
                throw new Exception("Layout de e-mail não encontrado");
            }  

            $codigoLayout[] = $SendLayoutEmails->buscaLayoutEmail(array(
                    'seeseefoid' => $dadosLayout[0]['funcionalidade_id'],
                    'seeseetoid' => $dadosLayout[0]['titulo_id']
                    )
            );

            foreach ($codigoLayout as $chave => $valor) {
                //busca o layout de acordo com o ID do codigo do layout
                $layouts[] = $SendLayoutEmails->getLayoutEmailPorId ($valor['seeoid']);
            }

            # Substituindo as variáveis do texto pela informação correta
            $subject = str_replace(array('[MODELO]', '[ANO]'), array($modelo, $ano), $layouts[0]['seecabecalho']);
            $body = str_replace(array('[MODELO]', '[ANO]'), array($modelo, $ano), $layouts[0]['seecorpo']);

            $result = $this->sendMail(array( 'subject' => $subject, 
                            'body' => $body, 
                            'to'=> $destinatarios));

            if ($result){

                # ATUALIZA CAMPO cavdt_email_solicita_homologacao na TABELA COMPATIBILIDADE
                $sqlUpdateData = "UPDATE compatibilidade_acessorio_veiculo 
                                  SET cavdt_email_solicita_homologacao = 'NOW()' 
                                  WHERE cavoid = {$cavoid}";
                
                $rs = pg_query($sqlUpdateData);
            }

        }catch(Exception $ex){
            return $ex->getMessage();
        }

    }

    /**
    * Método para enviar emails
    * @param array $emails array(email, nome)
    * @param String $subject assunto do e-mail
    * @param String $corpo corpo do e-mail
    **/
    public function sendMail($params)
    {
        try{
            $mail = new PHPMailer();
            
            $mail->isSMTP();
            $mail->From = "sascar@sascar.com.br";
            $mail->FromName = "sistema@sascar.com.br";
            $mail->Subject = $params['subject'];
            $mail->MsgHTML($params['body']);
            $mail->ClearAllRecipients();

            if ($_SESSION['servidor_teste'] == 1) {                
                foreach ($params['to'] as $to) {
                    $mail->AddAddress("teste_desenv@sascar.com.br", $email[1]);
                }
            } else {
                foreach ($params['to'] as $to) {
                    $mail->AddAddress($to[0], $to[1]);
                }
            }

            # Verifica se foi enviado
            return $mail->send();
            
        }catch(Exception $ex) {
            return $ex->getMessage();
        }

    }



    /**
    * STI 86643
    * Método responsável por validar o status da linha vinculada ao equipamento
    * @author Thomas de Lima - <thomas.lima.ext@sascar.com.br>
    * @param stdCLass $parametros
    * Atributos obrigatórios do objeto: 
    * 1) ajax = true|false - Identifica se a requisição é ajax ou não, 
    * 2) cmtoid = código do tipo da modificação,
    * 3) connumero = numero do contrato
    **/
    public function validaStatusLinha($parametros = null)
    {
        //Inicializando as variáveis
        if ($parametros === null){
            $parametros = $this->tratarParametros();
        }
	    $dados = null;

        /* AS REGRAS ABAIXO ESTÃO PARAMETRIZADAS NA TABELA celular_status_linha_particularidade */
        /**
        * Regras que devem ser aplicadas de acordo com o tipo e o status da linha
        * OBS: As chaves dos arrays são os ID's dos registros
        **/
        $regras = $this->dao->recuperaParticularidadesStatusLinha();

        try{
            // Buscando o status da linha
            $linha = (object)$this->dao->recuperaStatusLinha($parametros->connumero);
            $linha->cslstatus =  strtoupper($linha->cslstatus); //Jogando tudo pra maiúscula

            // Verificando se existe linha, se não existir, atribui status 0 para que siga a regra correta.
            $linha->lincsloid = ($linha->linoid != "" ? $linha->lincsloid : 0); 

            // Verificando se existe regra prevista para o tipo da migração e o status da linha
            if ($regras[$parametros->cmtoid][$linha->lincsloid]){

                //Verificando se a regra encontrada deve travar a migração
                if ( $regras[$parametros->cmtoid][$linha->lincsloid]['trava'] ){
                    throw new Exception( sprintf($regras[ $parametros->cmtoid ][ $linha->lincsloid ][ 'alerta' ], $linha->cslstatus) );
                }
                //Verifica se existe um callback, e executa caso encontre.
                $callback = $regras[$parametros->cmtoid][$linha->lincsloid]['callback'];
                if ( $callback !== "" && $callback !== null){

                    //Chamando a função especificada no atributo callback do array
                    if (!$resultCallback = self::$callback($linha)){
                        throw new Exception($resultCallback);
                    }
                }
            }

        }catch(Exception $e){
            $dados['erro'] = utf8_encode($e->getMessage());

            if (!$parametros->ajax){
                return $e->getMessage();
            }
        }

        // Verificando se foi uma requisição AJAX
        if ($parametros->ajax){
            echo json_encode($dados); 
            exit;
        }else{
            return true;
        }
    }

    /**
    * Método para adicionar a linha no relatório de linhas para reativação
    * @author Thomas de Lima <thomas.lima.ext@sascar.com.br>
    **/
    public function addLinhaRelatorioReativacao($linha = null)
    {
        try{
            if ($linha === null){
                throw new Exception("OS PARÂMETROS ENVIADOS PARA A INSERÇÃO NO RELATÓRIO SÃO INVÁLIDOS");
            }
            if ( $linha->linnumero == "" || $linha->conclioid == "" || $_SESSION['usuario']['oid'] == "" || $linha->linaraoid == "") {
                throw new Exception("Estão faltando informações para incluir no relatório de linhas para reativação");
            }

            //Se passar pela validação, grava
            $result = $this->dao->addLinhaRelatorioReativacao($linha);
            //Verifica se teu tudo certo
            if ($result > 0){
                return true;
            }else{
                return $result;
            }


        }catch(Exception $e){
            return $e->getMessage();
        }

    }


    /**
    * Formatar moeda entre brasileiro / americano
    *
    * @author Andre Zilz
    * @param float $valor | valor original
    * @param string $formato | [A] = americano para brasileiro, [B] = brasileiro para americano
    */
    public function tratarMoeda($valor, $formato){

        if($formato == 'A'){
            $valor = str_replace(",", "", $valor);

            $ponto = stripos($valor, '.');

            if($ponto === false) {
                $valor = $valor . ',' . '00';
            } else {
                $casas = explode('.', $valor);
                $valor = $casas[0] . ',' . ((strlen($casas[1]) == 1) ? ($casas[1] . '0') : $casas[1]);
            }

        } else {
            $valor = str_replace(",", ".", $valor);
        }

        return $valor;
    }

    /**
    * Formata qualquer numero em qualquer mascara
    *
    * @author Andre L. Zilz
    * @param string $mascara | Mascara. Ex formato CPF: ###.###.###-##
    * @param string $codigo | valor numerico
    * @param string $ordem | crescente [C] | descrescente [D]
    * @param string $coringa | preencher digitos faltantes com?
    * @return string
    *
    */
    private function aplicarMascara($mascara, $codigo, $ordem, $coringa) {

        $codigo = str_replace(" ","",$codigo);

        if($ordem == 'D'){

            for($i = strlen($codigo); $i>0; $i--) {

                $mascara[strrpos($mascara,"#")] = $codigo[$i-1];
            }
        } else {
            for($i = 0; $i < strlen($codigo); $i++) {

               $mascara[strpos($mascara,"#")] = $codigo[$i];
            }
        }

        $mascara = str_replace("#", $coringa, $mascara);

        return $mascara;
    }

    /**
    * Reorganiza o array de uma lista vinda do formulario
    *
    * @author Andre L. Zilz
    * @param stdClass $lista
    * @param stdCLass
    */
    private function reordenarListaFormulario($lista) {

           $arrayAux1 = array();
           $arrayAux2 = array();
           $objeto = new stdClass();

            //Passo 1
            foreach ($lista as $chave => $dados) {

                foreach ($dados as $indice => $valor) {

                    $arrayAux1[$indice][$chave] = $valor;
                }
            }

            //Passo 2
            foreach ($arrayAux1 as $key => $value) {
                $arrayAux2[$key] = (object) $value;
            }

            return $arrayAux2;

    }

    /**
     * Funcoes de modificacao de contrato
     *
     * @author Vinicius Senna
     * @param int $cmfoid | id da tabela contrato_modificacao
     */
    public function modificarContratoMigracaoSeg($cmfoid, $possuiAcessorioAdicional) {

        $connumeroNovo = 0;

        // Busca informações da modificacao
        $infoModificacao = $this->dao->recuperaInformacoesModificacao($cmfoid);

        if(is_object($infoModificacao)) {

            $arrParametros = $this->parametrosFuncoes($infoModificacao,$this->usuoid);

            // MIGRACAO PARA EX - categoria 1
            // MIGRACAO DE EX PARA ATIVO - categoria 2
            // MIGRACAO DE EX PARA CLIENTE - categoria 3
            $connumeroNovo = $this->migracaoContrato($arrParametros,$infoModificacao, $possuiAcessorioAdicional);
        }

        return $connumeroNovo;

    }

    /**
     * Passos da migracao, utilizados nas categorias 1, 2 e 3
     *
     * @author Vinicius Senna
     * @param int $categoria | id da categoria
     */
    public function migracaoContrato($arrParam,$infoModificacao, $possuiAcessorioAdicional) {

        $categoria = $arrParam['categoria'];

        // ALTERA CONTRATO ORIGINAL - OK
        $this->dao->alteraContratoOriginalMigracao($arrParam['connumero'], $arrParam['cmfveioid'], $arrParam['classe_contrato_original']);

        // Se cancela contrato original - OK
        if($arrParam['cancela_contrato_original'] == 't') {
            $this->dao->cancelaContratoOriginalMigracao($arrParam['connumero'],$arrParam['cd_usuario']);
        }

        // Gera contrato novo - OK
        $connumeroNovo = $this->dao->geraContratoNovoMigracao($arrParam['cd_usuario'],$arrParam['cmfoid']);

        if($connumeroNovo > 0) {

            // Faz update na contrato_modificacao gravando $connumero_novo em cmfconnumero_novo; - OK
            $this->dao->atualizaContratoModificacao($connumeroNovo, $arrParam['cmfoid']);

            // GRAVA NUMERO DE CONTRATO NOVO NA TABELA TERMO_NUMERO - OK
            $terntnsoid = 2;
            $ternrelroid = 1;
            $this->dao->geraTermoNumeroMigracao($connumeroNovo,$terntnsoid, $ternrelroid,$connumeroNovo);

            // Gera historico - OK
            $hitobs1 = "Migração Seguradora/Associação via módulo de Modificação. " . $arrParam['motivo_migracao'];
            $hitobs1 .= " .Id da modificação " . $arrParam['mdfoid'];
            $hitprotprotocolo1 = "contrato novo: ".$connumeroNovo;

            $hitobs2 = "Migração Seguradora/Associação via módulo de Modificação. ";
            $hitobs2 .= $arrParam['obs_modificacao'].". Id da modificação ".$arrParam['mdfoid'];
            $hitobs2 .= ". Contrato Original:".$arrParam['connumero'];

            $observacaoHistEqp = "Migração contrato " . $arrParam['connumero'] . " para " . $connumeroNovo;

            $this->dao->insereHistoricoTermoMigracao($arrParam['connumero'], $arrParam['cd_usuario'], $hitobs1, $hitprotprotocolo1);
            $this->dao->insereHistoricoTermoMigracao($connumeroNovo, $arrParam['cd_usuario'], $hitobs2);
            $this->dao->insereHistoricoTermoNumeroMigracao($connumeroNovo,$arrParam['cd_usuario']);

            // Insere na tabela historico_equipamento - OK
            $this->dao->insereHistoricoEquipamento($connumeroNovo, $arrParam['cd_usuario'],$observacaoHistEqp);

            // Migra os contatos - OK
            $this->dao->migraContatos($arrParam['connumero'], $connumeroNovo);
            // Busca na tabela contrato_modificacao_contato e INSERE na telefone_contato;
            // Insere na tabela telefone_contato - OK

            $this->migraTelefonesContatos($connumeroNovo,$arrParam['mdfoid'],$infoModificacao->cmfclioid_destino);

            // Atualiza tabela cliente_perfil - OK
            $this->dao->atualizaClientePerfil($connumeroNovo, $arrParam['cd_usuario'], $arrParam['connumero']);
            // Cancela ordem de servico - ver contrato original 4 ou 1 p/9
            $descricao = $arrParam['descricao_tipo_mod'];
            $descricaoHist = $arrParam['descricao_tipo_mod'] . " " .$arrParam['obs_modificacao'];
           
            $this->dao->cancelaOrdemServico($arrParam['connumero'], $arrParam['cd_usuario'], $descricaoHist, 'connumero');

            // GERA OS DE RETIRADA
            if($categoria == 1) {
                $ordoidRetirada = null;

                if($arrParam['gera_os_retirada'] == 't') {

                    $mtioid = 5;

                    // Cria ordem de servico de retirada - OK TODO: return ordoid;
                    $ordoidRetirada = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$arrParam['cd_usuario'], $arrParam['cmfoid'],$connumeroNovo);
                    // INSERE O SERVIÇO DE RETIRADA DO EQUIPAMENTO PRINCIPAL - OK
                    $this->dao->insereServicosOS($ordoidRetirada,8,'Retirada por motivo de Migração para Ex, via módulo de Modificação de Contrato.','P');
                    // INSERE RETIRADA PARA OS ACESSÓRIOS - OK
                    $this->dao->insereRetiradaAcessorios($ordoidRetirada);
                    // INSERINDO O HISTÓRICO NA O.S - OK
                    $this->dao->atualizaOSContratoModificacao($ordoidRetirada, $arrParam['cmfoid']);
                    $this->dao->insereOrdemSituacao($ordoidRetirada, $arrParam['cd_usuario'], $descricaoHist,9);

                }

                $this->pagamentoContratoPagamento($arrParam,$connumeroNovo,$ordoidRetirada);

            }

            if($categoria == 2 || $categoria == 3) {

                $ordoid = null;


                if (($arrParam['gera_os_teste'] == 't') && (!$possuiAcessorioAdicional)) {

                    $mtioid = 0;
                    $ositotioid = 0;

                    /**
                     *  Se for do Grupo UPGRADE (cmtcmgoid == 1 || cmtcmgoid == 2):
                     *  $descricao=Descrição do Tipo de Modificação
                     *  $descricao_hist="Descrição do Tipo de Modificação"
                     *  + conteúdo do campo mdfobservacao_modificacao da tabela modificacao,
                     *  fazendo JOIN com a tabela contrato_modificacao onde cmfmdfoid=mdfoid E cmfoid=$cmfoid;
                     */
                    $descricao = (string) $arrParam['descricao_tipo_mod'];
                    $descricaoHist = $arrParam['descricao_tipo_mod'].' '.$arrParam['obs_modificacao'];

                    if($arrParam['cmtcmgoid'] == 1 || $arrParam['cmtcmgoid'] == 2 || $arrParam['cmtcmgoid'] == 13) {
                        $mtioid = 16;
                        $ositotioid = 102;
                    }

                    /**
                     *   Se for do Grupo MIGRACAO (cmtcmgoid == 3):
                     *   $descricao=Descrição do Tipo de Modificação
                     *   $descricao_hist="Descrição do Tipo de Modificação"
                     *   + conteúdo do campo mdfobservacao_modificacao da tabela modificacao,
                     *   fazendo JOIN com a tabela contrato_modificacao onde cmfmdfoid=mdfoid E cmfoid=$cmfoid;
                     */
                    if($arrParam['cmtcmgoid'] == 3 || $arrParam['cmtcmgoid'] == 4) {
                        $mtioid = 10;
                        $ositotioid = 102;
                    }

                    if($mtioid > 0 && $ositotioid > 0) {

                        $ordoid = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$arrParam['cd_usuario'], $arrParam['cmfoid'],$connumeroNovo);
                        $this->dao->atualizaOSContratoModificacao($ordoid, $arrParam['cmfoid']);
                        $this->dao->insereServicosOS($ordoid,$ositotioid,'VERIFICAÇÃO - TESTE DE FUNCIONAMENTO.','A');
                        $this->dao->insereOrdemSituacao($ordoid, $arrParam['cd_usuario'], $descricaoHist,9);
                    } else {
                        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
                    }

                }

                $tipo_antigo_ex = true;
                $this->linhasStandBy($tipo_antigo_ex, 't', 0, $connumeroNovo, $this->usuoid);

                $this->pagamentoContratoPagamento($arrParam,$connumeroNovo,$ordoid);

                if($categoria == 3) {
                    $taxa = $this->dao->recuperarMonitoramentoLocacaoContrato($connumeroNovo);
                    $this->dao->insereDadosMensalidade($connumeroNovo,$taxa->cpagmonitoramento,$arrParam['classe_destino']);
                }

            }

            $this->migrarAnexos($arrParam['connumero'], $connumeroNovo, $arrParam['mdfoid']);

        } else {
            throw new ErrorException('Não foi possivel gerar novo contrato.');
        }

        return $connumeroNovo;
    }

    public function pagamentoContratoPagamento($arrParam,$connumeroNovo, $ordoidRetirada = null){

        $this->dao->ContratoPagamento($arrParam['cmfoid']);

        // MIGRA ACESSÓRIOS?
        if($arrParam['migra_acessorios'] == 't') {
            //Faz update na contrato_servico alterando o numero do contrato (consconoid) para o conumero_novo
            $this->dao->atualizaContratoServico($arrParam['connumero'], $connumeroNovo);

            // Migra dados da Gerenciadora (contrato_gerenciadora) do contrato original para o novo.
            $dadosGerenciadora = $this->dao->migraDadosGerenciadora($arrParam['connumero'],$connumeroNovo);

            $this->dao->atualizaModificacaoContratoNovo($connumeroNovo, $arrParam['cmfoid']);
            $this->dao->atualizaOrdemServicoModificacao($connumeroNovo, $arrParam['cmfoid']);
            $this->dao->atualizaContratoModificacao($connumeroNovo, $arrParam['cmfoid'], $ordoidRetirada);
        }

    }

    /**
     * Migracao equipamento
     *
     * @author Vinicius Senna
     */
    public function modificarContratoMigracaoEqpto($cmfoid, $possuiAcessorioAdicional) {

        // Busca informações da modificacao
        $infoModificacao = $this->dao->recuperaInformacoesModificacao($cmfoid);

        if(is_object($infoModificacao)) {

           $arrParam = $this->parametrosFuncoes($infoModificacao, $this->usuoid);

            // ALTERA CONTRATO ORIGINAL - OK
            $this->dao->alteraContratoOriginalMigracao($arrParam['connumero'], $arrParam['conveioid'], $arrParam['conequoid']);

             // Se cancela contrato original - OK
            if($arrParam['cancela_contrato_original'] == 't') {
                $this->dao->cancelaContratoOriginalMigracao($arrParam['connumero'],$arrParam['cd_usuario']);
            }

            // Gera contrato novo - OK
            $connumeroNovo = $this->dao->geraContratoNovoMigracao($arrParam['cd_usuario'],$arrParam['cmfoid']);


            if($connumeroNovo > 0) {

                // Faz update na contrato_modificacao gravando $connumero_novo em cmfconnumero_novo; - OK
                $this->dao->atualizaContratoModificacao($connumeroNovo, $arrParam['cmfoid']);

                // GRAVA NUMERO DE CONTRATO NOVO NA TABELA TERMO_NUMERO - OK
                $terntnsoid = 2;
                $ternrelroid = 1;
                $this->dao->geraTermoNumeroMigracao($connumeroNovo,$terntnsoid, $ternrelroid,$connumeroNovo);

                // Gera historico - OK
                $hitobs1 = "Migração equipamento via módulo de Modificação. " . $arrParam['motivo_migracao'];
                $hitobs1 .= " .Id da modificação " . $arrParam['mdfoid'];
                $hitprotprotocolo1 = "contrato novo:".$connumeroNovo;

                $hitobs2 = "Migração equipamento via módulo de Modificação.";
                $hitobs2 .= $arrParam['obs_modificacao'].". Id da modificação ".$arrParam['mdfoid'];
                $hitobs2 .= ". Contrato Original:".$arrParam['connumero'];

                $observacaoHistEqp = "Migração contrato " . $arrParam['connumero'] . " para " . $connumeroNovo;

                $this->dao->insereHistoricoTermoMigracao($arrParam['connumero'], $this->usuoid, $hitobs1, $hitprotprotocolo1);
                $this->dao->insereHistoricoTermoMigracao($connumeroNovo, $arrParam['cd_usuario'], $hitobs2);
                $this->dao->insereHistoricoTermoNumeroMigracao($connumeroNovo,$arrParam['cd_usuario']);

                // Insere na tabela historico_equipamento - OK
                $this->dao->insereHistoricoEquipamento($connumeroNovo, $arrParam['cd_usuario'],$observacaoHistEqp);

                // Insere na tabela telefone_contato - OK
                $this->migraTelefonesContatos($connumeroNovo,$arrParam['mdfoid'],$infoModificacao->cmfclioid_destino);

                $descricaoHist = $arrParam['descricao_tipo_mod'] .' '. $arrParam['obs_modificacao'];
                $this->dao->cancelaOrdemServico($arrParam['connumero'], $arrParam['cd_usuario'], $descricaoHist,'connumero');

                $ordoid = null;

                if (($arrParam['gera_os_teste'] == 't') && (!$possuiAcessorioAdicional)) {

                    /**
                     *  Se for do Grupo UPGRADE (cmtcmgoid == 1 || cmtcmgoid == 2):
                     *  $descricao=Descrição do Tipo de Modificação
                     *  $descricao_hist="Descrição do Tipo de Modificação"
                     *  + conteúdo do campo mdfobservacao_modificacao da tabela modificacao,
                     *  fazendo JOIN com a tabela contrato_modificacao onde cmfmdfoid=mdfoid E cmfoid=$cmfoid;
                     */
                    $descricao = (string) $arrParam['descricao_tipo_mod'];
                    $descricaoHist = $arrParam['descricao_tipo_mod'].' '.$arrParam['obs_modificacao'];

                    $mtioid = 16;
                    $ositotioid = 102;

                    $ordoid = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$arrParam['cd_usuario'], $arrParam['cmfoid'],$connumeroNovo);
                    $this->dao->atualizaOSContratoModificacao($ordoid, $arrParam['cmfoid']);
                    $this->dao->insereServicosOS($ordoid,$ositotioid,'VERIFICAÇÃO - TESTE DE FUNCIONAMENTO.','A');
                    $this->dao->insereOrdemSituacao($ordoid, $arrParam['cd_usuario'], $descricaoHist,9);

                    $this->dao->insereServicosNovos($arrParam['connumero']);

                    // SEMPRE QUE GERAR OS
                    $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);

                }

                $this->pagamentoContratoPagamento($arrParam,$connumeroNovo,$ordoid);
            }

            $this->migrarAnexos($arrParam['connumero'], $connumeroNovo, $arrParam['mdfoid']);
        }

        return $connumeroNovo;
    }

    /**
     * Modificar contrato upgrade
     *
     *
     */
    public function modificarContratoUpgrade($cmfoid, $isPagarTaxaCartao, $possuiAcessorioAdicional) {

        // Busca informações da modificacao
        $infoModificacao = $this->dao->recuperaInformacoesModificacao($cmfoid);

        if(is_object($infoModificacao)) {

            $arrParam = $this->parametrosFuncoes($infoModificacao,$this->usuoid);


            //Baixa os títulos que estão a vencer nas notas SL e F
            $titulosBaixa = $this->dao->getTitulosByContrato($arrParam['connumero']);
            $this->dao->efetuarBaixa($titulosBaixa);

          /**
             * STI 84969
             * Caso haja paralização do faturamento para o contrato
             * não pode realizar upgrade/downgrade
             *
            **/
            $this->dao->verificarParalizacaoFaturamento($arrParam['connumero']);
            // Insere na tabela telefone_contato - OK
            $this->migraTelefonesContatos($arrParam['connumero'],$arrParam['mdfoid'],$infoModificacao->cmfclioid_destino);
            // Registra historico no contrato, chamando o hitorico_termo_i, informanto o Tipo de Modificação, Descrição
            // do Motivo de Substituicao e concatenando com $motivo_upgrade
            $hitobs1 = "Upgrade via módulo de Modificação. " . $arrParam['motivo_migracao'];
            $hitobs1 .= " .Id da modificação " . $arrParam['mdfoid'];
            $hitobs1 .= " contrato:".$arrParam['connumero']." ";
            $this->dao->insereHistoricoTermoMigracao($arrParam['connumero'], $this->usuoid, $hitobs1);

            /**
             * Atualiza o Motivo de Substituição antes de verificar a compatibilidade
             */
            $this->dao->atualizaMotivoSubContrato($arrParam['connumero'], $infoModificacao->mdfmsuboid);

            /* Busca Compatibilidade */
            $resCompatibilidadeEqpto = $this->dao->verificaCompatibilidadeEqptoUp($arrParam['connumero']);
            $resCompatibilidadeAcessorios = $this->dao->verificaCompatibilidadeAcessoriosUp($arrParam['connumero']);

            $motivo  = $this->dao->recuperarMotivoSubstituicaoClasse($arrParam['cmtoid'], false);

            //motivo
            $msubtrocaveiculo = $motivo[0]->msubtrocaveiculo;

            if($msubtrocaveiculo == 't') {

                /*Se for uma TRC Insere uma O.S de Retirada de Eqpto e Acessórios */
                $mtioid = 5 ;
                $descricao = "UPGRADE com TRC";
                $ordoidRetirada = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$this->usuoid, $arrParam['cmfoid'],$arrParam['connumero']);

                // SEMPRE QUE GERAR OS
                $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);

                /* Insere Serviços de Retirada para Eqpto e Acessórios */
                $this->dao->insereServicosOS($ordoidRetirada,11,'Upgrade com TRC','P');
                $this->dao->insereRetiradaAcessorios($ordoidRetirada);

                 /* Insere Histórico na O.S */

                $this->dao->atualizaOSContratoModificacao($ordoidRetirada, $cmfoid);
                $this->dao->insereOrdemSituacao($ordoidRetirada, $this->usuoid, $descricao);

                if($arrParam['cmfveioid_novo'] > 0) {

                    $this->dao->insereOrdemServicoEspera($arrParam['connumero'],$ordoidRetirada,$infoModificacao->cmfclioid_destino,$arrParam['cmfveioid_novo']);
                    // SEMPRE QUE GERAR OS
                    $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);
                    $this->dao->atualizaClasseContratoIncompativel($arrParam['connumero'], $arrParam['classe_destino']);
                }

                /*Verifica compatibilidade total (Eqpto e Acessórios) */

                if( $resCompatibilidadeEqpto == true && $resCompatibilidadeAcessorios == true) {
                    /*Atualiza Classe de UPGRADE*/
                    $this->dao->atualizaClasseContrato($arrParam['connumero'], $arrParam['classe_destino']);

                } else {

                    $this->dao->insereServicosNovos($arrParam['connumero']);
                    $this->dao->atualizaClasseContratoIncompativel($arrParam['connumero'], $arrParam['classe_destino']);

                }

            } else {

                /*Quando não é troca de Veículo, somente UPGRADE*/

                if( $resCompatibilidadeEqpto == true && $resCompatibilidadeAcessorios == true) {
                    /*Atualiza Classe de UPGRADE*/

                    $this->dao->atualizaClasseContrato($arrParam['connumero'], $arrParam['classe_destino']);



                    if($arrParam['gera_os_teste'] == 'f'){

                        /*Atualiza Obrigação Financeira de Monitoramento*/
                        $this->dao->atualizaMonitoramento($cmfoid);

                    } else if(!$possuiAcessorioAdicional) {

                        /*Gera a O.S de Teste de Funcionamento */

                        $descricao = (string) $arrParam['mdfobservacao_modificacao'];
                        $mtioid = 1;
                        $ositotioid = 102;

                        $ordoid = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$this->usuoid, $arrParam['cmfoid'],$arrParam['connumero']);
                        $this->dao->atualizaOSContratoModificacao($ordoid, $arrParam['cmfoid']);
                        $this->dao->insereServicosOS($ordoid,$ositotioid,'VERIFICAÇÃO - TESTE DE FUNCIONAMENTO.','A');
                        $this->dao->insereOrdemSituacao($ordoid, $this->usuoid, $descricao,9);

                        // SEMPRE QUE GERAR OS
                        $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);
                    }

                    $this->dao->refidelizaContrato($arrParam['connumero'],$this->usuoid,$arrParam['cmfcvgoid'],$arrParam['classe_destino']);


                } else {

                    /*Upgrade com Incompatibilidade*/

                    $this->dao->atualizaClasseContratoIncompativel($arrParam['connumero'], $arrParam['classe_destino']);
                    $ordoidInstalacao = $this->dao->geraOSInstalacao($cmfoid, $this->usuoid,$arrParam['connumero']);
                    $this->dao->atualizaOSContratoModificacao($ordoidInstalacao, $arrParam['cmfoid']);
                    $this->dao->insereServicosNovos($arrParam['connumero']);
                    $this->dao->insereOrdemSituacao($ordoidInstalacao, $this->usuoid, $descricao,9);

                    // SEMPRE QUE GERAR OS
                    $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);

                    if($resCompatibilidadeEqpto == false) {
                        $this->dao->insereServicosOS($ordoidInstalacao,77,'TROCA DE EQUIPAMENTO - UPGRADE','A');
                    }

                    // SEMPRE QUE GERAR OS
                    $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);
                }



                $this->dao->refidelizaContrato($arrParam['connumero'],$this->usuoid,$arrParam['cmfcvgoid'],$arrParam['classe_destino']);
            }

            /*Atualiza Dados na Contrato Pagamento*/

            $this->dao->atualizaContratoPgto($arrParam['cmfoid']);

            /* Se for true, sera pago com cartao de credito (ver gerarContratos) */

            if(!$isPagarTaxaCartao) {
                // Cobra Taxa de Downgrade
                $this->dao->cobraTaxaUpgradeDowngrade($cmfoid,date('Y-m-d 00:00:00', strtotime('first day of next month')),$arrParam['mdfoid']);
            }



            if($arrParam['cmptproduto_siggo_seguro'] == 't' && $arrParam['siggo'] != 't') {

                if( $resCompatibilidadeEqpto == true && $resCompatibilidadeAcessorios == true) {

                    $this->geraCotacao($infoModificacao->cmfveioid,$arrParam['classe_destino'],$infoModificacao,true,$ordoid,true);

                } else {

                    $this->geraCotacao($infoModificacao->cmfveioid,$arrParam['classe_destino'],$infoModificacao,false,$ordoid,false);
                }

                // Gera proposta
                $prpoid = $this->dao->criaProposta($arrParam['cmftppoid_subtitpo'],$arrParam['modalidade'], $this->usuoid);
                $this->dao->atualizaContratoModificacaoProposta($prpoid, $arrParam['cmfoid']);
                $corretor = $this->dao->recuperaCorretorPadrao();
                $this->dao->atualizaCorretorProposta($prpoid,$corretor->psccorroid);
            }

            $this->migrarAnexos($arrParam['connumero'], 0, $arrParam['mdfoid']);

            // Atualizar serviços com o id da cond_pgto
            $this->dao->atualizarDadosParcelamento($arrParam['connumero'], $infoModificacao->cmdfpcpvoid);

            //Grava historico no contrato original
            $this->dao->inserirHistoricoContrato($arrParam['connumero'], "Alteração da parcela dos acessórios via módulo de modificação de contrato: de [24x] para [36x]");

            //Baixa os títulos que estão a vencer nas notas SL e F
            $titulosBaixa = $this->dao->getTitulosByContrato($arrParam['connumero']);
            $this->dao->efetuarBaixa($titulosBaixa);
        }

        return $arrParam['connumero'];

    }

    /**
     * Downgrade
     *
     *
     */
    public function modificarContratoDowngrade($cmfoid, $isPagarTaxaCartao, $possuiAcessorioAdicional) {

        // Busca informações da modificacao
        $infoModificacao = $this->dao->recuperaInformacoesModificacao($cmfoid);

        if(is_object($infoModificacao)) {

            $arrParam = $this->parametrosFuncoes($infoModificacao,$this->usuoid);

            //Baixa os títulos que estão a vencer nas notas SL e F
            $titulosBaixa = $this->dao->getTitulosByContrato($arrParam['connumero']);
            $this->dao->efetuarBaixa($titulosBaixa);

            /**
             * STI 84969
             * Caso haja paralização do faturamento para o contrato
             * não pode realizar upgrade/downgrade
             *
            **/
            $this->dao->verificarParalizacaoFaturamento($arrParam['connumero']);            // Insere na tabela telefone_contato - OK
            $this->migraTelefonesContatos($arrParam['connumero'],$arrParam['mdfoid'],$infoModificacao->cmfclioid_destino);
            // Registra historico no contrato, chamando o hitorico_termo_i, informanto o Tipo de Modificação, Descrição
            // do Motivo de Substituicao e concatenando com $motivo_upgrade
            $hitobs1 = "Downgrade via módulo de Modificação. " . $arrParam['motivo_migracao'];
            $hitobs1 .= " .Id da modificação " . $arrParam['mdfoid'];
            $hitobs1 .= " contrato:".$arrParam['connumero'];
            $this->dao->insereHistoricoTermoMigracao($arrParam['connumero'], $this->usuoid, $hitobs1);


            // Atualiza o Motivo de Substituição antes de verificar a compatibilidade
            $this->dao->atualizaMotivoSubContrato($arrParam['connumero'], $infoModificacao->mdfmsuboid);

            /* Busca Compatibilidade */
            $resCompatibilidadeEqpto = $this->dao->verificaCompatibilidadeEqptoDown($arrParam['connumero']);
            $resCompatibilidadeAcessorios = $this->dao->verificaCompatibilidadeAcessoriosDown($arrParam['connumero']);

            $motivo  = $this->dao->recuperarMotivoSubstituicaoClasse($arrParam['cmtoid'], false);

            //motivo
            $msubtrocaveiculo = $motivo[0]->msubtrocaveiculo;

            if($msubtrocaveiculo == 't') {

                /*Se for uma TRC Insere uma O.S de Retirada de Eqpto e Acessórios */
                $mtioid = 5 ;
                $descricao = "DOWNGRADE com TRC";
                $ordoidRetirada = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$this->usuoid, $arrParam['cmfoid'],$arrParam['connumero']);

                /* Insere Serviços de Retirada para Eqpto e Acessórios */
                $this->dao->insereServicosOS($ordoidRetirada,11,'DOWNGRADE com TRC','P');
                $this->dao->insereRetiradaAcessorios($ordoidRetirada);

                 /* Insere Histórico na O.S */

                $this->dao->atualizaOSContratoModificacao($ordoidRetirada, $cmfoid);
                $this->dao->insereOrdemSituacao($ordoidRetirada, $this->usuoid, $descricao);

                if($arrParam['cmfveioid_novo'] > 0) {

                    $this->dao->insereOrdemServicoEspera($arrParam['connumero'],$ordoidRetirada,$infoModificacao->cmfclioid_destino,$arrParam['cmfveioid_novo']);
                    // SEMPRE QUE GERAR OS
                    $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);
                    $this->dao->atualizaClasseContratoIncompativel($arrParam['connumero'], $arrParam['classe_destino']);
                }

                /*Verifica compatibilidade total (Eqpto e Acessórios) */

                if( $resCompatibilidadeEqpto == true && $resCompatibilidadeAcessorios == true) {
                    /*Atualiza Classe de UPGRADE*/
                    $this->dao->atualizaClasseContrato($arrParam['connumero'], $arrParam['classe_destino']);

                }

            } else {

                $mtioid=10;
                $descricao="DOWNGRADE";
                $ordoid = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$this->usuoid, $arrParam['cmfoid'],$arrParam['connumero']);

                /*Quando não é troca de Veículo, somente DOWNGRADE*/

                if( $resCompatibilidadeEqpto == true && $resCompatibilidadeAcessorios == true) {
                    /*Atualiza Classe de UPGRADE*/

                    $this->dao->atualizaClasseContrato($arrParam['connumero'], $arrParam['classe_destino']);

                    if($arrParam['gera_os_teste'] == 'f'){

                        /*Atualiza Obrigação Financeira de Monitoramento*/
                        $this->dao->atualizaMonitoramento($cmfoid);

                    } else if(!$possuiAcessorioAdicional) {

                        /*Gera a O.S de Teste de Funcionamento */

                        $descricao = (string) $arrParam['mdfobservacao_modificacao'];
                        $mtioid = 1;
                        $ositotioid = 102;

                        //$ordoid = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$this->usuoid, $arrParam['cmfoid'],$arrParam['connumero']);
                        $this->dao->atualizaOSContratoModificacao($ordoid, $arrParam['cmfoid']);
                        $this->dao->insereServicosOS($ordoid,$ositotioid,'VERIFICAÇÃO - TESTE DE FUNCIONAMENTO.','A');
                        $this->dao->insereOrdemSituacao($ordoid, $this->usuoid, $descricao,9);

                        // SEMPRE QUE GERAR OS
                        $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);

                    }

                    $this->dao->refidelizaContrato($arrParam['connumero'],$this->usuoid,$arrParam['cmfcvgoid'],$arrParam['classe_destino']);


                } else {

                    $this->dao->atualizaClasseContratoIncompativel($arrParam['connumero'], $arrParam['classe_destino']);

                    /*Downgrade com Incompatibilidade*/

                    if($resCompatibilidadeEqpto != true) {
                        $this->dao->insereServicosOS($ordoid,1128,'DOWNGRADE','A');
                        $this->dao->atualizaOSContratoModificacao($ordoid, $arrParam['cmfoid']);
                        $this->dao->insereOrdemSituacao($ordoid, $this->usuoid, $descricao,9);
                       // $this->dao->insereServicosOS($ordoid,8,'RETIRADA DE EQUIPAMENTO - DOWNGRADE','A');

                        /*$ordoidInstalacao = $this->dao->geraOSInstalacao($cmfoid, $this->usuoid,$arrParam['connumero']);
                        $this->dao->insereServicosOS($ordoidInstalacao,3,'TROCA DE EQUIPAMENTO - DOWNGRADE','A');
                        $this->dao->atualizaOSContratoModificacao($ordoidInstalacao, $arrParam['cmfoid']);
                        $this->dao->insereOrdemSituacao($ordoidInstalacao, $this->usuoid, $descricao,9);*/

                        // SEMPRE QUE GERAR OS
                        $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);

                    }

                    if($resCompatibilidadeAcessorios != true) {
                        // Gera O.S de Retirada para os Acessórios
                        $mtioid = 5 ;
                        $descricao = "DOWNGRADE - RETIRADA DE ACESSÓRIOS EXCEDENTES ";
                        //$ordoidRetirada = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$this->usuoid, $arrParam['cmfoid'],$arrParam['connumero']);
                        $this->dao->insereOrdemSituacao($ordoid, $this->usuoid, $descricao,9);
                        /* Insere Serviços de Retirada para Acessórios */
                        $this->dao->insereServicosRetiradaDowngradeOS($ordoid);
                        $this->dao->atualizaOSContratoModificacao($ordoid, $arrParam['cmfoid']);

                        /*if($resCompatibilidadeEqpto != true) {
                            $this->dao->insereServicosOS($ordoidRetirada,8,'Downgrade','P');
                        } else {
                            $descricao = "DOWNGRADE - RETIRADA";
                            $this->dao->atualizaOSContratoModificacao($ordoidRetirada, $arrParam['cmfoid']);
                            $this->dao->insereOrdemSituacao($ordoidRetirada, $this->usuoid, $descricao,9);
                        }*/
                    }

                }

                $this->dao->refidelizaContrato($arrParam['connumero'],$this->usuoid,$arrParam['cmfcvgoid'],$arrParam['classe_destino']);
            }

            /*Atualiza Dados na Contrato Pagamento*/

            $this->dao->atualizaContratoPgto($arrParam['cmfoid']);

            /* Se for true, sera pago com cartao de credito (ver gerarContratos) */
            if(!$isPagarTaxaCartao) {
                // Cobra Taxa de Downgrade
                $this->dao->cobraTaxaUpgradeDowngrade($cmfoid,date('Y-m-d 00:00:00', strtotime('first day of next month')),$arrParam['mdfoid']);
            } else {


                $prpoid = $this->dao->recuperarPropostaContrato($arrParam['connumero']);

                if(!empty($prpoid)){
                    $this->dao->atualizaContratoModificacaoProposta($prpoid, $cmfoid);
                }
            }

            $this->migrarAnexos($arrParam['connumero'], 0, $arrParam['mdfoid']);

            // Atualizar serviços com o id da cond_pgto
            $this->dao->atualizarDadosParcelamento($arrParam['connumero'], $infoModificacao->cmdfpcpvoid);

            //Grava historico no contrato original
            $this->dao->inserirHistoricoContrato($arrParam['connumero'], "Alteração da parcela dos acessórios via módulo de modificação de contrato: de [24x] para [36x]");

            
        }

        return $arrParam['connumero'];

    }

    /**
     * Renovacao
     *
     *
     *
     */
    public function modificarContratoRenovacao($cmfoid) {

        // Busca informações da modificacao
        $infoModificacao = $this->dao->recuperaInformacoesModificacao($cmfoid);

        if(is_object($infoModificacao)) {

            $ordoid = null;
            $arrParam = $this->parametrosFuncoes($infoModificacao,$this->usuoid);

           // Insere na tabela telefone_contato - OK
            $this->migraTelefonesContatos($arrParam['connumero'],$arrParam['mdfoid'],$infoModificacao->cmfclioid_destino);

            $hitobs1 = $arrParam['motivo_migracao'];
            $hitobs1 .= " Renovação Siggo seguro .Id da modificação " . $arrParam['mdfoid'];
            //$hitprotprotocolo1 = "contrato novo:".$arrParam['connumero'].$arrParam['obs_modificacao'];
            $this->dao->insereHistoricoTermoMigracao($arrParam['connumero'], $this->usuoid, $hitobs1);

            // INSERE NA TABELA historico_fidelizacao_contrato,  de acordo com o id da contrato_modificacao:

            $this->dao->refidelizaContrato($arrParam['connumero'],$this->usuoid,$arrParam['vigencia_meses'],$arrParam['classe_contrato_original']);

            //$this->dao->atualizaContratoPagamentoFinanceiro($arrParam['connumero'],$arrModPag);
            $this->dao->ContratoPagamento($arrParam['cmfoid']);
            $isVigencia = $this->dao->isVigenciaContratoVencida($arrParam['connumero']);

            if($isVigencia == FALSE) {

                $descricao = ' Renovação Siggo seguro ' . $arrParam['descricao_tipo_mod'];
                $descricaoHist = $arrParam['descricao_tipo_mod']." obs: ".$arrParam['obs_modificacao'];

                /**
                 * Se for do Grupo RENOVACAO
                 */

                $mtioid = 1;
                $ositotioid = 102;

                $ordoid = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$arrParam['cd_usuario'], $arrParam['cmfoid'],$arrParam['connumero']);
                $this->dao->atualizaOSContratoModificacao($ordoid, $arrParam['cmfoid']);
                $this->dao->insereServicosOS($ordoid,$ositotioid,'VERIFICAÇÃO - TESTE DE FUNCIONAMENTO.','A');
                $this->dao->insereOrdemSituacao($ordoid, $this->usuoid, $descricaoHist,9);

                // SEMPRE QUE GERAR OS
                $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);

                $this->geraCotacao($arrParam['cmfveioid'],$arrParam['classe_contrato_original'],$infoModificacao,true,$ordoid,true);

            } else if($isVigencia == TRUE) {

                $ordoidInstalacao = $this->dao->geraOSInstalacao($cmfoid, $this->usuoid,$arrParam['connumero']);
                $this->dao->insereServicosOS($ordoidInstalacao, 3,'INSTALAÇÃO DE EQUIPAMENTO','A');
                $this->dao->atualizaOSContratoModificacao($ordoidInstalacao, $cmfoid);
                $this->dao->insereOrdemSituacao($ordoidInstalacao, $this->usuoid, $hitobs1 . ' VISTORIA - TIRAR FOTO');

                // SEMPRE QUE GERAR OS
                $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);
            }

            $this->migrarAnexos($arrParam['connumero'], 0, $arrParam['mdfoid']);
        }

        return $arrParam['connumero'];

    }

    /**
     * Gera cotacao
     *
     *
     */
    public function geraCotacao($veioid, $eqcoid, $infoModificacao, $flagRenovacao, $numOrdem, $flagApolice = true, $veiculo_novo = false) {

        require _MODULEDIR_.'Produto_Com_Seguro/Action/ProdutoComSeguro.php';

        $ProdutoComSeguro = new ProdutoComSeguro();


        if($veiculo_novo){
            $info = $this->dao->recuperaDadosVeiculoSiggo2($veioid,$infoModificacao->cmfclioid_destino );
             $info = (object) $info;
        } else {
             $info = $this->dao->recuperaDadosVeiculoSiggo3($infoModificacao->cmfconnumero);
        }


        $ProdutoComSeguro->setCodigo_fipe($info->codigo_fipe_veiculo);
        $ProdutoComSeguro->setAno_modelo($info->ano_modelo);

        $info->id_zero_km = $info->id_zero_km == 't' ? 1 : 0;

        $ProdutoComSeguro->setCarro_zero($info->id_zero_km );

        $ProdutoComSeguro->setTipo_combustivel($info->combustivel);

        $ProdutoComSeguro->setUso_veiculo($info->utilizacao_do_veiculo);

        //$ProdutoComSeguro->setFinalidade_uso_veiculo($info->veiuso_veiculo);
        $ProdutoComSeguro->setClasseProduto($infoModificacao->cmfeqcoid_destino);

        //1= fisico   2= juridico
        if($info->tipo_cliente == 'F') {
            $ProdutoComSeguro->setTipoPessoa('1');
        } else if($info->tipo_cliente == 'J') {
            $ProdutoComSeguro->setTipoPessoa('2');
        }

        $ProdutoComSeguro->setCpf_cgc($info->cpf_cnpj);
        $ProdutoComSeguro->setCep($info->cep);
        $corretor = $this->dao->recuperaCorretorPadrao();
        $ProdutoComSeguro->setCorretor($corretor->psccodseg);

        $mensagem = '';
        $cotacao = $ProdutoComSeguro->processarCotacao();
		
		//echo "<pre>";var_dump($cotacao);echo "</pre>";exit();


        if ($cotacao['status'] == 'Erro') {

            $msg = '';

            if (is_array($cotacao['mensagem'])) {
                $msg = implode("\n", $cotacao['mensagem']);
            } else {
                $msg = $cotacao['mensagem'];
            }
            if ($msg == '' || $msg == ' ') {
                $msg = 'Erro interno seguradora';
            }
            $mensagem = $msg . "\n";

            throw new Exception($mensagem);
        }

        if(isset($cotacao['orcamento_numero'])) {

            $numeroCotacao = $cotacao['orcamento_numero'];

            $this->geraPropostaSeguro($numeroCotacao,$infoModificacao,$flagRenovacao, $numOrdem,$flagApolice,$info);
        }

    }

    /**
     * Gera proposta
     *
     *
     */
    public function geraPropostaSeguro($numeroCotacao,$infoModificacao, $flagRenovacao, $numOrdem,$flagApolice,$info) {

        $produtoComSeguro = new ProdutoComSeguro();
        $camposParticularidade = array(
                                'clipoid',
                                'clipclioid',
                                'clippessoa_politicamente_exposta1',
                                'clippessoa_politicamente_exposta2',
                                'clippspsoid',
                                'cliptipo_segurado'
                                );

        $dadosCliente = $this->dao->recuperaDadosCliente($infoModificacao->cmfclioid_destino);
        $particularidadeCliente = $this->dao->recuperarClienteParticularidade($camposParticularidade, $infoModificacao->cmfclioid_destino);
        $corretor = $this->dao->recuperaCorretorPadrao();

        $foneRes = floatval(trim(str_replace(array(')','-','('), '',substr($dadosCliente->clifone_res, 2))));
        $foneCel = floatval(trim(str_replace(array(')','-','('), '',substr($dadosCliente->clifone_cel, 2))));

        $produtoComSeguro->setCotacaoNumero($numeroCotacao);
        $produtoComSeguro->setContratoNumero($infoModificacao->cmfconnumero);
        $produtoComSeguro->setClienteNome($dadosCliente->clinome);
        $produtoComSeguro->setClienteSexo($this->dePara($dadosCliente->clisexo, 'sexo'));
        $produtoComSeguro->setClienteEstadoCivil($this->dePara($dadosCliente->cliestado_civil,'estado_civil'));
        $produtoComSeguro->setClienteProfissao($particularidadeCliente->clippspsoid);
        $produtoComSeguro->setClienteDataNascimento(date('d/m/Y',strtotime($dadosCliente->clidt_nascimento)));
        $produtoComSeguro->setClientePep1($particularidadeCliente->clippessoa_politicamente_exposta1);
        $produtoComSeguro->setClientePep2($particularidadeCliente->clippessoa_politicamente_exposta2);
        $produtoComSeguro->setClienteResidencialDdd(intval(substr($dadosCliente->clifone_res, 0,2)));
        $produtoComSeguro->setClienteResidencialFone(
            preg_replace('/\D/', '', $foneRes)
        );
        $produtoComSeguro->setClienteCelularDdd(
            intval(substr($dadosCliente->clifone_cel, 0,2))
        );
        $produtoComSeguro->setClienteCelularFone(
            preg_replace('/\D/', '', $foneCel)
        );
        $produtoComSeguro->setClienteEmail($dadosCliente->cliemail);
        $produtoComSeguro->setClienteEndereco($dadosCliente->clirua_res);
        $produtoComSeguro->setClienteEnderecoNumero($dadosCliente->clino_res);
        $produtoComSeguro->setClienteComplemento($dadosCliente->clicompl_res);
        $produtoComSeguro->setClienteCidade($dadosCliente->clicidade_res);
        $produtoComSeguro->setClienteUf($dadosCliente->cliuf_res);
        $produtoComSeguro->setVeiculoPlaca($info->placa);
        $produtoComSeguro->setVeiculoChassi($info->chassi);
        $produtoComSeguro->setVeiculoUtilizacao($info->utilizacao_do_veiculo);
        $produtoComSeguro->setClienteSeguroTipo($particularidadeCliente->cliptipo_segurado);
        $produtoComSeguro->setFormaPagamento($dadosCliente->cliformacobranca);


        $produtoComSeguro->setClasseProduto($infoModificacao->cmfeqcoid_destino);


        $produtoComSeguro->setCorretor($corretor->pscoid);

        //chamada do método para processar a proposta do seguro
        $seguro = $produtoComSeguro->processarProposta();

        if ($seguro['status'] == 'Erro') {
            if (is_array($seguro['mensagem'])) {
            $msg = implode("\n", $seguro['mensagem']);
            } else {
                $msg = $seguro['mensagem'];
            }
            if ($msg == '' || $msg == ' ') {
                $msg = 'Erro interno seguradora';
            }

            $mensagem = $msg . "\n";
            throw new ErrorException($mensagem);
        }

        $strHist = '';
        if($flagRenovacao == true) {
            $strHist = 'Renovação';
        } else {
            $strHist = 'Upgrade';
        }

        if($infoModificacao->cmtcmgoid == 7) {
            $strHist = 'Troca de veículo';
        }

        $hitobs1 = $arrParam['motivo_migracao'];
        $hitobs1 .= $strHist." Siggo seguro . Numero da cotação: " . $numeroCotacao;
        $hitobs1 .= " Numero da proposta: " . $seguro['proposta_numero'];

        $this->dao->insereHistoricoTermoMigracao($infoModificacao->cmfconnumero, $this->usuoid, $hitobs1);

        $this->geraApolice($infoModificacao,$flagRenovacao,$numOrdem,$flagApolice,$corretor,$info);
    }

    /**
     * Gera Apolice
     *
     *
     */
    public function geraApolice($infoModificacao,$flagRenovacao,$numOrdem,$flagApolice,$corretor,$info) {
       

        if($flagApolice == true) {

             $produtoComSeguro = new ProdutoComSeguro();

            $produtoComSeguro->setContratoNumero($infoModificacao->cmfconnumero);

            $produtoComSeguro->setDataInstalacaoEquipamento(date('d/m/Y'));
            $produtoComSeguro->setDataAtivacaoEquipamento(date('d/m/Y'));

            $produtoComSeguro->setClasseProduto($infoModificacao->cmfeqcoid_destino);


            $produtoComSeguro->setCodUsuarioLogado($this->usuoid);

            // Mantis 7005
            $produtoComSeguro->setFlagVigencia(false);

            $produtoComSeguro->setOrigemChamada('prn_modificacao_contrato');
            $produtoComSeguro->setOrigemSistema('Intranet');

            $seguro = $produtoComSeguro->processarApolice();

            if ($seguro['status'] == 'Erro') {
                 $mensagem  = '';
                //percorre os código de erro para buscar as mensagens
                foreach ($seguro['cod_msg'] as $key=> $cod){

                    //recupera mensagem de erro na tabela produto_seguro_mensagens
                    $msg_log = $produtoComSeguro->getMensagem($cod);

                    if(is_object($msg_log)){
                        $mensagem .=  $msg_log->msg_sascar."\r\n";
                    }
                }

                throw new ErrorException($mensagem);
            }

            $hitobs1 = $arrParam['motivo_migracao'];
            $hitobs1 .= " Apólice gerada com sucesso.";
            $ap = $this->dao->recuperaDadosVeiculoSeguro($infoModificacao->cmfconnumero);

            $hitobs1 .= ' Apólice anterior: ' . $ap->apolice_anterior . ' ';

            $this->dao->insereHistoricoTermoMigracao($infoModificacao->cmfconnumero, $this->usuoid, $hitobs1);
        }

    }

    public function dePara($de, $tipo = 'sexo') {

        switch ($tipo) {
            case 'sexo':
                $para = array('M' => 1, 'F' => 2);
                break;
            case 'estado_civil' :
                $para = array('S' => 1,'C' => 2,'A' => 3,'D' => 4,'V' => 5,'O' => 3);
                break;
            case 'tipo_pessoa':
                $para = array('F' => 1, 'J' => 2);
                break;
        }

        return $para[$de];
    }

    /**
     *
     *
     * @author Vinicius Senna
     */
    public function modificarContratoInstalacaoRnr($cmfoid) {

        $terntnsoid = 2;
        $ternrelroid = 1;

        // Busca informações da modificacao
        $modificacao = $this->dao->recuperaInformacoesModificacao($cmfoid);

        if(is_object($modificacao)) {

            //Cria novo contrato
            $connumeroNovo = $this->dao->geraContratoRnr($cmfoid,$this->usuoid);

            //Atualiza a contrato modificacao
            $this->dao->atualizaContratoModificacao($connumeroNovo, $cmfoid);

            //Altera o contrato original
            $this->dao->alteraContratoOriginalRnr($modificacao->cmfconnumero, $connumeroNovo);

            $this->dao->geraTermoNumeroMigracao($connumeroNovo,$terntnsoid, $ternrelroid, $connumeroNovo);

            //Troca o contrato antigo pelo novo na nota fiscal
            $this->dao->trocarContratoNotaFiscalItem($modificacao->cmfconnumero, $connumeroNovo);

            //Insere historico no contrato original
            $hitobs1 = "Contrato Novo originado de um RNR.  " . $modificacao->mdfobservacao_modificacao;
            $hitobs1 .= " .Id da modificação: " . $modificacao->mdfoid;
            $hitprotprotocolo1 = "contrato novo:".$connumeroNovo;
            $this->dao->insereHistoricoTermoMigracao($modificacao->cmfconnumero, $this->usuoid, $hitobs1, $hitprotprotocolo1);

            //Insere historico no contrato novo
            $hitobs2 = "Contrato Novo derivado de um RNR, criado via módulo de Modificação. ";
            $hitobs2 .= $modificacao->mdfobservacao_modificacao.". Id da modificação " . $modificacao->mdfoid . ". Contrato Original:".$modificacao->cmfconnumero;
            $this->dao->insereHistoricoTermoMigracao($connumeroNovo, $this->usuoid, $hitobs2);

            //Insere historico na tabela termo_numero_historico
            $this->dao->insereHistoricoTermoNumeroMigracao($connumeroNovo, $this->usuoid);

            //Insere na tabela telefone_contato - OK
            $contTelefones = $this->migraTelefonesContatos($connumeroNovo, $modificacao->mdfoid,$modificacao->cmfclioid_destino);

            if($contTelefones == 0) {
                $this->dao->migraTelefonesContratoOriginal($modificacao->cmfconnumero,$connumeroNovo);
            }

            $osInstalacao = $this->dao->geraOSInstalacao($cmfoid, $this->usuoid, $connumeroNovo);
            $this->dao->insereServicosOS($osInstalacao, 3,'INSTALAÇÃO DE EQUIPAMENTO','A');
            $this->dao->atualizaOSContratoModificacao($osInstalacao, $cmfoid);

            // SEMPRE QUE GERAR OS
            $this->dao->atualizaOrdemServicoModificacao($connumeroNovo, $cmfoid);

            $descricaoHist = $modificacao->cmtdescricao . " / " . $modificacao->mdfobservacao_modificacao;
            $this->dao->insereOrdemSituacao($osInstalacao, $this->usuoid, $descricaoHist);

            $this->dao->insereKitBasico($connumeroNovo, $this->usuoid);

            $arrParam = $this->parametrosFuncoes($modificacao, $this->usuoid);

            $this->pagamentoContratoPagamento($arrParam,$connumeroNovo,$osInstalacao);

            $this->migrarAnexos($arrParam['connumero'], $connumeroNovo, $arrParam['mdfoid']);

        }

        return $connumeroNovo;
    }

    /**
     * Troca de veiculo
     *
     *
     */
    public function modificarContratoTrocaVeiculo($cmfoid, $isPagarTaxaCartao) {


        // Busca informações da modificacao
        $infoModificacao = $this->dao->recuperaInformacoesModificacao($cmfoid);

        if(is_object($infoModificacao)) {

            $arrParam = $this->parametrosFuncoes($infoModificacao,$this->usuoid);

            // Insere na tabela telefone_contato - OK
            $this->migraTelefonesContatos($arrParam['connumero'],$arrParam['mdfoid'],$infoModificacao->cmfclioid_destino);

            $descricao = $arrParam['descricao_tipo_mod'];
            $descricaoHist = $arrParam['descricao_tipo_mod'] . ' '.$arrParam['obs_modificacao'];
            $mtioid = 6;

            // Cria ordem de servico de retirada - OK TODO: return ordoid;
            $ordoidRetirada = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$arrParam['cd_usuario'], $arrParam['cmfoid'],$arrParam['connumero']);

            // INSERE O SERVIÇO DE RETIRADA DO EQUIPAMENTO PRINCIPAL - OK
            $this->dao->insereServicosOS($ordoidRetirada,11,'Retirada por motivo de troca de veículo, via módulo de Modificação de Contrato.','P');

            // INSERE RETIRADA PARA OS ACESSÓRIOS - OK
            $this->dao->insereRetiradaAcessorios($ordoidRetirada);

            $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);

            $this->dao->atualizaContratoModificacao($arrParam['connumero'], $arrParam['cmfoid'], $ordoidRetirada);

            // INSERINDO O HISTÓRICO NA O.S - OK
            //$this->dao->atualizaOSContratoModificacao($ordoidRetirada, $arrParam['cmfoid']);
            $this->dao->insereOrdemSituacao($ordoidRetirada, $arrParam['cd_usuario'], $descricaoHist);

            $this->dao->insereOrdemServicoEspera($arrParam['connumero'],$ordoidRetirada,$infoModificacao->cmfclioid_destino,$arrParam['cmfveioid_novo']);

            $this->dao->atualizaModificacaoContratoNovo($arrParam['connumero'], $arrParam['cmfoid']);

            /* Se for true, sera pago com cartao de credito (ver gerarContratos) */
            if(!$isPagarTaxaCartao) {
                // Cobra Taxa de Downgrade
                $this->dao->cobraTaxaUpgradeDowngrade($cmfoid,date('Y-m-d 00:00:00', strtotime('first day of next month')),$arrParam['mdfoid']);
            }

            if($arrParam['cmptproduto_siggo_seguro'] == 't' && $arrParam['siggo'] != 't') {

                $this->geraCotacao($arrParam['cmfveioid_novo'], $arrParam['classe_destino'], $infoModificacao, false, $ordoidRetirada, false, true);
                // Gera proposta
                $prpoid = $this->dao->criaProposta($arrParam['cmftppoid_subtitpo'],$arrParam['modalidade'], $this->usuoid);
                $this->dao->atualizaContratoModificacaoProposta($prpoid, $cmfoid);
                $corretor = $this->dao->recuperaCorretorPadrao();
                $this->dao->atualizaCorretorProposta($prpoid,$corretor->psccorroid);
            }

            $this->migrarAnexos($arrParam['connumero'], 0, $arrParam['mdfoid']);

        }

        return $arrParam['connumero'];

    }

    /**
     *
     *
     * @author Vinicius Senna
     */
    public function modificarContratoEfetivacaoDemo($cmfoid, $possuiAcessorioAdicional) {

        // Busca informações da modificacao
        $infoModificacao = $this->dao->recuperaInformacoesModificacao($cmfoid);


        if(is_object($infoModificacao)) {

            $arrParam = $this->parametrosFuncoes($infoModificacao,$this->usuoid);

            // Insere na tabela telefone_contato - OK
            $this->migraTelefonesContatos($arrParam['connumero'],$arrParam['mdfoid'],$infoModificacao->cmfclioid_destino);
            $this->dao->atualizaVigenciaContrato($arrParam['connumero']);
            $hitobs1 = "Efetivação DEMO. " . $arrParam['motivo_migracao'];
            $hitobs1 .= " .Id da modificação " . $arrParam['mdfoid'];
            $hitprotprotocolo1 = "contrato: ".$arrParam['connumero'].' '.$arrParam['obs_modificacao'];
            $this->dao->insereHistoricoTermoMigracao($arrParam['connumero'], $this->usuoid, $hitobs1, $hitprotprotocolo1);

            $this->dao->ContratoPagamento($arrParam['cmfoid']);

            if (($arrParam['gera_os_teste'] == 't') && (!$possuiAcessorioAdicional)) {
                    $mtioid = 0;
                    $ositotioid = 0;

                    $descricao = (string) $arrParam['descricao_tipo_mod'];
                    $descricaoHist = $arrParam['descricao_tipo_mod'].' '.$arrParam['obs_modificacao'];

                    /**
                     *   Se for do Grupo MIGRACAO (cmtcmgoid == 3):
                     *   $descricao=Descrição do Tipo de Modificação
                     *   $descricao_hist="Descrição do Tipo de Modificação"
                     *   + conteúdo do campo mdfobservacao_modificacao da tabela modificacao,
                     *   fazendo JOIN com a tabela contrato_modificacao onde cmfmdfoid=mdfoid E cmfoid=$cmfoid;
                     */
                    if($arrParam['cmtcmgoid'] == 8) {
                        $mtioid = 1;
                        $ositotioid = 102;
                    }

                    if($mtioid > 0 && $ositotioid > 0) {
                        $ordoid = $this->dao->criaOrdemServicoRetirada($mtioid,$descricao,$arrParam['cd_usuario'], $arrParam['cmfoid'],$arrParam['connumero']);
                        $this->dao->atualizaOSContratoModificacao($ordoid, $arrParam['cmfoid']);
                        $this->dao->insereServicosOS($ordoid,$ositotioid,'VERIFICAÇÃO - TESTE DE FUNCIONAMENTO.','A');
                        $this->dao->insereOrdemSituacao($ordoid, $this->usuoid, $descricaoHist,9);
                        $this->dao->atualizaOrdemServicoModificacao($arrParam['connumero'], $cmfoid);
                    } else {
                        throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
                    }
            }

            $this->dao->atualizaTipoContrato($arrParam['tipo_contrato_migracao'],$arrParam['connumero']);

            $this->migrarAnexos($arrParam['connumero'], 0, $arrParam['mdfoid']);
        }

        return $arrParam['connumero'];

    }


    /**
     * Duplicacao de contrato
     *
     * @author Vinicius Senna
     */
    public function modificarContratoPlaca2($cmfoid) {

        // Busca informações da modificacao
        $infoModificacao = $this->dao->recuperaInformacoesModificacao($cmfoid);

        if(is_object($infoModificacao)) {

           $arrParam = $this->parametrosFuncoes($infoModificacao,$this->usuoid);

           $veiplaca = $this->dao->recuperaVeiculoDuplicacaoContrato($arrParam['cmfveioid']);


           if($veiplaca) {

                if(stripos($veiplaca,'-' )) {

                    $pedaco = explode('-', $veiplaca);

                    $digito  = intval($pedaco[1]) + 1;
                    
                    $placaNova = $pedaco[0] . '-' . $digito;
                    $placaAntiga = $veiplaca;

                } else {
                    $placaNova = $veiplaca . '-2';
                    $placaAntiga = $veiplaca . '-1';
                }
                

                $veioidNovo = $this->dao->geraVeiculoNovo($placaNova, $arrParam['cmfveioid']);

                $this->dao->atualizaPlacaVeiculo($arrParam['cmfveioid'], $placaAntiga);

                $connumeroNovo = $this->dao->insereContratoVeiculoNovo($veioidNovo,$this->usuoid,$cmfoid);

                $this->dao->atualizaContratoModificacao($connumeroNovo, $cmfoid);

                $terntnsoid = 2;
                $ternrelroid = 1;
                $this->dao->geraTermoNumeroMigracao($connumeroNovo,$terntnsoid, $ternrelroid,$connumeroNovo);

                $hitobs1 = "Duplicação - Placa 2 via módulo de Modificação ";
                $hitobs1 .= " .Id da modificação " . $arrParam['mdfoid'];
                $hitprotprotocolo1 = "contrato novo:".$connumeroNovo.' '.$arrParam['obs_modificacao'];
                $this->dao->insereHistoricoTermoMigracao($arrParam['connumero'], $this->usuoid, $hitobs1, $hitprotprotocolo1);

                $hitobs2 = "Duplicação - Placa 2 via módulo de Modificação ";
                $hitobs2 .= $arrParam['motivo_migracao'].". Id da modificação " . $arrParam['mdfoid'] . ". Contrato Original:".$arrParam['connumero']. " " . $arrParam['obs_modificacao'].".";
                $this->dao->insereHistoricoTermoMigracao($connumeroNovo, $arrParam['cd_usuario'], $hitobs2);


                $this->dao->insereHistoricoTermoNumeroMigracao($connumeroNovo,$this->usuoid);

                // Insere na tabela telefone_contato - OK
                $contTelefones = $this->migraTelefonesContatos($connumeroNovo,$arrParam['mdfoid'],$infoModificacao->cmfclioid_destino);

                if($contTelefones == 0) {
                    $this->dao->migraTelefonesContratoOriginal($arrParam['connumero'],$connumeroNovo);
                }

                $ordoid = $this->dao->geraOSInstalacao($cmfoid, $this->usuoid,$connumeroNovo);

                if(empty($ordoid)) {
                    throw new Exception(self::MENSAGEM_ERRO_PROCESSAMENTO);
                }

                $this->dao->atualizaOSContratoModificacao($ordoid, $arrParam['cmfoid']);

                $this->dao->insereServicosOS($ordoid,3,'INSTALAÇÃO DE EQUIPAMENTO','A');

                $descricao = $arrParam['descricao_tipo_mod'];
                $descricaoHist = $arrParam['descricao_tipo_mod'].' '.$arrParam['obs_modificacao'];

                $this->dao->insereOrdemSituacao($ordoid, $this->usuoid, $descricaoHist);

                $this->dao->insereKitBasico($connumeroNovo,$this->usuoid);

                $this->pagamentoContratoPagamento($arrParam,$connumeroNovo,$ordoid);

                $this->dao->atualizaOrdemServicoModificacao($connumeroNovo, $cmfoid);

                $this->migrarAnexos($arrParam['connumero'], $connumeroNovo, $arrParam['mdfoid']);

           } else {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
           }
        }

        return $connumeroNovo;

    }

    /**
     * Desfaz as acoes de uma modificacao
     *
     * @author Andre L. Zilz
     *
     */
    public function desfazerModificacao() {

        $dados = new stdClass();
        $contratosDesfeitos = 0;
        $ultimoContrato  = '';

        $this->view->parametros = $this->tratarParametros();
        $this->inicializarParametros();
        $dados = $this->view->parametros;

        try {
                $this->dao->begin();

                $modificacao = $this->dao->recuperarDadosModificacao($dados->mdfoid, array('mdfcmtoid'));

                $tipoModificacao = $this->dao->recuperarTipoModificacao($modificacao->mdfcmtoid);
                $grupo = $tipoModificacao[0]->cmtcmgoid;

                $obsComplementar = "Contrato referente a modificação Nr. " . $dados->mdfoid . ", foi desfeita. " ;

                if($grupo == 2) {

                    //Contratos selecionados em tela
                    foreach ($dados->contratos_marcados as $connumero) {

                        //Dados adicionais dos contratos
                        $contrato = $this->dao->recuperarModificacaoContrato($connumero, $dados->mdfoid);
                        $contrato = $contrato[0];

                         //Tratamento para caso haja mais de um contrato original
                        if($ultimoContrato != $connumero) {
                            //Atualiza os servicos do contrato, caso existam
                            $this->dao->atualizaContratoServico($connumero, $contrato->contrato_novo);
                        }

                        //No contrato novo: Retira veiculo,equipamento e cancela
                        $this->dao->cancelaEqpVeiculoContrato($contrato->contrato_novo);

                        //Tratamento para caso haja mais de um contrato original
                        if($ultimoContrato != $connumero) {
                            //No contrato original: Volta Veiculo,equipamento e acessorios
                            $this->dao->reverteVeiculo($connumero);

                            //Grava historico no contrato original
                            $this->dao->inserirHistoricoContrato($connumero, $obsComplementar . $dados->observacao_desfazer );
                        }

                        // Verificar se tem O.S resultante do processo, Cancelando a O.S
                        $this->dao->cancelaOrdemServico($contrato->cmfoid, $this->usuoid, $dados->observacao_desfazer, 'cmfoid' );

                        //Atualiza a contrato modificacao com os dados do desfazer
                        $this->dao->atualizarDesfazerModificacao($contrato->cmfoid, $dados->observacao_desfazer);

                        //Grava historico nos contratos novos
                        $this->dao->inserirHistoricoContrato($contrato->contrato_novo, $obsComplementar . $dados->observacao_desfazer);

                        $contratosDesfeitos++;

                        $ultimoContrato = $connumero;

                        $this->excluirAnexos($contrato->contrato_novo, $mdfoid);

                        $this->dao->inativarContratoServico($contrato->cmfoid);

                    }

                    //Recuperar dados da contrato_modificacao
                    $ContratoModificacao = $this->dao->recuperarContratoModificacao($dados->mdfoid, true, array("COUNT(cmfoid) OVER() AS total_contratos"));

                    //Se todos os contratos da modificacao foram desfeitos, cancela a modificacao
                    if($ContratoModificacao[0]->total_contratos == $contratosDesfeitos) {
                        $this->dao->atualizarStatusModificacao('X', $dados->mdfoid);
                    }

                } else {

                     //Contratos selecionados em tela
                    foreach ($dados->contratos_marcados as $connumero) {

                        //Dados adicionais dos contratos
                        $contrato = $this->dao->recuperarModificacaoContrato($connumero, $dados->mdfoid);
                        $contrato = $contrato[0];

                        if(empty($contrato->contrato_novo) && ($ultimoContrato != $connumero)) {
                            //Desfaz a modificacao no contrato original
                            $this->dao->desfazContratoOriginal($contrato, $connumero);

                            //Atualiza a contrato modificacao com os dados do desfazer
                            $this->dao->atualizarDesfazerModificacao($contrato->cmfoid, $dados->observacao_desfazer);

                            //Grava historico no contrato original
                            $this->dao->inserirHistoricoContrato($connumero, $obsComplementar . $dados->observacao_desfazer );


                        } else {

                            //Tratamento para caso haja mais de um contrato original
                            if($ultimoContrato != $connumero) {
                                //Atualiza os servicos do contrato, caso existam
                                $this->dao->atualizaContratoServico($connumero, $contrato->contrato_novo);

                                //Grava historico no contrato original
                                $this->dao->inserirHistoricoContrato($connumero, $obsComplementar . $dados->observacao_desfazer );
                            }

                            //No contrato novo: Retira veiculo,equipamento e cancela
                            $this->dao->cancelaEqpVeiculoContrato($contrato->contrato_novo);

                            //Desfazer os contatos
                            $this->dao->migraContatos($contrato->contrato_novo, $connumero);

                            //Desfazer os telefones
                            $this->dao->desfazerContratoTelefone($contrato->contrato_novo, $connumero);

                            //Grava historico no contrato original
                            $this->dao->inserirHistoricoContrato($contrato->contrato_novo, $obsComplementar . $dados->observacao_desfazer );

                        }

                        // Verificar se tem O.S resultante do processo, Cancelando a O.S
                        $this->dao->cancelaOrdemServico($contrato->cmfoid, $this->usuoid, $dados->observacao_desfazer, 'cmfoid');

                        $contratosDesfeitos++;
                        $ultimoContrato = $connumero;

                        $this->excluirAnexos($contrato->contrato_novo, $dados->mdfoid);

                        $this->dao->inativarContratoServico($contrato->cmfoid);
                    }

                    //Recuperar dados da contrato_modificacao
                    $ContratoModificacao = $this->dao->recuperarContratoModificacao($dados->mdfoid, true, array("COUNT(cmfoid) OVER() AS total_contratos"));

                    //Se todos os contratos da modificacao foram desfeitos, cancela a modificacao
                    if($ContratoModificacao[0]->total_contratos == $contratosDesfeitos) {
                        $this->dao->atualizarStatusModificacao('X', $dados->mdfoid);
                    }
                }

                $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_ATUALIZAR;

                $this->dao->commit();

        } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }


        $this->detalhar($dados);

    }

    /**
    * Cancela uma modificacao
    *
    * @author Andre L. Zilz
    */
    public function cancelarModificacao() {

        $this->view->parametros = $this->tratarParametros();
        $this->view->parametros->sub_tela = 'aba_dados_principais';

        $dados = $this->view->parametros;

        if($dados->is_nao_autorizar == 't') {
             $dados->observacao_cancelar = "Status técnico Não Autorizado via módulo de modificação de Nº " . $dados->mdfoid;
             $dados->observacao_cancelar .= ". Modificação Cancelada mediante reprovação técnica.";

        } else {
            $dados->observacao_cancelar = "Cancelamento da modificação: " . $dados->mdfoid . ". " .$this->tratarTextoInput($dados->observacao_cancelar,false);
        }

        try {

            if( ($dados->mdfstatus == 'P') || ($dados->mdfstatus == 'A') ) {

                $this->dao->begin();

                //Status cancelado na tabela modificacao
               $this->dao->atualizarStatusModificacao('X', $dados->mdfoid);

                $contratos = $this->dao->recuperarContratoModificacao($dados->mdfoid,false, array("cmfconnumero"));

                //Gravar Historico no(s) contrato(s) origem
                foreach ($contratos as $contrato) {
                    $this->dao->inserirHistoricoContrato($contrato->cmfconnumero, $dados->observacao_cancelar);
                }

                $this->dao->inserirHistoricoModificacao($dados->mdfoid,  $dados->observacao_cancelar);

                $this->dao->commit();

            } else {
                throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
            }

            $this->view->mensagemSucesso = self::MENSAGEM_SUCESSO_CANCELAR;

       } catch (ErrorException $e) {
            $this->dao->rollback();
            $this->view->mensagemErro = $e->getMessage();

        } catch (Exception $e) {
            $this->dao->rollback();
            $this->view->mensagemAlerta = $e->getMessage();
        }

        $this->detalhar($dados);
    }


    /**
     * Insere novos dados de contato
     *
     * @author Vinicius Senna
     */
    public function migraTelefonesContatos($connumeroNovo,$mdfoid,$clioid) {

        $contatos = $this->dao->pesquisarContatos($mdfoid);

        // Insere na tabela telefone_contato - OK
        foreach($contatos as $contato) {

            $arrContato = array(
                            'tctconnumero'      => $connumeroNovo,
                            'tctno_ddd_res'     => substr($contato->cmctfone_res, 0,3),
                            'tctno_fone_res'    => substr($contato->cmctfone_res, 3),
                            'tctno_ddd_com'     => substr($contato->cmctfone_com, 0,3),
                            'tctno_fone_com'    => substr($contato->cmctfone_com, 3),
                            'tctno_ddd_cel'     => substr($contato->cmctfone_cel, 0,3),
                            'tctno_fone_cel'    => substr($contato->cmctfone_cel, 3),
                            'tctrg'             => $contato->cmctrg,
                            'tctcpf'            => $contato->cmctcpf,
                            'tctorigem'         => '',
                            'cmctnome'          => $contato->cmctnome,
                            'clioid'            => intval($clioid),
                            'clicobs'           => $contato->cmctobservacao,
                            'nextel'            => $contato->cmctfone_nextel
                        );

            if($contato->cmctautorizada == 't'){

                $arrContato['tctorigem'] = 'A';
                $this->dao->gravaContato($arrContato);
                $this->dao->gravaClienteContato($arrContato);
            }

            if($contato->cmctinstalacao == 't') {

                $arrContato['tctorigem'] = 'I';

                $this->dao->gravaContato($arrContato);
            }

            if($contato->cmctemergencia == 't') {

                $arrContato['tctorigem'] = 'E';

                $this->dao->gravaContato($arrContato);

            }

        }

        return count($contatos);
    }

    /**
     *
     * @author Vinicius Senna
     *
     */
    public function parametrosFuncoes($infoModificacao,$usuario){

        // Id Contrato modificacao
        $cmfoid = $infoModificacao->cmfoid;
        // Tipo de Modificacao. Tabela contrato_modificacao_tipo
        $cmtoid = $infoModificacao->mdfcmtoid;
        // Observacao da modificacao
        $motivoMigracao = $infoModificacao->mdfobservacao_modificacao;
        $obsModificacao = $infoModificacao->mdfobservacao_modificacao;
        // Descricao do tipo de modificacao
        $cmtdescricao = $infoModificacao->cmtdescricao;
        // Observação sobre a Análise de Credito Realizada.
        $observacaoAnalise = $infoModificacao->mdfobservacao_analise_credito;
        // Numero do contrato original
        $connumero = $infoModificacao->cmfconnumero;
        // Classe do contrato original
        $classeContratoOriginal = $infoModificacao->cmfeqcoid_origem;
        // Classe destino, mas só para casos de Upgrade/Downgrade.
        $classeDestino = $infoModificacao->cmfeqcoid_destino;
        // Tipo de contrato original
        $tipoContratoOriginal = $infoModificacao->cmftpcoid_origem;
        // Tipo de contrato para o qual sera Migrado
        $tipoContratoMigracao = $infoModificacao->cmftpcoid_destino;
        // Cliente do contrato original
        $clienteContratoOriginal = $infoModificacao->cmfclioid_origem;
        // Cliente do novo contrato, neste caso sera o mesmo do original,
        // somente na Migracao de Equipamento teremos um clioid diferente.
        $clienteNovoContrato = $infoModificacao->cmfclioid_destino;
        // Na migração não ha.
        //$refidelizacao = $infoModificacao->cmfrefidelizacao;
        // Vigencia do contrato em meses
        $vigenciaMeses = $infoModificacao->cmfcvgoid;
        // Zona Comercial
        $zonaComercial = $infoModificacao->cmfrczoid;
        // Id do executivo
        $idExecutivo = $infoModificacao->cmffunoid_executivo;
        // Modalidade
        $modalidade = $infoModificacao->cmfmodalidade;
        // Id do veiculo no contrato
        $cmfveioid = $infoModificacao->cmfveioid;
        // Id do veiculo novo, apenas para casos de TRC
        $cmfveioidNovo = $infoModificacao->cmfveioid_novo;
        // Id do equipamento instalado.
        $cmfequoid = $infoModificacao->cmfequoid;
        // Motivo da modificacao
        $mdfoid = $infoModificacao->mdfoid;
        // Siggo seguro
        // Siggo

        // Tipo modificacao
        $cmftppoid = $infoModificacao->cmftppoid;
        // Subtipo Modificacao
        $cmftppoid_subtitpo = $infoModificacao->cmftppoid_subtitpo;
        // conveioid
        //$conveioid = $infoModificacao->
        ///var_dump($infoModificacao);exit;
        // LER PARAMETRIZAÇÃO PARA O TIPO e GRUPO
        $resParametrizacaoTipo = $this->dao->recuperaParametrizacaoTipo($cmtoid);
        $resParametrizacaoGrupo = $this->dao->recuperaParametrizacaoGrupo($cmtoid);

        $geraOSRetirada = null;
        $geraOSTeste = null;
        $migraAcessorios = null;
        $recebeDadosFinanceiro = null;
        $cancelaContratoOriginal = null;
        $refidelizacao = null;
        $cmtcmgoid = null;
        $cmgdescricao = null;
        $siggoSeguro = null;
        $siggo = null;

        if(is_object($resParametrizacaoTipo)) {
            $geraOSRetirada = $resParametrizacaoTipo->cmptgera_os_retirada;
            $geraOSTeste = $resParametrizacaoTipo->cmptgera_os_teste_funcionamento;
            $migraAcessorios = $resParametrizacaoTipo->cmptmigra_acessorios;
            $recebeDadosFinanceiro = $resParametrizacaoTipo->cmptrecebe_dados_financeiro;
            $cancelaContratoOriginal = $resParametrizacaoTipo->cmptcancela_contrato_original;
            $refidelizacao = $resParametrizacaoTipo->cmptrefidelizacao;
            $siggoSeguro = $resParametrizacaoTipo->cmptproduto_siggo_seguro;
            $siggo =  $resParametrizacaoTipo->cmptproduto_siggo;
        }

        if(is_object($resParametrizacaoGrupo)) {

            $cmtcmgoid = $resParametrizacaoGrupo->cmtcmgoid;
            $cmgdescricao = $resParametrizacaoGrupo->cmgdescricao;

        }

        // Identifica categoria da alteracao
        $categoriaAlteracao = $this->dao->identificaCategoriaAlteracao($cmfoid);
        $categoria = $categoriaAlteracao->categoria;

        return array (
            'cmfoid'                    => $cmfoid,
            'cmtoid'                    => $cmtoid,
            'mdfoid'                    => $mdfoid,
            'connumero'                 => $connumero,
            'conveioid'                 => $cmfveioid,
            'conequoid'                 => $conequoid,
            'cd_usuario'                => $usuario,
            'motivo_migracao'           => $motivoMigracao,
            'obs_modificacao'           => $obsModificacao,
            'obs_analise'               => $observacaoAnalise,
            'gera_os_retirada'          => $geraOSRetirada,
            'gera_os_teste'             => $geraOSTeste,
            'migra_acessorios'          => $migraAcessorios,
            'recebe_dados_financeiro'   => $recebeDadosFinanceiro,
            'cancela_contrato_original' => $cancelaContratoOriginal,
            'classe_contrato_original'  => $classeContratoOriginal,
            'classe_destino'            => $classeDestino,
            'tipo_contrato_original'    => $tipoContratoOriginal,
            'tipo_contrato_migracao'    => $tipoContratoMigracao,
            'cliente_contrato_original' => $clienteContratoOriginal,
            'cliente_novo_contrato'     => $clienteNovoContrato,
            'cmfcvgoid'                 => $vigenciaMeses,
            'vigencia_meses'            => $vigenciaMeses,
            'zona_comercial'            => $zonaComercial,
            'id_executivo'              => $idExecutivo,
            'modalidade'                => $modalidade,
            'cmfveioid'                 => $cmfveioid,
            'cmfveioid_novo'            => $cmfveioidNovo,
            'cmfequoid'                 => $cmfequoid,
            'refidelizacao'             => $refidelizacao,
            'descricao_tipo_mod'        => $cmtdescricao,
            'cmtcmgoid'                 => $cmtcmgoid,
            'cmgdescricao'              => $cmgdescricao,
            'categoria'                 => $categoria,
            'cmptproduto_siggo_seguro'  => $siggoSeguro,
            'siggo'                     => $siggo,
            'cmftppoid'                 => $cmftppoid,
            'cmftppoid_subtitpo'        => $cmftppoid_subtitpo
        );
    }

    /**
     * Caso 2  - Migração de Ex para Seguradora/Associação (Reativacao)
     * Verificar o status da linha, se estiver em stAND by deve-se:
     *  - voltá-la para habilitada
     *  - inserir historico na linha
     *  - enviar e-mail para o laboratorio
     * Se estiver cancelada deve-se:
     *  - gerar uma O.S de assistência troca de equipamento
     *
     * @author Vinicius Senna
     */
    public function linhasStandBy($tipo_antigo_ex, $ttpcseguradora, $conno_tipo_migrar, $novoTermo, $usuario) {

        if($tipo_antigo_ex && ($ttpcseguradora == 't' || $conno_tipo_migrar == 0)) {

            $tlinoid      = '';
            $tlincsloid   = '';
            $tlinaraoid   = '';
            $tlinnumero   = '';
            $tequno_fone  = '';
            $tequno_serie = '';
            $tclsoid      = '';

            $statusLinha = $this->dao->recuperaStatusLinha($novoTermo);

            if(is_array($statusLinha)) {
                $tlinoid      = $statusLinha['linoid'];
                $tlincsloid   = $statusLinha['lincsloid'];
                $tlinaraoid   = $statusLinha['linaraoid'];
                $tlinnumero   = $statusLinha['linnumero'];
                $tequno_fone  = $statusLinha['equno_fone'];
                $tequno_serie = $statusLinha['equno_serie'];
                $tclsoid      = $statusLinha['clsoid'];
            }

            if(intval($tlincsloid) == 26) {

                $observacao = 'Linha alterada para habilitada, Contrato Ex migrado para Cliente ou Seguradora.';

                if($this->dao->habilitaLinha($tlinoid, $observacao,$tlinaraoid,$tlinnumero) == true) {
                    $mail = new PHPMailer();
                    $mail->isSMTP();

                    $mail->ClearAllRecipients();
                    $mail->FROM = 'sascar@sascar.com.br';
                    $mail->FromName = 'Intranet Sascar';

                    if($_SESSION['servidor_teste'] == 1){
                        $destino = 'teste_desenv@sascar.com.br';
                    }else{
                        $destino = 'laboratorio@sascar.com.br';
                    }

                    $mail->AddAddress($destino);

                    $corpo = '
                    <html>
                    <head>
                    <title>SASCAR</title>
                    </head>
                    <body>
                    <div align="center">
                        <table class="tableMoldura">
                            <tr class = "tableTitulo">
                                <td><h1>'.$observacao." - Contrato: ".intval($novoTermo).'.</h1></td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <table width="100%">
                                        <tr class="tdc" align="left">
                                            <td colspan="2">Linha habilitada</td>
                                        </tr>
                                        <tr class="tableTituloColunas">
                                            <td><h3>Contrato</h3></td>
                                            <td><h3>Telefone</h3></td>
                                        </tr>
                                        <tr class="tdc">
                                            <td>'. intval($novoTermo) .'</td>
                                            <td>'. intval($tlinnumero) .'</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>';
                }

                $mail->MsgHTML($corpo);
                $mail->Send();


            } elseif(
                $tlincsloid == 3 || // Cancelada
                $tlincsloid == 5 || // Aguardando cancelamento
                $tlincsloid == 12 || // Disponível para troca de chip
                $tlincsloid == 21 || // Aguardando troca de chip
                $tequno_fone == '' || 
                $tclsoid != '') {

                $ordoid = $this->dao->recuperaOrdemServicoPorContrato($novoTermo);

                if($ordoid > 0) {

                   $this->dao->adicionaServicoTrocaEqpto($ordoid, $usuario, $eqcoid_serv);

                } else {

                    $ordoid = $this->dao->geraOSTrocaEquipamento($usuario, $connumero);

                    if($ordoid){

                        $this->dao->adicionaServicoTrocaEqpto($ordoid, $usuario, $eqcoid_serv);

                    }

                }
            }
        }
    }
}