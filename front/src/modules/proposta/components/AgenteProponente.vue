<template>
    <div
        v-if="proponente"
        id="proponente">

        <div
            v-if="identificacao"
            class="card">
            <div class="card-content">
                <h5>Identifica&ccedil;&atilde;o</h5>

                <div class="row">
                    <div class="col s12 l4 m4">
                        <b>CNPJ/CPF</b><br>
                        {{ identificacao.cnpjcpf }}
                    </div>
                    <div class="col s12 l4 m4">
                        <b>Nome Proponente</b><br>
                        {{ identificacao.descricao }}
                    </div>
                    <div class="col s12 l4 m4">
                        <b>Tipo de Pessoa</b><br>
                        {{ TipoPessoa }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>Endere&ccedil;o</h5>
                <table
                    v-if="proponente.enderecos"
                    class="bordered responsive-table">
                    <thead>
                        <tr>
                            <th>Tipo de Endere&ccedil;o</th>
                            <th>Tipo do Logradouro</th>
                            <th>Logradouro</th>
                            <th>N&uacute;mero</th>
                            <th>Complemento</th>
                            <th>Bairro</th>
                            <th>Cidade</th>
                            <th>UF</th>
                            <th>CEP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(endereco, index) in proponente.enderecos"
                            :key="index"
                        >
                            <td>{{ endereco.tipoendereco }}</td>
                            <td>{{ endereco.dstipologradouro }}</td>
                            <td>{{ endereco.logradouro }}</td>
                            <td>{{ endereco.numero }}</td>
                            <td>{{ endereco.complemento }}</td>
                            <td>{{ endereco.bairro }}</td>
                            <td>{{ endereco.municipio }}</td>
                            <td>{{ endereco.uf }}</td>
                            <td>{{ endereco.cep }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>Telefones</h5>
                <table
                    v-if="proponente.telefones"
                    class="bordered responsive-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>UF</th>
                            <th>DDD</th>
                            <th>N&uacute;mero</th>
                            <th>Pode Divulgar?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(telefone, index) in proponente.telefones"
                            :key="index"
                        >
                            <td>{{ telefone.dstelefone }}</td>
                            <td>{{ telefone.ufsigla }}</td>
                            <td>{{ telefone.ddd }}</td>
                            <td>{{ telefone.numero }}</td>
                            <td>{{ label_sim_ou_nao(telefone.divulgar) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>E-mail</h5>
                <table
                    v-if="proponente.emails"
                    class="bordered responsive-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>E-mail</th>
                            <th>Pode divulgar?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(email, index) in proponente.emails"
                            :key="index"
                        >
                            <td nowrap>{{ email.tipo }}</td>
                            <td nowrap>{{ email.descricao }}</td>
                            <td>{{ label_sim_ou_nao(email.divulgar) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>Natureza</h5>
                <table
                    v-if="proponente.natureza && proponente.natureza.length > 0"
                    class="bordered responsive-table">
                    <thead>
                        <tr>
                            <th>Direito</th>
                            <th>Esfera</th>
                            <th>Poder</th>
                            <th>Administra&ccedil;&atilde;o</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ proponente.natureza.direito }}</td>
                            <td>{{ proponente.natureza.esfera }}</td>
                            <td>{{ proponente.natureza.poder }}</td>
                            <td>{{ proponente.natureza.Admins }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>Natureza n&atilde;o informada</div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <h5>Dirigentes</h5>
                <table
                    v-if="proponente.dirigentes && proponente.dirigentes.length > 0"
                    class="bordered responsive-table">
                    <thead>
                        <tr>
                            <th width="20%">CPF</th>
                            <th>Nome</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(dirigente, index) in proponente.dirigentes"
                            :key="index"
                        >
                            <td align="center">{{ dirigente.cnpjcpfdirigente }}</td>
                            <td align="left">{{ dirigente.nomedirigente }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>N&atilde;o existem dirigentes cadastrados!</div>
            </div>
        </div>
    </div>
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'AgenteProponente',
    mixins: [
        utils,
    ],
    props: {
        idagente: { type: Number, default: 0 },
    },
    data() {
        return {
            proponente: [],
            identificacao: [],
        };
    },
    computed: {
        TipoPessoa() {
            return this.label_tipo_pessoa(this.identificacao.tipopessoa);
        },
    },
    watch: {
        idagente(value) {
            this.fetch(value);
        },
    },
    mounted() {
        if (typeof this.idagente !== 'undefined') {
            this.fetch(this.idagente);
        }
    },
    methods: {
        fetch(id) {
            if (id) {
                const self = this;
                /* eslint-disable */
                $3.ajax({
                    url: '/agente/visualizar/obter-dados-proponente/idAgente/' + id
                }).done(function (response) {
                    self.proponente = response.data;

                    if (self.proponente && self.proponente.identificacao) {
                        self.identificacao = self.proponente.identificacao;
                    }
                });
            }
        },
        label_tipo_pessoa(tipo) {
            let string = 'Pessoa F\xEDsica';

            if (tipo === '1') {
                string = 'Pessoa Jur\xEDdica';
            }

            return string;
        },
    },
};
</script>