<template>
    <div>
        <div v-if="dados">
            <v-toolbar
                app
                dense
                dark
                color="primary"
                :clipped-left="$vuetify.breakpoint.mdAndUp"
                fixed
                height="50px"
            >
                <!--<v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>-->
                <v-btn icon large href="/principal">
                    <v-avatar size="38px">
                        <img
                            src="/public/img/logomarca.png"
                            alt="Ir para o in&iacute;cio"
                        >
                    </v-avatar>
                </v-btn>
                <v-toolbar-title class="ma-0 hidden-sm-and-down">Salic</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-toolbar-items class="hidden-sm-and-down">
                    <v-menu offset-y v-for="(item, index) in dadosMenu" :key="index">
                        <v-btn v-if="item.menu"
                               slot="activator"
                               flat
                               dark
                               small
                               class="caption"
                        >
                            <span v-html="item.label"></span>
                            <!--<v-icon right dark class="ma-0">arrow_drop_down</v-icon>-->
                        </v-btn>
                        <v-list v-if="item.menu" class="pa-0">
                            <v-list-tile
                                v-for="(sub, index) in item.menu"
                                :key="index"
                                :href="('/' + sub.url.module + '/' + sub.url.controller + '/' + sub.url.action)"
                            >
                                <v-list-tile-title
                                ><span v-html="sub.label"></span></v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                        <v-btn v-else flat dark small class="caption"
                               :href="'/' + item.url.module + '/' + item.url.controller + '/' + item.url.action">
                            <span v-html="item.label"></span>
                        </v-btn>
                    </v-menu>
                </v-toolbar-items>

                <!--<v-btn icon>-->
                    <!--<v-icon>message</v-icon>-->
                <!--</v-btn>-->
                <v-menu
                    :close-on-content-click="false"
                    offset-x
                >
                    <v-btn flat slot="activator" class="pa-0" :loading="loadingUsuario">
                        <v-avatar color="teal" size="30px" class="mr-1 left">
                            <span class="white--text headline">{{primeiraLetraNomeUsuario}}</span>
                        </v-avatar>
                        <span class="hidden-sm-and-down text-capitalize">{{nomeUsuario}}</span>
                        <v-icon right dark class="ma-0 hidden-sm-and-down">arrow_drop_down</v-icon>
                    </v-btn>

                    <v-card>
                        <v-list>
                            <v-list-tile avatar>
                                <v-list-tile-avatar>
                                    <v-avatar color="teal" size="35px" class="mr-1 left">
                                        <span class="white--text headline">{{primeiraLetraNomeUsuario}}</span>
                                    </v-avatar>
                                </v-list-tile-avatar>

                                <v-list-tile-content>
                                    <v-list-tile-title>{{nomeUsuarioCompleto}}</v-list-tile-title>
                                    <v-list-tile-sub-title>{{cpfUsuario}}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>

                        <v-divider></v-divider>
                        <v-list v-if="Object.keys(perfisDisponiveis).length > 0">
                            <v-list-tile>
                                <v-list-tile-action>
                                    <v-icon color="indigo">person</v-icon>
                                </v-list-tile-action>

                                <v-list-tile-content>
                                    <v-menu>
                                        <v-list-tile-title
                                            v-if="perfisDisponiveis && perfisDisponiveis[grupoSelecionadoIndex].orgao_sigla_autorizada"
                                            slot="activator"
                                        >
                                            <span>{{perfisDisponiveis[grupoSelecionadoIndex].orgao_sigla_autorizada}} - {{perfisDisponiveis[grupoSelecionadoIndex].nome_grupo}}</span>
                                            <v-icon>arrow_drop_down</v-icon>
                                        </v-list-tile-title>
                                        <v-list-tile-title
                                            v-else
                                            slot="activator"
                                        >
                                            <span>{{perfisDisponiveis[grupoSelecionadoIndex].nome_grupo}}</span>
                                            <v-icon dark>arrow_drop_down</v-icon>
                                        </v-list-tile-title>
                                        <v-list class="scrollable">
                                            <v-list-tile v-for="(perfil, index) in perfisDisponiveis" :key="index">
                                                <div v-if="perfil.orgao_sigla_autorizada" @click="trocarPerfil(perfil)"
                                                     style="cursor:pointer;">
                                                    {{perfil.orgao_sigla_autorizada}} - {{perfil.nome_grupo}}
                                                </div>
                                                <div v-else @click="trocarPerfil(perfil)" style="cursor:pointer;">
                                                    {{perfil.nome_grupo}}
                                                </div>
                                            </v-list-tile>
                                        </v-list>
                                    </v-menu>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>

                        <v-divider></v-divider>

                        <v-list>
                            <v-list-tile :href="'/autenticacao/index/alterarsenhausuario'">
                                <v-list-tile-action>
                                    <v-icon color="indigo">lock</v-icon>
                                </v-list-tile-action>

                                <v-list-tile-content>
                                    <v-list-tile-title>Alterar senha</v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-list-tile :href="'/autenticacao/index/logout'">
                                <v-list-tile-action>
                                    <v-icon color="indigo">power_settings_new</v-icon>
                                </v-list-tile-action>

                                <v-list-tile-content>
                                    <v-list-tile-title>Sair do sistema</v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </v-card>
                </v-menu>
            </v-toolbar>
        </div>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';

    export default {
        name: 'SlNav',
        components: {},
        props: {
            dadosMenu: {},
        },
        data() {
            return {
                dados: this.dadosMenu,
                loadingUsuario: true,
            };
        },
        computed: {
            ...mapGetters({
                perfisDisponiveis: 'menuSuperior/perfisDisponiveis',
                usuarioAtivo: 'menuSuperior/usuarioAtivo',
                grupoAtivo: 'menuSuperior/grupoAtivo',
                grupoSelecionadoIndex: 'menuSuperior/grupoSelecionadoIndex',
            }),
            nomeUsuarioCompleto() {
                if (this.usuarioAtivo[0]) {
                    this.loadingUsuario = false;
                    return this.usuarioAtivo[0].usu_nome;
                }
                return '';
            },
            primeiraLetraNomeUsuario() {
                return this.nomeUsuarioCompleto.substr(0, 1);
            },
            nomeUsuario() {
                return this.nomeUsuarioCompleto.split(' ')[0];
            },
            cpfUsuario() {
                if (this.usuarioAtivo[0]) {
                    return this.usuarioAtivo[0].usu_identificacao;
                }
                return '';
            }
        },
        created() {
            this.buscarPerfisDisponiveis();
        },
        methods: {
            ...mapActions({
                buscarPerfisDisponiveis: 'menuSuperior/buscarPerfisDisponiveis',
                alterarPerfil: 'menuSuperior/alterarPerfil',
            }),
            trocarPerfil(perfil) {
                this.alterarPerfil(perfil);
            },
        },
    };
</script>

<style>
    .scrollable {
        width: 500px;
        height: 750px;
        overflow: scroll;
    }
</style>
