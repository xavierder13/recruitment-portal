<template>
  <v-card class="ma-2">
    <v-card-text>
      <v-row>
        <v-col cols="6">
          <v-simple-table class="elevation-1 file_table">
            <template v-slot:default>
              <thead class="grey lighten-3 font-weight-bold">
                <tr>
                  <th width="10px"> # </th>
                  <th> 
                    Applicant's Files 
                    <v-btn x-small color="primary" class="ml-2" @click="dialog = true"> 
                      <v-icon small class="mr-1"> mdi-plus </v-icon> Add
                    </v-btn>  
                  </th>
                  <th width="90px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(file, i) in applicant_files" :key="file.id">
                  <td>{{ i + 1 }}</td>
                  <td> 
                     {{ file.title.length > 50 ? file.title.substr(0, 40) + "..." : file.title }}
                      <!-- <v-btn class="ma-0" 
                        small icon color="error" 
                        @click="confirmDelete(file)" 
                        v-if="hasPermissionToDelete && file.title != 'Resume'"
                      >
                        <v-icon> mdi-close-circle </v-icon>
                      </v-btn>
                      <v-btn x-small text class="blue--text text--darken-2 ma-0" @click="downloadfile(file)">
                      
                      </v-btn>  -->
                  </td>
                  <td>
                    <v-tooltip top>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn 
                          class="ma-0" 
                          icon 
                          color="error"
                          x-small 
                          v-bind="attrs" v-on="on"
                          @click="confirmDelete(file)" 
                          v-if="hasPermissionToDelete && file.title != 'Resume'"
                        >
                          <v-icon> mdi-delete </v-icon>
                        </v-btn>
                      </template>
                      <span>Delete</span>
                    </v-tooltip>
                    <v-tooltip top>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn 
                          color="primary" 
                          icon 
                          x-small 
                          class="blue--text text--darken-2 ma-0" 
                          @click="downloadfile(file)"
                          v-bind="attrs" v-on="on"
                        >
                          <v-icon>mdi-download</v-icon>
                        </v-btn> 
                      </template>
                      <span>Download</span>
                    </v-tooltip>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
      </v-row>
      <v-dialog v-model="dialog" max-width="500px" persistent>
        <v-card>
          <v-card-title>
            <span class="headline">Attach Applicant's File</span>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col class="my-0 py-0">
                  <v-autocomplete
                    name="selected_doc_type"  
                    v-model="selected_doc_type"
                    :items="doc_types"
                    label="Document Type"
                    :error-messages="selectedDocTypeErrors"
                    @change="$v.selected_doc_type.$touch()"
                    @blur="$v.selected_doc_type.$touch()"
                    required
                  ></v-autocomplete>
                </v-col>
              </v-row>
              <v-row v-if="selected_doc_type == 'Others'">
                <v-col class="my-0 py-0">
                  <v-text-field
                    v-model="specified_doc_type"
                    label="Document type"
                    hint="(specify document type)"
                    persistent-hint
                    :error-messages="specifiedDocTypeErrors"
                    @change="$v.specified_doc_type.$touch()"
                    @blur="$v.specified_doc_type.$touch()"
                  >
                  </v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col class="mt-2 py-0">
                  <v-file-input
                    v-model="file"
                    show-size
                    label="File input"
                    prepend-icon="mdi-paperclip"
                    required
                    :error-messages="fileErrors"
                    @change="$v.file.$touch() + (fileIsEmpty = false)"
                    @blur="$v.file.$touch()"  
                  >
                    <template v-slot:selection="{ text }">
                      <v-chip small label color="primary">
                        {{ text }}
                      </v-chip>
                    </template>
                  </v-file-input>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-divider class="mb-3 mt-0"></v-divider>
          <v-card-actions class="pa-0">
            <v-spacer></v-spacer>
            <v-btn color="#E0E0E0" @click="closeDialog()" class="mb-3">
              Cancel
            </v-btn>
            <v-btn
              color="primary"
              @click="uploadFile()"
              class="mb-3 mr-4"
              :disabled="uploadDisabled"
            >
              Upload
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-card-text>
  </v-card>
</template>

<script>

import axios from "axios";
import { validationMixin } from "vuelidate";
import { required, requiredIf, maxLength, email } from "vuelidate/lib/validators";
import { mapState, mapGetters } from "vuex";

