<?php

require_once _MODULEDIR_ . 'Cron/DAO/VIVORetornoInstalacaoDAO.php';

/**
 * Classe responsável pelas regras de negócio
 *
 * @package VIVORetornoInstalacao
 * @since   24/09/2013
 * 
 */
class VIVORetornoInstalacao {

    /**
     * Objeto DAO.
     *
     * @var stdClass
     */   
    private $dao;
    
    /**
     * Atribui Arquivo XML contendo dados da linha e motivo cancelamento
     * 
     * @return string $xml 
     */
    public function geraXmlAtualizaPedido() {

        $ordemServicoSemRetorno = array();

        $retornoInstalacao = array();

        $ordemServicoSemRetorno = $this->dao->buscarOrdemServicoSemRetorno();
     
        $pedidos = array();
        $pedidoAnterior = "";
        $i = 0;
        foreach ($ordemServicoSemRetorno as $pedido) {

            if (trim($pedido['numeroPedido']) != trim($pedidoAnterior) && !isset($pedidos[$pedido['numeroPedido']]) ) {
                
            	$pedidos[$pedido['numeroPedido']]['numero'] = $pedido['numeroPedido'];

                $pedidos[$pedido['numeroPedido']]['xml'] = "<ped:pedido>
                                        <ped:numeroPedido>".$pedido['numeroPedido']."</ped:numeroPedido>
                                        <ped:versao>".$pedido['versao']."</ped:versao>
                                        <ped:status>OK</ped:status>
                                        <ped:itemPedido><!--1 or more repetitions:-->";

                foreach ($ordemServicoSemRetorno as $itemPedido) {
                    if (trim($pedido['numeroPedido']) == trim($itemPedido['numeroPedido'])) {
                        $descricaoStatus = trim($itemPedido['status']) == 'NOK' ? '999 - ' . utf8_encode($itemPedido['descricaoStatus']) : '';
                        $pedidos[$pedido['numeroPedido']]['xml'] .= "<ped:itensPedido>
                                                    <ped:numeroLinha>".$itemPedido['numeroLinha']."</ped:numeroLinha>
                                                    <ped:status>".$itemPedido['status']."</ped:status>
                                                    <!--Optional:-->
                                                    <ped:descricaoStatus>".$descricaoStatus."</ped:descricaoStatus>
                                                </ped:itensPedido>";
                    }

                    $pedidos[$pedido['numeroPedido']]['idRetorno'][$i]        = $itemPedido['idRetorno'];
                    $pedidos[$pedido['numeroPedido']]['ordStatusRetorno'][$i] = $itemPedido['ordStatusRetorno'] == 9 ? 'f' : 't';
                    $i++;
                }

                $pedidos[$pedido['numeroPedido']]['xml'] .= "</ped:itemPedido></ped:pedido>";
                $pedidos[$pedido['numeroPedido']]['xml'] = utf8_decode($pedidos[$pedido['numeroPedido']]['xml']);
                $pedidoAnterior = trim($pedido['numeroPedido']);
            }
        }
        
        return $pedidos;
        
    }  
    
    /**
     * Método que faz a chama para o WS da Vivo, através da extensão CURL
     * ele traz todo os dados a serem gravados do pedido
     * 
     * @param string $xml = > Xml com dados para envio
     *  
     * @return boolean True or False
     */
    public function enviaWSVIVOAtualizaPedido($xml) {

        if( $_SESSION['servidor_teste'] == 1){
			$url = "https://etahml.vivo.com.br/SASCAR/AtualizarPedido";
		} else {
			$url = "https://integracao.vivo.com.br/SASCAR/AtualizarPedido";
		}
	
	   $body = '
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ger="http://www.vivo.com.br/MC/Geral" xmlns:ped="http://www.vivo.com.br/SN/PedidoSasCar">
           <soap:Header xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
              <wsse:Security soap1:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:soap1="soapenv">
                 <wsse:UsernameToken wsu:Id="UsernameToken-157788905" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                    <wsse:Username>Sascar</wsse:Username>
                    <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">Vivos@scar2013</wsse:Password>
                 </wsse:UsernameToken>
              </wsse:Security>
              <ger:cabecalhoVivo>
                <ger:loginUsuario>Sascar</ger:loginUsuario>
                <ger:nomeAplicacao>Sascar</ger:nomeAplicacao>
                <ger:nomeServico>PedidoSasCar</ger:nomeServico>
              </ger:cabecalhoVivo>
           </soap:Header>
		   <soapenv:Body>
			  <ped:atualizarPedidoRequest>
				 '.$xml.'
			  </ped:atualizarPedidoRequest>
			</soapenv:Body>
        </soapenv:Envelope>
		';
       
	    $headers = array(
	            'Content-Type: text/xml; charset="utf-8"',
	            'Content-Length: ' . strlen($body),
	            'Accept: text/xml',
	            'Cache-Control: no-cache',
	            'Pragma: no-cache',
	            'SOAPAction: "http://www.vivo.com.br/SN/PedidoSasCar/atualizarPedidoRequest"'
	    );

        echo "<br/>Requisição enviada<br/>";
        echo "<pre>";
            print_r(htmlspecialchars($body));
        echo "</pre>";
        echo "<br/>Fim do texto de requisição enviada<br/><br/>";
	
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
	    // Stuff I have added
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    //curl_setopt($ch, CURLOPT_USERPWD, $credentials);
	
	    $data = curl_exec($ch);
	
	    if (!$data) {
	        echo '<br/> <b>ERRO CURL:</b> ' . curl_error($ch) . '<br/>';
	        return false;
	    }
	
	    curl_close($ch);        
	
	    echo "<br/>Retorno da requisição<br/>";
		echo "<pre>";print_r(htmlspecialchars($data));echo "</pre>";
	    echo "<br/>Fim do retorno da requisição<br/><br/>";
	    

        // quando é um pedido de sucesso é enviado elementos com namespace 'ped', caso ao contrario retorna falso.
        if (strpos($data, 'ped:pedido') === false) {
            return false;
        }

        $xml = simplexml_load_string($data);
        $xml->registerXPathNamespace('ped', 'http://www.vivo.com.br/SN/PedidoSasCar');
        $pedido = $xml->xpath('//ped:atualizarPedidoResponse');
        $pedido = $pedido[0]->xpath('//ped:pedido');
        $codigoRetorno = $pedido[0]->xpath('//ped:codigoErro');
        $descricaoRetorno = $pedido[0]->xpath('//ped:descricaoErro');
        $codigoRetorno = (string) $codigoRetorno[0];
        $descricaoRetorno = (string) $descricaoRetorno[0];

        if ($codigoRetorno == '0' && strtolower($descricaoRetorno) == 'sucesso') {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Método que chama DAO para atualizar retorno nstalação após chamada do WS VIVO Atualiza Pedido
     * 
     * @param array $envio => Numero do pedido VIVO e Status Retorno Instalação (Instalado = TRUE ou Cancelado = FALSE)
     *  
     * @return boolean
     */
    public function atualizaRetornoInstalacao($idRetorno, $statusRetorno) {
        $this->dao->atualizaRetornoInstalacao($idRetorno,$statusRetorno);
        return true;
    }
    
    /**
     * Metodo Construtor
     */
    public function __construct() {        
    	$this->dao = new VIVORetornoInstalacaoDAO();
    }

}