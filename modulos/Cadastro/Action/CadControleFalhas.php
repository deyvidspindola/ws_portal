<?php

/**
 * CadControleFalhas.php
 * 
 * -
 * 
 * @author Willian Ouchi <willian.ouchi@meta.com.br>
 * @package Cadastro
 * @since 08/02/2013
 * 
 */

require _MODULEDIR_ . 'Cadastro/DAO/CadControleFalhasDAO.php';

class CadControleFalhas {
    
    private $dao;
    private $conn;
	
    function __construct(){
        
        global $conn;
	$this->conn = $conn;
	$this->dao = new CadControleFalhasDAO($conn);	
    }

    
    
    public function index(){
        
        cabecalho();
                
        include(_MODULEDIR_ . 'Cadastro/View/cad_controle_falhas/index.php');
        
    }
    
    
    
    public function pesquisarHistoricoFalhas(){
        
        $numero_serie = ( isset($_POST['equno_serie']) ) ? $_POST['equno_serie'] : null;
        $this->msg = ( isset($_POST['msg_retorno']) ) ? $_POST['msg_retorno'] : $this->msg;
        cabecalho();
        
        try{
            
            $rsEquipamento = $this->dao->buscaEquipamento( $numero_serie );
            if ( $rsEquipamento["error"] == true ){
                throw new Exception($rsEquipamento["message"]);
            }
            
            $quantidade_equipamento = pg_num_rows($rsEquipamento["result"]);
            if ( $quantidade_equipamento == 0 ){
                throw new Exception('Esse número de serial não existe no sistema.');
            }
            
            $rsUltimaDataFalha = $this->dao->buscaUltimaDataFalha( $numero_serie );
            if ( $rsUltimaDataFalha["error"] == true ){
                throw new Exception($rsUltimaDataFalha["message"]);
            }
            $ultima_data_falha = pg_fetch_assoc($rsUltimaDataFalha["result"]);
            
            $rsHistoricoFalha = $this->dao->buscaHistoricoFalha( array("numero_serie" => $numero_serie) );
            if ( $rsHistoricoFalha["error"] == true ){
                throw new Exception($rsHistoricoFalha["message"]);
            }
            $quantidade_falhas = pg_num_rows($rsHistoricoFalha["result"]);
            $historicoFalhas = pg_fetch_all($rsHistoricoFalha["result"]);
            
            $rsFalhaReincidente = $this->dao->buscaDefeitoReincidente( $numero_serie );
            $quantidade_reincidencias = pg_num_rows($rsFalhaReincidente["result"]);
            
            if ( $quantidade_reincidencias > 0 ){
                $this->msg_reincidencia = "Reincidência desse equipamento maior que três vezes para o Defeito Lab.";
            }
            
            $rsStatusContrato = $this->dao->buscaStatusEquipamento( $numero_serie );
            if ( $rsStatusContrato["error"] == true ){
                throw new Exception($rsStatusContrato["message"]);
            }
            $statusContrato = pg_fetch_assoc($rsStatusContrato["result"]);
            
        }catch(Exception $e){    
            $this->msg = $e->getMessage();
        }

        include(_MODULEDIR_ . 'Cadastro/View/cad_controle_falhas/listagem_historico_falhas.php');      
    }

    
    public function carregarInsercaoFalhas(){
        
        $numero_serie = ( isset($_POST['equno_serie']) ) ? $_POST['equno_serie'] : null;
        
        cabecalho();
        
        try{
            
            $rsEquipamento = $this->dao->buscaEquipamento( $numero_serie );
            if ( $rsEquipamento["error"] == true ){
                throw new Exception($rsEquipamento["message"]);
            }
            
            $quantidade_equipamento = pg_num_rows($rsEquipamento["result"]);            
            if ( $quantidade_equipamento == 0 ){
                throw new Exception('Esse número de serial não existe no sistema.');
            }
            
            $rsInformacoesFalha = $this->dao->buscaInformacoesEquipamento( $numero_serie, $controle_falha_id );
            
            if ($rsInformacoesFalha["error"] == true){
                throw new Exception($rsInformacoesFalha["message"]);
            }
            
            $quantidade_falhas_equipamento = pg_num_rows($rsInformacoesFalha["result"]);
            $informacoesFalha = pg_fetch_assoc($rsInformacoesFalha["result"]);

            $rsAcoesLaboratorio = $this->dao->buscaAcaoLaboratorio(array("equno_serie" => $numero_serie));
            $acoesLaboratorio = pg_fetch_all($rsAcoesLaboratorio);

            
            $rsComponentesAfetados = $this->dao->buscaComponenteAfetado(array("equno_serie" => $numero_serie));
            $componentesAfetados = pg_fetch_all($rsComponentesAfetados);

            
            $rsDefeitosLaboratorio = $this->dao->buscaDefeitoLaboratorio(array("equno_serie" => $numero_serie));
            $defeitosLaboratorio = pg_fetch_all($rsDefeitosLaboratorio);

            $dadosLaboratorio = array(
                "acoesLaboratorio" => $acoesLaboratorio,
                "componentesAfetados" => $componentesAfetados,
                "defeitosLaboratorio" => $defeitosLaboratorio
            );           
            
            $rsStatusContrato = $this->dao->buscaStatusEquipamento( $numero_serie );
            if ( $rsStatusContrato["error"] == true ){
                throw new Exception($rsStatusContrato["message"]);
            }
            $statusContrato = pg_fetch_assoc($rsStatusContrato["result"]);
            
        }catch(Exception $e){                
            $this->msg = $e->getMessage();
        }
        
        include(_MODULEDIR_ . 'Cadastro/View/cad_controle_falhas/form_falhas.php');       
    }
    
    
    
