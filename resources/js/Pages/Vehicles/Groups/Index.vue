<template>
  <div>
    <Head :title="language.car_groups" />

    <page-header> {{ language.car_groups }} </page-header>

    <Section :title="language.the_groups" class="md:m-7 m-4">
      <div class="grid md:gap-7 gap-4 lg:grid-cols-6 md:grid-cols-4 grid-cols-2 mt-4">
        <Link
          v-for="group in groups"
          :key="group.id"
          :href="route('vehicles') + `?group=${group.id}`"
          as="button"
          class="flex justify-center flex-col items-center bg-gray-600 text-gray-200 rounded py-6 px-4 w-full"
        >
          <h2 class="text-3xl font-bold my-2 flex">
            <span> {{ group.count }} </span>
            <icon name="truck" class="w-6 h-6 mr-1 text-white" />
          </h2>
          <h3 class="text-lg font-semibold">{{ group.name }}</h3>
          <Link class="w-5 h-5" :href="route('vehicles.groups.edit', group.id)"
            ><Icon name="edit"> </Icon
          ></Link>
        </Link>

        <Link
          :href="route('vehicles.groups.create')"
          class="flex justify-center flex-col items-center bg-gray-200 text-gray-400 rounded cursor-pointer py-6 px-4"
        >
          <icon name="plus" class="w-8 h-8 my-2" />
          <h3 class="text-lg font-semibold">{{ language.add_a_group }}</h3>
        </Link>
      </div>
    </Section>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, Link } from "@inertiajs/vue3";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import Section from "@/Components/Section.vue";
import Icon from "@/Components/Icon.vue";

export default {
  components: {
    Head,
    PageHeader,
    Button,
    Section,
    Icon,
    Link,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    groups: Object,
    language: Object,
  },
  data() {
    return {};
  },
  methods: {},
};
</script>
