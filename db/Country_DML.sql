-- заполнение БД
USE countries_db;
-- удалить данные
TRUNCATE TABLE country_t;
-- добавить данные
INSERT INTO country_t (
	name_f,
    name_s,
    iso_alpha2,
    iso_alpha3,
    iso_numeric,
    popul,
    square
) VALUES 
	('Russian Federation', 'Russia', 'RU','RUS', '643', 144356858, 17151442),
    ('Great Kingdom Britain', 'Britain', 'GB','GBR', '826', 67777595, 242500),
    ('Panama Republic', 'Panama', 'PA','PAN', '591', 4546799, 78200),
    ('Singapour Republic','Singapour','SG','SGP','702',5866139,735),
    ('Slovak Republic','Slovakia','SK','SVK','703',5422194,49035);
-- получим данные
SELECT * FROM country_t;
