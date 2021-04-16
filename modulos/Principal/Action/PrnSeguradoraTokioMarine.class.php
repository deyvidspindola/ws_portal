<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once _SITEDIR_.'lib/phpMailer/class.phpmailer.php';
require      _SITEDIR_.'modulos/Principal/DAO/PrnSeguradoraTokioMarineDAO.class.php';

/**
 * Processamento Manual para a seguradora Tokio Marine
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @package Principal
 * @version 1.0
 * @since 11/04/2013
 */
class PrnSeguradoraTokioMarine{
    private $dao           = "";
    private $dir           = "";    
    private $veisegoid     = ""; //id da seguradora
    private $id_usuario    = ""; //id do usuário logado
    private $origem        = ""; //Origem do processo: Processamento Manual ou Processamento Automático.
    private $prpstpcoid    = ""; //id do tipo de contrato
    private $msg           = "";
    private $bsegcia       = "";
    private $dadosProposta = "";
    
    function __construct($veisegoid, $dir, $origem, $id_usuario = ""){
        $this->dao        = new PrnSeguradoraTokioMarineDAO();        
        $this->dir        = trim($dir);
        $this->veisegoid  = (int) $veisegoid;    
        $this->origem     = trim($origem);

        if($id_usuario != ""){
            $this->id_usuario = $id_usuario;
        } else{
            $this->id_usuario = $_SESSION['usuario']['oid'];
        }
        
        //Definindo o tipo de contrato
        if($this->veisegoid == 116){
            $this->prpstpcoid = 884;
            $this->bsegcia    = 'TKM';
        } elseif ($this->veisegoid == 32){
            $this->prpstpcoid = 883;
            $this->bsegcia    = 'TKB';
        } elseif ($this->veisegoid == 120){

        	$this->prpstpcoid = 919;
        	$this->bsegcia    = 'TKM';
        	
        }
    }
    
    /**
     * Verifica se o arquivo foi ou não processado.
     * @param String $arquivo
     * @return boolean
     */
    public function verificarProcessamento($arquivo){   
        $result = $this->dao->getProcessamento($this->prpstpcoid,$arquivo); 
        return $result;
    }
    
    /**
     * Valida as informações do arquivo.
     * @param String $arquivo 
     */
    public function validarArquivo($arquivo){
        try{
            //Caminho de armazenamento do arquivo
            $caminho  = $this->dir.$arquivo;
            $prpsqoid = $this->dao->inserirPropostaSeguradoraArquivo($arquivo,$this->id_usuario,$caminho,$this->prpstpcoid,$this->origem);
            
            //Chave primaria da tabela proposta_seguradora_arquivo
            if($prpsqoid == 0){
                throw new Exception("Erro.: Não foi possível inserir a Proposta Seguradora Arquivo.");                    
            }
            
            //Obtendo o total de linhas do arquivo
            $array = file($this->dir.$arquivo);
            $total_lines = count($array);
            
            $file = fopen($this->dir.$arquivo, 'r');
            $count_lines = 0;            
            
            $this->dao->beginTransaction(); 
            
            if($this->veisegoid == 116 || $this->veisegoid == 120){ //TOKIO MARINE OU TOKIO MARINE RF
                while(!feof($file)){
                    $count_lines++;
                    
                    if($count_lines > $total_lines){
                        break;
                    }
                    
                    //Linha que está sendo lida
                    $line = fgets($file);
                    
                    //Comodato
                    $cod_modulo_produto = trim(substr($line,0,5));
                    $cod_comodato       = trim(substr($line,5,9));
                    $desc_comodato      = trim(substr($line,14,30));
                    
                    if($cod_modulo_produto == "" || strlen($cod_modulo_produto) < 5){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Código Modulo Produto</b> está vazio ou incompleto.");                    
                    } elseif($cod_comodato == "" || strlen($cod_comodato) < 9){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Código Comodato</b> está vazio ou incompleto.");                    
                    } elseif($desc_comodato == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Descrição Comodato</b> está vazio.");                    
                    }
                    
                    //Dispositivo
                    $tipo_dispositivo_seguranca      = trim(substr($line,44,1));
                    $desc_tipo_dispositivo_seguranca = trim(substr($line,45,30));
                    
                    if($tipo_dispositivo_seguranca == "" || strlen($tipo_dispositivo_seguranca) < 1){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Tipo Dispositivo Segurança</b> está vazio ou incompleto.");                    
                    } elseif($desc_tipo_dispositivo_seguranca == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Descrição Tipo de Dispositivo Segurança</b> está vazio.");
                    }
                    
                    //Negocio
                    $numero_negocio        = trim(substr($line,75,9));
                    $identificacao_negocio = trim(substr($line,84,3));
                    
                    if($numero_negocio == "" || strlen($numero_negocio) < 9){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Número Negócio</b> está vazio ou incompleto.");                    
                    } elseif($identificacao_negocio == "" || strlen($identificacao_negocio) < 3){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Identificação Negócio</b> está vazio ou incompleto.");                    
                    }
                    
                    //Item
                    $numero_item          = trim(substr($line,87,9));
                    $numero_item_anterior = trim(substr($line,96,9));
                    
                    if($numero_item == "" || strlen($numero_item) < 9){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Número Item</b> está vazio ou incompleto.");                    
                    }
                    
                    //Endosso
                    $numero_endosso    = trim(substr($line,105,9));
                    $desc_tipo_endosso = trim(substr($line,114,13));
                    
                    //Apolice
                    $cod_apolice_unica      = trim(substr($line,127,9));
                    $cod_apolice_especifica = trim(substr($line,136,9));
                    
                    if($cod_apolice_unica == "" || strlen($cod_apolice_unica) < 9){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Código Apólice Única</b> está vazio ou incompleto.");                    
                    } elseif($cod_apolice_especifica == "" || strlen($cod_apolice_especifica) < 9){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Código Apólice Específica</b> está vazio ou incompleto.");                    
                    }
                    
                    //Datas
                    $dt_inicio_vigencia = trim(substr($line,145,8));
                    $dt_fim_vigencia    = trim(substr($line,153,8));
                    $dt_emissao         = trim(substr($line,161,8));
                    
                    if($dt_inicio_vigencia == "" || strlen($dt_inicio_vigencia) < 8){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Data Inicio Vigência</b> está vazio ou incompleto.");                    
                    } elseif($dt_fim_vigencia == "" || strlen($dt_fim_vigencia) < 8){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Data Fim Vigência</b> está vazio ou incompleto.");                    
                    } elseif($dt_emissao == "" || strlen($dt_emissao) < 8){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Data Emissão</b> está vazio ou incompleto.");                    
                    }
                    
                    //Agrupamento
                    $cod_agrupamento_regiao_tarifaria = trim(substr($line,169,9));
                    $cod_agrupamento_veiculo          = trim(substr($line,178,9));
                    $desc_agrupamento_veiculo         = trim(substr($line,187,30));
                    
                    //Fabricante
                    $cod_fabricante  = trim(substr($line,217,9));
                    $desc_fabricante = trim(substr($line,226,30));
                    
                    if($desc_fabricante == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Descrição Fabricante</b> está vazio.");                    
                    }
                    
                    //Modelo
                    $cod_marca_modelo = trim(substr($line,256,9));
                    $desc_modelo      = trim(substr($line,265,30));
                    $ano_modelo       = trim(substr($line,295,4));
                    $placa            = strtoupper(trim(substr($line,299,9)));
                    $chassi           = strtoupper(trim(substr($line,308,20)));
                    
                    if($desc_modelo == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Descrição do Modelo</b> está vazio.");                    
                    } elseif($ano_modelo == "" || strlen($ano_modelo) < 4){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Ano/Modelo</b> está vazio ou incompleto.");                    
                    } elseif($placa == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Placa</b> está vazio.");                    
                    } elseif($chassi == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Chassi</b> está vazio.");                    
                    }
                    
                    //Segurado
                    $nome_segurado = strtoupper(trim(substr($line,328,35)));
                    $cpf_cnpj      = trim(substr($line,363,20));
                    $endereco      = strtoupper(trim(substr($line,383,40)));
                    $numero        = trim(substr($line,423,5));
                    $complemento   = trim(substr($line,428,15));
                    $bairro        = strtoupper(trim(substr($line,443,15)));
                    $cep           = trim(substr($line,458,8));
                    $cidade        = strtoupper(trim(substr($line,466,15)));
                    $estado        = strtoupper(trim(substr($line,481,2)));
                    $ddd           = trim(substr($line,483,4));
                    $telefone      = trim(substr($line,487,10));
                    
                    //$this->formatarCpfCnpj($cpf_cnpj);
                    
                    if($nome_segurado == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Nome do Segurado</b> está vazio.");                    
                    } elseif($cpf_cnpj == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>CPF/CNPJ</b> está vazio.");                    
                    } elseif($endereco == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Endereço</b> está vazio.");                    
                    } elseif($numero == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Número do endereço</b> está vazio.");                    
                    } elseif($bairro == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Bairro</b> está vazio.");                    
                    } elseif($cep == "" || strlen($cep) < 8){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>CEP</b> está vazio ou incompleto.");                    
                    } elseif($cidade == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Cidade</b> está vazio.");                    
                    } elseif($estado == "" || strlen($estado) < 2){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Estado</b> está vazio ou incompleto.");                    
                    } elseif($ddd == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>DDD</b> está vazio.");                    
                    } elseif($telefone == ""){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Telefone do segurado</b> está vazio.");                    
                    }
                    
                    //Outras informacoes
                    $cod_sucursal            = trim(substr($line,497,4));
                    $cod_diretoria_comercial = trim(substr($line,501,2));
                    $cod_corretor            = trim(substr($line,503,6));
                    $dt_instalacao           = trim(substr($line,509,8));
                    $dt_retirada             = trim(substr($line,517,8));
                    $cod_evento_solicitacao  = trim(substr($line,525,2));
                    $cod_evento_retorno      = trim(substr($line,527,2));
                    $ddd_comercial           = trim(substr($line,529,4));
                    $telefone_comercial      = trim(substr($line,533,10));
                    $nome_corretora          = strtoupper(trim(substr($line,543,50)));
                    $ddd_corretora           = trim(substr($line,593,3));
                    $telefone_corretora      = trim(substr($line,596,10));
                    $email_corretora         = strtolower(trim(substr($line,606,50)));  

                    if($cod_sucursal == "" || strlen($cod_sucursal) < 4){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Código Sucursal</b> está vazio ou incompleto.");                    
                    } elseif($cod_evento_solicitacao == "" || strlen($cod_evento_solicitacao) < 2){
                        throw new Exception("Erro na linha: ".$count_lines.". <b>Código Evento Solicitação</b> está vazio ou incompleto.");                    
                    }
                    
                    //Array que contem as informações da linha que está sendo lida.
                    $dataLine = array("identificacao_negocio" => $identificacao_negocio, "numero_endosso" => $numero_endosso, "desc_tipo_endosso" => $desc_tipo_endosso,
                                      "cod_sucursal" => $cod_sucursal, "cod_evento_solicitacao" => $cod_evento_solicitacao, "numero_negocio" => $numero_negocio,
                                      "placa" => $placa, "chassi" => $chassi, "cod_apolice_unica" => $cod_apolice_unica, "cod_apolice_especifica" => $cod_apolice_especifica,
                                      "cpf_cnpj" => $cpf_cnpj, "nome_corretora" => $nome_corretora, "nome_segurado" => $nome_segurado, "email_corretora" => $email_corretora,
                                      "dt_inicio_vigencia" => $dt_inicio_vigencia, "dt_fim_vigencia" => $dt_fim_vigencia, "endereco" => $endereco, "bairro" => $bairro,
                                      "cidade" => $cidade, "estado" => $estado, "ddd" => $ddd, "telefone" => $telefone, "numero" => $numero, "cep" => $cep, "desc_fabricante" => $desc_fabricante,
                                      "desc_modelo" => $desc_modelo, "ddd_corretora" => $ddd_corretora, "telefone_corretora" => $telefone_corretora, "ano_modelo" => $ano_modelo,
                                      "numero_item" => $numero_item);
                    
                    $this->processarArquivo($arquivo, $dataLine);
                }
            } elseif($this->veisegoid == 32){ //TOKIO MARINE BRASIL
                if($total_lines < 3){
                    throw new Exception("Layout do arquivo inválido.");                    
                }
                
                while(!feof($file)){
                    $count_lines++;
                    
                    if($count_lines > $total_lines){
                        break;
                    }
                    
                    //Linha que está sendo lida
                    $line = fgets($file);
                    
                    //HEADER
                    if($count_lines == 1){
//                        echo $line;
                        if(strpos($line, ";") > 0){
                            $dados = explode(";", $line);
                            print_r($dados);
                        } else{
                            throw new Exception("Erro na linha: ".$count_lines.". <b>HEADER</b>: O layout está fora do padrão.");
                        }
                        
                        if(!empty($dados)){                        
                            $data_geracao    = $dados[0];
                            $empresa         = $dados[1];
                            $cnpj_prestadora = $dados[2]; //CNPJ da Prestadora de Serviço
                            $prestadora      = $dados[3]; //Nome da Prestadora de Serviço
                            
                            $cnpj_prestadora = explode(":", $cnpj_prestadora);
                            $cnpj_prestadora = $cnpj_prestadora[1];
                            
                            //CNPJ SASCAR
                            if($cnpj_prestadora != "3112879000151" || $cnpj_prestadora != "03112879000151"){
                                throw new Exception("Erro na linha: ".$count_lines.". Esse arquivo não é valido, o CNPJ da prestadora de serviço é inválido.");
                            }
                            
                        } else{
                            throw new Exception("Erro na linha: ".$count_lines.". <b>HEADER</b> está vazia.");                    
                        }
                    } elseif($count_lines > 1 && $count_lines < $total_lines){                        
                        //MOVIMENTO
                        if(strpos($line, ";") > 0){
                            $dados = explode(";", $line);
                        } else{
                            throw new Exception("Erro na linha: ".$count_lines.". O layout está fora do padrão.");                    
                        }
                        
                        if(!empty($dados)){
                            //Array que contem as informações da linha que está sendo lida.
                            $dataLine = array("cnpj" => $dados[0], "prestadora_servico" => $dados[1], "tipo_bem_segurado" => $dados[2],
                                              "carac_bem_segurado" => $dados[3], "ramo" => $dados[4], "tipo_dispositivo" => $dados[5],
                                              "codigo_veiculo" => $dados[6], "desc_veiculo" => $dados[7], "cod_modelo_veiculo" => $dados[8],
                                              "desc_modelo_veiculo" => $dados[9], "ano_modelo_veiculo" => $dados[10], "placa" => $dados[11],
                                              "chassi" => $dados[12], "cod_segurado" => $dados[13], "nome_segurado" => $dados[14],
                                              "cpf_cnpj_segurado" => $dados[15], "estabelecimento" => $dados[16], "digito_segurado" => $dados[17],
                                              "cep" => $dados[18], "endereco" => $dados[19], "complemento" => $dados[20], "numero" => $dados[21],
                                              "bairro" => $dados[22], "municipio" => $dados[23], "uf" => $dados[24], "ddd_segurado" => $dados[25],
                                              "telefone_segurado" => $dados[26], "local" => $dados[27], "sub_local" => $dados[28],
                                              "corretor" => $dados[29], "divisao_corretor" => $dados[30], "ddd_corretor" => $dados[31],
                                              "telefone_corretor" => $dados[32], "email_corretor" => $dados[33], "laudo_antifurto" => $dados[34],
                                              "antifurto_empresa" => $dados[35], "evento_laudo_af" => $dados[36], "data_laudo" => $dados[37],
                                              "motivo_frustacao" => $dados[38]);
                                              
                            $this->processarArquivo($arquivo, $dataLine);
                        } else{
                            throw new Exception("Erro na linha: ".$count_lines.". Dados estão vazios.");                    
                        }
                    } elseif($count_lines == $total_lines){
                        //TRAILLER
                        if(strpos($line, ";") > 0){
                            $dados = explode(";", $line);
                        } else{
                            throw new Exception("Erro na linha: ".$count_lines.". <b>TRAILLER</b>: O layout está fora do padrão.");                   
                        }
                        
                        if(!empty($dados)){
                            $cnpj_emp_prestadora = $dados[0];
                            $qtde_movimentos     = $dados[1];
                        } else{
                            throw new Exception("Erro na linha: ".$count_lines.". <b>TRAILLER</b> está vazia.");                    
                        }                        
                    }
                }                
            }
            
            //success
            fclose($file);       
            $this->gerarArquivoRetorno($arquivo);
            $this->dao->commitTransaction();
            echo "Arquivo processado com sucesso! ".$this->msg;
            
        } catch(Exception $e){
            //fclose($file);            
            $this->dao->rollbackTransaction();
            $this->dao->setStatusArquivo($prpsqoid,'N');
            echo $e->getMessage();
        }
    }
    
