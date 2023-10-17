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
            Job Applicant Lists  (For Screening)
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
                @click="openExportDialog()"
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
                    @click="view_dialog = false"
                  >
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                  <v-toolbar-title> Applicant's Details </v-toolbar-title>
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
                            @click="!progress.disabled ? clickProgress(progress) : ''" 
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
                                    label="Address"
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
                          <ApplicantFiles :applicant="applicant"/>
                        </v-tab-item>
                      </v-tabs-items>
                    </v-col>
                    <v-divider vertical></v-divider>
                    <v-col cols="4" class="mt-4 px-6">
                      <v-card>
                        <v-toolbar :color="applicationProgress(applicant).color" dense>
                          <v-row>
                            <v-col  class="white--text d-flex justify-space-around">
                              <v-toolbar-title>
                                {{ applicationProgress(applicant).progress }}
                              </v-toolbar-title>
                            </v-col>
                          </v-row>
                        </v-toolbar>
                        <v-card-text>
                          <v-row class="mt-6">
                            <v-col class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                v-model="applicant.position_name"
                                label="Job Position Applied"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                            <v-col class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0" 
                                v-model="applicant.branch_name"
                                label="Branch Applied"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                label="Initial Interview Date"
                                type="date"
                                prepend-icon="mdi-calendar"
                                v-model="applicant.initial_interview_date"
                                readonly
                              ></v-text-field>
                            </v-col>
                            <v-col class="my-2 py-0">
                              <v-autocomplete
                                class="ma-0 pa-0"
                                :items="statusItems"
                                label="Initial Interview Status"
                                v-model="applicant.initial_interview_status"
                                readonly
                              ></v-autocomplete>
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col class="my-2 py-0">
                              <v-autocomplete
                                class="ma-0 pa-0"
                                v-model="applicant.position_preference"
                                :items="positions"
                                item-text="name"
                                item-value="id"
                                label="Position Preference"
                                multiple
                                readonly
                              >
                              </v-autocomplete>
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col class="my-2 py-0">
                              <v-autocomplete
                                class="ma-0 pa-0"
                                v-model="applicant.branch_preference"
                                :items="branches"
                                item-text="name"
                                item-value="id"
                                label="Branch Preference"
                                multiple
                                clearable
                                readonly
                              >
                              </v-autocomplete>
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                label="Branch Complied"
                                v-model="applicant.branch_complied"
                                readonly
                              >
                              </v-text-field>
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col class="my-2 py-0">
                              <v-autocomplete
                                class="ma-0 pa-0"
                                :items="statusItems"
                                label="IQ Examination Status"
                                v-model="applicant.iq_status"
                                readonly
                              ></v-autocomplete>
                            </v-col>
                            <v-col class="my-2 py-0">
                              <v-autocomplete
                                class="ma-0 pa-0"
                                :items="statusItems"
                                label="Backgroud Investigation Status"
                                v-model="applicant.bi_status"
                                readonly
                              ></v-autocomplete>
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                label="Final Interview Date"
                                type="date"
                                prepend-icon="mdi-calendar"
                                v-model="applicant.final_interview_date"
                                readonly
                              ></v-text-field>
                            </v-col>
                            <v-col class="my-2 py-0">
                              <v-autocomplete
                                class="ma-0 pa-0"
                                :items="statusItems"
                                label="Final Interview Status"
                                v-model="applicant.final_interview_status"
                                readonly
                              ></v-autocomplete>
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col class="my-0 py-0">
                              <v-autocomplete
                                :items="positions"
                                item-value="id"
                                item-text="name"
                                label="Employment Position"
                                v-model="applicant.employment_position"
                                readonly
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
                                v-model="applicant.employment_branch"
                                readonly
                              ></v-autocomplete>
                            </v-col>
                          </v-row>
                          <v-row>
                            <v-col class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                label="Orientation/Training Date"
                                type="date"
                                prepend-icon="mdi-calendar"
                                v-model="applicant.orientation_date"
                                readonly
                              ></v-text-field>
                            </v-col>
                            <v-col class="my-2 py-0">
                              <v-text-field
                                class="ma-0 pa-0"
                                label="Signing of Contract Date"
                                type="date"
                                prepend-icon="mdi-calendar"
                                v-model="applicant.signing_of_contract_date"
                                readonly
                              ></v-text-field>
                            </v-col>
                          </v-row>
                        </v-card-text>
                      </v-card>
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
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      label="Initial Interview Date"
                      type="date"
                      prepend-icon="mdi-calendar"
                      v-model="editedItem.initial_interview_date"
                      :error-messages="initialInterviewDateErrors"
                      @input="$v.editedItem.initial_interview_date.$touch()"
                      @blur="$v.editedItem.initial_interview_date.$touch()"
                      :disabled="editedItem.status != 1"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </template>
              <template v-if="step == 1">
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="statusItems"
                      item-value="value"
                      item-text="text"
                      label="Status"
                      v-model="editedItem.initial_interview_status"
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
                      :error-messages="applicantError.final_interview_date"
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
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-autocomplete
                      :items="positions"
                      item-value="id"
                      item-text="name"
                      label="Employment Position"
                      v-model="editedItem.employment_position"
                      :disabled="editedItem.final_interview_status != 1"
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
                      :disabled="editedItem.final_interview_status != 1"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-0 py-0">
                    <v-text-field
                      label="Orientation/Training Date"
                      type="date"
                      prepend-icon="mdi-calendar"
                      v-model="editedItem.orientation_date"
                      :error-messages="applicantError.orientation_date"
                      :disabled="editedItem.final_interview_status != 1"
                      @input="applicantError.orientation_date = []"
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
                      :error-messages="applicantError.signing_of_contract_date"
                      :disabled="editedItem.final_interview_status != 1"
                      @input="applicantError.signing_of_contract_date = []"
                    ></v-text-field>
                  </v-col>
                </v-row>
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

