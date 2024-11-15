<template>
  <Head :title="language.receiving_shipments" />

  <div>
    <page-header
      :button="{
        text: language.add_shipment,
        href: route('shipments.create'),
      }"
    >
      {{ language.Receipt_of_shipments_from_the_platform }}
    </page-header>

    <flash-messages class="m-7" />

    <div
      class="p-3 m-4 overflow-hidden bg-white border border-gray-200 rounded sm:p-5 md:m-7"
    >
      <!-- ***************** -->

      <div class="flex flex-col items-baseline justify-between md:flex-row">
        <div class="flex justify-start gap-3">
          <h2 class="mb-4 text-lg font-semibold text-gray-700">
            {{ language.shops }}
          </h2>
        </div>

        <div class="flex items-center justify-between w-full gap-2 mb-4 md:w-fit">
          <a
          class="!bg-gray-100 text-sm !text-gray-500 !py-2 w-full text-center max-w-md items-center whitespace-nowrap px-4 border border-transparent rounded-md font-semibold uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
            :href="route('shipments.receive.pdf')"
          >
            {{ language.download_PDF }}
          </a>

          <search-filter v-model="form.search" class="w-full max-w-md" @reset="reset">
          </search-filter>

          <div class="relative ml-3">

          </div>
        </div>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="text-sm bg-gray-100 border-b">
                  <tr class="whitespace-nowrap">

                    <th class="px-6 pt-6 pb-4">{{ language.Store_name }}</th>
                    <th class="px-6 pt-6 pb-4">{{ language.platform }}</th>
                    <th class="px-6 pt-6 pb-4">{{ language.city }}</th>
                    <th class="px-6 pt-6 pb-4">{{ language.Neighborhood }}</th>
                    <th class="px-6 pt-6 pb-4">{{ language.phone_number }}</th>
                    <th
                      class="px-6 pt-6 pb-4"
                      v-if="
                        $page.props.auth.account == 'admin' ||
                        $page.props.auth.account == 'employee'
                      "
                    >
                      {{ language.Receiving_representative }}
                    </th>
                    <th class="px-6 pt-6 pb-4">{{ language.number_of_shipments }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(store, index) in stores"
                    :key="store.id"
                    class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
                  >


                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="route('stores.shipments', { store: store, status: 10 })"
                      >
                        {{ store.name }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="route('stores.shipments', { store: store, status: 10 })"
                      >
                        {{ store.platformName }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="'#'"
                      >
                        {{ store.city }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="'#'"
                      >
                        {{ store.neighborhood }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="'#'"
                      >
                        {{ store.phone }}
                      </Link>
                    </td>
                    <td
                      class="border-t"
                      v-if="
                        $page.props.auth.account == 'admin' ||
                        $page.props.auth.account == 'employee'
                      "
                    >
                      <div
                        v-show="!store.showForm"
                        class="flex items-center justify-center"
                      >
                        <Link class="px-6 py-4 focus:text-indigo-500" :href="'#'">
                          {{ store.operator }}
                        </Link>
                        <icon
                          name="edit"
                          class="block w-4 h-4 text-gray-500 cursor-pointer"
                          @click="switchUpdateOperatorForm(index)"
                        />
                      </div>

                      <div v-show="store.showForm" class="relative w-full">
                        <form
                          @submit.prevent="submit(store, index)"
                          class="relative w-11/12 my-4"
                        >
                          <Input
                            id="shipment"
                            type="text"
                            v-model="formUpdateOperator.value"
                            :error="formUpdateOperator.errors.value"
                            class="block w-full mt-4"
                            required
                            :placeholder="language.id_number_or_delegates_mobile_number"
                            autocomplete="shipment"
                          />
                          <InputError :message="formUpdateOperator.errors.value" />

                          <button
                            :class="{ 'opacity-25': formUpdateOperator.processing }"
                            :disabled="formUpdateOperator.processing"
                            class="absolute top-0 left-0 px-6 mt-4 text-gray-500"
                          >
                            ⏎
                          </button>
                        </form>
                        <p
                          name="delete"
                          class="absolute left-0 w-4 h-4 text-gray-500 cursor-pointer top-3"
                          @click="switchUpdateOperatorForm(index)"
                        >
                          X
                        </p>
                      </div>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="'#'"
                      >
                        {{ store.shipments }}
                      </Link>
                    </td>
                  </tr>
                  <tr v-if="stores.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.no_shipments }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- <pagination class="mt-6" :links="stores.links" /> -->
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
import Checkbox from "@/Components/Checkbox.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Dropdown from "@/Components/Dropdown.vue";
import FlashMessages from "@/Components/FlashMessages.vue";

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
    Checkbox,
    Dropdown,
    Input,
    InputError,
    FlashMessages,
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
        city: "",
      },
      formUpdateOperator: this.$inertia.form({
        value: "",
      }),
    };
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/shipments/receive", pickBy(this.form), {
          preserveState: true,
          preserveScroll: true,
        });
      }, 150),
    },
  },

  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
    submit(store, index) {
      this.formUpdateOperator.post(this.route("stores.setoperator", {'store':store,'address': store.platformId}), {
        preserveScroll: true,
        onSuccess: () => {
          this.formUpdateOperator.reset();
          this.stores[index].showForm = false;
        },
      });
    },
    switchUpdateOperatorForm(index) {
      this.stores[index].showForm = !this.stores[index].showForm;
    },
  },
};
</script>
