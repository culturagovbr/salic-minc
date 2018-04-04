<!-- ========== IN�CIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<script type="text/javascript">
    function carregaDados(url,divRetorno){
        //$("#titulo").html('');
        $("#conteudo").html('<br><br><center>Aguarde, carregando dados...<br><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" /></center><br><br>');
        $.ajax({
            url : url,
            /*data :
            {
                idPronac : 'teste'
            },*/
            success: function(data){
                //alert(data);
                $("#"+divRetorno).html(data);
            },
            type : 'post'

        });
    }


    var votacao = window.setInterval(
    function(){
        verificarReuniao("<?php echo $this->dadosReuniaoPlenariaAtual['stPlenaria']; ?>")
    }, 3000);

       function verificarReuniao(stplenaria){
        var stplenariaatual  = stplenaria;
        $.ajax({
            type: "POST",
            url: "<?php echo $this->url(array('module' => 'default', 'controller' => 'gerenciarpautareuniao', 'action' => 'verificarcnic')) ?>",
            data:{
                verificacnic : true,
                stPlenaria : stplenariaatual
            },
            success: function(dados)
            {
                if(!dados.error)
                {
                    if(dados.acao != 'reload'){
                        if(dados.real > 0)
                        {
                            if(dados.status != stplenariaatual)
                            {
                                data = dados.cronometro;
                                valor = data.split(":");
                                if(dados.stPlenaria == 'A'){
                                    $('#cronometro').css('display','');
                                    $("#minu").html(valor[0]);
                                    $("#seg").html(valor[1]);
                                    clearInterval(votacao);
                                    cronometro(function(){
                                            window.location = "<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao')); ?>";
                                    });// fim function cronometro
                                }// fim stPlenaria == A
                                else{
                                    window.location = "<?php echo $this->url(array('controller' => 'areadetrabalho', 'action' => 'index')) ?>";
                                } // // fim else stPlenaria == A
                            }// fim dados.stPlenaria != stplenariaatual
                            else if(dados.status == stplenariaatual)
                            {
                                if(dados.status == 'A')
                                {
                                    data = dados.cronometro;
                                    valor = data.split(":");
                                    $('#cronometro').css('display','');
                                    $("#minu").html(valor[0]);
                                    $("#seg").html(valor[1]);
                                    clearInterval(votacao);
                                    cronometro(function(){
                                            window.location = "<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao')); ?>";
                                    }); // fim funcao cronometro
                                }// fim if stPlenaria == A
                            }// fim elseif dados.stPlenaria == stplenariaatual
                        }
                        else{
                            if(dados.status == 'A')
                            {
                                    window.location = "<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao')); ?>";
                            }
                        }
                    }
                    else{
                    clearInterval(votacao);
                    verificarReuniao('A');
                    }
                }// fim !dados.error
            }
            , dataType : 'json'
        });
    }
    var contador;
    function cronometro(funcaofim){
        clearInterval(contador);
        contador = setInterval(function(){
            var minu = $('#cronometro #minu').html()*1;
            var seg = $('#cronometro #seg').html()*1;

            if(seg == 0){
                minu--;
                seg = 59;
            }
            else
                seg--;

            if(seg < 10)
                seg = '0'+seg;
            if(minu < 10)
                minu = '0'+minu;

            $('#cronometro #minu').html(minu);
            $('#cronometro #seg').html(seg);

            if(minu==0 && seg==0){
                clearInterval(contador);
                funcaofim();
            }
        },1000);
    }
    function abrirmodal(funcaoFim){
        $("#iniciareuniao").dialog({
            closeOnEscape: false,
            resizable: true,
            width:450,
            height:150,
            modal: true,
            autoOpen:false,
            buttons  :
                {
                'OK': funcaoFim
            }
        });
        $('#iniciareuniao').dialog('open');
    }
    function layout_fluido()
    {
        var janela = $(window).width();

        var fluidNavGlobal = janela - 245;
        var fluidConteudo = janela - 253;
        var fluidTitulo = janela - 252;
        var fluidRodape = janela - 19;

//        $("#navglobal").css("width","100%");
//        $("#conteudo").css("width","100%");
//        $("#titulo").css("width","100%");
//        $("#rodapeConteudo").css("width","100%");
//        $("#rodape").css("width","100%");

        $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
    }
    $(function(){
        $('.menuHorizontal').each(function(){
            var menu = this;
            $(menu).menu({
                content: $(menu).next().html(),
                flyOut: true
            });
        });
    });
</script>
<!-- in�cio: conte�do principal #container -->
<div id="menu">
    <!-- in�cio: navega��o local #qm0 -->
    <?php $getPronac = $this->idpronac; ?>

    <div id="menuContexto">
        <div class="top"></div>
        <div id="qm0" class="qmmc sanfona">
            <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'realizaranaliseprojeto', 'action' => 'parecerconsolidado')); ?>" title="Ir para Consultar Projetos">Parecer Consolidado </a>
            <a class="no_seta" id="consultarprojetos" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'dadosprojeto', 'action' => 'index')); ?>" title="Ir para Consultar Projeto">Consultar Projeto</a>
            <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'proponente', 'action' => 'index')); ?>" title="Ir para Dados do Proponente">Dados do Proponente</a>
            <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'anexardocumentos', 'action' => 'index')); ?>" title="Ir para Documentos anexados">Documentos anexados</a>
            <a class="no_seta last" href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaanalista'), '', true); ?>?idPronac=<?php echo $this->idpronac;?>&situacao=C30&tpDiligencia=126" title="Ir para Diligenciar Proponente">Dilig&ecirc;ncias</a>
            
        </div>
        <div class="bottom">
        </div>
        <div id="cronometro" style="background: #f8f8f8; display: none; font-size: 1.8em; padding-top: 2em; text-align: center; color: red; font-weight: 800; padding-left: 0.3em;" >
            In&iacute;cio da Plen&aacute;ria em <br/><br/> <span id="minu"></span>' : <span id="seg" ></span>"
        </div>
        <div id="space_menu">
        </div>
    </div>
    <div id="iniciareuniao" class="sumir">Plen&aacute;ria Iniciada. Voc&ecirc; ser&aacute; redirecionado</div>

    <script type="text/javascript">

    </script>
    <div id="alertar"></div>
    <!-- final: navega��o local #qm0 -->
</div>
<!-- ========== FIM MENU ========== -->