export default {
  components: {
    ApplicantFiles
  },
  mixins: [validationMixin],

  validations: {
    dateRangeText: { required },
    branch_id: { required },
    editedItem: {
      initial_interview_date: {
        required: requiredIf(function () {
          return this.initialInterviewDateIsRequired;
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
        { text: "Position", value: "position_name" },
        { text: "Branch Name", value: "branch_name" },
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
        'Last Name': 'lastname',
        'First Name': 'firstname',
        'Middle Name': 'middlename',
        'Complete Address': 'address',
        'Age': 'age',
        'Gender': 'gender',
        'Contact No.': 'contact_no',
        'Civil Status': 'civil_status',
        'Position Applied': 'position_name',
        'Educational Attainment': 'educ_attain',
        'Course/Specialization': 'course',
        'Date Applied': 'date_applied',
        'Branch Applied': 'branch_name',
        'Birthday': 'birthdate',
        'Email Address': 'email',
        // 'School graduated': 'school_grad',
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
        branch_complied: "",
        iq_status: "",
        bi_status: "",
        final_interview_status: "",
        initial_interview_date: "",
        position_preference: [],
        branch_preference: [],
        final_interview_date: "",
        employment_branch: "",
        orientation_date: "",
        hired_date: "",
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
        branch_complied: "",
        iq_status: "",
        bi_status: "",
        initial_interview_date: "",
        position_preference: [],
        branch_preference: [],
        final_interview_date: "",
        final_interview_status: "",
        employment_position: "",
        employment_branch: "",
        orientation_date: "",
        hired_date: "",
      },

      defaultItem: {
        status: "",
        initial_interview_status: "",
        branch_complied: "",
        iq_status: "",
        bi_status: "",
        final_interview_status: "",
        initial_interview_date: "",
        position_preference: [],
        branch_preference: [],
        final_interview_date: "",
        final_interview_status: "",
        employment_position: "",
        employment_branch: "",
        orientation_date: "",
        hired_date: "",
      },
      disabled: false,
      progress_items: ['Screening', 'Initial Interview', 'IQ Test', 'Background Investigation', 'Final Interview'],
    };
  },
  methods: {

    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },
    
    getApplicants() {
      this.v_table = false;
      this.loading = true;
      this.table_loader = true;
      axios.get("/api/job_applicant/screening_list").then(
        (response) => {
          let data = response.data
          console.log(data);
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
      this.view_dialog = true;
      this.applicant_id = id;

      const url = `/api/job_applicant/view_applicants_new/${id}`;
      axios.get(url).then(
        (response) => {
          this.view_applicant_loading = false;

          if (response.data.success) {
            const data = response.data;
            // console.log(data);
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
              let [start, end] = sy_attended.split(' to ');
              let sy_start = new Date(start);
              let sy_end = new Date(end);

              sy_attended = sy_attended ? sy_start.toLocaleDateString("en-US") + ' to ' +  sy_end.toLocaleDateString("en-US") : null;

              this.educ_attains.push(Object.assign(value, { sy_attended: sy_attended }));
              
            });

            data.experiences.forEach(value => {
              let date_of_service = value.date_of_service;
              let [start, end] = date_of_service.split(' to ');
              let service_start = new Date(start);
              let service_end = new Date(end);

              date_of_service = date_of_service ? service_start.toLocaleDateString("en-US") + ' to ' +  service_end.toLocaleDateString("en-US") : null;

              this.experiences.push(Object.assign(value, { date_of_service: date_of_service }));
              
            }); 

            let position_preference = this.applicant.position_preference;

            if(position_preference)
            {
              let positions = position_preference.split(',');
              this.applicant.position_preference = positions;
            }
            else
            {
              this.applicant.position_preference = [];
            }

            let branch_preference = this.applicant.branch_preference;
            if(position_preference)
            {
              let branches = branch_preference.split(',');
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
      if(this.job_applicants.length)
      {
        this.dialog = true
      }
      else
      {
        this.$toaster.warning('No record found.', {
          timeout: 2000
        });
      }
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
        formData.append('progress', this.progress_items[0]); // progress_items index 0 (Screening)
        formData.append('step', 0); // step 3 (Screening)

        axios.post("/api/job_applicant/export_applicants_new", formData, {
          headers: {
            'Content-Type': 'multipart/form-data' 
          }
        }).then(
          (response) => {
            console.log(response);

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
      this.branch_id = "";

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

      if(status == 1)
      {
        progress = "Initial Interview " + text;
        color = "purple";
        if(initial_interview_status == 1) // initial interview passed then set new progress
        {
          progress = "IQ Test " + text;
          color = "teal";

          if(iq_status == 1)// IQ Test passed then set new progress
          {
            progress = "Background Investigation " + text;
            color = "lime";

            if(bi_status == 1) // BI passed then set new progress
            {
              progress = "Final Interview " + text;

              if(final_interview_status == 1 ) // Final Interview passed then set new progress
              {
                progress = "Hired";
                color = "success";
                
              }
              else if(bi_status == 2)
              {
                progress = "Final Interview Failed";
                color = "error";
              }
            }
            else if(bi_status == 2)
            {
              progress = "Background Investigation Failed";
              color = "error";
            }

          }
          else if (iq_status == 2)
          {
            progress = "IQ Test Failed";
            color = "error";
          }

        }
        else if(initial_interview_status == 2) // Initial Interview Failed
        {
          progress = "Initial Interview Failed";
          color = "error";
          
        }
      }
      else if(status == 2) // not qualified
      {
        progress = "Not Qualified";
        color = "red";
      }

      return { progress: progress, color: color };

    },

    progressStatus(text, status) { 
      let color = '';
      let border_color = '';
      let icon = 'mdi-check-circle';
      let disabled = status >= 0 && status !== null? false : true; // if status is null then disable progress item (v-chip)
      if(status == 0) // if on process
      {
        color = 'warning';
        icon = '';
        text = '... ' + text;
      }
      else if(status == 1) // if qualified or passed
      {
        color = 'success';
        border_color = 'success';
        icon = 'mdi-check-circle';
      }
      else if(status == 2) // if not qualified, failed or did not comply
      {
        color = 'error';
        icon = 'mdi-close-circle';
      }

      return { color: color, border_color: border_color, icon: icon, text: text, status: status, disabled: disabled };
    },

    clickProgress(progress) {

      let index = this.progressItems.indexOf(progress);
      this.application_status_dialog = true;
      this.step = index;
      this.progressFormTitle = this.progress_items[index] + ' Status';

      let fields = Object.keys(this.editedItem);

      fields.forEach(field => {
        let field_value = this.applicant[field];
        
        if(['initial_interview_date', 'final_interview_date', 'signing_of_contract_date', 'orientation_date'].includes(field) && field_value)
        {
          let split_date_val = field_value.split('-');
          
          let day = split_date_val[0];
          let month = split_date_val[1];
          let year = split_date_val[2];

          field_value = `${year}-${month}-${day}`;
        }
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
    },

    showConfirmAlert() {

      this.$v.editedItem.$touch();

      if (!this.$v.editedItem.$error) {

        let progress = this.progress_items[this.step];

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

    initialInterviewDateErrors() {
      const errors = [];
      if (!this.$v.editedItem.initial_interview_date.$dirty) return errors;
      !this.$v.editedItem.initial_interview_date.required &&
        errors.push("Initial Interview Date is required.");
      return errors;
    },

    progressItems() {
      let progress_items = [
        this.progressStatus('Screening', this.applicant.status),
        this.progressStatus('Initial Interview', this.applicant.initial_interview_status),
        this.progressStatus('IQ Test', this.applicant.iq_status),
        this.progressStatus('Background Investigation', this.applicant.bi_status),
        this.progressStatus('Final Interview', this.applicant.final_interview_status),
      ];

      return progress_items;
    },

    statusItems() {
      let status_items = [
        { value: 0, text: 'On Process' },
        { value: 1, text: 'Passed' },
        { value: 2, text: 'Failed' },
      ];

      if(this.step > 1)
      {
        status_items.push({ value: 3, text: 'Did not Comply' });
      }

      return status_items
    },

    initialInterviewDateIsRequired() {
      return this.editedItem.status == 1; // if this.editedItem.status == 1 (passed) then interview date is required
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
            this.editedItem.position_preference.push(val);
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