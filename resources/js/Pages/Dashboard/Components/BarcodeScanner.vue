<template>
  <div id="scanner">
    <StreamBarcodeReader
      v-if="scan"
      @decode="(a) => onDecode(a)"
      @loaded="() => onLoaded()"
    ></StreamBarcodeReader>
  </div>
</template>

<script>
import { StreamBarcodeReader } from "vue-barcode-reader";

export default {
  components: {
    StreamBarcodeReader,
  },
  data() {
    return {
      text: "",
      id: null,
    };
  },
  props: {
    msg: String,
    scan: Boolean,
    onDecode: Function
  },
  methods: {
    
    onLoaded() {
      console.log("load");
    },
  },
};
</script>

<style >
    #scanner .laser[data-v-9c322c14] {
        right: 19%;
    }

    #scanner video[data-v-9c322c14] {
        margin: auto;
    }
</style>
