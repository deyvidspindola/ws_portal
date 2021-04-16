<?php

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/smart_agenda_'.date('d-m-Y').'.txt');

require_once _MODULEDIR_ . 'SmartAgenda/DAO/IntegradorSistemasDAO.php';

date_default_timezone_set('America/Sao_Paulo');

/**
 * Classe Middleware - Integrador entre ERP Sascar e OFSC
 * @author Andre L. Zilz
 */
class IntegradorSistemas {

    private $dao;
    private $ambiente;
    private $API;
    private $token;
    private $propriedades;
    private $propriedade;

    public function __construct(){
        $this->propriedades  = array();
        $this->propriedade   = NULL;
        $this->dao  = new IntegradorSistemasDAO();
        $this->getAmbiente();
        $this->token = $this->getAutenticacaoOAuth();
    }

    public function setAPI($api){
        $this->API = $api;
    }

    public function getParametroSmartAgenda($parametro) {
        $valorParam =  $this->dao->getParametroSmartAgenda( $parametro );
        return $valorParam;
    }

    public function setPropriedades($chave, $valor) {
        
        if( is_string($valor)){
            $valor = utf8_encode($valor);
        }
        $this->propriedades[$chave] = $valor;
    }

    public function setPropriedade($valor) {
        $this->propriedade = $valor;
    }


    private function getAmbiente(){

        switch (_AMBIENTE_) {
            case 'DESENVOLVIMENTO':
                $this->ambiente = 'DESENV';
                break;
            case 'TESTE':
                $this->ambiente = 'TESTE';
                break;
            case 'HOMOLOGACAO':
                $this->ambiente = 'HOMOLOG';
                break;
            case 'PRODUCAO':
                $this->ambiente = 'PROD';
                break;
            default:
                $this->ambiente = 'DESENV';
                break;
        }
    }

    private function getAutenticacaoOAuth() {

        try{

            $clientID      = $this->getParametroSmartAgenda( $this->ambiente . '_CLIENT_ID' );
            $clientSecret  = $this->getParametroSmartAgenda( $this->ambiente . '_CLIENT_SECRET' );
            $proxyauth   = $clientID . ':' . $clientSecret;

            $opcoesCurl = array(
                    CURLOPT_USERPWD => $proxyauth,
                    CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
                );

            $this->setPropriedade('grant_type=client_credentials');

            $dadosRetorno = $this->executarREST( 'POST', 'REST_GET_TOKEN', NULL, $opcoesCurl );

        } catch(Exception $e) {
            throw new Exception( $e->getMessage() );
        }

        return $dadosRetorno->token;

    }

    private function getParametrosCURL(){

        $dadosCURL = new stdClass();
        $dadosCURL->dominio = $this->getParametroSmartAgenda( $this->ambiente . '_OFSC_URL' );
        return $dadosCURL;

    }

    public function executarREST( $verbo, $metodo, $parametros = NULL, $opcoesCurlAdicionais = array() ){

        try{

            if( is_array($this->propriedades) && count($this->propriedades) > 0 ){
                $dados = json_encode($this->propriedades);
            } else if ( ! empty($this->propriedade) ) {
                $dados = $this->propriedade;
            } else{
                $dados = NULL;
            }

            $isFile = isset($opcoesCurlAdicionais[CURLOPT_BINARYTRANSFER]) ? true : false;

            $parametrosCurl = $this->getParametrosCURL();
            $uri = $this->getParametroSmartAgenda( $metodo );

            if( !is_null($parametros) ){
                $uri = $this->substituirParametros($uri, $parametros);
            }

            $curl  = curl_init( $parametrosCurl->dominio . $uri );

            if(_AMBIENTE_ == 'LOCALHOST'){
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            /* inserido para tratatar a ASM 2712 */
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,120); 
            curl_setopt($curl, CURLOPT_TIMEOUT, 120);

            if( $isFile ) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/octet-stream', 'Cache-Control: no-cache', 'Authorization: Bearer ' .  $this->token));
            } else {
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' .  $this->token));
            }

            if( ! empty($opcoesCurlAdicionais) ){
                curl_setopt_array($curl, $opcoesCurlAdicionais);
            }

            switch ($verbo) {
                case 'POST':
                    curl_setopt($curl, CURLOPT_POST, true);
                    if( ! is_null($dados) ){
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $dados);
                    }
                    break;
                case 'PUT':
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                    if( ! is_null($dados) ){
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $dados);
                    }
                    break;
                case 'PATCH':
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $dados);
                    break;
                case 'DELETE':
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    break;
                default:
                    break;
            }

            $timestampRequest =  date('Y-m-d H:i:s') . substr((string)microtime(), 1, 8);
            $dadosJSON    = curl_exec($curl);

            //Se for imagem nao retorna JSON e sim uma String
            if( $isFile ){
                $dadosRetorno = $dadosJSON;
                $response = 'Imagem Criptografada';
            } else {
                $dadosRetorno = json_decode($dadosJSON);
                $response = $dadosJSON;
            }

            $httpCode     = (string) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $code         = substr($httpCode, 0, 1 );

            $request =  "URI: " . $parametrosCurl->dominio . $uri . ' - DADOS: ' . $dados;
            $this->dao->gravaLogComunicacao($this->API, $request, $response, $timestampRequest);

            if ( ($code == '4') || ($code == '5')  ){

               if( isset($dadosRetorno->detail) & $dadosRetorno->detail != 'Unknown user login') {
                    $erro = curl_error($curl);
                    curl_close($curl);
                    $erroDescricao = ( isset($dadosRetorno->detail) ) ? $dadosRetorno->detail : $dadosJSON;
                    throw new Exception( 'ERRO: ' . $erro . ' - ' . $httpCode . ' - ' . $erroDescricao );
                }
            }

             if( $dadosJSON === FALSE ){
                $erroDescricao = 'ERRO: ' . curl_error($curl) . ' - ' . curl_errno($curl);
                curl_close($curl);
                throw new Exception(  $erroDescricao );
            }

            $this->propriedade = NULL;
            $this->propriedades = array();

            curl_close($curl);

        } catch(Exception $e){
            throw new Exception( $e->getMessage() );
        }

        return $dadosRetorno;

    }

    private function substituirParametros($uri, $parametros) {

        foreach ($parametros as $chave => $valor) {
            $uri  = str_replace('{' . $chave .'}', $valor , $uri);
        }

        return $uri;

    }

}


?>
