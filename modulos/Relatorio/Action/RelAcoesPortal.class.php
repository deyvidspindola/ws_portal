<?php

/** Dependência: classe de persistência */
require _MODULEDIR_."Relatorio/DAO/RelAcoesPortalDAO.class.php";
include _SITEDIR_."lib/Components/CsvWriter.php";

/**
 * RelAcoesPortal.class.php
 *
 * Classe desenvolvida para tratar as requisicoes do usuario ao utilizar o relatório
 *
 * @author Renato Teixeira Bueno
 * @copyright Copyright (c) 2012
 * @version 1.0
 * @package rel_acoes_portal
 * @date 28/06/2012T18:00:00
 */

class RelAcoesPortal
{
	private $acoes_portal_dao;
	private $num_result;
    private $caminho_tmp = '/var/www/docs_temporario/';
	
	public function RelAcoesPortal(){

		$this->acoes_portal_dao = new RelAcoesPortalDAO();
		
	}	
	
	/**
	 * Método default caso não haja nenhuma acao setada
	 */
	public function index(){
		
	}
	
	/**
	 * Método que efetua a pesquisa de acordo com os filtros selecionados
	 */
	public function pesquisar($post){

		ini_set('max_execution_time', 0);
		set_time_limit(0);

		$this->num_result = "";
		$ponto_virgula = ';';
						
		$tipo_relatorio = isset($post['tipo_relatorio']) ? $post['tipo_relatorio'] : '';
		$pesq_data_ini 	= isset($post['pesq_dt_ini']) 	 ? $post['pesq_dt_ini']    : '';
		$pesq_data_fim 	= isset($post['pesq_dt_fim'])  	 ? $post['pesq_dt_fim']    : '';
		$cliente_id     = isset($post['cliente_id'])     ? $post['cliente_id']     : ''; 
		$cliente_nome	= isset($post['cliente_nome']) 	 ? $post['cliente_nome']   : '';
		$regiao 		= isset($post['regiao']) 		 ? $post['regiao'] 		   : '';
		$cidade 		= isset($post['cidade']) 		 ? $post['cidade'] 		   : '';
		$tipo_contrato 	= isset($post['tipo_contrato'])  ? $post['tipo_contrato']  : ''; 
		$grupo_menu 	= isset($post['grupo_menu']) 	 ? $post['grupo_menu'] 	   : ''; 
		$item_menu 		= isset($post['item_menu']) 	 ? $post['item_menu'] 	   : '';
		$gerar_csv 		= isset($post['gerar_csv']) 	 ? $post['gerar_csv'] 	   : '';

		$nome_arquivo = "relatorio_{$tipo_relatorio}_acoes_portal_".date("Ymd").".csv";

		$msg = "<a href=\"download.php?arquivo={$this->caminho_tmp}{$nome_arquivo}\" target=\"_blank\">
                	<img src=\"images/icones/t3/caixa2.jpg\"><br>
					{$nome_arquivo}
            	</a>";
		
		try{
			
			//PESQUISA DADOS
			if(!empty($cliente_id)){
				$filtro_where .= " AND clinome = (SELECT clinome FROM clientes WHERE clioid = $cliente_id) ";
			}
			
			if(!empty($regiao) && $regiao != 'all'){
				$filtro_where .= "  AND enduf = (SELECT estuf FROM estado WHERE estoid = $regiao)";
			}
			
			if(!empty($cidade) && $cidade != 'all'){
				$filtro_where .= " AND endcidade = (SELECT ciddescricao FROM cidade WHERE cidoid = $cidade) ";
			}
			
			if(!empty($tipo_contrato) && $tipo_contrato != 'all'){
				switch($tipo_contrato){
					case "C":
						$filtro_where .= " AND
											tpcseguradora = false
									   AND
											tpcdescricao NOT ILIKE 'Ex-%' ";
						break;
					case "S":
						$filtro_where .= " AND
										tpcseguradora = true
									AND
										tpcdescricao NOT ILIKE 'Ex-%'  ";
						break;
					case "EX":
						$filtro_where .= " AND
										tpcseguradora = false
									AND
										tpcdescricao ILIKE 'Ex-%'  ";
						break;
					default:
						$filtro_where .= " AND tpcoid = $tipo_contrato ";
						break;
				}
			}
			
			if(!empty($grupo_menu) && $grupo_menu != 'all'){
				$filtro_where .= " AND mpadescricao = (SELECT mpadescricao FROM menus_portal_atendimento WHERE mpaoid = $grupo_menu) ";
			}
			
			if(!empty($item_menu) && $item_menu != 'all'){
				$filtro_where .= " AND impadescricao = (SELECT impadescricao FROM itens_menus_portal_atendimento WHERE impaoid = $item_menu) ";
			}
				
			
			//GERA O ARQUIVO
			if (file_exists($this->caminho_tmp) ){
				if(file_exists($this->caminho_tmp.$nome_arquivo)){
					unlink($this->caminho_tmp.$nome_arquivo);
				}
				
				// Gera CSV
				$csvWriter = new CsvWriter($this->caminho_tmp.$nome_arquivo, ';', '', true);				
				if($tipo_relatorio == 'S'){
					//Adiciona o cabeçalho ao arquivo
					$cabecalho = array(
							'Tipo Contrato',
							'Região',
							'Cidade',
							'Grupo Menu',
							'Item Acesso',
							'Quantidade'
					);
					$csvWriter->addLine( $cabecalho );
					$historicos = $this->acoes_portal_dao->pesquisaRelSintetico($filtro_where, $pesq_data_ini, $pesq_data_fim);
					
					if(!empty($historicos)){
						while($historico = pg_fetch_array($historicos)){
							$arrHistorico[$historico['item_acesso_id']][$historico['cliente_id']][] = array(
									'tipo_contrato' =>	$historico['tipo_contrato'],
									'regiao' =>			$historico['regiao'],
									'cidade' =>			$historico['cidade'],
									'grupo_menu' =>		$historico['grupo_menu'],
									'item_acesso' =>	$historico['item_acesso']
							);
						}
						 

						foreach($arrHistorico as $grupo_historico){
							foreach($grupo_historico as $grupo_cliente){
								$linhaCSV = array(
													$grupo_cliente[0]['tipo_contrato'],	
													$grupo_cliente[0]['regiao'],
													$grupo_cliente[0]['cidade'],
													$grupo_cliente[0]['grupo_menu'],
													$grupo_cliente[0]['item_acesso'],
													count($grupo_cliente)
										);

								$csvWriter->addLine( $linhaCSV );
							}
						}
					}else{
						$msg = $this->noResult();
					}					
				}
				
				
				if($tipo_relatorio == 'A'){
					//Adiciona o cabeçalho ao arquivo
					$cabecalho = array(
							'Tipo Contrato',
							'Região',
							'Cidade',
							'Cliente',
							'Data/Hora',
							'Grupo Menu',
							'Item Acesso',
							'Ação'
					);
					$csvWriter->addLine( $cabecalho );					
    				$historicos = $this->acoes_portal_dao->pesquisaRelAnalitico($filtro_where, $pesq_data_ini, $pesq_data_fim);
    				if(!empty($historicos)){
						while($historico = pg_fetch_array($historicos)){			
						$historico['acao'] = utf8_decode($historico['acao']);					
		    				$linhaCSV = array(
					    						$historico['tipo_contrato'],
					    						$historico['regiao'],
					    						$historico['cidade'],
					    						$historico['cliente'] ,
					    						$historico['data_hora'],
					    						$historico['grupo_menu'],
					    						$historico['item_acesso'],
					    						$historico['acao'] 
		    								);		    				
		    				$csvWriter->addLine( $linhaCSV );
						}
		    		}else{
						$msg = $this->noResult();
					}
				}
				
				//$csvWriter->closeFile();				
				$arquivo = file_exists($this->caminho_tmp.$nome_arquivo);
				if ($arquivo === false) {
					throw new Exception("O arquivo não pode ser gerado.");
				}
			} else {
				throw new Exception('Caminho do arquivo não existe.');
			}				
		} catch(Exception $e) {
			$msg = "Erro ao gerar o arquivo CSV: " . $e->getMessage() . "";
		}
	
		echo "<div style=\"text-align:center;\" class=\"conteudo centro\"><b>$msg</b></div>";
		exit;
		
	}
	
