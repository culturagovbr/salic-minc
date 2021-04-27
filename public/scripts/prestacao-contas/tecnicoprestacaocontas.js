$3.fn.extend(
    {
        dialog: $.fn.dialog
    }
);

$(document).ready(function() {
    $("#btn_pesquisar").click(function(){
        var pronac = $('#pronacPesquisa').val();
        window.location = "<?php echo $this->url(array('controller' => 'realizarprestacaodecontas', 'action' => 'tecnicoprestacaocontas')); ?>?pag=1&pronac="+pronac;
    });

    $('#pronacPesquisa').keydown(function(event){
        if(event.keyCode == 13){
            $("#btn_pesquisar").click();
        }
    });

    $('.btn_imprimir').click(function(){
        $('#addCampoXls').remove();
        $('#formImpressao').submit();
    });

    $('.btn_xls').click(function(){
        $('#addCampoXls').remove();
        $('#formImpressao').append('<input id="addCampoXls" type="hidden" name="xls" value="1">');
        $('#formImpressao').submit();
    });

    $('#tabela').DataTable (
        {
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.12/i18n/Portuguese-Brasil.json"
            },
            paging: true,
        }
    );
});

function JSDevolverParaChefeDivisao(idPronac,idOrgaoDestino,ocultarJustificativa){
    console.log('teste');
    $('html').css('overflow', 'hidden');
    $("body").append("<div id='divDinamica'></div>");
    $("#divDinamica").html("");
    $('#divDinamica').html("<br><br><center>Carregando dados...</center>");
    $.ajax({
        url : '/realizarprestacaodecontas/encaminharprestacaodecontas',
        data : {
            idPronac : idPronac,
            idOrgaoDestino : idOrgaoDestino,
            idSituacaoPrestContas : 1,
            tipoFiltro : '',
            ocultarJustificativa : true,
            idPerfilDestino: 132,
        },
        success: function(data){
            $('#divDinamica').html(data);
        },
        type : 'post'
    });

    var title = 'Devolver Projeto para Chefe de Divisão';
    $("#divDinamica").dialog({
        resizable: true,
        width:750,
        height:550,
        modal: true,
        autoOpen:true,
        draggable:false,
        title: title,
        buttons: {
            'Cancelar': function() {
                $("#divDinamica").remove();
                $(this).dialog('close');
                $('html').css('overflow', 'auto');
            }
        },
        close: function() {
            $("#divDinamica").remove();
            $(this).dialog('close');
            $('html').css('overflow', 'auto');
        }
    });
}

function JSEncaminharParaConsultoriaDI(idPronac,idOrgaoDestino)
{
    $('html').css('overflow', 'hidden');
    $("body").append("<div id='divDinamica'></div>");
    $("#divDinamica").html("");
    $('#divDinamica').html("<br><br><center>Carregando dados...</center>");
    $.ajax({
        url : 'realizarprestacaodecontas/encaminharprestacaodecontas',
        data :
            {
            idPronac : idPronac,
            idOrgaoDestino : idOrgaoDestino,
            idSituacaoPrestContas : 1,
            pag : 1
        },
        success: function(data){
            $('#divDinamica').html(data);
        },
        type : 'post'
    });

    var title = '';
    if(idOrgaoDestino == 177){
        title = 'Consultoria - AECI';
    }else if(idOrgaoDestino == 12){
        title = 'Consultoria - CONJUR';
    }else{
        title = 'Encaminhar Projeto para Análise';
    }
    $("#divDinamica").dialog({
        resizable: true,
        width:750,
        height:550,
        modal: true,
        autoOpen:true,
        draggable:false,
        title: title,
        buttons: {
            'Cancelar': function() {
                $("#divDinamica").remove();
                $(this).dialog('close');
                $('html').css('overflow', 'auto');
            }
        },
        close: function() {
            $("#divDinamica").remove();
            $(this).dialog('close');
            $('html').css('overflow', 'auto');
        }
    });
}

function JShistoricoEncaminhamento(idPronac)
{
    $('html').css('overflow', 'hidden');
    $("body").append("<div id='divDinamicaHistorico'></div>");
    $("#divDinamicaHistorico").html("");
    $('#divDinamicaHistorico').html("<br><br><center>Carregando dados...</center>");
    $.ajax({
        url : '/realizarprestacaodecontas/historicoencaminhamento?idPronac='+idPronac,
        data : {
            idPronac : idPronac
        },
        success: function(data){
            $('#divDinamicaHistorico').html(data);
        },
        type : 'post'

    });

    $3("#divDinamicaHistorico").dialog({
        resizable: true,
        width:750,
        height:550,
        modal: true,
        autoOpen:true,
        draggable:false,
        title: 'Hist&oacute;rico de Encaminhamento do Projeto',
        buttons: {
            'OK': function() {
                $("#divDinamicaHistorico").remove();
                $(this).dialog('close');
                $('html').css('overflow', 'auto');
            }
        },
        close: function() {
            $("#divDinamicaHistorico").remove();
            $3(this).dialog('close');
            $('html').css('overflow', 'auto');
        }
    });
}
