<?php
class FinParametrosComissaoIndicadorDAO
{
	private $conn;
	
	public function FinParametrosComissaoIndicadorDAO()
	{
	
		global $conn;
		$this->conn = $conn;
	}
	
	public function getClassesEquipamentos() 
	{
		$sql = "
				SELECT
					eqcoid AS value,
					eqcdescricao AS text
				FROM
					equipamento_classe
				WHERE
					eqcinativo IS NULL
				ORDER BY
					eqcdescricao";

		$arr = array();
		$res = pg_query($this->conn,$sql);
		while ($linha=pg_fetch_array($res)) $arr[] = $linha;
		
		return $arr;
	}
	
	public function getParametrosClasse($pcieqcoid) 
	{
		$arr1 = array();
		
		if ($pcieqcoid > 0) {
			$sql = "
					SELECT
						pcioid,
						pciitem_comissao,
						pcivl_comissao,
						pcivl_perc_comissao,
						pcitipo_comissao,
						pcivl_minimo_comissao,
						pcivl_maximo_comissao
					FROM
						parametros_comissao_indicador
					WHERE
						pcieqcoid=$pcieqcoid
					LIMIT 1";	
			
			$res = pg_query($this->conn,$sql);
			//$arr1 = pg_fetch_array($res);
			$arr1 = array();
			$arr1['pcioid']					= pg_fetch_result($res,0,0);
			$arr1['pciitem_comissao']		= pg_fetch_result($res,0,1);
			$arr1['pcivl_comissao']			= $this->formatMoeda(pg_fetch_result($res,0,2));
			$arr1['pcivl_perc_comissao']	= $this->formatMoeda(pg_fetch_result($res,0,3));
			$arr1['pcitipo_comissao']		= pg_fetch_result($res,0,4);
			$arr1['pcivl_minimo_comissao']	= $this->formatMoeda(pg_fetch_result($res,0,5));
			$arr1['pcivl_maximo_comissao']	= $this->formatMoeda(pg_fetch_result($res,0,6));

			if (pg_num_rows($res)>0) {
				$arr2 = array();
				
				$sqlHist = "
						SELECT
							pcihpcioid,
							pcihusuoid,
							pcihobs,
							pcihdt_historico
						FROM
							parametros_comissao_indicador_historico
						WHERE
							pcihpcioid=".$arr1['pcioid'];
				
				$resHist = pg_query($this->conn,$sqlHist);
				while ($linha = pg_fetch_array($resHist)) $arr2[] = $linha;
				for ($i=0;$i<count($arr2);$i++) {
					
					$arr2[$i]['pcihobs'] = utf8_encode($arr2[$i]['pcihobs']);
					$arr2[$i]['pcihobs'] = nl2br($arr2[$i]['pcihobs']);
					
					$cd_usuario = $arr2[$i]['pcihusuoid'];
					$cd_usuario = ($cd_usuario) ? $cd_usuario : 0;					
					$sqlUsuario = "SELECT nm_usuario FROM usuarios WHERE cd_usuario=$cd_usuario";
					$resUsuario = pg_query($this->conn,$sqlUsuario);
					$arr2[$i]['pcihusuoid'] = pg_fetch_result($resUsuario,0,0);
					
					$arr2[$i]['pcihdt_historico'] = date("d/m/Y",strtotime($arr2[$i]['pcihdt_historico']));
					
				}
				$arr1['relatorio'] = json_encode($arr2);
			}
		}
		
		return $arr1;
	}
	
	public function formatMoeda($valor) {
		$valor = $valor*1;	
		$mascarado = number_format($valor,2, ',', '.');
		return $mascarado;
	}
	
