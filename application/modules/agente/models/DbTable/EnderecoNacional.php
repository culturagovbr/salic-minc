<?php

/**
 * Class Agente_Model_DbTable_EnderecoNacional
 *
 * @name Agente_Model_DbTable_EnderecoNacional
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 06/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_EnderecoNacional extends MinC_Db_Table_Abstract
{
    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'agentes';

    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'endereconacional';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'idEndereco';

    /**
     * Metodo para buscar os enderecos do agente
     *
     * @access public
     * @static
     * @param integer $idAgente
     * @return object
     */
    public function buscarEnderecos($idAgente = null)
    {
        $ve = array(
            've.descricao as tipoendereco',
            've.idverificacao as codtipoendereco',
        );

        $m = array(
            'm.descricao as municipio',
            'm.idmunicipioibge as codmun',
        );

        $u = array(
            'u.sigla as uf',
            'u.iduf as coduf'
        );

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('e' => 'endereconacional'), $this->_getCols(), $this->_schema)
            ->joinLeft(array('ve' => 'verificacao'), 've.idverificacao = e.tipoendereco', $ve, $this->_schema)
            ->joinLeft(array('m' => 'municipios'), 'm.idmunicipioibge = e.cidade', $m, $this->_schema)
            ->joinLeft(array('u' => 'uf'), 'U.iduf = e.uf', $u, $this->_schema)
            ->joinLeft(array('vl' => 'verificacao'), 'vl.idverificacao = e.tipologradouro', array('vl.descricao as dstipologradouro'), $this->_schema)
            ->where('e.idagente = ?', $idAgente)
            ->order(array('status DESC'))
        ;

        return $this->fetchAll($sql);
    }

    /**
     * mudaCorrespondencia
     *
     * @param mixed $idAgente
     * @static
     * @access public
     * @return void
     */
    public function mudaCorrespondencia($idAgente)
    {
        try {
            return $resultado = $this->update(array('status' => 0), array('idagente = ?' => $idAgente));
        } catch (Zend_Exception $e) {
            throw new Zend_Db_Exception("Erro ao alterar o Status dos endere&ccedil;os: " . $e->getMessage());
        }
    }

    /**
     * novaCorrespondencia
     *
     * @param mixed $idAgente
     * @static
     * @access public
     * @return void
     *
     */
    public function novaCorrespondencia($idAgente)
    {
        try {
            $subSelect = $this->select()
                ->from($this->_name, array(new Zend_Db_Expr('min(idendereco) as valor')), $this->_schema)
                ->where('idagente = ?', $idAgente);

            $dados = array(
                'status' => 1
            );

            $where['idagente = ?'] = $idAgente;
            $where['idendereco = ?']  = $subSelect;

            $this->update($dados, $where);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao alterar o Status dos endere&ccedil;os: " . $e->getMessage();
        }
    }

    /**
     * delete
     * Deleta Endereco Nacional
     *
     * @param mixed $idEndereco
     * @static
     * @access public
     * @return void
     */
    public function delete($idEndereco)
    {
        return parent::delete(array('idEndereco = ? '=> $idEndereco));
    }
}
