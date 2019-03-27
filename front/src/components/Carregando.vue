<template>
    <div class="center-align padding10">
        <h6
            class=""
            v-html="text"/>
        <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-green-only">
                <div class="circle-clipper left">
                    <div class="circle"/>
                </div><div class="gap-patch">
                    <div class="circle"/>
                </div><div class="circle-clipper right">
                    <div class="circle"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Carregando',
    props: {
        text: {
            type: String,
            default: '',
        },
    },
};
</script>
