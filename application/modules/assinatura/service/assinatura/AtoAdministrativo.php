<?php

namespace Application\Modules\Assinatura\Service\Assinatura;

use MinC\Assinatura\Servico\IServico;

class AtoAdministrativo implements IServico
{
    public function obterAtoAdministrativoAtual(
        $idTipoDoAto,
        $idPerfilDoAssinante,
        $idOrgaoDoAssinante,
        $idOrgaoSuperiorDoAssinante
    )
    {

        $tbAtoAdministrativoDbTable = new \Assinatura_Model_DbTable_TbAtoAdministrativo();
        $atoAdministrativo = $tbAtoAdministrativoDbTable->obterPrimeiroAtoAdministrativo(
            $idTipoDoAto,
            $idPerfilDoAssinante,
            $idOrgaoDoAssinante,
            $idOrgaoSuperiorDoAssinante
        );

        return $atoAdministrativo;
    }

    public function obterGrupoAtoAdministrativoAtual(
        $idTipoDoAto,
        $idPerfilDoAssinante,
        $idOrgaoDoAssinante,
        $idOrgaoSuperiorDoAssinante
    )
    {

        $atoAdministrativo = $this->obterAtoAdministrativoAtual(
            $idTipoDoAto,
            $idPerfilDoAssinante,
            $idOrgaoDoAssinante,
            $idOrgaoSuperiorDoAssinante
        );

        $grupo = null;
        if (count($atoAdministrativo) > 0) {
            $grupo = $atoAdministrativo['grupo'];
        }

        return $grupo;
    }
}
