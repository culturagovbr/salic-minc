(function($){
    $(document).ready(function () {
        ready();

        // Extension pour comptabilité avec materialize.css
        $.validator.setDefaults({
            errorClass: 'invalid',
            validClass: "valid",
            errorPlacement: function (error, element) {
                var elmForm = $(element).closest("form"),
                    elmIcon = $(element).closest('.input-field').find('i'),
                    elmLabel = elmForm.find("label[for='" + $(element).attr("id") + "']");
                elmLabel.attr('data-error', error.text());
                elmLabel.removeClass('green-text');
                elmIcon.removeClass('green-text');
                elmLabel.addClass('red-text');
                elmIcon.addClass('red-text');
            },
            success: function (error, element) {
                var elmForm = $(element).closest("form"),
                    elmIcon = $(element).closest('div').find('i'),
                    elmLabel = elmForm.find("label[for='" + $(element).attr("id") + "']");
                elmLabel.removeClass('red-text');
                elmIcon.removeClass('red-text');
                elmLabel.addClass('green-text');
                elmIcon.addClass('green-text');
            }
            // submitHandler: function (form) {
            //     console.log('form ok');
            //     return false;
            // }
        });

        $('.input-field select').material_select();

    });

    $( document ).ajaxSuccess(function() {
        ready();
    });

    function ready()
    {
        // Colocando mascara nos inputs com a class date.
        $('.input-field input.cpf').mask('000.000.000-00');
        $('.input-field input.date').mask('00/00/0000');

        // Chamando o tooltip automaticamente
        $('.tooltipped').tooltip();
        $('.container.fade').fadeIn(1500);
        Materialize.updateTextFields();
        elmFormsMaterialize = $('form.materialize');
        elmFormsMaterialize.find('select').material_select();
        var arrObj = elmFormsMaterialize.find('[required=required]').filter(function(){
            if ($(this).closest('.input-field').find('label').find('b.red-text').length > 0) {
                return false;
            } else {
                return true;
            }
        });
        $(arrObj).each(function (){
            var elm = $(this),
                elmLabel = elm.closest('.input-field').find('label');
            elmLabel.html(elmLabel.html() + ' <b class="red-text">*</b>')
        });

        setTimeout(function () {
            Materialize.updateTextFields();
        }, 300);
    }
}($3));
