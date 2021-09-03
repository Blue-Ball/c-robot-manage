<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
@import '@core/scss/vue/libs/vue-flatpicker.scss';
.robot-data-list li{
  padding: 8px 0px;
  font-size: larger;
}
</style>
<template>
  <div>
    <b-row>
      <b-col md="6">
        <b-card class="text-center">
          <b-card-header>
            <!-- title and subtitle -->
            <div>
              <b-form-group>
                <v-select
                  dense
                  v-model="selected"
                  :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                  label="robot"
                  :options="option"
                />
              </b-form-group>
            </div>
            <!--/ title and subtitle -->

            <!-- datepicker -->
            <div class="d-flex align-items-center">
              <feather-icon
                icon="CalendarIcon"
                size="16"
              />
              <flat-pickr
                v-model="rangeDate"
                :config="{ mode: 'range'}"
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
            <b-col
              cols="12"
              md="8"
            >
              <ul class="robot-data-list list-unstyled text-left">
                <li >
                  <span>{{$t('home.robotData.TotalTime')}} : </span>
                </li>
                <li >
                  <span>{{$t('home.robotData.AverageTime')}} : </span>
                </li>
                <li >
                  <span>{{$t('home.robotData.RoomsCount')}} : </span>
                </li>
                <li >
                  <span>{{$t('home.robotData.CorridorsCount')}} : </span>
                </li>
                <li >
                  <span>{{$t('home.robotData.completed')}} : </span>
                </li>
              </ul>
              
            </b-col>
          </b-row>
          <!-- <b-card-text>
            With supporting text below as a natural lead-in to additional content
          </b-card-text> -->
          
        </b-card>
      </b-col>

      <b-col md="6">
        <b-card class="text-left">
          <b-card-title>{{ $t('home.taskTable.Title') }}</b-card-title>
          <b-table responsive="sm" :items="items"/>
        </b-card>
      </b-col>
    </b-row>
    
  </div>
</template>



<script>
import { BCard, BCardHeader, BCardText, BLink, BRow, BCol, BCardTitle, BButton, BFormGroup, BImg, BTable } from 'bootstrap-vue'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component' 

export default {
  components: {
    BCard,
    BCardHeader,
    BCardText,
    BLink,
    BRow, 
    BCol, 
    BCardTitle, 
    BButton,
    BFormGroup,
    BImg,
    BTable ,
    vSelect,
    flatPickr 
  },
  data () {
    return {
      selected: { robot: 'Square' },
      option: [{ robot: 'Square' }, { robot: 'Rectangle' }, { robot: 'Rombo' }, { robot: 'Romboid' }],

      rangeDate: null,

      robot_data: {
        img: require('@/assets/images/robot/robot-1.png'),
        name: 'English',
        total_time: '3 Hours 50 Minutes',
        averag_time: '3 Hours 24 Minutes',
        room_count: '125',
        corridors_count: '113',
        complate:'81%'
      },
      items: [
        {
          unit: 'Surgery', floor: 3, duration: '3:00', time: '23/1/2021 15:00',
        },
        {
          unit: 'Surgery', floor: 3, duration: '3:00', time: '23/1/2021 15:00',
        },
        {
          unit: 'Surgery', floor: 3, duration: '3:00', time: '23/1/2021 15:00',
        },
        {
          unit: 'Surgery', floor: 3, duration: '3:00', time: '23/1/2021 15:00',
        },
        {
          unit: 'Surgery', floor: 3, duration: '3:00', time: '23/1/2021 15:00',
        }
      ],
    }
  }
}
</script>


