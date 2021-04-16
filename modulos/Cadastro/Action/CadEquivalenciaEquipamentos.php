<?php

require_once _MODULEDIR_ ."/Cadastro/DAO/CadEquivalenciaEquipamentosDAO.php";
require_once "lib/Components/PHPExcel/PHPExcel.php";

class CadEquivalenciaEquipamentos {
        
     private $dao;
     private $path = '/var/www/docs_temporario/';

     public function __construct() {
         $this->dao = new CadEquivalenciaEquipamentosDAO();
     }
    
     /*
      * Método responsável por renderizar a index
      */
     public function index(){
         
         $this->limparParametrosPesquisa();
         
         try{
             require_once _MODULEDIR_ ."/Cadastro/View/cad_equivalencia_equipamentos/index.php";
         } catch (Exception $e){
             
         }
     }
     
     /*
      * Método responsável por processar a pesquisa
      */
     public function pesquisar(){
         try{
             
             if (isset($_POST['pesquisar'])){
                 $this->parametros = $this->tratarParametrosPesquisa(); 
             }else{
                 $this->parametros = (object) $_SESSION['pesquisa'];
             }
             
             if ($this->parametros->pesquisar == '') {
                 $this->index();
                 return;
             }
             
             $resultado = $this->dao->pesquisar($this->parametros);
             
             $this->listagem = array();
             
             if ($resultado === FALSE) {
                 $this->tipoErro = 'E';
                 throw new Exception('Houve um erro no processamento de dados.'); 
             }else{
                 $this->listagem = $resultado;
             }   
             
             require_once _MODULEDIR_ ."/Cadastro/View/cad_equivalencia_equipamentos/index.php";
             
         } catch (Exception $e){
             $this->mensagemErro = $e->getMessage();
         }
     }
     
