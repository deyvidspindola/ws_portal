<?php
require _MODULEDIR_.'Grafico/DAO/GrfAnaliseControleFalhasDAO.php';

/**
 * GrfAnaliseControleFalhas.php
 * 
 * Classe Action para o gráficos de análise de controle de falhas
 */
class GrfAnaliseControleFalhas {
	/**
     * Objeto DAO para tratamento dos dados
     * @var GrfAnaliseControleFalhasDAO 
     */
	private $dao;
	
    /**
     * Armazena uma determinada cor que será utilizada nos gráficos
     * @var string 
     */
	private $color = 0;
    
    /**
     * Armazena a data inicial e final para a pesquisa
     * @var array 
     */
	private $date  = array(
		'inicial' => null,
		'final'   => null
	);
    
    /**
     * Nome do arquivo de imagem do gráfico gerado
     * @var string  
     */
	private $image = "";
    
    /**
     * Tipo do gráfico a ser gerado
     * @var string 
     */
	private $type  = null;
    
    /**
     * Coleção dos tipos de graficos
     * @var array 
     */
	private $types = array(
		'0'                                  => 'Selecione',
		'EntradaLab'                         => 'Entrada Lab',
		'EntradaLabRetirada'                 => 'Entrada Lab Retirada',
		'SemDefeitoLab'                      => 'Sem Defeito Lab',
		'SemDefeitoLabRetirada'              => 'Sem Defeito Lab Retirada',
		'ComDefeitoLab'                      => 'Com Defeito Lab',
		'ComDefeitoLabRetirada'              => 'Com Defeito Lab Retirada',
		'SemAtualizacaoCampo'                => 'Sem Atualização Campo',
		'SemAtualizacaoCampoSemDefeitoLab'   => 'Sem Atualização Campo X Sem Defeito Lab',
		'ComDefeitoLabBaseInstalada'         => 'Com Defeito Lab X Base Instalada',
		'ComDefeitoLabRetiradaBaseInstalada' => 'Com Defeito Lab Retirada X Base Instalada',
		'DefeitoLabRvs'                      => 'Defeito Lab RVS',
		'MtbfLocacao'                        => 'MTBF (Locação)',
		'MtbfVenda'                          => 'MTBF (Venda)',
		'Aging'                              => 'Aging'
	);
	
	/**
	 * Construtor
	 */
	public function __construct() {
		global $conn;

		$this->dao = new GrfAnaliseControleFalhasDAO($conn);
	}
	
	/**
	 * Método principal
	 */
	public function index() {
		$tipo_grafico = $this->types;

		cabecalho();
		
		require _MODULEDIR_.'Grafico/View/grf_analise_controle_falhas/index.php';
	}
	
