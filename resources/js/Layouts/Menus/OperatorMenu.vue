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
      {{ $page.props.language.dashboard }}
    </NavLink>
    <NavLink
      v-if="$page.props.auth.operatorType == 'rec' || $page.props.auth.operatorType == 'vip'"
      :href="route('operator.get.shipments.receive')"
      :active="route().current('operator.get.shipments.receive')"
      @navlinkclick="clicked"
    >
      <icon
        name="shipment"
        class="ml-2 w-4 h-4 text-gray-400"
        :class="
          isUrl('dashboard') ? 'text-gray-600' : 'text-gray-400 group-hover:text-white'
        "
      ></icon>
      {{ $page.props.language.receiving_shipments }}
    </NavLink>
    <NavLink
      v-if="
        $page.props.auth.operatorType == 'rec' || $page.props.auth.operatorType == 'vip'
      "
      :href="route('shipments.receive')"
      :active="route().current('shipments.receive')"
      @navlinkclick="clicked"
    >
      <icon
        name="shipment"
        class="ml-2 w-4 h-4 text-gray-400"
        :class="
          isUrl('dashboard') ? 'text-gray-600' : 'text-gray-400 group-hover:text-white'
        "
      ></icon>
      {{ $page.props.language.ReceiptFromStores }}
    </NavLink>
    <!-- <NavLink
      v-if="$page.props.auth.operatorType == 'vip'"
      :href="route('operator.get.shipments.receive')"
      :active="route().current('operator.get.shipments.receive')"
      @navlinkclick="clicked"
    >
      <icon
        name="shipment"
        class="ml-2 w-4 h-4 text-gray-400"
        :class="
          isUrl('dashboard') ? 'text-gray-600' : 'text-gray-400 group-hover:text-white'
        "
      ></icon>
      {{ $page.props.language.receipt_from_the_warehouse }}
    </NavLink> -->
  </div>
</template>

<script>
import NavLink from "@/Components/NavLink.vue";
import Icon from "@/Components/Icon.vue";

export default {
  data() {
    return {
      submenus: {},
    };
  },

  components: {
    NavLink,
    Icon,
  },
  props: {
    language: Object,
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
