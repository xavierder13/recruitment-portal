<template>
  <v-card>
    <v-app-bar
      absolute
      color="#c00000"
      elevate-on-scroll
      class="fixed-bar"
      height="80px"
    >
      <v-toolbar-title data-aos="flip-left" data-aos-duration="2000" data-aos-easing="ease-in-sine" class="font-weight-bold">
        <img
          src="img/addessa_logo_1.png"
          width="200px"
          height="auto"
          class="ml-12 mt-5"
          style="border-radius: 5px; display:block; margin:0 auto;"
        />
      </v-toolbar-title>
      <template v-slot:extension>
        <v-tabs align-with-title data-aos="flip-right" data-aos-duration="2000" data-aos-easing="ease-in-sine" style="font-family: 'Lato', sans-serif;">
          <v-tab @click="home_tab()" style="color: #ffffff;">HOME</v-tab>
          <v-tab @click="about_tab()" style="color: #ffffff;">ABOUT US</v-tab>
          <v-tab @click="careers_tab()" style="color: #ffffff;">CAREERS</v-tab>
        </v-tabs>
        <v-progress-linear
          :active="loading"
          :indeterminate="loading"
          absolute
          bottom
          color="white accent-4"
        ></v-progress-linear>
      </template>
    </v-app-bar>

    <!-- Home tab -->
    <v-container style="width: 85%;" v-if="home">
      <v-row>
        <v-col cols="12">
          <v-carousel 
            v-model="model"
            data-aos="flip-left" 
            data-aos-duration="1000"
            height="auto" width="auto"
          >
            <v-carousel-item
              v-for="(images, i) in images"
              :key="i"
            >
              <v-img :src="images.src"></v-img>
            </v-carousel-item>
          </v-carousel>
        </v-col>
      </v-row>  
    </v-container>

    <v-container class="mb-12" style="width: 80%;" v-if="home">
      <v-row>
        <v-card width="100%" class="mt-3">
          <v-col cols="12" xs="12" sm="12" md="12" lg="12" 
            data-aos="fade-right" 
            data-aos-duration="2000"
          > 
            <v-text-field
              class="mt-2"
              v-model="search_textfield"
              append-icon="mdi-magnify"
              label="Search keywords"
              hint="Position title or branch name"
              persistent-hint
              outlined
              color="red"
              v-on:keyup="search_job_vacancy()"
            >
              <template v-slot:prepend>
                <v-tooltip
                  bottom
                >
                  <template v-slot:activator="{ on }">
                    <img
                      src="img/addessa.jpg"
                      width="30px"
                      height="auto"
                      alt="Search"
                      v-on="on"
                    />
                  </template>
                  Search keywords
                </v-tooltip>
              </template>
            </v-text-field>
          </v-col>
        </v-card>  
        <v-col class="text-right" cols="12" xs="12" sm="12" md="12" lg="12" data-aos="fade-up" data-aos-duration="2000">
          <v-row class="mt-5">
            <v-col xs="12" sm="12" md="6" lg="6">
              <h3 class="text-left" 
                style="font-weight: bold; color: #f44336; font-family: 'Lato', sans-serif;" 
                data-aos="fade-down" 
                data-aos-duration="3000"
              >Latest careers</h3>
            </v-col>

            <v-col xs="12" sm="12" md="6" lg="6">
              <v-btn
                color="#f44336"
                outlined
                class="font-weight-bold"
                @click="getAllJobVacancies(), show_all_job_vacancy_cards()"
              >
                View All Jobs <v-icon>mdi-menu-right-outline</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-col>  
      </v-row>  
      <v-row align="center" 
        data-aos="fade-down"
        data-aos-duration="2000"
      >
        <v-col xs="12" sm="12" md="3" lg="3"
          v-for="(job_vacancy, index) in job_vacancies"
          :key="index"
        >
          <v-scroll-y-transition mode="out-in">
            <v-card 
              id="card_hover" 
              class="text-center" 
              v-if="job_cards"
              style="font-family: 'Lato', sans-serif; min-height: 300px;"
            >
              <v-col md="12">
                <span class="font-weight-thin" 
                  style="color: #000000; font-family: Lato, 'Lato', sans-serif"
                  v-if="job_vacancy.days < 1"
                >
                  <v-chip
                    color="gray"
                    style="color: #000000; font-family: 'Lato', sans-serif;"
                    class="font-weight-medium mt-2"
                  >
                    <span style="font-size: 12px;" v-if="job_vacancy.minutes < 60"> 
                      <span v-if="job_vacancy.minutes < 2"> Posted 1 minute ago </span>
                      <span v-else> Posted {{ job_vacancy.minutes }} minutes ago </span>
                    </span>
                    <span style="font-size: 12px;" v-else> 
                      <span v-if="job_vacancy.hours < 2"> Posted 1 hour ago </span>
                      <span v-else> Posted {{ job_vacancy.hours }} hours ago </span>
                    </span>
                  </v-chip>
                </span>

                <span class="font-weight-thin" 
                  style="color: #000000; font-family: Lato, 'Lato', sans-serif; font-size: 12px;"
                  v-else
                >
                  <v-chip
                    color="gray"
                    style="color: #000000; font-family: 'Lato', sans-serif;"
                    class="font-weight-medium mt-2"
                    v-if="job_vacancy.days < 2"
                  >
                    Posted {{ job_vacancy.days }} day ago
                  </v-chip>

                  <v-chip
                    color="gray"
                    style="color: #000000; font-size: 12px;"
                    class="font-weight-medium"
                    v-else
                  >
                    Posted {{ job_vacancy.days }} days ago
                  </v-chip>
                </span>
              </v-col>  
              <v-col md="12" class="ma-0 pa-0">
                <span>
                  <v-img src="img/card_bg.png" height="auto" width="50%" style="display:block; margin:0 auto;" />
                </span>
              </v-col>  
              <v-col md="12" class="ma-0 pa-2">
                <h5 class="font-weight-black" style="color: #9d0a10; text-transform: uppercase;">{{ job_vacancy.position_name }}</h5>
              </v-col>   
              <v-col md="12" class="font-weight-medium ma-0 pa-0">
                <span style="display: block; font-weight: bold; font-size: 18px;">ADDESSA</span>
                <span>{{ job_vacancy.branch_name }}</span>
              </v-col>    
              <v-col md="12">
                <v-btn
                  color="#f44336"
                  outlined
                  class="font-weight-bold mt-2 mb-2"
                  @click="(dialog_apply_now = true), getJobDescription(job_vacancy.id)"
                >
                  Apply Now!
                </v-btn>
              </v-col>
            </v-card>
          </v-scroll-y-transition>
        </v-col>
      </v-row>
    </v-container>

    <!-- Dialogs -->
      <!-- Description dialog -->
      <v-row justify="center">
        <v-dialog
          v-model="dialog_apply_now"
          fullscreen
          hide-overlay
          transition="dialog-bottom-transition"
        >
          <v-card>
            <!-- overlay loading -->
            <v-overlay :value="overlay">
              <v-progress-circular
                indeterminate
                size="64"
              ></v-progress-circular>
            </v-overlay>
            
            <v-toolbar
              dark
              color="#c00000"
              class="fixed-bottombar"
            >
              <v-btn
                icon
                dark
                @click="(dialog_apply_now = false), (hide_cards())"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
              <v-toolbar-title style="font-size: 15px; font-family: 'Lato', sans-serif;">APPLICATION DETAILS</v-toolbar-title>
              <v-spacer></v-spacer>
            </v-toolbar>
            <v-container 
              style="background-color: #c00000; 
                     color: #ffffff; 
                     max-width: 100%; 
                     text-align: center;
                     font-family: 'Lato', sans-serif;
                     "
              v-if="jobtop_card"
            >
              <h1 class="font-weight-bold" 
                data-aos="fade_down" 
                data-aos-duration="2000"
              >{{ this.job_title }}</h1>
                <h5  
                  data-aos="fade_down" 
                  data-aos-duration="2000"
                >ADDESSA {{ this.branch }}</h5>
                  <span
                    data-aos="fade_down" 
                    data-aos-duration="2000"
                    v-if="this.days < 1"
                  >
                    <v-chip
                      color="gray"
                      style="color: #000000; font-size: 13px;"
                      class="font-weight-medium "
                    >
                      <span v-if="this.minutes < 60"> 
                        <span v-if="this.minutes < 2"> Posted 1 minute ago </span>
                        <span v-else> Posted {{ this.minutes }} minutes ago </span>
                      </span>
                      <span v-else> 
                        <span v-if="this.hours < 2"> Posted 1 hour ago </span>
                        <span v-else> Posted {{ this.hours }} hours ago </span>
                      </span>
                    </v-chip>
                  </span>
                  <span
                    data-aos="fade_down" 
                    data-aos-duration="2000"
                    v-else
                  >
                    <v-chip
                      color="gray"
                      style="color: #000000; font-size: 13px;"
                      class="font-weight-medium"
                      v-if="this.days < 2"
                    >Posted {{ this.days }} day ago
                    </v-chip>
                    <v-chip
                      color="gray"
                      style="color: #000000; font-size: 13px;"
                      class="font-weight-medium"
                      v-else
                    >Posted {{ this.days }} days ago
                    </v-chip>
                  </span>
            </v-container>
            
            <v-container style="font-family: 'Lato', sans-serif;">
              <v-row>
                <!-- LEFT  -->
                <v-col xs="12" sm="12" md="8" lg="8" 
                  v-if="jobdesc_card" 
                  data-aos="fade-down" 
                  data-aos-duration="1000"
                >
                  <v-container style="max-width: 100%; margin-top: 10px;">
                    <v-card>
                      <v-row style="padding: 1.750em">
                        <v-col xs="12" sm="12" md="12" lg="12">
                          <h3 class="font-weight-thin">JOB DESCRIPTION</h3>
                          <h5 class="font-weight-bold mt-5">RESPONSIBILITIES:</h5>
                          <p>
                            <span v-html="this.description"></span>
                          </p>
                          <h5 class="font-weight-bold mt-5">QUALIFICATIONS:</h5>
                          <p>
                            <span v-html="this.qualifications"></span>
                          </p>
                        </v-col>
                      </v-row>
                    </v-card>
                  </v-container>
                </v-col>

                <!-- RIGHT -->
                <v-col xs="12" 
                  sm="12" md="4" lg="4" 
                  v-if="overview_card"
                  data-aos="fade-down" 
                  data-aos-duration="1000"
                >
                  <v-container style="max-width: 100%; margin-top: 10px;">
                    <v-card>
                      <v-row style="padding: 1.750em">
                        <v-col xs="12" sm="12" md="12" lg="12">
                          <h3 class="font-weight-thin">OVERVIEW</h3>
                          <h6 class="font-weight-medium mt-5" style="color: #6e6767;">BRANCH LOCATION:</h6>
                          <p>
                            <span v-html="'ADDESSA ' + this.branch"></span>
                          </p>
                          <h6 class="font-weight-medium mt-5" style="color: #6e6767;">WORK TYPE:</h6>
                          <p>
                            <span>Full-time</span>
                          </p>
                          <h6 class="font-weight-medium mt-5" style="color: #6e6767;">EDUCATION LEVEL:</h6>
                          <p>
                            <span>{{ this.educ_attain }}</span>
                          </p>
                          <h6 class="font-weight-medium mt-5" style="color: #6e6767;">SEARCH TAGS:</h6>
                          <p>
                            <span>POSITION NAME</span>
                            <p>
                              <v-chip
                                color="gray"
                                style="color: #000000;"
                                class="font-weight-medium"
                              ><span>{{ this.job_title }}</span>
                              </v-chip>
                            </p>
                          </p>
                          <p>
                            <span>ADDESSA BRANCH</span>
                            <p>
                              <v-chip
                                color="gray"
                                style="color: #000000;"
                                class="font-weight-medium"
                              ><span>{{ this.branch }}</span>
                              </v-chip>
                            </p>
                          </p>
                        </v-col>
                      </v-row>
                    </v-card>
                    <div class="mt-7 text-center">
                      <v-btn
                        x-large
                        style="background-color: #f44336; color: #ffffff;"
                        @click="(dialog_submit_now = true), (e1 = 1)"
                      >
                        Submit Application
                      </v-btn>
                    </div>  
                  </v-container>
                </v-col>
              </v-row>  
            </v-container>  
          </v-card>
        </v-dialog>
      </v-row>

      <!-- Apply Now  -->
      <v-row justify="center">
        <v-dialog
          v-model="dialog_submit_now"
          persistent
          max-width="600px"
        >
          <v-card>
            <v-card-title style="background-color: #f44336; color: #ffffff;">
              <span class="text-h5">Applicant Details</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-stepper v-model="e1">
                  <v-stepper-header>
                    <v-stepper-step
                      :complete="e1 > 1"
                      step="1"
                    >
                      Personal Details
                    </v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step
                      :complete="e1 > 2"
                      step="2"
                    >
                      Attach Resume
                    </v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step step="3" :complete="e1 > 3">
                      Submit
                    </v-stepper-step>
                  </v-stepper-header>

                  <v-stepper-items>
                    <v-stepper-content step="1">
                      <v-row>
                        <v-col
                          cols="12"
                          sm="12"
                          md="12"
                        >
                          <v-text-field
                            label="Last name"
                            persistent-hint
                            v-model="applicant.lastname"
                            :error-messages="lastnameErrors + applicantError.lastname"
                            @input="$v.applicant.lastname.$touch() + (applicantError.lastname = []) + (validateFields_form1())"
                            @blur="$v.applicant.lastname.$touch() + (applicantError.lastname = [])"
                          ></v-text-field>
                        </v-col>

                        <v-col
                          cols="12"
                          sm="12"
                          md="12"
                        >
                          <v-text-field
                            label="First name"
                            persistent-hint
                            v-model="applicant.firstname"
                            :error-messages="firstnameErrors + applicantError.firstname"
                            @input="$v.applicant.firstname.$touch() + (applicantError.firstname = []) + (validateFields_form1())"
                            @blur="$v.applicant.firstname.$touch() + (applicantError.firstname = [])"
                          ></v-text-field>
                        </v-col>

                        <v-col
                          cols="12"
                          sm="12"
                          md="12"
                        >
                          <v-text-field
                            label="Middle name"
                            hint="Note: Just put asterisk (*) if you have no middlename."
                            persistent-hint
                            v-model="applicant.middlename"
                            :error-messages="middlenameErrors + applicantError.middlename"
                            @input="$v.applicant.middlename.$touch() + (applicantError.middlename = []) + (validateFields_form1())"
                            @blur="$v.applicant.middlename.$touch() + (applicantError.middlename = [])"
                          ></v-text-field>
                        </v-col>

                        <v-col
                          cols="12"
                          sm="12"
                          md="12"
                        >
                          <v-text-field
                            label="Complete Address"
                            hint="St./Brgy, Municipality, Province"
                            persistent-hint
                            v-model="applicant.address"
                            :error-messages="addressErrors + applicantError.address"
                            @input="$v.applicant.address.$touch() + (applicantError.address = []) + (validateFields_form1())"
                            @blur="$v.applicant.address.$touch() + (applicantError.address = [])"
                          ></v-text-field>
                        </v-col>

                        <v-col
                          xs="12"
                          sm="12"
                          md="12"
                          lg="6"
                        >
                          <v-text-field
                            label="Birthday"
                            type="date"
                            prepend-icon="mdi-calendar"
                            v-model="applicant.birthdate"
                            :error-messages="birthdateErrors + applicantError.birthdate"
                            @input="(bdate_value_change()) + $v.applicant.birthdate.$touch() + (applicantError.birthdate = []) + (validateFields_form1())"
                            @blur="$v.applicant.birthdate.$touch() + (applicantError.birthdate = [])"
                          ></v-text-field>
                        </v-col>

                        <v-col
                          xs="12"
                          sm="12"
                          md="12"
                          lg="6"
                        >
                          <v-text-field
                            label="Age"
                            v-model="applicant.age"
                            disabled
                          ></v-text-field>
                        </v-col>

                        <v-col
                          cols="12"
                          sm="12"
                          md="12"
                        >
                          <v-text-field
                            label="Contact No."
                            v-model="applicant.contact_no"
                            :rules="rules"
                            counter="11"
                            :error-messages="contactNoErrors + applicantError.contact_no"
                            @input="$v.applicant.contact_no.$touch() + (applicantError.contact_no = []) + (validateFields_form1())"
                            @blur="$v.applicant.contact_no.$touch() + (applicantError.contact_no = [])"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                          <v-text-field
                            label="Email"
                            v-model="applicant.email"
                            :error-messages="emailErrors + applicantError.email"
                            @input="$v.applicant.email.$touch() + (applicantError.email = []) + (validateFields_form1())"
                            @blur="$v.applicant.email.$touch() + (applicantError.email = [])"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                          <v-autocomplete
                            v-model="applicant.educ_attain"
                            :items="['College Graduate', 'College Undergraduate', 'Senior Highschool Graduate', 'Highschool Graduate', 'Vocational/TESDA Graduate']"
                            label="Educational Attainment"
                            :error-messages="educAttainErrors + applicantError.educ_attain"
                            @input="$v.applicant.educ_attain.$touch() + (applicantError.educ_attain = []) + (validateFields_form1())"
                            @blur="$v.applicant.educ_attain.$touch() + (applicantError.educ_attain = [])"
                          ></v-autocomplete>
                        </v-col>

                        <v-col
                          cols="12"
                          sm="12"
                          md="12"
                        >
                          <v-text-field
                            label="Course"
                            v-model="applicant.course"
                            :error-messages="courseErrors + applicantError.course"
                            @input="$v.applicant.course.$touch() + (applicantError.course = []) + (validateFields_form1())"
                            @blur="$v.applicant.course.$touch() + (applicantError.course = [])"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                          <v-text-field
                            label="School graduated"
                            v-model="applicant.school_grad"
                            :error-messages="schoolGradErrors + applicantError.school_grad"
                            @input="$v.applicant.school_grad.$touch() + (applicantError.school_grad = []) + (validateFields_form1())"
                            @blur="$v.applicant.school_grad.$touch() + (applicantError.school_grad = [])"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                          <v-autocomplete
                            v-model="applicant.how_learn"
                            :items="['Addessa Website', 'Addessa FB Page', 'PESO FB Page', 'Now hiring FB Group Page', 'Local Government FB Page', 'Others']"
                            label="How did you learn about job vacancy?"
                            :error-messages="howLearnErrors + applicantError.how_learn"
                            @input="$v.applicant.how_learn.$touch() + (applicantError.how_learn = []) + (validateFields_form1())"
                            @blur="$v.applicant.how_learn.$touch() + (applicantError.how_learn = [])"
														@change="handleChange"
                          ></v-autocomplete>

													<v-text-field
                            label="(Please specify here.)"
                            v-model="applicant.how_learn_2"
                            :error-messages="howLearn2Errors"
                            @input="$v.applicant.how_learn_2.$touch() + (validateHowlearn2_form1())"
                            @blur="$v.applicant.how_learn_2.$touch()"
														v-if="hide_howlearn_2"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                          <v-fade-transition mode="out-in">
                            <v-btn
                              color="primary"
                              @click="e1 = 2"
                              v-if="continue_1"
                            >
                              Continue
                            </v-btn>
                          </v-fade-transition>  
                        </v-col>
                      </v-row>
                    </v-stepper-content>

                    <v-stepper-content step="2">
                      <v-row>
                        <v-col cols="12">
                          <v-file-input
                            show-size
                            label="Please attach your resume here."
                            v-model="applicant.myFileInput"
                            hint=".docs, .docx, .pdf, .jpeg, .png, .jpg"
                            persistent-hint
                            accept=".pdf, .docs, .docx, .jpeg, .png, .jpg"
                            :error-messages="resumefileErrors + applicantError.file"
                            @change="$v.applicant.myFileInput.$touch() + (applicantError.file = []) + (validateFields_form2())"
                            @blur="$v.applicant.myFileInput.$touch() + (applicantError.file = [])"
                          ></v-file-input>
                        </v-col>
                      </v-row>
                      <v-col cols="12" class="mt-5">
                        <v-fade-transition mode="out-in">
                          <v-btn
                            color="primary"
                            @click="e1 = 3"
                            v-if="continue_2"
                          >
                            Continue
                          </v-btn>
                        </v-fade-transition>  

                        <v-btn text @click="e1 = 1">
                          Back
                        </v-btn>
                      </v-col>  
                    </v-stepper-content>

                    <v-stepper-content step="3">
                      <v-row>
                        <v-col cols="12" class="mt-5">
                          <v-container>
                            <v-checkbox v-model="checkbox">
                              <template v-slot:label>
                                <span class="mt-2">I hereby certify that the encoded &#38; attached information is true and correct.</span>
                              </template>
                            </v-checkbox>
                          </v-container>
                          <v-btn text @click="(e1 = 2), (checkbox = false)">
                            Back
                          </v-btn>
                        </v-col>
                      </v-row>  
                    </v-stepper-content>
                  </v-stepper-items>
                </v-stepper>
              </v-container>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-fade-transition mode="out-in">
                <v-btn
                  color="primary"
                  class="submit_btn"
                  :loading="loader_dialog"
                  :disabled="loader_dialog"
                  @click="submit_application()"
                  v-if="checkbox"
                >
                  Submit Form
                </v-btn>
              </v-fade-transition>  
              <v-btn
                color="blue darken-1"
                text
                @click="reset()"
              >
                Close
              </v-btn>
            </v-card-actions>
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
              <p class="text-center pt-2" v-if="saving_loader_text">
                Please stand by...
              </p>
              <p class="text-center pt-2" v-else>
                Refreshing the records...
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

    <!-- About Us  -->
    <v-container v-if="about_us" class="mt-5" 
      data-aos="flip-left"
      data-aos-duration="2000"
      style="font-family: 'Lato', sans-serif;"
    >
      <div class="flex column">
        <v-card class="pt-2 pr-2 pl-2 pb-2">
          <h2 class="text-center font-weight-black" 
            data-aos="fade-down"
            data-aos-duration="2000"
          >ABOUT US</h2>

          <v-list>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  <h4 class="font-weight-bold">History</h4>
                </v-list-item-title>
                <v-list-item>
                  <p style="text-align: left;">
                    Under a sole-proprietorship, ADDS Merchandising was established in October 27, 
                    1978 originally selling ready-to-wear merchandise, toys, and ladies accessories. 
                    The first store was located in the old Urdaneta Public Market which then branched to 
                    Alexander Street, Urdaneta City in January 20, 1981. It was then that the business name 
                    became ADDES Merchandising that started also to sell appliances and furniture. When the 
                    appliances and furniture business took off and far exceeded the sales of ready-to-wear merchandise, 
                    the latter was dropped from the business portfolio which consequently leads to the change in business 
                    name to ADDESA Trading in 1985. In December 19, 1994, the company was incorporated and the business 
                    name became ADDESSA CORPORATION. The name has been adapted ever since. The company’s expansion programs were 
                    the reason for the founding of three companies, EASY to Own Appliance Inc., PAN APPLIANCE CORPORATION, and METRO ILOCOS APPLIANCE INCORPORATED.
                  </p>
                </v-list-item>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <v-list>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  <h4 class="font-weight-bold">Vision</h4>
                </v-list-item-title>
                <v-list-item>
                  <p style="text-align: left;">
                    To emerge as the country’s local household name in providing quality and easy to own appliances, 
                    electronics and furniture making every home a comfortable and healthy place to live in.
                  </p>
                </v-list-item>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <v-list>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  <h4 class="font-weight-bold">Mission</h4>
                </v-list-item-title>
                <v-list-item>
                  <ul style="list-style-type:none;">
                    <li>✓ Devoted to provide personalized service by understanding individual customer needs.</li>
                    <li>✓ Committed to expand the organization to serve more customers and thus create more jobs to the community.</li>
                  </ul>
                </v-list-item>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <v-list>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  <h4 class="font-weight-bold">Corporate Values</h4>
                </v-list-item-title>
                <v-list-item>
                  <ol>
                    <li>Love for the service</li>
                    <li>Sincerity and honesty</li>
                    <li>Teamwork</li>
                    <li>Excellence</li>
                    <li>Equality &#x26; fairness</li>
                  </ol>
                </v-list-item>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>
      </div>
    </v-container>

    <!-- Careers  -->
    <v-container v-if="careers" class="mt-5" 
      data-aos="flip-left"
      data-aos-duration="2000"
    >
      <div class="flex column">
        <v-card>
          <h2 class="text-center font-weight-black pt-5" 
            data-aos="fade-down"
            data-aos-duration="2000"
            style="font-family: 'Lato', sans-serif;"
          >Careers</h2>

          <v-list style="font-family: 'Lato', sans-serif;">
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  <h4 class="font-weight-bold">Be part of our success.</h4>
                </v-list-item-title>
                <v-list-item>
                  <p style="text-align: left;">
                    Now is the time for you to join our team, and be part of our exciting growth journey across the community, reaching every Filipino family.
                  </p>
                </v-list-item>
              </v-list-item-content>
            </v-list-item>
          </v-list>

          <v-container style="font-family: 'Lato', sans-serif;">
            <v-expansion-panels focusable 
              data-aos="fade-down"
              data-aos-duration="2000"
            >
              <v-expansion-panel 
                v-for="(job_vacancy, index) in all_job_vacancies"
                :key="index"
              >
                <v-expansion-panel-header>
                  <span class="font-weight-bold">{{ job_vacancy.position_name }} 
                    <br>
                    <br>
                    <span class="font-weight-light">ADDESSA {{ job_vacancy.branch_name }}</span>
                  </span>
                </v-expansion-panel-header>
                <v-expansion-panel-content>
                  <v-list>
                    <v-list-item>
                      <v-list-item-content>
                        <v-list-item-title>
                          <h4 class="font-weight-bold">Job Details</h4>
                        </v-list-item-title>
                        <v-list-item>
                          <h5>Qualifications:</h5>
                        </v-list-item>
                        <v-list-item>
                          <p style="text-align: left;" v-html="job_vacancy.qualifications"></p>
                        </v-list-item>
                        <v-list-item>
                          <v-btn
                            color="#f44336"
                            outlined
                            class="font-weight-bold"
                            @click="(dialog_apply_now = true), getJobDescription(job_vacancy.id)"
                          >
                            Apply Now!
                          </v-btn>
                        </v-list-item>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list>
                </v-expansion-panel-content>
              </v-expansion-panel>
            </v-expansion-panels>
          </v-container>
           
          <v-list>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>For more info, please contact our HR Department:</v-list-item-title>
                  <v-list-item-group>
                    <ul>
                      <li><p>(Globe) +63 927 386 9363</p></li>
                      <li><p>(Sun) +63 933 850 1568</p></li>
                      <li><p>Or send your resume at princess_baniqued@addessa.com.ph</p></li>
                    </ul>
                  </v-list-item-group>  
              </v-list-item-content>
            </v-list-item>  
          </v-list>
        </v-card>
      </div>
    </v-container>

    <!-- Footer -->
    <v-footer
      dark
      padless
      class="mt-5"
      data-aos="fade-up"
      data-aos-duration="2000"
    >
      <v-card
        flat
        tile
        class="lighten-1 white--text text-center"
        style="background-color: #1b1a24; width: 100%"
      >
        <v-card-text class="white--text mt-5">
          <v-row class="mt-5">
            <v-col cols="6">
              <v-row>
                <v-col cols="12">
                  <v-img 
                    src="img/addessa.jpg" 
                    class="mb-4" 
                    width="50px" height="auto" 
                    style="border-radius: 50%; display:block; margin:0 auto;"
                  />
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12" style="font-weight: lighter;">
                  <p style="text-align: center;">
                    <span style="font-weight: bold;">Addessa Recruitment Portal</span>
                  </p>
                  <p>For more info, please contact our HR Department:</p>
                  <p>
                    <ul>
                      <li>(Globe) +63 927 386 9363</li>
                      <li>(Sun) +63 933 850 1568</li>
                      <li>Or send your resume at recruitment@addessa.com</li>
                    </ul>
                  </p>
                  <v-btn
                    class="mx-4 white--text remove_href"
                    icon
                    href="https://www.facebook.com/AddessaEasyInstallment/"
                  >
                    <v-icon size="24px">
                      mdi-facebook
                    </v-icon>
                  </v-btn>
                  <v-btn
                    class="mx-4 white--text remove_href"
                    icon
                    href="https://www.youtube.com/user/ADDESSACorporation"
                  >
                    <v-icon size="24px">
                      mdi-youtube
                    </v-icon>
                  </v-btn>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="6">
              <v-row>
                <v-col cols="12">
                  <v-icon size="40" class="mb-4">mdi-web</v-icon>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="12">
                  <v-btn text style="font-weight: lighter;" @click="home_tab()">HOME</v-btn>
                </v-col>
                <v-col cols="12">
                   <v-btn text style="font-weight: lighter;" @click="about_tab()">ABOUT US</v-btn>
                </v-col>
                <v-col cols="12">
                  <v-btn text style="font-weight: lighter;" @click="careers_tab()">CAREERS</v-btn>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text class="white--text">
          <strong>Addessa Corporation</strong>
          <p>All Rights Reserved. Copyright {{ new Date().getFullYear() }}</p>
        </v-card-text>
      </v-card>
    </v-footer>
  </v-card>
