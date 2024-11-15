<template>
  <div class="p-4 mt-4 main-menu">
    <NavLink
      :href="route('dashboard')"
      :active="route().current('dashboard')"
      @navlinkclick="clicked"
    >
      <icon
        name="dashboard"
        class="w-4 h-4 text-gray-400"
        :class="{
          'fill-gray-600': isUrl('dashboard'),
          'fill-gray-400 group-hover:fill-white ': !isUrl('dashboard'),
          'mr-2': $page.props.locale == 'en',
          'ml-2': $page.props.locale == 'ar',
        }"
      ></icon>
      {{ $page.props.language.dashboard }}
    </NavLink>

    <div>
      <NavLink
        :isActive="activeDropdown === 'shipments'"
        @click="activeDropdown = 'shipments'"
        :href="route('shipments')"
        :active="isUrl('shipments')"
        :submenus="submenus.shipments"
        @navlinkclick="clicked"
        v-if="
          this.$page.props.auth.account == 'admin' ||
          this.$page.props.auth.permissions.find((a) => a.includes('shipments'))
        "
      >
        <icon
          name="shipment"
          class="w-4 h-4 text-gray-400"
          :class="{
            'mr-2': $page.props.locale == 'en',
            'ml-2': $page.props.locale == 'ar',
          }"
        ></icon>

        {{ $page.props.language.shipments }}
      </NavLink>

      <NavLink
        :isActive="activeDropdown === 'clients'"
        @click="activeDropdown = 'clients'"
        :href="route('stores.')"
        :active="route().current('stores')"
        :submenus="submenus.stores"
        @navlinkclick="clicked"
        v-if="
          this.$page.props.auth.account == 'admin' ||
          this.$page.props.auth.permissions.find((a) => a.includes('stores'))
        "
      >
        <icon
          name="truck"
          class="w-4 h-4 text-gray-400"
          :class="{
            'mr-2': $page.props.locale == 'en',
            'ml-2': $page.props.locale == 'ar',
          }"
        ></icon>

        {{ $page.props.language.clients }}
      </NavLink>

      <NavLink
        :isActive="activeDropdown === 'delegates'"
        @click="activeDropdown = 'delegates'"
        :href="route('operator')"
        :active="route().current('operator')"
        :submenus="submenus.operators"
        @navlinkclick="clicked"
        v-if="
          this.$page.props.auth.account == 'admin' ||
          this.$page.props.auth.permissions.find((a) => a.includes('operators'))
        "
      >
        <icon
          name="truck"
          class="w-4 h-4 text-gray-400"
          :class="{
            'mr-2': $page.props.locale == 'en',
            'ml-2': $page.props.locale == 'ar',
          }"
        ></icon>

        {{ $page.props.language.delegates }}
      </NavLink>

      <NavLink
        :isActive="activeDropdown === 'employees'"
        @click="activeDropdown = 'employees'"
        v-if="
          this.$page.props.auth.account == 'admin' ||
          this.$page.props.auth.permissions.find((a) => a.includes('employees'))
        "
        :href="route('operator')"
        :active="route().current('operator')"
        :submenus="submenus.employees"
        @navlinkclick="clicked"
      >
        <icon
          name="truck"
          class="w-4 h-4 text-gray-400"
          :class="{
            'mr-2': $page.props.locale == 'en',
            'ml-2': $page.props.locale == 'ar',
          }"
        ></icon>

        {{ $page.props.language.employees }}
      </NavLink>

      <NavLink
        :isActive="activeDropdown === 'reports'"
        @click="activeDropdown = 'reports'"
        :href="route('shipments.statement')"
        :active="route().current('shipments.statement')"
        :submenus="submenus.reports"
        @navlinkclick="clicked"
      >
      <icon
          name="folder"
          class="w-4 h-4 text-gray-400"
          :class="{
            'mr-2': $page.props.locale == 'en',
            'ml-2': $page.props.locale == 'ar',
          }"
        ></icon>

        {{ $page.props.language.reports }}
      </NavLink>
    </div>

    <NavLink
      :href="route('marketers')"
      :active="route().current('marketers')"
      @navlinkclick="clicked"
      v-if="
        this.$page.props.auth.account == 'admin' ||
        this.$page.props.auth.permissions.includes('show_marketers')
      "
    >
      <icon
        name="users"
        class="w-4 h-4 text-gray-400"
        :class="{
          'mr-2': $page.props.locale == 'en',
          'ml-2': $page.props.locale == 'ar',
        }"
      ></icon>
      {{ this.$page.props.language.marketers }}
    </NavLink>

    <!-- <NavLink
            :href="route('vehicles.groups')"
            :active="route().current('vehicles.groups')"
            @navlinkclick = "clicked"
            v-if="this.$page.props.auth.account == 'admin' || this.$page.props.auth.permissions.includes('vehicles')"
        >
            <icon name="truck" class="w-4 h-4 ml-2 text-gray-400"></icon>
            السيارات
        </NavLink> -->

    <NavLink
      :href="route('partners.index')"
      :active="route().current('partners.index')"
      @navlinkclick="clicked"
      v-if="
        this.$page.props.auth.account == 'admin' ||
        this.$page.props.auth.permissions.includes('partners')
      "
    >
      <icon
        name="stars"
        class="w-4 h-4 text-gray-400"
        :class="{
          'mr-2': $page.props.locale == 'en',
          'ml-2': $page.props.locale == 'ar',
        }"
      ></icon>
      {{ this.$page.props.language.Partners }}
    </NavLink>

    <NavLink
      :href="route('complaints.index')"
      :active="route().current('complaints.index')"
      @navlinkclick="clicked"
      v-if="
        this.$page.props.auth.account == 'admin' ||
        this.$page.props.auth.permissions.includes('complaints')
      "
    >
      <icon
        name="warning"
        class="w-4 h-4 text-gray-400"
        :class="{
          'mr-2': $page.props.locale == 'en',
          'ml-2': $page.props.locale == 'ar',
        }"
      >
      </icon>
      {{ this.$page.props.language.complaints }}
    </NavLink>

    <NavLink
      :href="route('accounts')"
      :active="route().current('accounts')"
      @navlinkclick="clicked"
      v-if="
        this.$page.props.auth.account == 'admin' ||
        this.$page.props.auth.permissions.includes('wallets_accounts') ||
        this.$page.props.auth.permissions.includes('receipt_employees_dues_accounts')
      "
    >
      <icon
        name="cash"
        class="w-4 h-4 text-gray-400"
        :class="{
          'mr-2': $page.props.locale == 'en',
          'ml-2': $page.props.locale == 'ar',
        }"
      ></icon>
      {{ this.$page.props.language.accounts }}
    </NavLink>

    <NavLink
      :href="route('settings.general')"
      :active="route().current('settings')"
      @navlinkclick="clicked"
      v-if="
        this.$page.props.auth.account == 'admin' ||
        this.$page.props.auth.permissions.includes('settings')
      "
    >
      <icon
        name="settings"
        class="w-4 h-4 text-gray-400"
        :class="{
          'mr-2': $page.props.locale == 'en',
          'ml-2': $page.props.locale == 'ar',
        }"
      ></icon>
      {{ this.$page.props.language.System_settings }}
    </NavLink>

    <NavLink
      :href="route('users.edit')"
      :active="route().current('users.edit')"
      @navlinkclick="clicked"
    >
      <icon
        name="cog"
        class="w-4 h-4 text-gray-400"
        :class="{
          'mr-2': $page.props.locale == 'en',
          'ml-2': $page.props.locale == 'ar',
        }"
      ></icon>
      {{ this.$page.props.language.account_settings }}
    </NavLink>
  </div>
