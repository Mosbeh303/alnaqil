<template>
  <label :for="id" class="flex items-center cursor-pointer">
    <div class="relative">
      <input
        @click="clicked()"
        :id="id"
        type="checkbox"
        :checked="value"
        class="sr-only"
      />
      <div class="w-10 h-4 bg-gray-400 rounded-full shadow-inner toggle"></div>
      <div
        class="dot absolute w-6 h-6 bg-gray-100 rounded-full shadow -left-1 -top-1 transition"
      ></div>
    </div>
    <div class="ml-3 text-gray-700 font-medium">
      <slot />
    </div>
  </label>
</template>

<script>
import { v4 as uuid } from "uuid";

export default {
  //inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `toggle-${uuid()}`;
      },
    },
    value: [Boolean, Number],
  },
  emits: ["update:modelValue"],

  methods: {
    clicked() {
      this.$emit("inputclick");
    },
  },
};
</script>

<style scoped>
input:checked ~ .dot {
  transform: translateX(100%);
  /* background-color: #48bb78;*/
}

input:checked ~ .toggle {
  background-color: #48bb78;
}
</style>
