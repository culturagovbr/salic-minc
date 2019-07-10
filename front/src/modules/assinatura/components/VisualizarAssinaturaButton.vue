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
    </v-layout>
</template>
<script>
export default {
    name: 'VisualizarAssinaturaButton',
    props: {
        idDocumentoAssinatura: {
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
    methods: {
        abreLink() {
            let url = '/assinatura/index/visualizar-projeto?idDocumentoAssinatura=';
            url += `${this.idDocumentoAssinatura}&origin=#/readequacao/painel`;
            window.location.href = url;
        },
    },
};
</script>
