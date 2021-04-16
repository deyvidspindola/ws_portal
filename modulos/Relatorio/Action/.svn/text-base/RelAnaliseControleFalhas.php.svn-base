<?php
require_once 'modulos/Relatorio/DAO/RelAnaliseControleFalhasDAO.php';

/**
 * 
 * @author Dyorg Almeida <dyorg.almeida@meta.com.br>
 * @since 08-02-2013
 * 
 */
class RelAnaliseControleFalhas {
	
	private $dao;
	
	public $msg;
	
	public $lista;
        
        public $linhas;
	
	public function __construct() {
		$this->dao = new RelAnaliseControleFalhasDAO();	
	}
	
	public function pesquisar() {
		
                $this->linhas = array();
                $value['data_entrada_lab'] = '';
                $value['data_saida_lab'] = '';
		
                $data_entrada_lab_inicial	= $_POST['data_entrada_lab_inicial'] 	? $_POST['data_entrada_lab_inicial'] 	: null;
                $data_entrada_lab_final		= $_POST['data_entrada_lab_final'] 	? $_POST['data_entrada_lab_final'] 	: null;
                $data_saida_lab_inicial 	= $_POST['data_saida_lab_inicial'] 	? $_POST['data_saida_lab_inicial'] 	: null;
                $data_saida_lab_final 		= $_POST['data_saida_lab_final'] 	? $_POST['data_saida_lab_final'] 	: null;
                
                try 
		{
			$this->lista = $this->dao->pesquisar();
                       
			if (empty($this->lista)) throw new Exception('Nenhum Resultado Encontrado');
                        
                        foreach ($this->lista as $chave => $value ){
                            
                            
                            $contrato = $value['contrato'];
                            if ( $data_entrada_lab_inicial && $data_entrada_lab_final ){
                                $contrato = null;
                            }
                            
                            $data_entrada_lab = $this->dao->buscaDataEntradaLab( $value["equoid"], $contrato, $data_entrada_lab_inicial, $data_entrada_lab_final );
                            $value['data_entrada_lab'] = $data_entrada_lab['dt_entrada_lab'];                            
                            
                            if ( !empty($value['data_entrada_lab']) ){
                                $data_saida_lab = $this->dao->buscaDataSaidaLab( $value["equoid"], $value['data_entrada_lab'], $data_saida_lab_inicial, $data_saida_lab_final );
                                $value['data_saida_lab'] = $data_saida_lab['dt_saida_lab']; 
                            }

                            $this->linhas[] = $value;
                        }
                        
		} 
		catch (Exception $e) 
		{
			$this->msg = $e->getMessage();
		}
	}
	
