<style lang="scss">
@import "@core/scss/vue/libs/vue-select.scss";
.robot-data-list li {
  padding: 8px 0px;
  font-size: larger;
}
[dir] .table th,
[dir] .table td {
  padding: 0.72rem 0.1rem;
}
</style>
<template>
  <div id="dashboard">
    <b-row>
      <b-col md="7">
        <b-card class="text-center">
          <b-card-header>
            <div>
              <h6 class="font-weight-bolder">Robot 1</h6>
            </div>
            <div class="d-flex align-items-center">
              <b-card-text> 2021.9.1~2021.9.8 </b-card-text>
            </div>
            <!-- datepicker -->
          </b-card-header>
          <b-row class="my-2">
            <b-col
              cols="12"
              md="4"
              class="d-flex align-items-center justify-content-center mb-2 mb-md-0"
            >
              <div class="d-flex align-items-center justify-content-center">
                <b-img :src="robot_img" class="product-img" fluid />
              </div>
            </b-col>
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
            ref="chart"
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
} from "bootstrap-vue";
import AppEchartBar from "@core/components/charts/echart/AppEchartBar.vue";
import VueApexCharts from "vue-apexcharts";
import axios from "axios";
import useJwt from "@/auth/jwt/useJwt";
import moment from "moment";
import { jsPDF } from "jspdf";
import html2canvas from "html2canvas";

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
    AppEchartBar,
    VueApexCharts,
  },

  data() {
    return {
      setRobot: null,
      startDate: null,
      endDate: null,
      option: null,
      rangeDate: null,
      isRobot: true,
      requestParam: "",
      robot_img: require("@/assets/images/robot/robot-1.png"),
      robot_data: [],
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
          colors: ["#7367f0", "#ff9f43", "#9ca0a4", "#ff9f43", "#00cfe8"],
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
              // colors: ["#304758"],
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
            labels: {
              style: {
                colors: "#9ca0a4",
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
              style: {
                colors: "#9ca0a4",
                fontSize: "12px",
              },
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
              // colors: ["#304758"],
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
              style: {
                colors: "#9ca0a4",
                fontSize: "12px",
              },
              formatter: function (val) {
                return val; // + "%";
              },
            },
          },
        },
      },
    };
  },
  created() {
    this.setRobot = this.$route.params.robot;
    this.startDate = this.$route.params.startDate;
    this.endDate = this.$route.params.endDate;
    console.log(this.$route.params);
  },
  mounted() {
    this.rangeDate = [start_date, end_date];
    this.requestParam = {
      robot_serial: this.setRobot,
      start_date: start_date,
      end_date: end_date,
    };
    this.getDashboardData(this.requestParam);
    // console.log("this.isRobot = ", this.isRobot);
  },

  methods: {
    selected_robot() {
      console.log("this.rangeDate = ", this.rangeDate);
      this.requestParam = {
        robot_serial: this.setRobot,
        start_date: this.rangeDate.split(" to ").slice(0)[0],
        end_date: this.rangeDate.split(" to ").slice(0)[1],
      };
      this.getDashboardData(this.requestParam);
    },

    onDateChange: function (selectedDates, dateStr, instance) {
      if (this.isRobot) {
        this.requestParam = {
          robot_serial: this.setRobot,
          start_date: moment(selectedDates[0]).format("YYYY/MM/DD"),
          end_date: moment(selectedDates[1]).format("YYYY/MM/DD"),
        };
      } else {
        this.requestParam = {
          robot_serial: this.setRobot,
          start_date: moment(selectedDates[0]).format("YYYY/MM/DD"),
          end_date: moment(selectedDates[1]).format("YYYY/MM/DD"),
          unit: this.$route.params.unit,
          floor: this.$route.params.floor,
          room: this.$route.params.room,
        };
      }
      this.getDashboardData(this.requestParam);
    },
    getDashboardData(params) {
      axios
        .post("/api/user/dashboard", params, {
          headers: {
            Authorization: "Bearer " + useJwt.getToken(),
          },
        })
        .then((response) => {
          if (response.data.status == 1) {
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
            this.chartofday = {
              chartOptions: {
                xaxis: {
                  categories: daysOflabel,
                },
              },
              series: [
                {
                  data: daysOfvalue,
                },
              ],
            };
            const unitOfvalue = task_unit_info.map(function (x) {
              return x.u_cnt;
            });
            const unitOflabel = task_unit_info.map(function (x) {
              return x.unit;
            });
            this.chartofunit = {
              chartOptions: {
                xaxis: {
                  categories: unitOflabel,
                },
              },
              series: [
                {
                  data: unitOfvalue,
                },
              ],
            };
          } else {
            if (response.data.code != null && response.data.code == "-1") {
              //logout
              localStorage.removeItem("userData");
              this.$router.replace("/login");
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
