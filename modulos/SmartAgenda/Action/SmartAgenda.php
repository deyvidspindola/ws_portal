<?php

//grava log de erro
ini_set("log_errors", 1);
ini_set('error_log','/tmp/smart_agenda_'.date('d-m-Y').'.txt');


require_once _MODULEDIR_ . 'SmartAgenda/DAO/SmartAgendaDAO.php';
require_once _MODULEDIR_ . 'SmartAgenda/Action/OrdemServico.php';

date_default_timezone_set('America/Sao_Paulo');

class SmartAgenda {

    private $dao;
    private $ambiente;
    public $API;
    private $ordemServico;
    private $wsdl;
    private $conn;

    public function __construct($conn = null){
        if(is_null($conn)){
            Global $conn;
        }
        $this->conn = $conn;
        $this->dao              = new SmartAgendaDAO($conn);
        $this->ordemServico     = new OrdemServico($conn);
        $this->getAmbiente();
    }

    public function getUsuarioLogado() {
        return isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '2750';
    }

    private function getAcesso(){

        $retorno = array();
        $retorno['company']     = '';
        $retorno['login']       = '';
        $retorno['auth_string'] = '';
        $retorno['url_wsdl']    = '';
        $passwordCript          = '';



        try {

            if ($this->ambiente === ''){
                throw new Exception("Ambiente desconhecido.");
            }else{
                $this->dao->ws_company  = $this->ambiente.'_COMPANY';
                $this->dao->ws_login    = $this->ambiente.'_LOGIN';
                $this->dao->ws_password = $this->ambiente.'_PASSWORD';
                $this->dao->ws_api      = $this->ambiente.'_'.$this->API;
            }

            //pesquisa os dados de acesso de acordo os dados setados nos atributos da DAO
            $dados = $this->dao->getDadosAcesso();

            if(!is_array($dados)){
                throw new Exception("Dados de acesso não encontrados.");
            }

            //percorre o array para econtratar os dados
            foreach ($dados as $acessos){

                if($acessos['pcsioid'] == $this->dao->ws_company){
                    $retorno['company'] = $acessos['pcsidescricao'];
                }

                if($acessos['pcsioid'] == $this->dao->ws_login){
                    $retorno['login'] = $acessos['pcsidescricao'];
                }

                //recupera a senha do bd criptografada
                if($acessos['pcsioid'] == $this->dao->ws_password){
                    $passwordCript = $acessos['pcsidescricao'];
                }

                //recupera a senha do bd criptografada
                if($acessos['pcsioid'] == $this->dao->ws_api){
                    $retorno['url_wsdl'] = $acessos['pcsidescricao'];
                }
            }

            if($retorno['company'] == ''){
                throw new Exception("Dados de acesso => company não encontrado.");
            }

            if($retorno['login'] == ''){
                throw new Exception("Dados de acesso => login não encontrado.");
            }

            if($retorno['url_wsdl']== ''){
                throw new Exception("Dados de acesso => url_wsdl não encontrado.");
            }

            if($passwordCript == ''){
                throw new Exception("Dados de acesso => password não encontrado.");
            }

            //pega a data do ATOM
            $data_atom = date(DATE_ATOM);

            //gera a string de autenticação concatenada
            $auth_string = md5($data_atom.$passwordCript);

			//GMUD9333 - Alteracao do metodo de autenticacao OFSC - Agendamento unitario
			//SHA256(CURRENT_TIME + SHA256(CLIENT_SECRET + SHA256(CLIENT_ID)))
			$client_id_secret = hash('sha256',$passwordCript.hash('sha256',$retorno['login']));
			$auth_string = hash('sha256',$data_atom.$client_id_secret);

            $retorno['now'] = $data_atom;
            $retorno['auth_string'] = $auth_string;
            $retorno['status'] = 'ok';

            return $retorno;

        } catch (Exception $e) {

            $retorno['status'] = 'erro';
            $retorno['msg'] = $e->getMessage();
            return $retorno;
        }

    }

    private function getAmbiente(){


        if(_AMBIENTE_ == 'DESENVOLVIMENTO'){

            $this->ambiente = 'DESENV';

        }elseif (_AMBIENTE_ == 'TESTE') {

            $this->ambiente = 'TESTE';

        }elseif (_AMBIENTE_ == 'HOMOLOGACAO'){

            $this->ambiente = 'HOMOLOG';

        }elseif (_AMBIENTE_ == 'PRODUCAO')  {

            $this->ambiente = 'PROD';

        }else{
            $this->ambiente = 'DESENV';
        }

        return;
    }

