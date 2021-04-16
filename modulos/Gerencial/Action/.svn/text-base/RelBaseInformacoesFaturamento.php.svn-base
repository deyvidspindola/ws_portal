<?php
set_time_limit(0);
$ger_dir = dirname(__FILE__);

require(_SITEDIR_ . 'lib/Components/CsvWriter.php');
require($ger_dir . '/../DAO/RelBaseInformacoesFaturamentoDAO.php');
class RelBaseInformacoesFaturamento {
	
	private $dao;
	
	function __construct() {
		global $conn;
		$this->dao = new RelBaseInformacoesFaturamentoDAO($conn);
	}
	
	
	public function index() {
		include (_MODULEDIR_ . 'Gerencial/View/rel_base_informacoes_faturamento/index.php');
	}
	

	public function retornaNomeRelatorios(){
		return $relatorios = $this->dao->retornaRelatorios();
	}
	
	public function verificaAcao(){
		
		if(count($_POST)) {

			date_default_timezone_set('America/Sao_Paulo');
		
			$inicio = $_POST['rel_dt_ini'];
			$fim = $_POST['rel_dt_fim'];
			 
			$dataInicio = explode("/",$inicio);
			$dataFim = explode("/",$fim);
			
			$data_inicial = $dataInicio[2]."-".$dataInicio[1]."-".$dataInicio[0];
			$data_final = $dataFim[2]."-".$dataFim[1]."-".$dataFim[0];

			$dif = strtotime($data_inicial) - strtotime($data_final);
			
			$dias = floor($dif/(60*60*24) -1);
			
			$dias = explode("-",$dias);
			
			if($dias[1] <= 31) {
				if($_POST['tipo_relatorio'] == 55) {
					$resposta = $this->prepararBaseDetalhadaNF($_POST);
					if($resposta['codigo'] == 1) {
						$array = array ('tipo' => "erro", 'msg' => $resposta['msg']);
						echo json_encode($array);
						die();
					}else {
						$array = array ('tipo' => "sucesso", 'msg' => $resposta['msg']);
						echo json_encode($array);
						die();
					}
					
					
				}
			}else{
				$array = array ('tipo' => "erro", 'msg' => utf8_encode("A data não pode ser maior que 31 dias"));
				echo json_encode($array);
				die();
			}
		
		}else{
			$array = array ('tipo' => "erro", 'msg' => utf8_encode("Erro ao iniciar base detalhada nota fiscal"));
			echo json_encode($array);
			die();
		}
	}
	
	/**
	 * Metodo que chama o cron para base detalhada nota fiscal
	 * @throws String
	 */
	public function prepararBaseDetalhadaNF($post){

		$res = $this->verificarProcesso(false);
		
			if($res['codigo'] == 0) {
				
				try {
				$params .= $post['tipo_relatorio']."|";
				$params .= $post['rel_dt_ini']."|";
				$params .= $post['rel_dt_fim']."|";
				$cd_usuario = $_SESSION['usuario']['oid'];
				
				$this->dao->preparaRelatorioBaseInformacao($cd_usuario,26,$params);
				
				if(!is_dir(_SITEDIR_."faturamento")) {
					if(!mkdir(_SITEDIR_."faturamento" , 0777)) {
						throw new Exception('Falha ao criar arquivo de log.');
						$msg =  utf8_encode("Falha ao criar arquivo de log.");
						$retorno = array(
								"codigo" => 1,
								"msg" => $msg,
								"retorno" => array()
						);
					}
				}
				
				chmod(_SITEDIR_."faturamento",0777);
				
				if (!$handle = fopen(_SITEDIR_."faturamento/log_relatorio_base_de_informacoes", "w")) {
					throw new Exception('Falha ao criar arquivo de log.');
					$msg =  utf8_encode("Falha ao criar arquivo de log.");
					$retorno = array(
							"codigo" => 1,
							"msg" => $msg,
							"retorno" => array()
					);
				}
				
				fputs($handle, "Geração de relatórios base da informação\r\n");
				fclose($handle);
				
				chmod(_SITEDIR_."faturamento/log_relatorio_base_de_informacoes",0777);
				
				passthru("/usr/bin/php " . _SITEDIR_ . "CronProcess/geracao_rel_base_detalhada_nf.php >> " . _SITEDIR_ . "faturamento/log_relatorio_base_de_informacoes 2>&1 &");

				
				$res = $this->verificarProcesso(false);
				
				$msg =  $res['msg'];
				$msg =  utf8_encode($msg);
				$retorno = array(
						"codigo" => 2,
						"msg" => $msg,
						"retorno" => array()
				);
				
				}catch (Exception $e) { 
					$this->dao->finalizarProcesso($e->getMessage(),26);
					$msg = utf8_encode($e->getMessage());
					$retorno = array(
							"codigo" => 1,
							"msg" => $msg,
							"retorno" => array()
					);
				}
				
			}else {
				$msg = utf8_encode($res['msg']);
				$retorno = array(
						"codigo" => 1,
						"msg" => $msg,
						"retorno" => array()
				);
			 
		  }
		  
		  return $retorno;
		
	}
	
