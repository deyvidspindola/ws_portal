<?php

require _MODULEDIR_ . 'Relatorio/DAO/RelDaTransacoesDAO.php';

/**
 * 
 */
class RelDatransacoes {
	
	/**
	 * Atributo para acesso a persistência de dados
	 */
	private $dao;
	private $conn;	
		
	/**
	 * Construtor
	 */
	public function __construct() {
	
		global $conn;
	
		$this->conn = $conn;
	
		/**
		 * Objeto - DAO
		 */
		$this->dao = new RelDaTransacoesDAO($conn);
	}
	
	public function view($action, $resultadoPesquisa = array(),$layoutCompleto = true) {
       
	   		
		if (!empty($_POST)) {	
				
			 $retorno = $this->pesquisarDados($_POST);			
			
			if ($retorno['processo'] == "TRUE") {	
				$this->download_csv_results($retorno['result'], $retorno['resultRelRetornoDa'], 'relatorio.csv');
				die();
			}
			
		}			
		
        if($action == 'index'){        	

        	if($layoutCompleto)
        		include _MODULEDIR_.'Relatorio/View/rel_da_transacoes/header.php';  
        	
        	    include _MODULEDIR_.'Relatorio/View/rel_da_transacoes/'.$action.'.php';
        
        	if($layoutCompleto)
        		include _MODULEDIR_.'Relatorio/View/rel_da_transacoes/footer.php';
    	}
    }
/**
 *  Gerar Arquivo csv 
 * @param: array obrigatorio,name
 */

     public function download_csv_results($results, $resultRelRetornoDa, $name = NULL)
	{
		
		//cria nome do arquivo
	    if(!$name){
	        $name = md5(uniqid() . microtime(TRUE) . mt_rand()). '.csv';
	    }

		//monta cabeçalho
		$csv = utf8_decode('"Titulo";"Cliente";"Tipo de Pessoa";"Valor Total";"Dt. Vencimento";"Dt. da Baixa";"For. CobranÃ§a";"Dt. Cadastro D.A.";"Dt Status";"Status";"Motivo"' . "\n");	       
	   
		if(is_array($results)){
	
		$i =1;
		foreach ($results as $dados) {
			
				$tipoPessoa = "";
			
				if($dados->clitipo == 'F'){
					$tipoPessoa = 'Física';
				}elseif($dados->clitipo == 'J'){
					$tipoPessoa = 'Jurídica';
				}else{
					$tipoPessoa = '';
				}
				
				$motivo = $dados->mrdamensagem;
				if($dados->titvl_pagamento != NULL && $dados->titvl_pagamento > 0 && $dados->titdt_pagamento != NULL){
			   		$status	 = 'Débito efetuado';				
				} elseif((($dados->titcod_retorno_deb_automatico != '00' && $dados->titcod_retorno_deb_automatico != '31')
							&& $dados->titcod_retorno_deb_automatico != "" && $dados->titno_remessa !="")
								|| in_array($dados->titcod_retorno_deb_automatico, array('PE','RC','NA'))) {
					$status = 'Débito não efetuado';
				}elseif($dados->titcod_retorno_deb_automatico == NULL){
					$status = 'Aguardando Débito';
					if($dados->titno_remessa == NULL)
						$motivo = 'Título não incluso no arquivo de remessa';
			}else {
				$status = '';
			}	

	          $csv .= '"'.$dados->titoid.'";"'.$dados->clinome.'";"'.$tipoPessoa.'";"'.$dados->titvl_titulo.'";"'.$dados->titdt_vencimento.'";"'.$dados->titdt_pagamento.'";"'.$this->titformacobranca($dados->titformacobranca).'";"'.$this->formatDataBD($dados->cliccadastro).'";"'.$this->formatDataBD($dados->dt_status).'";"'.$status.'";"'.$motivo.'"' . "\n";
		$i++;
		}
		}
				
		
		//monta os dados do log de débito automático
		if(is_array($resultRelRetornoDa)){
			
			$i =1;
			foreach ($resultRelRetornoDa as $dados_ret) {
				
				$tipoPessoa_ret = "";
				
				if($dados_ret->clitipo == 'F'){
					$tipoPessoa_ret = 'Física';
				}elseif($dados_ret->clitipo == 'J'){
					$tipoPessoa_ret = 'Jurídica';
				}else{
					$tipoPessoa_ret = '';
				}
		
				$status_ret = 'Débito não efetuado';
		
				$csv .= '"'.$dados_ret->ldaatitoid.'";"'.$dados_ret->clinome.'";"'.$tipoPessoa_ret.'";"";"";"";'.$dados_ret->forma_cobranca.';'.$this->formatDataBD($dados_ret->cliccadastro).';"'.$this->formatDataBD(trim($dados_ret->ldaadt_inclusao)).'";"'.$status_ret.'";"'.$dados_ret->ldaaobs.'"' . "\n";
				$i++;
			}
		}
		
		
		//OUPUT HEADERS
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-type: text/csv");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$name);
		header("Content-Transfer-Encoding: binary"); 

		print $csv;
		
		// http://www.ziplineinteractive.com/blog/proper-php-headers-for-csv-documents-all-browsers/
		
	}
	 
