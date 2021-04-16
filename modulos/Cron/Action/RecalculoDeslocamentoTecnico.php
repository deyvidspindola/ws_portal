<?php
/*
 * Code atualization
 * @Autor: André Ávila - Sascar Developer
 * @Gestor: Marcos Kowalschuk
 * @Squad: Engine Software
 * Atualization Date: 05/04/2019
 * Objective: Implementation of Service Soap Maplink to Service REstfull Maplink Trip
 * Jira [SES-19] Maplink SOAP to RESTFUL
 */


require_once _MODULEDIR_ .'SmartAgenda/Action/Resource.php';
require_once _MODULEDIR_ .'/Cron/DAO/RecalculoDeslocamentoTecnicoDAO.php';
require_once _SITEDIR_   .'webservice/maplink/Action/AddressFinder.php';
require_once _SITEDIR_   .'webservice/maplink/Action/Route.php';

class CalculoDeslocamentoTecnico {

    private $dao;
    private $conn;

    private $resource;
    private $AddressFinder;

    private $locationStart;
    private $locationEnd;
    private $idHistorico;
    private $diasCalculoDeslocamento;

    /*
    * Informação Importante:
    * Favor nunca alterar esta ordem sem alterar tmb no relatório
    * RelCalculoDeslocamentoTecnico.php função: retornaMensagensCron()
     */
    const DESLOCAMENTO_STATUS_ZERO                    = 0;
    const DESLOCAMENTO_STATUS_OK                      = 1;
    const DESLOCAMENTO_STATUS_ERRO_PONTO_ATUAL        = 2;
    const DESLOCAMENTO_STATUS_ERRO_PONTO_ANTERIOR     = 3;
    const DESLOCAMENTO_STATUS_ERRO_ROTEAMENTO_MAPLINK = 4;

    public function __construct($conn){

        $this->dao           = new CalculoDeslocamentoTecnicoDAO($conn);
        $this->resource      = new Resource();
        $this->AddressFinder = new AddressFinder($conn);
        $this->conn          = $conn;

        $this->setDiasCalculoDeslocamento();
        $this->setIdHistorico();
    }

    /**
     * @author Thiago Leal
     * @since 27/03/2017
     * Pegar o Id do historico para Deslocamento - Pedágio
     */
    private function setIdHistorico(){
        $id = $this->dao->getIdHistorico();

        if (!is_null($id)){
            $this->idHistorico = $id;
        }
    }

    private function setDiasCalculoDeslocamento(){
        $this->diasCalculoDeslocamento = $this->dao->getDiasCalculoDeslocamento();
    }


    /**
     * @author Thiago Leal
     * @since 15/02/2017
     *
     * 1 - Consulta Ordens de Serviço Agendadas
     * 2 - Consulta Locais do PS
     * 3 - Consulta Totais de deslocamento e pedágio
     * 4 - Calcula deslocamento e pedágio da Rota
     * 5 - Salva os dados de deslocamento
     */
    public function executarRotina($data, $debug, $tecnicoTeste){

        if (is_null($data)) {
            $data = date('Y-m-d', strtotime("-".$this->diasCalculoDeslocamento." days"));
        }

        if( !is_null($debug) ) {
            $this->dao->setDebugQuery(TRUE);
        }

        $ordensVI = '0';

        $ordensElegiveis = $this->dao->verificaVisitaImprodutiva($data,$tecnicoTeste);

        $ordensElegiveis = $this->dao->verificaComissaoInstalacao($ordensElegiveis, $data, $ordensVI, $tecnicoTeste);

        if (count($ordensElegiveis) > 0){

            foreach ($ordensElegiveis as $idTecnico => $agendamentos) {

                $ordensTecnico  = array();
                $diaSemana      = "";

                //Extrai as informações dos Agendamentos do técnico
                foreach ($agendamentos as $chave => $ordem) {
                    $ordensTecnico[] = $ordem->ordoid;
                    $diaSemana       = $ordem->diasemana;
                }

                $diaSemana = $this->retornaDiaSemana($diaSemana);

                $atendimentos = new stdClass();
                $atendimentos->data      = $data;
                $atendimentos->ordens    = implode(",", $ordensTecnico);
                $atendimentos->idTecnico = $idTecnico;

                //Ordena as OS do técnico já verificando se é Visita Improdutiva ou Comissao Pendente de Pgto
                $atendimentosOrdenados = $this->dao->ordenaSequenciaAtendimentos($atendimentos, $ordensVI);

                if( count($atendimentosOrdenados) > 0 ) {

                    //Define latitude e longitude de cada ponto de parada da rota
                    $agedamentosGeocodificados = $this->defineCoordenadasAgendamentos($agendamentos, $data);

                   //Se não retornar ordenação é pq o técnico não gerou Visita Improdutiva e nem Atendimento
                    if( count($agedamentosGeocodificados) > 0 ) {

                        $primeiraOS = current($atendimentosOrdenados);
                        $primeiraOS = $agedamentosGeocodificados[$primeiraOS->ordoid];
                        $ultimaOS   = end($atendimentosOrdenados);
                        $ultimaOS   = $agedamentosGeocodificados[$ultimaOS->ordoid];
                        reset($atendimentosOrdenados);

                        //Configura os objetos de Origem e Destino da rota
                        $this->defineOrigemDestino($idTecnico, $diaSemana, $primeiraOS, $ultimaOS);

                        //Define a rota baseada no array de agedamentos ordenaddos, extrai do array de agendamentos geocodificados
                        $pontosPercorridos = $this->definirRota($atendimentosOrdenados, $agedamentosGeocodificados);

                        //Calcula Deslocamento e Pedágio
                        $pontosCalculados = $this->calculaDeslocamento($pontosPercorridos);

                        //Salva os custos de deslocamento
                        $this->salvaDeslocamentos($pontosCalculados, $idTecnico);

                        //Print de ajuda para DEBUG-Teste-Homologação
                        if (!empty($debug)) {
                            $tecnico_representante = $this->dao->getTecnicoRepresentante($idTecnico);
                            echo '<pre>';
                            echo 'Técnico : TC'.$idTecnico.' - '.$tecnico_representante->instalador.'<br>';
                            echo 'Representante : PS'.$tecnico_representante->repoid.' - '.$tecnico_representante->representante.'<br>';
                            print_r($pontosCalculados);
                            echo '<hr></pre>';
                        }
                    }
                }
            }
        }
    }

