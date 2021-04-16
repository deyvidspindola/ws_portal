<?php

class AgendamentoVO {

    private $atendimentoEmergencial = false;
    private $retiradaReinstalacao = false;
    private $prestadorDirecionado;
    private $workSkills = array();
    private $workSkillsConsumo = array();
    private $dataInicialAgenda = null;
    private $codigoEstado = null;
    private $idEstado = null;
    private $codigoCidade = null;
    private $cidade = null;
    private $codigoBairro = null;
    private $idBairro = null;
    private $bairro = null;
    private $cep = null;
    private $logradouro = null;
    private $numero = null;
    private $complemento = null;
    private $referencia = null;
    private $ordemServico = array();
    private $paginaAgendamento = 1;
    private $melhorDia = array();
    private $semanasCalendario = 2;
    private $dadosPesquisa          = array();
    private $horainicioAgendamento;
    private $estoqueDisponivelPrestador = array();
    private $prestadorAgendamento;
    private $duracaoInicialAtividade = null;
    private $tempoDeslocamentoInicial = null;


    public function setAtendimentoEmergencial($atendimentoEmergencial) {
        $this->atendimentoEmergencial = $atendimentoEmergencial;
        return $this;
    }

    public function isAtendimentoEmergencial() {
        return $this->atendimentoEmergencial;
    }


    public function setRetiradaReinstalacao($retiradaReinstalacao)   {
        $this->retiradaReinstalacao = $retiradaReinstalacao;
        return $this;
    }

    public function isRetiradaReinstalacao() {
        return $this->retiradaReinstalacao;
    }

    public function setPrestadorDirecionado($codigoPrestador) {
        $this->prestadorDirecionado = $codigoPrestador;
        return $this;
    }

    public function getPrestadorDirecionado() {
        $prestadorDirecionado = (!empty($this->prestadorDirecionado)) ? $this->prestadorDirecionado : NULL;
        return $prestadorDirecionado;
    }

    public function addWorkSkill($skill) {
        $skill = (array) $skill;
        $this->workSkills = array_merge($this->workSkills, $skill);
        return $this;
    }

    public function getWorkSkill() {
        return $this->workSkills;
    }


    public function addWorkSkillConsumo($skill) {
        $skill = (array) $skill;
        $this->workSkillsConsumo = array_merge($this->workSkillsConsumo, $skill);
        return $this;
    }


    public function getWorkSkillConsumo() {
        return $this->workSkillsConsumo;
    }


    public function setDataInicialAgenda(DateTime $data) {
        $this->dataInicialAgenda = $data;
        return $this;
    }

    public function getDataInicialAgenda() {
        return $this->dataInicialAgenda;
    }

    public function getDataCalculoAgenda() {
        if ($this->paginaAgendamento > 1) {
            $data = clone $this->dataInicialAgenda;
            $dias = ($this->paginaAgendamento - 1) * ($this->semanasCalendario * 7);
            $data->add(new DateInterval("P{$dias}D"));
            return $data;
        } else {
            return clone $this->dataInicialAgenda;
        }
    }

    public function setCodigoEstado($codigo) {
        $this->codigoEstado = $codigo;
        return $this;
    }

    public function getCodigoEstado() {
        return $this->codigoEstado;
    }

    public function setIdEstado($idEstado)  {
        $this->idEstado = $idEstado;
        return $this;
    }

    public function getIdEstado() {
        return $this->idEstado;
    }

    public function setCodigoCidade($codigo) {
        $this->codigoCidade = $codigo;
        return $this;
    }

    public function getCodigoCidade() {
        return $this->codigoCidade;
    }

