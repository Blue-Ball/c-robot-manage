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
[dir] .calendar-select {
  border: 1px solid #d8d6de;
  border-radius: 0.357rem;
}
table {
  text-align: center;
}
[dir] .download-title {
  margin-top: 10px;
  margin-bottom: 10px;
}

/* Center the loader */
#loader {
  border: 8px solid #3cd10f;
  border-radius: 50%;
  border-top: 8px solid blue;
  border-bottom: 8px solid blue;
  width: 40px;
  height: 40px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% {
    -webkit-transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
  }
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
<template>
  <div id="dashboard">
    <b-row class="download-title">
      <b-col cols="12" md="1">
        <b-button
          id="download_btn"
          variant="primary"
          @click="generatePDF"
          label="Download"
          >{{ $t("Download") }}</b-button
        >
      </b-col>
      <b-col cols="12" md="11">
        <div id="loader"></div>
      </b-col>
    </b-row>

    <div v-for="data in sortedArray" :key="data.index">
      <div v-if="data.data.items != null">
        <div class="pdf-page">
          <b-row>
            <b-col md="7">
              <b-card class="text-center" style="height: 95%">
                <b-card-header>
                  <div>
                    <h4 class="font-weight-bolder">{{ data.data.rangeDate }}</h4>
                  </div>
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
                        <b-row>
                          <b-col cols="12" md="6">
                            <span>{{ $t("home.robotData.TotalTime") }}</span>
                          </b-col>
                          <b-col cols="12" md="6">
                            <span
                              >:&nbsp;{{ data.data.robot_data.total_useage_time }}</span
                            >
                          </b-col>
                        </b-row>
                      </li>
                      <li>
                        <b-row>
                          <b-col cols="12" md="6">
                            <span>{{ $t("home.robotData.AverageTime") }}</span>
                          </b-col>
                          <b-col cols="12" md="6">
                            <span
                              >:&nbsp;{{
                                data.data.robot_data.average_useage_duration
                              }}</span
                            >
                          </b-col>
                        </b-row>
                      </li>
                      <li>
                        <b-row>
                          <b-col cols="12" md="6">
                            <span>{{ $t("home.robotData.RoomsCount") }}</span>
                          </b-col>
                          <b-col cols="12" md="6">
                            <span
                              >:&nbsp;{{
                                data.data.robot_data.rooms_disinfected_count
                              }}</span
                            >
                          </b-col>
                        </b-row>
                      </li>
                      <li>
                        <b-row>
                          <b-col cols="12" md="6">
                            <span>{{ $t("home.robotData.CorridorsCount") }}</span>
                          </b-col>
                          <b-col cols="12" md="6">
                            <span
                              >:&nbsp;{{
                                data.data.robot_data.corridor_disinfected_count
                              }}</span
                            >
                          </b-col>
                        </b-row>
                      </li>
                      <li>
                        <b-row>
                          <b-col cols="12" md="6">
                            <span>{{ $t("home.robotData.completed") }}</span>
                          </b-col>
                          <b-col cols="12" md="6">
                            <span
                              >:&nbsp;{{ data.data.robot_data.completed_tasks }} %</span
                            >
                          </b-col>
                        </b-row>
                      </li>
                    </ul>
                  </b-col>
                </b-row>
              </b-card>
            </b-col>

            <b-col md="5">
              <b-card style="height: 95%">
                <b-card-title class="text-left">{{
                  $t("home.taskTable.Title")
                }}</b-card-title>
                <b-table responsive="xl" :items="data.data.items" />
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
                  :options="data.data.chartofday.chartOptions"
                  :series="data.data.chartofday.series"
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
                  :options="data.data.chartofunit.chartOptions"
                  :series="data.data.chartofunit.series"
                ></vue-apex-charts>
              </b-card>
            </b-col>
          </b-row>
        </div>
      </div>
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
  BButton,
} from "bootstrap-vue";
import AppEchartBar from "@core/components/charts/echart/AppEchartBar.vue";
import VueApexCharts from "vue-apexcharts";
import axios from "axios";
import useJwt from "@/auth/jwt/useJwt";
import moment from "moment";
import html2pdf from "html2pdf.js";

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
    BButton,
  },

  data() {
    return {
      startDate: null,
      endDate: null,
      option: null,
      requestParam: "",
      robot_img: require("@/assets/images/robot/cbot.png"),
      pdfData: [],
    };
  },
  created() {
    this.startDate = this.$route.params.startDate;
    this.endDate = this.$route.params.endDate;
  },
  mounted() {
    this.getWeeks();
    document.getElementById("loader").style.display = "none";
  },
  methods: {
    generatePDF() {
      document.getElementById("download_btn").disabled = true;
      document.getElementById("loader").style.display = "block";
      let pages = Array.from(window.document.getElementsByClassName("pdf-page"));
      var opt = {
        margin: 1,
        filename: "robot-report.pdf",
        image: { type: "jpeg", quality: 1.0 },
        html2canvas: { scale: 1.2 },
        jsPDF: { unit: "mm", format: "a3", orientation: "landscape" },
      };
      var worker = html2pdf().set(opt).from(pages[0]).toPdf();
      pages.slice(1).forEach(function (page) {
        worker = worker
          .get("pdf")
          .then(function (pdf) {
            pdf.addPage();
          })
          .from(page)
          .toContainer()
          .toCanvas()
          .toPdf();
      });
      // worker = worker.save();
      worker = worker.save().then(function () {
        // console.log("finishLoading");
        document.getElementById("loader").style.display = "none";
        document.getElementById("download_btn").disabled = false;
      });
    },
    getWeeks() {
      let startDate = new Date(this.startDate);
      let lastdate = new Date(this.endDate);
      let firstSatday = new Date(
        startDate.setDate(startDate.getDate() - startDate.getDay())
      );
      let lastFriday = new Date(
        lastdate.setDate(lastdate.getDate() - lastdate.getDay() + 6)
      );
      firstSatday = moment(firstSatday).subtract(1, "days");
      lastFriday = moment(lastFriday).subtract(1, "days");
      var ajaxIndex = 0;
      let i;
      for (i = firstSatday; i <= lastFriday; i = i.add(7, "days")) {
        const sat = i;
        const fri = moment(i).add(6, "days");
        this.requestParam = {
          robot_serial: 0,
          start_date: sat.format("YYYY/MM/DD"),
          end_date: fri.format("YYYY/MM/DD"),
        };
        let rangeDate = sat.format("DD.MM.YYYY") + " ~ " + fri.format("DD.MM.YYYY");
        this.getDashboardData(this.requestParam, rangeDate, ajaxIndex++);
      }
    },
    async getDashboardData(params, rangeDate, ajaxIndex) {
      axios
        .post("/api/user/dashboard", params, {
          headers: {
            Authorization: "Bearer " + useJwt.getToken(),
          },
        })
        .then((response) => {
          if (response.data.status == 1) {
            const robotInfo = response.data.data.total_info;
            const task_info = response.data.data.performed_task_info;
            const task_day_info = response.data.data.performed_task_day_info;
            const task_unit_info = response.data.data.performed_task_unit_info;

            this.robot_data = robotInfo;
            this.items = task_info;
            const daysOfvalue = task_day_info.map(function (x) {
              return x.d_cnt;
            });
            // const daysOflabel = task_day_info.map(function (x) {
            //   return x.d_date;
            // });
            const unitOfvalue = task_unit_info.map(function (x) {
              return x.u_cnt;
            });
            const unitOflabel = task_unit_info.map(function (x) {
              return x.unit;
            });
            let tempData = {
              rangeDate: rangeDate,
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
                  colors: [
                    "#116af1",
                    "#ef8314",
                    "#8f9192",
                    "#f5c823",
                    "#25d0f7",
                    "#08f93b",
                    "#083eb7",
                  ],
                  plotOptions: {
                    bar: {
                      columnWidth: "45%",
                      borderRadius: 10,
                      dataLabels: {
                        position: "top", // top, center, bottom
                      },
                      distributed: true,
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
                      colors: ["#999"],
                    },
                  },
                  legend: {
                    show: false,
                  },
                  xaxis: {
                    categories: ["Sat", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri"],
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
                      colors: ["#999"],
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
            this.pdfData.push({ index: ajaxIndex, data: tempData });
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
  computed: {
    sortedArray: function () {
      function compare(a, b) {
        if (a.index < b.index) return -1;
        if (a.index > b.index) return 1;
        return 0;
      }

      return this.pdfData.sort(compare);
    },
  },
  //  watch: {
  //   this.pdfData
  // },
};
</script>
