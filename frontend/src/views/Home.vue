<style lang="scss">
@import "@core/scss/vue/libs/vue-select.scss";
@import "@core/scss/vue/libs/vue-flatpicker.scss";
.robot-data-list li {
  padding: 8px 0px;
  font-size: larger;
}
</style>
<template>
  <div>
    <b-row>
      <b-col md="7">
        <b-card class="text-center">
          <b-card-header>
            <!-- title and subtitle -->
            <div>
              <b-form-group>
                <b-form-select
                  v-model="selected"
                  label="robot"
                  v-bind:placeholder="$t('selected')"
                  :options="option"
                ></b-form-select>
              </b-form-group>
            </div>
            <!--/ title and subtitle -->

            <!-- datepicker -->
            <div class="d-flex align-items-center">
              <feather-icon icon="CalendarIcon" size="16" />
              <flat-pickr
                v-model="rangeDate"
                :config="{ mode: 'range' }"
                class="form-control flat-picker bg-transparent border-0 shadow-none"
                placeholder="YYYY/MM/DD"
              />
            </div>
            <!-- datepicker -->
          </b-card-header>

          <b-row class="my-2">
            <!-- Left: Product Image Container -->
            <b-col
              cols="12"
              md="4"
              class="d-flex align-items-center justify-content-center mb-2 mb-md-0"
            >
              <div class="d-flex align-items-center justify-content-center">
                <b-img
                  :src="robot_data.img"
                  :alt="`Image of ${robot_data.name}`"
                  class="product-img"
                  fluid
                />
              </div>
            </b-col>

            <!-- Right: Product Details -->
            <b-col cols="12" md="8">
              <ul class="robot-data-list list-unstyled text-left">
                <li>
                  <span>{{ $t("home.robotData.TotalTime") }} : </span>
                </li>
                <li>
                  <span>{{ $t("home.robotData.AverageTime") }} : </span>
                </li>
                <li>
                  <span>{{ $t("home.robotData.RoomsCount") }} : </span>
                </li>
                <li>
                  <span>{{ $t("home.robotData.CorridorsCount") }} : </span>
                </li>
                <li>
                  <span>{{ $t("home.robotData.completed") }} : </span>
                </li>
              </ul>
            </b-col>
          </b-row>
          <!-- <b-card-text>
            With supporting text below as a natural lead-in to additional content
          </b-card-text> -->
        </b-card>
      </b-col>

      <b-col md="5">
        <b-card>
          <b-card-title class="text-left">{{ $t("home.taskTable.Title") }}</b-card-title>
          <b-table responsive="xl" :items="items" />
        </b-card>
      </b-col>
    </b-row>

    <b-row>
      <b-col md="6">
        <b-card>
          <b-card-title>{{ $t("home.chart.Title") }}</b-card-title>
          <vue-apex-charts
            type="bar"
            height="350"
            :options="chartofday.chartOptions"
            :series="chartofday.series"
          ></vue-apex-charts>
        </b-card>
      </b-col>
      <b-col md="6">
        <b-card>
          <b-card-title>{{ $t("home.chart.Title") }}</b-card-title>
          <vue-apex-charts
            type="bar"
            height="350"
            :options="chartofunit.chartOptions"
            :series="chartofunit.series"
          ></vue-apex-charts>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import {
  BCard,
  BCardHeader,
  BCardText,
  BRow,
  BCol,
  BCardTitle,
  BFormGroup,
  BImg,
  BTable,
  BFormSelect,
} from "bootstrap-vue";
import flatPickr from "vue-flatpickr-component";
import AppEchartBar from "@core/components/charts/echart/AppEchartBar.vue";
import VueApexCharts from "vue-apexcharts";

