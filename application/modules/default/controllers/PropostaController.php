<?php

class PropostaController extends MinC_Controller_Action_Abstract
{
    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
            $PermissoesGrupo[] = 94;  // Parecerista
            $PermissoesGrupo[] = 121; // T�cnico
            $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
            parent::perfil(1, $PermissoesGrupo);

        // inicializando variaveis com valor padrao
        $this->intTamPag = 20;

        parent::init();

        //print_r(get_include_path());
            //Zend_Loader::loadClass("PlanoDistribuicao");
    }

    public function indexAction()
    {
        $this->_redirect("/proposta/listar-propostas");
    }

    public function propostaPorProponenteAction()
    {
        $get = Zend_Registry::get("get");
        $idAgente = $get->agente;

        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rsPropostas = $tblProposta->buscar(array("idagente = ?"=>$idAgente), array("nomeprojeto ASC"));


        //Descobrindo os dados do Agente/Proponente
        $tblAgente = new Nome();
        $rsAgente = $tblAgente->buscar(array("idAgente = ? "=>$idAgente))->current();

        //Descobrindo a movimenta��o corrente de cada proposta
        if (count($rsPropostas)>0) {
            //Conectando com movimentacao
            $tblMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
            //Conectando com projetos
            $tblProjetos = new Projetos();

            $movimentacoes = array();
            foreach ($rsPropostas as $proposta) {
                //Buscando movimenta��o desta proposta
                $rsMovimentacao = $tblMovimentacao->buscar(array("idprojeto = ?"=>$proposta->idPreProjeto, "stestado = ?"=>0))->current();

                //Descobrindo se esta proposta ja existe em projetos
                $rsProjeto = $tblProjetos->buscar(array("idprojeto = ?"=>$proposta->idPreProjeto));

                //Descobrindo tecnico
                $tecnico = $tblProposta->buscarConformidadeVisualTecnico($proposta->idPreProjeto);

                $movimentacoes[$proposta->idPreProjeto]["codMovimentacao"] = $rsMovimentacao->Movimentacao;

                if ($rsMovimentacao->Movimentacao == 96) {
                    $movimentacoes[$proposta->idPreProjeto]["txtMovimentacao"] = "<font color=#0000FF>" . $rsAgente->Descricao . "</font>";
                    //elseif ($tecnico[0]['tecnico'] == 96 and (!count($tecnico)>0)) //Antigo, que eu acho que estava errado
                    if (!count($tecnico)>0) {
                        $movimentacoes[$proposta->idPreProjeto]["txtMovimentacao"] = "<font color=#FF0000>" . 'Proposta em An�lise' . "</font>";
                    }
                } elseif ($rsMovimentacao->Movimentacao == 97 and (!count($rsProjeto)>0)) {
                    $movimentacoes[$proposta->idPreProjeto]["txtMovimentacao"] = "<font color=#FF0000>" . 'Proposta aguardando documentos' . "</font>";
                } elseif (count($rsProjeto)>0) {
                    $movimentacoes[$proposta->idPreProjeto]["txtMovimentacao"] = "<font color=#FF0000>" . 'Proposta transformada em projeto' . "</font>";
                } else {
                    $tblUsuario = new Autenticacao_Model_Usuario();
                    $rsUsuario = $tblUsuario->find($rsMovimentacao->Usuario)->current();

                    $movimentacoes[$proposta->idPreProjeto]["txtMovimentacao"] = "Proposta com o Analista";
                    if (count($rsUsuario) > 0) {
                        $movimentacoes[$proposta->idPreProjeto]["txtMovimentacao"] .= " (<font color=blue>".$rsUsuario->usu_nome."</font>)";
                    }
                }
            }
        }

        $arrDados = array(
                            "propostas"=>$rsPropostas,
                            "agente"=>$rsAgente,
                            "movimentacoes"=>$movimentacoes
                        );

        $this->montaTela("admissibilidade/listarpropostasproponente.phtml", $arrDados);
    }

    public function listarPropostasAnaliseVisualTecnicoAction()
    {
        $usuario = $_SESSION['Zend_Auth']['storage']->usu_orgao;

        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rsProposta = $tblProposta->buscarPropostaAnaliseVisualTecnico(array("idOrgao = "=>$usuario), array("Tecnico ASC"));

        $arrTecnicos = array();
        foreach ($rsProposta as $proposta) {
            $arrTecnicosPropostas[$proposta->Tecnico][] = $proposta;
        }

        $arrDados = array(
                            "propostas"=>$rsProposta,
                            "tecnicosPropostas"=>$arrTecnicosPropostas,
                            "urlXLS"=>$this->view->baseUrl()."/proposta/xls-propostas-analise-visual-tecnico",
                            "urlPDF"=>$this->view->baseUrl()."/proposta/pdf-propostas-analise-visual-tecnico"
                        );

        $this->montaTela("admissibilidade/listarpropostasanalisevisualtecnico.phtml", $arrDados);
    }

    public function listarPropostasAnaliseFinalAction()
    {
        $usuario = $_SESSION['Zend_Auth']['storage']->usu_orgao;

        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rsProposta = $tblProposta->buscarPropostaAnaliseFinal(array("idOrgao = "=>$usuario), array("Tecnico ASC"));

        $arrTecnicos = array();
        foreach ($rsProposta as $proposta) {
            $arrTecnicosPropostas[$proposta->Tecnico][] = $proposta;
        }

        $arrDados = array(
                            "propostas"=>$rsProposta,
                            "tecnicosPropostas"=>$arrTecnicosPropostas,
                            "urlXLS"=>$this->view->baseUrl()."/proposta/xls-propostas-analise-final",
                            "urlPDF"=>$this->view->baseUrl()."/proposta/pdf-propostas-analise-final"
                        );

        $this->montaTela("admissibilidade/listarpropostasanalisefinal.phtml", $arrDados);
    }

    public function xlsPropostasAnaliseFinalAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $usuario = $_SESSION['Zend_Auth']['storage']->usu_orgao;

        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rsProposta = $tblProposta->buscarPropostaAnaliseFinal(array("idOrgao = "=>$usuario), array("Tecnico ASC"));

        $html = "<table>
                    <tr>
                        <td>Nr. Proposta</td>
                        <td>Nome da Proposta</td>
                        <td>Dt.Movimenta��o</td>
                    </tr>
                    ";
        foreach ($rsProposta as $proposta) {
            $html .= "<tr><td>{$proposta->idPreProjeto}</td>" ;
            $html .= "<td>{$proposta->NomeProjeto}</td>" ;
            $html .= "<td>{$proposta->DtMovimentacao}</td></tr>" ;
        }
        $html .= "</table>" ;

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: inline; filename=file.ods;");
        echo $html;
    }

    public function xlsPropostasAnaliseVisualTecnicoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $usuario = $_SESSION['Zend_Auth']['storage']->usu_orgao;

        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rsProposta = $tblProposta->buscarPropostaAnaliseVisualTecnico(array("idOrgao = "=>$usuario), array("Tecnico ASC"));

        $html = "<table>
                    <tr>
                        <td>Nr. Proposta</td>
                        <td>Nome da Proposta</td>
                        <td>Dt.Movimenta��o</td>
                    </tr>
                    ";
        foreach ($rsProposta as $proposta) {
            $html .= "<tr><td>{$proposta->idProjeto}</td>" ;
            $html .= "<td>{$proposta->NomeProjeto}</td>" ;
            $html .= "<td>{$proposta->DtMovimentacao}</td></tr>" ;
        }
        $html .= "</table>" ;

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: inline; filename=file.ods;");
        echo $html;
    }

    public function pdfPropostasAnaliseFinalAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $usuario = $_SESSION['Zend_Auth']['storage']->usu_orgao;

        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rsProposta = $tblProposta->buscarPropostaAnaliseFinal(array("idOrgao = "=>$usuario), array("Tecnico ASC"));

        $arrTecnicos = array();
        foreach ($rsProposta as $proposta) {
            $arrTecnicosPropostas[$proposta->Tecnico][] = $proposta;
        }

        $html = '
                    <table width="100%">
                        <tr>
                            <th style="font-size:36px;">
                                Proposta em an�lise final
                            </th>
                        </tr>
                    ';
        $ultimoRegistro = null;
        $ct = 1;
        if (!empty($arrTecnicosPropostas)) {
            foreach ($arrTecnicosPropostas as $tecnico=>$propostas) {
                $html .= '
                        <tr>
                            <td align="left" style="border:1px #000000 solid; font-size:14px; font-weight:bold;">
                                Analista : '.$tecnico.'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" cellpadding="2" cellspacing="2" style="border:1px #000000 solid;">
                                    <tr>
                                        <th width="15%" style="border-bottom:1px #000000 solid;">Nr. Proposta</th>
                                        <th width="65%" style="border-bottom:1px #000000 solid;">Nome da Proposta</th>
                                        <th width="20%" style="border-bottom:1px #000000 solid;">Dt. Movimenta��o</th>
                                    </tr>
                    ';
                foreach ($propostas as $proposta) {
                    $html .= '
                                    <tr>
                                        <td align="center" style="font-size:12px;">'.$proposta->idPreProjeto.'</td>
                                        <td align="center" style="font-size:12px;">'.$proposta->NomeProjeto.'</td>
                                        <td align="center" style="font-size:12px;">'.ConverteData($proposta->DtMovimentacao, 5).'</td>
                                    </tr>
                    ';
                }
                $html .= '
                                </table>
                            </td>
                        </tr>
                    ';
                $ct++;
            }
        }

        $html .= '
                    </table>
                    ';
        //echo $html; die;
        $pdf = new PDF($html, 'pdf');
        $pdf->gerarRelatorio();
    }

    public function pdfPropostasAnaliseVisualTecnicoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $usuario = $_SESSION['Zend_Auth']['storage']->usu_orgao;

        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rsProposta = $tblProposta->buscarPropostaAnaliseVisualTecnico(array("idOrgao = "=>$usuario), array("Tecnico ASC"));

        $arrTecnicos = array();
        foreach ($rsProposta as $proposta) {
            $arrTecnicosPropostas[$proposta->Tecnico][] = $proposta;
        }

        $html = '
                    <table width="100%">
                        <tr>
                            <th style="font-size:36px;">
                                Avalia��o: Reavalia��o
                            </th>
                        </tr>
                    ';
        $ultimoRegistro = null;
        $ct = 1;
        if (!empty($arrTecnicosPropostas)) {
            foreach ($arrTecnicosPropostas as $tecnico=>$propostas) {
                $html .= '
                        <tr>
                            <td align="left" style="border:1px #000000 solid; font-size:14px; font-weight:bold;">
                                Analista : '.$tecnico.'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" cellpadding="2" cellspacing="2" style="border:1px #000000 solid;">
                                    <tr>
                                        <th width="15%" style="border-bottom:1px #000000 solid;">Nr. Proposta</th>
                                        <th width="65%" style="border-bottom:1px #000000 solid;">Nome da Proposta</th>
                                        <th width="20%" style="border-bottom:1px #000000 solid;">Dt. Movimenta��o</th>
                                    </tr>
                    ';
                foreach ($propostas as $proposta) {
                    if ($proposta->ConformidadeOK == 0) {
                        $html .= '
                                    <tr>
                                        <td align="center" style="font-size:12px;">'.$proposta->idProjeto.'</td>
                                        <td align="center" style="font-size:12px;">'.$proposta->NomeProjeto.'</td>
                                        <td align="center" style="font-size:12px;">'.ConverteData($proposta->DtMovimentacao, 5).'</td>
                                    </tr>
                    ';
                    }
                }
                $html .= '
                                </table>
                            </td>
                        </tr>
                    ';
                $ct++;
            }
        }

        $html .= '
                    </table>
                    ';

        $html .= '
                    <table width="100%">
                        <tr>
                            <th style="font-size:36px;">
                                Avalia��o: Inicial
                            </th>
                        </tr>
                    ';
        $ultimoRegistro = null;
        $ct = 1;
        if (!empty($arrTecnicosPropostas)) {
            foreach ($arrTecnicosPropostas as $tecnico=>$propostas) {
                $html .= '
                        <tr>
                            <td align="left" style="border:1px #000000 solid; font-size:14px; font-weight:bold;">
                                Analista : '.$tecnico.'
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" cellpadding="2" cellspacing="2" style="border:1px #000000 solid;">
                                    <tr>
                                        <th width="15%" style="border-bottom:1px #000000 solid;">Nr. Proposta</th>
                                        <th width="65%" style="border-bottom:1px #000000 solid;">Nome da Proposta</th>
                                        <th width="20%" style="border-bottom:1px #000000 solid;">Dt. Movimenta��o</th>
                                    </tr>
                    ';
                foreach ($propostas as $proposta) {
                    if ($proposta->ConformidadeOK == 9) {
                        $html .= '
                                    <tr>
                                        <td align="center" style="font-size:12px;">'.$proposta->idProjeto.'</td>
                                        <td align="center" style="font-size:12px;">'.$proposta->NomeProjeto.'</td>
                                        <td align="center" style="font-size:12px;">'.ConverteData($proposta->DtMovimentacao, 5).'</td>
                                    </tr>
                    ';
                    }
                }
                $html .= '
                                </table>
                            </td>
                        </tr>
                    ';
                $ct++;
            }
        }

        $html .= '
                    </table>
                    ';
        //echo $html; die;
        $pdf = new PDF($html, 'pdf');
        $pdf->gerarRelatorio();
    }

    public function historicoAnaliseVisualAction()
    {
        $post = Zend_Registry::get("post");
        $usuario = $_SESSION['Zend_Auth']['storage']->usu_orgao;

        if (empty($post->busca)) {
            $tblProposta = new Proposta_Model_DbTable_PreProjeto();
            $rsTecnicos = $tblProposta->buscarTecnicosHistoricoAnaliseVisual($usuario);

            $arrDados = array(
                                "tecnicos"=>$rsTecnicos,
                                "urlForm"=>$this->view->baseUrl()."/proposta/historico-analise-visual"
                            );

            $this->montaTela("admissibilidade/consultarhistoricoanalisevisual.phtml", $arrDados);
        } else {
            $tecnico = ($post->tecnico != "")?$post->tecnico:null;
            $dtInicio = ($post->dataPropostaInicial != "")?ConverteData($post->dataPropostaInicial, 13):null;
            $dtFim = ($post->dataPropostaFinal != "")?ConverteData($post->dataPropostaFinal, 13):null;

            $situacao = (!empty($post->situacao))?$post->situacao:null;

            $tblProposta = new Proposta_Model_DbTable_PreProjeto();
            $rsProposta = $tblProposta->buscarHistoricoAnaliseVisual($usuario, $tecnico, $situacao, $dtInicio, $dtFim);

            $arrTecnicosPropostas = array();
            foreach ($rsProposta as $proposta) {
                $arrTecnicosPropostas[$proposta->Tecnico][] = $proposta;
            }

            $arrDados = array(
                                "propostas"=>$rsProposta,
                                "tecnicosPropostas"=>$arrTecnicosPropostas
                            );

            $this->montaTela("admissibilidade/listarhistoricoanalisevisual.phtml", $arrDados);
        }
    }

    public function avaliacaoHistoricoAnaliseVisualAction()
    {
        $get = Zend_Registry::get("get");
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rsAvaliacao = $tblProposta->buscarAvaliacaoHistoricoAnaliseVisual($get->idAvaliacao);

        echo $rsAvaliacao[0]->Avaliacao;
    }

    public function listarPropostasAction()
    {
        $usuario = $_SESSION['Zend_Auth']['storage']->usu_codigo;
        $post = Zend_Registry::get("post");

        //$analistas = AdmissibilidadeDAO::consultarRedistribuirAnalise($params);
            $usuario = 605; //Apagar esta linha quando este modulo for para producao

            $rsPropostaInicial = array();
        $rsPropostaVisual = array();
        $rsPropostaDocumental = array();
        $rsPropostaFinal = array();
        $arrBusca['x.idTecnico = '] = $usuario;

        $tblProposta = new Proposta_Model_DbTable_PreProjeto();

        if ($post->numeroProposta != "") {
            $arrBusca['p.idPreProjeto = '] = $post->numeroProposta;
        }
        if ($post->nomeProposta != "") {
            if ($post->tiponome == "igual") {
                $arrBusca['p.NomeProjeto = '] = $post->nomeProposta;
            } elseif ($post->tiponome == "contendo") {
                $arrBusca['p.NomeProjeto LIKE '] = "('%".$post->nomeProposta."%')";
            }
        }
        if ($post->dataPropostaInicial != "") {
            if ($post->tipodata == "igual") {
                $arrBusca['x.DtAvaliacao > '] = "'".ConverteData($post->dataPropostaInicial, 13)." 00:00:00'";
                $arrBusca['x.DtAvaliacao < '] = "'".ConverteData($post->dataPropostaInicial, 13)." 23:59:59'";
            } else {
                $arrBusca['x.DtAvaliacao > '] = "'".ConverteData($post->dataPropostaInicial, 13)." 00:00:00'";
                if ($post->dataPropostaFinal != "") {
                    $arrBusca['x.DtAvaliacao < '] = "'".ConverteData($post->dataPropostaFinal, 13)." 23:59:59'";
                }
            }
        }

        if ($post->situacao != "") {
            if ($post->situacao == "inicial") {
                if ($post->tipobuscasituacao == "igual") {
                    $arrBusca['m.Movimentacao = '] = 96;
                    $rsPropostaInicial = $tblProposta->buscarPropostaAdmissibilidade($arrBusca, array("x.DtAvaliacao DESC")); //m.Movimentacao = 96 >> INICIAL
                }
            }
            if ($post->situacao == "visual") {
                if ($post->tipobuscasituacao == "igual") {
                    $arrBusca['m.Movimentacao = '] = 97;
                    $rsPropostaVisual = $tblProposta->buscarPropostaAdmissibilidade($arrBusca, array("x.DtAvaliacao DESC")); //m.Movimentacao = 96 >> INICIAL
                }
            }
            /*if($post->situacao == "documental"){
                if($post->tipobuscasituacao == "igual"){
                    $arrBusca['m.Movimentacao = '] = 97;
                    $rsPropostaVisual = $tblProposta->buscarPropostaAdmissibilidade($arrBusca, array("x.DtAvaliacao DESC")); //m.Movimentacao = 96 >> INICIAL
                }
            }*/
            if ($post->situacao == "final") {
                if ($post->tipobuscasituacao == "igual") {
                    $arrBusca['m.Movimentacao = '] = 128;
                    $rsPropostaFinal = $tblProposta->buscarPropostaAdmissibilidade($arrBusca, array("x.DtAvaliacao DESC")); //m.Movimentacao = 96 >> INICIAL
                }
            }
        } else {
            //x($arrBusca);
            $arrBusca['m.Movimentacao = '] = 96;
            $rsPropostaInicial = $tblProposta->buscarPropostaAdmissibilidade($arrBusca, array("x.DtAvaliacao DESC")); //m.Movimentacao = 96 >> INICIAL
            $arrBusca['m.Movimentacao = '] = 97;
            $rsPropostaVisual = $tblProposta->buscarPropostaAdmissibilidade($arrBusca, array("x.DtAvaliacao DESC")); //m.Movimentacao = 97 >> VISUAL
            //$arrBusca['m.Movimentacao = '] = ?;
            //$rsPropostaDocumental = $tblProposta->buscarPropostaAdmissibilidade($arrBusca, array("x.DtAvaliacao DESC")); //m.Movimentacao = ? >> DOCUMENTAL
            $arrBusca['m.Movimentacao = '] = 128;
            $rsPropostaFinal = $tblProposta->buscarPropostaAdmissibilidade($arrBusca, array("x.DtAvaliacao DESC")); //m.Movimentacao = 128 >> FINAL
        }

        $arrDados = array(
                            "propostasInicial"=>$rsPropostaInicial,
                            "propostasVisual"=>$rsPropostaVisual,
                            "propostasDocumental"=>$rsPropostaDocumental,
                            "propostasFinal"=>$rsPropostaFinal,
                            "formularioLocalizar"=>$this->_urlPadrao."/proposta/localizar"
                        );

        $this->montaTela("admissibilidade/listarpropostas.phtml", $arrDados);
    }

    public function localizarAction()
    {
        $arrDados = array(
                            "urlAcao"=>$this->_urlPadrao."/proposta/listar-propostas"
                        );

        $this->montaTela("admissibilidade/localizarpropostas.phtml", $arrDados);
    }
}
