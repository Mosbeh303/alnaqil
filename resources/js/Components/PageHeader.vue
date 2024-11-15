<template>
  <div class="bg-gray-100 py-6 lg:py-8">
    <!--dark:bg-gray-800-->
    <div class="container px-6 mx-auto flex items-center md:items-center justify-between">
      <div class="w-fit md:w-full">
        <p
          role="navigation"
          aria-label="breadcrumbs"
          class="flex items-center text-indigo-700 text-xs"
        >
          <slot name="breadcrumb"></slot>
        </p>
        <h1 class="text-2xl font-bold leading-tight text-gray-800 mt-2 relative w-full">
          <slot />
        </h1>
      </div>
      <div class="">
        <Button
          v-if="secondaryButton"
          :href="secondaryButton.href"
          class="!bg-gray-200 !text-gray-600 hover:!text-white"
        >
          {{ secondaryButton.text }}
        </Button>

        <Button
          v-if="button && (typeof button.enable == 'undefined' || button.enable == true)"
          :method="button.method"
          :href="button.href"
          class="whitespace-nowrap"
          :class="button.class"
        >
          {{ button.text }}
        </Button>
      </div>
    </div>
  </div>
</template>

<script>
import Button from "@/Components/Button.vue";

export default {
  components: {
    Button,
  },
  props: {
    button: {
      default: false,
    },
    secondaryButton: {
      default: false,
    },
  },
};
</script>
