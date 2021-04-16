<?php
/**
 * Sascar - Sistema Corporativo
 *
 * LICENSE
 *
 * Sascar Tecnologia Automotiva S/A - Todos os Direitos Reservados
 *
 * @author Rafael Dias <rafael.dias@meta.com.br>
 * @version 13/11/2013
 * @since 13/11/2013
 * @package Core
 * @subpackage Classe Controladora de Contratos (Modelo Antigo)
 * @copyright Copyright (c) Sascar Tecnologia Automotiva S/A (http://www.sascar.com.br)
 */
namespace module\ContratoAntigo;

use infra\ComumController,
infra\Helper\Response,
infra\Helper\Mascara,
infra\Helper\Validacao,
module\ContratoAntigo\ContratoAntigoModel as Modelo,
module\ContratoAntigo\ContratoAntigoController as Controlador,
module\Cliente\ClienteService as Cliente,
module\Veiculo\VeiculoService as Veiculo,
module\Contrato\ContratoController;

class ContratoAntigoController extends ContratoController{	
	
	/**
	 * Insere/cria uma proposta nova (modelo antigo [pré-cadastro])
	 *
	 * @author Rafael Dias <rafael.dias@meta.com.br>
	 * @version 14/11/2013
	 * @param array $propostaDadosArray ()
	 * @param array $propostaPagamentoArray ()
	 * @param array $propostaContato1Array ()
	 * @param array $propostaContato2Array ()
	 * @param array $propostaContato3Array ()
	 * @param array $propostaComercialArray ()
	 * @param array $propostaServicoList ()
	 * @param array $propostaGerenciadoraArray ()
	 * @param bool $transaction ()
	 * @return Response $response ($response->dados: $prpoid/false)
	 */
	public function propostaCria(
		$propostaDadosArray=array(),
		$propostaPagamentoArray=array(),
		$propostaContato1Array=array(),
		$propostaContato2Array=array(),
		$propostaContato3Array=array(),
		$propostaComercialArray=array(),
		$propostaServicoList=array(),
		$propostaGerenciadoraArray=array(),
		$propostaItemArray=array(),
		$transaction=true
	) {
		
		try{
			$modelo = new Modelo();
			$response = new Response();
			$controlador = new Controlador();
						
			# Início da transação ##################################################################
			if ($transaction){
				$modelo->prpDAO->startTransaction();
			}
			
			# Insert de proposta ###################################################################
			$prpoid = $modelo->propostaInsert($propostaDadosArray['prptppoid'], $propostaDadosArray['prptpcoid'], $propostaDadosArray['prpusuoid']);	
			if(!$prpoid){
				throw new \Exception('Erro ao criar proposta!','1');
			}			
			# Info de proposta_histórico -----------------------------------------------------------
			$prphobs  = 'Proposta criada com sucesso!';
			$histoticoObj = $controlador->propostaGravaHistorico($prpoid, $propostaDadosArray['prpusuoid'], $prphobs);
			$prphoid = $histoticoObj->dados;			
			if(!$prphoid){
				throw new \Exception($histoticoObj->mensagem,'');
			}
			$response->setResult($prphobs, '0');
			
			# Info de proposta #####################################################################
			$connumero = $modelo->contratoGetConnumero();
			if (!$connumero) {
				throw new \Exception('Erro ao gerar número de contrato!',1);
			}
			$propostaDadosArray['prptermo'] = $connumero;
			$propostaObj = $controlador->propostaAtualiza($prpoid, $propostaDadosArray);
			if (!$propostaObj->dados && count($propostaDadosArray)>0){
				throw new \Exception($propostaObj->mensagem,1);
			}
			
			# Info de cliente ######################################################################
			$propostaClienteArray = array(
					'clino_cgc' 		=> $propostaDadosArray['prpno_cpf_cgc'], 		// CNPJ gerado on-line
					'clitipo' 			=> $propostaDadosArray['prptipo_pessoa_prop'], 	// Tipo do cliente
					'clinome' 			=> $propostaDadosArray['prpproprietario'], 		// Nome do Cliente
					'cliemail' 			=> $propostaDadosArray['prpemail'], 			// E-mail do cliente
					'clireg_simples' 	=> $propostaDadosArray['prpoptante_simples'] 	// Optante simples
			);
			if (count($propostaClienteArray)>0){
				$clienteObj = Cliente::clienteInclui($propostaDadosArray['prpusuoid'], $propostaClienteArray);
				$clioid = $clienteObj->dados;
				if(!$clioid){
					throw new \Exception($clienteObj->mensagem,1);
				}
				$controlador->propostaSetaCliente($prpoid, $clioid);
			}
			
			# Info de endereços ####################################################################
			$propostaClienteEndereco1Array = array(
					'endno_cep' 		=> $propostaDadosArray['prpno_cep1'], 			// CEP
					'enduf' 			=> $propostaDadosArray['prpuf1'], 				// UF
					'endcidade' 		=> $propostaDadosArray['prpcidade1'], 			// Cidade
					'endbairro' 		=> '', 											// Bairro
					'endlogradouro' 	=> $propostaDadosArray['prpendereco1'], 		// Rua
					'endno_numero'		=> $propostaDadosArray['prpno_endereco1'], 		// Número
					'endcomplemento'	=> $propostaDadosArray['prpcompl1'],			// Complemento
					'endddd' 			=> '', 											// DDD
					'endfone' 			=> $propostaDadosArray['prpfone1']				// Número fone
			);
			$propostaClienteEndereco2Array = array(
					'endno_cep' 		=> $propostaDadosArray['prpno_cep2'], 			// CEP
					'enduf' 			=> $propostaDadosArray['prpuf2'], 				// UF
					'endcidade' 		=> $propostaDadosArray['prpcidade2'], 			// Cidade
					'endbairro' 		=> '', 											// Bairro
					'endlogradouro' 	=> $propostaDadosArray['prpendereco2'], 		// Rua
					'endno_numero'		=> $propostaDadosArray['prpno_endereco2'], 		// Número
					'endcomplemento'	=> $propostaDadosArray['prpcompl2'],			// Complemento
					'endddd' 			=> '', 											// DDD
					'endfone' 			=> $propostaDadosArray['prpfone4']				// Número fone
			);
			//principal
			if (count($propostaClienteEndereco1Array)>0){
				Cliente::clienteEnderecoInclui($clioid, $propostaDadosArray['prpusuoid'], $propostaClienteEndereco1Array, 'P');
			}
			//cobrança
			if (count($propostaClienteEndereco2Array)>0){
				Cliente::clienteEnderecoInclui($clioid, $propostaDadosArray['prpusuoid'], $propostaClienteEndereco2Array, 'C');
			}
			# Info de veículo ######################################################################
			$arrayVeiculo = array(
					'veiplaca' 			=> $propostaDadosArray['prpplaca'], 			// placa
					'veino_renavan' 	=> $propostaDadosArray['prprenavam'], 			// renavan
					'veichassi' 		=> $propostaDadosArray['prpchassi'], 			// chassi
					'veimlooid' 		=> $propostaDadosArray['prpmlooid'],			// ID do modelo
					'veicor' 			=> $propostaDadosArray['prpcor'], 				// cor
					'veino_ano' 		=> $propostaDadosArray['prpno_ano'] 			// ano
			);
			$veiculoObj = Veiculo::veiculoInclui($propostaDadosArray['prpusuoid'], $arrayVeiculo);
    		$veioid = $veiculoObj->dados;
			if(!$veioid){
				throw new \Exception($veiculoObj->mensagem,'');
			}
			# Info de proprietário #################################################################
			$arrayProprietario = array(
					'veipveioid' 		=> $veioid, 									// Id do veículo
					'veiptipopessoa' 	=> $propostaDadosArray['prptipo_pessoa_prop'], 	// Tipo pessoa
					'veipnome' 			=> $propostaDadosArray['prpproprietario'], 		// Nome do Proprietário
					'veipcnpjcpf' 		=> $propostaDadosArray['prpno_cpf_cgc_prop'], 	// CNPJ
					'veipcep' 			=> $propostaDadosArray['prpno_cep1'], 			// CEP
					'veipuf' 			=> $propostaDadosArray['prpuf1'], 				// Tipo pessoa
					'veipcidade' 		=> $propostaDadosArray['prpcidade1'], 			// Cidade
					'veipbairro' 		=> '', 											// Bairro
					'veiplogradouro' 	=> $propostaDadosArray['prpendereco1'], 		// Rua
					'veipnumero' 		=> $propostaDadosArray['prpno_endereco1'], 		// Número
					'veipcomplemento' 	=> $propostaDadosArray['prpcompl1'], 			// Complemento
					'veipfone' 			=> $propostaDadosArray['prpfone1'] 				// Telefone
			);
			Veiculo::veiculoProprietarioInclui($veioid, $propostaDadosArray['prpusuoid'], $arrayProprietario);
			
			# Info de proposta_contato #############################################################
			//contato1
			if (count($propostaContato1Array)>0){
				$contato1Obj = $controlador->propostaContatoInclui($prpoid, $propostaContato1Array);
				$prcoid1 = $contato1Obj->dados;
				if(!$prcoid1){
					throw new \Exception($contato1Obj->mensagem,1);
				}
			}
			//contato2
			if (count($propostaContato2Array)>0){
				$contato2Obj = $controlador->propostaContatoInclui($prpoid, $propostaContato1Array);
				$prcoid2 = $contato2Obj->dados;
				if(!$prcoid2){
					throw new \Exception($contato2Obj->mensagem,1);
				}
			}
			//contato3
			if (count($propostaContato3Array)>0){
				$contato3Obj = $controlador->propostaContatoInclui($prpoid, $propostaContato1Array);
				$prcoid3 = $contato3Obj->dados;
				if(!$prcoid3){
					throw new \Exception($contato3Obj->mensagem,1);
				}
			}
			
			# Info de proposta_comissao ############################################################
			if (count($propostaComercialArray)>0){
				$comercialObj = $controlador->propostaSetaComercial($prpoid, $propostaDadosArray['prpusuoid'], $propostaComercialArray);
				$pcomoid = $comercialObj->dados;
				if(!$pcomoid){
					throw new \Exception($comercialObj->mensagem,1);
				}
			}
			
			# Info de proposta_servico #############################################################
			if (count($propostaServicoList)>0){
				foreach ($propostaServicoList AS $propostaServicoArray){
					$propostaServicoArray['prosvalor_tabela'] = 0;
					$prosoid = $modelo->propostaServicoInsert($prpoid, $propostaDadosArray['prpusuoid'], $propostaServicoArray);
					if(!$prosoid){
						throw new \Exception('Erro ao criar registro de proposta_servico!',1);
					}
				}
			}
			
			# Info de proposta_gerenciadora ########################################################
			foreach ($propostaGerenciadoraArray AS $item){
				$gerenciadoraObj = $controlador->propostaGerenciadoraInclui($prpoid, $propostaDadosArray['prpusuoid'], $item['prggeroid'], $item['prgsequencia']);
				$prgoid = $gerenciadoraObj->dados;
				if(!$prgoid){
					throw new \Exception($gerenciadoraObj->mensagem,1);
				}
			}
			
			# Info de Item #########################################################################			
			if (count($propostaItemArray)>0){
				$itemObj = $controlador->propostaItemInclui($prpoid, $propostaDadosArray['prpusuoid'], $propostaItemArray);
				$pritoid = $itemObj;
				if(!$pritoid){
					throw new \Exception($itemObj->mensagem,1);
				}
			}
			
			# Info de proposta_pagamento ###########################################################
			if (count($propostaPagamentoArray)>0){
				$pagamentoObj = $controlador->propostaSetaPagamento($prpoid, $propostaDadosArray['prpusuoid'], $propostaPagamentoArray);
				$ppagoid = $pagamentoObj->dados;
				if(!$ppagoid){
					throw new \Exception($pagamentoObj->mensagem,1);
				}
				$propostaObj = $controlador->propostaAtualiza($prpoid, array('prpprpcoid'=>$prpprpcoid));
				if (!$propostaObj->dados){
					throw new \Exception($propostaObj->mensagem,1);
				}
			}
			
			# Final da transação ###################################################################
			if ($transaction){
				$modelo->prpDAO->commitTransaction();
			}			
			
		} catch (\Exception $e) {
			if ($transaction){
				$modelo->prpDAO->rollbackTransaction();
			}
			$response->setResult($e, 'EXCEPTION');
		}
	
		return $response;
	}
}