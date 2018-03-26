<?php

class spValidarApresentacaoDeProjeto extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'dbo.spValidarApresentacaoDeProjeto';

    public function validarEnvioProposta($idPreProjeto)
    {
        $select = new Zend_Db_Expr(" exec SAC.dbo.spValidarApresentacaoDeProjeto $idPreProjeto ");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchAll($select);
    }

    public function paChecklistDeEnvioDeProposta($idPreProjeto)
    {
        $select = new Zend_Db_Expr(" exec SAC.dbo.paChecklistDeEnvioDeProposta $idPreProjeto ");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchAll($select);
    }

    public function checklistEnvioProposta($idPreProjeto)
    {
        $validacao = new stdClass();
        $listaValidacao = array();

        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('PreProjeto'), 'idAgente', 'sac.dbo')
            ->where('idPreProjeto = ?', $idPreProjeto);
        $resultAgente = $db->fetchAll($sql);
        if (count($resultAgente) > 0) {
            $idAgente = $resultAgente[0]->idAgente;
        }

        $sql = $db->select()
            ->from(array('tbMovimentacao'), '*', 'sac.dbo')
            ->where('idProjeto = ?', $idPreProjeto)
            ->where('Movimentacao <> 95')
            ->where('stEstado = 0')
            ->limit(1);

        $movimentacao = $db->fetchAll($sql);

        if (!empty($movimentacao)) {
            $validacao->Descricao = '<font color=blue><b>A PROPOSTA CULTURAL ENCONTRA-SE NO MINISTÉRIO DA CULTURA.</b></font>';
            $validacao->Observacao = '';
            $listaValidacao[] =  clone($validacao);
        } else {
            $sql = $db->select()
                ->from(array('tbAvaliacaoProposta'), '*', 'sac.dbo')
                ->where('idProjeto = ?', $idPreProjeto)
                ;

            $avaliacaoProposta = $db->fetchAll($sql);
            if ((date('m') == 12 || date('m') == 1) && empty($avaliacaoProposta)) {
                $validacao->Descricao = 'Conforme Art 9º da Instrução Normativa nº 1, de 24 de junho de 2013, nenhuma proposta poderá ser enviada ao MinC nos meses de DEZEMBRO e JANEIRO!';
                $validacao->Observacao = '<font color=red><b>IMPEDIMENTO</b></font>';
                $listaValidacao[] =  clone($validacao);
            } else {
                $sql = $db->select()
                    ->from(array('v' => 'vCadastrarProponente'), 'v.*', 'sac.dbo')
                    ->join(array('p' => 'PreProjeto'), 'v.idAgente = p.idAgente', null, 'sac.dbo')
                    ->where('idpreprojeto = ?', $idPreProjeto)
                    ->where('Correspondencia = 1')
                    ->limit(1)
                    ;
                $vCadastrarProponente = $db->fetchAll($sql);

                //VERIFICAR AS INFORMACOES DO PROPONENTE
                if (empty($vCadastrarProponente)) {
                    $validacao->Descricao = 'Dados cadastrais do proponente inexistente ou não há endereço para correspondência selecionado.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao = 'Dados cadastrais do proponente lançado.';
                    $validacao->Observacao = 'OK';
                    $listaValidacao[] =  clone($validacao);
                }
                //VERIFICAR A REGULARIDADE DO PROPONENTE
                $sql = $db->select()
                        ->from(array('v' => 'vCadastrarProponente'), 'v.*', 'sac.dbo')
                        ->join(array('p' => 'PreProjeto'), 'v.idAgente = p.idAgente', null, 'sac.dbo')
                        ->join(array('i' => 'Inabilitado '), 'v.CnpjCpf=i.CgcCpf', null, 'sac.dbo')
                        ->where('idpreprojeto = ?', $idPreProjeto)
                        ->where('v.CnpjCpf=i.CgcCpf')
                        ->where("Habilitado='N'")
                        ->limit(1)
                        ;

                $regularidadeProponente = $db->fetchAll($sql);
                if (!empty($regularidadeProponente)) {
                    $validacao->Descricao ='Proponente em situação IRREGULAR no Ministério da Cultura.';
                    $validacao->Observacao =  'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao ='Proponente em situação REGULAR no Ministério da Cultura.';
                    $validacao->Observacao =  'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //-- VERIFICAR SE HA OS EMAILS DO PROPONENTE CADASTRADOS
                $sql = $db->select()
                        ->from(array('v' => 'Internet'), 'v.*', 'agentes.dbo')
                        ->join(array('p' => 'PreProjeto'), 'v.idAgente=p.idAgente', null, 'sac.dbo')
                        ->where('idpreprojeto= ?', $idPreProjeto)
                        ->where('Status=1')
                        ->limit(1)
                        ;
                $verificarEmail = $db->fetchAll($sql);
                if (empty($verificarEmail)) {
                    $validacao->Descricao ='E-mail do proponente inexistente';
                    $validacao->Observacao =  'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao ='E-mail do proponente cadastrado.';
                    $validacao->Observacao =  'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //-- NO CASO DE PESSOA FISICA, VERIFICAR O LANCAMENTO DA DATA DE NASCIMENTO
                $sql = $db->select()
                        ->from(array('v' => 'Agentes'), 'TipoPessoa', 'agentes.dbo')
                        ->where('idAgente = ?', $idAgente)
                        ;

                $resultTipoPessoa = $db->fetchAll($sql);
                if (count($resultTipoPessoa) > 0) {
                    $tipoPessoa = $resultTipoPessoa[0]->TipoPessoa;
                }
                if ($tipoPessoa == 0) {
                    $sql = $db->select()
                            ->from(array('tbAgenteFisico'), 'DtNascimento', 'agentes.dbo')
                            ->where('idagente = ?', $idAgente)
                            ;

                    $dataNasc = $db->fetchAll($sql);

                    if (empty($dataNasc)) {
                        $validacao->Descricao ='Data de Nascimento inexistente.';
                        $validacao->Observacao =  'PENDENTE';
                        $listaValidacao[] = clone($validacao);
                    } else {
                        $validacao->Descricao ='Data de Nascimento cadastrada.';
                        $validacao->Observacao =  'OK';
                        $listaValidacao[] =  clone($validacao);
                    }
                }

                //-- NO CASO DE PESSOA JURIDICA, VERIFICAR O LANCAMENTO DA NATUREZA DO PROPONENTE
                if ($tipoPessoa == 1) {
                    $sql = $db->select()
                            ->from(array('n' => 'Natureza'), '*', 'agentes.dbo')
                            ->join(array('p' => 'PreProjeto'), 'n.idAgente=p.idAgente', '*', 'sac.dbo')
                            ->where('idpreprojeto = ?', $idPreProjeto)
                            ->limit(1)
                            ;

                    $natureza = $db->fetchAll($sql);
                    if (empty($natureza)) {
                        $validacao->Descricao = 'Natureza do proponente.';
                        $validacao->Observacao =  'PENDENTE';
                        $listaValidacao[] =  clone($validacao);
                    } else {
                        $validacao->Descricao = 'Natureza do proponente cadastrada.';
                        $validacao->Observacao =  'OK';
                        $listaValidacao[] =  clone($validacao);
                    }

                    //-- VERIFICAR SE HA DIRIGENTE CADASTRADO
                    $sql = $db->select()
                            ->from(array('v' => 'vCadastrarDirigente'), '*', 'sac.dbo')
                            ->join(array('p' => 'PreProjeto'), 'v.idVinculoPrincipal=p.idAgente', '*', 'sac.dbo')
                            ->where('idPreProjeto= ?', $idPreProjeto)
                            ;

                    $dirigenteCadastrado = $db->fetchAll($sql);
                    if (empty($dirigenteCadastrado)) {
                        $validacao->Descricao = 'Cadastro de Dirigente.';
                        $validacao->Observacao = 'PENDENTE';
                        $listaValidacao[] =  clone($validacao);
                    } else {
                        $validacao->Descricao ='Cadastro de Dirigente lançado.'  ;
                        $validacao->Observacao =  'OK';
                        $listaValidacao[] =  clone($validacao);
                    }
                }

                //-- VERIFICAR SE O LOCAL DE REALIZACAO ESTA CADASTRADO
                //IF NOT EXISTS(SELECT TOP 1 * FROM Abrangencia WHERE idProjeto = @idProjeto)

                $sql = $db->select()
                        ->from(array('Abrangencia'), '*', 'sac.dbo')
                        ->where('idProjeto = ?', $idPreProjeto)
                        ->limit(1);

                $local = $db->fetchAll($sql);

                if (empty($local)) {
                    $validacao->Descricao = 'O Local de realização da proposta não foi preenchido.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao = 'Local de realização da proposta cadastrada.';
                    $validacao->Observacao = 'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //-- VERIFICAR SE O PLANO DE DIVULGACAO ESTA PREENCHIDO
                $sql = $db->select()
                        ->from(array('PlanoDeDivulgacao'), '*', 'sac.dbo')
                        ->where('idProjeto = ?', $idPreProjeto)
                        ->limit(1);

                $planoDivulgacao = $db->fetchAll($sql);

                if (empty($planoDivulgacao)) {
                    $validacao->Descricao = 'O Plano Básico de Divulgação não foi preenchido.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao = 'Plano Básico de Divulgação cadastrado.';
                    $validacao->Observacao = 'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //-- VERIFICAR SE EXISTE NO MINIMO 90 DIAS ENTRE A DATA DE ENVIO E O INICIO DO PERIODO DE EXECUCAO DO PROJETO
                $sql = $db->select()
                        ->from(array('PreProjeto'), '*', 'sac.dbo')
                        ->where('idPreProjeto = ?', $idPreProjeto)
                        ->where('DATEDIFF(DAY,GETDATE(),DtInicioDeExecucao) < 90')
                        ->limit(1);

                $minimo90 = $db->fetchAll($sql);

                if (!empty($minimo90)) {
                    $validacao->Descricao = 'A diferença em dias entre a data de envio do projeto ao MinC e a data de in&iacute;cio de execu&ccedil;&atilde;o do projeto est&aacute; menor do que 90 dias.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao = 'Prazo de in&iacute;cio de execu&ccedil;&atilde;o maior do que 90 dias.';
                    $validacao->Observacao = 'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //-- VERIFICAR SE O PLANO DE DISTRIBUICAO DO PRODUTO ESTA PREENCHIDO
                $sql = $db->select()
                        ->from(array('PlanoDistribuicaoProduto'), '*', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->limit(1);

                $planoDistribuicao = $db->fetchAll($sql);
                if (empty($planoDistribuicao)) {
                    $validacao->Descricao = 'O Plano Distribuição de Produto não foi preenchido.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao = 'O Plano Distribuição de Produto cadastrado.';
                    $validacao->Observacao = 'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //--Verificar a existencia do produto principal
                //SELECT @QtdeOutros=stPrincipal FROM PlanoDistribuicaoProduto  WHERE idProjeto = @idProjeto and stPrincipal = 1
                $sql = $db->select()
                        ->from(array('PlanoDistribuicaoProduto'), 'stPrincipal', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where('stPrincipal = 1')
                        ;

                $quantidade = count($db->fetchAll($sql));

                if ($quantidade = 0) {
                    $validacao->Descricao = 'Não há produto principal selecionado na proposta.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } elseif ($quantidade > 1) {
                    $validacao->Descricao = 'Só poderá haver um produto principal em cada proposta, a sua está com mais de um produto.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                }

                //-- VERIFICAR SE EXISTE NA PLANILHA ORCAMENTARIA ITENS DA FONTE INCENTIVO FISCAL FEDERAL.
                $sql = $db->select()
                        ->from(array('tbPlanilhaProposta'), '*', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where('FonteRecurso = 109')
                        ->limit(1)
                        ;

                $planilhaOrcamentaria = $db->fetchAll($sql);

                if (empty($planilhaOrcamentaria)) {
                    $validacao->Descricao = 'Não existe item orçamentário referente a fonte de recurso - Incentivo Fiscal Federal.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao = 'Itens Orçamentários com fontes de recurso - incentivo fiscal federal cadastrados.';
                    $validacao->Observacao = 'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //-- VERIFICAR SE EXISTE NA PLANILHA ORCAMENTARIA PARA CADA PRODUTO DESCRITO NO PLANO DE DISTRIBUICAO DO PRODUTO
                //IF EXISTS(SELECT * FROM PlanoDistribuicaoProduto pp WHERE idProjeto = @idProjeto and
                //NOT EXISTS(SELECT * FROM tbPlanilhaProposta pl WHERE idProjeto = @idProjeto and pp.idProduto=pl.idProduto and idProduto <> 0))
                $subSql = $db->select()
                        ->from(array('pl' => 'tbPlanilhaProposta'), '*', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where('pp.idProduto=pl.idProduto')
                        ->where('idProduto <> 0')
                        ;

                $sql = $db->select()
                        ->from(array('pp' => 'PlanoDistribuicaoProduto'), '*', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where(new Zend_Db_Expr("NOT EXISTS($subSql)"))
                        ;

                $planilhaProduto = $db->fetchAll($sql);

                if (!empty($planilhaProduto)) {
                    $validacao->Descricao = 'Existe produto cadastrado sem a respectiva planilha orcamentária cadastrada.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao = 'Todos os produtos com as respectivas planilhas orçamentárias cadastradas.';
                    $validacao->Observacao = 'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //-- VERIFICAR SE EXISTE NA PLANILHA ORCAMENTARIA PARA OS CUSTOS ADMINISTRATIVOS DO PROJETO
                $subSql = $db->select()
                        ->from(array('pl' => 'tbPlanilhaProposta'), '*', 'sac.dbo')
                        ->where('idProjeto = ?', $idPreProjeto)
                        ->where('pl.idProduto = 0')
                        ->where('idEtapa = 4')
                        ->where('idPlanilhaItem != 5249')
                        ;

                $sql = $db->select()
                        ->from(array('pp' => 'PlanoDistribuicaoProduto'), '*', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where(new Zend_Db_Expr("NOT EXISTS($subSql)"))
                        ;

                $custoAdministrativos = $db->fetchAll($sql);

                if (!empty($custoAdministrativos)) {
                    $validacao->Descricao = 'A planilha de custos administrativos do projeto não está cadastrada.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao = 'Planilha de custos administrativos cadastrada.';
                    $validacao->Observacao = 'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //--Pega o custo total do projeto
                $sql = $db->select()
                        ->from(array('tbPlanilhaProposta'), 'SUM(Quantidade * Ocorrencia * ValorUnitario) as total', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where('idEtapa <> 4')
                        ->where('FonteRecurso = 109')
                        ;

                $total = $db->fetchAll($sql);
                $total = empty($total[0]->total) ? 0 : $total[0]->total;

                //--pega o valor de custo administrativo
                $sql = $db->select()
                        ->from(array('tbPlanilhaProposta'), 'SUM(Quantidade * Ocorrencia * ValorUnitario) as total', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where('idEtapa <> 4')
                        ->where('FonteRecurso = 109')
                        ->where('idPlanilhaItem <> 5249')
                        ;

                $custoAdm = $db->fetchAll($sql);
                $custoAdm = empty($custoAdm[0]->total) ? 0 : $custoAdm[0]->total;

                if ($total != 0 && $custoAdm != 0) {
                    $resultadoPercentual = $custoAdm/$total*100;

                    if ($resultadoPercentual > 15) {
                        $validacao->Descricao = 'Custo administrativo superior a 15% do valor total do projeto.';
                        $validacao->Observacao = 'PENDENTE';
                        $listaValidacao[] =  clone($validacao);
                    } else {
                        $validacao->Descricao = 'Custo administrativo inferior a 15% do valor total do projeto.';
                        $validacao->Observacao = 'OK';
                        $listaValidacao[] =  clone($validacao);
                    }
                }
                //-- VERIFICAR O PERCENTUAL DA REMUNERACAO PARA CAPTACAO DE RECURSOS
                $sql = $db->select()
                        ->from(array('tbPlanilhaProposta'), 'SUM(Quantidade * Ocorrencia * ValorUnitario) as total', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where('FonteRecurso = 109')
                        ->where('idPlanilhaItem <> 5249')
                        ;

                $total = $db->fetchAll($sql);
                $total = empty($total[0]->total) ? 0 : $total[0]->total;

                //--pega o valor de remuneracao para captacao
                $sql = $db->select()
                        ->from(array('tbPlanilhaProposta'), 'SUM(Quantidade * Ocorrencia * ValorUnitario) as total', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where('FonteRecurso = 109')
                        ->where('idPlanilhaItem = 5249')
                        ;

                $custoAdm = $db->fetchAll($sql);
                $custoAdm = empty($custoAdm[0]->total) ? 0 : $custoAdm[0]->total;

                $resultadoPercentual = ($total == 0) ? 0 : ($custoAdm/$total *100);
                if ($resultadoPercentual > 10 || $custoAdm >100000) {
                    $validacao->Descricao = 'Remuneração para captação de recursos superior a 10% do valor total do projeto, ou superior a  R$ 100.000,00.';
                    $validacao->Observacao = 'PENDENTE';
                    $listaValidacao[] =  clone($validacao);
                } else {
                    $validacao->Descricao = 'Remuneração para captação de recursos está dentro dos parâmetros permitidos.';
                    $validacao->Observacao = 'OK';
                    $listaValidacao[] =  clone($validacao);
                }

                //-- VERIFICAR O PERCENTUAL DA DIVULGACAO E COMERCIALIZACAO
                $sql = $db->select()
                        ->from(array('tbPlanilhaProposta'), 'SUM(Quantidade * Ocorrencia * ValorUnitario) as total', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where('FonteRecurso = 109')
                        ->where('idEtapa <> 3')
                        ;

                $total = $db->fetchAll($sql);
                $total = empty($total[0]->total) ? 0 : $total[0]->total;

                //--pega o valor de remuneracao para captacao
                $sql = $db->select()
                        ->from(array('tbPlanilhaProposta'), 'SUM(Quantidade * Ocorrencia * ValorUnitario) as total', 'sac.dbo')
                        ->where('idProjeto =  ?', $idPreProjeto)
                        ->where('FonteRecurso = 109')
                        ->where('idEtapa = 3')
                        ;

                $custoAdm = $db->fetchAll($sql);
                $custoAdm = empty($custoAdm[0]->total) ? 0 : $custoAdm[0]->total;

                //--calcula o percentual
                if ($total != 0 && $custoAdm != 0) {
                    $resultadoPercentual = $custoAdm/$total*100;
                    //IF @resultadoPercentual > 20
                    if ($resultadoPercentual > 20) {
                        $validacao->Descricao = 'Divulgação / Comercialização superior a 20% do valor total do projeto.';
                        $validacao->Observacao = 'PENDENTE';
                        $listaValidacao[] =  clone($validacao);
                    } else {
                        $validacao->Descricao = 'Divulgação / Comercialização está dentro dos parâmetros permitidos.';
                        $validacao->Observacao = 'OK';
                        $listaValidacao[] =  clone($validacao);
                    }
                }
            }
        }

        //SELECT @IdProjeto = IdPreProjeto, @Usuario = idUsuario FROM PreProjeto WHERE idPreProjeto = @IdProjeto
        $sql = $db->select()
            ->from(array('PreProjeto'), 'idUsuario', 'sac.dbo')
            ->where('idPreProjeto =  ?', $idPreProjeto)
            ;
        $resultUsuario = $db->fetchAll($sql);
        if (count($resultUsuario) > 0) {
            $usuario = $resultUsuario[0]->idUsuario;
        }

        $validado= true;
        foreach ($listaValidacao as $valido) {
            if ($valido->Observacao == 'PENDENTE') {
                $validado = false;
                break;
            }
        }
        //var_dump($validado);die;

        if ($validado) {
            //INSERT INTO tbMovimentacao
            //(idProjeto,Movimentacao,DtMovimentacao,stEstado,Usuario)
            //VALUES (@IdProjeto,96,getdate(),0,@Usuario)
            $insert = $db->insert('sac.dbo.tbMovimentacao', array($idPreProjeto, 96, new Zend_Db_Expr('getdate()'), 0,$usuario));

            $validacao->Descricao = '<font color=blue><b>A PROPOSTA CULTURAL FOI ENCAMINHADA COM SUCESSO AO MINISTÉRIO DA CULTURA.</b></font>';
            $validacao->Observacao = 'OK';
            $listaValidacao[] =  clone($validacao);
        } else {
            $validacao->Descricao = '<font color=red><b> A PROPOSTA CULTURAL NÃO FOI ENVIADA AO MINISTÉRIO DA CULTURA DEVIDO ÀS PENDÊNCIAS ASSINALADAS ACIMA.</b></font>';
            $validacao->Observacao = '';
            $listaValidacao[] =  clone($validacao);
        }

        return $listaValidacao;
    }
}
