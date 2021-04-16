<?php
/**
 * Classe que lida com a reativação da cobrança de locação.
 * @author Vinicius Senna <[<email address>]>
 */
require_once _MODULEDIR_ ."Principal/DAO/PrnContratoReativacaoDAO.php";

class PrnContratoReativacao{
	
	private $dao;		// Instancia DAO
	public $view;		// Objeto view
	private $idUsuario;	// Id do usuario que esta realizando a reativação
	//private $diretorioArquivos = 'C:\var\www\arquivos_intranet\arquivos_reativacao_contrato';
	private $diretorioArquivos = '/var/www/arquivos_intranet/arquivos_reativacao_contrato';
	private $arrayContratos = array();

	public function __construct($conn,$usuario) {
		$this->dao = new PrnContratoReativacaoDAO($conn);
		$this->view = new stdClass();
		$this->idUsuario = (int) $usuario;
	}

	public function getUsuario() {
		return $this->idUsuario;
	}

	public function getDiretorioArquivos() {
		return $this->diretorioArquivos;
	}

	/**
	 * Renderiza view principal da reativação 
	 * @return [type] [description]
	 */
	public function index() {
		require_once _MODULEDIR_ . "Principal/View/prn_contrato_reativacao/index.php";
	}

	/**
	 * Retorna formulario padrao ou de envio de arquivo
	 * @param  [type] $formularioArquivo [description]
	 * @return [type]                 [description]
	 */
	public function retornaFormulario($formularioArquivo = NULL) {

		if(isset($formularioArquivo) && $formularioArquivo == 'true') {
			echo file_get_contents(_MODULEDIR_ . "Principal/View/prn_contrato_reativacao/formulario_arquivo.php");
		} else {
			header('Content-Type: text/html; charset=ISO-8859-1');
			require_once _MODULEDIR_ . "Principal/View/prn_contrato_reativacao/formulario_padrao.php";
		}
		
		exit;
	}


