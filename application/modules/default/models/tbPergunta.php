<?php
class tbPergunta extends MinC_Db_Table_Abstract
{
    protected $_banco   = "BDCORPORATIVO";
    protected $_schema  = "BDCORPORATIVO.scQuiz";
    protected $_name = 'tbPergunta';

    public function procurarPergunta($nrPergunta)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array("p" => $this->_schema .".". $this->_name),
                        array(
                                'p.dsPergunta','p.nrPergunta'
                             )
                     );

        $select->where('p.nrPergunta=?', $nrPergunta);

        return $this->fetchRow($select);
    }

    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTbPergunta = new tbPergunta();

        if (isset($dados['nrPergunta'])) {
            $tmpRsTbPergunta = $tmpTbPergunta->find($dados['nrPergunta'])->current();
        } else {
            $tmpRsTbPergunta = $tmpTbPergunta->createRow();
        }

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['stTipoRespPergunta'])) {
            $tmpRsTbPergunta->stTipoRespPergunta = $dados['stTipoRespPergunta'];
        }
        if (isset($dados['dsPergunta'])) {
            $tmpRsTbPergunta->dsPergunta = $dados['dsPergunta'];
        }
        if (isset($dados['dtCadastramento'])) {
            $tmpRsTbPergunta->dtCadastramento = $dados['dtCadastramento'];
        }
        if (isset($dados['idPessoaCadastro'])) {
            $tmpRsTbPergunta->idPessoaCadastro = $dados['idPessoaCadastro'];
        }

        //SALVANDO O OBJETO CRIADO
        $id = $tmpRsTbPergunta->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }


    public function buscarDados($where=array(), $order=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array("p" => $this->_schema .".". $this->_name),
                array("p.dsPergunta", "p.nrPergunta")
            );
        $select->joinInner(
                    array("rv" => $this->_schema ."."."tbOpcaoRespostaVariavel"),
                "p.nrPergunta = rv.nrPergunta",
                array('rv.vlMinOpcao','rv.vlMaxOpcao','rv.vlVariacaoOpcao')
            );
        $select->joinInner(
                    array("pd" => $this->_schema ."."."tbPerguntaFormDocto"),
                "p.nrPergunta = pd.nrPergunta",
                array('pd.nrPeso','pd.dsLabelPergunta')
            );
        $select->joinInner(
                    array("fd" => $this->_schema ."."."tbFormDocumento"),
                "fd.nrFormDocumento = pd.nrFormDocumento"
            );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order($order);

        return $this->fetchAll($select);
    }

    public function montarQuestionario($nrFormDocumento, $nrVersaoDocumento, $nrPergunta = '')
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array("p" =>  $this->_name),
            array('dsPergunta'=>new Zend_Db_Expr("CONVERT(TEXT,p.dsPergunta)"), "p.nrPergunta"),
                $this->_schema
        );
        $select->joinInner(
                array("pfd" => "tbPerguntaFormDocto"),
            "p.nrPergunta = pfd.nrPergunta",
            array('pfd.nrOrdemPergunta','dsLabelPergunta'=>new Zend_Db_Expr('CONVERT(TEXT,pfd.dsLabelPergunta)')),
            'BDCORPORATIVO.scQuiz'
        );
        $select->where('pfd.nrFormDocumento = ?', $nrFormDocumento);
        $select->where('pfd.nrVersaoDocumento = ?', $nrVersaoDocumento);

        if ($nrPergunta != '') {
            $select->where('pfd.nrPergunta = ?', $nrPergunta);
        }

        $select->order(array('pfd.nrOrdemPergunta'));
        return $this->fetchAll($select);
    }
    public function montarAlternativa($nrFormDocumento, $nrVersaoDocumento, $nrPergunta)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array("p" => $this->_schema .".". $this->_name),
            array('dsPergunta'=>new Zend_Db_Expr("CONVERT(TEXT,p.dsPergunta)"), "p.nrPergunta")
        );
        $select->joinInner(
                array("pfd" => "tbPerguntaFormDocto"),
            "pfd.nrPergunta = p.nrPergunta",
            array('pfd.nrQtdMinResposta','pfd.nrQtdMaxResposta'),
            'BDCORPORATIVO.scQuiz'
        );
        $select->joinInner(
                array("opr" => "tbOpcaoResposta"),
            "opr.nrPergunta = pfd.nrPergunta and opr.nrFormDocumento = pfd.nrFormDocumento and opr.nrVersaoDocumento = pfd.nrVersaoDocumento",
            array('opr.nrOpcao','opr.stTipoObjetoPgr','opr.dsOpcao'),
            'BDCORPORATIVO.scQuiz'
        );
        $select->where('pfd.nrFormDocumento = ?', $nrFormDocumento);
        $select->where('pfd.nrVersaoDocumento = ?', $nrVersaoDocumento);
        $select->where('pfd.nrPergunta = ?', $nrPergunta);

        return $this->fetchAll($select);
    }


    public function buscarDadosPerguntaFormDcto($where=array(), $order=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array("p" => $this->_schema .".". $this->_name),
                array("p.dsPergunta", "p.nrPergunta")
            );
        $select->joinInner(
                    array("pd" => $this->_schema ."."."tbPerguntaFormDocto"),
                "p.nrPergunta = pd.nrPergunta",
                array('pd.nrPeso','pd.dsLabelPergunta')
            );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order($order);
        return $this->fetchAll($select);
    }

    public function listaCompleta($where=array(), $order=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array("a" => $this->_schema .".". $this->_name),
                array(new Zend_Db_Expr('distinct(a.nrPergunta)'), "a.dsPergunta as dsPergunta")
            );
        $select->joinInner(
                    array("b" => $this->_schema ."."."tbPerguntaFormDocto"),
                "b.nrPergunta = a.nrPergunta",
                array('b.dsLabelPergunta as dsLabelPergunta','b.nrFormDocumento',
                'b.nrVersaoDocumento','b.nrPeso','b.nrOrdemPergunta')
            );

        $select->joinInner(
                    array("f" => $this->_schema ."."."tbFormDocumento"),
                "f.nrFormDocumento = b.nrFormDocumento AND f.nrVersaoDocumento = b.nrVersaoDocumento"
            );

        $select->joinInner(
                    array("c" => $this->_schema ."."."tbOpcaoResposta"),
                "c.nrPergunta = a.nrPergunta and c.nrVersaoDocumento = b.nrVersaoDocumento and c.nrFormDocumento = b.nrFormDocumento",
                array('c.nrOpcao')
            );
        $select->joinInner(
                    array("d" => $this->_schema ."."."tbOpcaoRespostaVariavel"),
                "d.nropcao = c.nropcao and d.nrPergunta = a.nrPergunta and d.nrVersaoDocumento = d.nrVersaoDocumento and d.nrFormDocumento = b.nrFormDocumento",
                array('d.vlMinOpcao as nrNotaInicio',
                'd.vlMaxOpcao as nrNotaFim','d.vlVariacaoOpcao as nrNotaVariacao')
            );
        $select->joinLeft(
                    array("r" => $this->_schema ."."."tbResposta"),
               "c.nropcao = r.nropcao and a.nrPergunta = r.nrPergunta",
               array('r.dsRespostaSubj')
            );


        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order($order);

        return $this->fetchAll($select);
    }
}
