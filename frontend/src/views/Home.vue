<style lang="scss">
@import "@core/scss/vue/libs/vue-select.scss";
@import "@core/scss/vue/libs/vue-flatpicker.scss";
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
</style>
<template>
  <div>
    <b-row>
      <b-col md="7">
        <b-card class="text-center" style="height: 95%">
          <b-card-header>
            <!-- title and subtitle -->
            <div>
              <b-form-group>
                <b-form-select
                  v-model="setRobot"
                  label="robot"
                  v-on:change="selected_robot()"
                  :options="option"
                ></b-form-select>
              </b-form-group>
            </div>
            <!--/ title and subtitle -->

            <!-- datepicker -->
            <div class="d-flex align-items-center calendar-select">
              <feather-icon icon="CalendarIcon" size="16" style="margin-left: 10px" />
              <flat-pickr
                v-model="selectDate"
                :config="dateConfig"
                @on-close="onDateChange"
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
                  <b-row>
                    <b-col cols="12" md="6">
                      <span>{{ $t("home.robotData.TotalTime") }}</span>
                    </b-col>
                    <b-col cols="12" md="6">
                      <span>:&nbsp;{{ robot_data.total_useage_time }}</span>
                    </b-col>
                  </b-row>
                </li>
                <li>
                  <b-row>
                    <b-col cols="12" md="6">
                      <span>{{ $t("home.robotData.AverageTime") }}</span>
                    </b-col>
                    <b-col cols="12" md="6">
                      <span>:&nbsp;{{ robot_data.average_useage_duration }}</span>
                    </b-col>
                  </b-row>
                </li>
                <li>
                  <b-row>
                    <b-col cols="12" md="6">
                      <span>{{ $t("home.robotData.RoomsCount") }}</span>
                    </b-col>
                    <b-col cols="12" md="6">
                      <span>:&nbsp;{{ robot_data.rooms_disinfected_count }}</span>
                    </b-col>
                  </b-row>
                </li>
                <li>
                  <b-row>
                    <b-col cols="12" md="6">
                      <span>{{ $t("home.robotData.CorridorsCount") }}</span>
                    </b-col>
                    <b-col cols="12" md="6">
                      <span>:&nbsp;{{ robot_data.corridor_disinfected_count }}</span>
                    </b-col>
                  </b-row>
                </li>
                <li>
                  <b-row>
                    <b-col cols="12" md="6">
                      <span>{{ $t("home.robotData.completed") }}</span>
                    </b-col>
                    <b-col cols="12" md="6">
                      <span>:&nbsp;{{ robot_data.completed_tasks }} %</span>
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
  BFormSelect,
} from "bootstrap-vue";
import flatPickr from "vue-flatpickr-component";
import AppEchartBar from "@core/components/charts/echart/AppEchartBar.vue";
import VueApexCharts from "vue-apexcharts";
import axios from "axios";
import useJwt from "@/auth/jwt/useJwt";
import moment from "moment";

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
      selectDate: null,
      isSelectDate: false,
      isRobot: true,
      startDate: "",
      endDate: "",
      dateConfig: {
        // mode: "range",
        wrap: true, // set wrap to true only when using 'input-group'
        altFormat: "d/m/Y",
        altInput: true,
        dateFormat: "Y/m/d",
        defaultDate: new Date(),
      },
      requestParam: "",
      robot_img: require("@/assets/images/robot/cbot.png"),
      robot_data: [],
      items: null,
      chartofday: {
        series: [],
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
        series: [],
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

  mounted() {
    axios
      .post("/api/user/getRobotList", "", {
        headers: {
          Authorization: "Bearer " + useJwt.getToken(),
        },
      })
      .then((response) => {
        this.option = response.data.data;
        this.setRobot = response.data.data[0].value;
        this.getDashboardData(this.getRequestParams());
      })
      .catch((error) => {
        console.log(error);
      });

    // console.log("this.isRobot = ", this.isRobot);
  },
  created() {
    if (this.$route.params.unit) {
      this.isRobot = false;
    }
  },
  methods: {
    getRequestParams: function (selectDate = null) {
      let curr = new Date();
      if (selectDate == null){
        curr = curr;
        this.selectDate = new Date();
      }else {
        curr = new Date(selectDate);
        this.selectDate = curr;
      }

      let firstday = new Date(curr.setDate(curr.getDate() - curr.getDay()));
      let lastday = new Date(curr.setDate(curr.getDate() - curr.getDay() + 6));

      this.startDate = moment(firstday).subtract(1, "days").format("YYYY/MM/DD");
      this.endDate = moment(lastday).subtract(1, "days").format("YYYY/MM/DD");

      // console.log(this.startDate, this.endDate);
      this.requestParam = {
        robot_serial: this.setRobot,
        start_date: this.startDate,
        end_date: this.endDate,
      };
      return this.requestParam;
    },
    selected_robot() {
      this.getDashboardData(this.getRequestParams(this.selectDate));
    },

    onDateChange: function (selectedDate, dateStr, instance) {
      this.selectDate = selectedDate;
      this.getDashboardData(this.getRequestParams(selectedDate));
    },
    getDashboardData(params) {
      axios
        .post("/api/user/dashboard", params, {
          headers: {
            Authorization: "Bearer " + useJwt.getToken(),
          },
        })
        .then((response) => {
          // console.log(response.data);
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
            // const daysOflabel = task_day_info.map(function (x) {
            //   return x.d_date;
            // });
            this.chartofday = {
              chartOptions: {
                xaxis: {
                  categories: ["Sat", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri"],
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
          //logout
          localStorage.removeItem("userData");
          this.$router.replace("/login");
        });
    },
  },
};
</script>
