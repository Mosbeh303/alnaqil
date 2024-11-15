<template>
  <div>
    <Head :title="language.shipping_policy" />

    <page-header>
      {{ language.shipment_no }} {{ shipment.number }}
      <Button
        v-if="shipment.failed < 3 && $page.props.auth.account !== 'client'"
        :class="{
          '!text-gray-700 !bg-yellow-400 md:absolute md:right-7':
            $page.props.locale == 'en',
          '!text-gray-700 !bg-yellow-400 md:absolute md:left-7':
            $page.props.locale == 'ar',
        }"
        @click="showingModal = !showingModal"
      >
        {{ language.unsuccessful_delivery_attempt }}</Button
      >
    </page-header>

    <flash-messages class="m-4 md:m-7" />

    <div class="grid gap-4 m-4 md:gap-7 md:grid-cols-2 md:m-7">
      <!-- بوليصة الشحن -->
      <div class="p-3 bg-white border border-gray-200 rounded sm:p-5">
        <h2 class="mb-4 text-lg font-semibold text-gray-700">
          {{ language.shipping_policy }}
        </h2>

        <iframe :src="route('shipments.policy', shipment.number) + '#view=fit&toolbar=0'" class="w-full h-screen "  title="description"></iframe>
        <!-- <Bill :shipment="shipment" /> -->

        <div class="flex justify-center gap-2">
          <a
            :href="route('shipments.policy', shipment.number)"
            target="_blank"
            class="flex items-center px-4 py-3 my-4 text-xs font-semibold tracking-widest text-gray-600 uppercase transition duration-150 ease-in-out bg-gray-200 border border-transparent rounded-md w-fit hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 hover:text-white focus:shadow-outline-gray"
            >{{ language.download_the_policy }}</a
          >

          <a
            v-if="shipment.jtBillCode !== null && $page.props.auth.account !== 'client'"
            :href="route('shipments.jtwaybill', shipment.jtBillCode)"
            target="_blank"
            class="flex items-center px-4 py-3 my-4 text-xs font-semibold tracking-widest text-gray-600 uppercase transition duration-150 ease-in-out bg-gray-200 border border-transparent rounded-md w-fit hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 hover:text-white focus:shadow-outline-gray"
            >بوليصة J&T</a
          >

          <a
            v-if="shipment.return == 1 || shipment.exchange == 1"
            :href="route('shipments.exchangebill', shipment.number)"
            target="_blank"
            class="flex items-center px-4 py-3 my-4 text-xs font-semibold tracking-widest text-gray-600 uppercase transition duration-150 ease-in-out bg-gray-200 border border-transparent rounded-md w-fit hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 hover:text-white focus:shadow-outline-gray"
            >{{ language.Download_exchange_or_refund }}</a
          >
          <a
            v-if="(shipment.status == 100 || shipment.status == -100) && $page.props.auth.account != 'client'"
            target="_blank"
            :href="route('shipments.invoice.pdf', shipment.number)"
            class="flex items-center px-4 py-3 my-4 text-xs font-semibold tracking-widest text-gray-600 uppercase transition duration-150 ease-in-out bg-gray-200 border border-transparent rounded-md w-fit hover:text-white hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray"
            >{{ language.download_the_invoice }}</a
          >
        </div>
      </div>

      <div>
        <!-- Create shipment notice  -->
        <div
          class="p-3 mb-4 bg-white border border-gray-200 rounded sm:p-5 md:mb-7"
          v-if="
            $page.props.auth.account == 'admin' ||
            $page.props.auth.permissions.includes('add_notices_shipments')
          "
        >
          <h2 class="mb-4 text-lg font-semibold text-gray-700">
            {{ language.add_a_note }}
          </h2>

          <form @submit.prevent="submit">
            <textarea
              :placeholder="language.add_a_note"
              v-model="form.notice2"
              class="w-full h-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            ></textarea>
            <Button
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              class="px-6 mt-4"
            >
              {{ language.add_a_note }}
            </Button>
          </form>
        </div>

        <Section :title="language.notes" class="mb-4 md:mb-7">
          <ul class="mt-4">
            <li
              v-if="notices === null || notices.length === 0"
              class="flex gap-2 text-sm text-blue-500"
            >
              <Icon name="warning" class="w-4 h-4" /> {{ language.there_is_no_notes }}
            </li>
            <li class="flex justify-between" v-for="notice in notices" :key="notice.id">
              <span>{{ notice.notice }}</span>
              <span
                v-if="
                  $page.props.auth.account == 'admin' ||
                  $page.props.auth.permissions.includes('add_notices_shipments')
                "
                >{{ language.by }} {{ notice.user }}</span
              >
              <span>{{ notice.date }}</span>
            </li>
          </ul>
        </Section>

        <div v-if="$page.props.auth.account == 'operator'" class="mb-4 md:mb-7">
          <div class="flex justify-end gap-3">
            <Button
              v-if="shipment.status == 10"
              :href="route('operator.shipment.receive', shipment)"
              method="post"
              class="mx-auto justify-center !text-green-600 bottom-4 !bg-transparent !border-green-500 ml-0 text-xs flex-no-wrap hover:!bg-green-500 hover:!text-white !rounded-xl !py-2 !px-4"
            >
              <span>{{ language.received }} </span>
            </Button>

            <Button
              v-if="shipment.status == 20"
              :href="route('operator.shipment.processing', shipment)"
              method="post"
              class="mx-auto justify-center !text-green-600 bottom-4 !bg-transparent !border-green-500 ml-0 text-xs flex-no-wrap hover:!bg-green-500 hover:!text-white !rounded-xl !py-2 !px-4"
            >
              <span>{{ language.processing_in_progress }} </span>
            </Button>

            <Button
              v-if="shipment.status == 35"
              :href="route('operator.shipment.assign', shipment)"
              method="post"
              class="mx-auto justify-center !text-blue-600 bottom-4 !bg-transparent !border-blue-500 ml-0 text-xs flex-no-wrap hover:!bg-blue-500 hover:!text-white !rounded-xl !py-2 !px-4"
            >
              <span>{{ language.receipt_from_the_warehouse }} </span>
            </Button>

            <Button
              v-if="shipment.status == 65 && $page.props.auth.operatorType == 'rec'"
              :href="route('operator.shipment.returned', shipment)"
              method="post"
              class="mx-auto justify-center !text-red-600 bottom-4 !bg-transparent !border-red-500 ml-0 text-xs flex-no-wrap hover:!bg-red-500 hover:!text-white !rounded-xl !py-2 !px-4"
            >
              <span>{{ language.audit }} </span>
            </Button>

            <Button
              v-if="shipment.status == 65 && $page.props.auth.operatorType != 'rec'"
              @click="showingPaymentModal(shipment)"
              class="mx-auto justify-center !text-blue-600 bottom-4 !bg-transparent !border-blue-500 ml-0 text-xs flex-no-wrap hover:!bg-blue-500 hover:!text-white !rounded-xl !py-2 !px-4"
            >
              <span> {{ language.delivered }} </span>
            </Button>
          </div>
        </div>

        <div >
          <Section class="mb-4 md:mb-7" v-if="receiver!==null &&
            receiver.national_adress !== null &&
           ( $page.props.auth.account == 'admin' ||
            $page.props.auth.account == 'operator' ||
            $page.props.auth.permissions.includes('receivers_stores'))
          ">
            <ul class="mt-4 space-y-2">
              <span
                v-if="receiver.national_adress !== null || dataForm.latitude !== null"
              >
                {{ language.receiver_location }} : {{ receiver.location_adress }}
              </span>
              <li class="flex justify-between text-gray-600">
                <a
                  v-if="receiver.national_adress !== null || dataForm.latitude !== null"
                  :href="googleMapsUrl"
                  target="_blank"
                  class="mr-4 text-blue-500 no-underline hover:underline"
                >
                  {{ language.visit_with_google_map }}
                </a>
                <a
                  v-if="receiver.door_photo"
                  :href="'/uploads/' + receiver.door_photo"
                  class="mr-4 text-blue-500 no-underline hover:underline"
                  >{{ language.show_door_photo }}</a
                >
              </li>

              <li
                class="text-gray-600"
                v-if="receiver.national_adress !== null || dataForm.latitude !== null"
              >
                <div id="map">
                  <div
                    id="mapContainer"
                    style="height: 600px; width: 100%"
                    ref="hereMap"
                  ></div>
                </div>
              </li>
            </ul>
          </Section>
        </div>
        <div  v-if="shipment.jtBillCode == null && ( $page.props.auth.account == 'admin' ||
            $page.props.auth.permissions.includes('shipments_jt_create_order'))
          ">
                <Button :href="route('shipments.jt.create', shipment.id)">{{language.issuing_jt_waybill}}</Button>
        </div>
      </div>
    </div>

    <!-- Main modal -->
    <div
      :class="{ hidden: !showingModal }"
      class="fixed left-0 right-0 z-50 flex items-center justify-center h-screen overflow-x-hidden overflow-y-auto bg-gray-900 bg-opacity-40 top-4 md:inset-0"
    >
      <div class="relative w-full h-full max-w-2xl px-4 md:h-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow opacity-100">
          <!-- Modal header -->
          <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold text-gray-900">
              {{ language.unsuccessful_delivery_attempt }}
            </h3>
            <button
              type="button"
              @click="showingModal = false"
              class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 mr-auto inline-flex items-center"
              data-modal-toggle="defaultModal"
            >
              <svg
                class="w-5 h-5"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                ></path>
              </svg>
            </button>
          </div>
          <!-- Modal body -->
          <div class="p-6 space-y-6">
            <form @submit.prevent="submitFailShip" id="failShip">
              <SelectInput
                v-model="formFailShip.notice"
                :error="formFailShip.errors.notice"
                class="block w-full mt-4"
                :selectPlaceholder="language.choose_an_option"
              >
                <option
                  v-for="(option, i) in failedNoticeOptions"
                  :key="i"
                  :value="option"
                >
                  {{ option }}
                </option>
              </SelectInput>
            </form>
          </div>
          <!-- Modal footer -->
          <div
            class="flex flex-row-reverse items-center p-6 space-x-2 border-t border-gray-200 rounded-b"
          >
            <Button
              :class="{ 'opacity-25': formFailShip.processing }"
              :disabled="formFailShip.processing"
              class="mt-4 px-6 !border !border-gray-500"
              form="failShip"
            >
              {{ language.save }}
            </Button>
            <Button
              @click="showingModal = false"
              type="button"
              class="mt-4 px-6 !mr-4 !bg-transparent !text-gray-500 !border !border-gray-500"
            >
              {{ language.cancel }}
            </Button>
          </div>
        </div>
      </div>
    </div>

    <Modal :show="showModal">
      <Icon
        name="x"
        class="absolute w-5 h-5 text-gray-500 cursor-pointer left-3 top-3"
        @click="showModal = false"
      ></Icon>

      <h2 class="mb-2 text-lg font-semibold text-gray-800">
        {{ language.choose_how_to_pay_the_amount }}
      </h2>

      <Button
        :href="paymentLink + '?method=cash'"
        method="post"
        @click="showModal = false"
        class="!bg-transparent !border !text-blue-600 !border-blue-400 mt-4 !mr-3 hover:!bg-blue-800 hover:!text-white hover:!border-blue-800"
      >
        <span>{{ language.cash }}</span>
      </Button>
      <Button
        @click="showModal = false"
        :href="paymentLink + '?method=epayment'"
        method="post"
        class="!bg-transparent !border !text-red-600 !border-red-400 !mr-3 hover:!bg-red-800 hover:!text-white hover:!border-red-800"
        >{{ language.network }}</Button
      >
    </Modal>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Icon from "@/Components/Icon.vue";
