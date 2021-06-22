<template>
    <div>
        <div class="text-xs-center">
            <v-menu
                v-model="menuAtivo"
                :close-on-content-click="false"
                :nudge-bottom="10"
                offset-y
            >
                <v-btn
                    slot="activator"
                    icon
                    dark
                    @click="buscarSolicitacoes()"
                >
                    <v-badge
                        overlap
                        color="orange">
                        <span
                            v-if="quantidade"
                            slot="badge">{{ quantidade }}</span>
                        <v-icon dark>
                            message
                        </v-icon>
                    </v-badge>
                </v-btn>
                <v-card>
                    <v-layout
                        row
                        wrap
                        style="width: 450px; max-height: 500px; overflow-y: auto;">
                        <v-layout
                            v-if="loading"
                            align-center
                            justify-center
                            column
                            fill-height
                            style="min-height: 150px;">
                            <v-progress-circular
                                :width="3"
                                color="red"
                                indeterminate
                            />
                            <div class="subheading">Carregando ...</div>
                        </v-layout>
                        <v-list
                            v-else-if="Object.keys(solicitacoes).length > 0"
                            two-line
                            style="width: 100%">
                            <template v-for="(item, index) in solicitacoes">
                                <v-list-tile
                                    :key="item.title"
                                    :href="`/solicitacao/mensagem/visualizar/id/${item.idSolicitacao}`"
                                    avatar
                                    ripple
                                >
                                    <v-list-tile-content>
                                        <v-list-tile-title>
                                            {{ item.NomeProjeto }} <span class="grey--text text--lighten-1">
                                                <template v-if="item.idPronac">{{ item.idPronac }}</template>
                                                <template v-else>{{ item.idProjeto }}</template>
                                            </span>
                                        </v-list-tile-title>
                                        <v-list-tile-sub-title>{{ item.dsSolicitacao }}</v-list-tile-sub-title>
                                    </v-list-tile-content>

                                    <v-list-tile-action>
                                        <v-list-tile-action-text>{{ item.dtSolicitacao }}</v-list-tile-action-text>
                                        <v-icon
                                            v-if="item.idPronac"
                                            color="grey lighten-1">
                                            turned_in
                                        </v-icon>
                                        <v-icon
                                            v-else
                                            color="grey lighten-1">turned_in_not</v-icon>
                                    </v-list-tile-action>
                                </v-list-tile>
                                <v-divider
                                    v-if="index + 1 < solicitacoes.length"
                                    :key="index"
                                />
                            </template>
                        </v-list>
                        <v-layout
                            v-else
                            align-center
                            justify-center
                            row
                            fill-height
                            style="min-height: 150px;">
                            <v-icon
                                medium
                                left>mood</v-icon>
                            <div class="subheading">Você não tem solicitações para visualizar.</div>
                        </v-layout>
                    </v-layout>
                    <v-divider light/>
                    <v-card-actions class="text-md-center">
                        <v-layout
                            row
                            justify-center>
                            <v-btn
                                small
                                href="/solicitacao/mensagem">Ir para solicitações</v-btn>
                        </v-layout>
                    </v-card-actions>
                </v-card>
            </v-menu>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'HeaderSolicitacoes',
    data: () => ({
        menuAtivo: false,
        loading: false,
    }),
    computed: {
        ...mapGetters({
            solicitacoes: 'layout/solicitacoes',
            quantidade: 'layout/quantidadeSolicitacoes',
        }),
    },
    watch: {
        solicitacoes() {
            this.loading = false;
        },
    },
    methods: {
        ...mapActions({
            obterSolicitacoes: 'layout/obterSolicitacoes',
        }),
        buscarSolicitacoes() {
            if (this.menuAtivo === false) {
                this.loading = true;
                this.obterSolicitacoes();
            }
        },
    },
};
</script>
