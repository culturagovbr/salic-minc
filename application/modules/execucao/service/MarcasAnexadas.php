<?php

namespace Application\Modules\Execucao\Service\MarcasAnexadas;


class MarcasAnexadas
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

    public function buscarMarcasAnexadas()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        $Projetos = new \Projetos();
        $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
        $pronac = $dadosProj->AnoProjeto.$dadosProj->Sequencial;

        $tbArquivoImagem = new \tbArquivoImagem();
        $marcas = $tbArquivoImagem->marcasAnexadas($pronac)->toArray();

        foreach ($marcas as &$item) {
            $estadoDocumentacao = $this->obterEstadoDocumento($item['stAtivoDocumentoProjeto']);
            $item['stAtivoDocumentoProjeto'] = $estadoDocumentacao;
        }

        return $marcas;
    }

    private function obterEstadoDocumento($stAtivoDocumentoProjeto)
    {
        switch ($stAtivoDocumentoProjeto) {
            case 'D':
                $situacaoDocumentoProjeto = 'Deferido';
                break;
            case 'I':
                $situacaoDocumentoProjeto = 'Indeferido';
                break;
            case 'E':
                $situacaoDocumentoProjeto = 'Enviado';
                break;
            default:
                $situacaoDocumentoProjeto = '';
                break;
        }

        return $situacaoDocumentoProjeto;
    }
}