</template>

<script>
import NavLink from "@/Components/NavLink.vue";
import Icon from "@/Components/Icon.vue";

export default {
  props: {
    language: Object,
  },
  data() {
    return {
      activeDropdown: null,
      submenus: {
        shipments: [
          {
            text: this.$page.props.language.active_shipments,
            href: route("shipments"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("index_shipments"),
          },
          {
            text: this.$page.props.language.Shipment_inquiry,
            href: route("shipments.query"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("query_shipments"),
          },
          {
            text: this.$page.props.language.Track_shipments,
            href: route("shipments.track"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("track_shipments"),
          },

          {
            text: this.$page.props.language.Unsuccessful_delivery_attempts,
            href: route("shipments.failed"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("failed_shipments"),
          },
          {
            text: this.$page.props.language.receiving_shipments,
            href: route("shipments.receive"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("receive_shipments"),
          },
          {
            text: this.$page.props.language.Quick_Action,
            href: route("shipments.proceedings"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("warehouse_shipments") ||
              this.$page.props.auth.permissions.includes("status_shipments") ||
              this.$page.props.auth.permissions.includes("operator_shipments"),
          },
        ],
        operators: [
          {
            text: this.$page.props.language.receipt_offices,
            href: route("groupes", { for: "rec" }),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("show_rec_operators"),
          },
          {
            text: this.$page.props.language.delivery_handlers,
            href: route("groupes", { for: "del" }),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("show_del_operators"),
          },
          {
            text: this.$page.props.language.distinguished_delegates,
            href: route("groupes", { for: "vip" }),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("show_vip_operators"),
          },
          {
            text: this.$page.props.language.car_receipt,
            href: route("vehicles.groups"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("index_vehicles"),
          },
        ],
        employees: [
          {
            text: this.$page.props.language.sections,
            href: route("employee.departments"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("show_employees"),
          },
          {
            text: this.$page.props.language.all_groups,
            href: route("groupes"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("show_employees"),
          },
          {
            text: this.$page.props.language.all_employees,
            href: route("employee"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("show_employees"),
          },
        ],

        stores: [
          {
            text: this.$page.props.language.all_clients,
            href: route("stores."),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("index_stores"),
          },

          {
            text: this.$page.props.language.client_dues,

            href: route("stores.dues"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("dues_stores"),
          },

          {
            text: this.$page.props.language.invoices_stores,

            href: route("invoices"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("invoices_stores"),
          },

          {
            text: this.$page.props.language.clients_wallets,

            href: route("stores.wallets"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("wallets_stores"),
          },

          {
            text: this.$page.props.language.store_account_statement,

            href: route("stores.summary"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("summary_stores"),
          },

          {
            text: this.$page.props.language.bond_disclosure,
            href: route("stores.vouchers"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("vouchers_stores"),
          },
          {
            text: this.$page.props.language.receivers_adresses,
            href: route("receivers"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("receivers_stores"),
          },
        ],
        reports: [
          {
            text: this.$page.props.language.Accounting_shipment_statement,
            href: route("shipments.statement"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("accounting_statement_shipments"),
          },
          {
            text: this.$page.props.language.Shipment_statement,
            href: route("shipments.summary"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("summary_shipments"),
          },
          {
            text: this.$page.props.language.tax_invoice_statement,
            href: route("stores.invoice"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("invoice_stores"),
          },
          {
            text: this.$page.props.language.Finance,
            href: route("financials"),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("financials"),
          },
          {
            text: this.$page.props.language.summary_jnt,
            href: route("shipments.summary", {'for' : 'jnt'}),
            display:
              this.$page.props.auth.account == "admin" ||
              this.$page.props.auth.permissions.includes("jnt_summary_shipments"),
          },
        ],
      },
    };
  },

  components: {
    NavLink,
    Icon,
  },

  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1);
      if (urls[0] === "") {
        return currentUrl === "";
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length;
    },
    clicked() {
      this.$emit("navlinkclick");
    },
  },
};
</script>
