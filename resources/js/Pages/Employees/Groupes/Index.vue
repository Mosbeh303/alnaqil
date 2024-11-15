<template>
  <div>
    <Head :title="language.section" />

    <page-header>
      {{ language.groups }}
      <span v-if="depart">{{ language.section }} {{ depart.name }}</span>
      <span v-else-if="groupFor === 'rec'">{{ language.receipts }}</span>
      <span v-else-if="groupFor === 'del'">{{ language.delivery_handlers }}</span>
      <span v-else-if="groupFor === 'vip'">{{ language.distinguished_delegates }}</span>

      <template v-if="depart" v-slot:breadcrumb>
        <Link href="/dashboard">{{ language.dashboard }}</Link>
        <span aria-hidden="true" class="mx-2">&gt;</span>
        <Link :href="route('employee.departments')"> {{ language.sections }}</Link>
        <span aria-hidden="true" class="mx-2">&gt;</span>
        <Link :href="route('employee.groupes', depart.id)">
          {{ language.section }} {{ depart.name }}</Link
        >
      </template>
    </page-header>

    <Section :title="language.the_groups" class="md:m-7 m-4">
      <div class="grid md:gap-7 gap-4 lg:grid-cols-6 md:grid-cols-4 grid-cols-2 mt-4">
        <Link
          v-for="groupe in groupes"
          :key="groupe.id"
          :href="link(groupe.id)"
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

        <Link
          :href="
            route('groupes.create', { for: groupFor, depart: depart ? depart.id : null })
          "
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
    groupes: Object,
    groupFor: String,
    depart: Object,
    language: Object,
  },
  data() {
    return {};
  },
  methods: {
    link(id) {
      if (this.groupFor) return route("operator", { type: this.groupFor, groupe: id });
      else return route("employee", { groupe: id });
    },
  },
};
</script>
