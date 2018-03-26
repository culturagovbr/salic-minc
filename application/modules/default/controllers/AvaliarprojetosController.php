<?php

class AvaliarprojetosController extends MinC_Controller_Action_Abstract
{
    private $idusuario                         = 0;
    private $usu_identificacao                 = 0;
    private $codGrupo                          = 0;
    private $codOrgao                          = 0;
    private $COD_SITUACAO_PROJETO              = 'G36';
    private $COD_SITUACAO_PROJETO_ATUALIZA     = 'G37';
    private $COD_SITUACAO_PROJETO_COMISSAO     = 'G51';
    private $COD_SITUACAO_PROJETO_SELECIONADOS = 'G52';
    private $COD_STTIPODEMANDA_PREPROJETO      = 'ED';
    private $TP_DISTRIBUICAO                   = 1;
    private $ST_DISTRIBUICAO_PENDENTE          = 1;
    private $ST_DISTRIBUICAO_REALIZADA         = 2;
    private $ST_APROVACAO_APROVADO             = 1;
    private $ST_APROVACAO_REPROVADO            = 0;
    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticaç?o
        $this->view->title = "Salic - Sistema de Apoio ?s Leis de Incentivo ? Cultura"; // título da página

        $auth = Zend_Auth::getInstance();// instancia da autenticação
        if (isset($auth->getIdentity()->usu_codigo)) { // autenticacao novo salic
              $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario["idAgente"] : 0;
        }

        $this->idusuario = $this->getIdUsuario;

        $idorgao = $auth->getIdentity()->usu_orgao;
        $usu_identificacao = $auth->getIdentity()->usu_identificacao;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo

        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sessão
        $codOrgao = $GrupoAtivo->codOrgao; //  Órgão ativo na sessão
        $this->codGrupo = $codGrupo; //  Grupo ativo na sessão
        $this->codOrgao = $codOrgao;
        $this->view->codOrgao = $codOrgao;
        $this->view->codGrupo = $codGrupo;
        //$this->view->idUsuarioLogado = $idusuario;

        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 130; //Avaliador de Editais


