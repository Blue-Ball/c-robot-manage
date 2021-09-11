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
    <div class="pdf-page" v-for="data in pdfData" :key="data.id">
      <b-row>
        <b-col md="7">
          <b-card class="text-center">
            <b-card-header>
              <div>
                <h4 class="font-weight-bolder">{{ data.rangeDate }}</h4>
              </div>
              <!-- <div class="d-flex align-items-center">
                <b-card-text>  </b-card-text>
              </div>
              datepicker -->
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
                      {{ data.robot_data.total_useage_time }}</span
                    >
                  </li>
                  <li>
                    <span
                      >{{ $t("home.robotData.AverageTime") }} :
                      {{ data.robot_data.average_useage_duration }}</span
                    >
                  </li>
                  <li>
                    <span
                      >{{ $t("home.robotData.RoomsCount") }} :
                      {{ data.robot_data.rooms_disinfected_count }}</span
                    >
                  </li>
                  <li>
                    <span
                      >{{ $t("home.robotData.CorridorsCount") }} :
                      {{ data.robot_data.corridor_disinfected_count }}</span
                    >
                  </li>
                  <li>
                    <span
                      >{{ $t("home.robotData.completed") }} :
                      {{ data.robot_data.completed_tasks }} %</span
                    >
                  </li>
                </ul>
              </b-col>
            </b-row>
          </b-card>
        </b-col>

        <b-col md="5">
          <b-card>
            <b-card-title class="text-left">{{
              $t("home.taskTable.Title")
            }}</b-card-title>
            <b-table responsive="xl" :items="data.items" />
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
              :options="data.chartofday.chartOptions"
              :series="data.chartofday.series"
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
              :options="data.chartofunit.chartOptions"
              :series="data.chartofunit.series"
            ></vue-apex-charts>
          </b-card>
        </b-col>
      </b-row>
    </div>
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
      startDate: null,
      endDate: null,
      option: null,

      requestParam: "",
      robot_img: require("@/assets/images/robot/robot-1.png"),
      pdfData: [
        {
          rangeDate: "",
          items: null,
          robot_data: [],
          chartofday: [],
          chartofunit: [],
        },
      ],
    };
  },
  created() {
    this.startDate = this.$route.params.startDate;
    this.endDate = this.$route.params.endDate;
  },
  mounted() {
    this.getWeeks();
  },

  methods: {
    getWeeks() {
      let startDate = new Date(this.startDate);
      let lastdate = new Date(this.endDate);
      let firstSunday = new Date(
        startDate.setDate(startDate.getDate() - startDate.getDay())
      );
      let lastSatday = new Date(
        lastdate.setDate(lastdate.getDate() - lastdate.getDay() + 6)
      );
      for (
        let i = firstSunday;
        i <= lastSatday;
        i.setDate(i.getDate() - i.getDay() + 7)
      ) {
        const sunday = new Date(
          firstSunday.setDate(firstSunday.getDate() - firstSunday.getDay())
        );
        const saturday = new Date(
          firstSunday.setDate(firstSunday.getDate() - firstSunday.getDay() + 6)
        );
        this.requestParam = {
          robot_serial: 1,
          start_date: sunday,
          end_date: saturday,
        };
        this.rangeDate =
          moment(sunday).format("DD/MM/YYYY") +
          " ~ " +
          moment(saturday).format("DD/MM/YYYY");

        this.getDashboardData(this.requestParam);
      }
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
            console.log("this.pdfData= ", this.pdfData);
            const robotInfo = response.data.data.total_info;
            const task_info = response.data.data.performed_task_info;
            const task_day_info = response.data.data.performed_task_day_info;
            const task_unit_info = response.data.data.performed_task_unit_info;

            this.robot_data = robotInfo;
            this.items = task_info;
            const daysOfvalue = task_day_info.map(function (x) {
              return x.d_cnt;
            });
            const daysOflabel = task_day_info.map(function (x) {
              return x.d_date;
            });
            const unitOfvalue = task_unit_info.map(function (x) {
              return x.u_cnt;
            });
            const unitOflabel = task_unit_info.map(function (x) {
              return x.unit;
            });
            let tempData = {
              rangeDate: this.rangeDate,
              items: task_info,
              robot_data: robotInfo,
              chartofday: {
                series: [
                  {
                    name: "Performed Tasks By Day",
                    data: daysOfvalue,
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
                    categories: daysOflabel,
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
                    data: unitOfvalue,
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
                    categories: unitOflabel,
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
            this.pdfData.push(tempData);
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
