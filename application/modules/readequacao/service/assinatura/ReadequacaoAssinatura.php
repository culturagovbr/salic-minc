<?php

namespace Application\Modules\Readequacao\Service\Assinatura;

use MinC\Servico\IServico;

class ReadequacaoAssinatura implements IServico
{
    private $grupoAtivo;
    private $auth;
    private $idTipoDoAto;

    private $idTiposAtoAdministrativos = [
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_PLANILHA_ORCAMENTARIA,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RAZAO_SOCIAL => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_ALTERACAO_RAZAO_SOCIAL,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_AGENCIA_BANCARIA => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_AGENCIA_BANCARIA,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SINOPSE_OBRA => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_SINOPSE_OBRA,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_IMPACTO_AMBIENTAL => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_IMPACTO_AMBIENTAL,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESPECIFICACAO_TECNICA => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_ESPECIFICACAO_TECNICA,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESTRATEGIA_EXECUCAO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_ESTRATEGIA_EXECUCAO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_LOCAL_REALIZACAO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_LOCAL_REALIZACAO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ALTERACAO_PROPONENTE => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_ALTERACAO_PROPONENTE,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_PLANO_DISTRIBUICAO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_NOME_PROJETO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_NOME_PROJETO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PERIODO_EXECUCAO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_PERIODO_EXECUCAO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DIVULGACAO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_PLANO_DIVULGACAO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RESUMO_PROJETO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_RESUMO_PROJETO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_OBJETIVOS => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_OBJETIVOS,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_JUSTIFICATIVA => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_JUSTIFICATIVA,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ACESSIBILIDADE => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_ACESSIBILIDADE,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_DEMOCRATIZACAO_ACESSO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_DEMOCRATIZACAO_ACESSO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ETAPAS_TRABALHO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_ETAPAS_TRABALHO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_FICHA_TECNICA => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_FICHA_TECNICA,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_SALDO_APLICACAO,
        \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS => \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_READEQUACAO_TRANSFERENCIA_RECURSOS
    ];
    
    public function __construct(
        $grupoAtivo,
        $auth
    ) {
        $this->grupoAtivo = $grupoAtivo;
        $this->auth = $auth;
    }    
    
    public function obterAssinaturas()
    {
        $tbReadequacaoDbTable = new \Readequacao_Model_DbTable_TbReadequacao();
        $tbAtoAdministrativoDbTable = new \Assinatura_Model_DbTable_TbAtoAdministrativo();
        $tbDocumentoAssinaturaDbTable = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();        
        $tbAssinaturaDbTable = new \Assinatura_Model_DbTable_TbAssinatura();
        
        $projetos = $tbReadequacaoDbTable->obterPainelDeDocumentosDeReadequacaoAguardandoAssinatura(
            $this->grupoAtivo->codOrgao,
            $this->grupoAtivo->codGrupo
        );

        $arrProjetos = (count($projetos) == 0) ? $projetos : [];
        
        foreach ($projetos as $projeto) {
            $tbAssinaturaDbTable->preencherModeloAssinatura([
                'idDocumentoAssinatura' => $projeto['idDocumentoAssinatura']
            ]);
            $qtPessoasQueFaltamAssinar = $this->qtPessoasQueFaltamAssinar(
                $projeto['idDocumentoAssinatura'],
                $projeto['idTipoDoAtoAdministrativo'],
                $projeto['idOrgaoSuperiorDoAssinante']
            );           
            $projeto['QtdeDePessoasQueFaltamAssinar'] = $qtPessoasQueFaltamAssinar;
            
            $qtAssinaram = $tbAssinaturaDbTable->obterQuantidadeAssinaturasRealizadas();
            $qtAssinaram = (is_null($qtAssinaram)) ? 0 : $qtAssinaram;
            $projeto['QtdeDePessoasQueAssinaramDocumento'] = $qtAssinaram;
            
            $qtAssinaturasPorAto = $tbAtoAdministrativoDbTable->obterQuantidadeMinimaAssinaturas(
                $projeto['idTipoDoAtoAdministrativo'],
                $projeto['idOrgaoSuperiorDoAssinante']
            );
            $qtAssinaturasPorAto = (is_null($qtAssinaturasPorAto)) ? 0 : $qtAssinaturasPorAto;
            $projeto['QtdeAssinaturasPorAtoAdministrativo'] = $qtAssinaturasPorAto;

            if ($qtPessoasQueFaltamAssinar > 0) {
                $ordemDaProximaAssinatura = $tbDocumentoAssinaturaDbTable->obterProximaAssinatura(
                    $projeto['idDocumentoAssinatura'],
                    $projeto['IdPRONAC']
                );
            }
            $projeto['ordemDaProximaAssinatura'] = $ordemDaProximaAssinatura;
            
            $arrProjetos[] = $projeto;
        }
        
        return $arrProjetos;
    }
    
