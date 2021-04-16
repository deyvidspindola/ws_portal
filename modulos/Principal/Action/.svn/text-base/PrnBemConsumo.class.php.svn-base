<?php
/**
 * @file PrnBemConsumo.class.php
 *
 * Modulo para Bem de Consumo
 *
 * @author Rafael Barbeta da Silva
 * @version 28/08/2013
 * @package SASCAR PrnBemConsumo.class.php
 */

require 'modulos/Principal/DAO/PrnBemConsumoDAO.class.php';

class PrnBemConsumo {
    
    private $dao;
    
    
    public function __construct() {
    	$this->dao = new PrnBemConsumoDAO(); 
    }

	public function index($acao = 'index',$resultadoPesquisa = array()) {
		
		if($acao == "principal" || $acao == "pesquisar"){
			$this->representantes 		= $this->dao->getRepresentantes();
			$this->status				= $this->dao->getStatus();
		}
		
		$this->resultadoPesquisa 	= $resultadoPesquisa;
		
		$this->view($acao);
    }
    
    /**
     * Busca de bem de consumo
     */
    public function principal(){
    	$this->index('principal');
    }

    /**
     * Pesquisa os contratos pelos parametros numero do contrato ou/e codigo do cliente
     */
    public function pesquisar() {
    	// para manter os valores após a busca
    	$params = $this->populaValoresPost();
    
    	if(isset($params['repoid']) && $params['repoid']!=""){
    		$this->representanteEstoque = $this->dao->getRepresentanteEstoque($params);
    	}
    
    	$resultadoPesquisa = $this->dao->pesquisa($params);
    	$this->index('pesquisar', $resultadoPesquisa);
    }
    
    /**
     * Visualizar dados do produto
     */
    public function visualizar(){
    	if(isset($_GET['serial']) && trim($_GET['serial']) != ""){
    		$resultadoPesquisa = $this->dao->dadosDaCompra($_GET['serial']);
    	}
    
    	$this->index('visualizar', $resultadoPesquisa);
    }
    
    /**
     * Lista fornecedores
     */
    public function pesquisarFornecedor() {
    	if($mensagem != '')
    		$this->retorno = $mensagem;
    
    	// Pega valores do Post
    	$params = $this->populaValoresPost();
    
    	$fornecedores = $this->dao->getFornecedores($params['fornecedor_busca']);
    
    	// Retorna Json com os fornecedores encontrados
    	echo json_encode($fornecedores);
    	exit;
    
    }
    
    /**
     * Lista produto
     */
    public function pesquisarProduto() {
    	if($mensagem != '')
    		$this->retorno = $mensagem;
    
    	// Pega valores do Post
    	$params = $this->populaValoresPost();
    
    	$produtos = $this->dao->getProdutos($params);
    
    	// Retorna Json com os produtos encontrados
    	echo json_encode($produtos);
    	exit;
    
    }
    
    /**
     * Lista cliente
     */
    public function pesquisarCliente() {
    	if($mensagem != '')
    		$this->retorno = $mensagem;
    
    	// Pega valores do Post
    	$params = $this->populaValoresPost();
    
    	$clientes = $this->dao->getClientes($params);
    
    	// Retorna Json com os clientes encontrados
    	echo json_encode($clientes);
    	exit;
    
    }
    
    /**
     * Lista cliente
     */
    public function pesquisarOperacoes() {
    	if($mensagem != '')
    		$this->retorno = $mensagem;
    
    	// Pega valores do Post
    	$params = $this->populaValoresPost();
    
    	$operacoes = $this->dao->getOperacoes($params['clioid'][0]);
    
    	// Retorna Json com os clientes encontrados
    	echo json_encode($operacoes);
    	exit;
    
    }
    
    /**
     * Lista Representante Estoque
     */
    public function pesquisarRepresentanteEstoque(){
    	if($mensagem != '')
    		$this->retorno = $mensagem;
    
    	// Pega valores do Post
    	$params = $this->populaValoresPost();
    
    	$clientes = $this->dao->getRepresentanteEstoque($params);
    
    	// Retorna Json com os clientes encontrados
    	echo json_encode($clientes);
    	exit;
    }
    
