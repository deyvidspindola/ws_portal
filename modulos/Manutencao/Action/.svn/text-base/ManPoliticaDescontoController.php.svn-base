<?php

/**
 * @author Felipe Ribeiro <felipe.ribeiro@ewave.com.br>
 * @since 01/07/2014
 */

header('Content-Type: text/html; charset=ISO-8859-1');
require _SITEDIR_.'modulos/Manutencao/DAO/ManPoliticaDescontoDAO.php';

class ManPoliticaDescontoController{
    
    private $dao;
    private $action;

    public function __construct($request) {

        error_reporting(E_ALL);

        $this->dao = new ManPoliticaDescontoDAO();

        $this->action = isset($request['action']) ? $request['action'] : '';
        
        switch($this->action) {
            
            case 'edit' :
                $this->edit();
            break;

            default :
                $this->index();
            break;
        }
    }

    // Aзгo de listagem inicial
    public function index($response = array()) {
        $politicasDesconto = $this->dao->getAll();
        $historicoPoliticasDesconto = $this->dao->getHistorico();
        require _SITEDIR_.'modulos/Manutencao/View/man_politica_desconto/index_view.php';       
    }

    // Aзгo de ediзгo
    public function edit($response = array()) {

        if(isset($_POST['submit'])) {
            
            $response = $this->dao->formValidation();

            if(empty($response)) {

                $this->dao->update();

                $response = array(
                    'message' => 'Registro alterado com sucesso!',
                    'class'   => 'mensagem sucesso'
                );
            }

            $politicaDesconto = $this->dao->getById($_POST['podoid']);
            $aplicacaoList = $this->dao->getAplicacaoList();

            require _SITEDIR_.'modulos/Manutencao/View/man_politica_desconto/edit_view.php';
        }
        else {

            if(isset($_GET['podoid']) && is_numeric($_GET['podoid'])) {
                $politicaDesconto = $this->dao->getById($_GET['podoid']);
                $aplicacaoList = $this->dao->getAplicacaoList();
                require _SITEDIR_.'modulos/Manutencao/View/man_politica_desconto/edit_view.php';
            }
            else {
                $response = array(
                    'message' => 'Polнtica de desconto invбlida!',
                    'class'   => 'mensagem erro',
                );

                $this->index($response);
            }   
        }          
    }
}
?>