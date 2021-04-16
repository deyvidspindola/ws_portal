<?php
/**
 * Classe CadGerenciadora.
 * Camada de regra de negócio. 
 * Atualmente serve apenas ao cadastro de Endpoint, o restante continua no arquivo base cad_gerenciadora.php
 *
 * @package  Cadastro
  *
 */
class CadGerenciadora {
    private $view;

    const MENSAGEM_ALERTA_CAMPOS_OBRIGATORIOS = "Existem campos obrigatórios não preenchidos.";
    const MENSAGEM_SUCESSO_INCLUIR            = "Registro incluído com sucesso.";
    const MENSAGEM_SUCESSO_ATUALIZAR          = "Registro alterado com sucesso.";
    const MENSAGEM_SUCESSO_EXCLUIR            = "Registro excluído com sucesso.";
    const MENSAGEM_NENHUM_REGISTRO            = "Nenhum registro encontrado.";
    const MENSAGEM_ERRO_PROCESSAMENTO         = "Houve um erro no processamento dos dados.";
	const MENSAGEM_ERRO_BUSCAR_DADOS          = "Houve erro ao tentar buscar os dados. Por favor atualize a tela (pressione F5) para recarregar os dados de endpoint.";
	const MENSAGEM_ERRO_JSON_DECODE           = "Houve erro ao tentar decodificar os dados recebidos do servidor.";
	const MENSAGEM_ID_GERENCIADORA_INVALIDO   = "Id gerenciadora inválido.";
	const MENSAGEM_PROTOCOLO_INVALIDO         = "Protocolo inválido.";
	const MENSAGEM_IP_INVALIDO                = "Ip inválido.";
	const MENSAGEM_PORTA_INVALIDO             = "Porta inválida.";	
	const MENSAGEM_FAVOR_ATUALIZE_A_TELA      = "Por favor atualize a tela (pressione F5) para recarregar os dados de endpoint.";
	const MENSAGEM_ALERTA_FIREWALL            = "* Atenção: deve-se pedir abertura de firewall para o Endpoint cadastrado via ASM, caso não tenha sido solicitado.";	
	
	const REGEX_FORMATO_IP                    = "/\b((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.|$)){4}\b/m";

    public function __construct() {
        $this->view                  = new stdClass();
        $this->view->parametros      = null;
    } 

    public function index() {
		$uri = "/endpoints/search/findByRiskManagerId/";
		$endpoint_acao = "index";
		$method = "GET";
		
        try {

            $this->view->parametros = $this->tratarParametros();
						
			$data = $this->validarCampos($method);
			if(array_key_exists('exception',$data)) {
				throw new Exception($data['exception']); 
			}
			
			$uri = $uri . $this->view->parametros->risk_manager_id;
			
			//Chama serviço de pesquisa
			$responseMS = $this->executeApi($method, $uri);	
			//error_log($responseMS);
			//print_r($responseMS);
				
			//Requisição bem sucedida
			if ($responseMS['curl_info']['http_code']==200) {

				//Transforma a string result em objeto
				$resultadoMS = json_decode($responseMS['result']);
				
				if(json_last_error() != JSON_ERROR_NONE || $resultadoMS == "") {
					throw new Exception(self::MENSAGEM_ERRO_JSON_DECODE);
				}									
									
				echo json_encode(array(
					"mensagemFirewall" => self::MENSAGEM_ALERTA_FIREWALL,
					"mensagemAviso" => '',
					"mensagemAlerta" => '', 
					"endpointId" => $resultadoMS->id,
					"riskManagerId" => $resultadoMS->riskManagerId,
					"endpointProtocolo" => $resultadoMS->protocol, 
					"endpointIp" => $resultadoMS->ip, 
					"endpointPorta" => $resultadoMS->port,
					"endpointAcao" => 'atualizar'
					)
				);	

			}
			else if($responseMS['curl_info']['http_code']==404) {
				echo json_encode(array(
					"mensagemFirewall" => self::MENSAGEM_ALERTA_FIREWALL,
					"mensagemAviso" => '',
					"mensagemAlerta" => '',	
					"endpointProtocolo" => "nenhum",
					"endpointId" => "",
					"endpointAcao" => 'inserir')
				);
			}
			else{
				throw new Exception(self::MENSAGEM_ERRO_BUSCAR_DADOS);
			}

        } catch (ErrorException $e) {
			
			error_log($responseMS);
			echo json_encode(array(
				"mensagemAviso" => "",
				"mensagemAlerta" => $e->getMessage(),				
				"endpointProtocolo" => "nenhum")); 

        } catch (Exception $e) {
			error_log($responseMS);
			echo json_encode(array(
				"mensagemAviso" => "",
				"mensagemAlerta" => $e->getMessage(),				
				"endpointProtocolo" => "nenhum"));
        }
    }
	