export default {
  components: {
    BCard,
    BCardHeader,
    BCardText,
    BRow,
    BCol,
    BCardTitle,
    BFormGroup,
    BImg,
    BTable,
    BFormSelect,
    flatPickr,
    AppEchartBar,
    VueApexCharts,
  },
  data() {
    return {
      selected: null,
      option: [
        { value: null, text: "Please select an robot" },
        { value: "a", text: "CBOT 1" },
        { value: "b", text: "CBOT 2" },
        { value: "c", text: "CBOT 3" },
        { value: "d", text: "CBOT 4" },
      ],

      rangeDate: ["2021-05-01", "2021-05-10"],

      robot_data: {
        img: require("@/assets/images/robot/robot-1.png"),
        name: "English",
        total_time: "3 Hours 50 Minutes",
        averag_time: "3 Hours 24 Minutes",
        room_count: "125",
        corridors_count: "113",
        complate: "81%",
      },
      items: [
        {
          unit: "Surgery",
          floor: 3,
          duration: "3:00",
          time: "23/1/2021 15:00",
        },
        {
          unit: "Surgery",
          floor: 3,
          duration: "3:00",
          time: "23/1/2021 15:00",
        },
        {
          unit: "Surgery",
          floor: 3,
          duration: "3:00",
          time: "23/1/2021 15:00",
        },
        {
          unit: "Surgery",
          floor: 3,
          duration: "3:00",
          time: "23/1/2021 15:00",
        },
        {
          unit: "Surgery",
          floor: 3,
          duration: "3:00",
          time: "23/1/2021 15:00",
        },
      ],
      chartofday: {
        series: [
          {
            name: "Performed Tasks By Day",
            data: [2.3, 3.1, 4.0, 10.1, 4.0, 3.6, 3.2, 2.3, 1.4, 0.8, 0.5, 0.2],
          },
        ],
        chartOptions: {
          chart: {
            height: 350,
            type: "bar",
          },
          plotOptions: {
            bar: {
              borderRadius: 10,
              dataLabels: {
                position: "top", // top, center, bottom
              },
            },
          },
          dataLabels: {
            enabled: true,
            formatter: function (val) {
              return val; // + "%";
            },
            offsetY: -20,
            style: {
              fontSize: "12px",
              colors: ["#304758"],
            },
          },

          xaxis: {
            categories: [
              "Jan",
              "Feb",
              "Mar",
              "Apr",
              "May",
              "Jun",
              "Jul",
              "Aug",
              "Sep",
              "Oct",
              "Nov",
              "Dec",
            ],
            position: "bottom",
            axisBorder: {
              show: false,
            },
            axisTicks: {
              show: false,
            },
            crosshairs: {
              fill: {
                type: "gradient",
                gradient: {
                  colorFrom: "#D8E3F0",
                  colorTo: "#BED1E6",
                  stops: [0, 100],
                  opacityFrom: 0.4,
                  opacityTo: 0.5,
                },
              },
            },
            tooltip: {
              enabled: true,
            },
          },
          yaxis: {
            axisBorder: {
              show: false,
            },
            axisTicks: {
              show: false,
            },
            labels: {
              show: true,
              formatter: function (val) {
                return val; // + "%";
              },
            },
          },
          toolbox: {
            show: false,
          },
        },
      },

      chartofunit: {
        series: [
          {
            data: [21, 22, 10, 28, 46],
          },
        ],
        chartOptions: {
          chart: {
            height: 350,
            type: "bar",
            events: {
              click: function (chart, w, e) {
                // console.log(chart, w, e)
              },
            },
          },
          colors: ["#7367f0", "#ff9f43", "#9ca0a4", "#ff9f43", "#00cfe8"],
          plotOptions: {
            bar: {
              columnWidth: "45%",
              distributed: true,
              dataLabels: {
                position: "top", // top, center, bottom
              },
            },
          },
          dataLabels: {
            enabled: true,
            formatter: function (val) {
              return val; // + "%";
            },
            offsetY: -20,
            style: {
              fontSize: "12px",
              colors: ["#304758"],
            },
          },
          legend: {
            show: false,
          },
          xaxis: {
            categories: ["Surgery", "Emergency", "Pharmacy", "Orthopedic", "X-ray"],
            labels: {
              style: {
                colors: ["#7367f0", "#ff9f43", "#9ca0a4", "#ff9f43", "#00cfe8"],
                fontSize: "12px",
              },
            },
          },
          yaxis: {
            axisBorder: {
              show: false,
            },
            axisTicks: {
              show: false,
            },
            labels: {
              show: true,
              formatter: function (val) {
                return val; // + "%";
              },
            },
          },
        },
      },
    };
  },
};
</script>
