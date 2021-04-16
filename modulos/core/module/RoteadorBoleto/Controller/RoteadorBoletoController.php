<?php

namespace module\RoteadorBoleto;

use H2P\Converter\PhantomJS;
use H2P\TempFile;

use InvalidArgumentException;
use infra\ComumController;
use infra\Helper\Response;
use module\Boleto\BoletoService;
use module\BoletoRegistrado\BoletoRegistradoModel;
use module\RegistroOnline\RegistrarBoletoService;
use module\TituloCobranca\TituloCobrancaModel;

class RoteadorBoletoController extends ComumController
{

    protected $idTitulo;
    protected $tipoRegistro;
    protected $tipoTitulo;
    protected $boleto;
    protected $response;

    /**
     * Método construtor seta os atributos do objeto e identifica qual tabela (tipoTitulo) o título está inserido
     *
     * @param {Number} $idTitulo
     * @param {string} $tipoRegistro - Valor pode ser vazio ou 'boleto_seco'
     * @return {RoteadorBoletoController}
     */
    public function __construct($idTitulo, $tipoRegistro)
    {
        if (!is_numeric($idTitulo)) {
            throw new InvalidArgumentException('O parâmetro $idTitulo deve ser um número.');
        }

        $this->idTitulo = $idTitulo;
        $this->tipoRegistro = $tipoRegistro;
        $this->model = new RoteadorBoletoModel;
        $this->tipoTitulo = $this->model->getTipoTitulo($idTitulo);
        $this->response = new Response;

        if (empty($this->tipoTitulo)) {
            throw new InvalidArgumentException('O parâmetro $idTitulo possui um valor não encontrado na base de dados.');
        }
    }

    /**
     * Método que retorna se o título já foi registrado
     * Utiliza os atributos do objeto e chama a função BoletoService::consultarRegistroBoleto()
     *
     * @return {boolean}
     */
    public function isTituloRegistrado()
    {
        $response = BoletoService::consultarRegistroBoleto($this->idTitulo, $this->tipoTitulo);
        return $response->dados;
    }
    
    /**
     * Atualiza a data de vencimento de um titulo.
     *
     * @param {string} $dataVencimento
     */
    public function updateDataVencimento($dataVencimento)
    {
        $this->model->updateDataVencimento($this->idTitulo, $this->tipoTitulo, $dataVencimento);
    }

    /**
     * Método que utiliza os atributos do objeto para registrar o título
     * Utiliza o método RegistrarBoletoService::setRegistrarTitulo()
     *
     * @return {\infra\Helper\Response}
     */
    public function registrarBoleto(
        $valor = null,
        $desconto = null,
        $multa = null,
        $juros = null,
        $abatimentos = null,
        $updateDatabase = true,
    	$dtVencimento = null
    ) {
        require_once _MODULEDIR_ . '/core/infra/autoload.php';

        $titulo = $this->model->getTituloBoleto($this->idTitulo, $this->tipoTitulo);
        
        /**
         * STI 87096 - 4.2 - Adicionado o valor de impostos a abatimentos
         * @author douglas.karling.ext
         * @since 07/12/2017
         */
        $response = RegistrarBoletoService::setRegistrarTitulo(
            $titulo->clioid,
            $this->idTitulo,
            $valor === null ? $titulo->valor : $valor,
            $desconto === null ? $titulo->desconto : $desconto,
            $multa === null ? $titulo->multa : $multa,
            $juros === null ? $titulo->juros : $juros,
            $abatimentos === null ? $titulo->impostos : $abatimentos,
            '',
            $updateDatabase,
            $dtVencimento
        );

        if (is_object($response) && $response->codigo == '0') {
            $this->response->setResult(true, 0, 'Boleto registrado com sucesso');
            return $this->response;
        }

        $this->response->setResult(false, 'CBR001', 'Boleto rejeitado ' . ($response->codigo == 'CBR001' ? $response->dados : ''));
        return $this->response;
    }

    public function getSonda()
    {
        require_once _MODULEDIR_ . '/core/infra/autoload.php';
        $titulo = $this->model->getTitulo($this->idTitulo, $this->tipoTitulo);
        
        return RegistrarBoletoService::getSonda(
            $titulo->clioid,
            $this->idTitulo,
            $titulo->valor,
            $titulo->desconto,
            $titulo->multa,
            $titulo->juros
        );
    }

