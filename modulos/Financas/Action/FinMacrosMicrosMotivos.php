<?php

/**
 * Classe de persistência de dados
 */
require (_MODULEDIR_ . "Financas/DAO/FinMacrosMicrosMotivosDAO.php");

require 'lib/Components/ComponenteBuscaCliente.php';

/**
 * FinMacrosMicrosMotivos.php
 *
 * @author Felipe Pereira Augusto <felipe.augusto@meta.com.br>
 * @package Finanças
 * @since 08/05/2020
 *
*/
class FinMacrosMicrosMotivos {

	private $dao;

	private $comp_cliente_params = array(
			'id'  => 'cliente_id',
			'name'=> 'cliente_nome',
			'cpf' => 'cliente_cpf',
			'cnpj'=> 'cliente_cnpj',
			'tipo_pessoa' => 'tipo_pessoa',
			'btnFind' => true,
			'btnFindText' => 'Pesquisar',
			'data' => array(
					'table'                    	=> 'clientes',
					'where'						=> 'clidt_exclusao is null',
					'fieldFindByText'          	=> 'clinome',
					'fieldFindById'            	=> 'clioid',
					'fieldFindByCPF'           	=> 'clino_cpf',
					'fieldFindByTipoPessoa'    	=> 'clitipo',
					'fieldFindByCNPJ'          	=> 'clino_cgc',
					'fieldLabel'               	=> 'clinome',
					'fieldReturn'              	=> 'clioid',
					'fieldReturnCPF'           	=> 'clino_cpf',
					'fieldReturnCNPJ'          	=> 'clino_cgc'
			)
	);
	
	/*
	 * Construtor
	 */
	public function FinMacrosMicrosMotivos() {

		global $conn;

		$this->dao = new FinMacrosMicrosMotivosDAO($conn);
		
		//componente para pesquisa de clientes
		$this->comp_cliente = new ComponenteCliente($this->comp_cliente_params);
		
	}
	
	/**
	 * M�todo que efetua a pesquisa do relatorio
	 * @author Felipe Pereira Augusto <felipe.augusto@meta.com.br>
	 */
	public function pesquisar(){

		$descricao 					= (!empty($_POST['descricao'])) ? trim($_POST['descricao']) : '';

        $nivel 						= (!empty($_POST['nivel'])) ? implode(', ', $_POST['nivel']) : '';
        $nivel_original             = (!empty($_POST['nivel'])) ? $_POST['nivel'] : array();

        $filtro = array();
        $filtro_pesquisa = "";
        $filtro_pesquisa .= " \n";

        if($descricao != ''){
            $descricao = pg_escape_string(stripslashes($descricao));
            array_push($filtro, "AND pfmmotivo ILIKE '%$descricao%'");
            $filtro_pesquisa .= "Descrição: ".$descricao." \n";
        }

        if($nivel != ''){
            $nivel2= "";

            $nivel2 = str_replace("1, 2","Todos ",$nivel);

            $nivel2 = str_replace("1","Macro Motivo ",$nivel2);
            $nivel2 = str_replace("2","Micro Motivo ",$nivel2);

            $nivel3 = str_replace("MACRO","'".'MACRO'."'",$nivel2);

            array_push($filtro, "AND pfmtipo IN ($nivel)");

            $filtro_pesquisa .= "Nível: ".$nivel2." \n";
        }

		if(!empty($filtro)){

			array_push($filtro, ' AND pfmdata_exclusao IS NULL');

			$where = implode(' ', $filtro);

			$dadosPesquisa = $this->dao->pesquisar($where);

			if(is_array($dadosPesquisa)){
				return $dadosPesquisa;
			}

		}
		return false;

	}
	
