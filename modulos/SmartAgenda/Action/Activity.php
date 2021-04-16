<?php

/**
 * Classe Middleware Activity.
  * @author Andre L. Zilz
 */
ini_set ( "log_errors", 1 );
ini_set ( 'error_log', '/tmp/smart_agenda_activity_' . date ( 'd-m-Y' ) . '.txt' );

require_once _MODULEDIR_ . 'SmartAgenda/Action/IntegradorSistemas.php';

class Activity extends IntegradorSistemas {

    private $parametros;

    public function __construct(){

        $this->setAPI('ACTIVITY');
        $this->parametros = new stdClass();
        parent::__construct();
    }

    public function setActivityId($activityId){
        $this->parametros->activityId = $activityId;
    }

    public function setLinkedActivityId($linkedActivityId){
        $this->parametros->linkedActivityId = $linkedActivityId;
    }

    public function setLinkType($linkType){
        $this->parametros->linkType = $linkType;
    }

    public function setPropertyLabel($propertyLabel){
        $this->parametros->propertyLabel = $propertyLabel;
    }

    public function consultarAtividade(){

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'GET', 'REST_GET_ACTIVITY', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function atualizarAtividade(){

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'PATCH', 'REST_UPDATE_ACTIVITY', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function cancelarAtividade(){

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'POST', 'REST_CANCEL_ACTIVITY', $this->parametros, array(), array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }
        return $retorno;
    }

    public function cancelarRelacionamentoAtividade(){

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'DELETE', 'REST_DELETE_LINK', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function getFile(){

        $retorno = new stdClass();
        $opcoesCurl = array(
                CURLOPT_BINARYTRANSFER => true
            );

        try{
            $retorno = $this->executarREST( 'GET', 'REST_GET_FILE', $this->parametros, $opcoesCurl );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }


}

?>
