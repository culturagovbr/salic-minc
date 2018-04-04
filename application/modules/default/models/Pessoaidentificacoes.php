<?php

/**
 * Description of Pessoa_Identificacoes
 *
 * @author tisomar
 */
class Pessoaidentificacoes extends MinC_Db_Table_Abstract
{
    protected $_banco = "TABELAS";
    protected $_name = "Pessoa_Identificacoes";
    protected $_schema = 'TABELAS';

    public function pesquisarPessoasDados($where=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
        array('pi'=>$this->_name),
            array(
            'pi.pid_pessoa',
            'pi.pid_identificacao'
            )
        );
        $select->joinLeft(
             array('pd'=>'Pessoa_Dados'),
             'pi.pid_pessoa = pd.pdd_pessoa',
             array('pd.pdd_dado'),
             'Tabelas.dbo'
         );
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }

    public function salvarDados($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblPessoasIdentificacoes = new PessoaIdentificacoes();

        $tmpTblPessoasIdentificacoes = $tmpTblPessoasIdentificacoes->createRow();

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['pid_pessoa'])) {
            $tmpTblPessoasIdentificacoes->pid_pessoa = $dados['pid_pessoa'];
        }
        if (isset($dados['pid_meta_dado'])) {
            $tmpTblPessoasIdentificacoes->pid_meta_dado = $dados['pid_meta_dado'];
        }
        if (isset($dados['pid_sequencia'])) {
            $tmpTblPessoasIdentificacoes->pid_sequencia = $dados['pid_sequencia'];
        }
        if (isset($dados['pid_identificacao'])) {
            $tmpTblPessoasIdentificacoes->pid_identificacao = $dados['pid_identificacao'];
        }

        $id = $tmpTblPessoasIdentificacoes->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    /*===========================================================================*/
    /*====================== ABAIXO - METODOS DA CNIC ===========================*/
    /*===========================================================================*/

    public function buscarAssinatura()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('pi' => $this->_name),
                array('pi.pid_identificacao')
        );
        $select->joinInner(
                array('pf'=>'PessoasXFuncoes'),
                'pi.pid_pessoa = pf.pxf_pessoa',
                array('pf.pxf_funcao')

        );
        $select->joinInner(
                array('f'=>'Funcoes'),
                'pf.pxf_funcao = f.fun_codigo',
                array('f.fun_descricao')
        );
        $select->where('f.fun_status = ?', 1);
        $select->where('pi.pid_meta_dado = ?', 1);

        return $this->fetchAll($select);
    }
}
