<?php
	/**
	 * @author	Felipe F. de Souza Carvalho
	 * @email	fscarvalho@brq.com
	 * @since	22/01/2013
	 *
	 * @author	Leandro Alves Ivanaga
	 * @email	leandroivanaga@brq.com
	 * @since 	30/01/2013
	 **/

	require_once 'lib/config.php';
	require_once 'lib/init.php';

	class CadTestesAtivacaoDAO {


		private $conn;

		public function __construct() {
			global $conn;
			$this->conn = $conn;
		}

		/**
		 * Retorna a listagem de grupos de testes cadastrados
		 */
		public function listarGruposTesteCadastrados(){


			try {
				$gruposCadastrados = array();

				$query = " SELECT epgtpoid AS oid, epgtpdescricao AS desc
						   FROM equipamento_projeto_grupo_teste_planejado
						   WHERE epgtpdt_exclusao IS NULL
						   ORDER BY epgtpdescricao;";

				$result = pg_query($this->conn, $query);

				if($result) {
					while(($row = pg_fetch_assoc($result)) != false){

						//Trata caracteres não utf8 da descrição ...
						$row['desc'] = utf8_encode($row['desc']);

						//Adiciona a linha no array de retorno
						array_push($gruposCadastrados, $row);
					}
				}

			} catch (Exception $e) {
				throw new Exception('Não foi possível recuperar os grupos de testes cadastrados.');
			}

			return $gruposCadastrados;
		}

		/**
		 * Realiza a pesquisa dos testes cadastrados
		 */
		public function pesquisar($idGrupoTeste, $descTeste, $siglaTeste){


			try {

				$resultadoPesquisa = array();

				$query = " SELECT eptpoid AS oid, epgtpdescricao AS grupo, epttdescricao AS desc,
						   epttsigla_teste AS sigla, eptpepttoid AS epttoid,
						   CASE epttacao
						      WHEN 't' THEN 'Ativação'
						      ELSE 'Desativação'
						   END AS acao,
						   CASE epttenvia_configuracao
							  WHEN 't' THEN 'Sim'
							  ELSE 'Não'
						   END AS enviacfg,
						   CASE epttverifica_porta
							  WHEN 't' THEN 'Sim'
							  ELSE 'Não'
						   END AS verporta,
						   CASE epttindica_telemetria
							  WHEN 't' THEN 'Sim'
							  ELSE 'Não'
						   END AS telemetria,
						   CASE epttindica_satelital
							  WHEN 't' THEN 'Sim'
							  ELSE 'Não'
						   END AS satelital,
						   CASE epttvalida_posicao
							  WHEN 't' THEN 'Sim'
							  ELSE 'Não'
						   END AS valposicao
						   FROM equipamento_projeto_teste_planejado
						   INNER JOIN equipamento_projeto_grupo_teste_planejado ON (eptpepgtpoid = epgtpoid)
						   INNER JOIN equipamento_projeto_tipo_teste_planejado  ON (epttoid = eptpepttoid)
						   WHERE eptpdt_exclusao IS NULL
						   AND epttdt_exclusao IS NULL";

				if(!empty($idGrupoTeste)){
					$query .= " AND eptpepgtpoid = $idGrupoTeste";
				}

				if(!empty($descTeste)){
					$query .= " AND epttdescricao ILIKE '%$descTeste%'";
				}

				if(!empty($siglaTeste)){
					$query .= " AND epttsigla_teste = '$siglaTeste'";
				}

				$query .= " ORDER BY eptpoid;";

				$result = pg_query($this->conn, $query);

				if($result){
					while(($row = pg_fetch_assoc($result)) != false){

						$row['grupo'] 	   = utf8_encode($row['grupo']);
						$row['desc']  	   = utf8_encode($row['desc']);
						$row['acao']  	   = utf8_encode($row['acao']);
						$row['enviacfg']   = utf8_encode($row['enviacfg']);
						$row['verporta']   = utf8_encode($row['verporta']);
						$row['telemetria'] = utf8_encode($row['telemetria']);
						$row['satelital']  = utf8_encode($row['satelital']);
						$row['valposicao'] = utf8_encode($row['valposicao']);

						array_push($resultadoPesquisa, $row);
					}
				}

			} catch(Exception $e){
				throw new Exception('Não foi possível pesquisar os testes cadastrados.');
			}

			return $resultadoPesquisa;
		}

		/**
		 * Função que salva os dados do novo teste
		 */
		public function cadastraNovoTeste (){

			$usuoid = $_SESSION['usuario']['oid'];


			try {
				//// Campos para tabela equipamento_projeto_tipo_teste_planejado
				$epttacao 				= $_POST['cb_acao_teste'];
				$epttverifica_porta 	= $_POST['cb_verifica_porta'];
				$epttsigla_teste 		= utf8_decode($_POST['cmp_sigla_teste']);
				$epttenvia_configuracao = $_POST['cb_envia_configuracao'];
				$epttindica_telemetria	= $_POST['cb_indica_telemetria'];
				$epttindica_satelital 	= $_POST['cb_teste_satelital'];
				$epttvalida_posicao 	= $_POST['cb_valida_posicao'];
				$epttdescricao 			= utf8_decode($_POST['cmp_descricao_teste']);
				$numeroWS				= isset($_POST['cb_numero_ws_teste']) ? $_POST['cb_numero_ws_teste'] : '';
				$exigeVerificacao		= isset($_POST['cb_exige_verificacao']) ? $_POST['cb_exige_verificacao'] : 'false';

				//// Campos para tabela equipamento_projeto_teste_planejado
                $eptpobrigatorio              = $_POST['cb_teste_obrigatorio'];
                $eptpinstrucao                = utf8_decode($_POST['cmp_instrucao_teste']);
                $eptpmsg_teste_ok             = utf8_decode($_POST['cmp_teste_sucesso']);
                $eptpmsg_teste                = utf8_decode($_POST['cmp_teste']);
                $eptpmsg_teste_nok            = utf8_decode($_POST['cmp_teste_insucesso']);
                $eptpepgtpoid                 = $_POST['cb_grupo_testes'];
                $eptpostoid_teste_obrigatorio = isset($_POST['eptpostoid_teste_obrigatorio']) ?  $_POST['eptpostoid_teste_obrigatorio'] : array(4);


				pg_query($this->conn, "BEGIN;");
				//// Inserir na tabela equipamento_projeto_tipo_teste_planejado
				$sql = "INSERT INTO
							equipamento_projeto_tipo_teste_planejado
								(epttdescricao,
								epttacao,
								epttverifica_porta,
								epttsigla_teste,
								epttenvia_configuracao,
								epttindica_telemetria,
								epttindica_satelital,
								epttvalida_posicao,
								epttdt_cadastro,
								epttusuoid_cadastro,
								epttnumero_webservice,
								epttexibe_tela_verificacao
								)
						VALUES ('".$epttdescricao."',
								$epttacao,
								$epttverifica_porta,
								'".$epttsigla_teste."',
								$epttenvia_configuracao,
								$epttindica_telemetria,
								$epttindica_satelital,
								$epttvalida_posicao,
								'now()',
								$usuoid,
								".intval($numeroWS).",
								$exigeVerificacao
								)
						RETURNING epttoid";

				$rs = pg_query($this->conn, $sql);

				//// Recuperar o id do teste gerado, para inserir em seguida na tabela equipamento_projeto_teste_planejado
				if(pg_num_rows($rs) > 0 ){
					while ($arrRs = pg_fetch_array($rs)){
						$epttoid = $arrRs['epttoid'];
					}
				}

				$sql = "INSERT INTO
							equipamento_projeto_teste_planejado
								(eptpobrigatorio,
								eptpinstrucao,
								eptpepttoid,
								eptpmsg_teste_ok,
								eptpmsg_teste,
								eptpmsg_teste_nok,
								eptpepgtpoid,
								eptpdt_cadastro,
								eptpusuoid_cadastro,
                                eptpostoid_teste_obrigatorio
								)
						VALUES ($eptpobrigatorio,
								'".$eptpinstrucao."',
								$epttoid,
								'".$eptpmsg_teste_ok."',
								'".$eptpmsg_teste."',
								'".$eptpmsg_teste_nok."',
								$eptpepgtpoid,
								'now()',
								$usuoid,
                                ARRAY[" . implode(',' ,$eptpostoid_teste_obrigatorio) ."]
                                )";

				$rs = pg_query($this->conn, $sql);

				pg_query($this->conn, "COMMIT;");
				return "ok";
				} catch(Exception $e) {
					//die($e->getMessage());
					return "erro";
				}

		}

		/**
		 * Excluir Teste do sistema
		 */

		public function excluiTeste (){

			$eptpoid = ($eptpoid)?$eptpoid:$_POST['eptpoid'];
			$epttoid = ($epttoid)?$epttoid:$_POST['epttoid'];
			$usuoid  = $_SESSION['usuario']['oid'];
			try {
				pg_query($this->conn, "BEGIN;");
				//// Excluir teste da tabela equipamento_projeto_teste_planejado
				$query = "UPDATE equipamento_projeto_teste_planejado
						SET eptpdt_exclusao = 'now()',
								eptpusuoid_exclusao = $usuoid
						WHERE eptpoid = $eptpoid";

				$rs = pg_query($this->conn, $query);

				//// Excluir teste da tabela equipamento_projeto_tipo_teste_planejado
				$query = "UPDATE equipamento_projeto_tipo_teste_planejado
						SET epttdt_exclusao = 'now()',
								epttusuoid_exclusao = $usuoid
						WHERE epttoid = $epttoid";

				$rs = pg_query($this->conn, $query);

				pg_query($this->conn, "COMMIT;");

				return "ok";
			} catch(Exception $e) {
				pg_query($this->this->conn, "ROLLBACK;");
				return "erro";
			}
		}

		/**
		 * Função para carregar os dados de um determinado teste
		 */
		public function carregaDados(){

			try {
				$epttoid = $_POST['eptpoid'];
				$dadosTeste = array();

				$query = "  SELECT eptpoid, epttoid, eptpepgtpoid AS cb_grupo_testes, epttdescricao AS cmp_descricao_teste,
						   	epttsigla_teste AS cmp_sigla_teste, eptpepttoid AS epttoid, eptpinstrucao AS cmp_instrucao_teste,
						   	eptpmsg_teste_ok AS cmp_teste_sucesso, eptpmsg_teste_nok AS cmp_teste_insucesso, eptpmsg_teste as cmp_teste,
						   	uc.nm_usuario AS cmp_usu_cadastro, eptpdt_cadastro AS cmp_data_cadastro,
						   	ua.nm_usuario AS cmp_usu_alteracao, eptpdt_alteracao AS cmp_ultima_alteracao,
						    epttnumero_webservice  AS cb_numero_ws_teste,

						   	CASE epttacao
						      	WHEN 't' THEN 'true'
								ELSE 'false'
						  	END AS cb_acao_teste,
							CASE epttenvia_configuracao
								WHEN 't' THEN 'true'
								ELSE 'false'
							END AS cb_envia_configuracao,
						   	CASE epttverifica_porta
								WHEN 't' THEN 'true'
						   		ELSE 'false'
						   	END AS cb_verifica_porta,
						   	CASE epttindica_telemetria
								WHEN 't' THEN 'true'
						      	ELSE 'false'
						   	END AS cb_indica_telemetria,
						   	CASE epttindica_satelital
							  	WHEN 't' THEN 'true'
						      	ELSE 'false'
						   	END AS cb_teste_satelital,
						   	CASE epttvalida_posicao
							  	WHEN 't' THEN 'true'
						      	ELSE 'false'
						   	END AS cb_valida_posicao,
						   	CASE eptpobrigatorio
							  	WHEN 't' THEN 'true'
						      	ELSE 'false'
						   	END AS cb_teste_obrigatorio,

						   	CASE epttexibe_tela_verificacao
							  	WHEN 't' THEN 'true'
						      	ELSE 'false'
						   	END AS cb_exige_verificacao,
                            eptpostoid_teste_obrigatorio

						   	FROM equipamento_projeto_teste_planejado
						   	INNER JOIN equipamento_projeto_tipo_teste_planejado  ON (epttoid = eptpepttoid)
						   	INNER JOIN usuarios AS uc ON (uc.cd_usuario = eptpusuoid_cadastro)
						   	LEFT JOIN usuarios AS ua ON (ua.cd_usuario = eptpusuoid_alteracao)

						   	WHERE eptpdt_exclusao IS NULL
						   	AND epttdt_exclusao IS NULL
						   	AND eptpoid = $epttoid";

				$result = pg_query($this->conn, $query);

				if($result){

					$row = pg_fetch_assoc($result);
					//// Passar todos os campos do resultado pela função utf8_encode
					//// Caso seja um campo de datas e tenha algum valor, formatar as datas
					foreach ($row as $key => $val)
					{
						if (($key == "cmp_data_cadastro" || $key == "cmp_ultima_alteracao") && ($val != "")){
							$val = date("H:i:s d/m/Y",strtotime($val));
                        }

                        if( $key == 'eptpostoid_teste_obrigatorio' ){
                            $val = str_replace('{', '', $val);
                            $val = str_replace('}', '', $val);
                            $val = explode(',', $val);
                            $dadosTeste[$key] = $val;
                        } else {
                            $dadosTeste[$key] = utf8_encode($val);
                        }
					}
				}

			} catch(Exception $e){
				throw new Exception('Não foi possível buscar os dados do Teste.');
			}
			return $dadosTeste;
		}

		/**
		 * Função para editar os dados do teste
		 */
		public function editarTeste(){

			$usuoid = $_SESSION['usuario']['oid'];
			try {
				//// Campos para tabela equipamento_projeto_tipo_teste_planejado
				$epttoid 				= $_POST['epttoid'];
				$epttacao 				= $_POST['cb_acao_teste'];
				$epttverifica_porta 	= $_POST['cb_verifica_porta'];
				$epttenvia_configuracao = $_POST['cb_envia_configuracao'];
				$epttindica_telemetria	= $_POST['cb_indica_telemetria'];
				$epttindica_satelital 	= $_POST['cb_teste_satelital'];
				$epttvalida_posicao 	= $_POST['cb_valida_posicao'];
				$epttdescricao 			= utf8_decode($_POST['cmp_descricao_teste']);
				$numeroWS				= isset($_POST['cb_numero_ws_teste']) ? $_POST['cb_numero_ws_teste'] : '';
				$exigeVerificacao		= isset($_POST['cb_exige_verificacao']) ? $_POST['cb_exige_verificacao'] : 'false';


				//// Campos para tabela equipamento_projeto_teste_planejado
                $eptpoid                      = $_POST['eptpoid'];
                $eptpobrigatorio              = $_POST['cb_teste_obrigatorio'];
                $eptpinstrucao                = utf8_decode($_POST['cmp_instrucao_teste']);
                $eptpmsg_teste_ok             = utf8_decode($_POST['cmp_teste_sucesso']);
                $eptpmsg_teste                = utf8_decode($_POST['cmp_teste']);
                $eptpmsg_teste_nok            = utf8_decode($_POST['cmp_teste_insucesso']);
                $eptpepgtpoid                 = $_POST['cb_grupo_testes'];
                $eptpostoid_teste_obrigatorio = isset($_POST['eptpostoid_teste_obrigatorio']) ?  $_POST['eptpostoid_teste_obrigatorio'] : array(4);

				pg_query($this->conn, "BEGIN;");

				//// Editar o teste na tabela equipamento_projeto_teste_planejado
				$query = "	UPDATE equipamento_projeto_teste_planejado
							SET
									eptpobrigatorio 			= $eptpobrigatorio,
									eptpinstrucao 				= '".$eptpinstrucao."',
									eptpmsg_teste_ok 			= '".$eptpmsg_teste_ok."',
									eptpmsg_teste				= '".$eptpmsg_teste."',
									eptpmsg_teste_nok 			= '".$eptpmsg_teste_nok."',
									eptpepgtpoid 				= $eptpepgtpoid,
									eptpdt_alteracao 			= 'now()',
									eptpusuoid_alteracao 		= $usuoid,
                                    eptpostoid_teste_obrigatorio = ARRAY[" . implode(',' ,$eptpostoid_teste_obrigatorio) ."]
							WHERE eptpoid = $eptpoid";

				$rs = pg_query($this->conn, $query);

				//// Editar o teste na tabela equipamento_projeto_tipo_teste_planejado
				$query = "	UPDATE equipamento_projeto_tipo_teste_planejado
							SET
									epttacao 					= $epttacao,
									epttverifica_porta  		= $epttverifica_porta,
									epttenvia_configuracao 		= $epttenvia_configuracao,
									epttindica_telemetria		= $epttindica_telemetria,
									epttindica_satelital 		= $epttindica_satelital,
									epttvalida_posicao 			= $epttvalida_posicao,
									epttdescricao 				= '".$epttdescricao."',
									epttdt_alteracao 			= 'now()',
									epttusuoid_alteracao 		= $usuoid,
									epttnumero_webservice		= ".intval($numeroWS).",
									epttexibe_tela_verificacao	= $exigeVerificacao
							WHERE epttoid = $epttoid";


				$rs = pg_query($this->conn, $query);

				pg_query($this->conn, "COMMIT;");
				return "ok";
			} catch(Exception $e) {
			//die($e->getMessage());
				return "erro";
			}

		}

		/**
		* Recupera os dados de WS para testes
		*
		* @return array
		*
		*/
		public function listarWebServicesTeste(){

			$dados = array();

			$sql = "
				SELECT
					pcsioid as ws
				FROM
					parametros_configuracoes_sistemas
				INNER JOIN
					parametros_configuracoes_sistemas_itens ON (pcsipcsoid = pcsoid)
				WHERE
					pcsoid = 'WS-PORTAL'
				ORDER BY
				pcsioid::INTEGER";

			$rs = pg_query($this->conn, $sql);

			while($tupla = pg_fetch_object($rs)) {
				$dados[] = $tupla;
			}

			return $dados;

		}

        public function recuperarTipoOrdemServico( ) {

            $dados = array();

            $sql = "SELECT DISTINCT ostoid, initcap(ostdescricao) AS ostdescricao
                    FROM os_tipo
                    WHERE ostdt_exclusao IS NULL
                    ORDER BY ostdescricao";

            $rs = pg_query($this->conn, $sql);

            while($tupla = pg_fetch_object($rs)) {
                $dados[] = $tupla;
            }

            return $dados;
        }
	}
?>
