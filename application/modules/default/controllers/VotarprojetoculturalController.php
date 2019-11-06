<?php

class VotarProjetoCulturalController extends MinC_Controller_Action_Abstract
{
    private $bln_readequacao = "false";

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // titulo da pagina
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $Usuario = new UsuarioDAO(); // objeto usuario
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuario esteja autenticado
            // verifica as permissoes
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 133;
            $PermissoesGrupo[] = 118;
            $PermissoesGrupo[] = 119;
            $PermissoesGrupo[] = 120;
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo esta no array de permissoes
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &agrave;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgaos e grupos do usuario (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visao
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usuario para a visao
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuario para a visao
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a visao
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o orgao ativo do usuario para a visao
        } // fecha if
        else {
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew

        /**** CODIGO DE READEQUACAO ****/
        $this->view->bln_readequacao = "false";

        $idpronac = null;
        $idpronac = $this->_request->getParam("idpronac");
        //VERIFICA SE O PROJETO ESTA NA FASE DE READEQUACAO
        /*if(!empty($idpronac)){
            $tbPedidoAlteracao = new tbPedidoAlteracaoProjeto();
            $arrBusca = array();
            $arrBusca['pa.idPronac = ?']          = $idpronac;
            $arrBusca['pa.stPedidoAlteracao = ?'] = 'I'; //pedido enviado pelo proponente
            $arrBusca['pa.siVerificacao = ?']     = '1';
            $arrBusca['paxta.tpAlteracaoProjeto = ?']='10'; //tipo Readequacao de Itens de Custo
            $rsPedidoAlteraco = $tbPedidoAlteracao->buscarPedidoAlteracaoPorTipoAlteracao($arrBusca, array('dtSolicitacao DESC'))->current();
            if(!empty($rsPedidoAlteraco)){
                $this->bln_readequacao = "true";
                $this->view->bln_readequacao = "true";
            }
        }*/
        /**** fim - CODIGO DE READEQUACAO ****/
    }

    // fecha metodo init()

    public function parecerconsolidadoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idpronac = $_POST['idpronac'];
        $projeto = new Projetos();
        $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $planilhaprojeto = new PlanilhaProjeto();
        $planilhaAprovacao = new PlanilhaAprovacao();
        $tblParecer = new Parecer_Model_DbTable_Parecer();
        $pt = new Pauta();
        $analiseaprovacao = new AnaliseAprovacao();
        $buscarPronac = $projeto->buscar(array('IdPRONAC = ?' => $idpronac))->current()->toArray();
        $idprojeto = $buscarPronac['idProjeto'];

        //antiga busca
        //$analiseparecer = $parecer->buscarParecer(array(1, 6), $idpronac);
        //nova busca
        $parecerAtivo = $tblParecer->buscar(array('idPronac=?' => $idpronac, 'stAtivo=?' => '1'))->current();
        $analiseparecer = $tblParecer->buscar(array('idTipoAgente in (?)' => array('1', '6'), 'TipoParecer=?' => $parecerAtivo->TipoParecer, 'idPronac=?' => $idpronac));

        $dadosparecerconsolidado = array();
        $buscarPauta = $pt->buscar(array('idPronac = ?' => $idpronac), array('dtEnvioPauta DESC'))->current()->toArray();

        $dadosparecerconsolidado['DtParecer'] = isset($analiseparecer[1]->DtParecer) ? $analiseparecer[1]->DtParecer : $analiseparecer[0]->DtParecer;
        $dadosparecerconsolidado['ParecerFavoravel'] = isset($analiseparecer[1]->ParecerFavoravel) ? $analiseparecer[1]->ParecerFavoravel : $analiseparecer[0]->ParecerFavoravel;
        $dadosparecerconsolidado['TipoParecer'] = isset($analiseparecer[1]->TipoParecer) ? $analiseparecer[1]->TipoParecer : $analiseparecer[0]->TipoParecer;

        $dadosparecerconsolidado['ParecerParecerista'] = $analiseparecer[0]->ResumoParecer;
        $dadosparecerconsolidado['ParecerComponente'] = isset($analiseparecer[1]->ResumoParecer) ? $analiseparecer[1]->ResumoParecer : ' ';
        $dadosparecerconsolidado['Envioplenaria'] = trim($buscarPauta['dsAnalise']) == '' ? 'N&atilde;o existe justificativa para o envio deste projeto para plen&aacute;ria' : $buscarPauta['dsAnalise'];

        $produtos = $analiseaprovacao->buscarAnaliseProduto('CO', $idpronac);
        $this->view->idpronac = $idpronac;
        $this->view->projeto = $buscarPronac;
        $this->view->ResultRealizarAnaliseProjeto = $dadosparecerconsolidado;

        /********** MODO ANTIGO ***************/
        //$fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
        //$outrasfontes = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
        //$valorplanilha = $planilhaAprovacao->somarPlanilhaAprovacao($idpronac, 206, 'CO');
        //$valorparecerista = $planilhaprojeto->somarPlanilhaProjeto($idpronac, false);
        //$this->view->fontesincentivo = $fonteincentivo['soma'];
        //$this->view->outrasfontes = $outrasfontes['soma'];
        //$this->view->valorproposta = $fonteincentivo['soma'] + $outrasfontes['soma'];
        //$this->view->valorcomponente = $valorplanilha['soma'];
        //$this->view->valorparecerista = $valorparecerista['soma'];
        /********** FIM - MODO ANTIGO ***************/

        /**** CODIGO DE READEQUACAO ****/

