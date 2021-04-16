<?php
class PrnPropostaSeguradoraDAO {
	private $conn;
	
	function __construct($conn){		
		$this->conn = $conn;		
	}
	
	public function propostaSegurado($id) {
		
		$mProposta_segurado = array();
		
		$sql_proposta_segurado = "
		SELECT
		prpsoid,
		prpsproposta,
		prpsprpssoid,
		prpstpcoid,
		rczdescricao,
		UPPER(prpssegurado) as prpssegurado,
		prpstipo_pessoa,
		prpsssexo,
		prpsscnpj_cpf,
		prpssrg,
		prpssinscr_estadual,
		TO_CHAR(prpssdt_fundacao,'DD/MM/YYYY') AS prpssdt_fundacao_br,
		TO_CHAR(prpssdt_nascimento,'DD/MM/YYYY') AS prpssdt_nascimento_br,
		prpsendereco,
		prpsnumero,
		prpsmunicipio,
		prpsbairro,
		prpscep,
		prpsuf,
		prpsddd,
		prpsfone,
        prpsddd2,
		prpsfone2,
        prpsddd3,
		prpsfone3,
		veimlooid,
		veino_proposta,
		prpsplaca,
		prpschassi,
		veino_ano,
		veicor,
		veino_renavan,
		veichave_geral,
		veisegoid,
		veiplaca,
		veicod_cia,
		prpscod_unid_emis,
		veioid,
		mlomcaoid,
		prpsapolice,
		prpsno_item,
		TO_CHAR(prpsinicio_vigencia,'DD/MM/YYYY') AS prpsinicio_vigencia,
		TO_CHAR(prpsfim_vigencia,'DD/MM/YYYY') AS prpsfim_vigencia,
		TO_CHAR(veiprazo_inst,'DD/MM/YYYY') AS veiprazo_inst,
		TO_CHAR(veinovo_prazo,'DD/MM/YYYY') AS veinovo_prazo,
		prpsobs_geral,
		prpsaoid,
		rczcd_zona,
		rczregcoid,
		regcoid,
		prpsprpsgoid,
		prpsscomplemento,
		prpsveioid,
		(SELECT corrnome FROM corretor WHERE  correxclusao IS NULL AND corroid = prpscorroid) AS corrnome,
		prpscorroid,
		trim(prpsemail_corretor) as prpsemail_corretor,
		prpsddd_corretor,
		prpsfone_corretor,
		prpssolicitante,
		to_char(prpsdt_solicitacao, 'dd/mm/YYYY')  AS prpsdt_solicitacao,
		coalesce(prpscombinacao,'') AS prpscombinacao,
		prpscorretor,
		prpsaditamento
		FROM
		veiculo
		LEFT JOIN proposta_seguradora ON veioid = prpsveioid
		LEFT JOIN proposta_seguradora_segurado ON prpsoid = prpssprpsoid
		LEFT JOIN proposta_seguradora_status ON prpsprpssoid = proposta_seguradora_status.prpssoid
		LEFT JOIN tipo_contrato ON tpcoid = prpstpcoid
		LEFT JOIN tipo_contrato_parametrizacao ON tcptpcoid = prpstpcoid
		LEFT JOIN modelo ON veimlooid = mlooid
		/*LEFT JOIN regiao_comercial_zona ON rczoid = tcprczoid*/
		LEFT JOIN regiao_comercial_zona ON rczoid = prpsrczoid
		LEFT JOIN regiao_comercial ON rczregcoid = regcoid
		LEFT JOIN proposta_seguradora_acao ON prpsaoid = prpsultima_acao
		WHERE
		prpsoid = '$id'
		
		";
		//echo $sql_proposta_segurado;
		//$query_proposta_seguradora = pg_query($conn,$sql_proposta_segurado);
		if(!$query_proposta_seguradora = pg_query($this->conn,$sql_proposta_segurado)){
			throw new Exception ("Erro ao numero da proposta nao foi passado. Tente Novamente.");
		}
		
		return $mProposta_segurado = pg_fetch_array($query_proposta_seguradora);
	}
	
