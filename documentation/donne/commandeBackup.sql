pg_dump -U postgres -d courrier > courrier.sql

psql -U postgres -d digitalisation < courrier.sql