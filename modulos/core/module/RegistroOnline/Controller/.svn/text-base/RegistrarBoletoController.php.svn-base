<?

    namespace module\RegistroOnline;
    
    use infra\ComumController,
        infra\Helper\Response,
        module\RegistroOnline\RegistrarBoletoModel as registrarDAO,
        module\Boleto\BoletoService as Boleto;

    class RegistrarBoletoController extends ComumController{
        public function __construct() {

            $this->registro = new registrarDAO();
            $this->response = new Response();
        }

        /**
         * STI 86970 - setRegistrarTitulo - Metodo que inicia o processo para registrar o Ti­tulo
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param recebe o array com os parametros enviados pela classe RegistrarBoletoService
         * @return boolean retorna sucesso ou o erro gerado
         *     
        */

        public function setRegistrarTitulo(array $params) {

            $getDados = $this->registro->getDadosRegistro($params['clienteId'], $params['tituloId']);
            
            if ($getDados == false) {
                $this->response->setResult(true, 'INF002');
                return $this->response;
            }
            
            $paramsMerged = array_merge($getDados, $params);
            
            $xml = $this->getTicketXml($getDados, $params, Boleto::isBoletoSeco($params['tituloId']));            
            
            $xmlCreate = $this->requestXml($xml, $getDados, $params);
            
            if ($xmlCreate == false) {
                $this->response->setResult(true, 'INF002');
                return $this->response;
            }

            if (isset($xmlCreate->dados) && $xmlCreate->dados != '0') {
                return $xmlCreate;
            }

            $this->response->setResult(true, '0');
            return $this->response;
        }

        /** 
         * STI 86970_3 - verificaRegistrarTitulo - Metodo que retorna o retCode (codigo de erro da solicitacao de ticket) a partir do XML recebido
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param array $responseRegistrarTitulo recebe o array com os parametros enviados pela classe RegistrarBoletoService
         * @return integer $responseRegistrarTitulo->TicketResponse->retCode
         *     
        */

        public function verificaRegistrarTitulo($responseRegistrarTitulo) {

            if (isset($responseRegistrarTitulo->TicketResponse->retCode)) {

                return $responseRegistrarTitulo->TicketResponse->retCode;
            }
            else
                return false;
        }

        /** 
         * STI 86970_3 - validarSituacao - Metodo que verifica a situacao da resposta da validacao da cobranca
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param recebe o array com os parametros enviados pela classe RegistrarBoletoService
         * @return integer $responseRegistrarTitulo->TicketResponse->retCode
         *     
        */

        public function validarSituacao($rResponse) {

            if ($rResponse->return->situacao == 20 && stripos($rResponse->return->descricaoErro, 'TITULO EXISTENTE') == -1) {
                return false;
            }

            return true;
        }

        /** 
         * STI 86970 - createEntry - Metodo que parametriza os campos do xml
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param $key chave do registro
         * @param $value valor do registro
         * @return array retorna o array parametrizado de acordo com o formato esperado no xml
         *     
        */

        public function createEntry($key, $value) {

          $toReturn = array('key'   => $key,
                            'value' => utf8_encode($value));
          return $toReturn;
        }

        /** 
         * STI 86970 - getTicketXml - Metodo que parametriza os campos do xml
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param $dados dados que vieram do metodo getDadosRegistro
         * @param $params sao os parametros que foram passados no instanciamento da classe setRegistro
         * @return array com os valores estruturados para validacao do XML
         *     
        */

        public function getTicketXml($dados, $params, $isBoletoSeco = false) {

            $dados = array_merge($dados, $params);
            $dom = new \DOMDocument('1.0', 'utf-8');

            $convenio = $this->registro->getParametros('COBRANCA_REGISTRADA','WEBSERVICE_COD_CONVENIO');

            $percentual_multa = $this->registro->getParametros('COBRANCA_REGISTRADA','PERCENTO_MULTA_APOS_VENCER');
            $percentual_multa = str_replace('.', '', number_format($percentual_multa['pcsidescricao'], 2));

            $percento_juros = $this->registro->getParametros('COBRANCA_REGISTRADA','PERCENTO_JUROS_AO_MES');
            $percento_juros = str_replace('.', '', number_format($percento_juros['pcsidescricao'], 2));
            
            $dias_baixa = $this->registro->getParametros('COBRANCA_REGISTRADA', 'DIAS_BAIXA_DEVOLUCAO');

            if ($dados['titvl_desconto'] != 0) {
                $tp_desconto = 1;
                $vl_desconto = $this->formataValor($dados['titvl_desconto'], 9);
                $dt_lim_desc = $dados['data_desconto1'];
            }
            else {
                $tp_desconto = 0;
                $vl_desconto = $this->formataValor($dados['titvl_desconto'], 9);
                $dt_lim_desc = '00000000';
            }
			
			$tipo_inscricao = $dados['tipo_inscricao'];
            $inscricao = $dados['inscricao'];
            $nome = substr($dados['nome'], 0, 40);
            $endereco = substr($dados['endereco'],0,40); //Validando maximo de 40 caracteres
            $cidade = substr($dados['cidade'], 0 , 20);
            $uf = substr($dados['uf'], 0, 2);
            $cep = substr($dados['cep'], 0, 8);
            $data_desconto = $dados['data_desconto1'];
            $data_emissao = date('dmY');//$dados['data_emissao'];
            $valor_nominal = $this->formataValor($dados['valor_nominal'], 13);
            
            $data_vencimento = ($dados['data_vencimento'] != '') ? $dados['data_vencimento']: $dados['data_vencimento_bd'];
            
            $bairro = substr($dados['bairro'], 0, 30);

            $seu_numero = $this->getNossoNumero($dados['clienteId'], $dados['tituloId'], $valor_nominal, $data_vencimento, $inscricao); 
           
		$nosso_numero = $seu_numero;
	
            if(strstr($data_vencimento,'/')){
            	$dataVencimento = explode('/',$data_vencimento);
            	$dataVencimento = $dataVencimento[0].$dataVencimento[1].$dataVencimento[2];
            	
            }elseif(strstr($data_vencimento,'-')){
            	$dataVencimento = explode('-',$data_vencimento);
            	$dataVencimento = $dataVencimento[0].$dataVencimento[1].$dataVencimento[2];
            }else{
            	$dataVencimento = $data_vencimento;
            }
            
            //sti-86970-1
            $configNossoNumero = $this->getConfigForTiposTitulos();

            //sti-86970-1
            $dadoParams = array($this->getParamsForTitulo($dados, $seu_numero), $configNossoNumero);

            // sti-86970-1Atualiza o Nosso Numero
            $isNossoNumeroAtualizado = $this->registro->updateNossoNumero($dadoParams);

            if ($isNossoNumeroAtualizado == false) {
                return $this->response->setResult(true, 'INF002');
            }
            
            if ($isBoletoSeco) {
                $dias_baixa = $this->registro->getParametros('COBRANCA_REGISTRADA', 'DIAS_BAIXA_DEVOLUCAO_BOLETO_SECO');
                $data_baixa = date('d/m/Y', strtotime("+{$dias_baixa['pcsidescricao']} days"));
                $instrucao = Boleto::getInstrucoes(true, $data_baixa);

                if (!empty($instrucao->dados) && is_array($instrucao->dados)) {
                    $instrucao = array('pcsidescricao' => $instrucao->dados['pcsidescricao']);
                }

                $percentual_multa = '00';
                $percento_juros = '00';
            } else {
                $instrucao = Boleto::getInstrucoes(false);
            }

            if (!$params['updateDatabase']) {
                $data_vencimento = date('dmY');
            }

            $dados = array($this->createEntry('CONVENIO.COD-BANCO', '0033'), 
                          $this->createEntry('CONVENIO.COD-CONVENIO', $convenio['pcsidescricao']),
                          
                          $this->createEntry('PAGADOR.TP-DOC', $tipo_inscricao),
                          $this->createEntry('PAGADOR.NUM-DOC', $inscricao),
                          $this->createEntry('PAGADOR.NOME', $nome),
                          $this->createEntry('PAGADOR.ENDER', $endereco),
                          $this->createEntry('PAGADOR.BAIRRO', $bairro),
                          $this->createEntry('PAGADOR.CIDADE', $cidade),
                          $this->createEntry('PAGADOR.UF', $uf),
                          $this->createEntry('PAGADOR.CEP', $cep),
                          
                          $this->createEntry('TITULO.NOSSO-NUMERO', $nosso_numero),
                          $this->createEntry('TITULO.SEU-NUMERO', $seu_numero),
            		      $this->createEntry('TITULO.DT-VENCTO', $dataVencimento),
                          $this->createEntry('TITULO.DT-EMISSAO', $data_emissao),
                          $this->createEntry('TITULO.ESPECIE', '02'),
                          $this->createEntry('TITULO.VL-NOMINAL', $valor_nominal),
                          $this->createEntry('TITULO.PC-MULTA', $percentual_multa),
                          $this->createEntry('TITULO.QT-DIAS-MULTA', '00'),
            		      $this->createEntry('TITULO.PC-JURO', $percento_juros),
                          $this->createEntry('TITULO.TP-DESC', $tp_desconto),
                          $this->createEntry('TITULO.VL-DESC', $vl_desconto),
                          $this->createEntry('TITULO.DT-LIMI-DESC', $dt_lim_desc),
                          $this->createEntry('TITULO.VL-ABATIMENTO', '0000000000000'),
                          $this->createEntry('TITULO.TP-PROTESTO', '0'),
                          $this->createEntry('TITULO.QT-DIAS-PROTESTO', '00'),
                          $this->createEntry('TITULO.QT-DIAS-BAIXA', $dias_baixa['pcsidescricao']),  
            		      $this->createEntry('MENSAGEM',substr($instrucao->dados['pcsidescricao'], 0, 100))
                         );

          $ticketRequest = array('dados'     => $dados,
                                 'expiracao' => 100,
                                 'sistema'   => 'YMB');

          $toReturn = array('TicketRequest' => $ticketRequest);
          
          return $toReturn;
        }

        /**		
         * STI 86970_1 - getConfigForTiposTitulos - Retorna os dados de configuracao serializados para update		
         *		
         * @author  marcelo.brondani marcelo.brondani@meta.com.br		
         * @since 21/08/2017		
         * @version 21/08/2017		
         * @return array dados serializados de configuracao para atualizar nosso numero		
         */		
         public function getConfigForTiposTitulos() {		
            return array( 		
                array('tabela'      => 'titulo',		
                      'colNossoNum' => 'titnumero_registro_banco',		
                      'colFormCob'  => 'titformacobranca',		
                      'colIdTitulo' => 'titoid'), 		
                array('tabela'      => 'titulo_retencao', 		
                      'colNossoNum' => 'titnumero_registro_banco',		
                      'colFormCob'  => 'titformacobranca',		
                      'colIdTitulo' => 'titoid'), 		
                array('tabela'      => 'titulo_consolidado', 		
                      'colNossoNum' => 'titcnumero_registro_banco',		
                      'colFormCob'  => 'titcformacobranca',		
                      'colIdTitulo' => 'titcoid')		
            ); 		
        }		
        /**		
         * STI 86970_1 - Retorna os dados de parametros serializados para update		
         *		
         * @author  marcelo.brondani marcelo.brondani@meta.com.br		
         * @since 21/08/2017		
         * @version 21/08/2017		
         * @param array $dados [<description>]		
         * @param int $seu_numero seu numero		
         * @return array dados serializados para atualizar nosso numero		
         */		
        public function getParamsForTitulo($dados, $seu_numero=0) {		
            return array('tabela' => $dados['tipo'],		
                         'coluna' => 'tipo',		
                         'numero' => $seu_numero,		
                         'id_titulo' => $dados['identificacao_titulo'],		
                        );		
        }

        /** 
         * STI 86970 - requestXml - Metodo que envia a validacao do ticket e da cobranca para o servico do Santander
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param $dados dados que vieram do metodo getDadosRegistro
         * @param $params sao os parametros que foram passados no instanciamento da classe setRegistro
         * @return boolean caso tenha ocorrido como o esperado ou retorna a descricao do erro
         *     
        */

        public function requestXml($xmlCreate, $dados, $params) {

            try
            {
            	//ambiente de desenvolvimento
            	if(_AMBIENTE_ != 'PRODUCAO') {
            		$options = array(
            				'keep_alive' => false,
            				'trace'      => true,
            				'local_cert' => __local_cert, // substituir pelo caminho do certificado
            				'exceptions' => true,
            				'cache_wsdl' => WSDL_CACHE_NONE
            			//	'proxy_host' => 'proxy-gvt.sascar.local',
            			//	'proxy_port' => 8080
            		);
            	
                //produção
            	}else{
            		
            		$options = array(
            				'keep_alive' => false,
            				'trace'      => true,
            				'local_cert' => __local_cert, // substituir pelo caminho do certificado
            				'exceptions' => true,
            				'cache_wsdl' => WSDL_CACHE_NONE
            		);
            		
            	}
            	
                $urlTicket = $this->registro->getParametros('COBRANCA_REGISTRADA', 'WEBSERVICE_TICKET');
                
                if ($urlTicket == false) {
                    $this->response->setResult(true, 'INF002');
                    return $this->response;
                }
    
                $urlCobranca = $this->registro->getParametros('COBRANCA_REGISTRADA', 'WEBSERVICE_COBRANCA');
                
                if ($urlCobranca == false) {
                    $this->response->setResult(true, 'INF002');
                    return $this->response;
                }

                //Envia o Xml para o servico
                $cliTicket = new \SoapClient($urlTicket['pcsidescricao'], $options); 
                $cResponse = $cliTicket->create($xmlCreate);
                
                //Associa o xml com as respectivas chaves
                foreach ($xmlCreate['TicketRequest']['dados'] as $val) {
                    $xmlAssoc[$val['key']] = $val['value'];
                }
                
                //insere o evento na tabela evento_titulo e titulo_historico_online
                $this->registro->registraTitulo($cResponse, $params, $xmlAssoc); 

                /* Verifica o retCode */
                $verificaRetCode = $this->verificaRegistrarTitulo($cResponse); //envia o xml de resposta para buscar se existe código de erro;
                
                if ($verificaRetCode != 0) {
                    $this->response->setResult(false, $verificaRetCode, $this->registro->getErroCode($verificaRetCode));          
                    return $this->response;
                }

                // Envia o Xml de cobranca
                $cliCobranca = new \SoapClient($urlCobranca['pcsidescricao'], $options);
                $xmlRegistro = $this->getRegistroXml($cResponse->TicketResponse->ticket, $params['tituloId'], $dados['data_emissao']);
                
                $rResponse = $cliCobranca->registraTitulo($xmlRegistro);
                
                if (!empty($rResponse->return->descricaoErro) && $rResponse->return->descricaoErro > 0) {
                    $this->response->setResult($rResponse->return->descricaoErro, 'CBR001');
                    return $this->response;
                }

                $validarSituacao = $this->validarSituacao($rResponse);
                
                if ($validarSituacao == false) { 
                    $this->response->setResult(true, 'INF002');
                    return $this->response;
                }

                $isAlterFormCobrancaCobTitoReg = true;
                
                if ($params['updateDatabase']) {
                    $configForTitulo = $this->getConfigForTiposTitulos();
                    $formCobParams = array($this->getParamsForTitulo($dados), $configForTitulo, array('codFormCobtit' => 84));
                    $isAlterFormCobrancaCobTitoReg = $this->registro->updateAlterFormCobTitoReg($formCobParams);
                }
                
                if ($isAlterFormCobrancaCobTitoReg == false) {
                   $this->response->setResult(true, 'INF002');
                   return $this->response;
                }

                $atualizaStatusTitulo = $this->updateTitulo($rResponse, $dados);
                $verificaNsu = $this->consultarTitulo($rResponse);

                if ($verificaNsu == null) {
                    $this->response->setResult(true, 'INF002');
                    return $this->response;
                }

                //STI 86970 1.1
                //@author marcelo.brondani.ext
                //retorna o tpetoid do tipo_evento_titulo
                $tpetoid = $this->getId_tpetoid();

                //STI 86970 1.1
                //@author marcelo.brondani.ext
                //atualiza o status dos titulos registrados
                $isUpdateStInTito     = $this->updateStatusInTitulo($params['tituloId'], $tpetoid);
                $isUpdateStInTitoRet  = $this->updateStatusInTituloRetencao($params['tituloId'], $tpetoid);
                $isUpdateStInTitoCons = $this->updateStatusInTituloConsolidado($params['tituloId'], $tpetoid);

                return true;

            }
            catch(\SoapFault $e)
            {
                return false;
            }
        }
        
        /** 
         * STI 86970 1.1 - Atualiza o status do titulo
         *
         * @author Marcelo.brondani.ext
         * @version 27/08/2017
         * @return int id do tipo_evento_titulo
         *     
        */
        public function updateStatusInTitulo($tituloId, $tpetoid) {
            return $this->registro->updateStatusInTitulo($tituloId, $tpetoid);
        }
        
        /** 
         * STI 86970 1.1 - Atualiza o status do titulo retencao
         *
         * @author Marcelo.brondani.ext
         * @version 27/08/2017
         * @return int id do tipo_evento_titulo
         *     
        */
        public function updateStatusInTituloRetencao($tituloId, $tpetoid) {
            $this->registro->updateStatusInTituloRetencao($tituloId, $tpetoid);
        }
        
        /** 
         * STI 86970 1.1 - Atualiza o status do titulo consolidado
         *
         * @author Marcelo.brondani.ext
         * @version 27/08/2017
         * @return int id do tipo_evento_titulo
         *     
        */
        public function updateStatusInTituloConsolidado($tituloId, $tpetoid) {
            return $this->registro->updateStatusInTituloConsolidado($tituloId, $tpetoid);
        }

        /** 
         * STI 86970 1.1 - Retorna o id
         *
         * @author Marcelo.brondani.ext
         * @version 27/08/2017
         * @return int id do tipo_evento_titulo
         *     
        */
        public function getId_tpetoid(){
            return $this->registro->getId_tpetoid();
        }

        /** 
         * STI 86970 - updateTitulo - Metodo que atualiza o Ti­tulo no db
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param array $rResponse resposta do envio de solicitacao de cobranca
         * @param array $dados sao os dados obtidos atraves do metodo getDadosRegistro
         * @param $tituloId e o id do titulo em questao
         * @return boolean com o status da insercao
         *     
        */

        public function updateTitulo($rResponse, $dados) {

            return $this->registro->updateTitulo($rResponse, $dados);
        }


        /** 
         * STI 86970 - getRegistroXml - Metodo que monta a solicitacao para o registro da cobranca
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param $ticket numero do ticket retornado pela requisicao do metodo registraTitulo (enviado pelo Santander)
         * @param $tituloId sao os parametros que foram passados no instanciamento da classe setRegistro
         * @return array com o xml de resposta do servico
         *     
        */

        public function getRegistroXml($ticket, $tituloId) {

            $hoje = date('dmY');
            $estacao = $this->registro->getParametros('COBRANCA_REGISTRADA','WEBSERVICE_SIGLA_ESTACAO');
            $nsu = $tituloId.$hoje;

            if (_AMBIENTE_ == 'DESENVOLVIMENTO' || _AMBIENTE_ == 'HOMOLOGACAO' || _AMBIENTE_ == 'TESTE') {
                $nsu = 'TST'.$nsu;
                $tpAmbiente = 'T';
            }
            else {
                $tpAmbiente = 'P';
            }

            $inclusaoTitulo = array('dtNsu'    => $hoje,
                                  'estacao'    => $estacao['pcsidescricao'],
                                  'nsu'        => $nsu, 
                                  'ticket'     => $ticket,
                                  'tpAmbiente' => $tpAmbiente
                                 );

            $toReturn = array('dto' => $inclusaoTitulo);

            return $toReturn;
        }


        /** 
         * STI 86970_3 - consultarTitulo - Metodo que consulta se titulo ja existe
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param array $rResponse
         * @return string contendo o numero do nsu do titulo caso exista, ou vazio
         *     
        */

        public function consultarTitulo($rResponse) {
            return $this->registro->consultarTitulo($rResponse);
        }


        /** 
         * STI 86970_3 - formataValor - Metodo para normalizar os dados do tipo valor
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param string $valor o valor a ser formatado
         * @param integer $pad a quantidade de 0 esquerda
         * @return string contendo o valor formatado de acordo
         *     
        */

        public function formataValor($valor, $pad) {
            
            return STR_PAD(str_replace('.', '', $valor), $pad, '0', STR_PAD_LEFT);
        }

        /**
         * STI 86970 - getNossoNumero - Metodo que parametriza as variaveis para buscar o nosso numero da classe BoletoService
         *
         * @author Dimitrius T Passos
         * @version 21/08/2017
         * @param $clienteId (valor da chave do Cliente)
         * @param $tituloId (valor da chave do Ti­tulo)
         * @param $valor_nominal Valor Nominal
         * @param $dtVenc Data de Vencimento
         * @param $numeroDocumento numero do Documento
         * @return Response $nosso_numero->dados: retorna o numero atualizado
         *     
        */

        public function getNossoNumero($clienteId, $tituloId, $valorNominal, $dtVenc, $numeroDocumento='') {
                  
        	if(strstr($dtVenc,'/')){
        		$dataVencimento = explode('/',$dtVenc);
        		$dataVencimento = $dataVencimento[0].'-'.$dataVencimento[1].'-'.$dataVencimento[2];
        		
        	}elseif(strstr($dtVenc,'-')){
        		$dataVencimento = explode('-',$dtVenc);
        		$dataVencimento = $dataVencimento[0].'-'.$dataVencimento[1].'-'.$dataVencimento[2];
        	}else{
        		$dataVencimento = $dtVenc;
        	}
 
            $dados = array(
                    'forcoid'  => 84,
                    'cfbbanco' => 33,
                    'clioid'   => $clienteId,
            		'dataVencimento' => $dataVencimento,
                    'valor'      => $valorNominal,
                    'sequencial' => $tituloId,
                    'carteira'   => 101,
                    'ios'        => 0,
                    'numeroDocumento' => $numeroDocumento
                    );

            $nosso_numero = Boleto::getNossoNumero($dados, '033');
            return $nosso_numero->dados;
        }

        public function getSonda($params) {    
            $getDados = $this->registro->getDadosRegistro($params['clienteId'], $params['tituloId']);
            
            if ($getDados == false) {
                $this->response->setResult(true, 'INF002');
                return $this->response;
            }

            $paramsMerged = array_merge($getDados, $params);
            $xml = $this->getTicketXml($getDados, $params, Boleto::isBoletoSeco($params['tituloId']));

            //ambiente de desenvolvimento
            if(_AMBIENTE_ != 'PRODUCAO') {
                $options = array(
                        'keep_alive' => false,
                        'trace'      => true,
                        'local_cert' => __local_cert, // substituir pelo caminho do certificado
                        'exceptions' => true,
                        'cache_wsdl' => WSDL_CACHE_NONE
                       // 'proxy_host' => 'proxy-gvt.sascar.local',
                       // 'proxy_port' => 8080
                );
            
            //produção
            }else{
                
                $options = array(
                        'keep_alive' => false,
                        'trace'      => true,
                        'local_cert' => __local_cert, // substituir pelo caminho do certificado
                        'exceptions' => true,
                        'cache_wsdl' => WSDL_CACHE_NONE
                );
                
            }
                
            $urlTicket = $this->registro->getParametros('COBRANCA_REGISTRADA', 'WEBSERVICE_TICKET');
            
            if ($urlTicket == false) {
                $this->response->setResult(true, 'INF002');
                return $this->response;
            }
            
            $cliTicket = new \SoapClient($urlTicket['pcsidescricao'], $options); 
            $cResponse = $cliTicket->create($xml);

            $ticket = $cResponse->TicketResponse->ticket;
            $params = $this->getRegistroXml($ticket, $params['tituloId']);

            $cliCobranca = new \SoapClient("https://ymbcash.santander.com.br/ymbsrv/CobrancaEndpointService/CobrancaEndpointService.wsdl", $options);

            return $cliCobranca->consultaTitulo($params);
        }
    }
                                  
?>
