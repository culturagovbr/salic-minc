<?php

namespace Application\Modules\Analise\Service\AnaliseAprovacao;

use Seguranca;

class Aprovacao implements \MinC\Servico\IServicoRestZend
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function buscarAprovacao()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        $tblProjeto = new \Projetos();
        $rsProjeto = $tblProjeto->buscar(array("IdPronac=?"=>$idPronac))->current();
        $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;

        $tblAprovacao = new \Aprovacao();

        $rsAprovacao = $tblAprovacao->buscaCompleta(
            ['a.AnoProjeto + a.Sequencial = ?'=> $pronac],
            ['a.TipoAprovacao ASC', 'a.DtAprovacao DESC']
        );

        $Aprovacao = $this->montaArrayAprovacao($rsAprovacao);
        $resultArray['Aprovacao'] = $Aprovacao;

        return $resultArray;
    }

    private function montaArrayAprovacao($aprovacoes)
    {
        $resultArray = [];

        foreach ($aprovacoes as $aprovacao) {
            $resultArray[] = [
                'TipoAprovacao' => $aprovacao['TipoAprovacao'],
                'DtAprovacao' => trim($aprovacao['DtAprovacao']),
                'DtPortariaAprovacao' => trim($aprovacao['DtPortariaAprovacao']),
                'PortariaAprovacao' => $aprovacao['PortariaAprovacao'],
                'DtPublicacaoAprovacao' => trim($aprovacao['DtPublicacaoAprovacao']),
                'DtInicioCaptacao' => trim($aprovacao['DtInicioCaptacao']),
                'DtFimCaptacao' => trim($aprovacao['DtFimCaptacao']),
                'Mecanismo' => $aprovacao['Mecanismo'],
                'ResumoAprovacao' => $aprovacao['ResumoAprovacao'],
                'AprovadoReal' => $aprovacao['AprovadoReal'],
                'CodTipoAprovacao' => $aprovacao['CodTipoAprovacao']
            ];
        }
        return $resultArray;
    }
}
