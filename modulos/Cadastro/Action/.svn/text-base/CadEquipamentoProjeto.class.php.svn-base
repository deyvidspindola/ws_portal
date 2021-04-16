<?php
/**
* @author	Emanuel Pires Ferreira
* @email	epferreira@brq.com
* @since	11/01/2013
* */


require_once (_MODULEDIR_ . 'Cadastro/DAO/CadEquipamentoProjetoDAO.class.php');

/**
 * Trata requisições do módulo financeiro para efetuar pagamentos 
 * de títulos com forma de cobrança 'cartão de crédito' 
 */
class CadEquipamentoProjeto {
	
	/**
	 * Fornece acesso aos dados necessarios para o módulo
	 * @property CadEquipamentoProjetoDAO
	 */
	private $equipamentoProjetoDAO;
    
	/**
	 * Construtor, configura acesso a dados e parâmetros iniciais do módulo
	 */
    public function __construct() 
    {
		global $conn;
        
        $this->equipamentoProjetoDAO = new CadEquipamentoProjetoDAO($conn);
    }
    
    /**
     * Action de pesquisa dos equipamentos para teste
     */
    public function pesquisar()
    {
        return $this->equipamentoProjetoDAO->pesquisar();
    }
    
    public function novo() 
    {
        return $this->equipamentoProjetoDAO->listarGrupoTecnologia();
    }
    
    public function editar() 
    {
        $view = $this->equipamentoProjetoDAO->editar();
        $view['grupo_tecnologia'] = $this->equipamentoProjetoDAO->listarGrupoTecnologia();

        return $view;
    }
    
    public function buscaProdutos()
    {
        return $this->equipamentoProjetoDAO->buscaProdutos();
    

    }
    
    public function atualizaProduto()
    {
        return $this->equipamentoProjetoDAO->atualizaProduto();
    }
    
    public function salvar()
    {
        return $this->equipamentoProjetoDAO->salvar();
    }
    
    public function salvarNovoComando()
    {
        return $this->manutencaoEquipamentosDAO->salvarNovoComando();
    }
    
    public function salvarNovoAlertaPanico()
    {
        return $this->manutencaoEquipamentosDAO->salvarNovoAlertaPanico();
    }
    
    public function listaComandos()
    {
        return $this->manutencaoEquipamentosDAO->listaComandos();
    }
    
    public function listaTestes()
    {
        return $this->manutencaoEquipamentosDAO->listaTestes();
    }
    
    public function listaComandosCadastrados()
    {
        return $this->manutencaoEquipamentosDAO->listaComandosCadastrados();
    }
    
    public function listaAlertasPanico()
    {
        return $this->manutencaoEquipamentosDAO->listaAlertasPanico();
    }
    
    public function listaAlertasPanicoCadastrados()
    {
        return $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados();
    }
    
    public function excluiComando()
    {
        return $this->manutencaoEquipamentosDAO->excluiComando();
    }
    
    public function excluiAlertaPanico()
    {
        return $this->manutencaoEquipamentosDAO->excluiAlertaPanico();
    }
    
    public function verificaIntegridadeTeste()
    {
        return $this->manutencaoEquipamentosDAO->verificaIntegridadeTeste();
    }
    
    public function substituirTeste()
    {
        $epcvoid_ref = $_POST['epcvoid_ref'];
        $epcvoid     = $_POST['epcvoid'];
        
        //lista comandos cadastrados no teste existente
        $arrComandosCadastrados = $this->manutencaoEquipamentosDAO->listaComandosCadastrados(false);
        
        //apaga comandos existentes no teste
        foreach($arrComandosCadastrados['comandos'] as $comando) {
            $this->manutencaoEquipamentosDAO->excluiComando($comando['ecmtoid']);
        }
        
        //lista alertas cadastrados no teste existente
        $arrAlertasCadastrados = $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados(false);
        
        //apaga alertas existentes no teste
        foreach($arrAlertasCadastrados['alertas'] as $alerta) {
            $this->manutencaoEquipamentosDAO->excluiAlertaPanico($alerta['epntoid']);
        }
        
        //lista comandos existentes no teste de referência
        $arrComandosCadastradosRef = $this->manutencaoEquipamentosDAO->listaComandosCadastrados(false, $epcvoid_ref);
        
        //copia para o teste existente os comandos do teste de referência
        foreach($arrComandosCadastradosRef['comandos'] as $comando) {
            $this->manutencaoEquipamentosDAO->salvarNovoComando($epcvoid, $comando['cmdoid'], $comando['eptpoid']);
        }
        
        //lista alertas existentes no teste de referência
        $arrAlertasCadastradosRef = $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados(false, $epcvoid_ref);
                
        //copia para o teste existente os alertas do teste de referência3
        foreach($arrAlertasCadastradosRef['alertas'] as $alerta) {
            $this->manutencaoEquipamentosDAO->salvarNovoAlertaPanico($epcvoid, $alerta['pantoid'], $alerta['eptpoid']);
        }
        
        return "ok";
    }

