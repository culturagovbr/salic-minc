<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected $_cache;

    protected function _initCoreCache()
    {
        $frontendOptions = array(
            'lifetime' => 7200, // cache lifetime of 2 hours
            'automatic_serialization' => true
        );
        $backendOptions = array(//            'cache_dir' => APPLICATION_PATH . '/../data/cache/' // Directory where to put the cache files
        );

        // getting a Zend_Cache_Core object
        $this->_cache = Zend_Cache::factory(
            'Core',
            'File',
            $frontendOptions,
            $backendOptions
        );
    }

    /**
     * @return Zend_Config
     */
    public function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
//        Zend_Registry::set('config', $config);

        $cacheId = APPLICATION_ENV . __FUNCTION__;
        if (APPLICATION_ENV == 'development' || APPLICATION_ENV == 'testing') $this->_cache->remove($cacheId);
//        if ( ($configObject = $this->_cache->load($cacheId)) === false ) {
        $configPath = APPLICATION_PATH . '/configs';
        $configFileExt = '.ini';
        $handle = opendir($configPath);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                $configName = strstr($file, $configFileExt, true);
                if ($configName) {
                    if ($configName == 'application' || $configName == 'exemplo-application') continue;
                    $config->{$configName} = new Zend_Config_Ini($configPath . '/' . $file, null, array(
                        'allowModifications' => true,
//                          'nestSeparator'      => ':',
//                          'skipExtends'        => false
                    ));

                }
            }
            closedir($handle);
        }
//            $configObject->{'application'}->{APPLICATION_ENV} = new Zend_Config($this->getApplication()->getOptions());
//            $this->_cache->save($config, $cacheId);
//        }

        Zend_Registry::set('config', $config);
        return $config;
    }

    public function _initPath()
    {
//        $strBancoAmbiente      = "bancos_treinamento";

        /* configuracao do caminho dos includes */
        set_include_path('.'
            . PATH_SEPARATOR . APPLICATION_PATH . '/modules/default/controllers'
            . PATH_SEPARATOR . APPLICATION_PATH . '/modules/default/models'
            . PATH_SEPARATOR . APPLICATION_PATH . '/modules/default/models/DAO'
            . PATH_SEPARATOR . APPLICATION_PATH . '/modules/default/models/Exception'
            . PATH_SEPARATOR . APPLICATION_PATH . '/modules/default/models/Servico'
            . PATH_SEPARATOR . APPLICATION_PATH . '/modules/default/models/Table'
            . PATH_SEPARATOR . APPLICATION_PATH . '/modules/default/models/TO'
            . PATH_SEPARATOR . get_include_path()
        );

        /* componente obrigatorio para carregar arquivos, classes e recursos */

        Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true)->setFallbackAutoloader(true);

        /* classes pessoais do ministcrio da cultura */
        require_once "MinC/Loader.php";
    }

    public function _initLocal()
    {
        /* formato, idioma e localizacao */
        setlocale(LC_ALL, 'pt_BR');
//        setlocale(LC_CTYPE, 'de_DE.iso-8859-1');
        date_default_timezone_set('America/Sao_Paulo');

        // Registra currency que sera usado automaticamente pelo zend
        Zend_Registry::set('Zend_Currency', new Zend_Currency('pt_BR'));
    }

    public function _initSession()
    {
        /* manipulacao de sessao */
        Zend_Session::start();
        Zend_Registry::set('session', new Zend_Session_Namespace()); // registra

    }

    public function _initFilter()
    {
        /* varicveis para pegar dados vindos via get e post */
        $filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_StringTrim()); // retira espaaos antes e depois
        $filter->addFilter(new Zend_Filter_StripTags()); // retira ccdigo html e etc
        $options = array('escapeFilter' => $filter);

        /* registra */
        Zend_Registry::set('post', new Zend_Filter_Input(NULL, NULL, $_POST, $options));
        Zend_Registry::set('get', new Zend_Filter_Input(NULL, NULL, $_GET, $options));
    }

    public function _initView()
    {
        /* configuraççes do layout padrão do sistema */
        Zend_Layout::startMvc(array(
            'layout' => 'layout',
            'layoutPath' => APPLICATION_PATH . '/layout/',
            'contentKey' => 'content'));
        # paginacao
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacaoMinc.phtml');

        // Initialize view
        $view = new Zend_View();
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);
        $view->addHelperPath(
            APPLICATION_PATH . '/../library/MinC/View/Helper/',
            'MinC_View_Helper_'
        );
    }

    public function _initRouteRest()
    {
        $controller = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route(
            $controller,
            array(),
            array(
                'default' => array(
                    'proponente-autenticacao-rest',
                    'proponente-rest',
                    'projeto-rest',
                    'dispositivo-movel-rest',
                    'mensagem-rest',
                    'diligencia-rest',
                    'segmento-cultural-rest',
                    'projeto-extrato-rest',
                    'projeto-extrato-ano-rest',
                    'projeto-extrato-mes-rest'
                )));
        $controller->getRouter()->addRoute('rest', $restRoute);
    }

    public function _initCarregarDependenciasComposer()
    {
        if (file_exists('vendor/autoload.php')) {
            require_once 'vendor/autoload.php';
        }
    }

    public function _initRoutes()
    {
        $controllerFront = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route(
            $controllerFront,
            array(),
            array(
                'default' => array(
                    'proponente-autenticacao-rest',
                    'proponente-rest',
                    'projeto-rest',
                    'dispositivo-movel-rest',
                    'mensagem-rest',
                    'diligencia-rest',
                    'projeto-extrato-rest',
                    'projeto-extrato-ano-rest',
                    'projeto-extrato-mes-rest'
                )));
        $controllerFront->getRouter()->addRoute('rest', $restRoute);
    }

    public function _initLayouts()
    {
        /* configuraççes do layout padrão do sistema */
//        Zend_Layout::startMvc(array(
//            'layout'     => 'layout',
//            'layoutPath' => APPLICATION_PATH.'/layout/',
//            'contentKey' => 'content'));
//        # paginacao
//        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacaoMinc.phtml');

        // Initialize view
//        $view         = new Zend_View();
//        $layout         = new Zend_Layout();
//        echo '<pre>';
//        var_dump($layout->name);
//        exit;
//        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
//            'ViewRenderer'
//        );
//        $viewRenderer->setView($view);
//        $view->addHelperPath(
//            APPLICATION_PATH . '/../library/MinC/View/Helper/',
//            'MinC_View_Helper_'
//        );
    }

    public function _initModuloAssinatura()
    {

        if (!class_exists(\Assinatura_Model_DbTable_TbAssinatura::class)) {
            require_once __DIR__ . '/modules/assinatura/models/DbTable/TbAssinatura.php';
        }

        $acoesGerais = new \Application\Modules\Assinatura\Service\Assinatura\Acao\ListaAcoesGerais();
        \MinC\Assinatura\Servico\Assinatura::definirAcoesGerais($acoesGerais);
    }

}
