Vue.component('salic-proposta-documentos', {
    template: `
    <div class="documentos-anexados">
        <div class="card">
            <div class="card-content">
                <h5>Documentos da Proposta</h5>
                <table v-if="documentos.proposta" class="bordered responsive-table">
                    <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Data envio</th>
                        <th>Arquivo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="documento in documentos.proposta">
                        <td>{{ documento.Descricao }}</td>
                        <td>{{ formatar_data(documento.Data) }}</td>
                        <td>
                            <a :href="documento.url" title="Abrir arquivo">{{documento.NoArquivo}}</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-else>Nenhum documento da proposta</div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>Documentos do Proponente</h5>
                <table v-if="documentos" class="bordered responsive-table">
                    <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Data envio</th>
                        <th>Arquivo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="documento in documentos.proponente">
                        <td>{{ documento.Descricao }}</td>
                        <td>{{ formatar_data(documento.Data) }}</td>
                        <td>
                            <a :href="documento.url" title="Abrir arquivo">{{ documento.NoArquivo }}</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-else>Nenhum documento do proponente</div>
            </div>
        </div>
    </div>
    `,
    data: function () {
        return {
            documentos: []
        }
    },
    props: ['proposta'],
    mounted: function () {
        if (typeof this.proposta != 'undefined') {
            this.fetch(this.proposta);
        }
    },
    watch: {
        proposta: function (value) {
            this.fetch(value);
        }
    },
    methods: {
        fetch: function (dados) {
            if (typeof dados.default == 'undefined') {

                let vue = this;
                $3.ajax({
                    url: '/proposta/visualizar/obter-documentos-anexados/idPreProjeto/' + dados.idPreProjeto + '/idAgente/' + dados.idAgente
                }).done(function (response) {
                    vue.documentos = response.data;
                });
            }
        },
        formatar_data: function (date) {

            date = moment(date).format('DD/MM/YYYY');

            return date;
        }
    }
});