	public function geraRelatorioBaseDetalhadaNF(){
		
		$retorno = false;
		
		try {
		 $caminho = $this->RetornaCaminhoServidor();
		
		$nomeArquivo = 'Base_detalhada_notas_ficais.csv';
		
		if(is_file($caminho.$nomeArquivo)) {
			unlink($caminho . $nomeArquivo);
		}
		
		if(file_exists($caminho)) {
			
			//cria o arquivo CSV
			$csvWriter = new CsvWriter($caminho . $nomeArquivo, ';', '', true);
			
			//Seta os cabeçalhos
			$cabeçalho = array(
				"nota",
				"serie",
				"prazo",
				"natureza",
				"dt_emissao",
				"codigo_do_cliente",
				"cliente",
				"tipo_cliente",
				"documento",
				"classe",
				"grupo",
				"tipo_contrato",
				"tipo",
				"contrato",
				"inicio_vigencia",
				"grupo_item_faturado",
				"item_faturado",
                "parcela_faturada",
				"valor_item",
				"desconto" ,
				"valor_liquido" ,
				"dt_cancelamento_nf" ,
				"motivo_cancelamento" ,
				"numero_parcelas" ,
				"cancelada_mes" ,
				"contrato_prazo",
				"Origem",
				"E-mail NF-e"
			);
			
			//Adiciona o Cabeçalho
			$csvWriter->addLine($cabeçalho);
			
			$res = pg_fetch_assoc($this->dao->recuperarParametros(false));
			
			
			$param = explode("|", $res['earparametros']);
			
			$datas = explode("/", $param[1]);
			
			$dataFormatadaIni = $datas[2]."-".$datas[1]."-".$datas[0];
			
			$datasFim = explode("/", $param[2]);
				
			$dataFormatadaFim = $datasFim[2]."-".$datasFim[1]."-".$datasFim[0];
			
			$res = $this->dao->retornoBaseDetalhadaNF($dataFormatadaIni,$dataFormatadaFim);
	
			while($row = pg_fetch_object($res)) {
				
				$nota = trim($row->nota) != '' ? trim($row->nota) : '';
				$serie = trim($row->serie) != '' ? trim($row->serie) : '';
				$prazo = trim($row->prazo) != '' ? trim($row->prazo) : '';
				$natureza = trim($row->natureza) != '' ? trim($row->natureza) : '';
				$dt_emissao = trim($row->dt_emissao) != '' ? trim($row->dt_emissao) : '';
				$codigo_do_cliente = trim($row->codigo_do_cliente) != '' ? trim($row->codigo_do_cliente) : '';
				$cliente  = trim($row->cliente) != '' ? trim($row->cliente) : '';
				$tipo_cliente  = trim($row->tipo_cliente) != '' ? trim($row->tipo_cliente) : '';
				$documento = trim($row->documento) != '' ? trim($row->documento) : '';
				$classe = trim($row->classe) != '' ? trim($row->classe) : '';
				$grupo = trim($row->grupo) != '' ? trim($row->grupo) : '';
				$tipocontrato = trim($row->tipocontrato) != '' ? trim($row->tipocontrato) : '';
				$tipo = trim($row->tipo) != '' ? trim($row->tipo) : '';
				$contrato = trim($row->contrato) != '' ? trim($row->contrato) : '';
				$inicio_vigencia = trim($row->inicio_vigencia) != '' ? trim($row->inicio_vigencia) : '';
				$grupo_item_faturado = trim($row->grupo_item_faturado) != '' ? trim($row->grupo_item_faturado) : '';
				$item_faturado = trim($row->item_faturado) != '' ? trim($row->item_faturado) : '';
                $total_parcelas = trim($row->total_parcelas) != '' ? trim($row->total_parcelas) : '';
                $exibe_parcela = trim($row->exibe_parcela) != '' ? trim($row->exibe_parcela) : '';
                $parcela = sprintf("%02s de %02s", (int) ( trim($row->parcela) != '' ? trim($row->parcela) : '' ), (int) $total_parcelas);

                if($exibe_parcela == 'f') {
            		$parcela = "";
                }

				$valor_item = trim($row->valor_item) != '' ? trim($row->valor_item) : '';
				$desconto = trim($row->desconto) != '' ? trim($row->desconto) : '';
				$valor_liquido = trim($row->valor_liquido) != '' ? trim($row->valor_liquido) : '';
				$dt_cancelamento_nf = trim($row->dt_cancelamento_nf) != '' ? trim($row->dt_cancelamento_nf) : '';
				$motivo_cancelamento = trim($row->motivo_cancelamento) != '' ? trim($row->motivo_cancelamento) : '';
				$numero_parcelas = trim($row->numero_parcelas) != '' ? trim($row->numero_parcelas) : '';
				$cancelada_mes = trim($row->cancelada_mes) != '' ? trim($row->cancelada_mes) : '';
				$contrato_prazo = trim($row->contrato_prazo) != '' ? trim($row->contrato_prazo) : '';
				$origem = trim($row->origem) != '' ? trim($row->origem) : '';
				$email_nf = trim($row->email_nf) != '' ? trim($row->email_nf) : '';

				// Corpo do CSV
				$csvWriter->addLine(
						array(
								$nota,
								$serie,
								$prazo,
								$natureza,
								$dt_emissao,
								$codigo_do_cliente,
								$cliente,
								$tipo_cliente,
								$documento,
								$classe,
								$grupo,
								$tipocontrato,
								$tipo,
								$contrato,
								$inicio_vigencia,
								$grupo_item_faturado,
								$item_faturado,
								$parcela,
								$valor_item,
								$desconto,
								$valor_liquido,
								$dt_cancelamento_nf,
								$motivo_cancelamento,
								$numero_parcelas,
								$cancelada_mes,
								$contrato_prazo,
								$origem,
								$email_nf
						)
				);
			}
			
			
			
			$arquivo_previa = "";
			if (is_file($caminho . $nomeArquivo)) {
				$arquivo_previa = $caminho . $nomeArquivo;
			}
				
			
			if (empty($arquivo_previa) || $arquivo_previa == "") {
				$this->dao->finalizarProcesso("Falha ao gerar relatórios da base detalhada nota fiscal.",26);
				$retorno = false;
			} else {
				$this->dao->finalizarProcesso("Geração de relatórios da base detalhada nota fiscal foi gerada com sucesso .",26);
				$this->view->msg = "Prévia gerada com sucesso.";
				$retorno = true;
			}
		}
		} catch (ExceptionValidation $e) {
    			$this->dao->finalizarProcesso($e->getMessage(),26);
    			$this->view->msg = $e->getMessage();
    			$retorno = false;
    			
    		} catch (Exception $e) {
    			$msg = "Falha ao gerar relatório base detalhada nota fiscal.";
    			$this->dao->finalizarProcesso($e->getMessage(),26);
    			$retorno = false;
    		}
			return $retorno;

	}

