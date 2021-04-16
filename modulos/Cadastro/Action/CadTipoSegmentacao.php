<?php

/**
 * @author Christian Monteiro <christian.@meta.com.br>
 */

/**
 * Arquivo DAO responsï¿½vel pelas requisiï¿½ï¿½es ao banco de dados
 */
require _MODULEDIR_ . 'Cadastro/DAO/CadTipoSegmentacaoDAO.php';
require _SITEDIR_ . 'lib/Components/Texto.php';

class CadTipoSegmentacao {
    
    private $dao;
    
    public function index() {
        $this->comboTiposSegmentacao = $this->dao->getComboTiposSegmentacao();
        
        require _MODULEDIR_ . 'Cadastro/View/cad_tipo_segmentacao/index.php';
        
    }
	
	public function pesquisar() {
		
		$and_array = array();
		
        $tpsdescricao = !empty($_POST['tpsdescricao']) ? addslashes(trim($_POST['tpsdescricao'])) : '';
        $tpssegmentacao = !empty($_POST['tpssegmentacao']) ? $_POST['tpssegmentacao'] : '';
        
        if(!empty($tpsdescricao)) {
			array_push($and_array, " AND f.tpsdescricao ILIKE '%$tpsdescricao%'");
		}
        
        if(!empty($tpssegmentacao)) {
			array_push($and_array, " AND (f.tpssegmentacaooid = $tpssegmentacao OR f.tpsoid = $tpssegmentacao)");
		}
                
        
        $and = implode('', $and_array);
        
		$resultado = $this->dao->pesquisar($and);        
      	
        echo json_encode($resultado);
		
	}
    
    public function novo() {
        $this->comboTiposSegmentacao = $this->dao->getComboTiposSegmentacao();
        require _MODULEDIR_ . 'Cadastro/View/cad_tipo_segmentacao/novo.php';
        
    }
    
    public function editar() {
        
        $id = !empty($_GET['id']) ? trim($_GET['id']) : '';
        
        if(empty($id)) {
            header('Location: cad_tipo_segmentacao.php');
        }
        
        $this->comboTiposSegmentacao = $this->dao->getComboTiposSegmentacao($id);
        
        $this->TipoSegmentacao = $this->dao->getTipoSegmentacaoById($id);
        
        if(!$this->TipoSegmentacao) {
            $this->error_message = "Segmentação não encontrada.";
            require _MODULEDIR_ . 'Cadastro/View/cad_tipo_segmentacao/index.php';
            return false;
        }
                
        require _MODULEDIR_ . 'Cadastro/View/cad_tipo_segmentacao/editar.php';
        
    } 
    
    public function atualizar() {
        $TipoSegmentacao = new stdClass();
        
        $TipoSegmentacao->tpsoid = $_POST['tpsoid'];
        $TipoSegmentacao->tpsdescricao   = isset($_POST['tpsdescricao'])     ? trim($_POST['tpsdescricao']) : '';
        $TipoSegmentacao->tpssegmentacao = !empty($_POST['tpssegmentacao'])   ? $_POST['tpssegmentacao']     : 'NULL';
        $TipoSegmentacao->tpsprincipal = isset($_POST['tpsprincipal'])   ? $_POST['tpsprincipal']  : '';
        $TipoSegmentacao->tpcodigoslug   = Texto::slugify($TipoSegmentacao->tpsdescricao, '_');
        
        $TipoSegmentacaoAtual = $this->dao->getTipoSegmentacaoById($TipoSegmentacao->tpsoid);
        
        $retorno = $this->validaForm($TipoSegmentacao);
        
        if($retorno['status']) {
            
            $qtd_filhos_segmentacao = $this->dao->getQtdFilhosSegmentacao($TipoSegmentacao->tpsoid);
            
            /**
             * A alteração não pode ser ralizada se:
             * Não for um tipo principal e possuir filhos;
             * Passar de tipo não principal para principal mas possuir um pai;
             * Se o id do pai e do filho forem iguais.
             */
            if(
                ($TipoSegmentacao->tpsprincipal == 'nao' && $qtd_filhos_segmentacao > 0) 
                    || 
                ($TipoSegmentacao->tpsprincipal == 'sim' && $TipoSegmentacaoAtual->TipoSegmentacaoPai != false)
            ) {
                
                $retorno['status']  = false;
                $retorno['tipoErro']  = 'alerta';
                $retorno['mensagem']  = utf8_encode("Não é possível alterar o tipo <b>".utf8_decode($TipoSegmentacao->tpsdescricao)."</b>, pois existem outros tipos vinculados a este.");
                
            } else if($TipoSegmentacao->tpsoid == $TipoSegmentacao->tpssegmentacao) {
                
                $retorno['status']  = false;
                $retorno['tipoErro']  = 'alerta';
                $retorno['mensagem']  = utf8_encode("A segmentação e o módulo principal devem ser diferentes.");
                
                $retorno['dados'][] = array(
                    'campo' => 'tpsdescricao',
                    'mensagem' => '');
                
                $retorno['dados'][] = array(
                    'campo' => 'tpssegmentacao',
                    'mensagem' => '');
                
            } else {
            
                $atualizou = $this->dao->atualizar($TipoSegmentacao);

                if(!$atualizou) {
                    $retorno['status']  = false;
                    $retorno['tipoErro']  = 'erro';
                    $retorno['mensagem']  = 'Houve algum erro no processamento dos dados.';
                }
            }
        }
        
        echo json_encode($retorno);
        
    }
    