	public function excluir($data = false) {
		$method = 'DELETE';
		$uri = "/endpoints";
		$data = array();
		
		try {
			if($data == false) {
				$this->view->parametros = $this->tratarParametros();
				$data = $this->validarCampos($method);
				if(array_key_exists('exception',$data)) {
					throw new Exception($data['exception']); 
				}			
			}
			
			$uri = $uri . "/" . $data['riskManagerId']; 			
			
			//Chama serviço de pesquisa
			$responseMS = $this->executeApi($method, $uri);	
			
			//Requisição bem sucedida
			if ($responseMS['curl_info']['http_code']==200) {
				echo json_encode(array( "mensagemFirewall" => "",
										"mensagemAviso" => self::MENSAGEM_SUCESSO_EXCLUIR,
										"mensagemAlerta" => "",				
										"endpointId" => '',
										"endpointProtocolo" => 'nenhum', 
										"endpointIp" => '', 
										"endpointPorta" => '8080',
										"endpointAcao" => 'inserir'));				
			} else {
				echo json_encode(array("mensagemAlerta" => self::MENSAGEM_ERRO_PROCESSAMENTO));
			}
			
		} catch (ErrorException $e) {	
			error_log($responseMS);		
			echo json_encode(array(	"mensagemAviso" => "",
									"mensagemAlerta" => $e->getMessage() )); 
        } catch (Exception $e) {
			error_log($responseMS);
			echo json_encode(array(	"mensagemAviso" => "",
									"mensagemAlerta" => $e->getMessage() )); 
		}
			
	}
	
	public function inserir() {		
		$this->atualizar(true);
	}
	
	public function atualizar($novo = false) {
		$uri = "/endpoints";
		$endpoint_acao = "";
		$method = ($novo == true? 'POST' : 'PUT');
		
        try {
            $this->view->parametros = $this->tratarParametros();			
			$data = $this->validarCampos($method);
			
			if(array_key_exists('exception',$data)) {
				throw new Exception($data['exception']); 
			}			

			if($data['protocol'] == "nenhum") {
				if($novo == false) {
					$this->excluir($data);
				} else {

				}					
				return;
			}
			
			if($method == 'PUT') {
				$uri = $uri . "/" . $data['riskManagerId'];
			}
			
			//Chama serviço de pesquisa
			$responseMS = $this->executeApi($method, $uri, $data);
						
			//Requisição bem sucedida
			if ($responseMS['curl_info']['http_code']==200 || $responseMS['curl_info']['http_code']==201) {
						
				//Transforma a string result em objeto
				$resultadoMS = json_decode($responseMS['result']);
				
				if(json_last_error() != JSON_ERROR_NONE || $resultadoMS == "") {
					throw new Exception(self::MENSAGEM_ERRO_JSON_DECODE);
				}
				
				// seta próxima ação					
				$endpoint_acao = "atualizar";
													
				echo json_encode(array(
					"mensagemFirewall" => self::MENSAGEM_ALERTA_FIREWALL,
					"mensagemAviso" => ($novo == true? self::MENSAGEM_SUCESSO_INCLUIR : self::MENSAGEM_SUCESSO_ATUALIZAR),
					"mensagemAlerta" => "", 
					"endpointId" => $resultadoMS->id,
					"riskManagerId" => $resultadoMS->riskManagerId,
					"endpointProtocolo" => $resultadoMS->protocol, 
					"endpointIp" => $resultadoMS->ip, 
					"endpointPorta" => $resultadoMS->port,
					"endpointAcao" => $endpoint_acao
					)
				);
			}

        } catch (ErrorException $e) {	
			error_log($responseMS);		
			echo json_encode(array(	"mensagemAviso" => "",
									"mensagemAlerta" => $e->getMessage() )); 
        } catch (Exception $e) {
			error_log($responseMS);
			echo json_encode(array("mensagemAlerta" => $e->getMessage())); 
		}
		
	}
	
