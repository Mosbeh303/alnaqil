<template>
  <div>
    <flash-messages class="m-7" />

    <div class="grid gap-4 lg:grid-cols-4 grid-cols-1 my-4 lg:px-7 px-4 lg:my-7">
      <!-- Create new shipment -->
      <div class="flex flex-col rounded-md shadow-sm text-center w-full" role="group">
        <Link
          :href="route('settings.general')"
          :class="{ 'bg-gray-800 text-gray-100': false }"
          class="bg-gray-100 py-3 px-4 text-sm font-medium text-gray-900 rounded-t-lg border-b border-gray-300"
        >
          {{ $page.props.language.general_settings }}
        </Link>
        <Link
          :href="route('settings.cities')"
          :class="{ 'bg-gray-800 text-gray-100': false }"
          class="bg-gray-100 py-3 px-4 text-sm font-medium text-gray-900 border-b border-gray-300"
        >
          {{ $page.props.language.cities_settings }}
        </Link>

        <Link
          :href="route('settings.messages')"
          :class="{ 'bg-gray-800 text-gray-100': false }"
          class="bg-gray-100 py-3 px-4 text-sm font-medium text-gray-900 border-b border-gray-300"
        >
          {{ $page.props.language.message_settings }}
        </Link>

        <Link
          :href="route('settings.notices')"
          :class="{ 'bg-gray-800 text-gray-100': false }"
          class="bg-gray-100 py-3 px-4 text-sm font-medium text-gray-900 rounded-b-lg"
        >
          {{ $page.props.language.note_settings }}
        </Link>
      </div>
      <div class="col-span-3 h-fit">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import Section from "@/Components/Section.vue";
import { Link } from "@inertiajs/vue3";
import FlashMessages from "@/Components/FlashMessages.vue";

export default {
  components: {
    Section,
    BreezeValidationErrors,
    FlashMessages,
    Link,
  },
  props: {
    language: Object,
  },
  data() {
    return {
      form: this.$inertia.form({}),
    };
  },

  methods: {},
};
</script>