    /**
     * @param String $arquivo
     * @param array $dataLine
     */    
    private function processarArquivo($arquivo, $dataLine){
        if($this->prpstpcoid == 884 || $this->prpstpcoid == 919){
            /*
             * TOKIO MARINE OU TOKIO MARINE RF
             */
             
            //Parâmetros que vem da linha do arquivo
            $identificacao_negocio  = $dataLine['identificacao_negocio'];
            $numero_endosso         = $dataLine['numero_endosso'];
            $desc_tipo_endosso      = $dataLine['desc_tipo_endosso'];
            $cod_sucursal           = $dataLine['cod_sucursal'];
            $cod_evento_solicitacao = $dataLine['cod_evento_solicitacao'];
            $numero_negocio         = $dataLine['numero_negocio'];
            $placa                  = $dataLine['placa'];
            $chassi                 = $dataLine['chassi'];
            $cod_apolice_unica      = $dataLine['cod_apolice_unica'];
            $cod_apolice_especifica = $dataLine['cod_apolice_especifica'];
            $cpf_cnpj               = $dataLine['cpf_cnpj'];
            $nome_corretora         = $dataLine['nome_corretora'];
            $nome_segurado          = $dataLine['nome_segurado'];
            $email_corretora        = $dataLine['email_corretora'];
            $dt_inicio_vigencia     = $dataLine['dt_inicio_vigencia'];
            $dt_fim_vigencia        = $dataLine['dt_fim_vigencia'];
            $endereco               = $dataLine['endereco'];
            $bairro                 = $dataLine['bairro'];
            $cidade                 = $dataLine['cidade'];
            $estado                 = $dataLine['estado'];
            $ddd                    = $dataLine['ddd'];
            $telefone               = $dataLine['telefone'];
            $numero                 = $dataLine['numero'];
            $cep                    = $dataLine['cep'];
            $desc_fabricante        = $dataLine['desc_fabricante'];
            $desc_modelo            = $dataLine['desc_modelo'];
            $ddd_corretora          = $dataLine['ddd_corretora'];
            $telefone_corretora     = $dataLine['telefone_corretora'];
            $ano_modelo             = $dataLine['ano_modelo'];
            $numero_item            = $dataLine['numero_item'];        
            
            //Tipo Contrato
            $result = $this->dao->getTipoContratoParametrizacao($this->prpstpcoid);
            
            $tcpprazo_instalacao     = (int) $result["tcpprazo_instalacao"];
            $tcpdias_quarentena      = $result["tcpdias_quarentena"];
            $tcpdia_corte_quarentena = $result["tcpdia_corte_quarentena"];
            $tcpemail_retorno        = $result["tcpemail_retorno"];
            
            //Parâmetros Borderô
            $tipo_solicitacao  = $identificacao_negocio;
            $tipo_doc          = $tipo_solicitacao;
            $endosso           = $numero_endosso;
            $tipo_endosso      = $desc_tipo_endosso;
            $sucursal          = $cod_sucursal;
            $evento_solicitado = (int) $cod_evento_solicitacao;
            $proposta          = $numero_negocio;
            $placa             = $placa;
            $placa_seguradora  = $placa;
            $chassi            = $chassi;
            $chassi_seguradora = $chassi;
            $numapolice        = $cod_apolice_unica;
            $numitemapolice    = $cod_apolice_especifica;
            $numero_item       = $numitemapolice;
            
            if($evento_solicitado == 1){
                $prazoinst = "'".date("Y-m-d",time()+3600*24*$tcpprazo_instalacao)."'";
            } else{
                $prazoinst = "NULL";
            }     

            if($evento_solicitado == 1){
                $bsegprpsaoid = 1;
            } elseif($evento_solicitado == 2){
                $bsegprpsaoid = 7;
            } elseif($evento_solicitado == 3){
                $bsegprpsaoid = 11;
            }        
            
            //@return int primary_key        
            $bsegoid = $this->dao->inserirBorderoSeguradora($this->prpstpcoid,$prazoinst,$proposta,$placa,$chassi,$numapolice,$tipo_solicitacao,$numitemapolice,$bsegprpsaoid,$arquivo,$this->bsegcia);
            
            if($bsegoid == 0){
                throw new Exception("Erro.: Não foi possível inserir o registro na Borderô Seguradora.");                    
            }
            
            $this->identificarEventos($tipo_solicitacao,$evento_solicitado,$proposta,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$tipo_doc,$cpf_cnpj,$nome_corretora,$nome_segurado,$prazoinst,$numero_negocio,$cod_evento_solicitacao,$cod_apolice_unica,$cod_apolice_especifica,$email_corretora,$dt_inicio_vigencia,$dt_fim_vigencia,$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$identificacao_negocio,$desc_fabricante,$desc_modelo,$ddd_corretora,$telefone_corretora,$ano_modelo,$numero_item,$cod_sucursal);
        
        } elseif($this->prpstpcoid == 883){
            /*
             * TOKIO MARINE BRASIL
             */             
            //Tipo Contrato
            $result = $this->dao->getTipoContratoParametrizacao($this->prpstpcoid);
            
            $tcpprazo_instalacao     = (int) $result["tcpprazo_instalacao"];
            $tcpdias_quarentena      = $result["tcpdias_quarentena"];
            $tcpdia_corte_quarentena = $result["tcpdia_corte_quarentena"];
            $tcpemail_retorno        = $result["tcpemail_retorno"];
            
            //Parâmetros Borderô            
            $evento_solicitado = (int) $dataLine['evento_laudo_af'];
            $sequencial        = (int) $this->dao->getSequencialTokioMarineBrasil();
            $proposta          = date('ymd').$sequencial;
            $placa             = $dataLine['placa'];
            $placa_seguradora  = $placa;
            $chassi            = $dataLine['chassi'];
            $chassi_seguradora = $chassi;
            $numapolice        = $dataLine['cod_segurado'];
            $numitemapolice    = $dataLine['codigo_veiculo'];
            $numero_item       = $numitemapolice;            
            $endosso           = 0;
            $tipo_endosso      = "NULL";
            $sucursal          = 0;
            
            //Acrescenta +1 ao sequencial.
            $result = $this->dao->setSequencialTokioMarineBrasil($sequencial);
            
            if($result == 0){
                throw new Exception("Erro.: Não foi possível atualizar o sequencial Tokio Marine Brasil.");
            }
            
            if($evento_solicitado == 0){
                $evento_solicitado = 1;
            }
            
            if($evento_solicitado == 1){
                $prazoinst = "'".date("Y-m-d",time()+3600*24*$tcpprazo_instalacao)."'";
            } else{
                $prazoinst = "NULL";
            }

            if($evento_solicitado == 1){
                $bsegprpsaoid     = 1;
                $tipo_solicitacao = "INS";
            } elseif($evento_solicitado == 2){
                $bsegprpsaoid     = 7;
                $tipo_solicitacao = "RET";
            } elseif($evento_solicitado == 3){
                $bsegprpsaoid     = 11;
                $tipo_solicitacao = "REV";
            }
            
            $tipo_doc               = $tipo_solicitacao;
            $cpf_cnpj               = $dataLine['cnpj'];            
            $nome_corretora         = $dataLine['divisao_corretor'];
            $nome_segurado          = $dataLine['nome_segurado'];
            $numero_negocio         = $proposta;
            $cod_evento_solicitacao = $evento_solicitado;
            $cod_apolice_unica      = $dataLine['cod_segurado'];
            $cod_apolice_especifica = (int) $dataLine['codigo_veiculo'];            
            $email_corretora        = $dataLine['email_corretor'];
            $dt_inicio_vigencia     = date("Ymd",time());
            $dt_fim_vigencia        = date("Ymd",time()+3600*24*360);    
            $endereco               = $dataLine['endereco'];
            $bairro                 = $dataLine['bairro'];
            $cidade                 = $dataLine['municipio'];
            $estado                 = $dataLine['uf'];    
            $ddd                    = $dataLine['ddd_segurado'];
            $telefone               = $dataLine['telefone_segurado'];
            $numero                 = $dataLine['numero'];
            $cep                    = $dataLine['cep'];
            $identificacao_negocio  = 0;
            $desc_fabricante        = ($dataLine['desc_veiculo'] != "") ? $dataLine['desc_veiculo'] : "";
            $desc_modelo            = ($dataLine['desc_modelo_veiculo'] != "") ? $dataLine['desc_modelo_veiculo'] : "";
            $ddd_corretora          = $dataLine['ddd_corretor'];
            $telefone_corretora     = $dataLine['telefone_corretor'];
            $ano_modelo             = $dataLine['ano_modelo_veiculo'];
            $cod_sucursal           = "NULL";            
            $tipo_dispositivo       = (int) $dataLine['tipo_dispositivo'];
            $cod_modelo_veiculo     = (int) $dataLine['cod_modelo_veiculo'];
            $cpf_cnpj_segurado      = $this->formatarCpfCnpj($dataLine['cpf_cnpj_segurado']);
            $cod_estabelecimento    = (int) $dataLine['estabelecimento'];
            $cod_digito_segurado    = (int) $dataLine['digito_segurado'];  
            $local                  = (int) $dataLine['local'];
            $sub_local              = (int) $dataLine['sub_local'];
            $corretor               = (int) $dataLine['corretor'];
            $laudo_antifurto        = (int) $dataLine['laudo_antifurto'];
            
            //Informações utilizadas após cada nova proposta gerada (Tokio Marine Brasil) - para que o arquivo de retorno esteja + completo possivel
            $this->dadosProposta = array("tipo_dispositivo" => $tipo_dispositivo, "cod_apolice_especifica" => $cod_apolice_especifica,
                                         "desc_fabricante" => $desc_fabricante, "cod_modelo_veiculo" => $cod_modelo_veiculo,
                                         "desc_modelo" => $desc_modelo, "cpf_cnpj_segurado" => $cpf_cnpj_segurado,
                                         "cod_estabelecimento" => $cod_estabelecimento, "cod_digito_segurado" => $cod_digito_segurado,
                                         "local" => $local, "sub_local" => $sub_local, "corretor" => $corretor, "laudo_antifurto" => $laudo_antifurto);
            
            //@return int primary_key        
            $bsegoid = $this->dao->inserirBorderoSeguradora($this->prpstpcoid,$prazoinst,$proposta,$placa,$chassi,$numapolice,$tipo_solicitacao,$numitemapolice,$bsegprpsaoid,$arquivo,$this->bsegcia);
            
            if($bsegoid == 0){
                throw new Exception("Erro.: Não foi possível inserir o registro na Borderô Seguradora.");                    
            }
            
            $this->identificarEventos($tipo_solicitacao,$evento_solicitado,$proposta,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$tipo_doc,$cpf_cnpj,$nome_corretora,$nome_segurado,$prazoinst,$numero_negocio,$cod_evento_solicitacao,$cod_apolice_unica,$cod_apolice_especifica,$email_corretora,$dt_inicio_vigencia,$dt_fim_vigencia,$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$identificacao_negocio,$desc_fabricante,$desc_modelo,$ddd_corretora,$telefone_corretora,$ano_modelo,$numero_item,$cod_sucursal);
        }
    }
    
