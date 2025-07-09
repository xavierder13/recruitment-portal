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
              cycle
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
                <!-- data-aos="fade-down" 
                data-aos-duration="1000" -->
              <v-col xs="12" sm="12" md="8" lg="8" 
                v-if="jobdesc_card" 
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
                <!-- data-aos="fade-down" 
                data-aos-duration="1000" -->
              <v-col xs="12" 
                sm="12" md="4" lg="4" 
                v-if="overview_card"
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
                      @click="(dialog_submit_now = true)"
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
      <v-dialog
        v-model="dialog_submit_now"
        persistent
      >
        <ApplicationForm
          ref="ApplicationForm"
          :job_vac_id="job_vac_id"
          :branch_id="branch_id"
          @closeDialog="closeApplicationDialog"
        />
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
  import ApplicationForm from './components/ApplicationForm.vue';

  export default {
    components: {
      ApplicationForm
    },
    mixins: [validationMixin],

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
      
        bdate_value: "",
        
        rules: [v => v.length <= 11 || 'Phone number is max 11 digits only.'],

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

      closeApplicationDialog() {
        this.dialog_submit_now = false;
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
    },

    watch:{

    },

    computed: {      
      
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