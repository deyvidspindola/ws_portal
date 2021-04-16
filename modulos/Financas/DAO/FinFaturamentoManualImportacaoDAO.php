<?php

set_time_limit(0);

class FinFaturamentoManualImportacaoDAO
{
    protected $_adapter;
	protected $_logFileI = "/var/www/docs_temporario/log_fat_inserir_item.csv";
	protected $_logFileN = "/var/www/docs_temporario/log_fat_nova_nota.csv";
    protected $_logMessages = array();
    
    private $vo;
    
    public function __construct()
    {
        global $conn;        
        $this->_adapter = $conn;
    }
    
    /**
     * Executa uma query
     * @param    string        $sql        SQL a ser executado
     * @return    resource
     */
    protected function _query($sql)
    {
        // Suprime erros para lançar exceção ao invés de E_WARNING
        $result = pg_query($this->_adapter, $sql);
        
        if ($result === false)
        {
            throw new Exception(pg_last_error($this->_adapter));
        }
        
        return $result;
    }
    
    /**
     * Conta os resultados de uma consulta
     * @param    resource    $results
     * @return    int
     */
    protected function _count($results)
    {
        return pg_num_rows($results);
    }
    
    /**
     * Retorna os resultados de uma consulta num array associativo (hash-like)
     * @param    resource    $results
     * @return    array
     */
    protected function _fetchAll($results)
    {
        return pg_fetch_all($results);
    }
    
    /**
     * Retorna o resultado de uma coluna num array associativo (hash-like)
     * @param    resource    $results
     * @return    array
     */
    protected function _fetchAssoc($result)
    {
        return pg_fetch_assoc($result);
    }
    
    /**
      * Retorna o resultado como um vetor de objetos
      * @param  resource     $results
      * @return  array
      */
    public function _fetchObj($results)
    {
        $rows = array_map(function($item) { 
            return (object) $item; 
        }, $this->_fetchAll($results)); 
        
        return $rows;
    }
    
    /**
     * Insere valores numa tabela
     * @param    string    $table
     * @param    array    $values
     * @return    boolean
     */
    protected function _insert($table, $arr)
    {
        // Suprime erros para lançar exceção ao invés de E_WARNING
        $result = pg_insert($this->_adapter, $table, $arr);
        
        if ($result === false)
        {
            throw new Exception(pg_last_error($this->_adapter));
        }
        
        return $result;
    }
    
    /**
     * Escapa os elementos de um vetor
     * @param    array    $arr
     * @return    array
     */
    protected function _escapeArray($arr)
    {
        array_walk($arr, function(&$item, $key) {
            $item = pg_escape_string($item);
        });
        
        return $arr;
    }
    
