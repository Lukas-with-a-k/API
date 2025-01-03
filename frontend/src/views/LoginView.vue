<template>
    <div>
      <h1>Connexion</h1>
      <form @submit.prevent="login">
        <div>
          <label for="email">Email</label>
          <input type="email" v-model="email" id="email" required />
        </div>
        <div>
          <label for="password">Mot de passe</label>
          <input type="password" v-model="password" id="password" required />
        </div>
        <button type="submit">Se connecter</button>
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
      async login() {
        try {
          const response = await apiClient.post("/auth/login", {
            email: this.email,
            password: this.password,
          });
          const token = response.data.token;
  
          // Sauvegarder le token JWT dans le localStorage
          localStorage.setItem("token", token);
  
          alert("Connexion réussie !");
          this.$router.push("/films"); // Rediriger après connexion
        } catch (error) {
          this.errorMessage = "Email ou mot de passe incorrect.";
        }
      },
    },
  };
  </script>
  