	/**
	 * Metodo que retorna a regiao
	 * @return string
	 */
	public function getRegiao(){
		
		$estados = $this->acoes_portal_dao->getRegiaoDAO();
		
		for($i = 0; $i < pg_num_rows($estados); $i++){
			
			$estado_id = pg_fetch_result($estados, $i, 'estoid');
			$estado_uf = pg_fetch_result($estados, $i, 'estuf');
			
			$regiao[$i]['id'] = $estado_id;
			$regiao[$i]['uf'] = $estado_uf;
			
		}
		
		return $regiao;
	}
	
	/**
	 * Metodo que retorna o grupo de menu
	 * @return string
	 */
	public function getGrupoMenu(){
		
		
		$menus = $this->acoes_portal_dao->getGrupoMenuDAO();
		
		if(!$menus){
			$this->setMessageException('ERRO: Erro ao buscar grupo de menu.');
		}
		
		for($i = 0; $i < pg_num_rows($menus); $i++){
			
			$menu_id = pg_fetch_result($menus, $i, 'mpaoid');
			$menu_descricao = pg_fetch_result($menus, $i, 'mpadescricao');
			
			$menu[$i]['menu_id'] = $menu_id;
			$menu[$i]['menu_descricao'] = $menu_descricao;
			
		}
		
		return $menu;
	}
	
