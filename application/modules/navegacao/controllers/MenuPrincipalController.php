<?php

    class Navegacao_MenuPrincipalController extends Zend_Rest_Controller{

        public function init()
        {
            $this->auth = Zend_Auth::getInstance();

            $this->GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

            if (isset($this->auth->getIdentity()->usu_codigo)) {
                $this->codGrupo = $this->GrupoAtivo->codGrupo;
                $this->codOrgao = $this->GrupoAtivo->codOrgao;
                $this->codOrgaoSuperior = (!empty($this->auth->getIdentity()->usu_org_max_superior)) ? $this->auth->getIdentity()->usu_org_max_superior : null;
            }

            $this->_helper->getHelper('contextSwitch')
                ->addActionContext('get', 'json')
                ->addActionContext('index', 'json')
                ->addActionContext('post', 'json')
                ->addActionContext('put', 'json')
                ->addActionContext('delete', 'json')
                ->initContext('json');
        }

        public function indexAction(){
//            ini_set('xdebug.var_display_max_depth', 5);
//           var_dump($this->usuarioProponente());die;
            $menuProponente = [];
            $menu = [];
            $menuProponente += $this->administrativoProponente();
            $menuProponente += $this->propostaProponente();
            $menuProponente += $this->projetoProponente();
            $menuProponente += $this->solicitacoesProponente();
            $menuProponente += $this->usuarioProponente();
            $menu += $this->prestacaoContas();
            $menu += $this->analise();
            $menu += $this->administrativo();
            $menu += $this->assinatura();
            $menu += $this->atendimento();
            $menu += $this->acompanhamento();
            $menu += $this->protocolo();
            $menu += $this->admissibilidade();
            $menu += $this->parecer();
            $menu += $this->edital();
            $menu += $this->relatorios();
            $menu += $this->segurança();
            $menu += $this->manuais();
            $menu += $this->aulas();
            $menu += $this->manutencao();


            $menu = $this->filtro($menu,$menuProponente);
            $this->view->assign('data', $menu );
            $this->getResponse()->setHttpResponseCode(200);
        }

        public function prestacaoContas():array{
            $arrMenu['prestacao-contas'] = [
                'id' => 'prestacao-contas',
                'label' => 'Presta&ccedil;&atilde;o de Contas',
                'title' => 'Ir para Presta&ccedil;&atilde;o de Contas',
                'menu' => [],
                'grupo' => [100,124,125,126,132,148,151],
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'label' => 'Analisar Presta&ccedil;&atilde;o de Contas',
                'title' => 'Ir para Analisar Presta&ccedil;&atilde;o de Contas',
                'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'painel'],
                'grupo' =>[126]
            ];
            /* $arrMenu['prestacao-contas']['menu'][] = [ */
            /*     'grupo' => [125], */
            /*     'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'painel'], */
            /*     'title' => 'Analisar Presta&ccedil;&atilde;o de Contas', */
            /*     'label' => 'Analisar Presta&ccedil;&atilde;o de Contas' */
            /* ]; */
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [125],
                'url' => ['module' => 'avaliacao-resultados','controller' => 'index', 'action' => 'index'],
                'title' => 'Analisar Presta&ccedil;&atilde;o de Contas',
                'label' => 'Analisar Presta&ccedil;&atilde;o de Contas'
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [125],
                'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'manter-assinantes'],
                'title' => 'Ir para Assinantes',
                'label' => 'Assinantes'
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [124],
                'url' => ['module' => 'avaliacao-resultados','controller' => 'index', 'action' => 'index#/painel'],
                'title' => 'Analisar Presta&ccedil;&atilde;o de Contas',
                'label' => 'Analisar Presta&ccedil;&atilde;o de Contas'
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [12],
                'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'conjurprestacaocontas'],
                'title' => 'Analisar Presta&ccedil;&atilde;o de Contas',
                'label' => 'Ir para Presta&ccedil;&atilde;o de Contas'
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [177],
                'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'aeciprestacaocontas'],
                'title' => 'Analisar Presta&ccedil;&atilde;o de Contas',
                'label' => 'Analisar Presta&ccedil;&atilde;o de Contas'
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [132],
                'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'chefedivisaoprestacaocontas'],
                'title' => 'Analisar Presta&ccedil;&atilde;o de Contas',
                'label' => 'Analisar Presta&ccedil;&atilde;o de Contas'
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [93],
                'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'coordenadorpareceristaprestacaocontas'],
                'title' => 'Analisar Presta&ccedil;&atilde;o de Contas',
                'label' => 'Analisar Presta&ccedil;&atilde;o de Contas'
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [94, 93],
                'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'pareceristaprestacaocontas'],
                'title' => 'Ir para Presta&ccedil;&atilde;o de Contas',
                'label' => 'Analisar Presta&ccedil;&atilde;o de Contas'
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [126,148,151],
                'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'analisar-laudo-final'],
                'title' => 'Ir para Analisar Laudo Final',
                'label' => 'Analisar Laudo Final'
            ];
            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [124,125,132],
                'url' => ['module' => 'default','controller' => 'realizarprestacaodecontas', 'action' => 'consultar-laudo-final'],
                'title' => 'Ir para Analisar Laudo Final',
                'label' => 'Analisar Laudo Final'
            ];

            $arrMenu['prestacao-contas']['menu'][] = [
                'grupo' => [147,148,149,150,151,152],
                'url' =>['module' => 'admissibilidade', 'controller' => 'enquadramento-assinatura', 'action' => 'gerenciar-assinaturas'],
                'title' => 'Assinatura',
                'label' => 'Assinatura'
            ];
            return $arrMenu;
        }

        public function analise(){
            $arrMenu['analise'] = [
                'id' => 'analise',
                'label' => 'An&aacute;lise',
                'title' => 'Ir para An&aacute;lise',
                'menu' => [],
                'grupo' => [
                    Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA,
                    Autenticacao_Model_Grupos::SUPERINTENDENTE_DE_VINCULADA,
                    Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER,
                    Autenticacao_Model_Grupos::PARECERISTA,
                    Autenticacao_Model_Grupos::GESTOR_SALIC,
                    Autenticacao_Model_Grupos::COORDENADOR_ANALISE,
                    Autenticacao_Model_Grupos::TECNICO_ANALISE,
                    118,
                    Autenticacao_Model_Grupos::PRESIDENTE_CNIC,
                    Autenticacao_Model_Grupos::COORDENADOR_CNIC,
                    Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO,
                    Autenticacao_Model_Grupos::TECNICO_PORTARIA,
                    132,
                    Autenticacao_Model_Grupos::MEMBROS_NATOS_CNIC,
                    137,
                    141,
                    142,
                    143,148,151],
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Realizar An&aacute;lise',
                'title' => 'Ir para An&aacute;lise do Membro da Comiss&atilde;o',
                'url' => [ 'module' => 'default' , 'controller' => 'areadetrabalho', 'action' => 'index'],
                'grupo' => [118,148,151]
            ];

            $arrMenu['analise']['menu'][] =[
                'label' => 'Avaliar adequa&ccedil;&atilde;o de projeto',
                'title' => 'Ir para Avaliar adequa&ccedi;&atilde;o de projeto',
                'url' => ['module' => 'analise', 'controller' => 'analise', 'action' => 'listarprojetos'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Autenticacao_Model_Grupos::TECNICO_ANALISE],
            ];

            $arrMenu['analise']['menu'][] = [
                'label' => 'Reuni&atilde;o CNIC',
                'title' => 'Ir para Gerenciar Pauta da Reuni&atilde;o',
                'url' => ['module'=> 'default' ,'controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao'],
                'grupo' => [118,Autenticacao_Model_Grupos::MEMBROS_NATOS_CNIC]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Reuni&atilde;o CNIC',
                'title' => 'Ir para Gerenciar Pauta da Reuni&atilde;o',
                'url' => ['module'=>'default', 'controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao'],
                'grupo' => [Autenticacao_Model_Grupos::PRESIDENTE_CNIC]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Reuni&atilde;o CNIC',
                'title' => 'Ir para Gerenciar Pauta da Reuni&atilde;o',
                'url' => ['module' => 'default', 'controller' => 'gerenciarpautareuniao', 'action' => 'gerenciaradministrativo'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_CNIC,148,151]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Gerenciar Pareceres',
                'title' => 'Ir para Gerenciar Pareceres',
                'url' => ['module' => 'default', 'controller' => 'gerenciarpareceres', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Autenticacao_Model_Grupos::TECNICO_ANALISE,148,151]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Imprimir Parecer T&eacute;cnico',
                'title' => 'Ir para Imprimir Parecer T&eacute;cnico',
                'url' => ['module'=> 'default' ,'controller' => 'gerenciarpareceres', 'action' => 'imprimir-parecer-tecnico'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER,Autenticacao_Model_Grupos::GESTOR_SALIC,Autenticacao_Model_Grupos::COORDENADOR_ANALISE,137]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Gerenciar Componente da Comiss&atilde;o',
                'title' => 'Ir para Gerenciar Componente da Comiss&atilde;o',
                'url' => ['module'=> 'default', 'controller' => 'projetosgerenciar', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE,148,151]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Homologar Projetos',
                'title' => 'Ir para Homologa&ccedil;&atilde;o dos Projetos',
                'url' => ['module' => 'projeto', 'controller' => 'homologacao', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE,148,151]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Assinar Homologa&ccedil;&atilde;o do Projetos',
                'title' => 'Ir para Assinatura de Homologa&ccedil;&atilde;o dos Projetos',
                'url' => ['module' => 'projeto', 'controller' => 'assinatura', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE,148,151]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Gerar Termo de Decis&atilde;o',
                'title' => 'Ir para Gerar Termo de Decis&atilde;o',
                'url' => ['module' => 'default' ,'controller' => 'gerartermodeaprovacao', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE,Autenticacao_Model_Grupos::TECNICO_ANALISE,Autenticacao_Model_Grupos::COORDENADOR_CNIC,Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'CheckList para Publica&ccedil;&atilde;o',
                'title' => 'Ir para CheckList para Publica&ccedil;&atilde;o',
                'url' => ['module' => 'default' ,'controller' => 'checklistpublicacao', 'action' => 'listas'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE,Autenticacao_Model_Grupos::TECNICO_ANALISE,Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO,148,151]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Certid&otilde;es Negativas',
                'title' => 'Ir para Certid&otilde;es Negativas',
                'url' => ['module' => 'default' ,'controller' => 'manterregularidadeproponente', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE,Autenticacao_Model_Grupos::TECNICO_ANALISE,Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Gerar Publica&ccedil;&atilde;o para DOU',
                'title' => 'Ir para Gerar Publica&ccedil;&atilde;o para DOU',
                'url' => ['module'=> 'default' ,'controller' => 'publicacaodou', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::TECNICO_PORTARIA]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Atualiza&ccedil;&atilde;o de Portaria',
                'title' => 'Ir para Atualiza&ccedil;&atilde;o de Portaria',
                'url' => ['module' => 'default', 'controller' => 'publicacaodou', 'action' => 'consultar-portaria'],
                'grupo' => [Autenticacao_Model_Grupos::TECNICO_PORTARIA]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Cadastrar Projetos FNC',
                'title' => 'Ir para Cadastrar Projetos FNC',
                'url' => ['module' => 'default' ,'controller' => 'cadastrar-projeto', 'action' => 'index'],
                'grupo' => [142]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Calend&aacute;rio CNIC',
                'title' => 'Ir para Calend&aacute;rio CNIC',
                'url' => ['module'=> 'default' ,'controller' => 'mantercalendariocnic', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_CNIC]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Gerenciar Pareceres',
                'title' => 'Ir para Gerenciar Pareceres',
                'url' => ['module' => 'parecer', 'controller' => 'gerenciar-parecer', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Gerenciar Pareceres',
                'title' => 'Ir para Gerenciar Pareceres',
                'url' => ['module' => 'parecer', 'controller' => 'gerenciar-parecer', 'action' => 'finalizar-parecer'],
                'grupo' => [Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Gerenciar Pareceres',
                'title' => 'Ir para Gerenciar Pareceres',
                'url' => ['module' => 'parecer', 'controller' => 'gerenciar-parecer', 'action' => 'finalizar-parecer'],
                'grupo' => [Autenticacao_Model_Grupos::SUPERINTENDENTE_DE_VINCULADA]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'An&aacute;lise T&eacute;cnica Inicial',
                'title' => 'Ir para An&aacute;lise T&eacute;cnica Inicial',
                'url' => ['module' => 'parecer', 'controller' => 'analise-inicial', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::PARECERISTA]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Parecer T&eacute;cnico FNC',
                'title' => 'Ir para Parecer T&eacute;cnico FNC',
                'url' => ['module'=> 'default','controller' => 'aprovacaoeparecer', 'action' => 'index'],
                'grupo' => [143]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Aprova&ccedil;&atilde;o FNC',
                'title' => 'Ir para Aprova&ccedil;&atilde;o FNC',
                'url' => ['module' => 'default','controller' => 'aprovacaoeparecer', 'action' => 'index'],
                'grupo' => [141,142]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Recurso',
                'title' => 'Ir para Recurso',
                'url' => ['module' => 'default', 'controller' => 'recursos', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE,Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO]
            ];

            $arrMenu['analise']['menu'][] = [
                'label' => 'Avaliar Recursos',
                'title' => 'Ir para Avaliar Recursos',
                'url' => ['module' => 'default', 'controller' => 'recursos', 'action' => 'painel-recursos'],
                'grupo' => [ Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER, Autenticacao_Model_Grupos::PARECERISTA, Autenticacao_Model_Grupos::TECNICO_ANALISE]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Avaliar Readequa&ccedil;&otilde;es',
                'title' => 'Ir para Avaliar Readequa&ccedil;&otilde;es',
                'url' => ['module' => 'readequacao', 'controller' => 'readequacoes', 'action' => 'painel-readequacoes'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER, Autenticacao_Model_Grupos::PARECERISTA, Autenticacao_Model_Grupos::TECNICO_ANALISE],
            ];

            $arrMenu['analise']['menu'][] = [
                'label' => 'Avaliar Readequa&ccedil;&otilde;es',
                'title' => 'Ir para Avaliar Readequa&ccedil;&otilde;es',
                'url' => ['module' => 'readequacao', 'controller' => 'readequacoes', 'action' => 'analisar-readequacoes-cnic'],
                'grupo' => [118]
            ];
            $arrMenu['analise']['menu'][] = [
                'label' => 'Avaliar Recursos',
                'title' => 'Ir para Avaliar Recursos',
                'url' => ['module' => 'default', 'controller' => 'recursos', 'action' => 'analisar-recursos-cnic'],
                'grupo' => [118]
            ];

            $arrMenu['analise']['menu'][] = [
                'label' => 'Configurar Assinatura',
                'title' => 'Ir para Configurar Assinatura',
                'url' => ['module'=> 'default', 'controller' => 'configuracoes', 'action' => 'secretarios '],
                'grupo' => [Autenticacao_Model_Grupos::TECNICO_PORTARIA]
            ];
            $arrMenu['analise']['menu'][] = [
                'grupo' => [ 147, 148, 149, 150, 151, 152 ],
                'url' => ['module' => 'admissibilidade', 'controller' => 'enquadramento-assinatura', 'action' => 'gerenciar-assinaturas'],
                'title' => 'Assinatura',
                'label' => 'Assinatura'
            ];

            return $arrMenu;
        }
        public function administrativo(){

            $arrMenu['administrativo'] = [
                'id' => 'administrativo',
                'label' => 'Administrativo',
                'title' => 'Ir para Administrativo',
                'menu' => [],
                'grupo' => [
                    93,
                    94,
                    97,
                    Autenticacao_Model_Grupos::COORDENADOR_ANALISE,
                    118,
                    Autenticacao_Model_Grupos::COORDENADOR_CNIC,
                    121,
                    122,
                    123,
                    137,
                ],
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Manter Agentes',
                'title' => 'Ir para Manter Agentes',
                'url' => ['module' => 'agente', 'controller' => 'agentes', 'action' => 'agentes'],
                'grupo' => [97, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 121, 122, 123]
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Gerenciar meus dados', 'title' => 'Ir para Gerenciar meus dados',
                'url' => ['module' => 'agente', 'controller' => 'agentes', 'action' => 'agentes'],
                'grupo' => [94, 118]
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Desvincular Agentes',
                'title' => 'Ir para Desvincular Agentes',
                'url' => ['module'=>'default','controller' => 'desvincularagentes', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 122]
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Analisar Procura&ccedil;&atilde;o',
                'title' => 'Ir para Analisar Procura&ccedil;&atilde;o',
                'url' => ['module'=>'default','controller' => 'procuracao', 'action' => 'analisar'],
                'grupo' => [122, Autenticacao_Model_Grupos::COORDENADOR_ANALISE]
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Manter Parecerista',
                'title' => 'Ir para Manter Parecerista',
                'url' =>['module' => 'agente', 'controller' => 'agentes', 'action' => 'painelcredenciamento'],
                'grupo' => [137, 93]
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Gerenciar assinantes',
                'title' => 'Ir para Gerenciar assinantes',
                'url' => ['module'=>'default','controller' => 'parecerista', 'action' => 'gerenciar-assinantes'],
                'grupo' => [137]
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Manter Termo de Decis&atilde;o',
                'title' => 'Ir para Manter Termo de Decis&atilde;o',
                'url' => ['module'=>'default','controller' => 'mantertermodecisao', 'action' => 'index'],
                'grupo' => [97]
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Manter Secret&aacute;rio',
                'title' => 'Ir para Manter Secret&aacute;rio',
                'url' => ['module'=>'default','controller' => 'mantersecretarioorgao', 'action' => 'index'],
                'grupo' => [97]
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Comunicados',
                'title' => 'Ir para Comunicados',
                'url' => ['module'=> 'default','controller' => 'comunicados', 'action' => 'index'],
                'grupo' => [97]
            ];
            $arrMenu['administrativo']['menu'][] = [
                'label' => 'Analisar Solicita&ccedil;&atilde;o de Item',
                'title' => 'Ir para Analisar Solicita&ccedil;&atilde;o de Item',
                'url' => ['module' => 'proposta', 'controller' => 'analisarsituacaoitem', 'action' => 'index'],
                'grupo' => [97]
            ];
            return $arrMenu;


            return $arrMenuProponente;
        }
        public function administrativoProponente(){

            $arrMenuProponente['administrativo'] = [
                'id' => 'administrativo',
                'label' => 'Administrativo',
                'title' => 'Ir para Administrativo',
                'menu' => [],
                'grupo' => [],
            ];
            $arrMenuProponente['administrativo']['menu'][] = [
                'label' => 'Cadastrar Proponente',
                'title' => 'Ir para Manter Agentes',
                'url' => ['module' => 'agente','controller' => 'agentes', 'action' => 'incluiragente'],
                'grupo' => []
            ];
            $arrMenuProponente['administrativo']['menu'][] = [
                'label' => 'Gerenciar respons&aacute;veis',
                'title' => 'Ir para Aceitar vinculo',
                'url' =>['module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal','action' => 'consultarresponsaveis'],
                'grupo' => []
            ];
            $arrMenuProponente['administrativo']['menu'][] = [
                'label' => 'Procura&ccedil;&atilde;o',
                'title' => 'Ir para Procura&ccedil;&atilde;o',
                'url' => ['module'=> 'default','controller' => 'procuracao', 'action' => 'index'],
                'grupo' => []
            ];
            return $arrMenuProponente;
        }
        public function propostaProponente(){
            $arrMenuProponente['proposta'] = [
                'id' => 'proposta',
                'label' => 'Proposta',
                'title' => 'Ir para Proposta',
                'menu' => [],
                'grupo' => [],
            ];
            $arrMenuProponente['proposta']['menu'][] = [
                'label' => 'Listar',
                'title' => 'Ir para Manter Proposta por Incentivo Fiscal',
                'url' => ['module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'listarproposta'],
                'grupo' => []
            ];

            $arrMenuProponente['proposta']['menu'][] = [
                'label' => 'Arquivadas',
                'title' => 'Propostas Arquivadas',
                'url' => ['module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'listar-propostas-arquivadas'],
                'grupo' => []
            ];
            return $arrMenuProponente;
        }
        public function projetoProponente(){

            $arrMenuProponente['projeto'] = [
                'id' => 'projeto',
                'label' => 'Projeto',
                'title' => 'Ir para Projetos',
                'menu' => [],
                'grupo' => [],
            ];
            $arrMenuProponente['projeto']['menu'][] = [
                'label' => 'Listar Projetos',
                'title' => 'Ir para Listar Projetos',
                'url' => ['module' => 'projeto', 'controller' => 'index', 'action' => 'listar'],
                'grupo' => []
            ];

            return $arrMenuProponente;
        }
        public function solicitacoesProponente(){
            $arrMenuProponente['solicitacoes'] = [
                'id' => 'solicitacoes',
                'label' => 'Solicita&ccedil;&otilde;es',
                'title' => 'Ir para Solicitacoes',
                'menu' => [],
                'grupo' => []
            ];
            $arrMenuProponente['solicitacoes']['menu'][] = [
                'label' => 'Listar Solicita&ccedil;&otilde;es', 'title' => 'Ir para Listar Solicita&ccedil;&otilde;es',
                'url' => ['module' => 'solicitacao', 'controller' => 'mensagem', 'action' => 'index'],
                'grupo' => []
                ];
            return $arrMenuProponente;
        }
        public function usuarioProponente(){
            $arrMenuProponente['usuario'] = [
                'id' => 'usuario',
                'label' => 'Usu&aacute;rio',
                'title' => 'Ir para Usu&aacute;rio',
                'menu' => [],
                'grupo' => [],
            ];
            $arrMenuProponente['usuario']['menu'][] = [
                'label' => 'Alterar Senha',
                'title' => 'Ir para Alterar Senha',
                'url' => ['module' => 'autenticacao', 'controller' => 'index', 'action' => 'alterarsenha'],
                'grupo' => []
            ];
            $arrMenuProponente['usuario']['menu'][] = [
                'label' => 'Alterar Dados',
                'title' => 'Ir para Alterar Dados',
                'url' => ['module' => 'autenticacao', 'controller' => 'index', 'action' => 'alterardados'],
                'grupo' => []
            ];
            return $arrMenuProponente;
        }
        public function assinatura(){
            $arrMenu['assinatura'] = [
                'id' => 'assinatura',
                'label' => 'Assinatura',
                'title' => 'Ir para Assinatura',
                'menu' => [],
//    'grupo' => array(90,91,92,93,96,97,Autenticacao_Model_Grupos::COORDENADOR_ANALISE,104,110,114,115,Autenticacao_Model_Grupos::PRESIDENTE_CNIC,Autenticacao_Model_Grupos::COORDENADOR_CNIC,121,122,123,124,125,126,Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO,Autenticacao_Model_Grupos::TECNICO_PORTARIA,131,132,135,138,139,148,151),
            ];
            $arrMenu['assinatura']['menu'][] = [
//    'grupo' => array(114,130),
                'url' => ['module' => 'assinatura', 'controller' => 'index', 'action' => 'gerenciar-assinaturas'],
                'title' => 'Ir para Gerenciamento de assinaturas',
                'label' => 'Gerenciar Assinaturas'
            ];

            $arrMenu['assinatura']['menu'][] = [
                'url' => ['module' => 'assinatura', 'controller' => 'index', 'action' => 'visualizar-assinaturas'],
                'title' => 'Ir para Visualização de Assinaturas',
                'label' => 'Visualizar Assinaturas'
            ];

            $arrMenu['assinatura']['menu'][] = [
                'grupo' => [
                    Autenticacao_Model_Grupos::PARECERISTA,
                    Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER,
                    Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO,
                    Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA,
                    Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO,
                    Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO,
                    Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO,
                    Autenticacao_Model_Grupos::SECRETARIO,
                ],
                'url' => ['module' => 'readequacao', 'controller' => 'readequacao-assinatura', 'action' => 'gerenciar-assinaturas'],
                'title' => 'Ir para Readequa&ccedil;&atilde;o - Gerenciar Assinaturas',
                'label' => 'Gerenciar Assinaturas - Readequa&ccedil;&atilde;o'
            ];


            $arrMenu['assinatura']['menu'][] = [
                'grupo' => [
                    Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
                    Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
                    Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
                ],
                'url' => ['module' => 'assinatura', 'controller' => 'index', 'action' => 'gerenciar-assinaturas'] . "?idTipoDoAtoAdministrativo=" . \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_LAUDO_PRESTACAO_CONTAS,
                'title' => 'Ir para Readequa&ccedil;&atilde;o - Gerenciar Assinaturas',
                'label' => 'Gerenciar Assinaturas - Laudo Presta&ccedil;&atilde;o de Contas'
            ];

            $arrMenu['assinatura']['menu'][] = [
                'grupo' => [97],
                'url' => ['module' => 'assinatura', 'controller' => 'ato-administrativo', 'action' => 'gerir-atos-administrativos'],
                'title' => 'Ir para Gerir Atos Administrativos',
                'label' => 'Gerir Atos Administrativos'
            ];
            return $arrMenu;

        }
        public function atendimento(){
            $arrMenu['atendimento'] = [
                'id' => 'atendimento',
                'title' => 'Ir para Assinatura',
                'label' => 'Atendimento',
                'menu' => [],
                'grupo' => [Autenticacao_Model_Grupos::TECNICO_DE_ATENDIMENTO]
            ];

            $arrMenu['atendimento']['menu'][] = [
                'title' => 'Ir para Solicita&ccedil;&otilde;es',
                'label' => 'Solicita&ccedil;&otilde;es Proponente',
                'url' => ['module' => 'solicitacao', 'controller' => 'mensagem', 'action' => 'index'],
                'grupo' => [Autenticacao_Model_Grupos::TECNICO_DE_ATENDIMENTO]
            ];
            return $arrMenu;
        }
        public function acompanhamento(){
            $arrMenu['acompanhamento'] = [
                'title' => 'Ir para Acompanhamento',
                'label' => 'Acompanhamento',
                'id' => 'acompanhamento',
                'menu' => [],
                'grupo' => [94, 121, 122, 123, 124, 125, 126, 129, 134, 135, 137, 138, 139, 148, 151],
            ];

            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Ir para CheckList de Publica&ccedil;&atilde;o',
                'label' => 'CheckList para Publica&ccedil;&atilde;o',
                'url' => ['module'=> 'default','controller' => 'checklistpublicacao', 'action' => 'listas'],
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 110, 121, 122, 123, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO]
            ];

            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Ir para Movimenta&ccedil;&atilde;o Banc&aacute;ria',
                'label' => 'Movimenta&ccedil;&atilde;o Banc&aacute;ria',
                'grupo' => [272, 166, 121, 122, 123, 129, 148, 151], // restringe ao �rgao SACAV && CAP
                'url' => ['module'=> 'default','controller' => 'movimentacaodeconta', 'action' => 'resultado-extrato-de-conta-captacao'],
            ];

            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Ir para Certid&otilde;es Negativas',
                'label' => 'Certid&otilde;es Negativas',
                'url' => ['module' => 'default', 'controller' => 'manterregularidadeproponente', 'action' => 'index'],
                'grupo' => [108, 121, 122, 123, 124, 125, 134, 135, 138, 139]
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Analisar Relat&oacute;rios Trimestrais',
                'label' => 'Analisar Relat&oacute;rios Trimestrais',
                'url' => ['module'=> 'default','controller' => 'analisarexecucaofisica', 'action' => 'projetos'],
                'grupo' => [122, 123]
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Verificar Readequa&ccedil;&atilde;o de Projetos',
                'label' => 'Verificar Readequa&ccedil;&atilde;o de Projeto',
                'url' => ['module'=> 'default','controller' => 'verificarreadequacaodeprojeto', 'action' => 'verificarreadequacaodeprojetocoordparecerista'],
                'grupo' => [93]
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Enviar Pareceres para Pagamento',
                'label' => 'Enviar Pareceres para Pagamento',
                'url' => ['module'=> 'default','controller' => 'gerenciarparecer', 'action' => 'enviarpagamento'],
                'grupo' => [93]
            ];
            /*if ($this->grupoAtivo == 94) : ?>
            <li><a href="<?php echo $this->url(array('controller' => 'parecerista', 'action' => 'confirmacao-pagamento-parecerista'), '', true); ?>" title="Meus pagamentos">Meus Pagamentos</a></li>
            <li><a href="<?php echo $this->url(array('controller' => 'verificarreadequacaodeprojeto', 'action' => 'verificarreadequacaodeprojetoparecerista'), '', true); ?>" title="Verificar Readequacao de Projetos">Verificar Readequacao de Projeto</a></li>
            <?php endif;*/
            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Meus pagamentos',
                'label' => 'Meus Pagamentos',
                'url' => ['module'=> 'default','controller' => 'parecerista', 'action' => 'confirmacao-pagamento-parecerista'],
                'grupo' => [94],
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Analisar Relat&oacute;rios Trimestrais',
                'label' => 'Analisar Relat&oacute;rios Trimestrais',
                'grupo' => [121, 129],
                'url' => ['module'=> 'default','controller' => 'analisarexecucaofisicatecnico', 'action' => 'index'],
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Ir para Fiscalizar Projeto',
                'label' => 'Fiscalizar Projeto',
                'grupo' => [134, 135],
                'url' => ['module'=> 'default','controller' => 'pesquisarprojetofiscalizacao', 'action' => 'grid'],
            ];

            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Ir para Analisar Projeto Parecer',
                'label' => 'Analisar Comprova&ccedil;&atilde;o do Objeto',
                'grupo' => [138],
                'url' => ['module'=> 'default','controller' => 'avaliaracompanhamentoprojeto', 'action' => 'index'],
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Ir para Analisar Projeto Parecer',
                'label' => 'Analisar Comprova&ccedil;&atilde;o do Objeto',
                'grupo' => [139, 148, 151],
                'url' => ['module'=> 'default','controller' => 'avaliaracompanhamentoprojeto', 'action' => 'index-tecnico'],
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'title' => 'Ir para Pagamento de parecerista',
                'label' => 'Pagamento de Pareceristas',
                'grupo' => [137],
                'url' => ['module'=> 'default','controller' => 'parecerista', 'action' => 'configurar-pagamento-parecerista'],
            ];
            $arrMenu['acompanhamento']['menu'][] =[
                'grupo' => [137],
                'url' => ['module'=> 'default','controller' => 'gerenciarparecer', 'action' => 'enviarpagamento'],
                'title' => 'Ir para Gerar Memorando',
                'label' => 'Gerar memorando de pagamento'
            ];

            $arrMenu['acompanhamento']['menu'][] = [
                'grupo' => [121, 122, 123],
                'url' => ['module'=> 'default','controller' => 'gerartermodeaprovacao', 'action' => 'index'],
                'title' => 'Ir para Gerenciar Termo de Decis&atilde;o',
                'label' => 'Gerar Termo de Decis&atilde;o'
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'grupo' => [121],
                'url' => ['module'=> 'default','controller' => 'marcas', 'action' => 'index'],
                'title' => 'Processar Marcas',
                'label' => 'Processar Marcas'
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'grupo' => [121, 122],
                'url' => ['module'=> 'default','controller' => 'avaliarpedidoprorrogacao', 'action' => 'index'],
                'title' => 'Avaliar Pedido de Prorroga&ccedil;&atilde;o',
                'label' => 'Avaliar Pedido de Prorroga&ccedil;&atilde;o'
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'grupo' => [122, 123, 148, 151],
                'url' => ['module' => 'readequacao', 'controller' => 'readequacoes', 'action' => 'painel'],
                'title' => 'Readequa&ccedil;&otilde;es',
                'label' => 'Readequa&ccedil;&otilde;es'
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'grupo' => [93, 121],
                'url' => ['module' => 'readequacao', 'controller' => 'readequacoes', 'action' => 'painel-readequacoes'],
                'title' => 'Avaliar Readequa&ccedil;&otilde;es',
                'label' => 'Avaliar Readequa&ccedil;&otilde;es'
            ];
            $arrMenu['acompanhamento']['menu'][] = [
                'url' => ['module' => 'admissibilidade', 'controller' => 'enquadramento-assinatura', 'action' => 'gerenciar-assinaturas'],
                'title' => 'Assinatura',
                'label' => 'Assinatura',
                'grupo' => [
                147
                , 148
                , 149
                , 150
                , 151
                , 152
            ]
                ];
            return $arrMenu;
        }
        public function protocolo(){
            $arrMenu['protocolo'] = [
                'id' => 'protocolo',
                'title' => 'Ir para Protocolo',
                'label' => 'Protocolo',
                'menu' => [],
                'grupo' => [90, 91, 97, 104, 109, 115],
            ];
            $arrMenu['protocolo']['menu'][] = [
                'grupo' => [91, 97, 104, 109, 115],
                'title' => 'Ir para Tramitar Projetos',
                'url' => ['module'=>'default','controller' => 'tramitarprojetos', 'action' => 'despacharprojetos'],
                'label' => 'Tramitar Projetos'
            ];
            $arrMenu['protocolo']['menu'][] = [
                'grupo' => [90, 91, 97, 104, 109, 115],
                'title' => 'Ir para Tramitar Documentos',
                'url' => ['module'=>'default','controller' => 'tramitardocumentos', 'action' => 'index'],
                'label' => 'Tramitar Documentos'
            ];
            return $arrMenu;
        }
        public function admissibilidade(){
            $arrMenu['admissibilidade'] = [
                'id' => 'admissibilidade',
                'label' => 'Admissibilidade',
                'title' => 'Ir para Admissibilidade',
                'menu' => [],
                'grupo' => [
                    92
                    , Autenticacao_Model_Grupos::GESTOR_SALIC
                    , Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE
                    , 147
                    , 148
                    , 149
                    , 150
                    , 151
                    , 152
                    , Autenticacao_Model_Grupos::COMPONENTE_COMISSAO
                    , Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE
                ],
            ];
            $arrMenu['admissibilidade']['menu'][] = [
                'title' => 'Avalia&ccedil;&atilde;o',
                'url' => ['module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'listar-propostas'],
                'label' => 'Avalia&ccedil;&atilde;o',
                'grupo' => [
                Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE,
                Autenticacao_Model_Grupos::GESTOR_SALIC,
                Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE,
                Autenticacao_Model_Grupos::COMPONENTE_COMISSAO,
                Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE,
            ]
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'grupo' => [92, 97, 131],
                'url' => ['module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'listar-solicitacoes-desarquivamento'],
                'title' => 'Desarquivamento',
                'label' => 'Desarquivamento'
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'title' => 'Ir para Certid&otilde;es Negativas',
                'url' => ['module' => 'default', 'controller' => 'manterregularidadeproponente', 'action' => 'index'],
                'label' => 'Certid&otilde;es Negativas',
                'grupo' => [
                Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE,
                Autenticacao_Model_Grupos::GESTOR_SALIC,
                Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE
            ]
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'url' => ['module' => 'admissibilidade', 'controller' => 'enquadramento', 'action' => 'gerenciar-enquadramento'],
                'title' => 'Ir para Enquadramento',
                'label' => 'Enquadramento',
                'grupo' => [92, 131]
            ];
            $arrMenu['admissibilidade']['menu'][] = [
                'grupo' => [92, 97, 131],
                'url' => ['module'=>'default','controller' => 'recursos', 'action' => 'recurso-enquadramento'],
                'title' => 'Ir para Recurso',
                'label' => 'Recurso'
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'grupo' => [92, 131, 147, 149],
                'url' => ['module' => 'admissibilidade', 'controller' => 'enquadramento', 'action' => 'encaminhar-assinatura'],
                'title' => 'Ir para Encaminhar para assinatura',
                'label' => 'Encaminhar para assinatura'
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'grupo' => [92, 131],
                'url' => ['module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'desarquivarpropostas'],
                'title' => 'Desarquivar Proposta',
                'label' => 'Desarquivar Proposta'
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'title' => 'Ir para Alterar Unidade da an&aacute;lise da Proposta',
                'url' => ['module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'alterarunianalisepropostaconsulta'],
                'label' => 'Alterar Uni. da an&aacute;lise da Proposta',
                'grupo' => [
                Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE,
                Autenticacao_Model_Grupos::GESTOR_SALIC,
                Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE,
                Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE,
                    ]
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'grupo' => [97, 131, 148, 151],
                'url' => ['module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'redistribuiranalise'],
                'title' => 'Redistribuir An&aacute;lise',
                'label' => 'Redistribuir An&aacute;lise'
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'grupo' => [97, 131, 148, 151],
                'url' => ['module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'redistribuiranalise'],
                'title' => 'Gerenciar Propostas',
                'label' => 'Gerenciar Propostas'
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'grupo' => [97, 131, 148, 151],
                'url' => ['module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'gerenciaranalistas'],
                'title' => 'Gerenciar Analistas',
                'label' => 'Gerenciar Analistas'
            ];

            $arrMenu['admissibilidade']['menu'][] = [
                'url' => ['module' => 'admissibilidade', 'controller' => 'enquadramento-assinatura', 'action' => 'gerenciar-assinaturas'],
                'title' => 'Assinatura',
                'label' => 'Assinatura',
                'grupo' => [
                147
                , 148
                , 149
                , 150
                , 151
                , 152
            ]
            ];
            return $arrMenu;
        }
        public function parecer(){
            $arrMenu['parecer'] = [
                'id' => 'parecer',
                'label' => 'Parecer',
                'title' => 'Ir para Pareceres',
                'menu' => [],
                'grupo' => [93, 137],
            ];
            $arrMenu['parecer']['menu'][] = [
                'label' => 'Gerar Relat&oacute;rios de Parecerista', 'title' => 'Gerar Relat&oacute;rios de Parecerista',
                'url' => ['module'=>'default','controller' => 'gerarrelatorioparecerista', 'action' => 'aguardandoparecer'],
                'grupo' => [93, 137]
            ];
            $arrMenu['parecer']['menu'][] = [
                'label' => 'Gerenciar Parecerista', 'title' => 'Gerenciar Parecerista',
                'url' => ['module'=>'default','controller' => 'gerenciarparecer', 'action' => 'enviarpagamento'],
                'grupo' => [137]
            ];
            $arrMenu['parecer']['menu'][] = [
                'label' => 'Listar Selecionados', 'title' => 'Listar Selecionados',
                'url' => ['module'=>'default','controller' => 'Listareditais', 'action' => 'listarselecionados'],
                'grupo' => [114]
            ];
            $arrMenu['parecer']['menu'][] = [
                'label' => 'Relat&oacute;rio', 'title' => 'Ir para Consultar Parecerista',
                'url' => ['module'=>'default','controller' => 'consultarpareceristas', 'action' => 'consultardadospareceristas'],
                'grupo' => [137]
            ];
            return $arrMenu;
        }
        public function edital(){
            $arrMenu['edital'] = [
                'id' => 'edital',
                'label' => 'Edital',
                'title' => 'Ir para Edital',
                'menu' => [],
                'grupo' => [114, 130],
            ];
            return $arrMenu;
        }
        public function relatorios(){
            $arrMenu['relatorios'] = [
                'id' => 'relatorios',
                'label' => 'Relat&oacute;rios',
                'title' => 'Ir para Relat&oacute;rios',
                'menu' => [],
                'grupo' => [90, 91, 92, 93, 96, 97, Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 104, 110, 114, 115, Autenticacao_Model_Grupos::PRESIDENTE_CNIC, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 121, 122, 123, 124, 125, 126, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, Autenticacao_Model_Grupos::TECNICO_PORTARIA, 131, 132, 135, 138, 139, 148, 151],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'gerarrelatorios', 'action' => 'index'],
                'title' => 'Ir para Edital',
                'label' => 'Edital',
                'grupo' => [92, 93, 97, Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 110, 114, 118, Autenticacao_Model_Grupos::PRESIDENTE_CNIC, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, 131, Autenticacao_Model_Grupos::MEMBROS_NATOS_CNIC],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'relatorio', 'action' => 'index'],
                'title' => 'Ir para Propostas',
                'label' => 'Propostas',
                'grupo' => [90, 91, 92, 93, 96, 97, Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 104, 110, 114, 115, Autenticacao_Model_Grupos::PRESIDENTE_CNIC, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 121, 122, 123, 124, 125, 126, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, Autenticacao_Model_Grupos::TECNICO_PORTARIA, 131, 132, 135, 138, 139, 148, 151],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'relatorio', 'action' => 'projeto'],
                'title' => 'Ir para Projetos',
                'label' => 'Projetos',
                'grupo' => [90, 91, 92, 93, 96, 97, Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 104, 110, 114, 115, Autenticacao_Model_Grupos::PRESIDENTE_CNIC, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 121, 122, 123, 124, 125, 126, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, Autenticacao_Model_Grupos::TECNICO_PORTARIA, 131, 132, 135, 138, 139, 148, 151],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'operacional', 'action' => 'index'],
                'title' => 'Ir para Operacional',
                'label' => 'Operacional',
                'grupo' => [90, 91, 92, 93, 96, 97, Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 104, 110, 114, 115, Autenticacao_Model_Grupos::PRESIDENTE_CNIC, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 121, 122, 123, 124, 125, 126, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, Autenticacao_Model_Grupos::TECNICO_PORTARIA, 131, 132, 135, 138, 139, 148, 151],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'relatorio', 'action' => 'gerencial'],
                'title' => 'Ir para Gerencial',
                'label' => 'Gerencial',
                'grupo' => [90, 91, 92, 93, 96, 97, Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 104, 110, 114, 115, Autenticacao_Model_Grupos::PRESIDENTE_CNIC, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 121, 122, 123, 124, 125, 126, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, Autenticacao_Model_Grupos::TECNICO_PORTARIA, 131, 132, 135, 138, 139, 148, 151],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'relatorio', 'action' => 'desembolso'],
                'title' => 'Ir para Consultar Pontos Culturais',
                'label' => 'Ponto de Cultura',
                'grupo' => [90, 91, 92, 93, 96, 97, Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 104, 110, 114, 115, Autenticacao_Model_Grupos::PRESIDENTE_CNIC, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 121, 122, 123, 124, 125, 126, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, Autenticacao_Model_Grupos::TECNICO_PORTARIA, 131, 132, 135, 138, 139, 148, 151],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'gerarrelatorioreuniao', 'action' => 'gerarrelatorioreuniao'],
                'title' => 'Ir para Gerenciar Relat&oacute;rio Reuni&atilde;o',
                'label' => 'Relat&oacute;rio de resultado CNIC',
                'grupo' => [90, 91, 92, 93, 96, 97, Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 104, 110, 114, 115, Autenticacao_Model_Grupos::PRESIDENTE_CNIC, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 121, 122, 123, 124, 125, 126, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, Autenticacao_Model_Grupos::TECNICO_PORTARIA, 131, 132, 135, 138, 139, 148, 151],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'gerarrelatorioparecerista', 'action' => 'geraldeanalise'],
                'title' => 'Ir para Relat&oacute;rio geral de an&aacute;lise',
                'label' => 'Relat&oacute;rio geral de an&aacute;lise',
                'grupo' => [90, 91, 92, 93, 96, 97, Autenticacao_Model_Grupos::COORDENADOR_ANALISE, 104, 110, 114, 115, Autenticacao_Model_Grupos::PRESIDENTE_CNIC, Autenticacao_Model_Grupos::COORDENADOR_CNIC, 121, 122, 123, 124, 125, 126, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, Autenticacao_Model_Grupos::TECNICO_PORTARIA, 131, 132, 135, 138, 139, 148, 151],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'consultarpareceristas', 'action' => 'consultardadospareceristas'],
                'title' => 'Consultar Dados do Parecerista',
                'label' => 'Consultar Dados do Parecerista',
                'grupo' => [93, 97, Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, 137, 148, 151]
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'gerarrelatorioparecerista', 'action' => 'geraldeanalise'],
                'title' => 'Gerar Relat&oacute;rio Parecerista',
                'label' => 'Gerar Relat&oacute;rio Parecerista',
                'grupo' => [Autenticacao_Model_Grupos::COORDENADOR_ATENDIMENTO, 137, 148, 151]
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'edital', 'action' => 'index'],
                'title' => 'Criar Edital',
                'label' => 'Criar Edital',
                'grupo' => [114],
            ];
            $arrMenu['relatorios']['menu'][] = [
                'url' => ['module'=>'default','controller' => 'Listareditais', 'action' => 'listarselecionados'],
                'title' => 'Ir para Listar Selecionados',
                'label' => 'Listar Selecionados',
                'grupo' => [114, 130]
            ];
            return $arrMenu;
        }
        public function segurança(){
            $arrMenu['seguranca'] = [
                'id' => 'seguranca',
                'label' => 'Seguran&ccedil;a',
                'title' => 'Seguran&ccedil;a',
                'menu' => [],
                'grupo' => [],
            ];
            $arrMenu['seguranca']['menu'][] = [
                'url' => ['module' => 'default', 'controller' => 'manterusuario', 'action' => 'gerarsenha'],
                'title' => 'Ir para Regerar Senha',
                'label' => 'Regerar Senha Proponente',
                'grupo' => [97],
            ];
            $arrMenu['seguranca']['menu'][] = [
                'grupo' => [97],
                'url' => ['module'=> 'default', 'controller' => 'manterusuario', 'action' => 'regerarsenha'],
                'title' => 'Ir para Regerar Senha Usu&aacute;rio',
                'label' => 'Regerar Senha Usu&aacute;rio'
            ];
            $arrMenu['seguranca']['menu'][] = [
                'url' => ['module' => 'default', 'controller' => 'manterusuario', 'action' => 'cadastrarusuarioexterno'],
                'title' => 'Ir para Cadastrar Usu&aacute;rio Externo',
                'label' => 'Cadastrar Usu&aacute;rio Externo',
                'grupo' => [97],
            ];
            $arrMenu['seguranca']['menu'][] = [
                'url' => ['module' => 'default', 'controller' => 'manterusuario', 'action' => 'permissoessalic']. '?session=x&pag=1',
                'title' => 'Ir para Permiss&otilde;es do SalicWeb',
                'label' => 'Permiss&otilde;es do SalicWeb',
                'grupo' => [97],
            ];
            $arrMenu['seguranca']['menu'][] = [
                'url' => ['module' => 'autenticacao', 'controller' => 'index', 'action' => 'alterarsenhausuario'],
                'title' => 'Ir para Alterar Senha',
                'label' => 'Alterar Senha',
                'grupo' => []
            ];
            return $arrMenu;
        }
        public function manuais(){
            $arrMenu['manuais'] = [
                'id' => 'manuais',
                'label' => 'Manuais',
                'title' => 'Ir para Manuais',
                'menu' => [],
                'grupo' => [94, 137],
            ];
            $arrMenu['manuais']['menu'][] = [
                'label' => 'Pagamento Parecerista', 'title' => 'Ir para Pagamento Parecerista',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'manuais'] . '/Manual_Coordenador_Pronac-Pagamento-Parecerista.m4v',
                'grupo' => [137],
                'target' => '_blank'

            ];
            $arrMenu['manuais']['menu'][] = [
                'label' => 'Parecerista', 'title' => 'Ir para Parecerista',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'manuais'] . '/Parecerista.m4v',
                'grupo' => [94],
                'target' => '_blank'
            ];
            $arrMenu['manuais']['menu'][] = [
                'label' => 'Confirmar Pagamento Parecerista', 'title' => 'Ir para Parecerista - An&aacute;lise',
                'url' => ['module' =>'default','controller' => 'public', 'action' => 'manuais'] . '/Manual_do_Parecerista-Confirmar-Pagamento.m4v',
                'grupo' => [94],
                'target' => '_blank'
            ];
            return $arrMenu;
        }
        public function aulas(){
            $arrMenu['aulas'] = [
                'id' => 'aulas',
                'label' => 'Introdu&ccedil;&atilde;o a an&aacute;lise de projetos',
                'title' => 'Ir para Aulas',
                'menu' => [],
                'grupo' => [94, 137],
            ];
            $arrMenu['aulas']['menu'][] = [
                'label' => '1. Introdu&ccedil;&atilde;o', 'title' => 'Ir para Aula 1',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'videos'] . '/introducao-a-analise-de-projetos/aula1.m4v',
                'grupo' => [94, 137],
                'target' => '_blank'
            ];
            $arrMenu['aulas']['menu'][] = [
                'label' => '2. Normativos', 'title' => 'Ir para Aula 2',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'videos'] . '/introducao-a-analise-de-projetos/aula2.m4v',
                'grupo' => [94, 137],
                'target' => '_blank'
            ];

            $arrMenu['aulas']['menu'][] = [
                'label' => '3. D&uacute;vidas', 'title' => 'Ir para Aula 3',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'videos'] . '/introducao-a-analise-de-projetos/aula3.m4v',
                'grupo' => [94, 137],
                'target' => '_blank'
            ];
            $arrMenu['aulas']['menu'][] = [
                'label' => '4. Abordagem do Projeto', 'title' => 'Ir para Aula 4',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'videos'] . '/introducao-a-analise-de-projetos/aula4.m4v',
                'grupo' => [94, 137],
                'target' => '_blank'
            ];
            $arrMenu['aulas']['menu'][] = [
                'label' => '5. Itens Or&ccedil;ament&aacute;rios', 'title' => 'Ir para Aula 5',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'videos'] . '/introducao-a-analise-de-projetos/aula5.m4v',
                'grupo' => [94, 137],
                'target' => '_blank'
            ];
            $arrMenu['aulas']['menu'][] = [
                'label' => '6. Intera&ccedil;&otilde;es', 'title' => 'Ir para Aula 6',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'videos'] . '/introducao-a-analise-de-projetos/aula6.m4v',
                'grupo' => [94, 137],
                'target' => '_blank'
            ];
            $arrMenu['aulas']['menu'][] = [
                'label' => '7. Como Elaborar Parecer', 'title' => 'Ir para Aula 7',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'videos'] . '/introducao-a-analise-de-projetos/aula7.m4v',
                'grupo' => [94, 137],
                'target' => '_blank'
            ];
            $arrMenu['aulas']['menu'][] = [
                'label' => '8. Considera&ccedil;&otilde;es', 'title' => 'Ir para Aula 8',
                'url' => ['module'=>'default','controller' => 'public', 'action' => 'videos'] . '/introducao-a-analise-de-projetos/aula8.m4v',
                'grupo' => [94, 137],
                'target' => '_blank'
            ];
            return $arrMenu;

        }
        public function manutencao(){
            $arrMenu['manutencao'] = [
                'id' => 'manutencao',
                'title' => 'Ir para Manuten&ccedil;&atilde;o',
                'label' => 'Manuten&ccedil;&atilde;o',
                'grupo' => [148,151,92,93,97,103,Autenticacao_Model_Grupos::PROTOCOLO_ENVIO_RECEBIMENTO,110,113,114,115,121,122,123,124,125,126,127,128,131,132,134,135,136,137,138,139,140,143],
                'menu' => []
            ];
            $arrMenu['manutencao']['menu'][] = [
                'title' => 'Ir para Alterar Projeto',
                'label' => 'Alterar Projeto',
                'url' => ['module'=>'default', 'controller' => 'alterarprojeto', 'action' => 'consultarprojeto'],
                'grupo' => [148,151,92,93,97,103,Autenticacao_Model_Grupos::PROTOCOLO_ENVIO_RECEBIMENTO,110,113,114,115,121,122,123,124,125,126,127,128,131,132,134,135,136,137,138,139,140,143],
            ];
            $arrMenu['manutencao']['menu'][] = [
                'title' => 'Anexar Documentos',
                'label' => 'Anexar Documentos',
                'url' => ['module'=>'defalut', 'controller' => 'anexardocumentosminc', 'action' => 'index'],
                'grupo' =>[148,151,92,93,97,103,Autenticacao_Model_Grupos::PROTOCOLO_ENVIO_RECEBIMENTO,110,113,114,115,121,122,123,124,125,126,127,128,131,132,134,135,136,137,138,139,140,143],
            ];
            $arrMenu['manutencao']['menu'][] = [
                'title' => 'Excluir Documentos',
                'label' => 'Excluir Documentos',
                'url' => ['module'=>'default','controller' => 'anexardocumentosminc', 'action' => 'excluir'],
                'grupo' => [92,93,97,103,Autenticacao_Model_Grupos::PROTOCOLO_ENVIO_RECEBIMENTO,110,113,114,115,121,122,123,124,125,126,127,128,131,132,134,135,136,137,138,139,140,143],
            ];
            $arrMenu['manutencao']['menu'][] = [
                'grupo' => [148,151,92,93,97,103,Autenticacao_Model_Grupos::PROTOCOLO_ENVIO_RECEBIMENTO,110,113,114,115,121,122,123,124,125,126,127,128,131,132,134,135,136,137,138,139,140,143],
                'url' => ['module'=>'default','controller' => 'rastrearagente', 'action' => 'index'],
                'title' => 'Ir para Rastrear Agente',
                'label' => 'Rastrear Agente'
            ];
            $arrMenu['manutencao']['menu'][] = [
                'title' => 'Localiza&ccedil;&atilde;o F&iacute;sica do Projeto',
                'label' => 'Localiza&ccedil;&atilde;o F&iacute;sica do Projeto',
                'url' => ['module'=>'default','controller' => 'localizacao-fisica', 'action' => 'index'],
                'grupo' => [148,151,92,93,97,103,Autenticacao_Model_Grupos::PROTOCOLO_ENVIO_RECEBIMENTO,110,113,114,115,121,122,123,124,125,126,127,128,131,132,134,135,136,137,138,139,140,143],
            ];

            return $arrMenu;
        }
        public function filtro( $arrMenu,$menuProponente){
//            $arrMenuProponente = $arrMenu;
            $auth = (array)$this->auth->getIdentity();
//            var_dump($this->grupoAtivo);die;
            if (isset ($auth['cpf'])) {
//                $arrMenu = $arrMenuProponente;
            } elseif (isset($auth['usu_codigo'])) {
                $arrMenu = array_filter($arrMenu, function ($arrMenu) {
//                    return (empty($arrMenu['grupo']) || in_array($this->grupoAtivo, $arrMenu['grupo']));
                    return (empty($arrMenu['grupo']) || in_array($this->codGrupo, $arrMenu['grupo']));
                });

                $arrMenu = array_map(function ($arrMenu) {
                    $arrMenu['menu'] = array_filter($arrMenu['menu'], function ($arrMenu2) {
//                        return (empty($arrMenu2['grupo']) || in_array($this->grupoAtivo, $arrMenu2['grupo']));
                        return (empty($arrMenu2['grupo']) || in_array($this->codGrupo, $arrMenu2['grupo']));
                    });
                    return $arrMenu;
                }, $arrMenu);
            } else if (!isset($auth['usu_codigo']) && !isset($auth['cpf'])) {
                $arrMenu = [];
            }
          return $arrMenu;


        }











        public function getAction()
        {
            $parametros = $this->getRequest()->getParams();
            $barService = new BarService($this->getRequest(), $this->getResponse());
            $resposta = $barService->buscar($parametros['id']);

            $this->view->assign('data', $resposta);

            $this->getResponse()->setHttpResponseCode(200);
        }

        public function headAction()
        {
            $this->getResponse()->setHttpResponseCode(200);
        }

        public function postAction()
        {
            $barService = new BarService($this->getRequest(), $this->getResponse());
            $resposta = $barService->salvarRegistro();

            $this->view->assign('data', $resposta);
            $this->getResponse()->setHttpResponseCode(201);
        }

        public function putAction()
        {

            $this->view->assign('data', 'asda');
            $this->getResponse()->setHttpResponseCode(200);
        }

        public function deleteAction()
        {
            $this->getResponse()->setHttpResponseCode(204);
        }
    }


