<template>
  <div class="mb-4 relative">
    <div v-if="submenus">
      <button
        :href="href"
        :class="classes"
        class="text-base w-full px-4 py-3"
        @click="isActive = !isActive"
      >
        <slot />

        <div
          class="absolute top-3"
          :class="{
            'left-2': $page.props.locale == 'ar',
            'right-2': $page.props.locale == 'en',
          }"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 text-gray-400"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              v-if="!isActive"
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
            <path
              v-else
              fill-rule="evenodd"
              d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </button>

      <ul
        :class=" isActive? 'max-h-auto' : 'max-h-0'"
        class="transition-all ease-in-out duration-1000 overflow-hidden mt-2"
      >
        <li v-for="(submenu, index) in submenus" :key="index">
          <Link
            @click="clicked()"
            v-if="submenu.display === undefined || submenu.display === true"
            :href="submenu.href"
            :class="submenu.classes"
            class="text-gray-600 mr-6 mt-3 inline-block"
          >
            <Icon name="list" :class="{ 'rotate-180': $page.props.locale == 'en' }" />
            {{ submenu.text }}
          </Link>
        </li>
      </ul>
    </div>

    <div v-else>

      <Link
        @click="clicked()"
        :href="href"
        :class="classes"
        class="text-base w-full px-4 py-3"
      >
        <slot />
      </Link>
    </div>
  </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import Icon from "@/Components/Icon.vue";

export default {
  components: {
    Link,
    Icon,
  },

  data() {
    return {
     isActive: this.isActive
    };
  },

  props: ["href", "active", "submenus","isActive"],

  computed: {
    classes() {
      return this.active
        ? "inline-flex items-start   bg-gray-100 rounded  font-medium leading-5 text-gray-800 focus:outline-none focus:border-indigo-700 transition  duration-150 ease-in-out"
        : "inline-flex  font-medium leading-5 text-gray-800 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:text-gray-700 focus:bg-gray-100 transition duration-150 ease-in-out rounded";
    },
  },
  methods: {
    dropDown(){},
    clicked() {
      this.$emit("navlinkclick");
    },
  },
};
</script>
