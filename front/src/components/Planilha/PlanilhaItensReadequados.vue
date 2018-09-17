<template>
    <div class="itens">
        <table class="bordered">
            <thead>
            <tr>
                <th class="center-align">#</th>
                <th class="left-align">Item</th>
                <th class="left-align">Unidade</th>
                <th class="center-align">Dias</th>
                <th class="center-align">Qtde</th>
                <th class="center-align">Ocor.</th>
                <th class="right-align">Vl. Unit&aacute;rio</th>
                <th class="right-align">Vl. Aprovado</th>
                <th class="right-align">Vl. Comprovado</th>
                <th class="center-align">Justf. de Readequa&ccedil;&atilde;o</th>
                <th class="center-align">A&ccedil;&atilde;o</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row of table"
                :key="row.idPlanilhaProposta"
                :class="definirClasseItem(row)"
                v-if="isObject(row)">
                <td class="center-align">{{ row.Seq }}</td>
                <td class="left-align">{{ row.Item }}</td>
                <td class="center-align">{{ row.Unidade }}</td>
                <td class="center-align">{{ row.QtdeDias }}</td>
                <td class="center-align">{{ row.Quantidade }}</td>
                <td class="center-align">{{ row.Ocorrencia }}</td>
                <td class="right-align">{{ row.vlUnitario | filtroFormatarParaReal }}</td>
                <td class="right-align">{{ row.vlAprovado | filtroFormatarParaReal }}</td>
                <td class="right-align">{{ row.VlComprovado | filtroFormatarParaReal }}</td>
                <td class="justify" width="30%" v-html="row.JustProponente"></td>
                <td class="center-align" v-html="row.DescAcao"></td>
            </tr>
            </tbody>
            <tfoot v-if="table && Object.keys(table).length > 0" style="opacity: 0.5">
            <tr>
                <td colspan="7"><b>Totais</b></td>
                <td class="right-align"><b>{{ obterValorAprovadoTotal(table) }}</b></td>
                <td class="right-align"><b>{{ obterValorComprovadoTotal(table) }}</b></td>
                <td colspan="2" class="right-align"></td>
            </tr>
            </tfoot>
        </table>
    </div>
</template>

<script>
    import planilhas from '@/mixins/planilhas';

    export default {
        mixins: [planilhas],
        props: {
            table: {},
        },
    };
</script>
