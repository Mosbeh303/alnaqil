<template>
    <Head title="Dashboard" />

    <div>
        <page-header :button="{
            text: language.add_shipment,
            href: route('shipments.create'),
            enable: ($page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('create_shipments'))
        }">
            {{ language.dashboard }}

        </page-header>

        <div class="pb-12 pt-7 sm:px-0 px-4">

            <!-- Main Container -->

            <!-- Top Stats Widgets -->
            <div class="max-w-full mx-auto sm:px-6 lg:px-8 mb-7">
                <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-4">
                    <flash-messages />
                    <Link :href="route('shipments')">
                    <Widget color="text-orange-500" iconName="shipment" :widgetText=language.shipments
                        :widgetValue="stats.shipments"
                        v-if="$page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('show_shipments_stats')" />
                    </link>
                    <Link :href="route('stores.')">
                    <Widget color="text-blue-500" iconName="users" :widgetText=language.clients :widgetValue="stats.clients"
                        v-if="$page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('show_clients_stats')" />
                    </Link>
                    <Widget color="text-purple-500" iconName="truck" :widgetText=language.delegates
                        :widgetValue="stats.operators"
                        v-if="$page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('show_operators_stats')" />
                    <Widget color="text-green-500" iconName="sms" :widgetText=language.sms_credit :widgetValue="stats.sms"
                        v-if="$page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('show_sms_stats')" />
                </div>
            </div>

            <!-- Charts -->
            <div class="grid sm:px-6 lg:px-8 gap-7 xl:grid-cols-2">
                <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white"
                    v-if="$page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('show_shipments_stats')">
                    <div class="flex justify-between">
                        <h2 class="text-gray-700 text-lg mb-4 font-semibold">

                            {{ language.shipments_rate }} </h2>

                    </div>

                    <Chart chartId="shipments" :_data="shipmentsDataChart" />
                </div>
                <div class="border rounded border-gray-200 p-3 sm:p-5 bg-white"
                    v-if="$page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('show_profit_stats')">
                    <div class="flex justify-between">
                        <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                            {{ language.profit_rate }}

                        </h2>
                        <!-- <select
                            name="filter"
                            id=""
                            class="outline-none border-0 rounded h-8 text-sm"
                        >
                            <option value="daily">يومي</option>
                            <option value="monthly">شهري</option>
                            <option value="yearly">سنوي</option>
                        </select> -->
                    </div>
                    <Chart chartId="profits" :_data="profitDataChart" />
                </div>
            </div>

            <!-- Bottom widget stats -->
            <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-7"
                v-if="$page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('show_shipments_stats')">
                <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-7">
                    <Link href="/dashboard/shipments?status=100">
                    <Widget color="text-green-500" type="vertical" iconName="completed"
                        :widgetText=language.completed_shipments :widgetValue="shipmentsCount.completed" />
                    </Link>
                    <Link href="/dashboard/shipments?status=-100">
                    <Widget color="text-orange-500" type="vertical" iconName="returned" :widgetText=language.returned_sh
                        :widgetValue="shipmentsCount.returned" />
                    </Link>

                    <Link href="/dashboard/shipments?status=65">
                    <Widget color="text-blue-500" type="vertical" iconName="truck" :widgetText=language.shipments_on_the_way
                        :widgetValue="shipmentsCount.progress" />
                    </Link>

                    <Link href="/dashboard/shipments?status=35">
                    <Widget color="text-yellow-500" type="vertical" iconName="process" :widgetText=language.processing
                        :widgetValue="shipmentsCount.processing" />
                    </Link>

                    <Link href="/dashboard/shipments?status=20">
                    <Widget color="text-green-500" type="vertical" iconName="hand" :widgetText=language.shipments_received
                        :widgetValue="shipmentsCount.received" />
                    </Link>

                    <Link href="/dashboard/shipments?status=10">
                    <Widget color="text-gray-500" type="vertical" iconName="shipment" :widgetText=language.created_shipments
                        :widgetValue="shipmentsCount.created" />
                    </Link>

                    <Link href="/dashboard/shipments?status=-1">
                    <Widget color="text-red-500" type="vertical" iconName="cancel"
                        :widgetText=language.the_canceled_shipments :widgetValue="shipmentsCount.canceled" />
                    </Link>
                </div>
            </div>

            <!-- Latest Client Registered-->
            <div class="
                        border
                        rounded
                        border-gray-200
                        p-3
                        sm:p-5 sm:mx-6
                        lg:mx-8
                        bg-white
                        mt-7
                    "
                v-if="$page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('show_latest_clients')">
                <div class="flex justify-between items-baseline">
                    <h2 class="text-white text-lg mb-4 font-semibold">
                        {{ language.last_registered_customer }}
                    </h2>
                    <Button class="" :href="route('stores.')"> {{ language.view_all }}</Button>
                </div>

                <div class="flex flex-col">
                    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="overflow-x-auto border rounded">
                                <table class="min-w-full">
                                    <thead class="border-b bg-gray-100 text-sm">
                                        <tr class="whitespace-nowrap">
                                            <th class="pb-4 pt-6 px-6">
                                                {{ language.Store_number }}
                                            </th>
                                            <th class="pb-4 pt-6 px-6"> {{ language.Store_name }} </th>
                                            <th class="pb-4 pt-6 px-6">
                                                {{ language.Store_owner_name }}
                                            </th>
                                            <th class="pb-4 pt-6 px-6">
                                                {{ language.city }}
                                            </th>
                                            <th class="pb-4 pt-6 px-6">
                                                {{ language.Neighborhood }}
                                            </th>
                                            <th class="pb-4 pt-6 px-6">{{ language.number_of_shipments }}</th>
                                            <th class="pb-4 pt-6 px-6">{{ language.Status }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="store in stores" :key="store.id"
                                            class="hover:bg-gray-100 whitespace-nowrap focus-within:bg-gray-100">
                                            <td class="border-t">
                                                <Link class="flex justify-center px-6 py-4 focus:text-indigo-500" :href="
                                                    route('stores.show', store)
                                                ">
                                                {{ store.id }}
                                                </Link>
                                            </td>
                                            <td class="border-t text-center">
                                                {{ store.name }}
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
                                                {{ store.shipments }}
                                            </td>


                                            <td class=" text-center">
                                                <p class="rounded-xl text-green-700 bg-green-200 py-1 px-4 w-fit text-xs m-auto"
                                                    v-if="store.status == 'approved'">
                                                    {{ language.activated }}
                                                </p>

                                                <p class="rounded-xl text-red-700 bg-red-200 py-1 px-4 w-fit text-xs m-auto"
                                                    v-else>
                                                    {{ language.not_activated }}
                                                </p>
                                            </td>


                                        </tr>
                                        <tr v-if="stores.length === 0">
                                            <td class="px-6 py-4 border-t" colspan="4">
                                                {{ language.no_clients }} </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Clien Registered -->

            <!-- Latest Shipments Added-->
            <div class="
                        border
                        rounded
                        border-gray-200
                        p-3
                        sm:p-5 sm:mx-6
                        lg:mx-8
                        bg-white
                        mt-7
                    "
                v-if="$page.props.auth.account == 'admin' || $page.props.auth.permissions.includes('show_latest_shipments')">
                <div class="flex justify-between items-baseline">
                    <h2 class="text-gray-700 text-lg mb-4 font-semibold">
                        {{ language.latest_shipments }}
                    </h2>
                    <Button class="
                                bg-gray-200
                                !text-white
                                py-2
                                hover:!text-gray-200
                                text-sm
                            " :href="route('shipments')">{{ language.view_all }}</Button>
                </div>

                <div class="flex flex-col">
                    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="overflow-x-auto border rounded">
                                <table class="min-w-full">
                                    <thead class="border-b bg-gray-100">
                                        <tr>
                                            <th scope="col" class="pb-4 pt-6 px-6" :class="{
                                                'text-left': $page.props.locale == 'en',
                                                'text-right': $page.props.locale == 'ar',
                                            }">
                                                {{ language.Shipment_number }}
                                            </th>
                                            <th scope="col" class="pb-4 pt-6 px-6" :class="{
                                                'text-left': $page.props.locale == 'en',
                                                'text-right': $page.props.locale == 'ar',
                                            }">
                                                {{ language.The_recipients_name }}
                                            </th>
                                            <th scope="col" class="pb-4 pt-6 px-6" :class="{
                                                'text-left': $page.props.locale == 'en',
                                                'text-right': $page.props.locale == 'ar',
                                            }">
                                                {{ language.phone_number }}
                                            </th>
                                            <th scope="col" class="pb-4 pt-6 px-6" :class="{
                                                'text-left': $page.props.locale == 'en',
                                                'text-right': $page.props.locale == 'ar',
                                            }">
                                                {{ language.city }}
                                            </th>
                                            <th scope="col" class="pb-4 pt-6 px-6" :class="{
                                                'text-left': $page.props.locale == 'en',
                                                'text-right': $page.props.locale == 'ar',
                                            }">
                                                {{ language.added_date }}
                                            </th>
                                            <th scope="col" class="pb-4 pt-6 px-6" :class="{
                                                'text-left': $page.props.locale == 'en',
                                                'text-right': $page.props.locale == 'ar',
                                            }">
                                                {{ language.shipment_status }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-t" v-for="shipment in shipments" :key="shipment.id">
                                            <td class="
                                                        px-6
                                                        py-4
                                                        whitespace-nowrap
                                                        text-sm
                                                        font-medium
                                                        text-gray-900
                                                    ">
                                                {{ shipment.number }}
                                            </td>
                                            <td class="
                                                        text-sm text-gray-900
                                                        font-light
                                                        px-6
                                                        py-4
                                                        whitespace-nowrap
                                                    ">
                                                {{ shipment.receiverName }}
                                            </td>
                                            <td class="
                                                        text-sm text-gray-900
                                                        font-light
                                                        px-6
                                                        py-4
                                                        whitespace-nowrap
                                                    ">
                                                {{ shipment.receiverPhone }}
                                            </td>
                                            <td class="
                                                        text-sm text-gray-900
                                                        font-light
                                                        px-6
                                                        py-4
                                                        whitespace-nowrap
                                                    ">
                                                {{ shipment.city }}
                                            </td>
                                            <td class="
                                                        text-sm text-gray-900
                                                        font-light
                                                        px-6
                                                        py-4
                                                        whitespace-nowrap
                                                    ">
                                                {{ shipment.created_at }}
                                            </td>
                                            <td class="
                                                        text-sm text-gray-900
                                                        font-light
                                                        px-6
                                                        py-4
                                                        whitespace-nowrap
                                                    ">
                                                <p class="
                                                            rounded-xl
                                                            text-gray-700
                                                            bg-gray-200
                                                            py-1
                                                            px-4
                                                            w-fit
                                                            text-xs

                                                        " v-if="shipment.status == 10">
                                                    {{ language.created }}
                                                </p>
                                                <p class="
                                                            rounded-xl
                                                            text-orange-700
                                                            bg-orange-200
                                                            py-1
                                                            px-4
                                                            w-fit
                                                            text-xs

                                                        " v-else-if="shipment.status == 20">
                                                    {{ language.received }} </p>
                                                <p class="
                                                            rounded-xl
                                                            text-blue-700
                                                            bg-blue-200
                                                            py-1
                                                            px-4
                                                            w-fit
                                                            text-xs

                                                        " v-else-if="shipment.status == 35">
                                                    {{ language.processing_in_progress }} </p>
                                                <p class="
                                                            rounded-xl
                                                            text-green-700
                                                            bg-green-200
                                                            py-1
                                                            px-4
                                                            w-fit
                                                            text-xs

                                                        " v-else-if="shipment.status == 65">
                                                    {{ language.out_to_deliver }}
                                                </p>

                                                <p class="
                                                            rounded-xl
                                                            text-red-700
                                                            bg-red-200
                                                            py-1
                                                            px-4
                                                            w-fit
                                                            text-xs

                                                        " v-else-if="shipment.status == -100">
                                                    {{ language.audit }}
                                                </p>

                                                <p class="
                                                            rounded-xl
                                                            text-red-700
                                                            bg-red-200
                                                            py-1
                                                            px-4
                                                            w-fit
                                                            text-xs

                                                        " v-else-if="shipment.status == -1">
                                                    {{ language.canceled }}
                                                </p>

                                                <p class="
                                                            rounded-xl
                                                            text-green-700
                                                            bg-green-200
                                                            py-1
                                                            px-4
                                                            w-fit
                                                            text-xs

                                                        " v-else-if="shipment.status == 100">
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
import FlashMessages from "@/Components/FlashMessages.vue";

export default {
    components: {
        Head,
        PageHeader,
        Widget,
        Chart,
        Button,
        Link,
        FlashMessages,
    },
    props: {
        shipmentsCount: Object,
        stats: Object,
        shipments: Object,
        stores: Object,
        monthlyShipments: Object,
        monthlyProfit: Object,
        language: Object,
    },
    methods: {
        data(data) {
            let rate = [];
            data.forEach(function (value) {
                rate[value.month - 1] = value.data;
            });
            return rate;
        },

    },
    data() {
        return {
            shipmentsDataChart: {
                labels: ['يناير', 'فبراير', 'مارس', 'ابريل', 'مايو', 'يونيو', 'يوليو', 'اغسطس', 'سبتمبر', 'اكتوبر', 'نوفمبر', 'ديسمبر'],
                datasets: [
                    {
                        label: this.language.number_of_shipments,
                        data: this.data(this.monthlyShipments),
                        backgroundColor: "rgba(54,73,93,.5)",
                        borderColor: "#36495d",
                        borderWidth: 3,
                    }
                ],
            },
            profitDataChart: {
                labels: ['يناير', 'فبراير', 'مارس', 'ابريل', 'مايو', 'يونيو', 'يوليو', 'اغسطس', 'سبتمبر', 'اكتوبر', 'نوفمبر', 'ديسمبر'],
                datasets: [
                    {
                        label: this.language.Profits_sr,
                        data: this.data(this.monthlyProfit),
                        backgroundColor: "rgba(54,73,93,.5)",
                        borderColor: "#36495d",
                        borderWidth: 3,
                    }
                ],
            }
        }
    },
    layout: BreezeAuthenticatedLayout,

};
</script>

<style>
th,
td {
    vertical-align: middle;
}
</style>