     /*
      * Método responsável por carregar a tela de cadastro
      */
     public function cadastrar($idEquivalencia = ''){
         
         
         $this->idEquivalencia = isset($_GET['id']) ? trim($_GET['id']) : $idEquivalencia;
         
         $this->produtos = $this->dao->buscarProdutosEquivalencia($this->idEquivalencia);
         
         try{
             
             if (isset($this->idEquivalencia) && !empty($this->idEquivalencia)) {
                 $this->parametrosEquivalencia = $this->dao->buscarEquivalencia($this->idEquivalencia);
                 
             }
             
             require_once _MODULEDIR_ ."/Cadastro/View/cad_equivalencia_equipamentos/cadastrar.php";
             
         } catch (Exception $e){
             
             
         }
     }
     
     
     /**
      * Metodo chamado pela view para geração do arquivo XLS referente ao arquivo.
      * @throws Exception
      * @return string
      */
     public function exportarXLS(){
     	$file = "rel_equivalencias_de_classes_".date('Y_m_d').".xlsx";
     	$path = $this->path;
  	
     	$retorno['tipo'] = 'S';
     	$retorno['mensagem'] = utf8_encode('Arquivo gerado com sucesso.');

     	try{
     		 
     		$this->parametros = $this->tratarPost();
     		$resultado = $this->dao->pesquisar($this->parametros);
     		 
     		if ($resultado === FALSE) {
     			$this->tipoErro = 'E';
     			throw new Exception('Houve um erro no processamento de dados.');
     		}
     		if(count($resultado)==0){
     			$this->tipoErro = 'A';
     			throw new Exception('Nenhum registro encontrado.');
     		}

     		$this->geraXLS($resultado, $path, $file);
     		
     		$retorno['file'] = utf8_encode("downloads.php?arquivo=docs_temporario/$file"); 
     	} catch (Exception $e){
     		$retorno['tipo'] = $this->tipoErro;
     		$retorno['mensagem'] = utf8_encode($e->getMessage());
     		echo $ret = json_encode($retorno);
     		return $ret;
     	}

     	echo $ret = json_encode($retorno);
     	return $ret;
     }
     
     
     /**
      * Gera o arquivo xls em disco
      * @param array $filtros
      */
     private function geraXLS($relatorio, $path, $file) {
     	 
     	// Arquivo modelo para gerar o XLS
     	$layout = (isset($this->parametros->classes_sem_cadastro) && !empty($this->parametros->classes_sem_cadastro)) ? "002" : "001";
     	$arquivoModelo = _MODULEDIR_.'Cadastro/View/cad_equivalencia_equipamentos/modelo_relatorio_'.$layout.'.xlsx';

     	// Instância PHPExcel
     	$reader = PHPExcel_IOFactory::createReader("Excel2007");
     	 
     	// Carrega o modelo
     	$PHPExcel = $reader->load($arquivoModelo);

     	if ($relatorio !== null) {
     		 
     		$linha = 8;
     		foreach ($relatorio as $row){     			
     			//$PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getNumberFormat()->setFormatCode('0');
     			
     			
     			
     			if(empty($this->parametros->classes_sem_cadastro)){
                    $row->tpcdescricao = is_null($row->tpcdescricao) ? 'TODOS' : $row->tpcdescricao;
                    $PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->modalidade));
                    $PHPExcel->getActiveSheet()->setCellValue('B'.$linha, utf8_encode($row->eqcdescricao));
	     			$PHPExcel->getActiveSheet()->setCellValue('C'.$linha, utf8_encode($row->tpcdescricao));
	     			$PHPExcel->getActiveSheet()->setCellValue('D'.$linha, utf8_encode($row->eeqdt_cadastro));
	     			$PHPExcel->getActiveSheet()->setCellValue('E'.$linha, utf8_encode($row->leidt_alteracao));
	     			$PHPExcel->getActiveSheet()->setCellValue('F'.$linha, utf8_encode(($row->nm_usuario)));
	     			$PHPExcel->getActiveSheet()->setCellValue('G'.$linha, utf8_encode($row->nm_usuario_2));
	     			
	     			$PHPExcel->getActiveSheet()->getStyle('B'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	     			$PHPExcel->getActiveSheet()->getStyle('C'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	     			$PHPExcel->getActiveSheet()->getStyle('D'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	     			$PHPExcel->getActiveSheet()->getStyle('E'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	     			$PHPExcel->getActiveSheet()->getStyle('F'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
	     			$PHPExcel->getActiveSheet()->getStyle('G'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
     			} else {
                    $PHPExcel->getActiveSheet()->setCellValue('A'.$linha, utf8_encode($row->eqcdescricao));
                }
                
                $PHPExcel->getActiveSheet()->getStyle('A'.$linha)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

     			$linha++;
     		}
     		 
     		$PHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
     		if(empty($this->parametros->classes_sem_cadastro)){
	     		$PHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	     		$PHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	     		$PHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	     		$PHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	     		$PHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	     		$PHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
     		}
     	}
     	else {
     		$PHPExcel->getActiveSheet()->setCellValue('A8', utf8_encode("Nenhum resultado encontrado."));
     	}
     	 
     	$writer = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
     	$writer->setPreCalculateFormulas(false);
     	 
     	if(!file_exists($path) || !is_writable($path)) {
     		$this->tipoErro = 'E';
     		throw new Exception('Houve um erro ao gerar o arquivo.');
     	}
     	 
     	$writer->save($path.$file);
     	return true;
     }
     
     /*
      * Método responsável por excluir produtos
      * @return json
      */
     public function excluirProdutos(){
         
         $parametros = $this->tratarPost();
         
         try{
            
            $usuarioId = $_SESSION['usuario']['oid'];
            
            foreach($parametros->excluir_produto as $produtoId) {
                $excluiu = $this->dao->excluirEquivalenciaItem($produtoId, $usuarioId);
                           
                if(!$excluiu) {
                    throw new Exception('Houve um erro no processamento dos dados.');
                }                
            }
            
            echo json_encode(array('equivalenciaId' => $parametros->eeieeqoid, 'tipoMensagem' => 'sucesso', 'mensagem' => utf8_encode('Registro excluído com sucesso.')));
            
         } catch (Exception $e){             
             echo json_encode(array('equivalenciaId' => $parametros->eeieeqoid, 'tipoMensagem' => 'erro', 'mensagem' => $e->getMessage()));             
         }
     }
     
     
     /*
      * Método responsável por carregar classes de contrato
      * @return json
      */
     public function carregarClassesContratos(){
         try{
             $erro = array();
             
             $filtros = $this->tratarPost();
             $dados = $this->dao->buscarClasseContrato($filtros);
             
             if ($dados === FALSE) {
                 $this->tipoErro = 'E';
                 throw new Exception('Houve um erro no processamento de dados.');
             }
             
             $opcoes = array();
             
             foreach ($dados as $item) {
                 $opcoes[] = array(
                   'id' => $item->eqcoid,
                   'label' => utf8_encode($item->eqcdescricao)
                 );
             }
             
             echo json_encode($opcoes);
             
         } catch (Exception $e){
             $erro['tipo'] = $this->tipoErro;
             $erro['mensagem'] = uft8_encode($e->getMessage());
             echo json_encode($erro);
         }
     }
     
     /*
      * Método responsável por carregar tipos de contrato
      * @return json
      */
     public function carregarTiposContratos(){
         try{
             $erro = array();
             
             $filtros = $this->tratarPost();
             $dados = $this->dao->buscarTipoContrato($filtros);
             
             if ($dados === FALSE) {
                 $this->tipoErro = 'E';
                 throw new Exception('Houve um erro no processamento de dados.');
             }
             
             $opcoes = array();
             if (!isset($filtros->copia) || !$filtros->copia){
                $opcoes[] = array(
                    'id'    => '-1',
                    'label' => 'TODOS'
                );
             }
             
             foreach ($dados as $item) {
                 if ($item->tpcoid < 0){
                     array_unshift($opcoes, array(
                        'id'    => '-1',
                        'label' => 'TODOS'
                    ));
                     continue;
                 }
                 array_push($opcoes, array(
                   'id' => $item->tpcoid,
                   'label' => utf8_encode($item->tpcdescricao)
                 ));
             }
             
             echo json_encode($opcoes);
             
         } catch (Exception $e){
            $erro['tipo'] = $this->tipoErro;
            $erro['mensagem'] = uft8_encode($e->getMessage());
            echo json_encode($erro);
             
         }
     }
     
     
     /*
      * Método responsável por carregar produtos para combobox
      * @return json
      */
     public function carregarProdutos(){
         try{
             $erro = array();
             
             $filtros = $this->tratarPost();
             $dados = $this->dao->buscarProdutos($filtros);
             
             if ($dados === FALSE) {
                 $this->tipoErro = 'E';
                 throw new Exception('Houve um erro no processamento de dados.');
             }
             
             $opcoes = array();
             
             foreach ($dados as $item) {
                 $opcoes[] = array(
                   'id' => $item->prdoid,
                   'label' => utf8_encode($item->prdproduto)
                 );
             }
             
             echo json_encode($opcoes);
             
         } catch (Exception $e){
            $erro['tipo'] = $this->tipoErro;
            $erro['mensagem'] = uft8_encode($e->getMessage());
            echo json_encode($erro);
             
         }
     }
     
     /*
      * Método responsável por carregar versões para combobox
      * @return json
      */
     public function carregarVersoes(){
         try{
             $erro = array();
             
             $filtros = $this->tratarPost();
             $dados = $this->dao->buscarVersoes($filtros);
             
             if ($dados === FALSE) {
                 $this->tipoErro = 'E';
                 throw new Exception('Houve um erro no processamento de dados.');
             }
             
             $opcoes = array();
             
             if (count($dados) > 0){
                 $opcoes[] = array(
                     'id'    => -1,
                     'label' => 'Todas'
                 );
             }
             
             foreach ($dados as $item) {
                 $opcoes[] = array(
                   'id' => $item->id,
                   'label' => utf8_encode($item->versao)
                 );
             }
             
             echo json_encode($opcoes);
             
         } catch (Exception $e){
            $erro['tipo'] = $this->tipoErro;
            $erro['mensagem'] = uft8_encode($e->getMessage());
            echo json_encode($erro);
             
         }
     }
     
     /*
      * Método responsável por cadastrar
      * @throws Exception
      */
     public function cadastrarEquivalencia(){
         try{
             
            $this->idEquivalencia = '';
            $this->parametrosEquivalencia = $this->tratarPost();
            
            $obrigatoriosPreenchidos = true;
            $erros = array();
            
            //valida Modalidade do Contrato
            if (!isset($this->parametrosEquivalencia->eqqmodalidade) || empty($this->parametrosEquivalencia->eqqmodalidade)) {
                $obrigatoriosPreenchidos = false;
                $erros[] = array(
                    'campo' => 'eqqmodalidade',
                    'mensagem'=> utf8_encode('Campo obrigatório.')
                );
            }
            
            //valida classe do Contrato
            if (!isset($this->parametrosEquivalencia->eeqeqcoid) || $this->parametrosEquivalencia->eeqeqcoid == '') {
                $obrigatoriosPreenchidos = false;
                $erros[] = array(
                    'campo' => 'eeqeqcoid',
                    'mensagem'=> utf8_encode('Campo obrigatório.')
                );
            }
            
            //valida tipo do Contrato
            if (!isset($this->parametrosEquivalencia->eeqtpcoid) || $this->parametrosEquivalencia->eeqtpcoid == '') {
                $obrigatoriosPreenchidos = false;
                $erros[] = array(
                    'campo' => 'eeqtpcoid',
                    'mensagem'=> utf8_encode('Campo obrigatório.')
                );
            }
            
            if (!$obrigatoriosPreenchidos) {
                $this->mensagemAlerta = 'Existem campos obrigatórios não preenchidos.';
                $this->erros = json_encode($erros);
                
            } else {
                
                $retornoPesquisa = $this->dao->pesquisar($this->parametrosEquivalencia);
            
                if(count($retornoPesquisa) > 0) {
                    $this->mensagemAlerta = 'Já existe uma equivalência cadastrada com a Modalidade, Classe e Tipo de Contrato selecionados.';
                    $this->cadastrar();
                } else {
                
                    //salvar
                    $this->parametrosEquivalencia->cd_usuario = isset($_SESSION['usuario']['oid']) && $_SESSION['usuario']['oid'] != '' ? $_SESSION['usuario']['oid'] : '';
                    $this->idEquivalencia = $this->dao->gravarEquivalencia($this->parametrosEquivalencia);

                    if ($this->idEquivalencia === FALSE) {
                        $this->tipoErro = 'E';
                        throw new Exception('Houve um erro no processamento de dados.');
                    }else{
                        $this->mensagemSucesso = "Registro inserido com sucesso.";
                    }
                }
            }
            
         } catch (Exception $e){
             $this->mensagemErro = $e->getMessage();
         }
         
         $this->cadastrar($this->idEquivalencia);
                  
     }
     
     /*
      * Método responsável produto por cadastrar
      * @return json
      */
     public function cadastrarEquivalenciaProduto(){
         try{
             
            $this->parametrosProdutos = $this->tratarPost();
            
            $obrigatoriosPreenchidos = TRUE;
            $erros = array();
            $retorno['erro'] = '';
            $retorno['alerta'] = '';
            
            //valida tipo do produto
            if (!isset($this->parametrosProdutos->eeitipo) || empty($this->parametrosProdutos->eeitipo)) {
                $obrigatoriosPreenchidos = FALSE;
                $erros[] = array(
                    'campo' => 'eeitipo',
                    'mensagem'=> utf8_encode('Campo obrigatório.')
                );
            }
            
            //valida produto
            if (!isset($this->parametrosProdutos->eeiprdoid) || $this->parametrosProdutos->eeiprdoid == '') {
                $obrigatoriosPreenchidos = FALSE;
                $erros[] = array(
                    'campo' => 'eeiprdoid',
                    'mensagem'=> utf8_encode('Campo obrigatório.')
                );
            }
            
            if (!$obrigatoriosPreenchidos) {
                $retorno['status'] = 0;
                $retorno['tipo'] = 'A';
                $retorno['mensagem'] = utf8_encode('Existem campos obrigatórios não preenchidos.');
                $retorno['erro'] = json_encode($erros);
                echo json_encode($retorno);exit;
                
                
            } else {
                //salvar
                $this->parametrosProdutos->eeiusuoid_cadastro = isset($_SESSION['usuario']['oid']) && $_SESSION['usuario']['oid'] != '' ? $_SESSION['usuario']['oid'] : '';
                $gravarProduto = $this->dao->gravarEquivalenciaProduto($this->parametrosProdutos);
                
                if ($gravarProduto === FALSE) {
                    $retorno['status'] = 0;
                    $retorno['tipo'] = 'E';
                    $retorno['mensagem'] = utf8_encode('Houve um erro no processamento de dados.');
                    echo json_encode($retorno);exit;
                }else{
                    $retorno['status'] = 1;
                    $retorno['tipo'] = 'S';
                    $retorno['mensagem'] = utf8_encode('Registro inserido com sucesso.');
                    echo json_encode($retorno);exit;
                }
            }
            
         } catch (Exception $e){
             
         }
         
     }
     
     
      /*
      * Método responsável por carregar grid de equipamento
      * @return array
      */
     public function carregarListagemEquipamento($idEquivalencia = ''){
         try{
             
             if (isset($idEquivalencia) && !empty($idEquivalencia)) {
                 $parametros->eeqoid = $idEquivalencia;
             }else{
                 $parametros = $this->tratarPost();
             }
             
             $this->dados = $this->dao->buscarProdutosEquivalencia($parametros->eeqoid);
             
             require_once _MODULEDIR_ ."/Cadastro/View/cad_equivalencia_equipamentos/cadastro_listagem.php";
         } catch (Exception $e){
             
             
         }
     }
     
     /*
      * Método responsável por copiar equivalência
      * @return json
      */
     public function copiarEquivalencia(){
         
         try{
                        
            $this->mostrarQuadroCopiaClasse = false;
             
            $this->parametros = $this->tratarPost();
            
            $obrigatoriosPreenchidos = true;
            $erros = array();
            
            //valida Modalidade do Contrato
            if (!isset($this->parametros->eqqmodalidade_copia) || empty($this->parametros->eqqmodalidade_copia)) {
                $this->mostrarQuadroCopiaClasse = true;
                $obrigatoriosPreenchidos = false;
                $erros[] = array(
                    'campo' => 'eqqmodalidade_copia',
                    'mensagem'=> utf8_encode('Campos obrigatório.')
                );
            }
            
            //valida classe do Contrato
            if (!isset($this->parametros->eeqeqcoid_copia) || empty($this->parametros->eeqeqcoid_copia)) {
                $this->mostrarQuadroCopiaClasse = true;
                $obrigatoriosPreenchidos = false;
                $erros[] = array(
                    'campo' => 'eeqeqcoid_copia',
                    'mensagem'=> utf8_encode('Campos obrigatório.')
                );
            }
            
            //valida tipo do Contrato
            if (!isset($this->parametros->eeqtpcoid_copia) || trim($this->parametros->eeqtpcoid_copia) == '') {
                $this->mostrarQuadroCopiaClasse = true;
                $obrigatoriosPreenchidos = false;
                $erros[] = array(
                    'campo' => 'eeqtpcoid_copia',
                    'mensagem'=> utf8_encode('Campos obrigatório.')
                );
            }
            
            if (!$obrigatoriosPreenchidos) {
                $this->mostrarQuadroCopiaClasse = true;
                $this->mensagemAlerta = 'Existem campos obrigatórios não preenchidos.';
                $this->erros = json_encode($erros);
                
            } else {
                //copiar classe
                                
                $filtros = new stdClass();
                $this->parametros->eeqoid_origem = '';
                $this->parametros->cd_usuario = $_SESSION['usuario']['oid'];

                $filtros->eqqmodalidade = $this->parametros->eqqmodalidade_copia;
                $filtros->eeqeqcoid = $this->parametros->eeqeqcoid_copia;
                $filtros->eeqtpcoid = $this->parametros->eeqtpcoid_copia;

                $pesquisaOrigem = $this->dao->pesquisar($filtros);

                if($pesquisaOrigem) {
                    $this->parametros->eeqoid_origem = $pesquisaOrigem[0]->id;
                }
                
                $produtosOrigem = $this->dao->buscarProdutosEquivalencia($pesquisaOrigem[0]->id);

                /**
                 * Se a origem não existir ou não possuir produtos vinculados
                 * não faz sentido copia-lá.
                 */                
                if(!$pesquisaOrigem || empty($produtosOrigem)) {
                    $this->mostrarQuadroCopiaClasse = true;
                    $this->mensagemAlerta = 'Não existem produtos vinculados a classe e tipo de contrato selecionados.';
                } else {
                
                    $clsseCopiada = $this->dao->copiarProdutos($this->parametros);

                    if ($clsseCopiada === false) {
                        throw new Exception('Houve um erro no processamento de dados.');
                    }

                    $this->mensagemSucesso = "Classe copiada com sucesso.";
                }
            }
             
         } catch (Exception $e){              
            $this->mensagemErro = $e->getMessage();
         }
        
         $this->cadastrar($this->parametros->eeqoid_destino);
         
     }
     
     /*
      * Método responsável por tratar post
      * @return Stdclass
      */
     public function tratarPost(){
         $parametros = new stdClass();
         if (isset($_POST) && !empty($_POST)) {
             foreach ($_POST as $key => $value) {
                 if(!is_array($_POST[$key])) {
                    $parametros->$key = isset($_POST[$key]) && trim($_POST[$key]) != ''  ? $_POST[$key] : '';
                 } else {
                     $parametros->$key = $_POST[$key];
                 }
             }
         }
         return $parametros;
     }
     
     /*
      * Método responsável por tratar post e guardar sessao para pesquisa
      * @return Stdclass
      */
     public function tratarParametrosPesquisa() {
        $parametros = new stdClass();
        
        $parametros->eqqmodalidade = '';
        $parametros->eeqeqcoid = '';
        $parametros->eeqtpcoid = '';
        $parametros->classes_sem_cadastro = '';
        $parametros->pesquisar = '';
        
        if (isset($_POST['classes_sem_cadastro'])) {
            
            $parametros->classes_sem_cadastro = $_POST['classes_sem_cadastro'];
            $_SESSION['pesquisa']['eqqmodalidade'] = '';
            $_SESSION['pesquisa']['eeqeqcoid'] = '';
            $_SESSION['pesquisa']['eeqtpcoid'] = '';
            
        }else{
            
            $_SESSION['pesquisa']['classes_sem_cadastro'] = '';
            if (isset($_POST['eqqmodalidade'])) {
                $parametros->eqqmodalidade = $_POST['eqqmodalidade'];
            } else if (isset($_SESSION['pesquisa']['eqqmodalidade'])) {
                $parametros->eqqmodalidade = $_SESSION['pesquisa']['eqqmodalidade'];
            }

            if (isset($_POST['eeqeqcoid'])) {
                $parametros->eeqeqcoid = $_POST['eeqeqcoid'];
            } else if (isset($_SESSION['pesquisa']['eeqeqcoid'])) {
                $parametros->eeqeqcoid = $_SESSION['pesquisa']['eeqeqcoid'];
            }

            if (isset($_POST['eeqtpcoid'])) {
                $parametros->eeqtpcoid = $_POST['eeqtpcoid'];
            } else if (isset($_SESSION['pesquisa']['eeqtpcoid'])) {
                $parametros->eeqtpcoid = $_SESSION['pesquisa']['eeqtpcoid'];
            }
        }
        
        if (isset($_POST['pesquisar'])) {
            $parametros->pesquisar = $_POST['pesquisar'];
        } else if (isset($_SESSION['pesquisa']['pesquisar'])) {
            $parametros->pesquisar = $_SESSION['pesquisa']['pesquisar'];
        }
 
        $_SESSION['pesquisa']['eqqmodalidade'] = $parametros->eqqmodalidade;
        $_SESSION['pesquisa']['eeqeqcoid'] = $parametros->eeqeqcoid;
        $_SESSION['pesquisa']['eeqtpcoid'] = $parametros->eeqtpcoid;
        $_SESSION['pesquisa']['classes_sem_cadastro'] = $parametros->classes_sem_cadastro;  
        $_SESSION['pesquisa']['pesquisar'] = $parametros->pesquisar; 
        
        return (object) $_SESSION['pesquisa'];
    }
     
     public function limparParametrosPesquisa(){
         $_SESSION['pesquisa']['eqqmodalidade'] = '';
         $_SESSION['pesquisa']['eeqeqcoid'] = '';
         $_SESSION['pesquisa']['eeqtpcoid'] = '';
         $_SESSION['pesquisa']['classes_sem_cadastro'] = '';
         $_SESSION['pesquisa']['pesquisar'] = '';
     }    
}

?>