        /********** MODO NOVO ***************/
        //TRATANDO SOMA DE PROJETO QUANDO ESTE FOR DE READEQUACAO
        $arrWhereSomaPlanilha = array();
        $arrWhereSomaPlanilha['idPronac = ?'] = $idpronac;
        if ($this->bln_readequacao == "false") {
            $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
            $outrasfontes = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
            $valorparecerista = $planilhaprojeto->somarPlanilhaProjeto($idpronac, false);
            //$valorplanilha = $planilhaAprovacao->somarPlanilhaAprovacao($idpronac, 206, 'CO');
        } else {
            $arrWhereFontesIncentivo = $arrWhereSomaPlanilha;
            $arrWhereFontesIncentivo['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
            $arrWhereFontesIncentivo['tpPlanilha = ? '] = 'SR';
            $arrWhereFontesIncentivo['stAtivo = ? '] = 'N';
            $arrWhereFontesIncentivo['NrFonteRecurso = ? '] = '109';
            $arrWhereFontesIncentivo["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWhereFontesIncentivo["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
            $fonteincentivo = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereFontesIncentivo);

            $arrWhereOutrasFontes = $arrWhereSomaPlanilha;
            $arrWhereOutrasFontes['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
            $arrWhereOutrasFontes['tpPlanilha = ? '] = 'SR';
            $arrWhereOutrasFontes['stAtivo = ? '] = 'N';
            $arrWhereOutrasFontes['NrFonteRecurso <> ? '] = '109';
            $arrWhereOutrasFontes["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWhereOutrasFontes["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
            $outrasfontes = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereOutrasFontes);

            $arrWherePlanilhaPA = $arrWhereSomaPlanilha;
            $arrWherePlanilhaPA['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
            $arrWherePlanilhaPA['tpPlanilha = ? '] = 'PA';
            $arrWherePlanilhaPA['stAtivo = ? '] = 'N';
            $arrWherePlanilhaPA['NrFonteRecurso = ? '] = '109';
            $arrWherePlanilhaPA["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWherePlanilhaPA["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
            $valorparecerista = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWherePlanilhaPA);
        }

        $arrWhereSomaPlanilha = array();
        $arrWhereSomaPlanilha['idPronac = ?'] = $idpronac;
        $arrWhereSomaPlanilha['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
        $arrWhereSomaPlanilha['tpPlanilha = ? '] = 'CO';
        $arrWhereSomaPlanilha['NrFonteRecurso = ? '] = '109';
        $arrWhereSomaPlanilha['stAtivo = ? '] = 'S';
        $valorplanilha = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

        $this->view->fontesincentivo = $fonteincentivo['soma'];
        $this->view->outrasfontes = $outrasfontes['soma'];
        $this->view->valorproposta = $fonteincentivo['soma'] + $outrasfontes['soma'];
        $this->view->valorcomponente = $valorplanilha['soma'];
        $this->view->valorparecerista = $valorparecerista['soma'];
        /***************** FIM  - MODO NOVO ********************/

        /**** fim - CODIGO DE READEQUACAO ****/

        $this->view->ResultProduto = $produtos;
        $verificaEnquadramento = RealizarAnaliseProjetoDAO::verificaEnquadramento($idpronac, 'CO');
        if (count($verificaEnquadramento) > 0) {
            if ($verificaEnquadramento[0]->stArtigo18 == true) {
                $this->view->enquadramento = 'Artigo 18';
            } elseif ($verificaEnquadramento[0]->stArtigo26 == true) {
                $this->view->enquadramento = 'Artigo 26';
            } else {
                $this->view->enquadramento = 'NAO ENQUADRADO';
            }
        } else {
            $this->view->enquadramento = 'NAO ENQUADRADO';
        }
    }

    public function analisedecontaAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $planilhaaprovacao = new PlanilhaAprovacao();
        $pt = new Pauta();
        $tblPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $projeto = new Projetos();
        $idpronac = $this->_request->getParam("idpronac");
        $buscarprojeto = $projeto->buscar(array('IdPRONAC = ?' => $idpronac))->current()->toArray();

        if ($this->bln_readequacao == "false") {
            $buscarAnaliseConta = $planilhaaprovacao->buscarAnaliseConta($idpronac, 'CO', array('pap.stAtivo=?' => 'S'));
            // ===== TOTAL VALOR REDUZIDO E TOTAL DE ITENS =====
            $itemReduzido = false;
            $itemRetirado = false;
            $totalValorReduzido = 0;
            $totalItemReduzido = 0;

            $totalValorRetirado = 0;
            $totalItemRetirado = 0;
            $valores['reduzido'] = array();
            $valores['retirado'] = array();
            foreach ($buscarAnaliseConta as $b) {
                $valorproponente = ($b->qtdSolicitado * $b->ocoSolicitado * $b->vlSolicitado);
                $valorcomponente = ($b->ocorrenciaRelator * $b->vlunitarioRelator * $b->qtdRelator);
                $valorparecerista = ($b->ocoParecer * $b->vlParecer * $b->qtdParecer);
                if ($valorcomponente < $valorproponente and $valorcomponente != 0) {
                    $valores['reduzido'][$totalItemReduzido]['idPlanilhaAprovacao'] = $b->idPlanilhaAprovacao;
                    $valores['reduzido'][$totalItemReduzido]['nrFonteRecurso'] = $b->nrFonteRecurso;
                    $valores['reduzido'][$totalItemReduzido]['idProduto'] = $b->idProduto;
                    $valores['reduzido'][$totalItemReduzido]['item'] = $b->Item;
                    $valores['reduzido'][$totalItemReduzido]['idEtapa'] = $b->idEtapa;
                    $valores['reduzido'][$totalItemReduzido]['Etapa'] = $b->Etapa;
                    $valores['reduzido'][$totalItemReduzido]['Produto'] = $b->produto;
                    $valores['reduzido'][$totalItemReduzido]['vlreduzidoComp'] = $valorproponente - $valorcomponente;
                    $valores['reduzido'][$totalItemReduzido]['VlReduzidoParecerista'] = $valorparecerista - $valorproponente;

                    $valores['reduzido'][$totalItemReduzido]['vltotalsolicitado'] = $valorproponente;
                    $valores['reduzido'][$totalItemReduzido]['UnidadeProposta'] = $b->UnidadeProposta;
                    $valores['reduzido'][$totalItemReduzido]['qtdSolicitado'] = $b->qtdSolicitado;
                    $valores['reduzido'][$totalItemReduzido]['ocoSolicitado'] = $b->ocoSolicitado;
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioSolicitado'] = $b->vlSolicitado;
                    $valores['reduzido'][$totalItemReduzido]['diasSolicitado'] = $b->diasSolicitado;

                    $valores['reduzido'][$totalItemReduzido]['idUnidade'] = $b->idUnidade;
                    $valores['reduzido'][$totalItemReduzido]['Unidade'] = $b->Unidade;
                    $valores['reduzido'][$totalItemReduzido]['diasRelator'] = $b->diasRelator;
                    $valores['reduzido'][$totalItemReduzido]['ocorrenciaRelator'] = $b->ocorrenciaRelator;
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioRelator'] = $b->vlunitarioRelator;
                    $valores['reduzido'][$totalItemReduzido]['diasRelator'] = $b->diasRelator;
                    $valores['reduzido'][$totalItemReduzido]['qtdRelator'] = $b->qtdRelator;
                    $valores['reduzido'][$totalItemReduzido]['vltotalcomponente'] = $valorcomponente;
                    $valores['reduzido'][$totalItemReduzido]['justcomponente'] = $b->JSComponente;

                    $valores['reduzido'][$totalItemReduzido]['UnidadeProjeto'] = $b->UnidadeProposta;
                    $valores['reduzido'][$totalItemReduzido]['qtdParecer'] = $b->qtdParecer;
                    $valores['reduzido'][$totalItemReduzido]['ocoParecer'] = $b->ocoParecer;
                    $valores['reduzido'][$totalItemReduzido]['diasParecerista'] = $b->diasParecerista;
                    $valores['reduzido'][$totalItemReduzido]['vltotalparecerista'] = $valorparecerista;
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioparecerista'] = $b->vlParecer;
                    $valores['reduzido'][$totalItemReduzido]['justparecerista'] = $b->JSParecerista;

                    $itemReduzido = true;
                    $reduzido = $valorproponente - $valorcomponente;
                    $totalValorReduzido += (float)$reduzido;
                    $totalItemReduzido++;
                }
                if ($valorcomponente == 0 and $valorproponente > 0) {
                    $valores['retirado'][$totalItemRetirado]['idPlanilhaAprovacao'] = $b->idPlanilhaAprovacao;
                    $valores['retirado'][$totalItemRetirado]['nrFonteRecurso'] = $b->nrFonteRecurso;
                    $valores['retirado'][$totalItemRetirado]['idProduto'] = $b->idProduto;
                    $valores['retirado'][$totalItemRetirado]['item'] = $b->Item;
                    $valores['retirado'][$totalItemRetirado]['idEtapa'] = $b->idEtapa;
                    $valores['retirado'][$totalItemRetirado]['Etapa'] = $b->Etapa;
                    $valores['retirado'][$totalItemRetirado]['Produto'] = $b->produto;
                    $valores['retirado'][$totalItemRetirado]['vlretiradoComp'] = $valorproponente - $valorcomponente;
                    $valores['retirado'][$totalItemRetirado]['VlretiradoParecerista'] = $valorparecerista - $valorproponente;

                    $valores['retirado'][$totalItemRetirado]['vltotalsolicitado'] = $valorproponente;
                    $valores['retirado'][$totalItemRetirado]['UnidadeProposta'] = $b->UnidadeProposta;
                    $valores['retirado'][$totalItemRetirado]['qtdSolicitado'] = $b->qtdSolicitado;
                    $valores['retirado'][$totalItemRetirado]['ocoSolicitado'] = $b->ocoSolicitado;
                    $valores['retirado'][$totalItemRetirado]['vlunitarioSolicitado'] = $b->vlSolicitado;
                    $valores['retirado'][$totalItemRetirado]['diasSolicitado'] = $b->diasSolicitado;

                    $valores['retirado'][$totalItemRetirado]['idUnidade'] = $b->idUnidade;
                    $valores['retirado'][$totalItemRetirado]['Unidade'] = $b->Unidade;
                    $valores['retirado'][$totalItemRetirado]['diasRelator'] = $b->diasRelator;
                    $valores['retirado'][$totalItemRetirado]['qtdRelator'] = $b->qtdRelator;
                    $valores['retirado'][$totalItemRetirado]['ocorrenciaRelator'] = $b->ocorrenciaRelator;
                    $valores['retirado'][$totalItemRetirado]['vlunitarioRelator'] = $b->vlunitarioRelator;
                    $valores['retirado'][$totalItemRetirado]['diasRelator'] = $b->diasRelator;
                    $valores['retirado'][$totalItemRetirado]['vltotalcomponente'] = $valorcomponente;
                    $valores['retirado'][$totalItemRetirado]['justcomponente'] = $b->JSComponente;

                    $valores['retirado'][$totalItemRetirado]['UnidadeProjeto'] = $b->UnidadeProposta;
                    $valores['retirado'][$totalItemRetirado]['qtdParecer'] = $b->qtdParecer;
                    $valores['retirado'][$totalItemRetirado]['ocoParecer'] = $b->ocoParecer;
                    $valores['retirado'][$totalItemRetirado]['diasParecerista'] = $b->diasParecerista;
                    $valores['retirado'][$totalItemRetirado]['vltotalparecerista'] = $valorparecerista;
                    $valores['retirado'][$totalItemRetirado]['vlunitarioparecerista'] = $b->vlParecer;
                    $valores['retirado'][$totalItemRetirado]['justparecerista'] = $b->JSParecerista;

                    $itemRetirado = true;
                    $retirado = $valorproponente - $valorcomponente;
                    $totalValorRetirado += (float)$retirado;
                    $totalItemRetirado++;
                }
            }
        } else {

            /**** CODIGO DE READEQUACAO ****/
            $buscarplanilhaCO = $planilhaaprovacao->buscarAnaliseContaPlanilhaAprovacao($idpronac, 'CO', array('pap.stAtivo=?' => 'S'));
            $buscarAnaliseConta = array();
            $cont = 0;
            foreach ($buscarplanilhaCO as $resuplanilha) {
                $buscarAnaliseConta[$cont]['qtdRelator'] = $resuplanilha->qtItem;
                $buscarAnaliseConta[$cont]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                $buscarAnaliseConta[$cont]['diasRelator'] = $resuplanilha->qtDias;
                $buscarAnaliseConta[$cont]['ocorrenciaRelator'] = $resuplanilha->nrOcorrencia;
                $buscarAnaliseConta[$cont]['vlunitarioRelator'] = $resuplanilha->vlUnitario;
                $buscarAnaliseConta[$cont]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $buscarAnaliseConta[$cont]['idProduto'] = $resuplanilha->idProduto;
                $buscarAnaliseConta[$cont]['idUnidade'] = $resuplanilha->idUnidade;
                $buscarAnaliseConta[$cont]['idEtapa'] = $resuplanilha->idEtapa;
                $buscarAnaliseConta[$cont]['JSComponente'] = $resuplanilha->dsJustificativa;

                $buscarAnaliseConta[$cont]['Unidade'] = $resuplanilha->Unidade;
                $buscarAnaliseConta[$cont]['Item'] = $resuplanilha->Item;
                $buscarAnaliseConta[$cont]['Etapa'] = $resuplanilha->Etapa;
                $buscarAnaliseConta[$cont]['produto'] = $resuplanilha->produto;
                $cont++;
            }

            /******** Planilha aprovacao SR (Proponente - solicitada) ****************/
            $arrBuscaPlanilha = array();
            $arrBuscaPlanilha["pap.stAtivo = ? "] = 'N';
            $arrBuscaPlanilha["pap.idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')"] = '(?)';

            $resuplanilha = null;
            $cont = 0;
            $buscarplanilhaSR = $planilhaaprovacao->buscarAnaliseContaPlanilhaAprovacao($idpronac, 'SR', $arrBuscaPlanilha);
            foreach ($buscarplanilhaSR as $resuplanilha) {
                $buscarAnaliseConta[$cont]['qtdSolicitado'] = $resuplanilha->qtItem;
                $buscarAnaliseConta[$cont]['ocoSolicitado'] = $resuplanilha->nrOcorrencia;
                $buscarAnaliseConta[$cont]['vlSolicitado'] = $resuplanilha->vlUnitario;
                $buscarAnaliseConta[$cont]['diasSolicitado'] = $resuplanilha->qtDias;
                $buscarAnaliseConta[$cont]['UnidadeProposta'] = $resuplanilha->Unidade;
                $cont++;
            }

            /******** Planilha aprovacao PA (Parecerista) ****************/
            $resuplanilha = null;
            $cont = 0;
            $buscarplanilhaPA = $planilhaaprovacao->buscarAnaliseContaPlanilhaAprovacao($idpronac, 'PA', $arrBuscaPlanilha);
            foreach ($buscarplanilhaPA as $resuplanilha) {
                $buscarAnaliseConta[$cont]['qtdParecer'] = $resuplanilha->qtItem;
                $buscarAnaliseConta[$cont]['ocoParecer'] = $resuplanilha->nrOcorrencia;
                $buscarAnaliseConta[$cont]['vlParecer'] = $resuplanilha->vlUnitario;
                $buscarAnaliseConta[$cont]['JSParecerista'] = $resuplanilha->dsJustificativa;
                $buscarAnaliseConta[$cont]['diasParecerista'] = $resuplanilha->qtDias;
                $buscarAnaliseConta[$cont]['UnidadeProjeto'] = $resuplanilha->Unidade;
                $cont++;
            }

            /********************************************************************************/

            // ===== TOTAL VALOR REDUZIDO E TOTAL DE ITENS =====
            $itemReduzido = false;
            $itemRetirado = false;
            $totalValorReduzido = 0;
            $totalItemReduzido = 0;

            $totalValorRetirado = 0;
            $totalItemRetirado = 0;
            $valores['reduzido'] = array();
            $valores['retirado'] = array();

            foreach ($buscarAnaliseConta as $b) {
                $valorproponente = ($b['qtdSolicitado'] * $b['ocoSolicitado'] * $b['vlSolicitado']);
                $valorcomponente = ($b['ocorrenciaRelator'] * $b['vlunitarioRelator'] * $b['qtdRelator']);
                $valorparecerista = ($b['ocoParecer'] * $b['vlParecer'] * $b['qtdParecer']);
                if ($valorcomponente < $valorproponente and $valorcomponente != 0) {
                    $valores['reduzido'][$totalItemReduzido]['idPlanilhaAprovacao'] = $b['idPlanilhaAprovacao'];
                    $valores['reduzido'][$totalItemReduzido]['nrFonteRecurso'] = $b['nrFonteRecurso'];
                    $valores['reduzido'][$totalItemReduzido]['idProduto'] = $b['idProduto'];
                    $valores['reduzido'][$totalItemReduzido]['item'] = $b['Item'];
                    $valores['reduzido'][$totalItemReduzido]['idEtapa'] = $b['idEtapa'];
                    $valores['reduzido'][$totalItemReduzido]['Etapa'] = $b['Etapa'];
                    $valores['reduzido'][$totalItemReduzido]['Produto'] = $b['produto'];
                    $valores['reduzido'][$totalItemReduzido]['vlreduzidoComp'] = $valorproponente - $valorcomponente;
                    $valores['reduzido'][$totalItemReduzido]['VlReduzidoParecerista'] = $valorparecerista - $valorproponente;

                    $valores['reduzido'][$totalItemReduzido]['vltotalsolicitado'] = $valorproponente;
                    $valores['reduzido'][$totalItemReduzido]['UnidadeProposta'] = $b['UnidadeProposta'];
                    $valores['reduzido'][$totalItemReduzido]['qtdSolicitado'] = $b['qtdSolicitado'];
                    $valores['reduzido'][$totalItemReduzido]['ocoSolicitado'] = $b['ocoSolicitado'];
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioSolicitado'] = $b['vlSolicitado'];
                    $valores['reduzido'][$totalItemReduzido]['diasSolicitado'] = $b['diasSolicitado'];

                    $valores['reduzido'][$totalItemReduzido]['idUnidade'] = $b['idUnidade'];
                    $valores['reduzido'][$totalItemReduzido]['Unidade'] = $b['Unidade'];
                    $valores['reduzido'][$totalItemReduzido]['diasRelator'] = $b['diasRelator'];
                    $valores['reduzido'][$totalItemReduzido]['ocorrenciaRelator'] = $b['ocorrenciaRelator'];
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioRelator'] = $b['vlunitarioRelator'];
                    $valores['reduzido'][$totalItemReduzido]['diasRelator'] = $b['diasRelator'];
                    $valores['reduzido'][$totalItemReduzido]['qtdRelator'] = $b['qtdRelator'];
                    $valores['reduzido'][$totalItemReduzido]['vltotalcomponente'] = $valorcomponente;
                    $valores['reduzido'][$totalItemReduzido]['justcomponente'] = $b['JSComponente'];

                    $valores['reduzido'][$totalItemReduzido]['UnidadeProjeto'] = $b['UnidadeProjeto'];
                    $valores['reduzido'][$totalItemReduzido]['qtdParecer'] = $b['qtdParecer'];
                    $valores['reduzido'][$totalItemReduzido]['ocoParecer'] = $b['ocoParecer'];
                    $valores['reduzido'][$totalItemReduzido]['diasParecerista'] = $b['diasParecerista'];
                    $valores['reduzido'][$totalItemReduzido]['vltotalparecerista'] = $valorparecerista;
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioparecerista'] = $b['vlParecer'];
                    $valores['reduzido'][$totalItemReduzido]['justparecerista'] = $b['JSParecerista'];

                    $itemReduzido = true;
                    $reduzido = $valorproponente - $valorcomponente;
                    $totalValorReduzido += (float)$reduzido;
                    $totalItemReduzido++;
                }
                if ($valorcomponente == 0 and $valorproponente > 0) {
                    $valores['retirado'][$totalItemRetirado]['idPlanilhaAprovacao'] = $b['idPlanilhaAprovacao'];
                    $valores['retirado'][$totalItemRetirado]['nrFonteRecurso'] = $b['nrFonteRecurso'];
                    $valores['retirado'][$totalItemRetirado]['idProduto'] = $b['idProduto'];
                    $valores['retirado'][$totalItemRetirado]['item'] = $b['Item'];
                    $valores['retirado'][$totalItemRetirado]['idEtapa'] = $b['idEtapa'];
                    $valores['retirado'][$totalItemRetirado]['Etapa'] = $b['Etapa'];
                    $valores['retirado'][$totalItemRetirado]['Produto'] = $b['produto'];
                    $valores['retirado'][$totalItemRetirado]['vlretiradoComp'] = $valorproponente - $valorcomponente;
                    $valores['retirado'][$totalItemRetirado]['VlretiradoParecerista'] = $valorparecerista - $valorproponente;

                    $valores['retirado'][$totalItemRetirado]['vltotalsolicitado'] = $valorproponente;
                    $valores['retirado'][$totalItemRetirado]['UnidadeProposta'] = $b['UnidadeProposta'];
                    $valores['retirado'][$totalItemRetirado]['qtdSolicitado'] = $b['qtdSolicitado'];
                    $valores['retirado'][$totalItemRetirado]['ocoSolicitado'] = $b['ocoSolicitado'];
                    $valores['retirado'][$totalItemRetirado]['vlunitarioSolicitado'] = $b['vlSolicitado'];
                    $valores['retirado'][$totalItemRetirado]['diasSolicitado'] = $b['diasSolicitado'];

                    $valores['retirado'][$totalItemRetirado]['idUnidade'] = $b['idUnidade'];
                    $valores['retirado'][$totalItemRetirado]['Unidade'] = $b['Unidade'];
                    $valores['retirado'][$totalItemRetirado]['diasRelator'] = $b['diasRelator'];
                    $valores['retirado'][$totalItemRetirado]['qtdRelator'] = $b['qtdRelator'];
                    $valores['retirado'][$totalItemRetirado]['ocorrenciaRelator'] = $b['ocorrenciaRelator'];
                    $valores['retirado'][$totalItemRetirado]['vlunitarioRelator'] = $b['vlunitarioRelator'];
                    $valores['retirado'][$totalItemRetirado]['diasRelator'] = $b['diasRelator'];
                    $valores['retirado'][$totalItemRetirado]['vltotalcomponente'] = $valorcomponente;
                    $valores['retirado'][$totalItemRetirado]['justcomponente'] = $b['JSComponente'];

                    $valores['retirado'][$totalItemRetirado]['UnidadeProjeto'] = $b['UnidadeProjeto'];
                    $valores['retirado'][$totalItemRetirado]['qtdParecer'] = $b['qtdParecer'];
                    $valores['retirado'][$totalItemRetirado]['ocoParecer'] = $b['ocoParecer'];
                    $valores['retirado'][$totalItemRetirado]['diasParecerista'] = $b['diasParecerista'];
                    $valores['retirado'][$totalItemRetirado]['vltotalparecerista'] = $valorparecerista;
                    $valores['retirado'][$totalItemRetirado]['vlunitarioparecerista'] = $b['vlParecer'];
                    $valores['retirado'][$totalItemRetirado]['justparecerista'] = $b['JSParecerista'];

                    $itemRetirado = true;
                    $retirado = $valorproponente - $valorcomponente;
                    $totalValorRetirado += (float)$retirado;
                    $totalItemRetirado++;
                }
            }//fecha foreach
        }//fecha if bln_readequacao

        $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();

        //ANTIGO MODELO DE SOMA
        //$buscarsomaaprovacao = $planilhaaprovacao->somarPlanilhaAprovacao($idpronac, 206, 'CO');
        //$buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto['idProjeto']);

        //NOVO MODELO DE SOMA
        /**********************************/
        $arrWhereSomaPlanilha = array();
        $arrWhereSomaPlanilha['idPronac = ?'] = $idpronac;
        $arrWhereSomaPlanilha['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
        $arrWhereSomaPlanilha['NrFonteRecurso = ? '] = '109';

        if ($this->bln_readequacao == "false") {
            //proponente
            $buscarsomaproposta = $tblPlanilhaProposta->somarPlanilhaProposta($buscarprojeto['idProjeto']);

            //componente
            $arrWhereSomaPlanilha['stAtivo = ? '] = 'S';
            $arrWhereSomaPlanilha['tpPlanilha = ? '] = 'CO';
            $buscarsomaaprovacao = $planilhaaprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
        } else {
            //proponente
            $arrWhereSomaSR = array();
            $arrWhereSomaSR = $arrWhereSomaPlanilha;
            $arrWhereSomaSR['tpPlanilha = ? '] = 'SR';
            $arrWhereSomaSR['stAtivo = ? '] = 'N';
            $arrWhereSomaSR["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWhereSomaSR["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
            $buscarsomaproposta = $planilhaaprovacao->somarItensPlanilhaAprovacao($arrWhereSomaSR);

            //componente
            $arrWhereSomaPlanilha['tpPlanilha = ? '] = 'CO';
            $arrWhereSomaPlanilha['stAtivo = ? '] = 'S';
            $buscarsomaaprovacao = $planilhaaprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
        }
        /************************************/
        /**** fim - CODIGO DE READEQUACAO ****/
        $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
        $this->view->analiseReduzido = $valores['reduzido'];
        $this->view->analiseRetirado = $valores['retirado'];
        $this->view->pronac = $buscarprojeto;
        $this->view->idpronac = $idpronac;
        $this->view->itemReduzido = $itemReduzido;
        $this->view->itemRetirado = $itemRetirado;

        $this->view->totValRed = $totalValorReduzido;
        $this->view->totItemRed = $totalItemReduzido;
        $this->view->totValRet = $totalValorRetirado;
        $this->view->totItemRet = $totalItemRetirado;

        $this->view->totalproponente = $buscarsomaproposta['soma'];
        $this->view->totalcomponente = $buscarsomaaprovacao['soma'];
    }

    /**
     * metodo para realizar a Analise de Conteudo
     * @access public
     * @param void
     * @return void
     */
    public function analisedeconteudoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idPronac = $_POST['idpronac'];
        $pt = new Pauta();
        $projeto = new Projetos();

        $buscarprojeto = $projeto->buscar(array('IdPRONAC = ?' => $idPronac))->current()->toArray();
        $analise = new AnaliseAprovacao();

        $buscaReadAprovacadoCnic = $pt->buscar(array('IdPRONAC = ?' => $idPronac, 'stAnalise = ?' => "AS"));
        // busca as informacoes de acordo com o id da planilha
        $buscar = $analise->buscarAnaliseProduto('CO', $idPronac);
        // manda para a visao
        $this->view->dados = $buscar;
        $this->view->dadosprojeto = $buscarprojeto;
        $this->view->qtdItens = count($buscar); // quantidade de itens
    }

    // fecha metodo analisedeconteudoAction()


    public function analisedecustosAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        // recebe os dados via get
        $idpronac = $this->_request->getParam("idpronac");
        $tblPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $tblPlanilhaProjeto = new PlanilhaProjeto();
        $tblPlanilhaAprovacao = new PlanilhaAprovacao();
        $tblProjetos = new Projetos();
        // busca os produtos do projeto
        /*$pt = new Pauta();
        $buscaReadAprovacadoCnic = $pt->buscar(array('IdPRONAC = ?' => $idpronac, 'stAnalise = ?' => "AS"));
        $tipoplanilha = $buscaReadAprovacadoCnic->count() > 0 ? 'SE' : 'CO';*/

        $rsPlanilhaAtual = $tblPlanilhaAprovacao->buscar(array('IdPRONAC = ?' => $idpronac), array('dtPlanilha DESC'))->current();
        $tipoplanilha = (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') ? 'SE' : 'CO';

        if ($this->bln_readequacao == "false") {
            $buscarplanilha = $tblPlanilhaAprovacao->buscarAnaliseCustos($idpronac, $tipoplanilha, array('PAP.stAtivo=?' => 'S'));

            $planilhaaprovacao = array();
            $count = 0;
            $fonterecurso = null;
            foreach ($buscarplanilha as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtitemcomp;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrocorrenciacomp;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlunitariocomp;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtdiascomp;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->VlSugeridoConselheiro;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativaConselheiro;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                $count++;
            }
            $buscarprojeto = $tblProjetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();
            //$buscarsomaaprovacao = $pa->somarPlanilhaAprovacao($idpronac, 206, $tipoplanilha);
            $buscarsomaproposta = $tblPlanilhaProposta->somarPlanilhaProposta($buscarprojeto->idProjeto);
            $buscarsomaprojeto = $tblPlanilhaProjeto->somarPlanilhaProjeto($idpronac);
        } else {

            /**** CODIGO DE READEQUACAO ****/
            $buscarplanilhaCO = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idpronac, 'CO', array('PAP.stAtivo=?' => 'S'));

            $planilhaaprovacao = array();
            $count = 0;
            $fonterecurso = null;
            foreach ($buscarplanilhaCO as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtItem;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrOcorrencia;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlUnitario;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtDias;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->vlTotal;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativa;
                //$planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                $count++;
            }

            /******** Planilha aprovacao SR (Proponente - solicitada) ****************/
            $arrBuscaPlanilha = array();
            $arrBuscaPlanilha["pap.stAtivo = ? "] = 'N';
            $arrBuscaPlanilha["pap.idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')"] = '(?)';

            $resuplanilha = null;
            $count = 0;
            $buscarplanilhaSR = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idpronac, 'SR', $arrBuscaPlanilha);

            foreach ($buscarplanilhaSR as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->qtDias;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->qtItem;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->nrOcorrencia;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->vlUnitario;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->vlTotal;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->dsJustificativa;

                $valorConselheiro = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'];
                $valorSolicitado = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'];
                $reducao = $valorConselheiro < $valorSolicitado ? 1 : 0;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $reducao;
                $count++;
            }

            /******** Planilha aprovacao PA (Parecerista) ****************/
            $resuplanilha = null;
            $count = 0;
            $buscarplanilhaPA = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idpronac, 'PA', $arrBuscaPlanilha);

            foreach ($buscarplanilhaPA as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->qtItem;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->nrOcorrencia;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->qtDias;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->vlUnitario;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->vlTotal;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativa;
                $count++;
            }

            $buscarprojeto = $tblProjetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();

            $arrWhereSomaPlanilha = array();
            $arrWhereSomaPlanilha['idPronac = ?'] = $idpronac;
            $arrWhereSomaPlanilha['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
            $arrWhereSomaPlanilha['NrFonteRecurso = ? '] = '109';
            $arrWhereSomaPlanilha['stAtivo = ? '] = 'N';
            $arrWhereSomaPlanilha["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWhereSomaPlanilha["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';

            $arrWhereSomaPlanilha['tpPlanilha = ? '] = 'SR';
            $buscarsomaproposta = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
            $arrWhereSomaPlanilha['tpPlanilha = ? '] = 'PA';
            $buscarsomaprojeto = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
        }//feacha if bln_readequacao
        /**** fim - CODIGO DE READEQUACAO ****/

        $arrWhereSomaPlanilha = array();
        $arrWhereSomaPlanilha['idPronac = ?'] = $idpronac;
        $arrWhereSomaPlanilha['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
        $arrWhereSomaPlanilha['tpPlanilha = ? '] = 'CO';
        $arrWhereSomaPlanilha['NrFonteRecurso = ? '] = '109';
        $arrWhereSomaPlanilha['stAtivo = ? '] = 'S';
        $buscarsomaaprovacao = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

        $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
        $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
        $this->view->planilha = $planilhaaprovacao;
        $this->view->projeto = $buscarprojeto;
        $this->view->totalcomponente = $buscarsomaaprovacao['soma'];
        $this->view->totalparecerista = $buscarsomaprojeto['soma'];
        $this->view->totalproponente = $buscarsomaproposta['soma'];
    }

    // fecha metodo analisedecustosAction()

    public function dadosproponenteAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idpronac = $this->_request->getParam("idpronac");

        $geral = new ProponenteDAO();
        $tbdados = $geral->buscarDadosProponente($idpronac);
        $this->view->dados = $tbdados;

        $tbemail = $geral->buscarEmail($idpronac);
        $this->view->email = $tbemail;

        $tbtelefone = $geral->buscarTelefone($idpronac);
        $this->view->telefone = $tbtelefone;

        $tbDirigentes = $geral->buscarDirigentes($idpronac);
        $this->view->dirigentes = $tbDirigentes;

        $this->view->CgcCpf = $tbdados[0]->CgcCpf;

        /*$tbarquivados = $geral->buscarArquivados($idpronac);
        $this->view->arquivados = $tbarquivados;

        $tbinativos = $geral->buscarInativos($tbdados[0]->CgcCpf);
        $this->view->inativos = $tbinativos;

        $tbativos = $geral->buscarAtivos($tbdados[0]->CgcCpf);
        $this->view->ativos = $tbativos;
        $this->view->idpronac = $idpronac;*/
    }

    public function votacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idagente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];
        if (isset($_POST['voto'])) {
            try {
                $votacao = new Votacao();
                $reuniao = new Reuniao();
                $raberta = $reuniao->buscarReuniaoAberta();
                $reuniaoaberta = $raberta['idNrReuniao'];

                //$dsjustificativa = $_POST['dsjustificativa'];
                $voto = $_POST['voto'];
                $dtvoto = date('Y-m-d H:i:s');
                $idpronac = explode('_', $_POST['idpronac']);
                $idpronac = $idpronac[0];

                $dadosupdate = array(
                    'dtVoto' => $dtvoto,
                    'stVoto' => $voto
                    //'dsjustificativa' => $dsjustificativa
                );
                $where = "IdPRONAC = $idpronac and idAgente = $idagente and idNrReuniao = $reuniaoaberta";
                $votar = $votacao->alterar($dadosupdate, $where);
                $this->_helper->json(array('error' => false));
            } catch (Exception $e) {
                $this->_helper->json(array('error' => true, 'descricao' => $e->getMessage()));
            }
            $this->_helper->viewRenderer->setNoRender(true);
        }
        $idpronac = $this->_request->getParam("idpronac");
        $dpc = new DistribuicaoProjetoComissao();
        $projeto = new Projetos();
        $buscarProjeto = $projeto->buscar(array('IdPRONAC = ?' => $idpronac))->current()->toArray();
        $buscarcomponente = $dpc->AgenteDistribuido($idpronac)->current();
        $this->view->componente = $buscarcomponente->nome;
        $this->view->idpronac = $idpronac;
        $this->view->dadosprojeto = $buscarProjeto;
    }