	/**
	 * Método de pesquisa
	 */
	public function pesquisar() {
		$retorno = array(
			'status'   => true,
			'dados'    => array(),
			'mensagem' => null
		);
		
		$tipo_grafico = isset( $_POST['tipo_grafico'] ) ? $_POST['tipo_grafico'] : null;
		$data_inicio  = isset( $_POST['data_inicio'] )  ? $_POST['data_inicio']  : null;
		$data_fim     = isset( $_POST['data_fim'] )     ? $_POST['data_fim']     : null;
		
		# LIMPA VARIÁVEIS
		unset($_POST);
		
		/*
		 * VERIFICAÇÕES
		 */
		if(empty($tipo_grafico)) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
				'campo'    => 'tipo_grafico',
				'mensagem' => utf8_encode('Campo obrigatório')
			);
		} elseif(!array_key_exists($tipo_grafico, $this->types)) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
				'campo'    => 'tipo_grafico',
				'mensagem' => utf8_encode('Tipo gráfico inválido')
			);
		}
		
		if(empty($data_inicio)) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
				'campo'    => 'data_inicio',
				'mensagem' => utf8_encode('Campo obrigatório')
			);
		} elseif(!$this->validarData($data_inicio)) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
				'campo'    => 'data_inicio',
				'mensagem' => utf8_encode('Data inicial inválida')
			);
		}
		
		if(empty($data_fim)) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
					'campo'    => 'data_fim',
					'mensagem' => utf8_encode('Campo obrigatório')
			);
		} elseif(!$this->validarData($data_fim)) {
			$retorno['status']  = false;
			$retorno['dados'][] = array(
					'campo'    => 'data_fim',
					'mensagem' => utf8_encode('Data final inválida')
			);
		}
		
		if($retorno['status']) {
			$data_inicio_tp = explode('/', $data_inicio);
			$data_fim_tp    = explode('/', $data_fim);
			
			$data_inicio_ts = strtotime($data_inicio_tp[2].'-'.$data_inicio_tp[1].'-'.$data_inicio_tp[0]);
			$data_fim_ts    = strtotime($data_fim_tp[2].'-'.$data_fim_tp[1].'-'.$data_fim_tp[0]);
			
			if(!$this->validarDataIntervalo($data_inicio, $data_fim)) {
				$retorno['status']   = false;
				$retorno['dados'][]  = array(
					'campo'    => 'data_inicio',
					'mensagem' => utf8_encode('Período inválido')
				);
				$retorno['dados'][]  = array(
					'campo'    => 'data_fim',
					'mensagem' => utf8_encode('Período inválido')
				);
				$retorno['mensagem'] = utf8_encode('O período da pesquisa não deve ultrapassar 3 meses.');
			}
		}
		
		if($retorno['status']) {
			try {
				set_time_limit(240);
				
				/*
				 * PREPARA O GRÁFICO E SEUS DADOS
				 */
				$funcao = "recuperar".$tipo_grafico;
				
				$retorno['dados'] = $this->dao->$funcao(array(
					'data_inicio' => $data_inicio,
					'data_fim'    => $data_fim
				));
				
				if(!empty($_SESSION['usuario'])) {
					$this->image = hash('md5', $_SESSION['usuario']['login']);
					
					$retorno['imagem'] = $this->image;
				}
				
				
				if($retorno['dados'] !== false) {
					$this->date = array(
						'inicial' => $data_inicio,
						'final'   => $data_fim
					);
					$this->type = $tipo_grafico;
					
					switch($tipo_grafico) {
						case "DefeitoLabRvs" :
							$retorno['dados'] = $this->prepararDadosRvs($retorno['dados']);
							break;
						case "MtbfLocacao" :
						case "MtbfVenda" :
							$retorno['dados'] = $this->prepararDadosMtbf($retorno['dados']);
							break;
						case "Aging" :
							$retorno['dados'] = $this->prepararDadosAging($retorno['dados']);
							break;
						default :
							$retorno['dados'] = $this->prepararDadosPadrao($retorno['dados']);
					}
				} else {
					$retorno['mensagem'] = utf8_encode('Nenhum resultado encontrado.');
				}
			} catch(Exception $e) {
				$retorno['status']   = false;
				$retorno['dados']    = array();
				$retorno['mensagem'] =  utf8_encode( $e->getMessage() );
			}
		}
		
		echo json_encode($retorno);
	}
	
	/**
	 * Salva o gráfico padrão num arquivo
	 * 
	 * @param array $registros
	 * @return boolean
	 */
	private function gerarGraficoPadrao($registros) {
		$sitedir = strpos($_SERVER['HTTP_HOST'], '10.20.12.') === false ? '' : _SITEDIR_.'/lib/php5-jpgraph/';
		
		require_once $sitedir.'jpgraph.php';
		require_once $sitedir.'jpgraph_bar.php';
		require_once $sitedir.'jpgraph_line.php';
		
		/*
		 * PERMISSÕES
		 */
		chmod(_SITEDIR_.'/images/grafico', 0777);
		
		/*
		 * VARIÁVEIS
		 */
		$grf_barras = array();
		$grf_linha  = array();
		
		$barras   = array();
		$projetos = array();
		
		$imagem = $this->image;
		$imagem = _SITEDIR_."/images/grafico/_AnaliseControleFalhas-$this->type-$imagem.jpg";
		
		foreach($registros as $mes => $detalhes) {
			$temp = array();
			
			foreach($detalhes['dados'] as $projeto => $quantidade) {
				$temp[] = $quantidade;
				
				if(!in_array($projeto, $projetos)) {
					$projetos[] = $projeto;
				}
			}
			
			if($mes == 'media') {
				$grf_linha = $temp;
			} else {
				$grf_barras[$detalhes['cor']] = $temp;
			}
		}
		
		/*
		 * GRÁFICO
		 */
		if(file_exists($imagem)) {
			chmod($imagem, 0777);
			
			unlink($imagem);
		}
		
		$jpgraph = new Graph(1100, 320, 'auto');
		$jpgraph->SetScale('textlin');
		$jpgraph->SetY2Scale('lin');
		$jpgraph->SetY2OrderBack(false);
		$jpgraph->SetBox(false);
		$jpgraph->SetMargin(50, 50, 20, 40);
		
		foreach($grf_barras as $cor => $barra) {
			$this->$cor = new BarPlot($barra);
			
			$barras[] = $this->$cor;
		}
		
		$barras = new GroupBarPlot($barras);
		
		$linha = new LinePlot($grf_linha);
		$linha->SetBarCenter();
		
		$jpgraph->Add($barras);
		$jpgraph->AddY2($linha);
		
		foreach($grf_barras as $cor => $barra) {
			$this->$cor->SetColor('#'.$cor);
			$this->$cor->SetFillColor('#'.$cor);
		}
		
		$linha->setColor('#'.$registros['media']['cor']);
		
		$jpgraph->xaxis->SetTickLabels($projetos);
		$jpgraph->yaxis->HideLine(false);
		$jpgraph->yaxis->HideTicks(false, false);
		$jpgraph->ygrid->SetFill(false);
		
		$jpgraph->y2grid->SetFill(false);
		
		$jpgraph->Stroke($imagem);
		
		return true;
	}
    
    /**
     * Salva o gráfico Aging em arquivo (nome do arquivo $this->image)
     * @param array $registros
     * @return boolean
     */
	private function gerarGraficoAging($registros) {

		$sitedir = strpos($_SERVER['HTTP_HOST'], '10.20.12.') === false ? '' : _SITEDIR_.'/lib/php5-jpgraph/';
		
		require_once $sitedir.'jpgraph.php';
		require_once $sitedir.'jpgraph_bar.php';
		
		/*
		 * PERMISSÕES
		 */
		chmod(_SITEDIR_.'/images/grafico', 0777);
		
		/*
		 * VARIÁVEIS
		 */
		$grf_barras = array();
		
		$barras   = array();
		$datas    = array();
		$projetos = array();
		$periodos = array();

		
		$imagem = $this->image;
		$imagem = _SITEDIR_."/images/grafico/_AnaliseControleFalhas-$this->type-$imagem.jpg";
        
        foreach($registros as $periodo => $detalhes) {
            
            if (!in_array($periodo, $periodos)) {
                $periodos[] = $periodo;
            }
            foreach($detalhes['dados'] as $projeto => $valores) {
                if (!is_array($grf_barras[$valores['cor']])){
                    $grf_barras[$valores['cor']] = array();
                }
                $grf_barras[$valores['cor']][] = $valores['percentual'];
            }
        }
        
		/*
		 * GRÁFICO
		 */
		if(file_exists($imagem)) {
			chmod($imagem, 0777);
			
			unlink($imagem);
		}
		
		//$jpgraph = new Graph(1500, 420, 'auto');
		$jpgraph = new Graph(1100, 420, 'auto');

		$jpgraph->SetScale('textlin');
		$jpgraph->SetBox(false);
		$jpgraph->SetMargin(50, 50, 20, 70);
        
		
		foreach($grf_barras as $cor => $barra) {
			$this->$cor = new BarPlot($barra);
			
			$barras[] = $this->$cor;
		}
		
		$barras = new GroupBarPlot($barras);
		
		$jpgraph->Add($barras);
		
		foreach($grf_barras as $cor => $barra) {
			$this->$cor->SetColor('#'.$cor);
			$this->$cor->SetFillColor('#'.$cor);
		}
		
		$jpgraph->xaxis->SetTickLabels($periodos);
		$jpgraph->yaxis->SetLabelFormat('%d%%');
        
        
		$jpgraph->yaxis->HideLine(false);
		$jpgraph->yaxis->HideTicks(false, false);
		$jpgraph->ygrid->SetFill(false);
		
		$jpgraph->Stroke($imagem);
		
		return true;
	}
	
    
	
	/**
	 * Salva o gráfico num arquivo
	 *
	 * @param array $registros
	 * @return boolean
	 */
	private function gerarGraficoMtbf($registros) {
		$sitedir = strpos($_SERVER['HTTP_HOST'], '10.20.12.') === false ? '' : _SITEDIR_.'/lib/php5-jpgraph/';
		
		require_once $sitedir.'jpgraph.php';
		require_once $sitedir.'jpgraph_bar.php';
		
		/*
		 * PERMISSÕES
		 */
		chmod(_SITEDIR_.'/images/grafico', 0777);
		
		/*
		 * VARIÁVEIS
		 */
		$grf_barras = array();
		
		$barras   = array();
		$datas    = array();
		$projetos = array();
		$meses    = array(
			1 => 'Janeiro',
			2 => 'Fevereiro',
			3 => 'Março',
			4 => 'Abril',
			5 => 'Maio',
			6 => 'Junho',
			7 => 'Julho',
			8 => 'Agosto',
			9 => 'Setembro',
			10 => 'Outubro',
			11 => 'Novembro',
			12 => 'Dezembro'
		);
		
		$imagem = $this->image;
		$imagem = _SITEDIR_."/images/grafico/_AnaliseControleFalhas-$this->type-$imagem.jpg";
		
		foreach($registros as $data => $detalhes) {
			$temp = array();
			
			foreach($detalhes['dados'] as $projeto => $valores) {
				$temp[]  = ($valores['qtd_dias'] > 0 && $valores['qtd_equipamento']) ? floor($valores['qtd_dias'] / $valores['qtd_equipamento']) : 0;
				
				if(!in_array($projeto, $projetos)) {
					$projetos[] = $projeto;
				}
			}
			
			$data = explode('-', $data);
			$data = "MTBF ".$meses[intval($data[1])]." $data[0]";
			
			if(!in_array($data, $datas)) {
				$datas[$detalhes['cor']] = $data;
			}
			
			$grf_barras[$detalhes['cor']] = $temp;
		}
		
		/*
		 * GRÁFICO
		 */
		if(file_exists($imagem)) {
			chmod($imagem, 0777);
			
			unlink($imagem);
		}
		
		$jpgraph = new Graph(1100, 420, 'auto');
		$jpgraph->SetScale('textlin');
		$jpgraph->SetBox(false);
		$jpgraph->SetMargin(50, 50, 20, 70);
		
		foreach($grf_barras as $cor => $barra) {
			$this->$cor = new BarPlot($barra);
			
			$barras[] = $this->$cor;
		}
		
        $barras = new GroupBarPlot($barras);
		
		$jpgraph->Add($barras);
		
		foreach($grf_barras as $cor => $barra) {
			$this->$cor->SetColor('#'.$cor);
			$this->$cor->SetFillColor('#'.$cor);
			$this->$cor->SetLegend($datas[$cor]);
			
			$this->$cor->value->SetFormat('%d');
			$this->$cor->value->Show();
			$this->$cor->value->SetColor('#000000');
		}
		
		$jpgraph->legend->SetColumns(3);
		$jpgraph->legend->SetFillColor('#ffffff');
		$jpgraph->legend->SetFrameWeight(1);
		$jpgraph->legend->SetPos(0.5, 0.933, 'center', 'center');
		
		$jpgraph->xaxis->SetTickLabels($projetos);
		$jpgraph->yaxis->HideLine(false);
		$jpgraph->yaxis->HideTicks(false, false);
		$jpgraph->ygrid->SetFill(false);
		
		$jpgraph->Stroke($imagem);
		
		return true;
	}
	
	/**
	 * Salva o gráfico num arquivo
	 * 
	 * @param array $registros
	 * @return boolean
	 */
	private function gerarGraficoRvs($registros) {
		$sitedir = strpos($_SERVER['HTTP_HOST'], '10.20.12.') === false ? '' : _SITEDIR_.'/lib/php5-jpgraph/';
		
		require_once $sitedir.'jpgraph.php';
		require_once $sitedir.'jpgraph_bar.php';
		
		/*
		 * PERMISSÕES
		 */
		chmod(_SITEDIR_.'/images/grafico', 0777);
		
		/*
		 * VARIÁVEIS
		 */
		$grf_barras = array();
		
		$barras   = array();
		$datas    = array();
		$meses    = array(
			1 => 'Janeiro',
			2 => 'Fevereiro',
			3 => 'Março',
			4 => 'Abril',
			5 => 'Maio',
			6 => 'Junho',
			7 => 'Julho',
			8 => 'Agosto',
			9 => 'Setembro',
			10 => 'Outubro',
			11 => 'Novembro',
			12 => 'Dezembro'
		);
		
		$imagem = $this->image;
		$imagem = _SITEDIR_."/images/grafico/_AnaliseControleFalhas-$this->type-$imagem.jpg";
		
		foreach($registros as $defeito => $detalhes) {
			$temp = array();
			
			foreach($detalhes['dados'] as $data => $valores) {
				$temp[]  = $valores['quantidade'];
				
				$data = explode('-', $data);
				$data = $meses[intval($data[1])]." $data[0]";
				
				if(!in_array($data, $datas)) {
					$datas[] = $data;
				}
			}
			
			$grf_barras[$detalhes['cor']] = $temp;
		}
		
		/*
		 * GRÁFICO
		 */
		if(file_exists($imagem)) {
			chmod($imagem, 0777);
			
			unlink($imagem);
		}
		
		$jpgraph = new Graph(1100, 320, 'auto');
		$jpgraph->SetScale('textlin');
		$jpgraph->SetBox(false);
		$jpgraph->SetMargin(50, 50, 20, 40);
		
		foreach($grf_barras as $cor => $barra) {
			$this->$cor = new BarPlot($barra);
			
			$barras[] = $this->$cor;
		}
		
		$barras = new GroupBarPlot($barras);
		
		$jpgraph->Add($barras);
		
		foreach($grf_barras as $cor => $barra) {
			$this->$cor->SetColor('#'.$cor);
			$this->$cor->SetFillColor('#'.$cor);
			
			$this->$cor->value->SetFormat('%d');
			$this->$cor->value->Show();
			$this->$cor->value->SetColor('#000000');
		}
		
		$jpgraph->xaxis->SetTickLabels($datas);
		$jpgraph->yaxis->HideLine(false);
		$jpgraph->yaxis->HideTicks(false, false);
		$jpgraph->ygrid->SetFill(false);
		
		$jpgraph->Stroke($imagem);
		
		return true;
	}
	
	/**
	 * Prepara os dados da pesquisa
	 * 
	 * @param array $registros
	 * @return array
	 */
	private function prepararDadosPadrao($registros) {
		$grafico  = array();
		$projetos = array();
		$precisao = 2;
		
		/*
		 * PRÉ-PREPARAÇÃO DOS DADOS DO GRÁFICO
		 */
		switch($this->type) {
			case "ComDefeitoLabBaseInstalada" :
			case "ComDefeitoLabRetiradaBaseInstalada" :
				foreach($registros as $indice => $dados) {
					$precisao = 3;
					
					$registros[$indice] = array(
						'nome_projeto'    => $dados['nome_projeto'],
						'dt_entrada_lab'  => $dados['dt_entrada_lab'],
						'qtd_equipamento' => number_format($dados['qtd_equipamento'] * 100 / $dados['qtd_total'], $precisao),
					);
				}
				break;
		}
		
		/*
		 * DEFINE OS PROJETOS ENCONTRADOS NA PESQUISA
		 */
		foreach($registros as $indice => $dados) {
			$projetos[$dados['nome_projeto']] = 0;
		}
		
		/*
		 * DEFINE OS MESES QUE FORAM PESQUISADOS
		 */
		$status = true;
		
		$mes_qtde = 0;
		
		$data_atual  = explode('/', $this->date['inicial']);
		$data_limite = explode('/', $this->date['final']);
		
		$mes_atual = intval($data_atual[1]);
		$ano_atual = intval($data_atual[2]);
		
		$mes_limite = intval($data_limite[1]);
		$ano_limite = intval($data_limite[2]);
		
		# LIMPA VARIÁVEIS
		unset($data_atual);
		unset($data_limite);
		
		while($status) {
			$mes_qtde++;
			
			$mes = $ano_atual.'-'.(strlen($mes_atual) == 1 ? '0' : '').$mes_atual;
			
			$grafico[$mes] = array(
				'cor'   => $this->definirCor(),
				'dados' => $projetos
			);
			
			$mes_atual++;
			
			if($mes_atual > 12) {
				$mes_atual = 1;
				$ano_atual++;
			}
			
			if(strtotime($ano_atual.'-'.$mes_atual.'-01') > strtotime($ano_limite.'-'.$mes_limite.'-01')) {
				$status = false;
			}
		}
		
		/*
		 * PREPARA OS DADOS QUE GERARÃO O GRÁFICO
		 */
		foreach($registros as $indice => $dados) {
			$grafico
				[$dados['dt_entrada_lab']]
				['dados']
				[$dados['nome_projeto']] = $dados['qtd_equipamento'];
			$projetos[$dados['nome_projeto']]+= $dados['qtd_equipamento'];
		}
		
		# LIMPA VARIÁVEIS
		unset($dados);
		unset($registros);
		
		# MÉDIA
		foreach($projetos as $projeto => $total) {
			$projetos[$projeto] = round($total / $mes_qtde, $precisao);
		}
		
		$grafico['media'] = array(
			'cor'   => $this->definirCor('media'),
			'dados' => $projetos
		);
		
		$this->gerarGraficoPadrao($grafico);
		
		return $grafico;
	}
	
	/**
	 * Prepara os dados da pesquisa
	 * 
	 * @param array $registros
	 * @return array
	 */
	private function prepararDadosAging($registros) {
		$grafico  = array();
		$projetos = array();
        $periodos = array();
		
		/*
		 * DEFINE OS PROJETOS ENCONTRADOS NA PESQUISA E OS ADICIONA AOS PERIODOS
		 */
		foreach($registros as $indice => $dados) {
			if ( !isset( $projetos[$dados['nome_projeto']] ) ) {
                $projetos[$dados['nome_projeto']]['cor'] = $this->definirCor();
                $projetos[$dados['nome_projeto']]['soma'] = 0;
            }
            //Soma os equipamentos do projeto
            $projetos[$dados['nome_projeto']]['soma'] += $dados['qtd_equipamento'];

            //Remove o index do periodo 
            $periodo = end(explode("-", $dados['qtd_dia']));
            //Adiciona o numero de equipamentos do projeto ao periodo
            $periodos[$periodo][$dados['nome_projeto']] += $dados['qtd_equipamento'];
		}
        
        /**
         * Prepara os dados para geração do gráfico
         */
        foreach($periodos as $periodo => $detalhes){
                        
            foreach($projetos as $projeto => $dados){
                //Total de equipamentos do projeto
                $qtdEquipamentosProjeto = $projetos[$projeto]['soma'];
                
                //Quantidade de equipamentos do periodo
                $qtdEquipamentos = isset($detalhes[$projeto]) ? $detalhes[$projeto] : 0;
                
                //Define a cor da barra, adiciona o quantidade de equipamentos e calcula o percentual
                $grafico[$periodo]['dados'][$projeto] = array(
                    'cor'                        => $dados['cor'],
                    'qtd_equipamentos'           => $qtdEquipamentos,
                    'percentual'                 => ($qtdEquipamentos > 0 && $qtdEquipamentosProjeto > 0) ? 
                                                        floor(($qtdEquipamentos / $qtdEquipamentosProjeto) * 100) : 0,
                    'total_equipamentos_projeto' => $qtdEquipamentosProjeto
                );
            }
        }
        
		# LIMPA VARIÁVEIS
		unset($detalhes);
		unset($registros);
        
        $this->gerarGraficoAging($grafico);
        
		return $grafico;
	}
	
	/**
	 * Prepara os dados da pesquisa do gráfico MTBF
	 * 
	 * @param array $registros
	 * @return array
	 */
	private function prepararDadosMtbf($registros) {
		$grafico  = array();
		$projetos = array();
		
		/*
		 * DEFINE OS PROJETOS ENCONTRADOS NA PESQUISA
		 */
		foreach($registros as $indice => $dados) {
			$projetos[$dados['nome_projeto']] = array(
				'qtd_dias'        => 0,
				'qtd_equipamento' => 0
			);
		}
		
		/*
		 * DEFINE OS MESES QUE FORAM PESQUISADOS
		 */
		$status = true;
		
		$mes_qtde = 0;
		
		$data_atual  = explode('/', $this->date['inicial']);
		$data_limite = explode('/', $this->date['final']);
		
		$mes_atual = intval($data_atual[1]);
		$ano_atual = intval($data_atual[2]);
		
		$mes_limite = intval($data_limite[1]);
		$ano_limite = intval($data_limite[2]);
		
		# LIMPA VARIÁVEIS
		unset($data_atual);
		unset($data_limite);
		
		while($status) {
			$mes_qtde++;
			
			$mes = $ano_atual.'-'.(strlen($mes_atual) == 1 ? '0' : '').$mes_atual;
			
			$grafico[$mes] = array(
				'cor'   => $this->definirCor(),
				'dados' => $projetos
			);
			
			$mes_atual++;
			
			if($mes_atual > 12) {
				$mes_atual = 1;
				$ano_atual++;
			}
			
			if(strtotime($ano_atual.'-'.$mes_atual.'-01') > strtotime($ano_limite.'-'.$mes_limite.'-01')) {
				$status = false;
			}
		}
		
		/*
		 * PREPARA OS DADOS QUE GERARÃO O GRÁFICO
		 */
		foreach($registros as $indice => $dados) {
			$grafico
				[$dados['dt_entrada_lab']]
				['dados']
				[$dados['nome_projeto']]
				['qtd_dias']+= $dados['qtd_dias'] * $dados['qtd_equipamento'];
			$grafico
				[$dados['dt_entrada_lab']]
				['dados']
				[$dados['nome_projeto']]
				['qtd_equipamento']+= $dados['qtd_equipamento'];
		}

        # LIMPA VARIÁVEIS
		unset($dados);
		unset($registros);
		
		$this->gerarGraficoMtbf($grafico);
		
		return $grafico;
	}
	
	/**
	 * Prepara os dados da pesquisa
	 * 
	 * @param array $registros
	 * @return array
	 */
	private function prepararDadosRvs($registros/*, $totais*/) {
		$grafico  = array();
		$meses    = array();
		$totais   = array();
		$precisao = 3;
		
		/*
		 * DEFINE OS MESES QUE FORAM PESQUISADOS
		 */
		$status = true;
		
		$mes_qtde = 0;
		
		$data_atual  = explode('/', $this->date['inicial']);
		$data_limite = explode('/', $this->date['final']);
		
		$mes_atual = intval($data_atual[1]);
		$ano_atual = intval($data_atual[2]);
		
		$mes_limite = intval($data_limite[1]);
		$ano_limite = intval($data_limite[2]);
		
		# LIMPA VARIÁVEIS
		unset($data_atual);
		unset($data_limite);
		
		while($status) {
			$mes_qtde++;
			
			$mes = $ano_atual.'-'.(strlen($mes_atual) == 1 ? '0' : '').$mes_atual;
						
			$meses[$mes] = array(
				'quantidade'  => 0,
				'porcentagem' => 0
			);
			
			$mes_atual++;
			
			if($mes_atual > 12) {
				$mes_atual = 1;
				$ano_atual++;
			}
			
			if(strtotime($ano_atual.'-'.$mes_atual.'-01') > strtotime($ano_limite.'-'.$mes_limite.'-01')) {
				$status = false;
			}
		}

		/*
		 * PREPARA OS TOTAIS PARA SEREM USADOS
		 */
		foreach($meses as $mes => $dados) {
			$totais[$mes] = 0;
		}
		
		foreach($registros as $indice => $dados) {
			$totais[$dados['dt_entrada_lab']]+= $dados['qtd_equipamento'];
		}
		
		/* *** MÉTODO ANTIGO *** */
		/* $temp = array();
		
		foreach($totais as $indice => $dados) {
			$temp[$dados['dt_entrada_lab']] = $dados['qtd_equipamento'];
		}
		
		$totais = $temp;
		
		# LIMPA VARIÁVEIS
		unset($temp); */
		
		/*
		 * REDEFINE QUAIS QUANTIDADES SERÃO USADAS
		 */
		$temp        = array();
		$defeitos    = array();
		$quantidades = array();
		$qtde_maior  = 0;
		
		foreach($meses as $mes => $dados) {
			$quantidades[$mes] = 0;
		}
		
		foreach($registros as $indice => $dados) {
			if(in_array($dados['desc_defeito'], $defeitos)) {
				$quantidades[$dados['dt_entrada_lab']]++;
				
				$temp[] = $dados;
			} elseif($quantidades[$dados['dt_entrada_lab']] < 3) {
				$quantidades[$dados['dt_entrada_lab']]++;
				
				$temp[]     = $dados;
				$defeitos[] = $dados['desc_defeito'];
				
				foreach($registros as $sub_indice => $sub_dados) {
					if($sub_dados['desc_defeito'] == $dados['desc_defeito']) {
						$temp[] = $sub_dados;
					}
				}
			}
		}
		
		$registros = $temp;
		
		# LIMPA VARIÁVEIS
		unset($defeitos);
		unset($quantidades);
		unset($temp);
		
		/*
		 * PREPARA OS DADOS QUE GERARÃO O GRÁFICO
		 */
		foreach($registros as $indice => $dados) {
			$dados['desc_defeito'] = utf8_encode($dados['desc_defeito']);
			
			# PROJETOS
			if(!array_key_exists($dados['desc_defeito'], $grafico)) {
				$grafico[$dados['desc_defeito']] = array(
					'cor'   => $this->definirCor(),
					'dados' => $meses
				);
			}
			
			$grafico
				[$dados['desc_defeito']]
				['dados']
				[$dados['dt_entrada_lab']]
				['quantidade'] = $dados['qtd_equipamento'];
			$grafico
				[$dados['desc_defeito']]
				['dados']
				[$dados['dt_entrada_lab']]
				['porcentagem'] = number_format($dados['qtd_equipamento'] * 100 / $totais[$dados['dt_entrada_lab']], $precisao);
		}
		
		$this->gerarGraficoRvs($grafico);
		
		return $grafico;
	}
	
	/**
	 * Define uma cor aleatoriamente
	 * 
	 * @return string
	 */
	private function definirCor($tipo = 'barra') {
		$codigos = array(
			'ffcc00', # LINHA
			'336600',
			'0066cc',
			'9966ff',
			'660000',
			'00cc99',
			'99cccc',
			'999900',
			'ff99cc',
			'996666',
			'996633'
		);
		$indice  = 0;
		
		/*
		 * DEFINE QUAL O "TIPO DE COR" DESEJADA
		 */
		if($tipo == 'barra') {
			$this->color++;
			
			$indice = $this->color;
		}
		
		return $codigos[$indice];
	}
	
	/**
	 * Valida datas no formato gregoriano
	 * 
	 * @param string $data
	 * @return boolean
	 */
	private function validarData($data) {
		$data_temp = explode('/', $data);
		
		if(count($data_temp) != 3) {
			return false;
		} elseif(!checkdate($data_temp[1], $data_temp[0], $data_temp[2])) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Valida o intervalo entre datas gregorianas
	 * 
	 * @param string $data_inicio
	 * @param string $data_fim
	 * @return boolean
	 */
	private function validarDataIntervalo($data_inicio, $data_fim) {
		$data_inicio = explode('/', $data_inicio);
		$data_fim    = explode('/', $data_fim);
		
		$data_inicio_ts = strtotime($data_inicio[2].'-'.$data_inicio[1].'-'.$data_inicio[0]);
		$data_fim_ts    = strtotime($data_fim[2].'-'.$data_fim[1].'-'.$data_fim[0]);
		
		if($data_inicio_ts > $data_fim_ts) {
			return false;
		}
		
		$data_inicio[1] = intval($data_inicio[1]);
		$data_fim[1]    = intval($data_fim[1]);
		
		if($data_inicio[2] != $data_fim[2]) {
			$data_fim[1]+= 12;
		}
		
		
        if($data_fim[1] - $data_inicio[1] > 3) {
			return false;
		}
		
		return true;
	}
	
}