	public function index() {	
		
		$param['parametros_banco'] = $this->GetClassesParametros_banco();
        	
    	$this->view('index', $param, true, false);
    }
	
	public function GetClassesParametros_banco(){
		
		return $this->dao->GetClassesParametros_banco();
		
	}
	
	private function titformacobranca($forcoid){
		
		$result =  $this->dao->titformacobrancaBd($forcoid);
		return $result[0]->forcnome;
		
		
	}
	/*
	 * Verificação e validação dos dados que veio no post
	 * 
	 * */
	public function pesquisarDados($dados)
	{

			$clinome = $dados['clinome'];
			$clitipo = $dados['clitipo'];
			$dataInicial_v = $this->formatDataVazia($dados['dataInicial_v']); 
			$dataFinal_v = $this->formatDataVazia($dados['dataFinal_v']) ;
			$dataInicial_p =  $this->formatDataVazia($dados['dataInicial_p']) ;
			$dataFinal_p = $this->formatDataVazia($dados['dataFinal_p']) ;
			$dataInicial_c =	$this->formatDataVazia($dados['dataInicial_c']) ;
			$dataFinal_c =	$this->formatDataVazia($dados['dataFinal_c']) ;
			$dataInicial_ret = $this->formatDataVazia($dados['dataInicial_ret']) ;
			$dataFinal_ret   = $this->formatDataVazia($dados['dataFinal_ret']) ;
			
			$banco =	$dados['banco'] ;
			$status =	$dados['status'] ;
			
			// mensagem 
			$periodo ="Favor selecionar ao menos um período para pesquisa.";
			$inicial = "Favor preencher a data inicial e a data final do período.";
			$menor = "A data inicial deve ser menor ou igual a data final.";
			$datainvalida = "Data inválida!";
			$datasuperior = "O período selecionado não pode ser maior que 1 ano.";
			$nenhum = 'Nenhum registro encontrado.';	
					
	
			// validação do sistema
			if($dataInicial_v =="" AND $dataFinal_v =="" AND $dataInicial_p =="" AND $dataFinal_p =="" AND $dataInicial_c =="" AND $dataFinal_c ==""){
				
				$msg = array('msg' => $periodo,'tipo' => 'alerta',''=>'erro');
				return $msg;		
			
			}elseif($dataInicial_v !="" AND $dataFinal_v =="" ){
						
				$msg = array('msg' => $inicial,'tipo' => 'alerta','dataFinal_v'=>'erro');
				return $msg;
			
			}elseif($dataInicial_v =="" AND $dataFinal_v !="" ){
						
				$msg = array('msg' => $inicial,'tipo' => 'alerta','dataInicial_v'=>'erro');
				return $msg;
			
			}elseif($this->formatData($dataInicial_v) > $this->formatData($dataFinal_v)){
				
				$msg = array('msg' => $menor,'tipo' => 'alerta','data_v_maior'=>'erro');
				return $msg;
			
			}elseif($dataInicial_p !="" AND $dataFinal_p ==""){
				
				$msg = array('msg' => $inicial,'tipo' => 'alerta','dataFinal_p'=>'erro');
				return $msg;
			
			}elseif($dataInicial_p =="" AND $dataFinal_p !=""){
				
				$msg = array('msg' => $inicial,'tipo' => 'alerta','dataInicial_p'=>'erro');
				return $msg;
			
			}elseif($this->formatData($dataInicial_p) > $this->formatData($dataFinal_p)){
				
				$msg = array('msg' => $menor,'tipo' => 'alerta','data_p_maior'=>'erro');
				return $msg;
			
			}elseif($dataInicial_c !="" AND $dataFinal_c ==""){
				
				$msg = array('msg' => $inicial,'tipo' => 'alerta','dataFinal_c'=>'erro');
				return $msg;
			
			}elseif($dataInicial_c =="" AND $dataFinal_c !=""){
				
				$msg = array('msg' => $inicial,'tipo' => 'alerta','dataInicial_c'=>'erro');
				return $msg;
			
			}elseif($this->formatData($dataInicial_c) > $this->formatData($dataFinal_c)){
				
				$msg = array('msg' =>$menor,'tipo' => 'alerta','data_c_maior'=>'erro');
				return $msg;
			
			}elseif($dataInicial_ret !="" AND $dataFinal_ret ==""){
			
				$msg = array('msg' => $inicial,'tipo' => 'alerta','dataFinal_ret'=>'erro');
				return $msg;
					
			}elseif($dataInicial_ret =="" AND $dataFinal_ret !=""){
			
				$msg = array('msg' => $inicial,'tipo' => 'alerta','dataInicial_ret'=>'erro');
				return $msg;
					
			}elseif($this->formatData($dataInicial_ret) > $this->formatData($dataFinal_ret)){
			
				$msg = array('msg' =>$menor,'tipo' => 'alerta','data_ret_maior'=>'erro');
				return $msg;
			}
			
			
			if($dataInicial_v !=""){
				if(!$this->validaData($dataInicial_v)){
					
					$msg = array('msg' =>$datainvalida,'tipo' => 'alerta','dataInicial_v'=>'erro');
					return $msg;
				}
			}
			
			
			if($dataFinal_v !=""){
				if(!$this->validaData($dataFinal_v)){
					$msg = array('msg' => $datainvalida,'tipo' => 'alerta','dataFinal_v'=>'erro');
					return $msg;
				}
			}
			
			
			if($dataInicial_p !=""){
				if(!$this->validaData($dataInicial_p)){					
					$msg = array('msg' => $datainvalida,'tipo' => 'alerta','dataInicial_p'=>'erro');
					return $msg;
				}
			}
			
			
			if($dataFinal_p !=""){
				if(!$this->validaData($dataFinal_p)){
					
					$msg = array('msg' => $datainvalida,'tipo' => 'alerta','dataFinal_p'=>'erro');
					return $msg;
				}
			}
			
			if($dataInicial_c !=""){
				if(!$this->validaData($dataInicial_c)){					
					$msg = array('msg' => $datainvalida,'tipo' => 'alerta','dataInicial_c'=>'erro');
					return $msg;
				}
			}
			
			if($dataFinal_c !=""){
				if(!$this->validaData($dataFinal_c)){				
					$msg = array('msg' => $datainvalida,'tipo' => 'alerta','dataFinal_c'=>'erro');
					return $msg;
				}
			}
			
			
			if($dataInicial_ret !=""){
				if(!$this->validaData($dataInicial_ret)){
					$msg = array('msg' => $datainvalida,'tipo' => 'alerta','dataInicial_ret'=>'erro');
					return $msg;
				}
			}
				
			if($dataFinal_ret !=""){
				if(!$this->validaData($dataFinal_ret)){
					$msg = array('msg' => $datainvalida,'tipo' => 'alerta','dataFinal_ret'=>'erro');
					return $msg;
				}
			}
			
			
			if ($dataInicial_v != '' and $dataFinal_v != '') {
				
				if ($this->calc_idade($dataInicial_v, $dataFinal_v) == FALSE) {
					
					$msg = array('msg' => $datasuperior,'tipo' => 'alerta','dataInicial_v'=>'erro','dataFinal_v'=>'erro');
					return $msg;
				}
			
			}	
			
			
			if ($dataInicial_p != '' and $dataFinal_p != '') {
				
				if ($this->calc_idade($dataInicial_p, $dataFinal_p) == FALSE) {
					
					$msg = array('msg' => $datasuperior,'tipo' => 'alerta','dataInicial_p'=>'erro','dataFinal_p'=>'erro');
					return $msg;
				}
			
			}	
			
			if ($dataInicial_c != '' and $dataFinal_c != '') {
				
				if ($this->calc_idade($dataInicial_c, $dataFinal_c) == FALSE) {
					
					$msg = array('msg' => $datasuperior,'tipo' => 'alerta','dataInicial_c'=>'erro','dataFinal_c'=>'erro');
					return $msg;
				}
			
			}	
			
			if ($dataInicial_ret != '' and $dataFinal_ret != '') {
					
				if ($this->calc_idade($dataInicial_ret, $dataFinal_ret) == FALSE) {
			
					$msg = array('msg' => $datasuperior,'tipo' => 'alerta','dataInicial_ret'=>'erro','dataFinal_ret'=>'erro');
					return $msg;
				}
					
			}
			
				$arrayDados = array(	
							'clinome' =>$clinome ,
							'clitipo' => $clitipo,
							'dataInicial_v'=>$this->formatData($dataInicial_v),
							'dataFinal_v'=> $this->formatData($dataFinal_v), 
							'dataInicial_p'=> $this->formatData($dataInicial_p),
							'dataFinal_p'=> $this->formatData($dataFinal_p),
							'dataInicial_c'=> $this->formatData($dataInicial_c),
							'dataFinal_c'=> $this->formatData($dataFinal_c),
							'banco'=> $banco,
							'status'=> $status
								);
				
				$result = $this->dao->GetClassesPesquisarDados($arrayDados);	
				
				
				if($dataInicial_ret != "" && $dataFinal_ret != ""){
					
					$arrayDadosRelDa = array(
							'dataInicial_ret'=> $this->formatData($dataInicial_ret),
							'dataFinal_ret'=> $this->formatData($dataFinal_ret),
							'formaCobranca'=> $banco,
					);
					
					$resultRelRetornoDa = $this->dao->getDadosHistoricoDa($arrayDadosRelDa);
			
				}
				
				
				if(!$result && !$resultRelRetornoDa) {								
					$msg = array('msg' => $nenhum,'tipo' => 'alerta');
				   return $msg;
				}else{
					$msg = array('result' => $result, 'resultRelRetornoDa' => $resultRelRetornoDa, 'processo' => 'TRUE');
				    return $msg;		
				}
		
		
	}
	