export default {

  name: "ApplicantFiles",
  props: [ 
    'applicant',
  ],

  mixins: [validationMixin],

  validations: {
    selected_doc_type: { required },
    file: { required },
    specified_doc_type: {
      required: requiredIf(function () {
        return this.specifiedFileTypeIsRequired;
      }),
    },
  },

  data() {
    return {
      file: [],
      selected_doc_type: "",
      specified_doc_type: "",
      doc_types: ["Medical Certificate", "Diploma", "TOR", "Driver's License", "Others"],
      dialog: false,
      file: [],
      loading: true,
      uploadDisabled: false,
      applicant_files: [],
    }
  },methods:{
    getApplicantFile() {
      this.loading = true;
      axios.get("/api/jobapplicant_file/get_applicant_files/" + this.applicant.id).then(
        (response) => {
          this.applicant_files = response.data.applicant_files;
          this.loading = false;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    uploadFile() {
      this.$v.$touch();
      this.fileIsEmpty = false;
      this.fileIsInvalid = false;

      
      if (!this.$v.file.$error) {
        this.uploadDisabled = true;

        let formData = new FormData();
        let document_type = this.selected_doc_type == 'Others' ? this.specified_doc_type : this.selected_doc_type;
        
        formData.append('applicant_id', this.applicant.id);
        formData.append("file", this.file);
        formData.append("document_type", document_type);

        axios
          .post('/api/jobapplicant_file/store', formData, {
            headers: {
              Authorization: "Bearer " + localStorage.getItem("access_token"),
              "Content-Type": "multipart/form-data",
            },
          })
          .then(
            (response) => {
              this.errors_array = [];
              let data = response.data;
              if (data.success) {

                this.$swal({
                  position: "center",
                  icon: "success",
                  title: data.success,
                  showConfirmButton: false,
                  timer: 2500,
                });

                this.$v.$reset();
                this.closeDialog();

                this.applicant_files.push(data.applicant_file);

              }
              else
              {
                this.$swal({
                  position: "center",
                  icon: "error",
                  title: "Error uploading file",
                  showConfirmButton: false,
                  timer: 2500,
                });
              }
              this.uploadDisabled = false;
              this.uploading = false;
      
            },
            (error) => {
              this.isUnauthorized(error);
              this.uploadDisabled = false;
              console.log(error);
              this.uploading = false;
            }
          );
      }
    },

    closeDialog() {
      this.dialog = false;
      this.file = [];
      this.selected_doc_type = "";
      this.specified_doc_type = "";
      this.$v.$reset();
    }, 

    confirmDelete(file){
      this.$swal({
        title: "Delete File",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Delete",
      }).then((result) => {
        // <--

        if (result.value) {
  
          this.deleteFile(file);
          
        }
      });
    },

    deleteFile(file) {
      const data = { file_id: file.id };
      const index = this.applicant_files.indexOf(file);

      axios.post("/api/jobapplicant_file/delete", data).then(
        (response) => {
          console.log(response);
          if (response.data.success) {

            let index = this.applicant_files.indexOf(file);
            this.applicant_files.splice(index, 1);

            this.$swal({
              position: "center",
              icon: "success",
              title: response.data.success,
              showConfirmButton: false,
              timer: 2500,
            });

          }
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },
    
    downloadfile(file){
      // const file = applicant_file;
      
      const data = { file_id: file.id };

      axios.post('/api/job_applicant/file/download', data, { responseType: 'arraybuffer'})
        .then((response) => {
            var fileURL = window.URL.createObjectURL(new Blob([response.data]));
            var fileLink = document.createElement('a');
            fileLink.href = fileURL;
            fileLink.setAttribute('download', file.title + '.' + file.file_type);
            document.body.appendChild(fileLink);
            fileLink.click();
        }, (error) => {
          console.log(error);
        }
      );

      // let url = "http://localhost/api/wysiwyg/resume_files/" + file;

      // let url = "https://recruitmentportal.addessa.com" + "/wysiwyg/resume_files/" + file;    
      // window.open(url, 'Download');

    },
    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },
  },
  computed:{
    fileErrors() {
      const errors = [];
      if (!this.$v.file.$dirty) return errors;
      !this.$v.file.required && errors.push("File is required.");
      this.fileIsEmpty && errors.push("File is empty.");
      return errors;
    },
    selectedDocTypeErrors() {
      const errors = [];
      if (!this.$v.selected_doc_type.$dirty) return errors;
      !this.$v.selected_doc_type.required && errors.push("Please select document type.");
      return errors;
    },
    specifiedDocTypeErrors() {
      const errors = [];
      if (!this.$v.specified_doc_type.$dirty) return errors;
      !this.$v.specified_doc_type.required && errors.push("Please specify document type.");
      return errors;
    },
    specifiedFileTypeIsRequired() {
      return this.selected_doc_type == 'Others';
    },
    hasPermissionToDelete() {
      return this.hasPermission('jobapplicants-file-delete') && (this.applicant.final_interview_status != 1 || this.applicant.final_interview_status == null);
    },
    ...mapState("auth", ["user", "userIsLoaded"]),
    ...mapGetters("userRolesPermissions", ["hasRole", "hasPermission"]),
  },
  watch: {
    selected_doc_type() {
      this.specified_doc_type = "";
      this.$v.specified_doc_type.$reset();
    }
  },
  mounted(){
    this.getApplicantFile();
  }
}
</script>
