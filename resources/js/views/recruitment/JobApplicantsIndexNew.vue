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
          <ApplicantDataTable
            :job_applicants="job_applicants"
            :loading="loading"
            :table_title="tableTitle"
            :api_url="api_url"
            @getData="getApplicants"
            @exportData="openExportDialog"
            @applicationProgress="applicationProgress"
            @view_applicant="view_applicant"
            @deleteApplicant="deleteApplicant"
            :key="dataTableKey"
          />
        </v-card>

        <!-- Dialogs -->
        <DialogExport
          :branches="branches"
          :dialog="dialog"
          :page_view="pageView"
          @closeDialog="closeExportDialog"
          ref="DialogExport"
        />

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
                @click="closeApplicantDialog()"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
              <v-toolbar-title> Applicant's Details {{ applicant.progress_status }} </v-toolbar-title>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    icon 
                    dark 
                    v-bind="attrs" v-on="on"
                    @click="downloadPDF()"
                  > 
                    <v-icon>mdi-file-pdf</v-icon> 
                  </v-btn>
                </template>
                <span>Download PDF</span>
              </v-tooltip>  
            </v-toolbar>
            <v-card-text>
              <v-row>
                <v-col class="mt-8">
                  <div class="d-flex justify-center mb-6 bg-surface-variant">
                    <v-spacer></v-spacer>
                    <template v-for="(progress, i) in progressItems" v-if="!view_applicant_loading">
                      <v-chip 
                        class="ma-0" 
                        :color="progress.color" 
                        :ripple="false"
                      > 
                        <!-- @click="progress.text == 'Final Interview' && applicant.final_interview_status == 1 && hasPermission('jobapplicants-update-hired-details') ? viewProgress(progress) : ''"  -->
                        <v-icon class="mr-1"> {{ progress.icon }} </v-icon> 
                        {{ progress.text }}
                      </v-chip>
                      <v-divider :class="'mt-4 thick-divider ' + progress.border_color" v-if="progressItems.length - 1 > i "></v-divider>
                    </template>
                    <v-spacer></v-spacer>
                  </div>
                </v-col>
              </v-row>
              <v-divider></v-divider>
              <v-row>
                <v-col cols="8" class="mt-4">
                  <v-tabs 
                    background-color="cyan darken-3" 
                    dark 
                    v-model="tab" 
                    class="mb-2 px-2"
                    show-arrows
                    slider-color="teal-lighten-3"
                  >
                    <v-tab v-for="item in tabs" :key="item.tab">
                      {{ item.description }}
                    </v-tab>
                  </v-tabs>
                  <v-tabs-items v-model="tab">
                    <v-tab-item class="mt-2">
                      <v-card class="mx-2">
                        <v-card-title class="justify-center mb-0 pb-0">
                          <strong>Personal Information</strong>  
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                          <v-row>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.name"
                                label="Full name"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.address"
                                label="Present Address"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.address2"
                                label="Home Address"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.birth_place"
                                label="Birth Place"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.birthdate"
                                label="Birthday"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.age"
                                label="Age"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.gender"
                                label="Gender"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.civil_status"
                                label="Civil Status"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.contact_no"
                                label="Contact Number"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.email"
                                label="Email Address"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.educ_attain"
                                label="Highest Educational Attainment"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <!-- <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.course"
                                label="Course"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.school_grad"
                                label="School Graduated"
                                readonly
                              >
                              </v-text-field>
                            </v-col> -->
                            <v-col cols="4" class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.how_learn"
                                label="Job Application learned from"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                          </v-row>
                        </v-card-text>
                      </v-card>
                      <v-card class="mt-2 mx-2">
                        <v-card-title class="justify-center mb-0 pb-0">
                          <strong>Educational Background</strong>
                        </v-card-title>
                        <v-divider class="mb-0"></v-divider>
                        <v-card-text>
                          <template v-for="(item, i) in educ_attains">
                            <v-row>
                              <v-col>
                                <span class="text-h6">
                                  <strong>{{ item.educ_level.charAt(0).toUpperCase() + item.educ_level.slice(1) }}</strong> 
                                </span>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="School"
                                  v-model="item.school"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Course/Specialization"
                                  v-model="item.course"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  :label=" item.educ_level == 'Senior HighSchool' ? 'Strand' : 'Major'"
                                  v-model="item.major"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="S.Y Attended"
                                  v-model="item.sy_attended"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Honors Received"
                                  v-model="item.honors"
                                  readonly
                                ></v-text-field>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col class="my-2 py-0">
                                <v-divider v-if="educ_attains.length > i + 1" class="my-0"></v-divider>
                              </v-col>
                            </v-row>
                          </template>
                        </v-card-text>
                      </v-card>
                      <v-card class="mt-2 mx-2">
                        <v-card-title class="justify-center mb-0 pb-0">
                          <strong>Parents/Guardian/Spouse</strong>
                        </v-card-title>
                        <v-divider class="mb-0"></v-divider>
                        <v-card-text>
                          <template v-for="(item, i) in fam_members">
                            <v-row>
                              <v-col>
                                <span class="text-h6">
                                  <strong>{{ item.relationship.charAt(0).toUpperCase() + item.relationship.slice(1) }}</strong> 
                                </span>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Name"
                                  v-model="item.name"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Age"
                                  v-model="item.age"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Address"
                                  v-model="item.address"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Contact"
                                  v-model="item.contact"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Occupation"
                                  v-model="item.occupation"
                                  readonly
                                ></v-text-field>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col class="my-2 py-0">
                                <v-divider v-if="fam_members.length > i + 1" class="my-0"></v-divider>
                              </v-col>
                            </v-row>
                          </template>
                        </v-card-text>
                      </v-card>
                      <v-card class="mt-4 mx-2">
                        <v-card-title class="justify-center mb-0 pb-0">
                          <strong>Dependents</strong>  
                        </v-card-title>
                        <v-divider class="mb-0"></v-divider>
                        <v-card-text>
                          <template v-for="(item, i) in dependents">
                            <v-row>
                              <v-col>
                                <span class="text-h6">
                                  <strong>Dependent {{ i + 1 }}</strong> 
                                </span>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Name"
                                  v-model="item.name"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Relationship"
                                  v-model="item.relationship"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                class="ma-0 pa-0"
                                label="Age"
                                v-model="item.age"
                                readonly
                              ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Address"
                                  v-model="item.address"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Occupation"
                                  v-model="item.occupation"
                                  readonly
                                ></v-text-field>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col class="my-2 py-0">
                                <v-divider v-if="dependents.length > i + 1" class="my-0"></v-divider>
                              </v-col>
                            </v-row>
                          </template>
                        </v-card-text>
                      </v-card>
                    </v-tab-item>
                    <v-tab-item class="mt-2">
                      <v-card class="mx-2">
                        <v-card-text>
                          <template v-for="(item, i) in experiences">
                            <v-row>
                              <v-col>
                                <span class="text-h6">
                                  <strong>Work Experience {{ item.length > 1 ? i + 1 : ''}}</strong> 
                                </span>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Company/Employer"
                                  v-model="item.employer"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Position"
                                  v-model="item.position"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Salary"
                                  v-model="item.salary"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Date of Service"
                                  v-model="item.date_of_service"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Job Description"
                                  v-model="item.job_description"
                                  readonly
                                ></v-text-field>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col class="my-2 py-0">
                                <v-divider v-if="item.length > 1" class="my-0"></v-divider>
                              </v-col>
                            </v-row>
                          </template>
                        </v-card-text>
                      </v-card>
                    </v-tab-item>
                    <v-tab-item class="mt-2">
                      <v-card class="mx-2">
                        <v-card-text>
                          <template v-for="(item, i) in references">
                            <v-row>
                              <v-col>
                                <span class="text-h6">
                                  <strong>Reference {{ i + 1 }}</strong> 
                                </span>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Name"
                                  v-model="item.name"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Address"
                                  v-model="item.address"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Contact"
                                  v-model="item.contact"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Company"
                                  v-model="item.company"
                                  readonly
                                ></v-text-field>
                              </v-col>
                              <v-col 
                                class="my-2 py-0"
                                cols="12"
                                xs="12"
                                sm="6"
                                md="6"
                                lg="4"
                              >
                                <v-text-field
                                  class="ma-0 pa-0"
                                  label="Position"
                                  v-model="item.position"
                                  readonly
                                ></v-text-field>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col class="my-2 py-0">
                                <v-divider class="my-0"></v-divider>
                              </v-col>
                            </v-row>
                          </template>
                        </v-card-text>
                      </v-card>
                    </v-tab-item>
                    <v-tab-item>
                      <ApplicantFiles 
                        :applicant="applicant" 
                        @refreshApplicantFiles="refreshApplicantFiles" 
                        :key="componentKey" 
                        ref="ApplicantFiles"
                      />
                    </v-tab-item>
                  </v-tabs-items>
                </v-col>
                <v-divider vertical></v-divider>
                <v-col cols="4" class="mt-4 px-6">
        
                  <ApplicationProgressCard 
                    :applicant="applicant"
                    :color="applicationProgress(applicant).color"
                    :progress="applicationProgress(applicant).progress"
                    :progressIsEditable="progressIsEditable"
                    :userHasPermissionToUpdateStatus="userHasPermissionToUpdateStatus"
                    :statusItems="statusItems"
                    :branches="branches"
                    :positions="positions"
                    @viewProgress="viewProgress"
                  />
                  
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-dialog>

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
              <p class="text-center pt-4">
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

        <ApplicationProgressDialog
          :applicant="applicant"
          :applicant_files="applicant_files"
          :step="step"
          :dialog="application_status_dialog"
          :progressFormTitle="progressFormTitle"
          :progress_items="progress_items"
          :statusItems="statusItems"
          :progressStatus="progressStatus"
          :branches="branches"
          :positions="positions"
          @saveStatus="saveStatus"
          @closeDialog="closeProgressDialog"
        />
        
        <ApplicantDetailsPDF 
          :applicant='applicant' 
          :educ_attains='educ_attains'
          :experiences="experiences"  
          :references="references"
          :fam_members="fam_members"
          :dependents="dependents"
          ref="ApplicantDetailsPDF"
        />
      </v-main>
    </div>
  </div>
