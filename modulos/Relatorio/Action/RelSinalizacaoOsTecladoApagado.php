<?php

require(_MODULEDIR_ . "Relatorio/DAO/RelSinalizacaoOsTecladoApagadoDAO.php");



class RelSinalizacaoOsTecladoApagado{

	private $conngr;
	private $relatorioDao;
	private $classesCliente;
	private $situacaoOS;
	private $datainicial;
	private $datafinal;
	private $canceladoautomatico;
	private $cliente;
	private $relatorio;
	private $alerta;


	public function __construct()  {
		global $conn;
		global $dbstring_gerenciadoras2;

		$this->conngr = pg_connect( $dbstring_gerenciadoras2 );
		if( !$this->conngr)
		{
			$this->conngr = null;
		}

		$this->relatorioDao = new RelSinalizacaoOsTecladoApagadoDAO( $conn, $this->conngr );
		$this->relatorio =  array();
		$this->alerta = "";
	}

	public function __destruct ( )
	{
		pg_close( $this->conngr);
	}

	
	public function carregaClasse()
	{
		$classeselecionada =  $this->obterValorRequisicao( 'idclasse');
		if( $classeselecionada == "" )
		{
			$classeselecionada = "-1";
		}

		$this->classesCliente = array();
		$this->classesCliente["-1"] = array("descricao" => "", "selecionado" => (($classeselecionada == "-1") ? "1" : "0") );

		$listaClasses = array();
		try
		{
			$listaClasses = $this->relatorioDao->carregaClasses();
		}
		catch (Exception $e)
		{
			$listaClasses = array();
			$this->alerta .= $e->getMessage() . "<br>";
		}

		foreach ( $listaClasses as $classe )
		{
			$selecionar = ($classeselecionada == $classe['idclasse']) ? "1" : "0";
			$temp = array( "descricao" => $classe['descricaoclasse'], "selecionado" => $selecionar );
			$this->classesCliente[$classe['idclasse']] = $temp;
		}
	}

	public function carregaSitauacaoOS()
	{
		$situacaoselecionada =  $this->obterValorRequisicao( 'idsituacao');
		if( $situacaoselecionada == "" )
		{
			$situacaoselecionada = "-1";
		}

		$this->situacaoOS = array();
		$this->situacaoOS["-1"] = array("descricao" => "", "selecionado" => (($situacaoselecionada == "-1") ? "1" : "0") );

		$listaSituacao = array();
		try
		{
			$listaSituacao = $this->relatorioDao->carregaSituacaoOS();
		}
		catch (Exception $e)
		{
			$listaSituacao = array();
			$this->alerta .= $e->getMessage() . "<br>";
		}

		foreach ( $listaSituacao as $situacao )
		{
			$selecionar = ($situacaoselecionada == $situacao['idsituacao']) ? "1" : "0";
			$temp = array( "descricao" => $situacao['descricaosituacao'], "selecionado" => $selecionar );
			$this->situacaoOS[$situacao['idsituacao']] = $temp;
		}
	}

	public function carregaCancelado()
	{
		$cancelado =  $this->obterValorRequisicao( 'idcancelado');
		if( $cancelado == "" )
		{
			$cancelado = "-1";
		}

		$this->canceladoautomatico = array();
		$this->canceladoautomatico["-1"] = array("descricao" => "", "selecionado" => (($cancelado == "-1") ? "1" : "0") );
		$this->canceladoautomatico["1"] = array("descricao" => "Sim", "selecionado" => (($cancelado == "1") ? "1" : "0") );
		$this->canceladoautomatico["2"] = array("descricao" => "Não", "selecionado" => (($cancelado == "2") ? "1" : "0") );
	}

	public function carregaDatas()
	{
		$this->datafinal = $this->obterValorRequisicao( 'data_fim');
		if( $this->datafinal == "" )
		{
			$this->datafinal = date('d/m/Y');
		}

		$this->datainicial = $this->obterValorRequisicao( 'data_ini');
		if( $this->datainicial == "" )
		{
			$this->datainicial = $this->datafinal;
		}
	}

	public function carregaCliente()
	{
		$this->cliente = $this->obterValorRequisicao( 'cliente');
	}

