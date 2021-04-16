<?php

require 'modulos/Relatorio/DAO/RelAgingEquipamentosDAO.class.php';

class RelAgingEquipamentos
{
	private $dao;	
	
	public function RelAgingEquipamentos() {
	
		global $conn;
	
		$this->dao = new RelAgingEquipamentosDAO($conn);
		
		$this->mesInicial = $_POST["mesInicial"];
		$this->anoInicial = $_POST["anoInicial"];
		$this->mesFinal = $_POST["mesFinal"];
		$this->anoFinal = $_POST["anoFinal"];
		$this->relatorio = $_POST["tipo_relatorio"];
		$this->visao = $_POST["visao"];
		$this->equipamentos = $_POST["equipamento_selecionado"];
		$this->antenas = $_POST["antena_selecionada"];
		$this->media = $_POST["media"];
		$this->representantes = $_POST["representante_selecionado"];
		$this->representantes_nao = $_POST["representante"];
		$this->uf = $_POST["uf"];
		
		$this->total_antena = count($this->antenas);
		$this->total_eqpto = count($this->equipamentos);
		
		$this->data1 = mktime(0, 0, 0, $this->mesInicial, 1, $this->anoInicial);
		$this->data2 = mktime(0, 0, 0, $this->mesFinal, 1, $this->anoFinal);
		$this->diffMes = $this->data2 - $this->data1;
		$this->diffMes = ceil($this->diffMes/60/60/24/30);
		$this->tipo_visao = 0;
		switch ($this->visao) {
			case "recuperacao":
				$this->titulo = "TEMPO RECUPERAÇÃO DE EQUIPAMENTOS - (Data Disponível ou Data Sucata - Data Retirada)";
				$this->status_eqpto1 = 3;
				$this->status_eqpto2 = 10;
				$this->status_antena1 = 3;
				$this->status_antena2 = 8;
				$this->tipo_visao = 1;
				break;
			case "laboratorio_total":
				$this->titulo = "TEMPO LABORATÓRIO (TOTAL) - (Data Disponível ou Data Sucata - Data Retorno)";
				$this->status_eqpto1 = 3;
				$this->status_eqpto2 = 19;
				$this->status_antena1 = 3;
				$this->status_antena2 = 51;
				$this->tipo_visao = 2;
				break;
			case "transito":
				$this->titulo = "TEMPO TRANSITO - (Data Retorno - Data Retirada)";
				$this->status_eqpto1 = 19;
				$this->status_eqpto2 = 10;
				$this->status_antena1 = 51;
				$this->status_antena2 = 8;
				$this->tipo_visao = 3;
				break;
			case "laboratorio_externo":
				$this->titulo = "TEMPO LABORATÓRIO EXTERNO - (Data Disponível ou Data Sucata - Data Manutenção Fornecedor)";
				$this->status_eqpto1 = 18;
				$this->status_eqpto2 = 31;
				$this->status_antena1 = 3;
				$this->status_antena2 = 10;
				$this->tipo_visao = 4;
				break;
		}
		
		$this->nomeMedia = '';
		switch ($this->media) {
			case '2':
				$this->nomeMedia = 'Bimestre';
				break;
			case '3':
				$this->nomeMedia = 'Trimestre';
				break;
			case '4':
				$this->nomeMedia = 'Quadrimestre';
				break;
			case '6':
				$this->nomeMedia = 'Semestre';
				break;		
		}
		
		if(is_array($this->representantes)){
			$this->representantes = implode(",", $this->representantes);
		}
		
		if(is_array($this->uf)){
			foreach ($this->uf AS $key => $value) {
				$this->uf[$key] = "'".$value."'";
			}
			$this->uf = implode(",", $this->uf);
		}
		
		$this->dataInicial = $this->mesInicial."/1/".$this->anoInicial;
		$this->dataFinal = $this->mesFinal."/1/".$this->anoFinal;
			
		$this->datas = date("n/j/Y", strtotime("$this->dataInicial -1 month"));
			
		if ($this->mesFinal && $this->anoFinal) $this->totalDiasDoMes = cal_days_in_month(CAL_GREGORIAN, $this->mesFinal, $this->anoFinal);
			
		$this->agingDataInicial = '01/'.$this->mesInicial.'/'.$this->anoInicial;
		$this->agingDataFinal = $this->totalDiasDoMes.'/'.$this->mesFinal.'/'.$this->anoFinal;
		
		if (count($this->equipamentos)>0) {
			$this->implodeEquipamentos = implode(',',$this->equipamentos);
		} else {
			$this->implodeEquipamentos = '';
		}
		if (count($this->antenas)>0) {
			$this->implodeAntenas = implode(',',$this->antenas);
		} else {
			$this->implodeAntenas = '';
		}
	}
	
