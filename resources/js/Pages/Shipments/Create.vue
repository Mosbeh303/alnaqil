<template>

    <div>
        <Head :title="language.add_a_new_shipment" />

        <page-header>
            <div class="flex justify-between">
                <div>
                    {{ language.add_a_new_shipment }}
                </div>
                <div>
                    <BackButton />
                </div>
            </div>
        </page-header>

        <div class="grid gap-4 lg:gap-7 md:grid-cols-2 m-4 lg:m-7">
            <!-- Create new shipment -->
            <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
                <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                    {{ language.add_shipment }}
                </h2>

                <flash-messages />
                <div v-if="$page.props.auth.account == 'employee'">
                   <div  v-if="!show_form">
                    <Input
                        type="text"
                        class="mt-4 block w-full"
                        name="retrun"
                        v-model="receiver_phone"
                        :placeholder="language.whrite_receiver_phone"
                    />
                    <Button class="mt-4 px-6" @click="show_shipment_form">
                        {{ language.next }}
                    </Button>
                   </div>

                    <form @submit.prevent="submit" class="mt-4" v-if="show_form">
                        <Input
                        id="receiverPhone"
                        type="text"
                        v-model="form.receiverPhone"
                        :error="form.errors.receiverPhone"
                        class="mt-4 block w-full"
                        required
                        :placeholder="language.mobile_number_"
                        autocomplete="receiverPhone"
                    />
                    <InputError :message="form.errors.receiverPhone" />
                    <Input
                        id="receiver"
                        type="text"
                        v-model="form.receiver"
                        :error="form.errors.receiver"
                        class="mt-4 block w-full mb-4"
                        required
                        autofocus
                        :placeholder="language.recipient_name"
                        autocomplete="receiver"
                    />
                    <InputError :message="form.errors.receiver" />

                    <Input
                        v-if="
                            !(
                                $page.props.auth.account == 'client' &&
                                $page.props.auth.storeFeature.order_id == 0
                            )
                        "
                        id="orderId"
                        type="text"
                        v-model="form.order_id"
                        :error="form.errors.order_id"
                        class="mt-4 block w-full mb-4"
                        :placeholder="language.order_number"
                    />
                    <InputError :message="form.errors.order_id" />

                    <div class="mb-4">
                        <SelectInput
                            v-model="form.district"
                            :error="form.errors.district"
                            class="mt-4 block w-full"
                            :selectPlaceholder="language.terretory"
                            @change="cityChanged()"
                        >
                            <option
                                v-for="(district, i) in districts"
                                :key="i"
                                :value="district.id"
                            >
                                {{ district.name }}
                            </option>
                        </SelectInput>
                    </div>
                    <searchableSelect
                        :myOptions="computedCities"
                        :myVModel="form.city"
                        :my_Place_holder="$page.props.language.city"
                        :my_Place_holder_when_Nothing_Found="
                            $page.props.language.No_cities_in_this_terretory
                        "
                        @click="cityChanged"
                        @update:myVModel="form.city = $event"
                    />

                    <Input
                        id="neighborhood"
                        list="neighborhoods"
                        type="text"
                        v-model="form.neighborhood"
                        :error="form.errors.neighborhood"
                        class="mt-4 block w-full"
                        required
                        :placeholder="language.Neighborhood"
                        autocomplete="off"
                    />
                    <InputError :message="form.errors.neighborhood" />

                    <datalist id="neighborhoods">
                        <option
                            v-for="(neighborhood, i) in neighborhoods"
                            :key="i"
                            :value="neighborhood"
                        />
                    </datalist>

                    <Input
                        id="street"
                        type="text"
                        v-model="form.street"
                        :error="form.errors.street"
                        class="mt-4 block w-full"
                        required
                        :placeholder="language.street_name"
                        autocomplete="street"
                    />
                    <InputError :message="form.errors.street" />


                    <Input
                    v-if="$page.props.auth.permissions.includes('add_ADP')"
                        id="ADP"
                        type="text"
                        v-model="form.ADP"
                        :error="form.errors.ADP"
                        class="mt-4 block w-full mb-4"
                        autofocus
                        :placeholder="language.ADP"
                    />
                    <InputError :message="form.errors.ADP" />
                    <Input
                        id="cod"
                        type="text"
                        v-model="form.cod"
                        class="mt-4 block w-full"
                        :error="form.errors.cod"
                        :placeholder="
                            language.the_amount_of_the_shipment_due_from_th_customer
                        "
                        autocomplete="cod"
                    />
                    <InputError :message="form.errors.cod" />

                    <searchableSelect
                        v-if="$page.props.auth.account != 'client'"
                        :myOptions="computedOptions"
                        :myVModel="form.store"
                        :my_Place_holder="
                            $page.props.language.store_name_or_number
                        "
                        :my_Place_holder_when_Nothing_Found="
                            $page.props.language.no_stores
                        "
                        @update:myVModel="form.store = $event"
                    />
                    <InputError :message="form.errors.store" />

                    <Input
                        id="weight"
                        type="number"
                        step="0.01"
                        v-model="form.weight"
                        class="mt-4 block w-full"
                        :error="form.errors.weight"
                        :placeholder="language.shipment_weight_kg"
                    />
                    <InputError :message="form.errors.weight" />

                    <Input
                        id="numberOfPieces"
                        type="number"
                        max="2"
                        min="1"
                        pattern="[0-2]"
                        v-model="form.numberOfPieces"
                        class="mt-4 block w-full"
                        :error="form.errors.numberOfPieces"
                        :placeholder="language.number_of_packages"
                        @keyup="maxNumberOfPieces()"
                    />

                    <InputError :message="form.errors.numberOfPieces" />
                    <small class="text-gray-500"
                        >{{ language.the_maximum_number_of_packages_is }}
                    </small>

                    <Input
                        id="details"
                        type="text"
                        v-model="form.details"
                        class="mt-4 block w-full"
                        :error="form.errors.details"
                        :placeholder="language.package_contents"
                        autocomplete="details"
                    />
                    <InputError :message="form.errors.details" />

                    <div
                        class="block mt-4"
                        v-if="
                            !$page.props.auth.storeFeature ||
                            $page.props.auth.storeFeature.return == 1
                        "
                    >
                        <label class="flex items-center">
                            <Checkbox
                                name="return"
                                v-model:checked="form.return"
                            />
                            <span
                                class="text-sm text-gray-600"
                                :class="{
                                    'ml-2': $page.props.locale == 'en',
                                    'mr-2': $page.props.locale == 'ar',
                                }"
                            >
                                {{ language.retrieval_from_the_client }}
                            </span>
                        </label>
                    </div>

                    <div
                        class="block mt-4"
                        v-if="
                            !$page.props.auth.storeFeature ||
                            $page.props.auth.storeFeature.exchange == 1
                        "
                    >
                        <label class="flex items-center">
                            <Checkbox
                                name="exchange"
                                v-model:checked="form.exchange"
                            />
                            <span
                                class="text-sm text-gray-600"
                                :class="{
                                    'ml-2': $page.props.locale == 'en',
                                    'mr-2': $page.props.locale == 'ar',
                                }"
                            >
                                {{ language.exchange_from_the_customer }}
                            </span>
                        </label>
                    </div>

                    <div
                        class="block mt-4"
                        v-if="
                            !$page.props.auth.storeFeature ||
                            $page.props.auth.storeFeature
                                .refrigerated_transport == 1
                        "
                    >
                        <label class="flex items-center">
                            <Checkbox
                                name="refrigerated"
                                v-model:checked="form.refrigerated"
                            />
                            <span
                                class="text-sm text-gray-600"
                                :class="{
                                    'ml-2': $page.props.locale == 'en',
                                    'mr-2': $page.props.locale == 'ar',
                                }"
                            >
                                {{ language.refrigerated_transport }}
                            </span>
                        </label>
                    </div>

                    <Button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="mt-4 px-6"
                    >
                        {{ language.addSHipment }}</Button
                    >
                </form>
                </div>
                <form @submit.prevent="submit" class="mt-4" v-if="$page.props.auth.account !== 'employee'">
                    <Input
                        id="receiver"
                        type="text"
                        v-model="form.receiver"
                        :error="form.errors.receiver"
                        class="mt-4 block w-full mb-4"
                        required
                        autofocus
                        :placeholder="language.recipient_name"
                        autocomplete="receiver"
                    />
                    <InputError :message="form.errors.receiver" />

                    <Input
                        v-if="
                            !(
                                $page.props.auth.account == 'client' &&
                                $page.props.auth.storeFeature.order_id == 0
                            )
                        "
                        id="orderId"
                        type="text"
                        v-model="form.order_id"
                        :error="form.errors.order_id"
                        class="mt-4 block w-full mb-4"
                        :placeholder="language.order_number"
                    />
                    <InputError :message="form.errors.order_id" />

                    <div class="mb-4">
                        <SelectInput
                            v-model="form.district"
                            :error="form.errors.district"
                            class="mt-4 block w-full"
                            :selectPlaceholder="language.terretory"
                            @change="cityChanged()"
                        >
                            <option
                                v-for="(district, i) in districts"
                                :key="i"
                                :value="district.id"
                            >
                                {{ district.name }}
                            </option>
                        </SelectInput>
                    </div>
                    <searchableSelect
                        :myOptions="computedCities"
                        :myVModel="form.city"
                        :my_Place_holder="$page.props.language.city"
                        :my_Place_holder_when_Nothing_Found="
                            $page.props.language.No_cities_in_this_terretory
                        "
                        @click="cityChanged"
                        @update:myVModel="form.city = $event"
                    />

                    <Input
                        id="neighborhood"
                        list="neighborhoods"
                        type="text"
                        v-model="form.neighborhood"
                        :error="form.errors.neighborhood"
                        class="mt-4 block w-full"
                        required
                        :placeholder="language.Neighborhood"
                        autocomplete="off"
                    />
                    <InputError :message="form.errors.neighborhood" />

                    <datalist id="neighborhoods">
                        <option
                            v-for="(neighborhood, i) in neighborhoods"
                            :key="i"
                            :value="neighborhood"
                        />
                    </datalist>

                    <Input
                        id="street"
                        type="text"
                        v-model="form.street"
                        :error="form.errors.street"
                        class="mt-4 block w-full"
                        required
                        :placeholder="language.street_name"
                        autocomplete="street"
                    />
                    <InputError :message="form.errors.street" />

                    <Input
                        id="receiverPhone"
                        type="text"
                        v-model="form.receiverPhone"
                        :error="form.errors.receiverPhone"
                        class="mt-4 block w-full"
                        required
                        :placeholder="language.mobile_number_"
                        autocomplete="receiverPhone"
                    />
                    <InputError :message="form.errors.receiverPhone" />

                    <Input
                        id="cod"
                        type="text"
                        v-model="form.cod"
                        class="mt-4 block w-full"
                        :error="form.errors.cod"
                        :placeholder="
                            language.the_amount_of_the_shipment_due_from_th_customer
                        "
                        autocomplete="cod"
                    />
                    <InputError :message="form.errors.cod" />

                    <searchableSelect
                        v-if="$page.props.auth.account != 'client'"
                        :myOptions="computedOptions"
                        :myVModel="form.store"
                        :my_Place_holder="
                            $page.props.language.store_name_or_number
                        "
                        :my_Place_holder_when_Nothing_Found="
                            $page.props.language.no_stores
                        "
                        @update:myVModel="form.store = $event"
                    />
                    <InputError :message="form.errors.store" />

                    <Input
                        id="weight"
                        type="number"
                        step="0.01"
                        v-model="form.weight"
                        class="mt-4 block w-full"
                        :error="form.errors.weight"
                        :placeholder="language.shipment_weight_kg"
                    />
                    <InputError :message="form.errors.weight" />

                    <Input
                        id="numberOfPieces"
                        type="number"
                        max="2"
                        min="1"
                        pattern="[0-2]"
                        v-model="form.numberOfPieces"
                        class="mt-4 block w-full"
                        :error="form.errors.numberOfPieces"
                        :placeholder="language.number_of_packages"
                        @keyup="maxNumberOfPieces()"
                    />

                    <InputError :message="form.errors.numberOfPieces" />
                    <small class="text-gray-500"
                        >{{ language.the_maximum_number_of_packages_is }}
                    </small>

                    <Input
                        id="details"
                        type="text"
                        v-model="form.details"
                        class="mt-4 block w-full"
                        :error="form.errors.details"
                        :placeholder="language.package_contents"
                        autocomplete="details"
                    />
                    <InputError :message="form.errors.details" />

                    <div
                        class="block mt-4"
                        v-if="
                            !$page.props.auth.storeFeature ||
                            $page.props.auth.storeFeature.return == 1
                        "
                    >
                        <label class="flex items-center">
                            <Checkbox
                                name="return"
                                v-model:checked="form.return"
                            />
                            <span
                                class="text-sm text-gray-600"
                                :class="{
                                    'ml-2': $page.props.locale == 'en',
                                    'mr-2': $page.props.locale == 'ar',
                                }"
                            >
                                {{ language.retrieval_from_the_client }}
                            </span>
                        </label>
                    </div>

                    <div
                        class="block mt-4"
                        v-if="
                            !$page.props.auth.storeFeature ||
                            $page.props.auth.storeFeature.exchange == 1
                        "
                    >
                        <label class="flex items-center">
                            <Checkbox
                                name="exchange"
                                v-model:checked="form.exchange"
                            />
                            <span
                                class="text-sm text-gray-600"
                                :class="{
                                    'ml-2': $page.props.locale == 'en',
                                    'mr-2': $page.props.locale == 'ar',
                                }"
                            >
                                {{ language.exchange_from_the_customer }}
                            </span>
                        </label>
                    </div>

                    <div
                        class="block mt-4"
                        v-if="
                            !$page.props.auth.storeFeature ||
                            $page.props.auth.storeFeature
                                .refrigerated_transport == 1
                        "
                    >
                        <label class="flex items-center">
                            <Checkbox
                                name="refrigerated"
                                v-model:checked="form.refrigerated"
                            />
                            <span
                                class="text-sm text-gray-600"
                                :class="{
                                    'ml-2': $page.props.locale == 'en',
                                    'mr-2': $page.props.locale == 'ar',
                                }"
                            >
                                {{ language.refrigerated_transport }}
                            </span>
                        </label>
                    </div>

                    <Button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="mt-4 px-6"
                    >
                        {{ language.addSHipment }}</Button
                    >
                </form>
            </div>

            <!-- Import Shipments -->
            <div
                class="border rounded border-gray-200 p-3 sm:p-5 bg-white flex flex-col gap-8"
            >
                <div class="flex justify-between items-center space-x-6">
                    <div class="space-x-1">
                        <h2 class="text-gray-700 text-lg mb-1 font-semibold">
                            {{ language.import_shipments }}
                        </h2>
                        <p class="text-gray-700">
                            {{
                                language.import_a_number_of_shipments_with_the_click_of_a_button
                            }}
                        </p>
                    </div>

                    <div>
                        <a
                            :href="route('shipments.excel')"
                            class="!bg-gray-200 flex-no-wrap !text-gray-500  inline-flex items-center px-4 py-3 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                        >
                            {{ language.download_excel_file }}
                        </a>
                    </div>
                </div>
                <div
                    class="mt-4 border-4 border-gray-200 rounded-md border-dashed grow p-7 cursor-pointer mx-8"
                >
                    <p class="text-gray-500">
                        {{ language.drag_the_file_into_the_frame }}
                    </p>
                    <form
                        @submit.prevent="importShimpents"
                        class="mt-4"
                        id="ImportShipments"
                    >
                        <div
                            class="h-full flex items-center justify-center relative"
                        >
                            <div>
                                <svg
                                    v-if="fileName"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-40 w-40 text-green-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-40 w-40 text-gray-200"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                                    />
                                </svg>
                                <span class="text-gray-700">{{
                                    fileName
                                }}</span>
                            </div>

                            <input
                                class="cursor-pointer absolute block opacity-0 pin-r pin-t h-full"
                                type="file"
                                accept=".csv,.xlsx"
                                @input="
                                    formImport.file = $event.target.files[0]
                                "
                                name="file"
                                @change="changeFileName($event)"
                                ref="file"
                            />
                        </div>
                    </form>
                </div>
                <Button class="mt-4 px-6 w-fit" form="ImportShipments">
                    {{ language.import_shipments }}</Button
                >
            </div>
        </div>
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Button from "@/Components/Button.vue";
import { Head } from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import searchableSelect from "../../Components/searchableSelect.vue";
import BackButton from "../../Components/backButton.vue";

