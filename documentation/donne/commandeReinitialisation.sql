delete from Historiques;
delete from Messages;
delete from courriers;
update courriers set date_message = null;

ALTER SEQUENCE utilisateurs_id_seq RESTART WITH 1;
ALTER SEQUENCE roles_id_seq RESTART WITH 1;
ALTER SEQUENCE courriers_id_seq RESTART WITH 1;
ALTER SEQUENCE messages_id_seq RESTART WITH 1;
ALTER SEQUENCE historiques_id_seq RESTART WITH 1;