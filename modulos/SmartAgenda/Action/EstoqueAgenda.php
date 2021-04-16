<?php
/**
 * Classe com as as regras de fluxos para a avalia?o de disponibilidade de estoque de determinado(s) produto(s),
 * a reserva de produto(s) e a solicitação de produto(s) para o centro de distribui?o.
 *
 */

require_once _MODULEDIR_ . 'SmartAgenda/Action/Action.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/OrdemServico.php';
require_once _MODULEDIR_ . 'SmartAgenda/DAO/EstoqueAgendaDAO.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/ReservaProduto.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/SolicitacaoProduto.php';


class EstoqueAgenda extends Action{

    private $dao;
    private $numeroOrdemServico;
    private $codigoPrestador;
    private $ordemServicoClass;
    private $IDagendamento;
    private $itensEncontrados = array();
    private $observacao;
    private $tempoModalTransporte;
    private $tempoPreparacaoRemessa;
    private $tempoRecebimentoRemessa;
    public $reservarSolicitar = array();
    private $dataDisponibilidade = array();
    private $data_hoje;
    private $msgHistorico;
    private $reservaProdutoClass;
    private $solicitacaoProdutoClass;
    private $usuarioLogado;
    private $isAlertaClientePremium;
    private $estoqueDisponivelNoCD = array();
    public $materiaisParaAntecipar = array();
    private $itensEssenciaisReservadoNoPrestadorParaValidarNoCD = array();
    private $itensEssenciaisEmTransitoReservadoNoPrestadorParaValidarNoCD = array();
    private $itensParaValidarEmTransito = array();

    public $itemsDisponivelDuplicados = array();
    public $itemsUtilizadosDuplicados = array();
    public $itemsReservadoDuplicados = array();
    public $itemsUtilizadosDuplicadosDisponivelNoPrestador = array();
    public $itemsUtilizadosDuplicadosTransitoNoPrestador = array();
    public $itemsUtilizadosDuplicadosTransitoReservadoNoPrestador = array();

    public $dataLimiteEstoqueTempoPreparacao = '';
    private $dataAgendamento;

    const FALTA_CRITICA_CD      = 'Falta de estoque no Centro de Distribui?o.';
    const ESTOQUE_INDISPONIVEL  = 'Estoque não está mais disponíel para a data selecionada';
    const CD_NOTIFICADO         = 'Centro de Distribuição notificado com sucesso.';
    const SOLICITACAO_PENDENTE  = 'O status da solicitação deve ser Pendente ';
    const RESERVA_OK            = 'Reserva realizada com sucesso.';
    const RESERVA_CANCELADA     = 'Reserva cancelada com sucesso.';
    const SOLICITACAO_OK        = 'Solicitação realizada com sucesso';
    const SOLICITACAO_CANCELADA = 'Solicitação cancelada com sucesso.';
    const PRODUTO_INSTALADO     = 'Produto(s) da reserva instalado(s) com sucesso.';
    const ESTOQUE_RESERVADO     = 1;
    const ESTOQUE_EM_TRANSITO   = 2;
    const ESTOQUE_EM_TRANSITO_RESERVADO = 3;

    public function __construct($conn) {

        $this->dao                              = new EstoqueAgendaDAO($conn);
        $this->ordemServicoClass                = new OrdemServico($conn);
        $this->reservaProdutoClass              = new ReservaProduto($conn);
        $this->solicitacaoProdutoClass          = new SolicitacaoProduto($conn);
        $this->data_hoje                        = date('d-m-Y');
        $this->usuarioLogado                    = $this->getUsuarioLogado();
        $this->isAlertaClientePremium           = FALSE;
        $this->logHabilitado                    = FALSE;
        $this->dataLimiteEstoqueTempoPreparacao = $this->data_hoje;

    }

    /**
     * Consulta a disponiblidade dos produtos essencias no estoque do representante, em transito e no CD
     */
    public function getEstoqueDisponivel(){

        $retorno = $this->retornarMensagem('', NULL, NULL);

        try {

            $this->dataDisponibilidade = array();

            if(empty($this->numeroOrdemServico)){
                throw new Exception('O n?mero da ordem de servi? dever ser informado.');
            }
            if(empty($this->codigoPrestador)){
                throw new Exception('O ID do representante dever ser informado.');
            }

            $this->setParametros();
            $this->getItensEssenciais();


            if( $this->isAlertaClientePremium  ) {

                $retorno = $this->retornarMensagem('alerta_cliente_premium', 'Item Essencial n? configurado para cliente Premium');

            } else if( empty($this->itensEncontrados) ){

                $retorno = $this->retornarMensagem('sucesso', NULL, $this->data_hoje);

            } else {

                $this->analisaItensEstoque();

                if(count($this->reservarSolicitar['falta_critica_CD']) > 0) {
                    $retorno = $this->retornarMensagem('falta_critica', self::FALTA_CRITICA_CD);
                } else {
                    $data_prevista = $this->ordenarData($this->dataDisponibilidade);
                    $retorno       = $this->retornarMensagem('sucesso', NULL, $data_prevista[0]);

                }
            }

        } catch (Exception $e) {
            $msg     = $e->getMessage();
            $retorno = $this->retornarMensagem('erro',  $msg, NULL);
        }

        return $retorno ;

    }

    private function setParametros(){

        $this->tempoModalTransporte = $this->dao->getTempoModal( $this->codigoPrestador) ;

        //tempo de prepara?o
        $tempoPreparacaoRemessa = $this->dao->getParametros('SMART_AGENDA','tempoPreparacaoRemessa');
        $this->tempoPreparacaoRemessa = $tempoPreparacaoRemessa[0]['valor'];

        //tempo de recebimento
        $tempoRecebimentoRemessa = $this->dao->getParametros('SMART_AGENDA','tempoRecebimentoRemessa');
        $this->tempoRecebimentoRemessa = $tempoRecebimentoRemessa[0][valor];

        //atribui o id do representante do CD
        $param_repoid = $this->dao->getParametros('SMART_AGENDA','REPOID_SOLICITACAO_FALSA') ;
        $this->codigoCentroDistribuicao = $param_repoid[0]['valor'];

    }

    private function filtrarItemEssencial ($itensEssenciais, $indice) {

        $filtros = array('iesmcaoid' , 'iesmlooid' , 'ieseqcoid' , 'ieseproid' , 'ieseveoid');
        $contadorColunasNulas = array('iesmcaoid' => 0 , 'iesmlooid' => 0 , 'ieseqcoid' => 0 , 'ieseproid' => 0 , 'ieseveoid' => 0);

        $totalEssenciais = count($itensEssenciais);

        if( $totalEssenciais > 1 ) {

            foreach ($itensEssenciais as $key => $registro) {

                if( is_null($registro[ $filtros[ $indice ] ]) ){
                    $contadorColunasNulas[  $filtros[ $indice ]  ] ++;
                }

            }

            foreach ($itensEssenciais as $key => $registro) {

                if( $contadorColunasNulas[  $filtros[ $indice ] ] < $totalEssenciais ) {
                    if( is_null( $registro[ $filtros[ $indice ] ] ) ) {
                        unset( $itensEssenciais[$key] );
                    }
                }
            }

            $indice++;

            //Se ja aplicou todos os filtros encerra a recursividade
            if( $indice > 4 ){
                return $itensEssenciais;
            }

            $itensEssenciais = $this->filtrarItemEssencial( $itensEssenciais, $indice );

        }

        return $itensEssenciais;
    }

