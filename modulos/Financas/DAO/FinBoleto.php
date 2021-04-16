<?php
require _SITEDIR_ . 'boleto_funcoes.php';

/**
 * FinBoleto.php
 */
class FinBoletoTest {
	public function __construct() {
		
		try {
			
			$banco = FinBoletoBancoFactory::factory('hsbc');
			
			$boleto = new FinBoleto($banco);
			$boleto->generate();
			
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
	}
}

class FinBoleto {
	/*
	 * @var FinBoletoBanco
	 */
	private $banco;
	
	/*
	 * @var FinBoletoVo
	*/
	private $vo;
	
	/**
	 * @param string $banco Nome do banco
	 */
	public function __construct($banco) {
		$this->banco = FinBoletoBancoFactory::factory($banco);
	}
	
	/**
	 * Gera as informações necessárias para impressão do boleto
	 * 
	 * @return FinBoletoVo
	 */
	public function generate($numeroDocumento, $vencimento, $cedente, $parcela) {
		$this->vo = new FinBoletoVo();
		
		$numeroDocumento = (int)$numeroDocumento;
		
		//$this->banco->alterarCobranca($numeroDocumento);
		$this->vo->nossoNumero = $this->banco->nossoNumero($numeroDocumento, $vencimento, $cedente);
		$this->vo->nossoNumeroDv = $this->banco->nossoNumeroDv($numeroDocumento, $vencimento, $cedente);
		$this->vo->linhaDigitavel = $this->banco->linhaDigitavel($numeroDocumento, $vencimento, $cedente, $parcela);
		$this->vo->codigoBarras = $this->banco->codigoBarras($numeroDocumento, $vencimento, $cedente, $parcela);
		
		return $this->vo;
	}
	
	/**
	 * Imprime o boleto de acordo com as informações geradas
	 * 
	 * return void
	 */
	public function show() {
		throw new Exception('implements show...');
	}
}

class FinBoletoVo {
	public $nossoNumero;
	public $nossoNumeroDv;
	public $linhaDigitavel;
	public $codigoBarras;
}

abstract class FinBoletoBancoFactory {
	/**
	 * @return FinBoletoBanco
	 */
	public static function factory($banco) {
		switch ($banco) {
			case 'hsbc':
				return new FinBoletoHsbc();
			break;
			case 'itau':
				return new FinBoletoItau();
			break;
		}
		
		throw new Exception('Banco não possui boleto implementado: '.$banco);
	}
}

class FinBoletoHsbc implements FinBoletoBanco {
	
	public function alterarCobranca($numeroDocumento){
		global $conn;
		
		$sqlAlteraTitulo = '';
		$numeroDocumento = (int)$numeroDocumento;
		
		if(!empty($numeroDocumento)){
			$sqlAlteraTitulo = "UPDATE titulo SET titformacobranca = 74 WHERE titoid = " . $numeroDocumento;
		}
		
		if ($sqlAlteraTitulo == '' || !$rsAlteraTitulo = pg_query($conn, $sqlAlteraTitulo)) {
			throw new Exception('Erro ao alterar forma de cobrança HSBC do título: ' . $numeroDocumento);
		}
	}

	public function nossoNumero($numeroDocumento, $vencimento, $cedente) {
		return montaNossoNumeroHSBC($numeroDocumento, $vencimento, $cedente);
	}
	
	public function nossoNumeroDv($numeroDocumento, $vencimento, $cedente) {
		return $this->nossoNumero($numeroDocumento, $vencimento, $cedente);
	}
	
	public function linhaDigitavel($numeroDocumento, $vencimento, $cedente, $parcela){
		return montaLinhaDigitavelCodBarrasHSBC($numeroDocumento, $vencimento, $cedente, $parcela, 'linha_digitavel');
	}
	
	public function codigoBarras($numeroDocumento, $vencimento, $cedente, $parcela){
		return montaLinhaDigitavelCodBarrasHSBC($numeroDocumento, $vencimento, $cedente, $parcela, 'codigo_barras');
	}
}

class FinBoletoItau implements FinBoletoBanco {
	private $carteira = 109;
	
	public function alterarCobranca($numeroDocumento){
		global $conn;
		
		$sqlAlteraTitulo = '';
		$numeroDocumento = (int)$numeroDocumento;
		
		if(!empty($numeroDocumento)){
		
			$sqlAlteraTitulo = "UPDATE titulo SET titformacobranca = 73 WHERE titoid = " . $numeroDocumento;
		}


		if ($sqlAlteraTitulo == '' || !$rsAlteraTitulo = pg_query($conn, $sqlAlteraTitulo)) {
			throw new Exception('Erro ao alterar forma de cobrança Itaú do título: ' . $numeroDocumento);
		}
	}
	
	public function nossoNumero($numeroDocumento, $vencimento, $cedente) {
		return (70000000 + $numeroDocumento);
	}
	
	public function nossoNumeroDv($numeroDocumento, $vencimento, $cedente) {
		$dadosTitulo = $this->dadosTitulo($numeroDocumento, 73);
		
		$nossoNumeroDv = $this->nossoNumero($numeroDocumento, $vencimento, $cedente);
		
		if($this->carteira == 109) {
			$nossoNumeroDv = $this->carteira.'/'.$nossoNumeroDv.'-'.modulo_10($dadosTitulo->cfbagencia.$dadosTitulo->cfbconta_corrente.$dadosTitulo->carteira.$nossoNumeroDv);
		}
		
		return $nossoNumeroDv;
	}
	
