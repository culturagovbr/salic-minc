<?php

namespace Application\Modules\Proposta\Service\Proposta;

class Enquadramento
{
    const OBJECT_KEYS = [
        'data_avaliacao' => 'Data',
        'usu_nome' => 'Avaliador',
        'org_sigla' => 'Unidade',
        'area' => '&Aacute;rea',
        'segmento' => 'Segmento',
        'enquadramento' => 'Enquadramento',
        'descricao_motivacao' => 'Parecer'
    ];

    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;


    function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function obterSugestaoEnquadramento()
    {
        $idPreProjeto = $this->request->idPreProjeto;

        $sugestoesEnquadramentoDbTable = new \Admissibilidade_Model_DbTable_SugestaoEnquadramento();
        $sugestoesEnquadramentoDbTable->sugestaoEnquadramento->setIdPreprojeto($idPreProjeto);
        $sugestoes_enquadramento = $sugestoesEnquadramentoDbTable->obterHistoricoEnquadramento();

        $data = $this->montarSugestoesEnquadramento($sugestoes_enquadramento );

        return $data;
    }

    private function montarSugestoesEnquadramento($sugestoes_enquadramento): array
    {
        $resultado = [];
        $resultado['class'] = 'bordered striped';
        $resultado['cols'] = $this->montarSugestoesEnquadramentoColunas();
        $resultado['lines'] = $this->montarSugestoesEnquadramentoLinhas($sugestoes_enquadramento);

        return $resultado;
    }

    private function montarSugestoesEnquadramentoColunas(): array
    {
        $colunas = [];

        foreach (self::OBJECT_KEYS as $key => $value) {
            $colunas[$key]['name'] = html_entity_decode($value);
        }

        return $colunas;
    }

    private function montarSugestoesEnquadramentoLinhas($sugestoes_enquadramento): array
    {
        $lines = [];

        foreach ($sugestoes_enquadramento as $sugestao_enquadramento) {
            $result = $this->montarSugestaoEnquadramento($sugestao_enquadramento);
            array_push($lines, $result);
        }

        return $lines;
    }

    private function montarSugestaoEnquadramento($sugestao_enquadramento) : array
    {
        $current_object = [];

        foreach (self::OBJECT_KEYS as $key => $value) {
            if ($key != 'data_avaliacao') {
                $current_object[$key] = html_entity_decode(utf8_encode($sugestao_enquadramento[$key]));
            } else {
                $date = new \DateTime($sugestao_enquadramento[$key]);
                $current_object[$key] = $date->format('d/m/Y H:i:s');
            }
        }

        return $current_object;
    }
}
