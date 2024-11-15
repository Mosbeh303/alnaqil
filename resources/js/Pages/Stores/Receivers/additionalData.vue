<template>
  <div dir="rtl">
    <Head :title="language.additional_data" />
    <page-header>
      <div class="flex justify-between">
        <div>
          {{ language.additional_data }}
        </div>
      </div>
    </page-header>
    <flash-messages class="mx-auto max-w-3xl mt-7" />

    <div class="grid md:grid-cols-1 gap-7 m-4 md:m-7">
      <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
        <section class="border-b border-gray-200 pb-4">
          <form @submit.prevent="addReceiverData" class="mt-4">
            <h2 class="text-gray-700 text-lg mb-4 font-semibold">
              {{ language.data }}
            </h2>
            <ul class="mt-4 space-y-4">
              <li class="text-gray-600">
                <Label
                  :value="language.id_number + ' ' + '(' + language.optional + ')'"
                />
                <Input
                  id="id_number"
                  type="text"
                  v-model="dataForm.id_number"
                  :error="dataForm.errors.id_number"
                  class="mt-2 block w-full"
                  :placeholder="language.id_number"
                />
                <InputError :message="dataForm.errors.id_number" />
              </li>
              <li class="text-gray-600">
                <Label
                  :value="language.national_adress + ' ' + '(' + language.optional + ')'"
                />
                <Input
                  id="street_name"
                  type="text"
                  v-model="dataForm.national_adress"
                  :error="dataForm.errors.national_adress"
                  class="mt-2 block w-full"
                  :placeholder="language.national_adress"
                />

                <InputError :message="dataForm.errors.national_adress" />
              </li>
              <li class="text-gray-600">
                <Label
                  :value="language.door_number + ' ' + '(' + language.optional + ')'"
                />
                <Input
                  id="door_number"
                  type="text"
                  v-model="dataForm.door_number"
                  :error="dataForm.errors.door_number"
                  class="mt-2 block w-full"
                  :placeholder="language.door_number"
                />
                <InputError :message="dataForm.errors.door_number" />
              </li>

              <li class="text-gray-600">
                <Label
                  :value="language.street_name + ' ' + '(' + language.optional + ')'"
                />
                <Input
                  id="street_name"
                  type="text"
                  v-model="dataForm.street_name"
                  :error="dataForm.errors.street_name"
                  class="mt-2 block w-full"
                  :placeholder="language.street_name"
                />

                <InputError :message="dataForm.errors.street_name" />
              </li>
              <li class="text-gray-600">
                <div class="flex items-center">
                  <div class="flex-1 flex items-center justify-between">
                    <span class="inline-flex items-center">
                      <span>{{
                        language.door_photo + " " + "(" + language.optional + ")"
                      }}</span>
                    </span>
                    <Label
                      v-if="dataForm.door_photo.length === 0"
                      for="file"
                      class="inline-block"
                    >
                      <input
                        id="file"
                        type="file"
                        hidden
                        @input="dataForm.door_photo = $event.target.files[0]"
                        autofocus
                      />
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6 mr-2"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"
                        />
                      </svg>
                    </Label>
                    <Label
                      v-else
                      for="file"
                      class="underline underline-offset-2 inline-block"
                    >
                      <input
                        id="file"
                        type="file"
                        hidden
                        @input="dataForm.door_photo = $event.target.files[0]"
                        autofocus
                      />
                      {{ dataForm.door_photo.name }}
                    </Label>
                  </div>
                </div>
              </li>
              <li class="text-gray-600">
                <Label :value="language.map_location + ' : ' + dataForm.location" />
              </li>
              <li class="text-gray-600">
                <div id="map">
                  <div
                    id="mapContainer"
                    style="height: 600px; width: 100%"
                    ref="mapContainer"
                  ></div>

                  <Input hidden type="text" v-model="dataForm.latitude" id="latitude" />
                  <Input hidden type="text" v-model="dataForm.longitude" id="longitude" />
                  <Input hidden type="text" v-model="dataForm.location" id="location" />
                </div>
              </li>
              <li>
                <Button class="text-left float-left">{{ language.send }}</Button>
              </li>
            </ul>
          </form>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Button from "@/Components/Button.vue";
import { Head } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import FlashMessages from "../../../Components/FlashMessages.vue";
import searchableSelect from "../../../Components/searchableSelect.vue";