    private function getItensEssenciais(){
        // Pega os items da OS (Ex: Antena satelital)
        $dadosOS          = $this->getDadosOS();
        $itemOS           = array();
        $itensEssenciais  = array();
        $isClientePremium = FALSE;

        if( (count($dadosOS) == 0) ) {
            throw new Exception('Dados da OS -> '. $this->ordoid .'  n? encontrados.');
        }

        //separa Dados da OS por Item
        foreach ($dadosOS as $key => $dadosItens){
            $itemOS[$dadosItens['chave_motivo']][] = $dadosOS[$key];
        }

        $isClientePremium = $this->dao->isClientePremium( $dadosOS[0]['conclioid'] );

        foreach ($itemOS as $itemOsId => $dadosItens) {

            $i      = 0;

            while ( $i < count($dadosItens) ) {

                $dadosFiltro['iesostoid']   = intval($dadosItens[$i]['chave_tipo']);
                $dadosFiltro['iesotioid']   = intval($dadosItens[$i]['chave_motivo']);
                $dadosFiltro['iesmcaoid']   = intval($dadosItens[$i]['chave_marca']);
                $dadosFiltro['iesmlooid']   = intval($dadosItens[$i]['chave_modelo']);
                $dadosFiltro['ieseqcoid']   = intval($dadosItens[$i]['chave_classe_equipamento']);
                $dadosFiltro['ieseproid']   = intval($dadosItens[$i]['chave_equipamento']);
                $dadosFiltro['ieseveoid']   = intval($dadosItens[$i]['chave_versao']);

                $listaItemEssencial = $this->dao->getItemEssencial($dadosFiltro);

                /**
                 * ASM-4493
                 * junho/2019
                 * Corre?o quantidade de produtos reservados, quando item ?inclu?o em multiplicidade na OS
                 */
                foreach ($listaItemEssencial as $key => $itemEssencial) {
                    $listaItemEssencial[$key]['quantidade'] = intval($dadosItens[$i]['quantidade']);
                }

                if( count($listaItemEssencial) > 0) {
                    $retornoItemEssencial = $this->filtrarItemEssencial( $listaItemEssencial, 0);
                    $itensEssenciais[$itemOsId] = $retornoItemEssencial;
                    break;
                }

                $i++;
            }

        }

        if( (count($itensEssenciais) > 0) ){

            foreach ($itensEssenciais as $chave => $itemID) {

                foreach ($itemID as $id => $iesoid) {
                    $dadosMateriais = $this->dao->getMateriaisItemEssencial( $iesoid['iesoid'], $isClientePremium );

                    /**
                     * ASM-4493
                     * junho/2019
                     * Corre?o quantidade de produtos reservados, quando item ?inclu?o em multiplicidade na OS
                     */
                    if($iesoid['quantidade'] > 1) {
                        foreach ($dadosMateriais as $key => $material) {
                            $dadosMateriais[$key]['iespquantidade'] = $material['iespquantidade'] * $iesoid['quantidade'];
                        }
                    }

                    if( empty($dadosMateriais) && $isClientePremium ) {
                        $this->isAlertaClientePremium = TRUE;
                        break;
                    } else if( empty($dadosMateriais)) {
                        $itensEssenciais[$chave][ $iesoid['iesoid'] ]['quantidade'] = 0;
                        $itensEssenciais[$chave][ $iesoid['iesoid'] ]['materiais'] = array();
                    } else {
                        $itensEssenciais[$chave][ $iesoid['iesoid'] ]['quantidade'] = $dadosMateriais[0]['iespquantidade'];
                        $itensEssenciais[$chave][ $iesoid['iesoid'] ]['materiais'] = $dadosMateriais;
                    }

                    unset($itensEssenciais[$chave][$id]);

                }
            }
        }

        $this->itensEncontrados = $itensEssenciais;

    }

    private function getDadosOS(){

        $dadosContrato = $this->dao->getDadosContrato($this->numeroOrdemServico);

        $isPossuiEquipamento = ($dadosContrato[0]['conequoid'] != 0) ? TRUE : FALSE;

       $idClasseMigracao = $this->ordemServicoClass->retornaClasseMigracaoOS($this->numeroOrdemServico);

       return $this->dao->getDadosOS($this->numeroOrdemServico, $isPossuiEquipamento, $idClasseMigracao);
    }

    /*
     * Verifica materiais no estoque reservado do prestador
     */
    private $itemOsIdJaUtilizadoReservado = array();
    private function buscaEstoqueReservadoDoPrestador(&$itensEssenciais, $itemOS){
        $produtosAtendidos = array();
        $podeAntecipar = $this->dao->getFlagAntecipacaoReservaMateriais();
        $this->itensEssenciaisReservadoNoPrestadorParaValidarNoCD = array();
        $this->itensParaValidarEmTransito = array();

        // Percorre os produtos [codicao E]
        foreach ( $itensEssenciais as $itemOsId => $dadosItens ) {

            $saldoProduto = 0;

            if( !isset($dadosItens['materiais']) || empty($dadosItens['materiais']) ){
                return $produtosAtendidos;
            }
            
            //Percorre os produtos equivalentes [codicao OU], para ver se atende a necessidade
            foreach ($dadosItens['materiais'] as $id => $materiais) {

                $estoqueReservado = $this->dao->getEstoqueReservadoDoPrestador(
                    $this->codigoPrestador, 
                    $materiais['iespprdoid'], 
                    false, 
                    $this->adicionarDiasData($this->data_hoje, false),
                    $this->dataAgendamento
                );

                // Soma o qtde de estoque de produtos que atendem a demanda
                $estoqueDisponivelPrestador = 0;
                if (!empty($estoqueReservado)) {
                    foreach($estoqueReservado as $reservado){
                        $estoqueDisponivelPrestador += $reservado['qtde_reserva_estoque'];
                    }
                }

                // Verifica se tem estoque
                if ($estoqueDisponivelPrestador > 0) {
                    // Qtde de produto solicitado para a demanda e a quantidade localizada no estoque
                    $produtosAtendidos[$itemOS][$itemOsId]['quantidade_necessaria'] = $dadosItens['quantidade'];
                    $produtosAtendidos[$itemOS][$itemOsId]['quantidade_localizada'] = $estoqueDisponivelPrestador;

                    // Coloca os items das OS que tem o produto solicitado
                    $quantidadeNecessaria = $dadosItens['quantidade'];
                    foreach($estoqueReservado as $produtos){
                        if(
                            !array_key_exists($produtos['raioid'], $this->itemOsIdJaUtilizadoReservado) ||
                            $this->itemOsIdJaUtilizadoReservado[$produtos['raioid']] > 0
                        ) {
                        // if ($this->validaTempoDeSubstituicao($this->data_hoje, $produtos['osadata'], false) === true) {
                            $quantidade = $quantidadeNecessaria;
                            if($reservado['qtde_reserva_estoque'] < $quantidadeNecessaria){
                                $quantidade = $reservado['qtde_reserva_estoque'];
                                $this->itemOsIdJaUtilizadoReservado[$produtos['raioid']] = 0;
                            } else {

                                $quantidadeProduto = $produtos['qtde_reserva_estoque'] - $dadosItens['quantidade'];
                                $this->itemOsIdJaUtilizadoReservado[$produtos['raioid']] = $quantidadeProduto;
                            }

                            $item = array(
                                'ordoid'            => $produtos['ordoid'],
                                'dataAgendamento'   => $produtos['osadata'],
                                'raioid'            => $produtos['raioid'],
                                'rairagoid'         => $produtos['rairagoid'],
                                'iesoid'            => $itemOsId,
                                'prdoid'            => $produtos['raiprdoid'],
                                'prdproduto'        => $produtos['prdproduto'],
                                'quantidade'        => $quantidade,
                                'quantidadeTotal'   => $dadosItens['quantidade'],
                                'tipo'              => 'estoque_reservado_representante'
                            );

                            $this->materiaisParaAntecipar['estoque_reservado_representante'][$produtos['ordoid']][] = $item;

                            $quantidadeNecessaria = $quantidadeNecessaria - $quantidade;

                            $this->itensEssenciaisReservadoNoPrestadorParaValidarNoCD[$itemOsId]['quantidade'] = $dadosItens['quantidade'];
                            $this->itensEssenciaisReservadoNoPrestadorParaValidarNoCD[$itemOsId]['materiais'] = $dadosItens['materiais'];

                            $this->dataDisponibilidade[$materiais['iespprdoid']] = $this->data_hoje;
                            $this->dataLimiteEstoqueTempoPreparacao = $this->adicionarDiasData($this->data_hoje, false);

                            // Caso encontre um material sai do loop
                            if ($quantidadeNecessaria === 0) {
                                break;
                            }
                        }
                        // } else {
                        //     //$podeAntecipar = false;
                        //     //os itens que não forem encontrados no estoque reservado, serão conferidos em transito
                        //     $this->itensParaValidarEmTransito[$itemOsId] = $dadosItens;
                        // }

                    }
                }

            }//LOOP 2
        }//LOOP 1

        if(!$podeAntecipar){
            $this->materiaisParaAntecipar['estoque_reservado_representante'] = array();
        }

        return $produtosAtendidos;
    }

