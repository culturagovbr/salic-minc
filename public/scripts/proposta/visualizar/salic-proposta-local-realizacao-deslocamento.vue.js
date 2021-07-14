Vue.component('salic-proposta-local-realizacao-deslocamento', {
    template:
        `<div class="local-realizacao-deslocamento">
            <div class="card">
                <div class="card-content">
                    <h5>Local de Realiza&ccedil;&atilde;o</h5>
                    <table v-if="proposta.localizacoes" class="bordered responsive-table">
                        <thead>
                        <tr>
                            <th>Pais</th>
                            <th>UF</th>
                            <th>Cidade</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="localizacao in proposta.localizacoes">
                            <td>{{ localizacao.pais }}</td>
                            <td>{{ localizacao.uf }}</td>
                            <td>{{ localizacao.cidade }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div v-else>Nenhuma localiza&ccedil;&atilde;o</div>
                </div>
            </div>
    
            <div class="card">
                <div class="card-content">
                    <h5>Deslocamentos</h5>
                    <table v-if="proposta.deslocamentos && proposta.deslocamentos.lenght > 1"
                           class="bordered responsive-table">
                        <thead>
                        <tr>
                            <th>Pais de Origem</th>
                            <th>UF de Origem</th>
                            <th>Cidade de Origem</th>
                            <th>Pais de Destino</th>
                            <th>UF de Destino</th>
                            <th>Cidade de Destino</th>
                            <th>Quantidade</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="deslocamento in proposta.deslocamentos">
                            <td>{{ deslocamento.paisorigem }}</td>
                            <td>{{ deslocamento.uforigem }}</td>
                            <td>{{ deslocamento.municipioorigem }}</td>
                            <td>{{ deslocamento.paisodestino }}</td>
                            <td>{{ deslocamento.ufdestino }}</td>
                            <td>{{ deslocamento.municipiodestino }}</td>
                            <td>{{ deslocamento.Qtde }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div v-else>N&atilde;o informado</div>
                </div>
            </div>
        </div>
    `,
    data: function () {
        return {
            proposta: []
        }
    },
    props: ['idpreprojeto', 'localizacoes'],
    mounted: function () {
        if (typeof this.idpreprojeto != 'undefined') {
            this.fetch(this.idpreprojeto);
        }

        if (typeof this.localizacoes != 'undefined') {
            this.$set(this.proposta, 'localizacoes', this.localizacoes.abrangencia);
            this.$set(this.proposta, 'deslocamentos', this.localizacoes.deslocamento);
        }
    },
    watch: {
        idpreprojeto: function (value) {
            this.fetch(value);
        },
        localizacoes: function (value) {
            this.$set(this.proposta, 'localizacoes', value.abrangencia);
            this.$set(this.proposta, 'deslocamentos', value.deslocamento);
        }
    },
    methods: {
        fetch: function (id) {
            if (id) {
                let vue = this;
                $3.ajax({
                    url: '/proposta/visualizar/obter-local-realizacao-deslocamento/idPreProjeto/' + id
                }).done(function (response) {
                    vue.proposta = response.data;
                });
            }
        },
        formatar_data: function (date) {

            date = moment(date).format('DD/MM/YYYY');

            return date;
        }
    }
});