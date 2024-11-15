<template>
  <div :dir=" $page.props.locale == 'ar' ? 'rtl'  : 'ltr'">
    <div class="min-h-screen relative">
      <aside
        class="aside bg-white top-0 bottom-0 no-print fixed shadow-md border-gray-100 transition-all duration-100 ease-in-out min-h-screen overflow-x-hidden z-10"
        :class="{
          'sm:w-60 w-0': showingNavOnDesktop,
          'sm:w-0 w-full': !showingNavOnDesktop,
          'border-l': $page.props.locale == 'en',
          'border-r': $page.props.locale == 'ar',
        }"
      >
        <div class="">
          <!-- Close Menu Button for mobile -->
          <button
            @click="showingNavOnDesktop = !showingNavOnDesktop"
            class="absolute left-3 top-5 sm:hidden"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-500"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
          <div class="shrink-0 flex items-center justify-center whitespace-nowrap">
            <Link :href="route('dashboard')">
              <BreezeApplicationLogo class="block w-32 h-auto mt-4" />
              <!-- <span class="text-lg font-bold mt-4 inline-block">كلاود اكسبرس</span> -->
            </Link>
          </div>

          <OperatorMenu
            @navlinkclick="clicked"
            v-if="$page.props.auth.account == 'operator'"
          />
          <MainMenu
            @navlinkclick="clicked"
            v-else-if="
              $page.props.auth.account == 'admin' ||
              $page.props.auth.account == 'employee'
            "
          />
          <ClientMenu
            @navlinkclick="clicked"
            v-else-if="$page.props.auth.account == 'client'"
          />
          <MarketerMenu
            @navlinkclick="clicked"
            v-else-if="$page.props.auth.account == 'marketer'"
          />
        </div>
      </aside>
      <div
        class="transition-all duration-100 ease-in-out"
        :class="{
          'sm:mr-60 mr-0': showingNavOnDesktop && $page.props.locale == 'ar',
          'sm:mr-0': !showingNavOnDesktop && $page.props.locale == 'ar',
          'sm:ml-60 ml-0': showingNavOnDesktop && $page.props.locale == 'en',
          'sm:ml-0': !showingNavOnDesktop && $page.props.locale == 'en',
        }"
      >
        <nav class="bg-white border-b border-gray-100 no-print">
          <!-- Primary Navigation Menu -->
          <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
              <div class="flex grow items-center">
                <button
                  class="ml-2 text-gray-500 focus:outline-none outline-none"
                  @click="showingNavOnDesktop = !showingNavOnDesktop"
                >
                  <svg
                    class="h-6 w-6"
                    stroke="currentColor"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"
                    />
                  </svg>
                </button>

                <form
                  @submit.prevent="submitTracking"
                  v-if="
                    $page.props.auth.account == 'admin' ||
                    $page.props.auth.permissions.includes('track_shipments')
                  "
                  class="relative w-full sm:w-1/2 md:w-1/3 mr-4 ml-4"
                >
                  <SearchInput
                    id="search"
                    type="text"
                    class="text-sm block w-full bg-gray-100 border-0 outline-none"
                    v-model="form.number"
                    autocomplete="search"
                    :placeholder="$page.props.language.track_shipment"
                  />
                  <button
                    :class="{
                      'outline-none absolute right-2 top-2': $page.props.locale == 'en',
                      'outline-none absolute left-2 top-2': $page.props.locale == 'ar',
                    }"
                    type="submit"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4 text-gray-400"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                      />
                    </svg>
                  </button>
                </form>

                <Link
                  v-if="
                    $page.props.auth.account == 'admin' ||
                    $page.props.auth.permissions.includes('warehouse_shipments') ||
                    $page.props.auth.permissions.includes('status_shipments') ||
                    $page.props.auth.permissions.includes('operator_shipments')
                  "
                  class="text-gray-600 text-sm mr-4 hidden md:block"
                  :href="route('shipments.proceedings')"
                >
                  {{ $page.props.language.quick_actions }}
                </Link>
              </div>

              <div class="hidden sm:flex sm:items-center">
                <!-- Lang Dropdown -->
                <div class="relative">
                  <BreezeDropdown
                    :align="$page.props.locale == 'ar' ? 'left' : 'right'"
                    width="48"
                  >
                    <template #trigger>
                      <span class="inline-flex rounded-md">
                        <button
                          type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                        >
                          <span v-if="$page.props.locale == 'en'">{{
                            language.English
                          }}</span>
                          <span v-else>{{ language.Arabic }}</span>

                          <svg
                            class="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </button>
                      </span>
                    </template>

                    <template #content>
                      <form :action="route('logout')" method="post">
                        <input type="hidden" name="_token" :value="$page.props.csrf" />
                        <a
                          v-if="$page.props.locale == 'ar'"
                          :href="route('set_locale', 'en')"
                          type="submit"
                          class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                        >
                          {{ language.English }}
                        </a>
                        <a
                          v-else
                          :href="route('set_locale', 'ar')"
                          type="submit"
                          class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                        >
                          {{ language.Arabic }}
                        </a>
                      </form>
                    </template>
                  </BreezeDropdown>
                </div>
              </div>

              <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                  <BreezeDropdown
                    :align="$page.props.locale == 'ar' ? 'left' : 'right'"
                    width="48"
                  >
                    <template #trigger>
                      <span class="inline-flex rounded-md">
                        <button
                          type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                        >
                          {{ $page.props.auth.user.name }}

                          <svg
                            class="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fill-rule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clip-rule="evenodd"
                            />
                          </svg>
                        </button>
                      </span>
                    </template>

                    <template #content>
                      <form :action="route('logout')" method="post">
                        <input type="hidden" name="_token" :value="$page.props.csrf" />
                        <button
                          type="submit"
                          class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                        >
                          {{ language.sign_out }}
                        </button>
                      </form>
                    </template>
                  </BreezeDropdown>
                </div>
              </div>

              <!-- Hamburger -->
              <div class="-mr-2 flex items-center sm:hidden">
                <button
                  @click="showingNavigationDropdown = !showingNavigationDropdown"
                  class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                >
                  <svg
                    class="h-6 w-6"
                    stroke="currentColor"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <path
                      :class="{
                        hidden: !showingNavigationDropdown,
                        'inline-flex': showingNavigationDropdown,
                      }"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                    <path
                      :class="{
                        hidden: showingNavigationDropdown,
                        'inline-flex': !showingNavigationDropdown,
                      }"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Responsive Navigation Menu -->
          <div
            :class="{
              block: showingNavigationDropdown,
              hidden: !showingNavigationDropdown,
            }"
            class="sm:hidden"
          >
            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
              <div class="px-4">
                <div class="font-medium text-base text-gray-800">
                  {{ $page.props.auth.user.name }}
                </div>
                <div class="font-medium text-sm text-gray-500">
                  {{ $page.props.auth.user.email }}
                </div>
              </div>

              <div class="mt-3 space-y-1">
                <form :action="route('logout')" method="post">
                  <input type="hidden" name="_token" :value="$page.props.csrf" />
                  <button
                    type="submit"
                    class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                  >
                    {{ language.sign_out }}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </nav>

        <!-- Page Content -->
        <main class="bg-white print">
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>