    /**
     * Trata os tipos de eventos de acordo com o tipo da solicitação.
     * @param String $tipo_solicitacao
     * @param int $evento_solicitado
     * @param String $proposta
     * @param String $placa
     * @param String $placa_seguradora
     * @param String $chassi
     * @param String $chassi_seguradora
     * @param String $numapolice
     * @param String $numitemapolice
     * @param String $tipo_doc
     * @param String $cpf_cnpj
     * @param String $nome_corretora
     * @param String $nome_segurado
     * @param String $prazoinst
     * @param String $numero_negocio
     * @param int $cod_evento_solicitacao
     * @param int $cod_apolice_unica
     * @param int $cod_apolice_especifica
     * @param String $email_corretora
     * @param int $dt_inicio_vigencia
     * @param int $dt_fim_vigencia
     * @param String $endereco
     * @param String $bairro
     * @param String $cidade
     * @param String $estado
     * @param String $ddd
     * @param String $telefone
     * @param int $numero
     * @param int $cep
     * @param String $identificacao_negocio
     * @param String $desc_fabricante
     * @param String $desc_modelo
     * @param String $ddd_corretora
     * @param String $telefone_corretora
     * @param int $ano_modelo
     * @param int $numero_item
     * @param int $cod_sucursal
     */
    private function identificarEventos($tipo_solicitacao,$evento_solicitado,$proposta,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$tipo_doc,$cpf_cnpj,$nome_corretora,$nome_segurado,$prazoinst,$numero_negocio,$cod_evento_solicitacao,$cod_apolice_unica,$cod_apolice_especifica,$email_corretora,$dt_inicio_vigencia,$dt_fim_vigencia,$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$identificacao_negocio,$desc_fabricante,$desc_modelo,$ddd_corretora,$telefone_corretora,$ano_modelo,$numero_item,$cod_sucursal){
        //Remove ponto, hífen e barra.
        $cpf_cnpj_formatado = $this->formatarCpfCnpj($cpf_cnpj);
        
        if($tipo_solicitacao == 'PRO'){        
            if($evento_solicitado == 1){
                $this->tratarInstalacao($tipo_solicitacao,$evento_solicitado,$proposta,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$tipo_doc,$cpf_cnpj,$nome_corretora,$nome_segurado,$prazoinst,$cpf_cnpj_formatado,$numero_negocio,$cod_evento_solicitacao,$cod_apolice_unica,$cod_apolice_especifica,$email_corretora,$dt_inicio_vigencia,$dt_fim_vigencia,$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$identificacao_negocio,$desc_fabricante,$desc_modelo,$ddd_corretora,$telefone_corretora,$ano_modelo,$numero_item,$cod_sucursal);
            } elseif($evento_solicitado == 2){
                $this->tratarRetirada($proposta,$cpf_cnpj,$cpf_cnpj_formatado,$tipo_doc,$numapolice,$numitemapolice,$placa,$chassi);
            } elseif($evento_solicitado == 3){
                $this->tratarRevisao($proposta,$cpf_cnpj,$cpf_cnpj_formatado,$chassi,$chassi_seguradora,$numero_negocio,$cod_evento_solicitacao,$placa,$placa_seguradora,$cod_apolice_unica,$cod_apolice_especifica,$email_corretora,$nome_segurado,$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$identificacao_negocio,$nome_corretora,$nome_corretora,$desc_fabricante,$desc_modelo,$ddd_corretora,$telefone_corretora,$ano_modelo,$numero_item,$cod_sucursal);
            }            
        } elseif($tipo_solicitacao == 'END'){
            if($evento_solicitado == 1){
                $this->tratarAtualizacaoInstalacao($cpf_cnpj, $cpf_cnpj_formatado, $chassi, $chassi_seguradora, $proposta, $tipo_doc);
            } elseif($evento_solicitado == 2){
                $this->tratarAtualizacaoRetirada($proposta,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice);
            }
        } elseif($tipo_solicitacao == 'REN'){
            if($evento_solicitado == 1){
                $this->tratarCancelamentoInstalacao($proposta,$cpf_cnpj,$cpf_cnpj_formatado,$tipo_doc);
            } elseif($evento_solicitado == 2){
                $this->tratarRetirada($proposta,$cpf_cnpj,$cpf_cnpj_formatado,$tipo_doc,$numapolice,$numitemapolice,$placa,$chassi);
            }
        } else{
            $tipo_solicitacao = 'PRO';
            $this->identificarEventos($tipo_solicitacao,$evento_solicitado,$proposta,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$tipo_doc,$cpf_cnpj,$nome_corretora,$nome_segurado,$prazoinst,$numero_negocio,$cod_evento_solicitacao,$cod_apolice_unica,$cod_apolice_especifica,$email_corretora,$dt_inicio_vigencia,$dt_fim_vigencia,$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$identificacao_negocio,$desc_fabricante,$desc_modelo,$ddd_corretora,$telefone_corretora,$ano_modelo,$numero_item,$cod_sucursal);
        }
    }
    
    /**
     * @param String $from
     * @param String $fromName
     * @param String $subject
     * @param String $message
     * @param String $to
     * @param String $path Path to the attachment.
     */
    private function enviarEmail($from, $fromName, $subject, $message, $to, $path = null){
        $mail = new PHPMailer();
        $mail->ClearAllRecipients();

        $mail->IsSMTP();
        
        $mail->From     = $from;
        $mail->FromName = $fromName;
        $mail->Subject  = $subject;
        
        $mail->MsgHTML($message);        
        $mail->AddAddress($to);
        
        if($path != null){
            $mail->AddAttachment($path);
        }  
        
        $mail->Send();
    }
    
