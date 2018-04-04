<?php

class Proposta_Model_DbTable_TbDespacho extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbDespacho';
    protected $_primary = 'idDespacho';

    public function devolverProjetoEncaminhadoParaAssinatura($idPronac, $motivoDevolucao, $idTipoDespacho = 129)
    {
        $this->update(array(
            'stEstado' => 1
        ), array(
            'idPronac = ?' => $idPronac
        ));
        $objTbProjetos = new Projeto_Model_DbTable_Projetos();
        $objProjetos = $objTbProjetos->findBy(array('IdPRONAC' => $idPronac));

        $auth = Zend_Auth::getInstance();

        $dadosInclusao = array(
            'idPronac' => $idPronac,
            'idProposta' => $objProjetos['idProjeto'],
            'Tipo' => $idTipoDespacho,
            'stEncaminhamento' => 0,
            'Data' => $this->getExpressionDate(),
            'Despacho' => $motivoDevolucao,
            'stEstado' => 0,
            'idUsuario' => $auth->getIdentity()->usu_codigo
        );

        return $this->inserir($dadosInclusao);
    }

    public function consultarDespachoAtivo($idPronac)
    {
        $objQuery = $this->select();
        $objQuery->from($this->_name, '*', $this->_schema);
        $objQuery->joinInner(
            "Projetos",
            "Projetos.IdProjeto = TbDespacho.idProposta",
            '',
            $this->_schema
        );

        $objQuery->where('Projetos.idPronac = ?', $idPronac);
        $objQuery->where('TbDespacho.stEstado = ?', 0);
        $result = $this->fetchRow($objQuery);
        if ($result) {
            return $result->toArray();
        }
    }
}
