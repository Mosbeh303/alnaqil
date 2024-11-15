<template>
  <Head :title="language.employees" />

  <div>
    <page-header
      :button="{
        text: language.add_a_car,
        href: `${route('vehicles.create')}?group=${form.group}`,
      }"
    >
      <span> {{ language.cars }} </span>
    </page-header>

    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white md:m-7 m-4 overflow-hidden"
    >
      <flash-messages class="mx-auto w-full" />
      <!-- ***************** -->

      <div class="md:flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            <span v-if="form.group == null">{{ language.all_cars }}</span>
            <div v-else class="flex h-full gap-2">
              <span
                >{{ language.group }}
                {{ groups.find((item) => item.id === parseInt(form.group)).name }}
              </span>
              <Link
                class="text-gray-500 w-6 h-6"
                :href="
                  route(
                    'vehicles.groups.edit',
                    groups.find((item) => item.id === parseInt(form.group)).id
                  )
                "
                ><Icon name="edit"> </Icon
              ></Link>
            </div>
          </h2>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
            <select-input v-model="form.group" :selectPlaceholder="language.car_filter">
              <option :value="null">{{ language.all_cars }}</option>
              <option v-for="group in groups" :key="group.id" :value="group.id">
                {{ group.name }}
              </option>
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
                    <th class="pb-4 pt-6 px-6">{{ language.car_name }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.car_brand }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.car_number }}</th>

                    <th class="pb-4 pt-6 px-6">{{ language.car_number_on_th_back }}</th>

                    <th class="pb-4 pt-6 px-6">{{ language.the_employee_delegate }}</th>

                    <th class="pb-4 pt-6 px-6" v-if="form.group == null">
                      {{ language.group }}
                    </th>
                    <th class="pb-4 pt-6 px-6"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="vehicle in vehicles.data"
                    :key="vehicle.id"
                    class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center"
                  >
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="route('vehicles.edit', vehicle)"
                      >
                        {{ vehicle.name }}
                      </Link>
                    </td>
                    <td class="border-t">
                      {{ vehicle.mark }}
                    </td>
                    <td class="border-t">
                      {{ vehicle.plate_number }}
                    </td>
                    <td class="border-t">
                      {{ vehicle.back_plate_number }}
                    </td>

                    <td class="border-t">
                      <span
                        class="text-xs text-green-500"
                        v-if="vehicle.userType == 'operator'"
                        >{{ language.the_delegate }} :</span
                      >
                      <span
                        class="text-xs text-green-500"
                        v-else-if="vehicle.userType == 'employee'"
                      >
                        {{ language.employee }} :
                      </span>
                      {{ vehicle.user }}
                    </td>

                    <td class="border-t" v-if="form.group == null">
                      {{ vehicle.group }}
                    </td>

                    <td class="w-px border-t">
                      <div
                        class="flex justify-center"
                        v-if="
                          $page.props.auth.account == 'admin' ||
                          $page.props.auth.permissions.includes('edit_vehicles')
                        "
                      >
                        <Link
                          class="flex justify-center pr-2 pl-4"
                          :href="route('vehicles.edit', vehicle)"
                          tabindex="-1"
                        >
                          <icon name="edit" class="block w-5 h-5 text-gray-500" />
                        </Link>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="vehicles.data.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.there_are_no_cars }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <pagination class="mt-6" :links="vehicles.links" />
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
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    filters: Object,
    vehicles: Object,
    groups: Object,
    language: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        group: this.filters.group,
      },
    };
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/vehicles", pickBy(this.form), {
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
