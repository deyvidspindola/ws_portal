<?php

	require_once 'lib/Components/PHPExcel/PHPExcel.php';
	require_once 'lib/html2pdf/html2pdf.class.php';

	class PrnLogAlteracaoSinalGerenciadora {

		public function exportarPdfLogDirecionamentoSinal($cabecalho, $data){

			$arrRelatorioInfo = array(
        'titulo' => 'Relatório do Direcionamento de Sinal',
        'data_inicial' => (is_null($cabecalho['data_inicial']) ? '-' : $cabecalho['data_inicial']),
        'data_final' => (is_null($cabecalho['data_final']) ? '-' : $cabecalho['data_final']),
        'data_geracao' => date('d/m/Y H:i'),
        'veiculo' => (is_null($cabecalho['veiculo']) ? 'Todos' : $cabecalho['veiculo']),
        'equipamento' => (is_null($cabecalho['equipamento']) ? 'Todos' : $cabecalho['equipamento']),
        'cliente' => (is_null($cabecalho['cliente']) ? 'Todos' : $cabecalho['cliente']),
        'gerenciadora_risco' => (is_null($cabecalho['gerenciadora_risco']) ? 'Todos' : $cabecalho['gerenciadora_risco'])
			);


			// Largura 309mm;
			$html = '
				<style>
					.bloco-header-logo,
					.bloco-header-info {
						width: 309mm;
						padding: 15px 10px;
					}
					.page-footer {
						display: block;
						width: 309mm;
						padding: 5px 10px;
						background-color: #ADD8E6;
						font-size: 11px;
						margin-top: 10px;
						text-align: right;
					}
					.page-footer-copyright {
						margin-top: 6px;
						background-color: #2D5C91;
						display: block;
						width: 309mm;
						padding: 5px 10px;
						font-size: 14px;
						font-weight: bold;
						color: #ffffff;
					}
					.bloco-header-logo {
						background-color: #2D5C91;
					}
					.bloco-header-info {
						background-color: #ADD8E6;
					}
					.bloco-header-info p {
						margin: 4px 0;
					}

					.bloco-header-info table {
						width: 309mm;
					}
					.bloco-header-info table td {
						width: 33%;
					}
					table.tabela-data {
						font-size: 10px;
						width: 309mm;
						max-width: 309mm;
						border-collapse: collapse;
						// border-width: 1px;
						border: 1px solid #000;
					}

					table.tabela-data th {
						background-color: #BFBFBF;
					}

					table.tabela-data th,
					table.tabela-data td {
						padding: 5px 6px;
						border: 1px solid #000;
					}

					table.tabela-data th {
						padding-top: 6px;
						padding-bottom: 6px;
					}

					table.tabela-data td {
						background-color: #ffffff;
						word-wrap:break-word;
						vertical-align: middle;
					}
				</style>
				<page backbottom="7mm">
					<page_footer>
						<div class="page-footer">
							Página [[page_cu]]
						</div>
						<div class="page-footer-copyright">
							www.sascar.com.br
						</div>
					</page_footer>
					<div class="bloco-header-logo">
						<img src="images/logo_sascar.png">
					</div>
					<div class="bloco-header-info">
						<p><b>'. $arrRelatorioInfo['titulo'] .'</b></p>
						<table>
							<tbody>
								<tr>
									<td><p>Data Inicial: '. $arrRelatorioInfo['data_inicial'] .'</p></td>
									<td><p>Data Final: '. $arrRelatorioInfo['data_final'] .'</p></td>
									<td><p>Gerado em: '. $arrRelatorioInfo['data_geracao'] .'</p></td>
								</tr>
								<tr>
									<td><p>Veículo: '. $arrRelatorioInfo['veiculo'] .'</p></td>
									<td><p>ID: '. $arrRelatorioInfo['equipamento'] .'</p></td>
								</tr>
								<tr>
									<td><p>Cliente: '. $arrRelatorioInfo['cliente'] .'</p></td>
									<td><p>Gerenciadora: '. $arrRelatorioInfo['gerenciadora_risco'] .'</p></td>
								</tr>
							</tbody>
						</table>
					</div>
					<br>
						<table class="tabela-data">
							<thead>
								<tr>
									<th>Data Solicitação</th>
									<th>Ação</th>
									<th>Layout</th>
									<th>Data Execução</th>
									<th>Status</th>
									<th>Validade</th>
									<th>ID</th>
									<th>Veículo</th>
									<th>Gerenciadora</th>
									<th>Prazo Direcionamento</th>
									<!-- <th>IP</th> -->
									<th>Usuário</th>
								</tr>
							</thead>
							<tbody>';

			$registroAnterior = null;

			foreach($data as $linha){

				$html .='<tr>';

				if($registroAnterior == null || $registroAnterior['lasgoid'] != $linha['lasgoid']){

					$rowspan = empty($linha['num_comandos']) ? '' : 'rowspan="'. $linha['num_comandos'] .'"';

					$html .='<td style="width:68px;" '. $rowspan .'>'. (isset($linha['data_solicitacao']) ? $linha['data_solicitacao'] : '-').'</td>';
					$html .='<td style="width:80px;" '. $rowspan .'>'. (isset($linha['acao']) ? $linha['acao'] : '-').'</td>';
					
					$rowspan = null;

				}

				$html .='<td style="width:80px;">'. (isset($linha['layout']) ? $linha['layout'] : '-').'</td>';
				$html .='<td style="width:68px;">'. (isset($linha['data_execucao']) ? $linha['data_execucao'] : '-').'</td>';
				$html .='<td style="width:60px;">'. (isset($linha['status']) ? $linha['status'] : '-').'</td>';
				$html .='<td style="width:68px;">'. (isset($linha['validade']) ? date('d/m/Y H:i', strtotime($linha['validade'])) : '-').'</td>';

				if($registroAnterior == null || $registroAnterior['lasgoid'] != $linha['lasgoid']){

					$rowspan = empty($linha['num_comandos']) ? '' : 'rowspan="'. $linha['num_comandos'] .'"';

					$html .='<td style="width:60px;" '. $rowspan .'>'. (isset($linha['veiculo_id']) ? $linha['veiculo_id'] : '-').'</td>';
					$html .='<td style="width:38px;" '. $rowspan .'>'. (isset($linha['veiculo']) ? $linha['veiculo'] : '-').'</td>';
					$html .='<td style="width:198px;" '. $rowspan .'>'. (isset($linha['gerenciadora']) ? $linha['gerenciadora'] : '-').'</td>';
					$html .='<td style="width:75px;" '. $rowspan .'>'. (isset($linha['prazo_direcionamento']) ? date('d/m/Y H:i', strtotime($linha['prazo_direcionamento'])) : ($linha['acao'] == 'DIRECIONAMENTO' ? 'Indeterminado' : '-')).'</td>';
					// $html .='<td style="width:58px;" '. $rowspan .'>'. (isset($linha['ip']) ? $linha['ip'] : '-').'</td>';
					$html .='<td style="width:78px;" '. $rowspan .'>'. (isset($linha['usuario']) ? $linha['usuario'] : '-').'</td>';

					$rowspan = null;

				}
				
				$html .= '</tr>';

				$registroAnterior = $linha;

			}

			$html .= "</tbody></table></page>";

			$html = utf8_encode($html);

			// $html2pdf = new Html2Pdf('L', array(210, 330), 'pt', true, 'UTF-8', array(15, 15, 15, 15));
			$html2pdf = new Html2Pdf('L', array(210, 350), 'pt', true, 'UTF-8', array(15, 15, 15, 15));
			$html2pdf->writeHTML($html);
			$html2pdf->output('exemple01.pdf');
			// echo 'a';

		}

		public function exportarCsvLogDirecionamentoSinal($cabecalho, $data){

			$arrRelatorioInfo = array(
        'titulo' => 'Relatório do Direcionamento de Sinal',
        'data_inicial' => $cabecalho['data_inicial'],
        'data_final' => $cabecalho['data_final'],
        'data_geracao' => date('d/m/Y H:i'),
        'veiculo' => $cabecalho['veiculo'],
        'equipamento' => $cabecalho['equipamento'] ? $cabecalho['equipamento'] : 'Todos',
        'cliente' => $cabecalho['cliente'],
        'gerenciadora_risco' => $cabecalho['gerenciadora_risco']
			);

			$arrRelatorioData = $data;

			$objPHPExcel = new PHPExcel();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("Sascar Tecnologia e Segurança Automotiva S/A")
										 ->setLastModifiedBy("Sascar Tecnologia e Segurança Automotiva S/A")
										 ->setTitle("Sascar Tecnologia e Segurança Automotiva S/A")
										 ->setSubject("Sascar Tecnologia e Segurança Automotiva S/A")
										 ->setDescription("Sascar Tecnologia e Segurança Automotiva S/A")
										 ->setKeywords("office excel sascar")
										 ->setCategory("Sascar Tecnologia e Segurança Automotiva S/A");


			$sharedStyleHeaderLogo = new PHPExcel_Style();
			$sharedStyleHeaderInfo = new PHPExcel_Style();
			$sharedStyleHeaderData = new PHPExcel_Style();

			$sharedStyleHeaderLogo->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '2D5C91')
					)
				)
			);

			$sharedStyleHeaderInfo->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'ADD8E6')
					)
				)
			);

			$sharedStyleHeaderData->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'BFBFBF')
					),
					'alignment' => array(
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					)
				)
			);

			$objPHPExcel->setActiveSheetIndex(0);

			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyleHeaderLogo, "B2:L4");
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyleHeaderInfo, "B5:L8");
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyleHeaderData, "B10:L10");

			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);

			foreach(range('B', 'M') as $column){
				$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
			}

			$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);

			// Informações do cabeçalho
			$objLogo = new PHPExcel_Worksheet_Drawing();
			$objLogo->setName('Sascar');
			$objLogo->setPath('images/logo_sascar.png');
			$objLogo->setCoordinates('B2');
			$objLogo->setOffsetX(5); 
			$objLogo->setOffsetY(5);                
			$objLogo->setWidth(100); 
			$objLogo->setHeight(35);
			$objLogo->setWorksheet($objPHPExcel->getActiveSheet());

			$objPHPExcel->getActiveSheet()
									->setCellValue('B5', utf8_encode($arrRelatorioInfo['titulo']))
									->setCellValue('B6', 'Data inicial:')
									->setCellValue('C6', $arrRelatorioInfo['data_inicial'])
									->setCellValue('E6', 'Data final:')
									->setCellValue('F6', $arrRelatorioInfo['data_final'])
									->setCellValue('H6', 'Gerado em:')
									->setCellValue('I6', $arrRelatorioInfo['data_geracao'])
									->setCellValue('B7', utf8_encode('Veículo:'))
									->setCellValue('C7', $arrRelatorioInfo['veiculo'])
									->setCellValue('E7', 'ID:')
									->setCellValue('F7', $arrRelatorioInfo['equipamento'])
									->setCellValue('B8', 'Cliente:')
									->setCellValue('C8', $arrRelatorioInfo['cliente'])
									->setCellValue('E8', 'Gerenciadora de risco:')
									->setCellValue('F8', $arrRelatorioInfo['gerenciadora_risco'])
									;

			// Cabeçalho da tabela
			$objPHPExcel->getActiveSheet()
									->setCellValue('B10', utf8_encode('Data Solicitação'))
									->setCellValue('C10', utf8_encode('Ação'))
									->setCellValue('D10', 'Layout')
									->setCellValue('E10', utf8_encode('Data Execução'))
									->setCellValue('F10', 'Status')
									->setCellValue('G10', 'Validade')
									->setCellValue('H10', 'ID')
									->setCellValue('I10', utf8_encode('Veículo'))
									->setCellValue('J10', 'Gerenciadora')
									->setCellValue('K10', 'Prazo direcionamento')
									// ->setCellValue('L10', 'IP')
									->setCellValue('L10', utf8_encode('Usuário'))
									;

			$objPHPExcel->getActiveSheet()
									->getRowDimension('10')
									->setRowHeight(26);

			// Informações da tabela
			$i = 11;
			$registroAnterior = null;

			foreach($arrRelatorioData as $linha){

				if($registroAnterior == null || $registroAnterior['lasgoid'] != $linha['lasgoid']){

					$objPHPExcel->getActiveSheet()->setCellValue('B'. $i, (isset($linha['data_solicitacao']) ? $linha['data_solicitacao'] : '-'));
					$objPHPExcel->getActiveSheet()->setCellValue('C'. $i, (isset($linha['acao']) ? $linha['acao'] : '-'));
					// -
					$objPHPExcel->getActiveSheet()->setCellValue('H'. $i, (isset($linha['veiculo_id']) ? $linha['veiculo_id'] : '-'));
					$objPHPExcel->getActiveSheet()->setCellValue('I'. $i, (isset($linha['veiculo']) ? $linha['veiculo'] : '-'));
					$objPHPExcel->getActiveSheet()->setCellValue('J'. $i, (isset($linha['gerenciadora']) ? utf8_encode($linha['gerenciadora']) : '-'));
					$objPHPExcel->getActiveSheet()->setCellValue('K'. $i, (isset($linha['prazo_direcionamento']) ? date('d/m/Y H:i', strtotime($linha['prazo_direcionamento'])) : ($linha['acao'] == 'DIRECIONAMENTO' ? 'Indeterminado' : '-')));
					// $objPHPExcel->getActiveSheet()->setCellValue('L'. $i, (isset($linha['ip']) ? $linha['ip'] : '-'));
					$objPHPExcel->getActiveSheet()->setCellValue('L'. $i, (isset($linha['usuario']) ? $linha['usuario'] : '-'));

					if($registroAnterior !== null && $registroAnterior['num_comandos'] != null){

						$x = $i - intval($registroAnterior['num_comandos']); // início registro
						$y = $i - 1; // linha anterior

						$objPHPExcel->getActiveSheet()->mergeCells("B$x:B$y");
						$objPHPExcel->getActiveSheet()->mergeCells("C$x:C$y");
						$objPHPExcel->getActiveSheet()->mergeCells("H$x:H$y");
						$objPHPExcel->getActiveSheet()->mergeCells("I$x:I$y");
						$objPHPExcel->getActiveSheet()->mergeCells("J$x:J$y");
						$objPHPExcel->getActiveSheet()->mergeCells("K$x:K$y");
						$objPHPExcel->getActiveSheet()->mergeCells("L$x:L$y");
						$objPHPExcel->getActiveSheet()->mergeCells("M$x:M$y");

						/* Style Data */

						$styleData = new PHPExcel_Style();

						$styleData->applyFromArray(
							array(
								'alignment' => array(
									'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
								)
							)
						);

						$objPHPExcel->getActiveSheet()->setSharedStyle($styleData, "B$x");
						$objPHPExcel->getActiveSheet()->setSharedStyle($styleData, "C$x");
						$objPHPExcel->getActiveSheet()->setSharedStyle($styleData, "H$x");
						$objPHPExcel->getActiveSheet()->setSharedStyle($styleData, "I$x");
						$objPHPExcel->getActiveSheet()->setSharedStyle($styleData, "J$x");
						$objPHPExcel->getActiveSheet()->setSharedStyle($styleData, "K$x");
						$objPHPExcel->getActiveSheet()->setSharedStyle($styleData, "L$x");
						$objPHPExcel->getActiveSheet()->setSharedStyle($styleData, "M$x");

					}

				}

				$objPHPExcel->getActiveSheet()->setCellValue('D'. $i, (isset($linha['layout']) ? utf8_encode($linha['layout']) : '-'));
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $i, (isset($linha['data_execucao']) ? $linha['data_execucao'] : '-'));
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $i, (isset($linha['status']) ? $linha['status'] : '-'));
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $i, (isset($linha['validade']) ? date('d/m/Y H:i', strtotime($linha['validade'])) : '-'));


				$registroAnterior = $linha;

				$i++;

			}

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('Direcionamento de sinal');

			$objPHPExcel->getActiveSheet()->removeColumn('A');
			$objPHPExcel->getActiveSheet()->removeRow(1);


			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);

			$filename = 'RELATORIO_DIRECIONAMENTO_'. date('dmYHi');

			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.ms-excel; charset=utf-8');
			// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');
			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit;

		}

	}