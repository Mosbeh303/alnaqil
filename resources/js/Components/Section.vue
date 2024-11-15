<template>
  <div
    class="border rounded border-gray-200 p-3 sm:p-5 bg-white"
    ref="warehouse"
    @click="warehouse = !warehouse"
  >
    <h2 v-if="title" class="text-gray-700 text-lg font-semibold" v-text="title" />
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    title: String,
  },
};
</script>
