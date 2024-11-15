<template>
    <div class="main-menu mt-4 p-4">
      <NavLink
        :href="route('dashboard')"
        :active="route().current('dashboard')"
        @navlinkclick="clicked"
      >
        <icon
          name="dashboard"
          class="ml-2 w-4 h-4 text-gray-400"
          :class="
            isUrl('dashboard') ? 'fill-gray-600' : 'fill-gray-400 group-hover:fill-white'
          "
        ></icon>
        {{ this.$page.props.language.dashboard }}
      </NavLink>

      <NavLink
        :isActive="activeDropdown === 'clients'"
        @click="activeDropdown = 'clients'"
        :href="route('stores.')"
        :active="route().current('stores')"
        :submenus="submenus.stores"
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

        {{ $page.props.language.clients }}
      </NavLink>

      <!-- <NavLink
        :href="route('shipments')"
        :active="route().current('shipments')"
        :submenus="submenus.shipments"
        @navlinkclick="clicked"
      >
        <icon name="shipment" class="ml-2 w-4 h-4 text-gray-400"></icon>
        {{ this.$page.props.language.shipments }}
      </NavLink> -->

      <!-- <NavLink
        :href="route('stores.summary')"
        :active="route().current('stores.summary')"
        @navlinkclick="clicked"
      >
        <icon name="shipment" class="ml-2 w-4 h-4 text-gray-400"></icon>
        {{ this.$page.props.language.account_statement }}
      </NavLink> -->

      <NavLink
        :href="route('marketers.commissions')"
        :active="route().current('marketers.commissions')"
        @navlinkclick="clicked"
      >
        <icon
          name="cash"
          class="ml-2 w-4 h-4 text-gray-400"
          :class="
            isUrl('commissions') ? 'fill-gray-600' : 'fill-gray-400 group-hover:fill-white'
          "
        ></icon>
        {{ this.$page.props.language.commissions }}
      </NavLink>


      <NavLink
        :href="route('users.edit')"
        :active="route().current('users.edit')"
        @navlinkclick="clicked"
      >
        <icon name="cog" class="ml-2 w-4 h-4 text-gray-400"></icon>
        {{ this.$page.props.language.account_settings }}
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
            stores: [
                {
                    text: this.$page.props.language.all_clients,
                    href: route("marketers.clients", this.$page.props.auth.user ),
                },
                {
                    text: this.$page.props.language.add_new_client,
                    href: route("stores.create"),
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
