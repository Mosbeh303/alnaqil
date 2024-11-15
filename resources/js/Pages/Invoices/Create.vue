<template>
    <Head :title="language.issuing_invoice" />

    <div>
        <page-header> {{ language.issuing_invoice }}</page-header>

        <FlashMessages class="m-4 md:m-7" />

        <Section title="" class="m-4 md:m-7">
            <form @submit.prevent="submit" id="form">
                <Label
                    v-if="$page.props.auth.account != 'client'"
                    :value="language.store"
                    class="mb-2"
                />

                <searchableSelect
                    v-if="$page.props.auth.account != 'client'"
                    :myOptions="computedOptions"
                    :myVModel="form.store"
                    :my_Place_holder="$page.props.language.store_name_or_number"
                    :my_Place_holder_when_Nothing_Found="
                        $page.props.language.no_stores
                    "
                    @update:myVModel="form.store = $event"
                />
                <InputError
                    v-if="$page.props.auth.account != 'client'"
                    :message="form.errors.store"
                />

                <Label :value="language.from" class="mt-8 mb-2" />

                <VueDatePicker
                    v-model="form.from"
                    readonly
                    no-disabled-range
                    :enable-time-picker="false"
                    week-start="0"
                    locale="ar"
                    :day-names="['أحد', 'إث', 'ثل', 'إر', 'خم', 'جم', 'سب']"
                />

                <Label :value="language.to" class="mt-8 mb-2" />

                <VueDatePicker
                    v-model="form.to"
                    no-disabled-range
                    :enable-time-picker="false"
                    week-start="0"
                    :min-date="minDate"
                    :max-date="maxDate"
                    locale="ar"
                    :day-names="['أحد', 'إث', 'ثل', 'إر', 'خم', 'جم', 'سب']"
                    auto-apply
                />
                <Button class="mt-4" type="button" @click="preview()">{{ language.preview }}</Button>
            </form>
        </Section>

        <Section v-if="showpPreview" title="معاينة الفاتورة" class="pt-6 m-4 md:m-7">
            <Button class="my-4" form="form"> {{language.issuing_the_invoice}}</Button>
            <Button class="mx-4 !bg-gray-100 text-sm !text-gray-500  inline-block " @click="cancel()"> {{language.cancel}}</Button>
            <iframe class="w-full h-screen mt-8" :src="previewLink" frameborder="0"></iframe>
        </Section>

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
import DateInput from "@/Components/DateInput.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { addDays, subDays } from "date-fns";
import throttle from "lodash/throttle";


export default {
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
        searchableSelect,
        DateInput,
        VueDatePicker,
    },
    layout: BreezeAuthenticatedLayout,
    props: {
        stores: Object,
        language: Object,
        max_date: String
    },
    computed: {
        computedOptions: function () {
            let options = [];

            options = Object.values(this.stores)
                .filter((option) => option.label !== null)
                .map((option) => option.label);

            return options;
        },
    },


    data() {
        return {
            form: this.$inertia.form({
                store: "",
                to: "",
                from: "",
            }),
            options: this.stores,
            sum: 0,
            disabledDates: [
                subDays(new Date(), 1),
                new Date(),
                addDays(new Date(), 1),
            ],
            minDate: "",
            maxDate: this.max_date,
            showpPreview: false,
            previewLink: ""
        }
    },

    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                axios.get("/dashboard/invoices/create/date-range?store="  + this.form.store).then((response) => {
                        console.log(response);
                        this.form.from = response.data.min_date;
                        this.minDate = response.data.min_date;
                    });
            }, 150),
        },
    },

    methods: {

        submit() {
            this.form.from = this.formatDate(this.form.from);
            this.form.to = this.formatDate(this.form.to);
            this.form.post(this.route("invoices.store"), {
                preserveScroll: true,
                onError: () => this.form.reset(),
            });
        },

        preview(){
            this.showpPreview = true;
            this.previewLink = route('invoices.preview')+ `?store=${this.form.store}&from=${this.formatDate(this.form.from)}&to=${this.formatDate(this.form.to)}#zoom=100`;

        },
        reset() {
            this.form = mapValues(this.form, () => null);
        },


        formatDate(dateString) {


            var date = new Date(dateString);

            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();

            var formattedDay = (day < 10 ? '0' : '') + day;
            var formattedMonth = (month < 10 ? '0' : '') + month;

            var formattedDate = formattedMonth  + '/' + formattedDay + '/' + year;

            console.log(formattedDate);


            return formattedDate;
        },

        cancel(){
            window.location.reload();
        }

    },
};
</script>

<style>
.v-select {
    direction: rtl;
}

.vs__clear {
    margin-left: 8px;
}
.vs__open-indicator {
    margin-left: 10px;
}

.vs__dropdown-toggle {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}
</style>
