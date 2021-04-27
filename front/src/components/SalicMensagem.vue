<template>
    <v-container
        fill-height
        style="min-height:calc(100vh - 400px);"
    >
        <v-layout
            align-center
        >
            <v-flex class="text-xs-center">
                <h1 :class="`display-2 ${type}--text`">
                    {{ title }}
                </h1>
                <p
                    class="headline mt-2"
                    v-html="texto"
                />
                <v-btn
                    v-if="urlRetorno"
                    :color="type"
                    :href="urlRetorno"
                    outline
                    v-html="msgUrlRetorno"
                />
                <v-btn
                    v-if="rotaRetorno"
                    :color="type"
                    :to="rotaRetorno"
                    outline
                    v-html="msgUrlRetorno"
                />
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
export default {
    name: 'SalicMensagem',
    props: {
        texto: {
            type: String,
            default: '',
        },
        urlRetorno: {
            type: String,
            default: '',
        },
        msgUrlRetorno: {
            type: String,
            default: 'Voltar',
        },
        rotaRetorno: {
            type: [String, Object],
            default: '',
        },
        type: {
            type: String,
            default: 'Voltar',
        },
    },
    computed: {
        title() {
            let title = '';
            switch (this.type) {
            case 'error':
                title = 'Erro! :(';
                break;
            case 'success':
                title = 'Sucesso! :)';
                break;
            default:
                title = 'Atenção!';
            }
            return title;
        },
    },
};
</script>
