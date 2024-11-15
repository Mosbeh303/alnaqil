<template>
    <div v-show="message">
        <p class="text-sm mt-2 mr-2 text-red-600">
            {{ message }}
        </p>
    </div>
</template>

<script>
export default {
    props: ['message']
}
</script>
