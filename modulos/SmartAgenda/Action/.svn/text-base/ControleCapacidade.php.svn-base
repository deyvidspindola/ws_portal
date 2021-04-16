
<?php

include_once _MODULEDIR_ . 'SmartAgenda/DAO/ControleCapacidadeDAO.php';

/**
 * Sascar - Tecnologia e Seguranca Automotiva
 *
 * Classe responsável por calcular e definir a agenda de dias e horários para
 * agendamento, cruzando as informações do serviço OFSC e do banco de dados
 */
class ControleCapacidade {


    protected $intervaloAgenda = 14;
    protected $dataInicial = null;
    protected $atividades = array();
    protected $slots = array();
    protected $matriz = array();
    protected $atendimentoEmergencial = false;
    protected $tamanhoTimeSlot;
    protected $dao;
    protected $horainicioAgendamento;

    public function __construct($conn) {

        $this->dao = new ControleCapacidadeDAO($conn);

        $this->tamanhoTimeSlot = $this->dao->getParametro('TAMANHO_TIME_SLOT');
        $this->tamanhoTimeSlot = intval($this->tamanhoTimeSlot);

     }

    /**
     * Define qual a data inicial da agenda de capacide
     */
    public function setDataInicial(DateTime $data) {
        $this->dataInicial = $data;
        return $this;
    }

    /**
     * Retorna a data inicial da agenda de capacide
     *
     * @return DateTime
     */
    public function getDataInicial() {
        return $this->dataInicial;
    }

    /**
     * Define as atividades que serão utilizadas para calcular os slos disponiveis
     */
    public function setAtividades($atividades) {
        foreach ($atividades as $atividade) {
            if (!isset($atividade['activity_duration']) || !isset($atividade['activity_travel_time'])) {
                throw new Exception('As informações da atividade para o controle de capacidade são inválidas');
            }
            array_push($this->atividades, $atividade);
        }
        return $this;
    }

	private function getHoraInicialCalendario() {

        $turnos = array();

        $turno1 = $this->dao->getParametro('PERIODO_DZERO_MANHA');
        $turno1 = explode(';', $turno1);
        $turnos['manha']['corte_inicio'] = strtotime($turno1[0]);
        $turnos['manha']['corte_fim']    = strtotime($turno1[1]);
        $turnos['manha']['hora_base']    = strtotime($turno1[2]);

        $turno2 = $this->dao->getParametro('PERIODO_DZERO_TARDE');
        $turno2 = explode(';', $turno2);
        $turnos['tarde']['corte_inicio'] = strtotime($turno2[0]);
        $turnos['tarde']['corte_fim']    = strtotime($turno2[1]);
        $turnos['tarde']['hora_base']    = strtotime($turno2[2]);

        $turno3 = $this->dao->getParametro('PERIODO_DZERO_NOITE');
        $turno3 = explode(';', $turno3);
        $turnos['noite']['corte_inicio'] = strtotime($turno3[0]);
        $turnos['noite']['corte_fim']    = strtotime($turno3[1]);
        $turnos['noite']['hora_base']    = strtotime($turno3[2]);

        return $turnos;

    }

    public function getAtividades() {
        return $this->atividades;
    }

    /**
     * Define quais os slots que serão usados para gerar a agenda
     *
     */
    public function setSlots($slots) {
        foreach ($slots as $slot) {
            $this->validarEstruturaAtividadeObjeto($slot);
            array_push($this->slots, $slot);
        }

        return $this;
    }

    /**
     * Retorna os slots usados para gerar a agenda
     */
    public function getSlots() {
        return $this->slots;
    }

    /**
     * Retorna qual o total em minutos da duração das atividades informadas
     */
    public function getAtividadeDuracao() {
        $duracao = 0;
        foreach ($this->atividades as $atividade) {
            $duracao += $atividade['activity_duration'];
        }
        return $duracao;
    }

    /**
     * Retorna qual o total em minutos do tempo de deslocamentos das atividades
     */
    public function getAtividadeDeslocamento() {
        $duracao = 0;
        foreach ($this->atividades as $atividade) {
            $duracao += $atividade['activity_travel_time'];
        }
        return $duracao;
    }

