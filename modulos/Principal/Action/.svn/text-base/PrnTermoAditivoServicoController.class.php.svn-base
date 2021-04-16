<?php
header('Content-Type: text/html; charset=ISO-8859-1');
require _SITEDIR_.'modulos/Principal/DAO/PrnTermoAditivoServicoDAO.class.php';
require _SITEDIR_.'modulos/Principal/View/PrnTermoAditivoServicoView.class.php'; 

/**
 * Classe para controlar os fluxos. - <PrnTermoAditivoServicoController.class.php>
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @package Principal
 * @version 1.0
 * @since 03/04/2013
 */
class PrnTermoAditivoServicoController{
    private $dao        = null;
    private $view       = null;
    private $id_usuario = null;

    /**
     * Construtor da classe.
     */
    function __construct($acao){
        $this->dao        = new PrnTermoAditivoServicoDAO();
        $this->view       = new PrnTermoAditivoServicoView();
        $this->id_usuario = $_SESSION['usuario']['oid'];

        switch($acao){
            case 'pesquisar_termo':
                $this->pesquisarTermoAditivoServico($_POST['dados']);
                break;
            case 'carregar_tela_tas':
                $this->getTelaTermoAditivoServico($_POST['termo_aditivo']);
                break;
            case 'carregar_contrato':
                $this->getContrato($_POST['dados']);
                break;
            case 'get_valor_obrigacao':
                $this->getValorObrigacaoFinanceira($_POST['dados']);
                break;
            case 'get_valor_min_obrigacao':
                $this->getValorMinimoObrigacao($_POST['dados']);
                break;
            case 'get_valor_max_obrigacao':
                $this->getValorMaximoObrigacao($_POST['dados']);
                break;
            case 'confirmar_termo':
                $this->confirmarTermoAditivoServico($_POST['dados']);
                break;
            case 'componente_pesquisar_cli':
                $this->getComponentePesquisaClientes($_POST['dados']);
                break;
            case 'adicionar_item':
                $this->adicionarItemAditivo($_POST['dados']);
                break;
            case 'remover_item':
                $this->removerItemAditivo($_POST['id_item']);
                break;
            case 'excluir_termo':
                $this->excluirTermoAditivoServico($_POST['id_termo']);
                break;
            case 'buscarServicos':
                $this->buscarServicos($_POST['dados']);
                break;
            default:
                $this->index();
                break;
        }
    }
    
