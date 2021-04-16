<?php
/**
 * Classe API Resource.
 * @author Andre L. Zilz
 */

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/smart_agenda_resource_'.date('d-m-Y').'.txt');


require_once _MODULEDIR_ . 'SmartAgenda/Action/IntegradorSistemas.php';

class Resource extends IntegradorSistemas {

    private $parametros;

    public function __construct(){

        $this->setAPI('RESOURCE');
        $this->parametros = new stdClass();
        parent::__construct();

    }

    public function setIdRecurso($resourceId){
        $this->parametros->resourceId = $resourceId;
    }

    public function setIdLocal($locationId){
        $this->parametros->locationId = $locationId;
    }

    public function getResource(){

        $retorno = new stdClass();

        try{

            $retorno = $this->executarREST( 'GET', 'REST_GET_RESOURCE', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function createResource() {

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'PUT', 'REST_CREATE_RESOURCE', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function updateResource() {

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'PATCH', 'REST_UPDATE_RESOURCE', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function getResourceLocation() {

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'GET', 'REST_GET_LOCATION', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function getAssignedLocations() {

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'GET', 'REST_GET_ASSIGNED_LOCATIONS', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

}

?>