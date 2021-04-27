<template>
    <v-list v-if="!isProponente">
        <v-list-tile v-if="Object.keys(perfis).length > 0">
            <v-list-tile-action>
                <v-icon color="indigo">
                    person
                </v-icon>
            </v-list-tile-action>
            <v-list-tile-content class="menu-perfis">
                <v-menu>
                    <v-list-tile-title
                        slot="activator"
                    >
                        <span><b>{{ perfilAtual.orgao_sigla_autorizada }}</b> - {{ perfilAtual.nome_grupo }}</span>
                        <v-icon>arrow_drop_down</v-icon>
                    </v-list-tile-title>
                    <v-list
                        v-if="perfis.length > 1"
                        style="min-width: 440px; max-height: calc(100vh - 200px); overflow-y: auto;"
                    >
                        <template
                            v-for="(perfil, index) in perfis"
                        >
                            <v-divider
                                :key="index"
                                :inset="isInset(perfil)" />
                            <v-list-tile
                                :key="perfil.id_unico"
                                @click="trocarPerfil(perfil)"
                            >
                                <div
                                    v-if="perfil.orgao_sigla_autorizada"
                                    style="cursor:pointer;"
                                >
                                    <b>{{ perfil.orgao_sigla_autorizada }}</b> - {{ perfil.nome_grupo }}
                                </div>
                                <div
                                    v-else
                                    style="cursor:pointer;"
                                    @click="trocarPerfil(perfil)"
                                >
                                    {{ perfil.nome_grupo }}
                                </div>
                            </v-list-tile>
                        </template>
                    </v-list>
                </v-menu>
            </v-list-tile-content>
        </v-list-tile>
        <v-list-tile v-else>
            <v-list-tile-action>
                <v-progress-circular
                    :width="3"
                    color="red"
                    indeterminate
                />
            </v-list-tile-action>
            <v-list-tile-content>
                <div>Carregando perfis ...</div>
            </v-list-tile-content>
        </v-list-tile>
    </v-list>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

let orgaoSuperiorAnterior = null;
export default {
    name: 'HeaderInformacoesDaContaPerfis',
    data() {
        return {
            loadingPerfis: true,
            perfilTeste: null,
        };
    },
    computed: {
        ...mapGetters({
            perfis: 'layout/perfisDisponiveis',
            usuario: 'autenticacao/getUsuario',
        }),
        perfilAtual() {
            if (!this.usuario || !this.perfis) {
                return {};
            }
            const grupoAtivo = this.usuario.grupo_ativo;
            const orgaoAtivo = this.usuario.orgao_ativo;
            const perfil = this.perfis.map((value) => {
                if (parseInt(value.gru_codigo, 10) === parseInt(grupoAtivo, 10)
                        && parseInt(value.uog_orgao, 10) === parseInt(orgaoAtivo, 10)) {
                    return value;
                }
                return false;
            }).filter(value => (value !== false))[0];
            return perfil || {};
        },
        isProponente() {
            return this.perfilAtual.gru_codigo === 1111;
        },
    },
    watch: {
        usuario(value) {
            if (Object.keys(value).length > 0) {
                this.loadingPerfis = false;
            }
        },
    },
    created() {
        this.buscarPerfisDisponiveis();
    },
    methods: {
        ...mapActions({
            buscarPerfisDisponiveis: 'layout/buscarPerfisDisponiveis',
            alterarPerfil: 'layout/alterarPerfil',
        }),
        trocarPerfil(perfil) {
            this.alterarPerfil(perfil);
        },
        isInset(perfil) {
            if (orgaoSuperiorAnterior === perfil.org_superior) {
                return true;
            }
            orgaoSuperiorAnterior = perfil.org_superior;
            return false;
        },
    },
};
</script>

<style>
    .menu-perfis .v-list .v-list__tile {
        font-size: 14px !important;
    }
</style>
