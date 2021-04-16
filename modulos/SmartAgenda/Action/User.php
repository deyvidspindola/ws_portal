<?php

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/smart_agenda_user_'.date('d-m-Y').'.txt');


require_once _MODULEDIR_ . 'SmartAgenda/Action/IntegradorSistemas.php';

/**
 * Classe API USER
 * @author Andre L. Zilz
 */
class User extends IntegradorSistemas {

    private $parametros;

    public function __construct(){

        $this->setAPI('USER');
        $this->parametros = new stdClass();
        parent::__construct();
    }

    public function setLogin($login){
        $this->parametros->login = $login;
    }

    public function getUser(){

        $retorno = new stdClass();

        try{

            $retorno = $this->executarREST( 'GET', 'REST_GET_USER', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function createUser() {

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'PUT', 'REST_CREATE_USER', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function updateUser() {

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'PATCH', 'REST_UPDATE_USER', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function deleteUser() {

        $retorno = new stdClass();

        try{
            $retorno = $this->executarREST( 'DELETE', 'REST_UPDATE_RESOURCE', $this->parametros, array() );

        } catch(Exception $e){
            $retorno->error_msg = $e->getMessage();
        }

        return $retorno;
    }

    public function checkUserExists(){

        try{

            $isUserExists = false;
            $dadosUsuario = $this->getUser();

            if( isset( $dadosUsuario->login ) ){
                $isUserExists = true;
            }
        } catch( Exception $e ) {
            $isUserExists = false;
        }

        return $isUserExists;


    }

    public function parametrizacaoSincronizacao( $politica ) {

        if($politica == 'ad_interno') {
            $parametro = 'USER_TYPE_LOCAL';
        } else if($politica == 'ad_prestadores') {
            if( _AMBIENTE_ != 'PRODUCAO' ) {
                $parametro = 'USER_TYPE_TESTE';
            } else {
                $parametro = 'USER_TYPE_EXTERNO';
            }
        }

        $param = $this->getParametroSmartAgenda($parametro);

        return $param;
    }
}

?>