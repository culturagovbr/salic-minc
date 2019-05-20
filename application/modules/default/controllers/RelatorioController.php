<?php

/**
 * Controller Disvincular Agentes
 * @author Equipe RUP - Politec
 * @since 07/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 */

class RelatorioController extends MinC_Controller_Action_Abstract
{
    private $getIdUsuario = 0;

    public function init()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        if (empty($auth->getIdentity()->usu_codigo)) {
            $this->redirect(Zend_Controller_Front::getInstance()->getBaseUrl());
        }

        // autentica��o e permiss�es zend (AMBIENTE MINC)
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 90; // Protocolo - Documento
        $PermissoesGrupo[] = 91; // Protocolo - Recebimento
        $PermissoesGrupo[] = 92; // Tec. de Admissibilidade
        $PermissoesGrupo[] = 93; // Coordenador - Geral de An�lise (Ministro)
        $PermissoesGrupo[] = 94; // Parecerista
        $PermissoesGrupo[] = 96;  // Consulta Gerencial
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 103; // Coord. de Analise
        $PermissoesGrupo[] = 104; // Protocolo - Envio / Recebimento
        $PermissoesGrupo[] = 110; // Tec. de Analise
        $PermissoesGrupo[] = 114; // Coord. de Editais
        $PermissoesGrupo[] = 115; // Atendimento Representacoes
        $PermissoesGrupo[] = 119; // Presidente da CNIC
        $PermissoesGrupo[] = 120; // Coord. da CNIC
        $PermissoesGrupo[] = 121; // Tec. de Acompanhamento
        $PermissoesGrupo[] = 122; // Coord. de Acompanhamento
        $PermissoesGrupo[] = 123; // Coord. Geral de Acompanhamento
        $PermissoesGrupo[] = 124; // Tec. de Presta��o de Contas
        $PermissoesGrupo[] = 125; // Coord. de Presta��o de Contas
        $PermissoesGrupo[] = 126; // Coord. Geral de Presta��o de Contas
        $PermissoesGrupo[] = 127; // Coord. Geral de An�lise
        $PermissoesGrupo[] = 128; // Tec. de Portaria
        $PermissoesGrupo[] = 131; // Coord. de Admissibilidade
        $PermissoesGrupo[] = 132; // Chefe de Divis�o
        $PermissoesGrupo[] = 135; // Tec. De Fiscaliza��o
        $PermissoesGrupo[] = 138; // Coord. de Avalia��o
        $PermissoesGrupo[] = 139; // Tec. de Avalia��o
        $PermissoesGrupo[] = 151; // Coord. de Avalia��o
        $PermissoesGrupo[] = 148; // Tec. de Avalia��o
        parent::perfil(1, $PermissoesGrupo);

