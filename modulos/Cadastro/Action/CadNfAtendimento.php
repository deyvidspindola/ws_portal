<?php

/*
 * require para persistência de dados - classe DAO 
 */
require _MODULEDIR_ . 'Cadastro/DAO/CadNfAtendimentoDAO.php';

require_once _SITEDIR_ . 'lib/phpMailer/class.phpmailer.php';

/**
 * Classes utilizadas na geração de pdf
 */
include_once _SITEDIR_."/lib/tcpdf_php4/config/lang/eng.php";
include_once _SITEDIR_."/lib/tcpdf_php4/tcpdf.php";

/**
 * FinRelContratosRevendaAtraso.php
 * 
 * Classe para buscar os contratos de revenda
 * com atraso na primeira parcela.
 * 
 * 
 * @author  Willian Ouchi
 * @email   willian.ouchi@meta.com.br
 * @since   09/11/2012
 * @package Finanças
 */
class CadNfAtendimento {

    private $dao;
    private $conn;
    private $permissao_total_ocorrencia;

    /*
     * Construtor: Atribui a conexão, instância a DAO e carrega as permissões.
     *  
     * @autor Willian Ouchi
     * @email willian.ouchi@meta.com.br
     */
    public function CadNfAtendimento() {

        global $conn;

        $this->conn = $conn;
        $this->dao = new CadNfAtendimentoDAO($conn);
        $this->permissao_total_ocorrencia = $_SESSION['funcao']['permissao_total_ocorrencia'];
        //$this->permissao_total_ocorrencia = 0;                
    }
    
    /*
     * Metodo para gerar PDF
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */
    public function gerarPDF(){
    
    	$this->dao->nfaoid = (!empty($_POST['id'])) ? $_POST['id'] : '';
    
    	$rs = $this->dao->pesquisar();

    	$valores = pg_fetch_all($rs);
    	
    	$this->valores = array();
    	
    	foreach($valores as $valor) {
    		
    		$this->valores[] = array(
    				'periodo' 						 	 =>  $valor['nfadt_nota'],
    				'valor_fixo' 						 =>  number_format($valor['nfavalor_fixo'], 2,',', '.'),
    				'valor_unidade_recuperada' 			 =>  number_format($valor['nfavalor_unidade_recuperada'], 2,',', '.'),
    				'quantidade_recuperada' 			 =>  number_format($valor['nfaqtde_recuperada'], 0, ',', '.'),
    				'total_recuperado' 					 =>  number_format($valor['nfatotal_recuperado'], 2,',', '.'),
    				'valor_unidade_nao_recuperada' 		 =>  number_format($valor['nfavalor_unidade_nao_recuperado'], 2,',', '.'),
    				'quantidade_nao_recuperada' 		 =>  number_format($valor['nfaqtde_nao_recuperada'], 0, ',', '.'),
    				'total_nao_recuperado' 				 =>  number_format($valor['nfatotal_nao_recuperado'], 2,',', '.'),
    				'valor_variavel' 					 =>  number_format($valor['nfavalor_variavel'], 2,',', '.'),
    				'quantidade_acionamentos_excedentes' =>  number_format($valor['nfaqtde_acionamento_excedente'], 0, ',', '.'),
    				'valor_unidade_excedentes' 			 =>  number_format($valor['nfavalor_unidade_excedente'], 2,',', '.'),
    				'total_fatura' 						 =>  number_format($valor['nfavalor_total'], 2,',', '.'),
                                'previsao_pagamento' =>  $valor['nfadt_previsao_pgto'] 
    		);
    	}
    	
    	
    	$this->acionamentos = array();
    	
    	$rs = $this->dao->buscarItensNota();
    	
    	$acionamentos = pg_fetch_all($rs);
    	
    	foreach($acionamentos as $acionamento) {
    		
    		$this->acionamentos[] = array(
    				'data_acionamento' => $acionamento['prerdt_atendimento'],
    				'veiculo' 		   => $acionamento['prerplaca_veiculo'],
    				'aprovar' 		   => ($acionamento['nfacaprovado'] == 't') ? 'Aprovado' : 'Não aprovado'
    		);
    	}
    	
    
    	$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);
    	 
    	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    	 
    	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    	 
    	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    	$pdf->setPrintHeader(false);
    	$pdf->setPrintFooter(false);
    	$pdf->SetFont('Arial', '', 8);
    
    	ob_start();
    
    	require _MODULEDIR_ . 'Cadastro/View/cad_nf_atendimento/layout_export.php';
    
    	$relatorioHTML = ob_get_contents();
    
    	ob_end_clean();
    
    	$pdf->AddPage();
    	$pdf->writeHTML($relatorioHTML, true, 0, true, 0);
    	$pdf->lastPage();
    