    /*
     * Verifica materiais no estoque em tr?sito do prestador
     */
    private $itemOsIdJaUtilizadoReservadoTransito = array();
    private function buscaEstoqueTransitoReservadoPrestador(&$itensEssenciais, $itemOS){
        $produtosAtendidos = array();
        $podeAntecipar = $this->dao->getFlagAntecipacaoReservaMateriais();
        // $this->materiaisParaAntecipar['estoque_transito_reservado_representante'] = array();
        $this->itensEssenciaisEmTransitoReservadoNoPrestadorParaValidarNoCD = array();

        //LOOP 1: Percorre os produtos [codicao E]
        foreach ( $itensEssenciais as $itemOsId => $dadosItens ) {

            $listaProdutos = array();
            $saldoProduto      = 0;

            if( !isset($dadosItens['materiais']) || empty($dadosItens['materiais']) ){
                return $produtosAtendidos;
            }

            //Percorre os produtos [codicao OU]
            foreach ($dadosItens['materiais'] as $id => $materiais) {
                $listaProdutos[] = intval($materiais['iespprdoid']);
            }

            $saldoProduto = ($saldoProduto === 0) ? $dadosItens['quantidade'] : $saldoProduto;

            //LOOP 2: Percorre os produtos equivalentes [codicao OU]
            foreach ($dadosItens['materiais'] as $id => $materiais) {

                // Pega as os que atendem a demanda do produto
                $estoqueEmTransito = $this->dao->getEstoqueReservadoDoPrestador(
                    $this->codigoPrestador, 
                    $materiais['iespprdoid'], 
                    true,
                    $this->adicionarDiasData($this->data_hoje, false),
                    $this->dataAgendamento
                );

                // Soma o qtde de estoque de produtos que atendem a demanda
                $estoqueDisponivelReservadoTransito = 0;
                if (!empty($estoqueEmTransito)) {
                    foreach($estoqueEmTransito as $reservado){
                        $estoqueDisponivelReservadoTransito += $reservado['qtde_reserva_transito'];
                    }
                }

                // Verifica se tem estoque
                if ($estoqueDisponivelReservadoTransito > 0) {
                    // Qtde de produto solicitado para a demanda e a quantidade localizada no estoque
                    $produtosAtendidos[$itemOS][$itemOsId]['quantidade_necessaria'] = $dadosItens['quantidade'];
                    $produtosAtendidos[$itemOS][$itemOsId]['quantidade_localizada'] = $estoqueDisponivelReservadoTransito;

                    // Coloca os items das OS que tem o produto solicitado
                    $quantidadeNecessaria = $dadosItens['quantidade'];
                    foreach($estoqueEmTransito as $produtos){
                        // if ($this->validaTempoDeSubstituicao($this->data_hoje, $produtos['osadata'], false) === true) {
                        if(
                            !array_key_exists($produtos['raioid'], $this->itemOsIdJaUtilizadoReservadoTransito) ||
                            $this->itemOsIdJaUtilizadoReservadoTransito[$produtos['raioid']] > 0
                        ) {

                            $quantidade = $quantidadeNecessaria;
                            if($reservado['qtde_reserva_transito'] < $quantidadeNecessaria){
                                $quantidade = $reservado['qtde_reserva_transito'];
                                $this->itemOsIdJaUtilizadoReservadoTransito[$produtos['raioid']] = 0;
                            } else {
                                $quantidadeProduto = $produtos['qtde_reserva_transito'] - $dadosItens['quantidade'];
                                $this->itemOsIdJaUtilizadoReservadoTransito[$produtos['raioid']] = $quantidadeProduto;
                            }

                            $item = array(
                                'ordoid'            => $produtos['ordoid'],
                                'ragosaoid'         => $produtos['ragosaoid'],
                                'dataAgendamento'   => $produtos['osadata'],
                                'raioid'            => $produtos['raioid'],
                                'rairagoid'         => $produtos['rairagoid'],
                                'iesoid'            => $itemOsId,
                                'prdoid'            => $produtos['raiprdoid'],
                                'prdproduto'        => $produtos['prdproduto'],
                                'quantidade'        => $quantidade,
                                'quantidadeTotal'   => $dadosItens['quantidade'],
                                'tipo'              => 'transito_reservado'
                            );
                            
                            $this->materiaisParaAntecipar['estoque_transito_reservado_representante'][$produtos['ordoid']][] = $item;

                            $quantidadeNecessaria = $quantidadeNecessaria - $quantidade;

                            $this->itensEssenciaisEmTransitoReservadoNoPrestadorParaValidarNoCD[$itemOsId]['quantidade'] = $dadosItens['quantidade'];
                            $this->itensEssenciaisEmTransitoReservadoNoPrestadorParaValidarNoCD[$itemOsId]['materiais'] = $dadosItens['materiais'];

                            $this->dataDisponibilidade[$materiais['iespprdoid']] = $this->adicionarDiasData($produtos['esrdata'], true);

                            // Caso encontre um material sai do loop
                            if ($quantidadeNecessaria === 0) {
                                break;
                            }
                        }
                        // } else {
                        //     //$podeAntecipar = false;
                        //     //os itens que não forem encontrados no estoque reservado, serão conferidos em transito
                        //     $this->itensParaValidarEmTransito[$itemOsId] = $dadosItens;
                        // }

                    }
                }
            }
        }//LOOP 1

        if(!$podeAntecipar){
            $this->materiaisParaAntecipar['estoque_reservado_representante'] = array();
            $this->materiaisParaAntecipar['estoque_transito_reservado_representante'] = array();
        }
        return $produtosAtendidos;

    }

