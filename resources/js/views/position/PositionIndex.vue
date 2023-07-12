<template>
  <div class="flex column">
    <div id="_wrapper" class="pa-5">
      <v-main>
        <v-breadcrumbs :items="items">
          <template v-slot:item="{ item }">
            <v-breadcrumbs-item :to="item.link" :disabled="item.disabled">
              {{ item.text.toUpperCase() }}
            </v-breadcrumbs-item>
          </template>
        </v-breadcrumbs>
        <v-card>
          <v-card-title>
            Position Lists
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              v-if="userPermissions.position_list"
            ></v-text-field>
            <template>
              <v-toolbar flat>
                <v-spacer></v-spacer>
                <v-btn
                  color="primary"
                  fab
                  dark
                  class="mb-2"
                  @click="clear() + (dialog = true)"
                  v-if="userPermissions.position_create"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
                <v-dialog v-model="dialog" max-width="700px" persistent>
                  <v-card>
                    <v-card-title style="background-color: #1976d2; color: #ffffff;">
                      <span class="headline">{{ formTitle }}</span>
                    </v-card-title>
                    <v-card-text>
                      <v-container class="mt-5">
                        <v-row>
                          <v-col cols="12" class="mt-0 mb-0 pt-0 pb-0">
                            <v-text-field
                              name="position"
                              v-model="editedItem.name"
                              label="Position"
                              required
                              :error-messages="positionErrors + positionError.name"
                              @input="$v.editedItem.name.$touch() + (positionError.name = [])"
                              @blur="$v.editedItem.name.$touch()"
                            ></v-text-field>
                          </v-col>

                          <v-col cols="12" class="mt-0 mb-0 pt-0 pb-0">
                            <v-switch v-model="switch1" :label="activeStatus"></v-switch>
                          </v-col>

                          <v-col cols="12" class="mt-3 mb-0 pt-0 pb-0">
                            <v-subheader>Responsibilities </v-subheader>
                            <ckeditor v-model="editedItem.description"></ckeditor>
                          </v-col>

                          <v-col cols="12" class="mt-5 mb-0 pt-0 pb-0">
                            <v-subheader>Qualifications </v-subheader>
                            <ckeditor v-model="editedItem.qualifications"></ckeditor>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn
                        color="primary"
                        @click="save"
                        class="mb-4"
                        :disabled="disabled"
                      >
                        Save
                      </v-btn>
                      <v-btn color="#E0E0E0" @click="close" class="mb-4">
                        Cancel
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-toolbar>
            </template>
          </v-card-title>

          <v-col
            cols="12"
            md="12"
            v-if="table_loader"
          >
            <v-skeleton-loader
              type="table-thead, image, table-tfoot"
            ></v-skeleton-loader>
          </v-col>

          <v-scroll-y-transition mode="out-in">
            <v-data-table
              :headers="headers"
              :items="positions"
              :search="search"
              :loading="loading"
              loading-text="Loading... Please wait"
              v-if="userPermissions.position_list"
            >
              <template v-slot:item.cnt_id="{ item }">
                {{ positions.indexOf(item) + 1 }}
              </template>
              <template v-slot:item.status="{ item }">
                <v-chip
                  color="#37474f"
                  dark
                  small
                  v-if="item.status === 0"
                >  
                  Inactive
                </v-chip>
                <v-chip
                  color="#009688"
                  dark
                  small
                  v-if="item.status === 1"
                >  
                  Active
                </v-chip>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-icon
                  small
                  class="mr-2"
                  color="green"
                  @click="editPosition(item)"
                  v-if="userPermissions.position_edit"
                >
                  mdi-pencil
                </v-icon>
                <v-icon
                  small
                  color="red"
                  @click="showConfirmAlert(item)"
                  v-if="userPermissions.position_delete"
                >
                  mdi-delete
                </v-icon>
              </template>
            </v-data-table>
          </v-scroll-y-transition>  
        </v-card>
      </v-main>
    </div>
  </div>
</template>
<script>

import axios from "axios";
import { validationMixin } from "vuelidate";
import { required, maxLength, email } from "vuelidate/lib/validators";
import { mapState } from "vuex";

