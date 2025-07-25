<template>
  <v-dialog v-model="dialog" max-width="500px" persistent>
    <v-card>
      <v-card-title class="mb-0 pb-0">
        <strong> {{ formTitle }} </strong>  
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <template v-if="step == 0 || hasPermissionToUpdateHiringDetails">
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
                label="Screening Date"
                type="date"
                prepend-icon="mdi-calendar"
                v-model="editedItem.screening_date"
                :disabled="!editedItem.status"
                :error-messages="screeningDateErrors"
                @input="validateDate('screening_date')"
                @blur="$v.editedItem.screening_date.$touch()"
              ></v-text-field>
            </v-col>
          </v-row>
        </template>
        <template v-if="step == 1 || hasPermissionToUpdateHiringDetails">
          <v-row>
            <v-col class="my-0 py-0">
              <v-text-field
                label="Initial Interview Date"
                type="date"
                prepend-icon="mdi-calendar"
                v-model="editedItem.initial_interview_date"
                :disabled="initialFieldDisabled"
                :error-messages="initialInterviewDateErrors"
                @input="validateDate('initial_interview_date')"
                @blur="$v.editedItem.initial_interview_date.$touch()"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col class="my-0 py-0">
              <v-autocomplete
                :items="statusItems"
                item-value="value"
                item-text="text"
                label="Initial Interview Status"
                v-model="editedItem.initial_interview_status"
                :disabled="initialFieldDisabled"
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
                :error-messages="positionPreferenceErrors"
                @input="$v.editedItem.position_preference.$touch()"
                @blur="$v.editedItem.position_preference.$touch()"
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
                :error-messages="branchPreferenceErrors"
                @input="$v.editedItem.branch_preference.$touch()"
                @blur="$v.editedItem.branch_preference.$touch()"
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
        <template v-if="step == 2 || hasPermissionToUpdateHiringDetails">
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
                :disabled="!editedItem.iq_status"
              ></v-autocomplete>
            </v-col>
          </v-row>
          <v-row>
            <v-col class="my-0 py-0">
              <v-autocomplete
                :items="statusItems"
                item-value="value"
                item-text="text"
                label="IQ Status"
                v-model="editedItem.iq_status"
                :disabled="editedItem.initial_interview_status != 1"
              ></v-autocomplete>
            </v-col>
          </v-row>
          <v-row>
            <v-col class="my-0 py-0">
              <v-text-field
                label="IQ Date"
                type="date"
                prepend-icon="mdi-calendar"
                v-model="editedItem.iq_date"
                :disabled="!editedItem.iq_status"
                :error-messages="iqDateErrors"
                @input="validateDate('iq_date')"
                @blur="$v.editedItem.iq_date.$touch()"
              ></v-text-field>
            </v-col>
          </v-row>
        </template>
        <template v-if="step == 3 || hasPermissionToUpdateHiringDetails">
          <v-row v-if="BIFilesIsRequired">
            <v-col class="my-2 py-0">
              <span class="font-italic font-weight-bold red--text">Please upload {{ bi_required_files.join(', ') }} files</span>
            </v-col>
          </v-row>
          <v-row>
            <v-col class="my-0 py-0">
              <v-autocomplete q
                :items="statusItems"
                item-value="value"
                item-text="text"
                label="BI Status"
                v-model="editedItem.bi_status"
                :disabled="!editedItem.iq_status"
              ></v-autocomplete>
            </v-col>
          </v-row>
          <v-row>
            <v-col class="my-0 py-0">
              <v-text-field
                label="BI Date"
                type="date"
                prepend-icon="mdi-calendar"
                v-model="editedItem.bi_date"
                :disabled="!editedItem.bi_status"
                :error-messages="biDateErrors"
                @input="validateDate('bi_date')"
                @blur="$v.editedItem.bi_date.$touch()"
              ></v-text-field>
            </v-col>
          </v-row>
        </template>
        <template v-if="step == 4 || hasPermissionToUpdateHiringDetails">
          <v-row v-if="FinalFilesIsRequired">
            <v-col class="my-2 py-0">
              <span class="font-italic font-weight-bold red--text">Please upload {{ final_interview_required_files.join(', ') }} files</span>
            </v-col>
          </v-row>
          <v-row>
            <v-col class="my-0 py-0">
              <v-text-field
                label="Final Interview Date"
                type="date"
                prepend-icon="mdi-calendar"
                v-model="editedItem.final_interview_date"
                :disabled="!editedItem.bi_status"
                :error-messages="finalInterviewDateErrors"
                @input="validateDate('final_interview_date')"
                @blur="$v.editedItem.final_interview_date.$touch()"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col class="my-0 py-0">
              <v-autocomplete
                :items="statusItems"
                item-value="value"
                item-text="text"
                label="Final Interview Status"
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
                  :error-messages="selectedNonCompliantFinalReasonErrors"
                  @input="$v.selected_non_compliant_final_reason.$touch()"
                  @blur="$v.selected_non_compliant_final_reason.$touch()"
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row v-if="selected_non_compliant_final_reason == 'Others (Specify)'">
              <v-col class="my-0 py-0">
                <v-text-field
                  label="Specify Non-Compliant Reason"
                  v-model="specified_non_compliant_final_reason"
                  :error-messages="specifiedNonCompliantFinalReasonErrors"
                  @input="$v.specified_non_compliant_final_reason.$touch()"
                  @blur="$v.specified_non_compliant_final_reason.$touch()"
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
                :disabled="(!editedItem.final_interview_status)"
                :error-messages="employmentPositionErrors"
                @input="$v.editedItem.employment_position.$touch()"
                @blur="$v.editedItem.employment_position.$touch()"
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
                :disabled="(!editedItem.final_interview_status)"
                :error-messages="employmentBranchErrors"
                @input="$v.editedItem.employment_branch.$touch()"
                @blur="$v.editedItem.employment_branch.$touch()"
              ></v-autocomplete>
            </v-col>
          </v-row>
          <v-row>
            <v-col class="my-0 py-0">
              <v-autocomplete
                :items="hiring_officer_positions"
                label="Hiring Officer Position"
                v-model="editedItem.hiring_officer_position"
                :disabled="(!editedItem.final_interview_status)"
                :error-messages="hiringOfficerPositionErrors"
                @input="$v.editedItem.hiring_officer_position.$touch()"
                @blur="$v.editedItem.hiring_officer_name.$touch()"
              ></v-autocomplete>
            </v-col>
          </v-row>
          <v-row>
            <v-col class="my-0 py-0">
              <v-text-field
                label="Hiring Officer Name"
                v-model="editedItem.hiring_officer_name"
                :disabled="(!editedItem.final_interview_status)"
                :error-messages="hiringOfficerNameErrors"
                @input="$v.editedItem.hiring_officer_name.$touch()"
                @blur="$v.editedItem.hiring_officer_name.$touch()"
              ></v-text-field>
            </v-col>
          </v-row>
        </template>
        <template v-if="step == 5 || hasPermissionToUpdateHiringDetails">
          <v-row>
            <v-col class="my-0 py-0">
              <v-text-field
                label="Orientation/Training Date"
                type="date"
                prepend-icon="mdi-calendar"
                v-model="editedItem.orientation_date"
                :error-messages="orientationDateErrors"
                :disabled="(!editedItem.final_interview_status)"
                :readonly="hasRole('Branch Manager')"
                @input="validateDate('orientation_date')"
                @blur="$v.editedItem.orientation_date.$touch()"
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
                :disabled="(!editedItem.final_interview_status)"
                :readonly="hasRole('Branch Manager')"
                @input="validateDate('signing_of_contract_date')"
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
                  label="Orientation Non-Compliant Reason"
                  v-model="selected_non_compliant_orientation_reason"
                  :readonly="hasRole('Branch Manager')"
                  :error-messages="selectedNonCompliantOrientationReasonErrors"
                  @input="$v.selected_non_compliant_orientation_reason.$touch()"
                  @blur="$v.selected_non_compliant_orientation_reason.$touch()"
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-row v-if="selected_non_compliant_orientation_reason == 'Others (Specify)'">
              <v-col class="my-0 py-0">
                <v-text-field
                  label="Specify Non-Compliant Reason"
                  v-model="specified_non_compliant_orientation_reason"
                  :readonly="hasRole('Branch Manager')"
                  :error-messages="specifiedNonCompliantOrientationReasonErrors"
                  @input="$v.specified_non_compliant_orientation_reason.$touch()"
                  @blur="$v.specified_non_compliant_orientation_reason.$touch()"
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
</template>
<script>

