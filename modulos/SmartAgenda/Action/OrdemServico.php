<?php
//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/smart_agenda_os_class_'.date('d-m-Y').'.txt');

//classe de manipulação de dados no bd
require_once (_MODULEDIR_ . 'SmartAgenda/DAO/OrdemServicoDAO.php');
include_once _MODULEDIR_ . 'SmartAgenda/Action/Contrato.php';

class OrdemServico {

	private $dao;


	public function __construct($conn = null){

        if(empty($conn)) {
            Global $conn;
        }
		$this->dao = new OrdemServicoDAO($conn);
        $this->contrato = new Contrato($conn);
	}

	//Metodo que chama a dao para armazenar o historico do sms e email
	public function salvaHistorico($ordem, $usuario, $msg, $dataAgenda, $horaAgenda, $status){

        $msg = str_replace("'", ' ', $msg);

		$historico = $this->dao->gravarHistorico($ordem, $usuario, $msg, $dataAgenda, $horaAgenda, $status);
		return $historico;
	}

	//metodo que chama a dao para buscar o id do titulo motivo corretora
	public function retornoHistoricoCorretora($tipoAgendamento){
		$motivo = $this->dao->motivoHistoricoCorretora($tipoAgendamento);
		return $motivo;
	}


	//retorna os dados do remetente do email
	public function dadosRemetente($servidor){
		$remetente = $this->dao->dadosRemetente($servidor);
		return $remetente;
	}

    public function recuperarDadosOrdemServico($campos, $filtros) {

        $dados = $this->dao->recuperarDadosOrdemServico($campos, $filtros);

        return $dados;
    }

    public function atualizarRepresentante($ordoid, $repoid = NULL) {

        $retorno = $this->dao->atualizarRepresentante($ordoid, $repoid);

        $retorno = ($retorno === 0) ? false : true;

        return $retorno;

    }

    public function atualizarInstalador($ordoid, $itloid = NULL) {

        $retorno = $this->dao->atualizarInstalador($ordoid, $repoid);

        $retorno = ($retorno === 0) ? false : true;

        return $retorno;

    }

    public function getTiposServicos(){

        $dados = $this->dao->getTiposServicos();

        return $dados;
    }

    public function excluirLocalInstalacao($ordoid) {

        $retorno = $this->dao->excluirLocalInstalacao($ordoid);

        $retorno = ($retorno === 0) ? false : true;

        return $retorno;

    }

    public function atualizarDirecionamento($ordoid) {

        $retorno = $this->dao->atualizarDirecionamento($ordoid);

        $retorno = ($retorno === 0) ? false : true;

        return $retorno;

    }

    public function retornaClasseMigracaoOS($ordemServico) {
        // Verifica se a OS tem motivo UPGRADE
        // Se tiver retorna a nova classe de migração
        $idClasseMigracao = 0;

        $equipamentoContrato = $this->contrato->getEquipamentoContrato($ordemServico);

        // Se retornar classe de migração é atualizado a OS
        if(count($equipamentoContrato) > 0) {
            $idClasseMigracao = $equipamentoContrato[0]['eqcoid'];
        }

        return $idClasseMigracao;
    }
}