    public function obterAtoAdministrativoPorTipoReadequacao($idTipoReadequacao)
    {
        if (array_key_exists($idTipoReadequacao, $this->idTiposAtoAdministrativos)) {
            return $this->idTiposAtoAdministrativos[$idTipoReadequacao];
        }
    }

    public function obterAtosAdministativos()
    {
        return $this->idTiposAtoAdministrativos;
    }

    public function qtPessoasQueFaltamAssinar(
        $idDocumentoAssinatura,
        $idTipoDoAto,
        $idOrgaoSuperiorDoAssinante
    ) {
        $tbAtoAdministrativoDbTable = new \Assinatura_Model_DbTable_TbAtoAdministrativo();
        $qtAssinaturas = $tbAtoAdministrativoDbTable->obterQuantidadeMinimaAssinaturas(
            array_values($this->idTiposAtoAdministrativos),
            $this->grupoAtivo->codOrgao
        );
        $qtAssinaturas = (is_null($qtAssinaturas)) ? 0 : $qtAssinaturas;

        $tbAssinaturaDbTable = new \Assinatura_Model_DbTable_TbAssinatura();
        $tbAssinaturaDbTable->preencherModeloAssinatura([
            'idDocumentoAssinatura' => $idDocumentoAssinatura
        ]);
        
        $qtAssinaram = $tbAssinaturaDbTable->obterQuantidadeAssinaturasRealizadas();
        $qtAssinaram = (is_null($qtAssinaram)) ? 0 : $qtAssinaram;

        
        $qtFaltamAssinar = $qtAssinaturas - $qtAssinaram;

        return $qtFaltamAssinar;
    }

