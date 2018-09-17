<?php
class Proposta_PreProjetoArquivadoController extends Proposta_GenericController
{
    private $blnPossuiDiligencias = 0;

    public function init()
    {
        parent::init();

        if (!empty($this->idPreProjeto)) {
            $this->view->idPreProjeto = $this->idPreProjeto;

            $this->verificarPermissaoAcesso(true, false, false);

            //VERIFICA SE A PROPOSTA TEM DILIGENCIAS
            $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $rsDiligencias = $PreProjeto->listarDiligenciasPreProjeto(array('pre.idpreprojeto = ?' => $this->idPreProjeto));
            $this->view->blnPossuiDiligencias = $rsDiligencias->count();

            $this->view->acao = $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/salvar";
        }

        // Busca na tabela apoio ExecucaoImediata
        $tableVerificacao = new Proposta_Model_DbTable_Verificacao();
        $listaExecucaoImediata = $tableVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idTipo' => 23), array('idVerificacao'));
        $this->view->listaExecucaoImediata = $listaExecucaoImediata;

        $this->auth = Zend_Auth::getInstance();

        $this->events = new Zend_EventManager_EventManager();
        $this->events->attach('email', $this->email('teste'));
    }

    public function indexAction()
    {
        $idAgente = $this->getRequest()->getParam('idagente');
        $stEstado = $this->getRequest()->getParam('stestado');
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');
        $order = new Zend_Db_Expr('"p"."idpreprojeto" DESC');
        $where = array();

        $idAgente = ((int)$idAgente == 0) ? $this->idAgente : (int)$idAgente;

        if (empty($idAgente)) {
            $this->_helper->json(array(
                "data" => 0,
                'recordsTotal' => 0,
                'draw' => 0,
                'recordsFiltered' => 0));
        }

        $tblPreProjetoArquivado = new Proposta_Model_PreProjetoArquivado();

        $rsPreProjetoArquivado = $tblPreProjetoArquivado->listar(
            $this->idAgente,
            $this->idResponsavel,
            $idAgente,
            $where,
            $order,
            $start,
            $length,
            $search,
            $stEstado
        );

        $recordsTotal = 0;
        $recordsFiltered = 0;
        $aux = array();
        if (!empty($rsPreProjetoArquivado)) {
            foreach ($rsPreProjetoArquivado as $key => $proposta) {

                $proposta->nomeproponente = utf8_encode($proposta->nomeproponente);
                $proposta->nomeprojeto = utf8_encode($proposta->nomeprojeto);
                $proposta->MotivoArquivamento = utf8_encode($proposta->MotivoArquivamento);
                $proposta->SolicitacaoDesarquivamento = utf8_encode($proposta->SolicitacaoDesarquivamento);

                $aux[$key] = $proposta;
            }

            $totalData = $tblPreProjetoArquivado->listar($this->idAgente, $this->idResponsavel, $idAgente, array(), null, null, null, null);
            $recordsTotal = count($totalData);
            $recordsFiltered = $recordsTotal;

        }

        $this->_helper->json(array(
            "data" => !empty($aux) ? $aux : 0,
            'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0));
    }

    public function verificaPermissaoAcessoProposta($idPreProjeto)
    {
        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rs = $tblProposta->buscar(array("idPreProjeto = ? " => $idPreProjeto, "1=1 OR idEdital IS NULL OR idEdital > 0" => "?", "idUsuario =?" => $this->idResponsavel));
        return $rs->count();
    }

    public function storeAction()
    {
        $message = null;
        $success = true;

        $idAvaliador = $this->auth->getIdentity()->usu_codigo;
        $idPreProjeto = $this->getRequest()->getParam("idPreProjeto");
        $MotivoArquivamento = $this->getRequest()->getParam("MotivoArquivamento");

        $arquivar = new Proposta_Model_PreProjetoArquivado();

        $data = [
            'idPreProjeto' => $idPreProjeto,
            'MotivoArquivamento' => utf8_decode($MotivoArquivamento),
            'idAvaliadorArquivamento' => $idAvaliador,
            'stEstado' =>  1, // arquivamento ativo.
            'dtArquivamento' => date('Y-m-d h:i'),
        ];

        $registroArquivamento = $arquivar->listaRegistrosDeArquivamento($idPreProjeto);

        try {
            if (!count($registroArquivamento)) {
                $arquivar->insert($data);
            } else {
                $segundaArquivacao = [
                    'stEstado' => '0', //segunda arquivacao
                ];
                $data = array_merge($data, $segundaArquivacao);

                $arquivar->update($data, ["idPreProjeto = ?" => $idPreProjeto]);
            }
        } catch(Exception $e){
            $message = $e->getMessage();
            $success = false;
        }

        $agente = new Proposta_Model_DbTable_PreProjeto();
        $agente = $agente->buscaCompleta(['a.idPreProjeto = ? ' => $idPreProjeto]);

        $email = new StdClass();
        $email->text = 'Motivo Arquivamento: '. $this->montarEmail($MotivoArquivamento);
        $email->to = $agente->current()->EmailAgente;
        $email->subject = 'SALIC - Arquivamento Proposta: ' . $idPreProjeto;

        $this->events->trigger('email', $this, $email);

        $this->_helper->json(
            [
                'data' => $data,
                'success' => $success,
                'message' => $message
            ]
        );
    }

    private function montarEmail($texto)
    {
        $textoQuebraDeLinha = $this->converterQuebraDeLinha($texto);
        $textoUtf8Decode = utf8_decode(html_entity_decode($textoQuebraDeLinha));

        return $textoUtf8Decode;
    }

    private function converterQuebraDeLinha($texto)
    {
        return str_replace(array("\r\n", "\n", "\r"), "<br/>", $texto);
    }

    public function updateAction()
    {
        $message = null;
        $success = true;
        $data = [];

        $idPreProjeto = $this->getRequest()->getParam("idpreprojeto");
        $SolicitacaoDesarquivamento = $this->getRequest()->getParam("SolicitacaoDesarquivamento");
        $stEstado = $this->getRequest()->getParam("stEstado");
        $Avaliacao = $this->getRequest()->getParam("Avaliacao");
        $stDecisao = $this->getRequest()->getParam("stDecisao");
        $avaliacaoFinal = $this->getRequest()->getParam("avaliacaoFinal");

        $arquivar = new Proposta_Model_PreProjetoArquivado();

        if($SolicitacaoDesarquivamento){
            $data['SolicitacaoDesarquivamento'] = utf8_decode($SolicitacaoDesarquivamento);
            $data['dtSolicitacaoDesarquivamento'] = new Zend_Db_Expr('GETDATE()');
        }

        if($avaliacaoFinal) {
            if ($Avaliacao != null) {
                $data['Avaliacao'] = $Avaliacao;
                $data['dtAvaliacao'] = new Zend_Db_Expr('GETDATE()');
                $data['idAvaliadorAnaliseDesarquivamento'] = $this->auth->getIdentity()->usu_codigo;
            }else{
                throw new Exception("&Eacute; necess&aacute;rio preencher a Avalia&ccedil;&atilde;o!");
            }
        }

        if($stEstado !== null) {
            $data['stEstado'] = $stEstado;
        }

        if($stDecisao != null) {
            $data['stDecisao'] = $stDecisao;
        }

        try {
            $arquivar->update($data, array('idPreProjeto = ?' => $idPreProjeto));
            $message = 'Solicita&ccedil;&atilde;o enviada!' . $idPreProjeto;
        } catch(Exception $e){
            $message = $e->getMessage();
            $success = false;
        }
        $data['SolicitacaoDesarquivamento'] = $SolicitacaoDesarquivamento;
        $this->_helper->json(
            [
                'data' => $data,
                'success' => $success,
                'message' => $message
            ]
        );
    }

    public function avaliarArquivamentoAction()
    {
        $message = null;
        $success = true;
        $data = [];

        $idPreProjeto = $this->getRequest()->getParam("idpreprojeto");
        $stEstado = $this->getRequest()->getParam("stEstado");
        $Avaliacao = $this->getRequest()->getParam("Avaliacao");
        $stDecisao = $this->getRequest()->getParam("stDecisao");
        $avaliacaoFinal = $this->getRequest()->getParam("avaliacaoFinal");



        if ($stEstado !== null) {
            $data['stEstado'] = $stEstado;
        }

        if ($stDecisao !== null) {
            $data['stDecisao'] = $stDecisao;
        }

        if ($avaliacaoFinal) {

            $data2 = [
                'dtAvaliacao' => new Zend_Db_Expr('GETDATE()'),
                'idAvaliadorAnaliseDesarquivamento' => $this->auth->getIdentity()->usu_codigo,
                'Avaliacao' => null
            ];

            if ($Avaliacao == null && $stDecisao == Proposta_Model_PreProjeto::ESTADO_ARQUIVADO) {
                $success = false;
                $message = "É necessário descrever a avaliação!";
            } else {
                $data2['Avaliacao'] = $Avaliacao;
            }
        }

        $data = array_merge($data, $data2);

        try {
            if($success){
                $where = ['idPreProjeto = ?' => $idPreProjeto];

                (new Proposta_Model_PreProjetoArquivado)->update($data, $where);

                if ($data['stDecisao'] == Proposta_Model_PreProjeto::ESTADO_ATIVO) {
                    (new Proposta_Model_DbTable_PreProjeto)->update([
                        'dtArquivamento' => null,
                        'stEstado' => Proposta_Model_PreProjeto::ESTADO_ATIVO
                    ], $where);
                }

                $agente = new Proposta_Model_DbTable_PreProjeto();
                $agente = $agente->buscaCompleta(['a.idPreProjeto = ? ' => $idPreProjeto]);

                $corpoEmail = '<p>Senhor(a) Proponente,</p></br>';
                if($stDecisao == Proposta_Model_PreProjeto::ESTADO_ATIVO){
                    $corpoEmail = '<p>O pedido de desarquivamento referente à proposta supracitada foi aceito.  A proposta será desarquivada e seguirá em análise. Dessa forma, acompanhe diariamente a proposta no sistema em virtude de novas diligências e comunicados</p>';

                } else {
                    $corpoEmail .= '<p>O pedido de desarquivamento referente à proposta supracitada não foi aceito pelo seguinte motivo:<p/></br>';
                    $corpoEmail .= "<p>{$Avaliacao}</p></br>";
                    $corpoEmail .= '<p>Salientamos que o proponente poderá inscrever e enviar novamente a mesma proposta ao MinC desde que observada a restrição contida na alínea "c", inciso I do artigo 23 da Instrução Normativa nº 05/2017 do Ministério da Cultura.</p></br>';
                }

                $email = new StdClass();
                $email->text = $corpoEmail;
                $email->to = $agente->current()->EmailAgente;
                $email->subject = "SALIC - Desarquivamento da proposta nº: " . $idPreProjeto;

                $this->events->trigger('email', $this, $email);
            }
        } catch(Exception $e){
            $message = $e->getMessage();
            $success = false;
        }

        $this->_helper->json(
            [
                'data' => $data,
                'success' => $success,
                'message' => $message
            ]
        );
    }

    private function email($texto)
    {
        return function($e) {
            $email = $e->getParams();
            $config = new Zend_Config_Ini(APPLICATION_PATH .'/configs/application.ini', APPLICATION_ENV);
            $emailDefault = $config->mail->default->toArray();
            $config = $config->mail->transport->toArray();

            $transport = new Zend_Mail_Transport_Smtp($config['host'], $config);
            $mail = new Zend_Mail();

            $mail->setBodyHtml($email->text);
            $mail->setFrom($emailDefault['email'], 'Salic');
            $mail->addTo($email->to);
            $mail->setSubject($email->subject);
            $mail->send($transport);
        };
    }

    public function listarSolicitacoesAguardandoAvaliacaoAction()
    {
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');
        $search = $this->getRequest()->getParam('search');

        $order = ($order[0]['dir'] != 1) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : ["idpreprojeto desc"];

        $tblPreProjetoArquivado = new Proposta_Model_PreProjetoArquivado();

        $rsPreProjetoArquivado = $tblPreProjetoArquivado->listarSolicitacoes(
            ['stDecisao ?' => new Zend_Db_Expr('IS NULL')],
            $order,
            $start,
            $length,
            $search
        );

        $aux = array();
        if (!empty($rsPreProjetoArquivado)) {
            foreach ($rsPreProjetoArquivado as $key => $proposta) {
                foreach ($proposta as $coluna => $valor){
                    $aux[$key][$coluna] = utf8_encode($valor);
                }
            }
            $totalData = $tblPreProjetoArquivado->listarSolicitacoes(
                ['stDecisao ?' => new Zend_Db_Expr('IS NULL')],
                null,
                null,
                null,
                $search
            );
            $recordsTotal = count($totalData);

            $recordsFiltered = $recordsTotal;
        }

        $this->_helper->json(array(
            "data" => !empty($aux) ? $aux : 0,
            'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0));
    }

    public function listarSolicitacoesAprovadasAction()
    {
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');
        $search = $this->getRequest()->getParam('search');

        $order = ($order[0]['dir'] != 1) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : ["idpreprojeto desc"];

        $tblPreProjetoArquivado = new Proposta_Model_PreProjetoArquivado();

        $rsPreProjetoArquivado = $tblPreProjetoArquivado->listarSolicitacoes(
            ['stDecisao = ?' => Proposta_Model_PreProjeto::ESTADO_ATIVO],
            $order,
            $start,
            $length,
            $search
        );

        $aux = array();
        if (!empty($rsPreProjetoArquivado)) {
            foreach ($rsPreProjetoArquivado as $key => $proposta) {
                foreach ($proposta as $coluna => $valor){
                    $aux[$key][$coluna] = utf8_encode($valor);
                }
            }
            $totalData = $tblPreProjetoArquivado->listarSolicitacoes(
                ['stDecisao = ?' => Proposta_Model_PreProjeto::ESTADO_ATIVO],
                null,
                null,
                null,
                $search
            );
            $recordsTotal = count($totalData);

            $recordsFiltered = $recordsTotal;
        }

        $this->_helper->json(array(
            "data" => !empty($aux) ? $aux : 0,
            'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0));
    }

    public function listarSolicitacoesReprovadasAction()
    {
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');
        $search = $this->getRequest()->getParam('search');

        $order = ($order[0]['dir'] != 1) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : ["idpreprojeto desc"];

        $tblPreProjetoArquivado = new Proposta_Model_PreProjetoArquivado();

        $rsPreProjetoArquivado = $tblPreProjetoArquivado->listarSolicitacoes(
            ['stDecisao = ?' => Proposta_Model_PreProjeto::ESTADO_ARQUIVADO],
            $order,
            $start,
            $length,
            $search
        );

        $aux = array();
        if (!empty($rsPreProjetoArquivado)) {
            foreach ($rsPreProjetoArquivado as $key => $proposta) {
                foreach ($proposta as $coluna => $valor){
                    $aux[$key][$coluna] = utf8_encode($valor);
                }
            }
            $totalData = $tblPreProjetoArquivado->listarSolicitacoes(
                ['stDecisao = ?' => Proposta_Model_PreProjeto::ESTADO_ARQUIVADO],
                null,
                null,
                null,
                $search
            );
            $recordsTotal = count($totalData);

            $recordsFiltered = $recordsTotal;
        }

        $this->_helper->json(array(
            "data" => !empty($aux) ? $aux : 0,
            'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0));
    }
}
