<?php

$ambiente = 'dev';

//Defines Padroes
define("_SISTEMA_NOME_" , "PORTAL_SERVICOS");
define('_TITULO_'       , "SASCAR - Sistema Ativo de Seguranчa Automotiva");
define('_PROTOCOLO_'    , "http://");

// Logs PHP PORTAL DE SERVICOS
# Define para o caminho dos LOGs
define('_PATH_LOG_', '/var/www/log/');
# Define para o caminho do LOG de comandos e ultima posicao
define('_LOG_PORTAL_COMANDOS_', _PATH_LOG_ . 'ws_portal_comandos_desenv_' . date('Ymd') . '.log');
# Define para o caminho do LOG de parser
define('_LOG_XML_PARSER_', _PATH_LOG_ . 'ws_portal_xml_parser_desenv_' . date('Ymd') . '.log');
# Define para o caminho do LOG de posicao valida
define('_LOG_POSICAO_VALIDA_', _PATH_LOG_ . 'ws_portal_posicaovalida_desenv_' . date('Ymd') . '.log');
# Define para o caminho do LOG das Integraчѕes com Webservices
define('_LOG_WEBSERVICES_', _PATH_LOG_ . 'ws_portal_integracao_desenv_' . date('Ymd') . '.log');
# Define para o caminho do LOG do nusoap
define('_LOG_PORTAL_NUSOAP_', _PATH_LOG_ . 'ws_portal_nusoap_desenv_' . date('Ymd') . '.log');
# Define para o caminho do LOG das Integraчѕes com Webservices
define('_LOG_WEBSERVICES_'   ,'/var/www/log/ws_portal_integracao_'.date('Ymd').'.log');
// DUM 80333 - diretѓrio com arquivos NFe
define('_DANFEDIR_', '/var/www/html/danfe_nfe/');
// --

define('_MODULEDIR_'    , "/var/www/html/modulos/");
define('_PORTALDIR_'    , "/var/www/html/");
define('_WS_PORTAL_'    , "http://172.18.0.1:8000/");

