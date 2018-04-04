<?php
class Produto extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "produto";

    public function find()
    {
        $args = func_get_args();
        $rowset = parent::find($args);
        if (1 == count($args) && 0 == $args[0]) {
            $rowset = new Zend_Db_Table_Rowset(
                    array(
                        'data' => array(
                            array(
                                'Codigo' => 0,
                                'Descricao' => 'Livro',
                                'Area' => 0,
                                'Sintese' => 'Administra&ccedil;&atilde;o do Projeto',
                                'Idorgao' => 0,
                                'stEstado' => 0,
                            )
                        )
                    )
                );
        }
        return $rowset;
    }

    public function listarProdutos($where = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(
                array($this->_name),
                array('codigo as codproduto', 'descricao as Produto'),
                $this->_schema
            )
            ->where('stestado = 0')
            ->order('produto')
        ;

        return $db->fetchAll($sql);
    }

    public function buscarProdutosContrato($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('prod'=>$this->_name),
                        array(
                                'id'=>'prod.Codigo','nome'=>'prod.Descricao'
                              )
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idProduto = prod.Codigo',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinLeft(
                            array('cxpa'=>'tbContratoxPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaAprovacao = cxpa.idPlanilhaAprovacao',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('cxpa.idContrato is null');

        $select->order('prod.Descricao');

        $select->group('prod.Codigo');
        $select->group('prod.Descricao');

        return $this->fetchAll($select);
    }
    public function buscarProdutosComprovacao($idpronac, $ckItens)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('prod'=>$this->_name),
                        array(
                                'id'=>'prod.Codigo','nome'=>'prod.Descricao'
                              )
                      );
        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idProduto = prod.Codigo',
                            array(),
                            'SAC.dbo'
                           );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');

        /* erro 26
        if(is_array($ckItens) and count(is_array($ckItens))>0)
            $select->where('pAprovacao.idPlanilhaItem in ('.implode(',',$ckItens).')');
        */
        if (is_array($ckItens) and count(is_array($ckItens))>0) {
            $select->where('pAprovacao.idPlanilhaAprovacao in ('.implode(',', $ckItens).')');
        }

        $select->order('prod.Descricao');

        $select->group('prod.Codigo');
        $select->group('prod.Descricao');

        return $this->fetchAll($select);
    }

    public function buscarProdutos($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('prod'=>$this->_name),
                        array(
                                'id'=>'prod.Codigo','nome'=>'prod.Descricao'
                              )
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idProduto = prod.Codigo',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinLeft(
                            array('cxpa'=>'tbCotacaoxPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaAprovacao = cxpa.idPlanilhaAprovacao',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        $select->joinLeft(
                            array('dlxpa'=>'tbDispensaLicitacaoxPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaAprovacao = dlxpa.idPlanilhaAprovacao',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        $select->joinLeft(
                            array('lxpa'=>'tbLicitacaoxPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaAprovacao = lxpa.idPlanilhaAprovacao',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('cxpa.idCotacao is null');
        $select->where('dlxpa.idDispensaLicitacao is null');
        $select->where('lxpa.idLicitacao is null');

        $select->order('prod.Descricao');

        $select->group('prod.Codigo');
        $select->group('prod.Descricao');

        return $this->fetchAll($select);
    }


    public function carregarProdutos($idpronac, $idCotacao, $idDispensaLicitacao, $idLicitacao, $idContrato)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('prod'=>$this->_name),
                        array(
                                'id'=>'prod.Codigo','nome'=>'prod.Descricao'
                              )
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idProduto = prod.Codigo',
                            array(),
                            'SAC.dbo'
                           );
        if ($idCotacao) {
            $select->joinInner(
                            array('ctxpa'=>'tbCotacaoxPlanilhaAprovacao'),
                            "pAprovacao.idPlanilhaAprovacao = ctxpa.idPlanilhaAprovacao and ctxpa.idCotacao = '$idCotacao' ",
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        }
        if ($idDispensaLicitacao) {
            $select->joinInner(
                            array('dlxpa'=>'tbDispensaLicitacaoxPlanilhaAprovacao'),
                            "pAprovacao.idPlanilhaAprovacao = dlxpa.idPlanilhaAprovacao and dlxpa.idDispensaLicitacao  = '$idDispensaLicitacao' ",
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        }
        if ($idLicitacao) {
            $select->joinInner(
                            array('lxpa'=>'tbLicitacaoxPlanilhaAprovacao'),
                            "pAprovacao.idPlanilhaAprovacao = lxpa.idPlanilhaAprovacao and lxpa.idLicitacao  = '$idLicitacao' ",
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        }
        if ($idContrato) {
            $select->joinInner(
                            array('cnxpa'=>'tbContratoxPlanilhaAprovacao'),
                            "pAprovacao.idPlanilhaAprovacao = cnxpa.idPlanilhaAprovacao and cnxpa.idContrato = '$idContrato' ",
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        }


        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);

        $select->order('prod.Descricao');

        $select->group('prod.Codigo');
        $select->group('prod.Descricao');

        return $this->fetchAll($select);
    }
}