    public function copiarTeste()
    {
        $epcvoid_ref = $_POST['epcvoid_ref'];
        $epcvoid     = $_POST['epcvoid'];
        
        //lista comandos existentes no teste de referência
        $arrComandosCadastradosRef = $this->manutencaoEquipamentosDAO->listaComandosCadastrados(false, $epcvoid_ref);
        
        //copia para o teste existente os comandos do teste de referência
        foreach($arrComandosCadastradosRef['comandos'] as $comando) {
            $this->manutencaoEquipamentosDAO->salvarNovoComando($epcvoid, $comando['cmdoid'], $comando['eptpoid']);
        }
        
        //lista alertas existentes no teste de referência
        $arrAlertasCadastradosRef = $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados(false, $epcvoid_ref);
                
        //copia para o teste existente os alertas do teste de referência3
        foreach($arrAlertasCadastradosRef['alertas'] as $alerta) {
            $this->manutencaoEquipamentosDAO->salvarNovoAlertaPanico($epcvoid, $alerta['pantoid'], $alerta['eptpoid']);
        }
        
        return "ok";
    }
    
    public function excluiTeste()
    {
    	$epcvoid     = $_POST['epcvoid'];
    	
    	//lista comandos existentes no teste
    	$arrComandosCadastrados = $this->manutencaoEquipamentosDAO->listaComandosCadastrados();
    	
    	//exclui todos os comandos do teste
    	foreach($arrComandosCadastradosRef['comandos'] as $comando) {
    		$this->manutencaoEquipamentosDAO->excluiComando($comando['ecmtoid']);
    	}
    	
    	//lista alertas existentes no teste
    	$arrAlertasCadastrados = $this->manutencaoEquipamentosDAO->listaAlertasPanicoCadastrados();
    	
    	//exclui todos os alertas do teste
    	foreach($arrAlertasCadastradosRef['alertas'] as $alerta) {
    		$this->manutencaoEquipamentosDAO->excluiAlertaPanico($alerta['epntoid']);
    	}
    	
    	return $this->manutencaoEquipamentosDAO->excluiTeste();
    }


    /**
    * Mantem cadastro, inclusão e  exclusão de grupo de tecnologia
    *
    * @return string
    */
    public function manterNovoGrupo(){

        $resultado  = ''; 
        $descricao  = isset($_POST['egtgrupo']) ? $_POST['egtgrupo'] : '' ;
        $descricao  = str_replace("'", '', $descricao);
        $descricao  = str_replace('\\', '', $descricao);
        $descricao  = strip_tags($descricao);
        $egtoid     = isset($_POST['egtoid']) ? $_POST['egtoid'] : '' ;
        $modo       = isset($_POST['modo']) ? $_POST['modo'] : '' ;
        $msgIncluir = "Erro ao cadastrar o grupo tecnologia";
        $smgAlterar = "Erro ao alterar o grupo tecnologia";
        $msgExcluir = "Erro ao excluir o grupo tecnologia";
        $smgDescricao = "Já existe um Grupo Tecnologia cadastrado com esta descrição";
        $msgErro    = "Ocorreu um erro ao processar o registro";

        try{

            if(empty($modo)) {
                return utf8_encode($msgErro);

            } else {                

                $this->equipamentoProjetoDAO->begin();

                switch ($modo) {
                    case 'incluir':

                        if(empty($descricao)){
                            return utf8_encode($msgIncluir);
                        }

                        $descricaoValida = $this->equipamentoProjetoDAO->verificarGrupoTecnologia($descricao);

                        if($descricaoValida) {
                            return utf8_encode($smgDescricao);
                        }

                        $resultado = $this->equipamentoProjetoDAO->incluirGrupoTecnologia($descricao);
                        break;

                    case 'alterar':

                        if(empty($descricao)){
                            return utf8_encode($smgAlterar);
                        }

                        if(empty($egtoid)){
                            return utf8_encode($smgAlterar);
                        }

                        $descricaoValida = $this->equipamentoProjetoDAO->verificarGrupoTecnologia($descricao);

                        if($descricaoValida) {
                            return utf8_encode($smgDescricao);
                        }

                        $resultado = $this->equipamentoProjetoDAO->alterarGrupoTecnologia($egtoid, $descricao);
                        break;

                    case 'excluir':

                        if(empty($egtoid)){
                            return utf8_encode($msgExcluir);
                        }

                        $resultado = $this->equipamentoProjetoDAO->excluirGrupoTecnologia($egtoid);
                        break;
                    
                    
                }

                $resultado = ($resultado) ? 'ok' : utf8_encode($msgErro);

                $this->equipamentoProjetoDAO->commit();    
               
                return $resultado;                           
            }

        } catch(Exception $e) {

            $this->equipamentoProjetoDAO->rollback();

            return utf8_encode($msgErro);
        }       

    }

    /**
    * Busca os grupos de tecnologias cadastrados
    *
    * @return JSON
    */
    public function listarGrupoTecnologiaAjax() {


        $dados = $this->equipamentoProjetoDAO->listarGrupoTecnologia();

        foreach ($dados as $valor) {
            
            $valor->egtgrupo = utf8_encode($valor->egtgrupo);

        }

        echo  json_encode($dados);

    }

}