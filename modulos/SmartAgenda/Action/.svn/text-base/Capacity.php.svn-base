<?php
/**
 * @file Capacity.class.php
 */

require_once (_MODULEDIR_ . 'SmartAgenda/Action/SmartAgenda.php');


class Capacity{

	private $smartAgenda;
	private $date = array();
	private $location;
	private $calculate_duration;
	private $calculate_travel_time;
	private $dont_aggregate_results;
	private $determine_location_by_work_zone;
	private $time_slot = array();
	private $work_skill = array();
	private $activity_field = array();
	private $worktype_label;
	private $XA_WO_TYPE;
	private $XA_WO_GROUP;
	private $XA_WO_REASON;
	private $XA_COUNTRY_CODE;
	private $XA_STATE_CODE;
	private $XA_CITY_CODE;
	private $XA_NEIGHBORHOOD_CODE;
	private $userID;

	const ERRO_PROCESSAMENTO = 'Houve um erro no processamento dos dados.';

    public function setUsuario($userID) {
        $this->userID = (int) $userID;
    }

    public function getUsuario() {
        return is_null($this->userID) ? 2750 : $this->userID;
    }

	public function __construct(){

		$this->smartAgenda = new SmartAgenda();

		//seta o nome da API para buscar os dados de acesso
		$this->smartAgenda->API = 'CAPACITY';

		//atributos setados com valor default 1, caso não chame o método set (determinação entre a SASCAR e a Oracle)
		$this->calculate_duration              = 1;
		$this->calculate_travel_time           = 1;
		$this->dont_aggregate_results          = 1;
		$this->determine_location_by_work_zone = 1;
	}