</template>
<style scoped>

  @media only screen and (min-width: 375px) {
    .top_text{
      font-size: 16px;
    }
  }

  @media only screen and (min-width: 412px) {
    .top_text{
      font-size: 20px;
    }
  }

  @media only screen and (min-width: 600px) {
    .top_text{
      font-size: 70%;
    }
  }
  @media only screen and (min-width: 1000px) {
    .top_text{
       font-size: 100%;
    }
  }
  @media only screen and (min-width: 1200px) {
    .top_text{
       font-size: 100%;
    }
  }

  #card_hover{
    box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.3);
    transition: box-shadow 500ms;
  }

  #card_hover:hover{
    box-shadow: 0 10px 50px 0 rgba(0, 0, 0, 0.5);
  }

  .fixed-bar {
    position: sticky;
    position: -webkit-sticky; /* for Safari */
    z-index: 2;
  }

  .fixed-bottombar {
    position: sticky;
    position: -webkit-sticky; /* for Safari */
    top: 0em;
    z-index: 1;
  }

  .remove_href{
    text-decoration: none;
  }
</style>
<script>
import axios from "axios";
import { validationMixin } from "vuelidate";
import { required, minLength, email } from "vuelidate/lib/validators";
import AOS from 'aos';
import 'aos/dist/aos.css';

