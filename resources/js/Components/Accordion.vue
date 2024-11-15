<template>
  
  <div class="">
    <div class="tab__header">
      <a
        href="#"
        class="tab__link p-4 bg-gray-100 hover:bg-blue-darker no-underline text-gray-500 border-b-2 border-white flex justify-between"
        @click.prevent="isActive  = !isActive "
      >
        <strong>{{ title }}</strong>
        <span class="down-Arrow" v-show="!isActive ">&#9660;</span>
        <span class="up-Arrow" v-show="isActive ">&#9650;</span>
      </a>
    </div>
    <div v-if="isActive" class="tab__content p-2"><slot /></div>
  </div>
</template>

<script>
export default {
  props: ["title","isActive"],

  mounted() {
    console.log(this.isActive)
  },
};
</script>
