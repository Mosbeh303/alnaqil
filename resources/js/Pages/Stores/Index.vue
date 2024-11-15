<template>
  <Head :title="language.clients" />

  <div>
    <page-header :button="{
            text: language.add,
            href: route('stores.create'),
            enable: ($page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('create_stores'))
        }">
      <span> {{ language.clients }} </span>
    </page-header>

    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white lg:m-7 m-4 overflow-hidden"
    >
      <flash-messages class="mx-auto" />
      <!-- ***************** -->

      <div class="flex justify-between items-baseline md:flex-row flex-col">
        <div class="flex justify-start gap-3">
          <h2
            v-if="form.status == 'approved'"
            class="text-gray-700 text-lg mb-4 font-semibold"
          >
            {{ language.activated_clients }}
          </h2>
          <h2
            v-else-if="form.status != 'approved' && form.status != null"
            class="text-gray-700 text-lg mb-4 font-semibold"
          >
            {{ language.the_notActivated_clients }}
          </h2>
          <h2 v-else class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.all_clients }}
          </h2>
          <span class="flex px-3 font-bold text-gray-600"
            ><Icon name="users" class="w-5 h-5 text-gray-500 relative bottom-1 ml-1" />
            {{ stores.total }}
          </span>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
            <select-input
              v-model="form.status"
              :selectPlaceholder="language.client_filtering"
            >
              <option :value="null">{{ language.all_clients }}</option>
              <option value="approved">{{ language.the_activated_clients }}</option>
              <option value="disapproved">{{ language.the_notActivated_clients }}</option>
            </select-input>
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
                      {{ language.store_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.Store_name }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.phone_number }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.Store_owner_name }}
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.city }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.Neighborhood }}</th>
                    <th class="pb-4 pt-6 px-6" v-if="$page.props.auth.account != 'marketer'">
                      {{ language.number_of_shipments }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.type }}
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.Status }}</th>
                    <th class="pb-4 pt-6 px-6"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="store in stores.data"
                    :key="store.id"
                    class="hover:bg-gray-100 whitespace-nowrap"
                  >
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="route('stores.show', store)"
                      >
                        {{ store.id }}
                        <!--<icon v-if="contact.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />-->
                      </Link>
                    </td>
                    <td class="border-t text-center">
                      {{ store.name }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.phone }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.ownerName }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.city }}
                    </td>
                    <td class="border-t text-center">
                      {{ store.neighborhood }}
                    </td>
                    <td class="border-t text-center" v-if="$page.props.auth.account != 'marketer'">
                      <Link :href="route('stores.shipments', store)">
                        {{ store.shipments }}
                      </Link>
                    </td>

                    <td class="text-center border-t px-2">
                      <p
                        v-if="store.type == 'platform'"
                      >
                        {{ language.platform }}
                      </p>

                      <p
                        v-else-if="store.type == 'individual'"
                      >
                        {{ language.individual }}
                      </p>

                      <p
                        v-else
                      >
                        {{ language.shop }}
                      </p>
                    </td>

                    <td class="text-center border-t">
                      <p
                        class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs m-auto"
                        v-if="store.status == 'approved'"
                      >
                        {{ language.activated }}
                      </p>

                      <p
                        class="rounded-xl text-red-700 bg-red-200 py-1 px-4 w-fit text-xs m-auto"
                        v-else
                      >
                        {{ language.not_activated }}
                      </p>
                    </td>

                    <td class="border-t">
                      <div class="flex justify-center" v-if="$page.props.auth.account != 'marketer'">
                        <Toggle

                          :value="approved(store.status)"
                          @inputclick="changeStoreStatus(store, $event)"
                        />

                        <Link
                          class="flex justify-center pr-2 pl-4"
                          :href="route('stores.edit', store)"
                          tabindex="-1"
                        >
                          <icon name="edit" class="block w-5 h-5 text-gray-500" />
                        </Link>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="stores.data.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.no_clients }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <pagination class="mt-6" :links="stores.links" />
    </div>

    <Modal :show="showAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.are_you_sure }}
      </h2>
      <p v-if="targetOperatorStatus" class="text-base text-gray-600">
        {{ language.target_operator_status }}
      </p>
      <p v-else class="text-base text-gray-600">
        {{ language.do_you_want_to_the_delegate_account }}
      </p>
      <Button
        class="mt-4 !border"
        :href="alertLink"
        method="post"
        @click="showAlert = false"
      >
        <span v-if="targetOperatorStatus">{{ language.stop }}</span>
        <span v-else>{{ language.allow }}</span>
      </Button>
      <Button
        @click="showAlert = false"
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
    stores: Object,
    language: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        status: this.filters.status,
      },
      showAlert: false,
      alertLink: "",
      targetOperatorStatus: 0,
    };
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/stores", pickBy(this.form), {
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
    changeStoreStatus(store, event) {
      this.$inertia.post(
        `/dashboard/stores/${store.id}/update-status`,
        pickBy(this.form),
        {
          preserveScroll: true,
        }
      );
      // this.showAlert = true;
      console.log("done!");
    },
    approved(status) {
      if (status == "approved") return true;
      else return false;
    },
  },
};
</script>
