<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tbreuniao
 *
 * @author 01155078179
 */
class tbreuniao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name  = 'tbreuniao';

    public function listar($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
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

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);
        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        //SETANDO A QUANTIDADE DE REGISTROS
        $this->_totalRegistros = $this->pegaTotal($where, $order);
        

        return $this->fetchAll($slct);
    }

    public function atualizarreuniao($dados)
    {
        $rsReuniao = $this->find($dados['idNrReuniao'])->current();

        $rsReuniao->NrReuniao   = $dados['NrReuniao'];
        $rsReuniao->DtInicio    = ConverteData($dados['DtInicio'], 13);
        $rsReuniao->DtFinal     = ConverteData($dados['DtFinal'], 13);
        $rsReuniao->DtFechamento = ConverteData($dados['DtFechamento'], 13);
        $rsReuniao->Mecanismo = $dados['Mecanismo'];
        $rsReuniao->idUsuario = $dados['Mecanismo'];

        if ($rsReuniao->save()) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo atualizarreuniao()


    public function pegaTotal($where=array(), $order=array())
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

        // adicionando clausulas where
        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        // adicionando linha order ao select
        $slct->order($order);

        $rows = $this->fetchAll($slct);
        return $rows->count();
    }


    public static function salvareuniao($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("SAC.dbo.tbReuniao", $dados);
                
        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo salvareuniao()
}
