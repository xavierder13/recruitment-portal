<template>
  <v-card>
    <v-card-title style="background-color: #f44336; color: #ffffff;" class="mb-0">
      <span class="text-h5">Applicant Details</span>
      <v-spacer></v-spacer>
      <v-btn
        icon
        dark
        @click="closeDialog()"
      >
        <v-icon>mdi-close-circle</v-icon>
      </v-btn>
    </v-card-title>
    <v-card-text class="px-0 mb-0">
      <v-stepper v-model="stepper" class="mt-2">
        <div class="mobile-scroll" ref="stepperWrapper">
          <v-stepper-header>
            <template v-for="(stepper, i) in stepperItems">
              <v-stepper-step 
                @click="stepper = stepper.step" 
                :editable="stepperItems[0].isComplete" 
                :step="stepper.step"
                :ref="'step-' + i"
                color="primary"
              >
                {{ stepper.text }}
              </v-stepper-step>
              <v-divider v-if="(stepperItems.length - 1) > i" :key="i"></v-divider>
            </template>
          </v-stepper-header>
        </div>
        <v-stepper-items>
          <v-stepper-content step="1">  
            <v-row>
              <v-col class="px-9">
                <span class="font-italic font-weight-bold red--text">Note: All fields with asterisk(*) are required</span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" sm="12" md="8" lg="4" xl="4">
                <v-file-input
                  show-size
                  label="Please attach your resume here. *"
                  v-model="applicant.resumeFile"
                  hint=".docs, .docx, .pdf, .jpeg, .png, .jpg"
                  persistent-hint
                  accept=".pdf, .docs, .docx, .jpeg, .png, .jpg"
                  :error-messages="resumefileErrors + applicantError.file"
                  @change="$v.applicant.resumeFile.$touch() + (applicantError.file = []) + (validateFile()) + resumeOnFileChange()"
                  @blur="$v.applicant.resumeFile.$touch() + (applicantError.file = [])"
                ></v-file-input>
              </v-col>
            </v-row>
            <v-row v-if="applicant.resumeFile ? (applicant.resumeFile.name ? true : false) : false">
              <v-col cols="12" class="my-4 py-0" align='right'>
                <v-fade-transition mode="out-in">
                  <v-btn
                    class="mr-1"
                    color="primary"
                    @click="stepper = 2"
                  >
                    Next
                  </v-btn>
                </v-fade-transition>  
              </v-col>  
            </v-row>
          </v-stepper-content>
          <v-stepper-content step="2" class="px-0">
            <v-row>
              <v-col class="px-9 mb-6">
                <span class="font-italic font-weight-bold red--text">Note: All fields with asterisk(*) are required</span>
              </v-col>
            </v-row>
            <v-card class="mt-2 mx-4" height="100%">
              <v-card-title class="justify-center mb-0 pb-0">
                <strong>Personal Information</strong>  
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text>
                <v-row>
                  <v-col 
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Last name *"
                      persistent-hint
                      v-model="applicant.lastname"
                      :error-messages="lastnameErrors + applicantError.lastname"
                      @input="$v.applicant.lastname.$touch() + (applicantError.lastname = [])"
                      @blur="$v.applicant.lastname.$touch() + (applicantError.lastname = [])"
                    ></v-text-field>
                  </v-col>
                  <v-col 
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="First name *"
                      persistent-hint
                      v-model="applicant.firstname"
                      :error-messages="firstnameErrors + applicantError.firstname"
                      @input="$v.applicant.firstname.$touch() + (applicantError.firstname = [])"
                      @blur="$v.applicant.firstname.$touch() + (applicantError.firstname = [])"
                    ></v-text-field>
                  </v-col>
                  <v-col 
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Middle name"
                      persistent-hint
                      v-model="applicant.middlename"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col 
                    cols="12" 
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Present Address *"
                      hint="St./Brgy, Municipality, Province"
                      persistent-hint
                      v-model="applicant.address"
                      :error-messages="addressErrors + applicantError.address"
                      @input="$v.applicant.address.$touch()"
                      @blur="$v.applicant.address.$touch()"
                    ></v-text-field>
                  </v-col>
                  <v-col 
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Home Address *"
                      hint="St./Brgy, Municipality, Province"
                      persistent-hint
                      v-model="applicant.address2"
                      :error-messages="address2Errors + applicantError.address2"
                      @input="$v.applicant.address2.$touch() + (applicantError.address2 = [])"
                      @blur="$v.applicant.address2.$touch() + (applicantError.address2 = [])"
                    ></v-text-field>
                  </v-col>
                  <v-col 
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Place of Birth *"
                      hint="St./Brgy, Municipality, Province"
                      persistent-hint
                      v-model="applicant.birth_place"
                      :error-messages="birthPlaceErrors + applicantError.birth_place"
                      @input="$v.applicant.birth_place.$touch() + (applicantError.birth_place = [])"
                      @blur="$v.applicant.birth_place.$touch() + (applicantError.birth_place = [])"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Birthday *"
                      type="date"
                      prepend-icon="mdi-calendar"
                      v-model="applicant.birthdate"
                      :error-messages="birthdateErrors + applicantError.birthdate"
                      @input="birthdateInput()"
                      @blur="$v.applicant.birthdate.$touch() + (applicantError.birthdate = [])"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-autocomplete
                      v-model="applicant.gender"
                      :items="['Male', 'Female']"
                      label="Gender *"
                      :error-messages="genderErrors + applicantError.gender"
                      @input="$v.applicant.gender.$touch() + (applicantError.gender = [])"
                      @blur="$v.applicant.gender.$touch() + (applicantError.gender = [])"
                    ></v-autocomplete>
                  </v-col>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-autocomplete
                      v-model="applicant.civil_status"
                      :items="['Single', 'Married', 'Widow', 'Domestic Partnership']"
                      label="Civil Status *"
                      :error-messages="civilStatusErrors + applicantError.civil_status"
                      @input="$v.applicant.civil_status.$touch() + (applicantError.civil_status = [])"
                      @blur="$v.applicant.civil_status.$touch() + (applicantError.civil_status = [])"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Contact No. *"
                      v-model="applicant.contact_no"
                      :error-messages="contactNoErrors + applicantError.contact_no"
                      counter="11"
                      :rules="rules"
                      @input="$v.applicant.contact_no.$touch() + (applicantError.contact_no = [])"
                      @blur="$v.applicant.contact_no.$touch() + (applicantError.contact_no = [])"
                      @keypress="intNumValFilter()"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Email"
                      v-model="applicant.email"
                      :error-messages="emailErrors + applicantError.email"
                      @input="$v.applicant.email.$touch() + (applicantError.email = [])"
                      @blur="$v.applicant.email.$touch() + (applicantError.email = [])"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Citizenship *"
                      v-model="applicant.citizenship"
                      :error-messages="citizenshipErrors + applicantError.citizenship"
                      @input="$v.applicant.citizenship.$touch() + (applicantError.citizenship = [])"
                      @blur="$v.applicant.citizenship.$touch() + (applicantError.citizenship = [])"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Religion *"
                      v-model="applicant.religion"
                      :error-messages="religionErrors + applicantError.religion"
                      @input="$v.applicant.religion.$touch() + (applicantError.religion = [])"
                      @blur="$v.applicant.religion.$touch() + (applicantError.religion = [])"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="Height (cm) *"
                      v-model="applicant.height"
                      :error-messages="heightErrors + applicantError.height"
                      @input="$v.applicant.height.$touch() + (applicantError.height = [])"
                      @blur="$v.applicant.height.$touch() + (applicantError.height = [])"
                      @keypress="decNumValFilter()"
                    ></v-text-field>
                  </v-col>
                  <v-col class="mb-0 pb-0">
                    <v-text-field
                      label="Weight (kg) *"
                      v-model="applicant.weight"
                      :error-messages="weightErrors + applicantError.weight"
                      @input="$v.applicant.weight.$touch() + (applicantError.weight = [])"
                      @blur="$v.applicant.weight.$touch() + (applicantError.weight = [])"
                      @keypress="decNumValFilter()"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="SSS #"
                      v-model="applicant.sss_no"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="PHILHEALTH #"
                      v-model="applicant.philhealth_no"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="PAGIBIG #"
                      v-model="applicant.pagibig_no"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    class="mb-0 pb-0"
                  >
                    <v-text-field
                      label="TIN #"
                      v-model="applicant.tin_no"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
            <v-card class="mt-2 mx-4">
              <v-card-title class="justify-center mb-0 pb-0">
                <strong>Parents/Guardian/Spouse</strong>  
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text>
                <v-row>
                  <v-col>
                    <span class="text-h6">
                      <strong>Father</strong> 
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
                      v-model="father.name"
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
                      v-model="father.age"
                      @keypress="intNumValFilter()"
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
                      v-model="father.address"
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
                      v-model="father.contact"
                      counter="11"
                      :rules="rules"
                      @keypress="intNumValFilter()"
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
                      v-model="father.occupation"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-2 py-0">
                    <v-divider class="my-0"></v-divider>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col>
                    <span class="text-h6">
                      <strong>Mother</strong> 
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
                      v-model="mother.name"
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
                      v-model="mother.age"
                      @keypress="intNumValFilter()"
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
                      v-model="mother.address"
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
                      v-model="mother.contact"
                      counter="11"
                      :rules="rules"
                      @keypress="intNumValFilter()"
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
                      v-model="mother.occupation"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-2 py-0">
                    <v-divider class="my-0"></v-divider>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col>
                    <span class="text-h6">
                      <strong>Spouse</strong> 
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
                      v-model="spouse.name"
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
                      v-model="spouse.age"
                      @keypress="intNumValFilter()"
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
                      v-model="spouse.address"
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
                      v-model="spouse.contact"
                      counter="11"
                      :rules="rules"
                      @keypress="intNumValFilter()"
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
                      v-model="spouse.occupation"
                    ></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col class="my-2 py-0">
                    <v-divider class="my-0"></v-divider>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col>
                    <span class="text-h6">
                      <strong>Guardian</strong> 
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
                      v-model="guardian.name"
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
                      v-model="guardian.age"
                      @keypress="intNumValFilter()"
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
                      v-model="guardian.address"
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
                      v-model="guardian.contact"
                      counter="11"
                      :rules="rules"
                      @keypress="intNumValFilter()"
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
                      v-model="guardian.occupation"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
            <v-card class="mt-2 mx-4">
              <v-card-title class="justify-center mb-0 pb-0">
                <strong>Dependents</strong>  
              </v-card-title>
              <v-divider></v-divider>
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
                      @keypress="intNumValFilter()"
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
            <v-row>
              <v-col class="px-9 mb-6">
                <span class="font-italic font-weight-bold red--text">Note: All fields with asterisk(*) are required</span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" class="my-4 py-0" align="right">
                <v-btn class="mr-1" @click="stepper = 1">
                  Back
                </v-btn>
                <v-fade-transition mode="out-in">
                  <v-btn
                    class="mr-6"
                    color="primary"
                    @click="stepper = 3"
                  >
                    Next
                  </v-btn>
                </v-fade-transition>  
              </v-col>  
            </v-row>
          </v-stepper-content>
          <v-stepper-content step="3" class="px-0">
            <v-row>
              <v-col class="px-9 mb-6">
                <span class="font-italic font-weight-bold red--text">Note: All fields with asterisk(*) are required</span>
              </v-col>
            </v-row>
            <v-card class="mt-2 mx-4">
              <v-card-title class="justify-center mb-0 pb-0">
                <strong>Educational Background</strong>  
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text>
                <v-row>
                  <v-col 
                    class="mt-2 mb-0"
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                  >
                    <v-autocomplete
                      class="ma-0 pa-0"
                      v-model="applicant.educ_attain"
                      :items="educ_attains"
                      item-text="text"
                      item-value="value"
                      label="Educational Attainment *"
                      :error-messages="educAttainErrors + applicantError.educ_attain"
                      @input="$v.applicant.educ_attain.$touch() + (applicantError.educ_attain = [])"
                      @blur="$v.applicant.educ_attain.$touch() + (applicantError.educ_attain = [])"
                    ></v-autocomplete>
                  </v-col>
                  <v-col 
                    class="mb-0"
                    cols="12"
                    xs="12"
                    sm="4"
                    md="4"
                    lg="4"
                    v-if="applicant.educ_attain > 3"
                  >
                    <div class="d-flex mb-0">
                      <v-checkbox class="mt-2 mb-0 pa-0" v-model="k_12_checkbox">
                        <template v-slot:label>
                          <span class="mt-2">K-12 Highschool</span>
                        </template>
                      </v-checkbox>
                    </div>
                  </v-col>
                </v-row>
                <template v-if="applicant.educ_attain">
                  <template v-if="!k_12_checkbox && ![2, 3].includes(applicant.educ_attain)">
                    <v-row>
                      <v-col class="mt-0">
                        <span class="text-h6">
                          <strong>High School</strong> 
                        </span>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col 
                        cols="12"
                        xs="12"
                        sm="4"
                        md="4"
                        lg="4"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Name of School *"
                          v-model="highschool.school"
                          :error-messages="HSSchoolErrors"
                          @input="$v.highschool.school.$touch()"
                          @blur="$v.highschool.school.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      > 
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y Start *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="highschool.sy_start"
                          :error-messages="HSSYStartErrors"
                          :max="highschool.sy_end ? highschool.sy_end : null"
                          @input="$v.highschool.sy_start.$touch() + validateDate('highschool.sy_start')"
                          @blur="$v.highschool.sy_start.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y End *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="highschool.sy_end"
                          :error-messages="HSSYEndErrors"
                          :min="highschool.sy_start ? highschool.sy_start : null"
                          @input="$v.highschool.sy_end.$touch() + validateDate('highschool.sy_end')"
                          @blur="$v.highschool.sy_end.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="4"
                        md="4"
                        lg="4"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Honors Received"
                          v-model="highschool.honors"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-divider v-if="applicant.educ_attain > 1"></v-divider>
                  </template>
                  <template v-if="(k_12_checkbox && applicant.educ_attain > 3) || (applicant.educ_attain > 1 && applicant.educ_attain < 4)">
                  <!-- <template v-if="applicant.educ_attain >= 1"> -->
                    <v-row>
                      <v-col>
                        <span class="text-h6">
                          <strong>Junior High School</strong> 
                        </span>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col 
                        cols="12"
                        xs="12"
                        sm="4"
                        md="4"
                        lg="4"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Name of School *"
                          v-model="jr_highschool.school"
                          :error-messages="JRHSchoolErrors"
                          @input="$v.jr_highschool.school.$touch()"
                          @blur="$v.jr_highschool.school.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      > 
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y Start *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="jr_highschool.sy_start"
                          :error-messages="JRSYStartErrors"
                          :max="jr_highschool.sy_end ? jr_highschool.sy_end : null"
                          @input="$v.jr_highschool.sy_start.$touch() + validateDate('jr_highschool.sy_start')"
                          @blur="$v.jr_highschool.sy_start.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y End *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="jr_highschool.sy_end"
                          :error-messages="JRSYEndErrors"
                          :min="jr_highschool.sy_start ? jr_highschool.sy_start : null"
                          @input="$v.jr_highschool.sy_end.$touch() + validateDate('jr_highschool.sy_end')"
                          @blur="$v.jr_highschool.sy_end.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="4"
                        md="4"
                        lg="4"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Honors Received"
                          v-model="jr_highschool.honors"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-divider v-if="(k_12_checkbox && applicant.educ_attain > 2) || applicant.educ_attain == 3"></v-divider>
                    <!-- <v-divider v-if="applicant.educ_attain > 1"></v-divider> -->
                  </template>
                  <template v-if="(k_12_checkbox && applicant.educ_attain > 2) || applicant.educ_attain == 3">
                  <!-- <template v-if="applicant.educ_attain >= 2"> -->
                    <v-row>
                      <v-col>
                        <span class="text-h6">
                          <strong>Senior High School</strong> 
                        </span>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col 
                        cols="12"
                        xs="12"
                        sm="4"
                        md="4"
                        lg="4"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Name of School *"
                          v-model="sr_highschool.school"
                          :error-messages="SRHSchoolErrors"
                          @input="$v.sr_highschool.school.$touch()"
                          @blur="$v.sr_highschool.school.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col 
                        cols="12"
                        xs="12"
                        sm="4"
                        md="4"
                        lg="4"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Strand *"
                          v-model="sr_highschool.strand"
                          :error-messages="SRHSStrandErrors"
                          @input="$v.sr_highschool.strand.$touch()"
                          @blur="$v.sr_highschool.strand.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      > 
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y Start *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="sr_highschool.sy_start"
                          :error-messages="SRSYStartErrors"
                          :max="sr_highschool.sy_end ? sr_highschool.sy_end : null"
                          @input="$v.sr_highschool.sy_start.$touch() + validateDate('sr_highschool.sy_start')"
                          @blur="$v.sr_highschool.sy_start.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y End *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="sr_highschool.sy_end"
                          :error-messages="SSYEndErrors"
                          :min="sr_highschool.sy_start ? sr_highschool.sy_start : null"
                          @input="$v.sr_highschool.sy_end.$touch() + validateDate('sr_highschool.sy_end')"
                          @blur="$v.sr_highschool.sy_end.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="4"
                        md="4"
                        lg="4"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Honors Received"
                          v-model="sr_highschool.honors"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <!-- <v-divider v-if="k_12_checkbox"></v-divider> -->
                    <v-divider v-if="applicant.educ_attain > 3"></v-divider>
                  </template>
                  <template v-if="[4, 5].includes(applicant.educ_attain)">
                    <v-row>
                      <v-col>
                        <span class="text-h6">
                          <strong>College</strong> 
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
                        lg="3"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Name of School *"
                          v-model="college.school"
                          :error-messages="collegeSchoolErrors"
                          @input="$v.college.school.$touch()"
                          @blur="$v.college.school.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col 
                        class="my-2 py-0"
                        cols="12"
                        xs="12"
                        sm="6"
                        md="6"
                        lg="3"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Course/Specialization *"
                          v-model="college.course"
                          :error-messages="collegeCourseErrors"
                          @input="$v.college.course.$touch()"
                          @blur="$v.college.course.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      > 
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y Start *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="college.sy_start"
                          :error-messages="collegeSYStartErrors"
                          :max="college.sy_end ? college.sy_end : null"
                          @input="$v.college.sy_start.$touch() + validateDate('college.sy_start')"
                          @blur="$v.college.sy_start.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y End *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="college.sy_end"
                          :error-messages="collegeSYEndErrors"
                          :min="college.sy_start ? college.sy_start : null"
                          @input="$v.college.sy_end.$touch() + validateDate('college.sy_end')"
                          @blur="$v.college.sy_end.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        class="my-2 py-0"
                        cols="12"
                        xs="12"
                        sm="6"
                        md="6"
                        lg="3"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Honors Received"
                          v-model="college.honors"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <!-- <v-divider v-if="applicant.educ_attain === 4"></v-divider> -->
                  </template>
                  <template v-if="applicant.educ_attain == 6">
                    <v-row>
                      <v-col>
                        <span class="text-h6">
                          <strong>Vocational School</strong> 
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
                        lg="3"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Name of School *"
                          v-model="vocational_school.school"
                          :error-messages="vocSchoolErrors"
                          @input="$v.vocational_school.school.$touch()"
                          @blur="$v.vocational_school.school.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col 
                        class="my-2 py-0"
                        cols="12"
                        xs="12"
                        sm="6"
                        md="6"
                        lg="3"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Course/Specialization *"
                          v-model="vocational_school.course"
                          :error-messages="vocCourseErrors"
                          @input="$v.vocational_school.course.$touch()"
                          @blur="$v.vocational_school.course.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      > 
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y Start *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="vocational_school.sy_start"
                          :error-messages="vocSYStartErrors"
                          :max="vocational_school.sy_end ? vocational_school.sy_end : null"
                          @input="$v.vocational_school.sy_start.$touch() + validateDate('vocational_school.sy_start')"
                          @blur="$v.vocational_school.sy_start.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                        xs="12"
                        sm="2"
                        md="2"
                        lg="2"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="S.Y End *"
                          hint="MM/DD/YYYY"
                          persistent-hint
                          type="date"
                          prepend-icon="mdi-calendar"
                          v-model="vocational_school.sy_end"
                          :error-messages="vocSYEndErrors"
                          :min="vocational_school.sy_start ? vocational_school.sy_start : null"
                          @input="$v.vocational_school.sy_end.$touch() + validateDate('vocational_school.sy_end')"
                          @blur="$v.vocational_school.sy_end.$touch()"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        class="my-2 py-0"
                        cols="12"
                        xs="12"
                        sm="6"
                        md="6"
                        lg="3"
                      >
                        <v-text-field
                          class="ma-0 pa-0"
                          label="Honors Received"
                          v-model="vocational_school.honors"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </template>
                </template>
              </v-card-text>
            </v-card>
            <v-row>
              <v-col class="px-9 mb-6">
                <span class="font-italic font-weight-bold red--text">Note: All fields with asterisk(*) are required</span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" class="my-4 py-0" align="right">
                <v-btn class="mr-1" @click="stepper = 2">
                  Back
                </v-btn>
                <v-fade-transition mode="out-in">
                  <v-btn
                    class="mr-6"
                    color="primary"
                    @click="stepper = 4"
                  >
                    Next
                  </v-btn>
                </v-fade-transition>
              </v-col>  
            </v-row>
          </v-stepper-content>
          <v-stepper-content step="4" class="px-0">
            <v-card class="mt-2 mx-4">
              <v-card-title class="justify-center mb-0 pb-0">
                <strong>Work Experience</strong>  
              </v-card-title>
              <v-divider></v-divider>
              <v-card-text>
                <template v-for="(item, i) in work_experiences">
                  <v-row v-if="work_experiences.length > 1">
                    <v-col>
                      <span class="text-h6">
                        <strong>Work Experience {{ work_experiences.length > 1 ? i + 1 : ''}}</strong> 
                      </span>
                      <v-btn icon class="mb-2" color="error" @click="removeExperience(i)" v-if="work_experiences.length > 1"> 
                        <v-icon>mdi-minus-circle</v-icon> 
                      </v-btn>
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
                        v-model="item.company"
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
                      ></v-text-field>
                    </v-col>
                    <v-col
                      cols="12"
                      xs="12"
                      sm="2"
                      md="2"
                      lg="2"
                    > 
                      <v-text-field
                        class="ma-0 pa-0"
                        label="Service Start"
                        hint="MM/DD/YYYY"
                        persistent-hint
                        type="date"
                        prepend-icon="mdi-calendar"
                        v-model="item.service_start"
                        :error-messages="work_experiences[i].service_start_error_msg"
                        :max="item.service_end ? item.service_end : null"
                      ></v-text-field>
                    </v-col>
                    <v-col
                      cols="12"
                      xs="12"
                      sm="2"
                      md="2"
                      lg="2"
                    >
                      <v-text-field
                        class="ma-0 pa-0"
                        label="Service End"
                        hint="MM/DD/YYYY"
                        persistent-hint
                        type="date"
                        prepend-icon="mdi-calendar"
                        v-model="item.service_end"
                        :error-messages="work_experiences[i].service_end_error_msg"
                        :min="item.service_start ? item.service_start : null"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col class="mb-2 py-0">
                      <v-btn color="primary" x-small @click="addExperience()" v-if="i + 1 === work_experiences.length"> 
                        <v-icon x-small>mdi-plus</v-icon> 
                        Add More
                      </v-btn>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col class="my-2 py-0">
                      <v-divider v-if="work_experiences.length > 1 && work_experiences.length - 1 != i" class="my-0"></v-divider>
                    </v-col>
                  </v-row>
                </template>
              </v-card-text>
            </v-card>
            <v-row class="mt-2">
              <v-col cols="12" class="my-4 py-0" align="right">
                <v-btn class="mr-1" @click="stepper = 3">
                  Back
                </v-btn>
                <v-fade-transition mode="out-in">
                  <v-btn
                    class="mr-6"
                    color="primary"
                    @click="stepper = 5"
                  >
                    Next
                  </v-btn>
                </v-fade-transition>  
              </v-col>  
            </v-row>
          </v-stepper-content>
          <v-stepper-content step="5" class="px-0">
            <v-row>
              <v-col class="px-9 mb-6">
                <span class="font-italic font-weight-bold red--text">Note: All fields with asterisk(*) are required</span>
              </v-col>
            </v-row>
            <v-card class="mt-2 mx-4">
              <v-card-title class="justify-center mb-0 pb-0">
                <strong>References</strong>  
              </v-card-title>
              <v-divider></v-divider>
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
                        label="Name *"
                        v-model="item.name"
                        :error-messages="referencesError[i].name"
                        @input="validateReference(i, 'name')"
                        @blur="validateReference(i, 'name')"
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
                        label="Address *"
                        v-model="item.address"
                        :error-messages="referencesError[i].address"
                        @input="validateReference(i, 'address')"
                        @blur="validateReference(i, 'address')"
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
                        label="Contact *"
                        v-model="item.contact"
                        counter="11"
                        :rules="rules"
                        :error-messages="referencesError[i].contact"
                        @input="validateReference(i, 'contact')"
                        @blur="validateReference(i, 'contact') "
                        @keypress="intNumValFilter()"
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
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col class="my-2 py-0">
                      <v-divider v-if="references.length > 1 && references.length - 1 != i" class="my-0"></v-divider>
                    </v-col>
                  </v-row>
                </template>
              </v-card-text>
            </v-card>
            <v-row>
              <v-col class="px-9 mb-6">
                <span class="font-italic font-weight-bold red--text">Note: All fields with asterisk(*) are required</span>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" class="my-4 py-0" align="right">
                <v-btn class="mr-1" @click="stepper = 4">
                  Back
                </v-btn>
                <v-fade-transition mode="out-in">
                  <v-btn
                    class="mr-6"
                    color="primary"
                    @click="stepper = 6"
                  >
                    Next
                  </v-btn>
                </v-fade-transition>  
              </v-col>  
            </v-row>
          </v-stepper-content>
          <v-stepper-content step="6">
            <v-row>
              <v-col cols="12" sm="12" md="8" lg="4" xl="4"  class="my-4 py-0">
                <v-file-input
                  show-size
                  label="Copy of Grades"
                  v-model="applicant.copy_of_grades"
                  hint=".docs, .docx, .pdf, .jpeg, .png, .jpg"
                  persistent-hint
                  accept=".pdf, .docs, .docx, .jpeg, .png, .jpg"
                  :error-messages="copyOfGradesErrors"
                  multiple
                  @change="$v.applicant.copy_of_grades.$touch() + (applicantError.copy_of_grades = []) + (validateFile())"
                  @blur="$v.applicant.copy_of_grades.$touch() + (applicantError.file = [])"
                ></v-file-input>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" class="my-4 py-0" align="right">
                <v-btn class="mr-1" @click="stepper = 5">
                  Back
                </v-btn>
                <v-fade-transition mode="out-in">
                  <v-btn
                    class="mr-6"
                    color="primary"
                    @click="stepper = 7"
                  >
                    Next
                  </v-btn>
                </v-fade-transition>  
              </v-col>  
            </v-row>
          </v-stepper-content>
          <v-stepper-content step="7">
            <v-row>
              <v-col
                cols="12"
                xs="12"
                sm="4"
                md="4"
                lg="4"
                class="mb-0 pb-0 pl-8"
              >
                <v-autocomplete
                  v-model="applicant.how_learn"
                  :items="how_learn"
                  label="How did you learn about job vacancy? *"
                  :error-messages="howLearnErrors + applicantError.how_learn"
                  @input="$v.applicant.how_learn.$touch() + (applicantError.how_learn = [])"
                  @blur="$v.applicant.how_learn.$touch() + (applicantError.how_learn = [])"
                  @change="handleChange"
                ></v-autocomplete>
              </v-col>
              <v-col 
                cols="12"
                xs="12"
                sm="4"
                md="4"
                lg="4"
                class="mb-0 pb-0 pl-8"
                v-if="applicant.how_learn == 'Others'"
              >
                <v-text-field
                  label="(Please specify here.) *"
                  v-model="applicant.how_learn_2"
                  :error-messages="howLearn2Errors"
                  @input="$v.applicant.how_learn_2.$touch()"
                  @blur="$v.applicant.how_learn_2.$touch()"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" class="my-4 py-0 ml-2">
                <v-checkbox 
                  v-model="checkboxConfirm" 
                  :error-messages="checkboxConfirmErrors"
                  @click="$v.checkboxConfirm.$touch()"
                >
                  <template v-slot:label>
                    <span class="mt-2">I hereby certify that the encoded &#38; attached information is true and correct.</span>
                  </template>
                </v-checkbox>
              </v-col>
            </v-row>  
            <v-row>
              <v-col cols="12" class="my-4 py-0" align="right">
                <v-btn @click="(stepper = 6)">
                  Back
                </v-btn>
              </v-col>
            </v-row>
          </v-stepper-content>
        </v-stepper-items>
      </v-stepper>
    </v-card-text>
    <!-- <v-divider class="mb-3 mt-0"></v-divider> -->
    <v-card-actions class="pa-0">
      <v-spacer></v-spacer>
      <v-fade-transition mode="out-in">
        <v-btn
          color="primary"
          class="submit_btn mb-3"
          :loading="loader_dialog"
          :disabled="loader_dialog"
          @click="submit_application()"
        >
          Submit Form
        </v-btn>
      </v-fade-transition>  
      <v-btn
        class="mb-3 mr-6"
        @click="closeDialog()"
      >
        Close
      </v-btn>
    </v-card-actions>
  </v-card>