    /**
     * @author Thiago Leal
     * @since 21/02/2017
     *
     * Define os objetos locationStart e locationEnd
     * Origem e Destino do técnico na rota
     */
    private function defineOrigemDestino($idTecnico, $diaSemana, $primeiraOS, $ultimaOS){


        $resourceLocationStart = "";
        $resourceLocationEnd   = "";

        $this->locationStart = new stdClass();
        $this->locationEnd   = new stdClass();
        $this->locationStart->geocoded = false;
        $this->locationStart->ponto    = 'partida';
        $this->locationEnd->geocoded   = false;
        $this->locationEnd->ponto      = 'chegada';
        $IDpartida;
        $IDchegada;

        //Busca os IDs de saída e chegada do Técnico no OFSC
        try {
            $this->resource->setIdRecurso( 'TC'.$idTecnico ) ;
            $dadosLocal = $this->resource->getAssignedLocations();

            if (isset($dadosLocal)) {
                foreach ($dadosLocal as $dia => $valor) {

                    if (in_array($dia, $diaSemana)) {
                        if( isset($valor->start) ) {
                            $IDpartida = $valor->start;
                        }

                        if( isset($valor->end) ) {
                            $IDchegada = $valor->end;
                        }
                    }
                }
            }

        } catch (Exception $e) {
            $this->registraLog("ERRO OFSC: ".$e->getMessage());
        }

        //Busca informações de Local Sáida do Prestador
        try {
            if ( !empty($IDpartida) ){
                $this->resource->setIdRecurso( 'TC'.$idTecnico  );
                $this->resource->setIdLocal( $IDpartida );
                $saida = $this->resource->getResourceLocation();
            }

        } catch (Exception $e) {
            $this->registraLog("ERRO OFSC: ".$e->getMessage());
        }

        if (!empty($saida)){

            $estado = (isset($saida->state) ? $this->dao->getUf( utf8_decode($saida->state) ) : null);
            $cidade = (isset($saida->city) ? $this->dao->getCidade( utf8_decode($saida->city) ) : null);

            $this->locationStart->cep        = (isset($saida->postalCode) ? $saida->postalCode : null);
            $this->locationStart->cidade     = (!is_null($cidade) ? $cidade->nome : null);
            $this->locationStart->estado     = (!is_null($estado) ? $estado->nome : null);
            $this->locationStart->logradouro = (isset($saida->address) ? utf8_decode($saida->address) : null);
            $this->locationStart->bairro     = '';
            $this->locationStart->uf         = (!is_null($estado) ? $estado->uf : null);
            $this->locationStart->osacoordy  = (isset($saida->latitude) ? $saida->latitude : 0);
            $this->locationStart->osacoordx  = (isset($saida->longitude) ? $saida->longitude : 0);
            $this->locationStart->ordoid  = 0;
            $this->locationStart->geocoded   = false;

            if(isset($saida->status) && ($saida->status == 'found' || $saida->status == 'manual') ) {

                $this->locationStart->geocoded = true;

            } else {

                //Tenta obter coordenadas do Prestador no MapLink
                $endereco = new stdClass();
                $endereco->logradouro   = $this->locationStart->logradouro;
                $endereco->cep          = $this->locationStart->cep;
                $endereco->bairro       = '';
                $endereco->cidade       = $this->locationStart->cidade;
                $endereco->estado       = $this->locationStart->estado;
                $endereco->orsordoid    = $primeiraOS->ordoid;
                $endereco->orsdt_agenda = $primeiraOS->osadata;
                $endereco->orshr_agenda = $primeiraOS->osahora;
                $resposta = $this->getCoordenadas($endereco, true, 'Saída');

                if(empty($resposta->erro)) {
                    $this->locationStart->osacoordy = $resposta->coordy;
                    $this->locationStart->osacoordx = $resposta->coordx;
                    $this->locationStart->geocoded  = true;
                }
            }
        }

        //Busca informações de Local Chegada do Prestador
        try {
            if ( !empty($IDchegada) ){
                $this->resource->setIdRecurso( 'TC'.$idTecnico  );
                $this->resource->setIdLocal( $IDchegada );
                $chegada = $this->resource->getResourceLocation();
            }
        } catch (Exception $e) {
            $this->registraLog("ERRO OFSC: ".$e->getMessage());
        }

        if (!empty($chegada)){

            $estado = (isset($chegada->state) ? $this->dao->getUf( utf8_decode($chegada->state) ) : null);
            $cidade = (isset($chegada->city) ? $this->dao->getCidade( utf8_decode($chegada->city) ) : null);

            $this->locationEnd->cep        = (isset($chegada->postalCode) ? $chegada->postalCode : null);
            $this->locationEnd->cidade     = (!is_null($cidade) ? $cidade->nome : null);
            $this->locationEnd->estado     = (!is_null($estado) ? $estado->nome : null);
            $this->locationEnd->logradouro = (isset($chegada->address) ? utf8_decode($chegada->address) : null);
            $this->locationEnd->bairro     = '';
            $this->locationEnd->uf         = (!is_null($estado) ? $estado->uf : null);
            $this->locationEnd->osacoordy  = (isset($chegada->latitude) ? $chegada->latitude : 0);
            $this->locationEnd->osacoordx  = (isset($chegada->longitude) ? $chegada->longitude : 0);
            $this->locationEnd->geocoded   = false;

            if(isset($chegada->status) && ($chegada->status == 'found' || $chegada->status == 'manual') ) {

                $this->locationEnd->geocoded = true;

            } else {

                //Tenta obter coordenadas do Prestador no MapLink
                $endereco = new stdClass();
                $endereco->logradouro   = $this->locationEnd->logradouro;
                $endereco->cep          = $this->locationEnd->cep;
                $endereco->bairro       = '';
                $endereco->cidade       = $this->locationEnd->cidade;
                $endereco->estado       = $this->locationEnd->estado;
                $endereco->orsordoid    = $ultimaOS->ordoid;
                $endereco->orsdt_agenda = $ultimaOS->osadata;
                $endereco->orshr_agenda = $ultimaOS->osahora;
                $resposta = $this->getCoordenadas($endereco, true, 'Chegada');

                if(empty($resposta->erro)) {
                    $this->locationEnd->osacoordy = $resposta->coordy;
                    $this->locationEnd->osacoordx = $resposta->coordx;
                    $this->locationEnd->geocoded  = true;
                }
            }
        }

        //Se não achou Start ou End no OFSC assume o endereço do Prestador como Local Base
        if ($this->locationStart->geocoded === false || $this->locationEnd->geocoded === false) {

            $enderecoPS = $this->dao->getEnderecoRepresentante($idTecnico);

            if (isset($enderecoPS)){

                if ($this->locationStart->geocoded === false) {
                    $this->locationStart->cep        = $enderecoPS->cep;
                    $this->locationStart->cidade     = $enderecoPS->cidade;
                    $this->locationStart->estado     = $enderecoPS->estado;
                    $this->locationStart->uf         = $enderecoPS->uf;
                    $this->locationStart->logradouro = $enderecoPS->logradouro;

                    //Grava Histórico
                    $historico = new stdClass();
                    $historico->orsordoid    = $primeiraOS->ordoid;
                    $historico->orsdt_agenda = $primeiraOS->osadata;
                    $historico->orshr_agenda = $primeiraOS->osahora;
                    $historico->orsstatus    = $this->idHistorico;
                    $historico->orssituacao  = "O Endereço de saída foi alterado para a base do PS.";
                    $retorno = $this->dao->gravarHistorico($historico);
                }

                if ($this->locationEnd->geocoded === false) {
                    $this->locationEnd->cep        = $enderecoPS->cep;
                    $this->locationEnd->cidade     = $enderecoPS->cidade;
                    $this->locationEnd->estado     = $enderecoPS->estado;
                    $this->locationEnd->uf         = $enderecoPS->uf;
                    $this->locationEnd->logradouro = $enderecoPS->logradouro;

                    //Grava Histórico
                    $historico = new stdClass();
                    $historico->orsordoid    = $ultimaOS->ordoid;
                    $historico->orsdt_agenda = $ultimaOS->osadata;
                    $historico->orshr_agenda = $ultimaOS->osahora;
                    $historico->orsstatus    = $this->idHistorico;
                    $historico->orssituacao  = "O Endereço de chegada foi alterado para a base do PS.";
                    $retorno = $this->dao->gravarHistorico($historico);
                }

                //Obter cordenadas do Prestador no AddressFinder do MapLink
                $endereco = new stdClass();
                $endereco->logradouro   = $enderecoPS->logradouro;
                $endereco->cep          = $enderecoPS->cep;
                $endereco->bairro       = $enderecoPS->bairro;
                $endereco->cidade       = $enderecoPS->cidade;
                $endereco->estado       = $enderecoPS->estado;

                if ($this->locationStart->geocoded === false) {
                    $endereco->orsordoid    = $primeiraOS->ordoid;
                    $endereco->orsdt_agenda = $primeiraOS->osadata;
                    $endereco->orshr_agenda = $primeiraOS->osahora;
                    $resposta = $this->getCoordenadas($endereco, true, 'Saída');

                    if(empty($resposta->erro)) {
                        $this->locationStart->osacoordy = $resposta->coordy;
                        $this->locationStart->osacoordx = $resposta->coordx;
                        $this->locationStart->geocoded  = true;
                    }
                }

                if ($this->locationEnd->geocoded === false) {
                    $endereco->orsordoid    = $ultimaOS->ordoid;
                    $endereco->orsdt_agenda = $ultimaOS->osadata;
                    $endereco->orshr_agenda = $ultimaOS->osahora;
                    $resposta = $this->getCoordenadas($endereco, true, 'Chegada');

                    if(empty($resposta->erro)) {

                        $this->locationEnd->osacoordy = $resposta->coordy;
                        $this->locationEnd->osacoordx = $resposta->coordx;
                        $this->locationEnd->geocoded  = true;
                    }
                }
            }
        }
    }