	/**
	 * Realiza reativação através do formulário padrão
	 * @return [type] [description]
	 */
	public function reativaLocacao($dadosEnvio) {

		$retorno  = new stdClass();
		$cpvoid = 0;
		$manterValor = (isset($dadosEnvio['manter_valor']) && $dadosEnvio['manter_valor'] == 'true') ? true : false;
        $parcelasAcessorios = (isset($dadosEnvio['parcelas_acessorios']) && $dadosEnvio['parcelas_acessorios'] == 'true') ? true : false;
		$valorParcela = str_replace(',', '.', $dadosEnvio['valor_servico']);
		$justificativa = isset($dadosEnvio['justificativa']) ? utf8_decode(trim(addslashes($dadosEnvio['justificativa']))) : '';

		// Busca da tabela cond_pgto_venda onde o numero de parcelas é 999 x
		$condPag = $this->dao->buscaCondicaoPagamento(array(
            "cpvexclusao" => array("value" => 'NULL', "condition" => "IS"),
            "cpvdescricao" => array("value" => '999 x', "condition" => "=")
        ));

		// Se encontrar a condição de pagamento de  999x
        if(isset($condPag->result) && pg_num_rows($condPag->result) > 0) {

        	try {
	        	$this->dao->begin();

	        	$dadosCondicaoPagamento = pg_fetch_object($condPag->result,0);
	        	$cpvoid = $dadosCondicaoPagamento->cpvoid;

	        	// Percorre os contratos
				foreach ($dadosEnvio['lista_contratos'] as $contrato) {

					// Valida se os status do contrato é ativo e se a modalidade é de locação
					$dadosContrato = $this->dao->buscaDadosContrato(array(
						"connumero" => array("value" => (int) $contrato, "condition" => "="),
						"conmodalidade" => array("value" => 'L', "condition" => "="),
						"concsioid" => array("value" => 1, "condition" => "=")
					));

					if(isset($dadosContrato->result) && pg_num_rows($dadosContrato->result) > 0) {

						$dadosUpdate = NULL;

						// Mantem o valor atual, só altera o numero de parcelas
						if($manterValor == true) {
							$dadosUpdate = array(
								"cpagcpvoid" => (int) $cpvoid,
                                "cpagsituacao" => 'L',
								"cpagusuoid" => $this->getUsuario()
							);
						} else {
							// Altera o valor atual e o numero de parcelas
							$dadosUpdate = array(
								"cpagcpvoid" => (int) $cpvoid,
								"cpagvl_servico" => $valorParcela,
								"cpagusuoid" => $this->getUsuario()
							);
						}

						// Atualiza tabela com os dados novos do pagamento
						$rsAtualizacao = $this->dao->atualizaContratoPagamento($dadosUpdate,array(
							"cpagconoid" => array("value" => (int) $contrato, "condition" => "=") 
						));

						if(isset($rsAtualizacao->erro)) {
							throw new Exception($rsAtualizacao->erro);
						}


                        // Verifica se é para ativar a parcelas dos acessórios
                        if($parcelasAcessorios == true) {

                            $dadosUpdateAcessorios = array(
                                "conscpvoid" => (int) $cpvoid
                            );

                            //busca obrigações financeiras do grupo Locações Acessórios
                            $obrigacoes = $this->dao->obrigacaoFinanceiraLocacaoAcessorios();

                            // Atualiza tabela com os dados novos do pagamento
                            $rsAtualizacao = $this->dao->atualizaContratoServico($dadosUpdateAcessorios,array(
                                "consconoid"    => array("value" => (int) $contrato, "condition" => "="),
                                "conssituacao"  => array("value" => "L", "condition" => "="), //Servico de locação
                                "consiexclusao" => array("value" => "NULL", "condition" => "IS"),
                                "consobroid"    => array("value" => $obrigacoes->ids, "condition" => "IN") // Obrigação Financeira for do grupo 'Locação Acessórios'
                            ));

                            if(isset($rsAtualizacao->erro)) {
                                throw new Exception($rsAtualizacao->erro);
                            }

                        }

						// Atualiza histórico do contrato
						$rsHistoricoTermo = $this->dao->insereHistoricoTermo((int) $contrato,$this->getUsuario(),$justificativa);

						if(isset($rsHistoricoTermo->erro)) {
							throw new Exception($retorno->erro);
						}
					} else {
						throw new Exception('O contrato '. (int) $contrato . ' não está com status ativo e/ou não está na modalidade de locação.');
					}
				}

				$this->dao->commit();

			} catch(Exception $e) {
				$retorno->erro = utf8_encode($e->getMessage());
				$this->dao->rollback();
			}

		} else {
			// retornar quando não acha a parcela 999 x
			$retorno->erro = utf8_encode('Não foi possível encontrar a condição de pagamento');
		}

		return $retorno;
	}