export default {
  components: {
    Head,
    PageHeader,
    Button,
    Input,
    Checkbox,
    BreezeValidationErrors,
    FlashMessages,
    SelectInput,
    InputError,
    Label,
    searchableSelect,
  },

  props: {
    receiver: Object,
    language: Object,
  },

  data() {
    return {
      platform: null,
      apikey: "-IqF16CutqVTBnUevvdhnE1aQ7-qfgBDeYwSL97Kv5U",

      dataForm: this.$inertia.form({
        door_photo: "",
        id_number: "",
        national_adress: "",
        door_number: "",
        street_name: "",

        latitude: null,
        longitude: null,
        location: this.language.alriyadh,
      }),
    };
  },

  async mounted() {
    // Initialize the platform object:

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

      this.dataForm.national_adress = this.extractValue(this.dataForm.location);
    } catch (error) {
      console.error("Error loading HERE Maps scripts:", error);
    }
    this.initializeHereMap();
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

    extractValue(address) {
      const regex = /([A-Za-z]{4}\d{4})/; // Regular expression to match 4 characters followed by 4 digits
      const match = regex.exec(address);
      if (match && match[1]) {
        return match[1];
      }
      return "";
    },
    async initializeHereMap() {
      if (this.dataForm.national_adress !== null) {
        try {
          // Call the HERE Geocoding API to convert address to coordinates
          const response = await fetch(
            `https://geocode.search.hereapi.com/v1/geocode?q=${encodeURIComponent(
              this.dataForm.national_adress
            )}&apiKey=${this.apikey}`
          );
          const data = await response.json();
          const address = data.items[0].address;
          this.dataForm.location = address.label;

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
      const container = this.$refs.mapContainer; // Update the container reference
      if (container) {
        container.innerHTML = "";
        // Obtain the default map types from the platform object
        var defaultLayers = this.platform.createDefaultLayers();

        // Instantiate (and display) a map object:

        const map = new H.Map(container, defaultLayers.vector.normal.map, {
          zoom: 10, // set initial zoom level to show Saudi Arabia
          center: {
            lat: 24.7136,
            lng: 46.6753,
          }, // set center to Riyadh, Saudi Arabia
        });
        H.util.Disposable();

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
          { lat: this.dataForm.latitude, lng: this.dataForm.longitude },
          { icon: icon }
        );

        // Add marker to map
        map.addObject(marker);

        // Add event listener to map for capturing click event

        if (this.$page.props.auth.user == null) {
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              async (position) => {
                const { latitude, longitude } = position.coords;

                map.setCenter({ lat: latitude, lng: longitude });

                this.dataForm.latitude = latitude;
                this.dataForm.longitude = longitude;

                const icon = new H.map.Icon("/images/location.png", {
                  size: { w: 30, h: 30 },
                });

                // Create marker object
                marker = new H.map.Marker(
                  { lat: latitude, lng: longitude },
                  { icon: icon }
                );

                // Add marker to map
                map.addObject(marker);

                const reverseGeocodeUrl = `https://revgeocode.search.hereapi.com/v1/revgeocode?apikey=${this.apikey}&at=${latitude},${longitude}&lang=${this.language.code}`;
                const response = await fetch(reverseGeocodeUrl);
                const data = await response.json();
                const address = data.items[0].address.label;
                this.dataForm.location = address;
                this.dataForm.national_adress = this.extractValue(address);

              },
              (error) => {
                console.error(error.message);
              },
              (error) => {
                console.error(error.message);
              }
            );
          } else {
            console.error("Geolocation is not supported by this browser.");
          }
        }
        map.addEventListener("tap", async (evt) => {
          var coord = map.screenToGeo(
            evt.currentPointer.viewportX,
            evt.currentPointer.viewportY
          );

          const reverseGeocodeUrl = `https://revgeocode.search.hereapi.com/v1/revgeocode?apikey=${this.apikey}&at=${coord.lat},${coord.lng}&lang=${this.language.code}`;
          const response = await fetch(reverseGeocodeUrl);
          const data = await response.json();
          const address = data.items[0].address.label;

          // Create icon object
          const icon = new H.map.Icon("/images/location.png", {
            size: { w: 30, h: 30 },
          });

          // If a marker object already exists, remove it from the map
          if (marker) {
            map.removeObject(marker);
          }

          // Create marker object
          marker = new H.map.Marker({ lat: coord.lat, lng: coord.lng }, { icon: icon });

          // Add marker to map
          map.addObject(marker);

          this.dataForm.latitude = coord.lat;
          this.dataForm.longitude = coord.lng;
          this.dataForm.location = address;
          this.dataForm.national_adress = this.extractValue(address);
        });
      } else {
        console.error("Container element not found.");
        return;
      }
    },

    async addReceiverData() {
      await this.initializeHereMap();
      if (this.$page.props.auth.user !== null) {
        this.dataForm.post(route("receivers.admin.addData", this.receiver), {
          forceFormData: true,
        });
      } else {
        this.dataForm.post(route("receivers.addData", this.receiver), {
          forceFormData: true,
        });
      }
    },
  },
};
</script>
