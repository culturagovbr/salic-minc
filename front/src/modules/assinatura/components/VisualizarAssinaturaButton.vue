<template>
    <v-layout>
        <v-tooltip
            v-if="idDocumentoAssinatura"
            bottom
        >
            <v-icon
                slot="activator"
                color="blue darken-3"
                @click.stop="dialog = true"
            >
                visibility
            </v-icon>
            <span>Visualizar assinatura</span>
        </v-tooltip>
        <div
            v-else
            style="width:24px"
        />
        <v-dialog
            v-model="dialog"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            @keydown.esc="dialog = false"
        >
            <v-card>
                <carregando
                    v-if="loading"
                    :text="'Montando assinatura...'"
                    class="mt-5"
                />
                <div
                    v-else
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
import Carregando from '@/components/CarregandoVuetify';

Vue.use(VueResource);

export default {
    name: 'VisualizarAssinaturaButton',
    components: {
        Carregando,
    },
    props: {
        idDocumentoAssinatura: {
            type: [Number, String],
            default: 0,
        },
        origin: {
            type: String,
            default: '',
        },
        modal: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            dialog: false,
            htmlAssinatura: '',
            loading: true,
        };
    },
    watch: {
        dialog() {
            if (this.dialog === false) {
                this.htmlAssinatura = '';
            } else {
                this.abreLink();
            }
        },
        htmlAssinatura() {
            if (this.htmlAssinatura !== '') {
                this.loading = false;
            }
        },
    },
    methods: {
        abreLink() {
            let url = '/assinatura/index/visualizar-projeto?idDocumentoAssinatura=';
            url += `${this.idDocumentoAssinatura}`;
            if (this.modal === true) {
                url += '&modal=1';
            }
            if (this.origin) {
                url += `&origin=${this.origin}`;
            }
            this.$http.get(url).then((response) => {
                this.dialog = true;
                const cssLink = '/public/library/materialize/css/materialize.css?v=v7.0.6';
                this.htmlAssinatura = `<link href="${cssLink}" media="all" rel="stylesheet" type="text/css" >`;
                this.htmlAssinatura += response.data;
            });
        },
    },
};
</script>
