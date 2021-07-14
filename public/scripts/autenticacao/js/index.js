$3(document).ready(function ($) {
    elmBody = $('body');
    elmBody.on('click', '#btConfirmar', function(){
        var elmForm = $(this).closest('form');
        elmForm.ajaxFormSubmit(function(booStatus){
            var  elmCard = $('.card');
            elmCard.removeClass('fadeInUp');
            elmCard.removeClass('animated');
            if (booStatus) {
                strAnimate = 'fadeOutDown';
            } else {
                strAnimate = 'jello';
            }
            elmCard.addClass(strAnimate);
            elmCard.addClass('animated');
            elmCard.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                elmCard.removeClass(strAnimate);
                elmCard.removeClass('animated');
            });
        });
        return false;
    });

    $("form.materialize").validate({
        rules: {
            cpf: {
                cpfBR: $('#cpf').val()
            },
            emailConf: {
                equalTo: "#email"
            },
            dataNasc: {
                date: false ,
                dateITA: true ,
            }
        },
        messages: {
            emailConf: {
                equalTo: "Digite correto o seu e-mail."
            }
        }
    });
});

function redirect(strUrl)
{
    var elmCard = $3('.card');
    elmCard.removeClass('fadeInUp');
    elmCard.removeClass('animated');
    strAnimate = 'fadeOutDown';
    elmCard.addClass(strAnimate);
    elmCard.addClass('animated');
    $3('#title').fadeOut();
    elmCard.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        window.location.href = strUrl;
    });
}
