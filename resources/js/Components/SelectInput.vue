<template>
  <select
    :id="id"
    ref="input"
    v-model="selected"
    v-bind="{ ...$attrs, class: null }"
    :class="{ '!border-red-500': error }"
    class="text-gray-600 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
  >
    <option value="" disabled>{{ selectPlaceholder }}</option>
    <slot />
  </select>
  <div v-if="error" class="mt-2 mr-2 list-disc list-inside text-sm text-red-600">
    {{ error }}
  </div>
</template>

<script>
import { v4 as uuid } from "uuid";

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `select-input-${uuid()}`;
      },
    },
    error: String,
    label: String,
    modelValue: [String, Number, Boolean],
    selectPlaceholder: String,
    readonly: Boolean,
  },
  emits: ["update:modelValue"],
  data() {
    return {
      selected: this.modelValue,
    };
  },
  watch: {
    selected(selected) {
      this.$emit("update:modelValue", selected);
    },
  },
  methods: {
    focus() {
      this.$refs.input.focus();
    },
    select() {
      this.$refs.input.select();
    },
  },
};
</script>
