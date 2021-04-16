<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
 * @version 29/08/2013
 * @since 29/08/2013
 * @package Core
 * @subpackage Classe Model Autenticacao
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
*/
namespace module\Autenticacao;

use infra\Helper\Validacao;
use infra\Helper\Mascara;

class AutenticacaoModel{
    // Atributos
    private $dao; // Acesso a dados

    // Membros para comunicação com Active Directory
    private $adHost;
    private $adPort;
    private $adUser;
    private $adPass;
    private $adProt;
    private $adConn;
  
    // Campos inteiros no BD
    private $intFieldList = array('field_01', 'field_02');
    
    // Campos float no BD
    private $floatFieldList = array();    
	
	/**
	* Contrutor da classe
	* 
	* @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 07/10/2014
    * @param array $adOptions Array com opções para conexão com o AD     
    * @return none
    */
    public function __construct($adOptions = array()){
        $this->dao = new AutenticacaoDAO();
        if (count($adOptions) > 0) {
            if (array_key_exists('prot',$adOptions)){ 
                $this->setProtocol($adOptions['prot']); 
            }
            if (array_key_exists('host',$adOptions)){ 
                $this->setHost($adOptions['host']); 
            }
            if (array_key_exists('port',$adOptions)){ 
                $this->setPort($adOptions['port']); 
            }
            if (array_key_exists('user',$adOptions)){ 
                $this->setUser($adOptions['user']); 
            }
            if (array_key_exists('pass',$adOptions)){ 
                $this->setPass($adOptions['pass']); 
            }
        }
    }
    
    /**
    * Define o host de comunicação com AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param string $adHost
    * @return none
    */
    public function setHost($adHost){ 
        $this->adHost = $adHost;
    }
    
   /**
    * Recupera o host de comunicação com AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param none
    * @return string $adHost
    */
    public function getHost(){ 
        return $this->adHost;
    }
    
    /**
    * Define a porta de comunicação com AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param int $adPort
    * @return none
    */
    public function setPort($adPort){ 
        $this->adPort = $adPort;
    }
    
   /**
    * Recupera a porta de comunicação com AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param none
    * @return int $adPort
    */
    public function getPort(){ 
        return $this->adPort;
    }
    
    /**
    * Define usuário AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param string $adUser
    * @return none
    */
    public function setUser($adUser){ 
        $this->adUser = $adUser;
    }
    
   /**
    * Recupera usuário AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param none
    * @return string $adUser
    */
    public function getUser(){ 
        return $this->adUser;
    }
    
     
    /**
    * Define senha para conexão AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param string $adPass
    * @return none
    */
    public function setPass($adPass){ 
        $this->adPass = $adPass;
    }
    
   /**
    * Recupera senha do usuário AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param none
    * @return string $adPass
    */
    public function getPass(){ 
        return $this->adPass;
    }
    
    /**
    * Define protocolo de conexão AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param string $adProt
    * @return none
    */
    public function setProtocol($adProt='ldap://'){ 
        $this->adProt = $adProt;
    }
    
   /**
    * Recupera o protocolo de conexão AD em uso
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param none
    * @return string $adProt
    */
    public function getProtocol(){ 
        return $this->adProt;
    }
    
