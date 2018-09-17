<?php

class Projeto_Model_Menu extends MinC_Db_Table_Abstract
{
    private $projeto;
    private $usuarioInterno = false;
    private $usuarioExterno = false;
    private $permissoesMenu;
    private $debug = false;
    private $situacaoProjeto = '';
    private $IN2017 = false;
    private $idUsuario = 0;
    private $idGrupoAtivo = 0;

    public function init()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->idGrupoAtivo = $GrupoAtivo->codGrupo;

        $auth = Zend_Auth::getInstance();
        $this->idUsuario = $auth->getIdentity()->IdUsuario;

        if (!empty($this->idUsuario)) {
            $this->usuarioExterno = true;
        }

    }

    public function obterMenu($idPronac)
    {
        if (empty($idPronac)) {
            return false;
        }

        $this->obterVersaoInDoProjeto($idPronac);
        $projeto = $this->obterProjeto($idPronac);
        $arrMenu = $this->obterArrayMenuConvenio($idPronac, $projeto);

        if ($projeto['Mecanismo'] == 1) {
            $this->obterPermissoesMenu($idPronac, $projeto);
            $arrMenu = $this->obterArrayMenuMecenato($idPronac, $projeto);
        }

        return $arrMenu;
    }

    public function obterProjeto($idPronac)
    {
        if (empty($idPronac)) {
            return false;
        }

        $projetos = new Projeto_Model_DbTable_Projetos();
        return $projetos->findBy(['IdPRONAC' => $idPronac]);

    }

    public function obterVersaoInDoProjeto($idPronac)
    {
        $tbProjetos = new Projeto_Model_DbTable_Projetos();
        $this->IN2017 = $tbProjetos->verificarIN2017($idPronac);
    }

    public function obterPermissoesMenu($idPronac, $projeto)
    {

        if ($this->usuarioExterno && !empty($this->idUsuario)) {

            $projetos = new Projeto_Model_DbTable_Projetos();

            $links = new fnLiberarLinks();
            $linksXpermissao = $links->links(2, $projeto['CgcCpf'], $this->idUsuario, $idPronac);

            $linksGeral = str_replace(' ', '', explode('-', $linksXpermissao->links));

            $arrayLinks = array(
                'Permissao' => $linksGeral[0],
                'FaseDoProjeto' => $linksGeral[1],
                'Diligencia' => $linksGeral[2],
                'Recursos' => $linksGeral[3],
                'Readequacao' => $linksGeral[4],
                'ComprovacaoFinanceira' => $linksGeral[5],
                'RelatorioTrimestral' => $linksGeral[6],
                'RelatorioFinal' => $linksGeral[7],
                'Analise' => $linksGeral[8],
                'Execucao' => $linksGeral[9],
                'PrestacaoContas' => $linksGeral[10],
                'Readequacao_50' => $linksGeral[11],
                'Marcas' => $linksGeral[12],
                'SolicitarProrrogacao' => $linksGeral[13],
                'ReadequacaoPlanilha' => $linksGeral[14],
                'ReadequacaoTransferenciaRecursos' => $linksGeral[15],
                'ReadequacaoSaldoAplicacao' => $linksGeral[16],
                'AdequarExecucao' => $projetos->fnChecarLiberacaoDaAdequacaoDoProjeto($idPronac)
            );

            $this->permissoesMenu = $arrayLinks;
        }
    }

    public function obterArrayMenuConvenio($idPronac, $projeto)
    {
        $idPronacHash = $idPronac;
        if ($this->usuarioExterno) {
            $idPronacHash = Seguranca::encrypt($idPronac);
        }

        $pronac = $projeto['AnoProjeto'] . '' . $projeto['Sequencial'];
        $menu = [];
        $menu['informacoes'] = [
            'id' => 'informacoes',
            'titulo' => "Pronac {$pronac}",
            'descricao' => utf8_encode($projeto['NomeProjeto']),
            'icone_ativo' => 'info',
            'ativo' => true,
            'icone_inativo' => 'info'
        ];

        $menu['dadosprojeto'] = [
            'id' => 'dadosdoprojeto',
            'label' => 'Dados do Projeto',
            'title' => '',
            'link' => '/projeto/#/' . $idPronacHash,
            'ajax' => false,
            'icon' => 'home',
            'submenu' => '',
            'grupo' => []
        ];

        $menu['convenente'] = [
            'id' => 'convenente',
            'label' => 'Convenente',
            'title' => '',
            'link' => "/projeto/#/{$idPronacHash}/convenente",
            'ajax' => false,
            'icon' => 'person',
            'submenu' => '',
            'grupo' => []
        ];

        $menu['outrasinformacoes'] = [
            'id' => 'outrasinformacoes',
            'label' => 'Outras Informações',
            'title' => 'Outras Informações',
            'link' => '',
            'ajax' => false,
            'icon' => 'style',
            'submenu' => '',
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Certid&otilde;es Negativas',
            'title' => 'Ir para Dados Certid&otilde;es Negativas',
            'link' => '/default/consultardadosprojeto/certidoes-negativas/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Documentos anexados',
            'title' => 'Ir para  Documentos anexados',
            'link' => '/default/consultardadosprojeto/documentos-anexados/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Dilig&ecirc;ncias do projeto',
            'title' => 'Ir para Dilig&ecirc;ncias do projeto',
            'link' => '/default/consultardadosprojeto/diligencias/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Provid&ecirc;ncia tomada',
            'title' => 'Ir para Provid&ecirc;ncia tomada',
            'link' => '/default/consultardadosprojeto/providencia-tomada/?idPronac=' . $idPronacHash,
            'ajax' => false,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Tramita&ccedil;&atilde;o',
            'title' => 'Ir para Tramita&ccedil;&atilde;o',
            'link' => '/default/consultardadosprojeto/tramitacao/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        # Execução
        if ($this->permissoesMenu['Execucao'] || !$this->usuarioExterno || $this->debug) {

            $menu['execucao'] = [
                'id' => 'execucao',
                'label' => 'Execu&ccedil;&atilde;o',
                'title' => 'Menu Execu&ccedil;&atilde;o',
                'link' => '',
                'ajax' => false,
                'icon' => 'play_for_work',
                'submenu' => '',
                'grupo' => []
            ];

            if (in_array($this->permissoesMenu['FaseDoProjeto'], array('2', '3', '4', '5')) || !$this->usuarioExterno || $this->debug) {

                $menu['execucao']['submenu'][] = [
                    'label' => 'Dados da fiscaliza&ccedil;&atilde;o',
                    'title' => 'Ir para Dados da fiscaliza&ccedil;&atilde;o',
                    'link' => '/default/consultardadosprojeto/dados-fiscalizacao/?idPronac=' . $idPronacHash,
                    'ajax' => true,
                    'grupo' => []
                ];
            }
        }
        
        $menu['solicitacoes'] = [
            'id' => 'solicitacoes',
            'label' => $this->usuarioExterno ? "Minhas solicita&ccedil;&otilde;es" : "Solicita&ccedil;&otilde;es",
            'title' => 'Ir para Solicitações',
            'link' => '/solicitacao/mensagem/index/listarTudo/true/idPronac/' . $idPronac,
            'ajax' => false,
            'icon' => 'contact_mail',
            'submenu' => '',
            'grupo' => []
        ];

        return $menu;
    }

    public function obterArrayMenuMecenato($idPronac, $projeto)
    {
        $idPronacHash = $idPronac;
        if ($this->usuarioExterno) {
            $idPronacHash = Seguranca::encrypt($idPronac);
        }

        $pronac = $projeto['AnoProjeto'] . '' . $projeto['Sequencial'];
        $menu = [];
        $menu['informacoes'] = [
            'id' => 'informacoes',
            'titulo' => "Pronac {$pronac}",
            'descricao' => utf8_encode($projeto['NomeProjeto']),
            'icone_ativo' => 'info',
            'ativo' => true,
            'icone_inativo' => 'info'
        ];

        $menu['dadosprojeto'] = [
            'id' => 'dadosdoprojeto',
            'label' => 'Dados do Projeto',
            'title' => '',
//            'link' => '/default/consultardadosprojeto/index?idPronac=' . $idPronacHash,
            'link' => '/projeto/#/' . $idPronacHash,
            'ajax' => false,
            'icon' => 'home',
            'submenu' => '',
            'grupo' => []
        ];

        $menu['proponente'] = [
            'id' => 'proponente',
            'label' => 'Proponente',
            'title' => '',
            'link' => "/projeto/#/{$idPronacHash}/proponente",
            // 'link' => '/projeto/proponente-rest/get/?idPronac=' . $idPronacHash,
            'ajax' => false,
            'icon' => 'person',
            'submenu' => '',
            'grupo' => []
        ];

        $menu['outrasinformacoes'] = [
            'id' => 'outrasinformacoes',
            'label' => 'Outras Informações',
            'title' => 'Outras Informações',
            'link' => '',
            'ajax' => false,
            'icon' => 'style',
            'submenu' => '',
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Certid&otilde;es Negativas',
            'title' => 'Ir para Dados Certid&otilde;es Negativas',
            'link' => '/default/consultardadosprojeto/certidoes-negativas/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Dados complementares do projeto',
            'title' => 'Ir para Dados complementares do projeto',
            'link' => '/default/consultardadosprojeto/dados-complementares/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Documentos anexados',
            'title' => 'Ir para  Documentos anexados',
            'link' => '/default/consultardadosprojeto/documentos-anexados/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Documentos assinados',
            'title' => 'Ir para Documentos assinados',
            'link' => '/assinatura/index/visualizar-documentos-assinatura-ajax/idPronac/' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Dilig&ecirc;ncias do projeto',
            'title' => 'Ir para Dilig&ecirc;ncias do projeto',
            'link' => '/default/consultardadosprojeto/diligencias/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Local de realiza&ccedil;&atilde;o/Deslocamento',
            'title' => 'Ir para Local de realiza&ccedil;&atilde;o/Deslocamento',
            'link' => '/default/consultardadosprojeto/local-realizacao-deslocamento/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        if (!$this->IN2017) {
            $menu['outrasinformacoes']['submenu'][] = [
                'label' => 'Plano de distribui&ccedil;&atilde;o',
                'title' => 'Ir para Plano de distribui&ccedil;&atilde;o',
                'link' => '/default/consultardadosprojeto/plano-de-distribuicao/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];
        } else {
            $menu['outrasinformacoes']['submenu'][] = [
                'label' => 'Plano de distribui&ccedil;&atilde;o',
                'title' => 'Ir para Plano de distribui&ccedil;&atilde;o',
                'link' => '/proposta/visualizar-plano-distribuicao/visualizar/idPreProjeto/' . $projeto['idProjeto'],
                'ajax' => true,
                'grupo' => []
            ];
        }

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Provid&ecirc;ncia tomada',
            'title' => 'Ir para Provid&ecirc;ncia tomada',
            'link' => '/default/consultardadosprojeto/providencia-tomada/?idPronac=' . $idPronacHash,
            'ajax' => false,
            'grupo' => []
        ];

        $menu['outrasinformacoes']['submenu'][] = [
            'label' => 'Tramita&ccedil;&atilde;o',
            'title' => 'Ir para Tramita&ccedil;&atilde;o',
            'link' => '/default/consultardadosprojeto/tramitacao/?idPronac=' . $idPronacHash,
            'ajax' => true,
            'grupo' => []
        ];

        if (!$this->usuarioExterno || $this->debug) {
            $menu['outrasinformacoes']['submenu'][] = [
                'label' => 'Hist&oacute;rico encaminhamento',
                'title' => 'Ir para Hist&oacute;rico encaminhamento',
                'link' => '/default/consultardadosprojeto/historico-encaminhamento/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];
        }

        # Análise e Aprovação
        if ($this->permissoesMenu['Analise'] || !$this->usuarioExterno || $this->debug) {

            $menu['analiseaprovacao'] = [
                'id' => 'analiseaprovacao',
                'label' => 'An&aacute;lise e Aprova&ccedil;&atilde;o',
                'title' => 'An&aacute;lise e Aprova&ccedil;&atilde;o',
                'link' => '',
                'ajax' => false,
                'icon' => 'gavel',
                'submenu' => '',
                'grupo' => []
            ];

            $menu['analiseaprovacao']['submenu'][] = [
                'label' => 'An&aacute;lise do projeto',
                'title' => 'Ir para An&aacute;lise do projeto',
                'link' => '/default/consultardadosprojeto/analise-projeto/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['analiseaprovacao']['submenu'][] = [
                'label' => 'Aprova&ccedil;&atilde;o',
                'title' => 'Ir para Aprova&ccedil;&atilde;o',
                'link' => '/default/consultardadosprojeto/aprovacao/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['analiseaprovacao']['submenu'][] = [
                'label' => 'Recursos',
                'title' => 'Ir para Recursos',
                'link' => '/default/consultardadosprojeto/recurso/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

        }

        # Execução
        if ($this->permissoesMenu['Execucao'] || !$this->usuarioExterno || $this->debug) {

            $menu['execucao'] = [
                'id' => 'execucao',
                'label' => 'Execu&ccedil;&atilde;o',
                'title' => 'Menu Execu&ccedil;&atilde;o',
                'link' => '',
                'ajax' => false,
                'icon' => 'play_for_work',
                'submenu' => '',
                'grupo' => []
            ];

            if (in_array($this->permissoesMenu['FaseDoProjeto'], array('2', '3', '4', '5')) || !$this->usuarioExterno || $this->debug) {

                $menu['execucao']['submenu'][] = [
                    'label' => 'Dados banc&aacute;rios',
                    'title' => 'Ir para Dados banc&aacute;rios',
                    'link' => '/default/consultardadosprojeto/dados-bancarios/?idPronac=' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];

                $menu['execucao']['submenu'][] = [
                    'label' => 'Dados da fiscaliza&ccedil;&atilde;o',
                    'title' => 'Ir para Dados da fiscaliza&ccedil;&atilde;o',
                    'link' => '/default/consultardadosprojeto/dados-fiscalizacao/?idPronac=' . $idPronacHash,
                    'ajax' => true,
                    'grupo' => []
                ];

            }

            $menu['execucao']['submenu'][] = [
                'label' => 'Dados das readequa&ccedil;&otilde;es',
                'title' => 'Ir para Dados das readequa&ccedil;&otilde;es',
                'link' => '/default/consultardadosprojeto/readequacoes/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['execucao']['submenu'][] = [
                'label' => 'Marcas Anexadas',
                'title' => 'Ir para Marcas Anexadas',
                'link' => '/default/consultardadosprojeto/marcas-anexadas/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['execucao']['submenu'][] = [
                'label' => 'Pedido de Prorroga&ccedil;&atilde;o',
                'title' => 'Ir para Pedido de Prorroga&ccedil;&atilde;o',
                'link' => '/default/consultardadosprojeto/pedido-prorrogacao/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];
        }

        # Prestação de contas
        if ($this->permissoesMenu['PrestacaoContas'] || !$this->usuarioExterno || $this->debug) {
            $menu['prestacaodecontas'] = [
                'id' => 'prestacaodecontas',
                'label' => 'Presta&ccedil;&atilde;o de Contas',
                'title' => 'Menu Presta&ccedil;&atilde;o de Contas',
                'link' => '',
                'ajax' => false,
                'icon' => 'list_alt',
                'submenu' => '',
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Extrato Banc&aacute;rio',
                'title' => 'Ir para Extrato Banc&aacute;rio',
                'link' => '/default/consultardadosprojeto/extratos-bancarios/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Concilia&ccedil;&atilde;o Banc&aacute;ria',
                'title' => 'Ir para Concilia&ccedil;&atilde;o Banc&aacute;ria',
                'link' => '/default/consultardadosprojeto/conciliacao-bancaria/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Rela&ccedil;&atilde;o de pagamentos',
                'title' => 'Ir para Rela&ccedil;&atilde;o de Pagamentos',
                'link' => '/default/consultardadosprojeto/dados-relacao-pagamentos/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Execu&ccedil;&atilde;o da receita e despesa',
                'title' => 'Ir para Execu&ccedil;&atilde;o da receita e despesa',
                'link' => '/default/consultardadosprojeto/execucao-receita-despesa/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Relat&oacute;rio f&iacute;sico',
                'title' => 'Ir para Relat&oacute;rio f&iacute;sico',
                'link' => '/default/consultardadosprojeto/relatorio-fisico/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];


            $menu['prestacaodecontas']['submenu'][] = [
              'label' => 'Pagamentos por UF / Munic&iacute;pio',
              'title' => 'Ir para Pagamentos por UF / Munic&iacute;pio',
              'link' => '/default/consultardadosprojeto/pagamentos-por-uf-municipio/?idPronac=' . $idPronacHash,
              'ajax' => true,
              'grupo' => []
            ];

            $menu['prestacaodecontas']['submenu'][] = [
                'label' => 'Pagamentos Consolidados',
                'title' => 'Ir para Pagamentos Consolidados',
                'link' => '/default/consultardadosprojeto/pagamentos-consolidados-por-uf-municipio/?idPronac=' . $idPronacHash,
                'ajax' => true,
                'grupo' => []
            ];

            if (in_array($this->permissoesMenu['FaseDoProjeto'], array('2', '3', '4', '5')) || !$this->usuarioExterno || $this->debug) {
                $menu['prestacaodecontas']['submenu'][] = [
                    'label' => 'Relat&oacute;rios trimestrais',
                    'title' => 'Ir para Relat&oacute;rios trimestrais',
                    'link' => '/default/consultardadosprojeto/relatorios-trimestrais/?idPronac=' . $idPronacHash,
                    'ajax' => true,
                    'grupo' => []
                ];
            }

            if (in_array($this->permissoesMenu['FaseDoProjeto'], array('4', '5')) || !$this->usuarioExterno || $this->debug) {
                $menu['prestacaodecontas']['submenu'][] = [
                    'label' => 'Relat&oacute;rio de cumprimento do objeto',
                    'title' => 'Ir para Relat&oacute;rio de cumprimento do objeto',
                    'link' => '/default/consultardadosprojeto/relatorio-final/?idPronac=' . $idPronacHash,
                    'ajax' => true,
                    'grupo' => []
                ];
            }
        }

        # Readequacao
        if ($this->usuarioExterno && ($this->permissoesMenu['Readequacao'] || $this->permissoesMenu['Readequacao_50']) || $this->debug) {
            $menu['readequacao'] = [
                'id' => 'readequacao',
                'label' => 'Readequa&ccedil;&atilde;o',
                'title' => 'Menu Readequa&ccedil;&atilde;o',
                'link' => '',
                'ajax' => false,
                'icon' => 'update',
                'submenu' => '',
                'grupo' => []
            ];

            $menu['readequacao']['submenu'][] = [
                'label' => 'Local de realiza&ccedil;&atilde;o',
                'title' => 'Readequar Local de realizaca&ccedil;&atilde;o',
                'link' => '/readequacao/local-realizacao/index/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];

            if ($this->permissoesMenu['ReadequacaoPlanilha'] || $this->debug) {

                $menu['readequacao']['submenu'][] = [
                    'label' => 'Planilha orçament&aacute;ria',
                    'title' => 'Readequar Planilha orçament&aacute;ria',
                    'link' => '/readequacao/readequacoes/planilha-orcamentaria/?idPronac=' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];
            }

            $menu['readequacao']['submenu'][] = [
                'label' => 'Plano de Distribui&ccedil;&atilde;o',
                'title' => 'Readequar Plano de Distribui&ccedil;&atilde;o',
                'link' => '/readequacao/plano-distribuicao/index/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];

            if ($this->permissoesMenu['Readequacao_50'] || $this->debug) {
                $menu['readequacao']['submenu'][] = [
                    'label' => 'Remanejamento &le; 50%',
                    'title' => 'Readequar Remanejamento &le; 50%',
                    'link' => '/readequacao/remanejamento-menor/index/?idPronac=' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];
            }

            if ($this->permissoesMenu['ReadequacaoTransferenciaRecursos'] || $this->debug) {
                $menu['readequacao']['submenu'][] = [
                    'label' => 'Transfer&ecirc;ncia de recursos',
                    'title' => 'Readequar Transfer&ecirc;ncia de recursos',
                    'link' => '/readequacao/transferencia-recursos/index/?idPronac=' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];
            }

            if ($this->permissoesMenu['ReadequacaoSaldoAplicacao'] || $this->debug) {
                $menu['readequacao']['submenu'][] = [
                    'label' => 'Saldo de aplica&ccedil;&atilde;o',
                    'title' => 'Ir para Saldo de aplica&ccedil;&atilde;o',
                    'link' => '/readequacao/saldo-aplicacao/index/?idPronac=' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];
            }

            $menu['readequacao']['submenu'][] = [
                'label' => 'Diversas',
                'title' => 'Readequa&ccedil;&otilde;es Diversas',
                'link' => '/readequacao/readequacoes/index/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];
        }

        if ($this->permissoesMenu['SolicitarProrrogacao'] || $this->debug) {
            $menu['prorrogacao'] = [
                'id' => 'prorrogacao',
                'label' => 'Solicitar Prorroga&ccedil;&atilde;o',
                'title' => 'Menu Solicitar Prorroga&ccedil;&atilde;o',
                'link' => '/default/solicitarprorrogacao/index/idpronac/' . $idPronacHash,
                'ajax' => false,
                'icon' => 'av_timer',
                'submenu' => '',
                'grupo' => []
            ];
        }

        if ($this->permissoesMenu['AdequarExecucao'] || $this->debug) {
            $menu['adequacao'] = [
                'id' => 'prorrogacao',
                'label' => 'Adequar &agrave; realidade',
                'title' => 'Adequar &agrave; realidade ou Encaminhar projeto adequado para o MinC',
                'link' => '/proposta/manterpropostaincentivofiscal/identificacaodaproposta/idPreProjeto/' . $projeto['idProjeto'],
                'ajax' => false,
                'icon' => 'timer',
                'submenu' => '',
                'grupo' => []
            ];
        }

        if ($this->permissoesMenu['Diligencia'] || $this->debug) {
            $menu['diligencia'] = [
                'id' => 'diligencia',
                'label' => 'Dilig&ecirc;ncias',
                'title' => 'Responder Dilig&ecirc;ncias',
                'link' => '/proposta/diligenciar/listardiligenciaproponente/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'icon' => 'warning',
                'badge' => '1',
                'submenu' => '',
                'grupo' => []
            ];
        }

        if ($this->permissoesMenu['Recursos'] || $this->debug) {
            $menu['recurso'] = [
                'id' => 'recurso',
                'label' => 'Recurso',
                'title' => 'Solicitar recurso ou desistir',
                'link' => '/proposta/diligenciar/listardiligenciaproponente/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'icon' => 'insert_comment',
                'submenu' => '',
                'grupo' => []
            ];

            $menu['recurso']['submenu'][] = [
                'label' => 'Solicitar Recurso',
                'title' => 'Ir para Solicitar Recurso',
                'link' => '/default/solicitarrecursodecisao/recurso/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];

            $menu['recurso']['submenu'][] = [
                'label' => 'Desistir do Recurso',
                'title' => 'Ir para Desistir do Recurso',
                'link' => '/default/solicitarrecursodecisao/recurso-desistir/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'grupo' => []
            ];
        }

        if ($this->permissoesMenu['ComprovacaoFinanceira'] || $this->debug) {
            $menu['comprovacaofinaceira'] = [
                'id' => 'comprovacaofinaceira',
                'label' => 'Comprova&ccedil;&atilde;o Financeira',
                'title' => 'Ir para Realizar Comprova&ccedil;&atilde;o Financeira',
                'link' => '/default/comprovarexecucaofinanceira/pagamento?idusuario=' . $this->idUsuario . '&idpronac=' . $idPronac,
                'ajax' => false,
                'icon' => 'attach_money',
                'submenu' => '',
                'grupo' => []
            ];
        }                    

        if ($this->situacaoProjeto != 'E24' || $this->debug) {

            if ($this->permissoesMenu['RelatorioTrimestral']
                || $this->permissoesMenu['RelatorioFinal'] || $this->debug) {

                $menu['comprovacaofisica'] = [
                    'id' => 'comprovacaofisica',
                    'label' => 'Comprova&ccedil;&atilde;o Física',
                    'title' => 'Ir para Realizar Comprova&ccedil;&atilde;o Física',
                    'link' => '',
                    'ajax' => false,
                    'icon' => 'attach_file',
                    'submenu' => '',
                    'grupo' => []
                ];

            }

            if ($this->permissoesMenu['RelatorioTrimestral'] || $this->debug) {
                $menu['comprovacaofisica']['submenu'][] = [
                    'label' => 'Relat&oacute;rio Trimestral',
                    'title' => 'Ir para Relat&oacute;rio Trimestral',
                    'link' => '/default/comprovarexecucaofisica/relatoriotrimestral/idpronac/' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];

            }
            

            if ($this->permissoesMenu['RelatorioFinal'] || $this->debug) {
                $menu['comprovacaofisica']['submenu'][] = [
                    'label' => 'Comprovar Realiza&ccedil;&atilde;o do Objeto',
                    'title' => 'Ir para Comprovar Realiza&ccedil;&atilde;o do Objeto',
                    'link' => '/default/comprovarexecucaofisica/etapas-de-trabalho-final/idpronac/' . $idPronacHash,
                    'ajax' => false,
                    'grupo' => []
                ];
            }
        }

        if ($this->usuarioExterno && $this->permissoesMenu['Marcas'] || $this->debug) {
            $menu['marcas'] = [
                'id' => 'marcas',
                'label' => 'Marcas',
                'title' => 'Ir para Marcas',
                'link' => '/default/upload/form-enviar-arquivo-marca/?idPronac=' . $idPronacHash,
                'ajax' => false,
                'icon' => 'image',
                'submenu' => '',
                'grupo' => []
            ];
        }

        if ($this->usuarioExterno || $this->debug) {
            $menu['projetos'] = [
                'id' => 'projetos',
                'label' => 'Listar Projetos',
                'title' => 'Ir para Listar Projetos',
                'link' => '/default/listarprojetos/listarprojetos',
                'ajax' => false,
                'icon' => 'ballot',
                'submenu' => '',
                'grupo' => []
            ];
        }

//        $perfisMensagens = array(131, 92, 93, 122, 123, 121, 129, 94, 103, 110, 118, 126, 125, 124, 132, 136, 134, 135, 138, 139);
//        if (in_array($this->idGrupoAtivo, $perfisMensagens) || $this->debug) {
//
//            $menu['mensagens'] = [
//                'id' => 'mensagens',
//                'label' => 'Mensagens',
//                'title' => 'Ir para Mensagens',
//                'link' => '/default/mantermensagens/consultarmensagem/idpronac/' . $idPronacHash,
//                'ajax' => false,
//                'icon' => 'email',
//                'submenu' => '',
//                'grupo' => []
//            ];
//        }

        $menu['solicitacoes'] = [
            'id' => 'solicitacoes',
            'label' => $this->usuarioExterno ? "Minhas solicita&ccedil;&otilde;es" : "Solicita&ccedil;&otilde;es",
            'title' => 'Ir para Solicitações',
            'link' => '/solicitacao/mensagem/index/listarTudo/true/idPronac/' . $idPronacHash,
            'ajax' => false,
            'icon' => 'contact_mail',
            'submenu' => '',
            'grupo' => []
        ];

        return $menu;
    }

    public function setDebug()
    {
        $this->debug = true;
    }

}