        parent::init();
    }

    public function indexAction()
    {
        $this->redirect("relatorio/proposta");
    }


    public function relatorioprojetopareceristaAction()
    {
        $tblUsuario = new Autenticacao_Model_DbTable_Usuario();
        $rsUsuario = $tblUsuario->buscarOrgao($_SESSION['Zend_Auth']['storage']->usu_orgao);
        $this->view->idOrgao = $rsUsuario->org_codigo;
        $this->view->idOrgaoSuperior = $rsUsuario->org_superior;
    }

    public function buscarorgaoAction()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o

        $idusuario = $auth->getIdentity()->usu_codigo;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;

        $this->view->idUsuarioLogado = $idusuario;
    }


    public function propostaAction()
    {
        $tblArea = new Area();
        $rsArea = $tblArea->buscar(array('Codigo != ?' => 7), array("Descricao ASC"));
        $this->view->areas = $rsArea;

        $tblUf = new Uf();
        $rsUf = $tblUf->buscar(array(), array("Descricao ASC"));
        $this->view->ufs = $rsUf;
    }

    public function gerarPdfNewAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        //$post = Zend_Registry::get('post');

        $pdf = new PDFCreator($_POST['html']);

        $pdf->gerarPdf();
    }

    public function resultadoPropostaAction()
    {
        $this->intTamPag = 30;

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            if ($campo == 4) {
                $order = array("4 ASC", "6 ASC", "8 ASC", "9 ASC");
            } else {
                $order = array($campo." ".$ordem);
            }
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //idPreProjeto
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        $where = array();
        $having = array();

        if (isset($get->proposta) && !empty($get->proposta)) {
            $where['p.idPreProjeto = ?'] = $get->proposta;
            $this->view->proposta = $get->proposta;
        }
        if (isset($get->nomeProposta) && !empty($get->nomeProposta)) {
            $where['p.nomeProjeto like (?)'] = "%".$get->nomeProposta."%";
            $this->view->nomeProposta = $get->nomeProposta;
        }
        if (isset($get->nomeProponente) && !empty($get->nomeProponente)) {
            $where['nm.Descricao like (?)'] = "%".$get->nomeProponente."%";
            $this->view->nomeProponente = $get->nomeProponente;
        }
        if (isset($get->cpfcnpj) && !empty($get->cpfcnpj)) {
            $where['ag.CNPJCPF = ?'] = Mascara::delMaskCPFCNPJ($get->cpfcnpj);
            $this->view->cpfcnpj = $get->cpfcnpj;
        }
        if (isset($get->area) && !empty($get->area)) {
            $where['pdp.Area = ?'] = $get->area;
            $this->view->area = $get->area;
        }
        if (isset($get->segmento) && !empty($get->segmento)) {
            $where['pdp.Segmento = ?'] = $get->segmento;
            $this->view->segmento = $get->segmento;
        }
        if (isset($get->uf) && !empty($get->uf)) {
            $where['ab.idUF = ?'] = $get->uf;
            $this->view->uf = $get->uf;
        }
        if (isset($get->municipio) && !empty($get->municipio)) {
            $where['ab.idMunicipioIBGE = ?'] = $get->municipio;
            $this->view->municipio = $get->municipio;
        }
        if ($get->valor != "" && $get->valor2 != "") {
            $having["SUM(Quantidade*Ocorrencia*ValorUnitario) >= ?"] = str_replace(",", ".", str_replace(".", "", $get->valor));
            $having["SUM(Quantidade*Ocorrencia*ValorUnitario) <= ?"] = str_replace(",", ".", str_replace(".", "", $get->valor2));
            $this->view->valor = $get->valor;
            $this->view->valor2 = $get->valor2;
        } elseif ($get->valor != "") {
            $having["SUM(Quantidade*Ocorrencia*ValorUnitario) = ?"] = str_replace(",", ".", str_replace(".", "", $get->valor));
            $this->view->valor = $get->valor;
        } elseif ($get->valor2 != "") {
            $having["SUM(Quantidade*Ocorrencia*ValorUnitario) = ?"] = str_replace(",", ".", str_replace(".", "", $get->valor2));
            $this->view->valor2 = $get->valor2;
        }
        if (isset($get->estado) && !empty($get->estado)) {
            switch ($get->estado) {
                case 'construcao':
                    $where['p.stEstado = ?'] = 1;
                    $where['m.Movimentacao = ?'] = 95;
                    $where['m.stEstado = ?'] = 0;
                    $where['x.ConformidadeOK IS NULL'] = '';
                    $where['x.stEstado IS NULL'] = '';
                    break;
                case 'diligenciada':
                    $where['p.stEstado = ?'] = 1;
                    $where['m.Movimentacao = ?'] = 95;
                    $where['m.stEstado = ?'] = 0;
                    $where['x.ConformidadeOK = ?'] = 0;
                    $where['x.stEstado = ?'] = 0;
                    $having["(SELECT TOP 1 idTecnico FROM (
                                SELECT idTecnico, convert(varchar(30),DtAvaliacao, 120 ) as DtAvaliacao
                                FROM SAC.dbo.tbAvaliacaoProposta tba
                                INNER JOIN tabelas.dbo.Usuarios u on (tba.idTecnico = u.usu_codigo)
                                WHERE ConformidadeOK < 9 AND tba.idProjeto = p.idPreProjeto
                                UNION ALL
                                SELECT 0,convert(varchar(30),DtMovimentacao, 120 ) as DtMovimentacao
                                FROM SAC.dbo.tbMovimentacao
                                WHERE Movimentacao=96 AND idProjeto = p.idPreProjeto
                            ) as slctPrincipal
                            ORDER BY convert(varchar(30),DtAvaliacao, 120 ) DESC) != ?"] = 0;
                    break;
                case 'respondida':
                    $where['p.stEstado = ?'] = 1;
                    $where['m.Movimentacao = ?'] = 96;
                    $where['m.stEstado = ?'] = 0;
                    $where['x.ConformidadeOK = ?'] = 0;
                    $where['x.stEstado = ?'] = 0;
                    $having["(SELECT TOP 1 idTecnico FROM (
                                SELECT idTecnico, convert(varchar(30),DtAvaliacao, 120 ) as DtAvaliacao
                                FROM SAC.dbo.tbAvaliacaoProposta tba
                                INNER JOIN tabelas.dbo.Usuarios u on (tba.idTecnico = u.usu_codigo)
                                WHERE ConformidadeOK < 9 AND tba.idProjeto = p.idPreProjeto
                                UNION ALL
                                SELECT 0,convert(varchar(30),DtMovimentacao, 120 ) as DtMovimentacao
                                FROM SAC.dbo.tbMovimentacao
                                WHERE Movimentacao=96 AND idProjeto = p.idPreProjeto
                            ) as slctPrincipal
                            ORDER BY convert(varchar(30),DtAvaliacao, 120 ) DESC) = ?"] = 0;
                    break;
                case 'enviada':
                    $where['p.stEstado = ?'] = 1;
                    $where['m.Movimentacao == ?'] = 96;
                    $where['m.stEstado = ?'] = 0;
                    $where['x.ConformidadeOK = ?'] = 9;
                    $where['x.stEstado = ?'] = 0;
                    break;
                case 'arquivada':
                    $where['p.stEstado = ?'] = 0;
                    break;
            }
            $this->view->estado = $get->estado;
        }

        $Proposta = new Proposta_Model_DbTable_PreProjeto();
        $total = $Proposta->relatorioPropostas($where, $having, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $Proposta->relatorioPropostas($where, $having, $order, $tamanho, $inicio);
        $paginacao = array(
            "pag"=>$pag,
            "qtde"=>$this->intTamPag,
            "campo"=>$campo,
            "ordem"=>$ordem,
            "ordenacao"=>$ordenacao,
            "novaOrdem"=>$novaOrdem,
            "total"=>$total,
            "inicio"=>($inicio+1),
            "fim"=>$fim,
            "totalPag"=>$totalPag,
            "Itenspag"=>$this->intTamPag,
            "tamanho"=>$tamanho
        );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function imprimirRelatorioPropostaAction()
    {
        $this->intTamPag = 30;

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            if ($campo == 4) {
                $order = array("4 ASC", "6 ASC", "8 ASC", "9 ASC");
            } else {
                $order = array($campo." ".$ordem);
            }
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //idPreProjeto
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('post');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        $where = array();
        $having = array();

        if (isset($get->proposta) && !empty($get->proposta)) {
            $where['p.idPreProjeto = ?'] = $get->proposta;
            $this->view->proposta = $get->proposta;
        }
        if (isset($get->nomeProposta) && !empty($get->nomeProposta)) {
            $where['p.nomeProjeto like (?)'] = "%".$get->nomeProposta."%";
            $this->view->nomeProposta = $get->nomeProposta;
        }
        if (isset($get->nomeProponente) && !empty($get->nomeProponente)) {
            $where['nm.Descricao like (?)'] = "%".$get->nomeProponente."%";
            $this->view->nomeProponente = $get->nomeProponente;
        }
        if (isset($get->cpfcnpj) && !empty($get->cpfcnpj)) {
            $where['ag.CNPJCPF = ?'] = Mascara::delMaskCPFCNPJ($get->cpfcnpj);
            $this->view->cpfcnpj = $get->cpfcnpj;
        }
        if (isset($get->area) && !empty($get->area)) {
            $where['pdp.Area = ?'] = $get->area;
            $this->view->area = $get->area;
        }
        if (isset($get->segmento) && !empty($get->segmento)) {
            $where['pdp.Segmento = ?'] = $get->segmento;
            $this->view->segmento = $get->segmento;
        }
        if (isset($get->uf) && !empty($get->uf)) {
            $where['ab.idUF = ?'] = $get->uf;
            $this->view->uf = $get->uf;
        }
        if (isset($get->municipio) && !empty($get->municipio)) {
            $where['ab.idMunicipioIBGE = ?'] = $get->municipio;
            $this->view->municipio = $get->municipio;
        }
        if ($get->valor != "" && $get->valor2 != "") {
            $having["SUM(Quantidade*Ocorrencia*ValorUnitario) >= ?"] = str_replace(",", ".", str_replace(".", "", $get->valor));
            $having["SUM(Quantidade*Ocorrencia*ValorUnitario) <= ?"] = str_replace(",", ".", str_replace(".", "", $get->valor2));
            $this->view->valor = $get->valor;
            $this->view->valor2 = $get->valor2;
        } elseif ($get->valor != "") {
            $having["SUM(Quantidade*Ocorrencia*ValorUnitario) = ?"] = str_replace(",", ".", str_replace(".", "", $get->valor));
            $this->view->valor = $get->valor;
        } elseif ($get->valor2 != "") {
            $having["SUM(Quantidade*Ocorrencia*ValorUnitario) = ?"] = str_replace(",", ".", str_replace(".", "", $get->valor2));
            $this->view->valor2 = $get->valor2;
        }
        if (isset($get->estado) && !empty($get->estado)) {
            switch ($get->estado) {
                case 'construcao':
                    $where['p.stEstado = ?'] = 1;
                    $where['m.Movimentacao = ?'] = 95;
                    $where['m.stEstado = ?'] = 0;
                    $where['x.ConformidadeOK IS NULL'] = '';
                    $where['x.stEstado IS NULL'] = '';
                    break;
                case 'diligenciada':
                    $where['p.stEstado = ?'] = 1;
                    $where['m.Movimentacao = ?'] = 95;
                    $where['m.stEstado = ?'] = 0;
                    $where['x.ConformidadeOK = ?'] = 0;
                    $where['x.stEstado = ?'] = 0;
                    $having["(SELECT TOP 1 idTecnico FROM (
                                SELECT idTecnico, convert(varchar(30),DtAvaliacao, 120 ) as DtAvaliacao
                                FROM SAC.dbo.tbAvaliacaoProposta tba
                                INNER JOIN tabelas.dbo.Usuarios u on (tba.idTecnico = u.usu_codigo)
                                WHERE ConformidadeOK < 9 AND tba.idProjeto = p.idPreProjeto
                                UNION ALL
                                SELECT 0,convert(varchar(30),DtMovimentacao, 120 ) as DtMovimentacao
                                FROM SAC.dbo.tbMovimentacao
                                WHERE Movimentacao=96 AND idProjeto = p.idPreProjeto
                            ) as slctPrincipal
                            ORDER BY convert(varchar(30),DtAvaliacao, 120 ) DESC) != ?"] = 0;
                    break;
                case 'respondida':
                    $where['p.stEstado = ?'] = 1;
                    $where['m.Movimentacao = ?'] = 96;
                    $where['m.stEstado = ?'] = 0;
                    $where['x.ConformidadeOK = ?'] = 0;
                    $where['x.stEstado = ?'] = 0;
                    $having["(SELECT TOP 1 idTecnico FROM (
                                SELECT idTecnico, convert(varchar(30),DtAvaliacao, 120 ) as DtAvaliacao
                                FROM SAC.dbo.tbAvaliacaoProposta tba
                                INNER JOIN tabelas.dbo.Usuarios u on (tba.idTecnico = u.usu_codigo)
                                WHERE ConformidadeOK < 9 AND tba.idProjeto = p.idPreProjeto
                                UNION ALL
                                SELECT 0,convert(varchar(30),DtMovimentacao, 120 ) as DtMovimentacao
                                FROM SAC.dbo.tbMovimentacao
                                WHERE Movimentacao=96 AND idProjeto = p.idPreProjeto
                            ) as slctPrincipal
                            ORDER BY convert(varchar(30),DtAvaliacao, 120 ) DESC) = ?"] = 0;
                    break;
                case 'enviada':
                    $where['p.stEstado = ?'] = 1;
                    $where['m.Movimentacao == ?'] = 96;
                    $where['m.stEstado = ?'] = 0;
                    $where['x.ConformidadeOK = ?'] = 9;
                    $where['x.stEstado = ?'] = 0;
                    break;
                case 'arquivada':
                    $where['p.stEstado = ?'] = 0;
                    //$where['p.DtArquivamento is not null'] = '';
                    break;
            }
            $this->view->estado = $get->estado;
        }

        $Proposta = new Proposta_Model_DbTable_PreProjeto();
        $total = $Proposta->relatorioPropostas($where, $having, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $Proposta->relatorioPropostas($where, $having, $order, $tamanho, $inicio);
        if (isset($get->xls) && $get->xls) {
            $html = '';
            $html .= '<table style="border: 1px">';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 16; font-weight: bold;" colspan="7">Relat�rio de Propostas - Resultado da pesquisa</td></tr>';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 10" colspan="7">Data do Arquivo: '. Data::mostraData() .'</td></tr>';
            $html .='<tr><td colspan="7"></td></tr>';

            $html .= '<tr>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">&nbsp;</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">N&ordm; Proposta</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Nome da Proposta</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">CPF / CNPJ</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Proponente</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Valor</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Estado</th>';
            $html .= '</tr>';

            $i=1;
            foreach ($busca as $v) {
                $cpfcnpj = (strlen($v->CNPJCPF) == 11) ? Mascara::addMaskCPF($v->CNPJCPF) : Mascara::addMaskCNPJ($v->CNPJCPF);

                if ($v->stEstado == 0) {
                    $estado = 'Proposta arquivada';
                } elseif ($v->stEstado == '1' && $v->Movimentacao == '95' && $v->estadoMovimentacao == '0' && $v->ConformidadeOK == '0' && $v->estadoAvaliacao == '0') {
                    $estado = 'Proposta diligenciada';
                } elseif ($v->stEstado == '1' && $v->Movimentacao == '96' && $v->estadoMovimentacao == '0' && $v->ConformidadeOK == '0' && $v->estadoAvaliacao == '0') {
                    $estado = 'Dilig�ncia respondida';
                } elseif ($v->stEstado == '1' && $v->Movimentacao == '95' && $v->estadoMovimentacao == '0' && is_null($v->ConformidadeOK) && is_null($v->estadoAvaliacao)) {
                    $estado = 'Proposta em constru��o';
                } elseif ($v->stEstado == '1' && $v->Movimentacao == '96' && $v->estadoMovimentacao == '0' && $v->ConformidadeOK == '9' && $v->estadoAvaliacao == '0') {
                    $estado = 'Enviada ao MinC p/ avalia��o';
                } else {
                    $estado = 'Enviada ao MinC p/ avalia��o';
                }

                $html .= '<tr>';
                $html .= '<td style="border: 1px dotted black;">'.$i.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->idProjeto.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->NomeProposta.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$cpfcnpj.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->Proponente.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.@number_format($v->valor, 2, ",", ".").'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$estado.'</td>';
                $html .= '</tr>';
                $i++;
            }
            $html .= '</table>';

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=Resultado_Relatorio_Propostas.ods;");
            echo $html;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->view->qtdRegistros = $total;
            $this->view->dados = $busca;
            $this->view->campo = $campo;
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        }
    }


    public function projetoAction()
    {
        $tblArea = new Area();
        $rsArea = $tblArea->buscar(array('Codigo != ?' => 7), array("Descricao ASC"));
        $this->view->areas = $rsArea;

        $tblUf = new Uf();
        $rsUf = $tblUf->buscar(array(), array("Descricao ASC"));
        $this->view->ufs = $rsUf;

        $tblMecanismo = new Mecanismo();
        $rsMecanismo = $tblMecanismo->buscar(array("Status = ?"=>"1"), array("Descricao ASC"));
        $this->view->mecanismos = $rsMecanismo;

        $tblFundoSetorial = new Verificacao();
        $rsFundoSetorial = $tblFundoSetorial->buscar(array("idTipo = ?"=>15), array("Descricao ASC"));
        $this->view->fundossetoriais = $rsFundoSetorial;

        $tblSituacao = new Situacao();
        $rsSituacao = $tblSituacao->buscar(array( "StatusProjeto = ?"=>1), array("1 ASC"));
        $this->view->situacoes = $rsSituacao;
    }

    public function resultadoProjetoAction()
    {
        $this->intTamPag = 30;

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(2); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        $where = array();

        if ((isset($get->pronac) && !empty($get->pronac))) {
            $where['pr.AnoProjeto+pr.Sequencial = ?'] = $get->pronac;
            $this->view->pronac = $get->pronac;
        }
        if ((isset($get->cnpfcpf) && !empty($get->cnpfcpf))) {
            $where['pr.CgcCpf = ?'] = retiraMascara($get->cnpfcpf);
            $this->view->cnpfcpf = $get->cnpfcpf;
        }
        if ((isset($get->nomeProjeto) && !empty($get->nomeProjeto))) {
            $where['pr.NomeProjeto like (?)'] = "%".$get->nomeProjeto."%";
            ;
            $this->view->nomeProjeto = $get->nomeProjeto;
        }
        if ((isset($get->nomeProponente) && !empty($get->nomeProponente))) {
            $where['nm.Descricao like (?)'] = "%".$get->nomeProponente."%";
            ;
            $this->view->nomeProponente = $get->nomeProponente;
        }
        if ((isset($get->area) && !empty($get->area))) {
            $where['ar.Codigo = ?'] = $get->area;
            $this->view->area = $get->area;
        }
        if ((isset($get->segmento) && !empty($get->segmento))) {
            $where['sg.Codigo = ?'] = $get->segmento;
            $this->view->segmento = $get->segmento;
        }
        if ((isset($get->mecanismo) && !empty($get->mecanismo))) {
            $where['pr.Mecanismo = ?'] = $get->mecanismo;
            $this->view->mecanismo = $get->mecanismo;
        }
        if ((isset($get->uf) && !empty($get->uf))) {
            $where['uf.CodUfIbge = ?'] = $get->uf;
            $this->view->uf = $get->uf;
        }
        if ((isset($get->municipio) && !empty($get->municipio))) {
            $where['u.idMunicipio = ?'] = $get->municipio;
            $this->view->municipio = $get->municipio;
        }
        if ((isset($get->situacao) && !empty($get->situacao))) {
            $where['pr.Situacao = ?'] = $get->situacao;
            $this->view->situacao = $get->situacao;
        }
        $where = MinC_Controller_Action_Abstract::montaBuscaData($get, "tpDtSituacao", "dtSituacao", "pr.DtSituacao", "dtSituacao_Final", $where);
        $where = MinC_Controller_Action_Abstract::montaBuscaData($get, "tpDtPublicacao", "dtPublicacao", "ap.DtPublicacaoAprovacao", "dtPublicacao_Final", $where);
        $where = MinC_Controller_Action_Abstract::montaBuscaData($get, "tpDtPortaria", "dtPortaria", "ap.DtPortariaAprovacao", "dtPortaria_Final", $where);

        if ((isset($get->dtInicioExec) && isset($get->dtFimExec) && !empty($get->dtInicioExec) && !empty($get->dtFimExec))) {
            $di = data::dataAmericana($get->dtInicioExec);
            $df = data::dataAmericana($get->dtFimExec);
            $where["pr.DtInicioExecucao BETWEEN '$di' AND '$df'"] = '';
            $where["pr.DtFimExecucao BETWEEN '$di' AND '$df'"] = '';
            $this->view->dtInicioExec = $get->dtInicioExec;
            $this->view->dtFimExec = $get->dtFimExec;
        }
        if ((isset($get->propRegular) && !empty($get->propRegular))) {
            $where['inab.Habilitado = ?'] = $get->propRegular;
            $this->view->propRegular = $get->propRegular;
        }
        if ((isset($get->planoAnual) && !empty($get->planoAnual))) {
            $where['p.stPlanoAnual = ?'] = $get->planoAnual;
            $this->view->planoAnual = $get->planoAnual;
        }
        if ((isset($get->datafixa) && !empty($get->datafixa))) {
            $where['p.stDataFixa = ?'] = $get->datafixa;
            $this->view->datafixa = $get->datafixa;
        }

        if (count($where) == 0) {
            parent::message('&Eacute; necess&aacute;rio pelo menos um argumento na pesquisa!', "/relatorio/projeto", "ERROR");
        }

        $Projetos = new Projetos();
        $total = $Projetos->relatorioProjeto($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $Projetos->relatorioProjeto($where, $order, $tamanho, $inicio);
        $paginacao = array(
            "pag"=>$pag,
            "qtde"=>$this->intTamPag,
            "campo"=>$campo,
            "ordem"=>$ordem,
            "ordenacao"=>$ordenacao,
            "novaOrdem"=>$novaOrdem,
            "total"=>$total,
            "inicio"=>($inicio+1),
            "fim"=>$fim,
            "totalPag"=>$totalPag,
            "Itenspag"=>$this->intTamPag,
            "tamanho"=>$tamanho
        );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function imprimirRelatorioAction()
    {
        $this->intTamPag = 30;

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(2); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('post');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        $where = array();

        if ((isset($get->pronac) && !empty($get->pronac))) {
            $where['pr.AnoProjeto+pr.Sequencial = ?'] = $get->pronac;
            $this->view->pronac = $get->pronac;
        }
        if ((isset($get->cnpfcpf) && !empty($get->cnpfcpf))) {
            $where['pr.CgcCpf = ?'] = retiraMascara($get->cnpfcpf);
            $this->view->cnpfcpf = $get->cnpfcpf;
        }
        if ((isset($get->nomeProjeto) && !empty($get->nomeProjeto))) {
            $where['pr.NomeProjeto like (?)'] = "%".$get->nomeProjeto."%";
            ;
            $this->view->nomeProjeto = $get->nomeProjeto;
        }
        if ((isset($get->nomeProponente) && !empty($get->nomeProponente))) {
            $where['nm.Descricao like (?)'] = "%".$get->nomeProponente."%";
            ;
            $this->view->nomeProponente = $get->nomeProponente;
        }
        if ((isset($get->area) && !empty($get->area))) {
            $where['ar.Codigo = ?'] = $get->area;
            $this->view->area = $get->area;
        }
        if ((isset($get->segmento) && !empty($get->segmento))) {
            $where['sg.Codigo = ?'] = $get->segmento;
            $this->view->segmento = $get->segmento;
        }
        if ((isset($get->mecanismo) && !empty($get->mecanismo))) {
            $where['pr.Mecanismo = ?'] = $get->mecanismo;
            $this->view->mecanismo = $get->mecanismo;
        }
        if ((isset($get->uf) && !empty($get->uf))) {
            $where['uf.CodUfIbge = ?'] = $get->uf;
            $this->view->uf = $get->uf;
        }
        if ((isset($get->municipio) && !empty($get->municipio))) {
            $where['u.idMunicipio = ?'] = $get->municipio;
            $this->view->municipio = $get->municipio;
        }
        if ((isset($get->situacao) && !empty($get->situacao))) {
            $where['pr.Situacao = ?'] = $get->situacao;
            $this->view->situacao = $get->situacao;
        }
        $where = MinC_Controller_Action_Abstract::montaBuscaData($get, "tpDtSituacao", "dtSituacao", "pr.DtSituacao", "dtSituacao_Final", $where);
        $where = MinC_Controller_Action_Abstract::montaBuscaData($get, "tpDtPublicacao", "dtPublicacao", "ap.DtPublicacaoAprovacao", "dtPublicacao_Final", $where);
        $where = MinC_Controller_Action_Abstract::montaBuscaData($get, "tpDtPortaria", "dtPortaria", "ap.DtPortariaAprovacao", "dtPortaria_Final", $where);

        if ((isset($get->dtInicioExec) && isset($get->dtFimExec) && !empty($get->dtInicioExec) && !empty($get->dtFimExec))) {
            $di = data::dataAmericana($get->dtInicioExec);
            $df = data::dataAmericana($get->dtFimExec);
            $where["pr.DtInicioExecucao BETWEEN '$di' AND '$df'"] = '';
            $where["pr.DtFimExecucao BETWEEN '$di' AND '$df'"] = '';
            $this->view->dtInicioExec = $get->dtInicioExec;
            $this->view->dtFimExec = $get->dtFimExec;
        }
        if ((isset($get->propRegular) && !empty($get->propRegular))) {
            $where['inab.Habilitado = ?'] = $get->propRegular;
            $this->view->propRegular = $get->propRegular;
        }
        if ((isset($get->planoAnual) && !empty($get->planoAnual))) {
            $where['p.stPlanoAnual = ?'] = $get->planoAnual;
            $this->view->planoAnual = $get->planoAnual;
        }
        if ((isset($get->datafixa) && !empty($get->datafixa))) {
            $where['p.stDataFixa = ?'] = $get->datafixa;
            $this->view->datafixa = $get->datafixa;
        }

        $Projetos = new Projetos();
        $total = $Projetos->relatorioProjeto($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $Projetos->relatorioProjeto($where, $order, $tamanho, $inicio);

        if (isset($get->xls) && $get->xls) {
            $colunas = 12;
            if ($campo != 12) {
                $colunas++;
            }

            $html = '';
            $html .= '<table style="border: 1px">';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 16; font-weight: bold;" colspan="'.$colunas.'">Relat�rio de Projetos - Resultado da pesquisa</td></tr>';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 10" colspan="'.$colunas.'">Data do Arquivo: '. Data::mostraData() .'</td></tr>';
            $html .='<tr><td colspan="'.$colunas.'"></td></tr>';

            $html .= '<tr>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">&nbsp;</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">PRONAC</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Nome do Projeto</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Agente</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">�rea</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Segmento</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">UF</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Munic�pio</th>';

            if ($campo != 12) {
                $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Situa��o</th>';
            }

            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Vl. Solicitado</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Vl. Aprovado</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Vl. Captado</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Habilitado</th>';
            $html .= '</tr>';

            $ds = '';
            $i=1;
            foreach ($busca as $v) {
                if ($v->Situacao != $ds && $campo == 12) {
                    $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD;" colspan="12">'.$v->Situacao.' - '.$v->dsSituacao.'</td></tr>';
                }

                $html .= '<tr>';
                $html .= '<td style="border: 1px dotted black;">'.$i.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->Pronac.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->NomeProjeto.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->NomeAgente.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->Area.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->Segmento.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->UfProjeto.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->Municipio.'</td>';

                if ($campo != 12) {
                    $html .= '<td style="border: 1px dotted black;">'.$v->Situacao.' - '.$v->dsSituacao.'</td>';
                }

                $html .= '<td style="border: 1px dotted black;">'.@number_format($v->ValorSolicitado, 2, ",", ".").'</td>';
                $html .= '<td style="border: 1px dotted black;">'.@number_format($v->ValorAprovado, 2, ",", ".").'</td>';
                $html .= '<td style="border: 1px dotted black;">'.@number_format($v->ValorCaptado, 2, ",", ".").'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->Habilitado.'</td>';
                $html .= '</tr>';

                $i++;
                $ds = $v->Situacao;
            }
            $html .= '</table>';

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=Resultado_Relatorio_Projetos.ods;");
            echo $html;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->view->qtdRegistros = $total;
            $this->view->dados = $busca;
            $this->view->campo = $campo;
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        }
    }

    public function extratorAction()
    {
        $tblArea = new Area();
        $rsArea = $tblArea->buscar(array(), array("Descricao ASC"));
        $this->view->areas = $rsArea;

        $tblUf = new Uf();
        $rsUf = $tblUf->buscar(array(), array("Descricao ASC"));
        $this->view->ufs = $rsUf;

        $tblMecanismo = new Mecanismo();
        $rsMecanismo = $tblMecanismo->buscar(array("Status = ?"=>"1"), array("Descricao ASC"));
        $this->view->mecanismos = $rsMecanismo;

        $tblFundoSetorial = new Verificacao();
        $rsFundoSetorial = $tblFundoSetorial->buscar(array("idTipo = ?"=>15));
        $this->view->fundossetoriais = $rsFundoSetorial;

        $tblSituacao = new Situacao();
        $rsSituacao = $tblSituacao->buscar(array("AreaAtuacao = ?"=>"C", "StatusProjeto = ?"=>1), array("Descricao ASC"));
        $this->view->situacoes = $rsSituacao;

        $tblOrgaos = new Orgaos();
        $rsOrgaos = $tblOrgaos->buscar(array(), array("Sigla ASC"));
        $this->view->orgaos = $rsOrgaos;
    }

    public function resultadoExtratorAction()
    {
        $this->_helper->layout->disableLayout();
        $post   = Zend_Registry::get('post');

        $tbl = new Projetos();

        //recuperando filtros do POST
        $arrBusca = array();
        if ($post->mecanismo != "") {
            $arrBusca["pr.Mecanismo = ?"] = $post->mecanismo;
        }
        if ($post->situacao != "") {
            $arrBusca["pr.Situacao = ?"] = $post->situacao;
        }
        if ($post->uf != "") {
            $arrBusca["pr.UFProjeto = ?"] = $post->uf;
        }
        if ($post->orgaoOrigem != "") {
            $arrBusca["pr.OrgaoOrigem = ?"] = $post->orgaoOrigem;
        }
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtSituacao", "dtSituacao", "pr.DtSituacao", "dtSituacao_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtProtocolo", "dtProtocolo", "pr.DtProtocolo", "dtProtocolo_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtLiberacao", "dtLiberacao", "SAC.dbo.fnDtLiberacaoConta(AnoProjeto,Sequencial)", "dtLiberacao_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtPortaria", "dtPortaria", "SAC.dbo.fnDtPortariaAprovacao(AnoProjeto,Sequencial)", "dtPortaria_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtPublicacao", "dtPublicacao", "SAC.dbo.fnDtPortariaPublicacao(AnoProjeto,Sequencial)", "dtPublicacao_Final", $arrBusca);
        $total = $tbl->extratorProjeto($arrBusca, array(), null, null);
        $total = count($total);

        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //orienta�ao da pagina no pdf
            $landscape = (sizeof($post->visaoAgente) > 4)?true:false;
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $fim = 0;

            // verifica se foi solicitado a ordena��o
            if (!empty($post->ordenacao)) {
                $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
            } else {
                $ordem = array('1 ASC');
            }

            $rs = $tbl->extratorProjeto($arrBusca, $ordem, null, null);
            $this->forward(
                'preparar-xls-pdf',
                null,
                null,
                array(
                                                                    'dados'=>$rs,
                                                                    'view'=>'relatorio/preparar-xls-pdf-extrator.phtml',
                                                                    'tipo'=> $post->tipo,
                                                                    'orientacao'=>$landscape
                                                                    )
            );
        } else {
            //controlando a paginacao
            $this->intTamPag = 10;
            $pag = 1;
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim = $inicio + $this->intTamPag;

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            // verifica se foi solicitado a ordena��o
            if (!empty($post->ordenacao)) {
                $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
            } else {
                $ordem = array('1 ASC');
            }

            $rs = $tbl->extratorProjeto($arrBusca, $ordem, $tamanho, $inicio);
        }
        $this->view->registros = $rs;
        $this->view->pag = $pag;
        $this->view->total = $total;
        $this->view->inicio = ($inicio+1);
        $this->view->fim = $fim;
        $this->view->totalPag = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function desembolsoAction()
    {
    }

    public function resultadoDesembolsoAction()
    {
        $this->_helper->layout->disableLayout();
        $post   = Zend_Registry::get('post');

        //recuperando filtros do POST
        $arrBusca = array();
        if ($post->nrEdital != "") {
            $arrBusca["idEdital = ?"] = $post->nrEdital;
        }
        if ($post->nrParcela != "") {
            $arrBusca["NrParcela = ?"] = $post->nrParcela;
        }
        if ($post->liquidado != "") {
            $arrBusca["Pagou = ?"] = $post->liquidado;
        }
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDt", "dt", "Data", "dt_Final", $arrBusca);

        //Varifica se foi solicitado a ordena��o
        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        } else {
            $ordem = array('15');
        }

        $tbl = new EditalDesembolso();

        $total = $tbl->editalDesembolsoXProjeto($arrBusca, $ordem, null, null, true);

        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //orienta�ao da pagina no pdf
            $landscape = (sizeof($post->visaoAgente) > 4)?true:false;
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $fim = 0;
            $rs = $tbl->editalDesembolsoXProjeto($arrBusca, $ordem, $tamanho, $inicio);
            $this->forward(
                'preparar-xls-pdf',
                null,
                null,
                array(
                                                                    'dados'=>$rs,
                                                                    'view'=>'relatorio/preparar-xls-pdf-desembolso.phtml',
                                                                    'tipo'=> $post->tipo,
                                                                    'orientacao'=> $landscape
                                                                    )
            );
        } else {
            //controlando a paginacao
            $this->intTamPag = 50;
            $pag = 1;
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim = $inicio + $this->intTamPag;

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

            $rs = $tbl->editalDesembolsoXProjeto($arrBusca, $ordem, $tamanho, $inicio);
        }
        $this->view->registros = $rs;
        $this->view->pag = $pag;
        $this->view->total = $total;
        $this->view->inicio = ($inicio+1);
        $this->view->fim = $fim;
        $this->view->totalPag = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function pcRegiaoUfCidadeAction()
    {
        $tblUf = new Uf();
        $rsUf = $tblUf->buscar(array(), array("Descricao ASC"));
        $this->view->ufs = $rsUf;
        $arrRegioes = array();
        foreach ($rsUf as $item) {
            $arrRegioes[] = $item->Regiao;
        }
        $arrRegioes = array_unique($arrRegioes);
        $this->view->regioes = $arrRegioes;
    }

    public function resultadoPcRegiaoUfCidadeAction()
    {
        $this->_helper->layout->disableLayout();
        $post   = Zend_Registry::get('post');

        //recuperando filtros do POST
        $arrBusca = array("modalidade = ?"=>76);
        if ($post->regiao != "") {
            $arrBusca["u.Regiao = ?"] = $post->regiao;
        }
        if ($post->uf != "") {
            $arrBusca["u.CodUfIbge = ?"] = $post->uf;
        }
        //if($post->cidade != ""){ $arrBusca["i.Cidade = ?"] = $post->cidade; }
        if (!empty($post->cidade)) {
            $tblMunicipio = new Municipios();
            $rsMunicipio = $tblMunicipio->buscar(array("idMunicipioIBGE = ?"=>$post->cidade))->current();
            $arrBusca["i.Cidade = ?"] = $rsMunicipio->Descricao;
        }

        //Varifica se foi solicitado a ordena��o
        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        } else {
            $ordem = array("5", "4", "3");
        }

        $tbl = new Projetos();

        $total = $tbl->pontoCulturaRegiaoUfCidade($arrBusca, $ordem, null, null);
        $total = count($total);

        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $fim = 0;
            $rs = $tbl->pontoCulturaRegiaoUfCidade($arrBusca, $ordem, $tamanho, $inicio);
            $this->forward(
                'preparar-xls-pdf',
                null,
                null,
                array(
                                                                    'dados'=>$rs,
                                                                    'view'=>'relatorio/preparar-xls-pdf-pc-regiao-uf-cidade.phtml',
                                                                    'tipo'=> $post->tipo
                                                                    )
            );
        } else {
            //controlando a paginacao
            $this->intTamPag = 10;
            $pag = 1;
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim = $inicio + $this->intTamPag;

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

            $rs = $tbl->pontoCulturaRegiaoUfCidade($arrBusca, $ordem, $tamanho, $inicio);
        }
        $this->view->registros = $rs;
        $this->view->pag = $pag;
        $this->view->total = $total;
        $this->view->inicio = ($inicio+1);
        $this->view->fim = $fim;
        $this->view->totalPag = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function gerencialAction()
    {
        $this->forward("visual-tecnico");
    }

    public function visualTecnicoAction()
    {
        $auth         = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario      = new Autenticacao_Model_DbTable_Usuario(); // objeto usu�rio
        $UsuarioAtivo = new Zend_Session_Namespace('UsuarioAtivo'); // cria a sess�o com o usu�rio ativo
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codPerfil  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
        $codOrgao  = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o

//        $tbl = new paUsuariosDoPerfil();
        $vw = new vwUsuariosOrgaosGrupos();
        $rs = $vw->buscarUsuarios($codPerfil, $codOrgao);
        $this->view->tecnicos = $rs;
    }

    public function resultadoVisualTecnicoAction()
    {
        $this->_helper->layout->disableLayout();
        $post   = Zend_Registry::get('post');

        $arrDados = array();
        $this->forward("listar-propostas-analise-visual-tecnico", "admissibilidade", "admissibilidade", array("view"=>"/relatorio/listarpropostasanalisevisualtecnico.phtml"));
    }

    public function prepararXlsAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
        $this->_response->clearHeaders();
        $dados = $this->getAllParams();
        $this->view->registros = $dados['dados'];

        if ($dados['view']) {
            $this->montaTela($dados['view']);
        }
    }

    public function gerarXlsAction()
    {
        $_POST['html'] = str_replace('display:none', '', $_POST['html']);

        parent::gerarXlsAction();
    }

    public function gerarPdfAction()
    {
        $_POST['html'] = str_replace('display:none', '', $_POST['html']);
        $_POST['html'] = str_replace('<input type="button" name="grid1" id="g1_1" class="btn_adicionar" title="Expandir" />', '', $_POST['html']);

        parent::gerarPdfAction();
    }

    /*
        private function newPdfPage()
        {
            $page  = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
            $style = new Zend_Pdf_Style();
            $style->setLineColor(new Zend_Pdf_Color_RGB(0.9, 0, 0));
            $style->setFillColor(new Zend_Pdf_Color_GrayScale(0.2));
            $style->setLineWidth(3);
            $style->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
            $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
            $page->setStyle($style);
            $pageHeight = $page->getHeight();
            $pageWidth  = $page->getWidth();
            return $page;
        }

        public function gerarPdfAction()
        {

            $_POST['html'] = str_replace('display:none', '', $_POST['html']);
            $_POST['html'] = str_replace('type="button"', 'type="hidden"', $_POST['html']);

            $UF = new Uf();
            $b = $UF->buscar();

            //parent::gerarPdfAction();

            $pdf = new Zend_Pdf();
            $page = $this->newPdfPage();

            $pageHeight = $page->getHeight();
            $pageWidth = $page->getWidth();
            $imageHeight = 72;
            $imageWidth = 72;
    $startPos = 72;
            foreach($b as $row){
                $title = $row->Sigla;
                $entrydata = $row->Descricao;

                if ($startPos < 72){
                     array_push($pdf->pages, $page);
                     $page = $this->newPdfPage();
                     $startPos = $pageHeight - 48;
                }

                $headlineStyle = new Zend_Pdf_Style();
                $headlineStyle->setLineWidth(3);
                $headlineStyle->setLineDashingPattern(array(3, 2, 3, 4), 1.6);

                $page->setStyle($headlineStyle);
                $title = strip_tags($title );
                $title = wordwrap($title , 55, '\n');

                $entrydata = strip_tags($entrydata);
                $entrydata = wordwrap($entrydata, 90, '\n');

                $articleArray = explode('\n', $entrydata);

                foreach ($articleArray as $line) {

                    if ($startPos < 48){

                         array_push($pdf->pages, $page);
                         $page = $this->newPdfPage();

                         $startPos = $pageHeight - 48;

                    }
                    $page->drawText($line, 48, $startPos);
                    //$page->restoreGS();
                    $startPos = $startPos - 16;

                }
                $startPos = $startPos - 16;

            }

            array_push($pdf->pages, $page);

           //header('Content-type: application/pdf');
            //echo $pdf->render();
            $pdf->save("chomp.pdf");

        }
    */

//    public function parecerProjetosAction(){
//
//    }
//
//    public function resultadoParecerProjetosAction(){
//        header("Content-Type: text/html; charset=ISO-8859-1");
//        $this->_helper->layout->disableLayout();
//        $post   = Zend_Registry::get('post');
//
//        $tbl = new Projetos();
//        $rs = $tbl->parecerProjetos();

//    }
}
