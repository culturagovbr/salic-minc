Vue.component('comprovacao-financeira', {
    props: ['idpronac'],
    template: `
        <div>
            <sl-planilha-produtos :produtos="produtos" :idpronac="idpronac"></sl-planilha-produtos>
        </div>`,
    created: function () {
        $3(document).ajaxStart(function () {
            $3('#container-loading').fadeIn('slow');
        });
        $3(document).ajaxComplete(function () {
            $3('#container-loading').fadeOut('slow');
        });
    },
    mounted: function () {
        let vue = this;
        $3.ajax({
            url: "/prestacao-contas/pagamento/planilha-pagamento/idpronac/" + this.idpronac
        }).done(function( data ) {
            vue.$data.produtos = data;
        });
    },
    data: function () {
        return {
            produtos: []
        };
    },
    methods: {
        iniciarCollapsible: function () {
            $3('.collapsible').each(function() {
                $3(this).collapsible();
            });
        }
    }
})
