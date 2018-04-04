<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DocumentosExigidos
 *
 * @author 01610881125
 */
class DocumentosExigidos extends MinC_Db_Table_Abstract
{
    protected $_name   = 'DocumentosExigidos';
    protected $_schema = 'sac';
    protected $_banco  = 'SAC';
    protected $_primary = 'Codigo';

    public function listarDocumentosExigido($idCodigoDocumentosExigidos = '')
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array($this->_name),
                        array(
                                'Codigo',
                                'Descricao',
                                'Area',
                                'Opcao',
                                'stEstado'
                              )
                      );
        $select->where('Opcao in ?', new Zend_Db_Expr('(1,2)'));
        if ($idCodigoDocumentosExigidos) {
            $select->where('Codigo = ?', $idCodigoDocumentosExigidos);
        }


        return $this->fetchAll($select);
    }

    public function listaDocumentosObrigatoriosPF()
    {
    }

    public function listaDocumentosObrigatoriosPJ()
    {
    }
}
