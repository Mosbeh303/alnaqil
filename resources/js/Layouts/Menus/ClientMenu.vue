<template>
  <div class="p-4 mt-4 main-menu">
    <NavLink
      :href="route('dashboard')"
      :active="route().current('dashboard')"
      @navlinkclick="clicked"
    >
      <icon
        name="dashboard"
        class="w-4 h-4 ml-2 text-gray-400"
        :class="
          isUrl('dashboard') ? 'fill-gray-600' : 'fill-gray-400 group-hover:fill-white'
        "
      ></icon>
      {{ this.$page.props.language.dashboard }}
    </NavLink>

    <NavLink
      :href="route('shipments')"
      :active="route().current('shipments')"
      :submenus="submenus.shipments"
      @navlinkclick="clicked"
    >
      <icon name="shipment" class="w-4 h-4 ml-2 text-gray-400"></icon>
      {{ this.$page.props.language.shipments }}
    </NavLink>

    <NavLink
      :href="route('stores.summary')"
      :active="route().current('stores.summary')"
      @navlinkclick="clicked"
    >
      <icon name="shipment" class="w-4 h-4 ml-2 text-gray-400"></icon>
      {{ this.$page.props.language.account_statement }}
    </NavLink>

    <NavLink
      :href="route('invoices')"
      :active="route().current('invoices')"
      @navlinkclick="clicked"
    >
      <icon name="invoice" class="w-4 h-4 ml-2 text-gray-400"></icon>
      {{ this.$page.props.language.invoices }}
    </NavLink>

    <NavLink
      :href="route('stores.invoice')"
      :active="route().current('stores.invoice')"
      @navlinkclick="clicked"
      v-if="this.$page.props.auth.storeFeature.invoice"
    >
      <icon name="invoice" class="w-4 h-4 ml-2 text-gray-400"></icon>
      {{ this.$page.props.language.tax_invoice }}
    </NavLink>

    <NavLink
      :href="route('users.edit')"
      :active="route().current('users.edit')"
      @navlinkclick="clicked"
    >
      <icon name="cog" class="w-4 h-4 ml-2 text-gray-400"></icon>
      {{ this.$page.props.language.account_settings }}
    </NavLink>

    <NavLink
      :href="route('users.api')"
      :active="route().current('users.api')"
      @navlinkclick="clicked"
    >
      <icon name="api" class="w-4 h-4 ml-2 text-gray-400"></icon>
      {{ this.$page.props.language.link_settings }}
    </NavLink>
  </div>
</template>

<script>
import NavLink from "@/Components/NavLink.vue";
import Icon from "@/Components/Icon.vue";

export default {
  data() {
    return {
      submenus: {
        shipments: [
          {
            text: this.$page.props.language.active_shipments,
            href: route("shipments"),
          },
        ],
      },
    };
  },
  props: {
    language: Object,
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
