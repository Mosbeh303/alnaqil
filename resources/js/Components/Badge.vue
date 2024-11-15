<template>

<p
    class="rounded-xl py-1 px-4 w-fit text-xs text-white" :class="color"
>
    <slot />
</p>

</template>

<script>

export default {

    props: {
        color: String
    }
}
</script>
