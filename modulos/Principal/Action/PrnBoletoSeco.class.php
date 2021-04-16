<?php
/**
* @author   Emanuel Pires Ferreira
* @email    epferreira@brq.com
* @since    28/05/2013
* */


//require_once (_MODULEDIR_ . 'Principal/DAO/PrnBoletoSecoDAO.class.php');
require_once _MODULEDIR_.'Principal/DAO/PrnBoletoSecoDAO.class.php';
require_once _MODULEDIR_.'Principal/Action/ServicoEnvioEmail.php';
require_once _MODULEDIR_.'core/module/RoteadorBoleto/RoteadorBoletoService.php';

use infra\Helper\Response;
use module\RoteadorBoleto\RoteadorBoletoService as RoteadorBoleto;

/**
 * Trata requisições do módulo principal para efetuar ações relacionadas aos Boletos
 */
class PrnBoletoSeco {
    
    /**
     * Fornece acesso aos dados necessarios para o módulo
     * @property prnBoletoSecoDAO
     */
    private $prnBoletoSecoDAO;
    
    /**
     * Construtor, configura acesso a dados e parâmetros iniciais do módulo
     */
    public function __construct() 
    {
        global $conn;
        
        $this->prnBoletoSecoDAO = new PrnBoletoSecoDAO($conn);
    }
    
    /**
     * STI 86986
     * @author marcelo.burkard.ext
     * @since 24/08/2017
     */
    public function gerarBoletoSeco($tituloTaxaInstalacao, $contrato = null, $tipo = null, $origem = null){
    	
    	if($tipo == 'titulos_oficiais' && $origem == 'CRON'){
    		
    		$registrar = RoteadorBoleto::registrarBoleto($tituloTaxaInstalacao, 'titulo');
    		
    	}else{
    		$registrar = RoteadorBoleto::registrarBoleto($tituloTaxaInstalacao, '');
    	}
    	
        if ($registrar->codigo != '0') {
            return $registrar;
        }

        try {
            $boletoHTML = RoteadorBoleto::getHtmlBoleto($tituloTaxaInstalacao, '');
            
            if ($boletoHTML == '') {
                $response = new Response();
                $response->setResult(false, 'CBR002', 'Ocorreu um erro durante o preenchimento do boleto seco');
                return $response;
            }
        } catch (\Exception $e) {
            $response = new Response();
            $response->setResult(false, 'CBR002', 'Ocorreu um erro durante o preenchimento do boleto seco');
            return $response;
        }
        
        try {
            	
           	$boletoTmp  = RoteadorBoleto::getArquivoBoleto($tituloTaxaInstalacao, '', $boletoHTML);
            	
        } catch (\Exception $e) {
            $response = new Response();
            $response->setResult(false, 'CBR003', 'Ocorreu um erro durante a criação do arquivo');
            return $response;
        }

        return $boletoTmp;
    }
    
