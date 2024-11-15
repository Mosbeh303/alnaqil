<template>
  <div>
    <Head :title="language.receiver_page" />

    <page-header>
      <div class="flex justify-between">
        <div>
          {{ language.receiver_page }}
        </div>
        <div>
          <BackButton />
        </div>
      </div>
    </page-header>

    <div
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 divide-y sm:divide-y-0 divide-gray-200"
    >
      <div class="bg-white p-4 border-b border-gray-200" v-if="receiver.door_photo">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
          {{ language.door_photo }}
        </h2>
        <div class="flex flex-col items-center">
          <img
            class="object-contain h-64 w-full sm:h-full sm:w-auto rounded"
            :src="`/uploads/${receiver.door_photo}`"
          />
        </div>
      </div>

      <div class="bg-white p-4 border-b border-gray-200">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">{{ language.data }}</h2>
        <ul class="mt-4 space-y-2">
          <li class="text-gray-600">
            <span class="font-medium">{{ language.the_name }} :</span> {{ receiver.name }}
          </li>
          <li class="text-gray-600">
            <span class="font-medium">{{ language.phone_number }} :</span>
            {{ receiver.phone }}
          </li>
          <li class="text-gray-600" v-if="receiver.id_number">
            <span class="font-medium">{{ language.id_number }} :</span>
            {{ receiver.id_number }}
          </li>
          <li class="text-gray-600" v-if="receiver.national_adress">
            <span class="font-medium">{{ language.national_adress }} :</span>
            {{ receiver.national_adress }}
          </li>

          <li class="text-gray-600">
            <span class="font-medium">{{ language.number_of_shipments }} :</span>
            {{ shipmentsNumber }}
          </li>
        </ul>
      </div>

      <div class="bg-white p-4 border-b border-gray-200">
        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
          {{ language.coordinates }}
        </h2>
        <ul class="mt-4 space-y-2">
          <li class="text-gray-600">
            <span class="font-medium">{{ language.city }} :</span> {{ receiver.city }}
          </li>
          <li class="text-gray-600" v-if="receiver.neighborhood">
            <span class="font-medium">{{ language.neighborhood }} :</span>
            {{ receiver.neighborhood }}
          </li>
          <li class="text-gray-600" v-if="receiver.street">
            <span class="font-medium">{{ language.street_name }} :</span>
            {{ receiver.street }}
          </li>
          <li class="text-gray-600" v-if="receiver.door_number">
            <span class="font-medium">{{ language.door_number }} :</span>
            {{ receiver.door_number }}
          </li>
          <li
            class="text-gray-600"
            v-if="receiver.national_adress !== null || coordinatesForm.latitude !== null"
          >
            <span class="font-medium">{{ language.map_location }} :</span>
            {{ receiver.location_adress }}
          </li>
        </ul>
      </div>
    </div>

    <div
      id="map"
      v-if="
        receiver.national_adress !== null ||
        (coordinatesForm.latitude !== null && coordinatesForm.latitude !== '')
      "
      class="flex justify-center items-center h-screen flex-col"
    >
      <h1 class="text-center text-gray-700 text-lg mb-4 font-semibold">
        {{ language.map_location }}
      </h1>
      <div id="mapContainer" class="h-3/4 w-3/4" ref="hereMap_SH"></div>
    </div>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Section from "@/Components/Section.vue";
import BackButton from "../../../Components/backButton.vue";

export default {
  components: {
    PageHeader,
    BackButton,
    Section,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    receiver: Object,
    language: Object,
    shipmentsNumber: Number,
  },
  beforeMount() {
    if (this.receiver.location_data && this.receiver.location_data !== null) {
      const locationData = JSON.parse(this.receiver.location_data);
      if (locationData && locationData !== null) {
        this.coordinatesForm.latitude = locationData.latitude;
        this.coordinatesForm.longitude = locationData.longitude;
        this.coordinatesForm.location = this.receiver.location_adress;
      }
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

      this.platform = platform;

      this.dataForm.national_adress = this.extractValue(this.coordinatesForm.location);
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
        name: this.receiver.name,
        phone: this.receiver.phone,
        id_number: this.receiver.id_number,
        national_adress: this.receiver.national_adress,
      }),
      coordinatesForm: this.$inertia.form({
        latitude: null,
        longitude: null,
        location: null,
      }),
    };
  },
  methods: {
    extractValue(address) {
      const regex = /([A-Za-z]{4}\d{4})/; // Regular expression to match 4 characters followed by 4 digits
      const match = regex.exec(address);
      if (match && match[1]) {
        return match[1];
      }
      return "";
    },
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
          this.coordinatesForm.location = address.label;

          // Extract the latitude and longitude from the response
          const latitude = data.items[0].position.lat;
          const longitude = data.items[0].position.lng;
          // Update the dataForm with the new coordinates
          this.coordinatesForm.latitude = latitude;
          this.coordinatesForm.longitude = longitude;
        } catch (error) {
          console.error(error);
          // Handle error if geocoding fails
        }
      }

      const container = document.getElementById("mapContainer"); // Update the container reference
      if (container) {
        const H = window.H;
        const platform = this.platform;

        // Obtain the default map types from the platform object
        var defaultLayers = platform.createDefaultLayers();

        // Instantiate (and display) a map object:

        const map = new H.Map(container, defaultLayers.vector.normal.map, {
          zoom: 10, // set initial zoom level to show Saudi Arabia
          center: {
            lat: this.coordinatesForm.latitude,
            lng: this.coordinatesForm.longitude,
          },
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

        // Add event listener to map for capturing click event
        var marker;

        //default location

        // Create icon object
        const icon = new H.map.Icon("/images/location.png", {
          size: { w: 30, h: 30 },
        });

        // Create marker object
        marker = new H.map.Marker(
          { lat: this.coordinatesForm.latitude, lng: this.coordinatesForm.longitude },
          { icon: icon }
        );

        // Add marker to map
        map.addObject(marker);
      } else {
        console.error("Container element not found.");
        return;
      }
    },
  },
};
</script>
