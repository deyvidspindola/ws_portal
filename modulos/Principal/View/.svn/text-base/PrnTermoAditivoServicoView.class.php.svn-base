<?php
/**
 * View - <PrnTermoAditivoServicoView.class.php>
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @package Principal
 * @version 1.0
 * @since 03/04/2012
 */
class PrnTermoAditivoServicoView{

    function __construct(){
    
    }

    /**
     * Renderiza a tela inicial do módulo.
     * @param Array $status
     * @param Array $servico
     */
    public function index($status, $servico, $pacote){
        require _SITEDIR_.'modulos/Principal/View/prn_termo_aditivo_servico/index.view.php';
    }
    
    /**
     * Renderiza a tela de inclusão/edição do termo aditivo de serviço.
     * @param Array $termo_aditivo
     * @param Array $status
     * @param Array $servico
     * @param Array $dados_termo     
     * @param Array $itens_termo
     */
    public function getTelaTermoAditivoServico($termo_aditivo, $status, $servico, $dados_termo, $itens_termo, $dao, $pacote){
        //Total registros
        $total_itens = "";        
        if($itens_termo != null){            
            $count = count($itens_termo);          
            if($count == 1){
                $total_itens = $count." registro.";
            } elseif($count > 1){
                $total_itens = $count." registros.";
            }
        }
        
        //Formatando CPF/CNPJ
        $cpf_cnpj = "";        
        if($dados_termo['clitipo'] == "F"){
            //Pessoa Física
            $cpf_cnpj = trim($dados_termo['clino_cpf']);
            $cpf_cnpj = str_pad($cpf_cnpj, 11, "0", STR_PAD_LEFT);            
        } elseif($dados_termo['clitipo'] == "J"){
            //Pessoa Jurídica
            $cpf_cnpj = trim($dados_termo['clino_cgc']);
            $cpf_cnpj = str_pad($cpf_cnpj, 14, "0", STR_PAD_LEFT);            
        }
        
        require _SITEDIR_.'modulos/Principal/View/prn_termo_aditivo_servico/telaTermoAditivoServico.view.php';
    }
    
    /**
     * Renderiza a tela com o resultado da pesquisa.
     * @param Array $dados
     */
    public function getTelaResultadoPesquisa($dados){
        $total = "";
        $count = count($dados);
        
        if($count == 1){
            $total = $count." registro.";
        } elseif($count > 1){
            $total = $count." registros.";
        }
        
        require _SITEDIR_.'modulos/Principal/View/prn_termo_aditivo_servico/telaResultadoPesquisa.view.php';       
    }
    
    /**
     * Renderiza o HTML da mensagem de feedback.
     * @param String $class
     * @param String $mensagem
     */
    public function setMensagem($class, $mensagem){        
        echo "<div class='$class'>".$mensagem."</div>";
    }
    
    /**
     * Renderiza o HTML com o resultado da pesquisa.
     * @param Array $dados
     */
    public function getComponentePesquisaClientes($dados){
        $html = "";        
        foreach($dados as $row){
            $cpf_cnpj   = "";
            $id_cliente = $row['clioid'];
            $cliente    = $row['clinome'];
                    
            //Pessoa Física
            if(strtoupper($row['clitipo']) == 'F'){
                $cpf_cnpj = $row['clino_cpf'];
                $tipo     = "CPF: ";
            } else{
                //Pessoa Jurídica
                $cpf_cnpj = $row['clino_cgc'];
                $tipo     = "CNPJ: ";
            }
            
            $html .= "<div class='div_link_result other-color' title='$cliente $tipo $cpf_cnpj'>
                        <input type='hidden' value='$cliente' id='cpx_cli_$id_cliente'/>
                        <input type='hidden' value='$cpf_cnpj' id='cpx_cpfcnpj_$cpf_cnpj'/>
                        <span>".$tipo.$cpf_cnpj."</span>
                        <label>".strtolower(substr($cliente,0,20))."...</label>
                        <div style='clear:both;'></div>
                      </div>";
        }
        
        echo json_encode(array("html" => utf8_encode($html)));
    }
    
    /**
     * Renderiza o HTML com a linha do item inserido.
     * @param String $servico
     * @param String $modalidade
     * @param int $contrato
     * @param String $placa
     * @param String $chassi
     * @param String $valor
     * @param int $id_item
     */
    public function getLinhaItemAditivo($servico, $modalidade, $contrato, $placa, $chassi, $valor, $id_item){
        $html = "<tr>
                    <td>".$servico."</td>
                    <td>".$modalidade."</td>
                    <td>".$contrato."</td>
                    <td>".$placa."</td>                                
                    <td>".$chassi."</td>
                    <td align='right'>".$valor."</td>
                    <td style='text-align:center;'>
                        <a href='javascript:void(0);' id='lnk_remove_item_".$id_item."'>
                            <img width='13' height='12' align='absmiddle' title='Remover' alt='Remover' src='images/del.gif'>
                        </a>
                    </td>
                 </tr>";
                 
        echo $html;
    }
}
?>