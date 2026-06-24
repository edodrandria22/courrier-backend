
INSERT INTO roles (id, name,created_at) VALUES (1, 'Admin', NOW());
INSERT INTO roles (id, name,created_at) VALUES (2, 'Utilisateur',NOW());
INSERT INTO roles (id, name,created_at) VALUES (3, 'Externe',NOW());


INSERT INTO utilisateurs
(id,role_id, created_at, deleted_at, email, mdp, nom, adresse)
VALUES
(2,3, NOW(), NULL, 'externe@gmail.com', 'externe', 'Externe', 'Externe');


