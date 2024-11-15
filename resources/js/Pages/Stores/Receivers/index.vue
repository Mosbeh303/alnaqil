<template>
  <Head :title="language.receivers_adresses" />
  <page-header>
    {{ language.receivers_adresses }}
  </page-header>
  <div>
    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white lg:m-7 m-4 overflow-hidden"
    >
      <flash-messages class="mx-auto w-full" />
      <!-- ***************** -->

      <div class="flex justify-between items-baseline md:flex-row flex-col">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.receivers }}
          </h2>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
          </search-filter>
        </div>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th class="pb-4 pt-6 px-6">
                      {{ language.id_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.national_adress }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.The_recipients_name }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.phone_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.city_neighborhood }}
                    </th>

                    <th class="pb-4 pt-6 px-6">
                      {{ language.number_of_shipments }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.Rceiver_link }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.actions }}
                    </th>

                    <!--  <th class="pb-4 pt-6 px-6">{{ language.street_name }}</th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.door_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.map_location }}</th>-->
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="receiver in receivers.data"
                    :key="receiver.id"
                    class="hover:bg-gray-100 whitespace-nowrap"
                  >
                    <td class="border-t">
                      <Link
                        :href="route('receivers.show', receiver.id)"
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                      >
                        {{ receiver.id_number }}
                        <!--<icon v-if="contact.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />-->
                      </Link>
                    </td>
                    <td class="border-t text-center">
                      {{ receiver.national_adress }}
                    </td>
                    <td class="border-t text-center">
                      {{ receiver.name }}
                    </td>
                    <td class="border-t text-center">
                      {{ receiver.phone }}
                    </td>
                    <td class="border-t text-center">
                      {{ receiver.city }}
                      <span v-if="receiver.neighborhood"
                        >(
                        {{ receiver.neighborhood }}
                        )</span
                      >
                    </td>

                    <td class="border-t text-center">
                      <Link :href="route('receivers.shipments', receiver)">
                        {{ receiver.shipments }}
                      </Link>
                    </td>
                    <td class="border-t text-center">
                      <div v-if="receiver.link">
                        <div
                          v-if="showAlert && this.currentId == receiver.id"
                          class="bg-gray-200 text-gray-800 rounded-lg p-3"
                        >
                          <span>{{ language.successfully_copied }}</span>
                        </div>
                        <button
                          v-else
                          @click="(currentId = receiver.id), copyLink(receiver.link)"
                        >
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 text-gray-900"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"
                            />
                          </svg>
                        </button>
                      </div>
                    </td>

                    <td class="border-t text-center flex justify-around">
                      <Link
                        class="flex items-center py-4 focus:text-indigo-500"
                        :href="route('receivers.edit', receiver.id)"
                      >
                        <icon name="edit" class="block w-5 h-5 text-gray-900" />
                      </Link>
                      <!-- <Link
                        class="flex items-center py-4 focus:text-indigo-500"
                        :href="
                          route('receivers.admin.create-additional-data', receiver.id)
                        "
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                          class="block w-5 h-5 text-gray-900"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                          />
                        </svg>
                      </Link> -->
                      <button class="cursor-pointer" @click="deleteReceiver(receiver)">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                          class="block w-5 h-5 text-gray-900"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                          />
                        </svg>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="receivers.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.no_receivers }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <pagination class="mt-6" :links="receivers.links" />
    </div>

    <Modal :show="showDeleteReceiverAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.are_you_sure }}
      </h2>
      <p class="text-base text-gray-600">
        {{ language.do_you_want_to_confirm_deletion_of_receiver }}
      </p>

      <Button
        class="mt-4 !border"
        :href="deleteReceiverLink"
        method="post"
        @click="showDeleteReceiverAlert = false"
      >
        <span>{{ language.delete }}</span>
      </Button>
      <Button
        @click="showDeleteReceiverAlert = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </Modal>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import PageHeader from "@/Components/PageHeader.vue";
import { Head, Link } from "@inertiajs/vue3";
import Icon from "@/Components/Icon.vue";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import SearchFilter from "@/Components/SearchFilter.vue";
import Pagination from "@/Components/Pagination.vue";
import Modal from "@/Components/Modal.vue";
import Button from "@/Components/Button.vue";
import FlashMessages from "@/Components/FlashMessages.vue";

export default {
  components: {
    Head,
    PageHeader,
    Icon,
    Link,
    SearchFilter,
    Pagination,
    Modal,
    Button,
    FlashMessages,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    receivers: Object,
    language: Object,
    filters: Object,
  },
  data() {
    return {
      currentId: 0,
      showAlert: false,
      deleteReceiverLink: "",
      showDeleteReceiverAlert: false,

      form: {
        search: this.filters.search,
      },
    };
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/receivers", pickBy(this.form), {
          preserveState: true,
          preserveScroll: true,
        });
      }, 150),
    },
  },

  methods: {
    async copyLink(link) {
      try {
        await navigator.clipboard.writeText(link);

        this.showAlert = true;

        // Hide the alert after a certain duration
        setTimeout(() => {
          this.showAlert = false;
        }, 1000); // Change the duration as per your requirement
      } catch ($e) {
        console.log($e);
      }
    },
    deleteReceiver(receiver) {
      this.deleteReceiverLink = route("receivers.delete", receiver);
      this.showDeleteReceiverAlert = true;
    },
  },
};
</script>
