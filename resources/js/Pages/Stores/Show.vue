<template>
  <div>
    <Head :title="language.store_file" />

    <page-header>
      <p>
        {{ language.store_file }} ({{ store.name }})
        <Badge v-if="store.type == 'platform'" color="bg-purple-600" class="inline-block ">{{ language.platform }}</Badge>
          <Badge v-else-if="store.type == 'individual'" color="bg-purple-600" class="inline-block ">{{ language.individual }}</Badge>
          <Badge v-else color="bg-purple-600" class="inline-block ">{{ language.shop }}</Badge>
      </p>

      <form :action="route('operator.shipments_pdf', store.id)" method="post" id="form">
        <input type="hidden" name="_token" :value="$page.props.csrf" />
        <Button
          class="bottom-0 md:absolute"
          :class="{
            'md:left-7': $page.props.locale == 'ar',
            'md:right-7': $page.props.locale == 'en',
          }"
          :href="route('stores.shipments', store)"
        >
          {{ language.store_shipments }}
        </Button>
      </form>
    </page-header>

    <flash-messages class="m-4 md:m-7" />

    <div class="grid gap-4 m-4 md:gap-7 md:grid-cols-2 md:m-7">
      <div>
        <Section :title="language.statistics">
          <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
            <p>{{ language.total_payment_upon_receipt }}</p>
            <p v-html="stats.cods + ' ' + language.riyal"></p>
          </div>
          <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
            <p>{{ language.total_delivery_fee }}</p>
            <p v-html="stats.fees + ' ' + language.riyal"></p>
          </div>
          <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
            <p>{{ language.paid }}</p>
            <p v-html="Math.abs(stats.paid) + ' ' + language.riyal"></p>
          </div>
          <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
            <p>{{ language.Receiver }}</p>
            <p v-html="Math.abs(stats.rec) + ' ' + language.riyal"></p>
          </div>
          <div
            class="flex justify-between w-full p-2 mt-4 font-semibold"
            :class="{
              'text-red-600': stats.dues > 0,
              'text-green-600': stats.dues < 0,
            }"
          >
            <p>{{ language.total_dues_to_the_store }}</p>
            <p v-html="stats.dues + ' ' + language.riyal"></p>
          </div>
        </Section>

        <Section :title="language.wallet" class="mt-4 md:mt-7">
          <!-- <div
                        class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200"
                    >
                        <p>المحفظة</p>
                        <p v-html="0 + ' ريال'"></p>
                    </div> -->

          <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
            <p>{{ language.delivery_fee }} ( {{ language.wallet }} )</p>
            <p v-html="store.wallet.fees + ' ' + language.riyal"></p>
          </div>

          <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
            <p>{{ language.paid_to_the_customer }} ({{ language.wallet }})</p>
            <p v-html="store.wallet.paid + ' ' + language.riyal"></p>
          </div>

          <div class="w-full p-2 pb-4 mt-4 border-b border-gray-200">
            <div class="flex justify-between">
              <p>{{ language.outstanding_balance }}</p>
              <p>{{ store.wallet.outstandingBalance }} {{ language.riyal }}</p>
            </div>
            <span class="text-xs text-gray-500">{{
              language.related_outstanding_balance_to_active_shipments
            }}</span>
          </div>

          <div class="flex justify-between w-full p-2 pb-4 mt-4">
            <p>{{ language.total_wallet_credit }}</p>
            <p>{{ store.wallet.balance }} {{ language.riyal }}</p>
          </div>
        </Section>
      </div>

      <Section class="h-fit">
        <div class="flex justify-between">
          <h2 class="text-lg font-semibold text-gray-700">
            {{ language.actions }}
          </h2>
        </div>

        <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
          <p>{{ language.activate_deactivate_the_store }}</p>
          <Toggle
            :value="approved(store.status)"
            @inputclick="changeStoreStatus(store)"
          />
        </div>

        <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
          <p>{{ language.deferred_account }}</p>
          <Toggle
            :value="store.postpaid_active == 1"
            @inputclick="changeStoreFeature(store, 'postpaid_active')"
          />
        </div>

        <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
          <p>{{ language.refrigerated_transport }}</p>
          <Toggle
            :value="store.refregirated_transport_active == 1"
            @inputclick="changeStoreFeature(store, 'refrigerated_transport_active')"
          />
        </div>

        <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
          <p>{{ language.replacement_from_the_customer }}</p>
          <Toggle
            :value="store.exchange_active == 1"
            @inputclick="changeStoreFeature(store, 'exchange_active')"
          />
        </div>

        <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
          <p>{{ language.retrieval_from_the_client }}</p>
          <Toggle
            :value="store.return_active == 1"
            @inputclick="changeStoreFeature(store, 'return_active')"
          />
        </div>

        <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
          <p>{{ language.payment_on_receipt_fee }}</p>
          <Toggle
            :value="store.cod_active"
            @inputclick="changeStoreFeature(store, 'cod_active')"
          />
        </div>

        <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
          <p>{{ language.tax_invoice }}</p>
          <Toggle
            :value="store.invoice_active == 1"
            @inputclick="changeStoreFeature(store, 'invoice_active')"
          />
        </div>

        <div class="flex justify-between w-full p-2 pb-4 mt-4 border-b border-gray-200">
          <p>{{ language.order_number }}</p>
          <Toggle
            :value="store.order_id_active == 1"
            @inputclick="changeStoreFeature(store, 'order_id_active')"
          />
        </div>

        <div class="flex justify-between w-full p-2 pb-4 mt-4 ">
          <p>{{ language.auto_billing }}</p>
          <Toggle
            :value="store.auto_billing == 1"
            @inputclick="changeStoreFeature(store, 'auto_billing')"
          />
        </div>

        <div v-show="store.auto_billing == 1">
            <Label> {{ language.billing_period  }}</Label>
            <select-input @change="changeBillingType()" v-model="formEditBillingPeriod.period" :selectPlaceholder="language.billing_period">
                <option value="day">{{ language.daily }}</option>
                <option value="week">{{ language.weekly }}</option>
                <option value="quarter">{{ language.quarterly }}</option>
                <option value="month">{{ language.monthly }}</option>
                <option value="year">{{ language.annual }}</option>
            </select-input>
          </div>
      </Section>
    </div>

    <Section class="m-4 md:m-7">
      <div class="flex items-baseline justify-between">
        <div class="flex justify-start gap-3">

          <h2 class="mb-4 text-lg font-semibold text-gray-700">
                    <span class="flex items-center">
                        {{ language.shipments_account_statement }}
                        <span class="flex px-3 font-bold text-gray-600">
                            <Icon
                                name="shipment"
                                class="relative w-5 h-5 ml-1 text-gray-500 bottom-1"
                            />
                            {{ shipments.total }}
                        </span>
                    </span>
                </h2>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="w-full max-w-md mr-4"
            @reset="reset"
          ></search-filter>
        </div>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full">
          <thead class="text-sm bg-gray-100 border-b">
            <tr class="whitespace-nowrap">
              <th class="px-6 pt-6 pb-4">{{ language.Shipment_number }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.delivery_fee }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.paiement_when_recieving }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.delivery_date }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.Status }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.the_total }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="shipment in shipments.data"
              :key="shipment.id"
              class="text-center hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
            >
              <td class="border-t">
                <Link
                  class="px-6 py-4 focus:text-indigo-500"
                  :href="route('shipments.show', shipment.id)"
                >
                  {{ shipment.number }}
                </Link>
              </td>

              <td class="border-t">
                <Link
                  class="px-6 py-4"
                  :href="route('shipments.show', shipment.id)"
                  tabindex="-1"
                >
                  {{ shipment.fees }}
                </Link>
              </td>

              <td class="border-t">
                <Link
                  class="flex items-center justify-center px-6 py-4"
                  :href="route('shipments.show', shipment.id)"
                  tabindex="-1"
                >
                  <span v-if="!shipment.cod">{{ language.pre_paid }}</span>
                  <span v-else>{{ shipment.cod }}</span>
                </Link>
              </td>

              <td class="border-t">
                {{ shipment.shipping_date }}
              </td>

              <td class="border-t">
                <p
                  class="px-4 py-1 text-xs text-green-700 bg-green-200 rounded-xl w-fit"
                  v-if="shipment.status == 100"
                >
                  {{ language.delivered }}
                </p>
                <p
                  class="px-4 py-1 text-xs text-red-700 bg-red-200 rounded-xl w-fit"
                  v-else-if="shipment.status == -100"
                >
                  {{ language.audit }}
                </p>
              </td>

              <td class="border-t">
                {{ shipment.due }}
              </td>
            </tr>
            <tr v-if="shipments.data.length === 0">
              <td class="px-6 py-4 border-t" colspan="4">{{ language.no_shipments }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </Section>

    <Section class="m-4 md:m-7">
      <div class="flex items-baseline justify-between">
        <div class="flex justify-start gap-3">
          <h2 class="mb-4 text-lg font-semibold text-gray-700">
            {{ language.receipts_of_receivables }}
          </h2>
        </div>

        <div class="flex items-center justify-between mb-6">
          <search-filter
            v-model="form.search"
            class="w-full max-w-md mr-4"
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
        <h3 class="text-base font-semibold text-gray-600">{{ language.add_payment }}</h3>
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
              <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">
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
            <label class="flex items-center">
              <Checkbox
                name="wallet"
                v-model:checked="formAddVoucher.belongsToStoreWallet"
              />
              <span class="mr-2 text-sm text-gray-600"> {{ language.wallet }}</span>
            </label>
          </div>

          <Button class="justify-center mt-4 whitespace-nowrap h-fit">
            {{ language.add_payment }}</Button
          >
        </form>
      </div>

      <div class="overflow-x-auto border rounded">
        <table class="min-w-full">
          <thead class="text-sm bg-gray-100 border-b">
            <tr class="whitespace-nowrap">
              <th class="px-6 pt-6 pb-4">#</th>
              <th class="px-6 pt-6 pb-4">{{ language.the_bond }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.the_amount }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.date }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.from_the_bank }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.to_the_bank }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.employee }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.notes }}</th>
              <th class="px-6 pt-6 pb-4">{{ language.wallet }}</th>
              <th class="px-6 pt-6 pb-4"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(voucher, index) in vouchers.data"
              :key="index"
              class="text-center hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
            >
              <td class="py-4 border-t">
                {{ voucher.number }}
              </td>
              <td class="py-4 border-t">
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
                <span v-if="voucher.employee !== null">{{ voucher.employee.name }}</span>
              </td>
              <td class="border-t">
                {{ voucher.notice }}
              </td>

              <td class="border-t">
                <div class="flex justify-center w-full">
                  <Icon
                    name="check"
                    class="w-5 h-5 text-green-600"
                    v-if="voucher.belongsToStoreWallet == 1"
                  ></Icon>
                </div>
              </td>

              <td class="border-t">
                <div class="flex justify-center">
                  <div class="flex justify-center px-2" @click="editVoucher(voucher)">
                    <icon name="edit" class="block w-5 h-5 text-gray-500" />
                  </div>

                  <div class="flex justify-center px-2" @click="deleteVoucher(voucher)">
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
      <h2 class="mb-2 text-lg font-semibold text-gray-800">
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
      <h2 class="mb-2 text-lg font-semibold text-gray-800">
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
            <option v-for="wallet in wallets" :key="wallet.id" :value="wallet.id">
              {{ wallet.name }}
            </option>
          </SelectInput>
        </div>

        <Input
          id="bank"
          type="text"
          v-model="formEditVoucher.bank"
          :error="formEditVoucher.errors.bank"
          class="block w-full mt-3 h-fit"
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
        <label class="flex items-center mt-3">
          <Checkbox
            name="wallet"
            v-model:checked="formEditVoucher.belongsToStoreWallet"
          />
          <span class="mr-2 text-sm text-gray-600"> {{ language.wallet }}</span>
        </label>
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
import Badge from "@/Components/Badge.vue";


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
    Badge
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    store: Object,
    stats: Object,
    shipments: Object,
    receivedShipments: Object,
    vouchers: Object,
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
        belongsToStoreWallet: false,
      }),
      formEditVoucher: this.$inertia.form({
        type: "",
        amount: "",
        notice: "",
        bank: "",
        wallet: "",
        belongsToStoreWallet: false,
      }),

      formEditBillingPeriod: this.$inertia.form({
        period: this.store.billing_period,
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
        this.$inertia.get(`/dashboard/stores/${this.store.id}/show`, pickBy(this.form), {
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

    changeBillingType() {
      this.formEditBillingPeriod.post(
        `/dashboard/stores/${this.store.id}/update-billing-period`,
        {
          preserveScroll: true,
        }
      );
      console.log("done");
    },

    submitAddVoucherForm() {
      this.formAddVoucher.post(this.route("vouchers.store", this.store.user_id), {
        preserveScroll: true,
        onSuccess: () =>
          this.formAddVoucher.reset("amount", "bank", "notice", "belongsToStoreWallet"),
      });
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
      this.formEditVoucher.belongsToStoreWallet =
        voucher.belongsToStoreWallet == 1 ? true : false;
      this.formEditVoucher.type = voucher.amount < 0 ? "-" : "+";
    },
    submitEditVoucherForm() {
      this.formEditVoucher.post(this.route("vouchers.update", this.targetVoucher.id), {
        preserveScroll: true,
      });
      this.showEditVoucherModel = false;
    },
  },

  computed: {
    csrf() {
      return document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    },
  },
};
</script>
