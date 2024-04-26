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
            Job Applicant Lists (For Orientation)
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  fab
                  dark
                  class="mb-2 mr-2"
                  @click="getApplicants()"
                  v-bind="attrs" v-on="on"
                >
                  <v-icon>mdi-refresh</v-icon>
                </v-btn>
              </template>
              <span>Refresh Data</span>
            </v-tooltip>  
            <v-tooltip top v-if="hasPermission('jobapplicants-export')">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="#009688"
                  fab
                  dark
                  class="mb-2"
                  @click="openExportDialog()"
                  v-bind="attrs" v-on="on"
                >
                  <v-icon>mdi-file-excel</v-icon>
                </v-btn>
              </template>
              <span>Export Data</span>
            </v-tooltip>
          </v-card-title>

          <v-data-table
            :headers="headers"
            :items="job_applicants"
            :search="search"
            :loading="loading"
           
            v-if="userPermissions.jobapplicants_list"
          >
            <template v-slot:item.status="{ item }">
              <v-chip
                small
                dark
                :color="applicationProgress(item).color"
              >  
                {{ applicationProgress(item).progress }}
              </v-chip>
            
            </template>
            <template v-slot:item.actions="{ item }">
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
                v-if="userPermissions.jobapplicants_delete"
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
              persistent
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
                          hint="From (MM/DD/YYY) ~ To (MM/DD/YYY)"
                          persistent-hint
                        ></v-text-field>
                        <!-- <span class="font-weight-bold"> {{ export_all_count ? 'As Of: ' : 'From - to:' }}  </span>
                        <p>{{ export_all_count ? asOfDate : dates }}</p> -->

                        <v-autocomplete
                          class="mt-4"
                          v-model="branch_id"
                          :items="branches"
                          item-text="name"
                          item-value="id"
                          label="Branch parameter"
                          :error-messages="branchIdErrors"
                          @input="$v.branch_id.$touch()"
                          @blur="$v.branch_id.$touch()"
                          :readonly="hasRole('Branch Manager')"
                        ></v-autocomplete>
                      </v-col>
                    </v-row>
                  </v-card-text>
                  <v-divider class="my-0"></v-divider>
                  <v-card-actions class="pr-4 py-4">
                    <v-spacer></v-spacer>
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
                    @click="closeApplicantDialog()"
                  >
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                  <v-toolbar-title> Applicant's Details </v-toolbar-title>
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
                          > 
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

        <v-dialog v-model="application_status_dialog" max-width="500px" persistent>
         <v-card>
            <v-card-title class="mb-0 pb-0">
              <strong> {{ progressFormTitle }} </strong>  
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
              <template v-if="step == 0">
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="statusItems"
                      item-value="value"
                      item-text="text"
                      label="Status"
                      v-model="editedItem.status"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
              </template>
              <template v-if="step == 1">
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      label="Initial Interview Date"
                      type="date"
                      prepend-icon="mdi-calendar"
                      v-model="editedItem.initial_interview_date"
                      :error-messages="applicantError.initial_interview_date + dateErrors.initial_interview_date.msg"
                      @input="validateDate('initial_interview_date')"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="statusItems"
                      item-value="value"
                      item-text="text"
                      label="Status"
                      v-model="editedItem.initial_interview_status"
                      :disabled="editedItem.initial_interview_date ? false : true"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      v-model="editedItem.position_preference"
                      :items="positions"
                      item-text="name"
                      item-value="id"
                      label="Position Preference"
                      multiple
                      chips
                      clearable
                      :disabled="editedItem.initial_interview_status != 1"
                    >
                      <template v-slot:selection="data">
                        <v-chip
                          color="secondary"
                          v-bind="data.attrs"
                          :input-value="data.selected"
                          close
                          @click="data.select"
                          @click:close="removePositionPref(data.item.id)"
                        >
                          {{ data.item.name }}
                        </v-chip>
                      </template>
                    </v-autocomplete>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      v-model="editedItem.branch_preference"
                      :items="branches"
                      item-text="name"
                      item-value="id"
                      label="Branch Preference"
                      multiple
                      chips
                      clearable
                      :disabled="editedItem.initial_interview_status != 1"
                    >
                      <template v-slot:selection="data">
                        <v-chip
                          color="secondary"
                          v-bind="data.attrs"
                          :input-value="data.selected"
                          close
                          @click="data.select"
                          @click:close="removeBranchPref(data.item.id)"
                        >
                          {{ data.item.name }}
                        </v-chip>
                      </template>
                    </v-autocomplete>
                  </v-col>
                </v-row>
              </template>
              <template v-if="step == 2">
                <v-row v-if="IQFilesIsRequired">
                  <v-col class="my-2 py-0">
                    <span class="font-italic font-weight-bold red--text">Please upload {{ iq_required_files.join(', ') }} files</span>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      v-model="editedItem.branch_id_complied"
                      :items="branches"
                      item-text="name"
                      item-value="id"
                      label="Branch Complied"
                      :readonly="hasRole('Branch Manager')"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="statusItems"
                      item-value="value"
                      item-text="text"
                      label="Status"
                      v-model="editedItem.iq_status"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
              </template>
              <template v-if="step == 3">
                <v-row v-if="BIFilesIsRequired">
                  <v-col class="my-2 py-0">
                    <span class="font-italic font-weight-bold red--text">Please upload {{ bi_required_files.join(', ') }} files</span>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="statusItems"
                      item-value="value"
                      item-text="text"
                      label="Status"
                      v-model="editedItem.bi_status"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
              </template>
              <template v-if="step == 4">
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      label="Final Interview Date"
                      type="date"
                      prepend-icon="mdi-calendar"
                      v-model="editedItem.final_interview_date"
                      :error-messages="applicantError.final_interview_date + dateErrors.final_interview_date.msg"
                      @input="validateDate('final_interview_date')"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="statusItems"
                      item-value="value"
                      item-text="text"
                      label="Status"
                      v-model="editedItem.final_interview_status"
                      :disabled="editedItem.final_interview_date ? false : true"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
                <!-- if final_interview_status value is non-compliant -->
                <template v-if="editedItem.final_interview_status == 3">
                  <v-row>
                    <v-col class="my-0 py-0">
                      <v-autocomplete
                        :items="non_compliant_reasons"
                        label="Non-Compliant Reason"
                        v-model="selected_non_compliant_final_reason"
                      ></v-autocomplete>
                    </v-col>
                  </v-row>
                  <v-row v-if="selected_non_compliant_final_reason == 'Others (Specify)'">
                    <v-col class="my-0 py-0">
                      <v-text-field
                        label="Specify Non-Compliant Reason"
                        v-model="specified_non_compliant_final_reason"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </template>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="positions"
                      item-value="id"
                      item-text="name"
                      label="Employment Position"
                      v-model="editedItem.employment_position"
                      :disabled="![1, 4].includes(editedItem.final_interview_status)"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="branches"
                      item-value="id"
                      item-text="name"
                      label="Employment Branch"
                      v-model="editedItem.employment_branch"
                      :disabled="![1, 4].includes(editedItem.final_interview_status)"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="hiring_officer_positions"
                      label="Hiring Officer Position"
                      v-model="editedItem.hiring_officer_position"
                      :disabled="![1, 4].includes(editedItem.final_interview_status)"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      label="Hiring Officer Name"
                      v-model="editedItem.hiring_officer_name"
                      :disabled="![1, 4].includes(editedItem.final_interview_status)"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </template>
              <template v-if="step == 5">
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      label="Orientation/Training Date"
                      type="date"
                      prepend-icon="mdi-calendar"
                      v-model="editedItem.orientation_date"
                      :error-messages="applicantError.orientation_date + dateErrors.orientation_date.msg"
                      :disabled="![1, 4].includes(editedItem.final_interview_status)"
                      :readonly="hasRole('Branch Manager')"
                      @input="(applicantError.orientation_date = []) + validateDate('orientation_date')"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      label="Signing of Contract Date"
                      type="date"
                      prepend-icon="mdi-calendar"
                      v-model="editedItem.signing_of_contract_date"
                      :error-messages="signingContractErrors"
                      :disabled="![1, 4].includes(editedItem.final_interview_status)"
                      :readonly="hasRole('Branch Manager')"
                      @input="(applicantError.signing_of_contract_date = []) + validateDate('signing_of_contract_date')"
                      @blur="$v.editedItem.signing_of_contract_date.$touch()"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="statusItems"
                      item-value="value"
                      item-text="text"
                      label="Orientation Status"
                      v-model="editedItem.orientation_status"
                      :disabled="editedItem.orientation_date ? false : true"
                      :readonly="hasRole('Branch Manager')"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
                <!-- if final_interview_status value is non-compliant -->
                <template v-if="editedItem.orientation_status == 3">
                  <v-row>
                    <v-col class="my-0 py-0">
                      <v-autocomplete
                        :items="non_compliant_reasons"
                        label="Non-Compliant Reason"
                        v-model="selected_non_compliant_orientation_reason"
                        :readonly="hasRole('Branch Manager')"
                      ></v-autocomplete>
                    </v-col>
                  </v-row>
                  <v-row v-if="selected_non_compliant_orientation_reason == 'Others (Specify)'">
                    <v-col class="my-0 py-0">
                      <v-text-field
                        label="Specify Non-Compliant Reason"
                        v-model="specified_non_compliant_orientation_reason"
                        :readonly="hasRole('Branch Manager')"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </template>
              </template>
            </v-card-text>
            <v-divider class="mb-3 mt-0"></v-divider>
            <v-card-actions class="pa-0">
              <v-spacer></v-spacer>
              <v-btn color="#E0E0E0" @click="closeProgressDialog()" class="mb-3">
                Cancel
              </v-btn>
              <v-btn
                color="primary"
                @click="showConfirmAlert()"
                class="mb-3 mr-4"
                :disabled="disabled"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
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
import ApplicantFiles from './components/ApplicantFiles.vue';
import ApplicantDetailsPDF from './components/ApplicantDetailsPDF.vue';
import ApplicationProgressCard from "./components/ApplicationProgressCard.vue";
import moment from "moment";

