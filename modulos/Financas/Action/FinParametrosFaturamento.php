<?php

/**
 * Classe de persistência de dados
 */
require (_MODULEDIR_ . "Financas/DAO/FinParametrosFaturamentoDAO.php");

require 'lib/Components/ComponenteBuscaCliente.php';

/**
 * FinParametrosFaturamento.php
 *
 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
 * @package Finanças
 * @since 22/01/2013
 *
*/
class FinParametrosFaturamento {

	private $dao;

	private $comp_cliente_params = array(
			'id'  => 'cliente_id',
			'name'=> 'cliente_nome',
			'cpf' => 'cliente_cpf',
			'cnpj'=> 'cliente_cnpj',
			'tipo_pessoa' => 'tipo_pessoa',
			'btnFind' => true,
			'btnFindText' => 'Pesquisar',
			'data' => array(
					'table'                    	=> 'clientes',
					'where'						=> 'clidt_exclusao is null',
					'fieldFindByText'          	=> 'clinome',
					'fieldFindById'            	=> 'clioid',
					'fieldFindByCPF'           	=> 'clino_cpf',
					'fieldFindByTipoPessoa'    	=> 'clitipo',
					'fieldFindByCNPJ'          	=> 'clino_cgc',
					'fieldLabel'               	=> 'clinome',
					'fieldReturn'              	=> 'clioid',
					'fieldReturnCPF'           	=> 'clino_cpf',
					'fieldReturnCNPJ'          	=> 'clino_cgc'
			)
	);
	
	/*
	 * Construtor
	 */
	public function FinParametrosFaturamento() {

		global $conn;

		$this->dao = new FinParametrosFaturamentoDAO($conn);
		
		//componente para pesquisa de clientes
		$this->comp_cliente = new ComponenteCliente($this->comp_cliente_params);
		
	}
	
	/**
	 * Método que efetua a pesquisa do relatorio
	 * @author Renato Teixeira Bueno <renato.bueno@meta.com.br>
	 */
	public function pesquisar(){

        $contrato 					= (!empty($_POST['contrato'])) ? $_POST['contrato'] : '';
        $cliente 					= (!empty($_POST['cliente'])) ? trim($_POST['cliente']) : '';
        $tipo_contrato 				= (!empty($_POST['tipo_contrato']) && $_POST['tipo_contrato'] >= 0 && is_numeric($_POST['tipo_contrato'])) ? $_POST['tipo_contrato'] : '';

        $obrigacao_financeira 		= (!empty($_POST['obrigacao_financeira'])) ? $_POST['obrigacao_financeira'] : '';
        $macro_motivo 		        = (!empty($_POST['macro_motivo'])) ? $_POST['macro_motivo'] : '';
        $micro_motivo 		        = (!empty($_POST['micro_motivo'])) ? $_POST['micro_motivo'] : '';
        $documento                  = (!empty($_POST['documento'])) ? $_POST['documento'] : '';
        //$periodicidade_faturamento 	= (!empty($_POST['periodicidade_faturamento'])) ? $_POST['periodicidade_faturamento'] : '';

        $isento_cobranca 						= (!empty($_POST['isento_cobranca'])) ? implode(', ', $_POST['isento_cobranca']) : '';
        $isento_cobranca_original             = (!empty($_POST['isento_cobranca'])) ? $_POST['isento_cobranca'] : array();

        $nivel 						= (!empty($_POST['nivel'])) ? implode(', ', $_POST['nivel']) : '';
        $nivel_original             = (!empty($_POST['nivel'])) ? $_POST['nivel'] : array();

        $vigencia 						= (!empty($_POST['vigencia'])) ? implode(', ', $_POST['vigencia']) : '';
        $vigencia_original             = (!empty($_POST['vigencia'])) ? $_POST['vigencia'] : array();

        $dt_ini 		        = (!empty($_POST['adt_ini'])) ? $_POST['adt_ini'] : '';
        $dt_fim 		        = (!empty($_POST['adt_fim'])) ? $_POST['adt_fim'] : '';

        if(preg_match('#(\d{1,2})\D(\d{1,2})\D(\d{2})#',$dt_ini,$match)){
            $time = mktime(0,0,0,$match[2],$match[1],$match[3]);
            $dt_ini = date('Y-m-d',$time);
        }
        if(preg_match('#(\d{1,2})\D(\d{1,2})\D(\d{2})#',$dt_fim,$match)){
            $time = mktime(0,0,0,$match[2],$match[1],$match[3]);
            $dt_fim = date('Y-m-d',$time);
        }

        $filtro = array();
        $filtro_pesquisa = "";
        $filtro_pesquisa .= " \n";

        if($contrato != ''){
            array_push($filtro, "AND parfconoid = $contrato");
            $filtro_pesquisa .= "Contrato: ".$contrato." \n";
        }

        if($cliente != ''){
            $cliente = pg_escape_string(stripslashes($cliente));
            array_push($filtro, "AND clinome ILIKE '%$cliente%'");
            $filtro_pesquisa .= "Cliente: ".$cliente." \n";
        }

        if( $tipo_contrato != '' && $tipo_contrato >= 0 && is_numeric($tipo_contrato)){
            array_push($filtro, "AND parftpcoid = $tipo_contrato");
            $filtro_pesquisa .= "Tipo Contrato: ".$tipo_contrato." \n";
        }

        if($obrigacao_financeira != ''){
            array_push($filtro, "AND (parfobroid = ".$obrigacao_financeira." OR parfobroid_multiplo && ARRAY[".$obrigacao_financeira."] )");
            $filtro_pesquisa .= "Obrigação(ões) Financeira(s): ".$obrigacao_financeira." \n";
        }

        $documento = preg_replace('/[^\w]/','', $documento);

        if(strlen($documento) >= 14){
            $cnpj = $documento;
        }else{
            $cpf = $documento;
        }

        if($cnpj != ''){
            array_push($filtro, "AND clino_cgc = $cnpj");
            $filtro_pesquisa .= "CNPJ: ".$cnpj." \n";
        }

        if($cpf != ''){
            array_push($filtro, "AND clino_cpf = $cpf");
            $filtro_pesquisa .= "CPF: ".$cpf." \n";
        }

        if($periodicidade_faturamento != ''){
            array_push($filtro, "AND parfperiodicidade = $periodicidade_faturamento");
            $filtro_pesquisa .= "Periodicidade do Faturamento: ".$periodicidade_faturamento." \n";
        }

        if($isento_cobranca != ''){

            $palavra0 = "1, 2, 3";
            if (preg_match("%\b{$palavra0}\b%", $isento_cobranca)){

            }else {
                $palavra4 = "1, 2";
                if (preg_match("%\b{$palavra4}\b%", $isento_cobranca)) {
                    array_push($filtro, "AND (parfisento = 't' OR parfvl_cobrado != 0) ");
                }else {
                    $palavra5 = "1, 3";
                    if (preg_match("%\b{$palavra5}\b%", $isento_cobranca)) {
                        array_push($filtro, "AND (parfisento = 't' OR parfdesconto != 0) ");
                    }else {
                        $palavra6 = "2, 3";
                        if (preg_match("%\b{$palavra6}\b%", $isento_cobranca)) {
                            array_push($filtro, "AND (parfvl_cobrado != 0 OR parfdesconto != 0) ");
                        }else {
                            $palavra = "1";
                            if (preg_match("%\b{$palavra}\b%", $isento_cobranca)) {
                                array_push($filtro, "AND parfisento = 't' ");

                                if ($dt_ini != '') {
                                    array_push($filtro, "AND parfdt_ini_cobranca >= '$dt_ini'");
                                }
                                if ($dt_fim != '') {
                                    array_push($filtro, "AND parfdt_fin_cobranca <= '$dt_fim'");
                                }
                            }
                            $palavra2 = "2";
                            if (preg_match("%\b{$palavra2}\b%", $isento_cobranca)) {
                                array_push($filtro, "AND parfvl_cobrado != 0 ");

                                if ($dt_ini != '') {
                                    array_push($filtro, "AND parfdt_ini_valor >= '$dt_ini'");
                                }
                                if ($dt_fim != '') {
                                    array_push($filtro, "AND parfdt_fin_valor <= '$dt_fim'");
                                }
                            }
                            $palavra3 = "3";
                            if (preg_match("%\b{$palavra3}\b%", $isento_cobranca)) {
                                array_push($filtro, "AND parfdesconto != 0 ");

                                if ($dt_ini != '') {
                                    array_push($filtro, "AND parfdt_ini_desconto >= '$dt_ini'");
                                }
                                if ($dt_fim != '') {
                                    array_push($filtro, "AND parfdt_fin_desconto <= '$dt_fim'");
                                }
                            }
                        }
                    }
                }
            }

            $isento_cobranca2 = str_replace("1","Isento Cobrança ",$isento_cobranca);
            $isento_cobranca2 = str_replace("2","Valor ",$isento_cobranca2);
            $isento_cobranca2 = str_replace("3","% de Descontos ",$isento_cobranca2);

            $filtro_pesquisa .= "Isenção de : ".$isento_cobranca2." \n";
        }

        if (($dt_ini != '' || $dt_fim != '') && $isento_cobranca == ''){
            if ($dt_ini != '') {
                array_push($filtro, "AND parfdt_ini_cobranca >= '$dt_ini'");
                array_push($filtro, "AND parfdt_ini_valor >= '$dt_ini'");
                array_push($filtro, "AND parfdt_ini_desconto >= '$dt_ini'");
            }
            if ($dt_fim != '') {
                array_push($filtro, "AND parfdt_fin_cobranca <= '$dt_fim'");
                array_push($filtro, "AND parfdt_fin_valor <= '$dt_fim'");
                array_push($filtro, "AND parfdt_fin_desconto <= '$dt_fim'");
            }
        }
	
        if($nivel != ''){
            array_push($filtro, "AND parfnivel IN ($nivel)");
            $nivel2= "";

            $nivel2 = str_replace("1, 3, 2","Todos ",$nivel);


            $nivel2 = str_replace("1","Contrato ",$nivel2);
            $nivel2 = str_replace("2","Cliente ",$nivel2);
            $nivel2 = str_replace("3","Tipo Contrato ",$nivel2);


            $filtro_pesquisa .= "Nível: ".$nivel2." \n";
        }

        if($macro_motivo != ''){
            array_push($filtro, "AND parfmotivo_macro = $macro_motivo");
            $filtro_pesquisa .= "Macro Motivo: ".$macro_motivo." \n";
        }

        if($micro_motivo != ''){
            array_push($filtro, "AND parfmotivo_micro = $micro_motivo");
            $filtro_pesquisa .= "Micro Motivo: ".$micro_motivo." \n";
        }

        if($vigencia != ''){
        	$vigencia2 = str_replace("0","Todos ",$vigencia);
			if($vigencia == '1, 2'){
				$vigencia2 = str_replace("1, 2","Todos ",$vigencia);
			}
            $vigencia2 = str_replace("1","SIM ",$vigencia2);
            $vigencia2 = str_replace("2","NÃO ",$vigencia2);

            $filtro_pesquisa .= "Vigencia: ".$vigencia2." \n";
        }

		if(!empty($filtro)){
			
			array_push($filtro, ' AND parfdt_exclusao IS NULL');
			
			$where = implode(' ', $filtro);
			
			$dadosPesquisa = $this->dao->pesquisar($where);

            $filtro_vigencia = array();

            if ($vigencia2 != ""){
                if ($vigencia2 != "Todos ") {
                    foreach ($dadosPesquisa as $cConteudo) {
                        if ($vigencia == "1"){
                            if ($cConteudo['vigencia'] == "SIM") {
                                array_push($filtro_vigencia, $cConteudo);
                            }
                        }
                        if ($vigencia == "2"){
                            if ($cConteudo['vigencia'] == "NÃO") {
                                array_push($filtro_vigencia, $cConteudo);
                            }
                        }
                    }
                    $dadosPesquisa = $filtro_vigencia;
                }
            }

			if(is_array($dadosPesquisa)){
				return $dadosPesquisa;
			}

		}
		return false;
			
	}
	
	
	public function verificarCadastro(){
		
		$nivel 						= (!empty($_POST['nivel'])) ? $_POST['nivel'] : '';
		$contrato 					= (!empty($_POST['contrato'])) ? $_POST['contrato'] : '';
		$clioid 					= (!empty($_POST['cod_cliente'])) ? trim($_POST['cod_cliente']) : '';
		$tipo_contrato 				= (!empty($_POST['tipo_contrato']) && $_POST['tipo_contrato'] >= 0 && is_numeric($_POST['tipo_contrato']) || $_POST['tipo_contrato'] == 0) ? $_POST['tipo_contrato'] : '';
		
		$parametros = array(
				'nivel'                       => $nivel,
				'contrato' 		              => $contrato,
				'clioid'			          => $clioid,
				'tipo_contrato'		          => $tipo_contrato
		);
		
		//verifica se já possui parâmetro cadastrado para inserção
		//$retorno = $this->dao->validarParametros($parametros);
		
		//echo json_encode(utf8_encode($retorno));
		exit;
	}
	
	
	
