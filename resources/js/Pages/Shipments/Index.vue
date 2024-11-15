<template>
  <Head :title="language.shipments_details" />
  <div>
    <page-header
      :button="{
        text: language.add_shipment,
        href: route('shipments.create'),
      }"
    >
      {{ language.shipments_details }}
    </page-header>

    <div
      class="p-3 m-4 overflow-hidden bg-white border border-gray-200 rounded sm:p-5 md:m-7"
    >
      <!-- ***************** -->

      <div class="items-baseline justify-between md:flex">
        <div class="flex justify-start gap-3">
          <h2 v-if="form.status == null" class="mb-4 text-lg font-semibold text-gray-700">
            {{ language.all_shipments }}
          </h2>
          <h2
            v-else-if="form.status == '10'"
            class="mb-4 text-lg font-semibold text-gray-700"
          >
            {{ language.Shipments_created }}
          </h2>
          <h2
            v-else-if="form.status == '20'"
            class="mb-4 text-lg font-semibold text-gray-700"
          >
            {{ language.Shipments_received }}
          </h2>
          <h2
            v-else-if="form.status == '35'"
            class="mb-4 text-lg font-semibold text-gray-700"
          >
            {{ language.Shipments_are_being_processed }}
          </h2>
          <h2
            v-else-if="form.status == '65'"
            class="mb-4 text-lg font-semibold text-gray-700"
          >
            {{ language.Shipments_are_out_for_delivery }}
          </h2>
          <h2
            v-else-if="form.status == '100'"
            class="mb-4 text-lg font-semibold text-gray-700"
          >
            {{ language.Shipments_have_been_delivered }}
          </h2>
          <h2
            v-else-if="form.status == '-100'"
            class="mb-4 text-lg font-semibold text-gray-700"
          >
            {{ language.the_returned_shipments }}
          </h2>
          <h2
            v-else-if="form.status == '-1'"
            class="mb-4 text-lg font-semibold text-gray-700"
          >
            {{ language.canceled_shipments }}
          </h2>
          <span class="flex px-3 font-bold text-gray-600">
            <Icon name="shipment" class="relative w-5 h-5 ml-1 text-gray-500 bottom-1" />
            {{ shipments.total }}
          </span>
        </div>

        <div class="flex flex-row items-center justify-between gap-4 mb-6">
          <search-filter v-model="form.search" class="w-full max-w-md" @reset="reset">
            <select-input
              v-if="
                form.status == 10 ||
                form.status == 20 ||
                form.status == 35 ||
                form.status == 65 ||
                form.status == null
              "
              v-model="form.status"
              :selectPlaceholder="language.shipment_filtering"
            >
              <option :value="null">{{ language.all_shipments }}</option>
              <option value="10">{{ language.created }}</option>
              <option value="20">{{ language.received }}</option>
              <option value="35">{{ language.processing_in_progress }}</option>
              <option value="65">{{ language.out_to_deliver }}</option>
            </select-input>
          </search-filter>
          <Dropdown v-bind:align="textAlignment" width="48">
            <template #trigger>
              <span class="inline-flex rounded-md">
                <button
                  type="button"
                  class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none"
                >
                  <icon name="dots" class="block w-5 h-5 text-gray-500" />
                </button>
              </span>
            </template>

            <template #content>


              <input
                name="policies"
                form="formSelect"
                type="submit"
                class="block w-full px-4 py-2 text-sm leading-5 text-gray-700 transition duration-150 ease-in-out cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                :value="language.policy_extraction"
              />
              <input
                v-if="form.status == '100' || form.status == '-100'"
                name="invoices"
                form="formSelect"
                type="submit"
                class="block w-full px-4 py-2 text-sm leading-5 text-gray-700 transition duration-150 ease-in-out cursor-pointer hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                :value="language.extract_invoice"
              />
            </template>
          </Dropdown>
        </div>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <form :action="route('shipments.file')" method="post" id="formSelect">
                <input type="hidden" name="_token" :value="$page.props.csrf" />
                <table class="min-w-full">
                  <thead class="text-sm bg-gray-100 border-b">
                    <tr class="whitespace-nowrap">
                      <th>
                        <Checkbox v-model:checked="selectedAll" @click="selectAll()" />
                      </th>
                      <th class="px-6 pt-6 pb-4">{{ language.shipment_number }}</th>
                      <th
                        class="px-6 pt-6 pb-4"
                        v-if="
                          (form.status == 100 || form.status == -100 || form.status == -1)
                          && $page.props.auth.account != 'client'
                        "
                      >
                        {{ language.bill_number }}
                      </th>
                      <th class="px-6 pt-6 pb-4">{{ language.create }}</th>
                      <th class="px-6 pt-6 pb-4">{{ language.shipment_type }}</th>
                      <th
                        class="px-6 pt-6 pb-4"
                        v-if="form.status == 100 || form.status == -100"
                      >
                        {{ language.delivery }}
                      </th>
                      <th
                        class="px-6 pt-6 pb-4"
                        v-if="
                          $page.props.auth.permissions.includes('fee_shipments') ||
                          $page.props.auth.account != 'employee'
                        "
                      >
                        {{ language.delivery_fee }}
                      </th>
                      <th class="px-6 pt-6 pb-4">
                        {{ language.paiement_when_recieving }}
                      </th>
                      <th class="px-6 pt-6 pb-4">
                        {{ language.phone_number }}
                      </th>
                      <th class="px-6 pt-6 pb-4">
                        <span v-if="$page.props.auth.account == 'client'">
                          {{ language.The_recipients_name }}</span
                        >
                        <span v-else>{{ language.sender_name }}</span>
                      </th>
                      <th class="px-6 pt-6 pb-4">
                        {{ language.city_neighborhood }}
                      </th>
                      <th class="px-6 pt-6 pb-4">
                        {{ language.street_name }}
                      </th>
                      <th
                        class="px-6 pt-6 pb-4"
                        v-if="
                          $page.props.auth.account == 'admin' ||
                          $page.props.auth.account == 'admin'
                        "
                      >
                        {{ language.branch }}
                      </th>
                      <th class="px-6 pt-6 pb-4" v-if="form.status == null">
                        {{ language.Status }}
                      </th>

                      <th class="px-6 pt-6 pb-4">
                        {{ language.order_number }}
                      </th>

                      <th class="px-6 pt-6 pb-4">
                        {{ language.package_contents }}
                      </th>

                      <th class="px-6 pt-6 pb-4">
                        {{ language.print }}
                      </th>

                      <th class="px-6 pt-6 pb-4">
                        {{ language.the_delegate }}
                      </th>

                      <th class="px-6 pt-6 pb-4">
                        {{ language.notes }}
                      </th>

                      <th
                        class="px-6 pt-6 pb-4"
                        v-if="$page.props.auth.account == 'admin'"
                      >
                        {{ language.storage_place }}
                      </th>
                      <th
                        class="px-6 pt-6 pb-4"
                        v-if="
                          $page.props.auth.account == 'admin' ||
                          $page.props.auth.permissions.includes('edit_shipments')
                        "
                      ></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="shipment in shipments.data"
                      :key="shipment.id"
                      class="text-center hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
                    >
                      <td class="px-2 py-4 border-t">
                        <Checkbox
                          name="shipments[]"
                          v-model:checked="selectedShipments"
                          :value="shipment.id"
                          @change="updateCheckall"
                        />
                      </td>
                      <td class="border-t">
                        <Link
                          class="flex items-center px-6 py-4 focus:text-indigo-500"
                          :href="route('shipments.show', shipment.id)"
                        >
                          {{ shipment.number }}
                        </Link>
                      </td>
                      <td
                        class="border-t"
                        v-if="
                          (form.status == 100 || form.status == -100 || form.status == -1)
                          && $page.props.auth.account != 'client'
                        "
                      >
                        <a
                          target="_blank"
                          class="flex items-center px-6 py-4 focus:text-indigo-500"
                          :href="route('shipments.invoice.pdf', shipment.number)"
                        >
                          SA{{ shipment.invoice }}
                        </a>
                      </td>
                      <td class="px-6 border-t">
                        {{ shipment.created_at }}
                      </td>
                      <td class="px-6 border-t">
                        <span v-if="shipment.return_back == 1">{{ language.redux }}</span>
                        <span v-else-if="shipment.refrigerated == 1">{{
                          language.refrigerated_transport
                        }}</span>
                        <span v-else-if="shipment.exchange == 1">{{
                          language.replacing
                        }}</span>
                        <span v-else>{{ language.normal }}</span>
                      </td>
                      <td
                        class="px-6 border-t"
                        v-if="form.status == 100 || form.status == -100"
                      >
                        {{ shipment.shipping_date }}
                      </td>
                      <td
                        class="px-6 border-t"
                        v-if="
                          $page.props.auth.permissions.includes('fee_shipments') ||
                          $page.props.auth.account != 'employee'
                        "
                      >
                        {{ shipment.fees }}
                      </td>
                      <td class="px-6 border-t">
                        <span v-if="!shipment.cod">{{ language.pre_paid }}</span>
                        <span v-else>{{ shipment.cod }}</span>
                      </td>
                      <td class="px-6 border-t">
                        {{ shipment.receiverPhone }}
                      </td>
                      <td class="px-6 border-t">
                        <span v-if="$page.props.auth.account == 'client'">
                          {{ shipment.receiverName }}
                        </span>
                        <span v-else>{{ shipment.storeName }}</span>
                      </td>
                      <td class="px-6 border-t">
                        {{ shipment.city }} ({{ shipment.neighborhood }})
                      </td>
                      <td class="px-6 border-t">
                        {{ shipment.street }}
                      </td>
                      <td
                        class="border-t"
                        v-if="
                          $page.props.auth.account == 'admin' ||
                          $page.props.auth.account == 'admin'
                        "
                      >
                        <span v-if="shipment.branchForOperator">{{
                          shipment.branchForOperator.name
                        }}</span>
                        <span v-else-if="shipment.branchForEmployee">{{
                          shipment.branchForEmployee.name
                        }}</span>
                      </td>
                      <td
                        class="px-6 border-t whitespace-nowrap"
                        v-if="form.status == null"
                      >
                        <p
                          class="px-4 py-1 text-xs text-gray-700 bg-gray-200 rounded-xl w-fit"
                          v-if="shipment.status == 10"
                        >
                          {{ language.created }}
                        </p>
                        <p
                          class="px-4 py-1 text-xs text-orange-700 bg-orange-200 rounded-xl w-fit"
                          v-else-if="shipment.status == 20"
                        >
                          {{ language.received }}
                        </p>
                        <p
                          class="px-4 py-1 text-xs text-blue-700 bg-blue-200 rounded-xl w-fit"
                          v-else-if="shipment.status == 35"
                        >
                          {{ language.processing_in_progress }}
                        </p>
                        <p
                          class="px-4 py-1 text-xs text-green-700 bg-green-200 rounded-xl w-fit"
                          v-else-if="shipment.status == 65"
                        >
                          {{ language.out_to_deliver }}
                        </p>
                      </td>

                      <td class="border-t">
                        {{ shipment.order_id }}
                      </td>

                      <td class="border-t">
                        {{ shipment.details }}
                      </td>

                      <td class="border-t">
                        <Link rel="stylesheet" :href="route('shipments.prints', shipment)"
                          >{{ shipment.prints }}
                        </Link>
                      </td>

                      <td class="border-t">
                        {{ shipment.operator }}
                      </td>

                      <td class="border-t">
                        {{ shipment.notice }}
                      </td>
                      <td
                        class="text-center border-t"
                        v-if="$page.props.auth.account == 'admin'"
                      >
                        {{ shipment.warehouseLocation }}
                      </td>
                      <td
                        class="w-px border-t"
                        v-if="
                          $page.props.auth.account == 'admin' ||
                          $page.props.auth.permissions.includes('edit_shipments')
                        "
                      >
                        <Link
                          class="flex items-center px-6 py-4 focus:text-indigo-500"
                          :href="route('shipments.edit', shipment.id)"
                        >
                          <icon name="edit" class="block w-5 h-5 text-gray-500" />
                        </Link>
                      </td>
                    </tr>
                    <tr v-if="shipments.data.length === 0">
                      <td class="px-6 py-4 border-t" colspan="4">
                        {{ language.no_shipments }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </form>
            </div>
          </div>
        </div>
      </div>
      <pagination class="mt-6" :links="shipments.links" />
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
    Checkbox,
    Dropdown,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    filters: Object,
    shipments: Object,
    language: Object,
  },
  created() {
    if (this.$page.props.locale == "ar") {
      this.textAlignment = "left";
    }
    if (this.$page.props.locale == "en") {
      this.textAlignment = "right";
    }
  },

  data() {
    return {
      textAlignment: "",
      form: {
        search: this.filters.search,
        status: this.filters.status,
      },
      selectedAll: false,
      selectedShipments: [],
    };
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/shipments", pickBy(this.form), {
          preserveState: true,
          preserveScroll: true,
        });
      }, 150),
    },
  },
  computed: {
    csrf() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    },
  },
  methods: {

    reset() {
      this.form = mapValues(this.form, () => null);
    },

    selectAll: function () {
      this.selectedAll = !this.selectedAll;
      this.selectedShipments = [];
      if (this.selectedAll) {
        // Check all
        for (let shipment of this.shipments.data) {
          this.selectedShipments.push(shipment.id);
        }
      }
    },
    updateCheckall: function () {
      if (this.selectedShipments.length == this.shipments.data.length) {
        this.selectedAll = true;
      } else {
        this.selectedAll = false;
      }
    },
  },
};
</script>
