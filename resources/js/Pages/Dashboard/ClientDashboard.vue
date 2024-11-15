<template>
    <Head title="Dashboard" />
    <div>
        <page-header
            :button="{
                text: language.add_shipment,
                href: route('shipments.create'),
            }"
        >
            {{ language.dashboard }}
        </page-header>
        <div class="pb-12 pt-7 sm:px-0 px-4">
            <!-- Main Container -->

            <!-- Top Stats Widgets -->
            <div class="max-w-full mx-auto sm:px-6 lg:px-8 mb-7">
                <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-4">
                    <Widget
                        color="text-orange-500"
                        iconName="shipment"
                        :widgetText="language.my_shipments"
                        :widgetValue="stats.allShipments"
                        :subText="language.active_shipments"
                        :subValue="
                            shipmentsCount.created +
                            shipmentsCount.received +
                            shipmentsCount.processing +
                            shipmentsCount.progress
                        "
                    />

                    <Widget
                        color="text-red-500"
                        iconName="cash"
                        :widgetText="language.receivables"
                        :widgetValue="stats.dues + language.riyal"
                    />

                    <Widget
                        color="text-purple-600"
                        iconName="wallet"
                        :widgetText="language.total_wallet_credit"
                        :widgetValue="stats.wallet.balance + language.riyal"
                    />

                    <Widget
                        color="text-purple-400"
                        iconName="wallet-off"
                        :widgetText="language.suspendedWalletBalance"
                        :widgetValue="
                            stats.wallet.outstandingBalance + language.riyal
                        "
                        :subText="language.relatedBalance"
                    />
                </div>
            </div>

            <!-- Charts -->
            <div class="grid sm:px-6 lg:px-8 gap-7 xl:grid-cols-2">
                <!-- Create new shipment -->
                <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
                    <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                        {{ language.add_shipment }}
                    </h2>

                    <flash-messages />

                    <form
                        @submit.prevent="submit"
                        class="mt-4"
                        v-if="show_form"
                    >
                        <Input
                            id="receiverPhone"
                            type="text"
                            v-model="form.receiverPhone"
                            :error="form.errors.receiverPhone"
                            class="mt-4 block w-full"
                            required
                            :placeholder="language.mobile_number + '0501234567'"
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
                            :placeholder="language.recipient_name"
                            autocomplete="receiver"
                        />
                        <InputError :message="form.errors.receiver" />

                        <Input
                            v-if="$page.props.auth.storeFeature.order_id == 1"
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
                            @update:myVModel="form.city = $event"
                            @click="cityChanged"
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
                            id="numberOfPieces"
                            type="number"
                            max="2"
                            min="1"
                            v-model="form.numberOfPieces"
                            class="mt-4 block w-full"
                            :error="form.errors.numberOfPieces"
                            :placeholder="language.number_of_packages"
                            @keyup="maxNumberOfPieces()"
                        />
                        <InputError :message="form.errors.numberOfPieces" />
                        <small class="text-gray-500">
                            {{
                                language.the_maximum_number_of_packages_is
                            }}</small
                        >

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

                        <Input
                            id="store"
                            type="hidden"
                            v-model="form.store"
                            class="mt-4 block w-full"
                            :error="form.errors.store"
                            required
                            :placeholder="language.store_number"
                            autocomplete="store"
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

                        <div
                            class="block mt-4"
                            v-if="$page.props.auth.storeFeature.return == 1"
                        >
                            <label class="flex items-center">
                                <Checkbox
                                    name="retrun"
                                    v-model:checked="form.return"
                                />
                                <span class="mr-2 text-sm text-gray-600">
                                    {{ language.retrieval_from_the_client }}
                                </span>
                            </label>
                        </div>

                        <div
                            class="block mt-4"
                            v-if="$page.props.auth.storeFeature.exchange == 1"
                        >
                            <label class="flex items-center">
                                <Checkbox
                                    name="exchange"
                                    v-model:checked="form.exchange"
                                />
                                <span class="mr-2 text-sm text-gray-600">
                                    {{ language.exchange_from_the_customer }}
                                </span>
                            </label>
                        </div>

                        <div
                            class="block mt-4"
                            v-if="
                                $page.props.auth.storeFeature
                                    .refrigerated_transport == 1
                            "
                        >
                            <label class="flex items-center">
                                <Checkbox
                                    name="refrigerated"
                                    v-model:checked="form.refrigerated"
                                />
                                <span class="mr-2 text-sm text-gray-600">
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

                    <div v-else>
                        <Input
                            type="text"
                            class="mt-4 block w-full"
                            name="retrun"
                            v-model="receiver_phone"
                            :placeholder="language.whrite_receiver_phone"
                        />
                        <Button
                            :class="{
                                'opacity-25': formCancelShipment.processing,
                            }"
                            :disabled="formCancelShipment.processing"
                            class="mt-4 px-6"
                            @click="show_shipment_form"
                        >
                            {{ language.next }}
                        </Button>
                    </div>
                </div>

                <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white">
                    <div class="flex justify-between">
                        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                            {{ language.shipments_rate }}
                        </h2>
                    </div>

                    <Chart chartId="shipments" :_data="shipmentsDataChart" />
                </div>
            </div>

            <!-- Bottom widget stats -->
            <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-7">
                <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-7">
                    <Link href="/dashboard/shipments?status=100">
                        <Widget
                            color="text-green-500"
                            type="vertical"
                            iconName="completed"
                            :widgetText="language.completed_shipments"
                            :widgetValue="shipmentsCount.completed"
                        />
                    </Link>
                    <Link href="/dashboard/shipments?status=-100">
                        <Widget
                            color="text-orange-500"
                            type="vertical"
                            iconName="returned"
                            :widgetText="language.returned_sh"
                            :widgetValue="shipmentsCount.returned"
                        />
                    </Link>

                    <Link href="/dashboard/shipments?status=65">
                        <Widget
                            color="text-blue-500"
                            type="vertical"
                            iconName="truck"
                            :widgetText="language.shipments_on_the_way"
                            :widgetValue="shipmentsCount.progress"
                        />
                    </Link>

                    <Link href="/dashboard/shipments?status=35">
                        <Widget
                            color="text-yellow-500"
                            type="vertical"
                            iconName="process"
                            :widgetText="language.processing"
                            :widgetValue="shipmentsCount.processing"
                        />
                    </Link>

                    <Link href="/dashboard/shipments?status=20">
                        <Widget
                            color="text-green-500"
                            type="vertical"
                            iconName="hand"
                            :widgetText="language.shipments_received"
                            :widgetValue="shipmentsCount.received"
                        />
                    </Link>

                    <Link href="/dashboard/shipments?status=10">
                        <Widget
                            color="text-gray-500"
                            type="vertical"
                            iconName="shipment"
                            :widgetText="language.created_shipments"
                            :widgetValue="shipmentsCount.created"
                        />
                    </Link>

                    <Link href="/dashboard/shipments?status=-1">
                        <Widget
                            color="text-red-500"
                            type="vertical"
                            iconName="cancel"
                            :widgetText="language.the_canceled_shipments"
                            :widgetValue="shipmentsCount.canceled"
                        />
                    </Link>
                </div>
            </div>

            <div
                class="rounded border border-gray-200 p-3 sm:p-5 bg-white md:m-7 m-4 md:w-1/2"
            >
                <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                    {{ language.cancel_shipment }}
                </h2>
                <form @submit.prevent="cancelShipment">
                    <Input
                        id="number"
                        type="text"
                        v-model="formCancelShipment.number"
                        :error="formCancelShipment.errors.number"
                        class="mt-4 block w-full mb-4"
                        required
                        :placeholder="language.Shipment_number"
                    />
                    <InputError :message="formCancelShipment.errors.number" />

                    <SelectInput
                        v-model="formCancelShipment.status"
                        :error="formCancelShipment.errors.status"
                        class="block w-full"
                        :selectPlaceholder="language.shipment_status"
                        required
                    >
                        <option value="-1">{{ language.canceled }}</option>
                    </SelectInput>

                    <Button
                        :class="{ 'opacity-25': formCancelShipment.processing }"
                        :disabled="formCancelShipment.processing"
                        class="mt-4 px-6"
                    >
                        {{ language.modify }}
                    </Button>
                </form>
            </div>

            <!-- Latest Shipments Added-->
            <div
                class="border rounded border-gray-200 p-3 sm:p-5 sm:mx-6 lg:mx-8 bg-white mt-7"
                v-if="
                    $page.props.auth.account == 'admin' ||
                    $page.props.auth.permissions.includes(
                        'show_latest_shipments'
                    )
                "
            >
                <div class="flex justify-between items-baseline">
                    <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                        {{ language.latest_shipments }}
                    </h2>
                    <Button
                        class="bg-gray-200 !text-gray-600 py-2 hover:!text-gray-200 text-sm"
                        :href="route('shipments')"
                    >
                        {{ language.view_all }}</Button
                    >
                </div>

                <div class="flex flex-col">
                    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div
                            class="py-2 inline-block min-w-full sm:px-6 lg:px-8"
                        >
                            <div class="overflow-x-auto border rounded">
                                <table class="min-w-full">
                                    <thead class="border-b bg-gray-100">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                            >
                                                {{ language.Shipment_number }}
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                            >
                                                {{
                                                    language.The_recipients_name
                                                }}
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                            >
                                                {{ language.phone_number }}
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                            >
                                                {{ language.city }}
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                            >
                                                {{ language.added_date }}
                                            </th>
                                            <th
                                                scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-right"
                                            >
                                                {{ language.shipment_status }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            class="border-t"
                                            v-for="shipment in shipments"
                                            :key="shipment.id"
                                        >
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                            >
                                                {{ shipment.number }}
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                {{ shipment.receiverName }}
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                {{ shipment.receiverPhone }}
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                {{ shipment.city }}
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                {{ shipment.created_at }}
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                <p
                                                    class="rounded-xl text-gray-700 bg-gray-200 py-1 px-4 w-fit text-xs"
                                                    v-if="shipment.status == 10"
                                                >
                                                    {{ language.created }}
                                                </p>
                                                <p
                                                    class="rounded-xl text-orange-700 bg-orange-200 py-1 px-4 w-fit text-xs"
                                                    v-else-if="
                                                        shipment.status == 20
                                                    "
                                                >
                                                    {{ language.received }}
                                                </p>
                                                <p
                                                    class="rounded-xl text-blue-700 bg-blue-200 py-1 px-4 w-fit text-xs"
                                                    v-else-if="
                                                        shipment.status == 35
                                                    "
                                                >
                                                    {{
                                                        language.processing_in_progress
                                                    }}
                                                </p>
                                                <p
                                                    class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                                                    v-else-if="
                                                        shipment.status == 65
                                                    "
                                                >
                                                    {{
                                                        language.out_to_deliver
                                                    }}
                                                </p>

                                                <p
                                                    class="rounded-xl text-red-700 bg-red-200 py-1 px-4 w-fit text-xs"
                                                    v-else-if="
                                                        shipment.status == -100
                                                    "
                                                >
                                                    {{ language.audit }}
                                                </p>

                                                <p
                                                    class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs"
                                                    v-else-if="
                                                        shipment.status == 100
                                                    "
                                                >
                                                    {{ language.completed }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr class="border-t">
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                            >
                                                1
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                أحمد الفضل
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                {{ language.playStore }}
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                {{
                                                    language.exampleEmailPlaceholder
                                                }}
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                {{ language.oneHourAgo }}
                                            </td>
                                            <td
                                                class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap"
                                            >
                                                <p
                                                    class="rounded-xl text-green-700 bg-green-100 py-1 px-4 w-fit text-xs"
                                                >
                                                    {{ language.completed }}
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End last shipmenets added -->
        </div>
        <!-- End Main Container -->
    </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Widget from "@/Pages/Dashboard/Components/Widget.vue";
import PageHeader from "@/Components/PageHeader.vue";
import Button from "@/Components/Button.vue";
import { Head, Link } from "@inertiajs/vue3";
import Chart from "@/Components/Chart.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import FlashMessages from "@/Components/FlashMessages.vue";
import searchableSelect from "../../Components/searchableSelect.vue";

export default {
    components: {
        Head,
        PageHeader,
        Widget,
        Chart,
        Button,
        Link,
        Label,
        Input,
        SelectInput,
        InputError,
        Checkbox,
        FlashMessages,
        searchableSelect,
    },
    props: {
        shipmentsCount: Object,
        shipments: Object,
        stats: Object,
        cities: Object,
        districts: Object,
        monthlyShipments: Object,
        language: Object,
    },
    layout: BreezeAuthenticatedLayout,
    data() {
        return {
            receiver_phone: null,
            show_form: false,
            form: this.$inertia.form({
                receiver: "",
                city: "",
                neighborhood: "",
                district: "",
                street: "",
                receiverPhone: "",
                cod: "",
                store: this.$page.props.auth.user.id,
                details: "",
                refrigerated: false,
                exchange: false,
                return: false,
                weight: "",
                numberOfPieces: "",
                order_id: "",
            }),

            formCancelShipment: this.$inertia.form({
                number: "",
                status: -1,
            }),
            shipmentsDataChart: {
                labels: [
                    "يناير",
                    "فبراير",
                    "مارس",
                    "ابريل",
                    "مايو",
                    "يونيو",
                    "يوليو",
                    "اغسطس",
                    "سبتمبر",
                    "اكتوبر",
                    "نوفمبر",
                    "ديسمبر",
                ],
                datasets: [
                    {
                        label: "عدد الشحنات",
                        data: this.data(this.monthlyShipments),
                        backgroundColor: "rgba(54,73,93,.5)",
                        borderColor: "#36495d",
                        borderWidth: 3,
                    },
                ],
            },
            neighborhoods: [],
        };
    },

    computed: {
        computedCities: function () {
            let cities = {};
            if (this.form.district == "") {
                cities = this.cities.map((city) => city.name);
            } else {
                cities = this.cities
                    .filter((city) => city.district_id == this.form.district)
                    .map((city) => city.name);
            }
            return cities;
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
        this.form.neighborhood = response.data.receiver.neighborhood;
        this.form.street = response.data.receiver.street;
        this.form.numberOfPieces = 1;
        this.form.details = response.data.lastShipmentContent;
        }

      });
  }

  this.show_form = true;
  this.receiver_phone = null;
}
,
        submit() {
            this.form.post(this.route("shipments.store"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.form.reset();
                    this.show_form = false;
                },
            });
        },

        cancelShipment() {
            this.formCancelShipment.post(this.route("shipments.cancel"), {
                preserveScroll: true,
                onSuccess: () => this.form.reset(),
            });
        },

        data(data) {
            let rate = [];
            data.forEach(function (value) {
                rate[value.month - 1] = value.data;
            });
            return rate;
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