<script>
import BreezeApplicationLogo from "@/Components/ApplicationLogo.vue";
import BreezeDropdown from "@/Components/Dropdown.vue";
import BreezeDropdownLink from "@/Components/DropdownLink.vue";
import BreezeNavLink from "@/Components/NavLink.vue";
import BreezeResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, router } from "@inertiajs/vue3";
import MainMenu from "@/Layouts/Menus/MainMenu.vue";
import OperatorMenu from "@/Layouts/Menus/OperatorMenu.vue";
import ClientMenu from "@/Layouts/Menus/ClientMenu.vue";
import MarketerMenu from "@/Layouts/Menus/MarketerMenu.vue";
import SearchInput from "@/Components/Input.vue";
import Icon from "@/Components/Icon.vue";

export default {
  components: {
    BreezeApplicationLogo,
    BreezeDropdown,
    BreezeDropdownLink,
    BreezeNavLink,
    BreezeResponsiveNavLink,
    Link,
    MainMenu,
    SearchInput,
    Icon,
    OperatorMenu,
    ClientMenu,
    MarketerMenu
  },
  props: {
    language: Object,
  },

  data() {
    return {
      showingNavigationDropdown: false,
      showingLangSwitch: false,
      showingNavOnDesktop: true,
      form: this.$inertia.form({
        number: "",
      }),
    };
  },

//   beforeUnmount() {
//     //router.remember(this.$page.props.locale, 'locale')
//    },
//     mounted() {
//         const locale = this.$page.props.locale;
//         if (locale === 'en')
//             document.getElementsByTagName("body")[0].dir = "ltr";
//         if (locale === 'ar')
//             document.getElementsByTagName("body")[0].dir = "rtl";
//     },





  methods: {
    submitTracking() {
      this.form.get(this.route("shipments.getShipmentTracking", this.form.number), {
        preserveScroll: true,
        onError: () => this.form.reset(),
      });
    },
    clicked() {
      this.showingNavOnDesktop = true;
    },
  },
};
</script>
