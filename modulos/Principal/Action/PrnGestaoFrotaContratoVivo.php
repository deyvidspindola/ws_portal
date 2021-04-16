<?php

include_once _SITEDIR_ . 'lib/funcoes.php';
require_once _MODULEDIR_ . "Principal/DAO/PrnGestaoFrotaContratoVivoDAO.php";


/**
 * Classe responsável pelas regras de negócio da Gestão de Frota Contratos Vivo
 * @author andre.zilz <andre.zilz@meta.com.br>
 * @package Principal
 * @since 17/06/2013
 */
class PrnGestaoFrotaContratoVivo{
	/**
	 * DAO
	 * @var DAO
	 */
	protected $dao;
	
	/**
	 * Dados Históricos Contatos
	 * $dadosHistoricoContatos->historico 		(array:stdClass)
	 * $dadosHistoricoContatos->contatosEmerg 	(array:stdClass)
	 * $dadosHistoricoContatos->contatosAssist 	(array:stdClass)
	 * @var stdClass
	 */
	private $dadosHistoricoContatos;
    
    /**
	 * Dados Históricos Contatos
     * Apresenta erros/alertas na tela de Histórico	 
	 */
	private $msgErro;
    
	/**
	 * Método Construtor
	 */
	public function __construct() {
		global $conn;
		$this->dao = new PrnGestaoFrotaContratoVivoDAO($conn);
		
		$this->dadosHistoricoContatos 					= new stdClass();
		$this->dadosHistoricoContatos->historico 		= array();
		$this->dadosHistoricoContatos->contatosEmerg 	= array();
		$this->dadosHistoricoContatos->contatosAssist 	= array();
	}
	
	/**
	 * Método Index 
	 */
    public function index() {
        
        include_once _MODULEDIR_ . 'Principal/View/prn_gestao_frota_contrato_vivo/index.php';
        
    }
		
	
	/**
	 * Realiza a pesquisa dinamica de acordo com o filtro informado	
	 */
	public function buscarDinamicamente(){
		
		$filtro 	= isset($_GET['filtro']) ? $_GET['filtro'] : '';
		$parametro 	= isset($_GET['term']) ? trim($_GET['term']) : '';	
		
		try{
		
			if(($filtro == 'nome') || ($filtro == 'razao_social')) {
				
				$retorno = $this->dao->retornarPesquisaDinamicaNome($filtro, $parametro);				
			}
			else if(($filtro == 'cpf') || ($filtro == 'cnpj')){
				
				$retorno = $this->dao->retornarPesquisaDinamicaCpfCnpj($filtro, $parametro);				
			}
			else {
				
				$retorno = $this->dao->retornarPesquisaDinamicaIdVivo($filtro, $parametro);
			}			
			
						
			echo json_encode($retorno);
			
			
		}catch(Exception $e){
			
			//$msg = $e->getMessage();			
		}
		
	}
	
	/**
	 * Retornar os dados do cliente para preencher campos da tela	 
	 */
	public function pesquisarInformacoesCliente(){
		
		$clioid		= isset($_POST['clioid']) ? (int) $_POST['clioid'] : '';			
		
		$cliente = $this->dao->buscarDadosCliente($clioid);
        
        $contatos = $this->dao->pesquisarContatosCliente($clioid);
        
        $veiculos = $this->dao->pesquisarVeiculos($clioid);
        
        $ordensServico = $this->dao->pesquisarOrdemServico($clioid);
        
        echo json_encode(
                array(
                    'cliente' => $cliente,
                    'contatos' => $contatos,
                    'veiculos' => $veiculos, 
                    'ordensServico' => $ordensServico
                    )
                );

		
	}
    
    public function pesquisarVeiculos() {
        $clioid		    = isset($_GET['clioid']) ? (int) $_GET['clioid'] : '';
        $placa 		    = isset($_GET['placa']) ? trim($_GET['placa']) : '';
        $idvivo 	= isset($_GET['idvivo_veiculo']) ? trim($_GET['idvivo_veiculo']) : '';
        
        if (!empty($placa)) {
             
            $veiculos = $this->dao->pesquisarVeiculos($clioid, $placa, null);
            
        } elseif (!empty($idvivo)) {
            
            $veiculos = $this->dao->pesquisarVeiculos($clioid, null, $idvivo);
            
        } else {
            
            $veiculos = $this->dao->pesquisarVeiculos($clioid);
            
        }
                
        include_once _MODULEDIR_ . 'Principal/View/prn_gestao_frota_contrato_vivo/pesquisa_de_veiculos.php';
    }
	
	/**
	 * Realiza a busca dos veiculos com contrato vivo por placa
	 */
	public function pesquisaVeiculoPlaca(){
		
		$clioid 	= isset($_POST['clioid']) ? (int) $_POST['clioid'] : '';
		$placa 		= isset($_POST['placa']) ? trim($_POST['placa']) : '';
		
		try{
			
			if(empty($placa)) {
                $veiculos = $this->dao->pesquisarVeiculos($clioid);
            } else {
                $veiculos = $this->dao->pesquisarVeiculos($clioid, $placa);
            }

			if(!count($veiculos['resultados'])){
				throw new Exception(htmlspecialchars($placa) . " não consta no cadastro.");
			}
            
            echo json_encode($veiculos);
			
		}catch(Exception $e){
			echo json_encode(
                    array(
                        'error' => true,
                        'message' => utf8_encode($e->getMessage())
                    ));
                    
		}
		
	}
	
