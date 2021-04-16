<?php
require _MODULEDIR_ . 'Cadastro/DAO/CadParametrizacaoConsultaGestorCreditoDAO.php';

/**
 * @author Angelo Frizzo Junior / Kleber Goto Kihara <angelo.frizzo@meta.com.br, kleber.kihara@meta.com.br>
 * @description	Módulo para Parametrização gestor Crédito - Action
 */
class ParametrizacaoConsultaGestorCredito {

	private $dao;
	private $parametrizacao;
	private $msg;
    private $tipos;
	
	/**
	 * Método construtor da classe
	 */
    public function __construct() {
	
		$this->dao = new CadParametrizacaoConsultaGestorCreditoDAO();
		$this->parametrizacao = "";
		$this->msg = array();
        $this->tipos = array(
				'L' => 'L-Locação',
				'D' => 'D-Demonstração',
				'I' => 'I-Duplicação',
				'M' => 'M-Migração de Contrato',
				'U' => 'U-Upgrade',
				'G' => 'G-Downgrade',
				'S' => 'S-Substituição',
				'T' => 'T-Transferência Titularidade',
				'V' => 'V-Transferência Titularidade com Troca Veículo',
				'C' => 'C-Troca de Veiculo',
				'R' => 'R-Revenda'
				);
	}
	
	/**
	 * Controlador para a página principal
	 */
	public function index() {
        
        $filtroPesquisa = $this->filtrar();
        
		try {
			$tipoProposta    = $this->carregarTipoProposta($filtroPesquisa->tipoProposta);
			$subtipoProposta = $this->carregarSubtipoProposta($filtroPesquisa->subtipoProposta, $filtroPesquisa->tipoProposta);
			$tipoContrato    = $this->carregarTipoContrato($filtroPesquisa->tipoContrato);
		} catch (Exception $e) {
			
		}
		require _MODULEDIR_ . 'Cadastro/View/cad_parametrizacao_consulta_gestor_credito/index.php';
	}
	
	/**
	 *  Controlador para a página de cadastro
	 */
	public function cadastrar() {
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		$persistirDados = "";
		$propostaAux = ""; //variável auxiliar de proposta em texto
		$propostaidAux = ""; // variável auxiliar de proposta id
		$subpropostaidAux = ""; // variável auxiliar de sub proposta id
		$contratoAux = 0; //variável auxiliar de contrato
		$pessoa = isset($_POST['tipo']) ? $_POST['tipo'] : '';
		$proposta_aux= isset($_POST['tipo_proposta']) ? $_POST['tipo_proposta'] : '';
		$proposta_aux = explode("-",$proposta_aux);
		$propostaid = $proposta_aux[0];
		$proposta = $proposta_aux[1];
		
		if(!empty($_POST['subtipo_proposta'])) {
			$subproposta_aux= $_POST['subtipo_proposta'];
			$subproposta_aux = explode("-",$subproposta_aux);
			$subpropostaid = $subproposta_aux[0];
			$subproposta = $subproposta_aux[1];
		} else {
			$subpropostaid = 'NULL';
			$subproposta = 'NULL';
		}
		
		$contrato = isset($_POST['tipo_contrato']) ? (int)$_POST['tipo_contrato'] : 0;
		$gestor = isset($_POST['vaigestor']) ? $_POST['vaigestor'] : '';
		$limite = !empty($_POST['limite_contrato']) ? $_POST['limite_contrato'] : 0;
		
		
		
		try {
			//verifica se é POST para inserção/alteração
			if (trim($contrato) != "" && !empty($proposta) && !empty($pessoa)) {
				$salvar =  !$this->dao->verificarExistenciaParametrizacao($pessoa, $proposta, $propostaid, $subpropostaid, $contrato, $gestor, $limite, intval($id) ) ;
				if ($salvar) {
					if (!$this->dao->salvar($pessoa, $proposta, $propostaid, $subpropostaid, $contrato, $gestor, $limite, $id)) {
						$this->msg = array(
								'tipo' => 'erro',
								'mensagem' => 'Falha ao gravar o registro.'
						);
					}
					
					$this->msg = array(
							'tipo' => 'sucesso',
							'mensagem' => 'Parâmetro salvo com sucesso.'
					);
					
					$this->limparFiltro();
					
				} else {
					$persistirDados = "$pessoa,$proposta,$propostaid,$subpropostaid,$contrato,$gestor,$limite";
					$this->msg = array(
							'tipo' => 'alerta',
							'mensagem' => 'Parâmetro já cadastrado.'
					);
					$this->limparFiltro();
				}
					
			}	
			
			//nesse caso é edição
			if ($id > 0) {
				$this->parametrizacao = $this->dao->buscarPorId($id);
				if ($this->parametrizacao != "") {
					$propostaAux = $this->parametrizacao->gcptipoproposta;
					$propostaidAux = $this->parametrizacao->gcptppoid;
					$subpropostaidAux = $this->parametrizacao->gcptppoid_sub;
					$contratoAux = $this->parametrizacao->gcptipocontrato;
                    $pessoa = $this->parametrizacao->gcptipopessoa;
                    $gestor = $this->parametrizacao->gcpindica_gestor;
                    $limite = $this->parametrizacao->gcpconlimite;
				} else {
					$this->msg = array(
						'tipo' => 'alerta',
						'mensagem' => 'Parâmetro inexistente.'
					);
				}
				
			}
			//Carrega as combos
			$tipoProposta = $this->carregarTipoProposta($propostaidAux);
			$subtipoProposta = $this->carregarSubTipoProposta($subpropostaidAux, $propostaidAux);
			$tipoContrato = $this->carregarTipoContrato($contratoAux);
			
		} catch (Exception $e) {
			
		}
		$limite = $this->parametrizacao->gcpconlimite;
		$limite = ($limite == 0) ? "" : $limite;
		require _MODULEDIR_ . 'Cadastro/View/cad_parametrizacao_consulta_gestor_credito/cadastro.php';
	}
	