export default {

  mixins: [validationMixin],

  validations: {
    applicant:{
      lastname: { required },
      firstname: { required },
      middlename: { required },
      address: { required },
      birthdate: { required },
      contact_no: { required, minLength: minLength(11) },
      email: { required, email },
      educ_attain: { required },
      course: { required },
      school_grad: { required },
      how_learn: { required },
      how_learn_2: { required },
      myFileInput: { required },
    },
  },

  data() {
    return {

      overlay: false,
      loading: false,
      job_cards: false,

      home: true,
      about_us: false,
      careers: false, 

      search_textfield: "",

      saving_loader_text: false,
      loader_dialog: false,
      dialog_apply_now: false,
      dialog_submit_now: false,
      submit_dialog: false,
      e1: 1,
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

      job_vac_id: "",
      file: "",
      applicant: {
        lastname: "",
        firstname: "",
        middlename: "",
        address: "",
        birthdate: "",
        age: "",
        contact_no: "",
        email: "",
        educ_attain: "",
        course: "",
        school_grad: "",
        how_learn: "",
        myFileInput: []
      },
      bdate_value: "",
      

      rules: [v => v.length <= 11 || 'Phone number is max 11 digits only.'],

      applicantError: {
        lastname: [],
        firstname: [],
        middlename: [],
        address: [],
        birthdate: [],
        contact_no: [],
        email: [],
        educ_attain: [],
        course: [],
        school_grad: [],
        how_learn: [],
        file: []
      },

      job_vacancies: [],
      all_job_vacancies: [],
      get_job_vacancy_lists: [],

      jobdesc_card: false,
      overview_card: false,
      jobtop_card: false,

      job_title: "",
      description: "",
      branch: "",
      qualifications: "",
      days: "",
      hours: "",
      minutes: "",
      educ_attain: "",

      continue_1: false,
      continue_2: false,

      how_learn_2: "",
			hide_howlearn_2: false,
    }
  },

  methods: {

    home_tab(){
      // tabs active
      this.home = true;
      this.about_us = false;
      this.careers = false;

      this.loading = true;
      setTimeout(() => (
        this.loading = false
      ), 3000);
    },

    about_tab(){
      // tabs active
      this.home = false;
      this.about_us = true;
      this.careers = false;

      this.loading = true;
      setTimeout(() => (
        this.loading = false
      ), 3000);
    },

    careers_tab(){
      // tabs active
      this.home = false;
      this.about_us = false;
      this.careers = true;

      this.loading = true;
      setTimeout(() => (
        this.loading = false
      ), 3000);
    },

    hide_cards(){
      this.jobdesc_card = false;
      this.overview_card = false;
      this.jobtop_card = false;
    },

    reset(){
      this.applicant.lastname = "";
      this.applicant.firstname = "";
      this.applicant.middlename = "";
      this.applicant.address = "";
      this.applicant.birthdate = "";
      this.applicant.age = "";
      this.applicant.contact_no = "";
      this.applicant.email = "";
      this.applicant.educ_attain = "";
      this.applicant.course = "";
      this.applicant.school_grad = "";
      this.applicant.how_learn = "";
      this.applicant.how_learn_2 = "";
      this.applicant.myFileInput = [];
      this.dialog_submit_now = false;

      this.$v.$reset();

      this.hide_howlearn_2 = false;
      this.checkbox = false;

      this.continue_1 = false;
      this.continue_2 = false;
    },

    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },

    getJobVacancies(){
      axios.get("/api/public_api/job_vacancy_public").then(
        (response) => {
          this.job_vacancies = response.data.job_vacancy_lists;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    getAllJobVacancies(){
      axios.get("/api/public_api/all_job_vacancy_public").then(
        (response) => {
          this.job_vacancies = response.data.job_vacancy_lists;
          this.all_job_vacancies = response.data.job_vacancy_lists;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    getAllJobVacanciesCareers(){

      axios.get("/api/public_api/all_job_vacancy_public").then(
        (response) => {
          this.all_job_vacancies = response.data.job_vacancy_lists;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    show_all_job_vacancy_cards(){

      this.saving_loader_text = false;
      this.loader_dialog = true;
      this.job_cards = false;
      setTimeout(() => (
        this.loader_dialog = false,
        this.job_cards = true
      ), 3000);
    },

    search_job_vacancy(){

      let formData = new FormData();
      formData.append('search_textfield', this.search_textfield);

      axios.post("/api/public_api/search_job_vacancy_public", formData, {
        headers: {
          'Content-Type': 'multipart/form-data' 
        }
      }).then(
        (response) => {
          this.job_vacancies = response.data.job_vacancy_lists;
        },
        (error) => {
          console.log(error);
          this.isUnauthorized(error);
        }
      );
    },

    getJobDescription(id){

      this.overlay = true;

      const job_vac_id = id;

      let formData = new FormData();
      formData.append('job_vac_id', job_vac_id);
      axios.post("/api/public_api/get_job_vacancy_public", formData).then(
        (response) => {
          const data = response.data.get_job_vacancy_lists[0];

          this.job_title = data.position_name;
          this.description = data.description;
          this.branch = data.branch_name;
          this.qualifications = data.qualifications;
          this.days = data.days;
          this.hours = data.hours;
          this.minutes = data.minutes;
          this.educ_attain = data.educ_attain;

          // for form_submit
          this.job_vac_id = data.id;
          
          this.jobtop_card = true;

          //Waste 2 seconds
          setTimeout(() => {
            this.overlay = false;

            this.jobdesc_card = true;
            this.overview_card = true;
          }, 1000)
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    submit_application(){

      this.loader_dialog = true;
      this.saving_loader_text = true;
      
      this.applicantError = {
        lastname: [],
        firstname: [],
        middlename: [],
        address: [],
        birthdate: [],
        contact_no: [],
        email: [],
        educ_attain: [],
        course: [],
        school_grad: [],
        how_learn: [],
        how_learn_2: [],
        file: []
      };

      const how_learn_selected = this.applicant.how_learn;

      let formData = new FormData();
      formData.append('jobvacancy_id', this.job_vac_id);
      formData.append('lastname', this.applicant.lastname);
      formData.append('firstname', this.applicant.firstname);
      formData.append('middlename', this.applicant.middlename);
      formData.append('address', this.applicant.address);
      formData.append('birthdate', this.applicant.birthdate);
      formData.append('age', this.applicant.age);
      formData.append('contact_no', this.applicant.contact_no);
      formData.append('email', this.applicant.email);
      formData.append('educ_attain', this.applicant.educ_attain);
      formData.append('course', this.applicant.course);
      formData.append('school_grad', this.applicant.school_grad);

      if(how_learn_selected === 'Others'){
        formData.append('how_learn', this.applicant.how_learn_2);
      }else{
        formData.append('how_learn', this.applicant.how_learn);
      }
      
      formData.append('file', this.applicant.myFileInput);

      axios.post("/api/public_api/submit_application", formData, {
        headers: {
          'Content-Type': 'multipart/form-data' 
        }
      }).then(
        (response) => {
          if(response.data.success){

            // send data to Sockot.IO Server
            this.$socket.emit("sendData", { action: "applicant-submit" });

            this.loader_dialog = false;

            this.$toaster.success('You have successfully submitted your job application.', {
              timeout: 2000
            });

            this.e1 = 1;
            // this.reset();
          }else if(response.data.duplicate){

            this.$toaster.error('You have already applied to this position, please select another position to apply.', {
              timeout: 7000
            });

            this.loader_dialog = false;
            this.e1 = 1;
            this.reset();
          }else{

            this.e1 = 1;
            this.checkbox = false;
            this.loader_dialog = false;

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
    },

    websocket() {
      // Socket.IO fetch data
      this.$options.sockets.sendData = (data) => {
        let action = data.action;
        if (action == "update-jobvacancy-records") {
          this.getJobVacancies();
        }
      };
    },

		handleChange(){
			const how_learn_selected = this.applicant.how_learn;

			if(how_learn_selected == "Others"){
				this.hide_howlearn_2 = true;
        this.continue_1 = false;

        const how_learn2 = this.applicant.how_learn_2;
        if(how_learn2){
          this.continue_1 = true;
        }
			}else{
				this.hide_howlearn_2 = false;
        this.continue_1 = true;
			}
		},

    validateFields_form1(){
      let fieldnames = Object.keys(this.applicant);

      this.continue_1 = true;

      fieldnames.forEach(value => {
        if(value != 'myFileInput'){
          if(!this.applicant[value]){
            this.continue_1 = false;
          }
        }
      });
    },

    validateHowlearn2_form1(){
      const how_learn2 = this.applicant.how_learn_2;
      
      if(how_learn2){
        this.continue_1 = true;
      }else{
        this.continue_1 = false;
      }
    },

    validateFields_form2(){
      let myFileInput = this.applicant.myFileInput;

      this.continue_2 = true;

      if(!myFileInput){
        this.continue_2 = false;
      }
    },

    bdate_value_change(){
      this.bdate_value = this.applicant.birthdate;
    }
  },

  watch:{

    bdate_value() {
      let currentDate = new Date();
      let birthDate = new Date(this.bdate_value);

      let difference = currentDate - birthDate;
      let age = Math.floor(difference/31557600000);

      this.applicant.age = age;
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
        errors.push("Address is required.");
      return errors;
    },

    birthdateErrors() {
      const errors = [];
      if (!this.$v.applicant.birthdate.$dirty) return errors;
      !this.$v.applicant.birthdate.required &&
        errors.push("Birthday is required.");
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
      !this.$v.applicant.email.required &&
        errors.push("Email is required.");

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

    schoolGradErrors() {
      const errors = [];
      if (!this.$v.applicant.school_grad.$dirty) return errors;
      !this.$v.applicant.school_grad.required &&
        errors.push("School graduated is required.");
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
      if (!this.$v.applicant.myFileInput.$dirty) return errors;
      !this.$v.applicant.myFileInput.required &&
        errors.push("File is required.");
      return errors;
    },

    howLearn2Errors() {
      const errors = [];
      if (!this.$v.applicant.how_learn_2.$dirty) return errors;
      !this.$v.applicant.how_learn_2.required &&
        errors.push("This field is required.");
      return errors;
    },
  },
  created(){
    AOS.init();
  },

  mounted() {
    this.websocket();

    this.getJobVacancies();
    this.getAllJobVacanciesCareers();

    this.home = true;
    this.about_us = false;
    this.careers = false;

    this.jobdesc_card = false;
    this.overview_card = false;
    this.jobtop_card = false;

    this.loading = true;
    this.job_cards = true;

    setTimeout(() => (
      this.loading = false
    ), 3000);
  },
};
</script>