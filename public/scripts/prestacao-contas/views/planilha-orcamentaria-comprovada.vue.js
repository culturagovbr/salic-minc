Vue.component('planilha-orcamentaria-comprovada', {
    props: ['idpronac'],
    template: `
        <div>
            <sl-planilha-produtos :produtos="produtos" :idpronac="idpronac"></sl-planilha-produtos>
        </div>
    `,
    mounted: function () {
        let vue = this;
        $3.ajax({
            url: "/prestacao-contas/realizar-prestacao-contas/planilha-analise/idPronac/" + this.idpronac
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
