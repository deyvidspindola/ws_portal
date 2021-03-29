<?php
//Defines Padroes
define("_SISTEMA_NOME_"      , "PORTAL_SERVICOS");
define('_TITULO_', "SASCAR - Sistema Ativo de Segurança Automotiva");
define('_PROTOCOLO_', "http://");


// Logs PHP PORTAL DE SERVICOS
# Define para o caminho dos LOGs
define('_PATH_LOG_', '/var/www/log/');
# Define para o caminho do LOG de comandos e ultima posicao
define('_LOG_PORTAL_COMANDOS_', _PATH_LOG_ . 'ws_portal_comandos_desenv_' . date('Ymd') . '.log');
# Define para o caminho do LOG de parser
define('_LOG_XML_PARSER_', _PATH_LOG_ . 'ws_portal_xml_parser_desenv_' . date('Ymd') . '.log');
# Define para o caminho do LOG de posicao valida
define('_LOG_POSICAO_VALIDA_', _PATH_LOG_ . 'ws_portal_posicaovalida_desenv_' . date('Ymd') . '.log');
# Define para o caminho do LOG das Integrações com Webservices
define('_LOG_WEBSERVICES_', _PATH_LOG_ . 'ws_portal_integracao_desenv_' . date('Ymd') . '.log');
# Define para o caminho do LOG do nusoap
define('_LOG_PORTAL_NUSOAP_', _PATH_LOG_ . 'ws_portal_nusoap_desenv_' . date('Ymd') . '.log');


/** Configuração variáveis de Ambiente variáveis de Log */
define('_DIRETORIO_LOG_', '/var/www/log/');

define('_SITEDIR_'           , "http://172.16.2.57/desenvolvimento/sistemaWeb/");
define('_MODULEDIR_'         , "/var/www/html/modulos/");
define('_PORTALDIR_'         , "/var/www/html/");
define('_SITEURL_'  , "desenvolvimento.sascar.com.br/sistemaWeb/");
define('_WS_PORTAL_', "http://172.18.0.1:8000/");
define('_URLINTRA_', "desenvolvimento.sascar.com.br/sistemaWeb/");
define('_XMLRPCGERAL_', "http://telemetriadev1.sascar.com.br/xmlrpc/enviar_comando_geral");
define("_AMBIENTE_", "DESENVOLVIMENTO");

/** FIM BLOCO: CONFIGURAÇÕES ACESSO GERENCIADORA */
define('__INSTRUCAO_TESTE__', false);
define('__VEIOID_TESTE__', '708752');
define('__ESN_TESTE__', '1831293836');
define('__PLACA_TESTE__', '0001DSV');

# Define o proxy para teste de teclado
define('_PROXY_', "");

# Define o login para o teste de teclado
define('_LOGIN_TESTE_TECLADO_WS61', "GERENCIADOR");

# Define a senha para o teste de teclado
define('_SENHA_TESTE_TECLADO_WS61', "GERENCIADOR");

# Define o login para o teste de teclado
define('_LOGIN_TESTE_TECLADO_WS62', "portalsascar");

# Define o login para O XML RPC NO MANTIS 7536
define('_LOGIN_XMLRPC', "GERENCIADOR");

# Define a senha para o O XML RPC CRIADO NO MANTIS 7536
define('_SENHA_XMLRPC', "GERENCIADOR");

# Define a senha para o teste de teclado
//define('_SENHA_TESTE_TECLADO_WS62', "ch0kit0");
define('_SENHA_TESTE_TECLADO_WS62', "teste");

define('_REGISTROS_PAGINA_', "10");

// DUM 79785 - Revisão de testes nova plataforma
define('__BASE_POSTGRES__', true);

// --
// DUM 80333 - diretório com arquivos NFe
define('_DANFEDIR_', '/var/www/html/danfe_nfe/');
// --

define('_SASINTEGRA_', 'http://sasintegradev1.sascar.com.br:80/SasIntegra/SasIntegraWSService?WSDL');
define('_XMLRPC_', 'http://10.1.110.9:7010/xmlrpc/enviar_comando');
define('_SERVIDOR_COMANDOS_', '10.1.110.20');
define('_SERVIDOR_BINARIO_', '10.1.110.20');

