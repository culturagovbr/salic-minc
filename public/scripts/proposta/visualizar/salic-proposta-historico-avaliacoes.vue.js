Vue.component('salic-proposta-historico-avaliacoes', {
    template: `
        <div class="tabelas">
            <div class="row">
                <salic-table-easy v-bind:dados="dado"></salic-table-easy>
            </div>
        </div>    
    `,
    data: function () {
        return {
            dado: []
        }
    },
    props: ['idpreprojeto'],
    mounted: function () {
        if (typeof this.idpreprojeto != 'undefined') {
            this.fetch(this.idpreprojeto);
        }
    },
    watch: {
        idpreprojeto: function (value) {
            this.fetch(value);
        }
    },
    methods: {
        fetch: function (id) {
            if (id) {
                let vue = this;
                $3.ajax({
                    url: '/proposta/visualizar/obter-historico-avaliacoes/idPreProjeto/' + id
                }).done(function (response) {
                    vue.dado = response.data;
                });
            }
        },
    }
});