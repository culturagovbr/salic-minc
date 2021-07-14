<?php
/**
 * ReadequacoesControllerTest
 *
 * @package
 * @author
 */
class ReadequacoesControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $this->idPronac = $this->getProjetoAptoReadequacao();

        $projetos = new Projetos();
        $projeto = $projetos->buscar(
            array(
                'IdPRONAC = ?' => $this->idPronac
            )
        )->current();
        
        $this->pronac = $projeto->AnoProjeto . $projeto->Sequencial;
        
        $this->hashPronac = Seguranca::encrypt($this->idPronac);
        
        $this->autenticar();
        
        $this->resetRequest()
            ->resetResponse();
        
        $this->perfilParaProponente();
        
        $this->resetRequest()
            ->resetResponse();
        
    }

    private function getProjetoAptoReadequacao()
    {
        $projetos = new Projetos();

        $select = $projetos->select();
        
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => 'projetos'),
            'p.IdPRONAC AS idPronac'
        );

        $select->joinLeft(
            array('l' => 'liberacao'),
            'l.AnoProjeto = p.AnoProjeto AND l.Sequencial = p.Sequencial',
            array(''),
            'sac.dbo'
        );
        $select->where(new Zend_Db_Expr('p.DtInicioExecucao < GETDATE()'));
        $select->where(new Zend_Db_Expr('p.DtFimExecucao > GETDATE()'));
        $select->where('p.AnoProjeto > ?', 16);
        $select->where('p.AnoProjeto < ?', 90);
        $select->limit(30);
        
        $result = $projetos->fetchAll($select);
        
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        foreach ($result as $item) {
            
            $existeReadequacaoEmAndamento = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmAndamento($item->idPronac);
            $existeReadequacaoPlanilhaEmEdicao = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoPlanilhaEmEdicao($item->idPronac);
            $existeReadequacaoParcialEmEdicao = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoParcialEmEdicao($item->idPronac);
            $possuiRelatorioDeCumprimento = $tbCumprimentoObjeto->possuiRelatorioDeCumprimento($item->idPronac);
            
            $Readequacao = false;
            if (!$existeReadequacaoEmAndamento && !$existeReadequacaoEmAndamento) {
                $Readequacao = true;
            } else if ($existeReadequacaoEmAndamento && $existeReadequacaoPlanilhaEmEdicao) {
                $ReadequacaoPlanilha = true;
            } else if ($existeReadequacaoEmAndamento && $existeReadequacaoParcialEmEdicao) {
                $Readequacao = false;
            } else if ($existeReadequacaoEmAndamento && !$existeReadequacaoPlanilhaEmEdicao)  {
                $Readequacao = false;
            }
            if ($possuiRelatorioDeCumprimento) {
                $Readequacao = false;
            }
            
            if ($Readequacao) {
                $idPronac = $item->idPronac;
                break;
            }                
        }
        return $idPronac;
    }
    
    /**
     * TestIndexAction
     *
     * @access public
     * @return void
     */    
    public function testIndexAction()
    {
        $this->dispatch('/readequacao/readequacoes?idPronac=' . $this->hashPronac);
        $this->assertUrl('readequacao', 'readequacoes', 'index');
    }

    /**
     * TestIndexIdPassandoUfAction
     *
     * @access public
     * @return void
     */    
    public function TestIndexIdPassandoUfAction()
    {
        $iduf = 43; // RS
        
        $this->request->setMethod('POST')
            ->setPost([
                'iduf' => $iduf
            ]);        
        $this->dispatch('/readequacao/readequacoes?idPronac=' . $this->hashPronac);
        $this->assertUrl('readequacao', 'readequacoes', 'index');
    }
    
    /**
     * TestPlanilhaOrcamentariaAction
     *
     * @access public
     * @return void
     */    
    public function testPlanilhaOrcamentariaAction()
    {
        $this->dispatch('/readequacao/readequacoes/planilha-orcamentaria?idPronac=' . $this->hashPronac);
        $this->assertUrl('readequacao', 'readequacoes', 'planilha-orcamentaria');
    }

    /**
     * TestPlanilhaOrcamentariaCondicoesNaoSatisfeitas()
     *
     * @access public
     * @return void
     */    
    public function testPlanilhaOrcamentariaCondicoesNaoSatisfeitas()
    {
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $possuiReadequacao = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmAndamento($this->idPronac);
        
        $this->assertFalse($possuiReadequacao);

    }

    /**
     * TestPainelAction
     * 
     * @access public
     * @return void
     */
    public function testPainelAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $this->dispatch('/readequacao/readequacoes/painel');
        $this->assertUrl('readequacao', 'readequacoes', 'painel');
        
    }

    /**
     * TestPainelEmAnaliseAction
     * 
     * @access public
     * @return void
     */
    public function testPainelEmAnaliseAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $this->dispatch('/readequacao/readequacoes/painel?pronac=&qtde=10&tipoFiltro=em_analise');
        $this->assertUrl('readequacao', 'readequacoes', 'painel');
        
    }

    /**
     * TestPainelEmAnalisadosAction
     * 
     * @access public
     * @return void
     */
    public function testPainelEmAnalisadosAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $this->dispatch('/readequacao/readequacoes/painel?pronac=&qtde=10&tipoFiltro=em_analisados');
        $this->assertUrl('readequacao', 'readequacoes', 'painel');
        
    }

    /**
     * TestPainelAguardandoPublicacaoAction
     * 
     * @access public
     * @return void
     */
    public function testPainelAguardandoPublicacaoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $this->dispatch('/readequacao/readequacoes/painel?pronac=&qtde=10&tipoFiltro=aguardando_publicacao');
        $this->assertUrl('readequacao', 'readequacoes', 'painel');
        
    }

    /**
     * TestPainelAnalisadosPronac
     * 
     * @access public
     * @return void
     */
    public function TestPainelAnalisadosPronacAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        
        $idPronac = 154566;
        $this->dispatch('/readequacao/readequacoes/painel?pronac=' . $idPronac . '&qtde=10&tipoFiltro=aguardando_publicacao');
        $this->assertUrl('readequacao', 'readequacoes', 'painel');

    }

    public function testPainelReadequacoesAction()
    {
        $this->dispatch('/readequacao/readequacoes/painel-readequacoes?idPronac=' . $this->idPronac);
        $this->assertUrl('readequacao', 'readequacoes', 'painel-readequacoes');
    }

    public function testPainelReadequacoesBuscaPronacAction()
    {
        $this->dispatch('/readequacao/readequacoes/painel-readequacoes?pronac=' . $this->pronac . '&qtde=10&tipoFiltro=aguardando_distribuicao');
        $this->assertUrl('readequacao', 'readequacoes', 'painel-readequacoes');
    }    
    
    public function testVisualizarReadequacaoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacao/readequacoes/visualizar-readequacao?idPronac=' . $this->idPronac);
        $this->assertUrl('readequacao', 'readequacoes', 'visualizar-readequacao');
    }

    public function testCarregarValorEntrePlanilhasAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacao/readequacoes/carregar-valor-entre-planilhas?idPronac=' . $this->idPronac);
        $this->assertUrl('readequacao', 'readequacoes', 'carregar-valor-entre-planilhas');
    }

    public function testAvaliarReadequacaoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacao/readequacoes/avaliar-readequacao?idPronac=' . $this->hashPronac);
        $this->assertUrl('readequacao', 'readequacoes', 'avaliar-readequacao');
    }

    public function testAvaliarReadequacaoTecnicoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacao/readequacoes/avaliar-readequacao?idPronac=' . $this->hashPronac);
        $this->assertUrl('readequacao', 'readequacoes', 'avaliar-readequacao');
    }

    public function testEncaminharReadequacaoTecnicoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacao/readequacoes/encaminhar-analise-tecnica?id=' . $this->hashPronac . '&filtro=painel_do_tecnico');
        $this->assertUrl('readequacao', 'readequacoes', 'encaminhar-analise-tecnica');
    }

    public function testFormAvaliarReadequacaoTecnicoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/readequacao/readequacoes/form-avaliar-readequacao?id=' . $this->hashPronac . '&filtro=painel_do_tecnico');
        $this->assertUrl('readequacao', 'readequacoes', 'form-avaliar-readequacao');
    }
}
