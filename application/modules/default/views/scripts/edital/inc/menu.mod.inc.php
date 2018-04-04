
<!-- ========== IN�CIO MENU ========== -->

<!-- ========== IN�CIO AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->
 <style type="text/css">
            /*demo page css*/
            .demoHeaders { margin-top: 2em; }
            #dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}
            #dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
            ul#icons {margin: 0; padding: 0;}
            ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}
            ul#icons span.ui-icon {float: left; margin: 0 4px;}
        </style>
        <style type="text/css">
            .sanfonaDiv {
                clear: both;
                display: none;
            }
            body {
                margin:0;
                background:#f8f8f8;
                font:12px "Trebuchet MS", Georgia, "Times New Roman", Times, serif;
                color:#666;
                line-height:14pt;
            }
            #tabs {
                margin:0;
                background:#f8f8f8;
                font:12px "Trebuchet MS", Georgia, "Times New Roman", Times, serif;
                color:#666;
                /*line-height:14pt;*/
            }
            #tabs ul li {
                /*display: inline;
                white-space: nowrap;*/
                width: 200px;
                padding: 0em 0em .5em 0em;
                float: left;
                display:block;
                margin: 0 1px 1px -1px; /* top=1em, right=2em, bottom=3em, left=2em */
                color: white;
            }

        </style>