    /**
     * Método que obtém o HTML de um boleto registrado Santander
     *
     * @return {string}
     */
    public function getHtmlBoleto(
        $moraMulta = null, 
        $outrosAcrescimos = null, 
        $descontosAbatimentos = null,
        $outrasDeducoes = null, 
        $valorCobrado = null
    ) {
        $boleto = $this->model->getTituloBoleto($this->idTitulo, $this->tipoTitulo);
        $isTituloVencido = strtotime('now') > strtotime($boleto->data_vencimento);

        $descontosAbatimentos = $descontosAbatimentos !== null ? $descontosAbatimentos : $boleto->desconto;

        /**
         * STI 87096 - 4.2 - Adicionado o valor de impostos ao campo Outras Deduções
         * @author douglas.karling.ext
         * @since 07/12/2017
         */
        $outrasDeducoes = $outrasDeducoes !== null ? $outrasDeducoes : $boleto->impostos;
        $moraMulta = $moraMulta !== null ? $moraMulta : $boleto->multa;
        $outrosAcrescimos = $outrosAcrescimos !== null ? $outrosAcrescimos : $boleto->juros;
        $valorCobrado = $valorCobrado !== null ? $valorCobrado : $boleto->valor_cobrado;

        $params = array(
            'clioid' => $boleto->clioid,
            'forcoid' => 84,
            'cfbbanco' => 0,
            'dataVencimento' => $isTituloVencido ? date('Y-m-d') : $boleto->data_vencimento,
            'valor' => $boleto->valor,
            'sequencial' => $this->idTitulo,
            'carteira' => 101, // cobrança registrada
            'ios' => 0,
            'numeroDocumento' => '',
            'descontosAbatimentos' => (float) $descontosAbatimentos ? $descontosAbatimentos : '',
            'outrasDeducoes' => (float) $outrasDeducoes ? $outrasDeducoes : '',
            'moraMulta' => (float) $moraMulta ? $moraMulta : '',
            'outrosAcrescimos' => (float) $outrosAcrescimos ? $outrosAcrescimos : '',
            'valorCobrado' => (float) $valorCobrado ? $valorCobrado : '',
        );

        $boleto = BoletoService::gerarBoleto($params, 'santander');
        return $boleto->dados;
    }
    
    /**
     * Método que decide qual arquivo de boleto gerar (registrado ou não registrado), chama a função correta para geração do arquivo
     * Retorna string binária do arquivo pdf que será enviado por email como anexo
     *
     * @param string $html O html do PDF
     * @return {string}  path para arquivo pdf
     */
    public function getArquivoBoleto($html)
    {
        include_once _SITEDIR_ . 'lib/h2p-master/src/H2P/TempFile.php';
        include_once _SITEDIR_ . 'lib/h2p-master/src/H2P/Converter/ConverterAbstract.php';
        include_once _SITEDIR_ . 'lib/h2p-master/src/H2P/Converter/PhantomJS.php';
        include_once _SITEDIR_ . 'lib/h2p-master/src/H2P/Request.php';
        include_once _SITEDIR_ . 'lib/h2p-master/src/H2P/Exception.php';
 
        try {
            $headTagStart = strpos(strtolower($html), '<head>') + strlen('<head>');
            $css = '<style type="text/css">html,body{height:100%}body{transform-origin: 0 0; -webkit-transform-origin: 0 0; transform: scale(0.75); -webkit-transform: scale(0.75);}</style>';
            $html = substr_replace($html, $css, $headTagStart, 0);
 
            $input = new TempFile($html, 'html');
            $path = _BOLETOTMPDIR_ . "//$this->idTitulo.pdf";

            $converter = new PhantomJS(array(
                'orientation' => PhantomJS::ORIENTATION_PORTRAIT,
                'format' => PhantomJS::FORMAT_A4,
            ));
 
            $converter->convert($input, $path);
        } catch (\Exception $e) {
            die('Ocorreu um erro durante a gerao do boleto. Erro: '. $e->getMessage());
        }
 
        return $path;
    }

    /**
     * Método que gera e exibe em tela o arquivo pdf de um boleto registrado
     *
     * @param string $html O html do PDF
     * @return {void}
     */
    public function mostrarBoletoRegistrado($html)
    {
        $file = $this->getArquivoBoleto($html);
        
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/pdf');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));

        echo file_get_contents($file);
    }

    /**
     * Retorna a instância do TCPDF.
     *
     * @param string $html O html do PDF
     * @return TCPDF
     */
    private function getPdf($html)
    {
        include_once _SITEDIR_ . '/lib/tcpdf_php4/tcpdf.php';

        $pdf = new \TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetFont('Arial', '', 7);
        $pdf->AddPage();
        $pdf->writeHTML($html);
        $pdf->lastPage();

        return $pdf;
    }

    /**
     * Método que altera o status de um título no banco de dados na tabela correta
     *
     * @param {string} $novoStatus
     * @return {boolean} - Sucesso ou Falha
     */
    public function alterarStatus($novoStatus)
    {
        return $this->model->alteraStatus($this->idTitulo, $this->tipoTitulo, $novoStatus);
    }

    /**
     * Método que busca no banco de dados o código correto a ser utilizado no arquivo CNAB
     *
     * @return {string}
     */
    public function getCodigoCancelamentoCnab()
    {
        $register = $this->model->getCodigoCancelamentoCnab();

        if (empty($register->tpetoid)) {
            return '';
        }

        return $register->tpetoid;
    }

