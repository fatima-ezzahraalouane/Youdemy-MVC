CREATE DATABASE youdemyMvc;

\c youdemyMvc;

create type user_role as enum ('Admin', 'Étudiant', 'Enseignant');

create type compte_status as enum ('Actif', 'Inactif', 'Suspendu');

create type cours_contenu_type as enum ('Vidéo', 'Document');

create type cours_status as enum ('En attente', 'Publié', 'Rejeté');

CREATE TABLE role (
    id_role serial primary key,
    name_user user_role not null
);

CREATE TABLE usersite (
    id_usersite SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL UNIQUE,
    id_role INT NOT NULL,
    statut compte_status DEFAULT 'Actif', 
    is_approved BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_role) REFERENCES role(id_role)
);

create table categorie (
    id_categorie SERIAL primary key,
    nom varchar(100) not null unique,
    image_url varchar(250) not null
);

create table tag (
    id_tag serial primary key,
	nom varchar(100) not null unique
);

CREATE TABLE cours (
    id_cours SERIAL PRIMARY KEY,
    titre VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    image_url VARCHAR(250) NOT NULL,
    contenu TEXT,
    contenu_type cours_contenu_type NOT NULL,
    id_usersite INT NOT NULL, 
    id_categorie INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut cours_status DEFAULT 'En attente', 
    duree FLOAT NULL,
    nb_pages INT NULL,
    FOREIGN KEY (id_usersite) REFERENCES usersite(id_usersite) ON DELETE CASCADE,
    FOREIGN KEY (id_categorie) REFERENCES categorie(id_categorie) ON DELETE SET NULL
);

CREATE TABLE cours_tag (
    id_cours_tag SERIAL PRIMARY KEY,
    id_cours INT NOT NULL,
    id_tag INT NOT NULL,
    UNIQUE (id_cours, id_tag),
    FOREIGN KEY (id_cours) REFERENCES cours(id_cours) ON DELETE CASCADE,
    FOREIGN KEY (id_tag) REFERENCES tag(id_tag) ON DELETE CASCADE
);

CREATE TABLE inscription (
    id_inscription SERIAL PRIMARY KEY,
    id_usersite INT NOT NULL,
    id_cours INT NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usersite) REFERENCES usersite(id_usersite) ON DELETE CASCADE,
    FOREIGN KEY (id_cours) REFERENCES cours(id_cours) ON DELETE CASCADE
);






-- Insertion

INSERT INTO role (name_user) VALUES 
('Admin'),
('Étudiant'),
('Enseignant');


INSERT INTO categorie (nom, image_url)
VALUES 
    ('Développement Web', 'https://www.keplearning.com/wp-content/uploads/2021/03/web-dev.jpg'),
    ('Développement Mobile', 'https://www.t-t.ma/produits-et-services/wp-content/uploads/2022/09/mobile-app-2022-scaled.jpg'),
    ('Data Science', 'https://www.oxfordinstitute.in/img/data-science-course.jpg'),
    ('Intelligence Artificielle', 'https://www.zabala.fr/wp-content/uploads/2023/11/intelligence-artificielle-et-conseil.jpg'),
    ('Cyber Sécurité', 'https://edito-img.annonces-legales.fr/2023/02/20230209-cybersecurite-system-hacked.jpg?w=888&h=450&q=90&s=3cc4fbd3f7c1dd903f09dee12d10d57e');