export default {

  mixins: [validationMixin],

  validations: {
    editedItem: {
      name: { required },
    },
  },
  data() {
    return {
      search: "",
      table_loader: false,
      headers: [
        { text: "#", value: "cnt_id", width: "20px" },
        { text: "Position", value: "name" },
        { text: "Status", value: "status" },
        { text: "Actions", value: "actions", sortable: false, width: "80px" },
      ],
      switch1: true,
      disabled: false,
      dialog: false,
      positions: [],
      editedIndex: -1,
      editedItem: {
        name: "",
        description: "",
        qualifications: "",
        status: 1
      },
      defaultItem: {
        name: "",
      },
      items: [
        {
          text: "Home",
          disabled: false,
          link: "/",
        },
        {
          text: "Position Lists",
          disabled: true,
        },
      ],
      loading: false,
      positionError: {
        name: [],
      }
    };
  },

  methods: {
    getPosition() {
      this.loading = true;
      this.table_loader = true;
      axios.get("/api/position/index").then(
        (response) => {
          this.positions = response.data.positions;
          this.loading = false;
          this.table_loader = false;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    editPosition(item) {
      this.editedIndex = this.positions.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;

      if (item.status == 1) {
        this.switch1 = true;
      } else {
        this.switch1 = false;
      }
    },

    deletePosition(position_id) {
      const data = { position_id: position_id };
      this.loading = true;
      axios.post("/api/position/delete", data).then(
        (response) => {
          if (response.data.success) {
            // send data to Sockot.IO Server
            // this.$socket.emit("sendData", { action: "position-delete" });
          }
          this.loading = false;
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    showAlert() {
      this.$toaster.success('Record has been saved.', {
        timeout: 2000
      });
    },

    showConfirmAlert(item) {
      this.$swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Delete record!",
      }).then((result) => {
        // <--

        if (result.value) {
          // <-- if confirmed

          const position_id = item.id;
          const index = this.positions.indexOf(item);

          //Call delete Position function
          this.deletePosition(position_id);

          //Remove item from array positions
          this.positions.splice(index, 1);

          this.$toaster.success('Record has been deleted.', {
            timeout: 2000
          });
        }else{
          this.$toaster.warning('You have cancelled deleting this record.', {
            timeout: 2000
          });
        }
      });
    },

    close() {
      this.dialog = false;
      this.clear();
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      this.$v.$touch();
      this.positionError = {
        name: [],
      };

      if (!this.$v.$error) {
        this.disabled = true;

        if (this.editedIndex > -1) {
          const data = this.editedItem;
          const position_id = this.editedItem.id;

          axios.post("/api/position/update/" + position_id, data).then(
            (response) => {
              if (response.data.success) {
                // send data to Sockot.IO Server
                // this.$socket.emit("sendData", { action: "position-edit" });

                Object.assign(
                  this.positions[this.editedIndex],
                  this.editedItem
                );
                this.showAlert();
                this.close();
              }
              else
              { 
                let errors = response.data;
                let errorNames = Object.keys(response.data);

                errorNames.forEach(value => {
                  this.positionError[value].push(errors[value]);
                });
                
              }

              this.disabled = false;
            },
            (error) => {
              this.isUnauthorized(error);
              this.disabled = false;
            }
          );
        } else {
          const data = this.editedItem;

          axios.post("/api/position/store", data).then(
            (response) => {
              if (response.data.success) {
                // send data to Sockot.IO Server
                // this.$socket.emit("sendData", { action: "position-create" });

                this.showAlert();
                this.close();

                //push recently added data from database
                this.positions.push(response.data.position);
              }
              else
              { 
                let errors = response.data;
                let errorNames = Object.keys(response.data);

                errorNames.forEach(value => {
                  this.positionError[value].push(errors[value]);
                });
                
              }
              this.disabled = false;
            },
            (error) => {
              this.isUnauthorized(error);
              this.disabled = false;
            }
          );
        }
      }
    },

    clear() {
      this.$v.$reset();
      this.editedItem.name = "";
      this.positionError = {
        name: []
      }
    },
    
    isUnauthorized(error) {
      // if unauthenticated (401)
      if (error.response.status == "401") {
        this.$router.push({ name: "unauthorize" });
      }
    },

    websocket() {
      // Socket.IO fetch data
      this.$options.sockets.sendData = (data) => {
        let action = data.action;
        if (
          action == "position-create" ||
          action == "position-edit" ||
          action == "position-delete"
        ) {
          this.getPosition();
        }
      };
    },
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New Position" : "Edit Position";
    },
    positionErrors() {
      const errors = [];
      if (!this.$v.editedItem.name.$dirty) return errors;
      !this.$v.editedItem.name.required &&
        errors.push("Position is required.");
      return errors;
    },
    activeStatus() {
      if (this.switch1) {
        this.editedItem.status = 1;
        return " Active";
      } else {
        this.editedItem.status = 0;
        return " Inactive";
      }
    },
    ...mapState("userRolesPermissions", ["userRoles", "userPermissions"]),
  },
  mounted() {
    axios.defaults.headers.common["Authorization"] =
      "Bearer " + localStorage.getItem("access_token");
    this.getPosition();
    // this.websocket();
  },
};
</script>