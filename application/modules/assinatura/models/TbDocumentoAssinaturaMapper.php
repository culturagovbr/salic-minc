<?php

class Assinatura_Model_TbDocumentoAssinaturaMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Assinatura_Model_DbTable_TbDocumentoAssinatura');
    }

    public function save(Assinatura_Model_TbDocumentoAssinatura $model)
    {
        return parent::save($model);
    }

    public function IsProjetoJaAssinado($idPronac, $idTipoDoAtoAdministrativo, $idPerfilDoAssinante)
    {
        $objAssinatura = new Assinatura_Model_DbTable_TbAssinatura();
        $assinaturas = $objAssinatura->obterAssinaturas($idPronac, $idTipoDoAtoAdministrativo);

        foreach ($assinaturas as $assinatura) {
            if ($assinatura->idPerfilDoAssinante == $idPerfilDoAssinante) {
                return true;
            }
        }
        return false;
    }    
}
