<template>
    <v-card>
        <v-card-title
            class="title"
        >
            Legenda - cores dos itens
        </v-card-title>
        <v-card-text
            class="text-xs-center"
        >
            <div
                width="20"
                height="20"
                class="blue lighten-5 pa-3"
            >
                aumento do valor
            </div>
            <div
                width="20"
                height="20"
                class="orange lighten-5 pa-3"
            >
                redução do valor
            </div>
            <div
                width="20"
                height="20"
                class="indigo lighten-5 pa-3"
            >
                alteração de unidade
            </div>
            <div
                width="20"
                height="20"
                class="green lighten-5 pa-3"
            >
                item adicionado
            </div>
            <div
                width="20"
                height="20"
                class="red lighten-4 pa-3"
            >
                item removido
            </div>
        </v-card-text>
    </v-card>
</template>
<script>
export default {
    name: 'LegendaPlanilha',
};
</script>
