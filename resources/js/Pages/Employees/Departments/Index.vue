<template>
  <div>
    <Head :title="language.staff_departments" />

    <page-header>
      {{ language.staff_departments }}
    </page-header>

    <Section :title="language.the_groups" class="md:m-7 m-4">
      <div class="grid md:gap-7 gap-4 lg:grid-cols-6 md:grid-cols-4 grid-cols-2 mt-4">
        <Link
          v-for="depart in departs"
          :key="depart.id"
          :href="route('employee.groupes', depart.id)"
          as="button"
          class="flex justify-center flex-col items-center bg-gray-600 text-gray-200 rounded py-6 px-4 w-full"
        >
          <!-- <h2 class="text-3xl font-bold my-2 flex">
                        <span> {{depart.count}} </span>
                        <icon name="users" class="w-5 h-5 mr-1 text-white"/>
                    </h2> -->
          <h3 class="text-lg font-semibold">{{ depart.name }}</h3>
          <Link class="w-5 h-5" :href="route('employee.departments.edit', depart.id)"
            ><Icon name="edit"> </Icon
          ></Link>
        </Link>

        <Link
          :href="route('employee.departments.create')"
          class="flex justify-center flex-col items-center bg-gray-200 text-gray-400 rounded cursor-pointer py-6 px-4"
        >
          <icon name="plus" class="w-8 h-8 my-2" />
          <h3 class="text-lg font-semibold">{{ language.add_a_section }}</h3>
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
    departs: Object,
    groupFor: String,
    language: Object,
  },
  data() {
    return {};
  },
  methods: {
    link(id) {
      if (this.groupFor) return route("operator", { type: this.groupFor, depart: id });
      else return route("employee", { depart: id });
    },
  },
};
</script>