/*
* Definição de variáveis para CargoTracck
*/
define('_TEMPOESPERAATIVACAO_', 10);
define('_URLAUTENTICACAO_', 'http://hom.cargotracck.com.br/api/ws/login');
define('_URLATIVACAO_', 'http://hom.cargotracck.com.br/api/ws/ativar');
define('_URLDISPONIBILIDADE_', 'http://hom.cargotracck.com.br/api/ws/disponivel');
define('_URLPOSICAO_', 'http://hom.cargotracck.com.br/api/ws/posicao');
define('_URLDESATIVACAO_', 'http://hom.cargotracck.com.br/api/ws/desativar');
define('_URLSUBSTITUIR_', 'http://hom.cargotracck.com.br/api/ws/substituir');
define('_CPFDESATIVACAO_', "186.251.023-79");
define('_URLCALLBACK_', _PROTOCOLO_ . _URLINTRA_ . 'callback_ct.php');

# ******** URL Integração WS Sasweb ********
define('_WS_SASWEB_', 'http://backdev1.sascar.com.br/');

/*
* Definição das configurações do serviço de mapa
*/
define('_IP_SERVICO_MAPA_', '172.16.16.59');
define('_PORTA_SERVICO_MAPA_', '8080');

define('_EMAIL_TESTE_', 'teste_desenv@sascar.com.br'); // ATENÇÃO!!! NÃO ALTEERAR O E-MAIL DE TESTES!!!

/*
* Definição WS DECARTA (Retorna endereço por coordenadas)
*/
define('_REVGEO_DECARTA_', 'http://revgeowebdev1.sascar.com.br/revgeoWeb-webapp/RevGeo?type=xml&x=$long2&y=$lat2&type=xml');

/*
* Definição do WS do Google Maps (em teste usar o IP fixo porque o DNS está com problema)
*/
define('_URL_GOOGLE_MAPS_', 'http://revgeowebdev1.sascar.com.br/revgeoWeb-webapp/RevGeo');


define('_DBSTRING_SIGGO_DBNAME_' , 'sascar_desenvolvimento');
define('_DBSTRING_SIGGO_HOST_' , '10.1.101.14');
define('_DBSTRING_SIGGO_USER_' , 'emerson.chiesse');
define('_DBSTRING_SIGGO_PASSWORD_' , '');

define('_DBSTRING_GERENCIADORA_HOST_' , '10.1.110.2');
define('_DBSTRING_GERENCIADORA_DBNAME_' , 'gerenciadora');
define('_DBSTRING_GERENCIADORA_USER_' , 'emerson.chiesse');
define('_DBSTRING_GERENCIADORA_PASSWORD_' , '');

define('_DBSTRING_BDGERENCIADORA_DOIS_DBNAME_' , 'gerenciadora2');
define('_DBSTRING_BDGERENCIADORA_DOIS_HOST_' , '10.1.110.2');
define('_DBSTRING_BDGERENCIADORA_DOIS_USER_' , 'emerson.chiesse');
define('_DBSTRING_BDGERENCIADORA_DOIS_PASSWORD_' , '');

define('_DBSTRING_CALLCENTER_DBNAME_' , 'callcenter');
define('_DBSTRING_CALLCENTER_HOST_' , '10.1.110.2');
define('_DBSTRING_CALLCENTER_USER_' , 'emerson.chiesse');
define('_DBSTRING_CALLCENTER_PASSWORD_' , '');

define('_DBSTRING_COMANDOS_DBNAME_' , 'servidor_comandos');
define('_DBSTRING_COMANDOS_HOST_' , '10.1.110.2');
define('_DBSTRING_COMANDOS_USER_' , 'emerson.chiesse');
define('_DBSTRING_COMANDOS_PASSWORD_' , '');

define('_DBSTRING_DBNAME_' , 'sascar_desenvolvimento');
define('_DBSTRING_HOST_' , '10.1.110.14');
define('_DBSTRING_USER_' , 'emerson.chiesse');
define('_DBSTRING_PASSWORD_' , '');

define('_DBSTRING_AVL_DBNAME_' , 'avl');
define('_DBSTRING_AVL_HOST_' , '10.1.110.2');
define('_DBSTRING_AVL_USER_' , 'emerson.chiesse');
define('_DBSTRING_AVL_PASSWORD_' , '');

define('_DBSTRING_BDCENTRAL_DBNAME_' , 'bdcentral');
define('_DBSTRING_BDCENTRAL_HOST_' , '10.1.110.2');
define('_DBSTRING_BDCENTRAL_USER_' , 'emerson.chiesse');
define('_DBSTRING_BDCENTRAL_PASSWORD_' , '');

define('_DBSTRING_BLACKBOX_DBNAME_' , 'blackbox_devel');
define('_DBSTRING_BLACKBOX_HOST_' , '10.1.110.2');
define('_DBSTRING_BLACKBOX_USER_' , 'emerson.chiesse');
define('_DBSTRING_BLACKBOX_PASSWORD_' , '');

