<?php
/**
 * DAO tbArquivo
 * @since 07/01/2013
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbArquivamento extends MinC_Db_Table_Abstract
{
    protected $_schema  = "SAC";
    protected $_name   = "tbArquivamento";


    public function conferirArquivamentoProjeto($pronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array("a" => $this->_name),
                array('a.Data', 'a.CaixaInicio', 'a.CaixaFinal'),
            $this->_schema
        );
        $select->joinInner(
                array('b' => 'Projetos'),
            'a.idPronac = b.idPronac',
                array(''),
            $this->_schema
        );

        $select->where('a.stEstado = ?', 1);
        $select->where('a.stAcao = ?', 0);
        $select->where('b.AnoProjeto+b.Sequencial = ?', $pronac);

        return $this->fetchRow($select);
    } // fecha m�todo buscarDados()
} // fecha class
