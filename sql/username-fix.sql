-- USE drupal8_ada;

UPDATE `users_field_data`
SET `name` = TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(`name`), ' ', ''), '003104176ci', ''), '-', ''), '_', ''), '.', ''), 'soygestantede7meses', ''), 'pasaporte', ''), 'ci', ''), 'v', ''), 'nin', ''), 'eltigre', ''), 'passport', ''), 'ptp', ''), 'céduladeidentidad', ''), 'cedula', ''), 'cédula', ''), 'ce', ''), 'p', ''), 'q', ''))
WHERE `name` NOT REGEXP '^[0-9]{7,8}$'
  AND `name` NOT REGEXP '^[0-9]{9,10}$'
  AND `name` REGEXP '[0-9]+'
  AND `name` != '20918073_88858043'
  AND `uid` > 1
  AND `uid` NOT IN (
    SELECT fixedids.uid
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
  );
