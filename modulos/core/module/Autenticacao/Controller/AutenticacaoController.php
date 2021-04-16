<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
 * @version 03/10/2014
 * @since 03/10/2014
 * @package Core
 * @subpackage Classe Controladora do módulo de Autenticação/Autorização
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Autenticacao;

use infra\ComumController,
    infra\Helper\Validacao,
    infra\Helper\Mascara,
    infra\Helper\Response;

class AutenticacaoController extends ComumController{
    
    private $model;
    private $response;
    
    
    /**
     * Construtor da classe
     * 
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 03/10/2014
     * @param array $adOptions Array com opções para conexão com o AD     
     * @return none
     */
    public function __construct($adOptions = array()){
        $this->model = new AutenticacaoModel($adOptions);
        $this->response = new Response();
    }
    
    /**
     * Verifica o status do serviço de autenticação A.D. via ldap
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 03/10/2014
     * @param none
     * @return response ($response->dados = 'OK'/'NOK')
     */
    public function verificaStatusServico() { 
        try {
             if($this->model->getServicoStatus() === false){
                $this->response->setResult('NOK', 'AD001', 'Nao foi possivel estabelecer uma conexao com o serviso AD.');
            } else{
                $this->response->setResult('OK', '0', 'Conexao AD OK!');
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }
        return $this->response;
     }
    
    /**
     * processa a autenticação A.D. via ldap
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 09/10/2014
     * @param none
     * @return response ($response->dados = 'OK'/'NOK')
     */
    public function autentica() {
        // Sequência de validações
        if (($this->model->getUser() === NULL) || ($this->model->getPass() === NULL)){
            $this->response->setResult('NOK', 'AD002', 'Usuario ou senha invalidos!');
            return $this->response;
        }
        if ((trim($this->model->getUser()) == '') || (trim($this->model->getPass()) == '')){
            $this->response->setResult('NOK', 'AD003', 'Usuario ou senha invalidos!');
            return $this->response;
        }
        // Processo de autenticação
        try {
            if($this->model->connect()){
                if($this->model->bind()){
                    $this->response->setResult('OK', '0', 'Autenticacao OK!');  
                }else{
                    $this->response->setResult('NOK', 'AD004', 'Falha na autenticacao!');
                }
            }else{
                $this->response->setResult('NOK', 'AD005', 'Falha ao estabelecer conexao!');
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }
        $this->model->close();
        return $this->response;
     }
    
    /**
     * Recupera lista de usuarios AD
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 15/10/2014
     * @param string $baseDn = 'base DN para listagem';
     * @param string $filter = 'Filtro';
     * @param array $attributes = 'array de atributos';
     * @return response ($response->dados = $userArray=array/NOK)
     */
    public function getList($baseDn = '', $filter = '', $attributes=array()) {
        $lista = array();
        try {
            if($this->model->connect()){
                if($this->model->bind()){
                    $lista = $this->model->getList($baseDn, $filter, $attributes);
                    if(!$lista){
                        $this->response->setResult('NOK', 'AD006', 'Erro ao realizar busca!');
                    }else{
                        $this->response->setResult($lista, '0', 'Busca realizada com sucesso!');
                    }
                }else{
                    $this->response->setResult('NOK', 'AD004', 'Falha na autenticacao!');
                }
            }else{
                $this->response->setResult('NOK', 'AD005', 'Falha ao estabelecer conexao!');
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }
        $this->model->close();
        return $this->response;
    }
    
    /**
     * Recupera informações de um usuário no DB do sistema
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 28/10/2014
     * @param string $userName = 'username a ser buscado';
     * @return response ($response->dados = $usrInfo=array/NOK)
     */
    public function getUserSysInfo($userName = '') {
        $usrInfo = array();
        try {
            $usrInfo = $this->model->getUserSysInfo(trim($userName));
            if(count($usrInfo) == 0){
                $this->response->setResult('NOK', 'DB001', 'Usuario nao localizado!');
            }else{
                $this->response->setResult($usrInfo, '0', 'Busca de usuario realizada com sucesso!');
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }
        return $this->response;
    }

    
    /**
     * Recupera informações do arquivo de configuração, para conexão com AD server
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 31/10/2014
     * @param array $adArrayConfig (array associativo de dados para conexão com o AD)
     * @param string $adKey = 'chave de busca no array';
     * @return response ($response->dados = $usrInfo=array/NOK)
     */
    public function getAdConfigInfo($adArrayConfig=array(), $adKey= '') {
        $adInfo = array();
        try {
            $adInfo = $this->model->getAdConfig($adArrayConfig, $adKey);
            if(count($adInfo) == 0){
                $this->response->setResult('NOK', 'AD009', 'Array de configurações AD não localizado!');
            }else{
                $this->response->setResult($adInfo, '0', 'Busca de array de configurações realizada com sucesso!');
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }
        return $this->response;
    }

    
    /**
     * Recupera informações de um usuário específico na base AD
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 15/10/2014
     * @param string $baseDn = 'base DN para busca';
     * @param string $userName = 'username do usuario a ser buscado';
     * @param array $attributes = 'array de atributos';
     * @return response ($response->dados = $usrInfo=array)
     */
    public function getUserInfo($baseDn = '', $userName = '', $attributes=array()) {
        $usrInfo = array();
        try {
            if($this->model->connect()){
                if($this->model->bind()){ 
                    $usrInfo = $this->model->getUserInfo($baseDn, $userName, $attributes);
                    if(!$usrInfo){
                        $this->response->setResult('NOK', 'AD007', 'Usuario nao localizado!');
                    }else{
                        $this->response->setResult($usrInfo, '0', 'Busca de usuario realizada com sucesso!');
                    }
                }else{
                    $this->response->setResult('NOK', 'AD004', 'Falha na autenticacao!');
                }
            }else{
                $this->response->setResult('NOK', 'AD005', 'Falha ao estabelecer conexao!');
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }
        $this->model->close();
        return $this->response;
    }

    
    /**
     * Cria um novo usuário no AD
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 21/10/2014
     * @param string $baseDn = 'base DN para busca';
     * @param array $attributesValues = 'array associativo com valores dos atributos';
     * @return response ($response->dados = true/false)
     */
    public function createUser($baseDn = '', $attributesValues=array()) {
        $usrInfo = false;
        try {
            if($this->model->connect()){
                if($this->model->bind()){ 
                    $usrInfo = $this->model->createUser($baseDn, $attributesValues);
                    if(!$usrInfo){
                        $this->response->setResult('NOK', 'AD008', 'Falha ao criar usuario!');
                        
                    }else{
                        $this->response->setResult($usrInfo, '0', 'Usuario criado com sucesso!');
                    }
                }else{
                    $this->response->setResult('NOK', 'AD004', 'Falha na autenticacao!');
                }
            }else{
                $this->response->setResult('NOK', 'AD005', 'Falha ao estabelecer conexao!');
            }
        } catch (\Exception $e) {
            $this->response->setResult($e, 'EXCEPTION');
        }
        $this->model->close();
        return $this->response;
    }

       
    /**
    * Encode a password for transmission over LDAP
    *
    * @param string $password The password to encode
    * @return string
    */
    public function encodePassword($password)
    {
        $password="\"".$password."\"";
        $encoded="";
        for ($i=0; $i <strlen($password); $i++){ $encoded.="{$password{$i}}\000"; }
        return $encoded;
    }
     

    
   /**
     * Fecha/encerra a conexão do objeto atual com o AD 
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 15/10/2014
     * @param none
     * @return none
     */
    public function close() {
        $this->model->close();
    }

}