    /**
     * Remove os pontos, hífens e barras.
     * @param String $cpf_cnpj
     * @return int
     */
    public function format2Int($cpf_cnpj){        
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
     * Formata para o padrão do Banco de Dados.
     * @param String $valor
     */
    public function format2Number($valor){
        $valor = str_replace(',', ';', $valor);
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(';', '.', $valor);
        
        return $valor;
    }

    /**
     * Acessa tela inicial (Pesquisar) do módulo.
     */
    private function index(){
        $status  = $this->dao->getStatus();
        $servico = $this->dao->getServico();
        $pacote = $this->dao->getPacote();
        
        $this->view->index($status, $servico, $pacote);
    }
    
    /**
     * Pesquisar termos aditivos de serviço.
     * @param Array $dados
     */
    private function pesquisarTermoAditivoServico($dados){
        $result = $this->dao->pesquisarTermoAditivoServico($dados);
        
        if($result != null){
            $this->view->getTelaResultadoPesquisa($result);
        } else{
            $this->view->setMensagem('mensagem info','Nenhum resultado encontrado.');
        }
        
        exit;
    }
    
    /**
     * Tela de inclusao/edição do termo aditivo de serviços.
     * @param int $termo_aditivo
     */
    private function getTelaTermoAditivoServico($termo_aditivo = ""){
        if($termo_aditivo != ""){
            //Recupera os dados do termo aditivo.
            $dados = $this->dao->getDadosTermoAditivo($termo_aditivo);
            $itens = $this->dao->getItensTermoAditivo($termo_aditivo);
        }
        
        $status  = $this->dao->getStatus();
        $servico = $this->dao->getServico();
        $pacote = $this->dao->getPacote();
        
        $this->view->getTelaTermoAditivoServico($termo_aditivo, $status, $servico, $dados, $itens, $this->dao, $pacote);
        exit;    
    }

    private function buscarServicos($dados = "") {
        $servico = null;

        if($dados != "") {
            $servico = $this->dao->getServico(null, $dados['tipo_serv'], $dados['pacote']);
        }

        $option = "<option value=''>Escolha</option>";

        if($servico != null){
            foreach($servico as $row){
                $option .= "<option value='".$row['obroid']."'>".$row['obrobrigacao']."</option>";
            }
        }

        echo json_encode(array("option" => utf8_encode($option)));
        exit;
    }
    
    /**
     * Retorna ao JS as opções de contrato conforme o serviço selecionado.
     * @param Array $dados
     */ 
    private function getContrato($dados){
        $servico  = trim($dados['servico']);
        $cliente  = trim(utf8_decode($dados['cliente']));
        $cpf_cnpj = $this->format2Int(trim($dados['cpf_cnpj']));        
        $option   = ",";
        
        //Verificando se a Obrigação Financeira é do tipo cliente.
        $tipo_cliente = $this->dao->verificarObrigacaoFinanceira($servico);
        
        /*
         * Se a obrigação financeira for do Tipo Cliente,
         * a combo contrato deve ser desabilitada.
         * Caso contrário retorna os contratos compatíveis.
         */
        if(!$tipo_cliente){     
            $result = $this->dao->getClientes($cliente, $cpf_cnpj);
            
            if($result != null){
                $cliente = $result[0]['clioid'];
            } else{
                $cliente = "";
            }
            
            if($servico != "" && $cliente != ""){
                $contrato = $this->dao->getContrato($servico, $cliente);
                
                if($contrato != null){
                    foreach($contrato as $row){
                        $option .= $row['connumero'].",";
                    }
                }

            }
        }
        
        echo json_encode(array("tipo_cliente" => $tipo_cliente, "option" => $option));
        exit;
    }
    
    /**
     * Retorna para o javascript o valor da Obrigação Financeira.
     * @param Array $dados
     */
    private function getValorObrigacaoFinanceira($dados){
        $servico = trim($dados['servico']);
        $valor   = "";
        
        if($servico != ""){
            $valor = $this->dao->getValorObrigacaoFinanceira($servico);
        }
        
        echo $valor;
        exit;
    }

    private function getValorMinimoObrigacao($dados) {
        $servico = trim($dados['servico']);
        $valor = '';

        if($servico != '') {
            $valor = $this->dao->getValoresLimiteObrigacao($servico);
        }

        if($valor != null) {
            echo $valor['obrvl_minimo'];
        }
        exit;
    }

    private function getValorMaximoObrigacao($dados) {
        $servico = trim($dados['servico']);
        $valor = '';

        if($servico != '') {
            $valor = $this->dao->getValoresLimiteObrigacao($servico);
        }

        if($valor != null) {
            echo $valor['obrvl_maximo'];
        }
        exit;
    }
    
    /**
     * Realiza a inclusão ou edição do termo aditivo.
     * @param Array $dados
     * @throws
     */
    private function confirmarTermoAditivoServico($dados){
        try{
            $this->dao->beginTransaction();
    
            if($dados != null){
                $id_termo      = $dados['id_termo'];
                $cliente       = utf8_decode($dados['cliente']);
                $cpf_cnpj      = $this->format2Int($dados['cpf_cnpj']);
                $ta_servico    = $dados['ta_servico'];
                $situacao      = $dados['situacao'];
                $status        = (int) $dados['status'];
                $validade      = "'" . $dados['validade'] . "'";

                if($situacao !== 'D') {
                    $validade = 'null';
                }
                             
                //Recuperando o id do cliente
                $result = $this->dao->getClientes($cliente, $cpf_cnpj);
                
                if($result != null){
                    $cliente = $result[0]['clioid'];
                } else{
                    //error
                    $msg = json_encode(array("tipo_msg" => "e", "msg" => "N&atilde;o foi poss&iacute;vel recuperar o id do cliente."));
                    throw new Exception($msg);
                }
                
                //INCLUSÃO
                if($id_termo == ""){
                    $status     = 1; //Pendente
                    $observacao = "";              
                    
                    $result = $this->dao->inserirTermoAditivoServico($status, $cliente, $this->id_usuario, $observacao, $situacao, $validade);
                    
                    if($result > 0){
                        //success
                        $id_termo = $result;
                        $this->dao->commitTransaction();
                        echo json_encode(array("tipo_msg" => "s", "msg" => "Termo Aditivo de Servi&ccedil;o criado com sucesso!", "id_termo" => $id_termo));
                        exit;
                    } else{
                        //error
                        $msg = json_encode(array("tipo_msg" => "e", "msg" => "N&atilde;o foi poss&iacute;vel inserir um novo Termo Aditivo de Servi&ccedil;o."));
                        throw new Exception($msg);
                    }
                } else{
                    //ALTERAÇÃO
                    $result = 0;
                    
                    if($status == 3){
                        /*
                         * Cancelado
                         */
                        $result = $this->dao->alterarTermoAditivoServico($status, $this->id_usuario, $id_termo);                        
                    } elseif($status == 2){
                        /*
                         * Aprovado
                         */
                        $result = $this->dao->alterarTermoAditivoServico($status, $this->id_usuario, $id_termo, $cliente, $observacao, $situacao, $validade);
                         
                        $this->dao->inserirContratoServico($this->id_usuario, $id_termo, $validade, $situacao);
						 
                        if($result > 0){
                            $itens = $this->dao->getItensTermoAditivo($id_termo);
                            
                            if($itens != null){
                                foreach($itens as $row){
                                    //Se a Situação do TA for Demonstração, inserir true.
                                    if($situacao == 'D'){
                                        $demonstracao = 't';
                                    } else{
                                        $demonstracao = 'f';
                                    }
                                    
                                    //Se a Situação do TA for Cortesia, inserir true.                                    
                                    if($situacao == 'C'){
                                        $cortesia = 't';
                                    } else{
                                        $cortesia = 'f';
                                    }
                                    
                                    //Verificando se é tipo Cliente ou Contrato.
                                    $tipo_item = $this->dao->verificarObrigacaoFinanceira($row['taseiobroid']);
                                    
                                    if($tipo_item){
                                    	
                                        //Cliente
                                        $this->dao->inserirClienteObrigacaoFinanceira($cliente, $row['taseiobroid'], $row['taseivalor_negociado'], $demonstracao, $cortesia);
                                        
                                        $tipo_obrigacao = $this->dao->getTipoObrigacao($row['taseiobroid']);
                                        
                                        if($tipo_obrigacao != null){
                                            if($tipo_obrigacao['obrtipo_obrigacao'] == "A"){
                                                if($tipo_obrigacao['obrebtoid'] != ""){
                                                    $beneficio = $this->dao->getClienteBeneficio($tipo_obrigacao['obrebtoid'], $cliente);
                                                    
                                                    if($beneficio == null){
                                                        $this->dao->inserirClienteBeneficio($tipo_obrigacao['obrebtoid'], $cliente);
                                                    }
                                                }
                                            }
                                        } 

                                        
                                    } else{
                                    	
                                        //Contrato
                                        $numero_contrato = trim($row['taseiconnumero']);
                                        
                                        if($numero_contrato != ""){
                                            $id_equip_classe = $this->dao->getIdEquipamentoClasse($numero_contrato);
                                            if($situacao != "D") {
                                                $this->dao->inserirContratoObrigacaoFinanceira($numero_contrato, $row['taseiobroid'], $row['taseivalor_negociado'], $id_equip_classe);
                                            }
                                        }
                                        
                                        // $this->dao->inserirContratoServico($this->id_usuario, $id_termo);
                                        
                                        //STI 81607 - 81608 - Tipo de reajuste 1-IGPM ou 2-INPC
                                        $aplica_reajuste = $this->dao->atualizarTipoReajusteContrato($row['taseiconnumero'], $row['taseireajuste']);
                                        
                                        if(!$aplica_reajuste){
                                            //error
                                            $msg = json_encode(array("tipo_msg" => "e", "msg" => "N&atilde;o foi poss&iacute;vel aplicar o tipo de reajuste (IGPM ou INPC).: ".$id_termo));
                                            throw new Exception($msg);
                                        }
                                    }
                                    
                                }
                            }                            
                        }  
                                              
                    } else{
                        /*
                         * Pendente
                         */
                        $result = $this->dao->alterarTermoAditivoServico($status, $this->id_usuario, $id_termo, $cliente, $observacao, $situacao, $validade);
                    }
                    
                    //ALTERAÇÃO COM SUCESSO
                    if($result > 0){
                        //success
                        $this->dao->commitTransaction();
                        echo json_encode(array("tipo_msg" => "s", "msg" => "Termo Aditivo de Servi&ccedil;o atualizado com sucesso!", "id_termo" => $id_termo));
                        exit;
                    } else{
                        //error
                        $msg = json_encode(array("tipo_msg" => "e", "msg" => "N&atilde;o foi poss&iacute;vel atualizar o TA.: ".$id_termo));
                        throw new Exception($msg);
                    }
                }        
            } else{
                //warning
                $msg = json_encode(array("tipo_msg" => "w", "msg" => "Preencha todos os campos!"));
                throw new Exception($msg);
            }
        
        } catch(Exception $e){          
            $this->dao->rollbackTransaction();
            echo $e->getMessage();
            exit;
        }
    }
    
    
    /**
     * Componente de Pesquisa por nome do cliente.
     * @param Array $dados
     */
    private function getComponentePesquisaClientes($dados){
        if($dados != null){
            $cliente = trim(utf8_decode($dados['cliente']));
            
            if(strlen($cliente) < 3){
                echo json_encode(array("tipo_msg" => "w", "msg" => "O nome do Cliente deve possuir no m&iaculte;nimo 3 caracteres!"));
                exit;
            }
            
            $result = $this->dao->getClientes($cliente);
            
            if($result != null){               
                $this->view->getComponentePesquisaClientes($result);
                exit;
            } else{
                echo json_encode(array("tipo_msg" => "i", "msg" => "Nenhum resultado encontrado."));
                exit;
            }
            
        } else{
            echo json_encode(array("tipo_msg" => "w", "msg" => "Informe o Cliente!"));
            exit;
        }     
    }
    
    
    
    /**
     * Adiciona um item aditivo.
     * @param Array $dados
     * @throws
     */
    private function adicionarItemAditivo($dados){
        try{
            $this->dao->beginTransaction();
            
            if($dados != null){
                if($dados['id_termo'] != ""){              
                    $id_termo        = $dados['id_termo'];
                    $num_contrato    = ($dados['contrato'] != "") ? $dados['contrato'] : "NULL";
                    $id_servico      = ($dados['servico'] != "") ? $dados['servico'] : "NULL";
                    $valor_tabela    = ($dados['valor_tabela'] != "") ? $this->format2Number($dados['valor_tabela']) : "0.00";
                    $valor_negociado = ($dados['valor_negociado'] != "") ? $this->format2Number($dados['valor_negociado']) : "0.00";
                    $desconto        = ($dados['desconto'] != "") ? $this->format2Number($dados['desconto']) : "0.00";
                    $tipo_reajuste   = $dados['tipo_reajuste'];
                    
                    if($tipo_reajuste == ""){
                    	throw new Exception("O tipo de reajuste (IGPM ou INPC) deve ser informado.");
                    }

                    $resLimites = $this->dao->getValoresLimiteObrigacao($id_servico);
                    
                    $result = $this->dao->inserirItemAditivo($id_termo, $num_contrato, $id_servico, $valor_tabela, $valor_negociado, $desconto, $tipo_reajuste);
                    
                    if($result > 0){
                        $id_item    = $result;                    
                        $result     = $this->dao->getServico($id_servico, null, null, true);
                        $pacote     = $this->dao->getPacote();
                        $servico    = $result[0]['obrobrigacao'];
                        $modalidade = $result[0]['modalidade'];
                        $contrato   = $num_contrato;
                        $placa      = "&nbsp;";
                        $chassi     = "&nbsp;";
                        $valor      = number_format($valor_negociado, 2, ",", ".");
                        
                        if($contrato != "NULL"){
                            $result = $this->dao->getDadosVeiculo($contrato);
                            
                            if($result != null){
                                $placa  = $result['veiplaca'];
                                $chassi = $result['veichassi'];
                            }
                        } else{
                            $contrato = "&nbsp;";
                        }
                        
                        //success
                        $this->dao->commitTransaction();
                        $this->view->getLinhaItemAditivo($servico, $modalidade, $contrato, $placa, $chassi, $valor, $id_item);                    
                        exit;
                    } else{
                        //warning
                        $msg = "N&atilde;o foi poss&iacute;vel salvar o item aditivo!";
                        throw new Exception($msg);
                    }
                } else{
                    //warning
                    $msg = "O ID do Termo Aditivo est&aacute; vazio!";
                    throw new Exception($msg);
                }
            } else{
                //warning
                $msg = "Preencha os campos referentes &agrave; Itens!";
                throw new Exception($msg);
            }
        
        } catch(Exception $e){          
            $this->dao->rollbackTransaction();
            echo $e->getMessage();
            exit;
        }
    }
    
    /**
     * Remove o item aditivo
     * @param int $id_item
     * @throws
     */
    private function removerItemAditivo($id_item){
        try{
            $this->dao->beginTransaction();
            
            if($id_item != ""){
                $result = $this->dao->removerItemAditivo($id_item);
                
                if($result > 0){
                    //success
                    $this->dao->commitTransaction();
                    echo json_encode(array("status" => true));
                    exit;
                } else{
                    //error
                    $msg = json_encode(array("status" => false, "tipo_msg" => "e", "msg" => "Erro ao tentar excluir o Item Aditivo!"));
                    throw new Exception($msg);
                }
            } else{
                //warning
                $msg = json_encode(array("status" => false, "tipo_msg" => "w", "msg" => "O ID do Item Aditivo est&aacute; vazio!"));
                throw new Exception($msg);
            }        
        } catch(Exception $e){          
            $this->dao->rollbackTransaction();
            echo $e->getMessage();
            exit;
        }          
    }
    
    /**
     * Excluí o TA e seus itens.
     * @param int $id_termo
     * @throws
     */
    private function excluirTermoAditivoServico($id_termo){
        $id_termo = trim($id_termo);
        
        try{
            $this->dao->beginTransaction();
            
            if($id_termo != ""){
            
                $result = $this->dao->excluirTermoAditivoServico($id_termo, $this->id_usuario);             
                
                if($result > 0){
                    //success
                    $this->dao->commitTransaction();
                    echo json_encode(array("status" => true, "tipo_msg" => "s", "msg" => "Termo Aditivo exclu&iacute;do com sucesso!"));
                    exit;
                } else{
                    //error
                    $msg = json_encode(array("status" => false, "tipo_msg" => "e", "msg" => "Erro ao tentar excluir o Termo Aditivo.: ".$id_termo));
                    throw new Exception($msg);
                }           
            } else{
                //warning
                $msg = json_encode(array("status" => false, "tipo_msg" => "w", "msg" => "O N&uacute;mero do TA est&aacute; vazio!"));
                throw new Exception($msg);
            }
        } catch(Exception $e){          
            $this->dao->rollbackTransaction();
            echo $e->getMessage();
            exit;
        }    
    }
}
?>