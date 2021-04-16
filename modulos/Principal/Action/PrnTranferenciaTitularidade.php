<?php

require_once _MODULEDIR_ . "/Financas/DAO/FinFaturamentoUnificadoDAO.php";
require_once _SITEDIR_ . "/lib/phpMailer/class.phpmailer.php";

use module\TituloCobranca\TituloCobrancaDAO;
use module\NotaFiscal\NotaFiscalModel;
use module\NotaFiscalEletronica\NotaFiscalEletronicaModel;
use module\Parametro\ParametroNotaFiscalEletronica;
use module\Parametro\ParametroCobrancaRegistrada;
use module\EscritorRemessaRPSBarueri\EscritorHeaderTipo1ArquivoRemessaRPSBarueriModel;
use module\EscritorRemessaRPSBarueri\EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel;
use module\EscritorRemessaRPSBarueri\EscritorDetalheTipo3ArquivoRemessaRPSBarueriModel;
use module\EscritorRemessaRPSBarueri\EscritorTrailerTipo9ArquivoRemessaRPSBarueriModel;
use module\LeitorRetornoRPSBarueri\LeitorRetornoRPSBarueriModel;
use module\LeitorRetornoErroRPSBarueri\LeitorRetornoErroRPSBarueriModel;

class PrnTranferenciaTitularidade
{
    const VIEW_DIR = 'Principal/View/prn_transferencia_titularidade/';
    const CODIGO_ORIGEM_OCORRENCIA_VALIDADOR = 1;
    const CODIGO_ORIGEM_OCORRENCIA_ARQUIVO_RETORNO = 2;