    public function importarRegistros($operacao, $resultados)
    {
        // Valida a quantidade de registros do arquivo
        if (count($resultados) == 0)
        {
            throw new Exception('O arquivo não contém nenhum registro válido.');
        }
    
        // Importa itens de forma diferenciada dependendo da operação
        $this->_query('BEGIN');
        
        try
        {
            if ($operacao == 1)
            {
                $count = $this->_inserirItemsNota($resultados);
            }
            elseif ($operacao == 2)
            {
                $count = $this->_emitirNovaNota($resultados);
            }
            
            $this->_query('COMMIT');
            return $count;
        }
        catch (Exception $e)
        {
            // Força rollback em exceção de qualquer transação
            $this->_query('ROLLBACK');
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Inserção de novo item em uma nota fiscal
     *
     * @param   array   $itens
     * @return  boolean
     */
    protected function _inserirItemsNota($itens)
    {  
    	  
    	$this->_logMessages = array();
        $itemCount = 0; 
        $errolinha = false; 
        $linhasSucesso = 0;   
        foreach ($itens as $item)
        {           
            $itemCount += 1;
            
            $nota=false;
            if(is_numeric($item['nflno_numero']))
            {
	            $sql = "SELECT
	                        *
	                    FROM
	                        nota_fiscal
	                    WHERE
	                        nflno_numero = {$item['nflno_numero']}
	                        AND nflserie = '{$item['nflserie']}'";                        
	            $nota = $this->_fetchAssoc($this->_query($sql));
            }
            
            // Para Importação de arquivo, opção 1, a NF de origem deve existir em base de dados.
            if (!$nota)
            {
            	$errolinha = true;
                $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: a nota {$item['nflno_numero']} não existe no banco de dados.";
            }

            // Para Importação de arquivo, opção 1, a NF de origem não pode estar cancelada.
            if (strlen($nota['nfldt_cancelamento']))
            {
            	$errolinha = true;
                $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: a nota {$item['nflno_numero']} já foi cancelada.";
            }

            // Para Importação de arquivo, opção 1, a NF de origem não pode ter sido enviada para a gráfica.
            if (strlen($nota['nfldt_envio_grafica']))
            {
            	$errolinha = true;
            	$this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: a nota {$item['nflno_numero']} já foi enviada para a gráfica.";
            }

            // Para Importação de arquivo, opção 1, a NF de origem não pode ter NF-e.
            if (strlen($nota['nflremessa_nfe']))
            {
            	$errolinha = true;
                $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: a nota {$item['nflno_numero']} possui NF-e.";
            }

            // Para importação de arquivo, o valor unitário é uma informação obrigatória e não pode ser igual a 0,00.
            if (!isset($item['titvl_titulo']) || floatval($item['titvl_titulo']) == 0.00)
            {
            	$errolinha = true;
                $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: o valor unitário é igual a 0.";
            }

            // Em layout, o tipo do item é uma informação obrigatória e seu conteúdo deve ser igual a "L" ou "M".
            if (!isset($item['nfitipo']) || !in_array(strtoupper($item['nfitipo']), array('L', 'M')))
            {
            	$errolinha = true;
                $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: o tipo do item é diferente de L e/ou M.";
            }

            // Para importação de arquivo, o contrato deve existir em base de dados.
            // Para importação de arquivo, o contrato deve ter status Ativo.
            //if (!isset(empty['connumero']))
            //{
            //	$errolinha = true;
            //    $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: o número do contrato não foi fornecido.";
            //}

            //contrato nao é obrigatório.
            if (!empty($item['connumero'])){
	            $contrato=false;
	            if(is_numeric($item['connumero'])) 
	            {
		            $sql = "SELECT
		                        *
		                    FROM
		                        contrato
		                    WHERE
		                        connumero = ".intval($item['connumero'])."
		                        AND condt_exclusao IS NULL
		                        AND concsioid = 1";	                        
		            $contrato = $this->_fetchAssoc($this->_query($sql));
	            }
	            
	            if (!$contrato)
	            {
	            	$errolinha = true;
	                $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: o contrato ".intval($item['connumero'])." não existe ou não está ativo.";
	            }
        	}

            // Para importação de arquivo, a obrigação financeira é uma informação obrigatória.
            // Deve ser uma obrigação financeira ativa e deve existir em cadastro de obrigação financeira.
            if (!isset($item['nfiobroid']))
            {
            	$errolinha = true;
                $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: a obrigação financeira não foi fornecida.";
            }

            $obrigacao=false;
            if(is_numeric($item['nfiobroid'])){
	            $sql = "SELECT
	                        *
	                    FROM
	                        obrigacao_financeira
	                    WHERE obroid = {$item['nfiobroid']}
                          AND obrdt_exclusao is null";
	            $obrigacao = $this->_fetchAssoc($this->_query($sql));
            }
            if (!$obrigacao)
            {
            	$errolinha = true;
                $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: a obrigação financeira {$item['nfiobroid']} não existe.";
            }


            //EXECUTA TAREFAS DE UPDATE DA NOTA
            if(!$errolinha){
	            // Insere novo item na nota fiscal
	            $dataReferencia = date("Y-m-01"); 
                
	            $sql = "SELECT
	                        *
	                    FROM
	                        contrato_obrigacao_financeira
	                    WHERE cofconoid = ".intval($item['connumero'])."
	                      AND cofobroid = {$item['nfiobroid']}
                          AND cofdt_termino is null";
                $obrigacao_valor = $this->_fetchAssoc($this->_query($sql));
                $obrigacao_valor['cofvl_obrigacao'] = floatval($obrigacao_valor['cofvl_obrigacao']) > 0 ? floatval($obrigacao_valor['cofvl_obrigacao']) : floatval($obrigacao['obrvl_obrigacao']);
                
                
	            $sql = "INSERT INTO nota_fiscal_item ( 
	                        nfino_numero
	                      , nfiserie
	                      , nficonoid
	                      , nfiobroid
	                      , nfids_item
	                      , nfivl_item
	                      , nfidt_referencia
	                      , nfidesconto
	                      , nfidt_inclusao
	                      , nfinfloid
	                      , nfivl_obrigacao
	                      , nfitipo
	     				  , nfinota_ant
	                    ) VALUES ( 
	                        {$nota['nflno_numero']}
	                      , '{$nota['nflserie']}'
	                      , ".(empty($item['connumero']) ? 'null': intval($item['connumero']))."
	                      , {$obrigacao['obroid']}
	                      , '{$obrigacao['obrobrigacao']}'
	                      , {$item['titvl_titulo']}
	                      , '{$nota['nfldt_referencia']}'
	                      , {$item['titvl_desconto']}
	                      , NOW()
	                      , {$nota['nfloid']}
	                      , {$obrigacao_valor['cofvl_obrigacao']}
	                      , '{$item['nfitipo']}'
	     				  , {$nota['nflno_numero']}
	                    )";
	            $this->_query($sql);
                
                // Atualiza a nota fiscal e recalcula impostos
	            // Dentro do loop porque existem itens da nota_fiscal_item sem nfinfloid,
	            // e isso ferra tudo.
	            $sql = "SELECT 
	                        SUM(nfivl_item)  AS valor_total
	                      , SUM(nfidesconto) AS valor_descontos
	                    FROM
	                        nota_fiscal_item
	                    WHERE
	                        nfino_numero = {$item['nflno_numero']}
	                        AND nfiserie = '{$item['nflserie']}'";              
	                            
	            $itensNota = $this->_fetchAssoc($this->_query($sql));
                
                //Retorna os dados do cliente
                if (is_numeric($nota['nflclioid'])){
                    $sql = "SELECT 
                                    * 
                                FROM 
                                    clientes
                                WHERE
                                    clioid = '{$nota['nflclioid']}'";
                    $cliente = $this->_fetchAssoc($this->_query($sql)); 
                }


				//Calcula os impostos
				$impostos = $this->calculaImpostos($cliente, $itensNota['valor_total']);

                //Calcula o totais dos impostos
                $impostosPisCofinsCsll = (float) floatval($impostos['pis']) + floatval($impostos['cofins']) + floatval($impostos['csll']);
                
	            $sql = "UPDATE nota_fiscal
	                    SET
	                        nflvl_total      = {$itensNota['valor_total']}
	                      , nflvl_desconto   = {$itensNota['valor_descontos']}
                          , nflvlr_pis 	     = {$impostos['pis']}
                          , nflvlr_cofins    = {$impostos['cofins']}
                          , nflvlr_csll 	 = {$impostos['csll']}
                          , nflvlr_ir        = {$impostos['ir']}
                          , nflvlr_iss       = {$impostos['iss']}
                          , nflvlr_piscofins = $impostosPisCofinsCsll
                          , nflnatureza      = 'PRESTACAO DE SERVICOS'
                          , nfltransporte    = 'RODOVIARIO' 
	                    WHERE
	                        nfloid = {$nota['nfloid']}";
	            $this->_query($sql);  
	            
	            // Atualiza título da nota
	            $dataVencimento = 'NOW()';
	            
                //Percorre os titulos da nf
                $sql = "SELECT 
                                *
                        FROM 
                                titulo
                        WHERE
                                titnfloid = {$nota['nfloid']}";
                $titulos = $this->_fetchAll($this->_query($sql)); 
                
                
                if (is_array($titulos)){
                    $numeroTitulos = count($titulos);
                    //Valor do Titulo por parcela
                    $valorParcelaTitulo = 0;
                    if ($itensNota['valor_total'] > 0){
                        $valorParcelaTitulo = round(  ( ( $itensNota['valor_total'] - $itensNota['valor_descontos']) / $numeroTitulos ), 2);
                    }
                    //impostos por parcelas de titulo.
                    if($impostos['iss'] > 0){
                        $valorIssParcela = round($impostos['iss']/$numeroTitulos ,2);
                    }
                    $valorIssTitulo        = 0;
                    $valorIrTitulo         = 0;
                    $valorPisCofinsParcela = ($impostosPisCofinsCsll > 0) ? round( ($impostosPisCofinsCsll / $numeroTitulos), 2) :  0;
                    foreach($titulos as $titulo){

                        //Valor do ISS por parcela
                        $valorIssTitulo     = $valorIssParcela;

                        //Valor do titulo
                        $valorTitulo = $valorParcelaTitulo;

                        //Valor do IR por parcela
                        $valorIrTitulo = $impostos['ir'];
                        if ($impostos['ir'] > 0 && $numeroTitulos > 1){
                            $valorIrTitulo = round($impostos['ir']/$numeroTitulos ,2);     		
                        }
                        
                        //Valor da soma dos impostos (PIS + Cofins + CSLL)
                        $valorPisCofins = $valorPisCofinsParcela;

                        /**
                         * Verifica se há dízima periódica do valor da parcela, ISS, IR e desconto. 
                         */
                        if ( $titulo['titno_parcela'] == $numeroTitulos && $numeroTitulos > 1){
                            //Verifica o valor da parcela
							$valorTitulo = $this->calcularValorParcela($itensNota['valor_total'], $numeroTitulos, $itensNota['valor_descontos']);
                            
                            //Verifica no valor do ISS
							$valorIssTitulo = $this->calcularValorParcela($impostos['iss'], $numeroTitulos);

							//Verifica no valor do IR
							$valorIrTitulo = $this->calcularValorParcela($impostos['ir'], $numeroTitulos);

							//Verifica no valor total dos impostos
							$valorPisCofins = $this->calcularValorParcela($impostosPisCofinsCsll, $numeroTitulos);
                        }
                        
                        $valorTitulo    = floatval($valorTitulo);
                        $valorIrTitulo  = floatval($valorIrTitulo);
                        $valorIssTitulo = floatval($valorIssTitulo);
                        $valorPisCofins = floatval($valorPisCofins);

                        $sql = "UPDATE titulo 
                                SET 
                                    titvl_titulo     = {$valorTitulo}
                                  , titvl_ir         = {$valorIrTitulo}
                                  , titvl_iss        = {$valorIssTitulo}
                                  , titvl_piscofins  = {$valorPisCofins}
                                WHERE
                                    titoid = {$titulo['titoid']}";
      
                        $this->_query($sql);   
                    }
                }
                
                
         
            }

            
            if(!$errolinha) $linhasSucesso++;
            $errolinha = false;
        }
        
       	if(count($this->_logMessages)>0){
       		$rsdestino = fopen($this->_logFileI, 'w+');
       		foreach ($this->_logMessages as $message){
       			fwrite($rsdestino, $message."\n");
       		}
       		fclose($rsdestino);
       		
       		$msg="Ocorreram erros ao processar o arquivo";
       		if($linhasSucesso==0){
       			$msg="O arquivo não contém nenhum registro válido";
       		}
       		throw new Exception("$msg, <a href=\"fin_fat_manual.php?acao=downloadLogFileI\" target=\"_blank\" />clique aqui</a> para baixar o arquivo com a(s) inconsistência(s).");
       	}
        
        return $itemCount;
    }
    
    /**
     * Emissão de uma nota fiscal
     *
     * @param   array   $itens
     * @return  boolean
     */
    protected function _emitirNovaNota($itens)
    {
    	
    

        // Vetor de armazenamento de itens (por cliente e tipo)
        $itensFiltrados = array();
        
        // Valida os itens a serem incluídos
        $itemCount = 0; 
        $linhasSucesso = 0;
        $errolinha = false;
        foreach ($itens as $item)
        {
            $itemCount += 1;

            // Para importação de arquivo, o valor unitário é uma informação obrigatória e não pode ser igual a 0,00.
            if (!isset($item['titvl_titulo']) || floatval($item['titvl_titulo']) == 0.00)
            {
            	$errolinha = true;
                $this->_logMessages[] = "Linha {$itemCount}: o valor unitário é igual a 0.";
            }

            // Em layout, o tipo do item é uma informação obrigatória e seu conteúdo deve ser igual a "L" ou "M".
            if (!isset($item['nfitipo']) || !in_array(strtoupper($item['nfitipo']), array('L', 'M')))
            {
            	$errolinha = true;
                $this->_logMessages[] = "Linha {$itemCount}: o tipo do item é diferente de L e/ou M.";
            }

            // Para importação de arquivo, o contrato deve existir em base de dados.
            // Para importação de arquivo, o contrato deve ter status Ativo.
            //if (!isset($item['connumero']))
            //{
            //  	$errolinha = true;
            //    $this->_logMessages[] = "Linha {$itemCount}: o contrato não foi fornecido.";
            //}
            // Para importação de arquivo, a obrigação financeira é uma informação obrigatória.
            // Deve ser uma obrigação financeira ativa e deve existir em cadastro de obrigação financeira.
            if (!isset($item['nfiobroid']))
            {
            	$errolinha = true;
                $this->_logMessages[] = "Linha {$itemCount}: a obrigação financeira não foi fornecida.";
            }
            
            // Para importação, verifica a data de vencimento
            $dataVencimento = "";
            if (!isset($item['dt_vencimento'])){
                $this->_logMessages[] = "Linha {$itemCount}: a data de vencimento não foi fornecida.";
            } else {
                //Verifica se a data é menor que data do sistema
                $dataVencimento = $item['dt_vencimento'];

                if (strpos($dataVencimento, "/") === false){
                    $errolinha = true;
                    $this->_logMessages[] = "Linha {$itemCount}: Data de vencimento incorreta.";
                } else {
                    
                    $arrayDataVencto = explode("/", $dataVencimento);
                    
                    if ( ( intval($arrayDataVencto[0]) > 31 ) ||
                         ( intval($arrayDataVencto[1]) > 12 ) || 
                         ( intval($arrayDataVencto[0]) == 0 ) || 
                         ( intval($arrayDataVencto[1]) == 0 ) || 
                         ( intval($arrayDataVencto[2]) == 0 ) ) {
                        $errolinha = true;
                        $this->_logMessages[] = "Linha {$itemCount}: Data de vencimento incorreta.";
                    } else {
                    
                        $dataVenctoAmericano = $arrayDataVencto[2] . '-' . $arrayDataVencto[1] . '-' . $arrayDataVencto[0];

                         if(strtotime($dataVenctoAmericano) < strtotime(date("Y-m-d"))){
                            $errolinha = true;
                            $this->_logMessages[] = "Linha {$itemCount}: Data de vencimento incorreta.";
                        }
                    }
                }
            }

            $obrigacao=false;
            if(is_numeric($item['nfiobroid'])){
	            $sql = "SELECT
	                        *
	                    FROM
	                        obrigacao_financeira
	                    WHERE obroid = {$item['nfiobroid']}
                          AND obrdt_exclusao is null";
	            $obrigacao = $this->_fetchAssoc($this->_query($sql));
            }
            if (!$obrigacao)
            {
            	$errolinha = true;
                $this->_logMessages[] = "Linha {$itemCount}: a obrigação financeira {$item['nfiobroid']} não existe.";
            }

            // Para importação de arquivo, o contrato deve existir em base de dados.
            // Para importação de arquivo, o contrato deve ter status Ativo.
            //if (!isset(empty['connumero']))
            //{
            //	$errolinha = true;
            //    $this->_logMessages[] = "{$item['nflno_numero']};{$item['nflserie']};Linha {$itemCount}: o número do contrato não foi fornecido.";
            //}
            
            //contrato nao é obrigatório.
            if (!empty($item['connumero'])){
	            $contrato=false;
	            if(is_numeric($item['connumero'])){
		            $sql = "SELECT
		                        *
		                    FROM
		                        contrato
		                    WHERE
		                        connumero     = {$item['connumero']}
		                        AND concsioid = 1
		                        AND condt_exclusao IS NULL";  
		                                  
		            $contrato = $this->_fetchAssoc($this->_query($sql));
	            }
	            if (!$contrato)
	            {
	            	$errolinha = true;
	                $this->_logMessages[] = "Linha {$itemCount}: o contrato {$item['connumero']} não existe ou não está ativo.";
	            }
            }

            // Para Importação de arquivo, opção 2, o CNPJ ou CPF do cliente é uma informação obrigatória. 
            // O cliente deve existir em base de dados e estar ativo.
            $cliente=false;
            if(is_numeric($item['cpf_cnpj'])){
	            $sql = "SELECT
	                        clientes.clioid,
                            cliente_cobranca.clicformacobranca,
							clientes.cliret_iss_perc,
							clientes.cliret_pis_perc,
							clientes.cliret_cofins_perc,
							clientes.cliret_csll_perc
	                    FROM
	                        clientes
                        LEFT JOIN cliente_cobranca ON (cliente_cobranca.clicclioid = clientes.clioid)
	                    WHERE
	                        clientes.clidt_exclusao IS NULL
	                        AND (clientes.clino_cgc    = {$item['cpf_cnpj']}
	                             OR clientes.clino_cpf = {$item['cpf_cnpj']})";            
	            $cliente = $this->_fetchAssoc($this->_query($sql));
            }
            if (!$cliente)
            {
            	$errolinha = true;
                $this->_logMessages[] = "Linha {$itemCount}: o cliente com CPF/CNPJ {$item['cpf_cnpj']} não existe ou não está ativo.";
            }    
            
            // Vetor de armazenamento de itens filtrados por cliente e por tipo (M ou L)
            $itensFiltrados[$cliente['clioid']][] = $item;

            if(!$errolinha) $linhasSucesso++;
        	$errolinha = false;        	
        }
        
        if(count($this->_logMessages)>0){
        	$rsdestino = fopen($this->_logFileN, 'w+');
        	foreach ($this->_logMessages as $message){
        		fwrite($rsdestino, $message."\n");
        	}
        	fclose($rsdestino);
        	
        	$msg="Ocorreram erros ao processar o arquivo";
        	if($linhasSucesso==0){
        		$msg="O arquivo não contém nenhum registro válido";
        	}        	 
        	throw new Exception("$msg, <a href=\"fin_fat_manual.php?acao=downloadLogFileN\" target=\"_blank\" />clique aqui</a> para baixar o arquivo com a(s) inconsistência(s).");
        }
                
        // Itera sobre cada item filtrado e validado, e emite as notas fiscais
        foreach ($itensFiltrados as $clioid => $itens)
        {
            // Inserção de nova nota: busca série e próximo número
            $sql = "SELECT
                        MAX(nflno_numero) + 1 AS nflno_numero
                      , 'A' AS nflserie
                    FROM
                        nota_fiscal
                    WHERE
                        nflserie = 'A'";                
            $dadosNota = $this->_fetchAssoc($this->_query($sql));
            
            // Código do usuário logado
            $cdUsuario = $_SESSION['usuario']['oid'];
            //Data de referencia
            $dataReferencia = date("01/m/Y");
            
            //Forma de pagamento
            $formaPagamento = !empty($cliente['clicformacobranca']) ? $cliente['clicformacobranca'] : 74;
            // Insere nova nota
            $sql = "INSERT INTO nota_fiscal (
                        nfldt_inclusao
     				  , nfldt_faturamento
                      , nfldt_nota
                      , nfldt_emissao
                      , nfldt_referencia
                      , nfldt_vencimento
                      , nflno_numero
                      , nflserie
                      , nflusuoid
                      , nflclioid
                      , nflvl_total
                      , nflvl_desconto 
                      , nflnatureza
                      , nfltransporte
                      , nflclioid_fatura
                    ) VALUES (
                        NOW()
                      , NOW()
                      , NOW()
                      , NOW()
                      , '{$dataReferencia}'
                      , '{$dataVencimento}'
                      , {$dadosNota['nflno_numero']}
                      , '{$dadosNota['nflserie']}'
                      , {$cdUsuario}
                      , {$clioid}
                      , 0.00
                      , 0.00
                      , 'PRESTACAO DE SERVICOS'
                      , 'RODOVIARIO'
                      , {$formaPagamento}
                    )
                    RETURNING nfloid, nflno_numero, nflserie";
            $nota = $this->_fetchAssoc($this->_query($sql));

            $sql = "UPDATE nota_fiscal
                        SET nflnota_ant = {$nota['nflno_numero']}
                     WHERE (nfloid = {$nota['nfloid']})";
            $this->_query($sql);
            
            
            // Itera sobre cada item do tipo, inserindo nota_fiscal_item e titulo
            foreach ($itens as $item)
            {
                // Insere novo item na nota fiscal
                $dataReferencia = date("Y-m-01");
                
                //Busca a obrigação financeira da linha
                if(is_numeric($item['nfiobroid'])){
                    $sql = "SELECT
                                *
                            FROM
                                obrigacao_financeira
                            WHERE obroid = {$item['nfiobroid']}
                              AND obrdt_exclusao is null";
                    $obrigacao = $this->_fetchAssoc($this->_query($sql));
                }
                
                
                //Busca o valor da obrigação financeira
                $sql = "SELECT
                                 *
                             FROM
                                 contrato_obrigacao_financeira
                             WHERE cofconoid = ".intval($item['connumero'])."
                               AND cofobroid = {$item['nfiobroid']}
                               AND cofdt_termino is null";
                $obrigacao_valor = $this->_fetchAssoc($this->_query($sql));
                $obrigacao_valor['cofvl_obrigacao'] = floatval($obrigacao_valor['cofvl_obrigacao']) > 0 ? floatval($obrigacao_valor['cofvl_obrigacao']) : floatval($obrigacao['obrvl_obrigacao']);
                
                $sql = "INSERT INTO nota_fiscal_item ( 
                            nfino_numero
                          , nfiserie
                          , nficonoid
                          , nfiobroid
                          , nfivl_item
                          , nfidt_referencia
                          , nfidesconto
                          , nfidt_inclusao
                          , nfinfloid
                          , nfivl_obrigacao
                          , nfitipo
                          , nfids_item
	     				  , nfinota_ant
                        ) VALUES ( 
                            {$nota['nflno_numero']}
                          , '{$nota['nflserie']}'
	                      , ".(empty($item['connumero']) ? 'null': intval($item['connumero']))."
                          , {$item['nfiobroid']}
                          , {$item['titvl_titulo']}
                          , '{$dataReferencia}'
                          , {$item['titvl_desconto']}
                          , NOW()
                          , {$nota['nfloid']}
                          , {$obrigacao_valor['cofvl_obrigacao']}
                          , '{$item['nfitipo']}'
                          , '{$obrigacao['obrobrigacao']}'
	     				  , {$nota['nflno_numero']}
                        )";
                $this->_query($sql);  
            }            
            
            // Totais da nota
            $sql = "SELECT 
                        SUM(nfivl_item)  AS valor_total
                      , SUM(nfidesconto) AS valor_descontos
                    FROM
                        nota_fiscal_item
                    WHERE
                        nfino_numero = {$nota['nflno_numero']}
                        AND nfiserie = '{$nota['nflserie']}'";
            $totais = $this->_fetchAssoc($this->_query($sql));
                        
            //Calcula os impostos
            $impostos = $this->calculaImpostos($cliente, $totais['valor_total']);
            
            //Calcula o totais dos impostos
            $impostosPisCofinsCsll = (float) floatval($impostos['pis']) + floatval($impostos['cofins']) + floatval($impostos['csll']);
            
            //Valor do titulo
            $valorTitulo = floatval($totais['valor_total']) - floatval($totais['valor_descontos']);
            
            ///Data Referencia titulo
            /* [start][ORGMKTOTVS-1929] - Leandro Corso */
            if (!INTEGRACAO_TOTVS) {
                $dataReferenciaTitulo = date("Y-m-01", mktime(0,0,0,$arrayDataVencto[1],1,$arrayDataVencto[2]));            
                $sql = "INSERT INTO titulo ( 
                            titnfloid
                        , titdt_referencia
                        , titdt_vencimento
                        , titemissao
                        , titvl_titulo
                        , titclioid
                        , titno_parcela
                        , titvl_ir
                        , titvl_iss
                        , titformacobranca
                        , titvl_piscofins
                        ) VALUES ( 
                            {$nota['nfloid']}
                        , '{$dataReferenciaTitulo}'
                        , '{$dataVencimento}'
                        , NOW()
                        , {$valorTitulo}
                        , {$clioid}
                        , {$item['titno_parcela']}
                        , {$impostos['ir']}
                        , {$impostos['iss']}
                        , {$item['titformacobranca']}
                        , $impostosPisCofinsCsll
                        )";
                $this->_query($sql);            
            }
            /* [end][ORGMKTOTVS-1929] - Leandro Corso */
         
