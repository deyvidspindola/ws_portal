<?php

/**
 * Classe ManCustosApagarDAO.
 * Camada de modelagem de dados.
 *
 * @package  Cadastro
 * @author   CÁSSIO VINÍCIUS LEGUIZAMON BUENO <cassio.bueno.ext@sascar.com.br>
 *
 */
class ManCustosApagarDAO {

	/** Conexão com o banco de dados */
	private $conn;

	/** Usuario logado */
	private $usarioLogado;

	const MENSAGEM_ERRO_PROCESSAMENTO = "Houve um erro no processamento dos dados.";

	public function __construct($conn) {

		//Seta a conexao na classe
        $this->conn = $conn;
        $this->usarioLogado = isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : '';

        //Se nao tiver nada na sessao assume usuario AUTOMATICO
        if(empty($this->usarioLogado)) {
            $this->usarioLogado = 2750;
        }
	}

	/**
	 * Método para realizar a pesquisa de varios registros
	 * @param stdClass $parametros Filtros da pesquisa
	 * @return array
	 * @throws ErrorException
	 */
	public function pesquisar(stdClass $parametros){

		$retorno = array();

		return $retorno;
	}

	/**
	 * Método para realizar a pesquisa de apenas um registro.
	 *
	 * @param int $id Identificador único do registro
	 * @return stdClass
	 * @throws ErrorException
	 */
	public function pesquisarPorID($id){	

		$retorno = new stdClass();
		
		$sql = "
		SELECT 
			apgoid, apgcoroid, apgforoid, apgdt_vencimento,apgvl_apagar_real, 
			apgno_notafiscal, apgobs, apgdt_agenda, apgdt_pgto, apgno_cheque,
			apgdt_exclusao, apgintoid, apgcntoid, apgfiloid, apgforma_pgto,
			apgdt_chq_compensacao, apgforma_recebimento,apgdt_autorizacao,
			apgusu_autorizacao, apgtecoid, apgocorrencia, apglinha_digitavel,
			apgcodigo_barras, apgdt_limite_desconto, apgtipo_docto, apgno_remessa,			
			apgtnfoid, apgentoid,			
			apgprevisao, apgautorizado, apgcod_novo, apgcod_antigo, apgplcoid,
			apgtctoid, apgforcoid, apgcfbbanco, 			
			apgftcoid, apgmbcooid,			
			cast(apgvl_apagar AS NUMERIC(15,2)) as apgvl_apagar2,
			cast(apgvl_pago AS NUMERIC(15,2)) as apgvl_pago2,
			cast(apgvl_inss AS NUMERIC(15,2)) as apgvl_inss2,
			cast(apgvl_desconto AS NUMERIC(15,2)) as apgvl_desconto2,
			cast(apgvl_multa AS NUMERIC(15,2)) as apgvl_multa2,
			cast(apgvl_juros AS NUMERIC(15,2)) as apgvl_juros2,
			apgvl_pago,
			cast(apgvl_ir AS NUMERIC(15,2)) as apgvl_ir2, 
			cast(apgvl_pis AS NUMERIC(15,2)) as apgvl_pis2, 
			cast(apgvl_iss AS NUMERIC(15,2)) as apgvl_iss2, 
			cast(apgvl_csll AS NUMERIC(15,2)) as apgvl_csll2, 
			cast(apgvl_cofins AS NUMERIC(15,2)) as apgvl_cofins2,			
			apgvl_apagar,			
			cast(apgvl_tarifa_bancaria AS NUMERIC(15,2)) as apgvl_tarifa_bancaria2,
			apgcod_ir, 
			cast(apgcsrf AS NUMERIC(15,2)) as apgcsrf2, 
			apgtipo_gasto, apgrproid,
			foroid, forfornecedor,			
			forendoid, forcntoid, forintoid, forplcoid, fordocto, foropt_simples,			
			coroid,
			cntoid, cntconta, cntno_centro,
			to_char(apgdt_vencimento,'dd/mm/yyyy') as dt_vencimento, 
			to_char(apgdt_limite_desconto,'dd/mm/yyyy') as dt_limite_desconto, 
			to_char(apgdt_entrada,'dd/mm/yyyy') as dt_entrada,
			apgdt_pagamento, apgcodigo_receita,
			to_char(apgdt_pagamento,'dd/mm/yyyy') as apgdt_pagamento2,
			to_char(apgperiodo_referencia,'mm/yyyy') as apgperiodo_referencia1,
			to_char(apgperiodo_referencia,'dd/mm/yyyy') as apgperiodo_referencia2,
			apgnumero_referencia,
			cast(apgvalor_receita_bruta AS NUMERIC(15,2)) as apgvalor_receita_bruta2,
			cast(apgpercentual_receita_bruta AS NUMERIC(15,2)) as apgpercentual_receita_bruta2,
			apgidentificador_fgts,
			apgidentificador_gps,
			apginscricao_estadual,
			apgcnpj_contribuinte,
			apgdivida_ativa,
			apgnum_parcela,
			cast(apgvalor_entidades AS NUMERIC(15,2)) as apgvalor_entidades2,
			(select forfornecedor FROM fornecedores WHERE fordocto = apgidentificador_gps and fordocto <> '0' and fordocto <> '' and fordt_exclusao IS NULL limit 1) as apgidentificador_gps_nome			
		FROM 
			apagar
			INNER JOIN fornecedores ON foroid = apgforoid
			LEFT JOIN conta_corrente ON coroid = apgcoroid
			LEFT JOIN centro_custo on cntoid = apgcntoid	
		WHERE 
			apgoid = " . intval( $id ); 		

		$rs = pg_query($this->conn,$sql);

		if (pg_num_rows($rs) > 0){
			$retorno = pg_fetch_object($rs);
		}

		return $retorno;
	}

