<template>
    <v-card
        v-if="usuario"
        class="mb-2">
        <v-card-title
            primary
            class="title">Usu&aacute;rio do sistema</v-card-title>
        <v-card-text>
            <v-layout
                row
                wrap>
                <v-flex
                    xs12
                    sm3
                    md3>
                    <b>CPF</b><br>
                    {{ usuario.cpf | formatarCpfOuCnpj }}
                </v-flex>
                <v-flex
                    xs12
                    sm6
                    md6>
                    <b>Nome</b>
                    <div>{{ usuario.nome }}</div>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>
<script>
import axios from 'axios';
import { utils } from '@/mixins/utils';

export default {
    name: 'AgenteUsuario',
    mixins: [utils],
    props: {
        idusuario: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            usuario: [],
        };
    },
    watch: {
        idusuario(value) {
            this.fetch(value);
        },
    },
    mounted() {
        if (typeof this.idusuario !== 'undefined') {
            this.fetch(this.idusuario);
        }
    },
    methods: {
        fetch(id) {
            if (id) {
                const self = this;
                axios.get(`/autenticacao/index/obter-dados-usuario/idUsuario/${id}`)
                    .then((response) => {
                        self.usuario = response.data.data;
                    });
            }
        },
    },
};
</script>
