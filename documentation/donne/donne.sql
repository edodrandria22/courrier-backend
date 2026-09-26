SET client_encoding = 'UTF8';

INSERT INTO roles (id, name,created_at) VALUES (1, 'Admin', NOW());
INSERT INTO roles (id, name,created_at) VALUES (2, 'Utilisateur',NOW());
INSERT INTO roles (id, name,created_at) VALUES (3, 'Externe',NOW());


INSERT INTO utilisateurs
(id,role_id, created_at, deleted_at, email, mdp, nom, adresse)
VALUES
(1,1, NOW(), NULL, 'admin@gmail.com', '$2y$10$Djns8FgsL.xk2GBACEtJh.Hs1civTyvdGQ9s6gqbSgDN81QkOHvTi', 'Admin', 'Admin');

INSERT INTO utilisateurs
(id,role_id, created_at, deleted_at, email, mdp, nom, adresse)
VALUES
(2,3, NOW(), NULL, 'externe@gmail.com', 'externe', 'Externe', 'Externe');

INSERT INTO entites (id,created_at, name)
VALUES
    (1,NOW(), 'Enseignant-chercheur'),
    (2,NOW(), 'Chercheur-enseignant'),
    (3,NOW(), 'PAT'),
    (4,NOW(),'Autre');

SET client_encoding = 'UTF8';

INSERT INTO employeurs (id, created_at, deleted_at, name)
VALUES
    (1, NOW(), NULL, U&'Universit\00E9 d''Antananarivo'),
    (2, NOW(), NULL, U&'Universit\00E9 de Mahajanga'),
    (3, NOW(), NULL, U&'Universit\00E9 de Tul\00E9ar'),
    (4, NOW(), NULL, U&'Universit\00E9 de Toamasina'),
    (5, NOW(), NULL, U&'Universit\00E9 de Fianarantsoa'),
    (6, NOW(), NULL, U&'Universit\00E9 d''Antsiranana'),
    (7, NOW(), NULL, U&'Universit\00E9 de l''Itasy'),
    (8, NOW(), NULL, U&'Universit\00E9 de Vakinankaratra'),
    (9, NOW(), NULL, U&'Universit\00E9 d''Analanjorofo'),
    (10, NOW(), NULL, U&'Universit\00E9 de SAVA'),
    (11, NOW(), NULL, U&'Universit\00E9 d''Agnamb\00E0'),
    (12, NOW(), NULL, U&'Universit\00E9 d''Alaotra-Mangoro'),
    (13, NOW(), NULL, U&'Universit\00E9 d''Anosy'),
    (14, NOW(), NULL, U&'Universit\00E9 d''Androy'),
    (15, NOW(), NULL, U&'Universit\00E9 d''Androna'),
    (16, NOW(), NULL, U&'Universit\00E9 d''Amoron''i Mania'),
    (17, NOW(), NULL, U&'Universit\00E9 de Menabe'),
    (18, NOW(), NULL, U&'IST d''Antananarivo'),
    (19, NOW(), NULL, U&'IST de Diego'),
    (20, NOW(), NULL, 'INSTN'),
    (21, NOW(), NULL, 'CNRE'),
    (22, NOW(), NULL, 'FOFIFA'),
    (23, NOW(), NULL, 'CNRO'),
    (24, NOW(), NULL, 'CNRIT'),
    (25, NOW(), NULL, 'CNARP'),
    (26, NOW(), NULL, 'PBZT'),
    (27, NOW(), NULL, 'IMVAVET'),
    (28, NOW(), NULL, 'CIDST'),
    (29, NOW(), NULL, 'CNTEMAD'),
    (30, NOW(), NULL, 'CNELA'),
    (31, NOW(), NULL, 'MESUPRES'),
    (32, NOW(), NULL, 'Autre');

SET client_encoding = 'UTF8';

BEGIN;

UPDATE employeurs SET name = U&'Universit\00E9 d''Antananarivo' WHERE id = 1;
UPDATE employeurs SET name = U&'Universit\00E9 de Mahajanga' WHERE id = 2;
UPDATE employeurs SET name = U&'Universit\00E9 de Tul\00E9ar' WHERE id = 3;
UPDATE employeurs SET name = U&'Universit\00E9 de Toamasina' WHERE id = 4;
UPDATE employeurs SET name = U&'Universit\00E9 de Fianarantsoa' WHERE id = 5;
UPDATE employeurs SET name = U&'Universit\00E9 d''Antsiranana' WHERE id = 6;
UPDATE employeurs SET name = U&'Universit\00E9 de l''Itasy' WHERE id = 7;
UPDATE employeurs SET name = U&'Universit\00E9 de Vakinankaratra' WHERE id = 8;
UPDATE employeurs SET name = U&'Universit\00E9 d''Analanjorofo' WHERE id = 9;
UPDATE employeurs SET name = U&'Universit\00E9 de SAVA' WHERE id = 10;
UPDATE employeurs SET name = U&'Universit\00E9 d''Agnamb\00E0' WHERE id = 11;
UPDATE employeurs SET name = U&'Universit\00E9 d''Alaotra-Mangoro' WHERE id = 12;
UPDATE employeurs SET name = U&'Universit\00E9 d''Anosy' WHERE id = 13;
UPDATE employeurs SET name = U&'Universit\00E9 d''Androy' WHERE id = 14;
UPDATE employeurs SET name = U&'Universit\00E9 d''Androna' WHERE id = 15;
UPDATE employeurs SET name = U&'Universit\00E9 d''Amoron''i Mania' WHERE id = 16;
UPDATE employeurs SET name = U&'Universit\00E9 de Menabe' WHERE id = 17;
UPDATE employeurs SET name = U&'IST d''Antananarivo' WHERE id = 18;
UPDATE employeurs SET name = U&'IST de Diego' WHERE id = 19;
UPDATE employeurs SET name = 'INSTN' WHERE id = 20;
UPDATE employeurs SET name = 'CNRE' WHERE id = 21;
UPDATE employeurs SET name = 'FOFIFA' WHERE id = 22;
UPDATE employeurs SET name = 'CNRO' WHERE id = 23;
UPDATE employeurs SET name = 'CNRIT' WHERE id = 24;
UPDATE employeurs SET name = 'CNARP' WHERE id = 25;
UPDATE employeurs SET name = 'PBZT' WHERE id = 26;
UPDATE employeurs SET name = 'IMVAVET' WHERE id = 27;
UPDATE employeurs SET name = 'CIDST' WHERE id = 28;
UPDATE employeurs SET name = 'CNTEMAD' WHERE id = 29;
UPDATE employeurs SET name = 'CNELA' WHERE id = 30;
UPDATE employeurs SET name = 'MESUPRES' WHERE id = 31;
UPDATE employeurs SET name = 'Autre' WHERE id = 32;

COMMIT;

UPDATE detail_personnes set employeur_id = 4 where id = 8;
UPDATE detail_personnes set employeur_id = 3 where id = 9;
UPDATE detail_personnes set employeur_id = 3 where id = 10;
UPDATE detail_personnes set employeur_id = 3 where id = 11;
UPDATE detail_personnes set employeur_id = 1 where id = 12;
UPDATE detail_personnes set employeur_id = 31 where id = 14;

