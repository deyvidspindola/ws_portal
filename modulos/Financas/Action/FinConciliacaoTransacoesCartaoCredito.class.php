<?php
/**
 * @author  Emanuel Pires Ferreira
 * @email   epferreira@brq.com
 * @since   18/12/2012
 * @package Finanças
 **/

require_once (_MODULEDIR_ . 'Financas/DAO/FinConciliacaoTransacoesCartaoCreditoDAO.class.php');
require_once _SITEDIR_.'lib/phpMailer/class.phpmailer.php';

//INTEGRAÇÃO TOTVS
require _SITEDIR_.'modulos/core/infra/autoload.php'; //Attempt to load CORE class
use module\Parametro\ParametroIntegracaoTotvs;
define('INTEGRACAO_TOTVS_ATIVA', ParametroIntegracaoTotvs::getIntegracaoTotvsAtiva());

//Inicio - [ORGMKTOTVS-1620] - bloqueio por integração específica
define('INTEGRACAO_ESITEF', ParametroIntegracaoTotvs::getIntegracao('INTEGRACAO_ESITEF'));

use module\WSProtheus\IntegracaoProtheusTotvs;

//Fim - [ORGMKTOTVS-1620] - bloqueio por integração específica

/**
 * Trata requisições do módulo financeiro para efetuar pagamentos 
 * de títulos com forma de cobrança 'cartão de crédito' 
 */
class FinConciliacaoTransacoesCartaoCredito {
    
    /**
     * Fornece acesso aos dados necessarios para o módulo
     * @property FinFaturamentoCartaoCreditoDAO
     */
    private $dao;
    
    /**
     * Construtor, configura acesso a dados e parâmetros iniciais do módulo
     */
    public function __construct() 
    {
        global $conn;
        
        $this->dao  = new FinConciliacaoTransacoesCartaoCreditoDAO($conn);
        $this->situacaoconciliacaotransacoescartaocredito = $this->dao->getSituacaoJobSoftexpressCartaoDeCredito(); 
        $this->diretorio = _SITEDIR_.'eSitef/relatorio_conciliacao/';
        if(is_dir($this->diretorio) === false) {
            mkdir($this->diretorio, 0777, true);
        }
    }
    
