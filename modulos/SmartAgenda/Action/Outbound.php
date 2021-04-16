<?php

/**
 * Classe Middleware Inbound.
 * Camada de regra de negócio.
 *
 * @package  SmartAgenda
 * @author   Vinicius Senna <vsenna@brq.com>
 *
 */

//Classe Middleware Smart Agenda
require_once _MODULEDIR_ ."/SmartAgenda/Action/SmartAgenda.php";
require_once _MODULEDIR_ ."/SmartAgenda/DAO/OutboundDAO.php";

class Outbound {


    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";
    const MENSAGEM_ERRO_ARRAY_LOTE = "Sem mensagens para envio em lote.";

    private $smartAgenda;
    private $processing_mode;
    private $upload_type = 'incremental';
    private $id;
    private $date;
    private $allow_change_date;
    private $appointmentKeysField = array('appt_number');
    private $appointmentointmentActionIfCompleted;
    private $inventoryKeysField = array('invsn');
    private $inventoryUploadType;
    private $properties_mode = 'update';
    private $provider_group;
    private $default_appointment_pool;
    private $commands = array();
    private $userID;
    private $messageId;
    private $messageStatus;
    private $externalId;
    private $description;
    private $data;



    public function __construct(){
        $this->smartAgenda = new SmartAgenda();
        $this->smartAgenda->API = 'OUTBOUND';
    }

    public function __set($nome, $valor){
        $this->$nome = $valor;
    }

    public function __get($nome){
        return $this->$nome;
    }

    public function setAppointmentKeysField($valor){
        array_push($this->appointmentKeysField, $valor);
    }

    public function setInventoryKeysField($valor){
        array_push($this->inventoryKeysField, $valor);
    }

    public function setCommands($valor){
        array_push($this->commands, $valor);
    }

    public function setUsuario($userID) {
        $this->userID = (int) $userID;
    }

    public function getUsuario() {
        return is_null($this->userID) ? 2750 : $this->userID;
    }

    public function setMessageId($mId) {
        $this->messageId = $mId;
    }

    public function getMessageId() {
        return $this->messageId;
    }

    public function getExternalId() {
        return $this->externalId;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getData(){
       return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setDescription($description){
        $this->description = $description;
    }

    public function setExternalId($externalId){
        $this->externalId = $externalId;
    }

    public function setStatus($status) {
        $this->messageStatus = $status;
    }

    public function getStatus() {
        return $this->messageStatus;
    }

    public function entrada(){

        $retorno = array();

        try{

            //busca dados de acesso para a tag user
            $envio = $this->smartAgenda->getAutenticacao();

            //adiciona parametros no array
            if(!is_null($this->getMessageId())){
                $envio['messages']['message']['message_id'] = $this->getMessageId();
            }

            if(!is_null($this->getStatus())){
                 $envio['messages']['message']['status'] = $this->getStatus();
            }

            if(!is_null($this->getExternalId())){
                 $envio['messages']['message']['external_id'] = $this->getExternalId();
            }

            if(!is_null($this->getDescription())){
                 $envio['messages']['message']['description'] = $this->getDescription();
            }

             if(!is_null($this->getData())){
                 $envio['messages']['message']['data'] = $this->getData();
            }

            $xmlEnvio = $this->smartAgenda->gerarXml( $envio, 'set_message_status');

            $ws = $this->smartAgenda->startWebService();

            //se conectar no webservice
            if($ws['status'] == 'ok'){

                try{
                    // Timestamp request
                    $timestampRequest = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 8);

                    //chama método do webservice
                    $resposta = $ws['client']->set_message_status($envio);

                    // STI 85957
                    $xmlResponse = (string) $this->smartAgenda->gerarXml( $resposta, 'set_message_status');
                    $this->smartAgenda->gravaLogComunicacao(
                        $this->smartAgenda->API,
                        $this->smartAgenda->formatXml($xmlEnvio),
                        $this->smartAgenda->formatXml($xmlResponse),
                        $timestampRequest,
                        $this->getUsuario()
                    );

                } catch (SoapFault $ex) {

                    $retorno['fault']     = $ex;
                    $retorno['error_msg'] = self::MENSAGEM_ERRO_PROCESSAMENTO;

                    return (object)$retorno;
                }

            }

            //retorno do webservice
            $retorno = $resposta;

            //remove tags que não serão úteis
            unset($retorno->user);
            unset($retorno->head);

        } catch (Exception $e) {

            $retorno['error_msg'] = self::MENSAGEM_ERRO_PROCESSAMENTO;
        }

        return $retorno;
    }

    public function retornoLote($mensagens) {

        $retorno = array();

        try {

            if(!is_array($mensagens) || count($mensagens) == 0) {
                throw new exception (self::MENSAGEM_ERRO_ARRAY_LOTE);
            }

            //busca dados de acesso para a tag user
            $envio = $this->smartAgenda->getAutenticacao();

            $ws = $this->smartAgenda->startWebService();

            //se conectar no webservice
            if($ws['status'] == 'ok'){

                try{

                    $envio['messages']['message'] = $mensagens;

                    //gera xml do envio (para realizar testes)
                    $xmlEnvio = $this->smartAgenda->gerarXml($envio,'set_message_status');

                    // Timestamp request
                    $timestampRequest = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 8);

                    //chama método do webservice
                    $resposta = $ws['client']->set_message_status($envio);

                    // STI 85957
                    $xmlResponse = (string) $this->smartAgenda->gerarXml( $resposta, 'set_message_status');
                    $this->smartAgenda->gravaLogComunicacao(
                        $this->smartAgenda->API,
                        $this->smartAgenda->formatXml($xmlEnvio),
                        $this->smartAgenda->formatXml($xmlResponse),
                        $timestampRequest,
                        $this->getUsuario()
                    );

                } catch (SoapFault $ex) {

                    $retorno['fault']     = $ex;
                    $retorno['error_msg'] = self::MENSAGEM_ERRO_PROCESSAMENTO;

                    return (object)$retorno;
                }

            }

            $retorno = $resposta;
            unset($retorno->user);
            unset($retorno->head);

        } catch (Exception $e) {
            $retorno['error_msg'] = $e->getMessage();
        }

        return $retorno;
    }

}
