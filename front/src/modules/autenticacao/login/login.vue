<template>
    <v-container
        fluid
        fill-height
    >
        <v-layout
            flex
            align-center
            justify-center
        >
            <v-flex
                xs12
                sm4
                elevation-6
            >
                <v-card class="green darken-4 justify-center">
                    <v-layout
                        row
                        align-center
                        justify-center
                    >
                        <img
                            class="ma-2 "
                            src="@/assets/logo_login.png"
                        >
                    </v-layout>
                    <v-card-text class="pt-4 white">
                        <div>
                            <v-form
                                ref="form"
                                v-model="valid"
                            >
                                <v-text-field
                                    v-model="CPF"
                                    :rules="CPFRules"
                                    required
                                    box
                                    append-icon="fas fa-edit"
                                    prepend-icon="account_circle"
                                    label="CPF"
                                    mask="###.###.###-##"
                                />
                                <v-text-field
                                    v-model="senha"
                                    :append-icon="e1 ? 'visibility' : 'visibility_off'"
                                    :type="e1 ? 'password' : 'text'"
                                    :rules="senhaRules"
                                    label="Senha"
                                    required
                                    box
                                    prepend-icon="lock"
                                    @click:append="() => (e1 = !e1)"
                                />
                                <v-layout justify-end>
                                    <a href="/autenticacao/index/solicitarsenha">Recuperar Senha</a>
                                </v-layout>
                                <v-layout justify-space-between>
                                    <v-btn
                                        :class=" { 'green darken-4 white--text' : valid, disabled: !valid }"
                                        color="teal lighten-1"
                                        dark
                                        block
                                        @click="submit()"
                                    >
                                        Entrar
                                    </v-btn>
                                </v-layout>
                                <v-layout justify-space-between>
                                    <v-btn
                                        outline
                                        color="orange darken-1"
                                        block
                                        href="/autenticacao/index/cadastrarusuario"
                                    >
                                        Cadastra-se
                                    </v-btn>
                                </v-layout>
                            </v-form>
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    data() {
        return {
            valid: true,
            e1: 'visibility',
            senha: '',
            CPF: '',
            CPFRules: [
                v => !!v || 'Preencher CPF!',
                v => this.TestaCPF(v) || 'CPF precisa ser valido!',
            ],
            senhaRules: [
                v => !!v || 'Preencher Senha!',
            ],
        };
    },
    computed: {
        ...mapGetters({
            loginGetter: 'autenticacao/loginGetter',
        }),
    },
    watch: {
        loginGetter(value) {
            if (value.status) {
                window.location.replace(value.redirect);
            }
        },
    },
    methods: {
        ...mapActions({
            loginAction: 'autenticacao/loginAction',
            setSnackbar: 'noticias/setDados',
        }),
        submit() {
            const user = { From: '', Login: this.CPF, Senha: this.senha };
            this.loginAction(user);
        },
        TestaCPF(strCPF) {
            let Soma;
            let Resto;
            Soma = 0;
            if (strCPF === '00000000000') return false;

            for (let i = 1; i <= 9; i += 1) Soma += parseInt(strCPF.substring(i - 1, i), 10) * (11 - i);
            Resto = (Soma * 10) % 11;

            if ((Resto === 10) || (Resto === 11)) Resto = 0;
            if (Resto !== parseInt(strCPF.substring(9, 10), 10)) return false;

            Soma = 0;
            for (let i = 1; i <= 10; i += 1) Soma += parseInt(strCPF.substring(i - 1, i), 10) * (12 - i);
            Resto = (Soma * 10) % 11;

            if ((Resto === 10) || (Resto === 11)) Resto = 0;
            if (Resto !== parseInt(strCPF.substring(10, 11), 10)) return false;
            return true;
        },
    },
};
</script>
