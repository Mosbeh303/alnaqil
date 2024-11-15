<template>
  <div>
    <div
      class="max-w-xl p-5 m-4 mx-auto text-gray-800 bg-white border border-gray-200 rounded sm:p-8"
      id="print"
      ref="content"
    >
      <div class="flex items-center justify-between">
        <div>
          <Barcode
            :value="shipment.number"
            :format="'CODE39'"
            :lineColor="'#000'"
            :width="1"
            :height="50"
          />
        </div>
        <div>
          <Logo class="block w-32 h-auto mt-4" />
        </div>
      </div>

      <div class="flex flex-col" :class="{ '!flex-col-reverse': shipment.return }">
        <div
          class="flex items-start justify-between mt-8"
          :class="{ 'items-center': shipment.return }"
        >
          <div>
            <p class="mb-4 text-xs font-bold sm:text-lg">
              <span v-if="!shipment.return">{{ $page.props.language.from }}</span>
              <span class="text-base sm:text-lg" v-if="shipment.pickupAddress && shipment.storeType == 'platform'">{{ shipment.pickupAddress.name }} </span>
              <span class="text-base sm:text-lg" v-else>{{ shipment.storeName }} </span>
            </p>
            <p class="mb-4 text-xs font-bold sm:text-lg">
              <span v-if="!shipment.pickupAddress" class="text-base"
                >{{ shipment.storeCity }},<span class="text-base"
                  >{{ shipment.storeNeighborhood }}
                </span>
              </span>
              <span v-else class="text-base"
                >{{ shipment.pickupAddress.city }},<span class="text-base"
                  >{{ shipment.pickupAddress.neighborhood }}
                </span>
                </span>
            </p>

            <p class="mb-4 text-xs font-bold sm:text-lg">
              <span class="text-base"> {{ shipment.storePhone }}</span>
            </p>

            <p class="mb-4 text-xs font-bold sm:text-lg">
              {{ $page.props.language.the_number_of_pieces }} :
              <span class="text-base">{{ shipment.numberOfPieces }} </span>
            </p>
            <p class="mb-4 text-xs font-bold sm:text-lg">
              {{ $page.props.language.package_contents }} :
              <span class="text-base">{{ shipment.details }} </span>
            </p>
            <p class="mb-4 text-xs font-bold sm:text-lg">
              {{ $page.props.language.package_weight }} :
              <span class="text-base"
                >{{ shipment.weight }} {{ $page.props.language.kg }}</span
              >
            </p>
          </div>
          <div>
            <p v-if="!shipment.return" class="mb-4 text-xs font-bold sm:text-lg">
              {{ $page.props.language.date }} :
              <span class="text-base sm:text-lg">{{ shipment.created_at }} </span>
            </p>

            <div v-if="shipment.return">
              <p>
                <span>
                  <span v-if="shipment.return" style="color: green"> ☑ </span>
                  <span v-else style="color: gray"> □ </span>
                </span>
                {{ $page.props.language.recovery }}
              </p>
              <p>
                <span>
                  <span v-if="shipment.exchange" style="color: green"> ☑ </span>
                  <span v-else style="color: gray"> □ </span>
                </span>
                {{ $page.props.language.replacing }}
              </p>
              <p>
                <span>
                  <span v-if="shipment.refrigerated" style="color: green"> ☑ </span>
                  <span v-else style="color: gray"> □ </span>
                </span>
                {{ $page.props.language.refrigerated_transport }}
              </p>
            </div>
          </div>
        </div>

        <div class="flex justify-center w-full my-8">
          <hr />
        </div>

        <div
          class="flex items-center justify-between mt-12"
          :class="{ '!items-start': shipment.return }"
        >
          <div>
            <p class="mb-4 text-xs font-bold sm:text-lg">
              <span> {{ $page.props.language.to }} </span>
              <span class="text-base sm:text-lg">{{ shipment.receiverName }}</span>
            </p>
            <p class="mb-4 text-xs font-bold sm:text-lg">
              <span class="text-base sm:text-lg"
                >{{ shipment.city }}, {{ shipment.neighborhood }}, {{ shipment.street }}
              </span>
            </p>
            <p class="mb-4 text-xs font-bold sm:text-lg">
              <span class="text-base sm:text-lg">{{ shipment.receiverPhone }} </span>
            </p>
            <p class="mb-4 text-xs font-bold sm:text-lg">
              {{ $page.props.language.payment_on_receipt }} <br />
              <span v-if="!shipment.cod" class="block mt-4 text-base sm:text-lg">{{
                $page.props.language.prepaid
              }}</span>
              <span v-else class="block mt-4 text-base sm:text-lg"
                >{{ shipment.cod }} {{ $page.props.language.riyal }}
              </span>
            </p>
          </div>
          <div>
            <p v-if="shipment.return" class="mb-4 text-xs font-bold sm:text-lg">
              {{ $page.props.language.creation_date }}:
              <span class="text-base sm:text-lg">{{ shipment.created_at }} </span>
            </p>
            <div v-if="!shipment.return">
              <p>
                <span>
                  <span v-if="shipment.return" style="color: green"> ☑ </span>
                  <span v-else style="color: gray"> □ </span>
                </span>
                {{ $page.props.language.recovery }}
              </p>
              <p>
                <span>
                  <span v-if="shipment.exchange" style="color: green"> ☑ </span>
                  <span v-else style="color: gray"> □ </span>
                </span>
                {{ $page.props.language.replacing }}
              </p>
              <p>
                <span>
                  <span v-if="shipment.refrigerated" style="color: green"> ☑ </span>
                  <span v-else style="color: gray"> □ </span>
                </span>
                {{ $page.props.language.refrigerated_transport }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- <div class="flex justify-center gap-2">
            <a :href="route('shipments.policy', shipment.id)" class="flex items-center px-4 py-3 my-4 text-xs font-semibold tracking-widest text-gray-600 uppercase transition duration-150 ease-in-out bg-gray-200 border border-transparent rounded-md w-fit hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 hover:text-white focus:shadow-outline-gray" >تحميل البوليصة</a>
            <a v-if="shipment.status == 100 || shipment.status == -100" target="_blank" :href="route('shipments.invoice.pdf', shipment.id)" class="flex items-center px-4 py-3 my-4 text-xs font-semibold tracking-widest text-gray-600 uppercase transition duration-150 ease-in-out bg-gray-200 border border-transparent rounded-md w-fit hover:text-white hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray" >تحميل الفاتورة</a>
    </div> -->
</template>

<script>
import { Head } from "@inertiajs/vue3";
import Logo from "@/Components/ApplicationLogo.vue";
import Barcode from "@/Components/Barcode.vue";
import Qrcode from "qrcode.vue";
import Button from "@/Components/Button.vue";

export default {
  components: {
    Head,
    Logo,
    Barcode,
    Qrcode,
    Button,
  },
  props: ["shipment"],
  language: Object,
  data() {
    return {
      size: 150,
    };
  },
};
</script>