    /**
     * @author Thiago Leal
     * @since 21/02/2017
     *
     * Define as coordenadas de cada agendamento
     */
    private function defineCoordenadasAgendamentos($agendamentos, $data){

        $agedamentosGeocodificados = array();

        foreach ($agendamentos as $chave => $ordem) {

            $endereco = new stdClass();
            $enderecoAgendamento = $this->dao->getEnderecoAgendamento($ordem, $data);


            if( ($ordem->os_direcionada == 'f') && isset($enderecoAgendamento->ordoid) ) {

                //Se o agendamento não tiver coordenadas, Obtem no AddressFinder do MapLink
                if ( empty($enderecoAgendamento->osacoordx) || empty($enderecoAgendamento->osacoordy)
                    || $enderecoAgendamento->osacoordx == 0 || $enderecoAgendamento->osacoordy == 0) {

                    $endereco->logradouro   = (empty($enderecoAgendamento->logradouro)) ? '' : $enderecoAgendamento->logradouro;
                    $endereco->cep          = (empty($enderecoAgendamento->cep))        ? '' : $enderecoAgendamento->cep;
                    $endereco->bairro       = (empty($enderecoAgendamento->bairro))     ? '' : $enderecoAgendamento->bairro;
                    $endereco->cidade       = (empty($enderecoAgendamento->cidade))     ? '' : $enderecoAgendamento->cidade;
                    $endereco->estado       = (empty($enderecoAgendamento->estado))     ? '' : $enderecoAgendamento->estado;
                    $endereco->orsordoid    = $ordem->ordoid;
                    $endereco->orsdt_agenda = (empty($enderecoAgendamento->osadata))    ? '' : $data;
                    $endereco->orshr_agenda = (empty($enderecoAgendamento->osahora))    ? '' : '00:00:00';

                    if( !empty($enderecoAgendamento->cidade) && !empty($enderecoAgendamento->estado) ){
                        $resposta = $this->getCoordenadas($endereco);

                        if(empty($resposta->erro)) {
                            $enderecoAgendamento->osacoordx = $resposta->coordx;
                            $enderecoAgendamento->osacoordy = $resposta->coordy;
                            $enderecoAgendamento->geocoded  = true;
                        } else {
                            $enderecoAgendamento->osacoordx = 0;
                            $enderecoAgendamento->osacoordy = 0;
                            $enderecoAgendamento->geocoded  = false;
                        }
                    } else {
                        $enderecoAgendamento->osacoordx = 0;
                        $enderecoAgendamento->osacoordy = 0;
                        $enderecoAgendamento->geocoded  = false;
                    }

                } else {
                    $enderecoAgendamento->geocoded = true;
                }


            } else {

                $enderecoAgendamento = $this->dao->getEnderecoAgendamentoDirecionado($ordem, $data);
                $enderecoAgendamento->geocoded  = false;

            }

            $agedamentosGeocodificados[ $ordem->ordoid ] = $enderecoAgendamento;
        }

        return $agedamentosGeocodificados;
    }

