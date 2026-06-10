drop view if exists vue_historique_details;
create view vue_historique_details as
select c.*,h.utilisateur_id,h.is_send,h.numero,destinataire_id,expediteur_id,message_id as message_id, m.is_read_at from Historiques h
join courriers c on h.courrier_id = c.id
left join messages m on h.message_id = m.id
where h.deleted_at is null;