-- USE `drupal8_ada`

SELECT "Total phone records", COUNT(`field_celular_value`) FROM `user__field_celular`;

SELECT "Phone records with spaces", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` LIKE "% %";

SELECT "Phone records starting with + (plus)", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` LIKE "+%";
-- SELECT `field_celular_value`
--   FROM `user__field_celular`
--   WHERE `field_celular_value` LIKE "+%";

SELECT "Phone records starting with +51", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` LIKE "+51%";
SELECT "Phone records starting with +56", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` LIKE "+56%";
SELECT "Phone records starting with +57", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` LIKE "+57%";
SELECT "Phone records starting with +58", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` LIKE "+58%";

SELECT "Phone records matching 9xxxxxxxx", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP '^9[0-9]{8}$';
SELECT "Phone records not matching 9xxxxxxxx", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` NOT REGEXP '^9[0-9]{8}$';
# SELECT `field_celular_value`
#   FROM `user__field_celular`
#   WHERE `field_celular_value` NOT REGEXP '^9[0-9]{8}$';

SELECT "Phone records containing 10 digits", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP '^[0-9]{10}$';
SELECT "Phone records containing 8 digits", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP '^[0-9]{8}$';
SELECT "Phone records containing 7 digits", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP '^[0-9]{7}$';

SELECT "Phone records containing alphabetical characters", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP "[A-Za-z]";
SELECT `field_celular_value`
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP "[A-Za-z]";

SELECT "Phone records matching 519xxxxxxxx", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP '^519[0-9]{8}$';
SELECT "Phone records matching 56xxxxxxxxx+", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP '^569[0-9]{8}';
SELECT "Phone records matching 57xxxxxxxxx+", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP '^579[0-9]{8}';
SELECT "Phone records matching 58xxxxxxxxx+", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP '^589[0-9]{8}';

SELECT "Phone records matching correct format", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` REGEXP '^519[0-9]{8}$'
    OR `field_celular_value` REGEXP '^5[678][0-9]{9}';

SELECT "Phone records NOT matching correct format", COUNT(`field_celular_value`)
  FROM `user__field_celular`
  WHERE `field_celular_value` NOT REGEXP '^519[0-9]{8}$'
    AND `field_celular_value` NOT REGEXP '^5[678][0-9]{9}';
# SELECT `field_celular_value`
#   FROM `user__field_celular`
#   WHERE `field_celular_value` NOT REGEXP '^519[0-9]{8}$'
#     AND `field_celular_value` NOT REGEXP '^5[678][0-9]{9}';
