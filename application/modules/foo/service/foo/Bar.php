<?php

namespace Application\Modules\Foo\Service\Foo;

use MinC\Servico\IServicoRestZend;

class Bar implements IServicoRestZend
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

    public function buscar($codigo)
    {
        $tabelaDbTabela = new \Foo_Model_DbTable_Tabela();
        $where = [
            'Codigo' => $codigo
        ];

        return $tabelaDbTabela->findBy($where);
    }

    public function buscarTodos()
    {
        $tabelaDbTabela = new \Foo_Model_DbTable_Tabela();

        $registros = $tabelaDbTabela->fetchAll();
        return $registros->toArray();
    }

    public function salvar()
    {
        $parametros = $this->request->getParams();
        $tabela = new \Foo_Model_Tabela($parametros);
        $mapper = new \Foo_Model_TabelaMapper();
        $codigo = $mapper->save($tabela);

        return $this->buscar($codigo);
    }

    public function atualizar()
    {
        $parametros = $this->request->getParams();
        $tabela = new \Foo_Model_Tabela($parametros);
        $mapper = new \Foo_Model_TabelaMapper();
        $codigo = $mapper->save($tabela);

        return $this->buscar($codigo);
    }

    public function remover()
    {
        $tabela = $this->buscar($this->request->getParam('id'));
        $mapper = new \Foo_Model_TabelaMapper();
        $id = (int) $tabela['Codigo'];
        $mapper->delete($id);
    }
}
