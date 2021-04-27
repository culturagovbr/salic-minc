utilitarios = {
    marcarDesmarcarCheckBoxesMaterialize: function (objeto, seletorCheckBoxes) {
        $3(seletorCheckBoxes).prop("checked", !$3(objeto).prop("checked"));
    }
}