<template>
    <div
        class="card center-align"
        style="margin: 20px auto; width:80%">
        <div class="card-content">
            <div class="row">
                <div class="col s2 right-align"><i class="medium red-text material-icons">not_interested</i></div>
                <div class="col s10 left-align">
                    <br>
                    <b v-html="texto"/>
                    <a
                        v-if="urlRetorno"
                        href="urlRetorno"> {{ msgUrlRetorno }}</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SalicMensagemErro',
    props: {
        texto: {
            type: String,
            default: '',
        },
        urlRetorno: {
            type: String,
            default: '',
        },
        msgUrlRetorno: {
            type: String,
            default: '',
        },
    },
};
</script>