	private function formatData($data){
		
		if($data !=''){
	    	$data_inverter = explode("/",$data);
			
		    $dataNova = $data_inverter[2].'-'. $data_inverter[1].'-'. $data_inverter[0];
			
		    return $dataNova;
		 }else{
		 	
		 	return '';
		 }

	}
	
	private function formatDataVazia($data){
		
		if($data !=''){
	    	$data_inverter = explode("/",$data);
			
		    $dataNova = $data_inverter[2].''. $data_inverter[1].''. $data_inverter[0];
		
			if(!is_numeric($dataNova)){				
				return ' ';				
			}else{				
				return $data;
			}
		   
		 }else{		 	
		 	return '';
		 }

	}
	private function formatDataBD($data){
		
		if($data !=''){
	    	$data_inverter = explode("-",$data);
			
		    $dataNova = $data_inverter[0].'/'. $data_inverter[1].'/'. $data_inverter[2];
			
		    return $dataNova;
		 }else{
		 	
		 	return '';
		 }

	}
	
	/**
	  * Valida data se e valida;
	  */
	 public function validaData($data) {
	 	
		try{
			$a = substr($data, 6, 4);
			$m = substr($data, 3, 2);
			$d = substr($data, 0, 2);
			
			//var_dump($data.'-'.$m.'+'.$d.'+'.$a);die;
				
			$valor = checkdate($m,$d,$a);
			
	 		if($valor){
	 			 return TRUE;
			}else{
				return FALSE;
			}			 
			
			
		}catch (Exception $e) {						
			 
            $mensagem = $e->getMessage();   
			        			
			return FALSE;
		 } 
		
	 }
	 public function calc_idade($dataInicial, $dataFinal) {


				$dataInicial = explode('/',$dataInicial);
				$dataFinal = explode('/',$dataFinal);
				
				//Data do inicial
				$diainicial = $dataInicial[0];
				$mesinicial = $dataInicial[1];
				$anoinicial = $dataInicial[2];
				
				//Data final
				$diafinal = $dataFinal[0];
				$mesfinal = $dataFinal[1];
				$anofinal = $dataFinal[2];
								
				$timestamp1 = mktime(0,0,0,$mesinicial,$diainicial,$anoinicial); 
				$timestamp2 = mktime(0,0,0,$mesfinal,$diafinal,$anofinal); 
				
				//diminuo a uma data a outra 
				$segundos_diferenca = $timestamp2 - $timestamp1; 
				//echo $segundos_diferenca; 
				
				//converto segundos em dias 
				$dias_diferenca = $segundos_diferenca / (60 * 60 * 24); 
				
				//obtenho o valor absoluto dos dias (tiro o possível sinal negativo) 
				$dias_diferenca = abs($dias_diferenca); 
				
				//tiro os decimais aos dias de diferenca 
				$dias_diferenca = floor($dias_diferenca); 
							
				
				if ($dias_diferenca <= 365) {					
					return TRUE;
					
				} else {
					
					return false;
				}	
				
		}
	
				}					            
