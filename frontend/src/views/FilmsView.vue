<template>
    <div>
      <h1>Liste des films</h1>
      <ul v-if="films.length">
        <li v-for="film in films" :key="film.id">
          <strong>{{ film.titre }}</strong> - {{ film.description }}
          <div>
            <button @click="likeFilm(film.id)">❤️ Like</button>
            <span>Likes: {{ film.likes || 0 }}</span>
          </div>
          <div v-if="film.commentaires.length">
            <h3>Commentaires :</h3>
            <ul>
              <li v-for="comment in film.commentaires" :key="comment.id">
                <strong>{{ comment.utilisateur }}</strong> :
                {{ comment.contenu }}
                <small>({{ comment.dateCreation }})</small>
              </li>
            </ul>
          </div>
          <div>
            <h4>Ajouter un commentaire :</h4>
            <textarea v-model="newComment[film.id]" placeholder="Écrire un commentaire..."></textarea>
            <button @click="addComment(film.id)">Envoyer</button>
          </div>
        </li>
      </ul>
      <p v-else>Chargement des films...</p>
    </div>
  </template>
  
  <script>
  import apiClient from "../services/api";
  
  export default {
    data() {
      return {
        films: [],
        newComment: {}, 
      };
    },
    async created() {
      try {
        const response = await apiClient.get("/films");
        this.films = response.data.map((film) => ({
          ...film,
          likes: film.likes || 0,
          commentaires: film.commentaires || [],
        }));
      } catch (error) {
        console.error("Erreur lors de la récupération des films :", error);
      }
    },
    methods: {
      async likeFilm(filmId) {
        try {
          await apiClient.post("/likes", { film_id: filmId });
          const film = this.films.find((f) => f.id === filmId);
          if (film) {
            film.likes += 1;
          }
          alert("Film liké avec succès !");
        } catch (error) {
          console.error("Erreur lors du like :", error);
          alert("Impossible de liker le film. Vérifiez votre connexion ou votre authentification.");
        }
      },
      async addComment(filmId) {
        try {
          const contenu = this.newComment[filmId];
          if (!contenu) {
            alert("Le commentaire ne peut pas être vide.");
            return;
          }
  
          const response = await apiClient.post(`/films/${filmId}/comments`, { contenu });
          const film = this.films.find((f) => f.id === filmId);
          if (film) {
            film.commentaires.push({
              id: response.data.id,
              contenu,
              utilisateur: "Vous", 
              dateCreation: new Date().toISOString().slice(0, 19).replace("T", " "),
            });
          }
  
          this.newComment[filmId] = ""; 
          alert("Commentaire ajouté avec succès !");
        } catch (error) {
          console.error("Erreur lors de l'ajout du commentaire :", error);
          alert("Impossible d'ajouter le commentaire. Vérifiez votre connexion ou votre authentification.");
        }
      },
    },
  };
  </script>
  
  <style scoped>
  h1 {
    text-align: center;
  }
  
  ul {
    list-style-type: none;
    padding: 0;
  }
  
  li {
    margin: 10px 0;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }
  
  button {
    margin-right: 10px;
    padding: 5px 10px;
    border: none;
    background-color: #f44336;
    color: white;
    border-radius: 5px;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #d32f2f;
  }
  
  textarea {
    display: block;
    width: 100%;
    margin: 10px 0;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }
  
  textarea:focus {
    outline: none;
    border-color: #f44336;
  }
  </style>
  