	public function gerarCSV_ajax() {
		
		ob_start();
		
                $data_entrada_lab_inicial	= $_POST['data_entrada_lab_inicial'] 	? $_POST['data_entrada_lab_inicial'] 	: null;
                $data_entrada_lab_final		= $_POST['data_entrada_lab_final'] 	? $_POST['data_entrada_lab_final'] 	: null;
                $data_saida_lab_inicial 	= $_POST['data_saida_lab_inicial'] 	? $_POST['data_saida_lab_inicial'] 	: null;
                $data_saida_lab_final 		= $_POST['data_saida_lab_final'] 	? $_POST['data_saida_lab_final'] 	: null;
                
		try 
		{
			$lista = $this->dao->pesquisar();
                        
                        if (empty($lista)) throw new RuntimeException('Nenhum Resultado Encontrado');
			
			$csv  = 'Serial;';
			$csv .= 'Nota Fiscal;';
			$csv .= 'Ordem;';
			$csv .= 'Dt Abertura;';
			$csv .= 'Dt Conclusão;';
			$csv .= 'Contrato;';
			$csv .= 'Modalidade;';
			$csv .= 'Ini Vigência;';
			$csv .= 'Defeito Alegado;';
			$csv .= 'Defeito Constatado;';
			$csv .= 'Causa;';
			$csv .= 'Ocorrência;';
			$csv .= 'Solução;';
			$csv .= 'Componente;';
			$csv .= 'Cliente;';
			$csv .= 'UF Cliente;';
			$csv .= 'Cidade;';
			$csv .= 'Obs;';
			$csv .= 'Classe Contrato;';
			$csv .= 'Tipo Veículo;';
			$csv .= 'Marca Veículo;';
			$csv .= 'Modelo Veículo;';
			$csv .= 'Ano Veículo;';
			$csv .= 'Chassi;';
			$csv .= 'Placa;';
			$csv .= 'Modelo Eqpto;';
			$csv .= 'Representante;';
			$csv .= 'Instalador;';
			$csv .= 'UF Repr;';
			$csv .= 'Motivo O.S.;';
			$csv .= 'Emissão NF;';
			$csv .= 'Entrada (Lote);';
			$csv .= 'Instalação;';
			$csv .= 'Retirada;';
			$csv .= '1º Instalação?;';
			$csv .= 'Defeito Lab.;';
			$csv .= 'Ação Lab.;';
			$csv .= 'Componente Afetado Lab.;';
			$csv .= 'Versão Eqpto;';
			$csv .= 'Data Entrada Lab.;';
			$csv .= 'Data Saída Lab.;';
			$csv .= 'Telefone;';
			$csv .= "Operadora\n";
			
			foreach ($lista as $linha) 
			{
                                $contrato = "";
                                
                                /*
                                 * Tratamento para o campo observação
                                 */
                                $obs = $linha['obs'];
                                $obs = preg_replace("/(\r\n|\n|\r|\t)/i", ' ', $obs);                                                                
                                $obs = str_replace(';', '', $obs);
                                
                                
				$csv .= $linha['serial'].';';
				$csv .= $linha['nota_fiscal'].';';
				$csv .= $linha['ordem'].';';
				$csv .= $linha['data_abertura'].';';
				$csv .= $linha['data_conclusao'].';';
				$csv .= $linha['contrato'].';';
				$csv .= $linha['modalidade'].';';
				$csv .= $linha['inicio_vigencia'].';';
				$csv .= $linha['defeito_alegado'].';';
				$csv .= $linha['defeito_constatado'].';';
				$csv .= $linha['causa'].';';
				$csv .= $linha['ocorrencia'].';';
				$csv .= $linha['solucao'].';';
				$csv .= $linha['componente'].';';
				$csv .= $linha['cliente'].';';
				$csv .= $linha['uf_cliente'].';';
				$csv .= $linha['cidade_cliente'].';';                            	
                                $csv .= $obs . ';';
                                $csv .= $linha['classe_contrato'].';';
				$csv .= $linha['tipo_veiculo'].';';
				$csv .= $linha['marca_veiculo'].';';
				$csv .= $linha['modelo_veiculo'].';';
				$csv .= $linha['ano_veiculo'].';';
				$csv .= $linha['chassi'].';';
				$csv .= $linha['placa'].';';
				$csv .= $linha['modelo_equipamento'].';';
				$csv .= $linha['representante'].';';
				$csv .= $linha['instalador'].';';
				$csv .= $linha['uf_representante'].';';
				$csv .= $linha['motivo_os'].';';
				$csv .= $linha['emissao_nf'].';';
				$csv .= $linha['data_entrada_lote'].';';
				$csv .= $linha['data_instalacao'].';';
				$csv .= $linha['data_retirada'].';';
				$csv .= $linha['primeira_instalacao'].';';
				$csv .= $linha['defeito_lab'].';';
				$csv .= $linha['acao_lab'].';';
				$csv .= $linha['componente_afetado_lab'].';';
				$csv .= $linha['versao_equipamento'].';';
                                
                                $contrato = $linha['contrato'];
                                if ( $data_entrada_lab_inicial && $data_entrada_lab_final ){
                                    $contrato = null;
                                }
                                
                                $data_entrada_lab = $this->dao->buscaDataEntradaLab( $linha["equoid"], $contrato, $data_entrada_lab_inicial, $data_entrada_lab_final );
                                $linha['data_entrada_lab'] = $data_entrada_lab['dt_entrada_lab']; 
                                if ( !empty($linha['data_entrada_lab']) ){
                                    $data_saida_lab = $this->dao->buscaDataSaidaLab( $linha["equoid"], $linha['data_entrada_lab'], $data_saida_lab_inicial, $data_saida_lab_final );
                                    $linha['data_saida_lab'] = $data_saida_lab['dt_saida_lab']; 
                                }
				$csv .= $linha['data_entrada_lab'].';';
				$csv .= $linha['data_saida_lab'].';';
                                $csv .= $linha['telefone'].';';
				$csv .= $linha['operadora']."\n";											
			}
			
			$file_name = "/var/www/docs_temporario/rel_analise_controle_falhas.csv";
			 
			file_put_contents($file_name, $csv);
			
			$msg = "Arquivo gerado com sucesso";
		} 
		catch (RuntimeException $e)
		{
			$erro = $e->getMessage();
		}
		catch (Exception $e) 
		{	
			$erro = 'Falha ao gerar o arquivo';	
		}
		
		ob_end_clean();
		echo json_encode(array(
			'msg' => utf8_encode($msg), 
			'erro' => utf8_encode($erro), 
			'file_name' => $file_name));
		exit();
		
	}
	
