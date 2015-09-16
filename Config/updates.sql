use rainbowmondays;
ALTER TABLE jobs ADD jobTitle VARCHAR(255);
alter table districts add column 'region_id' int;
CREATE TABLE regions
(
  id INT PRIMARY KEY NOT NULL,
  name TEXT NOT NULL,
  'long' INT NOT NULL,
  lat INT NOT NULL
);
