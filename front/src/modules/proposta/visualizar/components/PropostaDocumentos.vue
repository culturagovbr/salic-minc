<template>
    <div
        v-if="documentos">
        <proposta-documentos-proposta :documentos="documentos.documentos_proposta"/>
        <proposta-documentos-proponente :documentos="documentos.documentos_proponente"/>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import PropostaDocumentosProponente from './PropostaDocumentosProponente';
import PropostaDocumentosProposta from './PropostaDocumentosProposta';

export default {
    name: 'PropostaDocumentos',
    components: { PropostaDocumentosProposta, PropostaDocumentosProponente },
    mixins: [utils],
    props: {
        proposta: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            documentos: {},
        };
    },
    computed: {
        ...mapGetters({
            docs: 'proposta/documentos',
        }),
    },
    watch: {
        proposta(value) {
            if (value.documentos_proposta) {
                this.documentos = value;
            } else if (Object.keys(value).length > 2 && value.idAgente) {
                this.buscaDocumentos(value);
            }
        },
        docs(value) {
            this.documentos = value;
        },
    },
    mounted() {
        if (this.proposta.documentos_proposta) {
            this.documentos = this.proposta;
        } else if (Object.keys(this.proposta).length > 2 && this.proposta.idAgente) {
            this.buscaDocumentos(this.proposta);
        }
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
