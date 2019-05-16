<template>
    <v-container fluid>
        <v-layout
            row
            wrap
        >
            <v-flex
                xs10
                md5
            >
                <v-card>
                    <v-card-title class="grey lighten-2 title">Versão original</v-card-title>
                    <v-divider/>
                    <v-card-text>
                        {{ campo.valor }}
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex
                xs10
                md2
                class="text-xs-center"
            >
                <v-btn
                    flat
                    class="indigo darken-1 text-xs-center"
                    color="white"
                    @click="copiarOriginal()"
                >
                    igualar
                    <v-icon>sync</v-icon>
                </v-btn>
            </v-flex>
            <v-flex
                xs10
                md5
            >
                <v-card>
                    <v-card-title class="green lighten-2 title">Versão readequada</v-card-title>
                    <v-card-actions>
                        <v-text-field
                            :label="campo.titulo"
                            :value="dadosReadequacaoEmEdicao.dsSolicitacao"
                            :rules="rules"
                            counter
                            @input="updateCampo"
                        />
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
export default {
    name: 'TemplateInput',
    props: {
        campo: {
            type: Object,
            default: () => {},
        },
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        nomeAtributo: {
            type: String,
            default: () => '',
        },
        minChar: {
            type: Number,
            default: 0,
        },
        rules: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
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
        campoTexto() {
            return this.dadosReadequacao.dsSolicitacao;
        },
    },
    watch: {
        campo() {
            if (this.campo.idReadequacao !== 0) {
                this.dadosReadequacaoEmEdicao = Object.assign({}, this.dadosReadequacao);
            }
        },
    },
    created() {
        if (this.dadosReadequacao.idReadequacao !== 0) {
            this.dadosReadequacaoEmEdicao = Object.assign({}, this.dadosReadequacao);
        }
    },
    methods: {
        updateCampo(e) {
            this.$emit('dados-update', e);
            this.atualizarContador(e.length);
        },
        atualizarContador(valor) {
            this.$emit('editor-texto-counter', valor);
        },
        copiarOriginal() {
            this.dadosReadequacaoEmEdicao.dsSolicitacao = this.campo.valor;
            this.updateCampo(this.dadosReadequacaoEmEdicao.dsSolicitacao);
        },
    },
};
</script>
