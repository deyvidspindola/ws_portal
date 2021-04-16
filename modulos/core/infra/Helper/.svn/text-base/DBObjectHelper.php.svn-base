<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Jorge A. D. Kautzmann
 * @version 29/08/2013
 * @since 29/08/2013
 * @package Core
 * @subpackage Classe de auxílio a operações de banco
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace infra\Helper;

use infra\ComumDAO;

class DBObjectHelper extends ComumDAO{
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Inicia uma transação.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 09/12/2013
     * @return resource/FALSE
     */
    public function transactionBegin(){
        return parent::startTransaction();
    }
    /**
     * Finaliza uma transação.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 09/12/2013
     * @return resource/FALSE
     */
    public function transactionCommit(){
        return parent::commitTransaction();
    }
    /**
     * Reverte uma transação.
     *
     * @author Jorge A. D. kautzmann <jorge.kautzmann@sascar.com.br>
     * @version 09/12/2013
     * @return resource/FALSE
     */
    public function transactionRollback(){
        return parent::rollbackTransaction();
    }
    
}