#define('_DBSTRING_SIGGO_DBNAME_' , 'sascar_desenvolvimento');
#define('_DBSTRING_SIGGO_HOST_' , '10.1.101.14');
#define('_DBSTRING_SIGGO_USER_' , 'erp_cartoes');
#define('_DBSTRING_SIGGO_PASSWORD_' , '');

#define('_DBSTRING_GERENCIADORA_HOST_' , '10.1.110.2');
#define('_DBSTRING_GERENCIADORA_DBNAME_' , 'gerenciadora');
#define('_DBSTRING_GERENCIADORA_USER_' , 'postgres');
#define('_DBSTRING_GERENCIADORA_PASSWORD_' , '');

#define('_DBSTRING_BDGERENCIADORA_DOIS_DBNAME_' , 'gerenciadora2');
#define('_DBSTRING_BDGERENCIADORA_DOIS_HOST_' , '10.1.110.2');
#define('_DBSTRING_BDGERENCIADORA_DOIS_USER_' , 'sascar');
#define('_DBSTRING_BDGERENCIADORA_DOIS_PASSWORD_' , '');

#define('_DBSTRING_CALLCENTER_DBNAME_' , 'callcenter');
#define('_DBSTRING_CALLCENTER_HOST_' , '10.1.110.2');
#define('_DBSTRING_CALLCENTER_USER_' , 'postgres');
#define('_DBSTRING_CALLCENTER_PASSWORD_' , '');

#define('_DBSTRING_COMANDOS_DBNAME_' , 'servidor_comandos');
#define('_DBSTRING_COMANDOS_HOST_' , '10.1.110.2');
#define('_DBSTRING_COMANDOS_USER_' , 'postgres');
#define('_DBSTRING_COMANDOS_PASSWORD_' , '');

#define('_DBSTRING_DBNAME_' , 'sascar_desenvolvimento');
#define('_DBSTRING_HOST_' , '10.1.110.14');
#define('_DBSTRING_USER_' , 'postgres');
#define('_DBSTRING_PASSWORD_' , '');

#define('_DBSTRING_AVL_DBNAME_' , 'avl');
#define('_DBSTRING_AVL_HOST_' , '10.1.110.2');
#define('_DBSTRING_AVL_USER_' , 'portal');
#define('_DBSTRING_AVL_PASSWORD_' , 'p0rt4lh3ll');

#define('_DBSTRING_BDCENTRAL_DBNAME_' , 'bdcentral');
#define('_DBSTRING_BDCENTRAL_HOST_' , '10.1.110.2');
#define('_DBSTRING_BDCENTRAL_USER_' , 'portal');
#define('_DBSTRING_BDCENTRAL_PASSWORD_' , 'p0rt4lh3ll');

#define('_DBSTRING_BLACKBOX_DBNAME_' , 'blackbox_devel');
#define('_DBSTRING_BLACKBOX_HOST_' , '10.1.110.2');
#define('_DBSTRING_BLACKBOX_USER_' , 'postgres');
#define('_DBSTRING_BLACKBOX_PASSWORD_' , '');

//       define('ORA_USER' , 'EVANDERLEI');
//       define('ORA_SENHA' , '5r9k85*');
//       define('ORA_BD' , '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=sas5-scan.sascar.br)(PORT=1521))(CONNECT_DATA=(SERVER=DEDICATED)(SERVICE_NAME=sasdbdv)))');

define('ORA_USER' , 'PRD_WEBCENTER');
define('ORA_SENHA' , 'WeBSASCAR2021');
define('ORA_BD' , '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=10.1.110.47)(PORT=1521))(CONNECT_DATA=(SERVER=DEDICATED)(SERVICE_NAME=devdbpr)))');


define('_ADCONFIG_S1_HOST_', 'sascar.local');
define('_ADCONFIG_S1_PORT_', '389');
define('_ADCONFIG_S1_PROT_', 'ldap://');
define('_ADCONFIG_S1_DOMAIN_', 'sascar.local');
define('_ADCONFIG_S1_MUSER_', 'none');
define('_ADCONFIG_S1_MPASS_', 'none');

define('_ADCONFIG_S2_HOST_', '10.1.107.13');
define('_ADCONFIG_S2_PORT_', '389');
define('_ADCONFIG_S2_PROT_', 'ldap://');
define('_ADCONFIG_S2_DOMAIN_', 'teste.sascar');
define('_ADCONFIG_S2_MUSER_', 'teste');
define('_ADCONFIG_S2_MPASS_', 'sascar123!@');

/*
* Definição de variáveis para API que disponibiliza dados do boleto
*/
define('_API_USER_', 'SASCAR');
define('_API_SENHA_', 'S4SC4R2019');

//        require_once "config_desenvolvimento.php";

