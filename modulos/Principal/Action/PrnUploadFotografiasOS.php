<?php

/**
 * @author Felipe Ribeiro <felipe.ribeiro@ewave.com.br>
 * @since 27/11/2014
 */

header('Content-Type: text/html; charset=ISO-8859-1');

class PrnUploadFotografiasOS {
    
    private $model;
    private $viewPath;
    private $modelPath;
    private $moduleTitle;
    private $pageTitle;

    public function __construct() {

        $this->moduleTitle = 'Tela para Upload de Fotografias';

        $this->viewPath = _SITEDIR_ . 'modulos/Principal/View/prn_upload_fotografias_os/';
        
        $this->modelPath = _SITEDIR_ . 'modulos/Principal/DAO/';

        $this->loadModel('PrnUploadFotografiaOS.php');
        
        $this->model = new PrnUploadFotografiaOS();
    }

    public function index($response = array()) {

        $pageTitle = 'Upload de fotografia para ordem de serviço';
        
        $moduleTitle = $this->getModuleTitle();

        $model = $this->model;

        require $this->loadView('create_view.php');        
    }

    public function create($response = array()) {

        if(isset($_POST['submit'])) {
            
            $response = $this->model->formValidation();

            if(empty($response)) {

                try {
                    
                    $response = $this->model->create();
                    
                } catch (Exception $e) {
                    
                    $response[] = array(
                        'message' => $e->getMessage(),
                        'class'   => 'mensagem erro'
                    );
                    
                }

                if(empty($response)) {
                    
                    $response[] = array(
                        'message' => 'Upload de fotografias realizado com sucesso',
                        'class'   => 'mensagem sucesso'
                    );
                }
            }
        }
        
        $this->index($response);        
    }

    public function loadModel($model) {
        require $this->modelPath . $model;
    }

    public function loadView($view) {
        return $this->viewPath . $view;
    }

    public function getModuleTitle() {
        return $this->moduleTitle;
    }
}
?>