	public function buscarDataUltimaMensagem($idVeiculo, $dataOrdem, $dataAgendamento, $idCliente)
	{
		if( $idVeiculo == null || $idVeiculo == "") {
			return "";
		}

		if( $dataOrdem == null || $dataOrdem == "") {
			return "";
		}

		if( $dataAgendamento == null || $dataAgendamento == "")
		{
			$dataAgendamento = date('d/m/Y');
		}

		if( $idCliente == null || $idCliente == "")
		{
			return "";
		}

		$resultado = $this->relatorioDao->buscarUltimaMensagem( $idVeiculo, $dataOrdem, $dataAgendamento, $idCliente);

		$dataMensagem = "";

		foreach ( $resultado as $datas )
		{
			$dataMensagem =  $datas['data_ultima'];
		}

		if( $dataMensagem == null || $dataMensagem == "" )
		{
			$dataMensagem = "";
		}

		return $dataMensagem;
	}

	public function carregaRelatorioOS()
	{
		$datainicial = $this->obterValorRequisicao( 'data_ini');
		if( $datainicial == "" )
		{
			$datainicial = null;
		}

		$datafinal = $this->obterValorRequisicao( 'data_fim');
		if( $datafinal == "" )
		{
			$datafinal = null;
		}

		$situacao = $this->obterValorRequisicao( 'idsituacao');
		if( ($situacao == "") || ($situacao == "-1"))
		{
			$situacao = null;
		}

		$cancelado = $this->obterValorRequisicao( 'idcancelado');
		if( ($cancelado == "") || ($cancelado == "-1"))
		{
			$cancelado = null;
		}
		else
		{
			$cancelado =  ($cancelado == "1")? true: false;
		}
		
 
		 
		
		$classe =  $this->obterValorRequisicao( 'idclasse');
		if( ($classe == "") || ($classe == "-1")  )
		{
			$classe = null;
		}

		$cliente = $this->obterValorRequisicao( 'cliente');
		if( $cliente == "" )
		{
			$cliente = null;
		}

		$result = array();
		try
		{
			$result =  $this->relatorioDao->buscarOS( $datainicial, $datafinal, $situacao, $classe, $cancelado, $cliente );
		}
		catch (Exception $e)
		{
			$result = array();
			$this->alerta .= $e->getMessage() . "<br>";
		}

		$this->relatorio = array();
		if( $result == false )
		{
			$this->alerta .= "Não há dados disponíveis para os filtros escolhidos<br>";
		}
		else
		{
			foreach ($result as $os) {
				$ultimaMaensagem = "";

				try {
					$ultimaMaensagem = $this->buscarDataUltimaMensagem($os['conveioid'], $os['data_ordem'], $os['data_ultimo_agendamento'], $os['clioid']);
				} catch (Exception $e) {
					$ultimaMaensagem = "";
					$this->alerta .= $e->getMessage() . "<br>";
				}

				$dataAgendamento = $os['data_ultimo_agendamento'];

				if (($dataAgendamento == null) || ($dataAgendamento == "")) {
					$dataAgendamento = 'Não agendado';
				}

				$temp = array();
				$temp['data'] = $dataAgendamento;
				$temp['cliente'] = $os['cliente'];
				$temp['classe'] = $os['classe_contrato'];
				$temp['status'] = $os['status_os'];
				$temp['placa'] = $os['placa_veiculo'];
				$temp['idcliente'] = $os['clioid'];
				$temp['datamensagem'] = $ultimaMaensagem;

				$this->relatorio[$os['numero_os']] = $temp;
			}
		}
	}

	public function carregaItensFiltro()
	{
		$this->carregaClasse();
		$this->carregaSitauacaoOS();
		$this->carregaCancelado();
		$this->carregaDatas();
		$this->carregaCliente();
	}

	public function  carregaPagina()
	{
		include (_MODULEDIR_ . 'Relatorio/View/rel_sinalizacao_os_teclado_apagado/index.php');
	}

	public function obterValorRequisicao( $nome )
	{
		$valor = "";
		
		 
		
		if( isset($_POST[$nome]) && $_POST[$nome] != '' )
		{
			$valor = $_POST[$nome];
		}
		else if( isset($_GET[$nome]) && $_GET[$nome] != '' )
		{
			$valor = $_GET[$nome];
		}

		return $valor;
	}

	public function exibir(){

		if( $this->obterValorRequisicao( "acao") == "pesquisar")
		{
			$this->carregaRelatorioOS( );
		}

		$this->carregaItensFiltro();

		$this->carregaPagina();
	}
}	

?>
