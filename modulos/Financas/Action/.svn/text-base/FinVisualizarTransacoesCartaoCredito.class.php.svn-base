<?php
/**
* @author	Emanuel Pires Ferreira
* @email	epferreira@brq.com
* @since	10/12/2012
* */

require_once (_MODULEDIR_ . 'Principal/DAO/PrnManutencaoFormaCobrancaClienteDAO.php');
require_once (_MODULEDIR_ . 'Financas/DAO/FinVisualizarTransacoesCartaoCreditoDAO.class.php');
require_once (_MODULEDIR_ . 'Financas/Action/FinFaturamentoCartaoCredito.class.php');

/**
 * Trata requisições do módulo financeiro para efetuar pagamentos 
 * de títulos com forma de cobrança 'cartão de crédito' 
 */
class FinVisualizarTransacoesCartaoCredito {
	
	/**
	 * Fornece acesso aos dados necessarios para o módulo
	 * @property VisualizarTransacoesDAO
	 */
	private $visualizarTransacoesDAO;
	
	/**
	 * Fornece acesso aos dados da forma de cobrança 
	 * @property PrnManutencaoFormaCobrancaClienteDAO
	 */
	private $verificarCobrancaClienteDAO;
    
    /**
     * Fornece acesso aos objetos do WebService
     * @property IntegracaoSoftExpress
     */
    private $ws;
    
    private $file;
    
	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
    public function __construct() 
    {
		global $conn;
        
        $this->visualizarTransacoesDAO      = new FinVisualizarTransacoesCartaoCreditoDAO($conn);
        $this->verificarCobrancaClienteDAO  = new PrnManutencaoFormaCobrancaClienteDAO($conn);
        $this->fatCC                        = new FinFaturamentoCartaoCredito();
    }

    public function pesquisar()
    {
        return $this->visualizarTransacoesDAO->pesquisar();
    }
    
    public function detalhes()
    {
        $retorno = array();
        
        $retorno['detalhes']  = $this->visualizarTransacoesDAO->detalhes();
        $retorno['historico'] = $this->visualizarTransacoesDAO->historico();
        
        return $retorno;
    }
    
    public function reenviar()
    {
        //usuário AUTOMATICO para processos onde não existe autenticação
        $cd_usuario = 2750;
        
        $clioid = (isset($_POST['clioid'])) ? $_POST['clioid'] : null;
        $titoid = (isset($_POST['titoid'])) ? $_POST['titoid'] : null;
        $valort = (isset($_POST['valort'])) ? $_POST['valort'] : null;
        
        try {
        	
        	//verifica se não é produção
	    	if($_SESSION['servidor_teste'] != 0) {
        		//cria arquivo para testar conciliação no ambiente de teste
        		 $this->fatCC->criaArquivo();
        	}
           
            //retorna próximo dia útil para realizar a cobrança
            $diaCobranca    = $this->fatCC->retornaProximoDiaUtil();
            
            //processa o pagamento do título
            $ret = $this->fatCC->processaPagamento($clioid, $titoid, $valort, $diaCobranca);
                
            if($ret) {
                $retorno[$ret['acao']][$ret['code']]++;
            }

            //verifica se não é produção
            if($_SESSION['servidor_teste'] != 0) {
            	//fecha arquivo criado no ambiente de testes
            	$this->fatCC->fechaArquivo();
            }
        
        } catch(Exception $e) {
            $this->visualizarTransacoesDAO->rollback();
            
            $reporte['acao'] = 'Erro no processamento - Sascar';
            $reporte['code'] = 0;

        }
        
        $retorno[$reporte['acao']][$reporte['code']]++;

        return $retorno;
    }
    
    /**
     * Verifica se a forma de pagamento por cartão de crédito do cliente está ativa 
     * 
     * @param int $clioid
     * @return boolean
     */
    public function verificarFormaPagamentoAtualCliente($clioid){
    	
    	return $this->visualizarTransacoesDAO->buscaDadosCartao($clioid);
    	
    }
    