</template>
<style>
  .thick-divider{
    border: 2px solid;
    border-radius: 5px;
  }
</style>
<script>

import axios from "axios";
import { validationMixin } from "vuelidate";
import { required, requiredIf, maxLength, email } from "vuelidate/lib/validators";
import { mapState, mapGetters } from "vuex";
import ApplicantDataTable from './components/ApplicantDataTable.vue';
import ApplicantFiles from './components/ApplicantFiles.vue';
import ApplicantDetailsPDF from './components/ApplicantDetailsPDF.vue';
import ApplicationProgressCard from "./components/ApplicationProgressCard.vue";
import ApplicationProgressDialog from "./components/ApplicationProgressDialog.vue";
import DialogExport from "./components/DialogExport";
import moment from "moment";

export default {
  components: {
    ApplicantDataTable,
    ApplicantFiles,
    ApplicantDetailsPDF,
    ApplicationProgressCard,
    ApplicationProgressDialog,
    DialogExport
  },
  mixins: [validationMixin],

  validations: {
    dateRangeText: { required },
    branch_id: { required },
    editedItem: {
      status: { required },
      signing_of_contract_date: {
        required: requiredIf(function () {
          return this.signingContractDateIsRequired;
        }),
      },
    }
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
      asOfDate: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
      branch_id: "",

      applicant: {
        name: "",
        address: "",
        birthdate: "",
        age: "",
        gender: "",
        civil_status: "",
        contact_no: "",
        email: "",
        educ_attain: "",
        course: "",
        school_grad: "",
        how_learn: "",
        file: "",
        position_name: "",
        branch_name: "",
        status: "",
        screening_date: "",
        initial_interview_status: "",
        branch_id_complied: "",
        branch_complied: "",
        iq_status: "",
        iq_date: "",
        bi_status: "",
        bi_date: "",
        initial_interview_date: "",
        position_preference: [],
        branch_preference: [],
        final_interview_date: "",
        final_interview_status: "",
        final_interview_remarks: "",
        employment_position: "",
        employment_branch: "",
        hiring_officer_position: "",
        hiring_officer_name: "",
        orientation_date: "",
        orientation_status: "",
        orientation_remarks: "",
        signing_of_contract_date: "",
      },

      // export_btn
      export_btn: false,
      generate_btn: true,

      edit_applicant: [],
      appl_status: '',
      applicant_id: "",
      tab: null,
      tabs: [
        { tab: 1, description: "Personal Information"},
        { tab: 2, description: "Work Experiences" },
        { tab: 3, description: "References" },
        { tab: 4, description: "Files" },
      ],
      educ_attains: [],
      experiences: [],
      references: [],
      fam_members: [],
      dependents: [],
      applicant_files: [],
      applicantError: { 
        initial_interview_date: [],
        iq_date: [],
        bi_date: [],
        final_interview_date: [],
        orientation_date: [],
        signing_of_contract_date: [],
      },
      positions: [],
      view_applicant_loading: false,
      step: null,
      application_status_dialog: false,
      
      progress: {
        status: null,
        date: null,
      },

      progressFormTitle: "",

      editedItem: {
        status: "",
        initial_interview_status: "",
        branch_id_complied: "",
        branch_complied: "",   
        iq_status: "",
        bi_status: "",
        initial_interview_date: "",
        position_preference: [],
        branch_preference: [],
        final_interview_date: "",
        final_interview_status: "",
        final_interview_remarks: "",
        employment_position: "",
        employment_branch: "",
        hiring_officer_position: "",
        hiring_officer_name: "",
        orientation_date: "",
        orientation_status: "",
        orientation_remarks: "",
        signing_of_contract_date: "",
      },

      defaultItem: {
        status: "",
        initial_interview_status: "",
        branch_id_complied: "",
        branch_complied: "",
        iq_status: "",
        bi_status: "",
        initial_interview_date: "",
        position_preference: [],
        branch_preference: [],
        final_interview_date: "",
        final_interview_status: "",
        final_interview_remarks: "",
        employment_position: "",
        employment_branch: "",
        hiring_officer_position: "",
        hiring_officer_name: "",
        orientation_date: "",
        orientation_status: "",
        orientation_remarks: "",
        signing_of_contract_date: "",
      },
      disabled: false,
      progress_items: ['Screening', 'Initial Interview', 'Exam', 'B.I & Basic Req', 'Final Interview', 'Orientation'],
      dialog_preview: true,
      dataTableKey: 0,
      componentKey: 0, // use to force refresh component contents
      export_all_count: false, 
      editedIndex: -1,
      api_url: "get_applicants_new",
    };
  },
  methods: {

    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },

    // getBranch() {
    //   axios.get("/api/branch/index").then(
    //     (response) => {
    //       this.branches = response.data.branches;
    //     },
    //     (error) => {
    //       this.isUnauthorized(error);
    //     }
    //   );
    // },

    getApplicants() {
      this.v_table = false;
      this.loading = true;
      this.table_loader = true;
      axios.get("/api/job_applicant/" + this.api_url).then(
        (response) => {
          let data = response.data
 
          this.v_table = true;
          this.table_loader = false;
          this.loading = false;
          this.job_applicants = data.job_applicants;
          this.branches = data.branches;
          this.positions = data.positions;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    view_applicant(item){
  
      this.view_applicant_loading = true;
      this.applicant_id = item.id;
      this.editedIndex = this.job_applicants.indexOf(item);     

      const url = `/api/job_applicant/view_applicants_new/${item.id}`;
      axios.get(url).then(
        (response) => {
          this.view_applicant_loading = false;
          const data = response.data;          
                
          if (data.success) {
            let step = this.applicationProgress(data.applicant).step;
            let progress_fields = ['status', 'initial_interview_status', 'iq_status', 'bi_status', 'final_interview_status', 'orientation_status'];
            let field = progress_fields[step];

            // refresh data when there are some upated status/records detected
            if(item[field] != data.applicant[field])
            {
              this.$swal({
                title: "Updated Data Detected.",
                text: "There are some updated data detected. Refresh record List",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "primary",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Confirm",
              }).then((result) => {
                  if(result.value)
                  {
                    this.getApplicants();
                  }
              });
            }
            else
            {
              this.view_dialog = true;
              this.applicant = data.applicant;
              
              // this.educ_attains = data.educ_attains;
              // this.experiences = data.experiences;
              this.references = data.references;
              this.fam_members = data.fam_members;
              this.dependents = data.dependents;
              this.applicant_files = data.applicant_files;
              this.download_file = data.file;
              
              data.educ_attains.forEach(value => {
                let sy_attended = value.sy_attended;
                if(sy_attended)
                {
                  if(sy_attended.split(' to ').length > 1)
                  {
                    let [start, end] = sy_attended.split(' to ');
                    let sy_start = new Date(start);
                    let sy_end = new Date(end);

                    sy_attended = sy_attended ? sy_start.toLocaleDateString("en-US") + ' to ' +  sy_end.toLocaleDateString("en-US") : null;
                  }
                }
                
                this.educ_attains.push(Object.assign(value, { sy_attended: sy_attended }));
                
              });

              data.experiences.forEach(value => {
                let date_of_service = value.date_of_service;

                if(date_of_service)
                {
                  if(date_of_service.split(' to ').length > 1)// if has value format like '1900-01-01 to 1900-01-01'
                  {
                    let [start, end] = date_of_service.split(' to ');
                    let service_start = new Date(start);
                    let service_end = new Date(end);

                    date_of_service = date_of_service ? service_start.toLocaleDateString("en-US") + ' to ' +  service_end.toLocaleDateString("en-US") : null;
                  }
                }
            
                this.experiences.push(Object.assign(value, { date_of_service: date_of_service }));
                
              });   
              
              let position_preference = this.applicant.position_preference;
              
              if(position_preference)
              {
                let positionsIdArr = position_preference.split(',');
                let positions = positionsIdArr.map(Number);
                this.applicant.position_preference = positions;
              }
              else
              {
                this.applicant.position_preference = [];
              }

              let branch_preference = this.applicant.branch_preference;

              if(branch_preference)
              {
                let branchesIdArr = branch_preference.split(',');
                let branches = branchesIdArr.map(Number);
                this.applicant.branch_preference = branches;
              }
              else
              {
                this.applicant.branch_preference = [];
              }
            }

          }
        },
        (error) => {
          // this.isUnauthorized(error);
          console.log(error);
        }
      );
    },

    closeApplicantDialog()
    {
      this.view_dialog = false;
      this.tab = null;

      this.educ_attains = [];
      this.experiences = [];
      this.references = [];
      this.fam_members = [];
      this.dependents = [];
      this.applicant_files = [];
      this.editedIndex = -1;

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

    openExportDialog() {
      // this.dialog = true;
      // this.branch_id = this.user.branch_id;
      
      if(this.job_applicants.length)
      {
        this.dialog = true;
        this.branch_id = this.user.branch_id;
        // this.$refs.DialogExport.clearData();
      }
      else
      {
        this.$toaster.warning('No record found.', {
          timeout: 2000
        });
      }
    },

    clear_export(){
      this.generate_btn = true;
      this.export_btn = false;

      this.dates = [];
      this.asOfDate = (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10);
      this.export_all_count = false;
      this.branch_id = this.user.branch_id;

      this.$v.$reset();
    },

    saveStatus(data) {
      
      let position_preference = data.position_preference;
      
      if(position_preference)
      {
        let positionsIdArr = position_preference.split(',');
        let positions = positionsIdArr.map(Number);
        this.applicant.position_preference = positions;
      }
      else
      {
        this.applicant.position_preference = [];
      }

      let branch_preference = data.branch_preference;

      if(branch_preference)
      {
        let branchesIdArr = branch_preference.split(',');
        let branches = branchesIdArr.map(Number);
        this.applicant.branch_preference = branches;
      }
      else
      {
        this.applicant.branch_preference = [];
      }

      let date_fields = [
                          'date_applied', 
                          'screening_date', 
                          'initial_interview_date',
                          'iq_date', 
                          'bi_date',
                          'final_interview_date',
                          'orientation_date',
                          'signing_of_contract_date'
                        ];
      let preferences = ['position_preference', 'branch_preference'];

      let fields = Object.keys(this.applicant);

      fields.forEach(field => {

        if(!preferences.includes(field))
        {
          if(date_fields.includes(field))
          {
            let [m, d, y] = data[field] ? data[field].split('/') : [];
            let date = data[field]  ? `${y}-${m}-${d}` : '';
            this.applicant[field] = date; 
          }
          else
          {
            this.applicant[field] = data[field];
          }
        }

      });   

      this.applicant.employment_branch = data.employment_branch_id;
      this.applicant.employment_position = data.employment_position_id;
      
      this.job_applicants[this.editedIndex] = data;
      
      this.application_status_dialog = false;
      this.dataTableKey += 1; //to refresh/re-render ApplicantDataTable Component
    },

    applicationProgress(applicant) {
      
      let color = "warning";
      let status = applicant.status;
      let initial_interview_status = applicant.initial_interview_status;
      let iq_status = applicant.iq_status;
      let bi_status = applicant.bi_status;
      let final_interview_status = applicant.final_interview_status;
      let orientation_status = applicant.orientation_status;
      let step = 0;
      let statusArr = [status, initial_interview_status, iq_status, bi_status, final_interview_status, orientation_status];
      let statusErrorValues = [2, 3]; // failed, non-compliant
      
      if(statusArr.some(value => statusErrorValues.includes(value)))
      {
        color = "error";
      }
      else if(status == 1)
      {
        color = "purple";
        if(initial_interview_status == 1) // initial interview passed then set new progress
        {
          color = "teal";

          if(iq_status == 1)// Exam passed then set new progress
          {
            color = "lime";

            if(bi_status == 1) // BI passed then set new progress
            {
              color = "cyan";

              if(final_interview_status == 1 ) // Final Interview passed then set new progress
              {
                color = "secondary";

                if(orientation_status == 1 ) // Orientation passed then set new progress
                {
                  color = "success";
                }
              }
              else if(final_interview_status == 4) // Final Interview Reserved
              {
                color = "#1A237E";
              }
            }
          }
        }
      }
      else if(status == 2) // not qualified
      {
        color = "error";
      }

      return { progress: applicant.progress_status, color: color, step: step };

    },

    progressStatus(text, status) { 
      let color = '';
      let border_color = '';
      let icon = 'mdi-check-circle';
      let disabled = status >= 0 && status !== null ? false : true; // if status is null then disable progress item (v-chip)
      if(status == 0) // if on process
      {
        color = 'warning';
        icon = '';
        text = '... ' + text;
      }
      else if(status == 1 || status == 4) // if qualified or passed or reserved status (for final interview)
      {
        color = 'success';
        border_color = 'success';
        icon = 'mdi-check-circle';
      }
      else if(status == 2 || status == 3) // if not qualified, failed or Non-Compliant
      {
        color = 'error';
        icon = 'mdi-close-circle';
      }

      return { color: color, border_color: border_color, icon: icon, text: text, status: status, disabled: true };
    },

    viewProgress() {
      this.application_status_dialog = true;
      this.step = this.currentProgress;
    },
    
    closeProgressDialog() {
      this.application_status_dialog = false;
      this.step = null;
    },

    removeSelectedHeader(item) {
      let index = this.selectedTableHeaders.findIndex(header => header.value === item.value);
      this.selectedTableHeaders.splice(index, 1);
    },

    formatDate(date) {

      var date_val = moment(date, 'YYYY-MM-DD',true);
      if (!date || !date_val.isValid()) return null;

      return moment(date).format('MM/DD/YYYY');
    },

    downloadPDF() {
      this.$refs.ApplicantDetailsPDF.handleClickDownload();
    },

    refreshApplicantFiles(files) {
      this.applicant_files = files;
    },

    closeExportDialog() {
      this.dialog = false;
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
      let dates = [];

      this.dates.forEach(value => {
        dates.push(this.formatDate(value));
      });

      return this.export_all_count ? this.formatDate(this.asOfDate) : dates.join(' ~ ');
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

    progressItems() {
      let progress_items = [
        this.progressStatus('Screening', this.applicant.status),
        this.progressStatus('Initial Interview', this.applicant.initial_interview_status),
        this.progressStatus('Exam', this.applicant.iq_status),
        this.progressStatus('B.I & Basic Req', this.applicant.bi_status),
        this.progressStatus('Final Interview', this.applicant.final_interview_status),
        this.progressStatus('Orientation', this.applicant.orientation_status),
      ];

      return progress_items;
    },

    statusItems() {
      let status_items = [
        { value: 0, text: 'On Process' },
        { value: 1, text: 'Passed' },
        { value: 2, text: 'Failed' },
      ];

      if(this.currentProgress > 0)
      {
        status_items.push({ value: 3, text: 'Non-Compliant' });

        if(this.currentProgress == 4)
        {
          status_items.push({ value: 4, text: 'Reserved' });
        }
      }

      return status_items
    },

    dateHasError() {
      let hasError = false;
      let fields = Object.keys(this.dateErrors)

      fields.forEach(field => {
        if(this.dateErrors[field].status)
        {
          hasError = true;
        }
      });

      return hasError;
    },

    currentProgress() {
      let index = this.progressItems.length - 1; // default index is progress(Orientation)  

      // get the index of status value not 0; status with value not 0 is the current progress/step of applicant's application status with either On Process, Failed, Non-Compliant
      this.progressItems.forEach((value, i) => {
        if(value.status != 1 && value.status != null)
        {
          index = i;
        }
      });

      return index;
    },

    progressIsEditable() {
      let fields = [
        'final_interview_date',
        'final_interview_status',
        'employment_position',
        'employment_branch',
        'hiring_officer_position',
        'hiring_officer_name',
        'orientation_date',
        'orientation_status',
        'signing_of_contract_date',
      ];

      let isEditable = true;

      // let isEditable = false;

      // fields.forEach(field => {
      //   if(!this.applicant[field])
      //   {
      //     isEditable = true;
      //   }
      // });

      return isEditable;

    },

    userHasPermissionToUpdateStatus() {
      let hasPermission = true;

      // if user has rol Branch Manager and current phase or progress is final interview or orientation then restrict user to edit status
      if(this.hasRole('Branch Manager') && [4, 5].includes(this.currentProgress))
      {
        hasPermission = false;
      }

      return hasPermission;
    },

    tableTitle() {
      
      let text = this.pageView && this.pageView == "Hired" ? ' (Hired)' : ` (For ${this.pageView })`;
      
      return 'Job Applicant Lists ' + (this.pageView != 'All Status' ? text : ''); 
    },

    pageView() {
      let doctype = "All Status";

      let splitted_route = this.$router.history.current.path.split('/');

      if(splitted_route[2] == 'screening-list')
      {
        doctype = "Screening";
      }
      else if(splitted_route[2] == 'initial-interview-list')
      {
        doctype = "Initial Interview";
      }
      else if(splitted_route[2] == 'iq-test-list')
      {
        doctype = "Exam";
      }
      else if(splitted_route[2] == 'bi-list')
      {
        doctype = "B.I & Basic Req";
      }
      else if(splitted_route[2] == 'final-interview-list')
      {
        doctype = "Final Interview";
      }
      else if(splitted_route[2] == 'orientation-list')
      {
        doctype = "Orientation";
      }
      else if(splitted_route[2] == 'hired-list')
      {
        doctype = "Hired";
      }

      return doctype;
    },

    ...mapState("userRolesPermissions", ["userRoles", "userPermissions"]),
    ...mapState("auth", ["user", "userIsLoaded"]),
    ...mapGetters("userRolesPermissions", ["hasRole", "hasPermission"]),
  },
  watch: {
    userIsLoaded: {
      handler() {
        if (this.userIsLoaded) {
          this.branch_id = this.user.branch_id;
        }
      },
    },

    tab() {
      this.componentKey += 1;
    },

    export_all_count() {
      this.data = [];
      this.$v.dateRangeText.$reset();
    }

  },
  async mounted() {
    axios.defaults.headers.common["Authorization"] =
    await  "Bearer " + localStorage.getItem("access_token");
    await this.websocket();

    let splitted_route = this.$router.history.current.path.split('/');
    if(splitted_route[2] != 'index-new')
    {
      this.api_url = await splitted_route[2].split('-').join('_'); //e.g screening-list become screening_list
    }
    

    await this.getApplicants();
    
    // this.getBranch();
  }
};
</script>