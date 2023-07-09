<template>
  <div class="flex column"
    v-if="userPermissions.jobapplicants_view"
  >
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
            Applicant Details
            <v-spacer></v-spacer>

            <v-btn
              color="#009688"
              @click="updateStatus(status = 1)"
              transition="scale-transition"
              outlined
              v-if="applicant.status < 1"
              class="mr-2"
            >
              <v-icon>mdi-thumb-up</v-icon>
            </v-btn>

            <v-btn
              color="error"
              @click="updateStatus(status = 2)"
              transition="scale-transition"
              outlined
              v-if="applicant.status < 1"
              class="mr-2"
            >
              <v-icon>mdi-thumb-down</v-icon>
            </v-btn>

            <v-btn 
              style="text-decoration: none; background-color: #ebebeb;"
              to="/jobapplicants/index"
              text
            >
              Back
            </v-btn>
            <v-container>
              <v-row>
                <v-col
                  cols="12"
                >
                  <v-chip
                    color="#37474f"
                    dark
                    small
                    v-if="applicant.status === 0"
                  >  
                    On process
                  </v-chip>
                  <v-chip
                    color="#009688"
                    dark
                    small
                    v-if="applicant.status === 1"
                  >  
                    Qualified
                  </v-chip>
                  <v-chip
                    color="#f44336"
                    dark
                    small
                    v-if="applicant.status === 2"
                  >  
                    Not Qualified
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
                    @click="downloadfile()"
                  >
                  </v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-title>
        </v-card>
      </v-main>
    </div>
  </div>
</template>
<script>

import axios from "axios";
import { mapState } from "vuex";

export default {
  data() {
    return {
      switch1: true,

      items: [
        {
          text: "Home",
          disabled: false,
          link: "/",
        },
        {
          text: "View Applicant",
          disabled: true,
        },
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
      
      download_file: "",
    };
  },

  methods: {
    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },

    view_applicant(){

      const id = this.$route.params.id;

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

    downloadfile(){
      const file = this.download_file;

      let url = "http://127.0.0.1:8000" + "/wysiwyg/resume_files/" + file;

      // let url = "https://recruitmentportal.addessa.com" + "/wysiwyg/resume_files/" + file;    
      window.open(url, 'Download');
    },

    updateStatus(status){

      this.$swal({
        title: "Are you sure,",
        text: "You want to update the status of this record?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#1976d2",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Proceed",
      }).then((result) => {
        if (result.isConfirmed) {

          const id = this.$route.params.id;

          let formData = new FormData();
          formData.append('applicant_id', id);
          formData.append('status_value', status);

          axios.post("/api/job_applicant/update_status", formData, {
            headers: {
              'Content-Type': 'multipart/form-data' 
            }
          }).then(
            (response) => {
              if(response.data.success){

                this.$toaster.success('You have successfully updated the status of the applicant.', {
                  timeout: 3000
                });

                this.view_applicant();
              }else{
                
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

        }else{
          this.$toaster.warning('You have cancelled updating the status of this applicant.', {
            timeout: 2000
          });
        }  
      });  
    }
  },
  computed: {
    ...mapState("userRolesPermissions", ["userRoles", "userPermissions"]),
  },
  mounted() {
    axios.defaults.headers.common["Authorization"] =
      "Bearer " + localStorage.getItem("access_token");

    this.view_applicant();
  },
};
</script>