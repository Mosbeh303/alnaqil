<template>
    <div>
        <Head :title="$page.props.language.shipment_modification" />

        <page-header>
            <div class="flex justify-between">
                <div>
                    {{ $page.props.language.modify_shipment_no }}
                    {{ shipment.number }}
                </div>
                <div>
                    <BackButton />
                </div>
            </div>
        </page-header>
        <flash-messages class="max-w-3xl mx-auto mt-7" />
        <div class="grid gap-7 md:grid-cols-2 mt-7 px-7 mb-7">
            <!-- Create new shipment -->
            <div class="p-3 bg-white border border-gray-200 rounded sm:p-5">
                <h2 class="mb-4 text-lg font-semibold text-gray-700">
                    {{ $page.props.language.charge_modification }}
                </h2>

                <form @submit.prevent="submit" class="mt-4">
                    <Label :value="$page.props.language.The_recipients_name" />
                    <Input
                        id="receiver"
                        type="text"
                        v-model="form.receiver"
                        :error="form.errors.receiver"
                        class="block w-full mt-2"
                        required
                        autofocus
                        :placeholder="$page.props.language.The_recipients_name"
                        autocomplete="receiver"
                    />
                    <InputError :message="form.errors.receiver" />

                    <Label
                        :value="$page.props.language.order_number"
                        class="mt-4"
                    />
                    <Input
                        id="orderId"
                        type="text"
                        v-model="form.order_id"
                        :error="form.errors.order_id"
                        class="block w-full mt-4 mb-4"
                        :placeholder="$page.props.language.order_number"
                    />
                    <InputError :message="form.errors.order_id" />

                    <Label :value="$page.props.language.city_" class="mt-4" />
                    <!-- <SelectInput
            v-model="form.city"
            :error="form.errors.city"
            class="block w-full"
            :selectPlaceholder="$page.props.language.city_"
            required
          >
            <option v-for="(city, i) in cities" :key="i" :value="city.name">
              {{ city.name }}
            </option>
            <option v-if="cities.length === 0" :value="$page.props.language.alriyadh">
              {{ $page.props.language.alriyadh }}
            </option>
          </SelectInput> -->

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

                    <Label
                        :value="$page.props.language.Neighborhood"
                        class="mt-4"
                    />
                    <Input
                        id="neighborhood"
                        list="neighborhoods"
                        type="text"
                        v-model="form.neighborhood"
                        :error="form.errors.neighborhood"
                        class="block w-full mt-2"
                        required
                        :placeholder="$page.props.language.Neighborhood"
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

                    <Label
                        :value="$page.props.language.street_name"
                        class="mt-4"
                    />
                    <Input
                        id="street"
                        type="text"
                        v-model="form.street"
                        :error="form.errors.street"
                        class="block w-full mt-2"
                        required
                        :placeholder="$page.props.language.street_name"
                        autocomplete="street"
                    />
                    <InputError :message="form.errors.street" />

                    <Label
                        :value="$page.props.language.recipients_mobile_number"
                        class="mt-4"
                    />
                    <Input
                        id="receiverPhone"
                        type="text"
                        v-model="form.receiverPhone"
                        :error="form.errors.receiverPhone"
                        class="block w-full mt-2"
                        required
                        :placeholder="$page.props.language.phone_number"
                        autocomplete="receiverPhone"
                    />
                    <InputError :message="form.errors.receiverPhone" />

                    <Label
                        :value="
                            $page.props.language.the_amount_owed_by_the_customer
                        "
                        class="mt-4"
                    />
                    <Input
                        id="cod"
                        type="text"
                        v-model="form.cod"
                        class="block w-full mt-2"
                        :error="form.errors.cod"
                        :placeholder="
                            $page.props.language.the_amount_owed_by_the_customer
                        "
                        autocomplete="cod"
                    />
                    <InputError :message="form.errors.cod" />
                    <div v-if="$page.props.auth.account === 'admin'">
                        <Label :value="$page.props.language.ADP" class="mt-4" />
                        <Input
                            id="ADP"
                            type="text"
                            v-model="form.ADP"
                            class="block w-full mt-2"
                            :error="form.errors.ADP"
                            :placeholder="$page.props.language.ADP"
                            autocomplete="ADP"
                        />
                        <InputError :message="form.errors.ADP" />
                    </div>
                    <Label
                        :value="$page.props.language.Store_number"
                        class="mt-4"
                    />
                    <Input
                        id="store"
                        type="text"
                        v-model="form.store"
                        class="block w-full mt-2"
                        :error="form.errors.store"
                        required
                        :placeholder="$page.props.language.Store_number"
                        autocomplete="store"
                    />
                    <InputError :message="form.errors.store" />

                    <Label
                        :value="$page.props.language.shipment_weight_kg"
                        class="mt-4"
                    />
                    <Input
                        id="weight"
                        type="number"
                        step="0.01"
                        v-model="form.weight"
                        class="block w-full mt-2"
                        :error="form.errors.weight"
                        :placeholder="$page.props.language.shipment_weight_kg"
                    />
                    <InputError :message="form.errors.weight" />

                    <Label
                        :value="$page.props.language.the_number_of_pieces"
                        class="mt-4"
                    />
                    <Input
                        id="numberOfPieces"
                        type="number"
                        max="2"
                        min="1"
                        v-model="form.numberOfPieces"
                        class="block w-full mt-2"
                        :error="form.errors.numberOfPieces"
                        :placeholder="$page.props.language.the_number_of_pieces"
                        @keyup="maxNumberOfPieces()"
                    />
                    <InputError :message="form.errors.numberOfPieces" />

                    <Label
                        :value="$page.props.language.package_contents_optional"
                        class="mt-4"
                    />
                    <Input
                        id="details"
                        type="text"
                        v-model="form.details"
                        class="block w-full mt-2"
                        :error="form.errors.details"
                        :placeholder="
                            $page.props.language.package_contents_optional
                        "
                        autocomplete="details"
                    />
                    <InputError :message="form.errors.details" />

                    <Label
                        :value="
                            $page.props.language
                                .shipping_fee_excluding_additional_fees
                        "
                        class="mt-4"
                    />
                    <Input
                        id="baseFee"
                        type="number"
                        step="0.01"
                        v-model="form.base_fee"
                        class="block w-full mt-2"
                        :error="form.errors.base_fee"
                        :placeholder="$page.props.language.shipping_fee"
                        autocomplete="details"
                    />
                    <InputError :message="form.errors.base_fee" />

                    <div class="block mt-4">
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
                                {{
                                    $page.props.language
                                        .retrieved_from_the_client
                                }}
                            </span>
                        </label>
                    </div>

                    <div class="block mt-4">
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
                                {{
                                    $page.props.language
                                        .replacement_from_the_customer
                                }}
                            </span>
                        </label>
                    </div>

                    <div class="block mt-4">
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
                                {{
                                    $page.props.language.refrigerated_transport
                                }}
                            </span>
                        </label>
                    </div>

                    <Label
                        :value="
                            $page.props.language
                                .shipping_fees_extra_fees_included
                        "
                        class="mt-4"
                    />
                    <Input
                        id="totalFees"
                        type="text"
                        v-model="form.fees"
                        class="block w-full mt-2 bg-gray-100"
                        :placeholder="$page.props.language.shipping_fee"
                        autocomplete="details"
                        readonly
                    />

                    <Button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="px-6 mt-4"
                    >
                        {{ $page.props.language.charge_modification }}
                    </Button>
                </form>
            </div>

            <div>
                <div class="p-3 bg-white border border-gray-200 rounded sm:p-5">
                    <h2 class="mb-4 text-lg font-semibold text-gray-700">
                        {{ $page.props.language.modify_the_shipment_status }}
                    </h2>
                    <form @submit.prevent="updateShipmentStatus" class="mt-4">
                        <Label
                            :value="$page.props.language.shipment_status"
                            class="mt-4"
                        />
                        <SelectInput
                            v-model="formStatus.status"
                            :error="formStatus.errors.status"
                            class="block w-full"
                            :selectPlaceholder="
                                $page.props.language.shipment_status
                            "
                            required
                        >
                            <option
                                v-for="(status, i) in $page.props
                                    .shipmentStatus"
                                :key="i"
                                :value="i"
                                :hidden="statusOption(i)"
                            >
                                {{ status }}
                            </option>
                        </SelectInput>

                        <Button
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            class="px-6 mt-4"
                        >
                            {{ $page.props.language.modify }}
                        </Button>
                    </form>
                </div>
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
import Label from "@/Components/Label.vue";
import BackButton from "../../Components/backButton.vue";
import searchableSelect from "../../Components/searchableSelect.vue";

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
        Label,
        BackButton,
        searchableSelect,
    },
    layout: BreezeAuthenticatedLayout,

    props: [
        "shipment",
        "financial",
        "refrigerated_transport_fee",
        "exchange_fee",
        "cities",
        "neighborhoods",
        "return_fee",
        "cod_fee",
        "cod_active",
        "cod_fee_percent",
        "fixed_amount_cod_fee",
        "original_fees",
    ],
    //   language: Object,
    data() {
        return {
            form: this.$inertia.form({
                receiver: this.shipment.receiver_name,
                city: this.shipment.city,
                neighborhood: this.shipment.neighborhood,
                street: this.shipment.street,
                receiverPhone: this.shipment.receiver_phone,
                cod: this.financial.cod,
                ADP: this.financial.ADP,
                store: this.shipment.store_id,
                details: this.shipment.details,
                refrigerated: this.shipment.refrigerated == 1 ? true : false,
                exchange: this.shipment.exchange == 1 ? true : false,
                return: this.shipment.return_back == 1 ? true : false,
                base_fee: this.financial.base_fee,
                weight: this.shipment.weight,
                numberOfPieces: this.shipment.number_of_pieces,
                order_id: this.shipment.order_id,
                fees: this.original_fees,
            }),
            formStatus: this.$inertia.form({
                status: this.shipment.status,
            }),
        };
    },

    computed: {
        // fees: {
        //     get() {
        //         let fees = parseFloat(this.form.base_fee);
        //         if (this.form.refrigerated) {
        //             fees += parseFloat(this.refrigerated_transport_fee);
        //         }
        //         if (this.form.exchange) {
        //             fees += parseFloat(this.exchange_fee);
        //         }
        //         if (this.form.return) {
        //             fees += parseFloat(this.return_fee);
        //         }
        //         if (this.form.cod > 0) {
        //             if (this.form.cod > this.fixed_amount_cod_fee)
        //                 fees +=
        //                     (this.form.cod * parseFloat(this.cod_fee_percent)) /
        //                     100;
        //             else fees += parseFloat(this.cod_fee);
        //         }
        //         return fees;
        //     },
        // },

        computedCities: function () {
            let cities = [];

            cities = Object.values(this.cities).map((city) => {
                if (this.$page.props.locale === "en") {
                    return city.name_en;
                } else if (this.$page.props.locale === "ar") {
                    return city.name;
                }
            });

            return cities;
        },
    },

    created() {
        // console.log(this.form.refrigerated);
        // console.log(this.shipment);
        // console.log(this.financial);
    },

    // updated() {
    //     console.log("updated");
    //     this.form.fees = this.fees;
    // },

    methods: {
        submit() {
            this.form.post(this.route("shipments.update", this.shipment.id), {
                preserveScroll: true,
                onError: () => form.reset(),

            });
        },
        updateShipmentStatus() {
            this.formStatus.post(
                this.route("shipments.update_status", this.shipment.id),
                {
                    preserveScroll: true,
                    onError: () => this.formStatus.reset(),
                }
            );
        },
        maxNumberOfPieces() {
            if (this.form.numberOfPieces > 2) this.form.numberOfPieces = 2;
        },
        statusOption(status) {
            if (
                (status == 10 || status == 20) &&
                this.$page.props.auth.account != "admin"
            )
                return true;
            else return false;
        },
        cityChanged() {
            setTimeout(() => {
                this.setNeighborhoods();
            }, 100);
        },

        setNeighborhoods() {
            let city = this.form.city;
            console.log("city:" + city);

            axios.get("/dashboard/neighborhoods/" + city).then((response) => {
                console.log(response);
                this.neighborhoods = response.data;
            });
        },
    },
};
</script>