switch ($ambiente) {

    case 'dev':
        define('_SITEDIR_'  , "http://172.16.2.57/desenvolvimento/sistemaWeb/");
        define('_SITEURL_'  , "desenvolvimento.sascar.com.br/sistemaWeb/");
        define('_URLINTRA_' , "desenvolvimento.sascar.com.br/sistemaWeb/");
        define('_XMLRPCGERAL_', "http://telemetriadev1.sascar.com.br/xmlrpc/enviar_comando_geral");
        define("_AMBIENTE_", "DESENVOLVIMENTO");

        /** FIM BLOCO: CONFIGURAЧеES ACESSO GERENCIADORA */
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

// DUM 79785 - Revisуo de testes nova plataforma
        define('__BASE_POSTGRES__', true);

// --
// DUM 80333 - diretѓrio com arquivos NFe
        define('_DANFEDIR_', '/var/www/html/danfe_nfe/');
// --

        define('_SASINTEGRA_', 'http://sasintegradev1.sascar.com.br:80/SasIntegra/SasIntegraWSService?WSDL');
        define('_XMLRPC_', 'http://10.1.110.9:7010/xmlrpc/enviar_comando');
        define('_SERVIDOR_COMANDOS_', '10.1.110.20');
        define('_SERVIDOR_BINARIO_', '10.1.110.20');

        /*
        * Definiчуo de variсveis para CargoTracck
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

# ******** URL Integraчуo WS Sasweb ********
        define('_WS_SASWEB_', 'http://backdev1.sascar.com.br/');

        /*
        * Definiчуo das configuraчѕes do serviчo de mapa
        */
        define('_IP_SERVICO_MAPA_', '172.16.16.59');
        define('_PORTA_SERVICO_MAPA_', '8080');

        define('_EMAIL_TESTE_', 'teste_desenv@sascar.com.br'); // ATENЧУO!!! NУO ALTEERAR O E-MAIL DE TESTES!!!

        /*
        * Definiчуo WS DECARTA (Retorna endereчo por coordenadas)
        */
        define('_REVGEO_DECARTA_', 'http://revgeowebdev1.sascar.com.br/revgeoWeb-webapp/RevGeo?type=xml&x=$long2&y=$lat2&type=xml');


        /*
        * Definiчуo do WS do Google Maps (em teste usar o IP fixo porque o DNS estс com problema)
        */
        define('_URL_GOOGLE_MAPS_', 'http://revgeowebdev1.sascar.com.br/revgeoWeb-webapp/RevGeo');


        define('_DBSTRING_SIGGO_DBNAME_' , 'sascar_desenvolvimento');
        define('_DBSTRING_SIGGO_HOST_' , '10.1.101.14');
        define('_DBSTRING_SIGGO_USER_' , 'deyvid.s.ext');
        define('_DBSTRING_SIGGO_PASSWORD_' , 'D##$yvid@1361');

        define('_DBSTRING_GERENCIADORA_HOST_' , '10.1.110.2');
        define('_DBSTRING_GERENCIADORA_DBNAME_' , 'gerenciadora');
        define('_DBSTRING_GERENCIADORA_USER_' , 'deyvid.s.ext');
        define('_DBSTRING_GERENCIADORA_PASSWORD_' , 'D##$yvid@1361');

        define('_DBSTRING_BDGERENCIADORA_DOIS_DBNAME_' , 'gerenciadora2');
        define('_DBSTRING_BDGERENCIADORA_DOIS_HOST_' , '10.1.110.2');
        define('_DBSTRING_BDGERENCIADORA_DOIS_USER_' , 'deyvid.s.ext');
        define('_DBSTRING_BDGERENCIADORA_DOIS_PASSWORD_' , 'D##$yvid@1361');

        define('_DBSTRING_CALLCENTER_DBNAME_' , 'callcenter');
        define('_DBSTRING_CALLCENTER_HOST_' , '10.1.110.2');
        define('_DBSTRING_CALLCENTER_USER_' , 'deyvid.s.ext');
        define('_DBSTRING_CALLCENTER_PASSWORD_' , 'D##$yvid@1361');

        define('_DBSTRING_COMANDOS_DBNAME_' , 'servidor_comandos');
        define('_DBSTRING_COMANDOS_HOST_' , '10.1.110.2');
        define('_DBSTRING_COMANDOS_USER_' , 'deyvid.s.ext');
        define('_DBSTRING_COMANDOS_PASSWORD_' , 'D##$yvid@1361');

        define('_DBSTRING_DBNAME_' , 'sascar_desenvolvimento');
        define('_DBSTRING_HOST_' , '10.1.110.14');
        define('_DBSTRING_USER_' , 'deyvid.s.ext');
        define('_DBSTRING_PASSWORD_' , 'D##$yvid@1361');

        define('_DBSTRING_AVL_DBNAME_' , 'avl');
        define('_DBSTRING_AVL_HOST_' , '10.1.110.2');
        define('_DBSTRING_AVL_USER_' , 'deyvid.s.ext');
        define('_DBSTRING_AVL_PASSWORD_' , 'D##$yvid@1361');

        define('_DBSTRING_BDCENTRAL_DBNAME_' , 'bdcentral');
        define('_DBSTRING_BDCENTRAL_HOST_' , '10.1.110.2');
        define('_DBSTRING_BDCENTRAL_USER_' , 'deyvid.s.ext');
        define('_DBSTRING_BDCENTRAL_PASSWORD_' , 'D##$yvid@1361');

        define('_DBSTRING_BLACKBOX_DBNAME_' , 'blackbox_devel');
        define('_DBSTRING_BLACKBOX_HOST_' , '10.1.110.2');
        define('_DBSTRING_BLACKBOX_USER_' , 'deyvid.s.ext');
        define('_DBSTRING_BLACKBOX_PASSWORD_' , 'D##$yvid@1361');

        define('ORA_USER' , 'PRD_WEBCENTER');
        define('ORA_SENHA' , 'Trocar@2021');
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
         * Definiчуo de variсveis para API que disponibiliza dados do boleto
         */
        define('_API_USER_', 'SASCAR');
        define('_API_SENHA_', 'S4SC4R2019');
        break;

    case 'homol':
        define('_SITEDIR_'      , "http://10.1.101.21/homologacao/sistemaWeb/");
        define('_SITEURL_'      , "hom1-intranet.sascar.com.br/sistemaWeb/");
        define('_URLINTRA_'     , "hom1-intranet.sascar.com.br/sistemaWeb/");
        define('_XMLRPCGERAL_'  , "http://hom1.xmlrpc.sascar.com.br/xmlrpc/enviar_comando_geral");
        define("_AMBIENTE_", "HOMOLOGACAO");

        # Define endereчo do WS do SASINTEGRA
        # Define do endereco do SASINTEGRAV3
        define('_SASINTEGRAV3_', 'http://sasintegrahom1.sascar.com.br/SasIntegra/SasIntegraWSService?wsdl');
        define('_SASINTEGRA_', 'http://10.1.101.4:7010/SasIntegra/SasIntegraWSService?WSDL');

        # ******** URL Integraчуo WS Sasweb ********
        define('_WS_SASWEB_', 'http://backendhom1.sascar.com.br/unificado_backend/');

        # Define o login para o teste de teclado
        define('_LOGIN_TESTE_TECLADO_WS61', "PORTALSASCAR");

        # Define a senha para o teste de teclado
        define('_SENHA_TESTE_TECLADO_WS61', "P0RT4LS4SC4R");

        # Define o login para o teste de teclado
        define('_LOGIN_TESTE_TECLADO_WS62', "portalsascar");

        # Define de login para o teste de telemetria SASINTEGRAV3
        define('_LOGIN_TESTE_TELEMETRIA_SASINTEGRAV3', "portalsascar");

        # Define a senha para o teste de telemetria SASINTEGRAV3
        define('_SENHA_TESTE_TELEMETRIA_SASINTEGRAV3', "TESTE");

        # Define o login para O XML RPC NO MANTIS 7536
        define('_LOGIN_XMLRPC', "GERENCIADOR");

        # Define a senha para o O XML RPC CRIADO NO MANTIS 7536
        define('_SENHA_XMLRPC', "GERENCIADOR");

        # Define a senha para o teste de teclado
        define('_SENHA_TESTE_TECLADO_WS62', "ch0kit0");



        define('_REGISTROS_PAGINA_'         , "10");

        // DUM 79785 - Revisуo de testes nova plataforma
        define('__BASE_POSTGRES__', true);

        // --


        //define('_XMLRPC_', 'http://172.16.2.2:8010/xmlrpc/enviar_comando');
        define('_XMLRPC_', 'http://hom1.xmlrpc.sascar.com.br/xmlrpc/enviar_comando');

        // DEFINE dos Servidores de Comandos e Binario
        define('_SERVIDOR_COMANDOS_', '10.1.101.17');    // Homologaчуo
        define('_SERVIDOR_BINARIO_', '10.1.101.17');     // Homologaчуo

        /*
        * Definiчуo de variсveis para CargoTracck
        */
        define('_TEMPOESPERAATIVACAO_', 10);
        define('_URLAUTENTICACAO_',    'http://hom.cargotracck.com.br/api/ws/login');
        define('_URLATIVACAO_',        'http://hom.cargotracck.com.br/api/ws/ativar');
        define('_URLDISPONIBILIDADE_', 'http://hom.cargotracck.com.br/api/ws/disponivel');
        define('_URLPOSICAO_',         'http://hom.cargotracck.com.br/api/ws/posicao');
        define('_URLDESATIVACAO_',     'http://hom.cargotracck.com.br/api/ws/desativar');
        define('_URLSUBSTITUIR_',       'http://hom.cargotracck.com.br/api/ws/substituir');
        define('_CPFDESATIVACAO_',      "186.251.023-79");
        define('_URLCALLBACK_',         _PROTOCOLO_ . _URLINTRA_ . 'callback_ct.php');

        define('__INSTRUCAO_TESTE__' , false);
        define('__VEIOID_TESTE__' , '170267');
        define('__ESN_TESTE__', '351687030848419');
        define('__PLACA_TESTE__', '0002ENG');

        /*
        * Definiчуo WS DECARTA (Retorna endereчo por coordenadas)
        */
        define('_REVGEO_DECARTA_', 'http://10.1.101.5:8010/revgeoWeb-webapp/RevGeo?type=xml&x=$long2&y=$lat2&type=xml');


        /*
        * Definiчуo do WS do Google Maps (em teste usar o IP fixo porque o DNS estс com problema)
        */
        define('_URL_GOOGLE_MAPS_', 'http://10.1.101.5:8010/revgeoWeb-webapp/RevGeo');


        define('_DBSTRING_SIGGO_DBNAME_' , 'sascar_homologacao');
        define('_DBSTRING_SIGGO_HOST_' , '10.1.101.14');
        define('_DBSTRING_SIGGO_USER_' , 'erp_cartoes');
        define('_DBSTRING_SIGGO_PASSWORD_' , '');

        define('_DBSTRING_GERENCIADORA_HOST_' , '10.1.101.19');
        define('_DBSTRING_GERENCIADORA_DBNAME_' , 'gerenciadora');
        define('_DBSTRING_GERENCIADORA_USER_' , 'portal_servicos');
        define('_DBSTRING_GERENCIADORA_PASSWORD_' , 'p0rt4lservic05');

        define('_DBSTRING_BDGERENCIADORA_DOIS_HOST_' , '10.1.101.19');
        define('_DBSTRING_BDGERENCIADORA_DOIS_DBNAME_' , 'gerenciadora2');
        define('_DBSTRING_BDGERENCIADORA_DOIS_USER_' , 'portal_servicos');
        define('_DBSTRING_BDGERENCIADORA_DOIS_PASSWORD_' , 'p0rt4lservic05');

        define('_DBSTRING_DBNAME_' , 'sascar_homologacao');
        define('_DBSTRING_HOST_' , '10.1.101.14');
        define('_DBSTRING_USER_' , 'portal');
        define('_DBSTRING_PASSWORD_' , 'p0rt4lh3ll');

        define('_DBSTRING_AVL_DBNAME_' , 'avl');
        define('_DBSTRING_AVL_HOST_' , '10.1.101.18');
        define('_DBSTRING_AVL_USER_' , 'portal');
        define('_DBSTRING_AVL_PASSWORD_' , 'p0rt4lh3ll');

        define('_DBSTRING_BDCENTRAL_DBNAME_' , 'bdcentral');
        define('_DBSTRING_BDCENTRAL_HOST_' , '10.1.101.18');
        define('_DBSTRING_BDCENTRAL_USER_' , 'portal');
        define('_DBSTRING_BDCENTRAL_PASSWORD_' , 'p0rt4lh3ll');

        define('_DBSTRING_CALLCENTER_DBNAME_' , 'callcenter');
        define('_DBSTRING_CALLCENTER_HOST_' , '10.1.101.18');
        define('_DBSTRING_CALLCENTER_USER_' , 'callcenter_cco');
        define('_DBSTRING_CALLCENTER_PASSWORD_' , 'ch4p1c4');

        define('_DBSTRING_COMANDOS_DBNAME_' , 'servidor_comandos');
        define('_DBSTRING_COMANDOS_HOST_' , '10.1.101.19');
        define('_DBSTRING_COMANDOS_USER_' , 'portal_servicos');
        define('_DBSTRING_COMANDOS_PASSWORD_' , 'p0rt4lservic05');

        define('_DBSTRING_BLACKBOX_DBNAME_' , 'blackbox');
        define('_DBSTRING_BLACKBOX_HOST_' , '10.1.101.19');
        define('_DBSTRING_BLACKBOX_USER_' , 'blackbox_user');
        define('_DBSTRING_BLACKBOX_PASSWORD_' , '');

        define('ORA_USER' , 'PORTAL_SASCAR');
        define('ORA_SENHA' , 'P0RTS4S');
        define('ORA_BD' , '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=sas4-scan.sascar.com.br)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=sasdbhm.world)))');

        /*
         * Definiчуo de variсveis para API que disponibiliza dados do boleto
         */
        define('_API_USER_', 'SASCAR');
        define('_API_SENHA_', 'S4SC4R2019');
        break;

}

