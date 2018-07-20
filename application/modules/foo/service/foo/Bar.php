<?php

namespace Application\Modules\Foo\Service\Foo;

class Bar
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function soma()
    {
        return [ 'resultado' => 1 + 1 ];
    }

    public function buscar($codigo)
    {
        $tabelaDbTabela = new \Foo_Model_DbTable_Tabela();
        $where = [
            'Codigo' => $codigo
        ];

        return $tabelaDbTabela->findBy($where);
    }

    public function salvarRegistro()
    {
        $parametros = $this->request->getParams();
        $tabela = new \Foo_Model_Tabela($parametros);
        $mapper = new \Foo_Model_TabelaMapper();
        $codigo = $mapper->save($tabela);

        return $this->buscar($codigo);
    }
}