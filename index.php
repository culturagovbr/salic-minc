<?php

/**
 * Arquivo principal da aplicacao (bootstrap)
 * Define todos os caminhos onde os arquivos estao armazenados
 * Carrega as classes do Zend utilizadas durante toda a aplicacao
 */

define('APPLICATION_PATH', realpath(__DIR__ . DIRECTORY_SEPARATOR . 'application'));

defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

/* configuracao do caminho dos includes */
/* set_include_path('.'. PATH_SEPARATOR . './library/' . PATH_SEPARATOR . get_include_path()); */


set_include_path(
    implode(
        PATH_SEPARATOR,
        [
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/../vendor/zendframework/zendframework1/library')
        ]
    )
);

/** Zend_Application */
require_once APPLICATION_PATH.'/../vendor/zendframework/zendframework1/library/Zend/Application.php';

// Create application, bootstrap, and run
$app = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$app->bootstrap()->run();