export default {
    components: {
        Head,
        PageHeader,
        Button,
        Input,
        Checkbox,
        BreezeValidationErrors,
        FlashMessages,
        SelectInput,
        InputError,
        searchableSelect,
        BackButton,
    },
    layout: BreezeAuthenticatedLayout,

    props: {
        cities: Object,
        stores: Object,
        districts: Object,
        language: Object,
    },

    data() {
        return {
            receiver_phone: null,
            show_form: false,
            form: this.$inertia.form({
                receiver: "",
                city: "",
                district: "",
                neighborhood: "",
                ADP:"",
                receiverPhone: "",
                cod: "",
                store: "",
                details: "",
                refrigerated: false,
                exchange: false,
                return: false,
                street: "",
                weight: "",
                numberOfPieces: "",
                order_id: "",
            }),
            formImport: this.$inertia.form({
                file: {},
            }),
            fileName: "",
            neighborhoods: [],
        };
    },
    computed: {
        computedCities: function () {
            let cities = [];
            if (this.form.district == "") {
                cities = Object.values(this.cities).map((city) => {
                    if (this.$page.props.locale === "en") {
                        return city.name_en;
                    } else if (this.$page.props.locale === "ar") {
                        return city.name;
                    }
                });
            } else {
                cities = Object.values(this.cities)
                    .filter((city) => city.district_id == this.form.district)
                    .map((city) => {
                        if (this.$page.props.locale === "en") {
                            return city.name_en;
                        } else if (this.$page.props.locale === "ar") {
                            return city.name;
                        }
                    });
            }
            return cities;
        },
        computedOptions: function () {
            let options = [];

            options = Object.values(this.stores)
                .filter((option) => option.label !== null)
                .map((option) => option.label);

            return options;
        },
    },

    methods: {
        show_shipment_form() {
            const regex = /^\d{10}$/;
            const isValidPhone = regex.test(this.receiver_phone);

            if (isValidPhone) {
                this.form.receiverPhone = this.receiver_phone;
                axios
                    .get(this.route("receiverData", this.receiver_phone))
                    .then((response) => {
                       if (response.data.receiver!== null) {
                        this.form.receiver = response.data.receiver.name;
                        this.form.city = response.data.receiver.city;
                        this.form.neighborhood =
                            response.data.receiver.neighborhood;
                        this.form.street = response.data.receiver.street;

                        this.form.numberOfPieces = 1;
                        this.form.details = response.data.lastShipmentContent;

                       }
                    });
            }

            this.show_form = true;
            this.receiver_phone = null;
        },
        submit() {
            this.form.post(this.route("shipments.store"), {
                preserveScroll: true,
                onSuccess: () =>
                    this.form.reset(
                        "receiver",
                        "order_id",
                        "neighborhood",
                        "receiverPhone",
                        "cod",
                        "store",
                        "details",
                        "refrigerated",
                        "exchange",
                        "return",
                        "street"
                    ),
                onFinish: () => this.form.reset(""),
            });
        },
        importShimpents() {
            this.formImport.post(this.route("shipments.import"), {
                preserveScroll: true,
                onSuccess: () => this.formImport.reset(),
                onFinish: () => this.formImport.reset(""),
            });
        },
        changeFileName(e) {
            console.log(e.target.files);
            this.fileName = e.target.files[0].name;
        },
        setNeighborhoods() {
            let city = this.form.city;
            console.log("city:" + city);

            axios.get("/dashboard/neighborhoods/" + city).then((response) => {
                console.log(response);
                this.neighborhoods = response.data;
            });
        },

        cityChanged() {
            setTimeout(() => {
                this.setNeighborhoods();
            }, 100);
        },

        maxNumberOfPieces() {
            if (this.form.numberOfPieces > 2) this.form.numberOfPieces = 2;
        },
    },
};
</script>