	/**
	 * Metodo que retorna o tipo de contratos
	 * @return string
	 */
	public function getTipoContrato(){
		
		$tc = $this->acoes_portal_dao->getTipoContratoDAO();
		
		for($i = 0; $i < pg_num_rows($tc); $i++){
			
			$tc_id = pg_fetch_result($tc, $i, 'tpcoid');
			$tc_descricao = pg_fetch_result($tc, $i, 'tpcdescricao');
			
			$tipo_contrato[$i]['tc_id'] = $tc_id;
			$tipo_contrato[$i]['tc_descricao'] = $tc_descricao;
			
		}
		
		return $tipo_contrato;
	}
	
	/**
	 * Busca a cidade de acordo com a combo Regiao
	 */
	public function buscaCidade($regiao){
	    
	    $html = '';
	    
	    try{
	    	$regiao_id = $regiao['regiao'];
	    	
	    	if($regiao_id == 'all'){
	            
	            $html ='<table width="100%">
	                        <tr>
	                            <td>
	                                <select id="cidade" name="cidade" style="width:150px;">
	                                    <option value="all"> Todas </option>
	                                </select>
	                            </td>
	                        </tr>
	                    </table>';
	                    
	        }elseif(!empty($regiao_id)){
	 
	            $cidades = $this->acoes_portal_dao->getCidadeDAO($regiao_id);
	            
	            if(empty($cidades)){
	            	throw new Exception('Cidades não encontradas para o estado selecionado.');
	            }
	            
	           	    
                $html ='<table width="100%">
                            <tr>
                                <td>
                                    <select id="cidade" name="cidade" style="width:300px;">
                                    	<option value="" selected="selected">- Selecione -</option>';
                
                for($i = 0; $i < pg_num_rows($cidades); $i++){
                	
                	$cidade_id = pg_fetch_result($cidades, $i, 'cidoid');
					$cidade_desricao = pg_fetch_result($cidades, $i, 'ciddescricao');

                    $html .= '          <option value="'.$cidade_id.'">'.$cidade_desricao.'</option>';
                }
                
                $html .= '          </select>
                                </td>
                            </tr>
                        </table>';
	            
	        }else{
	            
	            $html ='<table width="100%">
	                        <tr>
	                            <td>
	                                <select id="cidade" name="cidade" style="width:300px;">
	                                    <option value="">- Selecione um Estado -</option>
	                                </select>
	                            </td>
	                        </tr>
	                    </table>';
	        }
	        
	        exit($html);
	        
	    }catch (Exception $e){
	    	exit('<b>'.$e->getMessage().'</b>');
	    }
	}
	
	/**
	 * Busca os Itens de Menu de acordo com a combo Grupo Menu
	 */
	public function buscaItemMenu($grupo_menu){
	    
	    $html = '';
	    
	    try{
	        
	        $grupo_menu_id = $grupo_menu['grupo_menu'];
	        
	        if($grupo_menu_id == 'all'){
	        	$html ='<table width="100%">
	                        <tr>
	                            <td>
	                                <select id="item_menu" name="item_menu" style="width:150px;">
	                                    <option value="all"> Todos </option>
	                                </select>
	                            </td>
	                        </tr>
	                    </table>';
	        	
	        }elseif((!empty($grupo_menu_id))){
	            
	            $item_menu = $this->acoes_portal_dao->getItemMenuDAO($grupo_menu_id);
	           	            
	            if(empty($item_menu)){
	                throw new exception('Não foi possível localizar itens de menu para o grupo de menu solicitado');
	            }
	            
	                
                $html ='<table width="100%">
                            <tr>
                                <td>
                                    <select id="item_menu" name="item_menu" style="width:300px;">
                						 <option value="" selected="selected">- Selecione -</option>';
                for($i = 0; $i < pg_num_rows($item_menu); $i++){
                	
                	$item_id = pg_fetch_result($item_menu, $i, 'impaoid');
					$item_desricao = pg_fetch_result($item_menu, $i, 'impadescricao');
					

                    $html .= '          <option value="'.$item_id.'">'.$item_desricao.'</option>';
                }
                
                $html .= '          </select>
                                </td>
                            </tr>
                        </table>';
	            
	        }else{
	            
	            $html ='<table width="100%">
	                        <tr>
	                            <td>
	                                <select id="item_menu" name="item_menu" style="width:300px;">
	                                    <option value="">- Selecione um Grupo Menu -</option>
	                                </select>
	                            </td>
	                        </tr>
	                    </table>';
	        }
	        
	        exit($html);
	        
	    }catch(exception $e){
	        exit('<b>'.$e->getMessage().'</b>');
	    }
	    
	}	
	
