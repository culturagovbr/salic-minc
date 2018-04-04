<?php

/**
 * Class Proposta_Model_DbTable_TbDeslocamento
 *
 * @name Proposta_Model_DbTable_TbDeslocamento
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 20/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br

    idDeslocamento
    idProjeto
    idPaisOrigem
    idUFOrigem
    idMunicipioOrigem
    idPaisDestino
    idUFDestino
    idMunicipioDestino
    QtdeidUsuario
 */
class Proposta_Model_DbTable_TbDeslocamento extends MinC_Db_Table_Abstract
{
    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'sac';

    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'tbdeslocamento';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'idDeslocamento';

    public function buscarDeslocamentosGeral($where = array(), $order = array(), $arrNot = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('de' => $this->_name), '*', $this->_schema)
            ->joinLeft(array('pao' => 'pais'), 'de.idpaisorigem = pao.idpais', array('continente as continenteorigem', 'descricao as paisorigem'), $this->getSchema('agentes'))
            ->joinLeft(array('ufO' => 'uf'), 'de.iduforigem = ufo.iduf', array('descricao as uforigem'), $this->getSchema('agentes'))
            ->joinLeft(array('muO' => 'municipios'), 'de.idmunicipioorigem = muo.idmunicipioibge', array('descricao as municipioorigem'), $this->getSchema('agentes'))
            ->joinLeft(array('paD' => 'pais'), 'de.idpaisdestino = pad.idpais', array('continente as continentedestino', 'descricao as paisodestino'), $this->getSchema('agentes'))
            ->joinLeft(array('ufD' => 'uf'), 'de.idufdestino = ufd.iduf', array('descricao as ufdestino'), $this->getSchema('agentes'))
            ->joinLeft(array('muD' => 'municipios'), 'de.idmunicipiodestino = mud.idmunicipioibge', array('descricao as municipiodestino'), $this->getSchema('agentes'))
        ;
        foreach ($where as $coluna => $valor) {
            $select->where($coluna . ' = ?', $valor);
        }
        foreach ($arrNot as $coluna => $valor) {
            if (!empty($valor)) {
                $select->where($coluna . ' != ?', $valor);
            }
        }
        if ($order) {
            $select->order($order);
        }
        $arrResult = $this->fetchAll($select);
        return ($arrResult) ? $arrResult->toArray() : array();
    }


    /**
     * buscarDeslocamentos
     *
     * @param mixed $idProjeto
     * @param bool $idDeslocamento
     * @static
     * @access public
     * @return void
     * @author wouerner <wouerner@gmail.com>
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     */
    public function buscarDeslocamento($idProjeto, $idDeslocamento = null)
    {
        $agenteSchema = $this->getSchema('agentes');
        $de = array(
            'de.iddeslocamento',
            'de.idprojeto',
            'de.idpaisorigem',
            'de.iduforigem',
            'de.idmunicipioorigem',
            'de.idpaisdestino',
            'de.idufdestino',
            'de.qtde',
            'de.idusuario',
            'de.idmunicipiodestino'
        );

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('de' => $this->_name), $de, $this->_schema)
            ->joinLeft(array('pao'=>'pais'), 'de.idpaisorigem = pao.idpais', 'pao.descricao as po', $agenteSchema)
            ->joinLeft(array('ufo'=>'uf'), 'de.iduforigem = ufo.iduf', 'ufo.descricao as ufo', $agenteSchema)
            ->joinLeft(array('muo' => 'municipios'), 'de.idmunicipioorigem = muo.idmunicipioibge', 'muo.descricao as muo', $agenteSchema)
            ->joinLeft(array('pad' => 'pais'), 'de.idpaisdestino = pad.idpais', 'pad.descricao as pd', $agenteSchema)
            ->joinLeft(array('ufd' => 'uf'), 'de.idufdestino = ufd.iduf', 'ufd.descricao as ufd', $agenteSchema)
            ->joinLeft(array('mud' => 'municipios'), 'de.idmunicipiodestino = mud.idmunicipioibge', 'mud.descricao as mud', $agenteSchema)
            ->where("idprojeto = ?", $idProjeto)
        ;

        if ($idDeslocamento != null) {
            $sql->where('de.iddeslocamento = ?', $idDeslocamento);
        }

        $resultado = $this->fetchAll($sql);

        return ($resultado)? $resultado->toArray() : array();
    }
}