    /**
     * Função responsável por fazer o upload do arquivo
     * @return date
     */
    public function upload(){   
        
        $msgErro = array();

        
        try{
             
            $file = $_FILES['arqconciliacao'];

            $tipo_cron = $file['cron'];

            list($nome, $ext) = explode(".",$file['name']);

            $tiposPermitidos = array('application/vnd.ms-excel','text/csv','application/force-download');

            if($ext != 'csv'){
                 return 2;
            }

            if(file_exists($file['tmp_name']))
            {
                $fh = fopen($file['tmp_name'], 'r');

                $this->dao->begin();
            
                $dadosCsv = array();
                $dadosCsvEstorno=array();
                $dadosCsvEstornoEstornados = array();
                $dadosCsvEstornoErro = array();


                // faz a contagem da linha
                $linha = 1;
                $arquivo_OK = false;
                
                while (($data = fgetcsv($fh, 1000, ";")) !== FALSE) {
                    
                    $arquivo_OK = true;
                    //Efetua a leitura do arquivo e aplica as regras de acordo com o layout encontrado
                    $dados = $this->_confereArquivo($data, $linha);

                    if (is_array($dados)) {
                        
                        $dadosCsv[] = $dados['conciliacao'];
                        $dadosCsvEstorno[] = $dados['estorno']['dados_estorno'];
                        $dadosCsvEstornoEstornados[] = $dados['estorno']['estornados'];
                        $dadosCsvEstornoErro[] = $dados['estorno']['nao_estornados'];
                        // Cris alterado em 27/05/2019 
                        $dadosTitoid[] = $dados['conciliacao']['titoidFilho'];
                    }

                    
                    $linha ++;
                }

                if(!$arquivo_OK){
                	 throw new Exception('Erro no envio ou processamento do arquivo.');
                }
                
                //fecha o arquivo
                fclose($fh);

                
                //retira os índices no array que estão vazios
                $dadosCsv                  = array_filter($dadosCsv);
                $dadosCsvEstorno           = array_filter($dadosCsvEstorno);
                $dadosCsvEstornoEstornados = array_filter($dadosCsvEstornoEstornados);
                $dadosCsvEstornoErro       = array_filter($dadosCsvEstornoErro);

                
                //gera Cabeçalho para os recebidos no .pdf
                 $cabecalhoCsv = array(
	                 		"Cliente" => '10',
	                 		"Dt. Vencimento" => '10',
	                 		"Título Pai "=> '10',
	                 		"Parcela" => '13',
	                 		"Vlr Bruto" => '15',
	                 		"Status" => '10',
                                        "Dt Envio" => '10',
	                 		"Dt. Pagamento" => '10',
	                 		"Título" => '10',
	                 		"Vlr Pago" => '10',
	                		"Num autorização" => '20',
	                 		"Tx Adm" => '10',
	                		"Forma Cobrança" => '10'
                );
                
                 //gera cabeçalho para os títulos estornados no .pdf
                 $cabecalho_estorno_ok = array(
			                "Cliente" => '150',
		            		"Dt Estorno" => '35',
		            		"Título Pai "=> '30',
		            		"Parcela" => '20',
		            		"Vlr Estorno" => '20',
		            		"Dt Pagamento" => '35',
		            		"Título Crédito" => '30',
		            		"Cód Motivo" => '20',
		            		"Motivo" => '140',
                );
                 
                 //gera cabeçalho no .pdf dos dados de estorno do arquivo 
                 $cabecalho_dados_estorno = array(
                 		"Linha" => '10',
                 		"Cliente" => '200',
                 		"Núm Parcial Cartão" => '100',
                 		"Dt Estorno" => '35',
                 		"Título Pai "=> '30',
                 		"Vlr Estorno" => '20',
                 		"Dt Pagamento" => '35',
                 		"Vlr Líq. Estorno" => '30',
                 		"Tx Adm" => '20',
                 		"Cód Motivo" => '20',
                 		"Motivo" => '100',
                 		
                 );
                 //gera cabeçalho dos dados que não puderam ser estornados
                 $cabecalho_erro_estorno = array(
                 		"Linha" => '10',
                        "Vlr Bruto" => '20',
                 		"Vlr Liquido" => '20',
                 		"Dt Cancelamento" => '35',
                 		"Dt. Pagamento" => '35',
                 		"Núm Parcial Cartão" => '100',
                 		"Cód. Motivo" => '20',
                 		"Motivo" => '150'
                 		
                 );
                 
                 $nome_arquivo = explode(".",$file['name']);
                 $nomeArquivo =  $nome_arquivo[0] .date('_H-i-s');
                 
                 $gerarCsv = $this->gerarArquivoCsv($dadosCsv, $dadosCsvEstorno, $dadosCsvEstornoEstornados, $dadosCsvEstornoErro, $nomeArquivo.".csv");
                
                if ($gerarCsv['csvgerado'] == true) {
					
	              	
                	$html = utf8_encode($this->geraPdf($cabecalhoCsv, $dadosCsv, $cabecalho_estorno_ok, $dadosCsvEstorno, $cabecalho_dados_estorno, $dadosCsvEstornoEstornados, $cabecalho_erro_estorno, $dadosCsvEstornoErro, $dados));

                    //chama classe geração de PDF
                    if($tipo_cron == 'sim'){
                        require_once('../lib/html2pdf/html2pdf.class.php');
                    }else{
                        require_once('lib/html2pdf/html2pdf.class.php');
                        
                    }    
                    $html2pdf = new HTML2PDF('L','A4','en');
                    $html2pdf->WriteHTML($html);

//                    print_r(' diretorio:'.$this->diretorio.$nomeArquivo);
                    // Salva na pasta
                    $html2pdf->Output($this->diretorio.$nomeArquivo.'.pdf', "F");
                    
                    //finaliza a transação
                    $this->dao->commit();

                    $corpo_email = utf8_decode($html);

                    //INÍCIO INTEGRAÇÃO TOTVS
                
                    $dadosIntegracao = array();
                    $dadosIntegracao["operation"] = "esitef";
                    $dadosIntegracao["strOrigem"] = "FinConciliacaoTransacoesCartaoCredito.class.php";
                    $dadosIntegracao["idTitle"] = $dadosTitoid;

                    if(INTEGRACAO_TOTVS_ATIVA && INTEGRACAO_ESITEF){
                                                
                        $dadosIntegracao["integration"] = true;
                        IntegracaoProtheusTotvs::integraProtheusTotvs($dadosIntegracao);

                    }else{
                        // montar o json em caso da integraçao nao estar ativa para colocar na fila e enviar depois.
                        $dadosIntegracao["integration"] = false;
                        IntegracaoProtheusTotvs::integraProtheusTotvs($dadosIntegracao);
                        echo "<script> alert('" . _MSG_INTEGRACAO_ . "');</script>";
                    }
//                    exit;
                    ////////// FIM INTEGRACAO TOTVS
					$enviarEmail = $this->enviarEmail($corpo_email, true, $gerarCsv['anexo']);
                    return 1;
                } else {
                    throw new Exception('Erro ao importar o arquivo.');
                }

            } else {
                //Erro
                throw new Exception('Erro ao importar o arquivo.');
            }

        }catch(Exception $e){
        	
            $this->dao->rollback();
        	
            $msgErro['msg'] = $e->getMessage();
            $msgErro['cod'] = 0;
            
            return $msgErro;
        }
    }
    
