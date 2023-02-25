

export default {
    data() {
        return {
            count: 0,
            name: 'Francis'
        }
    },
    props: {
        nomen: Object
    },
    template: `
    <button class="niger" @click="count++">
        You clicked me {{count}} times.
        <br>{{name}} and {{nomen.text}}: {{nomen.number}}
    </button>`,
}