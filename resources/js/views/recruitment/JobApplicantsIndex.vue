<template>
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
            Job Applicant Lists
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
            ></v-text-field>
            <v-spacer></v-spacer>
              <v-btn
                color="#009688"
                fab
                dark
                class="mb-2"
                @click="(dialog = true)"
                v-if="userPermissions.jobapplicants_export"
              >
                <v-icon>mdi-file-excel</v-icon>
              </v-btn>
          </v-card-title>

          <v-data-table
            :headers="headers"
            :items="job_applicants"
            :search="search"
            :loading="loading"
           
            v-if="userPermissions.jobvacancies_list, v_table"
          >
            <template v-slot:item.status="{ item }">
              <v-chip
                small
                v-if="item.status === 0"
              >  
                On process
              </v-chip>
              <v-chip
                color="orange"
                dark
                small
                v-if="item.status === 1"
              >  
                Qualified
              </v-chip>
              <v-chip
                color="#37474f"
                dark
                small
                v-if="item.status === 2"
              >  
                Not Qualified
              </v-chip>
              <v-chip
                color="#009688"
                dark
                small
                v-if="item.status === 3"
              >  
                Hired
              </v-chip>
              <v-chip
                color="#f44336"
                dark
                small
                v-if="item.status === 4"
              >  
                Failed
              </v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
              <!-- <v-icon
                small
                color="#fb8c00"
                @click="viewStatus(item.id)"
              >
                mdi-account-circle-outline
              </v-icon> -->
              <v-icon
                small
                color="blue"
                @click="view_applicant(item.id)"
              >
                mdi-eye
              </v-icon>
              <v-icon
                small
                color="red"
                @click="deleteApplicant(item.id)"
              >
                mdi-delete
              </v-icon>
            </template>
          </v-data-table>
        </v-card>

        <!-- Dialogs -->
        <v-row justify="space-around">
          <v-col cols="auto">
            <v-dialog
              transition="dialog-top-transition"
              max-width="600"
              v-model="dialog"
            >
              <template v-slot:default="dialog">
                <v-card>
                  <v-toolbar
                    color="#009688"
                    dark
                  ><h3>Export records</h3></v-toolbar>
                  <v-card-text>
                    <v-row class="mt-5">
                      <v-col
                        cols="12"
                        sm="6"
                      >
                        <v-date-picker
                          v-model="dates"
                          range
                        ></v-date-picker>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                      >
                        <v-text-field
                          v-model="dateRangeText"
                          label="Date parameter"
                          prepend-icon="mdi-calendar"
                          readonly
                          :error-messages="datefieldErrors"
                          @input="$v.dateRangeText.$touch()"
                          @blur="$v.dateRangeText.$touch()"
                        ></v-text-field>
                      <span class="font-weight-bold">From - to: </span>
                      <p>{{ dates }}</p>

                       <v-autocomplete
                          v-model="branch_id"
                          :items="branches"
                          item-text="name"
                          item-value="id"
                          label="Branch parameter"
                          :error-messages="branchIdErrors"
                          @input="$v.branch_id.$touch()"
                          @blur="$v.branch_id.$touch()"
                        ></v-autocomplete>
                      </v-col>
                    </v-row>
                  </v-card-text>
                  <v-divider></v-divider>
                  <v-card-actions class="justify-end">
                    <v-btn
                      color="success"
                      transition="scale-transition"
                      @click="(generate_btn = true) + (export_btn = false)"
                      v-if="export_btn"
                    >
                      <export-excel
                        :fields = "json_fields"
                        :data = "json_data"
                        :meta = "json_meta"
                        name = "applicants.xls"
                      >
                        Export Data
                      </export-excel>
                    </v-btn>  

                    <v-btn
                      color="primary"
                      @click="export_applications()"
                      transition="scale-transition"
                      v-if="generate_btn"
                    >
                      Generate
                    </v-btn>
                    <v-btn
                      text
                      @click="(dialog.value = false), clear_export()"
                    >Close</v-btn>
                  </v-card-actions>
                </v-card>
              </template>
            </v-dialog>
          </v-col>

          <!-- updatestatus -->
          <!-- <v-row justify="space-around">
            <v-col cols="auto">
              <v-dialog
                transition="dialog-top-transition"
                max-width="600"
                v-model="status_dialog"
              >
                <template v-slot:default="dialog">
                  <v-card>
                    <v-toolbar
                      color="#fb8c00"
                      dark
                    ><h3>Update Applicant Status</h3></v-toolbar>
                    <v-card-text>
                      <v-row>
                        <v-col cols="12" class="mt-5">
                          <v-list-item nine-line>
                            <v-list-item-content>
                              <v-list-item-title>
                                <v-row>
                                  <v-col cols="6">
                                    <h4 class="font-weight-bold">Applicant Details</h4>
                                  </v-col>
                                  <v-col cols="6" class="text-right">
                                    <h5>
                                      <v-chip color="#37474f" style="color: #ffffff;" v-if="appl_status === 0">On process</v-chip>
                                      <v-chip color="#009688" style="color: #ffffff;" v-else-if="appl_status === 1">Qualified</v-chip>
                                      <v-chip color="#f44336" style="color: #ffffff;" v-else-if="appl_status === 2">Not Qualified</v-chip>
                                      <span v-else></span>
                                    </h5>
                                  </v-col>
                                </v-row>
                                
                              </v-list-item-title>
                              <v-col
                                v-for="(applicant, index) in edit_applicant"
                                :key="index"
                              >
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Name: </span>{{ applicant.name }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Address: </span>{{ applicant.address }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Birthday: </span>{{ applicant.birthdate }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Age: </span>{{ applicant.age }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Position name: </span>{{ applicant.position_name }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Branch: </span>{{ applicant.branch_name }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Contact #: </span>{{ applicant.contact_no }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Course: </span>{{ applicant.course }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">School graduated: </span>{{ applicant.school_grad }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Educational attainment: </span>{{ applicant.educ_attain }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">Email address: </span>{{ applicant.email }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle>
                                  <span style="font-weight: bold;">How you learn: </span>{{ applicant.how_learn }}
                                </v-list-item-subtitle>
                                 <v-list-item-subtitle>
                                  <span style="font-weight: bold;">File attachment: </span>{{ applicant.file }} 
                                  <v-btn @click="downloadfile(applicant.file)" x-small color="#5b5b5b">
                                    <v-icon small color="#f98b00">
                                      mdi-download
                                    </v-icon>
                                  </v-btn>
                                </v-list-item-subtitle>
                              </v-col>  
                            </v-list-item-content>
                          </v-list-item>
                        </v-col>
                      </v-row>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions class="justify-end">
                      <v-btn
                        color="#009688"
                        @click="updateStatus(status = 1)"
                        transition="scale-transition"
                        outlined
                        v-if="appl_status < 1"
                      >
                        <v-icon>mdi-thumb-up</v-icon>
                      </v-btn>
                      <v-btn
                        color="error"
                        @click="updateStatus(status = 2)"
                        transition="scale-transition"
                        outlined
                        v-if="appl_status < 1"
                      >
                        <v-icon>mdi-thumb-down</v-icon>
                      </v-btn>
                      <v-btn
                        text
                        @click="(status_dialog = false), (appl_status = '')"
                      >Close</v-btn>
                    </v-card-actions>
                  </v-card>
                </template>
              </v-dialog>
            </v-col>
          </v-row> -->

          <v-row justify="center">
            <v-dialog
              v-model="view_dialog"
              fullscreen
              hide-overlay
              transition="dialog-bottom-transition"
            >
              <v-card>
                <v-toolbar
                  dark
                  color="primary"
                >
                  <v-btn
                    icon
                    dark
                    @click="view_dialog = false"
                  >
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                  <v-toolbar-title>Applicant's Details</v-toolbar-title>
                  <v-spacer></v-spacer>
                  <v-toolbar-items>
                    <v-btn
                      dark
                      text
                      @click="updateStatus(status = 1)"
                      v-if="applicant.status < 1"
                    >
                      Approve
                    </v-btn>
                    <v-btn
                      dark
                      text
                      @click="updateStatus(status = 2)"
                      v-if="applicant.status < 1"
                    >
                      Deny
                    </v-btn>

                    <v-btn
                      dark
                      text
                      @click="updateStatus(status = 3)"
                      v-if="applicant.status === 1"
                    >
                      Hired
                    </v-btn>
                    <v-btn
                      dark
                      text
                      @click="updateStatus(status = 4)"
                      v-if="applicant.status === 1"
                    >
                      Failed
                    </v-btn>
                  </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                  <v-container class="mt-3">
                    <v-row>
                      <v-col
                        cols="12"
                      >
                        <v-chip
                          small
                          v-if="applicant.status === 0"
                        >  
                          On process
                        </v-chip>
                        <v-chip
                          color="orange"
                          dark
                          small
                          v-if="applicant.status === 1"
                        >  
                          Qualified
                        </v-chip>
                        <v-chip
                          color="#37474f"
                          dark
                          small
                          v-if="applicant.status === 2"
                        >  
                          Not Qualified
                        </v-chip>
                        <v-chip
                          color="#009688"
                          dark
                          small
                          v-if="applicant.status === 3"
                        >  
                          Hired
                        </v-chip>
                        <v-chip
                          color="#f44336"
                          dark
                          small
                          v-if="applicant.status === 4"
                        >  
                          Failed
                        </v-chip>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col
                        cols="2"
                      >
                        <v-text-field
                          v-model="applicant.position_name"
                          label="Job Position"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="2"
                      >
                        <v-text-field
                          v-model="applicant.branch_name"
                          label="Branch Name"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                    </v-row>  
                    <v-row>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.name"
                          label="Full name"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.address"
                          label="Address"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.birthdate"
                          label="Birthday"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.age"
                          label="Age"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.contact_no"
                          label="Contact Number"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.email"
                          label="Email Address"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.educ_attain"
                          label="Educational Attainment"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.course"
                          label="Course"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.school_grad"
                          label="School Graduated"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.how_learn"
                          label="Job Application learned from"
                          readonly
                        >
                        </v-text-field>
                      </v-col>
                      <v-col
                        cols="4"
                      >
                        <v-text-field
                          v-model="applicant.file"
                          label="Resume file"
                          append-icon="mdi-download"
                          hint="(click to download file)"
                          persistent-hint
                          readonly
                          @click="downloadfile(applicant.file)"
                        >
                        </v-text-field>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
              </v-card>
            </v-dialog>
          </v-row>

          <!-- loader-dialog -->
          <v-dialog
            v-model="loader_dialog"
            hide-overlay
            persistent
            width="300"
          >
            <v-card
              color="primary"
              dark
            >
              <v-card-text>
                <p class="text-center">
                  Please stand by...
                </p>
                <v-progress-linear
                  indeterminate
                  color="white"
                  class="mb-0"
                ></v-progress-linear>
              </v-card-text>
            </v-card>
          </v-dialog>
        </v-row>
      </v-main>
    </div>
  </div>
</template>
<script>

import axios from "axios";
import { validationMixin } from "vuelidate";
import { required, maxLength, email } from "vuelidate/lib/validators";
import { mapState } from "vuex";

export default {

  mixins: [validationMixin],

  validations: {
    dateRangeText: { required },
    branch_id: { required },
  },

  data() {
    return {
      search: "",
      dialog: false,
      // status_dialog: false,
      loader_dialog: false,
      view_dialog: false,

      v_table: false,

      table_loader: false,
      job_applicants: [],
      headers: [
        { text: "#", value: "cnt_id" },
        { text: "Full name", value: "name" },
        { text: "Date Submitted", value: "created_at" },
        { text: "Status", value: "status" },
        { text: "Actions", value: "actions", sortable: false, width: "100px" },
      ],

      loading: false,
      items: [
        {
          text: "Home",
          disabled: false,
          link: "/",
        },
        {
          text: "Job Applicant Lists",
          disabled: true,
        },
      ],

      // select box
      branches: [],

      // parameters
      dates: [],
      branch_id: "",
      
      json_fields: {
        '#': 'cnt_id',
        'Date Applied': 'date_applied',
        'Branch Applied': 'branch_name',
        'Last Name': 'lastname',
        'First Name': 'firstname',
        'Middle Name': 'middlename',
        'Complete Address': 'address',
        'Birthday': 'birthdate',
        'Age': 'age',
        'Contact No.': 'contact_no',
        'Email Address': 'email',
        'Educational Attainment': 'educ_attain',
        'Course': 'course',
        'School graduated': 'school_grad',
        'How did you learn about job vacancy?': 'how_learn',
        'Status': 'status',
      },

      json_data: [],
      json_meta: [
        [
          {
            'key': 'charset',
            'value': 'utf-8'
          }
        ]
      ],

      applicant: {
        name: "",
        address: "",
        birthdate: "",
        age: "",
        contact_no: "",
        email: "",
        educ_attain: "",
        course: "",
        school_grad: "",
        how_learn: "",
        file: "",
        position_name: "",
        branch_name: ""
      },

      // export_btn
      export_btn: false,
      generate_btn: true,

      edit_applicant: [],
      appl_status: '',
      applicant_id: "",
    };
  },
  methods: {

    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },

    getBranch() {
      axios.get("/api/branch/index").then(
        (response) => {
          this.branches = response.data.branches;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    getApplicants() {
      this.v_table = false;
      this.loading = true;
      this.table_loader = true;
      axios.get("/api/job_applicant/get_applicants_old").then(
        (response) => {
          this.v_table = true;
          this.table_loader = false;
          this.loading = false;
          this.job_applicants = response.data.job_applicants;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    // viewApplicant(id){
    //   this.$router.push({ name: "jobapplicants.view", params: { id: id} });
    // },

    view_applicant(id){

      this.view_dialog = true;
      this.applicant_id = id;

      const url = `/api/job_applicant/view_applicants/${id}`;
      axios.get(url).then(
        (response) => {
          if (response.data.success) {
            // send data to Sockot.IO Server
            // this.$socket.emit("sendData", { action: "position-delete" });

            // console.log(response.data.resp[0]);

            const data = response.data.resp[0];
            this.applicant = data;

            this.download_file = data.file;
          }
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    deleteApplicant(id){

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
          const url = `/api/job_applicant/delete_applicant/${id}`;
          axios.get(url).then(
            (response) => {
              if (response.data.success) {
                // send data to Sockot.IO Server
                // this.$socket.emit("sendData", { action: "position-delete" });

                this.$toaster.success('You have successfully deleted the applicant.', {
                  timeout: 2000
                });

                this.getApplicants();
              }
              this.loading = false;
            },
            (error) => {
              this.isUnauthorized(error);
            }
          );

          }else{
            this.$toaster.warning('You have cancelled deleting the applicant.', {
              timeout: 2000
            });
        }  
      });  
    },

    export_applications(){

      this.loader_dialog = true;

      const date_from = this.dates[0];
      const date_to = this.dates[1];
      const branch_id = this.branch_id;
      
      if(date_from === undefined || branch_id === ''){

        this.loader_dialog = false;
        
        this.$toaster.error('Please select a date and branch.', {
          timeout: 5000
        });

        return;
      }

      if(date_from > date_to){

        this.loader_dialog = false;
        
        this.$toaster.error('Date from must be lesser than Date to.', {
          timeout: 5000
        });

        return;
      }

      if(date_from < date_to || date_from != undefined){

        let formData = new FormData();
        formData.append('date_from', date_from);
        formData.append('date_to', date_to);
        formData.append('branch_id', branch_id);

        axios.post("/api/job_applicant/export_applicants", formData, {
          headers: {
            'Content-Type': 'multipart/form-data' 
          }
        }).then(
          (response) => {
            // console.log(response);

            if(response.data.success){
              this.loader_dialog = false;

              this.$toaster.success('Success generating excel file.', {
                timeout: 2000
              });

              this.generate_btn = false;
              this.export_btn = true;

              this.json_data = response.data.resp;
            }else{

              this.generate_btn = true;
              this.export_btn = false;
              this.loader_dialog = false;

               this.$toaster.error('No records found.', {
                timeout: 2000
              });
            }
          },
          (error) => {
            console.log(error);
            this.isUnauthorized(error);
          }
        );
      }else{
        this.loader_dialog = false;

        this.$toaster.error('Error generating excel file.', {
          timeout: 5000
        });
      }
    },

    // viewStatus(id){
    //   this.status_dialog = true;

    //   const url = `/api/job_applicant/view_applicants/${id}`;
    //   axios.get(url).then(
    //     (response) => {
    //       if (response.data.success) {

    //         const data = response.data.resp;
            
    //         this.edit_applicant = data;
    //         this.appl_status = data[0].status;
    //         this.applicant_id = data[0].id;
    //       }
    //     },
    //     (error) => {
    //       this.isUnauthorized(error);
    //     }
    //   );
    // },

    updateStatus(status){

      let formData = new FormData();
      formData.append('applicant_id', this.applicant_id);
      formData.append('status_value', status);

      axios.post("/api/job_applicant/update_status", formData, {
        headers: {
          'Content-Type': 'multipart/form-data' 
        }
      }).then(
        (response) => {
          if(response.data.success){

            // this.status_dialog = false;
            this.view_dialog = false;

            this.$toaster.success('You have successfully updated the status of the applicant.', {
              timeout: 3000
            });

            var applicant = response.data.applicant;
            var updatedIndex = -1;
            for(var index = 0; index < this.job_applicants.length; index++) {
              var oldApplicant = this.job_applicants[index]
              if(oldApplicant.id == applicant.id) {
                applicant.cnt_id = oldApplicant.cnt_id
                updatedIndex = index;
                break;
              }
            }
            if(updatedIndex > -1) {
              this.job_applicants.splice(updatedIndex, 1, applicant)
            }
            // var updatedList = this.job_applicants.map(function(item){
            //   if(item.id == applicant.id) {
            //     applicant.cnt_id = item.cnt_id;
            //     return applicant;
            //   }
            //   return item;
            // });
            // this.job_applicants = updatedList;

            this.applicant_id = "";
          }else{
            // this.status_dialog = false;
            
            this.$toaster.error('You have errors updating the status of the applicant.', {
              timeout: 3000
            });
          }
        },
        (error) => {
          console.log(error);
          this.isUnauthorized(error);
        }
      );
    },

    downloadfile(applicant_file){
      const file = applicant_file;

      let url = "http://127.0.0.1:8000" + "/wysiwyg/resume_files/" + file;

      // let url = "https://recruitmentportal.addessa.com" + "/wysiwyg/resume_files/" + file;    
      window.open(url, 'Download');
    },
    
    clear_export(){
      this.generate_btn = true;
      this.export_btn = false;

      this.dates = [];
      this.branch_id = "";

      this.$v.$reset();
    },

    websocket() {
      // Socket.IO fetch data
      this.$options.sockets.sendData = (data) => {
        let action = data.action;
        if (action == "applicant-submit") {
          this.getApplicants();
        }
      };
    },
  },
  computed: {
    dateRangeText () {
      return this.dates.join(' ~ ')
    },

    // validations
    datefieldErrors() {
      const errors = [];
      if (!this.$v.dateRangeText.$dirty) return errors;
      !this.$v.dateRangeText.required &&
        errors.push("Please select a date range.");
      return errors;
    },

    branchIdErrors() {
      const errors = [];
      if (!this.$v.branch_id.$dirty) return errors;
      !this.$v.branch_id.required &&
        errors.push("Please select a branch.");
      return errors;
    },

    ...mapState("userRolesPermissions", ["userRoles", "userPermissions"]),
  },
  mounted() {
    axios.defaults.headers.common["Authorization"] =
      "Bearer " + localStorage.getItem("access_token");
    this.websocket();

    this.getApplicants();
    this.getBranch();
  }
};
</script>