    public function geraBoleto($dadosboleto, $codbarras)
    {
        // pega a global setada no config
        global $arrSistemas;
        
        $url = trim($arrSistemas['sascar']['URL'], '/'); // remove a barra

        if (strlen($url) == 0){
        	$url = _PROTOCOLO_ . "intranet.sascar.com.br/";
        }
        ob_start();
        
        include(_MODULEDIR_.'Principal/View/prn_boleto_seco/prn_boleto_seco.php');
        
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    public function geraCodigoBarras($CodBarras) {
    	$Bar[0] = "00110";
    	$Bar[1] = "10001";
    	$Bar[2] = "01001";
    	$Bar[3] = "11000";
    	$Bar[4] = "00101";
    	$Bar[5] = "10100";
    	$Bar[6] = "01100";
    	$Bar[7] = "00011" ;
    	$Bar[8] = "10010";
    	$Bar[9] = "01010";
    	
    	global $arrSistemas;
    	
    	$url = trim($arrSistemas['sascar']['URL'], '/'); // remove a barra
    	
    	$pathImg = "images/img_boleto/";
    	if (strlen($url) == 0){
    		$pathImg = _SITEDIR_ . 'images/img_boleto/';
    	}
    	
    	// Verifica se tem o caminho esta certo (procurando uma imagem)
    	$filename = $pathImg . "ptfin.gif";

		if (!file_exists($filename)) {
			$pathImg = _SITEDIR_ . 'images/img_boleto/';
		}
    	ob_start();
    	
    	// Inicio padrão do Código de Barras
    	echo "<img src={$pathImg}ptfin.gif>";
    	echo "<img src={$pathImg}brfin.gif>";
    	echo "<img src={$pathImg}ptfin.gif>";
    	echo "<img src={$pathImg}brfin.gif>";
    	
    	// Checando para saber se o conteúdo é impar e adicionando um zero se necessário
    	if ( $this->my_bcmod(strlen($CodBarras),2) <> 0)
    	{ $CodBarras = '0'.$CodBarras;}
    	
    	//Ecoando as imagens para montar o código de barras
    	for ($a = 0; $a < strlen($CodBarras); $a++){
    		$Preto = $CodBarras[$a];
    		$CodPreto = $Bar[$Preto];
    	
    		$a = $a+1; // Sabemos que o Branco é um depois do Preto...
    		$Branco = $CodBarras[$a];
    		$CodBranco = $Bar[$Branco];
    	
    		// Encontrado o CodPreto e o CodBranco vamos fazer outro looping dentro do nosso
    		for ($y = 0; $y < 5; $y++) { // O for vai pegar os binários
    	
    			if ($CodPreto[$y] == '0')
    			{ // Se o binario for preto e fino ecoa
    				echo "<img src={$pathImg}ptfin.gif>";
    			}
    	
    			if ($CodPreto[$y] == '1')
    			{ // Se o binario for preto e grosso ecoa
    				echo "<img src={$pathImg}ptgr.gif>";
    			}
    	
    			if ($CodBranco[$y] == '0')
    			{ // Se o binario for branco e fino ecoa
    				echo "<img src={$pathImg}brfin.gif>";
    			}
    	
    			if($CodBranco[$y] == '1')
    			{ // Se o binario for branco e grosso ecoa
    				echo "<img src={$pathImg}brgr.gif>";
    			}
    		}
    	
    	}
    	
    	// Final padrão do Codigo de Barras
    	
    	echo "<img src={$pathImg}ptgr.gif>";
    	echo "<img src={$pathImg}brfin.gif>";
    	echo "<img src={$pathImg}ptfin.gif>";
    	
    	$barras = ob_get_contents();
    	ob_end_clean();
    	
    	return $barras;
    }
    
    public function my_bcmod( $x, $y ) {
    
    	$take = 5;
    	$mod = '';
    
    	do {
    		$a = (int)$mod.substr( $x, 0, $take );
    		$x = substr( $x, $take );
    		$mod = $a % $y;
    	}
    	while ( strlen($x) );
    
    	return (int)$mod;
    }
    
    public function esquerda($entra,$comp){
        return substr($entra,0,$comp);
    }
    
    public function direita($entra,$comp){
        return substr($entra,strlen($entra)-$comp,$comp);
    }
    
    function modulo_11($num, $base=9, $r=0)  {
        $soma = 0;
        $fator = 2;
    
        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2 
                $fator = 1;
            }
            $fator++;
        }
    
        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            return $digito;
        } elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        }
    }
    
    public function geraCodigoBanco($numero) {
        $parte1 = substr($numero, 0, 3);
        $parte2 = $this->modulo_11($parte1);
        return $parte1 . "-" . $parte2;
    }
    
    public function fator_vencimento($data) {
        $data = explode("/",$data);
        $ano = $data[2];
        $mes = $data[1];
        $dia = $data[0];
        return(abs(($this->_dateToDays("1997","10","07")) - ($this->_dateToDays($ano, $mes, $dia))));
    }
    
    public function _dateToDays($year,$month,$day) {
        $century = substr($year, 0, 2);
        $year = substr($year, 2, 2);
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }
        return ( floor((  146097 * $century)    /  4 ) +
                floor(( 1461 * $year)        /  4 ) +
                floor(( 153 * $month +  2) /  5 ) +
                    $day +  1721119);
    }
    
    public function formata_numero($numero,$loop,$insert,$tipo = "geral") {
        if ($tipo == "geral") {
            $numero = str_replace(",","",$numero);
            while(strlen($numero)<$loop){
                $numero = $insert . $numero;
            }
        }
        if ($tipo == "valor") {
            /*
            retira as virgulas
            formata o numero
            preenche com zeros
            */
            $numero = str_replace(",","",$numero);
            while(strlen($numero)<$loop){
                $numero = $insert . $numero;
            }
        }
        if ($tipo == "convenio") {
            while(strlen($numero)<$loop){
                $numero = $numero . $insert;
            }
        }
        return $numero;
    }
    
    public function dataJuliano($data) 
    {
        $dia = (int)substr($data,1,2);
        $mes = (int)substr($data,3,2);
        $ano = (int)substr($data,6,4);
        $dataf = strtotime("$ano/$mes/$dia");
        $datai = strtotime(($ano-1).'/12/31');
        $dias  = (int)(($dataf - $datai)/(60*60*24));
        return str_pad($dias,3,'0',STR_PAD_LEFT).substr($data,9,4);
    }
    
    public function digitoVerificador_barra($numero) {
        $resto2 = $this->modulo_11($numero, 9, 1);
         if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
            $dv = 1;
         } else {
            $dv = 11 - $resto2;
         }
         return $dv;
    }
    
    public function monta_linha_digitavel($codigo) 
    { 
        $campo1 = substr($codigo,0,3) . substr($codigo,3,1) . substr($codigo,19,1) . substr($codigo,20,4);
        $campo1 = $campo1 . $this->modulo_10($campo1);
        $campo1 = substr($campo1, 0, 5).'.'.substr($campo1, 5);
    
        $campo2 = substr($codigo,24,10);
        $campo2 = $campo2 . $this->modulo_10($campo2);
        $campo2 = substr($campo2, 0, 5).'.'.substr($campo2, 5);
    
        $campo3 = substr($codigo,34,10);
        $campo3 = $campo3 . $this->modulo_10($campo3);
        $campo3 = substr($campo3, 0, 5).'.'.substr($campo3, 5);
    
        $campo4 = substr($codigo, 4, 1);
    
        $campo5 = substr($codigo, 5, 4) . substr($codigo, 9, 10);
        
        return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
    }
    
    public function modulo_10($num) { 
        $numtotal10 = 0;
        $fator = 2;
    
        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo (falor 10)
            // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
            $temp = $numeros[$i] * $fator; 
            $temp0=0;
            foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }
        
        // várias linhas removidas, vide função original
        // Calculo do modulo 10
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }
        
        return $digito;
    }

    public function geraNossoNumero($ndoc,$cedente,$venc,$tipoid) {
        $ndoc = $ndoc.$this->modulo_11_invertido($ndoc).$tipoid;
        $venc = substr($venc,0,2).substr($venc,3,2).substr($venc,8,2);
        $res = $ndoc + $cedente + $venc;
        return $ndoc . $this->modulo_11_invertido($res);
    }
    
    public function modulo_11_invertido($num)  { // Calculo de Modulo 11 "Invertido" (com pesos de 9 a 2  e não de 2 a 9)
        $ftini = 2;
            $ftfim = 9;
            $fator = $ftfim;
        $soma = 0;
        
        for ($i = strlen($num); $i > 0; $i--) {
                $soma += substr($num,$i-1,1) * $fator;
                if(--$fator < $ftini) $fator = $ftfim;
        }
        
        $digito = $soma % 11;
            if($digito > 9) $digito = 0;
        
            return $digito;
    }
    
    public function recuperaDadosFatura($titoid, $tipo)
    {
        return $this->prnBoletoSecoDAO->recuperaDadosFatura($titoid, $tipo);
    }
    
    public function recuperaDadosProposta($contrato)
    {
    	return $this->prnBoletoSecoDAO->recuperaDadosProposta($contrato);
    }
    
    public function isUtil($data)
    {
        list($dia, $mes, $ano) = explode("/",$data);
        $hoje= mktime(0,0,0,$mes,$dia,$ano);
        $next = mktime(0,0,0,$mes,$dia+1,$ano);
        
        $conta = 0;
        
        $conta+= $this->isFds($data);
        $conta+= $this->isFeriado($data);
        $conta+= $this->isCarnaval($data);
        $conta+= $this->isCorpusChristi($data);
        $conta+= $this->isSextaFeiraSanta($data);
        
        return ($conta > 0) ? $this->isUtil(date('d/m/Y',$next)) : $data;  
    }    
    
    public function dataPascoa($ano=false, $form="d/m/Y") {
        $ano=$ano?$ano:date("Y");
        $A = ($ano % 19);
        $B = (int)($ano / 100);
        $C = ($ano % 100);
        $D = (int)($B / 4);
        $E = ($B % 4);
        $F = (int)(($B + 8) / 25);
        $G = (int)(($B - $F + 1) / 3);
        $H = ((19 * $A + $B - $D - $G + 15) % 30);
        $I = (int)($C / 4);
        $K = ($C % 4);
        $L = ((32 + 2 * $E + 2 * $I - $H - $K) % 7);
        $M = (int)(($A + 11 * $H + 22 * $L) / 451);
        $P = (int)(($H + $L - 7 * $M + 114) / 31);
        $Q = (($H + $L - 7 * $M + 114) % 31) + 1;
        return date($form, mktime(0,0,0,$P,$Q,$ano));
    }
    
    
    public function dataCarnaval($ano=false, $form="d/m/Y") 
    {
        $ano=$ano?$ano:date("Y");
        $a=explode("/", $this->dataPascoa($ano));
        return date($form, mktime(0,0,0,$a[1],$a[0]-47,$a[2]));
    }
    
    public function dataCorpusChristi($ano=false, $form="d/m/Y") 
    {
            $ano=$ano?$ano:date("Y");
            $a=explode("/", $this->dataPascoa($ano));
            return date($form, mktime(0,0,0,$a[1],$a[0]+60,$a[2]));
    }
    
    public function dataSextaSanta($ano=false, $form="d/m/Y") 
    {
            $ano=$ano?$ano:date("Y");
            $a=explode("/", $this->dataPascoa($ano));
            return date($form, mktime(0,0,0,$a[1],$a[0]-2,$a[2]));
    } 
    
    public function isFds($data) 
    {
        list($dia, $mes, $ano) = explode("/",$data);
        
        return (in_array(date('w',mktime(0,0,0,$mes,$dia,$ano)),array(0,6)))?1:0;
    }
  
    public function isFeriado($data) 
    {
        $feriadosDia = array(
            '01/01/'.date('Y'),
            '21/04/'.date('Y'),
            '01/05/'.date('Y'),
            '07/09/'.date('Y'),
            '12/10/'.date('Y'),
            '02/11/'.date('Y'),
            '15/11/'.date('Y'),
            '25/12/'.date('Y')
        );
        
        return (in_array($data, $feriadosDia))?1:0; 
    }

    public function isCarnaval($data) {
        return ($data == $this->dataCarnaval(date('Y')))?1:0;
    }
    
    public function isCorpusChristi($data) {
        return ($data == $this->dataCorpusChristi(date('Y')))?1:0;
    }
    
    public function isSextaFeiraSanta($data) {
        return ($data == $this->dataSextaSanta(date('Y')))?1:0;
    }
    
    public function recuperaLayout($dados)
    {
    	if ($dados->tipoLayout == "PARCELADO"){
    		return $this->prnBoletoSecoDAO->recuperaLayoutParcelamento($dados);
    	}else{
    		return $this->prnBoletoSecoDAO->recuperaLayout($dados);
    	}
    }
    
    public function verificaStatusBoleto($contrato)
    {
    	return $this->prnBoletoSecoDAO->verificaStatusBoleto($contrato);
    }
    
    public function boletoEnviado($tituloTaxaInstalacao)
    {
    	return $this->prnBoletoSecoDAO->boletoEnviado($tituloTaxaInstalacao);
    }
    
    public function parcelasEnviadas($tituloParcelas)
    {
    	return $this->prnBoletoSecoDAO->parcelasEnviadas($tituloParcelas);
    }
    
    public function dadosProposta($prpoid)
    {
    	return $this->prnBoletoSecoDAO->dadosProposta($prpoid);
    }
    
    public function geraArquivo($html, $tituloTaxaInstalacao)
    {
    	$html = utf8_encode($html);
    	    	
    	//Gerar nome para o arquivo
    	$name = "B_" . date('d-m-Y_his');
    	$name .= "_T_". md5($tituloTaxaInstalacao);
    	$name .= ".pdf";
    	
    	$arquivo = _BOLETOTMPDIR_ . $name;
    	    	
    	$this->removeArquivo($arquivo);
    	
    	// Converte o boleto para PDF
    	require_once(_SITEDIR_.'lib/html2pdf/html2pdf.class.php');
    	$html2pdf = new HTML2PDF('P','A4','en');
    	$html2pdf->WriteHTML($html);
    	
    	// Salva na pasta
    	$html2pdf->Output($arquivo, "F");

        return $arquivo;
    }
    
    public function removeArquivo($arquivos)
    {
    	// Se for um array de arquivos
    	if (is_array($arquivos)){
    		foreach ($arquivos AS $key => $arquivo){
    			if (is_file($arquivo)){
    				unlink($arquivo);
    			}
    		}
    	}else {
    		// Verifica se arquivo existe no caminho informado, Se sim -> Adiciona Anexo
    		if (is_file($arquivos)){
    			unlink($arquivos);
    		}
    	}
    	
        return true;
    }
    
    
	public function enviarBoleto($prpoid, $tituloTaxaInstalacao, $arquivoBoleto) {
    	
    	// Verifica se o titulo é boleto
    	$boleto = $this->prnBoletoSecoDAO->verificaIsBoleto($tituloTaxaInstalacao);
    	
    	if ($boleto){
	    	$dadosTitulo  = $this->recuperaDadosFatura($tituloTaxaInstalacao, "boleto_seco");
	    	
	    	if ($_SESSION['servidor_teste'] == 1) {
	    		$dadosTitulo['cliemail'] = 'teste_desenv@sascar.com.br';
	    	}
	    	
	    	
	    	$prpoid = explode(",", $prpoid);
	    	if (is_array($prpoid)){
	    		$prpoid = $prpoid[0];
	    	}
	    	
	    	$dadosContratoLayout = $this->dadosProposta($prpoid);
	    	$layout = $this->recuperaLayout($dadosContratoLayout);
	    	
	    	$email_dest = $dadosTitulo['cliemail'];
	    	$assunto    = $layout['assunto'];
	    	$corpo_email = $layout['html'];
	    	
	    	$email_copia = null;
	    	$email_copia_oculta = null;
	    	$servidor = $layout['server'];
	    	$email_desenv = 'teste_desenv@sascar.com.br';
	    	
	    	$servicoEmail	 = new ServicoEnvioEmail();
	    	
	    	$envio = $servicoEmail->enviarEmail($email_dest,
	    			$assunto,
	    			$corpo_email,
	    			$arquivoBoleto,
	    			$email_copia,
	    			$email_copia_oculta,
	    			$servidor,
	    			$email_desenv);
	    	
	    	
	    	//remove arquivo temporario
	    	$this->removeArquivo($arquivoBoleto);
	    	
	    	$this->boletoEnviado($tituloTaxaInstalacao);
	    }
    }
    
    
    public function enviarParcelas($prpoid, $tituloParcela, $arquivoBoleto) {
    	 
    	// Verifica se o titulo é uma parcela
    	$boleto = $this->prnBoletoSecoDAO->verificaIsParcela($tituloParcela);
    	 
    	if ($boleto){
    		$dadosTitulo  = $this->recuperaDadosFatura($tituloParcela, "titulos_oficiais");
    
    		if ($_SESSION['servidor_teste'] == 1) {
    			$dadosTitulo['cliemail'] = 'teste_desenv@sascar.com.br';
    		}
    
    		$prpoid = explode(",", $prpoid);
    		if (is_array($prpoid)){
    			$prpoid = $prpoid[0];
    		}
    
    		$dadosContratoLayout = $this->dadosProposta($prpoid);
    		
    		// Busca o layout referente ao parcelamento
    		$dadosContratoLayout->tipoLayout = "PARCELADO";
    		$layout = $this->recuperaLayout($dadosContratoLayout);
    
    		// Se não encontrar o layout do parcelamento, busca o layout padrão
    		if (empty($layout)){
    		
    			unset ($dadosContratoLayout->tipoLayout);
    			$layout = $this->recuperaLayout($dadosContratoLayout);
    		}
    		 
    		$email_dest = $dadosTitulo['cliemail'];
    		$assunto    = $layout['assunto'];
    		$corpo_email = $layout['html'];
    
    		$email_copia = null;
    		$email_copia_oculta = null;
    		$servidor = $layout['server'];
    		$email_desenv = 'teste_desenv@sascar.com.br';
    
    		$servicoEmail	 = new ServicoEnvioEmail();
    
    		$envio = $servicoEmail->enviarEmail($email_dest,
    				$assunto,
    				$corpo_email,
    				$arquivoBoleto,
    				$email_copia,
    				$email_copia_oculta,
    				$servidor,
    				$email_desenv);
    		
    		//remove arquivo temporario
    		$this->removeArquivo($arquivoBoleto);
    
    		return $envio;
    	}
    }
}