	 /**
	  * Controlador para a página de cadastro novo
	  */
     public function carregarNovo() {
        
         $tipoProposta = $this->carregarTipoProposta($filtroPesquisa->tipoProposta);
         $subtipoProposta = $this->carregarSubTipoProposta($filtroPesquisa->subtipoProposta);
         $tipoContrato = $this->carregarTipoContrato($filtroPesquisa->tipoContrato);
        
         require _MODULEDIR_ . 'Cadastro/View/cad_parametrizacao_consulta_gestor_credito/cadastro.php';
        
    }
	
	 /**
	  * Controlador para carregar combo Tipo de Proposta
	  */
    public function carregarTipoProposta($selecionado = '') {
		$selecionado = explode('-', $selecionado);
		$selecionado = $selecionado[0];
		
		$args = array(
			'tppoid_supertipo IS NULL'
		);
		$html = "";
		
		foreach($this->dao->buscarTiposPropostas($args) as $tipo) {
			if($tipo->tppoid == $selecionado)
				$html .= "<option selected value=\"$tipo->tppoid-$tipo->tppcodigo\">$tipo->tppdescricao</option>";
			else
				$html .= "<option value=\"$tipo->tppoid-$tipo->tppcodigo\">$tipo->tppdescricao</option>";
		}
		
		return $html;
	}
	
	 /**
	  * Controlador para carregar combo Tipo de Sub Proposta
	  */
	public function carregarSubtipoProposta($selecionado = '', $tipo_proposta = 0) {
		$imprime = isset($_POST['imprime']) ? $_POST['imprime'] : 0;
		
		$selecionado = explode('-', $selecionado);
		$selecionado = $selecionado[0];
		
		if(!$tipo_proposta) {
			if(isset($_POST['tipoproposta'])) {
				$tipo_proposta = $_POST['tipoproposta'];
			} elseif(isset($_POST['tipo_proposta'])) {
				$tipo_proposta = $_POST['tipo_proposta'];
			}
		}
		
		if($tipo_proposta == '') {
			$tipo_proposta = 0;
		}
		
		if(strstr($tipo_proposta, '-') !== false) {
			$tipo_proposta = explode('-', $tipo_proposta);
			$tipo_proposta = $tipo_proposta[0];
		}
		
		$args = array(
			'tppoid_supertipo = '.$tipo_proposta
		);
		$html = "";
		
		if($tipo_proposta) {
			foreach($this->dao->buscarTiposPropostas($args) as $tipo) {
				if($tipo->tppoid == $selecionado)
					$html .= "<option selected value=\"$tipo->tppoid-$tipo->tppcodigo\">$tipo->tppdescricao</option>";
				else
					$html .= "<option value=\"$tipo->tppoid-$tipo->tppcodigo\">$tipo->tppdescricao</option>";
			}
		}
		
		if($imprime) {
			echo $html;
		}
		
		return $html;
	}
	
	/**
	 * Controlador para carregar combo Tipo de Contrato
	 */
	public function carregarTipoContrato($selecionado="") {
		$tipos = $this->dao->buscarTiposContratos();
		$html = "";
		foreach ($tipos as $tipo) {
			if ($tipo->tpcoid == $selecionado && $selecionado != "")
				$html .= "<option selected value='$tipo->tpcoid'>$tipo->tpcdescricao</option>";
			else 
				$html .= "<option value='$tipo->tpcoid'>$tipo->tpcdescricao</option>";
		}
		return $html;
	}
	