	/**
	 * Responsável por retornar o nome do fornecedor para o tipo GPS
	 * @param  bigint $identificador CPF / CNPJ do registro	 
	 * @throws ErrorException
	 */		
	public function retornaNomeFornecedorGPS( $strIdentif ){

		$retorno = array();

		$sql = "SELECT distinct 
					forfornecedor,
					foroid,
					fordocto 
				FROM
					fornecedores
				WHERE
					fordocto = '" . $strIdentif . "'
					and fordocto <> '0'
					and fordocto <> '' AND
					fordt_exclusao IS NULL                 
				LIMIT 1";
		
		$query = pg_query($this->conn,$sql);
        while($linha = pg_fetch_object($query)){
                    
            $retorno = array(
                'id' 		=> $linha->foroid,
                'label' 	=> utf8_encode($linha->forfornecedor),
                'value' 	=> utf8_encode($linha->forfornecedor),
                'fordocto' 	=> utf8_encode($linha->fordocto)
            );
        }		
        echo json_encode($retorno);        
	}


	public function limpacampos($str){
		$str = str_replace(",", ".", str_replace(".", "", $str));
		$retorno = $str; /// 100;

		return $retorno;
	}

	public function limpacamposIdentificador($str){
		$str = str_replace(",", "", str_replace(".", "", $str));
		$str = str_replace("/", "", str_replace("-", "", $str));
		$retorno = $str; /// 100;

		return $retorno;
	}

