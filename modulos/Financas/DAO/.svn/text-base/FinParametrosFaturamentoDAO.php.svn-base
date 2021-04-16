<?php

/**
 * Classe de persistência de dados
 *
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * @package Finanças
 * @since 22/01/2013
 */

class FinParametrosFaturamentoDAO {

	private $conn;
	
	/*
	 * Construtor
	 */
	function __construct($conn) {

		$this->conn = $conn;
	}
	
	
	/** Pesquisa **/
	
	/**
	 * Método que executa a consulta para pesquisa
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function pesquisar($pWhere){
		
		$sql = "SELECT
		            parfnivel as nivel,
					parfconoid as contrato,
					conclioid as id_cliente,
					contrato.conno_tipo as id_tipo_contrato,
					clinome as cliente,
					tpcdescricao as tipo_contrato,
					parfobroid_multiplo as obr_financeira_multiplo,
					obrobrigacao as obr_financeira,
					parfvl_cobrado as valor,
					CASE
						WHEN parfdt_fin_valor IS NOT NULL THEN 
							TO_CHAR(parfdt_ini_valor, 'DD/MM/YYYY') ||' a '|| TO_CHAR(parfdt_fin_valor, 'DD/MM/YYYY')
						ELSE
							TO_CHAR(parfdt_ini_valor, 'DD/MM/YYYY') ||' a 99/99/9999'
					END as periodo_valor,
					CASE WHEN parfisento  = 't'
						 	THEN 'Sim'
					     ELSE 'Não' 
					END as isento,
					CASE 
						WHEN parfdt_fin_cobranca IS NOT NULL THEN
							TO_CHAR(parfdt_ini_cobranca, 'DD/MM/YYYY') ||' a '|| TO_CHAR(parfdt_fin_cobranca, 'DD/MM/YYYY') 
						ELSE
							TO_CHAR(parfdt_ini_cobranca, 'DD/MM/YYYY') ||' a 99/99/9999' 
					END	as periodo_isencao,
					CASE
						WHEN parfdt_fin_desconto IS NOT NULL THEN 
							TO_CHAR(parfdt_ini_desconto, 'DD/MM/YYYY') ||' a '|| TO_CHAR(parfdt_fin_desconto, 'DD/MM/YYYY')
						ELSE
							TO_CHAR(parfdt_ini_desconto, 'DD/MM/YYYY') ||' a 99/99/9999'
					END as periodo_desconto,
					parfdesconto as desconto,
					parfqtd_min ||' a '|| parfqtd_max as quantidade_faturamento,
					parfperiodicidade as periodicidade,
					parfperiodicidade_reajuste as periodicidade_reajuste,
					parfmotivo_macro as macro_motivo,
					parfmotivo_micro as micro_motivo,
					TO_CHAR(parfdt_ini_valor, 'YYYY-MM-DD') as periodo_ini_valor,
					TO_CHAR(parfdt_fin_valor, 'YYYY-MM-DD') as periodo_fin_valor,
					TO_CHAR(parfdt_ini_cobranca, 'YYYY-MM-DD') as periodo_ini_isencao,
					TO_CHAR(parfdt_fin_cobranca, 'YYYY-MM-DD') as periodo_fin_isencao,
					TO_CHAR(parfdt_ini_desconto, 'YYYY-MM-DD') as periodo_ini_desconto,
					TO_CHAR(parfdt_fin_desconto, 'YYYY-MM-DD') as periodo_fin_desconto,
					parfobservacao_usuario,
					parfoid		AS id,
					parfprazo_vencimento AS prazo_vencimento 
				FROM
					parametros_faturamento
				LEFT JOIN
					clientes ON parfclioid = clioid
				LEFT JOIN
					contrato ON parfconoid = connumero
				LEFT JOIN
					tipo_contrato ON parftpcoid = tpcoid
				LEFT JOIN
					obrigacao_financeira ON parfobroid = obroid
				LEFT JOIN
					parametros_faturamento_motivos ON pfmoid = parfmotivo_macro
				WHERE true
				$pWhere  
		        ORDER BY parfoid ASC";
		
		
		if(!$rs = pg_query($this->conn, $sql)){
			throw new Exception('Erro ao pesquisar registro.');
		}
		
		$result = array();
		
		if(pg_num_rows($rs) > 0){
			
			$arrResult = pg_fetch_all($rs);
			
			foreach($arrResult as $resultado){
    			    
			    $descricaoObrigacaoFinanceira = null;

			    $obrFinanceiraLista = array();
			    
                                  
                    if ($resultado['obr_financeira_multiplo']) { 
                        
                    	$obr_fin_cod = explode(',', str_replace("{", "", str_replace("}", "", $resultado['obr_financeira_multiplo'])));
                        
                        $obrigacaoFinanceiraMult =  implode(',', $obr_fin_cod);
                        $retorno_descricao = $this->buscarObrigacaoFinanceira($obrigacaoFinanceiraMult);
                        
                        if(count($retorno_descricao) > 0){
                        		
                        	foreach ($retorno_descricao AS $obrigacaoFin) {
                        
                        		if (!empty($descricaoObrigacaoFinanceira)) {
                        			$descricaoObrigacaoFinanceira .= ",<br>";
                        		}
                        		
                        		$descricaoObrigacaoFinanceira .= $obrigacaoFin['descricao'];

                        		// ASM-5357 - Adição ID Obrigação Financeira, melhoria na exportação do CSV
                        		$obrFinanceiraLista[] = array(
                        			'id' => $obrigacaoFin['id'],	
                        			'descricao' => $obrigacaoFin['descricao'],
                        			'grupo' => $obrigacaoFin['grupo']
                        		);
                        	}
                        		
                        }
                        
                    }

                    if($resultado['macro_motivo']){
                        $retorno_descricao = $this->selecionaMacroMicroMotivo($resultado['macro_motivo']);
                        $resultado['macro_motivo'] = $retorno_descricao[0]['motivo'];
                    }

                    if($resultado['micro_motivo']){
                        $retorno_descricao = $this->selecionaMacroMicroMotivo($resultado['micro_motivo']);
                        $resultado['micro_motivo'] = $retorno_descricao[0]['motivo'];
                    }

                    $hoje = date('Y/m/d');
                    $resultado['vigencia'] = "NÃO";

                    if($resultado['periodo_valor']){
                        if(strtotime($resultado['periodo_ini_valor']) <= strtotime($hoje) && strtotime($resultado['periodo_fin_valor']) >= strtotime($hoje)) {
                            $resultado['vigencia'] = "SIM";
                        }
                    }

                    if($resultado['periodo_isencao']){
                        if(strtotime($resultado['periodo_ini_isencao']) <= strtotime($hoje) && strtotime($resultado['periodo_fin_isencao']) >= strtotime($hoje)) {
                            $resultado['vigencia'] = "SIM";
                        }
                    }

                    if($resultado['periodo_desconto']){
                        if(strtotime($resultado['periodo_ini_desconto']) <= strtotime($hoje) && strtotime($resultado['periodo_fin_desconto']) >= strtotime($hoje)) {
                            $resultado['vigencia'] = "SIM";
                        }
                    }

                    if($resultado['periodicidade_reajuste']){
                        $resultado['vigencia'] = "SIM";
                    }

                    if($resultado['id_cliente']){
                        $sqlcli = " SELECT clinome as cliente_nome
                                       FROM clientes
                                      WHERE clioid = ".$resultado['id_cliente']." ";

                        if(!$rs3 = pg_query($this->conn, $sqlcli)){
                            throw new Exception('Erro ao pesquisar registro.');
                        }
                        $arrResult3 = pg_fetch_all($rs3);

                        foreach($arrResult3 as $resultado3) {
                            $cliente = $resultado3['cliente_nome'];
                        }
                    }else{
                        $cliente = $resultado['cliente'];
                    }

                    if($resultado['id_tipo_contrato'] != null){
                        $sqltipo = " SELECT tpcdescricao as tipo_contrato_nome
                                                   FROM tipo_contrato
                                                  WHERE tpcoid = ".$resultado['id_tipo_contrato']." ";

                        if(!$rs2 = pg_query($this->conn, $sqltipo)){
                            throw new Exception('Erro ao pesquisar registro.');
                        }
                        $arrResult1 = pg_fetch_all($rs2);

                        foreach($arrResult1 as $resultado2) {
                            $tipo_contrato = $resultado2['tipo_contrato_nome'];
                        }
                    }else{
                        $tipo_contrato = $resultado['tipo_contrato'];
                    }

    			    			
				
				$result[] = array(
				    'nivel' => $resultado['nivel'],
				    'contrato' => $resultado['contrato'],
					'cliente' => $resultado['cliente'],
					'tipo_contrato' =>$resultado['tipo_contrato'],
                    'obr_financeira' => $resultado['obr_financeira'],
				    'obr_financeira_multiplo' =>$descricaoObrigacaoFinanceira,
				    'obr_financeira_lista' =>$obrFinanceiraLista,
					'valor' =>number_format($resultado['valor'], 2, ',', '.'),
                    'periodo_valor' => $resultado['periodo_valor'],
					'isento' => $resultado['isento'],
					'desconto' => number_format($resultado['desconto'], 2, ',', '.'),
					'periodo_isencao' => $resultado['periodo_isencao'],
					'periodo_desconto' => $resultado['periodo_desconto'],
					//'quantidade_faturamento' => $resultado['quantidade_faturamento'],
					'periodicidade' => $resultado['periodicidade'],
					'periodicidade_reajuste' => $resultado['periodicidade_reajuste'],
					'macro_motivo' => $resultado['macro_motivo'],
					'micro_motivo' => $resultado['micro_motivo'],
					'vigencia' => $resultado['vigencia'],
					'parfobservacao_usuario' => $resultado['parfobservacao_usuario'],
					'id'=> $resultado['id'],
					'id_cliente'=> $cliente,
					'id_tipo_contrato'=> $tipo_contrato,					
					'prazo_vencimento'=> $resultado['prazo_vencimento']
				);
				
		    }

		    return $result;
		}
		
		return false;
		
	}
	
	/**
	 * Método que executa a consulta para buscar os tipos de contratos
	 */
	public function buscarTipoContrato($cod = NULL){
		
		try {
			
			$sql = " SELECT tpcoid as id_tipo_contrato,
				 	        tpcdescricao as descricao
				       FROM tipo_contrato
				      WHERE tpcativo = 't' ";
			
			if(!empty($cod)){
				$sql .= " AND tpcoid = $cod ";
			}
			
			$sql .= " ORDER BY tpcdescricao ";
			
			if(!$rs = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao buscar tipo de contrato.');
			}
			
			if (pg_num_rows($rs) > 0) {
				return pg_fetch_all($rs);
			}
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
		
	}
	
	/**
	 * Método que executa a consulta para buscar as obrigações financeiras
	 */
	public function buscarObrigacaoFinanceira($varias_obr = NULL){
		
		try {
			
			$sql = " SELECT obroid as id,
					        obrobrigacao as descricao,
					        ofgdescricao as grupo
				       FROM obrigacao_financeira
		 	     INNER JOIN obrigacao_financeira_grupo ON obrofgoid = ofgoid
				      WHERE obrdt_exclusao IS NULL ";
			
			if(!empty($varias_obr)){
				 $sql .= " AND obroid IN ($varias_obr) ";
			}
			
		    $sql .= "   ORDER BY obrobrigacao ";
			
			if(!$rs = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao buscar obrigações financeiras');
			}
			
			if (pg_num_rows($rs) > 0) {
				return pg_fetch_all($rs);
			}
			
			return false;
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
		
	}


    /**
     * Método que executa a consulta para buscar dos micros e macros motivos
     */
    public function buscarMacroMicroMotivo($varios_micromacro = NULL){

        try {

            $sql = " SELECT pfmoid as id, pfmmotivo as motivo, pfmtipo as tipo
				       FROM parametros_faturamento_motivos
				      WHERE pfmdata_exclusao IS NULL ";

            if(!empty($varios_micromacro)){
                $sql .= " AND pfmtipo = '$varios_micromacro' ";
            }

            $sql .= "ORDER BY pfmoid ";
            if(!$rs = pg_query($this->conn, $sql)){
                throw new Exception('Erro ao buscar os Micros e Macros Motivos.');
            }

            if (pg_num_rows($rs) > 0) {
                return pg_fetch_all($rs);
            }

            return false;

        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

    /**
     * Método que executa a consulta para buscar dos micros e macros motivos
     */
    public function selecionaMacroMicroMotivo($motivo = NULL){

        try {

            $sql = " SELECT pfmoid as id, pfmmotivo as motivo, pfmtipo as tipo
				       FROM parametros_faturamento_motivos
				      WHERE pfmdata_exclusao IS NULL ";

            if(!empty($motivo)){
                $sql .= " AND pfmoid = $motivo ";
            }

            $sql .= "ORDER BY pfmoid ";

            //var_dump($sql);
            //die("ssssssss");
            if(!$rs = pg_query($this->conn, $sql)){
                throw new Exception('Erro ao buscar os Micros e Macros Motivos.');
            }

            if (pg_num_rows($rs) > 0) {
                return pg_fetch_all($rs);
            }

            return false;

        } catch (Exception $e) {
            return $e->getMessage();
        }

    }
	
	
	/** Inclusão e Edição **/
	
	/**
	 * Efetua a pesquisa de clientes e retorna em formato Array.
	 * 
	 * @param array $filtros Array de filtros
	 * @throws Exception
	 * @return multitype|array:
	 */
	public function buscarClientes($filtros) {
		
		$filtro = '';
		
		if (count($filtros) > 0) {

			// clioid
			if (isset($filtros['clioid']) && !empty($filtros['clioid'])) {
				$filtro = "WHERE clioid = ".$filtros['clioid'];
			} else {
				$filtro = "WHERE clidt_exclusao IS NULL";
			}
			
			// clinome
			if (isset($filtros['nome_cliente'])) {
				$filtro .= " AND clinome ILIKE '%".$filtros['nome_cliente']."%' ";
			}
			
			// clitipo
			if (isset($filtros['tipo_pessoa'])) {
				$filtro .= " AND clitipo = '".$filtros['tipo_pessoa']."' ";
			}
			
			// CPF ou CNPJ
			if (isset($filtros['cpf_cnpj'])) {
				
				if ($filtros['tipo_pessoa'] == 'F') {
					
					$filtro .= " AND clino_cpf = '".$filtros['cpf_cnpj']."' ";
				} else {
					
					$filtro .= " AND clino_cgc = '".$filtros['cpf_cnpj']."' ";
				}
			}
			
		}

		$sql = " SELECT	clioid AS id,
			            clinome	AS nome,
			            clitipo AS tipo_pessoa,
			            CASE
				          WHEN clitipo = 'F' THEN clino_cpf
				          WHEN clitipo = 'J' THEN clino_cgc
			            END AS cpf_cnpj,
			            clidt_exclusao AS dt_exclusao
		           FROM	clientes
		                $filtro		
		       ORDER BY	clinome
		          LIMIT 500	";

		$result = pg_query($this->conn, $sql);
		
		if (!$result) {
			throw new Exception("Erro ao buscar clientes");
		} else {
			return pg_fetch_all($result);
		}
	}

	
	public function insereParametro($valores = array()) {
		
		if (count($valores) == 0) {
			throw new Exception("Erro ao inserir parâmetro. Opções não informadas.");
		}
		
		$usuarioLogado 	= Sistema::getUsuarioLogado();
		$usuario 		= $usuarioLogado->cd_usuario; 
		
		// Campos
		$nivel					= "NULL";
		$contrato				= "NULL";
		$cliente				= "NULL";
		$tipoContrato			= "NULL";
		$isento					= "'f'";
		$desconto				= 0;
		$valor					= 0;
		$dataIniDesconto		= "NULL";
		$dataFimDesconto		= "NULL";
		$dataIniIsento			= "NULL";
		$dataFimIsento			= "NULL";
		$dataIniValor			= "NULL";
		$dataFimValor			= "NULL";
		//$quantidadeMin			= 0;
		//$quantidadeMax			= 0;
		$periodicidadeReajuste  = "NULL";
        $prazo_vencimento       = "NULL";
		//$periodicidade			= 1;
		$observacao				= "";
		$obrigacaoFinanceiraMultiplo = "NULL";
        $macro_motivo           = "NULL";
        $micro_motivo           = "NULL";

		//$trocasIsentas          = "NULL";
		//$trocaValor             = "NULL";
		
		$descricaoObrigacaoFinanceira = '';
		$descricaoTipoContrato		  = '';
		$nomeCliente				  = '';
		$observacao_usuario           = '';
		
		
		
		$obrigacaoFinanceiraMult =  implode(',', $valores['obrigacao_financeira_multiplo']);
		$retorno_descricao = $this->buscarObrigacaoFinanceira($obrigacaoFinanceiraMult);
		
		if(count($retorno_descricao) > 0){
			
			foreach ($retorno_descricao AS $obrigacaoFin) {
				
				if (!empty($descricaoObrigacaoFinanceira)) {
					$descricaoObrigacaoFinanceira .= ", ";
				}
				
				$descricaoObrigacaoFinanceira .= $obrigacaoFin['descricao'];
			}
			
			$descricaoObrigacaoFinanceira .= ".";
		}
	    
		
		// parfnivel integer NOT NULL, -- Nível de parametrização: 1 - Contrato, 2 - Cliente ou 3 - Tipo de Contrato
		if(!empty($nivel)){
			$nivel = $valores['nivel'];
		}
		
		// parfconoid integer, -- Id do contrato. Requerido para o nível 1.
		if (!empty($valores['contrato']) && $nivel != 2 ) {
			$contrato = $valores['contrato'];
		}
		
		//parfclioid integer, -- Id do cliente. Requerido para o nível 2.
		if (!empty($valores['cliente']) && $nivel != 1) {
			$cliente =  $valores['cliente'];
		}
		
		// parftpcoid integer, -- Id do tipo de contrato. Requerido para o nível 3.
		if (!empty($valores['tipo_contrato']) >= 0 && $nivel == 3) {
			$tipoContrato = $valores['tipo_contrato'];
		}
		
		// parfisento boolean, -- Se este campo estiver marcado, desconsidera o valor informado no campo valor cobrado e isenta de cobrança para o tipo definido.
		if (!empty($valores['isento'])) {
			if ($valores['isento']) {
				$isento = "'t'";
			}
		}
		
		// parfdesconto integer, -- Informa a porcentagem de desconto quando o campo parfvl_cobrado não for informado.
		if (!empty($valores['desconto'])) {
			$desconto = $valores['desconto'];
		}
		
		// parfvl_cobrado double precision, -- Valor parametrizado para cobrança de acordo com o tipo definido. Ex.: Vlr. De Monitoramento, Vlr. De Acionamento Indevido, Vlr. De Locação, etc.
		if (!empty($valores['valor'])) {
			$valor =  $valores['valor'];
		}
		
		// parfdt_ini_desconto date, -- Data Inicial caso desconto seja checado
		if (!empty($valores['data_ini_desconto'])) {
			$dataIniDesconto =  $valores['data_ini_desconto'];
		}
		
		// parfdt_fin_desconto date, -- Data Final caso desconto seja checado
		if (!empty($valores['data_fim_desconto'])) {
			$dataFimDesconto =  $valores['data_fim_desconto'];
		}
		
		// parfdt_ini_cobranca date, -- Data Final caso cobranca seja checado
		if (!empty($valores['data_ini_isento'])) { 
			$dataIniIsento = $valores['data_ini_isento'];
		}
		
		// parfdt_fin_cobranca date, -- Data Final caso cobranca seja checado
		if (!empty($valores['data_fim_isento'])) {
			$dataFimIsento = $valores['data_fim_isento'];
		}

        // parfdt_ini_valor date, -- Data Inicial caso valor seja preenchido
        if (!empty($valores['data_ini_valor'])) {
            $dataIniValor = $valores['data_ini_valor'];
        }

        // parfdt_fin_valor date, -- Data Final caso valor seja preenchido
        if (!empty($valores['data_fim_valor'])) {
            $dataFimValor = $valores['data_fim_valor'];
        }
		
		// parfqtd_min integer, -- Quantidade mínima para faturamento
// 		if (!empty($valores['quantidade_min'])) {
// 			$quantidadeMin = $valores['quantidade_min'];
// 		}

// 		// parfqtd_max integer, -- Quantidade máxima para faturamento
// 		if (!empty($valores['quantidade_max'])) {
// 			$quantidadeMax = $valores['quantidade_max'];
// 		}
		
		// parfperiodicidade_reajuste integer, -- Periodicidade do reajuste
		if (!empty($valores['periodicidade_reajuste'])) {
			$periodicidadeReajuste = $valores['periodicidade_reajuste'];
		}

		// parfprazo_vencimento integer, -- Prazo de Vencimento (Dias)
		if (!empty($valores['prazo_vencimento'])) {
            $prazo_vencimento = $valores['prazo_vencimento'];
		}
		
		if(count($obrigacaoFinanceiraMult) > 0){
			$obrigacaoFinanceiraMultiplo = " ARRAY[$obrigacaoFinanceiraMult] ";
		}
		
		
        // parfmotivo_macro integer, -- Macro motivo do parametro
        if (!empty($valores['macro_motivo'])) {
            $macro_motivo = $valores['macro_motivo'];
        }
		
		// parfmotivo_micro integer, -- Micro motivo do parametro
		if (!empty($valores['micro_motivo'])) {
            $micro_motivo = $valores['micro_motivo'];
		}
	
		// parfperiodicidade_reajuste integer, -- Periodicidade do reajuste
		if (!empty($valores['observacao_usuario'])) {
			$observacao_usuario = $valores['observacao_usuario'];
		}
		
// 		if (!empty($valores['troca_isentas'])) {
// 			$trocasIsentas = $valores['troca_isentas'];
// 		}
		
// 		if (!empty($valores['troca_valor'])) {
// 			$trocaValor = $valores['troca_valor'];
// 		}
		
// 		// parfperiodicidade integer, -- Periodicidade do faturamento
// 		if (!empty($valores['periodicidade'])) {
// 			$periodicidade = $valores['periodicidade'];
// 		}
		
		
		if ($nivel == 2) {
			//Busca nome do cliente
			$retorno_cliente = $this->buscarClientes(array('clioid' => $cliente));
			$nomeCliente = $retorno_cliente[0]['nome'];
		}
		
		// Busca o tipo de contrato
		if ($tipoContrato != "NULL") {
			$retorno_tipo_contrato = $this->buscarTipoContrato($tipoContrato);
			$descricaoTipoContrato = $retorno_tipo_contrato[0]['descricao'];
		}
		
		/*
		 * Monta observação
		 */
		$observacao .= "Operação: Inclusão<br>"; // parfobservacao text, -- Comentário para identificação da parametrização.
		$observacao .= "Data: ".date("d/m/Y H:i:s")."<br>";
		$observacao .= "Usuário: ".$usuarioLogado->nm_usuario."<br>";
		
		if ($tipoContrato != "NULL") {
			$observacao .= "Tipo de Contrato: ".$descricaoTipoContrato."<br>";
		}
		
		$observacao .= "Obrigação Financeira: ".$descricaoObrigacaoFinanceira."<br>";
		$observacao .= "Macro Motivo: ".$macro_motivo."<br>";
		$observacao .= "Micro Motivo: ".$micro_motivo."<br>";
		$observacao .= "Valor: ".number_format($valor, 2, ',', '.')."<br>";

        if ($valor != 0) {
            if ($dataFimValor == "NULL") {

                $observacao .= "Período do desconto do valor: $dataIniValor a 99/99/9999  <br>";
            } else {

                $observacao .= "Período do desconto valor: $dataIniValor a $dataFimValor <br>";
            }
        }


		$observacao .= "Isento Cobrança: ".($valores['isento'] == true ? 'SIM' : 'NÃO')."<br>";
		
		if ($valores['isento']) {
			
			if ($dataFimIsento == "NULL") {
				
				$observacao .= "Período de isenção: $dataIniIsento a 99/99/9999 <br>";
			} else {
				$observacao .= "Período de isenção: $dataIniIsento a $dataFimIsento <br>";
			}
		}
		
		
		$observacao .= "% de Desconto: ".number_format($desconto, 2, ',', '.')."<br>";
		
		if ($desconto != 0) {
			if ($dataFimDesconto == "NULL") {
				
				$observacao .= "Período do desconto: $dataIniDesconto a 99/99/9999  <br>";
			} else {
				
				$observacao .= "Período do desconto: $dataIniDesconto a $dataFimDesconto <br>";
			}
		}
		
		
		if ($periodicidadeReajuste != "NULL") {
		    $observacao .= "Periodicidade de Reajuste: ".($periodicidadeReajuste)."<br>";
		}

		if ($prazo_vencimento != "NULL") {
		    $observacao .= "Prazo de Vencimento (Dias): ".$prazo_vencimento."<br>";
		}
// 		$observacao .= "Periodicidade do Faturamento: ".($periodicidade)."<br>";
		
// 		if ($quantidadeMin != "NULL" && $quantidadeMax != "NULL") {
// 			$observacao .= "Quantidade para faturamento: $quantidadeMin a $quantidadeMax <br>";
// 		}
			
		$observacao = addslashes($observacao);
		$observacao_usuario = addslashes($observacao_usuario);
		
		pg_query($this->conn, "BEGIN;");
		 
		$sql = "
			INSERT INTO 
				parametros_faturamento
			(
				parfdt_cadastro,
				parfusuoid_cadastro,
				parfnivel,
				parfconoid,
				parfclioid,
				parftpcoid,
				parfisento,
				parfdesconto,
				parfvl_cobrado,
				parfdt_ini_desconto,
				parfdt_fin_desconto,
				parfdt_ini_cobranca,
				parfdt_fin_cobranca,
				parfdt_ini_valor,
				parfdt_fin_valor,
				--parfqtd_min,
				--parfqtd_max,
				--parfperiodicidade,
				parfobservacao,
				parfobservacao_usuario,
				parfobroid_multiplo,
				parfmotivo_macro,
				parfmotivo_micro,
				parfperiodicidade_reajuste,
				parfprazo_vencimento--,
				--parfquantidade_trocas_isentas, 
				--parfvalor_taxa_unica
			)
			VALUES
			(
				NOW(),
				$usuario,
				$nivel,
				$contrato,
				$cliente,
				$tipoContrato,
				$isento,
				$desconto,
				$valor,
				".($dataIniDesconto != 'NULL' ? "'$dataIniDesconto'::date" : 'NULL').",
				".($dataFimDesconto != 'NULL' ? "'$dataFimDesconto'::date" : 'NULL').",
				".($dataIniIsento != 'NULL' ? "'$dataIniIsento'::date" : 'NULL').",
				".($dataFimIsento != 'NULL' ? "'$dataFimIsento'::date" : 'NULL').",
				".($dataIniValor != 'NULL' ? "'$dataIniValor'::date" : 'NULL').",
				".($dataFimValor != 'NULL' ? "'$dataFimValor'::date" : 'NULL').",
				--$quantidadeMin,
				--$quantidadeMax,
				--$periodicidade,
				'$observacao',
				'$observacao_usuario',
				$obrigacaoFinanceiraMultiplo,
				$macro_motivo,
				$micro_motivo,
				$periodicidadeReajuste,
				$prazo_vencimento--,
				--" . $trocasIsentas . ",
				--" . $trocaValor . "

			);
		";
		
		
		//echo '<pre>';
		//print_r($sql); die;
		
		$result = pg_query($this->conn, $sql);
		
		if (!$result || pg_affected_rows($result) == 0) {
			
			pg_query($this->conn, "ROLLBACK;");
			throw new Exception("Erro ao inserir registro.");
			
		} else {
			
			//CONTRATO
			if ($nivel == 1) {
				
				$sqlInsereHistoricoContrato = " SELECT	historico_termo_i($contrato, $usuario, '$observacao'); ";
				
				$rsInsereHistoricoContrato = pg_query($this->conn, $sqlInsereHistoricoContrato);
				
				if (!$rsInsereHistoricoContrato) {
					pg_query($this->conn, "ROLLBACK;");
					throw new Exception("Erro ao inserir o histórico do contrato.");
				} 
			}
			
			//CLIENTE
			if ($nivel == 2) {
				
				$sqlInsereHistoricoCliente = " SELECT cliente_historico_i($cliente, $usuario, '$observacao',  'A', '0', '0');";
				
				$rsInsereHistoricoCliente = pg_query($this->conn, $sqlInsereHistoricoCliente);
				
				if (!$rsInsereHistoricoCliente) {
					pg_query($this->conn, "ROLLBACK;");
					throw new Exception("Erro ao inserir o histórico do cliente.");
				}
			}
			
			//TIPO CONTRATO
			if ($nivel == 3) {
				
				$sqlInsereHistoricoTipoContrato = "
					INSERT INTO
						historico_tipo_contrato
					(
						htctpcoid,
						htcusuoid_cadastro,
						htcobs,
						htcdt_cadastro
					)
					VALUES
					(
						$tipoContrato,
						$usuario,
						'$observacao',
						NOW()	
					)
				";
				
				$rsInsereHistoricoTipoContrato = pg_query($this->conn, $sqlInsereHistoricoTipoContrato);
				
				if (!$rsInsereHistoricoTipoContrato) {
					pg_query($this->conn, "ROLLBACK;");
					throw new Exception("Erro ao inserir o histórico do tipo contrato.");
				}
			}
			
			
		}
		
		pg_query($this->conn, "COMMIT;");
		
		return true;
	}
	
	
	/**
	 * Atualiza o parâmetro de acordo com os dados fornecidos.
	 * @param Array $valores Dados para a atualização do parâmetro
	 * @throws Exception
	 */
	public function atualizaParametro($valores) {
		
		
		$usuarioLogado 	= Sistema::getUsuarioLogado();
		$usuario 		= $usuarioLogado->cd_usuario; // -- Usuário que realizou o cadastro.
		
		// Campos
		$parfoid				     = null;
		$nivel					     = "NULL";
		$contrato				     = "NULL";
		$cliente				     = "NULL";
		$tipoContrato			     = "NULL";
		$isento					     = "'f'";
		$desconto				     = 0;
		$valor					     = 0;
		$dataIniDesconto		     = "NULL";
		$dataFimDesconto		     = "NULL";
		$dataIniIsento			     = "NULL";
		$dataFimIsento			     = "NULL";
        $dataIniValor			     = "NULL";
        $dataFimValor			     = "NULL";
		
		//$quantidadeMin			     = 0;
		//$quantidadeMax			     = 0;
		$periodicidadeReajuste       = "NULL";
        $prazo_vencimento            = "NULL";

		//$periodicidade			     = 1;
		
		$observacao				     = "";
		$observacao_usuario          = "";
		$obrigacaoFinanceiraMultiplo = "NULL";
        $macro_motivo           = "NULL";
        $micro_motivo           = "NULL";
		
		//$trocasIsentas               = "NULL";
		//$trocaValor                  = "NULL";
		
		$descricaoObrigacaoFinanceira = '';
		$descricaoTipoContrato		  = '';
		$nomeCliente				  = '';

		
		if (count($valores) == 0) {
			throw new Exception("Erro ao inserir parâmetro. Opções não informadas.");
		}
				
		// parfoid serial, -- Oid da tabela, PK.
		if (!empty($valores['parfoid'])) {
			$parfoid = $valores['parfoid'];
		}
			
		
		if (!empty($valores['obrigacao_financeira_multiplo'])) {
			
			$obrigacaoFinanceiraMult =  implode(',', $valores['obrigacao_financeira_multiplo']);
			$retorno_descricao = $this->buscarObrigacaoFinanceira($obrigacaoFinanceiraMult);
		
			if(count($retorno_descricao) > 0){
		
				foreach ($retorno_descricao AS $obrigacaoFin) {
		
					if (!empty($descricaoObrigacaoFinanceira)) {
						$descricaoObrigacaoFinanceira .= ",<br>";
					}
		
					$descricaoObrigacaoFinanceira .= $obrigacaoFin['descricao'];
				}
			}
		}
		
		
		// parfnivel integer NOT NULL, -- Nível de parametrização: 1 - Contrato, 2 - Cliente ou 3 - Tipo de Contrato
		if (!empty($valores['nivel'])) {
			$nivel = $valores['nivel'];
		}
		
		// parfconoid integer, -- Id do contrato. Requerido para o nível 1.
		if (!empty($valores['contrato'])) {
			$contrato = $valores['contrato'];
		}
		
		//parfclioid integer, -- Id do cliente. Requerido para o nível 2.
		if (!empty($valores['cliente']) && $nivel == 2) {
			$cliente =  $valores['cliente'];
		}
		
		// parftpcoid integer, -- Id do tipo de contrato. Requerido para o nível 3.
		if (is_numeric($valores['tipo_contrato']) && $valores['tipo_contrato'] >= 0) {
			$tipoContrato = $valores['tipo_contrato'];
		}
		
		// parfisento boolean, -- Se este campo estiver marcado, desconsidera o valor informado no campo valor cobrado e isenta de cobrança para o tipo definido.
		if (!empty($valores['isento'])) {
			
			if ($valores['isento']) {
				$isento = "'t'";
			}
		}
		
		// parfdesconto integer, -- Informa a porcentagem de desconto quando o campo parfvl_cobrado não for informado.
		if (!empty($valores['desconto'])) {
			$desconto = $valores['desconto'];
		}
		
		// parfvl_cobrado double precision, -- Valor parametrizado para cobrança de acordo com o tipo definido. Ex.: Vlr. De Monitoramento, Vlr. De Acionamento Indevido, Vlr. De Locação, etc.
		if (!empty($valores['valor'])) {
			$valor =  $valores['valor'];
		}
		
		// parfdt_ini_desconto date, -- Data Inicial caso desconto seja checado
		if (!empty($valores['data_ini_desconto'])) {
			$dataIniDesconto =  $valores['data_ini_desconto'];
		}
		
		// parfdt_fin_desconto date, -- Data Final caso desconto seja checado
		if (!empty($valores['data_fim_desconto'])) {
			$dataFimDesconto =  $valores['data_fim_desconto'];
		}
		
		// parfdt_ini_cobranca date, -- Data Final caso cobranca seja checado
		if (!empty($valores['data_ini_isento'])) {
			$dataIniIsento = $valores['data_ini_isento'];
		}
		
		// parfdt_fin_cobranca date, -- Data Final caso cobranca seja checado
		if (!empty($valores['data_fim_isento'])) {
			$dataFimIsento = $valores['data_fim_isento'];
		}

        // parfdt_ini_valor date, -- Data Inicial caso valor seja preenchido
        if (!empty($valores['data_ini_valor'])) {
            $dataIniValor = $valores['data_ini_valor'];
        }

        // parfdt_fin_valor date, -- Data Final caso valor seja preenchido
        if (!empty($valores['data_fim_valor'])) {
            $dataFimValor = $valores['data_fim_valor'];
        }
		
		// parfqtd_min integer, -- Quantidade mínima para faturamento
// 		if (!empty($valores['quantidade_min'])) {
// 			$quantidadeMin = $valores['quantidade_min'];
// 		}
		
// 		// parfqtd_max integer, -- Quantidade máxima para faturamento
// 		if (!empty($valores['quantidade_max'])) {
// 			$quantidadeMax = $valores['quantidade_max'];
// 		}
		
		// parfperiodicidade_reajuste integer, -- Periodicidade do reajuste
		if (!empty($valores['periodicidade_reajuste'])) {
		    $periodicidadeReajuste = $valores['periodicidade_reajuste'];
		}

		// parfprazo_vencimento integer, -- Prazo de Vencimento (Dias)
		if (!empty($valores['prazo_vencimento'])) {
            $prazo_vencimento = $valores['prazo_vencimento'];
		}

        // parfmotivo_macro integer, -- Macro motivo do parametro
        if (!empty($valores['macro_motivo'])) {
            $macro_motivo = $valores['macro_motivo'];
        }

        // parfmotivo_micro integer, -- Micro motivo do parametro
        if (!empty($valores['micro_motivo'])) {
            $micro_motivo = $valores['micro_motivo'];
        }

		if (!empty($valores['observacao_usuario'])) {
			$observacao_usuario = $valores['observacao_usuario'];
		}
		
		
		// parfperiodicidade integer, -- Periodicidade do faturamento
// 		if (!empty($valores['periodicidade'])) {
// 			$periodicidade = $valores['periodicidade'];
// 		}
		
// 		if (!empty($valores['troca_isentas']) && $valores['troca_isentas'] !=0 ) {
// 			$trocasIsentas = $valores['troca_isentas'];
// 		}
		
// 		if (!empty($valores['troca_valor']) && $valores['troca_valor'] !=0 ) {
// 			$trocaValor = $valores['troca_valor'];
// 		}
		
		if ($tipoContrato != "NULL") {
			$retorno_tipo_contrato = $this->buscarTipoContrato($tipoContrato);
			$descricaoTipoContrato = $retorno_tipo_contrato[0]['descricao'];
		}
		
		// Monta observação
		 
		$observacao .= "Operação: Alteração<br>"; // parfobservacao text, -- Comentário para identificação da parametrização.
		$observacao .= "Data: ".date("d/m/Y H:i:s")."<br>";
		$observacao .= "Usuário: ".$usuarioLogado->nm_usuario."<br>";
		
		if ($tipoContrato != "NULL") {
			$observacao .= "Tipo de Contrato: ".$descricaoTipoContrato."<br>";
		}
		
		$observacao .= "Obrigação Financeira: ".$descricaoObrigacaoFinanceira."<br>";
        $observacao .= "Macro Motivo: ".$macro_motivo."<br>";
        $observacao .= "Micro Motivo: ".$micro_motivo."<br>";
		$observacao .= "Valor: ".number_format($valor, 2, ',', '.')."<br>";

        if ($valor != 0) {
            if ($dataFimValor == "NULL") {

                $observacao .= "Período do desconto do valor: $dataIniValor a 99/99/9999  <br>";
            } else {

                $observacao .= "Período do desconto valor: $dataIniValor a $dataFimValor <br>";
            }
        }

		$observacao .= "Isento Cobrança: ".($valores['isento'] == true ? 'SIM' : 'NÃO')."<br>";
		
		
		if ($valores['isento']) {
			
			if ($dataFimIsento == "NULL") {
				
				$observacao .= "Período de isenção: $dataIniIsento a 99/99/9999 <br>";
			} else {
				$observacao .= "Período de isenção: $dataIniIsento a $dataFimIsento <br>";
			}
		}
		
		
		$observacao .= "% de Desconto: ".number_format($desconto, 2, ',', '.')."<br>";
		
		if ($desconto != 0) {
			if ($dataFimDesconto == "NULL") {
				
				$observacao .= "Período do desconto: $dataIniDesconto a 99/99/9999  <br>";
			} else {
				
				$observacao .= "Período do desconto: $dataIniDesconto a $dataFimDesconto <br>";
			}
		}
		
		
		//$observacao .= "Ativo p/ faturam.: ".($valores['ativo'] == true ? 'SIM' : 'NÃO')."<br>";
		if ($periodicidadeReajuste != "NULL") {
		    $observacao .= "Periodicidade de Reajuste: ".($periodicidadeReajuste)."<br>";
		}

		if ($prazo_vencimento != "NULL") {
		    $observacao .= "Prazo de Vencimento Dias: ".$prazo_vencimento."<br>";
		}
		//$observacao .= "Periodicidade do Faturamento: ".($periodicidade)."<br>";
		
// 		if ($quantidadeMin != "NULL" && $quantidadeMax != "NULL") {
// 			$observacao .= "Quantidade para faturamento: $quantidadeMin a $quantidadeMax <br>";
// 		}

		$observacao = addslashes($observacao);
		$observacao_usuario = addslashes($observacao_usuario);
		
		pg_query($this->conn, "BEGIN;");
		
		$sql = "
		UPDATE
			parametros_faturamento
		SET
			parfdt_alteracao		= NOW(),
			parfusuoid_alteracao 	= $usuario,
			parfconoid				= $contrato,
			parfclioid				= $cliente,
			parftpcoid				= $tipoContrato,
			parfisento				= $isento,
			parfdesconto			= $desconto,
			parfvl_cobrado			= " . floatval($valor) .",
			parfdt_ini_desconto		= ".($dataIniDesconto != 'NULL' ? "'$dataIniDesconto'::date" : 'NULL').",
			parfdt_fin_desconto		= ".($dataFimDesconto != 'NULL' ? "'$dataFimDesconto'::date" : 'NULL').",
			parfdt_ini_cobranca		= ".($dataIniIsento   != 'NULL' ? "'$dataIniIsento'::date"   : 'NULL').",
			parfdt_fin_cobranca		= ".($dataFimIsento   != 'NULL' ? "'$dataFimIsento'::date"   : 'NULL').",
			parfdt_ini_valor		= ".($dataIniValor    != 'NULL' ? "'$dataIniValor'::date"   : 'NULL').",
			parfdt_fin_valor		= ".($dataFimValor    != 'NULL' ? "'$dataFimValor'::date"   : 'NULL').",
			--parfqtd_min			= $quantidadeMin,
			--parfqtd_max			= $quantidadeMax,
			--parfperiodicidade		= $periodicidade,
			parfobservacao			= '$observacao',
			parfobservacao_usuario  = '$observacao_usuario',
			parfobroid_multiplo             = ARRAY[".implode(',',$valores['obrigacao_financeira_multiplo'])."],
			parfmotivo_macro                = $macro_motivo,
			parfmotivo_micro                = $micro_motivo,
			parfperiodicidade_reajuste      = $periodicidadeReajuste,
			parfprazo_vencimento            = $prazo_vencimento--,
			--parfquantidade_trocas_isentas = $trocasIsentas ,
			--parfvalor_taxa_unica          = $trocaValor 
		WHERE
			parfoid = $parfoid
		";

		$result = pg_query($this->conn, $sql);
		
		if (!$result || pg_affected_rows($result) == 0) {
			
			throw new Exception("Erro ao alterar registro.");
		} else {
		
			//CONTRATO
			if ($nivel == 1) {
				
				$sqlInsereHistoricoContrato = " SELECT	historico_termo_i($contrato, $usuario, '$observacao'); ";
				
				$rsInsereHistoricoContrato = pg_query($this->conn, $sqlInsereHistoricoContrato);
				
				if (!$rsInsereHistoricoContrato) {
					pg_query($this->conn, "ROLLBACK;");
					throw new Exception("Erro ao inserir o histórico do contrato.");
				} 
			}
			
			//CLIENTE
			if ($nivel == 2) {
				
				$sqlInsereHistoricoCliente = " SELECT cliente_historico_i($cliente, $usuario, '$observacao',  'A', '0', '0');";
				
				$rsInsereHistoricoCliente = pg_query($this->conn, $sqlInsereHistoricoCliente);
				
				if (!$rsInsereHistoricoCliente) {
					pg_query($this->conn, "ROLLBACK;");
					throw new Exception("Erro ao inserir o histórico do cliente.");
				}
			}
			
			//TIPO CONTRATO
			if ($nivel == 3) {
				
				$sqlInsereHistoricoTipoContrato = "
					INSERT INTO
						historico_tipo_contrato
					(
						htctpcoid,
						htcusuoid_cadastro,
						htcobs,
						htcdt_cadastro
					)
					VALUES
					(
						$tipoContrato,
						$usuario,
						'$observacao',
						NOW()	
					)
				";
				
				$rsInsereHistoricoTipoContrato = pg_query($this->conn, $sqlInsereHistoricoTipoContrato);
				
				if (!$rsInsereHistoricoTipoContrato) {
					pg_query($this->conn, "ROLLBACK;");
					throw new Exception("Erro ao inserir o histórico do tipo contrato.");
				}
			}
		}
		
		pg_query($this->conn, "COMMIT;");
		
		return true;
	} 
	
	
	/**
	 * Retorna true se o contrato existir, do contrario retorna false.
	 * @param integer $contrato
	 * @throws Exception
	 * @return boolean
	 */
	public function contratoValido($contrato) {
		
		$numRows = 0;
		
		$sql = "
		SELECT
			connumero
		FROM
			contrato
		WHERE		
			connumero = $contrato 
			AND condt_exclusao IS NULL
			AND concsioid = 1
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			
			throw new Exception("Erro ao validar o contrato.");
		} else {
			
			$numRows = pg_num_rows($result);
			
			if ($numRows == 0) {
				return false;
			} else {
				return true;	
			}
		}
	}
	
	/**
	 * Retorna o parâmetro para fatuamento de acordo com o parfoid informado.
	 * @param interger $parfoid
	 * @throws Exception
	 * @return object|null
	 */
	public function getParametroFaturamento($parfoid) {
		
		$numRows = 0;
		$sql = "
		SELECT
			*
		FROM
			parametros_faturamento
		WHERE
			parfoid = $parfoid
		";
		$result = pg_query($this->conn, $sql);
		if (!$result) {
			throw new Exception("Erro ao buscar parâmetro para faturamento.");
		} else {
			
			$numRows = pg_num_rows($result);
			if ($numRows > 0) {
				return pg_fetch_object($result);
			} else {
				return null;
			}
		}
	}
	
	/**
	 * Seta a data de exclusão de um parâmetro
	 * @param integer $parfiod
	 * @throws Exception
	 * @return boolean
	 */
	public function excluirParametro($parfiod) {
		
		if (!isset($parfiod) || empty($parfiod) || !is_numeric($parfiod)) {
			throw new Exception("Parâmetro não informado ou inválido.");
		}
		
		$usuarioLogado 	= Sistema::getUsuarioLogado();
		$usuario 		= $usuarioLogado->cd_usuario; 
		
		$parametroFaturamento = $this->getParametroFaturamento($parfiod);
		
		$nivel 			= $parametroFaturamento->parfnivel;
		$contrato 		= $parametroFaturamento->parfconoid;
		$cliente 		= $parametroFaturamento->parfclioid;
		$tipoContrato	= $parametroFaturamento->parftpcoid;
		
		
		$nomeCliente			= "";
		$descricaoTipoContrato	= "";
		$observacao 			= "";
		$obsTipoContrato 		= "";
		$obsTipoCliente			= "";
		
		/**
		 * Prepara a "string" que deverá ser concatenada a observação da exclusão.
		 * Basicamente excluiremos as três primeiras linhas da observação que está
		 * salva no parâmetro de faturamento.
		 */
		$observacao_temp = explode("<br>", $parametroFaturamento->parfobservacao);
		$observacao_temp = array_slice($observacao_temp, 3);
		
		if (is_numeric($tipoContrato) && $tipoContrato >= 0) {
				
			/**
			 * Busca o tipo de contrato
			 */
			$sqlBuscaTipoContrato = "
			SELECT
				tpcdescricao
			FROM
				tipo_contrato
			WHERE
				tpcoid = $tipoContrato
			";
			$rsBuscaTipoContrato = pg_query($this->conn, $sqlBuscaTipoContrato);
			if (!$rsBuscaTipoContrato) {
				throw new Exception("Erro ao buscar o tipo contrato para o histórico");
			} else {
				$descricaoTipoContrato = pg_fetch_result($rsBuscaTipoContrato, 0, 'tpcdescricao');
			}
		}
		
		if (!$parametroFaturamento) {
			throw new Exception("Parâmetro não encontrado");
		}
		
		$observacao = "Operação: Exclusão<br>";
		$observacao .= "Data: ".date("d/m/Y H:i:s")."<br>";
		$observacao .= "Usuário: ".$usuarioLogado->nm_usuario."<br>";
		$observacao .= implode('<br>', $observacao_temp);
		
		pg_query($this->conn, "BEGIN;");
		
		$numAffected = 0;
		$sql = "
			UPDATE
				parametros_faturamento
			SET
				parfdt_exclusao = NOW()
			WHERE
				parfoid = $parfiod
		";
		$result = pg_query($this->conn, $sql);
		
		if (!$result) {
			
			pg_query($this->conn, "ROLLBACK;");
			throw new Exception("Erro ao excluir registro.");
		} else {
			
			$numAffected = pg_affected_rows($result);
			
			if ($numAffected == 0) {
				
				pg_query($this->conn, "ROLLBACK;");
				return false;
			} else {
				
				
				//CONTRATO
				if ($nivel == 1) {
				
					$sqlInsereHistoricoContrato = " SELECT historico_termo_i($contrato, $usuario, '$observacao'); ";
					$rsInsereHistoricoContrato = pg_query($this->conn, $sqlInsereHistoricoContrato);
					if (!$rsInsereHistoricoContrato) {
						pg_query($this->conn, "ROLLBACK;");
						throw new Exception("Erro ao inserir o histórico do contrato.");
					}
				}
				
				//CLIENTE
				if ($nivel == 2 ) {
													
					$sqlInsereHistoricoCliente = " SELECT cliente_historico_i($cliente, $usuario, '$observacao',  'A', '0', '0');";
					$rsInsereHistoricoCliente = pg_query($this->conn, $sqlInsereHistoricoCliente);
					if (!$rsInsereHistoricoCliente) {
						pg_query($this->conn, "ROLLBACK;");
						throw new Exception("Erro ao inserir o histórico do cliente.");
					}
				}
				
				//TIPO CONTRATO
				if ($nivel == 3 ) {
					
					$sqlInsereHistoricoTipoContrato = "
					INSERT INTO
						historico_tipo_contrato
					(
						htctpcoid,
						htcusuoid_cadastro,
						htcobs,
						htcdt_cadastro
					)
					VALUES
					(
						$tipoContrato,
						$usuario,
						'$observacao',
						NOW()
					)
					";
					$rsInsereHistoricoTipoContrato = pg_query($this->conn, $sqlInsereHistoricoTipoContrato);
					if (!$rsInsereHistoricoTipoContrato) {
						pg_query($this->conn, "ROLLBACK;");
						throw new Exception("Erro ao inserir o histórico do tipo contrato.");
					}
				}
				
				pg_query($this->conn, "COMMIT;");
				return true;
			} 
		}
	}

	/**
	 * Valida se os parâmetros estão duplicados
	 * @param array $parametros
	 * @throws Exception
	 * @return boolean
	 */
	public function validarParametros($parametros = array()) {
		
		try {
		
			if(count($parametros) == 0) {
				throw new Exception("Erro: Nenhum parâmetro foi informado.");
			}
		
			$sql = " 
					SELECT	
						pf.parfoid, pf.parfobroid_multiplo
					FROM	
						parametros_faturamento pf
					WHERE	
						pf.parfdt_exclusao IS NULL	
						AND (pf.parftipo IS NULL OR pf.parftipo <> 'IS')
						AND pf.parfprazo_vencimento IS NULL
					";
	
			
			switch($parametros['nivel']) {
				case 1 :
					$sql.= " AND pf.parfnivel = 1
							 AND pf.parfconoid = ".$parametros['contrato']." ";
					
					$tipo = 'contrato'; //usado na exibição da mensagem
					break;
						
				case 2 :
					$sql.= " AND pf.parfnivel = 2
							 AND pf.parfclioid = ".$parametros['clioid']." ";
					
					$tipo = 'cliente'; //usado na exibição da mensagem
					break;
						
				case 3 :
					$sql.= " AND pf.parfnivel = 3
							 AND pf.parftpcoid = ".$parametros['tipo_contrato']." ";
					
					$tipo = 'tipo contrato'; //usado na exibição da mensagem
					break;
			}

			
			if(!$resultado = pg_query($this->conn, $sql)) {
				throw new Exception("Erro ao validar a duplicidade dos parâmetros.");
			}
			
			if(pg_num_rows($resultado)) {
			
				$msg = "Parâmetro do faturamento, para o ".$tipo.", já cadastrado.";
				return $resultado;
			
			} else {
				return $resultado;
			}
			
			    
		} catch (Exception $e) {
			return $e->getMessage();
		}

	}
	
	
	/**
	 * STI 84969
	 * Método que executa a consulta para buscar os tipos de parâmetro
	 * 
	 * @param int $cod
	 * @throws Exception
	 * @return multitype:
	 */
	public function verificarTipoParametro($id){
	
		try {
				
			$sql = " SELECT parftipo
					   FROM parametros_faturamento
					  WHERE parfoid = $id ";
				
			if(!$rs = pg_query($this->conn, $sql)){
				throw new Exception('Erro ao buscar o tipo do parâmetro.');
			}
				
			if (pg_num_rows($rs) > 0) {
				return pg_fetch_array($rs);
			}
				
		} catch (Exception $e) {
			return $e->getMessage();
		}
	
	}

    /**
     * Valida se os parâmetros estão duplicados
     * @param array $parametros
     * @throws Exception
     * @return boolean
     */
    public function validarParametrosPrazoVigencia($parametros = array()) {

        try {

            if(count($parametros) == 0) {
                throw new Exception("Erro: Nenhum parâmetro foi informado.");
            }

            $sql = " 
					SELECT	
						pf.parfoid, pf.parfprazo_vencimento
					FROM	
						parametros_faturamento pf
					WHERE	
						pf.parfdt_exclusao IS NULL	
						AND (pf.parftipo IS NULL OR pf.parftipo <> 'IS')
						AND pf.parfprazo_vencimento IS NOT NULL
					";

            if($parametros['nivel'] == 2 && $parametros['prazo_vencimento'] != ''){
                $sql.= " AND pf.parfnivel = 2
							 AND pf.parfclioid = ".$parametros['clioid']." ";

                $tipo = 'cliente'; //usado na exibição da mensagem
            }

            if(!$resultado = pg_query($this->conn, $sql)) {
                throw new Exception("Erro ao validar a duplicidade dos parâmetros.");
            }

            if(pg_num_rows($resultado)) {

                $msg = "Parâmetro do faturamento (Prazo de Vencimento), para o ".$tipo.", já cadastrado.";
                return $resultado;

            } else {
                return $resultado;
            }


        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

        /** Pesquisa **/
	
	/**
	 * Método que executa a consulta do parâmetro
	*/

       public function buscarParametro($contrato, $nivel, $isento, $valor, $desconto) {

        $sqlNivel = "";
        $sqlParam = "";

        if (!empty($nivel)) {
            $sqlNivel .= " AND parfnivel=$nivel ";
        }
        
        if(empty($isento) && empty($desconto) && empty($valor)){
            return false;
        }
        
        $sqlParam .= " AND (";
       
        if (!empty($isento)) {
            $sqlParam .= "  parfisento = true ";
        }

        if (!empty($valor) && !empty($isento)) {
            $sqlParam .= " OR parfvl_cobrado > 0";
        } else if (!empty($valor)) {
            $sqlParam .= " parfvl_cobrado > 0";
        }

        if (!empty($desconto) && (!empty($isento) || !empty($valor))) {
            $sqlParam .= " OR parfdesconto > 0";
        } else if (!empty($desconto)) {
            $sqlParam .= " parfdesconto > 0";
        }

        $sqlParam .= " )";
       
        if ($sqlParam == "") {
            return false;
        }

        $sql = "SELECT
		            parfoid 
				FROM
				parametros_faturamento
				WHERE true
				AND parfconoid=" . $contrato . " 
                                $sqlParam
                                AND parfdt_exclusao is null 
                                AND parfativo is not false; ";

        $result = pg_query($this->conn, $sql);
        
        if (!$result) {
            throw new Exception("Erro ao buscar parametros");
        } else {
            return pg_fetch_all($result);
        }


        return false;
    }

}