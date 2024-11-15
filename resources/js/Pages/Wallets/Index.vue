<template>
  <Head :title="language.banks" />

  <div>
    <page-header
      :button="{
        text: language.add_a_bank,
        href: route('wallets.create'),
        enable: this.$page.props.auth.account == 'admin',
      }"
    >
      <span> {{ language.banks }} </span>
    </page-header>

    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white lg:m-7 m-4 overflow-hidden"
    >
      <flash-messages class="mx-auto w-full" />
      <!-- ***************** -->

      <div class="flex justify-between items-baseline md:flex-row flex-col">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.banks }}
          </h2>

          <span class="flex px-3 font-bold text-gray-600">
            {{ wallets.total }}
          </span>
        </div>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th class="pb-4 pt-6 px-6">#</th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.bank }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.balance }}
                    </th>

                    <th class="pb-4 pt-6 px-6"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(wallet, i) in wallets"
                    :key="i"
                    class="hover:bg-gray-100 whitespace-nowrap"
                  >
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="route('wallets.show', wallet)"
                      >
                        {{ ++i }}
                      </Link>
                    </td>
                    <td class="border-t text-center">
                      {{ wallet.name }}
                    </td>
                    <td class="border-t text-center">
                      {{ wallet.balance }}
                    </td>

                    <td class="border-t">
                      <div class="flex justify-center">
                        <Link
                          class="flex justify-center pr-2 pl-4"
                          :href="route('wallets.show', wallet)"
                          tabindex="-1"
                        >
                          <icon name="eye" class="block w-5 h-5 text-gray-500" />
                        </Link>

                        <Link
                          v-if="this.$page.props.auth.account == 'admin'"
                          class="flex justify-center pr-2 pl-4"
                          :href="route('wallets.edit', wallet)"
                          tabindex="-1"
                        >
                          <icon name="edit" class="block w-5 h-5 text-gray-500" />
                        </Link>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="wallets.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.no_banks }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import SearchFilter from "@/Components/SearchFilter.vue";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import SelectInput from "@/Components/SelectInput.vue";
import Pagination from "@/Components/Pagination.vue";
import Icon from "@/Components/Icon.vue";
import Modal from "@/Components/Modal.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Toggle from "@/Components/Toggle.vue";

export default {
  components: {
    Head,
    PageHeader,
    SearchFilter,
    Button,
    Link,
    SelectInput,
    Pagination,
    Icon,
    Modal,
    FlashMessages,
    Toggle,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    filters: Object,
    wallets: Object,
    language: Object,
  },
  data() {
    return {};
  },
};
</script>
