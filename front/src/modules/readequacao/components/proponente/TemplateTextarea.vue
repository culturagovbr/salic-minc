<template>
    <v-container fluid>
        <v-layout
            row
            wrap
        >
            <v-flex
                xs10
                md5>
                <v-card min-height="336px">
                    <v-card-title class="grey lighten-2 title">Versão original</v-card-title>
                    <v-divider/>
                    <v-card-text>
                        <span v-html="tratarCampoVazio(campo.valor)"/>
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
                <v-card min-height="336px">
                    <v-card-title class="green lighten-2 title">Versão readequada</v-card-title>
                    <s-editor-texto
                        v-model="dadosReadequacaoEmEdicao.dsSolicitacao"
                        :style="''"
                        :min-char="minChar"
                        @text-change="atualizarForm($event)"
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
    name: 'TemplateTextarea',
    components: {
        SEditorTexto,
    },
    props: {
        campo: {
            type: Object,
            default: () => {},
        },
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
        atualizarForm() {
            this.updateCampo(this.dadosReadequacaoEmEdicao.dsSolicitacao);
        },
        updateCampo(e) {
            this.$emit('dados-update', e);
            this.atualizarContador(e.length);
        },
        atualizarContador(valor) {
            this.$emit('editor-texto-counter', valor);
        },
        tratarCampoVazio(value) {
            if (value.trim() === '') {
                const msgVazio = '<em>Campo vazio</em>';
                return msgVazio;
            }
            return value;
        },
        copiarOriginal() {
            this.dadosReadequacaoEmEdicao.dsSolicitacao = this.campo.valor;
            this.updateCampo(this.dadosReadequacaoEmEdicao.dsSolicitacao);
        },
    },
};
</script>
