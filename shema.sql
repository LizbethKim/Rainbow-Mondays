CREATE TABLE localities (
    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    districtId INT,
    localityId INT,
    suburbId INT,
    locationName TEXT NOT NULL,
    longitude TEXT NOT NULL,
    latitude TEXT NOT NULL
);

CREATE USER 'rainbowmondays'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON * . * TO 'rainbowmondays'@'localhost';
FLUSH PRIVILEGES;