    private function analisaItensEstoque() {
        $this->reservarSolicitar = array();
        $this->materiaisParaAntecipar = array();
        $this->itemOsIdJaUtilizadoReservadoTransito = array();
        $this->itemOsIdJaUtilizadoReservado = array();
        $podeAntecipar = $this->dao->getFlagAntecipacaoReservaMateriais();

        //Percorre cada item da orem de servico
        foreach ( $this->itensEncontrados as $itemOS => $itensEssenciais ){

            /*
                ESTOQUE PRESTADOR
             */

            $produtoDisponivelPrestador = $this->verificaDisponibilidadeEstoquePrestador($itensEssenciais, $itemOS);

            /*
                ESTOQUE TRANSITO
            */
            if( empty($produtoDisponivelPrestador) ) {
                
                    if($podeAntecipar){
                        // Verifica se tem estoque reservado que atende a demanda
                        $produtoReservadoPrestador = $this->verificaEstoqueReservadoPrestador($itensEssenciais, $itemOS);
                        if (!empty($produtoReservadoPrestador)) {
                            $this->buscaEstoqueReservadoDoPrestador($itensEssenciais, $itemOS);
                        }
                        
                        // Verifica se foi atendida a quantidade de produtos
                        $itensEssenciais = $this->verificaSaldoItensEssenciais($itensEssenciais, $produtoReservadoPrestador[$itemOS]);
                    }
                    
                    
                    // Caso tenha tudo em estoque reservado para o processo
                    if( empty($itensEssenciais) ) {
                        continue;
                    } else {
                        $produtoDisponivelTransito = $this->verificaDisponibilidadeEstoqueTransito($itensEssenciais, $itemOS);
                    }

            } else {
                $itensEssenciais = $this->verificaSaldoItensEssenciais($itensEssenciais, $produtoDisponivelPrestador[$itemOS]);

                //Se conseguiu tudo no estoque do PRESTADOR, o fluxo de verificacao de estoque para aqui
                if( empty($itensEssenciais) ) {
                    continue;
                } else {
                    if($podeAntecipar){
                        // Verifica se tem estoque reservado que atende a demanda
                        $produtoReservadoPrestador = $this->verificaEstoqueReservadoPrestador($itensEssenciais, $itemOS);
                        if (!empty($produtoReservadoPrestador)) {
                            $this->buscaEstoqueReservadoDoPrestador($itensEssenciais, $itemOS);
                        }
                        $itensEssenciais = $this->verificaSaldoItensEssenciais($itensEssenciais, $produtoReservadoPrestador[$itemOS]);
                    }

                    // Caso tenha tudo em estoque reservado para o processo
                    if( empty($itensEssenciais) ) {
                        continue;
                    } else {
                        $produtoDisponivelTransito = $this->verificaDisponibilidadeEstoqueTransito($itensEssenciais, $itemOS);
                    }
                }
            }

            /*
                ESTOQUE CD [CENTRO DISTRIBUICAO]
            */
            if( !empty($produtoDisponivelTransito) ) {
                $itensEssenciais = $this->verificaSaldoItensEssenciais($itensEssenciais, $produtoDisponivelTransito[$itemOS]);
            } else {
                if($podeAntecipar){
                    // Pega produtos em transito que estão reservados para uma os
                    $produtoDisponivelEmTransitoReservado = $this->verificaEstoqueTransitoReservadoDoPrestador($itensEssenciais, $itemOS);
                    if (!empty($produtoDisponivelEmTransitoReservado)) {
                        $this->buscaEstoqueTransitoReservadoPrestador($itensEssenciais, $itemOS);
                    }
                    
                    $itensEssenciais = $this->verificaSaldoItensEssenciais($itensEssenciais, $produtoDisponivelEmTransitoReservado[$itemOS]);
                }
                // Caso tenha tudo em estoque reservado em transito para o processo
                if ( empty($itensEssenciais) ) {
                    continue;
                }
            }

            //Se conseguiu tudo no estoque em TRANSITO, o fluxo de verificacao de estoque para aqui
            if( empty($itensEssenciais) ) {
                continue;
            } else {
                $produtoDisponivelCD = $this->verificaDisponibilidadeEstoqueCD($itensEssenciais, $itemOS);
            }

            /*
                ESTOQUE FALTA CRITICA
            */
            if( empty($produtoDisponivelCD)) {
                $this->verificaFaltaCriticaEstoque($itensEssenciais, $itemOS);
            } else {
                $itensEssenciais = $this->verificaSaldoItensEssenciais($itensEssenciais, $produtoDisponivelCD[$itemOS]);

                //Se conseguiu tudo no estoque do CD, o fluxo de verificacao de estoque para aqui
                if( empty($itensEssenciais) ) {
                    continue;
                } else {
                   $this->verificaFaltaCriticaEstoque($itensEssenciais, $itemOS);
                }
            }
        }
    }

    private function retiraMateriaisDoPrestador($materiaisParaAntecipar, $tipo)
    {

        if (count($materiaisParaAntecipar) > 0){
            $retorno = array();

            foreach ($materiaisParaAntecipar as $ordemServico => $materiais) {
                foreach($materiais as $dados){

                    if ($tipo == self::ESTOQUE_RESERVADO) {
                        $retorno = $this->dao->retiraMateriaisDoPrestador($this->codigoPrestador, $dados, self::ESTOQUE_RESERVADO);
                    } else if (self::ESTOQUE_EM_TRANSITO_RESERVADO) {
                        $retorno = $this->dao->retiraMateriaisDoPrestador($this->codigoPrestador, $dados, self::ESTOQUE_EM_TRANSITO_RESERVADO);
                    }

                    if(!empty($retorno)) {
                        foreach ($retorno as $resultado) {
                            $this->reservarSolicitar['solicitar_CD_RESERVADO'][] = array(
                                // 'otioid' => $resultado['ositotioid'],
                                'ordoid' => $resultado['ordoid'],
                                'prdoid' => $resultado['raiprdoid'],
                                'prdproduto' => $dados['prdproduto'],
                                'quantidade' => $dados['quantidade'],
                                'quantidadeTotal' => $dados['quantidadeTotal'],
                                'tipo' => 'estoque_cd'
                            );
                        }
                    }
                }
            }
        }
    }

    private function verificaDisponibilidadeEstoquePrestador(&$itensEssenciais, $itemOS){

        $produtosAtendidos = array();

        //LOOP 1: Percorre os produtos [codicao E]
        foreach ( $itensEssenciais as $itemOsId => $dadosItens ) {

            $saldoProduto = 0;

            if( !isset($dadosItens['materiais']) || empty($dadosItens['materiais']) ){
                return $produtosAtendidos;
            }

            //LOOP 2: Percorre os produtos equivalentes [codicao OU]
            foreach ($dadosItens['materiais'] as $id => $materiais) {

                $estoquePrestador = $this->dao->getEstoque($this->codigoPrestador, $materiais['iespprdoid']);
                $estoqueReservado = $this->dao->getReservaEstoque(
                    $this->codigoPrestador, 
                    $materiais['iespprdoid']
                );

                //subtrai a quantidade disponivel no representante sobre a quantidade reservada
                if( !empty($estoqueReservado) && !empty($estoquePrestador) ){
                    $estoqueDisponivelPrestador = ( $estoquePrestador['quantidade_estoque'] - $estoqueReservado[0]['qtde_reserva_estoque'] );
                } else if( !empty($estoquePrestador) ) {
                    $estoqueDisponivelPrestador = intval($estoquePrestador['quantidade_estoque']);
                } else {
                    $estoqueDisponivelPrestador = 0;
                }

                $saldoProduto = ($saldoProduto === 0) ? $dadosItens['quantidade'] : $saldoProduto;

                if($estoqueDisponivelPrestador > 0){

                    if($saldoProduto <= $estoqueDisponivelPrestador){
                        $preReserva = $saldoProduto;
                    } else {
                        $preReserva = $estoqueDisponivelPrestador;
                    }

                    $item = array(
                        'otioid'          => $itemOS,
                        'iesoid'          => $itemOsId,
                        'prdoid'          => $materiais['iespprdoid'],
                        'prdproduto'      => $materiais['prdproduto'],
                        'quantidade'      => $preReserva,
                        'quantidadeTotal' => $dadosItens['quantidade'],
                        'tipo'            => 'representante'
                    );

                    $this->reservarSolicitar['estoque_representante'][] = $item;

                    $saldoProduto = ($saldoProduto - $preReserva);

                    $produtosAtendidos[$itemOS][ $itemOsId ]['quantidade_localizada'] += $preReserva;
                    $produtosAtendidos[$itemOS][ $itemOsId ]['quantidade_necessaria'] = intval($dadosItens['quantidade']);

                    //se tiver o produto no representante, seta com a data atual
                    $this->dataDisponibilidade[$materiais['iespprdoid']] = $this->data_hoje;

                    if( $saldoProduto === 0 ){
                        break;
                    }
                }
            }//LOOP 2
        }//LOOP 1

        return $produtosAtendidos;
    }