    /**
     * Retorna o valor total das somas das atividades e do primeiro deslocamento
     */
    public function getAtividadeDuracaoTotal( $isPontoFixo ) {
        $duracao = 0;
        foreach ($this->atividades as $posicao => $atividade) {
            $duracao += $atividade['activity_duration'];

            if (!$posicao) {
                $duracao += $atividade['activity_travel_time'];
            }
        }

        $duracao = $isPontoFixo ? ( $duracao - $atividade['activity_travel_time'] ) : $duracao;

        return $duracao;
    }

    /**
     * Define qual o intervalo de dias usado para gerar a agenda
     */
    public function setIntervalo($intervalo) {
        $this->intervaloAgenda = $intervalo;
    }

    /**
     * Retorna qual o intervalo de dias usado para gerar a agenda
     */
    public function getIntervalo() {
        return $this->intervaloAgenda;
    }

    public function setAtendimentoEmergencial($atendimentoEmergencial) {
        $this->atendimentoEmergencial = $atendimentoEmergencial;
        return $this;
    }

    public function isAtendimentoEmergencial() {
        return $this->atendimentoEmergencial;
    }


    public function setHorainicioAgendamento($hora) {
        $this->horainicioAgendamento = strtotime($hora);
    }

    protected function validarEstruturaAtividadeObjeto($objeto) {
        $campos = array(
            'location', 'date', 'time_slot', 'work_skill', 'quota', 'available'
        );
        foreach ($campos as $campo) {
            if (!property_exists($objeto, $campo)) {
                throw new Exception('Propriedade do OFSC não encontrada.');
            }
        }
    }

    protected function buscarSlotsBanco() {
        $dataInicial = clone $this->getDataInicial();
        $dataFinal = clone $this->getDataInicial();
        $dataFinal->add(new DateInterval("P".($this->intervaloAgenda-1)."D"));
        return $this->dao->getCapacidadeConsumo(
            $dataInicial->format('Y-m-d'), $dataFinal->format('Y-m-d')
        );
    }

    /**
     * Retorna a hora no formato de chave de slot
     *
     * Exemplo:
     *   Informa 8
     *   Retorna 08-10
     *
     * @param string $hora
     * @return string
     */
    protected function formataChaveHora($hora) {
        $horaInicial = str_pad($hora, 2, "0", STR_PAD_LEFT);
        $horaFinal = str_pad(($hora==22 ? 00 : ($hora + 2)), 2, "0", STR_PAD_LEFT);
        return "{$horaInicial}-{$horaFinal}";
    }

    /**
     * Retorna o nome no formato de chave de slot da próxima hora
     *
     * Exemplo:
     *   Informa 08-10
     *   Retorna 10-12
     *
     * @param string $nome
     * @return string
     */
    protected function getProximoSlot($nome) {
        return $this->formataChaveHora(preg_filter('/[0-9]{1,2}-([0-9]{2})/', '$1', $nome));
    }

    /**
     * Gera a matriz base da agenda onde vai listar os próximos dias que serão
     * possíveis ser inseridas as informações dos slots disponíveis
     */
    protected function gerarMatrizBase() {
        $data = clone $this->getDataInicial();
        for ($i = 0; $i < $this->intervaloAgenda; $i++) {
            $this->matriz[$data->format('Y-m-d')] = array();
            $data->add(new DateInterval('P1D'));
        }
    }

