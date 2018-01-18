<?php

/**
 * Class LogincidadaoControllerTest
 * @author Vinícius Feitosa da Silva <viniciusfesil@mail.com>
 * @since 14/10/2016 12:20
 *
 * Dica : Para executar o teste somente para esse arquivo execute o comando a seguir na raiz do projeto:
 *
 *     ./vendor/bin/phpunit --bootstrap tests/application/Bootstrap.php UnitTest tests/application/modules/autenticacao/controllers/OauthControllerTest.php
 *
 */
class LogincidadaoControllerTest extends MinC_Test_ControllerActionTestCase {

    /* public function testIndex() */
    /* { */
    /*     $this->dispatch('/autenticacao/logincidadao'); */
    /*     $this->assertModule('autenticacao'); */
    /*     $this->assertController('logincidadao'); */
    /*     $this->assertAction('index'); */
    /*     $this->assertRedirect('/autenticacao/logincidadao/oauth2callback'); */
    /* } */

    /*
     * @todo: Tomar como base OPAuth/tests/Opauth/OpauthTest.php
     *
     public function testLogincidadaoAction() {
        $this->dispatch('/autenticacao/oauth/logincidadao');
    }

    /**
     * Make an Opauth config with basic parameters suitable for testing,
     * especially those that are related to HTTP
     *
     * @param array $config Config changes to be merged with the default
     * @return array Merged config

    protected static function configForTest($config = array()) {
        return array_merge(array(
            'host' => 'http://test.example.org',
            'path' => '/',
            'security_salt' => 'testing-salt',
            'strategy_dir' => dirname(__FILE__).'/Strategy/',
            'Strategy' => array(
                'Sample' => array(
                    'sample_id' => 'test_id',
                    'sample_secret' => 'test_secret'
                )
            )
        ), $config);
    }
     */
    /**
     * Instantiate Opauth with test config suitable for testing
     *
     * @param array $config Config changes to be merged with the default
     * @param boolean $autoRun Should Opauth be run right after instantiation, defaulted to false
     * @return object Opauth instance

    protected static function instantiateOpauthForTesting($config = array(), $autoRun = false) {
        $Opauth = new Opauth(self::configForTest($config), $autoRun);
        return $Opauth;
    }
     */
}