    public function setCidade($cidade) {
        $this->cidade = $cidade;
        return $this;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function setCodigoBairro($codigo) {
        $this->codigoBairro = $codigo;
        return $this;
    }

    public function getCodigoBairro() {
        return $this->codigoBairro;
    }

    public function setIdBairro($idBairro) {
        $this->idBairro = $idBairro;
        return $this;
    }

    public function getIdBairro() {
        return $this->idBairro;
    }

    public function setBairro($bairro) {
        $this->bairro = $bairro;
        return $this;
    }

    public function getBairro() {
        return $this->bairro;
    }

    public function setCEP($cep) {
        $this->cep = $cep;
        return $this;
    }

    public function getCEP() {
        return $this->cep;
    }

    public function setLogradouro($logradouro) {
        $this->logradouro = $logradouro;
        return $this;
    }

    public function getLogradouro() {
        return $this->logradouro;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
        return $this;
    }

    public function getNumero() {
        return $this->numero;
    }
    public function setComplemento($complemento) {
        $this->complemento = $complemento;
        return $this;
    }

    public function getComplemento() {
        return $this->complemento;
    }

    public function setReferencia($referencia) {
        $this->referencia = $referencia;
        return $this;
    }

    public function getReferencia() {
        return $this->referencia;
    }


    public function addOrdemServico($idOrdemServico, $codigoTipo, $grupo, $dificuldade) {
        array_push($this->ordemServico, array(
            'id' => $idOrdemServico,
            'tipo' => $codigoTipo,
            'grupo' => $grupo,
            'dificuldade' => $dificuldade
        ));
        return $this;
    }

    public function getOrdemServico() {
        return $this->ordemServico;
    }

    public function limparOrdemServico() {
        $this->ordemServico = array();
        return $this;
    }

    public function getTotalOrdemServico() {
        return count($this->ordemServico);
    }

    public function setPaginaAgenda($pagina) {
        $this->paginaAgendamento = $pagina;
        return $this;
    }

    public function getPaginaAgenda() {
        return $this->paginaAgendamento;
    }

    public function setMelhorDia(array $datas) {
        $this->melhorDia = $datas;
        return $this;
    }

    public function getMelhorDia() {
        return $this->melhorDia;
    }

    public function setSemanasCalendario($semanasCalendario) {
        $this->semanasCalendario = $semanasCalendario;
        return $this;
    }

    public function getSemanasCalendario() {
        return $this->semanasCalendario;
    }

    public function setDadosPesquisa($dadosPesquisa) {
        $this->dadosPesquisa = array(
            'cmp_cliente_autocomplete' => $dadosPesquisa->cmp_cliente_autocomplete,
            'cmp_cliente'              => $dadosPesquisa->cmp_cliente,
            'cmp_tipo_servico'         => $dadosPesquisa->cmp_tipo_servico,
            'cmp_data_inicio'          => $dadosPesquisa->cmp_data_inicio,
            'cmp_data_fim'             => $dadosPesquisa->cmp_data_fi
        );
        return $this;
    }

    public function getDadosPesquisa() {
        return $this->dadosPesquisa;
    }

    public function setHorainicioAgendamento($hora){
        $this->horainicioAgendamento = $hora;
    }

    public function getHorainicioAgendamento(){
        $horainicioAgendamento = (empty($this->horainicioAgendamento)) ? date('H:i') : $this->horainicioAgendamento;
        return $horainicioAgendamento;
    }

    public function setEstoqueDisponivelPrestador($estoqueDisponivelPrestador){
        $this->estoqueDisponivelPrestador = $estoqueDisponivelPrestador;
    }

    public function getEstoqueDisponivelPrestador(){
        return $this->estoqueDisponivelPrestador;
    }

    public function setPrestadorAgendamento($prestadorAgendamento){
        $this->prestadorAgendamento = $prestadorAgendamento;
    }

    public function getPrestadorAgendamento(){
        $prestadorAgendamento = (!empty( $this->prestadorAgendamento)) ?   $this->prestadorAgendamento : NULL;
        return $prestadorAgendamento;
    }

    public function setDuracaoInicialAtividade($duracaoInicial) {
        $this->duracaoInicialAtividade = $duracaoInicial;
    }

    public function getDuracaoInicialAtividade() {
        return $this->duracaoInicialAtividade;
    }

    public function setTempoDeslocamentoInicial($tempoDeslocamento) {
        $this->tempoDeslocamentoInicial = $tempoDeslocamento;
    } 

    public function getTempoDeslocamentoInicial() {
        return $this->tempoDeslocamentoInicial;
    }
}