<?php

class ConsultarDadosProjetoDAO extends Zend_Db_Table
{

    /** @todo verificar futuramente as retiradas do dbo do sql */
    public static function obterDadosProjeto($dados = array())
    {
        $retorno = false;
        if ($dados['idPronac']) {
            $table = Zend_Db_Table::getDefaultAdapter();

            $select = $table->select()
                ->from(
                    array('p' => 'Projetos'),
                    array('IdPRONAC',
                        'idProjeto',
                        'CgcCPf',
                        'ResumoProjeto',
                        'NomeProjeto',
                        'UfProjeto',
                        'ProvidenciaTomada',
                        new Zend_Db_Expr('p.AnoProjeto+p.Sequencial AS NrProjeto'),
                        new Zend_Db_Expr('SAC.dbo.fnCustoProjeto (p.AnoProjeto,p.Sequencial) AS ValorCaptado'),
                        new Zend_Db_Expr('Nome as Proponente,sac.dbo.fnFormataProcesso(p.idPronac) AS Processo'),
                        new Zend_Db_Expr('p.Situacao + \' - \' + si.Descricao AS Situacao'),
                        new Zend_Db_Expr('SAC.dbo.fnNomeUsuario(idUsuarioEmissor)  AS Emissor'),
                        new Zend_Db_Expr('SAC.dbo.fnNomeUsuario(idUsuarioReceptor) AS Receptor'),
                        new Zend_Db_Expr('TABELAS.dbo.fnEstruturaOrgao(p.Orgao,0) AS Origem'),
                        new Zend_Db_Expr('convert(varchar(10),DtSituacao,103) AS DtSituacao'),
                        new Zend_Db_Expr('isnull(sac.dbo.fnValorDaProposta(idProjeto)'),
                        new Zend_Db_Expr('SAC.dbo.fnValorSolicitado(p.AnoProjeto,p.Sequencial)) AS ValorProposta'),
                        new Zend_Db_Expr('SAC.dbo.fnValorSolicitado(p.AnoProjeto,p.Sequencial) AS ValorSolicitado'),
                        new Zend_Db_Expr('SAC.dbo.fnOutrasFontes(p.idPronac) AS OutrasFontes'),
                        new Zend_Db_Expr('tabelas.dbo.fnEstruturaOrgao(p.Orgao,0) AS Origem'),
                        new Zend_Db_Expr('p.Situacao AS codSituacao'),
                        new Zend_Db_Expr('
                            CASE
                                WHEN p.Mecanismo =\'2\' OR p.Mecanismo =\'6\' THEN sac.dbo.fnValorAprovadoConvenio(p.AnoProjeto,p.Sequencial)
                                ELSE sac.dbo.fnValorAprovado(p.AnoProjeto,p.Sequencial)
                            END AS ValorAprovado,
                            CASE
                                WHEN p.Mecanismo =\'2\' OR p.Mecanismo =\'6\' THEN sac.dbo.fnValorAprovadoConvenio(p.AnoProjeto,p.Sequencial)
                                ELSE sac.dbo.fnValorAprovado(p.AnoProjeto,p.Sequencial) + sac.dbo.fnOutrasFontes(p.idPronac)
                            END AS ValorProjeto,
                            CASE
                                WHEN Enquadramento = \'1\' THEN \'Artigo 26\'
                                WHEN Enquadramento = \'2\' THEN \'Artigo 18\'
                                ELSE \'N&atilde;o enquadrado\'
                            END as Enquadramento')
                         ),
                    'SAC.dbo'
                )
                ->joinLeft(
                    array('e' => 'Enquadramento'),
                    'p.idPronac = e.idPronac',
                    array(''),
                    'SAC.dbo'
                )
                ->joinInner(
                    array('i' => 'Interessado'),
                    'p.CgcCPf = i.CgcCPf',
                    array(''),
                    'SAC.dbo'
                )
                ->joinInner(
                    array('a' => 'Area'),
                    'p.Area = a.Codigo',
                    array(new Zend_Db_Expr('a.Descricao as Area')),
                    'SAC.dbo'
                )
                ->joinInner(
                    array('s' => 'Segmento'),
                    'p.Segmento = s.Codigo',
                    array(new Zend_Db_Expr('s.Descricao AS Segmento')),
                    'SAC.dbo'
                )
                ->joinInner(
                    array('m' => 'Mecanismo'),
                    'p.Mecanismo = m.Codigo',
                    array(new Zend_Db_Expr('m.Descricao as Mecanismo')),
                    'SAC.dbo'
                )
                ->joinInner(
                    array('si' => 'Situacao'),
                    'p.Situacao = si.Codigo',
                    array(''),
                    'SAC.dbo'
                )
                ->joinLeft(
                    array('h' => 'vwTramitarProjeto'),
                    'p.idPronac = h.idPronac',
                    array('Destino', 'DtTramitacaoEnvio', 'dtTramitacaoRecebida', new Zend_Db_Expr('h.Situacao AS Estado'), 'meDespacho'),
                    'SAC.dbo'
                )
                ->where('p.IdPRONAC = ?', $dados['idPronac']);

            try {
                $table->setFetchMode(Zend_DB::FETCH_OBJ);
            } catch (Zend_Exception_Db $e) {
                $this->view->message = "Falha ao buscar projeto: " . $e->getMessage();
            }

            $retorno = $table->fetchAll($select);
        }
        return $retorno;
    }
}
