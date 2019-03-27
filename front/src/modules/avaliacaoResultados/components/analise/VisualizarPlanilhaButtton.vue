<template>
    <v-btn
        :to="{ name: 'VisualizarPlanilha', params:{ id: idPronac }}"
        flat
        icon>
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                class="material-icons">visibility
            </v-icon>
            <span>Visualizar Projeto</span>
        </v-tooltip>
    </v-btn>
</template>

<script>
export default {
    name: 'VisualizarPlanilhaButtton',
    props: {
        idPronac: { type: String, default: '' },
    },
    data() {
        return {
        };
    },
    computed: {
    },
    methods: {
    },
};
</script>
