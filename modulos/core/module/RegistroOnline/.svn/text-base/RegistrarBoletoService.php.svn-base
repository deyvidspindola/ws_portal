<?

namespace module\RegistroOnline;

use module\RegistroOnline\RegistrarBoletoController as Controlador;
use infra\Helper\Response as Response;

define('__local_cert', _MODULEDIR_."core/module/RegistroOnline/Certificado/sascar.com.br.2017.pem");
define('__nosso_numero', '0000000000000');

class RegistrarBoletoService {

    /**
     * Método para Registrar Titulo do Boleto
     *
     * @author Dimitrius T Passos
     * @version 21/08/2017
     * @param $clienteId (valor da chave do Cliente)
     * @param $tituloId (valor da chave do Título)
     * @param $tituloValor (valor do Título)
     * @param $tituloValorDesconto (valor de desconto do titulo)
     * @param $tituloValorMulta (valor da Multa)
     * @param $tituloValorJuros (Valor do Juros)
     * @param $tituloValorAbatimento (Valor de Abatimento)
     * @param $tituloTabela (Titulo da Tabela)
     * @return Response $response: retorna o status do registro
     *     
    */

    public static function setRegistrarTitulo(
        $clienteId = 0,
        $tituloId = 0,
        $tituloValor = 0,
        $tituloValorDesconto = 0,
        $tituloValorMulta = 0,
        $tituloValorJuros = 0,
        $tituloValorAbatimento = 0,
        $tituloTabela = '',
        $updateDatabase = true,
    	$data_vencimento= ''
    ) {
        $boleto = new Controlador();

        return $boleto->setRegistrarTitulo(compact(
            'clienteId',
            'tituloId',
            'tituloValor',
            'tituloValorDesconto',
            'tituloValorMulta',
            'tituloValorJuros',
            'tituloValorAbatimento',
            'tituloTabela',
            'updateDatabase',
        	'data_vencimento'	
        ));
    }

    public static function getSonda(
        $clienteId = 0,
        $tituloId = 0,
        $tituloValor = 0,
        $tituloValorDesconto = 0,
        $tituloValorMulta = 0,
        $tituloValorJuros = 0,
        $tituloValorAbatimento = 0,
        $tituloTabela = ''
    ) {
        $boleto = new Controlador();

        return $boleto->getSonda(compact(
            'clienteId',
            'tituloId',
            'tituloValor',
            'tituloValorDesconto',
            'tituloValorMulta',
            'tituloValorJuros',
            'tituloValorAbatimento',
            'tituloTabela'
        ));
    }
}
?>