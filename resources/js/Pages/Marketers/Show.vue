<template>
  <div>
    <Head :title="language.marketer_file" />

    <page-header>
      <p>{{ language.marketer_file }} ({{ marketer.name }})</p>
    </page-header>

    <flash-messages class="md:m-7 m-4" />
    <!-- Top Stats Widgets -->
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 my-7">
        <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">
            <Widget
                color="text-green-500"
                iconName="completed"
                :widgetText="language.shipments"
                :widgetValue="stats.shipments_number"
            />
            <Widget
                color="text-orange-500"
                iconName="users"
                :widgetText="language.clients"
                :widgetValue="stats.stores_number"
            />

            <Widget
                color="text-red-500"
                iconName="cash"
                :widgetText="language.dues"
                :widgetValue="`${stats.dues}  ` + language.riyal"
            />
        </div>
    </div>

    <Section class="md:m-7 m-4">
      <div class="flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            <span class="flex items-center">
              {{ language.commissions }}
              <span class="flex px-3 font-bold text-gray-600">
                <Icon
                  name="shipment"
                  class="w-5 h-5 text-gray-500 relative bottom-1 ml-1"
                />
                {{ commissions.total }}
              </span>
            </span>
          </h2>
        </div>

        <div class="flex items-center justify-between mb-6">
            <!-- <search-filter
              v-model="form.search"
              class="mr-4 w-full max-w-md"
              @reset="reset"
            ></search-filter> -->
            <Button :href="route('marketers.commissions', marketer.id)">{{language.more_details}}</Button>
          </div>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full">
          <thead class="border-b bg-gray-100 text-sm">
            <tr class="whitespace-nowrap">
              <th class="pb-4 pt-6 px-6">{{ language.shipment_number }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.delivery_fee }}</th>
              <th class="pb-4 pt-6 px-6">
                {{ language.commission }} ({{ language.riyal }})
              </th>
              <!-- <th class="pb-4 pt-6 px-6" >
                          {{ language.Status }}
                        </th> -->
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="commission in commissions.data"
              :key="commission.id"
              class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center py-2"
            >
              <td class="border-t py-2">
                {{ commission.shipment.number }}
              </td>
              <td class="border-t px-4">
                {{ commission.shipment.fees }}
              </td>
              <td class="border-t px-4">
                {{ commission.amount }}
              </td>
            </tr>
            <tr v-if="commissions.data.length === 0">
              <td class="px-6 py-4 border-t" colspan="4">
                {{ language.no_commissions }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </Section>

    <Section class="md:m-7 m-4">
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
                    <th class="pb-4 pt-6 px-6">
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
                    <td class="border-t text-center">
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
    </Section>

    <Section class="md:m-7 m-4">
      <div class="flex justify-between items-baseline">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.receipts_of_receivables }}
          </h2>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="mr-4 w-full max-w-md"
            @reset="reset"
          >
          </search-filter>
          <Button
            type="button"
            @click="showAddVoucher = !showAddVoucher"
            class="!bg-gray-200 !text-gray-600 border-0 mr-2 whitespace-nowrap"
            >{{ language.add_payment }}
          </Button>
        </div>
      </div>

      <div v-show="showAddVoucher" class="mb-7">
        <h3 class="text-base font-semibold text-gray-600">
          {{ language.add_payment }}
        </h3>
        <form @submit.prevent="submitAddVoucherForm" class="mt-2 l">
          <div class="flex gap-4 md:flex-row flex-co">
            <div class="w-full">
              <SelectInput
                v-model="formAddVoucher.type"
                :error="formAddVoucher.errors.type"
                :selectPlaceholder="language.bond_type"
              >
                <option value="-">{{ language.catch }}</option>
                <option value="+">{{ language.cashing }}</option>
              </SelectInput>
            </div>

            <div class="w-full">
              <Input
                id="amount"
                type="number"
                step="0.01"
                v-model="formAddVoucher.amount"
                :error="formAddVoucher.errors.amount"
                class="block w-full"
                :placeholder="language.the_amount"
              />

              <InputError :message="formAddVoucher.errors.amount" />
            </div>

            <SelectInput
              v-model="formAddVoucher.wallet"
              :error="formAddVoucher.errors.wallet"
              :selectPlaceholder="language.bank"
            >
              <option
                v-for="wallet in wallets"
                :key="wallet.id"
                :value="wallet.id"
              >
                {{ wallet.name }}
              </option>
            </SelectInput>

            <Input
              v-if="formAddVoucher.type == '-'"
              id="bank"
              type="text"
              v-model="formAddVoucher.bank"
              :error="formAddVoucher.errors.bank"
              class="block w-full h-fit"
              :placeholder="language.from_the_bank"
            />

            <Input
              v-else
              id="bank"
              type="text"
              v-model="formAddVoucher.bank"
              :error="formAddVoucher.errors.bank"
              class="block w-full h-fit"
              :placeholder="language.to_the_bank"
            />

            <Input
              id="notice"
              type="text"
              v-model="formAddVoucher.notice"
              :error="formAddVoucher.errors.notice"
              class="block w-full h-fit"
              :placeholder="language.notes"
            />
            <InputError :message="formAddVoucher.errors.notice" />

          </div>

          <Button class="whitespace-nowrap h-fit justify-center mt-4">
            {{ language.add_payment }}</Button
          >
        </form>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full">
          <thead class="border-b bg-gray-100 text-sm">
            <tr class="whitespace-nowrap">
              <th class="pb-4 pt-6 px-6">#</th>
              <th class="pb-4 pt-6 px-6">{{ language.the_bond }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.the_amount }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.date }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.from_the_bank }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.to_the_bank }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.employee }}</th>
              <th class="pb-4 pt-6 px-6">{{ language.notes }}</th>
              <th class="pb-4 pt-6 px-6"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(voucher, index) in vouchers.data"
              :key="index"
              class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center"
            >
              <td class="border-t py-4">
                {{ voucher.number }}
              </td>
              <td class="border-t py-4">
                <Link :href="route('vouchers.show', voucher)">
                  {{ voucher.amount < 0 ? language.catch : language.cashing }}
                </Link>
              </td>
              <td class="border-t">
                {{ Math.abs(voucher.amount) }}
              </td>
              <td class="border-t">
                {{ voucher.date }}
              </td>
              <td class="border-t">
                <span v-if="voucher.amount < 0">{{ voucher.to_bank }}</span>
                <span v-else>{{ voucher.bank.name }}</span>
              </td>
              <td class="border-t">
                <span v-if="voucher.amount > 0">{{ voucher.to_bank }}</span>
                <span v-else>{{ voucher.bank.name }}</span>
              </td>
              <td class="border-t">
                <span v-if="voucher.employee !== null">{{
                  voucher.employee.name
                }}</span>
              </td>
              <td class="border-t">
                {{ voucher.notice }}
              </td>



              <td class="border-t">
                <div class="flex justify-center">
                  <div
                    class="flex justify-center px-2"
                    @click="editVoucher(voucher)"
                  >
                    <icon name="edit" class="block w-5 h-5 text-gray-500" />
                  </div>

                  <div
                    class="flex justify-center px-2"
                    @click="deleteVoucher(voucher)"
                  >
                    <icon name="trash" class="block w-5 h-5 text-gray-500" />
                  </div>

                  <Link
                    class="flex justify-center px-2"
                    :href="route('vouchers.show', voucher)"
                  >
                    <icon name="eye" class="block w-5 h-5 text-gray-500" />
                  </Link>
                </div>
              </td>
            </tr>
            <tr v-if="vouchers.data.length === 0">
              <td class="px-6 py-4 border-t" colspan="4">
                {{ language.there_are_no_receipts }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </Section>

    <Modal :show="showDeleteVoucherAlert">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.are_you_sure }}
      </h2>
      <p class="text-base text-gray-600">
        {{ language.do_you_want_to_confirm_deletion_of_the_receipt }}
      </p>

      <Button
        class="mt-4 !border"
        :href="deleteVoucherLink"
        method="post"
        @click="showDeleteVoucherAlert = false"
        preserve-scroll
      >
        <span>{{ language.delete }}</span>
      </Button>
      <Button
        @click="showDeleteVoucherAlert = false"
        type="button"
        class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
        >{{ language.cancel }}</Button
      >
    </Modal>

    <Modal :show="showEditVoucherModel">
      <h2 class="text-lg font-semibold mb-2 text-gray-800">
        {{ language.modify_the_receipt }}
      </h2>

      <form @submit.prevent="submitEditVoucherForm" class="w-full">
        <SelectInput
          v-model="formEditVoucher.type"
          :error="formEditVoucher.errors.type"
          :selectPlaceholder="language.bond_type"
        >
          <option value="-">{{ language.catch }}</option>
          <option value="+">{{ language.cashing }}</option>
        </SelectInput>
        <Input
          id="amount"
          type="number"
          step="0.01"
          v-model="formEditVoucher.amount"
          :error="formEditVoucher.errors.amount"
          class="block w-full mt-3"
          :placeholder="language.the_amount"
        />
        <InputError :message="formEditVoucher.errors.amount" />

        <div class="mt-3">
          <SelectInput
            v-model="formEditVoucher.wallet"
            :error="formEditVoucher.errors.wallet"
            :selectPlaceholder="language.bank"
          >
            <option
              v-for="wallet in wallets"
              :key="wallet.id"
              :value="wallet.id"
            >
              {{ wallet.name }}
            </option>
          </SelectInput>
        </div>

        <Input
          id="bank"
          type="text"
          v-model="formEditVoucher.bank"
          :error="formEditVoucher.errors.bank"
          class="block w-full h-fit mt-3"
          :placeholder="language.bank"
        />
        <Input
          id="name"
          type="text"
          v-model="formEditVoucher.notice"
          :error="formEditVoucher.errors.notice"
          class="block w-full mt-3"
          :placeholder="language.notes"
        />
        <InputError :message="formEditVoucher.errors.notice" />

        <Button class="mt-4 !border"> {{ language.modify }} </Button>
        <Button
          @click="showEditVoucherModel = false"
          type="button"
          class="!bg-transparent !border !text-gray-600 !border-gray-400 !mr-3"
          >{{ language.cancel }}</Button
        >
      </form>
    </Modal>
  </div>
