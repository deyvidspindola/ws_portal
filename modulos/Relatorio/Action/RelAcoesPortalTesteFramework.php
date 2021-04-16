<?php 
/**
 * Classe Action para controle logico do relatorio de Acoes do Portal
 */

include(__LIBPATH__.'/Components/CampoBuscaX.class.php');

class RelAcoesPortalTesteFramework extends UI{
		
	public function __construct(){
		
		/**
		 * Cria um objeto DAO para acesso ao banco de dados
		 */
		$this->dao = new RelAcoesPortalTesteFrameworkDAO();
				
	}	
	
	/**
	 * Método default caso não haja nenhuma acao setada
	 */
	public function index(){	
		
		/**
		 * Preenche a combo "Região"
		 */
		$this->view->gera_combo_regiao = $this->geraComboRegiao($this->getRegiao());
		
		/**
		 * Preenche a combo "Tipo Contrato"
		 */
		$this->view->gera_combo_tipo_contrato = $this->geraComboTipoContrato($this->getTipoContrato());
		 
		/**
		 * Preenche a combo "Grupo Menu"
		 */
		$this->view->gera_combo_grupo_menu = $this->geraComboGrupoMenu($this->getGrupoMenu());
		
		$component = new CampoBuscaX(array(
					  	'id'          => 'clioid',
					    'name'        => 'teste',
					    'btnFind'     => true,
					    'btnFindText' => 'Pesquisar Cliente',
					    'data'        => array(
									        'table'           => 'clientes',
									        'fieldFindByText' => 'clinome',
									        'fieldFindById'   => 'clioid',
									        'fieldLabel'      => 'clinome',
									        'fieldReturn'     => 'clioid'   
									    )
					));
		
		#$this->view->busca_cliente = $component->render();		
		
		$this->view->load('index');
	}
	
	/**
	 * Método que efetua a pesquisa do relatorio
	 */
	public function pesquisar(Request $request){
		
		$tipo_relatorio	= $request->post->tipo_relatorio; 
		$pesq_data_ini 	= $request->post->pesq_dt_ini;
		$pesq_data_fim 	= $request->post->pesq_dt_fim;
		$clioid     	= $request->post->cliente_id; 
		$clinome 		= $request->post->cliente_nome;
		$regiao 		= $request->post->regiao;
		$cidade 		= $request->post->cidade;
		$tipo_contrato 	= $request->post->tipo_contrato; 
		$grupo_menu 	= $request->post->grupo_menu; 
		$item_menu 		= $request->post->item_menu;
		
		if($tipo_relatorio == 'S'){

			
			#$resultado = 0;
			#$this->view->resultado = $resultado;
			
			$arrResult = array(
						'SulAmerica',
						'PR',
						'Curitiba',
						'Portal',
						'Menu 1',
						'36'
					);
			
			$class = 'tdc';
			
			foreach($arrResult as $result){
				
				$class = ($class=='tde') ? 'tdc' : 'tde';
				$f     = ($class=='tde') ? 'f' : '';
				
				$this->view->class= $class;
			
				$this->view->resultado_sintetico .= "<td>$result</td>";
			}
			
			$this->view->load('resultado_sintetico', false);
			
		}
		
		if($tipo_relatorio == 'A'){
			
			#$resultado = 0;
			#$this->view->resultado = $resultado;
			
			$arrResult = array(
					'Bradesco',
					'SC',
					'Florianopolis',
					'Ricardo Marangoni',
					'25/03/2012 18:28',
					'Portal2',
					'Menu 1',
					'Inserir'
			);
        
            $class = 'tdc';
            
            foreach($arrResult as $result){
                
            	$class = ($class=='tde') ? 'tdc' : 'tde';
                $f     = ($class=='tde') ? 'f' : '';
                
                $this->view->class= $class;

                $this->view->resultado_analitico .= "<td>".$result."</td>";
            }
                        
         	$this->view->load('resultado_analitico', false);   
		}
	}
	