	/**
	 * Responsável por atualizar os registros
	 * @param stdClass $dados Dados a serem gravados
	 * @return boolean
	 * @throws ErrorException
	 */
	public function atualizar(stdClass $dados){
		
		/*
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
		echo '<BR><BR>';
		*/
		$sql = "UPDATE apagar SET
			       	apgobs 						= '". $dados->apgobs ."',

			       	".(($dados->apgno_cheque != "" ) ? " apgno_cheque = '". $dados->apgno_cheque ."'," : " apgno_cheque = NULL, ")."
			       	".(($dados->apgforma_recebimento != "") ? " apgforma_recebimento = '". $dados->apgforma_recebimento ."'," : "apgforma_recebimento = NULL, "). " 			
			       	".(($_POST['apglinha_digitavel1'] != "" && $dados->apgforma_recebimento == '31' && $dados->apgtipo_docto == '05') ? "
			       	apglinha_digitavel 			= '".$_POST['apglinha_digitavel1']
			       									.$_POST['apglinha_digitavel2']
			       									.$_POST['apglinha_digitavel3']
			       									.$_POST['apglinha_digitavel4']
			       									.$_POST['apglinha_digitavel5']
					       							.$_POST['apglinha_digitavel6']
					       							.$_POST['apglinha_digitavel7']
					       							.$_POST['apglinha_digitavel8']. "', " : "") ."