	/**
	 * Metodo que cria os options para a combo grupo de menu
	 * @param unknown_type $combo_grupo_menu
	 * @return string
	 */
	public function geraComboGrupoMenu($combo_grupo_menu){
		
		foreach($combo_grupo_menu as $menu){
		
        	$option .= '<option value="'.$menu['menu_id'].'">'.$menu['menu_descricao'].'</option>';
                         
        }
        
        return $option;
	}
	
	/**
	 * Metodo que gera os options para a combo regiao
	 * @param unknown_type $combo_regiao
	 * @return string
	 */
	public function geraComboRegiao($combo_regiao){
		
		foreach($combo_regiao as $regiao){
			
			$option .= '<option value="'.$regiao['id'].'">'.$regiao['uf'].'</option>';        
        }
        
        return $option;
	}
	
	
	/**
	 * Metodo que gera os option para a combo tipo contrato
	 * @param unknown_type $combo_tipo_contrato
	 * @return string
	 */
	public function geraComboTipoContrato($combo_tipo_contrato){
		
		foreach($combo_tipo_contrato as $tipo_contrato){
			
			$option .= '<option value="'.$tipo_contrato['tc_id'].'">'.$tipo_contrato['tc_descricao'].'</option>';
        }
        
        return $option;
	}
	
	/**
	 * Metodo que busca o cliente e retorna o select multiple
	 * @param unknown_type $cliente
	 */
	public function buscaCliente($cliente) {
    
	    $nome = trim($cliente['nome_cliente']);
	    
	    $rs = $this->acoes_portal_dao->getClienteDAO($nome);
	    
	    $retorno = array();
	    
	    if(!empty($rs)){
	         $retorno = '<select name="cliente_combo" id="cliente_combo" multiple="multiple">';
            while($linha = pg_fetch_object($rs)){
                                                         
               $retorno .= '<option value="'.$linha->cliente_id.'">'.$linha->cliente_nome.'</option>';
            
            }
            $retorno .= '</select>';
	    }
	    
	    $retorno = ($retorno != NULL) ? $retorno : '<b>Nenhum resultado encontrado para a pesquisa.</b>';
	    
	    exit($retorno);
	    
	}
	
	
	public function getRegiaoTeste(){
		
		$estados = $this->acoes_portal_dao->getRegiaoDAO();
		
		for($i = 0; $i < pg_num_rows($estados); $i++){
			
			$estado_id = pg_fetch_result($estados, $i, 'estoid');
			$estado_uf = pg_fetch_result($estados, $i, 'estuf');
			
			$regiao[$i]['id'] = $estado_id;
			$regiao[$i]['uf'] = $estado_uf;
			
		}
		
		
	}
	
	/**
	 * Metodo para gerar CSV - TESTE
	 */
	public function geraCSV($tipo_relatorio, $historicos){
 		
 		$ponto_virgula = ';';
 		 		
 		if($tipo_relatorio == 'S'){
 			
 			$arrColunas = array(
 					'Tipo Contrato',
 					'Região',
 					'Cidade',
 					'Grupo Menu',
 					'Item Acesso',
 					'Quantidade'
 			);
 			
 		}
 		
 		if($tipo_relatorio == 'A'){
 			
 			$arrColunas = array(
 					'Tipo Contrato',
 					'Região',
 					'Cidade',
 					'Cliente',
 					'Data/Hora',
 					'Grupo Menu',
 					'Item Acesso',
 					'Ação'
 			);
 			
 			foreach($historicos as $historico){
 				$html .=
 						$historico['tipo_contrato'].$ponto_virgula.
 						$historico['regiao'].$ponto_virgula.
						$historico['cidade'].$ponto_virgula.
						$historico['cliente'].$ponto_virgula.
						date('d/m/Y H:i:s', strtotime($historico['data_hora'])).$ponto_virgula.
						$historico['grupo_menu'].$ponto_virgula.
						$historico['item_acesso'].$ponto_virgula.
						utf8_decode($historico['acao']).$ponto_virgula."\\r\\n";
 			}
 		}
 		
 		/*
 		echo '<pre>'; var_dump($historicos); echo '</pre>';
 		exit;
 		*/
 		foreach($arrColunas as $colunas){
			$arrCol[] = $colunas;
			array_push($arrCol, $ponto_virgula);
		}
				
		$colunas 	= implode('', $arrCol);
		
		header('Content-type: text/csv'); 
		header('Content-disposition: attachment;filename=relatorio_acoes_portal.csv');
		
		echo $colunas;
		echo $html;
		exit;
		
		
		
		
 	}
 	
