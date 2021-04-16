<?php

/**
 * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
 */

/**
 * Arquivo DAO responsável pelas requisições ao banco de dados
 */
require _MODULEDIR_ . 'Relatorio/DAO/RelAtendimentoFrontEndDAO.php';

/**
 * Classes utilizadas na geração de excel e pdf
 */
include_once _SITEDIR_."/lib/tcpdf_php4/config/lang/eng.php";
include_once _SITEDIR_."/lib/tcpdf_php4/tcpdf.php";
include_once _SITEDIR_."/lib/excelwriter.inc.php";

class RelAtendimentoFrontEnd {
    
    private $dao;
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * Método chamado quando não há ação setada, ele apenas chama a view
     */
    public function index() {

        include _MODULEDIR_ . 'Relatorio/View/rel_atendimento_front_end/dataTable.php';        
        
    }
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @param boolean $isAjax retorna json para true array para false
     */
    public function getComboMotivoNivel2($isAjax = true) {
        
        $motivo_nivel_1_id = $_POST['motivo_nivel_1'];
        
        $motivos_nivel_2 = $this->dao->getComboMotivoNivel2($motivo_nivel_1_id);
        
        if($isAjax){
            echo json_encode($motivos_nivel_2);        
            exit;
        }
        
        return $motivos_nivel_2;
        
    }
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * @param boolean $isAjax retorna json para true array para false
     */
    public function getComboMotivoNivel3($isAjax = true) {
        
        $motivo_nivel_2_id = $_POST['motivo_nivel_2'];
        
        $motivos_nivel_3 = $this->dao->getComboMotivoNivel3($motivo_nivel_2_id);
        
        if($isAjax){
            echo json_encode($motivos_nivel_3);        
            exit;
        }
        
        return $motivos_nivel_3;
        
    }
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * Método que realiza a pesquisa do relatório, ele chama dinâmicamente
     * as views para os relatórios analítico, sintético e data_hora. Isto encontra-se
     * encapsulado no método dao->pesquisar
     */
    public function pesquisar($export = array('pdf' => false, 'xls' => false)) { 
        
        $options = array();
        
        $options['tipo_relatorio']      = $_POST['tipo_relatorio'];
        $options['dt_ini']              = urldecode($_POST['dt_ini']);
        $options['dt_fim']              = urldecode($_POST['dt_fim']);
        $options['hora_ini']            = $_POST['hora_ini'];
        $options['hora_fim']            = $_POST['hora_fim'];
        $options['nome_cliente']        = urldecode($_POST['nome_cliente']);
        
        if($options['tipo_relatorio'] == "analitico"){
            $options['placa']               = $_POST['placa'];
            $options['protocolo_sascar']    = $_POST['protocolo_sascar'];
            $options['protocolo_vivo']      = $_POST['protocolo_vivo'];
            $options['tipo_ligacao']        = $_POST['tipo_ligacao'];
            $options['motivo_nivel_1']      = $_POST['motivo_nivel_1'];
            $options['motivo_nivel_2']      = $_POST['motivo_nivel_2'];
            $options['motivo_nivel_3']      = $_POST['motivo_nivel_3'];
            $options['status_protocolo']    = $_POST['status_protocolo'];
            $options['status_aten_mot']     = $_POST['status_aten_mot'];
            $options['numero_resultados']   = $_POST['numero_resultados'];
        }        
        
        $options['atendente']           = !empty($_POST['atendente']) ? $_POST['atendente'] : 0;
        $options['classe_cliente']      = $_POST['classe_cliente'];
        $options['tipo_contrato']       = $_POST['tipo_contrato'];
        $options['pessoa_autorizada']   = $_POST['pessoa_autorizada'];
        
        $dados_pesquisa = $this->dao->pesquisar($options);        
        
        if($export['pdf']) {
            
            $this->getPdf($dados_pesquisa, $options);
            
        } elseif($export['xls']) {
            
            $this->getXLS($dados_pesquisa, $options);
            
        } else {
            include _MODULEDIR_ . 'Relatorio/View/rel_atendimento_front_end/dataTable.php';
        }               
        
    }
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     * Método construtor, ele carrega as combos do relatório.
     */
    public function RelAtendimentoFrontEnd() {
    	
        $this->dao = new RelAtendimentoFrontEndDAO();      
        $this->combo_atendentes     = $this->dao->getComboAtendentes();        
        $this->combo_cliente_classe = $this->dao->getComboClienteClasse();
        $this->combo_tipo_contrato  = $this->dao->getComboTipoContrato();
        $this->combo_motivo_nivel1  = $this->dao->getComboMotivoNivel1();
        
        $this->combo_motivo_nivel2 = array();
        $this->combo_motivo_nivel3 = array();
        
        if(isset($_POST['motivo_nivel_1']) && $_POST['motivo_nivel_1'] != "") {
            $this->combo_motivo_nivel2 = $this->getComboMotivoNivel2(false);
        }
        
        if(isset($_POST['motivo_nivel_2']) && $_POST['motivo_nivel_2'] != "") {
            $this->combo_motivo_nivel3 = $this->getComboMotivoNivel3(false);
        }
        
        
    }
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>
     */
    public function gerarPdf() {        
        $this->pesquisar(array('pdf' => true, 'xls' => false));        
    }
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>     *
     */
    public function gerarXLS() {
        $this->pesquisar(array('pdf' => false, 'xls' => true));
    }
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>     *
     */
    private function getXLS($dados_pesquisa, $options) {

        $nome_arquivo = "/var/www/docs_temporario/rel_atendimento_front_end.xls";
        
        ob_start();
        
        if($options['tipo_relatorio'] == "analitico"){
            include _MODULEDIR_ . 'Relatorio/View/rel_atendimento_front_end/analitico_pdf.php';    
        } else {
             include _MODULEDIR_ . 'Relatorio/View/rel_atendimento_front_end/sintetico_pdf.php'; 
        }

        $relatorioHTML = ob_get_contents();
        ob_end_clean();
        
        file_put_contents($nome_arquivo, $relatorioHTML);
        
        header('Content-Description: File Transfer');
  		header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
  		header('Pragma: public');
  		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
  		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
  		header('Content-Type: application/force-download');
  		header('Content-Type: application/octet-stream', false);
  		header('Content-Type: application/download', false);
  		header('Content-Type: application/xls', false);
  		header('Content-Disposition: attachment; filename="rel_atendimento_front_end.xls";');
  		header('Content-Transfer-Encoding: binary');
  		header('Content-Length: '.filesize($nome_arquivo));
        
        echo $relatorioHTML;
    }
    