    /**
     * @author Thiago Leal
     * @since 03/03/2017
     *
     * Define a rota baseada no array de agedamentos ordenaddos
     * Extraí do array de agendamentos geocodificados
     */
    private function definirRota($atendimentosOrdenados, $agedamentosGeocodificados){

        //Array que armazenara os ponto percorridos e geocodificados
        $pontosPercorridos = array();

        $chavePercorridos  = 0;

        //Adiciona o ponto de SAÍDA
        $pontosPercorridos[$chavePercorridos] = $this->locationStart;
        $pontosPercorridos[$chavePercorridos]->ordoid = 0;
        $pontosPercorridos[$chavePercorridos]->os_direcionada = 'f';
        $chavePercorridos++;


        //Adiciona as OS geocodificadas na ordem da Rota se foi geocodificada
        foreach ($atendimentosOrdenados as $chave => $atendimento) {

            $chaveAnterior = ($chavePercorridos > 0 ? $chavePercorridos-1 : 0);

            $pontosPercorridos[$chavePercorridos] = $agedamentosGeocodificados[$atendimento->ordoid];
            $pontosPercorridos[$chavePercorridos]->tipo         = $atendimento->tipo;
            $pontosPercorridos[$chavePercorridos]->hora         = $atendimento->hora;
            $pontosPercorridos[$chavePercorridos]->kilometragem = 0;
            $pontosPercorridos[$chavePercorridos]->custoPedagio = 0;
            $pontosPercorridos[$chavePercorridos]->ponto        = 'atendimento';
            $pontosPercorridos[$chavePercorridos]->os_direcionada = $atendimento->os_direcionada;
            $pontosPercorridos[$chavePercorridos]->deslocamento = self::DESLOCAMENTO_STATUS_OK;

            //Monta Histórico
            $historico = new stdClass();
            $historico->orsordoid    = $atendimento->ordoid;
            $historico->orsdt_agenda = $agedamentosGeocodificados[ $atendimento->ordoid ]->osadata;
            $historico->orshr_agenda = $agedamentosGeocodificados[ $atendimento->ordoid ]->osahora;
            $historico->orsstatus    = $this->idHistorico;

            //Valida se o ponto atual tem problema de coordenada
            if (  $pontosPercorridos[$chavePercorridos]->geocoded === FALSE && $pontosPercorridos[$chaveAnterior]->geocoded === TRUE)
            {
                $pontosPercorridos[$chavePercorridos]->deslocamento = self::DESLOCAMENTO_STATUS_ERRO_PONTO_ATUAL;

                if($pontosPercorridos[$chavePercorridos]->os_direcionada == 't'){
                    $historico->orssituacao  = "Ordem de Serviço sem deslocamento. Motivo: Ordem de Serviço direcionada.";
                } else {
                    $historico->orssituacao  = "Ordem de Serviço sem deslocamento. Motivo: Houve problema de geocodificação no endereço do atendimento.";
                }

                $retorno = $this->dao->gravarHistorico($historico);

                $chavePercorridos++;
                continue;
            }

            //Valida se o ponto anterior tem problema de coordenada
            if ( $pontosPercorridos[$chaveAnterior]->geocoded === FALSE )
            {
                $pontosPercorridos[$chavePercorridos]->deslocamento = self::DESLOCAMENTO_STATUS_ERRO_PONTO_ANTERIOR;

                if($pontosPercorridos[$chaveAnterior]->os_direcionada == 't'){
                    $historico->orssituacao  = "Ordem de Serviço sem deslocamento. Motivo: Ordem de Serviço anterior (".$pontosPercorridos[$chaveAnterior]->ordoid."). direcionada.";
                } else if ($pontosPercorridos[$chaveAnterior]->ordoid > 0) {
                    $historico->orssituacao  = "Ordem de Serviço sem deslocamento. Motivo: Houve problema de geocodificação no endereço do atendimento da O.S. anterior (".$pontosPercorridos[$chaveAnterior]->ordoid.").";
                } else {
                    $historico->orssituacao  = "Ordem de Serviço sem deslocamento. Motivo: Houve problema de geocodificação no endereço do ponto anterior.";
                }

                $retorno = $this->dao->gravarHistorico($historico);

                $chavePercorridos++;
                continue;
            }

            //Valida se não houve mudança de endereço
            if ($agedamentosGeocodificados[$atendimento->ordoid]->osacoordx    == $pontosPercorridos[$chaveAnterior]->osacoordx
                && $agedamentosGeocodificados[$atendimento->ordoid]->osacoordy == $pontosPercorridos[$chaveAnterior]->osacoordy )
            {
                $pontosPercorridos[$chavePercorridos]->deslocamento = self::DESLOCAMENTO_STATUS_ZERO;

                if($pontosPercorridos[$chaveAnterior]->os_direcionada == 't'){
                    $historico->orssituacao  = "Ordem de Serviço sem deslocamento. Motivo: Ordem de Serviço anterior (".$pontosPercorridos[$chaveAnterior]->ordoid."). direcionada.";
                } else if ($pontosPercorridos[$chaveAnterior]->ordoid > 0) {
                    $historico->orssituacao  = "Ordem de Serviço sem deslocamento. Motivo: Mesmo endereço de atendimento da O.S. anterior (".$pontosPercorridos[$chaveAnterior]->ordoid.").";
                } else {
                    $historico->orssituacao  = "Ordem de Serviço sem deslocamento. Motivo: Mesmo endereço de atendimento do ponto anterior.";
                }

                $retorno = $this->dao->gravarHistorico($historico);

            }

            $chavePercorridos++;
        }

        //Adiciona ponto de CHEGADA se foi geocodificada
        $chaveAnterior = ($chavePercorridos > 0 ? $chavePercorridos-1 : 0);

        $pontosPercorridos[$chavePercorridos] = $this->locationEnd;

        //O ponto de retorno utiliza os dados da OS anterior
        $pontosPercorridos[$chavePercorridos]->itlkm_abrangencia = $pontosPercorridos[$chaveAnterior]->itlkm_abrangencia;
        $pontosPercorridos[$chavePercorridos]->itlkm_litro       = $pontosPercorridos[$chaveAnterior]->itlkm_litro;
        $pontosPercorridos[$chavePercorridos]->ordoid            = $pontosPercorridos[$chaveAnterior]->ordoid;
        $pontosPercorridos[$chavePercorridos]->osadata           = $pontosPercorridos[$chaveAnterior]->osadata;
        $pontosPercorridos[$chavePercorridos]->osahora           = $pontosPercorridos[$chaveAnterior]->osahora;
        $pontosPercorridos[$chavePercorridos]->data              = $pontosPercorridos[$chaveAnterior]->data;
        $pontosPercorridos[$chavePercorridos]->tipo              = $pontosPercorridos[$chaveAnterior]->tipo;
        $pontosPercorridos[$chavePercorridos]->kilometragem      = 0;
        $pontosPercorridos[$chavePercorridos]->custoPedagio      = 0;
        $pontosPercorridos[$chavePercorridos]->os_direcionada    = $pontosPercorridos[$chaveAnterior]->os_direcionada;
        $pontosPercorridos[$chavePercorridos]->deslocamento      = self::DESLOCAMENTO_STATUS_OK;


        //Valida se o ponto de chegada está geocodificada
        if ( $pontosPercorridos[$chavePercorridos]->geocoded === FALSE &&  $pontosPercorridos[$chaveAnterior]->geocoded === TRUE  )
        {
            $pontosPercorridos[$chavePercorridos]->deslocamento = self::DESLOCAMENTO_STATUS_ERRO_PONTO_ATUAL;
            return $pontosPercorridos;
        }

        //Valida se a última OS da rota está geocodificada
        if ( $pontosPercorridos[$chavePercorridos]->geocoded === TRUE  &&  $pontosPercorridos[$chaveAnterior]->geocoded === FALSE )
        {
            $pontosPercorridos[$chavePercorridos]->deslocamento = self::DESLOCAMENTO_STATUS_ERRO_PONTO_ANTERIOR;
            return $pontosPercorridos;
        }

        //Valida se houve mudança de endereço
        if ($this->locationEnd->osacoordx    == $pontosPercorridos[$chaveAnterior]->osacoordx
            && $this->locationEnd->osacoordy == $pontosPercorridos[$chaveAnterior]->osacoordy)
        {
            $pontosPercorridos[$chavePercorridos]->deslocamento = self::DESLOCAMENTO_STATUS_ZERO;
        }

        return $pontosPercorridos;
    }

