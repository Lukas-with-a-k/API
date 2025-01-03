<template>
    <div>
      <h1>Inscription</h1>
      <form @submit.prevent="register">
        <div>
          <label for="email">Email</label>
          <input type="email" v-model="email" id="email" required />
        </div>
        <div>
          <label for="password">Mot de passe</label>
          <input type="password" v-model="password" id="password" required />
        </div>
        <button type="submit">S'inscrire</button>
      </form>
      <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
    </div>
  </template>
  
  <script>
  import apiClient from "../services/api";
  
  export default {
    data() {
      return {
        email: "",
        password: "",
        errorMessage: "",
      };
    },
    methods: {
      async register() {
        try {
          const response = await apiClient.post("/auth/register", {
            email: this.email,
            password: this.password,
          });
  
          alert(response.data.message || "Inscription réussie !");
          this.$router.push("/login"); // Rediriger vers la page de connexion
        } catch (error) {
          this.errorMessage = "Une erreur est survenue lors de l'inscription.";
        }
      },
    },
  };
  </script>
  