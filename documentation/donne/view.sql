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
    h.date_reception

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