	/**
	 * Método verificarProcesso
	 * Verifica se já tem alguma processo rodando de base de informação
	 * @param bolean $finalizado
	 */
	public function verificarProcesso($finalizado){
		
		try {
			// Verifica concorrÃªncia entre processos
			$res = $this->dao->recuperarParametros($finalizado);

			if(pg_num_rows($res) > 0){
				
				$param = pg_fetch_assoc($res);
				$msg = "Geração iniciada por => ".$param['nm_usuario']." ás ".$param['data_inicio'];
				
				return array(
					"codigo" => 2,
					"msg" => $msg,
					"retorno" => $param
				);
			}else {
    			
    			return array(
    					"codigo"	=> 0,
    					"msg"		=>	'',
    					"retorno"	=>	$param
    			);
    		}
		}catch(Exception $e){
			return array(
					"codigo" => 1,
					"msg" => "Falha ao verificar concorrência. Tente novamente.",
					"retorno" => array()
			);
		}
	}
	
	
	/**
	 * Retorna os parametros do email data inicio e fim e o descricao do status
	 * da tabela execucao_arquivo_grafica  , essa função vai ser chamada pelo cron.
	 */
	public function retornoParametros(){
		$paramentros = $this->dao->recuperarParametros(true);
	
		while ($tipo = pg_fetch_object($paramentros)) {
			$email = $tipo->usuemail;
			$dataInicio = $tipo->data_inicio;
			$dataTermino = $tipo->data_termino;
			$status = $tipo->eardesc_status;
			$param  = $tipo->earparametros;
		}
	
		return array(
				"email"	=> $email,
				"dataInicio"=>$dataInicio,
				"dataTermino"=> $dataTermino,
				"status" => $status
		);
	
	}
	
	
	/**
	 * Lista os arquivos e armazena no array para retorna para view
	 * @return array
	 */
	public function listaArquivos(){
		$dir = $this->RetornaCaminhoServidor();
		
		if($handles = opendir($dir)){
			
			while(false !== ($entry = readdir($handles))) {
				if(empty($entry) || $entry == '..' || $entry == '.')continue;
				
				if($entry != "svn") {
					$arquivos[]['nome'] = $entry;
				}
			}
			closedir($handles);
		}
		
		if(count($arquivos) > 0 || !empty($arquivos)) {
			rsort($arquivos);
		}
		
		return $arquivos;
	}
	