	private function validarCampos($method) {
		
		$data = array();
		
		if(isset($this->view->parametros->risk_manager_id) 
			&& ctype_digit ($this->view->parametros->risk_manager_id)
			&& $this->view->parametros->risk_manager_id > 0) {
			$data['riskManagerId'] = $this->view->parametros->risk_manager_id;
		} else {
			$data['exception'] = self::MENSAGEM_ID_GERENCIADORA_INVALIDO;
			return $data;
		}
		
		if($method == 'GET') {
			return $data;
		}
		
		if(isset($this->view->parametros->endpoint_id) 
			&& ctype_digit ($this->view->parametros->endpoint_id)
			&& $this->view->parametros->endpoint_id > 0) {
			$data['endpointId'] = $this->view->parametros->endpoint_id;
		} else if($method != 'POST'){
			$data['exception'] = self::MENSAGEM_FAVOR_ATUALIZE_A_TELA;
			return $data;
		}
		
		if($method == 'DELETE') {
			return $data;
		}
		
		if(isset($this->view->parametros->ger_endpoint_protocolo) 
			&& ($this->view->parametros->ger_endpoint_protocolo == "nenhum" || $this->view->parametros->ger_endpoint_protocolo == "caessat"  || $this->view->parametros->ger_endpoint_protocolo == "wirsolut")) {
			$data['protocol'] = $this->view->parametros->ger_endpoint_protocolo;
		} else {
			$data['exception'] = self::MENSAGEM_PROTOCOLO_INVALIDO;
			return $data;
		}
		
		if($data['protocol'] == 'nenhum') {
			$data['ip'] = '';
			$data['port'] = '8080';
			return $data;			
		}
		
		if(isset($this->view->parametros->ger_endpoint_ip) 
			&& $this->view->parametros->ger_endpoint_ip != "") {
				
			$resultado = preg_match_all(self::REGEX_FORMATO_IP, $this->view->parametros->ger_endpoint_ip, $out);
			if($resultado != false || $resultado > 0){
				$data['ip'] = $this->view->parametros->ger_endpoint_ip;
			} else {
				$data['exception'] = self::MENSAGEM_IP_INVALIDO;
				return $data;
			}
		} else {
			$data['exception'] = self::MENSAGEM_IP_INVALIDO;
			return $data;
		}

		if(isset($this->view->parametros->ger_endpoint_porta)  
			&& ctype_digit($this->view->parametros->ger_endpoint_porta)
			&& $this->view->parametros->ger_endpoint_porta > 0 && $this->view->parametros->ger_endpoint_porta < 65536) {
			$data['port'] = $this->view->parametros->ger_endpoint_porta;
		} else {
			$data['exception'] = self::MENSAGEM_PORTA_INVALIDO;
			return $data;
		}
		
		return $data;
	}
	
	

    /**
     * Trata os parametros submetidos pelo formulario e popula um objeto com os parametros
     *
     * @return stdClass Parametros tradados
     * @return stdClass
     */
    private function tratarParametros() {

	   $retorno = new stdClass();

        if (count($_POST) > 0) {
            foreach ($_POST as $key => $value) {

                if(is_array($value)) {
                    foreach ($value as $chave => $valor) {
                        $value[$chave] = trim($valor);
                    }
                    $retorno->$key = isset($_POST[$key]) ? $_POST[$key] : array();

                } else {
                    $retorno->$key = isset($_POST[$key]) ? trim($value) : '';
                }
            }
        }
		
        return $retorno;
    }

    private function executeApi($method, $uri, $data = false) {
		
		$curl = curl_init();
		
		$headers = array(
			'Accept: application/json',	
			'Content-Type: application/json; charset=utf-8'
		);
						
		$options = array(
			CURLOPT_URL => _CAESSAT_ENDPOINTS_BASE_URL_.$uri,			
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,			
			CURLOPT_CONNECTTIMEOUT => 120,
			CURLOPT_AUTOREFERER    => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HEADER => false,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_SSL_VERIFYHOST => 0			
		);
		
		switch($method) {
			case 'GET':
			break;
			case 'POST':			
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = json_encode($data);
			break;
			case 'PUT':
			$options[CURLOPT_POSTFIELDS] = json_encode($data);			
			break;
			case 'DELETE':			
			break;
			default:			
			break;
		}

		curl_setopt_array($curl, $options);
		
		$result = curl_exec($curl);
        $curlInfo = curl_getinfo($curl);
		$curlError = curl_error($curl);
        curl_close($curl);

        return array(
            'result' => $result,
            'curl_info' => $curlInfo,
			'curl_error' => $curlError
        );
	}

}