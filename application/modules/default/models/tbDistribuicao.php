<?php
/**
 * DAO tbDistribuicaoProduto
 * @since 16/03/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbDistribuicao extends MinC_Db_Table_Abstract
{
    protected $_banco  = "BDCORPORATIVO";
    protected $_schema = "scSAC";
    protected $_name   = "tbDistribuicao";


    public function listaDistribuicao($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('dis'=>$this->_name),
                        array(
                            'dis.idItemDistribuicao',
                            'dis.idDestinatario',
                            'dis.dsObservacao',
                            'dis.idDistribuicao'
                            ),
                $this->_banco.".".$this->_schema
                        );
        $slct->joinInner(
                            array('nom'=>'Nomes'),
                            'nom.idAgente = dis.idDestinatario',
                            array('nom.Descricao'),
                            'AGENTES.dbo'
                            );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        
        return $this->fetchAll($slct);
    }


    public function listaDistribuicaoProjetos($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('dis'=>$this->_name),
                        array(
                            new Zend_Db_Expr('distinct(dis.idItemDistribuicao)'),
                            'dis.idDestinatario',
                            'dis.dsObservacao',
                            'dis.idDistribuicao'
                            ),
                $this->_banco.".".$this->_schema
                        );
        $slct->joinInner(
                            array('nom'=>'Nomes'),
                            'nom.idAgente = dis.idDestinatario',
                            array('nom.Descricao'),
                            'AGENTES.dbo'
                            );
        $slct->joinInner(
                            array('pro'=>'Projetos'),
                            'dis.idItemDistribuicao = pro.idProjeto',
                            array('pro.AnoProjeto','pro.Sequencial','NomeProjeto'),
                            'SAC.dbo'
                            );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        
        return $this->fetchAll($slct);
    }



    public function listaRedistribuicaoPreprojetos($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('dis'=>$this->_name),
                        array(
                            new Zend_Db_Expr('distinct(dis.idItemDistribuicao)'),
                            'dis.idDestinatario',
                            'dis.dsObservacao',
                            'dis.idDistribuicao'
                            ),
                $this->_banco.".".$this->_schema
                        );
        $slct->joinInner(
                            array('nom'=>'Nomes'),
                            'nom.idAgente = dis.idDestinatario',
                            array('nom.Descricao'),
                            'AGENTES.dbo'
                            );
        $slct->joinInner(
                            array('pp'=>'PreProjeto'),
                            'dis.idItemDistribuicao = pp.idPreProjeto',
                            array('idPreProjeto'),
                            'SAC.dbo'
                            );
        $slct->joinInner(
                            array('edi'=>'Edital'),
                            'pp.idEdital = edi.idEdital',
                            array('NrEdital'),
                            'SAC.dbo'  //'BDCORPORATIVO.scSAC' - Antigo
                            );
        $slct->joinInner(
                            array('pro'=>'Projetos'),
                            'pp.idPreProjeto = pro.idProjeto',
                            array('idPronac','UFProjeto','AnoProjeto','Sequencial','NomeProjeto','Situacao'),
                            'SAC.dbo'
                            );
        $slct->joinInner(
                array('fod' => 'tbFormDocumento'),
                            'fod.idEdital = edi.idEdital and fod.idClassificaDocumento not in (23,24,25)',
                            array('fod.nmFormDocumento'),
                            'BDCORPORATIVO.scQuiz'
                            );
        $slct->joinLeft(
                array('ava' => 'tbAvaliacaoPreProjeto'),
                            'ava.idPreProjeto = pp.idPreProjeto and ava.idAvaliador = dis.idDestinatario',
                            array('nrNotaFinal'),
                            'BDCORPORATIVO.scSAC'
                            );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        
        return $this->fetchAll($slct);
    }


    public function QTDAvaliadorXenvio($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('D'=>$this->_name),
                        array('*'),
                        $this->_banco.".".$this->_schema
                        );
        $slct->joinInner(
                            array('A'=>'tbAvaliacaoPreProjeto'),
                            'A.idPreProjeto = D.idItemDistribuicao and A.idAvaliador = D.idDestinatario',
                            array('A.stAvaliacao'),
                            $this->_banco.".".$this->_schema
                            );
        $slct->joinInner(
                            array('PP'=>'PreProjeto'),
                            'PP.idPreProjeto = D.idItemDistribuicao',
                            array(''),
                            'SAC.dbo'
                            );
        $slct->joinInner(
                            array('Ed'=>'Edital'),
                            'Ed.idEdital = PP.idEdital',
                            array('Ed.qtAvaliador'),
                            'SAC.dbo'
                            );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        
        return $this->fetchAll($slct);
    }

    



















    public function buscarDistribuicaoProduto($idDistribuicaoProduto)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('dp'=>$this->_name),
                        array(
                            'dp.qtDistribuicao',
                            'dp.idDocumento',
                            'dp.dsTamanhoDuracao',
                            'dp.idDistribuicaoProduto',
                            'dp.dsFinsLucrativos',
                            'dp.dsDestinacaoProduto',
                            'dp.dsReceptorProduto',
                            '*'
                            )
                        );
        $slct->joinInner(
                            array('pdp'=>'PlanoDistribuicaoProduto'),
                            'dp.idPlanoDistribuicao = pdp.idPlanoDistribuicao AND pdp.stPlanoDistribuicaoProduto = 1',
                            array(
                                    'pdp.QtdeProduzida',
                                    '(pdp.QtdePatrocinador+pdp.QtdeProponente+pdp.QtdeProponente) as DistribuicaoGratuita'
                                 )
                            );
        $slct->joinInner(
                             array('pd'=>'Produto'),
                             "pd.Codigo = pdp.idProduto",
                             array('pd.Descricao')
                            );
        $slct->where('dp.idDistribuicaoProduto = ?', $idDistribuicaoProduto);

        return $this->fetchAll($slct);
    }
} // fecha class