	/**
	 * Realiza a busca dos veiculos com contrato vivo por ID VIVO
	 */
	public function pesquisaVeiculoIdVivo(){
		
		$clioid 	= isset($_POST['clioid']) ? (int) $_POST['clioid'] : '';
		$idvivo 	= isset($_POST['idvivo_veiculo']) ? trim($_POST['idvivo_veiculo']) : '';
		
		try{
			
			if(empty($idvivo)) {
                $veiculos = $this->dao->pesquisarVeiculos($clioid);
            } else {
                $veiculos = $this->dao->pesquisarVeiculos($clioid, null, $idvivo);
            }

			if(!count($veiculos['resultados'])){
				throw new Exception(htmlspecialchars($idvivo) . " não consta no cadastro.");
			}
            
            echo json_encode($veiculos);
			
		}catch(Exception $e){
			echo json_encode(
                    array(
                        'error' => true,
                        'message' => utf8_encode($e->getMessage())
                    ));
                    
		}
		
	}
	
	/**
	 * Método que realiza a busca de todos os valores de 
	 * Histórico e Contatos de um determinado Contrato
	 */
	public function carregarDadosHistoricoContatos() {
		$connumero	=	isset($_GET['connumero']) ? (int) trim($_GET['connumero']) : 0;
		
		try {
			if (empty($connumero)) {
                throw new Exception('Número de contrato inválido');
            }
				
            $this->dadosHistoricoContatos->historico = $this->dao->buscarHistoricoContrato($connumero);			
            $this->dadosHistoricoContatos->contatosAssist = $this->dao->buscarContatosContrato($connumero);
            $this->dadosHistoricoContatos->contatosEmerg = $this->dao->buscarContatosContrato($connumero, 'E');                                

		} catch (Exception $e) {
			$this->msgErro = $e->getMessage();
		}
        
        include_once _MODULEDIR_ . 'Principal/View/prn_gestao_frota_contrato_vivo/historico_contatos.php';
        
	}
	
	
	/**
	 * Carrega as ordens de serviços do cliente relacionadas com contratos do tipo vivo por OS
	 * @throws Exception
	 */
	public function carregarOrdemServico(){
		
		$clioid 	= isset($_POST['clioid']) ? (int) $_POST['clioid'] : '';
		$numero_os 	= isset($_POST['numero_os']) ? trim($_POST['numero_os']) : '';
		
		try{
				
			if(empty($numero_os)) {
                $ordemServico = $this->dao->pesquisarOrdemServico($clioid);
            } else {
                $ordemServico = $this->dao->pesquisarOrdemServico($clioid, $numero_os);
            }
					
			if(!count($ordemServico['total_registros'])){
				throw new Exception(htmlspecialchars($numero_os) . " não consta no cadastro.");
			}
		
			echo json_encode($ordemServico);
				
		}catch(Exception $e){
			echo json_encode(
					array(
							'error' => true,
							'message' => utf8_encode($e->getMessage())
					));
		
		}
	}
    
	/**
	 * Carrega as ordens de serviços do cliente relacionadas com contratos do tipo vivo por ID VIVO
	 * @throws Exception
	 */
	public function carregarOrdemServicoIdVivo(){
		
		$clioid 	= isset($_POST['clioid']) ? (int) $_POST['clioid'] : '';
		$idvivo 	= isset($_POST['idvivo_os']) ? trim($_POST['idvivo_os']) : '';
		
		try{
				
            if(empty($idvivo)) {
                $ordemServico = $this->dao->pesquisarOrdemServico($clioid);
            } else {
                $ordemServico = $this->dao->pesquisarOrdemServico($clioid, 0, $idvivo);
            }
		    
			if(!count($ordemServico['total_registros'])){
				throw new Exception(htmlspecialchars($idvivo) . " não consta no cadastro.");
			}
		
			echo json_encode($ordemServico);
				
		}catch(Exception $e){
			echo json_encode(
					array(
							'error' => true,
							'message' => utf8_encode($e->getMessage())
					));
		
		}
	}
	
	public function pesquisarOrdemServico() {
        
        $clioid 	= isset($_GET['clioid']) ? (int) $_GET['clioid'] : '';
		$numero_os 	= isset($_GET['numero_os']) ? trim($_GET['numero_os']) : '';
		$idvivo 	= isset($_GET['idvivo_os']) ? trim($_GET['idvivo_os']) : '';
		
        if(!empty($numero_os)) {
            
            $ordensServico = $this->dao->pesquisarOrdemServico($clioid, $numero_os, null);
            
        } elseif (!empty($idvivo)) {
            
            $ordensServico = $this->dao->pesquisarOrdemServico($clioid, 0, $idvivo);
            
        } else {
            
            $ordensServico = $this->dao->pesquisarOrdemServico($clioid);
            
        }
        
        //ECHO '<pre>', var_dump($ordensServico);
        
        include_once _MODULEDIR_ . 'Principal/View/prn_gestao_frota_contrato_vivo/pesquisa_de_historico_os.php';
        
    }
    
    /**
     * Método que realiza a busca de todos os valores de
     * Histórico de uma determinada Ordem de Serviço
     */
    public function carregarDadosHistoricoOrdemServico() {
        
        $ordoid	=	isset($_GET['ordoid']) ? (int) trim($_GET['ordoid']) : 0;
    
        try {
            if (empty($ordoid)) {
                throw new Exception('Número de Ordem de Serviço inválido');
            }
    
            if (!$this->dadosHistoricoOrdemServico = $this->dao->buscarHistoricoOrdemServico($ordoid)) {
                throw new Exception('Nenhum registro encontrado.');
            }
    
        } catch (Exception $e) {
            $this->msgErro = $e->getMessage();
        }
    
        include_once _MODULEDIR_ . 'Principal/View/prn_gestao_frota_contrato_vivo/historico_ordem_servico.php';
    
    }
    
}