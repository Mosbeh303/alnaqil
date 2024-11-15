<template>
  <Head :title="language.Track_shipments" />

  <div>
    <page-header> {{ language.Track_shipments }} </page-header>

    <FlashMessages class="md:m-7 m-4" />

    <Section
      :title="language.search_by_the_shipment_number_or_the_recipients_mobile_number"
      class="md:m-7 m-4"
    >
      <form @submit.prevent="" class="pb-2">
        <Input
          id="search"
          type="text"
          :placeholder="language.enter_the_shipment_number"
          class="w-full mt-4"
          v-model="form.search"
          :error="form.errors.search"
        ></Input>
        <InputError :message="form.errors.search" />
      </form>
    </Section>

    <div
      v-if="shipments"
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white md:m-7 m-4 overflow-hidden"
    >
      <!-- ***************** -->

      <div class="flex justify-between items-baseline">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">الشحنات</h2>
        <form
          :action="route('shipments.getShipmentsTrackingPdf')"
          method="post"
          id="form"
        >
          <input type="hidden" name="_token" :value="$page.props.csrf" />
          <input type="hidden" name="all" v-model="form.all" />

          <Button
            for="from"
            class="bg-gray-200 !text-gray-600 py-2 hover:!text-gray-200 text-sm"
          >
            {{ language.download }}
          </Button>
        </form>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th></th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.Shipment_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.create }}</th>
                    <th
                      class="pb-4 pt-6 px-6"
                      v-if="
                        $page.props.auth.permissions.includes('fee_shipments') ||
                        $page.props.auth.account != 'employee'
                      "
                    >
                      {{ language.delivery_fee }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.paiement_when_recieving }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.mobile_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.sender_name }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.city_neighborhood }}
                    </th>
                    <th class="pb-4 pt-6 px-6" v-if="form.status == null">
                      {{ language.Status }}
                    </th>

                    <th class="pb-4 pt-6 px-6" colspan="2"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="shipment in shipments.data"
                    :key="shipment.id"
                    class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
                  >
                    <td>
                      <button class="px-2" @click="deleteShipment(shipment.number)">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-500"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                          />
                        </svg>
                      </button>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4 focus:text-indigo-500"
                        :href="route('shipments.show', shipment.id)"
                      >
                        {{ shipment.number }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.shipping_date }}
                      </Link>
                    </td>
                    <td
                      class="border-t"
                      v-if="
                        $page.props.auth.permissions.includes('fee_shipments') ||
                        $page.props.auth.account != 'employee'
                      "
                    >
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.fees }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        <span v-if="!shipment.cod">{{ language.pre_paid }}</span>
                        <span v-else>{{ shipment.cod }}</span>
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.receiverPhone }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.storeName }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
                      >
                        {{ shipment.city }} ({{ shipment.neighborhood }})
                      </Link>
                    </td>
                    <td class="border-t whitespace-nowrap" v-if="form.status == null">
                      <Link
                        class="flex items-center px-6 py-4"
                        :href="route('shipments.show', shipment.id)"
                        tabindex="-1"
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
                          v-else-if="shipment.status == 100"
                        >
                          {{ language.delivered }}
                        </p>
                        <p
                          class="rounded-xl text-yellow-700 bg-yellow-200 py-1 px-4 w-fit text-xs"
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
                      </Link>
                    </td>

                    <td class="w-px border-t">
                      <Link
                        class="flex items-center px-4"
                        :href="route('shipments.getShipmentTracking', shipment.number)"
                        tabindex="-1"
                      >
                        <icon name="list" class="block w-5 h-5 text-gray-500" />
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
import Pagination from "@/Components/Pagination.vue";
import Icon from "@/Components/Icon.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Section from "@/Components/Section.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import throttle from "lodash/throttle";

export default {
  components: {
    Head,
    PageHeader,
    Button,
    Link,
    Pagination,
    Icon,
    Section,
    Input,
    FlashMessages,
    InputError,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    shipments: Object,
    language: Object,
  },

  data() {
    return {
      form: this.$inertia.form({
        search: "",
        all: [],
      }),
    };
  },

  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        if (this.form.search.length === 10) {
          this.form.all.push(this.form.search);
          this.form.post(this.route("shipments.getTracking"), {
            preserveScroll: true,
            onSuccess: () => this.form.reset("search"),
            onError: () => this.form.reset("search"),
            onFinish: () => this.form.reset("search"),
          });
        }
      }, 150),
    },
  },

  computed: {
    csrf() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    },
  },
  methods: {
    deleteShipment(number) {
      let index = this.form.all.indexOf(number);
      this.form.all.splice(index, 1);
      this.form.post(this.route("shipments.getTracking"), {
        preserveScroll: true,
        // onSuccess: () => this.form.reset("search"),
      });
    },
  },
};
</script>
