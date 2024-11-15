<template>

    <Head :title="language.invoices" />
    <div>
      <page-header :button="{
            text: language.issuing_invoice,
            href: route('invoices.create'),
            enable: ($page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('invoices_stores'))
        }">{{ language.invoices }}</page-header>

      <FlashMessages class="m-4 md:m-7" />



      <Section title="" class="m-4 md:m-7">
      <form @submit.prevent="submit">
        <Label
          v-if="$page.props.auth.account != 'client'"
          :value="language.store + ' ' +  ' (' + language.optional + ') '"
          class="mb-2"
        />

        <searchableSelect
            v-if="$page.props.auth.account != 'client'"
            :myOptions="computedOptions"
            :myVModel="form.store"
            :my_Place_holder="$page.props.language.store_name_or_number"
            :my_Place_holder_when_Nothing_Found="$page.props.language.no_stores"
            @update:myVModel="form.store = $event"
          />
        <InputError
          v-if="$page.props.auth.account != 'client'"
          :message="form.errors.store"
        />

        <Label :value="language.from" class="mt-4" />
        <Input
          id="search"
          type="date"
          class="w-full mt-2"
          v-model="form.from"
          :error="form.errors.from"
        ></Input>
        <InputError :message="form.errors.from" />

        <Label :value="language.to" class="mt-4" />
        <Input
          id="search"
          type="date"
          class="w-full mt-2"
          v-model="form.to"
          :error="form.errors.to"
        ></Input>
        <InputError :message="form.errors.to" />

        <!-- <Label value="الشحنات" class="mt-4"/>
                <SelectInput v-model="form.type" :error="form.errors.type" class="mt-2" >
                    <option value=">=">جميع الشحنات</option>
                    <option value="=">دفع مسبق</option>
                    <option value=">">دفع عند الإستلام</option>
                </SelectInput> -->
        <Button class="mt-4">{{ language.search }}</Button>
      </form>


    </Section>


    <div class="flex items-center justify-end gap-4 mb-6 ml-8">
  <form method="post" :action="route('invoices.pdf')">
    <input type="hidden" name="_token" :value="$page.props.csrf" />
    <input type="hidden" name="from" v-model="form.from" />
    <input type="hidden" name="to" v-model="form.to" />
    <input type="hidden" name="store" v-model="form.store" />

    <Button
      for="from"
      class="!bg-white !text-gray-900 !border !border-gray-900 py-2 hover:!text-gray-200 hover:!bg-gray-900 text-sm"
    >
      {{ language.download_PDF }}
    </Button>
  </form>

  <form method="post" :action="route('invoices.exel')">
    <input type="hidden" name="_token" :value="$page.props.csrf" />
    <input type="hidden" name="from" v-model="form.from" />
    <input type="hidden" name="to" v-model="form.to" />
    <input type="hidden" name="store" v-model="form.store" />

    <Button
      for="from"
      class="!bg-white !text-gray-900 !border !border-gray-900 py-2 hover:!text-gray-200 hover:!bg-gray-900 text-sm"
    >
      {{ language.download_excel_file }}
    </Button>
  </form>
