<template>
  <div>
    <Head :title="language.receiver_modification" />
    <page-header>
      <div class="flex justify-between">
        <div>{{ language.receiver_modification }}</div>
        <div>
          <BackButton />
        </div>
      </div>
    </page-header>
    <flash-messages class="mx-auto max-w-3xl mt-7" />

    <div class="grid md:grid-cols-2 gap-7 m-4 md:m-7">
      <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
        <section class="border-b border-gray-200 pb-4">
          <form @submit.prevent="updateCoordinates" class="mt-4">
            <h2 class="text-gray-700 text-lg mb-4 font-semibold">
              {{ $page.props.language.coordinates }}
            </h2>
            <ul class="mt-4 space-y-4">
              <li>
                <div>
                  <Button type="submit">{{ language.save_coordinates }}</Button>
                </div>
              </li>
              <li class="text-gray-600">
                <Label :value="language.city" />

                <searchableSelect
                  :myOptions="computedCities"
                  :myVModel="coordinatesForm.city"
                  :my_Place_holder="$page.props.language.city"
                  :my_Place_holder_when_Nothing_Found="
                    $page.props.language.No_cities_in_this_terretory
                  "
                  @update:myVModel="coordinatesForm.city = $event"
                />

                <!--    <Input
                    id="city"
                    type="text"
                    v-model="coordinatesForm.city"
                    :error="coordinatesForm.errors.city"
                    class="mt-2 block w-full"
                    required
                    :placeholder="language.city"
                  />
                  <InputError :message="coordinatesForm.errors.city" />-->
              </li>
              <li class="text-gray-600">
                <Label :value="language.neighborhood + '(' + language.optional + ')'" />
                <Input
                  id="neighborhood"
                  type="text"
                  v-model="coordinatesForm.neighborhood"
                  :error="coordinatesForm.errors.neighborhood"
                  class="mt-2 block w-full"
                  :placeholder="language.neighborhood"
                />
                <InputError :message="coordinatesForm.errors.neighborhood" />
              </li>
              <li class="text-gray-600">
                <Label
                  :value="language.street_name + ' ' + '(' + language.optional + ')'"
                />
                <Input
                  id="street"
                  type="text"
                  v-model="coordinatesForm.street"
                  :error="coordinatesForm.errors.street"
                  class="mt-2 block w-full"
                  :placeholder="language.street_name"
                />
                <InputError :message="coordinatesForm.errors.street" />
              </li>
              <li class="text-gray-600">
                <Label
                  :value="language.door_number + ' ' + '(' + language.optional + ')'"
                />
                <Input
                  id="door_number"
                  type="text"
                  v-model="coordinatesForm.door_number"
                  :error="coordinatesForm.errors.door_number"
                  class="mt-2 block w-full"
                  :placeholder="language.door_number"
                />
                <InputError :message="coordinatesForm.errors.door_number" />
              </li>
              <li class="text-gray-600">
                <Label
                  :value="language.national_adress + ' ' + '(' + language.optional + ')'"
                />
                <Input
                  id="national_adress"
                  type="text"
                  v-model="coordinatesForm.national_adress"
                  :error="coordinatesForm.errors.national_adress"
                  class="mt-2 block w-full"
                  :placeholder="language.national_adress"
                />
                <InputError :message="coordinatesForm.errors.national_adress" />
              </li>
              <li
                class="text-gray-600"
                v-if="
                  receiver.national_adress !== null || coordinatesForm.latitude !== null
                "
              >
                <Label
                  :value="language.map_location + ' : ' + coordinatesForm.location"
                />
              </li>
              <li
                class="text-gray-600"
                v-if="
                  receiver.national_adress !== null ||
                  coordinatesForm.latitude !== null ||
                  this.coordinatesForm.national_adress !== null
                "
              >
                <div id="map">
                  <div
                    id="mapContainer"
                    style="height: 600px; width: 100%"
                    ref="mapContainer"
                  ></div>

                  <Input
                    hidden
                    type="text"
                    v-model="coordinatesForm.latitude"
                    id="latitude"
                  />
                  <Input
                    hidden
                    type="text"
                    v-model="coordinatesForm.longitude"
                    id="longitude"
                  />
                  <Input
                    hidden
                    type="text"
                    v-model="coordinatesForm.location"
                    id="location"
                  />
                </div>
              </li>
            </ul>
          </form>
        </section>
      </div>

      <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
        <section class="border-b border-gray-200 pb-4">
          <form @submit.prevent="updateData" class="mt-4">
            <h2 class="text-gray-700 text-lg mb-4 font-semibold">
              {{ language.data }}
            </h2>
            <ul class="mt-4 space-y-4">
              <li>
                <Button type="submit">{{ language.save_data }}</Button>
              </li>
              <li class="text-gray-600">
                <Label :value="language.the_name" />
                <Input
                  id="name"
                  type="text"
                  v-model="dataForm.name"
                  :error="dataForm.errors.name"
                  class="mt-2 block w-full"
                  required
                  :placeholder="language.the_name"
                />
                <InputError :message="dataForm.errors.name" />
              </li>
              <li class="text-gray-600">
                <Label :value="language.phone_number" />
                <Input
                  id="phone"
                  type="text"
                  v-model="dataForm.phone"
                  :error="dataForm.errors.phone"
                  class="mt-2 block w-full"
                  required
                  :placeholder="language.phone"
                />
                <InputError :message="dataForm.errors.phone" />
              </li>
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
            </ul>
          </form>
        </section>
        <br />
        <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
          <section class="border-b border-gray-200 pb-4 spaxe-y-4">
            <span class="inline-flex items-center">
              {{ language.door_photo + " " + "(" + language.optional + ")" }}
            </span>
            <div class="flex-1 flex items-center justify-between w-full h-full">
              <form
                @submit.prevent="update_file_form"
                ref="form"
                class="mt-4 w-full h-full space-y-3"
              >
                <Button v-show="file_Form.door_photo">{{ language.save_photo }}</Button>
                <Label for="door_photo" class="w-full h-full">
                  <input
                    class="w-full h-full"
                    id="door_photo"
                    ref="door_photo"
                    type="file"
                    accept="image/*"
                    hidden
                    @change="onFileChange"
                    @input="file_Form.door_photo = $event.target.files[0]"
                    autofocus
                  />

                  <div class="flex flex-col items-center h-full w-full">
                    <img
                      v-show="receiver.door_photo && !file_Form.door_photo"
                      class="object-contain h-full w-full rounded"
                      :src="`/uploads/${receiver.door_photo}`"
                    />

                    <img
                      v-show="file_Form.door_photo"
                      class="object-contain h-full w-full rounded"
                      :src="imageUrl"
                    />

                    <div class="flex items-center mt-2">
                      <svg
                        v-show="!showTrash"
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

                      <button v-show="showTrash" @click="remove_door_photo">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                          class="w-6 h-6"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>
                </Label>
              </form>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Button from "@/Components/Button.vue";
