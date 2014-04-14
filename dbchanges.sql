#adds categories and types to event table

ALTER TABLE event ADD type ENUM('Demonstration','Artistic','Meeting or planning','Social or network','Talk or discussion','Training or workshop','Workday or volunteering','Other');

ALTER TABLE event ADD category ENUM('Community and education', 'Economics', 'Environment', 'Justice and injustice', 'Peace and international relations', 'Other');