	public function propostaSeguradoHistorico($id) {
		
		$id = ($id>0) ? $id : 0;
		
		$sql_proposta_seguradora_historico = "
												SELECT
												TO_CHAR(prpshdt_cadastro,'dd/mm/yyyy hh24:mi') as prpshdt_cadastro_br,
												prpshoid,
												prpshprpsoid,
												prpshdt_cadastro,
												prpshprpsaoid, 
												prpshtipo_documento,
												prpshentrada,
												prpshentrada, 
												prpshcontato,
												prpshobservacao, 
												prpshpsmtoid, 
												prpshprpssoid,
												UPPER(psmtdescricao) AS psmtdescricao,
												UPPER(prpsadescricao) AS prpsadescricao,
												UPPER(prpssdescricao) AS prpssdescricao,
												UPPER(ds_login) AS nm_usuario,
												psmtenvia_seguradora,
												(
												CASE 
													WHEN prpsatipo_solic = 'INS' THEN
														'INSTALAÇÃO'
													WHEN prpsatipo_solic = 'RET' THEN
														'RETIRADA'
													WHEN prpsatipo_solic = 'REV' THEN
														'REVISÃO'
													WHEN prpsatipo_solic = 'SUB' THEN
														'SUBSTITUIÇÃO'
													ELSE
														'INDEFINIDO'
												END
												) AS solicitacao,
												prpshcombinacao,
												prpshcontato,
												coalesce(prpshapolice, '0') AS prpshapolice,
												coalesce(prpshno_item, '0') AS prpshno_item
 												FROM proposta_seguradora_historico
											LEFT JOIN proposta_seguradora_motivo ON  psmtoid = prpshpsmtoid		
											LEFT JOIN proposta_seguradora_acao ON prpshprpsaoid = prpsaoid
											LEFT JOIN proposta_seguradora_status ON prpssoid = prpshprpssoid
											LEFT JOIN usuarios ON cd_usuario = prpshusuoid
												WHERE	
												prpshprpsoid = $id order by prpshoid desc
												";
			
			if(!$query_proposta_seguradora_historico = pg_query($this->conn,$sql_proposta_seguradora_historico)){
				throw new Exception ("Erro ao consultar histórico!");
			}
			
			return $query_proposta_seguradora_historico;
	}

