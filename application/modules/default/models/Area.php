<?php
class Area extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'Area';

    const AREA_ARTES_CENICAS = 1;
    const AREA_AUDIOVISUAL = 2;
    const AREA_MUSICA = 3;
    const AREA_ARTES_VISUAIS = 4;
    const AREA_PATRIMONIO_CULTURAL = 5;
    const AREA_HUMANIDADES = 6;
    const AREA_ARTES_INTEGRADAS = 7;
    const AREA_MUSEUS_MEMORIA = 9;
    
    public function BuscarAreaProjeto($idpronac=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('a'=>$this->_name),
                        array('a.Descricao as nomeArea')
                     );
        $select->joinInner(
                            array('pr'=>'Projetos'),
                            'pr.Area = a.Codigo'
                          );
        if ($idpronac) {
            $select->where('pr.IdPRONAC = ?', $idpronac);
        }

        return $this->fetchRow($select);
    }


    public function BuscarAreas()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('a'=>$this->_name),
                        array('a.*')
                     );

        return $this->fetchAll($select);
    }
}
