<?php

namespace module\WSProtheus;

class IntegracaoProtheusTotvs {
    
    public static function integraProtheusTotvs($dadosIntegracao) {
        
		try {
				
				$dataDefinition = array();
				$dataDefinition["businessHeader"]["operation"] = $dadosIntegracao['operation'];
				$dataDefinition["businessHeader"]["senderId"] = "3f4dad18-2639-11e8-b467-0ed5f89f718b";
				$dataDefinition["data"]["strOrigem"] = $dadosIntegracao["strOrigem"];
				$dataDefinition["data"]["strAmbient"] = _AMBIENTE_;
				$dataDefinition["data"]["idTitle"] = $dadosIntegracao["idTitle"];
				if(isset($dadosIntegracao['idBullet'])){
					$dataDefinition["data"]["idBullet"] = $dadosIntegracao["idBullet"];
				}
				// Cris - [ORGMKTOTVS-1086]
				if(isset($dadosIntegracao['idTitleNew'])){
					$dataDefinition["data"]["idTitleNew"] = $dadosIntegracao["idTitleNew"];
				}
				
				$jsonInfo = json_encode($dataDefinition);

				if (json_last_error()) {
					throw new Exception('Erro ao realizar json encode');
				}

				$function = null;
				
				$url_local = $_SERVER['REQUEST_URI'];
				$url_testes = 'intranet-hom-wsprotheus.sascar.com.br/';
				$function = 'requestIntegrationOff';
				
				// array de operations de TÍTULOS do protheus
				$arrayIntegracoesProtheus = array
				(
					"bolautom",
					"esitef", 
					"trocaccbol", 
					"negociacao", 
					"instalacao", 
					"substituicao", 
					"baixaperda"
				);


				if ($dadosIntegracao['integration'] == true) {
				
					if(in_array($dadosIntegracao['operation'], $arrayIntegracoesProtheus)){
						$function = 'billIntegration';
					}
      			}
				
				$url = _URLWSPROTHEUS_ . $function;
				// para testes local
				if (strtoupper($_SERVER[HTTP_HOST]) == "LOCALHOST") {
					$url = 'http://localhost/WS_PROTHEUS/?action=' . $function;
				}
				
				// Cris - [ORGMKTOTVS-2112] 
				// gerar log banco 
				$dataLog = array();
				$dataLog['usuario_id'] = $_SESSION['usuario']['oid'];
				$dataLog['url_request'] = $url;
				$dataLog['json'] = $jsonInfo;	
				$wsprotheus_intranet_log_id = WsLog::insertWsLog($dataLog);

				// Cris - [ORGMKTOTVS-2112] inserir id do log da intranet
				$dataDefinition["businessHeader"]["wsprotheus_intranet_log_id"] = $wsprotheus_intranet_log_id['id'];
				$jsonInfo = json_encode($dataDefinition);
				
				$ch = curl_init($url);

				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonInfo);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($jsonInfo))
				);
			  
				$result = curl_exec($ch);
				return $jsonRet = json_decode($result);
		} catch (Exception $e) {
			
			$dataLog['json'] = '[Excecao capturada: '.  $e->getMessage() .' Data: ' . date('d/m/Y H:m:s') . ']'.'[Usuario: ' . $_SESSION['usuario']['oid'] . ']'.'[URL WSProtheus: ' . $url . ']';
			WsLog::insertWsLog($dataLog);
		}
    }

}