    public function getAutenticacao(){

        //busca dados de acesso
        $acesso = $this->getAcesso();

        if ($acesso['status'] === 'erro') {
            throw new Exception ($acesso['msg'] );
        }

        //seta wsdl do ambiente
        $this->wsdl = $acesso['url_wsdl'];

        //monta array na ordem para envio direto ao metodo do WS
        $return = array(
                'user' => array(
                        'now' => $acesso['now'],
                        'login' => $acesso['login'],
                        'company' => $acesso['company'],
                        'auth_string' => $acesso['auth_string']
                )
        );

        return $return;

    }

    public function startWebService(){

        try {

            if(empty($this->wsdl)){
                throw new Exception("A url do WSDL deve ser informada para instanciar o WebService.");
            }


            if(_AMBIENTE_ == "LOCALHOST") {

                $params = array(
                        'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                        'trace' => true,
                        'exceptions' => 1,
                        'soap_version' => SOAP_1_1,
                        'style' => SOAP_DOCUMENT,
                        'use' => SOAP_LITERAL,

                        'proxy_host' => 'proxy-gvt.sascar.local',
                        'proxy_port' => 8080,

                        'connection_timeout' => 30,
                        'stream_context' => stream_context_create(array('http' => array(
                            'protocol_version' => 1.0,
                        )))

                );

                $streamConfig = array(
                    'http' => array(
                        'proxy' => 'tcp://proxy-gvt.sascar.local:8080',
                        'request_fulluri' => true,
                    )
                );
                $streamContext = stream_context_create($streamConfig);

                //trata o WSDL para tratar erros que houve em algumas conexões
                //@erro: "Start tag expected, '<' not found"
                if(!$xml = file_get_contents($this->wsdl, false, $streamContext)){
                    throw new Exception("Falha de comunicação como o wsdl -->> ".$this->wsdl);
                }
            } else {

            $params = array(
                    'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                    'trace' => true,
                    'exceptions' => 1,
                    'soap_version' => SOAP_1_1,
                    'style' => SOAP_DOCUMENT,
                    'use' => SOAP_LITERAL,
                    'connection_timeout' => 30,
                    'stream_context' => stream_context_create(array('http' => array(
                        'protocol_version' => 1.0,
                    )))

            );


            //trata o WSDL para tratar erros que houve em algumas conexões
            //@erro: "Start tag expected, '<' not found"
            if(!$xml = file_get_contents($this->wsdl, false)){
                throw new Exception("Falha de comunicação como o wsdl -->> ".$this->wsdl);
            }

        }


            $file = 'data://text/plain;base64,'.base64_encode($xml);

            // Ambiente
            $client = new SoapClient($file, $params);

            $retorno['status'] = 'ok';
            $retorno['client'] = $client;

            return $retorno;

        }catch (SoapFault $e){

            $retorno['status'] = 'erro';
            $retorno['msg'] = $e;

            return $retorno;
        }

    }

    public function arrayToXml(array $arr, SimpleXMLElement $xml, $beforeTag=null) {

        foreach ($arr as $k => $v) {

            $attrArr = array();
            $kArray = explode(' ',$k);
            $tag = array_shift($kArray);

            if (is_array($v)) {
                if (is_numeric($k)) {
                    $tag = $beforeTag;

                    $child = $xml->addChild($tag);
                    $this->arrayToXml($v, $child, $tag);
                }else {
                    $arrk = array_keys($v);
                    if(is_int($arrk['0']) > 0){
                        $child = $xml;
                    }else{
                        $child = $xml->addChild($tag);
                    }

                    $this->arrayToXml($v, $child, $tag);
                }
            } else {
                if (is_numeric($k)) {
                    $tag = $beforeTag;
                }

                $xml->addChild($tag,  htmlspecialchars($v));
            }
        }

        return $xml;
    }


    public function arrayCastRecursive($array) {

        if (is_array ( $array )) {
            foreach ( $array as $key => $value ) {
                if (is_array ( $value )) {
                    $array [$key] = $this->arrayCastRecursive ( $value );
                }
                if ($value instanceof stdClass) {
                    $array [$key] = $this->arrayCastRecursive ( ( array ) $value );
                }
            }
        }
        if ($array instanceof stdClass) {
            return $this->arrayCastRecursive ( ( array ) $array );
        }
        return $array;
    }