	public function incluirProposta(
			$prpsproposta,
			$prpstpcoid,
			$prpsdt_solicitacao,
			$prpsprazo_inst,
			$prpsplaca,
			$prpschassi,
			$prpsapolice,
			$prpsno_item,
			$prpscorroid,
			$prpsemail_corretor,
			$prpscia,
			$prpscod_unid_emis,
			$prpsprpssoid,
			$prpsinicio_vigencia,
			$prpsfim_vigencia,
			$prpsobs_geral,
			$prpsscnpj_cpf,
			$prpstipo_pessoa,
			$prpssegurado,
			$prpsendereco,
			$prpsbairro,
			$prpsmunicipio,
			$prpsuf,
			$prpsddd,
			$prpsfone,
			$prpsnumero,
			$prpscep,
			$veisegoid,
			$prpsaditamento,
			$prpscombinacao,
			$prpssolicitante,
			$prpscorretor,
			$prpsssexo, 
			$prpssrg, 
			$prpssdt_nascimento,
			$prpsscomplemento,
			$veimlooid,
			$veicor,
			$veino_ano,
			$veino_renavan,
			$prpsddd_corretor,
			$prpsfone_corretor,
			$veinovo_prazo,
            $prpsddd2,
            $prpsfone2,
            $prpsddd3,
            $prpsfone3) {		
		
		$text = "\"".$prpsproposta."\"
				\"".$prpstpcoid."\"
				\"1\"
				\"".$prpsdt_solicitacao."\"
				\"".$prpsprazo_inst."\"
				\"".$prpsplaca."\"
				\"".$prpsplaca_seguradora."\"
				\"".$prpschassi."\"
				\"".$prpschassi_seguradora."\"
				\"".$prpsapolice."\"
				\"".$prpsno_item."\"
				\"".$prpscorroid."\"
				\"".$prpsemail_corretor."\"
                \"".$veisegoid."\"
                \"".$prpscod_unid_emis."\"
		        \"1\"
		        \"".$prpsinicio_vigencia."\"
		        \"".$prpsfim_vigencia."\"
                \"".$prpsobs_geral."\"
		        \"NULL\"
		        \"1\"
                \"".$prpsverificacao_manual."\"
                \"NULL\"
                \"".$prpsveioid."\"
                \"".$prpsscnpj_cpf."\"
		        \"".$prpstipo_pessoa."\"
                \"".$prpssegurado."\"
		        \"".$prpsemail."\"
                \"".$prpsendereco."\"
		        \"".$prpsbairro."\"
		        \"".$prpsmunicipio."\"
		        \"".$prpsuf."\"
                \"".$prpsddd."\"
                \"".$prpsfone."\"
                \"".$prpsnumero."\"
                \"".$prpscep."\"
		        \"".$veisegoid."\"
				\"".$tipodocumento."\"
		        \"".$prpsaditamento."\"
				\"".$prpscombinacao."\"
				\"NULL\"
				\"".$prpssolicitante."\"
				\"".$prpscorretor."\"
				\"".$prpsmarca."\"
				\"".$prpsmodelo."\"
                \"NULL\"
                \"NULL\"
                \"".$prpsddd2."\"
                \"".$prpsfone2."\"
                \"".$prpsddd3."\"
                \"".$prpsfone3."\"";
		
		$sql = "SELECT proposta_seguradora_i('".str_replace("'","",$text)."') AS prpsoid";
		//echo $sql;throw new Exception ("teste Erro ao inserir proposta",0);
		if(!$rs = pg_query($this->conn,$sql)){
			throw new Exception ("Erro ao inserir proposta",0);
		}
		
		$prpsoid = pg_fetch_result($rs,0,0);
		
		if ($prpsoid>0) {
			$sql2 = "UPDATE
						proposta_seguradora_segurado
					SET 
						prpsssexo = $prpsssexo, 
						prpssrg = $prpssrg, 
						prpssdt_nascimento = $prpssdt_nascimento,
						prpsscomplemento = '$prpsscomplemento'
					WHERE
						prpssprpsoid = $prpsoid";
			
			if(!$rs2 = pg_query($this->conn,$sql2)){
				throw new Exception ("Erro ao atualizar segurado",0);
			}
			
			$sql3 = "UPDATE
						proposta_seguradora
					SET
						prpscia = $prpscia,
						prpsddd_corretor = '$prpsddd_corretor',
						prpsfone_corretor = '$prpsfone_corretor',
						prpsaditamento = $prpsaditamento
					WHERE
						prpsoid = $prpsoid";
				
			if(!$rs3 = pg_query($this->conn,$sql3)){
				throw new Exception ("Erro ao atualizar proposta",0);
			}
			
			$sql4 = "SELECT
						prpsveioid
					FROM
						proposta_seguradora
					WHERE
						prpsoid = $prpsoid";
			if(!$rs4 = pg_query($this->conn,$sql4)){
				throw new Exception ("Erro ao selecionar veículo",0);
			}
			
			$prpsveioid = pg_fetch_result($rs4,0,0);
			
			if ($prpsveioid>0) {
				
				$sql5 = "UPDATE
							veiculo
						SET
							veimlooid = $veimlooid,
							veicor = $veicor,
							veino_ano = $veino_ano,
							veino_renavan = '$veino_renavan',
							veinovo_prazo = $veinovo_prazo,
							veicod_cia = $prpscia
						WHERE
							veioid = $prpsveioid";
				
				if(!$rs5 = pg_query($this->conn,$sql5)){
					throw new Exception ("Erro ao atualizar veiculo",0);
				}
			}
			
			$cd_usuario = $_SESSION["usuario"]["oid"];
			$cd_usuario = ($cd_usuario>0) ? $cd_usuario : 0;
			
			$sql6 = "INSERT INTO proposta_seguradora_historico
						(prpshprpssoid,prpshobservacao,prpshprpsoid,prpshusuoid)
					VALUES
						(1,'Proposta Inserida Manualmente',$prpsoid,$cd_usuario)";
			
			if(!$rs6 = pg_query($this->conn,$sql6)){
				throw new Exception ("Erro ao inserir histórico",0);
			}
			
			//verificação de contrato
			$sql7 = "SELECT
						veichassi, clino_cpf, clino_cgc, conno_tipo, condt_quarentena_seg, connumero, conequoid, conveioid
					FROM
						contrato
						INNER JOIN veiculo ON veioid = conveioid
						INNER JOIN clientes ON clioid = conclioid
					WHERE
						condt_exclusao IS NULL
						AND condt_quarentena_seg IS NOT NULL
						AND veichassi = '".str_replace("'","",$prpschassi)."'
						AND (clino_cpf = '".str_replace("'","",$prpsscnpj_cpf)."' OR clino_cgc = '".str_replace("'","",$prpsscnpj_cpf)."')
						AND conno_tipo = '".str_replace("'","",$prpstpcoid)."'";
			
			if(!$rs7 = pg_query($this->conn,$sql7)){
				throw new Exception ("Erro ao consultar contrato.",0);
			}

			if ( pg_num_rows($rs7)>0 ) {
				$connumero = pg_fetch_result($rs7,0,'connumero');
				$conequoid = pg_fetch_result($rs7,0,'conequoid');
				$conveioid = pg_fetch_result($rs7,0,'conveioid');
				
				$sql8 = "UPDATE
							contrato
						SET
							condt_quarentena_seg = NULL
						WHERE
							connumero = $connumero";
				
				if(!$rs8 = pg_query($this->conn,$sql8)){
					throw new Exception ("Erro ao atualizar data de quarentena",0);
				}				
				
				$sql9 = "INSERT INTO proposta_seguradora_historico
							(prpshprpssoid,prpshobservacao,prpshprpsoid,prpshusuoid)
						VALUES
							(2,'Contrato retirado de quarentena',$prpsoid,$cd_usuario)";
					
				if(!$rs9 = pg_query($this->conn,$sql9)) {
					throw new Exception ("Erro ao inserir histórico",0);
				}
				
				if ($conequoid>0) {
					$prpsultima_acao = 5;
				} else {
					$prpsultima_acao = 1;
				}
				
				$sql10 = "UPDATE
							proposta_seguradora
						SET
							prpsultima_acao = $prpsultima_acao,
							prpsprpssoid = 2,
							prpscombinacao = 'II'						
						WHERE
							prpsoid = $prpsoid";
					
				if(!$rs10 = pg_query($this->conn,$sql10)){
					throw new Exception ("Erro ao atualizar status",0);
				}
				
				$sql11 = "UPDATE
							veiculo
						SET
							veino_proposta = '".str_replace("'","",$prpsproposta)."',
							veiapolice = '".str_replace("'","",$prpsapolice)."',
							veino_item = '".str_replace("'","",$prpsno_item)."'
						WHERE
							veioid = $conveioid";
				
				if(!$rs11 = pg_query($this->conn,$sql11)){
					throw new Exception ("Erro ao atualizar veiculo",0);
				}				
				
			} else {
				$sql12 = "UPDATE
							proposta_seguradora
						SET
							prpsultima_acao = 1,
							prpsprpssoid = 1,
							prpscombinacao = 'II'
						WHERE
							prpsoid = $prpsoid";
					
				if(!$rs12 = pg_query($this->conn,$sql12)){
					throw new Exception ("Erro ao atualizar status",0);
				}
			}
			
		}
		
		return array(
				"feedback"	=> "Proposta incluída com sucesso!",
				"prpsoid"	=> $prpsoid
				);
	}
	