	public function verificarCadastro(){
		
		$nivel 						= (!empty($_POST['nivel'])) ? $_POST['nivel'] : '';
		$contrato 					= (!empty($_POST['contrato'])) ? $_POST['contrato'] : '';
		$clioid 					= (!empty($_POST['cod_cliente'])) ? trim($_POST['cod_cliente']) : '';
		$tipo_contrato 				= (!empty($_POST['tipo_contrato']) && $_POST['tipo_contrato'] >= 0 && is_numeric($_POST['tipo_contrato']) || $_POST['tipo_contrato'] == 0) ? $_POST['tipo_contrato'] : '';
		
		$parametros = array(
				'nivel'                       => $nivel,
				'contrato' 		              => $contrato,
				'clioid'			          => $clioid,
				'tipo_contrato'		          => $tipo_contrato
		);
		
		//verifica se j� possui par�metro cadastrado para inser��o
		//$retorno = $this->dao->validarParametros($parametros);
		
		//echo json_encode(utf8_encode($retorno));
		exit;
	}

	
	/** ## Inclus�o e Edi��o ## **/
	
	
	/**
	 * Cadastrar novo par�metro
	 */
	public function novo() {
		$mensagemInformativa = "";
		
		try {
			unset($_POST);
			
		} catch (Exception $e) {
			$mensagemInformativa = $e->getMessage();
		}
		
		$_POST = array(
			'nivel'                   => null,
			'descricao'               => null
		);
		
		include 'modulos/Financas/View/fin_macros_micros_motivos/insere_edita_parametros_faturamento.php';
	}
	
	
	/**
	 * Salva o par�metro de acordo com o preenchimento do formul�rio
	 */
	public function salvar() {
			
		//VAR
		$nivel						    = isset($_POST['nivel']) ? $_POST['nivel'] : '';
		$descricao 				        = isset($_POST['descricao']) ? $_POST['descricao'] : '';
        $id 				            = isset($_POST['pfmoid']) ? $_POST['pfmoid'] : '';
        $pfmoid 					    = isset($_POST['pfmoid'])  && !empty($_POST['pfmoid']) ? $_POST['pfmoid'] : '';

		try {

			if (!empty($pfmoid)) {
				
				$parametroFaturamento  = $this->dao->getParametroMacroMicro($pfmoid);
				
				if ($parametroFaturamento == null) {
					throw new Exception("Erro ao recuperar os dados do motivo.");
				}
			}
			
			
			// Recupera o n�vel selecionado se caso edi��o
			if ((isset($_POST['nivel']) && !empty($_POST['nivel'])) || $_POST['nivel'] === '0') {
				
				$nivel = $_POST['nivel'];
				
			} elseif ($parametroFaturamento != null) {
			
				$nivel = $parametroFaturamento->parfnivel;
				
				$_POST['nivel'] = $nivel;
				
			} else {
				throw new Exception("Nível não informado.");
			}

            if(empty($descricao)) {
                throw new Exception("Descricao deve ser preenchida.");
            }

            //se tiver vazio � inser��o, ent�o, verifica se j� tem cadastro de par�metro
            if(empty($pfmoid)){

                    $parametros = array(
                            'nivel'	    => $nivel,
                            'descricao' => $descricao,
                            'id'        => $id
                    );

                    //verifica se já possui par�metro cadastrado para inser��o
                    $retorno = $this->dao->validarParametros($parametros);

                    $retorno = pg_fetch_all($retorno);

                    //organiza todos as obrigacoes financeiras do (contrato,cliente ou tipo) em um array
                    if($retorno !== false) {
                        foreach ($retorno as $item) {
                            $arrayObrigacoes = explode(',', str_replace("{", "", str_replace("}", "", $item['parfobroid_multiplo'])));

                            foreach ($arrayObrigacoes as $obrigacao) {
                                array_push($obrigacoesContrato, $obrigacao);
                            }
                        }
                    }
            }

            $dados_param = array(   'pfmoid'			            => $id,
                                    'nivel'					        => $nivel,
                                    'descricao'				        => $descricao
                            );
            if(empty($pfmoid)){
                 $this->dao->insereParametro($dados_param);
                 $msg_ope = 'incluido(s)';
                 $mensagemInformativa['msg']    = "Motivo $msg_ope com sucesso.";
                 $mensagemInformativa['status'] = "OK";
		      
		        //limpa dados do post para o usu�rio inserir novos dados
		        unset($_POST);
            }else{
		     	 $this->dao->atualizaParametro($dados_param);

                $msg_ope = 'alterado';
                $mensagemInformativa['msg']    = "Motivo $msg_ope com sucesso.";
                $mensagemInformativa['status'] = "OK";

            }
                        
		} catch (Exception $e) {
			$mensagemInformativa['msg'] = $e->getMessage();	
			$mensagemInformativa['status'] = "ERRO";
		}
		
		include 'modulos/Financas/View/fin_macros_micros_motivos/insere_edita_parametros_faturamento.php';
	}
	
	/**
	 * Popula o post com os dados para edição
	 */
	public function editar() {
	
		//VAR
		$pfmoid 					= null;
		$nivel						= null;
		$descricao					= null;

		try {
			if (!isset($_POST['pfmoid']) || empty($_POST['pfmoid']) || !is_numeric($_POST['pfmoid']) || $_POST['pfmoid'] <= 0) {
				throw new Exception("Motivo não informado ou inválido.");
			}

            $pfmoid          = $_POST['pfmoid'];

			unset($_POST);

			$_POST['pfmoid'] = $pfmoid;

			$parametroFaturamento	= $this->dao->getParametroMacroMicro($pfmoid);

			if ($parametroFaturamento == null) {
				throw new Exception("Motivo para faturamento não encontrado.");
			}

			$_POST['nivel']    				= $parametroFaturamento->pfmtipo;
			$_POST['descricao'] 		    = $parametroFaturamento->pfmmotivo;

		} catch (Exception $e) {
			$mensagemInformativa = $e->getMessage();
		}
		
		include 'modulos/Financas/View/fin_macros_micros_motivos/insere_edita_parametros_faturamento.php';
	}
	
	
	/**
	 * Exclui o par�metro em edi��o
	 * @throws Exception
	 */
	public function excluir() {
		
		$pfmoid = null;
		
		try {
			
			if (!isset($_POST['pfmoid']) || empty($_POST['pfmoid'])) {
				throw new Exception("Par�metro n�o informado.");
			}

            $pfmoid = $_POST['pfmoid'];

			$exclusao = $this->dao->excluirParametro($pfmoid);

			if (!$exclusao) {
				throw new Exception("Erro ao excluir registro.");
			}

			$mensagemInformativa['msg']    = "Motivo excluido com sucesso.";
			$mensagemInformativa['status'] = "OK";
			
			unset($_POST);
			
			
		} catch (Exception $e) {
			$mensagemInformativa['msg'] = $e->getMessage();
				$mensagemInformativa['status'] = "ERRO";
		}
		
		include 'modulos/Financas/View/fin_macros_micros_motivos/insere_edita_parametros_faturamento.php';
	}
	
	
	/**
	 * STI 84969
	 * Retorna tipo de par�metro e verifica o tipo retornado para aplicar a regra
	 * Recede id do par�metro do faturamento via POST ou par�metro da fun��o
	 *
	 * @param string $id_param
	 * @throws Exception
	 * @return boolean
	 */

}