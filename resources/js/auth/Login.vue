<template>
  <v-app v-if="!user">
    <v-overlay :absolute="absolute" :value="overlay">
      <v-progress-circular
        size="64"
        indeterminate
      ></v-progress-circular>
    </v-overlay>
    <v-main>
      <v-container fluid fill-height>
        <v-row>
          <v-col cols="6">
            <v-img
              max-height="auto"
              max-width="auto"
              src="wysiwyg/background_pic/rec3.gif"
              style="display: block; margin-left: auto; margin-right: auto;"
            ></v-img>
          </v-col>
          <v-col cols="6" 
            style="margin-top: 5%"
            data-aos="fade-left"
            data-aos-duration="1000"
          >
            <v-layout align-center justify-center>
              <v-flex xs8 sm8 md8>
                <v-card class="elevation-12">
                  <v-toolbar color="dark" dark flat>
                    <v-toolbar-title color>Login</v-toolbar-title>
                    <v-spacer></v-spacer>
                  </v-toolbar>
                  <v-card-text>
                    <v-alert dense outlined type="error" v-if="isInvalid">
                      Invalid Credentials
                    </v-alert>
                    <v-form>
                      <v-text-field
                        label="Email"
                        name="email"
                        type="text"
                        v-model="email"
                        prepend-icon="mdi-account"
                        required
                        :error-messages="emailErrors"
                        @change="$v.email.$touch()"
                        @blur="$v.email.$touch()"
                        @keyup="isInvalid = false"
                      ></v-text-field>

                      <v-text-field
                        id="password"
                        label="Password"
                        name="password"
                        type="password"
                        v-model="password"
                        prepend-icon="mdi-key"
                        required
                        :error-messages="passwordErrors"
                        @change="$v.password.$touch()"
                        @blur="$v.password.$touch()"
                        @keyup="isInvalid = false"
                      ></v-text-field>
                    </v-form>
                  </v-card-text>
                  <v-card-actions class="mr-5 ml-5">
                    <v-btn color="primary" class="mb-5 mr-5" block @click="login"
                      >Login
                    </v-btn>
                  </v-card-actions>
                  <v-footer style="background-color: #272727; color: #ffffff; font-size: 13px;">
                    <v-row>
                      <v-col class="text-center pt-5" cols="12">
                        <p>All Rights Reserved. Copyright {{ new Date().getFullYear() }}</p>
                      </v-col>
                    </v-row>
                  </v-footer>
                </v-card>
              </v-flex>
            </v-layout>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import axios from "axios";
import { validationMixin } from "vuelidate";
import { required, maxLength, email } from "vuelidate/lib/validators";
import AOS from 'aos';
import 'aos/dist/aos.css';

export default {
  name: "login",
  mixins: [validationMixin],

  validations: {
    email: { required },
    password: { required },
  },
  data() {
    return {
      email: "",
      password: "",
      user: null,
      isInvalid: false,
      absolute: true,
      overlay: false,
    };
  },
  computed: {
    emailErrors() {
      const errors = [];
      if (!this.$v.email.$dirty) return errors;
      // !this.$v.lastname.maxLength &&
      // errors.push("Name must be at most 10 characters long");
      !this.$v.email.required && errors.push("email is required.");
      return errors;
    },

    passwordErrors() {
      const errors = [];
      if (!this.$v.password.$dirty) return errors;
      !this.$v.password.required && errors.push("Password is required.");
      return errors;
    },
  },
  methods: {
    login() {
      this.$v.$touch();

      if (!this.$v.$error) {
        this.overlay = true;
        const email = this.email;
        const password = this.password;
        const data = { email: email, password: password };

        axios.post("/api/auth/login", data).then(
          (response) => {
            if (response.data.access_token) {
              localStorage.setItem("access_token", response.data.access_token);
              this.$router.push("/jobapplicants/index").catch((e) => {});
              this.clear();
            } else {
              this.isInvalid = true;
              this.overlay = false;
            }
          },
          (error) => {
            console.log(error);
          }
        );
      }
    },

    clear() {
      this.$v.$reset();
      this.isInvalid = false;
      this.email = null;
      this.password = null;
      this.overlay = false;
    },
  },
  created(){
    AOS.init();
  },
  mounted() {
    let self = this;
    window.addEventListener("keyup", function (event) {
      if (event.keyCode === 13) {
        self.login();
      }
    });
  },
};
</script>