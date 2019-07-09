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
                  edit
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
            if (this.idDocumentoAssinatura !== 0) {
                let url = '/assinatura/index/assinar-projeto?idDocumentoAssinatura=';
                url += `${this.getDocumentoAssinatura.idDocumentoAssinatura}&origin=#/readequacao/painel`;
                console.log(url);
                // window.location.href = url;
            }
        },
    },
};
</script>
