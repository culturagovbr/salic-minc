<template>
    <v-card>
        <v-card-text>
            <v-container
                fluid>
                <div>
                    <v-layout
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            lg12
                            dark>
                            <b><h4>DADOS DO RECURSO</h4></b>
                            <v-divider class="pb-2"/>
                        </v-flex>
                        <v-flex>
                            <b>Recurso</b><br>
                            <span v-html="dadosRecurso.dsSolicitacaoRecurso"/>
                        </v-flex>
                    </v-layout>
                </div>

                <div>
                    <v-layout
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            lg12
                            dark>
                            <b>Dt. Desistência do Recurso</b>
                        </v-flex>
                        <v-flex>
                            <p>{{ dadosRecurso.dtSolicitacaoRecurso | formatarData }}</p>
                        </v-flex>
                    </v-layout>
                </div>
            </v-container>
        </v-card-text>
    </v-card>
</template>
<script>

import { utils } from '@/mixins/utils';

export default {
    name: 'DesistenciaRecursal',
    mixins: [utils],
    props: {
        dadosRecurso: {
            type: Object,
            default: () => {
            },
        },
    },
};
</script>
