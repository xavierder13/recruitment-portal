<template>
  <v-data-table
    :headers="tableHeaders"
    :items="job_applicants"
    :search="search"
    :loading="loading"
  >
    <template v-slot:top>
      <v-toolbar flat class="my-2 pt-2">
        <v-toolbar-title class="mt-4"> {{ table_title }} </v-toolbar-title>
        <v-divider vertical class="ma-2 ml-4" thickness="20px"></v-divider>
        <v-tooltip top>
          <template v-slot:activator="{ on, attrs }">
            <v-btn 
              small 
              class="mx-2 mt-4 white--text" 
              color="primary" 
              rounded 
              fab 
              @click="getData()"
              :disabled="loading"
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
              small
              class="mr-2 mt-4 white--text"
              color="#009688"
              fab
              dark
              @click="exportData()"
              :disabled="loading"
              v-bind="attrs" v-on="on"
            >
              <v-icon>mdi-file-excel</v-icon>
            </v-btn>
          </template>
          <span>Export Data</span>
        </v-tooltip> 
        <v-spacer></v-spacer>
        <v-text-field
          class="mr-2"
          v-model="search"
          append-icon="mdi-magnify"
          label="Search"
          single-line
          hide-details=""
        ></v-text-field>
        
      </v-toolbar>
      <v-toolbar flat class="ma-0 pa-0" extended extension-height="30px">
        <v-autocomplete
          class="mt-8"
          v-model="selectedTableHeaders"
          :items="headerItems"
          item-text="text"
          item-value="name"
          label="Table Columns"
          multiple
          hide-details=""
          item-disabled="disable"
          return-object
          chips
        >
          <template v-slot:selection="data">
            <v-chip
              v-bind="data.attrs"
              :input-value="data.selected"
              :close="selectedTableHeaders.length > 1 ? true : false"
              @click="data.select"
              @click:close="removeSelectedHeader(data.item)"
            >
              {{ data.item.text }}
            </v-chip>
          </template>
        </v-autocomplete>
      </v-toolbar>
    </template>
    <template v-slot:item.progress_status="{ item }">
      <v-chip
        small
        dark
        :color="applicationProgress(item).color"
      >  
        {{ applicationProgress(item).progress }}
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
        @click="view_applicant(item)"
      >
        mdi-eye
      </v-icon>
      <v-icon
        small
        color="red"
        v-if="hasPermission('jobapplicants-delete')"
        @click="deleteApplicant(item.id)"
      >
        mdi-delete
      </v-icon>
    </template>
  </v-data-table>
</template>
<script>
import { mapState, mapGetters } from "vuex";

export default {
  props: ['job_applicants', 'loading', 'table_title'],
  data() {
    return {
      headers: [
        { text: "Full name", value: "name" },
        { text: "Position", value: "position_name" },
        { text: "Branch Applied", value: "branch_name" },
        { text: "Date Submitted", value: "created_at" },
        { text: "Screening Date", value: "screening_date" },
        { text: "Init. Intrvw. Date", value: "initial_interview_date" },
        { text: "Position Pref.", value: "position_preference" },
        { text: "Branch Pref.", value: "branch_preference" },
        { text: "Init. Intrvw. Date", value: "initial_interview_date" },
        { text: "Exam Date", value: "iq_date" },
        { text: "BI Date", value: "bi_date" },
        { text: "Branch Complied", value: "branch_complied" },
        { text: "Employment Position", value: "employment_position" },
        { text: "Employment Branch", value: "employment_branch" },
        { text: "Officer Position", value: "hiring_officer_position" },
        { text: "Officer Name", value: "hiring_officer_name" },
        { text: "Orientation Date", value: "orientation_date" },
        { text: "Sign. Contract Date", value: "signing_of_contract_date" },
        { text: "Status", value: "progress_status" },
  
      ],
      selectedTableHeaders: [],
      defaultTableHeaders: [
        { text: "Full name", value: "name" },
        { text: "Position", value: "position_name" },
        { text: "Branch Applied", value: "branch_name" },
        { text: "Branch Complied", value: "branch_complied" },
        { text: "Employment Branch", value: "employment_branch" },
        { text: "Date Submitted", value: "created_at" },
        { text: "Status", value: "progress_status" },
      ],
      search: "",
    }
  },
  methods: {
    getData() {
      this.$emit('getData');
    },
    exportData() {
      this.$emit('exportData');
    },
    view_applicant(item) {
      this.$emit('view_applicant', item)
    },
    deleteApplicant(id)
    {
      this.$emit('deleteApplicant', id);
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
    removeSelectedHeader(item) {
      let index = this.selectedTableHeaders.findIndex(header => header.value === item.value);
      this.selectedTableHeaders.splice(index, 1);
    },
  },
  computed: {
    tableHeaders() {
      
      let headers = [];
      
      headers.push({ text: "#", value: "cnt_id" , sortable: false, width: "20px" })

      this.selectedTableHeaders.forEach(value => {
        this.headers.forEach(header => {
          if(header.value == value.value)
          {
            headers.push(header);
          }
        }); 
      });

      headers.push({ text: "Actions", value: "actions", sortable: false, width: "80px" })
 
      return headers;
    },
    headerItems() {
      
      let headers = [];
      let selected_items = this.selectedTableHeaders;
      
      this.headers.forEach(header => {
    
        let disable = false;
        
        // if selectedTableHeaders has 1 item then disable item -- must be atleast 1 column for data datable
        if(selected_items.length == 1 && selected_items[0].value == header.value)
        { 
          headers.push(Object.assign(header, { disable: disable }));
        }
        //disable all unselected items when selected items is equal to 9
        else if(selected_items.length == this.defaultTableHeaders.length)
        { 
          let index = selected_items.findIndex(item => item.value === header.value);  
          disable = index > -1 ? false : true;
        } 
        
        headers.push(Object.assign(header, { disable: disable }));
      });      
      
      return headers;
    },
    ...mapGetters("userRolesPermissions", ["hasRole", "hasPermission"]),
  },
  mounted() {
    
    this.defaultTableHeaders.forEach(value => {
      this.selectedTableHeaders.push(value)
    });
  }
}
</script>
