<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. kautzmann
 * @version 16/09/2013
 * @since 16/09/2013
 * @package Core
 * @subpackage Classe de Acesso a Dados de Autenticacao
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Autenticacao;

use infra\ComumDAO;

class AutenticacaoDAO extends ComumDAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Busca dados do usuário na base do Sistema
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 09/10/2014
     * @param $userName (username/login do usuario)
     * @return Array de dados do usuario
     */
    public function getUserSysData($userName) {
    	$sqlString = "
                SELECT
                      *
                FROM
                    usuarios
                WHERE 
                   upper(ds_login) =   '" .  strtoupper($userName) . "'
                AND
                    dt_exclusao IS NULL;";
        $this->queryExec($sqlString);
        if($this->getNumRows() == 1){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
    /**
     * Busca dados do usuário na base do Sistema
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 09/10/2014
     * @param $userKey (ID do usuario, chave principal da tabela de usuarios)
     * @return Array de dados do usuario
     */
    public function getSysUserKey($user = 'none') {
    	$sqlString = "
                SELECT
                      cd_usuario
                FROM
                    usuarios
                WHERE 
                    ds_login = '" .  $user . "'
                AND
                    dt_exclusao IS NULL;";
    	
        $this->queryExec($sqlString);
        if($this->getNumRows() == 1){
            return $this->getAssoc();
        } else{
            return false;
        }
    }
    
}