    /**
    * Função que separa os dados conforme o tipo de registro existente no arquivo
    * @param array $data - Dados do registro
    * @return boolean - False quando registro diferente de venda e crédito
    */
    private function _confereArquivo($data, $linha) {


    	
    	$dados_arq = new StdClass();
    	
        // Registro  1 - Layout Venda
        // Registro 10 - Layout Crédito
        // Registro  2 - Layout Ajuste

    	
        if($data[0] == 1){
             
            $dados_arq->tipo_registro    = trim($data[0]);
            $dados_arq->tituloCredito    = trim($data[1]);
            $dados_arq->nsusitef         = trim($data[6]);
            $dados_arq->totalParcelas    = trim($data[9]);
            $dados_arq->titDtPagamento   = trim($data[11]);
            $dados_arq->num_parcela      = trim($data[12]);
            $dados_arq->valor_titulo     = trim($data[8]);  // Valor Título
            $dados_arq->valor_pago       = trim($data[10]); // Valor Líquido
            $dados_arq->vlr_taxa_servico = trim($this->_converteValor($data[20])); // Taxa Administrativa
            $bancoPagamento = 341; //$bancoPagamento = $data[]; - Não passaram o arquivo Conta Pagamento Layout 1, Problema: 1582 
        
        // Layout 10 - Detalhes Crédito
        }elseif($data[0] == 10){
        										 //Layout Software 
            $dados_arq->tipo_registro         = trim($data[0]);//C01 - Tipo de registro
            $dados_arq->tituloCredito         = trim($data[1]);//C02 - Identificador da transação
            $dados_arq->data_venda            = trim($data[3]);//C04 - Data da venda
            $dados_arq->num_resumo            = trim($data[4]);//C05 - Número do resumo
            $dados_arq->num_comprovante       = trim($data[5]);//C06 - Número do comprovante
            $dados_arq->nsusitef              = trim($data[6]);//C07 - NSU do SiTef
            $dados_arq->num_parcial_cartao    = trim($data[7]);//C08 - Número do cartão
            $dados_arq->valor_titulo          = trim($this->_converteValor($data[8]));//C09 - Valor bruto
            $dados_arq->totalParcelas         = (string)trim($data[9]);//C10 - Total parcelas
            $dados_arq->valor_pago            = trim($this->_converteValor($data[10]));//C11 - Valor líquido
            $dados_arq->titDtPagamento        = trim($data[12]);//C13 - Data crédito
            $dados_arq->num_parcela           = (string)trim($data[14]);//C15 - Número da parcela
            $dados_arq->bancoPagamento        = trim($data[18]);//C19 - Código do banco
            $dados_arq->cod_agencia           = trim($data[19]);//C20 - Código da agência
            $dados_arq->numero_conta_corrente = trim($data[20]);//C21 - Número da conta corrente
            $dados_arq->vlr_comissao          = trim($this->_converteValor($data[21]));//C22 - Valor da comissão R$
            $dados_arq->vlr_taxa_servico      = trim($this->_converteValor($data[22]));//C23 - Valor da taxa de serviço %
            $dados_arq->cod_autorizacao       = str_pad(trim($data[24]), 6, '0', STR_PAD_LEFT);//C25 - Código de autorização 
            $dados_arq->cupom_fiscal          = trim($data[25]);//C26 - Cupom fiscal
            $dados_arq->cod_bandeira          = trim($data[26]);//C27 - Código da bandeira

            
            //consulta se  os dados do arquivo já estão na tabela
            $ret_dados_gravados = $this->dao->getDadosGravados($dados_arq);
            
            
            if(!$ret_dados_gravados){
            	//grava dados lidos do arquivo de retorno
            	$this->dao->setDadosArquivoConciliacao($dados_arq);

            }
            
            //verifica os dados do título para baixar caso existe na base
            $dadosTituloBaixar = $this->dao->buscaDadosHistorico($dados_arq);
               
           
            if(is_array($dadosTituloBaixar)){
            
            	$cd_usuario = 2750;
            
            	$titoid  = $dadosTituloBaixar['titoid'];
            	$ccchoid = $dadosTituloBaixar['ccchoid'];
            
            	$status = $dadosTituloBaixar['status'];
            
            	if ( $dados_arq->valor_pago == 0 ||  $dados_arq->valor_pago == ''){
            		$status = 'CANCELADO';
            	}

            
            	//efetua a baiza do título
            	$ret_confirma = $this->dao->confirmaPagamento($dados_arq->titDtPagamento,
            			$titoid,
            			$ccchoid,
            			$dados_arq->valor_titulo,
            			$dados_arq->valor_pago,
            			$dados_arq->vlr_comissao ,
            			$cd_usuario,
            			$status,
            			$dados_arq->bancoPagamento);
            
            	if ($dados_arq->totalParcelas == 0){
            		$parcelas = 0;
            	} else {
            		//espaço em branco para o CSV não interpretar como uma data
            		$parcelas = ' '.$dados_arq->num_parcela.'/'.$dados_arq->totalParcelas;
            	}
            	
            	// retorna os dados para gerar o CSV e o PDF
            	$retorno['conciliacao'] = array (
            			trim ( $dadosTituloBaixar ['clinome'] ),
            			$this->_converteData ( $dadosTituloBaixar ['titdt_vencimento'] ),
            			$dados_arq->tituloCredito,
            			$parcelas,
            			number_format( $dados_arq->valor_titulo, 2, ',', '.' ),
            			$status,
            			date ( 'd/m/Y' ),
            			$this->inverterData($dados_arq->titDtPagamento),
            			$dadosTituloBaixar ['titoid'],
            			number_format( $dados_arq->valor_pago, 2, ',', '.' ),
            			$dados_arq->nsusitef,
            			number_format( $dados_arq->vlr_comissao, 2, ',', '.' ),
            			$dadosTituloBaixar ['forcnome'],
                        
            	);
                 // Cris alterado em 27/05/2019 
                $retorno['conciliacao']['titoidFilho'] = $dadosTituloBaixar ['titoidFilho'];
            	
                return $retorno;
            	
            }else{
            	
            	return false;
            }
         
            
         //Layout 2 (Ajustes) Estorno
        }elseif($data[0] == 2){
        									  // Layout Software Express	
        	$dados_arq->tipo_registro         = trim($data[0]); //A01 - Tipo de Registro
        	$dados_arq->data_ajuste           = trim($data[2]); //A03 - Data do Ajuste
            $dados_arq->valor_titulo          = trim($this->_converteValor(abs($data[3]))); //A04 - Valor do Ajuste // Valor bruto
            $dados_arq->valor_pago            = trim($this->_converteValor(abs($data[4]))); //A05 - Valor Líquido do Ajuste
            $dados_arq->num_resumo            = trim($data[5]); //A06 - Número do Resumo
            $dados_arq->num_parcial_cartao    = trim($data[6]); //A07 - Número do cartão
            $dados_arq->num_comprovante       = trim($data[7]); //A08 - Número do comprovante
            $dados_arq->data_venda            = trim($data[8]); //A09 - Data da venda 
            $dados_arq->cod_motivo_ajuste     = trim($data[9]); //A10 - Cód. motivo do ajuste
            $dados_arq->desc_motivo_ajuste    = trim($data[10]); //A11 - Descrição motivo ajuste
            $dados_arq->bancoPagamento        = trim($data[15]); //A16 - Código do banco 
            $dados_arq->cod_agencia           = trim($data[16]); //A17 - Código da agência
            $dados_arq->numero_conta_corrente = trim($data[17]); //A18 - Número da conta corrente
            $dados_arq->vlr_comissao          = trim($this->_converteValor($data[18])); //A19 - Valor da comissão R$ 
            $dados_arq->vlr_taxa_servico      = trim($this->_converteValor($data[19])); //A20 - Valor da taxa de serviço %
        	
            //verifica se o registro já está armazenado para não haver duplicidade
            $ret_dados_gravados = $this->dao->getDadosGravados($dados_arq);

            
            if(!$ret_dados_gravados){
            	//grava dados lidos do aquivo de retorno
            	$this->dao->setDadosArquivoConciliacao($dados_arq);
            }
            
            //só realiza a pesquisa do título se tiver número do cartão e data de venda válida
            if(!empty($dados_arq->num_parcial_cartao) && $this->dao->validarData($dados_arq->data_venda) == true){
            	//pesquisa se os dados para estorno existe na base
            	$dados_estorno = $this->dao->getDadosEstornoTitulo($dados_arq);
            }
            
            if(is_array($dados_estorno)){
            	
            	foreach ($dados_estorno as $valor_estorno) {
            		
	            	$dadosEstorno = new StdClass();
	            	//recupera dados referente ao estorno
	            	foreach ($valor_estorno['dados_layout_2'] AS $dados){
	            		$dadosEstorno->cod_motivo  = $dados['arcccod_motivo_ajuste'];
	            		$dadosEstorno->desc_motivo = $dados['arcccdesc_motivo_ajuste'];
	            	}
	            	
	            	//recupera os id dos títulos para estornar 
	            	foreach ($valor_estorno['dados_layout_10'] AS $dados){
	            		
	            		if(!empty($dados['titoid'])){
	            			//estorna o título
	            			$this->dao->setEstornarTitulo($dadosEstorno, $dados['titoid']);
	            		}
	            		
	            		$retorno['estorno']['estornados'][]= array(
	            				trim($dados['clinome']),
	            				$this->_converteData($valor_estorno['dados_layout_2'][0]['arccdt_ajuste']),
	            				empty($dados['arcctitoid']) ? $dados['titoid']: $dados['arcctitoid'],
	            				$dados['titno_parcela'],
	            				number_format( $dados['titvl_titulo'], 2, ',', '.' ),
	            				$this->_converteData($dados['arccdt_venda']),
	            				!empty($dados['arcctitoid']) ? $dados['titoid']: ' -- ',
	            				$valor_estorno['dados_layout_2'][0]['arcccod_motivo_ajuste'],
	            				$valor_estorno['dados_layout_2'][0]['arcccdesc_motivo_ajuste']
	            		);
	            		
	            	}// fim foreach layout 10
	            	
	            	
	            	### retorna dados do layout 2
	            	$retorno['estorno']['dados_estorno'][]= array(
	            	    $linha,
		            	trim($valor_estorno['dados_layout_10'][0]['clinome']),
		            	$valor_estorno['dados_layout_2'][0]['arccnumero_cartao'],
		            	$this->_converteData($valor_estorno['dados_layout_2'][0]['arccdt_ajuste']),
		            	$valor_estorno['dados_layout_10'][0]['titoid'],
		            	//$dados_estorno['dados_layout_10'][0]['arcctotal_parcelas'],
		            	number_format($valor_estorno['dados_layout_2'][0]['arccvlr_bruto'], 2, ',', '.' ),
		            	$this->_converteData($valor_estorno['dados_layout_2'][0]['arccdt_venda']),
		            	//$dados_estorno['dados_layout_10'][0]['titoid'],
		            	number_format($valor_estorno['dados_layout_2'][0]['arccvlr_liquido'], 2, ',', '.' ),
		            	//$dados_estorno['dados_layout_2'][0]['arcccod_autorizacao'],
		            	//$dados_estorno['dados_layout_2'][0]['arccpercentual_taxa_servico'],
		            	number_format($valor_estorno['dados_layout_2'][0]['arccvlr_comissao'], 2, ',', '.' ),
		            	$valor_estorno['dados_layout_2'][0]['arcccod_motivo_ajuste'],
		            	$valor_estorno['dados_layout_2'][0]['arcccdesc_motivo_ajuste'],
	            	);
            	
            	}//fim foreach
            	
            	return $retorno;
            	
            }else{
            	
            	//Retorna os dados do arquivo não estornados
            	$retorno['estorno']['nao_estornados']= array(
            	     $linha,
            		 number_format($dados_arq->valor_titulo, 2, ',', '.' ),
            	     number_format($dados_arq->valor_pago, 2, ',', '.' ),
	                 $this->inverterData($dados_arq->data_ajuste),
	                 $this->inverterData($dados_arq->data_venda),
	                 $dados_arq->num_parcial_cartao,
	                 $dados_arq->cod_motivo_ajuste,
	                 $dados_arq->desc_motivo_ajuste
            	);
            	
            	return $retorno;
            }
           
        }else{
            return false;
        }
		
		
	}
    