    /**
     * @author Thiago Leal
     * @since 03/03/2017
     *
     * Realiza o calculo de deslocamento e pedágio
     */
    private function calculaDeslocamento($pontosPercorridos) {

        //Array que armazenara os pontos percorridos e calculados
        $pontosCalculados = array();


        //Percorre a Rota ordenada e calcula os trechos
        foreach ($pontosPercorridos as $chave => $ponto) {

            //Apartir da primeira parada (Segundo ponto na rota)
            if ($chave >= 1 && $ponto->deslocamento == self::DESLOCAMENTO_STATUS_OK) {

                try {

                    if ( (( $pontosPercorridos[$chave-1]->osacoordx ==  $ponto->osacoordx )
                         && ( $pontosPercorridos[$chave-1]->osacoordy == $ponto->osacoordy))
                         || ($ponto->os_direcionada == 't')  ) {

                            $kmParcial = 0;
                            $ponto->kilometragem = 0;
                            $ponto->custoPedagio = 0;


                    } else {

                        $Route = new Route($this->conn);

                        $Route->setPontoParada('PONTO A', $pontosPercorridos[$chave-1]->osacoordx, $pontosPercorridos[$chave-1]->osacoordy );
                        $Route->setPontoParada('PONTO B', $ponto->osacoordx, $ponto->osacoordy );

                        //Inicio - [SES-19] Maplink SOAP to RESTFUL

                        //$retorno = $Route->getTotaisRota();
                        $retorno = $Route->getTotaisRotaTrip();

                        //Fim - [SES-19] Maplink SOAP to RESTFUL

                        if (!isset($retorno->totais->distanciaKM)) {
                            throw new Exception("Erro no Route do MapLink", 1);
                        }

                        $formatKm = str_replace(",","",$retorno->totais->distanciaKM);
                        $retorno->totais->distanciaKM = $formatKm;

                        $kmParcial = $retorno->totais->distanciaKM - $ponto->itlkm_abrangencia;
                        $ponto->kilometragem = ($kmParcial < 0 ? 0 : $kmParcial);
                        $ponto->custoPedagio = $retorno->totais->custoPedagio;
                    }


                } catch (Exception $e) {

                    $ponto->deslocamento = self::DESLOCAMENTO_STATUS_ERRO_ROTEAMENTO_MAPLINK;

                    //Gera log de erro
                    $this->registraLog($e->getMessage());

                    //Grava Histórico
                    $historico = new stdClass();
                    $historico->orsordoid    = $ponto->ordoid;
                    $historico->orsdt_agenda = $ponto->osadata;
                    $historico->orshr_agenda = $ponto->osahora;
                    $historico->orsstatus    = $this->idHistorico;
                    $historico->orssituacao = "Não foi possível calcular a distância e pedágios no trecho entre o endereço".
                                              (!empty($pontosPercorridos[$chave-1]->logradouro) ? " {$pontosPercorridos[$chave-1]->logradouro}" : "").
                                              (!empty($pontosPercorridos[$chave-1]->bairro) ? " - {$pontosPercorridos[$chave-1]->bairro}" : "").
                                              (!empty($pontosPercorridos[$chave-1]->cidade) ? " - {$pontosPercorridos[$chave-1]->cidade}" : "").
                                              (!empty($pontosPercorridos[$chave-1]->estado) ? " / {$pontosPercorridos[$chave-1]->estado}" : "").
                                              (!empty($pontosPercorridos[$chave-1]->cep)    ? " - {$pontosPercorridos[$chave-1]->cep}" : "").
                                              " e ".
                                              (!empty($ponto->logradouro) ? " {$ponto->logradouro}" : "").
                                              (!empty($ponto->bairro) ? " - {$ponto->bairro}" : "").
                                              (!empty($ponto->cidade) ? " - {$ponto->cidade}" : "").
                                              (!empty($ponto->estado) ? " / {$ponto->estado}" : "").
                                              (!empty($ponto->cep)    ? " - {$ponto->cep}" : "").

                                              " Motivo: Esta informação não encontra-se no sistema Maplink.";

                    $retorno = $this->dao->gravarHistorico($historico);

                }
            }


            $pontosCalculados[] = $ponto;
        }

        return $pontosCalculados;
    }