 	########################################
 	# Métodos para Layout
 	########################################
 	
 	/**
 	 * Método para mostrar o cabecalho do resultado da pesquisa de acordo com o tipo de relatorio passado
 	 * @param string $tipo
 	 * @return string
 	 */
 	private function headerResultRelatorio($tipo){
 	
 		if($tipo == 'S'){
 	
 			return '<center>
	    				<table class="tableMoldura">
					    	<tr class="tableSubTitulo">
					        	<td colspan="6"><h2>Resultado da Pesquisa</h2></td>
					        </tr>
					        <tr class="tableTituloColunas">
					            <td width="15%" align="center"><h3>Tipo Contrato</h3></td>
					            <td width="15%" align="center"><h3>Região</h3></td>
					            <td width="20%" align="center"><h3>Cidade</h3></td>
					            <td width="20%" align="center"><h3>Grupo Menu</h3></td>
					        	<td width="20%" align="center"><h3>Item Acesso</h3></td>
					            <td width="10%" align="center"><h3>Quantidade</h3></td>
					        </tr>';
 		}
 	
 		if($tipo == 'A'){
 	
 			return '<center>
						<table class="tableMoldura">
				        	<tr class="tableSubTitulo">
				            	<td colspan="8"><h2>Resultado da Pesquisa</h2></td>
				            </tr>
				            <tr class="tableTituloColunas">
				                <td width="10%" align="center"><h3>Tipo Contrato</h3></td>
				                <td width="5%" align="center"><h3>Região</h3></td>
				                <td width="10%" align="center"><h3>Cidade</h3></td>
				                <td width="20%" align="center"><h3>Cliente</h3></td>
				                <td width="10%" align="center"><h3>Data Hora</h3></td>
				                <td width="10%" align="center"><h3>Grupo Menu</h3></td>
				                <td width="15%" align="center"><h3>Item Acesso</h3></td>
				                <td width="20%" align="center"><h3>Ação</h3></td>
				            </tr>';
 		}
 	
 	}
 	
 	/**
 	 * Método para retornar o rodapé do resultado do relatório caso haja resultado
 	 * @return string
 	 */
 	private function footerResultRelatorio(){
 	
 		return '<tr class="tableRodapeModelo3">
	                <td colspan="8" align="center">A pesquisa retornou <b>'.$this->num_result.'</b> resultado(s)</td>
	            </tr>
	            <tr class="tableTituloColunas">
	                <td colspan="8" align="center">
	                	<input type="button" class="botao" name="gerar_csv" id="gerar_csv" value="Gerar CSV" style="width:90px;"/>
	                </td>
	            </tr>
	        </table>
 		</center>';
 	}

 	
 	/**
 	 * Metodo para retornar o html caso nao haja resultado para a pesquisa
 	 * @return string
 	 */
	private function noResult(){			
			return 'Nenhum resultado encontrado.';
	}
	
	
	public function Insere_HistoricoAcoesPortal($hapaimpaoid, $hapausuario_acao, $hapaacao, $hapatipo, $hapaclioid){

		$dadosHistorico['hapaimpaoid']		= $hapaimpaoid;
		$dadosHistorico['hapausuario_acao'] = $hapausuario_acao;
		$dadosHistorico['hapaacao']			= utf8_encode($hapaacao);
		$dadosHistorico['hapatipo']			= $hapatipo;
		$dadosHistorico['hapaclioid']		= $hapaclioid;
		
		$retorno = $this->acoes_portal_dao->Insere_HistoricoAcoesPortal($dadosHistorico);
		
		if(!$retorno){
			throw new Exception('0011');
		}
	}

} # End Class