    /**
     * Aplica os slots informados para a classe na matriz principal
     */
    protected function aplicarDadosOFSCNaMatriz() {

        foreach($this->slots as $slot) {

            $disponivel = $this->isAtendimentoEmergencial() ? $slot->quota : $slot->available;
            $categoriaCapacidade = $slot->work_skill;

            if(strpos($slot->work_skill, 'HIBRIDO') !== false) {

                $categoriaCapacidade = trim(preg_replace('/HIBRIDO/', 'FIXO', $slot->work_skill));
                $categoriaCapacidadeMovel = trim(preg_replace('/HIBRIDO/', 'MOVEL', $slot->work_skill));

                 $this->matriz[$slot->date][$slot->location][$categoriaCapacidadeMovel][$slot->time_slot] = array(
                    'data_slot'             => $slot->date,
                    'time_slot_agendamento' => $slot->time_slot,
                    'categoria'             => $categoriaCapacidadeMovel,
                    'representante'         => $slot->location,
                    'quota_total'           => $slot->quota,
                    'quota_disponivel'      => $disponivel,
                    'tempo_distribuido'     => 0,
                    'permite_agendamento'   => false,
                    'agendamento'           => array()
                );

                ksort($this->matriz[$slot->date][$slot->location][$categoriaCapacidadeMovel], SORT_NUMERIC);
            }

            $this->matriz[$slot->date][$slot->location][$categoriaCapacidade][$slot->time_slot] = array(
                'data_slot'             => $slot->date,
                'time_slot_agendamento' => $slot->time_slot,
                'categoria'             => $categoriaCapacidade,
                'representante'         => $slot->location,
                'quota_total'           => $slot->quota,
                'quota_disponivel'      => $disponivel,
                'tempo_distribuido'     => 0,
                'permite_agendamento'   => false,
                'agendamento'           => array()
            );

            ksort($this->matriz[$slot->date][$slot->location][$categoriaCapacidade], SORT_NUMERIC);
        }
    }

    /**
     * Aplica os registros de agendamento encontrados no banco de dados na
     * matriz de agendamento principal
     */
    protected function aplicarDadosBancoNaMatriz() {

        $slotsBanco = $this->buscarSlotsBanco();

        foreach ($slotsBanco as $slot) {

            if (!isset($this->matriz[$slot['occdt_agenda']][$slot['occbucket']])) {
                continue;
            }

           $data = $this->matriz[$slot['occdt_agenda']][$slot['occbucket']]
                                [$slot['occdescricao']][$slot['occtime_slot']];

            if(empty($data)){
                continue;
            }

            $consumido = ($data['quota_total'] - $data['quota_disponivel']);
            $usadoReal = ($consumido + ($slot['occ_tempo_herdado'] - $slot['occ_tempo_distribuido']) );
            $data['quota_disponivel'] = ( $data['quota_total'] -  $usadoReal);

            if ($slot['occ_tempo_distribuido'] > 0) {
                $data['tempo_distribuido'] = $slot['occ_tempo_distribuido'];
            }


           $this->matriz[$slot['occdt_agenda']][$slot['occbucket']]
                    [$slot['occdescricao']][$slot['occtime_slot']] = $data;
        }

    }