    /**
     * Função que verifica o estoque que está reservado
     */
    private function verificaEstoqueReservadoPrestador(&$itensEssenciais, $itemOS){
        $produtosAtendidos = array();

        //LOOP 1: Percorre os produtos [codicao E]
        foreach ( $itensEssenciais as $itemOsId => $dadosItens ) {
            // Quantidade de produtos a serem solicitados
            $qtdeProdutosSolicitados = ($qtdeProdutosSolicitados === 0) ? $dadosItens['quantidade'] : $qtdeProdutosSolicitados;

            // Iniciando 
            $saldoProduto = 0;

            if( !isset($dadosItens['materiais']) || empty($dadosItens['materiais']) ){
                return $produtosAtendidos;
            }

            //LOOP 2: Percorre os produtos equivalentes [codicao OU]
            foreach ($dadosItens['materiais'] as $id => $materiais) {

                // Pega as os que atendem a demanda do produto
                $estoqueReservado = $this->dao->getEstoqueReservadoDoPrestador(
                    $this->codigoPrestador, 
                    $materiais['iespprdoid'], 
                    false, 
                    $this->adicionarDiasData($this->data_hoje, false),
                    $this->dataAgendamento
                );

                // Soma o qtde de estoque de produtos que atendem a demanda
                $estoqueDisponivelPrestador = 0;
                if (!empty($estoqueReservado)) {
                    foreach($estoqueReservado as $reservado){
                        $estoqueDisponivelPrestador += $reservado['qtde_reserva_estoque'];
                    }
                }

                // Verifica se tem estoque
                if ($estoqueDisponivelPrestador > 0) {
                    // Qtde de produto solicitado para a demanda e a quantidade localizada no estoque
                    $produtosAtendidos[$itemOS][$itemOsId]['quantidade_necessaria'] = $dadosItens['quantidade'];
                    $produtosAtendidos[$itemOS][$itemOsId]['quantidade_localizada'] = $estoqueDisponivelPrestador;

                    // Coloca os items das OS que tem o produto solicitado
                    $quantidadeNecessaria = $dadosItens['quantidade'];
                    foreach($estoqueReservado as $reservado){

                        $quantidade = $quantidadeNecessaria;
                        if($reservado['qtde_reserva_estoque'] < $quantidadeNecessaria){
                            $quantidade = $reservado['qtde_reserva_estoque'];
                        }

                        $item = array(
                            'ordoid'            => $reservado['ordoid'],
                            'osadata'           => $reservado['osadata'],
                            'rairagoid'         => $reservado['rairagoid'],
                            'ragosaoid'         => $reservado['ragosaoid'],
                            'raioid'            => $reservado['raioid'],
                            'otioid'            => $itemOS,
                            'iesoid'            => $itemOsId,
                            'prdoid'            => $materiais['iespprdoid'],
                            'prdproduto'        => $materiais['prdproduto'],
                            'quantidade'        => $quantidade,
                            'quantidadeTotal'   => $dadosItens['quantidade'],
                            'tipo'              => 'representante'
                        );
    
                        $this->reservarSolicitar['estoque_reservado_representante'][] = $item;
                        
                        $quantidadeNecessaria = $quantidadeNecessaria - $quantidade;

                        // Caso encontre um material sai do loop
                        if ($quantidadeNecessaria === 0) {
                            break;
                        }

                    }
                }
            }//LOOP 2
        }//LOOP 1

        return $produtosAtendidos;
    }

    private function verificaEstoqueTransitoReservadoDoPrestador($itensEssenciais, $itemOS){

        $produtosAtendidos = array();

        //LOOP 1: Percorre os produtos [codicao E]
        foreach ( $itensEssenciais as $itemOsId => $dadosItens ) {
            $listaProdutos = array();
            $saldoProduto      = 0;

            if( !isset($dadosItens['materiais']) || empty($dadosItens['materiais']) ){
                return $produtosAtendidos;
            }

            //Percorre os produtos [codicao OU]
            foreach ($dadosItens['materiais'] as $id => $materiais) {
                $listaProdutos[] = intval($materiais['iespprdoid']);
            }

            $produtoEmTransito = $this->dao->getEstoqueDisponivelTransito($this->codigoPrestador, $listaProdutos);
            $saldoProduto = ($saldoProduto === 0) ? $dadosItens['quantidade'] : $saldoProduto;

            if( !empty($produtoEmTransito) ) {

                //LOOP 2: Percorre os produtos equivalentes [codicao OU]
                foreach ($produtoEmTransito as $chave => $valor){

                    $estoqueReservado = $this->dao->getReservaEstoque(
                        $this->codigoPrestador, 
                        $valor['prdoid'], 
                        $valor['esroid'], 
                        $this->dataAgendamento,
                        $this->adicionarDiasData($this->data_hoje, false)
                    );

                    $disponivelTransito = 0;

                    //esse método deve validar apenas a quantidade reservada, sem considerar os itens disponiveis, pois isso é feito no método "verificaDisponibilidadeEstoqueTransito"

                    $disponivelTransito = ( $estoqueReservado[0]['qtde_reserva_transito'] );
                    $disponivelTransito = ($disponivelTransito < 0) ? 0 : $disponivelTransito;

                    if($saldoProduto <= $disponivelTransito){
                        $preReserva = $saldoProduto;
                    } else {
                        $preReserva = $disponivelTransito;
                    }

                    if ($preReserva === 0) {
                        continue;
                    }

                    $item = array(
                        'otioid'          => $itemOS,
                        'iesoid'          => $itemOsId,
                        'prdoid'          => $valor['prdoid'],
                        'prdproduto'      => $valor['prdproduto'],
                        'quantidade'      => $preReserva,
                        'quantidadeTotal' => $dadosItens['quantidade'],
                        'esroid'          => $valor['esroid'],
                        'tipo'            => 'transito'
                    );

                    $this->reservarSolicitar['estoque_em_transito_reservado'][] = $item;

                    $saldoProduto = ($saldoProduto - $preReserva);

                    $produtosAtendidos[$itemOS][$itemOsId]['quantidade_localizada'] += $preReserva;
                    $produtosAtendidos[$itemOS][$itemOsId]['quantidade_necessaria'] = intval($dadosItens['quantidade']);

                    $this->dataDisponibilidade[$valor['prdoid']] = $this->adicionarDiasData($valor['esrdata'], true);

                    if ($saldoProduto === 0) {
                        break;
                    }
                }
            }
        }//LOOP 1

        return $produtosAtendidos;
    }

    private function verificaDisponibilidadeEstoqueTransito($itensEssenciais, $itemOS){

        $produtosAtendidos = array();

        //LOOP 1: Percorre os produtos [codicao E]
        foreach ( $itensEssenciais as $itemOsId => $dadosItens ) {

            $listaProdutos = array();
            $saldoProduto      = 0;

            if( !isset($dadosItens['materiais']) || empty($dadosItens['materiais']) ){
                return $produtosAtendidos;
            }

            //Percorre os produtos [codicao OU]
            foreach ($dadosItens['materiais'] as $id => $materiais) {
                $listaProdutos[] = intval($materiais['iespprdoid']);
            }

            $produtoEmTransito = $this->dao->getEstoqueDisponivelTransito($this->codigoPrestador, $listaProdutos);
            $saldoProduto = ($saldoProduto === 0) ? $dadosItens['quantidade'] : $saldoProduto;

            if( !empty($produtoEmTransito) ){

                //LOOP 3 - itera cada remessa vs produto
                foreach ($produtoEmTransito as $chave => $valor){

                    // Verifica a quantidade de reservados em transito
                    $estoqueReservado = $this->dao->getReservaEstoque(
                        $this->codigoPrestador, 
                        $valor['prdoid'], 
                        $valor['esroid']
                    );

                    $disponivelTransito = 0;
                    $disponivelTransito = ( $valor['quantidade_transito'] - $estoqueReservado[0]['qtde_reserva_transito'] );
                    $disponivelTransito = ($disponivelTransito < 0) ? 0 : $disponivelTransito;

                    if($saldoProduto <= $disponivelTransito){
                        $preReserva = $saldoProduto;
                    } else {
                        $preReserva = $disponivelTransito;
                    }

                    if ($preReserva === 0) {
                        continue;
                    }

                    $item = array(
                        'otioid'          => $itemOS,
                        'iesoid'          => $itemOsId,
                        'prdoid'          => $valor['prdoid'],
                        'prdproduto'      => $valor['prdproduto'],
                        'quantidade'      => $preReserva,
                        'quantidadeTotal' => $dadosItens['quantidade'],
                        'esroid'          => $valor['esroid'],
                        'tipo'            => 'transito'
                    );

                    $this->reservarSolicitar['estoque_em_transito'][] = $item;

                    $saldoProduto = ($saldoProduto - $preReserva);

                    $produtosAtendidos[$itemOS][ $itemOsId ]['quantidade_localizada'] += $preReserva;
                    $produtosAtendidos[$itemOS][ $itemOsId ]['quantidade_necessaria'] = intval($dadosItens['quantidade']);

                    $this->dataDisponibilidade[$valor['prdoid']] = $this->adicionarDiasData($valor['esrdata'], true);

                    if( $saldoProduto === 0 ){
                        break;
                    }

                } //LOOP 3
            }

        }//LOOP 1

        return $produtosAtendidos;
    }

