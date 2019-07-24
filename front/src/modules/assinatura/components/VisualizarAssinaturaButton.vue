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
                    find_in_page
                </v-icon>
                <span>Visualizar assinatura</span>
            </v-tooltip>
        </v-btn>
        <v-dialog
            v-model="dialog"
            hide-overlay
            justify-center
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
    name: 'VisualizarAssinaturaButton',
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