	/**
	 * Método para buscar os tipos de contratos ativos no sistema
	 */
	public function buscaTipoContrato($todos = false){
		
		$retorno = $this->dao->buscarTipoContrato();
		
		$tipo_contrato = array();
		
		if(count($retorno) > 0){
			
			foreach($retorno as $tipo){
				$tipo_contrato[] = array(
					'id' => $tipo['id_tipo_contrato'],
					'descricao' => $tipo['descricao']	
				);
			}			
		}
		
		if($todos){
			return  $tipo_contrato;
		}
		
		echo json_encode($tipo_contrato);
		exit;
	}
	
	/**
	 * Método para buscar as obrigações financeiras
	 */
	public function buscaObrigacaoFinanceira($todos = false){
	
		$retorno = $this->dao->buscarObrigacaoFinanceira();
		
		$obr_financeira = array();
		
		if(count($retorno) > 0){
		
			foreach($retorno as $obr){
				$obr_financeira[] = array(
						'id' => $obr['id'],
						'descricao' => $obr['descricao']
				);
			}
		}
		
		if($todos){
			return  $obr_financeira;
		}
		
		echo json_encode($obr_financeira);
		exit;
		
	}


    /**
     * Método para buscar os  Macros Motivos
     */
    public function buscaMacroMotivo($todos = false){

        $retorno = $this->dao->buscarMacroMicroMotivo($tipo = "MACRO");

        $micro_macro_motivo = array();

        if(count($retorno) > 0){

            foreach($retorno as $mic_mac_motivo){
                $micro_macro_motivo[] = array(
                    'id' => $mic_mac_motivo['id'],
                    'tipo' => $mic_mac_motivo['tipo'],
                    'motivo' => $mic_mac_motivo['motivo']
                );
            }
        }

        if($todos){
            return  $micro_macro_motivo;
        }

        echo json_encode($micro_macro_motivo);
        exit;

    }

    /**
     * Método para buscar os Micros Motivos
     */
    public function buscaMicroMotivo($todos = false){

        $retorno = $this->dao->buscarMacroMicroMotivo($tipo = "MICRO");

        $micro_macro_motivo = array();

        if(count($retorno) > 0){

            foreach($retorno as $mic_mac_motivo){
                $micro_macro_motivo[] = array(
                    'id' => $mic_mac_motivo['id'],
                    'tipo' => $mic_mac_motivo['tipo'],
                    'motivo' => $mic_mac_motivo['motivo']
                );
            }
        }

        if($todos){
            return  $micro_macro_motivo;
        }

        echo json_encode($micro_macro_motivo);
        exit;

    }
	