import axios from 'axios';
import { mapState, mapGetters } from 'vuex';
import { validationMixin } from "vuelidate";
import { required, requiredIf, minLength, email } from "vuelidate/lib/validators";

export default {
  props: [
    'dialog', 
    'applicant', 
    'applicant_files',
    'step', 
    'userHasPermissionToUpdateStatus',
    'progressStatus',
    'statusItems',
    'progress_items',
    'positions',
    'branches',

  ],
  mixins: [validationMixin],

  validations: {

    editedItem: {
      status: { required },
      screening_date: {
        required: requiredIf(function () {
          return this.screeningDateIsRequired;
        }),
      },
      initial_interview_date: {
        required: requiredIf(function () {
          return this.initialInterviewDateIsRequired;
        }),
      },
      position_preference: {
        required: requiredIf(function () {
          return this.positionPreferenceIsRequired;
        }),
      },
      branch_preference: {
        required: requiredIf(function () {
          return this.branchPreferenceIsRequired;
        }),
      },
      iq_date: {
        required: requiredIf(function () {
          return this.iqDateIsRequired;
        }),
      },
      bi_date: {
        required: requiredIf(function () {
          return this.biDateIsRequired;
        }),
      },
      final_interview_date: {
        required: requiredIf(function () {
          return this.finalInterviewDateIsRequired;
        }),
      },
      orientation_date: {
        required: requiredIf(function () {
          return this.orientationDateIsRequired;
        }),
      },
      signing_of_contract_date: {
        required: requiredIf(function () {
          return this.signingContractDateIsRequired;
        }),
      },
      employment_position: {
        required: requiredIf(function () {
          return this.employmentPositionIsRequired;
        }),
      },
      employment_branch: {
        required: requiredIf(function () {
          return this.employmentBranchIsRequired;
        }),
      },
      hiring_officer_position: {
        required: requiredIf(function () {
          return this.hiringOfficerPositionIsRequired;
        }),
      },
      hiring_officer_name: {
        required: requiredIf(function () {
          return this.hiringOfficerNameIsRequired;
        }),
      },
    },
    selected_non_compliant_final_reason: {
      required: requiredIf(function () {
        return this.selectedNonCompliantFinalReasonIsRequired;
      }),
    },
    specified_non_compliant_final_reason: {
      required: requiredIf(function () {
        return this.specifiedNonCompliantFinalReasonIsRequired;
      }),
    },
    selected_non_compliant_orientation_reason: {
      required: requiredIf(function () {
        return this.selectedNonCompliantOrientationReasonIsRequired;
      }),
    },
    specified_non_compliant_orientation_reason: {
      required: requiredIf(function () {
        return this.specifiedNonCompliantOrientationReasonIsRequired;
      }),
    },
  },
  data() {
    return {
      editedItem: {
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

      defaultItem: {
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
      dateErrors: {
        screening_date: { status: false, msg: "" },
        initial_interview_date: { status: false, msg: "" },
        iq_date: { status: false, msg: "" },
        bi_date: { status: false, msg: "" },
        final_interview_date: { status: false, msg: "" },
        orientation_date: { status: false, msg: "" },
        signing_of_contract_date: { status: false, msg: "" },
      },
      applicantError: { 
        screening_date: [],
        initial_interview_date: [],
        iq_date: [],
        bi_date: [],
        final_interview_date: [],
        orientation_date: [],
        signing_of_contract_date: [],
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
      final_interview_required_files: ['Final Interview Result'],
      disabled: false,
    }
  },
  methods: {
    saveStatus() {

      let data = this.editedItem;
      
      let position_preference = data.position_preference ? data.position_preference.join(',') : '';
      let branch_preference = data.branch_preference ? data.branch_preference.join(',') : '';
      
      Object.assign(data, { 
        applicant_id: this.applicant.id, 
        step: this.step,
        position_preference: position_preference,
        branch_preference: branch_preference,
        final_interview_remarks: this.selected_non_compliant_final_reason == 'Others (Specify)' ? this.specified_non_compliant_final_reason : this.selected_non_compliant_final_reason,
        orientation_remarks: this.selected_non_compliant_orientation_reason == 'Others (Specify)' ? this.specified_non_compliant_orientation_reason : this.selected_non_compliant_orientation_reason,
      })

      let url = this.hasPermissionToUpdateHiringDetails ? "update_hiring_details" : "update_status";

      axios.post("/api/job_applicant/" + url, data).then(
        (response) => {
          
          if(response.data.success){
            
            this.$toaster.success('You have successfully updated the status of the applicant.', {
              timeout: 3000
            });

            this.$emit('saveStatus', response.data.applicant);
            
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
    showConfirmAlert() {
      let hiring_status = this.applicationProgress(this.applicant).progress;
  
      let progress = this.progress_items[this.step];
      let alert_msg = hiring_status != 'Hired' ? `Update ${progress} Status` : 'Save Hiring Details';

      this.$v.$touch();
      
      if( 
        !this.dateHasError && 
        !this.$v.$error && 
        !this.IQFilesIsRequired && 
        !this.BIFIlesIsRequired &&
        !this.FinalFilesIsRequired
      )
      {
        this.$swal({
          title: "Are you sure?",
          text: alert_msg,
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
    closeProgressDialog() {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.dateErrors = {
        screening_date: { status: false, msg: "" },
        initial_interview_date: { status: false, msg: "" },
        iq_date: { status: false, msg: "" },
        bi_date: { status: false, msg: "" },
        final_interview_date: { status: false, msg: "" },
        orientation_date: { status: false, msg: "" },
        signing_of_contract_date: { status: false, msg: "" },
      };
      this.$emit('closeDialog')
      this.$v.$reset();
      
    },

    validateDate(field) {
      this.applicantError[field] = []
      this.$v.editedItem[field].$touch();

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

    removePositionPref(item) {
      const index = this.editedItem.position_preference.indexOf(item);
      if (index >= 0) this.editedItem.position_preference.splice(index, 1);
    },

    removeBranchPref(item) {
      const index = this.editedItem.branch_preference.indexOf(item);
      if (index >= 0) this.editedItem.branch_preference.splice(index, 1);
    },

    applicationProgress() {
      let applicant = this.applicant;

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
      else if(status == 4)
      {
        color = "#1A237E";
      }

      return { progress: applicant.progress_status, color: color, step: step };

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

    screeningDateErrors () {
      const errors = [];
      
      if (!this.$v.editedItem.screening_date.$dirty) return errors;
      !this.$v.editedItem.screening_date.required &&
        errors.push("Please enter date.");
      
      if(this.applicantError.screening_date.length)
      {
        errors = this.applicantError.screening_date;
      }

      if(this.dateErrors.screening_date.msg)
      {
        errors.push(this.dateErrors.screening_date.msg);
      }
      return errors;

    },

    initialInterviewDateErrors () {
      const errors = [];
      
      if (!this.$v.editedItem.initial_interview_date.$dirty) return errors;
      !this.$v.editedItem.initial_interview_date.required &&
        errors.push("Please enter date.");
      
      if(this.applicantError.initial_interview_date.length)
      {
        errors = this.applicantError.initial_interview_date;
      }

      if(this.dateErrors.initial_interview_date.msg)
      {
        errors.push(this.dateErrors.initial_interview_date.msg);
      }
      return errors;

    },

    positionPreferenceErrors () {
      const errors = [];
      
      if (!this.$v.editedItem.position_preference.$dirty) return errors;
      !this.$v.editedItem.position_preference.required &&
        errors.push("Position Preference is required.");
      
      return errors;
    },

    branchPreferenceErrors () {
      const errors = [];
      
      if (!this.$v.editedItem.branch_preference.$dirty) return errors;
      !this.$v.editedItem.branch_preference.required &&
        errors.push("Branch Preference is required.");
      
      return errors;
    },

    iqDateErrors () {
      const errors = [];
      
      if (!this.$v.editedItem.iq_date.$dirty) return errors;
      !this.$v.editedItem.iq_date.required &&
        errors.push("Please enter date.");
      
      if(this.applicantError.iq_date.length)
      {
        errors = this.applicantError.iq_date;
      }

      if(this.dateErrors.iq_date.msg)
      {
        errors.push(this.dateErrors.iq_date.msg);
      }
      return errors;

    },

    biDateErrors () {
      const errors = [];
      
      if (!this.$v.editedItem.bi_date.$dirty) return errors;
      !this.$v.editedItem.bi_date.required &&
        errors.push("Please enter date.");
      
      if(this.applicantError.bi_date.length)
      {
        errors = this.applicantError.bi_date;
      }

      if(this.dateErrors.bi_date.msg)
      {
        errors.push(this.dateErrors.bi_date.msg);
      }
      return errors;

    },

    finalInterviewDateErrors () {
      const errors = [];
      
      if (!this.$v.editedItem.final_interview_date.$dirty) return errors;
      !this.$v.editedItem.final_interview_date.required &&
        errors.push("Please enter date.");
      
      if(this.applicantError.final_interview_date.length)
      {
        errors = this.applicantError.final_interview_date;
      }

      if(this.dateErrors.final_interview_date.msg)
      {
        errors.push(this.dateErrors.final_interview_date.msg);
      }
      return errors;

    },

    employmentPositionErrors() {
      const errors = [];
      
      if (!this.$v.editedItem.employment_position.$dirty) return errors;
      !this.$v.editedItem.employment_position.required &&
        errors.push("Employment Position is required.");
      
      return errors;
    },

    employmentBranchErrors() {
      const errors = [];
      
      if (!this.$v.editedItem.employment_branch.$dirty) return errors;
      !this.$v.editedItem.employment_branch.required &&
        errors.push("Employment Position is required.");
      
      return errors;
    },

    hiringOfficerNameErrors() {
      const errors = [];
      
      if (!this.$v.editedItem.hiring_officer_name.$dirty) return errors;
      !this.$v.editedItem.hiring_officer_name.required &&
        errors.push("Hiring Officer Name is required.");
      
      return errors;
    },

    hiringOfficerPositionErrors() {
      const errors = [];
      
      if (!this.$v.editedItem.hiring_officer_position.$dirty) return errors;
      !this.$v.editedItem.hiring_officer_position.required &&
        errors.push("Hiring Officer Position is required.");
      
      return errors;
    },

    selectedNonCompliantFinalReasonIsRequired() {
      return this.editedItem.final_interview_status == 3; //non-compliant
    },

    specifiedNonCompliantFinalReasonIsRequired() {
      return this.selected_non_compliant_final_reason == 'Others (Specify)';
    },

    selectedNonCompliantFinalReasonErrors() {
      const errors = [];
      
      if (!this.$v.selected_non_compliant_final_reason.$dirty) return errors;
      !this.$v.selected_non_compliant_final_reason.required &&
        errors.push("Please select reason.");
      
      return errors;
    },

    specifiedNonCompliantFinalReasonErrors() {
      const errors = [];
      
      if (!this.$v.specified_non_compliant_final_reason.$dirty) return errors;
      !this.$v.specified_non_compliant_final_reason.required &&
        errors.push("Please specify reason.");
      
      return errors;
    },

    orientationDateErrors () {
      const errors = [];
      
      if (!this.$v.editedItem.orientation_date.$dirty) return errors;
      !this.$v.editedItem.orientation_date.required &&
        errors.push("Please enter date.");
      
      if(this.applicantError.orientation_date.length)
      {
        errors = this.applicantError.orientation_date;
      }

      if(this.dateErrors.orientation_date.msg)
      {
        errors.push(this.dateErrors.orientation_date.msg);
      }
      return errors;

    },

    selectedNonCompliantOrientationReasonIsRequired() {
      return this.editedItem.orientation_status == 3; //non-compliant
    },

    specifiedNonCompliantOrientationReasonIsRequired() {
      return this.selected_non_compliant_orientation_reason == 'Others (Specify)';
    },

    selectedNonCompliantOrientationReasonErrors() {
      const errors = [];
      
      if (!this.$v.selected_non_compliant_orientation_reason.$dirty) return errors;
      !this.$v.selected_non_compliant_orientation_reason.required &&
        errors.push("Please select reason.");
      
      return errors;
    },

    specifiedNonCompliantOrientationReasonErrors() {
      const errors = [];
      
      if (!this.$v.specified_non_compliant_orientation_reason.$dirty) return errors;
      !this.$v.specified_non_compliant_orientation_reason.required &&
        errors.push("Please specify reason.");
      
      return errors;
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
    screeningDateIsRequired() {
      return this.editedItem.status > 0 && this.hasPermissionToUpdateHiringDetails; 
    },

    initialInterviewDateIsRequired() {
      return this.editedItem.initial_interview_status > 0;
    },

    positionPreferenceIsRequired() {
      return this.editedItem.initial_interview_status == 1;
    },

    branchPreferenceIsRequired() {
      return this.editedItem.initial_interview_status == 1;
    },

    employmentPositionIsRequired() {
      return this.editedItem.final_interview_status == 1;
    },

    employmentBranchIsRequired() {
      return this.editedItem.final_interview_status == 1;
    },

    hiringOfficerPositionIsRequired() {
      return this.editedItem.final_interview_status == 1;
    },

    hiringOfficerNameIsRequired() {
      return this.editedItem.final_interview_status == 1;
    },

    iqDateIsRequired() {
      return this.editedItem.iq_status > 0;
    },

    biDateIsRequired() {
      return this.editedItem.bi_status > 0; 
    },

    finalInterviewDateIsRequired() {
      return this.editedItem.final_interview_status > 0; 
    },

    orientationDateIsRequired() {
      return this.editedItem.orientation_status > 0; 
    },

    signingContractDateIsRequired() {
      return this.editedItem.orientation_status > 0; 
    },

    IQFilesIsRequired() {
      let isRequired = false;
      let hasAllRequiredFiles = this.iq_required_files.every(value => this.applicantDocuments.includes(value));

      if(this.editedItem.iq_status == 1 && !hasAllRequiredFiles && this.applicant.progress_status == 'Exam on Process')
      {
        isRequired = true;
      }

      return isRequired;
    },

    BIFilesIsRequired() {
      let isRequired = false;
      let hasAllRequiredFiles = this.bi_required_files.every(value => this.applicantDocuments.includes(value));
      
      if(this.editedItem.bi_status == 1 && !hasAllRequiredFiles && this.applicant.progress_status == 'B.I & Basic Req')
      {
        isRequired = true;
      }

      return isRequired;
    },

    FinalFilesIsRequired() {
      let isRequired = false;
      let hasAllRequiredFiles = this.final_interview_required_files.every(value => this.applicantDocuments.includes(value));

      if(this.editedItem.final_interview_status == 1 && !hasAllRequiredFiles && this.applicant.progress_status == 'Final Interview')
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
    formTitle() {
      return this.hasPermissionToUpdateHiringDetails ? 'Hiring Details' : this.progress_items[this.step] + ' Status';
    },
    hasPermissionToUpdateHiringDetails() {
      return this.hasPermission('jobapplicants-update-hiring-details');
    },
    initialFieldDisabled() {
      return [2, 4].includes(this.editedItem.status) || !this.editedItem.screening_date
    },
    ...mapState("auth", ["user", "userIsLoaded"]),
    ...mapGetters("userRolesPermissions", ["hasRole", "hasPermission"]),
  },
  watch: {
    dialog(){
      if(this.dialog)
      {
        let fields = Object.keys(this.editedItem);

        fields.forEach(field => {
          Object.assign(this.editedItem, { [field]: this.applicant[field] });
        });

        this.selected_non_compliant_final_reason = this.editedItem.final_interview_remarks;
        this.selected_non_compliant_orientation_reason = this.editedItem.orientation_remarks;

        if (this.editedItem.final_interview_remarks && !this.non_compliant_reasons.includes(this.editedItem.final_interview_remarks))
        {
          this.selected_non_compliant_final_reason = 'Others (Specify)';
          this.specified_non_compliant_final_reason = this.editedItem.final_interview_remarks;
        }

        if (this.editedItem.orientation_remarks  && !this.non_compliant_reasons.includes(this.editedItem.orientation_remarks))
        {
          this.selected_non_compliant_orientation_reason = 'Others (Specify)';
          this.specified_non_compliant_orientation_reason = this.editedItem.orientation_remarks;
        }
      }
    },
    "editedItem.status"() {
      let data = this.editedItem;
      if(data.status == 0) {
        data.screening_date = "";
        data.initial_interview_date = "";
        data.initial_interview_status = "";
        data.iq_status = "";
        data.bi_status = "";
        data.final_interview_status = "";
        data.orientation_status = "";
      }
      else if([2, 4].includes(data.status)) {
        data.initial_interview_status = "";
        data.initial_interview_date = "";
      }
      //  if screening is passed and screening_date is not null (1 value) and ( initial interview status is null, '' or 0 value)
      else if(data.status == 1 && data.screening_date  && ([null, 0, ''].includes(data.initial_interview_status)))
      {
        data.initial_interview_status = 0;
      }      
    },
    "editedItem.screening_date"() {
      let data = this.editedItem;
      if(data.status == 1 && data.screening_date && ([null, 0, ''].includes(data.initial_interview_status)))
      {
        data.initial_interview_status = 0;
      }
    },
    "editedItem.initial_interview_date"() {
      let data = this.editedItem;
      // if screening date has value and screening status is passed and initial interview date
      if(data.screening_date && data.status == 1 && [null, '', 0].includes(data.initial_interview_status)) {
        data.initial_interview_status = 0;
      }
      // else if(!data.initial_interview_date && ([null, 0, ''].includes(data.initial_interview_status)))
      // {
      //   data.initial_interview_status = "";
      // }
    },
    "editedItem.initial_interview_status"() {
      let data = this.editedItem;
      
      // status is null or on process, failed or non-compliant
      if(!data.initial_interview_status || [2, 3].includes(data.initial_interview_status)) {
        data.position_preference = "";
        data.branch_preference = "";
        data.branch_id_complied = "";
        data.iq_status = "";
        data.bi_status = "";
      }

      data.position_preference = [];
      data.branch_preference = [];

      if(data.initial_interview_status == 1) 
      { 

        let position_preference = this.applicant.position_preference;

        if(position_preference.length && position_preference)
        {
          position_preference.forEach(val => {
            data.position_preference.push(val);
          }); 
        }
        else
        {
          data.position_preference.push(this.applicant.position_id);
        }
        
        let branch_preference = this.applicant.branch_preference;
        
        if(branch_preference.length)
        { 
          branch_preference.forEach(val => {
            data.branch_preference.push(val);
          });
        }
        else
        {
          data.branch_preference.push(this.applicant.branch_id);
        }

        if(!data.iq_status)
        {
          data.iq_status = 0;
        }

      }
    },
    "editedItem.iq_status"() {
      let data = this.editedItem;
      if(!data.iq_status) {
        data.branch_id_complied = "";
        data.iq_date = "";
        data.bi_status = "";
      }
      else if(data.iq_status >= 0 && data.initial_interview_status == 1) {
        data.branch_id_complied = this.user.branch_id;
      }

      // failed or non-compliant
      if([2, 3].includes(data.iq_status))
      {
        data.bi_status = "";
      }
      
      //  if IQ/Exam is passed and iq_date is not null (1 value) and ( bi_status status is null, '' or 0 value)
      if(data.iq_status == 1 && data.iq_date  && ([null, 0, ''].includes(data.bi_status)))
      {
        data.bi_status = 0;
      }
    },
    "editedItem.iq_date"() {
      let data = this.editedItem;
      if(data.iq_status == 1 && data.iq_date && ([null, 0, ''].includes(data.bi_status)))
      {
        data.bi_status = 0;
      }
    },
    "editedItem.bi_status"() {
      let data = this.editedItem;
      if(!data.bi_status) {
        data.bi_date = "";
        data.final_interview_status = "";
        data.final_interview_date = "";
      }
      else if(data.bi_status == 0 && data.iq_status == 1) {
        data.final_interview_status = 0;
      }

      // failed or non-compliant
      if([2, 3].includes(data.bi_status))
      {
        data.final_interview_status = "";
      }

      //  if BI is passed and bi_date is not null (1 value) and ( final_interview_status is null, '' or 0 value)
      if(data.bi_status == 1 && data.bi_date && ([null, 0, ''].includes(data.final_interview_status)))
      {
        data.final_interview_status = 0;
      }
    },
    "editedItem.bi_date"() {
      let data = this.editedItem;
      if(data.bi_status == 1 && data.bi_date && ([null, 0, ''].includes(data.final_interview_status)))
      {
        data.final_interview_status = 0;
      }
    },
    "editedItem.final_interview_date"() {
      let data = this.editedItem;
      if(data.final_interview_status == 1 && data.final_interview_date && ([null, 0, ''].includes(data.orientation_status)))
      {
        data.orientation_status = 0;
      }
    },
    "editedItem.final_interview_status"() {
      let data = this.editedItem;
      if(data.final_interview_status == 1 && ([null, 0, ''].includes(data.orientation_status)))
      {
        data.orientation_status = 0;
        data.selected_non_compliant_final_reason = "";
        data.specified_non_compliant_final_reason = "";
      }
      else if(!data.final_interview_status) {
        data.employment_branch = "";
        data.employment_position = "";
        data.hiring_officer_name = "";
        data.hiring_officer_position = "";
        data.orientation_date = "";
        data.orientation_status = "";
        data.signing_of_contract_date = "";
      }

      // failed or non-compliant
      if([2, 3].includes(data.final_interview_status))
      {
        data.orientation_status = "";
      }
      
      // null, on process, failed
      if([null, '', 0, 2].includes(data.final_interview_status)) {
        data.selected_non_compliant_final_reason = "";
        data.specified_non_compliant_final_reason = "";
      }

    },
    selected_non_compliant_final_reason() {
      if(!this.selected_non_compliant_final_reason || this.selected_non_compliant_final_reason != 'Others (Specify)') 
      {
        this.specified_non_compliant_final_reason = "";
      }
    },
    // "editedItem.orientation_date"() {
    //   let data = this.editedItem;
    //   if(data.final_interview_status == 1 && ([null, 0, ''].includes(data.orientation_status)))
    //   {
    //     data.orientation_status = 0;
    //   }
    // },
    "editedItem.orientation_status"() {
      let data = this.editedItem;

      // null, on process, failed
      if([null, '', 0, 2].includes(data.orientation_status)) {
        data.selected_non_compliant_orientation_reason = "";
        data.specified_non_compliant_orientation_reason = "";
      }
    },
    selected_non_compliant_orientation_reason() {
      if(!this.selected_non_compliant_orientation_reason || this.selected_non_compliant_orientation_reason != 'Others (Specify)') 
      {
        this.specified_non_compliant_orientation_reason = "";
      }
    },
    editedValues() {
      let edited = this.editedItem;
      let original = this.originalItem;
      let editedItem = { title: edited.title, url: edited.url, description: edited.description };
      let originalItem = { title: original.title, url: original.url, description: original.description };

      if(this.editedIndex > -1)
      {
        this.btnText = "OK";
        if(JSON.stringify(editedItem) != JSON.stringify(originalItem))
        {
          this.btnText = "Update"; 
        }
      }
      
    },
    
  },
  mounted() {
    
    this.progressFormTitle = this.progress_items[this.step] + ' Status';
      
  }
}
</script>

