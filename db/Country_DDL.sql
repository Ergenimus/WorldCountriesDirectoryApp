-- создание БД
DROP DATABASE IF EXISTS countries_db;
CREATE DATABASE countries_db;
-- переключение на данную БД
USE countries_db;
-- создание таблицы стран
CREATE TABLE country_t (
	id INT NOT NULL AUTO_INCREMENT,
    name_f NVARCHAR(200) NOT NULL,
    name_s NVARCHAR(15) NOT NULL,
    iso_alpha2 CHAR(2) NULL,
    iso_alpha3 CHAR(3) NULL,
    iso_numeric CHAR(3) NULL,
    popul INT(10),
    square INT(15),
    --
    PRIMARY KEY(id),
    UNIQUE(iso_alpha2),
    UNIQUE(iso_alpha3),
    UNIQUE(iso_numeric)
);