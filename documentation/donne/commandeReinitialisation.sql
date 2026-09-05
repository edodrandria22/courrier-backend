delete from Historiques;
delete from Messages;
delete from detail_personnes;
delete from courriers;
delete from numero_courriers;
delete from utilisateurs;

ALTER SEQUENCE utilisateurs_id_seq RESTART WITH 1;
ALTER SEQUENCE roles_id_seq RESTART WITH 1;
ALTER SEQUENCE courriers_id_seq RESTART WITH 1;
ALTER SEQUENCE messages_id_seq RESTART WITH 1;
ALTER SEQUENCE numero_courriers_id_seq RESTART WITH 1;
ALTER SEQUENCE detail_personnes_id_seq RESTART WITH 1;
ALTER SEQUENCE historiques_id_seq RESTART WITH 1;