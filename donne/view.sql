drop view if exists vue_historique_details;
create view vue_historique_details as
select c.*,h.utilisateur_id,h.is_send from Historiques h
join courriers c on h.courrier_id = c.id
where h.deleted_at is null;