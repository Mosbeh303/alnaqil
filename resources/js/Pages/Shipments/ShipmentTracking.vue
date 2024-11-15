<template>
  <div>
    <Head :title="language.track_the_shipment" />

    <page-header> {{ language.shipment_no }} {{ number }} </page-header>

    <div class="grid gap-7 grid-cols-1 md:m-7 m-4">
      <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
        <div class="flex justify-between">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.track_the_shipment }}
          </h2>

          <a
            v-if="tracks.length != 0"
            class="inline-flex items-center px-4 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-700 hover:text-white active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
            :href="route('shipments.getShipmentTrackingPdf', number)"
            >{{ language.print }}</a
          >
        </div>

        <!-- component -->

        <div v-if="tracks.length == 0">
          <p class="">{{ language.sorry_no_data_yet }}</p>
        </div>
        <div class="w-10/12 md:w-7/12 lg:6/12 mx-auto relative py-20">
          <div class="border-r-2 mt-10">
            <!-- Card 1 -->
            <div
              v-for="track in tracks"
              :key="track.id"
              class="transform transition cursor-pointer hover:-translate-y-2 mr-10 relative flex items-center px-6 py-4 bg-gray-600 text-white rounded mb-10 flex-col md:flex-row space-y-4 md:space-y-0"
            >
              <!-- Dot Follwing the Left Vertical Line -->
              <div
                class="w-5 h-5 bg-gray-800 absolute -right-10 transform translate-x-2/4 rounded-full z-10 mt-2 md:mt-0"
              ></div>

              <!-- Line that connecting the box with the vertical line -->
              <div class="w-10 h-1 bg-gray-300 absolute -right-10 z-0"></div>

              <!-- Content that showing in the box -->
              <div class="flex-auto">
                <p class="text-base mb-2">
                  {{ track.created_at }}
                </p>
                <h2 v-if="track.action == 'update_status'" class="text-xl font-bold">
                  {{ action[statuses[track.status]] }}
                </h2>
                <h2 v-else class="text-xl font-bold">
                  {{ action[track.action] }}
                </h2>

                <h3 class="mt-2" v-if="track.action == 'update'">
                  <p
                    v-for="(change, name) in track.changes"
                    :key="name"
                    class="block my-2"
                  >
                    <span
                      class="block"
                      v-if="
                        track.original != null &&
                        parseFloat(change) != parseFloat(track.original[name]) &&
                        name !== 'updated_at' &&
                        name !== 'exchange' &&
                        name !== 'refrigerated' &&
                        name !== 'return_back' &&
                        name !== 'exchange_fee' &&
                        name !== 'refrigerated_transport_fee' &&
                        name !== 'extra_fees' &&
                        name !== 'cod_fee'
                      "
                    >
                      {{ language.been_modified }}
                      <span class="text-blue-400">{{ fields[name] }}</span>
                      {{ language.from }} "{{ track.original[name] }}"
                      {{ language.to }} "{{ change }}"
                    </span>

                    <span
                      class="block"
                      v-else-if="
                        track.original != null &&
                        parseFloat(change) > 0 &&
                        parseFloat(change) != parseFloat(track.original[name]) &&
                        (name == 'exchange_fee' ||
                          name == 'refrigerated_transport_fee' ||
                          name == 'extra_fees')
                      "
                    >
                      {{ language.has_been_activated }}
                      <span class="text-blue-400">{{ fields[name] }}</span>
                    </span>

                    <span
                      class="block"
                      v-else-if="
                        track.original != null &&
                        parseFloat(change) == 0 &&
                        parseFloat(change) != parseFloat(track.original[name]) &&
                        (name == 'exchange_fee' ||
                          name == 'refrigerated_transport_fee' ||
                          name == 'extra_fees')
                      "
                    >
                      {{ language.has_been_canceled }}
                      <span class="text-blue-400">{{ fields[name] }}</span>
                    </span>
                  </p>
                  {{ language.by }}
                  <span class="font-bold">
                    {{ track.userFullName }}
                  </span>
                </h3>
                <h3 class="mt-2" v-else-if="track.action == 'update_operator'">
                  {{ description[track.action] }}
                  {{ track.operator }}{{ language.by_shipment_by }}
                  {{ track.userFullName }}
                </h3>
                <h3 class="mt-2" v-else-if="track.action == 'update_status'">
                  {{ description[statuses[track.status]] }}
                  {{ track.userFullName }}
                </h3>
                <h3 class="mt-2" v-else>
                  {{ description[track.action] }}
                  {{ track.userFullName }}
                </h3>
              </div>
              <a href="#" class="text-center text-white hover:text-gray-300 underline">{{
                track.username
              }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";

export default {
  components: {
    Head,
    PageHeader,
    Button,

    Link,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    tracks: Object,
    number: [Number, String],
    action: [Array, Object],
    description: [Array, Object],
    statuses: [Array, Object],
    fields: [Array, Object],
    language: Object,
  },

  methods: {
    getAction(a) {
      if (a == "create") return language.the_shipment_has_been_created;
    },
    getMessage(track) {
      if (track.action == "create")
        return the_shipment_has_been_created + " " + track.userFullName;
    },
  },
};
</script>