    public function gravaLogComunicacao($servicoExecutante,$xmlRequest,$xmlResponse,$timestampRequest,$usuario) {
        $xmlRequest = $this->formatXml($xmlRequest);
        if(!is_null($xmlResponse) && trim($xmlResponse) != '') {
            $xmlResponse = $this->formatXml($xmlResponse);
        }
        // Busca id do serviço executante
        $slcslctoid = $this->dao->servicoExecutante($servicoExecutante);
        // Grava registro de log da transação
        $this->dao->gravaLogComunicacao($slcslctoid,$xmlRequest,$xmlResponse,$timestampRequest,$usuario);
    }

    public function formatXml($xml) {
        $dom = new DOMDocument;
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($xml);
        $dom->formatOutput = TRUE;

        return $dom->saveXml();
    }

    public function objectToArray($obj) {
        if(is_object($obj)) $obj = (array) $obj;
        if(is_array($obj)) {
            $new = array();
            foreach($obj as $key => $val) {
                $new[$key] = $this->objectToArray($val);
            }
        }
        else $new = $obj;
        return $new;
    }


    public function gerarXml($dadosXML, $operacao) {
        $xml = '';
        $operacao = trim($operacao);

        if(is_object($dadosXML)) {
            $dadosXML = $this->objectToArray($dadosXML);
        }

        if(is_array($dadosXML) && strlen($operacao) > 0) {
            $xml_ = new SimpleXMLElement("<".$operacao."></".$operacao.">");
            $xml = $this->arrayToXml($dadosXML, $xml_)->asXML();
        }

        return $xml;
    }

    public function parametrosSmartAgenda($parametros = null) {

        if(!is_null($parametros) && is_array($parametros)) {
            foreach ($parametros as $chave => $parametro) {
                $parametros[$chave] = "'". pg_escape_string($parametro) ."'";
            }

            $parametros = implode(',', $parametros);
        }

        $valorRetorno = $this->dao->parametrosSmartAgenda($parametros);

        return $valorRetorno;
    }

    public function duracaoAtividade($paramDuracao, $ordemServico, $duracao){

        if( ($paramDuracao['CONSIDERA_TEMPO_ATIVIDADE_OFSC'] == 'S' &&
            $paramDuracao['DURACAO_PADRAO_ATIVIDADE_OFSC'] == $duracao) ||
            ($paramDuracao['CONSIDERA_TEMPO_ATIVIDADE_OFSC'] == 'N') || 
            intval($duracao) <= 0 ) {

            $duracao = ( $ordemServico['dificuldade'] * intval($paramDuracao['FATOR_CALCULO_TEMPO_PESO']) );
        }

        return $duracao;
    }

