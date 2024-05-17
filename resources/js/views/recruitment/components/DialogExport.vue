<template>
  <div>
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
          ><h3>Export Records</h3></v-toolbar>
          <v-card-text>
            <v-row>
              <v-col class="mt-4 mb-0 pb-0">
                <v-autocomplete
                  v-model="report_group"
                  :items="reportGroups"
                  item-value="group"
                  item-text="group"
                  label="Report Group"
                  return-object
                  :readonly="page_view != 'All Status'"
                  :error-messages="reportGroupErrors"
                  @input="$v.report_group.$touch()"
                  @blur="$v.report_group.$touch()"
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="my-0 py-0">
                <v-autocomplete
                  v-model="report_type"
                  :items="reportTypes"
                  label="Report Type"
                  item-text="text"
                  item-value="text"
                  return-object
                  :error-messages="reportTypeErrors"
                  @input="$v.report_type.$touch()"
                  @blur="$v.report_type.$touch()"
                  :disabled="report_group == '' ? true : false"
                  :readonly="page_view != 'All Status'"
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="my-0 py-0">
                <v-autocomplete
                  v-model="branch_id"
                  :items="branches"
                  item-text="name"
                  item-value="id"
                  label="Branch"
                  :error-messages="branchIdErrors"
                  @input="$v.branch_id.$touch()"
                  @blur="$v.branch_id.$touch()"
                  :readonly="hasRole('Branch Manager')"
                ></v-autocomplete>
              </v-col>
            </v-row>
            <template v-if="report_group.group == 'Detailed Report'">
              <v-row>
                <v-col class="my-0 py-0">
                  <v-autocomplete
                    v-model="branch_field_param"
                    :items="branchFieldParameters"
                    label="Branch Field Parameter"
                    item-text="description"
                    item-value="field_name"
                    :error-messages="branchFieldParameterErrors"
                    @input="$v.branch_field_param.$touch()"
                    @blur="$v.branch_field_param.$touch()"
                  ></v-autocomplete>
                </v-col>
              </v-row>
              <v-row>
                <v-col class="my-0 py-0">
                  <v-autocomplete
                    v-model="date_field_param"
                    :items="dateFieldParameters"
                    label="Date Field Parameter"
                    item-text="description"
                    item-value="field_name"
                    :error-messages="dateFieldParameterErrors"
                    :disabled="report_type ? false : true"
                    @input="$v.date_field_param.$touch()"
                    @blur="$v.date_field_param.$touch()"
                  ></v-autocomplete>
                </v-col>
              </v-row>
            </template>
            <v-row>
              <v-col class="my-0 py-0" v-if="report_type.text != 'Overall Count'">
                <v-text-field
                  label="From"
                  hint="MM/DD/YYYY"
                  persistent-hint
                  type="date"
                  prepend-icon="mdi-calendar"
                  v-model="date_from"
                  :error-messages="dateFromErrors"
                  :max="date_to ? date_to : null"
                  @input="$v.date_from.$touch() + validateDate('date_from')"
                  @blur="$v.date_from.$touch()"
                ></v-text-field>
              </v-col>
              <v-col class="my-0 py-0">
                <v-text-field 
                  :label=" report_type.text == 'Overall Count' ? 'As Of' : 'To'"
                  hint="MM/DD/YYYY"
                  persistent-hint
                  type="date"
                  prepend-icon="mdi-calendar"
                  v-model="date_to"
                  :error-messages="dateToErrors"
                  :min="date_from ? date_from : null"
                  @input="$v.date_to.$touch() + validateDate('date_to')"
                  @blur="$v.date_to.$touch()"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row class="mt-4">
              <v-col class="my-0 py-0" cols="3">
                <v-checkbox
                  name="get_empty_date"
                  v-model="get_empty_date"
                  dense
                  hide-details
                  class="ma-0 pa-0"
                >
                  <template v-slot:label>
                    <v-chip :color="get_empty_date ? 'primary' : '' " class="mt-2"> 
                      Get Empty Date
                    </v-chip>
                  </template>
                </v-checkbox>
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
                :fields = "exportJSONFields"
                :data = "exportJSONData"
                :meta = "json_meta"
                :name = "fileName"
              >
                Export Data
              </export-excel>
            </v-btn>  

            <v-btn
              color="primary"
              @click="exportData()"
              transition="scale-transition"
              v-if="generate_btn"
            >
              Generate
            </v-btn>
            <v-btn
              text
              @click="closeDialog()"
            >Close</v-btn>
          </v-card-actions>
        </v-card>
      </template>
    </v-dialog>
    <v-dialog
      v-model="loading"
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
  </div>
