<template>
  <textarea
    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
    :class="{ '!border-red-500': error }"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    ref="input"
  >
  </textarea>
</template>

<script>
import InputError from "@/Components/InputError.vue";

export default {
  components: {
    InputError,
  },
  props: ["modelValue", "error"],

  emits: ["update:modelValue"],

  methods: {
    focus() {
      this.$refs.input.focus();
    },
  },
};
</script>