    public static function VerificarCpfCnpj($dado)
    {
        $qtdcarecteres = strlen($dado);
        switch ($qtdcarecteres) {
            case 11:
                $retorno = Mascara::addMaskCPF($dado);
                break;
            case 14:
                $retorno = Mascara::addMaskCNPJ($dado);
                break;
        }
        return $retorno;
    }

    public function verificarvotacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $this->view->idpronac = $_POST['idpronac'];
        $idpronac = explode('_', $_POST['idpronac']);

        $tipoReadequacao = null;
        if (isset($idpronac[1]) && !empty($idpronac[1])) {
            $tipoReadequacao = $idpronac[1];
        }
        $idpronac = $idpronac[0];

        $votacao = new Votacao();
        $reuniao = new Reuniao();

        $dadosreuniaoaberta = $reuniao->buscarReuniaoAberta();
        $reuniaoaberta = $dadosreuniaoaberta['idNrReuniao'];

        $aprovados = $votacao->resultadovotacao($reuniaoaberta, $idpronac, 'A', $tipoReadequacao);
        $indeferidos = $votacao->resultadovotacao($reuniaoaberta, $idpronac, 'I', $tipoReadequacao);
        $abstencao = $votacao->resultadovotacao($reuniaoaberta, $idpronac, 'B', $tipoReadequacao);
        $total = $votacao->resultadovotacao($reuniaoaberta, $idpronac, null, $tipoReadequacao);
        $descricao = $votacao->votantesjustificativavoto($reuniaoaberta, $idpronac, $tipoReadequacao);
        if ($descricao->count() <= 0) {
            $valores = array(
                'dsjustificativa' => utf8_encode("<table class='tabela'><tr class='centro'><td><font color='red'><b>A vota��o deste projeto foi cancelada pelo presidente da mesa.</b></font></td></tr></table> <script>window.location.reload();</script>"),
                'aprovados' => '',
                'indeferidos' => '',
                'abstencao' => '',
                'total' => '',
                'totalvotos' => '',
            );
            $json = json_encode($valores);
            echo $json;
            $this->_helper->viewRenderer->setNoRender(true);
        }
        $justificativa = "<table class='tabela'>";
        $justificativa .= "<tr class='centro'>";
        $justificativa .= "<th>Componente</th>";
        $justificativa .= "<th>Voto</th>";
        //$justificativa .= "<th>Justificativa</th>";
        $justificativa .= "</tr>";
        foreach ($descricao as $resultado) {
            $justificativa .= "<tr>";
            if ($resultado->justificativa == null) {
                if ($resultado->stVoto == 'I') {
                    $justificativa .= "<td><strong> <font color='red'>" . $resultado->nome . "</font></strong></td>";
                } else {
                    $justificativa .= "<td><strong> " . $resultado->nome . "</strong></td>";
                }
                $justificativa .= "<td>";
                if ($resultado->stVoto == 'A') {
                    $justificativa .= "Aprovar projeto cultural";
                } elseif ($resultado->stVoto == 'I') {
                    $justificativa .= "<font color='red'>Indeferir Projeto Cultural</font>";
                } elseif ($resultado->stVoto == 'B') {
                    $justificativa .= "Absteve o voto";
                } else {
                    $justificativa .= "Aguardando voto do componente";
                }
                $justificativa .= "</td>";
                //$justificativa .= "<td>Aguardando voto do componente<//td>";
            } /*else {
                if($resultado->stVoto == 'I'){
                    $justificativa .= "<td><strong> <font color='red'>" . $resultado->nome . "</font></strong></td>";
                }else{
                    $justificativa .= "<td><strong> " . $resultado->nome . "</strong></td>";
                }
                $justificativa .= "<td>";
                if ($resultado->stVoto == 'A') {
                    $justificativa .= "Aprovar projeto cultural";
                } else if ($resultado->stVoto == 'I') {
                    $justificativa .= "<font color='red'>Indeferir Projeto Cultural</font>";
                } else if ($resultado->stVoto == 'B') {
                    $justificativa .= "Absteve o voto";
                } else {
                    $justificativa .= "Aguardando voto do componente";
                }
                //$justificativa .= "<td>" . utf8_decode($resultado->justificativa) . "</td>";
            }*/
            $justificativa .= "</tr>";
        }
        $justificativa .= "<table>";
        $valores = array(
            'dsjustificativa' => utf8_encode($justificativa),
            'aprovados' => $aprovados['qtdvotos'],
            'indeferidos' => $indeferidos['qtdvotos'],
            'abstencao' => $abstencao['qtdvotos'],
            'total' => $total['qtdvotos'],
            'totalvotos' => $total['qtdvotos'],
        );
        $json = json_encode($valores);
        echo $json;
    }

    public function resultadovotoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $auth = Zend_Auth::getInstance(); // pega a autenticao
        $reuniao = new Reuniao();
        $reuniaoatual = $reuniao->buscarReuniaoAberta();
        $reuniaoatual = $reuniaoatual['idNrReuniao'];
        //$nrreuniaoatual = $reuniaoatual['NrReuniao'];
        $pauta = new Pauta();
        $votantes = new Votante();
        $parecer = new Parecer_Model_DbTable_Parecer();
        $tblProjetos = new Projetos();
        $pa = new PlanilhaAprovacao();
        $st = new Situacao();
        $dpc = new DistribuicaoProjetoComissao();

        if (isset($_POST['idpronac'])) {
            $idpronac = explode('_', $_POST['idpronac']);
            $idTipoReadequacao = null;
            if (isset($idpronac[1])) {
                $idTipoReadequacao = $idpronac[1];
            }
            $idpronac = $idpronac[0];

            $buscarvotante = $votantes->selecionarvotantes($reuniaoatual);
            $buscardadosprojeto = $pauta->pronacVotacaoAtual($reuniaoatual, $idpronac, $idTipoReadequacao);
            $buscarSituacao = $st->buscar(array('Codigo in (?)' => array('A13', 'A14', 'A16', 'A17', 'A20', 'A23', 'A24', 'D14', 'A41')));

            $qtdVotantes = $buscarvotante->count();
            $this->view->situacaoindeferimento = $buscarSituacao;
            $this->view->qtdVotantes = $qtdVotantes;
            $this->view->idpronac = $idpronac;
            if (isset($idpronac[1])) {
                $this->view->idpronac = $idpronac . '_' . $idTipoReadequacao;
            }
            $this->view->idTipoReadequacao = $idTipoReadequacao;
            $this->view->dadosprojeto = $buscardadosprojeto;
            $buscarcomponente = $dpc->AgenteDistribuido($idpronac)->current();
            $this->view->componente = isset($buscarcomponente) ? $buscarcomponente->nome : '';

            //verifica se o projeto e de plano anual
            $rsProjeto = $tblProjetos->buscar(array('idPronac=?' => $idpronac))->current();
            $tbPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $rsPreProjeto = $tbPreProjeto->buscar(array('idPreProjeto=?' => $rsProjeto->idProjeto))->current();
            $this->view->stPlanoAnual = $rsPreProjeto->stPlanoAnual;
        }
        /*
        //GRAVA CONSOLIDACAO DO VOTACAO
        if (isset($_POST['resultadovotacao']))
        {
            xd('para aqui');
            $this->_helper->viewRenderer->setNoRender(true);
            $tblConsolidacao = new Consolidacaovotacao();
            $tpresultadovotacao = $_POST['tpresultadovotacao'];
            $resultado = $_POST['resultadovotacao'];
            $idpronac = $_POST['dadosidpronac'];
            //$parecerSecretario = $_POST['parecerconsolidado'];
            $parecerSecretario = $_POST['parecerconsolidadoAtual']; //foi necessario essa alteracao pq o parecer nao estava sendo recuperado quando o salvamento era feito com ajax

            $where = "IdPRONAC = " . $idpronac . " and IdNrReuniao=" . $reuniaoatual;
            $pauta->alterar(array('stAnalise' => $resultado), $where);

            $dadosconsolidacao = array(
                'dsConsolidacao' => $parecerSecretario,
                'IdPRONAC' => $idpronac,
                'idNrReuniao' => $reuniaoatual
            );
            $tblConsolidacao->inserir($dadosconsolidacao);

            $arquivo = getcwd() . "/public/plenaria/votacao.txt";
            if (file_exists($arquivo)) {
                unlink($arquivo);
            }
            $situacao = null;

            if ($resultado == 'AS' and $_POST['situacao'] == null)
            {
                //TRATANDO SITUACAO DO PROJETO QUANDO ESTE FOR DE READEQUACAO
                if($this->bln_readequacao == "false"){
                    $situacao = 'D03';
                }else{
                    $situacao = 'D02';
                }

            } else if ($_POST['situacao'] != null) {
                $situacao = $_POST['situacao'];
            }

            if ($_POST['situacao'] != null)
            {
                $dtsituacao = date('Y-m-d H:i:s');
                $buscarsituacao = $st->listasituacao(array($situacao))->current()->toArray();
                $providencia = $_POST['situacao'] == null ? 'PROJETO APROVADO NA CNIC N ' . $nrreuniaoatual . ' - ' . $buscarsituacao['Descricao'] : 'PROJETO INDEFERIDO NA CNIC N ' . $nrreuniaoatual . ' - ' . $buscarsituacao['Descricao'];

                $where = "IdPRONAC = $idpronac";
                $dados = array(
                    "Situacao" => $situacao,
                    "DtSituacao" => date('Y-m-d H:i:s'),
                    "ProvidenciaTomada" => $providencia,
                );

                $tblProjetos->alterar($dados, $where);
                //$tblProjetos->alterarSituacao($idpronac, null, $situacao, $providencia);
            }

            $planilhaaprovacao = $pa->buscar(array("IdPRONAC = ?" => $idpronac, "stAtivo = ?" => 'S', "tpPlanilha = ?" => 'CO'));

            //Manteve o resultado igual
            if ($tpresultadovotacao == 1 and $resultado == 'AS')
            {
                $consolidacao = $parecer->buscar(array('IdPRONAC = ?' => $idpronac, 'stAtivo = ?' => 1))->current()->toArray();
                $consolidacao = $consolidacao['ResumoParecer'];

            }//Projeto deferido pelo componente a reprovado pela plenaria
            else if ($tpresultadovotacao == 2 and $resultado == 'AS')
            {
                $consolidacao = $_POST['parecerconsolidado'];

            }//Projeto indeferido pelo componente a aprovado pela plenaria
            else if ($tpresultadovotacao == 3 and $resultado == 'AS')
            {
                foreach ($planilhaaprovacao as $resu) {
                    $data = array(
                        'tpPlanilha' => 'SE',
                        'dtPlanilha' => date('Y-m-d H:i:s'),
                        'idPlanilhaProjeto' => $resu->idPlanilhaProjeto,
                        'idPlanilhaProposta' => $resu->idPlanilhaProposta,
                        'IdPRONAC' => $resu->IdPRONAC,
                        'idProduto' => $resu->idProduto,
                        'idEtapa' => $resu->idEtapa,
                        'idPlanilhaItem' => $resu->idPlanilhaItem,
                        'idUnidade' => $resu->idUnidade,
                        'qtItem' => $resu->qtItem,
                        'dsItem' => '',
                        'nrOcorrencia' => $resu->nrOcorrencia,
                        'vlUnitario' => $resu->vlUnitario,
                        'qtDias' => $resu->qtDias,
                        'tpDespesa' => $resu->tpDespesa,
                        'tpPessoa' => $resu->tpPessoa,
                        'nrContraPartida' => $resu->nrContraPartida,
                        'nrFonteRecurso' => $resu->nrFonteRecurso,
                        'idUFDespesa' => $resu->idUFDespesa,
                        'idMunicipioDespesa' => $resu->idMunicipioDespesa,
                        'dsJustificativa' => $resu->dsJustificativa,
                        'stAtivo' => 'S'
                    );
                    $inserirPlanilhaAprovacao = $pa->inserir($data);
                }
                $where = "IdPRONAC = $idpronac and tpPlanilha = 'CO' and stAtivo = 'S'";
                $dados = array('stAtivo' => 'N');
                $pa->alterar($dados, $where);

                $ana = new AnaliseAprovacao();
                $RanaliseConteudo = $ana->buscar(array("tpAnalise = ?" => 'CO', "IdPRONAC = ?" => $idpronac, 'idAnaliseAprovacaoPai is null' => null));
                foreach ($RanaliseConteudo as $resu) {
                    $data = array(
                        'tpAnalise' => 'SE',
                        'dtAnalise' => date('Y-m-d H:i:s'),
                        'idAnaliseConteudo' => $resu->idAnaliseConteudo,
                        'IdPRONAC' => $resu->IdPRONAC,
                        'idProduto' => $resu->idProduto,
                        'stLei8313' => $resu->stLei8313,
                        'stArtigo3' => $resu->stArtigo3,
                        'nrIncisoArtigo3' => $resu->nrIncisoArtigo3,
                        'dsAlineaArt3' => $resu->dsAlineaArt3,
                        'stArtigo18' => $resu->stArtigo18,
                        'dsAlineaArtigo18' => $resu->dsAlineaArtigo18,
                        'stArtigo26' => $resu->stArtigo26,
                        'stLei5761' => $resu->stLei5761,
                        'stArtigo27' => $resu->stArtigo27,
                        'stIncisoArtigo27_I' => $resu->stIncisoArtigo27_I,
                        'stIncisoArtigo27_II' => $resu->stIncisoArtigo27_II,
                        'stIncisoArtigo27_III' => $resu->stIncisoArtigo27_III,
                        'stIncisoArtigo27_IV' => $resu->stIncisoArtigo27_IV,
                        'stAvaliacao' => $resu->stAvaliacao,
                        'dsAvaliacao' => $resu->dsAvaliacao,
                        'idAnaliseAprovacaoPai' => $resu->idAnaliseAprovacao
                    );
                    $ana->inserir($data);
                }

                $dados = array('Situacao' => 'D01');
                $where = 'IdPRONAC = ' . $idpronac;
                $tblProjetos->alterar($dados, $where);
            }

            //INATIVA DISTRIBUICAO DESSE PROJETO PARA O COMPONENTE POIS SUA ANALIZE FOI FINALIZADA
            try{
                $tblDistribuicao = new tbDistribuicaoProjetoComissao();
                $tblDistribuicao->alterar(array('stDistribuicao' => 'I'), array('idPRONAC = ?'=>$idPronac));
            }// fecha try
            catch (Exception $e)
            {

                parent::message("Ocorreu um erro ao inativar a distribui��o desse Projeto feita ao Componente, mas as outras a��es foram realizadas com sucesso.", "gerenciarpautareuniao/gerenciaradministrativo", "ALERT");
            }
            echo "<script>msg();</script>";
        }*/
    }

    public function consolidarVotacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $auth = Zend_Auth::getInstance(); // pega a autenticao

        $post = Zend_Registry::get("post");
        $reuniao = new Reuniao();
        $reuniaoatual = $reuniao->buscarReuniaoAberta();
        $idReuniaoatual = $reuniaoatual['idNrReuniao'];
        $nrReuniaoatual = $reuniaoatual['NrReuniao'];
        $tpresultadovotacao = $_POST['tpresultadovotacao'];
        $resultado = $_POST['resultadovotacao'];
        $tpConsolidacaoVotacao = $_POST['tpconsolidacaovotacao'];
        $idTipoReadequacao = $_POST['resultadovotacao'];
        $parecerSecretario = Seguranca::tratarVarAjaxUFT8($_POST['parecerconsolidado']);

        //$idAbrangencia = $post->cod;
        $pauta = new Pauta();
        $votantes = new Votante();
        $parecer = new Parecer_Model_DbTable_Parecer();
        $tblProjetos = new Projetos();
        $pa = new PlanilhaAprovacao();
        $st = new Situacao();
        $dpc = new DistribuicaoProjetoComissao();

        $idPronac = explode('_', $post->idpronac);
        $idTipoReadequacao = null;
        if (isset($idPronac[1]) && !empty($idPronac[1])) {
            $idTipoReadequacao = $idPronac[1];
        }
        $idPronac = $idPronac[0];

        $idNrReuniao = $idReuniaoatual;
        $nrReuniao = $nrReuniaoatual;
        $tpResultadoVotacao = $tpresultadovotacao;
        $resultadoVotacao = $resultado;
        $dsParecerConsolidado = $parecerSecretario;
        $blnReadequacao = ($this->bln_readequacao == "false") ? 0 : 1;
        $situacao = ($_POST['situacao'] != null) ? $_POST['situacao'] : "NUL"; //a sp espera apenas 3 digitos para verificar se a situacao e null

        try {
            // executa a sp
            $sp = new paConsolidarProjetoVotadoNaCnic();
            $arr = $sp->consolidarVotacaoProjeto($idPronac, $idNrReuniao, $nrReuniao, $tpResultadoVotacao, $resultadoVotacao, $dsParecerConsolidado, $blnReadequacao, $situacao, $tpConsolidacaoVotacao, $idTipoReadequacao);

            if (!is_array($arr)) {
                //x('com erro');
                throw new Exception($sp);
            } else {
                //x('sem erro');
                if (count($arr) > 0 && $arr[0]->Tipo == 1) { //sucesso

                    /************** APAGA ARQUIVO DA VOTACAO DO PROJETO ********************/
                    $arquivo = getcwd() . "/public/plenaria/votacao.txt";
                    if (file_exists($arquivo)) {
                        unlink($arquivo);
                    }
                    $this->_helper->json(array('error' => false));
                    $this->_helper->viewRenderer->setNoRender(true);
                } else {
                    throw new Exception($sp);
                }
            }
        } // fecha try
        catch (Exception $e) {
            $this->_helper->json(array('error' => true, 'descricao' => "N&atilde;o foi poss&iacute;vel consolidar a vota&ccedil;&atilde;o do Projeto. <br />" . $e->getMessage()));
            $this->_helper->viewRenderer->setNoRender(true);
        }


        //GRAVA CONSOLIDACAO DO VOTACAO
        if (isset($_POST['resultadovotacao'])) {
            $this->_helper->viewRenderer->setNoRender(true);
            $tblConsolidacao = new Consolidacaovotacao();
            $tpresultadovotacao = $_POST['tpresultadovotacao'];
            $resultado = $_POST['resultadovotacao'];
            $parecerSecretario = Seguranca::tratarVarAjaxUFT8($_POST['parecerconsolidado']);
            //$idpronac           = $_POST['dadosidpronac'];
            //$parecerSecretario = $_POST['parecerconsolidadoAtual']; //foi necessario essa alteracao pq o parecer nao estava sendo recuperado quando o salvamento era feito com ajax

            try {
                /************** SETA VALOR FINAL DA VOTACAO DO PROJETO *****************/
                $where = "IdPRONAC = " . $idpronac . " and IdNrReuniao=" . $reuniaoatual;
                $pauta->alterar(array('stAnalise' => $resultado), $where);


                /************** INSERE DADOS DA CONSOLIDACAO ***************************/
                $dadosconsolidacao = array(
                    'dsConsolidacao' => $parecerSecretario,
                    'IdPRONAC' => $idpronac,
                    'idNrReuniao' => $reuniaoatual
                );
                $tblConsolidacao->inserir($dadosconsolidacao);

                /************** APAGA ARQUIVO DA VOTACAO DO PROJETO ********************/
                //
                $arquivo = getcwd() . "/public/plenaria/votacao.txt";
                if (file_exists($arquivo)) {
                    unlink($arquivo);
                }

                /************** ALTERA SITUACAO DO PROJETO *****************************/
                $situacao = null;
                if ($resultado == 'AS' and $_POST['situacao'] == null) {
                    //TRATANDO SITUACAO DO PROJETO QUANDO ESTE FOR DE READEQUACAO
                    if ($this->bln_readequacao == "false") {
                        $situacao = 'D03';
                    } else {
                        $situacao = 'D02';
                    }
                } elseif ($_POST['situacao'] != null) {
                    $situacao = $_POST['situacao'];

                    $dtsituacao = date('Y-m-d H:i:s');
                    $buscarsituacao = $st->listasituacao(array($situacao))->current()->toArray();
                    $providencia = $_POST['situacao'] == null ? 'PROJETO APROVADO NA CNIC N ' . $nrreuniaoatual . ' - ' . $buscarsituacao['Descricao'] : 'PROJETO INDEFERIDO NA CNIC N ' . $nrreuniaoatual . ' - ' . $buscarsituacao['Descricao'];

                    $where = "IdPRONAC = $idpronac";
                    $dados = array(
                        "Situacao" => $situacao,
                        "DtSituacao" => date('Y-m-d H:i:s'),
                        "ProvidenciaTomada" => $providencia,
                    );

                    $tblProjetos->alterar($dados, $where);
                    //$tblProjetos->alterarSituacao($idpronac, null, $situacao, $providencia);
                }

                /************** COPIA PLANILHAS *****************************************/
                $arrBuscaPlanilha = array();
                $arrBuscaPlanilha["idPronac = ?"] = $idpronac;
                $arrBuscaPlanilha["tpPlanilha = ? "] = 'CO';
                $arrBuscaPlanilha["stAtivo = ? "] = 'S';
                //TRATANDO QUANDO o PROJETO FOR DE READEQUACAO
                if ($this->bln_readequacao != "false") {
                    $arrBuscaPlanilha["idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')"] = '(?)';
                }
                $planilhaaprovacao = $pa->buscar($arrBuscaPlanilha);

                //Manteve o resultado igual
                if ($tpresultadovotacao == 1 and $resultado == 'AS') {
                    $consolidacao = $parecer->buscar(array('IdPRONAC = ?' => $idpronac, 'stAtivo = ?' => 1))->current()->toArray();
                    $consolidacao = $consolidacao['ResumoParecer'];
                }//Projeto deferido pelo componente a reprovado pela plenaria
                elseif ($tpresultadovotacao == 2 and $resultado == 'AS') {
                    $consolidacao = Seguranca::tratarVarAjaxUFT8($_POST['parecerconsolidado']);
                }//Projeto indeferido pelo componente a aprovado pela plenaria
                elseif ($tpresultadovotacao == 3 and $resultado == 'AS') {
                    foreach ($planilhaaprovacao as $resu) {
                        $data = array(
                            'tpPlanilha' => 'SE',
                            'dtPlanilha' => date('Y-m-d H:i:s'),
                            'idPlanilhaProjeto' => $resu->idPlanilhaProjeto,
                            'idPlanilhaProposta' => $resu->idPlanilhaProposta,
                            'IdPRONAC' => $resu->IdPRONAC,
                            'idProduto' => $resu->idProduto,
                            'idEtapa' => $resu->idEtapa,
                            'idPlanilhaItem' => $resu->idPlanilhaItem,
                            'idUnidade' => $resu->idUnidade,
                            'qtItem' => $resu->qtItem,
                            'dsItem' => '',
                            'nrOcorrencia' => $resu->nrOcorrencia,
                            'vlUnitario' => $resu->vlUnitario,
                            'qtDias' => $resu->qtDias,
                            'tpDespesa' => $resu->tpDespesa,
                            'tpPessoa' => $resu->tpPessoa,
                            'nrContraPartida' => $resu->nrContraPartida,
                            'nrFonteRecurso' => $resu->nrFonteRecurso,
                            'idUFDespesa' => $resu->idUFDespesa,
                            'idMunicipioDespesa' => $resu->idMunicipioDespesa,
                            'dsJustificativa' => $resu->dsJustificativa,
                            'stAtivo' => 'S',
                            'idPedidoAlteracao' => $resu->idPedidoAlteracao,
                            'idPlanilhaAprovacaoPai' => $resu->idPlanilhaAprovacaoPai
                        );
                        $inserirPlanilhaAprovacao = $pa->inserir($data);
                    }
                    //$where = "IdPRONAC = $idpronac and tpPlanilha = 'CO' and stAtivo = 'S'";
                    $where = "IdPRONAC = '{$idpronac}'";
                    $where .= " AND tpPlanilha = 'CO'";
                    $where .= " AND stAtivo    = 'S'";
                    //TRATANDO QUANDO o PROJETO FOR DE READEQUACAO
                    if ($this->bln_readequacao != "false") {
                        $where .= " AND idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')";
                    }
                    $dados = array('stAtivo' => 'N');
                    $pa->alterar($dados, $where);

                    $ana = new AnaliseAprovacao();
                    $RanaliseConteudo = $ana->buscar(array("tpAnalise = ?" => 'CO', "IdPRONAC = ?" => $idpronac, 'idAnaliseAprovacaoPai is null' => null));
                    foreach ($RanaliseConteudo as $resu) {
                        $data = array(
                            'tpAnalise' => 'SE',
                            'dtAnalise' => date('Y-m-d H:i:s'),
                            'idAnaliseConteudo' => $resu->idAnaliseConteudo,
                            'IdPRONAC' => $resu->IdPRONAC,
                            'idProduto' => $resu->idProduto,
                            'stLei8313' => $resu->stLei8313,
                            'stArtigo3' => $resu->stArtigo3,
                            'nrIncisoArtigo3' => $resu->nrIncisoArtigo3,
                            'dsAlineaArt3' => $resu->dsAlineaArt3,
                            'stArtigo18' => $resu->stArtigo18,
                            'dsAlineaArtigo18' => $resu->dsAlineaArtigo18,
                            'stArtigo26' => $resu->stArtigo26,
                            'stLei5761' => $resu->stLei5761,
                            'stArtigo27' => $resu->stArtigo27,
                            'stIncisoArtigo27_I' => $resu->stIncisoArtigo27_I,
                            'stIncisoArtigo27_II' => $resu->stIncisoArtigo27_II,
                            'stIncisoArtigo27_III' => $resu->stIncisoArtigo27_III,
                            'stIncisoArtigo27_IV' => $resu->stIncisoArtigo27_IV,
                            'stAvaliacao' => $resu->stAvaliacao,
                            'dsAvaliacao' => $resu->dsAvaliacao,
                            'idAnaliseAprovacaoPai' => $resu->idAnaliseAprovacao
                        );
                        $ana->inserir($data);
                    }

                    $dados = array('Situacao' => 'D01');
                    $where = 'IdPRONAC = ' . $idpronac;
                    $tblProjetos->alterar($dados, $where);
                }

                $this->_helper->json(array('error' => false));
            }// fecha try
            catch (Exception $e) {
                $this->_helper->json(array('error' => true, 'descricao' => $e->getMessage()));
                //parent::message("", "gerenciarpautareuniao/gerenciaradministrativo", "ALERT");
            }

            //INATIVA DISTRIBUICAO DESSE PROJETO PARA O COMPONENTE POIS SUA ANALIZE FOI FINALIZADA
            /*try{
                $tblDistribuicao = new tbDistribuicaoProjetoComissao();
                $tblDistribuicao->alterar(array('stDistribuicao' => 'I'), array('idPRONAC = ?'=>$idpronac));
                return;
            }// fecha try
            catch (Exception $e)
            {
                $this->_helper->json(array('error' => true, 'descricao' => $e->getMessage()));
                return;
                //parent::message("Ocorreu um erro ao inativar a distribui��o desse Projeto feita ao Componente, mas as outras a��es foram realizadas com sucesso.", "gerenciarpautareuniao/gerenciaradministrativo", "ALERT");
            }*/
            //echo "<script>msg();</script>";
        }
    }
}
