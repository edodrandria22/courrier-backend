pg_dump -U postgres -d courrier > courrierDode.sql

psql -U postgres -d courrier < courrierDode.sql

pg_dump -h 127.0.0.1 -p 5432 -U mesupres -d courrier > courrier.sql

psql -h 127.0.0.1 -p 5432 -U mesupres -d courrier < courrier.sql