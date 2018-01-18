<?php
require_once 'library/MinC/CPF/Cpf.php';
require_once 'library/MinC/Sessao/SessaoArquivo.php';
require_once 'library/MinC/Sessao/SessaoProponente.php';

/*Class pre-orientada a objetos
 * Aplicar Refactoring
 * @everton.gsilva
 *
 * */
class SolicitaralteracaoprojetoController extends MinC_Controller_Action_Abstract
{

//TODO aplicar Refactoring function init
    public function init()
    {
        /*  $this->view->title = "Salic - Sistema de Apoio às Leis de Incentivo à Cultura"; // título da página
          $auth              = Zend_Auth::getInstance(); // pega a autenticação
          $Usuario           = new UsuarioDAO(); // objeto usuário
          $GrupoAtivo        = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
          if ($auth->hasIdentity()) // caso o usuário esteja autenticado
          {

              if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) // verifica se o grupo ativo está no array de permissões
              {
                  parent::message("Você não tem permissão para acessar essa área do sistema!", "principal/index", "ALERT");
              }

              // pega as unidades autorizadas, orgãos e grupos do usuário (pega todos os grupos)
              $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

              // manda os dados para a visão
              $this->view->usuario     = $auth->getIdentity(); // manda os dados do usuário para a visão
              $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuário para a visão
              $this->view->grupoAtivo  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuário para a visão
              $this->view->orgaoAtivo  = $GrupoAtivo->codOrgao; // manda o órgão ativo do usuário para a visão
          } // fecha if
          else // caso o usuário não esteja autenticado
          {
              return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
          }*/

        // verifica as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 103; // Coordenador de Análise
        $PermissoesGrupo[] = 118; // Componente da Comissão
        $PermissoesGrupo[] = 119; // Presidente da Mesa
        $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
        parent::perfil(3, $PermissoesGrupo);

        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha método init()


    public function telaprojetoAction()
    {
        $cpfCnpj = str_replace("/", "", $_POST['dado']);
        $cpfCnpj = str_replace("-", "", $cpfCnpj);
        $cpfCnpj = str_replace(".", "", $cpfCnpj);

        $retornaDados = SolicitarAlteracaoProjetoDAO::buscaProjetos($cpfCnpj);
        $this->view->buscarprojeto = $retornaDados;
    }

    public function indexAction()
    {
    }

    public function tipoalteracaoprojetoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $idPronac = 111135;
        $buscaProjetoProduto = new SolicitarAlteracaoProjetoDAO();

        $resultado = $buscaProjetoProduto->detalhesProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;
    }

    public function detalhesprojetoAction()
    {
        $idPronac = 111135;
        $buscaProjetoProduto = new SolicitarAlteracaoProjetoDAO();

        $resultado = $buscaProjetoProduto->detalhesProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;
    }
}
