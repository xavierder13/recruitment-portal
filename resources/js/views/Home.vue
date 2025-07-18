<template>
  <v-app>
    <!-- Navbar -->
    <v-app-bar dense dark app id="topbar">
      <v-btn icon @click.stop="drawer = !drawer">
        <v-app-bar-nav-icon></v-app-bar-nav-icon>
      </v-btn>

      <v-spacer></v-spacer>
      <v-menu offset-y :nudge-width="200">
        <template v-slot:activator="{ on, attrs }">
          <v-btn small rounded v-bind="attrs" v-on="on" color="grey darken-3">
            <v-icon> mdi-menu-down </v-icon>
          </v-btn>
        </template>
        <v-card color="grey lighten-3">
          <v-card-text class="text-center">
            <v-row>
              <v-col
                ><img
                  src="/img/default-profile.png"
                  width="120px"
                  height="100px"
                  alt="User"
                  style="border-radius: 50%"
              /></v-col>
            </v-row>
            <v-row>
              <v-col>
                <h5 class="text--secondary">
                  {{ user.name }}
                </h5>
                <h6 class="text--disabled">
                  {{
                    user.id === 1
                      ? "Administrator"
                      : user.position
                      ? user.position.name
                      : ""
                  }}
                </h6>
              </v-col>
            </v-row>
          </v-card-text>
          <v-divider class="pa-0 mb-0"></v-divider>
          <v-card-actions class="grey darken-2 d-flex justify-content-around">
            <div>
              <v-btn class="white--text" plain small @click="userProfile()">
                <v-icon small class="mr-1">mdi-account</v-icon> Profile
              </v-btn>
            </div>
            <div>
              <v-btn class="white--text" plain small @click="logout">
                <v-icon small class="mr-1">mdi-logout</v-icon> Logout
              </v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-menu>
    </v-app-bar>

    <!-- Sidebar -->
    <v-navigation-drawer v-model="drawer" dark app id="drawer">
      <v-list id="drawer_user">
        <v-list-item class="px-2">
          <v-list-item-avatar class="rounded-5" height="60" width="60">
            <v-img src="/img/addessa.jpg" data-aos="zoom-in" data-aos-duration="2000"></v-img>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>Web Portal</v-list-item-title>
            <v-list-item-subtitle>{{ user.name }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-list expand>
        <v-list-item link to="/dashboard">
          <v-list-item-icon>
            <v-icon>mdi-view-dashboard</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Dashboard</v-list-item-title>
        </v-list-item>
        <v-list-group
          no-action
          v-if="
            hasPermission('user-list') || 
            hasPermission('user-create') ||
            hasPermission('jobvacancies-create') || 
            hasPermission('jobvacancies-list') ||
            hasPermission('jobapplicants-list')
          "
        >
          <!-- List Group Icon-->
          <v-icon slot="prependIcon">mdi-account-hard-hat</v-icon>
          <!-- List Group Title -->
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>Recruitment</v-list-item-title>
            </v-list-item-content>
          </template>
          <!-- List Group Items -->
          <v-list-item
            link
            to="/position/index"
            v-if="
              hasPermission('position-list') || hasPermission('position-create')
            "
          >
            <v-list-item-content>
              <v-list-item-title>Position</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item 
            link to="/jobvacancies/index"
            v-if="
              hasPermission('jobvacancies-create') || 
              hasPermission('jobvacancies-list') 
            "
          >
            <v-list-item-content>
              <v-list-item-title>Job Vacancies</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <!-- <v-list-item 
            link to="/jobapplicants/index"
            v-if="hasPermission('jobapplicants-list')"
          >
            <v-list-item-content>
              <v-list-item-title>Job Applicants</v-list-item-title>
            </v-list-item-content>
          </v-list-item> -->
          <v-list-item 
            link to="/jobapplicants/index-new"
            v-if="hasPermission('jobapplicants-list')"
          >
            <v-list-item-content>
              <v-list-item-title>Job Applicants</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-group>
        <v-list-group
          no-action
          v-if="hasPermission('user-list') || hasPermission('user-create')"
        >
          <!-- List Group Icon-->
          <v-icon slot="prependIcon">mdi-account-arrow-right-outline</v-icon>
          <!-- List Group Title -->
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>User</v-list-item-title>
            </v-list-item-content>
          </template>
          <!-- List Group Items -->
          <v-list-item link to="/user/index" v-if="hasPermission('user-list')">
            <v-list-item-content>
              <v-list-item-title>User Record</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item
            link
            to="/user/create"
            v-if="hasPermission('user-create')"
          >
            <v-list-item-content>
              <v-list-item-title>Create New</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-group>
        <v-list-group
          no-action
          v-if="
            hasPermission('branch-list') ||
            hasPermission('branch-create') ||
            hasPermission('role-list') ||
            hasPermission('role-create') ||
            hasPermission('permission-list') ||
            hasPermission('permission-create')
          "
        >
          <!-- List Group Icon-->
          <v-icon slot="prependIcon">mdi-cog</v-icon>
          <!-- List Group Title -->
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>Settings</v-list-item-title>
            </v-list-item-content>
          </template>
          <!-- List Group Items -->
          <v-list-item
            link
            to="/branch/index"
            v-if="hasPermission('branch-list') || hasPermission('branch-create')"
          >
            <v-list-item-content>
              <v-list-item-title>Branch</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item
            link
            to="/role/index"
            v-if="hasPermission('role-list') || hasPermission('role-create')"
          >
            <v-list-item-content>
              <v-list-item-title>Role</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item
            link
            to="/permission/index"
            v-if="hasPermission('permission-list') || hasPermission('permission-create')"
          >
            <v-list-item-content>
              <v-list-item-title>Permission</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-group>
      </v-list>
    </v-navigation-drawer>
    <v-overlay :absolute="absolute" :value="overlay">
      <v-progress-circular
        :size="70"
        :width="7"
        color="primary"
        indeterminate
      ></v-progress-circular>
    </v-overlay>

    <!-- Content -->
    <v-scroll-x-transition mode="out-in">
      <router-view :key="$route.fullPath"/>
    </v-scroll-x-transition>

    <v-footer padless dense dark app id="footer">
      <v-col class="text-center" cols="12">
        Copyright © {{ new Date().getFullYear() }} —
        <strong> ADDESSA CORPORATION</strong>
      </v-col>
    </v-footer>
  </v-app>
</template>

<style scoped>
html {
  overflow-y: auto;
} /* show scrollbar when overflow */

a {
  text-decoration: none;
}

#topbar {
  background-color: #001732;
}