$_SESSION["servidor_teste"]     = 1; //? Servidor de teste

//string de conex?o para a classe ProdutoComSeguro.php
$dbstringSiggo = "dbname=". _DBSTRING_SIGGO_DBNAME_ ." host=". _DBSTRING_SIGGO_HOST_ ." user=". _DBSTRING_SIGGO_USER_." password=". _DBSTRING_SIGGO_PASSWORD_;

$local_lib = explode("/",$_SERVER['SCRIPT_FILENAME']);

$dbstring_gerenciadora = "host=". _DBSTRING_GERENCIADORA_HOST_ ." dbname="._DBSTRING_GERENCIADORA_DBNAME_." user=". _DBSTRING_GERENCIADORA_USER_ ." password=". _DBSTRING_GERENCIADORA_PASSWORD_;
$conn_bdgerenciadora = pg_connect($dbstring_gerenciadora);

$dbstring_bdgerenciadoraDois = "dbname=". _DBSTRING_BDGERENCIADORA_DOIS_DBNAME_ ." host=". _DBSTRING_BDGERENCIADORA_DOIS_HOST_ ." user=". _DBSTRING_BDGERENCIADORA_DOIS_USER_ ." password=". _DBSTRING_BDGERENCIADORA_DOIS_PASSWORD_;
$conn_bdgerenciadoraDois = pg_connect($dbstring_bdgerenciadoraDois);