</template>
<style scoped>
  .full-height-stepper-content {
    height: calc(80vh - 120px); /* Adjust 270px to suits your needs */
    overflow-y: auto;
    overflow-x: hidden;
  }

  .full-height-card {
    height: calc(85vh - 130px); /* Adjust 270px to suits your needs */
    overflow-y: auto;
    overflow-x: hidden;
  }

  .mobile-scroll {
    overflow-x: auto;
    white-space: nowrap;
  }
  
  .mobile-scroll .v-stepper__header {
    min-width: 600px;
  }
</style>
<script>
import axios from "axios";
import { validationMixin } from "vuelidate";
import { required, requiredIf, minLength, email } from "vuelidate/lib/validators";
import AOS from 'aos';
import 'aos/dist/aos.css';

export default {

  mixins: [validationMixin],
  props: ['job_vac_id', 'branch_id'],
  validations: {
    applicant:{
      lastname: { required },
      firstname: { required },
      // middlename: { required },
      address: { required },
      address2: { required },
      birthdate: { required },
      birth_place: { required },
      gender: { required },
      civil_status: { required },
      contact_no: { required, minLength: minLength(11) },
      email: { email },
      educ_attain: { required },
      // course: { required },
      // school_grad: { required },
      citizenship: { required },
      religion: { required },
      height: { required },
      weight: { required },
      how_learn: { required },
      how_learn_2: {
        required: requiredIf(function () {
          return this.howLearn2IsRequired;
        }),
      },
      resumeFile: { required },
      copy_of_grades: { required }
    },
    highschool: {
      school: { required },
      major: { required },
      sy_start: { required },
      sy_end: { required },
      honors: { required },
    },
    jr_highschool: {
      school: { required },
      sy_start: { required },
      sy_end: { required },
      honors: { required },
    },
    sr_highschool: {
      school: { required },
      strand: { required },
      sy_start: { required },
      sy_end: { required },
      honors: { required },
    },
    college: {
      school: { required },
      course: { required },
      major: { required },
      sy_start: { required },
      sy_end: { required },
      honors: { required },
    },
    graduate_school: {
      school: { required },
      course: { required },
      major: { required },
      sy_start: { required },
      sy_end: { required },
      honors: { required },
    },
    vocational_school: {
      school: { required },
      course: { required },
      major: { required },
      sy_start: { required },
      sy_end: { required },
      honors: { required },
    },
    checkboxConfirm: { required },
    
  },

  data() {
    return {

      overlay: false,
      loading: false,

      search_textfield: "",
      saving_loader_text: false,
      loader_dialog: false,
      submit_dialog: false,
      stepper: 1,
      checkbox: false,

      model: 0,
      images: [
        {
          id: '0', src: 'wysiwyg/background_pic/careers1.png', title:'Join Us'
        },
        {
          id: '1', src: 'wysiwyg/background_pic/careers2.png' , title: 'Join Us'
        }
      ],

      file: "",
    
      highschool: {
        school: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      },
      jr_highschool: {
        school: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      },
      sr_highschool: {
        school: "",
        strand: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      },
      college: {
        school: "",
        course: "",
        major: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      },
      graduate_school: {
        school: "",
        course: "",
        major: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      },
      vocational_school: {
        school: "",
        course: "",
        major: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      },

      k_12_checkbox: false,
      educ_attains: [
        { text: 'Highschool Graduate', value: 1 }, 
        { text: 'Junior Highschool Graduate', value: 2 },
        { text: 'Senior Highschool Graduate', value: 3 },
        { text: 'College Undergraduate', value: 4 },
        { text: 'College Graduate', value: 5 },
        // { text: 'Graduate School', value: 6 },
        { text: 'Vocational School', value: 6 },
      ],
      // educ_attains: [
      //   { text: 'Junior Highschool Graduate', value: 1 },
      //   { text: 'Senior Highschool Graduate', value: 2 },
      //   { text: 'College Undergraduate', value: 3 },
      //   { text: 'College Graduate', value: 4 },
      //   { text: 'Vocational/TESTDA School', value: 5 },
      // ],
      work_experiences: [
        { 
          company: "", 
          position: "", 
          salary: "", 
          service_start: "", 
          service_start_error: false, 
          service_start_error_msg: "",
          service_end: "", 
          service_end_error: false,
          service_end_error_msg: "",
          job_description: "",
        },
      ],
      references: [
        { name: "", address: "", contact: "", company: "", position: "" },
        { name: "", address: "", contact: "", company: "", position: "" },
        { name: "", address: "", contact: "", company: "", position: "" },
      ],

      referencesError: [
        { name: "", address: "", contact: "" },
        { name: "", address: "", contact: "" },
        { name: "", address: "", contact: "" },
      ],

      father: { name: "", age: "", address: "", contact: "", occupation: "", },
      mother: { name: "", age: "", address: "", contact: "", occupation: "", },
      spouse: { name: "", age: "", address: "", contact: "", occupation: "", },
      guardian: { name: "", age: "", address: "", contact: "", occupation: "", },
        
      dependents: [
        { name: "", relationship: "", age: "", address: "", occupation: "" },
        { name: "", relationship: "", age: "", address: "", occupation: "" },
        { name: "", relationship: "", age: "", address: "", occupation: "" },
      ],
    
      applicant: {
        lastname: "",
        firstname: "",
        middlename: "",
        address: "",
        address2: "",
        birth_place: "",
        birthdate: "",
        age: "",
        gender: "",
        civil_status: "",
        contact_no: "",
        email: "",
        educ_attain: "",
        // course: "",
        // school_grad: "",
        citizenship: "",
        religion: "",
        height: "",
        weight: "",
        how_learn: "",
        resumeFile: [],
        copy_of_grades: []
      },
      bdate_value: "",
      

      rules: [v => v.length <= 11 || 'Phone number is max 11 digits only.'],

      applicantError: {
        lastname: [],
        firstname: [],
        middlename: [],
        address: [],
        address2: [],
        birthdate: [],
        birth_place: [],
        gender: [],
        civil_status: [],
        contact_no: [],
        email: [],
        citizenship: [],
        religion: [],
        height: [],
        weight: [],
        educ_attain: [],
        // course: [],
        // school_grad: [],
        how_learn: [],
        file: [],
        copy_of_grades: [],
      },
      
      referenceError: {

      },

      branch_job_vacancies: [],
      get_job_vacancy_lists: [],

      jobdesc_card: false,
      overview_card: false,
      jobtop_card: false,

      job_title: "",
      description: "",
      branch: "",
      qualifications: "",
      educ_attain: "",

      continue_1: false,
      continue_2: false,

      how_learn_2: "",
      hide_howlearn_2: false,

      /* REVISIONS V2 */
      all_branches: [],
      paginated_branch: [],
      page_length: 0,

      pagination: {
        page: 1,
      },

      // for v-chip selection active color
      selection: 0,

      how_learn: [
        'Addessa Website',
        'Addessa FB Page',
        'PESO FB Page',
        'Now hiring FB Group Page',
        'Local Government FB Page',
        'INDEED',
        'Print ADS(Posters, Leaflets, Tarpaulin)',
        'Job Fair',
        'PESO/DOLE',
        'School/University',
        'Employee Referral',
        'Customer Referral',
        'Others'
      ],

      edutAttainHasError: false,
      referencesHasError: false,
      hs_sy_start_menu: false,
      hs_sy_end_menu: false,
      jr_sy_start_menu: false,
      jr_sy_end_menu: false,
      sr_sy_start_menu: false,
      sr_sy_end_menu: false,
      college_sy_start_menu: false,
      college_sy_end_menu: false,
      vocational_sy_start_menu: false,
      vocational_sy_end_menu: false,
      work_start_menu: false,
      work_end_menu: false,
      dateErrors: {

        applicant: {
          birthdate: false,
        },
        highschool: {
          sy_start: false,
          sy_end: false,
        },
        jr_highschool: {
          sy_start: false,
          sy_end: false,
        },
        sr_highschool: {
          sy_start: false,
          sy_end: false,
        },
        college: {
          sy_start: false,
          sy_end: false,
        },
        vocational_school: {
          sy_start: false,
          sy_end: false,
        },
        
      },

      dateRangeErrors: {
        highschool: false,
        jr_highschool: false,
        sr_highschool: false,
        college: false,
        vocational_school: false,
      },

      fileInvalid: false,
      checkboxConfirm: "",
    }
  },

  methods: {

    reset(){
      this.applicant.lastname = "";
      this.applicant.firstname = "";
      this.applicant.middlename = "";
      this.applicant.address = "";
      this.applicant.address2 = "";
      this.applicant.birth_place = "";
      this.applicant.birthdate = "";
      this.applicant.age = "";
      this.applicant.gender = "";
      this.applicant.civil_status = "";
      this.applicant.contact_no = "";
      this.applicant.email = "";
      this.applicant.educ_attain = "";
      this.applicant.course = "";
      this.applicant.school_grad = "";
      this.applicant.citizenship = "";
      this.applicant.religion = "";
      this.applicant.weight = "";
      this.applicant.height = "";
      this.applicant.sss_no = "";
      this.applicant.philhealth_no = "";
      this.applicant.pagibig_no = "";
      this.applicant.tin_no = "";
      this.applicant.how_learn = "";
      this.applicant.how_learn_2 = "";
      this.applicant.resumeFile = [];
      this.applicant.copy_of_grades = [];
      this.dialog_submit_now = false;

      this.$v.$reset();

      this.hide_howlearn_2 = false;
      this.checkbox = false;

      this.continue_1 = false;
      this.continue_2 = false;

      //revert selection to index 0
      this.selection = 0;

      this.stepper = 1;
      this.loader_dialog = false;
      this.educAttainHasError = false;
      this.referencesHasError = false;

      this.resetEduc();

      this.k_12_checkbox = false,
    
      this.work_experiences = [
        { 
          company: "", 
          position: "", 
          salary: "", 
          service_start: "", 
          service_start_error: false, 
          service_start_error_msg: "",
          service_end: "", 
          service_end_error: false,
          service_end_error_msg: "",
          job_description: "",
          
        },
      ],

      this.references = [
        { name: "", address: "", contact: "", company: "", position: "" },
        { name: "", address: "", contact: "", company: "", position: "" },
        { name: "", address: "", contact: "", company: "", position: "" },
      ];

      this.referencesError = [
        { name: "", address: "", contact: "" },
        { name: "", address: "", contact: "" },
        { name: "", address: "", contact: "" },
      ];

      this.father = { name: "", age: "", address: "", contact: "", occupation: "", };
      this.mother = { name: "", age: "", address: "", contact: "", occupation: "", };
      this.spouse = { name: "", age: "", address: "", contact: "", occupation: "", };
      this.guardian = { name: "", age: "", address: "", contact: "", occupation: "", };
        
      this.dependents = [
        { name: "", relationship: "", age: "", address: "", occupation: "" },
        { name: "", relationship: "", age: "", address: "", occupation: "" },
        { name: "", relationship: "", age: "", address: "", occupation: "" },
      ];
      
    },

    resetEduc() {
      this.highschool = {
        school: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.jr_highschool = {
        school: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.sr_highschool = {
        school: "",
        strand: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.college = {
        school: "",
        course: "",
        major: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.graduate_school = {
        school: "",
        course: "",
        major: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.vocational_school = {
        school: "",
        course: "",
        major: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };

      this.dateErrors = {
        applicant: {
          birthdate: false,
        },
        highschool: {
          sy_start: false,
          sy_end: false,
        },
        jr_highschool: {
          sy_start: false,
          sy_end: false,
        },
        sr_highschool: {
          sy_start: false,
          sy_end: false,
        },
        college: {
          sy_start: false,
          sy_end: false,
        },
        vocational_school: {
          sy_start: false,
          sy_end: false,
        },

      };
      this.dateRangeErrors ={
        highschool: false,
        jr_highschool: false,
        sr_highschool: false,
        college: false,
        vocational_school: false,
      };
    },

    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },

    getBranches(){
      axios.get("/api/public_api/branches").then(
        (response) => {
          this.all_branches = response.data.all_branches;

          this.paginated_branch = response.data.paginated_branch.data;
          this.page_length = response.data.paginated_branch.total;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    submit_application(){

      this.$v.$touch();
      console.log('$v.$error', this.$v.$error);
      console.log('$v', this.$v);
      console.log('resumefileErrors', this.resumefileErrors.length); 
      console.log('personalIsComplete', this.personalIsComplete);
      console.log('educationIsComplete', this.educationIsComplete);
      console.log('workIsComplete', this.workIsComplete);
      console.log('referenceIsComplete', this.referenceIsComplete);
      console.log('attachmentIsComplete', this.attachmentIsComplete);
      console.log('copyOfGradesErrors', this.copyOfGradesErrors.length);
      console.log('this.$v.checkboxConfirm', this.$v.checkboxConfirm);
      console.log('this.$v.applicant.how_learn.$error', this.$v.applicant.how_learn.$error);
      console.log('this.$v.applicant.how_learn_2.$error', this.$v.applicant.how_learn_2.$error);    
      
      if( 
          this.personalIsComplete && 
          this.educationIsComplete && 
          this.workIsComplete && 
          this.referenceIsComplete && 
          this.attachmentIsComplete &&
          this.checkboxConfirm &&
          !this.resumefileErrors.length && 
          !this.copyOfGradesErrors.length
        )
      {
        this.loader_dialog = true;
        this.saving_loader_text = true;
        
        this.applicantError = {
          lastname: [],
          firstname: [],
          middlename: [],
          address: [],
          address2: [],
          birthdate: [],
          birth_place: [],
          gender: [],
          civil_status: [],
          contact_no: [],
          email: [],
          educ_attain: [],
          citizenship: [],
          religion: [],
          height: [],
          weight: [],
          // course: [],
          // school_grad: [],
          how_learn: [],
          how_learn_2: [],
          file: [],
          copy_of_grades: [],
        };

        const how_learn_selected = this.applicant.how_learn;

        let educ_attain = "";

        this.educ_attains.forEach(value => {
          if (value.value == this.applicant.educ_attain) {
            educ_attain = value.text;
          }
        });

        let formData = new FormData();
        formData.append('jobvacancy_id', this.job_vac_id);
        formData.append('branch_id', this.branch_id);
        formData.append('lastname', this.applicant.lastname);
        formData.append('firstname', this.applicant.firstname);
        formData.append('middlename', this.applicant.middlename);
        formData.append('address', this.applicant.address);
        formData.append('address2', this.applicant.address2);
        formData.append('birth_place', this.applicant.birth_place);
        formData.append('birthdate', this.applicant.birthdate);
        formData.append('age', this.applicant.age);
        formData.append('gender', this.applicant.gender);
        formData.append('civil_status', this.applicant.civil_status);
        formData.append('contact_no', this.applicant.contact_no);
        formData.append('email', this.applicant.email);
        formData.append('educ_attain', educ_attain);
        formData.append('religion', this.applicant.religion);
        formData.append('citizenship', this.applicant.citizenship);
        formData.append('height', this.applicant.height);
        formData.append('weight', this.applicant.weight);
        formData.append('k_12_highschool', this.k_12_checkbox);
        // formData.append('course', this.applicant.course);
        // formData.append('school_grad', this.applicant.school_grad);
        
        if(how_learn_selected === 'Others'){
          formData.append('how_learn', this.applicant.how_learn_2);
        }else{
          formData.append('how_learn', this.applicant.how_learn);
        }

        let hs_fieldnames = Object.keys(this.highschool)
        hs_fieldnames.forEach(value => {
          formData.append('highschool['+value+']', this.highschool[value]);
        });

        let jrhs_fieldnames = Object.keys(this.jr_highschool)
        jrhs_fieldnames.forEach(value => {
          formData.append('jr_highschool['+value+']', this.jr_highschool[value]);
        });

        let srhs_fieldnames = Object.keys(this.sr_highschool)
        srhs_fieldnames.forEach(value => {
          formData.append('sr_highschool['+value+']', this.sr_highschool[value]);
        });

        let college_fieldnames = Object.keys(this.college)
        college_fieldnames.forEach(value => {
          formData.append('college['+value+']', this.college[value]);
        });

        let gs_fieldnames = Object.keys(this.graduate_school)
        gs_fieldnames.forEach(value => {
          formData.append('graduate_school['+value+']', this.graduate_school[value]);
        });

        let voc_fieldnames = Object.keys(this.vocational_school)
        voc_fieldnames.forEach(value => {
          formData.append('vocational_school['+value+']', this.vocational_school[value]);
        });
    
        this.work_experiences.forEach((value, i) => {
          let field_names = Object.keys(value)
          
          field_names.forEach(field => {
            formData.append('experiences['+i+']['+field+']', value[field]);
          });
          
        });

        this.references.forEach((value, i) => {
          let field_names = Object.keys(value)

          field_names.forEach(field => {
            formData.append('references['+i+']['+field+']', value[field]);
          });
          
        });
        
        let father_fieldnames = Object.keys(this.father)
        father_fieldnames.forEach(value => {
          formData.append('father['+value+']', this.father[value]);
        });

        let mother_fieldnames = Object.keys(this.mother)
        mother_fieldnames.forEach(value => {
          formData.append('mother['+value+']', this.mother[value]);
        });

        let spouse_fieldnames = Object.keys(this.spouse)
        spouse_fieldnames.forEach(value => {
          formData.append('spouse['+value+']', this.spouse[value]);
        });

        let guardian_fieldnames = Object.keys(this.guardian)
        guardian_fieldnames.forEach(value => {
          formData.append('guardian['+value+']', this.guardian[value]);
        });

        this.dependents.forEach((value, i) => {
          let field_names = Object.keys(value)

          field_names.forEach(field => {
            formData.append('dependents['+i+']['+field+']', value[field]);
          });
          
        });

        formData.append('file', this.applicant.resumeFile);  

        this.applicant.copy_of_grades.forEach(file => {
          formData.append('copy_of_grades[]', file);
        });
            
        axios.post("/api/public_api/submit_application", formData, {
          headers: {
            'Content-Type': 'multipart/form-data' 
          }
        }).then(
          (response) => {
            console.log(response.data);
            this.disabled  = false;
            if(response.data.success){

              // send data to Sockot.IO Server
              // this.$socket.emit("sendData", { action: "applicant-submit" });

              this.$toaster.success('You have successfully submitted your job application.', {
                timeout: 2000
              });
              this.closeDialog();
              this.reset();
            }else if(response.data.duplicate){

              this.$toaster.error('You have already applied to this position, please select another position to apply.', {
                timeout: 7000
              });

              this.reset();
            }else{

              this.stepper = 1;
              this.checkbox = false;

              let errors = response.data;
              let errorNames = Object.keys(response.data);

              try{
                errorNames.forEach(value => {
                  this.applicantError[value].push(errors[value]);
                });
              } catch (error) {
                this.$toaster.info('There is an error in submitting your application. Please notify us at recruitment@addessa.com if you encounter this. Thank you!', {
                  timeout: 10000
                });
              }
            }
          },
          (error) => {
            console.log(error);
            this.isUnauthorized(error);
          }
        );
      }
      else
      {
        if(this.resumefileErrors.length)
        {
          this.stepper = 1;
          this.scrollToCurrentStep();
        }
        else if(!this.personalIsComplete)
        {
          this.stepper = 2;
        }
        else if(!this.educationIsComplete)
        {
          this.stepper = 3;
        }
        else if(!this.workIsComplete)
        {
          this.stepper = 4;
        }
        else if(!this.referenceIsComplete)
        {
          this.stepper = 5;
        }
        else if(!this.attachmentIsComplete || this.copyOfGradesErrors.length)
        {
          this.stepper = 6;
        }
        else if(this.$v.checkboxConfirm || this.$v.applicant.how_learn.$error || this.$v.applicant.how_learn_2.$error)
        {
          this.stepper = 7;
        }        

        this.$toaster.error('Check errors and fill in all required fields.', {
          timeout: 7000
        });
      }

      
    },

    birthdateInput() {

      this.bdate_value_change();
      this.$v.applicant.birthdate.$touch()
      this.applicantError.birthdate = [];
      this.validateDate('applicant.birthdate');
    },

    addExperience() {
      this.work_experiences.push(
        {
          company: "", 
          position: "", 
          salary: "", 
          service_start: "", 
          service_start_error: false, 
          service_start_error_msg: "",
          service_end: "", 
          service_end_error: false,
          service_end_error_msg: "",
          job_description: "",
        }
      );
    },

    removeExperience(index) {
      this.work_experiences.splice(index, 1);
    },

    handleChange(){
      const how_learn_selected = this.applicant.how_learn;

      if(how_learn_selected == "Others"){
        this.hide_howlearn_2 = true;
        this.continue_1 = false;

        const how_learn2 = this.applicant.how_learn_2;
        if(how_learn2){
        }
      }else{
        this.hide_howlearn_2 = false;
      }
    },

    async validateFields_form1(){
      let fieldnames = Object.keys(this.applicant);
      this.referencesHasError = false;
      this.educAttainHasError = false;
      this.continue_1 = true;

      let required_fields = [
        'lastname',
        'firstname',
        'address',
        'address2',
        'birth_place',
        'birthdate',
        'gender',
        'civil_status',
        'contact_no',
        'educ_attain',
        'citizenship',
        'religion',
        'height',
        'weight',
        'how_learn',
      ];

      await required_fields.forEach(value => {
        
        if(!this.applicant[value]){
          this.continue_1 = false;
          // console.log(value, this.applicant[value]);
        }
        else
        {
          if(this.$v.applicant.contact_no.$error || this.$v.applicant.email.$error)
          { 
            this.continue_1 = false;
          }

        }

      });

      this.educAttainHasError = await false;
      await this.validateEducAttain();
      
      // validateDateRang for educ attains S.Y
      let fields = Object.keys(this.dateRangeErrors);
      let dateRangeHasError = false;
      await fields.forEach(field => {
        this.dateRangeErrors[field] = false;
        let hasError = this.validateDateRange(this[field].sy_start, this[field].sy_end).hasError;
        this.dateRangeErrors[field] = hasError;
        if(hasError)
        {
          dateRangeHasError = true;
        }
      });

      // validate References
      await this.references.forEach((value, i) => {
        let fieldnames = Object.keys(value);
        
        fieldnames.forEach(field => {
          if(!['position', 'company'].includes(field))
          {
            if(!value[field])
            {
              this.referencesHasError = true;
            }
          }
        });
      });

      // validate all date input fields
      let date_obj_names = Object.keys(this.dateErrors);
      let dateInputHasError = false;
      await date_obj_names.forEach(field => {
        let obj_names = Object.keys(this.dateErrors[field]);
        obj_names.forEach(obj => {
          if(this.dateErrors[field][obj]) //if error value is true
          {
            dateInputHasError = true;
          } 
        });
      });

      // validate work experiences
      let workExperienceHasError = false;
      await this.work_experiences.forEach((value, i) => {
        this.validateWorkExperiences(i);
        if(this.work_experiences[i].service_start_error || this.work_experiences[i].service_end_error)
        {
          workExperienceHasError = true;
        }
      });

      // if educ attain has error or references has error or all date input has error or workexperiences has error then btn continue will be hidden
      this.continue_1 = this.educAttainHasError || this.referencesHasError ? false : dateInputHasError ? false : workExperienceHasError ? false : this.continue_1;
      // console.log('this.educAttainHasError', this.educAttainHasError);
      // console.log('this.referencesHasError', this.referencesHasError);
      // console.log('dateInputHasError', dateInputHasError);
      // console.log(workExperienceHasError);
      // console.log(this.continue_1);
      
    },

    validateDate(model) {
      let min_date = new Date('1900-01-01').getTime();
      let max_date = new Date().getTime();
      let [obj1, obj2] = model.split('.'); // e.g split 'highschool.sy_start'
      let date = this[obj1][obj2];

      let split_str = obj2.split('_');
      let str1 = "";
      let str2 = "";
      
      let date_value = new Date(date).getTime();
      let [year, month, day] = date.split('-');

      this.dateErrors[obj1][obj2] = false;

      if (date_value < min_date || date_value > max_date || year.length > 4) {
        this.dateErrors[obj1][obj2] = true;
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

    validateEducAttain(){
      let hs_fields = Object.keys(this.highschool);
      let jhs_fields = Object.keys(this.jr_highschool);
      let shs_fields = Object.keys(this.sr_highschool);
      let college_fields = Object.keys(this.college);
      let grad_fields = Object.keys(this.graduate_school);
      let voc_fields = Object.keys(this.vocational_school);
      let hasError = false;

      if(this.HSIsRequired)
      {
        hs_fields.forEach(value => {
          if(!this.highschool[value] && value != 'honors'){
            hasError = true;
          }
        });
      }

      if(this.JHSIsRequired)
      {
        jhs_fields.forEach(value => {
          if(!this.jr_highschool[value] && value != 'honors'){
            hasError = true;
          }
        });
      }

      if(this.SHSIsRequired)
      {
        shs_fields.forEach(value => {
          if(!this.sr_highschool[value] && value != 'honors'){
            hasError = true;
          }
        });
      }

      if(this.collegeIsRequired)
      {
        college_fields.forEach(value => {
          if(!this.college[value] && !['honors', 'major'].includes(value)){
            hasError = true;
          }
        });
      }

      if(this.gradSchoolIsRequired)
      {
        grad_fields.forEach(value => {
          if(!this.graduate_school[value] && !['honors', 'major'].includes(value)){
            hasError = true;
          }
        });
      }

      if(this.vocSchoolIsRequired)
      {
        voc_fields.forEach(value => {
          if(!this.vocational_school[value] && !['honors', 'major'].includes(value)){

            hasError = true;
          }
        });
      }
      
      this.educAttainHasError = hasError;


    },

    validateReference(i, field)
    {
      this.referencesHasError = false;
      this.referencesError[i][field] = "";
      if(!this.references[i][field]){
        this.continue_1 = false;
        this.referencesError[i][field] = 'This field is required';
      }
      
    },

    validateWorkExperiences(index) {
      let data = this.work_experiences[index];
      let date_now = new Date().getTime();
      let default_min_date = new Date('1900-01-01').getTime();
      let min_date = data.service_start;
      let max_date = data.service_end;
      let [min_year, min_month, min_day] = min_date.split('-');
      let [max_year, max_month, max_day] = max_date.split('-');

      min_date = min_date ? new Date(min_date) : new Date('1900-01-01').getTime();
      max_date = max_date ? new Date(max_date) : new Date().getTime();

      let service_start_error = false;
      let service_start_error_msg = "";
      let service_end_error = false;
      let service_end_error_msg = "";

      // if one of the fields has value and one the fields has no value, if field has a value the other one will be required
      if(data.service_start && !data.service_end)
      {
        service_end_error = true;
        service_end_error_msg = "This field is required";
      }
      if (!data.service_start && data.service_end)
      {
        service_start_error = true;
        service_start_error_msg = "This field is required";
      }
      if(date_now < min_date || default_min_date > min_date || min_year.length > 4)
      {
        service_start_error = true;
        service_start_error_msg = "Enter a valid date";
      }
      if(date_now < max_date || default_min_date > max_date || max_year.length > 4)
      {
        service_end_error = true;
        service_end_error_msg = "Enter a valid date";
      }
      if (max_date < min_date) {
        service_start_error = true;
        service_start_error_msg = "Enter a valid date";
        service_end_error = true;
        service_end_error_msg = "Enter a valid date";
      } 

      this.work_experiences[index].service_start_error = service_start_error;
      this.work_experiences[index].service_start_error_msg = service_start_error_msg;
      this.work_experiences[index].service_end_error = service_end_error;
      this.work_experiences[index].service_end_error_msg = service_end_error_msg;

      // console.log(this.work_experiences);
    },

    validateFile(){
      let resumeFile = this.applicant.resumeFile;
      let copy_of_grades = this.applicant.copy_of_grades;

      let extensions = ['docs', 'docx', 'pdf', 'jpg', 'jpeg', 'png'];
    
      if(resumeFile)
      {
        if(resumeFile.name)
        {
          let split_arr = resumeFile.name.split('.');
          let split_ctr = split_arr.length;
          let extension = split_arr[split_ctr - 1].toLowerCase();
          
          if(!extensions.includes(extension))
          {
            this.fileInvalid = true;
          }

          if(resumeFile.size > 5000000) // 5000000 bytes or 20MB
          {
            this.fileInvalid = true;
          }
        }
      }

      if(copy_of_grades.length)
      {
        copy_of_grades.forEach(file => {
          if(file.name)
          {
            let split_arr = file.name.split('.');
            let split_ctr = split_arr.length;
            let extension = split_arr[split_ctr - 1].toLowerCase();
            
            if(!extensions.includes(extension))
            {
              this.fileInvalid = true;
            }

            if(file.size > 5000000) // 5000000 bytes or 20MB
            {
              this.fileInvalid = true;
            }
          }
        });
        
      }

      this.continue_2 = true;

      if(!resumeFile || resumeFile.length == 0 || !copy_of_grades.length || this.fileInvalid){
        this.continue_2 = false;
      }
      
    },

    bdate_value_change(){
      this.bdate_value = this.applicant.birthdate;
    },

    decNumValFilter(evt) {
      evt = (evt) ? evt : window.event;
      let value = evt.target.value.toString() + evt.key.toString();

      if (!/^[-+]?[0-9]*\.?[0-9]*$/.test(value)) {
        evt.preventDefault();
      }
      else if(value.indexOf(".") > -1)
      {
        let split_val = value.split('.');
        let whole_num = split_val[0];
        let decimal_places = split_val[1];
        let whole_num_length = whole_num.length  
        let decimal_length = decimal_places.length;
  
        // if(decimal_length > 2) //decimal places limit 2
        // {
        //   evt.preventDefault();
        // }

      } else {

        return true;
      }
    },

    intNumValFilter(evt) {
      evt = (evt) ? evt : window.event;
      let value = evt.target.value.toString() + evt.key.toString();

      if (!/^[-+]?[0-9]*?[0-9]*$/.test(value)) {
        evt.preventDefault();
      }
      else {

        return true;
      }
    },

    formatDate(date) {
      if (!date) return null;

      const [year, month, day] = date.split("-");
      return `${month}/${day}/${year}`;
    },
    formatDateWorkService(field, toFormatDateField, index)
    { 
      let date_value = this.work_experiences[index][field];
      this.work_experiences[index][toFormatDateField] = this.formatDate(date_value);
      
    },
    resumeOnFileChange() {
      const file = this.applicant.resumeFile;
      
      if (file) {
        const reader = new FileReader();
        reader.onload = () => {
          const base64 = reader.result.split(',')[1];  // strip data URI prefix
          this.parseResume(base64, file.name);          
        };
        reader.readAsDataURL(file);
      }
    },
    async parseResume(filebase64, filename) {

      const payload = {
        filedata: filebase64,
        filename: filename,
        userkey: 'XSDYWBM9asdas',
        version: '8.0.0',
        subuserid: 'xavieraaa'
      };

      const requestOptions = {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      };

      try {
        const response = await fetch('https://rest.rchilli.com/RChilliParser/Rchilli/parseResumeBinary', requestOptions);
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        let responseData = await response.json();
        let data = responseData.ResumeParserData;       
        console.log(data);
        
        if(!responseData.error)
        {

          let [day, month, year] = data.DateOfBirth.split('/');
          let firstname = data.Name ? data.Name.FirstName : '';
          let lastname = data.Name ? data.Name.LastName : '';
          let middlename = data.Name ? data.Name.MiddleName : '';
          let contact_no = data.PhoneNumber.length ? data.PhoneNumber[0].Number : '';
          
          Object.assign(this.applicant, {
                              lastname: lastname,
                              firstname: firstname,
                              middlename: middlename,
                              address: data.Address.length ? data.Address[0].FormattedAddress : '',
                              address2: data.Address.length > 1 ? data.Address[1].FormattedAddress : '',
                              // birth_place: "",
                              birthdate: `${year}-${month}-${day}`,
                              // age: "",
                              // gender: "",
                              // civil_status: "",
                              contact_no: contact_no,
                              email: data.Email.length ? data.Email[0].EmailAddress : '',
                              // educ_attain: "",
                              // course: "",
                              // school_grad: "",
                              citizenship: data.Nationality,
                              // religion: "",
                              // height: "",
                              // weight: "",
                              // how_learn: "",
                              // resumeFile: [],
                              // copy_of_grades: []
                          })
          
        }
        else
        {
          console.log(responseData.error);
          
        }
        
      } catch (error) {
        console.error('Error:', error);
      }

    },
    closeDialog() {
      this.reset();
      this.$emit('closeDialog');
    },

    scrollToCurrentStep() {
      const wrapper = this.$refs.stepperWrapper;
      const stepEl = this.$refs[`step-${this.stepper}`]?.[0];

      if (wrapper && stepEl) {
        const wrapperRect = wrapper.getBoundingClientRect();

        setTimeout(() => {
           
          if(this.stepper == 1)
          {
            wrapper.scrollLeft = 0
          }
          else
          {
            wrapper.scrollLeft =  50 * this.stepper;
          }
        }, .500);

      }
      
      
    },
  },

  watch:{
    stepper() {
      this.scrollToCurrentStep();
    },
    bdate_value() {
      let currentDate = new Date();
      let birthDate = new Date(this.bdate_value);

      let difference = currentDate - birthDate;
      let age = Math.floor(difference/31557600000);

      this.applicant.age = age;

    },

    'applicant.educ_attain'() {
      this.$v.highschool.$reset();
      this.$v.jr_highschool.$reset();
      this.$v.sr_highschool.$reset();
      this.$v.college.$reset();
      this.$v.graduate_school.$reset();
      this.$v.vocational_school.$reset();
      this.highschool = {
        school: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.jr_highschool = {
        school: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.sr_highschool = {
        school: "",
        strand: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.college = {
        school: "",
        course: "",
        major: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.graduate_school = {
        school: "",
        course: "",
        major: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };
      this.vocational_school = {
        school: "",
        course: "",
        major: "",
        sy_start: "",
        sy_end: "",
        honors: "",
      };

      this.dateErrors = {
        applicant: {
          birthdate: false,
        },
        highschool: {
          sy_start: false,
          sy_end: false,
        },
        jr_highschool: {
          sy_start: false,
          sy_end: false,
        },
        sr_highschool: {
          sy_start: false,
          sy_end: false,
        },
        college: {
          sy_start: false,
          sy_end: false,
        },
        vocational_school: {
          sy_start: false,
          sy_end: false,
        },
      }; 

    } 
  },

  computed: {
    
    // validations
    lastnameErrors() {
      const errors = [];
      if (!this.$v.applicant.lastname.$dirty) return errors;
      !this.$v.applicant.lastname.required &&
        errors.push("Last name is required.");
      return errors;
    },

    firstnameErrors() {
      const errors = [];
      if (!this.$v.applicant.firstname.$dirty) return errors;
      !this.$v.applicant.firstname.required &&
        errors.push("First name is required.");
      return errors;
    },

    middlenameErrors() {
      const errors = [];
      if (!this.$v.applicant.middlename.$dirty) return errors;
      !this.$v.applicant.middlename.required &&
        errors.push("Middle name is required.");
      return errors;
    },

    addressErrors() {
      const errors = [];
      if (!this.$v.applicant.address.$dirty) return errors;
      !this.$v.applicant.address.required &&
        errors.push("Present Address is required.");
      return errors;
    },

    address2Errors() {
      const errors = [];
      if (!this.$v.applicant.address2.$dirty) return errors;
      !this.$v.applicant.address2.required &&
        errors.push("Home Address is required.");
      return errors;
    },

    birthdateErrors() {
      const errors = [];
      if (!this.$v.applicant.birthdate.$dirty) return errors;
      !this.$v.applicant.birthdate.required &&
        errors.push("Birthday is required.");
      
      if(this.dateErrors.applicant.birthdate)
      {
        errors.push("Enter a valid date");
      }
      
      return errors;
    },

    birthPlaceErrors() {
      const errors = [];
      if (!this.$v.applicant.birth_place.$dirty) return errors;
      !this.$v.applicant.birth_place.required &&
        errors.push("Birth Place is required.");
      return errors;
    },

    genderErrors() {
      const errors = [];
      if (!this.$v.applicant.gender.$dirty) return errors;
      !this.$v.applicant.gender.required &&
        errors.push("Gender is required.");
      return errors;
    },

    civilStatusErrors() {
      const errors = [];
      if (!this.$v.applicant.civil_status.$dirty) return errors;
      !this.$v.applicant.civil_status.required &&
        errors.push("Civil Status is required.");
      return errors;
    },

    contactNoErrors() {
      const errors = [];
      if (!this.$v.applicant.contact_no.$dirty) return errors;
      !this.$v.applicant.contact_no.required &&
        errors.push("Contact number is required. ");
      !this.$v.applicant.contact_no.minLength &&
        errors.push("Must be a valid Phone Number. ");
      return errors;
    },

    emailErrors() {
      const errors = [];
      if (!this.$v.applicant.email.$dirty) return errors;
      // !this.$v.applicant.email.required &&
      //   errors.push("Email is required.");

      !this.$v.applicant.email.email &&
        errors.push("Must be valid e-mail.");
      return errors;
    },

    educAttainErrors() {
      const errors = [];
      if (!this.$v.applicant.educ_attain.$dirty) return errors;
      !this.$v.applicant.educ_attain.required &&
        errors.push("Educational attainment is required.");
      return errors;
    },

    courseErrors() {
      const errors = [];
      if (!this.$v.applicant.course.$dirty) return errors;
      !this.$v.applicant.course.required &&
        errors.push("Course is required.");
      return errors;
    },

    citizenshipErrors() {
      const errors = [];
      if (!this.$v.applicant.citizenship.$dirty) return errors;
      !this.$v.applicant.citizenship.required &&
        errors.push("Citizenship is required. ");
      return errors;
    },

    religionErrors() {
      const errors = [];
      if (!this.$v.applicant.religion.$dirty) return errors;
      !this.$v.applicant.religion.required &&
        errors.push("Religion is required. ");
      return errors;
    },

    weightErrors() {
      const errors = [];
      if (!this.$v.applicant.weight.$dirty) return errors;
      !this.$v.applicant.weight.required &&
        errors.push("Weight is required. ");
      return errors;
    },

    heightErrors() {
      const errors = [];
      if (!this.$v.applicant.height.$dirty) return errors;
      !this.$v.applicant.height.required &&
        errors.push("Height is required. ");
      return errors;
    },

    // schoolGradErrors() {
    //   const errors = [];
    //   if (!this.$v.applicant.school_grad.$dirty) return errors;
    //   !this.$v.applicant.school_grad.required &&
    //     errors.push("School graduated is required.");
    //   return errors;
    // },

    HSSchoolErrors() {
      const errors = [];
      if (!this.$v.highschool.school.$dirty) return errors;
      !this.$v.highschool.school.required &&
        errors.push("This field is required.");
      return errors;
    },

    HSSYStartErrors() {
      const errors = [];

      if (!this.$v.highschool.sy_start.$dirty) return errors;
      !this.$v.highschool.sy_start.required &&
        errors.push("This field is required.");

      if(this.dateErrors.highschool.sy_start || this.dateRangeErrors.highschool)
      {
        errors.push('Enter a valid date');
      }  
      
      return errors;
    },

    HSSYEndErrors() {
      const errors = [];
      if (!this.$v.highschool.sy_end.$dirty) return errors;
      !this.$v.highschool.sy_end.required &&
        errors.push("This field is required.");

      if(this.dateErrors.highschool.sy_end || this.dateRangeErrors.highschool)
      {
        errors.push('Enter a valid date');
      }  
      return errors;
    },

    JRHSchoolErrors() {
      const errors = [];
      if (!this.$v.jr_highschool.school.$dirty) return errors;
      !this.$v.jr_highschool.school.required &&
        errors.push("This field is required.");
      
      return errors;
    },

    JRSYStartErrors() {
      const errors = [];
      if (!this.$v.jr_highschool.sy_start.$dirty) return errors;
      !this.$v.jr_highschool.sy_start.required &&
        errors.push("This field is required.");
      if(this.dateErrors.jr_highschool.sy_start || this.dateRangeErrors.jr_highschool)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },

    JRSYEndErrors() {
      const errors = [];
      if (!this.$v.jr_highschool.sy_end.$dirty) return errors;
      !this.$v.jr_highschool.sy_end.required &&
        errors.push("This field is required.");
      if(this.dateErrors.jr_highschool.sy_end || this.dateRangeErrors.jr_highschool)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },

    SRHSchoolErrors() {
      const errors = [];
      if (!this.$v.sr_highschool.school.$dirty) return errors;
      !this.$v.sr_highschool.school.required &&
        errors.push("This field is required.");
      return errors;
    },

    SRHSStrandErrors() {
      const errors = [];
      if (!this.$v.sr_highschool.strand.$dirty) return errors;
      !this.$v.sr_highschool.strand.required &&
        errors.push("This field is required.");
      return errors;
    },

    SRSYStartErrors() {
      const errors = [];
      if (!this.$v.sr_highschool.sy_start.$dirty) return errors;
      !this.$v.sr_highschool.sy_start.required &&
        errors.push("This field is required.");
      if(this.dateErrors.sr_highschool.sy_start || this.dateRangeErrors.sr_highschool)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },

    SSYEndErrors() {
      const errors = [];
      if (!this.$v.sr_highschool.sy_end.$dirty) return errors;
      !this.$v.sr_highschool.sy_end.required &&
        errors.push("This field is required.");
      if(this.dateErrors.sr_highschool.sy_end || this.dateRangeErrors.sr_highschool)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },

    collegeSchoolErrors() {
      const errors = [];
      if (!this.$v.college.school.$dirty) return errors;
      !this.$v.college.school.required &&
        errors.push("This field is required.");
      return errors;
    },

    collegeCourseErrors() {
      const errors = [];
      if (!this.$v.college.course.$dirty) return errors;
      !this.$v.college.course.required &&
        errors.push("This field is required.");
      return errors;
    },

    collegeSYStartErrors() {
      const errors = [];
      if (!this.$v.college.sy_start.$dirty) return errors;
      !this.$v.college.sy_start.required &&
        errors.push("This field is required.");
      if(this.dateErrors.college.sy_start || this.dateRangeErrors.college)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },
    collegeSYEndErrors() {
      const errors = [];
      if (!this.$v.college.sy_end.$dirty) return errors;
      !this.$v.college.sy_end.required &&
        errors.push("This field is required.");
      if(this.dateErrors.college.sy_end || this.dateRangeErrors.college)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },

    gradSchoolErrors() {
      const errors = [];
      if (!this.$v.graduate_school.school.$dirty) return errors;
      !this.$v.graduate_school.school.required &&
        errors.push("This field is required.");
      return errors;
    },

    gradCourseErrors() {
      const errors = [];
      if (!this.$v.graduate_school.course.$dirty) return errors;
      !this.$v.graduate_school.course.required &&
        errors.push("This field is required.");
      return errors;
    },

    gradSYStartErrors() {
      const errors = [];
      if (!this.$v.graduate_school.sy_start.$dirty) return errors;
      !this.$v.graduate_school.sy_start.required &&
        errors.push("This field is required.");
      if(this.dateErrors.graduate_school.sy_start || this.dateRangeErrors.graduate_school)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },

    gradSYEndErrors() {
      const errors = [];
      if (!this.$v.graduate_school.sy_end.$dirty) return errors;
      !this.$v.graduate_school.sy_end.required &&
        errors.push("This field is required.");
      if(this.dateErrors.graduate_school.sy_end || this.dateRangeErrors.graduate_school)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },

    vocSchoolErrors() {
      const errors = [];
      if (!this.$v.vocational_school.school.$dirty) return errors;
      !this.$v.vocational_school.school.required &&
        errors.push("This field is required.");
      return errors;
    },

    vocCourseErrors() {
      const errors = [];
      if (!this.$v.vocational_school.course.$dirty) return errors;
      !this.$v.vocational_school.course.required &&
        errors.push("This field is required.");
      return errors;
    },

    vocSYStartErrors() {
      const errors = [];
      if (!this.$v.vocational_school.sy_start.$dirty) return errors;
      !this.$v.vocational_school.sy_start.required &&
        errors.push("This field is required.");
      if(this.dateErrors.vocational_school.sy_start || this.dateRangeErrors.vocational_school)
      {
        errors.push('Enter a valid date');
      } 
      return errors;
    },

    vocSYEndErrors() {
      const errors = [];
      if (!this.$v.vocational_school.sy_start.$dirty) return errors;
      !this.$v.vocational_school.sy_start.required &&
        errors.push("This field is required.");
      if(this.dateErrors.vocational_school.sy_end || this.dateRangeErrors.vocational_school)
      {
        errors.push('Enter a valid date');
      }
      return errors;
    },

    howLearnErrors() {
      const errors = [];
      if (!this.$v.applicant.how_learn.$dirty) return errors;
      !this.$v.applicant.how_learn.required &&
        errors.push("This field is required.");
      return errors;
    },

    resumefileErrors() {
      const errors = [];
      if (!this.$v.applicant.resumeFile.$dirty) return errors;
      !this.$v.applicant.resumeFile.required &&
        errors.push("File is required.");
      
      let file = this.applicant.resumeFile;
      let extensions = ['docs', 'docx', 'pdf', 'jpg', 'jpeg', 'png'];
      let errorMsg = "";
      let fileInvalid = false;
    
      if(file)
      {
        if(file.name)
        {
          let split_arr = file.name.split('.');
          let split_ctr = split_arr.length;
          let extension = split_arr[split_ctr - 1].toLowerCase();
          
          if(!extensions.includes(extension))
          {
            fileInvalid = true;
            errorMsg = `File type must be ${extensions.join(', ')}.`;
          }

          if(file.size > 5000000) // 5000000 bytes or 20MB
          {
            errorMsg = "File size maximum is 5MB";
            fileInvalid = true;
          }
        }
      }
      this.fileInvalid = fileInvalid;
      fileInvalid && errors.push(errorMsg);

      return errors
      
    },
    copyOfGradesErrors() {
      const errors = [];
      if (!this.$v.applicant.copy_of_grades.$dirty) return errors;
      !this.$v.applicant.copy_of_grades.required &&
        errors.push("Copy of Grades is required.");

      let copy_of_grades = this.applicant.copy_of_grades;
      let extensions = ['docs', 'docx', 'pdf', 'jpg', 'jpeg', 'png'];
      let errorMsg = "";
      let fileInvalid = false;

      if(copy_of_grades.length)
      {
        copy_of_grades.forEach(file => {
          if(file.name)
          {
            let split_arr = file.name.split('.');
            let split_ctr = split_arr.length;
            let extension = split_arr[split_ctr - 1].toLowerCase();
            
            if(!extensions.includes(extension))
            {
              fileInvalid = true;
              errorMsg = `File type must be ${extensions.join(', ')}.`;
            }

            if(file.size > 5000000) // 5000000 bytes or 20MB
            {
              errorMsg = "File size maximum is 5MB";
              fileInvalid = true;
            }
          }
        });
        
      }

      this.fileInvalid = fileInvalid;
      fileInvalid && errors.push(errorMsg);

      return errors
    },
    howLearn2Errors() {

      const errors = [];
      if (!this.$v.applicant.how_learn_2.$dirty) return errors;
      !this.$v.applicant.how_learn_2.required &&
        errors.push("This field is required.");
      return errors;
    },

    checkboxConfirmErrors() {
      const errors = [];
      if (!this.$v.checkboxConfirm.$dirty) return errors;
      !this.$v.checkboxConfirm.required &&
        errors.push("Please cofirm");
      return errors;
    },

    howLearn2IsRequired() {
      return this.applicant.how_learn == 'Others';
    },

    HSIsRequired() {
      return !this.k_12_checkbox && ![2, 3].includes(this.applicant.educ_attain);
    },

    JHSIsRequired() {
      let educ_attain = this.applicant.educ_attain;
      return (this.k_12_checkbox && educ_attain > 3) || (educ_attain > 1 && educ_attain < 4);
    },

    SHSIsRequired() {
      let educ_attain = this.applicant.educ_attain;
      return (this.k_12_checkbox && educ_attain > 3) || (educ_attain > 1 && educ_attain == 3);
    },

    collegeIsRequired() {
      return this.applicant.educ_attain > 3 && this.applicant.educ_attain != 6;
    },

    gradSchoolIsRequired() {
      // return this.applicant.educ_attain  === 5;
      return false;
    },

    vocSchoolIsRequired() {
      return this.applicant.educ_attain === 6;
    },

    HSSYStartDate() {
      return this.formatDate(this.highschool.sy_start);      
    },

    HSSYEndDate() {
      return this.formatDate(this.highschool.sy_end);      
    },

    JRSYStartDate() {
      return this.formatDate(this.jr_highschool.sy_start);      
    },

    JRSYEndDate() {
      return this.formatDate(this.jr_highschool.sy_end);      
    },

    SRSYStartDate() {
      return this.formatDate(this.sr_highschool.sy_start);      
    },

    SRSYEndDate() {
      return this.formatDate(this.sr_highschool.sy_end);      
    },

    collegeSYStartDate() {
      return this.formatDate(this.college.sy_start);      
    },

    collegeSYEndDate() {
      return this.formatDate(this.college.sy_end);      
    },

    vocationalSYStartDate() {
      return this.formatDate(this.vocational_school.sy_start);      
    },

    vocationalSYEndDate() {
      return this.formatDate(this.vocational_school.sy_end);      
    },

    stepperItems() {
      let stepper = [
        { text: 'Attach Resume', step: 1, isComplete: false, color: 'primary' },
        { text: 'Personal Details', step: 2, isComplete: false, color: 'primary' },
        { text: 'Educational Background', step: 3, isComplete: false, color: 'primary' },
        { text: 'Work Experiences', step: 4, isComplete: false, color: 'primary' },
        { text: 'References', step: 5, isComplete: false, color: 'primary' },
        { text: 'Other Attachment', step: 6, isComplete: false, color: 'primary' },
        { text: 'Submit', step: 7, isComplete: false, color: 'primary' },
      ];

      let file = this.applicant.resumeFile;

      if(file)
      {
        if(file.name)
        {
          stepper[0].isComplete = true;
          Object.assign(stepper[0], { isComplete: true, color: 'success' });

          let steps = stepper.filter(value => value.step > 1);
        }
      }

      return stepper;
        
    },

    personalIsComplete() {

      let isComplete = true;
      let required_fields = [
        'lastname',
        'firstname',
        'address',
        'address2',
        'birth_place',
        'birthdate',
        'gender',
        'civil_status',
        'contact_no',
        'citizenship',
        'religion',
        'height',
        'weight',
      ];

      required_fields.forEach(value => {
        
        if(!this.applicant[value]){
          isComplete = false;
          // console.log(value, this.applicant[value]);
        }
        else
        {
          if(this.$v.applicant.contact_no.$error || this.$v.applicant.email.$error)
          { 
            isComplete = false;
          }

        }

      });

      return isComplete;

    },

    educationIsComplete() {
      let isComplete = true;

      let hs_fields = Object.keys(this.highschool);
      let jhs_fields = Object.keys(this.jr_highschool);
      let shs_fields = Object.keys(this.sr_highschool);
      let college_fields = Object.keys(this.college);
      let grad_fields = Object.keys(this.graduate_school);
      let voc_fields = Object.keys(this.vocational_school);
      let hasError = false;

      if(this.HSIsRequired)
      {
        hs_fields.forEach(value => {
          if(!this.highschool[value] && value != 'honors'){
            hasError = true;
            isComplete = false;
          }
        });
      }

      if(this.JHSIsRequired)
      {
        jhs_fields.forEach(value => {
          if(!this.jr_highschool[value] && value != 'honors'){
            hasError = true;
            isComplete = false;
          }
        });
      }

      if(this.SHSIsRequired)
      {
        shs_fields.forEach(value => {
          if(!this.sr_highschool[value] && value != 'honors'){
            hasError = true;
            isComplete = false;
          }
        });
      }

      if(this.collegeIsRequired)
      {
        college_fields.forEach(value => {
          if(!this.college[value] && !['honors', 'major'].includes(value)){
            hasError = true;
            isComplete = false;
          }
        });
      }

      if(this.gradSchoolIsRequired)
      {
        grad_fields.forEach(value => {
          if(!this.graduate_school[value] && !['honors', 'major'].includes(value)){
            hasError = true;
            isComplete = false;
          }
        });
      }

      if(this.vocSchoolIsRequired)
      {
        voc_fields.forEach(value => {
          if(!this.vocational_school[value] && !['honors', 'major'].includes(value)){
            isComplete = false;
          }
        });
      }
      
      let fields = Object.keys(this.dateRangeErrors);

      fields.forEach(field => {
        this.dateRangeErrors[field] = false;
        let hasError = this.validateDateRange(this[field].sy_start, this[field].sy_end).hasError;
        this.dateRangeErrors[field] = hasError;
        if(hasError)
        {
          isComplete = false;
        }
      });

      return isComplete;
      
    },

    referenceIsComplete() {
      // validate References
      let isComplete = true;
      this.references.forEach((value, i) => {
        let fieldnames = Object.keys(value);
        
        fieldnames.forEach(field => {
          if(!['position', 'company'].includes(field))
          {
            if(!value[field])
            {
              isComplete = false;
            }
          }
        });
      });

      // validate all date input fields
      let date_obj_names = Object.keys(this.dateErrors);

      date_obj_names.forEach(field => {
        let obj_names = Object.keys(this.dateErrors[field]);
        obj_names.forEach(obj => {
          if(this.dateErrors[field][obj]) //if error value is true
          {
            isComplete = false;
          } 
        });
      });

      return isComplete;
    },

    workIsComplete() {
      let isComplete = true;

      // validate work experiences

      this.work_experiences.forEach((value, i) => {
        this.validateWorkExperiences(i);
        if(this.work_experiences[i].service_start_error || this.work_experiences[i].service_end_error)
        {
          isComplete = false;
        }
      });

      return isComplete;
    },

    attachmentIsComplete() {
      let isComplete = true;

      if(this.$v.applicant.copy_of_grades.$error)
      {
        isComplete = false;
      }

      return isComplete;

    },
    
  },
  created(){
    AOS.init();
  },

  mounted() {
    
  },
}
</script>
