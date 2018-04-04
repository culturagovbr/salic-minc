Vue.component(
    'salic-projeto-identificacao',
    {
        template: `
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Identifica&ccedil;&atilde;o Projeto</span>
                    <div class="row">
                        <div class="col l2 s12">
                           PRONAC: <strong>{{dados.PRONAC}}</strong>
                        </div>
                        <div class="col l2 s12">
                           Projeto: <strong>{{dados.nome}}</strong>
                        </div>
                        <div class="col  l2 s12">
                           Segmento:
                            <strong>{{dados.descricao}}</strong>
                        </div>
                        <div class="col  l2 s12">
                           Proponente:
                            <strong>{{dados.nomeAgente}}</strong>
                        </div>
                        <div class="col  l3 s12">
                           Situa&ccedil;&atilde;o: 
                            <strong>{{dados.situacao}}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `,
        data: function() {
            return {
                dados:[]
            }
        },
        props: ['idpronac', 'pronac'],
        mounted: function() {
            this.fetch(this.idpronac);
        },
        methods: {
            fetch: function(idpronac) {
                let vue = this;
                $3.ajax({
                    url: '/projeto/projeto/identificacao?idPronac=' + idpronac
                })
                .done( function(data) {
                    vue.dados = data;
                });
            }
        }
    }
);
