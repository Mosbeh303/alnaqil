<template>
  <div class="overflow-hidden relative w-11/12 h-auto">
    <canvas :id="chartId" height="350"></canvas>
  </div>
</template>

<script>
import "@/chart.min.js";

export default {
  props: ["chartId", "_data"],
  data: () => ({
    chartData: {
      type: "line",
      data: "",
      options: {
        responsive: true,
        maintainAspectRatio: false,
        lineTension: 0.1,
        scales: {
          myScale: {
            //type: 'logarithmic',
            position: "left", // `axis` is determined by the position as `'y'`
            ticks: {
              beginAtZero: true,
              padding: 25,
            },
          },
        },
      },
    },
  }),
  mounted() {
    const ctx = document.getElementById(this.chartId);
    this.chartData.data = this._data;
    new Chart(ctx, this.chartData);
  },
};
</script>