					".(($_POST['apglinha_digitavel_conc1'] != "" && $dados->apgforma_recebimento == '31' && ($dados->apgtipo_docto == '09' || $dados->apgtipo_docto == '10' || $dados->apgtipo_docto == '11')) ? "
			       	apglinha_digitavel 			= '".$_POST['apglinha_digitavel_conc1']
			       									.$_POST['apglinha_digitavel_conc2']
			       									.$_POST['apglinha_digitavel_conc3']
			       									.$_POST['apglinha_digitavel_conc4']. "', " : "") . " 
					
					
			       	".(( ($_POST['apglinha_digitavel_conc1'] == "" || $_POST['apglinha_digitavel1'] == "" ) && $dados->apgforma_recebimento == "31" && ($dados->apgtipo_docto == "09" || $dados->apgtipo_docto == "10" || $dados->apgtipo_docto == "11" || $dados->apgtipo_docto == "05" )) ? " apgcodigo_barras = '".$dados->apgcodigo_barras."', " : " apgcodigo_barras = '', ") ."

			       	".(($_POST['apglinha_digitavel_conc1'] == "" && $_POST['apglinha_digitavel1'] == "") ? " apglinha_digitavel = '', " : "")."
					
					".(($_POST['apgplcoid'] != "") ? " apgplcoid = " . $_POST['apgplcoid'] .", " : "")."

			       	".(( $dados->apgtipo_docto != "") ? " apgtipo_docto = '".$dados->apgtipo_docto."'," : " apgtipo_docto = '', ")."
			       	apgvl_inss 					= '".$this->limpacampos($dados->apgvl_inss)."',				       	
			       	apgvl_desconto 				= '".$this->limpacampos($dados->apgvl_desconto)."', 
			       	apgvl_juros 				= '".$this->limpacampos($dados->apgvl_juros)."', 
			       	apgvl_multa 				= '".$this->limpacampos($dados->apgvl_multa)."', 
			       	apgvl_ir 					= '".$this->limpacampos($dados->apgvl_ir)."', 
			       	apgvl_pis 					= '".$this->limpacampos($dados->apgvl_pis)."', 
			       	apgvl_iss 					= '".$this->limpacampos($dados->apgvl_iss)."', 
			       	apgvl_csll 					= '".$this->limpacampos($dados->apgvl_csll)."', 
			       	apgvl_cofins 				= '".$this->limpacampos($dados->apgvl_cofins)."', 
			       	apgvl_tarifa_bancaria 		= '".$this->limpacampos($dados->apgvl_tarifa_bancaria)."', 
			       	
			       	".(($dados->apgcod_ir != "" ) ? "apgcod_ir = '".$dados->apgcod_ir."', " : "")."

			       	apgcsrf 					= '".$this->limpacampos($dados->apgcsrf)."',
			       	apgdt_pagamento 			= '".implode("-", array_reverse(explode('/', $dados->apgdt_pagamento)))."',					
			       	
			       	".(($dados->apgdt_vencimento != "") ? " apgdt_vencimento = '".implode("-", array_reverse(explode('/', $dados->apgdt_vencimento)))."'," : "" ) . "
			       	
			       	apgcodigo_receita 			= ".(($_POST["apgcodigo_receita"] != "") ? $_POST["apgcodigo_receita"] : "NULL") . ",

					apgperiodo_referencia 		= ".
							(($_POST["apgperiodo_referencia1"] != '') ? "'" . implode("-", array_reverse(explode('/', "01/".$_POST["apgperiodo_referencia1"]))) ."'" : 
								(($_POST["apgperiodo_referencia2"] != '') ? "'" . implode("-", array_reverse(explode('/', $_POST["apgperiodo_referencia2"]))) ."'" : "NULL" )) . "," ." 
					
					apgnumero_referencia 		= ".(($_POST["apgnumero_referencia"] != "") ? "'" . $_POST["apgnumero_referencia"] . "'" : "NULL") .",
					apgvalor_receita_bruta 		= ".(($_POST["apgvalor_receita_bruta"] != "") ? $this->limpacampos($_POST["apgvalor_receita_bruta"]) : "NULL" ) .",
					apgpercentual_receita_bruta = ".(($_POST["apgpercentual_receita_bruta"] != "") ? $this->limpacampos($_POST["apgpercentual_receita_bruta"]) : "NULL" ) . ",	
					apgidentificador_fgts 		= ".(($_POST["apgidentificador_fgts"] != "") ? "'". $_POST["apgidentificador_fgts"] ."'" : "NULL") .",
					apgidentificador_gps		= ".(($_POST["apgidentificador_gps"] != "") ? "'". $this->limpacamposIdentificador($_POST["apgidentificador_gps"]) . "'" : "NULL") .",
					apginscricao_estadual		= ".(($_POST["apginscricao_estadual"] != "") ? "'". $this->limpacamposIdentificador($_POST["apginscricao_estadual"]) ."'" : "NULL") .",
					apgcnpj_contribuinte		= ".(($_POST["apgcnpj_contribuinte"] != "") ? "'". $this->limpacamposIdentificador($_POST["apgcnpj_contribuinte"]) ."'" : "NULL") .",
					apgdivida_ativa 			= ".(($_POST["apgdivida_ativa"] != "") ? "'" . $_POST["apgdivida_ativa"] . "'" : "NULL" ) .",
					apgnum_parcela 				= ".(($_POST["apgnum_parcela"] != "") ? "'". $_POST["apgnum_parcela"] . "'" : "NULL" ).",
					apgvalor_entidades 			=  ".(($_POST["apgvalor_entidades"] != "") ? $this->limpacampos($_POST["apgvalor_entidades"]) : "NULL" ) ." 					
					".(($_POST["cmp_fornecedor"] != "" && intval($_POST["cmp_fornecedor"]) ) ? ", apgforoid = ". intval($_POST["cmp_fornecedor"]) : "") ."					
				 WHERE apgoid = '". $dados->apgoid ."'" ;				 

		/*
		echo '<hr />';
		echo '<pre>';
		print_r($sql); 
		echo '</pre>'; exit;
		*/
		$this->executarQuery($sql);


		$cd_usuario=$_SESSION[usuario][oid];
		$ip_address = $_SERVER['REMOTE_ADDR'];
	
		$escaped_sql = pg_escape_string($sql);
		$sql_log = "INSERT INTO contas_apagar_log_update (calcdusuario,calapgoid,calsql,calip_origem) VALUES ($cd_usuario,$dados->apgoid,'$escaped_sql','$ip_address')";
		$this->executarQuery($sql_log);

		return true;
	}

	/**
	 * Exclui (UPDATE) um registro da base de dados.
	 * @param int $id Identificador do registro
	 * @return boolean
	 * @throws ErrorException
	 */
	public function excluir(stdClass $dados){

		$sql = "SELECT apagar_d('".intval($dados->apgoid)."','".intval($this->usarioLogado)."')";		
		$this->executarQuery($sql);

		return true;
	}


    /** retorna todas as empresas */
	public function retornaTodasEmpresas(){
		
		$retorno = array();
		$sql = "SELECT tecoid, tecrazao
		        FROM tectran
		        WHERE tecexclusao IS NULL AND tecoid NOT IN (2,5)
		        ORDER BY tecrazao ASC ";
		        
		$rs = $this->executarQuery($sql);
		while($registro = pg_fetch_object($rs)){
		    $retorno[] = $registro;
		}

		return $retorno;
	}

	/**
	 * Retorna todos os tipos de documentos
	 */
	public function retornaTodosTiposDocumentos(){
		
		$retorno = array();
		$sql = "SELECT tnfoid, tnfdescricao 
				FROM tp_nota_fiscal 
				ORDER BY tnfdescricao ASC ";
		$rs = $this->executarQuery($sql);
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		return $retorno;
	}

	/**
	 * Retorna todos os tipos de contas a pagar
	 */
	public function retornaTodosTiposContasPagar(){
		
		$retorno = array();	
		$sql = "SELECT tctoid, tctdescricao 
				FROM tipo_ctpagar 
				ORDER BY tctdescricao ASC ";
		$rs = $this->executarQuery($sql);
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}
		return $retorno;
	}
	
	/**
	 * Retorna todos os tipos de contas a pagar
	 */
	public function retornaTodasContasContabeis($plctipo, $plctecoid){		

		$retorno = array();
		$sql = "SELECT plcoid, plcconta, plcdescricao
				FROM plano_contabil
				WHERE 
					plcexclusao IS NULL
					AND plctipo= ".$plctipo."
					AND plctecoid = ". $plctecoid . "
				ORDER BY plcconta ASC";			
		$rs = $this->executarQuery($sql);
		while($registro = pg_fetch_object($rs)){
			$retorno[] = $registro;
		}		
		return $retorno;
	}

	/**
	 * Retorna o Nome do Fornecedor (completar formulario)
	 */
	public function getNomeFornecedor($termo)
    {
        $retorno = array();
        $sql = "SELECT distinct 
                    foroid,
                    forfornecedor,
                    fordocto,
                    concat ( ' ', forfornecedor, ' - ', ' [ ', fordocto, ' ] ' ) as docto_nome
                FROM
                  fornecedores
                WHERE
                  fordt_exclusao IS NULL AND
                  to_ascii(forfornecedor,'LATIN1') ilike to_ascii('%{$termo}%', 'LATIN1')                  
                ORDER BY
                  forfornecedor ASC
                LIMIT 15";

        $query = pg_query($this->conn,$sql);
        while($linha = pg_fetch_object($query)){

            $retorno[] = array(
                'id' 		=> $linha->foroid,
                'label' 	=> utf8_encode($linha->docto_nome),
                'value' 	=> utf8_encode($linha->forfornecedor),
                'fordocto' 	=> utf8_encode($linha->fordocto),
            );
        }
        return $retorno;
    }

	/** Abre a transação */
	public function begin(){
		pg_query($this->conn, 'BEGIN');
	}

	/** Finaliza um transação */
	public function commit(){
		pg_query($this->conn, 'COMMIT');
	}

	/** Aborta uma transação */
	public function rollback(){
		pg_query($this->conn, 'ROLLBACK');
	}

	/** 
     * Submete uma query a execucao do SGBD
     * @param  [string] $query
     * @return [bool]
     */
	private function executarQuery($query) {

        if(!$rs = pg_query($this->conn, $query)) {
            throw new ErrorException(self::MENSAGEM_ERRO_PROCESSAMENTO);
        }

        return $rs;
    }

    /**
     * cria ponto de salvamento
     * @param  $nome [alias para o savepoint]
     */
    public function savePoint($nome){
        pg_query($this->conn, 'SAVEPOINT ' . $nome);
    }

     /**
     * Aborta ações dentro de um bloco de ponto de salvamento
     * @param  $nome [alias para do savepoint]
     */
    public function rollbackPoint($nome){
        pg_query($this->conn, 'ROLLBACK TO SAVEPOINT ' . $nome);
    }
}
?>