	/**
	 * Metodo que efetua a busca das regiões para preencher a combo do filtro
	 */
	public function getRegiao(){
		
		$estados = $this->dao->getRegiaoDAO();
		
		for($i = 0; $i < count($estados); $i++){
			
			$estado_id = $estados[$i]['estoid'];
			$estado_uf = $estados[$i]['estuf'];
			
			$regiao[$i]['id'] = $estado_id;
			$regiao[$i]['uf'] = $estado_uf;
			
		}
		
		return $regiao;
	}
	
	/**
	 * Metodo que efetua a busca dos grupos de menus do portal para preencher a combo do filtro
	 */
	public function getGrupoMenu(){
		
		
		$menus = $this->dao->getGrupoMenuDAO();
				
		for($i = 0; $i < count($menus); $i++){
			
			$menu_id = $menus[$i]['mpaoid'];
			$menu_descricao = $menus[$i]['mpadescricao'];
			
			$menu[$i]['menu_id'] = $menu_id;
			$menu[$i]['menu_descricao'] = $menu_descricao;
			
		}
		
		return $menu;
	}
	
	/**
	 * Metodo que efetua a busca dos tipos de contratoss para preencher a combo do filtro
	 */
	public function getTipoContrato(){
		
		$tc = $this->dao->getTipoContratoDAO();
		
		for($i = 0; $i < count($tc); $i++){
			
			$tc_id = $tc[$i]['tpcoid'];
			$tc_descricao = $tc[$i]['tpcdescricao'];
			
			$tipo_contrato[$i]['tc_id'] = $tc_id;
			$tipo_contrato[$i]['tc_descricao'] = $tc_descricao;
			
		}
		
		return $tipo_contrato;
	}
	
	/**
	 * Metodo que efetua a busca das cidades de acordo com a combo Regiao
	 */
	public function buscaCidade(Request $request){
		    
		try{
			
		    $regiao_id = $request->post->regiao;
		    $this->view->regiao_id = $regiao_id;
		    
		    if(!empty($regiao_id) && intval($regiao_id) > 0){
		    		
	    		$cidades = $this->dao->getCidadeDAO($regiao_id);
	    		
	    		if(empty($cidades)){
		    		throw new Exception('Cidades não localizadas para a região selecionada.');
		    	}
		    
				for($i = 0; $i < count($cidades); $i++){
		                	
		        	$cidade_id = $cidades[$i]['cidoid'];
					$cidade_desricao = $cidades[$i]['ciddescricao'];
		
		            $this->view->options_cidade .= '<option value="'.$cidade_id.'">'.$cidade_desricao.'</option>';
		        }
		    }
		    
	        $this->view->load('ajax_combo_cidade', false);
	        
		} catch (Exception $e){
			echo '<b>'.$e->getMessage().'</b>';
		}
	}
	
	/**
	 * Metodo que efetua a busca dos Itens de Menu de acordo com a combo Grupo Menu
	 */
	public function buscaItemMenu(Request $request){
	    
	    $grupo_menu_id = $request->post->grupo_menu;
	    
	    try{
	    	
	    	if(!empty($grupo_menu_id) && intval($grupo_menu_id) > 0)
		    {
	            $item_menu = $this->dao->getItemMenuDAO($grupo_menu_id);
	           	            
	            if(empty($item_menu)){
	                throw new exception('Não foi possível localizar itens de menu para o grupo de menu solicitado');
	            }
	            
	            for($i = 0; $i < count($item_menu); $i++){
					$this->view->option_item_menu .= '<option value="'.$item_menu[$i]['impaoid'].'">'.$item_menu[$i]['impadescricao'].'</option>';
	            }
	            
	            $this->view->item_menu 	= $item_menu;	            
		    }
		    
            $this->view->grupo_menu_id 	= $grupo_menu_id;
            $this->view->load('ajax_combo_item_menu', false);
            
	    }catch(exception $e){
	        echo $e->getMessage();
	    }
	    
	}	
	