	/** ## Inclusão e Edição ## **/
	
	
	/**
	 * Cadastrar novo parâmetro
	 */
	public function novo() {
		
		$mensagemInformativa = "";
		
		
		try {
			$listaTipoContrato 			= $this->dao->buscarTipoContrato();
			$listaObrigacaoFinanceira 	= $this->dao->buscarObrigacaoFinanceira();
			$listaMacroMotivo 	        = $this->dao->buscarMacroMicroMotivo($tipo = "MACRO");
			$listaMicroMotivo 	        = $this->dao->buscarMacroMicroMotivo($tipo = "MICRO");
			unset($_POST);
			
		} catch (Exception $e) {
			$mensagemInformativa = $e->getMessage();
		}
		
		$_POST = array(
			'contrato'                   => null,
			'clioid'                     => null,
			'tipo_pessoa'                => null,
			'tipo_pessoa_literal'        => null,
			'nome_cliente'               => null,
			'cpf_cnpj_cliente'           => null,
			'cpf_cnpj'                   => null,
			'tipo_contrato'              => null,
			'obrigacao_financeira'       => null,
			'valor'                      => null,
			'isento_cobranca'            => null,
			'perc_desconto'              => null,
		    'periodicidade_reajuste'     => null,
			'obs_param'	                 => null,
			'mic_motivo'	             => null,
			'mac_motivo'	             => null
			//'periodicidade_faturamento'  => null,
			//'quantidade_faturamento_de'  => null,
			//'quantidade_faturamento_ate' => null ,
	
		);
		
		include 'modulos/Financas/View/fin_parametros_faturamento/insere_edita_parametros_faturamento.php';
	}
	
	
	/**
	 * Salva o parâmetro de acordo com o preenchimento do formulário
	 */
	public function salvar() {
			
		//VAR
		$parfoid 					    = isset($_POST['parfoid'])  && !empty($_POST['parfoid']) ? $_POST['parfoid'] : '';
		$nivel						    = isset($_POST['nivel']) ? $_POST['nivel'] : '';
		$contrato 				        = isset($_POST['contrato']) ? $_POST['contrato'] : ''; 
		$clioid						    = isset($_POST['cpx_valor_cliente_nome']) ? $_POST['cpx_valor_cliente_nome'] : ''; 
		
		//se tiver $parfoid é uma edição então pega o id do clioid
		if($parfoid != ''){
			$clioid					    = isset($_POST['clioid']) ? $_POST['clioid'] : '';
		}
		
		$tipoContrato				    = isset($_POST['tipo_contrato']) ? $_POST['tipo_contrato'] : ''; 
		$obrigacaoFinanceiraMultiplo    = isset($_POST['checkbox_obrigacao_financeira']) ? $_POST['checkbox_obrigacao_financeira'] : '';  
		$macroMotivo                    = isset($_POST['radio_macro']) ? $_POST['radio_macro'] : '';
		$microMotivo                    = isset($_POST['radio_micro']) ? $_POST['radio_micro'] : '';
		$valor						    = isset($_POST['valor']) ? $_POST['valor'] : '';
        $valorDtIni		                = isset($_POST['valor_dt_ini']) ? $_POST['valor_dt_ini'] : '';
        $valorDtFim		                = isset($_POST['valor_dt_fim']) ? $_POST['valor_dt_fim'] : '';
		$isentoCobranca				    = isset($_POST['isento_cobranca']) ? $_POST['isento_cobranca'] : FALSE;
		$isentoCobrancaDtIni		    = isset($_POST['isento_cobranca_dt_ini']) ? $_POST['isento_cobranca_dt_ini'] : ''; 
		$isentoCobrancaDtFim		    = isset($_POST['isento_cobranca_dt_fim']) ? $_POST['isento_cobranca_dt_fim'] : '';
		$percDesconto				    = isset($_POST['perc_desconto']) ? $_POST['perc_desconto'] : '';
        $prazo_vencimento               = isset($_POST['prazo_vencimento']) ? $_POST['prazo_vencimento'] : '';
		$percDescontoDtIni			    = isset($_POST['perc_desconto_dt_ini']) ? $_POST['perc_desconto_dt_ini'] : '';  
		$percDescontoDtFim			    = isset($_POST['perc_desconto_dt_fim']) ? $_POST['perc_desconto_dt_fim'] : '';   
		$periodicidadeReajuste     	    = isset($_POST['periodicidade_reajuste']) ? $_POST['periodicidade_reajuste'] : ''; 
		$observacao_usuario                 = isset($_POST['obs_param']) ? trim(htmlentities(nl2br(strip_tags($_POST['obs_param'])))) : '';  
                $param_massivo                      = isset($_POST['param_massivo']) ? $_POST['param_massivo'] : ''; 
               
                // ler arquivo csv
		if ($param_massivo == 1) {
                    
                    $contrato = '';
                    $arquivo = $_FILES['arqcontratos'];

                    list($nome, $ext) = explode(".", $arquivo['name']);

                    $tiposPermitidos = array('application/vnd.ms-excel', 'text/csv', 'application/force-download');

                    if ($ext != 'csv') {
                        throw new Exception("Tipo de arquivo não permitido");
                    }

                    $contratos = array();
                    if (($handle = fopen($arquivo['tmp_name'], "r")) !== FALSE) {

                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            foreach($data as $cont){
                                $contratos[] = $cont;
                            }
                        }
                        fclose($handle);
                    }
                }
                //$periodicidadeFaturamento 	    = isset($_POST['periodicidade_faturamento']) ? $_POST['periodicidade_faturamento'] : 1;  
		//$quantidadeFaturamentoMin	    = isset($_POST['quantidade_faturamento_de']) ? $_POST['quantidade_faturamento_de'] : '';   
		//$quantidadeFaturamentoMax	    = isset($_POST['quantidade_faturamento_ate']) ? $_POST['quantidade_faturamento_ate'] : '';  

		//DUM 83078 Req.4/5
		//$trocaIsentas                   = isset($_POST['trocas_isentas']) ? $_POST['trocas_isentas'] : '';  
		//$trocaValor                     = isset($_POST['trocas_valor']) ? $_POST['trocas_valor'] : '';  
		
