
-- UPDATE `user__field_celular`
UPDATE `user__field_celular` SET `field_celular_value` = TRIM(`field_celular_value`);
UPDATE `user__field_celular` SET `field_celular_value` = REPLACE(`field_celular_value`, ' ', '');
UPDATE `user__field_celular` SET `field_celular_value` = REPLACE(`field_celular_value`, '+', '');
UPDATE `user__field_celular` SET `field_celular_value` = REPLACE(`field_celular_value`, '.', '');
UPDATE `user__field_celular` SET `field_celular_value` = REPLACE(`field_celular_value`, '-', '');
UPDATE `user__field_celular` SET `field_celular_value` = REPLACE(`field_celular_value`, '±', '');
UPDATE `user__field_celular` SET `field_celular_value` = REPLACE(`field_celular_value`, '?', '');
UPDATE `user__field_celular` SET `field_celular_value` = REPLACE(`field_celular_value`, '¹', '');
UPDATE `user__field_celular` SET `field_celular_value` = REPLACE(`field_celular_value`, 'l', '');
UPDATE `user__field_celular` SET `field_celular_value` = REPLACE(`field_celular_value`, 'p', '');
UPDATE `user__field_celular`
  SET `field_celular_value` = CONCAT('51', `field_celular_value`)
  WHERE `field_celular_value` REGEXP '^9[0-9]{8}$';