<script type="text/javascript">
            function layout_fluido()
            {
                var janela = $(window).width();

                var fluidNavGlobal = janela - 245;
                var fluidConteudo = janela - 253;
                var fluidTitulo = janela - 252;
                var fluidRodape = janela - 19;

//                $("#navglobal").css("width", fluidNavGlobal);
//                $("#conteudo").css("width", fluidConteudo);
//                $("#titulo").css("width", fluidTitulo);
//                $("#rodapeConteudo").css("width", fluidConteudo);
//                $("#rodape").css("width", fluidRodape);
                $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
            }

            /* ====================================================================== */
            /* =========================== MENU ABAS ================================ */
            /* ====================================================================== */
            function JSMostaConteudo(idConteudoAba, idModulo, idCategoria) {

                $("#ctModulo").html(idModulo);
                $("#ctCategoria").html(idCategoria);

                $("._conteudoAba").hide();
                $("#" + idConteudoAba).show();
            }

            function JSNumParcelas() {
                var numParcela = $("#slcNumParcela").val();
                if (numParcela > 0) {
                    $("#trParcelas").show();
                } else {
                    $("#trParcelas").hide();
                }
            }


            function JSAdicionarTextoConteudoEdital() {

                var conteudo = '';
                conteudo += '<table class="tabela">';
                conteudo += '<tr>';
                conteudo += '   <td class="bold">N�mero</td>';
                conteudo += '   <td class="bold">Texto</td>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                conteudo += '    <td style="vertical-align: top; text-align:center; width:5px;height"><input class="input_simples" type="text" size="2"></td>';
                conteudo += '    <td style="vertical-align: top;"> <textarea style="width:99%; height:240px" class="input_simples" name="txtConteudo" id="txtConteudo"></textarea></td>';
                conteudo += '</tr>';
                conteudo += '</table>';

                //adiciona coneudo a tabela
                $("#conteudoAdicionadoEdital").append(conteudo);

            }

            function JSAdicionarReferenciaConteudoEdital() {

                var conteudo = '';
                conteudo += '<table class="tabela">';
                conteudo += '<tr>';
                conteudo += '   <td class="bold">N�mero</td>';
                conteudo += '   <td class="bold">Refer�ncia</td>';
                conteudo += '</tr>';
                conteudo += '<tr>';
                conteudo += '    <td style="vertical-align: top; text-align:center; width:5px;height"><input class="input_simples" type="text" size="2"></td>';
                conteudo += '    <td style="vertical-align: top;"> ';
                conteudo += '    <select class="input_simples">';
                conteudo += '       <option>- Selecione -</option>';
                conteudo += '       <option>Crit�rios de Admissibilidade</option>';
                conteudo += '       <option>Crit�rios de Participa��o</option>';
                conteudo += '       <option>Formas de Pagamento</option>';
                conteudo += '       <option>Question�rio</option>';
                conteudo += '       <option>Planilha Or�ament�ria</option>';
                conteudo += '       <option>Portif�lio</option>';
                conteudo += '       <option>Plano de Viagem</option>';
                conteudo += '       <option>Local de Realiza��o</option>';
                conteudo += '       <option>Benefici�rios</option>';
                conteudo += '    </select>';
                conteudo += '    </td>';
                conteudo += '</tr>';
                conteudo += '</table>';

                //adiciona coneudo a tabela
                $("#conteudoAdicionadoEdital").append(conteudo);

            }

            function JSAddModulo(nomeModulo) {

                JSCalculaModulo();
                var conteudo = $("#divMenuModuloModelo").clone();
                var div = $(conteudo).find(".sanfona2");
                var numModulo = $("#numModulo").val();

                conteudo.find(".lbModulo").html('Modulo ' + numModulo + ' - <b>' + nomeModulo + '</b>');
                conteudo.find(".btAddCategoria").attr('modulo', numModulo);
                div.addClass("modulo" + numModulo);
                conteudo.removeClass("sumir");

                $("#menuContexto").append(conteudo);
                $("#numModuloEscolhido").val(numModulo);
                $("#nomModuloEscolhido").val(nomeModulo);

            }

            function JSAddCategoria(nomeCategoria) {

                //JSEcolherModulo();
                var numModulo = $("#numModuloEscolhido").val();
                var nomModulo = $("#nomModuloEscolhido").val();

                //return false;
                if (numModulo) {

                    numModulo = parseInt(numModulo);

                    switch (numModulo) {
                        case 1:
                            var numCategoriaMod = $("#numCategoriaMod_1").val();
                            break;
                        case 2:
                            var numCategoriaMod = $("#numCategoriaMod_2").val();
                            break;
                        case 3:
                            var numCategoriaMod = $("#numCategoriaMod_3").val();
                            break;
                        case 4:
                            var numCategoriaMod = $("#numCategoriaMod_4").val();
                            break;
                        case 5:
                            var numCategoriaMod = $("#numCategoriaMod_5").val();
                            break;
                        case 6:
                            var numCategoriaMod = $("#numCategoriaMod_6").val();
                            break;
                        case 7:
                            var numCategoriaMod = $("#numCategoriaMod_7").val();
                            break;
                        case 8:
                            var numCategoriaMod = $("#numCategoriaMod_8").val();
                            break;
                        case 9:
                            var numCategoriaMod = $("#numCategoriaMod_9").val();
                            break;
                        case 10:
                            var numCategoriaMod = $("#numCategoriaMod_10").val();
                            break;
                        default:
                            alert('dafault');
                    }
                    //var numCategoriaMod = $("#numCategoriaMod_" + numModulo).val();
                    numCategoriaMod++; //incrementa contador que controla o qtde. de tabelas de conteudo adicionadas
                    switch (numModulo) {
                        case 1:
                            $("#numCategoriaMod_1").val(numCategoriaMod);
                            break;
                        case 2:
                            $("#numCategoriaMod_2").val(numCategoriaMod);
                            break;
                        case 3:
                            $("#numCategoriaMod_3").val(numCategoriaMod);
                            break;
                        case 4:
                            $("#numCategoriaMod_4").val(numCategoriaMod);
                            break;
                        case 5:
                            $("#numCategoriaMod_5").val(numCategoriaMod);
                            break;
                        case 6:
                            $("#numCategoriaMod_6").val(numCategoriaMod);
                            break;
                        case 7:
                            $("#numCategoriaMod_7").val(numCategoriaMod);
                            break;
                        case 8:
                            $("#numCategoriaMod_8").val(numCategoriaMod);
                            break;
                        case 9:
                            $("#numCategoriaMod_9").val(numCategoriaMod);
                            break;
                        case 10:
                            $("#numCategoriaMod_10").val(numCategoriaMod);
                            break;

                    }
                    //$("#numCategoriaMod_" + numModulo).val(numCategoriaMod); 

                    var conteudo = '<div id="qm0" class="qmmc sanfona">';
                    conteudo += '<a href="#" class="nomeCategoria" onclick="JSSanfona($(this)); return false;">Categoria ' + numCategoriaMod + ' - <b>' + nomeCategoria + '</b></a>';
                    conteudo += '  <div class="sanfonaDiv2 sumir" style="width: 90%; margin-left: 20px;">';
                    conteudo += '  <a href="#" class="no_seta" onclick="JSMostaConteudo(\'informacoesGerais\',\'' + nomModulo + '\',\'' + nomeCategoria + '\' ); $(\'#displayModulo\').fadeIn();">Informa��es do gerais</a>';
                    conteudo += '  <a href="#" class="no_seta" onclick="JSMostaConteudo(\'criteriosAdmissibilidade\',\'' + nomModulo + '\',\'' + nomeCategoria + '\'); $(\'#displayModulo\').fadeIn();">Cirt�rios de admissibilidade</a>';
                    conteudo += '  <a href="#" class="no_seta" onclick="JSMostaConteudo(\'criteriosAvaliacao\',\'' + nomModulo + '\',\'' + nomeCategoria + '\'); $(\'#displayModulo\').fadeIn();">Cirt�rios de participa��o</a>';
                    conteudo += '  <a href="#" class="no_seta" onclick="JSMostaConteudo(\'formaPagamento\',\'' + nomModulo + '\',\'' + nomeCategoria + '\'); $(\'#displayModulo\').fadeIn();">Formas de pagamento</a>';
                    conteudo += '  <a href="#" class="no_seta" onclick="JSMostaConteudo(\'questionario\',\'' + nomModulo + '\',\'' + nomeCategoria + '\');  $(\'#displayModulo\').fadeIn();">Question�rio</a>';
                    conteudo += '  <a href="#" class="no_seta" class="last" onclick="JSMostaConteudo(\'planilhaOrcamentaria\',\'' + nomModulo + '\',\'' + nomeCategoria + '\');  $(\'#displayModulo\').fadeIn();">Planilha Or�ament�ria</a>';
                    conteudo += '   </div>';
                    conteudo += '</div>';

                    //alert(conteudo);
                    switch (numModulo) {
                        case 1:
                            $(".modulo1").append(conteudo);
                            break;
                        case 2:
                            $(".modulo2").append(conteudo);
                            break;
                        case 3:
                            $(".modulo3").append(conteudo);
                            break;
                        case 4:
                            $(".modulo4").append(conteudo);
                            break;
                        case 5:
                            $(".modulo5").append(conteudo);
                            break;
                        case 6:
                            $(".modulo6").append(conteudo);
                            break;
                        case 7:
                            $(".modulo7").append(conteudo);
                            break;
                        case 8:
                            $(".modulo8").append(conteudo);
                            break;
                        case 9:
                            $(".modulo9").append(conteudo);
                            break;
                        case 10:
                            $(".modulo10").append(conteudo);
                            break;
                    }
                }

            }

            function JSSanfona(obj) {
                //$('.sanfona2 > a').click(function(){
                $(obj).next().toggle('fast');
                //});
            }
            function JSCalculaModulo() {
                //atualiza contador que da nome ao item de conteudo
                var numModulo = $("#numModulo").val();
                numModulo++; //incrementa contador que controla o qtde. de tabelas de conteudo adicionadas
                $("#numModulo").val(numModulo);

                $("#lbModulo").html(numModulo);
                var inputContadorModulo = 'Num. Categoria Mod. ' + numModulo + ' <input type="text" name="numCategoriaMod_' + numModulo + '" id="numCategoriaMod_' + numModulo + '" value="1"/>';
                $("#frmContadores").append(inputContadorModulo);

                var optionSelectModulo = '<option value=" ' + numModulo + ' ">M�dulo ' + numModulo + '</option>';
                $("#slcModulo").append(optionSelectModulo);
                //return numModulo;
            }

            //OPCOES DE IMPRESSAO DO PROJETO
            function  JSEcolherModulo()
            {
                $("#boxInformaModulo").dialog({
                    title: 'Informar o M�dulo',
                    resizable: true,
                    width: 550,
                    height: 250,
                    modal: true,
                    autoOpen: false,
                    buttons: {
                        'Fechar': function() {
                            $(this).dialog('close');
                        },
                        'OK': function() {
                            if ($("#nomeCategoria").val() !== "") {
                                JSAddCategoria($("#nomeCategoria").val());
//                                      $('#formularioModulo').submit();
                            } else {
                                alert('Informe um nome para a categoria');
                            }
                        }
                    }
                });
                $("#boxInformaModulo").dialog('open');
            }



            function  JSNomeiaModulo()
            {
                $("#boxCriarModulo").dialog({
                    title: 'Criar o M�dulo',
                    resizable: true,
                    width: 550,
                    height: 250,
                    modal: true,
                    autoOpen: false,
                    buttons: {
                        'Fechar': function() {
                            $(this).dialog('close');
                        },
                        'OK': function() {
                            if ($("#nomeModulo").val() !== "") {
                                JSAddModulo($("#nomeModulo").val());
//                                   $('#formularioModulo').submit();
                                $(this).dialog('close');
                            } else {
                                alert('Informe um nome para o m�dulo');
                            }
                        }
                    }
                });
                $("#boxCriarModulo").dialog('open');
            }
