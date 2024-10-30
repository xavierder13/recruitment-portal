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
                hint="Branch name"
                persistent-hint
                outlined
                v-on:keyup="search_branches()"
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
          <v-col cols="12" xs="12" sm="12" md="12" lg="12" data-aos="fade-up" data-aos-duration="2000">
            <v-row class="mt-5">
              <v-col xs="12" sm="12" md="12" lg="12">
                <h3 class="text-center" 
                  style="font-weight: bold; color: #00244f; font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif" 
                  data-aos="fade-down" 
                  data-aos-duration="3000"
                >ADDESSA BRANCHES</h3>
              </v-col>
            </v-row>
          </v-col>  
        </v-row>
        <v-row>
          <v-overlay :value="overlay">
            <v-progress-circular
              indeterminate
              size="64"
            ></v-progress-circular>
          </v-overlay>
        </v-row>
        <v-row align="center" 
          data-aos="fade-down"
          data-aos-duration="2000"
        >
          <v-col xs="12" sm="12" md="3" lg="3"
            v-for="(branch, index) in paginated_branch"
            :key="index"
          >
            <v-scroll-y-transition mode="out-in">
              <v-card 
                id="card_hover" 
                class="text-center" 
                v-if="job_cards"
                style="font-family: Lato, sans-serif; min-height: 300px;"
              >
                <v-col md="12" class="ma-0 pt-6 pa-0">
                  <span>
                    <v-img src="img/card_bg.png" height="auto" width="50%" style="display:block; margin:0 auto;" />
                  </span>
                </v-col>  
                <v-col md="12" class="ma-0 pa-2">
                  <span style="display: block; font-weight: bold; font-size: 18px;">ADDESSA</span>
                  <h5 class="font-weight-black" style="color: #9d0a10; text-transform: uppercase;">{{ branch.name }}</h5>
                </v-col> 
                <v-col md="12">
                  <v-btn
                    color="#f44336"
                    outlined
                    class="font-weight-bold mt-2 mb-2"
                    @click="(dialog_apply_now = true), get_branch_job_vacancy(branch)"
                  >
                    Apply Now!
                  </v-btn>
                </v-col>
              </v-card>
            </v-scroll-y-transition>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-pagination
              v-model="pagination.page"
              :length="parseInt(this.page_length / 12)"
              circle
              @input="next"
              v-if="paginated_branch.length"
            ></v-pagination>
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
                <v-toolbar-title style="font-size: 15px; font-family: Lato, sans-serif;">APPLICATION DETAILS</v-toolbar-title>
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
                >ADDESSA</h1>
                <h2 class="font-weight-light" 
                  data-aos="fade_down" 
                  data-aos-duration="2000"
                >{{ this.branch }}</h2>
              </v-container>
              
              <v-container style="font-family: 'Lato', sans-serif;">
                <!-- TOP -->
                <v-row>
                  <v-col cols="12">
                    <v-container style="max-width: 100%; margin-top: 10px;">
                      <v-card>
                        <v-row style="padding: 1.750em">
                          <v-col xs="12" sm="12" md="12" lg="12">
                            <h3 class="font-weight-thin">LIST OF POSITIONS</h3>
                            <br>
                            <v-chip-group
                              v-model="selection"
                              active-class="green darken-4 white--text"
                              column
                            >
                              <v-chip
                                class="mt-2 ma-1 font-weight-bolder"
                                :ripple="true"
                                v-for="(job_vacancy, index) in branch_job_vacancies"
                                :key="index"
                                @click="get_job_details(job_vacancy.id)"
                              >
                                {{ job_vacancy.position_name }}
                              </v-chip>
                            </v-chip-group>
                          </v-col>
                        </v-row>
                      </v-card>
                    </v-container>
                  </v-col>
                </v-row>
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
                            <h5 class="font-weight-bold mt-5">POSITION:</h5>
                            <h6>
                              <v-chip color="#fb8c00" outlined dark><span v-html="this.job_title"></span></v-chip>
                            </h6>
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
                              <span>{{ educ_attain }}</span>
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
                          @click="(dialog_submit_now = true), (stepper = 1)"
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
            max-width="1200px"
          >
            <v-card>
              <v-card-title style="background-color: #f44336; color: #ffffff;" class="mb-0">
                <span class="text-h5">Applicant Details</span>
                <v-spacer></v-spacer>
                <v-btn
                  icon
                  dark
                  @click="reset()"
                >
                  <v-icon>mdi-close-circle</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="px-0 mb-0">
                <v-stepper v-model="stepper" class="mt-2">
                  <v-stepper-header>
                    <v-stepper-step :complete="stepper > 1" step="1">
                      Personal Details
                    </v-stepper-step>
                    <v-divider></v-divider>
                    <v-stepper-step :complete="stepper > 2" step="2">
                      Attach Resume
                    </v-stepper-step>
                    <v-divider></v-divider>
                    <v-stepper-step :complete="stepper > 3" step="3">
                      Submit
                    </v-stepper-step>
                  </v-stepper-header>
                  <v-stepper-items>
                    <v-stepper-content step="1" class="px-0">
                      <v-row>
                        <v-col class="px-9 mb-6">
                          <span class="font-italic font-weight-bold red--text">Note: All fields with asterisk(*) are required</span>
                        </v-col>
                      </v-row>
                      <v-card class="mt-2 mx-4">
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
                                @input="$v.applicant.lastname.$touch() + (applicantError.lastname = []) + (validateFields_form1())"
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
                                @input="$v.applicant.firstname.$touch() + (applicantError.firstname = []) + (validateFields_form1())"
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
                                @input="$v.applicant.address.$touch() + (validateFields_form1())"
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
                                @input="$v.applicant.address2.$touch() + (applicantError.address2 = []) + (validateFields_form1())"
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
                                @input="$v.applicant.birth_place.$touch() + (applicantError.birth_place = []) + (validateFields_form1())"
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
                            <!-- <v-col
                              cols="12"
                              xs="12"
                              sm="6"
                              md="6"
                              lg="6"
                              class="mb-0 pb-0"
                            >
                              <v-text-field
                                label="Age"
                                v-model="applicant.age"
                                readonly
                              ></v-text-field>
                            </v-col> -->
                          
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
                                @input="$v.applicant.gender.$touch() + (applicantError.gender = []) + (validateFields_form1())"
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
                                @input="$v.applicant.civil_status.$touch() + (applicantError.civil_status = []) + (validateFields_form1())"
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
                                :rules="rules"
                                counter="11"
                                :error-messages="contactNoErrors + applicantError.contact_no"
                                @input="$v.applicant.contact_no.$touch() + (applicantError.contact_no = []) + (validateFields_form1())"
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
                                @input="$v.applicant.email.$touch() + (applicantError.email = []) + (validateFields_form1())"
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
                                @input="$v.applicant.citizenship.$touch() + (applicantError.citizenship = []) + (validateFields_form1())"
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
                                @input="$v.applicant.religion.$touch() + (applicantError.religion = []) + (validateFields_form1())"
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
                                @input="$v.applicant.height.$touch() + (applicantError.height = []) + (validateFields_form1())"
                                @blur="$v.applicant.height.$touch() + (applicantError.height = [])"
                                @keypress="decNumValFilter()"
                              ></v-text-field>
                            </v-col>
                            <v-col class="mb-0 pb-0">
                              <v-text-field
                                label="Weight (kg) *"
                                v-model="applicant.weight"
                                :error-messages="weightErrors + applicantError.weight"
                                @input="$v.applicant.weight.$touch() + (applicantError.weight = []) + (validateFields_form1())"
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
                            <v-col
                              cols="12"
                              xs="12"
                              sm="4"
                              md="4"
                              lg="4"
                              class="mb-0 pb-0"
                            >
                              <v-autocomplete
                                v-model="applicant.how_learn"
                                :items="how_learn"
                                label="How did you learn about job vacancy? *"
                                :error-messages="howLearnErrors + applicantError.how_learn"
                                @input="$v.applicant.how_learn.$touch() + (applicantError.how_learn = []) + (validateFields_form1())"
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
                              class="mb-0 pb-0"
                              v-if="applicant.how_learn == 'Others'"
                            >
                              <v-text-field
                                label="(Please specify here.) *"
                                v-model="applicant.how_learn_2"
                                :error-messages="howLearn2Errors"
                                @input="$v.applicant.how_learn_2.$touch() + (validateFields_form1())"
                                @blur="$v.applicant.how_learn_2.$touch()"
                              ></v-text-field>
                            </v-col>
                            <!-- <v-col 
                              class="mb-0 pb-0"
                              cols="12"
                              xs="12"
                              sm="4"
                              md="4"
                              lg="4"
                            >
                              <v-autocomplete
                                v-model="applicant.educ_attain"
                                :items="educ_attains"
                                item-text="text"
                                item-value="value"
                                label="Educational Attainment *"
                                :error-messages="educAttainErrors + applicantError.educ_attain"
                                @input="$v.applicant.educ_attain.$touch() + (applicantError.educ_attain = []) + (validateEducAttain())"
                                @blur="$v.applicant.educ_attain.$touch() + (applicantError.educ_attain = [])"
                              ></v-autocomplete>
                            </v-col> -->
                          </v-row>
                        </v-card-text>
                      </v-card>
                      <!-- <v-row>
                      <v-col>
                        <v-divider  class="mb-0"></v-divider>
                      </v-col>
                      </v-row> -->
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
                                @input="$v.applicant.educ_attain.$touch() + (applicantError.educ_attain = []) + (validateFields_form1())"
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
                                    @input="$v.highschool.school.$touch() + validateFields_form1()"
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
                                    @input="$v.highschool.sy_start.$touch() + validateDate('highschool.sy_start') + validateFields_form1()"
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
                                    @input="$v.highschool.sy_end.$touch() + validateDate('highschool.sy_end') + validateFields_form1()"
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
                                    @input="$v.jr_highschool.school.$touch() + validateFields_form1()"
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
                                    @input="$v.jr_highschool.sy_start.$touch() + validateDate('jr_highschool.sy_start') + validateFields_form1()"
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
                                    @input="$v.jr_highschool.sy_end.$touch() + validateDate('jr_highschool.sy_end') + validateFields_form1()"
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
                                    @input="$v.sr_highschool.school.$touch() + validateFields_form1()"
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
                                    @input="$v.sr_highschool.strand.$touch() + validateFields_form1()"
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
                                    @input="$v.sr_highschool.sy_start.$touch() + validateDate('sr_highschool.sy_start') + validateFields_form1()"
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
                                    @input="$v.sr_highschool.sy_end.$touch() + validateDate('sr_highschool.sy_end') + validateFields_form1()"
                                    @blur="$v.sr_highschool.sy_end.$touch()"
                                  ></v-text-field>
                                </v-col>
                              
                                <!-- <v-col
                                  cols="12"
                                  xs="12"
                                  sm="4"
                                  md="4"
                                  lg="4"
                                >
                                  <v-text-field
                                    class="ma-0 pa-0"
                                    label="S.Y Attended *"
                                    v-model="sr_highschool.sy_attended"
                                    :error-messages="SRHSYErrors"
                                    @input="$v.sr_highschool.sy_attended.$touch() + (validateEducAttain())"
                                    @blur="$v.sr_highschool.sy_attended.$touch()"
                                  ></v-text-field>
                                </v-col> -->
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
                                    @input="$v.college.school.$touch() + validateFields_form1()"
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
                                    @input="$v.college.course.$touch() + validateFields_form1()"
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
                                    @input="$v.college.sy_start.$touch() + validateDate('college.sy_start') + validateFields_form1()"
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
                                    @input="$v.college.sy_end.$touch() + validateDate('college.sy_end') + validateFields_form1()"
                                    @blur="$v.college.sy_end.$touch()"
                                  ></v-text-field>
                                </v-col>
                                <!-- <v-col
                                  class="my-2 py-0"
                                  cols="12"
                                  xs="12"
                                  sm="6"
                                  md="6"
                                  lg="3"
                                >
                                  <v-text-field
                                    class="ma-0 pa-0"
                                    label="S.Y Attended *"
                                    v-model="college.sy_attended"
                                    :error-messages="collegeSYErrors"
                                    @input="$v.college.sy_attended.$touch() + (validateEducAttain())"
                                    @blur="$v.college.sy_attended.$touch()"
                                  ></v-text-field>
                                </v-col> -->
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

                            <!-- <template v-if="applicant.educ_attain === 4">
                              <v-row>
                                <v-col>
                                  <span class="text-h6">
                                    <strong>Graduate School</strong> 
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
                                    v-model="graduate_school.school"
                                    :error-messages="gradSchoolErrors"
                                    @input="$v.graduate_school.school.$touch() + (validateEducAttain())"
                                    @blur="$v.graduate_school.school.$touch()"
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
                                    v-model="graduate_school.course"
                                    :error-messages="gradCourseErrors"
                                    @input="$v.graduate_school.course.$touch() + (validateEducAttain())"
                                    @blur="$v.graduate_school.course.$touch()"
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
                                    label="S.Y Attended *"
                                    v-model="graduate_school.sy_attended"
                                    :error-messages="gradSYErrors"
                                    @input="$v.graduate_school.sy_attended.$touch() + (validateEducAttain())"
                                    @blur="$v.graduate_school.sy_attended.$touch()"
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
                                    v-model="graduate_school.honors"
                                  ></v-text-field>
                                </v-col>
                              </v-row>
                            </template> -->
                            
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
                                    @input="$v.vocational_school.school.$touch() + validateFields_form1()"
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
                                    @input="$v.vocational_school.course.$touch() + validateFields_form1()"
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
                                    @input="$v.vocational_school.sy_start.$touch() + validateDate('vocational_school.sy_start') + validateFields_form1()"
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
                                    @input="$v.vocational_school.sy_end.$touch() + validateDate('vocational_school.sy_end') + validateFields_form1()"
                                    @blur="$v.vocational_school.sy_end.$touch()"
                                  ></v-text-field>
                                </v-col>
                                <!-- <v-col
                                  class="my-2 py-0"
                                  cols="12"
                                  xs="12"
                                  sm="6"
                                  md="6"
                                  lg="3"
                                >
                                  <v-text-field
                                    class="ma-0 pa-0"
                                    label="S.Y Attended *"
                                    v-model="vocational_school.sy_attended"
                                    :error-messages="vocSYErrors"
                                    @input="$v.vocational_school.sy_attended.$touch() + (validateEducAttain())"
                                    @blur="$v.vocational_school.sy_attended.$touch()"
                                  ></v-text-field>
                                </v-col> -->
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
                      
                      <!-- <v-row>
                        <v-col>
                          <v-divider class="my-0"></v-divider>
                        </v-col>
                      </v-row> -->

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
                              <!-- <v-col 
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
                                ></v-text-field>
                              </v-col> -->
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
                                  @input="validateFields_form1()"
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
                                  @input="validateFields_form1"
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
                      
                      <!-- <v-row>
                        <v-col>
                          <v-divider class="my-0"></v-divider>
                        </v-col>
                      </v-row> -->
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
                                  @input="validateReference(i, 'name') + validateFields_form1()"
                                  @blur="validateReference(i, 'name') + validateFields_form1()"
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
                                  @input="validateReference(i, 'address') + validateFields_form1()"
                                  @blur="validateReference(i, 'address') + validateFields_form1()"
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
                                  @input="validateReference(i, 'contact') + validateFields_form1()"
                                  @blur="validateReference(i, 'contact') + validateFields_form1() "
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
                      <v-row class="mt-2 ml-3">
                        <v-col>
                          <span class="font-italic font-weight-bold red--text">Note: All fields with asterisk(*) are required</span>
                        </v-col>
                      </v-row>
                      <v-row class="ml-3">
                        <v-col>
                          <v-fade-transition mode="out-in">
                            <v-btn
                              color="primary"
                              @click="stepper = 2"
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
                        <v-col cols="12" sm="12" md="8" lg="4" xl="4" class="my-4 py-0">
                          <v-file-input
                            show-size
                            label="Please attach your resume here."
                            v-model="applicant.myFileInput"
                            hint=".docs, .docx, .pdf, .jpeg, .png, .jpg"
                            persistent-hint
                            accept=".pdf, .docs, .docx, .jpeg, .png, .jpg"
                            :error-messages="resumefileErrors + applicantError.file"
                            @change="$v.applicant.myFileInput.$touch() + (applicantError.file = []) + (validateFile())"
                            @blur="$v.applicant.myFileInput.$touch() + (applicantError.file = [])"
                          ></v-file-input>
                        </v-col>
                      </v-row>
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
                        <v-col cols="12" class="my-4 py-0">
                          <v-fade-transition mode="out-in">
                            <v-btn
                              class="mr-1"
                              color="primary"
                              @click="stepper = 3"
                              v-if="continue_2"
                            >
                              Continue
                            </v-btn>
                          </v-fade-transition>  

                          <v-btn @click="stepper = 1">
                            Back
                          </v-btn>
                        </v-col>  
                      </v-row>
                    </v-stepper-content>

                    <v-stepper-content step="3">
                      <v-row>
                        <v-col cols="12" class="my-4 py-0">
                          <v-container>
                            <v-checkbox v-model="checkbox">
                              <template v-slot:label>
                                <span class="mt-2">I hereby certify that the encoded &#38; attached information is true and correct.</span>
                              </template>
                            </v-checkbox>
                          </v-container>
                          <v-btn @click="(stepper = 2), (checkbox = false)">
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
                    v-if="checkbox"
                  >
                    Submit Form
                  </v-btn>
                </v-fade-transition>  
                <v-btn
                  class="mb-3 mr-6"
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
              style="font-family: Lato, sans-serif;"
            >Careers</h2>

            <v-list style="font-family: Lato, sans-serif;">
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
                  v-for="(branch, index) in all_branches"
                  :key="index"
                  @click="get_branch_job_vacancy(branch)"
                >
                  <v-expansion-panel-header>
                    <span class="font-weight-lighter">ADDESSA <span class="font-weight-bold">{{ branch.name }}</span></span>
                  </v-expansion-panel-header>
                  <v-expansion-panel-content>
                    <v-list>
                      <v-list-item>
                        <v-list-item-content>
                          <v-list-item>
                            <h5>List of Positions</h5>
                          </v-list-item>
                          <v-list-item>
                            <v-chip 
                              class="ma-1 font-weight-bolder"
                              color="green darken-4 white--text"
                              :ripple="true"
                              v-for="(job_vacancy, index) in branch_job_vacancies"
                              :key="index"
                              @click="get_job_details(job_vacancy.id)"
                            >
                              {{ job_vacancy.position_name }}
                            </v-chip>
                          </v-list-item>
                          <v-list-item>
                            <v-btn
                              color="#f44336"
                              outlined
                              class="font-weight-bold mt-5"
                              @click="(dialog_apply_now = true), get_branch_job_vacancy(branch)"
                            >
                              Check Details <v-icon right>mdi-menu-right</v-icon>
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
  import { required, requiredIf, minLength, email } from "vuelidate/lib/validators";
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
        myFileInput: { required },
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

        job_vac_id: "",
        branch_id: "",
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
          myFileInput: [],
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

        //revert selection to index 0
        this.selection = 0;
      },

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
        this.applicant.myFileInput = [];
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

      /* <<<<<<<<<<<<<<<<<<<<<<<<<<<<< REVISIONS V2 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> */

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

      next(page){
        this.overlay = true;

        axios.get("/api/public_api/branches?page=" + page).then(
          (response) => {

            setTimeout(() => {
              this.overlay = false;

              this.paginated_branch = response.data.paginated_branch.data;
              this.page_length = response.data.paginated_branch.total;
            }, 500);

          },
          (error) => {
            this.isUnauthorized(error);
          }
        );
      },

      search_branches(){

        let formData = new FormData();
        formData.append('search_textfield', this.search_textfield);
        
        axios.post("/api/public_api/search_branches", formData, {
          headers: {
            'Content-Type': 'multipart/form-data' 
          }
        }).then(
          (response) => {
        
            this.paginated_branch = response.data.branch;
          },
          (error) => {
            console.log(error);
            this.isUnauthorized(error);
          }
        );
      },

      get_branch_job_vacancy(branch){
        this.overlay = true;

        const branch_id = branch.id;

        //auto set branch of applicant
        this.branch_id = branch.id;

        let formData = new FormData();
        formData.append('branch_id', branch_id);
        axios.post("/api/public_api/get_job_vacancy_public", formData).then(
          (response) => {

            this.branch = branch.name;
            this.branch_job_vacancies = response.data.get_job_vacancy_lists;

            const data = response.data.get_job_vacancy_lists[0];
            this.description = data.description;
            this.qualifications = data.qualifications;
            this.educ_attain = data.educ_attain;
            this.job_title = data.position_name;

            //auto select 1st job upon open of Branch data
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

      get_job_details(job_vacancy_id){
        this.overlay = true;
        this.job_vac_id = job_vacancy_id;
        
        this.jobdesc_card = false;
        this.overview_card = false;

        let formData = new FormData();
        formData.append('job_vacancy_id', job_vacancy_id);
        axios.post("/api/public_api/get_job_details", formData).then(
          (response) => {

            //Waste 2 seconds
            setTimeout(() => {
              this.overlay = false;

              const data = response.data.job_details;
              this.description = data.description;
              this.qualifications = data.qualifications;
              this.educ_attain = data.educ_attain;
              this.job_title = data.position_name;

              this.jobdesc_card = true;
              this.overview_card = true;
            }, 500)
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

        formData.append('file', this.applicant.myFileInput);  

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
      },

      birthdateInput() {

        this.bdate_value_change();
        this.$v.applicant.birthdate.$touch()
        this.applicantError.birthdate = [];
        this.validateFields_form1();
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
        let myFileInput = this.applicant.myFileInput;
        let copy_of_grades = this.applicant.copy_of_grades;

        let extensions = ['docs', 'docx', 'pdf', 'jpg', 'jpeg', 'png'];
      
        if(myFileInput)
        {
          if(myFileInput.name)
          {
            let split_arr = myFileInput.name.split('.');
            let split_ctr = split_arr.length;
            let extension = split_arr[split_ctr - 1].toLowerCase();
            
            if(!extensions.includes(extension))
            {
              this.fileInvalid = true;
            }

            if(myFileInput.size > 5000000) // 5000000 bytes or 20MB
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

        if(!myFileInput || myFileInput.length == 0 || !copy_of_grades.length || this.fileInvalid){
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
        
      }
    },

    watch:{

      bdate_value() {
        let currentDate = new Date();
        let birthDate = new Date(this.bdate_value);

        let difference = currentDate - birthDate;
        let age = Math.floor(difference/31557600000);

        this.applicant.age = age;
        this.validateFields_form1();
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

        this.validateFields_form1();
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
        if (!this.$v.applicant.myFileInput.$dirty) return errors;
        !this.$v.applicant.myFileInput.required &&
          errors.push("File is required.");
        
        let file = this.applicant.myFileInput;
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
      
      
    },
    created(){
      AOS.init();
    },

    mounted() {
      // this.websocket();

      this.getBranches();

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