	/**
	 * Metodo que gera as options para a combo do Grupo de Menu
	 */
	public function geraComboGrupoMenu($combo_grupo_menu){
		
		foreach($combo_grupo_menu as $menu){
		
        	$option .= '<option value="'.$menu['menu_id'].'">'.$menu['menu_descricao'].'</option>';
                         
        }
        
        return $option;
	}
	
	/**
	 * Metodo que gera as options para a combo Regiao
	 */
	public function geraComboRegiao($combo_regiao){
		
		foreach($combo_regiao as $regiao){
			
			$option .= '<option value="'.$regiao['id'].'">'.$regiao['uf'].'</option>';        
        }
        
        return $option;
	}
	
	/**
	 * Metodo que gera as options para a combo Tipo de Contrato
	 */
	public function geraComboTipoContrato($combo_tipo_contrato){
		
		foreach($combo_tipo_contrato as $tipo_contrato){
			
			$option .= '<option value="'.$tipo_contrato['tc_id'].'">'.$tipo_contrato['tc_descricao'].'</option>';
        }
        
        return $option;
	}
	
	/**
	 * Metodo que efetua a busca do cliente e mostra a view com a selection multiple
	 */
	public function buscaCliente(Request $request) {
    
	    $nome = trim($request->post->nome_cliente);
	    
	    $clientes = $this->dao->getClienteDAO($nome);
	    
	     if(empty($clientes)){
            throw new exception('Não foi possível localizar o cliente solicitado.');
        }
	    	    
	    if(!empty($clientes)){
	    	$this->view->clientes = $clientes;
	        $this->view->load('ajax_busca_cliente', false);
	    }else{
	    	echo '<b>Nenhum resultado encontrado para a pesquisa.</b>'; 
	    }	    
	}
	
	/**
	 * Metodo teste
	 */
	public function getRegiaoTeste(){
		
		$estados = $this->dao->getRegiaoDAO();
		
		for($i = 0; $i < pg_num_rows($estados); $i++){
			
			$estado_id = pg_fetch_result($estados, $i, 'estoid');
			$estado_uf = pg_fetch_result($estados, $i, 'estuf');
			
			$regiao[$i]['id'] = $estado_id;
			$regiao[$i]['uf'] = $estado_uf;
			
		}
		
		$arrColunas = array('Id', 
							'Região'
							);
		
		$this->geraCSV($arrColunas, $regiao);
		
	}
	
	/**
	 * Metodo para gerar CSV - TESTE
	 */
	public function geraCSV(Request $request){
 		
 		$ponto_virgula = ';';
 		
 		$tipo_relatorio = $request->post->tipo_relatorio;
 		 		
 		if($tipo_relatorio == 'S'){
 			
 			$arrColunas = array(
 					'Tipo Contrato',
 					'Regiao',
 					'Cidade',
 					'Grupo Menu',
 					'Item Acesso',
 					'Quantidade'
 			);
 			
 			$resultado = array(
 					'SulAmerica',
 					'PR',
 					'Curitiba',
 					'Portal',
 					'Menu 1',
 					'36'
 			);
 			
 		}
 		
 		if($tipo_relatorio == 'A'){
 			
 			$arrColunas = array(
 					'Tipo Contrato',
 					'Regiao',
 					'Cidade',
 					'Cliente',
 					'Data Hora',
 					'Grupo Menu',
 					'Item Acesso',
 					'Ação'
 			);
 			
 			$resultado = array(
 					'Bradesco',
 					'SC',
 					'Florianopolis',
 					'Ricardo Marangoni',
 					'25/03/2012 18:28',
 					'Portal2',
 					'Menu 1',
 					'Inserir'
 					);
 		}
 		
 		
 		foreach($arrColunas as $colunas){
			$arrCol[] = $colunas;
			array_push($arrCol, $ponto_virgula);
		}
		
		foreach($resultado as $r){
			$arrRes[] = $r;
			array_push($arrRes, $ponto_virgula);
		}
		
		$colunas 	= implode('', $arrCol);
		$resultado 	= implode('', $arrRes);

		echo $colunas;
		echo '<br />';
		echo $resultado;exit;
		
 	}
 	
} # End Class