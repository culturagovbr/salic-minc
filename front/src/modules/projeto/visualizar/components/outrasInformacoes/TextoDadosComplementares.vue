<template>
    <div
        v-if="texto.length > 1"
        v-html="texto"/>
    <div v-else>
        <div
            class="text-xs-left pa-2">
            Não informado(a)
        </div>
    </div>
</template>

<script>

export default {
    name: 'TextoDadosComplementares',
    props: {
        texto: {
            type: String,
            default: '',
        },
    },
};
</script>
