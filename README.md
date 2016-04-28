# hltv & csgolounge scraper
A scraper for hltv.org and csgolounge.com, written for my project: www.brintos.dk/csgo


This project is no longer active since i have been banned from hltv. I will no longer keep it updated.

This is a scraper written in java with the purpose of scraping latest matches and results from hltv.org, 
as well as gathering odds from csgolounge.com. These results are then added to a database consisting of CS:GO players, teams, maps,
matches, and competitions




# Install
1. in order to take full effect of this scraper, you'll need a database structure to match. A copy of my structure can be found [here](http://brintos.dk/csgo/database_structure.pdf)
2. Replace database information in database.java marked with "DATABASE_URL", "USERNAME", "PASSWORD"
3. Replace userAgent in Main.java

# Dependencies
- JDBC driver
- JSoup