export default {
  components: {
    ApplicantFiles,
    ApplicantDetailsPDF,
    ApplicationProgressCard,
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
      headers: [
        { text: "#", value: "cnt_id" },
        { text: "Full name", value: "name" },
        { text: "Position Applied", value: "position_name" },
        { text: "Branch Applied", value: "branch_name" },
        { text: "Branch Complied", value: "branch_complied" },
        { text: "Employment Branch", value: "employment_branch" },
        { text: "Orientation Date", value: "orientation_date" },
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
        // '#': 'cnt_id',
        'Last Name': 'lastname',
        'First Name': 'firstname',
        'Middle Name': 'middlename',
        'Poistion Applied': 'position_name',
        'Branch Applied': 'branch_name',
        'Gender': 'gender',
        'Contact': 'contact_no',
        'Date Applied': 'date_applied',
        'Source' : 'how_learn',
        'Screening': 'screening_status',
        'Interview Schedule': 'initial_interview_date',
        'Initial Interview': 'initial_interview_status',
        'Position Preference': 'position_preference',
        'Branch Preference': 'branch_preference',
        'Branch Complied': 'branch_complied',
        'Exam': 'iq_status',
        'B.I & Basic Req': 'bi_status',
        'Final Interview Date': 'final_interview_date',
        'Final Interview Status': 'final_interview_status',
        'Employment Position' : 'employment_position',
        'Employment Branch' : 'employment_branch',
        'Requirements' : 'requirements',
        'Hiring Officer Position': 'hiring_officer_position',
        'Hiring Officer Name': 'hiring_officer_name',
        'Date of Orientation & Training' : 'orientation_date',
        'Date of Contract Signing' : 'signing_of_contract_date',
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
        initial_interview_status: "",
        iq_status: "",
        bi_status: "",
        final_interview_status: "",
        initial_interview_date: "",
        position_preference: [],
        branch_preference: [],
        final_interview_date: "",
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
        hired_date: "",
      },

      defaultItem: {
        status: "",
        initial_interview_status: "",
        iq_status: "",
        bi_status: "",
        final_interview_status: "",
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
        hired_date: "",
      },
      disabled: false,
      progress_items: ['Screening', 'Initial Interview', 'Exam', 'B.I & Basic Req', 'Final Interview', 'Orientation', 'Orientation'],
      dateErrors: {
        initial_interview_date: { status: false, msg: "" },
        final_interview_date: { status: false, msg: "" },
        orientation_date: { status: false, msg: "" },
        signing_of_contract_date: { status: false, msg: "" },
      },
      hiring_officer_positions: [
        'General Manager',
        'HR Division Manager',
        'Recruitment Manager',
        'Immediate Division Manager',
        'Immediate Department Manager',
        'Branch Manager',
        'Immediate Branch Supervisor',
        'Recruitment Staff',
      ],
      componentKey: 0, // use to force refresh component contents
      non_compliant_reasons: [
        'Hired in other organization',
        'Back out due to Training',
        'Others (Specify)'
      ],
      selected_non_compliant_final_reason: "",
      specified_non_compliant_final_reason: "",
      selected_non_compliant_orientation_reason: "",
      specified_non_compliant_orientation_reason: "",
      iq_required_files: ['Exam'],
      bi_required_files: ['Birth Certificate', 'Diploma/Copy of Grades', 'Background Investigation'],
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
      axios.get("/api/job_applicant/orientation_list").then(
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

    view_applicant(id){
      
      this.view_applicant_loading = true;
      this.applicant_id = id;

      const url = `/api/job_applicant/view_applicants_new/${id}`;
      axios.get(url).then(
        (response) => {
          this.view_applicant_loading = false;

          if (response.data.success) {
            const data = response.data;
              console.log(data);
            // // refresh data when there are some upated status/records detected
            // if(data.applicant.final_interview_status > 0)
            // {
            //   this.$swal({
            //     title: "Updated Data Detected.",
            //     text: "There are some updated data detected. Refresh record List",
            //     icon: "info",
            //     showCancelButton: true,
            //     confirmButtonColor: "primary",
            //     cancelButtonColor: "#6c757d",
            //     confirmButtonText: "Confirm",
            //   }).then((result) => {
            //       if(result.value)
            //       {
            //         this.getApplicants();
            //       }
            //   });
            // }
            // else
            // {
              

            // }

            this.applicant = data.applicant;
            this.view_dialog = true;
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
      this.dialog = true;
      this.branch_id = this.user.branch_id;
      // if(this.job_applicants.length)
      // {
      //   this.dialog = true;
      //   this.branch_id = this.user.branch_id;
      // }
      // else
      // {
      //   this.$toaster.warning('No record found.', {
      //     timeout: 2000
      //   });
      // }
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
        formData.append('progress', this.progress_items[5]); // progress_items index 5 (Orientation)
        formData.append('step', 5); // step 5 (Orientation)

        axios.post("/api/job_applicant/export_applicants_new", formData, {
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
    
    clear_export(){
      this.generate_btn = true;
      this.export_btn = false;

      this.dates = [];
      this.branch_id = this.user.branch_id;

      this.$v.$reset();
    },

    saveStatus() {

      let data = this.editedItem;
      let position_preference = this.editedItem.position_preference.join(',');
      let branch_preference = this.editedItem.branch_preference.join(',');

      Object.assign(data, { 
        applicant_id: this.applicant.id, 
        step: this.step,
        position_preference: position_preference,
        branch_preference: branch_preference,
        final_interview_remarks: this.selected_non_compliant_final_reason == 'Others (Specify)' ? this.specified_non_compliant_final_reason : '',
        orientation_remarks: this.selected_non_compliant_orientation_reason == 'Others (Specify)' ? this.specified_non_compliant_orientation_reason : '',
      })

      axios.post("/api/job_applicant/update_status", data).then(
        (response) => {
          console.log(response.data);
          this.application_status_dialog = false;
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

            this.applicant_id = "";

            this.getApplicants();
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
  
    removePositionPref(item) {
      const index = this.editedItem.position_preference.indexOf(item);
      if (index >= 0) this.editedItem.position_preference.splice(index, 1);
    },

    removeBranchPref(item) {
      const index = this.editedItem.branch_preference.indexOf(item);
      if (index >= 0) this.editedItem.branch_preference.splice(index, 1);
    },

    applicationProgress(applicant) {
      
      let text = "On Process";
      let progress = "Screening " + text;
      let color = "warning";
      let status = applicant.status;
      let initial_interview_status = applicant.initial_interview_status;
      let iq_status = applicant.iq_status;
      let bi_status = applicant.bi_status;
      let final_interview_status = applicant.final_interview_status;
      let orientation_status = applicant.orientation_status;
      
      if(status == 1)
      {
        progress = "Initial Interview " + text;
        color = "purple";
        if(initial_interview_status == 1) // initial interview passed then set new progress
        {
          progress = "Exam " + text;
          color = "teal";

          if(iq_status == 1)// Exam passed then set new progress
          {
            progress = "BI " + text;
            color = "lime";

            if(bi_status == 1) // BI passed then set new progress
            {
              progress = "Final Interview " + text;
              color = "cyan";

              if(final_interview_status == 1 ) // Final Interview passed then set new progress
              {
                progress = "Orientation " + text;
                color = "secondary";

                if(orientation_status == 1 ) // Orientation passed then set new progress
                {
                  progress = "Hired";
                  color = "success";
                }
                else if(orientation_status == 2)
                {
                  progress = "Orientation Failed";
                  color = "error";
                }
                else if(orientation_status == 3) //failed or Non-Compliant
                {
                  progress = "Non-Compliant - Orientation";
                  color = "error";
                }
                
              }
              else if(final_interview_status == 2)
              {
                progress = "Final Interview Failed";
                color = "error";
              }
              else if(final_interview_status == 3) //failed or Non-Compliant
              {
                progress = "Non-Compliant - Final Interview";
                color = "error";
              }
              else if(final_interview_status == 4)
              {
                progress = "Reserved";
                color = "#1A237E";
              }
            }
            else if(bi_status == 2)
            {
              progress = "BI Failed";
              color = "error";
            }
            else if (bi_status == 3)
            {
              progress = "Non-Compliant - BI";
              color = "error";
            }

          }
          else if (iq_status == 2)
          {
            progress = "Exam Failed";
            color = "error";
          }
          else if (iq_status == 3)
          {
            progress = "Non-Compliant - Exam";
            color = "error";
          }

        }
        else if(initial_interview_status == 2) // Initial Interview Failed
        {
          progress = "Initial Interview Failed";
          color = "error";
          
        }
        else if (initial_interview_status == 3)
        {
          progress = "Non-Compliant - Initial Interview";
          color = "error";
        }
      }
      else if(status == 2) // not qualified
      {
        progress = "Not Qualified";
        color = "error";
      }

      return { progress: progress, color: color };

    },

    progressStatus(text, status) { 
      let color = '';
      let border_color = '';
      let icon = 'mdi-check-circle';
      let disabled = [0, 2, 3].includes(status) && status !== null ? false : true; // if status is not null or ('on process', 'failed', 'Non-Compliant') then enable progress item (v-chip) else disabled
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

      return { color: color, border_color: border_color, icon: icon, text: text, status: status, disabled: disabled };
    },

    viewProgress() {
      
      this.application_status_dialog = true;
      this.step = this.currentProgress;
      this.progressFormTitle = this.progress_items[this.step] + ' Status';

      let fields = Object.keys(this.editedItem);

      fields.forEach(field => {
        let field_value = this.applicant[field];
        
        // if(['initial_interview_date', 'final_interview_date', 'signing_of_contract_date', 'orientation_date'].includes(field) && field_value)
        // {
        //   let split_date_val = field_value.split('-');
          
        //   let day = split_date_val[0];
        //   let month = split_date_val[1];
        //   let year = split_date_val[2];

        //   field_value = `${year}-${month}-${day}`;
        // }
        this.editedItem[field] = field_value;
      });

      if(!this.applicant.branch_id_complied)
      {
        this.editedItem.branch_id_complied = this.user.branch_id;
      }

    },
    
    closeProgressDialog() {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.application_status_dialog = false;
      this.step = null;
      this.dateErrors = {
        initial_interview_date: { status: false, msg: "" },
        final_interview_date: { status: false, msg: "" },
        orientation_date: { status: false, msg: "" },
        signing_of_contract_date: { status: false, msg: "" },
      };
      this.$v.editedItem.$reset();
    },


    showConfirmAlert() {

      let progress = this.progress_items[this.step];

      if(this.signingContractDateIsRequired)
      {
        this.$v.editedItem.signing_of_contract_date.$touch();
      }

      if(!this.dateHasError && !this.$v.editedItem.signing_of_contract_date.$error)
      {
        this.$swal({
          title: "Are you sure?",
          text: `Update ${progress} Status`,
          icon: "warning",
          showCancelButton: true,
          cancelButtonColor: "#6c757d",
          confirmButtonColor: "#1976d2", 
          confirmButtonText: "Save",
        }).then((result) => {
          // <--

          if (result.value) {
            this.saveStatus();
          }
        });
      }
      
    },

    validateDate(field) {
      let min_date = new Date('1900-01-01').getTime();
      let max_date = new Date().getTime();
      let date = this.editedItem[field];
   
      if(date)
      {
        let date_value = new Date(date).getTime();
        let [year, month, day] = date.split('-');

        this.dateErrors[field].status = false;
        this.dateErrors[field].msg = "";

        // if (date_value < min_date || date_value > max_date || year.length > 4) {
        if (date_value < min_date || year.length > 4) {
          this.dateErrors[field].status = true;
          this.dateErrors[field].msg = 'Enter a valid date';
        }  
      }
        
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

      return dates.join(' ~ ');
    },

    signingContractErrors () {
      const errors = [];
      
      if (!this.$v.editedItem.signing_of_contract_date.$dirty) return errors;
      !this.$v.editedItem.signing_of_contract_date.required &&
        errors.push("Please enter date.");
      
      if(this.applicantError.signing_of_contract_date.length)
      {
        errors = this.applicantError.signing_of_contract_date;
      }

      if(this.dateErrors.signing_of_contract_date.msg)
      {
        errors.push(this.dateErrors.signing_of_contract_date.msg);
      }
      return errors;

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
      let index = this.progressItems.length - 1; // default index is progress(Final Interview)  

      // get the index of status value not 0; status with value not 0 is the current progress/step of applicant's application status with either On Process, Failed, Non-Compliant
      this.progressItems.forEach((value, i) => {
        if(value.status != 1 && value.status != null)
        {
          index = i;
        }
      });

      return index;
    },

    signingContractDateIsRequired() {
      return this.editedItem.orientation_status == 1; // if this.editedItem.status == 1 (passed) then signing contract date is required
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

      let isEditable = false;

      fields.forEach(field => {
        if(!this.applicant[field])
        {
          isEditable = true;
        }
      });

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

    IQFilesIsRequired() {
      let isRequired = false;
      let hasAllRequiredFiles = this.iq_required_files.every(value => this.applicantDocuments.includes(value));

      if(this.editedItem.iq_status == 1 && !hasAllRequiredFiles)
      {
        isRequired = true;
      }

      return isRequired;
    },

    BIFilesIsRequired() {
      let isRequired = false;
      let hasAllRequiredFiles = this.bi_required_files.every(value => this.applicantDocuments.includes(value));

      if(this.editedItem.bi_status == 1 && !hasAllRequiredFiles)
      {
        isRequired = true;
      }

      return isRequired;
    },

    applicantDocuments () {
      let files = [];

      this.applicant_files.forEach(value => {
        files.push(value.title)
      });

      return files;
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

    "editedItem.status"() {
      if(this.editedItem.status == 0) 
      {
        this.editedItem.initial_interview_date = null;
      }
    },

    "editedItem.initial_interview_status"() {
      this.editedItem.position_preference = [];
      this.editedItem.branch_preference = [];

      if(this.editedItem.initial_interview_status == 1) 
      { 

        let position_preference = this.applicant.position_preference;

        if(position_preference.length && position_preference)
        {
          position_preference.forEach(val => {
            this.editedItem.position_preference.push(val);
          }); 
        }
        else
        {
          this.editedItem.position_preference.push(this.applicant.position_id);
        }

        let branch_preference = this.applicant.branch_preference;

        if(branch_preference.length)
        { 
          branch_preference.forEach(val => {
            this.editedItem.branch_preference.push(val);
          });
        }
        else
        {
          this.editedItem.branch_preference.push(this.applicant.branch_id);
        }

      }
   
    },

    "editedItem.bi_status"() {
      if(this.editedItem.bi_status == 0) 
      {
        this.editedItem.final_interview_date = null;
      }
    },

    "editedItem.final_interview_date"() {
      if(!this.editedItem.final_interview_date) 
      {
        this.editedItem.final_interview_status = 0;
      }
    },
    
    "editedItem.final_interview_status"() {
      if(this.editedItem.final_interview_status == 0) 
      {
        this.editedItem.orientation_date = null;
        this.editedItem.signing_of_contract_date = null;
        this.editedItem.employment_branch = null;
        this.editedItem.employment_position = null;
      }

      if(this.editedItem.final_interview_status == 1)
      { 
        // if employment branch is null then assign default value from branch complied value
        if(!this.hasRole('Administrator') && this.editedItem.employment_branch == null )
        {
          this.editedItem.employment_branch = this.editedItem.branch_id_complied;
        }
        
      }
    },

    tab() {
      this.componentKey += 1;
    }

  },
  mounted() {
    axios.defaults.headers.common["Authorization"] =
      "Bearer " + localStorage.getItem("access_token");
    this.websocket();

    this.getApplicants();
    
    // this.getBranch();
  }
};
</script>