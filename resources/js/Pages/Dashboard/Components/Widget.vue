<template>
  <div
    class="px-5 py-6 bg-white rounded border border-gray-200"
    :class="
      type == 'vertical'
        ? 'md:text-right text-center'
        : 'flex justify-center items-center'
    "
  >
    <div class="" :class="type == 'vertical' ? 'flex justify-center ' : 'pl-4'">
      <icon
        :name="iconName"
        :class="[
          color,
          type == 'vertical' ? ' h-16 w-16  md:h-12 md:w-12 m-0' : 'h-16 w-16 ',
        ]"
      />
    </div>
    <div :class="type == 'vertical' ? 'text-center mt-2' : 'pl-4'">
      <div
        class="text-gray-600 font-semibold mb-2"
        :class="type == 'vertical' ? 'md:text-sm text-base' : 'text-base' + textColor"
      >
        {{ widgetText }}
      </div>
      <div
        class="flex items-center pt-1"
        :class="type == 'vertical' ? 'justify-center' : ''"
      >
        <div
          class="font-bold"
          :class="type == 'vertical' ? 'md:text-lg text-2xl' : 'text-2xl'"
        >
          {{ widgetValue }}
        </div>
        <span
          v-if="widgetOption"
          class="flex items-center px-2 py-0.5 mx-2 text-sm text-green-600 bg-green-100 rounded-full"
        >
          <span>{{ widgetOption }} </span>
          <svg
            class="w-4 h-4"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M18 15L12 9L6 15"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            ></path>
          </svg>
        </span>
      </div>
      <div>
        <p class="text-xs">
          {{ subText }}
          <span class="text-sm text-red-600 font-semibold"> {{ subValue }}</span>
        </p>
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
  props: [
    "color",
    "type",
    "iconName",
    "widgetText",
    "widgetValue",
    "widgetOption",
    "textColor",
    "subText",
    "subValue",
  ],
};
</script>