-- Développement Web (id_categorie = 1)
INSERT INTO cours (titre, description, image_url, contenu, contenu_type, id_usersite, id_categorie, statut)
VALUES
    ('HTML Basics', 'Apprendre les bases de HTML.', 'https://lenadesign.org/wp-content/uploads/2019/12/html-basic.jpg', 'Introduction au HTML pour débutants.', 'Document', 20, 1, 'publie'),
    ('CSS Essentials', 'Maîtriser le CSS pour le design web.', 'https://cdn.sbspathways.umass.edu/wp-content/uploads/sites/105/2023/02/b51051f8035604eebd10f3374a5f2b43-1592866798214.jpg', 'https://youtu.be/OEV8gMkCHXQ?si=6nLsuZr5pE1UVbSx', 'Vidéo', 24, 1, 'publie'),
    ('JavaScript pour Débutants', 'Les bases de la programmation en JavaScript.', 'https://img-c.udemycdn.com/course/750x422/4885020_8f0c_2.jpg', 'https://youtu.be/v3Ho7QVaTXM?si=JM8rJ2tI-76Qo4ZK', 'Vidéo', 25, 1, 'publie'),
    ('Responsive Web Design', 'Créer des sites adaptables à tous les écrans.', 'https://miro.medium.com/v2/resize:fit:2000/1*qF8LfAwUhl57g9T0BVvVdg.jpeg', 'Techniques de conception responsive.', 'Document', 26, 1, 'publie'),
    ('Bootstrap Framework', 'Utilisation de Bootstrap pour le design rapide.', 'https://d1le3ohiuslpz1.cloudfront.net/skillcrush/wp-content/uploads/2023/08/What-is-Bootstrap-2.png', 'https://youtu.be/MyCvTSjkD74?si=eU74-v6NJ5ql34UB', 'Vidéo', 27, 1, 'publie'),
    ('SEO Basics', 'Introduction au SEO pour les sites web.', 'https://cdn2.hubspot.net/hubfs/6671704/Page-001-9.jpg', 'Optimisation pour les moteurs de recherche.', 'Document', 28, 1, 'publie'),
    ('PHP pour Débutants', 'Introduction au langage PHP.', 'https://www.agmwebhosting.com/blog/wp-content/uploads/2018/08/PHP-introduction.png', 'https://youtu.be/Mz57ncVpHaM?si=LkIOeIkS3CTrY0t9', 'Vidéo', 20, 1, 'publie'),
    ('Laravel Essentials', 'Apprendre Laravel pour les projets web.', 'https://miro.medium.com/v2/resize:fit:1080/1*hj4o11jY1Bk8zKDSO7WgMw.png', 'Framework Laravel pour débutants.', 'Document', 24, 1, 'publie'),
    ('Web Security', 'Sécuriser vos applications web.', 'https://cwatch.comodo.com/blog/wp-content/uploads/2020/05/good-website-security-plan.jpg', 'https://youtu.be/shQEXpUwaIY?si=0p7TbquofQ_VA_Vf', 'Vidéo', 25, 1, 'publie'),
    ('Git et GitHub', 'Gestion de projets avec Git et GitHub.', 'https://www.biteinteractive.com/wp-content/uploads/2021/05/git-vs-github.png', 'Collaboration avec Git.', 'Document', 26, 1, 'publie');



INSERT INTO tag (nom)
VALUES
    ('HTML/CSS'),
    ('JavaScript'),
    ('Responsive Design'),
    ('Frameworks Frontend'),
    ('Frameworks Backend'),
    ('Base de Données'),
    ('Cybersécurité'),
    ('Cloud Computing');


-- HTML Basics
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (1, 1), -- HTML Basics lié à 'HTML/CSS'
    (1, 3); -- HTML Basics lié à 'Responsive Design'

-- CSS Essentials
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (2, 1), -- CSS Essentials lié à 'HTML/CSS'
    (2, 3); -- CSS Essentials lié à 'Responsive Design'

-- JavaScript pour Débutants
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (3, 2), -- JavaScript pour Débutants lié à 'JavaScript'
    (3, 4); -- JavaScript pour Débutants lié à 'Frameworks Frontend'

-- Responsive Web Design
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (4, 3), -- Responsive Web Design lié à 'Responsive Design'
    (4, 4); -- Responsive Web Design lié à 'Frameworks Frontend'

-- Bootstrap Framework
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (5, 4), -- Bootstrap Framework lié à 'Frameworks Frontend'
    (5, 3); -- Bootstrap Framework lié à 'Responsive Design'

-- SEO Basics
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (6, 7), -- SEO Basics lié à 'Cybersécurité'
    (6, 4); -- SEO Basics lié à 'Frameworks Frontend'

-- PHP pour Débutants
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (7, 5), -- PHP pour Débutants lié à 'Frameworks Backend'
    (7, 6); -- PHP pour Débutants lié à 'Base de Données'

-- Laravel Essentials
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (8, 5), -- Laravel Essentials lié à 'Frameworks Backend'
    (8, 6); -- Laravel Essentials lié à 'Base de Données'

-- Web Security
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (9, 7); -- Web Security lié à 'Cybersécurité'

-- Git et GitHub
INSERT INTO cours_tag (id_cours, id_tag)
VALUES
    (10, 4); -- Git et GitHub lié à 'Frameworks Frontend'
  