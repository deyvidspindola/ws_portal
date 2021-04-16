<?php

//classe de manipulação de dados no bd
require_once (_MODULEDIR_ . 'SmartAgenda/DAO/AgendaDAO.php');

require_once (_MODULEDIR_ ."/Principal/Action/prnBackoffice.php");
require_once (_MODULEDIR_ ."/Principal/DAO/prnBackofficeDAO.php");

class Agenda{



	private $idAgendamento;
    private $idOrdemServico;
	private $dao;
    private $prnBackofficeDAO;
    private $prnBackoffice;

	public function __construct($conn = null){

        if(is_null($conn)){
		Global $conn;
        }

		$this->dao = new AgendaDAO($conn);

        //Instancia Classe BackOffice
        $this->prnBackofficeDAO = new prnBackofficeDAO($conn);
        $this->prnBackoffice    = new prnBackoffice($this->prnBackofficeDAO);
	}


    public function __set($nome, $valor){
        $this->$nome = $valor;
    }

    public function __get($nome){
        return $this->$nome;
    }


    public function getDadosAgendamento($id, $tipo){

    	$dadosAgendamento = $this->dao->dadosAgendamento($id, $tipo);

    	return $dadosAgendamento;
    }

    /**
     * Salva os agendamentos nas tabelas de agendamento das ordens de serviço
     * na intranet, retorna os ID's dos agendamentos em um array
     *
     * @param array $agendamentos
     * @param array $agendamentosEnderecos
     * @param array $contatos
     * @return array Informando os ID's dos agendamentos
     */
    public function salvarAgendamento(array $agendamentos, array $agendamentosEnderecos, array $contatos)
    {
        $registros = array();
        foreach ($agendamentos as $chave => $agendamento) {
            $dadosEndereco = $this->dao->getDadosInstalacao(
                $agendamento['osaordoid']
            );

            $idAgendamento = $this->dao->inserirRegistro(
                'ordem_servico_agenda', $agendamento, 'osaoid'
            );

            if (!count($dadosEndereco)) {
                $this->dao->inserirRegistro(
                    'ordem_servico_inst', $agendamentosEnderecos[$chave]
                );
            } else {
                $this->dao->atualizarRegistro(
                    'ordem_servico_inst',
                    $agendamentosEnderecos[$chave],
                    array('osiordoid' => $agendamento['osaordoid'])
                );
            }
            $registros[$chave] = $idAgendamento;
        }

        $this->dao->salvarContatos($contatos);

        return $registros;
    }

    /**
     * Atualiza a informação do número de agendamento da tarefa no OFSC na
     * tabela de agendamento da intranet
     *
     * @param integer $idOrdemServicoAgenda
     * @param integer $idAgendamentoOFSC
     * @return void
     */
    public function atualizarAgendamento($idOrdemServicoAgenda, $idAgendamentoOFSC)
    {
        $this->dao->atualizarRegistro(
            'ordem_servico_agenda',
            array('osaid_atividade' => $idAgendamentoOFSC),
            array('osaoid' => $idOrdemServicoAgenda)
        );
    }

    /**
     * Salva a requisição de analise de backoffice
     *
     * @param array $dados
     * @return void
     */
    public function salvarAnaliseBackoffice(array $dados)
    {
        $this->dao->inserirRegistro(
            'backoffice', $dados
        );

        $dadosHistorico = array(
            'bacplaca'  => $dados['bacplaca'],
            'clioid'    => $dados['bacclioid'],
            'bacstatus' => 'P',
            'acao'      => 'cadastrar',
            'bacbmsoid' => 23,
            'data_confirmar' => date("d/m/Y H:i:s"),
            'bacdetalhamento_solicitacao' => $dados['bacdetalhamento_solicitacao']
        );

        //Salva a solicitação no histórico do contrato
        $this->prnBackoffice->inserirHistoricoContrato( (object) $dadosHistorico );
    }

    public function setExcluirAgendamento($ordoid, $usuario, $obs){

        $dados = $this->dao->setExcluirAgendamento($ordoid, $usuario, $obs);

        return $dados;
    }
}