    /**
     * @author Thiago Leal
     * @since 03/03/2017
     *
     * Salva os custos de deslocamentos da Rota
     */
    private function salvaDeslocamentos($pontosCalculados, $idTecnico) {


        //Percorre a Rota ordenada e calculada dos trechos
        foreach ($pontosCalculados as $chave => $ponto) {

            //Apartir da primeira parada (Segundo ponto na rota)
            if ($chave >= 1) {

                //Inicia a transação por ponto de deslocamento
                $this->dao->beginTransaction();

                $ordemServico = new stdClass();
                $ordemServico->orddesloc_autoriz  = round($ponto->kilometragem);
                $ordemServico->orddesloc_pedagio  = $ponto->custoPedagio;
                $ordemServico->orddesloc_valorkm  = $ponto->itlkm_litro;
                $ordemServico->orddesloc_origem   = strtoupper($pontosCalculados[$chave-1]->uf.'/'. str_replace("'", ' ', $pontosCalculados[$chave-1]->cidade) );
                $ordemServico->orddesloc_destino  = strtoupper($ponto->uf.'/'. str_replace("'", ' ', $ponto->cidade));
                $ordemServico->ordoid             = $ponto->ordoid;
                $ordemServico->orddesloc_liberado = 'Automático';
                $ordemServico->retorno            = FALSE;

                //Último ponto é o retorno, soma e grava os custos na OS anterior
                if ($ponto->ponto == 'chegada') {

                    $ponto->cep = preg_replace('/\D/', '', $ponto->cep);

                    $ordemServico->ordoid                    = $pontosCalculados[$chave-1]->ordoid;
                    $ordemServico->orddesloc_autoriz         = round($ponto->kilometragem) + round($pontosCalculados[$chave-1]->kilometragem);
                    $ordemServico->orddesloc_pedagio         = $ponto->custoPedagio + $pontosCalculados[$chave-1]->custoPedagio;
                    $ordemServico->retorno                   = TRUE;

                }

                $retorno = $this->dao->atualizarOrdemServico($ordemServico);

                if(!$retorno->status){

                    //Rollback no ponto de deslocamento
                    $this->dao->rollbackTransaction();

                    //Gera log de erro
                    $this->registraLog($retorno->msg);

                    continue;
                }

                //CI=Comissao Instalacao | VI=Visita Improdutiva
                if ($ponto->tipo == "CI") {

                    $comissaoInstalacao = new stdClass();
                    $comissaoInstalacao->cmideslocamento         = round($ponto->kilometragem);
                    $comissaoInstalacao->cmivalor_pedagio        = $ponto->custoPedagio;
                    $comissaoInstalacao->cmivl_unit_deslocamento = $ponto->itlkm_litro;
                    $comissaoInstalacao->cmiord_serv             = $ponto->ordoid;
                    $comissaoInstalacao->cmiitloid               = $idTecnico;
                    $comissaoInstalacao->data                    = $ponto->data;
                    $comissaoInstalacao->ponto                   = 'atendimento';
                    $comissaoInstalacao->cmidesloc_status        = $ponto->deslocamento;

                    //Se for a Primeira OS grava o endereço do ponto de Saída
                    if ($chave == 1) {

                        $comissaoInstalacao->ponto           = 'partida';
                        $pontosCalculados[$chave-1]->cep     = preg_replace('/\D/', '', $pontosCalculados[$chave-1]->cep);
                        $comissaoInstalacao->cmidesloc_saida = wordwrap($pontosCalculados[$chave-1]->logradouro, 50, "<br>", TRUE).
                                                              '<br>'.$pontosCalculados[$chave-1]->cidade.
                                                              ' - '.$pontosCalculados[$chave-1]->uf.
                                                              '<br>'.$this->mask(str_pad($pontosCalculados[$chave-1]->cep, 8, "0", STR_PAD_LEFT), '#####-###');

                        $comissaoInstalacao->cmidesloc_saida = str_replace("'", ' ', $comissaoInstalacao->cmidesloc_saida);
                        $comissaoInstalacao->cmideslocamento         = round($ponto->kilometragem);
                        $comissaoInstalacao->cmivalor_pedagio        = $ponto->custoPedagio;
                        $comissaoInstalacao->cmivl_unit_deslocamento = $ponto->itlkm_litro;
                    }

                    //Último ponto é o retorno, grava os custos de deslocamento na OS anterior
                    if ($ponto->ponto == 'chegada') {

                        $comissaoInstalacao->ponto                     = 'chegada';
                        $comissaoInstalacao->cmiord_serv               = $pontosCalculados[$chave-1]->ordoid;
                        $comissaoInstalacao->cmidesloc_status_chegada  = $ponto->deslocamento;
                        $comissaoInstalacao->cmidesloc_pedagio_chegada = $ponto->custoPedagio;
                        $comissaoInstalacao->cmidesloc_km_chegada      = round($ponto->kilometragem);
                        $comissaoInstalacao->cmidesloc_chegada         = wordwrap($ponto->logradouro, 50, "<br>", TRUE).
                                                                        '<br>'.$ponto->cidade.
                                                                        ' - ' .$ponto->uf.
                                                                        '<br>'.$this->mask(str_pad($ponto->cep, 8, "0", STR_PAD_LEFT), '#####-###');

                        $comissaoInstalacao->cmidesloc_chegada         = str_replace("'", ' ', $comissaoInstalacao->cmidesloc_chegada);
                    }

                    $comissaoInstalacaoAnterior = $this->dao->buscaComissaoInstalacao($comissaoInstalacao);

                    $comissaoInstalacao->cmioid = $comissaoInstalacaoAnterior->cmioid;

                    $retorno = $this->dao->atualizarComissaoInstalacao($comissaoInstalacao);

                    if(!$retorno->status) {

                        //Rollback no ponto de deslocamento
                        $this->dao->rollbackTransaction();

                        //Gera log de erro
                        $this->registraLog($retorno->msg);

                        continue;
                    }

                    //Se não alterou nenhuma linha, é pq teve lançamento manual, sendo assim, vai para o proximo ponto sem gerar historico
                    if($retorno->nr_linhas_afetadas > 0) {

                        //Grava Histórico
                        $historico = new stdClass();
                        $historico->orsordoid    = $ponto->ordoid;
                        $historico->orsdt_agenda = $ponto->osadata;
                        $historico->orshr_agenda = $ponto->osahora;
                        $historico->orsstatus    = $this->idHistorico;

                        //Se for o ultimo ponto gera histórico de chegada
                        if ($ponto->ponto == 'chegada') {
                            $historico->orssituacao  = "O sistema alterou os dados de deslocamento e pedágio de chegada para comissão, de:
                                                    ".round($comissaoInstalacaoAnterior->cmideslocamento)." KM, Pedágio R$ {$comissaoInstalacaoAnterior->cmivalor_pedagio}
                                                    Para:
                                                    ".round($comissaoInstalacao->cmideslocamento)." KM, Pedágio R$ {$comissaoInstalacao->cmivalor_pedagio}";
                        } else {
                            $historico->orssituacao  = "O sistema alterou os dados de deslocamento e pedágio para comissão, de:
                                                    ".round($comissaoInstalacaoAnterior->cmideslocamento)." KM, Pedágio R$ {$comissaoInstalacaoAnterior->cmivalor_pedagio}
                                                    Para:
                                                    ".round($comissaoInstalacao->cmideslocamento)." KM, Pedágio R$ {$comissaoInstalacao->cmivalor_pedagio}";
                        }


                        $retorno = $this->dao->gravarHistorico($historico);
                    }


                } elseif ($ponto->tipo == "VI") {

                    $visitaImprodutiva = new stdClass();
                    $visitaImprodutiva->ovivalor_pedagio   = $ponto->custoPedagio;
                    $visitaImprodutiva->oviquantidade_km   = round($ponto->kilometragem);
                    $visitaImprodutiva->data               = $ponto->data;
                    $visitaImprodutiva->oviordoid          = $ponto->ordoid;
                    $visitaImprodutiva->oviitloid          = $idTecnico;
                    $visitaImprodutiva->ponto              = 'atendimento';
                    $visitaImprodutiva->ovivalorpor_km     = $ponto->itlkm_litro;
                    $visitaImprodutiva->ovidesloc_status   = $ponto->deslocamento;

                    //Se for a Primeira OS grava o endereço do ponto de Saída
                    if ($chave == 1) {

                        $visitaImprodutiva->ponto           = 'partida';
                        $pontosCalculados[$chave-1]->cep    = preg_replace('/\D/', '', $pontosCalculados[$chave-1]->cep);
                        $visitaImprodutiva->ovidesloc_saida = wordwrap($pontosCalculados[$chave-1]->logradouro, 50, "<br>", true).
                                                              '<br>'.$pontosCalculados[$chave-1]->cidade.
                                                              ' - '.$pontosCalculados[$chave-1]->uf.
                                                               '<br>'.$this->mask(str_pad($pontosCalculados[$chave-1]->cep, 8, "0", STR_PAD_LEFT), '#####-###');

                        $visitaImprodutiva->ovidesloc_saida = str_replace("'", ' ', $visitaImprodutiva->ovidesloc_saida);
                        $visitaImprodutiva->ovivalor_pedagio   = $ponto->custoPedagio;
                        $visitaImprodutiva->oviquantidade_km   = round($ponto->kilometragem);
                        $visitaImprodutiva->ovivalorpor_km     = $ponto->itlkm_litro;
                    }


                    //Último ponto é o retorno, grava os custos de deslocamento na OS anterior
                    if ($ponto->ponto == 'chegada') {

                        $visitaImprodutiva->ponto                     = 'chegada';
                        $visitaImprodutiva->oviordoid                 = $pontosCalculados[$chave-1]->ordoid;
                        $visitaImprodutiva->ovivalor_pedagio          = $pontosCalculados[$chave-1]->custoPedagio;
                        $visitaImprodutiva->oviquantidade_km          = round($pontosCalculados[$chave-1]->kilometragem);
                        $visitaImprodutiva->ovidesloc_status_chegada  = $ponto->deslocamento;
                        $visitaImprodutiva->ovidesloc_pedagio_chegada = $ponto->custoPedagio;
                        $visitaImprodutiva->ovidesloc_km_chegada      = round($ponto->kilometragem);
                        $visitaImprodutiva->ovidesloc_chegada         = wordwrap($ponto->logradouro, 50, "<br>", true).
                                                                     '<br>'.$ponto->cidade.
                                                                     ' - ' .$ponto->uf.
                                                                     '<br>'.$this->mask(str_pad($ponto->cep, 8, "0", STR_PAD_LEFT), '#####-###');

                        $visitaImprodutiva->ovidesloc_chegada         = str_replace("'", ' ', $visitaImprodutiva->ovidesloc_chegada);
                    }

                    $visitaImprodutivaAnterior = $this->dao->buscaVisitaImprodutiva($visitaImprodutiva);

                    $visitaImprodutiva->ovioid = $visitaImprodutivaAnterior->ovioid;

                    $retorno = $this->dao->atualizarVisitaImprodutiva($visitaImprodutiva);

                    if(!$retorno->status) {

                        //Rollback no ponto de deslocamento
                        $this->dao->rollbackTransaction();

                        //Gera log de erro
                        $this->registraLog($retorno->msg);

                        continue;
                    }

                    //Se não alterou nenhuma linha, é pq teve lançamento manual, sendo assim, vai para o proximo ponto sem gerar historico
                    if($retorno->nr_linhas_afetadas > 0) {                       

                        //Grava Histórico
                        $historico = new stdClass();
                        $historico->orsordoid    = $ponto->ordoid;
                        $historico->orsdt_agenda = $ponto->osadata;
                        $historico->orshr_agenda = $ponto->osahora;
                        $historico->orsstatus    = $this->idHistorico;

                        //Se for o ultimo ponto gera histórico de chegada
                        if ($ponto->ponto == 'chegada') {
                            $historico->orssituacao  = "O sistema alterou os dados de deslocamento e pedágio de chegada para visita improdutiva, de:
                                                    ".round($visitaImprodutivaAnterior->oviquantidade_km)." KM, Pedágio R$ {$visitaImprodutivaAnterior->ovivalor_pedagio}
                                                    Para:
                                                    ".round($visitaImprodutiva->oviquantidade_km)." KM, Pedágio R$ {$visitaImprodutiva->ovivalor_pedagio}";
                        } else {
                            $historico->orssituacao  = "O sistema alterou os dados de deslocamento e pedágio para visita improdutiva, de:
                                                    ".round($visitaImprodutivaAnterior->oviquantidade_km)." KM, Pedágio R$ {$visitaImprodutivaAnterior->ovivalor_pedagio}
                                                    Para:
                                                    ".round($visitaImprodutiva->oviquantidade_km)." KM, Pedágio R$ {$visitaImprodutiva->ovivalor_pedagio}";
                        }

                        $retorno = $this->dao->gravarHistorico($historico);
                    }


                    $retorno = $this->dao->atualizarComissaoVisitaImprodutiva($visitaImprodutiva);

                    if(!$retorno->status) {

                        //Rollback no ponto de deslocamento
                        $this->dao->rollbackTransaction();

                        //Gera log de erro
                        $this->registraLog($retorno->msg);

                        continue;
                    }                    

                }

                //Finaliza a transação por ponto de deslocamento
                $this->dao->commitTransaction();
            }
        }
    }


