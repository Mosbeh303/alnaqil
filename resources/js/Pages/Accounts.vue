<template>
  <div>
    <Head :title="language.accounts" />

    <page-header
    :button="{
        text: language.add_folder,
        href: route('accounts.folders.create'),
      }"
    >
      {{ language.accounts }}
    </page-header>

    <flash-messages />

    <Section :title="language.folders" class="md:m-7 m-4">
      <div class="grid md:gap-2 gap-4 lg:grid-cols-6 md:grid-cols-4 grid-cols-2 mt-4">
        <Link
          v-if="
            this.$page.props.auth.account == 'admin' ||
            this.$page.props.auth.permissions.includes('wallets_accounts')
          "
          :href="route('wallets')"
          as="button"
          class="flex justify-center flex-col items-center  text-gray-600 rounded py-6 px-4 w-full"
        >
          <Icon name="folder" class="text-gray-600 h-36 w-36"></Icon>
          <h3 class="text-base font-semibold">{{ language.banks }}</h3>
        </Link>

        <Link
          v-if="
            this.$page.props.auth.account == 'admin' ||
            this.$page.props.auth.permissions.includes('receipt_employees_dues_accounts')
          "
          :href="route('reimbursements')"
          as="button"
          class="flex justify-center flex-col items-center  text-gray-600 rounded py-6 px-4 w-full"
        >
            <Icon name="folder" class="text-gray-600 h-36 w-36"></Icon>
            <h3 class="text-base font-semibold">{{ language.receipts_from_employees }}</h3>
        </Link>


        <Link
            v-for="folder in folders" :key="folder.id"
             :href="route('accounts.folders.show', folder)"
             as="button"
             class="flex justify-center flex-col items-center  text-gray-600 rounded py-6 px-4 w-full relative"
            >
            <span class="absolute top-30 text-2xl font-bold text-white" style="top:110px">{{ folder.code }}</span>
            <Icon name="folder" class="text-gray-600 h-36 w-36"></Icon>
            <h3 class="text-base font-semibold">{{ folder.name }}</h3>
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
import FlashMessages from "@/Components/FlashMessages.vue";


export default {
  components: {
    Head,
    PageHeader,
    Button,
    Section,
    Icon,
    Link,
    FlashMessages
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    language: Object,
    folders: Object
  },
};
</script>