</template>
<script>
import axios from 'axios';
import { mapState, mapGetters } from 'vuex';
import { validationMixin } from "vuelidate";
import { required, requiredIf, minLength, email } from "vuelidate/lib/validators";
export default {
  mixins: [validationMixin],
  props: [
    'branches',
    'dialog',
    'page_view',
  ],
  validations: {
    branch_id: { required },
    date_from: {   
      required: requiredIf(function () {
        return this.report_type.text != 'Overall Count';
      }),
    },
    date_to: { required },
    report_group: { required },
    report_type: { required },
    date_field_param: { 
       required: requiredIf(function () {
        return this.report_group.group == 'Detailed Report';
      }),
    },
    branch_field_param: { 
      required: requiredIf(function () {
        return this.report_group.group == 'Detailed Report';
      }),
     },
  },
  data() {
    return {
      date_from: "",
      date_to: "", 
      dateErrors: {
        date_from: false,
        date_to: false,
      },
      branch_id: "",
      date_field_param: "",
      branch_field_param: "",
      json_fields: {
        // '#': 'cnt_id',
        'Progress Status': 'progress_status',
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
        'Orientation Status' : 'orientation_status',
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
      export_all_count: false,
      export_btn: false,
      generate_btn: true,
      loading: false,
      report_groups: [
        { 
          group: 'Front Page Report', 
          types: [
                  'Sourcing/Screening',
                  'Recruitment',
                  'Hiring', 
                  'Signing of Contract',
                  'Overall Count',
                ],
        },
        { 
          group: 'Detailed Report', 
          types: [
                    { value: 0, text: 'Screening' },
                    { value: 1, text: 'Initial Interview' },
                    { value: 2, text: 'Exam' },
                    { value: 3, text: 'B.I & Basic Req' },
                    { value: 4, text: 'Final Interview' },
                    { value: 5, text: 'Orientation' },
                    { value: 6, text: 'Hired' },
                 ],
        }
      ],
      report_type: "",
      report_group: "",
      get_empty_date: false,
      position_names: [
        'Branch Manager', 
        'Sales Supervisor', 
        'Credit & Collection Supervisor', 
        'Management System Supervisor',
        'Encoder',
        'Cashier',
        'Account Analyst',
        'Warehouseman',
        'Sales Specialist',
        'Technician',
        'Delivery/ Logistics Driver',
        'Delivery/Logistics Helper',
      ],
    }
  },
  methods: {
    exportData(){
      this.$v.$touch();

      if(!this.$v.$error && !this.dateErrors.date_from && !this.dateErrors.date_to){
   
        this.loading = true;

        const data = {
          'date_from': this.date_from,
          'date_to': this.date_to,
          'asOfDate': this.date_to,
          'branch_id': this.branch_id,
          'report_group': this.report_group.group,
          'report_type': this.report_type.text,
          'date_field_param': this.date_field_param,
          'get_empty_date': this.get_empty_date, 
        };
    
        let api = "/api/job_applicant/" + this.report_type.api;
        
        axios.post(api, data).then(
          (response) => {

            this.generate_btn = true;
            this.export_btn = false;
            this.loading = false;
            let data = response.data;

            if(data.success){
              
              if(this.report_group.group == 'Detailed Report')
              {
                if(data.applicants.length)
                {

                  this.$toaster.success('Success generating excel file.', {
                    timeout: 2000
                  });

                  this.generate_btn = false;
                  this.export_btn = true;

                  this.json_data = data.applicants;
                  // console.log(this.json_data);
                }
                else
                {
                  this.$toaster.error('No records found.', {
                    timeout: 2000
                  });
                }
              }
              else
              {
                this.loading = false;

                this.$toaster.success('Success generating excel file.', {
                  timeout: 2000
                });

                this.generate_btn = false;
                this.export_btn = true;

                this.json_data = data.applicants;
              }
              
              
            }else{
              this.$toaster.error('There are some error fetching data.', {
                timeout: 2000
              });
            }
          },
          (error) => {
            console.log(error);
            this.isUnauthorized(error);
          }
        );
      }
    },

    validateDate(model) {
      let min_date = new Date('1900-01-01').getTime();
      let max_date = new Date().getTime();
      let date = this[model];
      let date_value = new Date(date).getTime();
      
      let [year, month, day] = date.split('-');

      this.dateErrors[model] = false;

      if (date_value < min_date || year.length > 4) {
        this.dateErrors[model] = true;
      }  
    },

    validateDateRange(min, max) {
      let hasError = false;
      min = min ? new Date(min) : new Date('1900-01-01').getTime();
      max = max ? new Date(max) : new Date().getTime();

      if (max < min) {
        hasError = true;
      }  
      return { hasError: hasError };
    },
    clearData() {
      this.$v.$reset();
      this.date_from = "";
      this.date_to = ""; 
      this.dateErrors = {
        date_from: false,
        date_to: false,
      };
      this.branch_id = this.user.branch_id;
      this.branch_field_param = "";
      this.date_field_param = "";
      this.get_empty_date = false;
      this.export_btn = false;
      this.generate_btn = true;
      this.loading = false;
      this.report_group = this.reportGroups[0];
      this.report_type = "";

      if(this.page_view != 'All Status')
      {
        this.report_group = this.reportGroups[1];
        this.report_type = this.report_group.types.find((value) => { return value.text == this.page_view });
      }

    },
    closeDialog() {
      this.clearData();
      this.$emit('closeDialog');
    }
  },

  computed: {
    branchIdErrors() {
      const errors = [];
      if (!this.$v.branch_id.$dirty) return errors;
      !this.$v.branch_id.required &&
        errors.push("Please select a branch.");
      return errors;
    },
    reportGroupErrors() {
      const errors = [];
      if (!this.$v.report_group.$dirty) return errors;
      !this.$v.report_group.required &&
        errors.push("Please select report group.");
      return errors;
    },
    reportTypeErrors() {
      const errors = [];
      if (!this.$v.report_type.$dirty) return errors;
      !this.$v.report_type.required &&
        errors.push("Please select report type.");
      return errors;
    },
    dateFieldParameterErrors() {
      const errors = [];
      if (!this.$v.date_field_param.$dirty) return errors;
      !this.$v.date_field_param.required &&
        errors.push("Please select date field parameter.");
      return errors;
    },
    branchFieldParameterErrors() {
      const errors = [];
      if (!this.$v.branch_field_param.$dirty) return errors;
      !this.$v.branch_field_param.required &&
        errors.push("Please select branch field parameter.");
      return errors;
    },
    dateFromErrors() {
      const errors = [];
      if (!this.$v.date_from.$dirty) return errors;
      !this.$v.date_from.required &&
        errors.push("Please enter date.");
      if(this.dateErrors.date_from || this.validateDateRange(this.date_from, this.date_to).hasError)
      {
        errors.push('Invalid Date Range');
      } 
      return errors;
    },
    dateToErrors() {
      const errors = [];
      if (!this.$v.date_to.$dirty) return errors;
      !this.$v.date_to.required &&
        errors.push("Please enter date.");
      if(this.dateErrors.date_to || this.validateDateRange(this.date_from, this.date_to).hasError)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },

    exportJSONData() {
      let json_data = this.json_data;
      
      if(this.report_group.group == 'Front Page Report')
      {
       
        json_data = [];
        let fields = Object.keys(this.json_data); // fields are branches

        fields.forEach((field, i) => {
          json_data.push({
            branch: field,
          });
          
          let sub_fields = Object.keys(this.json_data[field]);

          sub_fields.forEach(sub_field => { // sub_fields are positions/branch/total_count field name

            let key_sub_field = sub_field.split('.').join(''); // remove '.' character

            Object.assign(json_data[i], { [key_sub_field.toLowerCase()]: this.json_data[field][sub_field] })  

          });

        });

      }

      return json_data;
    },

    exportJSONFields() {
      let json_fields = this.json_fields;
      
      if(this.report_group.group == 'Front Page Report')
      {
        json_fields = { 
          Branch: 'branch',
        };
        
        if(this.report_type.text == 'Overall Count')
        { 
          
          Object.assign(json_fields, { 
            'TOTAL_COUNT.total_applicants': 'total_count.total_applicants',  
            'TOTAL_COUNT.total_screening_initial_failed': 'total_count.total_screening_initial_failed', 
            'TOTAL_COUNT.total_initial_passed': 'total_count.total_initial_passed', 
          });

          let fields = Object.keys(this.exportJSONData[0]); // keys are position names

          this.position_names.forEach(position => {

            Object.assign(json_fields, { 
              [ position.toUpperCase() + '.beg_bal' ]: position.toLowerCase() + '.beg_bal', 
              [ position.toUpperCase() + '.qualified' ]: position.toLowerCase() + '.qualified',
              [ position.toUpperCase() + '.hired' ]: position.toLowerCase() + '.hired',
              [ position.toUpperCase() + '.expired' ]: position.toLowerCase() + '.expired',
              [ position.toUpperCase() + '.end_bal' ]: position.toLowerCase() + '.end_bal',
            });
  
          });
        }
        else 
        {
          // assign first TOTAL_COUNT.beg_bal for ordering of data purposes
          Object.assign(json_fields, { 'TOTAL_COUNT.beg_bal': 'total_count.beg_bal' });

          if(this.report_type.text == 'Sourcing/Screening')
          {
            Object.assign(json_fields, { 
              'TOTAL_COUNT.total_applicants': 'total_count.total_applicants', 
              'TOTAL_COUNT.total_screening_failed': 'total_count.total_screening_failed', 
              'TOTAL_COUNT.total_screening_passed': 'total_count.total_screening_passed', 
            });
          }
          else if(this.report_type.text == 'Recruitment')
          {
            Object.assign(json_fields, {  
              'TOTAL_COUNT.total_screening_passed': 'total_count.total_screening_passed', 
              'TOTAL_COUNT.total_recruitment_failed': 'total_count.total_recruitment_failed', 
              'TOTAL_COUNT.total_qualified': 'total_count.total_qualified',
            });
          }
          else if(this.report_type.text == 'Hiring')
          {
            Object.assign(json_fields, {  
              'TOTAL_COUNT.total_qualified': 'total_count.total_qualified', 
              'TOTAL_COUNT.total_final_interview_failed': 'total_count.total_final_interview_failed', 
              'TOTAL_COUNT.total_orientation': 'total_count.total_orientation',
              'TOTAL_COUNT.total_reserve': 'total_count.total_reserve',
            });
          }

          // assign last TOTAL_COUNT.end_bal for ordering of data purposes
          Object.assign(json_fields, { 'TOTAL_COUNT.end_bal': 'total_count.end_bal' });

          this.position_names.forEach(position => {
            
            // assign first beg_bal for ordering of data purposes
            Object.assign(json_fields, { [ position.toUpperCase() + '.beg_bal' ]: position.toLowerCase() + '.beg_bal' });

            if(this.report_type.text == 'Sourcing/Screening')
            {
              Object.assign(json_fields, { 
                [ position.toUpperCase() + '.total_applicants' ]: position.toLowerCase() + '.total_applicants',
                [ position.toUpperCase() + '.total_screening_failed' ]: position.toLowerCase() + '.total_screening_failed',
                [ position.toUpperCase() + '.total_screening_passed' ]: position.toLowerCase() + '.total_screening_passed',
              });
            }
            else if(this.report_type.text == 'Recruitment')
            {
              Object.assign(json_fields, { 
                [ position.toUpperCase() + '.total_screening_passed' ]: position.toLowerCase() + '.total_screening_passed',
                [ position.toUpperCase() + '.total_recruitment_failed' ]: position.toLowerCase() + '.total_recruitment_failed',
                [ position.toUpperCase() + '.total_qualified' ]: position.toLowerCase() + '.total_qualified',
              });
            }
            else if(this.report_type.text == 'Hiring')
            {
              Object.assign(json_fields, { 
                [ position.toUpperCase() + '.total_qualified' ]: position.toLowerCase() + '.total_qualified',
                [ position.toUpperCase() + '.total_final_interview_failed' ]: position.toLowerCase() + '.total_final_interview_failed',
                [ position.toUpperCase() + '.total_orientation' ]: position.toLowerCase() + '.total_orientation',
                [ position.toUpperCase() + '.total_reserve' ]: position.toLowerCase() + '.total_reserve',
                
              });
            }

            // assign last end_bal for ordering of data purposes
            Object.assign(json_fields, { [ position.toUpperCase() + '.end_bal' ]: position.toLowerCase() + '.end_bal' });
  
          });

        }
      }

      return json_fields;
    },

    reportGroups() {
      let items = [
        { 
          group: 'Front Page Report', 
          types: [
                  { 
                    text: 'Sourcing/Screening', 
                    hasPermission: this.hasPermission('sourcing-report'), 
                    api: 'export_sourcing',
                  },
                  { 
                    text: 'Recruitment', 
                    hasPermission: this.hasPermission('recruitment-report'),
                    api: 'export_recruitment', 
                  },
                  { 
                    text: 'Hiring', 
                    hasPermission: this.hasPermission('hiring-report'),
                    api: 'export_hiring', 
                  },
                  { 
                    text: 'Signing of Contract', 
                    hasPermission: this.hasPermission('signing-of-contract-report'),
                    api: 'export_signing_contract', 
                  },
                  { 
                    text: 'Overall Count', 
                    hasPermission: this.hasPermission('overall-count-report'),
                    api: 'export_total_number_of_applicants', 
                  }
                ],
          hasPermission: this.hasPermission('front-page-report'),
        },
        { 
          group: 'Detailed Report', 
          types: [  
                    { 
                      text: 'ALL', 
                      hasPermission: true,
                      api: 'export_applicants_new',
                    },
                    { 
                      text: 'Screening', 
                      hasPermission: this.hasPermission('jobapplicants-screening-list'),
                      api: 'export_applicants_new',
                    },
                    {
                      text: 'Initial Interview', 
                      hasPermission: this.hasPermission('jobapplicants-initial-interview-list'),
                      api: 'export_applicants_new', 
                    },
                    { 
                      text: 'Exam', 
                      hasPermission: this.hasPermission('jobapplicants-iq-test-list'),
                      api: 'export_applicants_new', 
                    },
                    { 
                      text: 'B.I & Basic Req', 
                      hasPermission: this.hasPermission('jobapplicants-screening-list'),
                      api: 'export_applicants_new', 
                    },
                    { 
                      text: 'Final Interview', 
                      hasPermission: this.hasPermission('jobapplicants-bi-list'),
                      api: 'export_applicants_new', 
                    },
                    { 
                      text: 'Orientation', 
                      hasPermission: this.hasPermission('jobapplicants-orientation-list'),
                      api: 'export_applicants_new', 
                    },
                    { 
                      text: 'Hired', 
                      hasPermission: this.hasPermission('jobapplicants-hired-list'),
                      api: 'export_applicants_new', 
                    },
                 ],
          hasPermission: this.hasPermission('detailed-report'),
        }
      ];

      let report_groups = [];

      items.forEach((value, i) => {
        
        if(value.hasPermission)
        {
          report_groups.push(value);
          let index = report_groups.indexOf(value)
          let report_types = [];

          value.types.forEach(val => {
            if(val.hasPermission)
            {
              report_types.push(val);
            }
          });

          report_groups[index].types = report_types;

        }  

      });
      
      return report_groups;
      
    },
    reportTypes() {

      let report_types = this.report_group.types;

      // if(this.hasRole('Branch Manager'))
      // {
      //   report_types = this.reportGroups[1].types;
      // }

      return report_types;

    },

    dateFieldParameters() {
      let  items = [
            { description: 'Date Applied', field_name: 'created_at' },
            { description: 'Initial Interview Date', field_name: 'initial_interview_date' },
            { description: 'Final Interview Date', field_name: 'final_interview_date' },
            { description: 'Orientation Date', field_name: 'orientation_date' },
            { description: 'Signing of Contract Date', field_name: 'signing_of_contract_date' },
          ];

      let fields = [];

      if(this.report_group.group == 'Detailed Report')
      {
        if(this.report_type.text == 'Screening') // if report type Screening
        {
          fields = [ items[0] ] ;
        }
        else if(['Initial Interview', 'Exam', 'B.I & Basic Req'].includes(this.report_type.text)) // if report type value Initial Interview or Exam or 3 B.I & Basic Req
        {
          fields = [ items[0], items[1] ];
        }
        else if(this.report_type.text == 'Final Interview') // if report type value Final Interview
        {
          fields = [ items[0], items[1], items[2] ];
        }
        else if(this.report_type.text == 'Orientation') // if report type Orientation
        {
          items.splice(4, 1) // remove index 4
          fields = items;
        }
        else if(['Hired', 'ALL'].includes(this.report_type.text)) // if report type value Hired or ALL
        {
          fields = items;
        }
      }

      return fields;

    },

    branchFieldParameters() {
      let items = [
            { description: 'Branch Applied', field_name: 'branch_id' },
            { description: 'Branch Complied', field_name: 'branch_complied' },
            { description: 'Employment Branch', field_name: 'employment_branch' },
          ];

      let fields = [];

      if(this.report_group.group == 'Detailed Report')
      {
        if(['Screening', 'Initial Interview', 'Exam'].includes(this.report_type.text)) // if report type Screening', 'Initial Interview', 'Exam
        {
          fields = [ items[0] ];
        }
        else if(['B.I & Basic Req', 'Final Interview'].includes(this.report_type.text)) // if report type 'B.I & Basic Req', 'Final Interview'
        {
          fields = fields = [ items[0], items[1] ] ;
        }
        else if(['Orientation', 'Hired', 'ALL'].includes(this.report_type.text)) // if report type 'Orientation', 'Hired', 'ALL'
        {
          fields = items;
        }
      }

      return fields;

    },

    fileName() { 
      let branch = this.branches.find((value) => { return value.id == this.branch_id });
      let report_type = this.report_group.group == 'Detailed Report' ? this.report_type.text + ' - Breakdown' : this.report_type.text;
      return report_type + ' (' + branch.name + ')';
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
          this.$emit('getData');
        }
      },
    },
    report_group() {
      this.$v.report_type.$reset();
      this.$v.date_field_param.$reset();
      this.$v.branch_field_param.$reset();
      this.date_field_param = "";
      this.branch_field_param = "";

      if(this.page_view != 'All Status')
      {
        this.report_type = this.page_view;
        this.report_type = this.report_group.types.find((value) => { return value.text == this.page_view });
      }

    },
    report_type() {
      this.$v.date_field_param.$reset();
      this.$v.branch_field_param.$reset();
      this.date_field_param = "";
      this.branch_field_param = "";
    },

    reportGroups() {
      this.report_group = this.reportGroups.length ? this.reportGroups[1] : "";
    }

  },

  mounted() {
    this.clearData();

    if(this.page_view != 'All Status')
    {
      this.report_group = this.reportGroups.length ? this.reportGroups[1] : "";
    }

    this.branch_id = this.user.branch_id;
  }
}
</script>