#drawer {
  background-color: #001c3e;
}
#drawer_user {
  background-color: #0d47a1;
}

#footer {
  background-color: #001732;
}
</style>

<script>
import axios from "axios";
import { mapState, mapActions, mapGetters } from "vuex";
import AOS from 'aos';
import 'aos/dist/aos.css';

export default {
  data() {
    return {
      absolute: true,
      overlay: false,
      drawer: true,
      mini: false,
      right: null,
      selectedItem: 1,
      loading: null,
    };
  },

  methods: {
    logout() {
      this.overlay = true;
      axios.get("/api/auth/logout").then(
        (response) => {
          if (response.data.success) {
            this.overlay = false;
            localStorage.removeItem("access_token");
            this.$router.push("/hradmin-login").catch(() => {});
          }
        },
        (error) => {
          this.overlay = false;
          console.log(error);

          // if unauthenticated (401)
          if (error.response.status == "401") {
            localStorage.removeItem("access_token");
            this.$router.push({ name: "login" });
          }
        }
      );
    },

    userProfile() {
      this.$router.push({ name: "user.profile" }).catch((e) => {});
    },

    websocket() {
      // Socket.IO fetch data
      this.$options.sockets.sendData = (data) => {
        let action = data.action;
        if (
          action == "user-edit" ||
          action == "role-edit" ||
          action == "role-delete" ||
          action == "permission-delete"
        ) {
          this.userRolesPermissions();
        }
      };
    },
    ...mapActions("auth", ["getUser"]),
    ...mapActions("userRolesPermissions", ["userRolesPermissions"]),
  },
  computed: {
    ...mapState("auth", ["user"]),
    ...mapState("userRolesPermissions", ["userRoles", "userPermissions"]),
    ...mapGetters("userRolesPermissions", ["hasRole", "hasPermission"]),
  },
  mounted() {
    axios.defaults.headers.common["Authorization"] =
      "Bearer " + localStorage.getItem("access_token");

    this.getUser();
    this.userRolesPermissions();
  },
  created(){
    AOS.init();
  },
};
</script>