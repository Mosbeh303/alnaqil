<template>
  <div ref="searchableSelect">
    <div class="relative">
      <Input
        v-if="myOptions.length == 0"
        type="text"
        class="mt-4 block w-full mb-4"
        :Placeholder="my_Place_holder_when_Nothing_Found"
        disabled
      />

      <Input
        v-else
        v-model="myComputedValue"
        @focus="(showList = true), this.updateVMValue(myComputedValue)"
        type="text"
        class="mt-4 block w-full mb-4"
        :Placeholder="my_Place_holder"
        ref="mySearchInput"
      />
      <div v-if="myOptions.length > 0">
        <div
          v-if="showList == false"
          class="absolute inset-y-0 pr-3 flex items-center"
          :class="{
            'left-0': $page.props.locale == 'ar',
            'right-0': $page.props.locale == 'en',
          }"
          @click="(showList = true), this.updateVMValue('')"
        >
          <svg
            class="h-4 w-4 text-gray-700"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path d="M19 9l-7 7-7-7"></path>
          </svg>
        </div>
        <div
          v-else
          class="absolute inset-y-0 pr-3 flex items-center"
          :class="{
            'left-0': $page.props.locale == 'ar',
            'right-0': $page.props.locale == 'en',
          }"
          @click="showList = false"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="h-4 w-4 text-gray-700"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M4.5 15.75l7.5-7.5 7.5 7.5"
            />
          </svg>
        </div>
      </div>
      <div>
        <ul
          v-if="myOptions.length > 0"
          class="list-none bg-white border border-gray-300 rounded-md shadow-md absolute top-10 w-full z-10"
          :class="{
            'left-0': $page.props.locale == 'ar',
            'right-0': $page.props.locale == 'en',
          }"
        >
          <li
            v-show="showList && !nothingFound"
            v-for="(result, index) in filteredResults"
            :key="index"
            @click.stop="optionSelected(result)"
            class="px-4 py-2 cursor-pointer hover:bg-gray-100"
          >
            <button type="button">
              {{ result }}
            </button>
          </li>
          <li v-show="showList && filteredResults.length === 0 && myComputedValue && myComputedValue.length > 0">
           <button type="button">{{ $page.props.language.nothing_found }} </button>
           </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
export default {
  components: {
    Button,
    Input,
  },
  layout: BreezeAuthenticatedLayout,

  props: {
    my_Place_holder: String,
    my_Place_holder_when_Nothing_Found: String,
    myVModel: String,
    myOptions: {
      type: Array,
      default: () => [],
    },
  },

  mounted() {
    window.addEventListener("click", this.handleClickOutside);
  },

  data() {
    return {
      noTerretory: false,
      showList: false,
      choiceDone: true,
      nothingFound: false,
    };
  },
  watch: {
    myVModel(value) {
      this.$emit("update:myVModel", value);
    },
  },
  computed: {
    myComputedValue: {
      get() {
        return this.myVModel;
      },
      set(value) {
        this.updateVMValue(value);
      },
    },

    filteredResults() {
    const regex = new RegExp(this.myComputedValue, "i");
    const filteredList = this.myOptions.filter((el) => {
      return regex.test(el);
    });

    return filteredList;
  },
  },

  methods: {
    optionSelected(nameEN) {
      this.updateVMValue(nameEN);
      this.showList = false;
      this.choiceDone = true;
    },

    handleClickOutside() {
      if (this.choiceDone == false) {
        this.updateVMValue("");
      }
    },

    updateVMValue(value) {
      this.$emit("update:myVModel", value);
      this.choiceDone = false;
    },
    tryAgain() {
      this.nothingFound = false;
      this.showList = true;

      this.updateVMValue("");
    },
  },
};
</script>