    /**
     * Verifica se a forma de cobrança do título é cartão de crédito
     * 
     * @param unknown $forcoid
     * @return boolean
     */
    public function verificarTituloPagamentoCredito($forcoid){
    	
    	return $this->verificarCobrancaClienteDAO->isCartaoCredito($forcoid);
    	
    }
    
   
    /**
     * Helper que converte o valor do título de Float para Int
     * 
     * @param float $entrada - valor em formato float
     * 
     * @return integer $valor - valor convertido em formato Int
     */
    private function _converteValor($entrada)
    {
        return number_format($entrada,2,"","");
    }
    
    /**
     * Helper que adiciona 30 dias a data de cobrança do título
     * 
     * @param date $data - data de pagamento
     * 
     * @return date 
     */
    private function _dataNovoTitulo($data) 
    {
        list($dia, $mes, $ano) = explode("/",$data);
        
        return date('d/m/Y', mktime(0,0,0, $mes, $dia + 30, $ano));
    }

    
	public function gerarCSV()
	{

		$pesquisar = $this->pesquisar();

		header('Content-Type: text/csv');
		header('Content-disposition: attachment;filename=transacoes_cartao_de_credito.csv');
    	header("Content-Type: application/force-download");

		$fileCSV  = "Data de Envio;Status;Motivo da Rejeição;Número da Autorização;Data de Pagamento;";
		$fileCSV .= "Número do Título;Nome do Cliente;Valor Título;Data de Vencimento;Forma de Cobrança\n";

        if(is_array($pesquisar['titulos'])) {
    		foreach($pesquisar['titulos'] as $linha){

    			$autorizacao  = $this->visualizarTransacoesDAO->detalhesTransacao($linha['ctctitoid']);

    			$dtTransacao  = implode("/",array_reverse(explode("-",$linha['dt_transacao'])));
    			$dtVencimento = implode("/",array_reverse(explode("-",$linha['titdt_vencimento'])));
    			$dtPagamento  = implode("/",array_reverse(explode("-",$linha['titdt_pagamento'])));
    			$valorTitulo  = number_format($linha['titvl_titulo'],2,',','.');
    			$valorPag	  = number_format($linha['titvl_pagamento'],2,',','.');	

    			if($linha['ctcccchoid'] == 0 && $linha['status'] == ""){
    				$situacao = 'Não Enviada';
    				$campos   = $dtTransacao.';'.$situacao.';'.$linha['ctcmotivo'].';;;'.$linha['ctctitoid'].';';
    				$campos  .= $linha['clinome'].';'.$valorTitulo.';'.$dtVencimento.';'.$linha['forcnome'];

    			} elseif ($linha['titdt_pagamento'] == "" &&  $linha['titdt_credito'] == "" && $linha['status'] != 'CON') {
    				$situacao = 'Pendente de Pagamento';
    				$campos   = $dtTransacao.';'.$situacao.';'.$linha['ctcmotivo'].';;;'.$linha['ctctitoid'].';';
    				$campos  .= $linha['clinome'].';'.$valorTitulo.';'.$dtVencimento.';'.$linha['forcnome'];

    			} elseif (($linha['titdt_pagamento'] != "" && $linha['titdt_credito'] != "" && $linha['status'] === 'CON') || ($linha['ctcccchoid'] != 0 && $linha['status'] == '')){
    				$situacao = 'Recebida';
    				$campos   = $dtTransacao.';'.$situacao.';;'.$autorizacao[0]['ccchnumero_autorizacao'].';'.$dtPagamento.';'.$linha['ctctitoid'].';';
    				$campos  .= $linha['clinome'].';'.$valorPag.';'.$dtVencimento.';'.$linha['forcnome'];
    			}

    			$fileCSV .= $campos."\n";
    		}
        }

		echo $fileCSV;
		exit;
	}

    public function criaArquivo()
    {
        $this->file = fopen(_SITEDIR_.'eSitef/arquivos_conciliacao/usar_'.date('dmY His').'.csv', 'a+');
        $cabecalho = array("0","Transacao","Cliente","Data Pagamento","","NSU","NSU","CARTAO","VALOR");

        fputcsv($this->file, $cabecalho,';');       
    }
    
    public function fechaArquivo()
    {
        fclose($this->file);
    }

}