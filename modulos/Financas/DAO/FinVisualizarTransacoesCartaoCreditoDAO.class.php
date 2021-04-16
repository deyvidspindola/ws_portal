<?php
/**
 * @author	Emanuel Pires Ferreira
 * @email	epferreira@brq.com
 * @since	10/12/2012 
 */
require_once (_MODULEDIR_ . 'Financas/DAO/FinFaturamentoCartaoCreditoDAO.class.php');
/**
 * Fornece os dados necessarios para o módulo do módulo financeiro para 
 * efetuar pagamentos de títulos com forma de cobrança 'cartão de crédito' 
 * @author Emanuel Pires Ferreira
 */
class FinVisualizarTransacoesCartaoCreditoDAO extends FinFaturamentoCartaoCreditoDAO {
	
	/**
	 * Link de conexão com o banco
	 * @property resource
	 */
	public $conn;
	
	
	/**
	 * Construtor
	 * @param resource $conn - Link de conexão com o banco
	 */
	public function __construct($conn)
	{
		$this->conn = $conn;
	}
    
    public function pesquisar() {
        try {
            
            $where = "";
            
            $clinome               = (isset($_POST['clinome'])) ? $_POST['clinome'] : null;
            $clitipo               = (isset($_POST['clitipo'])) ? $_POST['clitipo'] : null;
            $clioid                = (isset($_POST['clioid']))  ? $_POST['clioid']  : null;
            $clino_documento       = (isset($_POST['clino_documento'])) ? $_POST['clino_documento'] : null;
            $data_venc_ini         = (isset($_POST['titdt_vencimento_inicio'])) ? $_POST['titdt_vencimento_inicio'] : null;
            $data_venc_fim         = (isset($_POST['titdt_vencimento_fim'])) ? $_POST['titdt_vencimento_fim'] : null;
            $data_transacao_ini    = (isset($_POST['titdt_transacao_inicio'])) ? $_POST['titdt_transacao_inicio'] : null;
            $data_transacao_fim    = (isset($_POST['titdt_transacao_fim'])) ? $_POST['titdt_transacao_fim'] : null;
            $situacao              = (isset($_POST['situacao'])) ? $_POST['situacao'] : null;
    
            $clino_documento = preg_replace('/[^\d]/', '', $clino_documento);
            
            if (!empty($clinome)) {
                $where .= " AND clinome ILIKE '%$clinome%' ";
            }
            
            if (!empty($clino_documento)) {
                if ($clitipo == "F") {
                    $where .= " AND clino_cpf = $clino_documento ";
                } elseif ($clitipo == "J") {
                    $where .= " AND clino_cgc = $clino_documento ";
                }
            }
            
            if (!empty($clitipo) && $clitipo != 'T') {
                $where .= " AND clitipo = '$clitipo' ";
            }            		
			
            if($situacao == 'P') {
                $where .= " AND (ctcccchoid = 0 OR ctcccchoid IS NULL AND ctcstatus IS NULL)";
            } elseif ($situacao == 'E') {
                $where .= " AND titdt_pagamento IS NULL AND titdt_credito IS NULL AND ctcstatus <> 'CON'";
            } elseif ($situacao == 'R') {
                $where .= " AND titdt_pagamento IS NOT NULL AND titdt_credito IS NOT NULL AND ctcstatus IN ('CON') AND ctcccchoid IS NOT NULL ";
            }elseif(empty($situacao)){
            	//é obrigatório informar a situação
            	return false;
            }	

            if(!empty($data_venc_ini) && !empty($data_venc_fim)){
            	$where .= " AND titdt_vencimento BETWEEN '$data_venc_ini' AND '$data_venc_fim' ";
            }
            
            if(!empty($data_transacao_ini) && !empty($data_transacao_fim)){
            	$where .= " AND ctcdt_inclusao::DATE BETWEEN '$data_transacao_ini' AND '$data_transacao_fim' ";
            }
            
            //verifica se o filtro não está vazio, caso sim, retorna false, para não executar a consulta e trazer massa de dados sem filtrar
            if(!empty($where)){

            	$sql = "SELECT ctcoid,
		            		   ctcccchoid,
		            		   ctctitoid,
		            		   ctcstatus,
		            		   clinome,
		            		   ctcclioid,
		            		   titvl_titulo,
		            		   titdt_vencimento,
		            		   forcnome,
		            		   titvl_pagamento,
		            		   clitipo,
		            		   titdt_pagamento,
		            		   titdt_credito,
		            		   ctcdt_inclusao::DATE AS dt_transacao,
            				   titformacobranca,
							   ctcmotivo,
            				    ( titvl_titulo 
								+ titvl_multa 
								+ titvl_juros 
								- titvl_desconto 
								- (CASE WHEN titvl_ir IS NULL THEN 0.00 ELSE titvl_ir END) 
								- (CASE WHEN titvl_iss IS NULL THEN 0.00 ELSE titvl_iss END) 
								- (CASE WHEN titvl_piscofins IS NULL THEN 0.00 ELSE titvl_piscofins END)) as valor_corrigido
            			  FROM clientes
            		INNER JOIN controle_transacao_cartao ON clioid = ctcclioid
            		INNER JOIN titulo ON titoid = ctctitoid
            		INNER JOIN forma_cobranca ON titformacobranca = forcoid
            		     WHERE titdt_cancelamento IS NULL 
            			   AND titobs_cancelamento IS NULL
            			   AND ctcoid = (SELECT MAX(ctc1.ctcoid) AS ctcoid FROM controle_transacao_cartao ctc1 WHERE ctc1.ctctitoid = titoid)
            			$where
            		  ORDER BY ctctitoid, ctcoid DESC";

            	$resultado = array('titulos');

            	$cont = 0;

            	$rs = pg_query($this->conn, $sql);

            	while ($rTransacoes = pg_fetch_assoc($rs)) {

            		$resultado['titulos'][$cont]['ctctitoid']        = $rTransacoes['ctctitoid'];
            		$resultado['titulos'][$cont]['clinome']          = $rTransacoes['clinome'];
            		$resultado['titulos'][$cont]['clioid']           = $rTransacoes['ctcclioid'];
            		$resultado['titulos'][$cont]['status']           = $rTransacoes['ctcstatus'];
            		$resultado['titulos'][$cont]['titvl_titulo']     = $rTransacoes['valor_corrigido'];//$rTransacoes['titvl_titulo'];
            		$resultado['titulos'][$cont]['titdt_vencimento'] = $rTransacoes['titdt_vencimento'];
            		$resultado['titulos'][$cont]['forcnome']         = $rTransacoes['forcnome'];
            		$resultado['titulos'][$cont]['titvl_pagamento']  = $rTransacoes['titvl_pagamento'];
            		$resultado['titulos'][$cont]['ctcccchoid']       = $rTransacoes['ctcccchoid'];
            		$resultado['titulos'][$cont]['clitipo']          = $rTransacoes['clitipo'];
            		$resultado['titulos'][$cont]['titdt_pagamento']  = $rTransacoes['titdt_pagamento'];
            		$resultado['titulos'][$cont]['titdt_credito']    = $rTransacoes['titdt_credito'];
            		$resultado['titulos'][$cont]['dt_transacao']     = $rTransacoes['dt_transacao'];
            		$resultado['titulos'][$cont]['titformacobranca'] = $rTransacoes['titformacobranca'];
            		$resultado['titulos'][$cont]['ctcmotivo']		 = $rTransacoes['ctcmotivo'];

            		$cont++;
            	}

            	$resultado['total_registros'] = 'A pesquisa retornou ' . pg_num_rows($rs) . ' registro(s).';

            	return $resultado;

            }else{
            	return false;
            }
            
        }catch(Exception $e ) {
            return false;
        }
    }

