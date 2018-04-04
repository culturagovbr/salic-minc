<?php

/**
 * Description of VincularresponsavelController
 *
 * @author tisomar
 * @author wouerner <wouerner@gmail.com>
 */
class Proposta_VincularresponsavelController extends Proposta_GenericController
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
    }


    /**
     * mostraragentesAction
     *
     * @access public
     * @return void
     * @todo Remover Html do metodo.
     */
    public function mostraragentesAction()
    {
        $this->_helper->layout->disableLayout();
        $ag = new Agente_Model_DbTable_Agentes;

        if (isset($_POST['cnpjcpf'])) {
            $cnpjcpf = $_POST['cnpjcpf'];
            $nome = $_POST['nome'];
            $cnpjcpfSemMask = Mascara::delMaskCPFCNPJ($cnpjcpf);

            if ($cnpjcpf != '') {
                $wh['CNPJCPF = ?'] = Mascara::delMaskCPFCNPJ($cnpjcpf);
                $where['ag.CNPJCPF = ?'] = Mascara::delMaskCPFCNPJ($cnpjcpf);
                $buscaAgente = $ag->buscar($wh);

                if ($buscaAgente->count() == 0) {
                    echo "  <input type='hidden' id='novoproponente' value='" . $cnpjcpfSemMask . "' />
                            <table class='tabela'>
                                <tr>
                                    <td class='red-text centro'>Proponente n&atilde;o cadastrado! Deseja cadastrar agora?</td>
                                    <td class='centro'><button type='button' class='btn' id='novoprop' onclick='novoproponente();'>cadastrar proponente</button></td>
                                </tr>
                            </table>";
                    $this->_helper->viewRenderer->setNoRender(true);
                }
            }

            if ($nome != '') {
                $where["nm.Descricao like (?)"] = "%" . $nome . "%";
            }
            $buscarvinculo = $ag->buscarNovoProponente($where, $this->idResponsavel);
            if ($buscarvinculo->count() > 0) {
                $this->montaTela('vincularresponsavel/mostraragentes.phtml', array('vinculo' => $buscarvinculo, 'idResponsavel' => $this->idResponsavel));
            } else {
                echo "<div id='msgAgenteVinculado'>Nenhum registro encontrado!</div>
                    <script>
                        alertModal(null, 'msgAgenteVinculado', null, 150);
                      </script>";
                $this->_helper->viewRenderer->setNoRender(true);
            }
        } else {
            $where = array();
            $where["vp.idUsuarioResponsavel = ?"] = $this->idUsuario;
            //$where["v.siVinculo = ?"] = 2;
            $buscarvinculo = $ag->buscarAgenteVinculoProponente($where);
            $this->montaTela('proposta/vincularresponsavel/mostraragentes.phtml', array('vinculo' => $buscarvinculo));
        }
    }

    /**
     * vinculoAction
     *
     * @access public
     * @return void
     * @author <wouerner@gmail.com>
     */
    public function vinculoAction()
    {
        $this->_helper->layout->disableLayout();

        $v = new Agente_Model_DbTable_TbVinculo();
        $pp = new Proposta_Model_DbTable_PreProjeto();
        $vprp = new Agente_Model_DbTable_TbVinculoProposta();
        $emailDAO = new EmailDAO();
        $tableInternet = new Agente_Model_DbTable_Internet();

        /*Temos que ver aonde vamos buscar o email do cara?*/
        $buscarEmail = $tableInternet->buscarEmailAgente(null, $_POST['idAgente'], 1, null, false);
        if ($buscarEmail) {
            $buscarEmail = array_change_key_case($buscarEmail->toArray());
        }

        $emailProponente = $buscarEmail ? $buscarEmail['email'] : null;
        $assunto = 'Solicitação de vinculo ao responsável';
        $texto = 'Favor verificar o vinculo solicitado no Sistema SALIC WEB';

        if (isset($_POST['solicitarvinculo'])) {
            $idAgenteProponente = $_POST['idAgente'];
            $idUsuarioResponsavel = $this->idResponsavel;
            $dados = array('idusuarioresponsavel' => $idUsuarioResponsavel,
                'idagenteproponente' => $idAgenteProponente,
                'dtvinculo' => $tableInternet->getExpressionDate(),
                'sivinculo' => 0
            );
            try {
                $where['idagenteproponente   = ?'] = $idAgenteProponente;
                $where['idusuarioresponsavel = ?'] = $idUsuarioResponsavel;
                $vinculocadastrado = $v->buscar($where);

                if (count($vinculocadastrado) > 0) {
                    $v->alterar($dados, $where);
                } else {
                    $v->inserir($dados);
                }

                $enviarEmail = $emailDAO->enviarEmail($emailProponente, $assunto, $texto);

                $this->_helper->json(array('error' => false));
            } catch (Zend_Exception $e) {
                echo '<pre>';
                var_dump($e->getMessage());
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->json(array('error' => true));
            }
        }

        if (isset($_POST['solicitarvinculoproposta'])) {
            $idpreprojeto = $_POST['idpreprojeto'];
            $buscarpreprojeto = $pp->buscar(array('idPreProjeto = ?' => $idpreprojeto))->current();
            $idAgenteProponente = $buscarpreprojeto->idAgente;
            $idUsuarioResponsavel = $this->idUsuario;

            $buscarvinculo = $v->buscar(array('idAgenteProponente = ? ' => $idAgenteProponente, 'idUsuarioResponsavel = ?' => $idUsuarioResponsavel))->current();
            $idVinculo = $buscarvinculo->idVinculo;

            $dados = array('idVinculo' => $idVinculo, 'idPreProjeto' => $idpreprojeto, 'siVinculoProposta' => 0);

            try {
                $vprp->inserir($dados);
                $this->_helper->json(array('error' => false));
            } catch (Zend_Exception $e) {
                $this->_helper->json(array('error' => true));
            }
        }

        if (isset($_POST['aceitevinculo'])) {
            $dados = array('siVinculoProposta' => $_POST['stVinculoProposta']);
            $where = "idVinculoProposta = {$_POST['idVinculoProposta']}";

            try {
                $vprp->alterar($dados, $where);
                $this->_helper->json(array('error' => false));
            } catch (Zend_Exception $e) {
                $this->_helper->json(array('error' => true));
            }
        }

        if (isset($_POST['desvincular'])) {
            $dados = array('siVinculoProposta' => 1);
            $where = "idVinculoProposta = {$_POST['idVinculoProposta']}";

            try {
                $vprp->alterar($dados, $where);
                $this->_helper->json(array('error' => false));
            } catch (Zend_Exception $e) {
                $this->_helper->json(array('error' => true));
            }
        }

        $this->_helper->viewRenderer->setNoRender(true);
    }

    /* }}} */

    public function vincularresponsavelAction()
    {
        $ag = new Agente_Model_DbTable_Agentes;
        $buscarvinculo = $ag->buscarAgenteVinculoResponsavel(array('vr.idAgenteProponente = ?' => $this->idUsuario, 'siVinculoProposta = ?' => 0));
        $buscarvinculado = $ag->buscarAgenteVinculoResponsavel(array('vr.idAgenteProponente = ?' => $this->idUsuario, 'siVinculoProposta = ?' => 2));
        $this->view->vinculo = $buscarvinculo;
        $this->view->vinculado = $buscarvinculado;
    }

    public function vincularproponenteAction()
    {
        $ag = new Agente_Model_DbTable_Agentes;
        $buscarvinculo = $ag->buscarAgenteVinculoProponente(array('vp.idAgenteProponente = ?' => $this->idUsuario, 'siVinculoProposta = ?' => 0));
        $buscarvinculado = $ag->buscarAgenteVinculoProponente(array('vp.idAgenteProponente = ?' => $this->idUsuario, 'siVinculoProposta = ?' => 2));
        $this->view->vinculo = $buscarvinculo;
        $this->view->vinculado = $buscarvinculado;
    }

    public function consultarresponsavelAction()
    {
    }

    public function mostraresponsavelAction()
    {
        $this->_helper->layout->disableLayout();
        $ag = new Agente_Model_DbTable_Agentes();
        if ($_POST) {
            $cnpjcpf = $_POST['cnpjcpf'];
            $nome = $_POST['nome'];
            $stVinculo = $_POST['stVinculo'];
            if ($cnpjcpf != '') {
                $where['ag.CNPJCPF = ?'] = Mascara::delMaskCPFCNPJ($cnpjcpf);
            }
            if ($nome != '') {
                $where["nm.Descricao like (?)"] = "%" . $nome . "%";
            }
            if ($stVinculo != '') {
                $where['vprp.siVinculoProposta = ?'] = $stVinculo;
            }
        } else {
            $where['vr.idAgenteProponente = ?'] = $this->idAgente;
            $where['vprp.idPreProjeto is not null'] = '';
        }
        $buscarVinculo = $ag->buscarAgenteVinculoResponsavel($where);
        $this->view->vinculo = $buscarVinculo;
    }


    /********************************************************************************************************/

    public function vinculoproponenteAction()
    {
        $tbVinculo = new Agente_Model_DbTable_TbVinculo();
        $tbVinculoProposta = new Agente_Model_DbTable_TbVinculoProposta();
        $PreProjetoDAO = new Proposta_Model_DbTable_PreProjeto();

        $idVinculo = $this->_request->getParam("idVinculo");
        $siVinculo = $this->_request->getParam("siVinculo");
        $idUsuarioR = $this->_request->getParam("idUsuario");

        $dados = array('siVinculo' => $siVinculo, 'dtVinculo' => MinC_Db_Expr::date());
        $where['idVinculo = ?'] = $idVinculo;
        $msg = '';

        if ($siVinculo == 1) {
            $msg = 'O responsável foi rejeitado.';
        } elseif ($siVinculo == 2) {
            $msg = 'Responsável vinculado com sucesso!';
        } elseif ($siVinculo == 3) {
            $msg = 'O responsável foi desvinculado.';
        }

        try {
            $alterar = $tbVinculo->alterar($dados, $where);

            if ($siVinculo == 3) {
                $alterarVinculoProposta = $PreProjetoDAO->retirarProjetosVinculos($siVinculo, $idVinculo);
                $retirarPropostas = $PreProjetoDAO->retirarProjetos($this->idResponsavel, $idUsuarioR, $this->idAgente);
            }
            parent::message($msg, "proposta/manterpropostaincentivofiscal/consultarresponsaveis", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Falha na recuperação dos dados!", "proposta/manterpropostaincentivofiscal/consultarresponsaveis", "ERROR");
        }
    }

    public function vinculoresponsavelAction()
    {
        $tbVinculo = new Agente_Model_DbTable_TbVinculo();
//        $agentes = new Agente_Model_DbTable_Agentes();
        $idResponsavel = $this->_request->getParam("idResponsavel");
        $idProponente = $this->idAgente;

        $where['idUsuarioResponsavel = ?'] = $idResponsavel;
        $where['idAgenteProponente   = ?'] = $idProponente;
        $vinculo = $tbVinculo->buscar($where);


        $dados = array('idAgenteProponente' => $idProponente,
            'dtVinculo' => $tbVinculo->getExpressionDate(),
            'siVinculo' => 2,
            'idusuarioResponsavel' => $idResponsavel
        );

        try {
            if (count($vinculo) > 0) {
                $dadosUP['siVinculo'] = 2;
                $whereUP['idVinculo = ?'] = $vinculo[0]->idVinculo;

                $update = $tbVinculo->alterar($dadosUP, $whereUP);
            } else {
                $insere = $tbVinculo->inserir($dados);
            }

            parent::message("vinculado com sucesso!", "proposta/manterpropostaincentivofiscal/novoresponsavel", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao vincular! " . $e->getMessage(), "proposta/manterpropostaincentivofiscal/novoresponsavel", "ERROR");
        }
    }

    /**
     * Metodo trocarproponente()
     * UC 89 - Fluxo FA1 - Trocar Proponente
     * @access public
     * @param void
     * @return void
     */
    public function trocarproponenteAction()
    {
        $tbVinculoPropostaDAO = new Agente_Model_DbTable_TbVinculoProposta();
        $PreProjetoDAO = new Proposta_Model_DbTable_PreProjeto();

        $dadosPropronente = $this->_request->getParam("propronente");

        $parte = explode(":", $dadosPropronente);
        $idNovoVinculo = $parte[0];
        $idNovoPropronente = $parte[1];

        $idVinculoProposta = $this->_request->getParam("idVinculoProposta"); // Vinculo a alterar
        $idPreProjeto = $this->_request->getParam("idPreProjeto"); // idPreProjeto

        $mecanismo = $this->_request->getParam("mecanismo");

        try {
            $dados['siVinculoProposta'] = 3;
            $where['idVinculoProposta = ?'] = $idVinculoProposta;
            $alteraVP = $tbVinculoPropostaDAO->alterar($dados, $where, false);

            $novosDados = array('idVinculo' => $idNovoVinculo,
                'idPreProjeto' => $idPreProjeto,
                'siVinculoProposta' => 2);

            $insere = $tbVinculoPropostaDAO->inserir($novosDados, false);

            $alteraPP = $PreProjetoDAO->alteraproponente($idPreProjeto, $idNovoPropronente);

            if ($mecanismo == 2) {
                parent::message("Proponente trocado com sucesso!", "proposta/manterpropostaedital/dadospropostaedital?idPreProjeto=" . $idPreProjeto, "CONFIRM");
            } else {
                parent::message("Proponente trocado com sucesso!", "proposta/manterpropostaincentivofiscal/editar?idPreProjeto=" . $idPreProjeto, "CONFIRM");
            }
        } catch (Exception $e) {
            parent::message("Erro. " . $e->getMessage(), "proposta/manterpropostaincentivofiscal/editar?idPreProjeto=" . $idPreProjeto, "ERROR");
        }
    }

    /**
     * UC 89 - Fluxo FA1 - Trocar Proponente
     * @name vincularpropostasAction
     *
     * @author Ruy Junior Ferreira Silva
     * @author Cleber Santos <oclebersantos@gmail.com>
     * @since  ${DATE}
     */
    public function vincularpropostasAction()
    {
        $tblTbVinculoProposta = new Agente_Model_TbVinculoPropostaMapper();
        $dadosResponsavel = $this->_request->getParam("responsavel");
        $parte = explode(":", $dadosResponsavel);
        $arrData = array();
        $arrData['opcaovinculacao'] = $this->_request->getParam("opcaovinculacao");
        $arrData['idpreprojeto'] = $this->_request->getParam("propostas");
        $arrData['idvinculo'] = $parte[0];
        $arrData['idresponsavel'] = $parte[1];
        if ($tblTbVinculoProposta->saveCustom($arrData)) {
            parent::message($tblTbVinculoProposta->getMessage(), "proposta/manterpropostaincentivofiscal/vincularpropostas", "CONFIRM");
        } else {
            parent::message("Erro. " . $tblTbVinculoProposta->getMessage(), "proposta/manterpropostaincentivofiscal/vincularpropostas", "ERROR");
        }
    }

    /**
     * Metodo vincularprojetos()
     * UC 89 - Fluxo FA8 - Desvincular Projetos
     * @access public
     * @param void
     * @return void
     */
    public function vincularprojetosAction()
    {
        $tbVinculoPropostaDAO = new Agente_Model_DbTable_TbVinculoProposta();
        $PreProjetoDAO = new Proposta_Model_DbTable_PreProjeto();

        $idPreProjeto = $this->_request->getParam("propostas");
        $idResponsavel = $this->idResponsavel;

        try {
            $dados['siVinculoProposta'] = 3;
            $where['idPreProjeto = ?'] = $idPreProjeto;
            $alteraVP = $tbVinculoPropostaDAO->alterar($dados, $where, false);

            // Cade a procuracao?

            /* Nao vai cadastrar pois ele e dono da sua proposta
            $novosDados = array('idVinculo' 		=> $idVinculo,
                                'idPreProjeto' 		=> $idPreProjeto,
                                'siVinculoProposta' => 2
            );

            $insere = $tbVinculoPropostaDAO->inserir($novosDados, false);
            */
            $alteraPP = $PreProjetoDAO->alteraresponsavel($idPreProjeto, $idResponsavel);

            parent::message("O responsável foi desvinculado.", "proposta/manterpropostaincentivofiscal/vincularprojetos", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro. " . $e->getMessage(), "proposta/manterpropostaincentivofiscal/vincularprojetos", "ERROR");
        }
    }
}