    public function envioConcluido(){
        return 2;
    }

    public function getRelatorio() {
        $dataMaxima = strtotime('-7 day', mktime());
        $arquivosPasta = array();
        foreach (new DirectoryIterator($this->diretorio) as $fileInfo) {
            if($fileInfo->isDot()) continue;
                if ($fileInfo->getMTime() <= $dataMaxima)
                {
                    $excluido = unlink($this->diretorio.$fileInfo->getFilename());   
                } else {
                    $arquivosPasta[] = array (
                        'data_hora' => $fileInfo->getMTime(),
                        'titulo' => $fileInfo->getFilename(),
                        'ext' => pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION)
                    );
                }
            }
            rsort($arquivosPasta);
        return $arquivosPasta;
    }
    
    public function geraPdf($cabecalho, $dadosCsv, $cabecalho_estorno_ok, $dadosCsvEstorno, $cabecalho_dados_estorno, $dadosCsvEstornoEstornados, $cabecalho_erro_estorno, $dadosCsvEstornoErro, $dados)
    {      
        ob_start();
        
        include(_MODULEDIR_.'Financas/View/fin_conciliacao_transacoes_cartao_credito/relatorio.php');
        
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Gerar arquivo CSV
     * 
     * @param unknown $nome_arquivo
     * @throws Exception
     */
    public function gerarArquivoCsv($dadosCsv, $dadosCsvEstorno, $dadosCsvEstornoEstornados, $dadosCsvEstornoErro,$nome_arquivo){
    		
    	$planilhaRelatorio = $this->diretorio . $nome_arquivo;
		
		$handle = fopen ( $planilhaRelatorio, "w" );
		
		if (! $handle) {
			throw new Exception ( 'Erro ao gerar o arquivo csv.' );
		}
		
		$linha = "";
		$linha .= '"Relatório Conciliação:";';
		$linha .= "\r\n";
		$linha .= "\r\n";
		fwrite ( $handle, $linha );
		
		// Cabeçalho das colunas do títulos conciliados(baixados)
		$linha = "";
		$linha .= '"Cliente";';
		$linha .= '"Dt. Vencimento";';
		$linha .= '"Título Pai";';
		$linha .= '"Parcela";';
		$linha .= '"Vlr Bruto";';
		$linha .= '"Status";';
		$linha .= '"Dt Envio";';
		$linha .= '"Dt. Pagamento";';
		$linha .= '"Título";';
		$linha .= '"Vlr Pago";';
		$linha .= '"Num autorização";';
		$linha .= '"Tx Adm";';
		$linha .= '"Forma Cobrança";';
		$linha .= "\r\n";
		fwrite ( $handle, $linha );
			
		// Detalhe dos Títulos Baixados
		foreach ( $dadosCsv as $conciliacao ) {
			
			list ( $nome, 
					$dt_vencimento, 
					$titulo,
					$parcela, 
					$vl_bruto, 
					$status,
					$dt_envio,
					$dt_pagamento,
					$num_titulo,
					$vl_pago,
					$num_autorizacao,
					$tx_adm,
					$for_cobranca ) = $conciliacao;
			
			$linha = "";
			$linha .= $nome . ';';
			$linha .= $dt_vencimento . ';';
			$linha .= $titulo . ';';
			$linha .= $parcela . ';';
			$linha .= $vl_bruto . ';';
			$linha .= $status . ';';
			$linha .= $dt_envio . ';';
			$linha .= $dt_pagamento . ';';
			$linha .= $num_titulo . ';';
			$linha .= $vl_pago . ';';
			$linha .= $num_autorizacao . ';';
			$linha .= $tx_adm . ';';  
			$linha .= $for_cobranca . ';';
			$linha .= "\r\n";
			fwrite ( $handle, $linha );


		}
			
		// Títulos estornados
		$linha = "";
		$linha .= "\r\n";
		$linha .= "\r\n";
		$linha .= '"Título(s) de Crédito Estornado(s):";';
		$linha .= "\r\n";
		$linha .= "\r\n";
		fwrite ( $handle, $linha );
		
		// Cabeçalho das colunas do títulos estornados
		$linha = "";
		$linha .= '"Cliente";';
		$linha .= '"Dt Estorno";';
		$linha .= '"Título Pai";';
		$linha .= '"Parcela";';
		$linha .= '"Vlr Estorno";';
		$linha .= '"Dt Pagamento";';
		$linha .= '"Título Crédito";';
		$linha .= '"Cód Motivo";';
		$linha .= '"Motivo";';
		$linha .= "\r\n";
		fwrite ( $handle, $linha );
		
		foreach ( $dadosCsvEstornoEstornados as $dados_titulos ) {
			// Detalhe dos Títulos Estornados
			foreach ( $dados_titulos as $dadosEstornados ) {
				
				list ( $nome, 
						$dt_estorno,
						$titulo_pai, 
						$parcela, 
						$vlr_estorno,
						$dt_pagamento,
						$titulo_credito, 
						$cod_motivo, 
						$motivo ) = $dadosEstornados;
				
				$linha = "";
				$linha .= $nome . ';';
				$linha .= $dt_estorno . ';';
				$linha .= $titulo_pai . ';';
				$linha .= $parcela . ';';
				$linha .= $vlr_estorno . ';';
				$linha .= $dt_pagamento . ';';
				$linha .= $titulo_credito . ';';
				$linha .= $cod_motivo . ';';
				$linha .= $motivo . ';';
				$linha .= "\r\n";
				fwrite ( $handle, $linha );
			}
		}
			
		// Dados dos títulos estornados do arquivo
		$linha = "";
		$linha .= "\r\n";
		$linha .= "\r\n";
		$linha .= '"Dados de estornos do arquivo:";';
		$linha .= "\r\n";
		$linha .= "\r\n";
		fwrite ( $handle, $linha );
		
		// Cabeçalho das colunas do títulos estornados
		$linha = "";
		$linha .= '"Linha";';
		$linha .= '"Cliente";';
		$linha .= '"Núm Parcial Cartão";';
		$linha .= '"Dt Estorno";';
		$linha .= '"Título Pai";';
		$linha .= '"Vlr Estorno";';
		$linha .= '"Dt Pagamento";';
		$linha .= '"Vlr Líq. Estorno";';
		$linha .= '"Tx Adm";';
		$linha .= '"Cód Motivo";';
		$linha .= '"Motivo";';
		$linha .= "\r\n";
		fwrite ( $handle, $linha );
		
		// Detalhe dos Títulos Estornados
		foreach ( $dadosCsvEstorno as $dados_estornos_arquivo ) {
		
			foreach ( $dados_estornos_arquivo as $dados_estornos_arq) {
			
				list ( $linha_arq, 
						$nome, 
						$num_parcial_cartao, 
						$dt_estorno, 
						$titulo_pai, 
						$vlr_estorno, 
						$dt_pagamento,
						$vlr_liq_estorno, 
						$tx_adm, 
						$cod_motivo,
						$motivo 
						) = $dados_estornos_arq;
				
				$linha = "";
				$linha .= $linha_arq . ';';
				$linha .= $nome . ';';
				$linha .= $num_parcial_cartao . ';';
				$linha .= $dt_estorno . ';';
				$linha .= $titulo_pai . ';';
				$linha .= $vlr_estorno . ';';
				$linha .= $dt_pagamento . ';';
				$linha .= $vlr_liq_estorno . ';';
				$linha .= $tx_adm . ';';  
				$linha .= $cod_motivo . ';';
				$linha .= $motivo . ';';
				$linha .= "\r\n";
				fwrite ( $handle, $linha );
			}
		}
			
		
		// Títulos de crédito não estornados
		$linha = "";
		$linha .= "\r\n";
		$linha .= "\r\n";
		$linha .= '"O(s) dado(s) de Crédito de Cartão abaixo não foi(ram) estornado(s):";';
		$linha .= "\r\n";
		$linha .= "\r\n";
		fwrite ( $handle, $linha );
		
		// Dados dos valores que não puderam ser estornados
		$linha = "";
		$linha .= '"Linha";';
		$linha .= '"Vlr Bruto";';
		$linha .= '"Vlr Liquidoo";';
		$linha .= '"Dt Cancelamento";';
		$linha .= '"Dt. Pagamento";';
		$linha .= '"Núm Parcial Cartão";';
		$linha .= '"Cód Motivo";';
		$linha .= '"Motivo";';
		$linha .= "\r\n";
		fwrite ( $handle, $linha );
		
		// Detalhe dos dados que não foram Estornados
		foreach ( $dadosCsvEstornoErro as $dados_estornos_erro ) {
			
			list ( $linha_arq, 
					$vlr_bruto,
					$vlr_liq,
					$dt_cancelamento,
					$dt_pagamento, 
					$num_parcial_cartao, 
					$cod_motivo,
					$motivo ) = $dados_estornos_erro;
			
			$linha = "";
			$linha .= $linha_arq . ';';
			$linha .= $vlr_bruto . ';';
			$linha .= $vlr_liq . ';';
			$linha .= $dt_cancelamento . ';';
			$linha .= $dt_pagamento . ';';
			$linha .= $num_parcial_cartao . ';';
			$linha .= $cod_motivo . ';';
			$linha .= $motivo . ';';
			$linha .= "\r\n";
			fwrite ( $handle, $linha );
		}
		
		//fecha o arquivo criado
		fclose ( $handle );

		$dadosretornoCSV['anexo'] = $planilhaRelatorio;
        $dadosretornoCSV['csvgerado'] = true;

		return $dadosretornoCSV;
    }
    
    /**
     * Helper que converte o valor do título de Float para Int
     * 
     * @param integer $entrada - valor em formato integer
     * 
     * @return float $valor - valor convertido em formato float
     */
    private function _converteValor($entrada){
        return number_format($entrada/100,2,".","");
    }

    private function _converteData($data) {
        return implode('/', array_reverse(explode('-', $data)));
    }
    
    private function inverterData($data){
    	
		$dia = substr($data,6,2);
		$mes = substr($data,4,2);
		$ano = substr($data,0,4);
		
		return $dia.'/'.$mes.'/'.$ano;
    }

    private function enviarEmail($msg, $status, $anexo = NULL){
        //recupera os dados do usuário que iniciou o processo
        $emailGrupoEmail = $this->dao->getDadosGrupoEmail();

        if(is_array($emailGrupoEmail)){
            $nomeGrupoEmail = $emailGrupoEmail[0]['pcsdescricao'];

            //verifica se o grupo possui email cadastrado
            if(empty($nomeGrupoEmail)){
                $msg_erro_email = 'Falha ao enviar e-mail : Usuário [ '.$emailGrupoEmail[0]['pcsdescricao'].' ] , não possui e-mail cadastrado.';
                return true;
            }else{
                $assunto = 'Conciliação Cartão de Crédito - Retorno';

                if($status){
                    $corpo_email = 'Sr(a). '.$emailGrupoEmail[0]['pcsdescricao'].' o processamento do arquivo de retorno foi concluído, segue anexo o relatório de processamento. <br/><br/>';
                    $corpo_email .= $msg;
                }

                //recupera e-mail de testes
                if($_SESSION['servidor_teste'] == 1){
                    //recupera email de testes da tabela parametros_configuracoes_sistemas_itens
                    $emailTeste = $this->dao->getEmailTeste();

                    if(!is_object($emailTeste)){
                        $emailGrupoEmail[0]['usuemail'] = 'teste_desenv@sascar.com.br';
                        throw new exception('E necessario informar um e-mail de teste em ambiente de testes.');
                    }else{
                        $emailGrupoEmail[0]['usuemail'] = $emailTeste->pcsidescricao;
                    }
                }

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->From = "sascar@sascar.com.br";
                $mail->FromName = "sistema@sascar.com.br";
                $mail->Subject = $assunto;
                $mail->MsgHTML($corpo_email);
                $mail->ClearAllRecipients();
                $mail->AddAddress($emailGrupoEmail[0]['usuemail']);
                $mail->AddAttachment($anexo);

                if(!$mail->Send()) {
                    $msg_erro_email = ' - Falha ao enviar e-mail';
                    return false;
                }
                return true;
            }
        }
        return false;
    }

}