    public function carregarEdicaoFallhas(){
     
        $controle_falha_id = ( isset($_GET['controle_falha_id']) ) ? $_GET['controle_falha_id'] : null;
        $numero_serie = (isset($_GET['numero_serie'])) ? $_GET['numero_serie'] : null;
                
        cabecalho();
        
        try{
            $controle_falha_id = ( isset($_GET['controle_falha_id']) ) ? $_GET['controle_falha_id'] : null;
            
            $rsEquipamento = $this->dao->buscaEquipamento( $numero_serie );
            if ( $rsEquipamento["error"] == true ){
                throw new Exception($rsEquipamento["message"]);
            }
            
            $quantidade_equipamento = pg_num_rows($rsEquipamento["result"]);            
            if ( $quantidade_equipamento == 0 ){
                throw new Exception('Esse número de serial não existe no sistema.');
            }
            
            $rsControleFalhas = $this->dao->buscaControleFalhas( $controle_falha_id );
            if ($rsControleFalhas["error"] == true){
                throw new Exception($rsControleFalhas["message"]);
            }
            
            $controle_falhas = pg_fetch_assoc( $rsControleFalhas["result"] );
            

            $rsInformacoesFalha = $this->dao->buscaInformacoesEquipamento( $numero_serie, $controle_falha_id );
            if ($rsInformacoesFalha["error"] == true){
                throw new Exception($rsInformacoesFalha["message"]);
            }
            
            $quantidade_falhas_equipamento = pg_num_rows($rsInformacoesFalha["result"]);            
            if ( $quantidade_falhas_equipamento == 0 ){
                throw new Exception('Esse número de serial não existe no sistema.');
            }
            $informacoesFalha = pg_fetch_assoc($rsInformacoesFalha["result"]);


            $rsAcoesLaboratorio = $this->dao->buscaAcaoLaboratorio(array("equno_serie" => $numero_serie));
            $acoesLaboratorio = pg_fetch_all($rsAcoesLaboratorio);

            
            $rsComponentesAfetados = $this->dao->buscaComponenteAfetado(array("equno_serie" => $numero_serie));
            $componentesAfetados = pg_fetch_all($rsComponentesAfetados);

            
            $rsDefeitosLaboratorio = $this->dao->buscaDefeitoLaboratorio(array("equno_serie" => $numero_serie));
            $defeitosLaboratorio = pg_fetch_all($rsDefeitosLaboratorio);

            $dadosLaboratorio = array(
                "acoesLaboratorio" => $acoesLaboratorio,
                "componentesAfetados" => $componentesAfetados,
                "defeitosLaboratorio" => $defeitosLaboratorio
            );
            
            $rsStatusContrato = $this->dao->buscaStatusEquipamento( $numero_serie );
            if ( $rsStatusContrato["error"] == true ){
                throw new Exception($rsStatusContrato["message"]);
            }
            $statusContrato = pg_fetch_assoc($rsStatusContrato["result"]);
            
        }catch(Exception $e){    
            
            $msg = $e->getMessage();
        }
        
        include(_MODULEDIR_ . 'Cadastro/View/cad_controle_falhas/form_falhas.php');
    }
    
    
    public function gravarFalhas(){

        $controle_falhas_id     = (isset($_POST['controle_falhas_id'])) ? $_POST['controle_falhas_id'] : null;
        $numero_serie           = (isset($_POST['numero_serie'])) ? $_POST['numero_serie'] : null;
        $modelo_equipamento_id  = (isset($_POST['modelo_equipamento_id'])) ? $_POST['modelo_equipamento_id'] : null;
        $data_entrada           = (isset($_POST['data_entrada'])) ? $_POST['data_entrada'] : null;
        $defeito_constatado_id  = (isset($_POST['defeito_constatado_id'])) ? $_POST['defeito_constatado_id'] : null;
        $acao_laboratorio_id    = (isset($_POST['acao_laboratorio_id'])) ? $_POST['acao_laboratorio_id'] : null;
        $componente_afetado_id  = (isset($_POST['componente_afetado_id'])) ? $_POST['componente_afetado_id'] : null;
        $numero_contrato        = (isset($_POST['numero_contrato'])) ? $_POST['numero_contrato'] : null;
        $item_ordem_servico     = (isset($_POST['item_ordem_servico'])) ? $_POST['item_ordem_servico'] : null;
        
        
        if ( empty($controle_falhas_id) ){

            $rsFalha = $this->dao->insereFalha(
                $numero_serie,
                $modelo_equipamento_id,
                $data_entrada,
                $defeito_constatado_id,
                $acao_laboratorio_id,
                $componente_afetado_id,
                $numero_contrato,
                $item_ordem_servico
            );

        }
        else{

            $rsFalha = $this->dao->editaFalha(
                $controle_falhas_id,              
                $defeito_constatado_id,
                $acao_laboratorio_id,
                $componente_afetado_id
            );

        }

       echo json_encode($rsFalha);
        
    }
    
    
    
