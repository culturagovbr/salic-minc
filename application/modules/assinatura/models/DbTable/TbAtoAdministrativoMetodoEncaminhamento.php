<?php

/**
 * Class Assinatura_Model_DbTable_TbAtoAdministrativo
 * @var Assinatura_Model_TbAtoAdministrativo $dbTableTbAtoAdministrativo
 */
class Assinatura_Model_DbTable_TbAtoAdministrativoMetodoEncaminhamento extends MinC_Db_Table_Abstract
{
    public $modelAtoAdministrativoMetodoEncaminhamento;
    protected $_schema = 'sac';
    protected $_name = 'tbAtoAdministrativoMetodoEncaminhamento';
    protected $_primary = 'idAtoAdministrativoMetodoEncaminhamento';

    public function definirModeloAssinatura(array $dados)
    {
        $this->modelAtoAdministrativoMetodoEncaminhamento = new Assinatura_Model_TbAtoAdministrativoMetodoEncaminhamento($dados);
        return $this;
    }

    public function obterEncaminhaProjeto($idTipoDoAto)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            $this->_name,
            ['encaminhaProjeto']
            ,
            $this->_schema
        );

        $objQuery->where('idTipoDoAto = (?)', $idTipoDoAto);

        $objResultado = $this->fetchRow($objQuery);
        if ($objResultado) {
            $arrayResultado = $objResultado->toArray();
            return $arrayResultado['encaminhaProjeto'];
        }
    }
}
