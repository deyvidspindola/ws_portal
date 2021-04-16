<?php
/*
 * @author	Gabriel Luiz Pereira
 * @email	gabriel.pereira@meta.com.br
 * @since	21/08/2012
 * */

/**
 * Análise de kits de instação para ordem de serviço.
 */
 class AnaliseKitOrdemServico {

 	/**
 	 * Link de conexão com o banco
 	 * @var resource
 	 */
 	private $conn;

 	/**
 	 * OID da ordem de serviço em questão
 	 * @var integer
 	 */
 	private $ordoid;


 	/**
 	 * Construtor
 	 * @param integer $ordoid	OID da ordem de serviço
 	 * @throws Exception
 	 */
 	public function __construct($ordoid) {

 		global $conn;

 		if (empty($ordoid)) {
 			throw new Exception("Favor informar  uma ordem de serviço.");
 		}
 		else if (empty($conn)) {
 			throw new Exception("Link de conexão com o banco não encontrado.");
 		}
 		else if (!is_numeric($ordoid)) {
 			throw new Exception("O número da ordem de serviço deve ser númerica.");
 		}
 		else {

 			// Aloca valores iniciais obrigatórios
 			$this->ordoid 	= $ordoid;
 			$this->conn	 	= $conn;
 		}
 	}

 	/**
 	 * Executa o processo de análise e inserção de Kits da ordem de serviço.
 	 * @throws Exception
 	 */
 	public function executar() {

 		if (!empty($this->ordoid)) {

 			try {

 				// Se a ordem de serviço não for de assistência
 				if ($this->verificaOSTipoAssistencia() === false) {

 					$rsMotivos 			= $this->consultaMotivosOS();
 					$totalMotivos		= pg_num_rows($rsMotivos);

 					$rsKitsInseridos 	= $this->buscaKitsOrdemServico();
 					$totalKitsInseridos = pg_num_rows($rsKitsInseridos);

					$listaMotivos		= array(); // Array inicial de motivos
 					$listaKitsInstalados= array(); // Lista de kits encontrados
 					$listaKitsInseridos = array(); // Lista de kits incluidos ao final do processo

 					$listaAtuadoresAnalise= array();
 					$listaMotivosAnalise  = array();

 					/**
 					 * Listas para análise de Kit de Instalacao
 					 */
 					 $listaAtuadoresI	= array();
 					 $listaMotivosI		= array();
 					 $listaMotivosIB	= array(); // Lista de motivos para análise somente com itens básicos.

 					 $listaAtuadoresObrigatorios	= array();
 					 $listaMotivosObrigatorios		= array();

 					 $kitsObrigatorio	= array();
 					 $kitsNObrigatorio	= array();


 					$contaMotivosA 		= 0;

 					$ostoid				= '';


					/**
					 * Se não retornar nenhum motivo e retornar kits já inseridos,
					 * não executa o processo
					 */
 					if (!($totalKitsInseridos > 0 && $totalMotivos == 0)) {


 						// Monta a lista de kits já instalados antes do processo
 						while ($kit = pg_fetch_object($rsKitsInseridos)) {

 							$listaKitsInstalados[] = array(
 								'ositotioid'		=> $kit->ositotioid,
 								'instalado'			=> true
 							);
 						}

 						// Monta array inicial com os motivos
 						while ($motivo = pg_fetch_object($rsMotivos)) {

 							/**
 							 * Se o motivo for do tipo 'V' (Avulso) e retornar um kit,
 							 * adiciona o kit na lista de inseridos.
 							 *
 							 * Caso contrario, ignore o motivo e não inclua na lista de motivos a serem tratados.
 							 */
 							if ($motivo->otitipo_kit_instalacao == 'V') {

 							 	if ($motivo->kit_instalacao > 0) {

	 							 	// Insere o kit na lista
							 		$listaKitsInstalados[] = array(
		 								'ositotioid'		=> $motivo->kit_instalacao,
		 								'instalado'			=> false
		 							);
 							 	}
 							}
 							else {
 								// Caso não seja tipo 'V'

 								// Conta a quantidade de motivos do tipo 'A'
 								if ($motivo->otitipo_kit_instalacao == 'A') {
 									$contaMotivosA++;
 								}

 								// Adiciona na lista inicial
	 							$listaMotivos[] = array(
	 								'ordoid'					=> $motivo->ordoid,
	 								'ordstatus'					=> $motivo->ordstatus,
	 								'ositeqcoid'				=> $motivo->ositeqcoid,
	 								'eqcdescricao'				=> $motivo->eqcdescricao,
	 								'ostoid'					=> $motivo->ostoid,
	 								'ostdescricao'				=> $motivo->otidescricao,
	 								'ositstatus'				=> $motivo->ositstatus,
	 								'ositotioid'				=> $motivo->ositotioid,
	 								'otiattoid'					=> $motivo->otiattoid,
	 								'otitipo_kit_instalacao'	=> $motivo->otitipo_kit_instalacao,
	 								'otitipo_kit_visualizacao'	=> $motivo->otitipo_kit_visualizacao,
	 								'kit_instalacao'			=> $motivo->kit_instalacao
	 							);
 							}

 							// Preenche o ostoid para uso posterior
 							if (empty($ostoid)) {
 								$ostoid	= $motivo->ostoid;
 							}
						}

 						/**
 						 * Se existir valores no array Deverá ser percorrido o array verificando
 						 * quantos motivos de otitipo_kit_instalacao do tipo 'A' existem
 						 */
 						if (count($listaMotivos) > 0) {

 							/**
 							 * Se o numero de motivos otitipo_kit_instalacao tipo 'A' > 2,
        					 */
 							if ($contaMotivosA > 2) {

 							 	/**
 							 	 * criar 2 listas para guardar os itens que serão buscados
	 							 * na composição dos Kits, as listas serão diferenciada pelos
	 							 * códigos de Atuadores e Motivos.
 							 	 */
 							 	foreach($listaMotivos as $chave => $motivo) {

 							 		if ($motivo['otitipo_kit_instalacao'] == 'A') {

	 							 		if ($motivo['otitipo_kit_visualizacao'] == 'M') {

	 							 			/**
	 							 			 * Guardar  o valor do ositotioid na lista de Lista_Motivos_ositotioid
	 							 			 */
	 							 			$listaMotivosAnalise[] = $motivo['ositotioid'];
	 							 		}

	 							 		if ($motivo['otitipo_kit_visualizacao'] == 'A') {

	 							 			/**
	 							 			 * Guardar  o valor do ositotioid na lista de Lista_Atuadores_ositotioid
	 							 			 */
	 							 			$listaAtuadoresAnalise[] = $motivo['otiattoid'];
	 							 		}
 							 		}
 							 	}

 							 	// Consulta 3
						 		$rsBuscaKitsMotivoAtuador = $this->buscaKitMotivoAtuador(
						 			$ostoid,
						 			implode(',', $listaMotivosAnalise),
						 			implode(',', $listaAtuadoresAnalise)
						 		);

						 		// Se possuir resultados
						 		$buscaKitTotalLinhas = pg_num_rows($rsBuscaKitsMotivoAtuador);


						 		if ($buscaKitTotalLinhas > 0) {

						 			// Trata se houve inclusão de kit para os acessórios ou não
						 			$kitAcessorioIncluido = false;

						 			// Reorganiza o array
						 			sort($listaAtuadoresAnalise);
						 			sort($listaMotivosAnalise);

						 			/**
						 			 * No resultado da Consulta 3 serão retornados todos os Kits
						 			 * que contem os valores solicitados, devemos verificar se nos
						 			 * resultados existem um Kit  que seja composto exatamente pela
						 			 * composição de Motivos + Atuadores.
						 			 */
									while ($kitMotivoAtuador = pg_fetch_object($rsBuscaKitsMotivoAtuador)) {

										// Listas Motivos/Atuadores para comparação com o Kit
										if ($kitMotivoAtuador->atuador != '') {

											$atuadoresDoKit 	= explode(',', $kitMotivoAtuador->atuador);
										}
										else {

											$atuadoresDoKit 	= array();
										}

										if ($kitMotivoAtuador->motivo != '') {

											$motivosDoKit 		= explode(',', $kitMotivoAtuador->motivo);
										}
										else {

											$motivosDoKit = array();
										}


										// Organiza o array
										sort($atuadoresDoKit);
										sort($motivosDoKit);

										// Verifica se o Kit tem a mesma composição de Motivos e Atuadores
										$diferencaAtuadores	= array_diff_assoc($atuadoresDoKit, $listaAtuadoresAnalise);

										$diferencaMotivos	= array_diff_assoc($motivosDoKit,   $listaMotivosAnalise);

										// Verifica a diferença reversa entre itens da O.S. e a lista de kits
										$diferencaAtuadoresAnalise = array_diff_assoc($listaAtuadoresAnalise, $atuadoresDoKit);

										$diferencaMotivosAnalise = array_diff_assoc($listaMotivosAnalise, $motivosDoKit);

										// Se a estrutura do kit for exatamente igual (Não deve haver nenhuma das diferenças)
										if (count($diferencaAtuadores) == 0
											&& count($diferencaMotivos) == 0
											&& count($diferencaAtuadoresAnalise) == 0
											&& count($diferencaMotivosAnalise) == 0)
										{

											$kitNaoInserido = true;

											// Verifica se o Kit já esta inserido
			 							 	for($i = 0; $i <= count($listaKitsInstalados)-1; $i++) {

			 							 		if ($kitMotivoAtuador->kiosotioid_kit == $listaKitsInstalados[$i]['ositotioid']) {
			 							 			$kitNaoInserido = false;
			 							 		}
			 							 	}

			 							 	// Kit ainda não inserido?
			 							 	if ($kitNaoInserido == true) {

			 							 		// Então insere com flag de instalado = false
			 							 		$listaKitsInstalados[] = array(
			 							 			'ositotioid'		=> $kitMotivoAtuador->kiosotioid_kit,
 													'instalado'			=> false
			 							 		);


			 							 		$kitAcessorioIncluido = true;
			 							 		$totalListaMotivos = count($listaMotivos);

			 							 		// Remove itens do Array inicial
			 							 		for ($i = 0; $i < $totalListaMotivos; $i++) {

			 							 			if ($listaMotivos[$i]['otitipo_kit_instalacao'] == 'A') {

														unset($listaMotivos[$i]);
			 							 			}
			 							 		}
			 							 		sort($listaMotivos);
			 							 	}
			 							 	else {
			 							 		continue;
			 							 	}
										}
									}

									if ($kitAcessorioIncluido === false) {

										 /**
										 * Se não encontrou kit para adicionar
										 * Verificar os Kits avulsos dos itens do tipo acessório onde otitipo_kit_instalacao = 'A'
										 */
									 	foreach($listaMotivos as $chave => $motivo) {

									 		if ($motivo['otitipo_kit_instalacao'] == 'A') {

									 			$rsBuscaKitAvulso = $this->buscaKitAvulso($motivo['ositotioid']);

									 			if (pg_num_rows($rsBuscaKitAvulso) > 0) {

									 				// Controla a inserção do kit caso já o tenha na lista de instalados
									 				$insereKit = true;
									 				$kitAvulsoAssistencia = pg_fetch_object($rsBuscaKitAvulso);

									 				// Validação de kit existente
									 				for($i = 0; $i <= count($listaKitsInstalados)-1; $i++) {

									 					if ($kitAvulsoAssistencia->kiosotioid_kit == $listaKitsInstalados[$i]['ositotioid']) {

									 						$insereKit = false;
									 					}
									 				}

									 				// Se o Kit ainda não foi inserido na lista
									 				if ($insereKit == true) {

									 					// Adiciona kit
									 					$listaKitsInstalados[] = array(
									 						'ositotioid'		=> $kitAvulsoAssistencia->kiosotioid_kit,
															'instalado'			=> false
									 					);

									 					// Remove motivo da lista de análise
									 					unset($listaMotivos[$chave]);
									 				}
									 			}
									 		}
									 	}
									}

						 		}
 							 }


							 /**
						 	 * Executar regra de Kit de instalação ( Ver RN 6.9)
						 	 *
						 	 * Ao percorrer o array_inicial  deverá ser verificado o campo otitipo_kit_visualizacao.
						 	 * Verificar se existem motivos do Tipo otitipo_kit_instalacao  com 'A e 'B'
						 	 */
						 	 // Verifica se tem tipo A e B
						 	 $temA = false;
						 	 $temB = false;

						 	 foreach($listaMotivos as $chave => $motivo) {

						 	 		if ($motivo['otitipo_kit_instalacao'] == 'A' && $temA === false) {
						 	 			$temA = true;
						 	 		}

						 	 		if ($motivo['otitipo_kit_instalacao'] == 'B' && $temB === false) {
						 	 			$temB = true;
						 	 		}
						 	 }


						 	 if ($temA || $temB) {

						 	 	foreach($listaMotivos as $chave => $motivo) {

						 	 		if ($motivo['otitipo_kit_instalacao'] == 'A') {

						 	 			if ($motivo['otitipo_kit_visualizacao'] == 'M') {

						 	 				$listaMotivosI[] = $motivo['ositotioid'];
						 	 				$listaMotivosIB[] = $motivo['ositotioid'];
						 	 			}
						 	 			else {
						 	 				$listaAtuadoresI[] = $motivo['otiattoid'];
						 	 			}

						 	 		}

						 	 		if ($motivo['otitipo_kit_instalacao'] == 'B') {

						 	 			$listaMotivosI[] = $motivo['ositotioid'];
						 	 			$listaMotivosIB[] = $motivo['ositotioid'];
						 	 		}

						 	 	}

						 	 	/**
						 	 	 * Análise de kits de instalação considerando acessórios.
						 	 	 */
								if(is_array($listaMotivosI) and count($listaMotivosI)){
									$rsKitsInstalacao = $this->buscaKitsInstalacao(
										$ostoid,
										implode(',', $listaMotivosI),
										implode(',', $listaAtuadoresI)
									);


									if (pg_num_rows($rsKitsInstalacao) > 0) {

										// Montagem dos kits para avaliação
										while ($kit = pg_fetch_object($rsKitsInstalacao)) {

											// Trata espaços em branco
											if (empty($kit->motivo)) {
												$motivosKit = array();
											}
											else {
												$motivosKit = explode(',', $kit->motivo);
											}

											if (empty($kit->atuador)) {
												$atuadoresKit = array();
											}
											else {
												$atuadoresKit = explode(',', $kit->atuador);
											}


											// Monta listas de obrigatorios e não obrigatorios
											if ($kit->kiosobrigatorio == 't') {

												$kitsObrigatorio[] = array(
													'kiosotioid_kit'	=> $kit->kiosotioid_kit,
													'motivos'			=> $motivosKit,
													'atuadores'			=> $atuadoresKit
												);
											}
											else {

												$kitsNObrigatorio[] = array(
													'kiosotioid_kit'	=> $kit->kiosotioid_kit,
													'motivos'			=> $motivosKit,
													'atuadores'			=> $atuadoresKit
												);
											}

											// Ordena as listas
											sort($kitsObrigatorio);
											sort($kitsNObrigatorio);
										}

										sort($listaMotivosI);
										sort($listaMotivosIB);
										sort($listaAtuadoresI);


										// Análise dos kits (itens Obrigatórios), aqui eliminamos os kits que não serão usados.
										for($i = 0; $i < count($kitsObrigatorio); $i++) {

											$diferencaKitMotivos = 0;
											$diferencaKitAtuadores = 0;

											sort($kitsObrigatorio[$i]['atuadores']);
											sort($kitsObrigatorio[$i]['motivos']);

											$diferencaKitMotivos   = count(array_diff($kitsObrigatorio[$i]['motivos'],   $listaMotivosI));

											$diferencaKitAtuadores   = count(array_diff_assoc($kitsObrigatorio[$i]['atuadores'],   $listaAtuadoresI));

											if ($diferencaKitMotivos > 0 || $diferencaKitAtuadores > 0) {

												// Remove o mesmo kit da lista de itens não obrigatórios
												for ($j = 0; $j < count($kitsNObrigatorio); $j++) {
													if ($kitsNObrigatorio[$j]['kiosotioid_kit'] == $kitsObrigatorio[$i]['kiosotioid_kit']) {

														unset($kitsNObrigatorio[$j]);
														sort($kitsObrigatorio);
													}
												}

												unset($kitsObrigatorio[$i]);
												sort($kitsObrigatorio);
												$i = 0;
											}
										}

										// Se depois da análise eliminatória existir algum kit,
										if (count($kitsObrigatorio) > 0) {

											sort($kitsObrigatorio);
											sort($kitsNObrigatorio);

											$diferencaAtuadoresObrigatorios = 0;
											$diferencaMotivosObrigatorios	= 0;

											$diferencaAtuadoresInversa 		= 0;
											$diferencaMotivosInversa		= 0;

											$pesoDiferencaInversa			= null;
											$diferencaInversa				= 0;
											$kitMaisProximo					= null;


											// Efetua a análise para saber se exite algum kit que contempla a lista de
											// itens que sobraram da ordem de serviço
											for ($k = 0; $k < count($kitsObrigatorio); $k++) {

												$insereKit = false;

												sort($kitsObrigatorio[$k]['atuadores']);
												sort($kitsObrigatorio[$k]['motivos']);

												sort($listaAtuadoresI);
												sort($listaMotivosI);

												if (count($listaAtuadoresI) > 0) {
													$diferencaAtuadoresObrigatorios = count(array_diff_assoc($kitsObrigatorio[$k]['atuadores'], $listaAtuadoresI));
													$diferencaAtuadoresInversa		= count(array_diff_assoc($listaAtuadoresI, $kitsObrigatorio[$k]['atuadores']));
												}

												if (count($listaMotivosI) > 0) {
													$diferencaMotivosObrigatorios   = count(array_diff($kitsObrigatorio[$k]['motivos'],   $listaMotivosI));
													$diferencaMotivosInversa		= count(array_diff($listaMotivosI, $kitsObrigatorio[$k]['motivos']));
												}


												// Se o kit não tem diferênça, então entra para análise dos itens da O.S.
												if ($diferencaMotivosObrigatorios == 0 && $diferencaAtuadoresObrigatorios == 0) {


													// Análise das diferenças

													if (count($listaMotivosI) > 0 && count($listaAtuadoresI) > 0) {

														$diferencaInversa = $diferencaAtuadoresInversa + $diferencaMotivosInversa;
													}
													else if (count($listaMotivosI) > 0) {

														$diferencaInversa = $diferencaMotivosInversa;
													}
													else if (count($listaAtuadoresI) > 0) {

														$diferencaInversa = $diferencaAtuadoresInversa;
													} else {
														$diferencaInversa = 0;
													}

													// Análisa o peso entre as diferenças
													if ($pesoDiferencaInversa === null) {

														$kitMaisProximo		  = $k;
														$pesoDiferencaInversa = $diferencaInversa;

														if (count($kitsObrigatorio)-1 == $k) {

															$insereKit = true;
														}
													}
													else if ($diferencaInversa < $pesoDiferencaInversa) {

														$kitMaisProximo		  = $k;
														$pesoDiferencaInversa = $diferencaInversa;

														if ($k == count($kitsObrigatorio)-1) {

															$insereKit = true;
														}
													}
													else {


														if ($k == count($kitsObrigatorio)-1) {

															$insereKit = true;
														}
													}
												}

												// Inserção do kit
												if ($insereKit == true && $kitMaisProximo !== null) {

													$kitValido = true;

													// Verifica se o kit já esta inserido, se estiver, não insere
													foreach($listaKitsInstalados as $kit) {

														if ($kit['ositotioid'] == $kitsObrigatorio[$kitMaisProximo]['kiosotioid_kit']) {
															$kitValido = false;
														}
													}

													if ($kitValido == true) {

														// Insere o Kit encontrado
														$listaKitsInstalados[] = array(
															'ositotioid'		=> $kitsObrigatorio[$kitMaisProximo]['kiosotioid_kit'],
															'instalado'			=> false
														);
													}

													// TRATA OBRIGATÓRIOS

													// Se existir motivos na análise
													if (count($listaMotivosI) > 0) {

														// Remove ele da lista de motivos
														for($x = 0; $x < count($listaMotivosI); $x++) {

															if (in_array($listaMotivosI[$x], $kitsObrigatorio[$kitMaisProximo]['motivos'])) {

																for($m = 0; $m < count($listaMotivos); $m++) {

																	if ($listaMotivos[$m]['ositotioid'] == $listaMotivosI[$x]) {

																		unset($listaMotivos[$m]);
																		sort($listaMotivos);
																		$m = count($listaMotivos);
																	}
																}
																unset($listaMotivosIB[$x]);
															}
														}
													}

													// Se existir atuadores na análise
													if (count($listaAtuadoresI) > 0) {

														// Remove ele da lista de motivos
														for($x = 0; $x < count($listaAtuadoresI); $x++) {

															if (in_array($listaAtuadoresI[$x], $kitsObrigatorio[$kitMaisProximo]['atuadores'])) {

																for($m = 0; $m < count($listaMotivos); $m++) {

																	if ($listaMotivos[$m]['otiattoid'] == $listaAtuadoresI[$x]) {

																		unset($listaMotivos[$m]);
																		sort($listaMotivos);
																		$m = count($listaMotivos);
																	}
																}
															}
														}
													}

													// Encontra o indice do mesmo Kit para itens não obrigatórios
													$indeKitNObrigatorio = null;
													for ($n = 0; $n < count($kitsNObrigatorio); $n++) {

														if ($kitsNObrigatorio[$n]['kiosotioid_kit'] == $kitsNObrigatorio[$kitMaisProximo]['kiosotioid_kit']) {

															$indeKitNObrigatorio = $n;
														}
													}

													// TRATA NÃO OBRIGATÓRIOS
													if ($indeKitNObrigatorio !== null) {

														sort($kitsNObrigatorio[$indeKitNObrigatorio]['motivos']);
														sort($listaMotivos);

														// Se existir motivos na análise
														if (count($listaMotivosI) > 0) {

															// Remove ele da lista de motivos
															for($x = 0; $x <= count($listaMotivosI); $x++) {

																if (in_array($listaMotivosI[$x], $kitsNObrigatorio[$indeKitNObrigatorio]['motivos'])) {

																	for($m = 0; $m < count($listaMotivos); $m++) {

																		if ($listaMotivos[$m]['ositotioid'] == $listaMotivosI[$x]) {
																			unset($listaMotivos[$m]);
																			unset($listaMotivosIB[$x]);
																			sort($listaMotivos);
																			break;
																		}
																	}
																}

															}

														}
													}

												}
											}
										}


										/**
										 * Análise de kits de instalação considerando somente itens básico
										 * (Identico ao processo acima, considerando apenas itens com visualização do tipo 'B').
										 */
										 if (count($listaMotivosIB)) {

											$rsKitsInstalacao = $this->buscaKitsInstalacao(
												$ostoid,
												implode(',', $listaMotivosIB),
												''
											);

											if (pg_num_rows($rsKitsInstalacao) > 0) {

												// Montagem dos kits para avaliação
												while ($kit = pg_fetch_object($rsKitsInstalacao)) {

													// Trata espaços em branco
													if (empty($kit->motivo)) {
														$motivosKit = array();
													}
													else {
														$motivosKit = explode(',', $kit->motivo);
													}

													// Monta listas de obrigatorios e não obrigatorios
													if ($kit->kiosobrigatorio == 't') {

														$kitsObrigatorio[] = array(
															'kiosotioid_kit'	=> $kit->kiosotioid_kit,
															'motivos'			=> $motivosKit
														);
													}
													else {

														$kitsNObrigatorio[] = array(
															'kiosotioid_kit'	=> $kit->kiosotioid_kit,
															'motivos'			=> $motivosKit
														);
													}

													// Ordena as listas
													sort($kitsObrigatorio);
													sort($kitsNObrigatorio);
												}

												sort($listaMotivosIB);

												// Análise dos kits (itens Obrigatórios), aqui eliminamos os kits que não serão usados.
												for($i = 0; $i < count($kitsObrigatorio); $i++) {

													$diferencaKitMotivos = 0;

													sort($kitsObrigatorio[$i]['motivos']);

													$diferencaKitMotivos   = count(array_diff($kitsObrigatorio[$i]['motivos'],   $listaMotivosIB));

													if ($diferencaKitMotivos > 0) {

														// Remove o mesmo kit da lista de itens não obrigatórios
														for ($j = 0; $j < count($kitsNObrigatorio); $j++) {

															if ($kitsNObrigatorio[$j]['kiosotioid_kit'] == $kitsObrigatorio[$i]['kiosotioid_kit']) {

																unset($kitsNObrigatorio[$j]);
																sort($kitsObrigatorio);
															}
														}

														unset($kitsObrigatorio[$i]);
														sort($kitsObrigatorio);
														$i = 0;
													}
												}

												// Se depois da análise eliminatória existir algum kit,
												if (count($kitsObrigatorio) > 0) {

													sort($kitsObrigatorio);
													sort($kitsNObrigatorio);

													$diferencaMotivosObrigatorios	= 0;

													$diferencaMotivosInversa		= 0;

													$pesoDiferencaInversa			= null;
													$diferencaInversa				= 0;
													$kitMaisProximo					= null;


													// Efetua a análise para saber se exite algum kit que contempla a lista de
													// itens que sobraram da ordem de serviço
													for ($k = 0; $k < count($kitsObrigatorio); $k++) {

														$insereKit = false;

														sort($kitsObrigatorio[$k]['motivos']);

														sort($listaMotivosIB);


														if (count($listaMotivosI) > 0) {
															$diferencaMotivosObrigatorios   = count(array_diff($kitsObrigatorio[$k]['motivos'],   $listaMotivosIB));
															$diferencaMotivosInversa		= count(array_diff($listaMotivosIB, $kitsObrigatorio[$k]['motivos']));
														}


														// Se o kit não tem diferênça, então entra para análise dos itens da O.S.
														if ($diferencaMotivosObrigatorios == 0) {

															$diferencaInversa = 0;
															// Análise das diferenças

															if (count($listaMotivosIB) > 0) {

																$diferencaInversa = $diferencaMotivosInversa;
															}

															// Análisa o peso entre as diferenças
															if ($pesoDiferencaInversa === null) {

																$kitMaisProximo		  = $k;
																$pesoDiferencaInversa = $diferencaInversa;

																if (count($kitsObrigatorio)-1 == $k) {

																	$insereKit = true;
																}
															}
															else if ($diferencaInversa < $pesoDiferencaInversa) {

																$kitMaisProximo		  = $k;
																$pesoDiferencaInversa = $diferencaInversa;

																if ($k == count($kitsObrigatorio)-1) {

																	$insereKit = true;
																}
															}
															else {


																if ($k == count($kitsObrigatorio)-1) {

																	$insereKit = true;
																}
															}
														}

														// Inserção do kit
														if ($insereKit == true && $kitMaisProximo !== null) {

															$kitValido = true;

															// Verifica se o kit já esta inserido, se estiver, não insere
															foreach($listaKitsInstalados as $kit) {

																if ($kit['ositotioid'] == $kitsObrigatorio[$kitMaisProximo]['kiosotioid_kit']) {
																	$kitValido = false;
																}
															}

															if ($kitValido == true) {

																// Insere o Kit encontrado
																$listaKitsInstalados[] = array(
																	'ositotioid'		=> $kitsObrigatorio[$kitMaisProximo]['kiosotioid_kit'],
																	'instalado'			=> false
																);

															}

															// TRATA OBRIGATÓRIOS
															sort($listaMotivosIB);

															// Se existir motivos na análise
															if (count($listaMotivosIB) > 0) {

																// Remove ele da lista de motivos
																for($x = 0; $x < count($listaMotivosIB); $x++) {

																	if (in_array($listaMotivosIB[$x], $kitsObrigatorio[$kitMaisProximo]['motivos'])) {

																		for($m = 0; $m < count($listaMotivos); $m++) {

																			if ($listaMotivos[$m]['ositotioid'] == $listaMotivosIB[$x]) {

																				unset($listaMotivos[$m]);
																				sort($listaMotivos);
																				break;
																			}
																		}
																	}
																}
															}


															// Encontra o indice do mesmo Kit para itens não obrigatórios
															$indeKitNObrigatorio = null;
															for ($n = 0; $n < count($kitsNObrigatorio); $n++) {

																if ($kitsNObrigatorio[$n]['kiosotioid_kit'] == $kitsNObrigatorio[$kitMaisProximo]['kiosotioid_kit']) {

																	$indeKitNObrigatorio = $n;
																}
															}
															sort($kitsNObrigatorio[$indeKitNObrigatorio]['motivos']);

															// TRATA NÃO OBRIGATÓRIOS
															// Se exitir atuadores na análise
															if ($indeKitNObrigatorio !== null) {

																sort($listaMotivosIB);
																sort($kitsNObrigatorio[$indeKitNObrigatorio]['motivos']);

																// Se existir motivos na análise
																if (count($listaMotivosIB) > 0) {

																	// Remove ele da lista de motivos
																	for($x = 0; $x < count($listaMotivosIB); $x++) {

																		if (in_array($listaMotivosIB[$x], $kitsNObrigatorio[$indeKitNObrigatorio]['motivos'])) {

																			for($m = 0; $m < count($listaMotivos); $m++) {

																				if ($listaMotivos[$m]['ositotioid'] == $listaMotivosIB[$x]) {

																					unset($listaMotivos[$m]);
																					sort($listaMotivos);
																					break;
																				}
																			}
																		}
																	}

																}
															}
														}
													}
												}
											}
										 }


									}
								}
						 	 } // FIM temA e temB

 							 /**
 							  * REGRA :  Análise dos itens restantes que não entraram em um kit.
 							  * Verificar se inda existe itens no array_inicial  para fazer analise de itens avulsos.
 							  * Se existir (VERIFICAÇÃO DE KITS DO TIPO AVULSOS (SOBRA) Percorrer o array_inicial Executar
 							  * a CONSULTA 2 para cada item restante do array.
 							  *
 							  * Verifica se o Kit já não está cadastrado na O.S.
 							  *
 							  * SE NÂO EXISTIR Inclui o Kit no array kits_instalacao flega ele como novo.
 							  * FIM SE
 							  * Remove os itens do array_inicial que compoem o Kit
 							  */

 							 sort($listaMotivos);
 							 $totalListaMotivos = count($listaMotivos);

 							 if ( $totalListaMotivos > 0 ) {
 							 	// Se ainda houver itens no array inicial

 							 	for($l = 0; $l < $totalListaMotivos; $l++) {
 							 		// Para cada motivo restante, buscar kit avulso

 							 		$rskitAvulso    = $this->buscaKitAvulso( $listaMotivos[$l]['ositotioid'] );
 							 		$itemkitAvulso  = pg_fetch_object($rskitAvulso);

 							 		$kiosotioid_kit = $itemkitAvulso->kiosotioid_kit; // Código do kit

 							 		if (!empty($kiosotioid_kit) && $kiosotioid_kit > 0) {
 							 			// Se este motivo possuir um kit avulso

 							 			// Verifica se o kit já não foi inserido
 							 			$kitInserido = false;

 							 			for($y = 0; $y < count($listaKitsInstalados); $y++) {

 							 				if ($listaKitsInstalados[$y]['ositotioid'] == $kiosotioid_kit) {

 							 					$kitInserido = true;
 							 				}
 							 			}

 							 			if ($kitInserido === false) {

 							 				// Adiciona kit
						 					$listaKitsInstalados[] = array(
						 						'ositotioid'		=> $kiosotioid_kit,
												'instalado'			=> false
						 					);

						 					// Remove motivo da lista de análise
						 					unset( $listaMotivos[$l] );
 							 			}
 							 		}
 							 	}
 							 }
 						}


 						/**
 						 * Se houver kits na lista
 						 */
 						 if (count($listaKitsInstalados) > 0) {

							pg_query($this->conn, 'BEGIN;');
 						 	for ($i = 0; $i < count($listaKitsInstalados); $i++) {

 						 		if ($listaKitsInstalados[$i]['instalado'] === false) {

 						 			$listaKitsInseridos[] = $listaKitsInstalados[$i]['ositotioid'];
 						 			$this->incluirKitInstalacao($listaKitsInstalados[$i]['ositotioid']);
 						 		}
 						 	}
 						 }
 						pg_query($this->conn, 'COMMIT;');
 						return $listaKitsInseridos;
 					}

 				} else {

 					/*
 					 * Buscar todos os motivos do tipo 'Assistência' na OS que está sendo analisada, que
 					 * estejam concluídos. Armazenar esses motivos num ARRAY de motivos a ser analisado.
 					 */

 					//Lista de retorno com id(s) do(s) kit(s) inserido(s) por este processo
 					$listaKitsInseridos = array();

 					//Lista de motivos da Ordem de Serviço do tipo 'Assistência'
 					$listaMotivosAssistencia = array();

 					$rsMotivosAssistencia = $this->buscaMotivosOSAssistencia();

 					/*
 					 * Se houver registros de motivos retornados coloca na lista 'listaMotivosAssistencia'
 					 * Senão interrompe o processamento, pois não há motivos cadastrados para a OS.
 					 */
 					if(pg_num_rows($rsMotivosAssistencia) > 0){
 						$listaMotivosAssistencia = pg_fetch_all($rsMotivosAssistencia);
 					} else {
 						return $listaKitsInseridos;
 					}

 					/*
 					 * Lista de kits já incluidos/instalados da ordem de serviço
 					 * Extrai códigos dos kits retornados para a lista 'listaKitsExistentesOS'
 					 */
 					$listaKitsExistentesOS = array();

 					$rsKitsExistentesOS = $this->buscaKitsOrdemServico();

 					if(pg_num_rows($rsKitsExistentesOS) > 0){
 						while(($row = pg_fetch_assoc($rsKitsExistentesOS)) != false){
 							array_push($listaKitsExistentesOS, $row['ositotioid']);
 						}
 					}


 					//Lista de kits que serão processados/analisados posteriormente
 					$listaKitsAssistencia = array();


 					/*
 					 * Se dentro dos motivos retornados, tiver motivo que seja um KIT, ou seja, que o campo
 					 * 'otitipo_kit' for do tipo 'S – ASSISTENCIA', armazenar esses motivos num ARRAY de Kits
 					 * que será usado em todo o processo, e remover esse motivo da lista de Motivos que serão
 					 * analisados nas próximas etapas.
 					 */
 					if(is_array($listaMotivosAssistencia)){
	 					foreach($listaMotivosAssistencia as $key => $motivoAssistencia){

	 						$otitipo_kit = $motivoAssistencia['otitipo_kit'];

	 						if($otitipo_kit == 'S'){

	 							$ositotioid = $motivoAssistencia['ositotioid'];

	 							if(!in_array($ositotioid, $listaKitsAssistencia)) {
	 								array_push($listaKitsAssistencia, $ositotioid);
	 							}

	 							unset($listaMotivosAssistencia[$key]);
	 						}
	 					}
 					}

 					/* Se houver motivos do tipo ASSISTENCIA para analisar, para cada motivo buscar o KIT que
 					 * o motivo esta incluído. Para isso basta consultar na tabela 'kit_instalacao_ordem_servico'
 					 * usando como filtro o campo 'kiosotioid_item' como sendo o motivo que esta sendo analisado,
 					 * desde que o item esteja ativo (campo 'kiosdt_exclusao = NULL')
					 */
 					if(is_array($listaMotivosAssistencia)){
	 					foreach($listaMotivosAssistencia as $key => $motivoAssistencia){

	 						$ositotioid = $motivoAssistencia['ositotioid'];

	 						if(!empty($ositotioid)){

	 							$dadosKit = $this->buscaKitMotivoAssistencia($ositotioid);

	 							/*
	 							 * Se encontrou dados do kit em que o motivo está incluído, pega a chave
	 							 * do kit 'kiosotioid_kit' e adiciona na lista de kits (a menos que
	 							 * este estiver vazio ou já existir no array de kits).
	 							 */
	 							if(is_array($dadosKit))
	 							{
	 								foreach($dadosKit as $kit){
		 								$kiosotioid_kit = $kit['kiosotioid_kit'];

		 								if(!empty($kiosotioid_kit) && !in_array($kiosotioid_kit, $listaKitsAssistencia)){
		 									array_push($listaKitsAssistencia, $kiosotioid_kit);
		 								}
	 								}
	 							}
	 						}
	 					}
 					}

 					/*
 					 * Uma vez finalizado, os KITs encontrados deverão ser incluídos na Ordem de Servico com status
 					 * 'C – CONCLUIDO'. Usar a função 'incluirKitInstalacao' para incluir o KIT. IMPORTANTE: Se for
 					 * um KIT que já existe na OS, esse não deverá ser incluído novamente.
 					 */
 					if(count($listaKitsAssistencia) > 0){

 						pg_query($this->conn, 'BEGIN;');

	 					for($i = 0; $i < count($listaKitsAssistencia); $i++){

	 						$ositotioid = $listaKitsAssistencia[$i];

	 						if(!in_array($ositotioid, $listaKitsExistentesOS)){

	 							$this->incluirKitInstalacao($ositotioid);

	 							$listaKitsInseridos[] = $ositotioid;
	 						}
	 					}

	 					pg_query($this->conn, 'COMMIT;');
 					}

 					return $listaKitsInseridos;
 				}

 			} catch(Exception $e) {
 				pg_query($this->conn, 'ROLLBACK;');
 				throw $e;
 			}

 		} else {
 			throw new Exception("Favor informar uma ordem de serviço");
 		}
 	}


 	/**
 	 * CONSULTA 1
 	 *
 	 * Usada para buscar os motivos da ordem de seviço.
 	 *
 	 * @param $filtraTpKitInstNulo boolean - Flag para indicar se a consulta de
 	 * 		motivos deve ou não filtrar o tipo do kit instalação vazio na consulta.
 	 * 		Esta flag existe devido ao fato de que kits assistência são verificados
 	 * 		utilizando a mesma consulta e eles não possuem este campo cadastrado.
 	 *
 	 * @return pg_result Resultado da Consulta 1.]
 	 * @throws Exception
 	 */
 	public function consultaMotivosOS($filtraTpKitInstVazio = true) {

 		if (!empty($this->ordoid)) {

 			// Monta consulta 1
 			$sqlBuscaMotivos = "
			SELECT
				ordoid,
				ordstatus,
				ositeqcoid,
				eqcdescricao,
				ostoid,
				ostdescricao,
				otidescricao,
				ositstatus,
				ositotioid,
				otiattoid,
				otitipo_kit_instalacao,
				otitipo_kit_visualizacao,
				CASE
					WHEN otitipo_kit_instalacao = 'V' THEN
						(
						SELECT
							kiosotioid_kit
						FROM
							kit_instalacao_ordem_servico
							JOIN os_tipo_item ON otioid = kiosotioid_kit
						WHERE
							kiosotioid_item = ositotioid
							AND otitipo_kit = 'V'
							AND kiosdt_exclusao IS NULL
						)
					ELSE
						NULL
				END AS kit_instalacao
			FROM
				ordem_servico
				JOIN ordem_servico_item ON ordoid=ositordoid
				JOIN os_tipo_item ON otioid=ositotioid
				JOIN os_tipo ON otiostoid=ostoid
				JOIN equipamento_classe ON eqcoid=ositeqcoid
			WHERE
				ordoid=".$this->ordoid."
				AND ositexclusao IS NULL
				AND ositstatus = 'C'";

 			if($filtraTpKitInstVazio){
				$sqlBuscaMotivos .= " AND otitipo_kit_instalacao <> ''";
 			}

			$sqlBuscaMotivos .= " ORDER BY otitipo_kit_instalacao;";

			$rsBuscaMotivos = pg_query($this->conn, $sqlBuscaMotivos);

			if (!$rsBuscaMotivos) {
				throw new Exception("Erro ao executar consulta 1.");
			}
			else {
				return $rsBuscaMotivos;
			}
 		}
 		else {
 			throw new Exception("Favor informar  uma ordem de serviço");
 		}
 	}


 	/**
 	 * Verifica se a ordem de serviço é do tipo assistência.
 	 * @return boolean True, se o tipo da ordem for igual a 4 (Assistência). False se não for.
 	 */
 	public function verificaOSTipoAssistencia() {

 		if (!empty($this->ordoid)) {

 			$rsMotivos = $this->consultaMotivosOS(false);
 			$retorno   = false;

 			while ($motivoOS = pg_fetch_object($rsMotivos)) {
 				if ($motivoOS->ostoid == 4) {
 					$retorno = true;
 				}
 			}

 			return $retorno;
 		}
 		else {
 			throw new Exception("Favor informar  uma ordem de serviço");
 		}
 	}


 	/**
 	 * Busca por Kits já inseridos na ordem de serviço
 	 * @return pg_result
 	 * @throws Exception
 	 */
 	public function buscaKitsOrdemServico() {

 		if (!empty($this->ordoid)) {

 			$sqlBuscaKitsOrdemServico = "
 			SELECT
				ositotioid
			FROM
				ordem_servico_item,
				kit_instalacao_ordem_servico
			WHERE
				ositotioid = kiosotioid_kit
 				AND ositstatus = 'C'
				AND ositordoid  = ".$this->ordoid.
				" AND kiosdt_exclusao IS NULL;";

 			$rsBuscaKitsOrdemServico = pg_query($this->conn, $sqlBuscaKitsOrdemServico);
 			if (!$rsBuscaKitsOrdemServico) {
 				throw new Exception("Erro ao buscar kits da ordem de serviço.");
 			}
 			else {
 				return $rsBuscaKitsOrdemServico;
 			}
 		}
 		else {
 			throw new Exception("Favor informar  uma ordem de serviço");
 		}
 	}

 	/**
 	 * Efetua a busca do kit avulso pelo motivo
 	 * @param integer $otioid OID do motivo
 	 * @return pg_result Retorna o resultado da consulta kit avulso
 	 * @throws Exception
 	 */
 	public function buscaKitAvulso($otioid) {

 		if (!empty($this->ordoid)) {

 			$sqlBuscaKitAvulso = "
 			SELECT
				kiosotioid_kit
			FROM
				kit_instalacao_ordem_servico
				INNER JOIN os_tipo_item ON otioid = kiosotioid_kit
			WHERE
				kiosotioid_item = $otioid
				AND otitipo_kit = 'V'
				AND kiosdt_exclusao IS NULL;";

 			$rsBuscaKitAvulso = pg_query($this->conn, $sqlBuscaKitAvulso);

 			if (!$rsBuscaKitAvulso) {
 				throw new Exception("Erro ao efetuar a busca pelo kit avulso.");
 			}
 			else {
 				return $rsBuscaKitAvulso;
 			}
 		}
 		else {
 			throw new Exception("Favor informar uma ordem de serviço");
 		}
 	}

 	/**
 	 * Consulta 3 - Busca kits e as listas de Atuadores e Motivos
 	 * @param integer $ostoid Código do tipo da ordem de serviço
 	 * @param string $motivos Lista de motivos separados por virgula
 	 * @param string $atuadores Lista de atuadores separados por virgula
 	 * @throws Exception
 	 */
 	public function buscaKitMotivoAtuador($ostoid, $motivos, $atuadores) {

 		$sqlMotivos = '';
 		$sqlAtuadores = '';

 		if (empty($ostoid)) {
 			throw new Exception("É necessario informar um ostoid para a Consulta 3.");
 		}

 		if (!empty($motivos)) {
 			$sqlMotivos = " AND kiosotioid_item IN($motivos) ";
 		}

 		if (!empty($atuadores)) {
 			$sqlAtuadores = "
 			AND kiosotioid_kit IN(
				SELECT
					kiosotioid_kit
				FROM
					kit_instalacao_ordem_servico
					INNER JOIN os_tipo_item ON otioid = kiosotioid_kit
				WHERE kiosdt_exclusao IS NULL
				AND otidt_exclusao IS NULL
				AND	otitipo_kit = 'A'  -- Tipo do Kit
				AND kiosattoid_item IN($atuadores) -- Lista de Atuadores a ser pesquisado
			)";
 		}

 		if (!empty($this->ordoid)) {

 			$sqlBuscaKitMotivoAtuador = "
 			SELECT
				kiosotioid_kit,
				concatena(kiosotioid_item) AS motivo, -- Lista de Motivos que existem nos Kits retornados
				concatena(kiosattoid_item) AS atuador -- Lista de atuadores que existem nos Kits retornados
			FROM
				kit_instalacao_ordem_servico
				INNER JOIN os_tipo_item ON otioid = kiosotioid_kit
			WHERE
				kiosdt_exclusao IS NULL
			AND
				kiosotioid_kit IN(
					SELECT
						kiosotioid_kit
					FROM
						kit_instalacao_ordem_servico
						INNER JOIN os_tipo_item ON otioid = kiosotioid_kit
					WHERE
						otitipo_kit = 'A' -- Tipo do Kit
						AND kiosdt_exclusao IS NULL
						AND otidt_exclusao IS NULL
						AND otiostoid = $ostoid
						$sqlMotivos
						$sqlAtuadores
				)
			GROUP BY
				kiosotioid_kit
 			";

 			$rsBuscaKitMotivoAtuador = pg_query($this->conn, $sqlBuscaKitMotivoAtuador);
 			if (!$rsBuscaKitMotivoAtuador) {
 				throw new Exception("Erro ao executar busca de listas de motivo e atuador, Consulta 3");
 			}
 			else {
 				return $rsBuscaKitMotivoAtuador;
 			}
 		}
 		else {
 			throw new Exception("Favor informar  uma ordem de serviço");
 		}
 	}


 	/**
 	 * Busca Kits com a lista de itens obrigatórios e não obrigatórios
 	 *
 	 * @param integer $ostoid Tipo da Ordem de Serviço
 	 * @param string $atuadores Lista de atuadores separados por vigula, não é um parâmetro obrigatório
 	 * @param string $motivos Lista de motivos separados por vigula
 	 * @return pg_result
 	 * @throws Exception
 	 */
 	public function buscaKitsInstalacao($ostoid, $motivos, $atuadores) {

 		// Se for passado o parâmetro atuadores, será adicionado mais clausulas aqui
 		$sqlAtuadores = '';
 		$ordeqcoid 	  = '';

 		if (empty($this->ordoid)) {
 			throw new Exception("Ordem de serviço não informada");
 		}

 		if (empty($ostoid)) {
 			throw new Exception("É necessário informar o tipo da ordem de serviço");
 		}

 		if (empty($motivos)) {
 			throw new Exception("É necessário informar a lista de motivos separados por vigula");
 		}

 		if (!empty($atuadores)) {

 			$sqlAtuadores = "
 			AND kiosotioid_kit IN(
				SELECT
					kiosotioid_kit
				FROM
					kit_instalacao_ordem_servico
					INNER JOIN os_tipo_item ON otioid = kiosotioid_kit
				WHERE
					otitipo_kit = 'I'  -- Tipo do Kit
					AND kiosdt_exclusao IS NULL
					AND otiostoid = $ostoid -- Tipo do Kit
					AND kiosattoid_item IN($atuadores) -- Lista de Atuadores a ser pesquisado
			)";
 		}

 		// Busca a classe na ordem de serviço
 		$sqlBuscaClasseOrdemServico = "
 		SELECT
			coneqcoid
		FROM
			ordem_servico
			INNER JOIN contrato ON connumero = ordconnumero
		WHERE
			ordoid = ".$this->ordoid."
 		";
 		$rsBuscaClasseOrdemServico = pg_query($this->conn, $sqlBuscaClasseOrdemServico);
 		if (!$rsBuscaClasseOrdemServico) {
 			throw new Exception("Erro ao buscar classe na ordem de serviço");
 		}
 		else {
 			if (pg_num_rows($rsBuscaClasseOrdemServico) > 0) {
 				$ordemServico = pg_fetch_object($rsBuscaClasseOrdemServico);
 			}
 			else {
 				throw new Exception("Erro ao buscar classe na ordem de serviço, sem resultados");
 			}
 		}

 		$sqlBuscaKitInstalacao = "
 		SELECT
			kiosotioid_kit,
			kiosobrigatorio,
			concatena(kiosotioid_item) AS motivo, -- Lista de Motivos que existem nos Kits retornados
			concatena(kiosattoid_item) AS atuador -- Lista de atuadores que existem nos Kits retornados
		FROM
			kit_instalacao_ordem_servico
			INNER JOIN os_tipo_item ON otioid = kiosotioid_kit
		WHERE
			kiosotioid_kit IN(
				SELECT
					kiosotioid_kit
				FROM
					kit_instalacao_ordem_servico
					INNER JOIN os_tipo_item ON otioid = kiosotioid_kit
				WHERE
					otitipo_kit = 'I' -- Tipo do Kit
					AND kiosdt_exclusao IS NULL
					AND otiostoid = $ostoid
					AND kiosotioid_item IN($motivos) -- Lista de motivos a ser pesquisado

					-- Se existir motivos com visualização igual a atuador
					$sqlAtuadores
			)
			AND kiosdt_exclusao IS NULL
			AND kiosotioid_kit IN (
			SELECT
				kiosotioid_kit
			FROM
				kit_instalacao_ordem_servico_classe
			WHERE
				kioscdt_exclusao IS NULL
				AND kiosceqcoid = ".$ordemServico->coneqcoid."
			)
		GROUP BY
			kiosotioid_kit,
			kiosobrigatorio
		ORDER BY
			kiosotioid_kit
 		";
 		$rsBuscaKitInstalacao = pg_query($this->conn, $sqlBuscaKitInstalacao);
 		if (!$rsBuscaKitInstalacao) {
 			throw new Exception("Erro ao buscar lista de kits para instalação");
 		}
 		else {
 			return $rsBuscaKitInstalacao;
 		}
 	}

 	/**
 	 * Insere kit instalação para a ordem de serviço.
 	 *
 	 * @param integer $ostoid OID do Kit de instalação a inserir
 	 * @return boolean Em caso de sucesso retorna true
 	 * @throws Exception
 	 */
 	public function incluirKitInstalacao($otioid) {

 		// Valida entrada
 		if (empty($otioid)) {
 			throw new Exception("É necessario informar o código do kit para inserir");
 		}

 		// Valida preenchimento da ordem de serviço
 		if (!empty($this->ordoid)) {

 			$sqlIncluirKitInstalacao = "
 			INSERT INTO
				ordem_servico_item
				(
					ositotioid,
					ositordoid,
					ositobs,
					ositstatus,
					ositeqcoid
				)
			VALUES
				(
					$otioid,
					".$this->ordoid.",
					'Kit incluído automaticamente',
					'C',
					(
					SELECT
						coneqcoid
					FROM
						ordem_servico
						INNER JOIN contrato ON connumero = ordconnumero
					WHERE
						ordoid = ".$this->ordoid."
					LIMIT 1
					)
				)
			 RETURNING
					ositoid
 			";
 			$rsIncluirKitInstalacao = pg_query($this->conn, $sqlIncluirKitInstalacao);
 			if (!$rsIncluirKitInstalacao) { // Erro de SQL
 				throw new Exception("Erro ao tentar inserir kit de instalação.");
 			}
 			else if (pg_affected_rows($rsIncluirKitInstalacao) == 0) { // Erro de SQL
 				throw new Exception("Erro ao tentar inserir kit de instalação.");
 			}
 			else {

 				if (pg_num_rows($rsIncluirKitInstalacao) > 0) {

 					$cdUsuario 	= isset($_SESSION['usuario']['oid']) ? $_SESSION['usuario']['oid'] : 4873 ;
 					$clioid 	= null;

 					$sqlBuscaClioid = "
 					SELECT
 						conclioid
 					FROM
 						ordem_servico
 						INNER JOIN contrato ON connumero = ordconnumero
 					WHERE
 						ordoid = ".$this->ordoid."
 					";
 					$rsBuscaClioid = pg_query($this->conn, $sqlBuscaClioid);
 					if ($rsBuscaClioid && pg_num_rows($rsBuscaClioid) > 0) {

 						$clioid = pg_fetch_object($rsBuscaClioid);
 					}

	 				$item 		= pg_fetch_object($rsIncluirKitInstalacao);
	 				$observacao = "Geração de comissão automatica dos Kits de Instalação";

	 				$parametros = "\"".$item->ositoid."\" \"".$this->ordoid."\" \"".$clioid->conclioid."\" \"".$cdUsuario."\" \"".$observacao."\"";
	 				$sqlGeraComissao = "SELECT comissao_tecnica_i('".$parametros."') AS retorno";

	 				$rsGeraComissao = pg_query($this->conn, $sqlGeraComissao);
	 				if (!$rsGeraComissao) {
	 					throw new Exception("Erro ao gerar comissão do kit $otioid.");
	 				}
 				}

 				return true;
 			}
 		}
 		else {
 			throw new Exception("Nenhuma ordem de serviço informada.");
 		}
 	}

 	/**
 	 * Busca motivos do tipo 'assistência' da Ordem de Serviço analisada
 	 * que estejam com status 'C' - concluído
 	 * @return pg_result Retorna o resultado da consulta motivos assistência
 	 * @throws Exception
 	 */
 	public function buscaMotivosOSAssistencia() {

 		if (!empty($this->ordoid)) {

 			$sqlBuscaMotivoAssist = " SELECT ordoid, ordstatus, ositeqcoid, eqcdescricao, ostoid, ostdescricao,
									  otidescricao, ositstatus, ositotioid, otiattoid, otitipo_kit
									  FROM ordem_servico
									  INNER JOIN ordem_servico_item ON ordoid	 = ositordoid
									  INNER JOIN os_tipo_item 		ON otioid 	 = ositotioid
									  INNER JOIN os_tipo 			ON otiostoid = ostoid
									  INNER JOIN equipamento_classe ON eqcoid	 = ositeqcoid
									  WHERE otiostoid = 4
									  AND ositstatus  = 'C'
									  AND ositexclusao IS NULL
									  AND ordoid = ".$this->ordoid.";";

 			$rsBuscaMotivoAssist = pg_query($this->conn, $sqlBuscaMotivoAssist);

 			if (!$rsBuscaMotivoAssist) {
 				throw new Exception("Erro ao efetuar a busca pelos motivos do tipo 'Assistência'.");
 			} else {
 				return $rsBuscaMotivoAssist;
 			}
 		}
 		else {
 			throw new Exception("Favor informar uma ordem de serviço");
 		}
 	}

 	/**
 	 * Busca o(s) kit(s) em que o motivo do tipo assistencia está incluído.
 	 * @return array Array contendo dados do(s) kit(s) correspondente(s) ao motivo
 	 * @throws Exception
 	 */
 	public function buscaKitMotivoAssistencia($ositotioid) {

 		if (!empty($ositotioid)) {

 			$arrKitMotivo = array();

 			$sqlBuscaKitMotivoAssist = " SELECT kiosotioid_kit
 										 FROM kit_instalacao_ordem_servico
										 WHERE kiosotioid_item = $ositotioid
										 AND kiosdt_exclusao IS NULL;";

 			$rsBuscaKitMotivoAssist = pg_query($this->conn, $sqlBuscaKitMotivoAssist);

 			if (!$rsBuscaKitMotivoAssist) {
 				throw new Exception("Erro ao efetuar a busca do kit de motivo assistência (ositotioid: $ositotioid).");
 			} else {

 				$arrKitMotivo = pg_fetch_all($rsBuscaKitMotivoAssist);

 				return $arrKitMotivo;
 			}
 		}
 		else {
 			throw new Exception("Favor informar o parâmetro 'ositotioid'.");
 		}
 	}
 }