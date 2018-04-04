<?php
class GerarRelatorioReuniaoController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // tï¿½tulo da pï¿½gina
        $auth = Zend_Auth::getInstance(); // pega a autenticação
        $Usuario = new UsuarioDAO(); // objeto usuï¿½rio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuï¿½rio esteja autenticado
            // verifica as permissï¿½es
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 90; // Protocolo - Documento
            $PermissoesGrupo[] = 91; // Protocolo - Recebimento
            $PermissoesGrupo[] = 92; // Tec. de Admissibilidade
            $PermissoesGrupo[] = 93; // Coordenador - Geral de Análise (Ministro)
            $PermissoesGrupo[] = 94; // Parecerista
            $PermissoesGrupo[] = 96;  // Consulta Gerencial
            $PermissoesGrupo[] = 97;  // Gestor do SALIC
            $PermissoesGrupo[] = 103; // Coord. de Analise
            $PermissoesGrupo[] = 104; // Protocolo - Envio / Recebimento
            $PermissoesGrupo[] = 110; // Tec. de Analise
            $PermissoesGrupo[] = 114; // Coord. de Editais
            $PermissoesGrupo[] = 115; // Atendimento Representacoes
            $PermissoesGrupo[] = 119; // Presidente da CNIC
            $PermissoesGrupo[] = 120; // Coord. CNIC
            $PermissoesGrupo[] = 121; // Tec. de Acompanhamento
            $PermissoesGrupo[] = 122; // Coord. de Acompanhamento
            $PermissoesGrupo[] = 123; // Coord. Geral de Acompanhamento
            $PermissoesGrupo[] = 124; // Tec. de Prestação de Contas
            $PermissoesGrupo[] = 125; // Coord. de Prestação de Contas
            $PermissoesGrupo[] = 126; // Coord. Geral de Prestação de Contas
            $PermissoesGrupo[] = 127; // Coord. Geral de Análise
            $PermissoesGrupo[] = 128; // Tec. de Portaria
            $PermissoesGrupo[] = 131; // Coord. de Admissibilidade
            $PermissoesGrupo[] = 132; // Chefe de Divisão
            $PermissoesGrupo[] = 135; // Tec. De Fiscalização
            $PermissoesGrupo[] = 138; // Coord. de Avaliação
            $PermissoesGrupo[] = 139; // Tec. de Avaliação
            $PermissoesGrupo[] = 148; // Coord. de Avaliação
            $PermissoesGrupo[] = 150; // Tec. de Avaliação
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo estï¿½ no array de permissï¿½es
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgãos e grupos do usuï¿½rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visão
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usuï¿½rio para a visão
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuï¿½rio para a visão
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuï¿½rio para a visão
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o orgão ativo do usuï¿½rio para a visão
        } 
        else {
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }


    public function gerarrelatorioreuniaoAction()
    {
        $reuniao = new Reuniao();
        $pauta = new Pauta();
        $tblPauta = new tbPauta();
        $area = new Area();
        $projetos = new Projetos();
        $aprovacao = new Aprovacao();
        if ($_POST) {
            if (isset($_POST['idReuniao'])) {
                $NrReuniao = $_POST['idReuniao'];
                $buscarReuniao = $reuniao->buscar(array('NrReuniao = ?' => $NrReuniao))->current()->toArray();
                $idReuniao = $buscarReuniao['idNrReuniao'];
                $this->view->NrReuniao = $NrReuniao;
            } else {
                $buscarReuniao = $reuniao->buscarReuniaoAberta();
                if (count($buscarReuniao) > 0) {
                    $idReuniao = $buscarReuniao['idNrReuniao'];
                    $NrReuniao = $buscarReuniao['NrReuniao'];
                    $this->view->NrReuniao = $NrReuniao - 1;
                }
            }
            $idpronac = null;
            if (isset($_POST['pronac']) and $_POST['pronac'] != null) {
                $pronac = $_POST['pronac'];
                $buscarprojeto = $projetos->buscar(array('(AnoProjeto+Sequencial = ?)' => $pronac))->current()->toArray();
                $idpronac = $buscarprojeto['IdPRONAC'];
            }
            //$buscarPauta = $pauta->PautaAprovada($idReuniao, $idpronac);
            $arrBusca = array();
            $arrBusca['r.idNrReuniao = ?'] = $idReuniao;
            if (!empty($idpronac)) {
                $arrBusca['pr.idPronac = ?'] = $idpronac;
            }
            $buscarPauta = $tblPauta->buscarProjetosTermoAprovacao($arrBusca, array('a.Descricao ASC','pr.NomeProjeto ASC'));
            $projetos = array();
            $num = 0;
            foreach ($buscarPauta as $projetosCNIC) {
                $projetos[$projetosCNIC->Area][$num]['descricaoArea'] = $projetosCNIC->descricaoArea;
                $projetos[$projetosCNIC->Area][$num]['PRONAC'] = $projetosCNIC->pronac;
                $projetos[$projetosCNIC->Area][$num]['NomeProjeto'] = $projetosCNIC->NomeProjeto;
                $projetos[$projetosCNIC->Area][$num]['DtProtocolo'] = Data::tratarDataZend($projetosCNIC->DtProtocolo, 'Brasileira');
                $projetos[$projetosCNIC->Area][$num]['DtAprovacao'] = Data::tratarDataZend($projetosCNIC->DtAprovacao, 'Brasileira');
                $projetos[$projetosCNIC->Area][$num]['stAnaliseConselheiro'] = $projetosCNIC->ParecerFavoravel == 1 ? "Indeferir" : 'Aprovar';
                $projetos[$projetosCNIC->Area][$num]['dsConselheiro'] = $projetosCNIC->ResumoParecer;
                if ($projetosCNIC->stAnalise == 'AS') {
                    $projetos[$projetosCNIC->Area][$num]['stAnalisePlenaria'] = 'Aprovar';
                } elseif ($projetosCNIC->stAnalise == 'IS') {
                    $projetos[$projetosCNIC->Area][$num]['stAnalisePlenaria'] = 'Indeferir';
                } else {
                    $projetos[$projetosCNIC->Area][$num]['stAnalisePlenaria'] = '';
                }
                $projetos[$projetosCNIC->Area][$num]['dsPlenaria'] = $projetosCNIC->dsConsolidacao;
                $projetos[$projetosCNIC->Area][$num]['ValorAprovado'] = $projetosCNIC->AprovadoReal ? $projetosCNIC->AprovadoReal : 0;
                $num++;
            }
            $this->view->projetospauta = $projetos;
        } else {
            $buscarReuniao = $reuniao->buscarReuniaoAberta();
            $NrReuniao = $buscarReuniao['NrReuniao'];
            $this->view->NrReuniao = $NrReuniao - 1;
        }
    }

    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->dadosprojetos = $_POST['dadospdf'];
    }
}
