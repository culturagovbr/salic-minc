<template>
    <v-container fluid>
        <v-layout
            row
            wrap
        >
            <v-flex xs12>
                <v-card>
                    <s-editor-texto
                        v-model="dadosReadequacaoEmEdicao.dsJustificativa"
                        :placeholder="'Justificativa da solicitação de readequação'"
                        :min-char="minChar"
                        @text-change="atualizarForm()"
                        @editor-texto-counter="atualizarContador($event)"
                    />
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import SEditorTexto from '@/components/SalicEditorTexto';

export default {
    name: 'FormReadequacao',
    components: {
        SEditorTexto,
    },
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        minChar: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            dialog: false,
            dadosReadequacaoEmEdicao: {
                idReadequacao: 0,
                dsSolicitacao: '',
                dsJustificativa: '',
                dtSolicitacao: '',
                documento: {},
                idDocumento: '',
                dsAvaliacao: '',
            },
        };
    },
    computed: {
    },
    watch: {
        dadosReadequacao() {
            if (this.dadosReadequacao.idReadequacao !== 0) {
                this.dadosReadequacaoEmEdicao = Object.assign({}, this.dadosReadequacao);
            }
        },
        textIsValid() {
            this.$emit('texto-valido', this.textIsValid);
        },
    },
    created() {
        if (this.dadosReadequacao.idReadequacao !== 0) {
            this.dadosReadequacaoEmEdicao = Object.assign({}, this.dadosReadequacao);
        }
    },
    methods: {
        atualizarForm() {
            this.$emit('dados-update', this.dadosReadequacaoEmEdicao.dsJustificativa);
        },
        atualizarContador(valor) {
            this.$emit('editor-texto-counter', valor);
        },
    },
};
</script>
