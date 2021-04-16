<?php
require_once _CRONDIR_      .   'lib/validaCronProcess.php';
require_once _MODULEDIR_    .   'Cron/Action/CronAction.php';
require_once _SITEDIR_      .   'webservice/skywave/action/API.php';

/**
 * @author 	Douglas M Cordeiro  
 * @version 27/06/2018
 */
class SatelliteAntennaActivation extends CronAction {

    private $config;
    private $skywave;
    private $serialTest;
    private $limitSearch;
    private $gatewayAccountID;
    private $antennaActivationTypes;

    private function loadConfig(){
        $this->config = array();        
        foreach($this->dao->getConfig() as $configuration){
            $this->config[$configuration->key] = $configuration->value;
        }
        foreach($this->dao->getStatus() as $configuration){
            $this->config[$configuration->status] = intval($configuration->id);
        }
    }

    private function config($id){
        return (isset($this->config[$id])) ? $this->config[$id] : "";
    }

    public function setConfiguration(SatelliteAntennaDAO $dao) {
        $this->dao = $dao;
        $this->dao->transactionBegin();
        try{
            $this->loadConfig();
            $this->serialTest = $this->parseSeparatedItem(',', $this->config("ANTENAS_SERIAL_TESTE"));
            $this->antennaActivationTypes = $this->config("ANTENAS_QUE_PRECISAM_ATIVACAO");
            $this->gatewayAccountID = $this->config("GATEWAY_ACCOUNT_ID");
        }catch (Exception $e) {
            echo $e->getMessage();
        }
        $this->dao->transactionCommit();
    }

    private function getSkywaveAntennaUnit($antennaUnit){
        $antennaData = array();
        if( ($this->limitSearch && array_search($antennaUnit->asatno_serie,$this->serialTest) !== false) || (!$this->limitSearch)){
            $antennaData["MobileID"] = $antennaUnit->asatno_serie;
            $antennaData["GatewayAccountID"] = ($antennaUnit->asvgateway != '') ? $antennaUnit->asvgateway : intval($this->gatewayAccountID);
            return $antennaData;
        }        
    }

    private function updateAntenna($antennaUnit, $status, $enable){
        $statusSkywave = ( $status == '' )? ( $enable ? ACTIVE : INACTIVE ) : $status ;
        $this->dao->transactionBegin();
        try{
            $this->dao->setAntennaActivationStatus( ( is_array($antennaUnit) ? $antennaUnit["MobileID"]: $antennaUnit->MobileID ), $statusSkywave);
        } catch (Exception $e) {
            $this->log('W',"Antenna {$antennaUnit->MobileID} - database rollback: {$e}");
            $this->dao->transactionRollback();
        }
        $this->dao->transactionCommit();
    }

    private function reschedule($antenna, $enable){
        $codeFailure = ($enable) ? ACTIVATION_FAILED : PENDING_DEACTIVATION;
        foreach($antenna as $antennaUnit){
            $this->updateAntenna($antennaUnit, $codeFailure, !$enable);
            $this->log('W',"Antenna {$antennaUnit["MobileID"]} - Rescheduled");
        }
    }

    private function processActivationReturn($antenna, $activation, $enable){
        $codeFailure = ($enable) ? ERROR_ON_ACTIVATION : PENDING_DEACTIVATION;
        if($activation === NULL){            
            $this->log('E',"COULD NOT REACH THE SERVER");
            $this->reschedule($antenna, $enable);
        }else {
            $resultType = $enable ? 'ActivateResult' : 'DeactivateResult';
            foreach ($activation->{$resultType}->Mobiles as $antennaUnit) {
                $antennaResult = (in_array($antennaUnit->StatusCode, array(500,515,600,615))) ? true : false;
                if(!$antennaResult){
                    $this->log('E',"Antenna {$antennaUnit->MobileID} - {$antennaUnit->StatusMessage}");
                }else{
                    $this->log('I',"Antenna {$antennaUnit->MobileID} - ".(($enable) ? 'Activated' : 'Deactivated'));
                }
                $this->updateAntenna($antennaUnit, !$antennaResult ? $codeFailure : '',  $antennaResult ? $enable : !$enable );
            }
        }
    }

    private function callSkywave($antenna, $enable){        
        $skywaveAntenna = array();
        foreach($antenna as $antennaUnit){
            $skywaveAntenna[] = $this->getSkywaveAntennaUnit($antennaUnit);
        }    
        $skywaveAntenna = array_values((array_filter($skywaveAntenna)));
        if(count($skywaveAntenna) > 0){
            $this->skywave->setPropriedades('Mobiles', $skywaveAntenna);
            $this->processActivationReturn($skywaveAntenna, json_decode(($enable) ? $this->skywave->ativarAntena() : $this->skywave->desativarAntena()), $enable);       
        }else{
            //$this->log('W',"No antenna on ".(($enable) ? 'Activation' : 'Deactivation')." queue");
        }
    }

    public function executar() {
        try {
            if(!defined("_AMBIENTE_")){
                $this->log('E' ,"ENVIROMENT NOT DEFINED"); 
                exit(0);    
            }
            $this->limitSearch = ( _AMBIENTE_ != 'PRODUCAO' )? true : false;            
            $this->skywave = new API();
            if(strtoupper($this->config('HABILITAR_ATIVACAO_ANTENA')) === 'TRUE'){
                $this->callSkywave($this->dao->getAntennaInstalled($this->antennaActivationTypes), true);
            }
            if(strtoupper($this->config('HABILITAR_DESATIVACAO_ANTENA')) === 'TRUE'){
                $this->callSkywave($this->dao->getAntennaRemoved($this->antennaActivationTypes), false);
            }
        } catch (Exception $e) {
            $this->view->msg = $e->getMessage();			
        }
        return $this->view;
    }
}