	/**
	 * Realiza reativação a partir do arquivo CSV
	 * @param  [type] $arquivo [description]
	 * @return [type]          [description]
	 */
	public function reativaLocacaoArquivo($arquivo) {

		$retorno  = new stdClass();
		$cpvoid = 0;

		if($this->validaArquivo($arquivo) == true) {

			// Busca da tabela cond_pgto_venda onde o numero de parcelas é 999 x
			$condPag = $this->dao->buscaCondicaoPagamento(array(
	            "cpvexclusao" => array("value" => 'NULL', "condition" => "IS"),
	            "cpvdescricao" => array("value" => '999 x', "condition" => "=")
	        ));

	        if(isset($condPag->result) && pg_num_rows($condPag->result) > 0) {
	        	$dadosCondicaoPagamento = pg_fetch_object($condPag->result,0);
	        	$cpvoid = $dadosCondicaoPagamento->cpvoid;

	        	try {
		        	$this->dao->begin();
		        	foreach ($this->arrayContratos as $contrato) {

		        		$manterValor = (isset($contrato[1]) && $contrato[1] == 'S') ? true : false;

		        		$valorParcela = str_replace(',', '.', $contrato[2]);
		        		$dadosUpdate = NULL;

						// Mantem o valor atual, só altera o numero de parcelas
						if($manterValor == true) {
							$dadosUpdate = array(
								"cpagcpvoid" => (int) $cpvoid,
                                "cpagsituacao" => 'L',
								"cpagusuoid" => $this->getUsuario()
							);
						} else {
							// Altera o valor atual e o numero de parcelas
							$dadosUpdate = array(
								"cpagcpvoid" => (int) $cpvoid,
								"cpagvl_servico" => $valorParcela,
								"cpagusuoid" => $this->getUsuario()
							);
						}

                        // Atualiza tabela com os dados novos do pagamento
                        $rsAtualizacao = $this->dao->atualizaContratoPagamento($dadosUpdate,array(
                            "cpagconoid" => array("value" => (int) $contrato[0], "condition" => "=") 
                        ));


                        //valida reativação de parcelas de acessórios
                        $parcelasAcessorios = (isset($contrato[4]) && $contrato[4] == 'S') ? true : false;
                        if($parcelasAcessorios == true) {

                            $dadosUpdateAcessorios = array(
                                "conscpvoid" => (int) $cpvoid
                            );

                            //busca obrigações financeiras do grupo Locações Acessórios
                            $obrigacoes = $this->dao->obrigacaoFinanceiraLocacaoAcessorios();

                            // Atualiza tabela com os dados novos do pagamento
                            $rsAtualizacao = $this->dao->atualizaContratoServico($dadosUpdateAcessorios,array(
                                "consconoid"    => array("value" => (int) $contrato[0], "condition" => "="),
                                "conssituacao"  => array("value" => "L", "condition" => "="), //Servico de locação
                                "consiexclusao" => array("value" => "NULL", "condition" => "IS"),
                                "consobroid"    => array("value" => $obrigacoes->ids, "condition" => "IN") // Obrigação Financeira for do grupo 'Locação Acessórios'
                            ));
                        }

						// Atualiza histórico do contrato
						$rsHistoricoTermo = $this->dao->insereHistoricoTermo((int) $contrato[0],$this->getUsuario(),$contrato[3]);
		        	}

		        	$this->dao->commit();
	        	} catch(Exception $e) {
					//$retorno->erro = utf8_encode($e->getMessage());
					$this->dao->rollback();
				}
			}

			echo json_encode(array('sucesso' => utf8_encode("Arquivo de Reativação em Lote processado com sucesso.")));
		} 

		return $retorno;
	}

	/**
	 * Realiza validação do arquivo CSV
	 * @param  [type] $arquivo [description]
	 * @return [type]          [description]
	 */
	public function validaArquivo($arquivo) {

		$arquivoValido = true;
		$arquivoErro = 'erros_importacao_contratos'.date('dmYhis').'.txt';
		$stringErros = '';
		$magicFile = getenv('MAGIC');
		$numeroLinha = 1;
		$mimeTypesValidos = array(
			'text/csv',
			'text/plain',
			'application/csv',
			'text/comma-separated-values',
			'application/excel',
			'application/vnd.ms-excel',            
			'application/vnd.msexcel',
			'text/anytext',
			//'application/octet-stream' => 1,
			'application/txt'
		);

		// Grava arquivo no diretorio especificado
		$info = pathinfo($arquivo['arquivo_reativacao']['name']);
		$ext = $info['extension'];
		$nomeArquivo = "arquivo".date('dmYhis').'.'.$ext; 
		$caminhoArquivo = $this->getDiretorioArquivos().'/'.$nomeArquivo;
		$copy = copy($arquivo['arquivo_reativacao']['tmp_name'], $caminhoArquivo);

		if($fp = fopen($caminhoArquivo,'r')) {

			//Validando mimetype do arquivo
		 	if (function_exists('finfo_open')) {
		 		if (getenv('MAGIC') === FALSE && substr(PHP_OS, 0, 3) == 'WIN') {
                    $magicFile = realpath("C:\Program Files\Zend\Apache2\conf\magic");
	 			}
		 		
				$finfo = finfo_open(FILEINFO_MIME, $magicFile);
				$infoArquivo = finfo_file($finfo,$caminhoArquivo);
				$mimeType = explode(';', $infoArquivo);
				
				// Arquivo não está dentro dos mimeTypes validos
				if(isset($mimeType[0])) {
					if(!in_array($mimeType[0], $mimeTypesValidos)) {
						$stringErros .= "Arquivo enviado é inválido.". PHP_EOL;
						$arquivoValido = false;
					}
				}
			}

			if($arquivoValido == true) {

				while (($data = fgetcsv($fp, 1000, ";")) !== FALSE) {
					$retornoValidacaoLinha = $this->validaDadosArquivo($data,$numeroLinha);

					if(isset($retornoValidacaoLinha->erro)) {
						$stringErros .= $retornoValidacaoLinha->erro;
						$arquivoValido = false;
					}

					if($arquivoValido == true) {
						array_push($this->arrayContratos, $data);
					}

					$numeroLinha++;
				}
			}
			
			fclose($fp);
		} else {
			$arquivoValido = false;
			$stringErros .= "Falha ao abrir o arquivo enviado pelo usuário.". PHP_EOL;
		}

		// Grava conteúdo do arquivo de erros
		if($arquivoValido == false) {
			if($fp = fopen($this->getDiretorioArquivos() .'/'. $arquivoErro, 'w')) {
				fwrite($fp, $stringErros);
				fclose($fp);
			}

			// Envia resposta com layout do arquivo com os erros
			$layoutErro = "<div class=\"conteudo centro\">
			    <a href=\"download.php?arquivo=". $this->getDiretorioArquivos() .'/'. $arquivoErro."\" target=\"_blank\">
			        <img src=\"images/icones/t3/caixa2.jpg\"><br>" . basename($arquivoErro) . "
			    </a>
			</div>";

			echo json_encode(array('erro' => $layoutErro));
			exit;
		}

		return $arquivoValido;
	}