		try {
			
			$listaTipoContrato 			= $this->dao->buscarTipoContrato();
			$listaObrigacaoFinanceira 	= $this->dao->buscarObrigacaoFinanceira();
            $listaMacroMotivo 	        = $this->dao->buscarMacroMicroMotivo($tipo = "MACRO");
            $listaMicroMotivo 	        = $this->dao->buscarMacroMicroMotivo($tipo = "MICRO");

// 			if (trim($trocaValor) != '') {
// 				$trocaValor = str_replace('.', '', $trocaValor);
// 				$trocaValor = str_replace(',', '.', $trocaValor);
// 				$trocaValor = floatval($trocaValor);
// 			}
			
			
			if (!empty($parfoid)) {
				
				$parametroFaturamento  = $this->dao->getParametroFaturamento($parfoid);
				
				if ($parametroFaturamento == null) {
					throw new Exception("Erro ao recuperar os dados do parâmetro do faturamento.");
				}
			}
			
			
			// Recupera o nível selecionado se caso edição
			if ((isset($_POST['nivel']) && !empty($_POST['nivel'])) || $_POST['nivel'] === '0') {
				
				$nivel = $_POST['nivel'];
				
			} elseif ($parametroFaturamento != null) {
			
				$nivel = $parametroFaturamento->parfnivel;
				
				$_POST['nivel'] = $nivel;
				
			} else {
				throw new Exception("Nível não informado.");
			}
			
			
			if (empty($contrato) && $nivel == '1' && $param_massivo != 1) {
				throw new Exception("Para nível \"Contrato\"  deve ser informado o contrato.");
			}
                        
                        if (empty($contrato) && $nivel == '1' && $param_massivo == 1 && empty($arquivo)) {
				throw new Exception("Arquivo para importar não foi selecionado.");
			}
			
			if (empty($clioid) && $nivel == 2) {
				throw new Exception("Cliente não informado.");
			} 
						
			if ($tipoContrato != 0 && empty($tipoContrato) &&  $nivel == 3 ) {
				throw new Exception("Para nível \"Tipo Contrato\" o tipo do contrato deve ser informado. ");
			}
		
			if(empty($obrigacaoFinanceiraMultiplo)) {
				throw new Exception("Pelo menos uma obrigação financeira deve ser selecionada.");
			}

            if(empty($macroMotivo)) {
                throw new Exception("Pelo menos um Macro Motivo deve ser selecionada.");
            }

            if(empty($microMotivo)) {
                throw new Exception("Pelo menos um Micro Motivo deve ser selecionada.");
            }

			if (!empty($valor)) {
				
				$valor = str_replace('.', '', $valor);
				$valor = str_replace(',', '.', $valor);

                if (is_numeric($valor) && $valor > 0) {

                    // Recebe a data inicial do período informado
                    if (empty($valorDtIni)) {
                        throw new Exception("A data início de desconto deve ser informado.");
                    }

                    // Recebe a data final do período informado
                    if (empty($valorDtFim)) {
                        throw new Exception("A data fim de desconto deve ser informado.");
                    } else {
                        // O Período final não poderá ser menor que o Período inicial.
                        //Para o Período final será permitido informar 99/99/9999 para período indeterminado.

                        if ($valorDtFim == "99/99/9999") {
                            $valorDtFim = "";
                        }
                    }
                }
				
			} else{
				$valor = NULL;
			} 
			
			// Recupera a opção isenção de cobrança e o período informado
			if ($isentoCobranca == 'on') {
				
				$isentoCobranca = true;
				
				// Recebe a data inicial do período informado
				if (empty($isentoCobrancaDtIni)) {
					throw new Exception("A data início da isenção deve ser informado.");
				} 
				
				// Recebe a data final do período informado
				if (empty($isentoCobrancaDtFim)) {
					
					throw new Exception("A data fim de isenção deve ser informado.");
				
				} else {
					
					 //Se Isento Cobrança for marcado é obrigatório informar o Período inicial e final.
					// O Período final não poderá ser menor que o Período inicial.
					// Para o Período final será permitido informar 99/99/9999 para período indeterminado.
				
					if ($isentoCobrancaDtFim == "99/99/9999") {
						$isentoCobrancaDtFim = "";
					} 
				}
			} 

			// Recupera a porcentagem de desconto informada e o período de validade do desconto
			if (!empty($percDesconto)) {
				
				$percDesconto = str_replace(',', '.', $percDesconto);
				
				if (is_numeric($percDesconto) && $percDesconto > 0) {
					
					// Recebe a data inicial do período informado
					if (empty($percDescontoDtIni)) {
						throw new Exception("A data início de desconto deve ser informado.");
					} 
					
					// Recebe a data final do período informado
					if (empty($percDescontoDtFim)) {
						
						throw new Exception("A data fim de desconto deve ser informado.");
						
					} else {
					
						 // Se Isento Cobrança for marcado é obrigatório informar o Período inicial e final.
						// O Período final não poderá ser menor que o Período inicial.
						//Para o Período final será permitido informar 99/99/9999 para período indeterminado.
						
						if ($percDescontoDtFim == "99/99/9999") {
							$percDescontoDtFim = "";
						}
					}
				}
			}
		
			
                        //se tiver vazio é inserção, então, verifica se já tem cadastro de parâmetro
                        if(empty($parfoid)){
                            
                            $obrigacoes_financeiras = $_POST['checkbox_obrigacao_financeira'];
                                                      
                            // validacao do massivo
                            if($param_massivo == 1){
                                
                                $contratoObrcadastrada = array();
                                
                                foreach ($contratos as $cont){
                                    $obrigacoesContrato = array();
                                    $parametros = array( 
                                                'nivel'	                      => $nivel,
                                                'contrato' 		              => $cont,
                                                'clioid'			          => $clioid,
                                                'tipo_contrato'		          => $tipoContrato
                                        );
                                    
                                     //verifica se já possui parâmetro cadastrado para inserção
                                    $retorno = $this->dao->validarParametros($parametros);
                                    $retorno = pg_fetch_all($retorno);
                                    
                                    //organiza todos as obrigacoes financeiras do (contrato,cliente ou tipo) em um array
                                    if($retorno !== false) {
                                        foreach ($retorno as $item) {
                                            $arrayObrigacoes = explode(',', str_replace("{", "", str_replace("}", "", $item['parfobroid_multiplo'])));
                                           
                                            foreach ($arrayObrigacoes as $obrigacao) { 
                                                array_push($obrigacoesContrato, $obrigacao);
                                            }
                                        }
                                        //verifica se já possui obrigação cadastrada no parametro
                                        foreach ($obrigacoes_financeiras as $obrigacoesIncluidas) {
                                              
                                            if (in_array($obrigacoesIncluidas, $obrigacoesContrato )) {
                                                
                                                switch($nivel) {
                                                    case 1 :
                                                        $tipo = 'contrato'; //usado na exibição da mensagem
                                                        break;
                                                    case 2 :
                                                        $tipo = 'cliente'; //usado na exibição da mensagem
                                                        break;
                                                    case 3 :
                                                        $tipo = 'tipo de contrato'; //usado na exibição da mensagem
                                                        break;
                                                }
                                                $contratoObrcadastrada[] = $cont;
                                                $mensagemInformativaNaoIncluido[] = "Parâmetro(s) do faturamento do contrato (s): ". $cont ." não incluido (s). Parâmetro de faturamento para este ".$tipo.": ". $cont . " e obrigação financeira já cadastrado.";
                                                continue;
                                            }
                                        }
                                    }
                                    
                                }
                               
                            }else{
                                $parametros = array( 
                                                'nivel'	                      => $nivel,
                                                'contrato' 		              => $contrato,
                                                'clioid'			          => $clioid,
                                                'tipo_contrato'		          => $tipoContrato
                                        );

                                $parametrosPrazoVigencia = array(
                                    'nivel'	                      => $nivel,
                                    'prazo_vencimento' 		      => $prazo_vencimento,
                                    'clioid'			          => $clioid
                                );

                                if($parametrosPrazoVigencia['nivel'] == 2 && $parametrosPrazoVigencia['prazo_vencimento'] != ''){
                                    $retornoPrazoVigencia = $this->dao->validarParametrosPrazoVigencia($parametrosPrazoVigencia);
                                    $retornoPrazoVigencia = pg_fetch_all($retornoPrazoVigencia);
                                    if ($retornoPrazoVigencia !== false) {
                                        $msg = "Parâmetro de faturamento para este cliente (Prazo de Vencimento) já cadastrado.";
                                        throw new Exception($msg);
                                    }
                                }else {
                                    //verifica se já possui parâmetro cadastrado para inserção
                                    $retorno = $this->dao->validarParametros($parametros);

                                    $obrigacoesContrato = array();
                                    $retorno = pg_fetch_all($retorno);

                                //organiza todos as obrigacoes financeiras do (contrato,cliente ou tipo) em um array
                                if($retorno !== false) {
                                    foreach ($retorno as $item) {
                                        $arrayObrigacoes = explode(',', str_replace("{", "", str_replace("}", "", $item['parfobroid_multiplo'])));

                                        foreach ($arrayObrigacoes as $obrigacao) {
                                            array_push($obrigacoesContrato, $obrigacao);
                                        }
                                    }
  
                                    //verifica se já possui obrigação cadastrada no parametro
                                    foreach ($obrigacoes_financeiras as $obrigacoesIncluidas) {
                                        if (in_array($obrigacoesIncluidas, $obrigacoesContrato )) {
                                            switch($nivel) {
                                                case 1 :
                                                    $tipo = 'contrato'; //usado na exibição da mensagem
                                                    break;
                                                case 2 :
                                                    $tipo = 'cliente'; //usado na exibição da mensagem
                                                    break;
                                                case 3 :
                                                    $tipo = 'tipo de contrato'; //usado na exibição da mensagem
                                                    break;
                                            }

                                            $msg = "Parâmetro de faturamento para este ".$tipo." e obrigação financeira já cadastrado.";
                                            throw new Exception($msg);
                                        }
                                    }
                                }
                            }
                    	}
			}
                       
                        //valida o contrato para o nivel 1 - Contrato 
			if($nivel == 1){
                              if($param_massivo == 1){
                             
                                  $contratoInvalido = array();
                                  
                                  foreach($contratos as $contrato){
                                       
                                        if (!$this->dao->contratoValido($contrato)) {
                                            $contratoInvalido[] = $contrato;
                                            $mensagemInformativaNaoIncluido[] = "Parâmetro(s) do faturamento do contrato (s): ". $m ." não incluido (s). O status do contrato deve estar ativo.";
                                        }
                                   }
                            }else{

                               if (!$this->dao->contratoValido($contrato)) {
                                            throw new Exception("O status do contrato deve estar ativo.");
                                }
                            }
		        }
		 	
		     $dados_param = array(  'parfoid'			            => $parfoid,
									'nivel'					        => $nivel,
									'contrato'				        => $contrato,
									'cliente'				        => $clioid,
									'tipo_contrato'			        => $tipoContrato,
									'isento'				        => $isentoCobranca,
									'desconto'				        => $percDesconto,
									'valor'					        => $valor,
									'data_ini_desconto'		        => $percDescontoDtIni,
									'data_fim_desconto'		        => $percDescontoDtFim,
									'data_ini_isento'		        => $isentoCobrancaDtIni,
									'data_fim_isento'		        => $isentoCobrancaDtFim,
									'data_ini_valor'		        => $valorDtIni,
									'data_fim_valor'		        => $valorDtFim,
									//'quantidade_min'		        => $quantidadeFaturamentoMin,
									//'quantidade_max'		        => $quantidadeFaturamentoMax,
									'periodicidade_reajuste'        => $periodicidadeReajuste,
									'prazo_vencimento'              => $prazo_vencimento,
									//'periodicidade'			        => $periodicidadeFaturamento ,
									'obrigacao_financeira_multiplo'	=> $obrigacaoFinanceiraMultiplo, 
									'macro_motivo'	                => $macroMotivo,
									'micro_motivo'	                => $microMotivo,
		     		                'observacao_usuario'            => $observacao_usuario
									//'troca_isentas'                 => $trocaIsentas,
									//'troca_valor'                   => $trocaValor
		     		            );
		     
		     if(empty($parfoid)){
                         
                         if($param_massivo == 1){
                             
                             foreach($contratos as $con){
                                
                                 if(!in_array($con, $contratoObrcadastrada) && !in_array($con, $contratoInvalido)){
                                      $dados_param['contrato'] = $con;
                                      $this->dao->insereParametro($dados_param);
                                  }else{
                                       $msg_ope = 'incluído(s)';
                                       $mensagemInformativa['msg']    = "Parâmetro(s) do faturamento $msg_ope com sucesso.";
                                       $mensagemInformativa['status'] = "OK";
                                  }
                            }
                         }else{
                         
                             $this->dao->insereParametro($dados_param);
                             $msg_ope = 'incluído(s)';
                             $mensagemInformativa['msg']    = "Parâmetro(s) do faturamento $msg_ope com sucesso.";
                             $mensagemInformativa['status'] = "OK";
                         }
		      
		        //limpa dados do post para o usuário inserir novos dados
		        unset($_POST);
		        
		     }else{
		     	
		     	 $this->dao->atualizaParametro($dados_param);
		     	 $msg_ope = 'alterados(s)';
		     }
                        
               } catch (Exception $e) {
			$mensagemInformativa['msg'] = $e->getMessage();	
			$mensagemInformativa['status'] = "ERRO";
		}
		
