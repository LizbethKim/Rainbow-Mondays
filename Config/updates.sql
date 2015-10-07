use rainbowmondays;
ALTER TABLE jobs ADD jobTitle VARCHAR(255);
alter table districts add column `region_id` int;
CREATE TABLE regions
(
  id INT PRIMARY KEY NOT NULL,
  name TEXT NOT NULL,
  'long' INT NOT NULL,
  lat INT NOT NULL
);
ALTER TABLE jobs ADD COLUMN `type` int(1);
ALTER TABLE live_cache ADD COLUMN `icon_url` text;