$_SESSION["servidor_teste"]     = 1; //? Servidor de teste

$local_lib = explode("/",$_SERVER['SCRIPT_FILENAME']);

$dbstringSiggo = "dbname=". _DBSTRING_SIGGO_DBNAME_ ." host=". _DBSTRING_SIGGO_HOST_ ." user=". _DBSTRING_SIGGO_USER_;
$dbstring_gerenciadora = "host=". _DBSTRING_GERENCIADORA_HOST_ ." dbname="._DBSTRING_GERENCIADORA_DBNAME_." user=". _DBSTRING_GERENCIADORA_USER_;
$dbstring_bdgerenciadoraDois = "dbname=". _DBSTRING_BDGERENCIADORA_DOIS_DBNAME_ ." host=". _DBSTRING_BDGERENCIADORA_DOIS_HOST_ ." user=". _DBSTRING_BDGERENCIADORA_DOIS_USER_;
$conn_bdgerenciadoraDois = pg_connect($dbstring_bdgerenciadoraDois);

/** Conexão com a gerenciadora. */
$dbstring_callcenter = "dbname=". _DBSTRING_CALLCENTER_DBNAME_ ." host=". _DBSTRING_CALLCENTER_HOST_ ." user=". _DBSTRING_CALLCENTER_USER_;

$dbstringComandos = "dbname=". _DBSTRING_COMANDOS_DBNAME_ ." host=". _DBSTRING_COMANDOS_HOST_ ." user=". _DBSTRING_COMANDOS_USER_;
$dbstring = "dbname=". _DBSTRING_DBNAME_ ." host=". _DBSTRING_HOST_ ." user=". _DBSTRING_USER_;
$dbstring_avl = "dbname=". _DBSTRING_AVL_DBNAME_ ." host=". _DBSTRING_AVL_HOST_ ." user=". _DBSTRING_AVL_USER_ ." password=". _DBSTRING_AVL_PASSWORD_;
$conn_avl = "dbname=". _DBSTRING_AVL_DBNAME_ ." host=". _DBSTRING_AVL_HOST_ ." user=". _DBSTRING_AVL_USER_ ." password=". _DBSTRING_AVL_PASSWORD_;
$dbstring_bdcentral = "dbname=". _DBSTRING_BDCENTRAL_DBNAME_ ." host=". _DBSTRING_BDCENTRAL_HOST_ ." user=". _DBSTRING_BDCENTRAL_USER_ ." password=". _DBSTRING_BDCENTRAL_PASSWORD_;
$dbstring_blackbox =  "dbname=". _DBSTRING_BLACKBOX_DBNAME_ ." host=". _DBSTRING_BLACKBOX_HOST_ ." user=". _DBSTRING_BLACKBOX_USER_;

$conn = pg_connect($dbstring);
$connComandos = pg_connect($dbstringComandos);


//ORACLE
$ora_user = ORA_USER;
$ora_senha = ORA_SENHA;
$ora_bd = ORA_BD;

/*
* Configurações AD
*/
# ******** Configurações AD ********
$adConfig = array();
$adKey = 'S1'; // Padrão Intranet (todos os ambientes)
$adConfig[$adKey]['host'] = _ADCONFIG_S1_HOST_;
$adConfig[$adKey]['port'] = _ADCONFIG_S1_PORT_;
$adConfig[$adKey]['prot'] = _ADCONFIG_S1_PROT_;
$adConfig[$adKey]['domain'] = _ADCONFIG_S1_DOMAIN_;
$adConfig[$adKey]['m-user'] = _ADCONFIG_S1_MUSER_;
$adConfig[$adKey]['m-pass'] = _ADCONFIG_S1_MPASS_;
$adKey = 'S2'; // Portal
$adConfig[$adKey]['host'] = _ADCONFIG_S2_HOST_;
$adConfig[$adKey]['port'] = _ADCONFIG_S2_PORT_;
$adConfig[$adKey]['prot'] = _ADCONFIG_S2_PROT_;
$adConfig[$adKey]['domain'] = _ADCONFIG_S2_DOMAIN_;
$adConfig[$adKey]['m-user'] = _ADCONFIG_S2_MUSER_;
$adConfig[$adKey]['m-pass'] = _ADCONFIG_S2_MPASS_;


if (!function_exists('ddlogger')) {
    function ddlogger($txt) {
        $log = _DIRETORIO_LOG_."/log_".date('Y-m-d').".txt";
        $fp = fopen($log, "a+");
        $txt = json_encode([
            'date' => date('Y-m-d H:i:s'),
            'message' => $txt
        ]);
        $txt = $txt."\n";
        fwrite($fp, $txt);
        fclose($fp);
    }
}