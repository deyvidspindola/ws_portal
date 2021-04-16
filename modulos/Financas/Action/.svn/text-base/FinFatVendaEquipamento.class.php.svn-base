<?php

require 'modulos/Financas/DAO/FinFatVendaEquipamentoDAO.class.php';
require 'lib/Components/CampoBuscaX.class.php';

/*
 * @author	Ricardo Marangoni da Mota
 * @email	ricardo.mota@meta.com.br
 * @since	04/07/2012
 * */
class FinFatVendaEquipamento {
	
	private $dao;
	private $component_busca_cliente;
	
	public function FinFatVendaEquipamento() {
		
		global $conn;
		
		$this->dao = new FinFatVendaEquipamentoDAO($conn);
	}
	
	public function index() {
		
		$params = array(
					'id' 			=> 'cliente_id',
					'name'			=> 'cliente_nome',
					'btnFind'   	=> true,
                    'btnFindText'	=> 'Pesquisar Cliente',
                    'data'			=> array(
                                            'table'           => 'clientes',
                                            'fieldFindByText' => 'clinome',
                                            'fieldFindById'   => 'clioid',
                                            'fieldLabel'      => 'clinome',
                                            'fieldReturn'     => 'clioid'
                    					)   
				);
		
		/*
		 * Componente resposável por gerar o campo de pesquisa por cliente
		 * */
		$this->component_busca_cliente = new CampoBuscaX($params);	
		
	}
	
	/*
	 * @author	Ricardo Marangoni da Mota
 	 * @email	ricardo.mota@meta.com.br
 	 * @return	String json com os contratos retornados
	 * */
	public function pesquisar(){
		
		$contratos = $this->dao->getContratosFaturamento();
		
		echo json_encode($contratos);		
		exit;
	}
	
	/*
	 * @author	Ricardo Marangoni da Mota
	 * @email	ricardo.mota@meta.com.br
	 * @return	Mensagem de sucesso ou erro em formato JSON
	 * */
	public function gerarFaturamento() {
		try{
			
			/*
			 * Itens provenientes dos checkboxes marcados
			 * para faturamento.
			 * Cada vem em formato de string Json
			 * */
			$itens_faturamento = $_POST['itens_faturamento'];
			
			$itens_a_faturar = array();
						
			/*
			 * Se algum checkbox foi selecionado convertemos
			 * a string para objeto Json, para usarmos em seguida
			 * na camado de acesso à base de dados
			 * */
			if(isset($itens_faturamento)){
				foreach($itens_faturamento as $item_faturamento) {
					
					/*
					 * Decodificamos os dados postados através do value 
					 * do checkbox - $_POST['itens_faturamento']
					 * */
					$item = json_decode(utf8_encode(urldecode($item_faturamento)));

					/*
					 * Geramos uma array tendo como chave principal
					 * o id do cliente juntamento com os itens a faturar
					 * em formato de lista de objetos, assim temos o que 
					 * precisamos para gravar na base.
					 * */
					
					$cliente_id = $item->clioid;					
					$dia_vencimento  = $item->dia_vencimento;
					
					/*
					 * Removemos do objeto já que não precisamos mais
					 * */
					unset($item->clioid);
					unset($item->dia_vencimento);
					
					$itens_a_faturar[$cliente_id]['cliente_id'] 	 = $cliente_id;					
					$itens_a_faturar[$cliente_id]['dia_vencimento']  = $dia_vencimento;
					$itens_a_faturar[$cliente_id]['itens'][$item->numero_parcelas][] = $item;
					
				}	
				
				/*
				 * Com o array populado podemos realizar o faturamento
				 * */
				$this->dao->faturar($itens_a_faturar);
				
			}else{
				/*
				 * O usuário não selecionou nenhum checkbox
				 * devemos avisá-lo
				 * */				
				
				throw new Exception('Selecione os contratos que deseja faturar.');
				
			}			
		
		}catch(Exception $e) {
			
			$error = array(
					'error' 	=> true,
					'message' 	=> $e->getMessage()
			);
			
			echo json_encode($error);
		
		}
		
		exit;
		
	}	
	
	/*
	 * @author		Ricardo Marangoni da Mota
	 * @email		ricardo.mota@meta.com.br
	 * @description	Permite resgatar propriedades (varáveis) na template
	 * */
	public function __get($var){
		return $this->$var;
	}
	
}