    /**
     * @author Ricardo Marangoni da Mota <ricardo.mota@meta.com.br>     *
     */
    private function getPdf($dados_pesquisa, $options) {
        
        ob_start();
        
        if($options['tipo_relatorio'] == "analitico"){
            include _MODULEDIR_ . 'Relatorio/View/rel_atendimento_front_end/analitico_pdf.php';    
        } else {

             include _MODULEDIR_ . 'Relatorio/View/rel_atendimento_front_end/sintetico_pdf.php'; 
        }

        $relatorioHTML = ob_get_contents();
        ob_end_clean();

        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);   

        $titulo_compl = "Período de {$options['dt_ini']} a {$options['dt_fim']}";

        $pdf->SetHeaderData(false, PDF_HEADER_LOGO_WIDTH, 'Relatório Atendimento Front End', $titulo_compl);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);

        $pdf->SetLineStyle(array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));


        if($options['tipo_relatorio']  == 'analitico'){
            $pdf->SetFont('Arial', '', 5);
        } else { 
            $pdf->SetFont('Arial', '', 10);            
        }

        $pdf->AddPage();
        $pdf->writeHTML($relatorioHTML, true, 0, true, 0);
        $pdf->lastPage();

        $pdf->Output('rel_atendimento_front_end.pdf', 'D');
        
    }

    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $var;
    }
    
}