<?php
/**
 * STI 84974 - Cadastro de NOTAS DE SAÍDA do tipo: Monitoramento Diferido.
 * Item 116
 *
 * @class FinNotaMonitoramentoDiferidoView
 * @author Bruno Bonfim Affonso - <bruno.bonfim@sascar.com.br>
 * @version 1.0
 * @since 15/12/2014
 */
class FinNotaMonitoramentoDiferidoView{

    public function __construct(){
    
    }
    
    /**
     * Renderiza a tela inicial do módulo.
     */
    public function index($serie = array()){
        require _SITEDIR_.'modulos/Financas/View/fin_nota_monitoramento_diferido/index.php';
    }
    
    /**
     * Renderiza a tela de cadastro
     */
    public function cadastrar($serie = array()){
        require _SITEDIR_.'modulos/Financas/View/fin_nota_monitoramento_diferido/cadastrar.php';
    }
    
    /**
     * Renderiza a tela com o resultSet da pesquisa.
     */
    public function pesquisar($dados = array()){
        require _SITEDIR_.'modulos/Financas/View/fin_nota_monitoramento_diferido/pesquisar.php';
    }
}