	public function detalhesTransacao($titoid){
		try {            
            $sql = "SELECT ccchnumero_autorizacao FROM cliente_cobranca_credito_historico 
					WHERE ccchtitoid = $titoid ORDER BY ccchoid DESC LIMIT 1";

            $qryDetalhes = pg_query($this->conn, $sql);
            $detalhes = pg_fetch_all($qryDetalhes);

            return $detalhes;

		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
    public function detalhes()
    {
        try {
            $titoid = (isset($_POST['titoid'])) ? $_POST['titoid'] : null;
            
            $sql = "SELECT ccchtitoid,
                           ccchcupom_cliente,
                           ccchautorizadora,
                           ccchtipopagamento,
                           ccchnumero_autorizacao,
                           ccchnsu_autorizadora,
                           ccchstatustransacao,
                           ccchvalortransacao,
                           ccchmensagem,
                           ccchdt_resposta
                      FROM cliente_cobranca_credito_historico 
                     WHERE ccchtitoid = $titoid";
                     

            $qryDetalhes = pg_query($this->conn, $sql);
            
            $indice = 0;
            
            while ($rsDetalhe = pg_fetch_assoc($qryDetalhes)) {
                
                $detalhes[$indice]['ccchtitoid']             = $rsDetalhe['ccchtitoid'];
                $detalhes[$indice]['ccchcupom_cliente']      = $rsDetalhe['ccchcupom_cliente'];
                $detalhes[$indice]['ccchautorizadora']       = $rsDetalhe['ccchautorizadora'];
                $detalhes[$indice]['ccchtipopagamento']      = $rsDetalhe['ccchtipopagamento'];
                $detalhes[$indice]['ccchnumero_autorizacao'] = $rsDetalhe['ccchnumero_autorizacao'];
                $detalhes[$indice]['ccchnsu_autorizadora']   = $rsDetalhe['ccchnsu_autorizadora'];
                $detalhes[$indice]['ccchstatustransacao']    = $rsDetalhe['ccchstatustransacao'];
                $detalhes[$indice]['ccchvalortransacao']     = $this->_converteValor($rsDetalhe['ccchvalortransacao']);
                $detalhes[$indice]['ccchmensagem']           = str_replace(":"," - ",$rsDetalhe['ccchmensagem']);
                $detalhes[$indice]['ccchdt_resposta']        = $rsDetalhe['ccchdt_resposta'];
                
                $indice ++ ;
            } 
            
            return $detalhes;
         } catch (Exception $e) {
             die($e->getMessage());
         }
    }

    public function historico()
    {
        try {
            $titoid = (isset($_POST['titoid'])) ? $_POST['titoid'] : null;
            
            $sql = "SELECT ctcdt_inclusao,
                           ctcccchoid,
                           ctcmotivo
                      FROM controle_transacao_cartao 
                     WHERE ctctitoid = $titoid
                       AND ctctipotransacao != 'C'
                  ORDER BY ctcoid ASC";
                     

            $qryHistorico = pg_query($this->conn, $sql);
            
            $indice = 0;
            
            while ($rsHistorico = pg_fetch_assoc($qryHistorico)) {
                
                $historico[$indice]['ctcdt_inclusao'] = $rsHistorico['ctcdt_inclusao'];
                $historico[$indice]['ctcccchoid']   = $rsHistorico['ctcccchoid'];
                $historico[$indice]['ctcmotivo']      = $rsHistorico['ctcmotivo'];
                
                $indice ++ ;
            } 
            
            return $historico;
         } catch (Exception $e) {
             die($e->getMessage());
         }
    }

    public function retornaProximoDiaUtil()
    {
        return parent::retornaProximoDiaUtil();
    }
    
    public function incluirTransacaoCartao($clioid, $titoid, $idTransacao, $statusTransacao = false, $ccchoid)
    {
        return parent::incluirTransacaoCartao($clioid, $titoid, $idTransacao, $statusTransacao, $ccchoid);
    }
    
    /**
     * Recupera informações de cadastro do cartão atual
     * 
     * @param integer $clioid - id do cliente
     * 
     * @return Array
     */
    public function buscaDadosCartao($clioid)
    {
        return parent::buscaDadosCartao($clioid); 
    }
    
    /**
     * Inclui o registro na tabela cliente_cobranca_credito_historico em caso de confirmação 
     * dos dados do cartão e aprovação pela autorizadora. 
     * 
     * @param integer $clioid      - Id do Cliente
     * @param integer $idTransacao - Id da Transação 
     * @param integer $titoid      - Id do Título
     * @param array   $his         - Array de Retorno com os Dados do Pagamento
     * 
     * @return integer
     */
    public function incluiHistoricoPagamento($clioid, $idTransacao, $titoid, $hist)
    {
        return parent::incluiHistoricoPagamento($clioid, $idTransacao, $titoid, $hist);
    }
    
    /**
     * Após realizar todos os procedimentos de pagamento e validar,
     * baixar o título ao qual o pagamento se refere
     * 
     * @param integer $titoid     - id do Título
     * @param integer $ccchoid    - id da tabela cliente_cobranca_credito_historico
     * @param float   $valor_pago - Valor autorizado para cobrança pela operadora
     * @param integer $cd_usuario - Usuário responsável pela alteração do registro
     * 
     * @return boolean
     */
    public function confirmaPagamento($titoid, $ccchoid, $valor_pago, $cd_usuario)
    {
        return parent::confirmaPagamento($titoid, $ccchoid, $valor_pago, $cd_usuario);
    }
    
    /**
     * 
     */
    public function insereTituloCredito($titoid, $dt_venc)
    {
        return parent::insereTituloCredito($titoid, $dt_venc);
    }
    
    /**
     * Registra o erro na tabela controle_transacao_cartao
     * 
     * @param integer $idTransacao - Id do Histórico
     * @param boolean $motivo      - Motivo do erro
     * @param string  $nit         - Id da transação
     * 
     * @return integer
     */
    public function incluirTransacaoCartaoErro($idTransacao, $motivo, $nit)
    {
        return parent::incluirTransacaoCartaoErro($idTransacao, $motivo, $nit);
    }
   
    /**
     * Helper que converte o valor do título de Float para Int
     * 
     * @param integer $entrada - valor em formato integer
     * 
     * @return float $valor - valor convertido em formato float
     */
    private function _converteValor($entrada)
    {
        return number_format($entrada/100,2,".","");
    }
    
    /**
     * inicia transação com o BD
     */
    public function begin()
    {
        $rs = pg_query($this->conn, "BEGIN;");
    }
    
    /**
     * confirma alterações no BD
     */
    public function commit()
    {
        $rs = pg_query($this->conn, "COMMIT;");
    }
    
    /**
     * desfaz alterações no BD
     */
    public function rollback()
    {
        $rs = pg_query($this->conn, "ROLLBACK;");
    }
    
}