    public function inserir() {
        
        $TipoSegmentacao = new stdClass();
        
        $TipoSegmentacao->tpsdescricao   = isset($_POST['tpsdescricao'])     ? trim($_POST['tpsdescricao']) : '';
        $TipoSegmentacao->tpssegmentacao = !empty($_POST['tpssegmentacao'])   ? $_POST['tpssegmentacao']     : 'NULL';
        $TipoSegmentacao->tpsprincipal = isset($_POST['tpsprincipal'])   ? $_POST['tpsprincipal']  : '';
        $TipoSegmentacao->tpcodigoslug   = Texto::slugify($TipoSegmentacao->tpsdescricao, '_');
        
        $retorno = $this->validaForm($TipoSegmentacao);
        
        if($retorno['status']) {
            $inseriu = $this->dao->inserir($TipoSegmentacao);
            
            if(!$inseriu) {
                $retorno['status']  = false;
                $retorno['tipoErro']  = 'erro';
                $retorno['mensagem']  = 'Houve algum erro no processamento dos dados.';
            }
        }
        
        echo json_encode($retorno);
        
    }
    
    public function excluir() {
        
        $retorno = array(
            'status' => true,
            'dados'  => array()
        );
        
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $descricao = isset($_POST['descricao']) ? utf8_decode($_POST['descricao']) : '';
        
        $qtd_filhos_segmentacao = $this->dao->getQtdFilhosSegmentacao($id);
        
        $qtd_registros_clientes = $this->dao->getQtdRegistrosClientes($id);
            
        if($qtd_filhos_segmentacao > 0) {
            $retorno['status']  = false;
            $retorno['tipoErro']  = 'alerta';
            $retorno['mensagem']  = utf8_encode("Não é possível excluir o tipo <b>$descricao</b>, pois existem outros tipos vinculados a este.");            
        } else if($qtd_registros_clientes > 0) {
            $retorno['status']  = false;
            $retorno['tipoErro']  = 'alerta';
            $retorno['mensagem']  = utf8_encode("Não é possível excluir o tipo <b>$descricao</b>, pois este já está sendo utilizado.");            
        } else {

            $excluiu = $this->dao->excluir($id);

            if(!$excluiu) {
                $retorno['status']  = false;
                $retorno['tipoErro']  = 'erro';
                $retorno['mensagem']  = 'Houve algum erro no processamento dos dados.';
            }
        }
        
        echo json_encode($retorno);
        
    }
    
    private function validaForm($TipoSegmentacao) {
        
        $retorno = array(
            'status' => true,
            'dados'  => array()
        );
        
        if (empty($TipoSegmentacao->tpsdescricao)) {
            $retorno['status']  = false;
            $retorno['tipoErro']  = 'alerta';
            $retorno['mensagem'] = utf8_encode('Existem campos obrigatórios não preenchidos.');
            $retorno['dados'][] = array(
                'campo' => 'tpsdescricao',
                'mensagem' => utf8_encode('Campo obrigatório.'));
        }

        if (empty($TipoSegmentacao->tpsprincipal)) {
            $retorno['status']  = false;
            $retorno['tipoErro']  = 'alerta';
            $retorno['mensagem'] = utf8_encode('Existem campos obrigatórios não preenchidos.');
            $retorno['dados'][] = array(
                'campo' => 'tpsprincipal',
                'mensagem' => utf8_encode('Campo obrigatório.'));
        } 

        if ($TipoSegmentacao->tpsprincipal == 'nao' && $TipoSegmentacao->tpssegmentacao == 'NULL') {
            $retorno['status']  = false;
            $retorno['tipoErro']  = 'alerta';
            $retorno['mensagem'] = utf8_encode('Existem campos obrigatórios não preenchidos.');
            $retorno['dados'][] = array(
                'campo' => 'tpssegmentacao',
                'mensagem' => utf8_encode('Campo obrigatório.'));
            
        }
        
        return $retorno;
        
    }
    
    public function recarregaComboSegmentacao() {
        $combo = $this->dao->getComboTiposSegmentacao();
        
        echo json_encode($combo);
    }
    
    public function __set($var, $value) {
        $this->$var = $value;
    }
    
    public function __get($var) {
        return $this->$var;
    }
    
    public function __construct() {
        
        $this->dao = new CadTipoSegmentacaoDAO();
        
    }
    
}
