/***********************************************************************
 * Gerenciar Balanceamento Fun��es Modal
 **********************************************************************/

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
                    // campoNome ser&aacute; obrigatorio (required) e ter� tamanho minimo (minLength)
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


function desabilitar(id){

	$('#desabilitar'+id).modal(
			{
				appendTo:'body',
				focus:true,
				overlayId:'modal-overlay',
				zIndex:1000,
				closeClass:'Close',
				escClose:true
			}
	);

	$('#desabilitar'+id).draggable({handle: '#titleDesativar'});

	validar(id+'d');
}

function habilitar(id){

	$('#habilitar'+id).modal(
			{
				appendTo:'body',
				focus:true,
				overlayId:'modal-overlay',
				zIndex:1000,
				closeClass:'Close',
				escClose:true
			}
	);

	$('#habilitar'+id).draggable({handle: '#titlehabilitar'});


}

function abrir_fechar(id_div){

	$('#'+id_div).slideToggle(400);

		$('#maiss'+id_div).toggle();

		$('#menoss'+id_div).toggle();



};

function abrir_fechar2(id_div){

		$('#hab'+id_div).slideToggle(400);

		$('#mais'+id_div).toggle();

		$('#menos'+id_div).toggle();
	};
