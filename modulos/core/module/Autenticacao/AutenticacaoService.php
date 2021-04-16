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
 * @subpackage Classe core / Autenticacao
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (https://www.sascar.com.br)
 */

namespace module\Autenticacao;

class AutenticacaoService{
    
    /** 
     * Retorna uma instância da Controladora
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 09/10/2014
     * @param array $adArray (array associativo de dados para conexão com o AD)
     *     OBS: campos obrigatórios de $adArray
     *         string $adArray['host'] = 'Host AD';
     *         string $adArray['user'] = 'User name do AD';
     *         string $adArray['pass'] = 'Senha';
     *     OBS: campos NÃO obrigatórios de $arrayCliente
     *         string $adArray['port'] = 'Porta de conexão -> default 389';
     *         string $adArray['prot'] = 'protocolo conexão -> default ldap://';
     *
     * @return Object:
     *     Objeto da controladora Login
    */
    public static function instancia($adArray=array()) {
        return new AutenticacaoController($adArray);
    }

    /** 
     * Verifica status do serviço autenticação
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 03/10/2014
     * @param array $adArray (array associativo de dados para conexão com o AD)
     *     OBS: campos obrigatórios de $adArray
     *         string $adArray['host'] = 'Host AD';
     *     OBS: campos NÃO obrigatórios de $arrayCliente
     *         string $adArray['port'] = 'Porta de conexão -> default 389';
     * @return Response $response:
     *     string $response->dados (status='OK'/'NOK')
     *     string $response->codigo (código do erro/retorno)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function verificaStatusServico($adArray=array()) {
        $login = new AutenticacaoController($adArray);
        return $login->verificaStatusServico();
    }
    
  
    /** 
     * Busca Informações do usuário do sistema
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 28/10/2014
     * @param string $userName = 'username a ser buscado DB sistema';
     * @return Response $response:
     *     mixed $response->dados ($usrInfo=array/NOK)
     *     string $response->codigo (código do erro/retorno)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function userInfo($userName='') {
        $login = new AutenticacaoController();
        return $login->getUserSysInfo($userName);
    }
    
    /** 
     * Busca Configurações de conexão com o AD para o usuário
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 28/10/2014
     * @param array $adArrayConfig (array associativo de dados para conexão com o AD)
     * @param string $adKey = 'chave de busca no array';
     * @return Response $response:
     *     mixed $response->dados ($usrInfo=array/NOK)
     *     string $response->codigo (código do erro/retorno)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function adConfigInfo($adArrayConfig=array(), $adKey='S1') {
         $login = new AutenticacaoController();
        return $login->getAdConfigInfo($adArrayConfig, $adKey);
     }
    
   
    /** 
     * Processa autenticação
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 09/10/2014
     * @param array $adArray (array associativo de dados para conexão com o AD)
     *     OBS: campos obrigatórios de $adArray
     *         string $adArray['host'] = 'Host AD';
     *         string $adArray['user'] = 'User name do AD';
     *         string $adArray['pass'] = 'Senha';
     *     OBS: campos NÃO obrigatórios de $arrayCliente
     *         string $adArray['port'] = 'Porta de conexão -> default 389';
     *         string $adArray['prot'] = 'protocolo conexão -> default ldap://';
     * @return Response $response:
     *     string $response->dados (status='OK'/'NOK')
     *     string $response->codigo (código do erro/retorno)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function autentica($adArray=array()) {
        $login = new AutenticacaoController($adArray);
        return $login->autentica();
        
    }
    
    
}