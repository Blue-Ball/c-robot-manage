<template>
  <b-row>
    <b-col cols="6" v-for="mapDatas in sets" v-bind:key="mapDatas.id">
      <b-card class="text-center">
        <b-container class="bv-example-row">
          <b-row>
            <b-col cols="1"> </b-col>
            <b-col class="color-red">{{ $t(mapDatas.id) }}</b-col>
          </b-row>
          <b-row v-for="floor in mapDatas.floor_data" v-bind:key="floor.id">
            <div class="w-100"></div>
            <b-col cols="1" class="color-red">{{ floor.id }}</b-col>
            <b-col v-for="rooms in floor.room_data" :key="rooms.id">{{ rooms.id }}</b-col>
          </b-row>
        </b-container>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import { BCard, BCardTitle, BCardText, BContainer, BRow, BCol } from "bootstrap-vue";
import axios from "axios";
import useJwt from "@/auth/jwt/useJwt";

export default {
  components: {
    BCard,
    BCardTitle,
    BCardText,
    BContainer,
    BRow,
    BCol,
  },
  data() {
    return {
      sets: [
        {
          id: "Emergency",
          floor_data: [
            {
              id: "1",
              room: [
                {
                  id: 1,
                },
                {
                  id: 2,
                },
              ],
            },
          ],
        },
      ],
    };
  },
  mounted() {
    axios
      .post("/api/user/hospital_map", "", {
        headers: {
          Authorization: "Bearer " + useJwt.getToken(),
        },
      })
      .then((response) => {
        console.log(response.data);
        const mapInfo = response.data.data;
        this.sets = mapInfo;
      })
      .catch((error) => {
        console.log(error);
      });
  },
  methods: {
    // reversedFloorData() {
    //   let rev = this.sets.map((s) => {
    //     return s.floor_data.slice().reverse();
    //   });
    //   return rev.flat();
    // },
    getRoomNum(numbers) {
      console.log("numbers", numbers);
    },
  },
};
</script>

<style type="scss">
.bv-example-row .row > .col:not(.header),
.bv-example-row .row > [class^="col-"] {
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  background-color: rgba(250, 223, 102, 0.15);
  border: 1px solid rgba(107, 91, 251, 0.2);
}
.color-red {
  color: red;
  font-weight: bold;
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  background-color: rgba(250, 223, 102, 0.15);
  border: 1px solid rgba(107, 91, 251, 0.2);
}
</style>
