Vue.component('sl-planilha-button', {
    props: [
        'typeButton',
    ],
    template: `
        <a class="btn" v-bind:class="typeButton['colorButton']" title="Comprovar item" v-bind:href="typeButton['url']">
            <i class="material-icons small">{{typeButton['icon']}}</i>
        </a>
    `
});