import Section from "@/Components/Section.vue";
import Modal from "@/Components/Modal.vue";
import Bill from "./Bill.vue";

export default {
  components: {
    Head,
    PageHeader,
    Button,
    Input,
    FlashMessages,
    SelectInput,
    InputError,
    Label,
    Bill,
    Section,
    Icon,
    Modal,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    shipment: Object,
    receiver: Object,
    notices: Object,
    failedNoticeOptions: Array,
    language: Object,
  },

  beforeMount() {
    if (this.receiver !== null) {
      this.dataForm.address = this.receiver.location_adress;

      // Instantiate (and display) a map object:
      const locationData = JSON.parse(this.receiver.location_data);
      if (locationData !== null) {
        if (locationData.latitude !== null || locationData.longitude !== null) {
          this.dataForm.latitude = locationData.latitude;
          this.dataForm.longitude = locationData.longitude;
        }
      }
      // Initialize the platform object:
    }
  },
  async mounted() {
    try {
      // Load all the HERE Maps scripts dynamically
      await this.loadScript("https://js.api.here.com/v3/3.1/mapsjs-core.js");
      await this.loadScript("https://js.api.here.com/v3/3.1/mapsjs-service.js");
      await this.loadScript("https://js.api.here.com/v3/3.1/mapsjs-ui.js");
      await this.loadScript("https://js.api.here.com/v3/3.1/mapsjs-mapevents.js");

      // Once all scripts are loaded, initialize the platform
      const H = window.H; // Access the `H` object from the global window object
      const platform = new H.service.Platform({
        apikey: this.apikey,
      });

      console.log(platform); // Check if the platform is defined properly

      this.platform = platform;
    } catch (error) {
      console.error("Error loading HERE Maps scripts:", error);
    }
    this.initializeHereMap();
  },
  data() {
    return {
      platform: null,
      apikey: "-IqF16CutqVTBnUevvdhnE1aQ7-qfgBDeYwSL97Kv5U",
      dataForm: this.$inertia.form({
        id_number: null,
        door_number: null,
        latitude: null,
        longitude: null,
        address: null,
      }),
      form: this.$inertia.form({
        notice: "",
        notice2: "",
      }),
      formUpdatePhone: this.$inertia.form({
        phone: this.shipment.receiverPhone,
      }),
      formFailShip: this.$inertia.form({
        notice: "",
      }),
      showingModal: false,
      showModal: false,
      paymentLink: "",
      number: "",
      id: null,
      text: "",
    };
  },
  computed: {
    googleMapsUrl() {
      const baseUrl = "http://maps.google.com/maps";
      const searchParam = `?q=${this.dataForm.latitude},${this.dataForm.longitude}`;
      return baseUrl + searchParam;
    },
  },
  methods: {
    loadScript(src) {
      return new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = src;
        script.async = true;
        script.defer = true;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
      });
    },
    async initializeHereMap() {
      if (this.receiver !== null) {
        if (this.receiver.national_adress !== null) {
          try {
            // Call the HERE Geocoding API to convert address to coordinates
            const response = await fetch(
              `https://geocode.search.hereapi.com/v1/geocode?q=${encodeURIComponent(
                this.receiver.national_adress
              )}&apiKey=${this.apikey}`
            );
            const data = await response.json();
            const address = data.items[0].address;
            this.dataForm.address = address.label;
            // Extract the latitude and longitude from the response
            const latitude = data.items[0].position.lat;
            const longitude = data.items[0].position.lng;
            // Update the dataForm with the new coordinates
            this.dataForm.latitude = latitude;
            this.dataForm.longitude = longitude;
          } catch (error) {
            console.error(error);
            // Handle error if geocoding fails
          }
        }
      }
      const container = document.getElementById("mapContainer"); // Update the container reference

      if (!container) {
        console.error("Container element not found.");
        return;
      }
      const H = window.H;
      const platform = this.platform;

      // Obtain the default map types from the platform object
      var defaultLayers = platform.createDefaultLayers();

      const map = new H.Map(container, defaultLayers.vector.normal.map, {
        zoom: 10, // set initial zoom level to show Saudi Arabia
        center: { lat: this.dataForm.latitude, lng: this.dataForm.longitude },
      });

      // create a rectangular polygon that covers Saudi Arabia
      var saudiArabiaBounds = new H.geo.Rect(16.36, 34.57, 32.16, 55.67); // set bounds of Saudi Arabia

      var saudiArabiaPolygon = new H.map.Rect(saudiArabiaBounds, {
        style: {
          fillColor: "rgba(0, 0, 255, 0.1)", // set fill color of the rectangle
          strokeColor: "rgba(0, 0, 255, 1)", // set stroke color of the rectangle
          lineWidth: 2, // set stroke width of the rectangle
        },
      });

      // create a group to hold the rectangle polygon
      var saudiArabiaGroup = new H.map.Group();

      // add the rectangle polygon to the group
      saudiArabiaGroup.addObject(saudiArabiaPolygon);

      // add the group to the map
      map.addObject(saudiArabiaGroup);
      addEventListener("resize", () => map.getViewPort().resize());

      // add behavior control
      new H.mapevents.Behavior(new H.mapevents.MapEvents(map));

      // add UI
      H.ui.UI.createDefault(map, defaultLayers);

      // Create icon object
      const icon = new H.map.Icon("/images/location.png", {
        size: { w: 30, h: 30 },
      });

      // Create marker object with custom lat and lng
      const marker = new H.map.Marker(
        {
          lat: this.dataForm.latitude,
          lng: this.dataForm.longitude,
        },
        { icon: icon }
      );

      // Add marker to map
      map.addObject(marker);

      // Set form data to custom lat and lng
      this.dataForm.latitude = this.dataForm.latitude;
      this.dataForm.longitude = this.dataForm.longitude;
    },
    submit() {
      this.form.post(this.route("shipments.notice.store", this.shipment.id), {
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },
    updatePhone() {
      this.formUpdatePhone.post(this.route("shipments.update_phone", this.shipment.id), {
        preserveScroll: true,
        onError: () => this.form.reset(),
        onFinish: () => this.form.reset(""),
      });
    },
    submitFailShip() {
      this.showingModal = false;
      this.formFailShip.post(this.route("shipments.failed_delivery", this.shipment.id), {
        preserveScroll: true,
        onSuccess: function () {
          this.showingModal = false;
        },
        onError: () => this.formFailShip.reset(""),
      });
    },
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
    showingPaymentModal(shipment) {
      if (shipment.cod > 0) {
        this.paymentLink = route("operator.shipment.completed", shipment);
        this.showModal = true;
      } else {
        this.$inertia.post(route("operator.shipment.completed", shipment), {
          method: null,
        });
      }
    },
  },
};
</script>
