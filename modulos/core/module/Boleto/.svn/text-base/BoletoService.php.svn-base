<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
 * @version 09/09/2016
 * @since 09/09/2016
 * @package Core
 * @subpackage Classe Core de Boleto
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\Boleto;

use module\Boleto\BoletoController;

class BoletoService{
    /**
     * Método de busca de dados de boleto
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 09/09/2016
     * @param array $params Array com os dados para gerar o boleto         
                array(
                    'cod' => 84, //ID da Forma de cobrança (registrado=true) ou Código do banco (registrado=false)
                    'registrado' => true/false,
                    'clioid' => 307743,
                    'dataVencimento' => date('Y-m-d'), //Susbtituir date('Y-m-d') pela data de vencimento no formato 'Y-m-d'
                    'valor' => 59.90, //Valor do documento
                    'sequencial' => 12345678901, // Até 13 dígitos - Numero do título
                    'numeroDocumento' => '',
                    'carteira' => 102,
                    'instrucoes' => array( // Até 8
                        'Ap&oacute;s o vencimento cobrar 2% de multa e 0.033% de juros ao dia.'
                    ),
                    'ios' => 0, // Apenas para o Santander; IOS – Seguradoras (Se 7% informar 7. Limitado a 9%); Demais clientes usar 0 (zero)
                )
     *
     * @param string $banco Nome do banco (minusculo, sem espaço. Ex: santander; bancodobrasil; hsbc; itau; caixa;)
     * @return Response $response:
     *     mixed $response->dados (HTML do boleto = OK / false = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
    */
    public static function gerarBoleto($params=array(), $banco=''){
        $boleto = new BoletoController();
        return $boleto->gerarBoleto($params, $banco);
    }
    
    /**
     * Retorna true/false se o boleto está registrado no banco.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 13/09/2016
     * @param int $titulo Número do título
     * @param string $tipo Define em qual tabela realiza a consulta - Tipo do titulo: titulo; consolidado; retencao;
     * @return Response $response:
     *     mixed  $response->dados (true/false = OK / '' = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public static function consultarRegistroBoleto($titulo=0, $tipo='titulo'){
        $boleto = new BoletoController();
        return $boleto->consultarRegistroBoleto($titulo, $tipo);
    }
    
    /**
     * Retorna o "Nosso Número" conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 20/09/2016
     * @param array $array Array com os dados para o nosso número
     * @param int $cod_banco Código do banco (Santander: 033)
     * @return Response $response:
     *     mixed  $response->dados (string = OK / '' = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public static function getNossoNumero($array=array(), $cod_banco=0){
        $boleto = new BoletoController();
        return $boleto->getNossoNumero($array, $cod_banco);
    }
    
    /**
     * Retorna a "Linha Digitável" conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 20/09/2016
     * @param array $params Array com os dados para a linha digitável
     * @param int $cod_banco Código do banco (Santander: 33)
     * @return Response $response:
     *     mixed  $response->dados (string = OK / '' = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public static function getLinhaDigitavel($params=array(), $cod_banco=0){
        $boleto = new BoletoController();
        return $boleto->getLinhaDigitavel($params, $cod_banco);
    }
    
    /**
     * Retorna a "Código de barras" conforme o banco informado.
     *
     * @author Bruno Bonfim Affonso <bruno.bonfim@sascar.com.br>
     * @version 07/10/2016
     * @param array $params Array com os dados do código de barras
     * @param int $cod_banco Código do banco (Santander: 33)
     * @return Response $response:
     *     mixed  $response->dados (string = OK / '' = falha)
     *     string $response->codigo (código do erro)
     *     string $response->mensagem (mensagem emitida)
     */
    public static function getCodigoBarras($params=array(), $cod_banco=0){
        $boleto = new BoletoController();
        return $boleto->getCodigoBarras($params, $cod_banco);
    }

    /**
     * Retorna a data de vencimento de um boleto
     * @return string
     */
    public static function getDataVencimento($titoid){
        $boleto = new BoletoController();
        return $boleto->getDataVencimento($titoid);
    }
    
    /**
     * Retorna os prazso estipulados pela Febraban com o limite de valor e datas para registro de boletos
     * @return array|\infra\Array
     */
    public static function getPrazosFebraban($valor,$dtEmissao){
    	$boleto = new BoletoController();
    	return $boleto->getPrazosFebraban($valor,$dtEmissao);
    }
    
    /**
     * Verifica se o id de título fornecido é de retenção (boleto seco).
     * @return bool
     */
    public static function isBoletoSeco($titoid) {
        $boleto = new BoletoController();
        return $boleto->isBoletoSeco($titoid);
    }

    /**
     * Retorna a mensagem de instrução do boleto.
     * @return string
     */
    public static function getInstrucoes($isBoletoSeco = false, $dataExpiracao = null)
    {
        $boleto = new BoletoController();
        return $boleto->getInstrucoes($isBoletoSeco, $dataExpiracao);
    }

    /**
     * Retorna a forma de registro do Boleto no banco (XML ou CNAB)
     * @return string
     */
    public static function getformaRegistro($titoid) {
        $boleto = new BoletoController();
        return $boleto->getformaRegistro($titoid);
    }
    
	/**
	 * Retorna o nome da tabela em que o título se encontra 
	 * 
	 * @param unknown $titoid
	 * @return string
	 */
    public static function getTabelaTitulo($titoid) {
    	$boleto = new BoletoController();
    	return $boleto->getTabelaTitulo($titoid);
    }
}