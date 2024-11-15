<template>
  <Head v-if="operator" :title="language.delegate_shipments_details" />
  <Head v-if="store" :title="language.store_shipments" />
  <Head v-if="receiver" :title="language.receiver_shipments" />

  <div>
    <page-header>
      <span v-if="operator">
        {{ language.Details_of_todays_shipments_to_the_delegate }} ({{ operator.name }})
      </span>
      <span v-if="receiver">
        {{ language.receiver_shipments }} ({{ receiver.name }})
      </span>
      <span v-if="store"> {{ language.store_shipments }} ({{ store.name }}) </span>
    </page-header>

    <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white m-7 overflow-hidden">
      <!-- ***************** -->

      <div class="flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
          <h2 v-if="operator" class="text-gray-700 text-lg mb-4 font-semibold">
            <span v-if="form.method == 'prepaid'"> {{ language.prepaid_shipments }}</span>
            <span v-if="form.method == 'cash'">{{
              language.payment_shipments_upon_receipt_cash
            }}</span>
            <span v-if="form.method == 'epayment'">{{
              language.payment_on_receipt_shipments_network
            }}</span>
          </h2>

          <h2 v-if="store || receiver" class="text-gray-700 text-lg mb-4 font-semibold">
            <span v-if="form.status == null">
              {{ language.all_shipments }}
            </span>
            <span v-else-if="form.status == '10'">
              {{ language.Shipments_created }}
            </span>
            <span v-else-if="form.status == '20'">
              {{ language.Shipments_received }}
            </span>
            <span v-else-if="form.status == '35'">
              {{ language.Shipments_are_being_processed }}
            </span>
            <span v-else-if="form.status == '65'">
              {{ language.Shipments_are_out_for_delivery }}
            </span>
            <span v-else-if="form.status == '100'">
              {{ language.Shipments_have_been_delivered }}
            </span>
            <span v-else-if="form.status == '-100'">
              {{ language.the_returned_shipments }}
            </span>
            <span v-else-if="form.status == '-1'">
              {{ language.canceled_shipments }}
            </span>
          </h2>
          <span class="flex px-3 font-bold text-gray-600"
            ><Icon name="shipment" class="w-5 h-5 text-gray-500 relative bottom-1 ml-1" />
            {{ shipments.total }}
          </span>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
            <select-input
              v-if="
                (store || receiver) &&
                ($page.props.auth.account == 'admin' ||
                  $page.props.auth.account == 'employee')
              "
              v-model="form.status"
              :selectPlaceholder="language.shipment_filtering"
            >
              <option :value="null">{{ language.all_shipments }}</option>
              <option value="10">{{ language.created }}</option>
              <option value="20">{{ language.received }}</option>
              <option value="35">{{ language.preparing }}</option>
              <option value="65">{{ language.out_to_deliver }}</option>
              <option value="100">{{ language.delivered }}</option>
              <option value="-100">{{ language.audit }}</option>
              <option value="-1">{{ language.canceled }}</option>
            </select-input>
          </search-filter>
        </div>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <form :action="route('shipments.file')" method="post" id="formSelect">
                <input type="hidden" name="_token" :value="$page.props.csrf" />
                <table class="min-w-full">
                  <thead class="border-b bg-gray-100 text-sm">
                    <tr class="whitespace-nowrap">
                      <th class="pb-4 pt-6 px-6">{{ language.shipment_number }}</th>
                      <th class="pb-4 pt-6 px-6">{{ language.create }}</th>
                      <th class="pb-4 pt-6 px-6">{{ language.delivery }}</th>
                      <th
                        class="pb-4 pt-6 px-6"
                        v-if="$page.props.auth.account != 'operator'"
                      >
                        {{ language.delivery_fee }}
                      </th>
                      <th class="pb-4 pt-6 px-6">
                        {{ language.paiement_when_recieving }}
                      </th>
                      <th class="pb-4 pt-6 px-6">{{ language.mobile_number }}</th>
                      <th class="pb-4 pt-6 px-6">{{ language.recipient_name }}</th>
                      <th class="pb-4 pt-6 px-6">{{ language.city_neighborhood }}</th>
                      <th class="pb-4 pt-6 px-6" v-if="(store || receiver) && form.status == null">
                        {{ language.Status }}
                      </th>
                      <th class="pb-4 pt-6 px-6">{{ language.notes }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="shipment in shipments.data"
                      :key="shipment.id"
                      class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
                    >
                      <td class="border-t">
                        <Link
                          class="flex items-center px-6 py-4 focus:text-indigo-500"
                          :href="route('shipments.show', shipment.id)"
                        >
                          {{ shipment.number }}
                        </Link>
                      </td>
                      <td class="border-t px-4">
                        {{ shipment.created_at }}
                      </td>
                      <td class="border-t px-4">
                        {{ shipment.shipping_date }}
                      </td>
                      <td
                        class="border-t px-4"
                        v-if="$page.props.auth.account != 'operator'"
                      >
                        {{ shipment.fees }}
                      </td>
                      <td class="border-t px-4">
                        <span v-if="!shipment.cod">{{ language.pre_paid }}</span>
                        <span v-else>{{ shipment.cod }}</span>
                      </td>
                      <td class="border-t px-4">
                        {{ shipment.receiverPhone }}
                      </td>
                      <td class="border-t px-4">
                        {{ shipment.receiverName }}
                      </td>
                      <td class="border-t px-4">
                        {{ shipment.city }} ({{ shipment.neighborhood }})
                      </td>

                      <td
                        class="border-t whitespace-nowrap px-6"
                        v-if="(store || receiver) && form.status == null"
                      >
                        <p
                          class="rounded-xl text-gray-700 bg-gray-200 py-1 px-4 w-fit text-xs"
                          v-if="shipment.status == 10"
                        >
                          {{ language.created }}
                        </p>
                        <p
                          class="rounded-xl text-orange-700 bg-orange-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == 20"
                        >
                          {{ language.received }}
                        </p>
                        <p
                          class="rounded-xl text-blue-700 bg-blue-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == 35"
                        >
                          {{ language.processing_in_progress }}
                        </p>
                        <p
                          class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == 65"
                        >
                          {{ language.out_to_deliver }}
                        </p>

                        <p
                          class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == 100"
                        >
                          {{ language.delivered }}
                        </p>

                        <p
                          class="rounded-xl text-red-700 bg-red-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == -100"
                        >
                          {{ language.audit }}
                        </p>

                        <p
                          class="rounded-xl text-red-700 bg-red-200 py-1 px-4 w-fit text-xs"
                          v-else-if="shipment.status == -1"
                        >
                          {{ language.canceled }}
                        </p>
                      </td>

                      <td class="border-t px-4">
                        {{ shipment.notice }}
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
import { lang } from "moment";

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
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    filters: Object,
    shipments: Object,
    operator: Object,
    store: Object,
    receiver: Object,
    language: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        method: this.filters.method,
        status: this.filters.status,
      },
    };
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        if (this.store)
          this.$inertia.get(
            `/dashboard/stores/${this.store.id}/shipments`,
            pickBy(this.form),
            { preserveState: true, preserveScroll: true }
          );

        if (this.operator)
          this.$inertia.get(
            `/dashboard/operators/${this.operator.id}/shipments`,
            pickBy(this.form),
            { preserveState: true, preserveScroll: true }
          );
      }, 150),
    },
  },

  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
  },
};
</script>
