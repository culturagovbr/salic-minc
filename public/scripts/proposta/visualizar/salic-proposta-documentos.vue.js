Vue.component('salic-proposta-documentos', {
    template: `
    <div class="documentos-anexados">
        <div class="card">
            <div class="card-content">
                <h5>Documentos da Proposta</h5>
                <table v-if="documentos.proposta && documentos.proposta.length > 0" class="bordered responsive-table">
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
                            <a :href="get_url(documento.idDocumentosPreProjetos, documento.tpDoc)" title="Abrir arquivo">{{documento.NoArquivo}}</a>
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
                <table v-if="documentos.proponente && documentos.proponente.length > 0" class="bordered responsive-table">
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
                            <a :href="get_url(documento.idDocumentosAgentes, documento.tpDoc)" title="Abrir arquivo">{{ documento.NoArquivo }}</a>
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
    props: ['proposta', 'arrayDocumentos'],
    mounted: function () {
        if (typeof this.proposta != 'undefined') {
            this.fetch(this.proposta);
        }

        if (typeof this.arrayDocumentos != 'undefined') {
            this.$set(this.documentos, 'proposta', this.arrayDocumentos.documentos_proposta);
            this.$set(this.documentos, 'proponente', this.arrayDocumentos.documentos_proponente);
        }
    },
    watch: {
        proposta: function (value) {
            this.fetch(value);
        },
        arrayDocumentos: function (value) {
            this.$set(this.documentos, 'proposta', value.documentos_proposta);
            this.$set(this.documentos, 'proponente', value.documentos_proponente);
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
        }, get_url(id, tipo) {
            return '/admissibilidade/admissibilidade/abrir-documentos-anexados-admissibilidade/?id=' + id + '&tipo=' + tipo
        }
    }
});