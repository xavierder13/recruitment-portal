<template>
  <v-card>
    <v-toolbar :color="color" dense>
      <v-row>
        <v-col class="white--text d-flex">
          <v-spacer></v-spacer>
          <v-toolbar-title>
            {{ progress }}
          </v-toolbar-title>
          <v-tooltip top v-if="progressIsEditable && userHasPermissionToUpdateStatus">
            <template v-slot:activator="{ on, attrs }">
              <v-icon 
                dark
                class="ml-2" 
                v-bind="attrs" v-on="on"
                @click="viewProgress()"
              >
                mdi-pencil
              </v-icon>
            </template>
            <span>Update Info</span>
            
          </v-tooltip>  
          <v-spacer></v-spacer>
        </v-col>
      </v-row>
    </v-toolbar>
    <v-card-text>
      <v-row class="mt-6">
        <v-col class="my-2 py-0">
          <v-text-field
            class="ma-0 pa-0" 
            v-model="applicant.date_applied"
            label="Date Applied"
            type="date"
            prepend-icon="mdi-calendar"
            readonly
          >
          </v-text-field>
        </v-col>
        <v-col class="my-2 py-0">
          <v-text-field
            class="ma-0 pa-0" 
            v-model="applicant.screening_date"
            label="Screening Date"
            type="date"
            prepend-icon="mdi-calendar"
            readonly
          >
          </v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col class="my-2 py-0">
          <v-text-field
            class="ma-0 pa-0"
            v-model="applicant.position_name"
            label="Job Position Applied"
            readonly
          >
          </v-text-field>
        </v-col>
      </v-row>
      <v-row class="mt-6">
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
          <v-text-field
            class="ma-0 pa-0"
            label="IQ Date"
            type="date"
            prepend-icon="mdi-calendar"
            v-model="applicant.iq_date"
            readonly
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col class="my-2 py-0">
          <v-autocomplete
            class="ma-0 pa-0"
            :items="statusItems"
            label="B.I & Basic Req Status"
            v-model="applicant.bi_status"
            readonly
          ></v-autocomplete>
        </v-col>
        <v-col class="my-2 py-0">
          <v-text-field
            class="ma-0 pa-0"
            label="BI Date"
            type="date"
            prepend-icon="mdi-calendar"
            v-model="applicant.bi_date"
            readonly
          ></v-text-field>
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
      <v-row v-if="applicant.final_interview_status == 3">
        <v-col class="my-2 py-0">
          <v-text-field
            class="ma-0 pa-0"
            label="Final Interview Non-Compliant Reason"
            v-model="applicant.final_interview_remarks"
            readonly
          ></v-text-field>
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
            label="Hiring Officer Position"
            v-model="applicant.hiring_officer_position"
            readonly
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col class="my-2 py-0">
          <v-text-field
            class="ma-0 pa-0"
            label="Hiring Officer Name"
            v-model="applicant.hiring_officer_name"
            readonly
          ></v-text-field>
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
      <v-row>
        <v-col class="my-2 py-0">
          <v-autocomplete
            class="ma-0 pa-0"
            :items="statusItems"
            item-value="value"
            item-text="text"
            label="Orientation Status"
            v-model="applicant.orientation_status"
            readonly
          ></v-autocomplete>
        </v-col>
      </v-row>
      <v-row v-if="applicant.orientation_status == 3">
        <v-col class="my-2 py-0">
          <v-text-field
            class="ma-0 pa-0"
            label="Orientation Non-Compliant Reason"
            v-model="applicant.orientation_remarks"
            readonly
          ></v-text-field>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>
<script>

export default {
  name: 'ApplicationProgressCard',
  props: [
    'applicant',
    'color',
    'progress',
    'progressIsEditable', 
    'userHasPermissionToUpdateStatus',
    'statusItems',
    'positions',
    'branches',
  ],
  data() {
    return {

    }
  },

  methods: {
    viewProgress() {
      this.$emit('viewProgress');
    }
  },
}
</script>