	/**
	 * Ação inicial
	 */
	public function index() {
		//
	}
	
	/**
	 * Ação pesquisar
	 */
	public function pesquisar() {
		echo $this->geraTabelaSintetico();
	}
	
	/**
	 * Ação gerar CSV
	 */
	public function gerar_csv() {
		if ($this->relatorio=='sintetico') {
			echo $this->geraCsvSintetico();
		} else {
			echo $this->geraCsvAnalitico();
		}
	}
	
	/**
	 * Método para retornar uma matriz com os resultados das pesquisas
	 * @return multitype:
	 */
	private function getArrayResults() {
		
		$arr1 = array();		
		if (strlen($this->implodeEquipamentos)>0) {
			$result1 = $this->dao->getResultsEquipamentos(
					"$this->agingDataInicial",
					"$this->agingDataFinal",
					"$this->tipo_visao",
					"$this->representantes",
					"$this->uf",
					"$this->implodeEquipamentos",
					"$this->relatorio");
			
			
			for ($i=0;$i<pg_num_rows($result1);$i++) {
				$arr1[$i]['modelo'] = pg_fetch_result($result1,$i,'modelo');
				$arr1[$i]['tempo'] = pg_fetch_result($result1,$i,'tempo');
				$arr1[$i]['disponivel'] = pg_fetch_result($result1,$i,'disponivel');
				if ($this->relatorio=='analitico') {
					$arr1[$i]['nome_representante'] = pg_fetch_result($result1,$i,'nome_representante');
					$arr1[$i]['uf'] = pg_fetch_result($result1,$i,'uf');
					$arr1[$i]['serial'] = pg_fetch_result($result1,$i,'serial');
					$arr1[$i]['versao'] = pg_fetch_result($result1,$i,'versao');
					$arr1[$i]['retirada'] = pg_fetch_result($result1,$i,'retirada');
				} else {
					$arr1[$i]['total'] = pg_fetch_result($result1,$i,'total');
				}
			}
			/* echo "<pre>";
			var_dump($arr1);
			echo "</pre><br /><br /><br /><br />"; */
		}
		
		$arr2 = array();
		if (strlen($this->implodeAntenas)>0) {
			$result2 = $this->dao->getResultsAntenas(
					"$this->agingDataInicial",
					"$this->agingDataFinal",
					"$this->tipo_visao",
					"$this->representantes",
					"$this->uf",
					"$this->implodeAntenas",
					"$this->relatorio");
			
			for ($i=0;$i<pg_num_rows($result2);$i++) {
				$arr2[$i]['modelo'] = pg_fetch_result($result2,$i,'modelo');
				$arr2[$i]['tempo'] = pg_fetch_result($result2,$i,'tempo');
				$arr2[$i]['disponivel'] = pg_fetch_result($result2,$i,'disponivel');
				if ($this->relatorio=='analitico') {
					$arr2[$i]['nome_representante'] = pg_fetch_result($result2,$i,'nome_representante');
					$arr2[$i]['uf'] = pg_fetch_result($result2,$i,'uf');
					$arr2[$i]['serial'] = pg_fetch_result($result2,$i,'serial');
					$arr2[$i]['versao'] = pg_fetch_result($result2,$i,'versao');
					$arr2[$i]['retirada'] = pg_fetch_result($result2,$i,'retirada');
				} else {
					$arr2[$i]['total'] = pg_fetch_result($result2,$i,'total');
				}
			}
			/* echo "<pre>";
			var_dump($arr2);
			echo "</pre><br /><br /><br /><br />"; */
		}
		
		$arr3 = array_merge($arr1,$arr2);
		
		return $arr3;
	}
		