	public function getTiposContrato() {
		$return = array();
		
		$sql = "SELECT
					tpcoid, tpcdescricao
				FROM 
					tipo_contrato  
				ORDER BY
					tpcdescricao";
		if(!$rs = pg_query($this->conn,$sql)){
			throw new Exception ("Erro ao consultar tipos de contrato");
		}
		
		while ($linha = pg_fetch_array($rs)) {
			$return[] = $linha;
		}

		return $return;
	}
	
	public function existeProposta($prpsproposta,$prpstpcoid) {
		$return = false;
		
		$prpsproposta = ($prpsproposta>0) ? $prpsproposta : 0;
		$prpstpcoid = ($prpstpcoid>0) ? $prpstpcoid : 0;
		
		$sql = "SELECT 
					prpsproposta
				FROM
					proposta_seguradora 
				WHERE
					prpsproposta=$prpsproposta 
					AND prpstpcoid=$prpstpcoid";
		if(!$rs = pg_query($this->conn,$sql)){
			throw new Exception ("Erro ao consultar existência de proposta");
		}
		
		if (pg_num_rows($rs)>0) {
			$return = true;
		}

		return $return;
	}
	
	public function delQuarentena($connumero,$proposta) {

		$connumero = ($connumero>0) ? $connumero : 0;
	
		$sql = "UPDATE
					contrato
				SET
					condt_quarentena_seg=NULL
				WHERE
				connumero=$connumero";
		
		if(!$rs = pg_query($this->conn,$sql)){
			throw new Exception ("Erro ao remover quarentena");
		}
		
		$sql2 = "SELECT
					prpsoid
				FROM
					proposta_seguradora
				WHERE
					prpsproposta=$proposta";
		
		if(!$rs2 = pg_query($this->conn,$sql2)){
			throw new Exception ("Erro consultar proposta");
		}
		
		$prpsoid = pg_fetch_result($rs2,0,0);
		
		if ($prpsoid>0) {
		
			$sql12 = "UPDATE
						proposta_seguradora
					SET
						prpsprpssoid = 2
					WHERE
						prpsoid = $prpsoid";
				
			if(!$rs12 = pg_query($this->conn,$sql12)){
				throw new Exception ("Erro ao atualizar status",0);
			}
			
			$cd_usuario = $_SESSION["usuario"]["oid"];
			$cd_usuario = ($cd_usuario>0) ? $cd_usuario : 0;
			
			$sql9 = "INSERT INTO proposta_seguradora_historico
						(prpshprpssoid,prpshobservacao,prpshprpsoid,prpshusuoid)
					VALUES
						(2,'Contrato retirado de quarentena',$prpsoid,$cd_usuario)";
				
			if(!$rs9 = pg_query($this->conn,$sql9)) {
				throw new Exception ("Erro ao inserir histórico",0);
			}
		
			return true;
		} else {
			return false;
		}
	}
	
