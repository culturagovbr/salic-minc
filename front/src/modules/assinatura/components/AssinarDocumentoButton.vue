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
    </v-layout>
</template>
<script>
export default {
    name: 'AssinarDocumentoButton',
    props: {
        idDocumentoAssinatura: {
            type: [Number, String],
            default: 0,
        },
    },
    methods: {
        abreLink() {
            if (this.idDocumentoAssinatura !== 0) {
                let url = '/assinatura/index/assinar-projeto?idDocumentoAssinatura=';
                url += `${this.idDocumentoAssinatura}&origin=#/readequacao/painel`;
                window.location.href = url;
            }
        },
    },
};
</script>
