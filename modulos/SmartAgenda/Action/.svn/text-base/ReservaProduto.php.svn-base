<?php

require_once _MODULEDIR_ . 'SmartAgenda/Action/Action.php';
require_once _MODULEDIR_ . 'SmartAgenda/DAO/ReservaProdutoDAO.php';

/**
 * Classe controladora das entidades de Reserva de Produto
 */
class ReservaProduto extends Action {

    private $dao;
    private $dadosReserva;

    public function __construct($conn = null) {

        if( is_null($conn) ){
            global $conn;
        }

        $this->dao = new ReservaProdutoDAO($conn);

        $this->dadosReserva = new stdClass();

    }

    public function setNumeroOrdemServico($numeroOrdemServico){
        $this->dadosReserva->numeroOrdemServico = $numeroOrdemServico;

    }

    public function setCodigoAgendamento($codigoAgendamento){
        $this->dadosReserva->codigoAgendamento = $codigoAgendamento;

    }

    public function setCodigoPrestador($codigoPrestador){
        $this->dadosReserva->codigoPrestador = $codigoPrestador;

    }

    public function setCodigoSolicitacao($codigoSolicitacao){
        $this->dadosReserva->codigoSolicitacao = $codigoSolicitacao;

    }

    public function setReservarProduto($produtos){

        if(count($produtos) == 0){
            throw new Exception('Informe os produtos para efetuar a reserva.');
        }

        $this->dao->setReservarProduto( $this->dadosReserva, $produtos);

    }

    public function setCancelarReserva($justificativa = null) {

        $justificativa = empty($justificativa) ? 'Agendamento Cancelado' : $justificativa;

        $this->dao->setCancelarReserva( $this->dadosReserva, $justificativa );

    }

    public function setStatusProdutoInstalado(){
        $this->dao->setStatusProdutoInstalado( $this->dadosReserva );

    }


    public function setReservarProdutoNoCD($numeroOrdemServico, $codigoAgendamento = null, $codigoPrestador, $codigoSolicitacao = null, $prdoid, $quantidade){
        $this->dao->setReservarProdutoNoCD($numeroOrdemServico, $codigoAgendamento, $codigoPrestador, $codigoSolicitacao, $prdoid, $quantidade);
    }

}

?>