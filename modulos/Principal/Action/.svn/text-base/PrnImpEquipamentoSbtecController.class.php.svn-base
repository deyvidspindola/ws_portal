<?php
header('Content-Type: text/html; charset=ISO-8859-1');
require _SITEDIR_.'modulos/Principal/DAO/PrnImpEquipamentoSbtecDAO.class.php';
require _SITEDIR_.'modulos/Principal/View/PrnImpEquipamentoSbtecView.class.php'; 
ini_set("display_errors", 1);
/**
 * Classe para controlar os fluxos.
 * @author Bruno Bonfim Affonso [bruno.bonfim@sascar.com.br]
 * @package Principal
 * @version 1.0
 * @since 21/11/2013
 */
class PrnImpEquipamentoSbtecController{
    private $dao    = null;
    private $view   = null;
    private $usuoid = null;

    /**
     * Construtor da classe.
     */
    public function __construct($acao){
        $this->dao    = new PrnImpEquipamentoSbtecDAO();
        $this->view   = new PrnImpEquipamentoSbtecView();
        $this->usuoid = $_SESSION['usuario']['oid'];
		$this->redirect($acao);        
    }
    
    /**
     * Redireciona conforme a AÇÃO solicitada.
     * @param mixed $acao
     */
    private function redirect($acao=''){
    	switch($acao){
    		case 'processar':
    			$this->processarArquivo();
    		break;
    		case 'importar':
    			$this->importarEquipamento($_POST['dados']);
    		break;
    		default:
    			$this->index();
    		break;
    	}
    }
    
    /**
     * Solicita a tela inicial.
     * @param array $dados (Contem a mensagem de retorno)
     */
    private function index($dados=array()){
    	$this->view->index($dados);
    }
    
    /**
     * Processa o arquivo e mostra a tela com o grid de informações.
     */
    private function processarArquivo(){
    	$fileName = trim($_FILES['file']['name']);
    	$file = $_FILES['file']['tmp_name'];
    	    	
    	if($fileName != '' && $fileName != $_SESSION['imp_equipamento_sbtec']){
    		//Atributos
	    	$dados = array();
	    	$seriais = array();
	    	$ext = '';
	    	$count_lines = 0;
	    	    	   	
	    	//Capturando extensao
            $tmpArray = explode('.', $fileName);
	    	$ext = strtolower($tmpArray[count($tmpArray) - 1]);
	    	
	    	//Validando
	    	if($ext != 'csv'){
	    		$dados['msgalerta'] = 'O formato do arquivo não é válido.';
	    		$this->index($dados);
	    	} else{
	    		//Obtendo o total de linhas do arquivo
	            $array = file($file);
	            $total_lines = count($array);
	            
	            //Abrindo o arquivo
	            $file = fopen($file, 'r');	            
	            
	            //Arquivo vazio
	            if($total_lines == 0){
	            	$dados['msgerro'] = 'O arquivo está vazio.';
	            	$this->index($dados);
	            }
	            
	            //Percorrendo arquivo
	            while(!feof($file)){
	            	$count_lines++;
	            
	            	if($count_lines > $total_lines){
	            		break;
	            	}
	            	
	            	//Linha que está sendo lida
	            	$seriais[] = trim(fgets($file));
	            }
	            
	            if(!empty($seriais)){
	            	$_SESSION['imp_equipamento_sbtec'] = $fileName;
	            	$this->visualizarGrid($seriais);
	            } else{
	            	$this->index();
	            }
	    	}
    	} else{
    		$dados['msginfo'] = 'Arquivo inválido ou já foi processado.';
    		$this->index($dados);
    	}
    }
    
