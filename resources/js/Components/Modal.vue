<template>
    <!-- Main modal -->
    <div v-show="show" class="bg-gray-900 bg-opacity-40 flex   overflow-x-hidden fixed right-0 left-0 top-0 z-50 justify-center items-center h-full md:inset-0">
        <div class="relative px-4 w-full max-w-xl h-auto ">
            <!-- Modal content -->
            <div class="relative max-h-screen overflow-x-auto bg-white rounded-lg shadow  px-5 py-8  text-center">
                <div class="w-full">
                    <slot/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Icon from "@/Components/Icon.vue";
export default {
    components: {
        Icon,
    },

    props: ['show'],

    methods: {
        hidden:  function () {
            this.show = false ;
        },
    }
}
</script>