	public function setParametrosClasse($pcieqcoid,
			$pciitem_comissao,
			$pcivl_comissao,
			$pcivl_perc_comissao,
			$pcitipo_comissao,
			$pcivl_minimo_comissao,
			$pcivl_maximo_comissao)
	{
		
		try {
		
			$cd_usuario = $_SESSION['usuario']['oid'];
			$cd_usuario = ($cd_usuario) ? $cd_usuario : 0;
			
			$sql = "SELECT * FROM parametros_comissao_indicador WHERE pcieqcoid=$pcieqcoid LIMIT 1";
			if (!$res = pg_query($this->conn,$sql)) {
				throw new Exception("Erro ao pesquisar registro",true);
			}			
			$res = pg_query($this->conn,$sql);
			$valor = pg_fetch_array($res);
			$historico = '';	

			$pciitem_comissaoAntigo 		= ($valor['pciitem_comissao']=='t') ? 'verdadeiro' 						: 'falso';
			$pcivl_comissaoAntigo 			= ($valor['pcivl_comissao']) 		? $valor['pcivl_comissao'] 			: 0;
			$pcivl_perc_comissaoAntigo 		= ($valor['pcivl_perc_comissao']) 	? $valor['pcivl_perc_comissao'] 	: 0;
			$pcitipo_comissaoAntigo 		= ($valor['pcitipo_comissao']=='V') ? 'variavel' 						: 'fixa';
			$pcivl_minimo_comissaoAntigo 	= ($valor['pcivl_minimo_comissao'])	? $valor['pcivl_minimo_comissao'] 	: 0;
			$pcivl_maximo_comissaoAntigo 	= ($valor['pcivl_maximo_comissao']) ? $valor['pcivl_maximo_comissao'] 	: 0;
			
			$pciitem_comissaoNovo 		= ($pciitem_comissao=='t') 	? 'verdadeiro' 				: 'falso';
			$pcitipo_comissaoNovo 		= ($pcitipo_comissao=='V') 	? 'variavel' 				: 'fixa';			
			$pcivl_comissaoNovo 		= $this->formatMoeda($pcivl_comissao);
			$pcivl_perc_comissaoNovo 	= $this->formatMoeda($pcivl_perc_comissao);
			$pcivl_minimo_comissaoNovo 	= $this->formatMoeda($pcivl_minimo_comissao);
			$pcivl_maximo_comissaoNovo 	= $this->formatMoeda($pcivl_maximo_comissao);
				
			$pcivl_comissaoAntigo 			= $this->formatMoeda($pcivl_comissaoAntigo);
			$pcivl_perc_comissaoAntigo 		= $this->formatMoeda($pcivl_perc_comissaoAntigo);
			$pcivl_minimo_comissaoAntigo	= $this->formatMoeda($pcivl_minimo_comissaoAntigo);
			$pcivl_maximo_comissaoAntigo 	= $this->formatMoeda($pcivl_maximo_comissaoAntigo);
			
			if ($valor['pciitem_comissao']!=$pciitem_comissao) 
				$historico .= "Alterou o Item Comissionável de $pciitem_comissaoAntigo para $pciitem_comissaoNovo \n";
			if ($valor['pcivl_comissao']!=$pcivl_comissao)
				$historico .= "Alterou o Valor da Comissão de $pcivl_comissaoAntigo para $pcivl_comissaoNovo \n";
			if ($valor['pcivl_perc_comissao']!=$pcivl_perc_comissao)
				$historico .= "Alterou o Percentual da Comissão de $pcivl_perc_comissaoAntigo para $pcivl_perc_comissaoNovo \n";
			if ($valor['pcitipo_comissao']!=$pcitipo_comissao)
				$historico .= "Alterou o Tipo de Comissão de $pcitipo_comissaoAntigo para $pcitipo_comissaoNovo \n";
			if ($valor['pcivl_minimo_comissao']!=$pcivl_minimo_comissao)
				$historico .= "Alterou o Valor Mínimo de Comissão de $pcivl_minimo_comissaoAntigo para $pcivl_minimo_comissaoNovo \n";
			if ($valor['pcivl_maximo_comissao']!=$pcivl_maximo_comissao)
				$historico .= "Alterou o Valor Máximo de Comissão de $pcivl_maximo_comissaoAntigo para $pcivl_maximo_comissaoNovo \n";		
			
			if (pg_num_rows($res)>0) {
				
				$sqlUpdate = "
								UPDATE
									parametros_comissao_indicador
								SET 
									pciitem_comissao='$pciitem_comissao',
									pcivl_comissao=$pcivl_comissao,
									pcivl_perc_comissao=$pcivl_perc_comissao,
									pcitipo_comissao='$pcitipo_comissao',
									pcivl_minimo_comissao=$pcivl_minimo_comissao,
									pcivl_maximo_comissao=$pcivl_maximo_comissao,
									pciusuoid_atualizacao=$cd_usuario		
								WHERE
									pcieqcoid=$pcieqcoid";
				
				if (!$resUpdate = pg_query($this->conn,$sqlUpdate)) {
					throw new Exception("Erro ao atualizar registro",true);
				}		
				
				//salva Histórico
				if ($historico != '') {
					$sqlInsertHist = "
									INSERT INTO parametros_comissao_indicador_historico
										(
										pcihpcioid,
										pcihusuoid,
										pcihobs,
										pcihdt_historico
										)
									VALUES
										(
										".$valor['pcioid'].",
										$cd_usuario,
										'$historico',
										now()
										)";
						
					if (!$resInsertHist = pg_query($this->conn,$sqlInsertHist)) {
						throw new Exception("Erro ao inserir registro de histórico",true);
					}
				}
				
			} else {
				$sqlInsert = "
								INSERT INTO parametros_comissao_indicador
									(
									pcieqcoid,
									pciitem_comissao,
									pcivl_comissao,
									pcivl_perc_comissao,
									pcitipo_comissao,
									pcivl_minimo_comissao,
									pcivl_maximo_comissao,
									pciusuoid_cadastro,
									pciusuoid_atualizacao)
								VALUES
									(
									$pcieqcoid,
									'$pciitem_comissao',
									$pcivl_comissao,
									$pcivl_perc_comissao,
									'$pcitipo_comissao',
									$pcivl_minimo_comissao,
									$pcivl_maximo_comissao,
									$cd_usuario,
									$cd_usuario
									)";
				
				if (!$resInsert = pg_query($this->conn,$sqlInsert)) {
					throw new Exception("Erro ao inserir registro",true);
				}
				
				//seleciona id do registro novo
				$sqlNovo = "SELECT pcioid FROM parametros_comissao_indicador WHERE pcieqcoid=$pcieqcoid";
				$resNovo = pg_query($this->conn,$sqlNovo);
				$pcioid = pg_fetch_result($resNovo,0,0);
				$pcioid = ($pcioid>0) ? $pcioid : 0;
				
				if ($pcioid==0) {
					throw new Exception("Erro ao retornar id do novo registro.",true);
				}
				
				if ($resInsert) {
					//salva Histórico
					$sqlInsertHist = "
									INSERT INTO parametros_comissao_indicador_historico
										(
										pcihpcioid,
										pcihusuoid,
										pcihobs,
										pcihdt_historico
										)
									VALUES
										(
										$pcioid,
										$cd_usuario,
										'Registro Inserido',
										now()
										)";
					
					if (!$resInsertHist = pg_query($this->conn,$sqlInsertHist)) {
						throw new Exception("Erro ao inserir registro de histórico",true);
					}
				}
			}
			
			return array(
					"error" => false,
					"msg" => "Registro salvo com sucesso"
			);
			
		} catch (Exception $e) {
			return array(
					"error" => $e->getCode(),
					"msg" => $e->getMessage()
					);
		}
	}
}