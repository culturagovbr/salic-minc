<?php
/**
 * Readequacao_RemanejamentoMenorControllerTest
 *
 * @package
 * @author
 */
class Readequacao_RemanejamentoMenorControllerTest extends MinC_Test_ControllerActionTestCase
{
    /**
     * setUp - configuração inicial do teste
     *
     * @access public
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->autenticar();
        
        $this->resetRequest()
            ->resetResponse();
        
        $this->perfilParaProponente();
        
        $this->resetRequest()
            ->resetResponse();

        $this->idPronac = $this->getIdPronacProjetoAptoRemanejamento();
        $this->hashPronac = Seguranca::encrypt($this->idPronac);
    }

    /**
     * getIdPronacProjetoAptoRemanejamento - retorna idPronac de um projeto apto a realizar remanejamento parcial
     *
     * @access private
     * @return void
     */
    private function getIdPronacProjetoAptoRemanejamento()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV
        );
        
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
        $select->where('p.cgccpf = ?', $config->test->params->login);
        $select->limit(30);
        
        $result = $projetos->fetchAll($select);
        
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        foreach ($result as $item) {
            
            $existeReadequacaoEmAndamento = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmAndamento($item->idPronac);
            $existeReadequacaoPlanilhaEmEdicao = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoPlanilhaEmEdicao($item->idPronac);
            $existeReadequacaoParcialEmEdicao = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoParcialEmEdicao($item->idPronac);
            $possuiRelatorioDeCumprimento = $tbCumprimentoObjeto->possuiRelatorioDeCumprimento($item->idPronac);
            
            $Readequacao_50 = false;
            if (!$existeReadequacaoEmAndamento && !$existeReadequacaoEmAndamento) {
                $Readequacao_50 = true;
            } else if ($existeReadequacaoEmAndamento && $existeReadequacaoPlanilhaEmEdicao) {
                $Readequacao_50 = false;
            } else if ($existeReadequacaoEmAndamento && $existeReadequacaoParcialEmEdicao) {
                $Readequacao_50 = true;
            } else if ($existeReadequacaoEmAndamento && !$existeReadequacaoPlanilhaEmEdicao)  {
                $Readequacao_50 = false;
            }
            if ($possuiRelatorioDeCumprimento) {
                $Readequacao_50 = false;
            }
            
            if ($Readequacao_50) {
                $idPronac = $item->idPronac;
                break;
            }                
        }
        return $idPronac;
    }

    /**
     * getLastIdReadequacao - retorna último idReadequacao inserido
     *
     * @access private
     * @return void
     */
    private function getLastIdReadequacao()
    {
        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        
        $select = $tbReadequacao->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('r' => 'tbReadequacao'),
            'idReadequacao',
            'sac.dbo'
        );
        $select->where('idPronac = ?', $this->idPronac);
        $select->limit(1);
        $select->order('idReadequacao DESC');

        $result = $tbReadequacao->fetchAll($select)->current();

        return $result->idReadequacao;
    }
    
    /**
     * getItemReadequacao - retorna um item a partir do pronac
     *
     * @access private
     * @param integer $item
     * @return void
     */
    private function getItemReadequacao($item)
    {
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        
        $select = $tbPlanilhaAprovacao->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('r' => 'tbPlanilhaAprovacao'),
            '*',
            'sac.dbo'
        );
        $select->where('idPronac = ?', $this->idPronac);
        $select->where('idReadequacao = ?', $this->getLastIdReadequacao());
        $select->where('stAtivo = ?', 'N');
        $select->where('idEtapa = ?', 1);
        $select->order([
            'vlUnitario ASC',
            'nrOcorrencia ASC',
            'qtItem ASC'
        ]);
        $select->limit($item);
        
        $result = $tbPlanilhaAprovacao->fetchAll($select);
        
        if (count($result) > 0) {
            return $result[$item -1];
        } else {
            return false;
        }
    }

    /**
     * criarRemanejamento50 - cria um remanejamento 
     *
     * @access private
     * @return void
     */
    private function criarRemanejamento50()
    {
        $this->request->setMethod('POST')
            ->setPost([
                'idPronac' => $this->idPronac,
                'idReadequacao' => '',
                'idTipoReadequacao' => 5
            ]);                
        $this->dispatch('/readequacao/remanejamento-menor/verificar-planilha-ativa');
    }
    
    /**
     * salvarAlteracaoItem - realiza uma alteração em um item e grava
     *
     * @access private
     * @return void
     */
    private function salvarAlteracaoItem($item, $incremento)
    {
        $itemReadequacao =  $this->getItemReadequacao($item);
        $novoVlUnitario = $itemReadequacao->vlUnitario + $incremento;
        
        if ($incremento > 0) {
            $justificativa = 'Aumento do valor unitário. Realizada pelo teste.';
        } else if ($incremento < 0) {
            $justificativa = 'Diminuição do valor unitário. Realizada pelo teste.';
        }
        
        $this->request->setMethod('POST')
            ->setPost([
                'idPronac' => $this->idPronac,
                'idReadequacao' => $this->getLastIdReadequacao(),
                'idPlanilhaAprovacao' => $itemReadequacao->idPlanilhaAprovacao,
                'idPlanilhaAprovacaoPai' => $itemReadequacao->idPlanilhaAprovacaoPai,
                'qtItem' => $itemReadequacao->qtItem,
                'nrOcorrencia' => $itemReadequacao->nrOcorrencia,
                'vlUnitario' => $novoVlUnitario,
                'Justificativa' => $justificativa
            ]);
        
        $this->dispatch('/readequacao/remanejamento-menor/salvar-avaliacao-do-item-remanejamento');
    }

    /**
     * removerReadequacao - remove uma readequação
     *
     * @access private
     * @return void
     */
    private function removerReadequacao()
    {
       $this->request->setMethod('POST')
            ->setPost([
                'idPronac' => $this->idPronac,
                'idReadequacao' => $this->getLastIdReadequacao()
            ]);                
        $this->dispatch('/readequacao/remanejamento-menor/reintegrar-planilha');        
    }

    /**
     * finalizarReadequacao - finaliza uma readequação
     *
     * @access private
     * @return void
     */   
    private function finalizarReadequacao()
    {
        $this->request->setMethod('POST')
            ->setPost([
                'idPronac' => $this->idPronac,
                'idReadequacao' => $this->getLastIdReadequacao()
            ]);
        
        $this->dispatch('/readequacao/remanejamento-menor/finalizar');
    }       

    
    /**
     * TestIndexAction
     *
     * @access public
     * @return void
     */    
    public function testIndexAction()
    {
        $this->dispatch('/readequacao/remanejamento-menor?idPronac=' . $this->hashPronac);
        $this->assertUrl('readequacao','remanejamento-menor', 'index');
    }
    
    /**
     * TestCarregarValorPorGrupoRemanejamentoAction
     *
     * @access public
     * @return void
     */    
    public function testCarregarValorPorGrupoRemanejamentoAction()
    {
        $this->dispatch('/readequacao/remanejamento-menor/carregar-valor-entre-planilhas?idPronac=' . $this->idPronac);
        $this->assertUrl('readequacao','remanejamento-menor', 'carregar-valor-entre-planilhas');
    }

    /**
     * TestCriarRemanejamento50
     *
     * @access public
     * @return void
     */    
    public function testCriarRemanejamento50()
    {
        $this->criarRemanejamento50();
        
        $this->assertUrl('readequacao','remanejamento-menor', 'verificar-planilha-ativa');
        $this->assertResponseCode(200);
        
        $expected = new StdClass();
        $expected->msg = 'Planilha copiada corretamente';
        $expected->idReadequacao = (int)$this->getLastIdReadequacao();
        
        $data = $this->getResponse()->getBody();
        
        $this->assertEquals(
            json_encode($expected),
            $data
        );
    }   

    /**
     * TestCarregarTelaAlterarItem
     *
     * @access public
     * @return void
     */    
    public function testCarregarTelaAlterarItem()
    {
        $tbPlanilhaAprovacao = $this->getItemReadequacao(1);
        
        $this->request->setMethod('POST')
            ->setPost([
                'idPronac' => $this->idPronac,
                'idReadequacao' => $this->getLastIdReadequacao(),
                'idPlanilhaAprovacao' => $tbPlanilhaAprovacao->idPlanilhaAprovacao,
                'idPlanilhaAprovacaoPai' => $tbPlanilhaAprovacao->idPlanilhaAprovacaoPai
            ]);
        
        $this->dispatch('/readequacao/remanejamento-menor/alterar-item');
        $this->assertUrl('readequacao','remanejamento-menor', 'alterar-item');
        $this->assertResponseCode(200);
        
        $data = json_decode($this->getResponse()->getBody(), true);
        
        $this->assertArrayHasKey('dadosPlanilhaAtiva', $data);
        $this->assertArrayHasKey('dadosPlanilhaEditavel', $data);
        $this->assertArrayHasKey('dadosPlanilhaOriginal', $data);
        $this->assertArrayHasKey('dadosProjeto', $data);
        $this->assertArrayHasKey('resposta', $data);
        $this->assertArrayHasKey('valoresDoItem', $data);
    }

    
    /**
     * TestSalvarAlteracaoItem()
     *
     * @access public
     * @return void
     */    
    public function testSalvarAlteracaoItem()
    {
        $this->salvarAlteracaoItem(1, 1);
        
        $this->assertUrl('readequacao','remanejamento-menor', 'salvar-avaliacao-do-item-remanejamento');
        $this->assertResponseCode(200);
        
        $expected = new StdClass();
        $expected->resposta = true;
        $expected->msg = "Dados salvos com sucesso!";
        
        $data = $this->getResponse()->getBody();
        
        $this->assertEquals(
            json_encode($expected),
            $data
        );       
    }

    /**
     * TestRemoverRemanejamento50
     *
     * @access public
     * @return void
     */    
    public function testRemoverRemanejamento50()
    {
        $this->removerReadequacao();
        
        $this->assertUrl('readequacao','remanejamento-menor', 'reintegrar-planilha');
        $this->assertResponseCode(200);
    }    
    
    /**
     * TestRealizarRemanejamentoFinalizando
     *
     * @access public
     * @return void
     */    
    public function testRealizarRemanejamentoFinalizandoComErro()
    {
        $this->criarRemanejamento50();
        $this->salvarAlteracaoItem(1, 1);
        $this->salvarAlteracaoItem(2, -1);
        $this->finalizarReadequacao();
        $this->assertRedirect('readequacao','remanejamento-menor', 'index');
    }

    // TODO
    public function testRealizarRemanejamentoFinalizandoCorretamente()
    {
        // TODO
        // assegurar que remanejamento terá saldo zero
        // fazer conta de qtItem * nrOcorrencia * vlUnitario

        //$this->assertRedirect('default','consultardadosprojeto', 'index');
    }
}
