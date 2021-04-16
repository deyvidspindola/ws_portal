<?php

/**
 * Classe Middleware Inbound.
 * Camada de regra de negócio.
 *
 * @package  SmartAgenda
 * @author   LUIZ FERNANDO PONTARA <fernandopontara@brq.com>
 *
 */

ini_set ( "log_errors", 1 );
ini_set ( 'error_log', '/tmp/smart_agenda_inbound_' . date ( 'd-m-Y' ) . '.txt' ); 
 
//Classe Middleware Smart Agenda
require_once _MODULEDIR_ ."/SmartAgenda/Action/SmartAgenda.php";

class Inbound {


    const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

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

    public function __construct(){
        $this->smartAgenda = new SmartAgenda();
        $this->smartAgenda->API = 'INBOUND';

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

    public function entrada(){
        
        $retorno = array();

        try{

            //busca dados de acesso para a tag user
            $envio = $this->smartAgenda->getAutenticacao();

            //adiciona parametros no array
            if(!empty($this->processing_mode)){
                $envio['head']['processing_mode'] = $this->processing_mode;
            }

            if(!empty($this->upload_type)){
                $envio['head']['upload_type'] = $this->upload_type;
            }

            if(!empty($this->id)){
                $envio['head']['id'] = $this->id;
            }

            if(!empty($this->date)){
                $envio['head']['date'] = $this->date;
            }

            if(!empty($this->allow_change_date)){
                $envio['head']['allow_change_date'] = $this->allow_change_date;
            }

            if(!empty($this->upload_type)){
                $envio['head']['upload_type'] = $this->upload_type;
            }

            if(!empty($this->appointmentKeysField)){
                $envio['head']['appointment']['keys']['field'] = $this->appointmentKeysField;
            }

            if(!empty($this->appointmentActionIfCompleted)){
                $envio['head']['appointment']['action_if_completed'] = $this->appointmentActionIfCompleted;
            }

            if(!empty($this->inventoryKeysField)){
                $envio['head']['inventory']['keys']['field'] = $this->inventoryKeysField;
            }

            if(!empty($this->inventoryUploadType)){
                $envio['head']['inventory']['upload_type'] = $this->inventoryUploadType;
            }

            if(!empty($this->properties_mode)){
                $envio['head']['properties_mode'] = $this->properties_mode;
            }
            
            if(!empty($this->provider_group)){
                $envio['head']['provider_group'] = $this->provider_group;
            }

            if(!empty($this->default_appointment_pool)){
                $envio['head']['default_appointment_pool'] = $this->default_appointment_pool;
            }

            if(!empty($this->commands)){
                $envio['data']['commands']['command'] = $this->commands;
            }



            //gera xml do envio (para realizar testes)
            $xmlEnvio = $this->smartAgenda->gerarXml($envio, 'inbound_interface');
            //echo htmlspecialchars($xmlEnvio);

            $ws = $this->smartAgenda->startWebService();

            //se conectar no webservice
            if($ws['status'] == 'ok'){

                try{
                    // Timestamp request
                    $timestampRequest = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 8);

                    //chama método do webservice
                    $resposta = $ws['client']->inbound_interface($envio);

                    // STI 85957 - Vinicius
                    $xmlResponse = (string) $this->smartAgenda->gerarXml( $resposta, 'inbound_interface');

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
                    $retorno['resultado'] = FALSE;                   
                    return $retorno;
                }

            }

            //retorno do webservice
            $retorno = $resposta;

            //remove tags que não serão úteis
            unset($retorno->user);
            unset($retorno->head);

        } catch (Exception $e) {

            $retorno['error_msg'] = self::MENSAGEM_ERRO_PROCESSAMENTO;
	    $retorno['resultado'] = FALSE;
	    return $retorno;
        }

        return $this->trataRetorno($retorno);
    }

    public function trataRetorno($retorno)
    {
        $erros = 0;
        $comandos = array();
        
        if( isset($retorno->data->commands->command) 
            && is_array($retorno->data->commands->command)){

            foreach ($retorno->data->commands->command as $chave => $valor) {
                if (isset($valor->appointment->report->message) 
                    && is_array($valor->appointment->report->message)) {
                    // Valor padr�o para o comando
                    $comando = array(
                        'resultado' => false,
                        'erros' => array(),
                        'aid' => null
                    );

                    // verifica se possui erros
                    foreach ($valor->appointment->report->message as $status) {
                        if ($status->result === "error"){
                            $comando['erros'][$status->code] = $status->description;
                        }
                    }
                    
                    // Verifica se encontrou algum erro
                    if (!count($comando['erros'])) {
                        $comando['resultado'] = true;
                        $comando['aid'] = $valor->appointment->aid;
                    } else {
                        $erros++;
                    }
                    
                    // Adiciona a lista de comandos
                    $comandos[$chave] = $comando;
                }
            }
        }
        
        if (isset($retorno->report->message)) {
            // Valor padr�o para o comando
            $comando = array(
                'resultado' => false,
                'erros' => array(),
                'aid' => null
            );

            // verifica se possui erros
            foreach ($retorno->report->message as $status) {
                if ($status->result === "error"){
                    $comando['erros'][$status->code] = $status->description;
                }
            }

            // Verifica se encontrou algum erro
            if (!count($comando['erros'])) {
                $comando['resultado'] = true;
                $comando['aid'] = $valor->appointment->aid;
            } else {
                $erros++;
            }
            // Adiciona a lista de comandos
            $comandos[$chave] = $comando;
        }
        return array(
            'resultado' => ($erros == 0),
            'command'   => $comandos
        );
    }
}
