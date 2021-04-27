<template>
    <v-menu
        :close-on-content-click="false"
        offset-x
    >
        <v-btn
            slot="activator"
            :loading="loadingUsuario"
            :icon="$vuetify.breakpoint.smAndDown"
            class="pa-0"
            flat
            @click="isExibirPerfis = true"
        >
            <v-avatar
                color="teal"
                size="30px"
                class="mr-1 left">
                <span class="white--text headline">{{ primeiraLetraNomeUsuario }}</span>
            </v-avatar>
            <span class="hidden-sm-and-down text-capitalize">{{ nomeUsuario }}</span>
            <v-icon
                right
                dark
                class="ma-0 hidden-sm-and-down">arrow_drop_down</v-icon>
        </v-btn>

        <v-card style="width: 440px;">
            <v-list>
                <v-list-tile avatar>
                    <v-list-tile-avatar>
                        <v-avatar
                            color="teal"
                            size="35px"
                            class="mr-1 left">
                            <span class="white--text headline">{{ primeiraLetraNomeUsuario }}</span>
                        </v-avatar>
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ nomeUsuarioCompleto }}</v-list-tile-title>
                        <v-list-tile-sub-title>{{ cpfUsuario }}</v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>

            <v-divider/>

            <HeaderInformacoesDaContaPerfis v-if="isExibirPerfis"/>

            <v-divider/>

            <v-list>
                <v-list-tile :href="'/autenticacao/index/alterarsenhausuario'">
                    <v-list-tile-action>
                        <v-icon color="indigo">lock</v-icon>
                    </v-list-tile-action>

                    <v-list-tile-content>
                        <v-list-tile-title>Alterar senha</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile>
                    <v-list-tile-action>
                        <v-switch
                            v-model="switchModoNoturno"
                            color="indigo"
                        />
                    </v-list-tile-action>

                    <v-list-tile-content>
                        <v-list-tile-title>Modo noturno</v-list-tile-title>
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
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import HeaderInformacoesDaContaPerfis from '@/components/layout/header/HeaderInformacoesDaContaPerfis';

export default {
    name: 'HeaderInformacoesDaConta',
    components: { HeaderInformacoesDaContaPerfis },
    data() {
        return {
            loadingUsuario: true,
            isExibirPerfis: false,
            switchModoNoturno: false,
        };
    },
    computed: {
        ...mapGetters({
            usuario: 'autenticacao/getUsuario',
            isModoNoturno: 'layout/modoNoturno',
        }),
        nomeUsuarioCompleto() {
            if (Object.keys(this.usuario).length > 0) {
                return this.usuario.usu_nome;
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
            if (this.usuario) {
                return this.usuario.usu_identificacao;
            }
            return '';
        },
    },
    watch: {
        usuario(value) {
            if (Object.keys(value).length > 0) {
                this.loadingUsuario = false;
            }
        },
        isModoNoturno(value) {
            this.switchModoNoturno = value;
        },
        switchModoNoturno(value) {
            this.atualizarModoNoturno(value);
        },
    },
    methods: {
        ...mapActions({
            atualizarModoNoturno: 'layout/atualizarModoNoturno',
        }),
    },
};
</script>