    /**
     * Realiza a importação do(s) equipamento(s) SBTEC para SASCAR.
     * 
     * @param string $dados (string que contem IDs dos equipamentos separados por virgulas)
     * @return json
     */
    private function importarEquipamento($dados){
    	$dados = trim($dados);
    	    	
    	if($dados != ''){    		
    		$dados  = explode(',', $dados);    		
    		$classe = '#msgsucesso';
    		$msg 	= 'Equipamento(s) importado(s) com sucesso!';
    		$observacao   = 'Importação de equipamento schema SBTEC para SASCAR.';
            $arraySucesso = array();
            $arrayErro    = array();
            $importados   = '';
            $falha        = '';
    		
    		foreach($dados as $row){
    			$whereKey = "equoid = $row";
    			$vEquipamento = $this->dao->getEquipamento($whereKey);
    			
    			if(!empty($vEquipamento)){
                    $this->dao->beginTransaction();
                    
    				$patrimonio = $vEquipamento['equpatrimonio'];								
    				
    				if($vEquipamento['eqsoid'] == ''){$vEquipamento['eqsoid'] = "NULL";}
    				if($vEquipamento['equeqmoid'] == ''){$vEquipamento['equeqmoid'] = "NULL";}
    				if($vEquipamento['equeqfoid'] == ''){$vEquipamento['equeqfoid'] = "NULL";}
    				if($vEquipamento['equprdoid'] == ''){$vEquipamento['equprdoid'] = "NULL";}
    				if($vEquipamento['equversao_hardware'] == ''){$vEquipamento['equversao_hardware'] = "NULL";}
    				if($vEquipamento['equversao_firmware'] == ''){$vEquipamento['equversao_firmware'] = "NULL";}
    				    				
    				//ENTRADA
    				$entoid = $this->dao->existeEntrada($patrimonio);
    				
    				if($entoid){
    					$vEntrada = $this->dao->getEntrada($patrimonio);
    					$entoid = $vEntrada['entoid'];
    				} else{
    					$entoid = $this->dao->insertEntrada($patrimonio);
    					
    					if(!$entoid){
                            $entoid = "NULL";
    					}
    				}
    				
    				//ITEM ENTRADA
    				$entioid = $this->dao->existeItemEntrada($patrimonio);
    				
    				if($entioid){
    					$vItemEntrada = $this->dao->getItemEntrada($patrimonio);
    					$entioid = $vItemEntrada['entioid'];
    				} else{
    					$entioid = $this->dao->insertItemEntrada($patrimonio, $entoid);
    						
    					if($entioid == false){
                            $entioid = "NULL";
    					}
    				}
    				
    				//IMOBILIZADO
    				$imoboid = $this->dao->existeImobilizado($patrimonio);
    				
    				if($imoboid){
    					$vEntrada = $this->dao->getImobilizado($patrimonio);
    					$imoboid = $vEntrada['imoboid'];
    				} else{
    					$imoboid = $this->dao->insertImobilizado($patrimonio, $entoid, $entioid);
    						
    					if($imoboid == false){
    						$this->dao->rollbackTransaction();
    						$classe = '#msgerro';
    						$msg = 'N&atilde;o foi poss&iacute;vel inserir o imobilizado do seguinte patrim&ocirc;nio: '.$patrimonio;
                            $arrayErro[] = $vEquipamento['equno_serie'];
                            $falha .= $vEquipamento['equno_serie'].'; ';
    						continue;
    					}
    				}
    				
    				//ATUALIZANDO DADOS NO EQUIPAMENTO CRIADO A PARTIR DO IMOBILIZADO
    				$equoid = $this->dao->updateEquipamento($patrimonio, $vEquipamento);
    				
    				if($equoid == false){
    					$this->dao->rollbackTransaction();
    					$classe = '#msgerro';
    					$msg = 'N&atilde;o foi poss&iacute;vel atualizar os dados do equipamento (patrim&ocirc;nio: '.$patrimonio.')';
                        $arrayErro[] = $vEquipamento['equno_serie'];
                        $falha .= $vEquipamento['equno_serie'].'; ';
    					continue;
    				}
    				
    				//ATUALIZANDO IMOBILIZADO
    				$imoboid = $this->dao->updateImobilizado($patrimonio, $imoboid);
    				
    				if($imoboid == false){
    					$this->dao->rollbackTransaction();
    					$classe = '#msgerro';
    					$msg = 'N&atilde;o foi poss&iacute;vel atualizar o imobilizado do seguinte patrim&ocirc;nio: '.$patrimonio;
                        $arrayErro[] = $vEquipamento['equno_serie'];
                        $falha .= $vEquipamento['equno_serie'].'; ';
    					continue;
    				}
    				
    				//INSERINDO LINHA
    				$linoid = $this->dao->insertLinha($vEquipamento['equno_fone'], $vEquipamento['equaraoid']);
    				
    				if($linoid == false){
                        $linoid = "NULL";
    				}
                    
                    $linoid_sbtec = $this->dao->getLinhaSBTEC($vEquipamento['equno_fone'], $vEquipamento['equaraoid']);
    				
    				//INSERE CELULAR
    				$celoid = $this->dao->insertCelular($vEquipamento['equno_fone'], $vEquipamento['equaraoid'], $linoid, $linoid_sbtec);
    				
    				if($celoid == false){
                        $celoid = "NULL";
    				}
    				
    				//HISTÓRICO
    				//$this->dao->insertHistoricoImobilizado($observacao, $imoboid, $this->usuoid);
    				$this->dao->insertHistoricoEquipamento($observacao, $vEquipamento['equno_serie'], $this->usuoid);
    				
    				//BAIXA IMOBILIZADO SBTEC
    				//$this->dao->updateImobilizadoSBTEC($patrimonio);
    				//$this->dao->updateImobilizadoHistoricoSBTEC($observacao, $patrimonio, $this->usuoid);
    				$this->dao->updateEquipamentoSBTEC($patrimonio);
    				$this->dao->updateHistoricoEquipamentoSBTEC($observacao, $vEquipamento['equno_serie'], $this->usuoid);                    
                    
                    if($classe == '#msgsucesso'){
                        //Recebe 'equoid' importados com sucesso
                        $arraySucesso[] = $vEquipamento['equno_serie'];
                        $importados .= $vEquipamento['equno_serie']."; ";
                        $this->dao->commitTransaction();                        
                    }
    			}
    		}
            
            if(!empty($arraySucesso)){
                $classe = '#msgsucesso';
                $msg 	= trim('Equipamento(s) importado(s) com sucesso! Serial(s): '.$importados);
            } else{
                $classe = '#msgerro';
                $msg 	= trim('N&atilde;o foi poss&iacute;vel importar. Serial(s): '.$falha);
            }
    		
    		echo json_encode(array('classe' => "$classe", 'msg' => "$msg", 'arraySucesso' => $arraySucesso, 'arrayErro' => $arrayErro));
    	} else{
    		echo json_encode(array('classe' => '#msgalerta', 'msg' => 'Selecione o(s) equipamento(s) para importar.'));
    	}
    	exit;
    }
    
    /**
     * Solicita a tela com que contem o GRID dos seriais
     * carregados do arquivo (.csv)
     * 
     * @param array $dados (Contem os SERIAIS)
     */
    private function visualizarGrid($dados=array()){
    	if(!empty($dados)){
    		$resultSet = array();
    		
    		foreach($dados as $row){
                //Verifica se existe o número de série no schema sascar
                $result = $this->dao->serieExists($row);

                //Caso não exista, entra no IF.
                if($result == 0){
                    $whereKey = "equno_serie = $row";
                    $result   = $this->dao->getEquipamento($whereKey);
                
                    if(!empty($result)){
                        $resultSet[] = $result;
                    }
                }    			
    		}
    		    		
    		if(!empty($resultSet)){
    			$this->view->visualizarGrid($resultSet);
    		} else{
    			$dados['msginfo'] = 'Não há registros para esses seriais.';
    			$this->index($dados);
    		}
    	} else{
    		$dados['msgalerta'] = 'Verifique se o arquivo não está vazio.';
    		$this->index($dados);
    	}
    }
}
?>