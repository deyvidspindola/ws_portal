<?php
/**
 * INPC
 * 
 * @author Kleber Goto Kihara
 * @package Cadastro
 * @since 18/07/2013 10:35
 */

require_once _MODULEDIR_.'Cadastro/DAO/CadInpcDAO.php';

class CadInpc {
	
	/**
	 * Objeto DAO
	 * 
	 * @var object
	 */
	private $dao;
	
	/**
	 * Método construtor
	 * 
	 * @return boolean
	 */
	public function __construct() {
		$this->dao = new CadInpcDAO();
		
		return true;
	}
	
	/**
	 * Método da view principal
	 * 
	 * @return boolean
	 */
	public function index() {
		$pesquisa = new stdClass();
		
		if(isset($_GET['msg'])) {
			$mensagem = new stdClass();
			
			if($_GET['msg'] == 'excluir') {
				$mensagem->classe = 'sucesso';
				$mensagem->texto  = 'O registro foi excluído com sucesso.';
			}
		}
		
		if(isset($_GET['sessao']) && isset($_SESSION['sessao_cad_inpc'])) {
			$pesquisa->inpdt_inicial = $_SESSION['sessao_cad_inpc']->inpdt_inicial;
			$pesquisa->inpdt_final   = $_SESSION['sessao_cad_inpc']->inpdt_final;
		} else {
			$pesquisa->inpdt_inicial = '';
			$pesquisa->inpdt_final   = '';
			
			$_SESSION['sessao_cad_inpc'] = $pesquisa;
		}
		
		require_once _MODULEDIR_.'Cadastro/View/cad_inpc/listagem.php';
		
		return true;
	}
	
	/**
	 * Método da view de edição
	 * 
	 * @return boolean
	 */
	public function alterar() {
		$registro = new stdClass();
		$registro->inpdt_referencia = empty($_GET['data']) ? '' : '01/'.str_replace('-', '/', $_GET['data']);
		
		if($registro->inpdt_referencia) {
			$registro = $this->dao->pesquisar($registro);
			
			if(count($registro)) {
				$registro = $registro[0];
				
				$registro->inpdt_referencia = $registro->data;
				$registro->inpvl_referencia = number_format($registro->valor, 1, ',', '');
				
				require_once _MODULEDIR_.'Cadastro/View/cad_inpc/formulario.php';
			} else {
				$this->cadastrar();
			}
		} else {
			$this->cadastrar();
		}
		
		return true;
	}
	
	/**
	 * Método da view de cadastro
	 * 
	 * @return boolean
	 */
	public function cadastrar() {
		$registro = new stdClass();
		$registro->inpdt_referencia = '';
		$registro->inpvl_referencia = '';
		
		require_once _MODULEDIR_.'Cadastro/View/cad_inpc/formulario.php';
		
		return true;
	}
	
	public function excluir() {
		$dados   = new stdClass();
		$retorno = array(
			'status'   => true,
			'dados'    => array(),
			'mensagem' => array(
				'classe' => '',
				'texto'  => ''
			)
		);
		
		if(empty($_POST['data'])) {
			$retorno['status'] = false;
			
			$retorno['mensagem']['classe'] = 'erro';
			$retorno['mensagem']['texto']  = utf8_encode('Houve um erro no processamento dos dados.');
		} else {
			$dados->inpdt_referencia = '01/'.str_replace('-', '/', $_POST['data']);
		}
		
		if($retorno['status']) {
			$dados->usuario = $_SESSION['usuario']['oid'];
			
			if(!$this->dao->excluir($dados)) {
				$retorno['status'] = false;
				
				$retorno['mensagem']['classe'] = 'erro';
				$retorno['mensagem']['texto']  = utf8_encode('Houve um erro no processamento dos dados.');
			}
		}
		
		echo json_encode($retorno);
		
		return true;
	}
	