    public function encaminharOuFinalizarReadequacaoChecklist($idReadequacao)
    {
        try {
            $auth = \Zend_Auth::getInstance();
            $idUsuarioLogado = $this->auth->getIdentity()->usu_codigo;
            $reuniao = new \Reuniao();
            $raberta = $reuniao->buscarReuniaoAberta();
            $idNrReuniao = ($raberta['stPlenaria'] == 'A') ? $raberta['idNrReuniao'] + 1 : $raberta['idNrReuniao'];

            $tbReadequacaoXParecer = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
            $dadosParecer = $tbReadequacaoXParecer->buscar([
                'idReadequacao=?' => $idReadequacao
            ]);
            foreach ($dadosParecer as $key => $dp) {
                $pareceres = [];
                $pareceres[$key] = $dp->idParecer;
            }

            $Parecer = new \Parecer_Model_DbTable_Parecer();
            $parecerTecnico = $Parecer->buscar(
                ['IdParecer = (?)' => $pareceres],
                ['IdParecer']
            )->current();

            $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
            $read = $tbReadequacao->buscarReadequacao([
                'idReadequacao =?' => $idReadequacao
            ])->current();

            if ($parecerTecnico->ParecerFavoravel == 2) {
                switch ($read->idTipoReadequacao) {
                    case  \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO:
                        $tbPlanoDistribuicaoMapper = new \Readequacao_Model_TbPlanoDistribuicaoMapper();
                        $tbPlanoDistribuicaoMapper->finalizarAnaliseReadequacaoPlanoDistribuicao($read->idPronac, $idReadequacao, $parecerTecnico->ParecerFavoravel);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA:
                        $TipoDeReadequacao = $this->finalizarReadequacaoPlanilhaOrcamentaria($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RAZAO_SOCIAL:
                        $this->finalizarReadequacaoRazaoSocial($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_AGENCIA_BANCARIA:
                        $this->finalizarReadequacaoAgenciaBancaria($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SINOPSE_OBRA:
                        $this->finalizarReadequacaoSinopseObra($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_IMPACTO_AMBIENTAL:
                        $this->finalizarReadequacaoImpactoAmbiental($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESPECIFICACAO_TECNICA:
                        $this->finalizarReadequacaoEspecificacaoTecnica($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESTRATEGIA_EXECUCAO:
                        $this->finalizarReadequacaoEstrategiaExecucao($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_LOCAL_REALIZACAO:
                        $this->finalizarReadequacaoLocalRealizacao($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ALTERACAO_PROPONENTE:
                        $this->finalizarReadequacaoAlteracaoProponente($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_NOME_PROJETO:
                        $this->finalizarReadequacaoNomeProjeto($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PERIODO_EXECUCAO:
                        $this->finalizarReadequacaoPeriodoExecucao($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DIVULGACAO:
                        $this->finalizarReadequacaoPlanoDivulgacao($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RESUMO_PROJETO:
                        $this->finalizarReadequacaoResumoProjeto($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_OBJETIVOS:
                        $this->finalizarReadequacaoObjetivos($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_JUSTIFICATIVA:
                        $this->finalizarReadequacaoJustificativa($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ACESSIBILIDADE:
                        $this->finalizarReadequacaoAcessibilidade($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_DEMOCRATIZACAO_ACESSO:
                        $this->finalizarReadequacaoDemocratizacaoAcesso($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ETAPAS_TRABALHO:
                        $this->finalizarReadequacaoEtapasTrabalho($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_FICHA_TECNICA:
                        $this->finalizarReadequacaoFichaTecnica($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS:
                        $this->finalizarReadequacaoTransferenciaRecursos($read);
                        break;
                    case \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO:
                        $this->finalizarReadequacaoSaldoAplicacao($read, $parecerTecnico);
                        break;
                }
            }
            
            //Atualiza a tabela Readequacao_Model_DbTable_TbReadequacao
            $dados = [];
            $dados['siEncaminhamento'] = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_FINALIZADA_SEM_PORTARIA;
            $dados['stEstado'] = \Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_FINALIZADO;

            $tiposParaChecklist = [
                \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA,
                \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RAZAO_SOCIAL,
                \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ALTERACAO_PROPONENTE,
                \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_NOME_PROJETO,
                \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RESUMO_PROJETO
            ];
            
            if (in_array($read->idTipoReadequacao, $tiposParaChecklist)) {
                if ($read->idTipoReadequacao != \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA && $TipoDeReadequacao[0]['TipoDeReadequacao'] != 'RM') {
                    if ($parecerTecnico->ParecerFavoravel !== '1') { // desfavoravel
                        $dados['stEstado'] = \Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_EM_ANDAMENTO;
                        $dados['siEncaminhamento'] = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CHECKLIST_PUBLICACAO;
                    }
                }
            }

            // atualiza registro de tbReadequacao
            $dados['idNrReuniao'] = $idNrReuniao;
            $where = [];
            $where["idReadequacao = ?"] = $idReadequacao;

            $retorno = true;
            $atualizacaoReadequacao = $tbReadequacao->update($dados, $where);
            if (!$atualizacaoReadequacao) {
                $retorno = false;
            }

            if ($read->idTipoReadequacao == \Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA && $TipoDeReadequacao[0]['TipoDeReadequacao'] == 'RM') {
                // remanejamento: chama sp para trocar planilha ativa (desativa atual e ativa remanejada)
                $spAtivarPlanilhaOrcamentaria = new \spAtivarPlanilhaOrcamentaria();
                $ativarPlanilhaOrcamentaria = $spAtivarPlanilhaOrcamentaria->exec($read->idPronac);
            }

            //Atualiza a tabela tbDistribuirReadequacao
            $dados = [];
            $dados['stValidacaoCoordenador'] = 1;
            $dados['DtValidacaoCoordenador'] = new \Zend_Db_Expr('GETDATE()');
            $dados['idCoordenador'] = $idUsuarioLogado;
            $where = "idReadequacao = $idReadequacao";
            $tbDistribuirReadequacao = new \Readequacao_Model_tbDistribuirReadequacao();
            $atualizacaoDistribuicaoReadequacao = $tbDistribuirReadequacao->update($dados, $where);
            if (!$atualizacaoDistribuicaoReadequacao) {
                $retorno = false;
            }
            return $retorno;
        } catch (Exception $objExcetion) {
            xd($objExcetion->getMessage());
            throw $objExcetion;
        }
    } 
        
    protected function finalizarReadequacaoPlanilhaOrcamentaria($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();
        
        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
        $PlanilhaAtiva = $tbPlanilhaAprovacao->valorTotalPlanilhaAtiva($read->idPronac);
        
        //BUSCAR VALOR TOTAL DA PLANILHA DE READEQUADA
        $PlanilhaReadequada = $tbPlanilhaAprovacao->valorTotalPlanilhaReadequada(
            $read->idPronac,
            $read->idReadequacao
        );

        // chama SP que verifica o tipo do remanejamento
        $spTipoDeReadequacaoOrcamentaria = new \spTipoDeReadequacaoOrcamentaria();
        $TipoDeReadequacao = $spTipoDeReadequacaoOrcamentaria->exec($read->idPronac);

        // complementacao
        if ($TipoDeReadequacao[0]['TipoDeReadequacao'] == 'CO') {
            $TipoAprovacao = 2;
            $dadosPrj->Situacao = 'D28';
            $dadosPrj->ProvidenciaTomada = 'Aguardando portaria de complementação';
            $dadosPrj->Logon = $this->auth->getIdentity()->usu_codigo;
        } elseif ($TipoDeReadequacao[0]['TipoDeReadequacao'] == 'RE') {
            // reducao
            $TipoAprovacao = 4;
            $dadosPrj->Situacao = 'D29';
            $dadosPrj->ProvidenciaTomada = 'Aguardando portaria de redução';
            $dadosPrj->Logon = $this->auth->getIdentity()->usu_codigo;
        }

        // insere somente em reducao ou complementacao
        if ($TipoDeReadequacao[0]['TipoDeReadequacao'] == 'CO' || $TipoDeReadequacao[0]['TipoDeReadequacao'] == 'RE') {
            $dadosPrj->save();
            $tbAprovacao = new \Aprovacao();
            $dadosAprovacao = [
                'IdPRONAC' => $read->idPronac,
                'AnoProjeto' => $dadosPrj->AnoProjeto,
                'Sequencial' => $dadosPrj->Sequencial,
                'TipoAprovacao' => $TipoAprovacao,
                'DtAprovacao' => new \Zend_Db_Expr('GETDATE()'),
                'ResumoAprovacao' => 'Parecer favor&aacute;vel para readequa&ccedil;&atilde;o',
                'AprovadoReal' => $TipoDeReadequacao[0]['vlReadequado'], //Alterado pelo valor retornado pela Store
                'Logon' => $this->auth->getIdentity()->usu_codigo,
                'idReadequacao' => $read->idReadequacao
            ];

            $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);
        }

        return $TipoDeReadequacao;
    }

    protected function finalizarReadequacaoRazaoSocial($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();

        $tbAprovacao = new \Aprovacao();
        $dadosAprovacao = [
            'IdPRONAC' => $read->idPronac,
            'AnoProjeto' => $dadosPrj->AnoProjeto,
            'Sequencial' => $dadosPrj->Sequencial,
            'TipoAprovacao' => 8,
            'DtAprovacao' => new \Zend_Db_Expr('GETDATE()'),
            'ResumoAprovacao' => 'Parecer favor&aacute;vel para readequa&ccedil;&atilde;o',
            'Logon' => $this->auth->getIdentity()->usu_codigo,
            'idReadequacao' => $read->idReadequacao
        ];
        $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

    }
    
    protected function finalizarReadequacaoAgenciaBancaria($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();
        $agenciaBancaria = str_replace('-', '', $read->dsSolicitacao);
                        
        $tblContaBancaria = new \ContaBancaria();
        $arrayDadosBancarios = [
            'Agencia' => $agenciaBancaria,
            'ContaBloqueada' => '000000000000',
            'DtLoteRemessaCB' => null,
            'LoteRemessaCB' => '00000',
            'OcorrenciaCB' => '000',
            'ContaLivre' => '000000000000',
            'DtLoteRemessaCL' => null,
            'LoteRemessaCL' => '00000',
            'OcorrenciaCL' => '000',
            'Logon' => $this->auth->getIdentity()->usu_codigo,
            'idPronac' => $read->idPronac
        ];
        $whereDadosBancarios['AnoProjeto = ?'] = $dadosPrj->AnoProjeto;
        $whereDadosBancarios['Sequencial = ?'] = $dadosPrj->Sequencial;
        $tblContaBancaria->alterar($arrayDadosBancarios, $whereDadosBancarios);
    }

    protected function finalizarReadequacaoSinopseObra($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->Sinopse = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }

    protected function finalizarReadequacaoImpactoAmbiental($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->ImpactoAmbiental = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }

    protected function finalizarReadequacaoEspecificacaoTecnica($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar([
            'IdPRONAC=?' => $read->idPronac
        ])->current();
        
        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->EspecificacaoTecnica = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }

    protected function finalizarReadequacaoEstrategiaExecucao($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->EstrategiadeExecucao = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }

    protected function finalizarReadequacaoLocalRealizacao($read)
    {
        $Abrangencia = new \Proposta_Model_DbTable_Abrangencia();
        
        $tbAbrangencia = new \Readequacao_Model_DbTable_TbAbrangencia();
        $abrangencias = $tbAbrangencia->buscar([
            'idReadequacao=?' => $read->idReadequacao
        ]);
        foreach ($abrangencias as $abg) {
            $Projetos = new \Projetos();
            $dadosPrj = $Projetos->buscar([
                'IdPRONAC=?' => $read->idPronac
            ])->current();

            //Se não houve avalição do conselheiro, pega a avaliação técnica como referencia.
            $avaliacao = $abg->tpAnaliseComissao;
            if ($abg->tpAnaliseComissao == 'N') {
                $avaliacao = $abg->tpAnaliseTecnica;
            }

            //Se a avaliação foi deferida, realiza as mudanças necessárias na tabela original.
            if ($avaliacao == 'D') {
                if ($abg->tpSolicitacao == 'E') { //Se a abrangencia foi excluída, atualiza os status da abrangencia na SAC.dbo.Abrangencia
                    $Abrangencia->delete([
                        'idProjeto = ?' => $dadosPrj->idProjeto,
                        'idPais = ?' => $abg->idPais,
                        'idUF = ?' => $abg->idUF,
                        'idMunicipioIBGE = ?' => $abg->idMunicipioIBGE
                    ]);
                } elseif ($abg->tpSolicitacao == 'I') { //Se a abangência foi incluída, cria um novo registro na tabela SAC.dbo.Abrangencia
                    $novoLocalRead = [];
                    $novoLocalRead['idProjeto'] = $dadosPrj->idProjeto;
                    $novoLocalRead['idPais'] = $abg->idPais;
                    $novoLocalRead['idUF'] = $abg->idUF;
                    $novoLocalRead['idMunicipioIBGE'] = $abg->idMunicipioIBGE;
                    $novoLocalRead['Usuario'] = $this->auth->getIdentity()->usu_codigo;
                    $novoLocalRead['stAbrangencia'] = 1;
                    $Abrangencia->salvar($novoLocalRead);
                }
            }
        }
        
        $dadosAbr = [];
        $dadosAbr['stAtivo'] = 'N';
        $whereAbr = "idPronac = $read->idPronac AND idReadequacao = $read->idReadequacao";
        $tbAbrangencia->update($dadosAbr, $whereAbr);
    }

    protected function finalizarReadequacaoAlteracaoProponente($read)
    {

        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->find([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $tbAprovacao = new \Aprovacao();
        $dadosAprovacao = [
            'IdPRONAC' => $read->idPronac,
            'AnoProjeto' => $dadosPrj->AnoProjeto,
            'Sequencial' => $dadosPrj->Sequencial,
            'TipoAprovacao' => 8,
            'DtAprovacao' => new \Zend_Db_Expr('GETDATE()'),
            'ResumoAprovacao' => 'Parecer favor&aacute;vel para readequa&ccedil;&atilde;o',
            'Logon' => $this->auth->getIdentity()->usu_codigo,
            'idReadequacao' => $read->idReadequacao
        ];
        $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);
    }

    protected function finalizarReadequacaoNomeProjeto($read)
    {
                            
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->find([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $tbAprovacao = new \Aprovacao();
        $dadosAprovacao = [
            'IdPRONAC' => $read->idPronac,
            'AnoProjeto' => $dadosPrj->AnoProjeto,
            'Sequencial' => $dadosPrj->Sequencial,
            'TipoAprovacao' => 8,
            'DtAprovacao' => new \Zend_Db_Expr('GETDATE()'),
            'ResumoAprovacao' => 'Parecer favor&aacute;vel para readequa&ccedil;&atilde;o',
            'Logon' => $this->auth->getIdentity()->usu_codigo,
            'idReadequacao' => $read->idReadequacao
        ];
        $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);
    }

    protected function finalizarReadequacaoPeriodoExecucao($read)
    {             
        $dtFimExecucao = \Data::dataAmericana($read->dsSolicitacao);
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->find([
            'IdPRONAC=?' => $read->idPronac
        ])->current();
        $dadosPrj->DtFimExecucao = $dtFimExecucao;
        $dadosPrj->save();
    }
    
    protected function finalizarReadequacaoPlanoDivulgacao($read)
    {
        $PlanoDeDivulgacao = new \PlanoDeDivulgacao();
        $tbPlanoDivulgacao = new \tbPlanoDivulgacao();
        $planosDivulgacao = $tbPlanoDivulgacao->buscar([
            'idReadequacao=?' => $read->idReadequacao
        ]);

        foreach ($planosDivulgacao as $plano) {
            $Projetos = new \Projetos();
            $dadosPrj = $Projetos->buscar([
                'IdPRONAC=?' => $read->idPronac
            ])->current();

            //Se não houve avalição do conselheiro, pega a avaliação técnica como referencia.
            $avaliacao = $plano->tpAnaliseComissao;
            if ($plano->tpAnaliseComissao == 'N') {
                $avaliacao = $plano->tpAnaliseTecnica;
            }

            //Se a avaliação foi deferida, realiza as mudanças necessárias na tabela original.
            if ($avaliacao == 'D') {
                if ($plano->tpSolicitacao == 'E') { //Se o plano de divulgação foi excluído, atualiza os status do plano na SAC.dbo.PlanoDeDivulgacao
                    $PlanoDivulgacaoEmQuestao = $PlanoDeDivulgacao->buscar([
                        'idProjeto = ?' => $dadosPrj->idProjeto,
                        'idPeca = ?' => $plano->idPeca,
                        'idVeiculo = ?' => $plano->idVeiculo
                    ])->current();
                    $tbLogomarca = new \tbLogomarca();
                    $dadosLogomarcaDaDivulgacao = $tbLogomarca->buscar([
                        'idPlanoDivulgacao = ?' => $PlanoDivulgacaoEmQuestao->idPlanoDivulgacao
                    ])->current();
                    if (!empty($dadosLogomarcaDaDivulgacao)) {
                        $dadosLogomarcaDaDivulgacao->delete();
                    }
                    $PlanoDivulgacaoEmQuestao->delete();
                } elseif ($plano->tpSolicitacao == 'I') { //Se o plano de divulgação foi incluído, cria um novo registro na tabela SAC.dbo.PlanoDeDivulgacao
                    $novoPlanoDivRead = [];
                    $novoPlanoDivRead['idProjeto'] = $dadosPrj->idProjeto;
                    $novoPlanoDivRead['idPeca'] = $plano->idPeca;
                    $novoPlanoDivRead['idVeiculo'] = $plano->idVeiculo;
                    $novoPlanoDivRead['Usuario'] = $this->auth->getIdentity()->usu_codigo;
                    $novoPlanoDivRead['siPlanoDeDivulgacao'] = 0;
                    $novoPlanoDivRead['idDocumento'] = null;
                    $novoPlanoDivRead['stPlanoDivulgacao'] = 1;
                    $PlanoDeDivulgacao->inserir($novoPlanoDivRead);
                }
            }
        }

        $dadosPDD = [];
        $dadosPDD['stAtivo'] = 'N';
        $wherePDD = "idPronac = $read->idPronac AND idReadequacao = $read->idReadequacao";
        $tbPlanoDivulgacao->update($dadosPDD, $wherePDD);

    }

    protected function finalizarReadequacaoResumoProjeto($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->find([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $tbAprovacao = new \Aprovacao();
        $dadosAprovacao = [
            'IdPRONAC' => $read->idPronac,
            'AnoProjeto' => $dadosPrj->AnoProjeto,
            'Sequencial' => $dadosPrj->Sequencial,
            'TipoAprovacao' => 8,
            'DtAprovacao' => new \Zend_Db_Expr('GETDATE()'),
            'ResumoAprovacao' => 'Parecer favor&aacute;vel para readequa&ccedil;&atilde;o',
            'Logon' => $this->auth->getIdentity()->usu_codigo,
            'idReadequacao' => $read->idReadequacao
        ];
        $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

    }

    protected function finalizarReadequacaoObjetivos($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->Objetivos = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }

    protected function finalizarReadequacaoJustificativa($read)
    {
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->Justificativa = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }

    protected function finalizarReadequacaoAcessibilidade($read)
    {
                    
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->Acessibilidade = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }

    protected function finalizarReadequacaoDemocratizacaoAcesso($read)
    {
                    
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->DemocratizacaoDeAcesso = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }
    
    protected function finalizarReadequacaoEtapasTrabalho($read)
    {
                    
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->EtapaDeTrabalho = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }

    protected function finalizarReadequacaoFichaTecnica($read)
    {
                    
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->buscar([
            'IdPRONAC=?' => $read->idPronac
        ])->current();

        $PrePropojeto = new \Proposta_Model_DbTable_PreProjeto();
        $dadosPreProjeto = $PrePropojeto->find([
            'idPreProjeto=?' => $dadosPrj->idProjeto
        ])->current();
        $dadosPreProjeto->FichaTecnica = $read->dsSolicitacao;
        $dadosPreProjeto->save();
    }

    protected function finalizarReadequacaoTransferenciaRecursos($read)
    {
        $TbSolicitacaoTransferenciaRecursos = new \Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos();
        $tbProjetoRecebedorRecursoMapper = new \Readequacao_Model_TbProjetoRecebedorRecursoMapper();
        $tbSolicitacaoTransferenciaRecursosMapper = new \Readequacao_Model_TbSolicitacaoTransferenciaRecursosMapper();
        $projetos = new \Projetos();
        $projetosRecebedores = $TbSolicitacaoTransferenciaRecursos->obterProjetosRecebedores($read->idReadequacao);
        $projetoTransferidor = $projetos->buscarProjetoTransferidor($read->idPronac);

        foreach ($projetosRecebedores as $projetoRecebedor) {

            $arrData = [];
            $arrData['idSolicitacaoTransferenciaRecursos'] = $projetoRecebedor['idSolicitacao'];
            $arrData['idPronacTransferidor'] = $projetoTransferidor['idPronac'];
            $arrData['idPronacRecebedor'] = $projetoRecebedor['idPronacRecebedor'];
            $arrData['tpTransferencia'] = $projetoRecebedor['tpTransferencia'];
            $arrData['dtRecebimento'] = new \Zend_Db_Expr('GETDATE()');
            $arrData['vlRecebido'] = $projetoRecebedor['vlRecebido'];

            $statusProjetoRecebedorRecurso = $tbProjetoRecebedorRecursoMapper->finalizarSolicitacaoReadequacao($arrData);

            $arrData = [];
            $arrData['stEstado'] = \Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_FINALIZADO;
            $arrData['idSolicitacaoTransferenciaRecursos'] = $projetoRecebedor['idSolicitacao'];

            $data = new \Readequacao_Model_TbSolicitacaoTransferenciaRecursos($arrData);
            $statusSolicitacaoTransferenciaRecursos = $tbSolicitacaoTransferenciaRecursosMapper->save($data);

            if ($statusProjetoRecebedorRecurso == false
                || $statusSolicitacaoTransferenciaRecursos == false
            ) {
                throw new Exception("N&atilde;o foi poss&iacute;vel incluir os projetos recebedores da solicita&ccedil;&atilde;o");
            }
        }
    }

    protected function finalizarReadequacaoSaldoAplicacao($read, $parecerTecnico)
    {
                    
        $Projetos = new \Projetos();
        $dadosPrj = $Projetos->find([
            'IdPRONAC=?'=>$read->idPronac
        ])->current();

        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
        $planilhaReadequada = $tbPlanilhaAprovacao->valorTotalPlanilhaReadequada(
            $read->idPronac,
            $read->idReadequacao,
            [\Proposta_Model_Verificacao::INCENTIVO_FISCAL_FEDERAL]
        )->current();

        $where = array();
        $where['a.IdPRONAC = ?'] = $read->idPronac;
        $where['a.stAtivo = ?'] = 'S';
        $where['a.nrFonteRecurso = ?'] = \Proposta_Model_Verificacao::INCENTIVO_FISCAL_FEDERAL;
        $PlanilhaAtiva = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

        $valorDaReadequacao = $planilhaReadequada->Total - $PlanilhaAtiva->Total;

        $tbAprovacao = new \Aprovacao();
        $dadosAprovacao = [
            'IdPRONAC' => $read->idPronac,
            'AnoProjeto' => $dadosPrj->AnoProjeto,
            'Sequencial' => $dadosPrj->Sequencial,
            'TipoAprovacao' => \Aprovacao::TIPO_APROVACAO_COMPLEMENTACAO,
            'DtAprovacao' => new \Zend_Db_Expr('GETDATE()'),
            'AprovadoReal' => $valorDaReadequacao,
            'ResumoAprovacao' => $parecerTecnico->ResumoParecer,
            'idParecer' => $parecerTecnico->IdParecer,
            'Logon' => $this->auth->getIdentity()->usu_codigo,
            'idReadequacao' => $read->idReadequacao
        ];
        $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

        $tbPlanilhaAprovacao = new \tbPlanilhaAprovacao();
        $dadosReadequacaoAnterior = ['stAtivo' => 'N'];
        $whereReadequacaoAnterior = [
            'IdPRONAC = ?' => $read->idPronac,
            'stAtivo = ?' => 'S'
        ];
        $update = $tbPlanilhaAprovacao->update($dadosReadequacaoAnterior, $whereReadequacaoAnterior);

        $dadosReadequacaoNova = ['stAtivo' => 'S'];
        $whereReadequacaoNova = [
            'IdPRONAC = ?' => $read->idPronac,
            'stAtivo = ?' => 'N',
            'idReadequacao=?' => $read->idReadequacao
        ];
        $tbPlanilhaAprovacao->update($dadosReadequacaoNova, $whereReadequacaoNova);
    }
}