	private function geraCsvSintetico() {
		
		try {
			
			$arrayRetorno = $this->getArrayResults();
			
			if (count($arrayRetorno)==0) {
				throw new Exception ("<script>alert('Sem Resultados');</script>");
			}
			
			$arr = array();
			$arrData = array();
			$arrModelo = array();
				
			for ($i=0;$i<count($arrayRetorno);$i++) {
				
				$arr[$arrayRetorno[$i]['disponivel']][$arrayRetorno[$i]['modelo']]['tempo'] = $arrayRetorno[$i]['tempo'];
				$arr[$arrayRetorno[$i]['disponivel']][$arrayRetorno[$i]['modelo']]['total'] = $arrayRetorno[$i]['total'];
				
				if (!in_array($arrayRetorno[$i]['disponivel'],$arrData)) $arrData[] = $arrayRetorno[$i]['disponivel'];
				
				if (!in_array($arrayRetorno[$i]['modelo'],$arrModelo)) $arrModelo[] = $arrayRetorno[$i]['modelo'];				
			}
			
			$linha = 0;
			$conteudo = "Período;";
			foreach ($arrModelo AS $key2 => $modelo) {
					$conteudo .= "$modelo;";
			}
			
			if ($this->media==1) $strMedia = "Mês";
			if ($this->media==2) $strMedia = "Bimestre";
			if ($this->media==3) $strMedia = "Trimestre";
			if ($this->media==4) $strMedia = "Quadrimestre";
			if ($this->media==6) $strMedia = "Semestre";
			
			$conteudo .= "Média $strMedia;";
			$conteudo .= "\n";
			
			$mediaMeses = 0;
			$arrMediaModelo = array();
			$arrAcumuladoModelo = array();
			$arrTotalModelo = array();
			$contador = 1;
			foreach ($arrData AS $key1 => $data) {
				$conteudo .= "$data;";
				
				if ($contador % $this->media == 0) {
					$acumuladoMes = 0;
					$totalMes = 0;
					$mediaMes = 0;
				}
				foreach ($arrModelo AS $key2 => $modelo) {
					if ($arr[$data][$modelo]['tempo'] == "") {
						$conteudo .= ";";
					} else {
						$conteudo .= $arr[$data][$modelo]['tempo'].";";
						$totalMes += $arr[$data][$modelo]['total'];
						$acumuladoMes += $arr[$data][$modelo]['tempo'] * $arr[$data][$modelo]['total'];
						$total += $arr[$data][$modelo]['total'];
						$acumulado += $arr[$data][$modelo]['tempo'] * $arr[$data][$modelo]['total'];
						$arrTotalModelo[$modelo] += $arr[$data][$modelo]['total'];
						$arrAcumuladoModelo[$modelo] += $arr[$data][$modelo]['tempo'] * $arr[$data][$modelo]['total'];
					}
				}
				
				if (($contador + ($this->media-1)) % $this->media == 0) {
					$conteudo .= "[media$contador];";
				}
				if ($contador % $this->media == 0) {
					$mediaMes = round($acumuladoMes/$totalMes);
					$strContador = $contador-$this->media+1;
					$conteudo = str_replace("[media$strContador]",$mediaMes,$conteudo);
				}
				
				$conteudo .= ";\n";
				$contador++;
			}
			
			$conteudo .= "Média/ Eqpto;";
									
			foreach ($arrModelo AS $modelo) {
				$arrMediaModelo[$modelo] = $arrAcumuladoModelo[$modelo]/$arrTotalModelo[$modelo];
				$conteudo .= round($arrMediaModelo[$modelo]).";";
			}
				
			$mediaMeses = round($acumulado/$total);
			$conteudo .= $mediaMeses.";";			
					
			$nomeArquivoCsv = 'relatorio_aging_sintetico.csv';
			$arquivoCsvRelatorioSintetico = '/var/www/docs_temporario/'.$nomeArquivoCsv;
			$csvRelatorioSintetico = fopen($arquivoCsvRelatorioSintetico, 'w+');
		
			fwrite($csvRelatorioSintetico, $conteudo);
			
			fclose($csvRelatorioSintetico);
			
			$html = "<tr class=\"tableSubTitulo\"><td colspan=\"15\"><h3>Resultado da Pesquisa</h3></td></tr>
			<tr>
				<td colspan=\"15\">
					Arquivo <a href=\"download.php?arquivo=/var/www/docs_temporario/$nomeArquivoCsv\" target=\"_self\">$nomeArquivoCsv</a> gerado com sucesso.
				</td>
			</tr>";			
			$html .= "<iframe src='download.php?arquivo=/var/www/docs_temporario/$nomeArquivoCsv' width='1' height='1'></iframe>";
				
			return $html;			
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
		
	private function geraTabelaSintetico() {
	
		try {
				
			$arrayRetorno = $this->getArrayResults();
				
			if (count($arrayRetorno)==0) {
				throw new Exception ("<script>alert('Sem Resultados');</script>");
			}
				
			$arr = array();
			$arrData = array();
			$arrModelo = array();			
	
			for ($i=0;$i<count($arrayRetorno);$i++) {
	
				$arr[$arrayRetorno[$i]['disponivel']][$arrayRetorno[$i]['modelo']]['tempo'] = $arrayRetorno[$i]['tempo'];
				$arr[$arrayRetorno[$i]['disponivel']][$arrayRetorno[$i]['modelo']]['total'] = $arrayRetorno[$i]['total'];
	
				if (!in_array($arrayRetorno[$i]['disponivel'],$arrData)) $arrData[] = $arrayRetorno[$i]['disponivel'];
	
				if (!in_array($arrayRetorno[$i]['modelo'],$arrModelo)) $arrModelo[] = $arrayRetorno[$i]['modelo'];
			}
				
			//$linha = 0;
			$html = "<tr class='tableSubTitulo'>
						<td colspan='50'><h3>Resultado da Pesquisa</h3>
						</td>
					</tr>
					<tr>
						<td colspan='50'>
							<table class='resultado-rel' width='100%'>
								<!-- Desenha cabeçalho -->
								<tr class='tableTituloColunas'>
									<td align='center'><h3>Período</h3></td>";
			foreach ($arrModelo AS $key2 => $modelo) {
				$html .= "<td align='center'><h3>$modelo</h3></td>";
			}
			
			if ($this->media==1) $strMedia = "Mês";
			if ($this->media==2) $strMedia = "Bimestre";
			if ($this->media==3) $strMedia = "Trimestre";
			if ($this->media==4) $strMedia = "Quadrimestre";
			if ($this->media==6) $strMedia = "Semestre";
			
			$html .= "<td align='center'><h3> Média/$strMedia </h3></td>";
			
			$mediaMeses = 0;
			$arrMediaModelo = array();
			$arrAcumuladoModelo = array();
			$arrTotalModelo = array();
			$contador = 1;
			foreach ($arrData AS $key1 => $data) {
				$zebra = $zebra == 'tdc' ? 'tde' : 'tdc';
				$html .= "<tr class='$zebra'>
							<td align='right'>$data</td>";
								
				if ($contador % $this->media == 0) {
					$acumuladoMes = 0;
					$totalMes = 0;
					$mediaMes = 0;
				}
				foreach ($arrModelo AS $key2 => $modelo) {
					if ($arr[$data][$modelo]['tempo'] == "") {
						$html .= "<td align='right'>&nbsp;</td>";
					} else {
						$html .= "<td align='right'>".$arr[$data][$modelo]['tempo']."</td>";
						$totalMes += $arr[$data][$modelo]['total'];
						$acumuladoMes += $arr[$data][$modelo]['tempo'] * $arr[$data][$modelo]['total'];
						$total += $arr[$data][$modelo]['total'];
						$acumulado += $arr[$data][$modelo]['tempo'] * $arr[$data][$modelo]['total'];
						$arrTotalModelo[$modelo] += $arr[$data][$modelo]['total'];
						$arrAcumuladoModelo[$modelo] += $arr[$data][$modelo]['tempo'] * $arr[$data][$modelo]['total'];
					}
				}
				if (($contador + ($this->media-1)) % $this->media == 0) {
					$html .= "<td align='right' rowspan='".$this->media."'>[media$contador]</td>";
				}
				if ($contador % $this->media == 0) {
					$mediaMes = round($acumuladoMes/$totalMes);
					$strContador = $contador-$this->media+1;
					$html = str_replace("[media$strContador]",$mediaMes,$html);
				}
				$html .= "</tr>";
				$contador++;
			}
			
			$html .= "<tr class='tableTituloColunas' bgcolor='#88AACC'>
						<td align='center'>
							<h3>M&eacute;dia/ Eqpto</h3>
						</td>";
			
			foreach ($arrModelo AS $modelo) {
				$arrMediaModelo[$modelo] = $arrAcumuladoModelo[$modelo]/$arrTotalModelo[$modelo];
				$html .= "<td align='right'><h3>".round($arrMediaModelo[$modelo])."</h3></td>";
			}
			
			$mediaMeses = round($acumulado/$total);
			$html .= "<td align='right'><h3>$mediaMeses</h3></td>";
			
			$html .= "</tr>
					</table>
					</td>
					</tr>";
			
			return $html;
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	
	private function geraCsvAnalitico() {
		
		try {
			$arrayRetorno = $this->getArrayResults();
			
			if (count($arrayRetorno)==0) {
				throw new Exception ("<script>alert('Sem Resultados');</script>");
			}
			
			$nomeArquivoCsv = 'relatorio_aging.csv';
			$arquivoCsvRelatorioAnalitico = '/var/www/docs_temporario/'.$nomeArquivoCsv;
			$csvRelatorioAnalitico = fopen($arquivoCsvRelatorioAnalitico, 'w+');
				
			fwrite($csvRelatorioAnalitico, "\"".$this->titulo."\";\n");
				
			fwrite($csvRelatorioAnalitico, '"Representante";');
			fwrite($csvRelatorioAnalitico, '"UF";');
			fwrite($csvRelatorioAnalitico, '"Serial";');
			fwrite($csvRelatorioAnalitico, '"Modelo";');
			fwrite($csvRelatorioAnalitico, '"Versão";');
				
				
			if ($this->tipo_visao == 1) {
				fwrite($csvRelatorioAnalitico, '"Disponível";');
				fwrite($csvRelatorioAnalitico, '"Retirada";');
			}
			elseif ($this->tipo_visao == 2) {
				fwrite($csvRelatorioAnalitico, '"Disponível/Sucata";');
				fwrite($csvRelatorioAnalitico, '"Retorno";');
			}
			elseif ($this->tipo_visao == 3) {
				fwrite($csvRelatorioAnalitico, '"Retorno";');
				fwrite($csvRelatorioAnalitico, '"Retirada";');
			}
			else {
				fwrite($csvRelatorioAnalitico, '"Disponível/Sucata";');
				fwrite($csvRelatorioAnalitico, '"Manutenção";');
			}
			
			fwrite($csvRelatorioAnalitico, "\"Tempo\"\n");
				
			foreach ($arrayRetorno AS $linhaAging) {
			
				/* $dataDisponivel = date('d/m/Y', strtotime($linhaAging[disponivel]));
				$dataRetirada = date('d/m/Y', strtotime($linhaAging[retirada])); */
				
				$dataDisponivel = $linhaAging[disponivel];
				$dataRetirada = $linhaAging[retirada];
			
				fwrite($csvRelatorioAnalitico, '"'.$linhaAging[nome_representante].'";');
				fwrite($csvRelatorioAnalitico, '"'.$linhaAging[uf].'";');
				fwrite($csvRelatorioAnalitico, '"'.$linhaAging[serial].'";');
				fwrite($csvRelatorioAnalitico, '"'.$linhaAging[modelo].'";');
				fwrite($csvRelatorioAnalitico, '"'.$linhaAging[versao].'";');
				fwrite($csvRelatorioAnalitico, $dataDisponivel.';');
				fwrite($csvRelatorioAnalitico, $dataRetirada.';');
				fwrite($csvRelatorioAnalitico, $linhaAging[tempo] . ";\n");		
			}			
			fclose($csvRelatorioAnalitico);	
			
			$html = "<tr class=\"tableSubTitulo\"><td colspan=\"15\"><h3>Resultado da Pesquisa</h3></td></tr>
			<tr>
			<td colspan=\"15\">
			Arquivo <a href=\"download.php?arquivo=/var/www/docs_temporario/$nomeArquivoCsv\" target=\"_self\">$nomeArquivoCsv</a> gerado com sucesso.
			</td>
			</tr>";
			$html .= "<iframe src='download.php?arquivo=/var/www/docs_temporario/$nomeArquivoCsv' width='1' height='1'></iframe>";
			
			return $html;
			
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
}