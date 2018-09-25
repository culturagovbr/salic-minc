<template>
    <v-container fluid>
        <h1 class="display-2 font-weight-thin">Análise Técnica</h1>
        <v-card>
            <v-tabs
                    centered
                    color="green"
                    dark
                    icons-and-text
            >
                <v-tabs-slider color="deep-orange accent-3"></v-tabs-slider>

                <v-tab href="#tab-1">
                    Analisar
                    <v-icon>how_to_reg</v-icon>
                </v-tab>

                <v-tab href="#tab-2">
                    Assinados
                    <v-icon>done_all</v-icon>
                </v-tab>

                <v-tab-item
                        :id="'tab-1'"
                        :key="1"
                >
                    <v-card flat>
                        <v-card-text>
                            <TabelaProjetos
                                :analisar="true"
                            ></TabelaProjetos>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
                <v-tab-item
                        :id="'tab-2'"
                        :key="2"
                >
                    <v-card flat>
                        <v-card-text>
                            <TabelaProjetos
                            ></TabelaProjetos>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs>
        </v-card>
    </v-container>
</template>

<script>
import TabelaProjetos from './TabelaProjetos';

export default {
    name: 'Painel',
    data() {
        return {
        };
    },
    components: {
        TabelaProjetos,
    },
};
</script>