import { Head } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import BackButton from "../../../Components/backButton.vue";
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
    BackButton,
    Label,
    searchableSelect,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    receiver: Object,
    language: Object,
    cities: Object,
  },
  computed: {
    showTrash: function () {
      if (this.receiver.door_photo) {
        return true;
      } else {
        return false;
      }
    },

    computedCities: function () {
      let cities = [];

      cities = Object.values(this.cities).map((city) => {
        if (this.$page.props.locale === "en") {
          return city.name_en;
        } else if (this.$page.props.locale === "ar") {
          return city.name;
        }
      });

      return cities;
    },
  },
  beforeMount() {
    this.init_map_data(this.receiver.location_data);
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

      this.dataForm.national_address = this.extractValue(this.coordinatesForm.location);
    } catch (error) {
      console.error("Error loading HERE Maps scripts:", error);
    }
    this.initializeHereMap();
  },
  data() {
    return {
      platform: null,
      imageUrl: "",
      apikey: "-IqF16CutqVTBnUevvdhnE1aQ7-qfgBDeYwSL97Kv5U",
      dataForm: this.$inertia.form({
        name: this.receiver.name,
        phone: this.receiver.phone,
        id_number: this.receiver.id_number,
      }),
      coordinatesForm: this.$inertia.form({
        city: this.receiver.city,
        neighborhood: this.receiver.neighborhood,
        street: this.receiver.street,
        door_number: this.receiver.door_number,
        national_adress: this.receiver.national_adress,
        latitude: null,
        longitude: null,
        location: null,
      }),
      file_Form: this.$inertia.form({
        door_photo: null,
      }),
    };
  },

  methods: {
    async init_map_data(data_to_parse) {
      if (data_to_parse && data_to_parse !== null) {
        const locationData = JSON.parse(data_to_parse);
        if (locationData && locationData !== null) {
          this.coordinatesForm.latitude = locationData.latitude;
          this.coordinatesForm.longitude = locationData.longitude;
          this.coordinatesForm.location = this.receiver.location_adress;
        }
      }
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
    extractValue(address) {
      const regex = /([A-Za-z]{4}\d{4})/; // Regular expression to match 4 characters followed by 4 digits
      const match = regex.exec(address);
      if (match && match[1]) {
        return match[1];
      }
      return "";
    },
    remove_door_photo() {
      this.file_Form.door_photo = null;
    },
    onFileChange(e) {
      if (e.target.files[0]) {
        const file = e.target.files[0];
        this.imageUrl = URL.createObjectURL(file);
      }
    },
    async initializeHereMap() {
      if (this.coordinatesForm.national_adress !== null) {
        try {
          // Call the HERE Geocoding API to convert address to coordinates
          const response = await fetch(
            `https://geocode.search.hereapi.com/v1/geocode?q=${encodeURIComponent(
              this.coordinatesForm.national_adress
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

      const container = this.$refs.mapContainer; // Update the container reference

      if (container) {
        container.innerHTML = "";
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
          }, // set center to Riyadh, Saudi Arabia
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

        /*   function closeFS() {
          if (document.exitFullscreen) {
            document.exitFullscreen();
          } else if (document.webkitExitFullscreen) {
            // Safari
            document.webkitExitFullscreen();
          } else if (document.msExitFullscreen) {
            // IE11
            document.msExitFullscreen();
          }
        }
*/

        // Add event listener to map for capturing click event
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

          this.coordinatesForm.latitude = coord.lat;
          this.coordinatesForm.longitude = coord.lng;
          this.coordinatesForm.location = address;
          this.coordinatesForm.national_adress = this.extractValue(address);
        });
      } else {
        console.error("Container element not found.");
        return;
      }
    },

    updateData() {
      this.dataForm.post(this.route("receivers.update-data", this.receiver), {
        forceFormData: true,
      });
    },
    async updateCoordinates() {
      await this.initializeHereMap();

      this.coordinatesForm.post(
        this.route("receivers.update-coordinates", this.receiver),
        {
          forceFormData: true,
        }
      );
    },
    update_file_form() {
      this.file_Form.post(this.route("receivers.update-door-photo", this.receiver), {
        forceFormData: true,
      });

      // Reset the file input field
      this.$refs.door_photo.value = null;
    },
  },
};
</script>