            // Atualiza total da nota fiscal

            $sql = "UPDATE nota_fiscal
                    SET
                        nflserie         = '{$item['nflserie']}'
                      , nflvl_total     = {$totais['valor_total']}
                      , nflvl_desconto  = {$totais['valor_descontos']}
		              , nflvlr_pis 	     = {$impostos['pis']}
		              , nflvlr_cofins    = {$impostos['cofins']}
		              , nflvlr_csll 	 = {$impostos['csll']}
		              , nflvlr_ir        = {$impostos['ir']}
		              , nflvlr_iss       = {$impostos['iss']}
		              , nflvlr_piscofins = $impostosPisCofinsCsll
                    WHERE
                        nfloid = {$nota['nfloid']}";
  
            $this->_query($sql);
        }    
        
        return $itemCount;       
    } 
	
     
     private function calcularValorParcela($valorTotal, $numeroParcelas, $valorDesconto = 0){
         $valorBase = $valorTotal - $valorDesconto;
         
         if($valorTotal == 0){
             return 0;
         }
         
         if ($numeroParcelas == 0){
             return 0;
         }
         
         $valorParcelaOriginal = ($valorBase / $numeroParcelas); 
         $valorParcela = round(($valorBase / $numeroParcelas), 2); 
         
         if ($valorParcela > $valorParcelaOriginal) {        
            $diferenca = (($numeroParcelas * $valorParcela) - $valorBase);
         
             return ($valorParcela - $diferenca);
         } else {
            $diferenca = ($valorBase - ($numeroParcelas * $valorParcela));
            return ($valorParcela + $diferenca);
         }
         
    }
	 
	 private function calculaImpostos(array $cliente, $valorTotal){
		$valorTotal = floatval($valorTotal);
		$impostos = array(
			'iss' => 0,
			'pis' => 0,
			'cofins' => 0,
			'csll'   => 0,
			'ir'     => 0
		);
		
		//O ISS não tem valor limite para retenção.
		//Cálculo: aplicar sobre o valor total da NF o percentual do ISS encontrado em cadastro do cliente.
		if( ($cliente['cliret_iss_perc'] > 0 ) && ( $valorTotal > 0 ) ) {
			$impostos['iss'] = round( ( $cliente['cliret_iss_perc'] / 100 ) * $valorTotal, 2);
		}		
		//O PIS é sobre o valor total da nota fiscal acima de  R$ 5.000,00.
		if( ( $cliente['cliret_pis_perc'] > 0 ) && ( $valorTotal > 5000 ) ){
			$impostos['pis'] = round( ( $cliente['cliret_pis_perc'] / 100 ) * $valorTotal, 2);
		}
		//O COFINS é sobre o valor total da nota fiscal acima de  R$ 5.000,00.
		if( ( $cliente['cliret_cofins_perc'] > 0 ) && ( $valorTotal > 5000 ) ) {
			$impostos['cofins'] = round( ( $cliente['cliret_cofins_perc'] / 100 ) * $valorTotal, 2);
		}

		//O CSLL é sobre o valor total da nota fiscal acima de  R$ 5.000,00.
		if( ( $cliente['cliret_csll_perc'] > 0 ) && ( $valorTotal > 5000 ) ) {
			$impostos['csll'] = round( ( $cliente['cliret_csll_perc'] / 100 ) * $valorTotal, 2);
		}
		
		// Regra removida ASM 276807 GMUD 7393
		//O IR (1%) é sobre o valor total da nota fiscal. Para esta retenção, a NF tem que possuir um valor acima de R$ 1.000,00.
		//if($valorTotal > 1000){
			//$impostos['ir'] = round( ($valorTotal * 0.01), 2);
		//}
		
		return $impostos;
	}
}