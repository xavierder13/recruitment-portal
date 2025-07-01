<template>
  <div class="flex column">
    <div id="_wrapper" class="pa-5">
      <v-overlay :absolute="absolute" :value="overlay">
        <v-progress-circular
          :size="70"
          :width="7"
          color="primary"
          indeterminate
        ></v-progress-circular>
      </v-overlay>
      <v-main>
        <v-subheader class="py-0 d-flex justify-space-between rounded-lg">
          <h3>Dashboard</h3>
          <v-btn color="info" link to="/jobapplicants/index-new">
              View All Applicants
          </v-btn>
        </v-subheader>
        <br>
        <v-row>
          <v-col>
            <v-row>
              <template v-for="item in dashboardCardList">
                <v-col cols="4" v-if="item.hasPermission">
                  <v-card elevation="2" class="rounded-lg">
                    <v-card-text class="d-flex justify-space-between align-center">
                      <v-row>
                        <v-col>
                          <strong>{{ item.text }}</strong> <br>
                          <v-btn small color="info" class="mt-2" link :to="item.link">View </v-btn>
                        </v-col>
                        <v-col align="right">
                          <v-tooltip top v-if="hasPermission('jobapplicants-failed-list') && item.text != 'Hired (This Month)'">
                            <template v-slot:activator="{ on, attrs }">
                              <v-avatar size="60" color="error">
                                <span style="color: white" v-bind="attrs" v-on="on">{{ item.count_failed }}</span>
                              </v-avatar>
                            </template>
                            <span>Failed</span>
                          </v-tooltip>  
                          <v-tooltip top>
                            <template v-slot:activator="{ on, attrs }">
                              <v-avatar size="60" :color="item.color">
                                <span style="color: white" v-bind="attrs" v-on="on">{{ item.count }}</span>
                              </v-avatar>
                            </template>
                            <span>On Process</span>
                          </v-tooltip>  
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-col>
              </template>
              <!-- <v-col cols="4" v-if="hasPermission('jobapplicants-today-list')">
                <v-card elevation="2" class="rounded-lg">
                  <v-card-text class="d-flex justify-space-between align-center">
                    <div>
                      <strong>Total Applicants Today</strong> <br>
                      <v-btn small color="info" class="mt-2">View </v-btn>
                    </div>
                    <v-avatar size="60" color="cyan accent-3">
                      <span style="color: white">999</span>
                    </v-avatar>
                  </v-card-text>
                </v-card>
              </v-col>
               -->
            </v-row>
          </v-col>
        </v-row>
      </v-main>
    </div>
  </div>
</template>
<script>

import axios from "axios";
import { validationMixin } from "vuelidate";
import { mapState, mapGetters } from "vuex";

export default {

  mixins: [validationMixin],

  validations: {},
  data() {
    return {
      absolute: true,
      overlay: false,
      search: "",
      loading: true,
      totalCount: {
        screening_ctr: 0,
        screening_failed_ctr: 0,
        initial_interview_ctr: 0,
        initial_interview_failed_ctr: 0,
        iq_test_ctr: 0,
        iq_test_failed_ctr: 0,
        bi_ctr: 0,
        bi_failed_ctr: 0,
        final_interview_ctr: 0,
        final_interview_failed_ctr: 0,
        orientation_ctr: 0,
        orientation_failed_ctr: 0,
        hired_ctr: 0,
      }
    };
  },

  methods: {
    getApplicantsCount() {
      this.v_table = false;
      this.loading = true;
      this.table_loader = true;
      axios.get("/api/job_applicant/get_all_status_count").then(
        (response) => {
          let data = response.data
          let fields = Object.keys(this.totalCount)

          fields.forEach(field => {
            this.totalCount[field] = data[field];
          });

          // this.screening_ctr = data.screening_ctr;
          // this.initial_interview_ctr = data.initial_interview_ctr;
          // this.iq_test_ctr = data.iq_test_ctr;
          // this.bi_ctr = data.bi_ctr;
          // this.final_interview_ctr = data.final_interview_ctr;
          // this.orientation_ctr = data.orientation_ctr;
          // this.hired_ctr = data.hired_ctr;
          
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },
    showAlert() {
      this.$swal({
        position: "center",
        icon: "success",
        title: "Record has been saved",
        showConfirmButton: false,
        timer: 2500,
      });
    },

    showConfirmAlert(item) {
      this.$swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Delete record!",
      }).then((result) => {
        // <--

        if (result.value) {
          // <-- if confirmed
          this.$swal({
            position: "center",
            icon: "success",
            title: "Success",
            showConfirmButton: false,
            timer: 2500,
          });
        }
      });
    },

    close() {},

    save() {},
    clear() {},

    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },

    
    websocket() {
      // Socket.IO fetch data
      this.$options.sockets.sendData = (data) => {
        let action = data.action;
      };
    },
  },
  computed: {
    dashboardCardList() {

      let counter = this.totalCount

      let list = [
        // { 
        //   text: 'Total Applicants Today', 
        //   color: 'cyan accent-3', 
        //   hasPermission: this.hasPermission('jobapplicants-today-list'), 
        //   count: 999, 
        //   link: '/jobapplicants/screening-list'
        // },
        { 
          text: 'Screening', 
          color: 'warning', 
          hasPermission: this.hasPermission('jobapplicants-screening-list'), 
          count: counter.screening_ctr,
          count_failed: counter.screening_failed_ctr,
          link: '/jobapplicants/screening-list'
        },
        { 
          text: 'Initial Interview', 
          color: 'purple', 
          hasPermission: this.hasPermission('jobapplicants-initial-interview-list'), 
          count: counter.initial_interview_ctr, 
          count_failed: counter.initial_interview_failed_ctr, 
          link: '/jobapplicants/initial-interview-list'
        },
        { 
          text: 'Exam', 
          color: 'teal', 
          hasPermission: this.hasPermission('jobapplicants-iq-test-list'), 
          count: counter.iq_test_ctr, 
          count_failed: counter.iq_test_failed_ctr, 
          link: '/jobapplicants/iq-test-list'
        },
        { 
          text: 'Background Investigation', 
          color: 'lime', 
          hasPermission: this.hasPermission('jobapplicants-bi-list'), 
          count: counter.bi_ctr, 
          count_failed: counter.bi_failed_ctr, 
          link: '/jobapplicants/bi-list'
        },
        { 
          text: 'Final Interview', 
          color: 'cyan', 
          hasPermission: this.hasPermission('jobapplicants-final-interview-list'), 
          count: counter.final_interview_ctr, 
          count_failed: counter.final_interview_failed_ctr, 
          link: '/jobapplicants/final-interview-list'
        },
        { 
          text: 'Orientation', 
          color: 'secondary', 
          hasPermission: this.hasPermission('jobapplicants-orientation-list'), 
          count: counter.orientation_ctr, 
          count_failed: counter.orientation_failed_ctr, 
          link: '/jobapplicants/orientation-list'
        },
        { 
          text: 'Hired (This Month)', 
          color: 'success', 
          hasPermission: this.hasPermission('jobapplicants-hired-list'), 
          count: this.hired_ctr, 
          link: '/jobapplicants/hired-list'
        },
       ];

       return list;
    },
    ...mapState("userRolesPermissions", ["userRoles", "userPermissions"]),
    ...mapGetters("userRolesPermissions", ["hasRole", "hasPermission"]),
  },
  mounted() {
    axios.defaults.headers.common["Authorization"] = "Bearer " + localStorage.getItem("access_token");
    // this.websocket();
    this.getApplicantsCount();
  },
};
</script>