    /**
     * Calcula se um slot é válido para permitir um agendamento, para isso
     * percorre e verifica todo o conjunto de slots subsequentes para definir
     * se todo o conjunto de slots necessários possui o tempo necessário para
     * executar o tempo de deslocamento e atividade informado para a classe
     */
    protected function validarSlot($data, $prestador, $categoria, $horario, $isPontoFixo) {

        // Verifica se a posição horário/slot existe
        if (!isset($this->matriz[$data][$prestador][$categoria][$horario])) {
            return false;
        }

        // Retorna as informações do slot atual
        $quotaDisponivel = $this->matriz[$data][$prestador][$categoria][$horario]['quota_disponivel'];
        $quotaDisponivel = ($quotaDisponivel >= $this->tamanhoTimeSlot) ? $this->tamanhoTimeSlot : $quotaDisponivel;

        // Verifica se posicao hoário/slot tem quota disponível
        if (!$quotaDisponivel) {
            return false;
        }

        $utilizouTarefaCompleta = false;
        $distribuicao = array();
        $totalHoras = $this->getAtividadeDuracaoTotal( $isPontoFixo );

        /**
         * Verifica se a tarefa cabe por completo no timeslot
         */
        if ($quotaDisponivel >= $totalHoras) {
            $distribuicao[$horario] = $totalHoras;
            $totalHoras = 0;

            // Retorna a distribuição do próprio slot
            return $distribuicao;

        } else if ($this->isAtendimentoEmergencial() && $quotaDisponivel > 0 ){

            $distribuicao[$horario] = $totalHoras;
            $totalHoras = 0;

            // Retorna a distribuição do próprio slot
            return $distribuicao;

        /*
         * Verifica se a atividade é maior que X minutos para poder ser
         * distribuida entre os timeslots
         */
        } else if ($totalHoras < $this->tamanhoTimeSlot) {
            return false;

        // se o total de horas sera distribuida e o slot não for completo
        }else if ($totalHoras > $this->tamanhoTimeSlot && $quotaDisponivel < $this->tamanhoTimeSlot){
            return false;
        } else if(   $totalHoras == $this->tamanhoTimeSlot && $totalHoras > $quotaDisponivel ) {
            return false;
        }

        $totalHoras -= (int) $quotaDisponivel;
        $distribuicao[$horario] = $quotaDisponivel;

        // Gera o nome do proximo slot para continuar a verificar
        $horario = $this->getProximoSlot($horario);

        while (($totalHoras > 0) && isset($this->matriz[$data][$prestador][$categoria][$horario])) {
            // Retorna a quantidade de tempo disponivel
            $quotaDisponivel = $this->matriz[$data][$prestador][$categoria][$horario]['quota_disponivel'];
            $quotaDisponivel = ($quotaDisponivel >= $this->tamanhoTimeSlot) ? $this->tamanhoTimeSlot : $quotaDisponivel;

            // Verifica se vai precisar utilizar o slot inteiro
            if (($totalHoras >= $this->tamanhoTimeSlot) && ($quotaDisponivel == $this->tamanhoTimeSlot)) {
                $distribuicao[$horario] = $this->tamanhoTimeSlot;
                $totalHoras -= $this->tamanhoTimeSlot;
            } else if ($quotaDisponivel >= $totalHoras) {
                $distribuicao[$horario] = $totalHoras;
                $totalHoras = 0;
            } else {
                break;
            }
            $horario = $this->getProximoSlot($horario);
        }
        if ($totalHoras > 0) {
            return false;
        }

        return $distribuicao;
    }

    /**
     * Calcula a distribuição de tempo entre os slots por atividade
     */
    protected function calcularAgendamento( $distribuicao, $isPontoFixo) {

        $masterDistribuicao = array();

        foreach ($this->atividades as $posicao => $atividade) {
            $totalHorasAtividade = 0;
            $ditribuicaoAtividade = array(
                'slot_agendamento_ofsc' => null,
                'tempo_distribuido' => 0,
                'distribuicao' => array()
            );

            $totalGeralAtividade = $atividade['activity_duration'];
            $totalDeslocamento = $atividade['activity_travel_time'];

            if (!$posicao) {

                if( $isPontoFixo ) {
                    $totalDeslocamento = 0;
                } else {
                    $totalGeralAtividade += $atividade['activity_travel_time'];
                }

            } else {
                $totalDeslocamento = 0;
            }


            foreach ($distribuicao as $hora => $tempo) {
                $totalHorasAtividade += $tempo;
                $tempoDistribuido = 0;

                // Define o horário para cadastrar a tarefa
                if (is_null($ditribuicaoAtividade['slot_agendamento_ofsc']) && $totalHorasAtividade >= $totalDeslocamento) {
                    $ditribuicaoAtividade['slot_agendamento_ofsc'] = $hora;
                }

                if ($totalHorasAtividade >= $totalGeralAtividade) {
                    $distribuicao[$hora] = $totalHorasAtividade - $totalGeralAtividade;
                    $ditribuicaoAtividade['distribuicao'][$hora] = $tempo - $distribuicao[$hora];

                    if ($distribuicao[$hora] == 0) {
                        unset($distribuicao[$hora]);
                    }

                    // Calcula se existe um valor que foi distribuido
                    foreach ($ditribuicaoAtividade['distribuicao'] as $slot => $minutos) {
                        if ($slot != $ditribuicaoAtividade['slot_agendamento_ofsc']) {
                            $tempoDistribuido += $minutos;
                        }
                    }
                    $ditribuicaoAtividade['tempo_distribuido'] += $tempoDistribuido;

                    $masterDistribuicao[] = $ditribuicaoAtividade;
                    break;
                } else {
                    $ditribuicaoAtividade['distribuicao'][$hora] = $tempo;
                    unset($distribuicao[$hora]);
                }
            }
            reset($distribuicao);
        }
        return $masterDistribuicao;
    }

