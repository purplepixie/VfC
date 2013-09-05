Visions for Change Groups and Events Listings Tool

Initial Design by Ian Barker, Chris Brock
Further Development by Simeon Jackson (simeon@simeonjackson.co.uk), Alan Cottey (mail@visionsforchangenorwich.org.uk)

Purpose: A web-based listings service for community groups to announce events and aims.

Design principles: Written in php and css with some javascript features. Content stored in a MySQL database. PHP code does not generally include location-specific information so that it can be used universally.

Features:
-	Home page with expanding content that can be edited from Admin page
-	Events listings with subscription service (email mailing list) and filters (by date, location)
-	Group listings
-	User registration:
	- any user can add groups and events
	- groups and events must be approved by a moderator
-	Admin page (accessible to moderators)
	- group moderation
	- event moderation
	- send email to subscribers
	- group and event archives (includes events in past, deleted groups & events)
	- list users and subscribers
	- edit homepage information

Notes:
-	Some location-specific information is still included in the php.  This needs to be flushed out.
-	The project was derived in Norwich following the Occupy camp in 2010, initially by Chris Brock and Ian Barker.  Hosting and development support was provided by Ian Cottey who enlisted Simeon Jackson to develop the code when the original programmers no longer had time to contribute. Simeon Jackson is now the principal contact for code development, whilst Ian Cottey is the principal contact for Visions for Change Norwich.
-	