	public function getCapacity() {

		libxml_use_internal_errors(true);

		try {

			##  monta array com os dados para enviar para o WS

			//recupera os dados de acesso do usuário
		    $dadosXML = $this->smartAgenda->getAutenticacao();

		    if(empty($this->date)){
		    	throw new Exception ('Pelo menos uma data para a pesquisa deve ser informada.');
		    }

			$dadosXML['date'] = $this->date;

			if(!empty($this->location)){
				$dadosXML['location'] = $this->location;
			}

			if(!empty($this->calculate_duration)){
				$dadosXML['calculate_duration']  = $this->calculate_duration;
			}

			if(!empty($this->calculate_travel_time)){
				$dadosXML['calculate_travel_time'] = $this->calculate_travel_time;
			}

			if(!empty($this->dont_aggregate_results)){
				$dadosXML['dont_aggregate_results'] = $this->dont_aggregate_results;
			}

			if(!empty($this->determine_location_by_work_zone)){
				$dadosXML['determine_location_by_work_zone'] = $this->determine_location_by_work_zone;
			}

			if(!empty($this->time_slot)){
				$dadosXML['time_slot']  = $this->time_slot;
			}

			if(!empty($this->work_skill)){
				$dadosXML['work_skill'] = $this->work_skill;
			}

			if( _AMBIENTE_ != 'PRODUCAO' ) {
				$this->setActivityField('XA_SCHEDULING_TYPE', 'F');
				$this->setActivityField('aworktype', '53');
			}


			if(!empty($this->worktype_label)){
				$this->setActivityField('worktype_label', $this->worktype_label);
			}

			if(!empty($this->XA_WO_TYPE)){
				$this->setActivityField('XA_WO_TYPE', $this->XA_WO_TYPE);
			}

			if(!empty($this->XA_WO_GROUP)){
				$this->setActivityField('XA_WO_GROUP', $this->XA_WO_GROUP);
			}

			if(!empty($this->XA_WO_REASON)){
				$this->setActivityField('XA_WO_REASON', $this->XA_WO_REASON);
			}

			if(empty($this->XA_COUNTRY_CODE)){
				throw new Exception ('O valor do campo XA_COUNTRY_CODE dever ser informado.');
			}
			$this->setActivityField('XA_COUNTRY_CODE', $this->XA_COUNTRY_CODE);


			if(empty($this->XA_STATE_CODE)){
				throw new Exception ('O valor do campo XA_STATE_CODE dever ser informado.');
			}
			$this->setActivityField('XA_STATE_CODE', $this->XA_STATE_CODE);


			if(empty($this->XA_CITY_CODE)){
				throw new Exception ('O valor do campo XA_CITY_CODE dever ser informado.');
			}
			$this->setActivityField('XA_CITY_CODE', $this->XA_CITY_CODE);


			if(empty($this->XA_NEIGHBORHOOD_CODE)){
				throw new Exception ('O valor do campo XA_NEIGHBORHOOD_CODE dever ser informado.');
			}
			$this->setActivityField('XA_NEIGHBORHOOD_CODE', $this->XA_NEIGHBORHOOD_CODE);


			$dadosXML['activity_field']  = $this->activity_field;

			//gera o XML para consulta ou gravar em banco (não é enviado para o WS, é enviado apenas o ARRAY -> $dadosXML)
			$xml_ = new SimpleXMLElement("<urn:get_capacity></urn:get_capacity>");
			//$envioXML = $this->smartAgenda->arrayToXml($dadosXML, $xml_)->asXML();

			try {

				// Timestamp request
				$timestampRequest = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 8);

				//instancia o WEBSERVICE
				$instancia_ws = $this->smartAgenda->startWebService();

				//enviar os dados para o WS e retorna o response
				$response_ws = $instancia_ws['client']->get_capacity($dadosXML);

				//transforma o retorno (object) em array
				$ret_objeto_array = $this->smartAgenda->arrayCastRecursive($response_ws);

				//tronsforma o array de retorno em XML
   		        $xml_ret = new SimpleXMLElement("<retorno></retorno>");
   			    $retornoXML = $this->smartAgenda->arrayToXml($ret_objeto_array, $xml_ret)->asXML();

			   	// STI 85957 - Vinicius
				$xmlEnvio = (string) $this->smartAgenda->gerarXml($dadosXML, 'get_capacity' );
                $xmlResponse = (string) $this->smartAgenda->gerarXml( $response_ws, 'get_capacity' );

   			    $retorno['envioXML']   = htmlspecialchars($xmlEnvio);
			    $retorno['retornoXML'] = htmlspecialchars($xmlResponse);
			    $retorno['dados']      = $ret_objeto_array;

			    // STI 85957 - Vinicius
                $this->smartAgenda->gravaLogComunicacao(
                    $this->smartAgenda->API,
                    $xmlEnvio,
                    $xmlResponse,
                    $timestampRequest,
                    $this->getUsuario()
                );

				return $retorno;

			} catch (SoapFault $e) {

				$erro['erro']        = true;
				$erro['faultcode']   = $e->faultcode;
				$erro['faultstring'] = $e->faultstring;
				$erro['faultactor']  = $e->faultactor;
				$erro['detail']      = $e->detail;
				$erro['msg']         = self::ERRO_PROCESSAMENTO;

				return $erro;
			}

		} catch ( Exception $e ) {

			$erro['erro'] = true;
			$erro['msg']  = $e->getMessage();
			return $erro;
		}
	}


	private function setActivityField($name, $value){
		array_push($this->activity_field, array('name' => $name, 'value' => $value));
	}

	public function setDate($valor){
		array_push($this->date, $valor);
	}

	public function setLocation($valor){
		$this->location = $valor;
	}

	public function setCalculateDuration($valor){
		$this->calculate_duration = $valor;
	}

	public function setCalculateTraveltTime($valor){
		$this->calculate_travel_time = $valor;
	}

	public function setDontAaggregateResults($valor){
		$this->dont_aggregate_results = $valor;
	}

	public function setDetermineLocationByWorkZone($valor){
		$this->determine_location_by_work_zone = $valor;
	}

	public function setTimeSlot($valor){
		array_push($this->time_slot, $valor);
	}

	public function setWorkSkill($valor){
		array_push($this->work_skill, $valor);
	}

	public function setWorkTypeLabel($valor){
		$this->worktype_label = $valor;
	}

	public function setXA_WO_TYPE($valor){
		$this->XA_WO_TYPE = $valor;
	}

	public function setXA_WO_GROUP($valor){
		$this->XA_WO_GROUP = $valor;
	}

	public function setXA_WO_REASON($valor){
		$this->XA_WO_REASON = $valor;
	}

	public function setXA_COUNTRY_CODE($valor){
		$this->XA_COUNTRY_CODE = $valor;
	}

	public function setXA_STATE_CODE($valor){
		$this->XA_STATE_CODE = $valor;
	}

	public function setXA_CITY_CODE($valor){
		$this->XA_CITY_CODE = $valor;
	}

	public function setXA_NEIGHBORHOOD_CODE($valor){
		$this->XA_NEIGHBORHOOD_CODE = $valor;
	}
    }
?>