	public function importar(){
		
		$linhas	  = array();
		$dados	  = $this->populaValoresPost();
		$tempName = $_FILES["importFile"]["tmp_name"];
		
		$arquivo = fopen($tempName,'r+');

		
		try{
			$this->dao->begin();

			if(!isset($dados['foroid'])){
				throw new Exception('Fornecedor não selecionado.');
			}
			
			// Percorre arquivo
			while (!feof($arquivo)){
				$fgets = fgets($arquivo, filesize($tempName));
				$explode  = explode(';',$fgets);
				$grupolinhas[$explode[0]][] = $explode;	// agrupa por modelo	
				
				// Validar numero de Colunas
				if(count($explode) != 5){
					throw new Exception('Número de colunas fora do padrão do arquivo.');
				}
			}
			array_shift($grupolinhas);
			
			// executa vinculo por modelo
			foreach ($grupolinhas as $prod => $linhas) {
    			$linha++;
			    $dados['prdproduto'] = trim($prod);
	    
    			// Confere se a nota existe para o fornecedor
    			$produto = $this->dao->confereNotaFornecedor($dados);
    			
    			if($produto == null){
    				throw new Exception('O equipamento "'.$dados['prdproduto'].'" não faz parte da nota selecionada. Linha: '.$linha);
    			}

    			// validar numero linhas
    			$equipamentos  = $this->dao->getEquipamentos($produto['entoid'], $produto['entiprdoid']);
    			$serialConsumo = $this->dao->getSerialConsumo($produto['entoid'], $produto['entiprdoid']);  
                $saldoNota = $this->dao->recuperarSaldoEquipamento($produto['entoid'], $produto['entiprdoid']);          

    			// Validar o Numero de linhas
    			$countLinhas = count($linhas);


                if(($countLinhas > count($equipamentos)) || 
                    ($countLinhas > count($serialConsumo)) ||
                    ($countLinhas >  $saldoNota)) {
                    throw new Exception("O número de registros do arquivo ultrapassa a quantidade de equipamentos.");
                }              
			
    			for($for = 0; $for < $countLinhas; $for++){
    
                    $qtdeCaracteres = str_split($linhas[$for][1]);

                    if(sizeof($qtdeCaracteres) >= 18){
                        throw new Exception("Serial '".$linhas[$for][1]."' deve possuir até 17 posições numéricas.");
                    }

                    // Verifica se o serial é numerico
                    if(!is_numeric($linhas[$for][1])) {
                        throw new Exception("Serial '".$linhas[$for][1]."' deve possuir somente números.");
                    }  

    				// Verifica se o serial já existe
    				$getSerial = $this->dao->confereSerial($linhas[$for][1]);
    				
    				if($getSerial){
    					throw new Exception("Serial '".$linhas[$for][1]."' já cadastrado para o equipamento '$getSerial'.");
    				}

    				$cseroid = $serialConsumo[$for]['cseroid'];
    
    				$numeroLinha = $this->dao->getlinhas($linhas[$for][2]);
    				$equoid 	 = $this->dao->getEquipamentosLayout($linhas[$for][0],$produto['entoid']);
    				$ddd		 = $this->dao->getDDD($numeroLinha['linaraoid']);

    				if(!$equoid){
    					throw new Exception("Produto não encontrado para o equipamento '".$linhas[$for][0]."'.");
    				}

    				if($numeroLinha == null){
    					throw new Exception("CCID não encontrado '".$linhas[$for][2]."'.");
    				}

					// Atualiza Rádio Frequência
					$this->dao->setRadioFrequencia($linhas[$for][4], $cseroid);

    				// Atualizar Série e IMEI(ESN) do equipamento a uma linha e chip(CCID)			
    				$this->dao->setEquipamento($numeroLinha, $equoid, $linhas[$for][1], $linhas[$for][3], $cseroid, $ddd, $linhas[$for][2],$dados['versao']);
    			}
			
			}

			$this->dao->commit();
			$this->retorno['status']   = 'sucesso';
			$this->retorno['mensagem'] = 'Importação realizada com sucesso!';
			$this->view();

		} catch (Exception $e) {
			$this->dao->rollback();
			$this->retorno['status']   = 'erro';
			$this->retorno['mensagem'] = $e->getMessage();
			$this->view();
		}
	}

	public function populaValoresPost($clearPost = false, $params = null) {
	
        if(!is_null($params)):
            $data = $params;
        else:
            $data = $_POST;
        endif;
        
        foreach($data as $key => $value):
            if($clearPost === false) {
                // TODO: alterar strtoupper para mb_strtoupper assim que estiver habilitado o mb_string
                $this->$key = (is_string($value))?strtoupper($value):$value;                
            } else
                unset($this->$key);
        endforeach;     

        return $data;       
    }	
    
    public function view($action='index', $layoutCompleto = true){

    	if($action == 'index'){
    		$this->versao = $this->dao->getVersao();
    	}
    	
    	if($layoutCompleto){
    		include _MODULEDIR_.'Principal/View/prn_bem_consumo/header.php';
			include _MODULEDIR_.'Principal/View/prn_bem_consumo/abas.php';
    	}
		
    	include _MODULEDIR_.'Principal/View/prn_bem_consumo/'.$action.'.php';
    
    	if($layoutCompleto){
    		include _MODULEDIR_.'Principal/View/prn_bem_consumo/footer.php';
		}
    }

	# Funções para chamadas AJAX 
	// Carrega lista de fornecedores
	public function getFornecedores(){
		
		$dados = $this->populaValoresPost();
		$getFor = $this->dao->getFornecedores($dados['fornecedor']);
		
		echo json_encode($getFor);
	}

}