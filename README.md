# Youdemy Fatima-Ezzahra - Plateforme de Cours en Ligne 🎓

## Contexte du Projet 🏗️
La plateforme **Youdemy**, actuellement développée en **PHP natif** avec une approche procédurale, doit être migrée vers une architecture **MVC (Modèle-Vue-Contrôleur)** afin d'améliorer sa **modularité**, sa **maintenabilité** et son **extensibilité**. Cette migration permettra également de **mieux séparer les responsabilités** et de **faciliter les futures évolutions** du projet. 🚀

### 🎯 Objectifs :
- 🔄 **Restructurer** le code existant en suivant le modèle **MVC**.
- 📖 **Améliorer** la lisibilité et la maintenabilité du code.
- 🏆 **Implémenter** des **bonnes pratiques de développement** (SOLID, DRY, etc.).
- 🔮 **Préparer** la plateforme pour des fonctionnalités futures.

---

## ⚙️ Fonctionnalités Requises

### 🏛️ 1. Structure MVC :

#### 📂 **Modèle (Model)** :
- 🗂️ **Gérer** les interactions avec la base de données (**CRUD** pour les cours, utilisateurs, tags, etc.).
- 🔗 **Implémenter** les relations entre les entités (**one-to-many, many-to-many**).
- 🛡️ **Utiliser** des **requêtes préparées** pour éviter les **injections SQL**.

#### 🎨 **Vue (View)** :
- 🎭 **Créer** des **templates réutilisables** pour les pages (**header, footer, etc.**).
- 📱 **Assurer** un **design responsive et accessible**.
- ✅ **Intégrer** la validation côté client avec **HTML5 et JavaScript natif**.

#### 🧠 **Contrôleur (Controller)** :
- ⚡ **Gérer** la **logique métier** et les interactions entre les modèles et les vues.
- 🛡️ **Valider** les données côté serveur pour prévenir les attaques **XSS et CSRF**.
- 🔑 **Gérer** les **sessions utilisateurs** et les autorisations d'accès.

---

### 🚀 2. Fonctionnalités Existantes à Migrer :

#### 🌍 **Front Office** 🖥️

##### 🔍 **Visiteur** 🌐
- 📜 **Accès** au **catalogue des cours** avec pagination.
- 🔎 **Recherche** de cours par mots-clés.
- 📝 **Création d’un compte** avec le choix du rôle (**Étudiant ou Enseignant**).

##### 🎓 **Étudiant** 👨‍🎓
- 📖 **Visualisation** du **catalogue des cours**.
- 📚 **Recherche et consultation** des détails des cours (**description, contenu, enseignant, etc.**).
- ✅ **Inscription** à un cours après authentification.
- 🗂️ **Accès** à une section **“Mes cours”** regroupant les cours rejoints.

##### 👨‍🏫 **Enseignant** 🎤
- ➕ **Ajout de nouveaux cours** avec des détails tels que :
  - 🏷️ **Titre, description, contenu** (**vidéo ou document**), **tags, et catégorie**.
- 🛠️ **Gestion des cours** :
  - ✏️ **Modification**, 🗑️ **suppression** et 👀 **consultation des inscriptions**.
- 📊 **Accès** à une section **“Statistiques”** sur les cours :
  - 🔢 **Nombre d’étudiants inscrits**, 📋 **nombre de cours**, etc.

---

#### 🔐 **Back Office** 🛡️

##### 👩‍💼 **Administrateur** 🎩
- ✅ **Validation des comptes enseignants**.
- 👥 **Gestion des utilisateurs** :
  - 🔓 **Activation**, 🔒 **suspension** ou ❌ **suppression**.
- 📂 **Gestion des contenus** :
  - 📘 **Cours**, 🏷️ **catégories** et 🏷️ **tags**.
  - 🚀 **Insertion en masse de tags** pour **gagner en efficacité**.
- 📈 **Accès** à des **statistiques globales** :
  - 📊 **Nombre total de cours**, 📂 **répartition par catégorie**.
  - 🏆 **Le cours avec le plus d’étudiants**.
  - 👨‍🏫 **Les Top 3 enseignants**.

---

### 🔧 3. Exigences Techniques :

- 🗄️ **Utiliser PostgreSQL** comme **système de gestion de base de données**.
- 🏛️ **Respecter** les principes **OOP** (**encapsulation, héritage, polymorphisme**).
- 🔑 **Utilisation** de **sessions PHP** pour la **gestion des utilisateurs connectés**.
- ✅ **Validation** des données **côté serveur et client**.
- 🔥 **Prévention** des attaques **XSS, CSRF et SQL injection**.
- 💾 **Utilisation** de **requêtes préparées** pour les interactions avec la base de données.

---

### 🔄 Fonctionnalités Transversales 🏗️
- 🔗 **Un cours peut contenir plusieurs tags** (**relation many-to-many**).
- 🧩 **Application du concept de polymorphisme** dans les méthodes suivantes :
  - ➕ **Ajouter cours**.
  - 👀 **Afficher cours**.
- 🔒 **Système d’authentification et d’autorisation** pour **protéger les routes sensibles**.
- 🚦 **Contrôle d’accès** : chaque utilisateur **ne peut accéder qu’aux fonctionnalités correspondant à son rôle**.

---

Ce projet constitue une **étape clé** dans l’évolution de **Youdemy** en modernisant son architecture et en assurant une **meilleure expérience utilisateur** ! ✨

