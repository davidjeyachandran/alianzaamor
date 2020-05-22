-- USE drupal8_ada;

SELECT "Total users", COUNT(`name`) FROM `users_field_data`;
# SELECT `name` FROM `users_field_data`;

SELECT "Usernames matching format (7-10 integers)", COUNT(`name`)
  FROM `users_field_data`
  WHERE (`name` REGEXP '^[0-9]{7,8}$' OR `name` REGEXP '^[0-9]{9,10}$') AND `uid` > 1;

SELECT "Usernames NOT matching format (7-10 integers)", COUNT(`name`)
  FROM `users_field_data`
  WHERE `name` NOT REGEXP '^[0-9]{7,8}$' AND `name` NOT REGEXP '^[0-9]{9,10}$' AND `uid` > 1;
# SELECT `name`
#   FROM `users_field_data`
#   WHERE `name` NOT REGEXP '^[0-9]{7,8}$' AND `name` NOT REGEXP '^[0-9]{9}$' AND `uid` > 1;

SELECT "Usernames NOT containing digits", COUNT(`name`)
  FROM `users_field_data`
  WHERE `name` NOT REGEXP '[0-9]+' AND `uid` > 1;
# SELECT `name`
#   FROM `users_field_data`
#   WHERE `name` NOT REGEXP '[0-9]+' AND `uid` > 1;

SELECT "Usernames to fix", COUNT(`name`)
  FROM `users_field_data`
  WHERE `name` NOT REGEXP '^[0-9]{7,8}$'
    AND `name` NOT REGEXP '^[0-9]{9,10}$'
    AND `name` REGEXP '[0-9]+'
    AND `name` != '20918073_88858043'
    AND `uid` > 1;
# SELECT `name`
#   FROM `users_field_data`
#   WHERE `name` NOT REGEXP '^[0-9]{7,8}$'
#     AND `name` NOT REGEXP '^[0-9]{9,10}$'
#     AND `name` REGEXP '[0-9]+'
#     AND `name` != '20918073_88858043'
#     AND `uid` > 1;

-- REGEXP_REPLACE is not available until version 8.0

SELECT `name`, ' -> ', TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(`name`), ' ', ''), '003104176ci', ''), '-', ''), '_', ''), '.', ''), 'soygestantede7meses', ''), 'pasaporte', ''), 'ci', ''), 'v', ''), 'nin', ''), 'eltigre', ''), 'passport', ''), 'ptp', ''), 'céduladeidentidad', ''), 'cedula', ''), 'cédula', ''), 'ce', ''), 'p', ''), 'q', ''))
  FROM `users_field_data`
  WHERE `name` NOT REGEXP '^[0-9]{7,8}$'
    AND `name` NOT REGEXP '^[0-9]{9,10}$'
    AND `name` REGEXP '[0-9]+'
    AND `name` != '20918073_88858043'
    AND `uid` > 1;

SELECT 'Usernames NOT unique (proper id, count)';
SELECT fixedids.fixid as finalid, COUNT(fixedids.fixid) AS total
FROM (
  SELECT TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(`name`), ' ', ''), '003104176ci', ''), '-', ''), '_', ''), '.', ''), 'soygestantede7meses', ''), 'pasaporte', ''), 'ci', ''), 'v', ''), 'nin', ''), 'eltigre', ''), 'passport', ''), 'ptp', ''), 'céduladeidentidad', ''), 'cedula', ''), 'cédula', ''), 'ce', ''), 'p', ''), 'q', '')) AS fixid
    FROM `users_field_data`
    WHERE `name` REGEXP '[0-9]+'
      AND `name` != '20918073_88858043'
      AND `uid` > 1
    ORDER BY fixid) AS fixedids
  GROUP BY finalid
  HAVING total > 1
  ORDER BY total DESC;

SELECT 'Usernames NOT unique (uid, proper id)';
SELECT fixedids.uid, fixedids.fixednid
FROM (SELECT uid, TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(`name`), ' ', ''), '003104176ci', ''), '-', ''), '_', ''), '.', ''), 'soygestantede7meses', ''), 'pasaporte', ''), 'ci', ''), 'v', ''), 'nin', ''), 'eltigre', ''), 'passport', ''), 'ptp', ''), 'céduladeidentidad', ''), 'cedula', ''), 'cédula', ''), 'ce', ''), 'p', ''), 'q', '')) AS fixednid
FROM `users_field_data`) AS fixedids
    WHERE fixedids.fixednid IN
(SELECT fixedids.fixid as finalid
FROM (
         SELECT TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(`name`), ' ', ''), '003104176ci', ''), '-', ''), '_', ''), '.', ''), 'soygestantede7meses', ''), 'pasaporte', ''), 'ci', ''), 'v', ''), 'nin', ''), 'eltigre', ''), 'passport', ''), 'ptp', ''), 'céduladeidentidad', ''), 'cedula', ''), 'cédula', ''), 'ce', ''), 'p', ''), 'q', '')) AS fixid
         FROM `users_field_data`
         WHERE `name` REGEXP '[0-9]+'
           AND `name` != '20918073_88858043'
           AND `uid` > 1
         ORDER BY fixid) AS fixedids
GROUP BY finalid
HAVING COUNT(fixedids.fixid)  > 1
ORDER BY COUNT(fixedids.fixid) DESC)

ORDER BY fixedids.fixednid
;
