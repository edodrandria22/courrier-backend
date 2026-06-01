
INSERT INTO roles (id, name,created_at) VALUES (1, 'Admin', NOW());
INSERT INTO roles (id, name,created_at) VALUES (2, 'Utilisateur',NOW());


INSERT INTO utilisateurs
(id,role_id, created_at, deleted_at, email, mdp, nom, prenom)
VALUES
(1,1, NOW(), NULL, 'admin@gmail.com', '$2y$10$Djns8FgsL.xk2GBACEtJh.Hs1civTyvdGQ9s6gqbSgDN81QkOHvTi', 'Rakoto', 'Jean');