	public function listarDefeitoAlegados() {
		
		try 
		{
			$lista = $this->dao->listarDefeitosAlegados();
			if(empty($lista)) $lista = array();
			array_unshift($lista, array('id' => '', 'descricao' => 'Todos'));
			return $lista;	
		} 
		catch (Exception $e) 
		{
			return array(array('id' => '', 'descricao' => '- Falha na busca -'));
		}
	}
	
	public function listarDefeitoConstatados() {
	
		try 
		{
			$lista = $this->dao->listarDefeitosConstatados();
			if(empty($lista)) $lista = array();
			array_unshift($lista, array('id' => '', 'descricao' => 'Todos'));
			return $lista;	
		} 
		catch (Exception $e) 
		{
			return array(array('id' => '', 'descricao' => '- Falha na busca -'));
		}
	}
	
	public function listarDefeitosLab() {
	
		try 
		{
			$lista = $this->dao->listarDefeitosLab();
			if(empty($lista)) $lista = array();
			array_unshift($lista, array('id' => '', 'descricao' => 'Todos'));
			return $lista;	
		} 
		catch (Exception $e) 
		{
			return array(array('id' => '', 'descricao' => '- Falha na busca -'));
		}
	}
	
	public function listarAcaoLab() {
	
		try 
		{
			$lista = $this->dao->listarAcaoLab();
			if(empty($lista)) $lista = array();
			array_unshift($lista, array('id' => '', 'descricao' => 'Todos'));
			return $lista;	
		} 
		catch (Exception $e) 
		{
			return array(array('id' => '', 'descricao' => '- Falha na busca -'));
		}
	}
	
	public function listarComponentesAfetadosLab() {
	
		try 
		{
			$lista = $this->dao->listarComponentesAfetadosLab();
			if(empty($lista)) $lista = array();
			array_unshift($lista, array('id' => '', 'descricao' => 'Todos'));
			return $lista;	
		} 
		catch (Exception $e) 
		{
			return array(array('id' => '', 'descricao' => '- Falha na busca -'));
		}
	}
	
	public function listarModalidades() {
	
		$lista   = array();
		$lista[] = array('id' => '', 'descricao' => 'Selecione');		
		$lista[] = array('id' => 'L', 'descricao' => 'Locação');
		$lista[] = array('id' => 'V', 'descricao' => 'Revenda');		
		return $lista;
	}	
	
	public function listarModelosEquipamentos() {
	
		try 
		{
			$lista = $this->dao->listarModelosEquipamentos();
			if(empty($lista)) $lista = array();
			array_unshift($lista, array('id' => '', 'descricao' => 'Todos'));
			return $lista;	
		} 
		catch (Exception $e) 
		{
			return array(array('id' => '', 'descricao' => '- Falha na busca -'));
		}
	}
	
	public function listarTiposOS() {
	
		try 
		{
			$lista = $this->dao->listarTiposOS();
			if(empty($lista)) $lista = array();
			array_unshift($lista, array('id' => '', 'descricao' => 'AMBOS'));
			return $lista;	
		} 
		catch (Exception $e) 
		{
			return array(array('id' => '', 'descricao' => '- Falha na busca -'));
		}
	}
	
	public function listarVersoesEquipamentos() {
	
		try 
		{	
			$modelo = isset($_REQUEST['modelo_equipamento']) ? $_REQUEST['modelo_equipamento'] : null;
			
			if (is_numeric($modelo)) {
				$lista = $this->dao->listarVersoesEquipamentos($modelo);
				if(empty($lista)) $lista = array();
				array_unshift($lista, array('id' => '', 'descricao' => 'Todos'));
				
			} else {
				$lista = array(array('id' => '', 'descricao' => 'Selecione um modelo equip.'));
			}
			
			return $lista;	
		} 
		catch (Exception $e) 
		{
			return array(array('id' => '', 'descricao' => '- Falha na busca -'));
		}
	}
	
	public function listarVersoesEquipamentos_ajax() {
	
		$lista = $this->listarVersoesEquipamentos();
		
		foreach ($lista as &$item) {
			$item['descricao'] = utf8_decode($item['descricao']);
		}
		
		echo json_encode($lista);
		exit();
	}
}