	public function incQuarentena($connumero,$proposta,$contrato_tipo) {
	
		$retorno = array(
				"incluiu"			=> 0,
				"data_quarentena"	=> 0
		);
		
		$connumero = ($connumero>0) ? $connumero : 0;
		$proposta = ($proposta>0) ? $proposta : 0;
		$contrato_tipo = ($contrato_tipo>0) ? $contrato_tipo : 0;
		
		$sql0 = "SELECT 
					tcpdias_quarentena
				FROM
					tipo_contrato_parametrizacao
				WHERE
					tcptpcoid=$contrato_tipo";
		
		if(!$rs0 = pg_query($this->conn,$sql0)){
			throw new Exception ("Erro ao consultar parametros");
		}
		
		if(pg_num_rows($rs0) > 0){
			$tcpdias_quarentena = pg_fetch_result($rs0,0,0);
		}
		$tcpdias_quarentena = ($tcpdias_quarentena>0) ? $tcpdias_quarentena : 0;
	
		$sql1 = "UPDATE
					contrato
				SET
					condt_quarentena_seg=now()::date+$tcpdias_quarentena
				WHERE
				connumero=$connumero";
	
		if(!$rs1 = pg_query($this->conn,$sql1)){
			throw new Exception ("Erro ao inserir quarentena");
		}
				
		$sql2 = "SELECT
					to_char(condt_quarentena_seg,  'DD/MM/YYYY' ) AS condt_quarentena_seg
				FROM
					contrato
				WHERE
					connumero=$connumero";
		
		if(!$rs2 = pg_query($this->conn,$sql2)){
			throw new Exception ("Erro ao consultar quarentena");
		}
		
		if(pg_num_rows($rs2) > 0){
			$condt_quarentena_seg = pg_fetch_result($rs2,0,'condt_quarentena_seg');
		}
		
		$cd_usuario = $_SESSION["usuario"]["oid"];
		$cd_usuario = ($cd_usuario>0) ? $cd_usuario : 0;		
		$sql15 = "SELECT historico_termo_i($connumero, $cd_usuario, 'Quarentena até $condt_quarentena_seg');";		
		if(!$rs15 = pg_query($this->conn,$sql15)){
			throw new Exception ("Erro ao inserir histórico do termo");
		}
		
		$sql21 = "SELECT
					prpsoid
				FROM
					proposta_seguradora
				WHERE
					prpsproposta=$proposta";
		
		if(!$rs21 = pg_query($this->conn,$sql21)){
			throw new Exception ("Erro consultar proposta");
		}
		
		if(pg_num_rows($rs21) > 0){
			$prpsoid = pg_fetch_result($rs21,0,0);
		}
		
		if ($prpsoid>0) {
				
			$sql10 = "UPDATE
						proposta_seguradora
					SET
						prpsultima_acao = 7,
						prpsprpssoid = 6
					WHERE
						prpsoid = $prpsoid";
				
			if(!$rs10 = pg_query($this->conn,$sql10)){
				throw new Exception ("Erro ao atualizar status",0);
			}	

			$sql91 = "INSERT INTO proposta_seguradora_historico
						(prpshprpssoid,prpshobservacao,prpshprpsoid,prpshusuoid)
					VALUES
						(6,'O contrato nº$connumero vinculado a proposta nº$proposta foi colocado em quarentena manualmente',$prpsoid,$cd_usuario)";
			
			if(!$rs91 = pg_query($this->conn,$sql91)) {
				throw new Exception ("Erro ao inserir histórico",0);
			}
		}
		
		
	
		$retorno = array(
				"incluiu"			=> 1,
				"data_quarentena"	=> "$condt_quarentena_seg"
		);
		
		return $retorno;
	}
    