</template>

  <script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Section from "@/Components/Section.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import SearchFilter from "@/Components/SearchFilter.vue";
import Icon from "@/Components/Icon.vue";
import Modal from "@/Components/Modal.vue";
import Toggle from "@/Components/Toggle.vue";
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import Checkbox from "@/Components/Checkbox";
import Widget from "../Dashboard/Components/Widget.vue";



export default {
  components: {
    Head,
    Link,
    PageHeader,
    Button,
    Input,
    Section,
    BreezeValidationErrors,
    FlashMessages,
    SelectInput,
    InputError,
    SearchFilter,
    Modal,
    Icon,
    Toggle,
    Checkbox,
    Widget
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    marketer: Object,
    stats: Object,
    commissions: Object,
    vouchers: Object,
    stores: Object,
    wallets: Object,
    filters: Object,
    language: Object,
  },

  data() {
    return {
      form: {
        search: this.filters.search,
        status: this.filters.status,
      },
      formAddVoucher: this.$inertia.form({
        type: "-",
        amount: "",
        notice: "",
        bank: "",
        wallet: "",
      }),
      formEditVoucher: this.$inertia.form({
        type: "",
        amount: "",
        notice: "",
        bank: "",
        wallet: "",
      }),
      showAddVoucher: false,
      showDeleteVoucherAlert: false,
      deleteVoucherLink: "",
      showEditVoucherModel: false,
      targetVoucher: {},
    };
  },

  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get(
          `/dashboard/stores/${this.store.id}/show`,
          pickBy(this.form),
          {
            preserveState: true,
            preserveScroll: true,
          }
        );
      }, 150),
    },
  },

  methods: {
    reset() {
      this.form = mapValues(this.form, () => null);
    },
    changeStoreStatus(store) {
      this.$inertia.post(
        `/dashboard/stores/${store.id}/update-status`,
        pickBy(this.form),
        {
          preserveScroll: true,
        }
      );
      console.log("done");
    },
    approved(status) {
      if (status == "approved") return true;
      else return false;
    },
    changeStoreFeature(store, feature) {
      this.$inertia.post(
        `/dashboard/stores/${store.id}/update-feature/${feature}`,
        pickBy(this.form),
        {
          preserveScroll: true,
        }
      );
      console.log("done");
    },

    submitAddVoucherForm() {
      this.formAddVoucher.post(
        this.route("vouchers.store", this.marketer.user_id),
        {
          preserveScroll: true,
          onSuccess: () =>
            this.formAddVoucher.reset(
              "amount",
              "bank",
              "notice",
            ),
        }
      );
    },
    deleteVoucher(voucher) {
      this.deleteVoucherLink = route("vouchers.delete", voucher);
      this.showDeleteVoucherAlert = true;
    },

    editVoucher(voucher) {
      this.editVoucherLink = route("vouchers.update", voucher);
      this.showEditVoucherModel = true;
      this.targetVoucher = voucher;
      this.formEditVoucher.amount = Math.abs(voucher.amount);
      this.formEditVoucher.notice = voucher.notice;
      this.formEditVoucher.bank = voucher.to_bank;
      this.formEditVoucher.wallet = voucher.bank.id;

      this.formEditVoucher.type = voucher.amount < 0 ? "-" : "+";
    },
    submitEditVoucherForm() {
      this.formEditVoucher.post(
        this.route("vouchers.update", this.targetVoucher.id),
        {
          preserveScroll: true,
        }
      );
      this.showEditVoucherModel = false;
    },
  },

  computed: {
    csrf() {
      return document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    },
  },
};
</script>
