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
                  v-model="setRobot"
                  label="robot"
                  v-on:change="selected_robot()"
                  v-bind:placeholder="$t('setRobot')"
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
                :config="{
                  mode: 'range',
                }"
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
                <b-img :src="robot_img" class="product-img" fluid />
              </div>
            </b-col>

            <!-- Right: Product Details -->
            <b-col cols="12" md="8">
              <ul class="robot-data-list list-unstyled text-left">
                <li>
                  <span
                    >{{ $t("home.robotData.TotalTime") }} :
                    {{ robot_data.total_useage_time }}</span
                  >
                </li>
                <li>
                  <span
                    >{{ $t("home.robotData.AverageTime") }} :
                    {{ robot_data.average_useage_duration }}</span
                  >
                </li>
                <li>
                  <span
                    >{{ $t("home.robotData.RoomsCount") }} :
                    {{ robot_data.rooms_disinfected_count }}</span
                  >
                </li>
                <li>
                  <span
                    >{{ $t("home.robotData.CorridorsCount") }} :
                    {{ robot_data.corridor_disinfected_count }}</span
                  >
                </li>
                <li>
                  <span
                    >{{ $t("home.robotData.completed") }} :
                    {{ robot_data.completed_tasks }} %</span
                  >
                </li>
              </ul>
            </b-col>
          </b-row>
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
          <b-card-title>{{ $t("home.chart.Title1") }}</b-card-title>
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
          <b-card-title>{{ $t("home.chart.Title2") }}</b-card-title>
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
import axios from "axios";
import useJwt from "@/auth/jwt/useJwt";

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
      setRobot: null,
      option: null,
      rangeDate: null,
      robot_img: require("@/assets/images/robot/robot-1.png"),
      robot_data: null,
      items: null,
      chartofday: {
        series: [
          {
            name: "Performed Tasks By Day",
            data: [],
          },
        ],
        chartOptions: {
          chart: {
            height: 350,
            type: "bar",
          },
          plotOptions: {
            bar: {
              columnWidth: "45%",
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
            categories: [],
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
            data: [],
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
            categories: [],
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

  mounted() {
    var end_date = new Date().toJSON().slice(0, 10).replace(/-/g, "/");
    var start_date = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)
      .toISOString()
      .slice(0, 10);
    // ["2021-05-01", "2021-05-10"]
    this.rangeDate = [start_date, end_date];
    // axios
    //   .post("/api/user/getRobotList", params, {
    //     headers: {
    //       Authorization: "Bearer " + useJwt.getToken(),
    //     },
    //   })
    //   .then((response) => {
    //     this.option = response.data.data;
    //     this.setRobot = response.data.data[1].value;
    //   })
    //   .catch((error) => {
    //     console.log(error);
    //   });

    const params = {
      robot_serial: 1,
      start_date: start_date,
      end_date: end_date,
    };
    this.getDashboardData(params);
  },
  methods: {
    selected_robot() {
      const params = {
        robot_serial: this.setRobot,
        start_date: this.rangeDate[0],
        end_date: this.rangeDate[1],
      };
      this.getDashboardData(params);
    },

    onChangeDate: function (selectedDates, dateStr, instance) {
      //...
    },

    getDashboardData(params) {
      axios
        .post("/api/user/dashboard", params, {
          headers: {
            Authorization: "Bearer " + useJwt.getToken(),
          },
        })
        .then((response) => {
          console.log("=========", response.data);
          if(response.data.status == 1){
            const robotList = response.data.data.robot_list;
            const robotInfo = response.data.data.total_info;
            const task_info = response.data.data.performed_task_info;
            const task_day_info = response.data.data.performed_task_day_info;
            const task_unit_info = response.data.data.performed_task_unit_info;

            this.option = robotList;
            this.robot_data = robotInfo;
            this.items = task_info;
            const daysOfvalue = task_day_info.map(function (x) {
              return x.d_cnt;
            });
            const daysOflabel = task_day_info.map(function (x) {
              return x.d_date;
            });
            this.chartofday.series = [
              {
                data: daysOfvalue,
              },
            ];
            this.chartofday.chartOptions.xaxis.categories = daysOflabel;

            const unitOfvalue = task_unit_info.map(function (x) {
              return x.u_cnt;
            });
            const unitOflabel = task_unit_info.map(function (x) {
              return x.unit;
            });
            this.chartofunit.series = [
              {
                data: unitOfvalue,
              },
            ];
            this.chartofunit.chartOptions.xaxis.categories = unitOflabel;
            // console.log(this.chartofunit);
          }else{
            if(response.data.code != null && response.data.code == '-1'){
              //logout
              localStorage.removeItem('userData');
              this.$router.replace('/login');
            }
          }
          
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>