    private function verificaDisponibilidadeEstoqueCD($itensEssenciais, $itemOS){

        $produtosAtendidos = array();

        //LOOP 1: Percorre os produtos [codicao E]
        foreach ( $itensEssenciais as $itemOsId => $dadosItens ) {

            $saldoProduto = 0;

            if( !isset($dadosItens['materiais']) || empty($dadosItens['materiais']) ){
                return $produtosAtendidos;
            }

            //LOOP 2: Percorre os produtos equivalentes [codicao OU]
            foreach ($dadosItens['materiais'] as $id => $materiais) {

                $estoqueCD        = $this->dao->getEstoque($this->codigoCentroDistribuicao, $materiais['iespprdoid']);
                $estoqueReservado = $this->dao->getReservaEstoque($this->codigoCentroDistribuicao, $materiais['iespprdoid']);

                //subtrai a quantidade disponivel no representante sobre a quantidade reservada
                if( !empty($estoqueReservado) && !empty($estoqueCD) ){
                    $estoqueDisponivelCD = ( $estoqueCD['quantidade_estoque'] - $estoqueReservado[0]['qtde_reserva_estoque'] );
                } else if( !empty($estoqueCD) ) {
                    $estoqueDisponivelCD = intval($estoqueCD['quantidade_estoque']);
                } else {
                    $estoqueDisponivelCD = 0;
                }

                $saldoProduto = ($saldoProduto === 0) ? $dadosItens['quantidade'] : $saldoProduto;

                if($estoqueDisponivelCD > 0){

                    if($saldoProduto <= $estoqueDisponivelCD){
                        $preReserva = $saldoProduto;
                    } else {
                        $preReserva = $estoqueDisponivelCD;
                    }

                    $this->reservarSolicitar['solicitar_CD'][] = array(
                        'otioid'          => $itemOS,
                        'iesoid'          => $itemOsId,
                        'prdoid'          => $materiais['iespprdoid'],
                        'prdproduto'      => $materiais['prdproduto'],
                        'quantidade'      => $preReserva,
                        'quantidadeTotal' => $dadosItens['quantidade'],
                        'tipo'            => 'estoque_cd'
                    );

                    $saldoProduto = ($saldoProduto - $preReserva);

                    $produtosAtendidos[$itemOS][ $itemOsId ]['quantidade_localizada'] += $preReserva;
                    $produtosAtendidos[$itemOS][ $itemOsId ]['quantidade_necessaria'] = intval($dadosItens['quantidade']);

                    $this->dataDisponibilidade[$materiais['iespprdoid']] = $this->adicionarDiasData($this->data_hoje, false);

                    if( $saldoProduto === 0 ){
                        break;
                    }
                }
            }//LOOP 2
        }//LOOP 1

        return $produtosAtendidos;
    }

    private function verificaFaltaCriticaEstoque($itensEssenciais, $itemOS){

        //LOOP 1: Percorre os produtos [codicao E]
        foreach ( $itensEssenciais as $itemOsId => $dadosItens ) {

            if( !isset($dadosItens['materiais']) || empty($dadosItens['materiais']) ){
                return $produtosAtendidos;
            }

            //LOOP 2: Percorre os produtos equivalentes [codicao OU]
            foreach ($dadosItens['materiais'] as $id => $materiais) {

                $this->reservarSolicitar['falta_critica_CD'][] = array(
                    'otioid'          => $itemOS,
                    'iesoid'          => $itemOsId,
                    'prdoid'          => $materiais['iespprdoid'],
                    'prdproduto'      => $materiais['prdproduto'],
                    'quantidade'      => $dadosItens['quantidade'],
                    'quantidadeTotal' => $dadosItens['quantidade'],
                    'esroid'          => '',
                    'tipo'            => 'falta_critica'
                );
            }//LOOP 2
        }//LOOP 1
    }

    private function verificaSaldoItensEssenciais($itensEssenciais, $produtoDisponivel) {

        if(!empty($produtoDisponivel)) {
            foreach ($produtoDisponivel as $iesoid => $value) {

                if ($value['quantidade_localizada'] >= $value['quantidade_necessaria']) {
                    //Se atendeu 100% oproduto, elimina o item essencial
                    unset($itensEssenciais[$iesoid]);
                } else {
                    //Deixa o item essencial com a quantidade faltante
                    $itensEssenciais[$iesoid]['quantidade'] = ($value['quantidade_necessaria'] - $value['quantidade_localizada']);
                }
            }
        }

        return $itensEssenciais;
    }

    public function setPedirProduto(){
        // Para utilizar no segundo fluxo
        $numeroOrdemAtual = $this->numeroOrdemServico;
        $idAgendamentoAtual = $this->IDagendamento;

        $produtos_pendentes                       = array();
        $produtos_estoque_representante           = array();
        $produtos_estoque_reservado_representante = array();
        $produtos_estoque_em_transito_reservado   = array();
        $produtos_estoque_em_transito             = array();
        $produtos_estoque_cd                      = array();

        //Verifica se produtos ainda estao disponiveis
        $retorno = $this->getEstoqueDisponivel();

        if ( ($retorno['status'] == 'erro') || ($retorno['status'] == 'erro_sistema') ) {
            return $retorno;
        }

        try {

            // Antecipa?o de materiais mais a reserva
            if(count($this->materiaisParaAntecipar['estoque_reservado_representante']) > 0
                || count($this->materiaisParaAntecipar['estoque_transito_reservado_representante']) > 0) {

				//popula novo array com os produtos que est? no representante
                if (count($this->reservarSolicitar['estoque_reservado_representante']) > 0) {
                    foreach ($this->reservarSolicitar['estoque_reservado_representante'] AS $itens) {
                        $produtos_estoque_reservado_representante[] = $itens;
                    }
                }

                //popula novo array com os produtos que est? em transito reservado
                if (count($this->reservarSolicitar['estoque_em_transito_reservado']) > 0) {
                    foreach ($this->reservarSolicitar['estoque_em_transito_reservado'] AS $itens) {
                        $produtos_estoque_em_transito_reservado[] = $itens;
                    }
                }

                // Limpa variável
                $produtos_reservar = array();
                $produtos_reservar = array_merge($produtos_estoque_reservado_representante,$produtos_estoque_em_transito_reservado);

                //efetiva a reserva no BD
                if (count($produtos_reservar) > 0) {
                    $this->reservaProdutoClass->setNumeroOrdemServico($this->numeroOrdemServico);
                    $this->reservaProdutoClass->setCodigoAgendamento($this->IDagendamento);
                    $this->reservaProdutoClass->setCodigoPrestador($this->codigoPrestador);
                    $this->reservaProdutoClass->setReservarProduto($produtos_reservar);
                }

                if(count($this->materiaisParaAntecipar['estoque_reservado_representante']) > 0){
                    $this->retiraMateriaisDoPrestador($this->materiaisParaAntecipar['estoque_reservado_representante'], self::ESTOQUE_RESERVADO);
                }

                if(count($this->materiaisParaAntecipar['estoque_transito_reservado_representante']) > 0){
                    $this->retiraMateriaisDoPrestador($this->materiaisParaAntecipar['estoque_transito_reservado_representante'], self::ESTOQUE_EM_TRANSITO_RESERVADO);
                }

                $this->solicitacaoDeSubstituicaoCD();

            }

            // Retorna na variavel global o numero da ordem
            $this->numeroOrdemServico = $numeroOrdemAtual;
            $this->IDagendamento = $idAgendamentoAtual;

            //Fluxo normal de reserva de produtos
            //popula novo array com os produtos que est? no representante
            if(count($this->reservarSolicitar['estoque_representante']) > 0) {
                foreach ($this->reservarSolicitar['estoque_representante'] AS $itens){
                    $produtos_estoque_representante[] = $itens;
                }
            }

            //popula novo array com os produtos que est? em transito
            if(count($this->reservarSolicitar['estoque_em_transito']) > 0) {
                foreach ($this->reservarSolicitar['estoque_em_transito'] AS $itens){
                    $produtos_estoque_em_transito[] = $itens;
                }
            }

            // Limpa variável para não entrar no proximo fluxo
            $produtos_reservar = array();
            $produtos_reservar = array_merge($produtos_estoque_representante, $produtos_estoque_em_transito );

            //efetiva a reserva no BD
            if(count($produtos_reservar) > 0){
                $this->reservaProdutoClass->setNumeroOrdemServico($this->numeroOrdemServico);
                $this->reservaProdutoClass->setCodigoAgendamento($this->IDagendamento);
                $this->reservaProdutoClass->setCodigoPrestador($this->codigoPrestador);
                $this->reservaProdutoClass->setReservarProduto($produtos_reservar);
            }

            //solicitar produtos ao CD
            if(count($this->reservarSolicitar['solicitar_CD']) > 0) {
                foreach ($this->reservarSolicitar['solicitar_CD'] AS $chave => $itens){
                    $produtos_solicitar_CD[] = $itens;
                    $produtos_pendentes[]    = $itens['prdproduto'];
                    $produtos_estoque_cd[]   = $itens;
                }

                $dadosAgenda = $this->dao->getOrdemServicoAgenda($this->numeroOrdemServico);
                $statusAgenda = $this->dao->getStatusAgendaHistorico();
                $this->msgHistorico = 'Produto Solicitado para distribução '.implode(" ,",$produtos_pendentes).' :Pendente.';
                $this->ordemServicoClass->salvaHistorico($this->numeroOrdemServico, $this->usuarioLogado, $this->msgHistorico, $dadosAgenda[0]['osadata'], $dadosAgenda[0]['osahora'], $statusAgenda[0]['mhcoid']);

                $idSolicitacao = $this->setSolicitarProduto($produtos_solicitar_CD);

                //efetiva a reserva no BD
                if(count($produtos_estoque_cd) > 0){
                    $this->reservaProdutoClass->setNumeroOrdemServico($this->numeroOrdemServico);
                    $this->reservaProdutoClass->setCodigoAgendamento($this->IDagendamento);
                    $this->reservaProdutoClass->setCodigoPrestador($this->codigoCentroDistribuicao);
                    $this->reservaProdutoClass->setCodigoSolicitacao($idSolicitacao);
                    $this->reservaProdutoClass->setReservarProduto($produtos_estoque_cd);
                }

                return $this->retornarMensagem('sucesso', self::SOLICITACAO_OK);
            }

            return $this->retornarMensagem('sucesso', self::RESERVA_OK);

        } catch ( Exception $e ) {
            $erro['status'] = 'erro';
            $erro['msg'] = $e->getMessage();
            return $erro;
        }

    }