    /**
     * Busca de dados do veículo pelo ID
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 03/10/2014
     * @param none
     * @return Status do serviço 'OK'/'NOK'
     */
    public function getServicoStatus() {
        $resData = '';
        $resConn = curl_init();
        curl_setopt($resConn, CURLOPT_URL,$this->adProt . $this->adHost);
        curl_setopt($resConn, CURLOPT_HEADER, 0);
        curl_setopt($resConn, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($resConn, CURLOPT_RETURNTRANSFER, true);
        $resData = curl_exec($resConn);
        curl_close($resConn);
		return $resData;
    }
     
   /**
    * Conecta ao AD obtendo um identificador de conexão
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param none
    * @return mixed $adConn = 'Itentificador de conexão'/false
    */
    public function connect(){ 
        if(!isset($this->adConn)){
            $this->adConn = ldap_connect($this->adProt . $this->adHost, $this->adPort);
        }
        return $this->adConn;
    }
   
   /**
    * Linka a conexão com o servidor/controlador AD
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param none
    * @return mixed $adConn = 'Itentificador de conexão'/false
    */
    public function bind(){
        if(isset($this->adConn)){
            return @ldap_bind($this->adConn, $this->adUser, $this->adPass);
            return false;
        }
    }
 
   /**
     * Lista atributos conforme base DN e filtro
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 15/10/2014
     * @param string $baseDn = 'base DN para listagem';
     * @param string $filter = 'Filtro';
     * @param array $attributes = 'array de atributos';
     * @return array $resSearch = 'array/matriz com os dados dos usuários'
     */
    public function getList($baseDn = '', $filter = '', $attributes=array()) {
        $resSearch = ldap_search($this->adConn, $baseDn, $filter, $attributes);
        return ldap_get_entries($this->adConn, $resSearch);
    }
    
    /**
     * Recupera informações de um usuário no DB
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 15/10/2014
     * @param string $userName = 'username a ser buscado';
     * @return array $userInfo = 'array/matriz com os dados do usuário'
     */
    public function getUserSysInfo($userName){
        $userInfo = array();
        $userInfo =  $this->dao->getUserSysData($userName);
        if($userInfo != false){
            $userInfo = $this->getInfoArray($userInfo);
        }
        return $userInfo;
    }
    
   
    /**
     * Recupera informações de um usuário no DB
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 15/10/2014
     * @param array $userInfo = 'array com dado do registro do usuário';
     * @return array $userArray = 'array/matriz com os dados do usuário'
     */
    public function getInfoArray($userInfo = array()){
        $userArray = array();
        $userArrayAux = array();
        $userArray['cd_usuario'] =  $userInfo['cd_usuario'];
        $userArray['ds_login'] =  $userInfo['ds_login'];
        $userArray['usuemail'] =  $userInfo['usuemail'];
        $userArray['usucargooid'] =  $userInfo['usucargooid'];
        $userArray['usudepoid'] =  $userInfo['usudepoid'];
        $userArray['usuqtd_tentativas_login'] =  $userInfo['usuqtd_tentativas_login'];
        $userArray['usuacesso_externo'] =  $userInfo['usuacesso_externo'];
        $userArray['usubloqueado'] =  $userInfo['usubloqueado'];
        $userArray['usuloginseqad'] =  trim($userInfo['usuloginseqad']);
        $userArrayAux = explode('.', $userInfo['usuloginseqad']);
        $userArray['nivel'] =  Mascara::inteiro($userArrayAux[0]);
        $userArray['prioridade'] =  $userArrayAux[1];
        $userArray['ad-ativo'] =  Mascara::inteiro($userArrayAux[2]);
        $userArray['ad-server'] =  $userArrayAux[3];
        // remover comentario*** if($userArray['nivel'] > 0 && $userArray['prioridade'] == 'ADDB' && $userArray['ad-ativo'] == 1){
        if($userArray['ad-ativo'] == 1){
            $userArray['login-ad'] = true;
        }
        
        return $userArray;
    }

   
    /**
     * Recupera informações para conexão com AD
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 31/10/2014
     * @param array $adArrayConfig (array associativo de dados para conexão com o AD)
     * @param string $adKey = 'chave de busca no array';
     * @return array $adArrayConfig[$adKey] = 'array/matriz com os dados de configuração'
     */
    public function getAdConfig($adArrayConfig=array(), $adKey= ''){
        return $adArrayConfig[$adKey];
    }

      
    /**
     * Recupera informações de um usuário específico no AD
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 15/10/2014
     * @param string $baseDn = 'base DN para busca';
     * @param string $userName = 'username do usuario a ser buscado';
     * @param string $attributes = 'lista de atributos';
     * @return array $resSearch = 'array/matriz com os dados do usuário'
     */
    public function getUserInfo($baseDn = '', $userName = '', $attributes=array()){
        // Filtro pelo username 
        $filter = 'samaccountname=' . $userName;
        $filter = "(&(objectCategory=person)({$filter}))";
        if (count($attributes) == 0) {
            $attributes = array('samaccountname','mail','memberof','department','displayname','lastLogonTimestamp','pwdlastset','telephonenumber','primarygroupid','objectsid'); 
        }
        $resSearch=ldap_search($this->adConn, $baseDn, $filter, $attributes);
        return ldap_get_entries($this->adConn, $resSearch);
    }
    
    
    /**
     * Cria uma nova conta de usuário
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 21/10/2014
     * @param string $baseDn = 'base DN';
     * @param string $attributesValues = 'array associativo com valores de atributos';
     * @return bool $operSt = 'true/false'
     */
    public function createUser($baseDn = '', $attributesValues=array()){
        // Filtro pelo username 
        echo '<pre>';
        var_dump($attributesValues);
        echo '</pre>';
        if(true){ //valida/manipula atributos
            //return ldap_add($this->adConn, 'CN=tstusr,' . $baseDn, $attributesValues);
            $xa =  ldap_add($this->adConn, 'CN=tstusr,' . $baseDn, $attributesValues);
            
            echo '<br> Erro n°: ' . ldap_errno($this->adConn);
            echo '<br> Erro msg: ' . ldap_err2str( ldap_errno($this->adConn) );
            return $xa;
        }else{
            return false;
        }
    }
    
   
   /**
    * Desconecta/fecha conexão com ad
    *
    * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
    * @version 10/10/2014
    * @param none
    * @return none
    */
    public function close(){
        if(isset($this->adConn)){
            ldap_close($this->adConn);
        }
    }


}
