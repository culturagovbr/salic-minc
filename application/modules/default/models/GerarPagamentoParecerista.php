<?php
/**
 * Description of GerarPagamentoParecerista
 *
 * @author Tarcisio Angelo
 */
class GerarPagamentoParecerista extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbGerarPagamentoParecerista';
    protected $_schema = 'SAC';
    protected $_banco = 'SAC';

    public function buscarDespachos($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('gpp'=>$this->_name),
            array(
                'gpp.idGerarPagamentoParecerista',
                'gpp.idConfigurarPagamento',
                new Zend_Db_Expr('CONVERT(VARCHAR(10), gpp.dtGeracaoPagamento ,103) as dtGeracaoPagamento'),
                new Zend_Db_Expr('CONVERT(VARCHAR(10), gpp.dtEfetivacaoPagamento ,103) as dtEfetivacaoPagamento'),
                new Zend_Db_Expr('CONVERT(VARCHAR(10), gpp.dtOrdemBancaria ,103) as dtOrdemBancaria'),
                'gpp.nrOrdemBancaria',
                new Zend_Db_Expr('CONVERT(INT, gpp.nrDespacho) as nrDespacho'),
                'gpp.siPagamento',
                'gpp.vlTotalPagamento',
                'gpp.idUsuario'
            )
        );
        $select->joinInner(
            array('pp'=> 'tbPagarParecerista'),
            "pp.idGerarPagamentoParecerista = gpp.idGerarPagamentoParecerista",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('ag'=> 'Agentes'),
            "pp.idParecerista = ag.idAgente",
            array(''),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('nm'=> 'Nomes'),
            "ag.idAgente = nm.idAgente",
            array(new Zend_Db_Expr('nm.Descricao AS nmParecerista')),
            'AGENTES.dbo'
        );


        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        //Total de dados da pagina��o
        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($select);
    }

    public function ultimoDespachoDoAno()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('gpp'=>$this->_name),
            array(
                new Zend_Db_Expr('max(convert(int,(nrDespacho))) as UltimoDespachoDoAno')
            )
        );
        $select->where('YEAR([dtGeracaoPagamento]) = ?', new Zend_Db_Expr('YEAR(GETDATE())'));
        return $this->fetchRow($select);
    }

    public function buscarProjetosFinalizados($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pro' => 'Projetos'),
                        array('Pronac' => new Zend_Db_Expr('pro.AnoProjeto + pro.Sequencial'),
                              'sac.dbo.fnNome(pp.idParecerista) as Parecerista',
                              'pro.IdPRONAC as idPronac',
                              'pro.NomeProjeto')
        );


        $select->joinInner(
            array('pp'=> 'tbPagarParecerista'),
            "pro.IdPRONAC = pp.idPronac",
                            array('pp.vlPagamento')
        );

        $select->joinInner(
            array('a'=> 'Agentes'),
            "pp.idParecerista = a.idAgente",
                            array('a.CNPJCPF'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('p'=> 'Produto'),
            "pp.idProduto = p.Codigo",
                            array('p.Descricao as Produto')
        );

        $select->joinInner(
            array('o'=> 'Orgaos'),
            "pp.idUnidadeAnalise = o.Codigo",
                            array('o.Sigla as Vinculada')
        );

        $select->joinLeft(
            array('gpp'=> 'tbGerarPagamentoParecerista'),
            "pp.idGerarPagamentoParecerista = gpp.idGerarPagamentoParecerista",
            array(
                new Zend_Db_Expr('CONVERT(VARCHAR(10), gpp.dtGeracaoPagamento ,103) as dtGeracaoPagamento'),
                new Zend_Db_Expr('CONVERT(VARCHAR(10), gpp.dtEfetivacaoPagamento ,103) as dtEfetivacaoPagamento'),
                new Zend_Db_Expr('CONVERT(VARCHAR(10), gpp.dtOrdemBancaria ,103) as dtOrdemBancaria'),
                'gpp.nrOrdemBancaria',
                'gpp.nrDespacho',
                'gpp.siPagamento as Estado'
            )
        );

        $siArquivo = 1;
        if (isset($where['gpp.siPagamento = ?']) && !empty($where['gpp.siPagamento = ?'])) {
            if (5 == $where['gpp.siPagamento = ?']) {
                $siArquivo = 2;
            }
        }
        $select->joinLeft(
            array('pa'=>'tbPagamentoPareceristaXArquivo'),
                "pa.idGerarPagamentoParecerista = gpp.idGerarPagamentoParecerista and pa.siArquivo = {$siArquivo}",
            array(),
                'SAC.dbo'
        );
        $select->joinLeft(
            array('arq'=>'tbArquivo'),
            "arq.idArquivo = pa.idArquivo",
            array('arq.idArquivo', 'nomeArquivo' => 'arq.nmArquivo'),
            'BDCORPORATIVO.scCorp'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }




        return $this->fetchAll($select);
    }
}