</div>














      <div
        class="p-3 m-4 overflow-hidden bg-white border border-gray-200 rounded sm:p-5 md:m-7"
      >
        <!-- ***************** -->

        <!-- <div class="flex items-baseline justify-between">
          <h2 class="mb-4 text-lg font-semibold text-gray-700">
            {{ language.data }}
          </h2>
          <form :action="route('stores.invoice.pdf')" method="post">
            <input type="hidden" name="_token" :value="$page.props.csrf" />
            <input type="hidden" name="from" v-model="form.from" />
            <input type="hidden" name="to" v-model="form.to" />
            <input type="hidden" name="store" v-model="form.store" />
            <Button
              for="from"
              class="bg-gray-200 !text-gray-600 py-2 hover:!text-gray-200 text-sm"
            >
              {{ language.download_the_invoice }}
            </Button>
          </form>
        </div> -->

        <div class="flex flex-col">
          <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
              <div class="overflow-x-auto border rounded">
                <table class="min-w-full">
                  <thead class="text-sm bg-gray-100 border-b">
                    <tr class="whitespace-nowrap">
                        <th class="px-6 pt-6 pb-4">{{ language.invoice_number }}</th>
                        <th class="px-6 pt-6 pb-4">{{ language.store_number }}</th>
                        <th class="px-6 pt-6 pb-4">{{ language.Store_name}}</th>

                        <th class="px-6 pt-6 pb-4">{{ language.invoice_date }}</th>
                        <th class="px-6 pt-6 pb-4">{{ language.fees_without_tax }}</th>
                        <th class="px-6 pt-6 pb-4">{{ language.tax }}</th>
                        <th class="px-6 pt-6 pb-4">{{ language.fees_with_tax }}</th>
                        <th class="px-6 pt-6 pb-4">{{ language.tax_number }}</th>
                        <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(invoice, i) in invoices.data"
                      :key="i"
                      class="text-center hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100"
                    >

                      <td class="border-t">
                        <a :href="route('invoices.show', invoice.id)"  class="flex items-center px-6 py-4" tabindex="-1">
                            {{ invoice.number }}
                        </a>
                      </td>

                      <td class="border-t">
                        <Link
                          class="px-6 py-4 text-center focus:text-indigo-500"
                          :href="'#'"
                        >
                          {{ invoice.storeNumber }}
                        </Link>
                      </td>
                      <td class="border-t">
                        <Link class="flex items-center px-6 py-4" tabindex="-1">
                            {{ invoice.storeName }}
                        </Link>
                      </td>



                      <td class="px-4 border-t">
                        {{ invoice.date }}
                      </td>

                      <td class="px-4 border-t">
                        {{ parseFloat(invoice.fees_without_tax).toFixed(2) }}
                      </td>

                      <!-- <td class="px-4 border-t">15%</td> -->

                      <td class="px-4 border-t">
                        {{ parseFloat( invoice.tax ).toFixed(2)}}
                      </td>

                      <td class="px-4 border-t">
                        {{ parseFloat(invoice.fees).toFixed(2) }}
                      </td>

                      <td class="border-t">
                        <Link class="flex items-center px-6 py-4" tabindex="-1">
                            {{ invoice.tax_number }}
                        </Link>
                      </td>

                      <td class="border-t">
                        <a :href="route('invoices.show', invoice.id)"  class="flex items-center px-6 py-4" tabindex="-1">
                            <Icon class="w-6 h-6" name="eye"/>
                        </a>
                      </td>


                    </tr>
                    <tr v-if="invoices.data.length === 0">
                      <td class="px-6 py-4 border-t" colspan="4">{{ language.no_data }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
        <pagination class="mt-6" :links="invoices.links" />
      </div>
    </div>
  </template>

  <script>
  import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
  import PageHeader from "@/Components/PageHeader.vue";
  import Button from "@/Components/Button.vue";
  import { Head, Link } from "@inertiajs/vue3";
  import Pagination from "@/Components/Pagination.vue";
  import Icon from "@/Components/Icon.vue";
  import FlashMessages from "@/Components/FlashMessages.vue";
  import Section from "@/Components/Section.vue";
  import Input from "@/Components/Input.vue";
  import InputError from "@/Components/InputError.vue";
  import Label from "@/Components/Label.vue";
  import SelectInput from "@/Components/SelectInput.vue";
  import searchableSelect from "../../Components/searchableSelect.vue";

  export default{
    components: {
      Head,
      PageHeader,
      Button,
      Link,
      Pagination,
      Icon,
      Section,
      Input,
      FlashMessages,
      InputError,
      Label,
      SelectInput,
      searchableSelect
    },
    layout: BreezeAuthenticatedLayout,
    props: {
      invoices: Object,
      stores:Object,
      language: Object,
      items:Object,
      fromDate: String,
      to: String,
      store: String
    },

    data() {
      return {
        form: this.$inertia.form({
          store: this.store,
          from: this.fromDate,
          to: this.to,
        }),
        options: this.stores,

      };
    },

    computed: {
      computedOptions: function () {
          let options = [];

              options = Object.values(this.options)
              .filter((option) => option.label !== null)
              .map((option) => option.label);

          return options;
      },
    },

    methods: {
      submit() {
        this.form.get(this.route("invoices"), {
          preserveScroll: true,
          onFinish: () => this.form.reset(""),
          onSuccess: () => this.form.reset(""),
        });
      },
      reset() {
        this.form = mapValues(this.form, () => null);
      },
    },
  }
  </script>