		include 'modulos/Financas/View/fin_parametros_faturamento/insere_edita_parametros_faturamento.php';
	}
	
	
	/**
	 * Popula o post com os dados para edição
	 */
	public function editar() {
	
		//VAR
		$parfoid 					= null;
		$nivel						= null;
		$parametroFaturamento		= null;
		$cliente					= null;
	
		$contrato 					= null;
		$clioid						= null;
		
		$tipoContrato				= null;
		$valor						= 0; // Decimal
        $valorDtIni		            = null;
        $valorDtFim		            = null;
		$isentoCobranca				= false;
		$isentoCobrancaDtIni		= null;
		$isentoCobrancaDtFim		= null;
		$percDesconto				= 0; // Decimal
		$percDescontoDtIni			= null;
		$percDescontoDtFim			= null;
        $macroMotivo                = null;
        $microMotivo                = null;
		$periodicidadeReajuste     	= null;
        $prazo_vencimento     	    = null;

		//$periodicidadeFaturamento 	= 1;
	
		//$quantidadeFaturamentoMin	= null;
		//$quantidadeFaturamentoMax	= null;
	
		try {
				
			if (!isset($_POST['parfoid']) || empty($_POST['parfoid']) || !is_numeric($_POST['parfoid']) || $_POST['parfoid'] <= 0) {
				throw new Exception("Parâmetro não informado ou inválido.");
			}
				
			$parfoid          = $_POST['parfoid'];
			
			unset($_POST);

			$_POST['parfoid'] = $parfoid;
			
			$listaTipoContrato 			= $this->dao->buscarTipoContrato();
			$listaObrigacaoFinanceira 	= $this->dao->buscarObrigacaoFinanceira();
            $listaMacroMotivo 	        = $this->dao->buscarMacroMicroMotivo($tipo = "MACRO");
            $listaMicroMotivo 	        = $this->dao->buscarMacroMicroMotivo($tipo = "MICRO");
			
			$parametroFaturamento	= $this->dao->getParametroFaturamento($parfoid);
				
			if ($parametroFaturamento == null) {
				throw new Exception("Parâmetro para faturamento não encontrado.");
			}
			
			$_POST['contrato'] 				= $parametroFaturamento->parfconoid;
			//$_POST['trocas_isentas'] 		= $parametroFaturamento->parfquantidade_trocas_isentas;
			//$_POST['trocas_valor']          = '';	
					
// 			if (isset($parametroFaturamento->parfvalor_taxa_unica) && trim($parametroFaturamento->parfvalor_taxa_unica) != '') {
// 				$_POST['trocas_valor'] 		= number_format($parametroFaturamento->parfvalor_taxa_unica ,2 ,',','.');
// 			}
			
			$_POST['periodicidade_reajuste'] = $parametroFaturamento->parfperiodicidade_reajuste == "" ? '' : $parametroFaturamento->parfperiodicidade_reajuste;

			if (isset($parametroFaturamento->parfclioid) && !empty($parametroFaturamento->parfclioid)) {
	
				$_POST['clioid']   			= $parametroFaturamento->parfclioid;
				
				$cliente					= $this->dao->buscarClientes(array('clioid'=>$parametroFaturamento->parfclioid));
				
				$_POST['tipo_pessoa']		  = $cliente[0]['tipo_pessoa'];
				$_POST['tipo_pessoa_literal'] = $cliente[0]['tipo_pessoa'];
				$_POST['nome_cliente']		  = $cliente[0]['nome'];
				
				$_POST['cpf_cnpj_cliente']	= $cliente[0]['cpf_cnpj'];
				
				if ($cliente[0]['tipo_pessoa'] == 'F') {
					$_POST['cpf_cnpj']			= str_pad($cliente[0]['cpf_cnpj'], 11, 0, STR_PAD_LEFT);
				} else {
					$_POST['cpf_cnpj']			= str_pad($cliente[0]['cpf_cnpj'], 14, 0, STR_PAD_LEFT);
				}
				
				if(trim($cliente[0]['dt_exclusao']) != '') {
					$mensagemInformativa = "Cliente inativo.";
				}
			}
				
			$_POST['nivel']    				= $parametroFaturamento->parfnivel;
			$_POST['tipo_contrato'] 		= $parametroFaturamento->parftpcoid;
			
			$_POST['checkbox_obrigacao_financeira'] = explode(',', str_replace("{", "", str_replace("}", "", $parametroFaturamento->parfobroid_multiplo)));

            $_POST['radio_macro']    				= $parametroFaturamento->parfmotivo_macro;
            $_POST['radio_micro']    				= $parametroFaturamento->parfmotivo_micro;

			if ($parametroFaturamento->parfvl_cobrado > 0) {
				$_POST['valor'] 			= number_format($parametroFaturamento->parfvl_cobrado, 2, ',', '.');
			} else {
				$_POST['valor']				= "";
			}

            if ($parametroFaturamento->parfvl_cobrado > 0) {

                if (!empty($parametroFaturamento->parfdt_ini_valor)) {
                    $valorDtIni				 = date("d/m/Y", strtotime($parametroFaturamento->parfdt_ini_valor));
                } else {
                    $valorDtIni = "99/99/9999";
                }

                if (!empty($parametroFaturamento->parfdt_fin_valor)) {
                    $valorDtFim				 = date("d/m/Y", strtotime($parametroFaturamento->parfdt_fin_valor));
                } else {
                    $valorDtFim = "99/99/9999";
                }

                $_POST['valor_dt_ini'] = $valorDtIni;
                $_POST['valor_dt_fim'] = $valorDtFim;
            }
				
			$_POST['isento_cobranca']		= $parametroFaturamento->parfisento == 't' ? 'on' : '';
				
			if ($parametroFaturamento->parfisento == 't') {
	
				if (!empty($parametroFaturamento->parfdt_ini_cobranca)) {
					$isentoCobrancaDtIni			 = date("d/m/Y",strtotime($parametroFaturamento->parfdt_ini_cobranca));
				} else {
					$isentoCobrancaDtIni =  "99/99/9999";
				}
				
				if (!empty($parametroFaturamento->parfdt_fin_cobranca)) {
					$isentoCobrancaDtFim			 = date("d/m/Y",strtotime($parametroFaturamento->parfdt_fin_cobranca));
				} else {
					$isentoCobrancaDtFim = "99/99/9999";
				}
	
				$_POST['isento_cobranca_dt_ini'] = $isentoCobrancaDtIni;
				$_POST['isento_cobranca_dt_fim'] = $isentoCobrancaDtFim;
			}
			
			if ($parametroFaturamento->parfdesconto > 0) { 
				$_POST['perc_desconto']			= number_format($parametroFaturamento->parfdesconto, 2, ',', '.');
			} else {
				$_POST['perc_desconto']			= "";
			}
			
			if ($parametroFaturamento->parfdesconto > 0) {
				
				if (!empty($parametroFaturamento->parfdt_ini_desconto)) {
					$percDescontoDtIni				 = date("d/m/Y", strtotime($parametroFaturamento->parfdt_ini_desconto));
				} else {
					$percDescontoDtIni = "99/99/9999";
				}
				
				if (!empty($parametroFaturamento->parfdt_fin_desconto)) {
					$percDescontoDtFim				 = date("d/m/Y", strtotime($parametroFaturamento->parfdt_fin_desconto));
				} else {
					$percDescontoDtFim = "99/99/9999";
				}
	
				$_POST['perc_desconto_dt_ini'] = $percDescontoDtIni;
				$_POST['perc_desconto_dt_fim'] = $percDescontoDtFim;
			}
				
			$_POST['obs_param']	    =  trim(str_replace('<br />',' ', html_entity_decode($parametroFaturamento->parfobservacao_usuario)));

            $_POST['prazo_vencimento']	    = $parametroFaturamento->parfprazo_vencimento;

			//$_POST['periodicidade_faturamento']	    = $parametroFaturamento->parfperiodicidade;

			//$_POST['quantidade_faturamento_de'] 	= $parametroFaturamento->parfqtd_min > 0 ? $parametroFaturamento->parfqtd_min : '';
			//$_POST['quantidade_faturamento_ate'] 	= $parametroFaturamento->parfqtd_max > 0 ? $parametroFaturamento->parfqtd_max : '';
			
		} catch (Exception $e) {
			$mensagemInformativa = $e->getMessage();
		}
		
		include 'modulos/Financas/View/fin_parametros_faturamento/insere_edita_parametros_faturamento.php';
	}
	
	
	/**
	 * Exclui o parâmetro em edição
	 * @throws Exception
	 */
	public function excluir() {
		
		$parfoid = null;
		
		try {
			
			if (!isset($_POST['parfoid']) || empty($_POST['parfoid'])) {
				throw new Exception("Parâmetro não informado.");
			}
			
			$parfoid = $_POST['parfoid'];
			
			$exclusao = $this->dao->excluirParametro($parfoid);
					
			if (!$exclusao) {
				throw new Exception("Erro ao excluir registro.");
			}
			
			
			$listaTipoContrato 			= $this->dao->buscarTipoContrato();
			$listaObrigacaoFinanceira 	= $this->dao->buscarObrigacaoFinanceira();
            $listaMacroMotivo 	        = $this->dao->buscarMacroMicroMotivo($tipo = "MACRO");
            $listaMicroMotivo 	        = $this->dao->buscarMacroMicroMotivo($tipo = "MICRO");
			
			$mensagemInformativa['msg']    = "Parâmetro do faturamento excluído com sucesso.";
			$mensagemInformativa['status'] = "OK";
			
			unset($_POST);
			
			
		} catch (Exception $e) {
			$mensagemInformativa['msg'] = $e->getMessage();
				$mensagemInformativa['status'] = "ERRO";
		}
		
		include 'modulos/Financas/View/fin_parametros_faturamento/insere_edita_parametros_faturamento.php';
	}
	
	
	/**
	 * STI 84969
	 * Retorna tipo de parâmetro e verifica o tipo retornado para aplicar a regra
	 * Recede id do parâmetro do faturamento via POST ou parâmetro da função
	 *
	 * @param string $id_param
	 * @throws Exception
	 * @return boolean
	 */
	public function verificarTipoParametro($id_param = NULL){
		
		$id = !empty($_POST['id_parametro']) ? $_POST['id_parametro'] : $id_param;
		
		try {
			
			if(empty($id)){
				throw new Exception("O id do Parâmetro deve ser informado.");
			}
			
			$retornaTipo = $this->dao->verificarTipoParametro($id);
			
			if(array_key_exists('parftipo', $retornaTipo)){
				
				if(trim($retornaTipo['parftipo']) === 'IS'){
					$cod_erro = 2;
					throw new Exception("Esse veículo/contrato foi inserido pelo módulo de Faturamento por Safra, não será permitido alterar.");
				}
				
				
				if($id_param != NULL){
					return true;
				}else{
					echo 1;//segue com o fluxo normalmente
					exit();
				}
				
			} else {
				$cod_erro = 3;
				throw new Exception("Não foi possível verificar o tipo de parâmetro.");
			}
			
			
		} catch (Exception $e) {
		
			if($id_param != NULL){
				return $e->getMessage();
			}else{
				
				$erro['tipo_erro'] = $cod_erro;
				$erro['msg']       = utf8_encode($e->getMessage());
				
				echo json_encode($erro);
				exit();
			}
		}		
		
	}

    /**
     * Gera um relatório CSV a partir de uma busca
     */
    public function gerar_csv() {

        $contrato 					= (!empty($_POST['contrato'])) ? $_POST['contrato'] : '';
        $cliente 					= (!empty($_POST['cliente'])) ? trim($_POST['cliente']) : '';
        $tipo_contrato 				= (!empty($_POST['tipo_contrato']) && $_POST['tipo_contrato'] >= 0 && is_numeric($_POST['tipo_contrato'])) ? $_POST['tipo_contrato'] : '';

        $obrigacao_financeira 		= (!empty($_POST['obrigacao_financeira'])) ? $_POST['obrigacao_financeira'] : '';
        $macro_motivo 		        = (!empty($_POST['macro_motivo'])) ? $_POST['macro_motivo'] : '';
        $micro_motivo 		        = (!empty($_POST['micro_motivo'])) ? $_POST['micro_motivo'] : '';
        $documento                  = (!empty($_POST['documento'])) ? $_POST['documento'] : '';
        //$periodicidade_faturamento 	= (!empty($_POST['periodicidade_faturamento'])) ? $_POST['periodicidade_faturamento'] : '';

        $isento_cobranca 						= (!empty($_POST['isento_cobranca'])) ? implode(', ', $_POST['isento_cobranca']) : '';
        $isento_cobranca_original             = (!empty($_POST['isento_cobranca'])) ? $_POST['isento_cobranca'] : array();

        $nivel 						= (!empty($_POST['nivel'])) ? implode(', ', $_POST['nivel']) : '';
        $nivel_original             = (!empty($_POST['nivel'])) ? $_POST['nivel'] : array();

        $vigencia 						= (!empty($_POST['vigencia'])) ? implode(', ', $_POST['vigencia']) : '';
        $vigencia_original             = (!empty($_POST['vigencia'])) ? $_POST['vigencia'] : array();

        $dt_ini 		        = (!empty($_POST['adt_ini'])) ? $_POST['adt_ini'] : '';
        $dt_fim 		        = (!empty($_POST['adt_fim'])) ? $_POST['adt_fim'] : '';

        if(preg_match('#(\d{1,2})\D(\d{1,2})\D(\d{2})#',$dt_ini,$match)){
            $time = mktime(0,0,0,$match[2],$match[1],$match[3]);
            $dt_ini = date('Y-m-d',$time);
        }
        if(preg_match('#(\d{1,2})\D(\d{1,2})\D(\d{2})#',$dt_fim,$match)){
            $time = mktime(0,0,0,$match[2],$match[1],$match[3]);
            $dt_fim = date('Y-m-d',$time);
        }

        $filtro = array();
        $filtro_pesquisa = "";
        $filtro_pesquisa .= " \n";

        if($contrato != ''){
            array_push($filtro, "AND parfconoid = $contrato");
            $filtro_pesquisa .= "Contrato: ".$contrato." \n";
        }

        if($cliente != ''){
            $cliente = pg_escape_string(stripslashes($cliente));
            array_push($filtro, "AND clinome ILIKE '%$cliente%'");
            $filtro_pesquisa .= "Cliente: ".$cliente." \n";
        }

        if( $tipo_contrato != '' && $tipo_contrato >= 0 && is_numeric($tipo_contrato)){
            array_push($filtro, "AND parftpcoid = $tipo_contrato");
            $filtro_pesquisa .= "Tipo Contrato: ".$tipo_contrato." \n";
        }

        if($obrigacao_financeira != ''){
            array_push($filtro, "AND (parfobroid = ".$obrigacao_financeira." OR parfobroid_multiplo && ARRAY[".$obrigacao_financeira."] )");
            $filtro_pesquisa .= "Obrigação(ões) Financeira(s): ".$obrigacao_financeira." \n";
        }

        $documento = preg_replace('/[^\w]/','', $documento);

        if(strlen($documento) >= 14){
            $cnpj = $documento;
        }else{
            $cpf = $documento;
        }

        if($cnpj != ''){
            array_push($filtro, "AND clino_cgc = $cnpj");
            $filtro_pesquisa .= "CNPJ: ".$cnpj." \n";
        }

        if($cpf != ''){
            array_push($filtro, "AND clino_cpf = $cpf");
            $filtro_pesquisa .= "CPF: ".$cpf." \n";
        }

        if($periodicidade_faturamento != ''){
            array_push($filtro, "AND parfperiodicidade = $periodicidade_faturamento");
            $filtro_pesquisa .= "Periodicidade do Faturamento: ".$periodicidade_faturamento." \n";
        }

        if($isento_cobranca != ''){

            $palavra0 = "1, 2, 3";
            if (preg_match("%\b{$palavra0}\b%", $isento_cobranca)){

            }else {
                $palavra4 = "1, 2";
                if (preg_match("%\b{$palavra4}\b%", $isento_cobranca)) {
                    array_push($filtro, "AND (parfisento = 't' OR parfvl_cobrado != 0) ");
                }else {
                    $palavra5 = "1, 3";
                    if (preg_match("%\b{$palavra5}\b%", $isento_cobranca)) {
                        array_push($filtro, "AND (parfisento = 't' OR parfdesconto != 0) ");
                    }else {
                        $palavra6 = "2, 3";
                        if (preg_match("%\b{$palavra6}\b%", $isento_cobranca)) {
                            array_push($filtro, "AND (parfvl_cobrado != 0 OR parfdesconto != 0) ");
                        }else {
                            $palavra = "1";
                            if (preg_match("%\b{$palavra}\b%", $isento_cobranca)) {
                                array_push($filtro, "AND parfisento = 't' ");

                                if ($dt_ini != '') {
                                    array_push($filtro, "AND parfdt_ini_cobranca >= '$dt_ini'");
                                }
                                if ($dt_fim != '') {
                                    array_push($filtro, "AND parfdt_fin_cobranca <= '$dt_fim'");
                                }
                            }
                            $palavra2 = "2";
                            if (preg_match("%\b{$palavra2}\b%", $isento_cobranca)) {
                                array_push($filtro, "AND parfvl_cobrado != 0 ");

                                if ($dt_ini != '') {
                                    array_push($filtro, "AND parfdt_ini_valor >= '$dt_ini'");
                                }
                                if ($dt_fim != '') {
                                    array_push($filtro, "AND parfdt_fin_valor <= '$dt_fim'");
                                }
                            }
                            $palavra3 = "3";
                            if (preg_match("%\b{$palavra3}\b%", $isento_cobranca)) {
                                array_push($filtro, "AND parfdesconto != 0 ");

                                if ($dt_ini != '') {
                                    array_push($filtro, "AND parfdt_ini_desconto >= '$dt_ini'");
                                }
                                if ($dt_fim != '') {
                                    array_push($filtro, "AND parfdt_fin_desconto <= '$dt_fim'");
                                }
                            }
                        }
                    }
                }
            }

            $isento_cobranca2 = str_replace("1","Isento Cobrança ",$isento_cobranca);
            $isento_cobranca2 = str_replace("2","Valor ",$isento_cobranca2);
            $isento_cobranca2 = str_replace("3","% de Descontos ",$isento_cobranca2);

            $filtro_pesquisa .= "Isenção de : ".$isento_cobranca2." \n";
        }

        if (($dt_ini != '' || $dt_fim != '') && $isento_cobranca == ''){
            if ($dt_ini != '') {
                array_push($filtro, "AND parfdt_ini_cobranca >= '$dt_ini'");
                array_push($filtro, "AND parfdt_ini_valor >= '$dt_ini'");
                array_push($filtro, "AND parfdt_ini_desconto >= '$dt_ini'");
            }
            if ($dt_fim != '') {
                array_push($filtro, "AND parfdt_fin_cobranca <= '$dt_fim'");
                array_push($filtro, "AND parfdt_fin_valor <= '$dt_fim'");
                array_push($filtro, "AND parfdt_fin_desconto <= '$dt_fim'");
            }
        }

        if($nivel != ''){
            array_push($filtro, "AND parfnivel IN ($nivel)");
            $nivel2= "";

            $nivel2 = str_replace("1, 3, 2","Todos ",$nivel);


            $nivel2 = str_replace("1","Contrato ",$nivel2);
            $nivel2 = str_replace("2","Cliente ",$nivel2);
            $nivel2 = str_replace("3","Tipo Contrato ",$nivel2);


            $filtro_pesquisa .= "Nível: ".$nivel2." \n";
        }

        if($macro_motivo != ''){
            array_push($filtro, "AND parfmotivo_macro = $macro_motivo");
            $filtro_pesquisa .= "Macro Motivo: ".$macro_motivo." \n";
        }

        if($micro_motivo != ''){
            array_push($filtro, "AND parfmotivo_micro = $micro_motivo");
            $filtro_pesquisa .= "Micro Motivo: ".$micro_motivo." \n";
        }

        if($vigencia != ''){
            $vigencia2 = str_replace("0","Todos ",$vigencia);

            $vigencia2 = str_replace("1","SIM ",$vigencia2);
            $vigencia2 = str_replace("2","NÃO ",$vigencia2);

            $filtro_pesquisa .= "Vigencia: ".$vigencia2." \n";
        }

        if(!empty($filtro)){
            array_push($filtro, ' AND parfdt_exclusao IS NULL');

            $where = implode(' ', $filtro);

            $dadosParaCSV = $this->dao->pesquisar($where);

            $filtro_vigencia = array();

            if ($vigencia2 != ""){
                if ($vigencia2 != "Todos ") {
                    foreach ($dadosParaCSV as $cConteudo) {
                        if ($vigencia == "1"){
                            if ($cConteudo['vigencia'] == "SIM") {
                                array_push($filtro_vigencia, $cConteudo);
                            }
                        }
                        if ($vigencia == "2"){
                            if ($cConteudo['vigencia'] == "NÃO") {
                                array_push($filtro_vigencia, $cConteudo);
                            }
                        }
                    }
                    $dadosParaCSV = $filtro_vigencia;
                }
            }
        }

        $num_registros = count($dadosParaCSV);

        if($dadosParaCSV == false){
            $dadosParaCSV = null;
            $num_registros = 0;
        }

        $nome_arquivo = "relatorio";

        // Gera o arquivo CSV
        $arquivo_csv = $this->gerarArquivoCsv($num_registros , $dadosParaCSV, $nome_arquivo, $filtro_pesquisa);

        if($arquivo_csv){
            $planilhaRelatorio = $arquivo_csv['arquivo_gerado'];
            return $planilhaRelatorio;
        }else{
            throw new Exception($arquivo_csv['msg_erro']);
        }
    }

    public function gerarArquivoCsv($num_registros , $mdadosParaCSV, $nome_arquivo, $filtro_pesquisa, $exclusao = null){

        try {
            ob_start();
           
            $nome_arquivo = explode(".",$nome_arquivo);

            $nomeArquivo =  $nome_arquivo[0] . "_" . date('d_m_y_H_i') . ".csv";
            $planilhaRelatorio = "/var/www/docs_temporario/" . $nomeArquivo;
            $handle	= fopen($planilhaRelatorio, "w+");

            if(!$handle){
                throw new Exception('Erro ao gerar o arquivo csv.');
            }

            //Total de Registros encontrados na pesquisa
            if($filtro_pesquisa != null){
                $linha	= "";
                $linha .= 'Filtros Selecionados'. ";";
                $linha .= $filtro_pesquisa. ";";
                $linha .= "\r\n";
                fwrite($handle, $linha);
            }
            
            $cabecalho = isset($exclusao) ? '"Registros excluídos";' : '"Resultado da Pesquisa";';

            // Parametros Encontrados
            if(count($mdadosParaCSV) > 0 ){

                //Cabecalho principal do Relatorio
                $linha	= "";
                $linha .= $cabecalho;
                $linha .= "\r\n";
                fwrite($handle, $linha);

                //Cabecalho das colunas dos itens encontrados
                $linha	= "";
                $linha .= '"Contrato";';
                $linha .= '"Cliente";';
                $linha .= '"Tp. Contrato";';
                $linha .= '"ID Obrig. Financ.";';
                $linha .= '"Obrig. Financ.";';
                $linha .= '"Valor";';
                $linha .= '"Período Valor";';
                $linha .= '"Isento de Cobrança";';
                $linha .= '"Período Isenção";';
                $linha .= '"% Desc.";';
                $linha .= '"Período Desconto";';
                $linha .= '"Periodicidade de Reajuste (meses)";';
                $linha .= '"Observação";';
                $linha .= '"Parametro Vigente";';
                $linha .= '"Macro Motivo";';
                $linha .= '"Micro Motivo";';
                $linha .= "\r\n";
                fwrite($handle, $linha);

                //Detalhe dos Parametros encontrados
               foreach($mdadosParaCSV as $cConteudo){

                    //list($contrato, $cliente, $tipo_contrato, $obrigacao_financeira, $valor_desconto, $periodo_valor, $isento_cobranca, $periodo_isento, $porcentagem_desc, $period_reajuste, $observacao, $vigente, $macro_motivo, $micro_motivo) = explode(",,", $cConteudo);

                    if($cConteudo["valor"] == '0,00'){
                        $cConteudo["valor"] = "-";
                        $cConteudo["periodo_valor"] = "-";
                    }if($cConteudo["isento"] == "Não"){
                        $cConteudo["periodo_isencao"] = "-";
                    }if($cConteudo["desconto"] == '0,00'){
                        $cConteudo["desconto"] = "-";
                        $cConteudo["periodo_desconto"] = "-";
                    }if($cConteudo["periodicidade_reajuste"] == null){
                        $cConteudo["periodicidade_reajuste"] = "-";
                    }

                    // ASM-5357 - Adição ID Obrigação Financeira, melhoria na exportação do CSV
                    foreach ($cConteudo["obr_financeira_lista"] as $indice => $obrigacao) {
                        $linha = "";
                        $linha .= ($indice == 0) ? $cConteudo["contrato"] . ';' : ';';
                        $linha .= $cConteudo["id_cliente"] . ';';
                        $linha .= $cConteudo["id_tipo_contrato"] . ';';
                        $linha .= trim($obrigacao['id']) . ';';
                        $linha .= trim($obrigacao['descricao']) . ';';
                        $linha .= $cConteudo["valor"] . ';';
                        $linha .= $cConteudo["periodo_valor"] . ';';
                        $linha .= $cConteudo["isento"] . ';';
                        $linha .= $cConteudo["periodo_isencao"] . ';';
                        $linha .= $cConteudo["desconto"] . ';';
                        $linha .= $cConteudo["periodo_desconto"] . ';';
                        $linha .= $cConteudo["periodicidade_reajuste"] . ';';
                        $linha .= preg_replace('/[\n|\r|\n\r|\r\n]{2,}/',' ', trim(str_replace('<br />',' ', html_entity_decode($cConteudo["parfobservacao_usuario"])))). ';';
                        $linha .= $cConteudo["vigencia"] . ';';
                        $linha .= $cConteudo["macro_motivo"] . ';';
                        $linha .= $cConteudo["micro_motivo"] . ';';
                        $linha .= "\r\n";
                        fwrite($handle, $linha);
                    }
                }

                //Rodape dos Parametros Encontrados
                $linha	= "";
                $linha .= "\r\n";
                $linha .= 'Resultado dos Parametros Encontrados;';
                $linha .= "\r\n";
                fwrite($handle, $linha);

            }

            //Total de Registros encontrados na pesquisa
            if($num_registros >= 0){
                $linha	= "";
                $linha .= 'Filtros Selecionados'. ";";
                $linha	= "";
                $linha .= 'Total de '.$num_registros .' registros encontrados para o relatório'. ";";
                $linha .= "\r\n";
                fwrite($handle, $linha);
            }  
            
            fclose($handle);
            ob_end_flush();
            
            $dados['status'] = true;
            $dados['arquivo_gerado'] = $planilhaRelatorio;

            $diretorio = '/var/www/docs_temporario/';

            if(!empty($nomeArquivo)){

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-disposition: attachment; filename="'.$nomeArquivo.'"');
                readfile($diretorio . $nomeArquivo);
                
            }else{
                echo "Houve algum problema ao gerar o relatório de parametros de faturamento.";
            }

            return $dados;

        }
        catch(Exception $e) {

            fclose($handle);
            unlink($planilhaRelatorio);

            $dados['status'] = false;
            $dados['msg_erro'] = $e->getMessage();

            return $dados;
        }

    }
    
    // ORGMKTOTVS-3517 [CRIS]
    public function excluir_massivo() {

        $nivel = isset($_POST['nivel_excluir']) ? $_POST['nivel_excluir'] : '';
        $isento = isset($_POST['isento_excluir']) ? $_POST['isento_excluir'] : '';
        $desconto = isset($_POST['desconto_excluir']) ? $_POST['desconto_excluir'] : '';
        $valor = isset($_POST['valor_excluir']) ? $_POST['valor_excluir'] : '';
        $arquivo = isset($_FILES['arqcontratos_excluir']) ? $_FILES['arqcontratos_excluir'] : '';

        try {
            // ler arquivo csv
            if ($arquivo) {

                list($nome, $ext) = explode(".", $arquivo['name']);

                $tiposPermitidos = array('application/vnd.ms-excel', 'text/csv', 'application/force-download');

                if ($ext != 'csv') {
                    throw new Exception("Tipo de arquivo não permitido");
                }

                $contratos = array();
                if (($handle = fopen($arquivo['tmp_name'], "r")) !== FALSE) {

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        foreach ($data as $cont) {
                           
                            $cont  = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $cont);
                            if (is_numeric($cont)) {
                                $contratos[] = $cont;
                            } else {
                                $mensagemInformativaNaoIncluido[] = "Formato inválido: ". $cont;
                                break;
                            }
                        }
                    }
                    fclose($handle);
                }
                // buscar o parâmetro
                if (!empty($contratos)) {

                    $parametrosExcluidos = array();
                    $contratos = array_filter($contratos);

                    foreach ($contratos as $contrato) {

                        $parametros = $this->dao->buscarParametro($contrato, $nivel, $isento, $valor, $desconto);

                        // excluir o parâmetro
                        if (!empty($parametros)) {

                            foreach ($parametros as $parfoid) {

                                $exclusao = $this->dao->excluirParametro($parfoid['parfoid']);

                                if ($exclusao) {

                                    $where = " AND parfoid= " . $parfoid['parfoid'];
                                    $parametrosExcluidos[] = $this->dao->pesquisar($where);
                                } else {
                                    $mensagemInformativaNaoIncluido[] = $parfoid['parfoid'] . " - Erro ao excluir Parâmetro do faturamento.";
                                    continue;
                                }
                            }
                        } else {
                            $mensagemInformativaNaoIncluido[] = " Não foi possível excluir: Parâmetro (os) do faturamento do contrato: $contrato não localizados com os parâmetros informados.";
                        }
                    }
                }
                // gerar csv retorno
                if (!empty($parametrosExcluidos)) {

                    $parametrosExcluidos2 = array();
                    foreach ($parametrosExcluidos as $param) {
                        foreach ($param as $p) {
                            $parametrosExcluidos2[] = $p;
                        }
                    }

                    unset($_POST);
                    unset($_FILES);

                    if ($this->gerarArquivoCsv(count($parametrosExcluidos2), $parametrosExcluidos2, "relatorio_exclusao_massivo", NULL, true)) {
                        $mensagemInformativa['msg'] = "Arquivo de exclusão processado com sucesso. Arquivo de retorno gerado.";
                        $mensagemInformativa['status'] = "OK";
                        exit();
                    }
                } else {
                    include 'modulos/Financas/View/fin_parametros_faturamento/excluir_massivo_parametros_faturamento.php';
                }
            } else {
                include 'modulos/Financas/View/fin_parametros_faturamento/excluir_massivo_parametros_faturamento.php';
            }
        } catch (Exception $e) {
            $mensagemInformativa['msg'] = $e->getMessage();
            $mensagemInformativa['status'] = "ERRO";
        }
    }

    // FIM ORGMKTOTVS-3517 [CRIS]
}