	/**
	 * Função para ver o tamanho do arquivo
	 * @return String
	 */
	public function tamanhoArquivo($arquivo) {
		$tamanhoarquivo = filesize($arquivo);
		
		/* Medidas */
		$medidas = array('KB', 'MB', 'GB', 'TB');
		
		/* Se for menor que 1KB arredonda para 1KB */
		if($tamanhoarquivo < 999) {
			$tamanhoarquivo = 1000;
		}
		
		for ($i = 0; $tamanhoarquivo > 999; $i++){
			$tamanhoarquivo /= 1024;
		}
		
		return round($tamanhoarquivo) ." ".$medidas[$i - 1];
	}

	/**
	 *  Metodo para retornar caminho da pasta no servidor
	 *  @return String
	 */
	public function RetornaCaminhoServidor(){
		$res = $this->dao->getCaminhoServidor();
		$PATH =  _SITEDIR_ .$res;
		return $PATH;
	}
	
	/**
	 *  Metodo para excluir o  arquivo no servidor
	 *
	 */
	public function excluir(){
	
		$caminho = $this->RetornaCaminhoServidor();
		$nome_arquivo = $_POST['arquivo'];
		// Apaga os arquivos .xml e deixa apenas o ZIP na pasta
		if (file_exists($caminho . $pathSig)) {
			if (is_file($caminho . $nome_arquivo)) {
				if(unlink($caminho . $nome_arquivo)){
					echo "O Arquivo $nome_arquivo foi excluido com sucesso";
				}
				}else {
				echo "Acesso negado - Não pode excluir o arquivo!";
    		}
				}else {
				echo "O arquivo $nome_arquivo não existe";
				}
				}
	
	
}