    public function setCancelarReserva(){

        try {

            $this->reservaProdutoClass->setNumeroOrdemServico($this->numeroOrdemServico);
            $this->reservaProdutoClass->setCodigoAgendamento($this->IDagendamento);
            $this->reservaProdutoClass->setCancelarReserva();

            return $this->retornarMensagem('sucesso', self::RESERVA_CANCELADA);

        } catch (Exception $e) {
            $erro['status'] = 'erro';
            $erro['msg'] = $e->getMessage();
            return $erro;
        }
    }

    public function setProdutoInstalado(){

        try {

            $this->reservaProdutoClass->setNumeroOrdemServico($this->numeroOrdemServico);
            $this->reservaProdutoClass->setCodigoAgendamento($this->IDagendamento);
            $this->reservaProdutoClass->setStatusProdutoInstalado();

            return $this->retornarMensagem('sucesso', self::PRODUTO_INSTALADO);

        } catch (Exception $e) {
            $erro['status'] = 'erro';
            $erro['msg'] = $e->getMessage();
            return $erro;
        }
    }

    public function setSolicitarProduto($produtos = array()){

        try {

            if (empty ( $this->numeroOrdemServico )) {
                throw new Exception ( "O ID da ordem de servi? deve ser informado." );
            }

            if (empty ( $this->codigoPrestador )) {
                throw new Exception ( "O ID do representante deve ser informado." );
            }

            if (empty ( $this->IDagendamento )) {
                throw new Exception ( "O ID do agendamento deve ser informado." );
            }

            if(count($produtos) == 0){
                throw new Exception ( "Informe os produtos para efetuar a solicitação no CD." );
            }

            $this->solicitacaoProdutoClass->setNumeroOrdemServico($this->numeroOrdemServico);
            $this->solicitacaoProdutoClass->setCodigoAgendamento($this->IDagendamento);
            $this->solicitacaoProdutoClass->setCodigoPrestador($this->codigoPrestador);

            $retorno = $this->solicitacaoProdutoClass->setSolicitarProduto(FALSE, $produtos, $this->observacao);

            return $retorno;

        } catch ( Exception $e ) {
            $erro['status'] = 'erro';
            $erro['msg'] = $e->getMessage();
            return $erro;
        }
    }

    public function solicitacaoCritica(){

        //atribui o id do representante do CD
        $param_repoid = $this->dao->getParametros('SMART_AGENDA','REPOID_SOLICITACAO_FALSA') ;
        $this->codigoCentroDistribuicao = $param_repoid[0]['valor'];

        //recupera os itens (produtos) essenciais
        $this->getItensEssenciais();

        if( empty($this->itensEncontrados) ){
            return $this->retornarMensagem('erro', self::ERRO_PROCESSAMENTO);
        }

        //analisa status dos itens esseciais
        $this->analisaItensEstoque();

        $produtos_pendentes = array();
        $arrItemEssencial = array();

        if(count($this->reservarSolicitar['falta_critica_CD'])){

            //retira itens duplicados para o mesmo grupo essencial (Regra OU)
            foreach ($this->reservarSolicitar['falta_critica_CD'] as $chave => $itensCriticos) {

                if( !in_array($itensCriticos['iesoid'], $arrItemEssencial) ){

                    $arrItemEssencial[] = $itensCriticos['iesoid'];

                    $produtos_pendentes[] = $itensCriticos['prdproduto'];
                }else{
                    unset($this->reservarSolicitar['falta_critica_CD'][$chave]);
                }
            }

            $dadosAgenda = $this->dao->getOrdemServicoAgenda($this->numeroOrdemServico);
            $statusAgenda = $this->dao->getStatusAgendaHistorico();

            $this->msgHistorico = 'Produto Solicitado para distribuição '.implode(" ,",$produtos_pendentes).' :Aguardando estoque CD.';
            $this->ordemServicoClass->salvaHistorico($this->numeroOrdemServico, $this->usuarioLogado, $this->msgHistorico, $dadosAgenda[0]['osadata'], $dadosAgenda[0]['osahora'], $statusAgenda[0]['mhcoid']);

            $this->solicitacaoProdutoClass->setNumeroOrdemServico($this->numeroOrdemServico);
            $this->solicitacaoProdutoClass->setCodigoAgendamento($this->IDagendamento);
            $this->solicitacaoProdutoClass->setCodigoPrestador($this->codigoCentroDistribuicao);
            $sagoid = $this->solicitacaoProdutoClass->setSolicitarProduto(TRUE, $this->reservarSolicitar['falta_critica_CD'], $this->observacao);

        }

        return $this->retornarMensagem('sucesso', self::CD_NOTIFICADO);

    }

    public function setCancelarSolicitacaoProduto(){

        try {

            $this->solicitacaoProdutoClass->setNumeroOrdemServico($this->numeroOrdemServico);
            $this->solicitacaoProdutoClass->setCodigoAgendamento($this->IDagendamento);
            $this->solicitacaoProdutoClass->setCancelarSolicitacao('Agendamento Cancelado');

            return $this->retornarMensagem('sucesso', self::SOLICITACAO_CANCELADA);

        } catch (Exception $e) {
            $erro['status'] = 'erro';
            $erro['msg'] = $e->getMessage();
            return $erro;
        }
    }