    /**
     * Método que busca os códigos de título registrado
     *
     * @return {mixed}
     */
    public function getCodigosBoletoRegistrado()
    {
        $result = array();

        $aux = $this->model->getPcsiDescricao();

        if (empty($aux->pcsidescricao)) {
            return $result;
        }

        $codigos = $this->model->getCodigoBoletoRegistrado($aux->pcsidescricao);

        if (!is_array($codigos)) {
            return $result;
        }

        foreach ($codigos as $o) {
            array_push($result, $o->tpetoid);
        }

        return $result;
    }
    /**
     * Busca o código de cancelamento para arquivo cnab e salva no título na tabela correta
     *
     * @return {boolean} - Sucesso ou Falha
     */
    public function cancelarTituloCnab()
    {
        $codigo = $this->getCodigoCancelamentoCnab();

        if (empty($codigo)) {
            throw new InvalidArgumentException('Não foi encontrado o código cancelamento CNAB na base de dados.');
        }

        return $this->model->cancelarTituloCnab($this->idTitulo, $this->tipoTitulo, $codigo);
    }

    /**
     * Checa se o boleto é registrado e faz o cancelamento no banco
     * Efetua o cancelamento dentro do ERP
     *
     * @return {\infra\Helper\Response}
     */
    public function cancelarTitulo()
    {
        if ($this->isTituloRegistrado()) {
            $update = $this->cancelarTituloCnab();
        }

        $update   = $this->model->cancelarTituloERP($this->idTitulo, $this->tipoTitulo);
        $response = new Response;

        if ($update) {
            $response->setResult(true, 0, 'Titulo cancelado com sucesso');
            return $response;
        }

        $response->setResult(false, 'CBR004', 'Ocorreu um erro durante o cancelamento do título. Verifique os dados e tente novamente.');
        return $response;
    }

    /**
     * Método que busca no banco de dados o código correspondente a título expirado no arquivo cnab
     *
     * @return {array}
     */
    public function getCodigoExpiradoCnab()
    {
        $register = $this->model->getCodigoExpiradoCnab();

        if (empty($register)) {
            return '';
        }

        return $register;
    }

    /**
     * Método que verifica no banco de dados se um título está expirado
     *
     * @return {boolean}
     */
    public function isTituloExpirado()
    {
        $codigo = $this->getCodigoExpiradoCnab();

        if (empty($codigo)) {
            throw new InvalidArgumentException('Não foi encontrado o código expirado CNAB na base de dados.');
        }

        return $this->model->isTituloExpirado($this->idTitulo, $this->tipoTitulo, $codigo);
    }

    /**
     * Método que verifica no banco de dados se um título está ativo
     *
     * @return {boolean}
     */
    public function isTituloAtivo()
    {
        $isTituloAtivo = $this->model->isTituloAtivo($this->idTitulo, $this->tipoTitulo);

        if (!$isTituloAtivo) {
            $titulo = $this->model->getTitulo($this->idTitulo, $this->tipoTitulo);

            if ($titulo->tittpetoid == $this->getCodigoAtivo()) {
                return true;
            }
        }

        return $isTituloAtivo;
    }

    /**
     * Método que busca o código de título ativo
     *
     * @return {string}
     */
    public function getCodigoAtivo()
    {
        $register = $this->model->getCodigoAtivo();

        if (empty($register->tpetoid)) {
            return '';
        }

        return $register->tpetoid;
    }

    public function tituloHasBoletoRegistrado()
    {
        $titulo = TituloCobrancaModel::getTituloById($this->idTitulo);
        
        // [ORGMKTOTVS-2682] - Mostrar 2ª via de boleto quando for baixa como perda
        if($titulo->formaCobranca == 51 && (strstr($titulo->titpref_protheus, 'P')) && BoletoRegistradoModel::getUltimoBoletoValido($this->idTitulo)){
            return true;
        }
        
        $isFormaCobrancaBoleto = TituloCobrancaModel::isFormaCobrancaBoleto($this->idTitulo);
        $isFormaCobrancaDebitoAutomatico = TituloCobrancaModel::isFormaCobrancaDebitoAutomatico($this->idTitulo);
        $isFormaCobrancaCartaoDeCredito = TituloCobrancaModel::isFormaCobrancaCartaoDeCredito($this->idTitulo);

        if (!in_array(true, array($isFormaCobrancaBoleto, $isFormaCobrancaCartaoDeCredito, $isFormaCobrancaDebitoAutomatico))) {
            return false;
        }

        //tipo do evento que define que o boleto eh 2 via
        if (intval($titulo->tipoEventoTitulo) != TituloCobrancaModel::TIPO_EVENTO_ENTRADA_CONFIRMADA) {
            return false;
        }

        if (!BoletoRegistradoModel::getUltimoBoletoValido($this->idTitulo)) {
            return false;
        }

        return true;
    }
}