    /**
     * INSTALAÇÃO.
     * @param String $tipo_solicitacao
     * @param int $evento_solicitado
     * @param String $proposta
     * @param String $placa
     * @param String $placa_seguradora
     * @param String $chassi
     * @param String $chassi_seguradora
     * @param String $numapolice
     * @param String $numitemapolice
     * @param String $tipo_doc
     * @param String $cpf_cnpj
     * @param String $nome_corretora
     * @param String $nome_segurado
     * @param String $prazoinst
     * @param int $cpf_cnpj_formatado
     * @param String $numero_negocio
     * @param int $cod_evento_solicitacao
     * @param int $cod_apolice_unica
     * @param int $cod_apolice_especifica
     * @param String $email_corretora
     * @param int $dt_inicio_vigencia
     * @param int $dt_fim_vigencia
     * @param String $endereco
     * @param String $bairro
     * @param String $cidade
     * @param String $estado
     * @param String $ddd
     * @param String $telefone
     * @param int $numero
     * @param int $cep
     * @param String $identificacao_negocio
     * @param String $desc_fabricante
     * @param String $desc_modelo
     * @param String $ddd_corretora
     * @param String $telefone_corretora
     * @param int $ano_modelo
     * @param int $numero_item
     * @param int $cod_sucursal
     */                                                                                                                                                                                                                                              
    private function tratarInstalacao($tipo_solicitacao,$evento_solicitado,$proposta,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$tipo_doc,$cpf_cnpj,$nome_corretora,$nome_segurado,$prazoinst,$cpf_cnpj_formatado,$numero_negocio,$cod_evento_solicitacao,$cod_apolice_unica,$cod_apolice_especifica,$email_corretora,$dt_inicio_vigencia,$dt_fim_vigencia,$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$identificacao_negocio,$desc_fabricante,$desc_modelo,$ddd_corretora,$telefone_corretora,$ano_modelo,$numero_item,$cod_sucursal){
        //Verificando se a proposta existe.
        if($this->prpstpcoid == 884 || $this->prpstpcoid == 919){
            $result = $this->dao->getProposta($proposta);
        } elseif($this->prpstpcoid == 883){
            $result = $this->dao->getProposta($proposta, $cpf_cnpj, $chassi);
        }      
        
        if($result != null){
            $prpsoid        = $result["prpsoid"];
            $prpstpcoid     = $result["prpstpcoid"];
            $prpsprazo_inst = $result["prpsprazo_inst"];
            $prpsveioid     = $result["prpsveioid"];
            
            if($prpsveioid > 0){
                //Atualiza a proposta com os dados do veículo.
                $result = $this->dao->atualizarPropostaDadosVeiculo($placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$prpsoid);
                
                if($result == 0){
                    throw new Exception("Erro.: Não foi possível atualizar a proposta <b>".$proposta."</b> com os dados do veículo.");
                }
                
                //Atualiza os dados do veículo.
                $result = $this->dao->atualizarDadosVeiculo($numapolice,$this->id_usuario,$proposta,$numitemapolice,$tipo_doc,$chassi,$placa,$prpsveioid);
                
                if($result == 0){
                    throw new Exception("Erro.: Não foi possível atualizar os dados do veículo. Placa.: $placa e Chassi.: $chassi");
                }
            }

            //Verificando se existe o contrato.
            $result = $this->dao->getContrato($this->prpstpcoid,$proposta,$cpf_cnpj,$cpf_cnpj_formatado);
            
            if($result != null){
                $connumero  = $result["connumero"];
                $conclioid  = $result["conclioid"];
                $conequoid  = $result["conequoid"];
                $conno_tipo = $result["conno_tipo"];
                
                //Se o contrato consta em quarentena, atualizar 'condt_quarentena_seg' para NULL
                $campo = "condt_quarentena_seg";
                $valor = "NULL";
                
                $result = $this->dao->atualizarContrato($campo,$valor,$connumero);
                
                if($result == 0){
                    throw new Exception("Erro.: Não foi possível atualizar o campo <b>$campo</b> para <b>$valor</b> referente ao contrato: <b>$connumero</b>");
                }                        
                
                //Verifica se existe equipamento
                if($conequoid > 0){                
                    $result = $this->dao->inserirContratoAlteracaoSeguradora($prpstpcoid,$connumero,$conequoid,$conclioid,$prpsveioid,$tipo_doc);
                    
                    if($result == 0){
                        throw new Exception("Erro.: Não foi possível inserir o Contrato Alteração Seguradora. Contrato: <b>$connumero</b>");
                    }
                    
                    //Ultimo processamento da proposta
                    $result = $this->dao->atualizarProposta($prpsoid,2,5,true,null);
                    
                    if($result == 0){
                        throw new Exception("Erro.: Não foi possível atualizar o último processamento da proposta.");
                    }
                    
                    $observacao = "Proposta Válida vinculada a um contrato Ativo. Enviada Informação de Equipamento Instalado.";
                    $result     = $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 5, $tipo_doc, $observacao, 2, $this->id_usuario);
                    
                    if($result == 0){
                        throw new Exception("Erro.: Não foi possível inserir o histórico da proposta (Último processamento).");
                    }

                } else{
                    //Ação da proposta
                    $result = $this->dao->atualizarProposta($prpsoid,2,1,false,null);
                    
                    if($result == 0){
                        throw new Exception("Erro.: Não foi possível atualizar a última ação da proposta.");
                    }                            
                    
                    $observacao = "Proposta Válida vinculada a um contrato Ativo sem equipamento Instalado.";
                    $result     = $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 1, $tipo_doc, $observacao, 2, $this->id_usuario);
                    
                    if($result == 0){
                        throw new Exception("Erro.: Não foi possível inserir o histórico da proposta (Ação da proposta).");
                    }

                    //Verifica se tem O.S. de Instalação pendente.
                    $result = $this->dao->getInstalacaoPendente($connumero);
                    
                    if($result != null){
                        $ordoid          = $result["ordoid"];
                        $ordacomp_usuoid = $result["ordacomp_usuoid"];
                        
                        //Verifica se tem agendamento para os próximos dias.
                        $result = $this->dao->getAgendamento($ordoid);
                        
                        //Ultimo processamento da proposta
                        $this->dao->atualizarProposta($prpsoid,2,5,true,null);
                        
                        if($result > 0){
                            $this->dao->inserirArquivoSeguradora($prpstpcoid,$proposta,$tipo_doc,"INS",$numapolice,$numitemapolice,3);
                            
                            $observacao = "Proposta Válida vinculada a um contrato Ativo. Enviada Informação a CIA de Instalação Agendada.";
                            $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 5, $tipo_doc, $observacao, 2, $this->id_usuario);
                        } else{
                            //Busca o e-mail do responsável pela O.S
                            $result = $this->dao->getEmail($ordoid);
                            
                            if($result != null){
                                //ENVIA E-MAIL PARA O RESPONSÁVEL PELA O.S. COM O PRAZO DE INSTALAÇÃO.                                      
                                $from     = "sascar@sascar.com.br";
                                $fromName = "Sascar";
                                $subject  = "O.S. - Prazo de instalação";
                                $to       = $result;
                                
                                if(!strstr($_SERVER['HTTP_HOST'], 'intranet')){
                                    $to = "teste_desenv@sascar.com.br";
                                }                                
                                
                                $message = "<table style='border:solid 1px !important;'>
                                                <tbody>
                                                    <tr>
                                                        <td>Nome Seguradora:</td> <td>$nome_corretora</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nome Segurado:</td> <td>$nome_segurado</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Placa:</td> <td>$placa</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Chassi:</td> <td>$chassi</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nº do Documento (Proposta):</td> <td>$proposta</td>
                                                    </tr>
                                                    <tr>
                                                        <td>O.S. Nº:</td> <td>$ordoid</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Novo Prazo informado pela Seguradora:</td> <td>$prazoinst</td>
                                                    </tr>
                                                </tbody>
                                            </table>";
                                            
                                $this->enviarEmail($from, $fromName, $subject, $message, $to);
                            }
                        }
                        
                    } else{
                        //Insere no histórico "AGUARDANDO VERIFICAÇÃO MANUAL"
                        $observacao = "Aguardando Verificação Manual. Aguardando Agendamento.";
                        $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 1, $tipo_doc, $observacao, 1, $this->id_usuario);
                        
                        //Atualiza a proposta para verificação manual
                        $this->dao->atualizarProposta($prpsoid, 1, null, true, 't');
                    }
                }
            } else{
                //Verificando se o veículo existe
                $result = $this->dao->getVeiculo($chassi, $chassi_seguradora);                        
                
                if($result != null){
                    $veioid = $result['veioid'];                            
                    $this->dao->atualizarDadosVeiculo($numapolice,$this->id_usuario,$proposta,$numitemapolice,$tipo_doc,$chassi,$placa,$veioid);
                }
                
                //Verificando se é uma renovação
                $result = $this->dao->getRenovacao($chassi, $chassi_seguradora, $cpf_cnpj, $cpf_cnpj_formatado, $proposta);
                
                if($result != null){
                    $veioid         = $result['veioid'];
                    $veino_proposta = $result['veino_proposta'];
                    $connumero      = $result['connumero'];
                    $conclioid      = $result['conclioid'];
                    $conequoid      = $result['conequoid'];
                    
                    $campo  = "condt_quarentena_seg";
                    $valor  = "NULL";                            
                    $result = $this->dao->atualizarContrato($campo,$valor,$connumero);
                    
                    if($result == 0){
                        throw new Exception("Erro.: Não foi possível atualizar o contrato.: ".$connumero);
                    }
                    
                    //Retorna o prazo parametrizado conforme o tipo de contrato.
                    $prpsprazo_inst = $this->dao->getPrazoInstalacaoParametrizado($this->prpstpcoid);
                    
                    //Mudando formato da data para: dd/mm/YYYY
                    $inicio_vigencia = substr($dt_inicio_vigencia,6,2)."/".substr($dt_inicio_vigencia,4,2)."/".substr($dt_inicio_vigencia,0,4);
                    $fim_vigencia    = substr($dt_fim_vigencia,6,2)."/".substr($dt_fim_vigencia,4,2)."/".substr($dt_fim_vigencia,0,4);
                    
                    $prpsscnpj_cpf = $this->formatarCpfCnpj($cpf_cnpj);
                    
                    if(strlen($prpsscnpj_cpf) == 11){
                        $prpstipo_pessoa = "F";
                    } else{
                        $prpstipo_pessoa = "J";
                    }
                    
                    $observacaoGeral = $this->getObservacaoGeral($identificacao_negocio,$cod_evento_solicitacao,$prpsprazo_inst,$nome_corretora,$ddd_corretora,$telefone_corretora,$email_corretora,$desc_fabricante,$desc_modelo,$placa,$chassi,$numero_negocio,$cod_apolice_unica,$numero_item,$cod_sucursal,$ano_modelo);
                    
                    // INSERE NOVA PROPOSTA DE RENOVAÇÃO
                    $result = $this->dao->inserirProposta($numero_negocio,$this->prpstpcoid,$cod_evento_solicitacao,"now()",$prpsprazo_inst,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$cod_apolice_unica,$cod_apolice_especifica,"null",$email_corretora,"null","null",1,$inicio_vigencia,$fim_vigencia,$observacaoGeral,"now()",$cod_evento_solicitacao,"false","false","null",$prpsscnpj_cpf,$prpstipo_pessoa,$nome_segurado,"null",$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$this->veisegoid,$identificacao_negocio,"null","null","now()",$nome_corretora,$nome_corretora,$desc_fabricante,$desc_modelo,"null","null");
                    
                    if($result != null){
                        $prpsoid    = $result;
                        $observacao = "Cadastro da Proposta";
                        $this->dao->inserirPropostaSeguradoraHistorico($prpsoid,5,'PRO',$observacao,1,$this->id_usuario,$cod_apolice_unica,$cod_apolice_especifica,'SEG');
                        
                        //Tokio Marine Brasil - Informações que serão usadas para o arquivo de retorno
                        if($this->prpstpcoid == 883){
                            $this->dao->atualizarDadosProposta($prpsoid, $this->dadosProposta['tipo_dispositivo'], $this->dadosProposta['cod_apolice_especifica'], $this->dadosProposta['desc_fabricante'], $this->dadosProposta['cod_modelo_veiculo'], $this->dadosProposta['desc_modelo'], $this->dadosProposta['cpf_cnpj_segurado'], $this->dadosProposta['cod_estabelecimento'], $this->dadosProposta['cod_digito_segurado'], $this->dadosProposta['local'], $this->dadosProposta['sub_local'], $this->dadosProposta['corretor'],  $this->dadosProposta['laudo_antifurto']);
                        }
                    } else{
                        throw new Exception("Erro.: Não foi possível criar a proposta de renovação: <b>".$proposta."</b>.");
                    }                    
                    
                    if($conequoid > 0){                    
                        $result = $this->dao->atualizarPropostaSeguradoraSegurado($prpsoid);
                        
                        if($result == 0){
                            throw new Exception("Erro.: Não foi possível atualizar.: Proposta Seguradora Segurado.");
                        }

                        //Atualiza os dados do veículo.
                        $this->dao->atualizarDadosVeiculo($numapolice,$this->id_usuario,$proposta,$numitemapolice,$tipo_doc,$chassi,$placa,$veioid);
                        
                        //Insere histórico de navegação.
                        $observacao = "Contrato Renovado via solicitação da seguradora - Proposta: ".$proposta;
                        $this->dao->historico_termo_i($connumero,$this->id_usuario,$observacao);

                        //Cadastra resposta automática de instalação realizada.
                        $observacao = "RESPOSTA AUTOMATICA DE INSTALACAO REALIZADA";                                           
                        $result = $this->dao->inserirContratoAlteracaoSeguradora($prpstpcoid,$connumero,$conequoid,$conclioid,$veioid,$tipo_doc,$observacao);
                        
                        if($result == 0){
                            throw new Exception("Erro.: Não foi possível inserir a Resposta Automática de Instalação Realizada.");
                        }
                        
                        $observacao = "Renovação Automática. Enviada Resposta Automática de Instalação Realizada.";
                        $this->dao->inserirPropostaSeguradoraHistorico($prpsoid,1,$tipo_doc,$observacao,2,$this->id_usuario);                        
                        $this->dao->atualizarProposta($prpsoid, 2, 5, true, null);                        
                    } else{
                        //TEM CONTRATO DE RENOVAÇÃO SEM EQUIPAMENTO
                        
                        //Atualiza os dados do veículo com a prazo de instalação.
                        $this->dao->atualizarDadosVeiculo($numapolice,$this->id_usuario,$proposta,$numitemapolice,$tipo_doc,$chassi,$placa,$veioid,$prazoinst);
                        
                        //Insere histórico de renovação.
                        $observacao = "Contrato renovado via solicitação da seguradora - Proposta: $proposta";
                        $this->dao->historico_termo_i($connumero,$this->id_usuario,$observacao);
                        
                        //Proposta histórico
                        $observacao = "Renovação Automática. Nº de Proposta Alterado. Contrato Cadastrado sem Equipamento Instalado.";
                        $this->dao->inserirPropostaSeguradoraHistorico($prpsoid,1,$tipo_doc,$observacao,2,$this->id_usuario);
                        
                        //Atualizar proposta: último processamento e ação.
                        $this->dao->atualizarProposta($prpsoid, 2, 1, true, null);                                
                    }
                    
                } else{                 
                    //Retorna o prazo parametrizado conforme o tipo de contrato.
                    $prpsprazo_inst = $this->dao->getPrazoInstalacaoParametrizado($this->prpstpcoid);
                    
                    //Mudando formato da data para: dd/mm/YYYY
                    $inicio_vigencia = substr($dt_inicio_vigencia,6,2)."/".substr($dt_inicio_vigencia,4,2)."/".substr($dt_inicio_vigencia,0,4);
                    $fim_vigencia    = substr($dt_fim_vigencia,6,2)."/".substr($dt_fim_vigencia,4,2)."/".substr($dt_fim_vigencia,0,4);
                    
                    $prpsscnpj_cpf = $this->formatarCpfCnpj($cpf_cnpj);
                    
                    if(strlen($prpsscnpj_cpf) == 11){
                        $prpstipo_pessoa = "F";
                    } else{
                        $prpstipo_pessoa = "J";
                    }             
                    
                    //Verificando se a proposta existe.
                    if($this->prpstpcoid == 884 || $this->prpstpcoid == 919){
                        $result = $this->dao->getProposta($numero_negocio);
                    } elseif($this->prpstpcoid == 883){
                        $result = $this->dao->getProposta($numero_negocio, $cpf_cnpj, $chassi);
                    }
                    
                    if($result != null){
                        $prpsoid = $result["prpsoid"];
                    } else{
                        $observacaoGeral = $this->getObservacaoGeral($identificacao_negocio,$cod_evento_solicitacao,$prpsprazo_inst,$nome_corretora,$ddd_corretora,$telefone_corretora,$email_corretora,$desc_fabricante,$desc_modelo,$placa,$chassi,$numero_negocio,$cod_apolice_unica,$numero_item,$cod_sucursal,$ano_modelo);
                        
                        // INSERE NOVA PROPOSTA
                        $prpsoid = $this->dao->inserirProposta($numero_negocio,$this->prpstpcoid,$cod_evento_solicitacao,"now()",$prpsprazo_inst,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$cod_apolice_unica,$cod_apolice_especifica,"null",$email_corretora,"null","null",1,$inicio_vigencia,$fim_vigencia,$observacaoGeral,"now()",$cod_evento_solicitacao,"false","false","null",$prpsscnpj_cpf,$prpstipo_pessoa,$nome_segurado,"null",$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$this->veisegoid,$identificacao_negocio,"null","null","now()",$nome_corretora,$nome_corretora,$desc_fabricante,$desc_modelo,"null","null");
                        
                        if($prpsoid != null){
                            $observacao = "Cadastro da Proposta";
                            $this->dao->inserirPropostaSeguradoraHistorico($prpsoid,5,'PRO',$observacao,1,$this->id_usuario,$cod_apolice_unica,$cod_apolice_especifica,'SEG');
                            
                            //Tokio Marine Brasil - Informações que serão usadas para o arquivo de retorno
                            if($this->prpstpcoid == 883){
                                $this->dao->atualizarDadosProposta($prpsoid, $this->dadosProposta['tipo_dispositivo'], $this->dadosProposta['cod_apolice_especifica'], $this->dadosProposta['desc_fabricante'], $this->dadosProposta['cod_modelo_veiculo'], $this->dadosProposta['desc_modelo'], $this->dadosProposta['cpf_cnpj_segurado'], $this->dadosProposta['cod_estabelecimento'], $this->dadosProposta['cod_digito_segurado'], $this->dadosProposta['local'], $this->dadosProposta['sub_local'], $this->dadosProposta['corretor'],  $this->dadosProposta['laudo_antifurto']);
                            }
                        }
                    }
                    
                    if($prpsoid != null){
                        $result = $this->dao->atualizarPropostaSeguradoraSegurado($prpsoid);                        
                        if($result == 0){
                            //throw new Exception("Erro.: Não foi possível atualizar a Proposta Seguradora Segurado. #prpsoid.: ".$prpsoid);
                        }
                    } else{
                        throw new Exception("Erro.: Não foi possível criar a Proposta.: ".$numero_negocio);
                    }    
                    
                }
                
                //Verifica se possui um Contrato Ex
                $result = $this->dao->getContratoExSeguradora($prpstpcoid,$chassi,$chassi_seguradora,$proposta,$cpf_cnpj,$cpf_cnpj_formatado);
                
                if($result != null){
                    $connumero  = $result["connumero"];
                    $conequoid  = $result["conequoid"];
                    $conclioid  = $result["conclioid"];
                    $conveioid  = $result["conveioid"];
                    $conno_tipo = $result["conno_tipo"];
                    
                    if($conveioid > 0){                            
                        $this->dao->atualizarDadosVeiculo($numapolice,$this->id_usuario,$proposta,$numitemapolice,$tipo_doc,$chassi,$placa,$conveioid);
                    }
                    
                    // MIGRA O CONTRATO PARA ATIVO            
                    $this->dao->migrarContrato($this->id_usuario, $prpstpcoid, $connumero, 0, $conno_tipo, 'f', '');                   
                    
                    $observacao = "Contrato Migrado de Ex- Par Ativo.";
                    $this->dao->inserirPropostaSeguradoraHistorico($prpsoid,1,$tipo_doc,$observacao,2,$this->id_usuario);

                    if ($conequoid > 0){
                        $this->dao->atualizarProposta($prpsoid, 2, 5, true, null);
                        
                        $observacao = "RESPOSTA AUTOMATICA DE INSTALACAO REALIZADA";
                        $this->dao->inserirContratoAlteracaoSeguradora($prpstpcoid,$connumero,$conequoid,$conclioid,$conveioid,$tipo_doc,$observacao);
                        
                    } else{
                        $this->dao->atualizarProposta($prpsoid, 2, 1, true, null);
                    } 
                }
            }
            
        } else{   
            //Retorna o prazo parametrizado conforme o tipo de contrato.
            $prpsprazo_inst = $this->dao->getPrazoInstalacaoParametrizado($this->prpstpcoid);
            
            //Mudando formato da data para: dd/mm/YYYY
            $inicio_vigencia = substr($dt_inicio_vigencia,6,2)."/".substr($dt_inicio_vigencia,4,2)."/".substr($dt_inicio_vigencia,0,4);
            $fim_vigencia    = substr($dt_fim_vigencia,6,2)."/".substr($dt_fim_vigencia,4,2)."/".substr($dt_fim_vigencia,0,4);
            
            $prpsscnpj_cpf = $this->formatarCpfCnpj($cpf_cnpj);
            $cpf_cnpj      = $prpsscnpj_cpf;
            
            if(strlen($prpsscnpj_cpf) == 11){
                $prpstipo_pessoa = "F";
            } else{
                $prpstipo_pessoa = "J";
            }
            
            $observacaoGeral = $this->getObservacaoGeral($identificacao_negocio,$cod_evento_solicitacao,$prpsprazo_inst,$nome_corretora,$ddd_corretora,$telefone_corretora,$email_corretora,$desc_fabricante,$desc_modelo,$placa,$chassi,$numero_negocio,$cod_apolice_unica,$numero_item,$cod_sucursal,$ano_modelo);
            
            $result = $this->dao->inserirProposta($numero_negocio,$this->prpstpcoid,$cod_evento_solicitacao,"now()",$prpsprazo_inst,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$cod_apolice_unica,$cod_apolice_especifica,"null",$email_corretora,"null","null",1,$inicio_vigencia,$fim_vigencia,$observacaoGeral,"now()",$cod_evento_solicitacao,"false","false","null",$prpsscnpj_cpf,$prpstipo_pessoa,$nome_segurado,"null",$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$this->veisegoid,$identificacao_negocio,"null","null","now()",$nome_corretora,$nome_corretora,$desc_fabricante,$desc_modelo,"null","null");
            
            if($result != null){
                $prpsoid    = $result;
                $observacao = "Cadastro da Proposta";
                $this->dao->inserirPropostaSeguradoraHistorico($prpsoid,5,'PRO',$observacao,1,$this->id_usuario,$cod_apolice_unica,$cod_apolice_especifica,'SEG');
                
                //Tokio Marine Brasil - Informações que serão usadas para o arquivo de retorno
                if($this->prpstpcoid == 883){
                    $this->dao->atualizarDadosProposta($prpsoid, $this->dadosProposta['tipo_dispositivo'], $this->dadosProposta['cod_apolice_especifica'], $this->dadosProposta['desc_fabricante'], $this->dadosProposta['cod_modelo_veiculo'], $this->dadosProposta['desc_modelo'], $this->dadosProposta['cpf_cnpj_segurado'], $this->dadosProposta['cod_estabelecimento'], $this->dadosProposta['cod_digito_segurado'], $this->dadosProposta['local'], $this->dadosProposta['sub_local'], $this->dadosProposta['corretor'],  $this->dadosProposta['laudo_antifurto']);
                }                
                
                $this->tratarInstalacao($tipo_solicitacao,$evento_solicitado,$proposta,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$tipo_doc,$cpf_cnpj,$nome_corretora,$nome_segurado,$prazoinst,$cpf_cnpj_formatado,$numero_negocio,$cod_evento_solicitacao,$cod_apolice_unica,$cod_apolice_especifica,$email_corretora,$dt_inicio_vigencia,$dt_fim_vigencia,$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$identificacao_negocio,$desc_fabricante,$desc_modelo,$ddd_corretora,$telefone_corretora,$cod_marca_modelo,$numero_item,$cod_sucursal);
            } else{
                throw new Exception("Erro.: Não foi possível criar a proposta: <b>".$proposta."</b>.");
            }           
        }
    }
    
    /**
     * DESINSTALAÇÃO
     * @param int $proposta
     * @param String $cpf_cnpj
     * @param String $cpf_cnpj_formatado
     * @param String $tipo_doc
     */
    private function tratarRetirada($proposta,$cpf_cnpj,$cpf_cnpj_formatado,$tipo_doc,$numapolice,$numitemapolice,$placa,$chassi){
        // LOCALIZA A PROPOSTA
        if($this->prpstpcoid == 884 || $this->prpstpcoid == 919){
            $result = $this->dao->getProposta($proposta);
        } elseif($this->prpstpcoid == 883){
            $result = $this->dao->getProposta($proposta, $cpf_cnpj, $chassi);
        }
        
        if($tipo_doc == "RET"){
            $tipo_doc = "PRO";
        }
        
        if($result != null){
            $prpsoid    = $result['prpsoid'];
            $prpstpcoid = $result['prpstpcoid'];

            // VERIFICA SE TEM CONTRATO
            $result = $this->dao->getContrato($this->prpstpcoid, $proposta, $cpf_cnpj, $cpf_cnpj_formatado, false);

            if($result != null){            
                $connumero  = $result['connumero'];
                $conclioid  = $result['conclioid'];
                $conequoid  = $result['conequoid'];
                $conno_tipo = $result['conno_tipo'];
                $veioid     = $result['veioid'];
                $prpsoid    = $result['prpsoid'];
                
                // BUSCA O NÚMERO DE DIAS QUE O CONTRATO DEVE FICAR EM QUARENTENA E A DATA DE CORTE                
                $result = $this->dao->getTipoContratoParametrizacao($conno_tipo);
                
                if($result != null){
                    $tcpdias_quarentena      = $result['tcpdias_quarentena'];
                    $tcpdia_corte_quarentena = $result['tcpdia_corte_quarentena'];
                    
                    // TRATA A DATA DE QUARENTENA
                    if($tcpdias_quarentena > 0){
                        $dtQuarentena = mktime(0, 0, 0, date("m"), date("d"), date("Y")) + ($tcpdias_quarentena * 86400);
                    } else{
                        $today               = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                        list($dia,$mes,$ano) = explode('/', $tcpdia_corte_quarentena);
                        $dtQuarentena        = mktime(0, 0, 0, $mes, $dia, $ano);

                        if($dtQuarentena == $today || $dtQuarentena >= ($today + 86400 * 2)){
                            $dtQuarentena = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));
                        }
                    }
                    
                } else{
                    $dtQuarentena = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));
                }
                
                // GRAVA NO CONTRATO A DATA DE QUARENTENA
                $campo = "condt_quarentena_seg";
                $valor = "'".date('d/m/Y', $dtQuarentena)."'";
                
                $this->dao->atualizarContrato($campo,$valor,$connumero);
                
                // ALTERA STATUS DA PROPOSTA PARA QUARENTENA
                $this->dao->atualizarProposta($prpsoid, 6, 7, true, null);
                
                // INSERE HISTÓRICO
                $observacao = "AGUARDANDO RETIRADA - Em QUARENTENA até ".date('d/m/Y', $dtQuarentena);                
                $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 7, $tipo_doc, $observacao, 1, $this->id_usuario);
                
            } else{
                // VERIFICA SE POSSUI UM CONTRATO EX-SEGURADORA
                $result = $this->dao->getContratoExSeguradora($prpstpcoid,$chassi,$chassi_seguradora,$proposta,$cpf_cnpj,$cpf_cnpj_formatado);
                
                if($result != null){
                    $connumero  = $result['connumero'];
                    $conequoid  = $result['conequoid'];
                    $conclioid  = $result['conclioid'];
                    $conveioid  = $result['conveioid'];
                    $conno_tipo = $result['conno_tipo'];
                    
                    if($conveioid > 0){
                        $this->dao->atualizarDadosVeiculo($numapolice,$this->id_usuario,$proposta,$numitemapolice,$tipo_doc,$chassi,$placa,$conveioid);
                    }                
                
                    $observacao = "Contrato Ex- Localizado. Enviada a CIA Resposta de Desinstalação Efetuada.";
                    $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 7, $tipo_doc, $observacao, 4, $this->id_usuario);
                    
                    $this->dao->atualizarProposta($prpsoid, 4, 9, true, null);
                    
                    if($conequoid > 0 && $conveioid > 0){
                        $observacao = "RESPOSTA AUTOMATICA DE RETIRADA REALIZADA";
                        $this->dao->inserirContratoAlteracaoSeguradora($prpstpcoid, $connumero, $conequoid, $conclioid, $conveioid, $tipo_doc, $observacao, "R");
                    }
                    
                } else{
                    // INSERE HISTÓRICO DE "CONTRATO NÃO LOCALIZADO"
                    $observacao = "CONTRATO NAO LOCALIZADO - Aguardando Retirada";
                    $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 7, 'PRO', $observacao, 1, $this->id_usuario);                    
                    $this->dao->atualizarProposta($prpsoid, 1, 8, true, null);
                }                
            }
        }
    }
    
    /**
     * @param String $proposta
     * @param String $cpf_cnpj
     * @param int $cpf_cnpj_formatado
     * @param String $chassi
     * @param String $chassi_seguradora
     * @param String $numero_negocio
     * @param int $cod_evento_solicitacao
     * @param String $placa
     * @param String $placa_seguradora
     * @param int $cod_apolice_unica
     * @param int $cod_apolice_especifica
     * @param String $email_corretora
     * @param String $nome_segurado
     * @param String $endereco
     * @param String $bairro
     * @param String $cidade
     * @param String $estado
     * @param String $ddd
     * @param String $telefone
     * @param int $numero
     * @param int $cep
     * @param String $identificacao_negocio
     * @param String $nome_corretora
     * @param String $nome_corretora
     * @param String $desc_fabricante
     * @param String $desc_modelo
     * @param String $ddd_corretora
     * @param String $telefone_corretora
     * @param int $ano_modelo
     * @param int $numero_item
     * @param int $cod_sucursal
     */
    private function tratarRevisao($proposta,$cpf_cnpj,$cpf_cnpj_formatado,$chassi,$chassi_seguradora,$numero_negocio,$cod_evento_solicitacao,$placa,$placa_seguradora,$cod_apolice_unica,$cod_apolice_especifica,$email_corretora,$nome_segurado,$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$identificacao_negocio,$nome_corretora,$nome_corretora,$desc_fabricante,$desc_modelo,$ddd_corretora,$telefone_corretora,$ano_modelo,$numero_item,$cod_sucursal){
        // VERIFICA SE A PROPOSTA EXISTE
        if($this->prpstpcoid == 884  || $this->prpstpcoid == 919){
            $result = $this->dao->getProposta($proposta);
        } elseif($this->prpstpcoid == 883){
            $result = $this->dao->getProposta($proposta, $cpf_cnpj, $chassi);
        }
        
        if($result == null){
            //Retorna o prazo parametrizado conforme o tipo de contrato.
            $prpsprazo_inst = $this->dao->getPrazoInstalacaoParametrizado($this->prpstpcoid);
            
            //Mudando formato da data para: dd/mm/YYYY
            $inicio_vigencia = substr($dt_inicio_vigencia,6,2)."/".substr($dt_inicio_vigencia,4,2)."/".substr($dt_inicio_vigencia,0,4);
            $fim_vigencia    = substr($dt_fim_vigencia,6,2)."/".substr($dt_fim_vigencia,4,2)."/".substr($dt_fim_vigencia,0,4);
            
            $prpsscnpj_cpf = $this->formatarCpfCnpj($cpf_cnpj);
            
            if(strlen($prpsscnpj_cpf) == 11){
                $prpstipo_pessoa = "F";
            } else{
                $prpstipo_pessoa = "J";
            }
            
            $observacaoGeral = $this->getObservacaoGeral($identificacao_negocio,$cod_evento_solicitacao,$prpsprazo_inst,$nome_corretora,$ddd_corretora,$telefone_corretora,$email_corretora,$desc_fabricante,$desc_modelo,$placa,$chassi,$numero_negocio,$cod_apolice_unica,$numero_item,$cod_sucursal,$ano_modelo);
            
            $result = $this->dao->inserirProposta($numero_negocio,$this->prpstpcoid,$cod_evento_solicitacao,"now()",$prpsprazo_inst,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$cod_apolice_unica,$cod_apolice_especifica,"null",$email_corretora,"null","null",1,$inicio_vigencia,$fim_vigencia,$observacaoGeral,"now()",$cod_evento_solicitacao,"false","false","null",$prpsscnpj_cpf,$prpstipo_pessoa,$nome_segurado,"null",$endereco,$bairro,$cidade,$estado,$ddd,$telefone,$numero,$cep,$this->veisegoid,$identificacao_negocio,"null","null","now()",$nome_corretora,$nome_corretora,$desc_fabricante,$desc_modelo,"null","null");

            if($result > 0){
                $prpsoid    = $result;
                $observacao = "Cadastro da Proposta";
                $this->dao->inserirPropostaSeguradoraHistorico($prpsoid,5,'PRO',$observacao,1,$this->id_usuario,$cod_apolice_unica,$cod_apolice_especifica,'SEG');    
                $this->dao->atualizarProposta($prpsoid, 8, null, true, null);
                
                //Tokio Marine Brasil - Informações que serão usadas para o arquivo de retorno
                if($this->prpstpcoid == 883){
                    $this->dao->atualizarDadosProposta($prpsoid, $this->dadosProposta['tipo_dispositivo'], $this->dadosProposta['cod_apolice_especifica'], $this->dadosProposta['desc_fabricante'], $this->dadosProposta['cod_modelo_veiculo'], $this->dadosProposta['desc_modelo'], $this->dadosProposta['cpf_cnpj_segurado'], $this->dadosProposta['cod_estabelecimento'], $this->dadosProposta['cod_digito_segurado'], $this->dadosProposta['local'], $this->dadosProposta['sub_local'], $this->dadosProposta['corretor'],  $this->dadosProposta['laudo_antifurto']);
                }
            }
        }
        
        // VERIFICA SE TEM CONTRATO
        $result = $this->dao->getContrato($this->prpstpcoid, $proposta, $cpf_cnpj, $cpf_cnpj_formatado, false, true, $chassi, $chassi_seguradora);
        
        if($result != null){
            $connumero = $result["connumero"];
            $conequoid = $result["conequoid"];
            $conclioid = $result["conclioid"];
            $prpsoid   = $result["prpsoid"];
            $veioid    = $result["veioid"];;
            $eveoid    = $result["eveoid"];

            //ATUALIZA VEÍCULO COM A NOVA PROPOSTA
            $this->dao->atualizarVeiculoProposta($proposta,$veioid);        
            
            $observacao = "AGUARDANDO REVISÃO. PROPOSTA DO VEÍCULO ATUALIZADA.";
            $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 11, "PRO", $observacao, 8, $this->id_usuario);
            
            // VERIFICA SE ENCONTRA O.S. DE REVISAO PENDENTE
            $result = $this->dao->getRevisaoPendente($connumero);
            
            if($result != null){           
                $observacao = "Já existe uma Ordem de Serviço Pendente para REVISÃO";
                $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 11, "PRO", $observacao, 8, $this->id_usuario);            
            } else{
                // INCLUIR O.S. DE REVISÃO
                $desc_problema = "Solicitado pela Seguradora Revisão do Equipamento";
                $descr_motivo  = "Solicitado pela Seguradora Revisão do Equipamento";
                
                $result = $this->dao->inserirOrdemServicoRevisao($conequoid, $conclioid, 1, $desc_problema, $this->id_usuario, $connumero, 1, $descr_motivo, $veioid, 1, $eveoid, "true");
                
                if($result > 0){
                    $ordoid = $result;
                    
                    $observacao = "AGUARDANDO REVISÃO. GERADA O.S";
                    $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 11, "PRO", $observacao, 8, $this->id_usuario);
                }                
            }            
        }
    }
    
    /**
     * @param int $cpf_cnpj
     * @param int $cpf_cnpj_formatado
     * @param String $chassi
     * @param String $chassi_seguradora
     * @param String $proposta
     * @param String $tipo_doc
     */
    private function tratarAtualizacaoInstalacao($cpf_cnpj, $cpf_cnpj_formatado, $chassi, $chassi_seguradora, $proposta, $tipo_doc){
        if($this->prpstpcoid == 884 || $this->prpstpcoid == 919){
            $result = $this->dao->getProposta($proposta);
        } elseif($this->prpstpcoid == 883){
            $result = $this->dao->getProposta($proposta, $cpf_cnpj, $chassi);
        }
        
        $prpsoid = $result["prpsoid"];        
        
        // VERIFICA SE POSSUI ALGUM OUTRO CONTRATO ATIVO PARA OUTRO CLIENTE E COM EQUIPAMENTO INSTALADO
        $result = $this->dao->getContratoAtivoCliente($cpf_cnpj, $cpf_cnpj_formatado, $chassi, $chassi_seguradora, $proposta);
        
        if($result != null){
            $connumero = $result["connumero"];            
            
            // ATUALIZA PROPOSTA COMO TRANSFERÊNCIA DE TITULARIDADE
            $this->dao->atualizarProposta($prpsoid, 0, null, true, 't', 't');
            
            // INSERE NO HISTÓRICO "AGUARDANDO VERIFICAÇÃO MANUAL"
            if($tipo_doc == "END"){                
                $observacao = "Transferência de Titularidade - Aguardando verificação manual";
                $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 1, $tipo_doc, $observacao, 1, $this->id_usuario);
            } else{
                $observacao = "Provável Transferência de Titularidade - Aguardando verificação manual";
                $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 1, $tipo_doc, $observacao, 1, $this->id_usuario);
            }
        }        
    }
    
    /**
     * @param String $proposta
     * @param String $placa
     * @param String $placa_seguradora
     * @param String $chassi
     * @param String $chassi_seguradora
     * @param int $numapolice
     * @param int $numitemapolice
     */
    private function tratarAtualizacaoRetirada($proposta,$placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice){
        // VERIFICA SE A PROPOSTA EXISTE
        if($this->prpstpcoid == 884 || $this->prpstpcoid == 919){
            $result = $this->dao->getProposta($proposta);
        } elseif($this->prpstpcoid == 883){
            $result = $this->dao->getProposta($proposta, $cpf_cnpj, $chassi);
        }
        
        if($result != null){
            $prpsoid        = $result["prpsoid"];
            $prpstpcoid     = $result["prpstpcoid"];
            $prpsprazo_inst = $result["prpsprazo_inst"];
            $veioid         = $result["prpsveioid"];
            
            if($veioid > 0){
                //ATUALIZA PROPOSTA COM OS DADOS DO VEÍCULO
                $this->dao->atualizarPropostaDadosVeiculo($placa,$placa_seguradora,$chassi,$chassi_seguradora,$numapolice,$numitemapolice,$prpsoid);

                // ATUALIZA DADOS DO VEÍCULO
                $this->dao->atualizarDadosVeiculo($numapolice,$this->id_usuario,$proposta,$numitemapolice,$tipo_doc,$chassi,$placa,$veioid);
            }            
        }
    }
    
    /**
     * @param String $proposta
     * @param String $cpf_cnpj
     * @param String $cpf_cnpj_formatado
     * @param String $tipo_doc
     */
    private function tratarCancelamentoInstalacao($proposta,$cpf_cnpj,$cpf_cnpj_formatado,$tipo_doc){
        if($this->prpstpcoid == 884 || $this->prpstpcoid == 919){
            $result = $this->dao->getProposta($proposta);
        } elseif($this->prpstpcoid == 883){
            $result = $this->dao->getProposta($proposta, $cpf_cnpj, $chassi);
        }
        
        if($result != null){
            $prpsoid    = $result["prpsoid"];
            $prpstpcoid = $result["prpstpcoid"];
            
            // VERIFICA SE JÁ POSSUI CONTRATO
            $result = $this->getContrato($this->prpstpcoid,$proposta,$cpf_cnpj,$cpf_cnpj_formatado,false);
            
            if($result != null){
                $connumero  = $result['connumero'];
                $conclioid  = $result['conclioid'];
                $conequoid  = $result['conequoid'];
                $conno_tipo = $result['conno_tipo'];
                $veioid     = $result['veioid'];
                $prpsoid    = $result['prpsoid'];
                
                // BUSCA O NÚMERO DE DIAS QUE O CONTRATO DEVE FICAR EM QUARENTENA E A DATA DE CORTE
                $result = $this->dao->getTipoContratoParametrizacao($conno_tipo);
                
                if($result != null){
                    $tcpdias_quarentena      = $result["tcpdias_quarentena"];
                    $tcpdia_corte_quarentena = $result["tcpdia_corte_quarentena"];
                    
                    // TRATA A DATA DE QUARENTENA
                    if($tcpdias_quarentena > 0){
                        $dtQuarentena = mktime(0, 0, 0, date("m"), date("d"), date("Y")) + ($tcpdias_quarentena * 86400);
                    } else{
                        $today                 = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                        list($dia, $mes, $ano) = explode('/', $tcpdia_corte_quarentena);
                        $dtQuarentena          = mktime(0, 0, 0, $mes, $dia, $ano);

                        if($dtQuarentena == $today || $dtQuarentena >= ($today + 86400 * 2)){
                            $dtQuarentena = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));
                        }
                    }
                    
                } else{
                    $dtQuarentena = mktime(0, 0, 0, date("m") + 1, 1, date("Y"));
                }
                
                // GRAVA NO CONTRATO A DATA DE QUARENTENA
                $campo = "condt_quarentena_seg";
                $valor = "'".date('d/m/Y', $dtQuarentena)."'";
                $this->dao->atualizarContrato($campo,$valor,$connumero);

                // ALTERA STATUS DA PROPOSTA PARA QUARENTENA
                $this->dao->atualizarProposta($prpsoid, 6, 6, true, null);

                // INSERE HISTÓRICO
                $observacao = "INSTALAÇÃO CANCELADA - Em QUARENTENA até ".date('d/m/Y',$dtQuarentena);
                $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 6, $tipo_doc, $observacao, 6, $this->id_usuario);
                
            } else{
                // INSERE HISTÓRICO DE "CONTRATO NÃO LOCALIZADO"
                $observacao = "CONTRATO NAO LOCALIZADO - Instalação Cancelada";
                $this->dao->inserirPropostaSeguradoraHistorico($prpsoid, 6, $tipo_doc, $observacao, 1, $this->id_usuario);
                
                $this->dao->atualizarProposta($prpsoid, 3, 6, false, null);
            }
        }
    }
    
    /**
     * Remove ponto, hífen e barra.
     * @param String
     * @return int
     */
    private function formatarCpfCnpj($cpf_cnpj){
        $cpf_cnpj = trim($cpf_cnpj);
        $cpf_cnpj = str_replace('.', '', $cpf_cnpj);
        $cpf_cnpj = str_replace('.', '', $cpf_cnpj);
        $cpf_cnpj = str_replace('/', '', $cpf_cnpj);
        $cpf_cnpj = str_replace('-', '', $cpf_cnpj);        
        $cpf_cnpj = trim($cpf_cnpj) * 1;        
        
        if(strlen($cpf_cnpj) > 11){
            $cpf_cnpj = str_pad($cpf_cnpj, 14, "0", STR_PAD_LEFT);
        } else{
            $cpf_cnpj = str_pad($cpf_cnpj, 11, "0", STR_PAD_LEFT);
        }
        
        return $cpf_cnpj;
    }
    
    /**
     * Gera o arquivo de retorno após o processamento ser realizado.
     * @param String $arquivo
     */
    private function gerarArquivoRetorno($arquivo){
        $this->dao->atualizarPropostaTipoContrato();
        $this->dao->inserirArquivoSeguradoraTipoContrato();    
    
        if($this->prpstpcoid == 884 || $this->prpstpcoid == 919){ // Tokio Marine - Layout 6190
            $result = $this->dao->getDadosRetornoTokioMarine();
            
            if($result != null){                

            	//Selecionando a pasta
            	if($veisegoid == 884){
            		$pasta = "tokio_marine/";
            	} elseif($veisegoid == 919){
            		$pasta = "tokio_marine_rf/";
            	}
            	
                //Diretório
                $dir = "/var/www/$pasta/arquivo_retorno/";

                //Verificando se existe diretorio: arquivo_retorno
                //Caso não exista, cria automaticamente.
                if(!is_dir($dir)){
                    mkdir($dir, 0777);
                }
                
                $nome_arquivo = $dir."RETORNO_".$arquivo;
                $link_arquivo = "tokio_marine/arquivo_retorno/RETORNO_".$arquivo;
                
                $archive  = fopen($nome_arquivo, "w");
                $texto = "";
                $ids_contrato = "";
                $ids_arquivo  = "";
                
                foreach($result as $row){
                    $campo1  = str_pad(substr(trim($row['campo1']),0,5), 5, '0', STR_PAD_LEFT);
                    $campo2  = str_pad(substr(trim($row['campo2']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo3  = str_pad(substr(trim($row['campo3']),0,30), 30, ' ', STR_PAD_RIGHT);
                    $campo4  = str_pad(substr(trim($row['campo4']),0,1), 1, ' ', STR_PAD_RIGHT);
                    $campo5  = str_pad(substr(trim($row['campo5']),0,30), 30, ' ', STR_PAD_RIGHT);
                    $campo6  = str_pad(substr(trim($row['campo6']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo7  = str_pad(substr(trim($row['campo7']),0,3), 3, ' ', STR_PAD_RIGHT);
                    $campo8  = str_pad(substr(trim($row['campo8']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo9  = str_pad(substr(trim($row['campo9']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo10 = str_pad(substr(trim($row['campo10']),0,9), 9, ' ', STR_PAD_RIGHT);
                    $campo11 = str_pad(substr(trim($row['campo11']),0,13), 13, ' ', STR_PAD_RIGHT);
                    $campo12 = str_pad(substr(trim($row['campo12']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo13 = str_pad(substr(trim($row['campo13']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo14 = str_pad(substr(trim($row['campo14']),0,8), 8, '0', STR_PAD_LEFT);
                    $campo15 = str_pad(substr(trim($row['campo15']),0,8), 8, '0', STR_PAD_LEFT);
                    $campo16 = str_pad(substr(trim($row['campo16']),0,8), 8, '0', STR_PAD_LEFT);
                    $campo17 = str_pad(substr(trim($row['campo17']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo18 = str_pad(substr(trim($row['campo18']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo19 = str_pad(substr(trim($row['campo19']),0,30), 30, ' ', STR_PAD_RIGHT);
                    $campo20 = str_pad(substr(trim($row['campo20']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo21 = str_pad(substr(trim($row['campo21']),0,30), 30, ' ', STR_PAD_RIGHT);
                    $campo22 = str_pad(substr(trim($row['campo22']),0,9), 9, '0', STR_PAD_LEFT);
                    $campo23 = str_pad(substr(trim($row['campo23']),0,30), 30, ' ', STR_PAD_RIGHT);
                    $campo24 = str_pad(substr(trim($row['campo24']),0,4), 4, '0', STR_PAD_LEFT);
                    $campo25 = str_pad(substr(trim($row['campo25']),0,9), 9, ' ', STR_PAD_RIGHT);
                    $campo26 = str_pad(substr(trim($row['campo26']),0,20), 20, ' ', STR_PAD_RIGHT);
                    $campo27 = str_pad(substr(trim($row['campo27']),0,35), 35, ' ', STR_PAD_RIGHT);
                    $campo28 = str_pad(substr(trim($row['campo28']),0,20), 20, ' ', STR_PAD_RIGHT);
                    $campo29 = str_pad(substr(trim($row['campo29']),0,40), 40, ' ', STR_PAD_RIGHT);
                    $campo30 = str_pad(substr(trim($row['campo30']),0,5), 5, '0', STR_PAD_LEFT);
                    $campo31 = str_pad(substr(trim($row['campo31']),0,15), 15, ' ', STR_PAD_RIGHT);
                    $campo32 = str_pad(substr(trim($row['campo32']),0,15), 15, ' ', STR_PAD_RIGHT);
                    $campo33 = str_pad(substr(trim($row['campo33']),0,8), 8, '0', STR_PAD_LEFT);
                    $campo34 = str_pad(substr(trim($row['campo34']),0,15), 15, ' ', STR_PAD_RIGHT);
                    $campo35 = str_pad(substr(trim($row['campo35']),0,2), 2, ' ', STR_PAD_RIGHT);
                    $campo36 = str_pad(substr(trim($row['campo36']),0,4), 4, ' ', STR_PAD_RIGHT);
                    $campo37 = str_pad(substr(trim($row['campo37']),0,10), 10, ' ', STR_PAD_RIGHT);
                    $campo38 = str_pad(substr(trim($row['campo38']),0,4), 4, '0', STR_PAD_LEFT);
                    $campo39 = str_pad(substr(trim($row['campo39']),0,2), 2, '0', STR_PAD_LEFT);
                    $campo40 = str_pad(substr(trim($row['campo40']),0,6), 6, '0', STR_PAD_LEFT);
                    $campo41 = str_pad(substr(trim($row['campo41']),0,8), 8, '0', STR_PAD_LEFT);
                    $campo42 = str_pad(substr(trim($row['campo42']),0,8), 8, '0', STR_PAD_LEFT);
                    $campo43 = str_pad(substr(trim($row['campo43']),0,2), 2, '0', STR_PAD_LEFT);
                    $campo44 = str_pad(substr(trim($row['campo44']),0,2), 2, '0', STR_PAD_LEFT);
                    $campo45 = str_pad(substr(trim($row['campo45']),0,4), 4, ' ', STR_PAD_RIGHT);
                    $campo46 = str_pad(substr(trim($row['campo46']),0,10), 10, ' ', STR_PAD_RIGHT);
                    $campo47 = str_pad(substr(trim($row['campo47']),0,50), 50, ' ', STR_PAD_RIGHT);
                    $campo48 = str_pad(substr(trim($row['campo48']),0,3), 3, '0', STR_PAD_LEFT);
                    $campo49 = str_pad(substr(trim($row['campo49']),0,10), 10, '0', STR_PAD_LEFT);
                    $campo50 = str_pad(substr(trim($row['campo50']),0,50), 50, ' ', STR_PAD_RIGHT);
                    
                    $texto = $campo1.$campo2.$campo3.$campo4.$campo5.$campo6.$campo7.$campo8.$campo9.$campo10.$campo11.$campo12.$campo13.
                             $campo14.$campo15.$campo16.$campo17.$campo18.$campo19.$campo20.$campo21.$campo22.$campo23.$campo24.$campo25.
                             $campo26.$campo27.$campo28.$campo29.$campo30.$campo31.$campo32.$campo33.$campo34.$campo35.$campo36.$campo37.
                             $campo38.$campo39.$campo40.$campo41.$campo42.$campo43.$campo44.$campo45.$campo46.$campo47.$campo48.$campo49.
                             $campo50."\r\n";                    
                    
                    if($row['tabela'] == 'contrato_alteracao_seguradora'){
                        if($ids_contrato != ""){
                            $ids_contrato .= ",".$row['id'];
                        } else{
                            $ids_contrato = $row['id'];
                        }
                    } elseif($row['tabela'] == 'arquivo_seguradora'){
                        if($ids_arquivo != ""){
                            $ids_arquivo .= ",".$row['id'];
                        } else{
                            $ids_arquivo = $row['id'];
                        }
                    }
                    
                    fwrite($archive, "$texto");                   
                }
                
                fclose($archive);
                
                //Verificando se o arquivo foi criado.
                if(!file_exists($nome_arquivo)){
                    throw new Exception("Erro.: Não foi possível gerar o arquivo de retorno para a pasta.: ".$dir);
                } else{
                    $from     = "sascar@sascar.com.br";
                    $fromName = "Sascar";
                    $subject  = "Proposta - Arquivo de Retorno.";
                    $message  = "Segue em anexo.";    
                    $to       = $this->dao->getEmailRetorno($this->prpstpcoid);
                    $path     = null;
                    
                    if(!strstr($_SERVER['HTTP_HOST'], 'intranet')){
                        $to = "teste_desenv@sascar.com.br";
                    }
                    
                    if($to != null){
                        //Anexo
                        $path = $nome_arquivo;                        
                        $this->enviarEmail($from, $fromName, $subject, $message, $to, $path);
                        //$this->msg = "<br><a href='$link_arquivo' target='_blank'>Arquivo Retorno</a>";
                    } else{
                        throw new Exception("Erro.: E-mail de retorno está vazio. Tipo do contrato.: ".$this->prpstpcoid);
                    } 
                    
                    if($ids_contrato != ""){
                        $this->dao->atualizarContratoRemessa($ids_contrato);
                    }
                    if($ids_arquivo != ""){
                        $this->dao->atualizarArquivoSeguradora($ids_arquivo);
                    }
                    
                    $caminho = $this->dir.$arquivo;
                    $this->dao->inserirPropostaSeguradoraArquivo($arquivo,$this->id_usuario,$caminho,$this->prpstpcoid,$this->origem);
                }                    
            } else{
                $this->msg = "Não foi possível gerar o arquivo de retorno.";
            }
        
        } elseif($this->prpstpcoid == 883){ // Tokio Marine Brasil - Layout 5151
            $result = $this->dao->getDadosRetornoTokioMarineBrasil();
            
            if($result != null){                
                //Diretório
                $dir = "/var/www/tokio_marine_brasil/arquivo_retorno/";

                //Verificando se existe diretorio: arquivo_retorno
                //Caso não exista, cria automaticamente.
                if(!is_dir($dir)){
                    mkdir($dir, 0777);
                }
                
                $nome_arquivo = $dir."RETORNO_".$arquivo;
                $link_arquivo = "tokio_marine_brasil/arquivo_retorno/RETORNO_".$arquivo;
                
                $archive = fopen($nome_arquivo, "w");
                $texto = "";
                $qtde  = 0;
                $ids_contrato = "";
                $ids_arquivo  = "";
                
                $header = "Data: ".date('dmY').";Empresa: TOKIO MARINE SEGURADORA;CNPJ: 03112879000151;Prestadora de Servico: SASCAR - TECNOLOGIA E SEGURANCA AUTOMOTIVA S.A.;"."\r\n";
                fwrite($archive, "$header");
                
                foreach($result as $row){
                    $texto = $row['campo0'].";".$row['campo1'].";".$row['campo2'].";".$row['campo3'].";".$row['campo4'].";".$row['campo5'].";".$row['campo6'].";".
                             $row['campo7'].";".$row['campo8'].";".$row['campo9'].";".$row['campo10'].";".$row['campo11'].";".$row['campo12'].";".$row['campo13'].";".
                             $row['campo14'].";".$row['campo15'].";".$row['campo16'].";".$row['campo17'].";".$row['campo18'].";".$row['campo19'].";".$row['campo20'].";".
                             $row['campo22'].";".$row['campo23'].";".$row['campo24'].";".$row['campo25'].";".$row['campo26'].";".$row['campo27'].";".
                             $row['campo28'].";".$row['campo29'].";".$row['campo30'].";".$row['campo31'].";".$row['campo32'].";".$row['campo33'].";".$row['campo34'].";".
                             $row['campo35'].";".$row['campo36'].";".$row['campo37'].";".$row['campo38'].";"."\r\n";
                             
                    if($row['tabela'] == 'contrato_alteracao_seguradora'){
                        if($ids_contrato != ""){
                            $ids_contrato .= ",".$row['id'];
                        } else{
                            $ids_contrato = $row['id'];
                        }
                    } elseif($row['tabela'] == 'arquivo_seguradora'){
                        if($ids_arquivo != ""){
                            $ids_arquivo .= ",".$row['id'];
                        } else{
                            $ids_arquivo = $row['id'];
                        }
                    }
                    
                    $qtde = ($qtde + 1);
                    
                    fwrite($archive, "$texto");                   
                }
                
                $trailler = "03112879000151;Qtde=$qtde;";
                fwrite($archive, "$trailler");
                
                fclose($archive);
                
                //Verificando se o arquivo foi criado.
                if(!file_exists($nome_arquivo)){
                    throw new Exception("Erro.: Não foi possível gerar o arquivo de retorno para a pasta.: ".$dir);
                } else{
                    $from     = "sascar@sascar.com.br";
                    $fromName = "Sascar";
                    $subject  = "Proposta - Arquivo de Retorno.";
                    $message  = "Segue em anexo.";    
                    $to       = $this->dao->getEmailRetorno($this->prpstpcoid);
                    $path     = null;
                    
                    if(!strstr($_SERVER['HTTP_HOST'], 'intranet')){
                        $to = "teste_desenv@sascar.com.br";
                    }
                    
                    if($to != null){
                        //Anexo
                        $path = $nome_arquivo;                        
                        $this->enviarEmail($from, $fromName, $subject, $message, $to, $path);
                    } else{
                        throw new Exception("Erro.: E-mail de retorno está vazio. Tipo do contrato.: ".$this->prpstpcoid);
                    }  
                    
                    if($ids_contrato != ""){
                        $this->dao->atualizarContratoRemessa($ids_contrato);
                    }
                    if($ids_arquivo != ""){
                        $this->dao->atualizarArquivoSeguradora($ids_arquivo);
                    }
                    
                    $caminho = $this->dir.$arquivo;
                    $this->dao->inserirPropostaSeguradoraArquivo($arquivo,$this->id_usuario,$caminho,$this->prpstpcoid,$this->origem);
                }                
            } else{
                $this->msg = "Não foi possível gerar o arquivo de retorno.";
            }
        }
    }
    
    /**
     * Monta a observação geral com os dados da Solicitação, Corretora, E-mail e Seguro.
     * @param String $identificacao_negocio
     * @param int $cod_evento_solicitacao
     * @param String $prpsprazo_inst
     * @param String $nome_corretora
     * @param String $ddd_corretora
     * @param String $telefone_corretora
     * @param String $email_corretora
     * @param int $desc_fabricante
     * @param String $desc_modelo
     * @param String $placa
     * @param String $chassi
     * @param String $numero_negocio
     * @param int $cod_apolice_unica
     * @param int $numero_item
     * @param int $cod_sucursal
     * @param int $ano_modelo
     * @return String
     */
    private function getObservacaoGeral($identificacao_negocio,$cod_evento_solicitacao,$prpsprazo_inst,$nome_corretora,$ddd_corretora,$telefone_corretora,$email_corretora,$desc_fabricante,$desc_modelo,$placa,$chassi,$numero_negocio,$cod_apolice_unica,$numero_item,$cod_sucursal,$ano_modelo){
        if($identificacao_negocio == "PRO"){
            $tipoSolicitacao = "Proposta";
        } elseif($identificacao_negocio == "END"){
            $tipoSolicitacao = "Endosso";
        } elseif($identificacao_negocio == "NRA"){
            $tipoSolicitacao = "Retirada";
        }

        if($cod_evento_solicitacao == 1){
            $eventoSolicitado = "Solicitação de Instalação";
        } elseif($cod_evento_solicitacao == 2){
            $eventoSolicitado = "Retirada";
        } elseif($cod_evento_solicitacao == 3){
            $eventoSolicitado = "Revisão";
        }
        
        //Convertendo para d/m/Y
        list($ano,$mes,$dia) = explode("-",$prpsprazo_inst);
        $prpsprazo_inst      = $dia."/".$mes."/".$ano;
        
        return $observacaoGeral = "- Solicitação -\nTipo de Solicitação: $tipoSolicitacao\nEvento Solicitado: $eventoSolicitado\nPrazo Instalação/Retirada: $prpsprazo_inst\n\n- Corretor -\nNome Corretor: $nome_corretora\nTelefone: ($ddd_corretora) $telefone_corretora\nEmail: $email_corretora\n\n- Veiculo -\nMarca: $desc_fabricante\nModelo: $desc_modelo\nAno: $ano_modelo\nPlaca: $placa\nChassi: $chassi\n\n- Dados do Seguro-\nProposta: $numero_negocio\nApolice: $cod_apolice_unica\nItem: $numero_item\nSucursal: $cod_sucursal";
    }
}
?>