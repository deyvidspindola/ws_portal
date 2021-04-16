<?php

include_once _MODULEDIR_ . 'SmartAgenda/DAO/ControleConsumoDAO.php';

/**
 * Sascar - Tecnologia e Seguranca Automotiva
 *
 * Classe responsável por adicionar e remover os registros no banco de dados
 */
class ControleConsumo {

    private $dao;
    protected $idsOrdemServico = array();
    protected $idAgendamento;
    protected $idsAgendamento = array();
    protected $slot = array();
    protected $tipoAtendimento;
    protected $workSkillConsumo;

    public function __construct($conn) {
        $this->dao = new ControleConsumoDAO($conn);

    }

    public function setIdOrdemServico(array $idsOrdemServico)
    {
        $this->idsOrdemServico = $idsOrdemServico;
        return $this;
    }

    public function setIdAgendamento($id){
        $this->idAgendamento= $id;
        return $this;
    }

    public function setIdsAgendamento(array $ids) {
        $this->idsAgendamento = $ids;
        return $this;
    }

    public function setWorkSkillConsumo(array $workSkillConsumo){

        $this->workSkillConsumo = $workSkillConsumo;
        return $this;

    }

    public function getWorkSkillConsumo(){

        return $this->workSkillConsumo;

    }

    public function getIdOrdemServico()
    {
        return $this->idsOrdemServico;
    }

    public function setSlot(array $slot)
    {
        $this->slot = $slot;
        return $this;
    }

    public function getSlot()
    {
        return $this->slot;
    }

    public function setTipoAtendimento($tipo)
    {

        if($tipo == "F"){
            $this->tipoAtendimento = "FIXO";
        }else{
            $this->tipoAtendimento = "MOVEL";
        }
        return $this->tipoAtendimento;
    }

    public function getTipoAtendimento()
    {
        return $this->tipoAtendimento;
    }

    public function cadastrarOrdemServico() {

        $agendamentos = array();
        $consumoCapacidade = array();

        foreach ($this->slot['agendamento'] as $chave => $dados) {

            $workSkillConsumo = $this->getWorkSkillConsumo();

            if(count($workSkillConsumo) > 0) {
                 foreach ($workSkillConsumo as $workskill) {

                    //verifica se é hibrido
                    $tipoHibrido = strpos($workskill, "HIBRIDO");

                    //caso nao seja
                    if( $tipoHibrido === false ){
                        // verifica se o WS adicional é Fixo ou Movel
                        $tipoAtendimento = strpos($workskill, $this->tipoAtendimento);
                        if( $tipoAtendimento === false ){
                            continue;
                        }
                    }

                    $consumo = $this->salvarConsumoCapacidade($workskill, $dados);
                    $consumoCapacidade = array_merge($consumoCapacidade, $consumo);

                }
            }

            $agendamentos[$this->idsAgendamento[$chave]] = $consumoCapacidade;
            $consumoCapacidade = array();
        }

        $this->salvarOrdemServicoAgendaConsumo($agendamentos);
    }

    protected function salvarConsumoCapacidade($workskill, $dados)
    {
        //Pega as informações do workskill
        $categoria = $this->dao->getCategoriaPorNome($workskill);

        // Dados de retorno
        $ordemServico = array();

        foreach ($dados['distribuicao'] as $hora => $time) {
            $capacidade = $this->dao->verificaCapacidadeConsumoData(
                $this->slot['data_slot'], $this->slot['representante'], $categoria['occoid'], $hora
            );

            $slotAgendamento = ($hora == $dados['slot_agendamento_ofsc']);

            if (!count($capacidade)) {
                $capacidade = array(
                    'occoid' => null,
                    'occoccoid' => $categoria['occoid'],
                    'occbucket' => $this->slot['representante'],
                    'occdt_agenda' => $this->slot['data_slot'],
                    'occtime_slot' => $hora,
                    'occ_tempo_herdado' => 0,
                    'occ_tempo_distribuido' => 0,
                );

                if ($slotAgendamento) {
                    $capacidade['occ_tempo_distribuido'] = $dados['tempo_distribuido'];
                } else {
                    $capacidade['occ_tempo_herdado'] = $time;
                }

                $idCapacidadeConsumo = $this->dao->inserirRegistro(
                    'ofsc_capacidade_consumo', $capacidade, 'occoid'
                );
            } else {
                if ($slotAgendamento) {
                    $capacidade['occ_tempo_distribuido'] = $capacidade['occ_tempo_distribuido'] + $dados['tempo_distribuido'];
                } else {
                    $capacidade['occ_tempo_herdado'] = $capacidade['occ_tempo_herdado'] + $time;
                }

                $idCapacidadeConsumo = $capacidade['occoid'];
                $this->dao->atualizarRegistro(
                    'ofsc_capacidade_consumo', $capacidade, array(
                        'occoid' => $idCapacidadeConsumo
                    )
                );
            }
            $ordemServico[] = array(
                'idCapacidadeConsumo' => $idCapacidadeConsumo,
                'tempoHerdado' => $slotAgendamento ? 0 : $time,
                'tempoDistribuido' => $slotAgendamento ? $dados['tempo_distribuido'] : 0,
            );
        }
        return $ordemServico;
    }

    protected function salvarOrdemServicoAgendaConsumo($ordemServico) {

        foreach ($ordemServico as $idOrdemServico => $capacidadeConsumo) {
            foreach ($capacidadeConsumo as $dados) {
                $ordemServico = array(
                    'asacoid' => null,
                    'asacosaoid' => $idOrdemServico,
                    'asacoccoid' => $dados['idCapacidadeConsumo'],
                    'asacvalor_herdado' => $dados['tempoHerdado'],
                    'asacvalor_distribuido' => $dados['tempoDistribuido']
                );

                $this->dao->inserirRegistro(
                    'ordem_servico_agenda_consumo', $ordemServico
                );
            }
        }
    }

    public function removerAgenda() {

        foreach ($this->idsOrdemServico as $idOrdemServico) {

            $registros = $this->dao->getConsumoPorOrdemServico($idOrdemServico, $this->idAgendamento);

            foreach ($registros as $dados) {
                if ($dados['tempo_herdado'] == 0 && $dados['tempo_distribuido'] == 0) {
                    $this->dao->removerRegistro(
                        'ofsc_capacidade_consumo', array('occoid' => $dados['occoid'])
                    );
                } else {
                    $atualizar = array(
                        'occ_tempo_herdado' => $dados['tempo_herdado'],
                        'occ_tempo_distribuido' => $dados['tempo_distribuido']
                    );

                    $this->dao->atualizarRegistro(
                        'ofsc_capacidade_consumo', $atualizar, array('occoid' => $dados['occoid'])
                    );

                    $this->dao->removerRegistro(
                        'ordem_servico_agenda_consumo', array('asacoid' => $dados['asacoid'])
                    );
                }
            }
        }

        $this->dao->removerConsumoGeral();
    }
}