	public function linhaDigitavel($numeroDocumento, $vencimento, $cedente, $parcela){
		$codigoBarras = $this->codigoBarras($numeroDocumento, $vencimento, $cedente, $parcela);
		
		return montaLinhaDigitavelItau($codigoBarras);
	}
	
	public function codigoBarras($numeroDocumento, $vencimento, $cedente, $parcela){
		$dadosTitulo = $this->dadosTitulo($numeroDocumento);
		$nossoNumero = $this->nossoNumero($numeroDocumento, $vencimento, $cedente);
		
        return gerarCodigoBarrasItau('341',
					        		$dadosTitulo->cfbagencia,
					        		$dadosTitulo->cfbconta_corrente,
					        		$dadosTitulo->digito_conta,
					        		$dadosTitulo->carteira,
					        		$dadosTitulo->titdt_vencimento,
					        		$dadosTitulo->nTotal,
					        		$nossoNumero);
	}
	
	private function dadosTitulo($titoid) {
		global $conn;
		
		$sqlBuscaTitulo = "
			SELECT
	            '109' AS carteira, 
				CASE WHEN forccfbbanco='341' THEN cfbagencia_convenio ELSE cfbagencia END AS cfbagencia,
	            CASE WHEN forccfbbanco='341' THEN cfbconta_corrente_convenio ELSE cfbconta_corrente END AS cfbconta_corrente,
				forccfbbanco,
				(CASE WHEN clitipo = 'F' THEN clino_cpf ELSE clino_cgc END) AS cpf_cnpj,
				COALESCE(titvl_ir,0) AS titvl_ir, 
				COALESCE(titvl_iss,0) AS titvl_iss, 
				COALESCE(titvl_piscofins,0) AS titvl_piscofins, 
				COALESCE(titvl_desconto,0) AS titvl_desconto,
				COALESCE(titvl_titulo,0) AS titvl_titulo,
				COALESCE(titvl_acrescimo,0) AS titvl_acrescimo,
				titdt_vencimento
			FROM
				clientes
				INNER JOIN titulo 				ON titclioid 		= clioid
				INNER JOIN forma_cobranca 		ON titformacobranca = forcoid
				INNER JOIN config_banco 		ON cfbbanco			= forccfbbanco
				INNER JOIN nota_fiscal 			ON titnfloid		= nfloid
				INNER JOIN endereco 			ON cliend_cobr		= endoid
				LEFT JOIN motivo_inadimplente 	ON titmotioid 		= motioid
			WHERE
				titoid = " . $titoid;
        
        
        echo "<pre>";
            print_r($sqlBuscaTitulo);
        echo "</pre>";
        
        
		
		$rsBuscaTitulo = pg_query($conn, $sqlBuscaTitulo);
        
        echo "<pre>";
            print_r($rsBuscaTitulo);
        echo "</pre>";
        
        exit;
        
		if (!$rsBuscaTitulo || pg_num_rows($rsBuscaTitulo) == 0) {
			$sqlBuscaNf = " SELECT 
								nflno_numero, nflserie, clinome
							FROM
								titulo
								INNER JOIN nota_fiscal ON titnfloid = nfloid
								INNER JOIN clientes ON clioid = nflclioid
							WHERE
								titoid = " . $titoid;
			
			$rsBuscaNf = pg_query($conn, $sqlBuscaNf);
			
			$resul = pg_fetch_object($rsBuscaNf);
			
			throw new Exception('Erro ao buscar dados da nota fiscal: ' . $resul->nflno_numero . '/' . $resul->nflserie . ', cliente: ' . $resul->clinome);
		}
		
		$resul = pg_fetch_object($rsBuscaTitulo);
		
		$resul->nTotal = $resul->titvl_titulo + $resul->titvl_acrescimo - $resul->titvl_desconto - $resul->titvl_iss - $resul->titvl_ir - $resul->titvl_piscofins;
		
		$resul->digito_conta = '';
		
		if ($resul->forccfbbanco == '341') {
			if (strpos($resul->cfbconta_corrente,"-") !== false) { 
				$posicao_digito = strrpos( $resul->cfbconta_corrente,"-" );
                $resul->digito_conta = substr( $resul->cfbconta_corrente,$posicao_digito + 1,strlen( $resul->cfbconta_corrente ) - $posicao_digito );
                $resul->cfbconta_corrente = substr( $resul->cfbconta_corrente,0,$posicao_digito );
    
                if (strlen($resul->cfbconta_corrente) > 5) {
                	throw new exception("Conta não pode ultrapassar 5 caracteres: $resul->cfbconta_corrente");
                }
            }                                                
        }
		
		return $resul;
	}
}

interface FinBoletoBanco {
	public function nossoNumero($numeroDocumento, $vencimento, $cedente);
	public function linhaDigitavel($numeroDocumento, $vencimento, $cedente, $parcela);
	public function codigoBarras($numeroDocumento, $vencimento, $cedente, $parcela);
}
