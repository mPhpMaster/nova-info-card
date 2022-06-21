<template>
    <card class="flex flex-col justify-start px-3 overflow-hidden" :class="parseClass()" @click.native="fireClick()">
        <loading-view :loading="loading">
            <div class="px-3 py-3">
                <img v-if="image" :src="image" :class="image_class" class="" style="object-fit: cover;">
                <heading :level="2" class="text-sm pt-3 border-b border-40" v-if="title"> {{ title }}</heading>
                <template v-for="line in lines">
                    <p class="overflow-hidden" :class="line.class" v-html="line.text"/>
                </template>
            </div>
        </loading-view>
    </card>
</template>

<script>
export default {
    props: [
        'card',
    ],

    data: () => ({
        loading: false,
        title: '',
        lines: {},
        image: '',
        image_class: '',
        onclick: {
            type: null,
            data: {},
        },
    }),

    mounted() {
        this.registerData();
        // console.log(this.image_class, this.card)
    },

    methods: {
        linesCount() {
            return Object.keys(this.lines).length;
        },
        registerData() {
            this.title = this.card.title;
            this.image = this.card.image;
            this.image_class = this.card.image_class;
            this.lines = this.card.lines;
            this.onclick = this.card.onclick;

            return this;
        },
        fireClick() {
            this.loading = true;
            if (this.onclick.type === 'redirect') {
                return this.onclick.data.url && this.$router.push(this.onclick.data.url);
            }
        },
        parseClass() {
            let $class = this.onclick.type === 'redirect' ? 'cursor-pointer' : '';
            $class = [null, undefined, '', 'none'].includes(this.onclick.type) ? '' : $class;
            return $class;
        },
    }
}
</script>

<style scoped>
.one-line {
    margin: 0.8125rem auto;
}

</style>