</script>
<!-- ========== FIM AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->
<div id="divMenuModuloModelo" class="sumir">
    <div class="top"></div>
    <div id="qm0" class="qmmc sanfona">
        <a href="#" class="" onclick="JSSanfona($(this)); return false;">
            <span id="lbModulo" class="sumir"> M�dulos </span>
            <span class="lbModulo"></span>
        </a>
        <div class="sanfonaDiv last" style="width: 90%; margin-left: 20px;">
            <div id="qm1" class="qmmc sanfona2 last">
                <a href="#" class="no_seta btAddCategoria" modulo="" onclick="$('#numModuloEscolhido').val($(this).attr('modulo')); JSEcolherModulo();">Adicionar Categoria</a>
            </div>
        </div>
    </div>
    <div class="bottom"></div>
</div>

<div id="divMenuModuloModelo" class="">
    <div class="top"></div>
    <div id="qm0" class="qmmc sanfona">
        <a href="#" class="" onclick="JSSanfona($(this));
        return false;"><span id="lbModulo" class="sumir">1</span> <span class="lbModulo">Modulo 1 - <b>Modulo Teste</b></span> </a>
        <div class="sanfonaDiv last" style="width: 90%; margin-left: 20px; display: block;">
            <div id="qm1" class="qmmc sanfona2 last modulo1">
                <a href="#" class="no_seta btAddCategoria" modulo="1" onclick="$('#numModuloEscolhido').val($(this).attr('modulo'));
        JSEcolherModulo();">Adicionar Categoria</a>
            </div>
        </div>
    </div>
    <div class="bottom"></div>
