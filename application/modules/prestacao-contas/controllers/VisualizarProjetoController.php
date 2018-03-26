<?php

class PrestacaoContas_VisualizarProjetoController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $PermissoesGrupo = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS
        ];

        $auth = Zend_Auth::getInstance();

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        isset($auth->getIdentity()->usu_codigo) ? $this->idUsuario = $auth->getIdentity()->usu_codigo : $this->idUsuario = $auth->getIdentity()->IdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->codGrupo = $GrupoAtivo->codGrupo;
            $this->codOrgao = $GrupoAtivo->codOrgao;
            $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : null;
        }

        parent::init();
    }

    public function indexAction()
    {
        $this->_helper->json([]);
    }

    public function dadosProjetoAction()
    {
        $idPronac = $this->_request->getParam("idPronac");

        $itens = new PrestacaoContas_Model_vwComprovacaoFinanceiraProjetoPorItemOrcamentario();

        //consolidacaoPorProduto
        $consolidacaoPorProduto = $itens->consolidacaoPorProduto($idPronac);
        $itensAux = [];
        $totalAux = [];
        $json = [];
        foreach ($consolidacaoPorProduto as $k => $item) {
            $itensAux[$k]['dsProduto'] = utf8_encode($item->dsProduto);
            $itensAux[$k]['qtComprovantes'] = number_format($item->qtComprovantes, 0, ',', '.');
            $itensAux[$k]['vlComprovado'] = number_format($item->vlComprovado, 2, ',', '.');
            $itensAux[$k]['PercComprovado'] = number_format($item->PercComprovado, 2, ',', '.');
            //totais
            $totalAux['qtComprovantes'] += $item->qtComprovantes;
            $totalAux['vlComprovado'] += $item->vlComprovado;
        }

        $json['consolidacaoPorProduto']['lines'] =  $itensAux;
        $json['consolidacaoPorProduto']['cols'] = [
            'dsProduto' => ['name'=> 'Produto', 'class' => ''],
            'qtComprovantes' => ['name'=> 'Qtde. Comprovantes', 'class' => 'right-align'],
            'vlComprovado' => ['name'=> 'Valor Comprovado', 'class' => 'right-align'],
            'PercComprovado' => ['name'=> '% Comprovado', 'class' => 'right-align']
        ];
        $json['consolidacaoPorProduto']['title'] = 'COMPROVAÇÃO CONSOLIDADA POR PRODUTO';
        $json['consolidacaoPorProduto']['tfoot']['qtComprovantes'] = $totalAux['qtComprovantes'];
        $json['consolidacaoPorProduto']['tfoot']['vlComprovado'] =  number_format($totalAux['vlComprovado'], 2, ',', '.');
        $json['consolidacaoPorProduto']['tfoot']['dsProduto'] = 'Total';

        //consolidadoPorEtapa
        $consolidadoPorEtapa = $itens->consolidadoPorEtapa($idPronac);
        $itensAux = [];
        $totalAux = [];
        foreach ($consolidadoPorEtapa as $k => $item) {
            $itensAux[$k]['Descricao'] = utf8_encode($item->Descricao);
            $itensAux[$k]['qtComprovantes'] = number_format($item->qtComprovantes, 0, ',', '.');
            $itensAux[$k]['vlComprovado'] = number_format($item->vlComprovado, 2, ',', '.');
            $itensAux[$k]['PercComprovado'] = number_format($item->PercComprovado, 2, ',', '.');
            //totais
            $totalAux['qtComprovantes'] += $item->qtComprovantes;
            $totalAux['vlComprovado'] += $item->vlComprovado;
            $totalAux['Descricao'] = 'Total';
        }
        $json['consolidadoPorEtapa']['lines'] = $itensAux;
        $json['consolidadoPorEtapa']['cols'] = [
            'Descricao' => [ 'name'=> 'Etapa', 'class' => 'left-align'],
            'qtComprovantes' => [ 'name'=> 'Qtde. Comprovantes', 'class' => 'right-align'],
            'vlComprovado' => [ 'name'=> 'Valor Comprovado', 'class' => 'right-align'],
            'PercComprovado'=> [ 'name'=> '% Comprovado', 'class' => 'right-align']
        ];
        $json['consolidadoPorEtapa']['title'] = 'COMPROVAÇÃO CONSOLIDADA POR ETAPA';
        $totalAux['vlComprovado'] = number_format($totalAux['vlComprovado'], 2, ',', '.');
        $json['consolidadoPorEtapa']['tfoot'] = $totalAux;

        /* maioresItensComprovados */
        $maioresItensComprovados = $itens->maioresItensComprovados($idPronac);
        $itensAux = [];
        $totalAux = [];
        foreach ($maioresItensComprovados as $k => $item) {
            $itensAux[$k]['Descricao'] = utf8_encode($item->Descricao);
            $itensAux[$k]['qtComprovantes'] = number_format($item->qtComprovantes, 0, ',', '.');
            $itensAux[$k]['vlComprovado'] = number_format($item->vlComprovado, 2, ',', '.');
            $itensAux[$k]['PercComprovado'] = number_format($item->PercComprovado, 2, ',', '.');
            //totais
            $totalAux['qtComprovantes'] += $item->qtComprovantes;
            $totalAux['vlComprovado'] += $item->vlComprovado;
            $totalAux['Descricao'] = 'Total';
        }
        $json['maioresItensComprovados']['lines'] = $itensAux;
        $json['maioresItensComprovados']['cols'] = [
            'Descricao' =>['name' => 'Item Orçamentario'],
            'qtComprovantes' => ['name' => 'Qtde. Comprovantes', 'class' => 'right-align'],
            'vlComprovado' => ['name' => 'Valor Comprovado', 'class' => 'right-align'],
            'PercComprovado' => ['name' => '% Comprovado', 'class' => 'right-align']
        ];
        $json['maioresItensComprovados']['title'] = 'MAIORES ITENS ORÇAMENTARIOS COMPROVADOS';
        $totalAux['vlComprovado'] = number_format($totalAux['vlComprovado'], 2, ',', '.');
        $json['maioresItensComprovados']['tfoot'] = $totalAux;

        //comprovacaoConsolidadaUfMunicipio
        $comprovacaoConsolidadaUfMunicipio = $itens->comprovacaoConsolidadaUfMunicipio($idPronac);
        $itensAux = [];
        $totalAux = [];
        foreach ($comprovacaoConsolidadaUfMunicipio as $k => $item) {
            $itensAux[$k]['UF'] = utf8_encode($item->UF);
            $itensAux[$k]['qtComprovantes'] = number_format($item->qtComprovantes, 0, ',', '.');
            $itensAux[$k]['Municipio'] = utf8_encode($item->Municipio);
            $itensAux[$k]['vlComprovado'] = number_format($item->vlComprovado, 2, ',', '.');
            $itensAux[$k]['PercComprovado'] = number_format($item->PercComprovado, 2, ',', '.');
            //totais
            $totalAux['qtComprovantes'] += $item->qtComprovantes;
            $totalAux['vlComprovado'] += $item->vlComprovado;
            $totalAux['UF'] = 'Total';
        }
        $json['comprovacaoConsolidadaUfMunicipio']['lines'] = $itensAux;
        $json['comprovacaoConsolidadaUfMunicipio']['cols'] = [
            'UF' =>['name' => 'UF'],
            'qtComprovantes' =>['name' => 'Qtde. Comprovantes', 'class' => 'right-align'],
            'Municipio' =>['name' => 'Municipio', 'class' => 'center-align'],
            'vlComprovado' =>['name' => 'Valor Comprovado', 'class' => 'right-align'],
            'PercComprovado' =>['name' => '% Comprovado', 'class' => 'right-align']
        ];
        $json['comprovacaoConsolidadaUfMunicipio']['title'] = 'COMPROVAÇÃO CONSOLIDADA POR UF E MUNICIPIO';
        $totalAux['vlComprovado'] = number_format($totalAux['vlComprovado'], 2, ',', '.');
        $json['comprovacaoConsolidadaUfMunicipio']['tfoot'] = $totalAux;

        /* maioresComprovacaoTipoDocumento */
        $maioresComprovacaoTipoDocumento = $itens->maioresComprovacaoTipoDocumento($idPronac);
        $itensAux = [];
        $totalAux = [];
        foreach ($maioresComprovacaoTipoDocumento as $k => $item) {
            $itensAux[$k]['tpDocumento'] = utf8_encode($item->tpDocumento);
            $itensAux[$k]['nrComprovante'] = utf8_encode($item->nrComprovante);
            $itensAux[$k]['nmFornecedor'] = utf8_encode($item->nmFornecedor);
            $itensAux[$k]['qtComprovacoes'] = number_format($item->qtComprovacoes, 0, ',', '.');
            $itensAux[$k]['vlComprovado'] = number_format($item->vlComprovado, 2, ',', '.');
            $itensAux[$k]['PercComprovado'] = number_format($item->PercComprovado, 2, ',', '.');
            //totais
            $totalAux['qtComprovacoes'] += $item->qtComprovacoes;
            $totalAux['vlComprovado'] += $item->vlComprovado;
            $totalAux['tpDocumento'] = 'Total';
        }
        $json['maioresComprovacaoTipoDocumento']['lines']= $itensAux;
        $json['maioresComprovacaoTipoDocumento']['cols'] = [
            'tpDocumento' => ['name' => 'Tipo Documento'],
            'nrComprovante' => ['name' => 'Nr. Comprovante'],
            'nmFornecedor' => ['name' => 'Fornecedor'],
            'qtComprovacoes' => ['name' => 'Qtde. Comprovantes', 'class' => 'right-align'],
            'vlComprovado' => ['name' => 'Valor Comprovado', 'class' => 'right-align'],
            'PercComprovado' => ['name' => '% Comprovado', 'class' => 'right-align']
        ];
        $json['maioresComprovacaoTipoDocumento']['title'] = 'MAIORES COMPROVAÇÕES POR TIPO DE DOCUMENTOS COMPROBATÓRIOS';
        $totalAux['vlComprovado'] = number_format($totalAux['vlComprovado'], 2, ',', '.');
        $totalAux['qtComprovacoes'] = number_format($totalAux['qtComprovacoes'], 0, ',', '.');
        $json['maioresComprovacaoTipoDocumento']['tfoot'] = $totalAux;

        /* comprovacaoTipoDocumentoPagamento */
        $comprovacaoTipoDocumentoPagamento = $itens->comprovacaoTipoDocumentoPagamento($idPronac);
        $itensAux = [];
        $totalAux = [];
        foreach ($comprovacaoTipoDocumentoPagamento as $k => $item) {
            $itensAux[$k]['tpFormaDePagamento'] = utf8_encode($item->tpFormaDePagamento);
            $itensAux[$k]['nrDocumentoDePagamento'] = utf8_encode($item->nrDocumentoDePagamento);
            $itensAux[$k]['nmFornecedor'] = utf8_encode($item->nmFornecedor);
            $itensAux[$k]['qtComprovacoes'] = utf8_encode($item->qtComprovacoes);
            $itensAux[$k]['vlComprovado'] = number_format($item->vlComprovado, 2, ',', '.');
            $itensAux[$k]['PercComprovado'] = number_format(utf8_encode($item->PercComprovado), 2, ',', '.');
            //totais
            $totalAux['qtComprovacoes'] += $item->qtComprovacoes;
            $totalAux['vlComprovado'] += $item->vlComprovado;
            $totalAux['tpFormaDePagamento'] = 'Total';
        }
        $json['comprovacaoTipoDocumentoPagamento']['lines'] = $itensAux;
        $json['comprovacaoTipoDocumentoPagamento']['cols'] = [
            'tpFormaDePagamento' => ['name' =>  'Tipo Documento'],
            'nrDocumentoDePagamento' => ['name' => 'Nr. Comprovante'],
            'nmFornecedor' => ['name' => 'Fonecedor'],
            'qtComprovacoes' => ['name' => 'Qtde. Comprovantes', 'class' => 'right-align'],
            'vlComprovado' => ['name' => 'Valor Comprovado', 'class' => 'right-align'],
            'PercComprovado' => ['name' => '% Comprovado', 'class' => 'right-align']
        ];
        $json['comprovacaoTipoDocumentoPagamento']['title'] = 'MAIORES COMPROVAÇÕES POR TIPO DE DOCUMENTOS DE PAGAMENTO';
        $totalAux['vlComprovado'] = number_format($totalAux['vlComprovado'], 2, ',', '.');
        $json['comprovacaoTipoDocumentoPagamento']['tfoot'] = $totalAux;

        //maioresFornecedoresProjeto
        $maioresFornecedoresProjeto = $itens->maioresFornecedoresProjeto($idPronac);
        $itensAux = [];
        foreach ($maioresFornecedoresProjeto as $k => $item) {
            if (strlen($item->nrCNPJCPF) > 11) {
                $itensAux[$k]['nrCNPJCPF'] = $this->view->Mask($item->nrCNPJCPF, '##.###.###/####-##');
            } else {
                $itensAux[$k]['nrCNPJCPF'] = $this->view->Mask($item->nrCNPJCPF, '###.###.###-##');
            }

            $itensAux[$k]['nmFornecedor'] = utf8_encode($item->nmFornecedor);
            $itensAux[$k]['qtComprovacoes'] = number_format(utf8_encode($item->qtComprovacoes), 0, ',', '.');
            $itensAux[$k]['vlComprovado'] = number_format($item->vlComprovado, 2, ',', '.');
            $itensAux[$k]['PercComprovado'] = number_format(utf8_encode($item->PercComprovado), 2, ',', '.');
            //totais
            $totalAux['qtComprovacoes'] += $item->qtComprovacoes;
            $totalAux['vlComprovado'] += $item->vlComprovado;
            $totalAux['nrCNPJCPF'] = 'Total';
        }

        $json['maioresFornecedoresProjeto']['lines'] = $itensAux;
        $json['maioresFornecedoresProjeto']['cols'] = [
            'nrCNPJCPF' => ['name' => 'CNPJ/CPF'],
            'nmFornecedor' => ['name' => 'Fornecedor'],
            'qtComprovacoes' => ['name' => 'Qtde. Comprovações', 'class' => 'right-align'],
            'vlComprovado' => ['name' => 'Valor Comprovado', 'class' => 'right-align'],
            'PercComprovado' => ['name' => '% Comprovado', 'class' => 'right-align']
        ];
        $json['maioresFornecedoresProjeto']['title'] = 'MAIORES FORNECEDORES DO PROJETO';
        $totalAux['vlComprovado'] = number_format($totalAux['vlComprovado'], 2, ',', '.');
        $totalAux['qtComprovacoes'] = number_format($totalAux['qtComprovacoes'], 0, ',', '.');
        $json['maioresFornecedoresProjeto']['tfoot'] = $totalAux;

        $fornecedorItemProjeto = $itens->fornecedorItemProjeto($idPronac);
        $itensAux = [];
        foreach ($fornecedorItemProjeto as $k => $item) {
            if (strlen($item->nrCNPJCPF) > 11) {
                $itensAux[$k]['nrCNPJCPF'] = $this->view->Mask($item->nrCNPJCPF, '##.###.###/####-##');
            } else {
                $itensAux[$k]['nrCNPJCPF'] = $this->view->Mask($item->nrCNPJCPF, '###.###.###-##');
            }
            $itensAux[$k]['nmFornecedor'] = utf8_encode($item->nmFornecedor);
            $itensAux[$k]['Etapa'] = utf8_encode($item->Etapa);
            $itensAux[$k]['vlComprovado'] = number_format($item->vlComprovado, 2, ',', '.');
            $itensAux[$k]['PercComprovado'] = number_format($item->PercComprovado, 2, ',', '.');
            //totais
            $totalAux['qtComprovacoes'] += $item->qtComprovacoes;
            $totalAux['vlComprovado'] += $item->vlComprovado;
            $totalAux['nrCNPJCPF'] = 'Total';
        }

        $json['fornecedorItemProjeto']['lines'] = $itensAux;
        $json['fornecedorItemProjeto']['cols'] = [
            'nrCNPJCPF' => ['name'=> 'CNPJ/CPF'],
            'nmFornecedor' => ['name' => 'Fornecedor'],
            'Etapa' => ['name' => 'Etapa'],
            'vlComprovado' => ['name' => 'Valor Comprovado', 'class' => 'right-align'],
            'PercComprovado' => ['name' => '% Comprovado', 'class' => 'right-align']
        ];
        $json['fornecedorItemProjeto']['title'] = 'PROPONENTE FORNECEDOR DE ITEM PARA O PROJETO';
        $totalAux['vlComprovado'] = number_format($totalAux['vlComprovado'], 2, ',', '.');
        $json['fornecedorItemProjeto']['tfoot'] = $totalAux;

        //impugnados
        //itensOrcamentariosImpugnados
        $itensOrcamentariosImpugnados = $itens->itensOrcamentariosImpugnados($idPronac);
        $itensAux = [];
        $totalAux = [];
        foreach ($itensOrcamentariosImpugnados as $k => $item) {
            $itensAux[$k]['NomeProjeto'] = utf8_encode($item->NomeProjeto);
            $itensAux[$k]['Produto'] = utf8_encode($item->Produto);
            $itensAux[$k]['Etapa'] = utf8_encode($item->Etapa);
            $itensAux[$k]['Item'] = utf8_encode($item->Item);
            $itensAux[$k]['Documento'] = utf8_encode($item->Documento);
            $itensAux[$k]['nrComprovante'] = utf8_encode($item->nrComprovante);
            $itensAux[$k]['tpFormaDePagamento'] = utf8_encode($item->tpFormaDePagamento);
            $itensAux[$k]['nrDocumentoDePagamento'] = utf8_encode($item->nrDocumentoDePagamento);
            $itensAux[$k]['Documento'] = utf8_encode($item->Documento);
            $itensAux[$k]['dsJustificativa'] = utf8_encode($item->dsJustificativa);
            $itensAux[$k]['vlComprovado'] = number_format($item->vlComprovado, 2, ',', '.');
            //totais
            $totalAux['vlComprovado'] += $item->vlComprovado;
        }

        $json['itensOrcamentariosImpugnados']['lines'] = $itensAux;
        $json['itensOrcamentariosImpugnados']['cols'] = [
            'NomeProjeto' => [ 'name' => 'Projeto'],
            'Produto' => [ 'name' => 'Produto'],
            'Etapa' => [ 'name' => 'Etapa'],
            'Item' => [ 'name' => 'Item'],
            'Documento' => [ 'name' => 'Documento'],
            'nrComprovante' => [ 'name' => 'Nr. Comprovante'],
            'tpFormaDePagamento' => [ 'name' => 'Forma de Pagamento'],
            'nrDocumentoDePagamento' => [ 'name' => 'Documento de Pagamento'],
            'dsJustificativa' => [ 'name' => 'Justificativa'],
            'vlComprovado' => [ 'name' => 'Valor Comprovado', 'class' => 'right-align']
        ];
        $json['itensOrcamentariosImpugnados']['title'] = 'ITENS ORÇAMENTÁRIOS IMPUGNADOS NA AVALIAÇÃO FINANCEIRA';
        $totalAux['NomeProjeto'] = 'Total';
        $totalAux['vlComprovado'] = number_format($totalAux['vlComprovado'], 2, ',', '.');
        $json['itensOrcamentariosImpugnados']['tfoot'] = $totalAux;

        $this->_helper->json($json);
    }
}