    public function getTipoContrato(){
        $sql = "SELECT 
                    tpcoid,
                    tpcdescricao 
                FROM 
                    tipo_contrato
                WHERE
                    tpcprocesso_unificado_seg IS TRUE
                AND
                    tpcoid IN (883, 884, 919)
                ORDER BY
                    tpcdescricao;";
        
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        while($rs = pg_fetch_array($sql)){
            $result[] = array("valor" => $rs["tpcoid"], "desc" => $rs["tpcdescricao"]);
        }
        
        return $result;
    }
    
    public function getResultadoPesquisarArquivo($data_inicial, $data_final, $tipo_arquivo, $tipo_contrato, $status){
        $sql = "SELECT 
                    prpsqnome_arquivo as arquivo, 
                    to_char(prpsqdt_cadastro,'dd/mm/yyyy hh24:mi') as data,
                    (SELECT nm_usuario FROM usuarios WHERE prpsusuoid = cd_usuario) as usuario,
                    (CASE when prpsqstatus='P' THEN 'Processado' ELSE 'Nao Processado' END) as status,
                    (SELECT tpcdescricao FROM tipo_contrato WHERE prpsqtpcoid = tpcoid) as tipo_contrato,
                    prpsqorigem as origem,
                    prpsqcaminho as caminho
                FROM 
                    proposta_seguradora_arquivo
                WHERE
                    prpsqdt_exclusao IS NULL ";
                    
        if($data_inicial != "" && $data_final != ""){
            $sql .= "AND
                        prpsqdt_cadastro BETWEEN '$data_inicial 00:00:00' AND '$data_final 23:59:59' ";
        }
        
        if($tipo_arquivo != ""){
            if($tipo_arquivo == 1){
                //Processado
                $sql .= "AND
                            prpsqcaminho NOT ILIKE '%RETORNO%' ";
            } elseif($tipo_arquivo == 2){
                //Não Processado
                $sql .= "AND
                            prpsqcaminho ILIKE '%RETORNO%' ";
            }            
        }
        
        if($tipo_contrato != ""){
            $sql .= "AND
                        prpsqtpcoid = $tipo_contrato ";
        }
        
        if($status){
            $sql .= "AND
                        prpsqstatus = '$status' ";
        }
        
        $sql .= "ORDER BY
                    prpsqdt_cadastro DESC;";
                    
        $sql    = pg_query($this->conn, $sql);
        $result = null;
        
        while($rs = pg_fetch_array($sql)){
            $result[] = array("arquivo" => $rs["arquivo"], "data" => $rs["data"], "usuario" => $rs["usuario"],
                              "status" => $rs["status"], "tipo_contrato" => $rs["tipo_contrato"], "origem" => $rs["origem"],
                              "caminho" => $rs["caminho"]);
        }
        
        return $result;
    }
}
?>