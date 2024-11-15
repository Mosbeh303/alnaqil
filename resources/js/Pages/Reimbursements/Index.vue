<template>
  <div>
    <Head :title="language.Receipts_from_delegates" />

    <page-header>
      {{ language.Receipts_from_delegates }}
    </page-header>

    <Section :title="language.the_groups" class="md:m-7 m-4">
      <div class="grid md:gap-7 gap-4 lg:grid-cols-6 md:grid-cols-4 grid-cols-2 mt-4">
        <Link
          v-for="groupe in groupes"
          :key="groupe.id"
          :href="route('reimbursements.groupe', groupe)"
          as="button"
          class="flex justify-center flex-col items-center bg-gray-600 text-gray-200 rounded py-6 px-4 w-full"
        >
          <h2 class="text-3xl font-bold my-2 flex">
            <span> {{ groupe.count }} </span>
            <icon name="users" class="w-5 h-5 mr-1 text-white" />
          </h2>
          <h3 class="text-lg font-semibold">{{ groupe.name }}</h3>
          <Link
            class="w-5 h-5"
            :href="route('groupes.edit', groupe.id) + `?for=${groupe.for}`"
            ><Icon name="edit"> </Icon
          ></Link>
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
    groupes: Object,
    language: Object,
  },
  data() {
    return {};
  },
  methods: {},
};
</script>
