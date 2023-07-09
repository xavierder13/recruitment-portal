`<template>
  <div class="flex column">
    <div id="_wrapper" class="pa-5">
      <v-main>
        <v-breadcrumbs :items="items">
          <template v-slot:item="{ item }">
            <v-breadcrumbs-item :to="item.link" :disabled="item.disabled">
              {{ item.text.toUpperCase() }}
            </v-breadcrumbs-item>
          </template>
        </v-breadcrumbs>
        <v-card>
          <v-card-title>
            Job Vacancy Lists
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
            ></v-text-field>
            <template>
              <v-toolbar flat>
                <v-spacer></v-spacer>
                <v-btn
                  color="primary"
                  fab
                  dark
                  class="mb-2"
                  @click="(dialog = true), (modal_status = 0)"
                  v-if="userPermissions.jobvacancies_create"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
                <v-dialog v-model="dialog" max-width="500px" persistent>
                  <v-card>
                    <v-card-title style="background-color: #1976d2; color: #ffffff;">
                      <span class="headline">{{ formTitle }}</span>
                    </v-card-title>
                    <v-card-text>
                      <v-container class="mt-5">
                        <v-row>
                          <v-col cols="12" class="mt-0 mb-0 pt-0 pb-0">
                            <v-autocomplete
                              v-model="job_vacancies.position_id"
                              :items="positions"
                              item-text="name"
                              item-value="id"
                              label="Position"
                              :error-messages="positionErrors + jobvacancyError.position_id"
                              @input="$v.job_vacancies.position_id.$touch()"
                              @blur="$v.job_vacancies.position_id.$touch()"
                              :disabled="disabled_field > 0"
                            ></v-autocomplete>
                          </v-col>

                          <v-col cols="12" class="mt-0 mb-0 pt-0 pb-0">
                            <v-autocomplete
                              v-model="job_vacancies.educ_attain"
                              :items="['College Graduate', 'College Undergraduate', 'Senior Highschool Graduate', 'Highschool Graduate', 'Vocational/TESDA Graduate']"
                              label="Educational Attainment"
                              :error-messages="educattainErrors + jobvacancyError.educ_attain"
                              @input="$v.job_vacancies.educ_attain.$touch()"
                              @blur="$v.job_vacancies.educ_attain.$touch()"
                              :disabled="disabled_field > 0"
                            ></v-autocomplete>
                          </v-col>

                          <v-col cols="12" class="mt-0 mb-0 pt-0 pb-0">
                            <v-autocomplete
                              v-model="job_vacancies.branch_type"
                              :items="branch_type"
                              item-text="name"
                              item-value="id"
                              label="Branch type"
                              :error-messages="branchtypeErrors + jobvacancyError.branch_type"
                              @input="$v.job_vacancies.branch_type.$touch()"
                              @blur="$v.job_vacancies.branch_type.$touch()"
                              :disabled="disabled_field > 0"
                            ></v-autocomplete>
                          </v-col>

                          <v-col cols="12" class="mt-0 mb-0 pt-0 pb-0">
                            <v-switch v-model="switch1" :label="activeStatus"></v-switch>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn
                        color="primary"
                        class="mb-4"
                        @click="saveJobVacancy()"
                      >
                        Save
                      </v-btn>
                      <v-btn color="#E0E0E0" class="mb-4" @click="reset(), (modal_status = 0)">
                        Cancel
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-toolbar>
            </template>
          </v-card-title>

          <v-scroll-y-transition mode="out-in">
            <v-data-table
              :headers="headers"
              :items="job_vacancies_lists"
              :search="search"
              :loading="loading"
              v-if="userPermissions.jobvacancies_list"
            >
              <template v-slot:item.cnt_id="{ item }">
                {{ job_vacancies_lists.indexOf(item) + 1 }}
              </template>
              <template v-slot:item.status="{ item }">
                <v-chip
                  color="#37474f"
                  dark
                  small
                  v-if="item.status === 0"
                >  
                  Inactive
                </v-chip>
                <v-chip
                  color="#009688"
                  dark
                  small
                  v-if="item.status === 1"
                >  
                  Active
                </v-chip>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-icon
                  small
                  class="mr-2"
                  color="green"
                  @click="editJobVacancy(item.id)"
                >
                  mdi-pencil
                </v-icon>
                <v-icon
                  small
                  color="red"
                  @click="deleteJobVacancy(item.id)"
                >
                  mdi-delete
                </v-icon>
              </template>
            </v-data-table>
          </v-scroll-y-transition>  
        </v-card>
      </v-main>
    </div>
  </div>
</template>
<script>

import axios from "axios";
import { validationMixin } from "vuelidate";
import { required, maxLength, email } from "vuelidate/lib/validators";
import { mapState } from "vuex";

const cons = console.log;
export default {

  mixins: [validationMixin],

  validations: {
    job_vacancies:{
      position_id: { required },
      branch_type: { required },
      educ_attain: { required }
    },
  },
  data() {
    return {
      search: "",
      dialog: false,
      loading: false,
      switch1: true,

      headers: [
        { text: "#", value: "cnt_id", width: "15px" },
        { text: "Position", value: "position_name" },
        { text: "Branch Type", value: "branch_type" },
        { text: "Status", value: "status" },
        { text: "Actions", value: "actions", sortable: false, width: "80px" },
      ],

      items: [
        {
          text: "Home",
          disabled: false,
          link: "/",
        },
        {
          text: "Job Vacancy Lists",
          disabled: true,
        },
      ],

      job_vacancies: {
        position_id: "",
        branch_type: "",
        educ_attain: "",
        status: 1,
      },
      positions: [],
      job_vacancies_lists: [],

      branch_type: [
        { id: 0, name: "For Branch Only" },
        { id: 1, name: "For Admin Only" }
      ],

      jobvacancyError: {
        position_id: [],
        educ_attain: [],
        branch_type: []
      },

      job_vac_id: '',

      modal_title: "Add Job Vacancies",
      modal_status: 0,

      disabled_field: 0
    };
  },
  methods: {
    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },

    reset(){
      this.job_vacancies.position_id = "";
      this.job_vacancies.branch_type = "";
      this.job_vacancies.educ_attain = "";
      this.switch1 =  true;

      this.$v.$reset();

      this.dialog = false;
      this.disabled_field = 0;
    },

    getPosition() {
      axios.get("/api/position/index").then(
        (response) => {
          this.positions = response.data.positions;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    getJobVacancy() {
      this.loading = true;
      this.table_loader = true;
      axios.get("/api/job_vacancy/view").then(
        (response) => {
          this.loading = false;
          this.table_loader = false;
          this.job_vacancies_lists = response.data.job_vacancy_lists;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    saveJobVacancy(){
      let formData = new FormData();
      formData.append('job_vac_id', this.job_vac_id);
      formData.append('position_id', this.job_vacancies.position_id);
      formData.append('branch_type', this.job_vacancies.branch_type);
      formData.append('educ_attain', this.job_vacancies.educ_attain);
      formData.append('status', this.job_vacancies.status);

      if(this.modal_status == 0){
        axios.post("/api/job_vacancy/store", formData).then(
          (response) => {
            if(response.data.success){
              this.$toaster.success('You have successfully added new job vacancy.', {
                timeout: 2000
              });

              // send data to Sockot.IO Server
              this.$socket.emit("sendData", { action: "update-jobvacancy-records" });

              this.getJobVacancy();
              this.reset();
            }else{

              let errors = response.data;
              let errorNames = Object.keys(response.data);

              errorNames.forEach(value => {
                this.jobvacancyError[value].push(errors[value]);
              });

              this.$toaster.error('You have errors adding new job vacancy.', {
                timeout: 2000
              });
            }
          },
          (error) => {
            console.log(error);
            // this.isUnauthorized(error);
          }
        );
      }else{
        axios.post("/api/job_vacancy/update", formData).then(
          (response) => {

            if(response.data.success){
              this.$toaster.success('You have successfully updated the status of job vacancy.', {
                timeout: 2000
              });

              // send data to Sockot.IO Server
              this.$socket.emit("sendData", { action: "update-jobvacancy-records" });

              this.getJobVacancy();
              this.reset();
            }else{

              this.$toaster.error('You have errors updating the job vacancy.', {
                timeout: 2000
              });
            }
          },
          (error) => {
            console.log(error);
            // this.isUnauthorized(error);
          }
        );
      }  
    },

    deleteJobVacancy(id){

      this.$swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Delete record!",
      }).then((result) => {
        if (result.isConfirmed) {

          this.loading = true;
          const url = `/api/job_vacancy/delete_job_vacancy/${id}`;
          axios.get(url).then(
            (response) => {
              if (response.data.success) {

                this.$toaster.success('You have successfully deleted the job vacancy.', {
                  timeout: 2000
                });

                // send data to Sockot.IO Server
                this.$socket.emit("sendData", { action: "update-jobvacancy-records" });

                this.getJobVacancy();
              }
              this.loading = false;
            },
            (error) => {
              this.isUnauthorized(error);
            }
          );

          }else{
            this.$toaster.warning('You have cancelled deleting the job vacancy.', {
              timeout: 2000
            });
        }  
      });  
    },

    editJobVacancy(id){
      this.modal_status = 1;
      this.job_vac_id = id;
      this.disabled_field = 1;
      
      const url = `/api/job_vacancy/edit_job_vacancy/${id}`;
      axios.get(url).then(
        (response) => {
          if (response.data.success) {

            this.dialog = true;
            this.job_vacancies = response.data.resp;

            const resp_status = response.data.resp.status;

            if(resp_status == 1){
              this.job_vacancies.status = 1;
              this.switch1 = true;
            }else{
              this.job_vacancies.status = 0;
              this.switch1 = false;
            }
          }
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    }
  },
  computed: {
    formTitle(){
      return this.modal_status === 0 ? "Add Job Vacancies" : "Edit Job Vacancy Status";
    },

    activeStatus() {
      if (this.switch1) {
        this.job_vacancies.status = 1;
        return " Active";
      } else {
        this.job_vacancies.status = 0;
        return " Inactive";
      }
    },

    // validations
    positionErrors() {
      const errors = [];
      if (!this.$v.job_vacancies.position_id.$dirty) return errors;
      !this.$v.job_vacancies.position_id.required &&
        errors.push("Position is required.");
      return errors;
    },

    educattainErrors() {
      const errors = [];
      if (!this.$v.job_vacancies.educ_attain.$dirty) return errors;
      !this.$v.job_vacancies.educ_attain.required &&
        errors.push("Educational attainment is required.");
      return errors;
    },

    branchtypeErrors() {
      const errors = [];
      if (!this.$v.job_vacancies.branch_type.$dirty) return errors;
      !this.$v.job_vacancies.branch_type.required &&
        errors.push("Branch type is required.");
      return errors;
    },

    ...mapState("userRolesPermissions", ["userRoles", "userPermissions"]),
  },
  mounted() {
    axios.defaults.headers.common["Authorization"] 
      = "Bearer " + localStorage.getItem("access_token");

    this.getPosition();
    this.getJobVacancy();
    // this.websocket();
  }
};
</script>