    private function retornarMensagem($status, $msg, $data = NULL){
        $retorno['status'] = $status;
        $retorno['msg']    = $msg;
        $retorno['data']   = $data;
        return $retorno;
    }

    /**
     * cacula a diferen? em dias entre duas datas
     * @param date $from
     * @param date $to
     * @return number
     */
    private function date_difff($from, $to) {
        list($from_day, $from_month, $from_year) = explode("-", $from);
        list($to_day, $to_month, $to_year) = explode("-", $to);
        $from_date = mktime(0,0,0,$from_month,$from_day,$from_year);
        $to_date = mktime(0,0,0,$to_month,$to_day,$to_year);
        $days = ($to_date - $from_date)/86400;
        return ceil($days);
    }


    /**
     * Calcula quanto tempo gasta para enviar o produto para o representante
     *
     * @param date $data_inicial
     * @param bool $transito
     * @return string
     */
    private function adicionarDiasData($data_inicial, $transito){

        //calcula o tempo total para disponibilizar o produto
        $tempo_total = $this->tempoModalTransporte + $this->tempoRecebimentoRemessa;


        //caso o produto esteja em transito, n? somar o tempo de prepara?o
        if(!$transito){
            $tempo_total = ($tempo_total + $this->tempoPreparacaoRemessa);
        }

        //calcular a diferen? em dias entre a data de envio com a data atual
        $quant_dias_envio = $this->date_difff($data_inicial, $this->data_hoje);

        //se a quantidade de dias de envio for maior o prazo estimado total, retorna data de hoje
        if($quant_dias_envio > $tempo_total){
            return $this->data_hoje;
        }

        //calcula quantidade de dias que passaram a partir da data de envio da remessa
        $dias_prazo = $tempo_total - $quant_dias_envio;

        //adiciona os dias do prazo a partir da data de hj para saber a data prov?el que chega
        $data = DateTime::createFromFormat('d-m-Y', $this->data_hoje);
        $data->add(new DateInterval('P'.$dias_prazo.'D'));

        return $data->format('d-m-Y');

    }


    /**
     * Ordena as datas da menor para a maior
     *
     * @param array $datas
     * @return number|unknown
     */
    private function ordenarData($datas){

        if(!function_exists('cmp')){

            function cmp($a, $b){

                $a = strtotime(str_replace('/','-',$a));
                $b = strtotime(str_replace('/','-',$b));
                if ($a == $b) {
                    return 0;
                }
                return ($a > $b) ? -1 : 1;
            }
        }

        usort($datas, 'cmp');
        return $datas;

    }

    public function setNumeroOrdemServico( $valor ){
        $this->numeroOrdemServico = $valor;
    }

    public function setCodigoPrestador($valor){
        $this->codigoPrestador = $valor;
    }

    public function setAgendamentoID($valor){
        $this->IDagendamento = $valor;
    }

    public function setObs($valor){
        $this->observacao = $valor;
    }

    public function getItensEssenciaisEncontrados(){
        $this->getItensEssenciais();
        return $this->itensEncontrados;
    }

    /**
     * Recebe data atual e data do chamado que tem a item reservado e se a item esta em transito
     * Retorna se há tempo suficiente para substituicao do item
     *
     * @param date $data_inicial
     * @param date $data_do_agendamento
     * @param bool $transito
     * @return bool
     */
    private function validaTempoDeSubstituicao($data_inicial, $data_do_agendamento, $transito){

        //adiciona dias na data inicial para comparar com a data do agendamento
        $dataChegadaNovosItens = $this->adicionarDiasData($data_inicial, $transito);

        //valida se a data de chegada ?menor que a data do agendamento
        $diferencaDeDias = $this->date_difff($dataChegadaNovosItens, $data_do_agendamento);

        //retorna boolean se as pe?s chegar? ?tempo para o agendamento inicial
        $this->logAntecipacao("tempo de substituir ok?", $diferencaDeDias > 0);
        return $diferencaDeDias > 0;
    }

    /**
     * Faz a solicitação de item para o CD para substituir itens que foram usados em
     * antecipacao de atendimento
     *
     * @param $numeroOrdemServico
     * @param $IDagendamento
     * @param $codigoCentroDistribuicao
     * @param $dadosAgenda
     * @param $statusAgenda
     *
     */
    private function solicitacaoDeSubstituicaoCD() {
        // Verifica se tem produtos a serem solicitados par ao cd
        if(count($this->reservarSolicitar['solicitar_CD_RESERVADO']) > 0) {
            $produtos_solicitar_CD = array();

            // Cria array com os produtos por OS
            foreach ($this->reservarSolicitar['solicitar_CD_RESERVADO'] AS $item){
                $produtos_solicitar_CD[$item['ordoid']][] = $item;
            }

            // Pega os dados da agenda e coloca em um array com indice [idOrdermServico]
            $retornoDadosAgenda = array();

            // Faz um loop nas ordens de serviço
            foreach($produtos_solicitar_CD as $idOrdemServico => $itens){
                // Seta variavel global para fazer a solicitacao do produto
                $this->numeroOrdemServico = $idOrdemServico;

                // Loop nos produtos
                foreach($itens as $valor){
                    // Caso o idOrdermService não exista no array
                    if(!key_exists($valor['ordoid'], $retornoDadosAgenda)){
                        // Pega dados da agenda por OS
                        $dadosAgenda = $this->dao->getDadosDaAgenda($valor['ordoid']);
                        
                        // Solicitacao de produto
                        $this->numeroOrdemServico = $valor['ordoid'];
                        $this->IDagendamento = $dadosAgenda[0]['osaoid'];
                        $idSolicitacao = $this->setSolicitarProduto($itens);
                        
                        // Popular array para passar para outros foreach
                        $retornoDadosAgenda[$valor['ordoid']] = $dadosAgenda[0];
                        $retornoDadosAgenda[$valor['ordoid']]['idSolicitacao'] = $idSolicitacao;
                    }
                }
            }

            // Verifica o status da agenda
            $statusAgenda = $this->dao->getStatusAgendaHistorico();
            foreach($produtos_solicitar_CD as $idOrdemServico => $itens){
                foreach($itens as $dados){
                    // Salva historico
                    $this->ordemServicoClass->salvaHistorico(
                        $valor['ordoid'],
                        $this->usuarioLogado,
                        'Produto Solicitado para distribuição '.$valor['prdproduto'].' :Pendente.',
                        $retornoDadosAgenda[$dados['ordoid']]['osadata'],
                        $retornoDadosAgenda[$dados['ordoid']]['osahora'],
                        $statusAgenda[0]['mhcoid']
                    );
                }
            }

            //efetiva a reserva no BD
            foreach($produtos_solicitar_CD as $idOrdemServico => $itens){
                foreach($itens as $dados){
                    // Cria reserva de produto no CD
                    $this->reservaProdutoClass->setReservarProdutoNoCD(
                        $dados['ordoid'], 
                        $retornoDadosAgenda[$dados['ordoid']]['osaoid'],
                        $this->codigoCentroDistribuicao,
                        $retornoDadosAgenda[$dados['ordoid']]['idSolicitacao'], 
                        $dados['prdoid'],
                        $dados['quantidade']
                    );
                }
            }
            $this->reservarSolicitar['solicitar_CD_RESERVADO'] = array();
            return $this->retornarMensagem('sucesso', self::SOLICITACAO_OK);
        }

    }


    /**
     * Se habilitado, imprime os logs na tela para facilitar debug na demanda de antecipação
     *
     * @param string $mensagem
     * @param mix $valor
     * @param bool $die
     *
     */
    private function logAntecipacao($mensagem, $valor, $die = false) {
        if($this->logHabilitado) {
            echo "<pre>";
            var_dump($mensagem, $valor);
            if($die) {
                die();
            }
            echo "</pre>";
        }
    }

    /**
     * Seta data do agendamento
     */
    public function setDataAgendamento($data){
        $this->dataAgendamento = $data;
    }
}


?>