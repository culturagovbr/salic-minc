
function confirmar(id){
	$('#btn'+id).click(function (e) {
		e.preventDefault();

		confirm("Deseja realmente desabilitar o Componente da comissão?", function () {
			$('#form'+id).submit();
			//window.location.href = 'http://www.tarcisioangelo.com.br';
		});
	});
}

	function validar(id)
	{
	    $("#form"+id).validate(
	    	{
	            // Define as regras
	            rules:{
	                justificativa:{
	                    // campoNome será obrigatorio (required) e ter� tamanho minimo (minLength)
	                    required: true, minlength: 15
	                }
	            },
	            // Define as mensagens de erro para cada regra
	            messages:{
	                justificativa:{
	                    required: "<br />Dados obrigat�rios Não informados.",
	                    minlength: "<br />A justificativa deve conter, no m�nimo, 15 caracteres"
	                }
	            }

	    	});
	}

	function habilitar(id_div){
		$('#habilitar'+id_div).dialog({
			        //bgiframe: true,
					autoOpen: false,
					height: 350,
					width: 600,
					modal: true
			    });


		$('#habilitar'+id_div).dialog('open');

		validar(id_div+'a');

	};

	function desabilitar(id_div){
		$('#desabilita'+id_div).dialog({
			        //bgiframe: true,
					autoOpen: false,
					height: 350,
					width: 600,
					modal: true
			    });

		$('#desabilita'+id_div).dialog('open');

		validar(id_div);

	};

	function diligenciar(){
		$('#diligencia').dialog({
			        //bgiframe: true,
					autoOpen: false,
					height: 350,
					width: 600,
					modal: true
			    });

		$('#diligencia').dialog('open');
	};

	function encaminhar(id_div){
		$('#encaminhar'+id_div).dialog({
			        //bgiframe: true,
					autoOpen: false,
					width: 600,
					modal: true
			    });

		$('#encaminhar'+id_div).dialog('open');

		validar(id_div);
	};

	function encaminhar2(id_div){
		$('#encaminhar2'+id_div).dialog({
			        //bgiframe: true,
					autoOpen: false,
					width: 600,
					modal: true
			    });

		$('#encaminhar2'+id_div).dialog('open');
	};

	function reencaminhar(id_div){
		$('#reencaminhar'+id_div).dialog({
			        //bgiframe: true,
					autoOpen: false,
					width: 600,
					modal: true
			    });

		$('#reencaminhar'+id_div).dialog('open');
	};

	function reencaminhar2(id_div){
		$('#reencaminhar2'+id_div).dialog({
			        //bgiframe: true,
					autoOpen: false,
					width: 600,
					modal: true
			    });

		$('#reencaminhar2'+id_div).dialog('open');
	};


	function encaminharCoordAcomp(idAcao){
        $("#mostra").dialog("destroy");
        $('#mostra').html('Deseja devolver ao MinC?');
        $("#mostra").dialog
        ({
            height: 180,
            modal: true,
            draggable: false,
            resizable: false,
            closeOnEscape: false,
            buttons: {
                'Não': function()
                {
                        $(this).dialog('close');
                },
                'Sim': function()
                {
                	window.location.href = 'devolverpedido?id='+idAcao;
                }
            }
        });
        $('.ui-dialog-titlebar-close').remove();
	}

	function finalizarsolicitacaogeral(idAcao){
        $("#mostra").dialog("destroy");
        $('#mostra').html('Deseja validar a Análise realizada?');
        $("#mostra").dialog
        ({
            height: 180,
            modal: true,
            draggable: false,
            resizable: false,
            closeOnEscape: false,
            buttons: {
                'Não': function()
                {
                    $(this).dialog('close');
                },
                'Sim': function()
                {
                	window.location.href = 'finalizageral?id='+idAcao;
                }
            }
        });
	}

	function stReadequacao(idPedidoAlteracao,IdPronac,valor){
            var opcao = valor.value;
            $("#mostra").dialog("destroy");
            $('#mostra').html('Deseja alterar o status da Solicitação?');
            $("#mostra").dialog
            ({
                height: 180,
                modal: true,
                draggable: false,
                resizable: false,
                closeOnEscape: false,
                buttons: {
                    'Não': function()
                    {
                        document.getElementById('status').options[0].selected = true;
                        $(this).dialog('close');
                    },
                    'Sim': function()
                    {
                            window.location.href = 'streadequacaoprodutos?id='+idPedidoAlteracao+'&IdPronac='+IdPronac+'&opcao='+opcao;
                    }
                }
            });
	}

        function stReadequacaoItemdeCusto(idPedidoAlteracao,IdPronac,valor){
            var opcao = valor.value;
            $("#mostra").dialog("destroy");
            $('#mostra').html('Deseja alterar o status da Solicitação?');
            $("#mostra").dialog
            ({
                height: 180,
                modal: true,
                draggable: false,
                resizable: false,
                closeOnEscape: false,
                buttons: {
                    'Não': function()
                    {
                        document.getElementById('status').options[0].selected = true;
                        $(this).dialog('close');
                    },
                    'Sim': function()
                    {
                        window.location.href = 'streadequacaoitensdecusto?id='+idPedidoAlteracao+'&IdPronac='+IdPronac+'&opcao='+opcao;
                    }
                }
            });
            $('.ui-dialog-titlebar-close').remove();
	}

	function stProposta(idAvaliacao,IdPRONAC,valor){
            var opcao = valor.value;
            $("#mostra").dialog("destroy");
            $('#mostra').html('Deseja alterar o status da Solicitação?');
            $("#mostra").dialog
            ({
                height: 180,
                modal: true,
                draggable: false,
                resizable: false,
                closeOnEscape: false,
                buttons: {
                    'Não': function()
                    {var opcao = valor.value;
                                    document.getElementById('status').options[0].selected = true;
                                    $(this).dialog('close');
                    },
                    'Sim': function()
                    {
                            window.location.href = 'stpropostaped?id='+idAvaliacao+'&idPronac='+IdPRONAC+'&opcao='+opcao;
                    }
                }
            });
	}


	function visualizarhist(id_div,url){
		$('#visualizarhist'+id_div).dialog({
			        //bgiframe: true,
					autoOpen: false,
					height: 350,
					width: 800,
					modal: true
			    });

		$('#visualizarhist'+id_div).dialog('open');

		$.ajax({
			url : url,
			type : 'post',
			data :
			{
				idavaliacao : id_div
			},
			success: function(data)
			{
				var recebe = $('#visualizarhist'+id_div).find('#recebeValor');
				recebe.html(data);
			}
		});
	};


	function abrir_fechar(id_div){

		$('#'+id_div).toggle('slow');

	};

	function abrir_fechar2(id_div){

		$('#hab'+id_div).toggle('slow');

	};

	function abrir_fechar_aguardando(){

		$('#aguardando').toggle('slow');

	};

	function abrir_fechar_devolvidos(){

		$('#devolvidos').toggle('slow');

	};

	function abrir_fechar_diligienciasresp(){

		$('#diligienciasresp').toggle('slow');

	};

	function abrir_fechar_projdiligienciados(){

		$('#projetosdiligienciados').toggle('slow');

	};

	function abrir_fechar(vdiv){

		$('#'+vdiv).toggle('slow');

	};

        function stAnalise(idPedidoAlteracao,IdPronac,valor,action){
            var opcao = valor.value;
            $("#mostra").dialog("destroy");
            $('#mostra').html('Deseja alterar o status da Solicitação?');
            $("#mostra").dialog
            ({
                height: 180,
                modal: true,
                draggable: false,
                resizable: false,
                closeOnEscape: false,
                buttons: {
                    'Não': function()
                    {
                        document.getElementById('status').options[0].selected = true;
                        $(this).dialog('close');
                    },
                    'Sim': function()
                    {
                        window.location.href =action+'?id='+idPedidoAlteracao+'&idpedidoalteracao='+IdPronac+'&opcao='+opcao;
                    }
                }
            });

        }