    /**
     * @author Thiago Leal
     * @since 03/03/2017
     *
     * Encapsula a Consulta no AddressFinder do MapLink
     */
    private function getCoordenadas($endereco, $isStartEnd = false, $startEnd = null){

        $this->AddressFinder->setLogradouro( $endereco->logradouro );
        $this->AddressFinder->setCep( $endereco->cep );
        $this->AddressFinder->setBairro( $endereco->bairro );
        $this->AddressFinder->setCidade( $endereco->cidade );
        $this->AddressFinder->setEstado( $endereco->estado );
        
        //Inicio - [SES-19] Maplink SOAP to RESTFUL
        
        //$resposta = $this->AddressFinder->getCoordenadas();
        $resposta = $this->AddressFinder->getCoordenadasTrip();
        
        //Fim - [SES-19] Maplink SOAP to RESTFUL


        if(isset($resposta->erro)) {

            $this->registraLog("getXY: Falha ao efetuar geocode [".
                "Logradouro => " . $endereco->logradouro .
                " | Bairro => " . $endereco->bairro .
                " | Cidade => " . $endereco->cidade .
                " | Estado => " . $endereco->estado .
                " | Cep => "    . $endereco->cep ."]");


            $historico = new stdClass();
            $historico->orsordoid    = $endereco->orsordoid;
            $historico->orsdt_agenda = $endereco->orsdt_agenda;
            $historico->orshr_agenda = $endereco->orshr_agenda;
            $historico->orsstatus    = $this->idHistorico;

            if ($isStartEnd) {
                $historico->orssituacao  = "Não foi possível determinar o local de $startEnd do Prestador no Maplink, o cálculo de deslocamento e pedágio
                                        será realizado pelo centro da cidade.  Endereço pesquisado:".
                                        (!empty($endereco->logradouro) ? " {$endereco->logradouro}" : "").
                                        (!empty($endereco->bairro) ? " - {$endereco->bairro}" : "").
                                        (!empty($endereco->cidade) ? " - {$endereco->cidade}" : "").
                                        (!empty($endereco->estado) ? " / {$endereco->estado}" : "").
                                        (!empty($endereco->cep)    ? " - {$endereco->cep}" : "");
            } else {
                $historico->orssituacao  = "Não foi possível determinar o local de atendimento no Maplink, o cálculo de deslocamento e pedágio
                                        será realizado pelo centro da cidade.  Endereço pesquisado:".
                                        (!empty($endereco->logradouro) ? " {$endereco->logradouro}" : "").
                                        (!empty($endereco->bairro) ? " - {$endereco->bairro}" : "").
                                        (!empty($endereco->cidade) ? " - {$endereco->cidade}" : "").
                                        (!empty($endereco->estado) ? " / {$endereco->estado}" : "").
                                        (!empty($endereco->cep)    ? " - {$endereco->cep}" : "");
            }

            $historico->orssituacao = str_replace("'", ' ', $historico->orssituacao);

            $retorno = $this->dao->gravarHistorico($historico);


            //Faz uma nova tentativa buscando pelo centro da cidade
            $this->AddressFinder->setLogradouro( "Rua" );
            $this->AddressFinder->setCep( null );
            $this->AddressFinder->setBairro( "Centro" );
            $this->AddressFinder->setCidade( $endereco->cidade );
            $this->AddressFinder->setEstado( $endereco->estado );

            //Inicio - [SES-19] Maplink SOAP to RESTFUL
            
            //$resposta = $this->AddressFinder->getCoordenadas();
            $resposta = $this->AddressFinder->getCoordenadasTrip();
            
            //Fim - [SES-19] Maplink SOAP to RESTFUL


            if(isset($resposta->erro)) {

                $this->registraLog("getXY: Falha ao efetuar geocode [".
                "Logradouro => Rua" .
                " | Bairro => Centro" .
                " | Cidade => " . $endereco->cidade .
                " | Estado => " . $endereco->estado .
                " | Cep => NULL ]");

                $historico = new stdClass();
                $historico->orsordoid    = $endereco->orsordoid;
                $historico->orsdt_agenda = $endereco->orsdt_agenda;
                $historico->orshr_agenda = $endereco->orshr_agenda;
                $historico->orsstatus    = $this->idHistorico;

                if ($isStartEnd) {
                    $historico->orssituacao  = "Não foi possível determinar o local de $startEnd do PS no Maplink para calcular deslocamento e pedágio.
                                                Endereço pesquisado: ".
                                                (!empty($endereco->logradouro) ? " {$endereco->logradouro}" : "").
                                                (!empty($endereco->bairro) ? " - {$endereco->bairro}" : "").
                                                (!empty($endereco->cidade) ? " - {$endereco->cidade}" : "").
                                                (!empty($endereco->estado) ? " / {$endereco->estado}" : "").
                                                (!empty($endereco->cep)    ? " - {$endereco->cep}" : "");
                } else {
                    $historico->orssituacao  = "Não foi possível determinar o local de atendimento no Maplink para calcular deslocamento e pedágio
                                                Endereço pesquisado: ".
                                                (!empty($endereco->logradouro) ? " {$endereco->logradouro}" : "").
                                                (!empty($endereco->bairro) ? " - {$endereco->bairro}" : "").
                                                (!empty($endereco->cidade) ? " - {$endereco->cidade}" : "").
                                                (!empty($endereco->estado) ? " / {$endereco->estado}" : "").
                                                (!empty($endereco->cep)    ? " - {$endereco->cep}" : "");
                }

                $historico->orssituacao = str_replace("'", ' ', $historico->orssituacao);

                $retorno = $this->dao->gravarHistorico($historico);

            }
        }

        return $resposta;
    }