	/**
	 * [Valida os dados de uma linha do arquivo]
	 * @param  [type] $arrayDados [dados da linha do arquivo]
	 * @return [type]             [description]
	 */
	public function validaDadosArquivo($arrayDados,$numeroLinha) {
		$retorno = new stdClass();
		$qtdColunasAceitas = 5;

		// Valida quantidade de colunas
		if(count($arrayDados) != $qtdColunasAceitas) {
			$retorno->erro .= "Quantidade de colunas invalida na linha: " . $numeroLinha . PHP_EOL;
		}

		//verificar se o termo existe e estão ativos
		$dadosContrato = $this->dao->buscaDadosContrato(array(
			"connumero" => array("value" => (int) $arrayDados[0], "condition" => "="),
			"conmodalidade" => array("value" => 'L', "condition" => "="),
			"concsioid" => array("value" => 1, "condition" => "=")
		));

		if(isset($dadosContrato->result) && pg_num_rows($dadosContrato->result) == 0) {
			$retorno->erro .= "Contrato " . $arrayDados[0] ." não existe ou não está com status ativo ou não está na modalidade de locação na linha:". $numeroLinha . PHP_EOL;
		}

		//Valida coluna "mantem valor"
		if($arrayDados[1] != 'S' && $arrayDados[1] != 'N') {
			$retorno->erro .= "Valor invalido da coluna 'Mantém valor' na linha: ". $numeroLinha . PHP_EOL;
		}

		// Valida a coluna "valor negociado"
		if($arrayDados[1] == 'N') {
			$valorNegociado = str_replace(',', '.', $arrayDados[2]);

			if(!is_numeric($valorNegociado)){
				$retorno->erro .= "Valor da coluna 'Valor Negociado' não é numérico na linha: " . $numeroLinha . PHP_EOL;
			} else {
				$casasDecimais = explode('.', $valorNegociado);
				if((float) $valorNegociado <= 0) {
					$retorno->erro .= "Valor da coluna 'Valor Negociado' é menor ou igual a zero na linha: " . $numeroLinha . PHP_EOL;
				}else if(isset($casasDecimais[1]) && strlen(trim($casasDecimais[1])) > 2) {
					$retorno->erro .= "Valor da coluna 'Valor Negociado' possui numero de casas decimais inválido na linha: " . $numeroLinha . PHP_EOL;
				}
			}
		}

		// Valida coluna "justificativa"
		if(strlen($arrayDados[3]) > 50) {
			$retorno->erro .= "Quantidade de caracteres na coluna 'Justificativa' é maior que 50 na linha: " . $numeroLinha . PHP_EOL;
		}


        // Valida coluna "ativação acessórios"
        if(trim($arrayDados[4]) != 'S' && trim($arrayDados[4]) != 'N') {
            $retorno->erro .= "Valor inválido na coluna 'Ativar parcelas Acessórios' na linha: " . $numeroLinha . PHP_EOL;
        }

		return $retorno;
	}

}
?>