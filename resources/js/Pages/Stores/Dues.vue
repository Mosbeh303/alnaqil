<template>
  <Head :title="language.store_receivables" />

  <div>
    <page-header>
      <span> {{ language.receivables }} </span>
    </page-header>

    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white lg:m-7 m-4 overflow-hidden"
    >
      <flash-messages class="mx-auto w-full" />
      <!-- ***************** -->

      <div class="flex justify-between items-baseline lg:flex-row flex-col">
        <div class="flex justify-start gap-3 lg:w-1/4 shring:0 w-full">
          <h2 v-if="form.status == '<'" class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.customer_receivables }}
          </h2>
          <h2
            v-else-if="form.status == '>'"
            class="text-gray-700 text-lg mb-4 font-semibold"
          >
            {{ language.receivables_from_the_client }}
          </h2>
          <h2
            v-else-if="form.status == '!0'"
            class="text-gray-700 text-lg mb-4 font-semibold"
          >
            {{ language.All_ccruals_diff_0 }}
          </h2>
          <h2 v-else class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.all_receivables }}
          </h2>
          <!-- <span class="flex px-3 font-bold text-gray-600"><Icon name="users" class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"  /> {{Operators.total}} </span>-->
        </div>

        <div
          class="flex items-center mb-6 lg:flex-row flex-col gap-4 lg:w-3/4 w-full shring:0 overflow-hidden px-2"
        >
          <form
            :action="route('stores.dues.excel')"
            method="get"
            class="inline-block"
            id="form"
          >
            <input type="hidden" name="_token" :value="$page.props.csrf" />
            <input type="hidden" v-model="form.search" name="search" />
            <input type="hidden" v-model="form.month" name="month" />
            <input type="hidden" v-model="form.year" name="year" />
            <input type="hidden" v-model="form.status" name="status" />
            <Button class="!bg-gray-100 text-sm !text-gray-500 !py-2 inline-block ml-2">
              Excel
            </Button>
          </form>

          <form
            :action="route('stores.dues.pdf')"
            method="get"
            class="inline-block"
            id="form"
          >
            <input type="hidden" name="_token" :value="$page.props.csrf" />
            <input type="hidden" v-model="form.search" name="search" />
            <input type="hidden" v-model="form.month" name="month" />
            <input type="hidden" v-model="form.year" name="year" />
            <input type="hidden" v-model="form.status" name="status" />
            <Button class="!bg-gray-100 text-sm !text-gray-500 !py-2 inline-block ml-2">
              PDF
            </Button>
          </form>

          <div class="shrink-0 lg:w-1/5 w-full">
            <select-input class="grow" v-model="form.year">
              <option value="null" disabled>{{ language.Year }}</option>
              <option value="2022">2022</option>
              <option value="2023">2023</option>
            </select-input>
          </div>
          <div class="shrink-0 lg:w-1/5 w-fit">
            <select-input class="ml-8" v-model="form.month">
              <option value="null" disabled>{{ language.Mounth }}</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
            </select-input>
          </div>

          <div class="shrink-0 lg:w-1/5 w-full">
            <select-input
              class="ml-2"
              v-model="form.status"
              :selectPlaceholder="language.receivables_filtering"
            >
              <option :value="null">{{ language.all_receivables }}</option>
              <option value="!0">{{ language.All_ccruals_diff_0 }}</option>
              <option value="<">{{ language.customer_receivables }}</option>
              <option value=">">{{ language.receivables_from_the_client }}</option>
            </select-input>
          </div>

          <SearchInput
            @reset="reset"
            type="text"
            class="text-sm lg:w-1/5 bg-gray-100 border-0 outline-none lg:mr-2 lg:ml-4 mt-4 lg:mt-0 w-full"
            v-model="form.search"
            :placeholder="language.searsh_placeholder"
            autocomplete="off"
            name="search"
          />
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
                    <th class="pb-4 pt-6 px-6">{{ language.bank }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.total_delivery_fee }}</th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.total_payment_upon_receipt }}
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.cash }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.network }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.promissory_bills }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.exchange_bills }}</th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.receivables }}
                    </th>
                    <th class="pb-4 pt-6 px-6"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="store in computedStores"
                    :key="store.id"
                    class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
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
                      {{ store.bank }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.fees }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.cod }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.cash }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.epayment }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.receipt }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.payment }}
                    </td>
                    <td
                      class="border-t text-center font-semibold"
                      :class="{
                        'text-red-600':
                          store.previousDues -
                            (store.cod - store.fees - store.payment - store.receipt) >
                          0,
                        'text-green-600':
                          store.previousDues -
                            (store.cod - store.fees - store.payment - store.receipt) <
                          0,
                      }"
                    >
                      {{
                        (
                          store.previousDues -
                          (store.cod - store.fees - store.payment - store.receipt)
                        ).toFixed(2)
                      }}
                    </td>

                    <td class="w-px border-t px-4">
                      <div class="flex justify-center">
                        <a
                          :href="`https://api.whatsapp.com/send?phone=${store.phone.replace(
                            '0',
                            '+966'
                          )}&text=${whatsapp(store)}`"
                        >
                          <Button
                            class="mx-auto justify-center bg-green-600 ml-0 text-xs flex-no-wrap !rounded-xl !py-2 !px-4"
                          >
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              class="ml-2 w-4 h-4"
                              xmlns:xlink="http://www.w3.org/1999/xlink"
                              aria-hidden="true"
                              role="img"
                              width="1em"
                              height="1em"
                              preserveAspectRatio="xMidYMid meet"
                              viewBox="0 0 24 24"
                            >
                              <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967c-.273-.099-.471-.148-.67.15c-.197.297-.767.966-.94 1.164c-.173.199-.347.223-.644.075c-.297-.15-1.255-.463-2.39-1.475c-.883-.788-1.48-1.761-1.653-2.059c-.173-.297-.018-.458.13-.606c.134-.133.298-.347.446-.52c.149-.174.198-.298.298-.497c.099-.198.05-.371-.025-.52c-.075-.149-.669-1.612-.916-2.207c-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372c-.272.297-1.04 1.016-1.04 2.479c0 1.462 1.065 2.875 1.213 3.074c.149.198 2.096 3.2 5.077 4.487c.709.306 1.262.489 1.694.625c.712.227 1.36.195 1.871.118c.571-.085 1.758-.719 2.006-1.413c.248-.694.248-1.289.173-1.413c-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214l-3.741.982l.998-3.648l-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884c2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"
                                fill="currentColor"
                              />
                            </svg>
                            <span>{{ language.whatsapp }} </span>
                          </Button>
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="stores.data.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.There_are_no_receivables }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <pagination class="mt-6" :links="stores.links" />
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
import Input from "@/Components/Input.vue";
import Pagination from "@/Components/Pagination.vue";
import Icon from "@/Components/Icon.vue";
import Modal from "@/Components/Modal.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Dropdown from "@/Components/Dropdown.vue";
import SearchInput from "@/Components/Input";

export default {
  components: {
    Head,
    PageHeader,
    SearchFilter,
    Button,
    Link,
    SelectInput,
    Input,
    Pagination,
    Icon,
    Modal,
    FlashMessages,
    Dropdown,
    SearchInput,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    filters: Object,
    stores: Object,
    whatsappMessage: String,
    language: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        status: this.filters.status,
        year: this.filters.year,
        month: this.filters.month,
      },
      showAlert: false,
      alertLink: "",
      targetOperatorStatus: 0,
      status: this.filters.status,
    };
  },

  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/stores/dues", pickBy(this.form), {
          preserveState: true,
          preserveScroll: true,
        });
      }, 150),
    },
  },

  computed: {
    computedStores: function () {
      let stores = {};
      if (this.form.status == "<") {
        stores = this.stores.data.filter(
          (store) =>
            store.previousDues -
              (store.cod - store.fees - store.payment - store.receipt) <
            0
        );
      } else if (this.form.status == ">") {
        stores = this.stores.data.filter(
          (store) =>
            store.previousDues -
              (store.cod - store.fees - store.payment - store.receipt) >
            0
        );
      } else if (this.form.status == "!0") {
        stores = this.stores.data.filter(
          (store) =>
            store.previousDues -
              (store.cod - store.fees - store.payment - store.receipt) !=
            0
        );
      } else {
        stores = this.stores.data.filter(
          (store) =>
            (store.previousDues != 0 ||
              store.cod != 0 ||
              store.fees != 0 ||
              store.payment != 0 ||
              store.receipt) != 0
        );
      }
      return stores;
    },
  },

  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },

    whatsapp(store) {
      let msg = this.whatsappMessage.replace("{store}", store.name);
      if (this.whatsappMessage) {
        msg = msg.replace("{owner}", store.ownerName);
        msg = msg.replace("{dues}", Math.abs(store.dues));
      } else msg = "السلام عليكم";

      return msg;
    },
  },
};
</script>