    /**
     * Percorre e calcula individualmente cada slot para determinar se cada um
     * deles pode ser agendado ou não
     */
    protected function calcularSlotsValidos() {

    	$turnos    = $this->getHoraInicialCalendario();
        $horaAtual = $this->horainicioAgendamento;
        $dataAtual = date('Y-m-d');
        $date = new DateTime($dataAtual);
        $date->add(new DateInterval('P1D'));
        $amanha = $date->format('Y-m-d');

        foreach ($this->matriz as $data => $prestadores) {
            foreach ($prestadores as $prestador => $categorias) {
                foreach ($categorias as $categoria => $slots) {
                    foreach ($slots as $horario => $slot) {

                        if (!$this->isAtendimentoEmergencial()) {

                            //Se a data retornada pelo OFSC for D-zero
                            if ($dataAtual == $data) {

                                //Se a hora atual for superior a hora de corte de inicio da tarde...
                                if( $horaAtual > $turnos['manha']['corte_fim'] ){
                                    //...entao limpa os time-slots do dia, pois nao pode mais agendar em D-zero
                                    $this->matriz[$data] = array();
                                    continue 4;

                                } else {

                                    $horaInicial = preg_replace('/(\d+)-.*/', '$1', $slot['time_slot_agendamento']);
                                    $horaInicial = strtotime($horaInicial . ':00');

                                    if($horaInicial < $turnos['manha']['hora_base']) {
                                        continue;
                                    }
                                }
                            }

                            //Se a data retornada pelo OFSC for D+1
                            if($amanha == $data) {

                                if( $horaAtual >= $turnos['noite']['corte_inicio'] ){

                                    $horaInicial = preg_replace('/(\d+)-.*/', '$1', $slot['time_slot_agendamento']);
                                    $horaInicial = strtotime($horaInicial . ':00');

                                    if($horaInicial < $turnos['noite']['hora_base']) {


                                        continue;
                                    }
                                }
                            }

                        }

                        $isPontoFixo = stripos($categoria, 'FIXO');
                        $isPontoFixo = is_bool($isPontoFixo) ? $isPontoFixo : TRUE;

                        // Verifica se o slot é válido
                        $distribuicao = $this->validarSlot(
                            $data, $prestador, $categoria, $horario, $isPontoFixo
                        );

                        if (false === $distribuicao) {
                            continue;
                        }

                        $this->matriz[$data][$prestador][$categoria][$horario]['permite_agendamento'] = true;
                        $this->matriz[$data][$prestador][$categoria][$horario]['agendamento'] = $this->calcularAgendamento( $distribuicao, $isPontoFixo );
                    }
                }
            }
        }
    }

    /**
     * Retorna a agenda disponível e normalizada já incluindo a informação de
     * qual horário tem seu agendamento livre para a atividade informada
     */
    public function getCapacidade() {
        $this->gerarMatrizBase();

        $this->aplicarDadosOFSCNaMatriz();

        if (!$this->isAtendimentoEmergencial()) {
            $this->aplicarDadosBancoNaMatriz();
        }


        $this->calcularSlotsValidos();


        return $this->matriz;
    }


    public function verificarDisponiblidadeCota($matrizCapacidade, $dataAgenda) {

        $isCotaDisponivel = true;

        foreach ($matrizCapacidade as $data => $dados) {
            if($dados['permite_agendamento'] === false) {

                if(!is_null($dataAgenda)){
                    if( strtotime($dataAgenda) == strtotime($data) ) {
                         $isCotaDisponivel = false;
                         break;
    }
                }

                unset($matrizCapacidade[$data]);
            }
        }

        if($isCotaDisponivel){
            $isCotaDisponivel  = empty($matrizCapacidade) ? false : true;
        }

        return $isCotaDisponivel;

    }

    public function getSlot($data, $prestador, $categoria, $horario) {
        if (!isset($this->matriz[$data][$prestador][$categoria][$horario])) {
            return array();
        }
        return $this->matriz[$data][$prestador][$categoria][$horario];
    }
}