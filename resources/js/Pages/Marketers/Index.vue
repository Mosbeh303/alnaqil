<template>
    <Head :title="language.marketers" />

    <div>
      <page-header :button="{
              text: language.add,
              href: route('marketers.create'),
              enable: ($page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('create_marketers'))
          }">
        <span> {{ language.marketers }} </span>
      </page-header>

      <div
        class="border rounded border-gray-200 p-3 sm:p-5 bg-white lg:m-7 m-4 overflow-hidden"
      >
        <flash-messages class="mx-auto w-full" />
        <!-- ***************** -->

        <div class="flex justify-between items-baseline md:flex-row flex-col">
          <div class="flex justify-start gap-3">

            <h2 class="text-gray-700 text-lg mb-4 font-semibold">
              {{ language.all_marketers }}
            </h2>
            <span class="flex px-3 font-bold text-gray-600"
              ><Icon name="users" class="w-5 h-5 text-gray-500 relative bottom-1 ml-1" />
              {{ marketers.total }}
            </span>
          </div>

          <div class="flex items-center justify-between mb-6">
            <search-filter
              v-model="form.search"
              class="mr-4 w-full max-w-md"
              @reset="reset"
            >
              <!-- <select-input
                v-model="form.status"
                :selectPlaceholder="language.client_filtering"
              >
                <option :value="null">{{ language.all_clients }}</option>
                <option value="approved">{{ language.the_activated_clients }}</option>
                <option value="disapproved">{{ language.the_notActivated_clients }}</option>
              </select-input> -->
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
                        {{ language.marketer_number }}
                      </th>
                      <th class="pb-4 pt-6 px-6">
                        {{ language.name }}
                      </th>
                      <th class="pb-4 pt-6 px-6">
                        {{ language.phone_number }}
                      </th>
                      <th class="pb-4 pt-6 px-6">
                        {{ language.commisson }}
                      </th>
                      <th class="pb-4 pt-6 px-6">
                        {{ language.dues }}
                      </th>

                      <th class="pb-4 pt-6 px-6">
                        {{ language.number_of_shipments }}
                      </th>

                      <th class="pb-4 pt-6 px-6"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="marketer in marketers.data"
                      :key="marketer.id"
                      class="hover:bg-gray-100 whitespace-nowrap"
                    >
                      <td class="border-t">
                        <Link
                          class="flex justify-center px-6 py-4 focus:text-indigo-500"
                          :href="route('marketers.show', marketer)"
                        >
                          {{ marketer.id }}
                        </Link>
                      </td>
                      <td class="border-t text-center">
                        {{ marketer.name }}
                      </td>
                      <td class="border-t text-center">
                        {{ marketer.phone }}
                      </td>
                      <td class="border-t text-center">
                        {{ marketer.commission }}
                        <span v-if="marketer.commission_type == 'percentage'"> % </span>
                        <span v-else> {{ language.riyal }} </span>
                      </td>
                      <td class="border-t text-center">
                        {{ marketer.dues }}
                      </td>

                      <td class="border-t text-center">
                        <!-- <Link :href="route('stores.shipments', store)"> -->
                          {{ marketer.shipments }}
                        <!-- </Link> -->
                      </td>

                      <td class="border-t">
                        <div class="flex justify-center">
                          <Link
                            class="flex justify-center pr-2 pl-4"
                            :href="route('marketers.edit', marketer)"
                            tabindex="-1"
                          >
                            <icon name="edit" class="block w-5 h-5 text-gray-500" />
                          </Link>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="marketers.data.length === 0">
                      <td class="px-6 py-4 border-t" colspan="4">
                        {{ language.no_marketers }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <pagination class="mt-6" :links="marketers.links" />
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
  import Modal from "@/Components/Modal.vue";
  import FlashMessages from "@/Components/FlashMessages.vue";
  import Toggle from "@/Components/Toggle.vue";

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
      Modal,
      FlashMessages,
      Toggle,
    },
    layout: BreezeAuthenticatedLayout,
    props: {
      filters: Object,
      marketers: Object,
      language: Object,
    },
    data() {
      return {
        form: {
          search: this.filters.search,
        },
      };
    },
    watch: {
      form: {
        deep: true,
        handler: throttle(function () {
          this.$inertia.get("/dashboard/marketers", pickBy(this.form), {
            preserveState: true,
            preserveScroll: true,
          });
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
