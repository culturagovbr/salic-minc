<template>
    <v-layout>
        <v-btn
            v-if="idDocumentoAssinatura"
            dark
            icon
            flat
            small
            color="blue darken-3"
            @click.stop="abreLink()"
        >
            <v-tooltip bottom>
                <v-icon slot="activator">
                    edit
                </v-icon>
                <span>Assinar</span>
            </v-tooltip>
        </v-btn>
        <v-dialog
            v-model="dialog"
            hide-overlay
            fullscreen
            @keydown.esc="dialog = false"
        >
            <v-card>
                <div
                    class="pa-3"
                    v-html="htmlAssinatura"
                />
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
import Vue from 'vue';
import VueResource from 'vue-resource';

Vue.use(VueResource);

export default {
    name: 'AssinarDocumentoButton',
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        idDocumentoAssinatura: {
            type: [Number, String],
            default: 0,
        },
        idTipoDoAtoAdministrativo: {
            type: [Number, String],
            default: 0,
        },
    },
    data() {
        return {
            dialog: false,
            htmlAssinatura: '',
        };
    },
    watch: {
        dialog() {
            if (this.dialog === false) {
                this.htmlAssinatura = '';
            }
        },
    },
    methods: {
        abreLink() {
            if (this.idDocumentoAssinatura !== 0) {
                let url = `/assinatura/index/assinar-projeto?IdPRONAC=${this.dadosReadequacao.idPronac}`;
                url += `&idTipoDoAtoAdministrativo=${this.idTipoDoAtoAdministrativo}`;
                url += '&modal=1&origin=#/readequacao/painel';
                this.$http.get(url).then((response) => {
                    this.dialog = true;
                    const linkCss = '/public/library/materialize/css/materialize.css?v=v7.0.6';
                    this.htmlAssinatura = `<link href="${linkCss}" media="all" rel="stylesheet" type="text/css" >`;
                    this.htmlAssinatura += response.data;
                });
            }
        },
    },
};
</script>