$dbstring             = "dbname=". _DBSTRING_DBNAME_ ." host=". _DBSTRING_HOST_ ." user=". _DBSTRING_USER_ ." password=". _DBSTRING_PASSWORD_;
$dbstring_bdcentral = "dbname=". _DBSTRING_BDCENTRAL_DBNAME_ ." host=". _DBSTRING_BDCENTRAL_HOST_ ." user=". _DBSTRING_BDCENTRAL_USER_ ." password=". _DBSTRING_BDCENTRAL_PASSWORD_;
$dbstring_callcenter  =  "dbname=". _DBSTRING_CALLCENTER_DBNAME_ ." host=". _DBSTRING_CALLCENTER_HOST_ ." user=". _DBSTRING_CALLCENTER_USER_ ." password=". _DBSTRING_CALLCENTER_PASSWORD_;
$dbstringComandos = "dbname=". _DBSTRING_COMANDOS_DBNAME_ ." host=". _DBSTRING_COMANDOS_HOST_ ." user=". _DBSTRING_COMANDOS_USER_ ." password=". _DBSTRING_COMANDOS_PASSWORD_;
$dbstring_avl 	      = "dbname=". _DBSTRING_AVL_DBNAME_ ." host=". _DBSTRING_AVL_HOST_ ." user=". _DBSTRING_AVL_USER_ ." password=". _DBSTRING_AVL_PASSWORD_;

$conn = pg_connect($dbstring);
$connComandos = pg_connect($dbstringComandos);

$dbstring_blackbox = "dbname=". _DBSTRING_BLACKBOX_DBNAME_ ." host=". _DBSTRING_BLACKBOX_HOST_ ." user=". _DBSTRING_BLACKBOX_USER_." password=". _DBSTRING_BLACKBOX_PASSWORD_;
$conn_blackbox = pg_connect($dbstring_blackbox);
//$connSiggo = pg_connect($dbstringSiggo);

//ORACLE HOMOLOCAGAO
$ora_user = ORA_USER;
$ora_senha = ORA_SENHA;
$ora_bd = ORA_BD;