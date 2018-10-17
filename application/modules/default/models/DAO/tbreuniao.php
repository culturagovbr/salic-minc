<?php

class tbreuniao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'tbReuniao';
    protected $_primary = 'idNrReuniao';

    public function listar($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('tbr' => $this->_name));

        /*$slct->joinInner(array('v1' => 'Parecer'),
                               'v1.idVerificacao = pd.idPeca',
                         array('v1.Descricao as Peca'));*/
        $slct->joinInner(
            array('v2' => 'Mecanismo'),
            'v2.Codigo = tbr.Mecanismo',
            array('v2.descricao as str_Mecanismo')
        );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);
        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        $this->_totalRegistros = $this->pegaTotal($where, $order);


        return $this->fetchAll($slct);
    }

    public function atualizarreuniao($dados)
    {
        $rsReuniao = $this->find($dados['idNrReuniao'])->current();

        $rsReuniao->NrReuniao = $dados['NrReuniao'];
        $rsReuniao->DtInicio = ConverteData($dados['DtInicio'], 13);
        $rsReuniao->DtFinal = ConverteData($dados['DtFinal'], 13);
        $rsReuniao->DtFechamento = ConverteData($dados['DtFechamento'], 13);
        $rsReuniao->Mecanismo = $dados['Mecanismo'];
        $rsReuniao->idUsuario = $dados['Mecanismo'];

        if ($rsReuniao->save()) {
            return true;
        } else {
            return false;
        }
    }


    public function pegaTotal($where = array(), $order = array())
    {
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(array('tbr' => $this->_name));

        /*$slct->joinInner(array('v1' => 'Parecer'),
                               'v1.idVerificacao = pd.idPeca',
                         array('v1.Descricao as Peca'));*/
        $slct->joinInner(
            array('v2' => 'Mecanismo'),
            'v2.Codigo = tbr.Mecanismo',
            array('v2.descricao as str_Mecanismo')
        );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        $rows = $this->fetchAll($slct);
        return $rows->count();
    }


    public static function salvareuniao($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("SAC.dbo.tbReuniao", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    public function obterReuniaoDeAvaliacaoDoProjeto($idPronac, $where = [])
    {
        /**
         * select DtFinal from sac.dbo.tbReuniao as a
        inner join BDCORPORATIVO.scSAC.tbPauta as b
        on a.idNrReuniao = b.idNrReuniao
        where b.idPronac = 209751;
         */
        $query = $this->select();

        $query->setIntegrityCheck(false);
        $query->from(
            ['tbReuniao' => $this->_name],
            [
                new Zend_Db_Expr("DtFinal"),
                'diasAnaliseProjeto' => new Zend_Db_Expr('DATEDIFF(DAY, DtFinal, GETDATE())')
            ],
            $this->_schema
        );

        $query->joinInner(
            ['tbPauta' => "tbPauta"],
            'tbReuniao.idNrReuniao = tbPauta.idNrReuniao',
            [''],
            $this->getSchema('BDCORPORATIVO.scSAC')
        );

        $query->where('tbPauta.idPronac = ?', $idPronac);

        foreach ($where as $coluna => $valor) {
            $query->where($coluna, $valor);
        }

        return $this->fetchRow($query);
    }
}
