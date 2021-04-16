<?php

require_once _MODULEDIR_ . 'SmartAgenda/Action/Action.php';
require_once _MODULEDIR_ . 'SmartAgenda/DAO/SolicitacaoProdutoDAO.php';

/**
 * Classe controladora das entidades de Solicitacoes de Produto
 */
class SolicitacaoProduto extends Action {

    private $dao;
    private $numeroOrdemServico;
    private $codigoAgendamento;
    private $codigoPrestador;
    private $codigoSolicitacao;

    public function __construct($conn = null) {

        if( is_null($conn) ){
            global $conn;
        }

        $this->dao = new SolicitacaoProdutoDAO($conn);
   }

    public function setNumeroOrdemServico($numeroOrdemServico){
        $this->numeroOrdemServico = $numeroOrdemServico;

    }

    public function setCodigoAgendamento($codigoAgendamento){
        $this->codigoAgendamento = $codigoAgendamento;

    }

    public function setCodigoPrestador($codigoPrestador){
        $this->codigoPrestador = $codigoPrestador;

    }

    public function setCodigoSolicitacao($codigoSolicitacao){
        $this->codigoSolicitacao = $codigoSolicitacao;
    }

    public function setSolicitarProduto($isFaltaCritica, $produtos, $observacao) {

        $sagoid = $this->dao->setSolicitarProduto(
                $isFaltaCritica,
                $this->codigoAgendamento,
                $this->codigoPrestador,
                $this->numeroOrdemServico,
                $produtos,
                $observacao);

        return $sagoid;
    }

    public function setCancelarSolicitacao($justificativa) {
        $this->dao->setCancelarSolicitacao($this->codigoAgendamento, $this->numeroOrdemServico, $justificativa);

    }

    public function setAtenderSolicitacao(){
        $this->dao->setStatusSolicitacao($this->codigoSolicitacao, $this->numeroOrdemServico, $this->codigoAgendamento, "STATUS_ATENDIDO");
    }


    public function setReativarSolicitacao(){
        $this->dao->setStatusSolicitacao($this->codigoSolicitacao, $this->numeroOrdemServico, $this->codigoAgendamento, "STATUS_PENDENTE");
    }
}
?>