	 /**
	  * Controlador para carregar combo Tipo de Pessoa
	  */
	public function carregarTipoPessoa($selecionado="") {
		$tipos = array(
			    "F" => "F-Física",
				"J" => "J-Jurídica"
				);
		$html = "";
		
		foreach ($tipos as $tipo) {
			$tipo = explode("-", $tipo);
			if ($tipo[0] == $selecionado) 
				$html .= "<option selected value='$tipo[0]'>$tipo[1]</option>";	
			else 
				$html .= "<option value='$tipo[0]'>$tipo[1]</option>";
		}
		return $html;
	}
    
	 /**
	  * Controlador para pesquisar registros na base de dados
	  */
	public function pesquisar() {
        
        $filtroPesquisa = $this->filtrar();
        
        $resultado = $this->dao->pesquisar($filtroPesquisa);
        
        $conteudo = array(
			'numero_resultados',
			'registros' => array()
		);
        
        $contador = 0;
        foreach($resultado as $res) {
            
            $res->tipoproposta    = utf8_encode($res->tipoproposta);
            $res->subtipoproposta = utf8_encode($res->subtipoproposta);
            $res->tipopessoa      = utf8_encode($res->tipopessoa);
            $res->tipocontrato    = utf8_encode($res->tipocontrato);
            $res->vaigestor       = utf8_encode($res->vaigestor);
            $res->limite          = utf8_encode($res->limite);
            
            $conteudo['registros'][] = $res;
            
            $contador++;
        }
        
        $conteudo['numero_resultados'][] = $contador;
        
        echo json_encode($conteudo);
    }
    
    /**
     * Controlador para filtrar dados para pesquisa na base de dados
     */
    private function filtrar() {
        
        $filtroPesquisa = new stdClass();
        $filtroPesquisa->tipoContrato    = '';
        $filtroPesquisa->tipoProposta    = '';
        $filtroPesquisa->subtipoProposta = '';
        $filtroPesquisa->tipoPessoa      = '';
        $filtroPesquisa->vaiGestor       = '';
        $filtroPesquisa->limite          = '';
        
        if (isset($_SESSION['cad_parametrizacao_consulta_gestor_credito'])) {
            $filtroPesquisa = $_SESSION['cad_parametrizacao_consulta_gestor_credito'];
        }
        
        if (count($_POST)) {
            $filtroPesquisa->tipoContrato    = isset($_POST['tipoContrato'])    ? $_POST['tipoContrato']    : $filtroPesquisa->tipoContrato;
            $filtroPesquisa->tipoProposta    = isset($_POST['tipoProposta'])    ? $_POST['tipoProposta']    : $filtroPesquisa->tipoProposta;
            $filtroPesquisa->subtipoProposta = isset($_POST['subtipoProposta']) ? $_POST['subtipoProposta'] : $filtroPesquisa->subtipoProposta;
            $filtroPesquisa->tipoPessoa      = isset($_POST['tipoPessoa'])      ? $_POST['tipoPessoa']      : $filtroPesquisa->tipoPessoa;
            $filtroPesquisa->vaiGestor       = isset($_POST['vaiGestor'])       ? $_POST['vaiGestor']       : $filtroPesquisa->vaiGestor;
        }
        
        $_SESSION['cad_parametrizacao_consulta_gestor_credito'] = $filtroPesquisa;
        
        return $filtroPesquisa;
    }
    
    /**
     * Controlador para limpar filtros de pesquisa 
     */
    private function limparFiltro() {
    	
        $filtroPesquisa = new stdClass();
        $filtroPesquisa->tipoContrato    = '';
        $filtroPesquisa->tipoProposta    = '';
        $filtroPesquisa->subtipoProposta = '';
        $filtroPesquisa->tipoPessoa      = '';
        $filtroPesquisa->vaiGestor       = '';
        $filtroPesquisa->limite 		 = '';
        
        $_SESSION['cad_parametrizacao_consulta_gestor_credito'] = $filtroPesquisa;
    }
    
	 /**
	  * Controlador para a excluir registro da base de dados
	  */
	  public function excluir() {
        
        $retorno = array(
            'status' => true,
            'dados'  => array()
        );
        
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        
        $excluiu = $this->dao->excluir($id);

        if(!$excluiu) {
            $retorno['status']  = false;
            $retorno['tipoErro']  = 'erro';
            $retorno['mensagem']  = 'Houve algum erro no processamento dos dados.';
        }
     
        
        echo json_encode($retorno);
        
    }
}
