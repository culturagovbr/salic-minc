
<!-- ========== IN?CIO MENU ========== --> 
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script> 
<div id="menu"> 

    <!-- inￜcio: conteￜdo principal #container --> 
    <div id="container"> 

        <!-- inￜcio: navegaￜￜo local  --> 
        <script type="text/javascript"> 
            function layout_fluido() {
                var janela = $(window).width(); 
                var fluidNavGlobal = janela - 245; 
                var fluidConteudo = janela - 253; 
                var fluidTitulo = janela - 252; 
                var fluidRodape = janela - 19; 
                $("#navglobal").css("width",fluidNavGlobal); 
                $("#conteudo").css("width",fluidConteudo); 
                $("#titulo").css("width",fluidTitulo); 
                $("#rodapeConteudo").css("width",fluidConteudo); 
                $("#rodape").css("width",fluidRodape); 
                $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu"); 
            } 
        </script> 

        <style type="text/css"> 
            .sanfonaDiv { 
                clear: both; 
                display: none; 
            } 
        </style> 
        <div id="corfirma" title="Confirmacao" style='display:none;'></div> 
        <div id="ok" title="Confirmacao" style='display:none;'></div>

        <?php

        $get = Zend_Registry::get("get");
        //define id do PreProjeto que sera passado as outras implementacoes
        $codPronac = "?idPronac=";
        if (isset($this->idPronac)) {
            $codPronac .= $this->idPronac;
        } elseif (isset($get->idPronac)) {
            $codPronac .= $get->idPronac;
        }

        $auth = Zend_Auth::getInstance();// instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        ?>

        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'aprovacaoeparecer', 'action' => 'parecertecnico')); ?>">Parecer T&eacute;cnico</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'aprovacaoeparecer', 'action' => 'aprovacao')); ?>">Aprova&ccedil;&atilde;o</a>
            </div>
            <div class="bottom"></div>
            <div id="space_menu"></div>
            <!-- final: navegaￜￜo local -->
        </div>
    </div> 
</div> 
<!-- ========== FIM MENU ========== -->

