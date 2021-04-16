<?php

/**
 * Config responsável pelo Framework
 */
require _SITEDIR_ . 'lib/Atom/Config/config.php';	

/**
 * Recupera o módulo de acordo com o arquivo que incluiu o bootstrap
 */
$module = dirname(__FILE__);	

require __LIBPATH__ . '/Config/bootstrap_laucher.php';	