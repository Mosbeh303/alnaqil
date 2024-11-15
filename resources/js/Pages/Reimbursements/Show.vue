<template>
  <Head :title="employee.name" />
  <div>
    <page-header>
      <span>
        {{ language.receipts_from_delegates_the_employee }} {{ employee.name }}
      </span>
    </page-header>

    <div class="bg-white lg:m-7 m-4 overflow-hidden max-w-xl">
      <Widget
        color="text-orange-500"
        iconName="dollar"
        :widgetText="language.receivables"
        :widgetValue="employee.dues"
      />
    </div>
    <div
      class="px-5 py-6 bg-white rounded border border-gray-200 lg:m-7 m-4 overflow-hidden"
    >
      <form @submit.prevent="search">
        <div class="flex gap-4">
          <div class="flex-grow">
            <Label :value="language.from" class="mt-4" />
            <Input
              id="search"
              type="date"
              class="w-full mt-2"
              v-model="form.from"
              :error="form.errors.from"
            ></Input>
            <InputError :message="form.errors.from" />
          </div>

          <div class="flex-grow">
            <Label :value="language.to" class="mt-4" />
            <Input
              id="search"
              type="date"
              class="w-full mt-2"
              v-model="form.to"
              :error="form.errors.to"
            ></Input>
            <InputError :message="form.errors.to" />
          </div>
        </div>

        <div>
          <Label value="" class="mt-2" />
          <Button class="mt-4">{{ language.search }}</Button>
        </div>
      </form>
    </div>
    <div
      class="border rounded border-gray-200 p-3 sm:p-5 bg-white lg:m-7 m-4 overflow-hidden"
    >
      <flash-messages class="mx-auto w-full" />
      <!-- ***************** -->

      <div class="flex justify-between items-baseline md:flex-row flex-col">
        <div class="flex justify-start gap-3">
          <h2 class="text-gray-700 text-lg mb-4 font-semibold">
            {{ language.receipt }}
          </h2>

          <!-- <span class="flex px-3 font-bold text-gray-600">
                        {{ vouchers.total }}
                    </span> -->
        </div>

        <div class="flex items-center justify-between mb-6 gap-4">
          <Button
            v-if="
              this.$page.props.auth.account == 'admin' ||
              this.$page.props.auth.permissions.includes('add_voucher_accounts')
            "
            type="button"
            @click="showAddVoucher = !showAddVoucher"
            class="!bg-gray-200 !text-gray-600 border-0 mr-2 whitespace-nowrap"
            >{{ language.add_a_receipt }}
          </Button>
          <form :action="route('reimbursements.empReimbursements.pdf', this.employee)" method="get">
            <input type="hidden" name="_token" :value="$page.props.csrf" />
            <input type="hidden" name="from" v-model="form.from" />
            <input type="hidden" name="to" v-model="form.to" />
            <Button
              for="from"
              class="bg-gray-200 !text-white py-2 hover:!text-gray-200 text-sm"
            >
              {{ language.download_PDF }}
            </Button>
          </form>

          <form :action="route('reimbursements.empReimbursements.exel', this.employee)" method="get">
            <input type="hidden" name="_token" :value="$page.props.csrf" />
            <input type="hidden" name="from" v-model="form.from" />
            <input type="hidden" name="to" v-model="form.to" />
            <Button
              for="from"
              class="bg-gray-200 !text-white py-2 hover:!text-gray-200 text-sm"
            >
              {{ language.download_excel_file }}
            </Button>
          </form>
        </div>
      </div>

      <div v-show="showAddVoucher" class="mb-7">
        <h3 class="text-base font-semibold text-gray-600">{{ language.add_delivery }}</h3>
        <form
          @submit.prevent="submitAddVoucherForm"
          class="mt-2 flex gap-4 md:flex-row flex-col"
        >
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

          <Input
            id="notice"
            type="text"
            v-model="formAddVoucher.notice"
            :error="formAddVoucher.errors.notice"
            class="block w-full h-fit"
            :placeholder="language.notes"
          />
          <InputError :message="formAddVoucher.errors.notice" />
          <Button class="whitespace-nowrap h-fit justify-center">{{
            language.add_payment
          }}</Button>
        </form>
      </div>

      <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-x-auto border rounded">
              <table class="min-w-full">
                <thead class="border-b bg-gray-100 text-sm">
                  <tr class="whitespace-nowrap">
                    <th class="pb-4 pt-6 px-6">#</th>
                    <th class="pb-4 pt-6 px-6">{{ language.the_bond }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.the_amount }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.date }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.the_delegate }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.responsible }}</th>
                    <th class="pb-4 pt-6 px-6">{{ language.notes }}</th>
                    <th class="pb-4 pt-6 px-6"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(voucher, index) in vouchers"
                    :key="index"
                    class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100 text-center"
                  >
                    <td class="border-t py-4">
                      {{ voucher.number }}
                    </td>
                    <td class="border-t py-4">
                      {{ voucher.type }}
                    </td>
                    <td class="border-t">
                      {{ Math.abs(voucher.amount) }}
                    </td>
                    <td class="border-t">
                      {{ voucher.created_at }}
                    </td>

                    <td class="border-t">
                      {{ voucher.operator }}
                    </td>

                    <td class="border-t">
                      {{ voucher.responsible }}
                    </td>

                    <td class="border-t">
                      {{ voucher.notice }}
                    </td>

                    <td class="border-t">
                      <div class="flex justify-center">
                        <div
                          v-if="
                            (this.$page.props.auth.account == 'admin' ||
                              this.$page.props.auth.permissions.includes(
                                'edit_voucher_accounts'
                              )) &&
                            voucher.type === 'تسليم'
                          "
                          class="flex justify-center px-2"
                          @click="editVoucher(voucher)"
                        >
                          <icon name="edit" class="block w-5 h-5 text-gray-500" />
                        </div>

                        <div
                          v-if="
                            (this.$page.props.auth.account == 'admin' ||
                              this.$page.props.auth.permissions.includes(
                                'edit_voucher_accounts'
                              )) &&
                            voucher.type === 'تسليم'
                          "
                          class="flex justify-center px-2"
                          @click="deleteVoucher(voucher)"
                        >
                          <icon name="trash" class="block w-5 h-5 text-gray-500" />
                        </div>

                        <Link
                          class="flex justify-center px-2"
                          :href="route('reimbursements.show', voucher)"
                          v-if="voucher.type === 'تسليم'"
                        >
                          <icon name="eye" class="block w-5 h-5 text-gray-500" />
                        </Link>

                        <Link
                          class="flex justify-center px-2"
                          :href="route('vouchers.show', voucher)"
                          v-else
                        >
                          <icon name="eye" class="block w-5 h-5 text-gray-500" />
                        </Link>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="vouchers.length === 0">
                    <td class="px-6 py-4 border-t" colspan="4">
                      {{ language.there_are_no_receipts }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

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

        <Input
          id="notice"
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
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import SearchFilter from "@/Components/SearchFilter.vue";
import SelectInput from "@/Components/SelectInput.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Pagination from "@/Components/Pagination.vue";
import Icon from "@/Components/Icon.vue";
import Modal from "@/Components/Modal.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import Widget from "@/Pages/Dashboard/Components/Widget.vue";
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
    Widget,
    Input,
    InputError,
  },
  layout: BreezeAuthenticatedLayout,
  props: {
    vouchers: Object,
    employee: Object,
    language: Object,
    to: String,
    from: String,
  },
  data() {
    return {
        form: this.$inertia.form({
        from: this.from,
        to: this.to,
      }),
      formAddVoucher: this.$inertia.form({
        amount: "",
        notice: "",
      }),
      formEditVoucher: this.$inertia.form({
        amount: "",
        notice: "",
      }),
      showAddVoucher: false,
      showDeleteVoucherAlert: false,
      deleteVoucherLink: "",
      showEditVoucherModel: false,
      targetVoucher: {},
    };
  },
  methods: {
    search() {
      this.form.get(this.route('reimbursements.empReimbursements', this.employee.id), {
        preserveScroll: true,
        onSuccess: () => this.formAddVoucher.reset(""),
      });
    },
    formatDate(d) {
      var date = new Date(d);

      return date.toLocaleDateString("en-US");
    },
    submitAddVoucherForm() {
      this.formAddVoucher.post(this.route("reimbursements.store", this.employee), {
        preserveScroll: true,
        onSuccess: () => this.formAddVoucher.reset(),
      });
    },
    deleteVoucher(voucher) {
      this.deleteVoucherLink = route("reimbursements.delete", voucher);
      this.showDeleteVoucherAlert = true;
    },

    editVoucher(voucher) {
      this.editVoucherLink = route("reimbursements.update", voucher);
      this.showEditVoucherModel = true;
      this.targetVoucher = voucher;
      this.formEditVoucher.amount = Math.abs(voucher.amount);
      this.formEditVoucher.notice = voucher.notice;
      this.formEditVoucher.type = voucher.amount < 0 ? "-" : "+";
    },
    submitEditVoucherForm() {
      this.formEditVoucher.post(
        this.route("reimbursements.update", this.targetVoucher.id),
        {
          preserveScroll: true,
        }
      );
      this.showEditVoucherModel = false;
    },
  },
};
</script>
