function cronometrouso(funcaoFim){
    var contador = setInterval(function(){
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
            funcaoFim();
        }
        else{
            $('#menuGerenciar').css('display','none');
        }
    },1000);
}

function abrirdados(count, idpronac, tipoagente, envioplenaria){
    if($(".tradd[id='"+count+"']").attr('aberto') == 'false'){
        if($.trim($("#dadosplenaria"+count).html()) == '' ){
            var tr = "<tr id='dadosplenaria"+count+"' class='divdadosprojeto'>";
            tr += "<td colspan=\"9\">";
            tr += "<fieldset>";
            tr += "<input type='button' view='parecerconsolidado' idprojeto='parecerconsolidado"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Parecer Consolidado";
            tr += "</fieldset>";
            tr += "<div id='parecerconsolidado"+count+"' aberto='false'></div>";
            tr += "<fieldset>";
            tr += "<input type='button' view='analisedecustos' idprojeto='analisedecustos"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>An&aacute;lise de Custos";
            tr += "</fieldset>";
            tr += "<div id='analisedecustos"+count+"' aberto='false'></div>";
            tr += "<fieldset>";
            tr += "<input type='button' view='diligencias' idprojeto='diligencias"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Dilig&ecirc;ncias";
            tr += "</fieldset>";
            tr += "<div id='diligencias"+count+"' aberto='false'></div>";
            if(tipoagente == 'cc' && envioplenaria =='s'){ //coordenador cnic
                tr += "<fieldset>";
                tr += "<input type='button' view='aprovarparecer' idprojeto='aprovarparecer"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Retirar da Plen&aacute;ria";
                tr += "</fieldset>";
                tr += "<div id='aprovarparecer"+count+"' aberto='false'></div>";
            }
            if(tipoagente == 'coc'){ //componente da comissao
                tr += "<fieldset>";
                tr += "<input type='button' view='dadosproponente' idprojeto='dadosproponente"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Dados Proponente";
                tr += "</fieldset>";
                tr += "<div id='dadosproponente"+count+"' aberto='false'></div>";
            }
            if(tipoagente == 'coc' && envioplenaria =='n'){ //componente da comissao
                tr += "<fieldset>";
                tr += "<input type='button' view='aprovarparecer' idprojeto='aprovarparecer"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Submeter a Plen&aacute;ria";
                tr += "</fieldset>";
                tr += "<div id='aprovarparecer"+count+"' aberto='false'></div>";
            }
            tr += "</td>";
            tr += "</td>";
            $(".tradd[id='"+count+"']").after(tr) ;
            $(".tradd[id='"+count+"']").attr('aberto','true');
        }
        else{
            $("#dadosplenaria"+count).removeClass('sumir')
            $(".tradd[id='"+count+"']").attr('aberto','true');
        }
    }
    else{
        $("#dadosplenaria"+count).addClass('sumir')
        $(".tradd[id='"+count+"']").attr('aberto','false');
    }
}

function abrirdadosRecursos(count, idpronac, tipoagente, envioplenaria, idrecurso, tipoSolicitacao){
    if($(".tradd[id='"+count+"']").attr('aberto') == 'false'){
        if($.trim($("#dadosplenaria"+count).html()) == '' ){
            var tr = "<tr id='dadosplenaria"+count+"' class='divdadosprojeto'>";
            tr += "<td colspan=\"10\">";

            tr += "<fieldset>";
            tr += "<input type='button' view='parecerconsolidadorecursos' idprojeto='parecerconsolidado"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Parecer Consolidado do Recurso";
            tr += "</fieldset>";
            tr += "<div id='parecerconsolidado"+count+"' aberto='false'></div>";

            tr += "<fieldset>";
            tr += "<input type='button' view='diligencias' idprojeto='diligencias"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Dilig&ecirc;ncias";
            tr += "</fieldset>";
            tr += "<div id='diligencias"+count+"' aberto='false'></div>";

            if(tipoagente == 'coc'){ //componente da comissao
                tr += "<fieldset>";
                tr += "<input type='button' view='dadosproponente' idprojeto='dadosproponente"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Dados Proponente";
                tr += "</fieldset>";
                tr += "<div id='dadosproponente"+count+"' aberto='false'></div>";
            }

            tr += "</td>";
            tr += "</td>";
            $(".tradd[id='"+count+"']").after(tr) ;
            $(".tradd[id='"+count+"']").attr('aberto','true');
        }
        else{
            $("#dadosplenaria"+count).removeClass('sumir')
            $(".tradd[id='"+count+"']").attr('aberto','true');
        }
    }
    else{
        $("#dadosplenaria"+count).addClass('sumir')
        $(".tradd[id='"+count+"']").attr('aberto','false');
    }
}

function abrirdadosReadequacoes(count, idpronac, tipoagente, envioplenaria, idreadequacao, tipoSolicitacao){
    if($(".tradd[id='"+count+"']").attr('aberto') == 'false'){
        if($.trim($("#dadosplenaria"+count).html()) == '' ){
            var tr = "<tr id='dadosplenaria"+count+"' class='divdadosprojeto'>";
            tr += "<td colspan=\"9\">";

            tr += "<fieldset>";
            tr += "<input type='button' view='parecerconsolidadoreadequacoes' idprojeto='parecerconsolidado"+count+"' class='btn_adicionar' onClick='dadosprojetosreadequados("+idpronac+","+idreadequacao+", this)'/>Parecer Consolidado da Readequação";
            tr += "</fieldset>";
            tr += "<div id='parecerconsolidado"+count+"' aberto='false'></div>";

            tr += "<fieldset>";
            tr += "<input type='button' view='diligencias' idprojeto='diligencias"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Dilig&ecirc;ncias";
            tr += "</fieldset>";
            tr += "<div id='diligencias"+count+"' aberto='false'></div>";

            if(tipoagente == 'coc'){ //componente da comissao
                tr += "<fieldset>";
                tr += "<input type='button' view='dadosproponente' idprojeto='dadosproponente"+count+"' class='btn_adicionar' onClick='dadosprojetos("+idpronac+", this)'/>Dados Proponente";
                tr += "</fieldset>";
                tr += "<div id='dadosproponente"+count+"' aberto='false'></div>";
            }

            tr += "</td>";
            tr += "</td>";
            $(".tradd[id='"+count+"']").after(tr) ;
            $(".tradd[id='"+count+"']").attr('aberto','true');
        }
        else{
            $("#dadosplenaria"+count).removeClass('sumir')
            $(".tradd[id='"+count+"']").attr('aberto','true');
        }
    }
    else{
        $("#dadosplenaria"+count).addClass('sumir')
        $(".tradd[id='"+count+"']").attr('aberto','false');
    }
}
