<template>
  <div class="flex items-center">
    <div class="flex w-full bg-white rounded">
      <slot />
      <SearchInput
        type="text"
        class="text-sm w-full bg-gray-100 border-0 outline-none mr-2"
        v-bind:placeholder="$page.props.language.searsh_placeholder"
        autocomplete="off"
        name="search"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
      />
    </div>
    <!--<button class="ml-3 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm" type="button" @click="$emit('reset')">Reset</button>-->
  </div>
</template>

<script>
import Dropdown from "@/Components/Dropdown";
import SearchInput from "@/Components/Input";

export default {
  components: {
    Dropdown,
    SearchInput,
  },
  props: {
    language: Object,
    modelValue: String,
    maxWidth: {
      type: Number,
      default: 300,
    },
  },
  emits: ["update:modelValue", "reset"],
};
</script>
