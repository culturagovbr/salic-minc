<template>
    <v-layout>
        <v-btn
            v-if="idDocumentoAssinatura !== 0"
            dark
            icon
            flat
            small
            color="blue darken-3"
            @click.stop="abreVisualizar()"
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
    methods: {
        abreVisualizar() {
            let url = '/assinatura/index/visualizar-projeto?idDocumentoAssinatura=';
            url += `${this.getDocumentoAssinatura.idDocumentoAssinatura}&origin=#/readequacao/painel`;
            console.log(url);
            // window.location.href = url;
        },
    },
};
</script>
