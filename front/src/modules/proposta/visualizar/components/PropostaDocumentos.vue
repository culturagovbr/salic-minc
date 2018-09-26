<template>
    <div class="documentos-anexados" v-if="documentos">
        <div class="card">
            <div class="card-content">
                <h5>Documentos da Proposta</h5>
                <table v-if="documentos.documentos_proposta && documentos.documentos_proposta.length > 0" class="bordered responsive-table">
                    <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Data envio</th>
                        <th>Arquivo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(documento, index) in documentos.documentos_proposta" :key="index">
                        <td>{{ documento.Descricao }}</td>
                        <td>{{ documento.Data | formatarData }}</td>
                        <td>
                            <a :href="getUrl(documento.idDocumentosPreProjetos, documento.tpDoc)" title="Abrir arquivo">{{documento.NoArquivo}}</a>
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
                <table v-if="documentos.documentos_proponente && documentos.documentos_proponente.length > 0" class="bordered responsive-table">
                    <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Data envio</th>
                        <th>Arquivo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(documento, index) in documentos.documentos_proponente" :key="index">
                        <td>{{ documento.Descricao }}</td>
                        <td>{{ documento.Data | formatarData}}</td>
                        <td>
                            <a :href="getUrl(documento.idDocumentosAgentes, documento.tpDoc)" title="Abrir arquivo">{{ documento.NoArquivo }}</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-else>Nenhum documento do proponente</div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'PropostaDocumentos',
    props: {
        'proposta': {},
    },
    data() {
      return {
          documentos: {},
      }
    },
    mixins: [utils],
    mounted() {
        if (this.proposta.documentos_proposta) {
            this.documentos = this.proposta;
        } else if (Object.keys(this.proposta).length > 2 && this.proposta.idAgente) {
            this.buscaDocumentos(this.proposta);
        }
    },
    watch: {
        proposta(value) {
            if (value.documentos_proposta) {
                this.documentos = value.documentos_proposta;
            } else if (Object.keys(value).length > 2 && value.idAgente) {
                this.buscaDocumentos(value);
            }
        },
        docs(value) {
            this.documentos = value;
        },
    },
    computed: {
        ...mapGetters({
            docs: 'proposta/documentos',
        }),
    },
    methods: {
        ...mapActions({
            buscaDocumentos: 'proposta/buscaDocumentos',
        }),
        getUrl(id, tipo) {
            return `/admissibilidade/admissibilidade/abrir-documentos-anexados-admissibilidade/?id=${id}&tipo=${tipo}`;
        },
    },
};
</script>