    private $conn;
    private $dao;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
        $this->dao  = new PrnTranferenciaTitularidadeDAO($conn);
    }

    private function view($viewName)
    {
        include _MODULEDIR_ . self::VIEW_DIR . $viewName;
    }

    private function resJson($data)
    {
        header('Content-Type: application/json');
        $data = array_map("utf8_encode", $data);
        // JSON_UNESCAPED_UNICODE = 256
        echo json_encode($data, 256);
        exit;
    }

    public function index()
    {
        $this->view('index.php');
    }

    public function novo()
    {
        $this->action = 'novo';
        $this->view('novo.php');
    }

    public function historico()
    {
        $this->action = 'historico';
        $this->view('historico.php');
    }

    public function pesquisar()
    {
        try {
            $this->acao = "pesquisar";

            $numeroCpfCnpj = !empty($_POST['numero_cpf_cnpj']) ? $_POST['numero_cpf_cnpj'] : null;
            $atualTitular = !empty($_POST['atual_titular']) ? $_POST['atual_titular'] : null;
            $numeroTermoContrato = !empty($_POST['numero_termo_contrato']) ? $_POST['numero_termo_contrato'] : null;
            $numeroPlaca = !empty($_POST['numero_placa']) ? $_POST['numero_placa'] : null;
            $tipoProposta = !empty($_POST['tipo_proposta']) ? $_POST['tipo_proposta'] : null;
            $tipoContrato = !empty($_POST['tipo_contrato']) ? $_POST['tipo_contrato'] : null;
            $classeContrato = !empty($_POST['classe_contrato']) ? $_POST['classe_contrato'] : null;
            $numeroResultados = !empty($_POST['numero_resultados']) ? $_POST['numero_resultados'] : null;
            $ordenaResultados = !empty($_POST['ordena_resultados']) ? $_POST['ordena_resultados'] : null;
            $classificaResultados = !empty($_POST['classifica_resultados']) ? $_POST['classifica_resultados'] : null;

            $this->numeroCpfCnpj = $numeroCpfCnpj;
            $this->atualTitular = $atualTitular;
            $this->numeroTermoContrato = $numeroTermoContrato;
            $this->numeroPlaca = $numeroPlaca;
            $this->tipoProposta = $tipoProposta;
            $this->tipoContrato = $tipoContrato;
            $this->classeContrato = $classeContrato;
            $this->numeroResultados = $numeroResultados;
            $this->ordenaResultados = $ordenaResultados;
            $this->classificaResultados = $classificaResultados;

            if(empty($numeroCpfCnpj) && empty($atualTitular) && empty($numeroTermoContrato) && empty($numeroPlaca)){
                throw new Exception("Informe ao menos um filtro");
            }

            /*if((empty($numeroCpfCnpj))){
                throw new Exception("Informe o CPF/CNPJ no filtro");
            }*/

            /*if((empty($atualTitular))){
                throw new Exception("Informe o nome do titular atual");
            }*/

            if(!empty($numeroCpfCnpj)){
                $numeroCpfCnpj = explode(".", $numeroCpfCnpj);
                $numeroCpfCnpj = implode("", array_values($numeroCpfCnpj));
                $numeroCpfCnpj = explode("-", $numeroCpfCnpj);
                $numeroCpfCnpj = implode("", array_values($numeroCpfCnpj));
            }

            if(!empty($numeroPlaca)){
                $numeroPlaca = explode("-", $numeroPlaca);
                $numeroPlaca = implode("", array_values($numeroPlaca));
            }

            /*$this->ultimaDataEmissao = $this->dao->getUltimaDataEmissao();
            $this->ultimaDataEmissao = implode("/", array_reverse(explode("-", $this->ultimaDataEmissao)));*/

            $this->contratos = $this->dao->pesquisar(
                $numeroCpfCnpj,
                $atualTitular,
                $numeroTermoContrato,
                $numeroPlaca,
                $tipoProposta,
                $tipoContrato,
                $classeContrato,
                $numeroResultados,
                $ordenaResultados,
                $classificaResultados
            );

        }catch(Exception $e){
            $this->msgErro = $e->getMessage();
        }

        $this->view('index.php');

    }

    public function novoTitular()
    {
        try {
            $this->acao = "novo";

            $inicio = !empty($_POST['inicio_vigencia']) ? $_POST['inicio_vigencia'] : null;


            $this->inicioBoom = $inicio;

            echo "<pre>Aquiii - >  ";
            var_dump($inicio);
            echo "</pre>";


        }catch(Exception $e){
            $this->msgErro = $e->getMessage();
        }

        $this->view('novo.php');

    }




    public function gerarRPS(){

        try{

            $this->dao->begin();
            $this->dao->atualizarSequenciaNumeroRemessa();

            $novaDataEmissao = !empty($_POST['nfldt_emissao']) ? implode("-", array_reverse(explode('/', $_POST['nfldt_emissao']))) : null;
            $notasFiscais = $_POST['notas_fiscais'];

            if(count($notasFiscais) > 1000){
                throw new Exception("O número de notas selecionadas não pode ultrapassar 1000.");
            }

            global $autenticacaoSistemaUsuarios;

            $arquivo = '';
            $registros = '';
            $contadorLinhas = 0;
            $valorTotalNotas = 0;
            $valorTotalNaoTributados = 0;
            $codigoMovimentoRegistrado = ParametroCobrancaRegistrada::getCodigosMovimentoRegistrado();
            $inscricaoMunicipal = ParametroNotaFiscalEletronica::getCodigoInscricaoContribuinte();
            $numeroRemessaRPS = $this->dao->getSequenciaNumeroRemessa();
            $codigoUsuario = $_SESSION["usuario"]["oid"];
            $notasEnviadasAnteriormente = array();
            $titulosSemBoletoRegistrado = array();

            // Header
            $escritorHeaderRPS = new EscritorHeaderTipo1ArquivoRemessaRPSBarueriModel();
            $escritorHeaderRPS->setInscricaoContribuinte($inscricaoMunicipal);
            $escritorHeaderRPS->setIdentificacaoRemessaContribuinte($numeroRemessaRPS);

            if(!empty($novaDataEmissao)){
                // Se última data de emissao for maior que a data de emissao informada, não pode atualizar.
                $atualizarDataEmissao = !$this->dao->isUltimaDataEmissaoMaiorQueDataEmissaoInformada($novaDataEmissao);
            }

            foreach ($notasFiscais as $notaFiscalId){
                $contadorLinhas++;

                if(!empty($novaDataEmissao)){
                    if($atualizarDataEmissao){
                        $this->dao->atualizarDataEmissao($notaFiscalId, $novaDataEmissao);
                    }else{
                        $ultimaDataEmissao = $this->dao->getUltimaDataEmissao();
                        $ultimaDataEmissao = implode('/', array_reverse(explode('-', $ultimaDataEmissao)));
                        throw new Exception("Não é permitido a geração de notas fiscais com data inferior a de uma nota já enviada. Última da de emissão: {$ultimaDataEmissao}.");
                    }
                }else{
                    $this->dao->atualizarDataEmissao($notaFiscalId);
                }

                \FinFaturamentoUnificadoDAO::calcularImposto($notaFiscalId, $this->conn);

                // $notaFiscalModel = new NotaFiscalModel($notaFiscalId);
                $notaFiscalEletronicaModel = new NotaFiscalEletronicaModel($notaFiscalId);

                $escritorDetalheRPSTipo2 = new EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel();
                $escritorDetalheRPSTipo3 = new EscritorDetalheTipo3ArquivoRemessaRPSBarueriModel();

                $informacoesNotaFiscal = $this->dao->getInformacoesNotaFiscal($notaFiscalId);

                if(empty($informacoesNotaFiscal->data_transmissao_rps)){

                    $situacaoEnviado = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::SITUACAO_ENVIADO;
                    $localPrestacaoNoMunicipio = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::LOCAL_PRESTACAO_SERVICO_NO_MUNICIPIO;
                    $localPrestacaoViasNaoPublicas = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::LOCAL_PRESTACAO_SERVICO_VIAS_NAO_PUBLICAS;
                    $tipoTomadorBrasileiro = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::TIPO_BRASILEIRO;
                    $servicoNaoExportado = EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::SERVICO_NAO_EXPORTADO;
                    $codigoValorNaoIncluso = EscritorDetalheTipo3ArquivoRemessaRPSBarueriModel::CODIGO_VALOR_NAO_INCLUSO;
                    $tipoDocumento = $informacoesNotaFiscal->tipo_documento == 'F' ? EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::TIPO_CPF : EscritorDetalheTipo2ArquivoRemessaRPSBarueriModel::TIPO_CNPJ;

                    $informacoesObrigacaoFinanceira = $this->dao->getInformacoesObrigacaoFinanceira($notaFiscalId);

                    $discriminacaoServico = '';

                    if(isset($informacoesNotaFiscal->nflinfcomp)){
                        $discriminacaoServico .= "Inf Complementares: ". $informacoesNotaFiscal->nflinfcomp."|";
                    }

                    if(!empty($informacoesObrigacaoFinanceira)){
                        foreach($informacoesObrigacaoFinanceira as $informacoesItem){
                            $discriminacaoServico.= substr($informacoesItem['descricao_sub_grupo'], 0, 84);
                            $discriminacaoServico.= " R$ ". substr((number_format($informacoesItem['valor_item'], 2, ",", ".")), 0, 10);
                            $discriminacaoServico.= "|";
                        }
                    }

                    // Definir Sascar = 0 / Siggo = 1
                    $tipoContrato = $this->dao->buscarTipoContrato($informacoesNotaFiscal->numero_rps, $informacoesNotaFiscal->serie_rps);

                    $numeroContratosTipoVarejo = count(array_filter($tipoContrato, function($contrato){
                        return $contrato->tipo_proposta == 12 || $contrato->super_tipo_contrato == 12;
                    }));

                    $subCodigoCliente = count($tipoContrato) == $numeroContratosTipoVarejo ? 1 : 0;

                    $tituloCobrancaDAO = new TituloCobrancaDAO();

                    $nossoNumero = $this->dao->getNossoNumero($informacoesNotaFiscal->titulo_id);

                    if($tituloCobrancaDAO->isFormaCobrancaBoleto($informacoesNotaFiscal->titulo_id) && empty($nossoNumero)){
                        $titulosSemBoletoRegistrado[] = "O título {$informacoesNotaFiscal->titulo_id} no valor de R$ ". number_format($informacoesNotaFiscal->valor_nf, 2, ",", ".") ." referente a nota {$informacoesNotaFiscal->numero_rps}-{$informacoesNotaFiscal->serie_rps} está sem registro.";
                    }

                    $banco = $tituloCobrancaDAO->isFormaCobrancaBoleto($informacoesNotaFiscal->titulo_id) && !empty($nossoNumero) ? 9 : 'A';
                    $boleto = $informacoesNotaFiscal->numero_parcelas > 1 ? '0' : $subCodigoCliente . $banco . date('Ymd', strtotime($informacoesNotaFiscal->data_vencimento_titulo)) . str_pad($nossoNumero, 13, "0", STR_PAD_LEFT);

                    $discriminacaoServico.= "Boleto: ". $boleto ."|";
                    $discriminacaoServico.= "Valor aproximado dos tributos (Lei 12.741/12): R$ ". number_format($informacoesNotaFiscal->valor_tributos, 2, ",", ".") ."|";
                    $discriminacaoServico.= "Para mais informações, acesse o portal através do link: www.sascar.com.br/portal";

                    // Tipo 2
                    $escritorDetalheRPSTipo2->setSerieRPS($informacoesNotaFiscal->serie_rps);
                    $escritorDetalheRPSTipo2->setNumeroRPS($informacoesNotaFiscal->numero_rps);
                    $escritorDetalheRPSTipo2->setDataRPS($informacoesNotaFiscal->data_rps);
                    $escritorDetalheRPSTipo2->setSituacaoRPS($situacaoEnviado);
                    $escritorDetalheRPSTipo2->setCodigoServicoPrestado($informacoesNotaFiscal->codigo_servico_prestado);
                    $escritorDetalheRPSTipo2->setLocalPrestacaoServico($localPrestacaoNoMunicipio);
                    $escritorDetalheRPSTipo2->setServicoPrestadoViasPublicas($localPrestacaoViasNaoPublicas);
                    $escritorDetalheRPSTipo2->setQuantidadeServico(1);
                    $escritorDetalheRPSTipo2->setValorServico($informacoesNotaFiscal->total_tributados);
                    $escritorDetalheRPSTipo2->setTipoTomador($tipoTomadorBrasileiro);
                    // $escritorDetalheRPSTipo2->setServicoPrestadoExportacao($servicoNaoExportado);
                    $escritorDetalheRPSTipo2->setTipoDocumento($tipoDocumento);
                    $escritorDetalheRPSTipo2->setNumeroDocumento($informacoesNotaFiscal->numero_documento);
                    $escritorDetalheRPSTipo2->setNomeTomador($informacoesNotaFiscal->nome_tomador);
                    $escritorDetalheRPSTipo2->setEndereco($informacoesNotaFiscal->endereco_logradouro);
                    $escritorDetalheRPSTipo2->setNumero($informacoesNotaFiscal->endereco_numero);
                    $escritorDetalheRPSTipo2->setComplemento($informacoesNotaFiscal->endereco_complemento);
                    $escritorDetalheRPSTipo2->setBairro($informacoesNotaFiscal->endereco_bairro);
                    $escritorDetalheRPSTipo2->setCidade($informacoesNotaFiscal->endereco_cidade);
                    $escritorDetalheRPSTipo2->setUF($informacoesNotaFiscal->endereco_uf);
                    $escritorDetalheRPSTipo2->setCEP($informacoesNotaFiscal->endereco_cep);
                    $escritorDetalheRPSTipo2->setEmail($this->validarEnvioEmailNfe($informacoesNotaFiscal));
                    $escritorDetalheRPSTipo2->setDiscriminacaoServico($discriminacaoServico);

                    $registros .= $escritorDetalheRPSTipo2->getRegistro() . "\r\n";

                    // Tipo 3
                    if($informacoesNotaFiscal->total_nao_tributados > 0){
                        $contadorLinhas++;
                        $escritorDetalheRPSTipo3->setCodigoDeOutrosValores($codigoValorNaoIncluso);
                        $escritorDetalheRPSTipo3->setValor($informacoesNotaFiscal->total_nao_tributados);
                        $registros .= $escritorDetalheRPSTipo3->getRegistro() . "\r\n";
                    }

                    $valorTotalNotas += $escritorDetalheRPSTipo2->getValorServico();
                    $valorTotalNaoTributados += $informacoesNotaFiscal->total_nao_tributados;

                    $existeNotaFiscalEletronica = $this->dao->verificarExistenciaNotaFiscalEletronicaKernel($informacoesNotaFiscal->numero_rps, $informacoesNotaFiscal->serie_rps);

                    $idEmpresaAutenticada = $this->dao->getIdEmpresaAutenticada($autenticacaoSistemaUsuarios);

                    if($existeNotaFiscalEletronica){
                        $this->dao->atualizarNotaFiscalEletronicaKernel($idEmpresaAutenticada, $codigoUsuario, $informacoesNotaFiscal->numero_rps, $informacoesNotaFiscal->serie_rps);
                    }else{
                        $this->dao->inserirNotaFiscalEletronicaKernel($informacoesNotaFiscal->nf_id, $idEmpresaAutenticada, $codigoUsuario, $informacoesNotaFiscal->numero_rps, $informacoesNotaFiscal->serie_rps);
                    }

                }else{
                    $notasEnviadasAnteriormente[] = "{$informacoesNotaFiscal->numero_rps}-{$informacoesNotaFiscal->serie_rps}";
                }

            }

            if(!empty($notasEnviadasAnteriormente)){
                throw new Exception("As notas a seguir já foram enviadas:<br><br>" . implode("<br>", $notasEnviadasAnteriormente));
            }

            $escritorTrailerRPS = new EscritorTrailerTipo9ArquivoRemessaRPSBarueriModel();

            $escritorTrailerRPS->setNumeroTotalLinhasDoArquivo($contadorLinhas + 2);
            $escritorTrailerRPS->setValorTotalServicosContidosNoArquivo($valorTotalNotas);
            $escritorTrailerRPS->setValorTotalRetencoesEOutrosValores($valorTotalNaoTributados);

            $arquivo .= $escritorHeaderRPS->getRegistro() . "\r\n";
            $arquivo .= $registros;
            $arquivo .= $escritorTrailerRPS->getRegistro() . "\r\n";

            // EXEMPLO
            $dataEmissao = !empty($novaDataEmissao) ? $novaDataEmissao : date('Y-m-d');
            $rand = rand(0000, 9999);

            $nomeArquivo = "SASCAR_nfe_kernel_{$dataEmissao}_{$rand}.txt";
            $diretorioArquivo = '/var/www/Kernel/Entrada/';

            $fp = fopen($diretorioArquivo . $nomeArquivo, "w");

            if(!$fp){
                throw new Exception("Não foi possível gerar o arquivo por falta de permissão.");
            }

            fwrite($fp, $arquivo);
            fclose($fp);

            $emailDestino = $_SESSION['servidor_teste'] == 1 ? 'teste_desenv@sascar.com.br' : 'fat-kernel@sascar.com.br';

            $phpMailer = new PHPMailer();
            $phpMailer->ClearAllRecipients();
            $phpMailer->From = "sistema@sascar.com.br";
            $phpMailer->FromName = "Intranet SASCAR - Email automático";
            $phpMailer->Subject = "Arquivos para geracao de NFe - Arquivo[$nomeArquivo]";
            $phpMailer->MsgHTML("Segue anexo.");
            $phpMailer->AddAddress($emailDestino);
            $phpMailer->AddAttachment($diretorioArquivo . $nomeArquivo);
            $phpMailer->Send();

            $this->dao->commit();

            $msgSucesso = "";

            if(!empty($titulosSemBoletoRegistrado)){
                $msgSucesso .= "Algum título relacionado à NF selecionada não possui boleto registrado.<br>";
                $msgSucesso .= implode("<br>", $titulosSemBoletoRegistrado) . "<br>";
            }

            $msgSucesso .= "Arquivo gerado com sucesso.";

            $this->resJson(array(
                'urlArquivo' => $diretorioArquivo . $nomeArquivo,
                'msgSucesso' => $msgSucesso
            ));


        }catch(Exception $e){
            $this->dao->rollback();
            $this->resJson(array('msgErro' => $e->getMessage()));
        }

    }
}