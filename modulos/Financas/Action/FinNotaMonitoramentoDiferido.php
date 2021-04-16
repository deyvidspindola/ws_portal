<?php
require_once _MODULEDIR_.'Financas/View/FinNotaMonitoramentoDiferidoView.php';
require_once _MODULEDIR_.'Financas/DAO/FinNotaMonitoramentoDiferidoDAO.php';
/**
 * STI 84974 - Cadastro de NOTAS DE SAÍDA do tipo: Monitoramento Diferido.
 * Item 116
 *
 * @class FinNotaMonitoramentoDiferido
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @version 1.0
 * @since 15/12/2014
 */
class FinNotaMonitoramentoDiferido{
    private $view = '';
    private $dao = '';
    private $id_usuario = '';
    
    public function __construct($acao = ''){
        $this->view = new FinNotaMonitoramentoDiferidoView();
        $this->dao  = new FinNotaMonitoramentoDiferidoDAO();
        $this->id_usuario = $_SESSION['usuario']['oid'];

        switch($acao){
            case 'pesquisar':
                $this->pesquisar($_POST['dados']);
                break;
            case 'cadastrar':
                $this->cadastrar();
                break;
            case 'confirmar':
                $this->confirmar($_POST['dados']);
                break;
            case 'excluir':
                $this->excluir($_POST['dados']);
                break;
            default:
                $this->index();
                break;
        }
    }
    
    /**
     * Excluí a nota como Monitoramento Diferido.
     * @param array $dados['id']
     */
    private function excluir($dados){
        if(!empty($dados)){
            $dados = explode('-', $dados['id']);
            $nota  = trim($dados[0]);
            $serie = trim(strtoupper($dados[1]));
            
            $result = $this->dao->excluir($nota, $serie);
            
            if($result){
                echo json_encode(array('msg' => 'Nota exclu&iacute;da com sucesso!', 'tipo' => '#msgsucesso', 'status' => true));
            } else{
                echo json_encode(array('msg' => 'N&atilde;o foi poss&iacute;vel excluir a nota como Monitoramento Diferido.', 'tipo' => '#msgerro', 'status' => false));
            }
        } else{
            echo json_encode(array('msg' => 'N&atilde;o foi poss&iacute;vel excluir a nota como Monitoramento Diferido.', 'tipo' => '#msgerro', 'status' => false));
        }
        exit;
    }
    
    /**
     * Acessa tela inicial (Pesquisar) do módulo.
     */
    protected function index(){
        $serie = $this->dao->getSerie();
        $this->view->index($serie);
    }
    
    /**
     * Realiza a pesquisa das notas.
     * @param array $dados['periodo', 'nota', 'serie']
     */
    protected function pesquisar($dados){
        if(!empty($dados)){
            if(empty($dados['periodo'])){
                echo json_encode(array('msg' => 'Informe o per&iacute;odo.', 'tipo' => '#msgalerta', 'status' => false));
            } elseif(!empty($dados['nota']) && empty($dados['serie'])){
                echo json_encode(array('msg' => 'Informe a s&eacute;rie da nota.', 'tipo' => '#msgalerta', 'status' => false));
            } elseif(!empty($dados['serie']) && empty($dados['nota'])){
                echo json_encode(array('msg' => 'Informe o n&uacte;mero refer&ecirc;ncia da nota.', 'tipo' => '#msgalerta', 'status' => false));
            } else{
                $periodo = explode('/', $dados['periodo']);
                $periodo = $periodo[0].$periodo[1];
                
                $result = $this->dao->pesquisar($periodo, trim($dados['nota']), trim(strtoupper($dados['serie'])));
                $this->view->pesquisar($result);
            }
        } else{
            echo json_encode(array('msg' => 'Informe o per&iacute;odo e/ou a nota e a s&eacute;rie.', 'tipo' => '#msgalerta', 'status' => false));
        }
        exit;
    }
    
    /**
     * Acessa tela de cadastro.
     */
    protected function cadastrar(){
        $serie = $this->dao->getSerie();
        $this->view->cadastrar($serie);
        exit;
    }
    
    /**
     * Realiza o cadastro das notas como Monitoramento Diferido.
     * @param array $dados['nota', 'serie']
     */
    protected function confirmar($dados){
        if(!empty($dados)){
            if(empty($dados['nota'])){
                echo json_encode(array('msg' => 'Informe o n&uacute;mero da nota.', 'tipo' => '#msgalerta', 'status' => false));
            } elseif(empty($dados['serie'])){
                echo json_encode(array('msg' => 'Informe a s&eacute;rie da nota.', 'tipo' => '#msgalerta', 'status' => false));
            } else{
                $result = $this->dao->confirmar(trim($dados['nota']), trim(strtoupper($dados['serie'])));
                
                if($result){
                    echo json_encode(array('msg' => 'Nota cadastrada como Monitoramento Diferido!', 'tipo' => '#msgsucesso', 'status' => true));
                } else{
                    echo json_encode(array('msg' => 'N&atilde;o foi poss&iacute;vel realizar o cadastro.', 'tipo' => '#msgerro', 'status' => false));
                }
            }
        } else{
            echo json_encode(array('msg' => 'Informe a nota e a s&eacute;rie.', 'tipo' => '#msgalerta', 'status' => false));
        }
        exit;
    }    
}