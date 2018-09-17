<?php

/**
 * @name Agente_Model_TbMensagemProjetoMapper
 * @package Modules/Admissibilidade
 * @subpackage Models
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 20/03/2018
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Projeto_Model_TbProjetosMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Projeto_Model_DbTable_Projetos');
    }

    public function isValid($model)
    {
        $booStatus = true;
        $arrData = $model->toArray();
        $arrRequired = [
            'idPRONAC',
            'situacao',
            'dtSituacao',
            'providenciaTomada',
            'logon',
        ];
        foreach ($arrRequired as $strValue) {
            if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) {
                $this->setMessage('Campo obrigat&oacute;rio!', $strValue);
                $booStatus = false;
            }
        }

        return $booStatus;
    }

    public function obterProjetoCompleto($idPronac)
    {

        $tbProjetos = new Projeto_Model_DbTable_Projetos();
        $projeto = $tbProjetos->findBy(['IdPRONAC = ?' => $idPronac]);

        if($projeto['Mecanismo'] == 1) {
            return $this->obterProjetoIncentivo($idPronac);
        }

       return $this->obterProjetoConvenio($idPronac);
    }

    public function obterProjetoConvenio($idPronac)
    {
        if (empty($idPronac)) {
            return false;
        }

        $vwDadosProjeto = new Projeto_Model_DbTable_VwConsultarDadosDoProjetoFNC();
        $projeto = $vwDadosProjeto->obterDadosFnc($idPronac);

        $data = $projeto;
        $data['isTipoConvenio'] = true;
        $dbTableInabilitado = new Inabilitado();
        $proponenteInabilitado = $dbTableInabilitado->BuscarInabilitado($projeto['CNPJ_CPF'], null, null, true);
        $data['ProponenteInabilitado'] = !empty($proponenteInabilitado);

        return $data;
    }

    public function obterProjetoIncentivo($idPronac)
    {
        if (empty($idPronac)) {
            return false;
        }

        $dbTableProjetos = new Projeto_Model_DbTable_Projetos();
        $projeto = $dbTableProjetos->obterProjetoIncentivoCompleto($idPronac);

        $tbPreProjetoMeta = new Proposta_Model_PreProjetoMapper();
        $planilhaOriginal = $tbPreProjetoMeta->obterValorTotalPlanilhaPropostaCongelada($projeto->idPreProjeto);

        $data = $projeto->toArray();

        $data['vlSolicitadoOriginal'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlSolicitadoOriginal'] : $data['vlSolicitadoOriginal'];
        $data['vlOutrasFontesPropostaOriginal'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlOutrasFontesPropostaOriginal'] : $data['vlOutrasFontesPropostaOriginal'];
        $data['vlTotalPropostaOriginal'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlTotalPropostaOriginal'] : $data['vlTotalPropostaOriginal'];

        $data['vlAutorizado'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlSolicitadoOriginal'] : $data['vlAutorizado'];
        $data['vlAutorizadoOutrasFontes'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlOutrasFontesPropostaOriginal'] : $data['vlAutorizadoOutrasFontes'];
        $data['vlTotalAutorizado'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlTotalPropostaOriginal'] : $data['vlTotalAutorizado'];

        $dbTableInabilitado = new Inabilitado();
        $proponenteInabilitado = $dbTableInabilitado->BuscarInabilitado($projeto->CgcCPf, null, null, true);;

        $Parecer = new Parecer();
        $parecerAnaliseCNIC = $Parecer->verificaProjSituacaoCNIC($projeto->Pronac);

        $data['ProponenteInabilitado'] = !empty($proponenteInabilitado);
        $data['EmAnaliseNaCNIC'] = (count($parecerAnaliseCNIC) > 0) ? true : false;
        $data['idUsuarioExterno'] = !empty($this->idUsuarioExterno) ? $this->idUsuarioExterno : false;
        $data['isTipoIncentivo'] = true;

        return $data;

    }

}
