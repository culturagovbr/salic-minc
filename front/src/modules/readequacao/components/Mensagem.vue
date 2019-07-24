<template>
    <v-snackbar
        :color="mensagem.cor"
        :timeout="mensagem.timeout"
        v-model="mensagem.ativa"
        bottom
    ><span>{{ mensagem.conteudo }}</span>
        <v-btn
            dark
            flat
            @click="mensagem.ativa = false"
        >
            Fechar
        </v-btn>
    </v-snackbar>
</template>
<script>

export default {
    name: 'Mensagem',
    props: {
        mensagem: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