        parent::perfil(1, $PermissoesGrupo);
        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            if ($this->getIdUsuario) {
                $this->getIdUsuario = $this->getIdUsuario["idAgente"];
            } else {
                $this->getIdUsuario = 0;
            }
        } else {
            $this->getIdUsuario = $auth->getIdentity()->IdUsuario;
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha método init()

    public function indexAction()
    {
        $IdOrgao = $this->codOrgao;
        $idusuario = $this->idusuario;

        $tbprojetos = new Projetos();

        $where = array(
            'Situacao = ?'      =>  $this->COD_SITUACAO_PROJETO_ATUALIZA,
            'stTipoDemanda = ?' =>  $this->COD_STTIPODEMANDA_PREPROJETO,
            'idOrgao = ?'       =>  $IdOrgao,
            'age.idAgente = ?'  =>  $idusuario,
            //'vis.Visao = ?'     =>  '211'
        );

        $listaProjetos = $tbprojetos->listaProjetosPainelAvaliador($where);
        $this->view->listaProjetos = $listaProjetos;
    }

    public function avaliarAction()
    {
        if (!empty($_GET['nrFormDocumento']) && !empty($_GET['nrVersaoDocumento']) && !empty($_GET['idPreProjeto'])) {
            $idusuario = $this->idusuario;
            $IdOrgao = $this->codOrgao;

            //x($idusuario);
            //x($IdOrgao);

            $tbprojetos = new Projetos();
            $nrFormDocumento = $_GET['nrFormDocumento'];
            $nrVersaoDocumento = $_GET['nrVersaoDocumento'];
            $idPreProjeto = $_GET['idPreProjeto'];

            $where = array(
            'Situacao = ?'          =>  $this->COD_SITUACAO_PROJETO_ATUALIZA,
            'stTipoDemanda = ?'     =>  $this->COD_STTIPODEMANDA_PREPROJETO,
            'idOrgao = ?'           =>  $IdOrgao,
            'age.idAgente = ?'      =>  $idusuario,
            'pp.idPreProjeto = ?'   =>  $idPreProjeto,
            'stAvaliacao != ? OR stAvaliacao IS NULL' => '1',
            //'vis.Visao = ?'         =>  '211'
        );


            $dadosFD = array('nrFormDocumento = ?' => $nrFormDocumento, 'nrVersaoDocumento = ?' => $nrVersaoDocumento);
            $FD = new tbFormDocumento();
            $buscarFD = $FD->buscar($dadosFD)->toArray();
            $idEdital = $buscarFD[0]['idEdital'];


            $listaProjetos = $tbprojetos->listaProjetosPainelAvaliador($where)->current();
            $this->view->listaProjetos = $listaProjetos;

            $where = array(
                'b.nrFormDocumento = ?' => $nrFormDocumento,
                'b.nrVersaoDocumento = ?' => $nrVersaoDocumento,
                'f.idEdital = ?' => $idEdital
            );

            if (!empty($_GET['$nrPergunta'])) {
                $where['b.nrPergunta = ?'] = $_GET['$nrPergunta'];
            }
            $order = "b.nrOrdemPergunta asc";
            $tbpergunta = new tbPergunta();
            $perguntas = $tbpergunta->listaCompleta($where, $order);

            $this->view->perguntas = $perguntas;
        } else {
            parent::message("Dados inv&aacute;lidos", "Avaliarprojetos/index", "ERROR");
        }
    }

    public function salvarprojetoAction()
    {
        $idusuario = $this->idusuario;
        $tblRes = new tbResposta();

        if ($_POST) {
            // pega o id do edital
            $tbFormDocumento = new tbFormDocumento();
            $idEdital = $tbFormDocumento->buscar(array('nrFormDocumento = ?' => $_POST['nrFormDocumento']
                ,'nrVersaoDocumento = ?' => $_POST['nrVersaoDocumento']))->current()->toArray();
            $idEdital = $idEdital['idEdital'];

            // pega os documentos de critérios
            $criterios = $tbFormDocumento->buscar(array(
                    'idEdital = ?'               => $idEdital
                    ,'idClassificaDocumento = ?' => 25
                ))->toArray();
            ;

            // varre todos os critérios e adiciona na tbFormDocumentoProjeto
            $tbFormDocumentoProjeto = new tbFormDocumentoProjeto();

            /*foreach ($criterios as $c) {
                $buscarFormDocumentoProjeto = $tbFormDocumentoProjeto->buscar( array('nrFormDocumento = ?' => $c['nrFormDocumento']) )->toArray();

                if (count($buscarFormDocumentoProjeto) <= 0) : // cadastra na tbFormDocumentoProjeto
                    $dadosFormDocumentoProjeto = array(
                        'nrFormDocumento'    => $c['nrFormDocumento']
                        ,'nrVersaoDocumento' => $_POST['nrVersaoDocumento']
                        ,'idProjeto'         => $_POST['idPreProjeto']
                        ,'idPessoaCadastro'  => $idusuario
                        ,'dtIniValidade'     => '1900-01-01'
                        ,'dtFimValidade'     => '1900-01-01'
                    );
                    $tbFormDocumentoProjeto->inserir($dadosFormDocumentoProjeto);
                endif;
            } // endforeach*/


            $where = array(
                 'idProjeto = ?'        => $_POST['idPreProjeto'],
                 'idPessoaCadastro = ?' => $idusuario
            );

            $notas = 0;
            $contPeso = 0;
            $totalPeso = 0;
            foreach ($_POST['perguntas'] as $value) {
                $notas += (float) $_POST['nota_'.$value] * (float) $_POST['nrPeso'][$contPeso];
                $totalPeso += (float) $_POST['nrPeso'][$contPeso];
                $contPeso++;

                $where['nrOpcao = ?'] = $_POST['Opcao_'.$value];
                $verifica = $tblRes->buscar($where);
                if (count($verifica) >= 1) {
                    $dados = array(
                        'dtResposta' => date('Y-m-d H:i:s'),
                        'dsRespostaSubj' => $_POST['nota_'.$value]
                    );

                    try {
                        $tblRes->alterar($dados, $where);
                    } catch (Exception $e) {
                        parent::message("Falha ao salvar avalia&ccedil;&atilde;o", "Avaliarprojetos/index", "ERROR");
                    }
                } else {
                    /*$dados = array(
                        'nrFormDocumento'   => $_POST['nrFormDocumento'],
                        'nrVersaoDocumento' => $_POST['nrVersaoDocumento'],
                        'nrPergunta'        => $value,
                        'nrOpcao'           => $_POST['Opcao_'.$value],
                        'idPessoaCadastro'  => $idusuario,
                        'dtResposta '       => new Zend_Db_Expr('GETDATE()'),
                        'idProjeto'         => $_POST['idPreProjeto'],
                        'dsRespostaSubj'    => $_POST['nota_'.$value]
                    );*/
                    /*try{
                        $tblRes->inserir($dados);
                        }catch (Exception $e){
                        parent::message("Falha ao salvar avalia&ccedil;&atilde;o", "avaliarprojetos/index", "ERROR");
                    }*/
                }
            }
//           parent::message("Dados salvos com sucesso!", "avaliarprojetos/index", "CONFIRM");
            $tbAvaliacaoPreProjeto = new tbAvaliacaoPreProjeto();
            $dadosAvaliacao = array(
                        'idPreProjeto' => $_POST['idPreProjeto']
                        ,'idAvaliador' => $this->idusuario
                        ,'nrNotaFinal' => number_format($notas/$totalPeso, 2, '.', '')
                        ,'dtAvaliacao' => new Zend_Db_Expr('GETDATE()')
                        ,'stAvaliacao' => 0);
            try {
                $tbAvaliacaoPreProjeto->inserir($dadosAvaliacao);
                parent::message("Dados salvos com sucesso!", "avaliarprojetos/index", "CONFIRM");
            } catch (Exception $e) {
                parent::message("Falha ao salvar avalia&ccedil;&atilde;o", "avaliarprojetos/index", "ERROR");
            }
        } else {
            parent::message("Dados inv&aacute;lidos", "avaliarprojetos/index", "ERROR");
        }
    }

    public function enviaravaliacaoAction()
    {
        $idPreProjeto = !empty($_GET['idPreProjeto']) ? $_GET['idPreProjeto'] : 0;
        $idusuario = $this->idusuario;

        $dados = array(
          'stAvaliacao' => 1
        );

        $where = array(
          'idPreprojeto = ?' => $idPreProjeto,
          'idAvaliador = ?' => $idusuario
        );
        $tblAvaliacao = new tbAvaliacaoPreProjeto();

        try {
            $tblAvaliacao->alterar($dados, $where);
        } catch (Exception $e) {
            parent::message("Falha ao enviar avaliza&ccedil;&atilde;o", "avaliarprojetos/index", "ERROR");
        }
        $tbDistribuicao = new tbDistribuicao();
        $tblProjetos    = new Projetos();


        $where = array(
            'idItemDistribuicao = ?' => $idPreProjeto,
            'stAvaliacao = ?' => '1'
        );
        $enviados = $tbDistribuicao->QTDAvaliadorXenvio($where);
        if (count($enviados) > 0) {
            if (count($enviados) >= $enviados[0]->qtAvaliador) {
                try {
                    $dadosprojeto    = $tblProjetos->listaProjetosDistribuidos(array('idPreProjeto = ?' => $idPreProjeto))->current();
                    $tblProjetos->alterarSituacao($dadosprojeto->idPronac, $dadosprojeto->AnoProjeto.$dadosprojeto->Sequencial, $this->COD_SITUACAO_PROJETO_COMISSAO, "Projeto encaminhado para comiss?o");
                } catch (Exception $e) {
                    $dados = array(
                                  'stAvaliacao' => 0
                                );

                    $where = array(
                                  'idPreprojeto = ?' => $idPreProjeto,
                                  'idAvaliador = ?' => $idusuario
                                );
                    $tblAvaliacao->alterar($dados, $where);
                    parent::message("Falha ao enviar avaliza&ccedil;&atilde;o", "avaliarprojetos/index", "ERROR");
                }
            }
        }


        // $COD_SITUACAO_PROJETO_COMISSAO
        parent::message("Avalia&ccedil;&atilde;o enviada com sucesso!", "avaliarprojetos/index", "CONFIRM");
    }
}