    /**
     * @author Thiago Leal
     * @since 03/03/2017
     *
     * Informações a serem gravadas no arquivo no log
     */
    private function registraLog($texto){

        //diretorio do arquivo a ser gravado
        $insertdir = '/var/www/docs_temporario/';

        //gera e escreve o arquivo
        $fp = fopen($insertdir."log_erro_cron_calculo_deslocamento_".date("Ymd").".txt", "a+");

        $textoLog = date("H:i:s")."\n";
        $textoLog .= utf8_encode($texto)."\n";
        $textoLog .= "____________________________________________________________________________ \n";

        $escreve = fwrite($fp, $textoLog);

        // Fecha o arquivo
        fclose($fp);
    }

    /**
     * @author Thiago Leal
     * @since 14/03/2017
     *
     * Retorna um array com o dia da semana em português e inglês
     * caso seja o mesmo endereço para todos os dias, o OFSC retorna ALL
     */
    private function retornaDiaSemana($diaSemana) {

        $retorno = array();

        switch ($diaSemana) {
            case 0:
                $retorno = array('sun','dom','all');
                break;
            case 1:
                $retorno = array('mon','seg','all');
                break;
            case 2:
                $retorno = array('tue','ter','all');
                break;
            case 3:
                $retorno = array('wed','qua','all');
                break;
            case 4:
                $retorno = array('thu','qui','all');
                break;
            case 5:
                $retorno = array('fri','sex','all');
                break;
            case 6:
                $retorno = array('sat','sab','all');
                break;
         }

        return $retorno;
    }

    /**
     * Método que aplica qualquer máscara
     *
     * @return mask
     */
    public function mask($val, $mask) {

        $maskared = '';
        $k = 0;

        for($i = 0; $i<=strlen($mask)-1; $i++) {

            if($mask[$i] == '#') {
                if(isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }

}
?>