	/**
	 * Método ajax para pesquisar
	 * 
	 * @return boolean
	 */
	public function pesquisar() {
		$dados   = new stdClass();
		$retorno = array(
			'status'   => true,
			'dados'    => array(),
			'mensagem' => array(
				'classe' => '',
				'texto'  => ''
			),
			'html'     => ''
		);
		
		if(empty($_POST['inpdt_inicial'])) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
				'campo' => 'inpdt_inicial',
				'mensagem' => utf8_encode('Campo obrigatório')
			);
		} else {
			$dados->inpdt_inicial = '01/'.$_POST['inpdt_inicial'];
		}
		
		if(empty($_POST['inpdt_final'])) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
				'campo' => 'inpdt_final',
				'mensagem' => utf8_encode('Campo obrigatório')
			);
		} else {
			$dados->inpdt_final = '01/'.$_POST['inpdt_final'];
		}
		
        
        if (!empty($_POST['inpdt_inicial']) && !empty($_POST['inpdt_final'])) {
            $inpdt_inicial = 0;
            $inpdt_final   = 0;

            $temp = explode('/', $dados->inpdt_inicial);
            $inpdt_inicial = strtotime($temp[2].'-'.$temp[1].'-'.$temp[0].' 00:00:00');
            $temp = explode('/', $dados->inpdt_final);
            $inpdt_final = strtotime($temp[2].'-'.$temp[1].'-'.$temp[0].' 00:00:00');

            if($inpdt_inicial > $inpdt_final) {
                $retorno['status']  = false;
                $retorno['dados'][] = array(
                    'campo' => 'inpdt_inicial'
                );
                $retorno['dados'][] = array(
                    'campo' => 'inpdt_final'
                );
                $retorno['mensagem']['classe'] = 'alerta';
                $retorno['mensagem']['texto']  = utf8_encode('Período inicial não pode ser superior ao período final.');
            }
       }
		
		if($retorno['status']) {
			$registros = $this->dao->pesquisar($dados);
				
			if($registros === false) {
				$retorno['mensagem']['classe'] = 'erro';
				$retorno['mensagem']['texto']  = utf8_encode('Houve um erro no processamento dos dados.');
			} else {
				$_SESSION['sessao_cad_inpc'] = new stdClass();
				$_SESSION['sessao_cad_inpc']->inpdt_inicial = $_POST['inpdt_inicial'];
				$_SESSION['sessao_cad_inpc']->inpdt_final   = $_POST['inpdt_final'];
				
				ob_start();
				
				require_once _MODULEDIR_.'Cadastro/View/cad_inpc/listagem_resultado.php';
				
				$retorno['dados'] = $registros;
				$retorno['html']  = ob_get_clean();
			}
		} elseif($retorno['mensagem']['texto'] == '') {
			$retorno['mensagem']['classe'] = 'alerta';
			$retorno['mensagem']['texto']  = utf8_encode('Existem campos obrigatórios não preenchidos.');
		}
		
		echo json_encode($retorno);
		
		return true;
	}
	
	/**
	 * Método ajax para salvar
	 * 
	 * @return boolean
	 */
	public function salvar() {
		$dados   = new stdClass();
		$retorno = array(
			'status'   => true,
			'dados'    => array(),
			'mensagem' => array(
				'classe' => '',
				'texto'  => ''
			)
		);
		
		if(empty($_POST['tipo'])) {
			$retorno['mensagem']['classe'] = 'erro';
			$retorno['mensagem']['texto']  = utf8_encode('Houve um erro no processamento dos dados.');
			
			return true;
		} else {
			$tipo = $_POST['tipo'];
		}
		
		if(empty($_POST['inpdt_referencia'])) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
				'campo' => 'inpdt_referencia',
				'mensagem' => utf8_encode('Campo obrigatório')
			);
		} else {
			$dados->inpdt_referencia = '01/'.$_POST['inpdt_referencia'];
		}
		
		if(empty($_POST['inpvl_referencia'])) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
				'campo' => 'inpvl_referencia',
				'mensagem' => utf8_encode('Campo obrigatório')
			);
		} else {
			$dados->inpvl_referencia = (float)str_replace(',', '.', $_POST['inpvl_referencia']);
			
			if($dados->inpvl_referencia < 0 || $dados->inpvl_referencia > 100) {
				$retorno['status']  = false;
				$retorno['dados'][] = array(
					'campo' => 'inpvl_referencia',
					'mensagem' => utf8_encode('Valor inválido')
				);
			}
		}
		
		if($retorno['status']) {
			$registro = $this->dao->pesquisar($dados);
			
			if($registro === false) {
				$retorno['mensagem']['classe'] = 'erro';
				$retorno['mensagem']['texto']  = utf8_encode('Houve um erro no processamento dos dados.');
			} elseif($tipo == 'cadastrar') {
				if(count($registro) > 0) {
					$retorno['mensagem']['classe'] = 'alerta';
					$retorno['mensagem']['texto']  = utf8_encode('Já existe INPC cadastrado para esse mês/ano.');
				} elseif($this->dao->cadastrar($dados)) {
					$retorno['mensagem']['classe'] = 'sucesso';
					$retorno['mensagem']['texto']  = utf8_encode('O registro foi incluído com sucesso.');
				} else {
					$retorno['mensagem']['classe'] = 'erro';
					$retorno['mensagem']['texto']  = utf8_encode('Houve um erro no processamento dos dados.');
				}
			} elseif($tipo == 'alterar') {
				if($this->dao->alterar($dados)) {
					$retorno['mensagem']['classe'] = 'sucesso';
					$retorno['mensagem']['texto']  = utf8_encode('O registro foi alterado com sucesso.');
				} else {
					$retorno['mensagem']['classe'] = 'erro';
					$retorno['mensagem']['texto']  = utf8_encode('Houve um erro no processamento dos dados.');
				}
			}
		} elseif($retorno['mensagem']['texto'] == '') {
			$retorno['mensagem']['classe'] = 'alerta';
			$retorno['mensagem']['texto']  = utf8_encode('Existem campos obrigatórios não preenchidos.');
		}
		
		echo json_encode($retorno);
		
		return true;
	}
	
}