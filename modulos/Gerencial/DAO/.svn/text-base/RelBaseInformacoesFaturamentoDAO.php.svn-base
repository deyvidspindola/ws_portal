<?php


class RelBaseInformacoesFaturamentoDAO {
	private $conn;
	public function __construct($conn) {
		$this->conn = $conn;
	}
	public function MensagemLog($msg) {
		$hora_atual = date ( "H:i:s" );
		$data_processamento = date ( "Ymd" );
		$fp = fopen ( _SITEDIR_ . "faturamento/log_relatorio_base_de_informacoes", "a" );
		chmod ( _SITEDIR_ . "faturamento/log_relatorio_base_de_informacoes", 0777 );
		fputs ( $fp, "$hora_atual - $msg\n" );
		fclose ( $fp );
	}
	
	/**
	 * Retorna as informações dos relatorios cadastrado nas tabelas de parametrização
	 * 
	 * @return array
	 */
	public function retornaRelatorios() {
		$sql = "SELECT
				  valoid, 
				  valvalor
				FROM
					dominio
				INNER JOIN registro ON domoid = regdomoid
				 INNER JOIN valor ON valregoid = regoid
				 WHERE
					   valregoid = 26";
		
		if (! $res = pg_query ( $this->conn, $sql )) {
			throw new Exception ( 'Falha ao recuperar o nome dos relatórios nas tabelas de parametrização' . " " . $sql );
		} else {
			$this->MensagemLog ( "Recuperou o nome dos relatórios nas tabelas de parametrização" );
		}
		
		$relatorios;
		while ( $row = pg_fetch_object ( $res ) ) {
			$relatorios [] = array (
					'id' => $row->valoid,
					'valor' => $row->valvalor 
			);
		}
		
		return $relatorios;
	}
	
	/**
	 * Recupera as informações da tabela execucao_faturamento verificando se tem processo rodando
	 *
	 * @return array
	 */
	public function recuperarParametros($finalizado) {
		if (! $finalizado) {
			$filtro .= " AND eardt_termino IS NULL";
			$filtro .= " AND earstatus = true ";
			$filtro .= " AND eartipo_processo IN(26) ";
		} else {
			$filtro .= " AND eartipo_processo IN(26) ";
			$filtro .= " AND earstatus = false";
			$filtro .= " ORDER BY eardt_termino DESC";
		}
		
		$sql = "SELECT
		nm_usuario,
		usuemail,
		earoid serial,
		earusuoid,
		TO_CHAR(eardt_inicio, 'HH24:MI:SS') as inicio,
		TO_CHAR(eardt_termino, 'HH24:MI:SS') as termino,
		TO_CHAR(eardt_inicio, 'DD/MM/YYYY HH24:MI:SS') as data_inicio,
		TO_CHAR(eardt_termino, 'DD/MM/YYYY HH24:MI:SS') as data_termino,
		eartipo_processo,
		eardesc_status,
		earparametros,
		earnomearquivo
		FROM
		execucao_arquivo
		INNER JOIN usuarios on cd_usuario = earusuoid
		$filtro
		LIMIT 1";
		
		if (! $res = pg_query ( $this->conn, $sql )) {
			throw new Exception ( 'Falha ao recuperar parâmetros' . " " . $sql );
		} else {
			$this->MensagemLog ( "Recuperou parametros" );
		}
		
		return $res;
	}
	
	/**
	 * Função para iniciar o controle de geracao de relatórios da base de informação
	 * @param  $tipo
	 */
	public function preparaRelatorioBaseInformacao($usuoid,$tipo,$params="",$nomeArquivo=""){
		
		  	$sql = "INSERT INTO execucao_arquivo(earusuoid, eartipo_processo,earstatus, earparametros,earnomearquivo) 
    	VALUES ($usuoid, $tipo,true,'".$params."','".$nomeArquivo."')";
		  	
		  	if(!$res = pg_query($this->conn,$sql)) {
		  		throw new Exception('Falha ao preparar relatórios base da informação'  . " " . $sql);
		  	}else {
		  		$this->MensagemLog("Preparou relatórios base da informação");
		  	}
 	
	}
	
	 /**
     * Finaliza o processo de geração de relatórios da base de informação
     * 
     * @var $resultado
     */
    public function finalizarProcesso($resultado,$tipo){
    	
    	$sql = "UPDATE execucao_arquivo
    	SET  eardt_termino=NOW(), earstatus=false, eardesc_status='$resultado'
    	 WHERE eardt_termino is null AND eartipo_processo=$tipo;";

    	$rs = pg_query($this->conn, $sql);
    	
    	if (!$rs) {
    		throw new exception("Falha ao finalizar o processamento concorrente. Contate o administrador de sistemas.",1);
    		$this->MensagemLog("Falha ao finalizar o processamento concorrente. Contate o administrador de sistemas");
    	}
    	
    	
    	$this->MensagemLog("Finaliza o processo de geração relatórios base da informação");
    }
    
    
    /**
     * Retorna o caminho aonde estão os arquivos 
     * @throws Exception
     * @return array
     */
    public function getCaminhoServidor(){
    	 
    	$sql = "SELECT
    				valvalor
		    	FROM
		    		valor
    			WHERE
    				valoid = 57";
    	 
    	if(!$res = pg_query($this->conn, $sql)) {
    		throw new Exception('Falha ao recuperar caminho do servidor base detalhada'." ".$sql);
    	}else{
    		$this->MensagemLog("Recuperou caminho do servidor base detalhada");
    	}
    	 

    	while ($row = pg_fetch_object($res)) {
    		$caminho= $row->valvalor;
    	}
    	return $caminho;
    }
    
    public function retornoBaseDetalhadaNF($rel_dt_ini,$rel_dt_fim){
    	
    	$sql = "SELECT
			    	nota,
			    	serie,
			    	prazo,
			    	natureza,
			    	dt_emissao,
			    	codigo_do_cliente,
			    	cliente,
			    	tipo_cliente,
			    	documento,
			    	classe,
			    	grupo,
			    	tipocontrato,
			    	tipo,
			    	contrato,
			    	inicio_vigencia,
			    	grupo_item_faturado,
			    	item_faturado,
			    	valor_item,
			    	desconto,
			    	valor_liquido,
			    	dt_cancelamento_nf,
			    	motivo_cancelamento,
			    	numero_parcelas,
			    	cancelada_mes,
			    	contrato_prazo,
			    	origem,
			    	email_nf,
                    parcela,
                    total_parcelas,
                    exibe_parcela
    	      FROM
			    	base_nf_detalhada
			    	WHERE
			    	emissao between '$rel_dt_ini' and '$rel_dt_fim'";
   
    	$rs = pg_query($this->conn, $sql);
    	
    	if (!$rs) {
    		throw new Exception('Falha na pesquisa da base detalhada.');
    	}
    	
    	return $rs;
    	
    }
}