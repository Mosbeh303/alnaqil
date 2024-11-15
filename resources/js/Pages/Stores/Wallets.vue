<template>
  <Head :title="language.clients_wallets" />

  <div>
    <page-header>
      <span> {{ language.clients_wallets }} </span>
    </page-header>

    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white lg:m-7 m-4 overflow-hidden"
    >
      <flash-messages class="mx-auto" />
      <!-- ***************** -->

      <div class="flex justify-between items-baseline md:flex-row flex-col">
        <div class="flex justify-start gap-3">
          <!-- <h2
            v-if="form.status == 'approved'"
            class="text-gray-700 text-lg mb-4 font-semibold"
          >
            {{ language.activated_clients }}
          </h2>
          <h2
            v-else-if="form.status != 'approved' && form.status != null"
            class="text-gray-700 text-lg mb-4 font-semibold"
          >
            {{ language.the_notActivated_clients }}
          </h2> -->
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.wallets }}
          </h2>

        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
            <select-input
              v-model="form.status"
              :selectPlaceholder="language.client_filtering"
            >
              <option :value="null">{{ language.all_wallets }}</option>
              <option value="empty">{{ language.empty_wallets }}</option>
              <option value="recharged">{{ language.recharged_wallets }}</option>
            </select-input>
          </search-filter>
          <Dropdown v-bind:align="textAlignment" width="48">
            <template #trigger>
              <span class="inline-flex rounded-md">
                <button
                  type="button"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                >
                  <icon name="dots" class="block w-5 h-5 text-gray-500" />
                </button>
              </span>
            </template>

            <template #content>


              <a
                :href="route('stores.wallets.pdf')"
                class="cursor-pointer block w-full px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
              >{{language.download_PDF}} </a>
              <a
                :href="route('stores.wallets.excel')"
                class="cursor-pointer block w-full px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
              >{{language.download_excel_file}} </a>
            </template>
          </Dropdown>
        </div>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th class="pb-4 pt-6 px-6">
                      {{ language.store_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.Store_name }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.postpaid_prepaid }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.delivery_fee }} ({{ language.wallet }})
                    </th>
                    <th class="pb-4 pt-6 px-6">
                        {{ language.paid_to_the_customer }} ({{ language.wallet }})
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.outstanding_balance }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.total_wallet_credit }}</th>

                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="store in stores"
                    :key="store.id"
                    class="hover:bg-gray-100 whitespace-nowrap"
                  >
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="route('stores.show', store)"
                      >
                        {{ store.id }}
                      </Link>
                    </td>
                    <td class="border-t text-center">
                      {{ store.name }}
                    </td>
                    <td class="border-t text-center">
                     <span v-if=" store.postpaid == 1">{{ language.postpaid }}</span>
                     <span v-else>{{ language.prepaid }}</span>
                    </td>
                    <td class="border-t text-center">
                        <p v-html="store.wallet.fees + ' ' + language.riyal"></p>
                    </td>
                    <td class="border-t text-center">
                        <p v-html="store.wallet.paid + ' ' + language.riyal"></p>
                    </td>
                    <td class="border-t text-center">
                        <p>{{ store.wallet.outstandingBalance }} {{ language.riyal }}</p>
                    </td>
                    <td class="border-t text-center">
                        <p>{{ store.wallet.balance }} {{ language.riyal }}</p>
                    </td>
                  </tr>
                  <tr v-if="stores.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.no_clients }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>

    <Modal :show="showAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.are_you_sure }}
      </h2>
      <p v-if="targetOperatorStatus" class="text-base text-gray-600">
        {{ language.target_operator_status }}
      </p>
      <p v-else class="text-base text-gray-600">
        {{ language.do_you_want_to_the_delegate_account }}
      </p>
      <Button
        class="mt-4 !border"
        :href="alertLink"
        method="post"
        @click="showAlert = false"
      >
        <span v-if="targetOperatorStatus">{{ language.stop }}</span>
        <span v-else>{{ language.allow }}</span>
      </Button>
      <Button
        @click="showAlert = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </Modal>
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
import Dropdown from "@/Components/Dropdown.vue";

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
    Dropdown
  },

  layout: BreezeAuthenticatedLayout,

  props: {
    filters: Object,
    stores: Object,
    language: Object,
  },

  data() {
    return {
      form: {
        search: this.filters.search,
        status: this.filters.status,
      },
      showAlert: false,
      alertLink: "",
      targetOperatorStatus: 0,
    };
  },

  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/stores/wallets", pickBy(this.form), {
          preserveState: true,
          preserveScroll: true,
        });
      }, 150),
    },
  },

  created() {
    if (this.$page.props.locale == "ar") {
      this.textAlignment = "left";
    }
    if (this.$page.props.locale == "en") {
      this.textAlignment = "right";
    }
  },

  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
    changeStoreStatus(store, event) {
      this.$inertia.post(
        `/dashboard/stores/${store.id}/update-status`,
        pickBy(this.form),
        {
          preserveScroll: true,
        }
      );
      // this.showAlert = true;
      console.log("done!");
    },
    approved(status) {
      if (status == "approved") return true;
      else return false;
    },
  },
};
</script>