    	$pdf->Output('cad_nf_atendimento.pdf', 'D');
    
    }
    
    /*
     * Metodo que gera arquivo XLS
     * 
     * @autor Renato Teixeira Bueno
     * @email renato.bueno@meta.com.br
     */
    public function gerarXLS() {
    	
    	$this->dao->nfaoid = (!empty($_POST['id'])) ? $_POST['id'] : '';
    	
    	$rs = $this->dao->pesquisar();
    	
    	$valores = pg_fetch_all($rs);
    	 
    	$this->valores = array();
    	 
    	foreach($valores as $valor) {
    	
    		$this->valores[] = array(
    				'periodo' 						 	 => $valor['nfadt_nota'],
    				'valor_fixo' 						 => number_format($valor['nfavalor_fixo'], 2,',', '.'),
    				'valor_unidade_recuperada' 			 => number_format($valor['nfavalor_unidade_recuperada'], 2,',', '.'),
    				'quantidade_recuperada' 			 => number_format($valor['nfaqtde_recuperada'], 0, ',', '.'),
    				'total_recuperado' 					 => number_format($valor['nfatotal_recuperado'], 2,',', '.'),
    				'valor_unidade_nao_recuperada' 		 => number_format($valor['nfavalor_unidade_nao_recuperado'], 2, ',', '.'),
    				'quantidade_nao_recuperada' 		 => number_format($valor['nfaqtde_nao_recuperada'], 0, ',', '.'),
    				'total_nao_recuperado' 				 => number_format($valor['nfatotal_nao_recuperado'], 2, ',', '.'),
    				'valor_variavel' 					 => number_format($valor['nfavalor_variavel'], 2, ',', '.'),
    				'quantidade_acionamentos_excedentes' => number_format($valor['nfaqtde_acionamento_excedente'], 0, ',', '.'),
    				'valor_unidade_excedentes' 			 => number_format($valor['nfavalor_unidade_excedente'], 2, ',', '.'),
    				'total_fatura' 						 => number_format($valor['nfavalor_total'], 2, ',', '.'),
                                'previsao_pagamento' =>  $valor['nfadt_previsao_pgto'] 
    		);
    	}
    	 
    	 
    	$this->acionamentos = array();
    	 
    	$rs = $this->dao->buscarItensNota();
    	 
    	$acionamentos = pg_fetch_all($rs);
    	 
    	foreach($acionamentos as $acionamento) {
    	
    		$this->acionamentos[] = array(
    				'data_acionamento' => $acionamento['prerdt_atendimento'],
    				'veiculo' 		   => $acionamento['prerplaca_veiculo'],
    				'aprovar' 		   => ($acionamento['nfacaprovado'] == 't') ? 'Aprovado' : 'Não aprovado'
    		);
    	}
    	
    	ob_start();
    	
    	require _MODULEDIR_ . 'Cadastro/View/cad_nf_atendimento/layout_export.php';
    	
    	$relatorioHTML = ob_get_contents();
    	
    	ob_end_clean();
    	
    	header('Content-Description: File Transfer');
    	header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
    	header('Pragma: public');
    	header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    	header('Content-Type: application/force-download');
    	header('Content-Type: application/octet-stream', false);
    	header('Content-Type: application/download', false);
    	header('Content-Type: application/xls', false);
    	header('Content-Disposition: attachment; filename="rel_nf_atendimento.xls";');
    	header('Content-Transfer-Encoding: binary');
    	header('Content-Length: '.filesize($nome_arquivo));
    	
    	echo $relatorioHTML;
    	exit;
    }

    
    /*
     * Carrega as informações primárias da tela. Ex: Valores para combos e permissões.
     * 
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	array()
     */
    public function carregarInformacoes() {

        $resultado = array();
        $tetoid = null;
        
        /*
         * O retorno da permissão é carregado em um input hidden por jQuery para
         * fazer o tratamento do layout de acordo com a permissão do usuário.
         */
        $resultado['permissao_total_ocorrencia'] = $this->permissao_total_ocorrencia;

        /*
         * Se o usuário não tiver a permissão de visualização total
         * deve buscar a equipe que o usuário pertence e mostrar apenas esta 
         * equipe no combo de filtragem por equipe.
         */
        if (!$this->permissao_total_ocorrencia) {

            $rs = $this->dao->buscarEquipeUsuario();
            $rEquipe = pg_fetch_assoc($rs);
            $tetoid = $rEquipe['tetoid'];
        }

        $resultado['tetoid'] = $tetoid;
        
        $rs = $this->dao->buscarEquipes($tetoid);
        $cont = 0;
        if (pg_num_rows($rs) > 1){
            $resultado['equipes'][0]['tetoid'] = "";
            $resultado['equipes'][0]['tetdescricao'] = "Todos";
            $cont++;
        }
        
        while ($rEquipe = pg_fetch_assoc($rs)) {

            $resultado['equipes'][$cont]['tetoid'] = (!isset($rEquipe['tetoid'])) ? '' : $rEquipe['tetoid'];
            $resultado['equipes'][$cont]['tetdescricao'] = empty($rEquipe['tetdescricao']) ? '' : utf8_encode($rEquipe['tetdescricao']);
            $cont++;
        }

        echo json_encode($resultado);
        exit;
    }

    
    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	Informações primárias da tela. Ex: Valores para combos.
     */
    public function buscarNF() {

        $this->dao->nfaoid = (isset($_POST['nfaoid'])) ? $_POST['nfaoid'] : "";
        $this->dao->tetoid = !empty($_POST['tetoid']) ? $_POST['tetoid'] : $this->dao->getEquipeNf($this->dao->nfaoid);

        $resultado = array();
        $rs = $this->dao->pesquisar();
        $rNF = pg_fetch_assoc($rs);
        
        $resultado['nf']['nfaoid'] = (isset($rNF['nfaoid'])) ? $rNF['nfaoid'] : '';
        $resultado['nf']['nfadt_nota_inicial'] = (isset($rNF['nfadt_nota_inicial'])) ? $rNF['nfadt_nota_inicial'] : '';
        $resultado['nf']['nfadt_nota_final'] = (isset($rNF['nfadt_nota_final'])) ? $rNF['nfadt_nota_final'] : '';
        $resultado['nf']['nfavalor_fixo'] = (isset($rNF['nfavalor_fixo'])) ? $this->valorTela($rNF['nfavalor_fixo']) : '';
        $resultado['nf']['nfavalor_unidade_recuperada'] = (isset($rNF['nfavalor_unidade_recuperada'])) ? $this->valorTela($rNF['nfavalor_unidade_recuperada']) : '';
        $resultado['nf']['nfaqtde_recuperada'] = (isset($rNF['nfaqtde_recuperada'])) ? $rNF['nfaqtde_recuperada'] : '';
        $resultado['nf']['nfatotal_recuperado'] = (isset($rNF['nfatotal_recuperado'])) ? $this->valorTela($rNF['nfatotal_recuperado']) : '';
        $resultado['nf']['nfavalor_unidade_nao_recuperado'] = (isset($rNF['nfavalor_unidade_nao_recuperado'])) ? $this->valorTela($rNF['nfavalor_unidade_nao_recuperado']) : '';
        $resultado['nf']['nfaqtde_nao_recuperada'] = (isset($rNF['nfaqtde_nao_recuperada'])) ? $rNF['nfaqtde_nao_recuperada'] : '';
        $resultado['nf']['nfatotal_nao_recuperado'] = (isset($rNF['nfatotal_nao_recuperado'])) ? $this->valorTela($rNF['nfatotal_nao_recuperado']) : '';
        $resultado['nf']['nfavalor_variavel'] = (isset($rNF['nfavalor_variavel'])) ? $this->valorTela($rNF['nfavalor_variavel']) : '';
        $resultado['nf']['nfaqtde_acionamento_excedente'] = (isset($rNF['nfaqtde_acionamento_excedente'])) ? $rNF['nfaqtde_acionamento_excedente'] : '';
        $resultado['nf']['nfavalor_unidade_excedente'] = (isset($rNF['nfavalor_unidade_excedente'])) ? $this->valorTela($rNF['nfavalor_unidade_excedente']) : '';
        $resultado['nf']['nfavalor_total'] = (isset($rNF['nfavalor_total'])) ? $this->valorTela($rNF['nfavalor_total']) : '';
        $resultado['nf']['nfadt_previsao_pgto'] = (isset($rNF['nfadt_previsao_pgto'])) ? $rNF['nfadt_previsao_pgto'] : '';

        if (!$this->permissao_total_ocorrencia){

            $rs = $this->dao->buscarAcionamentos();
            $cont = 0;
            while ($rAcionamnetos = pg_fetch_assoc($rs)) {

                $resultado['acionamentos'][$cont]['preroid'] = (isset($rAcionamnetos['preroid'])) ? $rAcionamnetos['preroid'] : '';
                $resultado['acionamentos'][$cont]['prerdt_atendimento'] = (isset($rAcionamnetos['prerdt_atendimento'])) ? $rAcionamnetos['prerdt_atendimento'] : '';
                $resultado['acionamentos'][$cont]['prerplaca_veiculo'] = (isset($rAcionamnetos['prerplaca_veiculo'])) ? $rAcionamnetos['prerplaca_veiculo'] : '';
                $cont++;
            }
        }

        $rs = $this->dao->buscarItensNota();
        $resultado['quantidade_itens_nota'] = pg_num_rows($rs);
        
        $cont = 0;
        while ($rItensNota = pg_fetch_assoc($rs)) {

            $resultado['itens_nota'][$cont]['nfacoid'] = (isset($rItensNota['nfacoid'])) ? $rItensNota['nfacoid'] : '';
            $resultado['itens_nota'][$cont]['nfacdt_exclusao'] = (isset($rItensNota['nfacdt_exclusao'])) ? $rItensNota['nfacdt_exclusao'] : '';

            if ($this->permissao_total_ocorrencia) {
                
                 $resultado['itens_nota'][$cont]['aprovado'] = ($rItensNota['nfacaprovado'] == 't') ? 
                 '<button nfacoid="'.$resultado['itens_nota'][$cont]['nfacoid'].'" class="reprova_item_nf">Reprovar</button>' : 
                 '<button nfacoid="'.$resultado['itens_nota'][$cont]['nfacoid'].'" class="aprova_item_nf">Aprovar</button>';
            } else {
                $resultado['itens_nota'][$cont]['aprovado'] = ($rItensNota['nfacaprovado'] == 't') ? 'Aprovado' : 'Reprovado';
            }
            $resultado['itens_nota'][$cont]['excluir'] = ($rItensNota['nfacaprovado'] == 'f') ? '<b>[</b><img nfacoid="'.$resultado['itens_nota'][$cont]['nfacoid'].'" class="exclui_item_nf" align="absmiddle" height="12" width="13" title="Remover" alt="Remover" src="images/del.gif"><b>]</b>' : '';

            $resultado['itens_nota'][$cont]['nacaprovado'] = (isset($rItensNota['nfacaprovado'])) ? $rItensNota['nfacaprovado'] : '';

            $resultado['itens_nota'][$cont]['prerdt_atendimento'] = (isset($rItensNota['prerdt_atendimento'])) ? $rItensNota['prerdt_atendimento'] : '';
            $resultado['itens_nota'][$cont]['prerplaca_veiculo'] = (isset($rItensNota['prerplaca_veiculo'])) ? $rItensNota['prerplaca_veiculo'] : '';
            $cont++;
        }
         
        $rs = $this->dao->nfReprovada();
        $resultado['quantidade_nf_reprovada'] = pg_num_rows($rs);
        
        $resultado['nf_aprovada'] = ($resultado['quantidade_itens_nota'] == 0 || $resultado['quantidade_nf_reprovada'] > 0) ? false : true;
        
        $resultado['anexos'] = $this->getAnexosAtendimento($this->dao->nfaoid);
        
        echo json_encode($resultado);
        exit;
    }

    
    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	
     */
    public function insereAcionamento() {

        $this->dao->nfaoid = (isset($_POST['nfaoid'])) ? $_POST['nfaoid'] : "";
        $this->dao->preroid = (isset($_POST['preroid'])) ? $_POST['preroid'] : "";
        $this->dao->tetoid = $this->dao->getEquipeNf($this->dao->nfaoid);
        
        $rs = $this->dao->insereAcionamento();
        
        if ($rs) {

            $rs = $this->dao->buscarItensNota();
            $cont = 0;
            while ($rItensNota = pg_fetch_assoc($rs)) {

                $resultado['itens_nota'][$cont]['nfacoid'] = (isset($rItensNota['nfacoid'])) ? $rItensNota['nfacoid'] : '';
                $resultado['itens_nota'][$cont]['nfacdt_exclusao'] = (isset($rItensNota['nfacdt_exclusao'])) ? $rItensNota['nfacdt_exclusao'] : '';
                if ($this->permissao_total_ocorrencia) {
                   
                    $resultado['itens_nota'][$cont]['aprovado'] = ($rItensNota['nfacaprovado'] == 't') ? 
                    '<button nfacoid="'.$resultado['itens_nota'][$cont]['nfacoid'].'" class="reprova_item_nf">Reprovar</button>' : 
                    '<button nfacoid="'.$resultado['itens_nota'][$cont]['nfacoid'].'" class="aprova_item_nf">Aprovar</button>';
                } else {
                    
                    $resultado['itens_nota'][$cont]['aprovado'] = ($rItensNota['nfacaprovado'] == 't') ? 'Aprovado' : 'Reprovado';
                }
                
                $resultado['itens_nota'][$cont]['excluir'] = ($rItensNota['nfacaprovado'] == 'f') ? '<b>[</b><img nfacoid="'.$resultado['itens_nota'][$cont]['nfacoid'].'" class="exclui_item_nf" align="absmiddle" height="12" width="13" title="Remover" alt="Remover" src="images/del.gif"><b>]</b>' : '';
                $resultado['itens_nota'][$cont]['nfacaprovado'] = (isset($rItensNota['nfacaprovado'])) ? $rItensNota['nfacaprovado'] : '';
                $resultado['itens_nota'][$cont]['prerdt_atendimento'] = (isset($rItensNota['prerdt_atendimento'])) ? $rItensNota['prerdt_atendimento'] : '';
                $resultado['itens_nota'][$cont]['prerplaca_veiculo'] = (isset($rItensNota['prerplaca_veiculo'])) ? $rItensNota['prerplaca_veiculo'] : '';
                $cont++;
            }
            
            $rs = $this->dao->buscarAcionamentos();        
            $cont = 0;
            while ($rAcionamnetos = pg_fetch_assoc($rs)) {

                $resultado['acionamentos'][$cont]['preroid'] = (isset($rAcionamnetos['preroid'])) ? $rAcionamnetos['preroid'] : '';
                $resultado['acionamentos'][$cont]['prerdt_atendimento'] = (isset($rAcionamnetos['prerdt_atendimento'])) ? $rAcionamnetos['prerdt_atendimento'] : '';
                $resultado['acionamentos'][$cont]['prerplaca_veiculo'] = (isset($rAcionamnetos['prerplaca_veiculo'])) ? $rAcionamnetos['prerplaca_veiculo'] : '';
                $cont++;
            }
            
            echo json_encode($resultado);
            exit;
            
        }
    }

    
    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	
     */
    public function excluiItemNF() {
        
        $this->dao->nfacoid = (isset($_POST['nfacoid'])) ? $_POST['nfacoid'] : "";
        $this->dao->nfaoid = (isset($_POST['nfaoid'])) ? $_POST['nfaoid'] : "";
        $this->dao->tetoid = (!empty($_POST['tetoid'])) ? $_POST['tetoid'] : $this->dao->getEquipeNf($this->dao->nfaoid);
        
        
        $this->dao->excluiItemNF();
        
        $rs = $this->dao->buscarAcionamentos();        
        $cont = 0;
        while ($rAcionamnetos = pg_fetch_assoc($rs)) {

            $resultado['acionamentos'][$cont]['preroid'] = (isset($rAcionamnetos['preroid'])) ? $rAcionamnetos['preroid'] : '';
            $resultado['acionamentos'][$cont]['prerdt_atendimento'] = (isset($rAcionamnetos['prerdt_atendimento'])) ? $rAcionamnetos['prerdt_atendimento'] : '';
            $resultado['acionamentos'][$cont]['prerplaca_veiculo'] = (isset($rAcionamnetos['prerplaca_veiculo'])) ? $rAcionamnetos['prerplaca_veiculo'] : '';
            $cont++;
        }
        
        /*
        *   Verifica se existem itens da NF reprovados,
        *   caso não possua nenhum item reprovado retorna "nf_aprovada" = true 
        *   e envia um e-mail para a equipe da NF.
        */
        $this->dao->nfacoid = null;
        $rs = $this->dao->buscarItensNota();
        $resultado['quantidade_itens_nota'] = pg_num_rows($rs);
         
        $rs = $this->dao->nfReprovada();
        $resultado['quantidade_nf_reprovada'] = pg_num_rows($rs);
        
        $resultado['nf_aprovada'] = ($resultado['quantidade_itens_nota'] == 0 || $resultado['quantidade_nf_reprovada'] > 0) ? false : true;
        if ($resultado['nf_aprovada']){

            $this->enviaEmail();
        }
        
        echo json_encode($resultado);
        exit;
    }
    
    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	
     */
    public function aprovaItemNF() {
        
        $resultado = array();
        $aprovado = true;
        
        $this->dao->nfaoid = (isset($_POST['nfaoid'])) ? $_POST['nfaoid'] : "";
        $this->dao->nfacoid = (isset($_POST['nfacoid'])) ? $_POST['nfacoid'] : "";
        
        $rs = $this->dao->aprovaItemNF();
        if ($rs){
            
            $rs = $this->dao->buscarItensNota();
            $resultado['quantidade_itens_nota'] = pg_num_rows($rs);
            
            $rItensNota = pg_fetch_assoc($rs);
            
            
            $resultado['itens_nota'][0]['nfacoid'] = (isset($rItensNota['nfacoid'])) ? $rItensNota['nfacoid'] : '';
            $resultado['itens_nota'][0]['nfacdt_exclusao'] = (isset($rItensNota['nfacdt_exclusao'])) ? $rItensNota['nfacdt_exclusao'] : '';
            $resultado['itens_nota'][0]['aprovado'] = ($rItensNota['nfacaprovado'] == 't') ? 
            '<button nfacoid="'.$resultado['itens_nota'][0]['nfacoid'].'" class="reprova_item_nf">Reprovar</button>' : 
            '<button nfacoid="'.$resultado['itens_nota'][0]['nfacoid'].'" class="aprova_item_nf">Aprovar</button>';
            $resultado['itens_nota'][0]['excluir'] = ($rItensNota['nfacaprovado'] == 'f') ? '<b>[</b><img nfacoid="'.$resultado['itens_nota'][0]['nfacoid'].'" class="exclui_item_nf" align="absmiddle" height="12" width="13" title="Remover" alt="Remover" src="images/del.gif"><b>]</b>' : '';
            $resultado['itens_nota'][0]['nacaprovado'] = (isset($rItensNota['nacaprovado'])) ? $rItensNota['nacaprovado'] : '';
            $resultado['itens_nota'][0]['prerdt_atendimento'] = (isset($rItensNota['prerdt_atendimento'])) ? $rItensNota['prerdt_atendimento'] : '';
            $resultado['itens_nota'][0]['prerplaca_veiculo'] = (isset($rItensNota['prerplaca_veiculo'])) ? $rItensNota['prerplaca_veiculo'] : '';

            $resultado['error'] = false;
            
            /*
            *   Verifica se existem itens da NF reprovados,
            *   caso não possua nenhum item reprovado retorna "nf_aprovada" = true 
            *   e envia um e-mail para a equipe da NF.
            */
            $rs = $this->dao->nfReprovada();
            $resultado['quantidade_nf_reprovada'] = pg_num_rows($rs);
            
            $resultado['nf_aprovada'] = ($resultado['quantidade_itens_nota'] == 0 || $resultado['quantidade_nf_reprovada'] > 0) ? false : true;
            if ($resultado['nf_aprovada']){
              
                $this->enviaEmail();
            }
            
            echo json_encode($resultado);
        }
        
        exit;
    }
    
    
    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	
     */
    public function reprovaItemNF() {
        
        $resultado = array();
        
        $this->dao->nfaoid = (isset($_POST['nfaoid'])) ? $_POST['nfaoid'] : "";
        $this->dao->nfacoid = (isset($_POST['nfacoid'])) ? $_POST['nfacoid'] : "";
        
        $rs = $this->dao->reprovaItemNF();
        
        if ($rs){
            
            $rs = $this->dao->buscarItensNota();
            $resultado['quantidade_itens_nota'] = pg_num_rows($rs);
                        
            $resultado['error'] = false;

            $rs = $this->dao->buscarItensNota();
            $rItensNota = pg_fetch_assoc($rs);

            $resultado['itens_nota'][0]['nfacoid'] = (isset($rItensNota['nfacoid'])) ? $rItensNota['nfacoid'] : '';
            $resultado['itens_nota'][0]['nfacdt_exclusao'] = (isset($rItensNota['nfacdt_exclusao'])) ? $rItensNota['nfacdt_exclusao'] : '';
            $resultado['itens_nota'][0]['aprovado'] = ($rItensNota['nfacaprovado'] == 't') ? 
            '<button nfacoid="'.$resultado['itens_nota'][0]['nfacoid'].'" class="reprova_item_nf">Reprovar</button>' : 
            '<button nfacoid="'.$resultado['itens_nota'][0]['nfacoid'].'" class="aprova_item_nf">Aprovar</button>';
            $resultado['itens_nota'][0]['excluir'] = ($rItensNota['nfacaprovado'] == 'f') ? '<b>[</b><img nfacoid="'.$resultado['itens_nota'][0]['nfacoid'].'" class="exclui_item_nf" align="absmiddle" height="12" width="13" title="Remover" alt="Remover" src="images/del.gif"><b>]</b>' : '';
            $resultado['itens_nota'][0]['nfacaprovado'] = (isset($rItensNota['nfacaprovado'])) ? $rItensNota['nfacaprovado'] : '';
            $resultado['itens_nota'][0]['prerdt_atendimento'] = (isset($rItensNota['prerdt_atendimento'])) ? $rItensNota['prerdt_atendimento'] : '';
            $resultado['itens_nota'][0]['prerplaca_veiculo'] = (isset($rItensNota['prerplaca_veiculo'])) ? $rItensNota['prerplaca_veiculo'] : '';
            
            /*
            *   Verifica se existem itens da NF reprovados,
            *   caso não possua nenhum item reprovado retorna "nf_aprovada" = true 
            *   e envia um e-mail para a equipe da NF.
            */
            $rs = $this->dao->nfReprovada();
            $resultado['quantidade_nf_reprovada'] = pg_num_rows($rs);
            
            $resultado['nf_aprovada'] = ($resultado['quantidade_itens_nota'] == 0 || $resultado['quantidade_nf_reprovada'] > 0) ? false : true;
            
            echo json_encode($resultado);
        }
        
        exit;
    }
    

    

    
    /*
     * Envia e-mail para a equipe da NF.
     */
    public function enviaEmail(){
        
        $this->dao->nfaoid = (isset($_POST['nfaoid'])) ? $_POST['nfaoid'] : "";
        $this->dao->tetoid = !empty($_POST['tetoid']) ? $_POST['tetoid'] : $this->dao->getEquipeNf($this->dao->nfaoid);
        
        $destinatario = $this->dao->buscarEmailEquipe();
        if ($_SESSION['servidor_teste'] == 1){
            $destinatario = 'dayana.cruz@meta.com.br';
        }
        $mail = new PHPMailer();        
        
        $mail->IsHTML(true);
        $mail->IsSMTP();
        $mail->From = "sistema@sascar.com.br";
        $mail->FromName = "Intranet SASCAR - E-mail automático";
        $mail->Subject = "ENVIO DE NOTA FISCAL - URGENTE";

        $mail->MsgHTML("
            Prezados,<br><br>
            Sua Nota Fiscal foi aprovada, necessitamos que a mesma seja anexada ao sistema para que possamos destina-lá ao setor financeiro e agendarmos o pagamento.<br><br>
            Lembrando que nossos pagamentos são realizados conforme as normas pré-estabelecidas pelo contratante.<br><br>
            O valor a ser pago só será destinado ao setor responsável após o recebimento da NF em nosso sistema, caso contrário o pagamento ficará pendente.<br><br>
            Qualquer dúvida, estamos a disposição.
        ");

        if($this->validaEmail($destinatario)) {                  
            $mail->ClearAllRecipients();
            $mail->AddAddress($destinatario);
            $mail->Send();
        }        
        
    }

    private function validaEmail($email) {
        if (substr_count($email, "@") == 0) {
            // Verifica se o e-mail possui @
            return false;
        }
        
        $parseEmail = explode("@", $email);
        
        if (strlen($parseEmail[0]) < 1) {
            //Verifica se o email tem mais de 1 caracter antes do @
            return false;
        }
        
        if (!checkdnsrr($parseEmail[1], "MX")) {
            // Verificar se o domínio existe 
            return false;
        }
        
        return true;
    }  
    
    
    
    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	Valor formatado para o banco
     */
    private function valorBanco($valor) {

        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        return $valor;
    }

    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	Valor formatado para a tela
     */
    private function valorTela($valor) {

        $valor = number_format($valor, 2, ',', '.');
        return $valor;
    }

    
    
    
    
/* NF DE ATENDIMENTO */

    
    /*
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	ID (nfaoid) do registro inserido na tabela nf_atendimento
     */
    public function inserir() {
        
        $this->dao->tetoid = (isset($_POST['tetoid'])) ? $_POST['tetoid'] : '';
        $this->dao->nfadt_nota_inicial = (isset($_POST['nfadt_nota_inicial'])) ? $_POST['nfadt_nota_inicial'] : '';
        $this->dao->nfadt_nota_final = (isset($_POST['nfadt_nota_final'])) ? $_POST['nfadt_nota_final'] : '';
        $this->dao->nfavalor_fixo = (!empty($_POST['nfavalor_fixo'])) ? $this->valorBanco($_POST['nfavalor_fixo']) : 0;
        $this->dao->nfavalor_unidade_recuperada = (!empty($_POST['nfavalor_unidade_recuperada'])) ? $this->valorBanco($_POST['nfavalor_unidade_recuperada']) : 0;
        $this->dao->nfaqtde_recuperada = (!empty($_POST['nfaqtde_recuperada'])) ? $this->valorBanco($_POST['nfaqtde_recuperada']) : 0;
        $this->dao->nfatotal_recuperado = (!empty($_POST['nfatotal_recuperado'])) ? $this->valorBanco($_POST['nfatotal_recuperado']) : 0;
        $this->dao->nfavalor_unidade_nao_recuperado = (!empty($_POST['nfavalor_unidade_nao_recuperado'])) ? $this->valorBanco($_POST['nfavalor_unidade_nao_recuperado']) : 0;
        $this->dao->nfaqtde_nao_recuperada = (!empty($_POST['nfaqtde_nao_recuperada'])) ? $this->valorBanco($_POST['nfaqtde_nao_recuperada']) : 0;
        $this->dao->nfatotal_nao_recuperado = (!empty($_POST['nfatotal_nao_recuperado'])) ? $this->valorBanco($_POST['nfatotal_nao_recuperado']) : 0;
        $this->dao->nfaqtde_acionamento_excedente = (!empty($_POST['nfaqtde_acionamento_excedente'])) ? $this->valorBanco($_POST['nfaqtde_acionamento_excedente']) : 0;
        $this->dao->nfavalor_unidade_excedente = (!empty($_POST['nfavalor_unidade_excedente'])) ? $this->valorBanco($_POST['nfavalor_unidade_excedente']) : 0;
        $this->dao->nfavalor_total = (!empty($_POST['nfavalor_total'])) ? $this->valorBanco($_POST['nfavalor_total']) : 0;
        $this->dao->nfavalor_variavel = (!empty($_POST['nfavalor_variavel'])) ? $this->valorBanco($_POST['nfavalor_variavel']) : 0;
        $this->dao->nfadt_previsao_pgto = (!empty($_POST['nfadt_previsao_pgto'])) ? $_POST['nfadt_previsao_pgto'] : 'null';

        $rs = $this->dao->inserir();

        $nfaoid = pg_fetch_assoc($rs);

        $resultado['nfaoid'] = $nfaoid['nfaoid'];
        $this->dao->nfaoid = $nfaoid['nfaoid'];
        
        if (!$this->permissao_total_ocorrencia){

            $rs = $this->dao->buscarAcionamentos();
            $cont = 0;
            while ($rAcionamnetos = pg_fetch_assoc($rs)) {

                $resultado['acionamentos'][$cont]['preroid'] = (isset($rAcionamnetos['preroid'])) ? $rAcionamnetos['preroid'] : '';
                $resultado['acionamentos'][$cont]['prerdt_atendimento'] = (isset($rAcionamnetos['prerdt_atendimento'])) ? $rAcionamnetos['prerdt_atendimento'] : '';
                $resultado['acionamentos'][$cont]['prerplaca_veiculo'] = (isset($rAcionamnetos['prerplaca_veiculo'])) ? $rAcionamnetos['prerplaca_veiculo'] : '';
                $cont++;
            }
        }
        
        echo json_encode($resultado);

        exit;
    }

    
    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	Resource do Update
     */
    public function editar() {

        $this->dao->nfaoid = (isset($_POST['nfaoid'])) ? $_POST['nfaoid'] : "";
        $this->dao->nfadt_nota_inicial = (isset($_POST['nfadt_nota_inicial'])) ? $_POST['nfadt_nota_inicial'] : "";
        $this->dao->nfadt_nota_final = (isset($_POST['nfadt_nota_final'])) ? $_POST['nfadt_nota_final'] : "";
        $this->dao->nfavalor_fixo = (!empty($_POST['nfavalor_fixo'])) ? $this->valorBanco($_POST['nfavalor_fixo']) : 0;
        $this->dao->nfavalor_unidade_recuperada = (!empty($_POST['nfavalor_unidade_recuperada'])) ? $this->valorBanco($_POST['nfavalor_unidade_recuperada']) : 0;
        $this->dao->nfaqtde_recuperada = (!empty($_POST['nfaqtde_recuperada'])) ? $this->valorBanco($_POST['nfaqtde_recuperada']) : 0;
        $this->dao->nfatotal_recuperado = (!empty($_POST['nfatotal_recuperado'])) ? $this->valorBanco($_POST['nfatotal_recuperado']) : 0;
        $this->dao->nfavalor_unidade_nao_recuperado = (!empty($_POST['nfavalor_unidade_nao_recuperado'])) ? $this->valorBanco($_POST['nfavalor_unidade_nao_recuperado']) : 0;
        $this->dao->nfaqtde_nao_recuperada = (!empty($_POST['nfaqtde_nao_recuperada'])) ? $this->valorBanco($_POST['nfaqtde_nao_recuperada']) : 0;
        $this->dao->nfatotal_nao_recuperado = (!empty($_POST['nfatotal_nao_recuperado'])) ? $this->valorBanco($_POST['nfatotal_nao_recuperado']) : 0;
        $this->dao->nfaqtde_acionamento_excedente = (!empty($_POST['nfaqtde_acionamento_excedente'])) ? $this->valorBanco($_POST['nfaqtde_acionamento_excedente']) : 0;
        $this->dao->nfavalor_unidade_excedente = (!empty($_POST['nfavalor_unidade_excedente'])) ? $this->valorBanco($_POST['nfavalor_unidade_excedente']) : 0;
        $this->dao->nfavalor_total = (!empty($_POST['nfavalor_total'])) ? $this->valorBanco($_POST['nfavalor_total']) : 0;
        $this->dao->nfavalor_variavel = (!empty($_POST['nfavalor_variavel'])) ? $this->valorBanco($_POST['nfavalor_variavel']) : 0;
        $this->dao->nfadt_previsao_pgto = (isset($_POST['nfadt_previsao_pgto'])) ? $_POST['nfadt_previsao_pgto'] : "";
        $this->dao->tetoid = (isset($_POST['tetoid'])) ? $_POST['tetoid'] : "";
        $rs = $this->dao->editar();
        
        $resultado['nfaoid'] =  $this->dao->nfaoid;
        
        if (!$this->permissao_total_ocorrencia){

            $rs = $this->dao->buscarAcionamentos();
            $cont = 0;
            while ($rAcionamnetos = pg_fetch_assoc($rs)) {

                $resultado['acionamentos'][$cont]['preroid'] = (isset($rAcionamnetos['preroid'])) ? $rAcionamnetos['preroid'] : '';
                $resultado['acionamentos'][$cont]['prerdt_atendimento'] = (isset($rAcionamnetos['prerdt_atendimento'])) ? $rAcionamnetos['prerdt_atendimento'] : '';
                $resultado['acionamentos'][$cont]['prerplaca_veiculo'] = (isset($rAcionamnetos['prerplaca_veiculo'])) ? $rAcionamnetos['prerplaca_veiculo'] : '';
                $cont++;
            }
        }
        
        $rs = $this->dao->buscarItensNota();
        $cont = 0;
        while ($rItensNota = pg_fetch_assoc($rs)) {

            $resultado['itens_nota'][$cont]['nfacoid'] = (isset($rItensNota['nfacoid'])) ? $rItensNota['nfacoid'] : '';
            $resultado['itens_nota'][$cont]['nfacdt_exclusao'] = (isset($rItensNota['nfacdt_exclusao'])) ? $rItensNota['nfacdt_exclusao'] : '';

            if ($this->permissao_total_ocorrencia) {

                 $resultado['itens_nota'][$cont]['aprovado'] = ($rItensNota['nfacaprovado'] == 't') ? 
                 '<button nfacoid="'.$resultado['itens_nota'][$cont]['nfacoid'].'" class="reprova_item_nf">Reprovar</button>' : 
                 '<button nfacoid="'.$resultado['itens_nota'][$cont]['nfacoid'].'" class="aprova_item_nf">Aprovar</button>';
            } else {
                $resultado['itens_nota'][$cont]['aprovado'] = ($rItensNota['nfacaprovado'] == 't') ? 'Aprovado' : 'Reprovado';
            }
            $resultado['itens_nota'][$cont]['excluir'] = ($rItensNota['nfacaprovado'] == 'f') ? '<b>[</b><img nfacoid="'.$resultado['itens_nota'][$cont]['nfacoid'].'" class="exclui_item_nf" align="absmiddle" height="12" width="13" title="Remover" alt="Remover" src="images/del.gif"><b>]</b>' : '';

            $resultado['itens_nota'][$cont]['nfacaprovado'] = (isset($rItensNota['nfacaprovado'])) ? $rItensNota['nfacaprovado'] : '';

            $resultado['itens_nota'][$cont]['prerdt_atendimento'] = (isset($rItensNota['prerdt_atendimento'])) ? $rItensNota['prerdt_atendimento'] : '';
            $resultado['itens_nota'][$cont]['prerplaca_veiculo'] = (isset($rItensNota['prerplaca_veiculo'])) ? $rItensNota['prerplaca_veiculo'] : '';
            $cont++;
        } 

        echo json_encode($resultado);

        exit;
    }

    
    /**
     * @author	Willian Ouchi
     * @email	willian.ouchi@meta.com.br
     * @return	
     */
    public function pesquisar() {

        $this->dao->nfadt_nota_ini = (isset($_POST['par_nfadt_nota_ini'])) ? $_POST['par_nfadt_nota_ini'] : "";
        $this->dao->nfadt_nota_fin = (isset($_POST['par_nfadt_nota_fin'])) ? $_POST['par_nfadt_nota_fin'] : "";
        $this->dao->tetoid = (isset($_POST['par_tetoid'])) ? $_POST['par_tetoid'] : "";
        $this->dao->nfaaprovado = (isset($_POST['par_nfaaprovado'])) ? $this->valorBanco($_POST['par_nfaaprovado']) : "";

        $resultado = array();
        $cont = 0;

        $rs = $this->dao->pesquisar();
        while ($rNF = pg_fetch_assoc($rs)) {

            $resultado['nfs'][$cont]['nfaoid'] = (isset($rNF['nfaoid'])) ? $rNF['nfaoid'] : '';
            $resultado['nfs'][$cont]['tetdescricao'] = (isset($rNF['tetdescricao'])) ? $rNF['tetdescricao'] : '';
            $resultado['nfs'][$cont]['nfadt_nota_inicial'] = (isset($rNF['nfadt_nota_inicial'])) ? $rNF['nfadt_nota_inicial'] : '';
            $resultado['nfs'][$cont]['nfadt_nota_final'] = (isset($rNF['nfadt_nota_final'])) ? $rNF['nfadt_nota_final'] : '';

            $resultado['nfs'][$cont]['nfadt_nota_periodo'] = (isset($rNF['nfadt_nota_inicial']) && isset($rNF['nfadt_nota_final'])) ? $rNF['nfadt_nota_inicial'] . " - " . $rNF['nfadt_nota_final'] : $rNF['nfadt_nota_inicial'] . $rNF['nfadt_nota_final'];

            $resultado['nfs'][$cont]['nfavalor_fixo'] = (isset($rNF['nfavalor_fixo'])) ? $this->valorTela($rNF['nfavalor_fixo']) : '';
            $resultado['nfs'][$cont]['nfavalor_unidade_recuperada'] = (isset($rNF['nfavalor_unidade_recuperada'])) ? $this->valorTela($rNF['nfavalor_unidade_recuperada']) : '';
            $resultado['nfs'][$cont]['nfaqtde_recuperada'] = (isset($rNF['nfaqtde_recuperada'])) ? $rNF['nfaqtde_recuperada'] : '';
            $resultado['nfs'][$cont]['nfatotal_recuperado'] = (isset($rNF['nfatotal_recuperado'])) ? $this->valorTela($rNF['nfatotal_recuperado']) : '';
            $resultado['nfs'][$cont]['nfavalor_unidade_nao_recuperado'] = (isset($rNF['nfavalor_unidade_nao_recuperado'])) ? $this->valorTela($rNF['nfavalor_unidade_nao_recuperado']) : '';
            $resultado['nfs'][$cont]['nfaqtde_nao_recuperada'] = (isset($rNF['nfaqtde_nao_recuperada'])) ? $rNF['nfaqtde_nao_recuperada'] : '';
            $resultado['nfs'][$cont]['nfatotal_nao_recuperado'] = (isset($rNF['nfatotal_nao_recuperado'])) ? $this->valorTela($rNF['nfatotal_nao_recuperado']) : '';
            $resultado['nfs'][$cont]['nfaqtde_acionamento_excedente'] = (isset($rNF['nfaqtde_acionamento_excedente'])) ? $rNF['nfaqtde_acionamento_excedente'] : '';
            $resultado['nfs'][$cont]['nfavalor_unidade_excedente'] = (isset($rNF['nfavalor_unidade_excedente'])) ? $this->valorTela($rNF['nfavalor_unidade_excedente']) : '';
            $resultado['nfs'][$cont]['nfavalor_total'] = (isset($rNF['nfavalor_total'])) ? $this->valorTela($rNF['nfavalor_total']) : '';
            $resultado['nfs'][$cont]['aprovado'] = (isset($rNF['aprovado'])) ? $rNF['aprovado'] : '';
            $cont++;
        }

        $resultado['total_registros'] = pg_num_rows($rs);

        echo json_encode($resultado);
        exit;
    }

    
/* ITENS DA NF */
       
/* ANEXOS */
    
    
    public function uploadAnexo() {
        
        $file_uploaded = $_FILES['arquivo'];
        $id_nf = $_POST['id_nf'];
        
        try {
            
            pg_query($this->conn, 'BEGIN');
            
            $id_arquivo = $this->dao->inserirAnexo($file_uploaded, $id_nf);

            if(!$id_arquivo) {                
                throw new Exception('Erro ao inserir o arquivo de anexo.');
            }
            
            if($file_uploaded['error']) {                
                throw new Exception('Houve um erro no upload da imagem.');
            }

            $dir = '/var/www/anexos_nf_atendimento';

            if(!is_dir($dir)) {
                if(!mkdir($dir, '0777')) {
                    throw new Exception('Houve um erro ao criar a pasta '. $dir);                    
                }
            }

            $destination = $dir . '/' . $id_nf;

            if(!is_dir($destination)) {
                if(!mkdir($destination, '0777')) {
                    throw new Exception('Houve um erro ao criar a pasta. '. $destination);                                                 
                }
                else{
                    if (!chmod($destination, 0755)){
                        throw new Exception('Houve um erro ao dar permissão na pasta. '. $destination);                                                 
                    }
                }                
            }

            $moved = move_uploaded_file($file_uploaded['tmp_name'], $destination. '/'. $file_uploaded['name']);

            if(!$moved) {
                throw new Exception('Houve um erro ao mover a imagem.');                
            }

            echo json_encode(
                    array(
                        'error'         => false,
                        'message'       => 'Arquivo anexado com sucesso.',
                        'id_arquivo'    => $id_arquivo,
                        'data_inclusao' => date('d/m/Y'),
                        'nome_arquivo'  => $file_uploaded['name'],
                        'usuario'       => utf8_encode($_SESSION['usuario']['nome'])
                    )
            );
            
            pg_query($this->conn, 'COMMIT');
        
        } catch(Exception $e) {
            
            pg_query($this->conn, 'ROLLBACK');
            
            echo json_encode(
                array(
                    'error'     => true,
                    'message'   => utf8_encode($e->getMessage())
                )             
            );
            
        }
        
        exit;
        
    }
    
    
    public function getAnexosAtendimento($id_nf) {
        
        $resultado = array();
        
        $rs = $this->dao->getAnexos($id_nf);
        $cont = 0;
        while ($anexo = pg_fetch_assoc($rs)){
            
            $resultado[$cont]['id_anexo'] = $anexo['id_anexo'];
            $resultado[$cont]['arquivo'] = $anexo['arquivo'];
            $resultado[$cont]['usuario'] = utf8_encode($anexo['usuario']);
            $resultado[$cont]['data'] = $anexo['data'];           
            $cont++;
        }
        
        //print_r($resultado);
        
        return $resultado;
    }
    
    
    public function excluirAnexo() {
        
        $id_anexo       = $_POST['id_anexo'];
        $id_nf          = $_POST['id_nf'];
        $nome_arquivo   = $_POST['nome_arquivo'];
        
        //var_dump($id_atendimento);
        
        pg_query($this->conn, 'BEGIN');
        
        $is_deleted = $this->dao->excluirAnexo($id_anexo);
        
        if($is_deleted) {
            
            $dir = '/var/www/anexos_nf_atendimento/';
            $is_deleted_file = true;
            
            if(file_exists($dir. $id_nf . '/' . $nome_arquivo)){
                $is_deleted_file = unlink($dir . $id_nf . '/' . $nome_arquivo);
            }
            
            if(!$is_deleted_file) {
                
                pg_query($this->conn, 'ROLLBACK');
                
                echo json_encode(
                    array(
                        'error'     => true,
                        'message'   => utf8_encode('Erro ao excluir o arquivo da pasta.')
                    )
                ); 
               
                exit;
               
            }
            
                echo json_encode(
                    array(
                        'error'     => false,
                        'message'   => utf8_encode('Arquivo excluído com sucesso.')
                    )
                );
            
            pg_query($this->conn, 'COMMIT');
            
        } else {
            
            pg_query($this->conn, 'ROLLBACK');
            
            echo json_encode(
                array(
                    'error'     => true,
                    'message'   => 'Erro ao excluir o arquivo.'
                )
            );
        }
        
        exit;
        
    }

}

?>