drop view if exists vue_historique_details;
create view vue_historique_details as
select c.*,h.utilisateur_id,h.is_send,h.numero,destinataire_id,expediteur_id,message_id as message_id, m.is_read_at from Historiques h
join courriers c on h.courrier_id = c.id
left join messages m on h.message_id = m.id
where h.deleted_at is null;


DROP VIEW IF EXISTS vue_historique_details;

CREATE VIEW vue_historique_details AS
SELECT 
    c.id,
    c.createur_id,
    c.cloture_par_id,
    c.created_at,
    c.deleted_at,
    c.date_validation,
    c.reference,
    c.object,
    c.description,
    c.is_confidentiel,

    h.id as historique_id,
    h.utilisateur_id,
    h.is_send,
    h.numero,
    h.num_ref,
    h.created_at as date_message,
    destinataire_id,
    expediteur_id,
    h.message_id,
    h.observation,

    m.is_read_at,
    h.date_reception,
    m.is_traiter_at,
    m.numero_expediteur,
    m.numero_destinataire

FROM Historiques h
JOIN courriers c ON h.courrier_id = c.id
LEFT JOIN messages m ON h.message_id = m.id
WHERE h.deleted_at IS NULL;


DROP VIEW IF EXISTS vue_utilisateurs;

CREATE OR REPLACE VIEW vue_utilisateurs AS
SELECT
    *,
    LOWER(
        COALESCE(nom, '') || ' ' || COALESCE(prenom, '')
    ) AS nom_complet
FROM utilisateurs;


DROP VIEW IF EXISTS vue_historique_detail_personnes;
CREATE OR REPLACE VIEW vue_historique_detail_personnes AS
SELECT
    v.*,

    -- dp.id AS detail_personne_id,
    dp.name,
    dp.prenom,
    dp.email,
    dp.telephone

FROM vue_historique_details v

LEFT JOIN detail_personnes dp
    ON dp.courrier_id = v.id
    AND dp.deleted_at IS NULL;


SELECT historique_id, object
FROM vue_historique_details
WHERE utilisateur_id = 90
AND is_send = false
AND date_message between '2026-08-01' and '2026-08-08'
ORDER BY date_message DESC;


