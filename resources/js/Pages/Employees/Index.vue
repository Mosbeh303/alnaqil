<template>
  <Head :title="language.employees" />

  <div>
    <page-header
      :button="{
        text: language.add_employee,
        href: route('employee.create', { groupe: form.groupe }),
      }"
    >
      <span> {{ language.employees }} </span>
    </page-header>

    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white md:m-7 m-4 overflow-hidden"
    >
      <flash-messages class="mx-auto" />
      <!-- ***************** -->

      <div class="md:flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            <span v-if="form.groupe == null">{{ language.all_employees }}</span>
            <div v-else class="flex h-full gap-2">
              <span
                >{{ language.group }}
                {{ groupes.find((item) => item.id === parseInt(form.groupe)).name }}
              </span>
              <Link
                class="text-gray-500 w-6 h-6"
                :href="
                  route(
                    'groupes.edit',
                    groupes.find((item) => item.id === parseInt(form.groupe)).id
                  )
                "
                ><Icon name="edit"> </Icon
              ></Link>
            </div>
          </h2>

          <!-- <span class="flex px-3 font-bold text-gray-600"><Icon name="users" class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"  /> {{Operators.total}} </span>-->
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
            <select-input v-model="form.groupe" :selectPlaceholder="language.employee_filtering">
              <option :value="null">{{ language.all_employees }}</option>
              <option v-for="groupe in groupes" :key="groupe.id" :value="groupe.id">
                {{ groupe.name }}
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
                    <th class="pb-4 pt-6 px-6">
                      {{ language.username }}
                    </th>
                    <th class="pb-4 pt-6 px-6">{{ language.the_name }}</th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.email }}
                    </th>
                    <th class="pb-4 pt-6 px-6">
                      {{ language.phone_number }}
                    </th>

                    <th class="pb-4 pt-6 px-6">
                      {{ language.city_ }}
                    </th>

                    <th class="pb-4 pt-6 px-6">
                      {{ language.branch }}
                    </th>

                    <th class="pb-4 pt-6 px-6" v-if="form.type == null">
                      {{ language.group }}
                    </th>
                    <!-- <th class="pb-4 pt-6 px-6">الحالة</th> -->
                    <th class="pb-4 pt-6 px-6"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="employee in employees.data"
                    :key="employee.id"
                    class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
                  >
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4 focus:text-indigo-500"
                        :href="route('employee.edit', employee)"
                      >
                        {{ employee.username }}
                        <!--<icon v-if="contact.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />-->
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4"
                        :href="route('employee.edit', employee)"
                        tabindex="-1"
                      >
                        {{ employee.name }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4"
                        :href="route('employee.edit', employee)"
                        tabindex="-1"
                      >
                        {{ employee.email }}
                      </Link>
                    </td>
                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4"
                        :href="route('employee.edit', employee)"
                        tabindex="-1"
                      >
                        {{ employee.phone }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4"
                        :href="route('employee.edit', employee)"
                        tabindex="-1"
                      >
                        {{ employee.city }}
                      </Link>
                    </td>

                    <td class="border-t">
                      <Link
                        class="flex justify-center px-6 py-4"
                        :href="route('employee.edit', employee)"
                        tabindex="-1"
                      >
                        {{ employee.branch }}
                      </Link>
                    </td>

                    <td class="border-t" v-if="form.type == null">
                      <Link
                        class="flex justify-center px-6 py-4"
                        :href="route('employee.edit', employee)"
                        tabindex="-1"
                      >
                        {{ employee.groupe }}
                      </Link>
                    </td>

                    <td class="w-px border-t">
                      <div class="flex justify-center">
                        <Link
                          class="flex justify-center pr-2 pl-4"
                          :href="route('employee.edit', employee)"
                          tabindex="-1"
                        >
                          <icon name="edit" class="block w-5 h-5 text-gray-500" />
                        </Link>
                        <Button v-if="$page.props.auth.account === 'admin' && group.id !== null" @click="showPermissionsModal(employee)" class="mx-2 bg-transparent border-1 !border-purple-800 !text-purple-800 text-xs hover:bg-transparent !p-2">أضف صلاحيات</Button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="employees.data.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">{{ language.there_are_no_employees }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <pagination class="mt-6" :links="employees.links" />
    </div>

    <Modal :show="showAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">{{ language.are_you_sure }}</h2>
      <p v-if="targetOperatorStatus" class="text-base text-gray-600">
      {{ language.target_operator_status }}
      </p>
      <p v-else class="text-base text-gray-600">{{ language.do_you_want_to_the_delegate_account }}</p>
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

    <Modal :show="permissionsModal"  v-if="$page.props.auth.account === 'admin'">
        <div class="">
            <h2 class="text-lg font-semibold mb-2 text-gray-800">{{ language.add_permissions }}</h2>
            <form @submit.prevent="submit" >
                <accordion
                :isActive="activeDropdown === 'control_panel_characteristics'"
                @click="activeDropdown = 'control_panel_characteristics'"
                :title="language.control_panel_characteristics"
                class="mt-4"
                >
                <div class="grid md:grid-cols-3 pb-2">
                    <label
                    class="flex items-center mt-2"
                    :class="{ 'space-x-2': $page.props.locale == 'en' }"
                    v-for="(permission, i) in permissions.dashboard"
                    :key="i"
                    >
                    <Checkbox
                        v-if="group.permissions.includes(i)"
                        :value="i"
                        checked
                        disabled
                    />
                    <Checkbox
                        v-else
                        name="permissions[]"
                        :value="i"
                        v-model:checked="formPermissions.permissions"
                    />
                    <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
                    </label>
                </div>
                </accordion>
                <accordion
                :isActive="activeDropdown === 'terms_of_reference_for_shipments'"
                @click="activeDropdown = 'terms_of_reference_for_shipments'"
                :title="language.terms_of_reference_for_shipments">
                <div class="grid md:grid-cols-3 p-2">
                    <label
                    class="flex items-center mt-2"
                    :class="{ 'space-x-2': $page.props.locale == 'en' }"
                    v-for="(permission, i) in permissions.shipments"
                    :key="i"
                    >
                    <Checkbox
                        v-if="group.permissions.includes(i)"
                        :value="i"
                        checked
                        disabled
                    />
                    <Checkbox
                        v-else
                        name="permissions[]"
                        :value="i"
                        v-model:checked="formPermissions.permissions"
                    />
                    <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
                    </label>
                </div>
                </accordion>
                <accordion     :isActive="activeDropdown === 'delegates'"
                @click="activeDropdown = 'delegates'" :title="language.delegates">
                    <div class="grid md:grid-cols-3 p-2">
                        <label
                        class="flex items-center mt-2"
                        :class="{ 'space-x-2': $page.props.locale == 'en' }"
                        v-for="(permission, i) in permissions.operators"
                        :key="i"
                        >
                        <Checkbox
                            v-if="group.permissions.includes(i)"
                            :value="i"
                            checked
                            disabled
                        />
                        <Checkbox
                            v-else
                            name="permissions[]"
                            :value="i"
                            v-model:checked="formPermissions.permissions"
                        />
                        <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
                        </label>
                    </div>
                </accordion>

                <accordion     :isActive="activeDropdown === 'employees'"
                @click="activeDropdown = 'employees'" :title="language.employees">
                    <div class="grid md:grid-cols-3 p-2">
                        <label
                        class="flex items-center mt-2"
                        :class="{ 'space-x-2': $page.props.locale == 'en' }"
                        v-for="(permission, i) in permissions.employees"
                        :key="i"
                        >
                        <Checkbox
                            v-if="group.permissions.includes(i)"
                            :value="i"
                            checked
                            disabled
                        />
                        <Checkbox
                            v-else
                            name="permissions[]"
                            :value="i"
                            v-model:checked="formPermissions.permissions"
                        />
                        <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
                        </label>
                    </div>
                </accordion>

                <accordion :title="language.clients"  :isActive="activeDropdown === 'clients'"
                @click="activeDropdown = 'clients'" >
                <div class="grid md:grid-cols-3 p-2">
                    <label
                    class="flex items-center mt-2"
                    :class="{ 'space-x-2': $page.props.locale == 'en' }"
                    v-for="(permission, i) in permissions.stores"
                    :key="i"
                    >
                    <Checkbox
                        v-if="group.permissions.includes(i)"
                        :value="i"
                        checked
                        disabled
                    />
                    <Checkbox
                        v-else
                        name="permissions[]"
                        :value="i"
                        v-model:checked="formPermissions.permissions"
                    />
                    <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
                    </label>
                </div>
                </accordion>

                <accordion :title="language.accounts" :isActive="activeDropdown === 'accounts'"
                @click="activeDropdown = 'accounts'">
                <div class="grid md:grid-cols-3 p-2">
                    <label
                    class="flex items-center mt-2"
                    :class="{ 'space-x-2': $page.props.locale == 'en' }"
                    v-for="(permission, i) in permissions.accounts"
                    :key="i"
                    >
                    <Checkbox
                        v-if="group.permissions.includes(i)"
                        :value="i"
                        checked
                        disabled
                    />
                    <Checkbox
                        v-else
                        name="permissions[]"
                        :value="i"
                        v-model:checked="formPermissions.permissions"
                    />
                    <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
                    </label>
                </div>
                </accordion>

                <accordion :title="language.reports" :isActive="activeDropdown === 'reports'"
                @click="activeDropdown = 'reports'">
                    <div class="grid md:grid-cols-3 p-2">
                        <label
                            class="flex items-center mt-2"
                            :class="{ 'space-x-2': $page.props.locale == 'en' }"
                            v-for="(permission, i) in permissions.reports"
                            :key="i"
                        >
                            <Checkbox
                                v-if="group.permissions.includes(i)"
                                :value="i"
                                checked
                                disabled
                            />
                            <Checkbox
                                v-else
                                name="permissions[]"
                                :value="i"
                                v-model:checked="formPermissions.permissions"
                            />
                            <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
                        </label>
                    </div>
                </accordion>

                <accordion :title="language.marketers" :isActive="activeDropdown === 'marketers'"
                @click="activeDropdown = 'marketers'">
                    <div class="grid md:grid-cols-3 p-2">
                        <label
                            class="flex items-center mt-2"
                            :class="{ 'space-x-2': $page.props.locale == 'en' }"
                            v-for="(permission, i) in permissions.marketers"
                            :key="i"
                        >
                            <Checkbox
                                v-if="group.permissions.includes(i)"
                                :value="i"
                                checked
                                disabled
                            />
                            <Checkbox
                                v-else
                                name="permissions[]"
                                :value="i"
                                v-model:checked="formPermissions.permissions"
                            />
                            <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
                        </label>
                    </div>
                </accordion>

                <accordion :title="language.other" :isActive="activeDropdown === 'other'"
                @click="activeDropdown = 'other'">
                <div class="grid md:grid-cols-3 p-2">
                    <label
                    class="flex items-center mt-2"
                    :class="{ 'space-x-2': $page.props.locale == 'en' }"
                    v-for="(permission, i) in permissions.others"
                    :key="i"
                    >
                    <Checkbox
                        v-if="group.permissions.includes(i)"
                        :value="i"
                        checked
                        disabled
                    />
                    <Checkbox
                        v-else
                        name="permissions[]"
                        :value="i"
                        v-model:checked="formPermissions.permissions"
                    />
                    <span class="mr-2 text-sm text-gray-600"> {{ permission }}</span>
                    </label>
                </div>
                </accordion>

                <Button
                :class="{ 'opacity-25': formPermissions.processing }"
                :disabled="formPermissions.processing"
                class="mt-4 px-6"
                >
                    <span v-if="targetOperatorStatus">{{ language.stop }}</span>
                    <span v-else>{{ language.allow }}</span>
                </Button>
                <Button
                    @click="permissionsModal = false"
                    type="button"
                    class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
                    >{{ language.cancel }}</Button
                >
            </form>
        </div>

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
import Accordion from "@/Components/Accordion.vue";
import Checkbox from "@/Components/Checkbox.vue";


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
    Accordion,
    Checkbox
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    permissions: Object,
    filters: Object,
    employees: Object,
    groupes: Object,
    language: Object,
    group: Object
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        groupe: this.filters.groupe,
      },
      formPermissions: this.$inertia.form({
        permissions: [],
      }),
      showAlert: false,
      alertLink: "",
      targetOperatorStatus: 0,
      permissionsModal : false,
      employee: {},
    };
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/dashboard/employees", pickBy(this.form), {
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
    changeOperatorStatus(operator) {
      this.alertLink = route("operator.update_status", operator);
      this.targetOperatorStatus = operator.allowed;
      this.showAlert = true;
    },
    showPermissionsModal(employee){
        if (Array.isArray(employee.permissions))
            this.formPermissions.permissions = employee.permissions;
        this.employee = employee;
        this.permissionsModal = ! this.permissionsModal
    },
    submit() {
      this.formPermissions.post(this.route("employee.update_permissions", this.employee), {
        preserveScroll: true,
        onError: () => this.formPermissions.reset(),
        onFinish: () => this.formPermissions.reset(""),
      });
      this.permissionsModal = ! this.permissionsModal
    },
  },
};
</script>

<style scoped>
input[type="checkbox"][disabled][checked] {
  filter: invert(100%) hue-rotate(18deg) brightness(5);

}
input[type="checkbox"][disabled] {
  filter: invert(100%) hue-rotate(18deg) brightness(5);
}
</style>