    public function getWorkSkills($idOrdemServico, $isConsumo = false){

        $workSkills              = array();
        $habilidadesFiltro       = array(   1 => 'occtelemetria',
                                            2 => 'occtrava',
                                            3 => 'occbau',
                                            4 => 'occblindado',
                                            5 => 'occcamera_fadiga',
                                            6 => 'occotr_porto_ind');
        $idClasseGrupo           = 0;
        $idSkillClasse           = null;
        $filtroHabilidades       = "";

        if (empty($idOrdemServico)){
            return $workSkills;
        }

        try {
           
            $dadosOrdemServico = $this->ordemServico->recuperarDadosOrdemServico(array('ordconnumero'), "WHERE ordoid = " . $idOrdemServico);

            $idContrato = $dadosOrdemServico[0]['ordconnumero'];

            if(empty($idContrato)) {
                return $workSkills;
            }

            $dadosContrato = $this->dao->getWorkSillContrato($idContrato);

            $idClasseGrupo       = $dadosContrato->eqcecgoid;
            $idSkillClasse       = $dadosContrato->eqcohcoid;

            $idsHabilidadeGrupoItens = $this->dao->getWorkSkillOrdemServico($idOrdemServico);

            if(! empty($idSkillClasse)){
                array_push($idsHabilidadeGrupoItens, $idSkillClasse); 
            }  

            // Gera o filtro das habilidades
            $i = 0;
            foreach ($habilidadesFiltro as $id => $campo) {

                if(in_array($id, $idsHabilidadeGrupoItens)) {
                  if ($i == 0) {
                    $filtroHabilidades .= $campo . " IS TRUE ";
                  } else {

                    if($isConsumo) {
                        $filtroHabilidades .= " OR " . $campo . " IS TRUE ";
                    } else {
                        $filtroHabilidades .= " AND " . $campo . " IS TRUE ";
                    }
                  }
                  $i++;
                }
            }
          

            if(!$isConsumo) {              

                if(count($idsHabilidadeGrupoItens) > 0) {
                    $workSkills =  $this->dao->getWorkSkillsItens($filtroHabilidades);
                } else {                  

                    if(!empty($idClasseGrupo)){                                 
                        $workSkills =  $this->dao->getWorkSkillGrupoClasse($idClasseGrupo);
                    }
                }

            } else {

                $workSkillClasse = array();
                $workSkillItens = array();

                if(count($idsHabilidadeGrupoItens) > 0) {
                    $workSkillItens =  $this->dao->getWorkSkillsItens($filtroHabilidades);
                }

                if(!empty($idClasseGrupo)){                    
                    $workSkillClasse =  $this->dao->getWorkSkillGrupoClasse($idClasseGrupo);
                }

                $workSkills = array_merge($workSkillItens, $workSkillClasse);
                $workSkills = array_unique($workSkills);

            }                 

            return $workSkills;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    public function getOrdenacaoPrestadores($modalidade, $tipo) {

        $dados = $this->dao->getOrdenacaoPrestadores($modalidade, $tipo);

        return $dados;

     }

      public function getBairroMapeado($idBairro, $idCidade, $idEstado) {

        $retorno = $this->dao->getBairroMapeado($idBairro, $idCidade, $idEstado);

        return $retorno;
      }

    public function revalidarEnderecoAtendimento($idPrestador){

        $dados = array();

        if( !empty($idPrestador) ){

            $dadosPrestador = $this->dao->recuperarDadosEnderecoPrestador($idPrestador);

            $dadosWorkZone = $this->dao->getDadosWorkZonePrestador(
                $dadosPrestador[0]['xa_state_code'],
                $dadosPrestador[0]['city'],
                $dadosPrestador[0]['xa_neighborhood_name']
                );

            if(count($dadosWorkZone) > 0) {

                $dadosWorkZone[0]['xa_neighborhood_code'] = $this->getBairroMapeado(
                                                                    $dadosWorkZone[0]['id_bairro'],
                                                                    $dadosWorkZone[0]['id_cidade'],
                                                                    $dadosWorkZone[0]['id_estado']
                                                                );

                //Work Zone
                $dados['XA_STATE_CODE']        = $dadosPrestador[0]['xa_state_code'];
                $dados['XA_CITY_CODE']         = $dadosWorkZone[0]['xa_city_code'];
                $dados['XA_NEIGHBORHOOD_CODE'] = $dadosWorkZone[0]['xa_neighborhood_code'];
                $dados['id_estado']            = $dadosWorkZone[0]['id_estado'];
                $dados['id_cidade']            = $dadosWorkZone[0]['id_cidade'];
                $dados['id_bairro']            = $dadosWorkZone[0]['id_bairro'];

            } else {

                //Work Zone [area nao definida] quando nao encontrou o endereco para evitar ERRO ao criar atividade sem zona mapeada
                $dados['XA_STATE_CODE']        = 'XX';
                $dados['XA_CITY_CODE']         = '99999999';
                $dados['XA_NEIGHBORHOOD_CODE'] = '99999999';
                $dados['id_estado']            = NULL;
                $dados['id_cidade']            = NULL;
                $dados['id_bairro']            = NULL;

            }

            //Endereco Atendimento
            $dados['XA_NEIGHBORHOOD_NAME'] = $dadosPrestador[0]['xa_neighborhood_name'];
            $dados['XA_ADDRESS_REFERENCE'] = $dadosPrestador[0]['xa_address_reference'];
            $dados['XA_ADDRESS_2'] = $dadosPrestador[0]['xa_address_2'];
            $dados['address']      = $dadosPrestador[0]['address'];
            $dados['city']         = $dadosPrestador[0]['city'];
            $dados['state']        = $dadosPrestador[0]['state'];
            $dados['zip']          = $dadosPrestador[0]['zip'];

       }
        return $dados;

    }
}


?>
