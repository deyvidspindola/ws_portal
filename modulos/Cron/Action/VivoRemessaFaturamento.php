<?php

/**
 * Classe para persistência de dados deste modulo
 *
 *  @package RemessaFaturamento
 */

require_once _MODULEDIR_ . 'Cron/DAO/VivoRemessaFaturamentoDAO.php';

/**
 * Classe de geração CSV
 */
require_once "../lib/Components/CsvWriter.php";

/**
 * Classe responsável pela remessa para a Vivo
 *
 *  @package RemessaFaturamento
 *  @author  Marcelo Fuchs <marcelo.fuchs@meta.com.br>
 *  @since   14/10/2013
 */
class RemessaFaturamento {

    /**
     * Objeto DAO.
     *
     * @var stdClass
     */
    private $dao;
    
    private $deParaClasse = array(
            //Plano Básico
            127 => array(
                    "plano"=> "VIVOGESTAOFROTABASIC",
                    "descricao" => "VIVOGESTAODEFROTAS-BASICO"
            )
            /** // DE/PARA AINDA NAO MAPEADOS
             //Plano Intermediário
            * => array(
                    "plano"=> "VIVOGESTAOFROTAINTER",
                    "descricao" =>"VIVOGESTAODEFROTAS-INTERM"
            )
            //Plano Avançado
            * => array(
                    "plano"=> "VIVOGESTAOFROTAAVANC",
                    "descricao" =>"VIVOGESTAODEFROTAS-AVANCADO"
            )
            //Serviço de Instalação/Remoção do Vivo Gestão de Frotas
            * => array(
                    "plano"=>"VIVOGESTFROTINSTREMO" ,
                    "descricao" =>"VIVOGESTAODEFROTAS-INST REMO"
            )
            //Serviço de Equipamento Básico e Intermediário
            * => array(
                    "plano"=> "EQUSASSIMP",
                    "descricao" => "EQUIPAMENTO SASCAR SIMPLES"
            )
            //Serviço de Equipamento Avançado
            * => array(
                    "plano"=> "EQUSASTELE",
                    "descricao" => "EQUIP SASCAR TELEMETRIA"
            )
            //Acessório
            * => array(
                    "plano"=> "ACESSASIBT",
                    "descricao" => "ACESSORIO SASCAR IBOTTON"
            )
            */
    );

     
    /**
     * Busca os arquivos abaixo no diretório
     * caso existam todos os arquivos nesse diretório deve iniciar a atualização.
     * Todos os arquivos devem ser do tipo NE ou RE e iniciados por RSEG.
     *
     *  @return string
     */
    public function exportarArquivosServidor() {

        $caminho = "/var/www/ARQUIVO_ITENS_FATURAVEIS_SASCAR/";
        if (is_dir($caminho)) {
            echo "\n\n";
            echo "****** SQL CONSULTA ITENS FATURAVEIS ******\n";
            $res = $this->dao->buscarItensFaturavais();
            if($res){
                //código da empresa 011 fixo para SASCAR.
                $codEmpresa='011';
                //$empresa='SASCAR';
                $hora = date('His');
                $nomeArquivo = 'ESERVICOS.'.$codEmpresa.'.'.date('dmy').'.'.$hora.'.IE';

                // Gera CSV
                $csvWriter = new CsvWriter( $caminho.$nomeArquivo, '|', '', true);

                //Gera o cabeçalho
                $cabecalho = array(
                        "0", // coluna 1 - cabeçalho identifica 0
                        $nomeArquivo, // coluna 2 - nome do arquivo
                        $codEmpresa, // coluna 3 - codigo da empresa
                        date('Ymd').$hora, // coluna 4 - hora
                        ""
                );
                $csvWriter->addLine( $cabecalho );

                $conta=0;
                $vlBrutoTotal=0;
                while($linha = $this->dao->fetchObject($res)){
                   
                    /**
                     *
                     1.	[LO001_002_001] Código do registro = 1;
                     2.	[LO001_002_002] Assinante/Subscription ID = veiculo_pedido_parceiro.vppasubscription;
                     3.	[LO001_002_003] Plano / Serviço = equipamento_classe.eqcoid [5.3 De/ Para -> Plano/ Serviço];
                     4.	[LO001_002_004] Descrição do Plano / Serviço = equipamento_classe.eqcdescricao [5.3 De/ Para -> Plano/ Serviço];
                     5.	[LO001_002_005] Data da Contratação do Serviço = contrato.condt_ini_vigencia;
                     6.	[LO001_002_006] Identificação do Contrato de Seguro = NULL;
                     7.	[LO001_002_007] Nro. da Parcela do Contrato = titulo_venda.titno_parcela;
                     8.	[LO001_002_008] Qtde total de parcelas do Contrato = contrato_pagamento.cpagnum_parcela;
                     9.	[LO001_002_009] Data de Referência da Parcela = titulo_venda.titdt_referencia;
                     10.	[LO001_002_010] Valor Líquido do Serviço = titulo_venda.titvl_pagamento;
                     11.	[LO001_002_011] Valor Bruto do Serviço = titulo_venda.titvl_titulo_venda;
                     12.	[LO001_002_012] Código de Refaturamento = NULL;
                     13.	[LO001_002_013] Identificador do Registro no Lote = 0000001 a 9999999;
                     14.	[LO001_002_014] Campo para Uso Futuro = NULL;
                     15.	[LO001_002_015] Campo para Uso Futuro = NULL;
                     16.	[LO001_002_016] Campo para Uso Futuro = NULL;
                     17.	[LO001_002_017] Campo para Uso Futuro = NULL;
                     */
                    $conta++;
                    $vlBrutoTotal += $linha->titvl_titulo_venda;
                    $eqcoid = $linha->eqcoid;
                    $registro = array(
                                "1", //identifica linha de registro
                                $linha->vppasubscription,
                                (isset($this->deParaClasse[$eqcoid]) ? $this->deParaClasse[$eqcoid]["plano"] : ""),
                                (isset($this->deParaClasse[$eqcoid]) ? $this->deParaClasse[$eqcoid]["descricao"] : ""),
                                (!empty($linha->condt_ini_vigencia) ? date("Ymd", strtotime($linha->condt_ini_vigencia)) : ""),
                                "",
                                $linha->titno_parcela,
                                $linha->cpagnum_parcela,
                                (!empty($linha->titdt_referencia) ? date("Ym", strtotime($linha->titdt_referencia)) : ""),
                                number_format($linha->titvl_pagamento, 2, ",", ""),
                                number_format($linha->titvl_titulo_venda, 2, ",", ""),
                                "",
                                str_pad($conta, 7, "0", STR_PAD_LEFT),
                                "",
                                "",
                                "",
                                ""
                            );
                    $csvWriter->addLine( $registro );
                    
                    //grava histórico
                    if(!$this->dao->gravaHistorico($linha->vppasubscription, $linha->titoid, $nomeArquivo)){
                        throw new Exception("Houve um erro ao gravar o histórico.");
                    }
                }
                
                //Gera o cabeçalho
                $rodape = array(
                        "9", // coluna 1 - 9 identifica rodapé
                        $conta, // coluna 2 - quantidade de registros
                        number_format($vlBrutoTotal, 2, ",", ""), // coluna 3 - valor bruto
                        "" //reservaldo
                );
                $csvWriter->addLine( $rodape );
                
                $arquivo = file_exists($caminho.$nomeArquivo);
                if ($arquivo === false) {
                    throw new Exception("O arquivo não pode ser gerado.");
                }
            }
            else {
                echo "\n";
                echo "****** SQL NÃO RETORNOU RESULTADOS ******\n";
            }


        } else {
             
            throw new Exception('Diretório não existe.');

        }

        return true;

    }

    /**
     * Metodo Construtor
     */
    public function __construct() {
        $this->dao = new RemessaFaturamentoDAO();
        
    }
}