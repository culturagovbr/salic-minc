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
        /*  $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da página
          $auth              = Zend_Auth::getInstance(); // pega a autentica��o
          $Usuario           = new UsuarioDAO(); // objeto usu�rio
          $GrupoAtivo        = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
          if ($auth->hasIdentity()) // caso o usu�rio esteja autenticado
          {

              if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) // verifica se o grupo ativo est� no array de permiss�es
              {
                  parent::message("Voc� Não tem permiss�o para acessar essa �rea do sistema!", "principal/index", "ALERT");
              }

              // pega as unidades autorizadas, org�os e grupos do usu�rio (pega todos os grupos)
              $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

              // manda os dados para a vis�o
              $this->view->usuario     = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
              $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
              $this->view->grupoAtivo  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
              $this->view->orgaoAtivo  = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o
          } // fecha if
          else // caso o usu�rio Não esteja autenticado
          {
              return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
          }*/

        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 103; // Coordenador de Análise
        $PermissoesGrupo[] = 118; // Componente da comissão
        $PermissoesGrupo[] = 119; // Presidente da Mesa
        $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
        parent::perfil(3, $PermissoesGrupo);

        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha m�todo init()


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