</div>

<div id="boxCriarModulo" style="display: none;">
    <form id="formularioModulo" action="<?php echo $this->url(array('controller' => 'edital', 'action' => 'salvarmodulo')); ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="idEdital" value="<?php echo $this->idEdital;?>" />    
        <table class="tabela">
            <tr>
                <td class="destacar bold w150 esquerdo">Nome do M�dulo <span style="color: red;">*</span></td>
                <td><input maxlength="200" type="text" name="nomeModulo" id="nomeModulo" class="input_simples w300"></td>
            </tr>
            <tr>
                <td class="destacar bold w150 esquerdo"> Reutilizar m�dulo </td>
                <td> <img src="public/img/botaoReutilizar.png" style="cursor: pointer;" onclick="JSReutilizar()"/>&nbsp;&nbsp;</th> </td>
            </tr>
        </table>
    </form>    
</div>


<div id="boxInformaModulo" style="display: none;">

    <table class="tabela">
        <tr>
            <td class="destacar bold w150 esquerdo">Nome da Categoria <span style="color: red;">*</span></td>
            <td><input maxlength="200" type="text" name="nomeCategoria" id="nomeCategoria" class="input_simples w300"></td>
        </tr>
        <tr>
            <td class="destacar bold w150 esquerdo">
                Reutilizar Categoria
            </td>
            <td>
                <img src="public/img/botaoReutilizar.png" style="cursor: pointer;" onclick="JSReutilizar()"/>&nbsp;&nbsp;</th>  
            </td>
        </tr>
    </table>
</div>
<!-- ========== FIM MENU ========== -->