    public function excluirHistoricoFalhas(){
        
        $controle_falhas_id = (isset($_POST['ctfoid'])) ? $_POST['ctfoid'] : null;
        $numero_serie = ( isset($_POST['equno_serie']) ) ? $_POST['equno_serie'] : null;
        
        try{
            
            if ( is_array($controle_falhas_id) ){

                foreach ( $controle_falhas_id as $ctfoid ){            
                    
                    $rsFalha = $this->dao->excluiHistoricoFalha( $ctfoid );
                    
                    if ($rsFalha["error"] == true){
                        throw new Exception('Erro ao excluir registro(s).'); 
                    }
                }
                
                $this->msg = "Registro excluído.";
                
            } 
            else{
                throw new Exception('Erro ao excluir registro(s).'); 
            }
        
        }
        catch(Exception $e){                
           $this->msg = $e->getMessage();           
        }
        
        $this->pesquisarHistoricoFalhas();          
    }
    
    
    public function alteraStatusEquipamento(){
        
        $numero_serie = ( isset($_POST['numero_serie']) ) ? $_POST['numero_serie'] : null;
        
        $rsEquipamento = $this->dao->buscaEquipamento( $numero_serie );
        $equipamento = pg_fetch_assoc( $rsEquipamento['result'] );
            
        $rsStatusEquipamento = $this->dao->alteraStatusEquipamento( $equipamento['equoid'], 24, $equipamento['equesn'], $equipamento['equeveoid'] );
        
        echo json_encode( $rsStatusEquipamento );
    }
    
    
}

?>
