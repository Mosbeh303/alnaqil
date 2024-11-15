<template>
  <Head title="Dashboard" />

  <div>
    <page-header> {{ language.dashboard }}</page-header>

    <FlashMessages class="md:m-7 m-4" />

    <div class="pb-12 pt-7 sm:px-0 px-4">
      <!-- Main Container -->

      <!-- Top Stats Widgets -->
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mb-7">
        <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">
          <Widget
            color="text-green-500"
            iconName="completed"
            :widgetText="language.completed_shipments"
            :widgetValue="stats.completedShipments"
          />
          <Widget
            color="text-orange-500"
            iconName="returned"
            :widgetText="language.returned_sh"
            :widgetValue="stats.returnedShipments"
          />

          <Widget
            color="text-red-500"
            iconName="cash"
            :widgetText="language.my_debts"
            :widgetValue="`${stats.dues}  ` + language.riyal"
          />
        </div>
      </div>

      <!--  Shipments -->
      <div
        class="border rounded border-gray-200 p-3 sm:p-5 sm:mx-6 lg:mx-8 bg-white mt-7"
      >
        <div class="flex justify-between items-baseline">

          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                    <span class="flex items-center">
                        {{ language.shipments_registered_in_my_name }}
                        <span class="flex px-3 font-bold text-gray-600">
                            <Icon
                                name="shipment"
                                class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"
                            />
                            {{ shipments.total }}
                        </span>
                    </span>
                </h2>
          <div>
            <form
              :action="route('operator.shipments_pdf', operator_id)"
              method="post"
              class="inline-block"
              id="form"
            >
              <input type="hidden" name="_token" :value="$page.props.csrf" />
              <Button class="!bg-gray-100 text-sm !text-gray-500 !py-2 inline-block ml-2">
                {{ language.download_PDF }}
              </Button>
            </form>

            <div class="relative inline-block">
              <SearchInput
                v-model="form.search"
                type="text"
                class="text-sm w-full bg-gray-100 border-0 outline-none"
                autocomplete="search"
                :placeholder="language.searsh_placeholder"
              />
              <button class="outline-none absolute left-2 top-2" @click="scan = !scan">
                <Icon name="camera" class="h-4 w-4 text-gray-400" />
              </button>
            </div>
          </div>
        </div>

        <div class="w-full text-center relative">
          <Scanner :scan="scan" :onDecode="onDecode" />
        </div>

        <div class="flex flex-col">
          <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
              <div class="overflow-x-auto border rounded">
                <table class="min-w-full">
                  <thead class="border-b bg-gray-100">
                    <tr>
                      <th
                        scope="col"
                        class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                      >
                        {{ language.Shipment_number }}
                      </th>
                      <th
                        scope="col"
                        class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                      >
                        {{ language.The_recipients_name }}
                      </th>
                      <th
                        scope="col"
                        class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                      >
                        {{ language.city_and_neighborhood }}
                      </th>
                      <th
                        scope="col"
                        class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                      >
                        {{ language.amount_when_delivery_riyal }}
                      </th>
                      <th
                        scope="col"
                        class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                      >
                        {{ language.notes }}
                      </th>
                      <th
                        scope="col"
                        class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                      ></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      class="border-t"
                      v-for="shipment in shipments.data"
                      :key="shipment.id"
                    >
                      <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                      >
                        <Link
                          class="flex items-center px-4"
                          :href="route('shipments.show', shipment.id)"
                          tabindex="-1"
                        >
                          {{ shipment.number }}
                        </Link>
                      </td>
                      <td
                        class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                      >
                        <Link
                          class="flex items-center px-4"
                          :href="route('shipments.show', shipment.id)"
                          tabindex="-1"
                        >
                          {{ shipment.receiverName }}
                        </Link>
                      </td>
                      <td
                        class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                      >
                        <Link
                          class="flex items-center px-4"
                          :href="route('shipments.show', shipment.id)"
                          tabindex="-1"
                        >
                          {{ shipment.city }} ({{ shipment.neighborhood }})
                        </Link>
                      </td>
                      <td
                        class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                      >
                        <Link
                          class="flex items-center px-4"
                          :href="route('shipments.show', shipment.id)"
                          tabindex="-1"
                        >
                          <span v-if="!shipment.cod">{{ language.pre_paid }}</span>
                          <span v-else>{{ shipment.cod }}</span>
                        </Link>
                      </td>
                      <td
                        class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                      >
                        <Link
                          class="flex items-center px-4"
                          :href="route('shipments.show', shipment.id)"
                          tabindex="-1"
                        >
                          {{ shipment.notice }}
                        </Link>
                      </td>
                      <td
                        class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                      >
                        <!-- Buttons  -->
                        <a
                          v-if="$page.props.auth.operatorType != 'rec'"
                          :href="`https://api.whatsapp.com/send?phone=${shipment.receiverPhone.replace(
                            '0',
                            '+966'
                          )}&text=${whatsapp(shipment)}`"
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
        <pagination class="mt-4" :links="shipments.links" />
      </div>
    </div>
    <!-- End Main Container -->
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Widget from "@/Pages/Dashboard/Components/Widget.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import SearchInput from "@/Components/Input.vue";
import Icon from "@/Components/Icon.vue";
import Scanner from "@/Pages/Dashboard/Components/BarcodeScanner.vue";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import Pagination from "@/Components/Pagination.vue";
import Modal from "@/Components/Modal.vue";
import FlashMessages from "@/Components/FlashMessages.vue";

export default {
  components: {
    Head,
    Link,
    PageHeader,
    Widget,
    SearchInput,
    Button,
    Icon,
    Scanner,
    Pagination,
    Modal,
    FlashMessages,
  },

  layout: BreezeAuthenticatedLayout,

  props: {
    filters: Object,
    shipments: Object,
    stats: Object,
    operator_id: Number,
    whatsappMessage: String,
    language: Object,
  },

  data() {
    return {
      form: {
        search: this.filters.search,
      },
      scan: false,
      showModal: false,
      paymentLink: "",
      number: "",
      id: null,
      text: "",
    };
  },

  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard", pickBy(this.form), {
          preserveState: true,
          preserveScroll: true,
        });
      }, 150),
    },
  },

  methods: {
    whatsapp(shipment) {
      if (this.whatsappMessage) {
        let msg = this.whatsappMessage.replace("{phone}", shipment.phone);
        msg = msg.replace("{receiver}", shipment.receiverName);
        msg = msg.replace("{number}", shipment.number);
        msg = msg.replace("{store}", shipment.storeName);
        msg = msg.replace("{amount}", shipment.cod);
        msg = msg.replace("{content}", shipment.details);
        return msg;
      }
    },

    // getNumber(number){
    //     this.form.search = number;
    // },

    onDecode(a) {
      if (this.scan == false) return false;
      console.log(a);
      this.form.search = a;
      this.text = a;
      if (this.id) clearTimeout(this.id);
      this.id = setTimeout(() => {
        if (this.text === a) {
          this.text = "";
        }
      }, 5000);
      this.scan = false;
    },
  },
};
</script>
