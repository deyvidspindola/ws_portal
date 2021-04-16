<?php
require_once _MODULEDIR_ . 'Cron/Exception/CronException.php';
require_once _MODULEDIR_ . 'Cron/Action/CronView.php';

/**
 * @CronAction.php
 * 
 * Action padrão para controladores do Cron
 * 
 * @author 	Alex S. Médice
 * @email   alex.medice@meta.com.br
 * @version 20/11/2012
 * @since   20/11/2012
 */
class CronAction {
	/**
	 * Nome do processo que está sendo executado
	 * @var string
	 */
	private $nomeProcesso;

    /**
     * Informações do $_REQUEST
     * @property $request
     */
    protected $request;
    
	/**
	 * Link de conexão com o banco de dados
	 * @var mixed $conn
	 */
	protected $conn;

	/**
	 * Ação atual
	 * @var string $action
	 */
	protected $action;
	
    /**
     * Atributo para acesso a persistência de dados
     * @property FinGeraNfBoletoGraficaDAO
     */
    protected $dao;
    
    /**
     * Informações para View
     * @property CronView
     */
    protected $view;
    
    /**
     * @var array $request
     * @return void
     */
    public function __construct($request, $nomeProcesso=null) {
    	
        global $conn;
        
        $this->request 					= $request;
        $this->nomeProcesso 			= $nomeProcesso;
        $this->conn 					= $conn;
        $this->view 					= new CronView();
        $this->action 					= (isset($this->request['acao'])) ? $this->request['acao'] : 'index';

        $this->view->acao  				= $this->action;
        $this->view->msg 				= (isset($this->request['msg'])) ? $this->request['msg'] : '';
        
        if (!empty($this->nomeProcesso)) {
        	$this->verificarProcesso($this->nomeProcesso);
        }
    }
    
    public function setNomeProcesso($nomeProcesso) {
    	$this->nomeProcesso = $nomeProcesso;
    }
    
    /**
     * Verifica se o processo não está travado ou se ainda está rodando
     *
     * @param string $nomeProcesso
     * @throws CronException
     * @return void
     */
    private function verificarProcesso($nomeProcesso) {
    	if (burnCronProcess($nomeProcesso) === true) {
    		throw new CronException("ERRO: Processo [" . $nomeProcesso . "] ainda está rodando.");
    	}
    }

    protected function parseSeparatedItem($separator, $item) {
        return array_map('trim', explode($separator,$item));
    }

    protected function dateTimeLog() {
        $date = new DateTime();
        return $date->format("Y/m/d H:i:s");
    }

    protected function log($type,$msg) {        
        echo "{$this->dateTimeLog()} [{$type}]- {$msg}\n";
    }
}